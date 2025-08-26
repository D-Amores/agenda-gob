@extends ('layouts.app')

@section('title')
    Editar Perfil
@endsection

@section('link')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/cropperjs@1.6.2/dist/cropper.min.css">
@endsection

@section('content')
    <div class="card mb-4">
        <h5 class="card-header">Detalles del perfil</h5>

        <div class="card-body">
            @if (session('status'))
                <div class="alert alert-success">{{ session('status') }}</div>
            @endif

            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="d-flex align-items-start align-items-sm-center gap-4">
                <img src="{{ $user->avatar_url }}" alt="user-avatar" class="d-block rounded" height="100" width="100"
                    id="uploadedAvatar" />
                <div class="button-wrapper">
                    <label for="upload" class="btn btn-primary me-2 mb-4" tabindex="0">
                        <span class="d-none d-sm-block">Subir nueva foto</span>
                        <i class="bx bx-upload d-block d-sm-none"></i>
                    </label>
                    <input type="file" id="upload" name="profile_photo" class="account-file-input d-none"
                        accept="image/png, image/jpeg, image/jpg, image/gif" form="formAccountSettings" />

                    <button type="button" class="btn btn-label-secondary account-image-reset mb-4" id="resetPhoto">
                        <i class="bx bx-reset d-block d-sm-none"></i>
                        <span class="d-none d-sm-block">Restablecer</span>
                    </button>

                    <p class="text-muted mb-0">Formatos permitidos: JPG, GIF o PNG. Máx 800 KB</p>
                </div>
            </div>
        </div>

        <hr class="my-0">

        <div class="card-body">
            <form id="formAccountSettings" method="POST" action="{{ route('profile.update', $user) }}"
                enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="row">
                    <div class="mb-3 col-md-6">
                        <label for="username" class="form-label">Usuario</label>
                        <input class="form-control" type="text" id="username" name="username"
                            value="{{ old('username', $user->username) }}" required />
                    </div>

                    <div class="mb-3 col-md-6">
                        <label for="email" class="form-label">Correo</label>
                        <input class="form-control" type="email" id="email" name="email"
                            value="{{ old('email', $user->email) }}" required />
                    </div>
                </div>

                <div class="mt-2 d-flex gap-2">
                    <button type="submit" class="btn btn-primary me-2">Guardar cambios</button>
                    <a href="{{ url()->previous() }}" class="btn btn-label-secondary">Cancelar</a>
                </div>
            </form>
        </div>
    </div>

    {{-- Modal de recorte --}}
    <div class="modal fade" id="cropperModal" tabindex="-1" aria-labelledby="cropperModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="cropperModalLabel">Recortar imagen</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body">
                    <div style="max-height:60vh; width:100%; overflow:hidden;">
                        <img id="cropperImage" src="" style="max-width:100%; display:block;">
                    </div>
                </div>
                <div class="modal-footer">
                    <button id="applyCrop" type="button" class="btn btn-primary">Aplicar recorte</button>
                    <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Cancelar</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script src="https://cdn.jsdelivr.net/npm/cropperjs@1.6.2/dist/cropper.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const input = document.getElementById('upload');
            const imgPreview = document.getElementById('uploadedAvatar');
            const resetBtn = document.getElementById('resetPhoto');
            const originalSrc = imgPreview.getAttribute('src');
            const form = document.getElementById('formAccountSettings');

            const cropperModalEl = document.getElementById('cropperModal');
            const cropperImage = document.getElementById('cropperImage');
            const applyCropBtn = document.getElementById('applyCrop');
            let cropper = null;
            let fileSelected = false;

            // Bootstrap modal instance
            const modal = new bootstrap.Modal(cropperModalEl);

            // Al seleccionar archivo, abrir modal y cargar Cropper
            input.addEventListener('change', (e) => {
                const file = e.target.files && e.target.files[0];
                if (!file) return;

                fileSelected = true;
                const reader = new FileReader();
                reader.onload = () => {
                    cropperImage.src = reader.result;
                    modal.show();
                };
                reader.readAsDataURL(file);
            });

            // Iniciar/detener Cropper en show/hide del modal
            cropperModalEl.addEventListener('shown.bs.modal', () => {
                if (cropper) cropper.destroy();
                cropper = new Cropper(cropperImage, {
                    viewMode: 1,
                    aspectRatio: 1, // cuadrado
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

            cropperModalEl.addEventListener('hidden.bs.modal', () => {
                // no destruimos aquí para permitir aplicar si se reabre
            });

            // Aplicar recorte: actualizar preview y cerrar modal
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
            });

            // Reset preview e input
            resetBtn.addEventListener('click', () => {
                imgPreview.src = originalSrc;
                input.value = '';
                fileSelected = false;
                if (cropper) {
                    cropper.destroy();
                    cropper = null;
                }
            });

            // Interceptar submit: si hay imagen seleccionada, enviar recorte como Blob por fetch
            form.addEventListener('submit', async (e) => {
                if (!fileSelected || !cropper) {
                    // si no se cambió la imagen, submit normal
                    return true;
                }

                e.preventDefault();

                // Obtener blob del recorte
                const canvas = cropper.getCroppedCanvas({
                    width: 600,
                    height: 600,
                    imageSmoothingEnabled: true,
                    imageSmoothingQuality: 'high'
                });

                const blob = await new Promise(resolve => canvas.toBlob(resolve, 'image/jpeg', 0.92));

                // Construir FormData desde el form (incluye _token y _method)
                const formData = new FormData(form);
                // Reemplazar el archivo original por el recortado
                formData.delete('profile_photo');
                formData.append('profile_photo', blob, 'avatar.jpg');

                try {
                    const resp = await fetch(form.action, {
                        method: 'POST', // Laravel respetará _method=PUT
                        body: formData,
                        headers: {
                            // no seteamos Content-Type para que el navegador ponga boundary correcto
                        },
                        credentials: 'same-origin'
                    });

                    if (resp.ok) {
                        // recargar para ver cambios y flash message
                        window.location.reload();
                    } else {
                        // si el servidor devuelve validación, intentamos mostrar texto
                        const text = await resp.text();
                        console.error('Error al actualizar perfil', text);
                        alert('No se pudo actualizar el perfil. Revisa los campos.');
                    }
                } catch (err) {
                    console.error(err);
                    alert('Error de red al enviar el formulario.');
                }
            });
        });
    </script>
@endsection
