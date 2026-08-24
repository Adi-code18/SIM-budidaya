<?php

use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

// Redirect root to dashboard or login
Route::get('/', function () {
    return redirect()->route('dashboard');
});

// Authentication Web Manajer Routes
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Web Manajer Terproteksi (Wajib Login & Peran 'manajer')
Route::middleware(['auth', 'role:manajer'])->group(function () {
    Route::get('/dashboard', function () {
        return view('layouts.dashboard.index');
    })->name('dashboard');

    Route::get('/pembibitan', function () {
        return view('layouts.pembibitan.index');
    })->name('pembibitan');

    Route::get('/pembesaran', function () {
        return view('layouts.pembesaran.index');
    })->name('pembesaran');

    Route::get('/pembudidaya', function () {
        return view('layouts.pembudidaya.index');
    })->name('pembudidaya');

    Route::get('/log-pakan', function () {
        return view('layouts.pakan.index');
    })->name('log-pakan');

    Route::get('/distribusi', function () {
        return view('layouts.distribusi.index');
    })->name('distribusi');

    Route::get('/keuangan', function () {
        return view('layouts.keuangan.index');
    })->name('keuangan');

    Route::get('/mitra', function () {
        return view('layouts.mitra.index');
    })->name('mitra');

    Route::get('/petugas', function () {
        return view('layouts.petugas.index');
    })->name('petugas');

    Route::get('/petugas/create', function () {
        return view('layouts.petugas.create');
    })->name('petugas.create');

    Route::get('/petugas/{id}/edit', function () {
        return view('layouts.petugas.edit');
    })->name('petugas.edit');

    Route::get('/petugas/libur/approval', function () {
        return view('layouts.petugas.approval-libur');
    })->name('petugas.libur.approval');

    Route::get('/petugas/libur/ajukan', function () {
        return view('layouts.petugas.ajukan-libur');
    })->name('petugas.libur.ajukan');
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
        Route::get('/pengiriman', function () {
            return view('mobile_web_petugas.petugas_distribusi.index');
        })->name('pengiriman');

        Route::get('/detail/{id?}', function ($id = 'ORD-9924A') {
            return view('mobile_web_petugas.petugas_distribusi.detail', compact('id'));
        })->name('detail');

        Route::get('/riwayat', function () {
            return view('mobile_web_petugas.petugas_distribusi.riwayat');
        })->name('riwayat');

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
        Route::get('/', function () {
            return view('mobile_web_petugas.petugas_pembibitan.index');
        })->name('dashboard');

        Route::get('/form', function () {
            return view('mobile_web_petugas.petugas_pembibitan.log_pembibitan');
        })->name('form');

        Route::get('/log-pakan', function () {
            return view('mobile_web_petugas.petugas_pembibitan.log_pakan');
        })->name('log-pakan');

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
        Route::get('/', function () {
            return view('mobile_web_petugas.petugas_pembesaran.index');
        })->name('dashboard');

        Route::get('/create-batch', function () {
            return view('mobile_web_petugas.petugas_pembesaran.create_batch');
        })->name('create-batch');

        Route::get('/log-pakan', function () {
            return view('mobile_web_petugas.petugas_pembesaran.log_pakan');
        })->name('log-pakan');

        Route::get('/akun', function () {
            return view('mobile_web_petugas.petugas_pembesaran.akun');
        })->name('akun');
    });
});
