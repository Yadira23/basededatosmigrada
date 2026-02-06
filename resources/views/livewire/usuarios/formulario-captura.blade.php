<div class="formulario-root">
    <div class="alert alert-info" style="margin-bottom:15px;">
        <strong>Formulario:</strong> {{ $this->formulario->titulo_form ?? ('#' . $id_form) }} <br>
        <strong>Indicador:</strong> {{ $this->indicador->nombre_ind ?? ('ID ' . $id_ind) }} <br>
        <strong>Periodo:</strong> {{ $this->indicador->periodo_ind ?? '—' }} |
        <strong>Unidad:</strong> {{ $this->indicador->unidadmedida_ind ?? '—' }} <br>
        <strong>Fuente:</strong> {{ $this->indicador->fuenteverificacion_ind ?? '—' }}
    </div>

    {{-- 🚨 MODO CORRECCIÓN --}}
    @if($modoCorreccion)
    <div class="alert alert-warning" style="margin-bottom:15px;">
        <strong>Modo corrección:</strong><br>
        <strong>Observación:</strong> {{ $mensajeObservacion ?? '—' }}
    </div>
    @endif

    @if($soloLectura)
    <div class="alert alert-warning" style="margin-bottom:15px;">
        <strong>Formulario finalizado:</strong> ya no puedes capturar ni enviar información. Solo puedes consultar lo que ya existe.
    </div>
    @endif

    <h2>Selecciona el método de captura</h2>

    {{-- MÉTODOS DE CAPTURA --}}
    <div class="metodos">
        <div id="manual"
            class="card {{ $metodo === 'manual' ? 'selected' : '' }} {{ $soloLectura ? 'disabled-card' : '' }}"
            @if(!$soloLectura) wire:click="seleccionar('manual')" @endif>
            <h3>Captura Manual</h3>
            <p>Ingresa los valores manualmente (dinámico según el indicador).</p>
        </div>

        <div id="archivo"
            class="card {{ $metodo === 'archivo' ? 'selected' : '' }} {{ $soloLectura ? 'disabled-card' : '' }}"
            @if(!$soloLectura) wire:click="seleccionar('archivo')" @endif>
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
            @if(!empty($archivoNombre))
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
    <div id="manualForm" @class(['manual-form', 'd-none'=> $metodo !== 'manual'])>
        <h3>Captura Manual</h3>

        <fieldset @disabled($soloLectura) style="border:0; padding:0; margin:0;">
            {{-- ÁMBITO --}}
            <div style="margin-bottom:10px;">
                <label>Capturar por:</label>
                <select wire:model.live="ambito_geo">
                    <option value="SIN_AMBITO">Global (sin región/municipio)</option>
                    <option value="REGION">Región</option>
                    <option value="MUNICIPIO">Municipio</option>
                </select>
            </div>

            <form id="formManual" wire:submit.prevent="agregarManual">
                {{-- REGIÓN --}}
                @if($ambito_geo === 'REGION')
                <div style="flex:1 1 100%; margin-bottom:10px;">
                    <label>Región:</label>
                    <select wire:model.live="region">
                        <option value="">Selecciona una región</option>

                        @foreach($regiones as $r)
                        @php
                        $regionUsada = collect($manualData)->contains(function($row) use ($r) {
                        return ($row['ambito_geo'] ?? '') === 'REGION'
                        && (int)($row['id_region'] ?? 0) === (int)$r->id_region;
                        });
                        @endphp

                        <option value="{{ $r->id_region }}" @disabled($regionUsada)>
                            {{ $r->nombre_region }} @if($regionUsada) (ya agregado) @endif
                        </option>
                        @endforeach
                    </select>
                </div>
                @endif

                {{-- MUNICIPIO --}}
                @if($ambito_geo === 'MUNICIPIO')
                <div style="flex:1 1 100%; margin-bottom:10px;">
                    <label>Región (para filtrar municipios):</label>
                    <select wire:model.live="regionFiltro">
                        <option value="">Selecciona una región</option>
                        @foreach($regiones as $r)
                        <option value="{{ $r->id_region }}">{{ $r->nombre_region }}</option>
                        @endforeach
                    </select>

                    <label style="margin-top:8px; display:block;">Municipio:</label>
                    <select wire:model.live="municipio" @disabled(empty($regionFiltro))>
                        <option value="">Selecciona un municipio</option>

                        @foreach($municipiosFiltrados as $m)
                        @php
                        $munUsado = collect($manualData)->contains(function($row) use ($m) {
                        return ($row['ambito_geo'] ?? '') === 'MUNICIPIO'
                        && (int)($row['id_mun'] ?? 0) === (int)$m->id_mun;
                        });
                        @endphp

                        <option value="{{ $m->id_mun }}" @disabled($munUsado)>
                            {{ $m->nombre_municipio }} @if($munUsado) (ya agregado) @endif
                        </option>
                        @endforeach
                    </select>
                </div>
                @endif

                {{-- CAMPOS DINÁMICOS --}}
                <div style="flex:1 1 100%; margin-bottom:10px;">
                    <h4 style="margin:10px 0;">Campos del indicador</h4>

                    @if(empty($schema))
                    <div class="alert alert-warning">
                        Este indicador aún no tiene campos dinámicos configurados (config_campos).
                    </div>
                    @else
                    @foreach($schema as $campo)
                    @php
                    $slug = $campo['slug'] ?? '';
                    $label = $campo['label'] ?? $slug;
                    $required = !empty($campo['required']);
                    $step = (($campo['type'] ?? '') === 'porcentaje') ? 0.01 : 1;
                    @endphp

                    <div style="margin-bottom:10px;">
                        <label>
                            {{ $label }}
                            @if($required) <span style="color:red;">*</span> @endif
                        </label>

                        <input type="number"
                            wire:model.live="manualCampos.{{ $slug }}"
                            step="{{ $step }}"
                            @if(isset($campo['min'])) min="{{ $campo['min'] }}" @endif
                            @if(isset($campo['max'])) max="{{ $campo['max'] }}" @endif>
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
                            @if($ambito_geo === 'MUNICIPIO')
                            Municipio
                            @elseif($ambito_geo === 'REGION')
                            Región
                            @else
                            Global
                            @endif
                        </th>

                        @foreach($schema as $campo)
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

                        @foreach($schema as $campo)
                        @php $slug = $campo['slug'] ?? ''; @endphp
                        <td>{{ $camposRow[$slug] ?? '' }}</td>
                        @endforeach

                        <td>
                            <button type="button" wire:click="editarManual({{ $i }})">Editar</button>
                            <button type="button" wire:click="eliminarManual({{ $i }})">Eliminar</button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="{{ 2 + count($schema) }}">Aún no hay registros.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>

            <button type="button"
                wire:click="guardarTodo"
                class="btn btn-primary"
                @disabled($soloLectura || $guardando || $modoCorreccion)
                wire:loading.attr="disabled"
                wire:target="guardarTodo">
                <span wire:loading.remove wire:target="guardarTodo">Enviar</span>
                <span wire:loading wire:target="guardarTodo">Guardando...</span>
            </button>
        </fieldset>
    </div>

    {{-- =========================
         ✅ ARCHIVO (plantilla + subir + procesar + enviar)
       ========================= --}}
    <div id="tablaArchivoContainer" @class(['archivo-form', 'd-none'=> $metodo !== 'archivo'])>
        @if($metodo === 'archivo')

        <div class="alert alert-info mb-2">
            <strong>Ámbito:</strong> {{ $ambito_geo }}
        </div>

        {{-- Selector de ámbito (si decides que el usuario pueda elegirlo) --}}
        <div class="mb-2">
            <label class="form-label"><strong>Ámbito</strong></label><br>
            <label><input type="radio" wire:model="ambito_geo" value="SIN_AMBITO" @disabled($soloLectura)> SIN_AMBITO</label>
            <label class="ms-3"><input type="radio" wire:model="ambito_geo" value="REGION" @disabled($soloLectura)> REGION</label>
            <label class="ms-3"><input type="radio" wire:model="ambito_geo" value="MUNICIPIO" @disabled($soloLectura)> MUNICIPIO</label>
        </div>

        @if(!empty($motivoResetPlantilla))
        <div class="alert alert-warning mb-2">{{ $motivoResetPlantilla }}</div>
        @endif

        {{-- ✅ Plantilla --}}
        <div class="mb-2">
            <button type="button" class="btn btn-outline-success"
                wire:click="descargarPlantilla"
                @disabled($soloLectura)>
                Descargar plantilla
            </button>

            <div class="mt-2">
                <strong>Plantilla descargada:</strong>
                @if($plantillaDescargada) <span class="text-success">SÍ ✅</span>
                @else <span class="text-danger">NO ❌</span>
                @endif
            </div>

            @if(!$plantillaDescargada)
            <div class="alert alert-warning mt-2 mb-0">
                Debes descargar la plantilla antes de poder subir tu archivo.
            </div>
            @endif
        </div>

        {{-- ✅ Archivo --}}
        <div class="mb-2">
            <input type="file" class="form-control"
                wire:model.live="archivo"
                accept=".xlsx,.xls,.csv"
                @disabled($soloLectura || !$plantillaDescargada)>

            @error('archivo')
            <div class="text-danger mt-1">{{ $message }}</div>
            @enderror

            @if(!empty($archivoNombre))
            <div class="alert alert-secondary mt-2 mb-0">
                Archivo seleccionado: <strong>{{ $archivoNombre }}</strong>
            </div>
            @else
            <small class="text-muted d-block mt-1">Ningún archivo seleccionado</small>
            @endif
        </div>

        {{-- ✅ Procesar --}}
        <div class="mb-2">
            <button type="button" class="btn btn-primary"
                wire:click="procesarArchivo"
                @disabled($soloLectura || !$plantillaDescargada || !$archivo)">
                Procesar archivo
            </button>

            <div class="mt-2">
                <strong>Archivo procesado:</strong>
                @if($archivoProcesado) <span class="text-success">SÍ ✅</span>
                @else <span class="text-danger">NO ❌</span>
                @endif
                <div><strong>Filas insertadas:</strong> {{ $detallesInsertados }}</div>
            </div>
        </div>

        {{-- ✅ Enviar --}}
        <div>
            <button type="button" wire:click="guardarTodo"
                class="btn btn-success"
                @disabled($soloLectura || $guardando || !$archivoProcesado || $modoCorreccion)
                wire:loading.attr="disabled"
                wire:target="guardarTodo">
                <span wire:loading.remove wire:target="guardarTodo">Enviar</span>
                <span wire:loading wire:target="guardarTodo">Enviando...</span>
            </button>

            @if(!$archivoProcesado)
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
    @if($modoCorreccion)
    <div class="mt-4">
        <button type="button"
            class="btn btn-primary"
            wire:click="reenviarCorreccion"
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
    }

    .card {
        border: 2px solid #ccc;
        border-radius: 8px;
        padding: 15px 20px;
        width: 250px;
        cursor: pointer;
        transition: all 0.3s ease;
        background-color: #f9f9f9;
    }

    .card:hover {
        border-color: #777;
        background-color: #f0f0f0;
    }

    .card.selected {
        border-color: #3c763d;
        background-color: #d0f0d0;
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
</script>
@endpush