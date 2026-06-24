<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Group;
use App\Models\QuestionCode;
use App\Models\Category;
use App\Models\SubCategory;
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

    public function test_category_index_page_loads_successfully_without_subcategory_variable(): void
    {
        $response = $this->actingAs($this->admin)
            ->get(route('admin.categories.index'));

        $response->assertStatus(200);
        $response->assertSee('Kategori');
        $response->assertSee('Sub Kategori');
        $response->assertDontSee('3. Sub Kategori');
    }

    public function test_store_subcategory_redirects_to_category_tab(): void
    {
        $response = $this->actingAs($this->admin)
            ->post(route('admin.categories.storeSubCategory'), [
                'category_id' => $this->category->id,
                'name' => 'Sub Kategori Baru',
            ]);

        $response->assertRedirect(route('admin.categories.index', ['tab' => 'category']));
        $response->assertSessionHas('success', 'Sub Kategori berhasil ditambahkan.');

        $this->assertDatabaseHas('sub_categories', [
            'category_id' => $this->category->id,
            'name' => 'Sub Kategori Baru',
        ]);
    }

    public function test_update_subcategory_redirects_to_category_tab(): void
    {
        $subCategory = SubCategory::create([
            'category_id' => $this->category->id,
            'name' => 'Sub Kategori Lama',
        ]);

        $response = $this->actingAs($this->admin)
            ->put(route('admin.categories.updateSubCategory', $subCategory->id), [
                'category_id' => $this->category->id,
                'name' => 'Sub Kategori Diperbarui',
            ]);

        $response->assertRedirect(route('admin.categories.index', ['tab' => 'category']));
        $response->assertSessionHas('success', 'Sub Kategori berhasil diperbarui.');

        $this->assertDatabaseHas('sub_categories', [
            'id' => $subCategory->id,
            'name' => 'Sub Kategori Diperbarui',
        ]);
    }

    public function test_destroy_subcategory_redirects_to_category_tab(): void
    {
        $subCategory = SubCategory::create([
            'category_id' => $this->category->id,
            'name' => 'Sub Kategori Hapus',
        ]);

        $response = $this->actingAs($this->admin)
            ->delete(route('admin.categories.destroySubCategory', $subCategory->id));

        $response->assertRedirect(route('admin.categories.index', ['tab' => 'category']));
        $response->assertSessionHas('success', 'Sub Kategori berhasil dihapus.');

        $this->assertDatabaseMissing('sub_categories', [
            'id' => $subCategory->id,
        ]);
    }
}
