{{-- resources/views/admin/dasboard/partials/kapasitas-pendaftaran.blade.php --}}

<div class="panel">
    <div class="panel-header">
        <div class="panel-title">Kapasitas Pendaftaran</div>
        <span style="font-size:13px;font-weight:700;color:var(--color-primary);">
            {{ $persentaseKuota }}% Kuota
        </span>
    </div>

    <div class="kuota-bar-track">
        <div class="kuota-bar-fill" style="width: {{ $persentaseKuota }}%;"></div>
    </div>

    <div class="kuota-info">
        <span>Terpenuhi: {{ number_format($totalPendaftar, 0, ',', '.') }}</span>
        <span>Total Kuota: {{ number_format($kuotaTotal, 0, ',', '.') }}</span>
    </div>

    <div class="insight-box">
        💡
        <span>
            Insight: Data pendaftaran sedang meningkat pesat. Pastikan tim verifikasi siap untuk
            menangani lonjakan data minggu depan.
        </span>
    </div>
</div>