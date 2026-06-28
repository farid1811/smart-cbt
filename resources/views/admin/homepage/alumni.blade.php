@extends('admin.layouts.app')

@section('title', 'Kelola Alumni Homepage')

@push('styles')
<style>
    /* ── Alumni Grid ──────────────────────────────────────────────────── */
    .alumni-page-grid {
        display: grid;
        grid-template-columns: 1fr 360px;
        gap: 1.5rem;
        align-items: start;
    }

    /* ── Alumni Cards Grid ───────────────────────────────────────────── */
    .alumni-cards {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
        gap: 1rem;
        margin-top: 1rem;
    }

    .alumni-card {
        background: var(--surface);
        border: 1px solid var(--border);
        border-radius: var(--radius-lg);
        overflow: hidden;
        box-shadow: var(--shadow-sm);
        transition: all var(--transition);
        position: relative;
    }
    .alumni-card:hover {
        border-color: var(--primary-mid);
        box-shadow: var(--shadow-md);
        transform: translateY(-2px);
    }

    .alumni-card-img {
        width: 100%;
        aspect-ratio: 1 / 1;
        object-fit: cover;
        background: var(--surface2);
        display: block;
    }

    .alumni-card-body {
        padding: 0.875rem 1rem;
    }

    .alumni-card-name {
        font-size: 0.9rem;
        font-weight: 700;
        color: var(--text);
        line-height: 1.3;
        margin-bottom: 0.25rem;
    }

    .alumni-card-instansi {
        font-size: 0.75rem;
        font-weight: 600;
        color: var(--info);
        background: var(--info-soft);
        display: inline-flex;
        padding: 0.2rem 0.5rem;
        border-radius: 99px;
        border: 1px solid #BFDBFE;
        line-height: 1.4;
        margin-bottom: 0.25rem;
    }

    .alumni-card-year {
        font-size: 0.7rem;
        color: var(--text-muted);
        font-weight: 500;
    }

    .alumni-card-actions {
        display: flex;
        gap: 0.375rem;
        padding: 0.625rem 0.875rem;
        border-top: 1px solid var(--border);
        background: var(--surface2);
    }

    .alumni-card-actions a,
    .alumni-card-actions button {
        flex: 1;
        text-align: center;
        font-size: 0.75rem;
        font-weight: 600;
        padding: 0.3125rem 0.375rem;
        border-radius: var(--radius-sm);
        cursor: pointer;
        transition: all var(--transition);
        border: 1px solid transparent;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 0.25rem;
        font-family: 'Inter', sans-serif;
    }

    /* ── Sticky Form Panel ──────────────────────────────────────────── */
    .alumni-form-panel {
        background: var(--surface);
        border: 1px solid var(--border);
        border-radius: var(--radius-lg);
        box-shadow: var(--shadow-sm);
        position: sticky;
        top: calc(var(--topbar-h) + 1.75rem);
        overflow: hidden;
    }

    .alumni-form-header {
        padding: 1rem 1.25rem;
        border-bottom: 1px solid var(--border);
        display: flex;
        align-items: center;
        gap: 0.625rem;
        background: var(--surface2);
    }

    .alumni-form-title {
        font-size: 0.9375rem;
        font-weight: 700;
        color: var(--text);
    }

    .alumni-form-body {
        padding: 1.25rem;
    }

    /* ── Photo Preview ──────────────────────────────────────────────── */
    .photo-preview-box {
        width: 100%;
        aspect-ratio: 1 / 1;
        border: 2px dashed var(--border);
        border-radius: var(--radius);
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all var(--transition);
        overflow: hidden;
        position: relative;
        margin-bottom: 0.75rem;
        background: var(--surface2);
    }
    .photo-preview-box:hover { border-color: var(--primary); background: var(--primary-soft); }
    .photo-preview-box img { width: 100%; height: 100%; object-fit: cover; }

    .photo-preview-placeholder {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 0.5rem;
        color: var(--text-muted);
        font-size: 0.8125rem;
        text-align: center;
        padding: 1rem;
    }

    /* ── Empty State ─────────────────────────────────────────────────── */
    .alumni-empty {
        grid-column: 1 / -1;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        padding: 3rem 1rem;
        text-align: center;
        color: var(--text-muted);
        background: var(--surface2);
        border: 2px dashed var(--border);
        border-radius: var(--radius-lg);
    }

    @media (max-width: 900px) {
        .alumni-page-grid { grid-template-columns: 1fr; }
        .alumni-form-panel { position: static; }
        .alumni-cards { grid-template-columns: repeat(auto-fill, minmax(160px, 1fr)); }
    }
</style>
@endpush

@section('topbar-actions')
    <a href="{{ route('admin.homepage.index') }}" class="btn btn-secondary btn-sm">
        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 12H5M12 19l-7-7 7-7"/></svg>
        Kembali ke Pengaturan
    </a>
@endsection

@section('content')

<div class="alumni-page-grid">

    {{-- ── LEFT: Alumni List ─────────────────────────────────────────────── --}}
    <div>
        <div class="table-card">
            <div class="table-header">
                <h3>
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="display:inline;vertical-align:-2px;margin-right:6px;"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                    Daftar Alumni
                    <span class="badge badge-info" style="margin-left:0.5rem;font-size:0.75rem;">{{ $alumniList->count() }} alumni</span>
                </h3>
                <p style="font-size:0.8rem;color:var(--text-muted);">Alumni akan tampil di Homepage secara otomatis.</p>
            </div>

            @if($alumniList->isEmpty())
                <div style="padding: 1.25rem;">
                    <div class="alumni-empty">
                        <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" style="opacity:0.3;margin-bottom:0.75rem;"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                        <p style="font-weight:600;">Belum ada alumni</p>
                        <p style="font-size:0.8rem;margin-top:0.25rem;">Gunakan form di sebelah kanan untuk menambahkan alumni pertama.</p>
                    </div>
                </div>
            @else
                <div style="padding: 1.25rem;">
                    <div class="alumni-cards">
                        @foreach($alumniList as $a)
                        <div class="alumni-card {{ isset($editAlumni) && $editAlumni->id === $a->id ? 'ring-2 ring-primary' : '' }}" style="{{ isset($editAlumni) && $editAlumni->id === $a->id ? 'border-color: var(--primary); box-shadow: 0 0 0 3px rgba(30,42,120,0.1);' : '' }}">
                            <img src="{{ $a->foto_url }}" alt="{{ $a->nama }}" class="alumni-card-img" loading="lazy">
                            <div class="alumni-card-body">
                                <div class="alumni-card-name">{{ $a->nama }}</div>
                                <div class="alumni-card-instansi">{{ $a->instansi }}</div>
                                <div class="alumni-card-year">Lolos {{ $a->tahun_lulus }}</div>
                            </div>
                            <div class="alumni-card-actions">
                                <a href="{{ route('admin.alumni.edit', $a) }}" class="action-btn action-btn-edit">
                                    <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                                    Edit
                                </a>
                                <form method="POST" action="{{ route('admin.alumni.destroy', $a) }}" onsubmit="return confirm('Hapus alumni {{ addslashes($a->nama) }}?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="action-btn action-btn-delete">
                                        <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14H6L5 6"/><path d="M10 11v6M14 11v6"/><path d="M9 6V4h6v2"/></svg>
                                        Hapus
                                    </button>
                                </form>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </div>

    {{-- ── RIGHT: Add/Edit Form ──────────────────────────────────────────── --}}
    <div>
        <div class="alumni-form-panel">
            <div class="alumni-form-header">
                @if(isset($editAlumni))
                    <div class="cms-section-icon blue" style="width:30px;height:30px;border-radius:7px;background:#EFF6FF;color:#2563EB;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                    </div>
                    <div>
                        <div class="alumni-form-title">Edit Alumni</div>
                        <div style="font-size:0.75rem;color:var(--text-muted);">{{ $editAlumni->nama }}</div>
                    </div>
                @else
                    <div style="width:30px;height:30px;border-radius:7px;background:#ECFDF5;color:#059669;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                    </div>
                    <div>
                        <div class="alumni-form-title">Tambah Alumni</div>
                        <div style="font-size:0.75rem;color:var(--text-muted);">Isi data alumni baru</div>
                    </div>
                @endif
            </div>

            <div class="alumni-form-body">
                @if(isset($editAlumni))
                    {{-- EDIT FORM --}}
                    <form method="POST" action="{{ route('admin.alumni.update', $editAlumni) }}" enctype="multipart/form-data">
                        @csrf @method('PUT')
                @else
                    {{-- CREATE FORM --}}
                    <form method="POST" action="{{ route('admin.alumni.store') }}" enctype="multipart/form-data">
                        @csrf
                @endif

                {{-- Photo Upload --}}
                <div class="form-group">
                    <label class="form-label">Foto Alumni</label>
                    <div class="photo-preview-box" id="photoBox" onclick="document.getElementById('fotoInput').click()">
                        @if(isset($editAlumni) && $editAlumni->foto)
                            <img src="{{ $editAlumni->foto_url }}" alt="Preview" id="photoPreviewImg">
                            <div id="photoPlaceholder" style="display:none;" class="photo-preview-placeholder">
                        @else
                            <img src="" alt="Preview" id="photoPreviewImg" style="display:none;">
                            <div id="photoPlaceholder" class="photo-preview-placeholder">
                        @endif
                                <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg>
                                <span>Klik untuk upload foto</span>
                                <span style="font-size:0.7rem;">JPG, JPEG, PNG, WebP — maks. 10 MB</span>
                            </div>
                    </div>
                    <input type="file" id="fotoInput" name="foto" accept="image/*" style="display:none;" onchange="previewPhoto(this)">
                    @if(isset($editAlumni) && $editAlumni->foto)
                        <label style="display:flex;align-items:center;gap:0.5rem;font-size:0.8rem;color:var(--text-muted);cursor:pointer;margin-top:0.375rem;">
                            <input type="checkbox" name="hapus_foto" value="1" id="hapusFoto" onchange="toggleHapusFoto(this)">
                            Hapus foto (gunakan avatar otomatis)
                        </label>
                    @endif
                </div>

                {{-- Nama --}}
                <div class="form-group">
                    <label class="form-label" for="nama">Nama Alumni <span style="color:var(--error)">*</span></label>
                    <input type="text" id="nama" name="nama" class="form-control @error('nama') is-invalid @enderror"
                        value="{{ old('nama', $editAlumni->nama ?? '') }}" placeholder="Contoh: Budi Santoso" maxlength="200" required>
                    @error('nama')<div class="form-error">{{ $message }}</div>@enderror
                </div>

                {{-- Instansi --}}
                <div class="form-group">
                    <label class="form-label" for="instansi">Instansi / PTN Tujuan <span style="color:var(--error)">*</span></label>
                    <input type="text" id="instansi" name="instansi" class="form-control @error('instansi') is-invalid @enderror"
                        value="{{ old('instansi', $editAlumni->instansi ?? '') }}" placeholder="Contoh: PKN STAN, IPDN, UI..." maxlength="300" required>
                    @error('instansi')<div class="form-error">{{ $message }}</div>@enderror
                </div>

                {{-- Tahun Lulus --}}
                <div class="form-group">
                    <label class="form-label" for="tahun_lulus">Tahun Lulus <span style="color:var(--error)">*</span></label>
                    <input type="number" id="tahun_lulus" name="tahun_lulus" class="form-control @error('tahun_lulus') is-invalid @enderror"
                        value="{{ old('tahun_lulus', $editAlumni->tahun_lulus ?? date('Y')) }}" min="2000" max="{{ date('Y') + 1 }}" required>
                    @error('tahun_lulus')<div class="form-error">{{ $message }}</div>@enderror
                </div>

                {{-- Urutan --}}
                <div class="form-group">
                    <label class="form-label" for="urutan">Urutan Tampil <small style="color:var(--text-muted);">(0 = pertama)</small></label>
                    <input type="number" id="urutan" name="urutan" class="form-control"
                        value="{{ old('urutan', $editAlumni->urutan ?? 0) }}" min="0">
                    <span class="form-hint">Angka lebih kecil muncul lebih dulu di homepage.</span>
                </div>

                <div style="display:flex;gap:0.75rem;margin-top:1.25rem;">
                    @if(isset($editAlumni))
                        <a href="{{ route('admin.alumni.index') }}" class="btn btn-secondary" style="flex:1;justify-content:center;">Batal</a>
                        <button type="submit" class="btn btn-primary" style="flex:2;justify-content:center;">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
                            Simpan Perubahan
                        </button>
                    @else
                        <button type="submit" class="btn btn-primary" style="width:100%;justify-content:center;">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                            Tambah Alumni
                        </button>
                    @endif
                </div>

                </form>
            </div>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
function previewPhoto(input) {
    const file = input.files[0];
    if (!file) return;
    const reader = new FileReader();
    reader.onload = function(e) {
        const img = document.getElementById('photoPreviewImg');
        const placeholder = document.getElementById('photoPlaceholder');
        img.src = e.target.result;
        img.style.display = 'block';
        if (placeholder) placeholder.style.display = 'none';
    };
    reader.readAsDataURL(file);
}

function toggleHapusFoto(cb) {
    const img = document.getElementById('photoPreviewImg');
    const placeholder = document.getElementById('photoPlaceholder');
    if (cb.checked) {
        img.style.display = 'none';
        if (placeholder) placeholder.style.display = 'flex';
    } else {
        img.style.display = 'block';
        if (placeholder) placeholder.style.display = 'none';
    }
}
</script>
@endpush
