<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Formulario;
use Livewire\Attributes\Computed;
use Illuminate\Support\Facades\Auth;
use App\Models\Dependencia;
use App\Models\Usuario;



class Formularios extends Component
{
    use WithPagination;

	protected $paginationTheme = 'bootstrap';
    public $selected_id, $keyWord, $id_form, $titulo_form, $fecha_creacion_form, $descripcion_form, $boton_accion_form, $secciones_form, $periodo_form, $id_depen, $id_usr;

    #[Computed]
	public function filteredFormularios()
	{
		$keyWord = '%' . $this->keyWord . '%';
		return Formulario::latest()
			->where(function ($query) use ($keyWord) {
				$query
						->orWhere('titulo_form', 'LIKE', $keyWord)
						->orWhere('fecha_creacion_form', 'LIKE', $keyWord)
						->orWhere('descripcion_form', 'LIKE', $keyWord)
						->orWhere('boton_accion_form', 'LIKE', $keyWord)
						->orWhere('secciones_form', 'LIKE', $keyWord)
						->orWhere('periodo_form', 'LIKE', $keyWord)
						->orWhere('id_depen', 'LIKE', $keyWord)
						->orWhere('id_usr', 'LIKE', $keyWord);
			})
			->paginate(10);
	}

	public function render()
	{
		return view('livewire.formularios.view', [
			'formularios' => $this->filteredFormularios,
			'dependencias' => Dependencia::all(),
			'usuarios'      => Usuario::all(), 
		]);
	}
	
    public function cancel()
    {
        $this->reset();
    }

    public function save()
    {
        $this->validate([
		'titulo_form' => 'required',
		'fecha_creacion_form' => 'required',
		'boton_accion_form' => 'required',
		'secciones_form' => 'required',
		'periodo_form' => 'required',
		'id_depen' => 'required',
		'id_usr' => 'required',
        ]);

        Formulario::updateOrCreate(
			['id_form' => $this->selected_id],
			[
				'titulo_form' => $this-> titulo_form,
				'fecha_creacion_form' => $this-> fecha_creacion_form,
				'descripcion_form' => $this-> descripcion_form,
				'boton_accion_form' => $this-> boton_accion_form,
				'secciones_form' => $this-> secciones_form,
				'periodo_form' => $this-> periodo_form,
				'id_depen' => $this-> id_depen,
				'id_usr' => $this->id_usr,

			]
		);

        $this->dispatch('closeModal');
        $this->reset();

        session()->flash(
            'message',
            $this->selected_id
                ? 'Formulario actualizado correctamente'
                : 'Formulario creado correctamente'
        );
    }

    public function edit($id)
    {
        $formulario = Formulario::findOrFail($id);

        $this->selected_id = $formulario->id_form;
        $this->titulo_form = $formulario->titulo_form;
        $this->fecha_creacion_form = $formulario->fecha_creacion_form;
        $this->descripcion_form = $formulario->descripcion_form;
        $this->boton_accion_form = $formulario->boton_accion_form;
        $this->secciones_form = $formulario->secciones_form;
        $this->periodo_form = $formulario->periodo_form;
        $this->id_depen = $formulario->id_depen;
        $this->id_usr = $formulario->id_usr;
    }

    public function destroy($id)
    {
        if ($id) {
            Formulario::where('id_form', $id)->delete();
        }
    }
}