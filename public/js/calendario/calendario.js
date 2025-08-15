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
            reprogramado: "warning",  // amarillo
            atendido: "success",      // verde
            cancelado: "danger",       // rojo
            Reunion: "primary",         // Reuniones internas de la secretaría
            Audiencia: "success",       // Audiencias con ciudadanos o grupos
            Sesion: "warning",          // Sesiones de consejo, comité o junta
            Plazo: "danger",            // Fechas límite o vencimientos importantes
            pendiente: "info"       // Eventos abiertos al público o comunicados
        },
        t = document.querySelector(".offcanvas-title"),
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
        //I = [].slice.call(document.querySelectorAll(".input-filter")),
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

        // Establecer datos para acciones
        const btnEditar = document.getElementById("btnEditar");
        const btnEliminar = document.getElementById("btnEliminar");
        btnEditar.dataset.id = event.id;
        btnEditar.dataset.tipo = event.tipo;
        btnEliminar.dataset.id = event.id;
        btnEliminar.dataset.tipo = event.tipo;
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
                title: a.asunto_audiencia || 'Sin título',
                start: start,
                end: end,
                allDay: false,
                extendedProps: {
                    descripcion: a.descripcion || '',
                    lugar: a.lugar || '',
                    calendar: (a.estatus?.estatus || 'pendiente').toLowerCase(),
                    user: a.user?.name || '',
                    estatus: a.estatus?.estatus || '',
                    vestimenta: a.vestimenta?.tipo || 'No especificada'
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
                        vestimenta: e.vestimenta?.tipo || 'No especificada'
                    }
                };
            });
        l = l.concat(eventosProcesados); // Combinar con las audiencias
    }

    const p = new bootstrap.Offcanvas(D);

    function f(e) {
        return e.id
            ? "<span class='badge badge-dot bg-" + $(e.element).data("label") + " me-2'> </span>" + e.text
            : e.text;
    }

    function g(e) {
        return e.id
            ? "<div class='d-flex flex-wrap align-items-center'><div class='avatar avatar-xs me-2'><img src='" +
                  assetsPath +
                  "img/avatars/" +
                  $(e.element).data("avatar") +
                  "' alt='avatar' class='rounded-circle' /></div>" +
                  e.text +
                  "</div>"
            : e.text;
    }

    var h, b;

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

    u.length &&
        u
            .wrap('<div class="position-relative"></div>')
            .select2({
                placeholder: "Select value",
                dropdownParent: u.parent(),
                templateResult: f,
                templateSelection: f,
                minimumResultsForSearch: -1,
                escapeMarkup: function (e) {
                    return e;
                }
            });

    v.length &&
        v
            .wrap('<div class="position-relative"></div>')
            .select2({
                placeholder: "Select value",
                dropdownParent: v.parent(),
                closeOnSelect: !1,
                templateResult: g,
                templateSelection: g,
                escapeMarkup: function (e) {
                    return e;
                }
            });

    s &&
        (h = s.flatpickr({
            enableTime: !0,
            altFormat: "Y-m-dTH:i:S",
            onReady: function (e, t, n) {
                n.isMobile && n.mobileInput.setAttribute("step", null);
            }
        }));

    c &&
        (b = c.flatpickr({
            enableTime: !0,
            altFormat: "Y-m-dTH:i:S",
            onReady: function (e, t, n) {
                n.isMobile && n.mobileInput.setAttribute("step", null);
            }
        }));

    R &&
        (e = R.flatpickr({
            monthSelectorType: "static",
            inline: !0
        }));
    
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
        editable: true,
        dragScroll: true,
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
            return ["fc-event-" + M[e._def.extendedProps.calendar]];
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
        c.value = "";
        Y.value = "";
        s.value = "";
        F.value = "";
        C.value = "";
        m.checked = !1;
        v.val("").trigger("change");
        V.value = "";
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

    let eventFormEl = document.getElementById("eventForm");
    if(eventFormEl ){
        FormValidation.formValidation(eventFormEl , {
            fields: {
                eventTitle: {
                    validators: {
                        notEmpty: { message: "Please enter event title " }
                    }
                },
                eventStartDate: {
                    validators: {
                        notEmpty: { message: "Please enter start date " }
                    }
                },
                eventEndDate: {
                    validators: {
                        notEmpty: { message: "Please enter end date " }
                    }
                }
            },
            plugins: {
                trigger: new FormValidation.plugins.Trigger(),
                bootstrap5: new FormValidation.plugins.Bootstrap5({
                    eleValidClass: "",
                    rowSelector: function (e, t) {
                        return ".mb-3";
                    }
                }),
                submitButton: new FormValidation.plugins.SubmitButton(),
                autoFocus: new FormValidation.plugins.AutoFocus()
            }
        }).on("core.form.valid", function () {
            r = !0;
        });
    }

    T && T.addEventListener("click", e => {
        A.classList.remove("d-none");
    });

    n.addEventListener("click", e => {
        if (r) {
            let e = {
                id: i.getEvents().length + 1,
                tipo: 'evento',
                title: F.value,
                start: s.value,
                end: c.value,
                startStr: s.value,
                endStr: c.value,
                display: "block",
                extendedProps: {
                    location: C.value,
                    guests: v.val(),
                    calendar: u.val(),
                    description: V.value
                }
            };
            Y.value && (e.url = Y.value);
            m.checked && (e.allDay = !0);
            t = e;
            l.push(t);
            i.refetchEvents();
            p.hide();
        }
    });

    d.addEventListener("click", e => {
        var t, n;
        if (r) {
            t = {
                id: a.id,
                tipo: a.tipo || 'evento',
                title: F.value,
                start: s.value,
                end: c.value,
                url: Y.value,
                extendedProps: {
                    location: C.value,
                    guests: v.val(),
                    calendar: u.val(),
                    description: V.value
                },
                display: "block",
                allDay: !!m.checked
            };
            (n = t).id = parseInt(n.id);
            l[l.findIndex(e => e.id === n.id)] = n;
            i.refetchEvents();
            p.hide();
        }
    });

    o.addEventListener("click", e => {
        var t;
        t = parseInt(a.id);
        l = l.filter(function (e) {
            return e.id != t;
        });
        i.refetchEvents();
        p.hide();
    });

    D.addEventListener("hidden.bs.offcanvas", function () {
        w();
    });

    T.addEventListener("click", e => {
        o.classList.add("d-none");
        d.classList.add("d-none");
        n.classList.remove("d-none");
        q.classList.remove("show");
        P.classList.remove("show");
    });

    void e.config.onChange.push(function (e) {
        i.changeView(i.view.type, moment(e[0]).format("YYYY-MM-DD")), y(), q.classList.remove("show"), P.classList.remove("show");
    });
});