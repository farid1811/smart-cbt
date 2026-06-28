@extends('admin.layouts.app')

@section('title', 'Pengaturan Akun')

@section('content')
<div style="max-width: 900px; margin: 0 auto;">
    <div style="margin-bottom: 2rem;">
        <h2 style="font-weight: 800; color: #0f172a; margin: 0; font-size: 1.5rem;">Pengaturan Akun</h2>
        <p style="color: #64748b; font-size: 0.875rem; margin-top: 0.25rem;">
            Kelola username dan password akun Administrator Anda di sini.
        </p>
    </div>

    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; align-items: start;">
        {{-- Ubah Username --}}
        <div style="background: var(--surface); border: 1px solid var(--border); border-radius: var(--radius-lg); box-shadow: var(--shadow-sm); overflow: hidden;">
            <div style="padding: 1rem 1.25rem; border-bottom: 1px solid var(--border); background: var(--surface2); display: flex; align-items: center; gap: 0.75rem;">
                <div style="width: 34px; height: 34px; border-radius: 8px; display: flex; align-items: center; justify-content: center; background: #EFF6FF; color: #2563EB;">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                </div>
                <div>
                    <h3 style="font-size: 0.9375rem; font-weight: 700; color: var(--text); margin: 0;">Ubah Username</h3>
                </div>
            </div>
            <div style="padding: 1.25rem;">
                <form method="POST" action="{{ route('admin.settings.account.username') }}">
                    @csrf
                    <div style="margin-bottom: 1rem;">
                        <label style="display: block; font-size: 0.75rem; font-weight: 600; text-transform: uppercase; color: var(--text-light); margin-bottom: 0.5rem;">Username Saat Ini</label>
                        <input type="text" class="form-control" value="{{ $user->username }}" disabled style="background: var(--surface3); cursor: not-allowed; width: 100%;">
                    </div>
                    <div style="margin-bottom: 1.5rem;">
                        <label for="username" style="display: block; font-size: 0.75rem; font-weight: 600; text-transform: uppercase; color: var(--text-light); margin-bottom: 0.5rem;">Username Baru</label>
                        <input type="text" id="username" name="username" class="form-control" placeholder="Masukkan username baru" required style="width: 100%;" value="{{ old('username', $user->username) }}">
                    </div>
                    <button type="submit" class="btn btn-primary" style="width: 100%; font-weight: 700; display: flex; align-items: center; justify-content: center; gap: 0.5rem; padding: 0.625rem;">
                        Simpan Username
                    </button>
                </form>
            </div>
        </div>

        {{-- Ubah Password --}}
        <div style="background: var(--surface); border: 1px solid var(--border); border-radius: var(--radius-lg); box-shadow: var(--shadow-sm); overflow: hidden;">
            <div style="padding: 1rem 1.25rem; border-bottom: 1px solid var(--border); background: var(--surface2); display: flex; align-items: center; gap: 0.75rem;">
                <div style="width: 34px; height: 34px; border-radius: 8px; display: flex; align-items: center; justify-content: center; background: #FFF7ED; color: #EA580C;">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                </div>
                <div>
                    <h3 style="font-size: 0.9375rem; font-weight: 700; color: var(--text); margin: 0;">Ubah Password</h3>
                </div>
            </div>
            <div style="padding: 1.25rem;">
                <form method="POST" action="{{ route('admin.settings.account.password') }}">
                    @csrf
                    <div style="margin-bottom: 1rem;">
                        <label for="current_password" style="display: block; font-size: 0.75rem; font-weight: 600; text-transform: uppercase; color: var(--text-light); margin-bottom: 0.5rem;">Password Lama</label>
                        <input type="password" id="current_password" name="current_password" class="form-control" placeholder="Masukkan password saat ini" required style="width: 100%;">
                    </div>
                    <div style="margin-bottom: 1rem;">
                        <label for="password" style="display: block; font-size: 0.75rem; font-weight: 600; text-transform: uppercase; color: var(--text-light); margin-bottom: 0.5rem;">Password Baru</label>
                        <input type="password" id="password" name="password" class="form-control" placeholder="Minimal 8 karakter" required style="width: 100%;">
                    </div>
                    <div style="margin-bottom: 1.5rem;">
                        <label for="password_confirmation" style="display: block; font-size: 0.75rem; font-weight: 600; text-transform: uppercase; color: var(--text-light); margin-bottom: 0.5rem;">Konfirmasi Password Baru</label>
                        <input type="password" id="password_confirmation" name="password_confirmation" class="form-control" placeholder="Ulangi password baru" required style="width: 100%;">
                    </div>
                    <button type="submit" class="btn btn-warning" style="width: 100%; font-weight: 700; display: flex; align-items: center; justify-content: center; gap: 0.5rem; padding: 0.625rem; color: #fff; background: #EA580C; border-color: #EA580C;">
                        Perbarui Password
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
