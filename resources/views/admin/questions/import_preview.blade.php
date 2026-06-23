@extends('admin.layouts.app')
@section('title', 'Pratinjau Impor Soal')

@section('content')
<div style="max-width: 960px; margin: 0 auto;">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:1.5rem;">
        <div>
            <h2 style="font-weight: 800; font-size: 1.35rem; margin: 0 0 0.25rem;">Pratinjau Hasil Impor</h2>
            <p style="font-size: 0.85rem; color: var(--text-muted); margin: 0;">
                Target Paket: <strong style="color:var(--primary);">{{ $package->nama }}</strong> (Grup: <strong>{{ $package->group }}</strong>) | Jumlah: <strong>{{ count($questions) }} Soal</strong>
            </p>
        </div>
        <div style="display: flex; gap: 0.75rem;">
            <a href="{{ route('admin.questions.importForm') }}" class="btn btn-secondary">Ulangi Upload</a>
        </div>
    </div>

    <!-- Warnings List -->
    @if(session('temp_import_warnings') && count(session('temp_import_warnings')) > 0)
        <div class="alert alert-warning" style="background:#fffbeb; border:1px solid #fef3c7; color:#b45309; padding:1rem; border-radius:8px; margin-bottom:1.5rem;">
            <h5 style="margin:0 0 0.5rem; font-weight:700;">⚠️ Peringatan Ekstraksi Gambar:</h5>
            <ul style="margin:0; padding-left:1.25rem; font-size:0.85rem; line-height:1.4;">
                @foreach(session('temp_import_warnings') as $warning)
                    <li>{{ $warning }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('admin.questions.importConfirm') }}">
        @csrf

        @foreach($questions as $index => $q)
        <div class="table-card" style="margin-bottom: 2rem; padding: 1.5rem; border-radius: 12px; background:#fff; box-shadow:0 4px 6px -1px rgba(0,0,0,0.05);">
            <div style="display:grid; grid-template-columns: 1fr; gap:1rem; border-bottom:1px solid #f1f5f9; padding-bottom:1rem; margin-bottom:1.25rem;">
                <div style="display:flex; justify-content:space-between; align-items:center;">
                    <h4 style="margin:0; font-weight:700; color:#0f172a; font-size:1.1rem;">Soal #{{ $index + 1 }}</h4>
                    
                    {{-- Tingkat Kesulitan --}}
                    <div>
                        <select name="q[{{ $index }}][tingkat_kesulitan]" class="form-control" style="font-size:0.8rem; padding:0.35rem 0.6rem; width:120px;" required>
                            <option value="mudah" {{ $q['tingkat_kesulitan'] === 'mudah' ? 'selected' : '' }}>Mudah</option>
                            <option value="sedang" {{ $q['tingkat_kesulitan'] === 'sedang' ? 'selected' : '' }}>Sedang</option>
                            <option value="sulit" {{ $q['tingkat_kesulitan'] === 'sulit' ? 'selected' : '' }}>Sulit</option>
                        </select>
                    </div>
                </div>

                {{-- 4-Level Hierarchy Dropdowns --}}
                <div style="display:grid; grid-template-columns: repeat(3, 1fr); gap:1rem; background:#f8fafc; padding:1rem; border-radius:8px;">
                    <div>
                        <label style="font-size:0.75rem; font-weight:700; color:#475569; display:block; margin-bottom:0.25rem;">1. Kode Soal</label>
                        <select name="q[{{ $index }}][question_code_id]" id="code_select_{{ $index }}" onchange="loadCategories({{ $index }}, this.value)" class="form-control" style="font-size:0.8rem; padding:0.4rem;" required>
                            <option value="">-- Pilih Kode Soal --</option>
                            @foreach($codes as $code)
                                <option value="{{ $code->id }}">{{ $code->code }} - {{ $code->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label style="font-size:0.75rem; font-weight:700; color:#475569; display:block; margin-bottom:0.25rem;">2. Kategori</label>
                        <select name="q[{{ $index }}][category_id]" id="category_select_{{ $index }}" onchange="loadSubCategories({{ $index }}, this.value)" class="form-control" style="font-size:0.8rem; padding:0.4rem;" required disabled>
                            <option value="">-- Pilih Kategori --</option>
                        </select>
                    </div>
                    <div>
                        <label style="font-size:0.75rem; font-weight:700; color:#475569; display:block; margin-bottom:0.25rem;">3. Sub Kategori</label>
                        <select name="q[{{ $index }}][sub_category_id]" id="subcategory_select_{{ $index }}" class="form-control" style="font-size:0.8rem; padding:0.4rem;" required disabled>
                            <option value="">-- Pilih Sub Kategori --</option>
                        </select>
                    </div>
                </div>
            </div>

            {{-- Soal Teks & Gambar --}}
            <div class="form-group" style="margin-bottom: 1.25rem;">
                <label class="form-label" style="font-weight:700; font-size:0.9rem; color:#334155;">Isi Soal</label>
                <textarea name="q[{{ $index }}][soal]" class="form-control" rows="3" required style="font-size:0.88rem; width:100%; border-radius:6px; border:1px solid #cbd5e1; padding:0.5rem;">{{ $q['soal'] }}</textarea>
                @if($q['question_image'])
                    <div style="margin-top:0.75rem; display:flex; align-items:center; gap:0.5rem;">
                        <img src="{{ asset($q['question_image']) }}" alt="Soal Gambar" style="max-height:120px; border-radius:6px; border:1px solid #cbd5e1;">
                        <span style="font-size:0.75rem; color:#64748b;">[Gambar Soal Terdeteksi]</span>
                    </div>
                @endif
            </div>

            {{-- Opsi A - E --}}
            <div style="display:grid; grid-template-columns:1fr; gap:0.75rem; margin-bottom:1.25rem;">
                @foreach(['a', 'b', 'c', 'd', 'e'] as $opt)
                <div style="display:flex; align-items:flex-start; gap:0.5rem; border:1px solid #e2e8f0; padding:0.75rem; border-radius:8px; background:#f8fafc;">
                    <span style="font-weight:700; color:#1e40af; font-size:0.9rem; margin-top:0.4rem; width:20px;">{{ strtoupper($opt) }}</span>
                    <div style="flex:1;">
                        <input type="text" name="q[{{ $index }}][opsi_{{ $opt }}]" class="form-control" value="{{ $q['opsi_' . $opt] }}" style="font-size:0.85rem; width:100%; padding:0.4rem; border-radius:4px; border:1px solid #cbd5e1;" {{ $opt !== 'e' ? 'required' : '' }} placeholder="Opsi {{ strtoupper($opt) }} (Opsional)">
                        @if($q['option_' . $opt . '_image'])
                            <div style="margin-top:0.5rem; display:flex; align-items:center; gap:0.5rem;">
                                <img src="{{ asset($q['option_' . $opt . '_image']) }}" alt="Opsi {{ strtoupper($opt) }} Gambar" style="max-height:80px; border-radius:4px; border:1px solid #cbd5e1;">
                                <span style="font-size:0.7rem; color:#64748b;">[Gambar Opsi {{ strtoupper($opt) }}]</span>
                            </div>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>

            {{-- Kunci & Pembahasan --}}
            <div style="display:grid; grid-template-columns: 140px 1fr; gap:1rem;">
                <div class="form-group">
                    <label class="form-label" style="font-weight:700; font-size:0.9rem; color:#334155;">Kunci Jawaban</label>
                    <select name="q[{{ $index }}][jawaban_benar]" class="form-control" required style="font-weight:700; color:#1e40af; padding:0.4rem; border-radius:4px; border:1px solid #cbd5e1; width:100%;">
                        @foreach(['A', 'B', 'C', 'D', 'E'] as $ans)
                            <option value="{{ $ans }}" {{ $q['jawaban_benar'] === $ans ? 'selected' : '' }}>Opsi {{ $ans }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label" style="font-weight:700; font-size:0.9rem; color:#334155;">Pembahasan / Penjelasan</label>
                    <textarea name="q[{{ $index }}][pembahasan]" class="form-control" rows="2" style="font-size:0.85rem; width:100%; border-radius:6px; border:1px solid #cbd5e1; padding:0.5rem;" placeholder="Penjelasan pembahasan soal...">{{ $q['pembahasan'] }}</textarea>
                    @if($q['explanation_image'])
                        <div style="margin-top:0.5rem; display:flex; align-items:center; gap:0.5rem;">
                            <img src="{{ asset($q['explanation_image']) }}" alt="Pembahasan Gambar" style="max-height:100px; border-radius:4px; border:1px solid #cbd5e1;">
                            <span style="font-size:0.7rem; color:#64748b;">[Gambar Pembahasan]</span>
                        </div>
                    @endif
                </div>
            </div>
        </div>
        @endforeach

        <div style="position: sticky; bottom: 1.5rem; background: rgba(255,255,255,0.95); backdrop-filter: blur(8px); border: 1px solid #e2e8f0; border-radius: 12px; padding: 1.25rem; display: flex; justify-content: space-between; align-items: center; box-shadow: 0 10px 15px -3px rgba(0,0,0,0.1); z-index:100;">
            <div style="font-size: 0.88rem; color: #64748b; font-weight: 500;">
                Tinjau kembali semua soal di atas sebelum menekan tombol simpan.
            </div>
            <div style="display:flex; gap:0.75rem;">
                <a href="{{ route('admin.questions.importForm') }}" class="btn btn-secondary">Batal</a>
                <button type="submit" class="btn btn-primary" style="padding:0.6rem 1.5rem; font-weight:700;">
                    Simpan Semua Soal ke Database
                </button>
            </div>
        </div>
    </form>
</div>

<!-- Dynamic Dependent Dropdowns Fetch Logic -->
<script>
    async function loadCategories(index, codeId) {
        const catSelect = document.getElementById(`category_select_${index}`);
        const subSelect = document.getElementById(`subcategory_select_${index}`);
        
        // Reset and disable dependent inputs
        catSelect.innerHTML = '<option value="">-- Pilih Kategori --</option>';
        catSelect.disabled = true;
        subSelect.innerHTML = '<option value="">-- Pilih Sub Kategori --</option>';
        subSelect.disabled = true;

        if (!codeId) return;

        try {
            const response = await fetch(`/admin/api/categories/${codeId}`);
            const categories = await response.json();
            
            if (categories.length > 0) {
                categories.forEach(cat => {
                    const opt = document.createElement('option');
                    opt.value = cat.id;
                    opt.textContent = cat.name;
                    catSelect.appendChild(opt);
                });
                catSelect.disabled = false;
            } else {
                catSelect.innerHTML = '<option value="">(Tidak ada kategori)</option>';
            }
        } catch (error) {
            console.error('Error fetching categories:', error);
        }
    }

    async function loadSubCategories(index, categoryId) {
        const subSelect = document.getElementById(`subcategory_select_${index}`);
        
        // Reset and disable subcategory input
        subSelect.innerHTML = '<option value="">-- Pilih Sub Kategori --</option>';
        subSelect.disabled = true;

        if (!categoryId) return;

        try {
            const response = await fetch(`/admin/api/subcategories/${categoryId}`);
            const subCategories = await response.json();
            
            if (subCategories.length > 0) {
                subCategories.forEach(sub => {
                    const opt = document.createElement('option');
                    opt.value = sub.id;
                    opt.textContent = sub.name;
                    subSelect.appendChild(opt);
                });
                subSelect.disabled = false;
            } else {
                subSelect.innerHTML = '<option value="">(Tidak ada sub kategori)</option>';
            }
        } catch (error) {
            console.error('Error fetching subcategories:', error);
        }
    }

    // Auto-select first code option for all question blocks on page load
    document.addEventListener("DOMContentLoaded", function() {
        @foreach($questions as $index => $q)
            const codeSelect = document.getElementById(`code_select_{{ $index }}`);
            if (codeSelect && codeSelect.options.length > 1) {
                codeSelect.selectedIndex = 1; // select first real option
                loadCategories({{ $index }}, codeSelect.value);
            }
        @endforeach
    });
</script>
@endsection
