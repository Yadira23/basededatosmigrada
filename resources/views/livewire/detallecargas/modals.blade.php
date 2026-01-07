<!-- Modal -->
<div wire:ignore.self class="modal fade" id="DataModal" data-bs-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="DataModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="DataModalLabel">{{ $selected_id ? 'Update Detalle Carga' : 'Create Detalle Carga' }}</h5>
                <button wire:click.prevent="cancel()" type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
           <div class="modal-body">
				<form>
                @if ($selected_id)
                    <input type="hidden" wire:model="selected_id">
                @endif
                    <div class="form-group">
                        <input wire:model.live="id_detalle" type="text" class="form-control" id="id_detalle" placeholder="Id Detalle">@error('id_detalle') <span class="error text-danger">{{ $message }}</span> @enderror
                    </div>
                    <div class="form-group">
                        <input wire:model.live="id_carga" type="text" class="form-control" id="id_carga" placeholder="Id Carga">@error('id_carga') <span class="error text-danger">{{ $message }}</span> @enderror
                    </div>
                    <div class="form-group">
                        <input wire:model.live="id_ind" type="text" class="form-control" id="id_ind" placeholder="Id Ind">@error('id_ind') <span class="error text-danger">{{ $message }}</span> @enderror
                    </div>
                    <div class="form-group">
                        <input wire:model.live="id_region" type="text" class="form-control" id="id_region" placeholder="Id Region">@error('id_region') <span class="error text-danger">{{ $message }}</span> @enderror
                    </div>
                    <div class="form-group">
                        <input wire:model.live="id_mun" type="text" class="form-control" id="id_mun" placeholder="Id Mun">@error('id_mun') <span class="error text-danger">{{ $message }}</span> @enderror
                    </div>
                    <div class="form-group">
                        <input wire:model.live="periodo_det" type="text" class="form-control" id="periodo_det" placeholder="Periodo Det">@error('periodo_det') <span class="error text-danger">{{ $message }}</span> @enderror
                    </div>
                    <div class="form-group">
                        <input wire:model.live="ejercicio_det" type="text" class="form-control" id="ejercicio_det" placeholder="Ejercicio Det">@error('ejercicio_det') <span class="error text-danger">{{ $message }}</span> @enderror
                    </div>
                    <div class="form-group">
                        <input wire:model.live="fecha_registro_det" type="text" class="form-control" id="fecha_registro_det" placeholder="Fecha Registro Det">@error('fecha_registro_det') <span class="error text-danger">{{ $message }}</span> @enderror
                    </div>
                    <div class="form-group">
                        <input wire:model.live="fuente_det" type="text" class="form-control" id="fuente_det" placeholder="Fuente Det">@error('fuente_det') <span class="error text-danger">{{ $message }}</span> @enderror
                    </div>
                    <div class="form-group">
                        <input wire:model.live="valor_det" type="text" class="form-control" id="valor_det" placeholder="Valor Det">@error('valor_det') <span class="error text-danger">{{ $message }}</span> @enderror
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