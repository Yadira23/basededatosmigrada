@extends('layouts.guest')

@section('content')
    <div class="auth-wrap">
        <div class="auth-card">

            {{-- LOGOS --}}
            {{-- HEADER --}}
            <div class="auth-header">

                <div class="auth-logos">
                    <div class="logos-left">
                        <img src="{{ asset('sbadmin2/img/sedeco.png') }}" alt="SEDECO">
                        <img src="{{ asset('sbadmin2/img/seie.png') }}" alt="SEIE">
                    </div>

                    <div class="logos-right">
                        <img src="{{ asset('sbadmin2/img/ito.png') }}" alt="ITO">
                    </div>
                </div>

                <div class="logo-principal">
                    <img src="{{ asset('sbadmin2/img/Integra.png') }}" alt="Sistema Integra">
                </div>

                <p class="auth-subtitle">
                    Sistema para la integración de Información Estadística y Económica
                    generada por las Dependencias del Gobierno del Estado de Oaxaca
                </p>

                <h3 class="bienvenidos">BIENVENIDOS</h3>
            </div>

            {{-- FORM --}}
            <form method="POST" action="{{ route('login.post') }}" class="auth-form">

                @csrf
                <h1 class="auth-title">Iniciar sesión</h1>
                <div class="field">
                    <label>Correo electrónico</label>
                    <input type="email" name="email" class="input @error('email') is-invalid @enderror"
                        value="{{ old('email') }}" placeholder="usuario@correo.com" required autofocus>
                    @error('email')
                        <div class="error">{{ $message }}</div>
                    @enderror
                </div>

                <div class="field">
                    <label>Contraseña</label>
                    <div class="password-wrap">
                        <input type="password" id="password" name="password"
                            class="input @error('password') is-invalid @enderror" placeholder="••••••••" required>
                        <button type="button" class="toggle-pass" onclick="togglePassword()">👁</button>
                    </div>
                    @error('password')
                        <div class="error">{{ $message }}</div>
                    @enderror
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
        /* FONDO GENERAL (premium) */
        body {
            background: #f3f4f6;
        }

        /* CONTENEDOR */
        .auth-wrap {
            min-height: calc(100vh - 80px);
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 22px;
        }

        /* CARD (más premium) */
        .auth-card {
            width: 100%;
            max-width: 500px;
            /* un poquito más amplia */
            background: #fff;
            border-radius: 18px;
            border: 1px solid #e5e7eb;
            box-shadow: 0 22px 55px rgba(17, 24, 39, .12);
            overflow: hidden;
        }

        /* HEADER */
        .auth-header {
            text-align: center;
            padding: 26px 28px 14px;
        }

        /* LOGOS SUPERIORES */
        .auth-logos {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 12px;
            padding-bottom: 10px;
            border-bottom: 1px solid #eef2f7;
            /* línea institucional */
        }

        .logos-left {
            display: flex;
            gap: 14px;
            align-items: center;
        }

        .auth-logos img {
            height: 38px;
            object-fit: contain;
        }

        /* LOGO PRINCIPAL (protagonista) */
        .logo-principal {
            margin: 10px 0 12px;
            display: flex;
            justify-content: center;
        }

        .logo-principal img {
            max-height: 78px;
            /* más presente */
            width: auto;
            object-fit: contain;
        }

        /* SUBTITULO (más “oficial”) */
        .auth-subtitle {
            margin: 12px auto 0;
            font-size: 13px;
            color: #4b5563;
            line-height: 1.6;
            max-width: 400px;
        }

        /* BIENVENIDOS (premium) */
        .bienvenidos {
            margin: 20px auto 0;
            font-size: 28px;
            font-weight: 700;
            letter-spacing: 7px;
            color: #111827;
            display: inline-block;
            position: relative;
            padding-bottom: 10px;
        }

        .bienvenidos::after {
            content: "";
            position: absolute;
            left: 50%;
            transform: translateX(-50%);
            bottom: 0;
            width: 120px;
            height: 2px;
            background: #cbd5e1;
            /* línea elegante */
            border-radius: 2px;
        }

        /* FORM */
        .auth-form {
            padding: 18px 28px 26px;
        }

        /* TITULO DEL FORM */
        .auth-title {
            margin: 10px 0 10px;
            font-size: 22px;
            font-weight: 800;
            color: #111827;
        }

        /* CAMPOS */
        .field {
            margin-top: 14px;
        }

        label {
            font-size: 12px;
            font-weight: 700;
            margin-bottom: 7px;
            display: block;
            color: #111827;
        }

        .input {
            width: 100%;
            padding: 12px 12px;
            border-radius: 12px;
            border: 1px solid #d1d5db;
            background: #fff;
            transition: .15s ease;
        }

        .input:focus {
            border-color: #2563eb;
            outline: none;
            box-shadow: 0 0 0 4px rgba(37, 99, 235, .14);
        }

        /* PASSWORD */
        .password-wrap {
            position: relative;
        }

        .toggle-pass {
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            border: none;
            background: none;
            cursor: pointer;
            opacity: .7;
        }

        .toggle-pass:hover {
            opacity: 1;
        }

        /* EXTRAS */
        .row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 14px;
            font-size: 13px;
            color: #374151;
        }

        .check {
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .link {
            color: #1d4ed8;
            text-decoration: none;
            font-weight: 600;
        }

        .link:hover {
            text-decoration: underline;
        }

        /* BOTON (premium) */
        .btn-primary {
            width: 100%;
            margin-top: 18px;
            padding: 12px;
            border: none;
            border-radius: 12px;
            background: linear-gradient(180deg, #2563eb, #1d4ed8);
            color: #fff;
            font-weight: 800;
            letter-spacing: .4px;
            cursor: pointer;
            box-shadow: 0 10px 20px rgba(29, 78, 216, .20);
            transition: transform .08s ease, box-shadow .15s ease;
        }

        .btn-primary:hover {
            box-shadow: 0 14px 26px rgba(29, 78, 216, .28);
        }

        .btn-primary:active {
            transform: translateY(1px);
        }

        /* ERRORES */
        .error {
            margin-top: 6px;
            font-size: 12px;
            color: #b91c1c;
        }

        .is-invalid {
            border-color: #b91c1c;
        }

        /* FOOTER */
        .auth-footer {
            margin-top: 18px;
            text-align: center;
            color: #9ca3af;
            font-size: 12px;
        }
    </style>

    <script>
        function togglePassword() {
            const p = document.getElementById('password');
            p.type = p.type === 'password' ? 'text' : 'password';
        }
    </script>
@endsection
