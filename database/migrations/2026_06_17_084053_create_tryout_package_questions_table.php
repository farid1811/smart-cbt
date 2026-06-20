<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tryout_package_questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tryout_package_id')->constrained('tryout_packages')->onDelete('cascade');
            $table->foreignId('question_id')->constrained('questions')->onDelete('cascade');
            $table->integer('urutan')->default(0);
            $table->timestamps();

            $table->unique(['tryout_package_id', 'question_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tryout_package_questions');
    }
};
