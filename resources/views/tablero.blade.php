@extends ('layouts.app')

@section('title')
    Tablero
@endsection

@section('content')

<div class="row">
  <div class="col-md-6 mb-4">
    <div class="card">
      <div class="card-header d-flex align-items-center justify-content-between">
        <h6 class="card-title m-0 me-2">Audiencia</h6>
        <!-- <div class="dropdown">
          <button class="btn btn-sm p-0" type="button" id="customersList" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            Hoy <i class="bx bx-chevron-down"></i>
          </button>
          <div class="dropdown-menu dropdown-menu-end" aria-labelledby="customersList">
            <a class="dropdown-item" href="javascript:void(0);">Ayer</a>
            <a class="dropdown-item" href="javascript:void(0);">Ultima Semana</a>
            <a class="dropdown-item" href="javascript:void(0);">Ultimo Mes</a>
          </div>
        </div> -->
      </div>
      <div class="card-body text-center">
        <div class="avatar avatar-md border-5 border-light-success rounded-circle mx-auto mb-4">
          <span class="avatar-initial rounded-circle bg-label-success"><i class="bx bx-user bx-sm"></i></span>
        </div>
        <h3 class="card-title mb-1 me-2">{{$numeroAudiencia}}</h3>
      </div>
    </div>
  </div>

  <div class="col-md-6 mb-4">
    <div class="card">
      <div class="card-header d-flex align-items-center justify-content-between">
        <h6 class="card-title m-0 me-2">Eventos</h6>
        <!-- <div class="dropdown">
          <button class="btn btn-sm p-0" type="button" id="orderReceivedList" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            Hoy <i class="bx bx-chevron-down"></i>
          </button>
          <div class="dropdown-menu dropdown-menu-end" aria-labelledby="orderReceivedList">
            <a class="dropdown-item" href="javascript:void(0);">Ayer</a>
            <a class="dropdown-item" href="javascript:void(0);">Ultima Semana</a>
            <a class="dropdown-item" href="javascript:void(0);">Ultimo Mes</a>
          </div>
        </div> -->
      </div>
      <div class="card-body text-center">
        <div class="avatar avatar-md border-5 border-light-warning rounded-circle mx-auto mb-4">
          <span class="avatar-initial rounded-circle bg-label-warning"><i class="bx bx-archive bx-sm"></i></span>
        </div>
        <h3 class="card-title mb-1 me-2">{{$numeroEventos}}</h3>
      </div>
    </div>
  </div>
</div>

<div class="row">
    <!-- Line Area Chart -->
    <div class="col-12 mb-4">
        <div class="card">
            <div class="card-header d-flex justify-content-between">
                <div>
                    <h5 class="card-title mb-0">Ultimas Actualizaciones</h5>
                </div>
                <!-- <div class="dropdown">
                    <button type="button" class="btn dropdown-toggle px-0" data-bs-toggle="dropdown" aria-expanded="false"><i class="bx bx-calendar"></i></button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><a href="javascript:void(0);" class="dropdown-item d-flex align-items-center">Hoy</a></li>
                        <li><a href="javascript:void(0);" class="dropdown-item d-flex align-items-center">Ayer</a></li>
                        <li><a href="javascript:void(0);" class="dropdown-item d-flex align-items-center">Ultimos 7 Dias</a></li>
                        <li><a href="javascript:void(0);" class="dropdown-item d-flex align-items-center">Ultimos 30 Dias</a></li>
                        <li>
                            <hr class="dropdown-divider">
                        </li>
                        <li><a href="javascript:void(0);" class="dropdown-item d-flex align-items-center">Mes Actual</a></li>
                        <li><a href="javascript:void(0);" class="dropdown-item d-flex align-items-center">Ultimo Mes</a></li>
                    </ul>
                </div> -->
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

  var labels = @json($labels);
  var audiencias = @json($audienciasData);
  var lugares = ['Lugar A', 'Lugar B', 'Lugar C', 'Lugar D', 'Lugar E'];
  var vestimentas = ['Formal', 'Casual', 'Deportivo', 'Formal', 'Informal'];

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
  dataLabels: {
    enabled: false
  },
  colors: ["#4e73df"], // Azul elegante
  fill: {
    type: "gradient",
    gradient: {
      shade: "light",
      type: "vertical",
      shadeIntensity: 0.5,
      gradientToColors: ["#1cc88a"], // Verde degradado
      opacityFrom: 0.6,
      opacityTo: 0.1,
      stops: [0, 100]
    }
  },
  markers: {
    size: 6,
    colors: ["#fff"],
    strokeColors: "#4e73df",
    strokeWidth: 3,
    hover: { size: 8 }
  },
  series: [
    {
      name: "Audiencia",
      data: audiencias
    }
  ],
  xaxis: {
    categories: labels
  },
  tooltip: {
    custom: function({ series, seriesIndex, dataPointIndex, w }) {
      return `
        <div style="
          padding: 12px;
          background: rgba(255, 255, 255, 0.75);
          backdrop-filter: blur(10px);
          -webkit-backdrop-filter: blur(10px);
          color: #333;
          border-radius: 12px;
          font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
          box-shadow: 0 6px 15px rgba(0,0,0,0.12);
          min-width: 220px;
        ">
          <div style="font-weight: 700; font-size: 14px; margin-bottom: 8px; color:#4e73df;">
            ${w.config.xaxis.categories[dataPointIndex]}
          </div>
          <div style="font-size: 13px; margin-bottom: 5px;">
            <span style="color: #28a745;">●</span> Audiencias: 
            <strong>${series[seriesIndex][dataPointIndex]}</strong>
          </div>
          <div style="font-size: 13px; margin-bottom: 5px;">
            <span style="color: #007bff;">●</span> Lugar: 
            <strong>${lugares[dataPointIndex]}</strong>
          </div>
          <div style="font-size: 13px;">
            <span style="color: #ffc107;">●</span> Vestimenta: 
            <strong>${vestimentas[dataPointIndex]}</strong>
          </div>
        </div>
      `;
    }
  }
};

var chart = new ApexCharts(document.querySelector("#chart"), options);
chart.render();


</script>

@endsection
