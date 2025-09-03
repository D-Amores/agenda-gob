@extends ('layouts.app')

@section('title')
    Editar Perfil
@endsection

@section('link')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/cropperjs@1.6.2/dist/cropper.min.css">
@endsection

@section('content')

<div class="row">
    <!-- Columna de tabs -->
    <div class="col-md-3">
        <div class="nav flex-column nav-pills bg-light p-3 rounded" id="profileTabs" role="tablist" aria-orientation="vertical">
            <button class="nav-link active" id="profile-tab" data-bs-toggle="pill" data-bs-target="#profile-info" type="button" role="tab" aria-controls="profile-info" aria-selected="true">
                <i class="bx bx-user me-1"></i> Información Personal
            </button>
            <button class="nav-link" id="security-tab" data-bs-toggle="pill" data-bs-target="#security-settings" type="button" role="tab" aria-controls="security-settings" aria-selected="false">
                <i class="bx bx-lock me-1"></i> Seguridad
            </button>
        </div>
    </div>

    <!-- Contenido de tabs -->
    <div class="col-md-9">
        <div class="tab-content bg-light p-4 rounded" id="profileTabsContent">
                <!-- Profile Information Tab -->
                        <div class="tab-pane fade show active" id="profile-info" role="tabpanel" aria-labelledby="profile-tab">
                            <div class="row">
                                <!-- Avatar Section -->
                                <div class="col-12 mb-4">
                                    <div class="d-flex align-items-start align-items-sm-center gap-4 p-3 border rounded">
                                        <img src="{{ $user->avatar_url }}" alt="user-avatar" class="d-block rounded shadow-sm" 
                                             height="120" width="120" id="uploadedAvatar" />
                                        <div class="button-wrapper">
                                            <h6 class="mb-2">Foto de Perfil</h6>
                                            <p class="text-muted mb-3">Sube una nueva foto de perfil. Se recomienda una imagen cuadrada de al menos 300x300 píxeles.</p>
                                            <label for="upload" class="btn btn-primary me-2 mb-2" tabindex="0">
                                                <span class="d-none d-sm-block">
                                                    <i class="bx bx-upload me-1"></i>
                                                    Subir nueva foto
                                                </span>
                                                <i class="bx bx-upload d-block d-sm-none"></i>
                                            </label>
                                            <input type="file" id="upload" name="profile_photo" class="account-file-input d-none"
                                                accept="image/png, image/jpeg, image/jpg, image/gif" form="formAccountSettings" />

                                            <button type="button" class="btn btn-outline-secondary mb-2" id="resetPhoto">
                                                <i class="bx bx-reset me-1"></i>
                                                <span class="d-none d-sm-inline-block">Reset</span>
                                            </button>

                                            <p class="text-muted mb-0">Permitido JPG, GIF o PNG. Tamaño máximo de 800K</p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Profile Form -->
                            <form id="formAccountSettings" method="POST" action="{{ route('profile.update', $user) }}" enctype="multipart/form-data">
                                @csrf
                                @method('patch')
                                
                                <div class="row">
                                    <div class="mb-3 col-md-6">
                                        <label for="name" class="form-label">
                                            <i class="bx bx-user me-1"></i>
                                            Usuario
                                        </label>
                                        <input class="form-control" type="text" id="name" name="username" 
                                               value="{{ old('name', $user->username) }}" required />
                                    </div>
                                    <div class="mb-3 col-md-6">
                                        <label for="email" class="form-label">
                                            <i class="bx bx-envelope me-1"></i>
                                            Correo Electrónico
                                        </label>
                                        <input class="form-control" type="email" id="email" name="email" 
                                               value="{{ old('email', $user->email) }}" readonly />
                                    </div>
                                    <div class="mb-3 col-md-6">
                                        <label for="area_id" class="form-label">
                                            <i class="bx bx-buildings me-1"></i>
                                            Área de Trabajo
                                        </label>
                                        <input type="text" id="area" name="area" class="form-control" value="{{ old('area', $user->area->area ?? '') }}" readonly />
                                    </div>
                                </div>
                                
                                <div class="mt-4">
                                    <button type="submit" id="btnSubmit" class="btn btn-primary me-2">
                                        <i class="bx bx-check me-1"></i>
                                        Guardar Cambios
                                    </button>
                                    <a href="{{ route('dashboard')}}" class="btn btn-outline-secondary">
                                        <i class="bx bx-reset me-1"></i>
                                        Cancelar
                                    </a>
                                </div>
                            </form>
                        </div>
                <!-- Security Settings Tab -->
                        <div class="tab-pane fade" id="security-settings" role="tabpanel" aria-labelledby="security-tab">
                            <!-- Password Change Alert -->
                            <div id="passwordAlert" class="alert d-none" role="alert">
                                <span id="passwordAlertText"></span>
                            </div>

                            <div class="row">
                                <div class="col-12">
                                    <h6 class="mb-3">
                                        <i class="bx bx-shield-quarter me-2"></i>
                                        Cambiar Contraseña
                                    </h6>
                                    <p class="text-muted mb-4">
                                        Para tu seguridad, recomendamos que uses una contraseña segura y única que no uses en otros sitios web.
                                    </p>
                                </div>
                            </div>

                            <form id="changePasswordForm" action="{{ route('profile.change-password', $user) }}" method="POST">
                                @csrf
                                @method('PUT')
                                <div class="row">
                                    <div class="mb-3 col-md-6">
                                        <label for="current_password" class="form-label">
                                            <i class="bx bx-lock me-1"></i>
                                            Contraseña Actual
                                        </label>
                                        <div class="input-group input-group-merge">
                                            <input class="form-control" type="password" id="current_password" name="current_password" 
                                                   placeholder="Ingresa tu contraseña actual" required />
                                            <span class="input-group-text cursor-pointer" id="toggleCurrentPassword">
                                                <i class="bx bx-hide"></i>
                                            </span>
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-6">
                                        <!-- Espacio para equilibrar el diseño -->
                                    </div>
                                    
                                    <div class="mb-3 col-md-6">
                                        <label for="new_password" class="form-label">
                                            <i class="bx bx-key me-1"></i>
                                            Nueva Contraseña
                                        </label>
                                        <div class="input-group input-group-merge">
                                            <input class="form-control" type="password" id="new_password" name="new_password" 
                                                   placeholder="Ingresa tu nueva contraseña" required />
                                            <span class="input-group-text cursor-pointer" id="toggleNewPassword">
                                                <i class="bx bx-hide"></i>
                                            </span>
                                        </div>
                                        <div class="form-text">
                                            La contraseña debe tener al menos 8 caracteres e incluir letras, números y símbolos.
                                        </div>
                                    </div>
                                    
                                    <div class="mb-3 col-md-6">
                                        <label for="password_confirmation" class="form-label">
                                            <i class="bx bx-check-shield me-1"></i>
                                            Confirmar Nueva Contraseña
                                        </label>
                                        <div class="input-group input-group-merge">
                                            <input class="form-control" type="password" id="password_confirmation" name="new_password_confirmation" 
                                                   placeholder="Confirma tu nueva contraseña" required />
                                            <span class="input-group-text cursor-pointer" id="toggleConfirmPassword">
                                                <i class="bx bx-hide"></i>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="mt-4">
                                    <button type="submit" class="btn btn-primary me-2" id="changePasswordBtn">
                                        <span class="spinner-border spinner-border-sm me-2 d-none" id="passwordSpinner"></span>
                                        <i class="bx bx-key me-1"></i>
                                        Cambiar Contraseña
                                    </button>
                                    <button type="reset" class="btn btn-outline-secondary">
                                        <i class="bx bx-reset me-1"></i>
                                        Limpiar
                                    </button>
                                </div>
                            </form>

                            <!-- Security Tips -->
                            <div class="mt-5">
                                <div class="card border-info">
                                    <div class="card-body">
                                        <h6 class="card-title">
                                            <i class="bx bx-info-circle me-2 text-info"></i>
                                            Consejos de Seguridad
                                        </h6>
                                        <ul class="mb-0 text-muted">
                                            <li>Usa una contraseña única que no hayas usado en otros sitios</li>
                                            <li>Incluye una mezcla de letras mayúsculas, minúsculas, números y símbolos</li>
                                            <li>Evita usar información personal como fechas de nacimiento o nombres</li>
                                            <li>Considera usar un administrador de contraseñas</li>
                                            <li>Cambia tu contraseña regularmente</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
        </div>
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
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('js/jquery-confirm/jquery-confirm.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/cropperjs@1.6.2/dist/cropper.min.js"></script>
    <script src="{{ asset('js/profile/edit.js') }}"></script>
@endsection
