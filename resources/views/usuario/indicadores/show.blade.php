@extends('layouts.usuario')

@section('titulo', 'Detalle del indicador')

@section('content')
    <div class="py-3">

        <a href="{{ url()->previous() }}" class="btn btn-link px-0 mb-2">← Volver a indicadores</a>

        @php
            $ind = $formulario->indicador;

            // Periodo “bonito” (usa lo que ya calculas en listado)
            $hoy = now()->toDateString();
            $periodoPermitido = \App\Models\Formulario::periodoYMActualPermitido($formulario->periodo_form, $hoy);
            $periodoBase = $periodoPermitido
                ? \Carbon\Carbon::createFromFormat('Y-m', $periodoPermitido)->startOfMonth()
                : now()->startOfMonth();

            $perInd = mb_strtoupper(trim((string) ($ind->periodo_ind ?? ($formulario->periodo_form ?? 'MENSUAL'))));

            if ($perInd === 'MENSUAL') {
                $periodoLabel = $periodoBase->locale('es')->translatedFormat('F Y'); // “febrero 2026”
            } elseif ($perInd === 'TRIMESTRAL') {
                $t = (int) ceil($periodoBase->month / 3);
                $periodoLabel = "T{$t} " . $periodoBase->format('Y');
            } elseif ($perInd === 'SEMESTRAL') {
                $s = $periodoBase->month <= 6 ? 1 : 2;
                $periodoLabel = "S{$s} " . $periodoBase->format('Y');
            } else {
                $periodoLabel = $periodoBase->format('Y'); // anual
            }

            // Estado general (por ahora simple)
            $estadoGeneral = $tieneMetas ? 'En progreso' : 'En progreso';

            // Mensaje de acción
            $accionMsg = $tieneMetas
                ? 'Completa las metas pendientes para acreditar el indicador.'
                : 'Captura la información del periodo ' . $periodoLabel . '.';
        @endphp

        <div class="card shadow-sm border-0 mb-3" style="border-radius:16px;">
            <div class="card-body">
                <div class="d-flex flex-wrap justify-content-between align-items-start gap-2">
                    <div>
                        <h3 class="mb-1">{{ $ind->nombre_ind ?? 'Indicador' }}</h3>
                        <div class="text-muted small">
                            Formulario #{{ $formulario->id_form }}
                        </div>
                    </div>

                    <span class="badge bg-primary-subtle text-primary border">
                        {{ $estadoGeneral }}
                    </span>
                </div>

                <div class="d-flex flex-wrap gap-2 mt-3">
                    <span class="badge bg-light text-dark border">
                        {{ ucfirst(strtolower($perInd)) }}
                    </span>
                    <span class="badge bg-light text-dark border">
                        {{ $periodoLabel }}
                    </span>
                    <span class="badge bg-light text-dark border">
                        Unidad: {{ $ind->unidadmedida_ind ?? '-' }}
                    </span>
                </div>

                <div class="alert alert-info mt-3 mb-0">
                    <b>Acción requerida:</b> {{ $accionMsg }}
                </div>
            </div>
        </div>



        @if ($tieneMetas)
            {{-- ===========================
             INDICADOR CON METAS
            ============================ --}}
            @php
                $totalMetas = $metas->count();

                $aprobadas = 0;
                $enProceso = 0;
                $sinCaptura = 0;

                foreach ($metas as $m) {
                    $det = $m->detalleCargas->first();
                    $st = $det?->estado ? mb_strtoupper(trim((string) $det->estado)) : 'SIN CAPTURA';

                    $st = str_replace('REVISIÓN', 'REVISION', $st);

                    if ($st === 'APROBADO') {
                        $aprobadas++;
                    } elseif (in_array($st, ['BORRADOR', 'OBSERVADO', 'ENVIADO', 'EN REVISION', 'REENVIADO'], true)) {
                        $enProceso++;
                    } else {
                        $sinCaptura++;
                    }
                }
            @endphp

            @php
                $pctGeneral = $totalMetas > 0 ? (int) round(($aprobadas / $totalMetas) * 100) : 0;
            @endphp

            <div class="card shadow-sm border-0 mb-3" style="border-radius:16px;">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h5 class="mb-0">Avance general</h5>
                        <span class="badge bg-primary-subtle text-primary border">
                            {{ $aprobadas }} / {{ $totalMetas }} aprobadas · {{ $pctGeneral }}%
                        </span>
                    </div>

                    <div class="progress" style="height:12px;">
                        <div class="progress-bar" role="progressbar" style="width: {{ $pctGeneral }}%;"></div>
                    </div>

                    <div class="text-muted small mt-2">
                        El indicador se acredita cuando <b>todas</b> las metas estén aprobadas.
                    </div>
                </div>
            </div>

            <div class="card mb-3">
                <div class="card-body">
                    <div class="card shadow-sm border-0 mb-3" style="border-radius:16px;">
                        <div class="card-body">
                            <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">

                                <div>
                                    <h6 class="mb-1">Kardex del indicador</h6>
                                    <div class="text-muted small">
                                        Cumplimiento general por metas
                                    </div>
                                </div>

                                <div class="d-flex flex-wrap gap-3">
                                    <span class="badge bg-success-subtle text-success border">
                                        ✔ Aprobadas: <b>{{ $aprobadas }}</b>
                                    </span>

                                    <span class="badge bg-warning-subtle text-warning border">
                                        ⏳ En proceso: <b>{{ $enProceso }}</b>
                                    </span>

                                    <span class="badge bg-light text-dark border">
                                        ⭕ Sin captura: <b>{{ $sinCaptura }}</b>
                                    </span>

                                    <span class="badge bg-primary-subtle text-primary border">
                                        Total: <b>{{ $totalMetas }}</b>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h5 class="mb-0">
                            <i class="bi bi-list-check me-1"></i>
                            Metas del indicador
                        </h5>
                        <span class="badge bg-secondary">{{ $metas->count() }}</span>
                    </div>
                    @if ($metas->isEmpty())
                        <div class="alert alert-warning">
                            Este indicador tiene metas, pero aún no hay metas registradas.
                        </div>
                    @else
                        <div class="row g-2">
                            @foreach ($metas as $meta)
                                <div class="col-12 col-md-6 col-lg-4">
                                    <div class="card h-100 shadow-sm">
                                        <div class="card-body">
                                            <div class="d-flex justify-content-between align-items-start gap-2">
                                                <div>
                                                    <h6 class="mb-1">{{ $meta->titulo }}</h6>
                                                    <small class="text-muted">Orden: {{ $meta->orden }}</small>
                                                </div>

                                                <span class="badge bg-light text-dark">
                                                    META
                                                </span>
                                            </div>

                                            <hr class="my-2">

                                            {{-- Placeholder de estado/botón (en el siguiente paso lo conectamos a capturas reales) --}}
                                            @php
                                                $det = $meta->detalleCargas->first(); // ya viene filtrado por id_form vía controller

                                                $estado = $det?->estado
                                                    ? mb_strtoupper(trim((string) $det->estado))
                                                    : 'SIN CAPTURA';

                                                $estado = str_replace('REVISIÓN', 'REVISION', $estado);

                                                $badge = match ($estado) {
                                                    'APROBADO' => 'bg-success',
                                                    'EN REVISION' => 'bg-info',
                                                    'OBSERVADO' => 'bg-warning text-dark',
                                                    'REENVIADO' => 'bg-primary',
                                                    'ENVIADO' => 'bg-secondary',
                                                    'BORRADOR' => 'bg-dark',
                                                    default => 'bg-light text-dark border',
                                                };

                                                // fecha “última”
                                                $ultimaFecha = $det?->updated_at
                                                    ? \Carbon\Carbon::parse($det->updated_at)->format('d M Y')
                                                    : null;

                                                // botón
                                                if ($estado === 'BORRADOR') {
                                                    $btnText = 'Continuar';
                                                    $btnClass = 'btn-warning text-dark';
                                                } elseif ($estado === 'OBSERVADO') {
                                                    $btnText = 'Corregir';
                                                    $btnClass = 'btn-warning text-dark';
                                                } elseif (
                                                    in_array(
                                                        $estado,
                                                        ['ENVIADO', 'EN REVISION', 'APROBADO', 'REENVIADO'],
                                                        true,
                                                    )
                                                ) {
                                                    $btnText = 'Ver';
                                                    $btnClass = 'btn-outline-secondary';
                                                } else {
                                                    $btnText = 'Capturar';
                                                    $btnClass = 'btn-primary';
                                                }

                                                // link (ya con id_meta por ruta opcional que agregamos)
                                                $href = route('usuario.formulario.captura', [
                                                    'id_form' => $formulario->id_form,
                                                    'id_ind' => $formulario->id_ind,
                                                    'id_meta' => $meta->id,
                                                ]);

                                                // si ya existe una carga ligada (si tu detalle tiene id_carga, lo pasamos)
                                                if (!empty($det?->id_carga)) {
                                                    if ($estado === 'OBSERVADO') {
                                                        $href .= '?id_carga=' . $det->id_carga . '&modo=correccion';
                                                    } else {
                                                        $href .= '?id_carga=' . $det->id_carga;
                                                    }
                                                }

                                                $progreso = match ($estado) {
                                                    'APROBADO' => 100,
                                                    'ENVIADO', 'EN REVISION', 'REENVIADO' => 80,
                                                    'BORRADOR', 'OBSERVADO' => 50,
                                                    default => 0,
                                                };
                                            @endphp

                                            <div class="mt-2">
                                                <div class="d-flex justify-content-between small text-muted">
                                                    <span>Progreso</span>
                                                    <span><b>{{ $progreso }}%</b></span>
                                                </div>
                                                <div class="progress" style="height:8px;">
                                                    <div class="progress-bar" role="progressbar"
                                                        style="width: {{ $progreso }}%;"></div>
                                                </div>
                                            </div>

                                            <div class="d-flex justify-content-between align-items-center">
                                                <div>
                                                    <span class="badge {{ $badge }}">
                                                        @if ($estado === 'APROBADO')
                                                            <i class="bi bi-check-circle me-1"></i>
                                                        @elseif($estado === 'ENVIADO' || $estado === 'EN REVISION' || $estado === 'REENVIADO')
                                                            <i class="bi bi-hourglass-split me-1"></i>
                                                        @elseif($estado === 'OBSERVADO')
                                                            <i class="bi bi-exclamation-triangle me-1"></i>
                                                        @elseif($estado === 'BORRADOR')
                                                            <i class="bi bi-pencil-square me-1"></i>
                                                        @else
                                                            <i class="bi bi-circle me-1"></i>
                                                        @endif
                                                        {{ $estado }}
                                                    </span>
                                                    @if ($ultimaFecha)
                                                        <div class="text-muted small mt-1">Última:
                                                            <b>{{ $ultimaFecha }}</b>
                                                        </div>
                                                    @else
                                                        <div class="text-muted small mt-1">Sin registros aún</div>
                                                    @endif
                                                </div>

                                                <a class="btn {{ $btnClass }} btn-sm" href="{{ $href }}">
                                                    {{ $btnText }}
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>

            <div class="card shadow-sm border-0 mt-3" style="border-radius:16px;">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h5 class="mb-0">
                            <i class="bi bi-bar-chart-line me-1"></i>
                            Gráficas del indicador
                        </h5>
                        <span class="badge bg-light text-dark border">Vista previa</span>
                    </div>

                    <div class="row g-3">
                        <div class="col-12 col-lg-6">
                            <div class="border rounded-3 p-3 h-100" style="background:#f8fafc;">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <div class="fw-semibold">Tendencia / histórico</div>
                                    <span class="badge bg-secondary">Gráfica 1</span>
                                </div>
                                <div class="text-muted small">
                                    Aquí mostraremos el comportamiento del indicador en el tiempo.
                                </div>

                                <div class="mt-3 border rounded-3 p-2" style="height:200px; background:white;">
                                    <canvas id="chartAvanceMetas" style="width:100%; height:180px;"></canvas>
                                </div>
                            </div>
                        </div>

                        <div class="col-12 col-lg-6">
                            <div class="border rounded-3 p-3 h-100" style="background:#f8fafc;">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <div class="fw-semibold">
                                        {{ $tieneMetas ? 'Cumplimiento por periodo (kardex)' : 'Comparativo por periodo' }}
                                    </div>
                                    <span class="badge bg-secondary">Gráfica 2</span>
                                </div>
                                <div class="text-muted small">
                                    {{ $tieneMetas
                                        ? 'Aquí se verá el cumplimiento por periodo/segmento.'
                                        : 'Aquí se comparan periodos (ej. S1 vs S2 / meses / trimestres).' }}
                                </div>

                                <div class="mt-3 border rounded-3 p-2" style="height:200px; background:white;">
                                    <canvas id="chartEstadosMetas" style="width:100%; height:180px;"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @else
            {{-- ===========================
                INDICADOR SIN METAS
            ============================ --}}
            @php
                // ✅ Última carga del formulario (sin metas)
                $ultimaCarga = \App\Models\Carga::where('id_form', $formulario->id_form)
                    ->orderByDesc('id_carga')
                    ->first();

                $estadoPeriodo = $ultimaCarga?->status_env
                    ? mb_strtoupper(trim((string) $ultimaCarga->status_env))
                    : 'SIN CAPTURA';

                $estadoPeriodo = str_replace('REVISIÓN', 'REVISION', $estadoPeriodo);

                $badgePeriodo = match ($estadoPeriodo) {
                    'APROBADO' => 'bg-success',
                    'EN REVISION' => 'bg-info',
                    'OBSERVADO' => 'bg-warning text-dark',
                    'REENVIADO' => 'bg-primary',
                    'ENVIADO' => 'bg-secondary',
                    'BORRADOR' => 'bg-dark',
                    default => 'bg-light text-dark border',
                };

                // ✅ Link a captura (sin metas)
                $hrefCaptura = route('usuario.formulario.captura', [
                    'id_form' => $formulario->id_form,
                    'id_ind' => $formulario->id_ind,
                ]);

                $btnText = 'Capturar';
                $btnClass = 'btn-primary';

                if ($estadoPeriodo === 'BORRADOR' && $ultimaCarga) {
                    $hrefCaptura .= '?id_carga=' . $ultimaCarga->id_carga;
                    $btnText = 'Continuar';
                    $btnClass = 'btn-warning text-dark';
                } elseif ($estadoPeriodo === 'OBSERVADO' && $ultimaCarga) {
                    $hrefCaptura .= '?id_carga=' . $ultimaCarga->id_carga . '&modo=correccion';
                    $btnText = 'Corregir';
                    $btnClass = 'btn-warning text-dark';
                } elseif (
                    in_array($estadoPeriodo, ['ENVIADO', 'EN REVISION', 'APROBADO', 'REENVIADO'], true) &&
                    $ultimaCarga
                ) {
                    $hrefCaptura .= '?id_carga=' . $ultimaCarga->id_carga;
                    $btnText = 'Ver';
                    $btnClass = 'btn-outline-secondary';
                }

                // ✅ % general del periodo actual (para barra)
                $pctPeriodo = match ($estadoPeriodo) {
                    'APROBADO' => 100,
                    'ENVIADO', 'EN REVISION', 'REENVIADO' => 80,
                    'BORRADOR', 'OBSERVADO' => 50,
                    default => 0,
                };
            @endphp

            {{-- ✅ Avance del periodo actual --}}
            <div class="card shadow-sm border-0 mb-3" style="border-radius:16px;">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h5 class="mb-0">Avance del periodo actual</h5>
                        <span class="badge bg-primary-subtle text-primary border">
                            {{ $estadoPeriodo }} · {{ $pctPeriodo }}%
                        </span>
                    </div>

                    <div class="progress" style="height:12px;">
                        <div class="progress-bar" role="progressbar" style="width: {{ $pctPeriodo }}%;"></div>
                    </div>

                    <div class="text-muted small mt-2">
                        Periodo en curso: <b>{{ $periodoLabel }}</b>.
                    </div>
                </div>
            </div>

            {{-- ✅ Tabla de periodo actual --}}
            <div class="card shadow-sm border-0 mb-3" style="border-radius:16px;">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h5 class="mb-0">
                            <i class="bi bi-calendar-event me-1"></i>
                            Periodos a capturar
                        </h5>
                        <span class="badge bg-light text-dark border">1 periodo</span>
                    </div>

                    <div class="table-responsive">
                        <table class="table align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th style="width: 30%;">Periodo</th>
                                    <th style="width: 30%;">Estado</th>
                                    <th class="text-end" style="width: 40%;">Acción</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td class="fw-semibold">{{ $periodoLabel }}</td>
                                    <td>
                                        <span class="badge {{ $badgePeriodo }}">{{ $estadoPeriodo }}</span>
                                    </td>
                                    <td class="text-end">
                                        <a class="btn btn-sm {{ $btnClass }}" href="{{ $hrefCaptura }}">
                                            {{ $btnText }}
                                        </a>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="small text-muted mt-2">
                        Captura correspondiente al periodo <b>{{ $periodoLabel }}</b>.
                    </div>
                </div>
            </div>

            {{-- ✅ Gráficas reales (SIN metas) --}}
            <div class="card shadow-sm border-0 mt-3" style="border-radius:16px;">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h5 class="mb-0">
                            <i class="bi bi-bar-chart-line me-1"></i>
                            Gráficas del indicador
                        </h5>
                        <span class="badge bg-light text-dark border">Datos reales</span>
                    </div>

                    <div class="row g-3">
                        {{-- Gráfica 1: Histórico (últimas cargas) --}}
                        <div class="col-12 col-lg-6">
                            <div class="border rounded-3 p-3 h-100" style="background:#f8fafc;">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <div class="fw-semibold">Histórico (últimas cargas)</div>
                                    <span class="badge bg-secondary">Gráfica 1</span>
                                </div>
                                <div class="text-muted small">
                                    Avance estimado (0/50/80/100) según el estado de cada carga.
                                </div>

                                <div class="mt-3 border rounded-3 p-2" style="height:220px; background:white;">
                                    <canvas id="chartHistSinMetas" style="width:100%; height:200px;"></canvas>
                                </div>
                            </div>
                        </div>

                        {{-- Gráfica 2: (siguiente paso) Dona por estado --}}
                        <div class="col-12 col-lg-6">
                            <div class="border rounded-3 p-3 h-100" style="background:#f8fafc;">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <div class="fw-semibold">Distribución por estado</div>
                                    <span class="badge bg-secondary">Gráfica 2</span>
                                </div>
                                <div class="text-muted small">
                                    Conteo de cargas por estado (SIN CAPTURA / BORRADOR / ENVIADO / REVISION / APROBADO).
                                </div>

                                <div class="mt-3 border rounded-3 p-2" style="height:220px; background:white;">
                                    <canvas id="chartEstadosSinMetas" style="width:100%; height:200px;"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif

    </div>

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script>
            const STATE_COLORS = {
                SIN_CAPTURA: '#e5e7eb',
                BORRADOR: '#111827',
                ENVIADO_REVISION: '#3b82f6',
                OBSERVADO: '#f59e0b',
                APROBADO: '#22c55e'
            };
        </script>
        @if ($tieneMetas)
            <script>
                const labelsMetas = @json($chartMetasLabels ?? []);
                const valuesMetas = @json($chartMetasValues ?? []);

                const ctx = document.getElementById('chartAvanceMetas');
                if (ctx) {
                    new Chart(ctx, {
                        type: 'bar',
                        data: {
                            labels: labelsMetas,
                            datasets: [{
                                label: 'Avance (%)',
                                data: valuesMetas,
                                backgroundColor: STATE_COLORS.ENVIADO_REVISION
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            scales: {
                                y: {
                                    beginAtZero: true,
                                    max: 100
                                }
                            }
                        }
                    });
                }

                const labelsEstados = @json($chartEstadosLabels ?? []);
                const valuesEstados = @json($chartEstadosValues ?? []);

                const ctx2 = document.getElementById('chartEstadosMetas');
                if (ctx2) {
                    new Chart(ctx2, {
                        type: 'doughnut',
                        data: {
                            labels: labelsEstados,
                            datasets: [{
                                label: 'Metas',
                                data: valuesEstados,
                                backgroundColor: [
                                    STATE_COLORS.SIN_CAPTURA,
                                    STATE_COLORS.BORRADOR,
                                    STATE_COLORS.ENVIADO_REVISION,
                                    STATE_COLORS.OBSERVADO,
                                    STATE_COLORS.APROBADO
                                ]
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: {
                                    position: 'bottom'
                                }
                            }
                        }
                    });
                }
            </script>
        @endif

        @if (!$tieneMetas)
            <script>
                const labelsHist = @json($chartHistLabels ?? []);
                const valuesHist = @json($chartHistValues ?? []);

                const ctxH = document.getElementById('chartHistSinMetas');
                if (ctxH) {
                    new Chart(ctxH, {
                        type: 'bar',
                        data: {
                            labels: labelsHist,
                            datasets: [{
                                label: 'Avance (%)',
                                data: valuesHist,
                                backgroundColor: STATE_COLORS.ENVIADO_REVISION
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            scales: {
                                y: {
                                    beginAtZero: true,
                                    max: 100
                                }
                            }
                        }
                    });
                }

                const labelsEstadosSM = @json($chartEstadosSinMetasLabels ?? []);
                const valuesEstadosSM = @json($chartEstadosSinMetasValues ?? []);

                const ctxSM = document.getElementById('chartEstadosSinMetas');
                if (ctxSM) {
                    new Chart(ctxSM, {
                        type: 'doughnut',
                        data: {
                            labels: labelsEstadosSM,
                            datasets: [{
                                data: valuesEstadosSM,
                                backgroundColor: [
                                    STATE_COLORS.SIN_CAPTURA,
                                    STATE_COLORS.BORRADOR,
                                    STATE_COLORS.ENVIADO_REVISION,
                                    STATE_COLORS.OBSERVADO,
                                    STATE_COLORS.APROBADO
                                ]
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: {
                                    position: 'bottom'
                                }
                            }
                        }
                    });
                }
            </script>
        @endif
    @endpush
@endsection
