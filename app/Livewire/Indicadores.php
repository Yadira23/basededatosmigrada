<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Computed;
use App\Models\Indicador;
use App\Models\Formulario;

class Indicadores extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';
    public $selected_id;
    public $keyWord;
    public $tiene_formula = false;
    

    public $nombre_ind, $definicion_ind, $formula_ind, $tendencia_ind, $restriccion_ind, $formato_ind, $unidadmedida_ind, $meta_ind, $requerido_ind, $status_ind, $periodo_ind, $etiquetas_ind, $fuenteverificacion_ind, $id_form; 
    public $formularios;
    public function mount()
{
    $this->formularios = Formulario::orderBy('titulo_form')->get();

    if ($this->formularios->isEmpty()) {
        return redirect('/formularios');
    }
    $this->id_form = null;
}

    /* =============================== 
    LISTADO + BÚSQUEDA 
    ================================*/
    #[Computed] public function indicadores()
    {
        $keyWord = '%' . $this->keyWord . '%';
        return Indicador::where(function ($query) use ($keyWord) {
            $query->orWhere('nombre_ind', 'LIKE', $keyWord)->orWhere('definicion_ind', 'LIKE', $keyWord)->orWhere('formula_ind', 'LIKE', $keyWord)->orWhere('tendencia_ind', 'LIKE', $keyWord)->orWhere('periodo_ind', 'LIKE', $keyWord)->orWhere('etiquetas_ind', 'LIKE', $keyWord);
        })->latest()->paginate(10);
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

    // VALIDACIÓN BASE
    $rules = [
        'nombre_ind' => 'required|string|min:5',
        'definicion_ind' => 'required|string|min:15',
        'formato_ind' => 'required|in:porcentaje,cantidad,promedio',
        'tendencia_ind' => 'required|in:ascendente,descendente',
        'requerido_ind' => 'nullable|boolean',
        'status_ind' => 'required|boolean',
        'periodo_ind' => 'required',
        'fuenteverificacion_ind' => 'required|string',
        'id_form' => 'required|exists:formularios,id_form',
    ];

    if ($this->tiene_formula) {
        $rules['formula_ind'] = 'required|string|min:3';
    } else {
        $this->formula_ind = null;
    }

    // META SOLO SI ES REQUERIDO
    if ($this->requerido_ind) {
        $rules['meta_ind'] = $this->formato_ind === 'porcentaje'
            ? 'required|numeric|min:1|max:100'
            : 'required|numeric|min:1';
    } else {
        $this->meta_ind = null;
    }

    // VALIDAR
    $this->validate($rules);

    // NORMALIZAR BOOLEANOS
    $this->requerido_ind = $this->requerido_ind ? 1 : 0;
    $this->status_ind    = $this->status_ind ? 1 : 0;

    // 🔥 UNIDAD DE MEDIDA (OBLIGATORIA)
    $this->unidadmedida_ind = match ($this->formato_ind) {
        'porcentaje' => '%',
        'cantidad'   => 'unidades',
        'promedio'   => 'puntos',
        default      => throw new \Exception('Formato inválido'),
    };

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
            'id_form' => $this->id_form,
        ]
    );

    session()->flash(
        'message',
        $this->selected_id
            ? 'Indicador actualizado correctamente'
            : 'Indicador creado correctamente'
    );

    $this->reset();
    $this->dispatch('closeModal');
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
    'id_form',
    'tiene_formula',
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
        $this->id_form = $ind->id_form;

        $this->tiene_formula = !is_null($ind->formula_ind);

    } 
    
    
    /* =============================== 
    ELIMINAR 
    ================================*/
    public function destroy($id)
    {
        Indicador::where('id_ind', $id)->delete();
    }

    

}
