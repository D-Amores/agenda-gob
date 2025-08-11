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
<div class="row">
  <!-- Validation Wizard -->
  <div class="col-12 mb-4">
    <small class="text-light fw-semibold">Audiencia</small>
    <div id="wizard-validation" class="bs-stepper mt-2">
      <div class="bs-stepper-header">
        <div class="step" data-target="#account-details-validation">
          <button type="button" class="step-trigger">
            <span class="bs-stepper-circle">1</span>
            <span class="bs-stepper-label mt-1">
              <span class="bs-stepper-title">Detalles de la Audiencia</span>
              <span class="bs-stepper-subtitle">Audiencia</span>
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
              <span class="bs-stepper-title">Informacion de la Audiencia</span>
              <span class="bs-stepper-subtitle">Agrega Informacion de la Audiencia</span>
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
              <span class="bs-stepper-subtitle">Seleccione un estatus e ingrese una descripción opcional.</span>
            </span>
          </button>
        </div>
      </div>
      <div class="bs-stepper-content">
        <form id="wizard-validation-form" method="POST" action="{{ route('audiencias.store') }}">
          @csrf
          <!-- Audiencia Details -->
          <div id="account-details-validation" class="content">
            <div class="row g-3">
              <div class="col-sm-6">
                <label class="form-label" for="formValidationName">Nombre</label>
                <input type="text" name="formValidationName" id="formValidationName" class="form-control" placeholder="Reunion" required/>
              </div>
              <div class="col-sm-6">
                <label class="form-label" for="formValidationAsunto">Asunto</label>
                <input type="text" name="formValidationAsunto" id="formValidationAsunto" class="form-control" placeholder="ejemplo" aria-label="john.doe" required/>
              </div>
              <div class="col-sm-6">
                <label class="form-label" for="formValidationLugar">Lugar</label>
                <input type="text" name="formValidationLugar" id="formValidationLugar" class="form-control" placeholder="ejemplo" aria-label="john.doe" required />
              </div>
              <div class="col-sm-6">
                <label class="form-label" for="formValidationFecha">Fecha</label>
                <input type="date" name="formValidationFecha" id="formValidationFecha" class="form-control" value="<?php echo date('Y-m-d'); ?>"  aria-label="Fecha" required/>
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

              <!-- Procedencia -->
              <div class="col-sm-6">
                <label class="form-label" for="procedencia">Procedencia</label>
                <input type="text" id="procedencia" name="procedencia" class="form-control" placeholder="Ingrese la procedencia" />
              </div>

              <!-- Hora de Audiencia -->
              <div class="col-sm-6">
                <label class="form-label" for="hora_audiencia">Hora de Audiencia</label>
                <input type="time" id="hora_audiencia" name="hora_audiencia" class="form-control" required/>
              </div>


              <!-- Área (select) -->
              <div class="col-sm-6">
                <label class="form-label" for="area_id">Área</label>
                <select class="form-select" id="area_id" name="area_id">
                  <option value="">Seleccione un área</option>
                  <option value="1" selected>Área Legal</option>
                  <option value="2">Área Administrativa</option>
                  <option value="3">Área Técnica</option>
                  <!-- Agrega más opciones si es necesario -->
                </select>
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
                  <option value="">Seleccione un estatus</option>
                  @foreach($estatusList as $estatus)
                    <option value="{{ $estatus->id }}">{{ $estatus->estatus }}</option>
                  @endforeach
                </select>
              </div>


              <!-- Descripción (campo grande, opcional) -->
              <div class="col-sm-12">
                <label class="form-label" for="descripcion">Descripción (opcional)</label>
                <textarea class="form-control" id="descripcion" name="descripcion" rows="2" placeholder="Ingrese una descripción si es necesario...">
                </textarea>
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

<script>
  // Establece la hora actual en formato HH:MM
  const now = new Date();
  const hours = String(now.getHours()).padStart(2, '0');
  const minutes = String(now.getMinutes()).padStart(2, '0');
  document.getElementById('hora_audiencia').value = `${hours}:${minutes}`;
</script>
<script>
  document.addEventListener('DOMContentLoaded', function () {
    const wizardEl = document.querySelector('#wizard-validation');
    const stepper = new Stepper(wizardEl, {
      linear: true,
      animation: true
    });

    const form = document.querySelector('#wizard-validation-form');
    const steps = form.querySelectorAll('.content');
    const nextButtons = form.querySelectorAll('.btn-next:not(.btn-submit)');
    const prevButtons = form.querySelectorAll('.btn-prev');
    const submitButton = form.querySelector('.btn-submit');

    function validateStep(stepElement) {
      const inputs = stepElement.querySelectorAll('input, select, textarea');
      let valid = true;

      inputs.forEach(input => {
        input.classList.remove('is-invalid');

        // Validar campos requeridos
        if (input.hasAttribute('required') && !input.value.trim()) {
          input.classList.add('is-invalid');
          valid = false;
        }
      });

      return valid;
    }

    nextButtons.forEach(button => {
      button.addEventListener('click', function () {
        const currentStep = form.querySelector('.content.active');

        if (validateStep(currentStep)) {
          stepper.next();
        }
      });
    });

    prevButtons.forEach(button => {
      button.addEventListener('click', function () {
        stepper.previous();
      });
    });

    submitButton.addEventListener('click', function () {
      const currentStep = form.querySelector('.content.active');

      if (validateStep(currentStep)) {
        // Aquí puedes hacer envío AJAX si lo deseas
        form.submit();
      }
    });
  });
</script>

@endsection