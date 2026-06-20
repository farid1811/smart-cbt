@extends('admin.layouts.app')
@section('title', 'Edit Paket Tryout')

@section('content')
<div class="form-card">
    <h3>Edit Paket: {{ $tryout->nama }}</h3>
    <form method="POST" action="{{ route('admin.tryouts.update', $tryout) }}">
        @csrf @method('PUT')
        <div class="form-group">
            <label>Nama Paket</label>
            <input type="text" name="nama" class="form-control" value="{{ old('nama', $tryout->nama) }}" required>
        </div>
        <div class="form-group">
            <label>Deskripsi</label>
            <textarea name="deskripsi" class="form-control">{{ old('deskripsi', $tryout->deskripsi) }}</textarea>
        </div>
        <div class="form-row">
            <div class="form-group">
                <label>Jenis Ujian</label>
                <select name="jenis_ujian" class="form-control" required>
                    <option value="tryout" {{ old('jenis_ujian', $tryout->jenis_ujian) === 'tryout' ? 'selected' : '' }}>Tryout</option>
                    <option value="drill" {{ old('jenis_ujian', $tryout->jenis_ujian) === 'drill' ? 'selected' : '' }}>Drill Soal</option>
                </select>
                @error('jenis_ujian')<p class="form-error">{{ $message }}</p>@enderror
            </div>
            <div class="form-group">
                <label>Durasi (menit)</label>
                <input type="number" name="durasi_menit" class="form-control" value="{{ old('durasi_menit', $tryout->durasi_menit) }}" min="10" max="300" required>
                @error('durasi_menit')<p class="form-error">{{ $message }}</p>@enderror
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label>Mode Ujian</label>
                <select name="exam_mode" id="examMode" class="form-control" required onchange="toggleSebFields()">
                    <option value="normal" {{ old('exam_mode', $tryout->exam_mode) === 'normal' ? 'selected' : '' }}>Normal (Bisa diakses browser biasa)</option>
                    <option value="seb" {{ old('exam_mode', $tryout->exam_mode) === 'seb' ? 'selected' : '' }}>Safe Exam Browser (SEB)</option>
                </select>
                @error('exam_mode')<p class="form-error">{{ $message }}</p>@enderror
            </div>
        </div>

        <div id="sebFields" style="display: {{ old('exam_mode', $tryout->exam_mode) === 'seb' ? 'block' : 'none' }}; border: 1px solid var(--border); padding: 1rem; border-radius: 8px; margin-bottom: 1rem; background: #fafafa;">
            <h4 style="margin-bottom:0.75rem; color:var(--primary); font-weight:700;">Safe Exam Browser (SEB) Settings</h4>
            <div class="form-group">
                <label>URL Ujian (Mulai)</label>
                <input type="text" name="seb_url" class="form-control" value="{{ old('seb_url', $tryout->seb_url) }}" placeholder="Contoh: http://localhost:8000/peserta/tryout/1/mulai (Kosongkan untuk otomatis)">
                <small style="color:var(--text-muted); display:block; margin-top:0.25rem;">Biarkan kosong untuk otomatis mengarahkan ke link pengerjaan ujian peserta.</small>
                @error('seb_url')<p class="form-error">{{ $message }}</p>@enderror
            </div>
            <div class="form-group">
                <label>Password Keluar (Quit Password)</label>
                <input type="password" name="seb_quit_password" class="form-control" value="{{ old('seb_quit_password', $tryout->seb_quit_password) }}" placeholder="Masukkan password untuk keluar dari SEB">
                <small style="color:var(--text-muted); display:block; margin-top:0.25rem;">Mencegah peserta menutup SEB tanpa menyelesaikan ujian.</small>
                @error('seb_quit_password')<p class="form-error">{{ $message }}</p>@enderror
            </div>
            <div class="form-group" style="margin-bottom:0;">
                <label class="toggle" style="display:flex; align-items:center; gap:0.5rem; cursor:pointer;">
                    <input type="checkbox" name="seb_browser_lockdown" value="1" {{ old('seb_browser_lockdown', $tryout->seb_browser_lockdown) ? 'checked' : '' }} style="width:16px; height:16px; accent-color:var(--primary);">
                    <span class="toggle-label" style="font-weight:600; font-size:0.85rem; color:var(--text);">Lockdown Browser (Sembunyikan URL & Reload)</span>
                </label>
            </div>
        </div>

        <div class="form-group">
            <label class="toggle" style="display:flex; align-items:center; gap:0.5rem; cursor:pointer;">
                <input type="checkbox" name="is_active" value="1" {{ old('is_active', $tryout->is_active) ? 'checked' : '' }} style="width:16px; height:16px; accent-color:var(--primary);">
                <span class="toggle-label" style="font-weight:600; font-size:0.85rem; color:var(--text);">Aktifkan Paket Ujian</span>
            </label>
        </div>
        <div class="form-row">
            <div class="form-group">
                <label>Tanggal Mulai</label>
                <input type="datetime-local" name="mulai_at" class="form-control" value="{{ old('mulai_at', $tryout->mulai_at?->format('Y-m-d\TH:i')) }}">
            </div>
            <div class="form-group">
                <label>Tanggal Selesai</label>
                <input type="datetime-local" name="selesai_at" class="form-control" value="{{ old('selesai_at', $tryout->selesai_at?->format('Y-m-d\TH:i')) }}">
            </div>
        </div>
        <div class="form-actions">
            <button type="submit" class="btn btn-primary">Update Paket</button>
            <a href="{{ route('admin.tryouts.index') }}" class="btn btn-secondary">Batal</a>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
function toggleSebFields() {
    const examMode = document.getElementById('examMode').value;
    const sebFields = document.getElementById('sebFields');
    if (examMode === 'seb') {
        sebFields.style.display = 'block';
    } else {
        sebFields.style.display = 'none';
    }
}
</script>
@endpush
