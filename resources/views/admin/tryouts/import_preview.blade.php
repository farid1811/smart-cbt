@extends('admin.layouts.app')
@section('title', 'Pratinjau Impor Soal')

@section('content')
<style>
    .preview-container {
        max-width: 1000px;
        margin: 0 auto;
    }
    .warning-card {
        background: #fffbeb;
        border: 1px solid #fef3c7;
        color: #b45309;
        padding: 1rem 1.5rem;
        border-radius: 8px;
        margin-bottom: 1.5rem;
        font-size: 0.9rem;
    }
    .question-card {
        background: #fff;
        border-radius: 12px;
        border: 1px solid #e2e8f0;
        box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05);
        margin-bottom: 2rem;
        padding: 1.5rem 2rem;
    }
    .question-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        border-bottom: 1px solid #f1f5f9;
        padding-bottom: 1rem;
        margin-bottom: 1.5rem;
    }
    .question-num {
        font-size: 1.15rem;
        font-weight: 800;
        color: #1e3a8a;
    }
    .field-group {
        margin-bottom: 1.25rem;
    }
    .field-group label {
        display: block;
        font-weight: 700;
        font-size: 0.85rem;
        color: #475569;
        margin-bottom: 0.5rem;
    }
    .form-control {
        width: 100%;
        padding: 0.625rem 0.875rem;
        border: 1px solid #cbd5e1;
        border-radius: 6px;
        font-size: 0.9rem;
        transition: all 0.2s;
    }
    .form-control:focus {
        border-color: #3b82f6;
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.15);
        outline: none;
    }
    .options-grid {
        display: grid;
        grid-template-columns: 1fr;
        gap: 1rem;
        margin-bottom: 1.5rem;
    }
    .option-row {
        display: flex;
        gap: 0.75rem;
        align-items: center;
    }
    .option-label {
        font-weight: 800;
        color: #1e3a8a;
        width: 24px;
        text-align: center;
        font-size: 1.1rem;
    }
    .option-score {
        width: 80px;
        flex-shrink: 0;
    }
    .image-preview {
        margin-top: 0.5rem;
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }
    .image-preview img {
        max-height: 100px;
        border-radius: 6px;
        border: 1px solid #e2e8f0;
    }
    .image-badge {
        background: #f1f5f9;
        color: #475569;
        font-size: 0.7rem;
        font-weight: 700;
        padding: 0.2rem 0.5rem;
        border-radius: 4px;
    }
    .submit-bar {
        position: sticky;
        bottom: 0;
        background: rgba(255, 255, 255, 0.9);
        backdrop-filter: blur(8px);
        border-top: 1px solid #e2e8f0;
        padding: 1.25rem;
        display: flex;
        justify-content: space-between;
        align-items: center;
        z-index: 100;
        margin-top: 2rem;
        border-radius: 12px;
        box-shadow: 0 -4px 10px rgba(0,0,0,0.05);
    }
</style>

<div class="preview-container">
    <div style="margin-bottom: 1.5rem; display: flex; justify-content: space-between; align-items: center;">
        <h2 style="font-weight: 800; color: #0f172a; margin: 0;">Pratinjau Impor Soal</h2>
        <a href="{{ route('admin.tryouts.import.form', $tryout) }}" class="btn btn-secondary" style="font-weight:600;">
            ← Batal
        </a>
    </div>

    <div style="margin-bottom: 2rem; background:#fff; padding:1.25rem 1.5rem; border-radius:10px; border:1px solid #e2e8f0;">
        <span style="color:#64748b; font-size:0.85rem; text-transform:uppercase; font-weight:700; display:block; margin-bottom:0.25rem;">Impor ke Paket:</span>
        <span style="font-size:1.25rem; font-weight:800; color:#0f172a;">{{ $tryout->nama }}</span>
        <span style="color:#94a3b8; margin: 0 0.5rem;">|</span>
        <span style="font-weight:600; color:#475569;">Total: {{ count($questions) }} Soal Terdeteksi</span>
    </div>

    @if(!empty($warnings))
        <div class="warning-card">
            <h4 style="font-weight: 700; margin: 0 0 0.5rem 0; display: flex; align-items: center; gap: 0.5rem;">
                ⚠️ Catatan & Peringatan Impor:
            </h4>
            <ul style="margin: 0; padding-left: 1.25rem;">
                @foreach($warnings as $warn)
                    <li>{{ $warn }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form id="import-confirm-form" method="POST" action="{{ route('admin.tryouts.import.confirm', $tryout) }}">
        @csrf
        <input type="hidden" name="questions_json" id="questions_json">
        
        @foreach($questions as $index => $q)
            <div class="question-card">
                <div class="question-header">
                    <span class="question-num">Soal #{{ $index + 1 }}</span>
                    <div style="display: flex; gap: 0.75rem; align-items: center;">
                        <span class="image-badge">Tingkat Kesulitan:</span>
                        <select name="q[{{ $index }}][tingkat_kesulitan]" class="form-control" style="width: 110px; padding: 0.35rem 0.5rem; font-size: 0.8rem; font-weight: 600;">
                            <option value="mudah" {{ ($q['tingkat_kesulitan'] ?? 'sedang') == 'mudah' ? 'selected' : '' }}>Mudah</option>
                            <option value="sedang" {{ ($q['tingkat_kesulitan'] ?? 'sedang') == 'sedang' ? 'selected' : '' }}>Sedang</option>
                            <option value="sulit" {{ ($q['tingkat_kesulitan'] ?? 'sedang') == 'sulit' ? 'selected' : '' }}>Sulit</option>
                        </select>
                    </div>
                </div>

                <!-- Teks Soal -->
                <div class="field-group">
                    <label>Isi Pertanyaan Soal</label>
                    <textarea name="q[{{ $index }}][soal]" class="form-control" rows="3" required>{{ $q['soal'] }}</textarea>
                    @if(!empty($q['question_image']))
                        <div class="image-preview">
                            <img src="{{ asset($q['question_image']) }}" alt="Gambar Soal">
                            <span class="image-badge">📷 Gambar Soal Terdeteksi</span>
                        </div>
                    @endif
                </div>

                <!-- Opsi Jawaban -->
                <div class="field-group">
                    <label>Pilihan Jawaban & Skor</label>
                    <div class="options-grid">
                        @foreach(['a', 'b', 'c', 'd', 'e'] as $lbl)
                            <div class="option-row">
                                <span class="option-label">{{ strtoupper($lbl) }}</span>
                                <input type="text" name="q[{{ $index }}][opsi_{{ $lbl }}]" class="form-control" placeholder="Pilihan {{ strtoupper($lbl) }}" value="{{ $q['opsi_' . $lbl] ?? '' }}" {{ $lbl !== 'e' ? 'required' : '' }}>
                                <input type="number" name="q[{{ $index }}][score_{{ $lbl }}]" class="form-control option-score" placeholder="Skor" value="{{ $q['score_' . $lbl] ?? 0 }}" required min="0" max="100">
                            </div>
                            @if(!empty($q['option_' . $lbl . '_image']))
                                <div class="image-preview" style="margin-left: 2.5rem; margin-bottom: 0.5rem;">
                                    <img src="{{ asset($q['option_' . $lbl . '_image']) }}" alt="Gambar Opsi {{ strtoupper($lbl) }}">
                                    <span class="image-badge">📷 Gambar Opsi {{ strtoupper($lbl) }} Terdeteksi</span>
                                </div>
                            @endif
                        @endforeach
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 180px 1fr; gap: 1.5rem;">
                    <!-- Kunci Jawaban -->
                    <div class="field-group">
                        <label>Kunci Jawaban</label>
                        <select name="q[{{ $index }}][jawaban_benar]" class="form-control" style="font-weight: 700;">
                            @foreach(['A', 'B', 'C', 'D', 'E'] as $letter)
                                <option value="{{ $letter }}" {{ ($q['jawaban_benar'] ?? 'A') === $letter ? 'selected' : '' }}>Pilihan {{ $letter }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Pembahasan -->
                    <div class="field-group">
                        <label>Pembahasan</label>
                        <textarea name="q[{{ $index }}][pembahasan]" class="form-control" rows="2" placeholder="Masukkan pembahasan soal...">{{ $q['pembahasan'] ?? '' }}</textarea>
                        @if(!empty($q['explanation_image']))
                            <div class="image-preview">
                                <img src="{{ asset($q['explanation_image']) }}" alt="Gambar Pembahasan">
                                <span class="image-badge">📷 Gambar Pembahasan Terdeteksi</span>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        @endforeach

        <!-- STICKY SUBMIT BAR -->
        <div class="submit-bar">
            <div>
                <span style="font-size:0.85rem; color:#64748b; display:block;">Sebelum menyimpan:</span>
                <span style="font-size:0.95rem; font-weight:700; color:#334155;">Mohon periksa kecocokan kunci jawaban & skor di atas.</span>
            </div>
            <div style="display:flex; gap:0.75rem;">
                <a href="{{ route('admin.tryouts.import.form', $tryout) }}" class="btn btn-secondary" style="font-weight:600; padding:0.75rem 1.5rem;">Batal</a>
                <button type="submit" class="btn btn-primary" style="font-weight:800; padding:0.75rem 2rem;">💾 Simpan Semua Soal ke Paket</button>
            </div>
        </div>
    </form>
</div>

<script>
document.getElementById('import-confirm-form').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const form = this;
    const questions = [];
    const totalQuestions = {{ count($questions) }};
    
    for (let i = 0; i < totalQuestions; i++) {
        const qData = {};
        
        // Difficulty
        const difficultyEl = form.querySelector(`[name="q[${i}][tingkat_kesulitan]"]`);
        qData.tingkat_kesulitan = difficultyEl ? difficultyEl.value : 'sedang';
        
        // Soal
        const soalEl = form.querySelector(`[name="q[${i}][soal]"]`);
        qData.soal = soalEl ? soalEl.value : '';
        
        // Options & Scores
        const labels = ['a', 'b', 'c', 'd', 'e'];
        labels.forEach(lbl => {
            const opsiEl = form.querySelector(`[name="q[${i}][opsi_${lbl}]"]`);
            qData[`opsi_${lbl}`] = opsiEl ? opsiEl.value : '';
            
            const scoreEl = form.querySelector(`[name="q[${i}][score_${lbl}]"]`);
            qData[`score_${lbl}`] = scoreEl ? parseInt(scoreEl.value) || 0 : 0;
        });
        
        // Jawaban Benar
        const jawabanEl = form.querySelector(`[name="q[${i}][jawaban_benar]"]`);
        qData.jawaban_benar = jawabanEl ? jawabanEl.value : 'A';
        
        // Pembahasan
        const pembahasanEl = form.querySelector(`[name="q[${i}][pembahasan]"]`);
        qData.pembahasan = pembahasanEl ? pembahasanEl.value : '';
        
        questions.push(qData);
    }
    
    // Set the JSON string in the hidden input
    document.getElementById('questions_json').value = JSON.stringify(questions);
    
    // Remove the name attribute from all inputs inside the form except the csrf token and questions_json
    // to prevent the browser from sending thousands of parameters and hitting max_input_vars!
    const inputs = form.querySelectorAll('input, select, textarea');
    inputs.forEach(input => {
        if (input.name && input.name !== '_token' && input.name !== 'questions_json') {
            input.removeAttribute('name');
        }
    });
    
    // Now submit the form
    form.submit();
});
</script>
@endsection
