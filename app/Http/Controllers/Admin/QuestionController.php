<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Question;
use App\Models\Group;
use App\Models\QuestionCode;
use App\Models\Category;
use App\Models\SubCategory;
use App\Models\TryoutPackage;
use Illuminate\Http\Request;
use PhpOffice\PhpWord\IOFactory;

class QuestionController extends Controller
{
    public function index(Request $request)
    {
        $query = Question::with(['group', 'questionCode', 'category', 'subCategory', 'tryoutPackage']);

        if ($request->filled('tryout_package_id')) {
            $query->where('tryout_package_id', $request->tryout_package_id);
        }
        if ($request->filled('group_id')) {
            $query->where('group_id', $request->group_id);
        }
        if ($request->filled('question_code_id')) {
            $query->where('question_code_id', $request->question_code_id);
        }
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }
        if ($request->filled('sub_category_id')) {
            $query->where('sub_category_id', $request->sub_category_id);
        }
        if ($request->filled('tingkat_kesulitan')) {
            $query->where('tingkat_kesulitan', $request->tingkat_kesulitan);
        }
        if ($request->filled('package_type')) {
            $type = $request->package_type;
            $query->whereHas('tryoutPackage', function($pq) use ($type) {
                $pq->where('jenis_ujian', $type);
            });
        }
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('soal', 'like', '%' . $search . '%')
                  ->orWhereHas('category', function($cq) use ($search) {
                      $cq->where('name', 'like', '%' . $search . '%');
                  })
                  ->orWhereHas('subCategory', function($scq) use ($search) {
                      $scq->where('name', 'like', '%' . $search . '%');
                  })
                  ->orWhereHas('questionCode', function($qcq) use ($search) {
                      $qcq->where('name', 'like', '%' . $search . '%')
                          ->orWhere('code', 'like', '%' . $search . '%');
                  });
            });
        }

        $questions = $query->latest()->paginate(50)->withQueryString();
        
        $groups = Group::all();
        $packages = TryoutPackage::all();
        $categories = Category::all();
        $subCategories = SubCategory::all();

        return view('admin.questions.index', compact('questions', 'groups', 'packages', 'categories', 'subCategories'));
    }

    public function create(Request $request)
    {
        $groups = Group::all();
        $packages = TryoutPackage::all();
        $defaultPackageId = $request->tryout_package_id;
        return view('admin.questions.create', compact('groups', 'packages', 'defaultPackageId'));
    }

    private function handleImageUpload(Request $request, $fieldName, $currentPath = null, $deleteRequested = false)
    {
        if ($deleteRequested || $request->hasFile($fieldName)) {
            if ($currentPath && file_exists(public_path($currentPath))) {
                @unlink(public_path($currentPath));
            }
            $currentPath = null;
        }

        if ($request->hasFile($fieldName)) {
            $file = $request->file($fieldName);
            $filename = time() . '_' . uniqid() . '_' . $file->getClientOriginalName();
            $destDir = public_path('storage/questions');
            if (!file_exists($destDir)) {
                mkdir($destDir, 0777, true);
            }
            $file->move($destDir, $filename);
            $currentPath = 'storage/questions/' . $filename;
        }

        return $currentPath;
    }

    public function store(Request $request)
    {
        $rules = [
            'tryout_package_id' => 'required|exists:tryout_packages,id',
            'group_id'          => 'required|exists:groups,id',
            'question_code_id'  => 'required|exists:question_codes,id',
            'category_id'       => 'required|exists:categories,id',
            'sub_category_id'   => 'required|exists:sub_categories,id',
            'question_image'    => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048',
            'option_a_image'    => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048',
            'option_b_image'    => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048',
            'option_c_image'    => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048',
            'option_d_image'    => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048',
            'opsi_e'            => 'nullable|string',
            'option_e_image'    => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048',
            'score_a'           => 'nullable|integer|min:0|max:10',
            'score_b'           => 'nullable|integer|min:0|max:10',
            'score_c'           => 'nullable|integer|min:0|max:10',
            'score_d'           => 'nullable|integer|min:0|max:10',
            'score_e'           => 'nullable|integer|min:0|max:10',
            'jawaban_benar'     => 'required|in:A,B,C,D,E',
            'pembahasan'        => 'nullable|string',
            'explanation_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048',
            'tingkat_kesulitan' => 'required|in:mudah,sedang,sulit',
        ];

        // Dynamically set rules based on uploaded files
        $rules['soal'] = $request->hasFile('question_image') ? 'nullable|string' : 'required|string';
        $rules['opsi_a'] = $request->hasFile('option_a_image') ? 'nullable|string' : 'required|string';
        $rules['opsi_b'] = $request->hasFile('option_b_image') ? 'nullable|string' : 'required|string';
        $rules['opsi_c'] = $request->hasFile('option_c_image') ? 'nullable|string' : 'required|string';
        $rules['opsi_d'] = $request->hasFile('option_d_image') ? 'nullable|string' : 'required|string';

        $validated = $request->validate($rules);

        $data = $validated;
        
        $data['soal'] = $data['soal'] ?? '';
        $data['opsi_a'] = $data['opsi_a'] ?? '';
        $data['opsi_b'] = $data['opsi_b'] ?? '';
        $data['opsi_c'] = $data['opsi_c'] ?? '';
        $data['opsi_d'] = $data['opsi_d'] ?? '';
        
        $data['score_a'] = (int)($request->input('score_a') ?? 0);
        $data['score_b'] = (int)($request->input('score_b') ?? 0);
        $data['score_c'] = (int)($request->input('score_c') ?? 0);
        $data['score_d'] = (int)($request->input('score_d') ?? 0);
        $data['score_e'] = (int)($request->input('score_e') ?? 0);
        
        $maxUrutan = Question::where('tryout_package_id', $request->tryout_package_id)->max('urutan') ?? 0;
        $data['urutan'] = $maxUrutan + 1;

        $data['question_image'] = $this->handleImageUpload($request, 'question_image');
        $data['image'] = $data['question_image'];
        $data['option_a_image'] = $this->handleImageUpload($request, 'option_a_image');
        $data['option_b_image'] = $this->handleImageUpload($request, 'option_b_image');
        $data['option_c_image'] = $this->handleImageUpload($request, 'option_c_image');
        $data['option_d_image'] = $this->handleImageUpload($request, 'option_d_image');
        $data['option_e_image'] = $this->handleImageUpload($request, 'option_e_image');
        $data['explanation_image'] = $this->handleImageUpload($request, 'explanation_image');

        Question::create($data);
        return redirect()->route('admin.questions.index', ['tryout_package_id' => $request->tryout_package_id])->with('success', 'Soal berhasil ditambahkan.');
    }

    public function edit(Question $question)
    {
        $groups = Group::all();
        $codes = QuestionCode::where('group_id', $question->group_id)->get();
        $categories = Category::where('question_code_id', $question->question_code_id)->get();
        $subCategories = SubCategory::where('category_id', $question->category_id)->get();
        $packages = TryoutPackage::all();
        return view('admin.questions.edit', compact('question', 'groups', 'codes', 'categories', 'subCategories', 'packages'));
    }

    public function update(Request $request, Question $question)
    {
        $rules = [
            'tryout_package_id' => 'required|exists:tryout_packages,id',
            'group_id'          => 'required|exists:groups,id',
            'question_code_id'  => 'required|exists:question_codes,id',
            'category_id'       => 'required|exists:categories,id',
            'sub_category_id'   => 'required|exists:sub_categories,id',
            'question_image'    => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048',
            'option_a_image'    => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048',
            'option_b_image'    => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048',
            'option_c_image'    => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048',
            'option_d_image'    => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048',
            'opsi_e'            => 'nullable|string',
            'option_e_image'    => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048',
            'score_a'           => 'nullable|integer|min:0|max:10',
            'score_b'           => 'nullable|integer|min:0|max:10',
            'score_c'           => 'nullable|integer|min:0|max:10',
            'score_d'           => 'nullable|integer|min:0|max:10',
            'score_e'           => 'nullable|integer|min:0|max:10',
            'jawaban_benar'     => 'required|in:A,B,C,D,E',
            'pembahasan'        => 'nullable|string',
            'explanation_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048',
            'tingkat_kesulitan' => 'required|in:mudah,sedang,sulit',
        ];

        // For soal
        $hasQuestionImage = $request->hasFile('question_image') || ($question->question_image && !$request->boolean('hapus_question_image'));
        $rules['soal'] = $hasQuestionImage ? 'nullable|string' : 'required|string';

        // For opsi_a
        $hasOpsiAImage = $request->hasFile('option_a_image') || ($question->option_a_image && !$request->boolean('hapus_option_a_image'));
        $rules['opsi_a'] = $hasOpsiAImage ? 'nullable|string' : 'required|string';

        // For opsi_b
        $hasOpsiBImage = $request->hasFile('option_b_image') || ($question->option_b_image && !$request->boolean('hapus_option_b_image'));
        $rules['opsi_b'] = $hasOpsiBImage ? 'nullable|string' : 'required|string';

        // For opsi_c
        $hasOpsiCImage = $request->hasFile('option_c_image') || ($question->option_c_image && !$request->boolean('hapus_option_c_image'));
        $rules['opsi_c'] = $hasOpsiCImage ? 'nullable|string' : 'required|string';

        // For opsi_d
        $hasOpsiDImage = $request->hasFile('option_d_image') || ($question->option_d_image && !$request->boolean('hapus_option_d_image'));
        $rules['opsi_d'] = $hasOpsiDImage ? 'nullable|string' : 'required|string';

        $validated = $request->validate($rules);

        $data = $validated;

        $data['soal'] = $data['soal'] ?? '';
        $data['opsi_a'] = $data['opsi_a'] ?? '';
        $data['opsi_b'] = $data['opsi_b'] ?? '';
        $data['opsi_c'] = $data['opsi_c'] ?? '';
        $data['opsi_d'] = $data['opsi_d'] ?? '';

        $data['score_a'] = (int)($request->input('score_a') ?? 0);
        $data['score_b'] = (int)($request->input('score_b') ?? 0);
        $data['score_c'] = (int)($request->input('score_c') ?? 0);
        $data['score_d'] = (int)($request->input('score_d') ?? 0);
        $data['score_e'] = (int)($request->input('score_e') ?? 0);

        $data['question_image'] = $this->handleImageUpload($request, 'question_image', $question->question_image, $request->boolean('hapus_question_image'));
        $data['image'] = $data['question_image'];
        $data['option_a_image'] = $this->handleImageUpload($request, 'option_a_image', $question->option_a_image, $request->boolean('hapus_option_a_image'));
        $data['option_b_image'] = $this->handleImageUpload($request, 'option_b_image', $question->option_b_image, $request->boolean('hapus_option_b_image'));
        $data['option_c_image'] = $this->handleImageUpload($request, 'option_c_image', $question->option_c_image, $request->boolean('hapus_option_c_image'));
        $data['option_d_image'] = $this->handleImageUpload($request, 'option_d_image', $question->option_d_image, $request->boolean('hapus_option_d_image'));
        $data['option_e_image'] = $this->handleImageUpload($request, 'option_e_image', $question->option_e_image, $request->boolean('hapus_option_e_image'));
        $data['explanation_image'] = $this->handleImageUpload($request, 'explanation_image', $question->explanation_image, $request->boolean('hapus_explanation_image'));

        $question->update($data);
        return redirect()->route('admin.questions.index', ['tryout_package_id' => $request->tryout_package_id])->with('success', 'Soal berhasil diperbarui.');
    }

    public function destroy(Question $question)
    {
        $packageId = $question->tryout_package_id;
        
        $images = [
            $question->question_image,
            $question->option_a_image,
            $question->option_b_image,
            $question->option_c_image,
            $question->option_d_image,
            $question->option_e_image,
            $question->explanation_image,
        ];
        
        foreach ($images as $img) {
            if ($img && file_exists(public_path($img))) {
                @unlink(public_path($img));
            }
        }
        
        $question->delete();
        return redirect()->route('admin.questions.index', ['tryout_package_id' => $packageId])->with('success', 'Soal berhasil dihapus.');
    }

    public function bulkDelete(Request $request)
    {
        $ids = $request->input('ids', []);
        if (!empty($ids)) {
            $questions = Question::whereIn('id', $ids)->get();
            foreach ($questions as $q) {
                $images = [$q->question_image, $q->option_a_image, $q->option_b_image, $q->option_c_image, $q->option_d_image, $q->option_e_image, $q->explanation_image];
                foreach ($images as $img) {
                    if ($img && file_exists(public_path($img))) {
                        @unlink(public_path($img));
                    }
                }
                $q->delete();
            }
            return back()->with('success', count($ids) . ' soal berhasil dihapus secara massal.');
        }
        return back()->with('error', 'Tidak ada soal yang terpilih.');
    }

    public function deleteByCategory(Request $request)
    {
        $categoryId = $request->input('category_id');
        if ($categoryId) {
            $questions = Question::where('category_id', $categoryId)->get();
            $count = $questions->count();
            foreach ($questions as $q) {
                $images = [$q->question_image, $q->option_a_image, $q->option_b_image, $q->option_c_image, $q->option_d_image, $q->option_e_image, $q->explanation_image];
                foreach ($images as $img) {
                    if ($img && file_exists(public_path($img))) {
                        @unlink(public_path($img));
                    }
                }
                $q->delete();
            }
            return back()->with('success', $count . ' soal dalam kategori tersebut berhasil dihapus.');
        }
        return back()->with('error', 'Kategori tidak valid.');
    }

    public function deleteBySubCategory(Request $request)
    {
        $subCategoryId = $request->input('sub_category_id');
        if ($subCategoryId) {
            $questions = Question::where('sub_category_id', $subCategoryId)->get();
            $count = $questions->count();
            foreach ($questions as $q) {
                $images = [$q->question_image, $q->option_a_image, $q->option_b_image, $q->option_c_image, $q->option_d_image, $q->option_e_image, $q->explanation_image];
                foreach ($images as $img) {
                    if ($img && file_exists(public_path($img))) {
                        @unlink(public_path($img));
                    }
                }
                $q->delete();
            }
            return back()->with('success', $count . ' soal dalam sub kategori tersebut berhasil dihapus.');
        }
        return back()->with('error', 'Sub Kategori tidak valid.');
    }

    public function importForm()
    {
        $packages = TryoutPackage::all();
        $categories = Category::all();
        return view('admin.questions.import', compact('packages', 'categories'));
    }

    public function importWordPreview(Request $request)
    {
        $request->validate([
            'file' => 'required|file|extensions:docx,zip',
            'tryout_package_id' => 'required|exists:tryout_packages,id',
        ]);

        $packageId = $request->tryout_package_id;
        $file = $request->file('file');
        $originalName = $file->getClientOriginalName();
        $filePath = $file->getRealPath();

        // 1. Log: File diterima
        \Log::info("DOCX Import: File diterima. Nama: {$originalName}, Ukuran: " . $file->getSize() . " bytes");

        $warnings = [];
        $questions = [];
        $stats = [
            'tables_count' => 0,
            'rows_count' => 0,
            'soal_count' => 0,
            'jawaban_count' => 0,
            'pembahasan_count' => 0,
            'kunci_count' => 0,
            'images_count' => 0,
            'jenis_found' => [],
        ];

        // Pastikan direktori temp tersedia
        $tempImportDir = public_path('storage/temp_import');
        if (!file_exists($tempImportDir)) {
            mkdir($tempImportDir, 0777, true);
        }

        $zipAvailable = class_exists('ZipArchive');
        \Log::info("DOCX Import: ZipArchive tersedia: " . ($zipAvailable ? 'YA' : 'TIDAK') . " | PHP SAPI: " . PHP_SAPI);

        if ($zipAvailable) {
            // --- Path 1: Gunakan PHPWord (memerlukan ZipArchive) ---
            try {
                \PhpOffice\PhpWord\Settings::setTempDir($tempImportDir);
                $phpWord = IOFactory::load($filePath);
                \Log::info("DOCX Import: File berhasil dibuka menggunakan PHPWord.");
                $questions = $this->parseWordTables($phpWord, $warnings, $stats);
                
                // Check if the document has images in its relationships, but PHPWord extracted 0 images
                $hasImagesInRels = false;
                $relsContent = $this->readFileFromZip($filePath, 'word/_rels/document.xml.rels');
                if (!empty($relsContent)) {
                    if (strpos($relsContent, '/relationships/image') !== false) {
                        $hasImagesInRels = true;
                    }
                }

                if ($hasImagesInRels && $stats['images_count'] === 0) {
                    \Log::warning("DOCX Import: PHPWord mengekstrak 0 gambar tetapi dokumen memiliki gambar di relasi. Menggunakan parser XML sebagai fallback.");
                    $warnings[] = "Catatan: Parser utama mendeteksi gambar tetapi gagal mengekstraknya menggunakan PHPWord. Sistem otomatis beralih ke parser XML cadangan.";
                    throw new \Exception("PHPWord gagal mengekstrak gambar.");
                }
            } catch (\Throwable $e) {
                \Log::error("DOCX Import: PHPWord gagal. Pesan: " . $e->getMessage() . ". Mencoba fallback XML parser.");
                $warnings[] = "PHPWord gagal ({$e->getMessage()}), menggunakan parser XML cadangan.";
                // Fallback ke XML parser jika PHPWord gagal
                try {
                    $questions = $this->parseDocxXml($filePath, $warnings, $stats, $tempImportDir);
                } catch (\Throwable $e2) {
                    \Log::error("DOCX Import: XML parser juga gagal. Pesan: " . $e2->getMessage());
                    return back()->with('error', 'Gagal memproses file Word: ' . $e->getMessage());
                }
            }
        } else {
            // --- Path 2: ZipArchive tidak tersedia, gunakan pure XML parser ---
            \Log::warning("DOCX Import: ZipArchive tidak tersedia di lingkungan web. Menggunakan parser XML cadangan.");
            $warnings[] = "Catatan: Ekstensi PHP ZIP tidak aktif di server web. Gambar dalam dokumen tidak dapat diekstrak, namun teks soal tetap dapat dibaca.";
            try {
                $questions = $this->parseDocxXml($filePath, $warnings, $stats, $tempImportDir);
            } catch (\Throwable $e) {
                \Log::error("DOCX Import: XML parser gagal. Pesan: " . $e->getMessage());
                return back()->with('error', 'Gagal memproses file Word: ' . $e->getMessage() . '. Pastikan file DOCX valid dan tidak terenkripsi.');
            }
        }

        // 3. Log: Hasil parsing
        \Log::info("DOCX Import: Proses parsing selesai. Tabel: {$stats['tables_count']}, Baris: {$stats['rows_count']}, Soal: {$stats['soal_count']}, Jawaban: {$stats['jawaban_count']}, Gambar: {$stats['images_count']}, Soal valid terbentuk: " . count($questions));

        if (empty($questions)) {
            $reason = "Tidak ditemukan format soal yang sesuai.";
            if ($stats['tables_count'] === 0) {
                $reason = "Gagal membaca tabel soal pada dokumen. Dokumen Anda tidak memiliki tabel sama sekali.";
            } elseif ($stats['soal_count'] === 0) {
                $uniqueJenis = implode(', ', array_unique($stats['jenis_found']));
                $reason = "Tidak ditemukan format soal yang sesuai. Ditemukan {$stats['tables_count']} tabel dengan {$stats['rows_count']} baris, tetapi tidak ada baris berjenis 'SOAL' di kolom 'Jenis'. Kolom 'Jenis' yang terdeteksi: [" . ($uniqueJenis ?: 'Kosong') . "]. Pastikan kolom kedua bertuliskan 'SOAL' dengan huruf besar.";
            } elseif ($stats['jawaban_count'] === 0) {
                $reason = "Tidak ditemukan pilihan jawaban. Baris berjenis 'SOAL' terdeteksi, tetapi tidak ada baris berjenis 'JAWABAN' di bawahnya.";
            } else {
                $reason = "Gagal membentuk soal yang valid dari tabel. Pastikan setiap soal memiliki pilihan jawaban berjenis 'JAWABAN' dengan bobot skor di kolom keempat.";
            }

            \Log::warning("DOCX Import: Gagal mengimpor. Alasan: {$reason}");
            return back()->with('error', "Gagal mengimpor file Word: {$reason}");
        }

        session([
            'temp_import_questions' => $questions,
            'temp_import_package_id' => $packageId,
            'temp_import_warnings'   => $warnings
        ]);

        $package = TryoutPackage::findOrFail($packageId);
        $group = Group::where('name', $package->group)->first();
        $codes = $group ? QuestionCode::where('group_id', $group->id)->get() : collect();

        return view('admin.questions.import_preview', compact('questions', 'package', 'group', 'codes'));
    }

    private function extractImagesFromCellXml(\SimpleXMLElement $cell, string $docxPath, array $rels, string $tempImportDir): array
    {
        $images = [];
        
        $cell->registerXPathNamespace('w', 'http://schemas.openxmlformats.org/wordprocessingml/2006/main');
        $cell->registerXPathNamespace('a', 'http://schemas.openxmlformats.org/drawingml/2006/main');
        $cell->registerXPathNamespace('v', 'urn:schemas-microsoft-com:vml');
        $cell->registerXPathNamespace('r', 'http://schemas.openxmlformats.org/officeDocument/2006/relationships');
        
        $blips = $cell->xpath('.//a:blip');
        $vmls = $cell->xpath('.//v:imagedata');
        
        $rIds = [];
        if (!empty($blips)) {
            foreach ($blips as $blip) {
                $attrs = $blip->attributes('http://schemas.openxmlformats.org/officeDocument/2006/relationships');
                if (isset($attrs['embed'])) {
                    $rIds[] = (string)$attrs['embed'];
                }
            }
        }
        if (!empty($vmls)) {
            foreach ($vmls as $vml) {
                $attrs = $vml->attributes('http://schemas.openxmlformats.org/officeDocument/2006/relationships');
                if (isset($attrs['id'])) {
                    $rIds[] = (string)$attrs['id'];
                }
            }
        }
        
        $rIds = array_unique($rIds);
        
        foreach ($rIds as $rId) {
            if (isset($rels[$rId])) {
                $target = $rels[$rId];
                $zipPath = 'word/' . ltrim($target, '/');
                
                $imageData = $this->readFileFromZip($docxPath, $zipPath);
                if (!empty($imageData)) {
                    $ext = pathinfo($zipPath, PATHINFO_EXTENSION) ?: 'png';
                    $filename = 'extracted_xml_img_' . uniqid() . '.' . $ext;
                    $fullPath = $tempImportDir . '/' . $filename;
                    if (file_put_contents($fullPath, $imageData) !== false) {
                        $images[] = 'storage/temp_import/' . $filename;
                        \Log::info("DOCX XML Parser: Berhasil mengekstrak gambar dari cell: {$zipPath}");
                    }
                }
            }
        }
        
        return $images;
    }

    /**
     * Fallback DOCX parser menggunakan XML murni.
     * Mendukung 4 cara membaca file DOCX (ZIP):
     * 1. zip:// stream wrapper  2. ZipArchive  3. PharData  4. Pure PHP binary reader
     */
    private function parseDocxXml(string $docxPath, array &$warnings, array &$stats, string $tempImportDir): array
    {
        \Log::info("DOCX XML Parser: Mulai parsing | PHP SAPI: " . PHP_SAPI . " | ZipArchive: " . (class_exists('ZipArchive') ? 'Ya' : 'Tidak'));

        $xmlContent = $this->readFileFromZip($docxPath, 'word/document.xml');

        if (empty($xmlContent)) {
            throw new \Exception(
                "Tidak dapat membaca word/document.xml dari file DOCX. " .
                "File mungkin rusak atau terenkripsi. " .
                "(SAPI: " . PHP_SAPI . ", ZipArchive: " . (class_exists('ZipArchive') ? 'Ya' : 'Tidak') . ")"
            );
        }

        \Log::info("DOCX XML Parser: Berhasil baca document.xml (" . strlen($xmlContent) . " bytes)");

        // Load relationships to map rId to image files
        $relsContent = $this->readFileFromZip($docxPath, 'word/_rels/document.xml.rels');
        $rels = [];
        if (!empty($relsContent)) {
            try {
                $relsXml = new \SimpleXMLElement($relsContent, LIBXML_NOERROR | LIBXML_NOWARNING);
                foreach ($relsXml->Relationship as $rel) {
                    $id = (string)$rel['Id'];
                    $target = (string)$rel['Target'];
                    $rels[$id] = $target;
                }
            } catch (\Exception $e) {
                \Log::warning("DOCX XML Parser: Gagal membaca document.xml.rels: " . $e->getMessage());
            }
        }

        try {
            $xml = new \SimpleXMLElement($xmlContent, LIBXML_NOERROR | LIBXML_NOWARNING);
        } catch (\Exception $e) {
            throw new \Exception("Gagal parse XML dokumen Word: " . $e->getMessage());
        }

        $namespaces = $xml->getNamespaces(true);
        $wNs = $namespaces['w'] ?? 'http://schemas.openxmlformats.org/wordprocessingml/2006/main';
        $xml->registerXPathNamespace('w', $wNs);

        $tables = $xml->xpath('//w:tbl');
        if (empty($tables)) $tables = $xml->xpath('w:body/w:tbl');

        $stats['tables_count'] = count($tables ?? []);
        \Log::info("DOCX XML Parser: Ditemukan " . $stats['tables_count'] . " tabel.");

        if (empty($tables)) return [];

        $cleanText = function($str) {
            return trim(preg_replace('/[\x{00a0}\x{200b}\s]+/u', ' ', $str));
        };

        $extractCellText = function(\SimpleXMLElement $cell): string {
            $paragraphs = $cell->xpath('.//w:p');
            if (empty($paragraphs)) {
                $result = '';
                foreach ($cell->xpath('.//w:t') as $t) $result .= (string)$t;
                return $result;
            }
            $paraTexts = [];
            foreach ($paragraphs as $p) {
                $pt = '';
                foreach ($p->xpath('.//w:t') as $t) $pt .= (string)$t;
                if ($pt !== '') $paraTexts[] = $pt;
            }
            return implode("\n", $paraTexts);
        };

        $questions = [];

        foreach ($tables as $tableIndex => $table) {
            $rows = $table->xpath('.//w:tr');
            $stats['rows_count'] += count($rows);
            \Log::info("DOCX XML Parser: Tabel #" . ($tableIndex + 1) . " = " . count($rows) . " baris.");

            $jenisColIndex = null; $isiColIndex = null; $jawabanColIndex = null; $headerMapped = false;
            $currentQuestion = null; $options = []; $qNum = count($questions) + 1;

            foreach ($rows as $row) {
                $cells = $row->xpath('.//w:tc');
                if (count($cells) < 3) continue;

                if (!$headerMapped) {
                    $cellTexts = [];
                    foreach ($cells as $ci => $cell) {
                        $cellTexts[$ci] = strtolower($cleanText($extractCellText($cell)));
                    }
                    if (in_array('jenis', $cellTexts) && in_array('isi', $cellTexts)) {
                        $jenisColIndex   = array_search('jenis', $cellTexts);
                        $isiColIndex     = array_search('isi', $cellTexts);
                        $jawabanColIndex = array_search('jawaban', $cellTexts);
                        if ($jawabanColIndex === false) {
                            foreach ($cellTexts as $idx => $txt) {
                                if (strpos($txt, 'jawab') !== false) { $jawabanColIndex = $idx; break; }
                            }
                        }
                        $headerMapped = true;
                        continue;
                    }
                }

                $n = count($cells);
                $jIdx  = $jenisColIndex  ?? ($n >= 4 ? 1 : 0);
                $iIdx  = $isiColIndex    ?? ($n >= 4 ? 2 : 1);
                $jwIdx = $jawabanColIndex ?? ($n >= 4 ? 3 : 2);

                if (!isset($cells[$jIdx]) || !isset($cells[$iIdx])) continue;

                $jenisUpper  = strtoupper($cleanText($extractCellText($cells[$jIdx])));
                $isiText     = trim($extractCellText($cells[$iIdx]));
                $jawabanText = isset($cells[$jwIdx]) ? $cleanText($extractCellText($cells[$jwIdx])) : '';

                $isiImages = $this->extractImagesFromCellXml($cells[$iIdx], $docxPath, $rels, $tempImportDir);
                $stats['images_count'] += count($isiImages);

                if (!empty($jenisUpper)) $stats['jenis_found'][] = $jenisUpper;

                $isSoal      = strpos($jenisUpper, 'SOAL') !== false || strpos($jenisUpper, 'PERTANYAAN') !== false;
                $isJawaban   = strpos($jenisUpper, 'JAWABAN') !== false || strpos($jenisUpper, 'PILIHAN') !== false || strpos($jenisUpper, 'OPSI') !== false;
                $isPembahasan = strpos($jenisUpper, 'PEMBAHASAN') !== false || strpos($jenisUpper, 'PENJELASAN') !== false;
                $isKunci     = strpos($jenisUpper, 'KUNCI') !== false || strpos($jenisUpper, 'JWB') !== false;

                if ($isSoal) {
                    $stats['soal_count']++;
                    if ($currentQuestion) {
                        $questions[] = $this->finalizeTableQuestion($currentQuestion, $options);
                        \Log::info("DOCX XML Parser: Soal #{$qNum} selesai dengan " . count($options) . " pilihan.");
                        $qNum++;
                    }
                    $currentQuestion = [
                        'soal' => $isiText, 
                        'question_image' => !empty($isiImages) ? $isiImages[0] : null,
                        'tingkat_kesulitan' => 'sedang', 
                        'pembahasan' => '', 
                        'explanation_image' => null,
                    ];
                    $options = [];
                } elseif ($isJawaban && $currentQuestion) {
                    $stats['jawaban_count']++;
                    $options[] = [
                        'text' => $isiText, 
                        'image' => !empty($isiImages) ? $isiImages[0] : null, 
                        'score' => (int)$jawabanText
                    ];
                } elseif ($isPembahasan && $currentQuestion) {
                    $stats['pembahasan_count']++;
                    $currentQuestion['pembahasan'] = $isiText;
                    if (!empty($isiImages)) {
                        $currentQuestion['explanation_image'] = $isiImages[0];
                    }
                } elseif ($isKunci && $currentQuestion) {
                    $stats['kunci_count']++;
                    if (preg_match('/([A-E])/i', $isiText . $jawabanText, $m)) {
                        $currentQuestion['jawaban_benar'] = strtoupper($m[1]);
                    }
                }
            }

            if ($currentQuestion) {
                $questions[] = $this->finalizeTableQuestion($currentQuestion, $options);
                \Log::info("DOCX XML Parser: Soal #{$qNum} (terakhir) selesai dengan " . count($options) . " pilihan.");
            }
        }

        \Log::info("DOCX XML Parser: Selesai. Total: " . count($questions) . " soal.");
        return $questions;
    }

    /**
     * Membaca sebuah file dari dalam arsip ZIP menggunakan 4 metode fallback.
     * Urutan: zip:// stream → ZipArchive → PharData → Pure PHP binary reader
     */
    private function readFileFromZip(string $zipPath, string $entryName): ?string
    {
        // Metode 1: zip:// stream wrapper
        if (in_array('zip', stream_get_wrappers())) {
            $content = @file_get_contents("zip://{$zipPath}#{$entryName}");
            if (!empty($content)) {
                \Log::info("readFileFromZip: Berhasil via zip:// stream.");
                return $content;
            }
        }

        // Metode 2: ZipArchive class
        if (class_exists('ZipArchive')) {
            try {
                $zip = new \ZipArchive();
                if ($zip->open($zipPath) === true) {
                    $content = $zip->getFromName($entryName);
                    $zip->close();
                    if (!empty($content)) {
                        \Log::info("readFileFromZip: Berhasil via ZipArchive.");
                        return $content;
                    }
                }
            } catch (\Throwable $e) {
                \Log::warning("readFileFromZip: ZipArchive gagal: " . $e->getMessage());
            }
        }

        // Metode 3: PharData (tersedia sejak PHP 5.3, tidak butuh ext/zip)
        if (class_exists('PharData')) {
            $tmpZip = null;
            try {
                $tmpZip = sys_get_temp_dir() . '/docx_' . uniqid() . '.zip';
                if (copy($zipPath, $tmpZip)) {
                    $phar = new \PharData($tmpZip);
                    if (isset($phar[$entryName])) {
                        $content = file_get_contents($phar[$entryName]->getPathname());
                        @unlink($tmpZip);
                        if (!empty($content)) {
                            \Log::info("readFileFromZip: Berhasil via PharData.");
                            return $content;
                        }
                    }
                }
            } catch (\Throwable $e) {
                \Log::warning("readFileFromZip: PharData gagal: " . $e->getMessage());
            } finally {
                if ($tmpZip && file_exists($tmpZip)) @unlink($tmpZip);
            }
        }

        // Metode 4: Pure PHP binary ZIP reader (tidak butuh extension apapun)
        \Log::info("readFileFromZip: Mencoba pure PHP binary reader...");
        return $this->readFileFromZipBinary($zipPath, $entryName);
    }

    /**
     * Membaca file dari ZIP menggunakan parsing binary murni PHP.
     * Tidak memerlukan extension apapun. Mendukung DEFLATE (method 8) dan STORED (method 0).
     */
    private function readFileFromZipBinary(string $zipPath, string $entryName): ?string
    {
        $fp = @fopen($zipPath, 'rb');
        if (!$fp) {
            \Log::error("readFileFromZipBinary: Tidak dapat membuka file {$zipPath}");
            return null;
        }

        try {
            while (!feof($fp)) {
                $sig = fread($fp, 4);
                if ($sig === false || strlen($sig) < 4) break;
                if ($sig !== "PK\x03\x04") break; // Bukan local file header

                $header = fread($fp, 26);
                if ($header === false || strlen($header) < 26) break;

                $data = unpack('vversion/vflags/vcompression/vlastmod_time/vlastmod_date/Vcrc32/Vcompressed_size/Vuncompressed_size/vfilename_len/vextra_len', $header);

                $filename = $data['filename_len'] > 0 ? fread($fp, $data['filename_len']) : '';
                if ($data['extra_len'] > 0) {
                    fread($fp, $data['extra_len']); // skip extra
                }

                if ($filename === false || $filename === '') break;

                if ($filename === $entryName) {
                    $compressedData = $data['compressed_size'] > 0 ? fread($fp, $data['compressed_size']) : '';
                    fclose($fp);

                    if ($data['compression'] === 0) {
                        // STORED
                        \Log::info("readFileFromZipBinary: Berhasil '{$entryName}' (STORED).");
                        return $compressedData;
                    } elseif ($data['compression'] === 8) {
                        // DEFLATE
                        $result = @gzinflate($compressedData);
                        if ($result !== false) {
                            \Log::info("readFileFromZipBinary: Berhasil '{$entryName}' (DEFLATE).");
                            return $result;
                        }
                        \Log::error("readFileFromZipBinary: Gagal decompress DEFLATE untuk '{$entryName}'.");
                        return null;
                    } else {
                        \Log::error("readFileFromZipBinary: Compression method {$data['compression']} tidak didukung.");
                        return null;
                    }
                } else {
                    if ($data['compressed_size'] > 0) fseek($fp, $data['compressed_size'], SEEK_CUR);
                }
            }
        } finally {
            if (is_resource($fp)) fclose($fp);
        }

        \Log::warning("readFileFromZipBinary: Entry '{$entryName}' tidak ditemukan.");
        return null;
    }


    private function parseWordTables($phpWord, &$warnings, &$stats = null)
    {
        if ($stats === null) {
            $stats = [
                'tables_count' => 0,
                'rows_count' => 0,
                'soal_count' => 0,
                'jawaban_count' => 0,
                'pembahasan_count' => 0,
                'kunci_count' => 0,
                'images_count' => 0,
                'jenis_found' => [],
            ];
        }

        $questions = [];
        $tableIndex = 0;

        $cleanText = function($str) {
            // Bersihkan unicode non-breaking space (\x{00a0}), zero-width space (\x{200b}), dan whitespace lainnya ke spasi biasa
            $str = preg_replace('/[\x{00a0}\x{200b}\s]+/u', ' ', $str);
            return trim($str);
        };

        foreach ($phpWord->getSections() as $section) {
            foreach ($section->getElements() as $element) {
                if ($element instanceof \PhpOffice\PhpWord\Element\Table) {
                    $tableIndex++;
                    $stats['tables_count'] = $tableIndex;
                    
                    $currentQuestion = null;
                    $options = [];
                    $qNum = count($questions) + 1;

                    $rows = $element->getRows();
                    $stats['rows_count'] += count($rows);

                    // 4. Log: Jumlah tabel dan baris ditemukan
                    \Log::info("DOCX Import: Menganalisis Tabel #{$tableIndex} dengan " . count($rows) . " baris.");

                    $jenisColIndex = null;
                    $isiColIndex = null;
                    $jawabanColIndex = null;
                    $headerMapped = false;

                    foreach ($rows as $rowIndex => $row) {
                        $cells = $row->getCells();
                        if (count($cells) < 3) {
                            continue;
                        }

                        // Try to dynamically detect/map headers if not already done
                        if (!$headerMapped) {
                            $cellTexts = [];
                            foreach ($cells as $cellIndex => $cell) {
                                $txt = '';
                                $imgs = [];
                                $this->extractElementContent($cell, $txt, $imgs);
                                $cellTexts[$cellIndex] = strtolower($cleanText($txt));
                            }

                            // Check if this looks like a header row
                            $hasJenis = in_array('jenis', $cellTexts);
                            $hasIsi = in_array('isi', $cellTexts);
                            
                            if ($hasJenis && $hasIsi) {
                                $jenisColIndex = array_search('jenis', $cellTexts);
                                $isiColIndex = array_search('isi', $cellTexts);
                                
                                // 'jawaban' might be optional or named slightly differently
                                $jawabanColIndex = array_search('jawaban', $cellTexts);
                                if ($jawabanColIndex === false) {
                                    foreach ($cellTexts as $idx => $txt) {
                                        if (strpos($txt, 'jawab') !== false) {
                                            $jawabanColIndex = $idx;
                                            break;
                                        }
                                    }
                                }
                                
                                $headerMapped = true;
                                \Log::info("DOCX Import: Header tabel terdeteksi. Kolom 'Jenis' di indeks {$jenisColIndex}, 'Isi' di {$isiColIndex}, 'Jawaban' di " . ($jawabanColIndex !== false ? $jawabanColIndex : 'Tidak ditemukan') . ".");
                                continue; // Skip header row from processing as data
                            }
                        }

                        // Determine indices to use
                        $jIdx = $jenisColIndex;
                        $iIdx = $isiColIndex;
                        $jwIdx = $jawabanColIndex;

                        if ($jIdx === null || $iIdx === null) {
                            // Fallback to default index mapping if header not found yet
                            if (count($cells) >= 4) {
                                $jIdx = 1;
                                $iIdx = 2;
                                $jwIdx = 3;
                            } else {
                                $jIdx = 0;
                                $iIdx = 1;
                                $jwIdx = 2;
                            }
                        }

                        // Ensure indices are within bounds of this row's cells
                        if (!isset($cells[$jIdx]) || !isset($cells[$iIdx])) {
                            continue;
                        }

                        $jenisCell = $cells[$jIdx];
                        $isiCell = $cells[$iIdx];
                        $jawabanCell = ($jwIdx !== null && isset($cells[$jwIdx])) ? $cells[$jwIdx] : null;

                        $jenisText = '';
                        $jenisImages = [];
                        $this->extractElementContent($jenisCell, $jenisText, $jenisImages);
                        
                        $jenisUpper = strtoupper($cleanText($jenisText));
                        if (!empty($jenisUpper)) {
                            $stats['jenis_found'][] = $jenisUpper;
                        }

                        // Flexible Keyword Matching
                        $isSoal = (strpos($jenisUpper, 'SOAL') !== false || strpos($jenisUpper, 'PERTANYAAN') !== false);
                        $isJawaban = (strpos($jenisUpper, 'JAWABAN') !== false || strpos($jenisUpper, 'PILIHAN') !== false || strpos($jenisUpper, 'OPSI') !== false);
                        $isPembahasan = (strpos($jenisUpper, 'PEMBAHASAN') !== false || strpos($jenisUpper, 'PENJELASAN') !== false || strpos($jenisUpper, 'SOLUSI') !== false || strpos($jenisUpper, 'KETERANGAN') !== false);
                        $isKunci = (strpos($jenisUpper, 'KUNCI') !== false || strpos($jenisUpper, 'JWB') !== false);

                        if ($isSoal) {
                            $stats['soal_count']++;
                            if ($currentQuestion) {
                                $questions[] = $this->finalizeTableQuestion($currentQuestion, $options);
                                \Log::info("DOCX Import: Soal #{$qNum} berhasil di-parse dengan " . count($options) . " pilihan jawaban.");
                                $qNum++;
                            }

                            $hasImg = $this->hasImageElement($isiCell);
                            $isiText = '';
                            $isiImages = [];
                            $this->extractElementContent($isiCell, $isiText, $isiImages);
                            $stats['images_count'] += count($isiImages);

                            $currentQuestion = [
                                'soal' => trim($isiText),
                                'question_image' => !empty($isiImages) ? $isiImages[0] : null,
                                'tingkat_kesulitan' => 'sedang',
                                'pembahasan' => '',
                                'explanation_image' => null,
                            ];

                            if ($hasImg && empty($isiImages)) {
                                $warnings[] = "REVIEW REQUIRED: Gagal mengekstrak gambar soal pada Soal #{$qNum}.";
                                $currentQuestion['review_required'] = true;
                            }

                            $options = [];
                        } elseif ($isJawaban) {
                            $stats['jawaban_count']++;
                            if ($currentQuestion) {
                                $hasImg = $this->hasImageElement($isiCell);
                                $isiText = '';
                                $isiImages = [];
                                $this->extractElementContent($isiCell, $isiText, $isiImages);
                                $stats['images_count'] += count($isiImages);

                                $jawabanText = '';
                                $jawabanImages = [];
                                if ($jawabanCell) {
                                    $this->extractElementContent($jawabanCell, $jawabanText, $jawabanImages);
                                }
                                $score = (int)$cleanText($jawabanText);

                                $options[] = [
                                    'text' => trim($isiText),
                                    'image' => !empty($isiImages) ? $isiImages[0] : null,
                                    'score' => $score
                                ];

                                if ($hasImg && empty($isiImages)) {
                                    $warnings[] = "REVIEW REQUIRED: Gagal mengekstrak gambar opsi pada Soal #{$qNum}, Opsi " . chr(65 + count($options) - 1) . ".";
                                    $currentQuestion['review_required'] = true;
                                }
                            }
                        } elseif ($isPembahasan) {
                            $stats['pembahasan_count']++;
                            if ($currentQuestion) {
                                $hasImg = $this->hasImageElement($isiCell);
                                $isiText = '';
                                $isiImages = [];
                                $this->extractElementContent($isiCell, $isiText, $isiImages);
                                $stats['images_count'] += count($isiImages);

                                $currentQuestion['pembahasan'] = trim($isiText);
                                if (!empty($isiImages)) {
                                    $currentQuestion['explanation_image'] = $isiImages[0];
                                }

                                if ($hasImg && empty($isiImages)) {
                                    $warnings[] = "REVIEW REQUIRED: Gagal mengekstrak gambar pembahasan pada Soal #{$qNum}.";
                                    $currentQuestion['review_required'] = true;
                                }
                            }
                        } elseif ($isKunci) {
                            $stats['kunci_count']++;
                            if ($currentQuestion) {
                                $kunciText = '';
                                $kunciImages = [];
                                $this->extractElementContent($isiCell, $kunciText, $kunciImages);

                                $kunciVal = strtoupper($cleanText($kunciText));
                                if (preg_match('/([A-E])/i', $kunciVal, $matches)) {
                                    $currentQuestion['jawaban_benar'] = $matches[1];
                                }
                            }
                        }
                    }

                    if ($currentQuestion) {
                        $questions[] = $this->finalizeTableQuestion($currentQuestion, $options);
                        \Log::info("DOCX Import: Soal #{$qNum} berhasil di-parse dengan " . count($options) . " pilihan jawaban.");
                    }
                }
            }
        }

        return $questions;
    }

    private function extractElementContent($element, &$text, &$images)
    {
        if ($element instanceof \PhpOffice\PhpWord\Element\Text) {
            $text .= $element->getText();
        } elseif ($element instanceof \PhpOffice\PhpWord\Element\TextBreak) {
            $text .= "\n";
        } elseif ($element instanceof \PhpOffice\PhpWord\Element\TextRun) {
            foreach ($element->getElements() as $child) {
                $this->extractElementContent($child, $text, $images);
            }
            $text .= "\n";
        } elseif ($element instanceof \PhpOffice\PhpWord\Element\Link) {
            $text .= $element->getText();
        } elseif ($element instanceof \PhpOffice\PhpWord\Element\Title) {
            $text .= $element->getText();
        } elseif ($element instanceof \PhpOffice\PhpWord\Element\ListItem) {
            if (method_exists($element, 'getText')) {
                $text .= $element->getText();
            }
            if (method_exists($element, 'getElements')) {
                foreach ($element->getElements() as $child) {
                    $this->extractElementContent($child, $text, $images);
                }
            }
        } elseif ($element instanceof \PhpOffice\PhpWord\Element\Image) {
            $imgPath = $this->savePhpWordImage($element);
            if ($imgPath) {
                $images[] = $imgPath;
            }
        } elseif ($element instanceof \PhpOffice\PhpWord\Element\Shape || $element instanceof \PhpOffice\PhpWord\Element\Object) {
            $imgPath = $this->savePhpWordImage($element);
            if ($imgPath) {
                $images[] = $imgPath;
            }
        } elseif (method_exists($element, 'getSource') || method_exists($element, 'getImageString') || method_exists($element, 'getImageBinary')) {
            if (!($element instanceof \PhpOffice\PhpWord\Element\Text) && !($element instanceof \PhpOffice\PhpWord\Element\TextRun)) {
                $imgPath = $this->savePhpWordImage($element);
                if ($imgPath) {
                    $images[] = $imgPath;
                }
            }
        } elseif (method_exists($element, 'getElements')) {
            foreach ($element->getElements() as $child) {
                $this->extractElementContent($child, $text, $images);
            }
        }
    }

    private function hasImageElement($element)
    {
        if ($element instanceof \PhpOffice\PhpWord\Element\Image || 
            $element instanceof \PhpOffice\PhpWord\Element\Shape ||
            $element instanceof \PhpOffice\PhpWord\Element\Object) {
            return true;
        }
        if (method_exists($element, 'getSource') || method_exists($element, 'getImageString') || method_exists($element, 'getImageBinary')) {
            if (!($element instanceof \PhpOffice\PhpWord\Element\Text) && !($element instanceof \PhpOffice\PhpWord\Element\TextRun)) {
                return true;
            }
        }
        if (method_exists($element, 'getElements')) {
            foreach ($element->getElements() as $child) {
                if ($this->hasImageElement($child)) {
                    return true;
                }
            }
        }
        return false;
    }

    private function savePhpWordImage($imageElement)
    {
        $source = null;
        if (method_exists($imageElement, 'getSource')) {
            $source = $imageElement->getSource();
        }
        
        $imageData = null;
        $ext = 'png';

        // Check if the source is a ZIP archive entry
        if ($source && strpos($source, 'zip://') === 0) {
            $archivePath = substr($source, 6);
            $hashPos = strrpos($archivePath, '#');
            if ($hashPos !== false) {
                $zipFile = substr($archivePath, 0, $hashPos);
                $entryName = substr($archivePath, $hashPos + 1);
                
                $imageData = $this->readFileFromZip($zipFile, $entryName);
                if (!empty($imageData)) {
                    $ext = pathinfo($entryName, PATHINFO_EXTENSION) ?: 'png';
                    \Log::info("DOCX Import: Berhasil mengekstrak gambar langsung dari ZIP entry '{$entryName}' menggunakan readFileFromZip.");
                } else {
                    \Log::error("DOCX Import: Gagal mengekstrak gambar dari ZIP archive '{$zipFile}' untuk '{$entryName}'.");
                }
            }
        }

        // Fallback to standard methods if ZIP extraction didn't run or failed
        if (empty($imageData)) {
            if (empty($source)) {
                if (method_exists($imageElement, 'getImageString')) {
                    $imageData = $imageElement->getImageString();
                } elseif (method_exists($imageElement, 'getImageBinary')) {
                    $imageData = $imageElement->getImageBinary();
                }
            } else {
                if (strpos($source, 'data:') === 0) {
                    $parts = explode(',', $source);
                    if (isset($parts[1])) {
                        $imageData = base64_decode($parts[1]);
                        if (preg_match('/data:image\/(\w+);base64/', $parts[0], $matches)) {
                            $ext = $matches[1];
                        }
                    }
                } elseif (file_exists($source)) {
                    $imageData = file_get_contents($source);
                    $ext = pathinfo($source, PATHINFO_EXTENSION) ?: 'png';
                } else {
                    if (method_exists($imageElement, 'getImageString')) {
                        $imageData = $imageElement->getImageString();
                    } elseif (method_exists($imageElement, 'getImageBinary')) {
                        $imageData = $imageElement->getImageBinary();
                    }
                    $ext = pathinfo($source, PATHINFO_EXTENSION) ?: 'png';
                }
            }
        }

        if ($imageData) {
            $tempDestDir = public_path('storage/temp_import');
            if (!file_exists($tempDestDir)) {
                if (!mkdir($tempDestDir, 0777, true)) {
                    \Log::error("DOCX Import: Gagal membuat direktori penyimpanan sementara di {$tempDestDir}");
                    return null;
                }
            }
            $filename = 'extracted_img_' . uniqid() . '.' . $ext;
            $fullPath = $tempDestDir . '/' . $filename;
            if (file_put_contents($fullPath, $imageData) === false) {
                \Log::error("DOCX Import: Gagal menulis file gambar ke {$fullPath}");
                return null;
            }
            \Log::info("DOCX Import: Berhasil menyimpan gambar hasil ekstraksi ke {$fullPath}");
            return 'storage/temp_import/' . $filename;
        }

        return null;
    }

    private function finalizeTableQuestion($q, $options)
    {
        $q['opsi_a'] = '';
        $q['option_a_image'] = null;
        $q['opsi_b'] = '';
        $q['option_b_image'] = null;
        $q['opsi_c'] = '';
        $q['option_c_image'] = null;
        $q['opsi_d'] = '';
        $q['option_d_image'] = null;
        $q['opsi_e'] = '';
        $q['option_e_image'] = null;

        $q['score_a'] = 0;
        $q['score_b'] = 0;
        $q['score_c'] = 0;
        $q['score_d'] = 0;
        $q['score_e'] = 0;

        if (!isset($q['jawaban_benar'])) {
            $q['jawaban_benar'] = 'A';
        }

        $labels = ['a', 'b', 'c', 'd', 'e'];
        $highestScore = -1;
        $bestOptionIndex = 0;
        $hasExplicitScores = false;

        foreach ($options as $index => $opt) {
            if ($index >= 5) {
                break;
            }

            $label = $labels[$index];
            $q["opsi_" . $label] = $opt['text'];
            if ($opt['image']) {
                $q["option_" . $label . "_image"] = $opt['image'];
            }

            $q["score_" . $label] = $opt['score'];
            if ($opt['score'] > 0) {
                $hasExplicitScores = true;
            }

            if ($opt['score'] > $highestScore) {
                $highestScore = $opt['score'];
                $bestOptionIndex = $index;
            }
        }

        // Scale binary 1 to 5 for single correct option to match standard scoring by default
        if ($hasExplicitScores) {
            $nonZeroScoresCount = 0;
            foreach ($options as $opt) {
                if ($opt['score'] > 0) {
                    $nonZeroScoresCount++;
                }
            }
            if ($highestScore === 1 && $nonZeroScoresCount === 1) {
                foreach ($labels as $lbl) {
                    if ($q["score_" . $lbl] === 1) {
                        $q["score_" . $lbl] = 5;
                    }
                }
                $highestScore = 5;
            }
        }

        if (!empty($options)) {
            if ($hasExplicitScores) {
                $q['jawaban_benar'] = strtoupper($labels[$bestOptionIndex]);
            } else {
                $correctLetter = $q['jawaban_benar'] ?? 'A';
                $correctIndex = array_search(strtolower($correctLetter), $labels);
                if ($correctIndex !== false) {
                    $q["score_" . strtolower($correctLetter)] = 5;
                }
            }
        }

        return $q;
    }

    private function finalizeParagraphQuestion($q)
    {
        $correct = $q['jawaban_benar'] ?? 'A';
        $q['score_a'] = ($correct === 'A') ? 5 : 0;
        $q['score_b'] = ($correct === 'B') ? 5 : 0;
        $q['score_c'] = ($correct === 'C') ? 5 : 0;
        $q['score_d'] = ($correct === 'D') ? 5 : 0;
        $q['score_e'] = ($correct === 'E') ? 5 : 0;
        return $q;
    }

    private function parseWordParagraphs($phpWord, &$warnings)
    {
        $questions = [];
        $currentQuestion = null;
        $state = 'none';

        foreach ($phpWord->getSections() as $section) {
            foreach ($section->getElements() as $element) {
                if ($element instanceof \PhpOffice\PhpWord\Element\Table) {
                    continue;
                }

                $hasImg = $this->hasImageElement($element);
                $text = '';
                $images = [];
                $this->extractElementContent($element, $text, $images);

                $trimmed = trim($text);
                $imagePath = !empty($images) ? $images[0] : null;

                if ($hasImg && empty($images)) {
                    $qNum = count($questions) + 1;
                    $warnings[] = "REVIEW REQUIRED: Gagal mengekstrak gambar pada paragraf Soal #{$qNum}.";
                    if ($currentQuestion) {
                        $currentQuestion['review_required'] = true;
                    }
                }

                if ($trimmed === '' && !$imagePath) {
                    continue;
                }

                $isNewQuestionHeader = false;
                $questionTextPayload = '';

                if (preg_match('/^(?:SOAL\s*\d*[:\.]|\[SOAL\]|SOAL)\s*(.*)/i', $trimmed, $matches)) {
                    $isNewQuestionHeader = true;
                    $questionTextPayload = $matches[1];
                } elseif (preg_match('/^(?:No\.?\s*)?(\d+)(?:[\.\)\-\s])\s*(.*)/i', $trimmed, $matches)) {
                    if (!$currentQuestion || $state !== 'soal') {
                        $isNewQuestionHeader = true;
                        $questionTextPayload = $matches[2];
                    }
                }

                if ($isNewQuestionHeader) {
                    if ($currentQuestion) {
                        $questions[] = $this->finalizeParagraphQuestion($currentQuestion);
                    }
                    $currentQuestion = [
                        'soal' => $questionTextPayload,
                        'question_image' => $imagePath,
                        'opsi_a' => '',
                        'option_a_image' => null,
                        'opsi_b' => '',
                        'option_b_image' => null,
                        'opsi_c' => '',
                        'option_c_image' => null,
                        'opsi_d' => '',
                        'option_d_image' => null,
                        'opsi_e' => '',
                        'option_e_image' => null,
                        'jawaban_benar' => 'A',
                        'pembahasan' => '',
                        'explanation_image' => null,
                        'tingkat_kesulitan' => 'sedang'
                    ];
                    $state = 'soal';
                    continue;
                }

                if ($currentQuestion) {
                    // Split option paragraph if it contains one or more option markers
                    if (preg_match('/^\s*[\(\[]?\s*([A-E])(?:[:\.\)\-\s]|(?=\]))/i', $trimmed, $leadMatch)) {
                        $parts = preg_split('/(?:\s+|^)[\(\[]?\s*([A-E])(?:[:\.\)\-\s]|(?=\]))\s*/i', $trimmed, -1, PREG_SPLIT_DELIM_CAPTURE);
                        if (count($parts) >= 3) {
                            $currentLabel = null;
                            for ($i = 0; $i < count($parts); $i++) {
                                if ($i % 2 === 1) {
                                    $currentLabel = strtolower($parts[$i]);
                                } elseif ($currentLabel) {
                                    $val = trim($parts[$i]);
                                    $currentQuestion["opsi_" . $currentLabel] = $val;
                                    if ($imagePath && $currentLabel === 'a') {
                                        $currentQuestion["option_a_image"] = $imagePath;
                                    }
                                    $state = "opsi_" . $currentLabel;
                                }
                            }
                            continue;
                        }
                    }

                    if (preg_match('/^(?:KUNCI|JAWABAN|KUNCI\s+JAWABAN)[:\.\s\-]*\s*[\(\[\'\"]?\s*([A-E])\s*[\)\]\'\"]?/i', $trimmed, $matches)) {
                        $currentQuestion['jawaban_benar'] = strtoupper($matches[1]);
                        $state = 'kunci';
                        continue;
                    }
                    if (preg_match('/^(?:PEMBAHASAN)[:\.\s\-]*\s*(.*)/i', $trimmed, $matches)) {
                        $currentQuestion['pembahasan'] = $matches[1];
                        if ($imagePath) $currentQuestion['explanation_image'] = $imagePath;
                        $state = 'pembahasan';
                        continue;
                    }

                    if ($state === 'soal') {
                        $currentQuestion['soal'] .= ($currentQuestion['soal'] !== '' ? "\n" : '') . $text;
                        if ($imagePath && empty($currentQuestion['question_image'])) {
                            $currentQuestion['question_image'] = $imagePath;
                        }
                    } elseif ($state === 'opsi_a') {
                        $currentQuestion['opsi_a'] .= ($currentQuestion['opsi_a'] !== '' ? "\n" : '') . $text;
                        if ($imagePath && empty($currentQuestion['option_a_image'])) {
                            $currentQuestion['option_a_image'] = $imagePath;
                        }
                    } elseif ($state === 'opsi_b') {
                        $currentQuestion['opsi_b'] .= ($currentQuestion['opsi_b'] !== '' ? "\n" : '') . $text;
                        if ($imagePath && empty($currentQuestion['option_b_image'])) {
                            $currentQuestion['option_b_image'] = $imagePath;
                        }
                    } elseif ($state === 'opsi_c') {
                        $currentQuestion['opsi_c'] .= ($currentQuestion['opsi_c'] !== '' ? "\n" : '') . $text;
                        if ($imagePath && empty($currentQuestion['option_c_image'])) {
                            $currentQuestion['option_c_image'] = $imagePath;
                        }
                    } elseif ($state === 'opsi_d') {
                        $currentQuestion['opsi_d'] .= ($currentQuestion['opsi_d'] !== '' ? "\n" : '') . $text;
                        if ($imagePath && empty($currentQuestion['option_d_image'])) {
                            $currentQuestion['option_d_image'] = $imagePath;
                        }
                    } elseif ($state === 'opsi_e') {
                        $currentQuestion['opsi_e'] .= ($currentQuestion['opsi_e'] !== '' ? "\n" : '') . $text;
                        if ($imagePath && empty($currentQuestion['option_e_image'])) {
                            $currentQuestion['option_e_image'] = $imagePath;
                        }
                    } elseif ($state === 'pembahasan') {
                        $currentQuestion['pembahasan'] .= ($currentQuestion['pembahasan'] !== '' ? "\n" : '') . $text;
                        if ($imagePath && empty($currentQuestion['explanation_image'])) {
                            $currentQuestion['explanation_image'] = $imagePath;
                        }
                    }
                }
            }
        }

if ($currentQuestion) {
            $questions[] = $this->finalizeParagraphQuestion($currentQuestion);
        }

        return $questions;
    }



    public function importPdfPreview(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:pdf',
            'tryout_package_id' => 'required|exists:tryout_packages,id',
        ]);

        $packageId = $request->tryout_package_id;
        $file = $request->file('file');

        $parser = new \Smalot\PdfParser\Parser();
        try {
            $pdf = $parser->parseFile($file->getRealPath());
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal memproses file PDF: ' . $e->getMessage());
        }

        $warnings = [];

        // Ensure temp directory exists
        $tempDestDir = public_path('storage/temp_import');
        if (!file_exists($tempDestDir)) {
            mkdir($tempDestDir, 0777, true);
        }

        // 1. Extract all images globally
        $extractedImages = [];
        try {
            $objects = $pdf->getObjects();
            foreach ($objects as $key => $object) {
                if ($object instanceof \Smalot\PdfParser\PDFObject && $object->getHeader()->get('Subtype') == 'Image') {
                    $data = $object->getContent();
                    if (!empty($data)) {
                        $ext = 'jpg';
                        $filter = $object->getHeader()->get('Filter');
                        if ($filter == 'FlateDecode') {
                            $ext = 'png';
                        }
                        $filename = time() . '_' . uniqid() . '_pdf_img.' . $ext;
                        file_put_contents($tempDestDir . '/' . $filename, $data);
                        $extractedImages[] = 'storage/temp_import/' . $filename;
                    }
                }
            }
        } catch (\Exception $e) {
            $warnings[] = "Gagal mengekstrak gambar dari PDF: " . $e->getMessage();
        }

        // 2. Extract text page by page
        $fullText = '';
        try {
            $pages = $pdf->getPages();
            foreach ($pages as $page) {
                $fullText .= $page->getText() . "\n";
            }
        } catch (\Exception $e) {
            $fullText = $pdf->getText();
        }

        // 3. Parse text line-by-line based on the table format: No | Jenis | Isi | Jawaban
        $lines = explode("\n", $fullText);
        $questions = [];
        
        // This will hold the parsed questions in their "raw" form before final mapping/normalization
        $rawQuestions = [];
        $currentQuestion = null;
        $options = [];
        $lastState = null; // 'soal', 'opsi', 'pembahasan'

        $cleanText = function($str) {
            return trim(preg_replace('/[\x{00a0}\x{200b}\s]+/u', ' ', $str));
        };

        foreach ($lines as $line) {
            $trimmed = trim($line);
            if ($trimmed === '') {
                continue;
            }

            $parts = [];
            if (strpos($trimmed, '|') !== false) {
                // Split by vertical bars
                $rawParts = explode('|', $trimmed);
                foreach ($rawParts as $p) {
                    $parts[] = $cleanText($p);
                }
            } else {
                // Heuristic regex matching: [No] [Jenis] [Isi] [Jawaban]
                // Keywords: SOAL, JAWABAN, PEMBAHASAN, PERTANYAAN, PILIHAN, OPSI, KUNCI JAWABAN, KUNCI, PENJELASAN, SOLUSI, KETERANGAN
                $pattern = '/^(?:(\d+)\s+)?(SOAL|JAWABAN|PEMBAHASAN|PERTANYAAN|PILIHAN|OPSI|KUNCI\s+JAWABAN|KUNCI|PENJELASAN|SOLUSI|KETERANGAN)\s+(.*)$/i';
                if (preg_match($pattern, $trimmed, $matches)) {
                    $no = $matches[1] !== '' ? $matches[1] : '';
                    $jenis = strtoupper($matches[2]);
                    $rest = trim($matches[3]);
                    
                    $isi = $rest;
                    $jawaban = '';
                    
                    // If it is a JAWABAN row, extract score/key at the end
                    if (strpos($jenis, 'JAWABAN') !== false || strpos($jenis, 'PILIHAN') !== false || strpos($jenis, 'OPSI') !== false) {
                        if (preg_match('/^(.*)\s+(\d+)$/', $rest, $subMatches)) {
                            $isi = trim($subMatches[1]);
                            $jawaban = $subMatches[2];
                        }
                    }
                    
                    $parts = [$no, $jenis, $isi, $jawaban];
                } else {
                    // Append as continuation text if we have an active state
                    if ($currentQuestion && $lastState) {
                        if ($lastState === 'soal') {
                            $currentQuestion['soal'] .= "\n" . $line;
                        } elseif ($lastState === 'opsi' && !empty($options)) {
                            $options[count($options) - 1]['text'] .= "\n" . $line;
                        } elseif ($lastState === 'pembahasan') {
                            $currentQuestion['pembahasan'] .= "\n" . $line;
                        }
                    }
                    continue;
                }
            }

            if (count($parts) < 2) {
                continue;
            }

            $no = $parts[0] ?? '';
            $jenis = strtoupper($parts[1] ?? '');
            $isi = $parts[2] ?? '';
            $jawaban = $parts[3] ?? '';

            $isSoal = (strpos($jenis, 'SOAL') !== false || strpos($jenis, 'PERTANYAAN') !== false);
            $isJawaban = (strpos($jenis, 'JAWABAN') !== false || strpos($jenis, 'PILIHAN') !== false || strpos($jenis, 'OPSI') !== false);
            $isPembahasan = (strpos($jenis, 'PEMBAHASAN') !== false || strpos($jenis, 'PENJELASAN') !== false || strpos($jenis, 'SOLUSI') !== false);
            $isKunci = (strpos($jenis, 'KUNCI') !== false || strpos($jenis, 'JWB') !== false);

            if ($isSoal) {
                if ($currentQuestion) {
                    $rawQuestions[] = [
                        'q' => $currentQuestion,
                        'options' => $options
                    ];
                }

                $currentQuestion = [
                    'soal' => $isi,
                    'question_image' => null,
                    'tingkat_kesulitan' => 'sedang',
                    'pembahasan' => '',
                    'explanation_image' => null,
                    'has_explanation' => false,
                ];
                $options = [];
                $lastState = 'soal';
            } elseif ($isJawaban && $currentQuestion) {
                $score = ($jawaban !== '') ? (int)$jawaban : 0;
                $options[] = [
                    'text' => $isi,
                    'image' => null,
                    'score' => $score
                ];
                $lastState = 'opsi';
            } elseif ($isPembahasan && $currentQuestion) {
                $currentQuestion['pembahasan'] = $isi;
                $currentQuestion['has_explanation'] = true;
                $lastState = 'pembahasan';
            } elseif ($isKunci && $currentQuestion) {
                $kunciVal = strtoupper($cleanText($isi));
                if (preg_match('/([A-E])/i', $kunciVal, $matches)) {
                    $currentQuestion['jawaban_benar'] = $matches[1];
                }
                $lastState = 'kunci';
            }
        }

        // Finalize last parsed question
        if ($currentQuestion) {
            $rawQuestions[] = [
                'q' => $currentQuestion,
                'options' => $options
            ];
        }

        // 4. Map extracted images to raw questions using our smart slot-priority heuristic
        if (!empty($extractedImages) && !empty($rawQuestions)) {
            $slots = [];
            
            foreach ($rawQuestions as $qIdx => $raw) {
                $q = $raw['q'];
                
                // Soal image slot
                $slots[] = [
                    'q_idx' => $qIdx,
                    'type' => 'question_image',
                    'text' => $q['soal'] ?? '',
                    'priority' => (empty($q['soal']) ? 1 : 2),
                    'opt_idx' => null,
                    'seq' => count($slots)
                ];
                
                // Option image slots
                foreach ($raw['options'] as $optIdx => $opt) {
                    $slots[] = [
                        'q_idx' => $qIdx,
                        'type' => 'option_image',
                        'text' => $opt['text'] ?? '',
                        'priority' => (empty($opt['text']) ? 1 : 3),
                        'opt_idx' => $optIdx,
                        'seq' => count($slots)
                    ];
                }
                
                // Explanation image slot
                if ($q['has_explanation']) {
                    $slots[] = [
                        'q_idx' => $qIdx,
                        'type' => 'explanation_image',
                        'text' => $q['pembahasan'] ?? '',
                        'priority' => (empty($q['pembahasan']) ? 1 : 2),
                        'opt_idx' => null,
                        'seq' => count($slots)
                    ];
                }
            }
            
            // Sort slots: priority first (1 is highest), then seq (original order)
            usort($slots, function($a, $b) {
                if ($a['priority'] !== $b['priority']) {
                    return $a['priority'] - $b['priority'];
                }
                return $a['seq'] - $b['seq'];
            });
            
            // Assign images to slots in sorted order
            $imgCount = count($extractedImages);
            for ($i = 0; $i < $imgCount && $i < count($slots); $i++) {
                $slot = $slots[$i];
                if ($slot['type'] === 'question_image') {
                    $rawQuestions[$slot['q_idx']]['q']['question_image'] = $extractedImages[$i];
                } elseif ($slot['type'] === 'option_image') {
                    $rawQuestions[$slot['q_idx']]['options'][$slot['opt_idx']]['image'] = $extractedImages[$i];
                } elseif ($slot['type'] === 'explanation_image') {
                    $rawQuestions[$slot['q_idx']]['q']['explanation_image'] = $extractedImages[$i];
                }
            }
        }

        // 5. Finalize the questions into database format
        foreach ($rawQuestions as $raw) {
            $questions[] = $this->finalizeTableQuestion($raw['q'], $raw['options']);
        }

        if (empty($questions)) {
            return back()->with('error', 'Tidak ada soal valid yang berhasil diproses dari file PDF. Pastikan file PDF berbasis teks dan berformat template tabel resmi.');
        }

        session([
            'temp_import_questions' => $questions,
            'temp_import_package_id' => $packageId,
            'temp_import_warnings'   => $warnings
        ]);

        $package = TryoutPackage::findOrFail($packageId);
        $group = Group::where('name', $package->group)->first();
        $codes = $group ? QuestionCode::where('group_id', $group->id)->get() : collect();

        return view('admin.questions.import_preview', compact('questions', 'package', 'group', 'codes'));
    }

    public function importWordConfirm(Request $request)
    {
        $questions = session('temp_import_questions');
        $packageId = session('temp_import_package_id');

        if (!$questions || !$packageId) {
            return redirect()->route('admin.questions.index')->with('error', 'Sesi impor telah kedaluwarsa.');
        }

        $package = TryoutPackage::findOrFail($packageId);
        $group = Group::where('name', $package->group)->first();
        
        $overrides = $request->input('q', []);

        // Validate that all questions have a category_id
        foreach ($questions as $index => $qData) {
            $override = $overrides[$index] ?? [];
            $categoryId = $override['category_id'] ?? null;
            
            if (!$categoryId) {
                \Log::warning("DOCX Import Confirm: Validasi gagal karena Kategori tidak dipilih pada Soal #" . ($index + 1));
                return back()->with('error', 'Gagal menyimpan soal: Kategori harus dipilih untuk semua soal. Pastikan data Kategori sudah dibuat di Master Data.');
            }
        }

        $destDir = public_path('storage/questions');
        if (!file_exists($destDir)) {
            if (!mkdir($destDir, 0777, true)) {
                \Log::error("DOCX Import Confirm: Gagal membuat direktori tujuan {$destDir}");
                return back()->with('error', 'Gagal menyimpan soal: direktori penyimpanan permanen tidak dapat dibuat.');
            }
        }

        $moveFile = function($tempPath) use ($destDir) {
            if (!$tempPath || !file_exists(public_path($tempPath))) {
                return null;
            }
            $filename = time() . '_' . uniqid() . '_' . basename($tempPath);
            rename(public_path($tempPath), $destDir . '/' . $filename);
            return 'storage/questions/' . $filename;
        };

        $importedCount = 0;
        $maxUrutan = $package->questions()->max('urutan') ?? 0;
        $overrides = $request->input('q', []);

        foreach ($questions as $index => $qData) {
            $override = $overrides[$index] ?? [];
            $questionCodeId = $override['question_code_id'] ?? null;
            $categoryId = $override['category_id'] ?? null;
            $subCategoryId = $override['sub_category_id'] ?? null;
            
            $jawabanBenar = $override['jawaban_benar'] ?? $qData['jawaban_benar'];
            $tingkatKesulitan = $override['tingkat_kesulitan'] ?? $qData['tingkat_kesulitan'];
            $soalText = ($override['soal'] ?? $qData['soal']) ?? '';
            $opsiA = ($override['opsi_a'] ?? $qData['opsi_a']) ?? '';
            $opsiB = ($override['opsi_b'] ?? $qData['opsi_b']) ?? '';
            $opsiC = ($override['opsi_c'] ?? $qData['opsi_c']) ?? '';
            $opsiD = ($override['opsi_d'] ?? $qData['opsi_d']) ?? '';
            $opsiE = ($override['opsi_e'] ?? $qData['opsi_e']) ?? '';
            $pembahasan = ($override['pembahasan'] ?? $qData['pembahasan']) ?? '';

            $scoreA = (int)($override['score_a'] ?? $qData['score_a'] ?? 0);
            $scoreB = (int)($override['score_b'] ?? $qData['score_b'] ?? 0);
            $scoreC = (int)($override['score_c'] ?? $qData['score_c'] ?? 0);
            $scoreD = (int)($override['score_d'] ?? $qData['score_d'] ?? 0);
            $scoreE = (int)($override['score_e'] ?? $qData['score_e'] ?? 0);

            $qImg = $moveFile($qData['question_image']);
            $optAImg = $moveFile($qData['option_a_image']);
            $optBImg = $moveFile($qData['option_b_image']);
            $optCImg = $moveFile($qData['option_c_image']);
            $optDImg = $moveFile($qData['option_d_image']);
            $optEImg = $moveFile($qData['option_e_image']);
            $expImg = $moveFile($qData['explanation_image']);

            Question::create([
                'tryout_package_id' => $package->id,
                'urutan'            => ++$maxUrutan,
                'group_id'          => $group?->id,
                'question_code_id'  => $questionCodeId,
                'category_id'       => $categoryId,
                'sub_category_id'   => $subCategoryId,
                'soal'              => $soalText,
                'image'             => $qImg,
                'question_image'    => $qImg,
                'opsi_a'            => $opsiA,
                'option_a_image'    => $optAImg,
                'opsi_b'            => $opsiB,
                'option_b_image'    => $optBImg,
                'opsi_c'            => $opsiC,
                'option_c_image'    => $optCImg,
                'opsi_d'            => $opsiD,
                'option_d_image'    => $optDImg,
                'opsi_e'            => $opsiE !== '' ? $opsiE : null,
                'option_e_image'    => $optEImg,
                'score_a'           => $scoreA,
                'score_b'           => $scoreB,
                'score_c'           => $scoreC,
                'score_d'           => $scoreD,
                'score_e'           => $scoreE,
                'jawaban_benar'     => $jawabanBenar,
                'pembahasan'        => $pembahasan,
                'explanation_image' => $expImg,
                'tingkat_kesulitan' => $tingkatKesulitan,
            ]);

            $importedCount++;
        }

        $tempImportPath = public_path('storage/temp_import');
        if (file_exists($tempImportPath)) {
            shell_exec("powershell -Command \"Remove-Item -Path '$tempImportPath\\*' -Force\"");
        }

        session()->forget(['temp_import_questions', 'temp_import_package_id', 'temp_import_warnings']);

        return redirect()->route('admin.tryouts.show', $package)->with('success', "$importedCount soal berhasil diimpor ke paket {$package->nama}.");
    }

    public function importProcess(Request $request)
    {
        return back()->with('error', 'Silakan gunakan modul impor DOCX baru.');
    }
}
