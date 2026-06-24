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
            --sidebar-w: 232px;
            --brand: #0f9d8f;
            --brand-light: #d7faf3;
            --brand-dark: #0b7f74;
            --text-primary: #0f172a;
            --text-secondary: #475569;
            --text-muted: #64748b;
            --bg: linear-gradient(180deg, #eef4ff 0%, #f8fbff 52%, #f3f6fb 100%);
            --surface: #ffffff;
            --surface-soft: #f8fbff;
            --border: #d8e7ea;
            --green: #15803d;
            --green-bg: #e8fff1;
            --red: #dc2626;
            --red-bg: #fef2f2;
            --amber: #b45309;
            --amber-bg: #fff7e8;
            --shadow-sm: 0 18px 40px rgba(15, 23, 42, 0.08);
            --radius: 16px;
            --radius-sm: 12px;
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
            background: #ffffff;
            border-right: 1px solid var(--border);
            display: flex;
            flex-direction: column;
            position: fixed;
            top: 0; left: 0; bottom: 0;
            z-index: 100;
            color: #0f172a;
            box-shadow: 8px 0 28px rgba(15, 23, 42, 0.04);
        }
        .sidebar-brand {
            padding: 18px 16px 14px;
            border-bottom: 1px solid var(--border);
            display: flex;
            align-items: center;
            gap: 12px;
        }
        .sidebar-brand .brand-logo {
            width: 42px;
            height: 42px;
            object-fit: contain;
            flex-shrink: 0;
        }
        .sidebar-brand .brand-copy {
            min-width: 0;
        }
        .sidebar-brand .brand-name { font-size: 15px; font-weight: 700; line-height: 1.2; color: #0f172a; }
        .sidebar-brand .brand-sub  { font-size: 11px; color: var(--text-muted); margin-top: 2px; line-height: 1.35; }
        .sidebar-nav { flex: 1; padding: 12px 10px; display: flex; flex-direction: column; gap: 2px; }

        .nav-item {
            display: flex; align-items: center; gap: 10px;
            padding: 11px 12px;
            border-radius: var(--radius-sm);
            color: var(--text-secondary);
            text-decoration: none;
            font-size: 13.5px; font-weight: 500;
            transition: background .15s, color .15s, transform .15s;
        }
        .nav-item:hover {
            background: #f8fafc;
            color: #0f172a;
            transform: translateX(2px);
            box-shadow: 0 10px 20px rgba(15, 23, 42, .06);
        }
        .nav-item.active {
            background: var(--brand-light);
            color: var(--brand-dark);
            box-shadow: inset 0 0 0 1px rgba(15, 157, 143, .16), 0 12px 24px rgba(15, 118, 110, .1);
        }
        .nav-item svg { width: 16px; height: 16px; flex-shrink: 0; }

        .sidebar-bottom { padding: 12px 10px; border-top: 1px solid var(--border); display: flex; flex-direction: column; gap: 2px; }

        /* Main */
        .main { margin-left: var(--sidebar-w); flex: 1; display: flex; flex-direction: column; min-height: 100vh; }

        /* Topbar */
        .topbar {
            height: 64px; background: rgba(255,255,255,.86); border-bottom: 1px solid var(--border);
            display: flex; align-items: center; padding: 0 24px; gap: 12px;
            position: sticky; top: 0; z-index: 50;
            backdrop-filter: blur(14px);
        }
        .topbar-actions { margin-left: auto; display: flex; align-items: center; gap: 12px; }
        .icon-btn {
            width: 34px; height: 34px; display: flex; align-items: center; justify-content: center;
            border-radius: 999px; background: #ffffff; border: 1px solid var(--border);
            cursor: pointer; color: var(--text-secondary); transition: background .15s;
        }
        .icon-btn:hover { background: #eff6ff; color: var(--brand); }

        /* Content */
        .content { padding: 28px 28px 40px; flex: 1; }

        /* Alert */
        .alert {
            padding: 12px 16px; border-radius: var(--radius-sm);
            margin-bottom: 20px; font-size: 13.5px;
            display: flex; align-items: center; gap: 8px;
            box-shadow: 0 12px 24px rgba(15, 23, 42, 0.06);
            transition: opacity .35s ease, transform .35s ease, margin .35s ease;
        }
        .alert-success { background: var(--green-bg); color: #065f46; border: 1px solid #a7f3d0; }
        .alert-error   { background: var(--red-bg);   color: #991b1b; border: 1px solid #fecaca; }
        .alert.is-hiding {
            opacity: 0;
            transform: translateY(-6px);
            margin-bottom: 0;
        }
    </style>
    @stack('styles')
</head>
<body>

<aside class="sidebar">
    <div class="sidebar-brand">
        <img src="{{ asset('images/logo.png') }}" alt="Logo Politeknik Negeri Indramayu" class="brand-logo">
        <div class="brand-copy">
            <div class="brand-name">AdminPortal</div>
            <div class="brand-sub">Enterprise Console</div>
        </div>
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
    </nav>
    <div class="sidebar-bottom">
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
<script>
    document.addEventListener('DOMContentLoaded', () => {
        document.querySelectorAll('.alert').forEach((alertBox) => {
            setTimeout(() => {
                alertBox.classList.add('is-hiding');
                setTimeout(() => alertBox.remove(), 350);
            }, 2600);
        });
    });
</script>
</body>
</html>
