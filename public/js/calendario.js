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
            Reunion: "primary",         // Reuniones internas de la secretaría
            Audiencia: "success",       // Audiencias con ciudadanos o grupos
            Sesion: "warning",          // Sesiones de consejo, comité o junta
            Plazo: "danger",            // Fechas límite o vencimientos importantes
            EventoPublico: "info"       // Eventos abiertos al público o comunicados
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
        I = [].slice.call(document.querySelectorAll(".input-filter")),
        R = document.querySelector(".inline-calendar");

    let a, l = [], r = !1, e;

// Función para sumar una hora si hora_fin_audiencia es null
function addOneHour(dateStr, timeStr) {
        let dt = timeStr
            ? new Date(`${dateStr}T${timeStr}`)
            : new Date(`${dateStr}T00:00:00`);
        if (isNaN(dt.getTime())) dt = new Date();
        dt.setHours(dt.getHours() + 1);
        return dt;
    }


// Cargar audiencias desde la variable global `audiencias` generada en Blade
    if (typeof audiencias !== 'undefined') {
        console.log("AUDIENCIAS:", audiencias);

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
            title: a.asunto_audiencia || 'Sin título',
            start: start,
            end: end,
            allDay: false,
            extendedProps: {
                descripcion: a.descripcion || '',
                lugar: a.lugar || '',
                calendar: a.tipo || 'EventoPublico', // importante para colores
                user: a.user?.name || '',
                estatus: a.estatus?.nombre || ''
            }
        };
    });

    }
    console.log("AUDIENCIAS PROCESADAS:", l);


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

    //var { dayGrid: S, interaction: L, timeGrid: E, list: k } = calendarPlugins;
    
    let i = new FullCalendar.Calendar(x, {
        themeSystem: 'standard',
        initialView: "dayGridMonth",
        events: l, // por ahora vacío
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
        titleFormat: { month: 'short', year: 'numeric' },
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
            p.show();
        },
        eventClick: function (e) {
          p.show();

        },
        datesSet: function () {
            // Cambia SOLO el hover de los botones, respetando el resto de estilos de Sneat
            document.documentElement.style.setProperty('--fc-button-hover-bg-color', '#7b1fa2');
            document.documentElement.style.setProperty('--fc-button-hover-border-color', '#7b1fa2');
            document.documentElement.style.setProperty('--fc-button-hover-text-color', '#fff');
            // Cambiar cursor en días del mes anterior/siguiente
            document.querySelectorAll('.fc-day-other').forEach(cell => {
                cell.style.cursor = 'not-allowed';
            });
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

    B &&
        B.addEventListener("click", e => {
            e.currentTarget.checked
                ? document.querySelectorAll(".input-filter").forEach(e => (e.checked = 1))
                : document.querySelectorAll(".input-filter").forEach(e => (e.checked = 0));
            i.refetchEvents();
        });

    I &&
        I.forEach(e => {
            e.addEventListener("click", () => {
                document.querySelectorAll(".input-filter:checked").length <
                document.querySelectorAll(".input-filter").length
                    ? (B.checked = !1)
                    : (B.checked = !0);
                i.refetchEvents();
            });
        });

    void e.config.onChange.push(function (e) {
        i.changeView(i.view.type, moment(e[0]).format("YYYY-MM-DD")), y(), q.classList.remove("show"), P.classList.remove("show");
    });
});