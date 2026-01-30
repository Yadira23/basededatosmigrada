<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MunicipioSeeder extends Seeder
{
    public function run(): void
    {
        $path = database_path('seeders/data/municipios.csv');

        if (!file_exists($path)) {
            throw new \RuntimeException("No se encontró el archivo: {$path}");
        }

        DB::transaction(function () use ($path) {

            $handle = fopen($path, 'r');
            if ($handle === false) {
                throw new \RuntimeException("No se pudo abrir el archivo: {$path}");
            }

            // Detectar delimitador leyendo la primera línea cruda
            $firstLine = fgets($handle);
            if ($firstLine === false) {
                fclose($handle);
                throw new \RuntimeException("El CSV está vacío: {$path}");
            }

            // Quitar BOM si existe
            $firstLine = preg_replace('/^\xEF\xBB\xBF/', '', $firstLine);

            // Detectar delimitador (coma, punto y coma, tab)
            $delims = ["," , ";" , "\t"];
            $bestDelim = ",";
            $bestCount = -1;
            foreach ($delims as $d) {
                $c = substr_count($firstLine, $d);
                if ($c > $bestCount) {
                    $bestCount = $c;
                    $bestDelim = $d;
                }
            }

            // Regresar el puntero al inicio para leer con fgetcsv
            rewind($handle);

            // Leer encabezado con el delimitador detectado
            $header = fgetcsv($handle, 0, $bestDelim);
            if (!$header) {
                fclose($handle);
                throw new \RuntimeException("No se pudo leer el encabezado del CSV: {$path}");
            }

            // Normalizar encabezados: minúsculas, trim, quitar BOM raro
            $norm = function ($h) {
                $h = preg_replace('/^\xEF\xBB\xBF/', '', (string)$h);
                $h = trim(mb_strtolower($h));
                // reemplazos comunes
                $h = str_replace([' ', '-'], ['_', '_'], $h);
                return $h;
            };

            $headerNorm = array_map($norm, $header);
            $map = array_flip($headerNorm);

            // Aceptar nombres alternativos (por si tu CSV viene diferente)
            $aliases = [
                'cve_municipio'     => ['cve_municipio', 'clave_municipio', 'cve_mun', 'cve_mpio', 'cve'],
                'cve_region'        => ['cve_region', 'id_region', 'region', 'clave_region'],
                'nombre_municipio'  => ['nombre_municipio', 'municipio', 'nom_municipio', 'nombre'],
            ];

            $idx = [];
            foreach ($aliases as $target => $opts) {
                $found = null;
                foreach ($opts as $opt) {
                    $opt = $norm($opt);
                    if (isset($map[$opt])) {
                        $found = $map[$opt];
                        break;
                    }
                }
                if ($found === null) {
                    fclose($handle);
                    throw new \RuntimeException(
                        "Falta la columna '{$target}' en el CSV. Encabezados detectados: " . implode(', ', $headerNorm)
                    );
                }
                $idx[$target] = $found;
            }

            while (($row = fgetcsv($handle, 0, $bestDelim)) !== false) {

                // Salta filas vacías
                if (count(array_filter($row, fn($v) => trim((string)$v) !== '')) === 0) {
                    continue;
                }

                $cveMunicipio = trim((string)($row[$idx['cve_municipio']] ?? ''));
                $cveRegion    = trim((string)($row[$idx['cve_region']] ?? ''));
                $nombre       = trim((string)($row[$idx['nombre_municipio']] ?? ''));

                if ($cveMunicipio === '' || $cveRegion === '' || $nombre === '') {
                    continue;
                }

                // Normaliza municipio a 3 dígitos
                $claveMunicipio = str_pad($cveMunicipio, 3, '0', STR_PAD_LEFT);

                // Si cve_region viene como "6" está perfecto.
                // Si viene como texto raro, intenta sacar número.
                $idRegion = (int) preg_replace('/\D+/', '', $cveRegion);

                if ($idRegion < 1 || $idRegion > 8) {
                    // Si esto pasa, tu CSV no trae 1..8 como región.
                    // Mejor salta para no meter datos incorrectos.
                    continue;
                }

                DB::table('municipios')->updateOrInsert(
                    ['clave_municipio' => $claveMunicipio],
                    [
                        'nombre_municipio' => $nombre,
                        'id_region' => $idRegion,
                        'updated_at' => now(),
                        'created_at' => now(),
                    ]
                );
            }

            fclose($handle);
        });
    }
}
