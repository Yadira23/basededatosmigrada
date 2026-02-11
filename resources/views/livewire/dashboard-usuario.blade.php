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
            <a href="{{ route('usuario.indicadores') }}" class="btn btn-primary btn-sm shadow-sm mr-2">
                <i class="fas fa-chart-line mr-1"></i> Indicadores
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
                                Indicadores disponibles
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
                        <a class="small text-primary" href="{{ route('usuario.indicadores') }}">
                            Ver indicadores <i class="fas fa-arrow-right ml-1"></i>
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

    {{-- NOTIFICACIONES (Recordatorios / avisos del admin) --}}
    <div class="row">
        <div class="col-12 mb-4">
            @livewire('usuarios.notificaciones-usuario')
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
                    <a href="{{ route('usuario.indicadores') }}" class="btn btn-primary btn-block mb-2">
                        <i class="fas fa-pen mr-2"></i> Capturar indicador
                    </a>
                    <a href="{{ route('usuario.anexos') }}" class="btn btn-outline-secondary btn-block">
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
                                        <div>{{ optional($c->created_at)->format('Y-m-d H:i') }}</div>
                                        <small class="text-muted">
                                            Última act.: {{ optional($c->updated_at)->format('Y-m-d H:i') }}
                                        </small>
                                    </td>
                                    <td>
                                        @php
                                        $st = mb_strtoupper(trim((string)($c->status_env ?? '')));
                                        $st = str_replace('REVISIÓN', 'REVISION', $st);

                                        // id_ind del formulario (lo necesitamos para continuar / corregir)
                                        $id_ind = \App\Models\Formulario::where('id_form', $c->id_form)->value('id_ind');
                                        @endphp

                                        {{-- 👁 VER (siempre disponible) --}}
                                        <a class="btn btn-sm btn-outline-primary"
                                            href="{{ route('usuario.envio.ver.carga', $c->id_carga) }}">
                                            <i class="fas fa-eye"></i> Ver
                                        </a>

                                        {{-- ▶️ CONTINUAR (solo BORRADOR) --}}
                                        @if($st === 'BORRADOR')
                                        <a class="btn btn-sm btn-success ml-1"
                                            href="{{ route('usuario.formulario.captura', [
                                                    'id_form' => $c->id_form,
                                                    'id_ind'  => $id_ind
                                                ]) }}?id_carga={{ $c->id_carga }}">
                                            <i class="fas fa-play mr-1"></i> Continuar
                                        </a>
                                        @endif

                                        {{-- ✏️ CORREGIR (solo OBSERVADO) --}}
                                        @if($st === 'OBSERVADO')
                                        <a class="btn btn-sm btn-warning ml-1"
                                            href="{{ route('usuario.formulario.captura', [
                                                    'id_form' => $c->id_form,
                                                    'id_ind'  => $id_ind
                                                ]) }}?id_carga={{ $c->id_carga }}&modo=correccion">
                                            <i class="fas fa-edit mr-1"></i> Corregir
                                        </a>
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