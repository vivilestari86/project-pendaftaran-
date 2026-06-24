@extends('admin.layout.app')

@section('title', 'Settings')

@push('styles')
<style>
    .settings-shell { max-width: 1120px; margin: 0 auto; }
    .settings-hero {
        padding: 26px 28px; border-radius: 24px; margin-bottom: 24px;
        background: linear-gradient(135deg, #ecfeff 0%, #dffaf5 45%, #eef4ff 100%);
        border: 1px solid #d8e7ea; box-shadow: 0 22px 44px rgba(15, 23, 42, .08);
    }
    .settings-hero h1 { font-size: 28px; margin-bottom: 6px; color: #0f172a; }
    .settings-hero p { color: #475569; max-width: 720px; }
    .settings-grid {
        display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 20px;
    }
    .settings-card {
        background: #fff; border: 1px solid #d8e7ea; border-radius: 22px;
        box-shadow: 0 18px 42px rgba(15, 23, 42, .07); overflow: hidden;
    }
    .settings-card-head {
        padding: 20px 22px; border-bottom: 1px solid #d8e7ea;
        background: linear-gradient(180deg, #ffffff 0%, #f7fffd 100%);
    }
    .settings-card-head h2 { font-size: 17px; color: #0f172a; }
    .settings-card-head p { font-size: 13px; color: #64748b; margin-top: 4px; }
    .settings-card-body { padding: 22px; display: flex; flex-direction: column; gap: 16px; }
    .setting-item {
        display: flex; align-items: flex-start; justify-content: space-between; gap: 14px;
        padding: 15px 16px; border-radius: 16px; border: 1px solid #d8e7ea;
        background: linear-gradient(180deg, #fff 0%, #f8fffe 100%);
    }
    .setting-item h3 { font-size: 14px; margin-bottom: 5px; color: #0f172a; }
    .setting-item p { font-size: 12px; color: #64748b; line-height: 1.6; }
    .setting-chip {
        padding: 6px 11px; border-radius: 999px; font-size: 12px; font-weight: 700;
        background: #d7faf3; color: #0b7f74; white-space: nowrap;
    }
    @media (max-width: 900px) {
        .settings-grid { grid-template-columns: 1fr; }
    }
</style>
@endpush

@section('content')
<div class="settings-shell">
    <div class="settings-hero">
        <h1>Settings</h1>
        <p>Halaman ini berisi pengaturan umum panel admin. Untuk saat ini pengaturan masih bersifat informatif agar admin punya pusat kontrol yang rapi dan mudah diperluas.</p>
    </div>

    <div class="settings-grid">
        <section class="settings-card">
            <div class="settings-card-head">
                <h2>Pengaturan Sistem</h2>
                <p>Kontrol utama yang berhubungan dengan alur panel admin.</p>
            </div>
            <div class="settings-card-body">
                <div class="setting-item">
                    <div>
                        <h3>Status Dashboard</h3>
                        <p>Dashboard admin aktif dan siap digunakan untuk memonitor pendaftaran secara real-time.</p>
                    </div>
                    <span class="setting-chip">Aktif</span>
                </div>
                <div class="setting-item">
                    <div>
                        <h3>Session Login</h3>
                        <p>Admin tetap diarahkan ke halaman login ketika sesi berakhir atau logout dilakukan.</p>
                    </div>
                    <span class="setting-chip">Terkelola</span>
                </div>
            </div>
        </section>

        <section class="settings-card">
            <div class="settings-card-head">
                <h2>Pengaturan Akun</h2>
                <p>Ringkasan preferensi yang berkaitan dengan akun admin saat ini.</p>
            </div>
            <div class="settings-card-body">
                <div class="setting-item">
                    <div>
                        <h3>Admin Login</h3>
                        <p>Akun aktif saat ini: <strong>{{ $user->email }}</strong></p>
                    </div>
                    <span class="setting-chip">{{ strtoupper($user->role ?? 'admin') }}</span>
                </div>
                <div class="setting-item">
                    <div>
                        <h3>Status Akun</h3>
                        <p>Status akun digunakan untuk menentukan akses masuk ke panel admin.</p>
                    </div>
                    <span class="setting-chip">{{ $user->status ?? 'Active' }}</span>
                </div>
            </div>
        </section>
    </div>
</div>
@endsection
