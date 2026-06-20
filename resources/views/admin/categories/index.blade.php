@extends('admin.layouts.app')
@section('title', 'Kategori Soal')
@section('topbar-actions')
    <a href="{{ route('admin.categories.create') }}" class="btn btn-primary">+ Tambah Kategori</a>
@endsection

@section('content')
<div class="table-card">
    <div class="table-header">
        <h3 style="font-weight: 700;">Daftar Kategori</h3>
    </div>
    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Grup</th>
                <th>Kode</th>
                <th>Nama Kategori</th>
                <th>Deskripsi</th>
                <th>Jumlah Soal</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($categories as $i => $cat)
            <tr>
                <td style="color:var(--text-muted);">{{ $i+1 }}</td>
                <td>
                    @if($cat->group)
                        <span class="badge" style="background:#f1f3fb; color:#1e2a78; border-color:#d9deee; font-weight:700;">{{ $cat->group->name }}</span>
                    @else
                        <span style="color:#cbd5e1;">—</span>
                    @endif
                </td>
                <td>
                    <span class="badge badge-{{ strtolower($cat->kode) }}">{{ $cat->kode }}</span>
                </td>
                <td><strong style="color: var(--text);">{{ $cat->name }}</strong></td>
                <td style="color:var(--text-muted); font-size:0.82rem; max-width:300px;">{{ Str::limit($cat->deskripsi, 80) }}</td>
                <td><strong>{{ $cat->questions_count }}</strong> Soal</td>
                <td>
                    <div style="display:flex;gap:0.5rem;">
                        <a href="{{ route('admin.categories.edit', $cat) }}" class="btn btn-secondary btn-sm" style="font-weight: 600;">Edit</a>
                        <form method="POST" action="{{ route('admin.categories.destroy', $cat) }}" onsubmit="return confirm('Hapus kategori ini?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm" style="font-weight: 600;">Hapus</button>
                        </form>
                    </div>
                </td>
            </tr>
            @empty
            <tr><td colspan="6"><div class="empty-state"><p style="font-weight: 500;">Belum ada kategori.</p></div></td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
