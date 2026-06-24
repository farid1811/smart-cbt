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
                        Panduan Format Template Word (Tabel Resmi):
                    </h4>
                    <p style="font-size: 0.78rem; color: var(--text-muted); line-height: 1.5; margin: 0 0 0.75rem;">
                        File Word harus berisi tabel dengan 4 kolom utama: <strong>No</strong>, <strong>Jenis</strong>, <strong>Isi</strong>, dan <strong>Jawaban</strong>. Gambar dan teks dapat digabungkan langsung di dalam kolom <strong>Isi</strong>.
                    </p>
                    <div style="overflow-x: auto; background: #ffffff; border: 1px solid var(--border); border-radius: 6px; padding: 0.5rem; margin-bottom: 0.5rem;">
                        <table style="width: 100%; border-collapse: collapse; font-size: 0.72rem; font-family: monospace; text-align: left;">
                            <thead>
                                <tr style="background: #f1f5f9; border-bottom: 1px solid #cbd5e1;">
                                    <th style="padding: 0.4rem; font-weight: 700;">No</th>
                                    <th style="padding: 0.4rem; font-weight: 700;">Jenis</th>
                                    <th style="padding: 0.4rem; font-weight: 700;">Isi</th>
                                    <th style="padding: 0.4rem; font-weight: 700;">Jawaban</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr style="border-bottom: 1px solid #e2e8f0;">
                                    <td style="padding: 0.4rem;">1</td>
                                    <td style="padding: 0.4rem; font-weight: 700; color: #1e40af;">SOAL</td>
                                    <td style="padding: 0.4rem;">What is the capital city of Indonesia? [Teks & Gambar]</td>
                                    <td style="padding: 0.4rem; color: #94a3b8;">[Kosong]</td>
                                </tr>
                                <tr style="border-bottom: 1px solid #e2e8f0;">
                                    <td style="padding: 0.4rem;"></td>
                                    <td style="padding: 0.4rem; font-weight: 700; color: #059669;">JAWABAN</td>
                                    <td style="padding: 0.4rem;">Jakarta</td>
                                    <td style="padding: 0.4rem; font-weight: 700; color: #1e40af;">1</td>
                                </tr>
                                <tr style="border-bottom: 1px solid #e2e8f0;">
                                    <td style="padding: 0.4rem;"></td>
                                    <td style="padding: 0.4rem; font-weight: 700; color: #059669;">JAWABAN</td>
                                    <td style="padding: 0.4rem;">Bandung</td>
                                    <td style="padding: 0.4rem; font-weight: 700;">0</td>
                                </tr>
                                <tr style="border-bottom: 1px solid #e2e8f0;">
                                    <td style="padding: 0.4rem;"></td>
                                    <td style="padding: 0.4rem; font-weight: 700; color: #059669;">JAWABAN</td>
                                    <td style="padding: 0.4rem;">Medan</td>
                                    <td style="padding: 0.4rem; font-weight: 700;">0</td>
                                </tr>
                                <tr style="border-bottom: 1px solid #e2e8f0;">
                                    <td style="padding: 0.4rem;"></td>
                                    <td style="padding: 0.4rem; font-weight: 700; color: #059669;">JAWABAN</td>
                                    <td style="padding: 0.4rem;">Surabaya</td>
                                    <td style="padding: 0.4rem; font-weight: 700;">0</td>
                                </tr>
                                <tr style="border-bottom: 1px solid #e2e8f0;">
                                    <td style="padding: 0.4rem;"></td>
                                    <td style="padding: 0.4rem; font-weight: 700; color: #059669;">JAWABAN</td>
                                    <td style="padding: 0.4rem;">Makassar</td>
                                    <td style="padding: 0.4rem; font-weight: 700;">0</td>
                                </tr>
                                <tr>
                                    <td style="padding: 0.4rem;"></td>
                                    <td style="padding: 0.4rem; font-weight: 700; color: #7c3aed;">PEMBAHASAN</td>
                                    <td style="padding: 0.4rem;">Jakarta is the capital city. [Teks & Gambar]</td>
                                    <td style="padding: 0.4rem; color: #94a3b8;">[Kosong]</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div style="display: flex; gap: 0.75rem; justify-content: flex-end; padding-top: 1.25rem; border-top: 1px solid var(--border);">
                    <a href="{{ route('admin.questions.index') }}" class="btn btn-secondary">Batal</a>
                    <button type="submit" class="btn btn-primary">
                        Unggah & Pratinjau Word
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
                <div style="background: var(--primary-soft); border: 1px solid var(--primary-mid); border-radius: 8px; padding: 1.25rem; margin-bottom: 1.5rem;">
                    <h4 style="font-weight: 700; font-size: 0.85rem; color: var(--primary); margin: 0 0 0.5rem; display: flex; align-items: center; gap: 0.35rem;">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12" y2="8"/></svg>
                        Panduan Format Template PDF (Tabel Resmi):
                    </h4>
                    <p style="font-size: 0.78rem; color: var(--text-muted); line-height: 1.5; margin: 0 0 0.75rem;">
                        Berkas PDF harus berisi tabel dengan 4 kolom utama: <strong>No</strong>, <strong>Jenis</strong>, <strong>Isi</strong>, dan <strong>Jawaban</strong>. Gambar dan teks di dalam tabel akan diekstraksi secara otomatis dan dipetakan sesuai urutan.
                    </p>
                    <div style="overflow-x: auto; background: #ffffff; border: 1px solid var(--border); border-radius: 6px; padding: 0.5rem; margin-bottom: 0.5rem;">
                        <table style="width: 100%; border-collapse: collapse; font-size: 0.72rem; font-family: monospace; text-align: left;">
                            <thead>
                                <tr style="background: #f1f5f9; border-bottom: 1px solid #cbd5e1;">
                                    <th style="padding: 0.4rem; font-weight: 700;">No</th>
                                    <th style="padding: 0.4rem; font-weight: 700;">Jenis</th>
                                    <th style="padding: 0.4rem; font-weight: 700;">Isi</th>
                                    <th style="padding: 0.4rem; font-weight: 700;">Jawaban</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr style="border-bottom: 1px solid #e2e8f0;">
                                    <td style="padding: 0.4rem;">1</td>
                                    <td style="padding: 0.4rem; font-weight: 700; color: #1e40af;">SOAL</td>
                                    <td style="padding: 0.4rem;">What is the capital city of Indonesia? [Teks & Gambar]</td>
                                    <td style="padding: 0.4rem; color: #94a3b8;">[Kosong]</td>
                                </tr>
                                <tr style="border-bottom: 1px solid #e2e8f0;">
                                    <td style="padding: 0.4rem;"></td>
                                    <td style="padding: 0.4rem; font-weight: 700; color: #059669;">JAWABAN</td>
                                    <td style="padding: 0.4rem;">Jakarta</td>
                                    <td style="padding: 0.4rem; font-weight: 700; color: #1e40af;">1</td>
                                </tr>
                                <tr style="border-bottom: 1px solid #e2e8f0;">
                                    <td style="padding: 0.4rem;"></td>
                                    <td style="padding: 0.4rem; font-weight: 700; color: #059669;">JAWABAN</td>
                                    <td style="padding: 0.4rem;">Bandung</td>
                                    <td style="padding: 0.4rem; font-weight: 700;">0</td>
                                </tr>
                                <tr style="border-bottom: 1px solid #e2e8f0;">
                                    <td style="padding: 0.4rem;"></td>
                                    <td style="padding: 0.4rem; font-weight: 700; color: #059669;">JAWABAN</td>
                                    <td style="padding: 0.4rem;">Medan</td>
                                    <td style="padding: 0.4rem; font-weight: 700;">0</td>
                                </tr>
                                <tr style="border-bottom: 1px solid #e2e8f0;">
                                    <td style="padding: 0.4rem;"></td>
                                    <td style="padding: 0.4rem; font-weight: 700; color: #059669;">JAWABAN</td>
                                    <td style="padding: 0.4rem;">Surabaya</td>
                                    <td style="padding: 0.4rem; font-weight: 700;">0</td>
                                </tr>
                                <tr style="border-bottom: 1px solid #e2e8f0;">
                                    <td style="padding: 0.4rem;"></td>
                                    <td style="padding: 0.4rem; font-weight: 700; color: #059669;">JAWABAN</td>
                                    <td style="padding: 0.4rem;">Makassar</td>
                                    <td style="padding: 0.4rem; font-weight: 700;">0</td>
                                </tr>
                                <tr>
                                    <td style="padding: 0.4rem;"></td>
                                    <td style="padding: 0.4rem; font-weight: 700; color: #7c3aed;">PEMBAHASAN</td>
                                    <td style="padding: 0.4rem;">Jakarta is the capital city. [Teks & Gambar]</td>
                                    <td style="padding: 0.4rem; color: #94a3b8;">[Kosong]</td>
                                </tr>
                            </tbody>
                        </table>
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
