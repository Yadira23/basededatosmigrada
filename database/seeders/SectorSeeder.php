<?php

namespace Database\Seeders;

use App\Models\Sector;
use Illuminate\Database\Seeder;

class SectorSeeder extends Seeder
{
    public function run(): void
    {
        Sector::updateOrCreate(
            ['nombre_sector' => 'Salud'],
            ['descripcion_sector' => 'Dependencias relacionadas con servicios de salud, atención médica y bienestar social']
        );

        Sector::updateOrCreate(
            ['nombre_sector' => 'Educación'],
            ['descripcion_sector' => 'Dependencias relacionadas con servicios educativos, formación académica y capacitación']
        );

        Sector::updateOrCreate(
            ['nombre_sector' => 'Desarrollo Económico'],
            ['descripcion_sector' => 'Dependencias enfocadas en el crecimiento económico, comercio, industria y empleo']
        );

        Sector::updateOrCreate(
            ['nombre_sector' => 'Turismo'],
            ['descripcion_sector' => 'Dependencias relacionadas con turismo, hospedaje, promoción cultural y servicios turísticos']
        );
    }
}
