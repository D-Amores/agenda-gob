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
    // Agrega timeout (ms). Por defecto 2000. Si timeout = 0, no se autocierra.
    alert(message, type = 'blue', title = 'Información', onOk, timeout = 1000) {
        let autoCloseTimer = null;

        const jc = $.alert({
            title,
            content: message,
            type,
            buttons: {
                ok: {
                    text: 'OK',
                    btnClass: 'btn-primary',
                    action: () => {
                        if (autoCloseTimer) {
                            clearTimeout(autoCloseTimer);
                            autoCloseTimer = null;
                        }
                        if (typeof onOk === 'function') onOk();
                    }
                }
            }
        });

        if (timeout && Number(timeout) > 0) {
            autoCloseTimer = setTimeout(() => {
                try { jc.close(); } catch (_) {}
            }, Number(timeout));
        }

        return jc;
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
