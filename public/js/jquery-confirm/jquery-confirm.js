// Extensiones reutilizables
window.UI = {
    confirm(opts = {}) {
        $.confirm({
            title: opts.title || "Confirmar",
            content: opts.message || "¿Estás seguro?",
            type: opts.type || "blue",
            buttons: {
                cancel: {
                    text: opts.textCancel || "Cancelar",
                    action: () => {
                        if (typeof opts.onCancel === "function")
                            opts.onCancel();
                    },
                },
                ok: {
                    text: opts.text || "Aceptar",
                    btnClass: opts.class || "btn-primary",
                    action: () => {
                        if (typeof opts.onConfirm === "function")
                            opts.onConfirm();
                    },
                },
            },
        });
    },
    alert(message, type = "blue", title = "Información", onOk, timeout = 0) {
        const jc = $.alert({
            title,
            content: message,
            type,
            autoClose: false,
            buttons: {
                ok: {
                    text: "OK",
                    btnClass: "btn-primary",
                    action: () => {
                        if (typeof onOk === "function") onOk();
                    },
                },
            },
        });

        if (timeout && Number(timeout) > 0) {
            setTimeout(() => {
                try {
                    jc.close();
                    if (typeof onOk === "function") onOk();
                } catch (_) {}
            }, Number(timeout));
        }

        return jc;
    },
    // aviso breve
    notify(message, type = "green") {
        $.alert({
            content: message,
            type,
            backgroundDismiss: true,
            closeIcon: true,
            columnClass: "small",
            title: false,
        });
    },
    ajax({ url, method = "POST", data, success, fail, always }) {
        $.ajax({
            url,
            method,
            data,
            processData: false,
            contentType: false,
            headers: {
                "X-Requested-With": "XMLHttpRequest",
                Accept: "application/json",
            },
        })
            .done((resp) => {
                if (typeof success === "function") success(resp);
            })
            .fail((jq) => {
                if (typeof fail === "function") fail(jq);
            })
            .always(() => {
                if (typeof always === "function") always();
            });
    },
};
