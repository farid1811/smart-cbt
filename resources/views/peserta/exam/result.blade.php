<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hasil Ujian — {{ $result->tryoutPackage->nama }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- KaTeX for Math Formula Rendering -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/katex@0.16.8/dist/katex.min.css">
    <script defer src="https://cdn.jsdelivr.net/npm/katex@0.16.8/dist/katex.min.js"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/katex@0.16.8/dist/contrib/auto-render.min.js" onload="renderMathInElement(document.body, {delimiters:[{left:'$$',right:'$$',display:true},{left:'$',right:'$',display:false},{left:'\\(',right:'\\)',display:false},{left:'\\[',right:'\\]',display:true}],throwOnError:false});"></script>

    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --primary: #1E2A78;
            --primary-light: #2A3B9E;
            --bg: #f8fafc;
            --surface: #ffffff;
            --surface2: #f1f5f9;
            --text: #0f172a;
            --text-muted: #64748b;
            --border: #e2e8f0;
            --success: #10b981;
            --error: #ef4444;
            --accent: #F4C542;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: var(--bg);
            color: var(--text);
            min-height: 100vh;
        }

        .topbar {
            background: var(--surface);
            border-bottom: 1px solid var(--border);
            padding: 0.85rem 1.5rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .brand { display: flex; align-items: center; gap: 0.65rem; text-decoration: none; }
        .brand h1 { font-size: 1.1rem; font-weight: 700; color: var(--text); }

        .page { max-width: 860px; margin: 0 auto; padding: 2.5rem 1.5rem; }

        /* Hero score */
        .result-hero {
            text-align: center;
            margin-bottom: 2.5rem;
            animation: fadeUp 0.5s ease;
        }

        @keyframes fadeUp {
            from { opacity: 0; transform: translateY(16px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        .score-ring {
            width: 160px;
            height: 160px;
            border-radius: 50%;
            margin: 0 auto 1.25rem;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
        }

        .score-ring svg {
            position: absolute;
            top: 0; left: 0;
            width: 100%; height: 100%;
            transform: rotate(-90deg);
        }

        .score-ring .inner {
            width: 128px;
            height: 128px;
            background: var(--surface);
            border-radius: 50%;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            z-index: 1;
            box-shadow: inset 0 2px 4px rgba(0,0,0,0.03);
        }

        .score-val { font-size: 1.8rem; font-weight: 800; }
        .score-lbl { font-size: 0.75rem; color: var(--text-muted); font-weight: 500; }

        .result-hero h2 { font-size: 1.4rem; font-weight: 700; margin-bottom: 0.35rem; color: var(--text); }
        .result-hero p  { color: var(--text-muted); font-size: 0.875rem; }

        /* Category scores */
        .cat-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 1.25rem; margin-bottom: 2rem; }

        .cat-card {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 12px;
            padding: 1.25rem;
            text-align: center;
            box-shadow: 0 1px 3px rgba(0,0,0,0.05);
        }

        .cat-name { font-size: 0.7rem; color: var(--text-muted); margin-bottom: 0.35rem; text-transform: uppercase; letter-spacing: 0.05em; font-weight: 600; }
        .cat-kode { font-size: 0.9rem; font-weight: 800; margin-bottom: 0.75rem; }
        .cat-score { font-size: 1.6rem; font-weight: 800; }
        .cat-bar { height: 6px; background: var(--surface2); border-radius: 99px; margin-top: 0.75rem; overflow: hidden; }
        .cat-bar-fill { height: 100%; border-radius: 99px; transition: width 1s ease; }

        /* Summary */
        .summary-cards { display: grid; grid-template-columns: repeat(3, 1fr); gap: 1rem; margin-bottom: 2.5rem; }

        .sum-card {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 12px;
            padding: 1rem;
            text-align: center;
            box-shadow: 0 1px 3px rgba(0,0,0,0.05);
        }

        .sum-val { font-size: 1.6rem; font-weight: 800; }
        .sum-lbl { font-size: 0.75rem; color: var(--text-muted); margin-top: 0.15rem; font-weight: 500; }

        /* Pembahasan accordion */
        .pembahasan-section { margin-bottom: 2.5rem; }
        .pembahasan-section h3 { font-size: 1.1rem; font-weight: 700; margin-bottom: 1rem; color: var(--text); }

        .soal-item {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 12px;
            margin-bottom: 0.75rem;
            overflow: hidden;
            box-shadow: 0 1px 2px rgba(0,0,0,0.02);
        }

        .soal-item-header {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.85rem 1.25rem;
            cursor: pointer;
            user-select: none;
            transition: background 0.15s;
        }

        .soal-item-header:hover { background: #f8fafc; }

        .soal-status { width: 8px; height: 8px; border-radius: 50%; flex-shrink: 0; }
        .status-benar { background: var(--success); }
        .status-salah { background: var(--error); }
        .status-kosong { background: var(--text-muted); }

        .soal-item-body {
            padding: 0 1.25rem;
            max-height: 0;
            overflow: hidden;
            background: #fafafa;
            transition: max-height 0.3s ease, padding 0.3s ease;
        }

        .soal-item-body.open {
            max-height: 2000px;
            padding: 1.25rem;
            border-top: 1px solid var(--border);
            overflow-y: auto;
        }

        .opsi-row {
            display: flex;
            flex-direction: column;
            align-items: flex-start;
            gap: 0.35rem;
            padding: 0.65rem 0.85rem;
            border-radius: 6px;
            font-size: 0.875rem;
            margin-bottom: 0.5rem;
            background: #ffffff;
            border: 1px solid var(--border);
            color: #334155;
        }

        .opsi-correct { background: #ecfdf5; border-color: #a7f3d0; color: #065f46; font-weight: 500; }
        .opsi-wrong   { background: #fef2f2; border-color: #fca5a5; color: #991b1b; }

        .actions { display: flex; gap: 0.75rem; justify-content: center; }

        .btn {
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            padding: 0.55rem 1.1rem;
            border-radius: 8px;
            font-size: 0.875rem;
            font-weight: 600;
            text-decoration: none;
            cursor: pointer;
            transition: all 0.2s;
            border: 1px solid var(--border);
            background: var(--surface);
            color: var(--text);
        }

        .btn-primary { background: var(--primary); color: #fff; border-color: var(--primary); }
        .btn-primary:hover { background: var(--primary-dark); }
        
        .badge-code {
            background: #eff6ff;
            color: #1e40af;
            border: 1px solid #bfdbfe;
            font-weight: 700;
            padding: 0.2rem 0.5rem;
            border-radius: 9999px;
            font-size: 0.7rem;
        }
    </style>
</head>
<body>

<header class="topbar">
    <a href="{{ route('peserta.dashboard') }}" class="brand">
        <img src="{{ asset('images/logo-plano.jpg') }}" alt="Bimbel Plano" style="max-height: 34px; width: auto; filter: drop-shadow(0 1px 2px rgba(30,42,120,0.05));">
    </a>
    <span style="font-size:0.85rem;color:var(--text-muted);font-weight:700;">{{ $result->user->name }}</span>
</header>

<div class="page">

    {{-- Hero Score --}}
    <div class="result-hero">
        @php
            $isSkd = ($result->tryoutPackage->group === 'SKD');
            $maxScore = $isSkd ? 550 : (count($result->category_scores ?? []) * 100);
            if ($maxScore <= 0) $maxScore = 100;
            
            $scoreVal = $result->skor_total;
            $pct = min(100, ($scoreVal / $maxScore) * 100);
            
            $color = $pct >= 70 ? '#10b981' : ($pct >= 50 ? '#f59e0b' : '#f43f5e');
            $circ  = 2 * M_PI * 54;
            $offset = $circ * (1 - $pct / 100);

            // Calculate real-time rank of this attempt among all users
            $rank = \App\Models\Result::where('tryout_package_id', $result->tryout_package_id)
                ->where('skor_total', '>', $result->skor_total)
                ->count() + 1;
            $totalParticipants = \App\Models\Result::where('tryout_package_id', $result->tryout_package_id)
                ->distinct('user_id')
                ->count('user_id');
        @endphp

        <div class="score-ring">
            <svg viewBox="0 0 120 120">
                <circle cx="60" cy="60" r="54" fill="none" stroke="var(--surface2)" stroke-width="10"/>
                <circle cx="60" cy="60" r="54" fill="none" stroke="{{ $color }}" stroke-width="10"
                    stroke-dasharray="{{ $circ }}"
                    stroke-dashoffset="{{ $offset }}"
                    stroke-linecap="round"
                    style="transition: stroke-dashoffset 1.5s ease;"/>
            </svg>
            <div class="inner">
                <div class="score-val" style="color:{{ $color }};">{{ $scoreVal }}</div>
                <div class="score-lbl">Skor / {{ $maxScore }}</div>
            </div>
        </div>

        <h2>
            @if($pct >= 70) 🎉 Luar Biasa!
            @elseif($pct >= 50) 👍 Cukup Baik
            @else 💪 Perlu Latihan Lagi
            @endif
        </h2>
        <p>{{ $result->tryoutPackage->nama }} &bull; Peringkat <strong style="color: #D97706;">#{{ $rank }}</strong> dari {{ $totalParticipants }} peserta &bull;
            @if($result->examSession->status === 'selesai')
                Selesai tepat waktu
            @else
                Waktu habis (auto-submit)
            @endif
        </p>
    </div>

    {{-- Detailed Score Breakdown --}}
    <h3 style="font-size: 1.1rem; font-weight: 700; margin-bottom: 0.75rem;">📊 Rincian Nilai per Subtest</h3>
    <div class="cat-grid">
        @if($result->category_scores && count($result->category_scores) > 0)
            @php $scoreArray = is_string($result->category_scores) ? json_decode($result->category_scores, true) : $result->category_scores; @endphp
            @foreach($scoreArray as $codeId => $data)
            @php
                $clr = '#1E2A78';
                if ($data['kode'] === 'TWK') $clr = '#4f46e5';
                elseif ($data['kode'] === 'TIU') $clr = '#10b981';
                elseif ($data['kode'] === 'TKP') $clr = '#f59e0b';
                
                // percentage for visual bar
                $pFill = $isSkd ? (($data['score'] / ($data['total'] * 5)) * 100) : $data['score'];
            @endphp
            <div class="cat-card">
                <div class="cat-name">{{ $data['name'] }}</div>
                <div class="cat-kode" style="color:{{ $clr }}; font-weight:800;">{{ $data['kode'] }}</div>
                <div class="cat-score" style="color:{{ $clr }};">
                    {{ $data['score'] }}<span style="font-size:1rem; font-weight:500;">{{ $isSkd ? '' : '%' }}</span>
                </div>
                <div style="font-size: 0.75rem; color:var(--text-muted); margin-top: 0.25rem;">
                    B: {{ $data['benar'] }} | S: {{ $data['salah'] }} | K: {{ $data['kosong'] }}
                </div>
                <div class="cat-bar"><div class="cat-bar-fill" style="width:{{ $pFill }}%; background:{{ $clr }};"></div></div>
            </div>
            @endforeach
        @endif
    </div>

    {{-- Summary --}}
    <div class="summary-cards">
        <div class="sum-card">
            <div class="sum-val" style="color:#10b981;">{{ $result->jumlah_benar }}</div>
            <div class="sum-lbl">✅ Jawaban Benar</div>
        </div>
        <div class="sum-card">
            <div class="sum-val" style="color:#ef4444;">{{ $result->jumlah_salah }}</div>
            <div class="sum-lbl">❌ Jawaban Salah</div>
        </div>
        <div class="sum-card">
            <div class="sum-val" style="color:var(--text-muted);">{{ $result->jumlah_kosong }}</div>
            <div class="sum-lbl">⬜ Tidak Dijawab</div>
        </div>
    </div>

    {{-- Pembahasan --}}
    <div class="pembahasan-section">
        <h3>📖 Pembahasan Lengkap</h3>

        @foreach($result->examSession->answers as $i => $answer)
        @php
            $q       = $answer->question;
            if (!$q) continue;
            
            $benar   = $answer->isBenar();
            $kosong  = is_null($answer->jawaban);
            $status  = $kosong ? 'kosong' : ($benar ? 'benar' : 'salah');
            
            $mapping = $answer->options_mapping;
            $visualKeys = $mapping ? array_keys($mapping) : ['A', 'B', 'C', 'D', 'E'];
            
            $visualCorrectKey = null;
            if ($mapping) {
                foreach ($mapping as $vK => $oK) {
                    if ($oK === $q->jawaban_benar) {
                        $visualCorrectKey = $vK;
                        break;
                    }
                }
            } else {
                $visualCorrectKey = $q->jawaban_benar;
            }
        @endphp
        <div class="soal-item">
            <div class="soal-item-header" onclick="togglePembahasan({{ $i }})">
                <div class="soal-status status-{{ $status }}"></div>
                <span class="badge-code">{{ $q->questionCode->code ?? '—' }}</span>
                <span style="flex:1; font-size:0.85rem; font-weight:500; color:#334155;">{{ Str::limit(strip_tags($q->soal), 70) }}</span>
                <span style="font-size:0.75rem; color:var(--text-muted); font-weight:600;">
                    @if($kosong) ⬜ Kosong
                    @elseif($benar) ✅ Benar
                    @else ❌ Salah (Pilihan: {{ $answer->jawaban }}, Kunci: {{ $visualCorrectKey }})
                    @endif
                </span>
                <span style="font-size:0.8rem; color:var(--text-muted); font-weight:bold; margin-left:0.5rem;" id="arrow-{{ $i }}">▼</span>
            </div>
            <div class="soal-item-body" id="body-{{ $i }}">
                <div style="font-size:0.72rem; color:var(--text-muted); font-weight:700; margin-bottom:0.5rem; text-transform:uppercase;">
                    Kategori: {{ $q->category->name ?? '—' }} &rarr; {{ $q->subCategory->name ?? '—' }}
                </div>
                
                <p style="font-size:0.9rem; margin-bottom:1rem; line-height:1.6; color:#0f172a; white-space: pre-wrap;">{{ $q->soal }}</p>
                
                @if($q->question_image || $q->image)
                    @php $qImg = $q->question_image ?: $q->image; @endphp
                    <div style="margin-bottom: 1.25rem;">
                        <img src="{{ asset($qImg) }}" alt="Gambar Soal" style="max-width: 100%; max-height: 320px; height: auto; border-radius: 8px; border: 1px solid var(--border);">
                    </div>
                @endif

                {{-- Option lists --}}
                <div style="margin-bottom:1.25rem;">
                    @foreach($visualKeys as $visualKey)
                    @php
                        $originalKey = ($mapping && isset($mapping[$visualKey])) ? $mapping[$visualKey] : $visualKey;
                        $opsiText = $q->{'opsi_'.strtolower($originalKey)};
                        if (!$opsiText) continue;

                        $isCorrectOption = $originalKey === $q->jawaban_benar;
                        $isPilihan = $visualKey === $answer->jawaban;
                    @endphp
                    <div class="opsi-row {{ $isCorrectOption ? 'opsi-correct' : ($isPilihan && !$isCorrectOption ? 'opsi-wrong' : '') }}">
                        <div style="display:flex; align-items:center; width:100%;">
                            <strong style="min-width:18px;">{{ $visualKey }}.</strong>
                            <span>{{ $opsiText }}</span>
                            @if($isCorrectOption) <span style="margin-left:auto; font-size:0.7rem; color:#10b981; font-weight:700;">✓ Benar</span> @endif
                            @if($isPilihan && !$isCorrectOption) <span style="margin-left:auto; font-size:0.7rem; color:#ef4444; font-weight:700;">✗ Pilihan Anda</span> @endif
                        </div>
                        @php $optImg = $q->{'option_'.strtolower($originalKey).'_image'}; @endphp
                        @if($optImg)
                            <div style="margin-top:0.35rem; padding-left:18px;">
                                <img src="{{ asset($optImg) }}" alt="Gambar Opsi {{ $visualKey }}" style="max-height:80px; border-radius:4px; border:1px solid var(--border);">
                            </div>
                        @endif
                    </div>
                    @endforeach
                </div>

                {{-- Pembahasan --}}
                @if($q->pembahasan || $q->explanation_image)
                    <div style="margin-top:1rem; padding:1rem; background:#f1f5f9; border-radius:8px; font-size:0.875rem; color:#334155; line-height:1.6; border:1px solid #e2e8f0;">
                        💡 <strong>Pembahasan:</strong>
                        <div style="margin-top:0.35rem; white-space: pre-wrap;">{!! $q->pembahasan !!}</div>
                        @if($q->explanation_image)
                            <div style="margin-top:0.75rem;">
                                <img src="{{ asset($q->explanation_image) }}" alt="Gambar Pembahasan" style="max-height:160px; border-radius:6px; border:1px solid var(--border);">
                            </div>
                        @endif
                    </div>
                @endif
            </div>
        </div>
        @endforeach
    </div>

    {{-- Actions --}}
    <div class="actions">
        <a href="{{ route('peserta.dashboard') }}" class="btn" style="font-weight:700;">🏠 Dashboard</a>
        <a href="{{ route('peserta.results.index') }}" class="btn btn-primary" style="font-weight:700;">📊 Riwayat Nilai</a>
    </div>
</div>

<script>
function togglePembahasan(i) {
    const body  = document.getElementById('body-' + i);
    const arrow = document.getElementById('arrow-' + i);
    const isOpen = body.classList.contains('open');
    
    // Toggle class
    body.classList.toggle('open');
    arrow.textContent = isOpen ? '▼' : '▲';
}
</script>
</body>
</html>
