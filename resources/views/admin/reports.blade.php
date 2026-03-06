<x-admin-layout title="Laporan">
  <div class="admin-page-header flex flex-col sm:flex-row sm:items-end sm:justify-between gap-3">
    <div>
      <h1>Sales Reports</h1>
      <p>Kelola laporan penjualan Soraya Coffee.</p>
    </div>
    <div class="flex gap-2">
      <a class="tap-44 px-5 py-2.5 rounded-xl font-semibold text-sm {{ $range==='7d' ? 'btn-primary' : 'btn-secondary' }}" href="{{ route('admin.reports', ['range'=>'7d']) }}">7d</a>
      <a class="tap-44 px-5 py-2.5 rounded-xl font-semibold text-sm {{ $range==='30d' ? 'btn-primary' : 'btn-secondary' }}" href="{{ route('admin.reports', ['range'=>'30d']) }}">30d</a>
    </div>
  </div>

  <div class="section-card p-6">
    <p class="font-bold text-gray-900 mb-4">Grafik Penjualan (PAID)</p>
    <canvas id="chart" height="120"></canvas>
    <p class="text-xs text-muted mt-3">Sumber: transaksi order berstatus PAID.</p>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <script>
    const labels = @json($labels);
    const data = @json($values);

    new Chart(document.getElementById('chart'), {
      type: 'line',
      data: {
        labels: labels,
        datasets: [{ label: 'Sales (PAID)', data: data }]
      },
      options: {
        responsive: true,
        plugins: { legend: { display: true } }
      }
    });
  </script>
</x-admin-layout>
