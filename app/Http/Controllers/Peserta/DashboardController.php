<?php

namespace App\Http\Controllers\Peserta;

use App\Http\Controllers\Controller;
use App\Models\TryoutPackage;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        $tryoutsAktif = TryoutPackage::where('is_active', true)->latest()->get()
            ->filter(fn($t) => $t->isAvailable());

        $riwayat = $user->results()
            ->with(['tryoutPackage', 'examSession'])
            ->latest()
            ->limit(5)
            ->get();

        return view('peserta.dashboard', compact('user', 'tryoutsAktif', 'riwayat'));
    }
}
