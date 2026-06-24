<div class="panel panel-list">
    <div class="panel-header">
        <div>
            <div class="panel-title">Pendaftar Terbaru</div>
            <div class="panel-subtitle">Status pendaftaran masuk terakhir</div>
        </div>
    </div>

    <div class="pendaftar-list">
        @forelse ($pendaftarTerbaru as $p)
            <div class="pendaftar-item"
                 data-id="{{ $p->id }}"
                 data-detail-url="{{ route('admin.pendaftar.detail', $p->id) }}">
                <div class="pendaftar-avatar avatar-{{ $p->badge_color }}">
                    <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                </div>

                <div class="pendaftar-info">
                    <p class="pendaftar-nama">{{ $p->nama }}</p>
                    <p class="pendaftar-meta">{{ $p->profesi ?? '-' }} - ID: {{ $p->id_pendaftar }}</p>
                    <span class="badge badge-{{ $p->badge_color }}">{{ $p->status_label }}</span>
                </div>

                <span class="pendaftar-time">{{ $p->waktu_relatif }}</span>
            </div>
        @empty
            <p class="empty-state">Belum ada pendaftar</p>
        @endforelse
    </div>

    <button type="button" class="panel-link-button" id="openAllPendaftarModal">Lihat Semua Pendaftar</button>
</div>
