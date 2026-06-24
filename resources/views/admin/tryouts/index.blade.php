@extends('admin.layouts.app')
@section('title', request('type') === 'drill' ? 'Paket Drill Soal' : (request('type') === 'tryout' ? 'Paket Tryout' : 'Semua Paket Ujian'))
@section('topbar-actions')
    <a href="{{ route('admin.tryouts.create', ['type' => request('type')]) }}" class="btn btn-primary">+ Buat Paket</a>
@endsection
@section('content')
<form method="GET" class="filter-bar" style="display:flex; gap:0.5rem; background:#fff; padding:1rem; border-radius:10px; box-shadow:0 1px 3px rgba(0,0,0,0.05); margin-bottom:1.5rem; align-items:center;">
    <input type="hidden" name="type" value="{{ request('type') }}">
    <input type="text" name="search" class="form-control" placeholder="Cari nama paket..." value="{{ request('search') }}" style="flex:1; min-width:200px; max-width:300px;">
    <button type="submit" class="btn btn-secondary">Cari</button>
    @if(request('search'))
        <a href="{{ route('admin.tryouts.index', ['type' => request('type')]) }}" class="btn btn-secondary">Reset</a>
    @endif
</form>

<div class="table-card">
    <div class="table-header">
        <h3 style="font-weight: 700;">
            {{ request('type') === 'drill' ? 'Daftar Paket Drill Soal' : (request('type') === 'tryout' ? 'Daftar Paket Tryout' : 'Daftar Semua Paket Ujian') }}
        </h3>
    </div>
    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Nama Paket</th>
                <th>Program / Kategori</th>
                <th style="text-align:center;">Batas Percobaan</th>
                <th>Durasi</th>
                <th>Jumlah Soal</th>
                <th>Status</th>
                <th>Periode</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($packages as $p)
            <tr>
                <td style="color:var(--text-muted);">{{ $packages->firstItem() + $loop->index }}</td>
                <td>
                    <span class="badge" style="background:{{ $p->jenis_ujian === 'drill' ? '#fdf4ff' : '#eff6ff' }}; color:{{ $p->jenis_ujian === 'drill' ? '#9333ea' : '#1e2a78' }}; border-color:{{ $p->jenis_ujian === 'drill' ? '#e9d5ff' : '#d9deee' }}; font-weight:700; margin-bottom:0.25rem; font-size:0.65rem;">
                        {{ $p->jenis_ujian === 'drill' ? 'Drill Soal' : 'Tryout' }}
                    </span>
                    <span class="badge" style="background:{{ $p->exam_mode === 'seb' ? '#fef2f2' : '#f0fdf4' }}; color:{{ $p->exam_mode === 'seb' ? '#dc2626' : '#16a34a' }}; border-color:{{ $p->exam_mode === 'seb' ? '#fecaca' : '#bbf7d0' }}; font-weight:700; margin-bottom:0.25rem; font-size:0.65rem; margin-left:0.25rem;">
                        {{ $p->exam_mode === 'seb' ? 'SEB' : 'Normal' }}
                    </span><br>
                    <strong style="color: var(--text);">{{ $p->nama }}</strong><br>
                    <small style="color:var(--text-muted);">{{ Str::limit($p->deskripsi, 60) }}</small>
                </td>
                <td>
                    <span style="font-weight:600;color:var(--text);">{{ $p->group }}</span>
                    @if($p->jenis_ujian === 'drill')
                        @if($p->questionCode)
                            <span class="badge-code" style="font-size:0.65rem; padding: 0.1rem 0.35rem; margin-left: 0.25rem; background: #eff6ff; color: #1e40af; border: 1px solid #bfdbfe; font-weight: 700; border-radius: 9999px;">{{ $p->questionCode?->code }}</span>
                        @endif
                        <div style="font-size:0.75rem;color:var(--text-muted);margin-top:0.15rem;">
                            {{ $p->categoryRelation->name ?? $p->category }}
                            @if($p->subCategory)
                                <span style="color:#cbd5e1; margin:0 0.15rem;">&rarr;</span>
                                <span style="color:#475569; font-weight:500;">{{ $p->subCategory->name }}</span>
                            @endif
                        </div>
                    @else
                        <div style="font-size:0.75rem;color:var(--text-muted);">{{ $p->category }}</div>
                    @endif
                </td>
                <td style="text-align:center;">
                    <span class="badge" style="background:var(--background-light);color:var(--text);border-color:var(--border);font-weight:600;">
                        {{ $p->attempt_limit }}x
                    </span>
                </td>
                <td>{{ $p->durasi_menit }} Menit</td>
                <td>{{ $p->questions_count }} Soal</td>
                <td>
                    @if($p->is_active)
                        <span class="badge badge-active">Aktif</span>
                    @else
                        <span class="badge badge-inactive">Nonaktif</span>
                    @endif
                </td>
                <td style="font-size:0.78rem; color:var(--text-muted); font-weight:500;">
                    @if($p->mulai_at)
                        {{ $p->mulai_at->format('d/m/Y') }} — {{ $p->selesai_at?->format('d/m/Y') ?? '∞' }}
                    @else
                        <span>Tidak dibatasi</span>
                    @endif
                </td>
                <td>
                    <div style="display:flex;gap:0.5rem;flex-wrap:wrap;">
                        <a href="{{ route('admin.tryouts.show', $p) }}" class="btn btn-secondary btn-sm" style="font-weight: 600;">Soal</a>
                        @if($p->exam_mode === 'seb')
                            <a href="{{ route('admin.tryouts.sebConfig', $p) }}" class="btn btn-secondary btn-sm" style="font-weight: 600; background-color: #fef2f2; color: #dc2626; border-color: #fecaca;" title="Download SEB Config">SEB Config</a>
                        @endif
                        <a href="{{ route('admin.tryouts.edit', $p) }}" class="btn btn-secondary btn-sm" style="font-weight: 600;">Edit</a>
                        <form method="POST" action="{{ route('admin.tryouts.destroy', $p) }}" onsubmit="return confirm('Hapus paket ini?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm" style="font-weight: 600;">Hapus</button>
                        </form>
                    </div>
                </td>
            </tr>
            @empty
            <tr><td colspan="9"><div class="empty-state"><p style="font-weight: 500;">Belum ada paket.</p></div></td></tr>
            @endforelse
        </tbody>
    </table>
    <div style="padding:1rem;">{{ $packages->links() }}</div>
</div>
@endsection
