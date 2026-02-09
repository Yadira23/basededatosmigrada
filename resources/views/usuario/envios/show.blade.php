@extends('layouts.usuario')

@section('titulo', 'Ver envío')

@section('content')
<div class="py-3">

    {{-- Encabezado --}}
    <div class="d-flex justify-content-between align-items-start mb-3">
        <div>
            <h4 class="mb-0">Detalle del envío</h4>
            <small class="text-muted">
                Consulta la última información enviada de este indicador.
            </small>
        </div>

        <div class="d-flex gap-2">
            <a href="{{ route('usuario.envio.historial', $form->id_form) }}" class="btn btn-sm btn-outline-primary">
                <i class="fas fa-history mr-1"></i> Ver historial
            </a>

            <a href="{{ route('usuario.indicadores') }}" class="btn btn-sm btn-outline-secondary">
                <i class="fas fa-arrow-left mr-1"></i> Volver
            </a>
        </div>
    </div>

    {{-- Card info formulario --}}
    <div class="card shadow-sm border-0 mb-3">
        <div class="card-body">

            <div class="mb-2">
                <div class="text-muted small">Formulario</div>
                <div class="font-weight-bold">
                    {{ $form->titulo_form ?? ('Formulario #' . $form->id_form) }}
                </div>
            </div>

            <div class="mb-2">
                <div class="text-muted small">Indicador</div>
                <div class="font-weight-bold">
                    {{ $form->indicador->nombre_ind ?? 'Sin indicador' }}
                </div>
            </div>

            <div class="d-flex flex-wrap gap-4 mt-3">
                <div>
                    <div class="text-muted small">Periodo</div>
                    <div class="font-weight-bold">{{ $form->periodo_form ?? '—' }}</div>
                </div>

                <div>
                    <div class="text-muted small">Unidad</div>
                    <div class="font-weight-bold">{{ $form->indicador->unidadmedida_ind ?? '—' }}</div>
                </div>

                <div>
                    <div class="text-muted small">Fuente</div>
                    <div class="font-weight-bold">{{ $form->indicador->fuente_ind ?? ($form->fuente ?? '—') }}</div>
                </div>
            </div>

        </div>
    </div>

    {{-- Card último envío --}}
    <div class="card shadow-sm border-0">
        <div class="card-body">

            @if(!$ultima)
            <div class="alert alert-warning mb-0">
                Aún no hay envíos registrados para este indicador.
            </div>
            @else
            @php
            $estado = $ultima->status_env ?? '—';
            $badge = match ($estado) {
            'APROBADO' => 'success',
            'EN REVISION' => 'info',
            'OBSERVADO' => 'warning',
            'REENVIADO' => 'primary',
            'ENVIADO' => 'secondary',
            default => 'dark',
            };
            @endphp

            <div class="d-flex justify-content-between align-items-start flex-wrap gap-2">
                <div>
                    <div class="text-muted small">Estado</div>
                    <span class="badge badge-{{ $badge }} px-3 py-2">
                        {{ $estado }}
                    </span>
                </div>

                <div class="text-right">
                    <div class="text-muted small">Método</div>
                    <div class="font-weight-bold">{{ $ultima->metodo_captura ?? '—' }}</div>

                    <div class="text-muted small mt-2">Última actualización</div>
                    <div class="font-weight-bold">
                        {{ \Carbon\Carbon::parse($ultima->updated_at ?? $ultima->created_at)->format('d/m/Y H:i') }}
                    </div>
                </div>
            </div>

            @php
            $hayDetalles = isset($detalles) && $detalles->count() > 0;
            $hayManual = !empty($manualPreview['total']) && (int)$manualPreview['total'] > 0;
            $hayArchivo = !empty($archivoEvidencia); // ✅ ARCHIVO cuenta como captura

            $hayCaptura = $hayDetalles || $hayManual || $hayArchivo;

            $estadoNorm = mb_strtoupper(trim((string)($ultima->status_env ?? '')));
            $estadoNorm = str_replace('REVISIÓN','REVISION',$estadoNorm);
            @endphp

            {{-- =========================
   ACCIÓN PRINCIPAL (UX) 
========================= --}}
            @php
            $hayDetalles = (isset($detalles) && $detalles->count() > 0);
            $hayManual = (!empty($manualPreview['total']) && (int)$manualPreview['total'] > 0);
            $hayEvidenciaArchivo = !empty($archivoEvidencia); // ✅ si ya hay evidencia, ya hubo captura

            $hayCaptura = $hayDetalles || $hayManual || $hayEvidenciaArchivo;

            $estadoNorm = mb_strtoupper(trim((string)($ultima->status_env ?? '')));
            $estadoNorm = str_replace('REVISIÓN','REVISION',$estadoNorm);
            @endphp

            <div class="mt-3">

                {{-- ✅ 1) OBSERVADO: siempre muestra corregir --}}
                @if($estadoNorm === 'OBSERVADO')
                <div class="alert alert-warning">
                    <div class="font-weight-bold mb-1">
                        <i class="fas fa-exclamation-triangle mr-1"></i>
                        Tu envío fue observado
                    </div>
                    <div class="small">Revisa la observación y corrige la información para reenviar.</div>

                    @if(!empty($ultima->observacion_env))
                    <div class="mt-2 small">
                        <strong>Observación:</strong> {{ $ultima->observacion_env }}
                    </div>
                    @endif

                    <div class="mt-3">
                        <a class="btn btn-sm btn-warning"
                            href="{{ route('usuario.formulario.captura', [
                        'id_form' => $form->id_form,
                        'id_ind'  => $form->id_ind
                   ]) }}?id_carga={{ $ultima->id_carga }}&modo=correccion">
                            <i class="fas fa-edit mr-1"></i>
                            Corregir y reenviar
                        </a>
                    </div>
                </div>

                {{-- ✅ 2) SI NO HAY CAPTURA (aunque diga ENVIADO): mostrar “sin captura” --}}
                @elseif(!$hayCaptura && !in_array($estadoNorm, ['ENVIADO','EN REVISION','REENVIADO','APROBADO']))
                <div class="alert alert-secondary">
                    <div class="font-weight-bold mb-1">
                        <i class="fas fa-play mr-1"></i>
                        Aún no hay captura registrada
                    </div>
                    <div class="small">Inicia la captura para enviar información de este indicador.</div>

                    <div class="mt-3">
                        <a class="btn btn-sm btn-primary"
                            href="{{ route('usuario.formulario.captura', [
                        'id_form' => $form->id_form,
                        'id_ind'  => $form->id_ind
                   ]) }}">
                            <i class="fas fa-pen mr-1"></i>
                            Comenzar captura
                        </a>
                    </div>
                </div>

                {{-- ✅ 3) HAY CAPTURA: mensaje correcto según estado --}}
                @else
                @if($estadoNorm === 'ENVIADO' || $estadoNorm === 'REENVIADO')
                <div class="alert alert-info">
                    <i class="fas fa-info-circle mr-1"></i>
                    Tu información fue enviada correctamente y está pendiente de revisión.
                </div>
                @elseif($estadoNorm === 'EN REVISION')
                <div class="alert alert-info">
                    <i class="fas fa-clock mr-1"></i>
                    Tu envío está en revisión.
                </div>
                @elseif($estadoNorm === 'APROBADO')
                <div class="alert alert-success">
                    <i class="fas fa-check-circle mr-1"></i>
                    Tu envío fue aprobado.
                </div>
                @else
                <div class="alert alert-info">
                    <i class="fas fa-info-circle mr-1"></i>
                    Este envío ya fue registrado.
                </div>
                @endif
                @endif

            </div>
            {{-- Datos básicos del envío --}}
            <div class="mt-4">
                <div class="text-muted small mb-1">Folio</div>
                <div class="font-weight-bold">{{ $ultima->folioUnico_carga ?? '—' }}</div>
            </div>

            @if(!empty($ultima->descripcion_env))
            <div class="mt-3">
                <div class="text-muted small mb-1">Descripción</div>
                <div>{{ $ultima->descripcion_env }}</div>
            </div>
            @endif
            @endif

        </div>
        {{-- =========================
     DATOS ENVIADOS (ARCHIVO)
   ========================= --}}
        @if($ultima && $ultima->metodo_captura === 'ARCHIVO')

        <div class="card shadow-sm border-0 mt-3">
            <div class="card-body">

                <h5 class="mb-3">
                    <i class="fas fa-file-alt me-1"></i>
                    Datos enviados (Archivo)
                </h5>

                <div class="row">
                    <div class="col-md-6 mb-2">
                        <div class="text-muted small">Archivo enviado</div>
                        <div class="fw-semibold">
                            {{ $archivoEvidencia ?? '—' }}
                        </div>
                    </div>

                    <div class="col-md-3 mb-2">
                        <div class="text-muted small">Método</div>
                        <div class="fw-semibold">ARCHIVO</div>
                    </div>

                    <div class="col-md-3 mb-2">
                        <div class="text-muted small">Fecha de carga</div>
                        <div class="fw-semibold">
                            {{ \Carbon\Carbon::parse($ultima->fecha_carga)->format('d/m/Y') }}
                        </div>
                    </div>
                </div>

                <hr>

                <div class="d-flex flex-wrap gap-2">
                    {{-- Descargar archivo enviado --}}
                    <a href="{{ route('usuario.envio.descargar.archivo', $ultima->id_carga) }}"
                        class="btn btn-sm btn-outline-primary">
                        <i class="fas fa-download me-1"></i>
                        Descargar archivo enviado
                    </a>

                    {{-- Descargar log (si existe) --}}
                    @php
                    $dirEvid = storage_path('app/public/anexos/evidencias');
                    $hayLog = false; // por ahora
                    @endphp

                    @if($hayLog)
                    <a href="{{ route('usuario.envio.descargar.log', $ultima->id_carga) }}"
                        class="btn btn-sm btn-outline-secondary">
                        <i class="fas fa-file-alt me-1"></i>
                        Descargar log
                    </a>
                    @else
                    <button class="btn btn-sm btn-outline-secondary" disabled
                        title="No hay log disponible para este envío">
                        <i class="fas fa-file-alt me-1"></i>
                        Log no disponible
                    </button>
                    @endif
                </div>

            </div>
        </div>

        @endif
        {{-- =========================
     DATOS ENVIADOS (MANUAL)
   ========================= --}}
        @if($ultima && $ultima->metodo_captura === 'MANUAL')

        <div class="card shadow-sm border-0 mt-3">
            <div class="card-body">

                <div class="d-flex justify-content-between align-items-start mb-2">
                    <div>
                        <h5 class="mb-0">
                            <i class="fas fa-keyboard mr-1"></i> Datos enviados (Manual)
                        </h5>
                        <small class="text-muted">
                            Mostrando {{ count($manualPreview['rows'] ?? []) }} de {{ $manualPreview['total'] ?? 0 }} filas (solo lectura)
                        </small>
                    </div>
                </div>

                @if(empty($manualPreview['rows']))
                <div class="alert alert-warning mb-0">
                    No hay datos de captura manual para este envío.
                </div>
                @else
                <div class="table-responsive">
                    <table class="table table-sm table-hover mb-0">
                        <thead class="thead-light">
                            <tr>
                                <th>#</th>
                                @foreach(($manualPreview['columns'] ?? []) as $col)
                                <th>{{ $col }}</th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody>
                            @foreach(($manualPreview['rows'] ?? []) as $r)
                            <tr>
                                <td class="text-muted">
                                    {{ $r['_fila'] ?? $r['_id_detalle'] }}
                                </td>

                                @foreach(($manualPreview['columns'] ?? []) as $col)
                                <td>
                                    {{ $r['_campos'][$col] ?? '—' }}
                                </td>
                                @endforeach
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                @if(($manualPreview['total'] ?? 0) > 30)
                <div class="text-muted small mt-2">
                    Tip: si quieres, después hacemos paginación para ver todas las filas.
                </div>
                @endif
                @endif

            </div>
        </div>

        @endif

    </div>

</div>
@endsection