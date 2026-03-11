@extends('layouts.usuario')

@section('titulo', 'Documentación')

@section('content')
<div class="container py-4">

    <h4 class="mb-3">Manual de Usuario</h4>

    <p class="text-muted">
        En esta sección puedes consultar el manual de usuario del sistema.
        Este documento explica cómo utilizar la plataforma para registrar
        información y consultar los indicadores asignados a tu dependencia.
    </p>

    <div class="card shadow-sm">
        <div class="card-body">

            <a href="{{ asset('documentos/manual_usuario_dependencia.pdf') }}"
               target="_blank"
               class="btn btn-primary">

               Ver Manual de Usuario
            </a>

            <a href="{{ asset('documentos/manual_usuario_dependencia.pdf') }}"
               download
               class="btn btn-outline-secondary">

               Descargar Manual
            </a>

        </div>
    </div>

</div>
@endsection