<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tryout_packages', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->text('deskripsi')->nullable();
            $table->integer('durasi_menit')->default(90);
            $table->boolean('is_active')->default(true);
            $table->dateTime('mulai_at')->nullable();
            $table->dateTime('selesai_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tryout_packages');
    }
};
