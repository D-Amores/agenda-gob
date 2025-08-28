document.addEventListener("DOMContentLoaded", function () {
    const form = document.querySelector("#updateEventoForm");
    const submitButton = form.querySelector("#btnSubmit");

    function validateStep(stepElement) {
        const inputs = stepElement.querySelectorAll("input, select, textarea");
        let valid = true;

        inputs.forEach((input) => {
            input.classList.remove("is-invalid");
            const value = input.value.trim();
            const feedback = input.nextElementSibling;
            // Validar campos requeridos
            if (input.hasAttribute("required") && !value) {
                input.classList.add("is-invalid");
                if (
                    feedback &&
                    feedback.classList.contains("invalid-feedback")
                ) {
                    feedback.textContent = "Este campo es obligatorio.";
                }
                valid = false;
                return;
            }
            // Validar minlength si aplica
            const minlength = input.getAttribute("minlength");
            if (minlength && value.length < parseInt(minlength)) {
                input.classList.add("is-invalid");
                if (
                    feedback &&
                    feedback.classList.contains("invalid-feedback")
                ) {
                    feedback.textContent = `Debe tener al menos ${minlength} caracteres.`;
                }
                valid = false;
            }
        });
        return valid;
    }

    form.addEventListener("submit", (event) => {
        event.preventDefault();
        if (!validateStep(form)) {
            return;
        }

        UI.confirm({
            title: "Actualizar evento",
            message: "¿Deseas actualizar el evento?",
            type: "blue",
            text: "Actualizar",
            class: "btn-primary",
            onConfirm: () => {
                const originalText = submitButton.innerHTML;
                submitButton.disabled = true;
                submitButton.innerHTML = "Enviando...";
                UI.ajax({
                    url: form.action,
                    method: "POST", //lo trata como put
                    data: new FormData(form), // incluye @csrf
                    success: (data) => {
                        if (data) {
                            if (data.ok === false) {
                                UI.alert(
                                    data.message || "Ocurrió un error.",
                                    "red",
                                    "Error",
                                    null,
                                    4000
                                );
                                return;
                            }
                            UI.alert(
                                data.message || "Registro actualizado con exito.",
                                "green",
                                "Éxito",
                                () => {
                                    window.location = window.routes.calendarioIndex;
                                },
                                4000
                            );
                        }
                    },
                    fail: (jq) => {
                        const resp = jq.responseJSON || {};
                        const msg = resp.message || "No se pudo crear el registro. Intenta de nuevo.";
                        // Errores de validación o duplicado
                        if (jq.status === 422) {
                            if (resp.errors) {
                                const errs = Object.values(resp.errors).flat().join("<br>");
                                UI.alert(
                                    errs || "Errores de validación.",
                                    "red",
                                    "Errores",
                                    null,
                                    4000
                                );
                                return;
                            }
                            UI.alert(
                                msg || "Error de duplicado.",
                                "red",
                                "Errores",
                                null,
                                4000
                            );
                            return;
                        }
                        // 401/403/419/500 u otros
                        UI.alert(msg, "red", "Error", null, 3000);
                    },
                    always: () => {
                        submitButton.disabled = false;
                        submitButton.innerHTML = originalText;
                    },
                });
            },
        });
    });
});
