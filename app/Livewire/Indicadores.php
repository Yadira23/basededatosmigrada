<?php

namespace App\Livewire;

use App\Models\Formulario;
use App\Models\Indicador;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithPagination;

class Indicadores extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    protected $casts = [
        'config_campos' => 'array',
    ];

    public $selected_id;

    public $keyWord;

    public $tiene_formula = false;

    public $nombre_ind;

    public $definicion_ind;

    public $formula_ind;

    public $tendencia_ind;

    public $restriccion_ind;

    public $formato_ind;

    public $unidadmedida_ind;

    public $meta_ind;

    public $requerido_ind;

    public $status_ind;

    public $periodo_ind;

    public $etiquetas_ind;

    public $fuenteverificacion_ind;

    /* ===============================
       LISTADO + BÚSQUEDA
    ================================*/
    #[Computed]
    public function indicadores()
    {
        $keyWord = '%' . $this->keyWord . '%';

        return Indicador::withCount('metas')
            ->where(function ($query) use ($keyWord) {
                $query
                    ->orWhere('nombre_ind', 'LIKE', $keyWord)
                    ->orWhere('definicion_ind', 'LIKE', $keyWord)
                    ->orWhere('formula_ind', 'LIKE', $keyWord)
                    ->orWhere('tendencia_ind', 'LIKE', $keyWord)
                    ->orWhere('periodo_ind', 'LIKE', $keyWord)
                    ->orWhere('etiquetas_ind', 'LIKE', $keyWord);
            })
            ->latest()
            ->paginate(10);
    }

    public function render()
    {
        return view('livewire.indicadores.view', [
            'indicadores' => $this->indicadores,
        ]);
    }

    /* ===============================
       GUARDAR / ACTUALIZAR
    ================================*/
    public function save()
    {

        $this->requerido_ind = $this->requerido_ind ? 1 : 0;

        $antes = null;
        if ($this->selected_id) {
            $antes = Indicador::where('id_ind', $this->selected_id)->first([
                'periodo_ind',
                'formato_ind',
                'unidadmedida_ind',
                'requerido_ind',
                'meta_ind',
            ]);
        }

        $periodoAntes = $antes?->periodo_ind;

        // VALIDACIÓN BASE
        $rules = [
            'nombre_ind' => 'required|string|min:5',
            'definicion_ind' => 'required|string|min:15',
            'formato_ind' => 'required|in:porcentaje,cantidad,promedio',
            'tendencia_ind' => 'required|in:ascendente,descendente,estable',
            'requerido_ind' => 'nullable|boolean',
            'status_ind' => 'required|in:0,1',
            'periodo_ind' => 'required',
            'fuenteverificacion_ind' => 'required|string',
            'unidadmedida_ind' => 'nullable|string|max:50',
        ];

        if ($this->tiene_formula) {
            $rules['formula_ind'] = 'required|string|min:3';
        } else {
            $this->formula_ind = null;
        }

        // META SOLO SI ES REQUERIDO
        if ($this->requerido_ind) {
            $rules['meta_ind'] = match ($this->formato_ind) {
                'porcentaje' => 'required|numeric|min:0|max:100',
                'cantidad' => 'required|numeric|min:0',
                'promedio' => 'required|numeric|min:0',
                default => 'nullable',
            };
        } else {
            $this->meta_ind = null;
        }

        // VALIDAR
        $this->validate($rules);

        // ✅ BLOQUEO: no permitir cambio de periodo si ya hay capturas relacionadas
        if ($this->selected_id && $periodoAntes !== null && $periodoAntes !== $this->periodo_ind) {

            // Detectar si existe al menos una carga para cualquier formulario de este indicador
            $tieneCapturas = DB::table('cargas')
                ->join('formularios', 'formularios.id_form', '=', 'cargas.id_form')
                ->where('formularios.id_ind', $this->selected_id)
                ->exists();

            if ($tieneCapturas) {
                $this->addError('periodo_ind', 'No puedes cambiar el periodo: este indicador ya tiene capturas registradas. Para cambiar la periodicidad, crea un nuevo indicador (o versiona).');

                return;
            }
        }

        // ✅ BLOQUEO: no permitir cambios críticos si ya hay capturas
        if ($this->selected_id) {

            $tieneCapturas = DB::table('cargas')
                ->join('formularios', 'formularios.id_form', '=', 'cargas.id_form')
                ->where('formularios.id_ind', $this->selected_id)
                ->exists();

            if ($tieneCapturas && $antes) {

                $cambioFormato = $antes->formato_ind !== $this->formato_ind;
                $cambioUnidad = (string) $antes->unidadmedida_ind !== (string) $this->unidadmedida_ind;
                $cambioRequerido = (int) $antes->requerido_ind !== (int) $this->requerido_ind;

                // meta puede ser null; comparamos como string para evitar líos
                $metaAntes = $antes->meta_ind === null ? null : (string) $antes->meta_ind;
                $metaNueva = $this->meta_ind === null ? null : (string) $this->meta_ind;
                $cambioMeta = $metaAntes !== $metaNueva;

                if ($cambioFormato) {
                    $this->addError('formato_ind', 'No puedes cambiar el formato porque este indicador ya tiene capturas registradas.');

                    return;
                }

                if ($cambioUnidad) {
                    $this->addError('unidadmedida_ind', 'No puedes cambiar la unidad de medida porque este indicador ya tiene capturas registradas.');

                    return;
                }

                if ($cambioRequerido) {
                    $this->addError('requerido_ind', 'No puedes cambiar si es requerido/opcional porque este indicador ya tiene capturas registradas.');

                    return;
                }

                if ($cambioMeta) {
                    $this->addError('meta_ind', 'No puedes cambiar la meta porque este indicador ya tiene capturas registradas.');

                    return;
                }
            }
        }

        // NORMALIZAR BOOLEANOS
        $this->requerido_ind = $this->requerido_ind ? 1 : 0;
        $this->status_ind = (int) $this->status_ind;

        // 🔥 UNIDAD DE MEDIDA (OBLIGATORIA)
        $this->unidadmedida_ind = trim((string) $this->unidadmedida_ind);

        if ($this->unidadmedida_ind === '') {
            $this->unidadmedida_ind = match ($this->formato_ind) {
                'porcentaje' => '%',
                'cantidad' => 'unidades',
                'promedio' => 'indice',
                default => 'unidades',
            };
        }

        // GUARDAR
        Indicador::updateOrCreate(
            ['id_ind' => $this->selected_id],
            [
                'nombre_ind' => $this->nombre_ind,
                'definicion_ind' => $this->definicion_ind,
                'formula_ind' => $this->formula_ind,
                'tendencia_ind' => $this->tendencia_ind,
                'restriccion_ind' => $this->restriccion_ind,
                'formato_ind' => $this->formato_ind,
                'unidadmedida_ind' => $this->unidadmedida_ind,
                'meta_ind' => $this->meta_ind,
                'requerido_ind' => $this->requerido_ind,
                'status_ind' => $this->status_ind,
                'periodo_ind' => $this->periodo_ind,
                'etiquetas_ind' => $this->etiquetas_ind,
                'fuenteverificacion_ind' => $this->fuenteverificacion_ind,
            ]
        );

        // ✅ Sincronizar periodo en formularios si el indicador ya existía y cambió el periodo
        if ($this->selected_id && $periodoAntes !== null && $periodoAntes !== $this->periodo_ind) {
            DB::table('formularios')
                ->where('id_ind', $this->selected_id)
                ->update(['periodo_form' => $this->periodo_ind]);
        }

        session()->flash(
            'message',
            $this->selected_id
                ? 'Indicador actualizado correctamente'
                : 'Indicador creado correctamente'
        );

        $this->reset();
        $this->dispatch('close-modal', id: 'DataModal');
    }

    /* ===============================
       LIMPIAR FORM
    ================================*/
    public function cancel()
    {
        $this->reset([
            'selected_id',
            'nombre_ind',
            'definicion_ind',
            'formula_ind',
            'tendencia_ind',
            'restriccion_ind',
            'formato_ind',
            'meta_ind',
            'requerido_ind',
            'status_ind',
            'periodo_ind',
            'etiquetas_ind',
            'fuenteverificacion_ind',
            'tiene_formula',
            'unidadmedida_ind',
            'keyWord',
        ]);
    }

    /* ===============================
       EDITAR
    ================================*/
    public function edit($id)
    {
        $ind = Indicador::findOrFail($id);

        $this->selected_id = $ind->id_ind;
        $this->nombre_ind = $ind->nombre_ind;
        $this->definicion_ind = $ind->definicion_ind;
        $this->formula_ind = $ind->formula_ind;
        $this->tendencia_ind = $ind->tendencia_ind;
        $this->restriccion_ind = $ind->restriccion_ind;
        $this->formato_ind = $ind->formato_ind;
        $this->unidadmedida_ind = $ind->unidadmedida_ind;
        $this->meta_ind = $ind->meta_ind;
        $this->requerido_ind = $ind->requerido_ind;
        $this->status_ind = $ind->status_ind;
        $this->periodo_ind = $ind->periodo_ind;
        $this->etiquetas_ind = $ind->etiquetas_ind;
        $this->fuenteverificacion_ind = $ind->fuenteverificacion_ind;
        $this->tiene_formula = ! is_null($ind->formula_ind);
    }

    /* ===============================
       ELIMINAR
    ================================*/
    public function destroy($id)
    {
        // ✅ Bloquear eliminación si ya hay capturas relacionadas
        $tieneCapturas = DB::table('cargas')
            ->join('formularios', 'formularios.id_form', '=', 'cargas.id_form')
            ->where('formularios.id_ind', $id)
            ->exists();

        if ($tieneCapturas) {
            session()->flash('message', 'No puedes eliminar este indicador porque ya tiene capturas registradas.');

            return;
        }

        Indicador::where('id_ind', $id)->delete();

        session()->flash('message', 'Indicador eliminado correctamente.');
    }
}
