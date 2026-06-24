<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrasi User - Portal Pendaftaran</title>
    <link rel="stylesheet" href="{{ asset('css/register.css') }}">
</head>
<body>
    <div class="container">
        <div class="left-section">
            <div class="left-content">
                <div class="logo">
                    <img src="{{ asset('images/logo.png') }}" alt="Logo Politeknik Negeri Indramayu" class="brand-logo">
                </div>

                <div class="tagline">
                    <h1>Registrasi portal polindra</h1>
                </div>

                <div class="features">
                    <div class="feature">
                        <div class="feature-icon">🔐</div>
                        <h3>Akun User</h3>
                        <p>Form ini membuat akun dengan role user untuk akses aplikasi.</p>
                    </div>
                    <div class="feature">
                        <div class="feature-icon">⚡</div>
                        <h3>Akses Cepat</h3>
                        <p>Setelah daftar, user bisa langsung login tanpa proses admin register.</p>
                    </div>
                </div>
            </div>

            <div class="left-footer">
                <div class="trusted">
                    <div class="avatars">
                        <div class="avatar">👨</div>
                        <div class="avatar">👩</div>
                        <div class="avatar">👴</div>
                    </div>
                    <span>Registrasi publik hanya tersedia untuk user</span>
                </div>
            </div>
        </div>

        <div class="right-section">
            <div class="register-form">
                <div class="register-logo-wrap">
                    <img src="{{ asset('images/logo.png') }}" alt="Logo Politeknik Negeri Indramayu" class="register-logo">
                </div>

                <h2>Registrasi User</h2>
                <p class="subtitle">Isi form berikut untuk membuat akun user.</p>

                @if ($errors->any())
                    <div class="error-message">
                        @foreach ($errors->all() as $error)
                            <div class="error-item">{{ $error }}</div>
                        @endforeach
                    </div>
                @endif

                @if (session('success'))
                    <div class="success-message">
                        {{ session('success') }}
                    </div>
                @endif

                <form method="POST" action="{{ route('register.submit') }}">
                    @csrf

                    <div class="form-group">
                        <label for="name">Nama Lengkap</label>
                        <div class="input-wrapper">
                            <span class="input-icon">👤</span>
                            <input type="text" id="name" name="name"
                                placeholder="Nama lengkap"
                                value="{{ old('name') }}"
                                required>
                        </div>
                        @error('name')
                            <span style="font-size: 12px; color: #dc3545;">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="profesi">Profesi</label>
                        <div class="input-wrapper">
                            <span class="input-icon">💼</span>
                            <input type="text" id="profesi" name="profesi"
                                placeholder="Dokter / Perawat / Bidan"
                                value="{{ old('profesi') }}"
                                required>
                        </div>
                        @error('profesi')
                            <span style="font-size: 12px; color: #dc3545;">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="email">Alamat Email</label>
                        <div class="input-wrapper">
                            <span class="input-icon">✉️</span>
                            <input type="email" id="email" name="email"
                                placeholder="email@contoh.com"
                                value="{{ old('email') }}"
                                required>
                        </div>
                        @error('email')
                            <span style="font-size: 12px; color: #dc3545;">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="phone_number">Nomor Telepon</label>
                        <div class="input-wrapper">
                            <span class="input-icon">📱</span>
                            <input type="tel" id="phone_number" name="phone_number"
                                placeholder="+62 800-0000-000"
                                value="{{ old('phone_number') }}"
                                required>
                        </div>
                        @error('phone_number')
                            <span style="font-size: 12px; color: #dc3545;">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="password">Password</label>
                        <div class="input-wrapper">
                            <span class="input-icon">🔒</span>
                            <input type="password" id="password" name="password"
                                placeholder="••••••••"
                                required>
                            <button type="button" class="toggle-password" onclick="togglePassword()">👁️</button>
                        </div>
                        <p class="password-note">Minimal 8 karakter.</p>
                        @error('password')
                            <span style="font-size: 12px; color: #dc3545;">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="password_confirmation">Konfirmasi Password</label>
                        <div class="input-wrapper">
                            <span class="input-icon">🔒</span>
                            <input type="password" id="password_confirmation" name="password_confirmation"
                                placeholder="••••••••"
                                required>
                            <button type="button" class="toggle-password" onclick="togglePasswordConfirm()">👁️</button>
                        </div>
                        @error('password_confirmation')
                            <span style="font-size: 12px; color: #dc3545;">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="checkbox-group">
                        <input type="checkbox" id="terms" name="terms_agreed" required>
                        <label for="terms" class="checkbox-label">
                            Saya menyetujui <a href="#">Syarat Layanan</a> dan <a href="#">Kebijakan Privasi</a>.
                        </label>
                    </div>
                    @error('terms_agreed')
                        <span style="font-size: 12px; color: #dc3545;">{{ $message }}</span>
                    @enderror

                    <button type="submit" class="btn-register">
                        Daftar Akun <span>→</span>
                    </button>

                    <div class="divider">
                        <span>OR</span>
                    </div>

                    <div class="login-link">
                        Sudah punya akun? <a href="{{ route('login') }}">Masuk</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function togglePassword() {
            const passwordInput = document.getElementById('password');
            const toggleIcon = event.target;

            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                toggleIcon.textContent = '🙈';
            } else {
                passwordInput.type = 'password';
                toggleIcon.textContent = '👁️';
            }
        }

        function togglePasswordConfirm() {
            const passwordInput = document.getElementById('password_confirmation');
            const toggleIcon = event.target;

            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                toggleIcon.textContent = '🙈';
            } else {
                passwordInput.type = 'password';
                toggleIcon.textContent = '👁️';
            }
        }
    </script>
</body>
</html>
