<?php

namespace App\Livewire\Usuario;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\Region;
use App\Models\Municipio;
use App\Models\Carga;
use App\Models\DetalleCarga;
use Illuminate\Support\Facades\DB;
use App\Models\Anexo;
use App\Models\Formulario;
use App\Models\Indicador;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Reader\Csv;
use Illuminate\Support\Facades\Schema;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\PlantillaIndicadorExport;
use App\Models\MapeoIndicador; // si es tu tabla de campos
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;





class FormularioCaptura extends Component
{
    use WithFileUploads;

    public $id_form;
    public $id_ind;
    public $formulario;
    public $indicador;

    public $metodo = null;
    public bool $guardando = false;
    public bool $hayPlantilla = false;

    public bool $soloLectura = false;

    // SIN_AMBITO | REGION | MUNICIPIO
    public string $ambito_geo = 'SIN_AMBITO';

    // filtros manual
    public $regionFiltro = '';
    public $municipiosFiltrados;

    public $region = '';
    public $municipio = '';

    public $id_carga = null;
    public $cargaActual = null;


    // schema dinámico
    public array $schema = [];
    public array $manualCampos = [];
    public array $manualData = [];
    public ?int $editManualIndex = null;
    public $archivoData = [];

    // metadata
    public $fuente_dato = '';
    public $descripcion_env = '';

    // archivo + flujo plantilla
    public $archivo;
    public string $archivoNombre = '';
    public ?int $id_carga_actual = null;
    public bool $modoCorreccion = false;
    public bool $metodoBloqueado = false;
    public $mensajeObservacion = null;

    public bool $plantillaDescargada = false;
    public bool $archivoProcesado = false;
    public int $detallesInsertados = 0;

    // catálogos
    public $regiones;
    public string $ambito_plantilla_descargada = ''; // para saber con qué ámbito se descargó
    public string $motivoResetPlantilla = '';        // mensaje para UI

    public function mount($id_form, $id_ind)
    {
        $this->id_carga = request('id_carga');
        $this->modoCorreccion = request('modo') === 'correccion';
        $this->id_form = $id_form;
        $this->id_ind  = $id_ind;

        $this->formulario = Formulario::with('indicador')
            ->where('id_form', $this->id_form)
            ->firstOrFail();

        if ((int)$this->formulario->id_ind !== (int)$this->id_ind) {
            abort(403, 'Indicador inválido para este formulario.');
        }

        $this->indicador = $this->formulario->indicador;

        // ✅ schema: normalizar a un formato consistente (slug/type/label/required/min/max)
        $raw = $this->indicador->config_campos ?? [];
        if (is_string($raw)) $raw = json_decode($raw, true) ?: [];

        $this->schema = $this->normalizarSchema($raw);

        // inicializa inputs manuales
        foreach ($this->schema as $c) {
            $this->manualCampos[$c['slug']] = null;
        }

        // ✅ seguridad dependencia
        $user = Auth::user();
        if ((int)$this->formulario->id_depen !== (int)$user->id_depen) {
            abort(403, 'Este formulario no pertenece a tu dependencia.');
        }

        // ✅ estado publicación
        $estado = (string) $this->formulario->boton_accion_form;
        if (!in_array($estado, ['Ver', 'Finalizado'], true)) {
            abort(403, 'Este formulario aún no ha sido publicado.');
        }

        // ✅ vencimiento por periodo
        $fechaCreacion = Carbon::parse($this->formulario->fecha_creacion_form);
        $hoy = Carbon::now();

        $fechaFin = match ($this->formulario->periodo_form) {
            'Mensual' => $fechaCreacion->copy()->addMonth(),
            'Trimestral' => $fechaCreacion->copy()->addMonths(3),
            'Semestral' => $fechaCreacion->copy()->addMonths(6),
            'Anual' => $fechaCreacion->copy()->addYear(),
            default => $fechaCreacion,
        };

        if ($hoy->gte($fechaFin)) {
            $this->soloLectura = true;
            if ($this->formulario->boton_accion_form !== 'Finalizado') {
                $this->formulario->boton_accion_form = 'Finalizado';
                $this->formulario->save();
            }
        } else {
            $this->soloLectura = ($this->formulario->boton_accion_form !== 'Ver');
        }

        //$this->metodo = null;
        //$this->ambito_geo = 'SIN_AMBITO';

        $this->regiones = Region::orderBy('nombre_region')->get();
        $this->municipiosFiltrados = collect();

        //$this->fuente_dato = '';
        //$this->descripcion_env = '';

        // Si el indicador tiene config_campos, el sistema puede generar plantilla
        $this->hayPlantilla = !empty($this->schema);

        $this->id_carga_actual = $this->id_carga;

        /* =========================================================
   ✅ CASO A: CONTINUAR BORRADOR NORMAL (sin corrección)
   /usuario/formulario/... ?id_carga=XX
========================================================= */
        if (!$this->modoCorreccion && $this->id_carga_actual) {

            $carga = Carga::find($this->id_carga_actual);

            if (!$carga) {
                abort(404, 'Carga no encontrada.');
            }

            // ✅ seguridad extra: la carga debe ser del mismo formulario
            if ((int)$carga->id_form !== (int)$this->id_form) {
                abort(403, 'Esta carga no pertenece a este formulario.');
            }

            // ✅ método fijo desde BD
            $this->metodo = (strtoupper((string)$carga->metodo_captura) === 'ARCHIVO') ? 'archivo' : 'manual';

            // ✅ bloquear cambio de método (ya inició)
            $this->metodoBloqueado = true;

            // ✅ ámbito guardado
            if (!empty($carga->ambito_geo_carga)) {
                $this->ambito_geo = $carga->ambito_geo_carga;
            }

            // ✅ si NO es borrador => solo lectura
            $estadoNorm = mb_strtoupper(trim((string)($carga->status_env ?? '')));
            $estadoNorm = str_replace('REVISIÓN', 'REVISION', $estadoNorm);

            if ($estadoNorm !== 'BORRADOR') {
                $this->soloLectura = true;
            }

            // ✅ si es ARCHIVO: flags para UI
            if ($this->metodo === 'archivo') {
                $this->plantillaDescargada = !is_null($carga->plantilla_descargada_at);

                $this->archivoProcesado = DetalleCarga::where('id_carga', $carga->id_carga)
                    ->where('id_ind', $this->id_ind)
                    ->exists();

                $this->detallesInsertados = DetalleCarga::where('id_carga', $carga->id_carga)
                    ->where('id_ind', $this->id_ind)
                    ->count();
            }

            // ✅ si es MANUAL: cargar filas guardadas del borrador (autoguardado)
            if ($this->metodo === 'manual') {
                $hayDetalles = DetalleCarga::where('id_carga', $carga->id_carga)
                    ->where('id_ind', $this->id_ind)
                    ->exists();

                if ($hayDetalles) {
                    $this->cargarDatosDeCarga($carga); // ✅ llena $this->manualData
                }
            }
        }


        /* =========================================================
   ✅ CASO B: CORRECCIÓN
   /usuario/formulario/... ?id_carga=XX&modo=correccion
========================================================= */
        if ($this->modoCorreccion && $this->id_carga_actual) {

            $carga = Carga::find($this->id_carga_actual);

            if (!$carga) {
                abort(404, 'Carga no encontrada para corrección.');
            }

            // seguridad extra: la carga debe ser del mismo formulario
            if ((int)$carga->id_form !== (int)$this->id_form) {
                abort(403, 'Esta carga no pertenece a este formulario.');
            }

            // método original fijo
            $this->metodo = (strtoupper((string)$carga->metodo_captura) === 'ARCHIVO') ? 'archivo' : 'manual';

            // en corrección siempre bloqueado
            $this->metodoBloqueado = true;

            // mostrar observación
            $this->mensajeObservacion = $carga->observacion_env;

            // ámbito original si existe
            if (!empty($carga->ambito_geo_carga)) {
                $this->ambito_geo = $carga->ambito_geo_carga;
            }

            // carga datos existentes
            $this->cargarDatosDeCarga($carga);

            // flags de archivo (para UI)
            if ($this->metodo === 'archivo') {
                $this->plantillaDescargada = !is_null($carga->plantilla_descargada_at);

                $this->archivoProcesado = DetalleCarga::where('id_carga', $carga->id_carga)
                    ->where('id_ind', $this->id_ind)
                    ->exists();

                $this->detallesInsertados = DetalleCarga::where('id_carga', $carga->id_carga)
                    ->where('id_ind', $this->id_ind)
                    ->count();
            }
        }
    }

    public function cargarDatosDeCarga(\App\Models\Carga $carga): void
    {
        $detalles = \App\Models\DetalleCarga::where('id_carga', $carga->id_carga)
            ->where('id_ind', $this->id_ind)
            ->orderBy('id_detalle')
            ->get();

        if ($this->metodo === 'manual') {
            $this->manualData = [];

            foreach ($detalles as $d) {
                $payload = $d->payload_det;

                // si viene como string JSON (por si acaso), decodifica
                if (is_string($payload)) {
                    $payload = json_decode($payload, true) ?: [];
                }
                if (!is_array($payload)) $payload = [];

                // Nombre visible (para tu tabla manual: $row['nombre'])
                $nombre = 'Global';
                if ($d->ambito_geo === 'REGION') {
                    $nombre = optional(\App\Models\Region::find($d->id_region))->nombre_region ?? '—';
                } elseif ($d->ambito_geo === 'MUNICIPIO') {
                    $nombre = optional(\App\Models\Municipio::find($d->id_mun))->nombre_municipio ?? '—';
                }

                $this->manualData[] = [
                    'ambito_geo' => $d->ambito_geo,
                    'id_region'  => $d->id_region,
                    'id_mun'     => $d->id_mun,
                    'nombre'     => $nombre,

                    // ✅ lo que tu Blade usa:
                    'payload_det' => [
                        'campos' => $payload['campos'] ?? [],
                    ],
                ];
            }
        } else {
            // opcional preview para archivo
            $this->archivoData = $detalles->take(10)->toArray();
        }
    }

    /* =========================
       ✅ NORMALIZADOR DE SCHEMA
    ========================== */
    private function normalizarSchema(array $raw): array
    {
        $out = [];

        foreach ($raw as $c) {
            // soporta ambas llaves: name o slug
            $slug = $c['slug'] ?? $c['name'] ?? null;
            if (!$slug) continue;

            $out[] = [
                'slug' => (string)$slug,
                'label' => (string)($c['label'] ?? $c['nombre'] ?? $slug),
                'type' => $c['type'] ?? $c['tipo'] ?? 'number',
                'required' => (bool)($c['required'] ?? $c['requerido'] ?? false),
                'min' => $c['min'] ?? null,
                'max' => $c['max'] ?? null,
            ];
        }

        return $out;
    }

    public function seleccionar($metodo)
    {
        if ($this->metodoBloqueado) {
            session()->flash('error', 'Esta carga está en corrección. No puedes cambiar el método de captura.');
            return;
        }

        // ✅ PASO 3A: si ya existe una carga, respetar su método y bloquear cambios si hay avance
        if ($this->id_carga_actual) {
            $carga = Carga::find($this->id_carga_actual);

            if ($carga) {
                $metodoBD = strtolower((string)$carga->metodo_captura); // "manual" o "archivo"

                // si intenta cambiar a otro método distinto al que ya tiene la carga
                if ($metodoBD && $metodoBD !== $metodo) {

                    // ¿hay detalles ya guardados para esta carga e indicador?
                    $hayDetalles = DetalleCarga::where('id_carga', $carga->id_carga)
                        ->where('id_ind', $this->id_ind)
                        ->exists();

                    if ($hayDetalles) {
                        session()->flash(
                            'error',
                            "No puedes cambiar a {$metodo} porque esta carga ya tiene información capturada en {$metodoBD}. " .
                                "Si deseas cambiar, debes eliminar el avance y reiniciar la captura."
                        );
                        return;
                    }

                    // ✅ si NO hay detalles, permitimos cambio, pero limpiamos estado del otro método
                    if ($metodo === 'archivo') {
                        $this->manualData = [];
                        $this->editManualIndex = null;
                    } else {
                        $this->resetArchivoState();
                    }

                    // actualiza el método en BD para mantener consistencia
                    $carga->metodo_captura = strtoupper($metodo); // MANUAL/ARCHIVO
                    $carga->save();
                }
            }
        }

        $this->metodo = $metodo;

        if ($metodo === 'archivo') {
            $this->prepararCargaBorradorArchivo();
            $this->refrescarEstadoPlantilla();
            return;
        }

        if ($metodo === 'manual') {
            $this->prepararCargaBorradorManual();
            $this->resetArchivoState();
            return;
        }
    }

    private function prepararCargaBorradorArchivo()
    {
        if ($this->soloLectura) return;

        // ✅ si ya hay una carga actual en el componente, no crear otra
        if ($this->id_carga_actual) return;

        // ✅ PASO 4B: reusar borrador existente (mismo formulario + método ARCHIVO)
        $borrador = Carga::where('id_form', $this->id_form)
            ->where('status_env', 'Borrador')   // ⚠️ si usas 'BORRADOR' cambia aquí
            ->where('metodo_captura', 'ARCHIVO')
            ->orderByDesc('id_carga')
            ->first();

        if ($borrador) {
            $this->id_carga_actual = (int)$borrador->id_carga;

            // sincroniza flags desde BD
            $this->plantillaDescargada = !is_null($borrador->plantilla_descargada_at);

            // archivo procesado = si ya hay detalles guardados del indicador
            $this->archivoProcesado = DetalleCarga::where('id_carga', $borrador->id_carga)
                ->where('id_ind', $this->id_ind)
                ->exists();

            $this->detallesInsertados = DetalleCarga::where('id_carga', $borrador->id_carga)
                ->where('id_ind', $this->id_ind)
                ->count();

            // sincroniza ámbito guardado
            if (!empty($borrador->ambito_geo_carga)) {
                $this->ambito_geo = $borrador->ambito_geo_carga;
            }

            return;
        }

        // ✅ si no existe borrador, crear uno nuevo
        $carga = Carga::create([
            'folioUnico_carga' => 'CAR-' . now()->timestamp,
            'fecha_carga' => now(),
            'periodo' => now()->format('Y-m'),
            'ejercicio' => now()->year,
            'fuente' => 'N/D',
            'status_env' => 'Borrador',
            'ambito_geo_carga' => $this->ambito_geo,
            'metodo_captura' => 'ARCHIVO',
            'descripcion_env' => 'Borrador (archivo)',
            'observacion_env' => '',
            'id_form' => $this->id_form,
        ]);

        $this->id_carga_actual = (int)$carga->id_carga;
        $this->plantillaDescargada = false;
        $this->archivoProcesado = false;
        $this->detallesInsertados = 0;
    }

    private function prepararCargaBorradorManual()
    {
        if ($this->soloLectura) return;

        // ✅ si ya hay carga actual en el componente, no crear otra
        if ($this->id_carga_actual) return;

        // ✅ PASO 4: reusar borrador existente (mismo formulario + método MANUAL)
        $borrador = Carga::where('id_form', $this->id_form)
            ->where('status_env', 'Borrador')   // ⚠️ si usas 'BORRADOR' cambia aquí
            ->where('metodo_captura', 'MANUAL')
            ->orderByDesc('id_carga')
            ->first();

        if ($borrador) {
            $this->id_carga_actual = (int)$borrador->id_carga;

            // (Opcional) si quieres, sincroniza el ámbito desde BD al cargar borrador
            if (!empty($borrador->ambito_geo_carga)) {
                $this->ambito_geo = $borrador->ambito_geo_carga;
            }

            // (Opcional) si quieres que al retomar se carguen datos ya guardados:
            // En tu manual, guardas detalle hasta ENVIAR, así que normalmente no habrá detalles aún.
            // Si más adelante guardas parciales, esto te servirá:
            $hayDetalles = DetalleCarga::where('id_carga', $borrador->id_carga)
                ->where('id_ind', $this->id_ind)
                ->exists();

            if ($hayDetalles) {
                $this->metodo = 'manual';
                $this->cargarDatosDeCarga($borrador);
            }

            return;
        }

        // ✅ si no existe borrador, crear uno nuevo
        $carga = Carga::create([
            'folioUnico_carga' => Str::upper(Str::random(6)),
            'fecha_carga' => now(),
            'periodo' => now()->format('Y-m'),
            'ejercicio' => now()->year,
            'fuente' => 'N/D',
            'status_env' => 'Borrador',
            'ambito_geo_carga' => $this->ambito_geo,
            'metodo_captura' => 'MANUAL',
            'descripcion_env' => 'Borrador (manual)',
            'observacion_env' => '',
            'id_form' => $this->id_form,
        ]);

        $this->id_carga_actual = (int)$carga->id_carga;
    }

    public function refrescarEstadoPlantilla()
    {
        if (!$this->id_carga_actual) {
            $this->plantillaDescargada = false;
            return;
        }
        $carga = Carga::find($this->id_carga_actual);
        $this->plantillaDescargada = !is_null($carga?->plantilla_descargada_at);
    }

    public function reiniciarCaptura()
    {
        if ($this->soloLectura) {
            session()->flash('error', 'Solo lectura. No puedes reiniciar.');
            return;
        }

        if ($this->modoCorreccion) {
            session()->flash('error', 'Esta carga está en corrección. No puedes reiniciar el método.');
            return;
        }

        if (!$this->id_carga_actual) {
            // nada que reiniciar
            $this->metodo = null;
            $this->metodoBloqueado = false;
            return;
        }

        $carga = Carga::find($this->id_carga_actual);
        if (!$carga) {
            // si por algo no existe, resetea estado
            $this->id_carga_actual = null;
            $this->metodo = null;
            $this->metodoBloqueado = false;
            $this->resetArchivoState();
            $this->manualData = [];
            $this->editManualIndex = null;
            session()->flash('success', 'Captura reiniciada.');
            return;
        }

        // ✅ Solo permitir reiniciar si sigue siendo borrador
        if (strtoupper((string)$carga->status_env) !== 'BORRADOR' && (string)$carga->status_env !== 'Borrador') {
            session()->flash('error', 'Esta captura ya fue enviada. No se puede reiniciar.');
            return;
        }

        // ✅ No debe haber detalles guardados
        $hayDetalles = DetalleCarga::where('id_carga', $carga->id_carga)
            ->where('id_ind', $this->id_ind)
            ->exists();

        // ✅ Consideramos "avance" también: plantilla descargada / archivo procesado / filas en memoria manual
        $hayAvanceArchivo = !is_null($carga->plantilla_descargada_at) || $this->archivoProcesado || !empty($this->archivoNombre);
        $hayAvanceManual  = !empty($this->manualData);

        if ($hayDetalles || $hayAvanceArchivo || $hayAvanceManual) {
            session()->flash('error', 'No puedes reiniciar porque ya hay avance en la captura. Si deseas cambiar de método, elimina el avance primero.');
            return;
        }

        // ✅ Sin avance: se borra la carga borrador (no hay detalles, así que es seguro)
        $carga->delete();

        // reset estado del componente
        $this->id_carga_actual = null;
        $this->metodo = null;
        $this->metodoBloqueado = false;

        $this->resetArchivoState();
        $this->plantillaDescargada = false;
        $this->archivoProcesado = false;
        $this->detallesInsertados = 0;
        $this->ambito_plantilla_descargada = '';
        $this->motivoResetPlantilla = '';

        $this->manualData = [];
        $this->editManualIndex = null;

        session()->flash('success', 'Captura reiniciada. Ahora puedes elegir otro método.');
    }

    /* =========================
       ✅ MANUAL DINÁMICO (Opción B)
    ========================== */
    public function updatedAmbitoGeo($value)
    {
        $this->region = '';
        $this->municipio = '';
        $this->regionFiltro = '';
        $this->municipiosFiltrados = collect();

        // si no quieres mezclar ámbitos: limpia filas manuales
        $this->manualData = [];
        $this->editManualIndex = null;

        // si ya estaba en archivo, actualiza borrador (si existe)
        if ($this->metodo === 'archivo' && $this->id_carga_actual) {
            Carga::where('id_carga', $this->id_carga_actual)
                ->update(['ambito_geo_carga' => $this->ambito_geo]);
        }

        // Si ya había plantilla descargada y cambió el ámbito => forzar re-descarga
        if ($this->plantillaDescargada && $this->ambito_plantilla_descargada !== $this->ambito_geo) {
            $this->resetArchivoState();
            $this->plantillaDescargada = false;

            $this->motivoResetPlantilla = "Cambiaste el ámbito. Debes descargar una nueva plantilla para {$this->ambito_geo}.";
            session()->flash('warning', $this->motivoResetPlantilla);
        }
    }

    public function updatedRegionFiltro($value)
    {
        if ($this->soloLectura) return;

        $this->municipio = '';
        $this->municipiosFiltrados = collect();

        if ($this->ambito_geo !== 'MUNICIPIO') return;
        if (!$value) return;

        $this->municipiosFiltrados = Municipio::where('id_region', $value)
            ->orderBy('nombre_municipio')
            ->get();
    }

    private function validarFilaManual()
    {
        if ($this->ambito_geo === 'REGION' && !$this->region) {
            throw new \Exception("Selecciona una región.");
        }
        if ($this->ambito_geo === 'MUNICIPIO' && !$this->municipio) {
            throw new \Exception("Selecciona un municipio.");
        }

        // validar campos del schema
        foreach ($this->schema as $c) {
            $slug = $c['slug'];
            $val = $this->manualCampos[$slug] ?? null;

            if ($c['required'] && ($val === null || $val === '')) {
                throw new \Exception("El campo '{$c['label']}' es requerido.");
            }

            if (($c['type'] === 'number' || $c['type'] === 'porcentaje') && $val !== null && $val !== '') {
                if (!is_numeric($val)) throw new \Exception("El campo '{$c['label']}' debe ser numérico.");
                $num = (float)$val;

                if ($c['min'] !== null && $num < $c['min']) throw new \Exception("{$c['label']} debe ser >= {$c['min']}.");
                if ($c['max'] !== null && $num > $c['max']) throw new \Exception("{$c['label']} debe ser <= {$c['max']}.");

                // decimales: number = entero, porcentaje = decimal ok
                if ($c['type'] === 'number' && floor($num) != $num) {
                    throw new \Exception("{$c['label']} no acepta decimales.");
                }
            }
        }
    }

    public function agregarManual()
    {
        if ($this->soloLectura) {
            session()->flash('error', 'Este formulario está finalizado. Solo lectura.');
            return;
        }

        try {
            if (empty($this->schema)) {
                throw new \Exception("Este indicador no tiene config_campos. Pídele al admin que lo configure.");
            }

            $this->validarFilaManual();

            // nombre (para tabla)
            $nombre = 'GLOBAL';
            $idRegion = null;
            $idMun = null;

            if ($this->ambito_geo === 'REGION') {
                $r = Region::find($this->region);
                if (!$r) throw new \Exception("Región inválida.");
                $nombre = $r->nombre_region;
                $idRegion = (int)$r->id_region;
            }

            if ($this->ambito_geo === 'MUNICIPIO') {
                $m = Municipio::find($this->municipio);
                if (!$m) throw new \Exception("Municipio inválido.");
                $nombre = $m->nombre_municipio;
                $idMun = (int)$m->id_mun;
                $idRegion = (int)$m->id_region;
            }

            // evita duplicados por ubicación (opcional)
            $yaExiste = collect($this->manualData)->contains(function ($row) use ($idRegion, $idMun) {
                if (($row['ambito_geo'] ?? '') !== $this->ambito_geo) return false;
                if ($this->ambito_geo === 'SIN_AMBITO') return true; // solo una global
                if ($this->ambito_geo === 'REGION') return (int)($row['id_region'] ?? 0) === (int)$idRegion;
                if ($this->ambito_geo === 'MUNICIPIO') return (int)($row['id_mun'] ?? 0) === (int)$idMun;
                return false;
            });

            if ($yaExiste && $this->editManualIndex === null) {
                throw new \Exception("Esa ubicación ya fue agregada. Edita el registro existente.");
            }

            $fila = [
                'ambito_geo' => $this->ambito_geo,
                'id_region' => $this->ambito_geo === 'SIN_AMBITO' ? null : $idRegion,
                'id_mun' => $this->ambito_geo === 'MUNICIPIO' ? $idMun : null,
                'nombre' => $nombre,
                'payload_det' => [
                    'origen' => 'manual_dinamico',
                    'campos' => $this->manualCampos,
                ],
            ];

            if ($this->editManualIndex !== null) {
                $this->manualData[$this->editManualIndex] = $fila;
                $this->editManualIndex = null;
            } else {
                $this->manualData[] = $fila;
            }

            // limpia inputs
            foreach ($this->schema as $c) {
                $this->manualCampos[$c['slug']] = null;
            }
            $this->region = '';
            $this->municipio = '';

            $this->syncManualDraftToDB();

            session()->flash('success', 'Fila agregada.');
        } catch (\Throwable $e) {
            session()->flash('error', $e->getMessage());
        }
    }

    public function editarManual($index)
    {
        if ($this->soloLectura) {
            session()->flash('error', 'Solo lectura.');
            return;
        }

        $row = $this->manualData[$index] ?? null;
        if (!$row) return;

        $this->editManualIndex = $index;
        $this->ambito_geo = $row['ambito_geo'] ?? 'SIN_AMBITO';
        $this->region = $row['id_region'] ?? '';
        $this->municipio = $row['id_mun'] ?? '';

        $campos = $row['payload_det']['campos'] ?? [];
        foreach ($this->schema as $c) {
            $this->manualCampos[$c['slug']] = $campos[$c['slug']] ?? null;
        }
    }

    public function eliminarManual($index)
    {
        if ($this->soloLectura) {
            session()->flash('error', 'Solo lectura.');
            return;
        }

        if (!isset($this->manualData[$index])) return;

        unset($this->manualData[$index]);

        // ✅ reindexar
        $this->manualData = array_values($this->manualData);

        // ✅ guardar snapshot en BD
        $this->syncManualDraftToDB();

        session()->flash('success', 'Fila eliminada.');
    }


    public function reenviarCorreccion()
    {
        if ($this->soloLectura) {
            session()->flash('error', 'Solo lectura.');
            return;
        }

        $carga = \App\Models\Carga::findOrFail($this->id_carga_actual);

        DB::transaction(function () use ($carga) {

            $metodo = strtoupper((string)$carga->metodo_captura);

            if ($metodo === 'MANUAL') {
                // ✅ MANUAL: reemplaza reinsertando desde lo capturado en pantalla
                \App\Models\DetalleCarga::where('id_carga', $carga->id_carga)
                    ->where('id_ind', $this->id_ind)
                    ->delete();

                $this->guardarManualEnDetalle($carga->id_carga);
            } else {
                // ✅ ARCHIVO: NO borres aquí. Solo valida que ya se procesó archivo.
                $hay = \App\Models\DetalleCarga::where('id_carga', $carga->id_carga)
                    ->where('id_ind', $this->id_ind)
                    ->exists();

                if (!$hay) {
                    throw new \Exception('Sube y procesa el archivo antes de reenviar la corrección.');
                }
            }

            $carga->status_env = 'REENVIADO';
            $carga->save();
        });

        session()->flash('success', 'Corrección reenviada correctamente.');
    }

    private function guardarManualEnDetalle($idCarga): void
    {
        // Para numeración de filas
        $fila = 1;

        foreach ((array)$this->manualData as $row) {
            $ambito = $row['ambito_geo'] ?? $this->ambito_geo ?? 'SIN_AMBITO';
            $idRegion = $row['id_region'] ?? null;
            $idMun = $row['id_mun'] ?? null;

            $campos = $row['payload_det']['campos'] ?? [];

            \App\Models\DetalleCarga::create([
                'id_carga' => $idCarga,
                'id_ind'   => $this->id_ind,

                'ambito_geo' => $ambito,
                'id_region'  => $ambito === 'REGION' || $ambito === 'MUNICIPIO' ? $idRegion : null,
                'id_mun'     => $ambito === 'MUNICIPIO' ? $idMun : null,

                'fila_det' => $fila++,

                'periodo_det' => $this->periodo_det ?? (string)($this->formularioPeriodo ?? '') ?: now()->format('Y-m'),
                'ejercicio_det' => (int)($this->ejercicio_det ?? now()->format('Y')),
                'fecha_registro_det' => now()->toDateString(),
                'fuente_det' => 'Registro manual',

                // En manual tu valor_det no aplica (porque es dinámico)
                'valor_det' => null,

                // ✅ JSON compatible con tu vista
                'payload_det' => [
                    'origen' => 'manual',
                    'campos' => $campos,
                ],
            ]);
        }
    }

    private function syncManualDraftToDB(): void
    {
        if ($this->soloLectura) return;

        // ✅ si no hay borrador aún, créalo
        if (!$this->id_carga_actual) {
            $this->prepararCargaBorradorManual();
        }

        $carga = Carga::find($this->id_carga_actual);
        if (!$carga) return;

        // ✅ borra y reinsertar el snapshot actual del manual (simple y seguro)
        DetalleCarga::where('id_carga', $carga->id_carga)
            ->where('id_ind', $this->id_ind)
            ->delete();

        $periodo   = $carga->periodo ?? now()->format('Y-m');
        $ejercicio = $carga->ejercicio ?? now()->year;

        foreach (array_values((array)$this->manualData) as $idx => $item) {
            $ambito = $item['ambito_geo'] ?? 'SIN_AMBITO';

            DetalleCarga::create([
                'id_carga' => $carga->id_carga,
                'id_ind'   => $this->id_ind,
                'ambito_geo' => $ambito,
                'id_region' => $item['id_region'] ?? null,
                'id_mun'    => $item['id_mun'] ?? null,
                'fila_det'  => $idx + 1,
                'periodo_det' => $periodo,
                'ejercicio_det' => $ejercicio,
                'fecha_registro_det' => now()->toDateString(),
                'fuente_det' => 'Borrador manual',
                'valor_det' => null,
                'payload_det' => $item['payload_det'] ?? [],
            ]);
        }

        // ✅ marca actividad (updated_at)
        $carga->touch();
    }


    /* =========================
       ARCHIVO (tu lógica + ajustes)
    ========================== */

    protected function rules()
    {
        return [
            'archivo' => 'required|file|max:10240|mimes:csv,txt,xlsx,xls',
        ];
    }

    public function updatedArchivo()
    {
        if ($this->soloLectura) {
            session()->flash('error', 'Este formulario está finalizado. No puedes subir archivos.');
            $this->archivo = null;
            return;
        }

        if (!$this->archivo) return;

        $this->validateOnly('archivo');

        $this->archivoNombre = $this->archivo->getClientOriginalName();
        // si cambia archivo, hay que reprocesar
        $this->archivoProcesado = false;
        $this->detallesInsertados = 0;
    }

    public function descargarPlantilla()
    {
        if ($this->soloLectura) {
            session()->flash('error', 'Solo lectura.');
            return;
        }

        $this->prepararCargaBorradorArchivo();

        $carga = Carga::findOrFail($this->id_carga_actual);
        $indicador = Indicador::findOrFail($this->id_ind);

        $campos = $indicador->config_campos ?? [];
        if (is_string($campos)) {
            $campos = json_decode($campos, true) ?: [];
        } elseif (!is_array($campos)) {
            $campos = [];
        }
        if (empty($campos)) {
            session()->flash('error', 'Este indicador no tiene config_campos definidos.');
            return;
        }

        $ambito = $this->ambito_geo;

        $colAHeader = match ($ambito) {
            'MUNICIPIO' => 'Municipio',
            'REGION' => 'Región',
            default => 'Ámbito',
        };

        $usaClave = in_array($ambito, ['MUNICIPIO', 'REGION'], true);
        $colBHeader = $ambito === 'MUNICIPIO' ? 'Clave municipio (opcional)' : 'ID Región (opcional)';

        $headerRow = 7;
        $dataStart = 8;

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Plantilla');

        $sheet->setCellValue('A1', mb_strtoupper((string)$indicador->nombre_ind));
        $sheet->setCellValue('A2', 'Periodo: ' . (string)($carga->periodo ?? $indicador->periodo_ind ?? '—'));
        $sheet->setCellValue('A3', 'Folio: ' . (string)$carga->folioUnico_carga);
        $sheet->setCellValue('A4', 'Ámbito: ' . (string)$ambito);

        $sheet->setCellValue("A{$headerRow}", $colAHeader);
        if ($usaClave) {
            $sheet->setCellValue("B{$headerRow}", $colBHeader);
        }

        // Campos desde E (col 5)
        $colIndex = 5;
        foreach ($campos as $c) {
            $label = $c['label'] ?? $c['slug'] ?? 'campo';
            $sheet->setCellValueByColumnAndRow($colIndex, $headerRow, $label);
            $colIndex++;
        }

        // filas base opcionales
        if ($ambito === 'MUNICIPIO') {
            $municipios = Municipio::orderBy('nombre_municipio')->get();
            $r = $dataStart;
            foreach ($municipios as $m) {
                $sheet->setCellValue("A{$r}", $m->nombre_municipio);
                if ($usaClave && isset($m->clave_municipio)) {
                    $sheet->setCellValue("B{$r}", $m->clave_municipio);
                }
                $r++;
            }
        } elseif ($ambito === 'REGION') {
            $regiones = Region::orderBy('nombre_region')->get();
            $r = $dataStart;
            foreach ($regiones as $reg) {
                $sheet->setCellValue("A{$r}", $reg->nombre_region);
                if ($usaClave) $sheet->setCellValue("B{$r}", $reg->id_region);
                $r++;
            }
        } else {
            $sheet->setCellValue("A{$dataStart}", 'Estado');
        }

        /* =========================
       ✅ HOJA 2: AYUDA
       ========================= */
        $ayuda = $spreadsheet->createSheet();
        $ayuda->setTitle('Ayuda');
        // opcional: asegurar que el índice 1 sea Ayuda
        $spreadsheet->setActiveSheetIndex(1);


        // Encabezado de ayuda
        $ayuda->setCellValue('A1', 'GUÍA DE CAPTURA');
        $ayuda->setCellValue('A2', 'Indicador: ' . (string)$indicador->nombre_ind);
        $ayuda->setCellValue('A3', 'Periodo: ' . (string)($carga->periodo ?? $indicador->periodo_ind ?? '—'));
        $ayuda->setCellValue('A4', 'Ámbito: ' . (string)$ambito);
        $ayuda->setCellValue('A5', 'Fuente: ' . (string)($indicador->fuenteverificacion_ind ?? '—'));
        $ayuda->setCellValue('A6', 'Definición: ' . (string)($indicador->definicion_ind ?? '—'));
        $ayuda->setCellValue('A7', 'Restricción: ' . (string)($indicador->restriccion_ind ?? '—'));

        // Reglas
        $row = 9;

        $ayuda->setCellValue("A{$row}", 'REGLAS');
        $row++;

        $reglas = [
            'No cambies los encabezados de la hoja "Plantilla".',
            'Captura los datos debajo de los encabezados (no arriba).',
            'Si un campo es porcentaje, captura solo el número (ej. 12.5) a menos que se indique lo contrario.',
            'Usa punto decimal (.) y evita separadores de miles con coma.',
            'No insertes filas vacías entre registros.',
            'Si no sabes qué significa una abreviación, revisa el diccionario abajo.',
        ];

        foreach ($reglas as $txt) {
            $ayuda->setCellValue("A{$row}", '• ' . $txt);
            $row++;
        }

        $row++; // espacio

        // Diccionario de campos
        $ayuda->setCellValue("A{$row}", 'DICCIONARIO DE CAMPOS');
        $row++;

        // Cabeceras del diccionario
        $ayuda->setCellValue("A{$row}", 'Campo (label)');
        $ayuda->setCellValue("B{$row}", 'Clave (slug)');
        $ayuda->setCellValue("C{$row}", 'Tipo');
        $ayuda->setCellValue("D{$row}", 'Obligatorio');
        $ayuda->setCellValue("E{$row}", 'Descripción / Nota');
        $row++;

        foreach ($campos as $c) {
            $label = (string)($c['label'] ?? $c['slug'] ?? 'campo');
            $slug  = (string)($c['slug'] ?? '');
            $tipo  = (string)($c['type'] ?? '');
            $req   = !empty($c['required']) ? 'SI' : 'NO';
            $min   = $c['min'] ?? '';
            $max   = $c['max'] ?? '';

            $notaRango = '';
            if ($min !== '' || $max !== '') {
                $notaRango = 'Rango: ' . ($min === '' ? '—' : $min) . ' a ' . ($max === '' ? '—' : $max);
            }

            $ayuda->setCellValue("A{$row}", $label);
            $ayuda->setCellValue("B{$row}", $slug);
            $ayuda->setCellValue("C{$row}", $tipo);
            $ayuda->setCellValue("D{$row}", $req);
            $ayuda->setCellValue("E{$row}", $notaRango);
            $row++;
        }

        // (Opcional pero útil) Ajuste básico de anchos
        foreach (['A', 'B', 'C', 'D', 'E'] as $col) {
            $ayuda->getColumnDimension($col)->setAutoSize(true);
        }

        // marcar descargada
        $carga->plantilla_descargada_at = now();
        $carga->plantilla_ambito = $ambito;
        $carga->save();

        $this->plantillaDescargada = true;
        $this->ambito_plantilla_descargada = $ambito;
        $this->motivoResetPlantilla = '';

        $filename = 'Plantilla_' . ($indicador->id_ind ?? 'ind') . '_' . $ambito . '_' . $carga->folioUnico_carga . '.xlsx';

        $spreadsheet->setActiveSheetIndex(0);

        // 🔹 marcar actividad
        if ($this->id_carga_actual) {
            Carga::where('id_carga', $this->id_carga_actual)
                ->update(['updated_at' => now()]);
        }

        return response()->streamDownload(function () use ($spreadsheet) {
            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');
        }, $filename);
    }

    public function procesarArchivo()
    {
        if ($this->soloLectura) {
            session()->flash('error', 'Solo lectura.');
            return;
        }

        $this->validate([
            'archivo' => 'required|file|mimes:xlsx,xls,csv|max:10240',
        ]);

        $this->prepararCargaBorradorArchivo();
        $carga = Carga::findOrFail($this->id_carga_actual);

        if ($this->modoCorreccion && strtoupper((string)$carga->metodo_captura) !== 'ARCHIVO') {
            session()->flash('error', 'Esta carga se creó como MANUAL. No puedes corregirla subiendo archivo.');
            return;
        }

        // ✅ Siempre reemplaza detalles del indicador al procesar archivo (modo normal o corrección)
        \App\Models\DetalleCarga::where('id_carga', $carga->id_carga)
            ->where('id_ind', $this->id_ind)
            ->delete();

        // ✅ FIX: sincroniza el flag local con BD
        $this->plantillaDescargada = !is_null($carga->plantilla_descargada_at);
        if (is_null($carga->plantilla_descargada_at)) {
            session()->flash('error', 'Debes descargar la plantilla antes de subir el archivo.');
            return;
        }

        try {
            $indicador = Indicador::findOrFail($this->id_ind);

            $campos = $indicador->config_campos ?? [];
            if (is_string($campos)) $campos = json_decode($campos, true) ?: [];
            if (empty($campos)) {
                session()->flash('error', 'Este indicador no tiene config_campos definidos.');
                return;
            }

            $ambito = $this->ambito_geo;

            if (!empty($carga->plantilla_ambito) && $carga->plantilla_ambito !== $ambito) {
                session()->flash('error', "El ámbito actual ({$ambito}) no coincide con el de la plantilla descargada ({$carga->plantilla_ambito}). Descarga una nueva plantilla.");
                return;
            }

            $headerRow = 7;
            $dataStart = 8;

            $colAEsperado = match ($ambito) {
                'MUNICIPIO' => 'Municipio',
                'REGION' => 'Región',
                default => 'Ámbito',
            };

            $ext  = strtolower($this->archivo->getClientOriginalExtension());
            $path = $this->archivo->getRealPath();

            if ($ext === 'csv') {
                $reader = new Csv();
                $reader->setDelimiter(',');
                $reader->setEnclosure('"');
                $reader->setInputEncoding('UTF-8');
            } else {
                $reader = IOFactory::createReaderForFile($path);
            }

            $spreadsheet = $reader->load($path);
            $sheet = $spreadsheet->getActiveSheet();

            // ✅ VALIDACIONES DE ENCABEZADOS
            $headerA = trim((string)$sheet->getCell("A{$headerRow}")->getValue());
            if (mb_strtolower($headerA) !== mb_strtolower($colAEsperado)) {
                session()->flash('error', "Estructura inválida: en A{$headerRow} se esperaba '{$colAEsperado}'.");
                return;
            }

            // valida columnas dinámicas desde E (col 5)
            $colIndex = 5;
            foreach ($campos as $c) {
                $labelEsperado = trim((string)($c['label'] ?? $c['slug']));
                $labelArchivo  = trim((string)$sheet->getCellByColumnAndRow($colIndex, $headerRow)->getValue());

                if (mb_strtolower($labelArchivo) !== mb_strtolower($labelEsperado)) {
                    session()->flash('error', "Estructura inválida: columna {$this->colLetra($colIndex)} debía ser '{$labelEsperado}' y llegó '{$labelArchivo}'.");
                    return;
                }
                $colIndex++;
            }

            $maxRow = (int)$sheet->getHighestRow();
            $insertados = 0;
            $errores = [];

            DB::transaction(function () use ($sheet, $maxRow, $dataStart, $headerRow, $ambito, $campos, $carga, $indicador, $ext, &$insertados, &$errores) {

                for ($r = $dataStart; $r <= $maxRow; $r++) {
                    $dim = trim((string)$sheet->getCell("A{$r}")->getValue());
                    if ($dim === '') continue;
                    $id_region = null;
                    $id_mun = null;

                    if ($ambito === 'MUNICIPIO') {
                        $headerB = trim((string)$sheet->getCell("B{$headerRow}")->getValue());
                        if (mb_strtolower($headerB) !== mb_strtolower('Clave municipio (opcional)')) {
                            $errores[] = "Encabezado inválido en B{$headerRow}.";
                            continue;
                        }

                        $clave = trim((string)$sheet->getCell("B{$r}")->getValue());

                        $mun = null;
                        if ($clave !== '' && Schema::hasColumn('municipios', 'clave_municipio')) {
                            $mun = Municipio::where('clave_municipio', $clave)->first();
                        }
                        if (!$mun) {
                            $mun = Municipio::where('nombre_municipio', $dim)->first();
                        }
                        if (!$mun) {
                            $errores[] = "Fila {$r}: municipio no encontrado ({$dim}).";
                            continue;
                        }
                        $id_mun = (int)$mun->id_mun;
                        $id_region = (int)$mun->id_region;
                    }

                    if ($ambito === 'REGION') {
                        $id = trim((string)$sheet->getCell("B{$r}")->getValue());
                        $reg = null;
                        if ($id !== '') $reg = Region::where('id_region', $id)->first();
                        if (!$reg) $reg = Region::where('nombre_region', $dim)->first();
                        if (!$reg) {
                            $errores[] = "Fila {$r}: región no encontrada ({$dim}).";
                            continue;
                        }
                        $id_region = (int)$reg->id_region;
                    }

                    // payload en formato compatible con tu esquema general
                    $camposPayload = [];
                    $colIndex = 5;

                    foreach ($campos as $c) {
                        $slug = (string)($c['slug'] ?? '');
                        if ($slug === '') {
                            $errores[] = "Fila {$r}: hay un campo sin slug en config_campos.";
                            continue 2;
                        }

                        $type = $c['type'] ?? 'number';
                        $required = (bool)($c['required'] ?? false);
                        $min = $c['min'] ?? null;
                        $max = $c['max'] ?? null;

                        $raw = $sheet->getCellByColumnAndRow($colIndex, $r)->getValue();
                        $val = is_string($raw) ? trim($raw) : $raw;

                        if ($required && ($val === '' || $val === null)) {
                            $errores[] = "Fila {$r}: el campo '{$slug}' es requerido.";
                            continue 2;
                        }

                        if (($type === 'number' || $type === 'porcentaje') && $val !== '' && $val !== null) {
                            if (!is_numeric($val)) {
                                $errores[] = "Fila {$r}: '{$slug}' debe ser numérico.";
                                continue 2;
                            }
                            $num = (float)$val;

                            if ($min !== null && $min !== '' && $num < (float)$min) {
                                $errores[] = "Fila {$r}: '{$slug}' menor al mínimo ({$min}).";
                                continue 2;
                            }
                            if ($max !== null && $max !== '' && $num > (float)$max) {
                                $errores[] = "Fila {$r}: '{$slug}' mayor al máximo ({$max}).";
                                continue 2;
                            }

                            if ($type === 'number' && floor($num) != $num) {
                                $errores[] = "Fila {$r}: '{$slug}' no acepta decimales.";
                                continue 2;
                            }

                            $camposPayload[$slug] = $num;
                        } else {
                            $camposPayload[$slug] = $val;
                        }

                        $colIndex++;
                    }

                    DetalleCarga::create([
                        'id_carga' => $carga->id_carga,
                        'id_ind' => $indicador->id_ind,
                        'ambito_geo' => $ambito,
                        'id_region' => $id_region,
                        'id_mun' => $id_mun,
                        'fila_det' => $r,
                        'periodo_det' => $carga->periodo,
                        'ejercicio_det' => $carga->ejercicio,
                        'fecha_registro_det' => now()->toDateString(),
                        'fuente_det' => 'Carga por Archivo',
                        'valor_det' => null,
                        'payload_det' => [
                            'origen' => 'plantilla',
                            'campos' => $camposPayload,
                            'archivo' => [
                                'nombre' => $this->archivoNombre,
                                'tipo' => $ext,
                            ],
                        ],
                    ]);
                    $insertados++;
                }
            });

            if (!empty($errores)) {
                session()->flash('error', "Se detectaron errores. Insertados: {$insertados}. Primeros: " . implode(' | ', array_slice($errores, 0, 6)));
                $this->archivoProcesado = false;
                $this->detallesInsertados = $insertados;
                return;
            }

            $this->archivoProcesado = true;
            $this->detallesInsertados = $insertados;
            // =============================
            // ✅ EVIDENCIA: guardar archivo + registrar en anexos
            // =============================
            try {
                $archivo = $this->archivo;

                $original = $archivo->getClientOriginalName();
                $extOriginal = strtolower($archivo->getClientOriginalExtension());
                $peso = $archivo->getSize(); // bytes

                // nombre seguro para storage (evita espacios, colisiones, etc.)
                $nombreSeguro = 'EVIDENCIA_' . ($indicador->id_ind ?? 'ind') . '_'
                    . ($carga->folioUnico_carga ?? 'folio') . '_'
                    . now()->format('Ymd_His') . '_' . Str::random(6) . '.' . $extOriginal;

                $ruta = $archivo->storeAs('anexos/evidencias', $nombreSeguro, 'public');

                // =============================
                // 🧹 Evitar duplicados de evidencia
                // =============================
                Anexo::where('id_form', $this->id_form)
                    ->where('id_ind', $this->id_ind)
                    ->where('tipo_anexo', 'evidencia')
                    ->delete();

                Anexo::create([
                    'nombre_anexo'        => $original,
                    'tipo_anexo'          => 'evidencia',
                    'peso_anexo'          => $peso,
                    'guia_anexo'          => "Evidencia del capturador. Ámbito: {$ambito}. Filas insertadas: {$insertados}.",
                    'fin_proposito_anexo' => 'Evidencia de captura del indicador',
                    'fecha_subida_anexo'  => now(),
                    'ruta_archivo_anexo'  => $ruta,
                    'id_form'             => $this->id_form,
                    'id_ind'              => $this->id_ind,
                ]);
            } catch (\Throwable $e) {
                // no tumba el proceso de datos, solo avisa
                Log::warning('No se pudo guardar evidencia en anexos: ' . $e->getMessage());
            }

            // 🔹 marcar actividad
            if ($this->id_carga_actual) {
                Carga::where('id_carga', $this->id_carga_actual)
                    ->update(['updated_at' => now()]);
            }

            session()->flash('success', "Archivo procesado correctamente. Filas insertadas: {$insertados}");
        } catch (\Throwable $e) {
            session()->flash('error', "No se pudo procesar el archivo: " . $e->getMessage());
            $this->archivoProcesado = false;
        }
    }

    private function colLetra(int $colIndex): string
    {
        $col = '';
        while ($colIndex > 0) {
            $colIndex--;
            $col = chr($colIndex % 26 + 65) . $col;
            $colIndex = intdiv($colIndex, 26);
        }
        return $col;
    }

    private function resetArchivoState()
    {
        $this->archivo = null;
        $this->archivoNombre = '';
        $this->archivoProcesado = false;
        $this->detallesInsertados = 0;
        $this->plantillaDescargada = false;
        // NO borramos id_carga_actual a propósito: si vuelves al método archivo no quieres perder el borrador
    }

    /* =========================
       GUARDAR TODO (manual o archivo)
    ========================== */
    public function guardarTodo()
    {
        if ($this->soloLectura) {
            session()->flash('error', 'Este formulario está finalizado. Solo lectura.');
            return;
        }

        if ($this->guardando) return;
        $this->guardando = true;

        try {
            $this->validate([
                'metodo' => 'required|in:manual,archivo',
                'fuente_dato' => 'nullable|string|max:255',
                'descripcion_env' => 'nullable|string|max:255',
            ]);

            $fuenteDato = trim((string)$this->fuente_dato);
            if ($fuenteDato === '') $fuenteDato = 'N/D';

            $desc = trim((string)$this->descripcion_env);
            if ($desc === '') {
                $desc = $this->metodo === 'manual' ? 'Registro manual' : 'Importación de archivo';
            }

            if ($this->metodo === 'manual') {
                if (empty($this->manualData)) {
                    throw new \Exception('Agrega al menos 1 fila manual.');
                }

                // ✅ debe existir una carga borrador manual (creada al seleccionar método)
                if (!$this->id_carga_actual) {
                    throw new \Exception('No existe carga borrador para manual. Vuelve a seleccionar "Captura Manual".');
                }

                $periodo = now()->format('Y-m');
                $ejercicio = now()->year;

                DB::transaction(function () use ($periodo, $ejercicio, $fuenteDato, $desc) {

                    $carga = Carga::findOrFail($this->id_carga_actual);

                    // ✅ seguridad: debe ser del formulario y método MANUAL
                    if ((int)$carga->id_form !== (int)$this->id_form) {
                        throw new \Exception('La carga borrador no pertenece a este formulario.');
                    }
                    if (strtoupper((string)$carga->metodo_captura) !== 'MANUAL') {
                        throw new \Exception('Esta carga no es de método MANUAL.');
                    }

                    // ✅ reemplaza detalles anteriores del indicador (por si reintenta)
                    DetalleCarga::where('id_carga', $carga->id_carga)
                        ->where('id_ind', $this->id_ind)
                        ->delete();

                    // ✅ inserta detalles desde lo capturado en pantalla
                    foreach (array_values($this->manualData) as $idx => $item) {

                        $ambito = $item['ambito_geo'] ?? 'SIN_AMBITO';

                        DetalleCarga::create([
                            'id_carga' => $carga->id_carga,
                            'id_ind' => $this->id_ind,
                            'ambito_geo' => $ambito,
                            'id_region' => $item['id_region'] ?? null,
                            'id_mun' => $item['id_mun'] ?? null,
                            'periodo_det' => $periodo,
                            'ejercicio_det' => $ejercicio,
                            'fila_det' => $idx + 1,
                            'fecha_registro_det' => now()->toDateString(),
                            'fuente_det' => $fuenteDato,
                            'valor_det' => null,
                            'payload_det' => $item['payload_det'] ?? [],
                        ]);
                    }

                    // ✅ ahora sí finaliza la MISMA carga
                    $carga->update([
                        'fecha_carga' => now(),
                        'periodo' => $periodo,
                        'ejercicio' => $ejercicio,
                        'fuente' => $fuenteDato,
                        'descripcion_env' => $desc,
                        'ambito_geo_carga' => $this->ambito_geo,
                        'status_env' => 'ENVIADO',
                        'observacion_env' => '',
                    ]);
                });

                // limpiar manual
                $this->manualData = [];
                $this->editManualIndex = null;
                foreach ($this->schema as $c) $this->manualCampos[$c['slug']] = null;
                $this->region = '';
                $this->municipio = '';

                $cargaFinal = Carga::find($this->id_carga_actual);

                $this->dispatch(
                    'swal-enviado',
                    indicador: (string)($this->indicador->nombre_ind ?? 'Indicador'),
                    folio: (string)($cargaFinal->folioUnico_carga ?? ''),
                    url: url('/usuario/dashboard')
                );
                $this->guardando = false;
                return;
            }

            // ARCHIVO: solo “finaliza” la carga borrador
            if (!$this->id_carga_actual) {
                throw new \Exception('No existe carga borrador para archivo.');
            }
            if (!$this->plantillaDescargada) {
                $this->refrescarEstadoPlantilla();
                if (!$this->plantillaDescargada) throw new \Exception('Primero descarga la plantilla.');
            }
            if (!$this->archivoProcesado) {
                throw new \Exception('Primero sube y procesa el archivo.');
            }

            // valida que sí hay detalles
            $count = DetalleCarga::where('id_carga', $this->id_carga_actual)->where('id_ind', $this->id_ind)->count();
            if ($count <= 0) {
                throw new \Exception('No hay detalles insertados. Vuelve a procesar el archivo.');
            }

            Carga::where('id_carga', $this->id_carga_actual)->update([
                'fuente' => $fuenteDato,
                'descripcion_env' => $desc,
                'status_env' => 'ENVIADO',
                'ambito_geo_carga' => $this->ambito_geo,
                'metodo_captura' => 'ARCHIVO',
                'fecha_carga' => now(),
            ]);

            $cargaFinal = Carga::find($this->id_carga_actual);

            $this->dispatch(
                'swal-enviado',
                indicador: (string)($this->indicador->nombre_ind ?? 'Indicador'),
                folio: (string)($cargaFinal->folioUnico_carga ?? ''),
                url: url('/usuario/dashboard')
            );
            $this->guardando = false;
            return;
        } catch (\Throwable $e) {
            session()->flash('error', 'Error: ' . $e->getMessage());
        } finally {
            $this->guardando = false;
        }
    }

    public function badgeClass(string $status): string
    {
        $s = mb_strtoupper(trim($status));
        $s = str_replace('REVISIÓN', 'REVISION', $s);

        return match ($s) {
            'BORRADOR' => 'secondary',
            'ENVIADO', 'REENVIADO' => 'info',
            'EN REVISION' => 'primary',
            'OBSERVADO' => 'warning',
            'APROBADO' => 'success',
            default => 'light',
        };
    }

    public function render()
    {
        $this->cargaActual = $this->id_carga_actual ? Carga::find($this->id_carga_actual) : null;
        return view('livewire.usuarios.formulario-captura', [
            'regiones' => $this->regiones,
            'municipiosFiltrados' => $this->municipiosFiltrados,
            'manualData' => $this->manualData,
            'metodo' => $this->metodo,
            'ambito_geo' => $this->ambito_geo,
            'regionFiltro' => $this->regionFiltro,
            'archivoNombre' => $this->archivoNombre,
            'soloLectura' => $this->soloLectura,
            'formulario' => $this->formulario,
            'indicador' => $this->indicador,
            'schema' => $this->schema,
            'manualCampos' => $this->manualCampos,
            'plantillaDescargada' => $this->plantillaDescargada,
            'archivoProcesado' => $this->archivoProcesado,
            'detallesInsertados' => $this->detallesInsertados,
            'hayPlantilla' => $this->hayPlantilla,
            'modoCorreccion' => $this->modoCorreccion,
            'metodoBloqueado' => $this->metodoBloqueado,
            'mensajeObservacion' => $this->mensajeObservacion,
            'id_carga_actual' => $this->id_carga_actual,
            'cargaActual' => $this->cargaActual,
        ])->extends('layouts.usuario')->section('content');
    }
}
