<div class="panel">
    <div class="panel-header compact">
        <div>
            <div class="panel-title">Kapasitas Pendaftaran</div>
            <div class="panel-subtitle">Pemakaian kuota pendaftaran</div>
        </div>
    </div>

    <div class="kuota-bar-track">
        <div class="kuota-bar-fill" style="width: {{ min($persentaseKuota, 100) }}%;"></div>
    </div>

    <div class="kuota-info">
        <span>{{ $persentaseKuota }}% Kuota Terpenuhi</span>
        <span>{{ number_format($totalPendaftar, 0, ',', '.') }} / {{ number_format($kuotaTotal, 0, ',', '.') }}</span>
    </div>

    <div class="insight-box">
        <svg viewBox="0 0 24 24" aria-hidden="true"><circle cx="12" cy="12" r="10"/><path d="M12 16v-4M12 8h.01"/></svg>
        <span>Data pendaftaran sedang mengalami peningkatan. Pastikan tim verifikasi siap menangani lonjakan data.</span>
    </div>
</div>
