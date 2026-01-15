<!-- Modal -->
<div wire:ignore.self class="modal fade" id="DataModal" data-bs-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="DataModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="DataModalLabel">{{ $selected_id ? 'Update Anexo' : 'Create Anexo' }}</h5>
                <button wire:click.prevent="cancel()" type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form wire:submit.prevent="save" enctype="multipart/form-data">
                    @if ($selected_id)
                    <input type="hidden" wire:model="selected_id">
                    @endif

                    <!-- Nombre -->
                    <div class="form-group mb-2">
                        <input wire:model.defer="nombre_anexo" type="text" class="form-control" placeholder="Nombre Anexo">
                        @error('nombre_anexo') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>

                    <!-- Tipo -->
                    <div class="form-group mb-2">
                        <select wire:model="tipo_anexo" class="form-control">
                            <option value="">-- Selecciona tipo --</option>
                            <option value="PDF">PDF</option>
                            <option value="WORD">Word</option>
                            <option value="EXCEL">Excel</option>
                            <option value="IMAGEN">Imagen</option>
                            <option value="OTRO">Otro</option>
                        </select>
                        @error('tipo_anexo') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>

                    <!-- Archivo -->
                    <div class="form-group mb-2">
                        <input type="file" wire:model="archivo" class="form-control">
                        @error('archivo') <span class="text-danger">{{ $message }}</span> @enderror
                        <div wire:loading wire:target="archivo">Uploading...</div>
                    </div>

                    <!-- Guía -->
                    <div class="form-group mb-2">
                        <input wire:model.defer="guia_anexo" type="text" class="form-control" placeholder="Guia Anexo">
                        @error('guia_anexo') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>

                    <!-- Fin / Propósito -->
                    <div class="form-group mb-2">
                        <input wire:model.defer="fin_proposito_anexo" type="text" class="form-control" placeholder="Fin Proposito Anexo">
                        @error('fin_proposito_anexo') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>

                    <!-- Formulario -->
                    <div class="form-group mb-2">
                        <select wire:model="id_form" class="form-control">
                            <option value="">-- Selecciona formulario --</option>
                            @foreach($formularios as $form)
                            <option value="{{ $form->id_form }}">{{ $form->titulo_form }}</option>
                            @endforeach
                        </select>
                        @error('id_form') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>
                </form>
            </div>
            <!-- Indicador -->
            <div class="form-group mb-2">
                <select wire:model="id_ind" class="form-control">
                    <option value="">-- Selecciona indicador --</option>
                    @foreach($indicadores as $ind)
                    <option value="{{ $ind->id_ind }}">
                        {{ $ind->nombre_ind }}
                    </option>
                    @endforeach
                </select>
                @error('id_ind') <span class="text-danger">{{ $message }}</span> @enderror
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" wire:click.prevent="cancel()">Close</button>
                <button type="button" wire:click.prevent="save()" class="btn btn-primary">{{ $selected_id ? 'Update' : 'Create' }}</button>
            </div>
        </div>
    </div>
</div>