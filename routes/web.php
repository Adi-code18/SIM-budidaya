<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DistribusiController;
use App\Http\Controllers\KeuanganWebController;
use App\Http\Controllers\MitraController;
use App\Http\Controllers\MobilePetugasController;
use App\Http\Controllers\PakanController;
use App\Http\Controllers\PembesaranController;
use App\Http\Controllers\PembibitanController;
use App\Http\Controllers\PembudidayaController;
use App\Http\Controllers\PetugasController;
use Illuminate\Support\Facades\Route;

// Redirect root to dashboard or login
Route::get('/', function () {
    return redirect()->route('dashboard');
});

// Authentication Web Manajer Routes
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// 2FA Routes (Pre-login & Authenticated)
Route::get('/login/2fa', [\App\Http\Controllers\TwoFactorController::class, 'show2faForm'])->name('2fa.login');
Route::post('/login/2fa', [\App\Http\Controllers\TwoFactorController::class, 'verify2fa'])->name('2fa.verify');
Route::get('/2fa/setup', [\App\Http\Controllers\TwoFactorController::class, 'showSetup'])->name('2fa.setup');
Route::post('/2fa/confirm', [\App\Http\Controllers\TwoFactorController::class, 'confirm'])->name('2fa.confirm');

Route::middleware(['auth'])->group(function () {
    Route::post('/2fa/disable', [\App\Http\Controllers\TwoFactorController::class, 'disable'])->name('2fa.disable');
});

// Web Manajer Terproteksi (Wajib Login & Peran 'manajer')
Route::middleware(['auth', 'role:manajer'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/pembibitan', [PembibitanController::class, 'index'])->name('pembibitan');
    Route::post('/pembibitan', [PembibitanController::class, 'store'])->name('pembibitan.store');
    Route::put('/pembibitan/{id}', [PembibitanController::class, 'update'])->name('pembibitan.update');
    Route::delete('/pembibitan/{id}', [PembibitanController::class, 'destroy'])->name('pembibitan.destroy');
    Route::get('/pembesaran', [PembesaranController::class, 'index'])->name('pembesaran');
    Route::post('/pembesaran', [PembesaranController::class, 'store'])->name('pembesaran.store');
    Route::put('/pembesaran/{id}', [PembesaranController::class, 'update'])->name('pembesaran.update');
    Route::delete('/pembesaran/{id}', [PembesaranController::class, 'destroy'])->name('pembesaran.destroy');
    Route::get('/pembudidaya', [PembudidayaController::class, 'index'])->name('pembudidaya');
    Route::get('/log-pakan', [PakanController::class, 'index'])->name('log-pakan');
    Route::get('/distribusi', [DistribusiController::class, 'index'])->name('distribusi');
    Route::get('/keuangan', [KeuanganWebController::class, 'index'])->name('keuangan');
    Route::get('/mitra', [MitraController::class, 'index'])->name('mitra');
    Route::get('/petugas', [PetugasController::class, 'index'])->name('petugas');
    Route::post('/petugas', [PetugasController::class, 'store'])->name('petugas.store');
    Route::get('/petugas/create', [PetugasController::class, 'create'])->name('petugas.create');
    Route::get('/petugas/{id}/edit', [PetugasController::class, 'edit'])->name('petugas.edit');
    Route::put('/petugas/{id}', [PetugasController::class, 'update'])->name('petugas.update');
    Route::delete('/petugas/{id}', [PetugasController::class, 'destroy'])->name('petugas.destroy');
    Route::get('/petugas/libur/approval', [PetugasController::class, 'approvalLibur'])->name('petugas.libur.approval');
    Route::get('/petugas/libur/ajukan', [PetugasController::class, 'ajukanLibur'])->name('petugas.libur.ajukan');
});

// Mobile Web Petugas Routes
Route::prefix('mobile-petugas')->name('mobile.petugas.')->group(function () {
    Route::get('/splash', function () {
        return view('mobile_web_petugas.splash', ['role' => 'distribusi']);
    })->name('splash');

    Route::get('/login', [AuthController::class, 'showMobileLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'mobileLogin'])->name('login.post');
    Route::post('/logout', [AuthController::class, 'mobileLogout'])->name('logout');

    // Terproteksi Peran Petugas Distribusi
    Route::middleware(['auth', 'role:petugas_distribusi'])->group(function () {
        Route::get('/pengiriman', [MobilePetugasController::class, 'distribusiIndex'])->name('pengiriman');
        Route::get('/detail/{id?}', [MobilePetugasController::class, 'distribusiDetail'])->name('detail');
        Route::post('/complete/{id}', [MobilePetugasController::class, 'distribusiComplete'])->name('complete');
        Route::get('/riwayat', [MobilePetugasController::class, 'distribusiRiwayat'])->name('riwayat');
        Route::get('/akun', function () {
            return view('mobile_web_petugas.petugas_distribusi.akun');
        })->name('akun');
    });
});

// Mobile Web Petugas Pembibitan Routes
Route::prefix('petugas-pembibitan')->name('petugas.pembibitan.')->group(function () {
    Route::get('/splash', function () {
        return view('mobile_web_petugas.splash', ['role' => 'pembibitan']);
    })->name('splash');

    Route::get('/login', function () {
        return redirect()->route('mobile.petugas.login', ['role' => 'pembibitan']);
    })->name('login');

    // Terproteksi Peran Pembibitan
    Route::middleware(['auth', 'role:pembibitan'])->group(function () {
        Route::get('/', [MobilePetugasController::class, 'pembibitanIndex'])->name('dashboard');
        Route::get('/form', [MobilePetugasController::class, 'pembibitanForm'])->name('form');
        Route::post('/form', [MobilePetugasController::class, 'pembibitanStoreBatch'])->name('store-batch');
        Route::get('/log-pakan', [MobilePetugasController::class, 'pembibitanLogPakan'])->name('log-pakan');
        Route::get('/akun', function () {
            return view('mobile_web_petugas.petugas_pembibitan.akun');
        })->name('akun');
    });
});

// Mobile Web Petugas Pembesaran Routes
Route::prefix('petugas-pembesaran')->name('petugas.pembesaran.')->group(function () {
    Route::get('/splash', function () {
        return view('mobile_web_petugas.splash', ['role' => 'pembesaran']);
    })->name('splash');

    Route::get('/login', function () {
        return redirect()->route('mobile.petugas.login', ['role' => 'pembesaran']);
    })->name('login');

    // Terproteksi Peran Pembesaran
    Route::middleware(['auth', 'role:pembesaran'])->group(function () {
        Route::get('/', [MobilePetugasController::class, 'pembesaranIndex'])->name('dashboard');
        Route::get('/create-batch', [MobilePetugasController::class, 'pembesaranCreateBatch'])->name('create-batch');
        Route::post('/create-batch', [MobilePetugasController::class, 'pembesaranStoreBatch'])->name('store-batch');
        Route::get('/log-pakan', [MobilePetugasController::class, 'pembesaranLogPakan'])->name('log-pakan');
        Route::get('/akun', function () {
            return view('mobile_web_petugas.petugas_pembesaran.akun');
        })->name('akun');
    });
});
