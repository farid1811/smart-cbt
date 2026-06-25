<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Question;
use App\Models\Group;
use App\Models\QuestionCode;
use App\Models\Category;
use App\Models\TryoutPackage;
use Illuminate\Http\Request;

class QuestionController extends Controller
{
    public function index(Request $request)
    {
        $query = Question::with(['group', 'questionCode', 'category', 'tryoutPackage']);

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

        if ($request->filled('search')) {
            $query->where('soal', 'like', '%' . $request->search . '%');
        }

        $questions = $query->orderBy('tryout_package_id')->orderBy('urutan')->paginate(25);
        $groups = Group::all();
        $packages = TryoutPackage::all();
        $categories = Category::with(['questionCode.group'])->get();

        return view('admin.questions.index', compact('questions', 'groups', 'packages', 'categories'));
    }

    public function create(Request $request)
    {
        $groups = Group::all();
        $packages = TryoutPackage::all();
        $defaultPackageId = $request->input('tryout_package_id');
        $selectedPackage = $defaultPackageId ? TryoutPackage::find($defaultPackageId) : null;

        return view('admin.questions.create', compact('groups', 'packages', 'selectedPackage', 'defaultPackageId'));
    }

    private function handleImageUpload(Request $request, $fieldName, $existingPath = null, $deleteRequested = false)
    {
        if ($deleteRequested) {
            if ($existingPath && file_exists(public_path($existingPath))) {
                @unlink(public_path($existingPath));
            }
            return null;
        }

        if ($request->hasFile($fieldName)) {
            if ($existingPath && file_exists(public_path($existingPath))) {
                @unlink(public_path($existingPath));
            }
            $file = $request->file($fieldName);
            $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('storage/questions'), $filename);
            return 'storage/questions/' . $filename;
        }

        return $existingPath;
    }

    public function store(Request $request)
    {
        $rules = [
            'tryout_package_id' => 'required|exists:tryout_packages,id',
            'group_id'          => 'required|exists:groups,id',
            'question_code_id'  => 'required|exists:question_codes,id',
            'category_id'       => 'required|exists:categories,id',
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
        $packages = TryoutPackage::all();
        return view('admin.questions.edit', compact('question', 'groups', 'codes', 'categories', 'packages'));
    }

    public function update(Request $request, Question $question)
    {
        $rules = [
            'tryout_package_id' => 'required|exists:tryout_packages,id',
            'group_id'          => 'required|exists:groups,id',
            'question_code_id'  => 'required|exists:question_codes,id',
            'category_id'       => 'required|exists:categories,id',
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

        $hasQuestionImage = $request->hasFile('question_image') || ($question->question_image && !$request->boolean('hapus_question_image'));
        $rules['soal'] = $hasQuestionImage ? 'nullable|string' : 'required|string';

        $hasOpsiAImage = $request->hasFile('option_a_image') || ($question->option_a_image && !$request->boolean('hapus_option_a_image'));
        $rules['opsi_a'] = $hasOpsiAImage ? 'nullable|string' : 'required|string';

        $hasOpsiBImage = $request->hasFile('option_b_image') || ($question->option_b_image && !$request->boolean('hapus_option_b_image'));
        $rules['opsi_b'] = $hasOpsiBImage ? 'nullable|string' : 'required|string';

        $hasOpsiCImage = $request->hasFile('option_c_image') || ($question->option_c_image && !$request->boolean('hapus_option_c_image'));
        $rules['opsi_c'] = $hasOpsiCImage ? 'nullable|string' : 'required|string';

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
}
