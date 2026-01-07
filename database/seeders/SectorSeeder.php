<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SectorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        DB::table('sectores')->insert([
            ['nombre_sector' => 'Turismo','descripcion_sector' => 'Dependencias relacionadas con el Turismo, Hospedaje, Servicios'],
            ['nombre_sector' => 'Educación','descripcion_sector' => 'Dependencias relacionadas con servicios Educativos']
        ]);
    
    }
}
