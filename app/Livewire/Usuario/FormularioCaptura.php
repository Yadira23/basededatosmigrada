<?php

namespace App\Livewire\Usuario;

use Livewire\Component;
use App\Models\Region;

class FormularioCaptura extends Component
{
    public $metodo = null;
    public $region = '';
    public $valor = '';
    public $manualData = [];
    public $archivoNombre = '';
    public $archivoData = [];
    public $regiones;               // Todas las regiones
    public $regionesDisponibles;    // Regiones que aún no se han agregado

    public function mount()
    {
        $this->regiones = Region::all();
        $this->regionesDisponibles = $this->regiones;
    }

    // Seleccionar método
    public function seleccionar($metodo)
    {
        $this->metodo = $metodo;
        $this->resetArchivo();
    }

    // Agregar registro manual
    public function agregarManual()
    {
        if (!$this->region || !$this->valor) return;

        $region = Region::find($this->region);

        if (!$region) return;

        // Agregamos el registro a la tabla
        $this->manualData[] = [
            'region_id' => $region->id_region,
            'nombre_region' => $region->nombre_region,
            'valor' => $this->valor,
        ];

        // Eliminamos la región seleccionada de las disponibles
        $this->regionesDisponibles = $this->regionesDisponibles->reject(fn($r) => $r->id_region == $region->id_region);

        // Limpiamos los campos
        $this->region = '';
        $this->valor = '';
    }

    // Eliminar un registro manual
    public function eliminarManual($index)
    {
        $region_id = $this->manualData[$index]['region_id'];

        unset($this->manualData[$index]);
        $this->manualData = array_values($this->manualData);

        // Regresamos la región a disponibles
        $idsUsados = collect($this->manualData)->pluck('region_id');
        $this->regionesDisponibles = $this->regiones->whereNotIn('id_region', $idsUsados);
    }

    

    // Editar un registro manual
    public function editarManual($index)
    {
        $this->region = $this->manualData[$index]['region_id'];
        $this->valor = $this->manualData[$index]['valor'];
        $this->eliminarManual($index);
    }

    // Subir archivo
    public function updatedArchivo($archivo)
    {
        if (!$archivo) return;

        $this->archivoNombre = $archivo->getClientOriginalName();
        $contenido = file_get_contents($archivo->getRealPath());
        $lines = preg_split("/\r\n|\n/", $contenido);
        $this->archivoData = array_filter($lines, fn($line) => trim($line) !== '');
    }

    public function resetArchivo()
    {
        $this->archivoNombre = '';
        $this->archivoData = [];
    }

    public function render()
    {
        return view('livewire.usuarios.formulario-captura', [
            'regiones' => $this->regionesDisponibles, // Solo mostrar las disponibles
        ]);
    }

}
