@extends('admin.layouts.app')
@section('title', 'Dashboard')

@section('content')

{{-- Page header --}}
<div style="margin-bottom:1.5rem;">
    <h2 style="font-size:1.125rem;font-weight:800;color:var(--text);letter-spacing:-0.02em;">Selamat datang, {{ explode(' ', auth()->user()->name)[0] }} 👋</h2>
    <p style="font-size:0.8125rem;color:var(--text-muted);margin-top:0.25rem;">Berikut adalah ringkasan aktivitas Smart CBT Anda hari ini.</p>
</div>

{{-- Stats Grid --}}
<div class="stats-grid">
    <a href="{{ route('admin.questions.index') }}" class="stat-card" style="text-decoration:none;">
        <div class="stat-icon purple">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>
        </div>
        <div class="stat-info">
            <div class="stat-val">{{ $stats['total_soal'] }}</div>
            <div class="stat-label">Total Soal</div>
        </div>
    </a>

    <a href="{{ route('admin.categories.index') }}" class="stat-card" style="text-decoration:none;">
        <div class="stat-icon green">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20.59 13.41l-7.17 7.17a2 2 0 0 1-2.83 0L2 12V2h10l8.59 8.59a2 2 0 0 1 0 2.82z"/><line x1="7" y1="7" x2="7.01" y2="7"/></svg>
        </div>
        <div class="stat-info">
            <div class="stat-val">{{ $stats['total_kategori'] }}</div>
            <div class="stat-label">Kategori</div>
        </div>
    </a>

    <a href="{{ route('admin.tryouts.index') }}" class="stat-card" style="text-decoration:none;">
        <div class="stat-icon blue">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="7" width="20" height="14" rx="2"/><path d="M16 7V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v2"/></svg>
        </div>
        <div class="stat-info">
            <div class="stat-val">{{ $stats['total_paket'] }}</div>
            <div class="stat-label">Paket Tryout</div>
        </div>
    </a>

    <a href="{{ route('admin.peserta.index') }}" class="stat-card" style="text-decoration:none;">
        <div class="stat-icon orange">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
        </div>
        <div class="stat-info">
            <div class="stat-val">{{ $stats['total_peserta'] }}</div>
            <div class="stat-label">Peserta <span style="color:#16a34a;font-size:0.6875rem;font-weight:600;">({{ $stats['peserta_aktif'] }} aktif)</span></div>
        </div>
    </a>

    <div class="stat-card">
        <div class="stat-icon teal">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/></svg>
        </div>
        <div class="stat-info">
            <div class="stat-val">{{ $stats['total_ujian'] }}</div>
            <div class="stat-label">Total Ujian</div>
        </div>
    </div>

    <a href="{{ route('admin.rekap.index') }}" class="stat-card" style="text-decoration:none;">
        <div class="stat-icon rose">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
        </div>
        <div class="stat-info">
            <div class="stat-val">{{ $stats['ujian_selesai'] }}</div>
            <div class="stat-label">Ujian Selesai</div>
        </div>
    </a>
</div>

{{-- Quick Actions --}}
<div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(200px,1fr));gap:0.75rem;margin-bottom:1.75rem;">
    <a href="{{ route('admin.peserta.create') }}" class="btn btn-secondary" style="justify-content:flex-start;padding:0.75rem 1rem;border-radius:var(--radius);font-size:0.8125rem;">
        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="8.5" cy="7" r="4"/><line x1="20" y1="8" x2="20" y2="14"/><line x1="23" y1="11" x2="17" y2="11"/></svg>
        Tambah Peserta
    </a>
    <a href="{{ route('admin.questions.create') }}" class="btn btn-secondary" style="justify-content:flex-start;padding:0.75rem 1rem;border-radius:var(--radius);font-size:0.8125rem;">
        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
        Tambah Soal
    </a>
    <a href="{{ route('admin.tryouts.create') }}" class="btn btn-secondary" style="justify-content:flex-start;padding:0.75rem 1rem;border-radius:var(--radius);font-size:0.8125rem;">
        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><rect x="2" y="7" width="20" height="14" rx="2"/><path d="M16 7V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v2"/></svg>
        Buat Paket Tryout
    </a>
    <a href="{{ route('admin.rekap.index') }}" class="btn btn-secondary" style="justify-content:flex-start;padding:0.75rem 1rem;border-radius:var(--radius);font-size:0.8125rem;">
        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="18" y1="20" x2="18" y2="10"/><line x1="12" y1="20" x2="12" y2="4"/><line x1="6" y1="20" x2="6" y2="14"/></svg>
        Lihat Rekap Nilai
    </a>
</div>

{{-- Recent Sessions Table --}}
<div class="table-card">
    <div class="table-header">
        <div>
            <h3>Aktivitas Ujian Terbaru</h3>
            <p style="font-size:0.75rem;color:var(--text-muted);margin-top:2px;">10 sesi ujian paling baru</p>
        </div>
        <a href="{{ route('admin.rekap.index') }}" class="btn btn-secondary btn-sm">
            Lihat Semua
            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="9 18 15 12 9 6"/></svg>
        </a>
    </div>

    @if($recentSessions->isEmpty())
        <div class="empty-state">
            <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="#94a3b8" stroke-width="1.5"><circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/></svg>
            <p>Belum ada aktivitas ujian.</p>
            <div class="sub">Peserta yang mulai ujian akan muncul di sini.</div>
        </div>
    @else
        <div style="overflow-x:auto;">
            <table>
                <thead>
                    <tr>
                        <th>Peserta</th>
                        <th>Paket Tryout</th>
                        <th>Status</th>
                        <th>Skor</th>
                        <th>Waktu</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($recentSessions as $session)
                    <tr>
                        <td>
                            <div style="display:flex;align-items:center;gap:0.5rem;">
                                <div style="width:28px;height:28px;border-radius:50%;background:var(--primary-soft);color:var(--primary);display:flex;align-items:center;justify-content:center;font-size:0.75rem;font-weight:700;flex-shrink:0;">
                                    {{ strtoupper(substr($session->user->name, 0, 1)) }}
                                </div>
                                <div>
                                    <div style="font-weight:600;font-size:0.8125rem;">{{ $session->user->name }}</div>
                                    <div style="font-size:0.75rem;color:var(--text-muted);">{{ $session->user->email }}</div>
                                </div>
                            </div>
                        </td>
                        <td style="color:var(--text-muted);">{{ $session->tryoutPackage->nama }}</td>
                        <td>
                            @if($session->status === 'berlangsung')
                                <span class="badge badge-berlangsung">Berlangsung</span>
                            @elseif($session->status === 'selesai')
                                <span class="badge badge-selesai">Selesai</span>
                            @else
                                <span class="badge badge-batal">Timeout</span>
                            @endif
                        </td>
                        <td>
                            @if($session->result)
                                <span style="font-weight:700;color:var(--primary);font-size:0.9375rem;">{{ $session->result->skor_total }}</span>
                                <span style="color:var(--text-muted);font-size:0.75rem;">/100</span>
                            @else
                                <span style="color:var(--text-light);">—</span>
                            @endif
                        </td>
                        <td style="color:var(--text-muted);font-size:0.75rem;white-space:nowrap;">{{ $session->started_at->diffForHumans() }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>
@endsection
