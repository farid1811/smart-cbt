<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TryoutPackage;
use App\Models\Question;
use App\Models\Category;
use Illuminate\Http\Request;

class TryoutPackageController extends Controller
{
    public function index()
    {
        $packages = TryoutPackage::withCount('questions')->latest()->paginate(10);
        return view('admin.tryouts.index', compact('packages'));
    }

    public function create()
    {
        return view('admin.tryouts.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama'                 => 'required|string|max:255',
            'deskripsi'            => 'nullable|string',
            'jenis_ujian'          => 'required|in:tryout,drill',
            'durasi_menit'         => 'required|integer|min:10|max:300',
            'is_active'            => 'boolean',
            'mulai_at'             => 'nullable|date',
            'selesai_at'           => 'nullable|date|after_or_equal:mulai_at',
            'exam_mode'            => 'required|in:normal,seb',
            'seb_url'              => 'nullable|string|max:255',
            'seb_quit_password'    => 'nullable|string|max:255',
            'seb_browser_lockdown' => 'boolean',
        ]);

        $validated['is_active'] = $request->has('is_active');
        $validated['seb_browser_lockdown'] = $request->has('seb_browser_lockdown');
        TryoutPackage::create($validated);
        return redirect()->route('admin.tryouts.index')->with('success', 'Paket ujian berhasil dibuat.');
    }

    public function show(TryoutPackage $tryout)
    {
        $tryout->load('questions.category');
        $categories = Category::with('questions')->get();
        return view('admin.tryouts.show', compact('tryout', 'categories'));
    }

    public function edit(TryoutPackage $tryout)
    {
        return view('admin.tryouts.edit', compact('tryout'));
    }

    public function update(Request $request, TryoutPackage $tryout)
    {
        $validated = $request->validate([
            'nama'                 => 'required|string|max:255',
            'deskripsi'            => 'nullable|string',
            'jenis_ujian'          => 'required|in:tryout,drill',
            'durasi_menit'         => 'required|integer|min:10|max:300',
            'is_active'            => 'boolean',
            'mulai_at'             => 'nullable|date',
            'selesai_at'           => 'nullable|date|after_or_equal:mulai_at',
            'exam_mode'            => 'required|in:normal,seb',
            'seb_url'              => 'nullable|string|max:255',
            'seb_quit_password'    => 'nullable|string|max:255',
            'seb_browser_lockdown' => 'boolean',
        ]);

        $validated['is_active'] = $request->has('is_active');
        $validated['seb_browser_lockdown'] = $request->has('seb_browser_lockdown');
        $tryout->update($validated);
        return redirect()->route('admin.tryouts.index')->with('success', 'Paket ujian berhasil diperbarui.');
    }

    public function destroy(TryoutPackage $tryout)
    {
        $tryout->delete();
        return redirect()->route('admin.tryouts.index')->with('success', 'Paket tryout berhasil dihapus.');
    }

    // Tambah soal ke paket (AJAX)
    public function addQuestion(Request $request, TryoutPackage $tryout)
    {
        $request->validate(['question_id' => 'required|exists:questions,id']);

        if ($tryout->questions()->where('question_id', $request->question_id)->exists()) {
            return response()->json(['message' => 'Soal sudah ada di paket ini.'], 422);
        }

        $maxUrutan = $tryout->questions()->max('tryout_package_questions.urutan') ?? 0;
        $tryout->questions()->attach($request->question_id, ['urutan' => $maxUrutan + 1]);

        return response()->json(['message' => 'Soal berhasil ditambahkan.', 'total' => $tryout->questions()->count()]);
    }

    // Hapus soal dari paket (AJAX)
    public function removeQuestion(Request $request, TryoutPackage $tryout)
    {
        $request->validate(['question_id' => 'required|exists:questions,id']);
        $tryout->questions()->detach($request->question_id);
        return response()->json(['message' => 'Soal berhasil dihapus.', 'total' => $tryout->questions()->count()]);
    }

    public function downloadSebConfig(TryoutPackage $tryout)
    {
        $startUrl = $tryout->seb_url ?: route('peserta.exam.start', $tryout);
        $hashedQuitPassword = '';
        if ($tryout->seb_quit_password) {
            $hashedQuitPassword = hash('sha256', $tryout->seb_quit_password);
        }

        $allowReload = !$tryout->seb_browser_lockdown;
        $showUrl = !$tryout->seb_browser_lockdown;

        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $xml .= '<!DOCTYPE plist PUBLIC "-//Apple//DTD PLIST 1.0//EN" "http://www.apple.com/DTDs/PropertyList-1.0.dtd">' . "\n";
        $xml .= '<plist version="1.0">' . "\n";
        $xml .= '<dict>' . "\n";
        $xml .= '    <key>startURL</key>' . "\n";
        $xml .= '    <string>' . htmlspecialchars($startUrl) . '</string>' . "\n";
        $xml .= '    <key>allowQuit</key>' . "\n";
        $xml .= '    <true/>' . "\n";
        $xml .= '    <key>quitURLConfirm</key>' . "\n";
        $xml .= '    <true/>' . "\n";
        if ($hashedQuitPassword) {
            $xml .= '    <key>hashedQuitPassword</key>' . "\n";
            $xml .= '    <string>' . strtoupper($hashedQuitPassword) . '</string>' . "\n";
        }
        $xml .= '    <key>browserWindowShowURL</key>' . "\n";
        $xml .= $showUrl ? '    <true/>' . "\n" : '    <false/>' . "\n";
        $xml .= '    <key>browserWindowShowReload</key>' . "\n";
        $xml .= $allowReload ? '    <true/>' . "\n" : '    <false/>' . "\n";
        $xml .= '    <key>allowPreferences</key>' . "\n";
        $xml .= '    <false/>' . "\n";
        $xml .= '    <key>sendBrowserExamKey</key>' . "\n";
        $xml .= '    <true/>' . "\n";
        $xml .= '</dict>' . "\n";
        $xml .= '</plist>';

        $fileName = str_replace(' ', '_', strtolower($tryout->nama)) . '.seb';

        return response($xml, 200, [
            'Content-Type' => 'application/x-safeexambrowser-config',
            'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
        ]);
    }
}
