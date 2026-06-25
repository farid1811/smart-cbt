@extends('admin.layouts.app')
@section('title', 'Daftar Soal')

@section('topbar-actions')
    <div style="display:flex; gap:0.5rem; flex-wrap:wrap;">
        <button onclick="openModal('deleteByGroupModal')" class="btn btn-danger" style="font-weight:600;">🗑️ Hapus per Kategori</button>
        <a href="{{ route('admin.questions.create', ['tryout_package_id' => request('tryout_package_id')]) }}" class="btn btn-primary" style="font-weight:700;">+ Tambah Soal</a>
    </div>
@endsection

@section('content')
<style>
    .modal-backdrop {
        position: fixed;
        top: 0; left: 0; right: 0; bottom: 0;
        background: rgba(15, 23, 42, 0.6);
        display: none;
        align-items: center;
        justify-content: center;
        z-index: 1050;
        backdrop-filter: blur(4px);
    }
    .modal-backdrop.show {
        display: flex;
    }
    .modal-box {
        background: #ffffff;
        border-radius: 12px;
        width: 100%;
        max-width: 500px;
        box-shadow: 0 20px 25px -5px rgba(0,0,0,0.1);
        border: 1px solid #e2e8f0;
        overflow: hidden;
    }
    .modal-header {
        padding: 1.25rem 1.5rem;
        border-bottom: 1px solid #e2e8f0;
        display: flex;
        justify-content: space-between;
        align-items: center;
        background: #f8fafc;
    }
    .modal-header h3 {
        margin: 0;
        font-size: 1.1rem;
        font-weight: 700;
        color: #0f172a;
    }
    .modal-close {
        background: none;
        border: none;
        color: #94a3b8;
        font-size: 1.5rem;
        cursor: pointer;
    }
    .modal-body {
        padding: 1.5rem;
    }
    .modal-footer {
        padding: 1rem 1.5rem;
        border-top: 1px solid #e2e8f0;
        display: flex;
        justify-content: flex-end;
        gap: 0.75rem;
        background: #f8fafc;
    }
    .form-group {
        margin-bottom: 1.25rem;
    }
    .form-group label {
        display: block;
        font-weight: 600;
        font-size: 0.875rem;
        margin-bottom: 0.5rem;
        color: #334155;
    }
    .form-group select {
        width: 100%;
        padding: 0.625rem;
        border: 1px solid #cbd5e1;
        border-radius: 6px;
        font-size: 0.9rem;
    }
    .badge-code {
        background: #eff6ff;
        color: #1e40af;
        border: 1px solid #bfdbfe;
        font-weight: 700;
        padding: 0.2rem 0.5rem;
        border-radius: 9999px;
        font-size: 0.75rem;
    }
</style>

{{-- Filter Bar --}}
<form method="GET" class="filter-bar" style="display:flex; flex-wrap:wrap; gap:0.5rem; background:#fff; padding:1rem; border-radius:10px; box-shadow:0 1px 3px rgba(0,0,0,0.05); margin-bottom:1.5rem; align-items:center;">
    <input type="text" name="search" class="form-control" placeholder="Cari soal..." value="{{ request('search') }}" style="flex:1; min-width:200px;">
    
    <select name="tryout_package_id" class="form-control" style="max-width:180px;">
        <option value="">Semua Paket</option>
        @foreach($packages as $p)
            <option value="{{ $p->id }}" {{ request('tryout_package_id') == $p->id ? 'selected' : '' }}>[{{ strtoupper($p->jenis_ujian) }}] {{ $p->nama }}</option>
        @endforeach
    </select>

    <select name="package_type" class="form-control" style="max-width:130px;">
        <option value="">Semua Jenis</option>
        <option value="tryout" {{ request('package_type') == 'tryout' ? 'selected' : '' }}>Tryout</option>
        <option value="drill" {{ request('package_type') == 'drill' ? 'selected' : '' }}>Drill</option>
    </select>

    <select name="group_id" id="group_filter" class="form-control" style="max-width:130px;">
        <option value="">Semua Grup</option>
        @foreach($groups as $grp)
            <option value="{{ $grp->id }}" {{ request('group_id') == $grp->id ? 'selected' : '' }}>{{ $grp->name }}</option>
        @endforeach
    </select>

    <select name="question_code_id" id="code_filter" class="form-control" style="max-width:140px;" disabled>
        <option value="">Semua Kode</option>
    </select>

    <select name="category_id" id="category_filter" class="form-control" style="max-width:150px;" disabled>
        <option value="">Semua Kategori</option>
    </select>

    <button type="submit" class="btn btn-secondary">Filter</button>
    @if(request()->hasAny(['search','tryout_package_id','package_type','group_id','question_code_id','category_id']))
        <a href="{{ route('admin.questions.index') }}" class="btn btn-secondary">Reset</a>
    @endif
</form>

<!-- Bulk Action Toolbar -->
<div id="bulk-action-bar" style="display:none; align-items:center; justify-content:space-between; padding:0.85rem 1.25rem; background:#fef2f2; border:1px solid #fca5a5; border-radius:8px; margin-bottom:1.5rem; animation: fadeUp 0.2s ease;">
    <div style="display:flex; align-items:center; gap:0.5rem;">
        <span style="font-size:0.9rem; color:#b91c1c; font-weight:700;"><span id="selected-count">0</span> soal terpilih</span>
    </div>
    <button type="button" onclick="confirmBulkDelete()" class="btn btn-danger btn-sm" style="font-weight:700; padding:0.4rem 1rem;">
        🗑️ Hapus Massal Soal Terpilih
    </button>
</div>

<div class="table-card" style="background:#fff; border-radius:12px; box-shadow:0 4px 6px -1px rgba(0,0,0,0.05);">
    <div class="table-header" style="padding: 1.25rem 1.5rem; border-bottom:1px solid #f1f5f9; display:flex; justify-content:space-between; align-items:center;">
        <h3 style="font-weight:800; color:#0f172a; margin:0;">Daftar Soal ({{ $questions->total() }})</h3>
        <div style="font-size:0.85rem; color:#64748b;">Menampilkan {{ $questions->firstItem() ?? 0 }} - {{ $questions->lastItem() ?? 0 }} dari {{ $questions->total() }} soal</div>
    </div>
    <table style="width:100%; border-collapse:collapse;">
        <thead>
            <tr style="border-bottom:2px solid #f1f5f9; text-align:left; background:#f8fafc;">
                <th style="padding:1rem; width:40px; text-align:center;">
                    <input type="checkbox" id="select-all" style="width:16px; height:16px; accent-color:#1e40af; cursor:pointer;">
                </th>
                <th style="padding:1rem; width:50px;">#</th>
                <th style="padding:1rem;">Soal</th>
                <th style="padding:1rem;">Hierarki Kategori</th>
                <th style="padding:1rem; width:80px; text-align:center;">Kunci</th>
                <th style="padding:1rem; width:120px; text-align:center;">Aksi</th>
            </tr>
        </thead>
        <tbody>
            <form id="bulk-delete-form" method="POST" action="{{ route('admin.questions.bulkDelete') }}">
                @csrf
                @forelse($questions as $q)
                <tr style="border-bottom:1px solid #f1f5f9;">
                    <td style="padding:1rem; text-align:center;">
                        <input type="checkbox" name="ids[]" value="{{ $q->id }}" class="question-checkbox" style="width:16px; height:16px; accent-color:#1e40af; cursor:pointer;">
                    </td>
                    <td style="padding:1rem; color:#64748b; font-size:0.85rem;">{{ $questions->firstItem() + $loop->index }}</td>
                    <td style="padding:1rem; max-width:380px;">
                        <div style="display:flex; align-items:flex-start; gap:0.75rem;">
                            @php $qImg = $q->question_image ?: $q->image; @endphp
                            @if($qImg)
                                <img src="{{ asset($qImg) }}" alt="Soal Gambar" style="width:44px; height:44px; border-radius:6px; object-fit:cover; border:1px solid #cbd5e1; flex-shrink:0; cursor:pointer;" onclick="window.open('{{ asset($qImg) }}')">
                            @endif
                            <div>
                                <div style="font-size:0.9rem; color:#1e293b; font-weight:500; line-height:1.4;">{{ Str::limit(strip_tags($q->soal), 110) }}</div>
                                @if($q->tryoutPackage)
                                    <div style="font-size:0.72rem; color:#64748b; margin-top:0.25rem; font-weight:500;">
                                        Paket: <span style="color:#1e40af; font-weight:600;">{{ $q->tryoutPackage->nama }}</span>
                                        <span class="badge" style="font-size:0.6rem; padding:0.1rem 0.35rem; margin-left:0.25rem; background:#f1f5f9; color:#475569; border-color:#e2e8f0; font-weight:700;">
                                            {{ strtoupper($q->tryoutPackage->jenis_ujian) }}
                                        </span>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </td>
                    <td style="padding:1rem;">
                        <div style="font-size:0.8rem; font-weight:700; color:#334155;">
                            {{ $q->group->name ?? '—' }} &rarr; <span class="badge-code">{{ $q->questionCode?->code ?? '—' }}</span>
                        </div>
                        <div style="font-size:0.75rem; color:#64748b; margin-top:0.25rem;">
                            {{ $q->category->name ?? '—' }}
                        </div>
                    </td>
                    <td style="padding:1rem; text-align:center;">
                        <span class="badge" style="background:#eff6ff; color:#1e40af; font-weight:800; border-color:#bfdbfe; font-size:0.85rem; padding:0.25rem 0.5rem;">{{ $q->jawaban_benar }}</span>
                    </td>
                    <td style="padding:1rem; text-align:center;">
                        <div style="display:flex; gap:0.4rem; justify-content:center;">
                            <a href="{{ route('admin.questions.edit', $q) }}" class="btn btn-secondary btn-sm" style="font-weight:600; padding:0.35rem 0.65rem;">Edit</a>
                            <button type="button" onclick="confirmSingleDelete({{ $q->id }})" class="btn btn-danger btn-sm" style="font-weight:600; padding:0.35rem 0.65rem;">Hapus</button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" style="padding:3rem; text-align:center; color:#64748b;">Tidak ada soal ditemukan.</td></tr>
                @endforelse
            </form>
        </tbody>
    </table>
    <div style="padding:1rem; border-top:1px solid #f1f5f9;">
        {{ $questions->links() }}
    </div>
</div>

<!-- SINGLE DELETE FALLBACK FORM -->
<form id="single-delete-form" method="POST" style="display:none;">
    @csrf @method('DELETE')
</form>

<!-- ==============================================
     MODALS FOR BULK DELETE BY CATEGORY
     ============================================== -->
<div id="deleteByGroupModal" class="modal-backdrop">
    <div class="modal-box">
        <div class="modal-header">
            <h3>🗑️ Hapus Soal per Kategori</h3>
            <button onclick="closeModal('deleteByGroupModal')" class="modal-close">&times;</button>
        </div>
        <div class="modal-body">
            <!-- Delete by Category Form -->
            <form method="POST" action="{{ route('admin.questions.deleteByCategory') }}" onsubmit="return confirm('PERINGATAN: Hapus semua soal dalam Kategori terpilih? Tindakan ini tidak dapat dibatalkan!')">
                @csrf
                <div class="form-group">
                    <label>Hapus Semua Soal berdasarkan Kategori</label>
                    <select name="category_id" required>
                        <option value="">-- Pilih Kategori --</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}">[{{ $cat->questionCode?->group?->name ?? '—' }} - {{ $cat->questionCode?->code ?? '—' }}] {{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div style="display:flex; justify-content:flex-end;">
                    <button type="submit" class="btn btn-danger btn-sm" style="font-weight:700; width:100%; justify-content:center; padding:0.6rem;">Hapus Semua Soal Kategori</button>
                </div>
            </form>
        </div>
        <div class="modal-footer">
            <button type="button" onclick="closeModal('deleteByGroupModal')" class="btn btn-secondary">Batal</button>
        </div>
    </div>
</div>

<!-- ==============================================
     SCRIPTS
     ============================================== -->
<script>
    // Checkboxes Selection Control
    const selectAllCheckbox = document.getElementById('select-all');
    const questionCheckboxes = document.querySelectorAll('.question-checkbox');
    const bulkActionBar = document.getElementById('bulk-action-bar');
    const selectedCountSpan = document.getElementById('selected-count');

    function updateBulkActionBar() {
        const checkedCheckboxes = document.querySelectorAll('.question-checkbox:checked');
        const count = checkedCheckboxes.length;
        selectedCountSpan.textContent = count;
        
        if (count > 0) {
            bulkActionBar.style.display = 'flex';
        } else {
            bulkActionBar.style.display = 'none';
        }
    }

    selectAllCheckbox.addEventListener('change', function() {
        questionCheckboxes.forEach(cb => {
            cb.checked = this.checked;
        });
        updateBulkActionBar();
    });

    questionCheckboxes.forEach(cb => {
        cb.addEventListener('change', function() {
            const allChecked = Array.from(questionCheckboxes).every(c => c.checked);
            selectAllCheckbox.checked = allChecked;
            updateBulkActionBar();
        });
    });

    function confirmBulkDelete() {
        if (confirm('Apakah Anda yakin ingin menghapus semua soal yang dipilih secara massal? Tindakan ini tidak dapat dibatalkan!')) {
            document.getElementById('bulk-delete-form').submit();
        }
    }

    function confirmSingleDelete(id) {
        if (confirm('Hapus soal ini?')) {
            const form = document.getElementById('single-delete-form');
            form.action = `/admin/questions/${id}`;
            form.submit();
        }
    }

    // Modal Control
    function openModal(id) {
        document.getElementById(id).classList.add('show');
    }
    function closeModal(id) {
        document.getElementById(id).classList.remove('show');
    }

    // Dependent Filter Dropdowns Logic
    const groupSelect = document.getElementById('group_filter');
    const codeSelect = document.getElementById('code_filter');
    const catSelect = document.getElementById('category_filter');

    async function loadCodes(groupId, selectedCodeId = null) {
        codeSelect.innerHTML = '<option value="">Semua Kode</option>';
        codeSelect.disabled = true;
        catSelect.innerHTML = '<option value="">Semua Kategori</option>';
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
        catSelect.innerHTML = '<option value="">Semua Kategori</option>';
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
        const oldGroup = "{{ request('group_id') }}";
        const oldCode = "{{ request('question_code_id') }}";
        const oldCat = "{{ request('category_id') }}";

        if (oldGroup) {
            loadCodes(oldGroup, oldCode).then(() => {
                if (oldCode) {
                    loadCategories(oldCode, oldCat);
                }
            });
        }
    });
</script>
@endsection
