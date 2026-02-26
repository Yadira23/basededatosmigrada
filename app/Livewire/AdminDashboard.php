<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class AdminDashboard extends Component
{
    public $totalUsuarios;
    public $totalFormularios;
    public $cargasPendientes;
    public $nuevosRegistros;

    public $formulariosPorMes = [];
    public $usuariosPorRol = [];
    public $cargasPorEstado = [];

    public $borradoresPorDep = []; // [id_depen => total]
    public $totalBorradores = 0;

    public $avancePorDep = [];     // [id_depen => ['meta','hechos','pct','color']]
    public $kardexPorDep = [];     // [id_depen => ['meta','hechas','pct','color']]
    public $kardexPeriodosPorDep = []; // [id_depen => [ ['label','meta','hechas','estado'], ... ]]
    public $estatusPorDepSeg = []; // [id_depen][seg][estatus => total]

    public $depSeleccionadaMetas = null;
    public $depOptions = [];

    public $modalOpen = false;
    public $modalTitulo = '';
    public $modalCargas = [];

    /* =========================================================
       MOUNT
    ========================================================= */
    public function mount()
    {
        // Usuarios
        $this->totalUsuarios = DB::table('usuarios')->count();
        $this->nuevosRegistros = DB::table('usuarios')
            ->whereDate('created_at', now())
            ->count();

        // Formularios
        $this->totalFormularios = DB::table('formularios')->count();
        $this->formulariosPorMes = DB::table('formularios')
            ->selectRaw('MONTH(created_at) as mes, COUNT(*) as total')
            ->whereYear('created_at', now()->year)
            ->groupBy('mes')
            ->pluck('total', 'mes')
            ->toArray();

        // Usuarios por rol (Spatie)
        $this->usuariosPorRol = DB::table('model_has_roles')
            ->join('roles', 'roles.id', '=', 'model_has_roles.role_id')
            ->select('roles.name', DB::raw('COUNT(*) as total'))
            ->where('model_type', 'App\\Models\\Usuario')
            ->groupBy('roles.name')
            ->pluck('total', 'roles.name')
            ->toArray();

        // Cargas
        $this->cargasPendientes = DB::table('cargas')
            ->where(DB::raw("UPPER(REPLACE(TRIM(status_env),'REVISIÓN','REVISION'))"), 'EN REVISION')
            ->count();

        // ✅ Estados que quieres ver SIEMPRE en la gráfica (aunque sean 0)
        $estadosOrden = [
            'APROBADO',
            'EN REVISION',
            'REENVIADO',
            'ENVIADO',
            'OBSERVADO',
            'RECHAZADO',
            'BORRADOR',
            'SIN CAPTURA',
        ];

        // ✅ Query normalizada (por si viene “EN REVISIÓN”, espacios, minúsculas, etc.)
        $raw = DB::table('cargas')
            ->selectRaw("
        UPPER(REPLACE(TRIM(status_env),'REVISIÓN','REVISION')) AS estado,
        COUNT(*) AS total
    ")
            ->groupBy('estado')
            ->pluck('total', 'estado')
            ->toArray();

        // ✅ Rellenar faltantes con 0 y respetar el orden
        $this->cargasPorEstado = [];
        foreach ($estadosOrden as $st) {
            $this->cargasPorEstado[$st] = (int) ($raw[$st] ?? 0);
        }

        // Dependencias
        $this->depOptions = DB::table('dependencias')
            ->orderBy('nombre_depen')
            ->pluck('nombre_depen', 'id_depen')
            ->toArray();

        $this->depSeleccionadaMetas = array_key_first($this->depOptions);
    }

    /* =========================================================
       RECORDATORIOS
    ========================================================= */
    public function recordarBorradoresGlobal()
    {
        foreach (array_keys($this->borradoresPorDep) as $id_depen) {
            $this->recordarBorradoresDependencia($id_depen, true);
        }

        session()->flash('message', 'Recordatorio enviado a dependencias con borradores.');
    }

    public function recordarBorradoresDependencia($id_depen, $silent = false)
    {
        $usuariosIds = DB::table('usuarios')
            ->where('id_depen', $id_depen)
            ->pluck('id_usuario')
            ->toArray();

        if (empty($usuariosIds)) {
            if (!$silent) {
                session()->flash('message', 'No hay usuarios registrados en esa dependencia.');
            }
            return;
        }

        $recientes = DB::table('notificaciones')
            ->where('tipo', 'RECORDATORIO')
            ->where('id_depen', $id_depen)
            ->whereIn('id_usuario', $usuariosIds)
            ->where('created_at', '>=', now()->subHours(12))
            ->pluck('id_usuario')
            ->toArray();

        $ahora = now();
        $inserts = [];

        foreach ($usuariosIds as $id_usuario) {
            if (in_array($id_usuario, $recientes)) {
                continue;
            }

            $inserts[] = [
                'id_usuario' => $id_usuario,
                'id_depen'   => $id_depen,
                'id_carga'   => null,
                'tipo'       => 'RECORDATORIO',
                'titulo'     => 'Tienes cargas en borrador',
                'mensaje'    => 'Tu dependencia tiene cargas en estatus BORRADOR. Por favor revisa y envía tus cargas pendientes.',
                'leida'      => 0,
                'created_at' => $ahora,
                'updated_at' => $ahora,
            ];
        }

        if (!empty($inserts)) {
            DB::table('notificaciones')->insert($inserts);
            if (!$silent) {
                session()->flash('message', 'Recordatorio enviado a la dependencia seleccionada.');
            }
        } elseif (!$silent) {
            session()->flash('message', 'Ya se envió un recordatorio recientemente (últimas 12h).');
        }
    }

    /* =========================================================
       MODAL DETALLE
    ========================================================= */
    public function verDetalleKardex($id_depen, $periodicidad, $seg)
    {
        $ejercicio = (int) now()->year;

        $peri = mb_strtolower(trim((string)$periodicidad));
        $seg  = (int) $seg;

        // ✅ etiqueta bonita
        $label = match ($peri) {
            'mensual'    => 'M' . $seg,
            'trimestral' => 'T' . $seg,
            'semestral'  => 'S' . $seg,
            'anual'      => 'A',
            default      => (string)$periodicidad . ' ' . $seg,
        };

        $this->modalOpen = true;
        $this->modalTitulo = "Detalle de cargas · Dep: {$id_depen} · {$label} · {$ejercicio}";

        // ✅ Condición de segmento según periodicidad
        // (c.periodo viene como YYYY-MM)
        $whereSegmento = match ($peri) {
            'mensual' => function ($q) use ($seg) {
                // mes exacto
                $q->where(DB::raw("CAST(SUBSTRING(c.periodo,6,2) AS UNSIGNED)"), $seg);
            },
            'trimestral' => function ($q) use ($seg) {
                $q->where(DB::raw("CEIL(CAST(SUBSTRING(c.periodo,6,2) AS UNSIGNED) / 3)"), $seg);
            },
            'semestral' => function ($q) use ($seg) {
                $q->where(DB::raw("CEIL(CAST(SUBSTRING(c.periodo,6,2) AS UNSIGNED) / 6)"), $seg);
            },
            'anual' => function ($q) {
                // anual = todo el año (no filtramos por seg, siempre 1)
                // si quieres forzar seg=1:
                // $q->whereRaw("1=1");
            },
            default => function ($q) use ($seg) {
                // fallback: trimestral
                $q->where(DB::raw("CEIL(CAST(SUBSTRING(c.periodo,6,2) AS UNSIGNED) / 3)"), $seg);
            },
        };

        $query = DB::table('cargas as c')
            ->join('formularios as f', 'f.id_form', '=', 'c.id_form')
            ->join('detallecargas as dc', 'dc.id_carga', '=', 'c.id_carga')
            ->where('f.id_depen', $id_depen)
            ->where('c.ejercicio', $ejercicio)
            ->whereRaw("c.periodo REGEXP '^[0-9]{4}-[0-9]{2}$'");

        // aplicar filtro de segmento (excepto anual)
        if ($peri !== 'anual') {
            $whereSegmento($query);
        }

        $this->modalCargas = $query
            ->select(
                'c.id_carga',
                'c.id_form',
                'c.folioUnico_carga',
                'c.periodo',
                'c.ejercicio',
                'c.status_env',
                'c.created_at',
                'c.updated_at',
                DB::raw('MIN(f.titulo_form) as titulo_form')
            )
            ->groupBy(
                'c.id_carga',
                'c.id_form',
                'c.folioUnico_carga',
                'c.periodo',
                'c.ejercicio',
                'c.status_env',
                'c.created_at',
                'c.updated_at'
            )
            ->orderByDesc('c.id_carga')
            ->limit(50)
            ->get()
            ->map(function ($r) {
                return [
                    'id_carga'    => $r->id_carga,
                    'id_form'     => $r->id_form,
                    'folio'       => $r->folioUnico_carga ?: $r->id_carga,
                    'periodo'     => $r->periodo,
                    'ejercicio'   => $r->ejercicio,
                    'estatus'     => $r->status_env,
                    'formulario'  => $r->titulo_form,
                    'fecha'       => $r->created_at ? Carbon::parse($r->created_at)->format('Y-m-d H:i') : null,
                    'actualizado' => $r->updated_at ? Carbon::parse($r->updated_at)->format('Y-m-d H:i') : null,
                ];
            })
            ->toArray();

        $this->dispatch('open-admin-modal');
    }

    public function cerrarModal()
    {
        $this->modalOpen = false;
        $this->modalTitulo = '';
        $this->modalCargas = [];

        $this->dispatch('close-admin-modal');
    }

    public function testClick()
    {
        session()->flash('message', 'Click OK ' . now());
    }

    public function getListeners()
    {
        return [
            "echo:dashboard.admin,DashboardUpdated" => '$refresh',
        ];
    }

    public function render()
    {
        /* =========================================================
       ✅ BORRADORES por dependencia
    ========================================================= */
        $this->borradoresPorDep = DB::table('cargas as c')
            ->join('formularios as f', 'f.id_form', '=', 'c.id_form')
            ->where('c.status_env', 'BORRADOR')
            ->groupBy('f.id_depen')
            ->pluck(DB::raw('COUNT(*) as total'), 'f.id_depen')
            ->toArray();

        $this->totalBorradores = array_sum($this->borradoresPorDep);

        /* =========================================================
       ✅ AVANCE vs META por dependencia
    ========================================================= */
        $estatusValidos = ['ENVIADO', 'EN REVISION', 'REENVIADO', 'APROBADO'];

        $metas = DB::table('formularios')
            ->select('id_depen', DB::raw('COUNT(DISTINCT id_form) as meta'))
            ->groupBy('id_depen')
            ->pluck('meta', 'id_depen')
            ->toArray();

        $hechos = DB::table('cargas as c')
            ->join('formularios as f', 'f.id_form', '=', 'c.id_form')
            ->whereIn('c.status_env', $estatusValidos)
            ->select('f.id_depen', DB::raw('COUNT(DISTINCT c.id_form) as hechos'))
            ->groupBy('f.id_depen')
            ->pluck('hechos', 'f.id_depen')
            ->toArray();

        $this->avancePorDep = [];
        foreach ($metas as $id_depen => $meta) {
            $meta = (int) $meta;
            $done = (int) ($hechos[$id_depen] ?? 0);
            $pct  = $meta > 0 ? (int) round(($done / $meta) * 100) : 0;

            $color = $meta === 0 ? 'secondary' : ($pct >= 80 ? 'success' : ($pct >= 40 ? 'warning' : 'danger'));

            $this->avancePorDep[$id_depen] = [
                'meta'   => $meta,
                'hechos' => $done,
                'pct'    => $pct,
                'color'  => $color,
            ];
        }

        /* =========================================================
       ✅ INDICADORES CON METAS (Kardex) - RESUMEN POR DEP
    ========================================================= */
        $ejercicio = (int) now()->year;

        $metaPorInd = DB::table('metas_periodo')
            ->where('ejercicio', $ejercicio)
            ->groupBy('id_ind')
            ->select('id_ind', DB::raw('SUM(meta) as meta_total'))
            ->pluck('meta_total', 'id_ind')
            ->toArray();

        $indToDep = DB::table('formularios')
            ->whereIn('id_ind', array_keys($metaPorInd))
            ->select('id_ind', 'id_depen')
            ->groupBy('id_ind', 'id_depen')
            ->get();

        $metaPorDep = [];
        foreach ($indToDep as $r) {
            $idInd = (int) $r->id_ind;
            $idDep = (int) $r->id_depen;
            $metaPorDep[$idDep] = ($metaPorDep[$idDep] ?? 0) + (int) ($metaPorInd[$idInd] ?? 0);
        }

        $hechasPorDep = DB::table('cargas as c')
            ->join('detallecargas as dc', 'dc.id_carga', '=', 'c.id_carga')
            ->join('formularios as f', 'f.id_form', '=', 'c.id_form')
            ->whereIn('c.status_env', $estatusValidos)
            ->whereIn('dc.id_ind', array_keys($metaPorInd))
            ->select('f.id_depen', DB::raw('COUNT(DISTINCT c.id_carga) as hechas'))
            ->groupBy('f.id_depen')
            ->pluck('hechas', 'f.id_depen')
            ->toArray();

        $this->kardexPorDep = [];
        $deps = DB::table('dependencias')->select('id_depen')->get();

        foreach ($deps as $d) {
            $id = (int) $d->id_depen;

            $meta   = (int) ($metaPorDep[$id] ?? 0);
            $hechas = (int) ($hechasPorDep[$id] ?? 0);

            $pct   = $meta > 0 ? (int) round(($hechas / $meta) * 100) : 0;
            $color = $meta === 0 ? 'secondary' : ($pct >= 80 ? 'success' : ($pct >= 40 ? 'warning' : 'danger'));

            $this->kardexPorDep[$id] = [
                'meta'   => $meta,
                'hechas' => min($hechas, $meta),
                'pct'    => $pct,
                'color'  => $color,
            ];
        }

        /* =========================================================
       ✅ MULTI-PERIODICIDAD: Kardex por periodo por dependencia
    ========================================================= */
        $periodosDef = [
            'Mensual'    => 12,
            'Trimestral' => 4,
            'Semestral'  => 2,
            'Anual'      => 1,
        ];

        // 1) METAS: metasIdx[dep][periodicidad][seg] = meta
        $metasPorDepSeg = DB::table('metas_periodo as mp')
            ->join('formularios as f', 'f.id_ind', '=', 'mp.id_ind')
            ->where('mp.ejercicio', $ejercicio)
            ->whereIn('mp.id_ind', array_keys($metaPorInd))
            ->select(
                'f.id_depen',
                'mp.periodicidad',
                'mp.segmento',
                DB::raw('SUM(mp.meta) as meta')
            )
            ->groupBy('f.id_depen', 'mp.periodicidad', 'mp.segmento')
            ->get();

        $metasIdx = [];
        foreach ($metasPorDepSeg as $r) {
            $d = (int) $r->id_depen;
            $p = trim((string) $r->periodicidad);
            $s = (int) $r->segmento;
            $metasIdx[$d][$p][$s] = (int) $r->meta;
        }

        // ✅ Segmento según periodicidad REAL (metas_periodo.periodicidad)
        $segExpr = DB::raw("
            CASE
                WHEN UPPER(mp.periodicidad) = 'MENSUAL' THEN CAST(SUBSTRING(c.periodo,6,2) AS UNSIGNED)
                WHEN UPPER(mp.periodicidad) = 'TRIMESTRAL' THEN CEIL(CAST(SUBSTRING(c.periodo,6,2) AS UNSIGNED) / 3)
                WHEN UPPER(mp.periodicidad) = 'SEMESTRAL' THEN CEIL(CAST(SUBSTRING(c.periodo,6,2) AS UNSIGNED) / 6)
                WHEN UPPER(mp.periodicidad) = 'ANUAL' THEN 1
                ELSE CEIL(CAST(SUBSTRING(c.periodo,6,2) AS UNSIGNED) / 3)
            END
        ");

        // 2) HECHAS: hechasIdx[dep][periodicidad][seg] = hechas
        $hechasPorDepSeg = DB::table('cargas as c')
            ->join('detallecargas as dc', 'dc.id_carga', '=', 'c.id_carga')
            ->join('formularios as f', 'f.id_form', '=', 'c.id_form')
            ->join('metas_periodo as mp', function ($j) use ($ejercicio) {
                $j->on('mp.id_ind', '=', 'dc.id_ind')
                    ->where('mp.ejercicio', '=', $ejercicio);
            })
            ->whereIn('c.status_env', $estatusValidos)
            ->whereIn('dc.id_ind', array_keys($metaPorInd))
            ->whereRaw("c.periodo REGEXP '^[0-9]{4}-[0-9]{2}$'")
            ->select(
                'f.id_depen',
                'mp.periodicidad',
                DB::raw("({$segExpr->getValue(DB::connection()->getQueryGrammar())}) as seg"),
                DB::raw('COUNT(DISTINCT c.id_carga) as hechas')
            )
            ->groupBy('f.id_depen', 'mp.periodicidad', DB::raw('seg'))
            ->get();

        $hechasIdx = [];
        foreach ($hechasPorDepSeg as $r) {
            $d = (int) $r->id_depen;
            $p = trim((string) $r->periodicidad);
            $s = (int) $r->seg;
            $hechasIdx[$d][$p][$s] = (int) $r->hechas;
        }

        // 3) ESTATUS: estatusPorDepSeg[dep][periodicidad][seg][estatus] = total
        $estados = ['BORRADOR', 'ENVIADO', 'EN REVISION', 'REENVIADO', 'APROBADO', 'OBSERVADO', 'RECHAZADO'];

        $rows = DB::table('cargas as c')
            ->join('detallecargas as dc', 'dc.id_carga', '=', 'c.id_carga')
            ->join('formularios as f', 'f.id_form', '=', 'c.id_form')
            ->join('metas_periodo as mp', function ($j) use ($ejercicio) {
                $j->on('mp.id_ind', '=', 'dc.id_ind')
                    ->where('mp.ejercicio', '=', $ejercicio);
            })
            ->whereIn('dc.id_ind', array_keys($metaPorInd))
            ->whereIn('c.status_env', $estados)
            ->whereRaw("c.periodo REGEXP '^[0-9]{4}-[0-9]{2}$'")
            ->select(
                'f.id_depen',
                'mp.periodicidad',
                DB::raw("({$segExpr->getValue(DB::connection()->getQueryGrammar())}) as seg"),
                'c.status_env',
                DB::raw('COUNT(DISTINCT c.id_carga) as total')
            )
            ->groupBy('f.id_depen', 'mp.periodicidad', DB::raw('seg'), 'c.status_env')
            ->get();

        $this->estatusPorDepSeg = [];
        foreach ($rows as $r) {
            $d  = (int) $r->id_depen;
            $p  = trim((string) $r->periodicidad);
            $s  = (int) $r->seg;
            $st = strtoupper(trim((string) $r->status_env));
            $this->estatusPorDepSeg[$d][$p][$s][$st] = (int) $r->total;
        }

        // 4) CARDS: kardexPeriodosPorDep[dep][periodicidad] = cards[]
        $this->kardexPeriodosPorDep = [];

        foreach ($deps as $drow) {
            $idDep = (int) $drow->id_depen;

            foreach ($periodosDef as $peri => $maxSeg) {
                if (empty($metasIdx[$idDep][$peri] ?? [])) continue;

                $cards = [];
                for ($seg = 1; $seg <= $maxSeg; $seg++) {
                    $meta   = (int) ($metasIdx[$idDep][$peri][$seg] ?? 0);
                    $hechas = (int) ($hechasIdx[$idDep][$peri][$seg] ?? 0);

                    $hechasTop = $meta > 0 ? min($hechas, $meta) : 0;

                    if ($meta === 0) $estado = 'secondary';
                    elseif ($hechasTop >= $meta) $estado = 'success';
                    elseif ($hechasTop > 0) $estado = 'warning';
                    else $estado = 'danger';

                    $label = match ($peri) {
                        'Mensual'    => 'M' . $seg,
                        'Trimestral' => 'T' . $seg,
                        'Semestral'  => 'S' . $seg,
                        'Anual'      => 'A',
                        default      => $peri . ' ' . $seg,
                    };

                    $cards[] = [
                        'label' => $label,
                        'meta'  => $meta,
                        'hechas' => $hechasTop,
                        'estado' => $estado,
                        'seg'   => $seg,
                        'periodicidad' => $peri,
                    ];
                }

                $this->kardexPeriodosPorDep[$idDep][$peri] = $cards;
            }
        }

        return view('livewire.admin-dashboard');
    }
}
