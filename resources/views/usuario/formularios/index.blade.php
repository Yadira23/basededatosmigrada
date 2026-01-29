@extends('layouts.app')

@section('titulo', 'Formularios')

@section('content')
    <h3>Formularios disponibles</h3>
    @livewire('usuario.formulario-captura')
@endsection