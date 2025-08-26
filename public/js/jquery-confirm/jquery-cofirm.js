// Extensiones reutilizables
window.UI = {
    confirm(opts = {}) {
        $.confirm({
            title: opts.title || 'Confirmar',
            content: opts.message || '¿Estás seguro?',
            type: opts.type || 'blue',
            buttons: {
                cancel: {
                    text: opts.textCancel || 'Cancelar',
                    action: () => { if (typeof opts.onCancel === 'function') opts.onCancel(); }
                },
                ok: {
                    text: opts.text || 'Aceptar',
                    btnClass: opts.class || 'btn-primary',
                    action: () => { if (typeof opts.onConfirm === 'function') opts.onConfirm(); }
                }
            }
        });
    },
    alert(message, type = 'blue', title = 'Información', onOk) {
        $.alert({
            title,
            content: message,
            type,
            buttons: {
                ok: {
                    text: 'OK',
                    btnClass: 'btn-primary',
                    action: () => { if (typeof onOk === 'function') onOk(); }
                }
            }
        });
    },
    // aviso breve
    notify(message, type = 'green') {
        $.alert({
            content: message,
            type,
            backgroundDismiss: true,
            closeIcon: true,
            columnClass: 'small',
            title: false
        });
    },
    // AJAX helper con JSON por defecto
    ajax({ url, method = 'POST', data, success, fail, always }) {
        $.ajax({
            url,
            method,
            data,
            processData: false,
            contentType: false,
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        })
        .done((resp) => { if (typeof success === 'function') success(resp); })
        .fail((jq) => { if (typeof fail === 'function') fail(jq); })
        .always(() => { if (typeof always === 'function') always(); });
    }
};
