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

                    <button type="button" class="btn btn-label-secondary account-image-reset mb-4" id="resetPhoto" disabled
                        title="No hay cambios que restablecer">
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
                    <button type="submit" class="btn btn-primary me-2" id="btnSubmit">Guardar cambios</button>
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
    <script src="{{ asset('js/jquery-confirm/jquery-confirm.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/cropperjs@1.6.2/dist/cropper.min.js"></script>
    <script src="{{ asset('js/profile/edit.js') }}"></script>
@endsection
