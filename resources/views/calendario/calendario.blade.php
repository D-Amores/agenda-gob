@extends ('layouts.app')

@section('title')
    Calendario
@endsection

@section('link')
    <!-- Vendors CSS -->
    <link rel="stylesheet" href="{{ asset('sneat/assets/vendor/libs/select2/select2.css') }}" />
    <link rel="stylesheet" href="{{ asset('sneat/assets/vendor/libs/formvalidation/dist/css/formValidation.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('sneat/assets/vendor/libs/fullcalendar/fullcalendar.css') }}" />
    <link rel="stylesheet" href="{{ asset('sneat/assets/vendor/libs/flatpickr/flatpickr.css') }}" />
    <link rel="stylesheet" href="{{ asset('sneat/assets/vendor/libs/quill/editor.css') }}" />

    <!-- Page CSS -->
    <link rel="stylesheet" href="{{ asset('sneat/assets/vendor/css/pages/app-calendar.css') }}" />

    <!-- Css personalizado -->
    <link rel="stylesheet" href="{{ asset('css/calendario/calendario.css') }}" />
@endsection

@section('content')
<small class="text-light fw-semibold px-3">Calendario de Actividades</small>
<div class="card app-calendar-wrapper mt-2">
    <div class="row g-0">
        <!-- Calendar Sidebar -->
        <div class="col app-calendar-sidebar personalizado"
        style="background-color: white !important; box-shadow: 3px 7px 15px -3px rgba(0, 0, 0, 0.15) !important;">
                <div class="border-bottom p-4 my-sm-0 mb-3">
                    <div class="d-grid gap-2">
                        <!-- Botón Agregar Evento -->
                        <a href="{{ route('eventos.create') }}" class="btn btn-primary btn-sm">
                            <i class="bx bx-plus"></i>
                            <span class="align-middle">Agregar Evento</span>
                        </a>
                        <!-- Botón Agregar Audiencia -->
                        <a href="{{ route('audiencias.create') }}" class="btn btn-outline-secondary btn-sm">
                            <i class="bx bx-user-plus"></i>
                            <span class="align-middle">Agregar Audiencia</span>
                        </a>
                    </div>
                </div>
                <div class="p-4">
                    <!-- Filter -->
                    <div class="mb-4">
                        <small class="text-small text-muted text-uppercase align-middle">Filtros</small>
                    </div>
                    <div class="form-check mb-2">
                        <input class="form-check-input select-all" type="checkbox" id="selectAll" checked>
                        <label class="form-check-label" for="selectAll">Ver todo</label>
                    </div>

                    <!-- Grupo: Audiencias -->
                    <div class="app-calendar-events-filter">
                        <strong>Audiencias</strong>
                        <div class="form-check form-check-warning mb-2">
                            <input class="form-check-input input-filter estatus-reprogramado" type="checkbox"
                                id="select-reprogramado"data-tipo="audiencia" data-estatus="reprogramado" checked>
                            <label class="form-check-label">Reprogramado</label>
                        </div>
                        <div class="form-check form-check-success mb-2">
                            <input class="form-check-input input-filter" type="checkbox" data-tipo="audiencia"
                                data-estatus="atendido" checked>
                            <label class="form-check-label">Atendido</label>
                        </div>
                        <div class="form-check form-check-danger mb-2">
                            <input class="form-check-input input-filter" type="checkbox" data-tipo="audiencia"
                                data-estatus="cancelado" checked>
                            <label class="form-check-label">Cancelado</label>
                        </div>
                    </div>

                    <hr>

                    <!-- Grupo: Eventos -->
                    <div class="app-calendar-events-filter">
                        <strong>Eventos</strong>
                        <div class="form-check form-check-warning mb-2">
                            <input class="form-check-input input-filter" type="checkbox" data-tipo="evento"
                                data-estatus="reprogramado" checked>
                            <label class="form-check-label">Reprogramado</label>
                        </div>
                        <div class="form-check form-check-success mb-2">
                            <input class="form-check-input input-filter" type="checkbox" data-tipo="evento"
                                data-estatus="atendido" checked>
                            <label class="form-check-label">Atendido</label>
                        </div>
                        <div class="form-check form-check-danger mb-2">
                            <input class="form-check-input input-filter" type="checkbox" data-tipo="evento"
                                data-estatus="cancelado" checked>
                            <label class="form-check-label">Cancelado</label>
                        </div>
                    </div>
                </div>

            </div>
            <!-- /Calendar Sidebar -->

            <!-- Calendar & Modal -->
            <div class="col app-calendar-content ps-3">
                <div class="card shadow-none border-0">
                    <div class="card-body pb-0">
                        <!-- FullCalendar -->
                        <div id="calendar"></div>
                    </div>
                </div>
                <div class="app-overlay"></div>
                <!-- FullCalendar Offcanvas -->
                <div class="offcanvas offcanvas-end event-sidebar" tabindex="-1" id="addEventSidebar"
                    aria-labelledby="addEventSidebarLabel">
                    <div class="offcanvas-header border-bottom">
                        <h5 class="offcanvas-title">Eventos y Audiencias</h5>
                        <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas"
                            aria-label="Close"></button>
                    </div>
                    <div class="offcanvas-body">
                        <!-- Botones de acción -->
                        <div class="d-flex justify-content-center gap-2 mb-3 flex-wrap">
                            <!-- Botón Agregar Evento -->
                            <a href="{{ route('eventos.create') }}" class="btn btn-primary btn-sm w-auto px-3">
                                <i class="bx bx-plus"></i>
                                <span class="align-middle">Agregar Evento</span>
                            </a>

                            <!-- Botón Agregar Audiencia -->
                            <a href="{{ route('audiencias.create') }}"
                                class="btn btn-outline-secondary btn-sm w-auto px-3">
                                <i class="bx bx-user-plus"></i>
                                <span class="align-middle">Agregar Audiencia</span>
                            </a>
                        </div>

                        <hr class="my-3">

                        <!-- Detalles del evento -->
                        <div id="eventForm" class="text-center mb-4 d-none">

                            <div class="mx-auto" style="max-width: 320px;">
                                <div class="mb-2">
                                    <i class="bx bx-calendar-event text-primary fs-5 align-middle me-1"></i>
                                    <span class="fw-semibold">Asunto:</span>
                                    <span class="text-body text-truncate-2" id="asunto"></span>
                                </div>
                                <!-- Grupo de detalles -->
                                <div class="border rounded p-3 bg-light">

                                    <div class="mb-2">
                                        <i class="bx bx-time-five text-success fs-5 align-middle me-1"></i>
                                        <span class="fw-semibold">Hora:</span>
                                        <span class="text-body" id="hora"></span>
                                    </div>

                                    <div class="mb-2">
                                        <i class="bx bx-check-circle text-info fs-5 align-middle me-1"></i>
                                        <span class="fw-semibold">Estatus:</span>
                                        <span class="badge bg-info align-middle" id="estatus"></span>
                                    </div>

                                    <div>
                                        <i class="bx bx-user-pin text-warning fs-5 align-middle me-1"></i>
                                        <span class="fw-semibold">Vestimenta:</span>
                                        <span class="text-body" id="vestimenta"></span>
                                    </div>
                                </div>
                            </div>
                            <!-- Botones de acción del detalle -->
                            <form action="" method="POST" id="formEnviar"
                                class="d-flex justify-content-end gap-2 mb-3 mt-3 flex-wrap">
                                @csrf
                                @method('DELETE')
                                <!-- Botón Editar -->
                                <a class="btn btn-warning btn-sm w-auto text-white px-3 btnAccion" id="btnEditar">
                                    <i class="bx bx-edit"></i>
                                    <span class="align-middle">Editar</span>
                                </a>
                                <!-- Botón Eliminar -->
                                <button type="submit" class="btn btn-danger btn-sm w-auto px-3" id="btnEliminar"
                                    data-id="" data-tipo="">
                                    <i class="bx bx-trash"></i>
                                    <span class="align-middle">Eliminar</span>
                                </button>
                            </form>
                            <hr class="my-3">
                        </div>

                        <h5 class="text-center mb-3 fw-semibold">Eventos del Día</h5>
                        <div class="event-list-scroll">
                            <ul class="list-group">
                                <!-- Aquí se generarán los eventos del día -->
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            <!-- /Calendar & Modal -->
        </div>
    </div>
@endsection

@section('script')
    <script>
        const audiencias = @json($audiencias);
        const eventos = @json($eventos);
        const urlEventoEliminar = "{{ route('eventos.destroy', ['evento' => '__ID__']) }}";
        const urlAudienciaEliminar = "{{ route('audiencias.destroy', ['audiencia' => '__ID__']) }}";
        const csrfToken = "{{ csrf_token() }}";
        const urlEventoEditar = "{{ route('eventos.edit', ['evento' => '__ID__']) }}";
        const urlAudienciaEditar = "{{ route('audiencias.edit', ['audiencia' => '__ID__']) }}";
        const currentUserId = {{ Auth::id() }};
    </script>


    <!-- FullCalendar Core -->
    <script src="{{ asset('js/calendario/index.global.min.js') }}"></script>

    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/locales-all.global.min.js"></script>

    <!-- Vendors JS -->

    <script src="{{ asset('sneat/assets/vendor/libs/formvalidation/dist/js/FormValidation.min.js') }}"></script>
    <script src="{{ asset('sneat/assets/vendor/libs/formvalidation/dist/js/plugins/Bootstrap5.min.js') }}"></script>
    <script src="{{ asset('sneat/assets/vendor/libs/formvalidation/dist/js/plugins/AutoFocus.min.js') }}"></script>
    <script src="{{ asset('sneat/assets/vendor/libs/select2/select2.js') }}"></script>
    <script src="{{ asset('sneat/assets/vendor/libs/flatpickr/flatpickr.js') }}"></script>
    <script src="{{ asset('sneat/assets/vendor/libs/moment/moment.js') }}"></script>
    <script src="{{ asset('js/jquery-confirm/jquery-confirm.js') }}"></script>

    <!-- Page JS -->
    <script src="{{ asset('sneat/assets/js/app-calendar-events.js') }}"></script>
    <script src="{{ asset('js/calendario/calendario.js') }}"></script>
    <script src="{{ asset('js/calendario/calendar-sliderbar.js') }}"></script>
@endsection
