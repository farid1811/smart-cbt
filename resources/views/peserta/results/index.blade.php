@extends('peserta.layouts.app')
@section('title', 'Riwayat & Evaluasi')

@push('styles')
<style>
/* ─── Riwayat Header ─────────────────────────────────────────── */
.results-header {
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
.results-header-content {
    flex: 1;
}
.results-header-title {
    font-family: 'Outfit', sans-serif;
    font-size: 1.625rem;
    font-weight: 800;
    color: var(--text);
    letter-spacing: -0.02em;
    line-height: 1.2;
}
.results-header-desc {
    font-size: 0.875rem;
    color: var(--text-muted);
    margin-top: 0.5rem;
    line-height: 1.5;
}

/* ─── Modern Result Cards ────────────────────────────────────── */
.results-list {
    display: flex;
    flex-direction: column;
    gap: 1.5rem;
    margin-bottom: 2.5rem;
}
.result-card {
    background: var(--surface);
    border: 1px solid var(--border);
    border-radius: var(--radius-lg);
    padding: 1.75rem;
    display: flex;
    align-items: center;
    gap: 1.75rem;
    flex-wrap: wrap;
    box-shadow: var(--shadow);
    transition: all var(--transition);
    position: relative;
    overflow: hidden;
}
.result-card:hover {
    transform: translateY(-4px);
    box-shadow: var(--shadow-lg);
    border-color: var(--primary-mid);
}

/* ─── Circular Score Gauge ───────────────────────────────────── */
.circular-gauge {
    width: 80px;
    height: 80px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    position: relative;
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.04);
    transition: all var(--transition);
}
.result-card:hover .circular-gauge {
    transform: scale(1.05);
}
.circular-gauge-inner {
    width: 64px;
    height: 64px;
    background: var(--surface);
    border-radius: 50%;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    font-weight: 800;
    color: var(--text);
    box-shadow: inset 0 2px 4px rgba(0, 0, 0, 0.02);
}
.gauge-value {
    font-family: 'Outfit', sans-serif;
    font-size: 1.15rem;
    line-height: 1;
}
.gauge-label {
    font-size: 0.55rem;
    color: var(--text-light);
    text-transform: uppercase;
    font-weight: 800;
    margin-top: 2px;
    letter-spacing: 0.02em;
}

/* ─── Dynamic Subtest Breakdown ──────────────────────────────── */
.score-breakdown-row {
    display: flex;
    gap: 0.5rem;
    flex-wrap: wrap;
    margin-top: 0.75rem;
}
.breakdown-pill {
    font-size: 0.75rem;
    font-weight: 700;
    padding: 0.25rem 0.65rem;
    border-radius: 99px;
    border: 1px solid rgba(0, 0, 0, 0.05);
    display: inline-flex;
    align-items: center;
    gap: 0.25rem;
}

/* ─── Stats Badges ───────────────────────────────────────────── */
.analytical-badges {
    display: flex;
    gap: 0.5rem;
    flex-wrap: wrap;
    margin-top: 0.5rem;
}
.anal-badge {
    font-size: 0.72rem;
    font-weight: 700;
    padding: 0.2rem 0.5rem;
    border-radius: var(--radius-sm);
    background: var(--surface2);
    border: 1px solid var(--border);
    color: var(--text-muted);
}
.anal-badge-primary {
    background: var(--primary-soft);
    border-color: var(--primary-mid);
    color: var(--primary);
}
.anal-badge-success {
    background: var(--success-soft);
    border-color: #A7F3D0;
    color: var(--success);
}
.anal-badge-warning {
    background: #FFFBEB;
    border-color: #FDE68A;
    color: #D97706;
}

.pagination-wrapper {
    margin-top: 2rem;
}

@media (max-width: 768px) {
    .results-header { padding: 1.5rem; flex-direction: column; align-items: flex-start; }
    .result-card { padding: 1.5rem; flex-direction: column; align-items: flex-start; gap: 1.25rem; }
    .result-card-right { width: 100%; text-align: left !important; display: flex; flex-direction: column; gap: 1rem; }
    .result-card-right .btn { width: 100%; justify-content: center; }
}
</style>
@endpush

@section('content')
<div class="results-header">
    <div class="results-header-content">
        <h2 class="results-header-title">Riwayat & Evaluasi Ujian</h2>
        <p class="results-header-desc">Tinjau seluruh jejak skor Anda, analisis rincian nilai per subtest, periksa pembahasan interaktif, dan lacak peringkat Anda secara real-time.</p>
    </div>
    <div style="background: var(--primary-soft); color: var(--primary); padding: 1rem; border-radius: var(--radius); display: flex; align-items: center; justify-content: center; flex-shrink: 0; border: 1px solid var(--primary-mid);">
        <svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
            <line x1="18" y1="20" x2="18" y2="10"></line>
            <line x1="12" y1="20" x2="12" y2="4"></line>
            <line x1="6" y1="20" x2="6" y2="14"></line>
            <line x1="2" y1="20" x2="22" y2="20"></line>
        </svg>
    </div>
</div>

@if($results->isEmpty())
    <div style="background:var(--surface); border:1px dashed var(--border); border-radius:var(--radius-lg); padding:4rem 2rem; text-align:center; box-shadow: var(--shadow);">
        <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="var(--text-light)" stroke-width="1.2" style="margin:0 auto 1rem; display:block;">
            <circle cx="12" cy="12" r="10"></circle>
            <polyline points="12 6 12 12 16 14"></polyline>
        </svg>
        <h4 style="font-weight:700; color:var(--text); font-size:1rem; margin-bottom:0.25rem;">Belum Ada Riwayat</h4>
        <p style="color:var(--text-muted); font-size:0.875rem;">Anda belum pernah melaksanakan latihan drill atau tryout akbar sebelumnya.</p>
        <a href="{{ route('peserta.dashboard') }}" class="btn btn-primary btn-sm" style="margin-top:1.25rem; font-weight:700; box-shadow:none;">Mulai Ujian Pertama</a>
    </div>
@else
    <div class="results-list">
        @foreach($results as $r)
        @php
            // Calculate chronological attempt number for this package
            $allAttempts = \App\Models\Result::where('user_id', $r->user_id)
                ->where('tryout_package_id', $r->tryout_package_id)
                ->orderBy('created_at', 'asc')
                ->pluck('id')
                ->toArray();
            $attemptNumber = array_search($r->id, $allAttempts) !== false ? array_search($r->id, $allAttempts) + 1 : 1;

            // Calculate dense rank of this attempt among all users
            $rank = \App\Models\Result::where('tryout_package_id', $r->tryout_package_id)
                ->where('skor_total', '>', $r->skor_total)
                ->count() + 1;
            $totalParticipants = \App\Models\Result::where('tryout_package_id', $r->tryout_package_id)
                ->distinct('user_id')
                ->count('user_id');

            // Check test group format
            $isSkd = ($r->tryoutPackage->group === 'SKD');

            // Calculate max score and percentage
            if ($isSkd) {
                $maxScore = 550;
            } else {
                $totalQ = 0;
                if ($r->category_scores) {
                    $scoreArray = is_string($r->category_scores) ? json_decode($r->category_scores, true) : $r->category_scores;
                    foreach ($scoreArray as $data) {
                        $totalQ += $data['total'] ?? 0;
                    }
                }
                if ($totalQ <= 0) {
                    $totalQ = $r->examSession->answers->count();
                }
                $maxScore = $totalQ * 5;
            }
            if ($maxScore <= 0) $maxScore = 100;
            $pct = min(100, ($r->skor_total / $maxScore) * 100);

            // Select color based on percentage
            $scoreColor = $pct >= 70 ? 'var(--success)' : ($pct >= 50 ? '#D97706' : 'var(--error)');
        @endphp
        <div class="result-card">
            {{-- Circular Gauge --}}
            <div class="circular-gauge" style="background: conic-gradient({{ $scoreColor }} {{ $pct * 3.6 }}deg, var(--surface2) 0deg);">
                <div class="circular-gauge-inner">
                    <span class="gauge-value" style="color: {{ $scoreColor }};">{{ $r->skor_total }}</span>
                    <span class="gauge-label">Skor</span>
                </div>
            </div>

            {{-- Central Info --}}
            <div style="flex:1; min-width:260px;">
                <div style="display:flex; align-items:center; gap:0.5rem; flex-wrap:wrap;">
                    <span class="badge" style="background:{{ $r->tryoutPackage->jenis_ujian === 'drill' ? '#EFF6FF' : '#FFFBEB' }}; color:{{ $r->tryoutPackage->jenis_ujian === 'drill' ? 'var(--primary)' : '#D97706' }}; border-color:{{ $r->tryoutPackage->jenis_ujian === 'drill' ? '#BFDBFE' : '#FDE68A' }}; font-size:0.65rem; font-weight:700; text-transform:uppercase;">
                        {{ $r->tryoutPackage->jenis_ujian === 'drill' ? 'Drill' : 'Tryout' }}
                    </span>
                    <span style="font-size:0.8rem; color:var(--text-muted); font-weight:600;">
                        {{ $r->created_at->format('d M Y, H:i') }}
                    </span>
                </div>

                <h3 style="font-size:1.1rem; font-weight:800; color:var(--text); margin-top:0.35rem; line-height:1.3;">
                    {{ $r->tryoutPackage->nama }}
                </h3>

                {{-- Analytical Indicators --}}
                <div class="analytical-badges">
                    <span class="anal-badge anal-badge-primary">
                        Percobaan ke-{{ $attemptNumber }}
                    </span>
                    <span class="anal-badge anal-badge-warning">
                        Peringkat #{{ $rank }} dari {{ $totalParticipants }}
                    </span>
                    <span class="anal-badge {{ $r->examSession->status === 'selesai' ? 'anal-badge-success' : 'anal-badge-warning' }}">
                        {{ $r->examSession->status === 'selesai' ? 'Selesai' : 'Timeout' }}
                    </span>
                </div>

                {{-- Dynamic Subtest breakdown --}}
                @if($r->category_scores && count($r->category_scores) > 0)
                    @php $scoreArray = is_string($r->category_scores) ? json_decode($r->category_scores, true) : $r->category_scores; @endphp
                    <div class="score-breakdown-row">
                        @foreach($scoreArray as $codeId => $data)
                            @php
                                $badgeClr = 'var(--primary)';
                                $badgeBg = '#EFF6FF';
                                if ($data['kode'] === 'TWK') { $badgeClr = 'var(--primary)'; $badgeBg = '#EFF6FF'; }
                                elseif ($data['kode'] === 'TIU') { $badgeClr = '#6D28D9'; $badgeBg = '#F5F3FF'; }
                                elseif ($data['kode'] === 'TKP') { $badgeClr = 'var(--success)'; $badgeBg = '#ECFDF5'; }
                            @endphp
                            <span class="breakdown-pill" style="background:{{ $badgeBg }}; color:{{ $badgeClr }}; border-color:transparent;">
                                <strong>{{ $data['kode'] }}:</strong> {{ $data['score'] }}
                            </span>
                        @endforeach
                    </div>
                @else
                    {{-- Fallback --}}
                    <div class="score-breakdown-row">
                        <span class="breakdown-pill" style="background:#EFF6FF; color:var(--primary); border-color:transparent;"><strong>TWK:</strong> {{ $r->skor_twk }}</span>
                        <span class="breakdown-pill" style="background:#F5F3FF; color:#6D28D9; border-color:transparent;"><strong>TIU:</strong> {{ $r->skor_tiu }}</span>
                        <span class="breakdown-pill" style="background:#ECFDF5; color:var(--success); border-color:transparent;"><strong>TKP:</strong> {{ $r->skor_tkp }}</span>
                    </div>
                @endif
            </div>

            {{-- Action & Counters --}}
            <div class="result-card-right" style="text-align:right; min-width:200px;">
                <div style="font-size:0.82rem; color:var(--text-muted); margin-bottom:0.75rem; font-weight:600;">
                    <span style="color:var(--success);">{{ $r->jumlah_benar }} Benar</span> &bull; 
                    <span style="color:var(--error);">{{ $r->jumlah_salah }} Salah</span> &bull; 
                    <span>{{ $r->jumlah_kosong }} Kosong</span>
                </div>
                
                <a href="{{ route('peserta.exam.result', $r) }}" class="btn btn-secondary btn-sm" style="font-weight:700; padding:0.5rem 1.15rem; font-size:0.8rem; display:inline-flex; align-items:center; gap:0.25rem;">
                    Analisis Detail
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="9 18 15 12 9 6"></polyline></svg>
                </a>
            </div>
        </div>
        @endforeach
    </div>
    
    <div class="pagination-wrapper">
        {{ $results->links() }}
    </div>
@endif
@endsection

