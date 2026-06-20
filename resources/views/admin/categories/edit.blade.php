@extends('admin.layouts.app')
@section('title', 'Edit Kategori')

@section('content')
<div class="form-card">
    <h3>Edit Kategori</h3>
    <form method="POST" action="{{ route('admin.categories.update', $category) }}">
        @csrf @method('PUT')
        <div class="form-row">
            <div class="form-group">
                <label for="group_id">Grup / Kelas</label>
                <select id="group_id" name="group_id" class="form-control" required>
                    <option value="">-- Pilih Grup --</option>
                    @foreach($groups as $group)
                        <option value="{{ $group->id }}" {{ old('group_id', $category->group_id) == $group->id ? 'selected' : '' }}>{{ $group->name }}</option>
                    @endforeach
                </select>
                @error('group_id')<p class="form-error">{{ $message }}</p>@enderror
            </div>
            <div class="form-group">
                <label for="kode">Kode Kategori</label>
                <input type="text" id="kode" name="kode" class="form-control" value="{{ old('kode', $category->kode) }}" style="text-transform:uppercase;" maxlength="10" required>
                @error('kode')<p class="form-error">{{ $message }}</p>@enderror
            </div>
        </div>
        <div class="form-group">
            <label for="name">Nama Kategori</label>
            <input type="text" id="name" name="name" class="form-control" value="{{ old('name', $category->name) }}" required>
            @error('name')<p class="form-error">{{ $message }}</p>@enderror
        </div>
        <div class="form-group">
            <label for="deskripsi">Deskripsi</label>
            <textarea id="deskripsi" name="deskripsi" class="form-control">{{ old('deskripsi', $category->deskripsi) }}</textarea>
        </div>
        <div class="form-actions">
            <button type="submit" class="btn btn-primary">Update Kategori</button>
            <a href="{{ route('admin.categories.index') }}" class="btn btn-secondary">Batal</a>
        </div>
    </form>
</div>
@endsection
