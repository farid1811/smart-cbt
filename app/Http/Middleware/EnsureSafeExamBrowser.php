<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureSafeExamBrowser
{
    public function handle(Request $request, Closure $next): Response
    {
        // 1. Dapatkan model TryoutPackage atau ExamSession dari route
        $package = null;
        if ($request->route('package')) {
            $package = $request->route('package');
            if (is_numeric($package)) {
                $package = \App\Models\TryoutPackage::find($package);
            }
        } elseif ($request->route('session')) {
            $session = $request->route('session');
            if (is_numeric($session)) {
                $session = \App\Models\ExamSession::find($session);
            }
            if ($session) {
                $package = $session->tryoutPackage;
            }
        }

        // 2. Jika paket tidak ada atau exam_mode bukan 'seb', lanjutkan
        if (!$package || $package->exam_mode !== 'seb') {
            return $next($request);
        }

        // 3. Periksa header Safe Exam Browser
        $userAgent = $request->header('User-Agent') ?? '';
        $isSebUserAgent = str_contains($userAgent, 'SafeExamBrowser') || str_contains($userAgent, 'SEB');
        
        $hasRequestHash = $request->hasHeader('X-SafeExamBrowser-RequestHash') 
            || $request->server('HTTP_X_SAFEEXAMBROWSER_REQUESTHASH') !== null
            || $request->hasHeader('X-SafeExamBrowser-ConfigKeyHash')
            || $request->server('HTTP_X_SAFEEXAMBROWSER_CONFIGKEYHASH') !== null;

        if ($isSebUserAgent || $hasRequestHash) {
            return $next($request);
        }

        // 4. Jika bukan dari SEB, tampilkan view/halaman penjelasan
        return response()->view('peserta.exam.seb_required', ['package' => $package], 403);
    }
}
