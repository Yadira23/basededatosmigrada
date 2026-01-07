<!-- Modal -->
<div wire:ignore.self class="modal fade" id="DataModal" data-bs-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="DataModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="DataModalLabel">{{ $selected_id ? 'Update Carga' : 'Create Carga' }}</h5>
                <button wire:click.prevent="cancel()" type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
           <div class="modal-body">
				<form>
                @if ($selected_id)
                    <input type="hidden" wire:model="selected_id">
                @endif
                    <div class="form-group">
                        <label for="folioUnico_carga"></label>
                        <input wire:model="folioUnico_carga" type="text" class="form-control" id="folioUnico_carga" placeholder="Foliounico Carga">@error('folioUnico_carga') <span class="error text-danger">{{ $message }}</span> @enderror
                    </div>
                    <div class="form-group">
                        <label for="fecha_carga"></label>
                        <input wire:model="fecha_carga" type="date" class="form-control" id="fecha_carga" placeholder="Fecha Carga">@error('fecha_carga') <span class="error text-danger">{{ $message }}</span> @enderror
                    </div>
                    <div class="form-group">
                        <label for="periodo"></label>
                        <input wire:model="periodo" type="text" class="form-control" id="periodo" placeholder="Periodo">@error('periodo') <span class="error text-danger">{{ $message }}</span> @enderror
                    </div>
                    <div class="form-group">
                        <label for="status_env"></label>
                        <input wire:model="status_env" type="text" class="form-control" id="status_env" placeholder="Status Env">@error('status_env') <span class="error text-danger">{{ $message }}</span> @enderror
                    </div>
                    <div class="form-group">
                        <label for="descripcion_env"></label>
                        <input wire:model="descripcion_env" type="text" class="form-control" id="descripcion_env" placeholder="Descripcion Env">@error('descripcion_env') <span class="error text-danger">{{ $message }}</span> @enderror
                    </div>
                    <div class="form-group">
                        <label for="observacion_env"></label>
                        <input wire:model="observacion_env" type="text" class="form-control" id="observacion_env" placeholder="Observacion Env">@error('observacion_env') <span class="error text-danger">{{ $message }}</span> @enderror
                    </div>
                    <div class="form-group">
    <label for="id_form">Formulario</label>
    <select wire:model="id_form" class="form-control" id="id_form">
        <option value="">-- Selecciona un formulario --</option>
        @foreach($formularios as $formulario)
            <option value="{{ $formulario->id_form }}">
                {{ $formulario->nombre_form }}
            </option>
        @endforeach
    </select>
    @error('id_form') <span class="error text-danger">{{ $message }}</span> @enderror
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