@extends('layouts.app')

@section('content')
<div class="auth-wrap">
    <div class="auth-card">

        {{-- LOGOS --}}
        <div class="auth-header">
            <div class="auth-logos">
                {{-- IZQUIERDA --}}
                <img src="{{ asset('sbadmin2/img/sedeco.png') }}" alt="SEDECO">
                <img src="{{ asset('sbadmin2/img/seie.png') }}" alt="SEIE">

                {{-- DERECHA --}}
                <img src="{{ asset('sbadmin2/img/ito.png') }}" alt="ITO">
            </div>

            <h1 class="auth-title">Iniciar sesión</h1>
            <p class="auth-subtitle">Sistema de Captura de Indicadores</p>
        </div>

        {{-- FORM --}}
        <form method="POST" action="{{ route('login') }}" class="auth-form">
            @csrf

            <div class="field">
                <label>Correo electrónico</label>
                <input type="email" name="email"
                    class="input @error('email') is-invalid @enderror"
                    value="{{ old('email') }}"
                    placeholder="usuario@correo.com"
                    required autofocus>
                @error('email') <div class="error">{{ $message }}</div> @enderror
            </div>

            <div class="field">
                <label>Contraseña</label>
                <div class="password-wrap">
                    <input type="password" id="password" name="password"
                        class="input @error('password') is-invalid @enderror"
                        placeholder="••••••••"
                        required>
                    <button type="button" class="toggle-pass" onclick="togglePassword()">👁</button>
                </div>
                @error('password') <div class="error">{{ $message }}</div> @enderror
            </div>

            <div class="row">
                <label class="check">
                    <input type="checkbox" name="remember">
                    <span>Recordarme</span>
                </label>

                @if (Route::has('password.request'))
                    <a class="link" href="{{ route('password.request') }}">¿Olvidaste tu contraseña?</a>
                @endif
            </div>

            <button class="btn-primary">Entrar</button>

            <div class="auth-footer">
                <small>© {{ date('Y') }} Gobierno del Estado</small>
            </div>
        </form>
    </div>
</div>

<style>
/* CONTENEDOR */
.auth-wrap{
    min-height: calc(100vh - 80px);
    display:flex;
    justify-content:center;
    align-items:center;
}

/* CARD */
.auth-card{
    width:100%;
    max-width:460px;
    background:#fff;
    border-radius:14px;
    border:1px solid #e5e7eb;
    box-shadow:0 15px 35px rgba(0,0,0,.08);
}

/* HEADER */
.auth-header{
    text-align:center;
    padding:24px 24px 12px;
}
.auth-logos{
    display:flex;
    justify-content:space-between;
    align-items:center;
    gap:14px;
    margin-bottom:12px;
}
.auth-logos img{
    height:40px;
    object-fit:contain;
}
.auth-title{
    margin:0;
    font-size:22px;
    font-weight:700;
}
.auth-subtitle{
    margin-top:4px;
    font-size:13px;
    color:#6b7280;
}

/* FORM */
.auth-form{
    padding:14px 24px 24px;
}
.field{ margin-top:14px; }
label{
    font-size:12px;
    font-weight:600;
    margin-bottom:6px;
    display:block;
}
.input{
    width:100%;
    padding:11px;
    border-radius:10px;
    border:1px solid #d1d5db;
}
.input:focus{
    border-color:#2563eb;
    outline:none;
    box-shadow:0 0 0 3px rgba(37,99,235,.15);
}
.password-wrap{ position:relative; }
.toggle-pass{
    position:absolute;
    right:8px;
    top:50%;
    transform:translateY(-50%);
    border:none;
    background:none;
    cursor:pointer;
}

/* EXTRAS */
.row{
    display:flex;
    justify-content:space-between;
    align-items:center;
    margin-top:14px;
    font-size:13px;
}
.check{
    display:flex;
    align-items:center;
    gap:6px;
}
.link{
    color:#2563eb;
    text-decoration:none;
}
.link:hover{ text-decoration:underline; }

.btn-primary{
    width:100%;
    margin-top:18px;
    padding:11px;
    border:none;
    border-radius:10px;
    background:#2563eb;
    color:#fff;
    font-weight:600;
    cursor:pointer;
}
.btn-primary:hover{
    background:#1d4ed8;
}

/* ERRORES */
.error{
    margin-top:6px;
    font-size:12px;
    color:#b91c1c;
}
.is-invalid{
    border-color:#b91c1c;
}

/* FOOTER */
.auth-footer{
    margin-top:16px;
    text-align:center;
    color:#9ca3af;
    font-size:12px;
}
</style>

<script>
function togglePassword(){
    const p = document.getElementById('password');
    p.type = p.type === 'password' ? 'text' : 'password';
}
</script>
@endsection
