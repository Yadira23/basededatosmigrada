<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Carga;
use App\Models\Formulario;
use Illuminate\Support\Str;

class Cargas extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public $selected_id, $keyWord;
    public $id_carga, $folioUnico_carga, $fecha_carga, $periodo, $status_env, $descripcion_env, $observacion_env, $id_form;

    public function mount()
    {
        $this->fecha_carga = now()->format('Y-m-d'); // fecha de hoy
        $this->status_env = 'Enviado'; // estado inicial
        $this->generateFolio(); // folio único automático
    }

    // Genera folio único automáticamente
    public function generateFolio()
    {
        $this->folioUnico_carga = Str::upper(Str::random(6));

        if (Carga::where('folioUnico_carga', $this->folioUnico_carga)->exists()) {
            $this->generateFolio(); // si ya existe, genera otro
        }
    }

    public function openModal()
{
    $this->folioUnico_carga = strtoupper(uniqid('FOLIO-')); // ejemplo folio aleatorio
    $this->fecha_carga = now()->format('Y-m-d');
}

    #[\Livewire\Attributes\Computed]
    public function filteredCargas()
    {
        $keyWord = '%' . $this->keyWord . '%';
        return Carga::with('formulario')
            ->latest()
            ->where(function ($query) use ($keyWord) {
                $query->orWhere('folioUnico_carga', 'LIKE', $keyWord)
                    ->orWhere('fecha_carga', 'LIKE', $keyWord)
                    ->orWhere('periodo', 'LIKE', $keyWord)
                    ->orWhere('status_env', 'LIKE', $keyWord)
                    ->orWhere('descripcion_env', 'LIKE', $keyWord)
                    ->orWhere('observacion_env', 'LIKE', $keyWord);
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
        $this->generateFolio();
        $this->fecha_carga = now()->toDateString();
        $this->status_env = 'Enviado';
    }

    public function save()
    {
        $this->validate([
            'periodo' => 'required',
            'descripcion_env' => 'required',
            'id_form' => 'required',
        ]);

        Carga::updateOrCreate(
            ['id_carga' => $this->selected_id],
            [
                'folioUnico_carga' => $this->folioUnico_carga,
                'fecha_carga' => $this->fecha_carga,
                'periodo' => $this->periodo,
                'status_env' => $this->status_env,
                'descripcion_env' => $this->descripcion_env,
                'observacion_env' => $this->observacion_env,
                'id_form' => $this->id_form,
            ]
        );

        session()->flash(
            'message',
            $this->selected_id
                ? "Carga actualizada correctamente"
                : "Carga creada correctamente para el formulario {$this->id_form}"
        );

        $this->dispatch('closeModal');
        $this->reset();
        $this->mount(); // reinicia folio, fecha y estado
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

    // Cambiar estado dinámicamente desde los botones
    public function changeStatus($id, $nuevoStatus)
    {
        $carga = Carga::findOrFail($id);
        $carga->status_env = $nuevoStatus;
        $carga->save();

        session()->flash(
            'message',
            "El estado de la carga {$carga->folioUnico_carga} se cambió a {$nuevoStatus} para el formulario {$carga->formulario->titulo_form}"
        );
    }

public function saveObservation()
{
    $this->validate([
        'observacion_env' => 'required|min:3'
    ]);

    $carga = Carga::findOrFail($this->selected_id);

    $carga->update([
        'observacion_env' => $this->observacion_env
    ]);

    session()->flash(
        'message',
        "Observación guardada para la carga {$carga->folioUnico_carga}"
    );

    $this->dispatch('closeObservationModal');

    $this->reset(['observacion_env', 'selected_id']);
}

public function openObservation($id)
{
    $carga = Carga::findOrFail($id);

    $this->selected_id = $carga->id_carga;
    $this->observacion_env = $carga->observacion_env;
}

}
