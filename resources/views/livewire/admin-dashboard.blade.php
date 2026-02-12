<div class="admin-dash-wrap">

    {{-- DATOS PARA GRÁFICAS --}}
    <div id="chart-data" data-formularios='@json($formulariosPorMes)' data-roles='@json($usuariosPorRol)'
        data-cargas='@json($cargasPorEstado)'>
    </div>

    {{-- HEADER --}}
    <div class="admin-page-header">
        <div>
            <h1 class="h3 admin-page-title text-gray-800">Dashboard</h1>
            <p class="admin-page-subtitle">Resumen general del sistema y actividad reciente.</p>
        </div>
        <div class="d-none d-md-flex align-items-center">
            <span class="badge-soft">
                <i class="fas fa-clock mr-1"></i> {{ now()->format('d/m/Y H:i') }}
            </span>
        </div>
    </div>

    {{-- KPIs --}}
    @php
        $countDependencias = DB::table('dependencias')->count();
        $countIndicadores = DB::table('indicadores')->count();
    @endphp

    <div class="row">
        <div class="col-xl-2 col-md-4 col-sm-6 mb-3">
            <div class="card kpi-card pro-kpi kpi-primary">
                <div class="card-body">
                    <div class="kpi-top">
                        <div>
                            <div class="kpi-label">Usuarios</div>
                            <p class="kpi-value">{{ $totalUsuarios }}</p>
                        </div>
                        <div class="kpi-icon"><i class="fas fa-users"></i></div>
                    </div>
                    <div class="kpi-foot text-muted small">
                        <i class="fas fa-info-circle mr-1"></i> Total registrados
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-2 col-md-4 col-sm-6 mb-3">
            <div class="card kpi-card pro-kpi kpi-success">
                <div class="card-body">
                    <div class="kpi-top">
                        <div>
                            <div class="kpi-label">Formularios</div>
                            <p class="kpi-value">{{ $totalFormularios }}</p>
                        </div>
                        <div class="kpi-icon"><i class="fas fa-file-alt"></i></div>
                    </div>
                    <div class="kpi-foot text-muted small">
                        <i class="fas fa-layer-group mr-1"></i> Creados en el sistema
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-2 col-md-4 col-sm-6 mb-3">
            <div class="card kpi-card pro-kpi kpi-warning">
                <div class="card-body">
                    <div class="kpi-top">
                        <div>
                            <div class="kpi-label">En revisión</div>
                            <p class="kpi-value">{{ $cargasPendientes }}</p>
                        </div>
                        <div class="kpi-icon"><i class="fas fa-search"></i></div>
                    </div>
                    <div class="kpi-foot text-muted small">
                        <i class="fas fa-clock mr-1"></i> Pendientes por validar
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-2 col-md-4 col-sm-6 mb-3">
            <div class="card kpi-card pro-kpi kpi-info">
                <div class="card-body">
                    <div class="kpi-top">
                        <div>
                            <div class="kpi-label">Nuevos hoy</div>
                            <p class="kpi-value">{{ $nuevosRegistros }}</p>
                        </div>
                        <div class="kpi-icon"><i class="fas fa-bolt"></i></div>
                    </div>
                    <div class="kpi-foot text-muted small">
                        <i class="fas fa-calendar-day mr-1"></i> Actividad del día
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-2 col-md-4 col-sm-6 mb-3">
            <div class="card kpi-card pro-kpi kpi-primary">
                <div class="card-body">
                    <div class="kpi-top">
                        <div>
                            <div class="kpi-label">Dependencias</div>
                            <p class="kpi-value">{{ $countDependencias }}</p>
                        </div>
                        <div class="kpi-icon"><i class="fas fa-building"></i></div>
                    </div>
                    <div class="kpi-foot text-muted small">
                        <i class="fas fa-layer-group mr-1"></i> Registradas
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-2 col-md-4 col-sm-6 mb-3">
            <div class="card kpi-card pro-kpi kpi-success">
                <div class="card-body">
                    <div class="kpi-top">
                        <div>
                            <div class="kpi-label">Indicadores</div>
                            <p class="kpi-value">{{ $countIndicadores }}</p>
                        </div>
                        <div class="kpi-icon"><i class="fas fa-chart-line"></i></div>
                    </div>
                    <div class="kpi-foot text-muted small">
                        <i class="fas fa-tags mr-1"></i> En catálogo
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- CHARTS (solo HTML aquí, el JS va abajo) --}}
    <div class="row">
        {{-- Serie histórica --}}
        <div class="col-lg-6 mb-3">
            <div class="card pro-card">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <span>Formularios por mes (Serie histórica)</span>
                    <span class="text-muted small"><i class="fas fa-chart-line mr-1"></i> Tendencia</span>
                </div>
                <div class="card-body">
                    @php
                        $vals = array_values($formulariosPorMes ?? []);
                        $totalHist = array_sum($vals);
                        $maxHist = count($vals) ? max($vals) : 0;
                        $avgHist = count($vals) ? round($totalHist / count($vals), 1) : 0;
                    @endphp

                    <div class="mini-stats">
                        <div><span class="mini-k">Total</span><span class="mini-v">{{ $totalHist }}</span></div>
                        <div><span class="mini-k">Máximo</span><span class="mini-v">{{ $maxHist }}</span></div>
                        <div><span class="mini-k">Promedio</span><span class="mini-v">{{ $avgHist }}</span></div>
                    </div>
                    <div class="chart-box">
                        <canvas id="formMes"></canvas>
                    </div>
                </div>
            </div>
        </div>

        {{-- Roles --}}
        <div class="col-lg-6 mb-3">
            <div class="row">

                <div class="col-md-6 mb-3 mb-md-0">
                    <div class="card pro-card">
                        <div class="card-header d-flex justify-content-between">
                            <span>Admins</span>
                            <span class="text-muted small" id="pctAdmin">{{ $totalUsuarios }} total</span>
                        </div>
                        <div class="card-body">
                            <div class="chart-box" style="height:190px;">
                                <canvas id="roleAdminGauge"></canvas>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="card pro-card">
                        <div class="card-header d-flex justify-content-between">
                            <span>Usuarios</span>
                            <span class="text-muted small" id="pctAdmin">{{ $totalUsuarios }} total</span>
                        </div>
                        <div class="card-body">
                            <div class="chart-box" style="height:190px;">
                                <canvas id="roleUsuarioGauge"></canvas>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    {{-- ESTADO DE CARGAS (Semáforo) --}}
    @php
        $mapSemaforo = [
            'APROBADO' => ['class' => 'sema-verde', 'desc' => 'Aprobado / Validado'],
            'EN REVISION' => ['class' => 'sema-amarillo', 'desc' => 'En revisión'],
            'REENVIADO' => ['class' => 'sema-amarillo', 'desc' => 'Reenviado (verificar)'],
            'ENVIADO' => ['class' => 'sema-amarillo', 'desc' => 'Recibido (pendiente)'],
            'RECHAZADO' => ['class' => 'sema-rojo', 'desc' => 'Rechazado (corregir)'],
            'BORRADOR' => ['class' => 'sema-gris', 'desc' => 'Borrador (sin enviar)'],
        ];
    @endphp

    <div class="card pro-card mb-3">
        <div class="card-header d-flex align-items-center justify-content-between">
            <span>Estado de cargas (Semáforo)</span>

            <div class="d-flex align-items-center">
                @if (($totalBorradores ?? 0) > 0)
                    <button type="button" class="btn btn-sm btn-outline-primary mr-2"
                        wire:click="recordarBorradoresGlobal">
                        <i class="fas fa-bell mr-1"></i> Recordar borradores ({{ (int) $totalBorradores }})
                    </button>
                @endif

                <span class="text-muted small">
                    <i class="fas fa-traffic-light mr-1"></i> Resumen
                </span>
            </div>
        </div>

        <div class="card-body">
            @if (session()->has('message'))
                <div class="alert alert-info mb-3">
                    {{ session('message') }}
                </div>
            @endif

            <div class="table-responsive">
                <table class="table table-hover table-pro mb-0">
                    <thead>
                        <tr>
                            <th>Estado</th>
                            <th style="width:140px;">Cantidad</th>
                            <th style="width:320px;">Interpretación</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($cargasPorEstado as $estado => $cantidad)
                            @php
                                $estadoKey = strtoupper(trim($estado));
                                $cfg = $mapSemaforo[$estadoKey] ?? [
                                    'class' => 'sema-gris',
                                    'desc' => 'Sin clasificación',
                                ];
                            @endphp
                            <tr>
                                <td class="font-weight-bold text-gray-800">{{ $estado }}</td>
                                <td><span class="badge-soft">{{ (int) $cantidad }}</span></td>
                                <td>
                                    <span class="badge-semaforo {{ $cfg['class'] }}">
                                        {{ $estado }}
                                    </span>
                                    <div class="text-muted small mt-1">
                                        {{ $cfg['desc'] }}
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="legend-box">
                <div class="legend-item"><span class="legend-dot dot-verde"></span> Verde: Aprobado / Validado</div>
                <div class="legend-item"><span class="legend-dot dot-amarillo"></span> Amarillo: Enviado / En revisión
                    / Reenviado</div>
                <div class="legend-item"><span class="legend-dot dot-rojo"></span> Rojo: Rechazado / Requiere
                    corrección</div>
                <div class="legend-item"><span class="legend-dot dot-gris"></span> Gris: Borrador / Sin enviar</div>
            </div>
        </div>
    </div>

    {{-- DEPENDENCIAS + # INDICADORES --}}
    @php
        $depIndicadores = DB::table('dependencias as d')
            ->leftJoin('formularios as f', 'f.id_depen', '=', 'd.id_depen')
            ->select(
                'd.id_depen',
                'd.nombre_depen',
                DB::raw('COUNT(DISTINCT f.id_ind) as indicadores_asignados'),
                DB::raw('COUNT(DISTINCT f.id_form) as formularios_asociados'),
            )
            ->groupBy('d.id_depen', 'd.nombre_depen')
            ->get();

        $depIndicadores = $depIndicadores->sortBy(function ($row) use ($avancePorDep) {
            $pct = (int) ($avancePorDep[$row->id_depen]['pct'] ?? 0);
            $meta = (int) ($avancePorDep[$row->id_depen]['meta'] ?? 0);

            // Orden: pct asc (atrasados primero), meta desc (más importante primero), nombre asc
            return sprintf('%03d-%05d-%s', $pct, 99999 - $meta, mb_strtolower($row->nombre_depen));
        });
    @endphp

    <div class="card pro-card mb-3">
        <div class="card-header d-flex align-items-center justify-content-between">
            <span>Dependencias</span>
            <span class="text-muted small">Indicadores asignados</span>
        </div>
        <div class="card-body">
            <div class="row">
                @foreach ($depIndicadores as $row)
                    <div class="col-lg-4 col-md-6 mb-3">
                        <div class="dep-card">
                            <div class="dep-card-top">
                                <div class="dep-name text-truncate" title="{{ $row->nombre_depen }}">
                                    {{ $row->nombre_depen }}
                                </div>
                                <span class="badge-soft">Meta:
                                    {{ (int) ($avancePorDep[$row->id_depen]['meta'] ?? 0) }}</span>
                            </div>

                            <div class="dep-metrics">
                                <div class="dep-metric">
                                    <div class="dep-metric-label">Formularios</div>
                                    <div class="dep-metric-value">{{ (int) $row->formularios_asociados }}</div>
                                </div>
                                <div class="dep-metric">
                                    <div class="dep-metric-label">Indicadores</div>
                                    <div class="dep-metric-value">{{ (int) $row->indicadores_asignados }}</div>
                                </div>
                            </div>

                            @php
                                $borr = (int) ($borradoresPorDep[$row->id_depen] ?? 0);
                            @endphp

                            <div class="d-flex align-items-center justify-content-between mt-2">
                                <div class="text-muted small">
                                    <i class="fas fa-pencil-alt mr-1"></i> Borradores:
                                    <strong>{{ $borr }}</strong>
                                </div>

                                @if ($borr > 0)
                                    <button type="button" class="btn btn-sm btn-outline-primary"
                                        wire:click="recordarBorradoresDependencia({{ (int) $row->id_depen }})">
                                        <i class="fas fa-bell mr-1"></i> Recordar
                                    </button>
                                @endif
                            </div>

                            <div class="dep-mini">
                                <div class="dep-mini-label">Avance de meta</div>

                                @php
                                    $adv = $avancePorDep[$row->id_depen] ?? [
                                        'meta' => 0,
                                        'hechos' => 0,
                                        'pct' => 0,
                                        'color' => 'secondary',
                                    ];
                                    $pct = (int) ($adv['pct'] ?? 0);
                                    $meta = (int) ($adv['meta'] ?? 0);
                                    $hechos = (int) ($adv['hechos'] ?? 0);

                                    // clase para pintar tu fill (usa tu CSS actual o bootstrap)
                                    $fillClass =
                                        ($adv['color'] ?? 'secondary') === 'success'
                                            ? 'fill-success'
                                            : (($adv['color'] ?? 'secondary') === 'warning'
                                                ? 'fill-warning'
                                                : (($adv['color'] ?? 'secondary') === 'danger'
                                                    ? 'fill-danger'
                                                    : 'fill-secondary'));
                                @endphp

                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <div class="text-muted small">
                                        {{ $hechos }} / {{ $meta }} completados

                                        @if ($pct >= 100 && $meta > 0)
                                            <span class="badge badge-success ml-2">Meta cumplida</span>
                                        @endif
                                    </div>

                                    <div class="font-weight-bold">
                                        {{ $pct }}%
                                    </div>
                                </div>

                                <div class="dep-bar">
                                    <div class="dep-bar-fill {{ $fillClass }}"
                                        style="width: {{ $pct }}%"></div>
                                </div>

                                <div class="text-muted small mt-2">
                                    Meta: {{ $meta }} · Hechos: {{ $hechos }}
                                </div>
                            </div>

                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- ÚLTIMOS FORMULARIOS --}}
    <div class="card pro-card mt-2">
        <div class="card-header d-flex align-items-center justify-content-between">
            <span>Últimos Formularios</span>
            <span class="text-muted small"><i class="fas fa-list mr-1"></i> Últimos 5</span>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover table-pro mb-0">
                    <thead>
                        <tr>
                            <th style="width:80px;">ID</th>
                            <th>Título</th>
                            <th style="width:140px;">Fecha</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach (DB::table('formularios')->latest()->limit(5)->get() as $f)
                            <tr>
                                <td><span class="badge-soft">#{{ $f->id_form }}</span></td>
                                <td class="font-weight-bold text-gray-800">{{ $f->titulo_form }}</td>
                                <td class="text-muted">{{ \Carbon\Carbon::parse($f->created_at)->format('d/m/Y') }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>
        document.addEventListener("DOMContentLoaded", function() {

            const dataContainer = document.getElementById("chart-data");
            const formularios = JSON.parse(dataContainer.dataset.formularios || "{}");
            const roles = JSON.parse(dataContainer.dataset.roles || "{}");

            const commonOptions = {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                }
            };

            // =========================
            // SERIE HISTÓRICA (labels bonitos)
            // =========================
            function formatLabel(ym) {
                const parts = (ym || "").split("-");
                if (parts.length < 2) return ym;
                const y = parts[0];
                const m = parseInt(parts[1], 10);
                const meses = ["Ene", "Feb", "Mar", "Abr", "May", "Jun", "Jul", "Ago", "Sep", "Oct", "Nov", "Dic"];
                if (!m || m < 1 || m > 12) return ym;
                return meses[m - 1] + " " + y;
            }

            const labelsRaw = Object.keys(formularios).sort();
            const labelsForm = labelsRaw.map(formatLabel);
            const valuesForm = labelsRaw.map(k => formularios[k]);
            const avg = valuesForm.length ? (valuesForm.reduce((a, b) => a + b, 0) / valuesForm.length) : 0;
            const avgLine = valuesForm.map(() => avg);


            const centerTextPlugin = {
                id: 'centerTextPlugin',
                afterDraw(chart, args, options) {
                    const {
                        ctx
                    } = chart;
                    const meta = chart.getDatasetMeta(0);
                    if (!meta || !meta.data || !meta.data[0]) return;

                    const x = chart.chartArea.left + (chart.chartArea.right - chart.chartArea.left) / 2;
                    const y = chart.chartArea.top + (chart.chartArea.bottom - chart.chartArea.top) / 2 + 18;

                    ctx.save();
                    ctx.textAlign = 'center';
                    ctx.textBaseline = 'middle';

                    ctx.font = '700 18px Nunito, sans-serif';
                    ctx.fillStyle = '#111';
                    ctx.fillText(options.mainText || '', x, y - 6);

                    ctx.font = '600 12px Nunito, sans-serif';
                    ctx.fillStyle = '#6c757d';
                    ctx.fillText(options.subText || '', x, y + 14);

                    ctx.restore();
                }
            };
            Chart.register(centerTextPlugin);

            new Chart(document.getElementById('formMes'), {
                type: 'line',
                data: {
                    labels: labelsForm,
                    datasets: [{
                            label: 'Formularios',
                            data: valuesForm,
                            tension: 0.35,
                            fill: true,
                            pointRadius: 3,
                            pointHoverRadius: 5,
                            borderWidth: 2
                        },
                        {
                            label: 'Promedio',
                            data: avgLine,
                            tension: 0,
                            fill: false,
                            pointRadius: 0,
                            borderDash: [6, 6],
                            borderWidth: 2
                        }
                    ]
                },
                options: {
                    ...commonOptions,
                    plugins: {
                        legend: {
                            position: 'bottom'
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                precision: 0
                            }
                        }
                    }
                }
            });

            // =========================
            // GAUGES (Admins / Usuarios con %)
            // =========================
            const totalRoles = Object.values(roles).reduce((a, b) => a + b, 0) || 1;

            const adminsCount =
                roles["admin"] ?? roles["ADMIN"] ?? roles["Administrador"] ?? roles["ADMINISTRADOR"] ?? 0;

            const usersCount =
                roles["usuario"] ?? roles["USUARIO"] ?? roles["Usuario"] ?? 0;

            const pctAdmin = Math.round((adminsCount / totalRoles) * 100);
            const pctUser = Math.round((usersCount / totalRoles) * 100);

            const elPctAdmin = document.getElementById("pctAdmin");
            const elPctUsuario = document.getElementById("pctUsuario");
            if (elPctAdmin) elPctAdmin.innerText = pctAdmin + "%";
            if (elPctUsuario) elPctUsuario.innerText = pctUser + "%";

            function gauge(canvas, pct, mainText, subText) {
                if (!canvas) return;
                return new Chart(canvas, {
                    type: 'doughnut',
                    data: {
                        datasets: [{
                            data: [pct, 100 - pct],
                            borderWidth: 0,
                            cutout: '78%'
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        rotation: -90,
                        circumference: 180,
                        plugins: {
                            legend: {
                                display: false
                            },
                            tooltip: {
                                enabled: false
                            },
                            centerTextPlugin: {
                                mainText,
                                subText
                            }
                        }
                    }
                });
            }

            gauge(document.getElementById('roleAdminGauge'), pctAdmin, adminsCount + " admins", pctAdmin + "%");
            gauge(document.getElementById('roleUsuarioGauge'), pctUser, usersCount + " usuarios", pctUser + "%");

        });
    </script>
@endpush
