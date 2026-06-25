@extends('admin.layouts.app')
@section('title', 'Tambah Soal')

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/quill@2.0.2/dist/quill.snow.css" rel="stylesheet" />
<style>
.ql-container {
    font-family: inherit;
    font-size: 0.9rem;
    border-bottom-left-radius: 6px;
    border-bottom-right-radius: 6px;
}
.ql-toolbar {
    font-family: inherit;
    border-top-left-radius: 6px;
    border-top-right-radius: 6px;
}
</style>
@endpush

@section('content')
<div class="form-card" style="max-width:880px; margin: 0 auto; background:#fff; padding:2rem; border-radius:12px; box-shadow:0 4px 6px -1px rgba(0,0,0,0.05);">
    <h3 style="font-weight:800; color:#0f172a; margin-bottom:1.5rem;">Tambah Soal Baru</h3>
    <form method="POST" action="{{ route('admin.questions.store') }}" enctype="multipart/form-data">
        @csrf
        
        <div style="display:grid; grid-template-columns:1fr 1fr; gap:1rem; margin-bottom:1rem;">
            {{-- Target Paket Ujian --}}
            <div class="form-group">
                <label style="font-weight:600; display:block; margin-bottom:0.5rem;">Target Paket Ujian <span style="color:#ef4444;">*</span></label>
                <select name="tryout_package_id" class="form-control" required style="width:100%; padding:0.625rem; border-radius:6px; border:1px solid #cbd5e1;">
                    <option value="">-- Pilih Paket Ujian --</option>
                    @foreach($packages as $p)
                        <option value="{{ $p->id }}" {{ old('tryout_package_id', $defaultPackageId) == $p->id ? 'selected' : '' }}>
                            [{{ strtoupper($p->jenis_ujian) }}] {{ $p->nama }} ({{ $p->group }} - {{ $p->category }})
                        </option>
                    @endforeach
                </select>
                @error('tryout_package_id')<p class="form-error" style="color:#ef4444; font-size:0.75rem; margin-top:0.25rem;">{{ $message }}</p>@enderror
            </div>

            {{-- Grup Ujian --}}
            <div class="form-group">
                <label style="font-weight:600; display:block; margin-bottom:0.5rem;">Grup Ujian <span style="color:#ef4444;">*</span></label>
                <select name="group_id" id="group_id" class="form-control" required style="width:100%; padding:0.625rem; border-radius:6px; border:1px solid #cbd5e1;">
                    <option value="">-- Pilih Grup Ujian --</option>
                    @foreach($groups as $grp)
                        <option value="{{ $grp->id }}" {{ old('group_id') == $grp->id ? 'selected' : '' }}>{{ $grp->name }}</option>
                    @endforeach
                </select>
                @error('group_id')<p class="form-error" style="color:#ef4444; font-size:0.75rem; margin-top:0.25rem;">{{ $message }}</p>@enderror
            </div>
        </div>

        <div style="display:grid; grid-template-columns:1fr 1fr; gap:1rem; margin-bottom:1.5rem; background:#f8fafc; padding:1.25rem; border-radius:8px; border:1px solid #e2e8f0;">
            {{-- Kode Soal --}}
            <div class="form-group">
                <label style="font-weight:600; display:block; margin-bottom:0.5rem; font-size:0.85rem; color:#475569;">1. Kode Soal <span style="color:#ef4444;">*</span></label>
                <select name="question_code_id" id="question_code_id" class="form-control" required disabled style="width:100%; padding:0.5rem; border-radius:4px; border:1px solid #cbd5e1;">
                    <option value="">-- Pilih Kode Soal --</option>
                </select>
                @error('question_code_id')<p class="form-error" style="color:#ef4444; font-size:0.75rem; margin-top:0.25rem;">{{ $message }}</p>@enderror
            </div>

            {{-- Kategori --}}
            <div class="form-group">
                <label style="font-weight:600; display:block; margin-bottom:0.5rem; font-size:0.85rem; color:#475569;">2. Kategori <span style="color:#ef4444;">*</span></label>
                <select name="category_id" id="category_id" class="form-control" required disabled style="width:100%; padding:0.5rem; border-radius:4px; border:1px solid #cbd5e1;">
                    <option value="">-- Pilih Kategori --</option>
                </select>
                @error('category_id')<p class="form-error" style="color:#ef4444; font-size:0.75rem; margin-top:0.25rem;">{{ $message }}</p>@enderror
            </div>
        </div>

        <div style="display:grid; grid-template-columns:1fr 1fr; gap:1rem; margin-bottom:1.5rem;">
            {{-- Tingkat Kesulitan --}}
            <div class="form-group">
                <label style="font-weight:600; display:block; margin-bottom:0.5rem;">Tingkat Kesulitan <span style="color:#ef4444;">*</span></label>
                <select name="tingkat_kesulitan" class="form-control" required style="width:100%; padding:0.625rem; border-radius:6px; border:1px solid #cbd5e1;">
                    <option value="mudah"  {{ old('tingkat_kesulitan') == 'mudah'  ? 'selected' : '' }}>Mudah</option>
                    <option value="sedang" {{ old('tingkat_kesulitan','sedang') == 'sedang' ? 'selected' : '' }}>Sedang</option>
                    <option value="sulit"  {{ old('tingkat_kesulitan') == 'sulit'  ? 'selected' : '' }}>Sulit</option>
                </select>
            </div>
            
            {{-- Jawaban Benar --}}
            <div class="form-group">
                <label style="font-weight:600; display:block; margin-bottom:0.5rem;">Jawaban Benar <span style="color:#ef4444;">*</span></label>
                <select name="jawaban_benar" class="form-control" required style="width:100%; padding:0.625rem; border-radius:6px; border:1px solid #cbd5e1;">
                    @foreach(['A','B','C','D','E'] as $j)
                        <option value="{{ $j }}" {{ old('jawaban_benar') == $j ? 'selected' : '' }}>Opsi {{ $j }}</option>
                    @endforeach
                </select>
                @error('jawaban_benar')<p class="form-error" style="color:#ef4444; font-size:0.75rem; margin-top:0.25rem;">{{ $message }}</p>@enderror
            </div>
        </div>

        {{-- Soal / Pertanyaan --}}
        <div class="form-group" style="margin-bottom:1.25rem;">
            <label style="font-weight:600; display:block; margin-bottom:0.5rem;">Soal / Pertanyaan <span style="color:#ef4444;">*</span></label>
            <textarea name="soal" class="form-control" rows="3" placeholder="Tuliskan soal di sini (mendukung formula LaTeX/KaTeX seperti $x^2 + y^2 = z^2$ atau $$ \int x dx $$)..." style="width:100%; padding:0.625rem; border-radius:6px; border:1px solid #cbd5e1; font-family:inherit;">{{ old('soal') }}</textarea>
            @error('soal')<p class="form-error" style="color:#ef4444; font-size:0.75rem; margin-top:0.25rem;">{{ $message }}</p>@enderror
        </div>

        {{-- Gambar Soal --}}
        <div class="form-group" style="margin-bottom:1.5rem;">
            <label style="font-weight:600; display:block; margin-bottom:0.5rem;">Gambar Soal (Opsional)</label>
            <input type="file" name="question_image" class="form-control" accept="image/*" style="width:100%; padding:0.5rem; border-radius:6px; border:1px solid #cbd5e1;">
            @error('question_image')<p class="form-error" style="color:#ef4444; font-size:0.75rem; margin-top:0.25rem;">{{ $message }}</p>@enderror
        </div>

        <h4 style="font-weight:800; margin:2rem 0 1rem; color:#1e40af; font-size:1.05rem; border-bottom:2px solid #e2e8f0; padding-bottom:0.5rem;">Pilihan Jawaban (Opsi)</h4>

        {{-- Opsi A - E --}}
        <div style="display:grid; grid-template-columns:1fr; gap:1.25rem; margin-bottom:1.5rem;">
            @foreach(['a','b','c','d','e'] as $opsi)
            <div style="background:#f8fafc; border:1px solid #e2e8f0; padding:1rem 1.25rem; border-radius:8px;">
                <div style="display:grid; grid-template-columns: 2fr 2fr 1fr; gap:1rem;">
                    <div class="form-group" style="margin-bottom:0;">
                        <label style="font-weight:700; color:#334155;">Teks Opsi {{ strtoupper($opsi) }} {{ $opsi === 'e' ? '(Opsional)' : '' }}</label>
                        <input type="text" name="opsi_{{ $opsi }}" class="form-control" value="{{ old('opsi_'.$opsi) }}"
                            placeholder="Teks Pilihan {{ strtoupper($opsi) }}" style="width:100%; padding:0.625rem; border-radius:6px; border:1px solid #cbd5e1; margin-top:0.25rem;">
                        @error('opsi_'.$opsi)<p class="form-error" style="color:#ef4444; font-size:0.75rem; margin-top:0.25rem;">{{ $message }}</p>@enderror
                    </div>
                    <div class="form-group" style="margin-bottom:0;">
                        <label style="font-weight:700; color:#334155;">Gambar Opsi {{ strtoupper($opsi) }} (Opsional)</label>
                        <input type="file" name="option_{{ $opsi }}_image" class="form-control" accept="image/*" style="width:100%; padding:0.5rem; border-radius:6px; border:1px solid #cbd5e1; margin-top:0.25rem;">
                        @error('option_'.$opsi.'_image')<p class="form-error" style="color:#ef4444; font-size:0.75rem; margin-top:0.25rem;">{{ $message }}</p>@enderror
                    </div>
                    <div class="form-group" style="margin-bottom:0;">
                        <label style="font-weight:700; color:#334155;">Bobot Skor</label>
                        <input type="number" name="score_{{ $opsi }}" class="form-control" value="{{ old('score_'.$opsi, 0) }}" min="0" max="10" required style="width:100%; padding:0.625rem; border-radius:6px; border:1px solid #cbd5e1; margin-top:0.25rem;">
                        @error('score_'.$opsi)<p class="form-error" style="color:#ef4444; font-size:0.75rem; margin-top:0.25rem;">{{ $message }}</p>@enderror
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        <h4 style="font-weight:800; margin:2rem 0 1rem; color:#1e40af; font-size:1.05rem; border-bottom:2px solid #e2e8f0; padding-bottom:0.5rem;">Pembahasan & Penjelasan</h4>

        {{-- Pembahasan Editor --}}
        <div class="form-group" style="margin-bottom:1.5rem;">
            <label style="font-weight:600; display:block; margin-bottom:0.5rem;">Pembahasan (Rich Text & Matematika)</label>
            <div id="pembahasanEditor" style="height: 180px; background:#ffffff; border-radius:6px;">{!! old('pembahasan') !!}</div>
            <input type="hidden" name="pembahasan" id="pembahasanInput">
            @error('pembahasan')<p class="form-error" style="color:#ef4444; font-size:0.75rem; margin-top:0.25rem;">{{ $message }}</p>@enderror
        </div>

        {{-- Gambar Pembahasan --}}
        <div class="form-group" style="margin-bottom:2rem;">
            <label style="font-weight:600; display:block; margin-bottom:0.5rem;">Gambar Pembahasan (Opsional)</label>
            <input type="file" name="explanation_image" class="form-control" accept="image/*" style="width:100%; padding:0.5rem; border-radius:6px; border:1px solid #cbd5e1;">
            @error('explanation_image')<p class="form-error" style="color:#ef4444; font-size:0.75rem; margin-top:0.25rem;">{{ $message }}</p>@enderror
        </div>

        <div class="form-actions" style="margin-top:2rem; padding-top:1.25rem; border-top:1px solid #e2e8f0; display:flex; gap:0.75rem;">
            <button type="submit" class="btn btn-primary" style="padding:0.6rem 2rem; font-weight:700;">Simpan Soal</button>
            <a href="{{ route('admin.questions.index', ['tryout_package_id' => $defaultPackageId]) }}" class="btn btn-secondary" style="padding:0.6rem 2rem; font-weight:600;">Batal</a>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/quill@2.0.2/dist/quill.js"></script>
<script>
    // Dependent Dropdowns Logic
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
        // Old validation values check
        const oldGroup = "{{ old('group_id') }}";
        const oldCode = "{{ old('question_code_id') }}";
        const oldCat = "{{ old('category_id') }}";

        if (oldGroup) {
            loadCodes(oldGroup, oldCode).then(() => {
                if (oldCode) {
                    loadCategories(oldCode, oldCat);
                }
            });
        }

        // Quill Rich Text editor init
        var quill = new Quill('#pembahasanEditor', {
            theme: 'snow',
            modules: {
                toolbar: [
                    ['bold', 'italic', 'underline', 'strike'],
                    [{ 'list': 'ordered'}, { 'list': 'bullet' }],
                    ['clean']
                ]
            }
        });

        var form = document.querySelector('form');
        form.addEventListener('submit', function() {
            var input = document.getElementById('pembahasanInput');
            var text = quill.getText().trim();
            if (text === '') {
                input.value = '';
            } else {
                input.value = quill.root.innerHTML;
            }
        });
    });
</script>
@endpush
