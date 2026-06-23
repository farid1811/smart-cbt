<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Group;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class PesertaController extends Controller
{
    /**
     * Daftar semua peserta.
     */
    public function index(Request $request)
    {
        $query = User::where('role', 'peserta')->with(['group', 'assignedPackage']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('username', 'like', "%{$search}%")
                  ->orWhere('no_peserta', 'like', "%{$search}%")
                  ->orWhere('no_hp', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'aktif' ? 1 : 0);
        }

        if ($request->filled('group_id')) {
            $query->where('group_id', $request->group_id);
        }

        $peserta = $query->withCount('examSessions')->latest()->paginate(20);
        $groups = \App\Models\Group::all();

        return view('admin.peserta.index', compact('peserta', 'groups'));
    }

    /**
     * Form tambah peserta.
     */
    public function create()
    {
        $groups = \App\Models\Group::all();
        $packages = \App\Models\TryoutPackage::where('is_active', true)->get();
        return view('admin.peserta.create', compact('groups', 'packages'));
    }

    /**
     * Simpan peserta baru.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'       => 'required|string|max:100',
            'email'      => 'nullable|email|unique:users,email|max:150',
            'username'   => 'required|string|max:100|unique:users,username',
            'no_peserta' => 'nullable|string|max:30|unique:users,no_peserta',
            'no_hp'      => 'nullable|string|max:20',
            'password'   => ['required', 'confirmed', \Illuminate\Validation\Rules\Password::min(6)],
            'group_id'   => 'required|exists:groups,id',
            'category'   => 'required|string|max:50',
            'assigned_package_id' => 'nullable|exists:tryout_packages,id',
            'is_active'  => 'nullable|boolean',
        ], [
            'name.required'       => 'Nama wajib diisi.',
            'email.email'         => 'Format email tidak valid.',
            'email.unique'        => 'Email sudah digunakan oleh peserta lain.',
            'username.required'   => 'Username wajib diisi.',
            'username.unique'     => 'Username sudah digunakan.',
            'no_peserta.unique'   => 'Nomor peserta sudah digunakan.',
            'password.required'   => 'Password wajib diisi.',
            'password.confirmed'  => 'Konfirmasi password tidak cocok.',
            'password.min'        => 'Password minimal 6 karakter.',
            'group_id.required'   => 'Grup wajib dipilih.',
            'group_id.exists'     => 'Grup tidak valid.',
            'category.required'   => 'Kategori wajib dipilih.',
        ]);

        User::create([
            'name'       => $validated['name'],
            'email'      => $validated['email'] ?? null,
            'username'   => $validated['username'],
            'no_peserta' => $validated['no_peserta'] ?? null,
            'no_hp'      => $validated['no_hp'] ?? null,
            'password'   => Hash::make($validated['password']),
            'role'       => 'peserta',
            'group_id'   => $validated['group_id'],
            'category'   => $validated['category'],
            'assigned_package_id' => $validated['assigned_package_id'] ?? null,
            'is_active'  => $request->boolean('is_active', true),
        ]);

        return redirect()->route('admin.peserta.index')
            ->with('success', "Peserta {$validated['name']} berhasil ditambahkan.");
    }

    /**
     * Form edit peserta.
     */
    public function edit(User $peserta)
    {
        abort_if($peserta->role !== 'peserta', 404);
        $groups = \App\Models\Group::all();
        $packages = \App\Models\TryoutPackage::where('is_active', true)->get();

        return view('admin.peserta.edit', compact('peserta', 'groups', 'packages'));
    }

    /**
     * Update data peserta.
     */
    public function update(Request $request, User $peserta)
    {
        abort_if($peserta->role !== 'peserta', 404);

        $validated = $request->validate([
            'name'       => 'required|string|max:100',
            'email'      => "nullable|email|unique:users,email,{$peserta->id}|max:150",
            'username'   => "required|string|max:100|unique:users,username,{$peserta->id}",
            'no_peserta' => "nullable|string|max:30|unique:users,no_peserta,{$peserta->id}",
            'no_hp'      => 'nullable|string|max:20',
            'group_id'   => 'required|exists:groups,id',
            'category'   => 'required|string|max:50',
            'assigned_package_id' => 'nullable|exists:tryout_packages,id',
            'is_active'  => 'nullable|boolean',
        ], [
            'name.required'     => 'Nama wajib diisi.',
            'email.email'       => 'Format email tidak valid.',
            'email.unique'      => 'Email sudah digunakan oleh peserta lain.',
            'username.required' => 'Username wajib diisi.',
            'username.unique'   => 'Username sudah digunakan.',
            'no_peserta.unique' => 'Nomor peserta sudah digunakan.',
            'group_id.required' => 'Grup wajib dipilih.',
            'group_id.exists'   => 'Grup tidak valid.',
            'category.required' => 'Kategori wajib dipilih.',
        ]);

        $peserta->update([
            'name'       => $validated['name'],
            'email'      => $validated['email'] ?? null,
            'username'   => $validated['username'],
            'no_peserta' => $validated['no_peserta'] ?? null,
            'no_hp'      => $validated['no_hp'] ?? null,
            'group_id'   => $validated['group_id'],
            'category'   => $validated['category'],
            'assigned_package_id' => $validated['assigned_package_id'] ?? null,
            'is_active'  => $request->boolean('is_active', true),
        ]);

        return redirect()->route('admin.peserta.index')
            ->with('success', "Data peserta {$peserta->name} berhasil diperbarui.");
    }

    /**
     * Hapus peserta.
     */
    public function destroy(User $peserta)
    {
        abort_if($peserta->role !== 'peserta', 404);

        $name = $peserta->name;
        $peserta->delete();

        return redirect()->route('admin.peserta.index')
            ->with('success', "Peserta {$name} berhasil dihapus.");
    }

    /**
     * Reset password peserta.
     */
    public function resetPassword(Request $request, User $peserta)
    {
        abort_if($peserta->role !== 'peserta', 404);

        $request->validate([
            'password' => ['required', 'confirmed', Password::min(6)],
        ], [
            'password.required'  => 'Password baru wajib diisi.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
            'password.min'       => 'Password minimal 6 karakter.',
        ]);

        $peserta->update([
            'password' => Hash::make($request->password),
        ]);

        return redirect()->route('admin.peserta.index')
            ->with('success', "Password peserta {$peserta->name} berhasil direset.");
    }

    /**
     * Toggle status aktif/nonaktif peserta.
     */
    public function toggleStatus(User $peserta)
    {
        abort_if($peserta->role !== 'peserta', 404);

        $peserta->update(['is_active' => !$peserta->is_active]);

        $status = $peserta->is_active ? 'diaktifkan' : 'dinonaktifkan';
        return redirect()->back()
            ->with('success', "Peserta {$peserta->name} berhasil {$status}.");
    }
}
