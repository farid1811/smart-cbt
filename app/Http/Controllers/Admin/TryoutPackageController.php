<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TryoutPackage;
use App\Models\Question;
use App\Models\Category;
use Illuminate\Http\Request;

class TryoutPackageController extends Controller
{
    public function index(Request $request)
    {
        $query = TryoutPackage::query();
        if ($request->filled('type')) {
            $query->where('jenis_ujian', $request->type);
        }
        if ($request->filled('search')) {
            $query->where('nama', 'like', '%' . $request->search . '%');
        }
        $packages = $query->withCount('questions')->latest()->paginate(20)->withQueryString();
        return view('admin.tryouts.index', compact('packages'));
    }

    public function create()
    {
        $groups = \App\Models\Group::all();
        return view('admin.tryouts.create', compact('groups'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama'                 => 'required|string|max:255',
            'deskripsi'            => 'nullable|string',
            'jenis_ujian'          => 'required|in:tryout,drill',
            'group_id'             => 'required|exists:groups,id',
            'question_code_id'     => 'required|exists:question_codes,id',
            'category_id'          => 'required|exists:categories,id',
            'attempt_limit'        => 'required|integer|min:1',
            'durasi_menit'         => 'required|integer|min:10|max:300',
            'is_active'            => 'boolean',
            'mulai_at'             => 'nullable|date',
            'selesai_at'           => 'nullable|date|after_or_equal:mulai_at',
            'exam_mode'            => 'required|in:normal,seb',
            'seb_url'              => 'nullable|string|max:255',
            'seb_quit_password'    => 'nullable|string|max:255',
            'seb_browser_lockdown' => 'boolean',
            'token'                => 'nullable|string|max:255',
            'randomize_questions'  => 'boolean',
            'randomize_options'    => 'boolean',
        ]);

        $group = \App\Models\Group::findOrFail($validated['group_id']);
        $validated['group'] = $group->name;

        $categoryModel = \App\Models\Category::findOrFail($validated['category_id']);
        $validated['category'] = $categoryModel->name;

        $validated['is_active'] = $request->has('is_active');
        $validated['seb_browser_lockdown'] = $request->has('seb_browser_lockdown');
        $validated['randomize_questions'] = $request->has('randomize_questions');
        $validated['randomize_options'] = $request->has('randomize_options');

        TryoutPackage::create($validated);
        return redirect()->route('admin.tryouts.index', ['type' => $validated['jenis_ujian']])->with('success', 'Paket ujian berhasil dibuat.');
    }

    public function show(TryoutPackage $tryout)
    {
        $tryout->load('questions.category');
        $categories = Category::with('questions')->get();
        return view('admin.tryouts.show', compact('tryout', 'categories'));
    }

    public function edit(TryoutPackage $tryout)
    {
        $groups = \App\Models\Group::all();
        return view('admin.tryouts.edit', compact('tryout', 'groups'));
    }

    public function update(Request $request, TryoutPackage $tryout)
    {
        $validated = $request->validate([
            'nama'                 => 'required|string|max:255',
            'deskripsi'            => 'nullable|string',
            'jenis_ujian'          => 'required|in:tryout,drill',
            'group_id'             => 'required|exists:groups,id',
            'question_code_id'     => 'required|exists:question_codes,id',
            'category_id'          => 'required|exists:categories,id',
            'attempt_limit'        => 'required|integer|min:1',
            'durasi_menit'         => 'required|integer|min:10|max:300',
            'is_active'            => 'boolean',
            'mulai_at'             => 'nullable|date',
            'selesai_at'           => 'nullable|date|after_or_equal:mulai_at',
            'exam_mode'            => 'required|in:normal,seb',
            'seb_url'              => 'nullable|string|max:255',
            'seb_quit_password'    => 'nullable|string|max:255',
            'seb_browser_lockdown' => 'boolean',
            'token'                => 'nullable|string|max:255',
            'randomize_questions'  => 'boolean',
            'randomize_options'    => 'boolean',
        ]);

        $group = \App\Models\Group::findOrFail($validated['group_id']);
        $validated['group'] = $group->name;

        $categoryModel = \App\Models\Category::findOrFail($validated['category_id']);
        $validated['category'] = $categoryModel->name;

        $validated['is_active'] = $request->has('is_active');
        $validated['seb_browser_lockdown'] = $request->has('seb_browser_lockdown');
        $validated['randomize_questions'] = $request->has('randomize_questions');
        $validated['randomize_options'] = $request->has('randomize_options');

        $tryout->update($validated);
        return redirect()->route('admin.tryouts.index', ['type' => $validated['jenis_ujian']])->with('success', 'Paket ujian berhasil diperbarui.');
    }

    public function destroy(TryoutPackage $tryout)
    {
        $type = $tryout->jenis_ujian;
        $tryout->delete();
        return redirect()->route('admin.tryouts.index', ['type' => $type])->with('success', 'Paket tryout berhasil dihapus.');
    }

    // Tambah soal ke paket (AJAX)
    public function addQuestion(Request $request, TryoutPackage $tryout)
    {
        $request->validate(['question_id' => 'required|exists:questions,id']);

        $question = Question::findOrFail($request->question_id);
        if ($question->tryout_package_id === $tryout->id) {
            return response()->json(['message' => 'Soal sudah ada di paket ini.'], 422);
        }

        $maxUrutan = $tryout->questions()->max('urutan') ?? 0;
        $question->update([
            'tryout_package_id' => $tryout->id,
            'urutan' => $maxUrutan + 1
        ]);

        return response()->json(['message' => 'Soal berhasil ditambahkan.', 'total' => $tryout->questions()->count()]);
    }

    // Hapus soal dari paket (AJAX)
    public function removeQuestion(Request $request, TryoutPackage $tryout)
    {
        $request->validate(['question_id' => 'required|exists:questions,id']);
        $question = Question::findOrFail($request->question_id);
        $question->delete(); // Soal dihapus karena tidak ada bank soal global lagi
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
