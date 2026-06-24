<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ManageUserController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
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
        return view('welcome');
    })->name('user.dashboard');

    Route::prefix('admin')->name('admin.')->middleware('is.admin')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
        Route::get('/pendaftar/{pendaftar}/detail', [DashboardController::class, 'detail'])->name('pendaftar.detail');
        Route::get('/pendaftar/{pendaftar}/edit-form', [DashboardController::class, 'editForm'])->name('pendaftar.edit-form');
        Route::put('/pendaftar/{pendaftar}/update', [DashboardController::class, 'update'])->name('pendaftar.update');

        Route::resource('manage-users', ManageUserController::class)->except(['show']);
    });
});
