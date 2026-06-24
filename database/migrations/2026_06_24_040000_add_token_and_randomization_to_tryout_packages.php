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
        Schema::table('tryout_packages', function (Blueprint $table) {
            $table->string('token')->nullable()->after('attempt_limit');
            $table->boolean('randomize_questions')->default(false)->after('token');
            $table->boolean('randomize_options')->default(false)->after('randomize_questions');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tryout_packages', function (Blueprint $table) {
            $table->dropColumn(['token', 'randomize_questions', 'randomize_options']);
        });
    }
};
