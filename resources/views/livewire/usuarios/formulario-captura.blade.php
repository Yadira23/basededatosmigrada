<div class="formulario-root">

    <h2>Paso 1 de 2: Selecciona el método de captura</h2>

    {{-- MÉTODOS DE CAPTURA --}}
    <div class="metodos">
        <div id="manual"
            class="card {{ $metodo === 'manual' ? 'selected' : '' }}"
            wire:click="seleccionar('manual')">
            <h3>Captura Manual</h3>
            <p>Ingresa los valores manualmente por región. Ideal para pocos datos o correcciones.</p>
        </div>

        <div id="archivo"
            class="card {{ $metodo === 'archivo' ? 'selected' : '' }}"
            wire:click="seleccionar('archivo')">
            <h3>Subir Archivo</h3>
            <p>Sube un archivo Excel o CSV con todos los datos. Perfecto para grandes volúmenes.</p>

            {{-- ✅ INPUT SOLO CUANDO SE SELECCIONA ARCHIVO --}}
            @if($metodo === 'archivo')
            <input type="file"
                id="archivoInput"
                class="file-input"
                wire:model.live="archivo"
                wire:click.stop
                onclick="event.stopPropagation()"
                accept=".txt,.csv,.xlsx,.xls" />

            <div style="margin-top:6px;">
                <span wire:loading wire:target="archivo">Cargando archivo...</span>

                @error('archivo')
                <div style="color:red; margin-top:6px;">{{ $message }}</div>
                @enderror
            </div>
            @endif
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

    {{-- ✅ FORMULARIO MANUAL (UN SOLO DIV, NO @else) --}}
    <div id="manualForm" @class(['manual-form', 'd-none'=> $metodo !== 'manual'])>

        <h3>Captura Manual</h3>

        {{-- ÁMBITO (GLOBAL / REGIÓN / MUNICIPIO) --}}
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

            <div style="margin-bottom:10px;">
                <label>Valor:</label>
                <input type="number" id="valor" wire:model="valor" required>
            </div>

            <button type="submit">Agregar</button>
        </form>

        <h4>Datos ingresados:</h4>
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
                    <th>Valor</th>
                    <th>Acciones</th>
                </tr>
            </thead>

            <tbody>
                @forelse($manualData as $i => $row)
                <tr>
                    <td>{{ $row['nombre'] }}</td>
                    <td>{{ $row['valor'] }}</td>
                    <td>
                        <button type="button" wire:click="editarManual({{ $i }})">Editar</button>
                        <button type="button" wire:click="eliminarManual({{ $i }})">Eliminar</button>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="3">Aún no hay registros.</td>
                </tr>
                @endforelse
            </tbody>
        </table>

        <button type="button"
            wire:click="guardarTodo"
            class="btn btn-primary"
            wire:loading.attr="disabled"
            wire:target="guardarTodo">
            Enviar
        </button>
    </div>

    {{-- ✅ TABLA DE ARCHIVO (PREVIEW) --}}
    <div id="tablaArchivoContainer" @class(['archivo-form', 'd-none'=> $metodo !== 'archivo'])>
        <h3>Datos del archivo (preview)</h3>

        <table id="tablaArchivo">
            <thead>
                <tr>
                    <th>Fila</th>
                    <th>Texto (raw)</th>
                    <th>Número detectado</th>
                </tr>
            </thead>
            <tbody>
                @forelse($archivoPreview as $i => $row)
                <tr>
                    <td>{{ $i + 1 }}</td>
                    <td>{{ $row['payload_det']['raw'] ?? '' }}</td>
                    <td>
                        @if(isset($row['valor_det']) && $row['valor_det'] !== null)
                        {{ $row['valor_det'] }}
                        @else
                        —
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="3">Aún no hay datos cargados.</td>
                </tr>
                @endforelse
            </tbody>
        </table>

        <button type="button"
            wire:click="guardarTodo"
            class="btn btn-primary"
            wire:loading.attr="disabled">
            Enviar
        </button>
    </div>


</div>
@push('styles')
<style>
    .d-none {
        display: none !important;
    }

    /* TU CSS IGUAL */
    /* ===== ESTILO GENERAL ===== */
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

    /* ===== MÉTODOS ===== */
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

    /* ===== INFORMACIÓN ===== */
    .info {
        text-align: center;
        margin-bottom: 20px;
        font-size: 14px;
        color: #555;
    }

    /* ===== FORMULARIO MANUAL ===== */
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

    /* ===== TABLAS ===== */
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

    /* ===== ARCHIVO ===== */
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