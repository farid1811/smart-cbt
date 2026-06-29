@extends('peserta.layouts.app')
@section('title', 'Tryout Akbar')

@push('styles')
<style>
/* ─── Premium Tryout Layout ──────────────────────────────────── */
.tryout-header {
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
.tryout-header-content {
    flex: 1;
}
.tryout-header-title {
    font-family: 'Outfit', sans-serif;
    font-size: 1.625rem;
    font-weight: 800;
    color: var(--text);
    letter-spacing: -0.02em;
    line-height: 1.2;
}
.tryout-header-desc {
    font-size: 0.875rem;
    color: var(--text-muted);
    margin-top: 0.5rem;
    line-height: 1.5;
}

.tryout-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
    gap: 1.5rem;
    margin-bottom: 2.5rem;
}
.tryout-card {
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
.tryout-card:hover {
    transform: translateY(-5px);
    box-shadow: var(--shadow-lg);
    border-color: var(--primary-mid);
}
.tryout-card::before {
    content: '';
    position: absolute;
    top: 0; left: 0; right: 0;
    height: 4px;
    background: #F59E0B;
    transition: all var(--transition);
}
.tryout-card:hover::before {
    height: 6px;
}

.tryout-meta {
    display: flex;
    align-items: center;
    gap: 1rem;
    padding: 0.75rem 0;
    border-top: 1px solid var(--border);
    border-bottom: 1px solid var(--border);
}
.meta-item {
    display: flex;
    align-items: center;
    gap: 0.35rem;
    font-size: 0.8125rem;
    color: var(--text-muted);
    font-weight: 600;
}

/* ─── Security Mode Badges ───────────────────────────────────── */
.security-badge {
    display: inline-flex;
    align-items: center;
    gap: 0.35rem;
    font-size: 0.75rem;
    font-weight: 700;
    padding: 0.25rem 0.65rem;
    border-radius: 99px;
    border: 1px solid transparent;
    text-transform: uppercase;
}
.security-seb {
    background: var(--error-soft);
    color: var(--error);
    border-color: #FECACA;
}
.security-normal {
    background: var(--primary-soft);
    color: var(--primary);
    border-color: var(--primary-mid);
}

/* ─── Attempt Limits Tracker ─────────────────────────────────── */
.attempt-tracker {
    background: var(--surface2);
    border: 1px solid var(--border);
    border-radius: var(--radius);
    padding: 0.85rem 1rem;
}
.attempt-tracker-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    font-size: 0.78rem;
    color: var(--text-muted);
    margin-bottom: 0.5rem;
    font-weight: 700;
}
.attempt-bar-bg {
    height: 6px;
    background: var(--border);
    border-radius: 99px;
    overflow: hidden;
}
.attempt-bar-fill {
    height: 100%;
    background: #F59E0B;
    border-radius: 99px;
    transition: width 0.5s ease-in-out;
}

.score-badge-row {
    display: flex;
    align-items: center;
    justify-content: space-between;
    font-size: 0.8rem;
    font-weight: 700;
}
.best-score-capsule {
    background: var(--success-soft);
    color: var(--success);
    border: 1px solid #A7F3D0;
    padding: 0.25rem 0.65rem;
    border-radius: 99px;
    display: inline-flex;
    align-items: center;
    gap: 0.25rem;
}

.pagination-wrapper {
    margin-top: 2rem;
}

@media (max-width: 768px) {
    .tryout-header { padding: 1.5rem; flex-direction: column; align-items: flex-start; }
    .tryout-grid { grid-template-columns: 1fr; }
}
</style>
@endpush

@section('content')
<div class="tryout-header">
    <div class="tryout-header-content">
        <h2 class="tryout-header-title">Simulasi Tryout Akbar</h2>
        <p class="tryout-header-desc">Uji ketahanan mental dan kompetensi Anda dengan simulasi ujian nasional resmi. Dilengkapi sistem pengamanan Safe Exam Browser (SEB) terstandarisasi.</p>
    </div>
    <div style="background: #FFFBEB; color: #D97706; padding: 1rem; border-radius: var(--radius); display: flex; align-items: center; justify-content: center; flex-shrink: 0; border: 1px solid #FDE68A;">
        <svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
            <rect x="2" y="7" width="20" height="14" rx="2"></rect>
            <path d="M16 7V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v2"></path>
        </svg>
    </div>
</div>

@if($tryouts->isEmpty())
    <div style="background:var(--surface); border:1px dashed var(--border); border-radius:var(--radius-lg); padding:4rem 2rem; text-align:center; box-shadow: var(--shadow);">
        <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="var(--text-light)" stroke-width="1.2" style="margin:0 auto 1rem; display:block;">
            <rect x="2" y="7" width="20" height="14" rx="2"></rect>
            <path d="M16 7V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v2"></path>
        </svg>
        <h4 style="font-weight:700; color:var(--text); font-size:1rem; margin-bottom:0.25rem;">Belum Ada Paket Tryout</h4>
        <p style="color:var(--text-muted); font-size:0.875rem;">Paket simulasi tryout akbar belum tersedia untuk program bimbingan Anda saat ini.</p>
    </div>
@else
    <div class="tryout-grid">
        @foreach($tryouts as $tryout)
        @php
            $attempts = $tryout->packageAttempts;
            $attemptsCount = $attempts->count();
            $limit = $tryout->attempt_limit;
            $remaining = max(0, $limit - $attemptsCount);
            
            // Calculate percentage of attempts used
            $attemptPercent = $limit > 0 ? min(100, ($attemptsCount / $limit) * 100) : 0;
            $bestScore = $attempts->isNotEmpty() ? $attempts->max('score') : null;
        @endphp
        <div class="tryout-card">
            <div style="flex:1; min-width:0; display:flex; flex-direction:column; gap:0.5rem;">
                <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:0.5rem;">
                    <div style="display:flex; gap:0.35rem; align-items:center;">
                        @if($tryout->exam_mode === 'seb')
                            <span class="security-badge security-seb">
                                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect><path d="M7 11V7a5 5 0 0 1 10 0v4"></path></svg>
                                SEB Mode
                            </span>
                        @else
                            <span class="security-badge security-normal">
                                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path></svg>
                                Normal Mode
                            </span>
                        @endif
                        @if(!empty($tryout->token))
                            <span class="badge" style="background:#FEF2F2; color:#EF4444; border-color:#FEE2E2; font-size:0.65rem; font-weight:700; padding:0.15rem 0.45rem; border-radius:4px; display:inline-flex; align-items:center; gap:0.25rem; border: 1px solid #FEE2E2; line-height: 1;">
                                <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                                Butuh Token
                            </span>
                        @endif
                    </div>
                    
                    <span style="font-size:0.72rem; color:var(--text-muted); font-weight:600;">
                        Limit: {{ $limit }}x
                    </span>
                </div>
                
                <h4 style="font-size:1.05rem; font-weight:800; color:var(--text); line-height:1.3; margin-top:0.25rem;">
                    {{ $tryout->nama }}
                </h4>
                
                @if($tryout->deskripsi)
                    <p style="font-size:0.82rem; color:var(--text-muted); line-height:1.5;">
                        {{ Str::limit($tryout->deskripsi, 110) }}
                    </p>
                @endif
            </div>

            <div class="tryout-meta">
                <div class="meta-item">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/></svg>
                    <span>{{ $tryout->durasi_menit }} Menit</span>
                </div>
                <div class="meta-item">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                    <span>{{ $tryout->questions_count }} Soal</span>
                </div>
            </div>

            {{-- Attempt Limits Tracker --}}
            <div class="attempt-tracker">
                <div class="attempt-tracker-row">
                    <span>Limit Percobaan</span>
                    <span>{{ $attemptsCount }} / {{ $limit }}</span>
                </div>
                <div class="attempt-bar-bg">
                    <div class="attempt-bar-fill" style="width: {{ $attemptPercent }}%; background: {{ $attemptPercent >= 100 ? 'var(--error)' : '#F59E0B' }};"></div>
                </div>
            </div>

            <div class="score-badge-row">
                <span style="color:var(--text-muted); font-size:0.78rem;">Nilai Pencapaian:</span>
                @if($bestScore !== null)
                    <span class="best-score-capsule">
                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg>
                        Terbaik: {{ $bestScore }}
                    </span>
                @else
                    <span style="color:var(--text-light); font-size:0.8rem; font-style:italic;">Belum Ada</span>
                @endif
            </div>

            @if($remaining > 0)
                @if($tryout->exam_mode === 'seb')
                    <div style="display:flex; gap:0.5rem; margin-top:0.25rem;">
                        <a href="{{ route('peserta.exam.sebConfig', $tryout) }}" class="btn btn-secondary btn-sm" style="flex:1.1; justify-content:center; color:var(--error); border-color:#FECACA; font-weight:700; font-size:0.8rem;" title="Download file konfigurasi Safe Exam Browser">
                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline><line x1="12" y1="15" x2="12" y2="3"></line></svg>
                            Unduh SEB
                        </a>
                        <a href="{{ route('peserta.exam.start', $tryout) }}" class="btn btn-primary btn-sm" style="flex:1; justify-content:center; font-weight:700; font-size:0.8rem; box-shadow:none;">
                            Mulai Ujian
                        </a>
                    </div>
                @else
                    <a href="{{ route('peserta.exam.start', $tryout) }}" class="btn btn-primary" style="justify-content:center; width:100%; font-size:0.85rem; font-weight:700; margin-top:0.25rem; box-shadow:none;">
                        Mulai Ujian
                    </a>
                @endif
            @else
                <button class="btn btn-secondary" style="justify-content:center; width:100%; font-size:0.85rem; cursor:not-allowed; opacity:0.6; font-weight:700; margin-top:0.25rem;" disabled>
                    Batas Percobaan Habis
                </button>
            @endif
        </div>
        @endforeach
    </div>
    
    <div class="pagination-wrapper">
        {{ $tryouts->links() }}
    </div>
@endif
@endsection

