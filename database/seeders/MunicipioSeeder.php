<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MunicipioSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        DB::table('municipios')->insert([

            // Región de la Cañada (id_region = 1)
        ['clave_municipio' => 'CAN-001', 'nombre_municipio' => 'Cuicatlán', 'id_region' => 1],
        ['clave_municipio' => 'CAN-002', 'nombre_municipio' => 'Teotitlán de Flores Magón', 'id_region' => 1],
        ['clave_municipio' => 'CAN-003', 'nombre_municipio' => 'San Juan Bautista Cuicatlán', 'id_region' => 1],

        // Región de la Costa (id_region = 2)
        ['clave_municipio' => 'COS-001', 'nombre_municipio' => 'Puerto Escondido', 'id_region' => 2],
        ['clave_municipio' => 'COS-002', 'nombre_municipio' => 'Santa María Huatulco', 'id_region' => 2],
        ['clave_municipio' => 'COS-003', 'nombre_municipio' => 'San Pedro Pochutla', 'id_region' => 2],
        ['clave_municipio' => 'COS-004', 'nombre_municipio' => 'Pinotepa Nacional', 'id_region' => 2],

        // Región de la Mixteca (id_region = 3)
        ['clave_municipio' => 'MIX-001', 'nombre_municipio' => 'Huajuapan de León', 'id_region' => 3],
        ['clave_municipio' => 'MIX-002', 'nombre_municipio' => 'Tlaxiaco', 'id_region' => 3],
        ['clave_municipio' => 'MIX-003', 'nombre_municipio' => 'Putla Villa de Guerrero', 'id_region' => 3],
        ['clave_municipio' => 'MIX-004', 'nombre_municipio' => 'Juxtlahuaca', 'id_region' => 3],

        // Región del Papaloapan (id_region = 4)
        ['clave_municipio' => 'PAP-001', 'nombre_municipio' => 'Tuxtepec', 'id_region' => 4],
        ['clave_municipio' => 'PAP-002', 'nombre_municipio' => 'San Juan Bautista Tuxtepec', 'id_region' => 4],
        ['clave_municipio' => 'PAP-003', 'nombre_municipio' => 'Loma Bonita', 'id_region' => 4],

        // Región del Istmo (id_region = 5)
        ['clave_municipio' => 'IST-001', 'nombre_municipio' => 'Juchitán de Zaragoza', 'id_region' => 5],
        ['clave_municipio' => 'IST-002', 'nombre_municipio' => 'Salina Cruz', 'id_region' => 5],
        ['clave_municipio' => 'IST-003', 'nombre_municipio' => 'Tehuantepec', 'id_region' => 5],
        ['clave_municipio' => 'IST-004', 'nombre_municipio' => 'Matías Romero', 'id_region' => 5],

        // Región Sierra Norte (id_region = 6)
        ['clave_municipio' => 'SN-001', 'nombre_municipio' => 'Ixtlán de Juárez', 'id_region' => 6],
        ['clave_municipio' => 'SN-002', 'nombre_municipio' => 'Guelatao de Juárez', 'id_region' => 6],
        ['clave_municipio' => 'SN-003', 'nombre_municipio' => 'Capulálpam de Méndez', 'id_region' => 6],
        ['clave_municipio' => 'SN-004', 'nombre_municipio' => 'San Pablo Villa de Mitla', 'id_region' => 6],

        // Región Sierra Sur (id_region = 7)
        ['clave_municipio' => 'SS-001', 'nombre_municipio' => 'Miahuatlán de Porfirio Díaz', 'id_region' => 7],
        ['clave_municipio' => 'SS-002', 'nombre_municipio' => 'Sola de Vega', 'id_region' => 7],
        ['clave_municipio' => 'SS-003', 'nombre_municipio' => 'San Sebastián Coatlán', 'id_region' => 7],
        ['clave_municipio' => 'SS-004', 'nombre_municipio' => 'San Mateo Río Hondo', 'id_region' => 7],

        // Región Valles Centrales (id_region = 8)
        ['clave_municipio' => 'VC-001', 'nombre_municipio' => 'Oaxaca de Juárez', 'id_region' => 8],
        ['clave_municipio' => 'VC-002', 'nombre_municipio' => 'Santa María del Tule', 'id_region' => 8],
        ['clave_municipio' => 'VC-003', 'nombre_municipio' => 'San Bartolo Coyotepec', 'id_region' => 8],
        ['clave_municipio' => 'VC-004', 'nombre_municipio' => 'San Martín Tilcajete', 'id_region' => 8],
        ['clave_municipio' => 'VC-005', 'nombre_municipio' => 'Teotitlán del Valle', 'id_region' => 8],
        ['clave_municipio' => 'VC-006', 'nombre_municipio' => 'Mitla', 'id_region' => 8],

        ]);
    }
}
