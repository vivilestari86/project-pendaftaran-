<div class="stat-grid">
    <div class="stat-card">
        <div class="stat-card-top">
            <div class="stat-icon success">
                <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M20 6 9 17l-5-5"/></svg>
            </div>
            <span class="stat-delta up">+ 12.5%</span>
        </div>
        <div class="stat-label">Data Lengkap</div>
        <div class="stat-value">{{ number_format($totalLengkap, 0, ',', '.') }}</div>
    </div>

    <div class="stat-card">
        <div class="stat-card-top">
            <div class="stat-icon warning">
                <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 8v5l3 2"/><circle cx="12" cy="12" r="9"/></svg>
            </div>
            <span class="stat-delta down">- 4.2%</span>
        </div>
        <div class="stat-label">Data Belum Lengkap</div>
        <div class="stat-value">{{ number_format($totalBelumLengkap, 0, ',', '.') }}</div>
    </div>

    <div class="stat-card">
        <div class="stat-card-top">
            <div class="stat-icon primary">
                <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M19 8v6M22 11h-6"/></svg>
            </div>
            <span class="stat-delta neutral">Target: {{ $targetHarian }}</span>
        </div>
        <div class="stat-label">Pendaftar Baru Hari Ini</div>
        <div class="stat-value">{{ number_format($pendaftarHariIni, 0, ',', '.') }}</div>
    </div>

    <div class="stat-card">
        <div class="stat-card-top">
            <div class="stat-icon info">
                <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M4 19V9M12 19V5M20 19v-8"/></svg>
            </div>
            <span class="stat-delta up">+ 2.1%</span>
        </div>
        <div class="stat-label">Tingkat Kelengkapan</div>
        <div class="stat-value">{{ $tingkatKelengkapan }}%</div>
    </div>
</div>
