<?php

namespace Tests\Livewire\Admin\Place\Store;

use App\Livewire\Admin\Place\Store\PlaceStoreForm;
use App\Models\Category;
use App\Models\Place;
use App\Models\Tag;
use App\Models\TagTranslation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Tests\TestCase;

class PlaceStoreFormRelationsTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create();
        $this->actingAs($this->admin);
        app()->setLocale('fr');

        Storage::fake('public');
    }

    // ========================================
    // Relations - Categories & Tags
    // ========================================

    public function test_edit_mode_loads_associated_categories(): void
    {
        $place = Place::factory()->create();
        $category1 = Category::factory()->create();
        $category2 = Category::factory()->create();

        $place->categories()->attach([$category1->id, $category2->id]);

        $component = Livewire::test(PlaceStoreForm::class, ['placeId' => $place->id, 'placeRequestId' => null]);

        $categoryIds = $component->get('categoryIds');

        $this->assertCount(2, $categoryIds);
        $this->assertContains($category1->id, $categoryIds);
        $this->assertContains($category2->id, $categoryIds);
    }

    public function test_edit_mode_loads_associated_tags(): void
    {
        $place = Place::factory()->create();
        $tag1 = Tag::factory()->create();
        $tag2 = Tag::factory()->create();

        $place->tags()->attach([$tag1->id, $tag2->id]);

        $component = Livewire::test(PlaceStoreForm::class, ['placeId' => $place->id, 'placeRequestId' => null]);

        $tagIds = $component->get('tagIds');

        $this->assertCount(2, $tagIds);
        $this->assertContains($tag1->id, $tagIds);
        $this->assertContains($tag2->id, $tagIds);
    }

    public function test_mount_loads_available_categories(): void
    {
        $category1 = Category::factory()->create([
            'is_active' => true,
            'name' => 'Catégorie 1',
        ]);

        $category2 = Category::factory()->create([
            'is_active' => true,
            'name' => 'Catégorie 2',
        ]);

        $component = Livewire::test(PlaceStoreForm::class, ['placeId' => null, 'placeRequestId' => null]);

        $availableCategories = $component->get('availableCategories');

        $this->assertCount(2, $availableCategories);
    }

    public function test_mount_loads_available_tags(): void
    {
        $tag1 = Tag::factory()->create(['is_active' => true]);
        $tag2 = Tag::factory()->create(['is_active' => true]);

        TagTranslation::factory()->create([
            'tag_id' => $tag1->id,
            'locale' => 'fr',
            'name' => 'Tag 1',
        ]);

        TagTranslation::factory()->create([
            'tag_id' => $tag2->id,
            'locale' => 'fr',
            'name' => 'Tag 2',
        ]);

        $component = Livewire::test(PlaceStoreForm::class, ['placeId' => null, 'placeRequestId' => null]);

        $availableTags = $component->get('availableTags');

        $this->assertCount(2, $availableTags);
    }
}
