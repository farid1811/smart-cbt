@extends('admin.layouts.app')
@section('title', 'Rekap Nilai')
@section('topbar-actions')
    <a href="{{ route('admin.rekap.export') }}?{{ http_build_query(request()->all()) }}" class="btn btn-secondary">Export CSV</a>
@endsection

@section('content')
<form method="GET" class="filter-bar">
    <select name="tryout_package_id" class="form-control" style="max-width:280px;">
        <option value="">Semua Paket Tryout</option>
        @foreach($packages as $p)
            <option value="{{ $p->id }}" {{ request('tryout_package_id') == $p->id ? 'selected' : '' }}>{{ $p->nama }}</option>
        @endforeach
    </select>
    <button type="submit" class="btn btn-secondary">Filter</button>
    @if(request('tryout_package_id'))
        <a href="{{ route('admin.rekap.index') }}" class="btn btn-secondary">Reset</a>
    @endif
</form>

<div class="table-card">
    <div class="table-header">
        <h3 style="font-weight: 700;">Rekap Nilai Peserta ({{ $results->total() }})</h3>
    </div>
    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Peserta</th>
                <th>Paket</th>
                <th>TWK</th>
                <th>TIU</th>
                <th>TKP</th>
                <th>Total</th>
                <th>B / S / K</th>
                <th>Status</th>
                <th>Waktu</th>
            </tr>
        </thead>
        <tbody>
            @forelse($results as $r)
            <tr>
                <td style="color:var(--text-muted);">{{ $results->firstItem() + $loop->index }}</td>
                <td>
                    <strong style="color: var(--text);">{{ $r->user->name }}</strong><br>
                    <small style="color:var(--text-muted);">{{ $r->user->no_peserta ?? $r->user->email }}</small>
                </td>
                <td style="font-size:0.82rem;">{{ Str::limit($r->tryoutPackage->nama, 35) }}</td>
                <td><strong>{{ $r->skor_twk }}</strong></td>
                <td><strong>{{ $r->skor_tiu }}</strong></td>
                <td><strong>{{ $r->skor_tkp }}</strong></td>
                <td>
                    @php
                        $color = $r->skor_total >= 70 ? 'var(--success)' : ($r->skor_total >= 50 ? 'var(--warning)' : 'var(--error)');
                    @endphp
                    <strong style="color: {{ $color }}">{{ $r->skor_total }}%</strong>
                </td>
                <td style="font-size:0.8rem;color:var(--text-muted);font-weight:500;">
                    <span style="color:var(--success);">{{ $r->jumlah_benar }}</span> /
                    <span style="color:var(--error);">{{ $r->jumlah_salah }}</span> /
                    <span>{{ $r->jumlah_kosong }}</span>
                </td>
                <td>
                    @if($r->examSession->status === 'selesai')
                        <span class="badge badge-active">Selesai</span>
                    @else
                        <span class="badge badge-sulit">Timeout</span>
                    @endif
                </td>
                <td style="font-size:0.78rem;color:var(--text-muted);">{{ $r->created_at->format('d/m/Y H:i') }}</td>
            </tr>
            @empty
            <tr><td colspan="10"><div class="empty-state"><p style="font-weight: 500;">Belum ada data rekap nilai.</p></div></td></tr>
            @endforelse
        </tbody>
    </table>
    <div style="border-top: 1px solid var(--border);">{{ $results->links() }}</div>
</div>
@endsection
