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