@extends('layouts.app')
@section('content')
<div class="formulario-root"> {{-- Elemento raíz Livewire --}}

    <h2>Paso 1 de 2: Selecciona el método de captura</h2>

    {{-- MÉTODOS DE CAPTURA --}}
    <div class="metodos">
        <div id="manual" class="card" onclick="seleccionar('manual')">
            <h3>Captura Manual</h3>
            <p>Ingresa los valores manualmente por región. Ideal para pocos datos o correcciones.</p>
        </div>

        <div id="archivo" class="card" onclick="seleccionar('archivo')">
            <h3>Subir Archivo</h3>
            <p>Sube un archivo Excel o CSV con todos los datos. Perfecto para grandes volúmenes.</p>
            <input type="file" id="archivoInput" class="file-input" onchange="mostrarArchivo(event)">
        </div>
    </div>

    {{-- INFORMACIÓN --}}
    <div class="info">
        <p>Método seleccionado: <span id="metodo">No seleccionado</span></p>
        <p id="archivoNombre"></p>
        <p>Última actualización: 23/01/2026 23:32</p>
    </div>

    {{-- FORMULARIO MANUAL --}}
    <div id="manualForm" class="manual-form" style="display:none;">
        <h3>Captura Manual</h3>
        <form id="formManual" onsubmit="return enviarManual()">
            <label>Región:</label>
            <select id="region" required>
                <option value="" disabled selected>Selecciona una región</option>
                @foreach($regiones as $r)
                <option value="{{ $r->nombre_region }}">{{ $r->nombre_region }}</option>
                @endforeach
            </select>
            <label>Valor:</label>
            <input type="number" id="valor" required>
            <button type="submit">Agregar</button>
        </form>

        <h4>Datos ingresados:</h4>
        <table id="tablaManual">
            <thead>
                <tr>
                    <th>Región</th>
                    <th>Valor</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>

    {{-- TABLA DE ARCHIVO --}}
    <div id="tablaArchivoContainer" class="archivo-form" style="display:none;">
        <h3>Datos del archivo</h3>
        <table id="tablaArchivo">
            <thead>
                <tr>
                    <th>Fila</th>
                    <th>Contenido</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>

</div>
@endsection
<style>
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

<script>
    let metodoSeleccionado = null;

    function seleccionar(metodo) {
        metodoSeleccionado = metodo;
        document.getElementById('metodo').innerText = metodo === 'manual' ? 'Captura Manual' : 'Subir Archivo';
        document.getElementById('manual').classList.remove('selected');
        document.getElementById('archivo').classList.remove('selected');
        document.getElementById(metodo).classList.add('selected');

        document.getElementById('manualForm').style.display = metodo === 'manual' ? 'block' : 'none';
        document.getElementById('tablaArchivoContainer').style.display = 'none';

        if (metodo === 'archivo') document.getElementById('archivoInput').click();
    }

    // Función para enviar fila
    function enviarManual() {
        const select = document.getElementById('region');
        const valor = document.getElementById('valor');
        const regionTexto = select.options[select.selectedIndex].text;
        const regionValue = select.value;

        if (!regionValue) return false;

        const tbody = document.querySelector('#tablaManual tbody');
        const tr = document.createElement('tr');

        tr.innerHTML = `
        <td data-value="${regionValue}">${regionTexto}</td>
        <td>${valor.value}</td>
        <td>
            <button type="button" onclick="editarFila(this)">Editar</button>
            <button type="button" onclick="eliminarFila(this)">Eliminar</button>
        </td>
    `;

        tbody.appendChild(tr);

        // 🔒 Bloquear región en el select
        select.options[select.selectedIndex].disabled = true;

        document.getElementById('formManual').reset();
        return false;
    }

    // Función para eliminar fila
    function eliminarFila(btn) {
        const fila = btn.closest('tr');
        const regionValue = fila.cells[0].dataset.value;

        const select = document.getElementById('region');
        for (let option of select.options) {
            if (option.value == regionValue) {
                option.disabled = false;
                break;
            }
        }

        fila.remove();
    }

    // Función para editar fila
    function editarFila(btn) {
        const fila = btn.closest('tr');
        const regionValue = fila.cells[0].dataset.value;
        const regionTexto = fila.cells[0].innerText;
        const valor = fila.cells[1].innerText;

        const select = document.getElementById('region');

        // Rehabilitar región
        for (let option of select.options) {
            if (option.value == regionValue) {
                option.disabled = false;
                option.selected = true;
                break;
            }
        }

        document.getElementById('valor').value = valor;

        fila.remove();
    }

    function mostrarArchivo(event) {
        const file = event.target.files[0];
        if (!file) return;
        document.getElementById('archivoNombre').innerText = "Archivo seleccionado: " + file.name;

        const reader = new FileReader();
        reader.onload = function(e) {
            const lines = e.target.result.split(/\r\n|\n/);
            const tbody = document.querySelector('#tablaArchivo tbody');
            tbody.innerHTML = '';
            lines.forEach((line, index) => {
                if (line.trim() === '') return;
                const tr = document.createElement('tr');
                tr.innerHTML = `<td>${index+1}</td><td>${line}</td>`;
                tbody.appendChild(tr);
            });
            document.getElementById('tablaArchivoContainer').style.display = 'block';
        };
        reader.readAsText(file);
    }
</script>