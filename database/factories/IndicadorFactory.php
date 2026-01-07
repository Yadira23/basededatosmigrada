<?php

namespace Database\Factories;

use App\Models\Indicador;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class IndicadorFactory extends Factory
{
    protected $model = Indicador::class;

    public function definition()
    {
        return [
			'id_ind' => fake()->name(),
			'nombre_ind' => fake()->name(),
			'definicion_ind' => fake()->name(),
			'formula_ind' => fake()->name(),
			'tendencia_ind' => fake()->name(),
			'restriccion_ind' => fake()->name(),
			'formato_ind' => fake()->name(),
			'unidadmedida_ind' => fake()->name(),
			'meta_ind' => fake()->name(),
			'requerido_ind' => fake()->name(),
			'status_ind' => fake()->name(),
			'periodo_ind' => fake()->name(),
			'etiquetas_ind' => fake()->name(),
			'fuenteverificacion_ind' => fake()->name(),
			'id_form' => fake()->name(),
			'id_anexo' => fake()->name(),
        ];
    }
}
