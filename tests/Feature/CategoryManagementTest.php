<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Group;
use App\Models\QuestionCode;
use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CategoryManagementTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
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

        $this->group = Group::create(['name' => 'Grup Test']);
        
        $this->code = QuestionCode::create([
            'group_id' => $this->group->id,
            'code' => 'TEST',
            'name' => 'Test Code'
        ]);

        $this->category = Category::create([
            'question_code_id' => $this->code->id,
            'name' => 'Kategori Test'
        ]);
    }

    public function test_category_index_page_loads_successfully(): void
    {
        $response = $this->actingAs($this->admin)
            ->get(route('admin.categories.index'));

        $response->assertStatus(200);
        $response->assertSee('Kategori');
        $response->assertDontSee('Sub Kategori');
    }
}
