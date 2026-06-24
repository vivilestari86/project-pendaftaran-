@extends('admin.layout.app')

@section('title', 'Profil Admin')

@push('styles')
<style>
    .profile-shell { max-width: 880px; margin: 0 auto; }
    .profile-card {
        background: #fff;
        border: 1px solid #d8e7ea;
        border-radius: 24px;
        overflow: hidden;
        box-shadow: 0 22px 46px rgba(15, 23, 42, 0.08);
    }
    .profile-head {
        padding: 28px 28px 24px;
        display: flex;
        align-items: center;
        gap: 18px;
        background: linear-gradient(135deg, #ecfeff 0%, #dffaf5 45%, #eef4ff 100%);
        border-bottom: 1px solid #d8e7ea;
    }
    .profile-avatar {
        width: 82px;
        height: 82px;
        border-radius: 24px;
        object-fit: cover;
        background: linear-gradient(135deg, #2563eb 0%, #0f9d8f 100%);
        color: #fff;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 28px;
        font-weight: 700;
        box-shadow: 0 16px 32px rgba(37, 99, 235, 0.18);
        overflow: hidden;
    }
    .profile-head h1 {
        font-size: 28px;
        margin-bottom: 6px;
        color: #0f172a;
    }
    .profile-head p {
        color: #475569;
        max-width: 520px;
        line-height: 1.6;
    }
    .profile-body {
        padding: 26px 28px 28px;
    }
    .profile-form {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 22px;
    }
    .form-panel {
        padding: 20px;
        border-radius: 20px;
        border: 1px solid #d8e7ea;
        background: linear-gradient(180deg, #ffffff 0%, #f8fffe 100%);
    }
    .form-panel h2 {
        font-size: 16px;
        margin-bottom: 6px;
        color: #0f172a;
    }
    .form-panel p {
        font-size: 13px;
        color: #64748b;
        line-height: 1.6;
        margin-bottom: 16px;
    }
    .form-group {
        display: flex;
        flex-direction: column;
        gap: 7px;
        margin-bottom: 16px;
    }
    .form-group:last-child {
        margin-bottom: 0;
    }
    .form-label {
        font-size: 13px;
        font-weight: 600;
        color: #0f172a;
    }
    .form-input {
        width: 100%;
        padding: 11px 14px;
        border: 1px solid #d8e7ea;
        border-radius: 14px;
        background: #fff;
        font-size: 13.5px;
        color: #0f172a;
        outline: none;
    }
    .form-input:focus {
        border-color: #0f9d8f;
        box-shadow: 0 0 0 4px rgba(15, 157, 143, 0.12);
    }
    .form-input.is-error {
        border-color: #dc2626;
    }
    .preview-note {
        font-size: 12px;
        color: #64748b;
        line-height: 1.6;
    }
    .err {
        font-size: 12px;
        color: #dc2626;
    }
    .profile-footer {
        margin-top: 22px;
        display: flex;
        justify-content: flex-end;
    }
    .btn-save {
        border: none;
        border-radius: 14px;
        padding: 11px 18px;
        font-size: 13.5px;
        font-weight: 700;
        color: #fff;
        cursor: pointer;
        background: linear-gradient(135deg, #2563eb 0%, #0f9d8f 100%);
        box-shadow: 0 14px 28px rgba(15, 157, 143, 0.22);
    }
    @media (max-width: 820px) {
        .profile-head {
            flex-direction: column;
            align-items: flex-start;
        }
        .profile-form {
            grid-template-columns: 1fr;
        }
    }
</style>
@endpush

@section('content')
@php
    $initials = collect(explode(' ', trim($user->name)))->filter()->take(2)->map(fn ($part) => strtoupper(substr($part, 0, 1)))->implode('');
@endphp

<div class="profile-shell">
    <div class="profile-card">
        <div class="profile-head">
            @if($user->profile_photo_url)
                <img src="{{ $user->profile_photo_url }}" alt="Foto Profil" class="profile-avatar">
            @else
                <div class="profile-avatar">{{ $initials ?: 'A' }}</div>
            @endif
            <div>
                <h1>Profil Admin</h1>
                <p>Di halaman ini admin hanya bisa memperbarui foto profil dan nomor HP agar identitas akun tetap rapi dan mudah dikenali.</p>
            </div>
        </div>

        <div class="profile-body">
            <form method="POST" action="{{ route('admin.profile.update') }}" enctype="multipart/form-data">
                @csrf

                <div class="profile-form">
                    <section class="form-panel">
                        <h2>Foto Profil</h2>
                        <p>Unggah foto profil baru untuk ditampilkan di navbar admin.</p>

                        <div class="form-group">
                            <label class="form-label" for="profile_photo">Pilih Foto</label>
                            <input class="form-input @error('profile_photo') is-error @enderror" type="file" id="profile_photo" name="profile_photo" accept="image/*">
                            <span class="preview-note">Format gambar umum didukung, maksimal 2 MB.</span>
                            @error('profile_photo')<span class="err">{{ $message }}</span>@enderror
                        </div>
                    </section>

                    <section class="form-panel">
                        <h2>Nomor HP</h2>
                        <p>Perbarui nomor HP yang terhubung dengan akun admin ini.</p>

                        <div class="form-group">
                            <label class="form-label" for="phone_number">Nomor HP</label>
                            <input class="form-input @error('phone_number') is-error @enderror" type="text" id="phone_number" name="phone_number" value="{{ old('phone_number', $user->phone_number) }}" placeholder="Masukkan nomor HP" required>
                            @error('phone_number')<span class="err">{{ $message }}</span>@enderror
                        </div>
                    </section>
                </div>

                <div class="profile-footer">
                    <button type="submit" class="btn-save">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
