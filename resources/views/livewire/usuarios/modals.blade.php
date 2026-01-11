<!-- Modal -->
  @if (session()->has('error'))
    <div class="alert alert-danger">
        {{ session('error') }}
    </div>
@endif
<div wire:ignore.self class="modal fade" id="DataModal" data-bs-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="DataModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="DataModalLabel">{{ $selected_id ? 'Update Usuario' : 'Create Usuario' }}</h5>
                <button wire:click.prevent="cancel()" type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form>
                    @if ($selected_id)
                    <input type="hidden" wire:model="selected_id">
                    @endif
                    <div class="form-group">
                        <label for="usuario_usr"></label>
                        <input wire:model.defer="usuario_usr" type="text" class="form-control" id="usuario_usr" placeholder="Usuario Usr">@error('usuario_usr') <span class="error text-danger">{{ $message }}</span> @enderror
                    </div>
                    <div class="form-group">
                        <label for="nombre_usr"></label>
                        <input wire:model.defer="nombre_usr" type="text" class="form-control" id="nombre_usr" placeholder="Nombre Usr">@error('nombre_usr') <span class="error text-danger">{{ $message }}</span> @enderror
                    </div>
                    <div class="form-group">
                        <label for="apellido_paterno"></label>
                        <input wire:model.defer="apellido_paterno" type="text" class="form-control" id="apellido_paterno" placeholder="Apellido Paterno">@error('apellido_paterno') <span class="error text-danger">{{ $message }}</span> @enderror
                    </div>
                    <div class="form-group">
                        <label for="apellido_materno"></label>
                        <input wire:model.defer="apellido_materno" type="text" class="form-control" id="apellido_materno" placeholder="Apellido Materno">@error('apellido_materno') <span class="error text-danger">{{ $message }}</span> @enderror
                    </div>
                    <div class="form-group">
                        <label for="email_usr"></label>
                        <input wire:model.defer="email_usr" type="text" class="form-control" id="email_usr" placeholder="Email Usr">@error('email_usr') <span class="error text-danger">{{ $message }}</span> @enderror
                    </div>
                    <div class="form-group">
                        <label for="password">Contraseña</label>
                        <input type="password" wire:model.defer="password" class="form-control">
                        @error('password') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>
                    
                    <div class="form-group">
    <label>Rol</label>
    <select wire:model.live="id_rol" class="form-control">
        <option value="">Seleccione rol</option>

        @if(!$adminExiste)
            <option value="1">Administrador</option>
        @endif

        <option value="2">Enlace Técnico</option>
    </select>

    @error('id_rol') 
        <span class="text-danger">{{ $message }}</span> 
    @enderror
</div>

@if($id_rol == 2)
    <div class="form-group mt-2">
        <label>Dependencia</label>

        @if($dependenciasDisponibles->isEmpty())
            <div class="alert alert-warning">
                No hay dependencias disponibles para asignar.
            </div>
        @else
            <select wire:model.live="id_depen" class="form-control">
                <option value="">Seleccione dependencia</option>

                @foreach($dependenciasDisponibles as $dep)
                    <option value="{{ $dep->id_depen }}">
                        {{ $dep->nombre_depen }}
                    </option>
                @endforeach
            </select>
        @endif

        @error('id_depen')
            <small class="text-danger">{{ $message }}</small>
        @enderror
    </div>
@endif
                    <div class="form-group">
                        <label for="estado_usr"></label>
                        <select wire:model="estado_usr" class="form-control">
    <option value="">Seleccione estado</option>
    <option value="Activo">Activo</option>
    <option value="Inactivo">Inactivo</option>
</select>

@error('estado_usr')
    <small class="text-danger">{{ $message }}</small>
@enderror

                    </div>
                    <div class="form-group">
                        <label for="telefono_usr"></label>
                        <input wire:model.defer="telefono_usr" type="text" class="form-control" id="telefono_usr" placeholder="Telefono Usr">@error('telefono_usr') <span class="error text-danger">{{ $message }}</span> @enderror
                    </div>

                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary close-btn" data-bs-dismiss="modal">Close</button>
                <button type="button" wire:click.prevent="save()" class="btn btn-primary">{{ $selected_id ? 'Update' : 'Create' }}</button>
            </div>
        </div>
    </div>
</div>