<div class="row">
    <div class="col-md-12 mb-3">
        <h4>Bienvenido, {{ auth()->user()->nombre_usr }}</h4>
        <p class="text-muted">Dependencia: {{ auth()->user()->dependencia->nombre_depen ?? '' }}</p>
    </div>

    <div class="col-md-4">
        <div class="card shadow-sm">
            <div class="card-body text-center">
                <h6>Formularios disponibles</h6>
                <h2>{{ $formulariosDisponibles }}</h2>
                <a href="{{ route('usuario.formularios') }}" class="btn btn-primary btn-sm">
                    Ir a Formularios
                </a>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card shadow-sm">
            <div class="card-body text-center">
                <h6>Cargas realizadas</h6>
                <h2>{{ $cargasRealizadas }}</h2>
            </div>
        </div>
    </div>
</div>

