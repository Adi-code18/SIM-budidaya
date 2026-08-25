<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\BatchPembesaranController;
use App\Http\Controllers\Api\BatchPembibitanController;
use App\Http\Controllers\Api\KeuanganController;
use App\Http\Controllers\Api\KolamController;
use App\Http\Controllers\Api\ManajemenPakanController;
use App\Http\Controllers\Api\MitraDistributorController;
use App\Http\Controllers\Api\PengajuanLiburController;
use App\Http\Controllers\Api\TransaksiDistribusiController;
use App\Http\Controllers\Api\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// ==========================================
// 1. Endpoint Publik (Tanpa Token)
// ==========================================
Route::post('/login', [AuthController::class, 'login']);

// ==========================================
// 2. Endpoint Terproteksi (Wajib Bearer Token Sanctum)
// ==========================================
Route::middleware('auth:sanctum')->group(function () {
    // Auth & Profil
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', function (Request $request) {
        return response()->json([
            'status' => 'success',
            'data' => $request->user()
        ]);
    });
    Route::put('/user/profile', [UserController::class, 'updateProfile']);

    // Manajemen Pengguna / Users
    Route::apiResource('users', UserController::class);

    // 1. Kolam (KolamSeeder)
    Route::apiResource('kolam', KolamController::class);

    // 2. Pengajuan Libur (PengajuanLiburSeeder)
    Route::apiResource('pengajuan-libur', PengajuanLiburController::class);

    // 3. Batch Pembibitan (BatchPembibitanSeeder)
    Route::apiResource('batch-pembibitan', BatchPembibitanController::class);

    // 4. Batch Pembesaran (BatchPembesaranSeeder)
    Route::apiResource('batch-pembesaran', BatchPembesaranController::class);

    // 5. Manajemen Pakan (ManajemenPakanSeeder)
    Route::apiResource('manajemen-pakan', ManajemenPakanController::class);

    // 6. Mitra Distributor (MitraDistributorSeeder)
    Route::apiResource('mitra-distributor', MitraDistributorController::class);

    // 7. Transaksi Distribusi (TransaksiDistribusiSeeder)
    Route::apiResource('transaksi-distribusi', TransaksiDistribusiController::class);

    // 8. Keuangan (KeuanganSeeder)
    Route::get('keuangan/summary', [KeuanganController::class, 'summary']);
    Route::apiResource('keuangan', KeuanganController::class);
});