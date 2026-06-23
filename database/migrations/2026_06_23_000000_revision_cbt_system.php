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
        // 1. Alter users table
        Schema::table('users', function (Blueprint $table) {
            $table->string('username')->unique()->nullable()->after('email');
            $table->string('category')->nullable()->after('group_id'); // CPNS, Kedinasan, SNBT
            $table->foreignId('assigned_package_id')->nullable()->after('category')->constrained('tryout_packages')->onDelete('set null');
        });

        // Seed usernames for existing users based on email
        $users = DB::table('users')->get();
        foreach ($users as $user) {
            $username = explode('@', $user->email)[0];
            // ensure unique username
            $base = $username;
            $count = 1;
            while (DB::table('users')->where('username', $username)->exists()) {
                $username = $base . $count;
                $count++;
            }
            DB::table('users')->where('id', $user->id)->update(['username' => $username]);
        }

        // 2. Alter tryout_packages table
        Schema::table('tryout_packages', function (Blueprint $table) {
            $table->string('group')->nullable()->after('jenis_ujian'); // SKD, SNBT
            $table->string('category')->nullable()->after('group'); // CPNS, Kedinasan, SNBT
            $table->integer('attempt_limit')->default(2)->after('category');
        });

        // Set default group for existing packages based on their association or dummy logic
        // We know from seeder: 'Tryout SKD CPNS #1' is SKD, 'Drill Soal Kognitif SNBT' is SNBT
        DB::table('tryout_packages')->where('nama', 'like', '%SKD%')->update(['group' => 'SKD', 'category' => 'CPNS']);
        DB::table('tryout_packages')->where('nama', 'like', '%SNBT%')->update(['group' => 'SNBT', 'category' => 'SNBT']);
        DB::table('tryout_packages')->whereNull('group')->update(['group' => 'SKD', 'category' => 'CPNS']); // fallback

        // 3. Alter questions table
        Schema::table('questions', function (Blueprint $table) {
            $table->foreignId('tryout_package_id')->nullable()->after('category_id')->constrained('tryout_packages')->onDelete('cascade');
            $table->integer('urutan')->default(0)->after('tryout_package_id');
            $table->string('question_image')->nullable()->after('image');
            $table->string('option_a_image')->nullable()->after('opsi_a');
            $table->string('option_b_image')->nullable()->after('opsi_b');
            $table->string('option_c_image')->nullable()->after('opsi_c');
            $table->string('option_d_image')->nullable()->after('opsi_d');
            $table->string('option_e_image')->nullable()->after('opsi_e');
            $table->string('explanation_image')->nullable()->after('pembahasan');
        });

        // Copy question_image from old image column (if exists)
        DB::table('questions')->whereNotNull('image')->update([
            'question_image' => DB::raw('image')
        ]);

        // 4. Migrate associations from tryout_package_questions to questions table
        if (Schema::hasTable('tryout_package_questions')) {
            $relations = DB::table('tryout_package_questions')->get();
            $assignedQuestionIds = [];
            foreach ($relations as $rel) {
                if (in_array($rel->question_id, $assignedQuestionIds)) {
                    // Question belongs to multiple packages. Duplicate it for the second package to satisfy 1-to-many
                    $oldQuestion = DB::table('questions')->where('id', $rel->question_id)->first();
                    if ($oldQuestion) {
                        $newQuestionId = DB::table('questions')->insertGetId([
                            'category_id' => $oldQuestion->category_id,
                            'tryout_package_id' => $rel->tryout_package_id,
                            'urutan' => $rel->urutan,
                            'soal' => $oldQuestion->soal,
                            'image' => $oldQuestion->image,
                            'question_image' => $oldQuestion->question_image,
                            'opsi_a' => $oldQuestion->opsi_a,
                            'opsi_b' => $oldQuestion->opsi_b,
                            'opsi_c' => $oldQuestion->opsi_c,
                            'opsi_d' => $oldQuestion->opsi_d,
                            'opsi_e' => $oldQuestion->opsi_e,
                            'jawaban_benar' => $oldQuestion->jawaban_benar,
                            'pembahasan' => $oldQuestion->pembahasan,
                            'tingkat_kesulitan' => $oldQuestion->tingkat_kesulitan,
                            'created_at' => $oldQuestion->created_at,
                            'updated_at' => $oldQuestion->updated_at,
                        ]);

                        // Update answers pointing to this question in the sessions of this package
                        $sessions = DB::table('exam_sessions')
                            ->where('tryout_package_id', $rel->tryout_package_id)
                            ->pluck('id');
                        DB::table('exam_answers')
                            ->whereIn('exam_session_id', $sessions)
                            ->where('question_id', $rel->question_id)
                            ->update(['question_id' => $newQuestionId]);
                    }
                } else {
                    DB::table('questions')
                        ->where('id', $rel->question_id)
                        ->update([
                            'tryout_package_id' => $rel->tryout_package_id,
                            'urutan' => $rel->urutan,
                        ]);
                    $assignedQuestionIds[] = $rel->question_id;
                }
            }

            // Drop pivot table
            Schema::dropIfExists('tryout_package_questions');
        }

        // 5. Create learning_modules table
        Schema::create('learning_modules', function (Blueprint $table) {
            $table->id();
            $table->string('group'); // SKD, SNBT
            $table->string('subtest'); // TWK, TIU, TKP, PU, PPU, PBM, PK, LBI, LBIng, PM
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('pdf_file')->nullable();
            $table->string('video_url')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // 6. Create package_attempts table
        Schema::create('package_attempts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('participant_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('package_id')->constrained('tryout_packages')->onDelete('cascade');
            $table->integer('attempt_number');
            $table->double('score')->nullable();
            $table->timestamp('started_at');
            $table->timestamp('finished_at')->nullable();
            $table->timestamps();
        });

        // Seed attempt tracking table from existing results (if any)
        $results = DB::table('results')->get();
        foreach ($results as $res) {
            // Find exam session to get started_at/ended_at
            $session = DB::table('exam_sessions')->where('id', $res->exam_session_id)->first();
            $started = $session ? $session->started_at : $res->created_at;
            $finished = $session ? $session->ended_at : $res->created_at;

            // count previous attempts
            $count = DB::table('package_attempts')
                ->where('participant_id', $res->user_id)
                ->where('package_id', $res->tryout_package_id)
                ->count();

            DB::table('package_attempts')->insert([
                'participant_id' => $res->user_id,
                'package_id' => $res->tryout_package_id,
                'attempt_number' => $count + 1,
                'score' => $res->skor_total,
                'started_at' => $started,
                'finished_at' => $finished,
                'created_at' => $res->created_at,
                'updated_at' => $res->updated_at,
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('package_attempts');
        Schema::dropIfExists('learning_modules');

        Schema::create('tryout_package_questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tryout_package_id')->constrained('tryout_packages')->onDelete('cascade');
            $table->foreignId('question_id')->constrained('questions')->onDelete('cascade');
            $table->integer('urutan')->default(0);
            $table->timestamps();
        });

        Schema::table('questions', function (Blueprint $table) {
            $table->dropForeign(['tryout_package_id']);
            $table->dropColumn([
                'tryout_package_id',
                'urutan',
                'question_image',
                'option_a_image',
                'option_b_image',
                'option_c_image',
                'option_d_image',
                'option_e_image',
                'explanation_image',
            ]);
        });

        Schema::table('tryout_packages', function (Blueprint $table) {
            $table->dropColumn(['group', 'category', 'attempt_limit']);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['assigned_package_id']);
            $table->dropColumn(['username', 'category', 'assigned_package_id']);
        });
    }
};
