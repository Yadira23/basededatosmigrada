<div wire:ignore.self class="modal fade" id="ObservationModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title">Agregar observación</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                <textarea
                    wire:model.defer="observacion_env"
                    class="form-control"
                    rows="4"
                    placeholder="Escribe la observación..."></textarea>

                @error('observacion_env')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>

            <div class="modal-footer">
                <button class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button class="btn btn-primary" wire:click="saveObservation">
                    Guardar observación
                </button>
            </div>

        </div>
    </div>
</div>
