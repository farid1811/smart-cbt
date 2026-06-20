@extends('admin.layouts.app')
@section('title', 'Edit Soal')

@section('content')
<div class="form-card" style="max-width:820px;">
    <h3>Edit Soal</h3>
    <form method="POST" action="{{ route('admin.questions.update', $question) }}" enctype="multipart/form-data">
        @csrf @method('PUT')
        <div class="form-row">
            <div class="form-group">
                <label>Kategori</label>
                <select name="category_id" class="form-control" required>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ old('category_id', $question->category_id) == $cat->id ? 'selected' : '' }}>{{ $cat->kode }} — {{ $cat->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label>Tingkat Kesulitan</label>
                <select name="tingkat_kesulitan" class="form-control" required>
                    @foreach(['mudah','sedang','sulit'] as $t)
                        <option value="{{ $t }}" {{ old('tingkat_kesulitan', $question->tingkat_kesulitan) == $t ? 'selected' : '' }}>{{ ucfirst($t) }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="form-group">
            <label>Soal</label>
            <textarea name="soal" class="form-control" rows="4" required>{{ old('soal', $question->soal) }}</textarea>
        </div>

        <div class="form-group">
            <label>Gambar Soal / Diagram (Opsional)</label>
            @if($question->image)
                <div style="margin-bottom:0.75rem;">
                    <img src="{{ asset($question->image) }}" alt="Preview Gambar" style="max-height:150px; border-radius:6px; border:1px solid var(--border);">
                    <label style="display:flex; align-items:center; gap:0.35rem; font-size:0.8rem; color:var(--error); margin-top:0.25rem; cursor:pointer;">
                        <input type="checkbox" name="hapus_image" value="1"> Hapus gambar yang ada
                    </label>
                </div>
            @endif
            <input type="file" name="image" class="form-control" accept="image/*">
            <p class="form-hint" style="font-size:0.75rem; color:var(--text-muted); margin-top:0.25rem;">Pilih file gambar baru jika ingin mengganti. Format: JPEG, PNG, JPG, GIF, SVG. Maksimal 2MB.</p>
            @error('image')<p class="form-error">{{ $message }}</p>@enderror
        </div>
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:0.85rem;">
            @foreach(['a','b','c','d','e'] as $opsi)
            <div class="form-group">
                <label>Opsi {{ strtoupper($opsi) }}</label>
                <input type="text" name="opsi_{{ $opsi }}" class="form-control" value="{{ old('opsi_'.$opsi, $question->{'opsi_'.$opsi}) }}" {{ $opsi !== 'e' ? 'required' : '' }}>
            </div>
            @endforeach
        </div>
        <div class="form-group">
            <label>Jawaban Benar</label>
            <select name="jawaban_benar" class="form-control" required>
                @foreach(['A','B','C','D','E'] as $j)
                    <option value="{{ $j }}" {{ old('jawaban_benar', $question->jawaban_benar) == $j ? 'selected' : '' }}>{{ $j }}</option>
                @endforeach
            </select>
        </div>
        <div class="form-group">
            <label>Pembahasan</label>
            <textarea name="pembahasan" class="form-control">{{ old('pembahasan', $question->pembahasan) }}</textarea>
        </div>
        <div class="form-actions">
            <button type="submit" class="btn btn-primary">Update Soal</button>
            <a href="{{ route('admin.questions.index') }}" class="btn btn-secondary">Batal</a>
        </div>
    </form>
</div>
@endsection
