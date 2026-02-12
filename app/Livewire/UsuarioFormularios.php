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
        $this->formularios = Formulario::whereIn('boton_accion_form', ['Publicado', 'Ver'])
            ->where('id_depen', Auth::user()->id_depen)
            ->with('indicador') // ✅ clave para usar $formulario->indicador
            ->orderByDesc('id_form')
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
        $form = Formulario::select('id_form', 'id_ind')
            ->where('id_form', $id_form)
            ->firstOrFail();

        // ✅ DEFENSA: por si el formulario no tiene indicador
        if (!$form->id_ind) {
            session()->flash('error', 'Este formulario no tiene indicador asignado.');
            return;
        }

        return redirect()->route('usuario.formulario.captura', [
            'id_form' => $form->id_form,
            'id_ind'  => $form->id_ind,
        ]);
    }
}
