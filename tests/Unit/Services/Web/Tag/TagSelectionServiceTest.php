<?php

namespace Tests\Unit\Services\Web\Tag;

use App\Contracts\Repositories\Web\Tag\TagSelectionRepositoryInterface;
use App\Services\Web\Tag\TagSelectionService;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Mockery;
use Tests\TestCase;

class TagSelectionServiceTest extends TestCase
{
    private TagSelectionService $service;

    private TagSelectionRepositoryInterface $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = Mockery::mock(TagSelectionRepositoryInterface::class);
        $this->service = new TagSelectionService($this->repository);
    }

    public function test_get_available_tags_returns_formatted_array(): void
    {
        $tags = new EloquentCollection([
            (object) ['slug' => 'nasa', 'name' => 'NASA'],
            (object) ['slug' => 'spacex', 'name' => 'SpaceX'],
        ]);

        $this->repository
            ->shouldReceive('getPublishedActiveTagsForLocale')
            ->once()
            ->with('fr')
            ->andReturn($tags);

        $result = $this->service->getAvailableTagsForLocale('fr');

        $this->assertCount(2, $result);
        $this->assertEquals(['slug' => 'nasa', 'name' => 'NASA'], $result[0]);
        $this->assertEquals(['slug' => 'spacex', 'name' => 'SpaceX'], $result[1]);
    }

    public function test_get_tags_by_slug_list_returns_matching_tags(): void
    {
        $tags = new EloquentCollection([
            (object) ['slug' => 'nasa', 'name' => 'NASA'],
            (object) ['slug' => 'spacex', 'name' => 'SpaceX'],
        ]);

        $this->repository
            ->shouldReceive('getBySlugListInLocale')
            ->once()
            ->with(['nasa', 'spacex'], 'fr')
            ->andReturn($tags);

        $result = $this->service->getTagsBySlugList(['nasa', 'spacex'], 'fr');

        $this->assertCount(2, $result);
    }

    public function test_get_tags_by_slug_list_returns_empty_for_empty_array(): void
    {
        $result = $this->service->getTagsBySlugList([], 'fr');

        $this->assertEmpty($result);
    }

    public function test_validate_and_clean_slugs_removes_duplicates(): void
    {
        $this->repository
            ->shouldReceive('validateSlugsExistInLocale')
            ->once()
            ->with(['nasa', 'spacex'], 'fr')
            ->andReturn(['nasa', 'spacex']);

        $result = $this->service->validateAndCleanSlugs(['nasa', 'spacex', 'nasa'], 'fr');

        $this->assertCount(2, $result);
        $this->assertContains('nasa', $result);
        $this->assertContains('spacex', $result);
    }

    public function test_validate_and_clean_slugs_removes_empty_values(): void
    {
        // Note: '  ' (spaces) has strlen > 0 so it's not filtered by the service
        $this->repository
            ->shouldReceive('validateSlugsExistInLocale')
            ->once()
            ->with(['nasa', 'spacex', '  '], 'fr')
            ->andReturn(['nasa', 'spacex']); // Repository returns only valid slugs

        $result = $this->service->validateAndCleanSlugs(['nasa', '', 'spacex', '  '], 'fr');

        $this->assertCount(2, $result);
    }

    public function test_validate_and_clean_slugs_returns_empty_for_empty_input(): void
    {
        $result = $this->service->validateAndCleanSlugs([], 'fr');

        $this->assertEmpty($result);
    }

    public function test_validate_and_clean_slugs_returns_only_existing_slugs(): void
    {
        $this->repository
            ->shouldReceive('validateSlugsExistInLocale')
            ->once()
            ->with(['nasa', 'invalid'], 'fr')
            ->andReturn(['nasa']); // Repository retourne uniquement les slugs valides

        $result = $this->service->validateAndCleanSlugs(['nasa', 'invalid'], 'fr');

        $this->assertCount(1, $result);
        $this->assertContains('nasa', $result);
    }

    public function test_build_tags_url_parameter_creates_comma_separated_string(): void
    {
        $selectedTags = [
            ['slug' => 'nasa', 'name' => 'NASA'],
            ['slug' => 'spacex', 'name' => 'SpaceX'],
        ];

        $result = $this->service->buildTagsUrlParameter($selectedTags);

        $this->assertEquals('nasa,spacex', $result);
    }

    public function test_build_tags_url_parameter_returns_empty_for_empty_array(): void
    {
        $result = $this->service->buildTagsUrlParameter([]);

        $this->assertEquals('', $result);
    }

    public function test_build_tags_url_parameter_filters_duplicate_slugs(): void
    {
        $selectedTags = [
            ['slug' => 'nasa', 'name' => 'NASA'],
            ['slug' => 'nasa', 'name' => 'NASA'],
        ];

        $result = $this->service->buildTagsUrlParameter($selectedTags);

        $this->assertEquals('nasa', $result);
    }

    public function test_parse_tags_url_parameter_creates_array_from_string(): void
    {
        $result = $this->service->parseTagsUrlParameter('nasa,spacex,observatory');

        $this->assertEquals(['nasa', 'spacex', 'observatory'], $result);
    }

    public function test_parse_tags_url_parameter_returns_empty_for_empty_string(): void
    {
        $result = $this->service->parseTagsUrlParameter('');

        $this->assertEmpty($result);
    }

    public function test_parse_tags_url_parameter_trims_whitespace(): void
    {
        $result = $this->service->parseTagsUrlParameter(' nasa , spacex , observatory ');

        $this->assertEquals(['nasa', 'spacex', 'observatory'], $result);
    }

    public function test_parse_tags_url_parameter_filters_empty_values(): void
    {
        $result = $this->service->parseTagsUrlParameter('nasa,,spacex,  ,observatory');

        // array_filter doesn't reindex keys, so we check values only
        $this->assertEqualsCanonicalizing(['nasa', 'spacex', 'observatory'], $result);
        $this->assertCount(3, $result);
    }
}
