@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="row justify-content-center mt-5">
            <div class="col-12 col-md-8 col-lg-6">

                <div class="card shadow border-0 rounded-4 p-3">
                    <div class="card-body text-center">

                        @if (session('success'))
                            <div class="alert alert-success small text-start">
                                <i class="fas fa-check-circle me-1"></i>
                                {{ session('success') }}
                            </div>
                        @endif

                        {{-- AVATAR CON INICIALES --}}
                        @php
                            $nombre = auth()->user()->nombre_usr ?? '';
                            $apellido = auth()->user()->apellido_paterno ?? '';
                            $iniciales = strtoupper(substr($nombre, 0, 1) . substr($apellido, 0, 1));
                        @endphp

                        <div class="mb-3">
                            <div class="rounded-circle bg-primary text-white d-inline-flex align-items-center justify-content-center"
                                style="width: 100px; height: 100px; font-size: 34px; font-weight: 600;">
                                {{ $iniciales }}
                            </div>
                        </div>

                        {{-- TITULO --}}
                        <h5 class="card-title mb-0">
                            {{ auth()->user()->nombre_usr }}
                            {{ auth()->user()->apellido_paterno }}
                        </h5>

                        <p class="text-muted small mb-4">
                            Administrador del sistema
                        </p>

                        {{-- DATOS --}}
                        <div class="text-start small">

                            <div class="mb-2">
                                <strong>Usuario:</strong><br>
                                {{ auth()->user()->usuario_usr }}
                            </div>

                            <div class="mb-2">
                                <strong>Correo:</strong><br>
                                {{ auth()->user()->email ?? 'No registrado' }}
                            </div>

                            <div class="mb-2">
                                <strong>Rol:</strong><br>
                                Administrador
                            </div>

                            <div class="mb-2">
                                <strong>Acceso:</strong><br>
                                Control total del sistema
                            </div>

                            <div class="mb-2">
                                <strong>Última actualización:</strong><br>
                                <span class="badge bg-light text-dark border">
                                    <i class="fas fa-clock me-1"></i>
                                    {{ auth()->user()->updated_at?->format('d/m/Y H:i') ?? '—' }}
                                </span>
                            </div>

                        </div>

                        <hr>

                        {{-- ACCIONES --}}
                        <div class="d-grid mt-3">
                            <a href="{{ route('admin.password') }}" class="btn btn-primary">
                                Cambiar contraseña
                            </a>
                        </div>

                    </div>
                </div>

            </div>
        </div>
    </div>
@endsection
