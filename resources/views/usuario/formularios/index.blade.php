@extends('layouts.app')

@section('titulo', 'Formularios')

@section('content')
<div class="container py-3">
    <h3>Formularios disponibles</h3>

    @php
    // Ajusta estos modelos si tus nombres son distintos
    $formularios = \App\Models\Formulario::orderBy('id_form')->get();
    $indicadores = \App\Models\Indicador::where('status_ind', 1)
    ->orderBy('nombre_ind')
    ->get();
    @endphp

    @forelse($formularios as $form)
    <div class="card mb-3">
        <div class="card-header">
            <strong>Formulario:</strong>
            {{ $form->nombre_form ?? ('Formulario #' . $form->id_form) }}
        </div>

        <div class="card-body">
            <p class="text-muted mb-2">Indicadores disponibles:</p>

            <table class="table table-sm">
                <thead>
                    <tr>
                        <th>Indicador</th>
                        <th>Periodo</th>
                        <th>Unidad</th>
                        <th class="text-end">Acción</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($indicadores as $ind)
                    <tr>
                        <td>{{ $ind->nombre_ind }}</td>
                        <td>{{ $ind->periodo_ind }}</td>
                        <td>{{ $ind->unidadmedida_ind }}</td>
                        <td class="text-end">
                            <a class="btn btn-primary btn-sm"
                                href="{{ route('usuario.formulario.captura', ['id_form' => $form->id_form, 'id_ind' => $ind->id_ind]) }}">
                                Capturar
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>

        </div>
    </div>
    @empty
    <div class="alert alert-warning">No hay formularios registrados.</div>
    @endforelse
</div>
@endsection