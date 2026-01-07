<?php

namespace Database\Factories;

use App\Models\Usuario;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class UsuarioFactory extends Factory
{
    protected $model = Usuario::class;

    public function definition()
    {
        return [
			'id_usuario' => fake()->name(),
			'usuario_usr' => fake()->name(),
			'nombre_usr' => fake()->name(),
			'apellido_paterno' => fake()->name(),
			'apellido_materno' => fake()->name(),
			'email_usr' => fake()->name(),
			'id_depen' => fake()->name(),
			'id_rol' => fake()->name(),
			'estado_usr' => fake()->name(),
			'telefono_usr' => fake()->name(),
        ];
    }
}
