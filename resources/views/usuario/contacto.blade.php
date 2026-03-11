@extends('layouts.usuario')

@section('titulo', 'Contacto')

@section('content')

    <div class="container py-4">

        <h4 class="mb-3">Soporte del Sistema</h4>

        <p class="text-muted">
            Si tienes dudas sobre el uso del sistema o presentas algún problema técnico,
            puedes comunicarte con el administrador del sistema.
        </p>

        <div class="card shadow-sm">
            <div class="card-body">

                <h5>Administrador del Sistema</h5>

                <p class="mb-1"><strong>Nombre:</strong> Administrador del Sistema</p>

                <p class="mb-1"><strong>Correo:</strong> soporte@sistema.gob.mx</p>

                <p class="mb-1"><strong>Teléfono:</strong> (951) 000 0000</p>

                <p class="mb-0"><strong>Horario:</strong> Lunes a Viernes de 9:00 a 16:00</p>

            </div>
        </div>

    </div>

@endsection
