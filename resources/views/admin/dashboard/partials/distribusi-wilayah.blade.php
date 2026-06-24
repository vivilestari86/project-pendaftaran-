{{-- resources/views/admin/dasboard/partials/distribusi-wilayah.blade.php --}}

<div class="panel">
    <div class="panel-header">
        <div>
            <div class="panel-title">Distribusi Wilayah</div>
            <div class="panel-subtitle">Asal daerah pendaftar terbanyak</div>
        </div>
    </div>

    <div class="wilayah-grid">
        @foreach ($distribusiWilayah->take(3) as $w)
            <div class="wilayah-item">
                <svg class="wilayah-ring" viewBox="0 0 36 36">
                    <path d="M18 2.0845
                            a 15.9155 15.9155 0 0 1 0 31.831
                            a 15.9155 15.9155 0 0 1 0 -31.831"
                          fill="none" stroke="#edf0f7" stroke-width="3" />
                    <path d="M18 2.0845
                            a 15.9155 15.9155 0 0 1 0 31.831
                            a 15.9155 15.9155 0 0 1 0 -31.831"
                          fill="none" stroke="#2f63ff" stroke-width="3"
                          stroke-dasharray="{{ $w['persentase'] }}, 100" />
                </svg>
                <div class="wilayah-percent">{{ $w['persentase'] }}%</div>
                <div class="wilayah-name">{{ $w['wilayah'] }}</div>
            </div>
        @endforeach
    </div>
</div>