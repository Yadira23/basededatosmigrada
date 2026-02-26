<?php

namespace App\Livewire\Carga;

use App\Models\Formulario;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class CargaCreate extends Component
{
    // 🔹 ESTADOS (variables vivas)

    public $formulario;

    public $indicadores = [];

    public $anexos = [];

    public $tipoCarga = null; // anexo | web

    public $archivo;     // opción anexo

    public $datosWeb = []; // opción web

    // 🔹 ESTE MÉTODO SE EJECUTA AL ENTRAR A LA VISTA
    public function mount()
    {
        if (! Auth::check()) {
            return redirect()->route('login');
        }

        $usuario = Auth::user();

        $this->formulario = Formulario::with(['indicador', 'anexos'])
            ->where('id_depen', $usuario->id_depen)
            ->first();

        if ($this->formulario) {
            $this->indicadores = $this->formulario->indicador
                ? [$this->formulario->indicador]
                : [];

            $this->anexos = $this->formulario->anexos ?? [];
        }
    }

    // 🔹 SOLO CAMBIA EL ESTADO, NO GUARDA
    public function render()
    {
        return view('livewire.carga.carga-create');
    }
}
