<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Create groups table
        Schema::create('groups', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->timestamps();
        });

        // Seed default groups
        $skdId = DB::table('groups')->insertGetId([
            'name' => 'SKD',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $snbtId = DB::table('groups')->insertGetId([
            'name' => 'SNBT',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // 2. Add group_id to users
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('group_id')->nullable()->after('role')->constrained('groups')->onDelete('set null');
        });

        // 3. Add group_id to categories
        Schema::table('categories', function (Blueprint $table) {
            $table->foreignId('group_id')->nullable()->after('name')->constrained('groups')->onDelete('cascade');
        });

        // Map existing categories to SKD group
        DB::table('categories')->update(['group_id' => $skdId]);

        // 4. Add jenis_ujian to tryout_packages
        Schema::table('tryout_packages', function (Blueprint $table) {
            $table->string('jenis_ujian')->default('tryout')->after('deskripsi'); // 'tryout' or 'drill'
        });

        // 5. Add options_mapping to exam_answers
        Schema::table('exam_answers', function (Blueprint $table) {
            $table->json('options_mapping')->nullable()->after('is_ragu');
        });

        // 6. Add violation tracking to exam_sessions
        Schema::table('exam_sessions', function (Blueprint $table) {
            $table->integer('violations_count')->default(0)->after('status');
            $table->json('violation_logs')->nullable()->after('violations_count');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('exam_sessions', function (Blueprint $table) {
            $table->dropColumn(['violations_count', 'violation_logs']);
        });

        Schema::table('exam_answers', function (Blueprint $table) {
            $table->dropColumn('options_mapping');
        });

        Schema::table('tryout_packages', function (Blueprint $table) {
            $table->dropColumn('jenis_ujian');
        });

        Schema::table('categories', function (Blueprint $table) {
            $table->dropForeign(['group_id']);
            $table->dropColumn('group_id');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['group_id']);
            $table->dropColumn('group_id');
        });

        Schema::dropIfExists('groups');
    }
};
