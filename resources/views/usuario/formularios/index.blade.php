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

    // Buscar por nombre del indicador (si hay texto)
    if ($q !== '') {
    $query->whereHas('indicador', function($qq) use ($q) {
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
    // Ordenar por nombre del indicador (usa join lógico vía whereHas + orderBy en relación no directo)
    // Solución simple: ordenar ya en colección después de traer resultados:
    $formularios = $query->get()->sortBy(function($f){
    return $f->indicador->nombre_ind ?? '';
    })->values();
    } else {
    // Más recientes (por id_form desc)
    $formularios = $query->orderByDesc('id_form')->get();
    }
    @endphp

    @php
    $ultimoStatusPorForm = \App\Models\Carga::query()
    ->selectRaw('id_form, MAX(id_carga) as ultima_id')
    ->groupBy('id_form')
    ->pluck('ultima_id', 'id_form');

    $cargasUltimas = \App\Models\Carga::whereIn('id_carga', $ultimoStatusPorForm->values())
    ->get()
    ->keyBy('id_form');
    @endphp

    {{-- BARRA DE ACCIONES (funcional) --}}
    <div class="card shadow-sm border-0 mb-3">
        <div class="card-body py-2">
            <div class="d-flex flex-wrap justify-content-between align-items-center gap-2">

                <form class="d-flex align-items-center gap-2" method="GET" action="{{ url()->current() }}">
                    <div class="input-group input-group-sm" style="min-width:280px;">
                        <span class="input-group-text bg-white">
                            <i class="fas fa-search text-muted"></i>
                        </span>
                        <input type="text"
                            name="q"
                            class="form-control"
                            placeholder="Buscar indicador..."
                            value="{{ request('q') }}">
                    </div>

                    {{-- conservar sort al buscar --}}
                    <input type="hidden" name="sort" value="{{ request('sort', 'recientes') }}">

                    <button class="btn btn-sm btn-primary shadow-sm" type="submit">
                        Buscar
                    </button>

                    @if(request('q'))
                    <a class="btn btn-sm btn-outline-secondary" href="{{ url()->current() }}?sort={{ request('sort','recientes') }}">
                        Limpiar
                    </a>
                    @endif

                    <span class="text-muted small ms-2">
                        {{ $formularios->count() }} indicador(es)
                    </span>
                </form>

                <form method="GET" action="{{ url()->current() }}">
                    {{-- conservar q al ordenar --}}
                    <input type="hidden" name="q" value="{{ request('q') }}">

                    <select class="form-select form-select-sm"
                        name="sort"
                        style="min-width:190px;"
                        onchange="this.form.submit()">
                        <option value="recientes" @selected(request('sort','recientes')==='recientes' )>
                            Ordenar: más recientes
                        </option>
                        <option value="nombre" @selected(request('sort')==='nombre' )>
                            Ordenar: nombre (A–Z)
                        </option>
                    </select>
                </form>

            </div>
        </div>
    </div>

    {{-- CHIPS (solo UI por ahora) --}}
    @php
    $total = $formularios->count();
    $disponibles = $formularios->filter(fn($f) => !is_null($f->indicador))->count();
    $sinIndicador = $formularios->filter(fn($f) => is_null($f->indicador))->count();
    @endphp

    <div class="d-flex flex-wrap gap-2 mb-3">

        {{-- TODOS --}}
        <a href="{{ url()->current() }}?f=todos&q={{ request('q') }}&sort={{ request('sort','recientes') }}"
            class="btn btn-sm {{ $filtro === 'todos' ? 'btn-primary' : 'btn-outline-primary' }} shadow-sm">
            Todos
            <span class="badge bg-white text-primary ms-1">{{ $total }}</span>
        </a>

        {{-- CON INDICADOR --}}
        <a href="{{ url()->current() }}?f=con&q={{ request('q') }}&sort={{ request('sort','recientes') }}"
            class="btn btn-sm {{ $filtro === 'con' ? 'btn-success' : 'btn-outline-success' }}">
            Con indicador
            <span class="badge bg-success ms-1">{{ $disponibles }}</span>
        </a>

        {{-- SIN INDICADOR --}}
        <a href="{{ url()->current() }}?f=sin&q={{ request('q') }}&sort={{ request('sort','recientes') }}"
            class="btn btn-sm {{ $filtro === 'sin' ? 'btn-secondary' : 'btn-outline-secondary' }}">
            Sin indicador
            <span class="badge bg-secondary ms-1">{{ $sinIndicador }}</span>
        </a>

    </div>

    @if($filtro !== 'todos')
    <div class="text-muted small mb-2">
        Mostrando:
        <strong>
            {{ $filtro === 'con' ? 'Formularios con indicador' : 'Formularios sin indicador' }}
        </strong>
    </div>
    @endif

    @if(request('q') && $formularios->isEmpty())
    <div class="alert alert-info">
        No se encontraron indicadores para: <strong>{{ request('q') }}</strong>
    </div>
    @endif

    <div class="row">
        @forelse($formularios as $form)
        <div class="col-12 col-md-6 col-xl-4 mb-3">
            <div class="card shadow-sm border-0 h-100" style="border-radius:14px;">
                <div class="card-body">
                    @php
                    $ultima = $cargasUltimas[$form->id_form] ?? null;
                    $estado = $ultima->status_env ?? 'SIN CAPTURA';

                    $badge = match ($estado) {
                    'APROBADO' => 'bg-success',
                    'EN REVISION' => 'bg-info',
                    'OBSERVADO' => 'bg-warning text-dark',
                    'REENVIADO' => 'bg-primary',
                    'ENVIADO' => 'bg-secondary',
                    default => 'bg-dark',
                    };
                    @endphp
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div>
                            <h5 class="mb-0">
                                {{ $form->indicador->nombre_ind ?? ('Indicador (sin asignar) - Form #' . $form->id_form) }}
                            </h5>
                            <small class="text-muted">Información del indicador</small>
                            <div class="text-muted small mt-1">
                                Formulario #{{ $form->id_form }}
                            </div>
                        </div>

                        <div class="text-end">
                            <span class="badge {{ $badge }}">{{ $estado }}</span>

                            @if($ultima && !empty($ultima->fecha_carga))
                            <div class="text-muted small mt-1">
                                Última: {{ \Carbon\Carbon::parse($ultima->fecha_carga)->format('d M Y') }}
                            </div>
                            @endif
                        </div>
                    </div>

                    @if($form->indicador)
                    <div class="d-flex flex-wrap gap-4">
                        <div>
                            <div class="text-muted small">Periodo</div>
                            <div class="fw-semibold">{{ $form->periodo_form }}</div>
                        </div>

                        <div>
                            <div class="text-muted small">Unidad</div>
                            <div class="fw-semibold">{{ $form->indicador->unidadmedida_ind }}</div>
                        </div>

                        <div class="ms-auto text-end">
                            <div class="text-muted small">Acción</div>

                            @php
                            $btnClass = 'btn-primary';
                            $btnText = 'Capturar indicador';

                            $estadoNorm = mb_strtoupper(trim((string)$estado));
                            $estadoNorm = str_replace('REVISIÓN','REVISION',$estadoNorm);

                            if ($estadoNorm === 'OBSERVADO') {
                            $btnClass = 'btn-warning';
                            $btnText = 'Corregir indicador';
                            } elseif ($estadoNorm === 'BORRADOR') {
                            $btnClass = 'btn-success';
                            $btnText = 'Continuar';
                            } elseif (in_array($estadoNorm, ['ENVIADO','EN REVISION','APROBADO','REENVIADO'])) {
                            $btnClass = 'btn-outline-secondary';
                            $btnText = 'Ver envío';
                            } elseif ($estadoNorm === 'SIN CAPTURA') {
                            $btnClass = 'btn-primary';
                            $btnText = 'Capturar indicador';
                            }
                            @endphp

                            @php
                            // Ruta base a captura
                            $hrefCaptura = route('usuario.formulario.captura', [
                            'id_form' => $form->id_form,
                            'id_ind' => $form->id_ind
                            ]);

                            $href = $hrefCaptura;
                            $icon = 'bi-pencil-square';

                            // Normalizar estado
                            $estadoNorm = mb_strtoupper(trim((string)$estado));
                            $estadoNorm = str_replace('REVISIÓN','REVISION',$estadoNorm);

                            // ✅ Si es BORRADOR: continuar con id_carga
                            if ($estadoNorm === 'BORRADOR' && $ultima) {
                            $href = $hrefCaptura . '?id_carga=' . $ultima->id_carga;
                            $icon = 'bi-play-circle';
                            }

                            // ✅ Si ya fue enviado/revisado/aprobado: ver detalle
                            if (in_array($estadoNorm, ['ENVIADO','EN REVISION','APROBADO','REENVIADO'])) {
                            $href = route('usuario.envio.ver', $form->id_form);
                            $icon = 'bi-eye';
                            }

                            // ✅ Si es observado: corregir con id_carga + modo
                            if ($estadoNorm === 'OBSERVADO' && $ultima) {
                            $href = $hrefCaptura . '?id_carga=' . $ultima->id_carga . '&modo=correccion';
                            $icon = 'bi-pencil-square';
                            }
                            @endphp


                            <a class="btn btn-sm {{ $btnClass }} mt-1 shadow-sm" href="{{ $href }}">
                                <i class="bi {{ $icon }} me-1"></i>
                                {{ $btnText }}
                            </a>
                        </div>
                    </div>
                    @else
                    <div class="text-muted">
                        Este registro no tiene indicador asignado.
                    </div>
                    @endif

                </div> {{-- /card-body --}}
            </div> {{-- /card --}}
        </div> {{-- /col --}}
        @empty
        <div class="col-12">
            <div class="alert alert-warning mb-0">
                No hay indicadores disponibles para tu dependencia.
            </div>
        </div>
        @endforelse
    </div> {{-- /row --}}
</div> {{-- /py-3 --}}
@endsection