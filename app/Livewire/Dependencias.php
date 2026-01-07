<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Dependencia;
use Livewire\Attributes\Computed;
use App\Models\Sector;



class Dependencias extends Component
{
    use WithPagination;

	protected $paginationTheme = 'bootstrap';
    public $selected_id, $keyWord, $id_depen, $nombre_depen, $id_sector, $email_depen, $extension_depen, $telefono_depen, $calle_depen, $numerocalle_depen, $colonia_depen, $cp_depen;

    #[Computed]
	public function filteredDependencias()
	{
		$keyWord = '%' . $this->keyWord . '%';
		return Dependencia::latest()
			->where(function ($query) use ($keyWord) {
				$query
						->orWhere('id_depen', 'LIKE', $keyWord)
						->orWhere('nombre_depen', 'LIKE', $keyWord)
						->orWhere('id_sector', 'LIKE', $keyWord)
						->orWhere('email_depen', 'LIKE', $keyWord)
						->orWhere('extension_depen', 'LIKE', $keyWord)
						->orWhere('telefono_depen', 'LIKE', $keyWord)
						->orWhere('calle_depen', 'LIKE', $keyWord)
						->orWhere('numerocalle_depen', 'LIKE', $keyWord)
						->orWhere('colonia_depen', 'LIKE', $keyWord)
						->orWhere('cp_depen', 'LIKE', $keyWord);
			})
			->paginate(10);
	}

	public function render()
{
    return view('livewire.dependencias.view', [
        'dependencias' => $this->filteredDependencias,
        'sector' => Sector::all(), // 👈 catálogo
    ]);
}

	
    public function cancel()
    {
        $this->reset();
    }

    public function save()
{
    $this->validate([

    'nombre_depen' => [
        'required',
        'string',
        'min:5',
        'max:255',
        'regex:/^[A-Za-zÁÉÍÓÚÜÑáéíóúüñ\s]+$/',
        'unique:dependencias,nombre_depen,' . $this->selected_id . ',id_depen',
    ],

    'id_sector' => [
        'required',
        'exists:sectores,id_sector',
    ],

    'email_depen' => [
        'required',
        'email:rfc,dns',
        'max:255',
        'unique:dependencias,email_depen,' . $this->selected_id . ',id_depen',
    ],

    'extension_depen' => [
        'nullable',
        'regex:/^[0-9]{1,6}$/',
    ],

    'telefono_depen' => [
        'required',
        'regex:/^[0-9]{10}$/',
    ],

    'calle_depen' => [
        'required',
        'string',
        'max:255',
        'regex:/^[A-Za-z0-9ÁÉÍÓÚÜÑáéíóúüñ\s\.\-#]+$/',
    ],

    'numerocalle_depen' => [
        'required',
        'regex:/^[0-9]{1,5}$/',
    ],

    'colonia_depen' => [
        'required',
        'string',
        'max:255',
        'regex:/^[A-Za-zÁÉÍÓÚÜÑáéíóúüñ\s]+$/',
    ],

    'cp_depen' => [
        'required',
        'regex:/^[0-9]{5}$/',
    ],

]);


    Dependencia::updateOrCreate(
        ['id_depen' => $this->selected_id],
        [
            'nombre_depen' => $this->nombre_depen,
            'id_sector' => $this->id_sector,
            'email_depen' => $this->email_depen,
            'extension_depen' => $this->extension_depen,
            'telefono_depen' => $this->telefono_depen,
            'calle_depen' => $this->calle_depen,
            'numerocalle_depen' => $this->numerocalle_depen,
            'colonia_depen' => $this->colonia_depen,
            'cp_depen' => $this->cp_depen,
        ]
    );

    $this->reset();
    session()->flash('message', 'Dependencia guardada correctamente');
}



    public function edit($id)
    {
        $dep = Dependencia::findOrFail($id);

    $this->id_depen            = $dep->id_depen;
    $this->nombre_depen        = $dep->nombre_depen;
    $this->id_sector           = $dep->id_sector;
    $this->email_depen         = $dep->email_depen;
    $this->extension_depen     = $dep->extension_depen;
    $this->telefono_depen      = $dep->telefono_depen;
    $this->calle_depen         = $dep->calle_depen;
    $this->numerocalle_depen   = $dep->numerocalle_depen;
    $this->colonia_depen       = $dep->colonia_depen;
    $this->cp_depen            = $dep->cp_depen;

    // Para saber que estamos editando y no creando
    $this->selected_id = $dep->id_depen;
}

    public function destroy($id)
{
    Dependencia::where('id_depen', $id)->delete();
}

}