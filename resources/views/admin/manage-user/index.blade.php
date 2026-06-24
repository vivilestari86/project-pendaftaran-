@extends('admin.layout.app')

@section('title', 'User Management')

@push('styles')
<style>
    .page-header {
        margin-bottom: 24px;
        padding: 22px 24px;
        border-radius: 22px;
        background: linear-gradient(135deg, #0f172a 0%, #1d4ed8 100%);
        color: #fff;
        box-shadow: 0 20px 45px rgba(29, 78, 216, 0.22);
    }
    .page-title  { font-size: 24px; font-weight: 700; }
    .page-sub    { font-size: 13px; color: rgba(255,255,255,.82); margin-top: 5px; max-width: 560px; }

    .table-card {
        background: var(--surface); border: 1px solid var(--border);
        border-radius: 22px; box-shadow: 0 20px 45px rgba(15, 23, 42, 0.07); overflow: hidden;
    }
    .table-toolbar {
        padding: 18px 18px; display: flex; align-items: center; gap: 10px;
        border-bottom: 1px solid var(--border);
        background: linear-gradient(180deg, #ffffff 0%, #f8fbff 100%);
    }
    .search-wrap { position: relative; flex: 1; max-width: 360px; }
    .search-wrap input {
        width: 100%; padding: 8px 12px 8px 34px;
        border: 1px solid var(--border); border-radius: 999px;
        font-size: 13px; background: #fff; color: var(--text-primary); outline: none;
    }
    .search-wrap input:focus { border-color: var(--brand); background: #fff; box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.12); }
    .search-wrap .si {
        position: absolute; left: 10px; top: 50%;
        transform: translateY(-50%); color: var(--text-muted); pointer-events: none;
    }
    .tbl-count { margin-left: auto; font-size: 12px; color: var(--text-muted); white-space: nowrap; }

    table { width: 100%; border-collapse: collapse; }
    thead tr { border-bottom: 1px solid var(--border); background: #f8fbff; }
    thead th {
        text-align: left; padding: 10px 16px;
        font-size: 11px; font-weight: 600; color: var(--text-muted);
        text-transform: uppercase; letter-spacing: .6px;
    }
    tbody tr { border-bottom: 1px solid var(--border); transition: background .12s; }
    tbody tr:last-child { border-bottom: none; }
    tbody tr:hover { background: #f8fbff; }
    tbody td { padding: 16px 16px; font-size: 13.5px; vertical-align: middle; }

    .user-cell { display: flex; align-items: center; gap: 10px; }
    .u-avatar {
        width: 40px; height: 40px; border-radius: 14px;
        display: flex; align-items: center; justify-content: center;
        font-size: 12px; font-weight: 700; color: #fff; flex-shrink: 0;
        box-shadow: inset 0 -10px 18px rgba(255,255,255,.15);
    }
    .u-name  { font-weight: 600; }
    .u-email { font-size: 12px; color: var(--text-muted); margin-top: 1px; }

    .status-badge {
        display: inline-flex; align-items: center; gap: 5px;
        padding: 5px 11px; border-radius: 999px; font-size: 12px; font-weight: 700;
    }
    .status-badge::before { content: '*'; font-size: 8px; }
    .status-active   { background: var(--green-bg); color: #065f46; }
    .status-inactive { background: var(--amber-bg); color: #b45309; }

    .action-btns { display: flex; gap: 6px; }
    .action-btn {
        width: 34px; height: 34px; border-radius: 12px;
        display: flex; align-items: center; justify-content: center;
        background: #fff; border: 1px solid var(--border);
        color: var(--text-secondary); cursor: pointer; text-decoration: none;
        transition: background .15s, color .15s, border-color .15s;
    }
    .action-btn:hover { background: #eff6ff; color: var(--brand); border-color: #bfdbfe; }
    .action-btn.danger:hover { background: var(--red-bg); color: var(--red); border-color: #fecaca; }
    .action-btn:disabled { opacity: .45; cursor: not-allowed; }

    .pagi-row {
        padding: 16px 18px; display: flex; align-items: center;
        justify-content: space-between; border-top: 1px solid var(--border); gap: 12px;
        background: linear-gradient(180deg, #ffffff 0%, #f8fbff 100%);
    }
    .rows-pp { display: flex; align-items: center; gap: 8px; font-size: 13px; color: var(--text-secondary); }
    .rows-pp select {
        padding: 4px 8px; border: 1px solid var(--border);
        border-radius: var(--radius-sm); font-size: 13px; background: var(--bg); cursor: pointer;
    }
    .pagi-links { display: flex; align-items: center; gap: 4px; font-size: 13px; }
    .pg-info { margin: 0 8px; font-size: 13px; color: var(--text-secondary); }
    .pg-btn {
        width: 32px; height: 32px; border: 1px solid var(--border); border-radius: 12px;
        background: #fff; display: flex; align-items: center; justify-content: center;
        cursor: pointer; color: var(--text-secondary); text-decoration: none; transition: background .12s;
    }
    .pg-btn:hover:not(.off) { background: #eff6ff; color: var(--brand); }
    .pg-btn.off { opacity: .4; pointer-events: none; }

    .empty-state { text-align: center; padding: 60px 20px; color: var(--text-muted); }
    .empty-state svg { margin: 0 auto 12px; display: block; opacity: .4; }
    .empty-state p { font-size: 15px; font-weight: 500; color: var(--text-secondary); }
    .empty-state small { font-size: 13px; }

    .av-0{background:#3b5bdb} .av-1{background:#0e9f6e} .av-2{background:#d97706}
    .av-3{background:#db2777} .av-4{background:#7c3aed} .av-5{background:#0891b2}
    .av-6{background:#059669} .av-7{background:#d946ef}
</style>
@endpush

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">User Management</h1>
        <p class="page-sub">Lihat detail akun pengguna dan ubah password bila diperlukan.</p>
    </div>
</div>

<div class="table-card">
    <form method="GET" action="{{ route('admin.manage-users.index') }}" class="table-toolbar">
        <div class="search-wrap">
            <svg class="si" width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/></svg>
            <input type="text" name="search" value="{{ $search }}" placeholder="Filter by name or email..." onchange="this.form.submit()">
        </div>

        <span class="tbl-count">
            @if($users->total())
                Displaying {{ $users->firstItem() }}-{{ $users->lastItem() }} of {{ number_format($users->total()) }}
            @else
                No results
            @endif
        </span>
    </form>

    <table>
        <thead>
            <tr>
                <th>User</th>
                <th>Status</th>
                <th>Phone</th>
                <th>Last Active</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($users as $user)
            @php
                $c = 'av-' . ($user->id % 8);
                $initials = collect(explode(' ', trim($user->name)))->filter()->take(2)->map(fn ($part) => strtoupper(substr($part, 0, 1)))->implode('');
                $isCurrentUser = auth()->id() === $user->id;
                $isProtectedAdmin = $user->isAdmin() && $stats['admins'] <= 1;
                $canDelete = ! $isCurrentUser && ! $isProtectedAdmin;
                $deleteTitle = $canDelete
                    ? 'Hapus'
                    : ($isCurrentUser ? 'Akun yang sedang dipakai tidak bisa dihapus' : 'Admin terakhir tidak bisa dihapus');
            @endphp
            <tr>
                <td>
                    <div class="user-cell">
                        <div class="u-avatar {{ $c }}">{{ $initials ?: 'U' }}</div>
                        <div>
                            <div class="u-name">{{ $user->name }}</div>
                            <div class="u-email">{{ $user->email }}</div>
                        </div>
                    </div>
                </td>
                <td>
                    <span class="status-badge status-{{ strtolower($user->status) }}">
                        {{ $user->status === 'Active' ? 'Lengkap' : 'Belum Lengkap' }}
                    </span>
                </td>
                <td style="color:var(--text-secondary)">{{ $user->phone_number ?: 'Tidak tersedia' }}</td>
                <td style="color:var(--text-secondary)">{{ $user->last_active_at ? $user->last_active_at->diffForHumans() : 'Never' }}</td>
                <td>
                    <div class="action-btns">
                        <a href="{{ route('admin.manage-users.edit', $user) }}" class="action-btn" title="Detail dan ubah password">
                            <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                        </a>
                        @if ($canDelete)
                            <form method="POST" action="{{ route('admin.manage-users.destroy', $user) }}" style="display:inline" onsubmit="return confirm('Hapus user {{ addslashes($user->name) }}?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="action-btn danger" title="{{ $deleteTitle }}">
                                    <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/><path d="M10 11v6M14 11v6"/><path d="M9 6V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2"/></svg>
                                </button>
                            </form>
                        @else
                            <button type="button" class="action-btn danger" title="{{ $deleteTitle }}" disabled>
                                <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/><path d="M10 11v6M14 11v6"/><path d="M9 6V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2"/></svg>
                            </button>
                        @endif
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="5">
                    <div class="empty-state">
                        <svg width="40" height="40" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/></svg>
                        <p>Tidak ada user ditemukan</p>
                        <small>Coba gunakan kata kunci pencarian yang berbeda.</small>
                    </div>
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div class="pagi-row">
        <div class="rows-pp">
            <span>Rows per page:</span>
            <form method="GET" action="{{ route('admin.manage-users.index') }}">
                <input type="hidden" name="search" value="{{ $search }}">
                <select name="per_page" onchange="this.form.submit()">
                    @foreach([10,25,50,100] as $n)
                        <option value="{{ $n }}" {{ $perPage == $n ? 'selected' : '' }}>{{ $n }}</option>
                    @endforeach
                </select>
            </form>
        </div>
        <div class="pagi-links">
            <a href="{{ $users->url(1) }}" class="pg-btn {{ $users->onFirstPage() ? 'off' : '' }}">
                <svg width="12" height="12" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><polyline points="11 17 6 12 11 7"/><polyline points="18 17 13 12 18 7"/></svg>
            </a>
            <a href="{{ $users->previousPageUrl() ?? '#' }}" class="pg-btn {{ $users->onFirstPage() ? 'off' : '' }}">
                <svg width="12" height="12" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><polyline points="15 18 9 12 15 6"/></svg>
            </a>
            <span class="pg-info">Page {{ $users->currentPage() }} of {{ $users->lastPage() }}</span>
            <a href="{{ $users->nextPageUrl() ?? '#' }}" class="pg-btn {{ !$users->hasMorePages() ? 'off' : '' }}">
                <svg width="12" height="12" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"/></svg>
            </a>
            <a href="{{ $users->url($users->lastPage()) }}" class="pg-btn {{ !$users->hasMorePages() ? 'off' : '' }}">
                <svg width="12" height="12" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><polyline points="13 17 18 12 13 7"/><polyline points="6 17 11 12 6 7"/></svg>
            </a>
        </div>
    </div>
</div>
@endsection
