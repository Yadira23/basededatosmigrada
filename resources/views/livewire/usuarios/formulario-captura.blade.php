<div class="formulario-root">
    <div class="alert alert-info" style="margin-bottom:15px;">
        <strong>Formulario:</strong> {{ $this->formulario->titulo_form ?? '#' . $id_form }} <br>
        <strong>Indicador:</strong> {{ $this->indicador->nombre_ind ?? 'ID ' . $id_ind }} <br>
        <strong>Periodo:</strong> {{ $this->indicador->periodo_ind ?? '—' }} |
        <strong>Unidad:</strong> {{ $this->indicador->unidadmedida_ind ?? '—' }} <br>
        <strong>Fuente:</strong> {{ $this->indicador->fuenteverificacion_ind ?? '—' }}
    </div>

    {{-- 🚨 MODO CORRECCIÓN --}}
    @if ($modoCorreccion)
        <div class="alert alert-warning" style="margin-bottom:15px;">
            <strong>Modo corrección:</strong><br>
            <strong>Observación:</strong> {{ $mensajeObservacion ?? '—' }}
        </div>
    @endif

    @if ($soloLectura)
        <div class="alert alert-warning" style="margin-bottom:15px;">
            <strong>Captura bloqueada:</strong>
            {{ $mensajeBloqueo ?? 'No puedes capturar en este momento.' }}

            @if (!empty($periodoLabel) || !empty($capturaOpenAt) || !empty($capturaCloseAt))
                <div class="small text-muted mt-2">
                    @if (!empty($periodoLabel))
                        <div><b>Periodo:</b> {{ $periodoLabel }}</div>
                    @endif
                    @if (!empty($capturaOpenAt))
                        <div><b>Abre:</b> {{ $capturaOpenAt }}</div>
                    @endif
                    @if (!empty($capturaCloseAt))
                        <div><b>Cierra:</b> {{ $capturaCloseAt }}</div>
                    @endif
                </div>
            @endif
        </div>
    @else
        @if (!is_null($diasRestantes))
            <div class="alert alert-info" style="margin-bottom:15px;">
                Tienes <b>{{ $diasRestantes }}</b> día(s) para capturar.
                <span class="small text-muted">Cierra el {{ $capturaCloseAt ?? '—' }}.</span>
            </div>
        @endif
    @endif

    {{-- ✅ META (solo si hay metas) --}}
    @if (!empty($metasDisponibles) && count($metasDisponibles) > 0)

        @php
            $metaSel = collect($metasDisponibles)->first(function ($m) use ($id_meta) {
                $mid = $m['id'] ?? ($m['id_meta'] ?? null);
                return (int) $mid === (int) $id_meta;
            });
        @endphp

        <div class="mb-3">

            {{-- ✅ Si ya viene seleccionada (por URL o por selección previa), NO mostrar select --}}
            @if ($id_meta && $metaSel)
                <div class="p-2 rounded" style="background:#eef3ff;">
                    <div class="text-muted small">Meta (parcial)</div>
                    <div class="fw-semibold" style="font-size:1.05rem;">
                        {{ $metaSel['orden'] ?? '' }}. {{ $metaSel['titulo'] ?? 'Meta' }}
                    </div>
                    <div class="small text-muted">
                        Cada meta es un parcial distinto.
                    </div>
                </div>
            @else
                {{-- ✅ Si NO hay meta seleccionada, ahí sí mostrar select --}}
                <select class="form-select" wire:model="id_meta" @disabled($metaBloqueada)>
                    <option value="">— Selecciona una meta —</option>
                    @foreach ($metasDisponibles as $m)
                        <option value="{{ $m['id'] }}">
                            {{ $m['orden'] ?? '' }}. {{ $m['titulo'] ?? 'Meta' }}
                        </option>
                    @endforeach
                </select>

                <div class="small text-muted mt-1">
                    Cada meta es un parcial distinto. Selecciona la meta antes de capturar.
                </div>
            @endif
        </div>

    @endif

    <h2>Selecciona el método de captura</h2>

    @if ($cargaActual)
        @php
            $st = mb_strtoupper(trim((string) ($cargaActual->status_env ?? '')));
            $st = str_replace('REVISIÓN', 'REVISION', $st);
        @endphp

        <div class="alert alert-light border d-flex justify-content-between align-items-center">
            <div>
                <div class="font-weight-bold">
                    Folio: {{ $cargaActual->folioUnico_carga ?? $cargaActual->id_carga }}
                    <span class="badge badge-light ml-2">
                        {{ $cargaActual->status_env ?? '—' }}
                    </span>
                </div>
                <div class="text-muted small">
                    Creado: {{ optional($cargaActual->created_at)->format('d/m/Y H:i') }}
                    · Última actualización: {{ optional($cargaActual->updated_at)->format('d/m/Y H:i') }}
                </div>
            </div>

            @if (!$soloLectura && !$modoCorreccion && $st === 'BORRADOR')
                <button type="button" class="btn btn-sm btn-outline-danger" wire:click="reiniciarCaptura">
                    Reiniciar
                </button>
            @endif
        </div>
    @endif

    {{-- MÉTODOS DE CAPTURA --}}
    <div class="metodos">

        {{-- MANUAL --}}
        <div id="manual"
            class="metodo-card metodo-manual
        {{ $metodo === 'manual' ? 'selected' : '' }}
        {{ $soloLectura || ($metodo && $metodo !== 'manual') ? 'disabled-card' : '' }}"
            @if (!$soloLectura && (!$metodo || $metodo === 'manual')) wire:click="seleccionar('manual')" @endif>

            <div class="metodo-badge">Manual</div>
            <h3>Captura Manual</h3>
            <p>Ingresa los valores manualmente (dinámico según el indicador).</p>
        </div>

        {{-- ARCHIVO --}}
        <div id="archivo"
            class="metodo-card metodo-archivo
        {{ $metodo === 'archivo' ? 'selected' : '' }}
        {{ $soloLectura || ($metodo && $metodo !== 'archivo') ? 'disabled-card' : '' }}"
            @if (!$soloLectura && (!$metodo || $metodo === 'archivo')) wire:click="seleccionar('archivo')" @endif>

            <div class="metodo-badge">Archivo</div>
            <h3>Subir Archivo</h3>
            <p>Descarga la plantilla del indicador, llena y vuelve a subirla.</p>
        </div>

    </div>

    @if (!$soloLectura && !$modoCorreccion && $metodo)
        <div class="mt-2">
            <button type="button" class="btn btn-sm btn-outline-danger" wire:click="reiniciarCaptura">
                Reiniciar captura
            </button>
            <small class="text-muted d-block mt-1">
                Solo disponible si aún no has avanzado (sin plantilla descargada, sin archivo procesado, sin filas
                capturadas).
            </small>
        </div>
    @endif

    {{-- INFORMACIÓN --}}
    <div class="info">
        <p>
            Método seleccionado:
            <span id="metodo">
                {{ $metodo === 'manual' ? 'Captura Manual' : ($metodo === 'archivo' ? 'Subir Archivo' : 'No seleccionado') }}
            </span>
        </p>

        <p id="archivoNombre">
            @if (!empty($archivoNombre))
                Archivo seleccionado: {{ $archivoNombre }}
            @endif
        </p>

        <p>Última actualización: <span id="fechaHora"></span></p>
    </div>

    {{-- MENSAJES --}}
    @if (session()->has('success'))
        <div style="margin-bottom:12px; padding:10px; border:1px solid #c3e6cb; background:#d4edda;">
            {{ session('success') }}
        </div>
    @endif

    @if (session()->has('error'))
        <div style="margin-bottom:12px; padding:10px; border:1px solid #f5c6cb; background:#f8d7da;">
            {{ session('error') }}
        </div>
    @endif

    {{-- =========================
         ✅ FORMULARIO MANUAL DINÁMICO
       ========================= --}}
    <div id="manualForm" @class(['manual-form', 'd-none' => $metodo !== 'manual'])>
        <h3>Captura Manual</h3>

        <fieldset @disabled($soloLectura) style="border:0; padding:0; margin:0;">
            {{-- ÁMBITO --}}
            <div style="margin-bottom:10px;">
                <label>Capturar por:</label>
                <select wire:model.live="ambito_geo">
                    <option value="SIN_AMBITO">Estatal (sin región/municipio)</option>
                    <option value="REGION">Región</option>
                    <option value="MUNICIPIO">Municipio</option>
                </select>
            </div>

            <form id="formManual" wire:submit.prevent="agregarManual">
                {{-- REGIÓN --}}
                @if ($ambito_geo === 'REGION')
                    <div style="flex:1 1 100%; margin-bottom:10px;">
                        <label>Región:</label>
                        <select wire:model.live="region">
                            <option value="">Selecciona una región</option>

                            @foreach ($regiones as $r)
                                @php
                                    $regionUsada = collect($manualData)->contains(function ($row) use ($r) {
                                        return ($row['ambito_geo'] ?? '') === 'REGION' &&
                                            (int) ($row['id_region'] ?? 0) === (int) $r->id_region;
                                    });
                                @endphp

                                <option value="{{ $r->id_region }}" @disabled($regionUsada)>
                                    {{ $r->nombre_region }} @if ($regionUsada)
                                        (ya agregado)
                                    @endif
                                </option>
                            @endforeach
                        </select>
                    </div>
                @endif

                {{-- MUNICIPIO --}}
                @if ($ambito_geo === 'MUNICIPIO')
                    <div style="flex:1 1 100%; margin-bottom:10px;">
                        <label>Región (para filtrar municipios):</label>
                        <select wire:model.live="regionFiltro">
                            <option value="">Selecciona una región</option>
                            @foreach ($regiones as $r)
                                <option value="{{ $r->id_region }}">{{ $r->nombre_region }}</option>
                            @endforeach
                        </select>

                        <label style="margin-top:8px; display:block;">Municipio:</label>
                        <select wire:model.live="municipio" @disabled(empty($regionFiltro))>
                            <option value="">Selecciona un municipio</option>

                            @foreach ($municipiosFiltrados as $m)
                                @php
                                    $munUsado = collect($manualData)->contains(function ($row) use ($m) {
                                        return ($row['ambito_geo'] ?? '') === 'MUNICIPIO' &&
                                            (int) ($row['id_mun'] ?? 0) === (int) $m->id_mun;
                                    });
                                @endphp

                                <option value="{{ $m->id_mun }}" @disabled($munUsado)>
                                    {{ $m->nombre_municipio }} @if ($munUsado)
                                        (ya agregado)
                                    @endif
                                </option>
                            @endforeach
                        </select>
                    </div>
                @endif

                {{-- CAMPOS DINÁMICOS --}}
                <div style="flex:1 1 100%; margin-bottom:10px;">
                    <h4 style="margin:10px 0;">Campos del indicador</h4>

                    @if (empty($schema))
                        <div class="alert alert-warning">
                            Este indicador aún no tiene campos a capturar.
                        @else
                            @foreach ($schema as $campo)
                                @php
                                    $slug = $campo['slug'] ?? '';
                                    $label = $campo['label'] ?? $slug;
                                    $required = !empty($campo['required']);
                                    $step = ($campo['type'] ?? '') === 'porcentaje' ? 0.01 : 1;
                                @endphp

                                <div style="margin-bottom:10px;">
                                    <label>
                                        {{ $label }}
                                        @if ($required)
                                            <span style="color:red;">*</span>
                                        @endif
                                    </label>

                                    <input type="number" wire:model.live="manualCampos.{{ $slug }}"
                                        step="{{ $step }}"
                                        @if (isset($campo['min'])) min="{{ $campo['min'] }}" @endif
                                        @if (isset($campo['max'])) max="{{ $campo['max'] }}" @endif>
                                </div>
                            @endforeach
                    @endif
                </div>

                <button type="submit">
                    {{ isset($editManualIndex) && $editManualIndex !== null ? 'Guardar edición' : 'Agregar fila' }}
                </button>
            </form>

            {{-- TABLA MANUAL --}}
            <h4>Filas capturadas:</h4>

            <table id="tablaManual">
                <thead>
                    <tr>
                        <th>
                            @if ($ambito_geo === 'MUNICIPIO')
                                Municipio
                            @elseif($ambito_geo === 'REGION')
                                Región
                            @else
                                Estatal
                            @endif
                        </th>

                        @foreach ($schema as $campo)
                            <th>{{ $campo['label'] ?? ($campo['slug'] ?? '') }}</th>
                        @endforeach

                        <th>Acciones</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($manualData as $i => $row)
                        @php $camposRow = $row['payload_det']['campos'] ?? []; @endphp
                        <tr>
                            <td>{{ $row['nombre'] ?? '—' }}</td>

                            @foreach ($schema as $campo)
                                @php $slug = $campo['slug'] ?? ''; @endphp
                                <td>{{ $camposRow[$slug] ?? '' }}</td>
                            @endforeach

                            <td>
                                <button type="button" wire:click="editarManual({{ $i }})">Editar</button>
                                <button type="button"
                                    wire:click="eliminarManual({{ $i }})">Eliminar</button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ 2 + count($schema) }}">Aún no hay registros.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            <button type="button" wire:click="guardarTodo" class="btn btn-primary" @disabled($soloLectura || $guardando || $modoCorreccion)
                wire:loading.attr="disabled" wire:target="guardarTodo">
                <span wire:loading.remove wire:target="guardarTodo">Enviar</span>
                <span wire:loading wire:target="guardarTodo">Guardando...</span>
            </button>
        </fieldset>
    </div>

    {{-- =========================
         ✅ ARCHIVO (plantilla + subir + procesar + enviar)
       ========================= --}}
    <div id="tablaArchivoContainer" @class(['archivo-form', 'd-none' => $metodo !== 'archivo'])>
        @if ($metodo === 'archivo')

            @if (!$this->ambitoElegido)
                <div class="alert alert-warning mb-2">
                    <strong>Selecciona el ámbito a capturar.</strong>
                </div>
            @endif

            {{-- Selector de ámbito (si decides que el usuario pueda elegirlo) --}}
            <div class="mb-2">
                <label class="form-label"><strong>Ámbito</strong></label><br>

                <label>
                    <input type="radio" wire:model.live="ambito_geo" value="SIN_AMBITO" @disabled($soloLectura)>
                    ESTATAL
                </label>

                <label class="ms-3">
                    <input type="radio" wire:model.live="ambito_geo" value="REGION" @disabled($soloLectura)>
                    REGION
                </label>

                <label class="ms-3">
                    <input type="radio" wire:model.live="ambito_geo" value="MUNICIPIO"
                        @disabled($soloLectura)>
                    MUNICIPIO
                </label>
            </div>

            @if (!empty($motivoResetPlantilla))
                <div class="alert alert-warning mb-2">{{ $motivoResetPlantilla }}</div>
            @endif

            {{-- ✅ Plantilla --}}
            <div class="mb-2">
                <button type="button" class="btn btn-outline-success" wire:click="descargarPlantilla"
                    @disabled($soloLectura)>
                    Descargar plantilla
                </button>

                <div class="mt-2">
                    <strong>Plantilla descargada:</strong>
                    @if ($plantillaDescargada)
                        <span class="text-success">SÍ ✅</span>
                    @else
                        <span class="text-danger">NO ❌</span>
                    @endif
                </div>

                @if (!$plantillaDescargada)
                    <div class="alert alert-warning mt-2 mb-0">
                        Debes descargar la plantilla antes de poder subir tu archivo.
                    </div>
                @endif
            </div>

            {{-- ✅ Archivo --}}
            <div class="mb-2">
                <input type="file" class="form-control" wire:model.live="archivo" accept=".xlsx,.xls,.csv"
                    @disabled($soloLectura || !$plantillaDescargada)>

                @error('archivo')
                    <div class="text-danger mt-1">{{ $message }}</div>
                @enderror

                @if (!empty($archivoNombre))
                    <div class="alert alert-secondary mt-2 mb-0">
                        Archivo seleccionado: <strong>{{ $archivoNombre }}</strong>
                    </div>
                @else
                    <small class="text-muted d-block mt-1">Ningún archivo seleccionado</small>
                @endif
            </div>

            {{-- ✅ Procesar --}}
            <div class="mb-2">
                <button type="button" class="btn btn-primary" wire:click="procesarArchivo"
                    @disabled($soloLectura || !$plantillaDescargada || !$archivo)">
                    Procesar archivo
                </button>

                <div class="mt-2">
                    <strong>Archivo procesado:</strong>
                    @if ($archivoProcesado)
                        <span class="text-success">SÍ ✅</span>
                    @else
                        <span class="text-danger">NO ❌</span>
                    @endif
                    <div><strong>Filas insertadas:</strong> {{ $detallesInsertados }}</div>
                </div>
            </div>

            {{-- ✅ Enviar --}}
            <div>
                <button type="button" wire:click="guardarTodo" class="btn btn-success" @disabled($soloLectura || $guardando || !$archivoProcesado || $modoCorreccion)
                    wire:loading.attr="disabled" wire:target="guardarTodo">
                    <span wire:loading.remove wire:target="guardarTodo">Enviar</span>
                    <span wire:loading wire:target="guardarTodo">Enviando...</span>
                </button>

                @if (!$archivoProcesado)
                    <small class="text-muted d-block mt-1">
                        Primero debes subir y procesar el archivo para poder enviar.
                    </small>
                @endif
            </div>

        @endif
    </div>
    {{-- =========================
     🔁 REENVIAR CORRECCIÓN
   ========================= --}}
    @if ($modoCorreccion)
        <div class="mt-4">
            <button type="button" class="btn btn-primary" wire:click="reenviarCorreccion"
                wire:loading.attr="disabled">
                🔁 Reenviar corrección
            </button>

            <small class="text-muted d-block mt-1">
                Al reenviar se reemplazan los datos anteriores de esta carga.
            </small>
        </div>
    @endif

</div>

@push('styles')
    <style>
        .d-none {
            display: none !important;
        }

        .formulario-root {
            font-family: Arial, sans-serif;
            margin: 20px auto;
            max-width: 800px;
        }

        h2 {
            text-align: center;
            margin-bottom: 25px;
            color: #333;
        }

        .metodos {
            display: flex;
            gap: 20px;
            justify-content: center;
            margin-bottom: 20px;
            flex-wrap: wrap;
        }

        /* Tarjeta base */
        .metodo-card {
            position: relative;
            border: 2px solid #cbd5e1;
            border-radius: 10px;
            padding: 16px 18px;
            width: 280px;
            cursor: pointer;
            transition: transform .15s ease, box-shadow .15s ease, border-color .15s ease;
            background: #ffffff;
        }

        /* Badge */
        .metodo-badge {
            position: absolute;
            top: 12px;
            right: 12px;
            font-size: 12px;
            font-weight: 700;
            padding: 4px 10px;
            border-radius: 999px;
            border: 1px solid rgba(0, 0, 0, .08);
        }

        /* Diferenciación por color */
        .metodo-manual {
            border-left: 8px solid #2563eb;
            background: #eef5ff;
        }

        .metodo-manual .metodo-badge {
            background: #dbeafe;
            color: #1d4ed8;
        }

        .metodo-archivo {
            border-left: 8px solid #16a34a;
            background: #edfdf5;
        }

        .metodo-archivo .metodo-badge {
            background: #dcfce7;
            color: #15803d;
        }

        /* Hover */
        .metodo-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 18px rgba(0, 0, 0, .08);
            border-color: #94a3b8;
        }

        /* Seleccionado */
        .metodo-card.selected {
            outline: 3px solid rgba(0, 0, 0, .08);
            border-color: rgba(0, 0, 0, .12);
        }

        /* Deshabilitado (ya lo tienes, lo dejamos) */
        .disabled-card {
            opacity: 0.6;
            cursor: not-allowed;
            pointer-events: none;
        }

        .info {
            text-align: center;
            margin-bottom: 20px;
            font-size: 14px;
            color: #555;
        }

        .manual-form {
            border: 1px solid #ccc;
            padding: 15px 20px;
            border-radius: 8px;
            background-color: #fafafa;
            margin-bottom: 20px;
        }

        .manual-form h3 {
            margin-top: 0;
        }

        .manual-form form {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-bottom: 15px;
        }

        .manual-form label {
            flex: 1 1 100px;
            margin-top: 5px;
        }

        .manual-form input {
            flex: 1 1 200px;
            padding: 5px;
            border-radius: 4px;
            border: 1px solid #ccc;
        }

        .manual-form button {
            padding: 6px 15px;
            border: none;
            border-radius: 4px;
            background-color: #3c763d;
            color: white;
            cursor: pointer;
            transition: 0.2s;
        }

        .manual-form button:hover {
            background-color: #2e5e2e;
        }

        .disabled-card {
            opacity: 0.6;
            cursor: not-allowed;
            pointer-events: none;
        }

        table {
            border-collapse: collapse;
            width: 100%;
        }

        th,
        td {
            border: 1px solid #ccc;
            padding: 6px 10px;
            text-align: left;
        }

        th {
            background-color: #eee;
        }

        .archivo-form {
            border: 1px solid #ccc;
            padding: 15px 20px;
            border-radius: 8px;
            background-color: #f9f9f9;
            margin-bottom: 20px;
        }

        /* ✅ Resaltado fuerte del seleccionado por tipo */
        .metodo-manual.selected {
            border-color: #2563eb;
            box-shadow: 0 12px 22px rgba(37, 99, 235, .20);
        }

        .metodo-archivo.selected {
            border-color: #16a34a;
            box-shadow: 0 12px 22px rgba(22, 163, 74, .20);
        }

        /* ✅ “check” visual en la esquina cuando está seleccionado */
        .metodo-card.selected::after {
            content: "✓";
            position: absolute;
            left: 12px;
            top: 10px;
            width: 26px;
            height: 26px;
            display: grid;
            place-items: center;
            border-radius: 999px;
            font-weight: 900;
            background: rgba(0, 0, 0, .08);
        }

        .metodo-manual.selected::after {
            background: #2563eb;
            color: #fff;
        }

        .metodo-archivo.selected::after {
            background: #16a34a;
            color: #fff;
        }

        /* ✅ Deshabilitado más claro (no solo opacidad) */
        .metodo-card.disabled-card {
            opacity: 0.55;
            filter: grayscale(25%);
        }

        /* ✅ Mensaje visual de bloqueo */
        .metodo-card.disabled-card::before {
            content: "Bloqueado";
            position: absolute;
            bottom: 12px;
            right: 12px;
            font-size: 12px;
            font-weight: 700;
            padding: 4px 10px;
            border-radius: 999px;
            background: rgba(0, 0, 0, .06);
            color: rgba(0, 0, 0, .55);
        }
    </style>
@endpush

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            function actualizarFechaHora() {
                const ahora = new Date();
                const dia = String(ahora.getDate()).padStart(2, '0');
                const mes = String(ahora.getMonth() + 1).padStart(2, '0');
                const anio = ahora.getFullYear();
                const horas = String(ahora.getHours()).padStart(2, '0');
                const minutos = String(ahora.getMinutes()).padStart(2, '0');
                const span = document.getElementById('fechaHora');
                if (span) span.innerText = `${dia}/${mes}/${anio} ${horas}:${minutos}`;
            }
            actualizarFechaHora();
            setInterval(actualizarFechaHora, 60000);
        });

        document.addEventListener('livewire:init', () => {
            Livewire.on('swal-enviado', (data) => {
                const payload = Array.isArray(data) ? (data[0] || {}) : (data || {});

                Swal.fire({
                    icon: 'success',
                    title: 'Envío realizado',
                    html: `
        <div style="text-align:left">
          <div><b>Indicador:</b> ${payload.indicador ?? '—'}</div>
          <div><b>Folio:</b> ${payload.folio ?? '—'}</div>
        </div>
      `,
                    confirmButtonText: 'Ir a mi panel'
                }).then(() => {
                    window.location.href = payload.url ?? '/usuario/dashboard';
                });
            });
        });
    </script>
@endpush
