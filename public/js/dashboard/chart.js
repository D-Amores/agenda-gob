(function (window) {
  // Depende de ApexCharts global
  var chart = null;

  function buildOptions(initial) {
    return {
      chart: { height: 380, type: "area", background: "transparent", toolbar: { show: false } },
      stroke: { curve: "smooth", width: 3 },
      dataLabels: { enabled: false },
      colors: ["#28c76f", "#ff9f43"],
      fill: { type: "gradient", gradient: { shade: "light", type: "vertical", shadeIntensity: 0.4, gradientToColors: ["#81fbb8", "#ffd26f"], opacityFrom: 0.7, opacityTo: 0.1, stops: [0, 100] } },
      grid: { borderColor: "#e7e7e7", strokeDashArray: 4, yaxis: { lines: { show: true } } },
      markers: { size: 5, colors: ["#fff"], strokeColors: ["#28c76f", "#ff9f43"], strokeWidth: 3, hover: { size: 7 } },
      series: [
        { name: "Audiencias", data: initial.audiencias || [] },
        { name: "Eventos", data: initial.eventos || [] }
      ],
      xaxis: { categories: initial.fechas || [], labels: { style: { fontSize: "12px" } } },
      yaxis: { decimalsInFloat: 0 },
      tooltip: {
        shared: true,
        intersect: false,
        x: { format: "dd MMM yyyy" },
        y: {
          formatter: function (val, opts) {
            var icon = opts.seriesIndex === 0 ? "👥" : "📅";
            return icon + " " + Math.round(val);
          }
        }
      },
      legend: { position: "top", horizontalAlign: "right", markers: { radius: 12 } }
    };
  }

  window.DashboardChart = {
    init: function (initial) {
      var el = document.querySelector('#chart');
      if (!el) return;
      if (chart) chart.destroy();
      var opts = buildOptions(initial || {});
      chart = new ApexCharts(el, opts);
      chart.render();
      return this;
    },
    update: function (data) {
      if (!chart) return;
      chart.updateOptions({ xaxis: { categories: data.fechas || [] } }, false, true);
      chart.updateSeries([
        { name: "Audiencias", data: data.audiencias || [] },
        { name: "Eventos", data: data.eventos || [] }
      ]);
    },
    getInstance: function () { return chart; },
    destroy: function () { if (chart) { chart.destroy(); chart = null; } }
  };
})(window);