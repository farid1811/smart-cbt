@extends('admin.layouts.app')
@section('title', 'Data Peserta')

@section('topbar-actions')
    <a href="{{ route('admin.peserta.create') }}" class="btn btn-primary">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
        Tambah Peserta
    </a>
@endsection

@section('content')

{{-- Filter Bar --}}
<form method="GET" class="filter-bar">
    <input type="text" name="search" class="form-control" placeholder="Cari nama, email, no. HP..." value="{{ request('search') }}">
    <select name="group_id" class="form-control" style="max-width:160px;">
        <option value="">Semua Grup</option>
        @foreach($groups as $group)
            <option value="{{ $group->id }}" {{ request('group_id') == $group->id ? 'selected' : '' }}>{{ $group->name }}</option>
        @endforeach
    </select>
    <select name="status" class="form-control" style="max-width:160px;">
        <option value="">Semua Status</option>
        <option value="aktif"    {{ request('status') === 'aktif'    ? 'selected' : '' }}>Aktif</option>
        <option value="nonaktif" {{ request('status') === 'nonaktif' ? 'selected' : '' }}>Nonaktif</option>
    </select>
    <button type="submit" class="btn btn-secondary">Filter</button>
    @if(request()->hasAny(['search','status','group_id']))
        <a href="{{ route('admin.peserta.index') }}" class="btn btn-secondary">Reset</a>
    @endif
</form>

<div class="table-card">
    <div class="table-header">
        <h3 style="font-weight:700;">Data Peserta ({{ $peserta->total() }})</h3>
    </div>
    <table>
        <thead>
            <tr>
                <th style="width:40px;">#</th>
                <th>Nama Peserta</th>
                <th>Username</th>
                <th>Grup</th>
                <th>Kategori</th>
                <th>Paket Ditugaskan</th>
                <th style="width:90px;text-align:center;">Status</th>
                <th style="width:180px;text-align:center;">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($peserta as $i => $p)
            <tr>
                <td style="color:var(--text-muted);font-size:0.8rem;">{{ $peserta->firstItem() + $i }}</td>
                <td>
                    <div style="display:flex;align-items:center;gap:0.6rem;">
                        <div style="width:34px;height:34px;border-radius:50%;background:var(--primary-light);color:var(--primary);display:flex;align-items:center;justify-content:center;font-weight:700;font-size:0.85rem;flex-shrink:0;">
                            {{ strtoupper(substr($p->name, 0, 1)) }}
                        </div>
                        <div>
                            <div style="font-weight:600;color:var(--text);">{{ $p->name }}</div>
                            <div style="font-size:0.75rem;color:var(--text-muted);">{{ $p->email }} &middot; {{ $p->no_hp ?? '—' }}</div>
                        </div>
                    </div>
                </td>
                <td><code style="font-weight:600;color:var(--primary);">{{ $p->username }}</code></td>
                <td>
                    @if($p->group)
                        <span class="badge badge-info" style="font-weight:600;">{{ $p->group->name }}</span>
                    @else
                        <span style="color:#cbd5e1;">—</span>
                    @endif
                </td>
                <td>
                    @if($p->category)
                        <span class="badge badge-warning" style="font-weight:600;background:#fffbeb;color:#d97706;border:1px solid #fde68a;">{{ $p->category }}</span>
                    @else
                        <span style="color:#cbd5e1;">—</span>
                    @endif
                </td>
                <td>
                    @if($p->assignedPackage)
                        <span class="badge" style="background:#f0fdf4;color:#16a34a;border:1px solid #bbf7d0;font-weight:600;font-size:0.75rem;padding:3px 8px;">
                            [{{ strtoupper($p->assignedPackage->jenis_ujian) }}] {{ $p->assignedPackage->nama }}
                        </span>
                    @else
                        <span style="color:#cbd5e1;font-size:0.8rem;">Semua Paket</span>
                    @endif
                </td>

                <td style="text-align:center;">
                    @if($p->is_active)
                        <span class="badge badge-active">Aktif</span>
                    @else
                        <span class="badge" style="background:#fee2e2;color:#dc2626;border:1px solid #fecaca;">Nonaktif</span>
                    @endif
                </td>
                <td>
                    <div style="display:flex;align-items:center;justify-content:center;gap:0.4rem;flex-wrap:wrap;">

                        {{-- Edit --}}
                        <a href="{{ route('admin.peserta.edit', $p) }}"
                           style="display:inline-flex;align-items:center;gap:4px;padding:4px 10px;border-radius:6px;background:#eff6ff;color:#2563eb;border:1px solid #bfdbfe;font-size:0.78rem;font-weight:600;text-decoration:none;"
                           title="Edit">
                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                            Edit
                        </a>

                        {{-- Toggle Status --}}
                        <form method="POST" action="{{ route('admin.peserta.toggleStatus', $p) }}" style="display:inline;">
                            @csrf
                            <button type="submit"
                                style="display:inline-flex;align-items:center;gap:4px;padding:4px 10px;border-radius:6px;background:{{ $p->is_active ? '#fffbeb' : '#f0fdf4' }};color:{{ $p->is_active ? '#d97706' : '#16a34a' }};border:1px solid {{ $p->is_active ? '#fde68a' : '#bbf7d0' }};font-size:0.78rem;font-weight:600;cursor:pointer;"
                                title="{{ $p->is_active ? 'Nonaktifkan' : 'Aktifkan' }}">
                                @if($p->is_active)
                                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><line x1="8" y1="12" x2="16" y2="12"/></svg>
                                    Nonaktif
                                @else
                                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="16"/><line x1="8" y1="12" x2="16" y2="12"/></svg>
                                    Aktifkan
                                @endif
                            </button>
                        </form>

                        {{-- Reset Password --}}
                        <button type="button"
                            onclick="openResetModal({{ $p->id }}, '{{ addslashes($p->name) }}')"
                            style="display:inline-flex;align-items:center;gap:4px;padding:4px 10px;border-radius:6px;background:#fdf4ff;color:#9333ea;border:1px solid #e9d5ff;font-size:0.78rem;font-weight:600;cursor:pointer;"
                            title="Reset Password">
                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                            Reset PW
                        </button>

                        {{-- Hapus --}}
                        <form method="POST" action="{{ route('admin.peserta.destroy', $p) }}" style="display:inline;"
                              onsubmit="return confirm('Hapus peserta {{ addslashes($p->name) }}? Semua data ujiannya juga akan terhapus.')">
                            @csrf @method('DELETE')
                            <button type="submit"
                                style="display:inline-flex;align-items:center;gap:4px;padding:4px 10px;border-radius:6px;background:#fff1f2;color:#e11d48;border:1px solid #fecdd3;font-size:0.78rem;font-weight:600;cursor:pointer;"
                                title="Hapus">
                                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/><path d="M10 11v6"/><path d="M14 11v6"/><path d="M9 6V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2"/></svg>
                                Hapus
                            </button>
                        </form>

                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="8">
                    <div class="empty-state">
                        <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="#94a3b8" stroke-width="1.5"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                        <p style="font-weight:600;margin-top:0.75rem;">Belum ada data peserta.</p>
                        <a href="{{ route('admin.peserta.create') }}" class="btn btn-primary" style="margin-top:0.5rem;">Tambah Peserta Pertama</a>
                    </div>
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
    <div style="border-top:1px solid var(--border);">{{ $peserta->appends(request()->query())->links() }}</div>
</div>

{{-- Modal Reset Password --}}
<div id="resetModal" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,0.45);z-index:9000;display:none;align-items:center;justify-content:center;">
    <div style="background:#fff;border-radius:12px;width:100%;max-width:400px;padding:1.75rem;box-shadow:0 20px 60px rgba(0,0,0,0.15);margin:1rem;">
        <h3 style="font-size:1rem;font-weight:700;margin:0 0 0.25rem;">Reset Password Peserta</h3>
        <p id="resetModalName" style="font-size:0.85rem;color:var(--text-muted);margin:0 0 1.25rem;"></p>

        <form id="resetForm" method="POST">
            @csrf
            <div class="form-group">
                <label class="form-label">Password Baru</label>
                <input type="password" name="password" class="form-control" placeholder="Minimal 6 karakter" required minlength="6">
            </div>
            <div class="form-group">
                <label class="form-label">Konfirmasi Password</label>
                <input type="password" name="password_confirmation" class="form-control" placeholder="Ulangi password" required minlength="6">
            </div>
            <div style="display:flex;gap:0.5rem;justify-content:flex-end;margin-top:1rem;">
                <button type="button" onclick="closeResetModal()" class="btn btn-secondary">Batal</button>
                <button type="submit" class="btn btn-primary">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                    Reset Password
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
function openResetModal(id, name) {
    document.getElementById('resetModal').style.display = 'flex';
    document.getElementById('resetModalName').textContent = 'Peserta: ' + name;
    document.getElementById('resetForm').action = '/admin/peserta/' + id + '/reset-password';
}
function closeResetModal() {
    document.getElementById('resetModal').style.display = 'none';
}
document.getElementById('resetModal').addEventListener('click', function(e) {
    if (e.target === this) closeResetModal();
});
</script>
@endpush
@endsection
