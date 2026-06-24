<?php

namespace App\Http\Controllers\Peserta;

use App\Http\Controllers\Controller;
use App\Models\Result;
use Illuminate\Support\Facades\Auth;

class ResultController extends Controller
{
    public function index()
    {
        $results = Auth::user()->results()
            ->with(['tryoutPackage', 'examSession'])
            ->latest()
            ->paginate(25);

        return view('peserta.results.index', compact('results'));
    }
}
