<div>

    {{-- SECCIÓN 1: CONTEXTO --}}
    <div class="mb-4">
        <h2 class="text-xl font-bold">
            {{ $formulario->titulo_form ?? 'Formulario no asignado' }}
        </h2>
        <p>{{ $formulario->descripcion_form ?? '' }}</p>
    </div>

    {{-- SECCIÓN 2: INDICADORES --}}
    <div class="mb-4">
        <h3 class="font-semibold">Indicadores</h3>

        @foreach ($indicadores as $indicador)
            <div class="border p-2 mb-2">
                <strong>{{ $indicador->nombre_ind }}</strong>
                <p>{{ $indicador->definicion_ind }}</p>
            </div>
        @endforeach
    </div>

    {{-- SECCIÓN 3: ANEXOS --}}
    <div class="mb-4">
        <h3 class="font-semibold">Anexos requeridos</h3>

        @foreach ($anexos as $anexo)
            <div class="border p-2 mb-2">
                {{ $anexo->nombre_anexo }} ({{ $anexo->tipo_anexo }})
            </div>
        @endforeach
    </div>

    {{-- SECCIÓN 4: TIPO DE CARGA --}}
    <div class="mb-4">
        <h3 class="font-semibold">¿Cómo deseas cargar la información?</h3>

        <label class="mr-4">
            <input type="radio" wire:model="tipoCarga" value="anexo">
            Subir archivo
        </label>

        <label>
            <input type="radio" wire:model="tipoCarga" value="web">
            Capturar en web
        </label>
    </div>

    {{-- SECCIÓN 5: ÁREA DINÁMICA --}}
    <div class="mb-4">
        @if ($tipoCarga === 'anexo')
            <div class="border p-4">
                <h4>Subir archivo</h4>
                <input type="file" wire:model="archivo">
            </div>
        @endif

        @if ($tipoCarga === 'web')
            <div class="border p-4">
                <h4>Captura manual</h4>

                <input type="text" class="border p-1 mb-2"
                       placeholder="Valor ejemplo"
                       wire:model="datosWeb.valor">
            </div>
        @endif
    </div>

    {{-- SECCIÓN 6: CONTINUAR --}}
    <button class="bg-blue-600 text-white px-4 py-2 rounded">
        Continuar
    </button>

</div>
