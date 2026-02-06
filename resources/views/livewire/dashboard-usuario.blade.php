<div>

    {{-- HEADER --}}
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">Panel de Usuario</h1>
            <div class="text-muted small">
                Bienvenido,
                <strong>{{ auth()->user()->nombre_usr ?? auth()->user()->email }}</strong>
                @if(!empty($dependenciaNombre))
                · Dependencia: <strong>{{ $dependenciaNombre }}</strong>
                @endif
            </div>
        </div>

        <div class="d-none d-sm-flex">
            <a href="{{ url('/formularios') }}" class="btn btn-primary btn-sm shadow-sm mr-2">
                <i class="fas fa-file-alt mr-1"></i> Formularios
            </a>
        </div>
    </div>

    {{-- KPIs --}}
    <div class="row">

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Formularios disponibles
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $formulariosDisponibles ?? 0 }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-file-alt fa-2x text-gray-300"></i>
                        </div>
                    </div>
                    <div class="mt-2">
                        <a class="small text-primary" href="{{ url('/formularios') }}">
                            Ir a formularios <i class="fas fa-arrow-right ml-1"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        {{-- Cargas realizadas (SIN link) --}}
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Cargas realizadas
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $cargasRealizadas ?? 0 }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                    <div class="mt-2 small text-muted">
                        Historial disponible para administración
                    </div>
                </div>
            </div>
        </div>

        {{-- Pendientes --}}
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Pendientes
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $pendientes ?? 0 }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-exclamation-triangle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                    <div class="mt-2 small text-muted">
                        Por completar o por enviar.
                    </div>
                </div>
            </div>
        </div>

        {{-- Observaciones --}}
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-danger shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">
                                Observaciones
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $observaciones ?? 0 }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-comment-dots fa-2x text-gray-300"></i>
                        </div>
                    </div>
                    <div class="mt-2 small text-muted">
                        Cargas devueltas para corrección.
                    </div>
                </div>
            </div>
        </div>

    </div>

    {{-- Acciones + Actividad --}}
    <div class="row">

        <div class="col-lg-4 mb-4">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Acciones rápidas</h6>
                </div>
                <div class="card-body">
                    <a href="{{ url('/formularios') }}" class="btn btn-primary btn-block mb-2">
                        <i class="fas fa-pen mr-2"></i> Capturar información
                    </a>
                    <a href="{{ url('/formularios') }}" class="btn btn-outline-primary btn-block mb-2">
                        <i class="fas fa-file-upload mr-2"></i> Subir archivo
                    </a>
                    <a href="{{ url('/anexos') }}" class="btn btn-outline-secondary btn-block">
                        <i class="fas fa-paperclip mr-2"></i> Anexos / Plantillas
                    </a>
                    <hr>
                    <div class="small text-muted">
                        Tip: descarga plantilla antes de subir archivo si el indicador lo requiere.
                    </div>
                </div>
            </div>
        </div>

        {{-- Actividad reciente (SIN "Ver todo") --}}
        <div class="col-lg-8 mb-4">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        Actividad reciente
                    </h6>
                </div>
                <div class="card-body">
                    @if(!empty($ultimasCargas) && count($ultimasCargas))
                    <div class="table-responsive">
                        <table class="table table-sm table-hover">
                            <thead class="thead-light">
                                <tr>
                                    <th>Folio</th>
                                    <th>Periodo</th>
                                    <th>Estatus</th>
                                    <th>Observación</th>
                                    <th>Fecha</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($ultimasCargas as $c)
                                <tr>
                                    <td class="font-weight-bold">
                                        {{ $c->folioUnico_carga ?? $c->id_carga }}
                                    </td>
                                    <td>
                                        {{ $c->periodo ?? '—' }} / {{ $c->ejercicio ?? '—' }}
                                    </td>
                                    <td>
                                        <span class="badge badge-{{ $this->badgeClass($c->status_env ?? '') }}">
                                            {{ $c->status_env ?? '—' }}
                                        </span>
                                    </td>
                                    <td>
                                        @if(!empty($c->observacion_env))
                                        <span class="text-warning">{{ $c->observacion_env }}</span>
                                        @else
                                        <span class="text-muted">—</span>
                                        @endif
                                    </td>
                                    <td class="text-muted">
                                        {{ optional($c->created_at)->format('Y-m-d H:i') }}
                                    </td>
                                    <td>
                                        @php
                                        $st = mb_strtoupper(trim((string)($c->status_env ?? '')));
                                        $st = str_replace('REVISIÓN', 'REVISION', $st);
                                        @endphp

                                        @if($st === 'OBSERVADO')
                                        <a href="{{ route('usuario.formulario.captura', [$c->id_form, $c->id_ind]) }}?carga={{ $c->id_carga }}"
                                            class="btn btn-sm btn-warning">
                                            Corregir
                                        </a>
                                        @elseif($st === 'EN REVISION')
                                        <button class="btn btn-sm btn-secondary" disabled>En revisión</button>
                                        @elseif($st === 'APROBADO')
                                        <button class="btn btn-sm btn-success" disabled>Aprobado</button>
                                        @else
                                        <span class="text-muted">—</span>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @else
                    <div class="text-center text-muted py-4">
                        <i class="fas fa-inbox fa-2x mb-2 text-gray-300"></i>
                        <div>Aún no tienes actividad registrada.</div>
                    </div>
                    @endif
                </div>
            </div>
        </div>

    </div>

</div>