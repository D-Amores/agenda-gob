<aside id="layout-menu" class="layout-menu-horizontal menu-horizontal menu bg-menu-theme flex-grow-0">
    <style>
        /* acentos: borde izquierdo y pequeño hover para distinguir las entradas */
        .menu-link.menu-aud { border-left: 4px solid #28c76f; padding-left: .75rem; }
        .menu-link.menu-evt { border-left: 4px solid #ff9f43; padding-left: .75rem; }
        .menu-link.menu-home { border-left: 4px solid #6a5cff; padding-left: .75rem; } /* morado */
        .menu-link.menu-cal { border-left: 4px solid #ffd166; padding-left: .75rem; }  /* amarillo */
        .menu-link.menu-aud .menu-icon { color: #28c76f; }
        .menu-link.menu-evt .menu-icon { color: #ff9f43; }
        .menu-link.menu-home .menu-icon { color: #6a5cff; }
        .menu-link.menu-cal .menu-icon { color: #ffd166; }
        .menu-link.menu-aud:hover, .menu-link.menu-evt:hover, .menu-link.menu-home:hover, .menu-link.menu-cal:hover { background: rgba(0,0,0,0.02); }

        /* Estados activos - fondo más fuerte y texto en blanco */
        .menu-horizontal .menu-inner > .menu-item.active .menu-link.menu-aud { background: #28c76f !important; border-left: 4px solid #1e9653; }
        .menu-horizontal .menu-inner > .menu-item.active .menu-link.menu-evt { background: #ff9f43 !important; border-left: 4px solid #e6892b; }
        .menu-horizontal .menu-inner > .menu-item.active .menu-link.menu-home { background: #6a5cff !important; border-left: 4px solid #5a4bff; }
        .menu-horizontal .menu-inner > .menu-item.active .menu-link.menu-cal { background: #ffd166 !important; border-left: 4px solid #e6bc4d; }

        .menu-item.active .menu-link .menu-icon { color: white !important; }
        .menu-item.active .menu-link .menu-title { color: white !important; }

        /* Hover en elementos activos */
        .menu-item.active .menu-link:hover { opacity: 0.9; }
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
