{{-- Modal Crear/Editar Meta --}}
<div wire:ignore.self class="modal fade" id="MetaModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title">
                    {{ $selectedId ? 'Editar Meta' : 'Nueva Meta' }}
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body">
                <div class="row g-2">

                    <div class="col-md-6">
                        <label class="form-label">Título</label>
                        <input type="text" class="form-control" wire:model.defer="titulo">
                        @error('titulo')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                </div>
            </div>

            <div class="col-md-3">
                <label class="form-label">Ejercicio (año)</label>
                <input type="number" min="2000" max="2100" class="form-control" wire:model.defer="ejercicio"
                    placeholder="2026">
                @error('ejercicio')
                    <small class="text-danger">{{ $message }}</small>
                @enderror
            </div>

            <div class="col-md-3">
                <label class="form-label">Corte</label>
                <select class="form-select" wire:model.defer="corte">
                    @foreach ($cortesDisponibles as $value => $label)
                        <option value="{{ $value }}">{{ $label }}</option>
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
                <h5 class="modal-title">
                    Configurar campos de la meta
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body">

                <div class="d-flex justify-content-between align-items-center mb-2">
                    <div class="text-muted">
                        Define los campos (slug/label/tipo) que se capturarán para esta meta.
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
                                            wire:model.defer="campos.{{ $i }}.min" placeholder="0">
                                    </td>

                                    <td>
                                        <input class="form-control form-control-sm"
                                            wire:model.defer="campos.{{ $i }}.max" placeholder="">
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
                    * slug debe ser único por meta. Esto es lo que usará el JSON (payload_det.campos).
                </small>

            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-primary" wire:click="saveCampos">Guardar campos</button>
            </div>

        </div>
    </div>
</div>
