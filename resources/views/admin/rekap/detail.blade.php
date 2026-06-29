@extends('admin.layouts.app')
@section('title', 'Detail Evaluasi — ' . $result->user->name)

@section('content')
@php
    // Helper format tanggal Indonesia
    $formatTanggal = function($date) {
        if (!$date) return '—';
        $months = [
            1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
            'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
        ];
        $carbonDate = \Carbon\Carbon::parse($date);
        $day = $carbonDate->format('j');
        $month = $months[$carbonDate->month];
        $year = $carbonDate->format('Y');
        $time = $carbonDate->format('H:i');
        return "{$day} {$month} {$year} {$time} WIB";
    };

    // Kalkulasi Durasi Pengerjaan
    $startedAt = $result->examSession->started_at;
    $endedAt = $result->examSession->ended_at;
    $durasiLimit = $result->tryoutPackage->durasi_menit * 60;

    if ($result->examSession->status === 'timeout') {
        $durasiDetik = $durasiLimit;
    } else {
        if ($startedAt && $endedAt) {
            $diff = abs($endedAt->timestamp - $startedAt->timestamp);
            $durasiDetik = min($durasiLimit, $diff);
        } else {
            $durasiDetik = $result->examSession->durasi_detik ?? 0;
            if ($durasiDetik < 0) {
                $durasiDetik = 0;
            }
            $durasiDetik = min($durasiLimit, $durasiDetik);
        }
    }

    $menit = floor($durasiDetik / 60);
    $detik = $durasiDetik % 60;
@endphp

<style>
    .eval-container {
        max-width: 1000px;
        margin: 0 auto;
    }
    .back-btn {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        font-weight: 600;
        color: var(--text-muted);
        text-decoration: none;
        margin-bottom: 1.5rem;
        transition: color 0.2s;
    }
    .back-btn:hover {
        color: var(--primary);
    }
    .grid-2 {
        display: grid;
        grid-template-columns: 1.5fr 1fr;
        gap: 1.5rem;
        margin-bottom: 1.5rem;
    }
    @media (max-width: 768px) {
        .grid-2 {
            grid-template-columns: 1fr;
        }
    }
    .info-card {
        background: var(--surface);
        border: 1px solid var(--border);
        border-radius: var(--radius-lg);
        box-shadow: var(--shadow-sm);
        padding: 1.5rem;
    }
    .info-title {
        font-size: 1rem;
        font-weight: 700;
        color: var(--text);
        margin-bottom: 1rem;
        border-bottom: 1px solid var(--border);
        padding-bottom: 0.5rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }
    .info-row {
        display: flex;
        justify-content: space-between;
        padding: 0.5rem 0;
        border-bottom: 1px dashed var(--border);
        font-size: 0.875rem;
    }
    .info-row:last-child {
        border-bottom: none;
    }
    .info-label {
        color: var(--text-muted);
        font-weight: 500;
    }
    .info-value {
        color: var(--text);
        font-weight: 600;
        text-align: right;
    }
    .eval-badge {
        display: inline-flex;
        padding: 0.2rem 0.5rem;
        border-radius: 99px;
        font-size: 0.75rem;
        font-weight: 700;
    }
    .eval-badge.correct { background: #dcfce7; color: #15803d; }
    .eval-badge.incorrect { background: #fee2e2; color: #b91c1c; }
    .eval-badge.empty { background: #f1f5f9; color: #475569; }
</style>

<div class="eval-container">
    <a href="{{ route('admin.rekap.index') }}" class="back-btn">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/></svg>
        Kembali ke Rekap Nilai
    </a>

    <div class="grid-2">
        {{-- Card Kiri: Informasi Peserta --}}
        <div class="info-card">
            <div class="info-title">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                Informasi Pengerjaan Peserta
            </div>
            <div class="info-row">
                <span class="info-label">Nama Lengkap</span>
                <span class="info-value">{{ $result->user->name }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Username</span>
                <span class="info-value">{{ $result->user->username ?? '-' }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Nomor Peserta</span>
                <span class="info-value">{{ $result->user->no_peserta ?? '-' }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Grup / Kelompok</span>
                <span class="info-value">{{ $result->user->group->name ?? 'Belum ditentukan' }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Paket Tryout</span>
                <span class="info-value" style="color: var(--primary);">{{ $result->tryoutPackage->nama }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Mulai Ujian</span>
                <span class="info-value">{{ $formatTanggal($startedAt) }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Selesai Ujian</span>
                <span class="info-value">{{ $formatTanggal($endedAt) }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Durasi Pengerjaan</span>
                <span class="info-value">{{ $menit }} Menit {{ $detik }} Detik</span>
            </div>
            <div class="info-row">
                <span class="info-label">Status Sesi</span>
                <span class="info-value">
                    @if($result->examSession->status === 'selesai')
                        <span class="eval-badge correct">Selesai</span>
                    @else
                        <span class="eval-badge incorrect">Timeout</span>
                    @endif
                </span>
            </div>
        </div>

        {{-- Card Kanan: Ringkasan & Hasil Evaluasi --}}
        <div class="info-card" style="display: flex; flex-direction: column; justify-content: space-between;">
            <div>
                <div class="info-title" style="border-bottom-color: var(--border);">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="20" x2="18" y2="10"/><line x1="12" y1="20" x2="12" y2="4"/><line x1="6" y1="20" x2="6" y2="14"/><line x1="2" y1="20" x2="22" y2="20"/></svg>
                    Ringkasan Hasil Evaluasi
                </div>
                
                <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 0.5rem; margin-bottom: 1.25rem;">
                    <div style="background: #EFF6FF; border: 1px solid #BFDBFE; padding: 0.5rem; border-radius: 8px; text-align: center;">
                        <div style="font-size: 0.7rem; font-weight: 700; color: #1D4ED8; text-transform: uppercase;">Total Soal</div>
                        <div style="font-size: 1.25rem; font-weight: 800; color: #1E40AF; margin-top: 0.25rem;">{{ $answers->count() }}</div>
                    </div>
                    <div style="background: #ECFDF5; border: 1px solid #A7F3D0; padding: 0.5rem; border-radius: 8px; text-align: center;">
                        <div style="font-size: 0.7rem; font-weight: 700; color: #047857; text-transform: uppercase;">Benar</div>
                        <div style="font-size: 1.25rem; font-weight: 800; color: #065F46; margin-top: 0.25rem;">{{ $result->jumlah_benar }}</div>
                    </div>
                    <div style="background: #FEF2F2; border: 1px solid #FCA5A5; padding: 0.5rem; border-radius: 8px; text-align: center;">
                        <div style="font-size: 0.7rem; font-weight: 700; color: #B91C1C; text-transform: uppercase;">Salah</div>
                        <div style="font-size: 1.25rem; font-weight: 800; color: #991B1B; margin-top: 0.25rem;">{{ $result->jumlah_salah }}</div>
                    </div>
                    <div style="background: #F8FAFC; border: 1px solid #E2E8F0; padding: 0.5rem; border-radius: 8px; text-align: center;">
                        <div style="font-size: 0.7rem; font-weight: 700; color: #475569; text-transform: uppercase;">Kosong</div>
                        <div style="font-size: 1.25rem; font-weight: 800; color: #1E293B; margin-top: 0.25rem;">{{ $result->jumlah_kosong }}</div>
                    </div>
                </div>

                <div style="margin-top: 1rem;">
                    <span class="info-label" style="font-weight: 700; font-size: 0.8rem; text-transform: uppercase; color: var(--text-light); display: block; margin-bottom: 0.5rem;">Kategori Paling Lemah (Kesalahan Terbanyak):</span>
                    @if(!empty($categoryErrors))
                        <ul style="margin: 0; padding-left: 1.25rem; font-size: 0.85rem; color: #991B1B; font-weight: 500; display: flex; flex-direction: column; gap: 0.35rem;">
                            @foreach($categoryErrors as $cat => $count)
                                <li>{{ $cat }} ({{ $count }})</li>
                            @endforeach
                        </ul>
                    @else
                        <div style="font-size: 0.85rem; color: var(--success); font-weight: 600; background: #ECFDF5; padding: 0.5rem 0.75rem; border-radius: 6px; border: 1px solid #A7F3D0; display: inline-flex; align-items: center; gap: 0.35rem;">
                            🎉 Luar biasa! Tidak ada jawaban salah.
                        </div>
                    @endif
                </div>
            </div>

            <div style="margin-top: 1.5rem; padding-top: 1rem; border-top: 1px solid var(--border); display: flex; justify-content: space-between; align-items: center;">
                <span style="font-weight: 700; color: var(--text);">Skor Akhir:</span>
                <span style="font-size: 1.75rem; font-weight: 900; color: var(--primary);">{{ $result->skor_total }}</span>
            </div>
        </div>
    </div>

    {{-- Tabel Detail Jawaban & Kategori --}}
    <div class="table-card">
        <div class="table-header">
            <h3 style="font-weight: 700;">Rincian Jawaban Per Nomor Soal</h3>
        </div>
        <table>
            <thead>
                <tr>
                    <th>No</th>
                    <th>Kode Soal</th>
                    <th>Kategori</th>
                    <th>Tingkat Kesulitan</th>
                    <th>Jawaban Peserta</th>
                    <th>Jawaban Benar</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach($answers as $index => $ans)
                    @php
                        $visualKey = $ans->jawaban;
                        $mapping = $ans->options_mapping;
                        $originalKey = ($mapping && isset($mapping[$visualKey])) ? $mapping[$visualKey] : $visualKey;
                        $isCorrect = $ans->isBenar();
                        $isEmpty = is_null($visualKey);
                    @endphp
                    <tr>
                        <td style="font-weight: 700; color: var(--text-muted);">{{ $index + 1 }}</td>
                        <td>
                            <span class="badge badge-active" style="background: var(--surface2); color: var(--text); border: 1px solid var(--border); font-weight:700;">
                                {{ $ans->question->questionCode->code ?? '—' }}
                            </span>
                        </td>
                        <td style="font-weight: 600; color: var(--text);">{{ $ans->question->category->name ?? '—' }}</td>
                        <td>
                            @php
                                $diffClass = $ans->question->tingkat_kesulitan === 'sulit' ? 'badge-sulit' : ($ans->question->tingkat_kesulitan === 'sedang' ? 'badge-sedang' : 'badge-mudah');
                                $diffText = ucfirst($ans->question->tingkat_kesulitan ?? 'sedang');
                            @endphp
                            <span class="badge {{ $diffClass }}">{{ $diffText }}</span>
                        </td>
                        <td>
                            @if($isEmpty)
                                <span style="color: var(--text-muted); font-style: italic; font-weight: 500;">Kosong</span>
                            @else
                                <strong style="font-size: 1rem; color: var(--text);">{{ $visualKey }}</strong>
                                <span style="font-size:0.75rem; color: var(--text-muted);">({{ $originalKey }})</span>
                            @endif
                        </td>
                        <td>
                            @php
                                // Temukan visual key untuk jawaban benar berdasarkan mapping
                                $correctVisualKey = '—';
                                if ($mapping) {
                                    $flipped = array_flip($mapping);
                                    $correctVisualKey = $flipped[$ans->question->jawaban_benar] ?? $ans->question->jawaban_benar;
                                } else {
                                    $correctVisualKey = $ans->question->jawaban_benar;
                                }
                            @endphp
                            <strong style="font-size: 1rem; color: var(--primary);">{{ $correctVisualKey }}</strong>
                            <span style="font-size:0.75rem; color: var(--text-muted);">({{ $ans->question->jawaban_benar }})</span>
                        </td>
                        <td>
                            @if($isEmpty)
                                <span class="eval-badge empty">Kosong</span>
                            @elseif($isCorrect)
                                <span class="eval-badge correct">✅ Benar</span>
                            @else
                                <span class="eval-badge incorrect">❌ Salah</span>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
