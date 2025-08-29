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
    </style>
    <div class="container-xxl d-flex h-100">
        <ul class="menu-inner">

            <!-- Página Principal -->
            <li class="menu-item">
                <a href="{{ route('dashboard') }}" class="menu-link menu-home active">
                    <i class="menu-icon tf-icons bx bx-home"></i>
                    <span class="menu-title text-uppercase fw-semibold">Página Principal</span>
                </a>
            </li>

            <!-- Sección Registros -->
            <li class="menu-header small text-muted">Registros</li>

            <li class="menu-item">
                <a href="{{ route('audiencias.create') }}" class="menu-link menu-aud">
                    <i class="menu-icon tf-icons bx bx-group"></i>
                    <span class="menu-title text-uppercase fw-semibold">Registro de Audiencia</span>
                </a>
            </li>

            <li class="menu-item">
                <a href="{{ route('eventos.create') }}" class="menu-link menu-evt">
                    <i class="menu-icon tf-icons bx bx-calendar-event"></i>
                    <span class="menu-title text-uppercase fw-semibold">Registro de Evento</span>
                </a>
            </li>

            <!-- Sección Consultas -->
            <li class="menu-header small text-muted">Consultas</li>

            <li class="menu-item">
                <a href="{{ route('calendario.index') }}" class="menu-link menu-cal">
                    <i class="menu-icon tf-icons bx bx-calendar"></i>
                    <span class="menu-title text-uppercase fw-semibold">Calendario de Actividades</span>
                </a>
            </li>

        </ul>
    </div>
</aside>
