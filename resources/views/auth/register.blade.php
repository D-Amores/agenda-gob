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

                        {{-- Error general --}}
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

                        <div class="mb-3 form-password-toggle">
                            <label class="form-label" for="password">Contraseña <span class="text-danger">*</span></label>
                            <div class="input-group input-group-merge">
                                <span class="input-group-text"><i class="bx bx-lock"></i></span>
                                <input type="password" 
                                    class="form-control @error('password') is-invalid @enderror" 
                                    id="password" name="password" 
                                    placeholder="••••••••••••" 
                                    minlength="8"
                                    pattern="^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$"
                                    title="Mínimo 8 caracteres, debe incluir: mayúscula, minúscula, número y carácter especial"
                                    aria-describedby="password" required />
                                <span class="input-group-text cursor-pointer"><i class="bx bx-hide"></i></span>
                            </div>
                            <div class="mt-2">
                                <small class="text-muted">
                                    La contraseña debe contener:
                                    <ul class="mb-0 mt-1" style="font-size: 0.75rem;">
                                        <li>Al menos 8 caracteres</li>
                                        <li>Una letra mayúscula (A-Z)</li>
                                        <li>Una letra minúscula (a-z)</li>
                                        <li>Un número (0-9)</li>
                                        <li>Un carácter especial (@$!%*?&)</li>
                                    </ul>
                                </small>
                            </div>
                            @error('password')
                                <div class="text-danger mt-1"><small>{{ $message }}</small></div>
                            @enderror
                        </div>

                        <div class="mb-4 form-password-toggle">
                            <label class="form-label" for="password_confirmation">Confirmar Contraseña <span class="text-danger">*</span></label>
                            <div class="input-group input-group-merge">
                                <span class="input-group-text"><i class="bx bx-lock"></i></span>
                                <input type="password" 
                                    class="form-control @error('password_confirmation') is-invalid @enderror" 
                                    id="password_confirmation" name="password_confirmation" 
                                    placeholder="••••••••••••" 
                                    minlength="8"
                                    aria-describedby="password_confirmation" required />
                                <span class="input-group-text cursor-pointer"><i class="bx bx-hide"></i></span>
                            </div>
                            <small class="text-muted">Repite la misma contraseña</small>
                            @error('password_confirmation')
                                <div class="text-danger mt-1"><small>{{ $message }}</small></div>
                            @enderror
                        </div>

                        <!-- Honeypot field (hidden from users, visible to bots) -->
                        <div style="position: absolute; left: -9999px; top: -9999px;">
                            <input type="text" name="website" value="" autocomplete="off" tabindex="-1">
                        </div>

                        <div class="d-grid mb-3">
                            <button class="btn btn-primary btn-lg fw-semibold" type="submit">
                                <i class="bx bx-user-plus me-2"></i>
                                Crear Cuenta
                            </button>
                        </div>

                        <div class="text-center mb-3">
                            <small class="text-muted">
                                <i class="bx bx-info-circle me-1"></i>
                                Después del registro, recibirás un email de verificación
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
    const errorDiv = document.getElementById('register-error');
    if (errorDiv) {
        setTimeout(() => {
            errorDiv.style.transition = "opacity 0.5s";
            errorDiv.style.opacity = 0;
            setTimeout(() => errorDiv.remove(), 500);
        }, 5000);
    }

    // Validación en tiempo real para contraseña
    const passwordInput = document.getElementById('password');
    const confirmPasswordInput = document.getElementById('password_confirmation');
    const usernameInput = document.getElementById('username');
    const form = document.getElementById('formAuthentication');

    // Función para validar contraseña
    function validatePassword(password) {
        const minLength = password.length >= 8;
        const hasUpper = /[A-Z]/.test(password);
        const hasLower = /[a-z]/.test(password);
        const hasNumber = /\d/.test(password);
        const hasSpecial = /[@$!%*?&]/.test(password);
        
        return {
            minLength,
            hasUpper,
            hasLower,
            hasNumber,
            hasSpecial,
            isValid: minLength && hasUpper && hasLower && hasNumber && hasSpecial
        };
    }

    // Función para mostrar fortaleza de contraseña
    function updatePasswordStrength(password) {
        const validation = validatePassword(password);
        const strengthBar = document.getElementById('password-strength');
        
        if (!strengthBar) return;

        let strength = 0;
        if (validation.minLength) strength++;
        if (validation.hasUpper) strength++;
        if (validation.hasLower) strength++;
        if (validation.hasNumber) strength++;
        if (validation.hasSpecial) strength++;

        strengthBar.className = 'progress-bar';
        strengthBar.style.width = (strength * 20) + '%';
        
        if (strength <= 2) {
            strengthBar.classList.add('bg-danger');
            strengthBar.textContent = 'Débil';
        } else if (strength <= 3) {
            strengthBar.classList.add('bg-warning');
            strengthBar.textContent = 'Regular';
        } else if (strength <= 4) {
            strengthBar.classList.add('bg-info');
            strengthBar.textContent = 'Buena';
        } else {
            strengthBar.classList.add('bg-success');
            strengthBar.textContent = 'Fuerte';
        }
    }

    // Agregar indicador de fortaleza después del campo de contraseña
    if (passwordInput) {
        const strengthIndicator = document.createElement('div');
        strengthIndicator.innerHTML = `
            <div class="progress mt-2" style="height: 5px;">
                <div id="password-strength" class="progress-bar" role="progressbar" style="width: 0%"></div>
            </div>
        `;
        passwordInput.closest('.mb-3').appendChild(strengthIndicator);

        passwordInput.addEventListener('input', function() {
            updatePasswordStrength(this.value);
        });
    }

    // Validar confirmación de contraseña
    if (confirmPasswordInput && passwordInput) {
        confirmPasswordInput.addEventListener('input', function() {
            if (this.value !== passwordInput.value) {
                this.setCustomValidity('Las contraseñas no coinciden');
                this.classList.add('is-invalid');
            } else {
                this.setCustomValidity('');
                this.classList.remove('is-invalid');
            }
        });
    }

    // Validar nombre de usuario
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

    // Validación final del formulario
    if (form) {
        form.addEventListener('submit', function(e) {
            const password = passwordInput.value;
            const confirmPassword = confirmPasswordInput.value;
            const username = usernameInput.value;

            const passwordValidation = validatePassword(password);
            const usernameValid = /^[a-zA-Z0-9._-]+$/.test(username) && username.length >= 3;

            if (!passwordValidation.isValid) {
                e.preventDefault();
                alert('La contraseña no cumple con los requisitos de seguridad.');
                return false;
            }

            if (password !== confirmPassword) {
                e.preventDefault();
                alert('Las contraseñas no coinciden.');
                return false;
            }

            if (!usernameValid) {
                e.preventDefault();
                alert('El nombre de usuario no es válido.');
                return false;
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
