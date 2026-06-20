@extends('admin.layouts.app')
@section('title', 'Bank Soal')
@section('topbar-actions')
    <div style="display:flex;gap:0.5rem;">
        <button type="button" class="btn btn-secondary" onclick="openImportModal()">Import PDF</button>
        <a href="{{ route('admin.questions.create') }}" class="btn btn-primary">+ Tambah Soal</a>
    </div>
@endsection

@section('content')
{{-- Filter Bar --}}
<form method="GET" class="filter-bar">
    <input type="text" name="search" class="form-control" placeholder="Cari soal..." value="{{ request('search') }}">
    <select name="category_id" class="form-control" style="max-width:180px;">
        <option value="">Semua Kategori</option>
        @foreach($categories as $cat)
            <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->kode }} — {{ $cat->name }}</option>
        @endforeach
    </select>
    <select name="tingkat_kesulitan" class="form-control" style="max-width:150px;">
        <option value="">Semua Tingkat</option>
        <option value="mudah"  {{ request('tingkat_kesulitan') == 'mudah'  ? 'selected' : '' }}>Mudah</option>
        <option value="sedang" {{ request('tingkat_kesulitan') == 'sedang' ? 'selected' : '' }}>Sedang</option>
        <option value="sulit"  {{ request('tingkat_kesulitan') == 'sulit'  ? 'selected' : '' }}>Sulit</option>
    </select>
    <button type="submit" class="btn btn-secondary">Filter</button>
    @if(request()->hasAny(['search','category_id','tingkat_kesulitan']))
        <a href="{{ route('admin.questions.index') }}" class="btn btn-secondary">Reset</a>
    @endif
</form>

<div class="table-card">
    <div class="table-header">
        <h3 style="font-weight:700;">Daftar Soal ({{ $questions->total() }})</h3>
    </div>
    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Soal</th>
                <th>Kategori</th>
                <th>Tingkat</th>
                <th>Jawaban</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($questions as $q)
            <tr>
                <td style="color:var(--text-muted);">{{ $questions->firstItem() + $loop->index }}</td>
                <td style="max-width:350px;">
                    @if($q->image)
                        <div style="display:flex; align-items:center; gap:0.5rem;">
                            <img src="{{ asset($q->image) }}" alt="Soal Gambar" style="width:40px; height:40px; border-radius:4px; object-fit:cover; border:1px solid var(--border); flex-shrink:0;" onclick="window.open('{{ asset($q->image) }}')">
                            <div>{{ Str::limit($q->soal, 80) }}</div>
                        </div>
                    @else
                        {{ Str::limit($q->soal, 90) }}
                    @endif
                </td>
                <td><span class="badge badge-{{ strtolower($q->category->kode) }}">{{ $q->category->kode }}</span></td>
                <td><span class="badge badge-{{ $q->tingkat_kesulitan }}">{{ ucfirst($q->tingkat_kesulitan) }}</span></td>
                <td><strong style="color:var(--primary-light);">{{ $q->jawaban_benar }}</strong></td>
                <td>
                    <div style="display:flex;gap:0.5rem;">
                        <a href="{{ route('admin.questions.edit', $q) }}" class="btn btn-secondary btn-sm">✏️</a>
                        <form method="POST" action="{{ route('admin.questions.destroy', $q) }}" onsubmit="return confirm('Hapus soal ini?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm">🗑️</button>
                        </form>
                    </div>
                </td>
            </tr>
            @empty
            <tr><td colspan="6"><div class="empty-state"><p style="font-weight: 500;">Tidak ada soal ditemukan.</p></div></td></tr>
            @endforelse
        </tbody>
    </table>
    <div style="padding: 1.25rem; border-top: 1px solid var(--border);">
        {{ $questions->links() }}
    </div>
</div>

{{-- Modal Import PDF --}}
<div class="modal-overlay" id="importModal" style="position: fixed; inset: 0; background: rgba(15,23,42,0.3); backdrop-filter: blur(2px); display: flex; align-items: center; justify-content: center; z-index: 1000; opacity: 0; pointer-events: none; transition: opacity 0.25s;">
    <div class="modal" style="background: #ffffff; border: 1px solid var(--border); border-radius: 12px; padding: 1.75rem; max-width: 480px; width: 90%; transform: scale(0.95); transition: transform 0.25s; box-shadow: 0 20px 25px -5px rgba(0,0,0,0.1), 0 10px 10px -5px rgba(0,0,0,0.04);">
        <h3 style="font-size: 1.15rem; font-weight: 700; margin-bottom: 0.75rem; color: var(--text);">Import Soal dari PDF</h3>
        <p style="font-size: 0.82rem; color: var(--text-muted); margin-bottom: 1rem; line-height: 1.5;">
            Unggah file PDF (.pdf) berisi tabel soal pilihan ganda dengan format kolom berikut:
        </p>
        <div style="background: var(--surface2); border: 1px solid var(--border); border-radius: 8px; padding: 0.75rem 1rem; font-size: 0.75rem; color: var(--text-muted); line-height: 1.4; margin-bottom: 1.25rem;">
            <table style="width:100%; border-collapse:collapse; font-size:0.7rem; text-align:left;">
                <thead>
                    <tr style="border-bottom:1px solid var(--border); color:var(--text); font-weight:700;">
                        <th style="padding:4px 0;">No</th>
                        <th style="padding:4px 0;">Jenis</th>
                        <th style="padding:4px 0;">Isi</th>
                        <th style="padding:4px 0;">Jawaban</th>
                    </tr>
                </thead>
                <tbody>
                    <tr style="border-bottom:1px solid rgba(0,0,0,0.05);">
                        <td style="padding:6px 0; color:var(--text); font-weight:600;">1</td>
                        <td style="padding:6px 0; color:var(--primary); font-weight:600;">SOAL</td>
                        <td style="padding:6px 0; color:var(--text);">Pertanyaan / Soal Ujian...</td>
                        <td style="padding:6px 0; color:var(--text-muted); font-style:italic;">(kosong)</td>
                    </tr>
                    <tr style="border-bottom:1px solid rgba(0,0,0,0.05);">
                        <td style="padding:6px 0;"></td>
                        <td style="padding:6px 0; font-weight:600;">JAWABAN</td>
                        <td style="padding:6px 0;">Pilihan A (Salah)</td>
                        <td style="padding:6px 0;">0</td>
                    </tr>
                    <tr style="border-bottom:1px solid rgba(0,0,0,0.05);">
                        <td style="padding:6px 0;"></td>
                        <td style="padding:6px 0; font-weight:600;">JAWABAN</td>
                        <td style="padding:6px 0;">Pilihan B (Benar)</td>
                        <td style="padding:6px 0; font-weight:700; color:var(--success);">5</td>
                    </tr>
                </tbody>
            </table>
        </div>
        <form method="POST" action="{{ route('admin.questions.import') }}" enctype="multipart/form-data">
            @csrf
            <div class="form-group" style="margin-bottom: 1.25rem;">
                <label for="file_pdf" style="display: block; font-size: 0.8rem; font-weight: 600; color: var(--text); margin-bottom: 0.45rem;">Pilih File PDF (.pdf)</label>
                <input type="file" name="file" id="file_pdf" class="form-control" accept=".pdf" required>
            </div>
            <div style="display: flex; gap: 0.75rem;">
                <button type="button" class="btn btn-secondary" onclick="closeImportModal()" style="flex: 1; justify-content: center; font-weight: 600;">Batal</button>
                <button type="submit" class="btn btn-primary" style="flex: 1; justify-content: center; font-weight: 600;">Mulai Import</button>
            </div>
        </form>
    </div>
</div>

<script>
function openImportModal() {
    const modal = document.getElementById('importModal');
    modal.style.opacity = '1';
    modal.style.pointerEvents = 'all';
    modal.querySelector('.modal').style.transform = 'scale(1)';
}

function closeImportModal() {
    const modal = document.getElementById('importModal');
    modal.style.opacity = '0';
    modal.style.pointerEvents = 'none';
    modal.querySelector('.modal').style.transform = 'scale(0.95)';
}
</script>
@endsection
