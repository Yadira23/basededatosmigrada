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
            $filtro = request('f', 'todos'); // todos | con | sin

            $query = \App\Models\Formulario::publicados()
                ->porDependencia(auth()->user()->id_depen)
                ->with('indicador');

            // Buscar por nombre del indicador
            if ($q !== '') {
                $query->whereHas('indicador', function ($qq) use ($q) {
                    $qq->where('nombre_ind', 'like', "%{$q}%");
                });
            }

            // Filtro por indicador
            if ($filtro === 'con') {
                $query->whereNotNull('id_ind');
            } elseif ($filtro === 'sin') {
                $query->whereNull('id_ind');
            }

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
            $ultimasCargasMeta = Carga::selectRaw('id_form, id_meta, MAX(id_carga) as ultima_id')
                ->whereIn('id_form', $formularios->pluck('id_form')->unique())
                ->whereNotNull('id_meta')
                ->groupBy('id_form', 'id_meta')
                ->get();

            $cargasUltimasPorMeta = Carga::whereIn('id_carga', $ultimasCargasMeta->pluck('ultima_id'))
                ->get()
                ->keyBy(fn($c) => $c->id_form . '-' . $c->id_meta);

            // ===========================
            // 3) ÚLTIMA CARGA GENERAL por FORMULARIO (para la tarjeta principal)
            // ===========================
            $ultimasCargasForm = Carga::selectRaw('id_form, MAX(id_carga) as ultima_id')
                ->whereIn('id_form', $formularios->pluck('id_form')->unique())
                ->groupBy('id_form')
                ->get()
                ->pluck('ultima_id', 'id_form');

            $cargasUltimas = Carga::whereIn('id_carga', $ultimasCargasForm->values())->get()->keyBy('id_form');
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

        {{-- CHIPS --}}
        @php
            $total = $formularios->count();
            $disponibles = $formularios->filter(fn($f) => !is_null($f->indicador))->count();
            $sinIndicador = $formularios->filter(fn($f) => is_null($f->indicador))->count();
        @endphp

        <div class="d-flex flex-wrap gap-2 mb-3">
            <a href="{{ url()->current() }}?f=todos&q={{ request('q') }}&sort={{ request('sort', 'recientes') }}"
                class="btn btn-sm {{ $filtro === 'todos' ? 'btn-primary' : 'btn-outline-primary' }} shadow-sm">
                Todos <span class="badge bg-white text-primary ms-1">{{ $total }}</span>
            </a>

            <a href="{{ url()->current() }}?f=con&q={{ request('q') }}&sort={{ request('sort', 'recientes') }}"
                class="btn btn-sm {{ $filtro === 'con' ? 'btn-success' : 'btn-outline-success' }}">
                Con indicador <span class="badge bg-success ms-1">{{ $disponibles }}</span>
            </a>

            <a href="{{ url()->current() }}?f=sin&q={{ request('q') }}&sort={{ request('sort', 'recientes') }}"
                class="btn btn-sm {{ $filtro === 'sin' ? 'btn-secondary' : 'btn-outline-secondary' }}">
                Sin indicador <span class="badge bg-secondary ms-1">{{ $sinIndicador }}</span>
            </a>
        </div>

        @if ($filtro !== 'todos')
            <div class="text-muted small mb-2">
                Mostrando:
                <strong>{{ $filtro === 'con' ? 'Formularios con indicador' : 'Formularios sin indicador' }}</strong>
            </div>
        @endif

        @if (request('q') && $formularios->isEmpty())
            <div class="alert alert-info">
                No se encontraron indicadores para: <strong>{{ request('q') }}</strong>
            </div>
        @endif

        <div class="row">
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

                <div class="col-12 col-md-6 col-xl-4 mb-3">
                    <div class="card shadow-sm border-0 h-100" style="border-radius:14px;">
                        <div class="card-body" style="background:#dde7f8 !important;">

                            @php
                                // ✅ ÚLTIMA CARGA GENERAL DEL FORMULARIO
                                $ultima = $cargasUltimas[$form->id_form] ?? null;
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

                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <div class="pe-2">
                                    <h5 class="mb-1">
                                        {{ $form->indicador->nombre_ind ?? 'Indicador sin asignar' }}
                                    </h5>
                                    <div class="text-muted small">
                                        Formulario #{{ $form->id_form }}
                                    </div>
                                </div>

                                <div class="text-end">
                                    <span class="badge {{ $badge }}">{{ $estado }}</span>

                                    @if ($ultima && !empty($ultima->fecha_carga))
                                        <div class="text-muted small mt-1">
                                            Última: {{ \Carbon\Carbon::parse($ultima->fecha_carga)->format('d M Y') }}
                                        </div>
                                    @endif
                                </div>
                            </div>

                            @if ($form->indicador)
                                @php
                                    $metas = \App\Models\Meta::where('id_ind', $form->id_ind)
                                        ->where(function ($q) use ($form) {
                                            $q->whereNull('id_form')->orWhere('id_form', $form->id_form);
                                        })
                                        ->orderBy('orden')
                                        ->get();

                                    $totalMetas = $metas->count();
                                    $aprobadas = 0;

                                @endphp

                                <div class="d-flex flex-wrap gap-2 mt-2">
                                    <span class="badge bg-light text-dark border">
                                        Periodo: <b>{{ strtolower($form->periodo_form) }}</b>
                                    </span>

                                    <span class="badge bg-light text-dark border">
                                        Reporte: <b>{{ $periodoReportarTexto }}</b>
                                    </span>
                                </div>

                                <div class="d-flex justify-content-end mb-2">
                                    <a class="btn btn-sm btn-primary shadow-sm"
                                        href="{{ route('usuario.indicadores.show', $form->id_form) }}">
                                        Ver indicador
                                    </a>
                                </div>
                            @else
                                <div class="text-muted">
                                    Este registro no tiene indicador asignado.
                                </div>
                            @endif

                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12">
                    <div class="alert alert-warning mb-0">
                        No hay indicadores disponibles para tu dependencia.
                    </div>
                </div>
            @endforelse
        </div>
    </div>
@endsection

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/usuario-indicadores.css') }}">
@endpush
