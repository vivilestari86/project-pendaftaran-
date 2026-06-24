@extends('admin.layout.app')

@section('title', 'Edit User')

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
        display: flex; align-items: center; gap: 14px;
    }
    .fh-avatar {
        width: 44px; height: 44px; border-radius: 50%;
        background: var(--brand); color: #fff;
        display: flex; align-items: center; justify-content: center;
        font-size: 15px; font-weight: 700; flex-shrink: 0;
    }
    .form-header h2 { font-size: 16px; font-weight: 700; }
    .form-header p  { font-size: 13px; color: var(--text-secondary); margin-top: 2px; }

    .form-body { padding: 24px; display: flex; flex-direction: column; gap: 18px; }
    .form-row  { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }
    .form-group { display: flex; flex-direction: column; gap: 6px; }
    .form-label { font-size: 13px; font-weight: 600; }
    .section-label {
        font-size: 11px; font-weight: 700; color: var(--text-muted);
        text-transform: uppercase; letter-spacing: .6px;
    }
    .divider { border: none; border-top: 1px solid var(--border); }

    .form-input, .form-select {
        padding: 9px 12px; border: 1px solid var(--border);
        border-radius: var(--radius-sm); font-size: 13.5px;
        color: var(--text-primary); background: #fff; outline: none;
        transition: border-color .15s;
    }
    .form-input:focus, .form-select:focus { border-color: var(--brand); box-shadow: 0 0 0 3px #e0e7ff; }
    .form-input.is-error { border-color: var(--red); }
    .err  { font-size: 12px; color: var(--red); }
    .hint { font-size: 12px; color: var(--text-muted); }

    .form-footer {
        padding: 16px 24px; border-top: 1px solid var(--border);
        display: flex; justify-content: flex-end; gap: 10px;
    }
    .btn-cancel {
        padding: 9px 16px; background: none; border: 1px solid var(--border);
        border-radius: var(--radius-sm); font-size: 13.5px; font-weight: 600;
        color: var(--text-secondary); cursor: pointer; text-decoration: none;
        transition: background .15s;
    }
    .btn-cancel:hover { background: var(--bg); }
    .btn-submit {
        padding: 9px 20px; background: var(--brand); color: #fff;
        border: none; border-radius: var(--radius-sm);
        font-size: 13.5px; font-weight: 600; cursor: pointer;
        transition: background .15s;
    }
    .btn-submit:hover { background: var(--brand-dark); }
</style>
@endpush

@section('content')
<a href="{{ route('admin.manage-users.index') }}" class="back-link">
    <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><polyline points="15 18 9 12 15 6"/></svg>
    Kembali ke User Management
</a>

<div class="form-card">
    <div class="form-header">
        <div class="fh-avatar">
            {{ strtoupper(substr($user->name, 0, 1)) }}{{ strtoupper(substr(strstr($user->name, ' '), 1, 1)) }}
        </div>
        <div>
            <h2>Edit User</h2>
            <p>{{ $user->email }}</p>
        </div>
    </div>

    <form method="POST" action="{{ route('admin.manage-users.update', $user) }}">
        @csrf @method('PUT')
        <div class="form-body">
            <p class="section-label">Informasi Dasar</p>

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label" for="name">Full Name</label>
                    <input class="form-input @error('name') is-error @enderror"
                           type="text" id="name" name="name"
                           value="{{ old('name', $user->name) }}" required>
                    @error('name')<span class="err">{{ $message }}</span>@enderror
                </div>
                <div class="form-group">
                    <label class="form-label" for="email">Email Address</label>
                    <input class="form-input @error('email') is-error @enderror"
                           type="email" id="email" name="email"
                           value="{{ old('email', $user->email) }}" required>
                    @error('email')<span class="err">{{ $message }}</span>@enderror
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label" for="phone_number">Phone Number</label>
                    <input class="form-input @error('phone_number') is-error @enderror"
                           type="text" id="phone_number" name="phone_number"
                           value="{{ old('phone_number', $user->phone_number) }}" required>
                    @error('phone_number')<span class="err">{{ $message }}</span>@enderror
                </div>
                <div class="form-group"></div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label" for="role">Role</label>
                    <select class="form-select" id="role" name="role" required>
                        @foreach(['admin' => 'Admin', 'user' => 'User'] as $value => $label)
                            <option value="{{ $value }}" {{ old('role', $user->role) === $value ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label" for="status">Status</label>
                    <select class="form-select" id="status" name="status" required>
                        <option value="Active"   {{ old('status', $user->status) === 'Active'   ? 'selected' : '' }}>Active</option>
                        <option value="Inactive" {{ old('status', $user->status) === 'Inactive' ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>
            </div>

            <hr class="divider">
            <p class="section-label">
                Ubah Password
                <span style="font-weight:400;text-transform:none;letter-spacing:0;font-size:12px"> — kosongkan jika tidak berubah</span>
            </p>

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label" for="password">Password Baru</label>
                    <input class="form-input @error('password') is-error @enderror"
                           type="password" id="password" name="password"
                           placeholder="Min. 8 karakter">
                    @error('password')<span class="err">{{ $message }}</span>@enderror
                </div>
                <div class="form-group">
                    <label class="form-label" for="password_confirmation">Konfirmasi Password</label>
                    <input class="form-input" type="password"
                           id="password_confirmation" name="password_confirmation"
                           placeholder="Ulangi password baru">
                </div>
            </div>
        </div>

        <div class="form-footer">
            <a href="{{ route('admin.manage-users.index') }}" class="btn-cancel">Batal</a>
            <button type="submit" class="btn-submit">Simpan Perubahan</button>
        </div>
    </form>
</div>
@endsection
