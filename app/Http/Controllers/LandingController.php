<?php

namespace App\Http\Controllers;

use App\Models\Question;
use App\Models\User;
use App\Models\TryoutPackage;
use App\Models\HomepageSettings;
use App\Models\Alumni;
use Illuminate\Http\Request;

class LandingController extends Controller
{
    public function index()
    {
        // Dynamic stats from database
        $totalSoal    = Question::count();
        $totalPeserta = User::where('role', 'peserta')->count();
        $totalTryout  = TryoutPackage::count();

        // CMS settings (singleton — created with defaults if not exists)
        $settings = HomepageSettings::getInstance();

        // Alumni from database, ordered by urutan then tahun_lulus desc
        $alumniList = Alumni::orderBy('urutan')->orderBy('tahun_lulus', 'desc')->get();

        return view('landing', compact('totalSoal', 'totalPeserta', 'totalTryout', 'settings', 'alumniList'));
    }
}
