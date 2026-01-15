<!-- Modal -->
<div wire:ignore.self class="modal fade" id="DataModal" data-bs-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="DataModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="DataModalLabel">{{ $selected_id ? 'Actualizar Carga' : 'Nueva Carga' }}</h5>
                <button wire:click.prevent="cancel()" type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body">
                <form>

                    {{-- FOLIO AUTOMÁTICO --}}
                    <div class="form-group">
                        <label>Folio Único</label>
                        <input type="text" class="form-control" value="{{ $folioUnico_carga }}" disabled>
                        @error('folioUnico_carga') <span class="error text-danger">{{ $message }}</span> @enderror
                    </div>

                    {{-- FECHA AUTOMÁTICA --}}
                    <div class="form-group">
                        <label>Fecha</label>
                        <input type="date" class="form-control" wire:model="fecha_carga" disabled>
                        @error('fecha_carga') <span class="error text-danger">{{ $message }}</span> @enderror
                    </div>

                    {{-- PERIODO --}}
                    <div class="form-group">
                        <label>Periodo</label>
                        <select wire:model="periodo" class="form-control">
                            <option value="">-- Selecciona un periodo --</option>
                            <option value="Mensual">Mensual</option>
                            <option value="Trimestral">Trimestral</option>
                            <option value="Semestral">Semestral</option>
                            <option value="Anual">Anual</option>
                        </select>
                        @error('periodo') <span class="error text-danger">{{ $message }}</span> @enderror
                    </div>

                    {{-- FORMULARIO --}}
                    <div class="form-group">
                        <label>Formulario</label>
                        <select wire:model="id_form" class="form-control">
                            <option value="">-- Selecciona un formulario --</option>
                            @foreach($formularios as $formulario)
                            <option value="{{ $formulario->id_form }}">{{ $formulario->titulo_form }}</option>
                            @endforeach
                        </select>
                        @error('id_form') <span class="error text-danger">{{ $message }}</span> @enderror
                    </div>

                    {{-- DESCRIPCIÓN DE LA CARGA --}}
                    <div class="form-group">
                        <label>Descripción</label>
                        <input type="text" class="form-control" wire:model="descripcion_env" placeholder="Descripción de la carga">
                        @error('descripcion_env') <span class="error text-danger">{{ $message }}</span> @enderror
                    </div>

                    {{-- ESTADO INICIAL --}}
                    <input type="hidden" wire:model="status_env">

                </form>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary close-btn" data-bs-dismiss="modal">Cerrar</button>
                <button type="button" wire:click.prevent="save()" class="btn btn-primary">{{ $selected_id ? 'Actualizar' : 'Crear' }}</button>
            </div>

        </div>
    </div>
</div>