@extends('layouts.app') {{-- ajusta a tu layout real --}}

@section('content')
    <div class="container-fluid">

        <div class="d-flex align-items-center justify-content-between mb-3">
            <h1 class="h4 text-gray-800 mb-0">Resultados de búsqueda</h1>
            <span class="text-muted small">Búsqueda: <b>{{ $q ?: '—' }}</b></span>
        </div>

        @if ($q === '')
            <div class="alert alert-info">
                Escribe algo en el buscador para ver resultados.
            </div>
        @else
            @php
                $total = $dependencias->count() + $indicadores->count() + $formularios->count() + $usuarios->count();
            @endphp

            @if ($total === 0)
                <div class="alert alert-warning">
                    No se encontraron resultados para <b>{{ $q }}</b>.
                </div>
            @endif

            {{-- DEPENDENCIAS --}}
            <div class="card shadow-sm mb-3">
                <div class="card-header py-2"><b>Dependencias</b> <span
                        class="text-muted">({{ $dependencias->count() }})</span></div>
                <div class="card-body">
                    @forelse($dependencias as $d)
                        <div class="d-flex justify-content-between border-bottom py-2">
                            <div>
                                <div class="font-weight-bold">{{ $d->nombre_depen }}</div>
                                <div class="text-muted small">ID: {{ $d->id_depen ?? ($d->id ?? '—') }}</div>
                            </div>
                            <a href="{{ route('admin.dependencias.show', $d->id_depen ?? $d->id) }}"
                                class="btn btn-sm btn-outline-primary">
                                Ver
                            </a>
                        </div>
                    @empty
                        <div class="text-muted">Sin resultados.</div>
                    @endforelse
                </div>
            </div>

            {{-- INDICADORES --}}
            <div class="card shadow-sm mb-3">
                <div class="card-header py-2"><b>Indicadores</b> <span
                        class="text-muted">({{ $indicadores->count() }})</span></div>
                <div class="card-body">
                    @forelse($indicadores as $i)
                        <div class="d-flex justify-content-between border-bottom py-2">
                            <div>
                                <div class="font-weight-bold">{{ $i->nombre_ind }}</div>
                                <div class="text-muted small">ID: {{ $i->id_ind ?? ($i->id ?? '—') }}</div>
                            </div>
                            <a href="{{ route('admin.indicadores.show', $i->id_ind ?? $i->id) }}"
                                class="btn btn-sm btn-outline-primary">
                                Ver
                            </a>
                        </div>
                    @empty
                        <div class="text-muted">Sin resultados.</div>
                    @endforelse
                </div>
            </div>

            {{-- FORMULARIOS --}}
            <div class="card shadow-sm mb-3">
                <div class="card-header py-2"><b>Formularios</b> <span
                        class="text-muted">({{ $formularios->count() }})</span></div>
                <div class="card-body">
                    @forelse($formularios as $f)
                        <div class="d-flex justify-content-between border-bottom py-2">
                            <div>
                                <div class="font-weight-bold">
                                    {{ $f->nombre_form ?? 'Formulario #' . ($f->id_form ?? $f->id) }}</div>
                                <div class="text-muted small">
                                    Periodo: {{ $f->periodo_form ?? '—' }} · Estado: {{ $f->boton_accion_form ?? '—' }}
                                </div>
                            </div>
                            <a href="{{ route('formularios.index') }}" class="btn btn-sm btn-outline-primary">
                                Ir
                            </a>
                        </div>
                    @empty
                        <div class="text-muted">Sin resultados.</div>
                    @endforelse
                </div>
            </div>

            {{-- USUARIOS --}}
            <div class="card shadow-sm mb-3">
                <div class="card-header py-2"><b>Usuarios</b> <span class="text-muted">({{ $usuarios->count() }})</span>
                </div>
                <div class="card-body">
                    @forelse($usuarios as $u)
                        <div class="d-flex justify-content-between border-bottom py-2">
                            <div>
                                <div class="font-weight-bold">{{ $u->usuario_usr }}</div>
                                <div class="text-muted small">
                                    {{ trim(($u->nombre_usr ?? '') . ' ' . ($u->apellido_paterno ?? '') . ' ' . ($u->apellido_materno ?? '')) }}
                                </div>
                            </div>
                            <a href="{{ route('admin.usuarios.show', $u->id_usr ?? $u->id) }}"
                                class="btn btn-sm btn-outline-primary">
                                Ver
                            </a>
                        </div>
                    @empty
                        <div class="text-muted">Sin resultados.</div>
                    @endforelse
                </div>
            </div>
        @endif

    </div>
@endsection
