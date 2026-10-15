<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

/** Akun nonaktif tidak boleh masuk panel; password sementara wajib diganti dulu. */
class PanelAccess
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user->is_active) {
            Auth::logout();
            $request->session()->invalidate();

            return redirect()->route('login')->withErrors(['email' => 'Akun ini dinonaktifkan.']);
        }

        if ($user->must_change_password && ! $request->routeIs('password.*', 'logout')) {
            return redirect()->route('password.edit');
        }

        return $next($request);
    }
}
