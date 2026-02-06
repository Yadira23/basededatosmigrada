<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Carga;
use App\Models\Formulario;
use App\Models\Indicador;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

class Cargas extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public $selected_id, $keyWord;
    public $id_carga, $folioUnico_carga, $fecha_carga, $periodo, $periodo_det, $ejercicio, $ejercicio_det, $fuente, $status_env, $descripcion_env, $observacion_env, $id_form;
    public $indicadores = [];
    public $selectedIndicador;

    public function mount()
    {
        $this->fecha_carga = now()->format('Y-m-d'); // fecha de hoy
        $this->status_env = 'ENVIADO'; // estado inicial
        $this->generateFolio(); // folio único automático

        // Inicializar los campos de detalle si quieres un valor por defecto
        $this->periodo_det = '';
        $this->ejercicio_det = '';

        // 🔹 Inicializar indicadores según dependencia y formulario si ya existe id_form
        if ($this->id_form) {
            $this->loadIndicadores($this->id_form);
        }
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

        return Carga::query()
            ->with([
                'formulario',
                'primerDetalle',
                'primerDetalle.indicador'
            ])
            ->withCount('detallecargas')
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
        return view('livewire.carga.view', [
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
        // 1️⃣ Validación solo de los campos que existen en 'cargas'
        $this->validate([
            'periodo' => 'required',
            'descripcion_env' => 'required',
            'id_form' => 'required',
            'ejercicio' => 'required',
            'fuente' => 'required',
        ]);

        // 2️⃣ Crear o actualizar la carga
        $carga = Carga::updateOrCreate(
            ['id_carga' => $this->selected_id],
            [
                'folioUnico_carga' => $this->folioUnico_carga,
                'fecha_carga' => $this->fecha_carga,
                'periodo' => $this->periodo,
                'ejercicio' => $this->ejercicio,
                'fuente' => $this->fuente,
                'status_env' => $this->status_env,
                'descripcion_env' => $this->descripcion_env,
                'observacion_env' => $this->observacion_env,
                'id_form' => $this->id_form,
            ]
        );

        // 4️⃣ Mensaje de éxito
        session()->flash(
            'message',
            $this->selected_id
                ? "Carga actualizada correctamente"
                : "Carga creada correctamente con {$carga->detallecargas()->count()} detalles"
        );

        // 5️⃣ Reset de variables y regenerar folio
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

        // Normaliza (mayúsculas + sin espacios raros)
        $estadoActual = mb_strtoupper(trim((string)($carga->status_env ?? '')));
        $estadoActual = str_replace('REVISIÓN', 'REVISION', $estadoActual);

        $nuevo = mb_strtoupper(trim((string)$nuevoStatus));
        $nuevo = str_replace('REVISIÓN', 'REVISION', $nuevo);

        // ✅ Permitir "Tomar revisión" desde ENVIADO o REENVIADO
        if ($nuevo === 'EN REVISION' && !in_array($estadoActual, ['ENVIADO', 'REENVIADO'])) {
            session()->flash('message', "Solo puedes pasar a EN REVISION si está en ENVIADO o REENVIADO.");
            return;
        }

        $carga->status_env = $nuevo; // guarda normalizado
        $carga->save();

        session()->flash('message', "Estado cambiado a {$nuevo}.");
    }

    public function saveObservation()
    {
        $this->validate([
            'observacion_env' => 'required|min:3'
        ]);

        $carga = Carga::findOrFail($this->selected_id);

        $carga->update([
            'observacion_env' => $this->observacion_env,
            'status_env' => 'OBSERVADO', // ✅ clave: corrección
        ]);

        session()->flash(
            'message',
            "Carga {$carga->folioUnico_carga} marcada como OBSERVADO."
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

    // Método para cargar indicadores según el formulario seleccionado
    public function loadIndicadores($id_form)
    {
        $usuario = Auth::user();

        $formulario = Formulario::with('indicador')
            ->where('id_form', $id_form)
            ->where('id_depen', $usuario->id_depen)
            ->first();

        $this->indicadores = $formulario && $formulario->indicador ? [$formulario->indicador] : [];
    }

    // 🔹 Nuevo método: se ejecuta automáticamente cuando cambia $id_form
    public function updatedIdForm($value)
    {
        $this->loadIndicadores($value);
    }

    public function aprobar($id_carga)
    {
        $carga = \App\Models\Carga::findOrFail($id_carga);

        $estado = strtoupper(trim((string)$carga->status_env));

        // ✅ permite aprobar si está EN REVISION o REENVIADO
        if (!in_array($estado, ['EN REVISION', 'REENVIADO'])) {
            session()->flash('error', 'Solo puedes aprobar cargas en revisión.');
            return;
        }

        $carga->status_env = 'APROBADO';
        $carga->observacion_env = null; // limpia observación
        $carga->save();

        session()->flash('success', 'Carga aprobada correctamente.');
    }

    public function observar($id_carga)
    {
        $carga = \App\Models\Carga::findOrFail($id_carga);

        $estado = strtoupper(trim((string)$carga->status_env));

        // ✅ permite observar si está EN REVISION o REENVIADO
        if (!in_array($estado, ['EN REVISION', 'REENVIADO'])) {
            session()->flash('error', 'Solo puedes observar cargas en revisión.');
            return;
        }

        $carga->status_env = 'OBSERVADO';

        $carga->save();

        session()->flash('success', 'Carga marcada como observada.');
    }
}
