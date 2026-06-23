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
        if ($request->filled('search')) {
            $query->where('soal', 'like', '%' . $request->search . '%');
        }

        $questions = $query->latest()->paginate(15)->withQueryString();
        
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
        $validated = $request->validate([
            'tryout_package_id' => 'required|exists:tryout_packages,id',
            'group_id'          => 'required|exists:groups,id',
            'question_code_id'  => 'required|exists:question_codes,id',
            'category_id'       => 'required|exists:categories,id',
            'sub_category_id'   => 'required|exists:sub_categories,id',
            'soal'              => 'required|string',
            'question_image'    => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048',
            'opsi_a'            => 'required|string',
            'option_a_image'    => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048',
            'opsi_b'            => 'required|string',
            'option_b_image'    => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048',
            'opsi_c'            => 'required|string',
            'option_c_image'    => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048',
            'opsi_d'            => 'required|string',
            'option_d_image'    => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048',
            'opsi_e'            => 'nullable|string',
            'option_e_image'    => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048',
            'jawaban_benar'     => 'required|in:A,B,C,D,E',
            'pembahasan'        => 'nullable|string',
            'explanation_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048',
            'tingkat_kesulitan' => 'required|in:mudah,sedang,sulit',
        ]);

        $data = $validated;
        
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
        $validated = $request->validate([
            'tryout_package_id' => 'required|exists:tryout_packages,id',
            'group_id'          => 'required|exists:groups,id',
            'question_code_id'  => 'required|exists:question_codes,id',
            'category_id'       => 'required|exists:categories,id',
            'sub_category_id'   => 'required|exists:sub_categories,id',
            'soal'              => 'required|string',
            'question_image'    => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048',
            'opsi_a'            => 'required|string',
            'option_a_image'    => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048',
            'opsi_b'            => 'required|string',
            'option_b_image'    => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048',
            'opsi_c'            => 'required|string',
            'option_c_image'    => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048',
            'opsi_d'            => 'required|string',
            'option_d_image'    => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048',
            'opsi_e'            => 'nullable|string',
            'option_e_image'    => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048',
            'jawaban_benar'     => 'required|in:A,B,C,D,E',
            'pembahasan'        => 'nullable|string',
            'explanation_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048',
            'tingkat_kesulitan' => 'required|in:mudah,sedang,sulit',
        ]);

        $data = $validated;

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
            'file' => 'required|file|mimes:docx,zip',
            'tryout_package_id' => 'required|exists:tryout_packages,id',
        ]);

        $packageId = $request->tryout_package_id;
        $file = $request->file('file');

        $zipDir = storage_path('app/temp_import_' . uniqid());
        if (!file_exists($zipDir)) {
            mkdir($zipDir, 0777, true);
        }

        $escapedDocx = escapeshellarg($file->getRealPath());
        $escapedDest = escapeshellarg($zipDir);
        $cmd = "powershell -Command \"Expand-Archive -Path $escapedDocx -DestinationPath $escapedDest -Force\"";
        shell_exec($cmd);

        $docXmlPath = $zipDir . '/word/document.xml';
        $relsXmlPath = $zipDir . '/word/_rels/document.xml.rels';

        if (!file_exists($docXmlPath)) {
            shell_exec("powershell -Command \"Remove-Item -Path $escapedDest -Recurse -Force\"");
            return back()->with('error', 'Format file Word tidak valid (tidak ditemukan word/document.xml).');
        }

        // Map relationship IDs
        $rels = [];
        if (file_exists($relsXmlPath)) {
            $relsXml = simplexml_load_file($relsXmlPath);
            foreach ($relsXml->Relationship as $rel) {
                $id = (string)$rel['Id'];
                $target = (string)$rel['Target'];
                $rels[$id] = $target;
            }
        }

        $dom = new \DOMDocument();
        libxml_use_internal_errors(true);
        $dom->load($docXmlPath);
        libxml_clear_errors();

        $xpath = new \DOMXPath($dom);
        $xpath->registerNamespace('w', 'http://schemas.openxmlformats.org/wordprocessingml/2006/main');
        $xpath->registerNamespace('a', 'http://schemas.openxmlformats.org/drawingml/2006/main');
        $xpath->registerNamespace('r', 'http://schemas.openxmlformats.org/officeDocument/2006/relationships');

        $paragraphs = $xpath->query('//w:p');

        $questions = [];
        $currentQuestion = null;
        $state = 'none';
        $warnings = [];

        foreach ($paragraphs as $p) {
            $textNodes = $xpath->query('.//w:t', $p);
            $text = '';
            foreach ($textNodes as $tNode) {
                $text .= $tNode->nodeValue;
            }

            // Extract image (multiple image parser - parses all images inside drawing nodes)
            $drawingNodes = $xpath->query('.//a:blip', $p);
            $imagePath = null;
            if ($drawingNodes->length > 0) {
                $blip = $drawingNodes->item(0);
                $embedId = $blip->getAttribute('r:embed');
                if (!$embedId) {
                    $embedId = $blip->getAttributeNS('http://schemas.openxmlformats.org/officeDocument/2006/relationships', 'embed');
                }

                if ($embedId && isset($rels[$embedId])) {
                    $relTarget = $rels[$embedId];
                    $sourceImagePath = $zipDir . '/word/' . $relTarget;
                    if (file_exists($sourceImagePath)) {
                        $tempFilename = time() . '_' . uniqid() . '_' . basename($relTarget);
                        $tempDestDir = public_path('storage/temp_import');
                        if (!file_exists($tempDestDir)) {
                            mkdir($tempDestDir, 0777, true);
                        }
                        try {
                            copy($sourceImagePath, $tempDestDir . '/' . $tempFilename);
                            $imagePath = 'storage/temp_import/' . $tempFilename;
                        } catch (\Exception $e) {
                            $warnings[] = "Gagal menyalin gambar " . basename($relTarget) . ": " . $e->getMessage();
                        }
                    }
                }
            }

            $trimmed = trim($text);
            if ($trimmed === '' && !$imagePath) {
                continue;
            }

            if (preg_match('/^(?:SOAL\s*\d*[:\.]|\[SOAL\]|SOAL)\s*(.*)/i', $trimmed, $matches)) {
                if ($currentQuestion) {
                    $questions[] = $currentQuestion;
                }
                $currentQuestion = [
                    'soal' => $matches[1],
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
                if (preg_match('/^(?:A[:\.\s\-]|\[A\])\s*(.*)/i', $trimmed, $matches)) {
                    $currentQuestion['opsi_a'] = $matches[1];
                    if ($imagePath) $currentQuestion['option_a_image'] = $imagePath;
                    $state = 'opsi_a';
                    continue;
                }
                if (preg_match('/^(?:B[:\.\s\-]|\[B\])\s*(.*)/i', $trimmed, $matches)) {
                    $currentQuestion['opsi_b'] = $matches[1];
                    if ($imagePath) $currentQuestion['option_b_image'] = $imagePath;
                    $state = 'opsi_b';
                    continue;
                }
                if (preg_match('/^(?:C[:\.\s\-]|\[C\])\s*(.*)/i', $trimmed, $matches)) {
                    $currentQuestion['opsi_c'] = $matches[1];
                    if ($imagePath) $currentQuestion['option_c_image'] = $imagePath;
                    $state = 'opsi_c';
                    continue;
                }
                if (preg_match('/^(?:D[:\.\s\-]|\[D\])\s*(.*)/i', $trimmed, $matches)) {
                    $currentQuestion['opsi_d'] = $matches[1];
                    if ($imagePath) $currentQuestion['option_d_image'] = $imagePath;
                    $state = 'opsi_d';
                    continue;
                }
                if (preg_match('/^(?:E[:\.\s\-]|\[E\])\s*(.*)/i', $trimmed, $matches)) {
                    $currentQuestion['opsi_e'] = $matches[1];
                    if ($imagePath) $currentQuestion['option_e_image'] = $imagePath;
                    $state = 'opsi_e';
                    continue;
                }
                if (preg_match('/^(?:KUNCI|JAWABAN|KUNCI\s+JAWABAN)[:\.\s\-]*\s*([A-E])/i', $trimmed, $matches)) {
                    $currentQuestion['jawaban_benar'] = strtoupper($matches[1]);
                    $state = 'kunci';
                    continue;
                }
                if (preg_match('/^(?:PEMBAHASAN)[:\.\s\-]\s*(.*)/i', $trimmed, $matches)) {
                    $currentQuestion['pembahasan'] = $matches[1];
                    if ($imagePath) $currentQuestion['explanation_image'] = $imagePath;
                    $state = 'pembahasan';
                    continue;
                }

                if ($state === 'soal') {
                    $currentQuestion['soal'] .= ($currentQuestion['soal'] !== '' ? "\n" : '') . $text;
                    if ($imagePath) $currentQuestion['question_image'] = $imagePath;
                } elseif ($state === 'opsi_a') {
                    $currentQuestion['opsi_a'] .= ($currentQuestion['opsi_a'] !== '' ? "\n" : '') . $text;
                    if ($imagePath) $currentQuestion['option_a_image'] = $imagePath;
                } elseif ($state === 'opsi_b') {
                    $currentQuestion['opsi_b'] .= ($currentQuestion['opsi_b'] !== '' ? "\n" : '') . $text;
                    if ($imagePath) $currentQuestion['option_b_image'] = $imagePath;
                } elseif ($state === 'opsi_c') {
                    $currentQuestion['opsi_c'] .= ($currentQuestion['opsi_c'] !== '' ? "\n" : '') . $text;
                    if ($imagePath) $currentQuestion['option_c_image'] = $imagePath;
                } elseif ($state === 'opsi_d') {
                    $currentQuestion['opsi_d'] .= ($currentQuestion['opsi_d'] !== '' ? "\n" : '') . $text;
                    if ($imagePath) $currentQuestion['option_d_image'] = $imagePath;
                } elseif ($state === 'opsi_e') {
                    $currentQuestion['opsi_e'] .= ($currentQuestion['opsi_e'] !== '' ? "\n" : '') . $text;
                    if ($imagePath) $currentQuestion['option_e_image'] = $imagePath;
                } elseif ($state === 'pembahasan') {
                    $currentQuestion['pembahasan'] .= ($currentQuestion['pembahasan'] !== '' ? "\n" : '') . $text;
                    if ($imagePath) $currentQuestion['explanation_image'] = $imagePath;
                }
            }
        }

        if ($currentQuestion) {
            $questions[] = $currentQuestion;
        }

        shell_exec("powershell -Command \"Remove-Item -Path $escapedDest -Recurse -Force\"");

        if (empty($questions)) {
            return back()->with('error', 'Tidak ada soal valid yang berhasil diproses dari file Word.');
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
        // Check if OCR is available/Tesseract is on path (system command where or which)
        $hasTesseract = false;
        $tesseractCheck = shell_exec('where tesseract 2>&1');
        if ($tesseractCheck && str_contains($tesseractCheck, 'tesseract.exe')) {
            $hasTesseract = true;
        } else {
            $warnings[] = "OCR engine (Tesseract) tidak terdeteksi di server. Halaman PDF berupa gambar/hasil scan mungkin tidak akan terbaca teksnya secara otomatis.";
        }

        // Extract all images globally first
        $tempDestDir = public_path('storage/temp_import');
        if (!file_exists($tempDestDir)) {
            mkdir($tempDestDir, 0777, true);
        }

        $extractedImages = [];
        try {
            $objects = $pdf->getObjects();
            foreach ($objects as $key => $object) {
                if ($object instanceof \Smalot\PdfParser\Object && $object->getHeader()->get('Subtype') == 'Image') {
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

        // Extract text page by page
        $fullText = '';
        try {
            $pages = $pdf->getPages();
            foreach ($pages as $page) {
                $fullText .= $page->getText() . "\n";
            }
        } catch (\Exception $e) {
            $fullText = $pdf->getText();
        }

        // Now parse the text using the state machine
        $lines = explode("\n", $fullText);
        $questions = [];
        $currentQuestion = null;
        $state = 'none';
        $imgIndex = 0;

        foreach ($lines as $line) {
            $trimmed = trim($line);
            if ($trimmed === '') {
                continue;
            }

            // Check for new question
            if (preg_match('/^(?:SOAL\s*\d*[:\.]|\[SOAL\]|SOAL)\s*(.*)/i', $trimmed, $matches)) {
                if ($currentQuestion) {
                    $questions[] = $currentQuestion;
                }
                $currentQuestion = [
                    'soal' => $matches[1],
                    'question_image' => null,
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

                // Auto-attach extracted image if available
                if (isset($extractedImages[$imgIndex])) {
                    $currentQuestion['question_image'] = $extractedImages[$imgIndex++];
                }
                continue;
            }

            if ($currentQuestion) {
                if (preg_match('/^(?:A[:\.\s\-]|\[A\])\s*(.*)/i', $trimmed, $matches)) {
                    $currentQuestion['opsi_a'] = $matches[1];
                    $state = 'opsi_a';
                    continue;
                }
                if (preg_match('/^(?:B[:\.\s\-]|\[B\])\s*(.*)/i', $trimmed, $matches)) {
                    $currentQuestion['opsi_b'] = $matches[1];
                    $state = 'opsi_b';
                    continue;
                }
                if (preg_match('/^(?:C[:\.\s\-]|\[C\])\s*(.*)/i', $trimmed, $matches)) {
                    $currentQuestion['opsi_c'] = $matches[1];
                    $state = 'opsi_c';
                    continue;
                }
                if (preg_match('/^(?:D[:\.\s\-]|\[D\])\s*(.*)/i', $trimmed, $matches)) {
                    $currentQuestion['opsi_d'] = $matches[1];
                    $state = 'opsi_d';
                    continue;
                }
                if (preg_match('/^(?:E[:\.\s\-]|\[E\])\s*(.*)/i', $trimmed, $matches)) {
                    $currentQuestion['opsi_e'] = $matches[1];
                    $state = 'opsi_e';
                    continue;
                }
                if (preg_match('/^(?:KUNCI|JAWABAN|KUNCI\s+JAWABAN)[:\.\s\-]*\s*([A-E])/i', $trimmed, $matches)) {
                    $currentQuestion['jawaban_benar'] = strtoupper($matches[1]);
                    $state = 'kunci';
                    continue;
                }
                if (preg_match('/^(?:PEMBAHASAN)[:\.\s\-]\s*(.*)/i', $trimmed, $matches)) {
                    $currentQuestion['pembahasan'] = $matches[1];
                    $state = 'pembahasan';
                    continue;
                }

                // Append text based on state
                if ($state === 'soal') {
                    $currentQuestion['soal'] .= ($currentQuestion['soal'] !== '' ? "\n" : '') . $line;
                } elseif ($state === 'opsi_a') {
                    $currentQuestion['opsi_a'] .= ($currentQuestion['opsi_a'] !== '' ? "\n" : '') . $line;
                } elseif ($state === 'opsi_b') {
                    $currentQuestion['opsi_b'] .= ($currentQuestion['opsi_b'] !== '' ? "\n" : '') . $line;
                } elseif ($state === 'opsi_c') {
                    $currentQuestion['opsi_c'] .= ($currentQuestion['opsi_c'] !== '' ? "\n" : '') . $line;
                } elseif ($state === 'opsi_d') {
                    $currentQuestion['opsi_d'] .= ($currentQuestion['opsi_d'] !== '' ? "\n" : '') . $line;
                } elseif ($state === 'opsi_e') {
                    $currentQuestion['opsi_e'] .= ($currentQuestion['opsi_e'] !== '' ? "\n" : '') . $line;
                } elseif ($state === 'pembahasan') {
                    $currentQuestion['pembahasan'] .= ($currentQuestion['pembahasan'] !== '' ? "\n" : '') . $line;
                }
            }
        }

        if ($currentQuestion) {
            $questions[] = $currentQuestion;
        }

        if (empty($questions)) {
            return back()->with('error', 'Tidak ada soal valid yang berhasil diproses dari file PDF. Pastikan file PDF berbasis teks dan berformat standar.');
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
        
        $destDir = public_path('storage/questions');
        if (!file_exists($destDir)) {
            mkdir($destDir, 0777, true);
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
            $soalText = $override['soal'] ?? $qData['soal'];
            $opsiA = $override['opsi_a'] ?? $qData['opsi_a'];
            $opsiB = $override['opsi_b'] ?? $qData['opsi_b'];
            $opsiC = $override['opsi_c'] ?? $qData['opsi_c'];
            $opsiD = $override['opsi_d'] ?? $qData['opsi_d'];
            $opsiE = $override['opsi_e'] ?? $qData['opsi_e'];
            $pembahasan = $override['pembahasan'] ?? $qData['pembahasan'];

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
