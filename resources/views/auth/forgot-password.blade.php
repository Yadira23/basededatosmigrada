@extends('layouts.app')

@section('content')
<div class="auth-wrap">
  <div class="auth-card">
    <div class="auth-header">
      <h1 class="auth-title">Recuperar contraseña</h1>
      <p class="auth-subtitle">Te enviaremos un enlace a tu correo.</p>
    </div>

    <form method="POST" action="{{ route('password.email') }}" class="auth-form">
      @csrf

      @if (session('status'))
        <div class="alert alert-success">{{ session('status') }}</div>
      @endif

      <div class="field">
        <label>Correo electrónico</label>
        <input type="email" name="email" class="input @error('email') is-invalid @enderror"
               value="{{ old('email') }}" required autofocus>
        @error('email') <div class="error">{{ $message }}</div> @enderror
      </div>

      <button class="btn-primary">Enviar enlace</button>

      <div class="auth-footer" style="margin-top:12px;">
        <a class="link" href="{{ route('login') }}">Volver a iniciar sesión</a>
      </div>
    </form>
  </div>
</div>
@endsection
