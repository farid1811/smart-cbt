@extends('admin.layouts.app')
@section('title', 'Kelola Soal — ' . $tryout->nama)
@section('topbar-actions')
    <a href="{{ route('admin.tryouts.index') }}" class="btn btn-secondary">← Kembali</a>
@endsection

@section('content')
<div style="display:grid;grid-template-columns:1fr 1fr;gap:1.5rem;">

    {{-- Soal dalam paket --}}
    <div>
        <div class="table-card">
            <div class="table-header">
                <h3 style="font-weight: 700;">Soal dalam Paket <span id="soalCount" style="color:var(--primary);">({{ $tryout->questions->count() }})</span></h3>
            </div>
            <div id="soalList" style="max-height:520px;overflow-y:auto;background:#ffffff;">
                @forelse($tryout->questions as $i => $q)
                <div class="soal-item" data-id="{{ $q->id }}" style="display:flex;align-items:center;gap:0.75rem;padding:0.75rem 1rem;border-bottom:1px solid var(--border);transition:background 0.15s;">
                    <span style="color:var(--text-muted);font-size:0.8rem;width:28px;">{{ $i+1 }}</span>
                    <span class="badge badge-{{ strtolower($q->category->kode) }}">{{ $q->category->kode }}</span>
                    <span style="flex:1;font-size:0.83rem;color:var(--text);">{{ Str::limit($q->soal, 80) }}</span>
                    <button onclick="removeQuestion({{ $q->id }}, this)" class="btn btn-danger btn-sm" title="Hapus dari paket" style="padding: 0.25rem 0.5rem;">✕</button>
                </div>
                @empty
                <div id="emptyMsg" class="empty-state"><p style="font-weight: 500;">Belum ada soal dalam paket ini.</p></div>
                @endforelse
            </div>
        </div>
    </div>

    {{-- Bank soal --}}
    <div>
        <div class="table-card">
            <div class="table-header">
                <h3 style="font-weight: 700;">Tambah dari Bank Soal</h3>
            </div>
            <div style="padding:1rem 1.25rem;border-bottom:1px solid var(--border);background:#fafafa;display:flex;flex-direction:column;gap:0.5rem;">
                <select id="filterKategori" class="form-control">
                    <option value="">Semua Kategori</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->kode }}">{{ $cat->kode }} — {{ $cat->name }}</option>
                    @endforeach
                </select>
                <input type="text" id="searchSoal" class="form-control" placeholder="Cari soal...">
            </div>
            <div id="bankList" style="max-height:440px;overflow-y:auto;background:#ffffff;">
                @foreach($categories as $cat)
                    @foreach($cat->questions as $q)
                    <div class="bank-item" data-id="{{ $q->id }}" data-kode="{{ $cat->kode }}" data-soal="{{ strtolower($q->soal) }}"
                         style="display:flex;align-items:center;gap:0.75rem;padding:0.75rem 1rem;border-bottom:1px solid var(--border);">
                        <span class="badge badge-{{ strtolower($cat->kode) }}">{{ $cat->kode }}</span>
                        <span style="flex:1;font-size:0.83rem;color:var(--text);">{{ Str::limit($q->soal, 75) }}</span>
                        <button onclick="addQuestion({{ $q->id }}, this)" class="btn btn-primary btn-sm" style="padding: 0.25rem 0.5rem;">+</button>
                    </div>
                    @endforeach
                @endforeach
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
const tryoutId = {{ $tryout->id }};
const csrfToken = '{{ csrf_token() }}';

async function addQuestion(questionId, btn) {
    btn.disabled = true;
    btn.textContent = '…';
    const res = await fetch(`/admin/tryouts/${tryoutId}/add-question`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
        body: JSON.stringify({ question_id: questionId })
    });
    const data = await res.json();
    if (res.ok) {
        // Reload page to reflect changes
        window.location.reload();
    } else {
        alert(data.message || 'Gagal menambahkan soal.');
        btn.disabled = false;
        btn.textContent = '+';
    }
}

async function removeQuestion(questionId, btn) {
    if (!confirm('Hapus soal ini dari paket?')) return;
    btn.disabled = true;
    const res = await fetch(`/admin/tryouts/${tryoutId}/remove-question`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
        body: JSON.stringify({ question_id: questionId })
    });
    if (res.ok) {
        window.location.reload();
    } else {
        alert('Gagal menghapus soal.');
        btn.disabled = false;
    }
}

// Filter bank soal
document.getElementById('filterKategori').addEventListener('change', filterBank);
document.getElementById('searchSoal').addEventListener('input', filterBank);

function filterBank() {
    const kode   = document.getElementById('filterKategori').value;
    const search = document.getElementById('searchSoal').value.toLowerCase();
    document.querySelectorAll('.bank-item').forEach(item => {
        const matchKode   = !kode   || item.dataset.kode === kode;
        const matchSearch = !search || item.dataset.soal.includes(search);
        item.style.display = (matchKode && matchSearch) ? 'flex' : 'none';
    });
}
</script>
@endpush
