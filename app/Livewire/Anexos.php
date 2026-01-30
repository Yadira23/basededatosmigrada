<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use App\Models\Anexo;
use App\Models\Formulario;
use Illuminate\Support\Facades\Storage;
use App\Models\Indicador;


class Anexos extends Component
{
    use WithPagination, WithFileUploads;

    protected $paginationTheme = 'bootstrap';

    public $selected_id, $keyWord;
    public $nombre_anexo, $tipo_anexo, $archivo, $guia_anexo, $fin_proposito_anexo, $id_form, $id_ind;
    public $formularios;
    public $indicadores;

    protected $rules = [
        'nombre_anexo' => 'required|string|min:5|max:255',
        'tipo_anexo' => 'required|in:plantilla,guia',
        'archivo' => 'required|file',
        'guia_anexo' => 'nullable|string|max:255',
        'fin_proposito_anexo' => 'required|string|max:255',
        'id_form' => 'required|exists:formularios,id_form',
        'id_ind' => 'required|exists:indicadores,id_ind',
    ];

    public function mount()
    {
        $this->formularios = Formulario::all();
        $this->indicadores = Indicador::all();
        // Si no hay formularios, redirige al index de formularios
        if ($this->formularios->isEmpty()) {
            return redirect('/formularios'); // redirige directo a la URL
        }
    }

    public function crearAnexo()
    {
        // Verifica de nuevo por seguridad
        if (!$this->id_form) {
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
        if (!$this->archivo) return;

        // permite xlsx/xls/csv/pdf/doc/docx/jpg/png según lo que quieras
        $this->validate([
            'archivo' => 'required|file|max:10240|mimes:xlsx,xls,csv,pdf,doc,docx,jpg,jpeg,png',
        ]);
    }

    public function save()
    {
        $this->validate();

        // Guardar archivo en storage
        $subcarpeta = $this->tipo_anexo === 'plantilla' ? 'anexos/plantillas' : 'anexos/guias';
        $ruta = $this->archivo->store($subcarpeta, 'public');

        // ✅ Si está editando, borra archivo anterior (si subió uno nuevo)
        if ($this->selected_id) {
            $prev = Anexo::find($this->selected_id);
            if ($prev && $prev->ruta_archivo_anexo && Storage::disk('public')->exists($prev->ruta_archivo_anexo)) {
                Storage::disk('public')->delete($prev->ruta_archivo_anexo);
            }
        }

        $ext = $this->archivo->getClientOriginalExtension();
        $nombre = trim((string)$this->nombre_anexo);

        if ($nombre === '') {
            $nombre = $this->archivo->getClientOriginalName();
        } else {
            // si no trae extensión, se la agregamos
            if (!preg_match('/\.[a-z0-9]+$/i', $nombre)) {
                $nombre .= '.' . $ext;
            }
        }

        $this->nombre_anexo = $nombre;

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
                'id_ind' => $this->id_ind,
            ]
        );

        session()->flash('message', $this->selected_id ? 'Anexo actualizado' : 'Anexo creado');
        $this->reset(['nombre_anexo', 'tipo_anexo', 'archivo', 'guia_anexo', 'fin_proposito_anexo', 'id_form', 'id_ind', 'selected_id']);
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
        $this->id_ind = $anexo->id_ind;
    }

    public function destroy($id)
    {
        $anexo = Anexo::find($id);

        if ($anexo) {
            if ($anexo->ruta_archivo_anexo && Storage::disk('public')->exists($anexo->ruta_archivo_anexo)) {
                Storage::disk('public')->delete($anexo->ruta_archivo_anexo);
            }
            $anexo->delete();
        }
    }

    public function render()
    {
        $keyWord = '%' . $this->keyWord . '%';
        return view('livewire.anexos.view', [
            'anexos' => Anexo::with(['formulario', 'indicador'])
                ->where('nombre_anexo', 'LIKE', $keyWord)
                ->latest()
                ->paginate(10),
            'formularios' => $this->formularios,
            'indicadores' => $this->indicadores,
        ]);
    }

    public function cancel()
    {
        $this->reset(['nombre_anexo', 'tipo_anexo', 'archivo', 'guia_anexo', 'fin_proposito_anexo', 'id_form', 'selected_id']);
        $this->dispatch('closeModal'); // si usas modal
    }
}
