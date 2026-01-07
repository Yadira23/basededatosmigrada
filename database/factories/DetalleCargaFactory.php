<?php

namespace Database\Factories;

use App\Models\DetalleCarga;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class DetalleCargaFactory extends Factory
{
    protected $model = DetalleCarga::class;

    public function definition()
    {
        return [
			'id_detalle' => fake()->name(),
			'id_carga' => fake()->name(),
			'id_ind' => fake()->name(),
			'id_region' => fake()->name(),
			'id_mun' => fake()->name(),
			'periodo_det' => fake()->name(),
			'ejercicio_det' => fake()->name(),
			'fecha_registro_det' => fake()->name(),
			'fuente_det' => fake()->name(),
			'valor_det' => fake()->name(),
        ];
    }
}
