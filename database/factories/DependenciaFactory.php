<?php

namespace Database\Factories;

use App\Models\Dependencia;
use App\Models\Sector;
use Illuminate\Database\Eloquent\Factories\Factory;

class DependenciaFactory extends Factory
{
    protected $model = Dependencia::class;

    public function definition(): array
    {
        return [
            'nombre_depen' => fake()->company(),
            'id_sector' => Sector::factory(),
            'email_depen' => fake()->unique()->safeEmail(),
            'extension_depen' => fake()->numerify('####'),
            'telefono_depen' => fake()->numerify('##########'),
            'calle_depen' => fake()->streetName(),
            'numerocalle_depen' => fake()->buildingNumber(),
            'colonia_depen' => fake()->city(),
            'cp_depen' => fake()->postcode(),
        ];
    }
}
