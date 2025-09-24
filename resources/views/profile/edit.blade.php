@extends ('layouts.app')

@section('title')
    Editar Perfil
@endsection

@section('link')
    <link rel="stylesheet" href="{{ asset('sneat/assets/vendor/libs/cropperjs/cropper.min.css') }}">
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
            <button class="nav-link" id="telegram-tab" data-bs-toggle="pill" data-bs-target="#telegram-settings" type="button" role="tab" aria-controls="telegram-settings" aria-selected="false">
                <i class="bx bxl-telegram me-1"></i> Notificaciones Telegram
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
                                    <div class="d-flex flex-column flex-sm-row align-items-center align-items-sm-start justify-content-center justify-content-sm-start gap-4 p-3 border rounded">
                                        <img src="{{ $user->avatar_url }}" alt="user-avatar"
                                            class="d-block rounded shadow-sm w-50 w-sm-auto"
                                            style="max-width: 120px; height: auto;" id="uploadedAvatar" />

                                        <div class="button-wrapper text-center text-sm-start">
                                            <h6 class="mb-2">Foto de Perfil</h6>
                                            <p class="text-muted mb-3">
                                                Sube una nueva foto de perfil. Se recomienda una imagen cuadrada de al menos 300x300 píxeles.
                                            </p>

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

                                <div class="mt-4 d-flex flex-column flex-sm-row gap-2">
                                    <button type="submit" id="btnSubmit" class="btn btn-primary">
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

                                <div class="mt-4 d-flex flex-column flex-sm-row gap-2">
                                    <button type="submit" class="btn btn-primary" id="changePasswordBtn">
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

                        <!-- Telegram Settings Tab -->
                        <div class="tab-pane fade" id="telegram-settings" role="tabpanel" aria-labelledby="telegram-tab">
                            <div class="row">
                                <div class="col-12">
                                    <div class="card border-primary">
                                        <div class="card-header">
                                            <h5 class="card-title mb-0">
                                                <i class="bx bxl-telegram me-2 text-primary"></i>
                                                Configurar Notificaciones de Telegram
                                            </h5>
                                        </div>
                                        <div class="card-body">
                                            <!-- Status actual -->
                                            <div class="alert {{ $user->telegram_chat_id ? 'alert-success' : 'alert-warning' }}" role="alert">
                                                <div class="d-flex align-items-center">
                                                    <i class="bx {{ $user->telegram_chat_id ? 'bx-check-circle' : 'bx-info-circle' }} me-2"></i>
                                                    <div>
                                                        @if($user->telegram_chat_id)
                                                            <strong>¡Configurado!</strong> Recibirás notificaciones diarias sobre tus eventos y audiencias.
                                                        @else
                                                            <strong>No configurado.</strong> Configura tu Chat ID para recibir notificaciones.
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Instrucciones -->
                                            <div class="mb-4">
                                                <h6 class="fw-bold text-primary mb-3">
                                                    <i class="bx bx-info-circle me-1"></i>
                                                    Cómo obtener tu Chat ID:
                                                </h6>

                                                <div class="row g-3">
                                                    <div class="col-md-6">
                                                        <div class="card bg-light h-100">
                                                            <div class="card-body">
                                                                <h6 class="card-title">
                                                                    <span class="badge bg-primary me-2">1</span>
                                                                    Abrir Telegram
                                                                </h6>
                                                                <p class="card-text mb-2">
                                                                    Busca nuestro bot oficial en Telegram Web: @AgendaPersonalSAyBGBot
                                                                </p>
                                                                <div {{-- class="d-flex align-items-center" --}}>
                                                                    <p>
                                                                        Si tienes la app de Telegram instalada, puedes usar el Bot.
                                                                    </p>
                                                                    <a href="https://t.me/{{ config('telegram.bot_username') }}"
                                                                       target="_blank"
                                                                       class="btn btn-sm btn-outline-primary ms-2">
                                                                        <i class="bx bx-link-external"></i>
                                                                        Abrir Bot
                                                                    </a>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="col-md-6">
                                                        <div class="card bg-light h-100">
                                                            <div class="card-body">
                                                                <h6 class="card-title">
                                                                    <span class="badge bg-primary me-2">2</span>
                                                                    Enviar Mensaje
                                                                </h6>
                                                                <p class="card-text">
                                                                    Inicia la conversación con el bot (ejemplo: "START") para activar la conversación.
                                                                </p>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="col-md-6">
                                                        <div class="card bg-light h-100">
                                                            <div class="card-body">
                                                                <h6 class="card-title">
                                                                    <span class="badge bg-primary me-2">3</span>
                                                                    Detectar Chat ID
                                                                </h6>
                                                                <p class="card-text mb-2">
                                                                    Después de enviar el mensaje, espera al menos 1 minuto para obtener tu chat ID y usa el botón "Detectar mi Chat ID" de abajo.
                                                                </p>
                                                                <small class="text-success">
                                                                    El sistema detectará tu Chat ID o puedes hacerlo manualmente.
                                                                </small>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="col-md-6">
                                                        <div class="card bg-light h-100">
                                                            <div class="card-body">
                                                                <h6 class="card-title">
                                                                    <span class="badge bg-primary me-2">4</span>
                                                                    Configurar Aquí
                                                                </h6>
                                                                <p class="card-text">
                                                                    Selecciona tu Chat ID de la lista y guarda los cambios.
                                                                </p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Detección Automática de Chat ID -->
                                            <div class="mb-4">
                                                <div class="card border-success">
                                                    <div class="card-body">
                                                        <h6 class="card-title text-success">
                                                            <i class="bx bx-search me-1"></i>
                                                            Detectar mi Chat ID Automáticamente
                                                        </h6>
                                                        <p class="card-text mb-3">
                                                            Si ya enviaste un mensaje al bot, puedes detectar automáticamente tu Chat ID.
                                                        </p>

                                                        <button type="button"
                                                                class="btn btn-success me-2"
                                                                onclick="detectChatId()">
                                                            <i class="bx bx-search me-1"></i>
                                                            Detectar mi Chat ID
                                                        </button>

                                                        <div id="chatIdResults" class="mt-3" style="display: none;">
                                                            <h6 class="text-primary">Chat IDs encontrados:</h6>
                                                            <div id="chatIdList"></div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Formulario -->
                                            <form action="{{ route('profile.telegram.update', $user) }}" method="POST">
                                                @csrf
                                                @method('PUT')

                                                <div class="row g-3">
                                                    <div class="col-md-8">
                                                        <label for="telegram_chat_id" class="form-label fw-bold">
                                                            <i class="bx bx-id-card me-1"></i>
                                                            Chat ID de Telegram
                                                        </label>
                                                        <input type="password"
                                                               class="form-control @error('telegram_chat_id') is-invalid @enderror"
                                                               id="telegram_chat_id"
                                                               name="telegram_chat_id"
                                                               value="{{ old('telegram_chat_id', $user->telegram_chat_id) }}"
                                                               placeholder="Ejemplo: 123456789"
                                                               autocomplete="off">

                                                        @error('telegram_chat_id')
                                                            <div class="invalid-feedback">{{ $message }}</div>
                                                        @enderror

                                                        <div class="form-text">
                                                            <i class="bx bx-info-circle me-1"></i>
                                                            Tu Chat ID único de Telegram (solo números).
                                                        </div>
                                                    </div>

                                                    <div class="col-md-4 d-flex align-items-end">
                                                        <div class="form-check form-switch">
                                                            <input class="form-check-input"
                                                                   type="checkbox"
                                                                   id="telegram_notifications_enabled"
                                                                   name="telegram_notifications_enabled"
                                                                   {{ $user->telegram_notifications_enabled ? 'checked' : '' }}>
                                                            <label class="form-check-label fw-bold" for="telegram_notifications_enabled">
                                                                <i class="bx bx-bell me-1"></i>
                                                                Recibir Notificaciones
                                                            </label>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="mt-4 d-flex flex-column flex-sm-row gap-2">
                                                    <button type="submit" class="btn btn-primary">
                                                        <i class="bx bx-save me-1"></i>
                                                        Guardar Configuración
                                                    </button>
{{--
                                                    @if($user->telegram_chat_id)
                                                        <button type="button"
                                                                class="btn btn-outline-info"
                                                                onclick="testTelegram()">
                                                            <i class="bx bx-test-tube me-1"></i>
                                                            Enviar Mensaje de Prueba
                                                        </button>
                                                    @endif --}}
                                                </div>
                                            </form>

                                            <!-- Información adicional -->
                                            <div class="mt-5">
                                                <div class="card border-info">
                                                    <div class="card-body">
                                                        <h6 class="card-title">
                                                            <i class="bx bx-info-circle me-2 text-info"></i>
                                                            Sobre las Notificaciones
                                                        </h6>
                                                        <ul class="mb-0 text-muted">
                                                            <li><strong>Horario:</strong> Recibirás un mensaje diario a las 8:00 AM con tus eventos y audiencias del día</li>
                                                            <li><strong>Contenido:</strong> Lista detallada de eventos, audiencias, horarios, lugares y estados</li>
                                                            <li><strong>Privacidad:</strong> Solo tú recibirás información sobre tus propios eventos</li>
                                                            <li><strong>Control:</strong> Puedes habilitar/deshabilitar las notificaciones en cualquier momento</li>
                                                            {{-- <li><strong>Seguridad:</strong> Tu Chat ID está protegido y no se comparte con terceros</li> --}}
                                                        </ul>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
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
<script src="{{ asset('js/vendors/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('js/jquery-confirm/jquery-confirm.js') }}"></script>
    <script src="{{ asset('js/vendors/cropper.min.js') }}"></script>
    <script src="{{ asset('js/profile/edit.js') }}"></script>

    <script>
        function detectChatId() {
            const btn = event.target;
            const originalText = btn.innerHTML;
            const resultsDiv = document.getElementById('chatIdResults');
            const listDiv = document.getElementById('chatIdList');

            console.log('detectChatId function called');
            console.log('Route URL:', `{{ route('profile.telegram.detect', $user) }}`);

            // Mostrar loading
            btn.innerHTML = '<i class="bx bx-loader-alt bx-spin me-1"></i>Detectando...';
            btn.disabled = true;
            resultsDiv.style.display = 'none';

            // Hacer petición AJAX
            fetch(`{{ route('profile.telegram.detect', $user) }}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => {
                console.log('Response status:', response.status);
                return response.json();
            })
            .then(data => {
                console.log('Response data:', data);
                btn.innerHTML = originalText;
                btn.disabled = false;

                if (data.success && data.chat_ids && data.chat_ids.length > 0) {
                    // Mostrar lista de Chat IDs encontrados
                    let html = '';
                    data.chat_ids.forEach(function(chatData, index) {
                        html += `
                            <div class="card mb-2">
                                <div class="card-body p-3">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <strong>${chatData.name || 'Usuario'}</strong>
                                            ${chatData.username ? '<small class="text-muted">@' + chatData.username + '</small>' : ''}
                                            <br>
                                            <code class="text-primary">${chatData.chat_id}</code>
                                            <br>
                                            <small class="text-muted">"${chatData.message}" - ${chatData.time}</small>
                                        </div>
                                        <button type="button" class="btn btn-sm btn-primary" onclick="selectChatId('${chatData.chat_id}')">
                                            <i class="bx bx-check me-1"></i>Es mi Chat ID
                                        </button>
                                    </div>
                                </div>
                            </div>
                        `;
                    });

                    listDiv.innerHTML = html;
                    resultsDiv.style.display = 'block';
                } else {
                    alert('ℹ️ ' + (data.message || 'No se encontraron mensajes recientes. Envía un mensaje al bot primero.'));
                }
            })
            .catch(error => {
                console.error('Fetch error:', error);
                btn.innerHTML = originalText;
                btn.disabled = false;
                alert('❌ Error de conexión: ' + error.message);
            });
        }

        function selectChatId(chatId) {
            document.getElementById('telegram_chat_id').value = chatId;
            document.getElementById('chatIdResults').style.display = 'none';

            // Mostrar mensaje de confirmación
            const resultsDiv = document.getElementById('chatIdResults');
            const successDiv = document.createElement('div');
            successDiv.className = 'alert alert-success mt-2';
            successDiv.innerHTML = '<i class="bx bx-check-circle me-1"></i>Chat ID seleccionado. ¡Ahora guarda la configuración!';
            resultsDiv.parentNode.insertBefore(successDiv, resultsDiv.nextSibling);

            // Remover mensaje después de 3 segundos
            setTimeout(() => {
                successDiv.remove();
            }, 3000);
        }
    </script>
@endsection
