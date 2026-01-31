@extends('layouts.app')

@section('titulo', 'Formularios')

@section('content')
<div class="container py-3">
    <h3>Formularios disponibles</h3>

    @php
        // ✅ SOLO los publicados y SOLO de la dependencia del usuario
        $formularios = \App\Models\Formulario::publicados()
            ->porDependencia(auth()->user()->id_depen)
            ->with('indicador') // para no estar consultando en la vista
            ->orderBy('id_form')
            ->get();
    @endphp

    @forelse($formularios as $form)
        <div class="card mb-3">
            <div class="card-header">
                <strong>Formulario:</strong>
                {{ $form->titulo_form ?? ('Formulario #' . $form->id_form) }}
            </div>

            <div class="card-body">
                <p class="text-muted mb-2">Indicador asignado:</p>

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
                        @if($form->indicador)
                            <tr>
                                <td>{{ $form->indicador->nombre_ind }}</td>
                                <td>{{ $form->periodo_form }}</td>
                                <td>{{ $form->indicador->unidadmedida_ind }}</td>
                                <td class="text-end">
                                    <a class="btn btn-primary btn-sm"
                                       href="{{ route('usuario.formulario.captura', [
                                            'id_form' => $form->id_form,
                                            'id_ind'  => $form->id_ind
                                       ]) }}">
                                        Capturar
                                    </a>
                                </td>
                            </tr>
                        @else
                            <tr>
                                <td colspan="4" class="text-muted">
                                    Este formulario no tiene indicador asignado.
                                </td>
                            </tr>
                        @endif
                    </tbody>
                </table>

            </div>
        </div>
    @empty
        <div class="alert alert-warning">
            No hay formularios publicados para tu dependencia.
        </div>
    @endforelse
</div>
@endsection
