@extends('admin.layouts.app')
@section('title', 'Edit Paket Ujian')

@section('content')
<div class="form-card" style="max-width: 680px; margin: 0 auto;">
    <h3>Edit Paket: {{ $tryout->nama }}</h3>
    <form method="POST" action="{{ route('admin.tryouts.update', $tryout) }}">
        @csrf @method('PUT')

        {{-- Jenis Ujian --}}
        <input type="hidden" name="jenis_ujian" value="{{ old('jenis_ujian', $tryout->jenis_ujian) }}">

        {{-- Nama Paket --}}
        <div class="form-group">
            <label>Nama Paket <span style="color:#ef4444;">*</span></label>
            <input type="text" name="nama" class="form-control" value="{{ old('nama', $tryout->nama) }}" required>
            @error('nama')<p class="form-error">{{ $message }}</p>@enderror
        </div>

        {{-- Deskripsi --}}
        <div class="form-group">
            <label>Deskripsi</label>
            <textarea name="deskripsi" class="form-control" rows="3">{{ old('deskripsi', $tryout->deskripsi) }}</textarea>
        </div>

        {{-- Program/Grup, Kode Soal, Batas Percobaan, & Kategori --}}
        <div style="display:grid; grid-template-columns: 1fr 1fr 1fr; gap:1rem; margin-bottom:1rem;">
            <div class="form-group" style="margin-bottom:0;">
                <label>Program / Grup <span style="color:#ef4444;">*</span></label>
                <select name="group_id" id="groupSelect" class="form-control @error('group_id') is-invalid @enderror" required onchange="loadCodes()">
                    <option value="">-- Pilih --</option>
                    @foreach($groups as $g)
                        <option value="{{ $g->id }}" {{ old('group_id', $tryout->group_id) == $g->id ? 'selected' : '' }}>{{ $g->name }}</option>
                    @endforeach
                </select>
                @error('group_id')<p class="form-error">{{ $message }}</p>@enderror
            </div>
            <div class="form-group" style="margin-bottom:0;">
                <label>Kode Soal <span style="color:#ef4444;">*</span></label>
                <select name="question_code_id" id="codeSelect" class="form-control @error('question_code_id') is-invalid @enderror" required onchange="loadCategories()">
                    <option value="">-- Pilih --</option>
                </select>
                @error('question_code_id')<p class="form-error">{{ $message }}</p>@enderror
            </div>
            <div class="form-group" style="margin-bottom:0;">
                <label>Batas Percobaan <span style="color:#ef4444;">*</span></label>
                <input type="number" name="attempt_limit" class="form-control @error('attempt_limit') is-invalid @enderror" value="{{ old('attempt_limit', $tryout->attempt_limit) }}" min="1" required>
                @error('attempt_limit')<p class="form-error">{{ $message }}</p>@enderror
            </div>
        </div>
        <div style="display:grid; grid-template-columns: 1fr; gap:1rem; margin-bottom:1rem;">
            <div class="form-group" style="margin-bottom:0;">
                <label>Kategori <span style="color:#ef4444;">*</span></label>
                <select name="category_id" id="categorySelect" class="form-control @error('category_id') is-invalid @enderror" required>
                    <option value="">-- Pilih --</option>
                </select>
                @error('category_id')<p class="form-error">{{ $message }}</p>@enderror
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label>Durasi (menit) <span style="color:#ef4444;">*</span></label>
                <input type="number" name="durasi_menit" class="form-control" value="{{ old('durasi_menit', $tryout->durasi_menit) }}" min="10" max="300" required>
                @error('durasi_menit')<p class="form-error">{{ $message }}</p>@enderror
            </div>
            <div class="form-group">
                <label>Mode Ujian <span style="color:#ef4444;">*</span></label>
                <select name="exam_mode" id="examMode" class="form-control" required onchange="toggleSebFields()">
                    <option value="normal" {{ old('exam_mode', $tryout->exam_mode) === 'normal' ? 'selected' : '' }}>Normal (Bisa diakses browser biasa)</option>
                    <option value="seb" {{ old('exam_mode', $tryout->exam_mode) === 'seb' ? 'selected' : '' }}>Safe Exam Browser (SEB)</option>
                </select>
                @error('exam_mode')<p class="form-error">{{ $message }}</p>@enderror
            </div>
        </div>

        <div id="sebFields" style="display: {{ old('exam_mode', $tryout->exam_mode) === 'seb' ? 'block' : 'none' }}; border: 1px solid var(--border); padding: 1rem; border-radius: 8px; margin-bottom: 1rem; background: #fafafa;">
            <h4 style="margin-bottom:0.75rem; color:var(--primary); font-weight:700; font-size:0.9rem;">Safe Exam Browser (SEB) Settings</h4>
            <div class="form-group">
                <label>URL Ujian (Mulai)</label>
                <input type="text" name="seb_url" class="form-control" value="{{ old('seb_url', $tryout->seb_url) }}" placeholder="Contoh: http://localhost:8000/peserta/tryout/1/mulai (Kosongkan untuk otomatis)">
                <small style="color:var(--text-muted); display:block; margin-top:0.25rem;">Biarkan kosong untuk otomatis mengarahkan ke link pengerjaan ujian peserta.</small>
                @error('seb_url')<p class="form-error">{{ $message }}</p>@enderror
            </div>
            <div class="form-group">
                <label>Password Keluar (Quit Password)</label>
                <input type="password" name="seb_quit_password" class="form-control" value="{{ old('seb_quit_password', $tryout->seb_quit_password) }}" placeholder="Masukkan password untuk keluar dari SEB">
                <small style="color:var(--text-muted); display:block; margin-top:0.25rem;">Mencegah peserta menutup SEB tanpa menyelesaikan ujian.</small>
                @error('seb_quit_password')<p class="form-error">{{ $message }}</p>@enderror
            </div>
            <div class="form-group" style="margin-bottom:0;">
                <label class="toggle" style="display:flex; align-items:center; gap:0.5rem; cursor:pointer;">
                    <input type="checkbox" name="seb_browser_lockdown" value="1" {{ old('seb_browser_lockdown', $tryout->seb_browser_lockdown) ? 'checked' : '' }} style="width:16px; height:16px; accent-color:var(--primary);">
                    <span class="toggle-label" style="font-weight:600; font-size:0.85rem; color:var(--text);">Lockdown Browser (Sembunyikan URL & Reload)</span>
                </label>
            </div>
        </div>

        {{-- Token & Randomization --}}
        <div class="form-row">
            <div class="form-group">
                <label>Token Akses Ujian (Opsional)</label>
                <input type="text" name="token" class="form-control" value="{{ old('token', $tryout->token) }}" placeholder="Contoh: SKD2026 (Wajib diisi jika diatur)" style="text-transform: uppercase;">
                <small style="color:var(--text-muted); display:block; margin-top:0.25rem;">Peserta wajib memasukkan token ini sebelum dapat memulai ujian.</small>
            </div>
        </div>

        <div style="display:grid; grid-template-columns: 1fr 1fr; gap:1rem; margin-bottom:1.5rem; border: 1px solid var(--border); padding: 1rem; border-radius: 8px; background: #fcfcfc;">
            <div class="form-group" style="margin-bottom:0;">
                <label class="toggle" style="display:flex; align-items:center; gap:0.5rem; cursor:pointer;">
                    <input type="checkbox" name="randomize_questions" value="1" {{ old('randomize_questions', $tryout->randomize_questions) ? 'checked' : '' }} style="width:16px; height:16px; accent-color:var(--primary);">
                    <span class="toggle-label" style="font-weight:600; font-size:0.85rem; color:var(--text);">Acak Urutan Soal</span>
                </label>
                <small style="color:var(--text-muted); display:block; margin-top:0.25rem;">Setiap peserta akan menerima urutan soal yang berbeda.</small>
            </div>
            <div class="form-group" style="margin-bottom:0;">
                <label class="toggle" style="display:flex; align-items:center; gap:0.5rem; cursor:pointer;">
                    <input type="checkbox" name="randomize_options" value="1" {{ old('randomize_options', $tryout->randomize_options) ? 'checked' : '' }} style="width:16px; height:16px; accent-color:var(--primary);">
                    <span class="toggle-label" style="font-weight:600; font-size:0.85rem; color:var(--text);">Acak Pilihan Jawaban</span>
                </label>
                <small style="color:var(--text-muted); display:block; margin-top:0.25rem;">Setiap peserta akan menerima urutan pilihan (A-E) yang berbeda.</small>
            </div>
        </div>

        <div class="form-group">
            <label class="toggle" style="display:flex; align-items:center; gap:0.5rem; cursor:pointer;">
                <input type="checkbox" name="is_active" value="1" {{ old('is_active', $tryout->is_active) ? 'checked' : '' }} style="width:16px; height:16px; accent-color:var(--primary);">
                <span class="toggle-label" style="font-weight:600; font-size:0.85rem; color:var(--text);">Aktifkan Paket Ujian</span>
            </label>
        </div>
        <div class="form-row">
            <div class="form-group">
                <label>Tanggal Mulai (Opsional)</label>
                <input type="datetime-local" name="mulai_at" class="form-control" value="{{ old('mulai_at', $tryout->mulai_at?->format('Y-m-d\TH:i')) }}">
            </div>
            <div class="form-group">
                <label>Tanggal Selesai (Opsional)</label>
                <input type="datetime-local" name="selesai_at" class="form-control" value="{{ old('selesai_at', $tryout->selesai_at?->format('Y-m-d\TH:i')) }}">
            </div>
        </div>
        <div class="form-actions">
            <button type="submit" class="btn btn-primary">Update Paket</button>
            <a href="{{ route('admin.tryouts.index', ['type' => $tryout->jenis_ujian]) }}" class="btn btn-secondary">Batal</a>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
function toggleSebFields() {
    const examMode = document.getElementById('examMode').value;
    const sebFields = document.getElementById('sebFields');
    if (examMode === 'seb') {
        sebFields.style.display = 'block';
    } else {
        sebFields.style.display = 'none';
    }
}

async function loadCodes(selectedId = null) {
    const groupId = document.getElementById('groupSelect').value;
    const codeSelect = document.getElementById('codeSelect');
    const categorySelect = document.getElementById('categorySelect');
    
    codeSelect.innerHTML = '<option value="">-- Pilih --</option>';
    categorySelect.innerHTML = '<option value="">-- Pilih --</option>';
    
    if (!groupId) return;
    
    const res = await fetch(`${window.CbtConfig.baseUrl}/admin/api/codes/${groupId}`);
    const codes = await res.json();
    codes.forEach(c => {
        const opt = document.createElement('option');
        opt.value = c.id;
        opt.textContent = `[${c.code}] ${c.name}`;
        if (selectedId && c.id == selectedId) opt.selected = true;
        codeSelect.appendChild(opt);
    });
}

async function loadCategories(selectedId = null) {
    const codeId = document.getElementById('codeSelect').value;
    const categorySelect = document.getElementById('categorySelect');
    
    categorySelect.innerHTML = '<option value="">-- Pilih --</option>';
    
    if (!codeId) return;
    
    const res = await fetch(`${window.CbtConfig.baseUrl}/admin/api/categories/${codeId}`);
    const categories = await res.json();
    categories.forEach(c => {
        const opt = document.createElement('option');
        opt.value = c.id;
        opt.textContent = c.name;
        if (selectedId && c.id == selectedId) opt.selected = true;
        categorySelect.appendChild(opt);
    });
}

document.addEventListener('DOMContentLoaded', async function() {
    toggleSebFields();
    
    const groupId = "{{ old('group_id', $tryout->group_id) }}";
    const selectedCodeId = "{{ old('question_code_id', $tryout->question_code_id) }}";
    const selectedCategoryId = "{{ old('category_id', $tryout->category_id) }}";
    
    if (groupId) {
        await loadCodes(selectedCodeId);
    }
    if (selectedCodeId) {
        await loadCategories(selectedCategoryId);
    }
});
</script>
@endpush
