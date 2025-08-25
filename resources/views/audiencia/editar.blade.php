@extends ('layouts.app')

@section('title') Audiencia @endsection

@section('link')
    <!-- Vendors CSS -->
    <link rel="stylesheet" href="{{ asset('sneat/assets/vendor/libs/bs-stepper/bs-stepper.css') }}" />
    <link rel="stylesheet" href="{{ asset('sneat/assets/vendor/libs/bootstrap-select/bootstrap-select.css') }}" />
    <link rel="stylesheet" href="{{ asset('sneat/assets/vendor/libs/select2/select2.css') }}" />
    <link rel="stylesheet" href="{{ asset('sneat/assets/vendor/libs/formvalidation/dist/css/formValidation.min.css') }}" />

@endsection

@section('content')
<!-- Basic Layout -->
<div class="row justify-content-center">
  <div class="col-md-8 col-lg-6">
    <div class="card mb-4">
      <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Editar Audiencia</h5>
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

        <form method="POST" action="{{ route('audiencias.update', $audiencia->id) }}">
        @csrf
        @method('PUT')
          <!-- Nombre -->
          <div class="mb-3">
            <label class="form-label" for="formValidationName">Nombre</label>
            <input type="text" name="formValidationName" id="formValidationName" class="form-control" value="{{ old('formValidationName', $audiencia->nombre) }}" placeholder="Reunión" required />
          </div>

          <!-- Asunto -->
          <div class="mb-3">
            <label class="form-label" for="formValidationAsunto">Asunto</label>
            <input type="text" name="formValidationAsunto" id="formValidationAsunto" class="form-control" value="{{ old('formValidationAsunto', $audiencia->asunto_audiencia) }}" placeholder="Asunto de la audiencia" required />
          </div>

          <!-- Lugar -->
          <div class="mb-3">
            <label class="form-label" for="formValidationLugar">Lugar</label>
            <input type="text" name="formValidationLugar" id="formValidationLugar" class="form-control" placeholder="Lugar" value="{{ old('formValidationLugar', $audiencia->lugar) }}" required />
          </div>

          <!-- Fecha -->
          <div class="mb-3">
            <label class="form-label" for="formValidationFecha">Fecha</label>
            <input type="date" name="formValidationFecha" id="formValidationFecha" class="form-control" value="{{ old('formValidationFecha', $audiencia->fecha_audiencia) }}" required />
          </div>

          <!-- Hora inicio -->
          <div class="mb-3">
            <label class="form-label" for="hora_audiencia">Hora de inicio</label>
            <input type="time" name="hora_audiencia" id="hora_audiencia" class="form-control" value="{{ old('hora_audiencia', $audiencia->hora_audiencia) }}" required />
          </div>
          <!-- Hora fin -->
          <div class="mb-3">
            <label class="form-label" for="hora_fin_audiencia">Hora de finalizacion</label>
            <input type="time" name="hora_fin_audiencia" id="hora_fin_audiencia" class="form-control" value="{{ old('hora_fin_audiencia', $audiencia->hora_fin_audiencia) }}" required />
          </div>

          <!-- Procedencia -->
          <div class="mb-3">
            <label class="form-label" for="procedencia">Procedencia</label>
            <input type="text" name="procedencia" id="procedencia" class="form-control" value="{{ old('procedencia', $audiencia->procedencia) }}"/>
          </div>

          <!-- Estatus -->
          <div class="mb-3">
            <label class="form-label" for="estatus_id">Estatus</label>
            <select class="form-select" id="estatus_id" name="estatus_id">
              <option value="">Seleccione un estatus</option>
              {{-- Aquí se debe iterar estatusList si está disponible --}}
                @foreach($estatusLista as $estatus)
                    <option value="{{ $estatus->id }}" {{ old('estatus_id', $audiencia->estatus_id) == $estatus->id ? 'selected' : '' }}>
                        {{ $estatus->estatus }}
                    </option>
                @endforeach
            </select>
          </div>

          <!-- Descripción -->
          <div class="mb-3">
            <label class="form-label" for="descripcion">Descripción</label>
            <textarea class="form-control" id="descripcion" name="descripcion" rows="3">{{ old('descripcion', $audiencia->descripcion) }}</textarea>
          </div>

          <!-- Botón -->
          <div class="text-end">
            <a href="{{ route('dashboard') }}" class="btn btn-warning text-white">Cancelar</a>
            <button type="submit" class="btn btn-primary">Guardar cambios</button>
          </div>

        </form>
      </div>
    </div>
  </div>
</div>

@endsection


@section('script')
  <!-- Vendors JS -->
  <script src="{{ asset('sneat/assets/vendor/libs/bs-stepper/bs-stepper.js') }}"></script>
<script src="{{ asset('sneat/assets/vendor/libs/bootstrap-select/bootstrap-select.js') }}"></script>
<script src="{{ asset('sneat/assets/vendor/libs/select2/select2.js') }}"></script>

<script>
  document.addEventListener('DOMContentLoaded', function () {
    const form = document.querySelector('form');

    form.addEventListener('submit', function (e) {
      const inputs = form.querySelectorAll('input, select, textarea');
      let valid = true;

      inputs.forEach(input => {
        input.classList.remove('is-invalid');

        // Validar solo los campos requeridos
        if (input.hasAttribute('required') && !input.value.trim()) {
          input.classList.add('is-invalid');
          valid = false;
        }
      });

      if (!valid) {
        e.preventDefault(); // Evita el envío si hay errores
      }
    });
  });
</script>
@endsection
