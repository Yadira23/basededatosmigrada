<?php

namespace App\Livewire\Usuario;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\Region;
use App\Models\Municipio;
use App\Models\Carga;
use App\Models\DetalleCarga;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class FormularioCaptura extends Component
{
    use WithFileUploads;

    public $id_form;
    public $id_ind;

    public $metodo = null;
    public $guardando = false;

    // SIN_AMBITO | REGION | MUNICIPIO
    public $ambito_geo = 'SIN_AMBITO';

    public $regionFiltro = '';
    public $municipiosFiltrados;

    // inputs
    public $region = '';
    public $municipio = '';
    public $valor = '';

    // data
    public $manualData = [];

    // archivo
    public $archivo;
    public $archivoNombre = '';
    public $archivoData = [];
    public $archivoPreview = [];

    // catálogos
    public $regiones;

    public function mount($id_form, $id_ind)
    {
        $this->id_form = $id_form;
        $this->id_ind  = $id_ind;

        $this->metodo = null;
        $this->ambito_geo = 'SIN_AMBITO';

        $this->regiones = Region::orderBy('nombre_region')->get();
        $this->municipiosFiltrados = collect();
    }

    public function seleccionar($metodo)
    {
        $this->metodo = $metodo;

        // si cambia a manual, limpiamos archivo
        if ($metodo !== 'archivo') {
            $this->resetArchivo();
        }
    }

    public function agregarManual()
    {
        if ($this->valor === '' || $this->valor === null) return;

        $valor = (float) $this->valor;

        if ($this->ambito_geo === 'SIN_AMBITO') {
            $this->manualData[] = [
                'ambito_geo' => 'SIN_AMBITO',
                'id_region' => null,
                'id_mun' => null,
                'nombre' => 'GLOBAL',
                'valor' => $valor,
            ];
            $this->valor = '';
            return;
        }

        if ($this->ambito_geo === 'REGION') {
            if (!$this->region) return;

            $yaExiste = collect($this->manualData)->contains(
                fn($row) => ($row['ambito_geo'] ?? '') === 'REGION'
                    && (int)($row['id_region'] ?? 0) === (int)$this->region
            );

            if ($yaExiste) {
                session()->flash('error', 'Esa región ya fue agregada. Edita el registro existente.');
                return;
            }

            $r = Region::where('id_region', $this->region)->first();
            if (!$r) return;

            $this->manualData[] = [
                'ambito_geo' => 'REGION',
                'id_region' => (int)$r->id_region,
                'id_mun' => null,
                'nombre' => $r->nombre_region,
                'valor' => $valor,
            ];

            $this->region = '';
            $this->valor = '';
            return;
        }

        if ($this->ambito_geo === 'MUNICIPIO') {
            if (!$this->municipio) return;

            $yaExiste = collect($this->manualData)->contains(
                fn($row) => ($row['ambito_geo'] ?? '') === 'MUNICIPIO'
                    && (int)($row['id_mun'] ?? 0) === (int)$this->municipio
            );

            if ($yaExiste) {
                session()->flash('error', 'Ese municipio ya fue agregado. Edita el registro existente.');
                return;
            }

            $m = Municipio::where('id_mun', $this->municipio)->first();
            if (!$m) return;

            $this->manualData[] = [
                'ambito_geo' => 'MUNICIPIO',
                'id_region' => (int)$m->id_region,
                'id_mun' => (int)$m->id_mun,
                'nombre' => $m->nombre_municipio,
                'valor' => $valor,
            ];

            $this->municipio = '';
            $this->valor = '';
            return;
        }
    }

    public function eliminarManual($index)
    {
        if (!isset($this->manualData[$index])) return;
        unset($this->manualData[$index]);
        $this->manualData = array_values($this->manualData);
    }

    public function editarManual($index)
    {
        if (!isset($this->manualData[$index])) return;

        $row = $this->manualData[$index];

        $this->ambito_geo = $row['ambito_geo'] ?? 'SIN_AMBITO';
        $this->region = $row['id_region'] ?? '';
        $this->municipio = $row['id_mun'] ?? '';
        $this->valor = $row['valor'] ?? '';

        $this->eliminarManual($index);
    }

    public function updatedArchivo()
    {
        if (!$this->archivo) return;

        $this->validateOnly('archivo');

        $this->archivoNombre = $this->archivo->getClientOriginalName();
        $ext = strtolower($this->archivo->getClientOriginalExtension());

        try {
            if (in_array($ext, ['csv', 'txt'])) {
                $this->archivoData = $this->leerCsvComoFilas($this->archivo->getRealPath());
                $this->archivoPreview = $this->convertirArchivoAFilas($this->archivoData);
                session()->flash('success', 'CSV cargado correctamente.');
                return;
            }

            if (in_array($ext, ['xlsx', 'xls'])) {
                $this->archivoData = $this->leerExcelComoFilas($this->archivo->getRealPath());
                $this->archivoPreview = $this->convertirArchivoAFilas($this->archivoData);
                session()->flash('success', 'Excel cargado correctamente.');
                return;
            }

            session()->flash('error', 'Formato no permitido. Solo CSV / XLSX / XLS.');
            $this->resetArchivo();
        } catch (\Throwable $e) {
            $this->resetArchivo();
            session()->flash('error', 'No se pudo leer el archivo: ' . $e->getMessage());
        }
    }

    public function resetArchivo()
    {
        $this->archivo = null;
        $this->archivoNombre = '';
        $this->archivoData = [];
        $this->archivoPreview = [];
    }

    /**
     * ✅ ENVIAR (GUARDAR) ÚNICO: guarda manual o archivo según $metodo
     */
    public function guardarTodo()
    {
        if ($this->guardando) return;
        $this->guardando = true;

        try {
            // ✅ validación base
            $this->validate([
                'metodo' => 'required|in:manual,archivo',
                'id_form' => 'required|integer',
                'id_ind'  => 'required|integer',
            ]);

            // ✅ validar según método
            if ($this->metodo === 'manual') {
                $this->validate([
                    'manualData' => 'required|array|min:1',
                    'manualData.*.valor' => 'required|numeric',
                ]);
                $filas = $this->manualData;
            } else {
                $this->validate([
                    'archivo' => 'required|file|max:10240|mimes:csv,txt,xlsx,xls',
                ]);

                if (empty($this->archivoPreview)) {
                    throw new \Exception('Sube un archivo y verifica que tenga filas.');
                }

                $filas = $this->archivoPreview;
                if (empty($filas)) {
                    throw new \Exception('El archivo no tiene líneas válidas.');
                }
            }

            // ✅ datos de carga
            $periodo   = now()->format('Y-m');
            $ejercicio = now()->year;
            $fuente    = $this->metodo === 'manual' ? 'Captura Manual' : 'Carga por Archivo';

            DB::transaction(function () use ($filas, $periodo, $ejercicio, $fuente) {

                // ✅ crear carga
                $carga = Carga::create([
                    'folioUnico_carga' => 'CAR-' . now()->timestamp,
                    'fecha_carga' => now(),
                    'periodo' => $periodo,
                    'ejercicio' => $ejercicio,
                    'fuente' => $fuente,
                    'status_env' => 'ENVIADO',
                    'descripcion_env' => $this->metodo === 'manual'
                        ? 'Captura manual (según indicador)'
                        : 'Carga desde archivo (según indicador)',
                    'observacion_env' => '',
                    'id_form' => $this->id_form,
                ]);

                $filas = array_values($filas); // ✅ reindexa 0..N

                foreach ($filas as $idx => $item) {

                    // ✅ usa fila_det si viene del preview; si no, usa idx+1
                    $filaDet = isset($item['fila_det']) && (int)$item['fila_det'] > 0
                        ? (int)$item['fila_det']
                        : ($idx + 1);

                    $ambito = $item['ambito_geo'] ?? 'SIN_AMBITO';

                    $payload = $item['payload_det'] ?? [
                        'tipo' => $this->metodo,
                        'raw' => ($item['nombre'] ?? 'GLOBAL') . ' ' . ($item['valor'] ?? ''),
                        'nombre' => $item['nombre'] ?? null,
                        'valor' => $item['valor'] ?? null,
                    ];

                    $valorDet = $item['valor_det'] ?? ($item['valor'] ?? null);

                    // ✅ NO tronar por duplicado: actualiza si ya existe esa llave única
                    DetalleCarga::updateOrCreate(
                        [
                            'id_carga' => $carga->id_carga,
                            'id_ind' => $this->id_ind,
                            'ambito_geo' => $ambito,
                            'periodo_det' => $periodo,
                            'ejercicio_det' => $ejercicio,
                            'fila_det' => $filaDet,
                        ],
                        [
                            'id_region' => $item['id_region'] ?? null,
                            'id_mun' => $item['id_mun'] ?? null,
                            'fecha_registro_det' => now()->toDateString(),
                            'fuente_det' => $fuente,
                            'valor_det' => $valorDet,
                            'payload_det' => $payload,
                        ]
                    );
                }
            });

            // ✅ limpiar estado (solo si todo salió bien)
            $this->manualData = [];
            $this->region = '';
            $this->municipio = '';
            $this->valor = '';
            $this->resetArchivo();

            session()->flash('success', 'Guardado correctamente (Carga + Detalles).');
        } catch (\Throwable $e) {
            session()->flash('error', 'Error al guardar: ' . $e->getMessage());
        } finally {
            $this->guardando = false; // ✅ SIEMPRE se libera
        }
    }

    /**
     * Convierte líneas a filas "libres" (texto + número opcional)
     * ✅ Limpia BOM / invisibles y evita filas extra
     * ✅ Solo detecta número si está al final separado por espacio
     */

    private function convertirArchivoAFilas(array $lineas): array
    {
        $out = [];
        $fila = 1;

        foreach ($lineas as $linea) {

            // limpiar BOM y caracteres invisibles
            $raw = preg_replace('/^\xEF\xBB\xBF/', '', (string)$linea);
            $raw = preg_replace('/[[:^print:]]/', '', $raw);
            $raw = trim($raw);

            // si queda vacío, NO crear fila
            if ($raw === '') {
                continue;
            }

            // normaliza espacios internos
            $raw = preg_replace('/\s+/', ' ', $raw);

            // detectar número SOLO si está al final separado por espacio
            $valor = null;
            if (preg_match('/\s(-?\d+(?:\.\d+)?)\s*$/', $raw, $m)) {
                $valor = (float)$m[1];
            }

            $out[] = [
                'ambito_geo' => 'SIN_AMBITO',
                'id_region' => null,
                'id_mun' => null,
                'fila_det' => $fila++,

                // puede ser null (texto)
                'valor_det' => $valor,

                // texto completo SIEMPRE
                'payload_det' => [
                    'tipo' => 'archivo_libre',
                    'raw' => $raw,
                ],
            ];
        }

        return $out;
    }

    public function updatedAmbitoGeo($value)
    {
        $this->region = '';
        $this->municipio = '';
        $this->valor = '';
        $this->regionFiltro = '';
        $this->municipiosFiltrados = collect();

        // si no quieres mezclar ámbitos, limpias tabla:
        $this->manualData = [];
    }

    public function updatedRegionFiltro($value)
    {
        $this->municipio = '';
        $this->municipiosFiltrados = collect();

        if ($this->ambito_geo !== 'MUNICIPIO') return;
        if (!$value) return;

        $this->municipiosFiltrados = Municipio::where('id_region', $value)
            ->orderBy('nombre_municipio')
            ->get();
    }

    protected function rules()
    {
        return [
            'archivo' => 'required|file|max:10240|mimes:csv,txt,xlsx,xls',
        ];
    }

    private function leerCsvComoFilas(string $path): array
    {
        $lines = file($path, FILE_IGNORE_NEW_LINES);
        $lines = array_values(array_filter($lines, fn($l) => trim($l) !== ''));

        $max = 500;
        if (count($lines) > $max) {
            $lines = array_slice($lines, 0, $max);
            session()->flash('error', "El archivo trae muchas filas. Mostrando solo las primeras {$max}.");
        }

        return $lines;
    }

    private function leerExcelComoFilas(string $path): array
    {
        $sheets = Excel::toArray([], $path);
        $rows = $sheets[0] ?? [];

        $out = [];
        foreach ($rows as $row) {
            $out[] = implode(' | ', array_map(fn($c) => is_null($c) ? '' : (string)$c, $row));
        }

        $out = array_values(array_filter($out, fn($l) => trim($l) !== ''));

        $max = 500;
        if (count($out) > $max) {
            $out = array_slice($out, 0, $max);
            session()->flash('error', "El Excel trae muchas filas. Mostrando solo las primeras {$max}.");
        }

        return $out;
    }

    public function render()
    {

        return view('livewire.usuarios.formulario-captura', [
            'regiones' => $this->regiones,
            'municipiosFiltrados' => $this->municipiosFiltrados,
            'manualData' => $this->manualData,
            'metodo' => $this->metodo,
            'ambito_geo' => $this->ambito_geo,
            'regionFiltro' => $this->regionFiltro,
            'archivoNombre' => $this->archivoNombre,
            'archivoData' => $this->archivoData,
            'archivoPreview' => $this->archivoPreview,
        ])->extends('layouts.app')->section('content');
    }
}
