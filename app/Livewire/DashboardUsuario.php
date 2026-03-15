<?php

namespace App\Livewire;

use App\Models\Carga;
use App\Models\Formulario;
use App\Models\Indicador;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class DashboardUsuario extends Component
{
    // KPI existentes
    public int $formulariosDisponibles = 0;

    public int $cargasRealizadas = 0;

    // NUEVOS (los usa tu blade)
    public ?string $dependenciaNombre = null;

    public int $pendientes = 0;

    public int $observaciones = 0;

    public $ultimasCargas = [];

    public $metaIndicadores = 0;

    public $indicadoresCompletados = 0;

    public $porcentajeAvance = 0;

    public int $metaCargas = 0;      // total de metas sumadas (ej: 11)

    public int $cargasHechas = 0;    // total hechas (ej: 5)

    public int $porcentajeMeta = 0;  // ej: 45

    public array $kardexMetas = [];

    public array $kardexMetasPorIndicador = []; // NUEVO: kardex por indicador (cada uno con su periodicidad)

    public function mount()
    {
        $this->cargarDashboard();
    }

    public function refrescarDashboard()
    {
        $this->cargarDashboard();
    }

    public function cargarDashboard()
    {
        $user = Auth::user();
        $idDepen = $user->id_depen;

        $this->dependenciaNombre = $user->dependencia->nombre_depen ?? null;

        $this->formulariosDisponibles = Formulario::whereIn('boton_accion_form', ['Publicado', 'Ver'])
            ->where('id_depen', $idDepen)
            ->count();

        $base = Carga::whereHas('formulario', function ($q) use ($idDepen) {
            $q->where('id_depen', $idDepen);
        });

        $this->cargasRealizadas = (clone $base)->count();

        $this->ultimasCargas = (clone $base)
            ->with(['formulario.indicador'])
            ->latest('cargas.updated_at')
            ->take(5)
            ->get();

        $this->metaIndicadores = Formulario::publicados()
            ->where('id_depen', $idDepen)
            ->distinct('id_form')
            ->count('id_form');

        $estatusValidos = ['ENVIADO', 'EN REVISION', 'REENVIADO', 'APROBADO'];

        $this->indicadoresCompletados = (clone $base)
            ->whereIn('cargas.status_env', $estatusValidos)
            ->whereNotNull('cargas.id_form')
            ->distinct('cargas.id_form')
            ->count('cargas.id_form');

        $this->porcentajeAvance = $this->metaIndicadores > 0
            ? (int) round(($this->indicadoresCompletados / $this->metaIndicadores) * 100)
            : 0;

        $this->pendientes = max(0, (int) $this->metaIndicadores - (int) $this->indicadoresCompletados);

        $this->observaciones = (clone $base)
            ->whereIn('status_env', ['OBSERVADO', 'RECHAZADO'])
            ->count();

        $this->cargarKardexMetas($idDepen);
    }

    protected function cargarKardexMetas($idDepen)
    {
        $ejercicio = now()->year;

        $indicadoresConMetas = Indicador::whereHas('metasPeriodo', function ($q) use ($ejercicio) {
            $q->where('ejercicio', $ejercicio);
        })
            ->whereHas('formularios', function ($q) use ($idDepen) {
                $q->where('id_depen', $idDepen);
            })
            ->with(['metasPeriodo' => function ($q) use ($ejercicio) {
                $q->where('ejercicio', $ejercicio)
                    ->orderBy('segmento');
            }])
            ->get();

        $this->kardexMetasPorIndicador = [];

        $estatusValidos = ['ENVIADO', 'EN REVISION', 'REENVIADO', 'APROBADO'];

        foreach ($indicadoresConMetas as $ind) {
            $periodicidad = $ind->metasPeriodo->first()->periodicidad ?? null;
            $pl = mb_strtolower(trim((string) $periodicidad));

            if ($pl === '') {
                $pl = 'sin_periodicidad';
            }

            $metaTotal = (int) $ind->metasPeriodo->sum('meta');

            $cargasValidas = Carga::query()
                ->join('detallecargas as dc', 'dc.id_carga', '=', 'cargas.id_carga')
                ->where('dc.id_ind', $ind->id_ind)
                ->whereIn('cargas.status_env', $estatusValidos)
                ->select('cargas.id_carga', 'cargas.periodo', 'cargas.ejercicio')
                ->distinct()
                ->get();

            $hechasPorSegmento = [];

            foreach ($cargasValidas as $c) {
                $p = trim((string) ($c->periodo ?? ''));
                $seg = null;

                if ($pl === 'mensual') {
                    if (preg_match('/^\d{4}\-(\d{2})$/', $p, $m)) {
                        $seg = (int) $m[1];
                    }
                } elseif ($pl === 'trimestral') {
                    if (preg_match('/^\d{4}\-T(\d{1,2})$/i', $p, $m)) {
                        $seg = (int) $m[1];
                    } elseif (preg_match('/^\d{4}\-(\d{2})$/', $p, $m)) {
                        $mes = (int) $m[1];
                        $seg = (int) ceil($mes / 3);
                    }
                } elseif ($pl === 'semestral') {
                    if (preg_match('/^\d{4}\-S(\d)$/i', $p, $m)) {
                        $seg = (int) $m[1];
                    } elseif (preg_match('/^\d{4}\-(\d{2})$/', $p, $m)) {
                        $mes = (int) $m[1];
                        $seg = ($mes <= 6) ? 1 : 2;
                    }
                } elseif ($pl === 'anual') {
                    if (preg_match('/^\d{4}$/', $p) || preg_match('/^\d{4}\-(\d{2})$/', $p)) {
                        $seg = 1;
                    }
                }

                if ($seg !== null) {
                    $hechasPorSegmento[$seg] = ($hechasPorSegmento[$seg] ?? 0) + 1;
                }
            }

            $kardex = [];
            $hechasTotal = 0;

            foreach ($ind->metasPeriodo as $m) {
                $seg = (int) $m->segmento;
                $metaSeg = (int) $m->meta;

                $hechasSeg = (int) ($hechasPorSegmento[$seg] ?? 0);
                $hechasSegTop = min($hechasSeg, $metaSeg);

                $hechasTotal += $hechasSegTop;

                if ($metaSeg > 0 && $hechasSegTop >= $metaSeg) {
                    $estado = 'success';
                } elseif ($hechasSegTop > 0) {
                    $estado = 'warning';
                } else {
                    $estado = 'danger';
                }

                $label = match ($pl) {
                    'mensual' => 'M' . $seg,
                    'trimestral' => 'T' . $seg,
                    'semestral' => 'S' . $seg,
                    'anual' => 'Año',
                    default => 'P' . $seg,
                };

                $kardex[] = [
                    'label' => $label,
                    'meta' => $metaSeg,
                    'hechas' => $hechasSegTop,
                    'estado' => $estado,
                ];
            }

            $hechasTotal = min($hechasTotal, $metaTotal);

            $porcentaje = $metaTotal > 0
                ? (int) round(($hechasTotal / $metaTotal) * 100)
                : 0;

            $this->kardexMetasPorIndicador[] = [
                'id_ind' => $ind->id_ind,
                'nombre' => $ind->nombre_ind ?? ('Indicador ' . $ind->id_ind),
                'periodicidad' => $periodicidad ?? 'Sin periodicidad',
                'meta_total' => $metaTotal,
                'hechas_total' => $hechasTotal,
                'pct' => $porcentaje,
                'kardex' => $kardex,
            ];
        }

        if (!empty($this->kardexMetasPorIndicador)) {
            $primero = $this->kardexMetasPorIndicador[0];

            $this->metaCargas = (int) $primero['meta_total'];
            $this->cargasHechas = (int) $primero['hechas_total'];
            $this->porcentajeMeta = (int) $primero['pct'];
            $this->kardexMetas = $primero['kardex'];
        } else {
            $this->metaCargas = 0;
            $this->cargasHechas = 0;
            $this->porcentajeMeta = 0;
            $this->kardexMetas = [];
        }
    }

    public function render()
    {
        return view('livewire.dashboard-usuario');
    }

    // Helper para color del badge (lo usamos en la vista)
    public function badgeClass($status): string
    {
        $st = strtolower((string) $status);

        if (str_contains($st, 'rech')) {
            return 'danger';
        }
        if (str_contains($st, 'obs')) {
            return 'warning';
        }
        if (str_contains($st, 'pend') || str_contains($st, 'borr')) {
            return 'secondary';
        }
        if (str_contains($st, 'env')) {
            return 'primary';
        }
        if (str_contains($st, 'aprob') || str_contains($st, 'final')) {
            return 'success';
        }

        return 'primary';
    }

    public function getListeners()
    {
        $idDepen = auth()->user()->id_depen;

        return [
            "echo:dashboard.dependencia.{$idDepen},DashboardUpdated" => 'refrescarDashboard',
        ];
    }
}
