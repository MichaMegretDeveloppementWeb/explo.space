<?php

namespace Tests\Unit\Services\Admin\Tag;

use App\Contracts\Repositories\Admin\Tag\TagSelectionRepositoryInterface;
use App\Services\Admin\Tag\TagSelectionService;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Mockery;
use Tests\TestCase;

class TagSelectionServiceTest extends TestCase
{
    protected TagSelectionRepositoryInterface $repository;

    protected TagSelectionService $service;

    protected function setUp(): void
    {
        parent::setUp();

        $this->repository = Mockery::mock(TagSelectionRepositoryInterface::class);
        $this->service = new TagSelectionService($this->repository);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_get_available_tags_for_locale_returns_formatted_array(): void
    {
        // Arrange
        $locale = 'fr';

        // Create stdClass objects with both slug and name properties
        $tag1 = new \stdClass;
        $tag1->slug = 'nasa';
        $tag1->name = 'NASA';

        $tag2 = new \stdClass;
        $tag2->slug = 'spacex';
        $tag2->name = 'SpaceX';

        $mockTags = new EloquentCollection([$tag1, $tag2]);

        $this->repository
            ->shouldReceive('getPublishedActiveTagsForLocale')
            ->once()
            ->with($locale)
            ->andReturn($mockTags);

        // Act
        $result = $this->service->getAvailableTagsForLocale($locale);

        // Assert
        $this->assertIsArray($result);
        $this->assertCount(2, $result);
        $this->assertEquals([
            ['slug' => 'nasa', 'name' => 'NASA'],
            ['slug' => 'spacex', 'name' => 'SpaceX'],
        ], $result);
    }

    public function test_get_available_tags_for_locale_returns_empty_array_when_no_tags(): void
    {
        // Arrange
        $locale = 'fr';
        $mockTags = new EloquentCollection([]);

        $this->repository
            ->shouldReceive('getPublishedActiveTagsForLocale')
            ->once()
            ->with($locale)
            ->andReturn($mockTags);

        // Act
        $result = $this->service->getAvailableTagsForLocale($locale);

        // Assert
        $this->assertIsArray($result);
        $this->assertEmpty($result);
    }

    public function test_search_tags_by_name_returns_filtered_results(): void
    {
        // Arrange
        $query = 'nasa';
        $locale = 'fr';
        $limit = 10;

        $tag = new \stdClass;
        $tag->slug = 'nasa';
        $tag->name = 'NASA';

        $mockTags = new EloquentCollection([$tag]);

        $this->repository
            ->shouldReceive('searchByNameInLocale')
            ->once()
            ->with($query, $locale, $limit)
            ->andReturn($mockTags);

        // Act
        $result = $this->service->searchTagsByName($query, $locale, $limit);

        // Assert
        $this->assertIsArray($result);
        $this->assertCount(1, $result);
        $this->assertEquals([
            ['slug' => 'nasa', 'name' => 'NASA'],
        ], $result);
    }

    public function test_search_tags_by_name_trims_query(): void
    {
        // Arrange
        $query = '  nasa  ';
        $trimmedQuery = 'nasa';
        $locale = 'fr';
        $limit = 10;

        $mockTags = new EloquentCollection([]);

        $this->repository
            ->shouldReceive('searchByNameInLocale')
            ->once()
            ->with($trimmedQuery, $locale, $limit)
            ->andReturn($mockTags);

        // Act
        $result = $this->service->searchTagsByName($query, $locale, $limit);

        // Assert
        $this->assertIsArray($result);
        $this->assertEmpty($result);
    }

    public function test_search_tags_by_name_respects_limit(): void
    {
        // Arrange
        $query = 'space';
        $locale = 'fr';
        $limit = 5;

        $tag1 = new \stdClass;
        $tag1->slug = 'nasa';
        $tag1->name = 'NASA';

        $tag2 = new \stdClass;
        $tag2->slug = 'spacex';
        $tag2->name = 'SpaceX';

        $tag3 = new \stdClass;
        $tag3->slug = 'esa';
        $tag3->name = 'ESA';

        $mockTags = new EloquentCollection([$tag1, $tag2, $tag3]);

        $this->repository
            ->shouldReceive('searchByNameInLocale')
            ->once()
            ->with($query, $locale, $limit)
            ->andReturn($mockTags);

        // Act
        $result = $this->service->searchTagsByName($query, $locale, $limit);

        // Assert
        $this->assertIsArray($result);
        $this->assertCount(3, $result);
    }

    public function test_translate_tag_slugs_returns_translated_slugs(): void
    {
        // Arrange
        $slugs = ['observatoire', 'nasa'];
        $fromLocale = 'fr';
        $toLocale = 'en';
        $expectedTranslated = ['observatory', 'nasa'];

        $this->repository
            ->shouldReceive('translateSlugsToLocale')
            ->once()
            ->with($slugs, $fromLocale, $toLocale)
            ->andReturn($expectedTranslated);

        // Act
        $result = $this->service->translateTagSlugs($slugs, $fromLocale, $toLocale);

        // Assert
        $this->assertIsArray($result);
        $this->assertEquals($expectedTranslated, $result);
    }

    public function test_translate_tag_slugs_returns_empty_array_when_empty_input(): void
    {
        // Arrange
        $slugs = [];
        $fromLocale = 'fr';
        $toLocale = 'en';

        // Repository should not be called
        $this->repository
            ->shouldNotReceive('translateSlugsToLocale');

        // Act
        $result = $this->service->translateTagSlugs($slugs, $fromLocale, $toLocale);

        // Assert
        $this->assertIsArray($result);
        $this->assertEmpty($result);
    }

    public function test_translate_tag_slugs_removes_tags_without_translation(): void
    {
        // Arrange
        $slugs = ['observatoire', 'nasa', 'tag-sans-traduction'];
        $fromLocale = 'fr';
        $toLocale = 'en';
        $expectedTranslated = ['observatory', 'nasa']; // tag-sans-traduction n'a pas de traduction

        $this->repository
            ->shouldReceive('translateSlugsToLocale')
            ->once()
            ->with($slugs, $fromLocale, $toLocale)
            ->andReturn($expectedTranslated);

        // Act
        $result = $this->service->translateTagSlugs($slugs, $fromLocale, $toLocale);

        // Assert
        $this->assertIsArray($result);
        $this->assertCount(2, $result);
        $this->assertEquals($expectedTranslated, $result);
    }
}
