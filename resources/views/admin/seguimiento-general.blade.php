@extends('layouts.app')

@section('titulo', 'Seguimiento General')

@section('content')
    <div class="py-3">

        {{-- ENCABEZADO --}}
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <div>
                <h1 class="h3 mb-1 text-gray-800">Seguimiento General</h1>
                <p class="mb-0 text-muted">
                    Consulta general del avance de dependencias e indicadores.
                </p>
            </div>
        </div>

        {{-- TARJETAS RESUMEN --}}
        <div class="row">
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-primary shadow h-100 py-2">
                    <div class="card-body">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                            Dependencias
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalDependencias }}</div>
                        <div class="small text-muted mt-2">Total registradas en el sistema</div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-success shadow h-100 py-2">
                    <div class="card-body">
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                            Indicadores
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalIndicadores }}</div>
                        <div class="small text-muted mt-2">Asignados a dependencias</div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-warning shadow h-100 py-2">
                    <div class="card-body">
                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                            Pendientes
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalPendientes }}</div>
                        <div class="small text-muted mt-2">Indicadores aún no capturados</div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-info shadow h-100 py-2">
                    <div class="card-body">
                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                            Avance General
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $avanceGeneral }}%</div>
                        <div class="small text-muted mt-2">Promedio general de cumplimiento</div>
                    </div>
                </div>
            </div>
        </div>

        {{-- AVANCE GENERAL --}}
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold text-primary">Avance general del sistema</h6>
                <span class="badge badge-info px-3 py-2">{{ $avanceGeneral }}%</span>
            </div>
            <div class="card-body">
                <div class="mb-2 d-flex justify-content-between">
                    <span class="small text-muted">Progreso global</span>
                    <span class="small font-weight-bold">{{ $totalCapturados }} de {{ $totalIndicadores }}
                        indicadores</span>
                </div>
                <div class="progress" style="height: 18px; border-radius: 10px;">
                    <div class="progress-bar bg-info" role="progressbar" style="width: {{ $avanceGeneral }}%;"
                        aria-valuenow="{{ $avanceGeneral }}" aria-valuemin="0" aria-valuemax="100">
                        {{ $avanceGeneral }}%
                    </div>
                </div>
            </div>
        </div>

        {{-- TABLA GENERAL --}}
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Avance por dependencia</h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover align-middle mb-0">
                        <thead class="thead-light">
                            <tr>
                                <th>Dependencia</th>
                                <th class="text-center">Total a capturar</th>
                                <th class="text-center">Capturados</th>
                                <th class="text-center">Pendientes</th>
                                <th class="text-center">Observados</th>
                                <th class="text-center">Aprobados</th>
                                <th class="text-center">Avance</th>
                                <th class="text-center">Detalle</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($filas as $fila)
                                <tr>
                                    <td>{{ $fila['dependencia'] }}</td>
                                    <td class="text-center">{{ $fila['indicadores'] }}</td>
                                    <td class="text-center">{{ $fila['capturados'] }}</td>
                                    <td class="text-center">{{ $fila['pendientes'] }}</td>
                                    <td class="text-center">{{ $fila['observados'] }}</td>
                                    <td class="text-center">{{ $fila['aprobados'] }}</td>
                                    <td class="text-center">
                                        @php
                                            $badgeClass = 'badge-danger';
                                            if ($fila['avance'] >= 80) {
                                                $badgeClass = 'badge-success';
                                            } elseif ($fila['avance'] >= 60) {
                                                $badgeClass = 'badge-warning';
                                            } elseif ($fila['avance'] >= 40) {
                                                $badgeClass = 'badge-info';
                                            }
                                        @endphp

                                        <span class="badge {{ $badgeClass }} px-3 py-2">
                                            {{ $fila['avance'] }}%
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <a href="{{ route('admin.seguimiento.general', ['dep' => $fila['id_depen']]) }}"
                                            class="btn btn-sm btn-outline-primary">
                                            Ver detalle
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center text-muted py-4">
                                        No hay dependencias o formularios registrados para mostrar.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        @if ($dependenciaSeleccionada)
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="m-0 font-weight-bold text-primary">
                            Dependencia: {{ $dependenciaSeleccionada->nombre_depen }}
                        </h6>
                        <small class="text-muted">
                            Detalle general de indicadores y metas.
                        </small>
                    </div>

                    <a href="{{ route('admin.seguimiento.general') }}" class="btn btn-sm btn-outline-secondary">
                        Cerrar detalle
                    </a>
                </div>

                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover align-middle mb-0">
                            <thead class="thead-light">
                                <tr>
                                    <th>Indicador / Meta</th>
                                    <th class="text-center">Estado</th>
                                    <th class="text-center">Fecha</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($detalleDependencia as $item)
                                    <tr>
                                        <td>{{ $item['indicador'] }}</td>
                                        <td class="text-center">
                                            @php
                                                $estado = strtoupper($item['estado']);
                                                $clase = 'badge-secondary';

                                                if ($estado === 'APROBADO') {
                                                    $clase = 'badge-success';
                                                } elseif ($estado === 'OBSERVADO') {
                                                    $clase = 'badge-danger';
                                                } elseif ($estado === 'PENDIENTE') {
                                                    $clase = 'badge-warning';
                                                } else {
                                                    $clase = 'badge-info';
                                                }
                                            @endphp

                                            <span class="badge {{ $clase }} px-3 py-2">
                                                {{ $estado }}
                                            </span>
                                        </td>
                                        <td class="text-center">{{ $item['fecha'] }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="text-center text-muted py-4">
                                            No hay detalle disponible para esta dependencia.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-3 small text-muted">
                        <strong>Leyenda:</strong>
                        <span class="ml-2"><span class="badge badge-success">APROBADO</span></span>
                        <span class="ml-2"><span class="badge badge-info">ENVIADO / REENVIADO</span></span>
                        <span class="ml-2"><span class="badge badge-warning">PENDIENTE</span></span>
                        <span class="ml-2"><span class="badge badge-danger">OBSERVADO</span></span>
                    </div>
                </div>
            </div>
        @endif
    </div>
@endsection
