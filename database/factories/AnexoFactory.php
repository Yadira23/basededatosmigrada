<?php

namespace Database\Factories;

use App\Models\Anexo;
use Illuminate\Database\Eloquent\Factories\Factory;

class AnexoFactory extends Factory
{
    protected $model = Anexo::class;

    public function definition()
    {
        return [
            'nombre_anexo' => fake()->name(),
            'tipo_anexo' => fake()->name(),
            'peso_anexo' => fake()->name(),
            'guia_anexo' => fake()->name(),
            'fin_proposito_anexo' => fake()->name(),
            'fecha_subida_anexo' => fake()->name(),
            'ruta_archivo_anexo' => fake()->name(),
            'id_form' => fake()->name(),
        ];
    }
}
