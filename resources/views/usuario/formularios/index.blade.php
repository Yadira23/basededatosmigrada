@extends('layouts.usuario')

@section('titulo', 'Indicadores')

@section('content')
    <div class="py-3">

        {{-- HEADER USUARIO --}}
        <div class="d-flex justify-content-between align-items-start mb-3">

            <div>
                <h4 class="mb-0">Indicadores disponibles</h4>
                <small class="text-muted">
                    Consulta y captura información de los indicadores asignados a tu dependencia.
                </small>
            </div>
        </div>

        @php
            $q = trim(request('q', ''));
            $sort = request('sort', 'recientes'); // recientes | nombre
            $filtro = request('f', 'todos');
            // todos | sin_metas | con_metas | aprobados | observados | enviados | borradores

            $query = \App\Models\Formulario::publicados()
                ->porDependencia(auth()->user()->id_depen)
                ->with(['indicador', 'indicador.metas', 'ultimaCarga']);

            // Buscar por nombre del indicador
            if ($q !== '') {
                $query->whereHas('indicador', function ($qq) use ($q) {
                    $qq->where('nombre_ind', 'like', "%{$q}%");
                });
            }

            $base = clone $query; // base SIN filtros para contar

            // ✅ Filtros nuevos
            switch ($filtro) {
                case 'sin_metas':
                    $query->whereDoesntHave('indicador.metas');
                    break;

                case 'con_metas':
                    $query->whereHas('indicador.metas');
                    break;

                case 'aprobados':
                    $query->whereHas('ultimaCarga', fn($c) => $c->where('status_env', 'APROBADO'));
                    break;

                case 'observados':
                    $query->whereHas('cargas', fn($c) => $c->where('status_env', 'OBSERVADO'));
                    break;

                case 'enviados':
                    $query->whereHas('ultimaCarga', fn($c) => $c->where('status_env', 'ENVIADO'));
                    break;

                case 'borradores':
                    $query->whereHas('ultimaCarga', fn($c) => $c->where('status_env', 'BORRADOR'));
                    break;

                default:
                    // todos
                    break;
            }

            $counts = [
                'todos' => (clone $base)->count(),
                'sin_metas' => (clone $base)->whereDoesntHave('indicador.metas')->count(),
                'con_metas' => (clone $base)->whereHas('indicador.metas')->count(),
                'aprobados' => (clone $base)
                    ->whereHas('ultimaCarga', fn($c) => $c->where('status_env', 'APROBADO'))
                    ->count(),
                'observados' => (clone $base)
                    ->whereHas('cargas', fn($c) => $c->where('status_env', 'OBSERVADO'))
                    ->count(),
                'enviados' => (clone $base)
                    ->whereHas('ultimaCarga', fn($c) => $c->where('status_env', 'ENVIADO'))
                    ->count(),
                'borradores' => (clone $base)
                    ->whereHas('ultimaCarga', fn($c) => $c->where('status_env', 'BORRADOR'))
                    ->count(),
            ];

            // Ordenar
            if ($sort === 'nombre') {
                $formularios = $query->get()->sortBy(fn($f) => $f->indicador->nombre_ind ?? '')->values();
            } else {
                $formularios = $query->orderByDesc('id_form')->get();
            }
        @endphp

        @php
            use App\Models\Meta;
            use App\Models\Carga;

            // ===========================
            // 1) METAS por INDICADOR
            // ===========================
            $metasPorIndicador = Meta::whereIn('id_ind', $formularios->pluck('id_ind')->filter()->unique())
                ->where(function ($q) use ($formularios) {
                    $q->whereNull('id_form')->orWhereIn('id_form', $formularios->pluck('id_form')->unique());
                })
                ->orderBy('orden')
                ->get()
                ->groupBy('id_ind');

            // ===========================
            // 2) ÚLTIMA CARGA por (FORM + META)
            // ===========================
            $ultimasCargasMeta = Carga::selectRaw('id_form, meta_id, MAX(id_carga) as ultima_id')
                ->whereIn('id_form', $formularios->pluck('id_form')->unique())
                ->whereNotNull('meta_id')
                ->groupBy('id_form', 'meta_id')
                ->get();

            $cargasUltimasPorMeta = Carga::whereIn('id_carga', $ultimasCargasMeta->pluck('ultima_id'))
                ->get()
                ->keyBy(fn($c) => $c->id_form . '-' . $c->meta_id);

        @endphp

        {{-- BARRA DE ACCIONES --}}
        <div class="card shadow-sm border-0 mb-3">
            <div class="card-body py-2">
                <div class="d-flex flex-wrap justify-content-between align-items-center gap-2">

                    <form class="d-flex align-items-center gap-2" method="GET" action="{{ url()->current() }}">
                        <div class="input-group input-group-sm" style="min-width:280px;">
                            <span class="input-group-text bg-white">
                                <i class="fas fa-search text-muted"></i>
                            </span>
                            <input type="text" name="q" class="form-control" placeholder="Buscar indicador..."
                                value="{{ request('q') }}">
                        </div>

                        <input type="hidden" name="sort" value="{{ request('sort', 'recientes') }}">

                        <button class="btn btn-sm btn-primary shadow-sm" type="submit">Buscar</button>

                        @if (request('q'))
                            <a class="btn btn-sm btn-outline-secondary"
                                href="{{ url()->current() }}?sort={{ request('sort', 'recientes') }}">
                                Limpiar
                            </a>
                        @endif

                        <span class="text-muted small ms-2">
                            {{ $formularios->count() }} indicador(es)
                        </span>
                    </form>

                    <form method="GET" action="{{ url()->current() }}">
                        <input type="hidden" name="q" value="{{ request('q') }}">
                        <input type="hidden" name="f" value="{{ request('f', 'todos') }}">

                        <select class="form-select form-select-sm" name="sort" style="min-width:190px;"
                            onchange="this.form.submit()">
                            <option value="recientes" @selected(request('sort', 'recientes') === 'recientes')>
                                Ordenar: más recientes
                            </option>
                            <option value="nombre" @selected(request('sort') === 'nombre')>
                                Ordenar: nombre (A–Z)
                            </option>
                        </select>
                    </form>

                </div>
            </div>
        </div>

        <div class="d-flex flex-wrap gap-2 mb-3">
            <a href="{{ url()->current() }}?f=todos&q={{ request('q') }}&sort={{ request('sort', 'recientes') }}"
                class="btn btn-sm {{ $filtro === 'todos' ? 'btn-primary' : 'btn-outline-primary' }} shadow-sm">
                Todos <span class="badge bg-white text-primary ms-1">{{ $counts['todos'] }}</span>
            </a>

            <a href="{{ url()->current() }}?f=sin_metas&q={{ request('q') }}&sort={{ request('sort', 'recientes') }}"
                class="btn btn-sm {{ $filtro === 'sin_metas' ? 'btn-secondary' : 'btn-outline-secondary' }}">
                Sin metas <span class="badge bg-white text-primary ms-1">{{ $counts['sin_metas'] }}</span>
            </a>

            <a href="{{ url()->current() }}?f=con_metas&q={{ request('q') }}&sort={{ request('sort', 'recientes') }}"
                class="btn btn-sm {{ $filtro === 'con_metas' ? 'btn-success' : 'btn-outline-success' }}">
                Con metas <span class="badge bg-white text-primary ms-1">{{ $counts['con_metas'] }}</span>
            </a>

            <a href="{{ url()->current() }}?f=aprobados&q={{ request('q') }}&sort={{ request('sort', 'recientes') }}"
                class="btn btn-sm {{ $filtro === 'aprobados' ? 'btn-success' : 'btn-outline-success' }}">
                Aprobados <span class="badge bg-white text-primary ms-1">{{ $counts['aprobados'] }}</span>
            </a>

            <a href="{{ url()->current() }}?f=observados&q={{ request('q') }}&sort={{ request('sort', 'recientes') }}"
                class="btn btn-sm {{ $filtro === 'observados' ? 'btn-warning text-dark' : 'btn-outline-warning' }}">
                Observados <span class="badge bg-white text-primary ms-1">{{ $counts['observados'] }}</span>
            </a>

            <a href="{{ url()->current() }}?f=enviados&q={{ request('q') }}&sort={{ request('sort', 'recientes') }}"
                class="btn btn-sm {{ $filtro === 'enviados' ? 'btn-info' : 'btn-outline-info' }}">
                Enviados <span class="badge bg-white text-primary ms-1">{{ $counts['enviados'] }}</span>
            </a>

            <a href="{{ url()->current() }}?f=borradores&q={{ request('q') }}&sort={{ request('sort', 'recientes') }}"
                class="btn btn-sm {{ $filtro === 'borradores' ? 'btn-dark' : 'btn-outline-dark' }}">
                Borradores <span class="badge bg-white text-primary ms-1">{{ $counts['borradores'] }}</span>
            </a>
        </div>

        @if ($filtro !== 'todos')
            @php
                $labelFiltro = match ($filtro) {
                    'sin_metas' => 'Indicadores sin metas',
                    'con_metas' => 'Indicadores con metas',
                    'aprobados' => 'Aprobados',
                    'observados' => 'Observados',
                    'enviados' => 'Enviados',
                    'borradores' => 'Borradores',
                    default => 'Todos',
                };
            @endphp

            <div class="text-muted small mb-2">
                Mostrando: <strong>{{ $labelFiltro }}</strong>
            </div>
        @endif

        @if (request('q') && $formularios->isEmpty())
            <div class="alert alert-info">
                No se encontraron indicadores para: <strong>{{ request('q') }}</strong>
            </div>
        @endif

        <div class="row g-3">
            @forelse($formularios as $form)
                @php
                    $hoy = now()->toDateString();
                    $periodoPermitido = \App\Models\Formulario::periodoYMActualPermitido($form->periodo_form, $hoy);

                    // "Enero 2026"
                    // Texto bonito para "Periodo a reportar" según periodicidad del INDICADOR
                    $periodoBase = \Carbon\Carbon::createFromFormat('Y-m', $periodoPermitido)->startOfMonth();
                    $perInd = mb_strtoupper(
                        trim((string) ($form->indicador->periodo_ind ?? ($form->periodo_form ?? 'MENSUAL'))),
                    );

                    if ($perInd === 'MENSUAL') {
                        $periodoReportarTexto = $periodoBase->locale('es')->translatedFormat('F Y'); // "Enero 2026"
                    } elseif ($perInd === 'TRIMESTRAL') {
                        $t = (int) ceil($periodoBase->month / 3);
                        $periodoReportarTexto = "T{$t} " . $periodoBase->format('Y'); // "T1 2026"
                    } elseif ($perInd === 'SEMESTRAL') {
                        $s = $periodoBase->month <= 6 ? 1 : 2;
                        $periodoReportarTexto = "S{$s} " . $periodoBase->format('Y'); // "S1 2026"
                    } else {
                        // ANUAL
                        $periodoReportarTexto = 'Ejercicio ' . $periodoBase->format('Y'); // "Ejercicio 2026"
                    }

                    $ymHoy = now()->format('Y-m');
                    $habilitadoHoy = !empty($periodoPermitido);
                @endphp

                <div class="col-12 col-md-6 col-xl-4 col-xxl-3">
                    <div class="indicador-card h-100">
                        <div class="indicador-card__body">

                            @php
                                $ultima = $form->ultimaCarga ?? null;
                                $estado = $ultima->status_env ?? 'SIN CAPTURA';

                                $badge = match ($estado) {
                                    'APROBADO' => 'bg-success',
                                    'EN REVISION' => 'bg-info',
                                    'OBSERVADO' => 'bg-warning text-dark',
                                    'REENVIADO' => 'bg-primary',
                                    'ENVIADO' => 'bg-secondary',
                                    'BORRADOR' => 'bg-dark',
                                    default => 'bg-dark',
                                };
                            @endphp

                            <div class="indicador-card__header">
                                <div class="indicador-card__title-wrap">
                                    <h5 class="indicador-card__title">
                                        {{ $form->indicador->nombre_ind ?? 'Indicador sin asignar' }}
                                    </h5>

                                    <div class="indicador-card__subtitle">
                                        Formulario #{{ $form->id_form }}
                                    </div>
                                </div>

                                <div class="indicador-card__status">
                                    <span class="estado-badge {{ $badge }}">
                                        {{ $estado }}
                                    </span>

                                    @if ($ultima && !empty($ultima->fecha_carga))
                                        <div class="indicador-card__date">
                                            Última: {{ \Carbon\Carbon::parse($ultima->fecha_carga)->format('d M Y') }}
                                        </div>
                                    @endif
                                </div>
                            </div>

                            @if ($form->indicador)
                                @php
                                    $totalMetas = $form->indicador?->metas?->count() ?? 0;
                                @endphp

                                <div class="indicador-card__chips">
                                    <span class="meta-chip">
                                        <span class="meta-chip__label">Periodo:</span>
                                        <span>{{ strtolower($form->indicador->periodo_ind ?? $form->periodo_form) }}</span>
                                    </span>

                                    <span class="meta-chip">
                                        <span class="meta-chip__label">Reporte:</span>
                                        <span>{{ $periodoReportarTexto }}</span>
                                    </span>
                                </div>

                                <div class="indicador-card__footer">
                                    <a class="btn btn-primary btn-indicador shadow-sm"
                                        href="{{ route('usuario.indicadores.show', $form->id_form) }}">
                                        Ver indicador
                                    </a>
                                </div>
                            @else
                                <div class="text-muted mt-2">
                                    Este registro no tiene indicador asignado.
                                </div>
                            @endif

                        </div>
                    </div>
                </div>
            @empty
                @php
                    $msgVacio = match ($filtro) {
                        'aprobados' => 'Aún no tienes indicadores aprobados.',
                        'observados' => 'Aún no tienes indicadores con observaciones.',
                        'enviados' => 'Aún no tienes indicadores enviados.',
                        'borradores' => 'Aún no tienes indicadores en borrador.',
                        'con_metas' => 'Aún no tienes indicadores con metas.',
                        'sin_metas' => 'Aún no tienes indicadores sin metas.',
                        default => 'No hay indicadores disponibles para tu dependencia.',
                    };

                    $tipo = $filtro === 'todos' ? 'warning' : 'info';
                @endphp

                <div class="col-12">
                    <div class="alert alert-{{ $tipo }} mb-0">
                        {{ $msgVacio }}
                    </div>

                    @if ($filtro !== 'todos')
                        <div class="mt-2">
                            <a class="btn btn-sm btn-outline-primary"
                                href="{{ url()->current() }}?f=todos&q={{ request('q') }}&sort={{ request('sort', 'recientes') }}">
                                Ver todos los indicadores
                            </a>
                        </div>
                    @endif
                </div>
            @endforelse
        </div>
    </div>
@endsection

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/usuario-indicadores.css') }}">
@endpush
