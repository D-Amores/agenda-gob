<!DOCTYPE html>

<!-- beautify ignore:start -->
<html lang="en" class="light-style  customizer-hide" dir="ltr" data-theme="theme-default" data-assets-path="{{ asset('sneat/assets/') }}/"
data-template="horizontal-menu-template">
 <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />

    <title>Recuperar Contraseña</title>
    
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
            <!-- Password Reset Card -->
            <div class="card shadow-sm rounded-5 bg-white bg-opacity-75">
                <div class="card-body p-5">

                    <!-- Logo -->
                    <div class="app-brand justify-content-center mb-4 text-center">
                        <span class="app-brand-text fw-bold fs-4 text-primary">Agenda</span>
                    </div>

                    <h4 class="mb-2 text-center fw-semibold">¿Olvidaste tu contraseña? 🔐</h4>
                    <p class="mb-4 text-center text-muted">Ingresa tu correo electrónico verificado y te enviaremos una nueva contraseña</p>

                    <form id="formPasswordReset" action="{{ route('password.send') }}" method="POST" novalidate>
                        @csrf

                        {{-- Contenedor para mensajes dinámicos --}}
                        <div id="dynamic-alerts"></div>

                        <div class="mb-3">
                            <label for="email" class="form-label">Correo Electrónico <span class="text-danger">*</span></label>
                            <div class="input-group input-group-merge">
                                <span class="input-group-text"><i class="bx bx-envelope"></i></span>
                                <input type="email" 
                                    class="form-control @error('email') is-invalid @enderror" 
                                    id="email" name="email" placeholder="Ingresa tu correo electrónico" 
                                    value="{{ old('email') }}" autofocus required>
                            </div>
                        </div>

                        <div class="d-grid mb-3">
                            <button class="btn btn-primary btn-lg fw-semibold" type="submit" id="submit-btn">
                                <span id="btn-text">
                                    <i class="bx bx-mail-send me-2"></i>
                                    Enviar Nueva Contraseña
                                </span>
                                <span id="btn-loading" class="d-none">
                                    <span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>
                                    Enviando...
                                </span>
                            </button>
                        </div>

                        <div class="text-center mb-3">
                            <small class="text-muted">
                                <i class="bx bx-info-circle me-1"></i>
                                Recibirás una nueva contraseña automática en tu correo verificado
                            </small>
                        </div>

                        <div class="text-center">
                            <p class="mb-0">¿Recordaste tu contraseña? 
                                <a href="{{ route('login') }}" class="text-primary fw-semibold">Iniciar Sesión</a>
                            </p>
                        </div>

                    </form>

                </div>
            </div>
            <!-- /Password Reset Card -->
        </div>
    </div>
</div>
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

        <!-- jQuery Confirm Library -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery-confirm/3.3.2/jquery-confirm.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-confirm/3.3.2/jquery-confirm.min.js"></script>

    <!-- Page JS -->
    <script src="{{ asset('js/jquery-confirm/jquery-confirm.js') }}"></script>
    <script src="{{ asset('js/auth/forgot-password.js') }}"></script>

    
</body>
</html>
