<!-- Modal -->
@if (session()->has('error'))
    <div class="alert alert-danger">
        {{ session('error') }}
    </div>
@endif

<div wire:ignore.self class="modal fade" id="DataModal" data-bs-backdrop="static" tabindex="-1" role="dialog"
    aria-labelledby="DataModalLabel" aria-hidden="true">

    <div class="modal-dialog" role="document">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title" id="DataModalLabel">
                    {{ $selected_id ? 'Actualizar formulario' : 'Crear formulario' }}
                </h5>
                <button wire:click.prevent="cancel()" type="button" class="btn-close" data-bs-dismiss="modal"
                    aria-label="Close"></button>
            </div>

            <div class="modal-body">
                <form>
                    @if ($selected_id)
                        <input type="hidden" wire:model="selected_id">
                    @endif

                    {{-- TÍTULO --}}
                    <div class="mb-3">
                        <label for="titulo_form" class="form-label fw-semibold">Título del formulario</label>
                        <input wire:model.defer="titulo_form" type="text" class="form-control" id="titulo_form"
                            placeholder="Ej. Captura trimestral de servicios">
                        <small class="text-muted">
                            Nombre identificable del formulario.
                        </small>
                        @error('titulo_form')
                            <span class="error text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    {{-- FECHA DE CREACIÓN --}}
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Fecha de creación</label>
                        <input type="date" wire:model.defer="fecha_creacion_form" class="form-control">
                        <small class="text-muted">
                            Fecha de referencia del formulario.
                        </small>
                        @error('fecha_creacion_form')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    {{-- DESCRIPCIÓN --}}
                    <div class="mb-3">
                        <label for="descripcion_form" class="form-label fw-semibold">Descripción del formulario</label>
                        <input wire:model.defer="descripcion_form" type="text" class="form-control"
                            id="descripcion_form" placeholder="Breve descripción del formulario">
                        <small class="text-muted">
                            Explica el propósito del formulario.
                        </small>
                        @error('descripcion_form')
                            <span class="error text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    {{-- BOTÓN ACCIÓN (OCULTO) --}}
                    <input wire:model.defer="boton_accion_form" type="hidden" id="boton_accion_form">

                    {{-- SECCIONES --}}
                    <div class="mb-3">
                        <label for="secciones_form" class="form-label fw-semibold">Secciones del formulario</label>
                        <input wire:model.defer="secciones_form" type="text" class="form-control" id="secciones_form"
                            placeholder="Ej. Datos generales, Resultados">
                        <small class="text-muted">
                            Define cómo se organiza el formulario.
                        </small>
                        @error('secciones_form')
                            <span class="error text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    {{-- PERIODO --}}
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Periodo</label>
                        <input type="text" class="form-control"
                            value="{{ $periodo_form ?: 'Selecciona un indicador' }}" readonly>
                        <small class="text-muted">
                            Se hereda automáticamente del indicador.
                        </small>
                    </div>

                    {{-- INDICADOR --}}
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Indicador asociado</label>

                        <select wire:model="id_ind" class="form-control" @disabled($indicadorBloqueado)>
                            <option value="">Seleccione</option>
                            @foreach ($indicadores ?? [] as $ind)
                                <option value="{{ $ind->id_ind }}">{{ $ind->nombre_ind }}</option>
                            @endforeach
                        </select>

                        @if ($indicadorBloqueado)
                            <small class="text-muted">
                                Este formulario se genera a partir del indicador seleccionado.
                            </small>
                        @else
                            <small class="text-muted">
                                Selecciona el indicador al que pertenecerá este formulario.
                            </small>
                        @endif

                        @error('id_ind')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    {{-- DEPENDENCIA --}}
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Dependencia responsable</label>
                        <select class="form-select" wire:model.defer="id_depen">
                            <option value="">Seleccione</option>
                            @foreach ($this->dependencias ?? [] as $dep)
                                <option value="{{ $dep->id_depen }}">{{ $dep->nombre_depen }}</option>
                            @endforeach
                        </select>
                        <small class="text-muted">
                            Dependencia que realizará la captura.
                        </small>
                        @error('id_depen')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
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
