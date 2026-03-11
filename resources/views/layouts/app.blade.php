<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('titulo', 'Dashboard') - Admin</title>

    <!-- SB Admin 2 Styles -->
    <link href="{{ asset('sbadmin2/vendor/fontawesome-free/css/all.min.css') }}" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,300,400,600,700,800,900" rel="stylesheet">
    <link href="{{ asset('sbadmin2/css/sb-admin-2.min.css') }}" rel="stylesheet">

    @vite(['resources/js/app.js'])
    @livewireStyles
    @stack('styles')
    <style>
        /* SOLO ADMIN (usuario no cambia) */
        .sidebar-admin {
            background: linear-gradient(180deg, #7f1d1d 0%, #450a0a 100%) !important;
            /* vino/rojo */
        }

        .sidebar-admin .btn.btn-primary {
            background-color: #7f1d1d !important;
            border-color: #7f1d1d !important;
        }

        /* ==========================
   SOLO ADMIN: cambiar "primary" (azul SB Admin) a rojo
   aplica en TODO el contenido (Formularios, Cargas, etc.)
   ========================== */

        /* Botones primary dentro del contenido */
        .sidebar-admin~#content-wrapper .btn-primary {
            background-color: #7f1d1d !important;
            border-color: #7f1d1d !important;
        }

        /* Textos primary dentro del contenido */
        .sidebar-admin~#content-wrapper .text-primary {
            color: #7f1d1d !important;
        }

        /* Fondos primary dentro del contenido */
        .sidebar-admin~#content-wrapper .bg-primary {
            background-color: #7f1d1d !important;
        }

        /* Bordes left primary (cards tipo KPI) dentro del contenido */
        .sidebar-admin~#content-wrapper .border-left-primary {
            border-left: .25rem solid #7f1d1d !important;
        }
    </style>

</head>

<body id="page-top">
    <div id="wrapper">

        <!-- ===== SIDEBAR ===== -->
        @auth
            @php $user = auth()->user(); @endphp

            <ul class="navbar-nav sidebar sidebar-dark accordion
        @role('admin') sidebar-admin @endrole
        @role('usuario') bg-gradient-primary @endrole"
                id="accordionSidebar">

                <!-- Brand -->
                <a class="sidebar-brand d-flex align-items-center justify-content-center" href="{{ url('/') }}">
                    <div class="sidebar-brand-icon rotate-n-15">
                        <i class="fas fa-landmark"></i>
                    </div>
                    <div class="sidebar-brand-text mx-3">
                        @role('admin')
                            ADMINISTRADOR
                            @elserole('usuario')
                            {{ strtoupper($user->dependencia->nombre_depen ?? 'USUARIO') }}
                        @endrole
                    </div>
                </a>

                <hr class="sidebar-divider my-0">

                <!-- Buscador -->
                <div class="px-3 mt-3">
                    <div class="input-group">
                        <input type="text" id="sidebarSearch" class="form-control bg-light border-0 small"
                            placeholder="Buscar en menú..." onkeyup="filterSidebar()">
                        <div class="input-group-append">
                            <button class="btn btn-primary" type="button">
                                <i class="fas fa-search fa-sm"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <hr class="sidebar-divider">

                <!-- ===== DASHBOARD / PANEL ===== -->
                @role('admin')
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('admin.dashboard') }}">
                            <i class="fas fa-fw fa-tachometer-alt"></i>
                            <span>Dashboard</span>
                        </a>
                    </li>
                @endrole

                @role('usuario')
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('usuario.dashboard') }}">
                            <i class="fas fa-fw fa-home"></i>
                            <span>Panel</span>
                        </a>
                    </li>
                @endrole

                <!-- =========================================================
                                                                                             MENÚ USUARIO (solo lo que puede ver)
                                                                                        ========================================================== -->
                @role('usuario')
                    <hr class="sidebar-divider">
                    <div class="sidebar-heading">Captura</div>

                    <li class="nav-item">
                        <a class="nav-link" href="{{ url('/formularios') }}">
                            <i class="fas fa-file-alt"></i>
                            <span>Formularios</span>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link" href="{{ url('/anexos') }}">
                            <i class="fas fa-paperclip"></i>
                            <span>Anexos</span>
                        </a>
                    </li>
                @endrole

                <!-- =========================================================
                                                                                             MENÚ ADMIN (todo lo de administración)
                                                                                        ========================================================== -->
                @role('admin')
                    <hr class="sidebar-divider">
                    <div class="sidebar-heading">Formularios</div>

                    <li class="nav-item {{ request()->is('formularios*') ? 'active' : '' }}">
                        <a class="nav-link" href="{{ url('/formularios') }}">
                            <i class="fas fa-file-alt"></i>
                            <span>Formularios</span>
                        </a>
                    </li>

                    <hr class="sidebar-divider">
                    <div class="sidebar-heading">Anexos</div>

                    <li class="nav-item {{ request()->is('anexos*') ? 'active' : '' }}">
                        <a class="nav-link" href="{{ url('/anexos') }}">
                            <i class="fas fa-paperclip"></i>
                            <span>Anexos</span>
                        </a>
                    </li>

                    <hr class="sidebar-divider">
                    <div class="sidebar-heading">Indicadores</div>

                    <li class="nav-item {{ request()->is('indicadores*') ? 'active' : '' }}">
                        <a class="nav-link" href="{{ url('/indicadores') }}">
                            <i class="fas fa-chart-line"></i>
                            <span>Indicadores</span>
                        </a>
                    </li>

                    <hr class="sidebar-divider">
                    <div class="sidebar-heading">Dependencias</div>

                    <li class="nav-item {{ request()->is('dependencias*') ? 'active' : '' }}">
                        <a class="nav-link" href="{{ url('/dependencias') }}">
                            <i class="fas fa-building"></i>
                            <span>Dependencias</span>
                        </a>
                    </li>

                    <hr class="sidebar-divider">
                    <div class="sidebar-heading">Usuarios</div>

                    <li class="nav-item {{ request()->is('usuarios*') ? 'active' : '' }}">
                        <a class="nav-link" href="{{ url('/usuarios') }}">
                            <i class="fas fa-users"></i>
                            <span>Usuarios</span>
                        </a>
                    </li>

                    <hr class="sidebar-divider">
                    <div class="sidebar-heading">Cargas</div>

                    <li class="nav-item {{ request()->is('cargas*') ? 'active' : '' }}">
                        <a class="nav-link" href="{{ url('/cargas') }}">
                            <i class="fas fa-boxes"></i>
                            <span>Cargas</span>
                        </a>
                    </li>

                    <li class="nav-item {{ request()->is('DetalleCargas*') ? 'active' : '' }}">
                        <a class="nav-link" href="{{ url('/DetalleCargas') }}">
                            <i class="fas fa-clipboard-list"></i>
                            <span>Detalle Cargas</span>
                        </a>
                    </li>

                    <hr class="sidebar-divider">
                    <div class="sidebar-heading">Configuración</div>

                    <li class="nav-item {{ request()->routeIs('admin.configuracion.captura') ? 'active' : '' }}">
                        <a class="nav-link" href="{{ route('admin.configuracion.captura') }}">
                            <i class="fas fa-cog"></i>
                            <span>Configuración de Captura</span>
                        </a>
                    </li>
                @endrole

                <hr class="sidebar-divider d-none d-md-block">

                <!-- Sidebar Toggler -->
                <div class="text-center d-none d-md-inline">
                    <button class="rounded-circle border-0" id="sidebarToggle"></button>
                </div>

            </ul>
        @endauth
        <!-- ===== END SIDEBAR ===== -->

        <!-- ===== CONTENT WRAPPER ===== -->
        <div id="content-wrapper" class="d-flex flex-column">
            <div id="content">

                <!-- ===== TOPBAR ===== -->
                @auth
                    <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 shadow">

                        <!-- Logos izquierda -->
                        <div class="d-flex align-items-center mr-3">
                            <img src="{{ asset('sbadmin2/img/sedeco.png') }}" style="height:40px;" class="mr-2">
                            <img src="{{ asset('sbadmin2/img/seie.png') }}" style="height:40px;">
                        </div>

                        <!-- Sidebar Toggle (mobile) -->
                        <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3">
                            <i class="fa fa-bars"></i>
                        </button>

                        <!-- Search -->
                        <form method="GET" action="{{ route('admin.buscar') }}"
                            class="d-none d-sm-inline-block form-inline mr-auto ml-md-3 navbar-search">
                            <div class="input-group">
                                <input type="text" name="q" value="{{ request('q') }}"
                                    class="form-control bg-light border-0 small" placeholder="Buscar...">
                                <div class="input-group-append">
                                    <button class="btn btn-primary" type="submit">
                                        <i class="fas fa-search fa-sm"></i>
                                    </button>
                                </div>
                            </div>
                        </form>

                        <img src="{{ asset('sbadmin2/img/Integra.png') }}" style="height:40px;" class="mr-2">

                        <!-- Topbar Navbar -->
                        <ul class="navbar-nav ml-auto">

                            <!-- User Info -->
                            <li class="nav-item dropdown no-arrow">
                                <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button"
                                    data-toggle="dropdown">
                                    <span class="mr-2 d-none d-lg-inline text-gray-600 small">
                                        {{ auth()->user()->nombre_usr ?? 'Usuario' }}
                                    </span>
                                    <img class="img-profile rounded-circle"
                                        src="{{ asset('sbadmin2/img/undraw_profile.svg') }}">
                                </a>
                                <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in"
                                    aria-labelledby="userDropdown">
                                    <a class="dropdown-item" href="{{ route('admin.perfil') }}">
                                        Perfil
                                    </a>
                                    <div class="dropdown-divider"></div>
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit" class="dropdown-item">Cerrar Sesión</button>
                                    </form>
                                </div>
                            </li>

                            <!-- Logos derecha -->
                            <li class="nav-item d-flex align-items-center ml-3">
                                <img src="{{ asset('sbadmin2/img/ito.png') }}" style="height:40px;">
                            </li>
                        </ul>
                    </nav>
                @endauth
                <!-- ===== END TOPBAR ===== -->

                <!-- ===== MAIN CONTENT ===== -->
                <div class="container-fluid">

                    {{-- ✅ MIGAS DE PAN (GLOBAL) --}}
                    <x-breadcrumbs />

                    @yield('content')
                </div>

            </div>

            <!-- ===== FOOTER ===== -->
            @auth
                <footer class="sticky-footer bg-white">
                    <div class="container my-auto">
                        <div class="copyright text-center my-auto d-flex justify-content-between"
                            style="font-size: 14px; padding: 8px 15px;">
                            <div class="text-secondary">
                                <a href="#" class="text-secondary mx-2">Aviso de Privacidad</a> |
                                <a href="{{ route('documentacion.admin') }}" class="mx-2">
                                    Documentación
                                </a>
                            </div>
                            <div class="text-secondary">Versión <strong>1.0.0</strong></div>
                        </div>
                    </div>
                </footer>
            @endauth

        </div>
        <!-- END CONTENT WRAPPER -->
    </div>
    <!-- END WRAPPER -->

    <!-- Scripts -->
    <script src="{{ asset('sbadmin2/vendor/jquery/jquery.min.js') }}"></script>
    <script src="{{ asset('sbadmin2/vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('sbadmin2/vendor/jquery-easing/jquery.easing.min.js') }}"></script>
    <script src="{{ asset('sbadmin2/vendor/jquery/jquery.min.js') }}"></script>
    <script>
        if (!window.jQuery) {
            document.write('<script src="https://code.jquery.com/jquery-3.6.0.min.js"><\/script>');
        }
    </script>
    <script src="{{ asset('sbadmin2/js/sb-admin-2.min.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

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

    <script>
        window.addEventListener('show-formulario-modal', event => {
            // si NO usas bootstrap 5, comenta esta parte
            // var myModal = new bootstrap.Modal(document.getElementById('FormularioCreateModal'), {});
            // myModal.show();
        });
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', () => {

            function forceCloseModal(el) {
                // quitar foco
                if (document.activeElement) document.activeElement.blur();

                // ✅ Bootstrap 4 (SB Admin 2): jQuery modal
                if (window.jQuery && typeof window.jQuery(el).modal === 'function') {
                    window.jQuery(el).modal('hide');
                }
                // ✅ Bootstrap 5: bootstrap.Modal
                else if (window.bootstrap && bootstrap.Modal) {
                    const modal = bootstrap.Modal.getOrCreateInstance(el);
                    modal.hide();
                }

                // Limpieza (por si queda pegado)
                el.classList.remove('show', 'd-block');
                el.style.display = 'none';
                el.setAttribute('aria-hidden', 'true');

                document.body.classList.remove('modal-open');
                document.querySelectorAll('.modal-backdrop').forEach(b => b.remove());
                document.body.style.removeProperty('overflow');
                document.body.style.removeProperty('padding-right');
            }

            function hideAnyModalById(id) {
                const el = id ? document.getElementById(id) : null;
                if (el) return forceCloseModal(el);

                const any = document.querySelector('.modal.show') || document.querySelector('.modal.d-block');
                if (any) forceCloseModal(any);
            }

            window.addEventListener('close-modal', (event) => hideAnyModalById(event.detail?.id));
            document.addEventListener('close-modal', (event) => hideAnyModalById(event.detail?.id));
        });
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', () => {

            function openModalById(id) {
                const el = document.getElementById(id);
                if (!el) return;

                // Bootstrap 4 (SB Admin 2)
                if (window.jQuery && typeof window.jQuery(el).modal === 'function') {
                    window.jQuery(el).modal('show');
                }
                // Bootstrap 5
                else if (window.bootstrap && bootstrap.Modal) {
                    const modal = bootstrap.Modal.getOrCreateInstance(el);
                    modal.show();
                }
            }

            window.addEventListener('show-modal', (event) => {
                openModalById(event.detail?.id || 'DataModal');
            });

        });
    </script>

    @livewireScripts
    @stack('scripts')
</body>

</html>
