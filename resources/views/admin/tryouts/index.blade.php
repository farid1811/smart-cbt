@extends('admin.layouts.app')
@section('title', 'Paket Tryout')
@section('topbar-actions')
    <a href="{{ route('admin.tryouts.create') }}" class="btn btn-primary">+ Buat Paket</a>
@endsection

@section('content')
<div class="table-card">
    <div class="table-header">
        <h3 style="font-weight: 700;">Daftar Paket Tryout</h3>
    </div>
    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Nama Paket</th>
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
            <tr><td colspan="7"><div class="empty-state"><p style="font-weight: 500;">Belum ada paket tryout.</p></div></td></tr>
            @endforelse
        </tbody>
    </table>
    <div>{{ $packages->links() }}</div>
</div>
@endsection
