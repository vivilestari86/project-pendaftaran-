<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function showLogin()
    {
        if (Auth::check()) {
            return Auth::user()->isAdmin()
                ? redirect()->route('admin.dashboard')
                : redirect()->route('user.dashboard');
        }

        return view('auth.login');
    }

    public function login(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ], [
            'email.required' => 'Email harus diisi',
            'email.email' => 'Format email tidak valid',
            'password.required' => 'Password harus diisi',
        ]);

        if (!Auth::attempt([
            'email' => $validated['email'],
            'password' => $validated['password'],
        ], $request->boolean('remember'))) {
            return back()->withErrors([
                'email' => 'Email atau password salah',
            ])->onlyInput('email');
        }

        $request->session()->regenerate();

        /** @var \App\Models\User $user */
        $user = Auth::user();

        if ($user->status === 'Inactive') {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return back()->withErrors([
                'email' => 'Akun Anda sedang tidak aktif.',
            ])->onlyInput('email');
        }

        $user->forceFill([
            'last_active_at' => now(),
        ])->save();

        return redirect()->route('dashboard');
    }

    public function showRegister()
    {
        if (Auth::check()) {
            return Auth::user()->isAdmin()
                ? redirect()->route('admin.dashboard')
                : redirect()->route('user.dashboard');
        }

        return view('auth.register');
    }

    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phone_number' => 'required|string|max:20',
            'password' => 'required|string|min:8|confirmed',
            'terms_agreed' => 'required|accepted',
        ], [
            'name.required' => 'Nama harus diisi',
            'email.required' => 'Email harus diisi',
            'email.email' => 'Format email tidak valid',
            'email.unique' => 'Email sudah terdaftar',
            'phone_number.required' => 'Nomor telepon harus diisi',
            'password.required' => 'Password harus diisi',
            'password.min' => 'Password minimal 8 karakter',
            'password.confirmed' => 'Password tidak cocok',
            'terms_agreed.required' => 'Anda harus menyetujui syarat dan ketentuan',
            'terms_agreed.accepted' => 'Anda harus menyetujui syarat dan ketentuan',
        ]);

        User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone_number' => $validated['phone_number'],
            'password' => Hash::make($validated['password']),
            'role' => 'user',
            'status' => 'Active',
            'terms_agreed' => true,
        ]);

        return redirect()->route('login')->with('success', 'Registrasi berhasil! Silakan login.');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success', 'Logout berhasil!');
    }
}
