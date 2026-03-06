<div wire:poll.60s>

    {{-- HEADER --}}
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">Panel de Usuario</h1>
            <div class="text-muted small">
                Bienvenido,
                <strong>{{ auth()->user()->nombre_usr ?? auth()->user()->email }}</strong>
                @if (!empty($dependenciaNombre))
                    · Dependencia: <strong>{{ $dependenciaNombre }}</strong>
                @endif
            </div>
        </div>

        <div class="d-none d-sm-flex">
            <a href="{{ route('usuario.indicadores') }}" class="btn btn-seie-accent btn-sm shadow-sm mr-2">
                <i class="fas fa-chart-line mr-1"></i> Indicadores
            </a>
        </div>
    </div>

    {{-- KPIs --}}
    <div class="row">

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-0 seie-kpi-user shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Lista de Indicadores 
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

    {{-- ✅ AVANCE DE INDICADORES (Meta vs completados) --}}
    <div class="row">
        <div class="col-12 mb-4">
            <div class="card shadow">
                <div class="card-body">

                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <div>
                            <div class="font-weight-bold text-gray-800">Mi avance de indicadores</div>
                            <div class="small text-muted">
                                {{ $indicadoresCompletados ?? 0 }} / {{ $metaIndicadores ?? 0 }} completados
                            </div>
                        </div>

                        <div class="text-right">
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $porcentajeAvance ?? 0 }}%
                            </div>
                            <div class="small text-muted">
                                @php
                                    $pct = (int) ($porcentajeAvance ?? 0);
                                    $label = $pct >= 100 ? 'Meta cumplida' : ($pct >= 1 ? 'En progreso' : 'Sin avance');
                                @endphp
                                {{ $label }}
                            </div>
                        </div>
                    </div>

                    @php
                        $pct = (int) ($porcentajeAvance ?? 0);
                        $barClass = $pct >= 80 ? 'bg-success' : ($pct >= 40 ? 'bg-warning' : 'bg-danger');
                    @endphp

                    <div class="progress mt-2" style="height: 10px; border-radius: 999px;">
                        <div class="progress-bar {{ $barClass }}" role="progressbar"
                            style="width: {{ $pct }}%;" aria-valuenow="{{ $pct }}" aria-valuemin="0"
                            aria-valuemax="100">
                        </div>
                    </div>

                    <div class="d-flex justify-content-between small text-muted mt-2">
                        <span>0%</span>
                        <span>40%</span>
                        <span>80%</span>
                        <span>100%</span>
                    </div>

                </div>
            </div>
        </div>
    </div>

    {{-- ✅ INDICADORES CON METAS (Kardex por periodos) --}}
    <div class="row">
        <div class="col-12 mb-4">
            <div class="card shadow">
                <div class="card-body">

                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <div>
                            <div class="font-weight-bold text-gray-800">Indicadores con metas</div>
                            <div class="small text-muted">
                                Cumplimiento por periodo (kardex)
                            </div>
                        </div>
                    </div>

                    {{-- ✅ LISTA: un bloque por indicador --}}
                    @if (!empty($kardexMetasPorIndicador) && count($kardexMetasPorIndicador))
                        <div style="display:flex; flex-direction:column; gap:14px;">

                            @foreach ($kardexMetasPorIndicador as $bloque)
                                @php
                                    $pm = (int) ($bloque['pct'] ?? 0);
                                    $barClassMeta = $pm >= 80 ? 'bg-success' : ($pm >= 40 ? 'bg-warning' : 'bg-danger');
                                    $periodicidadTxt = $bloque['periodicidad'] ?? '—';
                                    $nombreInd = $bloque['nombre'] ?? 'Indicador';
                                    $metaTotal = (int) ($bloque['meta_total'] ?? 0);
                                    $hechasTotal = (int) ($bloque['hechas_total'] ?? 0);
                                    $kardex = $bloque['kardex'] ?? [];
                                @endphp

                                <div class="card border-left-primary shadow-sm">
                                    <div class="card-body">

                                        <div class="d-flex justify-content-between align-items-start">
                                            <div>
                                                <div class="font-weight-bold text-gray-800">
                                                    {{ $nombreInd }}
                                                </div>
                                                <div class="small text-muted">
                                                    Periodicidad: <strong>{{ $periodicidadTxt }}</strong>
                                                </div>
                                            </div>

                                            <div class="text-right">
                                                <div class="h5 mb-0 font-weight-bold text-gray-800">
                                                    {{ $pm }}%
                                                </div>
                                                <div class="small text-muted">Avance de la meta</div>
                                            </div>
                                        </div>

                                        <div class="progress mt-2" style="height: 10px; border-radius: 999px;">
                                            <div class="progress-bar {{ $barClassMeta }}" role="progressbar"
                                                style="width: {{ $pm }}%;"
                                                aria-valuenow="{{ $pm }}" aria-valuemin="0"
                                                aria-valuemax="100">
                                            </div>
                                        </div>

                                        <div class="d-flex justify-content-between small text-muted mt-2">
                                            <span>Meta total: <strong>{{ $metaTotal }}</strong></span>
                                            <span>Hechas: <strong>{{ $hechasTotal }}</strong></span>
                                        </div>

                                        {{-- Kardex por periodo (del indicador) --}}
                                        <div class="mt-3">
                                            <div class="small text-muted mb-2">Kardex por periodo</div>

                                            @if (!empty($kardex) && count($kardex))
                                                <div class="d-flex flex-wrap" style="gap:10px;">
                                                    @foreach ($kardex as $k)
                                                        @php
                                                            $badge = $k['estado'] ?? 'secondary';
                                                            $borderClass =
                                                                $badge === 'success'
                                                                    ? 'border-left-success'
                                                                    : ($badge === 'warning'
                                                                        ? 'border-left-warning'
                                                                        : ($badge === 'danger'
                                                                            ? 'border-left-danger'
                                                                            : 'border-left-secondary'));
                                                        @endphp

                                                        <div class="card shadow-sm {{ $borderClass }}"
                                                            style="min-width: 110px;">
                                                            <div class="card-body p-2 text-center">
                                                                <div class="font-weight-bold">{{ $k['label'] ?? '—' }}
                                                                </div>
                                                                <div class="text-muted small">
                                                                    {{ $k['hechas'] ?? 0 }} / {{ $k['meta'] ?? 0 }}
                                                                </div>
                                                                <span class="badge badge-{{ $badge }} mt-1">
                                                                    {{ $badge === 'success' ? 'Cumplido' : ($badge === 'warning' ? 'En progreso' : 'Atrasado') }}
                                                                </span>
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            @else
                                                <div class="alert alert-light border mb-0">
                                                    <i class="fas fa-info-circle mr-1"></i>
                                                    Este indicador aún no tiene metas por periodo.
                                                </div>
                                            @endif
                                        </div>

                                    </div>
                                </div>
                            @endforeach

                        </div>
                    @else
                        <div class="alert alert-light border mb-0">
                            <i class="fas fa-info-circle mr-1"></i>
                            Aún no hay indicadores con metas por periodo configuradas para tu dependencia.
                        </div>
                    @endif

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

      {{--  <div class="col-lg-4 mb-4">
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
        </div> --}}

        {{-- Actividad reciente (SIN "Ver todo") --}}
        <div class="col-lg-12 mb-4">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        Actividad reciente
                    </h6>
                </div>
                <div class="card-body">
                    @if (!empty($ultimasCargas) && count($ultimasCargas))
                        <div class="table-responsive">
                            <table class="table table-sm table-hover">
                                <thead class="thead-light">
                                    <tr>
                                        <th>Folio</th>
                                        <th>Indicador</th>
                                        <th>Periodo</th>
                                        <th>Estatus</th>
                                        <th>Observación</th>
                                        <th>Fecha</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($ultimasCargas as $c)
                                        <tr>
                                            <td class="font-weight-bold">
                                                {{ $c->folioUnico_carga ?? $c->id_carga }}
                                            </td>

                                            <td>
                                                {{ optional($c->formulario->indicador)->nombre_ind ?? '—' }}
                                            </td>

                                            <td>
                                                {{ $c->periodo ?? '—' }} / {{ $c->ejercicio ?? '—' }}
                                            </td>

                                            <td>
                                                <span
                                                    class="badge badge-{{ $this->badgeClass($c->status_env ?? '') }}">
                                                    {{ $c->status_env ?? '—' }}
                                                </span>
                                            </td>

                                            <td>
                                                @if (!empty($c->observacion_env))
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
                                                    $st = mb_strtoupper(trim((string) ($c->status_env ?? '')));
                                                    $st = str_replace('REVISIÓN', 'REVISION', $st);

                                                    // id_ind del formulario (lo necesitamos para continuar / corregir)
                                                    $id_ind = optional($c->formulario)->id_ind;
                                                @endphp

                                                {{-- 👁 VER (siempre disponible) --}}
                                                <a class="btn btn-sm btn-outline-primary"
                                                    href="{{ route('usuario.envio.ver.carga', $c->id_carga) }}">
                                                    <i class="fas fa-eye"></i> Ver
                                                </a>

                                                {{-- ▶️ CONTINUAR (solo BORRADOR) --}}
                                                @if ($st === 'BORRADOR')
                                                    <a class="btn btn-sm btn-success ml-1"
                                                        href="{{ route('usuario.formulario.captura', [
                                                            'id_form' => $c->id_form,
                                                            'id_ind' => $id_ind,
                                                        ]) }}?id_carga={{ $c->id_carga }}">
                                                        <i class="fas fa-play mr-1"></i> Continuar
                                                    </a>
                                                @endif

                                                {{-- ✏️ CORREGIR (solo OBSERVADO) --}}
                                                @if ($st === 'OBSERVADO')
                                                    <a class="btn btn-sm btn-warning ml-1"
                                                        href="{{ route('usuario.formulario.captura', [
                                                            'id_form' => $c->id_form,
                                                            'id_ind' => $id_ind,
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
