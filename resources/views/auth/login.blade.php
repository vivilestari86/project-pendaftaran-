<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Portal Pendaftaran</title>
    <link rel="stylesheet" href="{{ asset('css/login.css') }}">
</head>
<body>
    <div class="scene">
        <div class="bg-orb orb-one"></div>
        <div class="bg-orb orb-two"></div>
        <div class="bg-orb orb-three"></div>
        <div class="bg-grid"></div>
    </div>

    <main class="page-shell">
        <div class="login-container">
            <div class="logo-mark">
                <img src="{{ asset('images/logo.png') }}" alt="Logo Politeknik Negeri Indramayu" class="logo-image">
            </div>
            <h1>Login</h1>
            <p class="subtitle">Health Console</p>

            @if ($errors->any())
                <div class="error">
                    {{ $errors->first() }}
                </div>
            @endif

            @if (session('success'))
                <div class="success">
                    {{ session('success') }}
                </div>
            @endif

            <form method="POST" action="{{ route('login.submit') }}">
                @csrf

                <div class="form-group">
                    <label for="email">Alamat Email</label>
                    <input type="email" id="email" name="email" value="{{ old('email') }}"
                        placeholder="name@medical-center.com" required>
                </div>

                <div class="form-group">
                    <div class="label-row">
                        <label for="password">Kata Sandi</label>
                        <a href="#" class="forgot-password">Lupa Kata Sandi?</a>
                    </div>
                    <div class="password-wrapper">
                        <input type="password" id="password" name="password" placeholder="••••••••" required>
                        <button type="button" class="toggle-password" onclick="togglePassword(event)" aria-label="Tampilkan password">◉</button>
                    </div>
                </div>

                <button type="submit" class="btn-login">
                    <span>Masuk</span>
                    <span class="btn-icon">⌁</span>
                </button>

                <div class="signup-link">
                    Belum punya akun? <a href="{{ route('register') }}">Daftar Sekarang</a>
                </div>
            </form>
        </div>

        <div class="footer">
            <p>Kebijakan Privasi <span>•</span> Syarat Layanan <span>•</span> Keamanan</p>
            <p>© 2026 Health Enterprise.</p>
        </div>
    </main>

    <script>
        function togglePassword(event) {
            const passwordInput = document.getElementById('password');
            const toggleIcon = event.target;

            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                toggleIcon.textContent = '◎';
            } else {
                passwordInput.type = 'password';
                toggleIcon.textContent = '◉';
            }
        }
    </script>
</body>
</html>
