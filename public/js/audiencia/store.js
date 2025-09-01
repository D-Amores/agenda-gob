document.addEventListener("DOMContentLoaded", function () {
    const wizardEl = document.querySelector("#wizard-validation");
    const stepper = new Stepper(wizardEl, {
        linear: true,
        animation: true,
    });

    const form = document.querySelector("#wizard-validation-form");
    const steps = form.querySelectorAll(".content");
    const nextButtons = form.querySelectorAll(".btn-next:not(.btn-submit)");
    const prevButtons = form.querySelectorAll(".btn-prev");
    const submitButton = form.querySelector(".btn-submit");

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

    nextButtons.forEach((button) => {
        button.addEventListener("click", function () {
            const currentStep = form.querySelector(".content.active");

            if (validateStep(currentStep)) {
                stepper.next();
            }
        });
    });

    prevButtons.forEach((button) => {
        button.addEventListener("click", function () {
            stepper.previous();
        });
    });

    form.addEventListener("submit", (e) => {
        e.preventDefault();
        const currentStep = form.querySelector(".content.active");

        if (!validateStep(currentStep)) return;

        UI.confirm({
            title: "Guardar audiencia",
            message: "¿Deseas registrar la audiencia?",
            type: "blue",
            text: "Guardar",
            class: "btn-primary",
            onConfirm: () => {
                const originalText = submitButton.innerHTML;
                submitButton.disabled = true;
                submitButton.innerHTML = "Enviando...";
                UI.ajax({
                    url: form.action,
                    method: "POST",
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
                                data.message || "Registro creado con exito.",
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
                        const msg =
                            resp.message ||
                            "No se pudo crear el registro. Intenta de nuevo.";
                        // Errores de validación
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
                                msg || "Errores de duplicado.",
                                "red",
                                "Errores",
                                null,
                                4000
                            );
                            return;
                        }
                        // 401/403/419/500 u otros
                        UI.alert(msg, "red", "Error");
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
