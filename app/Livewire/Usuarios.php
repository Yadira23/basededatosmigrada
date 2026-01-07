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

	protected $paginationTheme = 'bootstrap';
    public $selected_id, $keyWord, $usuario_usr, $nombre_usr, $apellido_paterno, $apellido_materno, $email_usr, $password, $id_depen, $id_rol, $estado_usr, $telefono_usr;

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
    // Validaciones
    $this->validate([
        'usuario_usr' => 'required',
        'nombre_usr' => 'required',
        'apellido_paterno' => 'required',
        'apellido_materno' => 'nullable',
        'email_usr' => 'required|email',
        'password' => $this->selected_id ? 'nullable|min:6' : 'required|min:6', // obligatorio al crear
        'id_depen' => 'required',
        'id_rol' => 'required',
        'estado_usr' => 'required',
        'telefono_usr' => 'nullable',
    ]);

    // Determinar password a guardar
    if ($this->selected_id) {
        // Actualizando: solo hash si se ingresó uno nuevo
        $passwordToSave = $this->password 
            ? Hash::make($this->password) 
            : Usuario::find($this->selected_id)->password;
    } else {
        // Creando: obligatorio, hash seguro
        $passwordToSave = Hash::make($this->password);
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
            'password' => $passwordToSave,
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
}