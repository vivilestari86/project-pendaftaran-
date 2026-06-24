{{-- resources/views/admin/dasboard/partials/pendaftar-list.blade.php --}}

<div class="panel">
    <div class="panel-header">
        <div class="panel-title">Pendaftar Terbaru</div>
        <span style="font-size:12px;color:var(--color-text-muted);">🔄 Status pendaftar masuk terkini</span>
    </div>

    <div class="pendaftar-list">
        @forelse ($pendaftarTerbaru as $p)
            <div class="pendaftar-item"
                 data-id="{{ $p->id }}"
                 data-detail-url="{{ route('admin.pendaftar.detail', $p->id) }}"
                 data-edit-url="{{ route('admin.pendaftar.edit-form', $p->id) }}">
                
                <img src="{{ $p->foto_url }}"
                     alt="{{ $p->nama }}"
                     class="pendaftar-avatar">

                <div class="pendaftar-info">
                    <p class="pendaftar-nama">{{ $p->nama }}</p>
                    <p class="pendaftar-meta">
                        {{ $p->profesi ?? '-' }} • {{ $p->id_pendaftar }}
                        <span class="badge badge-{{ $p->badge_color }}">
                            {{ $p->status_label }}
                        </span>
                    </p>
                </div>

                <span class="pendaftar-time">{{ $p->waktu_relatif }}</span>
            </div>
        @empty
            <p style="color:var(--color-text-muted); font-size:13px; text-align:center; padding:20px 0;">
                📭 Belum ada pendaftar
            </p>
        @endforelse
    </div>

    <button type="button" class="btn-outline" style="width:100%; margin-top:14px; color:var(--color-primary);">
        Lihat Semua Pendaftar →
    </button>
</div>