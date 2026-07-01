<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\TryoutPackage;
use App\Models\Group;
use App\Models\QuestionCode;
use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;
use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\IOFactory;

class WordImportTest extends TestCase
{
    use RefreshDatabase;

    private function createTinyPng()
    {
        $tempImg = tempnam(sys_get_temp_dir(), 'test_img') . '.png';
        file_put_contents($tempImg, base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mNkYAAAAAYAAjCB0C8AAAAASUVORK5CYII='));
        return $tempImg;
    }

    private function buildMockTableDocx(array $rowsData)
    {
        $phpWord = new PhpWord();
        $section = $phpWord->addSection();
        $table = $section->addTable();

        // Header Row
        $row = $table->addRow();
        $row->addCell(2000)->addText("No");
        $row->addCell(2000)->addText("Jenis");
        $row->addCell(5000)->addText("Isi");
        $row->addCell(2000)->addText("Jawaban");

        foreach ($rowsData as $data) {
            $row = $table->addRow();
            $row->addCell(2000)->addText($data['no'] ?? '');
            $row->addCell(2000)->addText($data['jenis'] ?? '');
            
            $cell = $row->addCell(5000);
            if (!empty($data['image_path'])) {
                $cell->addImage($data['image_path']);
            } else {
                $cell->addText($data['isi'] ?? '');
            }
            
            $row->addCell(2000)->addText($data['jawaban'] ?? '');
        }

        $tempFile = tempnam(sys_get_temp_dir(), 'docx_test') . '.docx';
        @unlink($tempFile);
        $objWriter = IOFactory::createWriter($phpWord, 'Word2007');
        $objWriter->save($tempFile);

        return $tempFile;
    }

    private function buildMockParagraphDocx(array $paragraphs)
    {
        $phpWord = new PhpWord();
        $section = $phpWord->addSection();

        foreach ($paragraphs as $p) {
            if (is_array($p) && isset($p['image_path'])) {
                $section->addImage($p['image_path']);
            } else {
                $section->addText((string)$p);
            }
        }

        $tempFile = tempnam(sys_get_temp_dir(), 'docx_test') . '.docx';
        @unlink($tempFile);
        $objWriter = IOFactory::createWriter($phpWord, 'Word2007');
        $objWriter->save($tempFile);

        return $tempFile;
    }

    public function test_word_table_import_parsing_flow(): void
    {
        $admin = User::create([
            'name'     => 'Admin Test',
            'username' => 'admin_test',
            'email'    => 'admin_test@test.com',
            'password' => bcrypt('password'),
            'role'     => 'admin',
            'is_active'=> true,
        ]);

        $group = Group::create([
            'name' => 'SKD'
        ]);

        $code = QuestionCode::create([
            'group_id' => $group->id,
            'name' => 'Tes Karakteristik Pribadi',
            'code' => 'TKP'
        ]);

        $category = Category::create([
            'question_code_id' => $code->id,
            'name' => 'Pelayanan Publik'
        ]);

        $package = TryoutPackage::create([
            'nama' => 'Paket Tryout Test',
            'durasi_menit' => 30,
            'is_active' => true,
            'group_id' => $group->id,
            'group' => 'SKD',
            'question_code_id' => $code->id,
            'category_id' => $category->id,
            'category' => 'Pelayanan Publik',
        ]);

        $rowsData = [
            [
                'no' => '1',
                'jenis' => 'SOAL',
                'isi' => 'Apa tindakan Anda jika melihat warga kebingungan mengisi formulir?',
                'jawaban' => ''
            ],
            [
                'no' => '',
                'jenis' => 'JAWABAN',
                'isi' => 'Membiarkannya saja',
                'jawaban' => '1'
            ],
            [
                'no' => '',
                'jenis' => 'JAWABAN',
                'isi' => 'Menyuruh pulang',
                'jawaban' => '2'
            ],
            [
                'no' => '',
                'jenis' => 'JAWABAN',
                'isi' => 'Membantu langsung dengan sabar',
                'jawaban' => '5'
            ],
            [
                'no' => '',
                'jenis' => 'JAWABAN',
                'isi' => 'Memanggil satpam',
                'jawaban' => '3'
            ],
            [
                'no' => '',
                'jenis' => 'JAWABAN',
                'isi' => 'Menyarankan tanya orang lain',
                'jawaban' => '4'
            ]
        ];

        $tempFile = $this->buildMockTableDocx($rowsData);
        $uploadedFile = new UploadedFile(
            $tempFile,
            'test_questions_table.docx',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            null,
            true
        );

        $response = $this->actingAs($admin)
            ->post(route('admin.tryouts.import.word', $package->id), [
                'file' => $uploadedFile,
            ]);

        @unlink($tempFile);

        $response->assertStatus(200);
        $response->assertViewIs('admin.tryouts.import_preview');
        $response->assertViewHas('questions');
        $response->assertSessionHas('import_questions_' . $package->id);

        $sessionQuestions = session('import_questions_' . $package->id);
        $this->assertCount(1, $sessionQuestions);
        $this->assertEquals('Apa tindakan Anda jika melihat warga kebingungan mengisi formulir?', trim($sessionQuestions[0]['soal']));
        $this->assertEquals('Membantu langsung dengan sabar', trim($sessionQuestions[0]['opsi_c']));
        $this->assertEquals('C', $sessionQuestions[0]['jawaban_benar']);
        $this->assertEquals(1, $sessionQuestions[0]['score_a']);
        $this->assertEquals(2, $sessionQuestions[0]['score_b']);
        $this->assertEquals(5, $sessionQuestions[0]['score_c']);
        $this->assertEquals(3, $sessionQuestions[0]['score_d']);
        $this->assertEquals(4, $sessionQuestions[0]['score_e']);

        // Now simulate confirming the import
        $confirmResponse = $this->actingAs($admin)
            ->post(route('admin.tryouts.import.confirm', $package->id), [
                'q' => [
                    0 => [
                        'jawaban_benar' => 'C',
                        'tingkat_kesulitan' => 'sedang',
                        'soal' => 'Apa tindakan Anda jika melihat warga kebingungan mengisi formulir?',
                        'opsi_a' => 'Membiarkannya saja',
                        'opsi_b' => 'Menyuruh pulang',
                        'opsi_c' => 'Membantu langsung dengan sabar',
                        'opsi_d' => 'Memanggil satpam',
                        'opsi_e' => 'Menyarankan tanya orang lain',
                        'score_a' => 1,
                        'score_b' => 2,
                        'score_c' => 5,
                        'score_d' => 3,
                        'score_e' => 4,
                        'pembahasan' => '',
                    ]
                ]
            ]);

        $confirmResponse->assertRedirect(route('admin.tryouts.show', $package));

        // Assert that the question was created in the database and has the correct scores!
        $this->assertDatabaseHas('questions', [
            'tryout_package_id' => $package->id,
            'soal' => 'Apa tindakan Anda jika melihat warga kebingungan mengisi formulir?',
            'score_a' => 1,
            'score_b' => 2,
            'score_c' => 5,
            'score_d' => 3,
            'score_e' => 4,
            'jawaban_benar' => 'C',
        ]);
    }

    public function test_word_paragraph_import_fallback_flow(): void
    {
        $admin = User::create([
            'name'     => 'Admin Test',
            'username' => 'admin_test',
            'email'    => 'admin_test@test.com',
            'password' => bcrypt('password'),
            'role'     => 'admin',
            'is_active'=> true,
        ]);

        $group = Group::create([
            'name' => 'SKD'
        ]);

        $code = QuestionCode::create([
            'group_id' => $group->id,
            'name' => 'Tes Inteligensia Umum',
            'code' => 'TIU'
        ]);

        $category = Category::create([
            'question_code_id' => $code->id,
            'name' => 'Figural'
        ]);

        $package = TryoutPackage::create([
            'nama' => 'Paket Tryout Fallback Test',
            'durasi_menit' => 30,
            'is_active' => true,
            'group_id' => $group->id,
            'group' => 'SKD',
            'question_code_id' => $code->id,
            'category_id' => $category->id,
            'category' => 'Figural',
        ]);

        $paragraphs = [
            'SOAL: Pertanyaan nomor satu paragraf',
            'A. Opsi Paragraph A',
            'B. Opsi Paragraph B',
            'C. Opsi Paragraph C',
            'D. Opsi Paragraph D',
            'KUNCI: B'
        ];

        $tempFile = $this->buildMockParagraphDocx($paragraphs);
        $uploadedFile = new UploadedFile(
            $tempFile,
            'test_questions_paragraph.docx',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            null,
            true
        );

        $response = $this->actingAs($admin)
            ->post(route('admin.tryouts.import.word', $package->id), [
                'file' => $uploadedFile,
            ]);

        @unlink($tempFile);

        $response->assertRedirect();
        $response->assertSessionHas('error', "Format Word tidak sesuai dengan Template Import Smart CBT. Silakan unduh Template Word terlebih dahulu dan sesuaikan struktur dokumen sebelum melakukan import.");
    }

    public function test_word_import_figural_image_only_question(): void
    {
        $admin = User::create([
            'name'     => 'Admin Test',
            'username' => 'admin_test',
            'email'    => 'admin_test@test.com',
            'password' => bcrypt('password'),
            'role'     => 'admin',
            'is_active'=> true,
        ]);

        $group = Group::create([
            'name' => 'SKD'
        ]);

        $code = QuestionCode::create([
            'group_id' => $group->id,
            'name' => 'Tes Inteligensia Umum',
            'code' => 'TIU'
        ]);

        $category = Category::create([
            'question_code_id' => $code->id,
            'name' => 'Figural'
        ]);

        $package = TryoutPackage::create([
            'nama' => 'Paket Figural Test',
            'durasi_menit' => 30,
            'is_active' => true,
            'group_id' => $group->id,
            'group' => 'SKD',
            'question_code_id' => $code->id,
            'category_id' => $category->id,
            'category' => 'Figural',
        ]);

        $tinyPng = $this->createTinyPng();

        $rowsData = [
            [
                'no' => '1',
                'jenis' => 'SOAL',
                'image_path' => $tinyPng,
                'jawaban' => ''
            ],
            [
                'no' => '',
                'jenis' => 'JAWABAN',
                'image_path' => $tinyPng,
                'jawaban' => '5'
            ],
            [
                'no' => '',
                'jenis' => 'JAWABAN',
                'image_path' => $tinyPng,
                'jawaban' => '0'
            ]
        ];

        $tempFile = $this->buildMockTableDocx($rowsData);
        @unlink($tinyPng);

        $uploadedFile = new UploadedFile(
            $tempFile,
            'test_questions_figural.docx',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            null,
            true
        );

        $response = $this->actingAs($admin)
            ->post(route('admin.tryouts.import.word', $package->id), [
                'file' => $uploadedFile,
            ]);

        @unlink($tempFile);

        $response->assertStatus(200);
        $response->assertViewIs('admin.tryouts.import_preview');

        $sessionQuestions = session('import_questions_' . $package->id);
        $this->assertCount(1, $sessionQuestions);
        
        $this->assertEquals('', trim($sessionQuestions[0]['soal']));
        $this->assertEquals('', trim($sessionQuestions[0]['opsi_a']));
        
        // Assert that the images were successfully extracted
        $this->assertNotNull($sessionQuestions[0]['question_image']);
        $this->assertNotNull($sessionQuestions[0]['option_a_image']);
        $this->assertNotNull($sessionQuestions[0]['option_b_image']);
        
        // Ensure no review_required is set because extraction succeeded!
        $this->assertFalse($sessionQuestions[0]['review_required'] ?? false);

        // Confirm the import
        $confirmResponse = $this->actingAs($admin)
            ->post(route('admin.tryouts.import.confirm', $package->id), [
                'q' => [
                    0 => [
                        'jawaban_benar' => 'A',
                        'tingkat_kesulitan' => 'sedang',
                        'soal' => '',
                        'opsi_a' => '',
                        'opsi_b' => '',
                        'opsi_c' => '',
                        'opsi_d' => '',
                        'opsi_e' => '',
                        'score_a' => 5,
                        'score_b' => 0,
                        'score_c' => 0,
                        'score_d' => 0,
                        'score_e' => 0,
                        'pembahasan' => '',
                    ]
                ]
            ]);

        $confirmResponse->assertRedirect(route('admin.tryouts.show', $package));

        // Assert that the question was created in the database
        $this->assertDatabaseHas('questions', [
            'tryout_package_id' => $package->id,
            'soal' => '',
            'opsi_a' => '',
            'opsi_b' => '',
            'jawaban_benar' => 'A',
        ]);

        // Clean up temp images if any
        $tempImportPath = public_path('storage/temp_import');
        if (file_exists($tempImportPath)) {
            shell_exec("powershell -Command \"Remove-Item -Path '$tempImportPath\\*' -Force\"");
        }
    }

    public function test_word_paragraph_import_inline_options(): void
    {
        $admin = User::create([
            'name'     => 'Admin Test',
            'username' => 'admin_test',
            'email'    => 'admin_test@test.com',
            'password' => bcrypt('password'),
            'role'     => 'admin',
            'is_active'=> true,
        ]);

        $group = Group::create([
            'name' => 'SKD'
        ]);

        $code = QuestionCode::create([
            'group_id' => $group->id,
            'name' => 'Tes Inteligensia Umum',
            'code' => 'TIU'
        ]);

        $category = Category::create([
            'question_code_id' => $code->id,
            'name' => 'Figural'
        ]);

        $package = TryoutPackage::create([
            'nama' => 'Paket Tryout Inline Test',
            'durasi_menit' => 30,
            'is_active' => true,
            'group_id' => $group->id,
            'group' => 'SKD',
            'question_code_id' => $code->id,
            'category_id' => $category->id,
            'category' => 'Figural',
        ]);

        $paragraphs = [
            '1. Pertanyaan pertama dengan inline options',
            'A. Opsi Satu B. Opsi Dua C. Opsi Tiga',
            'D. Opsi Empat E. Opsi Lima',
            'Kunci Jawaban: [C]',
            'Pembahasan: Ini adalah penjelasan untuk jawaban C.'
        ];

        $tempFile = $this->buildMockParagraphDocx($paragraphs);
        $uploadedFile = new UploadedFile(
            $tempFile,
            'test_questions_inline.docx',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            null,
            true
        );

        $response = $this->actingAs($admin)
            ->post(route('admin.tryouts.import.word', $package->id), [
                'file' => $uploadedFile,
            ]);

        @unlink($tempFile);

        $response->assertRedirect();
        $response->assertSessionHas('error', "Format Word tidak sesuai dengan Template Import Smart CBT. Silakan unduh Template Word terlebih dahulu dan sesuaikan struktur dokumen sebelum melakukan import.");
    }

    public function test_word_table_import_explanation_and_kunci(): void
    {
        $admin = User::create([
            'name'     => 'Admin Test',
            'username' => 'admin_test',
            'email'    => 'admin_test@test.com',
            'password' => bcrypt('password'),
            'role'     => 'admin',
            'is_active'=> true,
        ]);

        $group = Group::create([
            'name' => 'SKD'
        ]);

        $code = QuestionCode::create([
            'group_id' => $group->id,
            'name' => 'Tes Inteligensia Umum',
            'code' => 'TIU'
        ]);

        $category = Category::create([
            'question_code_id' => $code->id,
            'name' => 'Figural'
        ]);

        $package = TryoutPackage::create([
            'nama' => 'Paket Tryout Table Expl Test',
            'durasi_menit' => 30,
            'is_active' => true,
            'group_id' => $group->id,
            'group' => 'SKD',
            'question_code_id' => $code->id,
            'category_id' => $category->id,
            'category' => 'Figural',
        ]);

        $rowsData = [
            [
                'no' => '1',
                'jenis' => 'SOAL',
                'isi' => 'Siapakah presiden pertama Indonesia?',
                'jawaban' => ''
            ],
            [
                'no' => '',
                'jenis' => 'JAWABAN',
                'isi' => 'Soeharto',
                'jawaban' => '0'
            ],
            [
                'no' => '',
                'jenis' => 'JAWABAN',
                'isi' => 'Soekarno',
                'jawaban' => '0'
            ],
            [
                'no' => '',
                'jenis' => 'JAWABAN',
                'isi' => 'B.J. Habibie',
                'jawaban' => '0'
            ],
            [
                'no' => '',
                'jenis' => 'KUNCI',
                'isi' => 'B',
                'jawaban' => ''
            ],
            [
                'no' => '',
                'jenis' => 'PEMBAHASAN',
                'isi' => 'Presiden pertama Indonesia adalah Ir. Soekarno.',
                'jawaban' => ''
            ]
        ];

        $tempFile = $this->buildMockTableDocx($rowsData);
        $uploadedFile = new UploadedFile(
            $tempFile,
            'test_questions_table_expl.docx',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            null,
            true
        );

        $response = $this->actingAs($admin)
            ->post(route('admin.tryouts.import.word', $package->id), [
                'file' => $uploadedFile,
            ]);

        @unlink($tempFile);

        $response->assertStatus(200);
        $response->assertViewIs('admin.tryouts.import_preview');

        $sessionQuestions = session('import_questions_' . $package->id);
        $this->assertCount(1, $sessionQuestions);
        $this->assertEquals('Siapakah presiden pertama Indonesia?', trim($sessionQuestions[0]['soal']));
        $this->assertEquals('Soekarno', trim($sessionQuestions[0]['opsi_b']));
        $this->assertEquals('B', $sessionQuestions[0]['jawaban_benar']);
        $this->assertEquals(5, $sessionQuestions[0]['score_b']);
        $this->assertEquals('Presiden pertama Indonesia adalah Ir. Soekarno.', trim($sessionQuestions[0]['pembahasan']));
    }
}
