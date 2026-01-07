<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DependenciaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        DB::table('dependencias')->insert([
            [
                'nombre_depen'        => 'Secretaría de Turismo',
                'id_sector'           => 1, // Turismo
                'email_depen'         => 'contacto@turismo.gob.mx',
                'extension_depen'     => '101',
                'telefono_depen'      => '9517654321',
                'calle_depen'         => 'Calzada Porfirio Díaz',
                'numerocalle_depen'   => '300',
                'colonia_depen'       => 'Reforma',
                'cp_depen'            => '68050',
            ],
            [
                'nombre_depen'        => 'Secretaría de Educación Pública',
                'id_sector'           => 2, // Educación
                'email_depen'         => 'contacto@educacion.gob.mx',
                'extension_depen'     => '202',
                'telefono_depen'      => '9511234567',
                'calle_depen'         => 'Avenida Universidad',
                'numerocalle_depen'   => '100',
                'colonia_depen'       => 'Centro',
                'cp_depen'            => '68000',
            ],
        ]);
    }
}
