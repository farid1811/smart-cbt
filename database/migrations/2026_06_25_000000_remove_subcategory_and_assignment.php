<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Nullify sub_category_id sebelum drop (aman)
        DB::statement('UPDATE questions SET sub_category_id = NULL WHERE sub_category_id IS NOT NULL');
        DB::statement('UPDATE learning_modules SET sub_category_id = NULL WHERE sub_category_id IS NOT NULL');
        DB::statement('UPDATE tryout_packages SET sub_category_id = NULL WHERE sub_category_id IS NOT NULL');

        // 2. Drop index sub_category_id di questions (jika ada dari migration sebelumnya)
        // Gunakan try/catch karena index mungkin bernama berbeda di tiap DB
        try {
            Schema::table('questions', function (Blueprint $table) {
                $table->dropIndex(['sub_category_id']);
            });
        } catch (\Exception $e) {
            // Index tidak ada, lanjutkan
        }

        // 3. Drop foreign keys dan kolom sub_category_id di setiap tabel
        Schema::table('questions', function (Blueprint $table) {
            try { $table->dropForeign(['sub_category_id']); } catch (\Exception $e) {}
            $table->dropColumn('sub_category_id');
        });

        Schema::table('learning_modules', function (Blueprint $table) {
            try { $table->dropForeign(['sub_category_id']); } catch (\Exception $e) {}
            $table->dropColumn('sub_category_id');
        });

        Schema::table('tryout_packages', function (Blueprint $table) {
            try { $table->dropForeign(['sub_category_id']); } catch (\Exception $e) {}
            $table->dropColumn('sub_category_id');
        });

        // 4. Drop tabel sub_categories
        Schema::dropIfExists('sub_categories');

        // 5. Drop assigned_package_id dari users
        Schema::table('users', function (Blueprint $table) {
            try { $table->dropForeign(['assigned_package_id']); } catch (\Exception $e) {}
            if (Schema::hasColumn('users', 'assigned_package_id')) {
                $table->dropColumn('assigned_package_id');
            }
        });
    }

    public function down(): void
    {
        // Recreate sub_categories table
        Schema::create('sub_categories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained()->onDelete('cascade');
            $table->string('name', 100);
            $table->timestamps();
        });

        // Re-add sub_category_id to questions
        Schema::table('questions', function (Blueprint $table) {
            $table->unsignedBigInteger('sub_category_id')->nullable()->after('category_id');
        });

        // Re-add sub_category_id to learning_modules
        Schema::table('learning_modules', function (Blueprint $table) {
            $table->unsignedBigInteger('sub_category_id')->nullable()->after('category_id');
        });

        // Re-add sub_category_id to tryout_packages
        Schema::table('tryout_packages', function (Blueprint $table) {
            $table->unsignedBigInteger('sub_category_id')->nullable()->after('category_id');
        });

        // Re-add assigned_package_id to users
        Schema::table('users', function (Blueprint $table) {
            $table->unsignedBigInteger('assigned_package_id')->nullable()->after('group_id');
        });
    }
};
