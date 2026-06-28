@extends('admin.layouts.app')
@section('title', 'Tambah Peserta')

@section('topbar-actions')
    <a href="{{ route('admin.peserta.index') }}" class="btn btn-secondary">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="15 18 9 12 15 6"/></svg>
        Kembali
    </a>
@endsection

@section('content')
<div style="max-width:640px;margin:0 auto;">

    <div class="table-card" style="padding:0;">
        <div class="table-header" style="padding:1.25rem 1.5rem;border-bottom:1px solid var(--border);">
            <div>
                <h3 style="font-weight:700;font-size:1rem;margin:0 0 0.15rem;">Tambah Peserta Baru</h3>
                <p style="font-size:0.8rem;color:var(--text-muted);margin:0;">Isi formulir di bawah untuk mendaftarkan peserta baru.</p>
            </div>
        </div>

        <form method="POST" action="{{ route('admin.peserta.store') }}" style="padding:1.5rem;">
            @csrf

            {{-- Nama Lengkap --}}
            <div class="form-group">
                <label class="form-label" for="name">
                    Nama Lengkap <span style="color:#ef4444;">*</span>
                </label>
                <input type="text" id="name" name="name" class="form-control @error('name') is-invalid @enderror"
                       value="{{ old('name') }}" placeholder="Masukkan nama lengkap" autofocus>
                @error('name')
                    <p class="form-error">{{ $message }}</p>
                @enderror
            </div>

            {{-- Email --}}
            <div class="form-group">
                <label class="form-label" for="email">
                    Email <span style="font-size:0.75rem;color:var(--text-light);font-weight:normal;">(Opsional)</span>
                </label>
                <input type="email" id="email" name="email" class="form-control @error('email') is-invalid @enderror"
                       value="{{ old('email') }}" placeholder="email@contoh.com">
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
                       value="{{ old('username') }}" placeholder="Masukkan username unik" required>
                @error('username')
                    <p class="form-error">{{ $message }}</p>
                @enderror
            </div>

            {{-- 2 kolom: No HP & No Peserta --}}
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem;">
                <div class="form-group">
                    <label class="form-label" for="no_hp">Nomor HP</label>
                    <input type="text" id="no_hp" name="no_hp" class="form-control @error('no_hp') is-invalid @enderror"
                           value="{{ old('no_hp') }}" placeholder="08xx-xxxx-xxxx">
                    @error('no_hp')
                        <p class="form-error">{{ $message }}</p>
                    @enderror
                </div>
                <div class="form-group">
                    <label class="form-label" for="no_peserta">Nomor Peserta</label>
                    <input type="text" id="no_peserta" name="no_peserta" class="form-control @error('no_peserta') is-invalid @enderror"
                           value="{{ old('no_peserta') }}" placeholder="Opsional">
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
                            <option value="{{ $group->id }}" data-name="{{ $group->name }}" {{ old('group_id') == $group->id ? 'selected' : '' }}>{{ $group->name }}</option>
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
                <label class="form-label" for="assigned_package_id">
                    Paket Ditugaskan <span style="font-size:0.75rem;color:var(--text-light);font-weight:normal;">(Opsional)</span>
                </label>
                <select id="assigned_package_id" name="assigned_package_id" class="form-control @error('assigned_package_id') is-invalid @enderror">
                    <option value="">-- Tidak Ada (Akses Semua Paket) --</option>
                    @foreach($packages as $package)
                        <option value="{{ $package->id }}" {{ old('assigned_package_id') == $package->id ? 'selected' : '' }}>
                            [{{ strtoupper($package->jenis_ujian) }}] {{ $package->nama }}
                        </option>
                    @endforeach
                </select>
                @error('assigned_package_id')
                    <p class="form-error">{{ $message }}</p>
                @enderror
            </div>



            {{-- 2 kolom: Password & Konfirmasi --}}
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem;">
                <div class="form-group">
                    <label class="form-label" for="password">
                        Password <span style="color:#ef4444;">*</span>
                    </label>
                    <input type="password" id="password" name="password"
                           class="form-control @error('password') is-invalid @enderror"
                           placeholder="Minimal 6 karakter">
                    @error('password')
                        <p class="form-error">{{ $message }}</p>
                    @enderror
                </div>
                <div class="form-group">
                    <label class="form-label" for="password_confirmation">
                        Konfirmasi Password <span style="color:#ef4444;">*</span>
                    </label>
                    <input type="password" id="password_confirmation" name="password_confirmation"
                           class="form-control" placeholder="Ulangi password">
                </div>
            </div>

            {{-- Status --}}
            <div class="form-group">
                <label class="form-label">Status Peserta</label>
                <div style="display:flex;gap:1.25rem;margin-top:0.35rem;">
                    <label style="display:flex;align-items:center;gap:0.5rem;cursor:pointer;font-size:0.9rem;">
                        <input type="radio" name="is_active" value="1" {{ old('is_active', '1') === '1' ? 'checked' : '' }}
                               style="accent-color:var(--primary);">
                        <span style="color:#16a34a;font-weight:500;">Aktif</span>
                    </label>
                    <label style="display:flex;align-items:center;gap:0.5rem;cursor:pointer;font-size:0.9rem;">
                        <input type="radio" name="is_active" value="0" {{ old('is_active') === '0' ? 'checked' : '' }}
                               style="accent-color:#ef4444;">
                        <span style="color:#dc2626;font-weight:500;">Nonaktif</span>
                    </label>
                </div>
            </div>

            <div style="display:flex;gap:0.75rem;justify-content:flex-end;margin-top:1.5rem;padding-top:1.25rem;border-top:1px solid var(--border);">
                <a href="{{ route('admin.peserta.index') }}" class="btn btn-secondary">Batal</a>
                <button type="submit" class="btn btn-primary">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v14a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
                    Simpan Peserta
                </button>
            </div>
        </form>
    </div>

</div>
@endsection

@push('styles')
<style>
.form-error {
    color: #ef4444;
    font-size: 0.8rem;
    margin-top: 0.3rem;
}
.form-control.is-invalid {
    border-color: #ef4444 !important;
    box-shadow: 0 0 0 3px rgba(239,68,68,0.1) !important;
}
</style>
@endpush

@push('scripts')
<script>
function updateCategories() {
    const groupSelect = document.getElementById('group_id');
    const categorySelect = document.getElementById('category');
    const selectedOption = groupSelect.options[groupSelect.selectedIndex];
    const groupName = selectedOption ? selectedOption.getAttribute('data-name') : '';
    
    const oldCategory = "{{ old('category') }}";
    
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
