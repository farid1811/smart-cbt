<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Question;
use App\Models\TryoutPackage;
use App\Models\User;
use App\Models\ExamSession;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'total_soal'      => Question::count(),
            'total_kategori'  => Category::count(),
            'total_paket'     => TryoutPackage::count(),
            'total_peserta'   => User::where('role', 'peserta')->count(),
            'peserta_aktif'   => User::where('role', 'peserta')->where('is_active', true)->count(),
            'total_ujian'     => ExamSession::count(),
            'ujian_selesai'   => ExamSession::where('status', 'selesai')->count(),
        ];

        $recentSessions = ExamSession::with(['user', 'tryoutPackage', 'result'])
            ->latest()
            ->limit(10)
            ->get();

        return view('admin.dashboard', compact('stats', 'recentSessions'));
    }
}
