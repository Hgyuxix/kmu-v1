<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\LayananController;
use App\Http\Controllers\PermohonanController;
use App\Http\Controllers\DashboardController;

// =========================
// PUBLIC
// =========================

Route::get('/', [LayananController::class, 'index'])->name('layanan.index');
Route::get('/layanan/{layanan}', [LayananController::class, 'show'])->name('layanan.show');
Route::get('/layanan/{layanan}/ajukan', [PermohonanController::class, 'create'])->name('permohonan.create');
Route::post('/layanan/{layanan}/ajukan', [PermohonanController::class, 'store'])->name('permohonan.store');
Route::get('/permohonan/{permohonan}/preview', [PermohonanController::class, 'preview'])->name('permohonan.preview');

// =========================
// LOGIN
// =========================

Route::middleware('guest')->group(function () {

    Route::get('/login', function () {
        return view('auth.login');
    })->name('login');

    Route::post('/login', [AuthController::class, 'login'])
        ->name('login.process');

});

// =========================
// DASHBOARD FO
// =========================

Route::middleware('auth')->group(function () {

    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->name('dashboard');

    Route::get('/dashboard/pengajuan/{permohonan}',
        [DashboardController::class, 'show'])
        ->name('dashboard.pengajuan.show');

    Route::patch('/dashboard/pengajuan/{permohonan}/status',
        [DashboardController::class, 'updateStatus'])
        ->name('dashboard.pengajuan.status');

    Route::post('/logout', [AuthController::class, 'logout'])
        ->name('logout');

});
