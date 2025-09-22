/**
 * Funcionalidad fetch para la verificación de email
 */

/**
 * Función para verificar email via fetch
 * Puede ser llamada programáticamente o desde enlaces especiales
 */
window.verifyEmailToken = function(token) {
    if (!token) {
        UI.alert({
            title: 'Error',
            content: 'Token de verificación no válido.',
            type: 'red'
        });
        return Promise.reject('Token inválido');
    }

    // Mostrar loading
    const loadingDialog = UI.dialog({
        title: 'Verificando...',
        content: `
            <div class="text-center">
                <div class="spinner-border text-primary me-3" role="status">
                    <span class="visually-hidden">Verificando...</span>
                </div>
                <span>Verificando tu email, por favor espera...</span>
            </div>
        `,
        boxWidth: '400px',
        useBootstrap: false,
        buttons: {}
    });

    return fetch(route('registrationVerify', token), {
        method: 'GET',
        headers: {
            'Accept': 'application/json',
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
        }
    })
    .then(response => response.json())
    .then(data => {
        loadingDialog.close();
        
        if (data.ok) {
            UI.confirm({
                title: '¡Verificación Exitosa! 🎉',
                content: `
                    <div class="text-center">
                        <div class="mb-3">
                            <i class="bx bx-check-circle display-4 text-success"></i>
                        </div>
                        <p class="mb-3">${data.message}</p>
                        <div class="alert alert-success">
                            <i class="bx bx-mail-send me-2"></i>
                            <strong>¡Tu cuenta ha sido creada!</strong><br>
                            Hemos enviado tu contraseña a <strong>${data.data.email}</strong>
                        </div>
                        <p class="text-muted">
                            <i class="bx bx-info-circle me-1"></i>
                            Revisa tu bandeja de entrada para encontrar tu contraseña.
                        </p>
                    </div>
                `,
                type: 'green',
                boxWidth: '500px',
                useBootstrap: false,
                buttons: {
                    login: {
                        text: '<i class="bx bx-log-in me-2"></i>Ir al Login',
                        btnClass: 'btn-success',
                        action: function() {
                            window.location.href = data.data.redirect_url || '/login';
                        }
                    },
                    cerrar: {
                        text: 'Cerrar',
                        btnClass: 'btn-outline-secondary'
                    }
                }
            });
            
            return data;
        } else {
            // Error en la verificación
            UI.alert({
                title: 'Error de Verificación',
                content: `
                    <div class="text-center">
                        <div class="mb-3">
                            <i class="bx bx-error-circle display-4 text-danger"></i>
                        </div>
                        <p class="mb-3">${data.message}</p>
                        ${data.errors ? `
                            <div class="alert alert-danger">
                                ${Object.values(data.errors).flat().join('<br>')}
                            </div>
                        ` : ''}
                        <div class="d-grid gap-2 mt-3">
                            <a href="${route('login')}" class="btn btn-outline-secondary">
                                <i class="bx bx-log-in me-2"></i>
                                Ir al Login
                            </a>
                        </div>
                    </div>
                `,
                type: 'red',
                boxWidth: '500px',
                useBootstrap: false
            });
            
            throw new Error(data.message);
        }
    })
    .catch(error => {
        loadingDialog.close();
        
        console.error('Error en verificación:', error);
        
        UI.alert({
            title: 'Error de Conexión',
            content: `
                <div class="text-center">
                    <div class="mb-3">
                        <i class="bx bx-wifi-off display-4 text-warning"></i>
                    </div>
                    <p class="mb-3">No se pudo conectar al servidor para verificar tu email.</p>
                    <div class="alert alert-warning">
                        <i class="bx bx-info-circle me-2"></i>
                        Por favor, verifica tu conexión a internet e intenta de nuevo.
                    </div>
                    <div class="d-grid gap-2 mt-3">
                        <button class="btn btn-warning" onclick="location.reload()">
                            <i class="bx bx-refresh me-2"></i>
                            Intentar de nuevo
                        </button>
                    </div>
                </div>
            `,
            type: 'orange',
            boxWidth: '500px',
            useBootstrap: false
        });
        
        throw error;
    });
};

/**
 * Auto-inicialización si estamos en una página de verificación
 */
document.addEventListener('DOMContentLoaded', function() {
    // Verificar si estamos en una URL de verificación
    const urlPattern = /\/registration\/verify\/([a-zA-Z0-9]+)/;
    const match = window.location.pathname.match(urlPattern);
    
    if (match && match[1]) {
        const token = match[1];
        
        // Si tenemos las librerías necesarias (jquery-confirm), usar fetch
        if (typeof UI !== 'undefined' && UI.confirm) {
            // Preguntar si quiere usar verificación fetch
            UI.confirm({
                title: 'Verificación de Email',
                content: `
                    <div class="text-center">
                        <div class="mb-3">
                            <i class="bx bx-mail-send display-4 text-primary"></i>
                        </div>
                        <p>¿Quieres verificar tu email ahora?</p>
                        <small class="text-muted">
                            Se procesará tu verificación y recibirás tu contraseña por email.
                        </small>
                    </div>
                `,
                type: 'blue',
                boxWidth: '400px',
                useBootstrap: false,
                buttons: {
                    verify: {
                        text: '<i class="bx bx-check me-2"></i>Verificar Ahora',
                        btnClass: 'btn-primary',
                        action: function() {
                            verifyEmailToken(token);
                        }
                    },
                    cancel: {
                        text: 'Cancelar',
                        btnClass: 'btn-outline-secondary',
                        action: function() {
                            window.location.href = route('login');
                        }
                    }
                }
            });
        }
        // Si no hay librerías fetch, la página se comportará normalmente
    }
});
