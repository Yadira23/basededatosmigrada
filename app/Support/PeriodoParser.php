<?php

namespace App\Support;

class PeriodoParser
{
    // Convierte texto tipo "Enero 2026" a "2026-01"
    public static function mensualTextoAValor(string $texto): ?string
    {
        $texto = trim(mb_strtolower($texto));

        $map = [
            'enero' => '01', 'febrero' => '02', 'marzo' => '03', 'abril' => '04',
            'mayo' => '05', 'junio' => '06', 'julio' => '07', 'agosto' => '08',
            'septiembre' => '09', 'setiembre' => '09', 'octubre' => '10',
            'noviembre' => '11', 'diciembre' => '12',
        ];

        // Ej: "enero 2026"
        if (preg_match('/^([a-záéíóúñ]+)\s+(\d{4})$/u', $texto, $m)) {
            $mes = $m[1];
            $anio = $m[2];
            if (!isset($map[$mes])) return null;
            return "{$anio}-{$map[$mes]}";
        }

        return null;
    }
}
