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

class PdfImportTest extends TestCase
{
    use RefreshDatabase;

    private Group $group;
    private QuestionCode $code;
    private Category $category;

    protected function setUp(): void
    {
        parent::setUp();
        $this->group = Group::create(['name' => 'SKD']);
        $this->code = QuestionCode::create([
            'group_id' => $this->group->id,
            'name' => 'Tes Inteligensia Umum',
            'code' => 'TIU'
        ]);
        $this->category = Category::create([
            'question_code_id' => $this->code->id,
            'name' => 'Figural'
        ]);
    }

    private function buildPdf($text) {
        $objs = [
            1 => "<< /Type /Catalog /Pages 2 0 R >>",
            2 => "<< /Type /Pages /Kids [3 0 R] /Count 1 >>",
            3 => "<< /Type /Page /Parent 2 0 R /MediaBox [0 0 595 842] /Resources << /Font << /F1 << /Type /Font /Subtype /Type1 /BaseFont /Helvetica >> >> >> /Contents 4 0 R >>",
        ];

        $stream = "BT\n/F1 12 Tf\n72 712 Td\n" . $text . "\nET";
        $objs[4] = "<< /Length " . strlen($stream) . " >>\nstream\n" . $stream . "\nendstream";

        $pdf = "%PDF-1.4\n";
        $offsets = [];
        foreach ($objs as $id => $content) {
            $offsets[$id] = strlen($pdf);
            $pdf .= $id . " 0 obj\n" . $content . "\nendobj\n";
        }

        $xrefOffset = strlen($pdf);
        $pdf .= "xref\n0 " . (count($objs) + 1) . "\n";
        $pdf .= "0000000000 65535 f\n";
        foreach ($objs as $id => $content) {
            $pdf .= sprintf("%010d 00000 n\n", $offsets[$id]);
        }
        $pdf .= "trailer\n<< /Size " . (count($objs) + 1) . " /Root 1 0 R >>\n";
        $pdf .= "startxref\n" . $xrefOffset . "\n";
        $pdf .= "%%EOF";

        return $pdf;
    }

    public function test_pdf_import_parsing_flow(): void
    {
        $admin = User::create([
            'name'     => 'Admin Test',
            'username' => 'admin_test',
            'email'    => 'admin_test@test.com',
            'password' => bcrypt('password'),
            'role'     => 'admin',
            'is_active'=> true,
        ]);

        $package = TryoutPackage::create([
            'nama' => 'Paket Tryout Test',
            'durasi_menit' => 30,
            'is_active' => true,
            'group_id' => $this->group->id,
            'group' => 'SKD',
            'question_code_id' => $this->code->id,
            'category_id' => $this->category->id,
            'category' => 'Figural',
        ]);

        $text = "BT\n/F1 12 Tf\n72 712 Td\n(1 | SOAL | Pertanyaan nomor satu |) Tj\nET\n" .
                "BT\n/F1 12 Tf\n72 692 Td\n(1 | JAWABAN | Pilihan A | 0) Tj\nET\n" .
                "BT\n/F1 12 Tf\n72 672 Td\n(1 | JAWABAN | Pilihan B | 1) Tj\nET";

        $pdfContent = $this->buildPdf($text);

        $tempFile = tempnam(sys_get_temp_dir(), 'pdf_test');
        file_put_contents($tempFile, $pdfContent);

        $uploadedFile = new UploadedFile(
            $tempFile,
            'test_questions.pdf',
            'application/pdf',
            null,
            true
        );

        $response = $this->actingAs($admin)
            ->post(route('admin.tryouts.import.pdf', $package->id), [
                'file' => $uploadedFile,
            ]);

        // Clean up temp file
        @unlink($tempFile);

        $response->assertStatus(200);
        $response->assertViewIs('admin.tryouts.import_preview');
        $response->assertViewHas('questions');
        $response->assertSessionHas('import_questions_' . $package->id);

        $sessionQuestions = session('import_questions_' . $package->id);
        $this->assertNotEmpty($sessionQuestions);
        $this->assertEquals('Pertanyaan nomor satu', trim($sessionQuestions[0]['soal']));
        $this->assertEquals('Pilihan A', trim($sessionQuestions[0]['opsi_a']));
        $this->assertEquals('B', $sessionQuestions[0]['jawaban_benar']);
    }

    private function buildPdfWithImages($text, $numImages) {
        $objs = [
            1 => "<< /Type /Catalog /Pages 2 0 R >>",
            2 => "<< /Type /Pages /Kids [3 0 R] /Count 1 >>",
        ];
        
        $pageResources = "<< /Font << /F1 << /Type /Font /Subtype /Type1 /BaseFont /Helvetica >> >>";
        $xobjects = [];
        $nextObjId = 5;
        
        for ($i = 0; $i < $numImages; $i++) {
            $imgId = $nextObjId++;
            $xobjects["/Img" . $i] = $imgId . " 0 R";
            $objs[$imgId] = "<< /Type /XObject /Subtype /Image /Width 10 /Height 10 /ColorSpace /DeviceRGB /BitsPerComponent 8 /Length 10 >>\nstream\n1234567890\nendstream";
        }
        
        if (!empty($xobjects)) {
            $pageResources .= " /XObject << ";
            foreach ($xobjects as $name => $ref) {
                $pageResources .= $name . " " . $ref . " ";
            }
            $pageResources .= " >>";
        }
        $pageResources .= " >>";
        
        $objs[3] = "<< /Type /Page /Parent 2 0 R /MediaBox [0 0 595 842] /Resources " . $pageResources . " /Contents 4 0 R >>";
        
        $stream = "BT\n/F1 12 Tf\n72 712 Td\n" . $text . "\nET";
        $objs[4] = "<< /Length " . strlen($stream) . " >>\nstream\n" . $stream . "\nendstream";
        
        ksort($objs);
        
        $pdf = "%PDF-1.4\n";
        $offsets = [];
        foreach ($objs as $id => $content) {
            $offsets[$id] = strlen($pdf);
            $pdf .= $id . " 0 obj\n" . $content . "\nendobj\n";
        }
        
        $xrefOffset = strlen($pdf);
        $pdf .= "xref\n0 " . (count($objs) + 1) . "\n";
        $pdf .= "0000000000 65535 f\n";
        foreach ($objs as $id => $content) {
            $pdf .= sprintf("%010d 00000 n\n", $offsets[$id]);
        }
        $pdf .= "trailer\n<< /Size " . (count($objs) + 1) . " /Root 1 0 R >>\n";
        $pdf .= "startxref\n" . $xrefOffset . "\n";
        $pdf .= "%%EOF";
        
        return $pdf;
    }

    public function test_pdf_import_figural_image_only_question(): void
    {
        $admin = User::create([
            'name'     => 'Admin Test',
            'username' => 'admin_test',
            'email'    => 'admin_test@test.com',
            'password' => bcrypt('password'),
            'role'     => 'admin',
            'is_active'=> true,
        ]);

        $package = TryoutPackage::create([
            'nama' => 'Paket Figural Test',
            'durasi_menit' => 30,
            'is_active' => true,
            'group_id' => $this->group->id,
            'group' => 'SKD',
            'question_code_id' => $this->code->id,
            'category_id' => $this->category->id,
            'category' => 'Figural',
        ]);

        $text = "BT\n/F1 12 Tf\n72 712 Td\n(1 | SOAL | |) Tj\nET\n" .
                "BT\n/F1 12 Tf\n72 692 Td\n(1 | JAWABAN | | 1) Tj\nET\n" .
                "BT\n/F1 12 Tf\n72 672 Td\n(1 | JAWABAN | | 0) Tj\nET";

        $pdfContent = $this->buildPdfWithImages($text, 3);

        $tempFile = tempnam(sys_get_temp_dir(), 'pdf_test');
        file_put_contents($tempFile, $pdfContent);

        $uploadedFile = new UploadedFile(
            $tempFile,
            'figural_questions.pdf',
            'application/pdf',
            null,
            true
        );

        $response = $this->actingAs($admin)
            ->post(route('admin.tryouts.import.pdf', $package->id), [
                'file' => $uploadedFile,
            ]);

        @unlink($tempFile);

        $response->assertStatus(200);
        $sessionQuestions = session('import_questions_' . $package->id);
        $this->assertNotEmpty($sessionQuestions);
        
        $q = $sessionQuestions[0];
        $this->assertEquals('', trim($q['soal']));
        
        $this->assertNotNull($q['question_image']);
        $this->assertNotNull($q['option_a_image']);
        $this->assertNotNull($q['option_b_image']);
        
        $this->assertStringContainsString('pdf_img', $q['question_image']);
        $this->assertStringContainsString('pdf_img', $q['option_a_image']);
        $this->assertStringContainsString('pdf_img', $q['option_b_image']);
    }
}
