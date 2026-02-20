<?php

use App\Models\Configuracion;

if (! function_exists('config_val')) {
    function config_val(string $clave, $default = null)
    {
        return Configuracion::where('clave', $clave)->value('valor') ?? $default;
    }
}

if (! function_exists('config_bool')) {
    function config_bool(string $clave, bool $default = false): bool
    {
        $v = config_val($clave, null);
        if ($v === null) return $default;
        return in_array(strtolower((string)$v), ['1', 'true', 'si', 'sí', 'on'], true);
    }
}

if (! function_exists('config_int')) {
    function config_int(string $clave, int $default = 0): int
    {
        $v = config_val($clave, null);
        if ($v === null) return $default;
        return (int) $v;
    }
}
