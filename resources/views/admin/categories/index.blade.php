@extends('admin.layouts.app')
@section('title', 'Kategori & Hierarki Soal')

@section('content')
<style>
    .tab-nav {
        display: flex;
        gap: 0.5rem;
        border-bottom: 2px solid #e2e8f0;
        margin-bottom: 1.5rem;
        padding-bottom: 2px;
    }
    .tab-btn {
        background: none;
        border: none;
        padding: 0.75rem 1.25rem;
        font-weight: 600;
        color: #64748b;
        cursor: pointer;
        border-bottom: 2px solid transparent;
        margin-bottom: -2px;
        transition: all 0.2s ease;
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
        box-shadow: 0 20px 25px -5px rgba(0,0,0,0.1), 0 10px 10px -5px rgba(0,0,0,0.04);
        border: 1px solid #e2e8f0;
        overflow: hidden;
        animation: modalSlide 0.2s ease-out;
    }
    @keyframes modalSlide {
        from { transform: translateY(20px); opacity: 0; }
        to { transform: translateY(0); opacity: 1; }
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
        font-size: 1.15rem;
        font-weight: 700;
        color: #0f172a;
    }
    .modal-close {
        background: none;
        border: none;
        color: #94a3b8;
        font-size: 1.5rem;
        cursor: pointer;
        line-height: 1;
    }
    .modal-close:hover {
        color: #64748b;
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
    .form-group input, .form-group select {
        width: 100%;
        padding: 0.625rem 0.875rem;
        border: 1px solid #cbd5e1;
        border-radius: 6px;
        font-size: 0.9rem;
        transition: all 0.2s;
    }
    .form-group input:focus, .form-group select:focus {
        border-color: #3b82f6;
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.15);
        outline: none;
    }
    .badge-code {
        background: #eff6ff;
        color: #1e40af;
        border: 1px solid #bfdbfe;
        font-weight: 700;
        padding: 0.25rem 0.625rem;
        border-radius: 9999px;
        font-size: 0.75rem;
    }
</style>

<div class="table-card" style="padding: 1.5rem; border-radius: 12px; background:#fff; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05);">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
        <div>
            <h2 style="font-weight: 800; color: #0f172a; margin: 0;">Topik & Kategori Soal</h2>
            <p style="color: #64748b; font-size: 0.875rem; margin-top: 0.25rem;">Kelola struktur berjenjang bank soal Smart CBT (Grup &rarr; Kode &rarr; Kategori &rarr; Sub Kategori).</p>
        </div>
        <div>
            <button onclick="openModal('addCodeModal')" class="btn btn-primary" id="btn-add-code" style="display:none;">+ Kode Soal</button>
            <button onclick="openModal('addCategoryModal')" class="btn btn-primary" id="btn-add-category" style="display:none;">+ Kategori</button>
        </div>
    </div>

    <!-- Navigation Tabs -->
    <div class="tab-nav">
        <button class="tab-btn active" onclick="switchTab('code')">1. Kode Soal</button>
        <button class="tab-btn" onclick="switchTab('category')">2. Kategori & Sub Kategori</button>
    </div>

    <!-- TAB 1: KODE SOAL -->
    <div id="tab-code" class="tab-content active">
        <table style="width: 100%; border-collapse: collapse;">
            <thead>
                <tr style="border-bottom: 2px solid #f1f5f9; text-align: left;">
                    <th style="padding: 0.75rem 1rem;">#</th>
                    <th style="padding: 0.75rem 1rem;">Grup</th>
                    <th style="padding: 0.75rem 1rem;">Kode</th>
                    <th style="padding: 0.75rem 1rem;">Nama Kode</th>
                    <th style="padding: 0.75rem 1rem;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($codes as $i => $code)
                <tr style="border-bottom: 1px solid #f1f5f9;">
                    <td style="padding: 1rem; color: #64748b;">{{ $i+1 }}</td>
                    <td style="padding: 1rem;">
                        <span class="badge" style="background:#f1f3fb; color:#1e2a78; border-color:#d9deee; font-weight:700;">{{ $code->group->name ?? '—' }}</span>
                    </td>
                    <td style="padding: 1rem;"><span class="badge-code">{{ $code->code }}</span></td>
                    <td style="padding: 1rem; font-weight: 600; color: #1e293b;">{{ $code->name }}</td>
                    <td style="padding: 1rem;">
                        <div style="display:flex; gap:0.5rem;">
                            <button onclick="editCode({{ json_encode($code) }})" class="btn btn-secondary btn-sm">Edit</button>
                            <form method="POST" action="{{ route('admin.categories.destroyCode', $code) }}" onsubmit="return confirm('Hapus kode soal ini beserta kategori didalamnya?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm">Hapus</button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="5" style="padding:2rem; text-align:center; color:#64748b;">Belum ada kode soal. Silakan tambahkan terlebih dahulu.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- TAB 2: KATEGORI -->
    <div id="tab-category" class="tab-content">
        <table style="width: 100%; border-collapse: collapse;">
            <thead>
                <tr style="border-bottom: 2px solid #f1f5f9; text-align: left;">
                    <th style="padding: 0.75rem 1rem;">#</th>
                    <th style="padding: 0.75rem 1rem;">Grup &rarr; Kode Soal</th>
                    <th style="padding: 0.75rem 1rem;">Nama Kategori</th>
                    <th style="padding: 0.75rem 1rem; width: 45%;">Sub Kategori</th>
                    <th style="padding: 0.75rem 1rem;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($categories as $i => $cat)
                <tr style="border-bottom: 1px solid #f1f5f9;">
                    <td style="padding: 1rem; color: #64748b;">{{ $i+1 }}</td>
                    <td style="padding: 1rem;">
                        <span style="font-weight:700; color:#475569;">{{ $cat->questionCode->group->name ?? '—' }}</span>
                        <span style="color:#cbd5e1; margin:0 0.25rem;">&rarr;</span>
                        <span class="badge-code">{{ $cat->questionCode->code ?? '—' }}</span>
                    </td>
                    <td style="padding: 1rem; font-weight: 600; color: #1e293b;">{{ $cat->name }}</td>
                    <td style="padding: 1rem;">
                        <div style="display: flex; flex-wrap: wrap; gap: 0.5rem; align-items: center;">
                            @forelse($cat->subCategories as $sub)
                                <span style="background: #f8fafc; border: 1px solid #e2e8f0; color: #334155; padding: 0.35rem 0.75rem; border-radius: 6px; font-size: 0.825rem; font-weight: 500; display: inline-flex; align-items: center; gap: 0.5rem; transition: all 0.15s ease-in-out;">
                                    <span>{{ $sub->name }}</span>
                                    <span style="display: inline-flex; gap: 0.25rem; border-left: 1px solid #cbd5e1; padding-left: 0.25rem; margin-left: 0.25rem;">
                                        <button type="button" onclick="editSubCategory({{ json_encode($sub) }})" style="background: none; border: none; padding: 0.125rem; color: #64748b; cursor: pointer; display: inline-flex; align-items: center; border-radius: 4px;" title="Edit Sub Kategori" onmouseover="this.style.color='#1e40af'" onmouseout="this.style.color='#64748b'">
                                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 1 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>
                                        </button>
                                        <form method="POST" action="{{ route('admin.categories.destroySubCategory', $sub) }}" onsubmit="return confirm('Hapus sub kategori ini?')" style="display: inline; margin: 0;">
                                            @csrf @method('DELETE')
                                            <button type="submit" style="background: none; border: none; padding: 0.125rem; color: #ef4444; cursor: pointer; display: inline-flex; align-items: center; border-radius: 4px;" title="Hapus Sub Kategori" onmouseover="this.style.color='#b91c1c'" onmouseout="this.style.color='#ef4444'">
                                                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path><line x1="10" y1="11" x2="10" y2="17"></line><line x1="14" y1="11" x2="14" y2="17"></line></svg>
                                            </button>
                                        </form>
                                    </span>
                                </span>
                            @empty
                                <span style="color: #94a3b8; font-size: 0.825rem; font-style: italic;">Belum ada sub kategori</span>
                            @endforelse
                            <button type="button" onclick="addSubCategoryForCategory({{ $cat->id }})" style="background: #f0fdf4; border: 1px dashed #bbf7d0; color: #16a34a; padding: 0.35rem 0.75rem; border-radius: 6px; font-size: 0.825rem; font-weight: 600; cursor: pointer; display: inline-flex; align-items: center; gap: 0.25rem; transition: all 0.15s ease;" onmouseover="this.style.background='#dcfce7'" onmouseout="this.style.background='#f0fdf4'">
                                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                                <span>Tambah Sub</span>
                            </button>
                        </div>
                    </td>
                    <td style="padding: 1rem;">
                        <div style="display:flex; gap:0.5rem;">
                            <button onclick="editCategory({{ json_encode($cat) }})" class="btn btn-secondary btn-sm">Edit</button>
                            <form method="POST" action="{{ route('admin.categories.destroy', $cat) }}" onsubmit="return confirm('Hapus kategori ini beserta sub-kategori didalamnya?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm">Hapus</button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="5" style="padding:2rem; text-align:center; color:#64748b;">Belum ada kategori. Silakan tambahkan terlebih dahulu.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>


</div>

<!-- ==============================================
     MODALS
     ============================================== -->

<!-- MODAL ADD KODE SOAL -->
<div id="addCodeModal" class="modal-backdrop">
    <div class="modal-box">
        <div class="modal-header">
            <h3>+ Tambah Kode Soal</h3>
            <button onclick="closeModal('addCodeModal')" class="modal-close">&times;</button>
        </div>
        <form method="POST" action="{{ route('admin.categories.storeCode') }}">
            @csrf
            <div class="modal-body">
                <div class="form-group">
                    <label>Grup Ujian</label>
                    <select name="group_id" required>
                        @foreach($groups as $grp)
                            <option value="{{ $grp->id }}">{{ $grp->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label>Nama Kode (e.g. Tes Intelegensia Umum)</label>
                    <input type="text" name="name" required placeholder="Masukkan nama subtest">
                </div>
                <div class="form-group">
                    <label>Kode Singkatan (e.g. TIU)</label>
                    <input type="text" name="code" required placeholder="Masukkan kode unik">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" onclick="closeModal('addCodeModal')" class="btn btn-secondary">Batal</button>
                <button type="submit" class="btn btn-primary">Simpan</button>
            </div>
        </form>
    </div>
</div>

<!-- MODAL EDIT KODE SOAL -->
<div id="editCodeModal" class="modal-backdrop">
    <div class="modal-box">
        <div class="modal-header">
            <h3>Edit Kode Soal</h3>
            <button onclick="closeModal('editCodeModal')" class="modal-close">&times;</button>
        </div>
        <form id="editCodeForm" method="POST">
            @csrf @method('PUT')
            <div class="modal-body">
                <div class="form-group">
                    <label>Grup Ujian</label>
                    <select name="group_id" id="edit_code_group_id" required>
                        @foreach($groups as $grp)
                            <option value="{{ $grp->id }}">{{ $grp->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label>Nama Kode</label>
                    <input type="text" name="name" id="edit_code_name" required>
                </div>
                <div class="form-group">
                    <label>Kode Singkatan</label>
                    <input type="text" name="code" id="edit_code_code" required>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" onclick="closeModal('editCodeModal')" class="btn btn-secondary">Batal</button>
                <button type="submit" class="btn btn-primary">Perbarui</button>
            </div>
        </form>
    </div>
</div>

<!-- MODAL ADD KATEGORI -->
<div id="addCategoryModal" class="modal-backdrop">
    <div class="modal-box">
        <div class="modal-header">
            <h3>+ Tambah Kategori</h3>
            <button onclick="closeModal('addCategoryModal')" class="modal-close">&times;</button>
        </div>
        <form method="POST" action="{{ route('admin.categories.store') }}">
            @csrf
            <div class="modal-body">
                <div class="form-group">
                    <label>Kode Soal (Induk)</label>
                    <select name="question_code_id" required>
                        @foreach($codes as $c)
                            <option value="{{ $c->id }}">[{{ $c->group->name }}] {{ $c->code }} - {{ $c->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label>Nama Kategori (e.g. Pilar Negara)</label>
                    <input type="text" name="name" required placeholder="Masukkan nama kategori">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" onclick="closeModal('addCategoryModal')" class="btn btn-secondary">Batal</button>
                <button type="submit" class="btn btn-primary">Simpan</button>
            </div>
        </form>
    </div>
</div>

<!-- MODAL EDIT KATEGORI -->
<div id="editCategoryModal" class="modal-backdrop">
    <div class="modal-box">
        <div class="modal-header">
            <h3>Edit Kategori</h3>
            <button onclick="closeModal('editCategoryModal')" class="modal-close">&times;</button>
        </div>
        <form id="editCategoryForm" method="POST">
            @csrf @method('PUT')
            <div class="modal-body">
                <div class="form-group">
                    <label>Kode Soal (Induk)</label>
                    <select name="question_code_id" id="edit_cat_code_id" required>
                        @foreach($codes as $c)
                            <option value="{{ $c->id }}">[{{ $c->group->name }}] {{ $c->code }} - {{ $c->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label>Nama Kategori</label>
                    <input type="text" name="name" id="edit_cat_name" required>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" onclick="closeModal('editCategoryModal')" class="btn btn-secondary">Batal</button>
                <button type="submit" class="btn btn-primary">Perbarui</button>
            </div>
        </form>
    </div>
</div>

<!-- MODAL ADD SUB KATEGORI -->
<div id="addSubCategoryModal" class="modal-backdrop">
    <div class="modal-box">
        <div class="modal-header">
            <h3>+ Tambah Sub Kategori</h3>
            <button onclick="closeModal('addSubCategoryModal')" class="modal-close">&times;</button>
        </div>
        <form method="POST" action="{{ route('admin.categories.storeSubCategory') }}">
            @csrf
            <div class="modal-body">
                <div class="form-group">
                    <label>Kategori (Induk)</label>
                    <select name="category_id" id="add_sub_category_parent_id" required>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}">[{{ $cat->questionCode->group->name }} - {{ $cat->questionCode->code }}] {{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label>Nama Sub Kategori (e.g. Pancasila)</label>
                    <input type="text" name="name" required placeholder="Masukkan nama sub kategori">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" onclick="closeModal('addSubCategoryModal')" class="btn btn-secondary">Batal</button>
                <button type="submit" class="btn btn-primary">Simpan</button>
            </div>
        </form>
    </div>
</div>

<!-- MODAL EDIT SUB KATEGORI -->
<div id="editSubCategoryModal" class="modal-backdrop">
    <div class="modal-box">
        <div class="modal-header">
            <h3>Edit Sub Kategori</h3>
            <button onclick="closeModal('editSubCategoryModal')" class="modal-close">&times;</button>
        </div>
        <form id="editSubCategoryForm" method="POST">
            @csrf @method('PUT')
            <div class="modal-body">
                <div class="form-group">
                    <label>Kategori (Induk)</label>
                    <select name="category_id" id="edit_sub_category_id" required>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}">[{{ $cat->questionCode->group->name }} - {{ $cat->questionCode->code }}] {{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label>Nama Sub Kategori</label>
                    <input type="text" name="name" id="edit_sub_name" required>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" onclick="closeModal('editSubCategoryModal')" class="btn btn-secondary">Batal</button>
                <button type="submit" class="btn btn-primary">Perbarui</button>
            </div>
        </form>
    </div>
</div>

<!-- ==============================================
     SCRIPTS
     ============================================== -->
<script>
    // Tab Switching
    function switchTab(tabName) {
        if (tabName === 'subcategory') {
            tabName = 'category';
        }
        document.querySelectorAll('.tab-btn').forEach(btn => btn.classList.remove('active'));
        document.querySelectorAll('.tab-content').forEach(content => content.classList.remove('active'));
        
        const activeBtn = Array.from(document.querySelectorAll('.tab-btn')).find(btn => btn.getAttribute('onclick').includes(tabName));
        if (activeBtn) activeBtn.classList.add('active');
        
        const activeContent = document.getElementById('tab-' + tabName);
        if (activeContent) activeContent.classList.add('active');

        // Update button visibility
        document.getElementById('btn-add-code').style.display = (tabName === 'code') ? 'inline-block' : 'none';
        document.getElementById('btn-add-category').style.display = (tabName === 'category') ? 'inline-block' : 'none';

        // Update URL parameter
        const url = new URL(window.location);
        url.searchParams.set('tab', tabName);
        window.history.pushState({}, '', url);
    }

    // Modal Control
    function openModal(id) {
        document.getElementById(id).classList.add('show');
    }
    function closeModal(id) {
        document.getElementById(id).classList.remove('show');
    }

    // Populate Edit Code
    function editCode(codeObj) {
        document.getElementById('editCodeForm').action = "/admin/categories/code/" + codeObj.id;
        document.getElementById('edit_code_group_id').value = codeObj.group_id;
        document.getElementById('edit_code_name').value = codeObj.name;
        document.getElementById('edit_code_code').value = codeObj.code;
        openModal('editCodeModal');
    }

    // Populate Edit Category
    function editCategory(catObj) {
        document.getElementById('editCategoryForm').action = "/admin/categories/" + catObj.id;
        document.getElementById('edit_cat_code_id').value = catObj.question_code_id;
        document.getElementById('edit_cat_name').value = catObj.name;
        openModal('editCategoryModal');
    }

    // Populate Edit Sub Category
    function editSubCategory(subObj) {
        document.getElementById('editSubCategoryForm').action = "/admin/categories/subcategory/" + subObj.id;
        document.getElementById('edit_sub_category_id').value = subObj.category_id;
        document.getElementById('edit_sub_name').value = subObj.name;
        openModal('editSubCategoryModal');
    }

    // Helper to add subcategory directly for a category
    function addSubCategoryForCategory(catId) {
        const select = document.getElementById('add_sub_category_parent_id');
        if (select) {
            select.value = catId;
        }
        openModal('addSubCategoryModal');
    }

    // On Load: read URL parameter and switch to tab
    document.addEventListener("DOMContentLoaded", function() {
        const params = new URLSearchParams(window.location.search);
        const activeTab = params.get('tab') || 'code';
        switchTab(activeTab);
    });
</script>
@endsection
