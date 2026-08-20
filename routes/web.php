<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('dashboard');
});

Route::get('/login', function () {
    return view('layouts.auth.login');
})->name('login');

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

// Mobile Web Petugas Distribusi Routes
Route::prefix('mobile-petugas')->name('mobile.petugas.')->group(function () {
    Route::get('/splash', function () {
        return view('mobile_web_petugas_distribusi.splash');
    })->name('splash');

    Route::get('/login', function () {
        return view('mobile_web_petugas_distribusi.login');
    })->name('login');

    Route::get('/pengiriman', function () {
        return view('mobile_web_petugas_distribusi.index');
    })->name('pengiriman');

    Route::get('/detail/{id?}', function ($id = 'ORD-9924A') {
        return view('mobile_web_petugas_distribusi.detail', compact('id'));
    })->name('detail');

    Route::get('/riwayat', function () {
        return view('mobile_web_petugas_distribusi.riwayat');
    })->name('riwayat');

    Route::get('/akun', function () {
        return view('mobile_web_petugas_distribusi.akun');
    })->name('akun');
});


