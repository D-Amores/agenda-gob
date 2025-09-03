document.addEventListener('DOMContentLoaded', () => {
    // ==========================
    // Elementos del DOM
    // ==========================
    const dom = {
        inputFile: document.getElementById('upload'),
        imgPreview: document.getElementById('uploadedAvatar'),
        resetBtn: document.getElementById('resetPhoto'),
        formProfile: document.getElementById('formAccountSettings'),
        submitBtn: document.getElementById('btnSubmit'),
        cropperModalEl: document.getElementById('cropperModal'),
        cropperImage: document.getElementById('cropperImage'),
        applyCropBtn: document.getElementById('applyCrop'),
        changePasswordForm: document.getElementById('changePasswordForm'),
        changePasswordBtn: document.getElementById('changePasswordBtn'),
        passwordSpinner: document.getElementById('passwordSpinner')
    };

    // ==========================
    // Estado
    // ==========================
    let originalSrc = dom.imgPreview.getAttribute('src');
    let cropper = null;
    let fileSelected = false;
    const modal = new bootstrap.Modal(dom.cropperModalEl);

    // ==========================
    // Funciones auxiliares
    // ==========================
    const showAlert = (type, message, errors = null, timeout = 5000, onOk = null) => {
        let fullMessage = message;

        if (errors) {
            fullMessage += '<ul class="mb-0 mt-2">';
            Object.values(errors).forEach(fieldErrors => {
                if (Array.isArray(fieldErrors)) fieldErrors.forEach(e => fullMessage += `<li>${e}</li>`);
                else fullMessage += `<li>${fieldErrors}</li>`;
            });
            fullMessage += '</ul>';
        }

        const typeMap = { success: 'green', danger: 'red', warning: 'orange', info: 'blue' };
        const titleMap = { success: 'Éxito', danger: 'Error', warning: 'Advertencia', info: 'Información' };
        const alertType = typeMap[type] || 'blue';
        const title = titleMap[type] || 'Información';

        UI.alert(fullMessage, alertType, title, onOk, timeout);
    };

    const updateResetState = () => {
        const changed = fileSelected || dom.imgPreview.src !== originalSrc;
        dom.resetBtn.disabled = !changed;
        dom.resetBtn.classList.toggle('disabled', !changed);
        dom.resetBtn.title = changed ? '' : 'No hay cambios que restablecer';
    };

    const initCropper = () => {
        if (cropper) cropper.destroy();
        cropper = new Cropper(dom.cropperImage, {
            viewMode: 1,
            aspectRatio: 1,
            dragMode: 'move',
            autoCropArea: 1,
            responsive: true,
            background: false,
            movable: true,
            zoomable: true,
            rotatable: false,
            scalable: false,
            minContainerWidth: 400,
            minContainerHeight: 300
        });
    };

    const getCroppedBlob = async (width = 600, height = 600) => {
        if (!cropper) return null;
        const canvas = cropper.getCroppedCanvas({ width, height, imageSmoothingEnabled: true, imageSmoothingQuality: 'high' });
        return new Promise(resolve => canvas.toBlob(resolve, 'image/jpeg', 0.92));
    };

    const sendFormData = async (formData, form, submitBtnOverride = null) => {
        const btn = submitBtnOverride || dom.submitBtn;
        btn.disabled = true;
        try {
            const res = await fetch(form.action, {
                method: 'POST',
                body: formData,
                headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
            });
            const data = await res.json();
            if (data.ok) showAlert('success', data.message, null, 5000, () => window.location.reload());
            else showAlert('danger', data.message || 'Ocurrió un error', data.errors || null);
        } catch (err) {
            console.error(err);
            showAlert('danger', 'Error de conexión. Por favor, inténtalo de nuevo.');
        } finally {
            btn.disabled = false;
        }
    };

    // ==========================
    // Eventos
    // ==========================
    // Selección de archivo -> abrir cropper
    dom.inputFile.addEventListener('change', (e) => {
        const file = e.target.files[0];
        if (!file) return;
        fileSelected = true;
        updateResetState();
        const reader = new FileReader();
        reader.onload = () => {
            dom.cropperImage.src = reader.result;
            modal.show();
        };
        reader.readAsDataURL(file);
    });

    // Iniciar cropper al mostrar modal
    dom.cropperModalEl.addEventListener('shown.bs.modal', initCropper);

    // Aplicar recorte
    dom.applyCropBtn.addEventListener('click', async () => {
        if (!cropper) return;
        const canvas = cropper.getCroppedCanvas({ width: 300, height: 300 });
        dom.imgPreview.src = canvas.toDataURL('image/jpeg', 0.92);
        modal.hide();
        updateResetState();
    });

    // Reset foto
    dom.resetBtn.addEventListener('click', (e) => {
        e.preventDefault();
        if (!fileSelected && dom.imgPreview.src === originalSrc) return UI.alert('No hay nada que restablecer.', 'orange', 'Aviso');
        UI.confirm({
            title: 'Restablecer foto',
            message: '¿Deseas restablecer la foto al valor anterior?',
            type: 'orange',
            text: 'Restablecer',
            class: 'btn-warning',
            onConfirm() {
                dom.imgPreview.src = originalSrc;
                dom.inputFile.value = '';
                fileSelected = false;
                if (cropper) cropper.destroy();
                cropper = null;
                updateResetState();
            }
        });
    });

    // Submit perfil
    dom.formProfile.addEventListener('submit', async (e) => {
        e.preventDefault();
        UI.confirm({
            title: 'Guardar cambios',
            message: '¿Deseas guardar los cambios del perfil?',
            type: 'blue',
            text: 'Guardar',
            class: 'btn-primary',
            onConfirm: async () => {
                const formData = new FormData(dom.formProfile);
                if (fileSelected && cropper) {
                    const blob = await getCroppedBlob();
                    formData.delete('profile_photo');
                    formData.append('profile_photo', blob, 'avatar.jpg');
                }
                await sendFormData(formData, dom.formProfile);
            }
        });
    });

    // Submit contraseña
    dom.changePasswordForm?.addEventListener('submit', async (e) => {
        e.preventDefault();
        const formData = new FormData(dom.changePasswordForm);
        UI.confirm({
            title: 'Cambiar contraseña',
            message: '¿Deseas cambiar tu contraseña?',
            type: 'blue',
            text: 'Cambiar',
            class: 'btn-primary',
            onConfirm: async () => {
                dom.changePasswordBtn.disabled = true;
                dom.passwordSpinner.classList.remove('d-none');
                try {
                    await sendFormData(formData, dom.changePasswordForm, dom.changePasswordBtn);
                } finally {
                    dom.passwordSpinner.classList.add('d-none');
                }
            }
        });
    });

    // Estado inicial
    updateResetState();
});

// ==========================
// Toggle ver contraseña
// ==========================
function setupPasswordToggle(toggleId, inputId) {
    const toggle = document.getElementById(toggleId);
    const input = document.getElementById(inputId);

    if (toggle && input) {
        toggle.addEventListener('click', () => {
            const isPassword = input.type === 'password';
            input.type = isPassword ? 'text' : 'password';

            // Cambiar el ícono
            const icon = toggle.querySelector('i');
            if (icon) {
                icon.classList.toggle('bx-hide', !isPassword);
                icon.classList.toggle('bx-show', isPassword);
            }
        });
    }
}

// Inicializar toggles
setupPasswordToggle('toggleCurrentPassword', 'current_password');
setupPasswordToggle('toggleNewPassword', 'new_password');
setupPasswordToggle('toggleConfirmPassword', 'password_confirmation');
