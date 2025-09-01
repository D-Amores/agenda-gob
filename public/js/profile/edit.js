document.addEventListener('DOMContentLoaded', () => {
    const input = document.getElementById('upload');
    const imgPreview = document.getElementById('uploadedAvatar');
    const resetBtn = document.getElementById('resetPhoto');
    let originalSrc = imgPreview.getAttribute('src'); // ahora let para poder actualizar tras guardar
    const form = document.getElementById('formAccountSettings');
    const submitBtn = document.getElementById('btnSubmit');

    const cropperModalEl = document.getElementById('cropperModal');
    const cropperImage = document.getElementById('cropperImage');
    const applyCropBtn = document.getElementById('applyCrop');
    let cropper = null;
    let fileSelected = false;

    // Habilita/deshabilita el botón de restablecer según cambios
    function updateResetState() {
        const changed = fileSelected || imgPreview.src !== originalSrc;
        resetBtn.disabled = !changed;
        resetBtn.classList.toggle('disabled', !changed);
        resetBtn.title = changed ? '' : 'No hay cambios que restablecer';
    }

    const modal = new bootstrap.Modal(cropperModalEl);

    // seleccionar archivo -> abrir cropper
    input.addEventListener('change', (e) => {
        const file = e.target.files && e.target.files[0];
        if (!file) return;

        fileSelected = true;
        updateResetState();

        const reader = new FileReader();
        reader.onload = () => {
            cropperImage.src = reader.result;
            modal.show();
        };
        reader.readAsDataURL(file);
    });

    cropperModalEl.addEventListener('shown.bs.modal', () => {
        if (cropper) cropper.destroy();
        cropper = new Cropper(cropperImage, {
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
    });

    applyCropBtn.addEventListener('click', () => {
        if (!cropper) return;
        const canvas = cropper.getCroppedCanvas({
            width: 300,
            height: 300,
            imageSmoothingEnabled: true,
            imageSmoothingQuality: 'high'
        });
        imgPreview.src = canvas.toDataURL('image/jpeg', 0.92);
        modal.hide();
        updateResetState();
    });

    // Restablecer solo si hay algo que restablecer
    resetBtn.addEventListener('click', (e) => {
        e.preventDefault();
        const changed = fileSelected || imgPreview.src !== originalSrc;
        if (!changed) {
            if (window.UI?.alert) UI.alert('No hay nada que restablecer.', 'orange', 'Aviso');
            return;
        }

        UI.confirm({
            title: 'Restablecer foto',
            message: '¿Deseas restablecer la foto al valor anterior?',
            type: 'orange',
            text: 'Restablecer',
            class: 'btn-warning',
            onConfirm() {
                imgPreview.src = originalSrc;
                input.value = '';
                fileSelected = false;
                if (cropper) {
                    cropper.destroy();
                    cropper = null;
                }
                updateResetState();
            }
        });
    });

    // submit AJAX con confirm
    form.addEventListener('submit', async (e) => {
        e.preventDefault();
        UI.confirm({
            title: 'Guardar cambios',
            message: '¿Deseas guardar los cambios del perfil?',
            type: 'blue',
            text: 'Guardar',
            class: 'btn-primary',
            onConfirm: async () => {
                submitBtn.disabled = true;
                submitBtn.innerText = 'Guardando...';

                const formData = new FormData(form);

                // si hubo recorte, reemplaza el archivo
                if (fileSelected && cropper) {
                    const canvas = cropper.getCroppedCanvas({
                        width: 600,
                        height: 600,
                        imageSmoothingEnabled: true,
                        imageSmoothingQuality: 'high'
                    });
                    const blob = await new Promise(resolve => canvas.toBlob(resolve,
                        'image/jpeg', 0.92));
                    formData.delete('profile_photo');
                    formData.append('profile_photo', blob, 'avatar.jpg');
                }

                UI.ajax({
                    url: form.action,
                    method: 'POST', // respeta _method=PUT
                    data: formData,
                    success: (data) => {
                        if (data && data.ok === false) {
                            UI.alert(data.message ||
                                'Ocurrió un error.', 'red', 'Error');
                            return;
                        }

                        if (data.user && data.user.avatar_url) {
                            imgPreview.src = data.user.avatar_url +
                                '?t=' + Date.now();
                            originalSrc = imgPreview.src;
                        }

                        // Mostrar alert y recargar al presionar OK
                        UI.alert(data.message ||
                            'Perfil actualizado correctamente.',
                            'green', 'Éxito', () => {
                                window.location.reload();
                            });

                        // limpiar estado de recorte
                        fileSelected = false;
                        if (cropper) {
                            cropper.destroy();
                            cropper = null;
                        }
                        input.value = '';
                        updateResetState();
                    },
                    fail: (jq) => {
                        const resp = jq.responseJSON || {};
                        // Errores de validación
                        if (jq.status === 422 && resp.errors) {
                            const errs = Object.values(resp.errors)
                                .flat().join('<br>');
                            UI.alert(errs || 'Errores de validación.',
                                'red', 'Errores');
                            return;
                        }
                        // 401/403/419/500 u otros
                        const msg = resp.message ||
                            'No se pudo actualizar el perfil. Intenta de nuevo.';
                        UI.alert(msg, 'red', 'Error');
                    },
                    always: () => {
                        submitBtn.disabled = false;
                        submitBtn.innerText = 'Guardar cambios';
                    }
                });
            }
        });
    });

    // Estado inicial
    updateResetState();
});
