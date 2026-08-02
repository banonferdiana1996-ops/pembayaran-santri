<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\KelasController;
use App\Http\Controllers\PlaceholderController;
use App\Http\Controllers\SantriController;
use App\Http\Controllers\TahunAjaranController;
use App\Http\Controllers\UserController;
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

    // ===== Master Data (admin) =====
    Route::middleware('role:admin')->group(function () {
        Route::get('/tahun-ajaran', [TahunAjaranController::class, 'index'])->name('tahun-ajaran.index');
        Route::post('/tahun-ajaran', [TahunAjaranController::class, 'store'])->name('tahun-ajaran.store');
        Route::put('/tahun-ajaran/{tahunAjaran}', [TahunAjaranController::class, 'update'])->name('tahun-ajaran.update');
        Route::delete('/tahun-ajaran/{tahunAjaran}', [TahunAjaranController::class, 'destroy'])->name('tahun-ajaran.destroy');

        Route::get('/kelas', [KelasController::class, 'index'])->name('kelas.index');
        Route::post('/kelas', [KelasController::class, 'store'])->name('kelas.store');
        Route::put('/kelas/{kelas}', [KelasController::class, 'update'])->name('kelas.update');
        Route::delete('/kelas/{kelas}', [KelasController::class, 'destroy'])->name('kelas.destroy');

        Route::get('/santri', [SantriController::class, 'index'])->name('santri.index');
        Route::post('/santri', [SantriController::class, 'store'])->name('santri.store');
        Route::put('/santri/{santri}', [SantriController::class, 'update'])->name('santri.update');
        Route::delete('/santri/{santri}', [SantriController::class, 'destroy'])->name('santri.destroy');

        Route::get('/users', [UserController::class, 'index'])->name('users.index');
        Route::post('/users', [UserController::class, 'store'])->name('users.store');
        Route::put('/users/{user}', [UserController::class, 'update'])->name('users.update');
        Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');
    });

    // ===== Routing skeleton — modul dibangun bertahap =====
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
