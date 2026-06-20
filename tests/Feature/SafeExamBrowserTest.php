<?php

namespace Tests\Feature;

use App\Models\ExamSession;
use App\Models\TryoutPackage;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SafeExamBrowserTest extends TestCase
{
    use RefreshDatabase;

    public function test_normal_exam_accessible_from_any_browser(): void
    {
        $user = User::factory()->create([
            'role' => 'peserta',
            'is_active' => true,
        ]);

        $package = TryoutPackage::create([
            'nama' => 'Normal Exam',
            'durasi_menit' => 30,
            'is_active' => true,
            'exam_mode' => 'normal',
        ]);

        $response = $this->actingAs($user)
            ->get(route('peserta.exam.start', $package->id));

        // Should successfully redirect to the exam session
        $response->assertStatus(302);
    }

    public function test_seb_exam_blocked_from_normal_browser(): void
    {
        $user = User::factory()->create([
            'role' => 'peserta',
            'is_active' => true,
        ]);

        $package = TryoutPackage::create([
            'nama' => 'SEB Required Exam',
            'durasi_menit' => 30,
            'is_active' => true,
            'exam_mode' => 'seb',
        ]);

        $response = $this->actingAs($user)
            ->get(route('peserta.exam.start', $package->id));

        // Should block access with 403 status and render the seb_required view
        $response->assertStatus(403);
        $response->assertSee('Safe Exam Browser Diperlukan');
    }

    public function test_seb_exam_accessible_with_seb_user_agent(): void
    {
        $user = User::factory()->create([
            'role' => 'peserta',
            'is_active' => true,
        ]);

        $package = TryoutPackage::create([
            'nama' => 'SEB Required Exam',
            'durasi_menit' => 30,
            'is_active' => true,
            'exam_mode' => 'seb',
        ]);

        $response = $this->actingAs($user)
            ->withHeaders([
                'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) SafeExamBrowser/3.0.1.282 Chrome/88.0.4324.190 Safari/537.36'
            ])
            ->get(route('peserta.exam.start', $package->id));

        // Should successfully redirect to start the session (meaning passed the middleware)
        $response->assertStatus(302);
    }

    public function test_seb_exam_accessible_with_request_hash_header(): void
    {
        $user = User::factory()->create([
            'role' => 'peserta',
            'is_active' => true,
        ]);

        $package = TryoutPackage::create([
            'nama' => 'SEB Required Exam',
            'durasi_menit' => 30,
            'is_active' => true,
            'exam_mode' => 'seb',
        ]);

        $response = $this->actingAs($user)
            ->withHeaders([
                'X-SafeExamBrowser-RequestHash' => 'somehashedvalue123456789'
            ])
            ->get(route('peserta.exam.start', $package->id));

        // Should successfully redirect to start the session (meaning passed the middleware)
        $response->assertStatus(302);
    }

    public function test_admin_can_download_seb_configuration(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
            'is_active' => true,
        ]);

        $package = TryoutPackage::create([
            'nama' => 'SEB Premium Exam',
            'durasi_menit' => 45,
            'is_active' => true,
            'exam_mode' => 'seb',
            'seb_quit_password' => 'quit123',
            'seb_browser_lockdown' => true,
        ]);

        $response = $this->actingAs($admin)
            ->get(route('admin.tryouts.sebConfig', $package->id));

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'application/x-safeexambrowser-config');
        $response->assertHeader('Content-Disposition', 'attachment; filename="seb_premium_exam.seb"');
        
        $xmlContent = $response->getContent();
        $this->assertStringContainsString('<key>startURL</key>', $xmlContent);
        $this->assertStringContainsString('<key>hashedQuitPassword</key>', $xmlContent);
        $this->assertStringContainsString(strtoupper(hash('sha256', 'quit123')), $xmlContent);
    }

    public function test_participant_can_download_seb_configuration(): void
    {
        $user = User::factory()->create([
            'role' => 'peserta',
            'is_active' => true,
        ]);

        $package = TryoutPackage::create([
            'nama' => 'SEB Participant Exam',
            'durasi_menit' => 45,
            'is_active' => true,
            'exam_mode' => 'seb',
            'seb_quit_password' => 'exit456',
        ]);

        $response = $this->actingAs($user)
            ->get(route('peserta.exam.sebConfig', $package->id));

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'application/x-safeexambrowser-config');
        
        $xmlContent = $response->getContent();
        $this->assertStringContainsString('<key>startURL</key>', $xmlContent);
        $this->assertStringContainsString(strtoupper(hash('sha256', 'exit456')), $xmlContent);
    }
}
