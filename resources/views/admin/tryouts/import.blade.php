@extends('admin.layouts.app')
@section('title', 'Import Soal — ' . $tryout->nama)

@section('content')
<style>
    .import-container {
        max-width: 800px;
        margin: 0 auto;
    }
    .card {
        background: #fff;
        border-radius: 12px;
        box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05), 0 2px 4px -1px rgba(0,0,0,0.03);
        border: 1px solid #e2e8f0;
        margin-bottom: 2rem;
        overflow: hidden;
    }
    .card-header {
        padding: 1.5rem 2rem;
        border-bottom: 1px solid #e2e8f0;
        background: #f8fafc;
    }
    .card-body {
        padding: 2rem;
    }
    .tabs {
        display: flex;
        border-bottom: 2px solid #e2e8f0;
        margin-bottom: 2rem;
    }
    .tab-btn {
        flex: 1;
        text-align: center;
        background: none;
        border: none;
        padding: 1rem;
        font-weight: 700;
        color: #64748b;
        cursor: pointer;
        border-bottom: 2px solid transparent;
        margin-bottom: -2px;
        transition: all 0.2s ease;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
    }
    .tab-btn:hover {
        color: #1e40af;
    }
    .tab-btn.active {
        color: #1e40af;
        border-bottom-color: #1e40af;
    }
    .tab-content {
        display: none;
    }
    .tab-content.active {
        display: block;
    }
    .upload-area {
        border: 2px dashed #cbd5e1;
        border-radius: 10px;
        padding: 3rem 2rem;
        text-align: center;
        cursor: pointer;
        transition: all 0.2s;
        background: #f8fafc;
        position: relative;
    }
    .upload-area:hover, .upload-area.dragover {
        border-color: #3b82f6;
        background: #eff6ff;
    }
    .upload-icon {
        font-size: 3rem;
        color: #94a3b8;
        margin-bottom: 1rem;
        display: block;
    }
    .upload-text {
        font-weight: 600;
        color: #334155;
        margin-bottom: 0.25rem;
    }
    .upload-hint {
        color: #64748b;
        font-size: 0.8rem;
    }
    .file-input {
        position: absolute;
        top: 0; left: 0; width: 100%; height: 100%;
        opacity: 0;
        cursor: pointer;
    }
    .btn-submit {
        width: 100%;
        padding: 0.75rem;
        font-weight: 700;
        font-size: 1rem;
        margin-top: 1.5rem;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
    }
    .info-box {
        background: #eff6ff;
        border: 1px solid #bfdbfe;
        color: #1e40af;
        padding: 1rem;
        border-radius: 8px;
        margin-bottom: 1.5rem;
        font-size: 0.875rem;
        line-height: 1.5;
    }
    .info-title {
        font-weight: 700;
        margin-bottom: 0.25rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }
</style>

<div class="import-container">
    <div style="margin-bottom: 1.5rem;">
        <a href="{{ route('admin.tryouts.show', $tryout) }}" class="btn btn-secondary" style="font-weight:600; padding: 0.5rem 1rem;">
            ← Kembali ke Detail Paket
        </a>
    </div>

    <div style="margin-bottom: 2rem;">
        <h2 style="font-weight: 800; color: #0f172a; margin: 0;">Import Soal ke Paket</h2>
        <p style="color: #64748b; font-size: 0.9rem; margin-top: 0.25rem;">
            Paket: <strong>{{ $tryout->nama }}</strong> | Kategori: <strong>{{ $tryout->categoryRelation?->name ?? '—' }}</strong>
        </p>
    </div>

    <div class="card">
        <div class="tabs">
            <button class="tab-btn active" onclick="switchTab('word')">
                📄 Word (DOCX / ZIP)
            </button>
            <button class="tab-btn" onclick="switchTab('pdf')">
                📋 PDF (Tabel)
            </button>
        </div>

        <div class="card-body">
            <!-- TAB 1: WORD (DOCX) -->
            <div id="tab-word" class="tab-content active">
                <div class="info-box">
                    <div class="info-title">ℹ️ Panduan Import Word (DOCX)</div>
                    Dokumen Word harus memiliki tabel dengan struktur kolom minimal:
                    <ul style="margin: 0.5rem 0 0 1.25rem; padding: 0;">
                        <li><strong>No</strong>: Nomor urut soal</li>
                        <li><strong>Jenis</strong>: Isi dengan <code>SOAL</code>, <code>JAWABAN</code>, <code>PEMBAHASAN</code>, atau <code>KUNCI</code></li>
                        <li><strong>Isi</strong>: Teks soal, teks pilihan jawaban, pembahasan, atau huruf kunci jawaban (A/B/C/D/E)</li>
                        <li><strong>Jawaban</strong> (Opsional): Bobot skor untuk pilihan jawaban (e.g. 5, 0)</li>
                    </ul>
                    <div style="margin-top: 1rem; padding-top: 0.75rem; border-top: 1px solid #bfdbfe; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 0.5rem;">
                        <span style="font-weight: 500; font-size: 0.8rem; color: #1e40af;">
                            Belum memiliki format Word yang sesuai? Silakan unduh Template Word terlebih dahulu agar proses import berjalan dengan benar.
                        </span>
                        <a href="{{ route('admin.tryouts.import.template.word') }}" class="btn btn-secondary btn-sm" style="font-weight:700; font-size:0.75rem; background:#ffffff; border:1px solid #bfdbfe; color:#1e40af; display:inline-flex; align-items:center; gap:0.25rem; text-decoration:none; padding: 0.25rem 0.5rem; border-radius: 4px;">
                            ⬇ Download Template Word (.docx)
                        </a>
                    </div>
                </div>

                <form method="POST" action="{{ route('admin.tryouts.import.word', $tryout) }}" enctype="multipart/form-data">
                    @csrf
                    <div class="upload-area" id="word-upload-area">
                        <span class="upload-icon">📁</span>
                        <div class="upload-text" id="word-upload-text">Pilih file Word atau seret ke sini</div>
                        <div class="upload-hint">Format file yang didukung: .docx, .zip (maksimal 10MB)</div>
                        <input type="file" name="file" class="file-input" accept=".docx,.zip" onchange="fileSelected(this, 'word-upload-text')" required>
                    </div>
                    <button type="submit" class="btn btn-primary btn-submit">Pratinjau & Proses Word →</button>
                </form>
            </div>

            <!-- TAB 2: PDF -->
            <div id="tab-pdf" class="tab-content">
                <div class="info-box" style="background:#f0fdf4; border-color:#bbf7d0; color:#15803d;">
                    <div class="info-title" style="color:#15803d;">ℹ️ Panduan Import PDF</div>
                    Dokumen PDF harus berbasis teks (bukan hasil scan gambar) dan memiliki format tabel baris per baris yang dipisahkan dengan karakter pipa (<code>|</code>) atau menggunakan format template resmi:
                    <ul style="margin: 0.5rem 0 0 1.25rem; padding: 0;">
                        <li>Format baris: <code>No | Jenis | Isi | Jawaban</code></li>
                        <li>Pastikan teks di dalam PDF terbaca dengan baik sebelum diupload.</li>
                    </ul>
                </div>

                <form method="POST" action="{{ route('admin.tryouts.import.pdf', $tryout) }}" enctype="multipart/form-data">
                    @csrf
                    <div class="upload-area" id="pdf-upload-area">
                        <span class="upload-icon">📄</span>
                        <div class="upload-text" id="pdf-upload-text">Pilih file PDF atau seret ke sini</div>
                        <div class="upload-hint">Format file yang didukung: .pdf (maksimal 10MB)</div>
                        <input type="file" name="file" class="file-input" accept=".pdf" onchange="fileSelected(this, 'pdf-upload-text')" required>
                    </div>
                    <button type="submit" class="btn btn-primary btn-submit">Pratinjau & Proses PDF →</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    function switchTab(tabName) {
        document.querySelectorAll('.tab-btn').forEach(btn => btn.classList.remove('active'));
        document.querySelectorAll('.tab-content').forEach(content => content.classList.remove('active'));
        
        const activeBtn = Array.from(document.querySelectorAll('.tab-btn')).find(btn => btn.textContent.toLowerCase().includes(tabName));
        if (activeBtn) activeBtn.classList.add('active');
        
        const activeContent = document.getElementById('tab-' + tabName);
        if (activeContent) activeContent.classList.add('active');
    }

    function fileSelected(input, textId) {
        if (input.files && input.files.length > 0) {
            const fileName = input.files[0].name;
            document.getElementById(textId).textContent = "File terpilih: " + fileName;
            document.getElementById(textId).style.color = "#1e40af";
        }
    }

    // Drag and drop styles
    document.querySelectorAll('.upload-area').forEach(area => {
        area.addEventListener('dragover', function(e) {
            e.preventDefault();
            this.classList.add('dragover');
        });
        area.addEventListener('dragleave', function() {
            this.classList.remove('dragover');
        });
        area.addEventListener('drop', function() {
            this.classList.remove('dragover');
        });
    });
</script>
@endsection
