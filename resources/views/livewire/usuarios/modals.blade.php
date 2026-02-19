<!-- Modal -->
@if (session()->has('error'))
    <div class="alert alert-danger">
        {{ session('error') }}
    </div>
@endif

<div wire:ignore.self class="modal fade" id="DataModal" data-bs-backdrop="static" tabindex="-1" role="dialog"
    aria-labelledby="DataModalLabel" aria-hidden="true">

    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title" id="DataModalLabel">
                    {{ $selected_id ? 'Actualizar usuario' : 'Crear usuario' }}
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
                                <label for="usuario_usr" class="form-label fw-semibold">Usuario</label>
                                <input wire:model.defer="usuario_usr" type="text" class="form-control"
                                    id="usuario_usr" placeholder="Ej. jtellez">
                                <small class="text-muted">Nombre de inicio de sesión.</small>
                                @error('usuario_usr')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>

                            <div>
                                <label for="nombre_usr" class="form-label fw-semibold">Nombre</label>
                                <input wire:model.defer="nombre_usr" type="text" class="form-control" id="nombre_usr"
                                    placeholder="Ej. Juan">
                                <small class="text-muted">Nombre(s) del usuario.</small>
                                @error('nombre_usr')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>

                            <div>
                                <label for="apellido_paterno" class="form-label fw-semibold">Apellido paterno</label>
                                <input wire:model.defer="apellido_paterno" type="text" class="form-control"
                                    id="apellido_paterno" placeholder="Ej. Pérez">
                                <small class="text-muted">Primer apellido.</small>
                                @error('apellido_paterno')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>

                            <div>
                                <label for="apellido_materno" class="form-label fw-semibold">Apellido materno</label>
                                <input wire:model.defer="apellido_materno" type="text" class="form-control"
                                    id="apellido_materno" placeholder="Ej. López">
                                <small class="text-muted">Segundo apellido (si aplica).</small>
                                @error('apellido_materno')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>

                            <div>
                                <label for="telefono_usr" class="form-label fw-semibold">Teléfono</label>
                                <input wire:model.defer="telefono_usr" type="text" class="form-control"
                                    id="telefono_usr" placeholder="Ej. 9511234567">
                                <small class="text-muted">Contacto del usuario.</small>
                                @error('telefono_usr')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>

                        </div>

                        {{-- COLUMNA DERECHA --}}
                        <div class="col-md-6">

                            <div>
                                <label for="email_usr" class="form-label fw-semibold">Correo</label>
                                <input wire:model.defer="email_usr" type="text" class="form-control" id="email_usr"
                                    placeholder="Ej. usuario@correo.com">
                                <small class="text-muted">Correo para notificaciones.</small>
                                @error('email_usr')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>

                            <div>
                                <label for="password" class="form-label fw-semibold">Contraseña</label>
                                <input type="password" wire:model.defer="password" class="form-control" id="password"
                                    placeholder="••••••••">
                                <small class="text-muted">
                                    Solo captura una contraseña si deseas cambiarla.
                                </small>
                                @error('password')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>

                            <div>
                                <label class="form-label fw-semibold">Rol</label>
                                <select wire:model.live="id_rol" class="form-control">
                                    <option value="">Seleccione rol</option>

                                    @if (!$adminExiste)
                                        <option value="1">Administrador</option>
                                    @endif

                                    <option value="2">Enlace Técnico</option>
                                </select>
                                <small class="text-muted">Define permisos del usuario.</small>
                                @error('id_rol')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>

                            @if ($id_rol == 2)
                                <div>
                                    <label class="form-label fw-semibold">Dependencia</label>

                                    @if ($dependenciasDisponibles->isEmpty())
                                        <div class="alert alert-warning mb-0">
                                            No hay dependencias disponibles para asignar.
                                        </div>
                                    @else
                                        <select wire:model.live="id_depen" class="form-control">
                                            <option value="">Seleccione dependencia</option>
                                            @foreach ($dependenciasDisponibles as $dep)
                                                <option value="{{ $dep->id_depen }}">
                                                    {{ $dep->nombre_depen }}
                                                </option>
                                            @endforeach
                                        </select>
                                        <small class="text-muted">Se asigna solo para Enlace Técnico.</small>
                                    @endif

                                    @error('id_depen')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                            @endif

                            <div>
                                <label class="form-label fw-semibold">Estado</label>
                                <select wire:model="estado_usr" class="form-control">
                                    <option value="">Seleccione estado</option>
                                    <option value="Activo">Activo</option>
                                    <option value="Inactivo">Inactivo</option>
                                </select>
                                <small class="text-muted">Establece si el usuario esta activo oh inactivo</small>
                                @error('estado_usr')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>

                        </div>

                    </div>
                </form>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary close-btn" data-bs-dismiss="modal">Cerrar</button>
                <button type="button" wire:click.prevent="save()" class="btn btn-primary">
                    {{ $selected_id ? 'Actualizar' : 'Crear' }}
                </button>
            </div>

        </div>
    </div>
</div>
