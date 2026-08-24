<?php

use App\Http\Middleware\RoleMiddleware;
use App\Http\Middleware\SecurityHeadersMiddleware;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Session\Middleware\AuthenticateSession;
use Illuminate\Support\Facades\Log;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Tambahkan security headers dan single session listener ke web middleware
        $middleware->web(append: [
            SecurityHeadersMiddleware::class,
            AuthenticateSession::class,
        ]);

        // Daftarkan middleware alias untuk otorisasi peran
        $middleware->alias([
            'role' => RoleMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // Perlindungan Keamanan Siber: Mencegah kebocoran SQL Query / Database Error ke Client / JS
        $exceptions->render(function (QueryException $e, Request $request) {
            Log::error('Database Query Exception: ' . $e->getMessage());

            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Terjadi kesalahan pemrosesan basis data pada server. Silakan hubungi administrator.',
                ], 500);
            }

            if (!config('app.debug')) {
                return response()->view('errors.500', [
                    'message' => 'Terjadi kesalahan sistem pada server. Permintaan Anda tidak dapat diproses saat ini.'
                ], 500);
            }
        });

        $exceptions->render(function (\PDOException $e, Request $request) {
            Log::error('PDO Database Exception: ' . $e->getMessage());

            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Koneksi ke basis data mengalami gangguan. Silakan coba beberapa saat lagi.',
                ], 500);
            }
        });

        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*'),
        );
    })->create();
