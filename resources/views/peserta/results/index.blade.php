@extends('peserta.layouts.app')
@section('title', 'Riwayat Nilai')

@section('content')
<div style="margin-bottom: 2rem;">
    <h2 style="font-size: 1.5rem; font-weight: 700; color: var(--text); letter-spacing: -0.02em;">Riwayat Nilai Ujian</h2>
    <p style="color: var(--text-muted); margin-top: 0.25rem; font-size: 0.875rem;">Berikut adalah seluruh riwayat nilai tryout yang telah Anda ikuti.</p>
</div>

@if($results->isEmpty())
    <div class="empty-state card" style="border-style: dashed;">
        <p style="font-weight: 500;">Anda belum pernah mengikuti tryout.</p>
        <a href="{{ route('peserta.dashboard') }}" class="btn btn-primary" style="margin-top:1rem;">Lihat Tryout Tersedia</a>
    </div>
@else
    <div style="display:flex;flex-direction:column;gap:1.25rem;">
        @foreach($results as $r)
        <div class="card" style="display:flex;align-items:center;gap:1.5rem;flex-wrap:wrap;padding: 1.5rem;">
            {{-- Skor Lingkaran --}}
            @php
                $scoreColor = $r->skor_total >= 70 ? 'var(--success)' : ($r->skor_total >= 50 ? 'var(--warning)' : 'var(--error)');
            @endphp
            <div style="width:70px;height:70px;border-radius:50%;background:conic-gradient(
                {{ $scoreColor }} {{ $r->skor_total * 3.6 }}deg,
                var(--surface2) 0deg
            );display:flex;align-items:center;justify-content:center;flex-shrink:0;box-shadow: 0 2px 8px rgba(0,0,0,0.03);">
                <div style="width:54px;height:54px;background:var(--surface);border-radius:50%;display:flex;align-items:center;justify-content:center;font-weight:800;font-size:0.95rem;color:var(--text);">
                    {{ $r->skor_total }}%
                </div>
            </div>

            <div style="flex:1;min-width:240px;">
                <h3 style="font-size:1.05rem;font-weight:700;color:var(--text);">{{ $r->tryoutPackage->nama }}</h3>
                <p style="font-size:0.82rem;color:var(--text-muted);margin-top:0.35rem;font-weight:500;">
                    {{ $r->created_at->format('d M Y, H:i') }} &bull;
                    @if($r->examSession->status === 'selesai')
                        <span style="color:var(--success);">Selesai</span>
                    @else
                        <span style="color:var(--error);">Timeout</span>
                    @endif
                </p>
                <div style="display:flex;gap:1.5rem;margin-top:0.75rem;font-size:0.82rem;">
                    <span><strong style="color:var(--primary);">TWK:</strong> {{ $r->skor_twk }}%</span>
                    <span><strong style="color:var(--success);">TIU:</strong> {{ $r->skor_tiu }}%</span>
                    <span><strong style="color:var(--warning);">TKP:</strong> {{ $r->skor_tkp }}%</span>
                </div>
            </div>

            <div style="text-align:right;min-width:200px;">
                <div style="font-size:0.82rem;color:var(--text-muted);margin-bottom:0.75rem;font-weight:500;">
                    <span style="color:var(--success);">{{ $r->jumlah_benar }} Benar</span> &bull; 
                    <span style="color:var(--error);">{{ $r->jumlah_salah }} Salah</span> &bull; 
                    <span>{{ $r->jumlah_kosong }} Kosong</span>
                </div>
                <a href="{{ route('peserta.exam.result', $r) }}" class="btn btn-secondary btn-sm" style="font-weight:600;">Lihat Detail &rarr;</a>
            </div>
        </div>
        @endforeach
    </div>
    <div style="margin-top:1.5rem;">{{ $results->links() }}</div>
@endif
@endsection
