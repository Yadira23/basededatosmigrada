<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Carga;
use Livewire\Attributes\Computed;
use App\Models\Formulario;

class Cargas extends Component
{
    use WithPagination;

	protected $paginationTheme = 'bootstrap';
    public $selected_id, $keyWord, $id_carga, $folioUnico_carga, $fecha_carga, $periodo, $status_env, $descripcion_env, $observacion_env, $id_form;

    #[Computed]
	public function filteredCargas()
	{
		$keyWord = '%' . $this->keyWord . '%';
		return Carga::latest()
			->where(function ($query) use ($keyWord) {
				$query
						->orWhere('folioUnico_carga', 'LIKE', $keyWord)
						->orWhere('fecha_carga', 'LIKE', $keyWord)
						->orWhere('periodo', 'LIKE', $keyWord)
						->orWhere('status_env', 'LIKE', $keyWord)
						->orWhere('descripcion_env', 'LIKE', $keyWord)
						->orWhere('observacion_env', 'LIKE', $keyWord)
						->orWhere('id_form', 'LIKE', $keyWord);
			})
			->paginate(10);
	}

	public function render()
	{
		return view('livewire.cargas.view', [
			'cargas' => $this->filteredCargas,
			'formularios' => Formulario::all(),
		]);
	}
	
    public function cancel()
    {
        $this->reset();
    }

    public function save()
    {
        $this->validate([
		'folioUnico_carga' => 'required',
		'fecha_carga' => 'required',
		'periodo' => 'required',
		'status_env' => 'required',
		'descripcion_env' => 'required',
		'observacion_env' => 'required',
		'id_form' => 'required',
        ]);

        Carga::updateOrCreate(
			['id_carga' => $this->selected_id],
			[
				'folioUnico_carga' => $this-> folioUnico_carga,
				'fecha_carga' => $this-> fecha_carga,
				'periodo' => $this-> periodo,
				'status_env' => $this-> status_env,
				'descripcion_env' => $this-> descripcion_env,
				'observacion_env' => $this-> observacion_env,
				'id_form' => $this-> id_form
			]
		);

        $this->dispatch('closeModal');
        $this->reset();

        session()->flash(
            'message',
            $this->selected_id
                ? 'Carga actualizada correctamente'
                : 'Carga creada correctamente'
        );
    
    }

    public function edit($id)
    {
        $this->selected_id = $id;
		$this->fill(Carga::findOrFail($id)->toArray());
    }

    public function destroy($id)
    {
        if ($id) {
            Carga::where('id_carga', $id)->delete();
        }
    }
}