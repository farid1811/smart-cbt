<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Group;
use App\Models\QuestionCode;
use App\Models\Category;
use App\Models\Question;
use App\Models\TryoutPackage;
use App\Models\ExamSession;
use App\Models\ExamAnswer;
use App\Models\Result;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ParticipantExamResultTest extends TestCase
{
    use RefreshDatabase;

    private User $peserta;
    private TryoutPackage $package;
    private Result $result;

    protected function setUp(): void
    {
        parent::setUp();

        // Create a Group
        $group = Group::create(['name' => 'SKD']);

        // Create a Participant
        $this->peserta = User::create([
            'name' => 'Peserta Ujian',
            'username' => 'peserta_cbt',
            'email' => 'peserta_cbt@smartcbt.com',
            'password' => bcrypt('password'),
            'role' => 'peserta',
            'group_id' => $group->id,
            'category' => 'CPNS',
            'is_active' => true,
        ]);

        // Create Question Code
        $code = QuestionCode::create([
            'group_id' => $group->id,
            'code' => 'TWK',
            'name' => 'Tes Wawasan Kebangsaan',
        ]);

        // Create Category
        $category = Category::create([
            'question_code_id' => $code->id,
            'name' => 'Pilar Negara',
        ]);

        // Create Tryout Package
        $this->package = TryoutPackage::create([
            'nama' => 'Tryout CPNS Mini',
            'deskripsi' => 'Simulasi mini.',
            'jenis_ujian' => 'tryout',
            'group_id' => $group->id,
            'group' => 'SKD',
            'question_code_id' => $code->id,
            'category_id' => $category->id,
            'category' => 'Pilar Negara',
            'durasi_menit' => 30,
            'is_active' => true,
            'attempt_limit' => 2,
        ]);

        // Create a Question
        $question = Question::create([
            'group_id' => $group->id,
            'question_code_id' => $code->id,
            'category_id' => $category->id,
            'tryout_package_id' => $this->package->id,
            'soal' => 'Apa dasar negara Indonesia?',
            'jawaban_benar' => 'A',
            'opsi_a' => 'Pancasila',
            'opsi_b' => 'UUD 1945',
            'opsi_c' => 'Proklamasi',
            'opsi_d' => 'Bhinneka Tunggal Ika',
            'urutan' => 1,
        ]);

        // Create Exam Session
        $session = ExamSession::create([
            'user_id' => $this->peserta->id,
            'tryout_package_id' => $this->package->id,
            'started_at' => now(),
            'status' => 'selesai',
            'ended_at' => now()->addMinutes(10),
            'durasi_detik' => 600,
            'soal_order' => [$question->id],
        ]);

        // Create Exam Answer
        ExamAnswer::create([
            'exam_session_id' => $session->id,
            'question_id' => $question->id,
            'jawaban' => 'A',
            'is_ragu' => false,
            'options_mapping' => ['A' => 'A', 'B' => 'B', 'C' => 'C', 'D' => 'D'],
        ]);

        // Create Result record (with category breakdown matching TWK score)
        $this->result = Result::create([
            'exam_session_id' => $session->id,
            'user_id' => $this->peserta->id,
            'tryout_package_id' => $this->package->id,
            'skor_twk' => 5,
            'skor_tiu' => 0,
            'skor_tkp' => 0,
            'skor_total' => 5,
            'jumlah_benar' => 1,
            'jumlah_salah' => 0,
            'jumlah_kosong' => 0,
            'category_scores' => [
                $code->id => [
                    'name' => 'Tes Wawasan Kebangsaan',
                    'kode' => 'TWK',
                    'score' => 5,
                    'benar' => 1,
                    'salah' => 0,
                    'kosong' => 0,
                    'total' => 1,
                ]
            ],
        ]);
    }

    /**
     * Verify that the participant exam result page loads successfully without any RelationNotFoundException.
     */
    public function test_participant_exam_result_loads_successfully(): void
    {
        $response = $this->actingAs($this->peserta)
            ->get(route('peserta.exam.result', $this->result));

        $response->assertStatus(200);
        $response->assertSee('Hasil Ujian');
        $response->assertSee($this->package->nama);
        $response->assertSee('Pilar Negara');
        $response->assertSee('Pancasila'); // Option A text
    }
}
