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

    // helpers
    function setActiveItem(el) {
        document.querySelectorAll('.dropdown-item[data-filter]').forEach(i => i.classList.remove('active'));
        if (el) el.classList.add('active');
    }

    function hideCustomRange() {
        document.getElementById('custom-range').style.display = 'none';
    }

    function showCustomRange() {
        document.getElementById('custom-range').style.display = 'flex';
    }

    // attach events to dropdown items
    document.querySelectorAll('.dropdown-item[data-filter]').forEach(function(item) {
        item.addEventListener('click', function(e) {
            e.preventDefault();

            let filter = this.getAttribute('data-filter');

            // Manage active class and update title
            setActiveItem(this);
            document.getElementById('chart-title').textContent = this.textContent.trim();

            // If personalizado, show inputs and wait for confirm
            if (filter === 'personalizado') {
                showCustomRange();
                return;
            } else {
                hideCustomRange();
            }

            fetchChartData(filter);
        });
    });

    // custom range controls
    document.getElementById('custom-apply').addEventListener('click', function(e) {
        e.preventDefault();
        let start = document.getElementById('custom-start').value;
        let end = document.getElementById('custom-end').value;
        if (!start || !end) {
            console.error('Start and end dates required');
            return;
        }
        // Add active to personalizado item and update title
        let personalItem = document.querySelector('.dropdown-item[data-filter="personalizado"]');
        setActiveItem(personalItem);
        document.getElementById('chart-title').textContent = personalItem.textContent.trim();

        fetchChartData('personalizado', { start: start, end: end });
    });

    document.getElementById('custom-cancel').addEventListener('click', function(e){
        e.preventDefault();
        hideCustomRange();
        // restore first item as active
        let first = document.querySelector('.dropdown-item[data-filter]');
        setActiveItem(first);
        document.getElementById('chart-title').textContent = first ? first.textContent.trim() : '';
        if (first) fetchChartData(first.getAttribute('data-filter'));
    });

    // fetch helper
    function fetchChartData(filter, params = {}) {
        let url = `/dashboard/chart-data?filter=${encodeURIComponent(filter)}`;
        if (params.start) url += `&start=${encodeURIComponent(params.start)}`;
        if (params.end) url += `&end=${encodeURIComponent(params.end)}`;

        fetch(url)
        .then(res => {
            if (!res.ok) throw new Error('Network response was not ok');
            return res.json();
        })
        .then(data => {
            chart.updateOptions({
                xaxis: { categories: data.fechas }
            });
            chart.updateSeries([
                { name: "Audiencias", data: data.audiencias },
                { name: "Eventos", data: data.eventos }
            ]);
        })
        .catch(err => {
            console.error('Error fetching chart data:', err);
        });
    }

    // Inicializar: marcar primer item activo y cargar sus datos (evita título servidor)
    (function initDefault() {
        let first = document.querySelector('.dropdown-item[data-filter]');
        if (first) {
            setActiveItem(first);
            document.getElementById('chart-title').textContent = first.textContent.trim();
            fetchChartData(first.getAttribute('data-filter'));
        }
    })();

</script>


@endsection
