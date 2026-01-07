<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RolSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        DB::table('roles')->insert([
            ['nombre_rol' => 'Administrador', 'descripcion_rol' => 'Control total del sistema'],
            ['nombre_rol' => 'Capturista', 'descripcion_rol' => 'Captura de información']
        ]);
    }
    
}
