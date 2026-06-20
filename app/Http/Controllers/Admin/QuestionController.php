<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Question;
use App\Models\Category;
use Illuminate\Http\Request;

class QuestionController extends Controller
{
    public function index(Request $request)
    {
        $query = Question::with('category');

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }
        if ($request->filled('tingkat_kesulitan')) {
            $query->where('tingkat_kesulitan', $request->tingkat_kesulitan);
        }
        if ($request->filled('search')) {
            $query->where('soal', 'like', '%' . $request->search . '%');
        }

        $questions  = $query->latest()->paginate(15)->withQueryString();
        $categories = Category::all();

        return view('admin.questions.index', compact('questions', 'categories'));
    }

    public function create()
    {
        $categories = Category::all();
        return view('admin.questions.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'category_id'       => 'required|exists:categories,id',
            'soal'              => 'required|string',
            'image'             => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'opsi_a'            => 'required|string',
            'opsi_b'            => 'required|string',
            'opsi_c'            => 'required|string',
            'opsi_d'            => 'required|string',
            'opsi_e'            => 'nullable|string',
            'jawaban_benar'     => 'required|in:A,B,C,D,E',
            'pembahasan'        => 'nullable|string',
            'tingkat_kesulitan' => 'required|in:mudah,sedang,sulit',
        ]);

        $data = $validated;
        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('uploads/questions'), $filename);
            $data['image'] = 'uploads/questions/' . $filename;
        }

        Question::create($data);
        return redirect()->route('admin.questions.index')->with('success', 'Soal berhasil ditambahkan.');
    }

    public function edit(Question $question)
    {
        $categories = Category::all();
        return view('admin.questions.edit', compact('question', 'categories'));
    }

    public function update(Request $request, Question $question)
    {
        $validated = $request->validate([
            'category_id'       => 'required|exists:categories,id',
            'soal'              => 'required|string',
            'image'             => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'opsi_a'            => 'required|string',
            'opsi_b'            => 'required|string',
            'opsi_c'            => 'required|string',
            'opsi_d'            => 'required|string',
            'opsi_e'            => 'nullable|string',
            'jawaban_benar'     => 'required|in:A,B,C,D,E',
            'pembahasan'        => 'nullable|string',
            'tingkat_kesulitan' => 'required|in:mudah,sedang,sulit',
        ]);

        $data = $validated;
        
        // Hapus gambar lama jika dicentang atau diupload gambar baru
        if ($request->boolean('hapus_image') || $request->hasFile('image')) {
            if ($question->image && file_exists(public_path($question->image))) {
                @unlink(public_path($question->image));
            }
            $data['image'] = null;
        }

        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('uploads/questions'), $filename);
            $data['image'] = 'uploads/questions/' . $filename;
        } else {
            // Jika tidak ada upload baru dan tidak dicentang hapus, pertahankan gambar lama
            if (!$request->boolean('hapus_image')) {
                unset($data['image']);
            }
        }

        $question->update($data);
        return redirect()->route('admin.questions.index')->with('success', 'Soal berhasil diperbarui.');
    }

    public function importProcess(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:pdf',
        ]);

        $file = $request->file('file');

        try {
            $parser = new \Smalot\PdfParser\Parser();
            $pdf = $parser->parseFile($file->getRealPath());
            $text = $pdf->getText();
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal memproses file PDF: ' . $e->getMessage());
        }

        $lines = preg_split('/\r\n|\r|\n/', $text);
        $questionsToInsert = [];
        $currentQuestion = null;
        $currentOpsiIndex = -1;
        $currentSection = null;

        foreach ($lines as $line) {
            $trimmedLine = trim($line);
            if ($trimmedLine === '') {
                continue;
            }

            if (preg_match('/^SOAL\b\s*(.*)/i', $trimmedLine, $matches)) {
                if ($currentQuestion !== null) {
                    $questionsToInsert[] = $currentQuestion;
                }

                $currentQuestion = [
                    'soal' => trim($matches[1]),
                    'opsi_a' => '',
                    'opsi_b' => '',
                    'opsi_c' => '',
                    'opsi_d' => '',
                    'opsi_e' => '',
                    'tingkat_kesulitan' => 'sedang',
                    'question_number' => null,
                ];
                $currentOpsiIndex = -1;
                $currentSection = 'SOAL';
                continue;
            }

            if (preg_match('/^(?:(\d+)\s+)?JAWABAN\b\s*(.*)/i', $trimmedLine, $matches)) {
                $qNumber = $matches[1] !== '' ? $matches[1] : null;
                $restOfLine = trim($matches[2]);

                if ($currentQuestion !== null) {
                    if ($qNumber !== null) {
                        $currentQuestion['question_number'] = (int) $qNumber;
                    }

                    $currentOpsiIndex++;
                    $currentSection = 'JAWABAN';

                    switch ($currentOpsiIndex) {
                        case 0: $currentQuestion['opsi_a'] = $restOfLine; break;
                        case 1: $currentQuestion['opsi_b'] = $restOfLine; break;
                        case 2: $currentQuestion['opsi_c'] = $restOfLine; break;
                        case 3: $currentQuestion['opsi_d'] = $restOfLine; break;
                        case 4: $currentQuestion['opsi_e'] = $restOfLine; break;
                    }
                }
                continue;
            }

            if ($currentQuestion !== null) {
                if ($currentSection === 'SOAL') {
                    if ($currentQuestion['soal'] === '') {
                        $currentQuestion['soal'] = $trimmedLine;
                    } else {
                        $currentQuestion['soal'] .= "\n" . $trimmedLine;
                    }
                } elseif ($currentSection === 'JAWABAN' && $currentOpsiIndex >= 0 && $currentOpsiIndex <= 4) {
                    $field = '';
                    switch ($currentOpsiIndex) {
                        case 0: $field = 'opsi_a'; break;
                        case 1: $field = 'opsi_b'; break;
                        case 2: $field = 'opsi_c'; break;
                        case 3: $field = 'opsi_d'; break;
                        case 4: $field = 'opsi_e'; break;
                    }
                    if ($currentQuestion[$field] === '') {
                        $currentQuestion[$field] = $trimmedLine;
                    } else {
                        $currentQuestion[$field] .= "\n" . $trimmedLine;
                    }
                }
            }
        }

        if ($currentQuestion !== null) {
            $questionsToInsert[] = $currentQuestion;
        }

        if (empty($questionsToInsert)) {
            return back()->with('error', 'Tidak ada soal valid yang berhasil diproses dari file PDF.');
        }

        $categories = Category::all()->pluck('id', 'kode')->toArray();
        $importedCount = 0;

        foreach ($questionsToInsert as $qData) {
            $scores = [];
            $options = ['a', 'b', 'c', 'd', 'e'];
            foreach ($options as $opt) {
                $field = 'opsi_' . $opt;
                $textOpt = trim($qData[$field] ?? '');

                if ($textOpt === '') {
                    $scores[$opt] = 0;
                    continue;
                }

                if (preg_match('/^(.*?)\s+(\d+)$/s', $textOpt, $matches)) {
                    $qData[$field] = trim($matches[1]);
                    $scores[$opt] = (int) $matches[2];
                } else {
                    $scores[$opt] = 0;
                }
            }

            $maxScore = -1;
            $bestOpt = 'A';
            foreach ($scores as $opt => $score) {
                if ($score > $maxScore) {
                    $maxScore = $score;
                    $bestOpt = strtoupper($opt);
                }
            }

            $soalText = trim($qData['soal']);
            if ($soalText === '') {
                $soalText = "(Soal Gambar / Formula)";
            }

            if ($soalText === '' || $qData['opsi_a'] === '' || $qData['opsi_b'] === '' || $qData['opsi_c'] === '' || $qData['opsi_d'] === '') {
                continue;
            }

            $num = $qData['question_number'];
            if ($num === null) {
                $num = $importedCount + 1;
            }

            if ($num >= 1 && $num <= 30) {
                $kategoriKode = 'TWK';
            } elseif ($num >= 31 && $num <= 65) {
                $kategoriKode = 'TIU';
            } else {
                $kategoriKode = 'TKP';
            }

            $catId = $categories[$kategoriKode] ?? null;
            if (!$catId) {
                $catId = reset($categories);
            }

            Question::create([
                'category_id'       => $catId,
                'soal'              => $soalText,
                'opsi_a'            => trim($qData['opsi_a']),
                'opsi_b'            => trim($qData['opsi_b']),
                'opsi_c'            => trim($qData['opsi_c']),
                'opsi_d'            => trim($qData['opsi_d']),
                'opsi_e'            => $qData['opsi_e'] !== '' ? trim($qData['opsi_e']) : null,
                'jawaban_benar'     => $bestOpt,
                'pembahasan'        => null,
                'tingkat_kesulitan' => $qData['tingkat_kesulitan'] ?? 'sedang',
            ]);
            $importedCount++;
        }

        return redirect()->route('admin.questions.index')->with('success', "$importedCount soal berhasil diimport.");
    }

    public function destroy(Question $question)
    {
        if ($question->image && file_exists(public_path($question->image))) {
            @unlink(public_path($question->image));
        }
        $question->delete();
        return redirect()->route('admin.questions.index')->with('success', 'Soal berhasil dihapus.');
    }
}
