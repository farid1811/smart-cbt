<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tryout_packages', function (Blueprint $table) {
            $table->foreignId('group_id')->nullable()->after('jenis_ujian')->constrained('groups')->onDelete('cascade');
            $table->foreignId('question_code_id')->nullable()->after('group_id')->constrained('question_codes')->onDelete('set null');
            $table->foreignId('category_id')->nullable()->after('question_code_id')->constrained('categories')->onDelete('set null');
            $table->foreignId('sub_category_id')->nullable()->after('category_id')->constrained('sub_categories')->onDelete('set null');
        });

        // Migrate existing packages' group string to group_id
        $packages = DB::table('tryout_packages')->get();
        foreach ($packages as $pkg) {
            $groupName = $pkg->group ?: 'SKD';
            $group = DB::table('groups')->where('name', $groupName)->first();
            if ($group) {
                DB::table('tryout_packages')->where('id', $pkg->id)->update([
                    'group_id' => $group->id
                ]);
            }
        }
    }

    public function down(): void
    {
        Schema::table('tryout_packages', function (Blueprint $table) {
            $table->dropForeign(['group_id']);
            $table->dropForeign(['question_code_id']);
            $table->dropForeign(['category_id']);
            $table->dropForeign(['sub_category_id']);
            $table->dropColumn(['group_id', 'question_code_id', 'category_id', 'sub_category_id']);
        });
    }
};
