<!DOCTYPE html>

<!-- beautify ignore:start -->
<html lang="en" class="light-style  customizer-hide" dir="ltr" data-theme="theme-default" data-assets-path="{{ asset('sneat/assets/') }}/"
data-template="horizontal-menu-template">
 <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />

    <title>Verificación Pendiente</title>
    
    <meta name="description" content="Most Powerful &amp; Comprehensive Bootstrap 5 HTML Admin Dashboard Template built for developers!" />
    <meta name="keywords" content="dashboard, bootstrap 5 dashboard, bootstrap 5 design, bootstrap 5">
    <!-- Canonical SEO -->
    <link rel="canonical" href="https://themeselection.com/products/sneat-bootstrap-html-admin-template/">
    
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="https://demos.themeselection.com/sneat-bootstrap-html-admin-template/assets/img/favicon/favicon.ico" />

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com/">
    <link rel="preconnect" href="https://fonts.gstatic.com/" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Public+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&amp;display=swap" rel="stylesheet">

    <!-- Icons -->
    <link rel="stylesheet" href="{{ asset('sneat/assets/vendor/fonts/boxicons.css') }}" />
    <link rel="stylesheet" href="{{ asset('sneat/assets/vendor/fonts/fontawesome.css') }}" />
    <link rel="stylesheet" href="{{ asset('sneat/assets/vendor/fonts/flag-icons.css') }}" />

    <!-- Core CSS -->
    <link rel="stylesheet" href="{{ asset('sneat/assets/vendor/css/rtl/core.css') }}" class="template-customizer-core-css" />
    <link rel="stylesheet" href="{{ asset('sneat/assets/vendor/css/rtl/theme-default.css') }}" class="template-customizer-theme-css" />
    <link rel="stylesheet" href="{{ asset('sneat/assets/css/demo.css') }}" />

    <!-- Vendors CSS -->
    <link rel="stylesheet" href="{{ asset('sneat/assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css') }}" />
    <link rel="stylesheet" href="{{ asset('sneat/assets/vendor/libs/typeahead-js/typeahead.css') }}" />

    <!-- Page CSS -->
    <link rel="stylesheet" href="{{ asset('sneat/assets/vendor/css/pages/page-auth.css') }}" />

    <!-- Helpers -->
    <script src="{{ asset('sneat/assets/vendor/js/helpers.js') }}"></script>

    <!-- Template customizer & Theme config -->
    <script src="{{ asset('sneat/assets/vendor/js/template-customizer.js') }}"></script>
    <script src="{{ asset('sneat/assets/js/config.js') }}"></script>

</head>

<body>

  <!-- Content -->

   <div class="container-xxl">
    <div class="authentication-wrapper authentication-basic d-flex align-items-center min-vh-100">
        <div class="authentication-inner w-100 mx-auto" style="max-width: 500px;">
            <!-- Verification Pending Card -->
            <div class="card shadow-sm rounded-5 bg-white bg-opacity-75">
                <div class="card-body p-5 text-center">

                    <!-- Logo -->
                    <div class="app-brand justify-content-center mb-4">
                        <span class="app-brand-text fw-bold fs-4 text-primary">Agenda</span>
                    </div>

                    <!-- Icono principal -->
                    <div class="mb-4">
                        <i class="bx bx-mail-send display-1 text-primary"></i>
                    </div>

                    <h4 class="mb-3 fw-semibold">¡Verifica tu correo electrónico! 📧</h4>
                    
                    @if(session('success'))
                        <div id="success-message" class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="bx bx-check-circle me-2"></i>
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <p class="text-muted mb-4">
                        Hemos enviado un enlace de verificación a 
                        @if(session('email'))
                            <strong>{{ session('email') }}</strong>
                        @else
                            <strong>tu correo electrónico</strong>
                        @endif
                    </p>

                    <div class="alert alert-info" role="alert">
                        <i class="bx bx-info-circle me-2"></i>
                        <strong>¿Qué sigue?</strong><br>
                        1. Revisa tu bandeja de entrada<br>
                        2. Haz clic en el enlace de verificación<br>
                        3. Tu cuenta será creada automáticamente<br>
                        4. Recibirás tu contraseña por email
                    </div>

                    <div class="mb-4">
                        <small class="text-muted">
                            <i class="bx bx-time-five me-1"></i>
                            El enlace expira en 24 horas
                        </small>
                    </div>

                    <div class="d-grid gap-2">
                        <a href="{{ route('login') }}" class="btn btn-primary">
                            <i class="bx bx-log-in me-2"></i>
                            Ir al Login
                        </a>
                        
                        <a href="{{ route('register') }}" class="btn btn-outline-secondary">
                            <i class="bx bx-user-plus me-2"></i>
                            Registrar otra cuenta
                        </a>
                    </div>

                    <div class="mt-4 pt-3 border-top">
                        <small class="text-muted">
                            <i class="bx bx-help-circle me-1"></i>
                            ¿No recibiste el email? Revisa tu carpeta de spam o contacta al administrador.
                        </small>
                    </div>

                </div>
            </div>
            <!-- /Verification Pending Card -->
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const successMessage = document.getElementById('success-message');
    
    if (successMessage) {
        setTimeout(() => {
            successMessage.style.transition = "opacity 0.5s";
            successMessage.style.opacity = 0;
            setTimeout(() => successMessage.remove(), 500);
        }, 8000); // 8 segundos para que lean bien el mensaje
    }
});
</script>

    <!-- / Content -->    

    <!-- Core JS -->
    <!-- build:js assets/vendor/js/core.js -->
    <script src="{{ asset('sneat/assets/vendor/libs/jquery/jquery.js') }}"></script>
    <script src="{{ asset('sneat/assets/vendor/libs/popper/popper.js') }}"></script>
    <script src="{{ asset('sneat/assets/vendor/js/bootstrap.js') }}"></script>
    <script src="{{ asset('sneat/assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js') }}"></script>

    <script src="{{ asset('sneat/assets/vendor/libs/hammer/hammer.js') }}"></script>
    <script src="{{ asset('sneat/assets/vendor/libs/i18n/i18n.js') }}"></script>
    <script src="{{ asset('sneat/assets/vendor/libs/typeahead-js/typeahead.js') }}"></script>

    <script src="{{ asset('sneat/assets/vendor/js/menu.js') }}"></script>
    <!-- endbuild -->

    <!-- Main JS -->
    <script src="{{ asset('sneat/assets/js/main.js') }}"></script>

    
</body>
</html>
