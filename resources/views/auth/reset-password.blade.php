@extends('layouts.app')

@section('content')
<div class="auth-wrap">
  <div class="auth-card">
    <div class="auth-header">
      <h1 class="auth-title">Nueva contraseña</h1>
      <p class="auth-subtitle">Escribe tu nueva contraseña.</p>
    </div>

    <form method="POST" action="{{ route('password.update') }}" class="auth-form">
      @csrf

      <input type="hidden" name="token" value="{{ $token }}">

      <div class="field">
        <label>Correo electrónico</label>
        <input type="email" name="email" class="input @error('email') is-invalid @enderror"
               value="{{ old('email', $email) }}" required autofocus>
        @error('email') <div class="error">{{ $message }}</div> @enderror
      </div>

      <div class="field">
        <label>Nueva contraseña</label>
        <input type="password" name="password" class="input @error('password') is-invalid @enderror" required>
        @error('password') <div class="error">{{ $message }}</div> @enderror
      </div>

      <div class="field">
        <label>Confirmar contraseña</label>
        <input type="password" name="password_confirmation" class="input" required>
      </div>

      <button class="btn-primary">Actualizar contraseña</button>
    </form>
  </div>
</div>
@endsection
