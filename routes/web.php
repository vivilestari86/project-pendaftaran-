<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ManageUserController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    if (auth()->check()) {
        return auth()->user()->isAdmin()
            ? redirect()->route('admin.dashboard')
            : redirect()->route('user.dashboard');
    }

    return redirect()->route('login');
})->name('home');

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.submit');
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('register.submit');
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    Route::get('/dashboard', function () {
        if (! auth()->check()) {
            return redirect()->route('login');
        }

        $user = auth()->user();

        if ($user->isAdmin()) {
            return redirect()->route('admin.dashboard');
        }

        return view('user.dashboard', compact('user'));
    })->name('user.dashboard');

    Route::prefix('admin')->name('admin.')->middleware('is.admin')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
        Route::get('/dashboard/export-excel', [DashboardController::class, 'exportExcel'])->name('dashboard.export-excel');
        Route::get('/pendaftar/{pendaftar}/detail', [DashboardController::class, 'detail'])->name('pendaftar.detail');

        Route::post('/manage-users/{manage_user}/verify', [ManageUserController::class, 'verify'])->name('manage-users.verify');
        Route::resource('manage-users', ManageUserController::class)->except(['show', 'create', 'store']);
    });
});
