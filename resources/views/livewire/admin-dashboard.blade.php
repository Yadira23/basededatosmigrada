<div class="admin-dash-wrap">

    {{-- DATOS PARA GRÁFICAS --}}
    <div id="chart-data"
        data-formularios='@json($formulariosPorMes)'
        data-roles='@json($usuariosPorRol)'
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

    {{-- KPIs (PRO, sin fondo fuerte) --}}
    <div class="row">
        <div class="col-xl-3 col-md-6 mb-3">
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

        <div class="col-xl-3 col-md-6 mb-3">
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

        <div class="col-xl-3 col-md-6 mb-3">
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

        <div class="col-xl-3 col-md-6 mb-3">
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
    </div>

    {{-- CHARTS --}}
    <div class="row">
        <div class="col-lg-6 mb-3">
            <div class="card pro-card">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <span>Formularios por mes</span>
                    <span class="text-muted small"><i class="fas fa-chart-bar mr-1"></i> Últimos meses</span>
                </div>
                <div class="card-body">
                    <div class="chart-box">
                        <canvas id="formMes"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-6 mb-3">
            <div class="card pro-card">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <span>Usuarios por rol</span>
                    <span class="text-muted small"><i class="fas fa-user-tag mr-1"></i> Distribución</span>
                </div>
                <div class="card-body">
                    <div class="chart-box">
                        <canvas id="rolesChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-6 mb-3">
            <div class="card pro-card">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <span>Estado de cargas</span>
                    <span class="text-muted small"><i class="fas fa-circle-notch mr-1"></i> Seguimiento</span>
                </div>
                <div class="card-body">
                    <div class="chart-box">
                        <canvas id="cargasChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        {{-- Card simple de relleno (solo visual) --}}
        <div class="col-lg-6 mb-3">
            <div class="card pro-card">
                <div class="card-header">Actividad reciente</div>
                <div class="card-body">
                    <div class="text-muted small">
                        Aquí luego puedes mostrar últimas cargas / últimos accesos, etc.
                    </div>
                </div>
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
                        @foreach(DB::table('formularios')->latest()->limit(5)->get() as $f)
                        <tr>
                            <td><span class="badge-soft">#{{ $f->id_form }}</span></td>
                            <td class="font-weight-bold text-gray-800">{{ $f->titulo_form }}</td>
                            <td class="text-muted">{{ \Carbon\Carbon::parse($f->created_at)->format('d/m/Y') }}</td>
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
        const formularios = JSON.parse(dataContainer.dataset.formularios);
        const roles = JSON.parse(dataContainer.dataset.roles);
        const cargas = JSON.parse(dataContainer.dataset.cargas);

        const commonOptions = {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            }
        };

        new Chart(document.getElementById('formMes'), {
            type: 'bar',
            data: {
                labels: Object.keys(formularios),
                datasets: [{
                    label: 'Formularios',
                    data: Object.values(formularios),
                    borderWidth: 0
                }]
            },
            options: {
                ...commonOptions,
                plugins: {
                    ...commonOptions.plugins,
                    legend: {
                        display: false
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

        new Chart(document.getElementById('rolesChart'), {
            type: 'pie',
            data: {
                labels: Object.keys(roles),
                datasets: [{
                    data: Object.values(roles)
                }]
            },
            options: commonOptions
        });

        new Chart(document.getElementById('cargasChart'), {
            type: 'doughnut',
            data: {
                labels: Object.keys(cargas),
                datasets: [{
                    data: Object.values(cargas)
                }]
            },
            options: commonOptions
        });

    });
</script>
@endpush