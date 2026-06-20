@extends('admin.layouts.app')
@section('title', 'Tambah Kategori')

@section('content')
<div class="form-card">
    <h3>Tambah Kategori Baru</h3>
    <form method="POST" action="{{ route('admin.categories.store') }}">
        @csrf
        <div class="form-row">
            <div class="form-group">
                <label for="group_id">Grup / Kelas</label>
                <select id="group_id" name="group_id" class="form-control" required>
                    <option value="">-- Pilih Grup --</option>
                    @foreach($groups as $group)
                        <option value="{{ $group->id }}" {{ old('group_id') == $group->id ? 'selected' : '' }}>{{ $group->name }}</option>
                    @endforeach
                </select>
                @error('group_id')<p class="form-error">{{ $message }}</p>@enderror
            </div>
            <div class="form-group">
                <label for="kode">Kode Kategori</label>
                <input type="text" id="kode" name="kode" class="form-control" value="{{ old('kode') }}" placeholder="TPS / CPNS / KEDINASAN" style="text-transform:uppercase;" maxlength="10" required>
                @error('kode')<p class="form-error">{{ $message }}</p>@enderror
            </div>
        </div>
        <div class="form-group">
            <label for="name">Nama Kategori</label>
            <input type="text" id="name" name="name" class="form-control" value="{{ old('name') }}" placeholder="Tes Potensi Skolastik / CPNS" required>
            @error('name')<p class="form-error">{{ $message }}</p>@enderror
        </div>
        <div class="form-group">
            <label for="deskripsi">Deskripsi</label>
            <textarea id="deskripsi" name="deskripsi" class="form-control" placeholder="Deskripsi singkat kategori soal...">{{ old('deskripsi') }}</textarea>
            @error('deskripsi')<p class="form-error">{{ $message }}</p>@enderror
        </div>
        <div class="form-actions">
            <button type="submit" class="btn btn-primary">Simpan Kategori</button>
            <a href="{{ route('admin.categories.index') }}" class="btn btn-secondary">Batal</a>
        </div>
    </form>
</div>
@endsection
