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
        $user = Auth::user();
        $idDepen = $user->id_depen;

        // Nombre de dependencia
        $this->dependenciaNombre = $user->dependencia->nombre_depen ?? null;

        // Formularios disponibles
        $this->formulariosDisponibles = Formulario::whereIn('boton_accion_form', ['Publicado', 'Ver'])
            ->where('id_depen', $idDepen)
            ->count();

        // Base: cargas de la dependencia
        $base = Carga::whereHas('formulario', function ($q) use ($idDepen) {
            $q->where('id_depen', $idDepen);
        });

        $this->cargasRealizadas = (clone $base)->count();

        // Últimas cargas
        $this->ultimasCargas = (clone $base)
            ->with(['formulario.indicador']) // ✅ trae el nombre del indicador
            ->latest('cargas.created_at')
            ->take(5)
            ->get();

        /* =========================================================
       ✅ AVANCE vs META (por formularios)
    ========================================================= */

        // 1) META
        $this->metaIndicadores = Formulario::publicados()
            ->where('id_depen', $idDepen)
            ->distinct('id_form')
            ->count('id_form');

        // 2) COMPLETADOS (sin contar BORRADOR)
        $estatusValidos = ['ENVIADO', 'EN REVISION', 'REENVIADO', 'APROBADO'];

        $this->indicadoresCompletados = (clone $base)
            ->whereIn('cargas.status_env', $estatusValidos)
            ->whereNotNull('cargas.id_form') // por seguridad
            ->distinct('cargas.id_form')
            ->count('cargas.id_form');

        // 3) %
        $this->porcentajeAvance = $this->metaIndicadores > 0
            ? (int) round(($this->indicadoresCompletados / $this->metaIndicadores) * 100)
            : 0;

        /* =========================================================
       ✅ Pendientes / Observaciones (ya con meta/completados listos)
    ========================================================= */

        // ✅ Pendientes reales = meta - completados
        $this->pendientes = max(0, (int) $this->metaIndicadores - (int) $this->indicadoresCompletados);

        // ⚠️ Ajusta estos status a los reales (en tu UI usas OBSERVADO)
        $this->observaciones = (clone $base)->whereIn('status_env', ['OBSERVADO', 'RECHAZADO'])->count();

        /* =========================================================
✅ INDICADORES CON METAS (KARDEX) — MULTI (MENSUAL/TRIM/SEM/ANUAL)
========================================================= */

        $ejercicio = now()->year;

        // Traer TODOS los indicadores que:
        // - tienen metasPeriodo del ejercicio
        // - están asignados a la dependencia (por formularios)
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

        // limpiar (por si Livewire rehidrata)
        $this->kardexMetasPorIndicador = [];

        // estatus válidos (sin BORRADOR)
        $estatusValidos = ['ENVIADO', 'EN REVISION', 'REENVIADO', 'APROBADO'];

        foreach ($indicadoresConMetas as $ind) {

            // periodicidad (tomamos la primera meta del indicador; todas deben ser iguales)
            $periodicidad = $ind->metasPeriodo->first()->periodicidad ?? null;
            $pl = mb_strtolower(trim((string) $periodicidad));

            // si viene vacío, NO inventamos trimestral; mejor mostrar "Sin periodicidad"
            if ($pl === '') {
                $pl = 'sin_periodicidad';
            }

            // meta total del indicador (suma de metas por segmento)
            $metaTotal = (int) $ind->metasPeriodo->sum('meta');

            // Traer cargas válidas del indicador con su periodo
            $cargasValidas = Carga::query()
                ->join('detallecargas as dc', 'dc.id_carga', '=', 'cargas.id_carga')
                ->where('dc.id_ind', $ind->id_ind)
                ->whereIn('cargas.status_env', $estatusValidos)
                ->select('cargas.id_carga', 'cargas.periodo', 'cargas.ejercicio')
                ->distinct()
                ->get();

            // hechas por segmento
            $hechasPorSegmento = []; // [segmento => count]

            foreach ($cargasValidas as $c) {
                $p = trim((string) ($c->periodo ?? ''));
                $seg = null;

                if ($pl === 'mensual') {
                    // YYYY-MM
                    if (preg_match('/^\d{4}\-(\d{2})$/', $p, $m)) {
                        $seg = (int) $m[1]; // 1..12
                    }
                } elseif ($pl === 'trimestral') {
                    // YYYY-T1 o YYYY-02
                    if (preg_match('/^\d{4}\-T(\d{1,2})$/i', $p, $m)) {
                        $seg = (int) $m[1]; // 1..4
                    } elseif (preg_match('/^\d{4}\-(\d{2})$/', $p, $m)) {
                        $mes = (int) $m[1];
                        $seg = (int) ceil($mes / 3); // 1..4
                    }
                } elseif ($pl === 'semestral') {
                    // YYYY-S1 o YYYY-S2 (si viene como mes, lo convertimos)
                    if (preg_match('/^\d{4}\-S(\d)$/i', $p, $m)) {
                        $seg = (int) $m[1]; // 1..2
                    } elseif (preg_match('/^\d{4}\-(\d{2})$/', $p, $m)) {
                        $mes = (int) $m[1];
                        $seg = ($mes <= 6) ? 1 : 2; // 1..2
                    }
                } elseif ($pl === 'anual') {
                    // YYYY o YYYY-01..12 (cualquier mes cuenta como el año)
                    if (preg_match('/^\d{4}$/', $p)) {
                        $seg = 1;
                    } elseif (preg_match('/^\d{4}\-(\d{2})$/', $p)) {
                        $seg = 1;
                    }
                }

                if ($seg !== null) {
                    $hechasPorSegmento[$seg] = ($hechasPorSegmento[$seg] ?? 0) + 1;
                }
            }

            // construir kardex de este indicador
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
                    'mensual' => 'M'.$seg,
                    'trimestral' => 'T'.$seg,
                    'semestral' => 'S'.$seg,
                    'anual' => 'Año',
                    default => 'P'.$seg,
                };

                $kardex[] = [
                    'label' => $label,
                    'meta' => $metaSeg,
                    'hechas' => $hechasSegTop,
                    'estado' => $estado,
                ];
            }

            // tope global a meta total
            $hechasTotal = min($hechasTotal, $metaTotal);

            $porcentaje = $metaTotal > 0
                ? (int) round(($hechasTotal / $metaTotal) * 100)
                : 0;

            // guardar bloque para el blade
            $this->kardexMetasPorIndicador[] = [
                'id_ind' => $ind->id_ind,
                'nombre' => $ind->nombre_ind ?? ('Indicador '.$ind->id_ind),
                'periodicidad' => $periodicidad ?? 'Sin periodicidad',
                'meta_total' => $metaTotal,
                'hechas_total' => $hechasTotal,
                'pct' => $porcentaje,
                'kardex' => $kardex,
            ];
        }

        /**
         * Compatibilidad con tu blade actual:
         * Si tu vista SOLO usa $kardexMetas, $metaCargas, etc.,
         * llenamos esos con el primer indicador para que no se rompa.
         */
        if (! empty($this->kardexMetasPorIndicador)) {
            $primero = $this->kardexMetasPorIndicador[0];

            $this->metaCargas = (int) $primero['meta_total'];
            $this->cargasHechas = (int) $primero['hechas_total'];
            $this->porcentajeMeta = (int) $primero['pct'];
            $this->kardexMetas = $primero['kardex'];
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
            "echo:dashboard.dependencia.{$idDepen},DashboardUpdated" => '$refresh',
        ];
    }
}
