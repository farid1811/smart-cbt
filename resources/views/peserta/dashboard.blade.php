@extends('peserta.layouts.app')
@section('title', 'Dashboard')

@push('styles')
<style>
.tryout-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
    gap: 1.25rem;
    margin-bottom: 2rem;
}

.tryout-card {
    background: var(--surface);
    border: 1px solid var(--border);
    border-radius: var(--radius-lg);
    padding: 1.5rem;
    display: flex;
    flex-direction: column;
    gap: 1rem;
    box-shadow: var(--shadow-sm);
    transition: all var(--transition);
}

.tryout-card:hover {
    border-color: var(--primary-mid);
    box-shadow: var(--shadow-md);
    transform: translateY(-2px);
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
    font-weight: 500;
}

.riwayat-table-card {
    background: var(--surface);
    border: 1px solid var(--border);
    border-radius: var(--radius-lg);
    overflow: hidden;
    box-shadow: var(--shadow-sm);
    margin-bottom: 2rem;
}

@media (max-width: 560px) {
    .tryout-grid { grid-template-columns: 1fr; }
}
</style>
@endpush

@section('content')

{{-- Welcome --}}
<div style="margin-bottom:2rem;">
    <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:1rem;flex-wrap:wrap;">
        <div>
            <h2 style="font-size:1.375rem;font-weight:800;color:var(--text);letter-spacing:-0.02em;line-height:1.2;">
                Halo, {{ explode(' ', $user->name)[0] }} 👋
            </h2>
            <p style="color:var(--text-muted);margin-top:0.375rem;font-size:0.875rem;">
                Selamat datang di Smart CBT. Pilih paket tryout untuk mulai ujian.
                @if($user->no_peserta)
                    &nbsp;·&nbsp; No. Peserta: <strong style="color:var(--primary);">{{ $user->no_peserta }}</strong>
                @endif
            </p>
        </div>
        <a href="{{ route('peserta.results.index') }}" class="btn btn-secondary btn-sm" style="flex-shrink:0;">
            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="18" y1="20" x2="18" y2="10"/><line x1="12" y1="20" x2="12" y2="4"/><line x1="6" y1="20" x2="6" y2="14"/><line x1="2" y1="20" x2="22" y2="20"/></svg>
            Riwayat Nilai
        </a>
    </div>
</div>

{{-- Paket Ujian Tersedia --}}
@php
    $tryoutPackages = $tryoutsAktif->filter(fn($t) => $t->jenis_ujian === 'tryout' || !$t->jenis_ujian);
    $drillPackages = $tryoutsAktif->filter(fn($t) => $t->jenis_ujian === 'drill');
@endphp

{{-- Section 1: Paket Tryout Akbar --}}
<div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:1rem;">
    <h3 style="font-size:1rem;font-weight:700;color:var(--text);display:flex;align-items:center;gap:0.5rem;">
        <span style="background:var(--primary);width:4px;height:16px;border-radius:2px;display:inline-block;"></span>
        Paket Tryout Akbar
    </h3>
    <span style="font-size:0.8rem;color:var(--text-muted);">{{ $tryoutPackages->count() }} paket aktif</span>
</div>

@if($tryoutPackages->isEmpty())
    <div style="background:var(--surface);border:1px dashed var(--border);border-radius:var(--radius-lg);padding:2.5rem 1.5rem;text-align:center;margin-bottom:2.5rem;">
        <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="#94a3b8" stroke-width="1.5" style="margin:0 auto 0.75rem;display:block;"><rect x="2" y="7" width="20" height="14" rx="2"/><path d="M16 7V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v2"/></svg>
        <p style="font-weight:600;color:var(--text-muted);font-size:0.85rem;">Belum ada paket Tryout Akbar yang tersedia.</p>
    </div>
@else
    <div class="tryout-grid">
        @foreach($tryoutPackages as $tryout)
        <div class="tryout-card">
            {{-- Header --}}
            <div style="display:flex;justify-content:space-between;align-items:flex-start;gap:0.75rem;">
                <div style="flex:1;min-width:0;">
                    <h4 style="font-size:0.9375rem;font-weight:700;color:var(--text);line-height:1.3;">{{ $tryout->nama }}</h4>
                    @if($tryout->deskripsi)
                        <p style="font-size:0.8rem;color:var(--text-muted);margin-top:0.375rem;line-height:1.6;">{{ Str::limit($tryout->deskripsi, 90) }}</p>
                    @endif
                </div>
                <span class="badge badge-active" style="flex-shrink:0;background:var(--primary-soft);color:var(--primary);font-weight:600;">Tryout</span>
            </div>

            {{-- Meta info --}}
            <div class="tryout-meta">
                <div class="meta-item">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/></svg>
                    {{ $tryout->durasi_menit }} Menit
                </div>
                <div class="meta-item">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>
                    {{ $tryout->questions()->count() }} Soal
                </div>
                @if($tryout->selesai_at)
                    <div class="meta-item" style="margin-left:auto;">
                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                        s/d {{ $tryout->selesai_at->format('d M') }}
                    </div>
                @endif
            </div>

            {{-- CTA --}}
            <a href="{{ route('peserta.exam.start', $tryout) }}" class="btn btn-primary" style="justify-content:center;width:100%;font-size:0.9375rem;">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polygon points="5 3 19 12 5 21 5 3"/></svg>
                Mulai Ujian
            </a>
        </div>
        @endforeach
    </div>
@endif

{{-- Section 2: Latihan Drill Soal --}}
<div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:1rem;margin-top:2.5rem;">
    <h3 style="font-size:1rem;font-weight:700;color:var(--text);display:flex;align-items:center;gap:0.5rem;">
        <span style="background:var(--success);width:4px;height:16px;border-radius:2px;display:inline-block;"></span>
        Latihan Drill Soal
    </h3>
    <span style="font-size:0.8rem;color:var(--text-muted);">{{ $drillPackages->count() }} paket aktif</span>
</div>

@if($drillPackages->isEmpty())
    <div style="background:var(--surface);border:1px dashed var(--border);border-radius:var(--radius-lg);padding:2.5rem 1.5rem;text-align:center;margin-bottom:2.5rem;">
        <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="#94a3b8" stroke-width="1.5" style="margin:0 auto 0.75rem;display:block;"><rect x="2" y="7" width="20" height="14" rx="2"/><path d="M16 7V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v2"/></svg>
        <p style="font-weight:600;color:var(--text-muted);font-size:0.85rem;">Belum ada paket Drill Soal yang tersedia.</p>
    </div>
@else
    <div class="tryout-grid">
        @foreach($drillPackages as $tryout)
        <div class="tryout-card">
            {{-- Header --}}
            <div style="display:flex;justify-content:space-between;align-items:flex-start;gap:0.75rem;">
                <div style="flex:1;min-width:0;">
                    <h4 style="font-size:0.9375rem;font-weight:700;color:var(--text);line-height:1.3;">{{ $tryout->nama }}</h4>
                    @if($tryout->deskripsi)
                        <p style="font-size:0.8rem;color:var(--text-muted);margin-top:0.375rem;line-height:1.6;">{{ Str::limit($tryout->deskripsi, 90) }}</p>
                    @endif
                </div>
                <span class="badge badge-active" style="flex-shrink:0;background:var(--success-soft);color:var(--success);font-weight:600;">Drill Soal</span>
            </div>

            {{-- Meta info --}}
            <div class="tryout-meta">
                <div class="meta-item">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/></svg>
                    {{ $tryout->durasi_menit }} Menit
                </div>
                <div class="meta-item">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>
                    {{ $tryout->questions()->count() }} Soal
                </div>
                @if($tryout->selesai_at)
                    <div class="meta-item" style="margin-left:auto;">
                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                        s/d {{ $tryout->selesai_at->format('d M') }}
                    </div>
                @endif
            </div>

            {{-- CTA --}}
            <a href="{{ route('peserta.exam.start', $tryout) }}" class="btn btn-primary" style="justify-content:center;width:100%;font-size:0.9375rem;">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polygon points="5 3 19 12 5 21 5 3"/></svg>
                Mulai Ujian
            </a>
        </div>
        @endforeach
    </div>
@endif

{{-- Riwayat Singkat --}}
@if($riwayat->isNotEmpty())
<div>
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:1rem;">
        <h3 style="font-size:0.9375rem;font-weight:700;color:var(--text);">Riwayat Ujian Terakhir</h3>
        <a href="{{ route('peserta.results.index') }}" class="btn btn-secondary btn-sm">
            Lihat Semua
            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="9 18 15 12 9 6"/></svg>
        </a>
    </div>

    <div class="riwayat-table-card">
        <div style="overflow-x:auto;">
            <table>
                <thead>
                    <tr>
                        <th>Paket Tryout</th>
                        <th style="text-align:center;">Skor</th>
                        <th>Benar / Salah / Kosong</th>
                        <th>Tanggal</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($riwayat as $r)
                    <tr>
                        <td style="font-weight:600;">{{ $r->tryoutPackage->nama }}</td>
                        <td style="text-align:center;">
                            @php
                                $score = $r->skor_total;
                                $scoreColor = $score >= 70 ? '#059669' : ($score >= 50 ? '#D97706' : '#DC2626');
                            @endphp
                            <span style="font-weight:800;font-size:1.0625rem;color:{{ $scoreColor }};letter-spacing:-0.02em;">{{ $score }}</span>
                        </td>
                        <td>
                            <span style="color:#059669;font-weight:600;">{{ $r->jumlah_benar }}</span>
                            <span style="color:var(--text-light);margin:0 2px;">/</span>
                            <span style="color:#DC2626;font-weight:600;">{{ $r->jumlah_salah }}</span>
                            <span style="color:var(--text-light);margin:0 2px;">/</span>
                            <span style="color:var(--text-muted);">{{ $r->jumlah_kosong }}</span>
                        </td>
                        <td style="color:var(--text-muted);font-size:0.75rem;white-space:nowrap;">{{ $r->created_at->format('d M Y') }}</td>
                        <td>
                            <a href="{{ route('peserta.exam.result', $r) }}" class="btn btn-secondary btn-sm">
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
