<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PlaceholderController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route(auth()->check() ? 'dashboard' : 'login');
});

Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // ===== Routing skeleton — modul dibangun bertahap =====
    Route::get('/tahun-ajaran', PlaceholderController::class)->name('tahun-ajaran.index');
    Route::get('/kelas', PlaceholderController::class)->name('kelas.index');
    Route::get('/santri', PlaceholderController::class)->name('santri.index');
    Route::get('/users', PlaceholderController::class)->name('users.index');
    Route::get('/jenis-pembayaran', PlaceholderController::class)->name('jenis-pembayaran.index');
    Route::get('/tagihan', PlaceholderController::class)->name('tagihan.index');
    Route::get('/pembayaran', PlaceholderController::class)->name('pembayaran.index');
    Route::get('/income', PlaceholderController::class)->name('income.index');
    Route::get('/expense', PlaceholderController::class)->name('expense.index');
    Route::get('/laporan', PlaceholderController::class)->name('report.index');
    Route::get('/pengumuman', PlaceholderController::class)->name('announcement.index');
    Route::get('/pengaturan', PlaceholderController::class)->name('setting.index');
    Route::get('/backup', PlaceholderController::class)->name('backup.index');
});
