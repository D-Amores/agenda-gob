document.addEventListener('DOMContentLoaded', function () {
    const form = document.querySelector('#formAuthentication');
    const usernameInput = document.querySelector('#username');
    const emailInput = document.querySelector('#email');
    const areaSelect = document.querySelector('#area_id');

    // Auto-hide server errors
    const errorDiv = document.getElementById('register-error');
    if (errorDiv) setTimeout(() => errorDiv.remove(), 5000);

    // Función para mostrar alertas usando UI.alert
    function showAlert(type, message, errors = null, timeout = 5000, onOk = null) {
        let fullMessage = message;

        if (errors && Object.keys(errors).length > 0) {
            fullMessage += '<ul class="mb-0 mt-2">';
            Object.values(errors).forEach(fieldErrors => {
                if (Array.isArray(fieldErrors)) {
                    fieldErrors.forEach(error => fullMessage += `<li>${error}</li>`);
                } else {
                    fullMessage += `<li>${fieldErrors}</li>`;
                }
            });
            fullMessage += '</ul>';
        }

        const typeMap = { 'success': 'green', 'danger': 'red', 'warning': 'orange', 'info': 'blue' };
        const alertType = typeMap[type] || 'blue';
        const titleMap = { 'success': 'Éxito', 'danger': 'Error', 'warning': 'Advertencia', 'info': 'Información' };
        const title = titleMap[type] || 'Información';

        UI.alert(fullMessage, alertType, title, onOk, timeout);
    }

    // Limpiar errores visuales
    function clearFieldErrors() {
        [usernameInput, emailInput, areaSelect].forEach(field => {
            if (field) {
                field.classList.remove('is-invalid');
                const errorDiv = field.parentNode.parentNode.querySelector('.text-danger');
                if (errorDiv) errorDiv.remove();
            }
        });
    }

    // Mostrar errores de campos específicos
    function showFieldErrors(errors) {
        Object.keys(errors).forEach(fieldName => {
            const field = document.getElementById(fieldName);
            if (field) {
                field.classList.add('is-invalid');

                const existingError = field.parentNode.parentNode.querySelector('.text-danger');
                if (existingError) existingError.remove();

                const errorDiv = document.createElement('div');
                errorDiv.className = 'text-danger mt-1';
                errorDiv.innerHTML = `<small>${errors[fieldName][0]}</small>`;
                field.parentNode.parentNode.appendChild(errorDiv);
            }
        });
    }

    // Validación en tiempo real del username
    if (usernameInput) {
        usernameInput.addEventListener('input', function() {
            const username = this.value;
            const valid = /^[a-zA-Z0-9._-]{3,}$/.test(username);
            this.classList.toggle('is-invalid', !valid && username.length > 0);
        });
    }

    // Envío de formulario con AJAX
    if (form) {
        form.addEventListener("submit", function (e) {
            e.preventDefault();
            clearFieldErrors();

            const username = usernameInput?.value;
            const email = emailInput?.value;
            const areaId = areaSelect?.value;

            if (!username || !email || !areaId) {
                showAlert('danger', 'Por favor, completa todos los campos requeridos.');
                return;
            }

            if (!/^[a-zA-Z0-9._-]{3,}$/.test(username)) {
                showAlert('danger', 'El nombre de usuario no es válido.');
                return;
            }

            const formData = new FormData(form);

            UI.confirm({
                title: "Confirmar registro",
                message: "¿Deseas continuar con el registro?",
                onConfirm: () => sendRegistration(form, formData)
            });
        });
    }

    // Enviar registro
    function sendRegistration(form, formData) {
        const submitBtn = document.querySelector('#submit-btn');
        const btnText = document.querySelector('#btn-text');
        const btnLoading = document.querySelector('#btn-loading');

        if (submitBtn) {
            submitBtn.disabled = true;
            btnText?.classList.add('d-none');
            btnLoading?.classList.remove('d-none');
        }

        fetch(form.action, {
            method: 'POST',
            body: formData,
            headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
        })
        .then(res => res.json())
        .then(data => {
            if (data.ok) {
                showAlert('success', data.message, null, 5000, () => {
                    window.location.href = data.data.redirect_url;
                });
            } else {
                if (data.errors) {
                    showFieldErrors(data.errors);
                    showAlert('danger', data.message || 'Error de validación.', data.errors);
                } else {
                    showAlert('danger', data.message || 'Ocurrió un error inesperado.');
                }
            }
        })
        .catch(() => showAlert('danger', 'Error de conexión. Por favor, inténtalo de nuevo.'))
        .finally(() => {
            if (submitBtn) {
                submitBtn.disabled = false;
                btnText?.classList.remove('d-none');
                btnLoading?.classList.add('d-none');
            }
        });
    }
});
