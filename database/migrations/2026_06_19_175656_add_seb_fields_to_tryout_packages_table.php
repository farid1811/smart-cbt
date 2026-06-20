<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tryout_packages', function (Blueprint $table) {
            $table->string('exam_mode')->default('normal')->after('jenis_ujian');
            $table->string('seb_url')->nullable()->after('exam_mode');
            $table->string('seb_quit_password')->nullable()->after('seb_url');
            $table->boolean('seb_browser_lockdown')->default(true)->after('seb_quit_password');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tryout_packages', function (Blueprint $table) {
            $table->dropColumn(['exam_mode', 'seb_url', 'seb_quit_password', 'seb_browser_lockdown']);
        });
    }
};
