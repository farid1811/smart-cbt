<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Group;
use App\Models\QuestionCode;
use App\Models\Category;
use App\Models\TryoutPackage;
use App\Models\Question;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class QuestionManagementTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private Group $group;
    private QuestionCode $code;
    private Category $category;
    private TryoutPackage $package;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('public');

        $this->admin = User::create([
            'name'     => 'Admin Test',
            'username' => 'admin_test',
            'email'    => 'admin@test.com',
            'password' => bcrypt('password'),
            'role'     => 'admin',
            'is_active'=> true,
        ]);

        $this->group = Group::create(['name' => 'SKD']);
        $this->code = QuestionCode::create([
            'group_id' => $this->group->id,
            'code' => 'TIU',
            'name' => 'Tes Inteligensia Umum'
        ]);
        $this->category = Category::create([
            'question_code_id' => $this->code->id,
            'name' => 'Analitis'
        ]);
        $this->package = TryoutPackage::create([
            'nama' => 'Paket Test',
            'jenis_ujian' => 'tryout',
            'group_id' => $this->group->id,
            'group' => 'SKD',
            'attempt_limit' => 2,
            'durasi_menit' => 90,
            'is_active' => true,
        ]);
    }

    public function test_store_question_with_explanation_text_only(): void
    {
        $response = $this->actingAs($this->admin)
            ->post(route('admin.questions.store'), [
                'tryout_package_id' => $this->package->id,
                'group_id' => $this->group->id,
                'question_code_id' => $this->code->id,
                'category_id' => $this->category->id,
                'soal' => 'Siapakah Presiden pertama RI?',
                'opsi_a' => 'Soeharto',
                'opsi_b' => 'Soekarno',
                'opsi_c' => 'Habibie',
                'opsi_d' => 'Gusdur',
                'jawaban_benar' => 'B',
                'pembahasan' => '<p>Pembahasan presiden pertama RI adalah Ir. Soekarno.</p>',
                'tingkat_kesulitan' => 'mudah',
            ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('questions', [
            'pembahasan' => '<p>Pembahasan presiden pertama RI adalah Ir. Soekarno.</p>',
            'explanation_image' => null,
        ]);
    }

    public function test_store_question_with_explanation_image_only(): void
    {
        $image = UploadedFile::fake()->create('explanation.jpg', 100, 'image/jpeg');

        $response = $this->actingAs($this->admin)
            ->post(route('admin.questions.store'), [
                'tryout_package_id' => $this->package->id,
                'group_id' => $this->group->id,
                'question_code_id' => $this->code->id,
                'category_id' => $this->category->id,
                'soal' => 'Pertanyaan gambar saja',
                'opsi_a' => 'Opsi A',
                'opsi_b' => 'Opsi B',
                'opsi_c' => 'Opsi C',
                'opsi_d' => 'Opsi D',
                'jawaban_benar' => 'A',
                'explanation_image' => $image,
                'tingkat_kesulitan' => 'sedang',
            ]);

        $response->assertRedirect();
        $question = Question::first();
        $this->assertNotNull($question->explanation_image);
        $this->assertNull($question->pembahasan);
    }

    public function test_store_question_with_both_text_and_image(): void
    {
        $image = UploadedFile::fake()->create('explanation2.png', 100, 'image/png');

        $response = $this->actingAs($this->admin)
            ->post(route('admin.questions.store'), [
                'tryout_package_id' => $this->package->id,
                'group_id' => $this->group->id,
                'question_code_id' => $this->code->id,
                'category_id' => $this->category->id,
                'soal' => 'Pertanyaan teks dan gambar',
                'opsi_a' => 'Opsi A',
                'opsi_b' => 'Opsi B',
                'opsi_c' => 'Opsi C',
                'opsi_d' => 'Opsi D',
                'jawaban_benar' => 'C',
                'pembahasan' => '<p>Penjelasan teks detail</p>',
                'explanation_image' => $image,
                'tingkat_kesulitan' => 'sulit',
            ]);

        $response->assertRedirect();
        $question = Question::first();
        $this->assertEquals('<p>Penjelasan teks detail</p>', $question->pembahasan);
        $this->assertNotNull($question->explanation_image);
    }

    public function test_update_question_text(): void
    {
        $question = Question::create([
            'tryout_package_id' => $this->package->id,
            'group_id' => $this->group->id,
            'question_code_id' => $this->code->id,
            'category_id' => $this->category->id,
            'soal' => 'Soal awal',
            'opsi_a' => 'A',
            'opsi_b' => 'B',
            'opsi_c' => 'C',
            'opsi_d' => 'D',
            'jawaban_benar' => 'A',
            'pembahasan' => 'Teks awal',
            'urutan' => 1,
            'tingkat_kesulitan' => 'mudah',
        ]);

        $response = $this->actingAs($this->admin)
            ->put(route('admin.questions.update', $question->id), [
                'tryout_package_id' => $this->package->id,
                'group_id' => $this->group->id,
                'question_code_id' => $this->code->id,
                'category_id' => $this->category->id,
                'soal' => 'Soal awal',
                'opsi_a' => 'A',
                'opsi_b' => 'B',
                'opsi_c' => 'C',
                'opsi_d' => 'D',
                'jawaban_benar' => 'A',
                'pembahasan' => 'Teks baru yang diperbarui',
                'tingkat_kesulitan' => 'mudah',
            ]);

        $response->assertRedirect();
        $this->assertEquals('Teks baru yang diperbarui', $question->fresh()->pembahasan);
    }

    public function test_update_question_image_does_not_clear_text(): void
    {
        $question = Question::create([
            'tryout_package_id' => $this->package->id,
            'group_id' => $this->group->id,
            'question_code_id' => $this->code->id,
            'category_id' => $this->category->id,
            'soal' => 'Soal awal',
            'opsi_a' => 'A',
            'opsi_b' => 'B',
            'opsi_c' => 'C',
            'opsi_d' => 'D',
            'jawaban_benar' => 'A',
            'pembahasan' => 'Teks awal yang harus tetap ada',
            'urutan' => 1,
            'tingkat_kesulitan' => 'mudah',
        ]);

        $newImage = UploadedFile::fake()->create('new_explanation.jpg', 100, 'image/jpeg');

        $response = $this->actingAs($this->admin)
            ->put(route('admin.questions.update', $question->id), [
                'tryout_package_id' => $this->package->id,
                'group_id' => $this->group->id,
                'question_code_id' => $this->code->id,
                'category_id' => $this->category->id,
                'soal' => 'Soal awal',
                'opsi_a' => 'A',
                'opsi_b' => 'B',
                'opsi_c' => 'C',
                'opsi_d' => 'D',
                'jawaban_benar' => 'A',
                'pembahasan' => 'Teks awal yang harus tetap ada',
                'explanation_image' => $newImage,
                'tingkat_kesulitan' => 'mudah',
            ]);

        $response->assertRedirect();
        $fresh = $question->fresh();
        $this->assertEquals('Teks awal yang harus tetap ada', $fresh->pembahasan);
        $this->assertNotNull($fresh->explanation_image);
    }
}
