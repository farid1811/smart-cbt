<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hasil Ujian — {{ $result->tryoutPackage->nama }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
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
        .brand-icon {
            width: 32px;
            height: 32px;
            background: linear-gradient(135deg, var(--primary), var(--primary-light));
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 2px 8px rgba(37,99,235,0.15);
        }
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

        .score-val { font-size: 2.2rem; font-weight: 800; }
        .score-lbl { font-size: 0.75rem; color: var(--text-muted); font-weight: 500; }

        .result-hero h2 { font-size: 1.4rem; font-weight: 700; margin-bottom: 0.35rem; color: var(--text); }
        .result-hero p  { color: var(--text-muted); font-size: 0.875rem; }

        /* Category scores */
        .cat-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 1.25rem; margin-bottom: 2rem; }

        .cat-card {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 12px;
            padding: 1.25rem;
            text-align: center;
            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.05), 0 1px 2px 0 rgba(0, 0, 0, 0.03);
        }

        .cat-name { font-size: 0.7rem; color: var(--text-muted); margin-bottom: 0.35rem; text-transform: uppercase; letter-spacing: 0.05em; font-weight: 600; }
        .cat-kode { font-size: 0.9rem; font-weight: 800; margin-bottom: 0.75rem; }
        .cat-score { font-size: 1.8rem; font-weight: 800; }
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
            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.05), 0 1px 2px 0 rgba(0, 0, 0, 0.03);
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
            box-shadow: 0 1px 2px 0 rgba(0,0,0,0.02);
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
            max-height: 800px;
            padding: 1rem 1.25rem;
            border-top: 1px solid var(--border);
        }

        .opsi-row {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.5rem 0.75rem;
            border-radius: 6px;
            font-size: 0.875rem;
            margin-bottom: 0.35rem;
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
            font-family: 'Inter', sans-serif;
        }

        .btn-primary { background: var(--primary); color: #fff; border-color: var(--primary); }
        .btn-primary:hover { background: var(--primary-dark); }
    </style>
</head>
<body>

<header class="topbar">
    <a href="{{ route('peserta.dashboard') }}" class="brand" style="display:flex;align-items:center;text-decoration:none;">
        <img src="{{ asset('images/logo-plano.jpg') }}" alt="Bimbel Plano" style="max-height: 34px; width: auto; filter: drop-shadow(0 1px 2px rgba(30,42,120,0.05));">
    </a>
    <span style="font-size:0.85rem;color:var(--text-muted);font-weight:500;">{{ auth()->user()->name }}</span>
</header>

<div class="page">

    {{-- Hero Score --}}
    <div class="result-hero">
        @php
            $pct   = $result->skor_total;
            $color = $pct >= 70 ? '#10b981' : ($pct >= 50 ? '#f59e0b' : '#f43f5e');
            $circ  = 2 * M_PI * 54;
            $offset = $circ * (1 - $pct / 100);
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
                <div class="score-val" style="color:{{ $color }};">{{ $pct }}%</div>
                <div class="score-lbl">Skor Total</div>
            </div>
        </div>

        <h2>
            @if($pct >= 70) 🎉 Luar Biasa!
            @elseif($pct >= 50) 👍 Cukup Baik
            @else 💪 Perlu Latihan Lagi
            @endif
        </h2>
        <p>{{ $result->tryoutPackage->nama }} •
            @if($result->examSession->status === 'selesai')
                Selesai tepat waktu
            @else
                Waktu habis (auto-submit)
            @endif
        </p>
    </div>

    {{-- Per-kategori --}}
    <div class="cat-grid">
        @if($result->category_scores && count($result->category_scores) > 0)
            @foreach($result->category_scores as $catId => $data)
            @php
                $clr = '#1E2A78';
                if ($data['kode'] === 'TWK') $clr = '#4f46e5';
                elseif ($data['kode'] === 'TIU') $clr = '#10b981';
                elseif ($data['kode'] === 'TKP') $clr = '#f59e0b';
            @endphp
            <div class="cat-card">
                <div class="cat-name">{{ $data['name'] }}</div>
                <div class="cat-kode" style="color:{{ $clr }};">{{ $data['kode'] }}</div>
                <div class="cat-score" style="color:{{ $clr }};">{{ $data['score'] }}%</div>
                <div class="cat-bar"><div class="cat-bar-fill" style="width:{{ $data['score'] }}%;background:{{ $clr }};"></div></div>
            </div>
            @endforeach
        @else
            @foreach([['TWK','Tes Wawasan Kebangsaan','skor_twk','#4f46e5'],['TIU','Tes Intelegensia Umum','skor_tiu','#10b981'],['TKP','Tes Karakteristik Pribadi','skor_tkp','#f59e0b']] as [$kode,$nama,$field,$clr])
            <div class="cat-card">
                <div class="cat-name">{{ $nama }}</div>
                <div class="cat-kode" style="color:{{ $clr }};">{{ $kode }}</div>
                <div class="cat-score" style="color:{{ $clr }};">{{ $result->$field }}%</div>
                <div class="cat-bar"><div class="cat-bar-fill" style="width:{{ $result->$field }}%;background:{{ $clr }};"></div></div>
            </div>
            @endforeach
        @endif
    </div>

    {{-- Summary --}}
    <div class="summary-cards">
        <div class="sum-card">
            <div class="sum-val" style="color:#6ee7b7;">{{ $result->jumlah_benar }}</div>
            <div class="sum-lbl">✅ Jawaban Benar</div>
        </div>
        <div class="sum-card">
            <div class="sum-val" style="color:#fda4af;">{{ $result->jumlah_salah }}</div>
            <div class="sum-lbl">❌ Jawaban Salah</div>
        </div>
        <div class="sum-card">
            <div class="sum-val" style="color:var(--text-muted);">{{ $result->jumlah_kosong }}</div>
            <div class="sum-lbl">⬜ Tidak Dijawab</div>
        </div>
    </div>

    {{-- Pembahasan --}}
    <div class="pembahasan-section">
        <h3>📖 Pembahasan Soal</h3>

        @foreach($result->examSession->answers->sortBy(fn($a) => $a->question->category->kode) as $i => $answer)
        @php
            $q       = $answer->question;
            $benar   = $answer->isBenar();
            $kosong  = is_null($answer->jawaban);
            $status  = $kosong ? 'kosong' : ($benar ? 'benar' : 'salah');
            
            $mapping = $answer->options_mapping;
            $visualKeys = $mapping ? array_keys($mapping) : ['A', 'B', 'C', 'D', 'E'];
            
            // Find visual correct option key
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
                <span class="badge badge-{{ strtolower($q->category->kode) }}" style="font-size:0.7rem;padding:0.15rem 0.5rem;">{{ $q->category->kode }}</span>
                <span style="flex:1;font-size:0.83rem;">{{ Str::limit($q->soal, 80) }}</span>
                <span style="font-size:0.75rem;color:var(--text-muted);">
                    @if($kosong) ⬜ Kosong
                    @elseif($benar) ✅ Benar
                    @else ❌ Salah (Pilihan: {{ $answer->jawaban }}, Kunci: {{ $visualCorrectKey }})
                    @endif
                </span>
                <span style="font-size:0.8rem;color:var(--text-muted);" id="arrow-{{ $i }}">▼</span>
            </div>
            <div class="soal-item-body" id="body-{{ $i }}">
                <p style="font-size:0.875rem;margin-bottom:0.75rem;line-height:1.6;">{{ $q->soal }}</p>
                
                @if($q->image)
                    <div style="margin-bottom: 1.25rem;">
                        <img src="{{ asset($q->image) }}" alt="Gambar Soal" style="max-width: 100%; max-height: 350px; height: auto; border-radius: 8px; border: 1px solid var(--border); box-shadow: var(--shadow-sm);">
                    </div>
                @endif

                @foreach($visualKeys as $visualKey)
                @php
                    $originalKey = ($mapping && isset($mapping[$visualKey])) ? $mapping[$visualKey] : $visualKey;
                    $opsiText = $q->{'opsi_'.strtolower($originalKey)};
                    if (!$opsiText) continue;

                    $isBenar = $originalKey === $q->jawaban_benar;
                    $isPilihan = $visualKey === $answer->jawaban;
                @endphp
                <div class="opsi-row {{ $isBenar ? 'opsi-correct' : ($isPilihan && !$isBenar ? 'opsi-wrong' : '') }}">
                    <strong style="min-width:18px;">{{ $visualKey }}.</strong>
                    <span>{{ $opsiText }}</span>
                    @if($isBenar) <span style="margin-left:auto;font-size:0.7rem;color:#10b981;font-weight:600;">✓ Benar</span> @endif
                    @if($isPilihan && !$isBenar) <span style="margin-left:auto;font-size:0.7rem;color:#ef4444;font-weight:600;">✗ Pilihan Anda</span> @endif
                </div>
                @endforeach
                @if($q->pembahasan)
                    <div style="margin-top:0.75rem;padding:0.75rem;background:var(--surface2);border-radius:8px;font-size:0.82rem;color:var(--text-muted);line-height:1.6;">
                        💡 <strong>Pembahasan:</strong> {{ $q->pembahasan }}
                    </div>
                @endif
            </div>
        </div>
        @endforeach
    </div>

    {{-- Actions --}}
    <div class="actions">
        <a href="{{ route('peserta.dashboard') }}" class="btn">🏠 Dashboard</a>
        <a href="{{ route('peserta.results.index') }}" class="btn btn-primary">📊 Riwayat Nilai</a>
    </div>
</div>

<script>
function togglePembahasan(i) {
    const body  = document.getElementById('body-' + i);
    const arrow = document.getElementById('arrow-' + i);
    const isOpen = body.classList.contains('open');
    body.classList.toggle('open');
    arrow.textContent = isOpen ? '▼' : '▲';
}
</script>
</body>
</html>
