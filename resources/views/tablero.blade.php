@extends ('layouts.app')

@section('title')
    Tablero
@endsection

@section('content')

<div class="row">
  <!-- Tarjeta Audiencias -->
  <div class="col-md-6 mb-4">
    <div class="card shadow-sm border-0 h-100 hover-shadow" style="transition: all 0.3s ease;">
      <div class="card-body text-center p-4">
        <!-- Icono -->
        <div class="avatar avatar-xl rounded-circle mx-auto mb-3"
             style="background: linear-gradient(135deg, #28c76f, #81fbb8); display: flex; align-items: center; justify-content: center;">
          <i class="bx bx-user-voice bx-lg text-white"></i>
        </div>

        <!-- Número -->
        <h2 id="aud-count" class="fw-bold mb-1 text-success">{{$numeroAudiencia}}</h2>

        <!-- Subtítulo -->
        <p class="text-muted mb-0">Audiencias Registradas</p>
      </div>
    </div>
  </div>

  <!-- Tarjeta Eventos -->
  <div class="col-md-6 mb-4">
    <div class="card shadow-sm border-0 h-100 hover-shadow" style="transition: all 0.3s ease;">
      <div class="card-body text-center p-4">
        <!-- Icono -->
        <div class="avatar avatar-xl rounded-circle mx-auto mb-3"
             style="background: linear-gradient(135deg, #ff9f43, #ffd26f); display: flex; align-items: center; justify-content: center;">
          <i class="bx bx-calendar-event bx-lg text-white"></i>
        </div>

        <!-- Número -->
        <h2 id="evt-count" class="fw-bold mb-1 text-warning">{{$numeroEventos}}</h2>

        <!-- Subtítulo -->
        <p class="text-muted mb-0">Eventos Registrados</p>
      </div>
    </div>
  </div>
</div>

<style>
  .hover-shadow:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 20px rgba(0,0,0,0.1);
  }
  #custom-range { display: none; gap: .5rem; align-items: center; }
</style>


<div class="row">
    <!-- Line Area Chart -->
    <div class="col-12 mb-4">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <div>
                    <!-- Titular ahora controlado por JS -->
                    <h5 id="chart-title" class="card-title mb-0"></h5>
                </div>

                <div class="dropdown">
                    <button type="button" class="btn dropdown-toggle px-0" data-bs-toggle="dropdown" aria-expanded="false"><i class="bx bx-calendar"></i></button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><a href="#" class="dropdown-item" data-filter="mis">Mis Eventos y Audiencias</a></li>
                        <li><a href="#" class="dropdown-item" data-filter="area">Eventos y Audiencias del Area</a></li>
                        <li><a href="#" class="dropdown-item" data-filter="7dias">Próximos 7 Dias</a></li>
                        <li><a href="#" class="dropdown-item" data-filter="30dias">Próximos 30 Dias</a></li>
                        <li><a href="#" class="dropdown-item" data-filter="personalizado">Personalizado</a></li>
                    </ul>
                </div>
            </div>

            <div class="card-body">
                <div id="chart"></div>

                <div id="custom-range" class="mt-3">
                    <input type="date" id="custom-start" class="form-control form-control-sm" />
                    <input type="date" id="custom-end" class="form-control form-control-sm" />
                    <button id="custom-apply" class="btn btn-sm btn-primary">Aplicar</button>
                    <button id="custom-cancel" class="btn btn-sm btn-outline-secondary">Cancelar</button>
                </div>
            </div>
        </div>
    </div>
    <!-- /Line Area Chart -->
</div>


@endsection

@section('script')
  <!-- librerías necesarias -->
  <script src="{{ asset('sneat/assets/js/charts-apex.js') }}"></script>

  <script type="text/javascript">
    var fechasTodas = @json($fechasTodas);
    var audiencias = @json($audienciasData);
    var eventos = @json($eventosData);
  </script>

  <script src="{{ asset('js/dashboard/tablero.js') }}"></script>
@endsection
