@extends('peserta.layouts.app')
@section('title', 'Dashboard')

@push('styles')
<style>
/* ─── Skeleton Loading Effect ────────────────────────────────── */
.skeleton {
    background: linear-gradient(90deg, #f1f5f9 25%, #e2e8f0 50%, #f1f5f9 75%);
    background-size: 200% 100%;
    animation: skeleton-loading 1.5s infinite;
}
@keyframes skeleton-loading {
    0% { background-position: 200% 0; }
    100% { background-position: -200% 0; }
}

/* ─── Welcome Banner ─────────────────────────────────────────── */
.welcome-banner {
    background: linear-gradient(135deg, var(--primary) 0%, #2563eb 100%);
    border-radius: var(--radius-lg);
    padding: 2.25rem 2rem;
    color: #ffffff;
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 1.5rem;
    margin-bottom: 2rem;
    box-shadow: var(--shadow-lg);
    position: relative;
    overflow: hidden;
    transition: all var(--transition);
}
.welcome-banner::before {
    content: '';
    position: absolute;
    top: -50%;
    right: -20%;
    width: 350px;
    height: 350px;
    border-radius: 50%;
    background: rgba(255, 255, 255, 0.05);
    pointer-events: none;
}
.welcome-banner:hover {
    transform: translateY(-2px);
    box-shadow: 0 20px 30px -10px rgba(30, 64, 175, 0.25);
}
.welcome-badge {
    background: rgba(255, 255, 255, 0.18);
    backdrop-filter: blur(4px);
    color: #ffffff;
    font-size: 0.75rem;
    font-weight: 700;
    padding: 0.3rem 0.85rem;
    border-radius: 99px;
    display: inline-block;
    margin-bottom: 0.85rem;
    letter-spacing: 0.05em;
    text-transform: uppercase;
}
.welcome-title {
    font-family: 'Outfit', sans-serif;
    font-size: 1.75rem;
    font-weight: 800;
    line-height: 1.2;
    margin-bottom: 0.5rem;
    letter-spacing: -0.01em;
}
.welcome-subtitle {
    font-size: 0.9rem;
    color: rgba(255, 255, 255, 0.9);
}
.welcome-illustration {
    color: rgba(255, 255, 255, 0.85);
    flex-shrink: 0;
    animation: float 4s ease-in-out infinite;
}
@keyframes float {
    0%, 100% { transform: translateY(0); }
    50% { transform: translateY(-8px); }
}

/* ─── Summary Stats Cards ────────────────────────────────────── */
.stats-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 1.25rem;
    margin-bottom: 2rem;
}
.stat-card {
    background: var(--surface);
    border: 1px solid var(--border);
    border-radius: var(--radius-lg);
    padding: 1.25rem 1.5rem;
    display: flex;
    align-items: center;
    gap: 1rem;
    box-shadow: var(--shadow);
    transition: all var(--transition);
}
.stat-card:hover {
    transform: translateY(-4px);
    box-shadow: var(--shadow-md);
    border-color: var(--primary-mid);
}
.stat-icon {
    width: 48px;
    height: 48px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    transition: all var(--transition);
}
.stat-card:hover .stat-icon {
    transform: scale(1.08) rotate(3deg);
}
.stat-icon-blue { background: #EFF6FF; color: var(--primary); }
.stat-icon-green { background: #ECFDF5; color: var(--success); }
.stat-icon-amber { background: #FFFBEB; color: #D97706; }
.stat-icon-purple { background: #F5F3FF; color: #7C3AED; }

.stat-info {
    display: flex;
    flex-direction: column;
}
.stat-label {
    font-size: 0.78rem;
    color: var(--text-muted);
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.02em;
}
.stat-value {
    font-family: 'Outfit', sans-serif;
    font-size: 1.625rem;
    font-weight: 800;
    color: var(--text);
    line-height: 1.2;
    margin-top: 0.15rem;
}

/* ─── Interactive Alur Belajar ────────────────────────────────── */
.path-container {
    background: linear-gradient(135deg, #1E40AF 0%, #1D4ED8 100%);
    border-radius: var(--radius-lg);
    padding: 1.75rem;
    color: #ffffff;
    margin-bottom: 2.5rem;
    box-shadow: var(--shadow-md);
    position: relative;
}
.path-container::before {
    content: '';
    position: absolute;
    bottom: 0; right: 0;
    width: 150px; height: 150px;
    background: radial-gradient(circle, rgba(255,255,255,0.06) 0%, transparent 70%);
    pointer-events: none;
}
.path-steps {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 1.25rem;
    margin-top: 1.5rem;
}
.path-step {
    background: rgba(255, 255, 255, 0.07);
    border: 1px solid rgba(255, 255, 255, 0.1);
    border-radius: var(--radius);
    padding: 1.25rem 1rem;
    position: relative;
    transition: all var(--transition);
    display: flex;
    flex-direction: column;
    height: 100%;
}
.path-step:hover {
    background: rgba(255, 255, 255, 0.13);
    transform: translateY(-4px);
    box-shadow: 0 10px 20px -5px rgba(0, 0, 0, 0.15);
    border-color: rgba(255, 255, 255, 0.25);
}
.step-num {
    background: #FBBF24;
    color: #1E40AF;
    width: 28px;
    height: 28px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.85rem;
    font-weight: 800;
    margin-bottom: 0.75rem;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}
.step-title {
    font-family: 'Outfit', sans-serif;
    font-weight: 700;
    font-size: 0.95rem;
    margin-bottom: 0.35rem;
    color: #ffffff;
}
.step-desc {
    font-size: 0.78rem;
    color: rgba(255, 255, 255, 0.85);
    line-height: 1.4;
}

/* ─── Premium Cards Grid ─────────────────────────────────────── */
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
    background: var(--border);
    transition: all var(--transition);
}
.tryout-card.card-module::before { background: var(--primary); }
.tryout-card.card-drill::before { background: var(--success); }
.tryout-card.card-tryout::before { background: #F59E0B; }
.tryout-card:hover::before {
    height: 6px;
}

.tryout-meta {
    display: flex;
    align-items: center;
    gap: 1.25rem;
    padding: 0.75rem 0;
    border-top: 1px solid var(--border);
    border-bottom: 1px solid var(--border);
}
.meta-item {
    display: flex;
    align-items: center;
    gap: 0.375rem;
    font-size: 0.8125rem;
    color: var(--text-muted);
    font-weight: 600;
}

/* ─── Riwayat Table Modernization ────────────────────────────── */
.riwayat-table-card {
    background: var(--surface);
    border: 1px solid var(--border);
    border-radius: var(--radius-lg);
    overflow: hidden;
    box-shadow: var(--shadow);
    margin-bottom: 2rem;
}

@media (max-width: 992px) {
    .stats-grid { grid-template-columns: repeat(2, 1fr); }
}

@media (max-width: 820px) {
    .path-steps { grid-template-columns: repeat(2, 1fr); }
}

@media (max-width: 576px) {
    .welcome-banner { padding: 1.5rem 1.25rem; }
    .welcome-title { font-size: 1.35rem; }
    .welcome-illustration { display: none; }
    .stats-grid { grid-template-columns: 1fr; }
    .path-steps { grid-template-columns: 1fr; }
    .tryout-grid { grid-template-columns: 1fr; }
}
</style>
@endpush

@section('content')

@php
    // Calculate stats dynamically for the logged-in student
    $totalModules = \App\Models\LearningModule::where('group_id', $user->group_id)->where('is_active', true)->count();
    $totalDrills = \App\Models\TryoutPackage::where('group', $user->group?->name ?: 'SKD')->where('jenis_ujian', 'drill')->where('is_active', true)->count();
    $totalTryouts = \App\Models\TryoutPackage::where('group', $user->group?->name ?: 'SKD')->where('jenis_ujian', 'tryout')->where('is_active', true)->count();
    $highestScore = $user->results()->max('skor_total') ?: 0;
@endphp

{{-- Welcome Banner --}}
<div class="welcome-banner">
    <div class="welcome-content">
        <span class="welcome-badge">Program Belajar Bimbel Plano</span>
        <h2 class="welcome-title">Selamat Datang, {{ $user->name }} 👋</h2>
        <p class="welcome-subtitle">
            Kelompok Ujian: <strong style="text-decoration: underline;">{{ $user->group?->name }}</strong>
            @if($user->category)
                 &bull; Kategori: <strong>{{ $user->category }}</strong>
            @endif
        </p>
    </div>
    <div class="welcome-illustration">
        <svg width="72" height="72" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M22 10v6M2 10l10-5 10 5-10 5z"/>
            <path d="M6 12v5c0 2 2 3 6 3s6-1 6-3v-5"/>
        </svg>
    </div>
</div>

{{-- Summary Stats Cards --}}
<div class="stats-grid">
    {{-- Total Modul --}}
    <div class="stat-card">
        <div class="stat-icon stat-icon-blue">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path>
                <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"></path>
            </svg>
        </div>
        <div class="stat-info">
            <span class="stat-label">Total Modul</span>
            <span class="stat-value">{{ $totalModules }}</span>
        </div>
    </div>

    {{-- Total Drill Soal --}}
    <div class="stat-card">
        <div class="stat-icon stat-icon-green">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                <path d="M12 20h9"></path>
                <path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"></path>
            </svg>
        </div>
        <div class="stat-info">
            <span class="stat-label">Drill Soal</span>
            <span class="stat-value">{{ $totalDrills }}</span>
        </div>
    </div>

    {{-- Total Tryout --}}
    <div class="stat-card">
        <div class="stat-icon stat-icon-amber">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                <rect x="2" y="7" width="20" height="14" rx="2"></rect>
                <path d="M16 7V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v2"></path>
            </svg>
        </div>
        <div class="stat-info">
            <span class="stat-label">Tryout</span>
            <span class="stat-value">{{ $totalTryouts }}</span>
        </div>
    </div>

    {{-- Nilai Tertinggi --}}
    <div class="stat-card">
        <div class="stat-icon stat-icon-purple">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                <circle cx="12" cy="12" r="10"></circle>
                <line x1="12" y1="8" x2="12" y2="12"></line>
                <line x1="12" y1="16" x2="12.01" y2="16"></line>
            </svg>
        </div>
        <div class="stat-info">
            <span class="stat-label">Skor Tertinggi</span>
            <span class="stat-value">{{ $highestScore }}%</span>
        </div>
    </div>
</div>

{{-- Alur Sukses --}}
<div class="path-container">
    <h3 style="font-family:'Outfit', sans-serif; font-weight:800; font-size:1.15rem; margin:0; display:flex; align-items:center; gap:0.6rem;">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
            <polygon points="6 3 20 12 6 21 6 3"></polygon>
        </svg>
        Alur Sukses Bimbel Plano
    </h3>
    <p style="font-size:0.82rem; color:rgba(255,255,255,0.85); margin-top:0.35rem;">Ikuti langkah pembelajaran berjenjang ini untuk memaksimalkan peluang kelulusan Anda.</p>
    
    <div class="path-steps">
        <a href="{{ route('peserta.modules.index') }}" class="path-step" style="text-decoration:none;">
            <div class="step-num">1</div>
            <div class="step-title">Pelajari Modul</div>
            <div class="step-desc">Tinjau ringkasan konsep, pahami peta materi, dan unduh dokumen modul PDF.</div>
        </a>
        <a href="{{ route('peserta.drills.index') }}" class="path-step" style="text-decoration:none;">
            <div class="step-num">2</div>
            <div class="step-title">Latihan Drill Soal</div>
            <div class="step-desc">Kerjakan paket latihan per subtest secara dinamis tanpa batasan browser lockdown.</div>
        </a>
        <a href="{{ route('peserta.tryouts.index') }}" class="path-step" style="text-decoration:none;">
            <div class="step-num">3</div>
            <div class="step-title">Simulasi Tryout</div>
            <div class="step-desc">Uji kekuatan ujian sebenarnya menggunakan sistem Safe Exam Browser terintegrasi.</div>
        </a>
        <a href="{{ route('peserta.results.index') }}" class="path-step" style="text-decoration:none;">
            <div class="step-num">4</div>
            <div class="step-title">Riwayat & Evaluasi</div>
            <div class="step-desc">Evaluasi skor per subtest, periksa pembahasan ber-KaTeX, dan bandingkan peringkat.</div>
        </a>
    </div>
</div>

{{-- Modul Pembelajaran Terbaru --}}
<div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:1.25rem;">
    <h3 style="font-family:'Outfit', sans-serif; font-size:1.1rem;font-weight:700;color:var(--text);display:flex;align-items:center;gap:0.5rem;">
        <span style="background:var(--primary);width:4px;height:18px;border-radius:2px;display:inline-block;"></span>
        Modul Pembelajaran Terbaru
    </h3>
    <a href="{{ route('peserta.modules.index') }}" style="font-size:0.8rem;color:var(--primary);text-decoration:none;font-weight:700;display:flex;align-items:center;gap:0.25rem;transition:transform var(--transition);" onmouseover="this.style.transform='translateX(3px)'" onmouseout="this.style.transform='translateX(0)'">
        Lihat Semua &rarr;
    </a>
</div>

@if($modules->isEmpty())
    <div style="background:var(--surface);border:1px dashed var(--border);border-radius:var(--radius-lg);padding:2.5rem 1.5rem;text-align:center;margin-bottom:2.5rem;">
        <p style="font-weight:600;color:var(--text-muted);font-size:0.85rem;">Belum ada modul materi untuk program bimbingan Anda.</p>
    </div>
@else
    <div class="tryout-grid" style="margin-bottom:2.5rem;">
        @foreach($modules as $m)
        <div class="tryout-card card-module">
            <div>
                <span class="badge" style="background:#EFF6FF; color:var(--primary); border-color:#BFDBFE; margin-bottom:0.75rem;">
                    {{ $m->questionCode->code ?? 'UMUM' }}
                </span>
                <div style="font-size:0.7rem; color:var(--text-muted); font-weight:700; margin-bottom:0.5rem; text-transform:uppercase; letter-spacing:0.02em;">
                    {{ $m->category->name ?? 'Materi' }}
                </div>
                <h4 style="font-size:0.95rem;font-weight:700;color:var(--text);line-height:1.3;margin-bottom:0.5rem;">{{ $m->name }}</h4>
                <p style="font-size:0.8rem;color:var(--text-muted);line-height:1.4;">{{ Str::limit($m->description, 90) }}</p>
            </div>
            
            <div style="display:flex; align-items:center; justify-content:space-between; margin-top:auto; padding-top:0.75rem; border-top:1px solid var(--border);">
                <span style="font-size:0.75rem; color:var(--text-muted); font-weight:600; display:flex; align-items:center; gap:0.25rem;">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/></svg>
                    15 Menit Baca
                </span>
                <a href="{{ route('peserta.modules.show', $m) }}" class="btn btn-secondary btn-sm" style="font-weight:700; padding:0.4rem 0.85rem; font-size:0.8rem;">
                    Buka Modul
                </a>
            </div>
        </div>
        @endforeach
    </div>
@endif

{{-- Drill Soal Terbaru --}}
<div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:1.25rem;">
    <h3 style="font-family:'Outfit', sans-serif; font-size:1.1rem;font-weight:700;color:var(--text);display:flex;align-items:center;gap:0.5rem;">
        <span style="background:var(--success);width:4px;height:18px;border-radius:2px;display:inline-block;"></span>
        Latihan Drill Soal
    </h3>
    <a href="{{ route('peserta.drills.index') }}" style="font-size:0.8rem;color:var(--primary);text-decoration:none;font-weight:700;display:flex;align-items:center;gap:0.25rem;transition:transform var(--transition);" onmouseover="this.style.transform='translateX(3px)'" onmouseout="this.style.transform='translateX(0)'">
        Lihat Semua &rarr;
    </a>
</div>

@if($drills->isEmpty())
    <div style="background:var(--surface);border:1px dashed var(--border);border-radius:var(--radius-lg);padding:2.5rem 1.5rem;text-align:center;margin-bottom:2.5rem;">
        <p style="font-weight:600;color:var(--text-muted);font-size:0.85rem;">Belum ada paket Drill Soal yang tersedia.</p>
    </div>
@else
    <div class="tryout-grid" style="margin-bottom:2.5rem;">
        @foreach($drills as $tryout)
        @php
            $attempts = $tryout->packageAttempts;
            $attemptsCount = $attempts->count();
            $limit = $tryout->attempt_limit;
            $remaining = max(0, $limit - $attemptsCount);
        @endphp
        <div class="tryout-card card-drill">
            <div style="flex:1;min-width:0;">
                <h4 style="font-size:0.95rem;font-weight:700;color:var(--text);line-height:1.3;">{{ $tryout->nama }}</h4>
                @if($tryout->deskripsi)
                    <p style="font-size:0.8rem;color:var(--text-muted);margin-top:0.5rem;line-height:1.4;">{{ Str::limit($tryout->deskripsi, 90) }}</p>
                @endif
            </div>

            <div class="tryout-meta">
                <div class="meta-item">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/></svg>
                    {{ $tryout->durasi_menit }}m
                </div>
                <div class="meta-item">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                    {{ $tryout->questions_count }} Soal
                </div>
                <div class="meta-item" style="margin-left:auto; color:var(--success); font-weight:700; font-size:0.78rem; text-transform:uppercase;">
                    Drill
                </div>
            </div>

            <div style="display:flex; justify-content:space-between; align-items:center; font-size:0.78rem; color:var(--text-muted); background:var(--surface2); padding:0.5rem 0.75rem; border-radius:var(--radius-sm);">
                <span>Percobaan: <strong style="color:var(--text);">{{ $attemptsCount }} / {{ $limit }}</strong></span>
                @if($attempts->isNotEmpty())
                     <span>Terbaik: <strong style="color:#059669; font-weight:700;">{{ $attempts->max('score') }}%</strong></span>
                @endif
            </div>

            @if($remaining > 0)
                <a href="{{ route('peserta.exam.start', $tryout) }}" class="btn btn-primary" style="justify-content:center;width:100%;font-size:0.85rem;font-weight:700;box-shadow:none;">
                    Mulai {{ $attemptsCount > 0 ? 'Ulang' : 'Latihan' }}
                </a>
            @else
                <button class="btn btn-secondary" style="justify-content:center;width:100%;font-size:0.85rem;cursor:not-allowed;opacity:0.6;font-weight:700;" disabled>
                    Batas Percobaan Habis
                </button>
            @endif
        </div>
        @endforeach
    </div>
@endif

{{-- Tryout Akbar Terbaru --}}
<div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:1.25rem;">
    <h3 style="font-family:'Outfit', sans-serif; font-size:1.1rem;font-weight:700;color:var(--text);display:flex;align-items:center;gap:0.5rem;">
        <span style="background:#F59E0B;width:4px;height:18px;border-radius:2px;display:inline-block;"></span>
        Paket Tryout Akbar
    </h3>
    <a href="{{ route('peserta.tryouts.index') }}" style="font-size:0.8rem;color:var(--primary);text-decoration:none;font-weight:700;display:flex;align-items:center;gap:0.25rem;transition:transform var(--transition);" onmouseover="this.style.transform='translateX(3px)'" onmouseout="this.style.transform='translateX(0)'">
        Lihat Semua &rarr;
    </a>
</div>

@if($tryouts->isEmpty())
    <div style="background:var(--surface);border:1px dashed var(--border);border-radius:var(--radius-lg);padding:2.5rem 1.5rem;text-align:center;margin-bottom:2.5rem;">
        <p style="font-weight:600;color:var(--text-muted);font-size:0.85rem;">Belum ada paket Tryout Akbar yang tersedia.</p>
    </div>
@else
    <div class="tryout-grid" style="margin-bottom:2.5rem;">
        @foreach($tryouts as $tryout)
        @php
            $attempts = $tryout->packageAttempts;
            $attemptsCount = $attempts->count();
            $limit = $tryout->attempt_limit;
            $remaining = max(0, $limit - $attemptsCount);
        @endphp
        <div class="tryout-card card-tryout">
            <div style="flex:1;min-width:0;">
                <h4 style="font-size:0.95rem;font-weight:700;color:var(--text);line-height:1.3;">{{ $tryout->nama }}</h4>
                @if($tryout->deskripsi)
                    <p style="font-size:0.8rem;color:var(--text-muted);margin-top:0.5rem;line-height:1.4;">{{ Str::limit($tryout->deskripsi, 90) }}</p>
                @endif
            </div>

            <div class="tryout-meta">
                <div class="meta-item">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/></svg>
                    {{ $tryout->durasi_menit }}m
                </div>
                <div class="meta-item">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                    {{ $tryout->questions_count }} Soal
                </div>
                <div class="meta-item" style="margin-left:auto; color:#F59E0B; font-weight:700; font-size:0.78rem; text-transform:uppercase;">
                    {{ $tryout->exam_mode === 'seb' ? 'SEB Mode' : 'Normal' }}
                </div>
            </div>

            <div style="display:flex; justify-content:space-between; align-items:center; font-size:0.78rem; color:var(--text-muted); background:var(--surface2); padding:0.5rem 0.75rem; border-radius:var(--radius-sm);">
                <span>Percobaan: <strong style="color:var(--text);">{{ $attemptsCount }} / {{ $limit }}</strong></span>
                @if($attempts->isNotEmpty())
                     <span>Terbaik: <strong style="color:#059669; font-weight:700;">{{ $attempts->max('score') }}%</strong></span>
                @endif
            </div>

            @if($remaining > 0)
                @if($tryout->exam_mode === 'seb')
                    <div style="display:flex; gap:0.5rem;">
                        <a href="{{ route('peserta.exam.sebConfig', $tryout) }}" class="btn btn-secondary btn-sm" style="flex:1; justify-content:center; color:#dc2626; border-color:#fecaca; font-weight:700;" title="Download file konfigurasi untuk membuka Safe Exam Browser">
                            Unduh SEB
                        </a>
                        <a href="{{ route('peserta.exam.start', $tryout) }}" class="btn btn-primary btn-sm" style="flex:1; justify-content:center; font-weight:700;">
                            Mulai
                        </a>
                    </div>
                @else
                    <a href="{{ route('peserta.exam.start', $tryout) }}" class="btn btn-primary" style="justify-content:center;width:100%;font-size:0.85rem;font-weight:700;box-shadow:none;">
                        Mulai Ujian
                    </a>
                @endif
            @else
                <button class="btn btn-secondary" style="justify-content:center;width:100%;font-size:0.85rem;cursor:not-allowed;opacity:0.6;font-weight:700;" disabled>
                    Batas Percobaan Habis
                </button>
            @endif
        </div>
        @endforeach
    </div>
@endif

{{-- Riwayat Singkat --}}
@if($riwayat->isNotEmpty())
<div>
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:1.25rem;">
        <h3 style="font-family:'Outfit', sans-serif; font-size:1.1rem;font-weight:700;color:var(--text);display:flex;align-items:center;gap:0.5rem;">
            <span style="background:var(--text-muted);width:4px;height:18px;border-radius:2px;display:inline-block;"></span>
            Riwayat Ujian Terakhir
        </h3>
        <a href="{{ route('peserta.results.index') }}" class="btn btn-secondary btn-sm" style="font-weight:700; font-size:0.8rem; display:flex; align-items:center; gap:0.25rem;">
            Lihat Semua
            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="9 18 15 12 9 6"/></svg>
        </a>
    </div>

    <div class="riwayat-table-card">
        <div style="overflow-x:auto;">
            <table>
                <thead>
                    <tr>
                        <th style="font-weight:700; font-size:0.75rem;">Paket Ujian</th>
                        <th style="text-align:center; font-weight:700; font-size:0.75rem;">Skor</th>
                        <th style="font-weight:700; font-size:0.75rem;">Benar / Salah / Kosong</th>
                        <th style="font-weight:700; font-size:0.75rem;">Tanggal</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($riwayat as $r)
                    <tr>
                        <td style="font-weight:600; color:var(--text);">
                            <span class="badge" style="background:{{ $r->tryoutPackage->jenis_ujian === 'drill' ? '#EFF6FF' : '#FFFBEB' }}; color:{{ $r->tryoutPackage->jenis_ujian === 'drill' ? 'var(--primary)' : '#D97706' }}; border-color:{{ $r->tryoutPackage->jenis_ujian === 'drill' ? '#BFDBFE' : '#FDE68A' }}; font-size:0.65rem; padding:0.15rem 0.4rem; margin-right:0.5rem; font-weight:700; text-transform:uppercase;">
                                {{ $r->tryoutPackage->jenis_ujian === 'drill' ? 'Drill' : 'Tryout' }}
                            </span>
                            {{ $r->tryoutPackage->nama }}
                        </td>
                        <td style="text-align:center;">
                            @php
                                $score = $r->skor_total;
                                $scoreColor = $score >= 70 ? 'var(--success)' : ($score >= 50 ? '#D97706' : 'var(--error)');
                            @endphp
                            <span style="font-family:'Outfit', sans-serif; font-weight:800;font-size:1.15rem;color:{{ $scoreColor }};">{{ $score }}%</span>
                        </td>
                        <td>
                            <span style="color:var(--success);font-weight:700;">{{ $r->jumlah_benar }}</span>
                            <span style="color:var(--text-light);margin:0 2px;">/</span>
                            <span style="color:var(--error);font-weight:700;">{{ $r->jumlah_salah }}</span>
                            <span style="color:var(--text-light);margin:0 2px;">/</span>
                            <span style="color:var(--text-muted);font-weight:500;">{{ $r->jumlah_kosong }}</span>
                        </td>
                        <td style="color:var(--text-muted);font-size:0.8rem;white-space:nowrap;font-weight:500;">{{ $r->created_at->format('d M Y') }}</td>
                        <td style="text-align:right;">
                            <a href="{{ route('peserta.exam.result', $r) }}" class="btn btn-secondary btn-sm" style="font-weight:700; font-size:0.8rem; padding:0.4rem 0.85rem;">
                                Detail
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endif
@endsection

