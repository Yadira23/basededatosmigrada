<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Formulario;
use Illuminate\Support\Facades\Auth;

class UsuarioFormularios extends Component
{
    public $formularios;
    public function mount()
    {
        // Trae solo los formularios "Ver" de la dependencia del usuario logueado
        $this->formularios = Formulario::where('boton_accion_form', 'Ver')
            ->where('id_depen', Auth::user()->id_depen)
            ->get();
    }

    public function render()
    {
        return view('livewire.usuarios.usuario-formularios', [
            'formularios' => $this->formularios
        ]);
    }

    // Puedes agregar esta función si quieres abrir el formulario para responder
    public function abrirFormulario($id_form)
    {
        // Aquí rediriges o cargas la vista del formulario para que el usuario lo complete
        return redirect()->route('usuario.formulario.captura', $id_form);
    }
}
