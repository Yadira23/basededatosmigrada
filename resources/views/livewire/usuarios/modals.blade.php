<!-- Modal -->
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
                        <input wire:model.live="usuario_usr" type="text" class="form-control" id="usuario_usr" placeholder="Usuario Usr">@error('usuario_usr') <span class="error text-danger">{{ $message }}</span> @enderror
                    </div>
                    <div class="form-group">
                        <label for="nombre_usr"></label>
                        <input wire:model.live="nombre_usr" type="text" class="form-control" id="nombre_usr" placeholder="Nombre Usr">@error('nombre_usr') <span class="error text-danger">{{ $message }}</span> @enderror
                    </div>
                    <div class="form-group">
                        <label for="apellido_paterno"></label>
                        <input wire:model.live="apellido_paterno" type="text" class="form-control" id="apellido_paterno" placeholder="Apellido Paterno">@error('apellido_paterno') <span class="error text-danger">{{ $message }}</span> @enderror
                    </div>
                    <div class="form-group">
                        <label for="apellido_materno"></label>
                        <input wire:model.live="apellido_materno" type="text" class="form-control" id="apellido_materno" placeholder="Apellido Materno">@error('apellido_materno') <span class="error text-danger">{{ $message }}</span> @enderror
                    </div>
                    <div class="form-group">
                        <label for="email_usr"></label>
                        <input wire:model.live="email_usr" type="text" class="form-control" id="email_usr" placeholder="Email Usr">@error('email_usr') <span class="error text-danger">{{ $message }}</span> @enderror
                    </div>
                    <div class="form-group">
    <label for="password">Contraseña</label>
    <input type="password" wire:model.defer="password" class="form-control">
    @error('password') <span class="text-danger">{{ $message }}</span> @enderror
</div>

                    <div class="form-group">
    <label>Dependencia</label>
    <select wire:model="id_depen" class="form-control">
        <option value="">Seleccione dependencia</option>
        @foreach($dependencias as $dep)
            <option value="{{ $dep->id_depen }}">
                {{ $dep->nombre_depen }}
            </option>
        @endforeach
    </select>
    @error('id_depen') <span class="text-danger">{{ $message }}</span> @enderror
</div>
                    <div class="form-group">
    <label>Rol</label>
    <select wire:model="id_rol" class="form-control">
        <option value="">Seleccione rol</option>
        @foreach($roles as $rol)
            <option value="{{ $rol->id_rol }}">
                {{ $rol->nombre_rol }}
            </option>
        @endforeach
    </select>
    @error('id_rol') <span class="text-danger">{{ $message }}</span> @enderror
</div>

                    <div class="form-group">
                        <label for="estado_usr"></label>
                        <input wire:model.live="estado_usr" type="text" class="form-control" id="estado_usr" placeholder="Estado Usr">@error('estado_usr') <span class="error text-danger">{{ $message }}</span> @enderror
                    </div>
                    <div class="form-group">
                        <label for="telefono_usr"></label>
                        <input wire:model.live="telefono_usr" type="text" class="form-control" id="telefono_usr" placeholder="Telefono Usr">@error('telefono_usr') <span class="error text-danger">{{ $message }}</span> @enderror
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