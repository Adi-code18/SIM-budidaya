<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureSingleSession
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check()) {
            $user = Auth::user();
            $currentSessionId = session()->getId();
            $sessionInApp = session('user_session_id');

            // If user's DB last_session_id is not set, set it now
            if (!$user->last_session_id) {
                $user->update(['last_session_id' => $currentSessionId]);
                session(['user_session_id' => $currentSessionId]);
            } else {
                // Check if another device/browser logged in to the SAME ACCOUNT
                if ($user->last_session_id !== $currentSessionId && $user->last_session_id !== $sessionInApp) {
                    $userRole = $user->role;
                    
                    Auth::logout();
                    $request->session()->invalidate();
                    $request->session()->regenerateToken();

                    $errorMsg = 'Sesi Anda telah berakhir karena akun ini baru saja login dari perangkat/browser lain.';

                    if ($request->expectsJson() || $request->is('api/*')) {
                        return response()->json([
                            'status'  => 'error',
                            'message' => $errorMsg
                        ], 401);
                    }

                    if (in_array($userRole, ['petugas_distribusi', 'distribusi', 'pembesaran', 'pembibitan'])) {
                        return redirect()->route('mobile.petugas.login')->withErrors(['email' => $errorMsg]);
                    }

                    return redirect()->route('login')->withErrors(['username' => $errorMsg]);
                }
            }
        }

        return $next($request);
    }
}
