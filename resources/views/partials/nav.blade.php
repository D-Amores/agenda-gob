<link rel="stylesheet" href="{{ asset('css/navbar.css') }}">
<link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>

<nav class="layout-navbar navbar navbar-expand-xl align-items-center" id="layout-navbar">
    <div class="container-xxl">
        <div class="navbar-brand app-brand demo d-none d-xl-flex py-0 me-4">
            <a href="{{ route('dashboard') }}" class="app-brand-link gap-2">
                <span class="app-brand-logo demo">
                    <i class='bx bx-calendar-event'></i>
                </span>
                <span class="app-brand-text demo menu-text fw-bolder">Agenda</span>
            </a>

            <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto d-xl-none">
                <i class="bx bx-chevron-left bx-sm align-middle"></i>
            </a>
        </div>

        <!-- Botón de menú hamburguesa -->
        <div class="navbar-nav align-items-center d-xl-none">
            <a class="nav-link layout-menu-toggle" href="javascript:void(0)">
                <i class="bx bx-menu bx-md"></i>
            </a>
        </div>

        <div class="navbar-nav-right d-flex align-items-center" id="navbar-collapse">
            <ul class="navbar-nav flex-row align-items-center ms-auto">
                <!-- User -->
                <li class="nav-item navbar-dropdown dropdown-user dropdown me-3">
                    <div class="flex-grow-1 user-info">
                        <span class="fw-semibold d-block">{{ auth()->user()->username }}</span>
                        <small class="text-muted d-block">{{ auth()->user()->area->area }}</small>
                    </div>
                </li>
                <li class="nav-item navbar-dropdown dropdown-user dropdown">
                    <a class="nav-link dropdown-toggle hide-arrow" href="javascript:void(0);" data-bs-toggle="dropdown"
                        id="userDropdownToggle">
                        <div class="avatar avatar-online">
                            <img src="{{ auth()->user()->avatar_url }}" alt class="w-px-40 h-auto rounded-circle">
                        </div>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end" id="userDropdownMenu">
                        <li>
                            <!-- SOLO VISUAL - No es clickeable -->
                            <div class="dropdown-item visual-item">
                                <div class="d-flex">
                                    <div class="flex-shrink-0 me-3">
                                        <div class="avatar avatar-online">
                                            <img src="{{ auth()->user()->avatar_url }}" alt
                                                class="w-px-40 h-auto rounded-circle">
                                        </div>
                                    </div>
                                    <div class="flex-grow-1">
                                        <span class="fw-semibold d-block">{{ auth()->user()->username }}</span>
                                        <small class="text-muted">{{ auth()->user()->area->area }}</small>
                                    </div>
                                </div>
                            </div>
                        </li>
                        <li>
                            <div class="dropdown-divider"></div>
                        </li>
                        <li>
                            <!-- ENLACE REAL para editar perfil -->
                            <a class="dropdown-item" href="{{ route('profile.edit', auth()->user()) }}">
                                <i class="bx bx-user me-2"></i>
                                <span class="align-middle">Editar Perfil</span>
                            </a>
                        </li>
                        <li>
                            <div class="dropdown-divider"></div>
                        </li>
                        <li>
                            <!-- ENLACE REAL para cerrar sesión -->
                            <a class="dropdown-item" href="#"
                                onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                <i class="bx bx-power-off me-2"></i>
                                <span class="align-middle">Cerrar Sesión</span>
                            </a>
                        </li>
                    </ul>
                </li>
                <!--/ User -->
            </ul>
        </div>
    </div>
</nav>

<!-- Formulario de logout -->
<form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
    @csrf
</form>

<script>
    // Solución para el problema del dropdown que no se abre después de navegar
    document.addEventListener('DOMContentLoaded', function() {
        const dropdownToggle = document.getElementById('userDropdownToggle');
        const dropdownMenu = document.getElementById('userDropdownMenu');

        if (dropdownToggle && dropdownMenu) {
            // Re-inicializar el dropdown de Bootstrap
            const bsDropdown = new bootstrap.Dropdown(dropdownToggle);

            // Mantener el dropdown abierto cuando se hace clic dentro de él
            dropdownMenu.addEventListener('click', function(e) {
                e.stopPropagation();
            });

            // Forzar la re-inicialización si hay problemas
            dropdownToggle.addEventListener('click', function(e) {
                // Solo si el dropdown no está mostrándose
                if (!dropdownMenu.classList.contains('show')) {
                    bsDropdown.show();
                }
            });
        }
    });
</script>
