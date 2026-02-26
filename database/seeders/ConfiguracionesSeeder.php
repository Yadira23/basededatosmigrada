<?php

namespace Database\Seeders;

use App\Models\Configuracion;
use Illuminate\Database\Seeder;

class ConfiguracionesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $items = [
            [
                'clave' => 'CAPTURA_MODO_PRUEBAS',
                'valor' => '0',
                'tipo' => 'bool',
                'descripcion' => 'Si = permite capturar aunque esté fuera de fechas.',
            ],
            [
                'clave' => 'CAPTURA_DIAS_GRACIA',
                'valor' => '0',
                'tipo' => 'int',
                'descripcion' => 'Días extra después del cierre para permitir capturas.',
            ],

            // Días de apertura por tipo de periodo
            [
                'clave' => 'CAPTURA_DIAS_APERTURA_MENSUAL',
                'valor' => '60',
                'tipo' => 'int',
                'descripcion' => 'Días que permanece abierta la captura para periodos mensuales.',
            ],
            [
                'clave' => 'CAPTURA_DIAS_APERTURA_TRIMESTRAL',
                'valor' => '120',
                'tipo' => 'int',
                'descripcion' => 'Días que permanece abierta la captura para periodos trimestrales.',
            ],
            [
                'clave' => 'CAPTURA_DIAS_APERTURA_SEMESTRAL',
                'valor' => '180',
                'tipo' => 'int',
                'descripcion' => 'Días que permanece abierta la captura para periodos semestrales.',
            ],
            [
                'clave' => 'CAPTURA_DIAS_APERTURA_ANUAL',
                'valor' => '365',
                'tipo' => 'int',
                'descripcion' => 'Días que permanece abierta la captura para periodos anuales.',
            ],
        ];

        foreach ($items as $it) {
            Configuracion::updateOrCreate(
                ['clave' => $it['clave']],
                $it
            );
        }
    }
}
