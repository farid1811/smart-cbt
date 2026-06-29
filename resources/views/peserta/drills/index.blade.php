@extends('peserta.layouts.app')
@section('title', 'Drill Soal')

@push('styles')
<style>
/* ─── Premium Drills Layout ──────────────────────────────────── */
.drill-header {
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
.drill-header-content {
    flex: 1;
}
.drill-header-title {
    font-family: 'Outfit', sans-serif;
    font-size: 1.625rem;
    font-weight: 800;
    color: var(--text);
    letter-spacing: -0.02em;
    line-height: 1.2;
}
.drill-header-desc {
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
    background: var(--success);
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

/* ─── Attempt Progress Bar ───────────────────────────────────── */
.attempt-progress-container {
    background: var(--surface2);
    border: 1px solid var(--border);
    border-radius: var(--radius);
    padding: 0.85rem 1rem;
}
.attempt-progress-row {
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
    background: var(--success);
    border-radius: 99px;
    transition: width 0.5s ease-in-out;
}

/* ─── Score Highlights ───────────────────────────────────────── */
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
    .drill-header { padding: 1.5rem; flex-direction: column; align-items: flex-start; }
    .tryout-grid { grid-template-columns: 1fr; }
}
</style>
@endpush

@section('content')
<div class="drill-header">
    <div class="drill-header-content">
        <h2 class="drill-header-title">Latihan Drill Soal</h2>
        <p class="drill-header-desc">Uji pemahaman Anda per topik secara mendalam dan fleksibel tanpa pengawasan Safe Exam Browser. Sempurna untuk evaluasi mandiri harian.</p>
    </div>
    <div style="background: var(--success-soft); color: var(--success); padding: 1rem; border-radius: var(--radius); display: flex; align-items: center; justify-content: center; flex-shrink: 0; border: 1px solid #A7F3D0;">
        <svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
            <path d="M12 20h9"></path>
            <path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"></path>
        </svg>
    </div>
</div>

@if($drills->isEmpty())
    <div style="background:var(--surface); border:1px dashed var(--border); border-radius:var(--radius-lg); padding:4rem 2rem; text-align:center; box-shadow: var(--shadow);">
        <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="var(--text-light)" stroke-width="1.2" style="margin:0 auto 1rem; display:block;">
            <rect x="2" y="7" width="20" height="14" rx="2"></rect>
            <path d="M16 7V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v2"></path>
        </svg>
        <h4 style="font-weight:700; color:var(--text); font-size:1rem; margin-bottom:0.25rem;">Belum Ada Paket Drill</h4>
        <p style="color:var(--text-muted); font-size:0.875rem;">Paket latihan soal belum tersedia untuk program bimbingan Anda saat ini.</p>
    </div>
@else
    <div class="tryout-grid">
        @foreach($drills as $tryout)
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
                <div style="display:flex; justify-content:space-between; align-items:center;">
                    <div style="display:flex; gap:0.35rem; align-items:center;">
                        <span class="badge badge-TWK" style="background:#ECFDF5; color:var(--success); border-color:#A7F3D0; font-weight:700; text-transform:uppercase;">
                            {{ $tryout->jenis_ujian }}
                        </span>
                        @if(!empty($tryout->token))
                            <span class="badge" style="background:#FEF2F2; color:#EF4444; border-color:#FEE2E2; font-size:0.65rem; font-weight:700; padding:0.15rem 0.45rem; border-radius:4px; display:inline-flex; align-items:center; gap:0.25rem; border: 1px solid #FEE2E2;">
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
                @if($tryout->categoryRelation)
                    <div style="font-size:0.75rem; color:var(--text-muted); margin-top:0.25rem; font-weight:600;">
                        {{ $tryout->categoryRelation->name }}
                    </div>
                @endif
                
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

            {{-- Attempt Progress bar --}}
            <div class="attempt-progress-container">
                <div class="attempt-progress-row">
                    <span>Limit Percobaan</span>
                    <span>{{ $attemptsCount }} / {{ $limit }}</span>
                </div>
                <div class="attempt-bar-bg">
                    <div class="attempt-bar-fill" style="width: {{ $attemptPercent }}%; background: {{ $attemptPercent >= 100 ? 'var(--error)' : 'var(--success)' }};"></div>
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
                <a href="{{ route('peserta.exam.start', $tryout) }}" class="btn btn-primary" style="justify-content:center; width:100%; font-size:0.85rem; font-weight:700; margin-top:0.25rem; box-shadow:none;">
                    {{ $attemptsCount > 0 ? 'Lanjutkan Latihan' : 'Mulai Latihan' }}
                </a>
            @else
                <button class="btn btn-secondary" style="justify-content:center; width:100%; font-size:0.85rem; cursor:not-allowed; opacity:0.6; font-weight:700; margin-top:0.25rem;" disabled>
                    Batas Percobaan Habis
                </button>
            @endif
        </div>
        @endforeach
    </div>
    
    <div class="pagination-wrapper">
        {{ $drills->links() }}
    </div>
@endif
@endsection

