<?php

namespace Tests\Feature;

use App\Models\ExamSession;
use App\Models\TryoutPackage;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExamViolationTest extends TestCase
{
    use RefreshDatabase;

    public function test_logging_violation_writes_to_database(): void
    {
        // 1. Create a user
        $user = User::factory()->create([
            'role' => 'peserta',
            'is_active' => true,
        ]);

        // 2. Create a TryoutPackage
        $package = TryoutPackage::create([
            'nama' => 'Test Tryout',
            'durasi_menit' => 30,
            'is_active' => true,
        ]);

        // 3. Create an ExamSession
        $session = ExamSession::create([
            'user_id' => $user->id,
            'tryout_package_id' => $package->id,
            'started_at' => now(),
            'status' => 'berlangsung',
            'violations_count' => 0,
        ]);

        // 4. Log violation
        $response = $this->actingAs($user)
            ->post(route('peserta.exam.logViolation', $session->id), [
                'tipe' => 'fullscreen_exit',
            ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'violations_count' => 1,
            'auto_submit' => false,
        ]);

        // 5. Verify database records
        $this->assertDatabaseHas('exam_violations', [
            'exam_session_id' => $session->id,
            'violation_type' => 'fullscreen_exit',
        ]);

        $this->assertEquals(1, $session->fresh()->violations_count);
    }

    public function test_logging_three_violations_triggers_auto_submit(): void
    {
        $user = User::factory()->create([
            'role' => 'peserta',
            'is_active' => true,
        ]);
        
        $package = TryoutPackage::create([
            'nama' => 'Test Tryout',
            'durasi_menit' => 30,
            'is_active' => true,
        ]);
        
        $session = ExamSession::create([
            'user_id' => $user->id,
            'tryout_package_id' => $package->id,
            'started_at' => now(),
            'status' => 'berlangsung',
            'violations_count' => 2, // Start with 2 violations
        ]);

        $response = $this->actingAs($user)
            ->post(route('peserta.exam.logViolation', $session->id), [
                'tipe' => 'tab_switch',
            ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'violations_count' => 3,
            'auto_submit' => true,
        ]);

        $this->assertDatabaseHas('exam_violations', [
            'exam_session_id' => $session->id,
            'violation_type' => 'tab_switch',
        ]);

        // Verify exam session is now 'kecurangan'
        $this->assertEquals('kecurangan', $session->fresh()->status);
    }
}
