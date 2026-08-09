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
