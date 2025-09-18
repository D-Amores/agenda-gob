let usersData = []; // Guardar datos de usuarios para edición
// Función para hacer peticiones fetch con CSRF token
async function getData() {
    const data = { method: 'get' };
    const url = vURL + '/users-api';
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    const response = await fetch(url, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken
        },
        body: JSON.stringify(data)
    });
    return await response.json();
}

// CRUD para usuarios - inicio
async function store(userId) {
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    // Buscar la fila pendiente
    const row = document.querySelector(`tr[data-pending-id="${userId}"]`);

    // Guardar contenido original
    const originalHTML = row.innerHTML;

    // Mostrar animación en la fila
    row.innerHTML = `
        <td colspan="7" class="text-center py-3">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Procesando...</span>
            </div>
        </td>
    `;

    try {
        const url = vURL;
        const response = await fetch(url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            },
            body: JSON.stringify({ id: userId })
        });


        const result = await response.json();

        if (result.ok) {
            alert(result.message, 'green', 'Éxito', null, 3000);

            const newData = await getData(); // Obtener datos actualizados
            if (newData.ok && newData.data?.pending_registrations && newData.data?.users) {
                pendingDataTableOnHTML(newData.data.pending_registrations);
                userDataTableOnHTML(newData.data.users);
            }
        } else {
            alert(result.message || 'Error al crear usuario', 'red', 'Error', null, 5000);
            row.innerHTML = originalHTML; // restaurar fila si falla
        }
    } catch (error) {
        alert('Error en la solicitud', 'red', 'Error', null, 5000);
        row.innerHTML = originalHTML; // restaurar fila si hay excepción
    }
}


async function update() {
    const btn = document.getElementById('btnUserEdit');
    const spinner = document.getElementById('userEditSpinner');

    spinner.classList.remove('d-none');
    btn.disabled = true;
    
    const form = document.getElementById('userEditForm');
    const formData = new FormData(form);
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    // Convertir FormData a objeto plano
    const data = {};
    let userId = null;
    formData.forEach((value, key) => {  
        if (key === "user_id") {
            userId = value.trim();   // guardamos aparte
        } else {
            data[key] = value.trim(); // guardamos el resto
        }
    });
    
    try {
        const url = vURL + '/' + userId;
        const response = await fetch(url, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            },
            body: JSON.stringify(data)
        });

        const result = await response.json();

        if(result.ok){
            alert(result.message, 'green', 'Éxito', null, 3000);
            form.reset();

            const newData = await getData(); // Obtener datos actualizados
            if (newData.ok && newData.data?.users) {
                usersData = newData.data.users; // Actualizar datos locales
                userDataTableOnHTML(newData.data.users); // Actualizar tabla de usuarios
            }
            // Cerrar modal de edición
            const modal = bootstrap.Modal.getInstance(document.getElementById('editarUsuarioModal'));
            // Mover el foco a un botón fuera del modal
            document.getElementById('btnAbrirCrearUsuario')?.focus();
            // Ahora sí cerrar modal
            modal?.hide();
        }else{
            alert(result.message || 'Error al actualizar usuario', 'red', 'Error', null, 5000);
        }
    }catch (error) {
        alert('Error en la solicitud', 'red', 'Error', null, 5000);
    }finally {
        spinner.classList.add('d-none');
        btn.disabled = false;
    }
}

async function destroy(userId) {
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    const url = vURL + '/' + userId;

    try {
        const response = await fetch(url, {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            }
        });

        const result = await response.json();

        if (result.ok) {
            alert(result.message, 'green', 'Éxito', null, 3000);

            // Actualizar tabla
            const newData = await getData();
            if (newData.ok && newData.data?.users) {
                usersData = newData.data.users;
                userDataTableOnHTML(newData.data.users);
            }
        } else {
            alert(result.message || 'Error al eliminar usuario', 'red', 'Error', null, 5000);
        }
    } catch (error) {
        alert('Error en la solicitud', 'red', 'Error', null, 5000);
    }
}
// CRUD para usuarios - fin
//CRUD para registros pendientes - inicio
async function storePendingRegistration() {
    const btn = document.getElementById('btnUserCreate');
    const spinner = document.getElementById('userCreateSpinner');

    // Mostrar spinner y desactivar botón
    spinner.classList.remove('d-none');
    btn.disabled = true;

    const form = document.getElementById('userCreateForm');
    const formData = new FormData(form);
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    // Convertir FormData a objeto plano
    const data = {};
    formData.forEach((value, key) => {
        data[key] = value.trim();
    });

    try {
        const url = vURLPending;
        const response = await fetch(url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            },
            body: JSON.stringify(data)
        });

        const result = await response.json();

        if (result.ok) {
            alert(result.message, 'green', 'Éxito', null, 3000);
            form.reset();

            const newData = await getData(); // Obtener datos actualizados
            if (newData.ok && newData.data?.pending_registrations) {
                pendingDataTableOnHTML(newData.data.pending_registrations); // Actualizar tabla de pendientes
            }

            // Cerrar modal de creación
            const modal = bootstrap.Modal.getInstance(document.getElementById('crearUsuarioModal'));

            // Mover el foco a un botón fuera del modal
            document.getElementById('btnAbrirCrearUsuario')?.focus();

            // Ahora sí cerrar modal
            modal?.hide();

        } else {
            alert(result.message || 'Error al crear usuario', 'red', 'Error', null, 5000);
        }
    } catch (error) {
        alert('Error en la solicitud', 'red', 'Error', null, 5000);
    } finally {
        // Ocultar spinner y habilitar botón
        spinner.classList.add('d-none');
        btn.disabled = false;
    }
}

async function destroyPendingRegistration(pendingId) {
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    const url = vURLPending + '/' + pendingId;
    try {
        const response = await fetch(url, {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            }
        });

        const result = await response.json();

        if (result.ok) {
            alert(result.message, 'green', 'Éxito', null, 3000);

            // Actualizar tabla
            const newData = await getData();
            if (newData.ok && newData.data?.users && newData.data?.pending_registrations) {
                usersData = newData.data.users;
                userDataTableOnHTML(newData.data.users);
                pendingDataTableOnHTML(newData.data.pending_registrations);
            }
        } else {
            alert(result.message || 'Error al eliminar registro pendiente', 'red', 'Error', null, 5000);
        }
    } catch (error) {
        alert('Error en la solicitud', 'red', 'Error', null, 5000);
    }
}

//funciones para llenar tablas y selects
function userDataTableOnHTML(users) {
    // Pintar Usuarios
    console.log(users);
    const usuariosTbody = document.querySelector('#usuarios tbody');
    usuariosTbody.innerHTML = ''; // limpiar tabla
    users.forEach(user => {
        if (user.id === authUserId) return;
        const tr = document.createElement('tr');
        tr.dataset.userId = user.id;
        tr.innerHTML = `
            <td>
                <div class="d-flex align-items-center">
                    <img src="${user.avatar_url || ''}" class="rounded-circle me-2" width="32" height="32" alt="Avatar">
                    <div>
                        <strong>${user.name}</strong><br>
                        <small class="text-muted">${user.username}</small>
                    </div>
                </div>
            </td>
            <td>${user.email}</td>
            <td><span class="badge bg-info text-truncate d-inline-block" style="max-width: 200px;" data-bs-toggle="tooltip" data-bs-placement="top" title="${user.area?.area || ''}">${user.area?.area || ''}</span></td>
            <td><span class="badge bg-secondary">${user.roles?.[0]?.name || ''}</span></td>
            <td><span class="badge ${user.email_verified_at ? 'bg-success' : 'bg-warning'}">${user.email_verified_at ? 'Verificado' : 'Pendiente'}</span></td>
            <td class="text-center">
                <div class="btn-group" role="group">
                    <button class="btn btn-sm btn-outline-primary btnUserEditModal" data-bs-toggle="modal" data-bs-target="#editarUsuarioModal"><i class="bx bx-edit"></i></button>
                    <button class="btn btn-sm btn-outline-danger btnUserDelete"><i class="bx bx-trash"></i></button>
                </div>
            </td>
        `;
        usuariosTbody.appendChild(tr);
    });
}

function pendingDataTableOnHTML(pending) {
    // Pintar Registros Pendientes
    const pendientesTbody = document.querySelector('#pendientes tbody');
    pendientesTbody.innerHTML = ''; // limpiar tabla

    pending.forEach(p => {
        const tr = document.createElement('tr');
        tr.dataset.pendingId = p.id;
        tr.innerHTML = `
                <td>
                    <strong>${p.name}</strong><br>
                    <small class="text-muted">${p.username}</small>
                </td>
                <td>${p.email}</td>
                <td>${p.phone ? p.phone : 'S/N'}</td>
                <td><span class="badge bg-warning">${p.area?.area || ''}</span></td>
                <td><span class="badge bg-secondary">${p.rol || ''}</span></td>
                <td><span class="text-muted small">${new Date(p.expires_at).toLocaleString()}</span></td>
                <td class="text-center">
                    <div class="btn-group" role="group">
                        <button class="btn btn-sm btn-outline-success btnPendingConfirm" title="Aprobar"><i class="bx bx-check"></i></button>
                        <button class="btn btn-sm btn-outline-danger btnPendingDelete" title="Eliminar"><i class="bx bx-trash"></i></button>
                    </div>
                </td>
            `;
        pendientesTbody.appendChild(tr);
    });
}

function areasToSelect(areas) {
    const selects = document.getElementsByClassName('areaSelect');

    Array.from(selects).forEach(select => {
        // Limpiar opciones
        select.innerHTML = '<option value="" disabled selected>Seleccione un área</option>';

        // Llenar opciones
        areas.forEach(area => {
            const option = document.createElement('option');
            option.value = area.id;
            option.textContent = area.area;

            select.appendChild(option);
        });
    });
}

function rolesToSelect(roles) {
    const selects = document.getElementsByClassName('rol-select');

    Array.from(selects).forEach(select => {
        // Limpiar opciones
        select.innerHTML = '<option value="" disabled selected>Seleccione un rol</option>';

        // Llenar opciones
        roles.forEach(role => {
            const option = document.createElement('option');
            option.value = role.name;
            option.textContent = role.name;

            select.appendChild(option);
        });
    });
}

//funcion index para mandar a llamar todas las funciones de llenado de tablas y selects
function index(data) {
    userDataTableOnHTML(data.users);
    pendingDataTableOnHTML(data.pending_registrations);
    areasToSelect(data.areas);
    rolesToSelect(data.roles);

    // Inicializar tooltips
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
    tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl)
    });
}

//funcion para llenar formulario de editar usuario
// Abrir modal de edición con datos del usuario
function openEditModal(user) {
    // Llenar campos
    document.getElementById('edit_usuario_id').value = user.id;
    document.getElementById('edit_username').value = user.username;

    // Área
    const areaSelect = document.getElementById('edit_area_id');
    Array.from(areaSelect.options).forEach(option => {
        option.selected = option.value == (user.area?.id || '');
    });

    // Rol (minúsculas para coincidir con el backend)
    const rolSelect = document.getElementById('edit_rol');
    Array.from(rolSelect.options).forEach(option => {
        option.selected = option.value.toLowerCase() == (user.roles?.[0]?.name || '').toLowerCase();
    });
}
//Funciones generales
function saveTabsState() {
    let adminTabs = document.querySelectorAll('#adminTabs button[data-bs-toggle="tab"]');
    let storedTab = localStorage.getItem("activeAdminTab");

    if (storedTab) {
        // Mostrar la pestaña guardada
        let tabTrigger = document.querySelector(`#adminTabs button[data-bs-target="${storedTab}"]`);
        if (tabTrigger) {
            new bootstrap.Tab(tabTrigger).show();
        }
    } else {
        // Si no hay nada guardado, mostrar la primera
        if (adminTabs.length > 0) {
            new bootstrap.Tab(adminTabs[0]).show();
        }
    }

    // Guardar al cambiar
    adminTabs.forEach(tab => {
        tab.addEventListener("shown.bs.tab", function (e) {
            localStorage.setItem("activeAdminTab", e.target.getAttribute("data-bs-target"));
        });
    });
}
// Validar formularios de creación y edición
function validateCreateForm(form) {
    let errors = [];

    if (!form.name.value.trim() || form.name.value.trim().length < 6) errors.push("El nombre completo es obligatorio y debe tener mas de 6 caracteres.");
    if (!form.username.value.trim() || form.username.value.trim().length < 3) errors.push("El nombre de usuario es obligatorio y debe tener mas de 3 caracteres.");
    if (!form.email.value.trim() || form.email.value.trim().length < 5) errors.push("El correo electrónico es obligatorio y debe tener mas de 5 caracteres.");
    if (!form.rol.value.trim()) errors.push("El rol es obligatorio.");
    if (!form.area_id.value.trim()) errors.push("El área es obligatoria.");

    // Validación del teléfono si hay valor
    const phone = form.phone.value.trim();
    if (phone) {
        if (!/^\d+$/.test(phone)) errors.push("El teléfono solo debe contener números.");
        if (phone.length > 10) errors.push("El teléfono debe tener como máximo 10 dígitos.");
    }

    if (errors.length > 0) {
        alert(errors.join("\n"), "red", "Error", null, 5000);
        return false;
    }
    return true;
}
function validateEditForm(form) {
    let errors = [];
    if (!form.username.value.trim() || form.username.value.trim().length < 3) errors.push("El nombre de usuario es obligatorio y debe tener más de 3 caracteres.");
    if (!form.rol.value.trim()) errors.push("El rol es obligatorio.");
    if (!form.area_id.value.trim()) errors.push("El área es obligatoria.");

    if (errors.length > 0) {
        alert(errors.join("\n"), "red", "Error", null, 5000);
        return false;
    }
    return true;
}
// Función de alerta personalizada
function alert(message, type = "blue", title = "Información", onOk, timeout = 0) {
    const jc = $.alert({
        title,
        content: message,
        type,
        theme: 'material',
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
            } catch (_) { }
        }, Number(timeout));
    }

    return jc;
}
// Confirmar creación de usuario sin necesidad de que el usuario lo confirme en su email
function confirmStore(userId)
{
    $.confirm({
        title: 'Advertencia',
        content: '¿Está seguro de aprobar este registro pendiente? Esta acción no podrá ser revertida.',
        type: 'orange',
        theme: 'material',
        buttons: {
            Cancelar: function() { 
                //alert('Acción cancelada', 'blue', 'Información', null, 2000);
            },
            Guardar: function() {
                store(userId);
            }
        }
    });
}
// Confirmar edición de usuario en la tabla de usuarios
function confirmUpdate()
{
    $.confirm({
        title: 'Advertencia',
        content: '¿Está seguro de editar el registro? Esta acción no podrá ser revertida.',
        type: 'orange',
        theme: 'material',
        buttons: {
            Cancelar: function() { 
                //alert('Acción cancelada', 'blue', 'Información', null, 2000);
            },
            Guardar: function() {
                update();
                //alert('Funcionalidad en desarrollo', 'blue', 'Información', null, 3000);
            }
        }
    });
}

// Confirmar eliminación de usuario en la tabla de usuarios
function confirmDestroy(userId) {
    $.confirm({
        title: 'Advertencia',
        content: '¿Está seguro de eliminar este usuario? Esta acción no podrá ser revertida.',
        type: 'red',
        theme: 'material',
        buttons: {
            Cancelar: function () {},
            Eliminar: function () {
                destroy(userId);
            }
        }
    });
}

// Confirmar creación de usuario en la tabla de registros pendientes
function confirmStorePending()
{
    $.confirm({
        title: 'Advertencia',
        content: '¿Está seguro de guardar el registro? Esta acción no podrá ser revertida.',
        type: 'orange',
        theme: 'material',
        buttons: {
            Cancelar: function() { 
                //alert('Acción cancelada', 'blue', 'Información', null, 2000);
            },
            Guardar: function() {
                storePendingRegistration();
            }
        }
    });
}
// Confirmar eliminación de registro pendiente
function confirmDestroyPending(pendingId) {
    $.confirm({
        title: 'Advertencia',
        content: '¿Está seguro de eliminar este registro pendiente? Esta acción no podrá ser revertida.',
        type: 'red',
        theme: 'material',
        buttons: {
            Cancelar: function () {},
            Eliminar: function () {
                destroyPendingRegistration(pendingId);
            }
        }
    });
}


document.addEventListener('DOMContentLoaded', async function () {
    
    saveTabsState();
    // Petición para obtener datos iniciales
    const newData = await getData();

    //Botones para crear usuario y editar usuario
    const btnUserCreate = document.getElementById('btnUserCreate');
    const btnUserEdit = document.getElementById('btnUserEdit');
    //Contenedor de botones de acción en tabla de usuarios y pendientes
    const userActionBtn = document.querySelector('#usuarios tbody');
    const pendingActionBtn = document.querySelector('#pendientes tbody');

    if(!newData.ok){
        alert(newData.message, 'red', 'Error', null, 5000);
        return;
    }
    //Llenar tablas y selects
    index(newData.data);
    usersData = newData.data.users;
    

    btnUserCreate.addEventListener('click', function (e) {
        const form = document.getElementById('userCreateForm');
        if (!validateCreateForm(form)) return;
        confirmStorePending();
    });

    btnUserEdit.addEventListener('click', function (e) {
        const form = document.getElementById('userEditForm');
        if (!validateEditForm(form)) return;
        confirmUpdate();
        
    });

    // Delegación de eventos para abrir modal de edición
    userActionBtn.addEventListener('click', function(e) {
        // Editar
        const btnEdit = e.target.closest('.btnUserEditModal');
        if (btnEdit) {
            const row = btnEdit.closest('tr');
            const userId = row.dataset.userId;
            const user = usersData.find(u => u.id == userId);
            if (user) openEditModal(user);
            return;
        }

        // Eliminar
        const btnDelete = e.target.closest('.btnUserDelete');
        if (btnDelete) {
            const row = btnDelete.closest('tr');
            const userId = row.dataset.userId;
            confirmDestroy(userId);
        }
    });
    // Delegación de eventos para eliminar registro pendiente
    pendingActionBtn.addEventListener('click', function(e) {
        // Aprobar
        const btnConfirm = e.target.closest('.btnPendingConfirm');
        if (btnConfirm) {
            const row = btnConfirm.closest('tr');
            const pendingId = row.dataset.pendingId;
            confirmStore(pendingId);
            return;
        }
    
        // Eliminar
        const btnDelete = e.target.closest('.btnPendingDelete');
        if (btnDelete) {
            const row = btnDelete.closest('tr');
            const pendingId = row.dataset.pendingId;
            confirmDestroyPending(pendingId);
        }
    });
});