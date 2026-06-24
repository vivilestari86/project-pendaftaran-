@extends('admin.layout.app')

@section('title', 'User Management')

@push('styles')
<style>
    .page-header { display: flex; align-items: flex-start; justify-content: space-between; margin-bottom: 24px; }
    .page-title  { font-size: 22px; font-weight: 700; }
    .page-sub    { font-size: 13px; color: var(--text-secondary); margin-top: 3px; }

    .btn-primary {
        display: inline-flex; align-items: center; gap: 7px;
        padding: 9px 16px; background: var(--brand); color: #fff;
        border: none; border-radius: var(--radius-sm);
        font-size: 13.5px; font-weight: 600;
        cursor: pointer; text-decoration: none; white-space: nowrap;
        transition: background .15s;
    }
    .btn-primary:hover { background: var(--brand-dark); }

    /* Stats */
    .stats-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 16px; margin-bottom: 24px; }
    .stat-card {
        background: var(--surface); border: 1px solid var(--border);
        border-radius: var(--radius); padding: 18px 20px;
        display: flex; flex-direction: column; gap: 10px;
        box-shadow: var(--shadow-sm);
    }
    .stat-icon-row { display: flex; align-items: center; justify-content: space-between; }
    .stat-icon {
        width: 40px; height: 40px; border-radius: var(--radius-sm);
        display: flex; align-items: center; justify-content: center;
    }
    .si-blue   { background: #e0e7ff; color: var(--brand); }
    .si-green  { background: var(--green-bg); color: var(--green); }
    .si-amber  { background: var(--amber-bg); color: var(--amber); }
    .si-red    { background: var(--red-bg); color: var(--red); }
    .stat-badge {
        font-size: 11px; font-weight: 600; padding: 2px 8px;
        border-radius: 20px; background: var(--green-bg); color: var(--green);
    }
    .stat-badge.dot::before { content: '●'; margin-right: 4px; font-size: 8px; }
    .stat-label { font-size: 12px; color: var(--text-muted); font-weight: 500; }
    .stat-value { font-size: 28px; font-weight: 700; line-height: 1; }

    /* Table card */
    .table-card {
        background: var(--surface); border: 1px solid var(--border);
        border-radius: var(--radius); box-shadow: var(--shadow-sm); overflow: hidden;
    }
    .table-toolbar {
        padding: 14px 16px; display: flex; align-items: center; gap: 10px;
        border-bottom: 1px solid var(--border);
    }
    .search-wrap { position: relative; flex: 1; max-width: 300px; }
    .search-wrap input {
        width: 100%; padding: 8px 12px 8px 34px;
        border: 1px solid var(--border); border-radius: var(--radius-sm);
        font-size: 13px; background: var(--bg); color: var(--text-primary); outline: none;
    }
    .search-wrap input:focus { border-color: var(--brand); background: #fff; }
    .search-wrap .si {
        position: absolute; left: 10px; top: 50%;
        transform: translateY(-50%); color: var(--text-muted); pointer-events: none;
    }
    .role-select {
        padding: 8px 12px; border: 1px solid var(--border);
        border-radius: var(--radius-sm); font-size: 13px;
        color: var(--text-primary); background: var(--bg); cursor: pointer; outline: none;
    }
    .role-select:focus { border-color: var(--brand); }
    .tbl-count { margin-left: auto; font-size: 12px; color: var(--text-muted); white-space: nowrap; }

    table { width: 100%; border-collapse: collapse; }
    thead tr { border-bottom: 1px solid var(--border); }
    thead th {
        text-align: left; padding: 10px 16px;
        font-size: 11px; font-weight: 600; color: var(--text-muted);
        text-transform: uppercase; letter-spacing: .6px;
    }
    tbody tr { border-bottom: 1px solid var(--border); transition: background .12s; }
    tbody tr:last-child { border-bottom: none; }
    tbody tr:hover { background: var(--bg); }
    tbody td { padding: 14px 16px; font-size: 13.5px; }

    .user-cell { display: flex; align-items: center; gap: 10px; }
    .u-avatar {
        width: 36px; height: 36px; border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
        font-size: 12px; font-weight: 700; color: #fff; flex-shrink: 0;
    }
    .u-name  { font-weight: 600; }
    .u-email { font-size: 12px; color: var(--text-muted); margin-top: 1px; }

    .badge { display: inline-block; padding: 3px 10px; border-radius: 20px; font-size: 12px; font-weight: 600; }
    .badge-admin  { background: #ede9fe; color: #6d28d9; }
    .badge-user   { background: #e0f2fe; color: #0369a1; }
    .badge-editor { background: #e0f2fe; color: #0369a1; }
    .badge-viewer { background: #f1f5f9; color: #475569; }

    .status-badge {
        display: inline-flex; align-items: center; gap: 5px;
        padding: 3px 10px; border-radius: 20px; font-size: 12px; font-weight: 600;
    }
    .status-badge::before { content: '●'; font-size: 8px; }
    .status-active   { background: var(--green-bg); color: #065f46; }
    .status-active::before { color: var(--green); }
    .status-inactive { background: #f1f5f9; color: #64748b; }
    .status-inactive::before { color: #94a3b8; }

    .action-btns { display: flex; gap: 6px; }
    .action-btn {
        width: 30px; height: 30px; border-radius: var(--radius-sm);
        display: flex; align-items: center; justify-content: center;
        background: none; border: 1px solid var(--border);
        color: var(--text-secondary); cursor: pointer; text-decoration: none;
        transition: background .15s, color .15s, border-color .15s;
    }
    .action-btn:hover { background: var(--bg); color: var(--text-primary); }
    .action-btn.danger:hover { background: var(--red-bg); color: var(--red); border-color: #fecaca; }

    /* Pagination */
    .pagi-row {
        padding: 14px 16px; display: flex; align-items: center;
        justify-content: space-between; border-top: 1px solid var(--border); gap: 12px;
    }
    .rows-pp { display: flex; align-items: center; gap: 8px; font-size: 13px; color: var(--text-secondary); }
    .rows-pp select {
        padding: 4px 8px; border: 1px solid var(--border);
        border-radius: var(--radius-sm); font-size: 13px; background: var(--bg); cursor: pointer;
    }
    .pagi-links { display: flex; align-items: center; gap: 4px; font-size: 13px; }
    .pg-info { margin: 0 8px; font-size: 13px; color: var(--text-secondary); }
    .pg-btn {
        width: 28px; height: 28px; border: 1px solid var(--border); border-radius: var(--radius-sm);
        background: none; display: flex; align-items: center; justify-content: center;
        cursor: pointer; color: var(--text-secondary); text-decoration: none; transition: background .12s;
    }
    .pg-btn:hover:not(.off) { background: var(--bg); color: var(--text-primary); }
    .pg-btn.off { opacity: .4; pointer-events: none; }

    .empty-state { text-align: center; padding: 60px 20px; color: var(--text-muted); }
    .empty-state svg { margin: 0 auto 12px; display: block; opacity: .4; }
    .empty-state p { font-size: 15px; font-weight: 500; color: var(--text-secondary); }
    .empty-state small { font-size: 13px; }

    /* Avatar palette */
    .av-0{background:#3b5bdb} .av-1{background:#0e9f6e} .av-2{background:#d97706}
    .av-3{background:#db2777} .av-4{background:#7c3aed} .av-5{background:#0891b2}
    .av-6{background:#059669} .av-7{background:#d946ef}
</style>
@endpush

@section('content')

{{-- Header --}}
<div class="page-header">
    <div>
        <h1 class="page-title">User Management</h1>
        <p class="page-sub">Manage platform access, roles, and security permissions for all workspace members.</p>
    </div>
    <a href="{{ route('admin.manage-users.create') }}" class="btn-primary">
        <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
        Add New User
    </a>
</div>

{{-- Stats --}}
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-icon-row">
            <div class="stat-icon si-blue">
                <svg width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
            </div>
            <span class="stat-badge">+12%</span>
        </div>
        <div class="stat-label">Total Users</div>
        <div class="stat-value">{{ number_format($stats['total']) }}</div>
    </div>

    <div class="stat-card">
        <div class="stat-icon-row">
            <div class="stat-icon si-green">
                <svg width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
            </div>
            <span class="stat-badge dot">Active</span>
        </div>
        <div class="stat-label">Active Now</div>
        <div class="stat-value">{{ number_format($stats['active']) }}</div>
    </div>

    <div class="stat-card">
        <div class="stat-icon-row">
            <div class="stat-icon si-amber">
                <svg width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><circle cx="12" cy="12" r="3"/><path d="M19.07 4.93a10 10 0 0 1 0 14.14M4.93 4.93a10 10 0 0 0 0 14.14"/></svg>
            </div>
        </div>
        <div class="stat-label">Administrators</div>
        <div class="stat-value">{{ number_format($stats['admins']) }}</div>
    </div>

    <div class="stat-card">
        <div class="stat-icon-row">
            <div class="stat-icon si-red">
                <svg width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><circle cx="12" cy="12" r="10"/><line x1="4.93" y1="4.93" x2="19.07" y2="19.07"/></svg>
            </div>
        </div>
        <div class="stat-label">Inactive Accounts</div>
        <div class="stat-value">{{ number_format($stats['inactive']) }}</div>
    </div>
</div>

{{-- Table --}}
<div class="table-card">
    <form method="GET" action="{{ route('admin.manage-users.index') }}" class="table-toolbar">
        <div class="search-wrap">
            <svg class="si" width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/></svg>
            <input type="text" name="search" value="{{ $search }}"
                   placeholder="Filter by name or email..."
                   onchange="this.form.submit()">
        </div>

        <select name="role" class="role-select" onchange="this.form.submit()">
            <option value="">All Roles</option>
            @foreach(['admin' => 'Admin', 'user' => 'User'] as $value => $label)
                <option value="{{ $value }}" {{ $role === $value ? 'selected' : '' }}>{{ $label }}</option>
            @endforeach
        </select>

        <span class="tbl-count">
            @if($users->total())
                Displaying {{ $users->firstItem() }}–{{ $users->lastItem() }} of {{ number_format($users->total()) }}
            @else
                No results
            @endif
        </span>
    </form>

    <table>
        <thead>
            <tr>
                <th>USER</th>
                <th>ROLE</th>
                <th>STATUS</th>
                <th>LAST ACTIVE</th>
                <th>ACTIONS</th>
            </tr>
        </thead>
        <tbody>
            @forelse($users as $user)
            @php $c = 'av-' . ($user->id % 8); @endphp
            <tr>
                <td>
                    <div class="user-cell">
                        <div class="u-avatar {{ $c }}">
                            {{ strtoupper(substr($user->name, 0, 1)) }}{{ strtoupper(substr(strstr($user->name, ' '), 1, 1)) }}
                        </div>
                        <div>
                            <div class="u-name">{{ $user->name }}</div>
                            <div class="u-email">{{ $user->email }}</div>
                        </div>
                    </div>
                </td>
                <td>
                    <span class="badge badge-{{ strtolower($user->role) }}">{{ ucfirst($user->role) }}</span>
                </td>
                <td>
                    <span class="status-badge status-{{ strtolower($user->status) }}">{{ $user->status }}</span>
                </td>
                <td style="color:var(--text-secondary)">
                    {{ $user->last_active_at ? $user->last_active_at->diffForHumans() : 'Never' }}
                </td>
                <td>
                    <div class="action-btns">
                        <a href="{{ route('admin.manage-users.edit', $user) }}" class="action-btn" title="Edit">
                            <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                        </a>
                        @if(! $user->is(auth()->user()))
                            <form method="POST" action="{{ route('admin.manage-users.destroy', $user) }}" style="display:inline"
                                  onsubmit="return confirm('Hapus user {{ addslashes($user->name) }}?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="action-btn danger" title="Hapus">
                                    <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/><path d="M10 11v6M14 11v6"/><path d="M9 6V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2"/></svg>
                                </button>
                            </form>
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
                        <small>Coba ubah filter atau <a href="{{ route('admin.manage-users.create') }}">tambah user baru</a>.</small>
                    </div>
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>

    {{-- Pagination --}}
    <div class="pagi-row">
        <div class="rows-pp">
            <span>Rows per page:</span>
            <form method="GET" action="{{ route('admin.manage-users.index') }}">
                <input type="hidden" name="search" value="{{ $search }}">
                <input type="hidden" name="role" value="{{ $role }}">
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
