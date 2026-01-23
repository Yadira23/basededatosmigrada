@extends('layouts.app')

@section('content')
    @livewire('admin-dashboard')
@endsection

@section('titulo', 'Dashboard Admin')

@section('content')
    <div class="container">
        <h1>Dashboard Admin</h1>
        <p>Bienvenido, {{ auth()->user()->nombre_usr }}!</p>
    </div>
@endsection
