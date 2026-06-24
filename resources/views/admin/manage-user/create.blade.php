@extends('admin.layout.app')

@section('title', 'Tambah User')

@push('styles')
<style>
    .back-link {
        display: inline-flex; align-items: center; gap: 6px;
        font-size: 13px; color: var(--text-secondary); text-decoration: none; margin-bottom: 20px;
    }
    .back-link:hover { color: var(--text-primary); }
    .form-card {
        background: var(--surface); border: 1px solid var(--border);
        border-radius: var(--radius); box-shadow: var(--shadow-sm); max-width: 620px;
    }
    .form-header {
        padding: 20px 24px; border-bottom: 1px solid var(--border);
    }
    .form-header h2 { font-size: 16px; font-weight: 700; }
    .form-header p  { font-size: 13px; color: var(--text-secondary); margin-top: 2px; }
    .form-body { padding: 24px; display: flex; flex-direction: column; gap: 18px; }
    .form-row  { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }
    .form-group { display: flex; flex-direction: column; gap: 6px; }
    .form-label { font-size: 13px; font-weight: 600; }
    .form-input, .form-select {
        padding: 9px 12px; border: 1px solid var(--border);
        border-radius: var(--radius-sm); font-size: 13.5px;
        color: var(--text-primary); background: #fff; outline: none;
    }
    .form-input:focus, .form-select:focus { border-color: var(--brand); box-shadow: 0 0 0 3px #e0e7ff; }
    .form-input.is-error { border-color: var(--red); }
    .err  { font-size: 12px; color: var(--red); }
    .form-footer {
        padding: 16px 24px; border-top: 1px solid var(--border);
        display: flex; justify-content: flex-end; gap: 10px;
    }
    .btn-cancel, .btn-submit {
        padding: 9px 16px; border-radius: var(--radius-sm);
        font-size: 13.5px; font-weight: 600; text-decoration: none;
    }
    .btn-cancel {
        background: none; border: 1px solid var(--border); color: var(--text-secondary);
    }
    .btn-submit {
        background: var(--brand); color: #fff; border: none; cursor: pointer;
    }
</style>
@endpush

@section('content')
<a href="{{ route('admin.manage-users.index') }}" class="back-link">
    <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><polyline points="15 18 9 12 15 6"/></svg>
    Kembali ke User Management
</a>

<div class="form-card">
    <div class="form-header">
        <h2>Tambah User</h2>
        <p>Buat akun baru untuk admin atau user.</p>
    </div>

    <form method="POST" action="{{ route('admin.manage-users.store') }}">
        @csrf
        <div class="form-body">
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label" for="name">Full Name</label>
                    <input class="form-input @error('name') is-error @enderror" type="text" id="name" name="name" value="{{ old('name') }}" required>
                    @error('name')<span class="err">{{ $message }}</span>@enderror
                </div>
                <div class="form-group">
                    <label class="form-label" for="email">Email Address</label>
                    <input class="form-input @error('email') is-error @enderror" type="email" id="email" name="email" value="{{ old('email') }}" required>
                    @error('email')<span class="err">{{ $message }}</span>@enderror
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label" for="phone_number">Phone Number</label>
                    <input class="form-input @error('phone_number') is-error @enderror" type="text" id="phone_number" name="phone_number" value="{{ old('phone_number') }}" required>
                    @error('phone_number')<span class="err">{{ $message }}</span>@enderror
                </div>
                <div class="form-group">
                    <label class="form-label" for="role">Role</label>
                    <select class="form-select" id="role" name="role" required>
                        <option value="admin" {{ old('role') === 'admin' ? 'selected' : '' }}>Admin</option>
                        <option value="user" {{ old('role', 'user') === 'user' ? 'selected' : '' }}>User</option>
                    </select>
                    @error('role')<span class="err">{{ $message }}</span>@enderror
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label" for="status">Status</label>
                    <select class="form-select" id="status" name="status" required>
                        <option value="Active" {{ old('status', 'Active') === 'Active' ? 'selected' : '' }}>Active</option>
                        <option value="Inactive" {{ old('status') === 'Inactive' ? 'selected' : '' }}>Inactive</option>
                    </select>
                    @error('status')<span class="err">{{ $message }}</span>@enderror
                </div>
                <div class="form-group"></div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label" for="password">Password</label>
                    <input class="form-input @error('password') is-error @enderror" type="password" id="password" name="password" required>
                    @error('password')<span class="err">{{ $message }}</span>@enderror
                </div>
                <div class="form-group">
                    <label class="form-label" for="password_confirmation">Konfirmasi Password</label>
                    <input class="form-input" type="password" id="password_confirmation" name="password_confirmation" required>
                </div>
            </div>
        </div>

        <div class="form-footer">
            <a href="{{ route('admin.manage-users.index') }}" class="btn-cancel">Batal</a>
            <button type="submit" class="btn-submit">Simpan User</button>
        </div>
    </form>
</div>
@endsection
