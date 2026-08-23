<?php

use App\Http\Controllers\Api\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Endpoint Publik (Tanpa Token)
Route::post('/login', [AuthController::class, 'login']);

// Endpoint Terproteksi (Wajib Bawa Bearer Token Sanctum)
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);

    // Cek data user yang sedang login
    Route::get('/user', function (Request $request) {
        return response()->json([
            'status' => 'success',
            'data' => $request->user()
        ]);
    });
});