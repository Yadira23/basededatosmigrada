<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;


class AdminDashboard extends Component
{
    public $totalUsuarios;
    public $totalFormularios;
    public $cargasPendientes;
    public $nuevosRegistros;

    public $formulariosPorMes = [];
    public $usuariosPorRol = [];
    public $cargasPorEstado = [];
    public $borradoresPorDep = [];   // [id_depen => total]
    public $totalBorradores = 0;
    public $avancePorDep = []; // [id_depen => ['meta'=>, 'hechos'=>, 'pct'=>, 'color'=>]]


    public function mount()
    {
        // Usuarios reales (tabla usuarios)
        $this->totalUsuarios = DB::table('usuarios')->count();
        $this->nuevosRegistros = DB::table('usuarios')
            ->whereDate('created_at', now())
            ->count();

        // Formularios reales
        $this->totalFormularios = DB::table('formularios')->count();

        $this->formulariosPorMes = DB::table('formularios')
            ->selectRaw('MONTH(created_at) as mes, COUNT(*) as total')
            ->whereYear('created_at', now()->year)
            ->groupBy('mes')
            ->pluck('total', 'mes')
            ->toArray();

        // Roles desde Spatie conectados a usuarios
        $this->usuariosPorRol = DB::table('model_has_roles')
            ->join('roles', 'roles.id', '=', 'model_has_roles.role_id')
            ->select('roles.name', DB::raw('count(*) as total'))
            ->where('model_type', 'App\\Models\\Usuario')
            ->groupBy('roles.name')
            ->pluck('total', 'roles.name')
            ->toArray();

        // Cargas
        $this->cargasPendientes = DB::table('cargas')
            ->where('status_env', 'EN REVISION')
            ->count();

        $this->cargasPorEstado = DB::table('cargas')
            ->select('status_env', DB::raw('count(*) as total'))
            ->groupBy('status_env')
            ->pluck('total', 'status_env')
            ->toArray();
    }

    public function recordarBorradoresGlobal()
    {
        // tomar dependencias que tienen borradores
        $depIds = array_keys($this->borradoresPorDep);

        foreach ($depIds as $id_depen) {
            $this->recordarBorradoresDependencia($id_depen, true); // true = silencioso (sin spam de mensajes)
        }

        session()->flash('message', 'Recordatorio enviado a dependencias con borradores.');
    }

    public function recordarBorradoresDependencia($id_depen, $silent = false)
    {
        // ✅ 1) Obtener usuarios de esa dependencia
        // CAMBIA 'id_depen' si tu columna en usuarios tiene otro nombre
        $usuariosIds = DB::table('usuarios')
            ->where('id_depen', $id_depen)
            ->pluck('id_usuario')
            ->toArray();

        if (empty($usuariosIds)) {
            if (!$silent) session()->flash('message', 'No hay usuarios registrados en esa dependencia.');
            return;
        }

        // ✅ 2) Anti-spam: no repetir en últimas 12h
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
            if (in_array($id_usuario, $recientes)) continue;

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
            if (!$silent) session()->flash('message', 'Recordatorio enviado a la dependencia seleccionada.');
        } else {
            if (!$silent) session()->flash('message', 'Ya se envió un recordatorio recientemente (últimas 12h).');
        }
    }

    public function render()
    {
        // ✅ borradores por dependencia (ya lo tenías)
        $this->borradoresPorDep = DB::table('cargas as c')
            ->join('formularios as f', 'f.id_form', '=', 'c.id_form')
            ->where('c.status_env', 'BORRADOR')
            ->groupBy('f.id_depen')
            ->pluck(DB::raw('COUNT(*) as total'), 'f.id_depen')
            ->toArray();

        $this->totalBorradores = array_sum($this->borradoresPorDep);

        /* =========================================================
       ✅ NUEVO: AVANCE vs META por dependencia
       Meta = # formularios asignados a la dependencia
       Hechos = # formularios con al menos 1 carga válida (sin BORRADOR)
    ========================================================= */
        $estatusValidos = ['ENVIADO', 'EN REVISION', 'REENVIADO', 'APROBADO'];

        // Meta por dependencia
        $metas = DB::table('formularios')
            ->select('id_depen', DB::raw('COUNT(DISTINCT id_form) as meta'))
            ->groupBy('id_depen')
            ->pluck('meta', 'id_depen')
            ->toArray();

        // Completados por dependencia
        $hechos = DB::table('cargas as c')
            ->join('formularios as f', 'f.id_form', '=', 'c.id_form')
            ->whereIn('c.status_env', $estatusValidos)
            ->select('f.id_depen', DB::raw('COUNT(DISTINCT c.id_form) as hechos'))
            ->groupBy('f.id_depen')
            ->pluck('hechos', 'f.id_depen')
            ->toArray();

        // Armar arreglo final con pct + color
        $this->avancePorDep = [];
        foreach ($metas as $id_depen => $meta) {
            $meta = (int)$meta;
            $done = (int)($hechos[$id_depen] ?? 0);

            $pct = $meta > 0 ? (int)round(($done / $meta) * 100) : 0;

            // semáforo (si meta=0, gris)
            $color = $meta === 0 ? 'secondary' : ($pct >= 80 ? 'success' : ($pct >= 40 ? 'warning' : 'danger'));

            $this->avancePorDep[$id_depen] = [
                'meta'  => $meta,
                'hechos' => $done,
                'pct'   => $pct,
                'color' => $color,
            ];
        }

        return view('livewire.admin-dashboard');
    }
}
