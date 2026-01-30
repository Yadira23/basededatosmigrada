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
                        <label class="form-label">Nombre</label>
                        <input wire:model.defer="nombre_anexo" type="text" class="form-control" placeholder="Ej. Plantilla Enero 2026">
                        @error('nombre_anexo') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>

                    <!-- Tipo (AHORA: plantilla / guia) -->
                    <div class="form-group mb-2">
                        <label class="form-label">Tipo de anexo</label>
                        <select wire:model="tipo_anexo" class="form-control">
                            <option value="">-- Selecciona tipo --</option>
                            <option value="plantilla">Plantilla (Excel/CSV)</option>
                            <option value="guia">Guía (PDF/Word/Imagen)</option>
                        </select>
                        @error('tipo_anexo') <span class="text-danger">{{ $message }}</span> @enderror

                        {{-- ayuda visual --}}
                        @if($tipo_anexo === 'plantilla')
                            <small class="text-muted">Sugerido: .xlsx / .xls / .csv</small>
                        @elseif($tipo_anexo === 'guia')
                            <small class="text-muted">Sugerido: .pdf / .docx / imagen</small>
                        @endif
                    </div>

                    <!-- Archivo -->
                    <div class="form-group mb-2">
                        <label class="form-label">Archivo</label>
                        <input type="file" wire:model="archivo" class="form-control">
                        @error('archivo') <span class="text-danger">{{ $message }}</span> @enderror
                        <div wire:loading wire:target="archivo" class="text-muted mt-1">Uploading...</div>
                    </div>

                    <!-- Guía (texto opcional / instrucciones) -->
                    <div class="form-group mb-2">
                        <label class="form-label">Instrucciones (opcional)</label>
                        <input wire:model.defer="guia_anexo" type="text" class="form-control" placeholder="Ej. Llenar por región, no dejar celdas vacías">
                        @error('guia_anexo') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>

                    <!-- Fin / Propósito -->
                    <div class="form-group mb-2">
                        <label class="form-label">Fin / Propósito</label>
                        <input wire:model.defer="fin_proposito_anexo" type="text" class="form-control" placeholder="Ej. Plantilla oficial para capturar datos del indicador">
                        @error('fin_proposito_anexo') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>

                    <!-- Formulario -->
                    <div class="form-group mb-2">
                        <label class="form-label">Formulario</label>
                        <select wire:model="id_form" class="form-control">
                            <option value="">-- Selecciona formulario --</option>
                            @foreach($formularios as $form)
                                <option value="{{ $form->id_form }}">{{ $form->titulo_form }}</option>
                            @endforeach
                        </select>
                        @error('id_form') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>

                    <!-- Indicador (AHORA DENTRO DEL FORM) -->
                    <div class="form-group mb-2">
                        <label class="form-label">Indicador</label>
                        <select wire:model="id_ind" class="form-control">
                            <option value="">-- Selecciona indicador --</option>
                            @foreach($indicadores as $ind)
                                <option value="{{ $ind->id_ind }}">{{ $ind->nombre_ind }}</option>
                            @endforeach
                        </select>
                        @error('id_ind') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>

                </form>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" wire:click.prevent="cancel()">Close</button>

                {{-- puedes dejar wire:click o cambiar a type="submit" si quieres --}}
                <button type="button" wire:click.prevent="save()" class="btn btn-primary">
                    {{ $selected_id ? 'Update' : 'Create' }}
                </button>
            </div>

        </div>
    </div>
</div>