@extends('admin.layout.app')

@section('title', 'Detail User')

@push('styles')
<style>
    .back-link {
        display: inline-flex; align-items: center; gap: 6px;
        font-size: 13px; color: var(--text-secondary); text-decoration: none; margin-bottom: 20px;
    }
    .back-link:hover { color: var(--text-primary); }

    .detail-grid {
        display: grid;
        grid-template-columns: 1.25fr .95fr;
        gap: 20px;
    }
    .card {
        background: var(--surface); border: 1px solid var(--border);
        border-radius: 22px; box-shadow: 0 20px 45px rgba(15, 23, 42, 0.08);
    }
    .card-header {
        padding: 22px 24px; border-bottom: 1px solid var(--border);
        display: flex; align-items: center; gap: 14px;
        background: linear-gradient(180deg, #ffffff 0%, #f8fbff 100%);
    }
    .avatar {
        width: 56px; height: 56px; border-radius: 18px;
        background: linear-gradient(135deg, #2563eb 0%, #1e3a8a 100%); color: #fff;
        display: flex; align-items: center; justify-content: center;
        font-size: 18px; font-weight: 700; flex-shrink: 0;
        box-shadow: 0 14px 28px rgba(37, 99, 235, 0.22);
    }
    .card-header h2 { font-size: 18px; font-weight: 700; }
    .card-header p { font-size: 13px; color: var(--text-secondary); margin-top: 3px; }

    .card-body { padding: 24px; }
    .info-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }
    .info-item {
        padding: 14px 16px; border: 1px solid var(--border);
        border-radius: 16px; background: linear-gradient(180deg, #ffffff 0%, #f8fbff 100%);
        box-shadow: inset 0 1px 0 rgba(255,255,255,.7);
    }
    .info-label {
        font-size: 11px; font-weight: 700; color: var(--text-muted);
        text-transform: uppercase; letter-spacing: .6px; margin-bottom: 8px;
    }
    .info-value { font-size: 14px; font-weight: 600; color: var(--text-primary); }
    .info-value.muted { font-weight: 500; color: var(--text-secondary); }

    .status-badge {
        display: inline-flex; align-items: center; gap: 5px;
        padding: 5px 11px; border-radius: 999px; font-size: 12px; font-weight: 700;
    }
    .status-badge::before { content: '*'; font-size: 8px; }
    .status-active { background: var(--green-bg); color: #065f46; }
    .status-inactive { background: #f1f5f9; color: #64748b; }

    .password-form { display: flex; flex-direction: column; gap: 18px; }
    .section-title { font-size: 16px; font-weight: 700; margin-bottom: 4px; }
    .section-sub { font-size: 13px; color: var(--text-secondary); margin-bottom: 6px; }
    .form-group { display: flex; flex-direction: column; gap: 6px; }
    .form-label { font-size: 13px; font-weight: 600; }
    .form-input {
        padding: 10px 12px; border: 1px solid var(--border);
        border-radius: 14px; font-size: 13.5px;
        color: var(--text-primary); background: #fff; outline: none;
    }
    .form-input:focus { border-color: var(--brand); box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.12); }
    .form-input.is-error { border-color: var(--red); }
    .err { font-size: 12px; color: var(--red); }
    .hint { font-size: 12px; color: var(--text-muted); }

    .form-footer {
        padding-top: 16px; display: flex; justify-content: flex-end; gap: 10px;
    }
    .btn-cancel, .btn-submit {
        padding: 10px 16px; border-radius: 14px;
        font-size: 13.5px; font-weight: 600; text-decoration: none;
    }
    .btn-cancel {
        background: #fff; border: 1px solid var(--border); color: var(--text-secondary);
    }
    .btn-submit {
        background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%); color: #fff; border: none; cursor: pointer;
        box-shadow: 0 14px 28px rgba(37, 99, 235, 0.22);
    }

    @media (max-width: 960px) {
        .detail-grid { grid-template-columns: 1fr; }
        .info-grid { grid-template-columns: 1fr; }
    }
</style>
@endpush

@section('content')
@php
    $initials = collect(explode(' ', trim($user->name)))->filter()->take(2)->map(fn ($part) => strtoupper(substr($part, 0, 1)))->implode('');
@endphp

<a href="{{ route('admin.manage-users.index') }}" class="back-link">
    <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><polyline points="15 18 9 12 15 6"/></svg>
    Kembali ke User Management
</a>

<div class="detail-grid">
    <div class="card">
        <div class="card-header">
            <div class="avatar">{{ $initials ?: 'U' }}</div>
            <div>
                <h2>Detail User</h2>
                <p>Informasi akun ini hanya untuk dilihat, bukan untuk mengubah role atau data pokok.</p>
            </div>
        </div>
        <div class="card-body">
            <div class="info-grid">
                <div class="info-item">
                    <div class="info-label">Nama</div>
                    <div class="info-value">{{ $user->name }}</div>
                </div>
                <div class="info-item">
                    <div class="info-label">Email</div>
                    <div class="info-value">{{ $user->email }}</div>
                </div>
                <div class="info-item">
                    <div class="info-label">Nomor Telepon</div>
                    <div class="info-value {{ $user->phone_number ? '' : 'muted' }}">{{ $user->phone_number ?: 'Tidak tersedia' }}</div>
                </div>
                <div class="info-item">
                    <div class="info-label">Status</div>
                    <div class="info-value">
                        <span class="status-badge status-{{ strtolower($user->status) }}">{{ $user->status }}</span>
                    </div>
                </div>
                <div class="info-item">
                    <div class="info-label">Terakhir Aktif</div>
                    <div class="info-value muted">{{ $user->last_active_at ? $user->last_active_at->diffForHumans() : 'Belum pernah login' }}</div>
                </div>
                <div class="info-item">
                    <div class="info-label">Tanggal Dibuat</div>
                    <div class="info-value muted">{{ $user->created_at?->format('d M Y H:i') ?: '-' }}</div>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <div>
                <h2>{{ $user->isAdmin() ? 'Ubah Password Admin' : 'Reset Password Pengguna' }}</h2>
                <p>
                    {{ $user->isAdmin()
                        ? 'Admin dapat memperbarui password akun admin dari halaman detail ini.'
                        : 'Jika pengguna lupa password, admin dapat mereset password akun ini dari halaman detail.' }}
                </p>
            </div>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('admin.manage-users.update', $user) }}" class="password-form">
                @csrf
                @method('PUT')

                <div>
                    <div class="section-title">{{ $user->isAdmin() ? 'Password Baru Admin' : 'Password Baru Pengguna' }}</div>
                    <div class="section-sub">
                        {{ $user->isAdmin()
                            ? 'Masukkan password baru minimal 8 karakter untuk akun admin ini.'
                            : 'Masukkan password baru minimal 8 karakter agar pengguna bisa login kembali.' }}
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label" for="password">Password Baru</label>
                    <input class="form-input @error('password') is-error @enderror" type="password" id="password" name="password" placeholder="Masukkan password baru" required>
                    @error('password')<span class="err">{{ $message }}</span>@enderror
                </div>

                <div class="form-group">
                    <label class="form-label" for="password_confirmation">Konfirmasi Password</label>
                    <input class="form-input" type="password" id="password_confirmation" name="password_confirmation" placeholder="Ulangi password baru" required>
                    <span class="hint">Pastikan password konfirmasi sama persis.</span>
                </div>

                <div class="form-footer">
                    <a href="{{ route('admin.manage-users.index') }}" class="btn-cancel">Batal</a>
                    <button type="submit" class="btn-submit">Simpan Password</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
