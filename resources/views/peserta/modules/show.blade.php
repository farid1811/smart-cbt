@extends('peserta.layouts.app')
@section('title', $module->name)

@section('content')
<div style="margin-bottom: 1.5rem;">
    <a href="{{ route('peserta.modules.index') }}" class="btn btn-secondary btn-sm" style="margin-bottom: 1rem;">
        &larr; Kembali ke Daftar Modul
    </a>
    <div style="display:flex; justify-content:space-between; align-items:flex-start; flex-wrap:wrap; gap:1rem;">
        <div>
            <span class="badge" style="background:#eff6ff; color:#1e40af; border-color:#bfdbfe; margin-bottom:0.5rem; font-weight:700;">
                {{ $module->questionCode?->code ?? '—' }}
            </span>
            <div style="font-size:0.75rem; color:var(--text-muted); font-weight:600; margin-bottom:0.5rem;">
                {{ $module->category->name ?? '' }} &rarr; {{ $module->subCategory->name ?? '' }}
            </div>
            <h2 style="font-weight: 800; font-size: 1.35rem; margin: 0 0 0.25rem; color:var(--text);">{{ $module->name }}</h2>
            <p style="font-size: 0.85rem; color: var(--text-muted); margin: 0;">{{ $module->description }}</p>
        </div>
    </div>
</div>

<div style="display:grid; grid-template-columns: 1fr; gap:2rem;">

    {{-- Video Pembelajaran --}}
    @if($module->video_url)
        @php
            $videoEmbedUrl = null;
            if (preg_match('/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/ ]{11})/i', $module->video_url, $matches)) {
                $videoEmbedUrl = "https://www.youtube.com/embed/" . $matches[1];
            } else {
                $videoEmbedUrl = $module->video_url;
            }
        @endphp
        <div class="table-card" style="padding:1.5rem;">
            <h3 style="font-size:0.95rem; font-weight:700; color:var(--text); margin-bottom:1rem; display:flex; align-items:center; gap:0.35rem;">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="var(--primary)" stroke-width="2.5"><polygon points="23 7 16 12 23 17 23 7"/><rect x="1" y="5" width="15" height="14" rx="2" ry="2"/></svg>
                Video Pembahasan Materi
            </h3>
            <div style="position:relative; padding-bottom:56.25%; height:0; overflow:hidden; border-radius:8px; border:1px solid var(--border);">
                <iframe src="{{ $videoEmbedUrl }}" style="position:absolute; top:0; left:0; width:100%; height:100%; border:none;" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
            </div>
        </div>
    @endif

    {{-- PDF Ringkasan Materi --}}
    @if($module->pdf_file)
        <div class="table-card" style="padding:1.5rem;">
            <h3 style="font-size:0.95rem; font-weight:700; color:var(--text); margin-bottom:1rem; display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:0.5rem;">
                <span style="display:flex; align-items:center; gap:0.35rem;">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#ef4444" stroke-width="2.5"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                    Ringkasan Materi (PDF)
                </span>
                <a href="{{ asset($module->pdf_file) }}" target="_blank" class="btn btn-secondary btn-sm" style="font-size:0.75rem;">
                    Buka di Tab Baru
                </a>
            </h3>
            <div style="border:1px solid var(--border); border-radius:8px; overflow:hidden; background:#fafafa;">
                <iframe src="{{ asset($module->pdf_file) }}" style="width:100%; height:600px; border:none; display:block;"></iframe>
            </div>
        </div>
    @endif

</div>
@endsection
