<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name', 'Integra') }}</title>

    {{-- Si usas Vite --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    {{-- Si usas otros estilos globales, aquí los pones --}}
</head>

<body>
    @yield('content')
</body>

</html>
