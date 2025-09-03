<link rel="stylesheet" href="{{ asset('css/menu.css') }}">

<aside id="layout-menu" class="layout-menu-horizontal menu-horizontal menu bg-menu-theme flex-grow-0">
    <style>

    </style>
    <div class="container-xxl d-flex h-100">
        <ul class="menu-inner">

            <!-- Página Principal -->
            <li class="menu-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                <a href="{{ route('dashboard') }}" class="menu-link menu-home">
                    <i class="menu-icon tf-icons bx bx-home"></i>
                    <span class="menu-title text-uppercase fw-semibold">Página Principal</span>
                </a>
            </li>

            <!-- Sección Registros -->
            <li class="menu-header small text-muted">Registros</li>

            <li class="menu-item {{ request()->routeIs('audiencias.*') ? 'active' : '' }}">
                <a href="{{ route('audiencias.create') }}" class="menu-link menu-aud">
                    <i class="menu-icon tf-icons bx bx-group"></i>
                    <span class="menu-title text-uppercase fw-semibold">Registro de Audiencia</span>
                </a>
            </li>

            <li class="menu-item {{ request()->routeIs('eventos.*') ? 'active' : '' }}">
                <a href="{{ route('eventos.create') }}" class="menu-link menu-evt">
                    <i class="menu-icon tf-icons bx bx-calendar-event"></i>
                    <span class="menu-title text-uppercase fw-semibold">Registro de Evento</span>
                </a>
            </li>

            <!-- Sección Consultas -->
            <li class="menu-header small text-muted">Consultas</li>

            <li class="menu-item {{ request()->routeIs('calendario.*') ? 'active' : '' }}">
                <a href="{{ route('calendario.index') }}" class="menu-link menu-cal">
                    <i class="menu-icon tf-icons bx bx-calendar"></i>
                    <span class="menu-title text-uppercase fw-semibold">Calendario de Actividades</span>
                </a>
            </li>

        </ul>
    </div>
</aside>
