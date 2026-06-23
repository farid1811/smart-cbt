@extends('admin.layouts.app')
@section('title', 'Impor Soal Massal (DOCX / PDF)')

@push('styles')
<style>
.tab-container {
    display: flex;
    border-bottom: 2px solid var(--border);
    margin-bottom: 1.5rem;
}
.tab-link {
    padding: 0.75rem 1.25rem;
    font-weight: 600;
    font-size: 0.88rem;
    color: var(--text-muted);
    cursor: pointer;
    border-bottom: 2px solid transparent;
    margin-bottom: -2px;
    transition: all var(--transition);
    text-decoration: none;
}
.tab-link:hover {
    color: var(--primary);
}
.tab-link.active {
    color: var(--primary);
    border-bottom-color: var(--primary);
}
.tab-content {
    display: none;
}
.tab-content.active {
    display: block;
}
</style>
@endpush

@section('content')
<div style="max-width: 680px; margin: 0 auto;">
    <div class="table-card" style="padding: 0;">
        <div class="table-header" style="padding: 1.5rem; border-bottom: 1px solid var(--border);">
            <div>
                <h3 style="font-weight: 700; font-size: 1.1rem; margin: 0 0 0.25rem;">Impor Soal Massal</h3>
                <p style="font-size: 0.8rem; color: var(--text-muted); margin: 0;">Gunakan halaman ini untuk mengimpor soal secara massal dari file Microsoft Word (.docx) atau berkas PDF (.pdf).</p>
            </div>
        </div>

        <div style="padding: 1.5rem 1.5rem 0;">
            <div class="tab-container">
                <div class="tab-link active" id="docx-tab" onclick="switchTab('docx')">Microsoft Word (.docx)</div>
                <div class="tab-link" id="pdf-tab" onclick="switchTab('pdf')">Berkas PDF (.pdf)</div>
            </div>
        </div>

        {{-- ─── TAB 1: WORD (DOCX) IMPORT ─── --}}
        <div class="tab-content active" id="docx-content">
            <form method="POST" action="{{ route('admin.questions.importPreview') }}" enctype="multipart/form-data" style="padding: 0 1.5rem 1.5rem;">
                @csrf

                {{-- Target Paket --}}
                <div class="form-group">
                    <label class="form-label" for="docx_tryout_package_id">Target Paket Ujian <span style="color:#ef4444;">*</span></label>
                    <select name="tryout_package_id" id="docx_tryout_package_id" class="form-control" required>
                        <option value="">-- Pilih Paket Ujian --</option>
                        @foreach($packages as $p)
                            <option value="{{ $p->id }}">
                                [{{ strtoupper($p->jenis_ujian) }}] {{ $p->nama }} ({{ $p->group }} - {{ $p->category }})
                            </option>
                        @endforeach
                    </select>
                    <small style="color:var(--text-muted); font-size:0.75rem; margin-top:0.25rem; display:block;">Pilih paket Drill atau Tryout yang akan ditambahkan soal ini.</small>
                </div>

                {{-- File DOCX --}}
                <div class="form-group" style="margin-bottom: 1.5rem;">
                    <label class="form-label" for="docx_file">Pilih File Word (.docx) <span style="color:#ef4444;">*</span></label>
                    <input type="file" name="file" id="docx_file" class="form-control" accept=".docx" required>
                </div>

                {{-- Panduan Format --}}
                <div style="background: var(--primary-soft); border: 1px solid var(--primary-mid); border-radius: 8px; padding: 1.25rem; margin-bottom: 1.5rem;">
                    <h4 style="font-weight: 700; font-size: 0.85rem; color: var(--primary); margin: 0 0 0.5rem; display: flex; align-items: center; gap: 0.35rem;">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12" y2="8"/></svg>
                        Panduan Format Penulisan File Word:
                    </h4>
                    <p style="font-size: 0.78rem; color: var(--text-muted); line-height: 1.5; margin: 0 0 0.75rem;">
                        Pastikan file Word Anda ditulis mengikuti pola di bawah ini. Gambar dapat disisipkan langsung di dalam teks pertanyaan maupun pembahasan.
                    </p>
                    <div style="background: #ffffff; border: 1px solid var(--border); border-radius: 6px; padding: 0.75rem 1rem; font-family: monospace; font-size: 0.75rem; color: var(--text); line-height: 1.4;">
                        SOAL: Pertanyaan nomor satu...<br>
                        (Masukkan gambar soal di sini jika ada)<br>
                        A. Pilihan jawaban A<br>
                        B. Pilihan jawaban B<br>
                        C. Pilihan jawaban C<br>
                        D. Pilihan jawaban D<br>
                        E. Pilihan jawaban E<br>
                        KUNCI: B<br>
                        PEMBAHASAN: Penjelasan pembahasan soal nomor satu...<br>
                        (Masukkan gambar pembahasan di sini jika ada)
                    </div>
                </div>

                <div style="display: flex; gap: 0.75rem; justify-content: flex-end; padding-top: 1.25rem; border-top: 1px solid var(--border);">
                    <a href="{{ route('admin.questions.index') }}" class="btn btn-secondary">Batal</a>
                    <button type="submit" class="btn btn-primary">
                        Unggah & Pratinjau DOCX
                    </button>
                </div>
            </form>
        </div>

        {{-- ─── TAB 2: PDF IMPORT ─── --}}
        <div class="tab-content" id="pdf-content">
            <form method="POST" action="{{ route('admin.questions.importPdfPreview') }}" enctype="multipart/form-data" style="padding: 0 1.5rem 1.5rem;">
                @csrf

                {{-- Target Paket --}}
                <div class="form-group">
                    <label class="form-label" for="pdf_tryout_package_id">Target Paket Ujian <span style="color:#ef4444;">*</span></label>
                    <select name="tryout_package_id" id="pdf_tryout_package_id" class="form-control" required>
                        <option value="">-- Pilih Paket Ujian --</option>
                        @foreach($packages as $p)
                            <option value="{{ $p->id }}">
                                [{{ strtoupper($p->jenis_ujian) }}] {{ $p->nama }} ({{ $p->group }} - {{ $p->category }})
                            </option>
                        @endforeach
                    </select>
                    <small style="color:var(--text-muted); font-size:0.75rem; margin-top:0.25rem; display:block;">Pilih paket Drill atau Tryout yang akan ditambahkan soal ini.</small>
                </div>

                {{-- File PDF --}}
                <div class="form-group" style="margin-bottom: 1.5rem;">
                    <label class="form-label" for="pdf_file">Pilih File PDF (.pdf) <span style="color:#ef4444;">*</span></label>
                    <input type="file" name="file" id="pdf_file" class="form-control" accept=".pdf" required>
                </div>

                {{-- Panduan Format --}}
                <div style="background: var(--warning-soft); border: 1px solid #FDE68A; border-radius: 8px; padding: 1.25rem; margin-bottom: 1.5rem;">
                    <h4 style="font-weight: 700; font-size: 0.85rem; color: var(--warning); margin: 0 0 0.5rem; display: flex; align-items: center; gap: 0.35rem;">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
                        Informasi & Panduan Impor PDF:
                    </h4>
                    <p style="font-size: 0.78rem; color: var(--text-muted); line-height: 1.5; margin: 0 0 0.75rem;">
                        Parser PDF akan membaca teks berbasis digital dalam berkas PDF Anda. Pola penulisan soal harus sama persis dengan panduan berkas Word. Gambar yang tertanam dalam PDF akan diekstraksi dan dilampirkan secara otomatis ke pertanyaan.
                    </p>
                    <div style="background: #ffffff; border: 1px solid var(--border); border-radius: 6px; padding: 0.75rem 1rem; font-family: monospace; font-size: 0.75rem; color: var(--text); line-height: 1.4;">
                        SOAL: Pertanyaan nomor satu...<br>
                        A. Pilihan jawaban A<br>
                        B. Pilihan jawaban B<br>
                        C. Pilihan jawaban C<br>
                        D. Pilihan jawaban D<br>
                        E. Pilihan jawaban E<br>
                        KUNCI: B<br>
                        PEMBAHASAN: Penjelasan pembahasan soal nomor satu...
                    </div>
                </div>

                <div style="display: flex; gap: 0.75rem; justify-content: flex-end; padding-top: 1.25rem; border-top: 1px solid var(--border);">
                    <a href="{{ route('admin.questions.index') }}" class="btn btn-secondary">Batal</a>
                    <button type="submit" class="btn btn-primary">
                        Unggah & Pratinjau PDF
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function switchTab(tabId) {
    document.querySelectorAll('.tab-link').forEach(el => el.classList.remove('active'));
    document.querySelectorAll('.tab-content').forEach(el => el.classList.remove('active'));
    
    document.getElementById(tabId + '-tab').classList.add('active');
    document.getElementById(tabId + '-content').classList.add('active');
}
</script>
@endpush
