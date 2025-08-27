(function (window) {
  var fpStart, fpEnd;

  function setActiveItem(el) {
    document.querySelectorAll('.dropdown-item[data-filter]').forEach(i => i.classList.remove('active'));
    if (el) el.classList.add('active');
  }

  function showCustomRange() { document.getElementById('custom-range').style.display = 'flex'; }
  function hideCustomRange() { document.getElementById('custom-range').style.display = 'none'; }

  function updateCounters(data) {
    try {
      var totalAud = (Array.isArray(data.audiencias) ? data.audiencias : []).reduce((s, v) => s + Number(v || 0), 0);
      var totalEvt = (Array.isArray(data.eventos) ? data.eventos : []).reduce((s, v) => s + Number(v || 0), 0);
      var audEl = document.getElementById('aud-count');
      var evtEl = document.getElementById('evt-count');
      if (audEl) audEl.textContent = totalAud;
      if (evtEl) evtEl.textContent = totalEvt;
    } catch (e) { console.error('Error updating counters', e); }
  }

  function fetchChartData(filter, params) {
    params = params || {};
    var url = '/dashboard/chart-data?filter=' + encodeURIComponent(filter);
    if (params.start) url += '&start=' + encodeURIComponent(params.start);
    if (params.end) url += '&end=' + encodeURIComponent(params.end);

    return fetch(url)
      .then(res => { if (!res.ok) throw new Error('Network response was not ok'); return res.json(); })
      .then(data => {
        window.DashboardChart.update(data);
        updateCounters(data);
        return data;
      })
      .catch(err => { console.error('Error fetching chart data:', err); throw err; });
  }

  function initFlatpickr() {
    if (typeof flatpickr === 'undefined') return;
    fpStart = flatpickr("#custom-start", { dateFormat: "Y-m-d", minDate: "today", onChange: function (selectedDates, dateStr) { if (selectedDates.length && fpEnd) fpEnd.set('minDate', dateStr); } });
    fpEnd = flatpickr("#custom-end", { dateFormat: "Y-m-d", minDate: "today" });
  }

  function initUI(initialFilter) {
    document.querySelectorAll('.dropdown-item[data-filter]').forEach(function (item) {
      item.addEventListener('click', function (e) {
        e.preventDefault();
        var filter = this.getAttribute('data-filter');
        setActiveItem(this);
        document.getElementById('chart-title').textContent = this.textContent.trim();

        if (filter === 'personalizado') {
          showCustomRange();
          // default dates if empty
          var startEl = document.getElementById('custom-start');
          var endEl = document.getElementById('custom-end');
          var todayStr = new Date().toISOString().slice(0, 10);
          var plus6 = new Date(); plus6.setDate(plus6.getDate() + 6); var plus6Str = plus6.toISOString().slice(0, 10);
          if (startEl && !startEl.value) { startEl.value = todayStr; if (fpStart) fpStart.setDate(todayStr, true); if (fpEnd) fpEnd.set('minDate', todayStr); }
          if (endEl && !endEl.value) { endEl.value = plus6Str; if (fpEnd) fpEnd.setDate(plus6Str, true); }
          return;
        } else {
          hideCustomRange();
        }

        fetchChartData(filter);
      });
    });

    document.getElementById('custom-apply').addEventListener('click', function (e) {
      e.preventDefault();
      var start = document.getElementById('custom-start').value;
      var end = document.getElementById('custom-end').value;
      if (!start || !end) { console.error('Start and end dates required'); return; }
      if (new Date(end) < new Date(start)) { console.error('End must be after or equal to start'); return; }
      var personalItem = document.querySelector('.dropdown-item[data-filter="personalizado"]');
      setActiveItem(personalItem);
      document.getElementById('chart-title').textContent = personalItem.textContent.trim();
      fetchChartData('personalizado', { start: start, end: end });
    });

    document.getElementById('custom-cancel').addEventListener('click', function (e) {
      e.preventDefault();
      hideCustomRange();
      var first = document.querySelector('.dropdown-item[data-filter]');
      setActiveItem(first);
      document.getElementById('chart-title').textContent = first ? first.textContent.trim() : '';
      if (first) fetchChartData(first.getAttribute('data-filter'));
    });

    // inicializar estado por defecto
    var first = document.querySelector('.dropdown-item[data-filter]');
    if (first) {
      setActiveItem(first);
      document.getElementById('chart-title').textContent = first.textContent.trim();
      fetchChartData(first.getAttribute('data-filter')).catch(()=>{});
    }
  }

  // public init
  document.addEventListener('DOMContentLoaded', function () {
    initFlatpickr();
    // inicializar chart con datos pasados desde blade si existen
    var initData = window.__DASHBOARD_INITIAL__ || {};
    if (window.DashboardChart && typeof window.DashboardChart.init === 'function') {
      window.DashboardChart.init(initData);
    }
    initUI();
  });

})(window);