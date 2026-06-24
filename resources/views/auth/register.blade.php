<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Account - RSUD Healthcare</title>
    <link rel="stylesheet" href="{{ asset('css/register.css') }}">
</head>
<body>
    <div class="container">
        <!-- Left Section -->
        <div class="left-section">
            <div>
                <div class="logo">⚕️</div>
                <div class="tagline">
                    <h1>Compassionate Care, Digital Precision.</h1>
                    <p>Join the RSUD Regional Healthcare network to manage your health records, schedule appointments, and receive personalized medical updates.</p>
                </div>

                <div class="features">
                    <div class="feature">
                        <div class="feature-icon">🔐</div>
                        <h3>Secure Records</h3>
                        <p>HIPAA compliant data encryption for all patients.</p>
                    </div>
                    <div class="feature">
                        <div class="feature-icon">⚡</div>
                        <h3>Fast Track</h3>
                        <p>Reduce waiting room time with pre-registration.</p>
                    </div>
                </div>
            </div>

            <div>
                <div class="trusted">
                    <div class="avatars">
                        <div class="avatar">👨</div>
                        <div class="avatar">👩</div>
                        <div class="avatar">👴</div>
                    </div>
                    <span>Trusted by 50,000+ local residents</span>
                </div>
                <div class="footer">
                    © 2024 RSUD Regional Healthcare. All rights reserved.
                </div>
            </div>
        </div>

        <!-- Right Section -->
        <div class="right-section">
            <div class="register-form">
                <h2>Create Account</h2>
                <p class="subtitle">Complete the form below to start your healthcare journey.</p>

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

                    <!-- Full Name -->
                    <div class="form-group">
                        <label for="name">Full Name</label>
                        <div class="input-wrapper">
                            <span class="input-icon">👤</span>
                            <input type="text" id="name" name="name" 
                                placeholder="John Doe" 
                                value="{{ old('name') }}" 
                                required>
                        </div>
                        @error('name')
                            <span style="font-size: 12px; color: #dc3545;">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Profesi -->
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

                    <!-- Email -->
                    <div class="form-group">
                        <label for="email">Email Address</label>
                        <div class="input-wrapper">
                            <span class="input-icon">✉️</span>
                            <input type="email" id="email" name="email" 
                                placeholder="john@example.com" 
                                value="{{ old('email') }}" 
                                required>
                        </div>
                        @error('email')
                            <span style="font-size: 12px; color: #dc3545;">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Phone Number -->
                    <div class="form-group">
                        <label for="phone_number">Phone Number</label>
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

                    <!-- Password -->
                    <div class="form-group">
                        <label for="password">Password</label>
                        <div class="input-wrapper">
                            <span class="input-icon">🔒</span>
                            <input type="password" id="password" name="password" 
                                placeholder="••••••••" 
                                required>
                            <button type="button" class="toggle-password" onclick="togglePassword()">👁️</button>
                        </div>
                        <p class="password-note">Must be at least 8 characters.</p>
                        @error('password')
                            <span style="font-size: 12px; color: #dc3545;">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Confirm Password -->
                    <div class="form-group">
                        <label for="password_confirmation">Confirm Password</label>
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

                    <!-- Terms Checkbox -->
                    <div class="checkbox-group">
                        <input type="checkbox" id="terms" name="terms_agreed" required>
                        <label for="terms" class="checkbox-label">
                            I agree to the <a href="#">Terms of Service</a> and <a href="#">Privacy Policy</a> of RSUD Regional Healthcare.
                        </label>
                    </div>
                    @error('terms_agreed')
                        <span style="font-size: 12px; color: #dc3545;">{{ $message }}</span>
                    @enderror

                    <!-- Register Button -->
                    <button type="submit" class="btn-register">
                        Register Account <span>→</span>
                    </button>

                    <!-- Divider -->
                    <div class="divider">
                        <span>OR</span>
                    </div>

                    <!-- Google Button -->
                    <button type="button" class="btn-google">
                        <span>👤</span> Sign up with Google
                    </button>

                    <!-- Login Link -->
                    <div class="login-link">
                        Already have an account? <a href="{{ route('login') }}">Sign In</a>
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
