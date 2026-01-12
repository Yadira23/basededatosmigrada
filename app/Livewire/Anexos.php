<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use App\Models\Anexo;
use App\Models\Formulario;
use Illuminate\Support\Facades\Storage;

class Anexos extends Component
{
    use WithPagination, WithFileUploads;

    protected $paginationTheme = 'bootstrap';

    public $selected_id, $keyWord;
    public $nombre_anexo, $tipo_anexo, $archivo, $guia_anexo, $fin_proposito_anexo, $id_form;

    public $formularios;

    protected $rules = [
        'nombre_anexo' => 'required|string|min:5|max:255',
        'tipo_anexo' => 'required|in:PDF,WORD,EXCEL,IMAGEN,OTRO',
        'archivo' => 'required|file',
        'guia_anexo' => 'nullable|string|max:255',
        'fin_proposito_anexo' => 'required|string|max:255',
        'id_form' => 'required|exists:formularios,id_form',
    ];

    public function mount()
    {
        $this->formularios = Formulario::all();
        // Si no hay formularios, redirige al index de formularios
    if ($this->formularios->isEmpty()) {
    return redirect('/formularios'); // redirige directo a la URL
}

    }

    public function crearAnexo()
    {
        // Verifica de nuevo por seguridad
        if(!$this->id_form) {
            session()->flash('error', 'Debes seleccionar un formulario antes de subir un anexo.');
            return;
        }

        Anexo::create([
            'nombre_anexo' => $this->nombre_anexo,
            'tipo_anexo' => $this->tipo_anexo,
            'id_form' => $this->id_form
        ]);

        session()->flash('success', 'Anexo creado correctamente.');
    
    }

    public function updatedArchivo()
    {
        // Validar peso y extensión según tipo
        $tipos = [
            'PDF' => ['mimes' => 'pdf', 'max' => 5120],       // 5 MB
            'WORD' => ['mimes' => 'doc,docx', 'max' => 5120], // 5 MB
            'EXCEL' => ['mimes' => 'xls,xlsx', 'max' => 5120],// 5 MB
            'IMAGEN' => ['mimes' => 'jpg,jpeg,png,gif', 'max' => 3072], // 3 MB
            'OTRO' => ['mimes' => '*', 'max' => 2048],        // 2 MB
        ];

        $tipo = $this->tipo_anexo;
        $this->validate([
            'archivo' => "required|file|mimes:{$tipos[$tipo]['mimes']}|max:{$tipos[$tipo]['max']}",
        ]);
    }

    public function save()
    {
        $this->validate();

        // Guardar archivo en storage
        $ruta = $this->archivo->store('anexos', 'public');

        Anexo::updateOrCreate(
            ['id_anexo' => $this->selected_id],
            [
                'nombre_anexo' => $this->nombre_anexo,
                'tipo_anexo' => $this->tipo_anexo,
                'peso_anexo' => $this->archivo->getSize(), // en bytes
                'guia_anexo' => $this->guia_anexo,
                'fin_proposito_anexo' => $this->fin_proposito_anexo,
                'fecha_subida_anexo' => now(),
                'ruta_archivo_anexo' => $ruta,
                'id_form' => $this->id_form,
            ]
        );

        session()->flash('message', $this->selected_id ? 'Anexo actualizado' : 'Anexo creado');
        $this->reset(['nombre_anexo','tipo_anexo','archivo','guia_anexo','fin_proposito_anexo','id_form','selected_id']);
        $this->dispatch('closeModal');
    }

    public function edit($id)
    {
        $anexo = Anexo::findOrFail($id);
        $this->selected_id = $anexo->id_anexo;
        $this->nombre_anexo = $anexo->nombre_anexo;
        $this->tipo_anexo = $anexo->tipo_anexo;
        $this->guia_anexo = $anexo->guia_anexo;
        $this->fin_proposito_anexo = $anexo->fin_proposito_anexo;
        $this->id_form = $anexo->id_form;
    }

    public function destroy($id)
    {
        if ($id) {
            $anexo = Anexo::find($id);
            if ($anexo) {
                // Borrar archivo físico
                Storage::disk('public')->delete($anexo->ruta_archivo_anexo);
                $anexo->delete();
            }
        }
    }

    public function render()
    {
        $keyWord = '%' . $this->keyWord . '%';
        return view('livewire.anexos.view', [
            'anexos' => Anexo::with('formulario')
                ->where('nombre_anexo', 'LIKE', $keyWord)
                ->latest()
                ->paginate(10),
            'formularios' => $this->formularios,
        ]);
    }

    function formatoPeso($bytes) {
    $unidades = ['B', 'KB', 'MB', 'GB', 'TB'];
    $i = 0;
    while ($bytes >= 1024 && $i < count($unidades) - 1) {
        $bytes /= 1024;
        $i++;
    }
    return round($bytes, 2) . ' ' . $unidades[$i];
}

public function cancel()
{
    $this->reset(['nombre_anexo','tipo_anexo','archivo','guia_anexo','fin_proposito_anexo','id_form','selected_id']);
    $this->dispatch('closeModal'); // si usas modal
}


}
