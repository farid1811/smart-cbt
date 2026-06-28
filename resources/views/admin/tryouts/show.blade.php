@extends('admin.layouts.app')

@section('title', 'Kelola Soal — ' . $tryout->nama)

@section('topbar-actions')
    <a href="{{ route('admin.tryouts.index', ['type' => $tryout->jenis_ujian]) }}" class="btn btn-secondary">← Kembali</a>
@endsection

@section('content')

{{-- Header Info Paket --}}
<div class="table-card" style="margin-bottom:1.5rem;padding:1.25rem 1.5rem;">
    <div style="display:flex;align-items:center;justify-content:space-between;gap:1rem;flex-wrap:wrap;">
        <div>
            <h2 style="margin:0;font-size:1.2rem;font-weight:700;color:var(--text);">
                {{ $tryout->nama }}
                <span class="badge {{ $tryout->jenis_ujian === 'drill' ? 'badge-active' : '' }}" style="font-size:0.7rem;margin-left:0.5rem;{{ $tryout->jenis_ujian === 'tryout' ? 'background:#ede9fe;color:#7c3aed;border:1px solid #ddd6fe;' : '' }}">
                    {{ $tryout->jenis_ujian === 'drill' ? '🏋️ Drill' : '📝 Tryout' }}
                </span>
            </h2>
            <div style="display:flex;gap:1rem;margin-top:0.5rem;flex-wrap:wrap;">
                <span style="font-size:0.82rem;color:var(--text-muted);">
                    🏷️ <strong>Grup:</strong> {{ $tryout->group ?? '—' }}
                </span>
                @if($tryout->categoryRelation)
                <span style="font-size:0.82rem;color:var(--text-muted);">
                    📂 <strong>Kategori:</strong> {{ $tryout->categoryRelation->name }}
                </span>
                @endif
                <span style="font-size:0.82rem;color:var(--text-muted);">
                    ⏱️ <strong>Durasi:</strong> {{ $tryout->durasi_menit }} menit
                </span>
                <span style="font-size:0.82rem;color:var(--text-muted);">
                    📊 <strong>Total Soal:</strong> <span id="soalCount">{{ $tryout->questions->count() }}</span>
                </span>
            </div>
        </div>
        {{-- Tombol Import --}}
        <div style="display:flex;gap:0.75rem;flex-wrap:wrap;">
            <a href="{{ route('admin.tryouts.import.form', $tryout) }}"
               style="display:inline-flex;align-items:center;gap:6px;padding:8px 16px;border-radius:8px;background:linear-gradient(135deg,#7c3aed,#4f46e5);color:#fff;font-weight:600;font-size:0.85rem;text-decoration:none;box-shadow:0 2px 8px rgba(124,58,237,0.3);transition:all 0.2s;"
               onmouseover="this.style.transform='translateY(-1px)';this.style.boxShadow='0 4px 12px rgba(124,58,237,0.4)'"
               onmouseout="this.style.transform='';this.style.boxShadow='0 2px 8px rgba(124,58,237,0.3)'">
                📥 Import Word / PDF
            </a>
            <a href="{{ route('admin.questions.create', ['tryout_package_id' => $tryout->id]) }}"
               style="display:inline-flex;align-items:center;gap:6px;padding:8px 16px;border-radius:8px;background:linear-gradient(135deg,#10b981,#059669);color:#fff;font-weight:600;font-size:0.85rem;text-decoration:none;box-shadow:0 2px 8px rgba(16,185,129,0.3);transition:all 0.2s;"
               onmouseover="this.style.transform='translateY(-1px)'"
               onmouseout="this.style.transform=''">
                ✏️ Tambah Soal Manual
            </a>
        </div>
    </div>
</div>

<div style="display:grid;grid-template-columns:1fr 1fr;gap:1.5rem;">

    {{-- Panel Kiri: Soal dalam Paket --}}
    <div>
        <div class="table-card">
            <div class="table-header" style="border-bottom:1px solid var(--border);padding:1rem 1.25rem;">
                <h3 style="margin:0;font-size:1rem;font-weight:700;">
                    📋 Soal dalam Paket <span style="color:var(--primary);">({{ $tryout->questions->count() }})</span>
                </h3>
            </div>
            <div id="soalList" style="max-height:520px;overflow-y:auto;padding:0.5rem;">
                @forelse($tryout->questions as $i => $q)
                <div class="soal-item" data-id="{{ $q->id }}"
                     style="display:flex;align-items:center;gap:0.75rem;padding:0.6rem 0.75rem;border-radius:8px;margin-bottom:0.35rem;background:var(--card-alt,#f8fafc);border:1px solid var(--border);transition:all 0.15s;"
                     onmouseover="this.style.background='var(--hover,#eff6ff)'"
                     onmouseout="this.style.background='var(--card-alt,#f8fafc)'">
                    <span style="min-width:24px;height:24px;border-radius:50%;background:var(--primary);color:#fff;font-size:0.72rem;font-weight:700;display:flex;align-items:center;justify-content:center;">{{ $i+1 }}</span>
                    @if($q->category)
                    <span class="badge" style="font-size:0.7rem;background:#eff6ff;color:#2563eb;border:1px solid #bfdbfe;padding:2px 6px;flex-shrink:0;">{{ $q->category->kode ?? $q->category->name }}</span>
                    @endif
                    <span style="flex:1;font-size:0.82rem;color:var(--text);overflow:hidden;text-overflow:ellipsis;white-space:nowrap;" title="{{ $q->soal }}">{{ Str::limit($q->soal, 70) }}</span>
                    <span style="font-size:0.75rem;font-weight:700;color:#10b981;flex-shrink:0;">{{ $q->jawaban_benar }}</span>
                    <div style="display:flex;gap:4px;flex-shrink:0;">
                        <a href="{{ route('admin.questions.edit', $q) }}"
                           style="padding:3px 8px;border-radius:5px;background:#eff6ff;color:#2563eb;border:1px solid #bfdbfe;font-size:0.72rem;font-weight:600;text-decoration:none;">Edit</a>
                        <button onclick="removeQuestion({{ $q->id }}, this)"
                                style="padding:3px 8px;border-radius:5px;background:#fee2e2;color:#dc2626;border:1px solid #fecaca;font-size:0.72rem;font-weight:600;cursor:pointer;">✕</button>
                    </div>
                </div>
                @empty
                    <div style="padding:2.5rem;text-align:center;color:var(--text-muted);">
                        <div style="font-size:2.5rem;margin-bottom:0.75rem;">📭</div>
                        <p style="margin:0;font-size:0.9rem;">Belum ada soal dalam paket ini.</p>
                        <p style="margin:0.5rem 0 0;font-size:0.82rem;">Gunakan tombol <strong>Import</strong> atau <strong>Tambah Manual</strong> di atas.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    {{-- Panel Kanan: Bank Soal --}}
    <div>
        <div class="table-card">
            <div class="table-header" style="border-bottom:1px solid var(--border);padding:1rem 1.25rem;">
                <h3 style="margin:0;font-size:1rem;font-weight:700;">🏦 Tambah dari Bank Soal</h3>
            </div>
            @if($tryout->jenis_ujian === 'drill')
            <div style="padding:0.75rem;border-bottom:1px solid var(--border);display:flex;gap:0.5rem;">
                <select id="filterKategori" style="flex:1;padding:7px 10px;border-radius:7px;border:1px solid var(--border);background:var(--card);font-size:0.83rem;color:var(--text);">
                    <option value="">Semua Kategori</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                    @endforeach
                </select>
                <input type="text" id="searchSoal" placeholder="Cari soal..."
                       style="flex:2;padding:7px 10px;border-radius:7px;border:1px solid var(--border);background:var(--card);font-size:0.83rem;color:var(--text);">
            </div>
            @else
            <div style="padding:0.75rem;border-bottom:1px solid var(--border);display:flex;gap:0.5rem;">
                <input type="text" id="searchSoal" placeholder="Cari soal..."
                       style="flex:1;padding:7px 10px;border-radius:7px;border:1px solid var(--border);background:var(--card);font-size:0.83rem;color:var(--text);">
            </div>
            @endif
            <div id="bankList" style="max-height:440px;overflow-y:auto;padding:0.5rem;">
                @foreach($categories as $cat)
                    @foreach($cat->questions as $q)
                    <div class="bank-item" data-id="{{ $q->id }}" data-cat="{{ $cat->id }}" data-soal="{{ strtolower($q->soal) }}"
                         style="display:flex;align-items:center;gap:0.6rem;padding:0.5rem 0.6rem;border-radius:7px;margin-bottom:0.3rem;background:var(--card-alt,#f8fafc);border:1px solid var(--border);transition:all 0.15s;"
                         onmouseover="this.style.background='var(--hover,#eff6ff)'"
                         onmouseout="this.style.background='var(--card-alt,#f8fafc)'">
                        <span class="badge" style="font-size:0.68rem;background:#f0fdf4;color:#16a34a;border:1px solid #bbf7d0;flex-shrink:0;">{{ $cat->name }}</span>
                        <span style="flex:1;font-size:0.8rem;color:var(--text);overflow:hidden;text-overflow:ellipsis;white-space:nowrap;" title="{{ $q->soal }}">{{ Str::limit($q->soal, 65) }}</span>
                        <button onclick="addQuestion({{ $q->id }}, this)"
                                style="padding:3px 10px;border-radius:5px;background:#dcfce7;color:#16a34a;border:1px solid #bbf7d0;font-size:0.75rem;font-weight:700;cursor:pointer;flex-shrink:0;">+</button>
                    </div>
                    @endforeach
                @endforeach
                @if($categories->flatMap->questions->isEmpty())
                    <div style="padding:2rem;text-align:center;color:var(--text-muted);">
                        <p style="margin:0;font-size:0.85rem;">Tidak ada soal di bank soal.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
const addQuestionUrl = "{{ route('admin.tryouts.addQuestion', $tryout) }}";
const removeQuestionUrl = "{{ route('admin.tryouts.removeQuestion', $tryout) }}";
const csrfToken = '{{ csrf_token() }}';

async function addQuestion(questionId, btn) {
    btn.disabled = true;
    btn.textContent = '...';
    try {
        const res = await fetch(addQuestionUrl, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
            body: JSON.stringify({ question_id: questionId })
        });
        const data = await res.json();
        if (res.ok) {
            btn.textContent = '✓';
            btn.style.background = '#dcfce7';
            btn.style.color = '#15803d';
            document.getElementById('soalCount').textContent = data.total;
            showToast('Soal berhasil ditambahkan ke paket.', 'success');
        } else {
            btn.textContent = '+';
            btn.disabled = false;
            showToast(data.message || 'Gagal menambahkan soal.', 'error');
        }
    } catch (e) {
        btn.textContent = '+';
        btn.disabled = false;
        showToast('Terjadi kesalahan koneksi.', 'error');
    }
}

async function removeQuestion(questionId, btn) {
    if (!confirm('Yakin hapus soal ini dari paket?')) return;
    btn.disabled = true;
    try {
        const res = await fetch(removeQuestionUrl, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
            body: JSON.stringify({ question_id: questionId })
        });
        const data = await res.json();
        if (res.ok) {
            const item = btn.closest('.soal-item');
            item?.remove();
            document.getElementById('soalCount').textContent = data.total;
            // Re-number
            document.querySelectorAll('.soal-item').forEach((el, i) => {
                const badge = el.querySelector('span:first-child');
                if (badge) badge.textContent = i + 1;
            });
            showToast('Soal berhasil dihapus dari paket.', 'success');
        } else {
            btn.disabled = false;
            showToast(data.message || 'Gagal menghapus soal.', 'error');
        }
    } catch (e) {
        btn.disabled = false;
        showToast('Terjadi kesalahan koneksi.', 'error');
    }
}

// Live filter bank soal
const filterKategoriEl = document.getElementById('filterKategori');
if (filterKategoriEl) {
    filterKategoriEl.addEventListener('change', filterBank);
}
document.getElementById('searchSoal').addEventListener('input', filterBank);

function filterBank() {
    const filterKategoriEl = document.getElementById('filterKategori');
    const cat = filterKategoriEl ? filterKategoriEl.value : '';
    const q = document.getElementById('searchSoal').value.toLowerCase();
    document.querySelectorAll('.bank-item').forEach(el => {
        const matchCat = !cat || el.dataset.cat === cat;
        const matchQ = !q || el.dataset.soal.includes(q);
        el.style.display = (matchCat && matchQ) ? '' : 'none';
    });
}

function showToast(msg, type = 'success') {
    const t = document.createElement('div');
    t.textContent = msg;
    t.style.cssText = `position:fixed;bottom:1.5rem;right:1.5rem;padding:0.75rem 1.25rem;border-radius:10px;font-size:0.85rem;font-weight:600;z-index:9999;box-shadow:0 4px 20px rgba(0,0,0,0.15);${type==='success'?'background:#d1fae5;color:#065f46;border:1px solid #6ee7b7':'background:#fee2e2;color:#991b1b;border:1px solid #fca5a5'}`;
    document.body.appendChild(t);
    setTimeout(() => t.remove(), 3000);
}
</script>
@endpush
