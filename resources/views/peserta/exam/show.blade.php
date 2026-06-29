<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Soal {{ $nomor }}/{{ $totalSoal }} — {{ $session->tryoutPackage->nama }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --primary: #1E2A78;
            --primary-dark: #141D54;
            --primary-light: #2A3B9E;
            --bg: #f8fafc;
            --surface: #ffffff;
            --surface2: #f1f5f9;
            --surface3: #e2e8f0;
            --text: #0f172a;
            --text-muted: #64748b;
            --border: #e2e8f0;
            --answered: #10B981;
            --ragu: #f59e0b;
            --unanswered: #f1f5f9;
            --success: #10b981;
            --error: #ef4444;
            --accent: #F4C542;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: var(--bg);
            color: var(--text);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        /* ── Header ujian ── */
        .exam-header {
            background: var(--surface);
            border-bottom: 1px solid var(--border);
            padding: 0.85rem 1.5rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: sticky;
            top: 0;
            z-index: 100;
            gap: 1rem;
        }

        .exam-info h2 {
            font-size: 0.95rem;
            font-weight: 700;
            color: var(--text);
        }

        .exam-info p {
            font-size: 0.78rem;
            color: var(--text-muted);
            margin-top: 0.1rem;
        }

        /* ── Timer ── */
        .timer-box {
            display: flex;
            align-items: center;
            gap: 0.6rem;
            background: var(--surface2);
            border: 1px solid var(--border);
            border-radius: 10px;
            padding: 0.5rem 1rem;
            transition: border-color 0.3s;
        }

        .timer-box.warning { border-color: #f59e0b; animation: pulse 1s infinite; }
        .timer-box.danger  { border-color: #ef4444; animation: pulse 0.5s infinite; }

        @keyframes pulse {
            0%, 100% { box-shadow: 0 0 0 0 rgba(239,68,68,0); }
            50%       { box-shadow: 0 0 0 4px rgba(239,68,68,0.15); }
        }

        .timer-icon { font-size: 1rem; }

        #timerDisplay {
            font-size: 1.15rem;
            font-weight: 700;
            font-variant-numeric: tabular-nums;
            letter-spacing: 0.02em;
            color: var(--text);
        }

        .timer-label {
            font-size: 0.68rem;
            color: var(--text-muted);
            font-weight: 500;
        }

        .header-right { display: flex; align-items: center; gap: 0.75rem; }

        /* ── Layout ── */
        .exam-body {
            flex: 1;
            display: flex;
            max-width: 1200px;
            width: 100%;
            margin: 0 auto;
            padding: 1.5rem;
            gap: 1.5rem;
        }

        /* ── Soal Panel ── */
        .soal-panel {
            flex: 1;
            display: flex;
            flex-direction: column;
            gap: 1.25rem;
        }

        .soal-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 0.75rem;
        }

        .soal-number {
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .soal-num-badge {
            background: var(--primary);
            color: #fff;
            width: 34px;
            height: 34px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 0.95rem;
        }

        .soal-meta { font-size: 0.8rem; color: var(--text-muted); font-weight: 500; }

        .btn-ragu {
            display: flex;
            align-items: center;
            gap: 0.4rem;
            padding: 0.5rem 0.85rem;
            border-radius: 8px;
            font-size: 0.82rem;
            font-weight: 600;
            border: 1px solid var(--border);
            cursor: pointer;
            transition: all 0.2s;
            font-family: 'Inter', sans-serif;
        }

        .btn-ragu.active {
            background: #fffbeb;
            border-color: #fde68a;
            color: #b45309;
        }

        .btn-ragu:not(.active) {
            background: var(--surface);
            border-color: var(--border);
            color: var(--text-muted);
        }

        .soal-text-card {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 12px;
            padding: 1.75rem;
            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.05), 0 1px 2px 0 rgba(0, 0, 0, 0.03);
        }

        .soal-text {
            font-size: 1.05rem;
            line-height: 1.75;
            font-weight: 400;
            color: #1e293b;
        }

        /* ── Opsi ── */
        .opsi-list { display: flex; flex-direction: column; gap: 0.75rem; }

        .opsi-item {
            display: flex;
            align-items: center;
            gap: 1rem;
            padding: 0.85rem 1.1rem;
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.18s;
            -webkit-user-select: none;
            user-select: none;
            box-shadow: 0 1px 2px 0 rgba(0,0,0,0.02);
        }

        .opsi-item:hover {
            border-color: var(--primary-light);
            background: #f8fafc;
        }

        .opsi-item.selected {
            border-color: var(--primary);
            background: #eff6ff;
        }

        .opsi-label {
            width: 30px;
            height: 30px;
            border-radius: 50%;
            background: #f1f5f9;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 0.82rem;
            flex-shrink: 0;
            transition: all 0.18s;
            border: 1px solid var(--border);
            color: var(--text);
        }

        .opsi-item.selected .opsi-label {
            background: var(--primary);
            color: #fff;
            border-color: var(--primary);
        }

        .opsi-text { font-size: 0.95rem; line-height: 1.5; color: #334155; }

        /* ── Navigation ── */
        .soal-nav {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 0.75rem;
            margin-top: 0.5rem;
        }

        .btn-nav {
            display: flex;
            align-items: center;
            gap: 0.4rem;
            padding: 0.55rem 1rem;
            border-radius: 8px;
            font-size: 0.85rem;
            font-weight: 600;
            text-decoration: none;
            border: 1px solid var(--border);
            background: var(--surface);
            color: var(--text);
            cursor: pointer;
            transition: all 0.2s;
            font-family: 'Inter', sans-serif;
        }

        .btn-nav:hover { background: var(--surface2); }
        .btn-nav:disabled { opacity: 0.4; cursor: not-allowed; }

        .btn-submit {
            padding: 0.55rem 1.25rem;
            background: var(--primary);
            color: #fff;
            border: none;
            border-radius: 8px;
            font-size: 0.85rem;
            font-weight: 600;
            cursor: pointer;
            font-family: 'Inter', sans-serif;
            transition: all 0.2s;
        }

        .btn-submit:hover { background: var(--primary-dark); }

        /* ── Sidebar Navigasi Soal ── */
        .nav-panel {
            width: 280px;
            flex-shrink: 0;
        }

        .nav-panel-card {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 12px;
            padding: 1.25rem;
            position: sticky;
            top: 80px;
            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.05), 0 1px 2px 0 rgba(0, 0, 0, 0.03);
        }

        .nav-panel-card h3 {
            font-size: 0.88rem;
            font-weight: 700;
            margin-bottom: 0.85rem;
            padding-bottom: 0.6rem;
            border-bottom: 1px solid var(--border);
            color: var(--text);
        }

        .nav-legend {
            display: flex;
            flex-wrap: wrap;
            gap: 0.65rem;
            margin-bottom: 1rem;
            font-size: 0.72rem;
            color: var(--text-muted);
            font-weight: 500;
        }

        .legend-item { display: flex; align-items: center; gap: 0.3rem; }

        .legend-dot {
            width: 10px;
            height: 10px;
            border-radius: 3px;
        }

        .nav-grid {
            display: grid;
            grid-template-columns: repeat(5, 1fr);
            gap: 6px;
        }

        .nav-btn {
            aspect-ratio: 1;
            border: 1px solid var(--border);
            border-radius: 6px;
            font-size: 0.8rem;
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.15s;
            font-family: 'Inter', sans-serif;
            background: var(--unanswered);
            color: var(--text-muted);
        }

        .nav-btn.current { border: 2.5px solid var(--primary); color: var(--primary); font-weight: 800; }
        .nav-btn.answered { background: var(--answered); color: #fff; border-color: var(--answered); }
        .nav-btn.ragu { background: var(--ragu); color: #fff; border-color: var(--ragu); }
        .nav-btn.answered.current { border: 2.5px solid var(--primary); }
        .nav-btn.ragu.current { border: 2.5px solid var(--primary); }
        .nav-btn:hover:not(.current):not(.answered):not(.ragu) { border-color: var(--primary); background: #e2e8f0; }

        .stat-mini {
            display: flex;
            justify-content: space-between;
            margin-top: 1.25rem;
            padding-top: 0.85rem;
            border-top: 1px solid var(--border);
            font-size: 0.75rem;
        }

        .stat-mini-item { text-align: center; }
        .stat-mini-item .val { font-weight: 700; font-size: 1rem; color: var(--text); }
        .stat-mini-item .lbl { color: var(--text-muted); font-weight: 500; }

        /* ── Save indicator ── */
        .save-indicator {
            position: fixed;
            bottom: 1.5rem;
            right: 1.5rem;
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 99px;
            padding: 0.5rem 1rem;
            font-size: 0.78rem;
            color: var(--text-muted);
            display: flex;
            align-items: center;
            gap: 0.4rem;
            transition: all 0.3s;
            opacity: 0;
            box-shadow: 0 4px 12px rgba(0,0,0,0.05);
        }

        .save-indicator.show { opacity: 1; }
        .save-indicator.saved { border-color: var(--success); color: #059669; background: #ecfdf5; }
        .save-indicator.error { border-color: var(--error); color: #dc2626; background: #fef2f2; }

        /* Modal Submit */
        .modal-overlay {
            position: fixed;
            inset: 0;
            background: rgba(15,23,42,0.3);
            -webkit-backdrop-filter: blur(2px);
            backdrop-filter: blur(2px);
            display: none;
            align-items: center;
            justify-content: center;
            z-index: 1000;
        }

        .modal-overlay.active { display: flex; }
        .modal-overlay:not(.active) {
            display: none !important;
            pointer-events: none !important;
            visibility: hidden !important;
            z-index: -100 !important;
        }

        .modal {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 12px;
            padding: 1.75rem;
            max-width: 400px;
            width: 90%;
            transform: scale(0.95);
            transition: transform 0.25s;
            box-shadow: 0 20px 25px -5px rgba(0,0,0,0.1), 0 10px 10px -5px rgba(0,0,0,0.04);
        }

        .modal-overlay.active .modal { transform: scale(1); }

        .modal h3 { font-size: 1.1rem; font-weight: 700; margin-bottom: 0.5rem; color: var(--text); }
        .modal p  { font-size: 0.85rem; color: var(--text-muted); margin-bottom: 1.25rem; line-height: 1.6; }

        .modal-stats {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr;
            gap: 0.65rem;
            margin-bottom: 1.25rem;
        }

        .modal-stat {
            text-align: center;
            padding: 0.6rem;
            background: var(--surface2);
            border-radius: 8px;
            border: 1px solid var(--border);
        }

        .modal-stat .val { font-size: 1.2rem; font-weight: 700; color: var(--text); }
        .modal-stat .lbl { font-size: 0.7rem; color: var(--text-muted); margin-top: 0.1rem; font-weight: 500; }

        .modal-actions { display: flex; gap: 0.75rem; }
        .btn-cancel { flex: 1; padding: 0.6rem; background: var(--surface); border: 1px solid var(--border); border-radius: 8px; color: var(--text); font-size: 0.85rem; cursor: pointer; font-family: 'Inter', sans-serif; font-weight: 500; }
        .btn-confirm { flex: 1; padding: 0.6rem; background: var(--primary); color: #fff; border: none; border-radius: 8px; font-size: 0.85rem; font-weight: 600; cursor: pointer; font-family: 'Inter', sans-serif; }
        .btn-confirm:hover { background: var(--primary-dark); }

    </style>
</head>
<body>

{{-- Header Ujian --}}
<header class="exam-header">
    <div class="exam-info" style="display: flex; flex-direction: column;">
        <div style="display: flex; align-items: center; gap: 0.5rem; flex-wrap: wrap;">
            <h2 style="font-weight: 800; color: var(--primary);">{{ $session->tryoutPackage->nama }}</h2>
            <span id="lockdownBadge" style="font-size: 0.68rem; font-weight: 700; padding: 0.15rem 0.5rem; border-radius: 99px; background: #f1f5f9; color: #64748b; display: inline-flex; align-items: center; gap: 0.25rem;">
                🔓 Mode Normal
            </span>
        </div>
        <p>Soal {{ $nomor }} dari {{ $totalSoal }}</p>
    </div>

    <!-- Participant Name -->
    <div class="participant-name" style="text-align: center; display: flex; flex-direction: column;">
        <span style="font-weight: 700; font-size: 0.9rem; color: var(--text);">{{ Auth::user()->name }}</span>
        <span style="font-size: 0.75rem; color: var(--text-muted); font-weight: 500;">
            {{ Auth::user()->email }} @if(Auth::user()->group) · {{ Auth::user()->group->nama }} @endif
        </span>
    </div>

    <div class="timer-box" id="timerBox">
        <span class="timer-icon" style="color: var(--text-muted);">
            <svg style="width: 16px; height: 16px; display: inline-block; vertical-align: middle; fill: none; stroke: currentColor; stroke-width: 2.5;" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/></svg>
        </span>
        <div>
            <div id="timerDisplay">--:--</div>
            <div class="timer-label">Sisa Waktu</div>
        </div>
    </div>

    <div class="header-right">
        <button class="btn-submit" onclick="openModal()">Kumpulkan Ujian</button>
    </div>
</header>

{{-- Body --}}
<div class="exam-body">

    {{-- Soal Panel --}}
    <div class="soal-panel">
        <div class="soal-header">
            <div class="soal-number">
                <div class="soal-num-badge">{{ $nomor }}</div>
                <div>
                    <div style="font-weight:700; color: var(--text);">Soal {{ $nomor }} dari {{ $totalSoal }}</div>
                    <div class="soal-meta">
                        {{ $question->category->name }}
                        <span class="badge badge-{{ strtolower($question->category->kode) }}" style="margin-left:0.4rem;font-size:0.7rem;padding:0.15rem 0.5rem;">{{ $question->category->kode }}</span>
                    </div>
                </div>
            </div>

            <button id="btnRagu" class="btn-ragu {{ ($currentAnswer && $currentAnswer->is_ragu) ? 'active' : '' }}" onclick="toggleRagu()">
                <span id="raguIcon" style="font-size: 1.1rem; line-height: 1;">{{ ($currentAnswer && $currentAnswer->is_ragu) ? '⚑' : '⚐' }}</span>
                <span id="raguText">{{ ($currentAnswer && $currentAnswer->is_ragu) ? 'Ragu-ragu' : 'Tandai Ragu' }}</span>
            </button>
        </div>

        <div class="soal-text-card">
            @php
                $qImg = $question->question_image ?: $question->image;
            @endphp
            @if($qImg)
                <div style="margin-bottom: 1.25rem; text-align: center;">
                    <img src="{{ asset($qImg) }}" alt="Gambar Soal" style="max-width: 100%; max-height: 380px; height: auto; border-radius: 8px; border: 1px solid var(--border); box-shadow: var(--shadow-sm);">
                </div>
            @endif
            @if($question->soal)
                <p class="soal-text">{{ $question->soal }}</p>
            @endif
        </div>

        <div class="opsi-list" id="opsiList">
            @php 
                $selectedJawaban = $currentAnswer?->jawaban; 
                $mapping = $currentAnswer?->options_mapping;
                $visualKeys = $mapping ? array_keys($mapping) : ['A', 'B', 'C', 'D', 'E'];
            @endphp
            @foreach($visualKeys as $visualKey)
                @php 
                    $originalKey = ($mapping && isset($mapping[$visualKey])) ? $mapping[$visualKey] : $visualKey;
                    $opsiText = $question->{'opsi_'.strtolower($originalKey)}; 
                    $optImg = $question->{'option_'.strtolower($originalKey).'_image'};
                @endphp
                @if($opsiText || $optImg)
                <div class="opsi-item {{ $selectedJawaban === $visualKey ? 'selected' : '' }}"
                     id="opsi-{{ $visualKey }}"
                     onclick="pilihJawaban('{{ $visualKey }}')"
                     style="display: flex; flex-direction: column; align-items: flex-start; gap: 0.5rem; padding: 0.85rem 1.25rem;">
                     <div style="display: flex; align-items: center; gap: 0.75rem; width: 100%;">
                         <div class="opsi-label">{{ $visualKey }}</div>
                         @if($opsiText)
                             <div class="opsi-text" style="flex: 1;">{{ $opsiText }}</div>
                         @endif
                     </div>
                     @if($optImg)
                         <div style="margin-top: 0.25rem; padding-left: 2.25rem; width: 100%;">
                             <img src="{{ asset($optImg) }}" alt="Gambar Opsi {{ $visualKey }}" style="max-height: 120px; max-width: 100%; height: auto; border-radius: 6px; border: 1px solid var(--border);">
                         </div>
                     @endif
                </div>
                @endif
            @endforeach
        </div>

        {{-- Navigasi prev/next --}}
        <div class="soal-nav">
            @if($nomor > 1)
                <a href="{{ route('peserta.exam.show', [$session->id, $nomor - 1]) }}" class="btn-nav" id="btnPrev">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="15 18 9 12 15 6"/></svg>
                    Sebelumnya
                </a>
            @else
                <span></span>
            @endif

            <span style="font-size:0.82rem;font-weight:600;color:var(--text-muted);background:var(--surface2);padding:0.3rem 0.75rem;border-radius:99px;border:1px solid var(--border);">{{ $nomor }} / {{ $totalSoal }}</span>

            @if($nomor < $totalSoal)
                <a href="{{ route('peserta.exam.show', [$session->id, $nomor + 1]) }}" class="btn-nav" id="btnNext">
                    Berikutnya
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="9 18 15 12 9 6"/></svg>
                </a>
            @else
                <button class="btn-nav" onclick="openModal()" style="background:var(--primary);color:#fff;border-color:var(--primary);">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M20 6L9 17l-5-5"/></svg>
                    Selesai
                </button>
            @endif
        </div>
    </div>

    {{-- Navigasi Soal Grid --}}
    <aside class="nav-panel">
        <div class="nav-panel-card">
            <h3>Navigasi Soal</h3>
            <div class="nav-legend">
                <div class="legend-item"><div class="legend-dot" style="background:var(--answered);"></div> Dijawab</div>
                <div class="legend-item"><div class="legend-dot" style="background:var(--ragu);"></div> Ragu</div>
                <div class="legend-item"><div class="legend-dot" style="background:var(--unanswered); border: 1px solid var(--border);"></div> Belum</div>
            </div>
            <div class="nav-grid" id="navGrid">
                @foreach($questions as $i => $q)
                    @php
                        $ans    = $answers->get($q->id);
                        $status  = '';
                        if ($ans && $ans->is_ragu) $status = 'ragu';
                        elseif ($ans && $ans->jawaban) $status = 'answered';
                        
                        $isCurrent = (($i+1) == $nomor);
                    @endphp
                    <a href="{{ route('peserta.exam.show', [$session->id, $i+1]) }}"
                       class="nav-btn {{ $status }} {{ $isCurrent ? 'current' : '' }}"
                       title="Soal {{ $i+1 }}">{{ $i+1 }}</a>
                @endforeach
            </div>

            @php
                $totalDijawab = $answers->filter(fn($a) => $a->jawaban)->count();
                $totalRagu    = $answers->filter(fn($a) => $a->is_ragu)->count();
                $totalBelum   = $totalSoal - $totalDijawab;
            @endphp

            <div class="stat-mini">
                <div class="stat-mini-item">
                    <div class="val" style="color:var(--answered);">{{ $totalDijawab }}</div>
                    <div class="lbl">Dijawab</div>
                </div>
                <div class="stat-mini-item">
                    <div class="val" style="color:var(--ragu);">{{ $totalRagu }}</div>
                    <div class="lbl">Ragu</div>
                </div>
                <div class="stat-mini-item">
                    <div class="val" style="color:var(--text-light);">{{ $totalBelum }}</div>
                    <div class="lbl">Belum</div>
                </div>
            </div>
        </div>
    </aside>
</div>

{{-- Save Indicator --}}
<div class="save-indicator" id="saveIndicator"></div>

{{-- Modal Submit --}}
<div class="modal-overlay" id="modalOverlay">
    <div class="modal">
        <h3>📤 Kumpulkan Ujian?</h3>
        <p>Pastikan semua soal sudah Anda isi. Setelah dikumpulkan, jawaban tidak bisa diubah.</p>
        <div class="modal-stats">
            <div class="modal-stat">
                <div class="val" style="color:var(--primary-light);">{{ $totalDijawab }}</div>
                <div class="lbl">Dijawab</div>
            </div>
            <div class="modal-stat">
                <div class="val" style="color:#fcd34d;">{{ $totalRagu }}</div>
                <div class="lbl">Ragu</div>
            </div>
            <div class="modal-stat">
                <div class="val" style="color:#fda4af;">{{ $totalBelum }}</div>
                <div class="lbl">Belum</div>
            </div>
        </div>
        <div class="modal-actions">
            <button class="btn-cancel" onclick="closeModal()">Kembali</button>
            <button class="btn-confirm" id="btnConfirmSubmit" onclick="submitUjian()">Ya, Kumpulkan!</button>
        </div>
    </div>
</div>

<script>
// ── Konfigurasi ──────────────────────────────────────────────
const SESSION_ID    = {{ $session->id }};
const QUESTION_ID   = {{ $question->id }};
const REMAINING_SEC = {{ $session->remaining_seconds }};
const CSRF          = document.querySelector('meta[name="csrf-token"]').content;
const SAVE_URL      = '{{ route("peserta.exam.saveAnswer", $session->id) }}';
const RAGU_URL      = '{{ route("peserta.exam.toggleRagu", $session->id) }}';
const SUBMIT_URL    = '{{ route("peserta.exam.submit", $session->id) }}';
const RESULT_BASE   = '{{ url("/peserta/hasil/") }}/';
const VIOLATION_URL = '{{ route("peserta.exam.logViolation", $session->id) }}';

let selectedJawaban = '{{ $currentAnswer?->jawaban ?? "" }}';
let isRagu          = {{ ($currentAnswer && $currentAnswer->is_ragu) ? 'true' : 'false' }};
let saveTimeout     = null;
let autoSubmitted   = false;

// ── Safe Exam Browser Detection & Config ──────────────────────
const examMode = '{{ $session->tryoutPackage->exam_mode }}';
const isSEB = {{ $isSEB ? 'true' : 'false' }} || navigator.userAgent.includes('SafeExamBrowser') || navigator.userAgent.includes('SEB');

if (isSEB) {
    const badge = document.getElementById('lockdownBadge');
    if (badge) {
        badge.style.background = '#dcfce7';
        badge.style.color = '#15803d';
        badge.innerHTML = '🔒 Safe Exam Browser (Locked)';
    }
}

// ── Timer ─────────────────────────────────────────────────────
const PAGE_LOAD_TIME = Date.now();
const TOTAL_REMAINING = REMAINING_SEC;

function updateTimer() {
    const elapsed = Math.floor((Date.now() - PAGE_LOAD_TIME) / 1000);
    const timeLeft = Math.max(0, TOTAL_REMAINING - elapsed);

    if (timeLeft <= 0) {
        document.getElementById('timerDisplay').textContent = '00:00';
        if (!autoSubmitted) {
            autoSubmitted = true;
            autoSubmit();
        }
        return;
    }

    const h = Math.floor(timeLeft / 3600);
    const m = Math.floor((timeLeft % 3600) / 60);
    const s = timeLeft % 60;

    let display;
    if (h > 0) {
        display = `${h}:${String(m).padStart(2,'0')}:${String(s).padStart(2,'0')}`;
    } else {
        display = `${String(m).padStart(2,'0')}:${String(s).padStart(2,'0')}`;
    }

    document.getElementById('timerDisplay').textContent = display;

    const box = document.getElementById('timerBox');
    if (box) {
        box.classList.remove('warning','danger');
        if (timeLeft <= 60)       box.classList.add('danger');
        else if (timeLeft <= 300) box.classList.add('warning');
    }
}

updateTimer();
const timerInterval = setInterval(updateTimer, 1000);

async function autoSubmit() {
    clearInterval(timerInterval);
    document.getElementById('timerDisplay').textContent = '00:00';
    showSaveIndicator('⏰ Waktu habis! Mengumpulkan...', 'saved');
    await doSubmit();
}

// ── Pilih Jawaban ─────────────────────────────────────────────
function pilihJawaban(opsi) {
    // Toggle: klik lagi = batal
    if (selectedJawaban === opsi) {
        selectedJawaban = null;
    } else {
        selectedJawaban = opsi;
    }

    // Update UI
    document.querySelectorAll('.opsi-item').forEach(el => el.classList.remove('selected'));
    if (selectedJawaban) {
        document.getElementById('opsi-' + selectedJawaban)?.classList.add('selected');
    }

    saveJawaban();
}

function saveJawaban() {
    clearTimeout(saveTimeout);
    showSaveIndicator('💾 Menyimpan...', '');
    saveTimeout = setTimeout(async () => {
        try {
            const res = await fetch(SAVE_URL, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF },
                body: JSON.stringify({ question_id: QUESTION_ID, jawaban: selectedJawaban || null })
            });
            const data = await res.json();

            if (data.redirect) {
                window.location.href = data.redirect;
                return;
            }

            showSaveIndicator('Tersimpan', 'saved');
        } catch(e) {
            showSaveIndicator('Gagal menyimpan', 'error');
        }
    }, 400);
}

// ── Toggle Ragu ───────────────────────────────────────────────
async function toggleRagu() {
    try {
        const res = await fetch(RAGU_URL, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF },
            body: JSON.stringify({ question_id: QUESTION_ID })
        });
        const data = await res.json();
        if (data.success) {
            isRagu = data.is_ragu;
            const btn  = document.getElementById('btnRagu');
            const icon = document.getElementById('raguIcon');
            const text = document.getElementById('raguText');

            if (isRagu) {
                btn.classList.add('active');
                icon.textContent = '⚑';
                text.textContent = 'Ragu-ragu';
            } else {
                btn.classList.remove('active');
                icon.textContent = '⚐';
                text.textContent = 'Tandai Ragu';
            }
        }
    } catch(e) { console.error(e); }
}

// ── Modal Submit ──────────────────────────────────────────────
function openModal() { document.getElementById('modalOverlay').classList.add('active'); }
function closeModal() { document.getElementById('modalOverlay').classList.remove('active'); }

async function submitUjian() {
    const btn = document.getElementById('btnConfirmSubmit');
    btn.disabled = true;
    btn.textContent = 'Mengumpulkan...';
    await doSubmit();
}

async function doSubmit() {
    try {
        const res = await fetch(SUBMIT_URL, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF },
            body: JSON.stringify({})
        });
        const data = await res.json();
        if (data.redirect) {
            clearInterval(timerInterval);
            window.location.href = data.redirect;
        }
    } catch(e) {
        // Fallback form submit
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = SUBMIT_URL;
        form.innerHTML = `<input type="hidden" name="_token" value="${CSRF}">`;
        document.body.appendChild(form);
        form.submit();
    }
}

// ── Save Indicator ────────────────────────────────────────────
let hideTimeout;
function showSaveIndicator(msg, type) {
    const el = document.getElementById('saveIndicator');
    el.textContent = msg;
    el.className = 'save-indicator show ' + type;
    clearTimeout(hideTimeout);
    if (type === 'saved') {
        hideTimeout = setTimeout(() => { el.classList.remove('show'); }, 2000);
    }
}

// Keyboard shortcut: A-E = pilih opsi, ArrowLeft/Right = prev/next
document.addEventListener('keydown', (e) => {
    // Prevent selecting options when modifier keys (shortcuts) are active
    if (e.ctrlKey || e.altKey || e.metaKey) return;

    if (['a','b','c','d','e'].includes(e.key.toLowerCase())) {
        pilihJawaban(e.key.toUpperCase());
    } else if (e.key === 'ArrowLeft') {
        document.getElementById('btnPrev')?.click();
    } else if (e.key === 'ArrowRight') {
        document.getElementById('btnNext')?.click();
    }
});

// ── Lockdown Restrictions (Context Menu, Copy/Cut/Paste, Development Shortcuts) ──
if (examMode === 'seb' && isSEB) {
    document.addEventListener('contextmenu', e => e.preventDefault());
    document.addEventListener('copy', e => e.preventDefault());
    document.addEventListener('cut', e => e.preventDefault());
    document.addEventListener('paste', e => e.preventDefault());

    document.addEventListener('keydown', e => {
        if (e.key === 'F12') {
            e.preventDefault();
            return false;
        }
        if (e.ctrlKey && ['u', 'p', 's', 'c', 'v'].includes(e.key.toLowerCase())) {
            e.preventDefault();
            return false;
        }
    });
}

</script>
</body>
</html>
