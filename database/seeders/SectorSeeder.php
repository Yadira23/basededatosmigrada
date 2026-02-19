<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Sector;

class SectorSeeder extends Seeder
{
    public function run(): void
    {
        Sector::updateOrCreate(
            ['nombre_sector' => 'Turismo'],
            ['descripcion_sector' => 'Dependencias relacionadas con el Turismo, Hospedaje, Servicios']
        );

        Sector::updateOrCreate(
            ['nombre_sector' => 'Educación'],
            ['descripcion_sector' => 'Dependencias relacionadas con servicios Educativos']
        );
    }
}
