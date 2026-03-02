<?php

namespace App\Livewire;

use App\Models\Carga;
use App\Models\DetalleCarga;
use App\Models\Indicador;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithPagination;

class DetalleCargas extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    // ✅ filtro por ruta /detallecargas/{id_carga}
    public $id_carga_filtro = null;

    public $selected_id;

    public $keyWord;

    public $id_detalle;

    public $id_carga;

    public $id_ind;

    public $periodo_det;

    public $ejercicio_det;

    public $fecha_registro_det;

    public $fuente_det;

    public $valor_det;

    public function mount($id_carga = null)
    {
        // No hay cargas → manda a cargas
        if (! Carga::exists()) {
            session()->flash('error', 'Debes registrar al menos una Carga.');

            return redirect()->to('/cargas');
        }

        // Sí hay cargas, pero NO indicadores → manda a indicadores
        if (! Indicador::exists()) {
            session()->flash('error', 'Debes registrar al menos un Indicador.');

            return redirect()->to('/indicadores');
        }

        // ✅ si viene de la ruta, guardamos el filtro
        if ($id_carga !== null) {

            // si viene numérico, es id_carga
            if (is_numeric($id_carga)) {
                $this->id_carga_filtro = (int) $id_carga;
            } else {
                // si viene string, es folio → buscar id_carga real
                $c = Carga::where('folioUnico_carga', $id_carga)->first();
                $this->id_carga_filtro = $c?->id_carga;
            }
        }
    }

    // ✅ cuando escribes en search, vuelve a la página 1
    public function updatingKeyWord()
    {
        $this->resetPage();
    }

    #[Computed]
    public function filteredDetalleCargas()
    {
        $keyWord = '%' . ($this->keyWord ?? '') . '%';

        $query = DetalleCarga::with(['carga', 'indicador', 'region', 'municipio'])
            ->latest();

        // ✅ aplicar filtro por carga si viene en la ruta
        if (! empty($this->id_carga_filtro)) {
            $query->where('id_carga', $this->id_carga_filtro);
        }

        // ✅ buscador
        if (! empty($this->keyWord)) {
            $query->where(function ($q) use ($keyWord) {
                $q->orWhere('id_detalle', 'LIKE', $keyWord)
                    ->orWhere('id_carga', 'LIKE', $keyWord)
                    ->orWhere('id_ind', 'LIKE', $keyWord)
                    ->orWhere('periodo_det', 'LIKE', $keyWord)
                    ->orWhere('ejercicio_det', 'LIKE', $keyWord)
                    ->orWhere('fecha_registro_det', 'LIKE', $keyWord)
                    ->orWhere('fuente_det', 'LIKE', $keyWord)
                    ->orWhere('valor_det', 'LIKE', $keyWord);
            });
        }

        return $query->paginate(10);
    }

    public function render()
    {
        return view('livewire.detalleCargas.view', [
            'detalleCargas' => $this->filteredDetalleCargas,
            'id_carga_filtro' => $this->id_carga_filtro, // por si lo quieres mostrar en pantalla
        ])->extends('layouts.app')->section('content');
    }

    public function cancel()
    {
        $this->reset();
        // ✅ mantener filtro al cancelar/reset
        // (si no quieres esto, quítalo)
        // $this->id_carga_filtro = $this->id_carga_filtro;
    }

    public function save()
    {
        if (! Carga::exists() || ! Indicador::exists()) {
            session()->flash('error', 'Proceso inválido.');

            return;
        }

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
                'id_carga' => $this->id_carga,
                'id_ind' => $this->id_ind,
                'periodo_det' => $this->periodo_det,
                'ejercicio_det' => $this->ejercicio_det,
                'fecha_registro_det' => $this->fecha_registro_det,
                'fuente_det' => $this->fuente_det,
                'valor_det' => $this->valor_det,
            ]
        );

        $message = $this->selected_id ? 'DetalleCarga Successfully updated.' : 'DetalleCarga Successfully created.';
        $this->reset();
        session()->flash('message', $message);
        $this->dispatch('close-modal', id: 'DataModal');
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
