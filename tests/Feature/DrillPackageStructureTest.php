<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Group;
use App\Models\QuestionCode;
use App\Models\Category;
use App\Models\TryoutPackage;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DrillPackageStructureTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private User $peserta;
    private Group $group;
    private QuestionCode $code;
    private Category $category;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create([
            'role' => 'admin',
            'is_active' => true,
        ]);

        $this->group = Group::create(['name' => 'SKD']);

        $this->peserta = User::factory()->create([
            'role' => 'peserta',
            'is_active' => true,
            'group_id' => $this->group->id,
            'category' => 'CPNS',
        ]);
        
        $this->code = QuestionCode::create([
            'group_id' => $this->group->id,
            'code' => 'TWK',
            'name' => 'Tes Wawasan Kebangsaan'
        ]);

        $this->category = Category::create([
            'question_code_id' => $this->code->id,
            'name' => 'Pilar Negara'
        ]);
    }

    public function test_store_drill_package_resolves_relations_successfully(): void
    {
        $response = $this->actingAs($this->admin)
            ->post(route('admin.tryouts.store'), [
                'nama' => 'Drill Pancasila 1',
                'deskripsi' => 'Latihan soal pancasila',
                'jenis_ujian' => 'drill',
                'group_id' => $this->group->id,
                'question_code_id' => $this->code->id,
                'category_id' => $this->category->id,
                'attempt_limit' => 3,
                'durasi_menit' => 30,
                'is_active' => true,
                'exam_mode' => 'normal',
            ]);

        $response->assertRedirect();
        
        $this->assertDatabaseHas('tryout_packages', [
            'nama' => 'Drill Pancasila 1',
            'jenis_ujian' => 'drill',
            'group_id' => $this->group->id,
            'group' => 'SKD', // resolved automatically
            'category_id' => $this->category->id,
            'category' => 'Pilar Negara', // resolved automatically
            'question_code_id' => $this->code->id,
            'attempt_limit' => 3,
            'durasi_menit' => 30,
        ]);
    }

    public function test_store_tryout_package_stores_relations_successfully(): void
    {
        $response = $this->actingAs($this->admin)
            ->post(route('admin.tryouts.store'), [
                'nama' => 'Tryout Akbar SKD 1',
                'deskripsi' => 'Simulasi ujian lengkap',
                'jenis_ujian' => 'tryout',
                'group_id' => $this->group->id,
                'question_code_id' => $this->code->id,
                'category_id' => $this->category->id,
                'attempt_limit' => 2,
                'durasi_menit' => 90,
                'is_active' => true,
                'exam_mode' => 'normal',
            ]);

        $response->assertRedirect();

        $this->assertDatabaseHas('tryout_packages', [
            'nama' => 'Tryout Akbar SKD 1',
            'jenis_ujian' => 'tryout',
            'group_id' => $this->group->id,
            'group' => 'SKD', // resolved automatically
            'category_id' => $this->category->id,
            'category' => 'Pilar Negara', // resolved automatically
            'question_code_id' => $this->code->id,
            'attempt_limit' => 2,
            'durasi_menit' => 90,
        ]);
    }

    public function test_participant_dashboard_displays_drill_hierarchy(): void
    {
        // Create a Drill package
        $drill = TryoutPackage::create([
            'nama' => 'Drill Pancasila 1',
            'jenis_ujian' => 'drill',
            'group_id' => $this->group->id,
            'group' => 'SKD',
            'category_id' => $this->category->id,
            'category' => 'Pilar Negara',
            'question_code_id' => $this->code->id,
            'attempt_limit' => 3,
            'durasi_menit' => 30,
            'is_active' => true,
            'exam_mode' => 'normal',
        ]);

        $response = $this->actingAs($this->peserta)
            ->get(route('peserta.dashboard'));

        $response->assertStatus(200);
        $response->assertSee('Drill Pancasila 1');
        $response->assertSee('Pilar Negara');
    }

    public function test_participant_drills_listing_displays_drill_hierarchy(): void
    {
        // Create a Drill package
        $drill = TryoutPackage::create([
            'nama' => 'Drill Pancasila 1',
            'jenis_ujian' => 'drill',
            'group_id' => $this->group->id,
            'group' => 'SKD',
            'category_id' => $this->category->id,
            'category' => 'Pilar Negara',
            'question_code_id' => $this->code->id,
            'attempt_limit' => 3,
            'durasi_menit' => 30,
            'is_active' => true,
            'exam_mode' => 'normal',
        ]);

        $response = $this->actingAs($this->peserta)
            ->get(route('peserta.drills.index'));

        $response->assertStatus(200);
        $response->assertSee('Drill Pancasila 1');
        $response->assertSee('Pilar Negara');
    }
}
