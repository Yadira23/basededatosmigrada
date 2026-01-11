<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Formulario;
use Livewire\Attributes\Computed;
use Illuminate\Support\Facades\Auth;
use App\Models\Dependencia;
use App\Models\Usuario;



class Formularios extends Component
{
    use WithPagination;

    public function mount()
    {

        // Inicializa la acción por defecto para formularios nuevos
        if (!$this->selected_id) {
            $this->boton_accion_form = 'Publicar';
            $this->fecha_creacion_form = now()->toDateString();
        }



        // Inicializa el usuario logueado
        $this->id_usr = auth()->user()->id_usuario;
    }

    protected $paginationTheme = 'bootstrap';
    public $id_usr;
    public $selected_id, $keyWord, $id_form, $titulo_form, $fecha_creacion_form, $descripcion_form, $boton_accion_form, $secciones_form, $periodo_form, $id_depen;

    #[Computed]
    public function filteredFormularios()
    {
        $keyWord = '%' . $this->keyWord . '%';

        $query = Formulario::query();

        // Filtrar por palabra clave en varios campos
        if ($this->keyWord) {
            $query->where(function ($q) use ($keyWord) {
                $q->where('titulo_form', 'LIKE', $keyWord)
                    ->orWhere('descripcion_form', 'LIKE', $keyWord)
                    ->orWhere('boton_accion_form', 'LIKE', $keyWord)
                    ->orWhere('secciones_form', 'LIKE', $keyWord)
                    ->orWhere('periodo_form', 'LIKE', $keyWord);
            });
        }

        return $query->latest()->paginate(10);
    }


    public function render()
    {
        return view('livewire.formularios.view', [
            'formularios' => $this->filteredFormularios,
            'dependencias' => Dependencia::all(),
        ]);
    }

    public function cancel()
    {
        $this->reset();
        $this->boton_accion_form = 'Publicar';
    $this->fecha_creacion_form = now()->toDateString();
    }

    public function save()
    {
        $this->boton_accion_form = $this->boton_accion_form ?? 'Publicar';
        $this->validate([
            'titulo_form' => 'required',
            'fecha_creacion_form' => 'required',
            'boton_accion_form' => 'required',
            'secciones_form' => 'required',
            'periodo_form' => 'required',
            'id_depen' => 'required',
        ]);

        // Verificar que la dependencia tenga usuario
        if (!$this->id_usr) {
            $usuario = Usuario::where('id_depen', $this->id_depen)->first();
            if ($usuario) {
                $this->id_usr = $usuario->id_usuario;
            } else {
                session()->flash('error', 'La dependencia seleccionada no tiene usuarios asignados.');
                return; // salir sin guardar
            }
        }

        if (!$this->fecha_creacion_form) {
            $this->fecha_creacion_form = now()->format('Y-m-d');
        }

        if (!$this->selected_id) {
    $this->boton_accion_form = 'Publicar';
}


        Formulario::updateOrCreate(
            ['id_form' => $this->selected_id],
            [
                'titulo_form' => $this->titulo_form,
                'fecha_creacion_form' => $this->fecha_creacion_form,
                'descripcion_form' => $this->descripcion_form,
                'boton_accion_form' => $this->boton_accion_form,
                'secciones_form' => $this->secciones_form,
                'periodo_form' => $this->periodo_form,
                'id_depen' => $this->id_depen,
                'id_usr' => $this->id_usr,

            ]
        );

        $this->dispatch('closeModal');
        $this->resetExcept(['id_usr', 'fecha_creacion_form', 'boton_accion_form']);

        session()->flash(
            'message',
            $this->selected_id
                ? 'Formulario actualizado correctamente'
                : 'Formulario creado correctamente'
        );
    }

    public function edit($id)
    {
        $formulario = Formulario::findOrFail($id);

        $this->selected_id = $formulario->id_form;
        $this->titulo_form = $formulario->titulo_form;
        $this->fecha_creacion_form = $formulario->fecha_creacion_form;
        $this->descripcion_form = $formulario->descripcion_form;
        $this->boton_accion_form = $formulario->boton_accion_form;
        $this->secciones_form = $formulario->secciones_form;
        $this->periodo_form = $formulario->periodo_form;
        $this->id_depen = $formulario->id_depen;

        // Asignar usuario de la dependencia
        $usuario = Usuario::where('id_depen', $this->id_depen)->first();
        $this->id_usr = $usuario ? $usuario->id_usuario : null;
    }

    public function destroy($id)
    {
        if ($id) {
            Formulario::where('id_form', $id)->delete();
        }
    }

    public function updatedIdDepen($value)
    {
        $usuario = Usuario::where('id_depen', $value)->first();

        if ($usuario) {
            $this->id_usr = $usuario->id_usuario;
        } else {
            $this->id_usr = null;
            session()->flash('error', 'La dependencia seleccionada no tiene usuarios asignados.');
        }
    }

    // Acción dinámica del botón
    public function accionFormulario($id)
    {
        $formulario = Formulario::findOrFail($id);

        switch ($formulario->boton_accion_form) {
            case 'Publicar':
                $formulario->boton_accion_form = 'Finalizar';
                session()->flash('message', "Formulario publicado en {$formulario->dependencia->nombre_depen}");
                break;

            case 'Finalizar':
                $formulario->boton_accion_form = 'Ver';
                session()->flash('message', "Formulario finalizado");
                break;

            case 'Ver':
                session()->flash('message', "Formulario en modo solo lectura");
                break;
        }
    }

    public function publicar($id)
{
    $formulario = Formulario::find($id);

    if ($formulario) {
        $formulario->boton_accion_form = 'Ver'; // Al publicar, cambiar a "Ver"
        $formulario->save();

        // Mostrar mensaje con la dependencia
        $dependencia = Dependencia::find($formulario->id_depen);
        session()->flash('message', "Formulario publicado correctamente en {$dependencia->nombre_depen}.");
    }
}

public function botonFinalizarVisible($formulario)
{
    $fechaCreacion = \Carbon\Carbon::parse($formulario->fecha_creacion_form);
    $hoy = \Carbon\Carbon::now();

    switch($formulario->periodo_form) {
        case 'Mensual':
            $fechaFin = $fechaCreacion->copy()->addMonth();
            break;
        case 'Trimestral':
            $fechaFin = $fechaCreacion->copy()->addMonths(3);
            break;
        case 'Semestral':
            $fechaFin = $fechaCreacion->copy()->addMonths(6);
            break;
        case 'Anual':
            $fechaFin = $fechaCreacion->copy()->addYear();
            break;
        default:
            $fechaFin = $fechaCreacion;
    }

    return $hoy->gte($fechaFin); // Devuelve true si ya pasó el periodo
}

public function ver($id)
{
    // Aquí puedes redirigir o abrir modal con detalles del formulario
    $formulario = Formulario::find($id);
    session()->flash('message', "Viendo formulario: {$formulario->titulo_form}");
}

public function finalizar($id)
{
    $formulario = Formulario::find($id);
    $formulario->boton_accion_form = 'Finalizado';
    $formulario->save();

    session()->flash('message', "Formulario {$formulario->titulo_form} finalizado.");
}


}