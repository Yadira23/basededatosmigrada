@extends('layouts.app')

@section('titulo','Documentación')

@section('content')

<div class="container py-4">

<h4 class="mb-3">Documentación del Administrador</h4>

<p class="text-muted">
En esta sección se encuentra el manual de uso del sistema para administradores.
Aquí se describen las funciones de gestión de usuarios, dependencias e indicadores.
</p>

<div class="card shadow-sm">
    <div class="card-body">

        <a href="{{ asset('documentos/manual_usuario_admin.pdf') }}"
           target="_blank"
           class="btn btn-primary">

           Ver Manual de Administrador
        </a>

        <a href="{{ asset('documentos/manual_usuario_admin.pdf') }}"
           download
           class="btn btn-outline-secondary">

           Descargar Manual
        </a>

    </div>
</div>

</div>

@endsection