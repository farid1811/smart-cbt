@extends('admin.layouts.app')
@section('title', 'Modul Pembelajaran')

@section('topbar-actions')
    <a href="{{ route('admin.modules.create') }}" class="btn btn-primary">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
        Tambah Modul
    </a>
@endsection

@section('content')
<form method="GET" class="filter-bar" style="display:flex; flex-wrap:wrap; gap:0.5rem; background:#fff; padding:1rem; border-radius:10px; box-shadow:0 1px 3px rgba(0,0,0,0.05); margin-bottom:1.5rem; align-items:center;">
    <input type="text" name="search" class="form-control" placeholder="Cari nama modul..." value="{{ request('search') }}" style="flex:1; min-width:200px;">
    
    <select name="group_id" id="group_filter" class="form-control" style="max-width:140px;">
        <option value="">Semua Grup</option>
        @foreach($groups as $grp)
            <option value="{{ $grp->id }}" {{ request('group_id') == $grp->id ? 'selected' : '' }}>{{ $grp->name }}</option>
        @endforeach
    </select>

    <select name="question_code_id" id="code_filter" class="form-control" style="max-width:160px;" disabled>
        <option value="">Semua Kode</option>
    </select>

    <select name="category_id" id="category_filter" class="form-control" style="max-width:160px;" disabled>
        <option value="">Semua Kategori</option>
    </select>

    <select name="sub_category_id" id="subcategory_filter" class="form-control" style="max-width:160px;" disabled>
        <option value="">Semua Sub Kategori</option>
    </select>

    <button type="submit" class="btn btn-primary" style="padding: 0.5rem 1rem;">Filter</button>
    @if(request()->hasAny(['search','group_id','question_code_id','category_id','sub_category_id']))
        <a href="{{ route('admin.modules.index') }}" class="btn btn-secondary" style="padding: 0.5rem 1rem;">Reset</a>
    @endif
</form>

<div class="table-card" style="background:#fff; border-radius:12px; box-shadow:0 4px 6px -1px rgba(0,0,0,0.05);">
    <div class="table-header" style="padding: 1.25rem 1.5rem; border-bottom:1px solid #f1f5f9;">
        <h3 style="font-weight:800; color:#0f172a; margin:0;">Daftar Modul Pembelajaran ({{ $modules->total() }})</h3>
    </div>
    <table style="width:100%; border-collapse:collapse;">
        <thead>
            <tr style="border-bottom:2px solid #f1f5f9; text-align:left; background:#f8fafc;">
                <th style="padding: 1rem; width:40px;">#</th>
                <th style="padding: 1rem;">Nama Modul</th>
                <th style="padding: 1rem;">Hierarki</th>
                <th style="padding: 1rem;">Media</th>
                <th style="padding: 1rem; width:90px; text-align:center;">Status</th>
                <th style="padding: 1rem; width:160px; text-align:center;">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($modules as $i => $m)
            <tr style="border-bottom:1px solid #f1f5f9;">
                <td style="padding: 1rem; color:#64748b; font-size:0.85rem;">{{ $modules->firstItem() + $i }}</td>
                <td style="padding: 1rem;">
                    <strong style="color:#1e293b; font-size:0.95rem;">{{ $m->name }}</strong>
                    @if($m->description)
                        <div style="font-size:0.75rem; color:#64748b; margin-top:0.25rem;">{{ Str::limit($m->description, 100) }}</div>
                    @endif
                </td>
                <td style="padding: 1rem; font-size:0.85rem;">
                    <div style="display:flex; flex-direction:column; gap:0.25rem;">
                        <div>
                            <span class="badge" style="background:#f1f3fb; color:#1e2a78; border-color:#d9deee; font-weight:700;">{{ $m->group->name ?? '—' }}</span>
                            <span style="color:#cbd5e1; margin:0 0.15rem;">&rarr;</span>
                            <span class="badge" style="background:#eff6ff; color:#1e40af; border-color:#bfdbfe; font-weight:700;">{{ $m->questionCode?->code ?? '—' }}</span>
                        </div>
                        <div style="color:#64748b; font-size:0.8rem; margin-top:0.15rem;">
                            {{ $m->category->name ?? '—' }} &rarr; <span style="font-weight:500;">{{ $m->subCategory->name ?? '—' }}</span>
                        </div>
                    </div>
                </td>
                <td style="padding: 1rem;">
                    <div style="display:flex; gap:0.35rem; flex-wrap:wrap;">
                        @if($m->pdf_file)
                            <a href="{{ asset($m->pdf_file) }}" target="_blank" class="badge" style="background:#fef2f2; color:#dc2626; border:1px solid #fecaca; text-decoration:none; font-weight:700;">PDF</a>
                        @endif
                        @if($m->video_url)
                            <a href="{{ $m->video_url }}" target="_blank" class="badge" style="background:#f0fdf4; color:#16a34a; border:1px solid #bbf7d0; text-decoration:none; font-weight:700;">Video</a>
                        @endif
                        @if(!$m->pdf_file && !$m->video_url)
                            <span style="color:#94a3b8; font-size:0.75rem;">—</span>
                        @endif
                    </div>
                </td>
                <td style="padding: 1rem; text-align:center;">
                    @if($m->is_active)
                        <span class="badge badge-active" style="background:#ecfdf5; color:#10b981; border-color:#a7f3d0;">Aktif</span>
                    @else
                        <span class="badge badge-inactive" style="background:#fef2f2; color:#ef4444; border-color:#fca5a5;">Nonaktif</span>
                    @endif
                </td>
                <td style="padding: 1rem;">
                    <div style="display:flex; align-items:center; justify-content:center; gap:0.5rem;">
                        <a href="{{ route('admin.modules.edit', $m) }}" class="btn btn-secondary btn-sm" style="font-weight:600;">Edit</a>
                        <form method="POST" action="{{ route('admin.modules.destroy', $m) }}" onsubmit="return confirm('Hapus modul ini?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm" style="font-weight:600;">Hapus</button>
                        </form>
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="6" style="padding: 3rem; text-align:center;">
                    <div class="empty-state">
                        <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="#94a3b8" stroke-width="1.5"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/></svg>
                        <p style="font-weight:600; margin-top:0.75rem; color:#64748b;">Belum ada modul pembelajaran.</p>
                        <a href="{{ route('admin.modules.create') }}" class="btn btn-primary" style="margin-top:0.5rem;">Tambah Modul Pertama</a>
                    </div>
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
    <div style="padding:1rem; border-top:1px solid #f1f5f9;">{{ $modules->links() }}</div>
</div>

<!-- Dependent Dropdowns Fetch Logic for Filters -->
<script>
    const groupSelect = document.getElementById('group_filter');
    const codeSelect = document.getElementById('code_filter');
    const catSelect = document.getElementById('category_filter');
    const subSelect = document.getElementById('subcategory_filter');

    async function loadCodes(groupId, selectedCodeId = null) {
        codeSelect.innerHTML = '<option value="">Semua Kode</option>';
        codeSelect.disabled = true;
        catSelect.innerHTML = '<option value="">Semua Kategori</option>';
        catSelect.disabled = true;
        subSelect.innerHTML = '<option value="">Semua Sub Kategori</option>';
        subSelect.disabled = true;

        if (!groupId) return;

        try {
            const response = await fetch(`/admin/api/codes/${groupId}`);
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
        subSelect.innerHTML = '<option value="">Semua Sub Kategori</option>';
        subSelect.disabled = true;

        if (!codeId) return;

        try {
            const response = await fetch(`/admin/api/categories/${codeId}`);
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
                if (selectedCatId) catSelect.dispatchEvent(new Event('change'));
            }
        } catch (e) { console.error(e); }
    }

    async function loadSubCategories(catId, selectedSubId = null) {
        subSelect.innerHTML = '<option value="">Semua Sub Kategori</option>';
        subSelect.disabled = true;

        if (!catId) return;

        try {
            const response = await fetch(`/admin/api/subcategories/${catId}`);
            const subs = await response.json();
            if (subs.length > 0) {
                subs.forEach(s => {
                    const opt = document.createElement('option');
                    opt.value = s.id;
                    opt.textContent = s.name;
                    if (selectedSubId && s.id == selectedSubId) opt.selected = true;
                    subSelect.appendChild(opt);
                });
                subSelect.disabled = false;
            }
        } catch (e) { console.error(e); }
    }

    groupSelect.addEventListener('change', function() {
        loadCodes(this.value);
    });

    codeSelect.addEventListener('change', function() {
        loadCategories(this.value);
    });

    catSelect.addEventListener('change', function() {
        loadSubCategories(this.value);
    });

    document.addEventListener('DOMContentLoaded', function() {
        const oldGroup = "{{ request('group_id') }}";
        const oldCode = "{{ request('question_code_id') }}";
        const oldCat = "{{ request('category_id') }}";
        const oldSub = "{{ request('sub_category_id') }}";

        if (oldGroup) {
            loadCodes(oldGroup, oldCode).then(() => {
                if (oldCode) {
                    loadCategories(oldCode, oldCat).then(() => {
                        if (oldCat) {
                            loadSubCategories(oldCat, oldSub);
                        }
                    });
                }
            });
        }
    });
</script>
@endsection
