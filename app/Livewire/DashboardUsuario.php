<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Formulario;
use App\Models\Carga;
use Illuminate\Support\Facades\Auth;

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

    public function mount()
    {
        $user = Auth::user();
        $idDepen = $user->id_depen;

        // Nombre de dependencia (ya lo tienes en layout, aquí para el dashboard)
        $this->dependenciaNombre = $user->dependencia->nombre_depen ?? null;

        // Formularios disponibles
        $this->formulariosDisponibles = Formulario::where('boton_accion_form', 'Ver')
            ->where('id_depen', $idDepen)
            ->count();

        // Base: cargas de la dependencia (misma lógica que ya usabas)
        $base = Carga::whereHas('formulario', function ($q) use ($idDepen) {
            $q->where('id_depen', $idDepen);
        });

        $this->cargasRealizadas = (clone $base)->count();

        // Últimas cargas
        $this->ultimasCargas = (clone $base)
            ->latest('created_at')
            ->take(5)
            ->get();

        /**
         * Pendientes / Observaciones
         * IMPORTANTE: ajusta estos status a los reales de tu BD.
         * Si no existen, se quedarán en 0 (no truena).
         */
        $this->pendientes = (clone $base)->whereIn('status_env', ['Pendiente', 'Borrador'])->count();
        $this->observaciones = (clone $base)->whereIn('status_env', ['Observado', 'Rechazado'])->count();
    }

    public function render()
    {
        return view('livewire.dashboard-usuario');
    }

    // Helper para color del badge (lo usamos en la vista)
    public function badgeClass($status): string
    {
        $st = strtolower((string) $status);

        if (str_contains($st, 'rech')) return 'danger';
        if (str_contains($st, 'obs'))  return 'warning';
        if (str_contains($st, 'pend') || str_contains($st, 'borr')) return 'secondary';
        if (str_contains($st, 'env'))  return 'primary';
        if (str_contains($st, 'aprob') || str_contains($st, 'final')) return 'success';

        return 'primary';
    }
}
