<?php

namespace App\Http\Controllers;

use App\Models\Question;
use App\Models\User;
use App\Models\TryoutPackage;
use Illuminate\Http\Request;

class LandingController extends Controller
{
    public function index()
    {
        // Fetch dynamic stats from database
        $totalSoal = Question::count();
        $totalPeserta = User::where('role', 'peserta')->count();
        $totalTryout = TryoutPackage::count();
        
        // Custom conversion rate/passing rate
        $tingkatKelulusan = '98.6%';

        return view('landing', compact('totalSoal', 'totalPeserta', 'totalTryout', 'tingkatKelulusan'));
    }
}
