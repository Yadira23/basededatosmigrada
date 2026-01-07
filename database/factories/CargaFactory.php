<?php

namespace Database\Factories;

use App\Models\Carga;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class CargaFactory extends Factory
{
    protected $model = Carga::class;

    public function definition()
    {
        return [
			'id_carga' => fake()->name(),
			'folioUnico_carga' => fake()->name(),
			'fecha_carga' => fake()->name(),
			'periodo' => fake()->name(),
			'status_env' => fake()->name(),
			'descripcion_env' => fake()->name(),
			'observacion_env' => fake()->name(),
			'id_form' => fake()->name(),
        ];
    }
}
