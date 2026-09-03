<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Endpoint API untuk integrasi tambahan jika diperlukan di masa depan.
| Seluruh fungsionalitas utama Web & PWA menggunakan routes/web.php.
|
*/

Route::get('/', function () {
    return response()->json([
        'app' => config('app.name', 'SIM-BUDIDAYA'),
        'version' => '1.0.0',
        'status' => 'online'
    ]);
});

// RESTful API Endpoint untuk Master Jenis Ikan
Route::apiResource('ikan', \App\Http\Controllers\Api\IkanController::class);
Route::apiResource('pembibitan', \App\Http\Controllers\Api\BatchPembibitanController::class);
Route::apiResource('pembesaran', \App\Http\Controllers\Api\BatchPembesaranController::class);