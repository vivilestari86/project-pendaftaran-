{{-- resources/views/admin/dasboard/partials/stat-cards.blade.php --}}

<div class="stat-grid">

    {{-- Data Lengkap --}}
    <div class="stat-card">
        <div class="stat-card-top">
            <div class="stat-icon success">✔</div>
            <span class="stat-delta up">↑ 12.5%</span>
        </div>
        <div class="stat-label">Data Lengkap</div>
        <div class="stat-value">{{ number_format($totalLengkap, 0, ',', '.') }}</div>
    </div>

    {{-- Data Belum Lengkap --}}
    <div class="stat-card">
        <div class="stat-card-top">
            <div class="stat-icon warning">⏱</div>
            <span class="stat-delta down">↓ 4.2%</span>
        </div>
        <div class="stat-label">Data Belum Lengkap</div>
        <div class="stat-value">{{ number_format($totalBelumLengkap, 0, ',', '.') }}</div>
    </div>

    {{-- Pendaftar Baru Hari Ini --}}
    <div class="stat-card">
        <div class="stat-card-top">
            <div class="stat-icon primary">👤</div>
            <span class="stat-delta up">Target: {{ $targetHarian }}/Hari</span>
        </div>
        <div class="stat-label">Pendaftar Baru Hari Ini</div>
        <div class="stat-value">{{ number_format($pendaftarHariIni, 0, ',', '.') }}</div>
    </div>

    {{-- Tingkat Kelengkapan --}}
    <div class="stat-card">
        <div class="stat-card-top">
            <div class="stat-icon info">📊</div>
            <span class="stat-delta up">↑ 2.1%</span>
        </div>
        <div class="stat-label">Tingkat Kelengkapan</div>
        <div class="stat-value">{{ $tingkatKelengkapan }}%</div>
    </div>

</div>