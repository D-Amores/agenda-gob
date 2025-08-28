@extends ('layouts.app')

@section('title')
    Audiencia
@endsection

@section('link')
    <!-- Vendors CSS -->
    <link rel="stylesheet" href="{{ asset('sneat/assets/vendor/libs/bs-stepper/bs-stepper.css') }}" />
    <link rel="stylesheet" href="{{ asset('sneat/assets/vendor/libs/bootstrap-select/bootstrap-select.css') }}" />
    <link rel="stylesheet" href="{{ asset('sneat/assets/vendor/libs/select2/select2.css') }}" />
    <link rel="stylesheet" href="{{ asset('sneat/assets/vendor/libs/formvalidation/dist/css/formValidation.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
@endsection

@section('content')
    <div class="row">
        <!-- Validation Wizard -->
        <div class="col-12 mb-4">
            <small class="text-light fw-semibold">Evento</small>
            <div id="wizard-validation" class="bs-stepper mt-2">
                <div class="bs-stepper-header">
                    <div class="step" data-target="#account-details-validation">
                        <button type="button" class="step-trigger">
                            <span class="bs-stepper-circle">1</span>
                            <span class="bs-stepper-label mt-1">
                                <span class="bs-stepper-title">Detalles del Evento</span>
                                <span class="bs-stepper-subtitle">Evento</span>
                            </span>
                        </button>
                    </div>
                    <div class="line">
                        <i class="bx bx-chevron-right"></i>
                    </div>
                    <div class="step" data-target="#personal-info-validation">
                        <button type="button" class="step-trigger">
                            <span class="bs-stepper-circle">2</span>
                            <span class="bs-stepper-label mt-1">
                                <span class="bs-stepper-title">Informacion del Evento</span>
                                <span class="bs-stepper-subtitle">Agrega Informacion del Evento</span>
                            </span>
                        </button>
                    </div>
                    <div class="line">
                        <i class="bx bx-chevron-right"></i>
                    </div>
                    <div class="step" data-target="#social-links-validation">
                        <button type="button" class="step-trigger">
                            <span class="bs-stepper-circle">3</span>
                            <span class="bs-stepper-label mt-1">
                                <span class="bs-stepper-title">Información adicional</span>
                                <span class="bs-stepper-subtitle">Seleccione un estatus e ingrese una descripción
                                    opcional.</span>
                            </span>
                        </button>
                    </div>
                </div>
                <div class="bs-stepper-content">
                    <form id="wizard-validation-form" method="POST" action="{{ route('eventos.store') }}">
                        @csrf
                        <!-- Audiencia Details -->
                        <div id="account-details-validation" class="content">
                            <div class="row g-3">
                                <div class="col-sm-6">
                                    <label class="form-label" for="formValidationName">Nombre del Evento</label>
                                    <input type="text" name="formValidationName" id="formValidationName"
                                        class="form-control" placeholder="Reunion" required minlength="10" />
                                    <div class="invalid-feedback">Debe tener al menos 10 caracteres.</div>
                                </div>
                                <div class="col-sm-6">
                                    <label class="form-label" for="asistenciaGobernador">Asistencia del Gobernador</label>
                                    <select name="asistenciaGobernador" id="asistenciaGobernador" class="form-control"
                                        required>
                                        <option value="" disabled selected>Seleccione...</option>
                                        <option value="1">Sí</option>
                                        <option value="0">No</option>
                                    </select>
                                    <div class="invalid-feedback">Debe seleccionar una opción.</div>
                                </div>

                                <div class="col-sm-6">
                                    <label class="form-label" for="formValidationLugar">Lugar</label>
                                    <input type="text" name="formValidationLugar" id="formValidationLugar"
                                        class="form-control" placeholder="ejemplo" aria-label="john.doe" required
                                        minlength="10" />
                                    <div class="invalid-feedback">Debe tener al menos 10 caracteres.</div>
                                </div>
                                <div class="col-sm-6">
                                    <label class="form-label" for="formValidationFecha">Fecha</label>
                                    <input type="text" name="formValidationFecha" id="formValidationFecha"
                                        class="form-control" aria-label="Fecha" required />
                                </div>

                                <div class="col-12 d-flex justify-content-between">
                                    <button type="button" class="btn btn-label-secondary btn-prev" disabled>
                                        <i class="bx bx-chevron-left bx-sm ms-sm-n2"></i>
                                        <span class="align-middle d-sm-inline-block d-none">Anterior</span>
                                    </button>
                                    <button type="button" class="btn btn-primary btn-next">
                                        <span class="align-middle d-sm-inline-block d-none me-sm-1">Siguiente</span>
                                        <i class="bx bx-chevron-right bx-sm me-sm-n2"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                        <!-- Audiencia Info -->
                        <div id="personal-info-validation" class="content">
                            <div class="row g-3">

                                <div class="col-sm-6">
                                    <label class="form-label" for="vestimenta">Tipo de vestimenta</label>
                                    <select id="vestimenta" name="vestimenta" class="form-control" required>
                                        <option value="" disabled selected>Seleccione un tipo</option>
                                        <option value="1">Formal</option>
                                        <option value="2">Casual</option>
                                        <option value="3">Uniforme</option>
                                        <option value="4">Deportivo</option>
                                    </select>
                                </div>

                                <!-- Hora de Audiencia -->
                                <div class="col-sm-6">
                                    <label class="form-label" for="hora_evento">Hora de inicio</label>
                                    <input type="text" id="hora_evento" name="hora_evento"
                                        class="form-control timepicker" required readonly />
                                </div>

                                <!-- Área (select) -->
                                <div class="col-sm-6">
                                    <label class="form-label">Área</label>
                                    <input type="text" class="form-control"
                                        value="{{ Auth::user()->area->area ?? 'Sin área' }}" readonly>
                                </div>

                                <!-- Hora de Audiencia -->
                                <div class="col-sm-6">
                                    <label class="form-label" for="hora_fin_evento">Hora de finalizacion</label>
                                    <input type="text" id="hora_fin_evento" name="hora_fin_evento"
                                        class="form-control timepicker" required />
                                </div>


                                <!-- Botones de navegación -->
                                <div class="col-12 d-flex justify-content-between">
                                    <button type="button" class="btn btn-primary btn-prev">
                                        <i class="bx bx-chevron-left bx-sm ms-sm-n2"></i>
                                        <span class="align-middle d-sm-inline-block d-none">Anterior</span>
                                    </button>
                                    <button type="button" class="btn btn-primary btn-next">
                                        <span class="align-middle d-sm-inline-block d-none me-sm-1">Siguiente</span>
                                        <i class="bx bx-chevron-right bx-sm me-sm-n2"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                        <!-- Social Links -->
                        <div id="social-links-validation" class="content">

                            <div class="row g-3">
                                <!-- Estatus -->
                                <div class="col-sm-6">
                                    <label class="form-label" for="estatus_id">Estatus</label>
                                    <select class="form-select" id="estatus_id" name="estatus_id" required>
                                        @foreach ($estatusLista as $estatus)
                                            <option value="{{ $estatus->id }}">
                                                {{ ucfirst(strtolower($estatus->estatus)) }}</option>
                                        @endforeach
                                    </select>
                                </div>


                                <!-- Descripción (campo grande, opcional) -->
                                <div class="col-sm-12">
                                    <label class="form-label" for="descripcion">Descripción (opcional)</label>
                                    <textarea class="form-control" id="descripcion" name="descripcion" rows="2"
                                        placeholder="Ingrese una descripción si es necesario..."></textarea>
                                </div>

                                <!-- Botones -->
                                <div class="col-12 d-flex justify-content-between">
                                    <button type="button" class="btn btn-primary btn-prev">
                                        <i class="bx bx-chevron-left bx-sm ms-sm-n2"></i>
                                        <span class="align-middle d-sm-inline-block d-none">Anterior</span>
                                    </button>
                                    <button type="submit" class="btn btn-success btn-next btn-submit">Enviar</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <!-- /Validation Wizard -->

    </div>
@endsection


@section('script')
    <!-- Vendors JS -->
    <script src="{{ asset('sneat/assets/vendor/libs/bs-stepper/bs-stepper.js') }}"></script>
    <script src="{{ asset('sneat/assets/vendor/libs/bootstrap-select/bootstrap-select.js') }}"></script>
    <script src="{{ asset('sneat/assets/vendor/libs/select2/select2.js') }}"></script>
    <!-- <script src="{{ asset('js/evento/establecer-hora-inicio-fin.js') }}"></script> -->
    <!-- jQuery y ClockPicker JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/clockpicker/0.0.7/bootstrap-clockpicker.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

    <script src="{{ asset('js/flatpicker/evento.js') }}"></script>

    <!-- jQuery Confirm -->
    <script src="{{ asset('js/jquery-confirm/jquery-confirm.js') }}"></script>
    <script src="{{ asset('js/evento/store.js') }}"></script>
    <script>
        window.routes = {
            calendarioIndex: "{{ route('calendario.index') }}"
        };
    </script>
@endsection
