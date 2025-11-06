<?php

namespace Tests\Unit\Repositories\Web\Tag;

use App\Models\Tag;
use App\Models\TagTranslation;
use App\Repositories\Web\Tag\TagSelectionRepository;
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

    public function test_get_published_active_tags_for_locale_returns_correct_tags(): void
    {
        // Créer des tags publiés et actifs
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

    public function test_get_published_active_tags_excludes_unpublished_tags(): void
    {
        $tag = Tag::factory()->create(['is_active' => true]);
        TagTranslation::factory()->create([
            'tag_id' => $tag->id,
            'locale' => 'fr',
            'name' => 'Draft Tag',
            'slug' => 'draft-tag',
            'status' => 'draft',
        ]);

        $result = $this->repository->getPublishedActiveTagsForLocale('fr');

        $this->assertCount(0, $result);
    }

    public function test_get_published_active_tags_excludes_inactive_tags(): void
    {
        $tag = Tag::factory()->create(['is_active' => false]);
        TagTranslation::factory()->create([
            'tag_id' => $tag->id,
            'locale' => 'fr',
            'name' => 'Inactive Tag',
            'slug' => 'inactive-tag',
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
            'name' => 'NASA France',
            'slug' => 'nasa',
            'status' => 'published',
        ]);
        TagTranslation::factory()->create([
            'tag_id' => $tag->id,
            'locale' => 'en',
            'name' => 'NASA English',
            'slug' => 'nasa',
            'status' => 'published',
        ]);

        $resultFr = $this->repository->getPublishedActiveTagsForLocale('fr');
        $resultEn = $this->repository->getPublishedActiveTagsForLocale('en');

        $this->assertCount(1, $resultFr);
        $this->assertCount(1, $resultEn);
        $this->assertEquals('NASA France', $resultFr->first()->name);
        $this->assertEquals('NASA English', $resultEn->first()->name);
    }

    public function test_get_published_active_tags_orders_by_name(): void
    {
        $tag1 = Tag::factory()->create(['is_active' => true]);
        TagTranslation::factory()->create([
            'tag_id' => $tag1->id,
            'locale' => 'fr',
            'name' => 'Zulu',
            'slug' => 'zulu',
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

        $this->assertEquals('Alpha', $result->first()->name);
        $this->assertEquals('Zulu', $result->last()->name);
    }

    public function test_get_by_slug_list_returns_matching_tags(): void
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

        $result = $this->repository->getBySlugListInLocale(['nasa', 'spacex'], 'fr');

        $this->assertCount(2, $result);
    }

    public function test_get_by_slug_list_returns_empty_collection_for_empty_array(): void
    {
        $result = $this->repository->getBySlugListInLocale([], 'fr');

        $this->assertCount(0, $result);
        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Collection::class, $result);
    }

    public function test_get_by_slug_list_excludes_non_matching_slugs(): void
    {
        $tag = Tag::factory()->create(['is_active' => true]);
        TagTranslation::factory()->create([
            'tag_id' => $tag->id,
            'locale' => 'fr',
            'name' => 'NASA',
            'slug' => 'nasa',
            'status' => 'published',
        ]);

        $result = $this->repository->getBySlugListInLocale(['nasa', 'nonexistent'], 'fr');

        $this->assertCount(1, $result);
        $this->assertEquals('nasa', $result->first()->slug);
    }

    public function test_validate_slugs_exist_returns_valid_slugs(): void
    {
        $tag1 = Tag::factory()->create(['is_active' => true]);
        TagTranslation::factory()->create([
            'tag_id' => $tag1->id,
            'locale' => 'fr',
            'slug' => 'nasa',
            'status' => 'published',
        ]);

        $tag2 = Tag::factory()->create(['is_active' => true]);
        TagTranslation::factory()->create([
            'tag_id' => $tag2->id,
            'locale' => 'fr',
            'slug' => 'spacex',
            'status' => 'published',
        ]);

        $result = $this->repository->validateSlugsExistInLocale(['nasa', 'spacex', 'invalid'], 'fr');

        $this->assertCount(2, $result);
        $this->assertContains('nasa', $result);
        $this->assertContains('spacex', $result);
        $this->assertNotContains('invalid', $result);
    }

    public function test_validate_slugs_exist_returns_empty_array_for_empty_input(): void
    {
        $result = $this->repository->validateSlugsExistInLocale([], 'fr');

        $this->assertIsArray($result);
        $this->assertEmpty($result);
    }

    public function test_validate_slugs_exist_filters_by_locale(): void
    {
        $tag = Tag::factory()->create(['is_active' => true]);
        TagTranslation::factory()->create([
            'tag_id' => $tag->id,
            'locale' => 'fr',
            'slug' => 'nasa',
            'status' => 'published',
        ]);

        $resultFr = $this->repository->validateSlugsExistInLocale(['nasa'], 'fr');
        $resultEn = $this->repository->validateSlugsExistInLocale(['nasa'], 'en');

        $this->assertCount(1, $resultFr);
        $this->assertCount(0, $resultEn);
    }

    public function test_validate_slugs_exist_excludes_inactive_tags(): void
    {
        $tag = Tag::factory()->create(['is_active' => false]);
        TagTranslation::factory()->create([
            'tag_id' => $tag->id,
            'locale' => 'fr',
            'slug' => 'inactive',
            'status' => 'published',
        ]);

        $result = $this->repository->validateSlugsExistInLocale(['inactive'], 'fr');

        $this->assertCount(0, $result);
    }
}
