<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RegionSeeder extends Seeder
{
    public function run(): void
    {
        $regiones = [
            ['id_region' => 1, 'clave_region' => 'CAN-401', 'nombre_region' => 'Cañada'],
            ['id_region' => 2, 'clave_region' => 'COS-402', 'nombre_region' => 'Costa'],
            ['id_region' => 3, 'clave_region' => 'MIX-403', 'nombre_region' => 'Mixteca'],
            ['id_region' => 4, 'clave_region' => 'PAP-404', 'nombre_region' => 'Papaloapan'],
            ['id_region' => 5, 'clave_region' => 'IST-405', 'nombre_region' => 'Istmo de Tehuantepec'],
            ['id_region' => 6, 'clave_region' => 'SN-406',  'nombre_region' => 'Sierra Norte'],
            ['id_region' => 7, 'clave_region' => 'SS-407',  'nombre_region' => 'Sierra Sur'],
            ['id_region' => 8, 'clave_region' => 'VC-408',  'nombre_region' => 'Valles Centrales'],
        ];

        foreach ($regiones as $region) {
            DB::table('regiones')->updateOrInsert(
                ['id_region' => $region['id_region']],
                [
                    'clave_region' => $region['clave_region'],
                    'nombre_region' => $region['nombre_region'],
                    'updated_at' => now(),
                    'created_at' => now(),
                ]
            );
        }
    }
}
