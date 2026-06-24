@extends('peserta.layouts.app')
@section('title', 'Masukkan Token Ujian')

@section('content')
<div style="min-height: calc(80vh - 100px); display: flex; align-items: center; justify-content: center; padding: 2rem 0;">
    <div style="width: 100%; max-width: 480px; background: #ffffff; border: 1px solid var(--border); border-radius: var(--radius-lg); box-shadow: 0 10px 25px -5px rgba(0,0,0,0.05); overflow: hidden; padding: 2.25rem;">
        
        {{-- Header --}}
        <div style="text-align: center; margin-bottom: 2rem;">
            <div style="width: 56px; height: 56px; border-radius: 50%; background: var(--primary-soft); display: inline-flex; align-items: center; justify-content: center; color: var(--primary); margin-bottom: 1rem;">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                    <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                </svg>
            </div>
            <h3 style="font-weight: 800; font-size: 1.25rem; color: var(--text); margin: 0 0 0.5rem;">Token Akses Diperlukan</h3>
            <p style="font-size: 0.85rem; color: var(--text-muted); margin: 0; line-height: 1.4;">
                Paket ujian <strong style="color: var(--primary);">{{ $package->nama }}</strong> dilindungi oleh token akses. Silakan hubungi pengawas atau administrator untuk mendapatkan token.
            </p>
        </div>

        {{-- Error Alert --}}
        @if(isset($errorMsg))
            <div style="background: #fff5f5; border: 1px solid #fecaca; border-radius: 8px; padding: 0.75rem 1rem; color: #ef4444; font-size: 0.82rem; margin-bottom: 1.5rem; display: flex; align-items: center; gap: 0.5rem; font-weight: 600;">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="12" cy="12" r="10"></circle>
                    <line x1="12" y1="8" x2="12" y2="12"></line>
                    <line x1="12" y1="16" x2="12.01" y2="16"></line>
                </svg>
                {{ $errorMsg }}
            </div>
        @endif

        {{-- Form --}}
        <form method="GET" action="{{ route('peserta.exam.start', $package) }}">
            <div class="form-group" style="margin-bottom: 1.75rem;">
                <label class="form-label" style="text-align: center; display: block; font-weight: 700; margin-bottom: 0.75rem; font-size: 0.85rem;">MASUKKAN TOKEN</label>
                <input type="text" name="token" class="form-control" placeholder="T O K E N" required autocomplete="off" autofocus
                       style="text-transform: uppercase; text-align: center; font-size: 1.75rem; letter-spacing: 0.3rem; font-weight: 800; padding: 0.75rem; height: auto; border: 2px solid var(--border); border-radius: 8px; font-family: monospace;">
            </div>

            <div style="display: flex; flex-direction: column; gap: 0.75rem;">
                <button type="submit" class="btn btn-primary" style="width: 100%; padding: 0.75rem; font-weight: 700; font-size: 0.9rem; justify-content: center;">
                    Verifikasi & Mulai Ujian
                </button>
                <a href="{{ route('peserta.dashboard') }}" class="btn btn-secondary" style="width: 100%; padding: 0.75rem; font-weight: 600; font-size: 0.85rem; justify-content: center; border-color: transparent;">
                    Kembali ke Dashboard
                </a>
            </div>
        </form>

    </div>
</div>
@endsection
