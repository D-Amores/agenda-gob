document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('formPasswordReset');
    const emailInput = document.getElementById('email');
    const submitBtn = document.getElementById('submit-btn');
    const btnText = document.getElementById('btn-text');
    const btnLoading = document.getElementById('btn-loading');
    const dynamicAlerts = document.getElementById('dynamic-alerts');

    // Auto-hide server errors after 5 seconds
    const errorDiv = document.getElementById('reset-error');
    if (errorDiv) {
        setTimeout(() => {
            errorDiv.style.transition = "opacity 0.5s";
            errorDiv.style.opacity = 0;
            setTimeout(() => errorDiv.remove(), 500);
        }, 5000);
    }

    // Función para mostrar alertas dinámicas
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

    // Función para limpiar errores de validación visual
    function clearFieldErrors() {
        emailInput.classList.remove('is-invalid');
        const errorDiv = emailInput.parentNode.parentNode.querySelector('.text-danger');
        if (errorDiv) errorDiv.remove();
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

    // Manejar envío del formulario con AJAX
    if (form) {
        form.addEventListener('submit', async function(e) {
            e.preventDefault();
            
            // Validación básica del lado del cliente
            const email = emailInput.value;
            
            if (!email) {
                showAlert('danger', 'Por favor, ingresa tu correo electrónico.', null, 5000);
                return;
            }

            // Mostrar estado de carga
            submitBtn.disabled = true;
            btnText.classList.add('d-none');
            btnLoading.classList.remove('d-none');
            clearFieldErrors();

            try {
                const formData = new FormData(form);
                const res = await fetch(form.action, {
                    method: 'POST',
                    body: formData,
                    headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
                });

                const data = await res.json();

                if(data.ok) {
                    showAlert('success', data.message, null, 5000, () => {
                        window.location.href = data.data.redirect_url;
                    });
                }else{
                    if (data.errors) {
                        showAlert('danger', data.message || 'Error de validación.', data.errors);
                    } else {
                        showAlert('danger', data.message || 'Ocurrió un error inesperado.');
                    }
                }
            } catch (error) {
                console.error('Error:', error);
                showAlert('danger', 'Error de conexión. Por favor, inténtalo de nuevo.', null, 5000);
            } finally {
                // Restaurar estado del botón
                submitBtn.disabled = false;
                btnText.classList.remove('d-none');
                btnLoading.classList.add('d-none');
            }
        });
    }
});