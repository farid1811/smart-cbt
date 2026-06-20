<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('results', function (Blueprint $table) {
            $table->id();
            $table->foreignId('exam_session_id')->constrained('exam_sessions')->onDelete('cascade')->unique();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('tryout_package_id')->constrained('tryout_packages')->onDelete('cascade');
            $table->decimal('skor_twk', 8, 2)->nullable()->default(0);
            $table->decimal('skor_tiu', 8, 2)->nullable()->default(0);
            $table->decimal('skor_tkp', 8, 2)->nullable()->default(0);
            $table->decimal('skor_total', 8, 2)->default(0);
            $table->integer('jumlah_benar')->default(0);
            $table->integer('jumlah_salah')->default(0);
            $table->integer('jumlah_kosong')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('results');
    }
};
