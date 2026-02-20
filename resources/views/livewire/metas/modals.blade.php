{{-- Modal Crear/Editar Meta --}}
<div wire:ignore.self class="modal fade" id="MetaModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">

            <div class="modal-header">
                <div>
                    <h5 class="modal-title mb-0">
                        {{ $selectedId ? 'Actualizar meta' : 'Crear meta' }}
                    </h5>
                    <small class="text-muted">
                        Define el título y el periodo a reportar. El orden se asigna automáticamente según el indicador.
                    </small>
                </div>

                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body">
                <div class="row g-2">

                    <div class="col-md-6">
                        <label class="form-label">Título</label>
                        <input type="text" class="form-control" wire:model.defer="titulo"
                            placeholder="Ej: Avance del trimestre / Meta anual / etc.">
                        @error('titulo')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">Ejercicio (año)</label>
                        <input type="number" min="2000" max="2100" class="form-control" wire:model="ejercicio"
                            placeholder="2026">
                        @error('ejercicio')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">Corte</label>
                        <select wire:model="corte" class="form-control">
                            @foreach ($cortesDisponibles as $key => $label)
                                <option value="{{ $key }}" @if (in_array((int) $key, $cortesUsados, true)) disabled @endif>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>

                        @error('corte')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="col-md-12">
                        <small class="text-muted">
                            El corte se genera según el periodo del indicador. El orden se asigna automáticamente.
                        </small>
                    </div>

                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>

                @if ($selectedId)
                    <button type="button" class="btn btn-primary" wire:click="update">Guardar cambios</button>
                @else
                    <button type="button" class="btn btn-primary" wire:click="store">Crear</button>
                @endif
            </div>

        </div>
    </div>
</div>

{{-- Modal Configurar Campos --}}
<div wire:ignore.self class="modal fade" id="CamposModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">

            <div class="modal-header">
                <div>
                    <h5 class="modal-title d-flex align-items-center gap-2 mb-0">
                        <i class="bi bi-ui-checks"></i>
                        Configurar campos de la meta
                    </h5>
                    <small class="text-muted">
                        Define los campos que se capturarán en esta meta (slug, etiqueta, tipo y validaciones).
                        <br> <strong>Min</strong> y <strong>Max</strong> son <strong>opcionales</strong>: déjalos vacíos si
                        el dato puede ser negativo o no tiene un rango fijo.</br>
                        El <strong>slug</strong> es la clave que se guarda en el JSON.
                    </small>
                </div>

                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body">

                <div class="d-flex justify-content-between align-items-center mb-2">
                    <div class="text-muted">
                        Agrega los campos que formarán parte de la captura. Estos se guardan en el JSON de la meta.
                    </div>
                    <button class="btn btn-sm btn-outline-primary" type="button" wire:click="addCampo">
                        + Agregar campo
                    </button>
                </div>

                <div class="table-responsive">
                    <table class="table table-bordered table-sm align-middle">
                        <thead>
                            <tr>
                                <th style="width:18%">slug</th>
                                <th style="width:22%">label</th>
                                <th style="width:12%">type</th>
                                <th style="width:10%">required</th>
                                <th style="width:10%">min</th>
                                <th style="width:10%">max</th>
                                <th style="width:8%"></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($campos as $i => $c)
                                <tr>
                                    <td>
                                        <input class="form-control form-control-sm"
                                            wire:model.defer="campos.{{ $i }}.slug"
                                            placeholder="ej: poblacion_total">
                                    </td>

                                    <td>
                                        <input class="form-control form-control-sm"
                                            wire:model.defer="campos.{{ $i }}.label"
                                            placeholder="Etiqueta visible">
                                    </td>

                                    <td>
                                        <select class="form-select form-select-sm"
                                            wire:model.defer="campos.{{ $i }}.type">
                                            <option value="number">number (entero)</option>
                                            <option value="porcentaje">porcentaje (decimal)</option>
                                            <option value="text">text</option>
                                        </select>
                                    </td>

                                    <td class="text-center">
                                        <input type="checkbox" class="form-check-input"
                                            wire:model.defer="campos.{{ $i }}.required">
                                    </td>

                                    <td>
                                        <input class="form-control form-control-sm"
                                            wire:model.defer="campos.{{ $i }}.min" placeholder="Opcional (ej: -10)">
                                    </td>

                                    <td>
                                        <input class="form-control form-control-sm"
                                            wire:model.defer="campos.{{ $i }}.max" placeholder="Opcional (ej: 100)">
                                    </td>

                                    <td class="text-center">
                                        <button class="btn btn-sm btn-outline-danger" type="button"
                                            wire:click="removeCampo({{ $i }})">
                                            <i class="bi-trash3"></i>
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <small class="text-muted">
                    * El <strong>slug</strong> debe ser único por meta y es la clave usada en el JSON
                    (<code>payload_det.campos</code>).
                </small>

            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-primary" wire:click="saveCampos">Guardar campos</button>
            </div>

        </div>
    </div>
</div>
