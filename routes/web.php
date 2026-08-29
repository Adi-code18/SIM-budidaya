<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Auth\EmailOtpController;
use App\Http\Controllers\Auth\TwoFactorController;
use App\Http\Controllers\Manajer\DashboardController;
use App\Http\Controllers\Manajer\DistribusiController;
use App\Http\Controllers\Manajer\KeuanganWebController;
use App\Http\Controllers\Manajer\MitraController;
use App\Http\Controllers\Manajer\PakanController;
use App\Http\Controllers\Manajer\PembesaranController;
use App\Http\Controllers\Manajer\PembibitanController;
use App\Http\Controllers\Manajer\PembudidayaController;
use App\Http\Controllers\Manajer\PetugasController;
use App\Http\Controllers\Manajer\PengaturanController;
use App\Http\Controllers\Petugas\PetugasDistribusiController;
use App\Http\Controllers\Petugas\PetugasPembesaranController;
use App\Http\Controllers\Petugas\PetugasPembibitanController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes - SIM-BUDIDAYA
|--------------------------------------------------------------------------
| Terstruktur rapi berdasarkan peran (Auth, Manajer, Petugas)
*/

// Redirect root to dashboard or login
Route::get('/', function () {
    return redirect()->route('dashboard');
});

// =========================================================================
// 1. AUTENTIKASI & KEAMANAN 2FA / EMAIL OTP (AUTH)
// =========================================================================
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Email OTP Routes (Role Manajer)
Route::get('/login/otp', [EmailOtpController::class, 'showForm'])->name('email.otp.show');
Route::post('/login/otp', [EmailOtpController::class, 'verify'])->name('email.otp.verify');
Route::post('/login/otp/resend', [EmailOtpController::class, 'resend'])->name('email.otp.resend');
Route::get('/login/otp/cancel', [EmailOtpController::class, 'cancel'])->name('email.otp.cancel');

// Google Authenticator 2FA Routes (Role Petugas)
Route::get('/login/2fa', [TwoFactorController::class, 'show2faForm'])->name('2fa.login');
Route::post('/login/2fa', [TwoFactorController::class, 'verify2fa'])->name('2fa.verify');
Route::get('/2fa/setup', [TwoFactorController::class, 'showSetup'])->name('2fa.setup');
Route::post('/2fa/confirm', [TwoFactorController::class, 'confirm'])->name('2fa.confirm');
Route::get('/2fa/cancel', [TwoFactorController::class, 'cancel'])->name('2fa.cancel');

Route::middleware(['auth'])->group(function () {
    Route::post('/2fa/disable', [TwoFactorController::class, 'disable'])->name('2fa.disable');
});

// =========================================================================
// 2. WEB PORTAL MANAJER (ROLE: MANAJER)
// =========================================================================
Route::middleware(['auth', 'role:manajer'])->group(function () {
    // Dashboard Utama
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Manajemen Batch Pembibitan
    Route::get('/pembibitan', [PembibitanController::class, 'index'])->name('pembibitan');
    Route::post('/pembibitan', [PembibitanController::class, 'store'])->name('pembibitan.store');
    Route::put('/pembibitan/{id}', [PembibitanController::class, 'update'])->name('pembibitan.update');
    Route::delete('/pembibitan/{id}', [PembibitanController::class, 'destroy'])->name('pembibitan.destroy');
    Route::post('/pembibitan/{id}/transfer', [PembibitanController::class, 'transferKePembesaran'])->name('pembibitan.transfer');

    // Manajemen Batch Pembesaran
    Route::get('/pembesaran', [PembesaranController::class, 'index'])->name('pembesaran');
    Route::post('/pembesaran', [PembesaranController::class, 'store'])->name('pembesaran.store');
    Route::put('/pembesaran/{id}', [PembesaranController::class, 'update'])->name('pembesaran.update');
    Route::delete('/pembesaran/{id}', [PembesaranController::class, 'destroy'])->name('pembesaran.destroy');

    // Monitoring Kolam & Pembudidaya
    Route::get('/pembudidaya', [PembudidayaController::class, 'index'])->name('pembudidaya');

    // Log Stok & Pakan
    Route::get('/log-pakan', [PakanController::class, 'index'])->name('log-pakan');

    // Distribusi & Pesanan
    Route::get('/distribusi', [DistribusiController::class, 'index'])->name('distribusi');

    // Rekap Keuangan & Margin
    Route::get('/keuangan', [KeuanganWebController::class, 'index'])->name('keuangan');

    // Manajemen Mitra Distributor
    Route::get('/mitra', [MitraController::class, 'index'])->name('mitra');

    // Manajemen Akun Petugas & Cuti/Libur
    Route::get('/petugas', [PetugasController::class, 'index'])->name('petugas');
    Route::post('/petugas', [PetugasController::class, 'store'])->name('petugas.store');
    Route::get('/petugas/create', [PetugasController::class, 'create'])->name('petugas.create');
    Route::get('/petugas/{id}/edit', [PetugasController::class, 'edit'])->name('petugas.edit');
    Route::put('/petugas/{id}', [PetugasController::class, 'update'])->name('petugas.update');
    Route::delete('/petugas/{id}', [PetugasController::class, 'destroy'])->name('petugas.destroy');
    Route::get('/petugas/libur/approval', [PetugasController::class, 'approvalLibur'])->name('petugas.libur.approval');
    Route::get('/petugas/libur/ajukan', [PetugasController::class, 'ajukanLibur'])->name('petugas.libur.ajukan');

    // Halaman Pengaturan & Profil
    Route::get('/pengaturan', [PengaturanController::class, 'index'])->name('pengaturan');
    Route::put('/pengaturan/profile', [PengaturanController::class, 'updateProfile'])->name('pengaturan.update-profile');
    Route::put('/pengaturan/preferences', [PengaturanController::class, 'updatePreferences'])->name('pengaturan.update-preferences');
});

// =========================================================================
// 3. MOBILE WEB PETUGAS (ROLE: DISTRIBUSI, PEMBIBITAN, PEMBESARAN)
// =========================================================================

// Petugas Distribusi (Pengiriman & Rute)
Route::prefix('mobile-petugas')->name('mobile.petugas.')->group(function () {
    Route::get('/splash', function () {
        return view('mobile_web_petugas.splash', ['role' => 'distribusi']);
    })->name('splash');

    Route::get('/login', [AuthController::class, 'showMobileLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'mobileLogin'])->name('login.post');
    Route::post('/logout', [AuthController::class, 'mobileLogout'])->name('logout');

    Route::middleware(['auth', 'role:petugas_distribusi'])->group(function () {
        Route::get('/pengiriman', [PetugasDistribusiController::class, 'index'])->name('pengiriman');
        Route::get('/detail/{id?}', [PetugasDistribusiController::class, 'detail'])->name('detail');
        Route::post('/complete/{id}', [PetugasDistribusiController::class, 'complete'])->name('complete');
        Route::get('/riwayat', [PetugasDistribusiController::class, 'riwayat'])->name('riwayat');
        Route::get('/akun', function () {
            return view('mobile_web_petugas.petugas_distribusi.akun');
        })->name('akun');
    });
});

// Petugas Pembibitan (Hatchery / Benih)
Route::prefix('petugas-pembibitan')->name('petugas.pembibitan.')->group(function () {
    Route::get('/splash', function () {
        return view('mobile_web_petugas.splash', ['role' => 'pembibitan']);
    })->name('splash');

    Route::get('/login', function () {
        return redirect()->route('mobile.petugas.login', ['role' => 'pembibitan']);
    })->name('login');

    Route::middleware(['auth', 'role:pembibitan'])->group(function () {
        Route::get('/', [PetugasPembibitanController::class, 'index'])->name('dashboard');
        Route::get('/form', [PetugasPembibitanController::class, 'form'])->name('form');
        Route::post('/form', [PetugasPembibitanController::class, 'storeBatch'])->name('store-batch');
        Route::get('/log-pakan', [PetugasPembibitanController::class, 'logPakan'])->name('log-pakan');
        Route::get('/akun', function () {
            return view('mobile_web_petugas.petugas_pembibitan.akun');
        })->name('akun');
    });
});

// Petugas Pembesaran (Kolam Pembesaran / Panen)
Route::prefix('petugas-pembesaran')->name('petugas.pembesaran.')->group(function () {
    Route::get('/splash', function () {
        return view('mobile_web_petugas.splash', ['role' => 'pembesaran']);
    })->name('splash');

    Route::get('/login', function () {
        return redirect()->route('mobile.petugas.login', ['role' => 'pembesaran']);
    })->name('login');

    Route::middleware(['auth', 'role:pembesaran'])->group(function () {
        Route::get('/', [PetugasPembesaranController::class, 'index'])->name('dashboard');
        Route::get('/create-batch', [PetugasPembesaranController::class, 'createBatch'])->name('create-batch');
        Route::post('/create-batch', [PetugasPembesaranController::class, 'storeBatch'])->name('store-batch');
        Route::get('/log-pakan', [PetugasPembesaranController::class, 'logPakan'])->name('log-pakan');
        Route::get('/akun', function () {
            return view('mobile_web_petugas.petugas_pembesaran.akun');
        })->name('akun');
    });
});
