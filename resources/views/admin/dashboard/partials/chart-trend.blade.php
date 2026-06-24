{{-- resources/views/admin/dasboard/partials/chart-trend.blade.php --}}

<div class="panel">
    <div class="panel-header">
        <div>
            <div class="panel-title">Tren Pendaftar Mingguan</div>
            <div class="panel-subtitle">Pendaftar = total data masuk per hari, Data Lengkap = pendaftar yang statusnya lengkap di hari yang sama.</div>
        </div>
        <div class="chart-toggle">
            <button type="button" class="active" data-mode="pendaftar">Pendaftar</button>
            <button type="button" data-mode="kelengkapan">Data Lengkap</button>
        </div>
    </div>

    <canvas id="chartTrenMingguan" height="220"></canvas>
</div>
