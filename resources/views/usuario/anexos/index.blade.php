@extends('layouts.usuario')

@section('content')
<div class="container">
    <h1 class="h3 mb-2">Anexos</h1>
    <div class="text-muted mb-3">Plantillas y archivos relacionados a tus envíos.</div>

    {{-- PLANTILLAS --}}
    <div class="card mb-3">
        <div class="card-header d-flex justify-content-between align-items-center">
            <strong>Plantillas</strong>
            <small class="text-muted">Descarga la plantilla del indicador</small>
        </div>
        <div class="card-body p-0">
            @if(($plantillas ?? collect())->count() === 0)
            <div class="p-3 text-muted">No hay plantillas disponibles para tu dependencia.</div>
            @else
            <div class="table-responsive">
                <table class="table mb-0 table-hover">
                    <thead class="thead-light">
                        <tr>
                            <th>Indicador</th>
                            <th>Periodo</th>
                            <th>Unidad</th>
                            <th class="text-right">Acción</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($plantillas as $p)
                        <tr>
                            <td>{{ $p->nombre_ind ?? $p->titulo_form }}</td>
                            <td>{{ $p->periodo_ind ?? '—' }}</td>
                            <td>{{ $p->unidadmedida_ind ?? '—' }}</td>
                            <td class="text-right">
                                <a class="btn btn-sm btn-outline-primary"
                                    href="{{ route('usuario.formulario.captura', [$p->id_form, $p->id_ind]) }}">
                                    Abrir
                                    </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @endif
        </div>
    </div>

    {{-- MIS ARCHIVOS / ENVÍOS --}}
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <strong>Mis archivos subidos</strong>
            <small class="text-muted">Historial de envíos por folio</small>
        </div>
        <div class="card-body p-0">
            @if(($envios ?? collect())->count() === 0)
            <div class="p-3 text-muted">
                Aún no tienes envíos registrados. Cuando envíes o subas un CSV/XLSX, aparecerá aquí.
            </div>
            @else
            <div class="table-responsive">
                <table class="table mb-0 table-hover">
                    <thead class="thead-light">
                        <tr>
                            <th>Folio</th>
                            <th>Origen</th>
                            <th>Archivo</th>
                            <th>Estatus</th>
                            <th>Fecha</th>
                            <th class="text-right">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($envios as $e)
                        <tr>
                            <td>{{ $e->folioUnico_carga ?? ('#'.$e->id_carga) }}</td>
                            <td>{{ $e->origen ?? 'manual' }}</td>
                            <td>
                                @if(!empty($e->archivo_nombre) && $e->archivo_nombre !== 'null')
                                {{ $e->archivo_nombre }}
                                @if(!empty($e->archivo_tipo) && $e->archivo_tipo !== 'null')
                                <span class="text-muted">({{ $e->archivo_tipo }})</span>
                                @endif
                                @else
                                <span class="text-muted">—</span>
                                @endif
                            </td>
                            <td>{{ $e->status_env ?? '—' }}</td>
                            <td>{{ $e->created_at }}</td>
                            <td class="text-right">
                                <a class="btn btn-sm btn-outline-secondary"
                                    href="{{ route('usuario.envio.ver.carga', $e->id_carga) }}">
                                    Ver
                                </a>

                                @if(!empty($e->archivo_nombre))
                                <a class="btn btn-sm btn-outline-primary"
                                    href="{{ route('usuario.envio.descargar.archivo', $e->id_carga) }}">
                                    Archivo
                                </a>
                                @endif

                                @if(false)
                                <a class="btn btn-sm btn-outline-dark"
                                    href="{{ route('usuario.envio.descargar.log', $e->id_carga) }}">
                                    Log
                                </a>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection