@extends('admin.layouts.app')
@section('title', 'Tambah Soal')

@section('content')
<div class="form-card" style="max-width:820px;">
    <h3>Tambah Soal Baru</h3>
    <form method="POST" action="{{ route('admin.questions.store') }}" enctype="multipart/form-data">
        @csrf
        <div class="form-row">
            <div class="form-group">
                <label>Kategori</label>
                <select name="category_id" class="form-control" required>
                    <option value="">-- Pilih Kategori --</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ old('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->kode }} — {{ $cat->name }}</option>
                    @endforeach
                </select>
                @error('category_id')<p class="form-error">{{ $message }}</p>@enderror
            </div>
            <div class="form-group">
                <label>Tingkat Kesulitan</label>
                <select name="tingkat_kesulitan" class="form-control" required>
                    <option value="mudah"  {{ old('tingkat_kesulitan') == 'mudah'  ? 'selected' : '' }}>Mudah</option>
                    <option value="sedang" {{ old('tingkat_kesulitan','sedang') == 'sedang' ? 'selected' : '' }}>Sedang</option>
                    <option value="sulit"  {{ old('tingkat_kesulitan') == 'sulit'  ? 'selected' : '' }}>Sulit</option>
                </select>
            </div>
        </div>

        <div class="form-group">
            <label>Soal / Pertanyaan</label>
            <textarea name="soal" class="form-control" rows="4" placeholder="Tuliskan soal di sini..." required>{{ old('soal') }}</textarea>
            @error('soal')<p class="form-error">{{ $message }}</p>@enderror
        </div>

        <div class="form-group">
            <label>Gambar Soal / Diagram (Opsional)</label>
            <input type="file" name="image" class="form-control" accept="image/*">
            <p class="form-hint" style="font-size:0.75rem; color:var(--text-muted); margin-top:0.25rem;">Format file: JPEG, PNG, JPG, GIF, SVG. Maksimal 2MB.</p>
            @error('image')<p class="form-error">{{ $message }}</p>@enderror
        </div>

        <div style="display:grid;grid-template-columns:1fr 1fr;gap:0.85rem;">
            @foreach(['a','b','c','d','e'] as $opsi)
            <div class="form-group">
                <label>Opsi {{ strtoupper($opsi) }} {{ $opsi === 'e' ? '(Opsional)' : '' }}</label>
                <input type="text" name="opsi_{{ $opsi }}" class="form-control" value="{{ old('opsi_'.$opsi) }}"
                    placeholder="Pilihan {{ strtoupper($opsi) }}" {{ $opsi !== 'e' ? 'required' : '' }}>
                @error('opsi_'.$opsi)<p class="form-error">{{ $message }}</p>@enderror
            </div>
            @endforeach
        </div>

        <div class="form-group">
            <label>Jawaban Benar</label>
            <select name="jawaban_benar" class="form-control" required>
                @foreach(['A','B','C','D','E'] as $j)
                    <option value="{{ $j }}" {{ old('jawaban_benar') == $j ? 'selected' : '' }}>{{ $j }}</option>
                @endforeach
            </select>
            @error('jawaban_benar')<p class="form-error">{{ $message }}</p>@enderror
        </div>

        <div class="form-group">
            <label>Pembahasan (Opsional)</label>
            <textarea name="pembahasan" class="form-control" placeholder="Penjelasan jawaban benar...">{{ old('pembahasan') }}</textarea>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary">Simpan Soal</button>
            <a href="{{ route('admin.questions.index') }}" class="btn btn-secondary">Batal</a>
        </div>
    </form>
</div>
@endsection
