@extends('admin.layouts.app')
@section('title', 'Edit Modul Pembelajaran')

@section('topbar-actions')
    <a href="{{ route('admin.modules.index') }}" class="btn btn-secondary">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="15 18 9 12 15 6"/></svg>
        Kembali
    </a>
@endsection

@section('content')
<div style="max-width:640px; margin:0 auto;">
    <div class="table-card" style="padding:0; background:#fff; border-radius:12px; box-shadow:0 4px 6px -1px rgba(0,0,0,0.05);">
        <div class="table-header" style="padding:1.25rem 1.5rem; border-bottom:1px solid #e2e8f0;">
            <div>
                <h3 style="font-weight:800; font-size:1.15rem; color:#0f172a; margin:0 0 0.25rem;">Edit Modul Pembelajaran</h3>
                <p style="font-size:0.8rem; color:#64748b; margin:0;">Ubah formulir di bawah untuk memperbarui modul materi belajar.</p>
            </div>
        </div>

        <form method="POST" action="{{ route('admin.modules.update', $module) }}" enctype="multipart/form-data" style="padding:1.5rem;">
            @csrf
            @method('PUT')

            {{-- Grup Ujian --}}
            <div class="form-group" style="margin-bottom:1.25rem;">
                <label class="form-label" style="font-weight:600; display:block; margin-bottom:0.5rem;">Grup Ujian <span style="color:#ef4444;">*</span></label>
                <select name="group_id" id="group_id" class="form-control" required style="width:100%; padding:0.625rem; border-radius:6px; border:1px solid #cbd5e1;">
                    <option value="">-- Pilih Grup Ujian --</option>
                    @foreach($groups as $grp)
                        <option value="{{ $grp->id }}" {{ old('group_id', $module->group_id) == $grp->id ? 'selected' : '' }}>{{ $grp->name }}</option>
                    @endforeach
                </select>
                @error('group_id')<p class="form-error" style="color:#ef4444; font-size:0.75rem; margin-top:0.25rem;">{{ $message }}</p>@enderror
            </div>

            <div style="background:#f8fafc; padding:1.25rem; border-radius:8px; border:1px solid #e2e8f0; margin-bottom:1.25rem;">
                <div class="form-group" style="margin-bottom:1rem;">
                    <label class="form-label" style="font-weight:600; display:block; margin-bottom:0.5rem; font-size:0.85rem; color:#475569;">1. Kode Soal <span style="color:#ef4444;">*</span></label>
                    <select name="question_code_id" id="question_code_id" class="form-control" required disabled style="width:100%; padding:0.5rem; border-radius:4px; border:1px solid #cbd5e1;">
                        <option value="">-- Pilih Kode Soal --</option>
                    </select>
                    @error('question_code_id')<p class="form-error" style="color:#ef4444; font-size:0.75rem; margin-top:0.25rem;">{{ $message }}</p>@enderror
                </div>

                <div class="form-group" style="margin-bottom:0;">
                    <label class="form-label" style="font-weight:600; display:block; margin-bottom:0.5rem; font-size:0.85rem; color:#475569;">2. Kategori <span style="color:#ef4444;">*</span></label>
                    <select name="category_id" id="category_id" class="form-control" required disabled style="width:100%; padding:0.5rem; border-radius:4px; border:1px solid #cbd5e1;">
                        <option value="">-- Pilih Kategori --</option>
                    </select>
                    @error('category_id')<p class="form-error" style="color:#ef4444; font-size:0.75rem; margin-top:0.25rem;">{{ $message }}</p>@enderror
                </div>
            </div>

            {{-- Nama Modul --}}
            <div class="form-group" style="margin-bottom:1.25rem;">
                <label class="form-label" for="name" style="font-weight:600; display:block; margin-bottom:0.5rem;">Nama Modul <span style="color:#ef4444;">*</span></label>
                <input type="text" id="name" name="name" class="form-control" value="{{ old('name', $module->name) }}" placeholder="Masukkan nama modul pembelajaran" required style="width:100%; padding:0.625rem; border-radius:6px; border:1px solid #cbd5e1;">
                @error('name')<p class="form-error" style="color:#ef4444; font-size:0.75rem; margin-top:0.25rem;">{{ $message }}</p>@enderror
            </div>

            {{-- Deskripsi --}}
            <div class="form-group" style="margin-bottom:1.25rem;">
                <label class="form-label" for="description" style="font-weight:600; display:block; margin-bottom:0.5rem;">Deskripsi</label>
                <textarea id="description" name="description" class="form-control" placeholder="Deskripsi ringkas materi modul..." rows="3" style="width:100%; padding:0.625rem; border-radius:6px; border:1px solid #cbd5e1; font-family:inherit;">{{ old('description', $module->description) }}</textarea>
                @error('description')<p class="form-error" style="color:#ef4444; font-size:0.75rem; margin-top:0.25rem;">{{ $message }}</p>@enderror
            </div>

            {{-- File PDF --}}
            <div class="form-group" style="margin-bottom:1.25rem;">
                <label class="form-label" for="pdf_file" style="font-weight:600; display:block; margin-bottom:0.5rem;">File PDF Materi (Maksimal 10MB)</label>
                @if($module->pdf_file)
                    <div style="margin-bottom:0.5rem; display:flex; align-items:center; gap:0.5rem; background:#eff6ff; padding:0.5rem 0.75rem; border-radius:6px; border:1px solid #bfdbfe;">
                        <span style="font-size:0.85rem; color:#1e40af; font-weight:600;">📄 {{ basename($module->pdf_file) }}</span>
                        <label style="display:flex; align-items:center; gap:0.25rem; font-size:0.75rem; color:#ef4444; cursor:pointer; margin-left:auto; font-weight:700;">
                            <input type="checkbox" name="hapus_pdf" value="1" style="accent-color:#ef4444;"> Hapus PDF
                        </label>
                    </div>
                @endif
                <input type="file" id="pdf_file" name="pdf_file" class="form-control" accept="application/pdf" style="width:100%; padding:0.5rem; border-radius:6px; border:1px solid #cbd5e1;">
                @error('pdf_file')<p class="form-error" style="color:#ef4444; font-size:0.75rem; margin-top:0.25rem;">{{ $message }}</p>@enderror
            </div>

            {{-- Video URL --}}
            <div class="form-group" style="margin-bottom:1.5rem;">
                <label class="form-label" for="video_url" style="font-weight:600; display:block; margin-bottom:0.5rem;">URL Video Pembelajaran (Opsional)</label>
                <input type="url" id="video_url" name="video_url" class="form-control" value="{{ old('video_url', $module->video_url) }}" placeholder="https://youtube.com/watch?v=..." style="width:100%; padding:0.625rem; border-radius:6px; border:1px solid #cbd5e1;">
                @error('video_url')<p class="form-error" style="color:#ef4444; font-size:0.75rem; margin-top:0.25rem;">{{ $message }}</p>@enderror
            </div>

            {{-- Status --}}
            <div class="form-group" style="margin-bottom:1.5rem;">
                <label class="form-label" style="font-weight:600; display:block; margin-bottom:0.5rem;">Status Modul</label>
                <div style="display:flex; gap:1.25rem; margin-top:0.35rem;">
                    <label style="display:flex; align-items:center; gap:0.5rem; cursor:pointer; font-size:0.9rem;">
                        <input type="radio" name="is_active" value="1" {{ old('is_active', $module->is_active ? '1' : '0') === '1' ? 'checked' : '' }} style="accent-color:#1e40af;">
                        <span style="color:#16a34a; font-weight:700;">Aktif</span>
                    </label>
                    <label style="display:flex; align-items:center; gap:0.5rem; cursor:pointer; font-size:0.9rem;">
                        <input type="radio" name="is_active" value="0" {{ old('is_active', $module->is_active ? '1' : '0') === '0' ? 'checked' : '' }} style="accent-color:#ef4444;">
                        <span style="color:#dc2626; font-weight:700;">Nonaktif</span>
                    </label>
                </div>
            </div>

            <div style="display:flex; gap:0.75rem; justify-content:flex-end; margin-top:1.5rem; padding-top:1.25rem; border-top:1px solid #e2e8f0;">
                <a href="{{ route('admin.modules.index') }}" class="btn btn-secondary" style="padding:0.5rem 1.5rem; font-weight:600;">Batal</a>
                <button type="submit" class="btn btn-primary" style="padding:0.5rem 1.5rem; font-weight:700; display:flex; align-items:center; gap:0.35rem;">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v14a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    const groupSelect = document.getElementById('group_id');
    const codeSelect = document.getElementById('question_code_id');
    const catSelect = document.getElementById('category_id');

    async function loadCodes(groupId, selectedCodeId = null) {
        codeSelect.innerHTML = '<option value="">-- Pilih Kode Soal --</option>';
        codeSelect.disabled = true;
        catSelect.innerHTML = '<option value="">-- Pilih Kategori --</option>';
        catSelect.disabled = true;

        if (!groupId) return;

        try {
            const response = await fetch(`${window.CbtConfig.baseUrl}/admin/api/codes/${groupId}`);
            const codes = await response.json();
            if (codes.length > 0) {
                codes.forEach(c => {
                    const opt = document.createElement('option');
                    opt.value = c.id;
                    opt.textContent = `${c.code} - ${c.name}`;
                    if (selectedCodeId && c.id == selectedCodeId) opt.selected = true;
                    codeSelect.appendChild(opt);
                });
                codeSelect.disabled = false;
                if (selectedCodeId) codeSelect.dispatchEvent(new Event('change'));
            }
        } catch (e) { console.error(e); }
    }

    async function loadCategories(codeId, selectedCatId = null) {
        catSelect.innerHTML = '<option value="">-- Pilih Kategori --</option>';
        catSelect.disabled = true;

        if (!codeId) return;

        try {
            const response = await fetch(`${window.CbtConfig.baseUrl}/admin/api/categories/${codeId}`);
            const cats = await response.json();
            if (cats.length > 0) {
                cats.forEach(c => {
                    const opt = document.createElement('option');
                    opt.value = c.id;
                    opt.textContent = c.name;
                    if (selectedCatId && c.id == selectedCatId) opt.selected = true;
                    catSelect.appendChild(opt);
                });
                catSelect.disabled = false;
            }
        } catch (e) { console.error(e); }
    }

    groupSelect.addEventListener('change', function() {
        loadCodes(this.value);
    });

    codeSelect.addEventListener('change', function() {
        loadCategories(this.value);
    });

    document.addEventListener('DOMContentLoaded', function() {
        const currentGroup = "{{ old('group_id', $module->group_id) }}";
        const currentCode = "{{ old('question_code_id', $module->question_code_id) }}";
        const currentCat = "{{ old('category_id', $module->category_id) }}";

        if (currentGroup) {
            loadCodes(currentGroup, currentCode).then(() => {
                if (currentCode) {
                    loadCategories(currentCode, currentCat);
                }
            });
        }
    });
</script>
@endpush
@section('content')
