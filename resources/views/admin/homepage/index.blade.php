@extends('admin.layouts.app')

@section('title', 'Pengaturan Homepage')

@push('styles')
<style>
    /* ── CMS Layout ─────────────────────────────────────────────────── */
    .cms-grid {
        display: grid;
        grid-template-columns: 240px 1fr;
        gap: 1.5rem;
        align-items: start;
    }

    /* Sticky sidebar navigation */
    .cms-nav {
        background: var(--surface);
        border: 1px solid var(--border);
        border-radius: var(--radius-lg);
        box-shadow: var(--shadow-sm);
        position: sticky;
        top: calc(var(--topbar-h) + 1.75rem);
        overflow: hidden;
    }

    .cms-nav-header {
        padding: 1rem 1.125rem 0.75rem;
        border-bottom: 1px solid var(--border);
        font-size: 0.6875rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.07em;
        color: var(--text-light);
    }

    .cms-nav-list { padding: 0.5rem 0.5rem; }

    .cms-nav-item {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.45rem 0.625rem;
        border-radius: var(--radius-sm);
        font-size: 0.8125rem;
        font-weight: 500;
        color: var(--text-muted);
        text-decoration: none;
        transition: all var(--transition);
        cursor: pointer;
        border: none;
        background: none;
        width: 100%;
        text-align: left;
    }
    .cms-nav-item:hover  { background: var(--surface2); color: var(--text); }
    .cms-nav-item.active { background: var(--primary-soft); color: var(--primary); font-weight: 600; }

    .cms-nav-dot {
        width: 7px; height: 7px;
        border-radius: 50%;
        background: currentColor;
        flex-shrink: 0;
        opacity: 0.5;
    }
    .cms-nav-item.active .cms-nav-dot { opacity: 1; }

    /* ── CMS Sections ────────────────────────────────────────────────── */
    .cms-section {
        background: var(--surface);
        border: 1px solid var(--border);
        border-radius: var(--radius-lg);
        box-shadow: var(--shadow-sm);
        overflow: hidden;
        margin-bottom: 1.25rem;
        scroll-margin-top: calc(var(--topbar-h) + 2rem);
    }

    .cms-section-header {
        padding: 1rem 1.25rem;
        border-bottom: 1px solid var(--border);
        display: flex;
        align-items: center;
        gap: 0.75rem;
        background: var(--surface2);
    }

    .cms-section-icon {
        width: 34px; height: 34px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    .cms-section-icon.blue   { background:#EFF6FF; color:#2563EB; }
    .cms-section-icon.green  { background:#ECFDF5; color:#059669; }
    .cms-section-icon.orange { background:#FFF7ED; color:#EA580C; }
    .cms-section-icon.purple { background:#F5F3FF; color:#7C3AED; }
    .cms-section-icon.teal   { background:#F0FDFA; color:#0D9488; }
    .cms-section-icon.rose   { background:#FFF1F2; color:#E11D48; }
    .cms-section-icon.amber  { background:#FFFBEB; color:#D97706; }

    .cms-section-title { font-size: 0.9375rem; font-weight: 700; color: var(--text); }
    .cms-section-desc  { font-size: 0.75rem; color: var(--text-muted); margin-top: 1px; }

    .cms-section-body { padding: 1.25rem; }

    /* ── Input Groups ────────────────────────────────────────────────── */
    .input-group { margin-bottom: 1rem; }
    .input-group:last-child { margin-bottom: 0; }

    .input-row-2 {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 1rem;
    }

    .input-row-3 {
        display: grid;
        grid-template-columns: 1fr 1fr 1fr;
        gap: 1rem;
    }

    /* ── Preview Badge ──────────────────────────────────────────────── */
    .preview-tag {
        display: inline-flex;
        align-items: center;
        gap: 0.25rem;
        padding: 0.2rem 0.5rem;
        background: #EFF6FF;
        color: #2563EB;
        border: 1px solid #BFDBFE;
        border-radius: 99px;
        font-size: 0.6875rem;
        font-weight: 600;
        margin-left: 0.5rem;
    }

    /* ── Sticky Save Bar ─────────────────────────────────────────────── */
    .save-bar {
        position: sticky;
        bottom: 0;
        left: 0;
        right: 0;
        background: var(--surface);
        border-top: 1px solid var(--border);
        padding: 0.875rem 1.5rem;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 1rem;
        z-index: 50;
        box-shadow: 0 -4px 12px rgba(0,0,0,0.06);
        margin: 0 -1.75rem -1.75rem;
    }

    .save-bar-msg {
        font-size: 0.8125rem;
        color: var(--text-muted);
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    /* ── Char Counter ────────────────────────────────────────────────── */
    .char-counter {
        font-size: 0.6875rem;
        color: var(--text-light);
        text-align: right;
        margin-top: 0.25rem;
    }
    .char-counter.warn { color: var(--warning); }
    .char-counter.over { color: var(--error); }

    /* ── Alumni Quick-link card ──────────────────────────────────────── */
    .alumni-cta-card {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 1rem;
        padding: 1rem 1.25rem;
        background: linear-gradient(135deg, #EFF6FF 0%, #F5F3FF 100%);
        border: 1px solid #BFDBFE;
        border-radius: var(--radius);
    }

    .alumni-cta-info { display: flex; align-items: center; gap: 0.75rem; }
    .alumni-cta-icon {
        width: 40px; height: 40px;
        border-radius: 10px;
        background: #fff;
        border: 1px solid #BFDBFE;
        display: flex; align-items: center; justify-content: center;
        flex-shrink: 0;
        color: #2563EB;
    }
    .alumni-cta-label { font-weight: 700; font-size: 0.875rem; color: var(--text); line-height: 1.3; }
    .alumni-cta-sub   { font-size: 0.75rem; color: var(--text-muted); }

    /* ── Nav divider ─────────────────────────────────────────────────── */
    .cms-nav-divider {
        height: 1px;
        background: var(--border);
        margin: 0.375rem 0.5rem;
    }
    .cms-nav-link-item {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.45rem 0.625rem;
        border-radius: var(--radius-sm);
        font-size: 0.8125rem;
        font-weight: 600;
        color: #7C3AED;
        text-decoration: none;
        transition: all var(--transition);
        background: #F5F3FF;
        margin: 0 0 2px;
    }
    .cms-nav-link-item:hover { background: #EDE9FE; color: #6D28D9; }

    @media (max-width: 900px) {
        .cms-grid { grid-template-columns: 1fr; }
        .cms-nav  { position: static; }
        .input-row-2, .input-row-3 { grid-template-columns: 1fr; }
        .save-bar { margin: 0 -1rem -1rem; padding: 0.75rem 1rem; }
    }
</style>
@endpush

@section('content')

<form id="cmsForm" method="POST" action="{{ route('admin.homepage.update') }}">
@csrf
@method('PUT')

<div class="cms-grid">
    {{-- ── LEFT: Navigation ────────────────────────────────────────────────── --}}
    <div>
        <div class="cms-nav">
            <div class="cms-nav-header">Bagian Homepage</div>
            <div class="cms-nav-list">
                {{-- Alumni Management Link --}}
                <a href="{{ route('admin.alumni.index') }}" class="cms-nav-link-item">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                    Kelola Alumni
                </a>
                <div class="cms-nav-divider"></div>

                {{-- Settings sections --}}
                <a href="#section-identity" class="cms-nav-item" onclick="cmsScrollTo(event, 'section-identity')">
                    <span class="cms-nav-dot"></span> Identitas Lembaga
                </a>
                <a href="#section-hero" class="cms-nav-item" onclick="cmsScrollTo(event, 'section-hero')">
                    <span class="cms-nav-dot"></span> Hero / Banner
                </a>
                <a href="#section-sections" class="cms-nav-item" onclick="cmsScrollTo(event, 'section-sections')">
                    <span class="cms-nav-dot"></span> Judul Seksi
                </a>
                <a href="#section-footer" class="cms-nav-item" onclick="cmsScrollTo(event, 'section-footer')">
                    <span class="cms-nav-dot"></span> Footer & Kontak
                </a>
                <a href="#section-meta" class="cms-nav-item" onclick="cmsScrollTo(event, 'section-meta')">
                    <span class="cms-nav-dot"></span> SEO / Meta
                </a>
            </div>
            <div style="padding: 0.75rem 0.875rem; border-top: 1px solid var(--border);">
                <a href="{{ route('landing') }}" target="_blank" class="btn btn-secondary" style="width:100%; justify-content:center; font-size:0.75rem;">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/><polyline points="15 3 21 3 21 9"/><line x1="10" y1="14" x2="21" y2="3"/></svg>
                    Lihat Homepage
                </a>
            </div>
        </div>
    </div>

    {{-- ── RIGHT: Sections ─────────────────────────────────────────────────── --}}
    <div>

        {{-- 1. Identitas Lembaga --}}
        <div class="cms-section" id="section-identity">
            <div class="cms-section-header">
                <div class="cms-section-icon blue">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
                </div>
                <div>
                    <div class="cms-section-title">Identitas Lembaga</div>
                    <div class="cms-section-desc">Nama, tagline, dan kontak lembaga</div>
                </div>
            </div>
            <div class="cms-section-body">
                <div class="input-row-2">
                    <div class="input-group">
                        <label class="form-label" for="nama_lembaga">Nama Lembaga</label>
                        <input type="text" id="nama_lembaga" name="nama_lembaga" class="form-control" value="{{ old('nama_lembaga', $settings->nama_lembaga) }}" maxlength="200" required>
                    </div>
                    <div class="input-group">
                        <label class="form-label" for="tagline">Tagline</label>
                        <input type="text" id="tagline" name="tagline" class="form-control" value="{{ old('tagline', $settings->tagline) }}" maxlength="200" required>
                        <span class="form-hint">Contoh: Premium Education</span>
                    </div>
                </div>
                <div class="input-row-2">
                    <div class="input-group">
                        <label class="form-label" for="whatsapp_number">Nomor WhatsApp <small style="color:var(--text-muted);">(tanpa + atau spasi)</small></label>
                        <input type="text" id="whatsapp_number" name="whatsapp_number" class="form-control" value="{{ old('whatsapp_number', $settings->whatsapp_number) }}" maxlength="30" required>
                        <span class="form-hint">Contoh: 6285233687867</span>
                    </div>
                    <div class="input-group">
                        <label class="form-label" for="email">Email</label>
                        <input type="email" id="email" name="email" class="form-control" value="{{ old('email', $settings->email) }}" maxlength="200" required>
                    </div>
                </div>
                <div class="input-row-2">
                    <div class="input-group">
                        <label class="form-label" for="instagram">Instagram Handle</label>
                        <input type="text" id="instagram" name="instagram" class="form-control" value="{{ old('instagram', $settings->instagram) }}" maxlength="100" required>
                        <span class="form-hint">Contoh: @bimbelplano_</span>
                    </div>
                </div>
                <div class="input-group">
                    <label class="form-label" for="alamat">Alamat Lengkap</label>
                    <textarea id="alamat" name="alamat" class="form-control" rows="2" maxlength="500" required>{{ old('alamat', $settings->alamat) }}</textarea>
                </div>
            </div>
        </div>

        {{-- 2. Hero / Banner --}}
        <div class="cms-section" id="section-hero">
            <div class="cms-section-header">
                <div class="cms-section-icon orange">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
                </div>
                <div>
                    <div class="cms-section-title">Hero / Banner Utama</div>
                    <div class="cms-section-desc">Bagian pertama yang dilihat pengunjung</div>
                </div>
            </div>
            <div class="cms-section-body">
                <div class="input-group">
                    <label class="form-label" for="hero_badge">Badge Label (di atas judul)</label>
                    <input type="text" id="hero_badge" name="hero_badge" class="form-control" value="{{ old('hero_badge', $settings->hero_badge) }}" maxlength="200" required>
                    <span class="form-hint">Contoh: 🎓 Lembaga Bimbingan Belajar Premium</span>
                </div>
                <div class="input-group">
                    <label class="form-label" for="hero_title">Judul Utama Hero</label>
                    <textarea id="hero_title" name="hero_title" class="form-control" rows="2" maxlength="500" required>{{ old('hero_title', $settings->hero_title) }}</textarea>
                </div>
                <div class="input-group">
                    <label class="form-label" for="hero_subtitle">Sub-judul / Deskripsi Hero</label>
                    <textarea id="hero_subtitle" name="hero_subtitle" class="form-control" rows="3" maxlength="1000" required>{{ old('hero_subtitle', $settings->hero_subtitle) }}</textarea>
                </div>
                <div class="input-row-3">
                    <div class="input-group">
                        <label class="form-label" for="hero_cta_primary">Teks Tombol Utama</label>
                        <input type="text" id="hero_cta_primary" name="hero_cta_primary" class="form-control" value="{{ old('hero_cta_primary', $settings->hero_cta_primary) }}" maxlength="100" required>
                    </div>
                    <div class="input-group">
                        <label class="form-label" for="hero_cta_whatsapp">Teks Tombol WhatsApp</label>
                        <input type="text" id="hero_cta_whatsapp" name="hero_cta_whatsapp" class="form-control" value="{{ old('hero_cta_whatsapp', $settings->hero_cta_whatsapp) }}" maxlength="100" required>
                    </div>
                    <div class="input-group">
                        <label class="form-label" for="hero_passing_rate">Tingkat Kelulusan (%)</label>
                        <input type="number" id="hero_passing_rate" name="hero_passing_rate" class="form-control" value="{{ old('hero_passing_rate', $settings->hero_passing_rate) }}" min="0" max="100" step="0.1" required>
                        <span class="form-hint">Contoh: 98.6 → "Alumni Lolos 98.6%"</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- 3. Alumni CTA --}}
        <div class="cms-section" id="section-alumni-cta">
            <div class="cms-section-header">
                <div class="cms-section-icon purple">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                </div>
                <div>
                    <div class="cms-section-title">Data Alumni</div>
                    <div class="cms-section-desc">Tambah, edit, hapus foto & profil alumni</div>
                </div>
            </div>
            <div class="cms-section-body">
                <div class="alumni-cta-card">
                    <div class="alumni-cta-info">
                        <div class="alumni-cta-icon">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                        </div>
                        <div>
                            <div class="alumni-cta-label">Halaman Kelola Alumni</div>
                            <div class="alumni-cta-sub">Data alumni tampil otomatis di homepage. Upload foto, nama, instansi, dan tahun lulus.</div>
                        </div>
                    </div>
                    <a href="{{ route('admin.alumni.index') }}" class="btn btn-primary" style="white-space:nowrap;flex-shrink:0;">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/></svg>
                        Kelola Alumni
                    </a>
                </div>
            </div>
        </div>

        {{-- 4. Section Headings --}}
        <div class="cms-section" id="section-sections">
            <div class="cms-section-header">
                <div class="cms-section-icon amber">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="8" y1="6" x2="21" y2="6"/><line x1="8" y1="12" x2="21" y2="12"/><line x1="8" y1="18" x2="21" y2="18"/><line x1="3" y1="6" x2="3.01" y2="6"/><line x1="3" y1="12" x2="3.01" y2="12"/><line x1="3" y1="18" x2="3.01" y2="18"/></svg>
                </div>
                <div>
                    <div class="cms-section-title">Judul Setiap Seksi</div>
                    <div class="cms-section-desc">Ubah heading dan deskripsi setiap bagian halaman</div>
                </div>
            </div>
            <div class="cms-section-body">
                {{-- Program Unggulan --}}
                <p style="font-size:0.75rem;font-weight:700;text-transform:uppercase;letter-spacing:0.06em;color:var(--text-light);margin-bottom:0.75rem;padding-bottom:0.5rem;border-bottom:1px solid var(--border);">Seksi Program Unggulan</p>
                <div class="input-group">
                    <label class="form-label" for="program_section_title">Judul</label>
                    <input type="text" id="program_section_title" name="program_section_title" class="form-control" value="{{ old('program_section_title', $settings->program_section_title) }}" maxlength="300" required>
                </div>
                <div class="input-group">
                    <label class="form-label" for="program_section_subtitle">Deskripsi</label>
                    <textarea id="program_section_subtitle" name="program_section_subtitle" class="form-control" rows="2" maxlength="1000" required>{{ old('program_section_subtitle', $settings->program_section_subtitle) }}</textarea>
                </div>

                {{-- Alumni --}}
                <p style="font-size:0.75rem;font-weight:700;text-transform:uppercase;letter-spacing:0.06em;color:var(--text-light);margin-top:1.25rem;margin-bottom:0.75rem;padding-bottom:0.5rem;border-bottom:1px solid var(--border);">Seksi Alumni Sukses</p>
                <div class="input-group">
                    <label class="form-label" for="alumni_section_title">Judul</label>
                    <input type="text" id="alumni_section_title" name="alumni_section_title" class="form-control" value="{{ old('alumni_section_title', $settings->alumni_section_title) }}" maxlength="300" required>
                </div>
                <div class="input-group">
                    <label class="form-label" for="alumni_section_subtitle">Deskripsi</label>
                    <textarea id="alumni_section_subtitle" name="alumni_section_subtitle" class="form-control" rows="2" maxlength="1000" required>{{ old('alumni_section_subtitle', $settings->alumni_section_subtitle) }}</textarea>
                </div>

                {{-- Testimoni --}}
                <p style="font-size:0.75rem;font-weight:700;text-transform:uppercase;letter-spacing:0.06em;color:var(--text-light);margin-top:1.25rem;margin-bottom:0.75rem;padding-bottom:0.5rem;border-bottom:1px solid var(--border);">Seksi Testimoni</p>
                <div class="input-group">
                    <label class="form-label" for="testimoni_section_title">Judul</label>
                    <input type="text" id="testimoni_section_title" name="testimoni_section_title" class="form-control" value="{{ old('testimoni_section_title', $settings->testimoni_section_title) }}" maxlength="300" required>
                </div>
                <div class="input-group">
                    <label class="form-label" for="testimoni_section_subtitle">Deskripsi</label>
                    <textarea id="testimoni_section_subtitle" name="testimoni_section_subtitle" class="form-control" rows="2" maxlength="1000" required>{{ old('testimoni_section_subtitle', $settings->testimoni_section_subtitle) }}</textarea>
                </div>

                {{-- FAQ --}}
                <p style="font-size:0.75rem;font-weight:700;text-transform:uppercase;letter-spacing:0.06em;color:var(--text-light);margin-top:1.25rem;margin-bottom:0.75rem;padding-bottom:0.5rem;border-bottom:1px solid var(--border);">Seksi FAQ</p>
                <div class="input-group">
                    <label class="form-label" for="faq_section_title">Judul</label>
                    <input type="text" id="faq_section_title" name="faq_section_title" class="form-control" value="{{ old('faq_section_title', $settings->faq_section_title) }}" maxlength="300" required>
                </div>
                <div class="input-group">
                    <label class="form-label" for="faq_section_subtitle">Deskripsi</label>
                    <textarea id="faq_section_subtitle" name="faq_section_subtitle" class="form-control" rows="2" maxlength="1000" required>{{ old('faq_section_subtitle', $settings->faq_section_subtitle) }}</textarea>
                </div>
            </div>
        </div>

        {{-- 5. Footer --}}
        <div class="cms-section" id="section-footer">
            <div class="cms-section-header">
                <div class="cms-section-icon teal">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                </div>
                <div>
                    <div class="cms-section-title">Deskripsi Footer</div>
                    <div class="cms-section-desc">Teks deskripsi singkat di bagian bawah halaman</div>
                </div>
            </div>
            <div class="cms-section-body">
                <div class="input-group">
                    <label class="form-label" for="footer_description">Deskripsi Footer</label>
                    <textarea id="footer_description" name="footer_description" class="form-control" rows="3" maxlength="1000" required>{{ old('footer_description', $settings->footer_description) }}</textarea>
                </div>
            </div>
        </div>

        {{-- 6. SEO / Meta — PALING BAWAH (fitur lanjutan) --}}
        <div class="cms-section" id="section-meta">
            <div class="cms-section-header">
                <div class="cms-section-icon green">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                </div>
                <div>
                    <div class="cms-section-title">SEO & Meta Tags <small style="font-weight:400;color:var(--text-muted);font-size:0.75rem;">(lanjutan)</small></div>
                    <div class="cms-section-desc">Judul dan deskripsi yang muncul di mesin pencari Google</div>
                </div>
            </div>
            <div class="cms-section-body">
                <div class="input-group">
                    <label class="form-label" for="meta_title">Meta Title</label>
                    <input type="text" id="meta_title" name="meta_title" class="form-control" value="{{ old('meta_title', $settings->meta_title) }}" maxlength="200" required oninput="countChars(this, 'meta_title_count', 60)">
                    <div class="char-counter" id="meta_title_count">{{ strlen($settings->meta_title) }}/60 karakter (disarankan)</div>
                </div>
                <div class="input-group">
                    <label class="form-label" for="meta_description">Meta Description</label>
                    <textarea id="meta_description" name="meta_description" class="form-control" rows="3" maxlength="500" required oninput="countChars(this, 'meta_desc_count', 160)">{{ old('meta_description', $settings->meta_description) }}</textarea>
                    <div class="char-counter" id="meta_desc_count">{{ strlen($settings->meta_description) }}/160 karakter (disarankan)</div>
                </div>

                {{-- Preview Google — tanpa URL --}}
                <div style="background: var(--surface2); border: 1px solid var(--border); border-radius: var(--radius); padding: 1rem; margin-top: 0.5rem;">
                    <p style="font-size:0.6875rem;font-weight:700;text-transform:uppercase;letter-spacing:0.06em;color:var(--text-light);margin-bottom:0.75rem;display:flex;align-items:center;gap:0.5rem;">
                        <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                        Preview tampilan di Google
                    </p>
                    <div id="seo-preview-title" style="font-size:1rem;color:#1a0dab;font-weight:500;line-height:1.3;margin-bottom:0.375rem;"></div>
                    <div id="seo-preview-desc" style="font-size:0.8125rem;color:#545454;line-height:1.5;"></div>
                </div>
            </div>
        </div>

    </div>{{-- end right column --}}
</div>{{-- end grid --}}

{{-- ── Sticky Save Bar ──────────────────────────────────────────────────── --}}
<div class="save-bar">
    <div class="save-bar-msg">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
        Perubahan hanya tersimpan setelah klik tombol Simpan.
    </div>
    <div style="display:flex;gap:0.75rem;align-items:center;">
        <a href="{{ route('landing') }}" target="_blank" class="btn btn-secondary btn-sm">
            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/><polyline points="15 3 21 3 21 9"/><line x1="10" y1="14" x2="21" y2="3"/></svg>
            Lihat Homepage
        </a>
        <button type="submit" class="btn btn-primary" id="saveBtn">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
            Simpan Pengaturan
        </button>
    </div>
</div>

</form>
@endsection

@push('scripts')
<script>
// ── Character counter ────────────────────────────────────────────────────────
function countChars(el, counterId, recommended) {
    const len = el.value.length;
    const counter = document.getElementById(counterId);
    counter.textContent = len + '/' + recommended + ' karakter (disarankan)';
    counter.className = 'char-counter';
    if (len > recommended * 1.1) {
        counter.classList.add('over');
    } else if (len > recommended * 0.9) {
        counter.classList.add('warn');
    }
}

// ── SEO Live Preview (hanya title + desc, tanpa URL) ─────────────────────────
function updateSeoPreview() {
    const title = document.getElementById('meta_title')?.value || '';
    const desc  = document.getElementById('meta_description')?.value || '';
    const titleEl = document.getElementById('seo-preview-title');
    const descEl  = document.getElementById('seo-preview-desc');
    if (titleEl) titleEl.textContent = title || '(Judul halaman)';
    if (descEl)  descEl.textContent  = desc  || '(Deskripsi halaman)';
}

document.getElementById('meta_title')?.addEventListener('input', updateSeoPreview);
document.getElementById('meta_description')?.addEventListener('input', updateSeoPreview);
updateSeoPreview();

// ── Sidebar Nav scroll tracking ──────────────────────────────────────────────
function cmsScrollTo(e, id) {
    e.preventDefault();
    const el = document.getElementById(id);
    if (el) el.scrollIntoView({ behavior: 'smooth', block: 'start' });
    document.querySelectorAll('.cms-nav-item').forEach(i => i.classList.remove('active'));
    e.currentTarget.classList.add('active');
}

// Active nav on scroll using IntersectionObserver
const cmsSections = ['section-identity', 'section-hero', 'section-sections', 'section-footer', 'section-meta'];
const cmsNavItems = document.querySelectorAll('.cms-nav-item');

const cmsObserver = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
        if (entry.isIntersecting) {
            const id = entry.target.id;
            cmsNavItems.forEach(nav => {
                const href = nav.getAttribute('href');
                nav.classList.toggle('active', href === '#' + id);
            });
        }
    });
}, { rootMargin: '-20% 0px -70% 0px' });

cmsSections.forEach(id => {
    const el = document.getElementById(id);
    if (el) cmsObserver.observe(el);
});

// ── Save button loading state ────────────────────────────────────────────────
document.getElementById('cmsForm')?.addEventListener('submit', function() {
    const btn = document.getElementById('saveBtn');
    btn.disabled = true;
    btn.innerHTML = `<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="animation:spin 1s linear infinite"><polyline points="23 4 23 10 17 10"/><path d="M20.49 15a9 9 0 1 1-2.12-9.36L23 10"/></svg> Menyimpan...`;
    btn.style.opacity = '0.8';
});

// ── Initial char count ───────────────────────────────────────────────────────
(function() {
    const mt = document.getElementById('meta_title');
    const md = document.getElementById('meta_description');
    if (mt) countChars(mt, 'meta_title_count', 60);
    if (md) countChars(md, 'meta_desc_count', 160);
})();
</script>
<style>
@keyframes spin { to { transform: rotate(360deg); } }
</style>
@endpush
