@extends('admin.layout.app')

@section('title', 'Detail User')

@push('styles')
<style>
    .profile-page {
        max-width: 1180px;
        margin: 0 auto;
    }

    .breadcrumb {
        display: flex;
        align-items: center;
        gap: 8px;
        margin-bottom: 8px;
        color: var(--text-muted);
        font-size: 12px;
        font-weight: 600;
    }

    .breadcrumb a {
        color: inherit;
        text-decoration: none;
    }

    .breadcrumb a:hover {
        color: var(--brand-dark);
    }

    .page-heading {
        margin-bottom: 28px;
    }

    .page-heading h1 {
        font-size: 26px;
        line-height: 1.2;
        color: var(--text-primary);
    }

    .editor-layout {
        display: grid;
        grid-template-columns: 320px minmax(0, 1fr);
        gap: 24px;
        align-items: start;
    }

    .side-stack,
    .main-stack {
        display: flex;
        flex-direction: column;
        gap: 18px;
    }

    .profile-card,
    .files-card,
    .form-card,
    .notice-card {
        background: #ffffff;
        border: 1px solid #e4eaf1;
        border-radius: 10px;
        box-shadow: 0 18px 45px rgba(15, 23, 42, 0.06);
    }

    .profile-card {
        padding: 26px 24px;
        text-align: center;
    }

    .avatar-wrap {
        position: relative;
        width: 124px;
        height: 124px;
        margin: 0 auto 16px;
    }

    .avatar-large {
        width: 124px;
        height: 124px;
        border-radius: 999px;
        background: linear-gradient(135deg, #0f766e 0%, #2563eb 100%);
        color: #ffffff;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 34px;
        font-weight: 800;
        box-shadow: 0 20px 35px rgba(37, 99, 235, 0.18);
        overflow: hidden;
    }

    .avatar-large img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .camera-dot {
        position: absolute;
        right: 8px;
        bottom: 8px;
        width: 34px;
        height: 34px;
        border-radius: 999px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #ffffff;
        background: #2563eb;
        border: 4px solid #ffffff;
    }

    .profile-name {
        font-size: 18px;
        font-weight: 800;
        margin-bottom: 3px;
    }

    .profile-email {
        color: var(--text-muted);
        font-size: 13px;
        margin-bottom: 24px;
        word-break: break-word;
    }

    .profile-meta {
        border-top: 1px solid #e4eaf1;
        padding-top: 18px;
        display: flex;
        flex-direction: column;
        gap: 14px;
        text-align: left;
    }

    .meta-row {
        display: flex;
        align-items: center;
        gap: 10px;
        color: var(--text-secondary);
        font-size: 13px;
    }

    .meta-row svg {
        color: var(--text-muted);
        flex-shrink: 0;
    }

    .status-pill {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 4px 10px;
        border-radius: 999px;
        font-size: 12px;
        font-weight: 800;
    }

    .status-active {
        color: #047857;
        background: #dcfce7;
    }

    .status-inactive {
        color: #b45309;
        background: #fff7e8;
    }

    .verification-pill {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 4px 10px;
        border-radius: 999px;
        font-size: 12px;
        font-weight: 800;
    }

    .verification-pill.is-verified {
        color: #047857;
        background: #dcfce7;
    }

    .verification-pill.is-waiting {
        color: #1d4ed8;
        background: #eff6ff;
    }

    .verification-pill.is-blocked {
        color: #64748b;
        background: #f1f5f9;
    }

    .files-card {
        padding: 20px;
    }

    .files-head {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 14px;
    }

    .files-title {
        font-size: 12px;
        font-weight: 800;
        color: #334155;
        text-transform: uppercase;
        letter-spacing: .5px;
    }

    .files-action {
        color: #2563eb;
        font-size: 12px;
        font-weight: 700;
        text-decoration: none;
    }

    .file-list {
        display: flex;
        flex-direction: column;
        gap: 10px;
        max-height: 520px;
        overflow-y: auto;
        padding-right: 6px;
    }

    .file-list::-webkit-scrollbar {
        width: 8px;
    }

    .file-list::-webkit-scrollbar-track {
        background: #f1f5f9;
        border-radius: 999px;
    }

    .file-list::-webkit-scrollbar-thumb {
        background: #cbd5e1;
        border-radius: 999px;
    }

    .file-list::-webkit-scrollbar-thumb:hover {
        background: #94a3b8;
    }

    .verify-form {
        margin-top: 12px;
    }

    .file-category {
        border: 1px solid #e4eaf1;
        border-radius: 10px;
        padding: 12px;
        background: #ffffff;
    }

    .file-category-head {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 10px;
        margin-bottom: 10px;
    }

    .file-category-title {
        font-size: 12px;
        font-weight: 800;
        color: var(--text-primary);
    }

    .file-category-state {
        border-radius: 999px;
        padding: 3px 8px;
        font-size: 10px;
        font-weight: 800;
        white-space: nowrap;
    }

    .file-category-state.is-uploaded {
        background: #dcfce7;
        color: #047857;
    }

    .file-category-state.is-empty {
        background: #f1f5f9;
        color: #64748b;
    }

    .file-item {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 12px;
        border: 1px solid #edf2f7;
        border-radius: 8px;
        background: #f8fafc;
    }

    .file-category .file-item + .file-item {
        margin-top: 8px;
    }

    .file-icon {
        width: 34px;
        height: 34px;
        border-radius: 8px;
        background: #e0ecff;
        color: #2563eb;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    .file-name {
        font-size: 12px;
        font-weight: 800;
        color: var(--text-primary);
    }

    .file-meta {
        font-size: 11px;
        color: var(--text-muted);
        margin-top: 2px;
    }

    .file-view {
        margin-left: auto;
        color: var(--text-muted);
        display: inline-flex;
        align-items: center;
        justify-content: center;
        text-decoration: none;
        transition: color .15s;
    }

    .file-view:hover {
        color: #2563eb;
    }

    .file-empty {
        padding: 14px;
        border: 1px dashed #cbd5e1;
        border-radius: 8px;
        color: var(--text-muted);
        background: #f8fafc;
        font-size: 12px;
        line-height: 1.5;
    }

    .verify-button {
        border: 1px dashed #cbd5e1;
        border-radius: 8px;
        padding: 13px;
        display: flex;
        width: 100%;
        justify-content: center;
        align-items: center;
        gap: 8px;
        color: var(--text-secondary);
        font-size: 12px;
        font-weight: 700;
        background: #ffffff;
        cursor: pointer;
        transition: border-color .15s, color .15s, background .15s;
    }

    .verify-button:hover {
        border-color: #22c55e;
        color: #047857;
        background: #f0fdf4;
    }

    .verify-button.is-complete {
        border-style: solid;
        border-color: #bbf7d0;
        color: #047857;
        background: #f0fdf4;
        cursor: default;
    }

    .form-card {
        overflow: hidden;
    }

    .form-banner {
        background: #0f172a;
        color: #dbeafe;
        padding: 18px 22px;
        display: flex;
        align-items: center;
        gap: 10px;
        font-size: 13px;
        font-weight: 700;
    }

    .form-body {
        padding: 28px 30px;
    }

    .form-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 18px 22px;
    }

    .form-group {
        display: flex;
        flex-direction: column;
        gap: 8px;
    }

    .form-group.span-2 {
        grid-column: span 2;
    }

    .form-label {
        font-size: 12px;
        color: #475569;
        font-weight: 800;
    }

    .input-shell {
        position: relative;
    }

    .input-shell svg {
        position: absolute;
        left: 12px;
        top: 50%;
        transform: translateY(-50%);
        color: #94a3b8;
        pointer-events: none;
    }

    .form-input {
        width: 100%;
        min-height: 42px;
        padding: 10px 12px 10px 38px;
        border: 1px solid #d9e2ec;
        border-radius: 7px;
        background: #ffffff;
        color: var(--text-primary);
        font-size: 13px;
        outline: none;
    }

    .form-input[readonly] {
        background: #f8fafc;
        color: #475569;
    }

    .form-input:focus {
        border-color: #2563eb;
        box-shadow: 0 0 0 4px rgba(37, 99, 235, 0.12);
    }

    .form-input.is-error {
        border-color: var(--red);
    }

    .form-divider {
        margin: 24px 0;
        border: 0;
        border-top: 1px solid #e4eaf1;
    }

    .password-title {
        font-size: 15px;
        font-weight: 800;
        margin-bottom: 6px;
    }

    .password-copy {
        color: var(--text-muted);
        font-size: 13px;
        margin-bottom: 18px;
    }

    .err {
        color: var(--red);
        font-size: 12px;
        font-weight: 600;
    }

    .form-footer {
        margin-top: 24px;
        padding-top: 22px;
        border-top: 1px solid #e4eaf1;
        display: flex;
        justify-content: flex-end;
        align-items: center;
        gap: 12px;
    }

    .btn-cancel,
    .btn-submit {
        min-height: 42px;
        padding: 10px 18px;
        border-radius: 7px;
        font-size: 13px;
        font-weight: 800;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
    }

    .btn-cancel {
        color: var(--text-secondary);
        background: #ffffff;
        border: 1px solid transparent;
    }

    .btn-submit {
        color: #ffffff;
        background: #2563eb;
        border: 1px solid #2563eb;
        box-shadow: 0 12px 24px rgba(37, 99, 235, 0.22);
        cursor: pointer;
    }

    .notice-card {
        padding: 22px;
        display: flex;
        gap: 14px;
        align-items: flex-start;
    }

    .notice-icon {
        width: 36px;
        height: 36px;
        border-radius: 999px;
        background: #fee2e2;
        color: #dc2626;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    .notice-card h3 {
        font-size: 14px;
        margin-bottom: 5px;
    }

    .notice-card p {
        color: var(--text-muted);
        font-size: 12.5px;
        line-height: 1.55;
    }

    @media (max-width: 980px) {
        .editor-layout {
            grid-template-columns: 1fr;
        }

        .form-grid {
            grid-template-columns: 1fr;
        }

        .form-group.span-2 {
            grid-column: span 1;
        }
    }
</style>
@endpush

@section('content')
@php
    $initials = collect(explode(' ', trim($user->name)))->filter()->take(2)->map(fn ($part) => strtoupper(substr($part, 0, 1)))->implode('');
    $roleLabel = $user->isAdmin() ? 'Administrator' : 'Registrant';
    $statusClass = $isDocumentComplete ? 'status-active' : 'status-inactive';
    $statusLabel = $isDocumentComplete ? 'Lengkap' : 'Belum Lengkap';
    $verificationLabel = $isDocumentVerified ? 'Terverifikasi' : ($isDocumentComplete ? 'Menunggu Verifikasi' : 'Belum Bisa Diverifikasi');
    $verificationClass = $isDocumentVerified ? 'is-verified' : ($isDocumentComplete ? 'is-waiting' : 'is-blocked');
@endphp

<div class="profile-page">
    <div class="breadcrumb">
        <a href="{{ route('admin.manage-users.index') }}">Users</a>
        <span>&gt;</span>
        <span>Edit Detail</span>
    </div>

    <div class="page-heading">
        <h1>Edit Data User</h1>
    </div>

    <div class="editor-layout">
        <aside class="side-stack">
            <section class="profile-card">
                <div class="avatar-wrap">
                    <div class="avatar-large">
                        @if($user->profile_photo_url)
                            <img src="{{ $user->profile_photo_url }}" alt="Foto {{ $user->name }}">
                        @else
                            {{ $initials ?: 'U' }}
                        @endif
                    </div>
                    <div class="camera-dot" aria-hidden="true">
                        <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z"/><circle cx="12" cy="13" r="4"/></svg>
                    </div>
                </div>

                <div class="profile-name">{{ $user->name }}</div>
                <div class="profile-email">{{ $user->email }}</div>

                <div class="profile-meta">
                    <div class="meta-row">
                        <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                        <span>Role: {{ $roleLabel }}</span>
                    </div>
                    <div class="meta-row">
                        <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M9 12l2 2 4-4"/><circle cx="12" cy="12" r="10"/></svg>
                        <span>Status: <span class="status-pill {{ $statusClass }}">{{ $statusLabel }}</span></span>
                    </div>
                    <div class="meta-row">
                        <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><path d="M14 2v6h6"/></svg>
                        <span>Dokumen: {{ $uploadedDocumentCount }}/{{ $requiredDocumentCount }}</span>
                    </div>
                    <div class="meta-row">
                        <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M12 3l8 4v5c0 5-3.4 8.3-8 9-4.6-.7-8-4-8-9V7l8-4z"/><path d="M9 12l2 2 4-4"/></svg>
                        <span>Verifikasi: <span class="verification-pill {{ $verificationClass }}">{{ $verificationLabel }}</span></span>
                    </div>
                </div>
            </section>

            <section class="files-card">
                <div class="files-head">
                    <div class="files-title">Scanned Files</div>
                    <a class="files-action" href="{{ route('admin.manage-users.index') }}">Update All</a>
                </div>

                <div class="file-list">
                    @foreach ($documentCategories as $category)
                        <div class="file-category">
                            <div class="file-category-head">
                                <div class="file-category-title">{{ $category['label'] }}</div>
                                <span class="file-category-state {{ $category['documents']->isNotEmpty() ? 'is-uploaded' : 'is-empty' }}">
                                    {{ $category['documents']->isNotEmpty() ? 'Uploaded' : 'Belum Upload' }}
                                </span>
                            </div>

                            @forelse ($category['documents'] as $document)
                                <div class="file-item">
                                    <div class="file-icon">
                                        <svg width="17" height="17" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><path d="M14 2v6h6"/><path d="M8 13h8M8 17h5"/></svg>
                                    </div>
                                    <div>
                                        <div class="file-name">{{ $document->original_name }}</div>
                                        <div class="file-meta">
                                            {{ number_format(($document->file_size ?: 0) / 1048576, 1) }} MB
                                            - {{ strtoupper(pathinfo($document->original_name, PATHINFO_EXTENSION) ?: 'FILE') }}
                                        </div>
                                    </div>
                                    <a class="file-view" href="{{ route('admin.manage-users.documents.show', [$user, $document]) }}" target="_blank" rel="noopener" title="Lihat file {{ $category['label'] }}">
                                        <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M1 12s4-7 11-7 11 7 11 7-4 7-11 7-11-7-11-7z"/><circle cx="12" cy="12" r="3"/></svg>
                                    </a>
                                </div>
                            @empty
                                <div class="file-empty">
                                    Belum ada file untuk kategori ini.
                                </div>
                            @endforelse
                        </div>
                    @endforeach

                </div>

                <form method="POST" action="{{ route('admin.manage-users.verify', $user) }}" class="verify-form">
                    @csrf
                    <button type="submit" class="verify-button {{ $isDocumentVerified ? 'is-complete' : '' }}" {{ (! $isDocumentComplete || $isDocumentVerified) ? 'disabled' : '' }}>
                        <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M9 12l2 2 4-4"/><circle cx="12" cy="12" r="10"/></svg>
                        {{ $isDocumentVerified ? 'Sudah Terverifikasi' : ($isDocumentComplete ? 'Verifikasi' : 'Dokumen Belum Lengkap') }}
                    </button>
                </form>
            </section>
        </aside>

        <div class="main-stack">
            <section class="form-card">
                <div class="form-banner">
                    <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M12 16v-4M12 8h.01"/></svg>
                    Perbarui kata sandi akun ini tanpa mengubah data personal pengguna.
                </div>

                <form method="POST" action="{{ route('admin.manage-users.update', $user) }}">
                    @csrf
                    @method('PUT')

                    <div class="form-body">
                        <div class="form-grid">
                            <div class="form-group">
                                <label class="form-label" for="name">Full Name</label>
                                <div class="input-shell">
                                    <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                                    <input class="form-input" type="text" id="name" value="{{ $user->name }}" readonly>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="form-label" for="email">Email Address</label>
                                <div class="input-shell">
                                    <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M4 4h16v16H4z"/><path d="m22 6-10 7L2 6"/></svg>
                                    <input class="form-input" type="email" id="email" value="{{ $user->email }}" readonly>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="form-label" for="phone">Phone Number</label>
                                <div class="input-shell">
                                    <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.8 19.8 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6A19.8 19.8 0 0 1 2.12 4.18 2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72c.13.96.35 1.89.66 2.78a2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.3-1.23a2 2 0 0 1 2.11-.45c.89.31 1.82.53 2.78.66A2 2 0 0 1 22 16.92z"/></svg>
                                    <input class="form-input" type="text" id="phone" value="{{ $user->phone_number ?: 'Tidak tersedia' }}" readonly>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="form-label" for="created_at">Registered At</label>
                                <div class="input-shell">
                                    <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><path d="M16 2v4M8 2v4M3 10h18"/></svg>
                                    <input class="form-input" type="text" id="created_at" value="{{ $user->created_at?->format('d/m/Y H:i') ?: '-' }}" readonly>
                                </div>
                            </div>

                            <div class="form-group span-2">
                                <label class="form-label" for="last_active_at">Last Active</label>
                                <div class="input-shell">
                                    <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/></svg>
                                    <input class="form-input" type="text" id="last_active_at" value="{{ $user->last_active_at ? $user->last_active_at->diffForHumans() : 'Belum pernah login' }}" readonly>
                                </div>
                            </div>
                        </div>

                        <hr class="form-divider">

                        <div class="password-title">Ubah Kata Sandi</div>
                        <div class="password-copy">Admin dapat mengganti kata sandi akun ini. Password minimal 8 karakter dan harus dikonfirmasi.</div>

                        <div class="form-grid">
                            <div class="form-group">
                                <label class="form-label" for="password">Password Baru</label>
                                <div class="input-shell">
                                    <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                                    <input class="form-input @error('password') is-error @enderror" type="password" id="password" name="password" placeholder="Masukkan password baru" required>
                                </div>
                                @error('password')<span class="err">{{ $message }}</span>@enderror
                            </div>

                            <div class="form-group">
                                <label class="form-label" for="password_confirmation">Konfirmasi Password</label>
                                <div class="input-shell">
                                    <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M9 12l2 2 4-4"/><rect x="3" y="3" width="18" height="18" rx="2"/></svg>
                                    <input class="form-input" type="password" id="password_confirmation" name="password_confirmation" placeholder="Ulangi password baru" required>
                                </div>
                            </div>
                        </div>

                        <div class="form-footer">
                            <a href="{{ route('admin.manage-users.index') }}" class="btn-cancel">Batal</a>
                            <button type="submit" class="btn-submit">
                                <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><path d="M17 21v-8H7v8M7 3v5h8"/></svg>
                                Simpan Password
                            </button>
                        </div>
                    </div>
                </form>
            </section>

            <section class="notice-card">
                <div class="notice-icon">
                    <svg width="17" height="17" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><rect x="5" y="11" width="14" height="10" rx="2"/><path d="M8 11V7a4 4 0 0 1 8 0v4"/></svg>
                </div>
                <div>
                    <h3>Privacy & Security Notice</h3>
                    <p>Perubahan password langsung mengganti akses login pengguna. Pastikan permintaan reset sudah diverifikasi sebelum menyimpan perubahan.</p>
                </div>
            </section>
        </div>
    </div>
</div>
@endsection
