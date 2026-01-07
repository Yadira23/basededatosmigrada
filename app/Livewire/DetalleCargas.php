<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\DetalleCarga;
use Livewire\Attributes\Computed;

class DetalleCargas extends Component
{
    use WithPagination;

	protected $paginationTheme = 'bootstrap';
    public $selected_id, $keyWord, $id_detalle, $id_carga, $id_ind, $id_region, $id_mun, $periodo_det, $ejercicio_det, $fecha_registro_det, $fuente_det, $valor_det;

    #[Computed]
	public function filteredDetalleCargas()
	{
		$keyWord = '%' . $this->keyWord . '%';
		return DetalleCarga::latest()
			->where(function ($query) use ($keyWord) {
				$query
						->orWhere('id_detalle', 'LIKE', $keyWord)
						->orWhere('id_carga', 'LIKE', $keyWord)
						->orWhere('id_ind', 'LIKE', $keyWord)
						->orWhere('periodo_det', 'LIKE', $keyWord)
						->orWhere('ejercicio_det', 'LIKE', $keyWord)
						->orWhere('fecha_registro_det', 'LIKE', $keyWord)
						->orWhere('fuente_det', 'LIKE', $keyWord)
						->orWhere('valor_det', 'LIKE', $keyWord);
			})
			->paginate(10);
	}

	public function render()
	{
		return view('livewire.detalleCargas.view', [
			'detalleCargas' => $this->filteredDetalleCargas,
		]);
	}
	
    public function cancel()
    {
        $this->reset();
    }

    public function save()
    {
        $this->validate([
		'id_carga' => 'required',
		'id_ind' => 'required',
		'periodo_det' => 'required',
		'ejercicio_det' => 'required',
		'fecha_registro_det' => 'required',
		'fuente_det' => 'required',
		'valor_det' => 'required',
        ]);

        DetalleCarga::updateOrCreate(
			['id_detalle' => $this->selected_id],
			[
				'id_carga' => $this-> id_carga,
				'id_ind' => $this-> id_ind,
				'id_region' => $this-> id_region,
				'id_mun' => $this-> id_mun,
				'periodo_det' => $this-> periodo_det,
				'ejercicio_det' => $this-> ejercicio_det,
				'fecha_registro_det' => $this-> fecha_registro_det,
				'fuente_det' => $this-> fuente_det,
				'valor_det' => $this-> valor_det
			]
		);

        $message = $this->selected_id ? 'DetalleCarga Successfully updated.' : 'DetalleCarga Successfully created.';
		$this->dispatch('closeModal');
        $this->reset();
		session()->flash('message', $message);
    }

    public function edit($id)
    {
        $this->selected_id = $id;
		$this->fill(DetalleCarga::findOrFail($id)->toArray());
    }

    public function destroy($id)
    {
        if ($id) {
            DetalleCarga::where('id_detalle', $id)->delete();
        }
    }
}