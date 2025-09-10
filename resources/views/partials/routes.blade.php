/**
 * Helper para obtener rutas de Laravel desde JavaScript
 */

// Rutas principales de la aplicación
window.Routes = {
    // Autenticación
    login: "{{ route('login') }}",
    register: "{{ route('register') }}",
    
    // Verificación de registro
    registrationPending: "{{ route('registration.pending') }}",
    registrationVerify: function(token) {
        return "{{ route('registration.verify', '__TOKEN__') }}".replace('__TOKEN__', token);
    }
};

// Función helper para acceder a las rutas
window.route = function(name, params = null) {
    if (Routes[name]) {
        if (typeof Routes[name] === 'function') {
            return Routes[name](params);
        } else {
            return Routes[name];
        }
    }
    console.error(`Route '${name}' not found`);
    return '#';
};
