<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\TryoutPackage;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class PdfImportTest extends TestCase
{
    use RefreshDatabase;

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
            'group' => 'SKD',
            'category' => 'CPNS',
        ]);

        $text = "BT\n/F1 12 Tf\n72 712 Td\n(SOAL: Pertanyaan nomor satu) Tj\nET\n" .
                "BT\n/F1 12 Tf\n72 692 Td\n(A. Pilihan A) Tj\nET\n" .
                "BT\n/F1 12 Tf\n72 672 Td\n(B. Pilihan B) Tj\nET\n" .
                "BT\n/F1 12 Tf\n72 652 Td\n(KUNCI: B) Tj\nET";

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
            ->post(route('admin.questions.importPdfPreview'), [
                'file' => $uploadedFile,
                'tryout_package_id' => $package->id,
            ]);

        // Clean up temp file
        @unlink($tempFile);

        $response->assertStatus(200);
        $response->assertViewIs('admin.questions.import_preview');
        $response->assertViewHas('questions');
        $response->assertSessionHas('temp_import_questions');
        $response->assertSessionHas('temp_import_package_id', $package->id);

        $sessionQuestions = session('temp_import_questions');
        $this->assertNotEmpty($sessionQuestions);
        $this->assertEquals('Pertanyaan nomor satu', trim($sessionQuestions[0]['soal']));
        $this->assertEquals('Pilihan A', trim($sessionQuestions[0]['opsi_a']));
        $this->assertEquals('B', $sessionQuestions[0]['jawaban_benar']);
    }
}
