<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('homepage_settings', function (Blueprint $table) {
            $table->id();

            // ── Hero Section ───────────────────────────────────────────────
            $table->string('hero_badge')->default('🎓 Lembaga Bimbingan Belajar Premium');
            $table->string('hero_title')->default('Platform Tryout dan Bimbingan Belajar Terbaik');
            $table->text('hero_subtitle')->default('Bimbingan belajar untuk program SNBT, Sekolah Kedinasan, dan CPNS.');
            $table->string('hero_cta_primary')->default('Mulai Tryout');
            $table->string('hero_cta_whatsapp')->default('Hubungi Admin');
            $table->decimal('hero_passing_rate', 5, 2)->default(98.6);

            // ── Contact & Identity ──────────────────────────────────────────
            $table->string('nama_lembaga')->default('Bimbel Plano');
            $table->string('tagline')->default('Premium Education');
            $table->string('whatsapp_number')->default('6285233687867');
            $table->string('email')->default('bimbelplano@gmail.com');
            $table->string('instagram')->default('@bimbelplano_');
            $table->text('alamat')->default('Jana Residence No.2, Lr. Petua Usman, TM Bahrum, Kota Langsa, Provinsi Aceh');

            // ── Section Headings ───────────────────────────────────────────
            $table->string('program_section_title')->default('Program Bimbingan Belajar Terbaik');
            $table->text('program_section_subtitle')->default('Pilihan program belajar terbaik untuk membantu Anda meraih impian akademis dan karier masa depan.');
            $table->string('alumni_section_title')->default('Alumni yang Berhasil Meraih Impian');
            $table->text('alumni_section_subtitle')->default('Beberapa alumni peserta Bimbel Plano yang telah berhasil lulus di PTN dan instansi tujuan mereka.');
            $table->string('testimoni_section_title')->default('Cerita Sukses Alumni');
            $table->text('testimoni_section_subtitle')->default('Ungkapan langsung dari para peserta yang berhasil lolos ujian impian setelah belajar bersama Bimbel Plano.');
            $table->string('faq_section_title')->default('Pertanyaan Umum');
            $table->text('faq_section_subtitle')->default('Berikut adalah jawaban atas beberapa pertanyaan umum yang sering ditanyakan mengenai Bimbel Plano.');

            // ── Footer Description ─────────────────────────────────────────
            $table->text('footer_description')->default('Lembaga bimbingan belajar profesional yang membantu siswa lolos SNBT, CPNS, dan Sekolah Kedinasan melalui pembelajaran tatap muka, latihan soal, dan tryout online berkualitas.');

            // ── Metadata ───────────────────────────────────────────────────
            $table->string('meta_title')->default('Bimbel Plano — Lembaga Bimbingan Belajar Premium');
            $table->text('meta_description')->default('Lembaga Bimbingan Belajar Premium untuk Persiapan SNBT, Sekolah Kedinasan, dan Seleksi CPNS. Kelas Tatap Muka & Tryout Online Terbaik.');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('homepage_settings');
    }
};
