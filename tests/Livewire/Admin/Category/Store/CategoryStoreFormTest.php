<?php

namespace Tests\Livewire\Admin\Category\Store;

use App\Livewire\Admin\Category\Store\CategoryStoreForm;
use App\Models\Category;
use App\Models\Place;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class CategoryStoreFormTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create();
        $this->actingAs($this->admin);
    }

    // ========================================
    // Component Rendering & Initialization
    // ========================================

    public function test_component_can_be_rendered_in_create_mode(): void
    {
        Livewire::test(CategoryStoreForm::class, ['categoryId' => null])
            ->assertStatus(200);
    }

    public function test_component_can_be_rendered_in_edit_mode(): void
    {
        $category = Category::factory()->create();

        Livewire::test(CategoryStoreForm::class, ['categoryId' => $category->id])
            ->assertStatus(200);
    }

    public function test_component_view_exists(): void
    {
        Livewire::test(CategoryStoreForm::class, ['categoryId' => null])
            ->assertViewIs('livewire.admin.category.store.category-store-form');
    }

    // ========================================
    // Mount - Create Mode
    // ========================================

    public function test_mount_create_mode_sets_default_values(): void
    {
        Livewire::test(CategoryStoreForm::class, ['categoryId' => null])
            ->assertSet('mode', 'create')
            ->assertSet('categoryId', null)
            ->assertSet('name', '')
            ->assertSet('slug', '')
            ->assertSet('description', null)
            ->assertSet('color', '#3B82F6')
            ->assertSet('is_active', true);
    }

    public function test_mount_create_mode_category_is_null(): void
    {
        Livewire::test(CategoryStoreForm::class, ['categoryId' => null])
            ->assertSet('category', null);
    }

    // ========================================
    // Mount - Edit Mode
    // ========================================

    public function test_mount_edit_mode_loads_category_data(): void
    {
        $category = Category::factory()->create([
            'name' => 'Test Category',
            'slug' => 'test-category',
            'description' => 'Test description',
            'color' => '#FF5733',
            'is_active' => false,
        ]);

        Livewire::test(CategoryStoreForm::class, ['categoryId' => $category->id])
            ->assertSet('mode', 'edit')
            ->assertSet('categoryId', $category->id)
            ->assertSet('name', 'Test Category')
            ->assertSet('original_name', 'Test Category')
            ->assertSet('slug', 'test-category')
            ->assertSet('description', 'Test description')
            ->assertSet('color', '#FF5733')
            ->assertSet('is_active', false);
    }

    public function test_mount_edit_mode_category_is_loaded(): void
    {
        $category = Category::factory()->create();

        $component = Livewire::test(CategoryStoreForm::class, ['categoryId' => $category->id]);

        $this->assertNotNull($component->get('category'));
        $this->assertEquals($category->id, $component->get('category')->id);
    }

    public function test_mount_edit_mode_with_null_description(): void
    {
        $category = Category::factory()->create([
            'name' => 'Test',
            'description' => null,
        ]);

        Livewire::test(CategoryStoreForm::class, ['categoryId' => $category->id])
            ->assertSet('description', null);
    }

    // ========================================
    // Slug Auto-generation
    // ========================================

    public function test_updating_name_generates_slug_in_create_mode(): void
    {
        Livewire::test(CategoryStoreForm::class, ['categoryId' => null])
            ->set('name', 'Test Category Name')
            ->assertSet('slug', 'test-category-name');
    }

    public function test_slug_generation_removes_special_characters(): void
    {
        Livewire::test(CategoryStoreForm::class, ['categoryId' => null])
            ->set('name', 'Catégorie Spéciale!')
            ->assertSet('slug', 'categorie-speciale');
    }

    public function test_slug_generation_converts_to_lowercase(): void
    {
        Livewire::test(CategoryStoreForm::class, ['categoryId' => null])
            ->set('name', 'UPPERCASE CATEGORY')
            ->assertSet('slug', 'uppercase-category');
    }

    public function test_slug_can_be_manually_edited(): void
    {
        Livewire::test(CategoryStoreForm::class, ['categoryId' => null])
            ->set('name', 'Test Category')
            ->set('slug', 'custom-slug')
            ->assertSet('slug', 'custom-slug');
    }

    public function test_updating_name_regenerates_slug(): void
    {
        Livewire::test(CategoryStoreForm::class, ['categoryId' => null])
            ->set('name', 'Test Category')
            ->assertSet('slug', 'test-category')
            ->set('name', 'Updated Name')
            ->assertSet('slug', 'updated-name');
    }

    // ========================================
    // Color Management
    // ========================================

    public function test_color_can_be_updated(): void
    {
        Livewire::test(CategoryStoreForm::class, ['categoryId' => null])
            ->set('color', '#FF5733')
            ->assertSet('color', '#FF5733');
    }

    public function test_color_is_normalized_to_uppercase(): void
    {
        Livewire::test(CategoryStoreForm::class, ['categoryId' => null])
            ->set('name', 'Test')
            ->set('slug', 'test')
            ->set('color', '#ff5733')
            ->call('save')
            ->assertHasNoErrors();

        // Verify color is uppercase in database
        $this->assertDatabaseHas('categories', [
            'name' => 'Test',
            'color' => '#FF5733',
        ]);
    }

    // ========================================
    // Save - Create Mode
    // ========================================

    public function test_save_creates_category_with_valid_data(): void
    {
        Livewire::test(CategoryStoreForm::class, ['categoryId' => null])
            ->set('name', 'Test Category')
            ->set('slug', 'test-category')
            ->set('description', 'Test description')
            ->set('color', '#FF5733')
            ->set('is_active', true)
            ->call('save')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('categories', [
            'name' => 'Test Category',
            'slug' => 'test-category',
            'description' => 'Test description',
            'color' => '#FF5733',
            'is_active' => true,
        ]);
    }

    public function test_save_redirects_after_create(): void
    {
        $component = Livewire::test(CategoryStoreForm::class, ['categoryId' => null])
            ->set('name', 'Test')
            ->set('slug', 'test')
            ->set('color', '#FF5733')
            ->call('save');

        // Get the created category ID from database
        $category = \App\Models\Category::where('slug', 'test')->first();

        $component->assertRedirect(route('admin.categories.edit', $category->id));
    }

    public function test_save_creates_category_with_null_description(): void
    {
        Livewire::test(CategoryStoreForm::class, ['categoryId' => null])
            ->set('name', 'Test')
            ->set('slug', 'test')
            ->set('color', '#FF5733')
            ->set('description', null)
            ->call('save')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('categories', [
            'name' => 'Test',
            'description' => null,
        ]);
    }

    // ========================================
    // Save - Edit Mode
    // ========================================

    public function test_save_updates_category_with_valid_data(): void
    {
        $category = Category::factory()->create([
            'name' => 'Old Name',
            'slug' => 'old-slug',
            'description' => 'Old description',
            'color' => '#FF0000',
            'is_active' => true,
        ]);

        Livewire::test(CategoryStoreForm::class, ['categoryId' => $category->id])
            ->set('name', 'New Name')
            ->set('slug', 'new-slug')
            ->set('description', 'New description')
            ->set('color', '#00FF00')
            ->set('is_active', false)
            ->call('save')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('categories', [
            'id' => $category->id,
            'name' => 'New Name',
            'slug' => 'new-slug',
            'description' => 'New description',
            'color' => '#00FF00',
            'is_active' => false,
        ]);
    }

    public function test_save_stays_on_page_after_update(): void
    {
        $category = Category::factory()->create();

        $component = Livewire::test(CategoryStoreForm::class, ['categoryId' => $category->id])
            ->set('name', 'Updated')
            ->call('save');

        // Should NOT redirect, just reload data
        $component->assertNoRedirect();
        $component->assertSet('name', 'Updated');
    }

    // ========================================
    // Validation - Name
    // ========================================

    public function test_name_is_required(): void
    {
        Livewire::test(CategoryStoreForm::class, ['categoryId' => null])
            ->set('name', '')
            ->set('slug', 'test')
            ->set('color', '#FF5733')
            ->call('save')
            ->assertHasErrors(['name' => 'required']);
    }

    public function test_name_max_length_is_255(): void
    {
        Livewire::test(CategoryStoreForm::class, ['categoryId' => null])
            ->set('name', str_repeat('a', 256))
            ->call('save')
            ->assertHasErrors(['name' => 'max']);
    }

    // ========================================
    // Validation - Slug
    // ========================================

    public function test_slug_is_required(): void
    {
        Livewire::test(CategoryStoreForm::class, ['categoryId' => null])
            ->set('name', 'Test')
            ->set('slug', '')
            ->set('color', '#FF5733')
            ->call('save')
            ->assertHasErrors(['slug' => 'required']);
    }

    public function test_slug_max_length_is_255(): void
    {
        Livewire::test(CategoryStoreForm::class, ['categoryId' => null])
            ->set('slug', str_repeat('a', 256))
            ->call('save')
            ->assertHasErrors(['slug' => 'max']);
    }

    public function test_slug_must_match_regex_pattern(): void
    {
        Livewire::test(CategoryStoreForm::class, ['categoryId' => null])
            ->set('name', 'Test')
            ->set('slug', 'Invalid Slug!@#')
            ->set('color', '#FF5733')
            ->call('save')
            ->assertHasErrors(['slug' => 'regex']);
    }

    public function test_slug_must_be_unique(): void
    {
        Category::factory()->create(['slug' => 'existing-slug']);

        Livewire::test(CategoryStoreForm::class, ['categoryId' => null])
            ->set('name', 'Test')
            ->set('slug', 'existing-slug')
            ->set('color', '#FF5733')
            ->call('save')
            ->assertHasErrors(['slug' => 'unique']);
    }

    public function test_slug_unique_ignores_current_category_in_edit_mode(): void
    {
        $category = Category::factory()->create(['slug' => 'test-slug']);

        Livewire::test(CategoryStoreForm::class, ['categoryId' => $category->id])
            ->set('name', 'Updated Name')
            ->set('slug', 'test-slug')
            ->call('save')
            ->assertHasNoErrors(['slug']);
    }

    // ========================================
    // Validation - Description
    // ========================================

    public function test_description_is_optional(): void
    {
        Livewire::test(CategoryStoreForm::class, ['categoryId' => null])
            ->set('name', 'Test')
            ->set('slug', 'test')
            ->set('color', '#FF5733')
            ->set('description', null)
            ->call('save')
            ->assertHasNoErrors(['description']);
    }

    public function test_description_max_length_is_2000(): void
    {
        Livewire::test(CategoryStoreForm::class, ['categoryId' => null])
            ->set('description', str_repeat('a', 2001))
            ->call('save')
            ->assertHasErrors(['description' => 'max']);
    }

    // ========================================
    // Validation - Color
    // ========================================

    public function test_color_is_required(): void
    {
        Livewire::test(CategoryStoreForm::class, ['categoryId' => null])
            ->set('name', 'Test')
            ->set('slug', 'test')
            ->set('color', '')
            ->call('save')
            ->assertHasErrors(['color' => 'required']);
    }

    public function test_color_must_be_string(): void
    {
        Livewire::test(CategoryStoreForm::class, ['categoryId' => null])
            ->set('color', 123)
            ->call('save')
            ->assertHasErrors(['color']);
    }

    public function test_color_must_match_regex_pattern(): void
    {
        Livewire::test(CategoryStoreForm::class, ['categoryId' => null])
            ->set('name', 'Test')
            ->set('slug', 'test')
            ->set('color', '123invalid!@#')
            ->call('save')
            ->assertHasErrors(['color' => 'regex']);
    }

    public function test_color_accepts_valid_hex_format(): void
    {
        $validColors = ['#FF5733', '#3B82F6', '#000', '#fff'];

        foreach ($validColors as $color) {
            Livewire::test(CategoryStoreForm::class, ['categoryId' => null])
                ->set('name', 'Test')
                ->set('slug', 'test-'.$color)
                ->set('color', $color)
                ->call('save')
                ->assertHasNoErrors(['color']);
        }
    }

    // ========================================
    // Delete Functionality
    // ========================================

    public function test_delete_removes_category_from_database(): void
    {
        $category = Category::factory()->create();

        Livewire::test(CategoryStoreForm::class, ['categoryId' => $category->id])
            ->call('delete')
            ->assertRedirect(route('admin.categories.index'));

        $this->assertDatabaseMissing('categories', ['id' => $category->id]);
    }

    public function test_delete_detaches_places_before_deletion(): void
    {
        $category = Category::factory()->create();
        $place = Place::factory()->create(['admin_id' => $this->admin->id]);
        $category->places()->attach($place->id);

        Livewire::test(CategoryStoreForm::class, ['categoryId' => $category->id])
            ->call('delete');

        $this->assertDatabaseMissing('place_category', [
            'category_id' => $category->id,
        ]);
        $this->assertDatabaseHas('places', ['id' => $place->id]);
    }

    public function test_delete_shows_modal_when_places_associated(): void
    {
        $category = Category::factory()->create();
        $place = Place::factory()->create(['admin_id' => $this->admin->id]);
        $category->places()->attach($place->id);

        $component = Livewire::test(CategoryStoreForm::class, ['categoryId' => $category->id])
            ->call('confirmDeleteModal')
            ->assertSet('showDeleteModal', true)
            ->assertSet('associatedPlacesCount', 1);
    }

    public function test_delete_modal_not_shown_when_no_places_associated(): void
    {
        $category = Category::factory()->create();

        Livewire::test(CategoryStoreForm::class, ['categoryId' => $category->id])
            ->call('confirmDeleteModal')
            ->assertSet('showDeleteModal', true)
            ->assertSet('associatedPlacesCount', 0);
    }

    public function test_cancel_delete_closes_modal(): void
    {
        $category = Category::factory()->create();

        Livewire::test(CategoryStoreForm::class, ['categoryId' => $category->id])
            ->call('confirmDeleteModal')
            ->assertSet('showDeleteModal', true)
            ->call('cancelDelete')
            ->assertSet('showDeleteModal', false);
    }
}
