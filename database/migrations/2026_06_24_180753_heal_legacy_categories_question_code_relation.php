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
        // Only run healing if there are actually legacy categories to heal
        if (DB::table('categories')->whereNull('question_code_id')->exists()) {
            // 1. Ensure groups exist and get their IDs
            $skdGroupId = DB::table('groups')->where('name', 'SKD')->value('id');
            if (!$skdGroupId) {
                $skdGroupId = DB::table('groups')->insertGetId([
                    'name' => 'SKD',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            $snbtGroupId = DB::table('groups')->where('name', 'SNBT')->value('id');
            if (!$snbtGroupId) {
                $snbtGroupId = DB::table('groups')->insertGetId([
                    'name' => 'SNBT',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            // 2. Ensure default question codes exist and get their IDs
            $defaultCodes = [
                'TWK' => ['group_id' => $skdGroupId, 'name' => 'Tes Wawasan Kebangsaan'],
                'TIU' => ['group_id' => $skdGroupId, 'name' => 'Tes Intelegensia Umum'],
                'TKP' => ['group_id' => $skdGroupId, 'name' => 'Tes Karakteristik Pribadi'],
                
                'TPS-PU' => ['group_id' => $snbtGroupId, 'name' => 'Penalaran Umum'],
                'TPS-PPU' => ['group_id' => $snbtGroupId, 'name' => 'Pengetahuan & Pemahaman Umum'],
                'TPS-PK' => ['group_id' => $snbtGroupId, 'name' => 'Pengetahuan Kuantitatif'],
                'TPS-PBM' => ['group_id' => $snbtGroupId, 'name' => 'Pemahaman Bacaan & Menulis'],
                'LBI' => ['group_id' => $snbtGroupId, 'name' => 'Literasi Bahasa Indonesia'],
                'LBIng' => ['group_id' => $snbtGroupId, 'name' => 'Literasi Bahasa Inggris'],
                'PM' => ['group_id' => $snbtGroupId, 'name' => 'Penalaran Matematika'],
            ];

            $codeIds = [];
            foreach ($defaultCodes as $code => $data) {
                $existing = DB::table('question_codes')->where('code', $code)->first();
                if ($existing) {
                    $codeIds[$code] = $existing->id;
                } else {
                    $codeIds[$code] = DB::table('question_codes')->insertGetId([
                        'group_id' => $data['group_id'],
                        'name' => $data['name'],
                        'code' => $code,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }

            // 3. Mapping dictionary for legacy categories
            $mappings = [
                'TWK' => [
                    'pilar negara', 'nasionalisme', 'bela negara', 'integritasi', 'integritas',
                    'pancasila', 'uud 1945', 'nkri', 'bhinneka tunggal ika',
                    'sejarah', 'bahasa indonesia'
                ],
                'TIU' => [
                    'kemampuan verbal', 'kemampuan numerik', 'kemampuan figural',
                    'sinonim', 'antonim', 'analogi', 'deret angka', 'berhitung cepat',
                    'soal cerita', 'silogisme', 'analitis'
                ],
                'TKP' => [
                    'pelayanan publik', 'jejaring kerja', 'sosial budaya',
                    'teknologi informasi', 'profesionalisme', 'anti radikalisme'
                ],
                'TPS-PU' => [
                    'penalaran induktif', 'penalaran deduktif', 'penalaran umum', 'tps'
                ],
                'PM' => [
                    'penalaran matematika', 'aljabar dan kalkulus', 'aljabar', 'kalkulus'
                ],
                'TPS-PK' => [
                    'pengetahuan kuantitatif', 'kuantitatif'
                ],
                'TPS-PPU' => [
                    'pengetahuan & pemahaman umum', 'pengetahuan dan pemahaman umum'
                ],
                'TPS-PBM' => [
                    'pemahaman bacaan & menulis', 'pemahaman bacaan dan menulis'
                ],
                'LBI' => [
                    'literasi bahasa indonesia'
                ],
                'LBIng' => [
                    'literasi bahasa inggris'
                ],
            ];

            // 4. Update legacy categories where question_code_id is null
            $categories = DB::table('categories')->whereNull('question_code_id')->get();
            foreach ($categories as $cat) {
                $catNameLower = strtolower($cat->name);
                $matchedCode = null;

                // Try to find a match in our mapping dictionary
                foreach ($mappings as $code => $keywords) {
                    foreach ($keywords as $keyword) {
                        if (str_contains($catNameLower, $keyword)) {
                            $matchedCode = $code;
                            break 2;
                        }
                    }
                }

                // Update the category if a match was found
                if ($matchedCode && isset($codeIds[$matchedCode])) {
                    DB::table('categories')
                        ->where('id', $cat->id)
                        ->update([
                            'question_code_id' => $codeIds[$matchedCode],
                            'updated_at' => now(),
                        ]);
                }
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No down migration logic needed as this is a one-way data healing migration
    }
};
