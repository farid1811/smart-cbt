<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class IsPeserta
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth()->check() || !auth()->user()->isPeserta()) {
            abort(403, 'Akses ditolak. Halaman ini hanya untuk Peserta.');
        }

        // Cek status aktif peserta
        if (!auth()->user()->is_active) {
            auth()->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            return redirect()->route('login')
                ->with('error', 'Akun Anda telah dinonaktifkan. Hubungi administrator.');
        }

        return $next($request);
    }
}
