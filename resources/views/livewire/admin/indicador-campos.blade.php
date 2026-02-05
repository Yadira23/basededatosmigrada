<div class="container py-3" style="max-width:900px;">
    <div class="alert alert-info">
        <strong>Configuración de campos</strong><br>
        Indicador: <b>{{ $indicador->nombre_ind }}</b><br>
        ID: {{ $indicador->id_ind }}
    </div>

    @if(session()->has('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session()->has('error'))
    <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <div class="mb-3">
        <button class="btn btn-primary" wire:click="agregarCampo">+ Agregar campo</button>
        <a class="btn btn-secondary" href="{{ url('/indicadores') }}">Volver</a>
    </div>

    @foreach($campos as $i => $c)
    <div class="card mb-2">
        <div class="card-body">
            <div class="row g-2">
                <div class="col-md-3">
                    <label class="form-label">Slug</label>
                    <input class="form-control" wire:model.defer="campos.{{ $i }}.slug" placeholder="poblacion_total">
                    <small class="text-muted">solo a-z, 0-9, _</small>
                </div>

                <div class="col-md-4">
                    <label class="form-label">Label</label>
                    <input class="form-control" wire:model.defer="campos.{{ $i }}.label" placeholder="Población total">
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
                        <input type="checkbox" class="form-check-input" wire:model.defer="campos.{{ $i }}.required">
                        Req
                    </label>
                </div>

                <div class="col-md-1">
                    <label class="form-label">Min</label>
                    <input
                        class="form-control"
                        type="number"
                        min="0"
                        step="0.01"
                        onkeydown="if(event.key==='-'||event.key==='e'||event.key==='E') event.preventDefault();"
                        onpaste="event.preventDefault();"
                        oninput="if(this.value!=='' && parseFloat(this.value)<0){ this.value='0'; }"
                        wire:model.defer="campos.{{ $i }}.min" />
                </div>

                <div class="col-md-1">
                    <label class="form-label">Max</label>
                    <input
                        class="form-control"
                        type="number"
                        min="0"
                        step="0.01"
                        onkeydown="if(event.key==='-'||event.key==='e'||event.key==='E') event.preventDefault();"
                        onpaste="event.preventDefault();"
                        oninput="if(this.value!=='' && parseFloat(this.value)<0){ this.value='0'; }"
                        wire:model.defer="campos.{{ $i }}.max" />
                </div>

                <div class="col-md-12 mt-2">
                    <button class="btn btn-danger btn-sm" wire:click="eliminarCampo({{ $i }})">Eliminar</button>
                </div>
            </div>
        </div>
    </div>
    @endforeach

    <div class="mt-3">
        <button class="btn btn-success" wire:click="guardar">Guardar configuración</button>
    </div>
</div>