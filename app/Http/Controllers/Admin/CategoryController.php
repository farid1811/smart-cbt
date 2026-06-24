<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Group;
use App\Models\QuestionCode;
use App\Models\Category;
use App\Models\SubCategory;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    // API Dropdown Dinamis
    public function getGroups()
    {
        return response()->json(Group::all());
    }

    public function getCodesByGroup($groupId)
    {
        $codes = QuestionCode::where('group_id', $groupId)->get();
        return response()->json($codes);
    }

    public function getCategoriesByCode($codeId)
    {
        $categories = Category::where('question_code_id', $codeId)->get();
        return response()->json($categories);
    }

    public function getSubCategoriesByCategory($categoryId)
    {
        $subCategories = SubCategory::where('category_id', $categoryId)->get();
        return response()->json($subCategories);
    }

    // Unified List View (Tabbed)
    public function index()
    {
        $groups = Group::all();
        $codes = QuestionCode::with('group')->get();
        $categories = Category::with(['questionCode.group', 'subCategories'])->get();

        return view('admin.categories.index', compact('groups', 'codes', 'categories'));
    }

    // Question Code Store/Update/Destroy
    public function storeCode(Request $request)
    {
        $validated = $request->validate([
            'group_id' => 'required|exists:groups,id',
            'name'     => 'required|string|max:100',
            'code'     => 'required|string|max:20|unique:question_codes,code',
        ]);

        QuestionCode::create($validated);
        return redirect()->route('admin.categories.index', ['tab' => 'code'])->with('success', 'Kode soal berhasil ditambahkan.');
    }

    public function updateCode(Request $request, QuestionCode $code)
    {
        $validated = $request->validate([
            'group_id' => 'required|exists:groups,id',
            'name'     => 'required|string|max:100',
            'code'     => 'required|string|max:20|unique:question_codes,code,' . $code->id,
        ]);

        $code->update($validated);
        return redirect()->route('admin.categories.index', ['tab' => 'code'])->with('success', 'Kode soal berhasil diperbarui.');
    }

    public function destroyCode(QuestionCode $code)
    {
        if ($code->categories()->count() > 0 || $code->questions()->count() > 0) {
            return back()->with('error', 'Kode soal tidak bisa dihapus karena masih digunakan.');
        }
        $code->delete();
        return redirect()->route('admin.categories.index', ['tab' => 'code'])->with('success', 'Kode soal berhasil dihapus.');
    }

    // Category Store/Update/Destroy (Resource Overrides)
    public function store(Request $request)
    {
        $validated = $request->validate([
            'question_code_id' => 'required|exists:question_codes,id',
            'name'             => 'required|string|max:100',
        ]);

        Category::create($validated);
        return redirect()->route('admin.categories.index', ['tab' => 'category'])->with('success', 'Kategori berhasil ditambahkan.');
    }

    public function update(Request $request, Category $category)
    {
        $validated = $request->validate([
            'question_code_id' => 'required|exists:question_codes,id',
            'name'             => 'required|string|max:100',
        ]);

        $category->update($validated);
        return redirect()->route('admin.categories.index', ['tab' => 'category'])->with('success', 'Kategori berhasil diperbarui.');
    }

    public function destroy(Category $category)
    {
        if ($category->subCategories()->count() > 0 || $category->questions()->count() > 0) {
            return back()->with('error', 'Kategori tidak bisa dihapus karena masih digunakan.');
        }
        $category->delete();
        return redirect()->route('admin.categories.index', ['tab' => 'category'])->with('success', 'Kategori berhasil dihapus.');
    }

    // Sub Category Store/Update/Destroy
    public function storeSubCategory(Request $request)
    {
        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'name'        => 'required|string|max:100',
        ]);

        SubCategory::create($validated);
        return redirect()->route('admin.categories.index', ['tab' => 'category'])->with('success', 'Sub Kategori berhasil ditambahkan.');
    }

    public function updateSubCategory(Request $request, SubCategory $subcategory)
    {
        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'name'        => 'required|string|max:100',
        ]);

        $subcategory->update($validated);
        return redirect()->route('admin.categories.index', ['tab' => 'category'])->with('success', 'Sub Kategori berhasil diperbarui.');
    }

    public function destroySubCategory(SubCategory $subcategory)
    {
        if ($subcategory->questions()->count() > 0) {
            return back()->with('error', 'Sub Kategori tidak bisa dihapus karena masih digunakan oleh soal.');
        }
        $subcategory->delete();
        return redirect()->route('admin.categories.index', ['tab' => 'category'])->with('success', 'Sub Kategori berhasil dihapus.');
    }
}

