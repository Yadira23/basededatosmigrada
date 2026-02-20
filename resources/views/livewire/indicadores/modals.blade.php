<!-- Modal -->
<div wire:ignore.self class="modal fade" id="DataModal" data-bs-backdrop="static" tabindex="-1" role="dialog"
    aria-labelledby="DataModalLabel" aria-hidden="true">

    <div class="modal-dialog" role="document">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title" id="DataModalLabel">
                    {{ $selected_id ? 'Actualizar indicador' : 'Crear indicador' }}
                </h5>

                <button wire:click.prevent="cancel()" type="button" class="btn-close" data-bs-dismiss="modal"
                    aria-label="Close"></button>
            </div>

            <div class="modal-body">
                <form>
                    @if ($selected_id)
                        <input type="hidden" wire:model="selected_id">
                    @endif

                    {{-- NOMBRE --}}
                    <div class="mb-3">
                        <label for="nombre_ind" class="form-label fw-semibold">Nombre del indicador</label>
                        <input wire:model.defer="nombre_ind" type="text" class="form-control" id="nombre_ind"
                            placeholder="Ej. Servicios turísticos atendidos">
                        <small class="text-muted">
                            Nombre corto y claro. Este nombre será visible en listados.
                        </small>
                        @error('nombre_ind') <span class="error text-danger">{{ $message }}</span> @enderror
                    </div>

                    {{-- DEFINICIÓN --}}
                    <div class="mb-3">
                        <label for="definicion_ind" class="form-label fw-semibold">Definición / descripción</label>
                        <input wire:model.defer="definicion_ind" type="text" class="form-control" id="definicion_ind"
                            placeholder="Describe qué mide el indicador y su propósito">
                        <small class="text-muted">
                            Describe qué mide el indicador y para qué se utiliza.
                        </small>
                        @error('definicion_ind') <span class="error text-danger">{{ $message }}</span> @enderror
                    </div>

                    {{-- ¿TIENE FÓRMULA? --}}
                    <div class="mb-3">
                        <label class="form-label fw-semibold">¿El indicador tiene fórmula?</label>
                        <select wire:model.live="tiene_formula" class="form-control">
                            <option value="0">No</option>
                            <option value="1">Sí</option>
                        </select>
                        <small class="text-muted">
                            Selecciona “Sí” si el resultado se obtiene mediante un cálculo (ej. A/B*100).
                        </small>
                    </div>

                    {{-- FÓRMULA --}}
                    @if($tiene_formula)
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Fórmula del indicador</label>
                            <input wire:model.defer="formula_ind" type="text" class="form-control"
                                placeholder="Ej. (Casos atendidos / Casos programados) * 100">
                            <small class="text-muted">
                                Escribe la fórmula en texto. Debe entenderse fácilmente por cualquier usuario.
                            </small>
                            @error('formula_ind') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>
                    @endif

                    {{-- TENDENCIA --}}
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Tendencia (interpretación)</label>
                        <select wire:model.defer="tendencia_ind" class="form-control">
                            <option value="">Seleccione</option>
                            <option value="ascendente">Ascendente</option>
                            <option value="descendente">Descendente</option>
                            <option value="estable">Estable</option>
                        </select>
                        <small class="text-muted">
                            Indica si el indicador mejora al subir, al bajar o si se busca mantener un valor.
                        </small>
                        @error('tendencia_ind') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>

                    {{-- RESTRICCIÓN --}}
                    <div class="mb-3">
                        <label for="restriccion_ind" class="form-label fw-semibold">Restricción / notas (opcional)</label>
                        <input wire:model.defer="restriccion_ind" type="text" class="form-control" id="restriccion_ind"
                            placeholder="Ej. Solo aplica a población mayor de 18 años">
                        <small class="text-muted">
                            Regla que valida rangos, tipo de dato o coherencia de los valores.
                        </small>
                        @error('restriccion_ind') <span class="error text-danger">{{ $message }}</span> @enderror
                    </div>

                    {{-- FORMATO --}}
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Formato del indicador</label>
                        <select wire:model="formato_ind" class="form-control">
                            <option value="">Seleccione</option>
                            <option value="porcentaje">Porcentaje</option>
                            <option value="cantidad">Cantidad</option>
                            <option value="promedio">Promedio</option>
                        </select>
                        <small class="text-muted">
                            Define el tipo de valor que se capturará.
                        </small>
                        @error('formato_ind') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>

                    {{-- REQUERIDO --}}
                    <div class="mb-3 form-check">
                        <input type="checkbox" class="form-check-input" id="requerido_ind" wire:model.live="requerido_ind">
                        <label class="form-check-label fw-semibold" for="requerido_ind">Indicador requerido</label>
                        <small class="text-muted d-block">
                            Si está activo, se solicitará una meta del indicador (cuando aplique).
                        </small>
                        @error('requerido_ind') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>

                    {{-- META DEL INDICADOR --}}
                    @if($requerido_ind)
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Meta del indicador</label>
                            <input wire:model.defer="meta_ind" type="number" class="form-control" min="0" step="0.01"
                                placeholder="Ej. 85"
                                @if($formato_ind==='porcentaje') max="100" @endif>
                            <small class="text-muted">
                                Valor objetivo esperado. Si el formato es porcentaje, el máximo es 100.
                            </small>
                            @error('meta_ind') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>
                    @endif

                    {{-- ESTATUS --}}
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Estatus del indicador</label>
                        <select wire:model="status_ind" class="form-control">
                            <option value="">Seleccione</option>
                            <option value="1">Activo</option>
                            <option value="0">Inactivo</option>
                        </select>
                        <small class="text-muted">
                            Activo: se podrá usar y asignar. Inactivo: no se mostrará para captura.
                        </small>
                        @error('status_ind') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>

                    {{-- PERIODO --}}
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Periodo del indicador</label>
                        <select wire:model.defer="periodo_ind" class="form-control">
                            <option value="">Seleccione</option>
                            <option value="mensual">Mensual</option>
                            <option value="trimestral">Trimestral</option>
                            <option value="semestral">Semestral</option>
                            <option value="anual">Anual</option>
                        </select>
                        <small class="text-muted">
                            Define cada cuánto se reportará/capturará información para este indicador.
                        </small>
                        @error('periodo_ind') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>

                    {{-- ETIQUETAS --}}
                    <div class="mb-3">
                        <label for="etiquetas_ind" class="form-label fw-semibold">Etiquetas (búsqueda)</label>
                        <input wire:model.defer="etiquetas_ind" type="text" class="form-control" id="etiquetas_ind"
                            placeholder="Ej. turismo, servicios, atención">
                        <small class="text-muted">
                            Palabras clave separadas por coma para clasificar y organizar el indicador.
                        </small>
                        @error('etiquetas_ind') <span class="error text-danger">{{ $message }}</span> @enderror
                    </div>

                    {{-- FUENTE DE VERIFICACIÓN --}}
                    <div class="mb-3">
                        <label for="fuenteverificacion_ind" class="form-label fw-semibold">Fuente de verificación</label>
                        <input wire:model.defer="fuenteverificacion_ind" type="text" class="form-control"
                            id="fuenteverificacion_ind"
                            placeholder="Ej. INEGI, reportes internos, sistema X, actas, evidencia documental">
                        <small class="text-muted">
                            Indica de dónde proviene el dato o qué evidencia valida la captura.
                        </small>
                        @error('fuenteverificacion_ind') <span class="error text-danger">{{ $message }}</span> @enderror
                    </div>

                </form>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary close-btn" data-bs-dismiss="modal">
                    Cerrar
                </button>
                <button type="button" wire:click.prevent="save()" class="btn btn-primary">
                    {{ $selected_id ? 'Actualizar' : 'Crear' }}
                </button>
            </div>

        </div>
    </div>
</div>
