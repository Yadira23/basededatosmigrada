<?php

namespace Database\Factories;

use App\Models\Dependencia;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class DependenciaFactory extends Factory
{
    protected $model = Dependencia::class;

    public function definition()
    {
        return [
			'id_depen' => fake()->name(),
			'nombre_depen' => fake()->name(),
			'id_sector' => fake()->name(),
			'email_depen' => fake()->name(),
			'extension_depen' => fake()->name(),
			'telefono_depen' => fake()->name(),
			'calle_depen' => fake()->name(),
			'numerocalle_depen' => fake()->name(),
			'colonia_depen' => fake()->name(),
			'cp_depen' => fake()->name(),
        ];
    }
}
