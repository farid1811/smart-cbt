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
        Schema::table('questions', function (Blueprint $table) {
            $table->index('group_id');
            $table->index('question_code_id');
            $table->index('category_id');
            $table->index('sub_category_id');
            $table->index('tryout_package_id');
        });

        Schema::table('results', function (Blueprint $table) {
            $table->index('user_id');
            $table->index('tryout_package_id');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->index('group_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('questions', function (Blueprint $table) {
            $table->dropIndex(['group_id']);
            $table->dropIndex(['question_code_id']);
            $table->dropIndex(['category_id']);
            $table->dropIndex(['sub_category_id']);
            $table->dropIndex(['tryout_package_id']);
        });

        Schema::table('results', function (Blueprint $table) {
            $table->dropIndex(['user_id']);
            $table->dropIndex(['tryout_package_id']);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex(['group_id']);
        });
    }
};
