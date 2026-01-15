<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('titulo', 'Dashboard') - Admin</title>
    <!-- SB Admin 2 Styles -->
    <link href="{{ asset('sbadmin2/vendor/fontawesome-free/css/all.min.css') }}" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,300,400,600,700,800,900" rel="stylesheet">
    <link href="{{ asset('sbadmin2/vendor/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('sbadmin2/css/sb-admin-2.min.css') }}" rel="stylesheet">
    @vite(['resources/js/app.js'])
</head>

<body id="page-top">
    <div id="wrapper">
        <!-- ===== SIDEBAR ===== -->
        <ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">
            <!-- Brand -->
            <a class="sidebar-brand d-flex align-items-center justify-content-center" href="{{ url('/') }}">
                <div class="sidebar-brand-icon rotate-n-15"><i class="fas fa-laugh-wink"></i></div>
                <div class="sidebar-brand-text mx-3">ADMINISTRADOR</div>
            </a>
            <hr class="sidebar-divider my-0">

            <!-- Buscador -->
            <div class="px-3 mt-3">
                <div class="input-group">
                    <input type="text" id="sidebarSearch" class="form-control bg-light border-0 small"
                        placeholder="Buscar en menú..." onkeyup="filterSidebar()">
                    <div class="input-group-append">
                        <button class="btn btn-primary"><i class="fas fa-search fa-sm"></i></button>
                    </div>
                </div>
            </div>
            <hr class="sidebar-divider">

            <!-- Menú CRUD -->
            <li class="nav-item"><a class="nav-link" href="{{ url('/cargas') }}"><i class="fas fa-boxes"></i> <span>Cargas</span></a></li>
            <li class="nav-item"><a class="nav-link" href="{{ url('/formularios') }}"><i class="fas fa-file-alt"></i> <span>Formularios</span></a></li>
            <li class="nav-item"><a class="nav-link" href="{{ url('/usuarios') }}"><i class="fas fa-users"></i> <span>Usuarios</span></a></li>
            <li class="nav-item"><a class="nav-link" href="{{ url('/DetalleCargas') }}"><i class="fas fa-clipboard-list"></i> <span>Detalle Cargas</span></a></li>
            <li class="nav-item"><a class="nav-link" href="{{ url('/anexos') }}"><i class="fas fa-paperclip"></i> <span>Anexos</span></a></li>
            <li class="nav-item"><a class="nav-link" href="{{ url('/indicadores') }}"><i class="fas fa-chart-line"></i> <span>Indicadores</span></a></li>
            <li class="nav-item"><a class="nav-link" href="{{ url('/dependencias') }}"><i class="fas fa-building"></i> <span>Dependencias</span></a></li>

            <hr class="sidebar-divider d-none d-md-block">
            <div class="text-center d-none d-md-inline"><button class="rounded-circle border-0" id="sidebarToggle"></button></div>
        </ul>
        <!-- ===== END SIDEBAR ===== -->

        <!-- ===== CONTENT WRAPPER ===== -->
        <div id="content-wrapper" class="d-flex flex-column">
            <div id="content">
                <!-- ===== TOPBAR ===== -->
                <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 shadow">
                    <!-- Logos izquierda -->
                    <div class="d-flex align-items-center mr-3">
                        <img src="{{ asset('sbadmin2/img/sedeco.png') }}" style="height:40px;" class="mr-2">
                        <img src="{{ asset('sbadmin2/img/seie.png') }}" style="height:40px;">
                    </div>

                    <!-- Sidebar Toggle (mobile) -->
                    <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3"><i class="fa fa-bars"></i></button>

                    <!-- Search -->
                    <form class="d-none d-sm-inline-block form-inline mr-auto ml-md-3 navbar-search">
                        <div class="input-group">
                            <input type="text" class="form-control bg-light border-0 small" placeholder="Buscar...">
                            <div class="input-group-append">
                                <button class="btn btn-primary"><i class="fas fa-search fa-sm"></i></button>
                            </div>
                        </div>
                    </form>

                    <!-- Topbar Navbar -->
                    <ul class="navbar-nav ml-auto">
                        <!-- Alerts -->
                        <li class="nav-item dropdown no-arrow mx-1">
                            <a class="nav-link dropdown-toggle" href="#" id="alertsDropdown" role="button" data-toggle="dropdown">
                                <i class="fas fa-bell fa-fw"></i>
                                <span class="badge badge-danger badge-counter">3+</span>
                            </a>
                        </li>

                        <!-- Messages -->
                        <li class="nav-item dropdown no-arrow mx-1">
                            <a class="nav-link dropdown-toggle" href="#" id="messagesDropdown" role="button" data-toggle="dropdown">
                                <i class="fas fa-envelope fa-fw"></i>
                                <span class="badge badge-danger badge-counter">7</span>
                            </a>
                        </li>

                        <div class="topbar-divider d-none d-sm-block"></div>

                        <!-- User Info -->
                        <li class="nav-item dropdown no-arrow">
                            <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button"
                                data-toggle="dropdown">
                                <span class="mr-2 d-none d-lg-inline text-gray-600 small">Yesenia Jimenez</span>
                                <img class="img-profile rounded-circle" src="{{ asset('sbadmin2/img/undraw_profile.svg') }}">
                            </a>
                            <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in" aria-labelledby="userDropdown">
                                <a class="dropdown-item" href="#">Perfil</a>
                                <a class="dropdown-item" href="#">Configuración</a>
                                <a class="dropdown-item" href="#">Registro de Actividad</a>
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item" href="{{ url('/') }}">Cerrar Sesión</a>
                            </div>
                        </li>

                        <!-- Logos derecha -->
                        <li class="nav-item d-flex align-items-center ml-3">
                            <img src="{{ asset('sbadmin2/img/ito.png') }}" style="height:40px;">
                        </li>
                    </ul>
                </nav>
                <!-- ===== END TOPBAR ===== -->


                <!-- ===== MAIN CONTENT ===== -->
                <div class="container-fluid">
                    @yield('content')
                </div>
            </div>

            <!-- ===== FOOTER ===== -->
            <footer class="sticky-footer bg-white">
                <div class="container my-auto">
                    <div class="copyright text-center my-auto d-flex justify-content-between" style="font-size: 14px; padding: 8px 15px;">
                        <div class="text-secondary">
                            <a href="#" class="text-secondary mx-2">Aviso de Privacidad</a> |
                            <a href="#" class="text-secondary mx-2">Contacto</a> |
                            <a href="#" class="text-secondary mx-2">Documentación</a>
                        </div>
                        <div class="text-secondary">Versión <strong>1.0.0</strong></div>
                    </div>
                </div>
            </footer>
        </div>
        <!-- END CONTENT WRAPPER -->
    </div>
    <!-- END WRAPPER -->

    <!-- Scripts -->
    <script src="{{ asset('sbadmin2/vendor/jquery/jquery.min.js') }}"></script>
    <script src="{{ asset('sbadmin2/vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('sbadmin2/vendor/jquery-easing/jquery.easing.min.js') }}"></script>
    <script src="{{ asset('sbadmin2/js/sb-admin-2.min.js') }}"></script>

    <script>
        function filterSidebar() {
            let input = document.getElementById("sidebarSearch").value.toLowerCase();
            let items = document.querySelectorAll("#accordionSidebar .nav-item");
            items.forEach(item => {
                let text = item.innerText.toLowerCase();
                item.style.display = text.includes(input) ? "" : "none";
            });
        }
    </script>
</body>

</html>