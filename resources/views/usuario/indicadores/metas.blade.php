@extends('layouts.usuario')

@section('titulo', 'Metas del indicador')

@section('content')
    <div class="py-3">
        <h4 class="mb-1">Indicador: {{ $indicador->nombre_ind }}</h4>
        <div class="text-muted mb-3">
            Periodo del indicador: {{ $indicador->periodo_ind }}
        </div>

        @if ($metas->isEmpty())
            <div class="alert alert-secondary">
                Este indicador aún no tiene metas registradas.
            </div>
        @else
            <div class="list-group">
                @foreach ($metas as $item)
                    @php
                        $meta = $item['meta'];
                        $estado = $item['estado'];
                    @endphp

                    <div class="list-group-item d-flex justify-content-between">
                        <div>
                            <strong>Meta {{ $meta->orden }} – {{ $meta->titulo }}</strong>
                            <div class="text-muted small">Periodo: {{ $meta->periodo }}</div>
                        </div>

                        <div>
                            @if ($estado === 'SIN_CAPTURA')
                                <a href="{{ $item['url_capturar'] }}" class="btn btn-sm btn-primary">
                                    Capturar
                                </a>
                            @else
                                <a href="{{ $item['url_ver'] }}" class="btn btn-sm btn-outline-primary">
                                    Ver
                                </a>
                            @endif
                        </div>
                    </div>
                @endforeach

            </div>
        @endif
    </div>
@endsection
