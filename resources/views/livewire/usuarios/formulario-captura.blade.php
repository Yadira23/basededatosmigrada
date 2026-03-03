<div class="formulario-root">
    <div class="cap-topcard">
        <div class="cap-topcard-main">
            <div class="cap-topcard-kicker">Formulario</div>
            <div class="cap-topcard-title">
                {{ $this->formulario->titulo_form ?? '#' . $id_form }}
            </div>
        </div>

        <div class="cap-topcard-grid">
            <div class="cap-topcard-item">
                <div class="cap-topcard-label">Indicador</div>
                <div class="cap-topcard-value">{{ $this->indicador->nombre_ind ?? 'ID ' . $id_ind }}</div>
            </div>

            <div class="cap-topcard-item">
                <div class="cap-topcard-label">Periodo</div>
                <div class="cap-topcard-value">{{ $this->indicador->periodo_ind ?? '—' }}</div>
            </div>

            <div class="cap-topcard-item">
                <div class="cap-topcard-label">Unidad</div>
                <div class="cap-topcard-value">{{ $this->indicador->unidadmedida_ind ?? '—' }}</div>
            </div>

            <div class="cap-topcard-item">
                <div class="cap-topcard-label">Fuente</div>
                <div class="cap-topcard-value">{{ $this->indicador->fuenteverificacion_ind ?? '—' }}</div>
            </div>
        </div>
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
            $metaSel = collect($metasDisponibles)->first(function ($m) use ($meta_id) {
                $mid = $m['id'] ?? ($m['meta_id'] ?? null);
                return (int) $mid === (int) $meta_id;
            });
        @endphp

        <div class="mb-3">

            {{-- ✅ Si ya viene seleccionada (por URL o por selección previa), NO mostrar select --}}
            @if ($meta_id && $metaSel)
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
                <select class="form-select" wire:model="meta_id" @disabled($metaBloqueada)>
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
            <p>Ingresa los valores manualmente (Según el indicador).</p>
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
    </div>

    @if ($cargaActual)
        @php
            $st = mb_strtoupper(trim((string) ($cargaActual->status_env ?? '')));
            $st = str_replace('REVISIÓN', 'REVISION', $st);
        @endphp

        <div class="cap-folio-card">
            <div class="cap-folio-left">
                <div class="cap-folio-title">
                    Folio: <span
                        class="cap-folio-value">{{ $cargaActual->folioUnico_carga ?? $cargaActual->id_carga }}</span>

                    <span class="cap-status">
                        {{ $cargaActual->status_env ?? '—' }}
                    </span>
                </div>

                <div class="cap-folio-sub">
                    <span><b>Creado:</b> {{ optional($cargaActual->created_at)->format('d/m/Y H:i') }}</span>
                    <span class="cap-dot">•</span>
                    <span><b>Última actualización:</b>
                        {{ optional($cargaActual->updated_at)->format('d/m/Y H:i') }}</span>
                </div>
            </div>

            @if (!$soloLectura && !$modoCorreccion && $st === 'BORRADOR' && $metodo)
                <div class="cap-folio-actions">
                    <button type="button" class="cap-btn-danger" wire:click="reiniciarCaptura">
                        Reiniciar captura
                    </button>

                    <div class="cap-reset-help">
                        Vuelve al paso inicial y te deja elegir el método.
                        <b>Solo si aún no has avanzado.</b>
                    </div>
                </div>
            @endif
        </div>
    @endif

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

    @if (isset($editManualIndex) && $editManualIndex !== null)
        <div class="cap-edit-mode">
            ✏️ Estás editando una fila. Al guardar se reemplazará el valor anterior.
        </div>
    @endif

    {{-- =========================
         ✅ FORMULARIO MANUAL DINÁMICO
       ========================= --}}
    <div id="manualForm" @class(['manual-form', 'd-none' => $metodo !== 'manual'])>
        <div class="cap-section-head">
            <div>
                <div class="cap-section-title">Captura Manual</div>
                <div class="cap-section-sub">Ingresa los valores y agrega filas antes de enviar.</div>
            </div>
        </div>

        <div class="cap-grid">
            <div class="cap-left">

                <fieldset @disabled($soloLectura) style="border:0; padding:0; margin:0;">
                    {{-- ÁMBITO --}}
                    {{-- ÁMBITO --}}
                    <div class="cap-field">
                        <label class="cap-label">Capturar por:</label>

                        <div class="cap-ambito-grid">
                            <label class="cap-ambito-pill">
                                <input class="cap-ambito-radio" type="radio" wire:model.live="ambito_geo"
                                    value="SIN_AMBITO">
                                <span class="cap-ambito-text">ESTATAL</span>
                            </label>

                            <label class="cap-ambito-pill">
                                <input class="cap-ambito-radio" type="radio" wire:model.live="ambito_geo"
                                    value="REGION">
                                <span class="cap-ambito-text">REGIÓN</span>
                            </label>

                            <label class="cap-ambito-pill">
                                <input class="cap-ambito-radio" type="radio" wire:model.live="ambito_geo"
                                    value="MUNICIPIO">
                                <span class="cap-ambito-text">MUNICIPIO</span>
                            </label>
                        </div>
                    </div>

                    <form id="formManual" wire:submit.prevent="agregarManual">
                        {{-- REGIÓN --}}
                        @if ($ambito_geo === 'REGION')
                            <div style="flex:1 1 100%; margin-bottom:10px;">
                                <label class="cap-label">Región</label>
                                <select class="cap-input" wire:model.live="region">
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
                                                (Agregado)
                                            @endif
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        @endif

                        {{-- MUNICIPIO --}}
                        @if ($ambito_geo === 'MUNICIPIO')
                            <div style="flex:1 1 100%; margin-bottom:10px;">
                                <label class="cap-label">Región (para filtrar municipios)</label>
                                <select class="cap-input" wire:model.live="regionFiltro">
                                    <option value="">Selecciona una región</option>
                                    @foreach ($regiones as $r)
                                        <option value="{{ $r->id_region }}">{{ $r->nombre_region }}</option>
                                    @endforeach
                                </select>

                                <label class="cap-label" style="margin-top:10px;">Municipio</label>
                                <select class="cap-input" wire:model.live="municipio" @disabled(empty($regionFiltro))>
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
                                                (agregado)
                                            @endif
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        @endif

                        {{-- CAMPOS DINÁMICOS --}}
                        <div style="flex:1 1 100%; margin-bottom:10px;">
                            <div class="cap-card">
                                <h4 class="cap-card-title">Campos del indicador</h4>

                                @if (empty($schema))
                                    <div class="alert alert-warning">
                                        Este indicador aún no tiene campos a capturar.
                                    @else
                                        <div class="cap-fields-grid">
                                            @foreach ($schema as $campo)
                                                @php
                                                    $slug = $campo['slug'] ?? '';
                                                    $label = $campo['label'] ?? $slug;
                                                    $required = !empty($campo['required']);
                                                    $step = ($campo['type'] ?? '') === 'porcentaje' ? 0.01 : 1;
                                                @endphp

                                                <div class="cap-field">
                                                    <label class="cap-label">
                                                        {{ $label }}
                                                        @if ($required)
                                                            <span class="cap-req">*</span>
                                                        @endif
                                                    </label>

                                                    <input class="cap-input" type="number"
                                                        wire:model.live="manualCampos.{{ $slug }}"
                                                        step="{{ $step }}"
                                                        @if (isset($campo['min'])) min="{{ $campo['min'] }}" @endif
                                                        @if (isset($campo['max'])) max="{{ $campo['max'] }}" @endif>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif
                            </div>

                            <button type="submit"
                                class="cap-btn {{ isset($editManualIndex) && $editManualIndex !== null ? 'cap-btn-warning' : 'cap-btn-primary' }}">
                                {{ isset($editManualIndex) && $editManualIndex !== null ? 'Guardar edición' : 'Agregar fila' }}
                            </button>
                    </form>

            </div>
            <div class="cap-right">

                {{-- TABLA MANUAL --}}
                <div class="cap-card">
                    <h4 class="cap-card-title">
                        Filas capturadas ({{ count($manualData ?? []) }})
                    </h4>

                    <div class="cap-table-wrap">
                        <table id="tablaManual" class="cap-table">
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
                                            <div class="cap-row-actions">
                                                <button type="button" class="cap-action cap-action-edit"
                                                    wire:click="editarManual({{ $i }})">
                                                    ✏️ Editar
                                                </button>

                                                <button type="button" class="cap-action cap-action-del"
                                                    wire:click="eliminarManual({{ $i }})">
                                                    🗑️ Eliminar
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="{{ 2 + count($schema) }}">
                                            <div class="cap-empty">
                                                <div class="cap-empty-title">Aún no hay filas capturadas</div>
                                                <div class="cap-empty-sub">
                                                    Completa los campos y usa <b>“Agregar fila”</b> para ir armando tu
                                                    captura antes de enviar.
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="cap-sticky">
                        <div class="cap-sticky-info">
                            Estado:
                            <b>{{ $cargaActual->status_env ?? '—' }}</b>
                            · Filas:
                            <b>{{ count($manualData ?? []) }}</b>
                        </div>

                        <div class="cap-sticky-actions">
                            <button type="button" class="cap-btn cap-btn-primary" wire:click="guardarTodo"
                                @disabled($soloLectura || $guardando || $modoCorreccion) wire:loading.attr="disabled" wire:target="guardarTodo">

                                <span wire:loading.remove wire:target="guardarTodo">
                                    Enviar
                                </span>

                                <span wire:loading wire:target="guardarTodo">
                                    Guardando...
                                </span>
                            </button>
                        </div>
                    </div>
                    </fieldset>
                </div>
            </div>
        </div>
    </div>

    {{-- =========================
         ✅ ARCHIVO (plantilla + subir + procesar + enviar)
       ========================= --}}
    <div id="tablaArchivoContainer" @class(['archivo-form', 'd-none' => $metodo !== 'archivo'])>

        <div class="cap-archivo-card">
            <div class="cap-archivo-head">
                <div>
                    <div class="cap-archivo-title">Captura por archivo</div>
                    <div class="cap-archivo-sub">Descarga la plantilla, súbela llena y procesa para enviar.</div>
                </div>
            </div>
            @if ($metodo === 'archivo')

                @if (!$this->ambitoElegido)
                    <div class="alert alert-warning mb-2">
                        <strong>Selecciona el ámbito a capturar.</strong>
                    </div>
                @endif

                {{-- Selector de ámbito (si decides que el usuario pueda elegirlo) --}}
                <div class="cap-ambito">
                    <div class="cap-ambito-label">Ámbito</div>

                    <div class="cap-ambito-grid">
                        <label class="cap-ambito-pill">
                            <input class="cap-ambito-radio" type="radio" wire:model.live="ambito_geo"
                                value="SIN_AMBITO" @disabled($soloLectura)>
                            <span class="cap-ambito-text">ESTATAL</span>
                        </label>

                        <label class="cap-ambito-pill">
                            <input class="cap-ambito-radio" type="radio" wire:model.live="ambito_geo"
                                value="REGION" @disabled($soloLectura)>
                            <span class="cap-ambito-text">REGIÓN</span>
                        </label>

                        <label class="cap-ambito-pill">
                            <input class="cap-ambito-radio" type="radio" wire:model.live="ambito_geo"
                                value="MUNICIPIO" @disabled($soloLectura)>
                            <span class="cap-ambito-text">MUNICIPIO</span>
                        </label>
                    </div>
                </div>

                @if (!empty($motivoResetPlantilla))
                    <div class="alert alert-warning mb-2">{{ $motivoResetPlantilla }}</div>
                @endif

                {{-- ✅ Plantilla --}}
                <div class="cap-step">
                    <div class="cap-step-top">
                        <div>
                            <div class="cap-step-kicker">Paso 1</div>
                            <div class="cap-step-title">Descargar plantilla</div>
                            <div class="cap-step-sub">Baja el formato oficial del indicador para llenarlo
                                correctamente.</div>
                        </div>

                        <div class="cap-step-status {{ $plantillaDescargada ? 'ok' : 'no' }}">
                            {{ $plantillaDescargada ? 'Plantilla descargada ✓' : 'Plantilla no descargada ✕' }}
                        </div>
                    </div>

                    <div class="cap-step-actions">
                        <button type="button" class="btn btn-outline-success" wire:click="descargarPlantilla"
                            @disabled($soloLectura)>
                            Descargar plantilla
                        </button>
                    </div>

                    @if (!$plantillaDescargada)
                        <div class="cap-step-hint">
                            Debes descargar la plantilla antes de poder subir tu archivo.
                        </div>
                    @endif
                </div>

                {{-- ✅ Archivo --}}
                <div class="cap-step">
                    <div class="cap-step-top">
                        <div>
                            <div class="cap-step-kicker">Paso 2</div>
                            <div class="cap-step-title">Subir archivo lleno</div>
                            <div class="cap-step-sub">Selecciona el archivo (.xlsx/.xls/.csv) basado en la plantilla.
                            </div>
                        </div>

                        <div class="cap-step-status {{ !empty($archivoNombre) ? 'ok' : 'no' }}">
                            {{ !empty($archivoNombre) ? 'Archivo listo ✓' : 'Sin archivo ✕' }}
                        </div>
                    </div>

                    <label class="cap-drop {{ $soloLectura || !$plantillaDescargada ? 'is-disabled' : '' }}">
                        <input type="file" class="cap-drop-input" wire:model.live="archivo"
                            accept=".xlsx,.xls,.csv" @disabled($soloLectura || !$plantillaDescargada)>

                        <div class="cap-drop-body">
                            <div class="cap-drop-title">
                                @if (!empty($archivoNombre))
                                    Archivo seleccionado:
                                    <span class="cap-drop-file">{{ $archivoNombre }}</span>
                                @else
                                    Arrastra tu archivo aquí o haz clic para seleccionarlo
                                @endif
                            </div>

                            <div class="cap-drop-sub">
                                Formatos permitidos: .xlsx, .xls, .csv
                            </div>
                        </div>
                    </label>

                    @error('archivo')
                        <div class="cap-drop-error">{{ $message }}</div>
                    @enderror

                    @if (!$plantillaDescargada)
                        <div class="cap-step-hint">
                            Primero descarga la plantilla para habilitar la subida.
                        </div>
                    @endif
                </div>

                {{-- ✅ Procesar --}}
                <div class="cap-step">
                    <div class="cap-step-top">
                        <div>
                            <div class="cap-step-kicker">Paso 3</div>
                            <div class="cap-step-title">Procesar archivo</div>
                            <div class="cap-step-sub">Validamos y cargamos las filas del archivo al sistema.</div>
                        </div>

                        <div class="cap-step-status {{ $archivoProcesado ? 'ok' : 'no' }}">
                            {{ $archivoProcesado ? 'Procesado ✓' : 'Sin procesar ✕' }}
                        </div>
                    </div>

                    <div class="cap-step-actions cap-step-actions-split">
                        <button type="button" class="btn btn-primary" wire:click="procesarArchivo"
                            @disabled($soloLectura || !$plantillaDescargada || !$archivo)">
                            Procesar archivo
                        </button>

                        <div class="cap-metrics">
                            <div class="cap-metric">
                                <div class="cap-metric-label">Filas insertadas</div>
                                <div class="cap-metric-value">{{ $detallesInsertados }}</div>
                            </div>
                        </div>
                    </div>

                    @if (!$archivo)
                        <div class="cap-step-hint">
                            Selecciona un archivo para habilitar el procesamiento.
                        </div>
                    @elseif (!$plantillaDescargada)
                        <div class="cap-step-hint">
                            Descarga la plantilla para habilitar el procesamiento.
                        </div>
                    @endif
                </div>

                {{-- ✅ Enviar --}}
                <div class="cap-final">
                    <div class="cap-final-left">
                        <div class="cap-final-title">Acción final</div>
                        <div class="cap-final-sub">
                            Envía la captura cuando el archivo esté procesado correctamente.
                        </div>

                        @if (!$archivoProcesado)
                            <div class="cap-final-hint">
                                Primero debes subir y procesar el archivo para poder enviar.
                            </div>
                        @endif
                    </div>

                    <div class="cap-final-right">
                        <button type="button" wire:click="guardarTodo" class="btn btn-success cap-final-btn"
                            @disabled($soloLectura || $guardando || !$archivoProcesado || $modoCorreccion) wire:loading.attr="disabled" wire:target="guardarTodo">

                            <span wire:loading.remove wire:target="guardarTodo">Enviar</span>
                            <span wire:loading wire:target="guardarTodo">Enviando...</span>
                        </button>
                    </div>
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
</div>

@push('styles')
    <style>
        .d-none {
            display: none !important;
        }

        .formulario-root {
            font-family: Arial, sans-serif;
            margin: 20px auto;
            max-width: 1200px;
            /* o 1400px si quieres más */
            padding: 0 16px;

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

        /* ✅ Solo botón principal (submit) del formulario manual */
        .manual-form form button[type="submit"] {
            padding: 6px 15px;
            border: none;
            border-radius: 4px;
            background-color: #3c763d;
            color: white;
            cursor: pointer;
            transition: 0.2s;
        }

        .manual-form form button[type="submit"]:hover {
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

        /* =========================
                                                                                                                                                                                                                               ✅ CARD FOLIO (PASO 2)
                                                                                                                                                                                                                               ========================= */
        .cap-folio-card {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 14px;
            padding: 14px 16px;
            border-radius: 14px;
            border: 1px solid #e5e7eb;
            background: #ffffff;
            box-shadow: 0 8px 24px rgba(15, 23, 42, .08);
            margin-bottom: 14px;
        }

        .cap-folio-title {
            font-size: 14px;
            font-weight: 900;
            color: #0f172a;
        }

        .cap-folio-value {
            font-weight: 900;
            color: #2563eb;
        }

        .cap-status {
            display: inline-block;
            margin-left: 10px;
            padding: 4px 10px;
            border-radius: 999px;
            border: 1px solid #e5e7eb;
            background: #f8fafc;
            color: #0f172a;
            font-size: 12px;
            font-weight: 900;
        }

        .cap-folio-sub {
            margin-top: 6px;
            color: #64748b;
            font-size: 12px;
            font-weight: 700;
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            align-items: center;
        }

        .cap-dot {
            opacity: .7;
        }

        .cap-btn-danger {
            border-radius: 12px;
            border: 1px solid #fecaca;
            background: #fff;
            color: #ef4444;
            font-weight: 900;
            font-size: 13px;
            padding: 8px 12px;
            cursor: pointer;
        }

        .cap-btn-danger:hover {
            background: #fff5f5;
        }

        /* =========================
                                                                                                                                                                                               ✅ MANUAL PRO (PASO 3)
                                                                                                                                                                                               ========================= */
        .cap-section-head {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 12px;
            margin-bottom: 12px;
        }

        .cap-section-title {
            font-size: 16px;
            font-weight: 900;
            color: #0f172a;
        }

        .cap-section-sub {
            margin-top: 4px;
            font-size: 12px;
            color: #64748b;
            font-weight: 700;
        }

        .cap-field {
            margin-bottom: 12px;
        }

        .cap-label {
            display: block;
            font-size: 12px;
            font-weight: 900;
            color: #475569;
            margin-bottom: 6px;
        }

        .cap-req {
            color: #ef4444;
            font-weight: 900;
            margin-left: 4px;
        }

        .cap-input {
            width: 100%;
            padding: 10px 12px;
            border-radius: 12px;
            border: 1px solid #e5e7eb;
            background: #fff;
            font-size: 14px;
            outline: none;
        }

        .cap-input:focus {
            border-color: #93c5fd;
            box-shadow: 0 0 0 4px rgba(37, 99, 235, .12);
        }

        .cap-btn {
            border-radius: 12px;
            border: 1px solid #e5e7eb;
            padding: 8px 12px;
            font-weight: 900;
            font-size: 13px;
            cursor: pointer;
            background: #fff;
        }

        .cap-btn-primary {
            background: #2563eb;
            border-color: #2563eb;
            color: #fff;
        }

        .cap-btn-ghost {
            background: #fff;
        }

        .cap-btn-danger {
            border-color: #fecaca;
            color: #ef4444;
            background: #fff;
        }

        .cap-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            overflow: hidden;
            border-radius: 12px;
            border: 1px solid #e5e7eb;
            margin-top: 10px;
        }

        .cap-table th {
            background: #f8fafc;
            color: #0f172a;
            font-size: 12px;
            font-weight: 900;
            padding: 10px 12px;
            border-bottom: 1px solid #e5e7eb;
        }

        .cap-table td {
            padding: 10px 12px;
            border-bottom: 1px solid #f1f5f9;
            font-size: 13px;
            color: #334155;
        }

        .cap-table tbody tr:hover td {
            background: #fbfdff;
        }

        /* === LAYOUT SIMPLE 2 COLUMNAS === */
        .cap-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 16px;
        }

        @media (max-width: 900px) {
            .cap-grid {
                grid-template-columns: 1fr;
            }
        }

        .cap-left,
        .cap-right {
            display: flex;
            flex-direction: column;
            gap: 14px;
        }

        /* === TARJETAS === */
        .cap-card {
            background: #fff;
            border: 1px solid #e5e7eb;
            border-radius: 12px;
            padding: 14px;
        }

        .cap-card-title {
            font-size: 14px;
            font-weight: 700;
            margin-bottom: 10px;
            color: #111;
        }

        /* === BARRA STICKY ENVIAR === */
        .cap-sticky {
            position: sticky;
            bottom: 12px;
            margin-top: 14px;
            padding: 12px 14px;
            background: rgba(255, 255, 255, .95);
            border: 1px solid #e5e7eb;
            border-radius: 12px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 12px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, .12);
        }

        .cap-sticky-info {
            font-size: 13px;
            color: #555;
        }

        .cap-sticky-actions {
            display: flex;
            gap: 10px;
        }

        /* === ACCIONES DE TABLA (EDITAR/ELIMINAR) === */
        .cap-row-actions {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
        }

        .cap-action {
            border: 1px solid #e5e7eb;
            background: #fff;
            border-radius: 10px;
            padding: 6px 10px;
            font-size: 12px;
            font-weight: 700;
            cursor: pointer;
            transition: .15s;
        }

        .cap-action:hover {
            transform: translateY(-1px);
            box-shadow: 0 8px 18px rgba(0, 0, 0, .10);
        }

        .cap-action-edit {
            border-color: #bfdbfe !important;
            color: #1d4ed8 !important;
            background: #eff6ff !important;
        }

        .cap-action-del {
            border-color: #fecaca !important;
            color: #b91c1c !important;
            background: #fff5f5 !important;
        }

        /* === TABLA PRO === */
        .cap-table-wrap {
            border: 1px solid #e5e7eb;
            border-radius: 14px;
            overflow: auto;
            /* en móvil hace scroll horizontal */
            background: #fff;
        }

        .cap-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            min-width: 550px;
            /* más razonable; si es móvil hará scroll */
        }

        .cap-table th {
            position: sticky;
            top: 0;
            background: #f8fafc;
            color: #0f172a;
            font-size: 12px;
            font-weight: 900;
            padding: 12px;
            border-bottom: 1px solid #e5e7eb;
            z-index: 1;
        }

        .cap-table td {
            padding: 12px;
            font-size: 13px;
            color: #334155;
            border-bottom: 1px solid #f1f5f9;
            background: #fff;
        }

        .cap-table tbody tr:hover td {
            background: #fbfdff;
        }

        .cap-table td:last-child {
            white-space: nowrap;
            /* para que Editar/Eliminar no se rompa feo */
        }

        /* ✅ EVITA QUE LA COLUMNA DERECHA SE SALGA (FIX OVERFLOW GRID) */
        .cap-left,
        .cap-right,
        .cap-card {
            min-width: 0;
        }

        .cap-table-wrap {
            max-width: 100%;
        }

        /* === MODO EDICIÓN === */
        .cap-edit-mode {
            background: #fff7ed;
            border: 1px solid #fed7aa;
            color: #9a3412;
            font-size: 13px;
            font-weight: 700;
            padding: 10px 12px;
            border-radius: 10px;
            margin-bottom: 12px;
        }

        /* Botón warning (editar) */
        .cap-btn-warning {
            background: #f59e0b;
            border-color: #f59e0b;
            color: #fff;
        }

        .cap-btn-warning:hover {
            background: #d97706;
        }

        /* =========================
                                                                                                   ✅ ARCHIVO UI (PASO 1)
                                                                                                   ========================= */
        .cap-archivo-card {
            background: #fff;
            border: 1px solid #e5e7eb;
            border-radius: 14px;
            padding: 16px;
            box-shadow: 0 10px 24px rgba(15, 23, 42, .08);
        }

        .cap-archivo-head {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 12px;
            margin-bottom: 12px;
            padding-bottom: 12px;
            border-bottom: 1px solid #eef2f7;
        }

        .cap-archivo-title {
            font-size: 16px;
            font-weight: 900;
            color: #0f172a;
        }

        .cap-archivo-sub {
            margin-top: 4px;
            font-size: 12px;
            font-weight: 700;
            color: #64748b;
        }

        /* =========================
                                                                                           ✅ ÁMBITO estilo pills (PASO 2)
                                                                                           ========================= */
        .cap-ambito {
            margin-bottom: 12px;
        }

        .cap-ambito-label {
            font-size: 12px;
            font-weight: 900;
            color: #475569;
            margin-bottom: 8px;
        }

        .cap-ambito-grid {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 10px;
        }

        @media (max-width: 720px) {
            .cap-ambito-grid {
                grid-template-columns: 1fr;
            }
        }

        .cap-ambito-pill {
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            padding: 10px 12px;
            border-radius: 12px;
            border: 1px solid #e5e7eb;
            background: #fff;
            cursor: pointer;
            user-select: none;
            font-weight: 900;
            color: #0f172a;
            transition: .15s ease;
        }

        .cap-ambito-pill:hover {
            transform: translateY(-1px);
            box-shadow: 0 10px 18px rgba(0, 0, 0, .08);
            border-color: #cbd5e1;
        }

        .cap-ambito-radio {
            position: absolute;
            opacity: 0;
            pointer-events: none;
        }

        .cap-ambito-text {
            font-size: 12px;
            letter-spacing: .2px;
        }

        .cap-ambito-pill:has(input:checked) {
            border-color: #16a34a;
            box-shadow: 0 0 0 4px rgba(22, 163, 74, .14);
            background: #f0fdf4;
        }

        .cap-ambito-pill:has(input:disabled) {
            opacity: .55;
            cursor: not-allowed;
            box-shadow: none;
            transform: none;
        }

        /* =========================
                                                                                   ✅ Steps archivo (PASO 3)
                                                                                   ========================= */
        .cap-step {
            border: 1px solid #e5e7eb;
            border-radius: 14px;
            padding: 14px;
            background: #fff;
            margin-bottom: 12px;
        }

        .cap-step-top {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 12px;
            margin-bottom: 10px;
        }

        .cap-step-kicker {
            font-size: 11px;
            font-weight: 900;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: .6px;
        }

        .cap-step-title {
            font-size: 14px;
            font-weight: 900;
            color: #0f172a;
            margin-top: 2px;
        }

        .cap-step-sub {
            font-size: 12px;
            font-weight: 700;
            color: #64748b;
            margin-top: 4px;
        }

        .cap-step-status {
            font-size: 12px;
            font-weight: 900;
            padding: 6px 10px;
            border-radius: 999px;
            border: 1px solid #e5e7eb;
            background: #f8fafc;
            white-space: nowrap;
        }

        .cap-step-status.ok {
            border-color: #bbf7d0;
            background: #f0fdf4;
            color: #166534;
        }

        .cap-step-status.no {
            border-color: #fecaca;
            background: #fff5f5;
            color: #991b1b;
        }

        .cap-step-actions {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }

        .cap-step-hint {
            margin-top: 10px;
            padding: 10px 12px;
            border-radius: 12px;
            border: 1px solid #fed7aa;
            background: #fff7ed;
            color: #9a3412;
            font-size: 12px;
            font-weight: 700;
        }

        /* =========================
                                                                           ✅ Dropzone visual (PASO 4)
                                                                           ========================= */
        .cap-drop {
            display: block;
            border: 2px dashed #cbd5e1;
            border-radius: 14px;
            padding: 14px;
            background: #f8fafc;
            cursor: pointer;
            transition: .15s ease;
        }

        .cap-drop:hover {
            background: #f1f5f9;
            border-color: #94a3b8;
        }

        .cap-drop-input {
            display: none;
        }

        .cap-drop-body {
            display: flex;
            flex-direction: column;
            gap: 6px;
        }

        .cap-drop-title {
            font-size: 13px;
            font-weight: 900;
            color: #0f172a;
        }

        .cap-drop-file {
            color: #16a34a;
            font-weight: 900;
        }

        .cap-drop-sub {
            font-size: 12px;
            font-weight: 700;
            color: #64748b;
        }

        .cap-drop-error {
            margin-top: 10px;
            font-size: 12px;
            font-weight: 800;
            color: #b91c1c;
        }

        .cap-drop.is-disabled {
            opacity: .55;
            cursor: not-allowed;
        }

        .cap-drop.is-disabled:hover {
            background: #f8fafc;
            border-color: #cbd5e1;
        }

        /* =========================
                                                                   ✅ Métricas (PASO 5)
                                                                   ========================= */
        .cap-step-actions-split {
            justify-content: space-between;
            align-items: center;
        }

        .cap-metrics {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }

        .cap-metric {
            border: 1px solid #e5e7eb;
            background: #f8fafc;
            border-radius: 12px;
            padding: 10px 12px;
            min-width: 160px;
            text-align: right;
        }

        .cap-metric-label {
            font-size: 11px;
            font-weight: 900;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: .6px;
        }

        .cap-metric-value {
            margin-top: 4px;
            font-size: 18px;
            font-weight: 900;
            color: #0f172a;
        }

        /* =========================
                                                           ✅ Acción final (PASO 6)
                                                           ========================= */
        .cap-final {
            margin-top: 12px;
            padding: 14px;
            border-radius: 14px;
            border: 1px solid #e5e7eb;
            background: linear-gradient(180deg, #ffffff 0%, #f8fafc 100%);
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 12px;
        }

        @media (max-width: 720px) {
            .cap-final {
                flex-direction: column;
                align-items: stretch;
            }

            .cap-final-right {
                width: 100%;
            }
        }

        .cap-final-title {
            font-size: 14px;
            font-weight: 900;
            color: #0f172a;
        }

        .cap-final-sub {
            margin-top: 4px;
            font-size: 12px;
            font-weight: 700;
            color: #64748b;
        }

        .cap-final-hint {
            margin-top: 10px;
            padding: 10px 12px;
            border-radius: 12px;
            border: 1px solid #e5e7eb;
            background: #fff;
            color: #475569;
            font-size: 12px;
            font-weight: 700;
        }

        .cap-final-btn {
            padding: 10px 16px;
            border-radius: 12px;
            font-weight: 900;
        }

        /* =========================
                                               ✅ Grid campos manual (PASO 2)
                                               ========================= */
        .cap-fields-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 12px;
        }

        @media (max-width: 720px) {
            .cap-fields-grid {
                grid-template-columns: 1fr;
            }
        }

        /* =========================
                                       ✅ Empty state tabla (PASO 3)
                                       ========================= */
        .cap-empty {
            margin: 10px 0;
            padding: 14px;
            border-radius: 14px;
            border: 1px dashed #cbd5e1;
            background: #f8fafc;
            text-align: center;
        }

        .cap-empty-title {
            font-size: 13px;
            font-weight: 900;
            color: #0f172a;
        }

        .cap-empty-sub {
            margin-top: 6px;
            font-size: 12px;
            font-weight: 700;
            color: #64748b;
        }

        /* =========================
                               ✅ Header card (PASO 4)
                               ========================= */
        .cap-topcard {
            margin-bottom: 14px;
            padding: 14px 16px;
            border-radius: 14px;
            border: 1px solid #e5e7eb;
            background: linear-gradient(180deg, #ffffff 0%, #f8fafc 100%);
            box-shadow: 0 10px 24px rgba(15, 23, 42, .08);
        }

        .cap-topcard-kicker {
            font-size: 11px;
            font-weight: 900;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: .6px;
        }

        .cap-topcard-title {
            margin-top: 4px;
            font-size: 16px;
            font-weight: 900;
            color: #0f172a;
        }

        .cap-topcard-grid {
            margin-top: 12px;
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 10px;
        }

        @media (max-width: 900px) {
            .cap-topcard-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }

        @media (max-width: 520px) {
            .cap-topcard-grid {
                grid-template-columns: 1fr;
            }
        }

        .cap-topcard-item {
            border: 1px solid #e5e7eb;
            background: #fff;
            border-radius: 12px;
            padding: 10px 12px;
        }

        .cap-topcard-label {
            font-size: 11px;
            font-weight: 900;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: .6px;
        }

        .cap-topcard-value {
            margin-top: 4px;
            font-size: 12px;
            font-weight: 800;
            color: #0f172a;
        }

        /* ✅ Botón + ayuda (folio card) */
        .cap-folio-actions {
            display: flex;
            flex-direction: column;
            align-items: flex-end;
            gap: 10px;
            flex: 0 0 300px;
            /* ancho fijo agradable */
            padding-left: 14px;
            /* separa del texto izquierdo */
        }

        .cap-reset-help {
            max-width: 300px;
            text-align: right;
            font-size: 12px;
            font-weight: 700;
            color: #64748b;
            line-height: 1.3;
        }

        @media (max-width: 720px) {
            .cap-folio-card {
                flex-direction: column;
                align-items: flex-start;
            }

            .cap-folio-actions {
                width: 100%;
                flex: 1 1 auto;
                align-items: flex-start;
                padding-left: 0;
            }

            .cap-reset-help {
                text-align: left;
                max-width: 100%;
            }
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
