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
        $this->borradoresPorDep = DB::table('cargas as c')
            ->join('formularios as f', 'f.id_form', '=', 'c.id_form')
            ->where('c.status_env', 'BORRADOR')
            ->groupBy('f.id_depen')
            ->pluck(DB::raw('COUNT(*) as total'), 'f.id_depen')
            ->toArray();

        $this->totalBorradores = array_sum($this->borradoresPorDep);

        return view('livewire.admin-dashboard');
    }
}
