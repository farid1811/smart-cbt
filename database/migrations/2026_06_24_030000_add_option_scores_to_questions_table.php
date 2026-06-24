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
        Schema::table('questions', function (Blueprint $table) {
            $table->integer('score_a')->default(0)->after('option_a_image');
            $table->integer('score_b')->default(0)->after('option_b_image');
            $table->integer('score_c')->default(0)->after('option_c_image');
            $table->integer('score_d')->default(0)->after('option_d_image');
            $table->integer('score_e')->default(0)->after('option_e_image');
        });

        // Seed existing questions with option scores
        $questions = DB::table('questions')->get();
        foreach ($questions as $q) {
            $isTkp = false;
            if ($q->question_code_id) {
                $code = DB::table('question_codes')->where('id', $q->question_code_id)->first();
                if ($code && $code->code === 'TKP') {
                    $isTkp = true;
                }
            }
            
            $correctKey = strtoupper(trim($q->jawaban_benar));
            if ($isTkp) {
                $scores = [];
                foreach (['A', 'B', 'C', 'D', 'E'] as $key) {
                    if ($key === $correctKey) {
                        $scores[$key] = 5;
                    } else {
                        // Replicate legacy hash scoring
                        $scores[$key] = 1 + (crc32($q->id . $key) % 4);
                    }
                }
                
                DB::table('questions')->where('id', $q->id)->update([
                    'score_a' => $scores['A'],
                    'score_b' => $scores['B'],
                    'score_c' => $scores['C'],
                    'score_d' => $scores['D'],
                    'score_e' => $scores['E'],
                ]);
            } else {
                DB::table('questions')->where('id', $q->id)->update([
                    'score_a' => ($correctKey === 'A') ? 5 : 0,
                    'score_b' => ($correctKey === 'B') ? 5 : 0,
                    'score_c' => ($correctKey === 'C') ? 5 : 0,
                    'score_d' => ($correctKey === 'D') ? 5 : 0,
                    'score_e' => ($correctKey === 'E') ? 5 : 0,
                ]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('questions', function (Blueprint $table) {
            $table->dropColumn(['score_a', 'score_b', 'score_c', 'score_d', 'score_e']);
        });
    }
};
