<div class="container py-3" style="max-width:900px;">
    <div class="alert alert-info">
        <strong>Configuración de campos</strong><br>
        Indicador: <b>{{ $indicador->nombre_ind }}</b><br>
        ID: {{ $indicador->id_ind }}
    </div>

    <div class="alert alert-light border small">
        <div class="fw-semibold mb-1">Guía rápida de llenado</div>
        <ul class="mb-0 ps-3">
            <li><strong>Slug</strong>: identificador interno (solo <code>a-z</code>, <code>0-9</code>, <code>_</code>).
                Ej: <code>poblacion_total</code>.</li>
            <li><strong>Label</strong>: texto visible para el usuario. Ej: “Cantidad de personas”.</li>
            <li><strong>Tipo</strong>:
                <ul class="mb-0 ps-3">
                    <li><strong>Número</strong>: acepta valores positivos y negativos.</li>
                    <li><strong>Porcentaje</strong>: si es porcentaje real, se recomienda <code>min=0</code> y
                        <code>max=100</code>.</li>
                    <li><strong>Texto</strong>: texto libre (min/max son opcionales si decides limitar longitud).</li>
                </ul>
            </li>
            <li><strong>Req</strong>: obliga a capturar un valor, pero <strong>no obliga</strong> a definir min/max.
            </li>
            <li><strong>Min / Max</strong>: opcionales; déjalos vacíos si el campo puede ser negativo o no tiene rango
                fijo.</li>
        </ul>
    </div>

    @if (session()->has('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if (session()->has('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <div class="mb-3">
        <button class="btn btn-primary" wire:click="agregarCampo">+ Agregar campo</button>
        <a class="btn btn-secondary" href="{{ url('/indicadores') }}">Volver</a>
    </div>

    @foreach ($campos as $i => $c)
        <div class="card mb-2">
            <div class="card-body">
                <div class="row g-2">
                    <div class="col-md-3">
                        <label class="form-label">Slug</label>
                        <input class="form-control" wire:model.defer="campos.{{ $i }}.slug"
                            placeholder="poblacion_total">
                        <small class="text-muted">solo a-z, 0-9, _</small>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Label</label>
                        <input class="form-control" wire:model.defer="campos.{{ $i }}.label"
                            placeholder="Población total">
                    </div>

                    <div class="col-md-2">
                        <label class="form-label">Tipo</label>
                        <select class="form-control" wire:model.defer="campos.{{ $i }}.type">
                            <option value="number">Número</option>
                            <option value="text">Texto</option>
                            <option value="porcentaje">Porcentaje</option>
                        </select>
                    </div>

                    <div class="col-md-1 d-flex align-items-end">
                        <label class="form-check-label">
                            <input type="checkbox" class="form-check-input"
                                wire:model.defer="campos.{{ $i }}.required">
                            Req
                        </label>
                    </div>

                    <div class="col-md-1">
                        <label class="form-label">Min</label>
                        <input class="form-control" type="number" step="0.01" placeholder="Opcional (ej: -10)"
                            wire:model.defer="campos.{{ $i }}.min" />
                            <small class="text-muted">Opcional</small>
                    </div>

                    <div class="col-md-1">
                        <label class="form-label">Max</label>
                        <input class="form-control" type="number" step="0.01" placeholder="Opcional (ej: 100)"
                            wire:model.defer="campos.{{ $i }}.max" />
                            <small class="text-muted">Opcional</small>
                    </div>

                    <div class="col-md-12 mt-2">
                        <button class="btn btn-danger btn-sm"
                            wire:click="eliminarCampo({{ $i }})">Eliminar</button>
                    </div>
                </div>
            </div>
        </div>
    @endforeach

    <div class="mt-3">
        <button class="btn btn-success" wire:click="guardar">Guardar configuración</button>
    </div>
</div>
