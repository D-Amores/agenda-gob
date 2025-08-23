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
        <h2 class="fw-bold mb-1 text-success">{{$numeroAudiencia}}</h2>

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
        <h2 class="fw-bold mb-1 text-warning">{{$numeroEventos}}</h2>

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
</style>


<div class="row">
    <!-- Line Area Chart -->
    <div class="col-12 mb-4">
        <div class="card">
            <div class="card-header d-flex justify-content-between">
                <div>
                    <h5 class="card-title mb-0">{{ $tituloGrafica }}</h5>
                </div>

                <div class="dropdown">
                    <div>
                        <h5 class="card-title mb-0"> Eventos y Audiencias </h5>
                    </div>
                    <button type="button" class="btn dropdown-toggle px-0" data-bs-toggle="dropdown" aria-expanded="false"><i class="bx bx-calendar"></i></button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><a href="javascript:void(0);" class="dropdown-item d-flex align-items-center">Today</a></li>
                        <li><a href="javascript:void(0);" class="dropdown-item d-flex align-items-center">Yesterday</a></li>
                        <li><a href="javascript:void(0);" class="dropdown-item d-flex align-items-center">Last 7 Days</a></li>
                        <li><a href="javascript:void(0);" class="dropdown-item d-flex align-items-center">Last 30 Days</a></li>
                    </ul>
                </div>
            </div>

            <div class="card-body">
                <div id="chart"></div>
            </div>
        </div>
    </div>
    <!-- /Line Area Chart -->
</div>


@endsection

@section('script')
  <!-- Page JS -->
  <script src="{{ asset('sneat/assets/js/charts-apex.js') }}"></script>

  <script type="text/javascript">

  var fechasTodas = @json($fechasTodas);
  var audiencias = @json($audienciasData);
  var eventos = @json($eventosData);

var options = {
  chart: {
    height: 380,
    type: "area",
    background: "transparent",
    toolbar: { show: false }
  },
  stroke: {
    curve: "smooth",
    width: 3
  },
  dataLabels: { enabled: false },
  colors: ["#28c76f", "#ff9f43"], // verde y naranja
  fill: {
    type: "gradient",
    gradient: {
      shade: "light",
      type: "vertical",
      shadeIntensity: 0.4,
      gradientToColors: ["#81fbb8", "#ffd26f"],
      opacityFrom: 0.7,
      opacityTo: 0.1,
      stops: [0, 100]
    }
  },
  grid: {
    borderColor: "#e7e7e7",
    strokeDashArray: 4,
    yaxis: { lines: { show: true } }
  },
  markers: {
    size: 5,
    colors: ["#fff"],
    strokeColors: ["#28c76f", "#ff9f43"],
    strokeWidth: 3,
    hover: { size: 7 }
  },
  series: [
    { name: "Audiencias", data: audiencias },
    { name: "Eventos", data: eventos }
  ],
  xaxis: {
    categories: fechasTodas,
    labels: { style: { fontSize: "12px" } }
  },
  yaxis: {
    decimalsInFloat: 0,
    labels: {
      formatter: function (val) {
        return Math.round(val);
      }
    }
  },
  tooltip: {
    shared: true,
    intersect: false,
    x: { format: "dd MMM yyyy" },
    y: {
      formatter: function (val, opts) {
          let icon = opts.seriesIndex === 0 ? "👥" : "📅"; // 👥 para Audiencias, 📅 para Eventos
          return icon + " " + Math.round(val);
      }
    }
  },
  legend: {
    position: "top",
    horizontalAlign: "right",
    markers: { radius: 12 }
  }
};


  var chart = new ApexCharts(document.querySelector("#chart"), options);
  chart.render();

</script>


@endsection
