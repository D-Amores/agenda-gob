"use strict";
FullCalendar.globalLocales.push(function () {
     var es = {
         code: "es",
         week: {
             dow: 1,
             doy: 4
         },
         buttonText: {
             prev: "Ant",
             next: "Sig",
             today: "Hoy",  
             month: "Mes",
             week: "Semana",
             day: "Día",
             list: "Agenda"
         },
         weekText: "Sm",
         allDayText: "Todo el día",
         moreLinkText: "más",
         noEventsText: "No hay eventos para mostrar"
     };
     return es;
 }());

let direction = "ltr";
isRtl && (direction = "rtl");

document.addEventListener("DOMContentLoaded", function () {
    const x = document.getElementById("calendar"),
        q = document.querySelector(".app-calendar-sidebar"),
        D = document.getElementById("addEventSidebar"),
        P = document.querySelector(".app-overlay"),
        M = {
            reprogramado: "warning",
            programado: "primary",
            atendido: "success",
            cancelado: "danger",      
            pendiente: "info"
        },
        T = document.querySelector(".btn-toggle-sidebar"),
        n = document.querySelector(".btn-add-event"),
        d = document.querySelector(".btn-update-event"),
        o = document.querySelector(".btn-delete-event"),
        A = document.querySelector(".btn-cancel"),
        F = document.querySelector("#eventTitle"),
        s = document.querySelector("#eventStartDate"),
        c = document.querySelector("#eventEndDate"),
        Y = document.querySelector("#eventURL"),
        u = $("#eventLabel"),
        v = $("#eventGuests"),
        C = document.querySelector("#eventLocation"),
        V = document.querySelector("#eventDescription"),
        m = document.querySelector(".allDay-switch"),
        B = document.querySelector(".select-all"),
        R = document.querySelector(".inline-calendar");

    let a, l = [], r = !1, e;

    function renderEventosDelDia(fechaSeleccionada) {
        const lista = document.querySelector('.event-list-scroll ul');
        lista.innerHTML = ''; // Limpiar lista previa

        const fechaFormateada = moment(fechaSeleccionada).format('YYYY-MM-DD');

        const tiposSeleccionados = Array.from(document.querySelectorAll('.input-filter:checked'))
        .map(cb => ({ tipo: cb.dataset.tipo, estatus: cb.dataset.estatus }));

        const eventosDelDia = l.filter(evento => {
            const mismaFecha = moment(evento.start).format('YYYY-MM-DD') === fechaFormateada;
            const coincideFiltro = tiposSeleccionados.some(f =>
                f.tipo === evento.tipo && f.estatus.toLowerCase() === (evento.extendedProps.estatus || '').toLowerCase()
            );
            return mismaFecha && coincideFiltro;
        });


        if (eventosDelDia.length === 0) {
            lista.innerHTML = `<li class="list-group-item text-center text-muted">No hay eventos</li>`;
            document.getElementById("eventForm").classList.add("d-none"); // Ocultar detalle
            return;
        }

        eventosDelDia.forEach((evento, index) => {
            const li = document.createElement('li');
            li.className = 'list-group-item d-flex justify-content-between align-items-center';

            // Marca como seleccionado si es el primero
            const isFirst = index === 0;
            const btnClass = isFirst ? 'btn-primary active' : 'btn-outline-primary';

            li.innerHTML = `
                <div>
                    <i class="bx bx-calendar me-2 text-primary"></i>
                    <strong>${evento.title}</strong>
                    <div class="small text-muted">${moment(evento.start).format('h:mm A')} - ${moment(evento.end).format('h:mm A')}</div>
                </div>
                <button class="btn btn-sm ${btnClass} btn-select-event" data-id="${evento.id}" data-tipo="${evento.tipo}">
                    <i class="bx bx-check-circle"></i> Seleccionar
                </button>
            `;

            lista.appendChild(li);
        });

        // Mostrar detalles del primer evento (solo si hay eventos)
        llenarFormulario(eventosDelDia[0]);
        document.getElementById("eventForm").classList.remove("d-none");
        // Agregar listeners a los botones
        lista.querySelectorAll(".btn-select-event").forEach(btn => {
            btn.addEventListener("click", e => {
                // Quitar estilo de seleccionado de todos
                lista.querySelectorAll(".btn-select-event").forEach(b => {
                    b.classList.remove("btn-primary", "active");
                    b.classList.add("btn-outline-primary");
                });

                // Aplicar estilo al seleccionado
                btn.classList.remove("btn-outline-primary");
                btn.classList.add("btn-primary", "active");

                const eventId = btn.getAttribute("data-id");
                const eventTipo = btn.getAttribute("data-tipo");
                const evento = l.find(ev => ev.id == eventId && ev.tipo === eventTipo);

                if (!evento) return;

                llenarFormulario(evento);
                p.show();
                document.getElementById("eventForm").classList.remove("d-none");
            });
        });
    }

    function llenarFormulario(event) {
        const eventForm = document.getElementById("eventForm");

        if (!eventForm || !event) return;

        // Asegurarse de mostrar el formulario
        eventForm.classList.remove("d-none");

        document.getElementById("asunto").innerText = event.title;
        document.getElementById("hora").innerText = `${moment(event.start).format('h:mm A')} - ${moment(event.end).format('h:mm A')}`;
        document.getElementById("estatus").innerText = event.extendedProps.estatus || "N/D";
        document.getElementById("vestimenta").innerText = event.extendedProps.vestimenta || "N/D";
        
        const btnEditar = document.getElementById("btnEditar");
        const btnEliminar = document.getElementById("btnEliminar");
        const formEnviar = document.getElementById("formEnviar");

        const puedeEditar = event.extendedProps.user_id == currentUserId;

        if (puedeEditar) {
            btnEditar.classList.remove("d-none");
            btnEliminar.classList.remove("d-none");

            let urlEliminar = '', urlEditar = '', id = event.id, tipo = event.tipo;

            if (tipo === 'evento') {
                urlEliminar = urlEventoEliminar.replace('__ID__', id);
                urlEditar = urlEventoEditar.replace('__ID__', id);
            } else if (tipo === 'audiencia') {
                urlEliminar = urlAudienciaEliminar.replace('__ID__', id);
                urlEditar = urlAudienciaEditar.replace('__ID__', id);
            }

            console.log("URL de edición:", urlEditar);
            console.log("URL de eliminación:", urlEliminar);

            formEnviar.action = urlEliminar;
            btnEditar.href = urlEditar;
            btnEditar.dataset.id = id;
            btnEditar.dataset.tipo = tipo;
            btnEliminar.dataset.id = id;
            btnEliminar.dataset.tipo = tipo;

        } else {
            // Oculta los botones si no puede editar
            btnEditar.classList.add("d-none");
            btnEliminar.classList.add("d-none");
        }
    }

// Cargar audiencias desde la variable global `audiencias` generada en Blade
    if (typeof audiencias !== 'undefined') {
        l = audiencias
        .filter(a => a && a.id)
        .map(a => {
            // Obtener solo la fecha en formato YYYY-MM-DD
            const fecha = a.fecha_audiencia.split(' ')[0];

            // Inicio
            const start = a.hora_audiencia
                ? moment(`${fecha} ${a.hora_audiencia}`, 'YYYY-MM-DD HH:mm').toDate()
                : moment(`${fecha} 00:00`, 'YYYY-MM-DD HH:mm').toDate();

            // Fin
            const end = a.hora_fin_audiencia
                ? moment(`${fecha} ${a.hora_fin_audiencia}`, 'YYYY-MM-DD HH:mm').toDate()
                : moment(start).add(1, 'hours').toDate();

            return {
                id: Number(a.id),
                tipo: 'audiencia',
                title: a.nombre || 'Sin título',
                asunto: a.asunto_audiencia || 'Sin título',
                start: start,
                end: end,
                allDay: false,
                extendedProps: {
                    descripcion: a.descripcion || '',
                    lugar: a.lugar || '',
                    calendar: (a.estatus?.estatus || 'pendiente').toLowerCase(),
                    user: a.user?.name || '',
                    estatus: a.estatus?.estatus || '',
                    vestimenta: a.vestimenta?.tipo || 'No especificada',
                    user_id: a.user?.id || null
                }
            };
        });
    }

    if (typeof eventos !== 'undefined') {
        const eventosProcesados = eventos
            .filter(e => e && e.id)
            .map(e => {
                const fecha = e.fecha_evento.split(' ')[0];
                const start = e.hora_evento
                    ? moment(`${fecha} ${e.hora_evento}`, 'YYYY-MM-DD HH:mm').toDate()
                    : moment(`${fecha} 00:00`, 'YYYY-MM-DD HH:mm').toDate();

                const end = e.hora_fin_evento
                    ? moment(`${fecha} ${e.hora_fin_evento}`, 'YYYY-MM-DD HH:mm').toDate()
                    : moment(start).add(1, 'hours').toDate();

                return {
                    id: Number(e.id), // prefijo para evitar colisiones con audiencias
                    tipo: 'evento',
                    title: e.nombre || 'Sin título',
                    start: start,
                    end: end,
                    allDay: false,
                    extendedProps: {
                        descripcion: e.descripcion || '',
                        lugar: e.lugar || '',
                        calendar: (e.estatus?.estatus || 'pendiente').toLowerCase(),
                        user: e.user?.name || '',
                        estatus: e.estatus?.estatus || '',
                        vestimenta: e.vestimenta?.tipo || 'No especificada',
                        user_id: e.user?.id || null
                    }
                };
            });
        l = l.concat(eventosProcesados); // Combinar con las audiencias
    }

    const p = new bootstrap.Offcanvas(D);

    function y() {
        const e = document.querySelector(".fc-sidebarToggle-button");
        if (e) {
            e.classList.remove("fc-button-primary");
            e.classList.add("d-lg-none", "d-inline-block", "ps-0");
            while (e.firstChild) e.firstChild.remove();
            e.setAttribute("data-bs-toggle", "sidebar");
            e.setAttribute("data-overlay", "");
            e.setAttribute("data-target", "#app-calendar-sidebar");
            e.insertAdjacentHTML("beforeend", '<i class="bx bx-menu bx-sm text-body"></i>');
        }
    }
    
    let i = new FullCalendar.Calendar(x, {
        themeSystem: 'standard',
        initialView: "dayGridMonth",
        events: function (info, successCallback, failureCallback) {
                const tiposSeleccionados = Array.from(document.querySelectorAll('.input-filter:checked'))
                    .map(cb => ({ tipo: cb.dataset.tipo, estatus: cb.dataset.estatus }));
                const eventosFiltrados = l.filter(ev => {
                    return tiposSeleccionados.some(f =>
                        f.tipo === ev.tipo && f.estatus.toLowerCase() === (ev.extendedProps.estatus || '').toLowerCase()
                    );
                });
                successCallback(eventosFiltrados);
         },
        editable: false,
        contentHeight: 'parent',
        dayMaxEvents: 2,
        eventResizableFromStart: true,
        customButtons: {
            sidebarToggle: { text: "Sidebar" }
        },
        headerToolbar: {
            start: "sidebarToggle,prev,next, title",
            end: "dayGridMonth,timeGridWeek,timeGridDay,listMonth"
        },
        locale: "es",
        direction: direction,
        initialDate: new Date(),
        navLinks: true,
        fixedWeekCount: false,
        titleFormat: { month: 'short', year: 'numeric' },
        eventTimeFormat: {
            hour: '2-digit',
            minute: '2-digit',
            hour12: false // o true si quieres 12h con AM/PM
        },
        eventClassNames: function ({ event: e }) {
            return ["fc-event-" + M[e._def.extendedProps.estatus]];
        },
        eventDidMount: function({ event, el }) {
            // le asignamos el title al elemento DOM del evento
            let tooltipContent = `<strong>${event.title}</strong><br>${event.extendedProps.descripcion || ''}`;
            new bootstrap.Tooltip(el, {
                title: tooltipContent,
                html: true, // permite HTML dentro del tooltip
                placement: 'top', // se puede cambiar a 'right', 'left', 'bottom'
                trigger: 'hover',
                container: 'body'
            });
        },
        dateClick: function (e) {
            renderEventosDelDia(e.date);
            p.show();
        },
        eventClick: function (info) {
            const fechaEvento = info.event.start;
            renderEventosDelDia(fechaEvento);

            // Buscar el botón correspondiente al evento clicado
            const lista = document.querySelector('.event-list-scroll ul');
            const btn = lista.querySelector(`.btn-select-event[data-id="${info.event.id}"][data-tipo="${info.event.extendedProps?.tipo || info.event.tipo}"]`);

            if (btn) btn.click(); // Simular clic para disparar el listener que ya hace todo
            p.show();
        },
        datesSet: function () {
            y();
        },
        viewDidMount: function () {
            y();
        }
    });

    function w() {
        if (c) c.value = "";
        if (Y) Y.value = "";
        if (s) s.value = "";
        if (F) F.value = "";
        if (C) C.value = "";
        if (m) m.checked = false;
        if (v) v.val("").trigger("change");
        if (V) V.value = "";
    }

    i.render();
    y();
    // Reasigna los inputs después de que el DOM ya esté montado
    const filtros = document.querySelectorAll(".input-filter");
    filtros.forEach(filtro => {
        filtro.addEventListener("change", () => {
            const total = filtros.length;
            const checked = document.querySelectorAll(".input-filter:checked").length;
            B.checked = total === checked;
            i.refetchEvents();
        });
    });

    B && B.addEventListener("click", e => {
                const checked = e.currentTarget.checked;
                document.querySelectorAll(".input-filter").forEach(cb => cb.checked = checked);
                i.refetchEvents();
            });

    D.addEventListener("hidden.bs.offcanvas", function () {
        w();
    });

    if (T) {
        T.addEventListener("click", e => {
            o.classList.add("d-none");
            d.classList.add("d-none");
            n.classList.remove("d-none");
            q.classList.remove("show");
            P.classList.remove("show");
        });
    }
});