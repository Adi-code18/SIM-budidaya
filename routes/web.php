<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('dashboard');
});

Route::get('/login', function () {
    return view('auth.login');
})->name('login');

Route::get('/dashboard', function () {
    return view('dashboard.index');
})->name('dashboard');

Route::get('/pembibitan', function () {
    return view('pembibitan.index');
})->name('pembibitan');

Route::get('/pembesaran', function () {
    return view('pembesaran.index');
})->name('pembesaran');

Route::get('/pembudidaya', function () {
    return view('pembudidaya.index');
})->name('pembudidaya');

Route::get('/log-pakan', function () {
    return view('pakan.index');
})->name('log-pakan');

Route::get('/distribusi', function () {
    return view('distribusi.index');
})->name('distribusi');

Route::get('/keuangan', function () {
    return view('keuangan.index');
})->name('keuangan');

Route::get('/mitra', function () {
    return view('mitra.index');
})->name('mitra');

Route::get('/petugas', function () {
    return view('petugas.index');
})->name('petugas');

Route::get('/petugas/create', function () {
    return view('petugas.create');
})->name('petugas.create');

Route::get('/petugas/{id}/edit', function () {
    return view('petugas.edit');
})->name('petugas.edit');

Route::get('/petugas/libur/approval', function () {
    return view('petugas.approval-libur');
})->name('petugas.libur.approval');

Route::get('/petugas/libur/ajukan', function () {
    return view('petugas.ajukan-libur');
})->name('petugas.libur.ajukan');

