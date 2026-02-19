<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('titulo', 'Admin') - Admin</title>

    <link href="{{ asset('sbadmin2/vendor/fontawesome-free/css/all.min.css') }}" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,300,400,600,700,800,900" rel="stylesheet">
    <link href="{{ asset('sbadmin2/css/sb-admin-2.min.css') }}" rel="stylesheet">

    @vite(['resources/js/app.js'])
    @livewireStyles
    @stack('styles')
</head>

<body id="page-top">
    <div id="wrapper">

        @auth
            <!-- SIDEBAR ADMIN -->
            <ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">

                <!-- Brand -->
                <a class="sidebar-brand d-flex align-items-center justify-content-center"
                    href="{{ route('admin.dashboard') }}">
                    <div class="sidebar-brand-icon rotate-n-15">
                        <i class="fas fa-landmark"></i>
                    </div>
                    <div class="sidebar-brand-text mx-3">ADMINISTRADOR</div>
                </a>

                <hr class="sidebar-divider my-0">

                <!-- Dashboard -->
                <li class="nav-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                    <a class="nav-link" href="{{ route('admin.dashboard') }}">
                        <i class="fas fa-fw fa-tachometer-alt"></i>
                        <span>Dashboard</span>
                    </a>
                </li>

                <hr class="sidebar-divider">

                <!-- Formularios -->
                <div class="sidebar-heading">Formularios</div>
                <li class="nav-item">
                    <a class="nav-link" href="{{ url('/formularios') }}">
                        <i class="fas fa-file-alt"></i>
                        <span>Formularios</span>
                    </a>
                </li>

                <hr class="sidebar-divider">
                <!-- Anexos -->
                <div class="sidebar-heading">Anexos</div>
                <li class="nav-item">
                    <a class="nav-link" href="{{ url('/anexos') }}">
                        <i class="fas fa-paperclip"></i>
                        <span>Anexos</span>
                    </a>
                </li>

                <hr class="sidebar-divider">
                <!-- Indicadores -->
                <div class="sidebar-heading">Indicadores</div>
                <li class="nav-item">
                    <a class="nav-link" href="{{ url('/indicadores') }}">
                        <i class="fas fa-chart-line"></i>
                        <span>Indicadores</span>
                    </a>
                </li>

                <hr class="sidebar-divider">
                <!-- Dependencias -->
                <div class="sidebar-heading">Dependencias</div>
                <li class="nav-item">
                    <a class="nav-link" href="{{ url('/dependencias') }}">
                        <i class="fas fa-building"></i>
                        <span>Listar</span>
                    </a>
                </li>

                <hr class="sidebar-divider">
                <!-- Usuarios -->
                <div class="sidebar-heading">Usuarios</div>
                <li class="nav-item">
                    <a class="nav-link" href="{{ url('/usuarios') }}">
                        <i class="fas fa-users"></i>
                        <span>Listar</span>
                    </a>
                </li>

                <hr class="sidebar-divider">
                <!-- Cargas -->
                <div class="sidebar-heading">Cargas</div>
                <li class="nav-item">
                    <a class="nav-link" href="{{ url('/cargas') }}">
                        <i class="fas fa-boxes"></i>
                        <span>Listar</span>
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link" href="{{ url('/DetalleCargas') }}">
                        <i class="fas fa-clipboard-list"></i>
                        <span>Detalle Cargas</span>
                    </a>
                </li>

                <hr class="sidebar-divider d-none d-md-block">

                <!-- Sidebar Toggler -->
                <div class="text-center d-none d-md-inline">
                    <button class="rounded-circle border-0" id="sidebarToggle"></button>
                </div>
            </ul>
        @endauth

        <!-- CONTENT WRAPPER -->
        <div id="content-wrapper" class="d-flex flex-column">
            <div id="content">

                @auth
                    <!-- TOPBAR (igual que el tuyo) -->
                    <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 shadow">
                        <div class="d-flex align-items-center mr-3">
                            <img src="{{ asset('sbadmin2/img/sedeco.png') }}" style="height:40px;" class="mr-2">
                            <img src="{{ asset('sbadmin2/img/seie.png') }}" style="height:40px;">
                        </div>

                        <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3">
                            <i class="fa fa-bars"></i>
                        </button>

                        <ul class="navbar-nav ml-auto">
                            <li class="nav-item dropdown no-arrow">
                                <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button"
                                    data-toggle="dropdown">
                                    <span class="mr-2 d-none d-lg-inline text-gray-600 small">
                                        {{ auth()->user()->nombre_usr ?? 'Admin' }}
                                    </span>
                                    <img class="img-profile rounded-circle"
                                        src="{{ asset('sbadmin2/img/undraw_profile.svg') }}">
                                </a>
                                <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in"
                                    aria-labelledby="userDropdown">
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit" class="dropdown-item">Cerrar Sesión</button>
                                    </form>
                                </div>
                            </li>

                            <li class="nav-item d-flex align-items-center ml-3">
                                <img src="{{ asset('sbadmin2/img/ito.png') }}" style="height:40px;">
                            </li>
                        </ul>
                    </nav>
                @endauth

                <div class="container-fluid">

                    {{-- ✅ MIGAS DE PAN (GLOBAL) --}}
                    <x-breadcrumbs />

                    @yield('content')
                </div>
            </div>

            @auth
                <footer class="sticky-footer bg-white">
                    <div class="container my-auto">
                        <div class="copyright text-center my-auto d-flex justify-content-between"
                            style="font-size: 14px; padding: 8px 15px;">
                            <div class="text-secondary">
                                <a href="#" class="text-secondary mx-2">Aviso de Privacidad</a> |
                                <a href="#" class="text-secondary mx-2">Contacto</a> |
                                <a href="#" class="text-secondary mx-2">Documentación</a>
                            </div>
                            <div class="text-secondary">Versión <strong>1.0.0</strong></div>
                        </div>
                    </div>
                </footer>
            @endauth
        </div>

    </div>

    <script src="{{ asset('sbadmin2/vendor/jquery/jquery.min.js') }}"></script>
    <script src="{{ asset('sbadmin2/vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('sbadmin2/vendor/jquery-easing/jquery.easing.min.js') }}"></script>
    <script src="{{ asset('sbadmin2/js/sb-admin-2.min.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    @livewireScripts
    @stack('scripts')
</body>

</html>
