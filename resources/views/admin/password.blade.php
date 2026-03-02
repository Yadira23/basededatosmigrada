@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="row justify-content-center mt-5">
            <div class="col-12 col-md-8 col-lg-6">

                <div class="card shadow border-0 rounded-4 p-3">
                    <div class="card-body">

                        <div class="mb-3">
                            <a href="{{ route('admin.perfil') }}" class="btn btn-light btn-sm">
                                <i class="fas fa-arrow-left me-1"></i> Volver al perfil
                            </a>
                        </div>

                        <h5 class="mb-3 text-center">Cambiar contraseña</h5>

                        @if ($errors->any())
                            <div class="alert alert-danger small">
                                <b>Revisa lo siguiente:</b>
                                <ul class="mb-0">
                                    @foreach ($errors->all() as $e)
                                        <li>{{ $e }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <form method="POST" action="{{ route('admin.password.update') }}">
                            @csrf

                            <div class="mb-3">
                                <label class="form-label">Contraseña actual</label>
                                <input type="password" name="password_actual"
                                    class="form-control @error('password_actual') is-invalid @enderror" required>
                                @error('password_actual')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">
                                    Escribe tu contraseña actual para confirmar que eres tú.
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Nueva contraseña</label>
                                <input type="password" name="password"
                                    class="form-control @error('password') is-invalid @enderror" required>
                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror

                                <div class="form-text">
                                    Mínimo 6 caracteres. Recomendado: letras y números.
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Confirmar nueva contraseña</label>
                                <input type="password" name="password_confirmation" class="form-control" required>
                            </div>

                            <div class="d-grid">
                                <button class="btn btn-primary">
                                    Guardar cambios
                                </button>
                            </div>
                        </form>

                    </div>
                </div>

            </div>
        </div>
    </div>
@endsection
