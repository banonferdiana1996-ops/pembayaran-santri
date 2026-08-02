<?php

use App\Http\Controllers\AccountController;
use App\Http\Controllers\AnnouncementController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\BackupController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\IncomeController;
use App\Http\Controllers\JenisPembayaranController;
use App\Http\Controllers\KelasController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\PembayaranController;
use App\Http\Controllers\SantriController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\TagihanController;
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

    // ===== Akun pribadi (semua role) =====
    Route::get('/pengaturan-user', [AccountController::class, 'showProfile'])->name('account.profile');
    Route::put('/pengaturan-user', [AccountController::class, 'updateProfile'])->name('account.updateProfile');
    Route::get('/ubah-password', [AccountController::class, 'showPassword'])->name('account.password');
    Route::put('/ubah-password', [AccountController::class, 'updatePassword'])->name('account.updatePassword');

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

    // ===== Pembayaran (admin & bendahara) =====
    Route::middleware('role:admin|bendahara')->group(function () {
        Route::get('/jenis-pembayaran', [JenisPembayaranController::class, 'index'])->name('jenis-pembayaran.index');
        Route::post('/jenis-pembayaran', [JenisPembayaranController::class, 'store'])->name('jenis-pembayaran.store');
        Route::put('/jenis-pembayaran/{jenisPembayaran}', [JenisPembayaranController::class, 'update'])->name('jenis-pembayaran.update');
        Route::delete('/jenis-pembayaran/{jenisPembayaran}', [JenisPembayaranController::class, 'destroy'])->name('jenis-pembayaran.destroy');

        Route::get('/tagihan', [TagihanController::class, 'index'])->name('tagihan.index');
        Route::post('/tagihan/generate', [TagihanController::class, 'generate'])->name('tagihan.generate');
        Route::delete('/tagihan/{tagihan}', [TagihanController::class, 'destroy'])->name('tagihan.destroy');

        Route::get('/pembayaran', [PembayaranController::class, 'index'])->name('pembayaran.index');
        Route::get('/pembayaran/buat', [PembayaranController::class, 'create'])->name('pembayaran.create');
        Route::get('/pembayaran/santri/{santri}/tagihan-belum-lunas', [PembayaranController::class, 'tagihanBelumLunas'])->name('pembayaran.tagihan-belum-lunas');
        Route::post('/pembayaran', [PembayaranController::class, 'store'])->name('pembayaran.store');
        Route::get('/pembayaran/{pembayaran}/kwitansi', [PembayaranController::class, 'kwitansi'])->name('pembayaran.kwitansi');
        Route::get('/pembayaran/{pembayaran}/unduh', [PembayaranController::class, 'unduhKwitansi'])->name('pembayaran.unduh');
        Route::delete('/pembayaran/{pembayaran}', [PembayaranController::class, 'destroy'])->name('pembayaran.destroy');
    });

    // ===== Kas (income & expense) — admin & bendahara =====
    Route::middleware('role:admin|bendahara')->group(function () {
        Route::get('/income', [IncomeController::class, 'index'])->name('income.index');
        Route::post('/income', [IncomeController::class, 'store'])->name('income.store');
        Route::put('/income/{income}', [IncomeController::class, 'update'])->name('income.update');
        Route::delete('/income/{income}', [IncomeController::class, 'destroy'])->name('income.destroy');

        Route::get('/expense', [ExpenseController::class, 'index'])->name('expense.index');
        Route::post('/expense', [ExpenseController::class, 'store'])->name('expense.store');
        Route::put('/expense/{expense}', [ExpenseController::class, 'update'])->name('expense.update');
        Route::delete('/expense/{expense}', [ExpenseController::class, 'destroy'])->name('expense.destroy');

        Route::get('/laporan', [LaporanController::class, 'index'])->name('report.index');
        Route::get('/laporan/unduh-pdf', [LaporanController::class, 'unduhPdf'])->name('report.pdf');
        Route::get('/laporan/unduh-excel', [LaporanController::class, 'unduhExcel'])->name('report.excel');
    });

    // ===== Pengaturan — admin =====
    Route::middleware('role:admin')->group(function () {
        Route::get('/pengumuman', [AnnouncementController::class, 'index'])->name('announcement.index');
        Route::post('/pengumuman', [AnnouncementController::class, 'store'])->name('announcement.store');
        Route::put('/pengumuman/{announcement}', [AnnouncementController::class, 'update'])->name('announcement.update');
        Route::delete('/pengumuman/{announcement}', [AnnouncementController::class, 'destroy'])->name('announcement.destroy');

        Route::get('/pengaturan', [SettingController::class, 'index'])->name('setting.index');
        Route::put('/pengaturan', [SettingController::class, 'update'])->name('setting.update');

        Route::get('/backup', [BackupController::class, 'index'])->name('backup.index');
        Route::post('/backup', [BackupController::class, 'store'])->name('backup.store');
        Route::get('/backup/{backup}/unduh', [BackupController::class, 'unduh'])->name('backup.unduh');
        Route::delete('/backup/{backup}', [BackupController::class, 'destroy'])->name('backup.destroy');
    });
});
