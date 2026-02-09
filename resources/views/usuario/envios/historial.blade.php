@extends('layouts.usuario')

@section('titulo', 'Historial de envíos')

@section('content')
<div class="py-3">

    {{-- Encabezado --}}
    <div class="d-flex justify-content-between align-items-start mb-3">
        <div>
            <h4 class="mb-0">Historial de envíos</h4>
            <small class="text-muted">
                Indicador: {{ $form->indicador->nombre_ind ?? 'Sin indicador' }}
            </small>
        </div>

        <a href="{{ route('usuario.envio.ver', $form->id_form) }}"
            class="btn btn-sm btn-outline-secondary">
            <i class="fas fa-arrow-left mr-1"></i> Volver al último envío
        </a>
    </div>

    {{-- Tabla --}}
    <div class="card shadow-sm border-0">
        <div class="card-body p-0">

            @if($historial->isEmpty())
            <div class="alert alert-warning m-3">
                No hay envíos registrados para este indicador.
            </div>
            @else
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="thead-light">
                        <tr>
                            <th>Fecha</th>
                            <th>Método</th>
                            <th>Estado</th>
                            <th>Folio</th>
                            <th class="text-end">Acción</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($historial as $h)
                        @php
                        $badge = match ($h->status_env) {
                        'APROBADO' => 'success',
                        'EN REVISION' => 'info',
                        'OBSERVADO' => 'warning',
                        'REENVIADO' => 'primary',
                        'ENVIADO' => 'secondary',
                        default => 'dark',
                        };
                        @endphp
                        <tr>
                            <td>
                                {{ \Carbon\Carbon::parse($h->fecha_carga)->format('d/m/Y') }}
                            </td>
                            <td>{{ $h->metodo_captura }}</td>
                            <td>
                                <span class="badge badge-{{ $badge }}">
                                    {{ $h->status_env }}
                                </span>
                            </td>
                            <td>{{ $h->folioUnico_carga }}</td>
                            <td class="text-end">
                                <a href="{{ route('usuario.envio.ver.carga', $h->id_carga) }}"
                                    class="btn btn-sm btn-outline-primary">
                                    <i class="fas fa-eye"></i> Ver
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

</div>
@endsection