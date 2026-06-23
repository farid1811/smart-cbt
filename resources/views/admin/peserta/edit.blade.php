@extends('admin.layouts.app')
@section('title', 'Edit Peserta')

@section('topbar-actions')
    <a href="{{ route('admin.peserta.index') }}" class="btn btn-secondary">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="15 18 9 12 15 6"/></svg>
        Kembali
    </a>
@endsection

@section('content')
<div style="max-width:640px;margin:0 auto;display:flex;flex-direction:column;gap:1.25rem;">

    {{-- Form Edit Data --}}
    <div class="table-card" style="padding:0;">
        <div class="table-header" style="padding:1.25rem 1.5rem;border-bottom:1px solid var(--border);">
            <div style="display:flex;align-items:center;gap:0.75rem;">
                <div style="width:40px;height:40px;border-radius:50%;background:var(--primary-light);color:var(--primary);display:flex;align-items:center;justify-content:center;font-weight:700;font-size:1rem;flex-shrink:0;">
                    {{ strtoupper(substr($peserta->name, 0, 1)) }}
                </div>
                <div>
                    <h3 style="font-weight:700;font-size:1rem;margin:0 0 0.1rem;">Edit Data Peserta</h3>
                    <p style="font-size:0.8rem;color:var(--text-muted);margin:0;">{{ $peserta->email }}</p>
                </div>
            </div>
        </div>

        <form method="POST" action="{{ route('admin.peserta.update', $peserta) }}" style="padding:1.5rem;">
            @csrf @method('PUT')

            {{-- Nama Lengkap --}}
            <div class="form-group">
                <label class="form-label" for="name">
                    Nama Lengkap <span style="color:#ef4444;">*</span>
                </label>
                <input type="text" id="name" name="name"
                       class="form-control @error('name') is-invalid @enderror"
                       value="{{ old('name', $peserta->name) }}" autofocus>
                @error('name')
                    <p class="form-error">{{ $message }}</p>
                @enderror
            </div>

            {{-- Email --}}
            <div class="form-group">
                <label class="form-label" for="email">
                    Email <span style="font-size:0.75rem;color:var(--text-light);font-weight:normal;">(Opsional)</span>
                </label>
                <input type="email" id="email" name="email"
                       class="form-control @error('email') is-invalid @enderror"
                       value="{{ old('email', $peserta->email) }}">
                @error('email')
                    <p class="form-error">{{ $message }}</p>
                @enderror
            </div>

            {{-- Username --}}
            <div class="form-group">
                <label class="form-label" for="username">
                    Username <span style="color:#ef4444;">*</span>
                </label>
                <input type="text" id="username" name="username" class="form-control @error('username') is-invalid @enderror"
                       value="{{ old('username', $peserta->username) }}" placeholder="Masukkan username unik" required>
                @error('username')
                    <p class="form-error">{{ $message }}</p>
                @enderror
            </div>

            {{-- 2 kolom: No HP & No Peserta --}}
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem;">
                <div class="form-group">
                    <label class="form-label" for="no_hp">Nomor HP</label>
                    <input type="text" id="no_hp" name="no_hp"
                           class="form-control @error('no_hp') is-invalid @enderror"
                           value="{{ old('no_hp', $peserta->no_hp) }}" placeholder="08xx-xxxx-xxxx">
                    @error('no_hp')
                        <p class="form-error">{{ $message }}</p>
                    @enderror
                </div>
                <div class="form-group">
                    <label class="form-label" for="no_peserta">Nomor Peserta</label>
                    <input type="text" id="no_peserta" name="no_peserta"
                           class="form-control @error('no_peserta') is-invalid @enderror"
                           value="{{ old('no_peserta', $peserta->no_peserta) }}" placeholder="Opsional">
                    @error('no_peserta')
                        <p class="form-error">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            {{-- Grup & Kategori --}}
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem;">
                <div class="form-group">
                    <label class="form-label" for="group_id">
                        Grup <span style="color:#ef4444;">*</span>
                    </label>
                    <select id="group_id" name="group_id" class="form-control @error('group_id') is-invalid @enderror" required onchange="updateCategories()">
                        <option value="">-- Pilih Grup --</option>
                        @foreach($groups as $group)
                            <option value="{{ $group->id }}" data-name="{{ $group->name }}" {{ old('group_id', $peserta->group_id) == $group->id ? 'selected' : '' }}>{{ $group->name }}</option>
                        @endforeach
                    </select>
                    @error('group_id')
                        <p class="form-error">{{ $message }}</p>
                    @enderror
                </div>
                <div class="form-group">
                    <label class="form-label" for="category">
                        Kategori <span style="color:#ef4444;">*</span>
                    </label>
                    <select id="category" name="category" class="form-control @error('category') is-invalid @enderror" required>
                        <option value="">-- Pilih Kategori --</option>
                    </select>
                    @error('category')
                        <p class="form-error">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            {{-- Paket Ditugaskan --}}
            <div class="form-group">
                <label class="form-label" for="assigned_package_id">Paket Ditugaskan</label>
                <select id="assigned_package_id" name="assigned_package_id" class="form-control @error('assigned_package_id') is-invalid @enderror">
                    <option value="">-- Tidak Ada Paket --</option>
                    @foreach($packages as $package)
                        <option value="{{ $package->id }}" {{ old('assigned_package_id', $peserta->assigned_package_id) == $package->id ? 'selected' : '' }}>{{ $package->nama }} ({{ $package->jenis_ujian === 'drill' ? 'Drill Soal' : 'Tryout' }} &middot; {{ $package->group }})</option>
                    @endforeach
                </select>
                @error('assigned_package_id')
                    <p class="form-error">{{ $message }}</p>
                @enderror
            </div>

            {{-- Status --}}
            <div class="form-group">
                <label class="form-label">Status Peserta</label>
                <div style="display:flex;gap:1.25rem;margin-top:0.35rem;">
                    <label style="display:flex;align-items:center;gap:0.5rem;cursor:pointer;font-size:0.9rem;">
                        <input type="radio" name="is_active" value="1"
                               {{ old('is_active', $peserta->is_active ? '1' : '0') === '1' ? 'checked' : '' }}
                               style="accent-color:var(--primary);">
                        <span style="color:#16a34a;font-weight:500;">Aktif</span>
                    </label>
                    <label style="display:flex;align-items:center;gap:0.5rem;cursor:pointer;font-size:0.9rem;">
                        <input type="radio" name="is_active" value="0"
                               {{ old('is_active', $peserta->is_active ? '1' : '0') === '0' ? 'checked' : '' }}
                               style="accent-color:#ef4444;">
                        <span style="color:#dc2626;font-weight:500;">Nonaktif</span>
                    </label>
                </div>
            </div>

            <div style="display:flex;gap:0.75rem;justify-content:flex-end;margin-top:1.5rem;padding-top:1.25rem;border-top:1px solid var(--border);">
                <a href="{{ route('admin.peserta.index') }}" class="btn btn-secondary">Batal</a>
                <button type="submit" class="btn btn-primary">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v14a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>

    {{-- Card Reset Password terpisah --}}
    <div class="table-card" style="padding:0;">
        <div class="table-header" style="padding:1rem 1.5rem;border-bottom:1px solid var(--border);">
            <h3 style="font-weight:700;font-size:0.95rem;margin:0;color:#9333ea;">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" style="vertical-align:middle;margin-right:4px;"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                Reset Password
            </h3>
        </div>
        <form method="POST" action="{{ route('admin.peserta.resetPassword', $peserta) }}" style="padding:1.5rem;">
            @csrf
            <p style="font-size:0.85rem;color:var(--text-muted);margin:0 0 1.25rem;">
                Kosongkan jika tidak ingin mengubah password. Password baru akan langsung berlaku.
            </p>
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem;">
                <div class="form-group">
                    <label class="form-label" for="new_password">Password Baru</label>
                    <input type="password" id="new_password" name="password"
                           class="form-control @error('password') is-invalid @enderror"
                           placeholder="Minimal 6 karakter">
                    @error('password')
                        <p class="form-error">{{ $message }}</p>
                    @enderror
                </div>
                <div class="form-group">
                    <label class="form-label" for="new_password_confirmation">Konfirmasi</label>
                    <input type="password" id="new_password_confirmation" name="password_confirmation"
                           class="form-control" placeholder="Ulangi password baru">
                </div>
            </div>
            <div style="display:flex;justify-content:flex-end;margin-top:0.5rem;">
                <button type="submit" class="btn btn-primary" style="background:#9333ea;border-color:#9333ea;">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                    Reset Password
                </button>
            </div>
        </form>
    </div>

    {{-- Info sesi ujian --}}
    <div style="background:#fffbeb;border:1px solid #fde68a;border-radius:10px;padding:1rem 1.25rem;font-size:0.84rem;color:#92400e;">
        <strong>Info:</strong> Peserta ini sudah melakukan
        <strong>{{ $peserta->examSessions()->count() }} sesi ujian</strong>.
        Menghapus peserta akan menghapus seluruh data ujian dan nilainya.
    </div>

</div>
@endsection

@push('styles')
<style>
.form-error { color:#ef4444;font-size:0.8rem;margin-top:0.3rem; }
.form-control.is-invalid { border-color:#ef4444!important;box-shadow:0 0 0 3px rgba(239,68,68,.1)!important; }
</style>
@endpush

@push('scripts')
<script>
function updateCategories() {
    const groupSelect = document.getElementById('group_id');
    const categorySelect = document.getElementById('category');
    const selectedOption = groupSelect.options[groupSelect.selectedIndex];
    const groupName = selectedOption ? selectedOption.getAttribute('data-name') : '';
    
    const oldCategory = "{{ old('category', $peserta->category) }}";
    
    // Clear options
    categorySelect.innerHTML = '<option value="">-- Pilih Kategori --</option>';
    
    if (groupName === 'SKD') {
        const opt1 = document.createElement('option');
        opt1.value = 'CPNS';
        opt1.textContent = 'CPNS';
        if (oldCategory === 'CPNS') opt1.selected = true;
        categorySelect.appendChild(opt1);
        
        const opt2 = document.createElement('option');
        opt2.value = 'Kedinasan';
        opt2.textContent = 'Kedinasan';
        if (oldCategory === 'Kedinasan') opt2.selected = true;
        categorySelect.appendChild(opt2);
    } else if (groupName === 'SNBT') {
        const opt = document.createElement('option');
        opt.value = 'SNBT';
        opt.textContent = 'SNBT';
        if (oldCategory === 'SNBT') opt.selected = true;
        categorySelect.appendChild(opt);
    }
}

// Initialize categories on load
document.addEventListener('DOMContentLoaded', function() {
    updateCategories();
});
</script>
@endpush
