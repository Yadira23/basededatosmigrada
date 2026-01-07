<!-- Modal -->
<div wire:ignore.self class="modal fade" id="DataModal" data-bs-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="DataModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="DataModalLabel">{{ $selected_id ? 'Update Formulario' : 'Create Formulario' }}</h5>
                <button wire:click.prevent="cancel()" type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
           <div class="modal-body">
				<form>
                @if ($selected_id)
                    <input type="hidden" wire:model="selected_id">
                @endif
                    <div class="form-group">
                        <label for="titulo_form"></label>
                        <input wire:model.defer="titulo_form" type="text" class="form-control" id="titulo_form" placeholder="Titulo Form">@error('titulo_form') <span class="error text-danger">{{ $message }}</span> @enderror
                    </div>
                    <div class="form-group">
    <label>Fecha de creación</label>
    <input
        type="date"
        wire:model.defer="fecha_creacion_form"
        class="form-control"
    >
    @error('fecha_creacion_form')
        <span class="text-danger">{{ $message }}</span>
    @enderror
</div>

                    <div class="form-group">
                        <label for="descripcion_form"></label>
                        <input wire:model.defer="descripcion_form" type="text" class="form-control" id="descripcion_form" placeholder="Descripcion Form">@error('descripcion_form') <span class="error text-danger">{{ $message }}</span> @enderror
                    </div>
                    <div class="form-group">
                        <label for="boton_accion_form"></label>
                        <input wire:model.defer="boton_accion_form" type="text" class="form-control" id="boton_accion_form" placeholder="Boton Accion Form">@error('boton_accion_form') <span class="error text-danger">{{ $message }}</span> @enderror
                    </div>
                    <div class="form-group">
                        <label for="secciones_form"></label>
                        <input wire:model.defer="secciones_form" type="text" class="form-control" id="secciones_form" placeholder="Secciones Form">@error('secciones_form') <span class="error text-danger">{{ $message }}</span> @enderror
                    </div>
                    <div class="form-group">
    <label>Periodo</label>
    <select wire:model="periodo_form" class="form-control">
        <option value="">Seleccione</option>
        <option value="Mensual">Mensual</option>
        <option value="Trimestral">Trimestral</option>
        <option value="Semestral">Semestral</option>
        <option value="Anual">Anual</option>
    </select>
    @error('periodo_form') <span class="text-danger">{{ $message }}</span> @enderror
</div>
                    <div class="form-group">
    <label>Dependencia</label>
    <select wire:model="id_depen" class="form-control">
        <option value="">Seleccione</option>
        @foreach($dependencias as $dep)
            <option value="{{ $dep->id_depen }}">
                {{ $dep->nombre_depen }}
            </option>
        @endforeach
    </select>
    @error('id_depen') <span class="text-danger">{{ $message }}</span> @enderror
</div>

                    <div class="form-group">
    <label>Usuario</label>
    <select wire:model="id_usr" class="form-control">
        <option value="">Seleccione</option>
        @foreach($usuarios as $usr)
            <option value="{{ $usr->id_usuario }}">
                {{ $usr->usuario_usr }}
            </option>
        @endforeach
    </select>

    @error('id_usr')
        <span class="text-danger">{{ $message }}</span>
    @enderror
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