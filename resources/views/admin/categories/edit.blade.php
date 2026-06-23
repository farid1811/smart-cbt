@extends('admin.layouts.app')
@section('title', 'Edit Kategori')

@section('content')
<div class="card" style="padding: 2rem; border-radius: 12px; background: #fff; text-align: center;">
    <h3 style="font-weight: 700; color: #0f172a;">Mengedit Kategori</h3>
    <p style="color: #64748b; margin-bottom: 1.5rem;">Sistem kategori, kode soal, dan sub-kategori sekarang dikelola secara langsung dari halaman utama Kategori & Hierarki Soal menggunakan modal.</p>
    <a href="{{ route('admin.categories.index') }}" class="btn btn-primary">Kembali ke Kategori & Hierarki Soal</a>
</div>
@endsection
