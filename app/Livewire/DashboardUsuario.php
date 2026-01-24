<?php

namespace App\Livewire;

use Livewire\Component;

use App\Models\Formulario;
use App\Models\Carga;
use Illuminate\Support\Facades\Auth;

class DashboardUsuario extends Component
{
    public $formulariosDisponibles;
    public $cargasRealizadas;

    public function mount()
    {
        $this->formulariosDisponibles = Formulario::where('boton_accion_form', 'Ver')
            ->where('id_depen', Auth::user()->id_depen)
            ->count();

        $this->cargasRealizadas = Carga::whereHas('formulario', function ($q) {
            $q->where('id_depen', Auth::user()->id_depen);
        })->count();
    }

    public function render()
    {
        return view('livewire.dashboard-usuario');
    }
}
