<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AdminPortal – @yield('title', 'Dashboard')</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --sidebar-w: 200px;
            --brand: #3b5bdb;
            --brand-light: #eef2ff;
            --brand-dark: #2f4ac2;
            --text-primary: #1a1d23;
            --text-secondary: #5c6370;
            --text-muted: #9099a8;
            --bg: #f5f6fa;
            --surface: #ffffff;
            --border: #e5e7ef;
            --green: #12b76a;
            --green-bg: #ecfdf5;
            --red: #f04438;
            --red-bg: #fef3f2;
            --amber: #f79009;
            --amber-bg: #fffaeb;
            --shadow-sm: 0 1px 3px rgba(0,0,0,.08);
            --radius: 10px;
            --radius-sm: 6px;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: var(--bg);
            color: var(--text-primary);
            display: flex;
            min-height: 100vh;
            font-size: 14px;
        }

        /* Sidebar */
        .sidebar {
            width: var(--sidebar-w);
            background: var(--surface);
            border-right: 1px solid var(--border);
            display: flex;
            flex-direction: column;
            position: fixed;
            top: 0; left: 0; bottom: 0;
            z-index: 100;
        }
        .sidebar-brand { padding: 18px 16px 14px; border-bottom: 1px solid var(--border); }
        .sidebar-brand .brand-name { font-size: 15px; font-weight: 700; }
        .sidebar-brand .brand-sub  { font-size: 11px; color: var(--text-muted); margin-top: 1px; }
        .sidebar-nav { flex: 1; padding: 12px 10px; display: flex; flex-direction: column; gap: 2px; }

        .nav-item {
            display: flex; align-items: center; gap: 10px;
            padding: 9px 12px;
            border-radius: var(--radius-sm);
            color: var(--text-secondary);
            text-decoration: none;
            font-size: 13.5px; font-weight: 500;
            transition: background .15s, color .15s;
        }
        .nav-item:hover { background: var(--bg); color: var(--text-primary); }
        .nav-item.active { background: var(--brand-light); color: var(--brand); }
        .nav-item svg { width: 16px; height: 16px; flex-shrink: 0; }

        .sidebar-bottom { padding: 12px 10px; border-top: 1px solid var(--border); display: flex; flex-direction: column; gap: 2px; }

        .btn-new-report {
            display: flex; align-items: center; justify-content: center; gap: 8px;
            padding: 10px;
            background: var(--text-primary);
            color: #fff; border: none; border-radius: var(--radius-sm);
            font-size: 13px; font-weight: 600;
            cursor: pointer; text-decoration: none; margin-bottom: 8px;
            transition: opacity .15s;
        }
        .btn-new-report:hover { opacity: .85; }

        /* Main */
        .main { margin-left: var(--sidebar-w); flex: 1; display: flex; flex-direction: column; min-height: 100vh; }

        /* Topbar */
        .topbar {
            height: 56px; background: var(--surface); border-bottom: 1px solid var(--border);
            display: flex; align-items: center; padding: 0 24px; gap: 12px;
            position: sticky; top: 0; z-index: 50;
        }
        .topbar-search { flex: 1; max-width: 360px; position: relative; }
        .topbar-search input {
            width: 100%; padding: 8px 12px 8px 36px;
            border: 1px solid var(--border); border-radius: var(--radius-sm);
            font-size: 13px; background: var(--bg); color: var(--text-primary); outline: none;
        }
        .topbar-search input:focus { border-color: var(--brand); }
        .topbar-search .si {
            position: absolute; left: 10px; top: 50%;
            transform: translateY(-50%); color: var(--text-muted); pointer-events: none;
        }
        .topbar-actions { margin-left: auto; display: flex; align-items: center; gap: 12px; }
        .icon-btn {
            width: 34px; height: 34px; display: flex; align-items: center; justify-content: center;
            border-radius: var(--radius-sm); background: none; border: 1px solid var(--border);
            cursor: pointer; color: var(--text-secondary); transition: background .15s;
        }
        .icon-btn:hover { background: var(--bg); }
        .user-chip {
            display: flex; align-items: center; gap: 8px;
            padding: 4px 10px 4px 4px;
            border: 1px solid var(--border); border-radius: 20px; cursor: pointer;
        }
        .avatar-sm {
            width: 28px; height: 28px; border-radius: 50%;
            background: var(--brand); color: #fff;
            display: flex; align-items: center; justify-content: center;
            font-size: 11px; font-weight: 700;
        }
        .user-chip-name { font-size: 13px; font-weight: 600; }
        .user-chip-role { font-size: 10px; color: var(--text-muted); text-transform: uppercase; letter-spacing: .5px; }

        /* Content */
        .content { padding: 28px 28px 40px; flex: 1; }

        /* Alert */
        .alert {
            padding: 12px 16px; border-radius: var(--radius-sm);
            margin-bottom: 20px; font-size: 13.5px;
            display: flex; align-items: center; gap: 8px;
        }
        .alert-success { background: var(--green-bg); color: #065f46; border: 1px solid #a7f3d0; }
        .alert-error   { background: var(--red-bg);   color: #991b1b; border: 1px solid #fecaca; }
    </style>
    @stack('styles')
</head>
<body>

<aside class="sidebar">
    <div class="sidebar-brand">
        <div class="brand-name">AdminPortal</div>
        <div class="brand-sub">Enterprise Console</div>
    </div>
    <nav class="sidebar-nav">
        <a href="{{ route('admin.dashboard') }}" class="nav-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/></svg>
            Dashboard
        </a>
        <a href="{{ route('admin.manage-users.index') }}" class="nav-item {{ request()->routeIs('admin.manage-users.*') ? 'active' : '' }}">
            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
            Users
        </a>
        <a href="#" class="nav-item">
            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
            Reports
        </a>
        <a href="#" class="nav-item">
            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><circle cx="12" cy="12" r="3"/><path d="M19.07 4.93a10 10 0 0 1 0 14.14M4.93 4.93a10 10 0 0 0 0 14.14"/></svg>
            Settings
        </a>
    </nav>
    <div class="sidebar-bottom">
        <a href="#" class="btn-new-report">
            <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="16"/><line x1="8" y1="12" x2="16" y2="12"/></svg>
            New Report
        </a>
        <a href="#" class="nav-item">
            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><circle cx="12" cy="12" r="10"/><path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
            Help
        </a>
        <a href="{{ route('logout') }}" class="nav-item"
           onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
            Logout
        </a>
        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display:none">@csrf</form>
    </div>
</aside>

<div class="main">
    <header class="topbar">
        <div class="topbar-search">
            <svg class="si" width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/></svg>
            <input type="text" placeholder="Search resources, users, or audits...">
        </div>
        <div class="topbar-actions">
            <button class="icon-btn">
                <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 0 1-3.46 0"/></svg>
            </button>
            <button class="icon-btn">
                <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><circle cx="12" cy="12" r="10"/><path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
            </button>
            <button class="icon-btn">
                <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><circle cx="5" cy="12" r="1"/><circle cx="12" cy="12" r="1"/><circle cx="19" cy="12" r="1"/></svg>
            </button>
            @auth
            <div class="user-chip">
                <div class="avatar-sm">{{ strtoupper(substr(auth()->user()->name, 0, 2)) }}</div>
                <div>
                    <div class="user-chip-name">{{ auth()->user()->name }}</div>
                    <div class="user-chip-role">{{ auth()->user()->role ?? 'Admin' }}</div>
                </div>
            </div>
            @endauth
        </div>
    </header>

    <main class="content">
        @if(session('success'))
            <div class="alert alert-success">
                <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg>
                {{ session('success') }}
            </div>
        @endif
        @if(session('error'))
            <div class="alert alert-error">
                <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                {{ session('error') }}
            </div>
        @endif

        @yield('content')
    </main>
</div>

@stack('scripts')
</body>
</html>
