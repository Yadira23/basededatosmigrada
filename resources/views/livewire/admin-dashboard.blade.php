<div class="admin-dash-wrap" wire:poll.60s>

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

    {{-- CHARTS --}}
    <div class="row align-items-stretch">

        {{-- ✅ CARGAS POR ESTADO (HORIZONTAL PRO) --}}
        <div class="col-lg-6 mb-3">
            <div class="card pro-card h-100">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <div>
                        <span class="font-weight-bold text-gray-800">Cargas por estado</span>
                        <div class="text-muted small">Conteo y porcentaje por estatus.</div>
                    </div>

                    <div class="d-flex align-items-center" style="gap:10px;">
                        <span class="badge-soft" id="topEstadoLabel">Top: —</span>
                        <span class="badge-soft" id="pctBorradoresLabel">% borrador: —</span>
                        <span class="badge-soft" id="totalCargasLabel">Total: —</span>
                    </div>
                </div>

                <div class="card-body">
                    <div class="chart-box" style="height:190px;">
                        <canvas id="chartCargasEstado"></canvas>
                    </div>

                    <div class="d-flex flex-wrap mt-2" style="gap:8px;" id="chipsEstados"></div>
                </div>
            </div>
        </div>

        {{-- ✅ ROLES (como ya lo tienes) --}}
        <div class="col-lg-6 mb-3">
            <div class="row h-100">

                <div class="col-md-6 mb-3 mb-md-0">
                    <div class="card pro-card h-100">
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
                    <div class="card pro-card h-100">
                        <div class="card-header d-flex justify-content-between">
                            <span>Usuarios</span>
                            <span class="text-muted small" id="pctUsuario">{{ $totalUsuarios }} total</span>
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

            {{-- ✅ RESUMEN (Opción A) - reemplaza tabla --}}
            <div class="row g-3 align-items-stretch">

                {{-- RESUMEN PRINCIPAL --}}
                <div class="col-12 col-lg-7">
                    <div class="border rounded-3 p-3 bg-white h-100">

                        <div class="mb-2">
                            <div class="font-weight-bold text-gray-800">Resumen de estado de cargas</div>
                            <div class="text-muted small">Interpretación automática del sistema</div>
                        </div>

                        <div class="d-flex flex-column" style="gap:10px;">

                            {{-- APROBADO --}}
                            @if (($cargasPorEstado['APROBADO'] ?? 0) > 0)
                                <div class="d-flex align-items-center justify-content-between border rounded-3 p-2">
                                    <div class="d-flex align-items-center" style="gap:10px;">
                                        <span class="badge badge-success">APROBADO</span>
                                        <span class="text-muted small">Validado correctamente</span>
                                    </div>
                                    <span class="badge-soft">{{ (int) ($cargasPorEstado['APROBADO'] ?? 0) }}</span>
                                </div>
                            @endif

                            {{-- ENVIADO --}}
                            @if (($cargasPorEstado['ENVIADO'] ?? 0) > 0)
                                <div class="d-flex align-items-center justify-content-between border rounded-3 p-2">
                                    <div class="d-flex align-items-center" style="gap:10px;">
                                        <span class="badge badge-warning">ENVIADO</span>
                                        <span class="text-muted small">Pendiente de revisión</span>
                                    </div>
                                    <span class="badge-soft">{{ (int) ($cargasPorEstado['ENVIADO'] ?? 0) }}</span>
                                </div>
                            @endif

                            {{-- BORRADOR --}}
                            @if (($cargasPorEstado['BORRADOR'] ?? 0) > 0)
                                <div class="d-flex align-items-center justify-content-between border rounded-3 p-2">
                                    <div class="d-flex align-items-center" style="gap:10px;">
                                        <span class="badge badge-secondary">BORRADOR</span>
                                        <span class="text-muted small">Sin enviar</span>
                                    </div>
                                    <span class="badge-soft">{{ (int) ($cargasPorEstado['BORRADOR'] ?? 0) }}</span>
                                </div>
                            @endif

                        </div>

                        @php
                            // ✅ Normaliza llaves de estatus para que el resumen sea consistente
                            $cargasNorm = [];

                            foreach ($cargasPorEstado ?? [] as $k => $v) {
                                $key = strtoupper(trim((string) $k));
                                $key = str_replace('REVISIÓN', 'REVISION', $key);
                                $key = preg_replace('/\s+/', ' ', $key);

                                $cargasNorm[$key] = ($cargasNorm[$key] ?? 0) + (int) $v;
                            }

                            // ✅ cálculos para el resumen
                            $borr = (int) ($cargasNorm['BORRADOR'] ?? 0);
                            $total = collect($cargasNorm)->sum();
                            $pctBorr = $total > 0 ? round(($borr / $total) * 100) : 0;
                        @endphp

                        @if ($borr > 0)
                            <div class="alert alert-warning mt-3 mb-0">
                                <i class="fas fa-exclamation-triangle mr-1"></i>
                                Hay <b>{{ $borr }}</b> borradores sin enviar ({{ $pctBorr }}%). Se
                                recomienda enviar recordatorio.
                            </div>
                        @else
                            <div class="alert alert-success mt-3 mb-0">
                                <i class="fas fa-check-circle mr-1"></i>
                                Excelente: no hay borradores pendientes.
                            </div>
                        @endif

                    </div>
                </div>

                {{-- ACCIÓN SUGERIDA --}}
                <div class="col-12 col-lg-5">
                    <div class="border rounded-3 p-3 bg-white h-100 d-flex flex-column justify-content-center">

                        <div class="d-flex align-items-start" style="gap:12px;">
                            {{-- ICONO --}}
                            <div class="rounded-circle d-flex align-items-center justify-content-center"
                                style="width:42px;height:42px;background:rgba(13,110,253,.12);">
                                <i class="fas fa-bell" style="color:#0d6efd;"></i>
                            </div>

                            {{-- CONTENIDO --}}
                            <div class="flex-grow-1">
                                <div class="font-weight-bold text-gray-800 mb-1">
                                    Acción sugerida
                                </div>

                                @if ($borr > 0)
                                    <div class="text-muted small mb-2">
                                        Hay borradores pendientes. Puedes enviar un recordatorio global.
                                    </div>

                                    <div class="border rounded-3 p-2 bg-light small">
                                        <i class="fas fa-bell mr-1"></i>
                                        Recomendación: usar
                                        <b>“Recordar borradores ({{ $borr }})”</b>.
                                    </div>

                                    <div class="text-muted small mt-2">
                                        El botón se encuentra en el encabezado de esta sección.
                                    </div>
                                @else
                                    <div class="text-muted small mb-2">
                                        No hay borradores pendientes. No se requiere acción.
                                    </div>

                                    <div class="border rounded-3 p-2 bg-light small">
                                        <i class="fas fa-check-circle mr-1"></i>
                                        Todo al día ✅
                                    </div>
                                @endif
                            </div>
                        </div>

                    </div>
                </div>

            </div>
        </div>
    </div>

    {{-- DEPENDENCIAS + # INDICADORES --}}
    @php
        $depIndicadores = DB::table('dependencias as d')
            ->leftJoin('formularios as f', 'f.id_depen', '=', 'd.id_depen')
            ->select('d.id_depen', 'd.nombre_depen', DB::raw('COUNT(DISTINCT f.id_ind) as indicadores_asignados'))
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
                                <div class="dep-mini-label">Avance de indicadores sin metas</div>

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

    {{-- =========================================================
   ✅ NUEVO: Metas por periodo (kardex) - Sección independiente
========================================================= --}}
    <div class="card pro-card mb-3">
        <div class="card-header d-flex align-items-center justify-content-between">
            <span>Metas por periodo (kardex)</span>
            <span class="text-muted small">Indicadores con meta</span>
        </div>

        <div class="card-body">
            <div class="row align-items-end">
                <div class="col-md-5 mb-2">
                    <label class="small text-muted mb-1">Dependencia</label>
                    <select class="form-control form-control-sm" wire:model.live="depSeleccionadaMetas">
                        @foreach ($depOptions as $id => $nombre)
                            <option value="{{ $id }}">{{ $nombre }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            @php
                $depId = (int) ($depSeleccionadaMetas ?? 0);

                $k = $kardexPorDep[$depId] ?? ['meta' => 0, 'hechas' => 0, 'pct' => 0, 'color' => 'secondary'];
                $kMeta = (int) $k['meta'];
                $kHechas = (int) $k['hechas'];
                $kPct = (int) $k['pct'];

                // ✅ AHORA viene por periodicidad: [depId][Mensual|Trimestral|Semestral|Anual] => cards[]
                $cardsByPeri = $kardexPeriodosPorDep[$depId] ?? [];

                $fill =
                    ($k['color'] ?? 'secondary') === 'success'
                        ? 'fill-success'
                        : (($k['color'] ?? 'secondary') === 'warning'
                            ? 'fill-warning'
                            : (($k['color'] ?? 'secondary') === 'danger'
                                ? 'fill-danger'
                                : 'fill-secondary'));

                $orden = ['APROBADO', 'EN REVISION', 'REENVIADO', 'ENVIADO', 'OBSERVADO', 'RECHAZADO', 'BORRADOR'];
            @endphp

            @if ($kMeta === 0)
                <div class="alert alert-light border mb-0">
                    <i class="fas fa-info-circle mr-1"></i>
                    Aún no hay metas por periodo configuradas para esta dependencia.
                </div>
            @else
                @foreach ($cardsByPeri as $peri => $cards)
                    <div class="mb-2 font-weight-bold text-gray-800">
                        {{ $peri }}
                    </div>

                    <div class="d-flex flex-wrap mb-3" style="gap:10px;">
                        @foreach ($cards as $c)
                            @php
                                $badge = $c['estado'] ?? 'secondary';

                                // ✅ el segmento ya viene listo (1..12, 1..4, 1..2, 1)
                                $segNum = (int) ($c['seg'] ?? 1);

                                // ✅ stats ahora: [dep][periodicidad][seg][estatus]
                                $stats = $estatusPorDepSeg[$depId][$peri][$segNum] ?? [];
                            @endphp

                            <div class="card shadow-sm" style="min-width: 160px; cursor:pointer;"
                                wire:click="verDetalleKardex({{ $depId }}, '{{ $peri }}', {{ $segNum }})">
                                <div class="card-body p-2">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div class="font-weight-bold">{{ $c['label'] }}</div>
                                        <span class="badge badge-{{ $badge }}">
                                            {{ $badge === 'success' ? 'Cumplido' : ($badge === 'warning' ? 'En progreso' : ($badge === 'danger' ? 'Atrasado' : 'Sin meta')) }}
                                        </span>
                                    </div>

                                    <div class="text-muted small mt-1">
                                        {{ $c['hechas'] }} / {{ $c['meta'] }}
                                    </div>

                                    @if (!empty($stats))
                                        <div class="text-muted small mt-2" style="line-height:1.15;">
                                            @foreach ($orden as $stName)
                                                @php $val = (int) ($stats[$stName] ?? 0); @endphp
                                                @if ($val > 0)
                                                    <div><strong>{{ $stName }}:</strong> {{ $val }}
                                                    </div>
                                                @endif
                                            @endforeach
                                        </div>
                                    @else
                                        <div class="text-muted small mt-2">Sin cargas</div>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endforeach

            @endif

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

    {{-- ✅ MODAL DENTRO DEL ROOT --}}
    @if ($modalOpen)
        <div wire:ignore.self class="modal fade show d-block" tabindex="-1"
            style="background:rgba(0,0,0,.45); position:fixed; inset:0; z-index:99999;">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">{{ $modalTitulo }}</h5>
                        <button type="button" class="btn-close" aria-label="Close"
                            wire:click="cerrarModal"></button>
                    </div>

                    <div class="modal-body">
                        @if (empty($modalCargas))
                            <div class="text-muted">Sin cargas para este periodo.</div>
                        @else
                            <div class="table-responsive">
                                <table class="table table-sm table-hover">
                                    <thead class="thead-light">
                                        <tr>
                                            <th>Folio</th>
                                            <th>Periodo</th>
                                            <th>Estatus</th>
                                            <th>Formulario</th>
                                            <th>Fecha</th>
                                            <th>Actualización</th>
                                            <th>Acciones</th> {{-- ✅ nuevo --}}
                                        </tr>
                                    </thead>

                                    <tbody>
                                        @foreach ($modalCargas as $c)
                                            <tr>
                                                <td class="font-weight-bold">{{ $c['folio'] }}</td>
                                                <td>{{ $c['periodo'] }} / {{ $c['ejercicio'] }}</td>
                                                <td>{{ $c['estatus'] }}</td>
                                                <td class="text-truncate" style="max-width:260px;">
                                                    {{ $c['formulario'] }}</td>
                                                <td class="text-muted">{{ $c['fecha'] }}</td>
                                                <td class="text-muted">{{ $c['actualizado'] }}</td>
                                                <td>
                                                    <a class="btn btn-sm btn-primary"
                                                        href="{{ route('admin.cargas.revision', $c['id_carga']) }}">
                                                        Ver
                                                    </a>
                                                </td>

                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif
                    </div>

                    <div class="modal-footer">
                        <button class="btn btn-secondary" wire:click="cerrarModal">Cerrar</button>
                    </div>
                </div>
            </div>
        </div>

        <style>
            body {
                overflow: hidden;
            }
        </style>
    @endif

</div>

@once
    @push('scripts')
        {{-- ✅ Cargar librerías SOLO UNA VEZ --}}
        <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1"></script>
        <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2.2.0"></script>

        <script>
            (function() {

                // =========================
                // ✅ REGISTRO ÚNICO DE PLUGINS (NO DUPLICAR)
                // =========================
                function registerChartPluginsOnce() {
                    if (!window.Chart) return;

                    if (window.__chartPluginsRegistered) return;
                    window.__chartPluginsRegistered = true;

                    // ✅ PLUGIN TEXTO CENTRADO (para gauges)
                    const centerTextPlugin = {
                        id: 'centerTextPlugin',
                        afterDraw(chart, args, options) {
                            options = options || {};

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

                    // ✅ datalabels SOLO UNA VEZ
                    if (window.ChartDataLabels) {
                        Chart.register(window.ChartDataLabels);
                    }
                }

                function initAdminCharts() {
                    registerChartPluginsOnce();

                    const dataContainer = document.getElementById("chart-data");
                    if (!dataContainer) return;

                    const roles = JSON.parse(dataContainer.dataset.roles || "{}");
                    let cargas = JSON.parse(dataContainer.dataset.cargas || "{}");

                    window.__charts = window.__charts || {};

                    // =========================
                    // ✅ GAUGES (Admins / Usuarios)
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

                    function gauge(canvas, pct, mainText, subText, key) {
                        if (!canvas) return;

                        if (window.__charts[key]) window.__charts[key].destroy();

                        window.__charts[key] = new Chart(canvas, {
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
                                        mainText: String(mainText ?? ''),
                                        subText: String(subText ?? '')
                                    }
                                }
                            }
                        });
                    }

                    gauge(document.getElementById('roleAdminGauge'), pctAdmin, adminsCount + " admins", pctAdmin + "%",
                        "gaugeAdmin");
                    gauge(document.getElementById('roleUsuarioGauge'), pctUser, usersCount + " usuarios", pctUser + "%",
                        "gaugeUsuario");


                    // =========================
                    // ✅ CARGAS POR ESTADO (HORIZONTAL PRO)
                    // =========================
                    const ordenEstados = ["APROBADO", "EN REVISION", "REENVIADO", "ENVIADO", "OBSERVADO", "RECHAZADO",
                        "BORRADOR", "SIN CAPTURA"
                    ];

                    const estadoStyle = {
                        "APROBADO": {
                            bg: "rgba(25,135,84,.85)",
                            bd: "rgba(25,135,84,1)"
                        },
                        "EN REVISION": {
                            bg: "rgba(255,193,7,.85)",
                            bd: "rgba(255,193,7,1)"
                        },
                        "REENVIADO": {
                            bg: "rgba(255,193,7,.70)",
                            bd: "rgba(255,193,7,1)"
                        },
                        "ENVIADO": {
                            bg: "rgba(13,110,253,.75)",
                            bd: "rgba(13,110,253,1)"
                        },
                        "OBSERVADO": {
                            bg: "rgba(253,126,20,.80)",
                            bd: "rgba(253,126,20,1)"
                        },
                        "RECHAZADO": {
                            bg: "rgba(220,53,69,.85)",
                            bd: "rgba(220,53,69,1)"
                        },
                        "BORRADOR": {
                            bg: "rgba(108,117,125,.70)",
                            bd: "rgba(108,117,125,1)"
                        },
                        "SIN CAPTURA": {
                            bg: "rgba(173,181,189,.70)",
                            bd: "rgba(173,181,189,1)"
                        },
                        "DEFAULT": {
                            bg: "rgba(108,117,125,.55)",
                            bd: "rgba(108,117,125,1)"
                        },
                    };

                    function normKey(k) {
                        return String(k || "")
                            .toUpperCase()
                            .trim()
                            .replace("REVISIÓN", "REVISION");
                    }

                    function buildEntries(rawObj) {
                        const map = {};
                        Object.entries(rawObj || {}).forEach(([k, v]) => {
                            const key = normKey(k);
                            map[key] = (map[key] || 0) + (Number(v) || 0);
                        });

                        const entries = Object.entries(map);
                        entries.sort((a, b) => {
                            const ia = ordenEstados.indexOf(a[0]);
                            const ib = ordenEstados.indexOf(b[0]);
                            return (ia === -1 ? 999 : ia) - (ib === -1 ? 999 : ib);
                        });

                        return entries;
                    }

                    function renderChips(entries, total) {
                        const wrap = document.getElementById("chipsEstados");
                        if (!wrap) return;

                        wrap.innerHTML = "";

                        entries.forEach(([k, v]) => {
                            const pct = total ? Math.round((v / total) * 100) : 0;
                            const st = estadoStyle[k] || estadoStyle.DEFAULT;

                            const chip = document.createElement("div");
                            chip.className = "badge-soft";
                            chip.style.display = "inline-flex";
                            chip.style.alignItems = "center";
                            chip.style.gap = "6px";

                            const dot = document.createElement("span");
                            dot.style.width = "10px";
                            dot.style.height = "10px";
                            dot.style.borderRadius = "999px";
                            dot.style.background = st.bd;

                            const txt = document.createElement("span");
                            txt.textContent = `${k}: ${v} (${pct}%)`;

                            chip.appendChild(dot);
                            chip.appendChild(txt);
                            wrap.appendChild(chip);
                        });
                    }

                    function renderCargasEstadoChart() {
                        const canvas = document.getElementById("chartCargasEstado");
                        if (!canvas) return;

                        const entries = buildEntries(cargas);
                        const labels = entries.map(e => e[0]);
                        const values = entries.map(e => e[1]);
                        const total = values.reduce((a, b) => a + b, 0) || 0;

                        // ✅ Top estado
                        let topIdx = 0;
                        for (let i = 1; i < values.length; i++) {
                            if (values[i] > values[topIdx]) topIdx = i;
                        }
                        const topLabel = labels[topIdx] || "—";
                        const topVal = values[topIdx] || 0;
                        const topPct = total ? Math.round((topVal / total) * 100) : 0;

                        // ✅ % borradores
                        const borrIdx = labels.indexOf("BORRADOR");
                        const borrVal = borrIdx >= 0 ? values[borrIdx] : 0;
                        const borrPct = total ? Math.round((borrVal / total) * 100) : 0;

                        // pinta labels en header
                        const totalLabel = document.getElementById("totalCargasLabel");
                        const topEstadoLabel = document.getElementById("topEstadoLabel");
                        const pctBorradoresLabel = document.getElementById("pctBorradoresLabel");

                        if (totalLabel) totalLabel.textContent = `Total: ${total}`;
                        if (topEstadoLabel) topEstadoLabel.textContent = `Top: ${topLabel} (${topPct}%)`;
                        if (pctBorradoresLabel) pctBorradoresLabel.textContent = `% borrador: ${borrPct}%`;

                        // chips abajo
                        renderChips(entries, total);

                        const bgColors = labels.map((k, i) => (estadoStyle[k] || estadoStyle.DEFAULT).bg);
                        const bdColors = labels.map(k => (estadoStyle[k] || estadoStyle.DEFAULT).bd);

                        // destruir si existe
                        if (window.__charts.cargasEstado) window.__charts.cargasEstado.destroy();

                        window.__charts.cargasEstado = new Chart(canvas, {
                            type: "bar",
                            data: {
                                labels,
                                datasets: [{
                                    label: "Cargas",
                                    data: values,
                                    backgroundColor: bgColors,
                                    borderColor: bdColors,
                                    borderWidth: 1,
                                    borderRadius: 10,
                                    barThickness: 16,
                                    maxBarThickness: 18
                                }]
                            },
                            options: {
                                responsive: true,
                                maintainAspectRatio: false,
                                indexAxis: "y",
                                layout: {
                                    padding: {
                                        right: 14,
                                        left: 6
                                    }
                                },
                                plugins: {
                                    legend: {
                                        display: false
                                    },
                                    tooltip: {
                                        callbacks: {
                                            title: (items) => items?.[0]?.label ?? '',
                                            label: (ctx) => {
                                                const v = ctx.parsed.x || 0;
                                                const pct = total ? (v / total) * 100 : 0;
                                                const isTop = ctx.dataIndex === topIdx;
                                                return ` ${v} cargas (${pct.toFixed(1)}%)${isTop ? "  • TOP" : ""}`;
                                            }
                                        }
                                    },
                                    datalabels: {
                                        anchor: "end",
                                        align: "right",
                                        offset: 6,
                                        clamp: true,
                                        formatter: (value) => {
                                            const pct = total ? Math.round((value / total) * 100) : 0;
                                            return value === 0 ? "0" : `${value} (${pct}%)`;
                                        },
                                        font: {
                                            weight: "700"
                                        },
                                        color: "#111"
                                    }
                                },
                                scales: {
                                    x: {
                                        beginAtZero: true,
                                        ticks: {
                                            precision: 0
                                        },
                                        grid: {
                                            color: "rgba(0,0,0,.06)"
                                        }
                                    },
                                    y: {
                                        grid: {
                                            display: false
                                        },
                                        ticks: {
                                            font: {
                                                weight: "700"
                                            }
                                        }
                                    }
                                }
                            }
                        });
                    }

                    renderCargasEstadoChart();

                    // =========================
                    // ✅ Livewire: refrescar sin duplicar hooks
                    // =========================
                    if (!window.__lwChartsHooked) {
                        window.__lwChartsHooked = true;

                        document.addEventListener('livewire:initialized', () => {
                            if (!window.Livewire) return;

                            Livewire.hook('message.processed', () => {
                                const dc = document.getElementById("chart-data");
                                if (!dc) return;

                                // actualiza dataset
                                cargas = JSON.parse(dc.dataset.cargas || "{}");

                                // repinta
                                renderCargasEstadoChart();
                            });
                        });
                    }
                }

                document.addEventListener("DOMContentLoaded", initAdminCharts);
            })
            ();
        </script>
    @endpush
@endonce
