<!DOCTYPE html>

<!-- beautify ignore:start -->
<html lang="en" class="light-style  customizer-hide" dir="ltr" data-theme="theme-default" data-assets-path="{{ asset('sneat/assets/') }}/"
data-template="horizontal-menu-template">
 <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />

    <title>Registrarse</title>
    
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

    <!-- Vendor -->
    <link rel="stylesheet" href="{{ asset('sneat/assets/vendor/libs/formvalidation/dist/css/formValidation.min.css') }}" />

    <!-- Page CSS -->
    <link rel="stylesheet" href="{{ asset('sneat/assets/vendor/css/pages/page-auth.css') }}" />

    <!-- Helpers -->
    <script src="{{ asset('sneat/assets/vendor/js/helpers.js') }}"></script>

    <!-- Template customizer & Theme config -->
    <script src="{{ asset('sneat/assets/vendor/js/template-customizer.js') }}"></script>
    <script src="{{ asset('sneat/assets/js/config.js') }}"></script>

    
    <!-- Global site tag (gtag.js) - Google Analytics -->
    <script async="async" src="https://www.googletagmanager.com/gtag/js?id=GA_MEASUREMENT_ID"></script>
    <script>
    window.dataLayer = window.dataLayer || [];

    function gtag() {
      dataLayer.push(arguments);
    }
    gtag('js', new Date());
    gtag('config', 'GA_MEASUREMENT_ID');
    </script>
    <!-- Custom notification for demo -->
    <!-- beautify ignore:end -->

</head>

<body>

  <!-- Content -->

   <div class="container-xxl">
    <div class="authentication-wrapper authentication-basic d-flex align-items-center min-vh-100">
        <div class="authentication-inner w-100 mx-auto" style="max-width: 500px;">
            <!-- Register Card -->
            <div class="card shadow-sm rounded-5 bg-white bg-opacity-75">
                <div class="card-body p-5">

                    <!-- Logo -->
                    <div class="app-brand justify-content-center mb-4 text-center">
                        <span class="app-brand-text fw-bold fs-4 text-primary">Agenda</span>
                    </div>

                    <h4 class="mb-2 text-center fw-semibold">Crear Nueva Cuenta 📝</h4>
                    <p class="mb-4 text-center text-muted">Completa los datos para registrar tu cuenta</p>

                    <form id="formAuthentication" action="{{ route('register.submit') }}" method="POST" novalidate>
                        @csrf

                        {{-- Contenedor para mensajes dinámicos --}}
                        <div id="dynamic-alerts"></div>

                        {{-- Error general (solo para fallback sin JS) --}}
                        @if($errors->any())
                        <div id="register-error" class="alert alert-danger alert-dismissible fade show" role="alert">
                            <ul class="mb-0">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                        @endif

                        <div class="mb-3">
                            <label for="username" class="form-label">Usuario <span class="text-danger">*</span></label>
                            <div class="input-group input-group-merge">
                                <span class="input-group-text"><i class="bx bx-user"></i></span>
                                <input type="text" 
                                    class="form-control @error('username') is-invalid @enderror" 
                                    id="username" name="username" placeholder="Ingresa tu nombre de usuario" 
                                    value="{{ old('username') }}" 
                                    minlength="3"
                                    pattern="[a-zA-Z0-9._-]+"
                                    title="Solo letras, números, puntos, guiones y guiones bajos. Mínimo 3 caracteres."
                                    autofocus required>
                            </div>
                            <small class="text-muted">Solo letras, números, puntos (.), guiones (-) y guiones bajos (_)</small>
                            @error('username')
                                <div class="text-danger mt-1"><small>{{ $message }}</small></div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="email" class="form-label">Correo Electrónico <span class="text-danger">*</span></label>
                            <div class="input-group input-group-merge">
                                <span class="input-group-text"><i class="bx bx-envelope"></i></span>
                                <input type="email" 
                                    class="form-control @error('email') is-invalid @enderror" 
                                    id="email" name="email" placeholder="Ingresa tu correo electrónico" 
                                    value="{{ old('email') }}" required>
                            </div>
                            @error('email')
                                <div class="text-danger mt-1"><small>{{ $message }}</small></div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="area_id" class="form-label">Área <span class="text-danger">*</span></label>
                            <div class="input-group input-group-merge">
                                <span class="input-group-text"><i class="bx bx-building"></i></span>
                                <select class="form-control @error('area_id') is-invalid @enderror" id="area_id" name="area_id" required>
                                    <option value="">Selecciona tu área</option>
                                    @foreach($areas as $area)
                                        <option value="{{ $area->id }}" {{ old('area_id') == $area->id ? 'selected' : '' }}>
                                            {{ $area->area }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            @error('area_id')
                                <div class="text-danger mt-1"><small>{{ $message }}</small></div>
                            @enderror
                        </div>

                        <!-- Honeypot field (hidden from users, visible to bots) -->
                        <div style="position: absolute; left: -9999px; top: -9999px;">
                            <input type="text" name="website" value="" autocomplete="off" tabindex="-1">
                        </div>

                        <div class="d-grid mb-3">
                            <button class="btn btn-primary btn-lg fw-semibold" type="submit" id="submit-btn">
                                <span id="btn-text">
                                    <i class="bx bx-user-plus me-2"></i>
                                    Crear Cuenta
                                </span>
                                <span id="btn-loading" class="d-none">
                                    <span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>
                                    Creando cuenta...
                                </span>
                            </button>
                        </div>

                        <div class="text-center mb-3">
                            <small class="text-muted">
                                <i class="bx bx-info-circle me-1"></i>
                                Se enviará un enlace de verificación a tu email antes de crear la cuenta
                            </small>
                        </div>

                        <div class="text-center">
                            <p class="mb-0">¿Ya tienes una cuenta? 
                                <a href="{{ route('login') }}" class="text-primary fw-semibold">Iniciar Sesión</a>
                            </p>
                        </div>

                    </form>

                </div>
            </div>
            <!-- /Register Card -->
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('formAuthentication');
    const usernameInput = document.getElementById('username');
    const emailInput = document.getElementById('email');
    const areaSelect = document.getElementById('area_id');
    const submitBtn = document.getElementById('submit-btn');
    const btnText = document.getElementById('btn-text');
    const btnLoading = document.getElementById('btn-loading');
    const dynamicAlerts = document.getElementById('dynamic-alerts');

    // Auto-hide server errors after 5 seconds
    const errorDiv = document.getElementById('register-error');
    if (errorDiv) {
        setTimeout(() => {
            errorDiv.style.transition = "opacity 0.5s";
            errorDiv.style.opacity = 0;
            setTimeout(() => errorDiv.remove(), 500);
        }, 5000);
    }

    // Función para mostrar alertas dinámicas
    function showAlert(type, message, errors = null) {
        let alertHTML = `<div class="alert alert-${type} alert-dismissible fade show" role="alert">`;
        
        if (errors && Object.keys(errors).length > 0) {
            alertHTML += '<ul class="mb-0">';
            Object.values(errors).forEach(fieldErrors => {
                if (Array.isArray(fieldErrors)) {
                    fieldErrors.forEach(error => {
                        alertHTML += `<li>${error}</li>`;
                    });
                } else {
                    alertHTML += `<li>${fieldErrors}</li>`;
                }
            });
            alertHTML += '</ul>';
        } else {
            alertHTML += message;
        }
        
        alertHTML += '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>';
        
        dynamicAlerts.innerHTML = alertHTML;
        
        // Auto-hide después de 5 segundos
        setTimeout(() => {
            const alert = dynamicAlerts.querySelector('.alert');
            if (alert) {
                alert.style.transition = "opacity 0.5s";
                alert.style.opacity = 0;
                setTimeout(() => {
                    if (alert.parentNode) alert.remove();
                }, 500);
            }
        }, 5000);
    }

    // Función para limpiar errores de validación visual
    function clearFieldErrors() {
        [usernameInput, emailInput, areaSelect].forEach(field => {
            if (field) {
                field.classList.remove('is-invalid');
                const errorDiv = field.parentNode.parentNode.querySelector('.text-danger');
                if (errorDiv) errorDiv.remove();
            }
        });
    }

    // Función para mostrar errores en campos específicos
    function showFieldErrors(errors) {
        Object.keys(errors).forEach(fieldName => {
            const field = document.getElementById(fieldName);
            if (field) {
                field.classList.add('is-invalid');
                
                // Agregar mensaje de error debajo del campo
                const existingError = field.parentNode.parentNode.querySelector('.text-danger');
                if (existingError) existingError.remove();
                
                const errorDiv = document.createElement('div');
                errorDiv.className = 'text-danger mt-1';
                errorDiv.innerHTML = `<small>${errors[fieldName][0]}</small>`;
                field.parentNode.parentNode.appendChild(errorDiv);
            }
        });
    }

    // Validación en tiempo real para nombre de usuario
    if (usernameInput) {
        usernameInput.addEventListener('input', function() {
            const username = this.value;
            const isValid = /^[a-zA-Z0-9._-]+$/.test(username) && username.length >= 3;
            
            if (!isValid && username.length > 0) {
                this.setCustomValidity('Solo letras, números, puntos, guiones y guiones bajos. Mínimo 3 caracteres.');
                this.classList.add('is-invalid');
            } else {
                this.setCustomValidity('');
                this.classList.remove('is-invalid');
            }
        });
    }

    // Manejar envío del formulario con AJAX
    if (form) {
        form.addEventListener('submit', async function(e) {
            e.preventDefault();
            
            // Validación básica del lado del cliente
            const username = usernameInput.value;
            const email = emailInput.value;
            const areaId = areaSelect.value;
            
            if (!username || !email || !areaId) {
                showAlert('danger', 'Por favor, completa todos los campos requeridos.');
                return;
            }
            
            const usernameValid = /^[a-zA-Z0-9._-]+$/.test(username) && username.length >= 3;
            if (!usernameValid) {
                showAlert('danger', 'El nombre de usuario no es válido.');
                return;
            }

            // Mostrar estado de carga
            submitBtn.disabled = true;
            btnText.classList.add('d-none');
            btnLoading.classList.remove('d-none');
            clearFieldErrors();

            try {
                // Preparar datos del formulario
                const formData = new FormData(form);
                
                // Hacer petición AJAX
                const response = await fetch(form.action, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
                });

                const data = await response.json();

                if (response.ok && data.success) {
                    // Éxito - mostrar mensaje y redirigir
                    showAlert('success', data.message);
                    
                    // Redirigir después de un momento
                    setTimeout(() => {
                        window.location.href = data.data.redirect_url;
                    }, 1500);
                    
                } else {
                    // Error de validación o del servidor
                    if (data.errors) {
                        showFieldErrors(data.errors);
                        showAlert('danger', data.message || 'Error de validación.', data.errors);
                    } else {
                        showAlert('danger', data.message || 'Ocurrió un error inesperado.');
                    }
                }

            } catch (error) {
                console.error('Error:', error);
                showAlert('danger', 'Error de conexión. Por favor, inténtalo de nuevo.');
            } finally {
                // Restaurar estado del botón
                submitBtn.disabled = false;
                btnText.classList.remove('d-none');
                btnLoading.classList.add('d-none');
            }
        });
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

    <!-- Vendors JS -->
    <script src="{{ asset('sneat/assets/vendor/libs/formvalidation/dist/js/FormValidation.min.js') }}"></script>
    <script src="{{ asset('sneat/assets/vendor/libs/formvalidation/dist/js/plugins/Bootstrap5.min.js') }}"></script>
    <script src="{{ asset('sneat/assets/vendor/libs/formvalidation/dist/js/plugins/AutoFocus.min.js') }}"></script>

    <!-- Main JS -->
    <script src="{{ asset('sneat/assets/js/main.js') }}"></script>

    <!-- Page JS -->
    <script src="{{ asset('sneat/assets/js/pages-auth.js') }}"></script>

    
</body>
</html>
