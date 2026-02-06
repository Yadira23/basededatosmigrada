<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\DB;


class AdminDashboard extends Component
{
    public $totalUsuarios;
    public $totalFormularios;
    public $cargasPendientes;
    public $nuevosRegistros;

    public $formulariosPorMes = [];
    public $usuariosPorRol = [];
    public $cargasPorEstado = [];

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

    public function render()
    {
        return view('livewire.admin-dashboard');
    }
}
