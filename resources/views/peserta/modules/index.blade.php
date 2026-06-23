@extends('peserta.layouts.app')
@section('title', 'Modul Pembelajaran')

@push('styles')
<style>
/* ─── Premium Modules Layout ─────────────────────────────────── */
.module-header {
    background: #ffffff;
    border: 1px solid var(--border);
    border-radius: var(--radius-lg);
    padding: 2rem;
    margin-bottom: 2rem;
    box-shadow: var(--shadow);
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 1.5rem;
}
.module-header-content {
    flex: 1;
}
.module-header-title {
    font-family: 'Outfit', sans-serif;
    font-size: 1.625rem;
    font-weight: 800;
    color: var(--text);
    letter-spacing: -0.02em;
    line-height: 1.2;
}
.module-header-desc {
    font-size: 0.875rem;
    color: var(--text-muted);
    margin-top: 0.5rem;
    line-height: 1.5;
}

.module-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
    gap: 1.5rem;
    margin-bottom: 2.5rem;
}
.module-card {
    background: var(--surface);
    border: 1px solid var(--border);
    border-radius: var(--radius-lg);
    padding: 1.75rem;
    display: flex;
    flex-direction: column;
    gap: 1.25rem;
    box-shadow: var(--shadow);
    transition: all var(--transition);
    position: relative;
    overflow: hidden;
}
.module-card:hover {
    transform: translateY(-5px);
    box-shadow: var(--shadow-lg);
    border-color: var(--primary-mid);
}
.module-card::before {
    content: '';
    position: absolute;
    top: 0; left: 0; right: 0;
    height: 4px;
    background: var(--primary);
    transition: all var(--transition);
}
.module-card:hover::before {
    height: 6px;
}

.category-breadcrumb {
    font-size: 0.72rem;
    color: var(--text-muted);
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.04em;
    display: flex;
    align-items: center;
    gap: 0.25rem;
    margin-top: 0.25rem;
}
.breadcrumb-separator {
    color: var(--text-light);
}

.module-meta-info {
    display: flex;
    align-items: center;
    gap: 1rem;
    font-size: 0.8rem;
    color: var(--text-muted);
    font-weight: 600;
    padding-top: 0.75rem;
    border-top: 1px solid var(--border);
}
.meta-icon-text {
    display: flex;
    align-items: center;
    gap: 0.35rem;
}

/* ─── Progress Bar ───────────────────────────────────────────── */
.progress-container {
    margin-top: 0.25rem;
}
.progress-label-row {
    display: flex;
    justify-content: space-between;
    font-size: 0.75rem;
    color: var(--text-muted);
    margin-bottom: 0.35rem;
    font-weight: 700;
}
.progress-bar-bg {
    height: 6px;
    background: var(--surface2);
    border-radius: 99px;
    overflow: hidden;
}
.progress-bar-fill {
    height: 100%;
    background: var(--primary);
    border-radius: 99px;
    transition: width 0.5s ease-in-out;
}

/* Pagination Customization */
.pagination-wrapper {
    margin-top: 2rem;
}

@media (max-width: 768px) {
    .module-header { padding: 1.5rem; flex-direction: column; align-items: flex-start; }
    .module-grid { grid-template-columns: 1fr; }
}
</style>
@endpush

@section('content')
<div class="module-header">
    <div class="module-header-content">
        <h2 class="module-header-title">Modul Pembelajaran</h2>
        <p class="module-header-desc">Pelajari materi komprehensif, ringkasan rumus, serta tonton video pembahasan eksklusif dari tutor berpengalaman Bimbel Plano.</p>
    </div>
    <div style="background: var(--primary-soft); color: var(--primary); padding: 1rem; border-radius: var(--radius); display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
        <svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
            <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path>
            <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"></path>
        </svg>
    </div>
</div>

@if($modules->isEmpty())
    <div style="background:var(--surface); border:1px dashed var(--border); border-radius:var(--radius-lg); padding:4rem 2rem; text-align:center; box-shadow: var(--shadow);">
        <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="var(--text-light)" stroke-width="1.2" style="margin:0 auto 1rem; display:block;">
            <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path>
            <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"></path>
        </svg>
        <h4 style="font-weight:700; color:var(--text); font-size:1rem; margin-bottom:0.25rem;">Belum Ada Modul</h4>
        <p style="color:var(--text-muted); font-size:0.875rem;">Materi pembelajaran belum ditambahkan untuk program bimbingan Anda saat ini.</p>
    </div>
@else
    <div class="module-grid">
        @foreach($modules as $m)
        @php
            // Calculate dynamic reading time based on description length
            $wordCount = str_word_count(strip_tags($m->description ?? ''));
            $readTime = max(10, ceil(($wordCount + 50) / 15) * 5); // Dynamic 10-30 min indicator
        @endphp
        <div class="module-card">
            <div style="display:flex; flex-direction:column; gap:0.5rem;">
                <div style="display:flex; justify-content:space-between; align-items:center;">
                    <span class="badge" style="background:#EFF6FF; color:var(--primary); border-color:#BFDBFE; font-weight:700;">
                        {{ $m->questionCode->code ?? 'UMUM' }}
                    </span>
                    <span style="font-size:0.72rem; color:var(--success); font-weight:700; background:var(--success-soft); padding:0.15rem 0.5rem; border-radius:99px; border:1px solid #A7F3D0;">
                        Aktif
                    </span>
                </div>
                
                <div class="category-breadcrumb">
                    <span>{{ $m->category->name ?? 'Materi' }}</span>
                    @if(isset($m->subCategory))
                        <span class="breadcrumb-separator">&rarr;</span>
                        <span>{{ $m->subCategory->name }}</span>
                    @endif
                </div>
                
                <h4 style="font-size:1rem; font-weight:700; color:var(--text); line-height:1.4; margin-top:0.25rem;">
                    {{ $m->name }}
                </h4>
                
                <p style="font-size:0.82rem; color:var(--text-muted); line-height:1.5;">
                    {{ Str::limit($m->description, 130) }}
                </p>
            </div>

            {{-- Course Progress Tracker --}}
            <div class="progress-container">
                <div class="progress-label-row">
                    <span>Status Pelajaran</span>
                    <span>Ready</span>
                </div>
                <div class="progress-bar-bg">
                    <div class="progress-bar-fill" style="width: 100%; background: var(--primary-mid);"></div>
                </div>
            </div>
            
            <div class="module-meta-info" style="margin-top:auto;">
                <div class="meta-icon-text">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/></svg>
                    <span>{{ $readTime }} Menit Baca</span>
                </div>
                <div class="meta-icon-text" style="margin-left:auto;">
                    <a href="{{ route('peserta.modules.show', $m) }}" class="btn btn-primary btn-sm" style="font-weight:700; padding:0.45rem 1rem; border-radius:var(--radius-sm); font-size:0.8rem; box-shadow:none;">
                        Buka Modul
                    </a>
                </div>
            </div>
        </div>
        @endforeach
    </div>
    
    <div class="pagination-wrapper">
        {{ $modules->links() }}
    </div>
@endif
@endsection

