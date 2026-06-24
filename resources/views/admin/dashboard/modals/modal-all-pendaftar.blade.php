<div class="modal-overlay" id="modalAllPendaftar">
    <div class="modal-box modal-box-xl">
        <div class="modal-head">
            <h3>Semua Pendaftar Terbaru</h3>
            <button type="button" class="modal-close" aria-label="Tutup modal">✕</button>
        </div>

        <div class="modal-body">
            <div class="modal-list-head">
                <p>Lihat ringkasan data pendaftar yang paling baru masuk ke sistem.</p>
            </div>

            <div class="modal-pendaftar-table">
                <div class="modal-pendaftar-row modal-pendaftar-row-head">
                    <span>Nama</span>
                    <span>Profesi</span>
                    <span>Dokumen</span>
                    <span>Status</span>
                    <span>Tanggal</span>
                </div>

                @forelse ($semuaPendaftar as $pendaftar)
                    <div class="modal-pendaftar-row">
                        <span>
                            <strong>{{ $pendaftar->nama }}</strong>
                            <small>{{ $pendaftar->id_pendaftar }}</small>
                        </span>
                        <span>{{ $pendaftar->profesi ?: '-' }}</span>
                        <span>{{ $pendaftar->wilayah }}</span>
                        <span>
                            <span class="badge badge-{{ $pendaftar->badge_color }}">{{ $pendaftar->status_label }}</span>
                        </span>
                        <span>{{ $pendaftar->tanggal_daftar?->format('d M Y') ?: '-' }}</span>
                    </div>
                @empty
                    <div class="empty-state">Belum ada data pendaftar untuk ditampilkan.</div>
                @endforelse
            </div>
        </div>
    </div>
</div>
