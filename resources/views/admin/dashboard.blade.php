@extends('layouts.app')

@section('titulo', 'Dashboard Admin')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/admin-dashboard.css') }}">
@endpush

@section('content')
    @livewire('admin-dashboard')
@endsection
