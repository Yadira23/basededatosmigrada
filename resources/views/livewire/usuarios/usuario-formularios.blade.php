<div class="card shadow-sm border-left-primary mb-3">
    <div class="card-body">
        <h4>Módulo de Formularios</h4>
        @if($formularios->isEmpty())
        <div class="alert alert-info">No hay formularios publicados para tu dependencia.</div>
        @else
        <ul class="list-group">
            @foreach($formularios as $formulario)
            <div>
                <h5>{{ $formulario->titulo_form }}</h5>
                <p>{{ $formulario->descripcion_form }}</p>
                <button wire:click="abrirFormulario({{ $formulario->id_form }})" class="btn btn-primary">
                    Abrir
                </button>
            </div>
            @endforeach
        </ul>
        @endif
    </div>
</div>