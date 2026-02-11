<div class="container-fluid">

    <div class="d-flex align-items-center justify-content-between mb-3">
        <div>
            <h1 class="h4 mb-0">Revisión de carga</h1>
            <div class="text-muted small">
                Folio: <strong>{{ $carga->folioUnico_carga ?? 'N/D' }}</strong>
                · Status: <strong>{{ $carga->status_env ?? '—' }}</strong>
            </div>
        </div>

        <div class="d-flex gap-2">
            <a href="{{ $returnUrl ?? url('/cargas') }}" class="btn btn-secondary btn-sm">
                Volver
            </a>

            <button wire:click="aprobar" class="btn btn-success btn-sm">
                Aprobar
            </button>

            <button wire:click="abrirObservacion" class="btn btn-danger btn-sm">
                Observar
            </button>
        </div>
    </div>

    <div class="card shadow-sm mb-3">
        <div class="card-body">
            <p class="mb-1"><strong>Periodo:</strong> {{ $carga->periodo ?? '—' }}</p>
            <p class="mb-1"><strong>Ejercicio:</strong> {{ $carga->ejercicio ?? '—' }}</p>
            <p class="mb-1"><strong>Fuente:</strong> {{ $carga->fuente ?? '—' }}</p>
            <p class="mb-0"><strong>Descripción:</strong> {{ $carga->descripcion_env ?? '—' }}</p>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-header d-flex align-items-center justify-content-between">
            <div>
                <strong>Datos enviados</strong>

                @php
                $metodo = $rows[0]['fuente'] ?? null;
                @endphp

                <div class="text-muted small">
                    Mostrando {{ count($rows) }} de {{ count($rows) }} filas
                    @if($metodo)
                    · Método: <strong>{{ $metodo }}</strong>
                    @endif
                </div>
            </div>
        </div>

        <div class="card-body p-0">
            @if(empty($rows))
            <div class="p-3 text-muted">No hay captura/importación registrada para esta carga.</div>
            @else
            <div class="table-responsive">
                <table class="table table-sm table-bordered mb-0 align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            @foreach($headers as $h)
                            <th>{{ $h }}</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($rows as $r)
                        <tr>
                            <td>{{ $r['num'] ?? '—' }}</td>

                            @foreach($headers as $h)
                            @php $val = $r['campos'][$h] ?? null; @endphp
                            <td>{{ ($val === null || $val === '') ? '—' : $val }}</td>
                            @endforeach
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @endif
        </div>
    </div>

    {{-- MODAL OBSERVACIÓN --}}
    @if($showObsModal)
    <div class="modal fade show d-block" tabindex="-1" style="background: rgba(0,0,0,.45);">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Agregar observación</h5>
                    <button type="button" class="btn-close" wire:click="$set('showObsModal', false)"></button>
                </div>

                <div class="modal-body">
                    <label class="form-label">Observación para el usuario</label>
                    <textarea class="form-control" rows="5"
                        wire:model.defer="observacion"
                        placeholder="Ej. Falta agregar Nombre, revisar formato, etc."></textarea>
                </div>

                <div class="modal-footer">
                    <button class="btn btn-light" wire:click="$set('showObsModal', false)">Cancelar</button>
                    <button class="btn btn-primary" wire:click="guardarObservacion">Enviar observación</button>
                </div>
            </div>
        </div>
    </div>
    @endif

</div>