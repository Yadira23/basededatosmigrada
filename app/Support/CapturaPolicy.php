<?php

namespace App\Support;

use Carbon\Carbon;

class CapturaPolicy
{
    /**
     * $periodoTipo: mensual|trimestral|semestral|anual
     * $periodoValor: ejemplo "2026-02" (mensual) | "2026-T1" (trimestral) | "2026-S1" (semestral) | "2026" (anual)
     */
    public static function permite(string $periodoTipo, string $periodoValor, ?Carbon $hoy = null): array
    {
        $hoy = $hoy ?: now();

        // 1) Modo pruebas (override global)
        if (config_bool('CAPTURA_MODO_PRUEBAS', false)) {
            return [true, 'Modo pruebas habilitado por el administrador.'];
        }

        // 2) Calcular inicio del periodo
        $inicioPeriodo = self::inicioDelPeriodo($periodoTipo, $periodoValor);
        if (!$inicioPeriodo) {
            return [false, 'No se pudo determinar el inicio del periodo.'];
        }

        // 3) Leer días de apertura según tipo
        $diasApertura = match (strtolower($periodoTipo)) {
            'mensual'    => config_int('CAPTURA_DIAS_APERTURA_MENSUAL', 60),
            'trimestral' => config_int('CAPTURA_DIAS_APERTURA_TRIMESTRAL', 120),
            'semestral'  => config_int('CAPTURA_DIAS_APERTURA_SEMESTRAL', 180),
            'anual'      => config_int('CAPTURA_DIAS_APERTURA_ANUAL', 365),
            default      => config_int('CAPTURA_DIAS_APERTURA_MENSUAL', 60),
        };

        $diasGracia = config_int('CAPTURA_DIAS_GRACIA', 0);

        $inicioVentana = $inicioPeriodo->copy()->startOfDay();
        $finVentana = $inicioPeriodo->copy()->addDays($diasApertura + $diasGracia)->endOfDay();

        $permitido = $hoy->between($inicioVentana, $finVentana);

        if ($permitido) {
            return [true, "Captura permitida del {$inicioVentana->format('d/m/Y')} al {$finVentana->format('d/m/Y')}."];
        }

        return [false, "Captura bloqueada. Ventana: {$inicioVentana->format('d/m/Y')} a {$finVentana->format('d/m/Y')}."];
    }

    private static function inicioDelPeriodo(string $tipo, string $valor): ?Carbon
    {
        $tipo = strtolower($tipo);

        try {
            if ($tipo === 'mensual') {
                // valor: YYYY-MM
                return Carbon::createFromFormat('Y-m', $valor)->startOfMonth();
            }

            if ($tipo === 'trimestral') {
                // valor: YYYY-T1..T4
                if (!preg_match('/^(\d{4})-T([1-4])$/', $valor, $m)) return null;
                $year = (int)$m[1];
                $t = (int)$m[2];
                $month = [1 => 1, 2 => 4, 3 => 7, 4 => 10][$t];
                return Carbon::create($year, $month, 1)->startOfMonth();
            }

            if ($tipo === 'semestral') {
                // valor: YYYY-S1 o YYYY-S2
                if (!preg_match('/^(\d{4})-S([1-2])$/', $valor, $m)) return null;
                $year = (int)$m[1];
                $s = (int)$m[2];
                $month = ($s === 1) ? 1 : 7;
                return Carbon::create($year, $month, 1)->startOfMonth();
            }

            if ($tipo === 'anual') {
                // valor: YYYY
                if (!preg_match('/^\d{4}$/', $valor)) return null;
                return Carbon::create((int)$valor, 1, 1)->startOfYear();
            }
        } catch (\Throwable $e) {
            return null;
        }

        return null;
    }
}
