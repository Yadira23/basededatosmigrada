<!-- Modal -->
<div wire:ignore.self class="modal fade" id="DataModal" data-bs-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="DataModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="DataModalLabel">{{ $selected_id ? 'Update Indicador' : 'Create Indicador' }}</h5>
                <button wire:click.prevent="cancel()" type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
           <div class="modal-body">
				<form>
                @if ($selected_id)
                    <input type="hidden" wire:model="selected_id">
                @endif
                    
                    <div class="form-group">
                        <input wire:model.defer="nombre_ind" type="text" class="form-control" id="nombre_ind" placeholder="Nombre Ind">@error('nombre_ind') <span class="error text-danger">{{ $message }}</span> @enderror
                    </div>
                    <div class="form-group">
                        <input wire:model.defer="definicion_ind" type="text" class="form-control" id="definicion_ind" placeholder="Definicion Ind">@error('definicion_ind') <span class="error text-danger">{{ $message }}</span> @enderror
                    </div>
                    <div class="form-group">
                        <input wire:model.defer="formula_ind" type="text" class="form-control" id="formula_ind" placeholder="Formula Ind">@error('formula_ind') <span class="error text-danger">{{ $message }}</span> @enderror
                    </div>
                    <div class="form-group">
    <label for="tendencia_ind">Tendencia del indicador</label>
    <select wire:model.defer="tendencia_ind" class="form-control" id="tendencia_ind">
        <option value="">-- Selecciona una opción --</option>
        <option value="POSITIVA">Positiva</option>
        <option value="NEGATIVA">Negativa</option>
    </select>
    @error('tendencia_ind') 
        <span class="text-danger">{{ $message }}</span> 
    @enderror
</div>

                    <div class="form-group">
                        <input wire:model.defer="restriccion_ind" type="text" class="form-control" id="restriccion_ind" placeholder="Restriccion Ind">@error('restriccion_ind') <span class="error text-danger">{{ $message }}</span> @enderror
                    </div>
                    <div class="form-group">
                        <input wire:model.defer="formato_ind" type="text" class="form-control" id="formato_ind" placeholder="Formato Ind">@error('formato_ind') <span class="error text-danger">{{ $message }}</span> @enderror
                    </div>
                    <div class="form-group">
                        <input wire:model.defer="unidadmedida_ind" type="text" class="form-control" id="unidadmedida_ind" placeholder="Unidadmedida Ind">@error('unidadmedida_ind') <span class="error text-danger">{{ $message }}</span> @enderror
                    </div>
                    <div class="form-group">
                        <input wire:model.defer="meta_ind" type="text" class="form-control" id="meta_ind" placeholder="Meta Ind">@error('meta_ind') <span class="error text-danger">{{ $message }}</span> @enderror
                    </div>
                    <div class="form-group form-check">
    <input
        type="checkbox"
        class="form-check-input"
        id="requerido_ind"
        wire:model="requerido_ind"
    >
    <label class="form-check-label" for="requerido_ind">
        Requerido
    </label>

    @error('requerido_ind')
        <span class="text-danger">{{ $message }}</span>
    @enderror
</div>

                    <div class="form-group">
    <label>Status</label>
    <select wire:model.defer="status_ind" class="form-control">
        <option value="">Seleccione</option>
        <option value="Activo">Activo</option>
        <option value="Inactivo">Inactivo</option>
    </select>
    @error('status_ind') <span class="text-danger">{{ $message }}</span> @enderror
</div>


                    <div class="form-group">
    <label>Periodo</label>
    <select wire:model="periodo_ind" class="form-control">
        <option value="">Seleccione</option>
        <option value="Mensual">Mensual</option>
        <option value="Trimestral">Trimestral</option>
        <option value="Semestral">Semestral</option>
        <option value="Anual">Anual</option>
    </select>
    @error('periodo_ind') <span class="text-danger">{{ $message }}</span> @enderror
</div>

                    <div class="form-group">
                        <label for="etiquetas_ind"></label>
                        <input wire:model.defer="etiquetas_ind" type="text" class="form-control" id="etiquetas_ind" placeholder="Etiquetas Ind">@error('etiquetas_ind') <span class="error text-danger">{{ $message }}</span> @enderror
                    </div>
                    <div class="form-group">
                        <label for="fuenteverificacion_ind"></label>
                        <input wire:model.defer="fuenteverificacion_ind" type="text" class="form-control" id="fuenteverificacion_ind" placeholder="Fuenteverificacion Ind">@error('fuenteverificacion_ind') <span class="error text-danger">{{ $message }}</span> @enderror
                    </div>
                    <div class="form-group">
    <label>Formulario</label>
    <select wire:model="id_form" class="form-control">
        <option value="">Seleccione</option>
        @foreach($formularios as $form)
            <option value="{{ $form->id_form }}">
                {{ $form->nombre_form ?? 'Formulario '.$form->id_form }}
            </option>
        @endforeach
    </select>
    @error('id_form') <span class="text-danger">{{ $message }}</span> @enderror
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