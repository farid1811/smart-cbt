<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Group;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::with('group')->withCount('questions')->get();
        return view('admin.categories.index', compact('categories'));
    }

    public function create()
    {
        $groups = Group::all();
        return view('admin.categories.create', compact('groups'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'      => 'required|string|max:100',
            'kode'      => 'required|string|max:10|unique:categories,kode',
            'group_id'  => 'required|exists:groups,id',
            'deskripsi' => 'nullable|string',
        ], [
            'name.required'     => 'Nama kategori wajib diisi.',
            'kode.required'     => 'Kode kategori wajib diisi.',
            'kode.unique'       => 'Kode kategori sudah digunakan.',
            'group_id.required' => 'Grup wajib dipilih.',
            'group_id.exists'   => 'Grup tidak valid.',
        ]);

        Category::create($validated);
        return redirect()->route('admin.categories.index')->with('success', 'Kategori berhasil ditambahkan.');
    }

    public function edit(Category $category)
    {
        $groups = Group::all();
        return view('admin.categories.edit', compact('category', 'groups'));
    }

    public function update(Request $request, Category $category)
    {
        $validated = $request->validate([
            'name'      => 'required|string|max:100',
            'kode'      => 'required|string|max:10|unique:categories,kode,' . $category->id,
            'group_id'  => 'required|exists:groups,id',
            'deskripsi' => 'nullable|string',
        ], [
            'name.required'     => 'Nama kategori wajib diisi.',
            'kode.required'     => 'Kode kategori wajib diisi.',
            'kode.unique'       => 'Kode kategori sudah digunakan.',
            'group_id.required' => 'Grup wajib dipilih.',
            'group_id.exists'   => 'Grup tidak valid.',
        ]);

        $category->update($validated);
        return redirect()->route('admin.categories.index')->with('success', 'Kategori berhasil diperbarui.');
    }

    public function destroy(Category $category)
    {
        if ($category->questions()->count() > 0) {
            return back()->with('error', 'Kategori tidak bisa dihapus karena masih memiliki soal.');
        }
        $category->delete();
        return redirect()->route('admin.categories.index')->with('success', 'Kategori berhasil dihapus.');
    }
}
