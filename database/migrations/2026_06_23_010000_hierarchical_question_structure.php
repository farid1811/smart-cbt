<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Create question_codes table
        Schema::create('question_codes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('group_id')->constrained('groups')->onDelete('cascade');
            $table->string('name');
            $table->string('code')->unique();
            $table->timestamps();
        });

        // 2. Alter categories table
        Schema::table('categories', function (Blueprint $table) {
            // Drop unique index on kode first
            $table->dropUnique(['kode']);
            // Drop foreign key first if exists
            $table->dropForeign(['group_id']);
            $table->dropColumn(['group_id', 'kode', 'deskripsi']);
            $table->foreignId('question_code_id')->nullable()->constrained('question_codes')->onDelete('cascade');
        });

        // 3. Create sub_categories table
        Schema::create('sub_categories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained('categories')->onDelete('cascade');
            $table->string('name');
            $table->timestamps();
        });

        // 4. Alter questions table
        Schema::table('questions', function (Blueprint $table) {
            $table->foreignId('group_id')->nullable()->constrained('groups')->onDelete('cascade');
            $table->foreignId('question_code_id')->nullable()->constrained('question_codes')->onDelete('cascade');
            $table->foreignId('sub_category_id')->nullable()->constrained('sub_categories')->onDelete('cascade');
        });

        // 5. Alter learning_modules table
        Schema::table('learning_modules', function (Blueprint $table) {
            $table->foreignId('group_id')->nullable()->constrained('groups')->onDelete('cascade');
            $table->foreignId('question_code_id')->nullable()->constrained('question_codes')->onDelete('cascade');
            $table->foreignId('category_id')->nullable()->constrained('categories')->onDelete('cascade');
            $table->foreignId('sub_category_id')->nullable()->constrained('sub_categories')->onDelete('cascade');
            $table->dropColumn(['group', 'subtest']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('learning_modules', function (Blueprint $table) {
            $table->string('group')->nullable();
            $table->string('subtest')->nullable();
            $table->dropForeign(['group_id']);
            $table->dropForeign(['question_code_id']);
            $table->dropForeign(['category_id']);
            $table->dropForeign(['sub_category_id']);
            $table->dropColumn(['group_id', 'question_code_id', 'category_id', 'sub_category_id']);
        });

        Schema::table('questions', function (Blueprint $table) {
            $table->dropForeign(['group_id']);
            $table->dropForeign(['question_code_id']);
            $table->dropForeign(['sub_category_id']);
            $table->dropColumn(['group_id', 'question_code_id', 'sub_category_id']);
        });

        Schema::dropIfExists('sub_categories');

        Schema::table('categories', function (Blueprint $table) {
            $table->dropForeign(['question_code_id']);
            $table->dropColumn('question_code_id');
            $table->string('kode')->nullable();
            $table->text('deskripsi')->nullable();
            $table->foreignId('group_id')->nullable()->constrained('groups')->onDelete('cascade');
        });

        Schema::dropIfExists('question_codes');
    }
};
