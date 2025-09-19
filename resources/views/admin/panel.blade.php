@extends('layouts.app')

@section('title', 'Panel de Administración')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <!-- Header -->
            <div class="card mb-4">
                <div class="card-header d-flex flex-column flex-md-row align-items-center justify-content-between">
                    <div class="mb-2 mb-md-0 d-flex flex-column align-items-center align-items-md-start">
                        <h4 class="mb-0">
                            Panel de Administración
                        </h4>
                        <small class="text-muted">Gestión de usuarios y registros pendientes</small>
                    </div>
                    <button class="btn btn-primary w-md-auto" data-bs-toggle="modal" data-bs-target="#crearUsuarioModal">
                        <i class="bx bx-plus me-1"></i>
                        Crear Usuario
                    </button>
                </div>
            </div>

            <!-- Tabs -->
            <ul class="nav nav-tabs" id="adminTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link rounded-top" id="pendientes-tab" data-bs-toggle="tab" data-bs-target="#pendientes" type="button" role="tab" aria-controls="pendientes" aria-selected="true">
                        Registros Pendientes
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link rounded-top" id="usuarios-tab" data-bs-toggle="tab" data-bs-target="#usuarios" type="button" role="tab" aria-controls="usuarios" aria-selected="false">
                        Usuarios
                    </button>
                </li>
            </ul>
            <div class="tab-content card mb-3" id="adminTabsContent">
                <!-- Tabla Registros Pendientes -->
                <div class="tab-pane fade" id="pendientes" role="tabpanel" aria-labelledby="pendientes-tab">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="bx bx-time-five me-2"></i>
                            Registros Pendientes
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>Usuario</th>
                                        <th>Email</th>
                                        <th>Teléfono</th>
                                        <th>Área</th>
                                        <th>Rol</th>
                                        <th>Expira</th>
                                        <th class="text-center">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    {{-- Ejemplo --}}
                                    <tr>
                                        <td>
                                            <strong>pendiente1</strong>
                                            <br>
                                            <small class="text-muted">usuario.pendiente</small>
                                        </td>
                                        <td>pendiente@ejemplo.com</td>
                                        <td><span class="badge bg-warning">Área X</span></td>
                                        <td><span class="text-muted small">24/09/2025 12:00</span></td>
                                        <td class="text-center">
                                            <div class="btn-group" role="group">
                                                <button class="btn btn-sm btn-outline-success" title="Aprobar"><i class="bx bx-check"></i></button>
                                                <button class="btn btn-sm btn-outline-danger" title="Eliminar"><i class="bx bx-trash"></i></button>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="d-flex justify-content-center mt-3">
                            {{-- Paginación si es necesario --}}
                        </div>
                    </div>
                </div>
                <!-- Tabla Usuarios -->
                <div class="tab-pane fade" id="usuarios" role="tabpanel" aria-labelledby="usuarios-tab">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="bx bx-users me-2"></i>
                            Lista de Usuarios
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>Usuario</th>
                                        <th>Email</th>
                                        <th>Área</th>
                                        <th>Rol</th>
                                        <th>Estado</th>
                                        <th class="text-center">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    {{-- Ejemplo --}}
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <img src="" class="rounded-circle me-2" width="32" height="32" alt="Avatar">
                                                <div>
                                                    <strong>nombre</strong>
                                                    <br>
                                                    <small class="text-muted">nombre.de.usuario</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td>email@ejemplo.com</td>
                                        <td><span class="badge bg-info">Nombre del área</span></td>
                                        <td><span class="badge">rol</span></td>
                                        <td><span class="badge bg-success">Verificado</span></td>
                                        <td class="text-center">
                                            <div class="btn-group" role="group">
                                                <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#editarUsuarioModal"><i class="bx bx-edit"></i></button>
                                                <button class="btn btn-sm btn-outline-danger"><i class="bx bx-trash"></i></button>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="d-flex justify-content-center mt-3">
                            {{-- Paginación si es necesario --}}
                        </div>
                    </div>
                </div>
            </div>
            <!-- /Tabs -->

        </div>
    </div>
</div>

<!-- Modal Crear Usuario -->
<div class="modal fade" id="crearUsuarioModal" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-dialog-centered modal-lg"> <!-- Centrado y tamaño grande -->
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="bx bx-user-plus me-2"></i>
                    Crear Nuevo Usuario
                </h5>
                <!-- Quité el botón de cierre (X) para que solo se cierre con Cancelar) -->
            </div>
            <form id="userCreateForm">
                <div class="modal-body pb-0">
                    <div class="row g-3"> <!-- Espaciado responsivo -->
                        <div class="col-md-6">
                            <label for="name" class="form-label">
                                <i class="bx bx-user me-1"></i> Nombre Completo *
                            </label>
                            <input type="text" class="form-control" id="name" name="name" required>
                        </div>
                        <div class="col-md-6">
                            <label for="username" class="form-label">
                                <i class="bx bx-at me-1"></i> Nombre de Usuario *
                            </label>
                            <input type="text" class="form-control" id="username" name="username" required>
                        </div>

                        <div class="col-md-6">
                            <label for="email" class="form-label">
                                <i class="bx bx-envelope me-1"></i> Correo Electrónico *
                            </label>
                            <input type="email" class="form-control" id="email" name="email" required>
                        </div>
                        <div class="col-md-6">
                            <label for="phone" class="form-label">
                                <i class="bx bx-phone me-1"></i> Teléfono
                            </label>
                            <input type="text" class="form-control" id="phone" name="phone">
                        </div>

                        <div class="col-md-6">
                            <label for="area_id" class="form-label">
                                <i class="bx bx-buildings me-1"></i> Área
                            </label>
                            <select class="form-select areaSelect" id="area_id" name="area_id">
                                <option value="">Seleccionar área...</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="rol" class="form-label">
                                <i class="bx bx-shield me-1"></i> Rol *
                            </label>
                            <select class="form-select rol-select" id="rol" name="rol" required>
                                <option value="">Seleccionar rol...</option>
                            </select>
                        </div>
                    </div>

                    <div class="alert alert-info mt-3">
                        <i class="bx bx-info-circle me-2"></i>
                        Se generará automáticamente una contraseña segura y se enviará al usuario por correo electrónico despues de confirmar su correo.
                    </div>
                </div>
                <div class="modal-footer ps-1 d-flex flex-row justify-content-end">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-primary" id="btnUserCreate">
                        <span class="spinner-border spinner-border-sm me-2 d-none" id="userCreateSpinner" role="status"></span>
                        <i class="bx d-none d-md-inline bx-plus me-1"></i>
                        Crear Usuario
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Editar Usuario -->
<div class="modal fade" id="editarUsuarioModal" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="bx bx-edit me-2"></i>
                    Editar Usuario
                </h5>
                <!-- Quité el botón de cierre (X) -->
            </div>
            <form id="userEditForm">
                <input type="hidden" id="edit_usuario_id" name="user_id">
                <div class="modal-body">

                    <div class="row g-3">
                        {{-- <div class="col-md-6">
                            <label for="edit_name" class="form-label">
                                <i class="bx bx-user me-1"></i> Nombre Completo *
                            </label>
                            <input type="text" class="form-control" id="edit_name" name="name" required>
                        </div> --}}
                        <div class="col-md-6">
                            <label for="edit_username" class="form-label">
                                <i class="bx bx-at me-1"></i> Nombre de Usuario *
                            </label>
                            <input type="text" class="form-control" id="edit_username" name="username" required>
                        </div>

                        {{-- <div class="col-md-6">
                            <label for="edit_email" class="form-label">
                                <i class="bx bx-envelope me-1"></i> Correo Electrónico *
                            </label>
                            <input type="email" class="form-control" id="edit_email" name="email" required>
                        </div>
                        <div class="col-md-6">
                            <label for="edit_phone" class="form-label">
                                <i class="bx bx-phone me-1"></i> Teléfono
                            </label>
                            <input type="text" class="form-control" id="edit_phone" name="phone">
                        </div> --}}

                        <div class="col-md-6">
                            <label for="edit_area_id" class="form-label">
                                <i class="bx bx-buildings me-1"></i> Área
                            </label>
                            <select class="form-select areaSelect" id="edit_area_id" name="area_id">
                                <option value="">Seleccionar área...</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="edit_rol" class="form-label">
                                <i class="bx bx-shield me-1"></i> Rol *
                            </label>
                            <select class="form-select rol-select" id="edit_rol" name="rol" required>
                                <option value="">Seleccionar rol...</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer d-flex flex-row justify-content-md-end ps-1 justify-content-center">
                    <button type="button" class="btn btn-outline-secondary" id="btnCloseEdit">Cancelar</button>
                    <button type="button" class="btn btn-primary" id="btnUserEdit">
                        <span class="spinner-border spinner-border-sm me-2 d-none" id="userEditSpinner" role="status"></span>
                        <i class="bx bx-save d-none d-md-inline me-1"></i> Guardar Cambios
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@section('script')
{{-- <script>
    const csrfToken = "{{ csrf_token() }}";
</script> --}}
<script src="{{ asset('js/jquery-confirm/jquery-confirm.js') }}"></script>
<script src="{{ asset('js/admin/panel.js') }}"></script>
<script>
    var vURL=window.location.origin + '/agenda-new/admin/users';
    var vURLPending=window.location.origin + '/agenda-new/admin/pending-registrations';
    const authUserId = {{ auth()->id() }};
</script>
@endsection