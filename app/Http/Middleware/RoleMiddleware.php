<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  ...$roles
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        if (!Auth::check()) {
            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Unauthenticated.'
                ], 401);
            }

            // Redirect ke portal login yang sesuai
            if ($request->is('mobile-petugas*') || $request->is('petugas-pembibitan*') || $request->is('petugas-pembesaran*')) {
                return redirect()->route('mobile.petugas.login');
            }

            return redirect()->route('login');
        }

        $user = Auth::user();

        // Normalisasi peran (misal: 'petugas_distribusi' atau 'distribusi')
        $userRole = $user->role;
        $matched = false;

        foreach ($roles as $role) {
            if ($userRole === $role) {
                $matched = true;
                break;
            }

            // Alias distribusi
            if (($role === 'distribusi' || $role === 'petugas_distribusi') && 
                ($userRole === 'distribusi' || $userRole === 'petugas_distribusi')) {
                $matched = true;
                break;
            }
        }

        if (!$matched) {
            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Anda tidak memiliki hak akses untuk resource ini.'
                ], 403);
            }

            // Dialihkan secara otomatis ke dashboard yang sesuai dengan peran user saat ini
            if ($userRole === 'manajer') {
                return redirect()->route('dashboard')->with('status', 'Dialihkan ke Dashboard Manajer (Peran akun Anda: Manajer).');
            } elseif ($userRole === 'pembibitan') {
                return redirect()->route('petugas.pembibitan.dashboard')->with('status', 'Dialihkan ke Dashboard Petugas Pembibitan (Peran akun Anda: Pembibitan).');
            } elseif ($userRole === 'pembesaran') {
                return redirect()->route('petugas.pembesaran.dashboard')->with('status', 'Dialihkan ke Dashboard Petugas Pembesaran (Peran akun Anda: Pembesaran).');
            } elseif ($userRole === 'petugas_distribusi' || $userRole === 'distribusi') {
                return redirect()->route('mobile.petugas.pengiriman')->with('status', 'Dialihkan ke Halaman Petugas Distribusi (Peran akun Anda: Distribusi).');
            }

            Auth::logout();
            $request->session()->invalidate();
            return redirect()->route('login')->withErrors(['username' => 'Akses ditolak. Silakan login kembali dengan akun yang sesuai.']);
        }

        return $next($request);
    }
}
