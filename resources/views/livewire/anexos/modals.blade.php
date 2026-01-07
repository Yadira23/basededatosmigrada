<!-- Modal -->
<div wire:ignore.self class="modal fade" id="DataModal" data-bs-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="DataModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="DataModalLabel">{{ $selected_id ? 'Update Anexo' : 'Create Anexo' }}</h5>
                <button wire:click.prevent="cancel()" type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
           <div class="modal-body">
				<form>
                @if ($selected_id)
                    <input type="hidden" wire:model="selected_id">
                @endif
                    <div class="form-group">
                        <input wire:model.live="nombre_anexo" type="text" class="form-control" id="nombre_anexo" placeholder="Nombre Anexo">@error('nombre_anexo') <span class="error text-danger">{{ $message }}</span> @enderror
                    </div>
                    <select wire:model.live="tipo_anexo" class="form-control">
    <option value="">-- Selecciona tipo --</option>
    <option value="PDF">PDF</option>
    <option value="WORD">Word</option>
    <option value="EXCEL">Excel</option>
    <option value="IMAGEN">Imagen</option>
    <option value="OTRO">Otro</option>
</select>

                    <div class="form-group">
                        <input wire:model.live="peso_anexo" type="number" class="form-control" id="peso_anexo" placeholder="Peso Anexo">@error('peso_anexo') <span class="error text-danger">{{ $message }}</span> @enderror
                    </div>
                    <div class="form-group">
                        <input wire:model.live="guia_anexo" type="text" class="form-control" id="guia_anexo" placeholder="Guia Anexo">@error('guia_anexo') <span class="error text-danger">{{ $message }}</span> @enderror
                    </div>
                    <div class="form-group">
                        <input wire:model.live="fin_proposito_anexo" type="text" class="form-control" id="fin_proposito_anexo" placeholder="Fin Proposito Anexo">@error('fin_proposito_anexo') <span class="error text-danger">{{ $message }}</span> @enderror
                    </div>
                    <div class="form-group">
                        <input wire:model.live="fecha_subida_anexo" type="date" class="form-control" id="fecha_subida_anexo" placeholder="Fecha Subida Anexo">@error('fecha_subida_anexo') <span class="error text-danger">{{ $message }}</span> @enderror
                    </div>
                    <div class="form-group">
                        <input wire:model.live="ruta_archivo_anexo" type="text" class="form-control" id="ruta_archivo_anexo" placeholder="Ruta Archivo Anexo">@error('ruta_archivo_anexo') <span class="error text-danger">{{ $message }}</span> @enderror
                    </div>
                    <select wire:model.live="id_form" class="form-control">
    <option value="">-- Selecciona formulario --</option>
    @foreach($formularios as $form)
        <option value="{{ $form->id_form }}">
            {{ $form->nombre_form }}
        </option>
    @endforeach
</select>


                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary close-btn" data-bs-dismiss="modal">Close</button>
                <button type="button" wire:click.prevent="save()" class="btn btn-primary">{{ $selected_id ? 'Update' : 'Create' }}</button>
            </div>
        </div>
    </div>
</div>