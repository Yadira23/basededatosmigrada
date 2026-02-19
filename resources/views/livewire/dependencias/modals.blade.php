<!-- Modal -->
@if (session()->has('error'))
    <div class="alert alert-danger">
        {{ session('error') }}
    </div>
@endif

<div wire:ignore.self class="modal fade" id="DataModal" data-bs-backdrop="static" tabindex="-1" role="dialog"
    aria-labelledby="DataModalLabel" aria-hidden="true">

    <div class="modal-dialog modal-lg" role="document"><!-- modal-lg para mejor espacio -->
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title" id="DataModalLabel">
                    {{ $selected_id ? 'Actualizar dependencia' : 'Crear dependencia' }}
                </h5>
                <button wire:click.prevent="cancel()" type="button" class="btn-close" data-bs-dismiss="modal"
                    aria-label="Close"></button>
            </div>

            <div class="modal-body">
                <form>
                    @if ($selected_id)
                        <input type="hidden" wire:model="selected_id">
                    @endif

                    <div class="row g-3">
                        {{-- COLUMNA IZQUIERDA --}}
                        <div class="col-md-6">

                            <div>
                                <label class="form-label fw-semibold">Nombre de la dependencia</label>
                                <input wire:model.live="nombre_depen" type="text" class="form-control"
                                    oninput="this.value=this.value.replace(/[^A-Za-zÁÉÍÓÚÜÑáéíóúüñ\s]/g,'')"
                                    placeholder="Ej. Secretaría de Turismo">
                                <small class="text-muted">Nombre oficial.</small>
                                @error('nombre_depen') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label class="form-label fw-semibold">Sector</label>
                                <select wire:model.live="id_sector" class="form-control">
                                    <option value="">Seleccione un sector</option>
                                    @foreach($sector as $sector)
                                        <option value="{{ $sector->id_sector }}">
                                            {{ $sector->nombre_sector }}
                                        </option>
                                    @endforeach
                                </select>
                                <small class="text-muted">Clasificación institucional.</small>
                                @error('id_sector') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label class="form-label fw-semibold">Correo institucional</label>
                                <input wire:model.live="email_depen" type="text" class="form-control"
                                    placeholder="contacto@dependencia.gob.mx">
                                <small class="text-muted">Correo de contacto.</small>
                                @error('email_depen') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label class="form-label fw-semibold">Teléfono</label>
                                <input wire:model.live="telefono_depen" type="text" class="form-control" maxlength="10"
                                    inputmode="numeric" placeholder="9511234567"
                                    oninput="this.value=this.value.replace(/[^0-9]/g,'')">
                                <small class="text-muted">10 dígitos.</small>
                                @error('telefono_depen') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>

                        </div>

                        {{-- COLUMNA DERECHA --}}
                        <div class="col-md-6">

                            <div>
                                <label class="form-label fw-semibold">Extensión</label>
                                <input wire:model.live="extension_depen" type="text" maxlength="6"
                                    inputmode="numeric" class="form-control"
                                    oninput="this.value=this.value.replace(/[^0-9]/g,'')"
                                    placeholder="Ej. 1234">
                                <small class="text-muted">Opcional.</small>
                                @error('extension_depen') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label class="form-label fw-semibold">Calle</label>
                                <input wire:model.live="calle_depen" type="text" class="form-control"
                                    placeholder="Av. Juárez">
                                <small class="text-muted">Domicilio.</small>
                                @error('calle_depen') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label class="form-label fw-semibold">Número</label>
                                <input wire:model.live="numerocalle_depen" type="text" maxlength="5"
                                    inputmode="numeric" class="form-control"
                                    oninput="this.value=this.value.replace(/[^0-9]/g,'')"
                                    placeholder="120">
                                <small class="text-muted">Número exterior.</small>
                                @error('numerocalle_depen') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label class="form-label fw-semibold">Colonia</label>
                                <input wire:model.live="colonia_depen" type="text" class="form-control"
                                    oninput="this.value=this.value.replace(/[^A-Za-zÁÉÍÓÚÜÑáéíóúüñ\s]/g,'')"
                                    placeholder="Centro">
                                <small class="text-muted">Colonia o localidad.</small>
                                @error('colonia_depen') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label class="form-label fw-semibold">Código Postal</label>
                                <input wire:model.live="cp_depen" type="text" maxlength="5"
                                    inputmode="numeric" class="form-control"
                                    oninput="this.value=this.value.replace(/[^0-9]/g,'')"
                                    placeholder="68000">
                                <small class="text-muted">5 dígitos.</small>
                                @error('cp_depen') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>

                        </div>
                    </div>
                </form>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                <button type="button" wire:click.prevent="save()" class="btn btn-primary">
                    {{ $selected_id ? 'Actualizar' : 'Crear' }}
                </button>
            </div>

        </div>
    </div>
</div>
