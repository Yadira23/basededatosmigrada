<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Usuario;
use Livewire\Attributes\Computed;
use App\Models\Dependencia;
use App\Models\Role;
use Illuminate\Support\Facades\Hash;

class Usuarios extends Component
{
    use WithPagination;
    
    public $dependenciasDisponibles;

    public function mount()
{
    // bloqueo por dependencias (ya lo tienes)
    if (Dependencia::count() === 0) {
        session()->flash('error', 'Debes registrar al menos una Dependencia.');
        return redirect()->route('dependencias');
    }

    $this->dependenciasDisponibles = collect();
}

    protected $paginationTheme = 'bootstrap';
    public $selected_id, $keyWord, $usuario_usr, $nombre_usr, $apellido_paterno, $apellido_materno, $email_usr, $password, $id_depen, $id_rol, $estado_usr, $telefono_usr;

    #[Computed]
public function adminExiste()
{
    return Usuario::where('id_rol', 1)->exists();
}


    #[Computed]
    public function filteredUsuarios()
    {
        $keyWord = '%' . $this->keyWord . '%';
        return Usuario::latest()
            ->where(function ($query) use ($keyWord) {
                $query
                    ->orWhere('usuario_usr', 'LIKE', $keyWord)
                    ->orWhere('nombre_usr', 'LIKE', $keyWord)
                    ->orWhere('apellido_paterno', 'LIKE', $keyWord)
                    ->orWhere('apellido_materno', 'LIKE', $keyWord)
                    ->orWhere('email_usr', 'LIKE', $keyWord)
                    ->orWhere('id_depen', 'LIKE', $keyWord)
                    ->orWhere('id_rol', 'LIKE', $keyWord)
                    ->orWhere('estado_usr', 'LIKE', $keyWord)
                    ->orWhere('telefono_usr', 'LIKE', $keyWord);
            })
            ->paginate(10);
    }

    public function render()
    {
        return view('livewire.usuarios.view', [
            'usuarios' => $this->filteredUsuarios,
            'dependencias' => Dependencia::all(),
            'roles'        => Role::all(),
        ]);
    }

    public function cancel()
    {
        $this->reset();
    }

    public function save()
{
    // 1️⃣ Reglas base
    $rules = [
        'usuario_usr' => 'required|unique:usuarios,usuario_usr,' . $this->selected_id . ',id_usuario',
        'nombre_usr'         => 'required',
        'apellido_paterno'   => 'required',
        'apellido_materno'   => 'nullable',
        'email_usr'          => 'required|email|unique:usuarios,email_usr,' . $this->selected_id . ',id_usuario',
        'password'           => $this->selected_id ? 'nullable|min:6' : 'required|min:6',
        'id_rol'             => 'required',
        'estado_usr'         => 'required',
        'telefono_usr'       => 'nullable',
    ];

    // 2️⃣ Si NO es administrador → dependencia obligatoria
    if ($this->id_rol != 1) {
    $rules['id_depen'] = 'required|unique:usuarios,id_depen,' . $this->selected_id . ',id_usuario';
}

    // 3️⃣ Validar (UNA sola vez)
    $this->validate($rules);

    // 4️⃣ Regla: solo un administrador
    if ($this->id_rol == 1 && Usuario::where('id_rol', 1)->exists() && !$this->selected_id) {
        session()->flash('error', 'Ya existe un Administrador registrado.');
        return;
    }

    // 5️⃣ Administrador no tiene dependencia
    if ($this->id_rol == 1) {
        $this->id_depen = null;
    }
        // Guardar o actualizar
        Usuario::updateOrCreate(
            ['id_usuario' => $this->selected_id],
            [
                'usuario_usr' => $this->usuario_usr,
                'nombre_usr' => $this->nombre_usr,
                'apellido_paterno' => $this->apellido_paterno,
                'apellido_materno' => $this->apellido_materno,
                'email_usr' => $this->email_usr,
                'password' => $this->password ? bcrypt($this->password) : Usuario::find($this->selected_id)->password,
                'id_depen' => $this->id_depen,
                'id_rol' => $this->id_rol,
                'estado_usr' => $this->estado_usr,
                'telefono_usr' => $this->telefono_usr,
            ]
        );

        $this->dispatch('closeModal');
        $this->reset();

        session()->flash(
            'message',
            $this->selected_id
                ? 'Usuario actualizado correctamente'
                : 'Usuario creado correctamente'
        );
    }


    public function edit($id)
    {
        $usuario = Usuario::findOrFail($id);

        $this->selected_id = $usuario->id_usuario;
        $this->usuario_usr = $usuario->usuario_usr;
        $this->nombre_usr = $usuario->nombre_usr;
        $this->apellido_paterno = $usuario->apellido_paterno;
        $this->apellido_materno = $usuario->apellido_materno;
        $this->email_usr = $usuario->email_usr;
        $this->id_depen = $usuario->id_depen;
        $this->id_rol = $usuario->id_rol;
        $this->estado_usr = $usuario->estado_usr;
        $this->telefono_usr = $usuario->telefono_usr;
    }

    public function destroy($id)
    {
        if ($id) {
            Usuario::where('id_usuario', $id)->delete();
        }
    }

    public function getRolesProperty()
    {
        $roles = \App\Models\Role::query();

        if (Usuario::where('id_rol', 1)->exists()) {
            // Si ya hay admin, ocultamos ese rol
            $roles->where('id_rol', '!=', 1);
        }

        return $roles->get();
    }

    public function updatedIdRol($value)
{
    if ($value == 2) {
        $this->dependenciasDisponibles = Dependencia::whereNotIn(
            'id_depen',
            Usuario::whereNotNull('id_depen')->pluck('id_depen')
        )->get();

        if ($this->dependenciasDisponibles->isEmpty()) {
            session()->flash('message', '⚠️ No hay dependencias disponibles para registrar usuarios.');
        }
    } else {
        $this->id_depen = null;
    }
}

}
