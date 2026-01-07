<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RegionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        DB::table('regiones')->insert([
            [
                'clave_region'  => 'CAN-401',
                'nombre_region' => 'La Cañada',
            ],
            [
                'clave_region'  => 'COS-402',
                'nombre_region' => 'La Costa',
            ],
            [
                'clave_region'  => 'MIX-403',
                'nombre_region' => 'La Mixteca',
            ],
            [
                'clave_region'  => 'PAP-404',
                'nombre_region' => 'Papaloapan',
            ],
            [
                'clave_region'  => 'IST-405',
                'nombre_region' => 'Istmo de Tehuantepec',
            ],
            [
                'clave_region'  => 'SN-406',
                'nombre_region' => 'Sierra Norte',
            ],
            [
                'clave_region'  => 'SS-407',
                'nombre_region' => 'Sierra Sur',
            ],
            [
                'clave_region'  => 'VC-408',
                'nombre_region' => 'Valles Centrales',
            ],
        ]);
    }
}
