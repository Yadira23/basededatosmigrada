@php
    $custom = trim($__env->yieldContent('breadcrumbs'));
    $segments = request()->segments();

    // ✅ Detectar rol
    $role = null;
    if (auth()->check()) {
        if (auth()->user()->hasRole('admin')) {
            $role = 'admin';
        } elseif (auth()->user()->hasRole('usuario')) {
            $role = 'usuario';
        }
    }

    // ✅ Inicio correcto por rol
    $home = url('/');
    if ($role === 'admin') {
        $home = route('admin.dashboard');
    }
    if ($role === 'usuario') {
        $home = route('usuario.dashboard');
    }

    // ✅ Ocultar segmentos técnicos
    $hidden = ['admin', 'usuario'];

    // ✅ Etiquetas bonitas (lo que se muestra)
    $labels = [
        // usuario (aunque tu URL diga "formulario(s)", en UI queremos "Indicadores")
        'formularios' => 'Indicadores',
        'formulario' => 'Indicador',
        'indicadores' => 'Indicadores',
        'anexos' => 'Anexos',
        'cargas' => 'Cargas',
        'DetalleCargas' => 'Detalle de cargas',

        'create' => 'Crear',
        'edit' => 'Editar',
        'show' => 'Detalle',
    ];

    // ✅ Links REALES (solo los que existen) para evitar 404
    $links = [];

    if ($role === 'usuario') {
        $links['formularios'] = route('usuario.indicadores');
        $links['indicadores'] = route('usuario.indicadores');
        $links['formulario'] = route('usuario.indicadores'); // ✅ ESTE
        $links['anexos'] = route('usuario.anexos');
    }

    if ($role === 'admin') {
        // Ajusta si tienes rutas con name; si no, deja url()
        $links['formularios'] = url('/formularios');
        $links['indicadores'] = url('/indicadores');
        $links['anexos'] = url('/anexos');
        $links['dependencias'] = url('/dependencias');
        $links['usuarios'] = url('/usuarios');
        $links['cargas'] = url('/cargas');
        $links['DetalleCargas'] = url('/DetalleCargas');
    }

    // ✅ Filtrar segmentos: ocultar admin/usuario y quitar IDs intermedios (deja solo el último ID)
    $filtered = [];
    foreach ($segments as $seg) {
        if (in_array($seg, $hidden)) {
            continue;
        }
        $filtered[] = $seg;
    }

    // quitar IDs que NO sean el último elemento
    $lastIndex = count($filtered) - 1;
    $final = [];
    foreach ($filtered as $i => $seg) {
        if (is_numeric($seg) && $i !== $lastIndex) {
            continue;
        }
        $final[] = $seg;
    }
@endphp

<nav aria-label="breadcrumb" class="mb-3">
    <ol class="breadcrumb bg-transparent px-0 mb-0">
        <li class="breadcrumb-item">
            <a href="{{ $home }}">Inicio</a>
        </li>

        @if ($custom !== '')
            @yield('breadcrumbs')
        @else
            @foreach ($final as $seg)
                @php
                    $isLast = $loop->last;

                    $name = $labels[$seg] ?? ucwords(str_replace(['-', '_'], ' ', $seg));
                    if (is_numeric($seg)) {
                        $name = 'ID ' . $seg;
                    }

                    // solo linkear si existe en $links
                    $href = $links[$seg] ?? null;
                @endphp

                @if ($isLast)
                    <li class="breadcrumb-item active" aria-current="page">{{ $name }}</li>
                @else
                    <li class="breadcrumb-item">
                        @if ($href)
                            <a href="{{ $href }}">{{ $name }}</a>
                        @else
                            {{ $name }}
                        @endif
                    </li>
                @endif
            @endforeach
        @endif
    </ol>
</nav>
