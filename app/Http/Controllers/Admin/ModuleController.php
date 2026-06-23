<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LearningModule;
use App\Models\Group;
use App\Models\QuestionCode;
use App\Models\Category;
use App\Models\SubCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ModuleController extends Controller
{
    public function index(Request $request)
    {
        $query = LearningModule::with(['group', 'questionCode', 'category', 'subCategory']);

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
        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        $modules = $query->latest()->paginate(15)->withQueryString();
        $groups = Group::all();

        return view('admin.modules.index', compact('modules', 'groups'));
    }

    public function create()
    {
        $groups = Group::all();
        return view('admin.modules.create', compact('groups'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'group_id'          => 'required|exists:groups,id',
            'question_code_id'  => 'required|exists:question_codes,id',
            'category_id'       => 'required|exists:categories,id',
            'sub_category_id'   => 'required|exists:sub_categories,id',
            'name'              => 'required|string|max:255',
            'description'       => 'nullable|string',
            'pdf_file'          => 'nullable|file|mimes:pdf|max:10240',
            'video_url'         => 'nullable|string|max:255',
            'is_active'         => 'nullable|boolean',
        ]);

        $data = $validated;
        $data['is_active'] = $request->boolean('is_active', true);

        if ($request->hasFile('pdf_file')) {
            $file = $request->file('pdf_file');
            $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $file->storeAs('public/modules', $filename);
            $data['pdf_file'] = 'storage/modules/' . $filename;
        }

        LearningModule::create($data);

        return redirect()->route('admin.modules.index')->with('success', 'Modul pembelajaran berhasil ditambahkan.');
    }

    public function edit(LearningModule $module)
    {
        $groups = Group::all();
        $codes = QuestionCode::where('group_id', $module->group_id)->get();
        $categories = Category::where('question_code_id', $module->question_code_id)->get();
        $subCategories = SubCategory::where('category_id', $module->category_id)->get();
        
        return view('admin.modules.edit', compact('module', 'groups', 'codes', 'categories', 'subCategories'));
    }

    public function update(Request $request, LearningModule $module)
    {
        $validated = $request->validate([
            'group_id'          => 'required|exists:groups,id',
            'question_code_id'  => 'required|exists:question_codes,id',
            'category_id'       => 'required|exists:categories,id',
            'sub_category_id'   => 'required|exists:sub_categories,id',
            'name'              => 'required|string|max:255',
            'description'       => 'nullable|string',
            'pdf_file'          => 'nullable|file|mimes:pdf|max:10240',
            'video_url'         => 'nullable|string|max:255',
            'is_active'         => 'nullable|boolean',
        ]);

        $data = $validated;
        $data['is_active'] = $request->boolean('is_active', true);

        if ($request->boolean('hapus_pdf') || $request->hasFile('pdf_file')) {
            if ($module->pdf_file) {
                $oldPath = str_replace('storage/', 'public/', $module->pdf_file);
                Storage::delete($oldPath);
            }
            $data['pdf_file'] = null;
        }

        if ($request->hasFile('pdf_file')) {
            $file = $request->file('pdf_file');
            $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $file->storeAs('public/modules', $filename);
            $data['pdf_file'] = 'storage/modules/' . $filename;
        } else {
            if (!$request->boolean('hapus_pdf')) {
                unset($data['pdf_file']);
            }
        }

        $module->update($data);

        return redirect()->route('admin.modules.index')->with('success', 'Modul pembelajaran berhasil diperbarui.');
    }

    public function destroy(LearningModule $module)
    {
        if ($module->pdf_file) {
            $oldPath = str_replace('storage/', 'public/', $module->pdf_file);
            Storage::delete($oldPath);
        }
        $module->delete();

        return redirect()->route('admin.modules.index')->with('success', 'Modul pembelajaran berhasil dihapus.');
    }
}
