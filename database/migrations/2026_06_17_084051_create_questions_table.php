<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained('categories')->onDelete('cascade');
            $table->text('soal');
            $table->text('opsi_a');
            $table->text('opsi_b');
            $table->text('opsi_c');
            $table->text('opsi_d');
            $table->text('opsi_e')->nullable();
            $table->enum('jawaban_benar', ['A', 'B', 'C', 'D', 'E']);
            $table->text('pembahasan')->nullable();
            $table->enum('tingkat_kesulitan', ['mudah', 'sedang', 'sulit'])->default('sedang');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('questions');
    }
};
