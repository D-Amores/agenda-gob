// This file was extracted from resources/views/tablero.blade.php
// It expects the following globals to be defined BEFORE this script is loaded:
//   fechasTodas, audiencias, eventos

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

// flatpickr initialization (if loaded)
var fpStart = null, fpEnd = null;
function initFlatpickr() {
  if (typeof flatpickr === 'undefined') return;
  fpStart = flatpickr('#custom-start', {
    dateFormat: 'Y-m-d',
    minDate: 'today',
    onChange: function(selectedDates, dateStr) {
      if (selectedDates.length && fpEnd) fpEnd.set('minDate', dateStr);
    }
  });
  fpEnd = flatpickr('#custom-end', { dateFormat: 'Y-m-d', minDate: 'today' });
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
                // default dates when opening personalizado
                var startEl = document.getElementById('custom-start');
                var endEl = document.getElementById('custom-end');
                var todayStr = new Date().toISOString().slice(0,10);
                var plus6 = new Date(); plus6.setDate(plus6.getDate()+6); var plus6Str = plus6.toISOString().slice(0,10);
                if (startEl && !startEl.value) { startEl.value = todayStr; if (fpStart) fpStart.setDate(todayStr, true); if (fpEnd) fpEnd.set('minDate', todayStr); }
                if (endEl && !endEl.value) { endEl.value = plus6Str; if (fpEnd) fpEnd.setDate(plus6Str, true); }
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
    // Usar la URL configurada desde Laravel o fallback a ruta relativa
    let baseUrl = window.dashboardConfig?.chartDataUrl || 'dashboard/chart-data';
    let url = `${baseUrl}?filter=${encodeURIComponent(filter)}`;
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

        // Actualizar tarjetas: sumar las series
        try {
            const totalAud = (Array.isArray(data.audiencias) ? data.audiencias : []).reduce((s, v) => s + Number(v || 0), 0);
            const totalEvt = (Array.isArray(data.eventos) ? data.eventos : []).reduce((s, v) => s + Number(v || 0), 0);
            const audEl = document.getElementById('aud-count');
            const evtEl = document.getElementById('evt-count');
            if (audEl) audEl.textContent = totalAud;
            if (evtEl) evtEl.textContent = totalEvt;
        } catch (e) {
            console.error('Error updating counters:', e);
        }
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
  // init flatpickr after DOM ready
  initFlatpickr();
})();
