<div class="container py-3">

    <h4 class="mb-2">Configuración de Captura</h4>
    <p class="text-muted">Estos parámetros controlan cuándo se permite capturar. Se leen desde base de datos.</p>

    @if (session()->has('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card">
        <div class="card-body">

            <div class="form-check form-switch mb-3">
                <input class="form-check-input" type="checkbox" id="modoPruebas" wire:model="modo_pruebas">
                <label class="form-check-label" for="modoPruebas">
                    Modo pruebas (permitir capturar aunque esté fuera de fechas)
                </label>
            </div>

            <div class="row g-2 mb-3">
                <div class="col-md-4">
                    <label class="form-label">Días de plazo adicional</label>
                    <input type="number" class="form-control" wire:model="dias_gracia" min="0">
                    @error('dias_gracia') <small class="text-danger">{{ $message }}</small> @enderror
                </div>
            </div>

            <hr>

            <h6 class="mb-2">Días de apertura por tipo de periodo</h6>

            <div class="row g-2">
                <div class="col-md-3">
                    <label class="form-label">Mensual</label>
                    <input type="number" class="form-control" wire:model="dias_mensual" min="1">
                    @error('dias_mensual') <small class="text-danger">{{ $message }}</small> @enderror
                </div>

                <div class="col-md-3">
                    <label class="form-label">Trimestral</label>
                    <input type="number" class="form-control" wire:model="dias_trimestral" min="1">
                    @error('dias_trimestral') <small class="text-danger">{{ $message }}</small> @enderror
                </div>

                <div class="col-md-3">
                    <label class="form-label">Semestral</label>
                    <input type="number" class="form-control" wire:model="dias_semestral" min="1">
                    @error('dias_semestral') <small class="text-danger">{{ $message }}</small> @enderror
                </div>

                <div class="col-md-3">
                    <label class="form-label">Anual</label>
                    <input type="number" class="form-control" wire:model="dias_anual" min="1">
                    @error('dias_anual') <small class="text-danger">{{ $message }}</small> @enderror
                </div>
            </div>

            <div class="mt-3">
                <button class="btn btn-primary" wire:click="guardar">
                    Guardar configuración
                </button>
            </div>

        </div>
    </div>
</div>
