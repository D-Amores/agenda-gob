@extends ('layouts.app')

@section('title')
    Evento
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
    <!-- Basic Layout -->
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center border-start border-4 border-warning">
                    <h5 class="mb-0">Editar Evento</h5>
                    <small class="text-muted float-end">Formulario de edición</small>
                </div>
                <div class="card-body">
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('eventos.update', $evento->id) }}" id="updateEventoForm">
                        @csrf
                        @method('PUT')
                        <!-- Nombre -->
                        <div class="mb-3">
                            <label class="form-label" for="formValidationName">Nombre</label>
                            <input type="text" name="formValidationName" id="formValidationName" class="form-control"
                                value="{{ old('formValidationName', $evento->nombre) }}" placeholder="Reunión" required />
                        </div>

                        <div class="mb-3">
                            <label class="form-label" for="asistenciaGobernador">Asistencia del Gobernador</label>
                            <select name="asistenciaGobernador" id="asistenciaGobernador" class="form-control" required>
                                <option value="" disabled>Seleccione...</option>
                                <option value="1"
                                    {{ old('asistenciaGobernador', $evento->asistencia_de_gobernador) == 1 ? 'selected' : '' }}>
                                    Sí</option>
                                <option value="0"
                                    {{ old('asistenciaGobernador', $evento->asistencia_de_gobernador) == 0 ? 'selected' : '' }}>
                                    No</option>
                            </select>
                            <div class="invalid-feedback">Debe seleccionar una opción.</div>
                        </div>


                        <!-- Lugar -->
                        <div class="mb-3">
                            <label class="form-label" for="formValidationLugar">Lugar</label>
                            <input type="text" name="formValidationLugar" id="formValidationLugar" class="form-control"
                                placeholder="Lugar" value="{{ old('formValidationLugar', $evento->lugar) }}" required />
                        </div>

                        <!-- Fecha -->
                        <div class="mb-3">
                            <label class="form-label" for="formValidationFecha">Fecha</label>
                            <input type="text" name="formValidationFecha" id="formValidationFecha" class="form-control"
                                value="{{ old('formValidationFecha', $evento->fecha_evento) }}" required />
                        </div>

                        <!-- Hora inicio -->
                        <div class="mb-3 d-flex align-items-center gap-2">
                            <div class="flex-grow-1">
                                <label class="form-label" for="hora_evento">Hora de inicio</label>
                                <input type="text" name="hora_evento" id="hora_evento" class="form-control timepicker"
                                    value="{{ old('hora_evento', $evento->hora_evento) }}" required readonly/>
                            </div>
                            <div class="flex-grow-1">
                                <label class="form-label" for="hora_fin_evento">Hora de finalizacion</label>
                                <input type="text" name="hora_fin_evento" id="hora_fin_evento"
                                    class="form-control timepicker"
                                    value="{{ old('hora_fin_evento', $evento->hora_fin_evento) }}" required readonly/>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label" for="vestimenta">Tipo de vestimenta</label>
                            <select id="vestimenta" name="vestimenta" class="form-control" required>
                                <option value="" disabled>Seleccione un tipo</option>
                                <option value="1"
                                    {{ old('vestimenta', $evento->vestimenta_id) == 1 ? 'selected' : '' }}>Formal</option>
                                <option value="2"
                                    {{ old('vestimenta', $evento->vestimenta_id) == 2 ? 'selected' : '' }}>Casual</option>
                                <option value="3"
                                    {{ old('vestimenta', $evento->vestimenta_id) == 3 ? 'selected' : '' }}>Uniforme
                                </option>
                                <option value="4"
                                    {{ old('vestimenta', $evento->vestimenta_id) == 4 ? 'selected' : '' }}>Deportivo
                                </option>
                            </select>
                        </div>


                        <!-- Estatus -->
                        <div class="mb-3">
                            <label class="form-label" for="estatus_id">Estatus</label>
                            <select class="form-select" id="estatus_id" name="estatus_id">
                                <option value="">Seleccione un estatus</option>
                                {{-- Aquí se debe iterar estatusList si está disponible --}}
                                @foreach ($estatus as $status)
                                    <option value="{{ $status->id }}"
                                        {{ old('estatus_id', $evento->estatus_id) == $status->id ? 'selected' : '' }}>
                                        {{ $status->estatus }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Descripción -->
                        <div class="mb-3">
                            <label class="form-label" for="descripcion">Descripción</label>
                            <textarea class="form-control" id="descripcion" name="descripcion" rows="3">{{ old('descripcion', $evento->descripcion) }}</textarea>
                        </div>

                        <!-- Botón -->
                        <div class="text-end">
                            <a href="{{ route('dashboard') }}" class="btn btn-warning text-white">Cancelar</a>
                            <button type="submit" class="btn btn-primary" id="btnSubmit">Guardar cambios</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection


<!-- @section('script')
    <!-- Vendors JS -->
    <script src="{{ asset('sneat/assets/vendor/libs/bs-stepper/bs-stepper.js') }}"></script>
    <script src="{{ asset('sneat/assets/vendor/libs/bootstrap-select/bootstrap-select.js') }}"></script>
    <script src="{{ asset('sneat/assets/vendor/libs/select2/select2.js') }}"></script>
    <script src="{{ asset('js/vendors/bootstrap-clockpicker.min.js') }}"></script>
    <script src="{{ asset('js/vendors/toastr.min.js') }}"></script>

    <script src="{{ asset('js/flatpicker/editar-evento.js') }}"></script>

    <!-- jQuery Confirm -->
    <script src="{{ asset('js/jquery-confirm/jquery-confirm.js') }}"></script>
    <script src="{{ asset('js/evento/update.js') }}"></script>
    <script>
        window.routes = {
            calendarioIndex: "{{ route('calendario.index') }}"
        };
    </script>
@endsection -->
