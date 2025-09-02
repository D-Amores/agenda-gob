/**
 * Funcionalidad fetch para la página de verificación pendiente
 */

document.addEventListener('DOMContentLoaded', function () {
    initializeVerificationPending();
});

function initializeVerificationPending() {
    const successMessage = document.getElementById('success-message');
    
    // Auto-hide success message after 8 seconds
    if (successMessage) {
        setTimeout(() => {
            successMessage.style.transition = "opacity 0.5s";
            successMessage.style.opacity = 0;
            setTimeout(() => successMessage.remove(), 500);
        }, 8000);
    }
    
    // Handle navigation buttons
    setupNavigationButtons();
    
    // Check for URL parameters that might indicate fetch flow
    checkAjaxFlow();
}

function setupNavigationButtons() {
    const loginBtn = document.querySelector('a[href*="login"]');
    const registerBtn = document.querySelector('a[href*="register"]');
    
    if (loginBtn) {
        loginBtn.addEventListener('click', handleNavigation);
    }
    
    if (registerBtn) {
        registerBtn.addEventListener('click', handleNavigation);
    }
}

function handleNavigation(e) {
    // Si estamos en flujo fetch, usar navegación normal
    if (isFetchFlow()) {
        e.preventDefault();
        const url = e.target.getAttribute('href') || e.target.closest('a').getAttribute('href');
        
        if (url) {
            // Para login y register, simplemente redirigir
            window.location.href = url;
        }
    }
    // Si no es flujo fetch, dejar que el navegador maneje la navegación normal
}

function checkAjaxFlow() {
    const urlParams = new URLSearchParams(window.location.search);
    const isFetch = urlParams.get('fetch') === '1';
    
    if (isFetch) {
        // Marcar como flujo fetch
        document.body.setAttribute('data-fetch-flow', 'true');
        
        // Opcional: Mostrar indicador visual de que es flujo fetch
        showFetchFlowIndicator();
    }
}

function isFetchFlow() {
    return document.body.getAttribute('data-fetch-flow') === 'true';
}

function showFetchFlowIndicator() {
    // Opcional: Agregar una pequeña indicación visual
    const indicator = document.createElement('div');
    indicator.className = 'badge bg-info position-absolute';
    indicator.style.cssText = 'top: 10px; right: 10px; font-size: 0.7rem; z-index: 1000;';
    indicator.textContent = 'Modo Fetch';
    document.body.appendChild(indicator);
}

/**
 * Función auxiliar para cargar la página de verificación pendiente via fetch
 * Puede ser llamada desde otros scripts
 */
window.loadVerificationPending = function(email = null) {
    return fetch(route('registrationPending'), {
        method: 'GET',
        headers: {
            'Accept': 'application/json',
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.ok) {
            // Crear modal con la información de verificación pendiente
            UI.alert({
                title: data.data.title,
                content: `
                    <div class="text-start">
                        <p class="mb-3">${data.data.description}</p>
                        
                        <div class="alert alert-info mb-3">
                            <strong>¿Qué sigue?</strong><br>
                            ${data.data.instructions.map((instruction, index) => 
                                `${index + 1}. ${instruction}`
                            ).join('<br>')}
                        </div>
                        
                        <div class="mb-3">
                            <small class="text-muted">
                                <i class="bx bx-time-five me-1"></i>
                                ${data.data.expiration_info}
                            </small>
                        </div>
                        
                        <div class="d-grid gap-2">
                            <a href="${route('login')}" class="btn btn-primary">
                                <i class="bx bx-log-in me-2"></i>
                                Ir al Login
                            </a>
                            <a href="${route('register')}" class="btn btn-outline-secondary">
                                <i class="bx bx-user-plus me-2"></i>
                                Registrar otra cuenta
                            </a>
                        </div>
                        
                        <div class="mt-3 pt-3 border-top">
                            <small class="text-muted">
                                <i class="bx bx-help-circle me-1"></i>
                                ¿No recibiste el email? Revisa tu carpeta de spam o contacta al administrador.
                            </small>
                        </div>
                    </div>
                `,
                type: 'blue',
                boxWidth: '600px',
                useBootstrap: false,
                buttons: {
                    cerrar: {
                        text: 'Cerrar',
                        btnClass: 'btn-secondary'
                    }
                }
            });
            
            return data;
        } else {
            throw new Error(data.message || 'Error al cargar información de verificación');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        UI.alert({
            title: 'Error',
            content: 'No se pudo cargar la información de verificación pendiente.',
            type: 'red'
        });
        throw error;
    });
};
