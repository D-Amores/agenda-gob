<!DOCTYPE html>
<html lang="en" class="light-style customizer-hide" dir="ltr" data-theme="theme-default" data-assets-path="{{ asset('sneat/assets/') }}/"
data-template="horizontal-menu-template">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />

    <title>Verificación de Email</title>
    
    <meta name="description" content="Verificación de correo electrónico" />
    
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

    <!-- jQuery Confirm CSS -->
    <link rel="stylesheet" href="{{ asset('vendor/jquery-confirm/dist/jquery-confirm.min.css') }}" />

    <!-- Helpers -->
    <script src="{{ asset('sneat/assets/vendor/js/helpers.js') }}"></script>

    <!-- Template customizer & Theme config -->
    <script src="{{ asset('sneat/assets/vendor/js/template-customizer.js') }}"></script>
    <script src="{{ asset('sneat/assets/js/config.js') }}"></script>
    
    <!-- CSRF Token Meta -->
    <meta name="csrf-token" content="{{ csrf_token() }}" />
</head>

<body>
    <!-- Content -->
    <div class="container-xxl">
        <div class="authentication-wrapper authentication-basic d-flex align-items-center min-vh-100">
            <div class="authentication-inner w-100 mx-auto" style="max-width: 500px;">
                <!-- Email Verification Card -->
                <div class="card shadow-sm rounded-5 bg-white bg-opacity-75">
                    <div class="card-body p-5 text-center">

                        <!-- Logo -->
                        <div class="app-brand justify-content-center mb-4">
                            <span class="app-brand-text fw-bold fs-4 text-primary">Agenda</span>
                        </div>

                        <!-- Mensaje dinámico -->
                        <div id="verification-content">
                            @if(isset($success) && $success)
                                <!-- Verificación exitosa -->
                                <div class="mb-4">
                                    <i class="bx bx-check-circle display-1 text-success"></i>
                                </div>
                                <h4 class="mb-3 fw-semibold text-success">¡Verificación Exitosa! 🎉</h4>
                                <p class="text-muted mb-4">{{ $message }}</p>
                                
                                <div class="alert alert-success" role="alert">
                                    <i class="bx bx-mail-send me-2"></i>
                                    <strong>¡Tu cuenta ha sido creada!</strong><br>
                                    Hemos enviado tu contraseña a <strong>{{ $email }}</strong>
                                </div>
                                
                                <p class="text-muted">
                                    <i class="bx bx-info-circle me-1"></i>
                                    Revisa tu bandeja de entrada para encontrar tu contraseña.
                                </p>
                                
                            @elseif(isset($error) && $error)
                                <!-- Error en verificación -->
                                <div class="mb-4">
                                    <i class="bx bx-error-circle display-1 text-danger"></i>
                                </div>
                                <h4 class="mb-3 fw-semibold text-danger">Error de Verificación</h4>
                                <p class="text-muted mb-4">{{ $message }}</p>
                                
                                <div class="alert alert-danger" role="alert">
                                    <i class="bx bx-info-circle me-2"></i>
                                    El enlace de verificación puede haber expirado o ya fue usado.
                                </div>
                                
                            @else
                                <!-- Estado de carga/procesando -->
                                <p class="text-muted mb-4">
                                    Procesando tu verificación de email...
                                </p>
                                
                                <div class="d-flex justify-content-center mb-4">
                                    <div class="spinner-border text-primary" role="status">
                                        <span class="visually-hidden">Procesando...</span>
                                    </div>
                                </div>
                            @endif
                        </div>

                        <div class="d-grid gap-2 mt-4">
                            @if(isset($success) && $success)
                                <a href="{{ $redirect_url ?? route('login') }}" class="btn btn-success">
                                    <i class="bx bx-log-in me-2"></i>
                                    Ir al Login
                                </a>
                            @elseif(isset($error) && $error)
                                <a href="{{ route('login') }}" class="btn btn-outline-secondary">
                                    <i class="bx bx-log-in me-2"></i>
                                    Ir al Login
                                </a>
                            @else
                                <a href="{{ route('login') }}" class="btn btn-primary">
                                    <i class="bx bx-log-in me-2"></i>
                                    Ir al Login
                                </a>
                            @endif
                        </div>

                    </div>
                </div>
                <!-- /Email Verification Card -->
            </div>
        </div>
    </div>
    <!-- / Content -->

    <!-- Core JS -->
    <script src="{{ asset('sneat/assets/vendor/libs/jquery/jquery.js') }}"></script>
    <script src="{{ asset('sneat/assets/vendor/libs/popper/popper.js') }}"></script>
    <script src="{{ asset('sneat/assets/vendor/js/bootstrap.js') }}"></script>
    <script src="{{ asset('sneat/assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js') }}"></script>

    <script src="{{ asset('sneat/assets/vendor/libs/hammer/hammer.js') }}"></script>
    <script src="{{ asset('sneat/assets/vendor/libs/i18n/i18n.js') }}"></script>
    <script src="{{ asset('sneat/assets/vendor/libs/typeahead-js/typeahead.js') }}"></script>

    <script src="{{ asset('sneat/assets/vendor/js/menu.js') }}"></script>

    <!-- Main JS -->
    <script src="{{ asset('sneat/assets/js/main.js') }}"></script>

    <!-- jQuery Confirm -->
    <script src="{{ asset('vendor/jquery-confirm/dist/jquery-confirm.min.js') }}"></script>
    
    <!-- UI Wrapper -->
    <script src="{{ asset('js/jquery-confirm.js') }}"></script>
    
    <!-- Routes Helper -->
    <script>
        @include('partials.routes')
    </script>
    
    <!-- Email Verification JS -->
    <script src="{{ asset('js/auth/email-verification.js') }}"></script>
</body>
</html>
