<?php

namespace App\Http\Controllers\Peserta;

use App\Http\Controllers\Controller;
use App\Models\TryoutPackage;
use App\Models\LearningModule;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        $modules = LearningModule::where('group_id', $user->group_id)
            ->where('is_active', true)
            ->with(['group', 'questionCode', 'category'])
            ->latest()
            ->limit(3)
            ->get();

        $drillsQuery = TryoutPackage::where('group_id', $user->group_id)
            ->where('jenis_ujian', 'drill')
            ->where('is_active', true);

        $tryoutsQuery = TryoutPackage::where('group_id', $user->group_id)
            ->where('jenis_ujian', 'tryout')
            ->where('is_active', true);

        if ($user->assigned_package_id) {
            $drillsQuery->where('id', $user->assigned_package_id);
            $tryoutsQuery->where('id', $user->assigned_package_id);
        }

        $drills = $drillsQuery->withCount('questions')
            ->with([
                'packageAttempts' => fn($q) => $q->where('participant_id', $user->id),
                'categoryRelation',
            ])
            ->latest()
            ->limit(3)
            ->get();

        $tryouts = $tryoutsQuery->withCount('questions')
            ->with(['packageAttempts' => fn($q) => $q->where('participant_id', $user->id)])
            ->latest()
            ->limit(3)
            ->get();

        $riwayat = $user->results()
            ->with(['tryoutPackage', 'examSession'])
            ->latest()
            ->limit(5)
            ->get();

        return view('peserta.dashboard', compact('user', 'modules', 'drills', 'tryouts', 'riwayat'));
    }

    public function modules()
    {
        $user = Auth::user();

        $modules = LearningModule::where('group_id', $user->group_id)
            ->where('is_active', true)
            ->with(['group', 'questionCode', 'category'])
            ->latest()
            ->paginate(20);

        return view('peserta.modules.index', compact('modules'));
    }

    public function showModule(LearningModule $module)
    {
        $user = Auth::user();

        if (!$module->is_active || $module->group_id !== $user->group_id) {
            abort(403, 'Anda tidak memiliki akses ke modul ini.');
        }

        return view('peserta.modules.show', compact('module'));
    }

    public function drills()
    {
        $user = Auth::user();

        $drillsQuery = TryoutPackage::where('group_id', $user->group_id)
            ->where('jenis_ujian', 'drill')
            ->where('is_active', true);

        if ($user->assigned_package_id) {
            $drillsQuery->where('id', $user->assigned_package_id);
        }

        $drills = $drillsQuery->withCount('questions')
            ->with([
                'packageAttempts' => fn($q) => $q->where('participant_id', $user->id),
                'categoryRelation',
            ])
            ->latest()
            ->paginate(10);

        return view('peserta.drills.index', compact('drills'));
    }

    public function tryouts()
    {
        $user = Auth::user();

        $tryoutsQuery = TryoutPackage::where('group_id', $user->group_id)
            ->where('jenis_ujian', 'tryout')
            ->where('is_active', true);

        if ($user->assigned_package_id) {
            $tryoutsQuery->where('id', $user->assigned_package_id);
        }

        $tryouts = $tryoutsQuery->withCount('questions')
            ->with(['packageAttempts' => fn($q) => $q->where('participant_id', $user->id)])
            ->latest()
            ->paginate(10);

        return view('peserta.tryouts.index', compact('tryouts'));
    }

    public function streamPdf(LearningModule $module)
    {
        $user = Auth::user();

        if (!$module->is_active || $module->group_id !== $user->group_id) {
            abort(403, 'Anda tidak memiliki akses ke modul ini.');
        }

        if (!$module->pdf_file) {
            abort(404, 'File PDF tidak ditemukan.');
        }

        $storagePath = str_replace('storage/', 'public/', $module->pdf_file);

        if (!Storage::exists($storagePath)) {
            abort(404, 'File PDF tidak ditemukan di storage.');
        }

        return Storage::response($storagePath, null, [
            'Content-Disposition' => 'inline; filename="' . basename($module->pdf_file) . '"'
        ]);
    }
}
