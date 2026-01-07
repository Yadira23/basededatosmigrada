<?php

namespace Database\Factories;

use App\Models\Formulario;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class FormularioFactory extends Factory
{
    protected $model = Formulario::class;

    public function definition()
    {
        return [
			'id_form' => fake()->name(),
			'titulo_form' => fake()->name(),
			'fecha_creacion_form' => fake()->name(),
			'descripcion_form' => fake()->name(),
			'boton_accion_form' => fake()->name(),
			'secciones_form' => fake()->name(),
			'periodo_form' => fake()->name(),
			'id_depen' => fake()->name(),
			'id_usr' => fake()->name(),
        ];
    }
}
