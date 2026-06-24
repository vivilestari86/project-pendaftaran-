{{-- resources/views/admin/dasboard/partials/chart-trend.blade.php --}}

<div class="panel">
    <div class="panel-header">
        <div>
            <div class="panel-title">Tren Pendaftar Mingguan</div>
            <div class="panel-subtitle">Volume pendaftar harian dalam satu minggu</div>
        </div>
        <div class="chart-toggle">
            <button type="button" class="active" data-mode="pendaftar">Pendaftar</button>
            <button type="button" data-mode="kelengkapan">Kelengkapan</button>
        </div>
    </div>

    <canvas id="chartTrenMingguan" height="220"></canvas>
</div>