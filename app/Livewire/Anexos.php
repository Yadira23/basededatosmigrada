<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Anexo;
use Livewire\Attributes\Computed;
use App\Models\Formulario;

class Anexos extends Component
{
    use WithPagination;

	protected $paginationTheme = 'bootstrap';
    public $selected_id, $keyWord, $id_anexo, $nombre_anexo, $tipo_anexo, $peso_anexo, $guia_anexo, $fin_proposito_anexo, $fecha_subida_anexo, $ruta_archivo_anexo, $id_form;

    #[Computed]
	public function filteredAnexos()
	{
		$keyWord = '%' . $this->keyWord . '%';
		return Anexo::latest()
			->where(function ($query) use ($keyWord) {
				$query
						->orWhere('id_anexo', 'LIKE', $keyWord)
						->orWhere('nombre_anexo', 'LIKE', $keyWord)
						->orWhere('tipo_anexo', 'LIKE', $keyWord)
						->orWhere('peso_anexo', 'LIKE', $keyWord)
						->orWhere('guia_anexo', 'LIKE', $keyWord)
						->orWhere('fin_proposito_anexo', 'LIKE', $keyWord)
						->orWhere('fecha_subida_anexo', 'LIKE', $keyWord)
						->orWhere('ruta_archivo_anexo', 'LIKE', $keyWord)
						->orWhere('id_form', 'LIKE', $keyWord);
			})
			->paginate(10);
	}

	public function render()
    {
        $keyWord = '%' . $this->keyWord . '%';

        return view('livewire.anexos.view', [
            'anexos' => Anexo::where('nombre_anexo', 'LIKE', $keyWord)
                ->latest()
                ->paginate(10),
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
            'nombre_anexo' => 'required|string|min:5|max:255',
            'tipo_anexo' => 'required|in:PDF,WORD,EXCEL,IMAGEN,OTRO',
            'peso_anexo' => 'required|numeric|min:1',
            'guia_anexo' => 'nullable|string|max:255',
            'fin_proposito_anexo' => 'required|string|max:255',
            'fecha_subida_anexo' => 'required|date',
            'ruta_archivo_anexo' => 'required|string|max:255',
            'id_form' => 'required|exists:formularios,id_form',
        ]);

        Anexo::updateOrCreate(
            ['id_anexo' => $this->selected_id],
            [
                'nombre_anexo' => $this->nombre_anexo,
                'tipo_anexo' => $this->tipo_anexo,
                'peso_anexo' => $this->peso_anexo,
                'guia_anexo' => $this->guia_anexo,
                'fin_proposito_anexo' => $this->fin_proposito_anexo,
                'fecha_subida_anexo' => $this->fecha_subida_anexo,
                'ruta_archivo_anexo' => $this->ruta_archivo_anexo,
                'id_form' => $this->id_form,
            ]
        );

        session()->flash('message', $this->selected_id ? 'Anexo actualizado' : 'Anexo creado');
        $this->reset();
        $this->dispatch('closeModal');
    }

    public function edit($id)
    {
        $anexo = Anexo::findOrFail($id);
        $this->selected_id = $anexo->id_anexo;
        $this->fill($anexo->toArray());
    }

    public function destroy($id)
    {
        if ($id) {
            Anexo::where('id_anexo', $id)->delete();
        }
    }
}