<?php

namespace Tests\Unit\Repositories\Admin\Tag;

use App\Models\Tag;
use App\Models\TagTranslation;
use App\Repositories\Admin\Tag\TagSelectionRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TagSelectionRepositoryTest extends TestCase
{
    use RefreshDatabase;

    private TagSelectionRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = new TagSelectionRepository;
    }

    // ========================================
    // getPublishedActiveTagsForLocale
    // ========================================

    public function test_get_published_active_tags_for_locale_returns_correct_tags(): void
    {
        $tag1 = Tag::factory()->create(['is_active' => true]);
        TagTranslation::factory()->create([
            'tag_id' => $tag1->id,
            'locale' => 'fr',
            'name' => 'NASA',
            'slug' => 'nasa',
            'status' => 'published',
        ]);

        $tag2 = Tag::factory()->create(['is_active' => true]);
        TagTranslation::factory()->create([
            'tag_id' => $tag2->id,
            'locale' => 'fr',
            'name' => 'SpaceX',
            'slug' => 'spacex',
            'status' => 'published',
        ]);

        $result = $this->repository->getPublishedActiveTagsForLocale('fr');

        $this->assertCount(2, $result);
        $this->assertEquals('NASA', $result->first()->name);
        $this->assertEquals('nasa', $result->first()->slug);
    }

    public function test_get_published_active_tags_excludes_unpublished(): void
    {
        $tag = Tag::factory()->create(['is_active' => true]);
        TagTranslation::factory()->create([
            'tag_id' => $tag->id,
            'locale' => 'fr',
            'status' => 'draft',
        ]);

        $result = $this->repository->getPublishedActiveTagsForLocale('fr');

        $this->assertCount(0, $result);
    }

    public function test_get_published_active_tags_excludes_inactive(): void
    {
        $tag = Tag::factory()->create(['is_active' => false]);
        TagTranslation::factory()->create([
            'tag_id' => $tag->id,
            'locale' => 'fr',
            'status' => 'published',
        ]);

        $result = $this->repository->getPublishedActiveTagsForLocale('fr');

        $this->assertCount(0, $result);
    }

    public function test_get_published_active_tags_filters_by_locale(): void
    {
        $tag = Tag::factory()->create(['is_active' => true]);
        TagTranslation::factory()->create([
            'tag_id' => $tag->id,
            'locale' => 'fr',
            'name' => 'NASA FR',
            'status' => 'published',
        ]);
        TagTranslation::factory()->create([
            'tag_id' => $tag->id,
            'locale' => 'en',
            'name' => 'NASA EN',
            'status' => 'published',
        ]);

        $result = $this->repository->getPublishedActiveTagsForLocale('fr');

        $this->assertCount(1, $result);
        $this->assertEquals('NASA FR', $result->first()->name);
    }

    public function test_get_published_active_tags_orders_by_name(): void
    {
        $tag1 = Tag::factory()->create(['is_active' => true]);
        TagTranslation::factory()->create([
            'tag_id' => $tag1->id,
            'locale' => 'fr',
            'name' => 'Zénith',
            'slug' => 'zenith',
            'status' => 'published',
        ]);

        $tag2 = Tag::factory()->create(['is_active' => true]);
        TagTranslation::factory()->create([
            'tag_id' => $tag2->id,
            'locale' => 'fr',
            'name' => 'Alpha',
            'slug' => 'alpha',
            'status' => 'published',
        ]);

        $result = $this->repository->getPublishedActiveTagsForLocale('fr');

        $this->assertCount(2, $result);
        $this->assertEquals('Alpha', $result->first()->name);
        $this->assertEquals('Zénith', $result->last()->name);
    }

    // ========================================
    // searchByNameInLocale
    // ========================================

    public function test_search_by_name_finds_partial_matches(): void
    {
        $tag = Tag::factory()->create(['is_active' => true]);
        TagTranslation::factory()->create([
            'tag_id' => $tag->id,
            'locale' => 'fr',
            'name' => 'Observatoire Spatial',
            'slug' => 'observatoire-spatial',
            'status' => 'published',
        ]);

        $result = $this->repository->searchByNameInLocale('observa', 'fr', 10);

        $this->assertCount(1, $result);
        $this->assertEquals('Observatoire Spatial', $result->first()->name);
    }

    public function test_search_by_name_is_case_insensitive(): void
    {
        $tag = Tag::factory()->create(['is_active' => true]);
        TagTranslation::factory()->create([
            'tag_id' => $tag->id,
            'locale' => 'fr',
            'name' => 'NASA',
            'slug' => 'nasa',
            'status' => 'published',
        ]);

        $result = $this->repository->searchByNameInLocale('nasa', 'fr', 10);

        $this->assertCount(1, $result);
        $this->assertEquals('NASA', $result->first()->name);
    }

    public function test_search_by_name_respects_limit(): void
    {
        for ($i = 1; $i <= 15; $i++) {
            $tag = Tag::factory()->create(['is_active' => true]);
            TagTranslation::factory()->create([
                'tag_id' => $tag->id,
                'locale' => 'fr',
                'name' => "Tag Space {$i}",
                'slug' => "tag-space-{$i}",
                'status' => 'published',
            ]);
        }

        $result = $this->repository->searchByNameInLocale('space', 'fr', 10);

        $this->assertCount(10, $result);
    }

    public function test_search_by_name_returns_empty_when_no_match(): void
    {
        $tag = Tag::factory()->create(['is_active' => true]);
        TagTranslation::factory()->create([
            'tag_id' => $tag->id,
            'locale' => 'fr',
            'name' => 'NASA',
            'status' => 'published',
        ]);

        $result = $this->repository->searchByNameInLocale('inexistant', 'fr', 10);

        $this->assertCount(0, $result);
    }

    public function test_search_by_name_excludes_inactive_tags(): void
    {
        $tag = Tag::factory()->create(['is_active' => false]);
        TagTranslation::factory()->create([
            'tag_id' => $tag->id,
            'locale' => 'fr',
            'name' => 'Inactive NASA',
            'status' => 'published',
        ]);

        $result = $this->repository->searchByNameInLocale('nasa', 'fr', 10);

        $this->assertCount(0, $result);
    }

    // ========================================
    // translateSlugsToLocale
    // ========================================

    public function test_translate_slugs_returns_translated_slugs(): void
    {
        $tag = Tag::factory()->create(['is_active' => true]);
        TagTranslation::factory()->create([
            'tag_id' => $tag->id,
            'locale' => 'fr',
            'slug' => 'observatoire',
            'status' => 'published',
        ]);
        TagTranslation::factory()->create([
            'tag_id' => $tag->id,
            'locale' => 'en',
            'slug' => 'observatory',
            'status' => 'published',
        ]);

        $result = $this->repository->translateSlugsToLocale(['observatoire'], 'fr', 'en');

        $this->assertCount(1, $result);
        $this->assertEquals('observatory', $result[0]);
    }

    public function test_translate_slugs_handles_multiple_tags(): void
    {
        $tag1 = Tag::factory()->create(['is_active' => true]);
        TagTranslation::factory()->create([
            'tag_id' => $tag1->id,
            'locale' => 'fr',
            'slug' => 'observatoire',
            'status' => 'published',
        ]);
        TagTranslation::factory()->create([
            'tag_id' => $tag1->id,
            'locale' => 'en',
            'slug' => 'observatory',
            'status' => 'published',
        ]);

        $tag2 = Tag::factory()->create(['is_active' => true]);
        TagTranslation::factory()->create([
            'tag_id' => $tag2->id,
            'locale' => 'fr',
            'slug' => 'nasa',
            'status' => 'published',
        ]);
        TagTranslation::factory()->create([
            'tag_id' => $tag2->id,
            'locale' => 'en',
            'slug' => 'nasa',
            'status' => 'published',
        ]);

        $result = $this->repository->translateSlugsToLocale(['observatoire', 'nasa'], 'fr', 'en');

        $this->assertCount(2, $result);
        $this->assertContains('observatory', $result);
        $this->assertContains('nasa', $result);
    }

    public function test_translate_slugs_removes_tags_without_translation(): void
    {
        $tag = Tag::factory()->create(['is_active' => true]);
        TagTranslation::factory()->create([
            'tag_id' => $tag->id,
            'locale' => 'fr',
            'slug' => 'observatoire',
            'status' => 'published',
        ]);
        // Pas de traduction EN pour ce tag

        $result = $this->repository->translateSlugsToLocale(['observatoire'], 'fr', 'en');

        $this->assertCount(0, $result);
    }

    public function test_translate_slugs_returns_empty_when_empty_input(): void
    {
        $result = $this->repository->translateSlugsToLocale([], 'fr', 'en');

        $this->assertIsArray($result);
        $this->assertEmpty($result);
    }

    public function test_translate_slugs_excludes_inactive_tags(): void
    {
        $tag = Tag::factory()->create(['is_active' => false]);
        TagTranslation::factory()->create([
            'tag_id' => $tag->id,
            'locale' => 'fr',
            'slug' => 'observatoire',
            'status' => 'published',
        ]);
        TagTranslation::factory()->create([
            'tag_id' => $tag->id,
            'locale' => 'en',
            'slug' => 'observatory',
            'status' => 'published',
        ]);

        $result = $this->repository->translateSlugsToLocale(['observatoire'], 'fr', 'en');

        $this->assertCount(0, $result);
    }

    public function test_translate_slugs_excludes_unpublished_target_translations(): void
    {
        $tag = Tag::factory()->create(['is_active' => true]);
        TagTranslation::factory()->create([
            'tag_id' => $tag->id,
            'locale' => 'fr',
            'slug' => 'observatoire',
            'status' => 'published',
        ]);
        TagTranslation::factory()->create([
            'tag_id' => $tag->id,
            'locale' => 'en',
            'slug' => 'observatory',
            'status' => 'draft', // Non publié
        ]);

        $result = $this->repository->translateSlugsToLocale(['observatoire'], 'fr', 'en');

        $this->assertCount(0, $result);
    }
}
