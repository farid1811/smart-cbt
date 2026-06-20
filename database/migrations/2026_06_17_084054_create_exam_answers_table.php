<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('exam_answers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('exam_session_id')->constrained('exam_sessions')->onDelete('cascade');
            $table->foreignId('question_id')->constrained('questions')->onDelete('cascade');
            $table->enum('jawaban', ['A', 'B', 'C', 'D', 'E'])->nullable();
            $table->boolean('is_ragu')->default(false);
            $table->timestamps();

            $table->unique(['exam_session_id', 'question_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('exam_answers');
    }
};
