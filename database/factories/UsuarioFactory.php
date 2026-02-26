<?php

namespace Database\Factories;

use App\Models\Dependencia;
use App\Models\Usuario;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

class UsuarioFactory extends Factory
{
    protected $model = Usuario::class;

    public function definition(): array
    {
        return [
            'usuario_usr' => $this->faker->userName(),
            'nombre_usr' => $this->faker->firstName(),
            'apellido_paterno' => $this->faker->lastName(),
            'apellido_materno' => $this->faker->lastName(),
            'email_usr' => $this->faker->unique()->safeEmail(),
            'password' => Hash::make('password'),
            'telefono_usr' => $this->faker->numerify('##########'),
            'estado_usr' => 1,
            'id_depen' => Dependencia::factory(),
        ];
    }
}
