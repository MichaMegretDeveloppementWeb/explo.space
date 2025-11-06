<?php

namespace Tests\Unit\Services\Web\Place\Show;

use App\Contracts\Repositories\Web\Place\Show\PlaceDetailRepositoryInterface;
use App\DTO\Web\Place\PlaceDetailDTO;
use App\Exceptions\Web\Place\Show\PlaceNotFoundException;
use App\Exceptions\Web\Place\Show\PlaceTranslationNotFoundException;
use App\Models\Photo;
use App\Models\Place;
use App\Models\PlaceTranslation;
use App\Models\Tag;
use App\Models\TagTranslation;
use App\Services\Web\Place\Show\PlaceDetailService;
use Mockery;
use Tests\TestCase;

class PlaceDetailServiceTest extends TestCase
{
    private PlaceDetailService $service;

    private PlaceDetailRepositoryInterface $repository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->repository = Mockery::mock(PlaceDetailRepositoryInterface::class);
        $this->service = new PlaceDetailService($this->repository);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_get_place_detail_by_slug_returns_dto_on_success(): void
    {
        $place = $this->createPlaceMock();

        $this->repository
            ->shouldReceive('getPlaceBySlug')
            ->once()
            ->with('test-slug', 'fr')
            ->andReturn($place);

        $result = $this->service->getPlaceDetailBySlug('test-slug', 'fr');

        $this->assertInstanceOf(PlaceDetailDTO::class, $result);
        $this->assertEquals('test-slug', $result->slug);
        $this->assertEquals('Test Place', $result->title);
    }

    public function test_get_place_detail_by_slug_throws_place_not_found_exception_when_place_is_null(): void
    {
        $this->repository
            ->shouldReceive('getPlaceBySlug')
            ->once()
            ->with('non-existent', 'fr')
            ->andReturn(null);

        $this->expectException(PlaceNotFoundException::class);

        $this->service->getPlaceDetailBySlug('non-existent', 'fr');
    }

    public function test_get_place_detail_by_slug_throws_translation_not_found_exception_when_no_translation(): void
    {
        $place = Mockery::mock(Place::class)->makePartial();
        $place->id = 1;
        $place->shouldReceive('setAttribute')->andReturn(true);
        $place->shouldReceive('getAttribute')->with('translations')->andReturn(collect([]));

        $this->repository
            ->shouldReceive('getPlaceBySlug')
            ->once()
            ->with('test-slug', 'fr')
            ->andReturn($place);

        $this->expectException(PlaceTranslationNotFoundException::class);

        $this->service->getPlaceDetailBySlug('test-slug', 'fr');
    }

    public function test_get_place_detail_by_slug_builds_dto_with_all_data(): void
    {
        $place = $this->createPlaceMock();

        $this->repository
            ->shouldReceive('getPlaceBySlug')
            ->once()
            ->andReturn($place);

        $result = $this->service->getPlaceDetailBySlug('test-slug', 'fr');

        $this->assertEquals(1, $result->id);
        $this->assertEquals('Test Place', $result->title);
        $this->assertEquals(28.5728, $result->latitude);
        $this->assertEquals(-80.6490, $result->longitude);
        $this->assertCount(1, $result->tags);
        $this->assertCount(2, $result->photos);
    }

    public function test_get_place_detail_by_slug_filters_tags_without_translation(): void
    {
        $place = $this->createPlaceMockWithTagsWithoutTranslation();

        $this->repository
            ->shouldReceive('getPlaceBySlug')
            ->once()
            ->andReturn($place);

        $result = $this->service->getPlaceDetailBySlug('test-slug', 'fr');

        $this->assertCount(0, $result->tags);
    }

    public function test_get_place_detail_by_slug_handles_place_without_photos(): void
    {
        $place = $this->createPlaceMockWithoutPhotos();

        $this->repository
            ->shouldReceive('getPlaceBySlug')
            ->once()
            ->andReturn($place);

        $result = $this->service->getPlaceDetailBySlug('test-slug', 'fr');

        $this->assertEmpty($result->photos);
        $this->assertNull($result->mainPhotoUrl);
    }

    public function test_get_place_detail_by_slug_formats_dates_correctly(): void
    {
        $place = $this->createPlaceMock();

        $this->repository
            ->shouldReceive('getPlaceBySlug')
            ->once()
            ->andReturn($place);

        $result = $this->service->getPlaceDetailBySlug('test-slug', 'fr');

        $this->assertIsString($result->createdAt);
        $this->assertIsString($result->updatedAt);
    }

    public function test_get_place_detail_by_slug_returns_main_photo_url(): void
    {
        $place = $this->createPlaceMock();

        $this->repository
            ->shouldReceive('getPlaceBySlug')
            ->once()
            ->andReturn($place);

        $result = $this->service->getPlaceDetailBySlug('test-slug', 'fr');

        $this->assertEquals('https://example.com/main-photo.jpg', $result->mainPhotoUrl);
    }

    public function test_get_place_detail_by_slug_handles_null_practical_info(): void
    {
        $place = $this->createPlaceMockWithoutPracticalInfo();

        $this->repository
            ->shouldReceive('getPlaceBySlug')
            ->once()
            ->andReturn($place);

        $result = $this->service->getPlaceDetailBySlug('test-slug', 'fr');

        $this->assertNull($result->practicalInfo);
    }

    private function createPlaceMock(): Place
    {
        $place = Mockery::mock(Place::class)->makePartial();
        $place->id = 1;
        $place->latitude = 28.5728;
        $place->longitude = -80.6490;
        $place->address = 'Kennedy Space Center, FL';
        $place->shouldReceive('setAttribute')->andReturn(true);

        $createdAt = now();
        $updatedAt = now();
        $place->shouldReceive('getAttribute')->with('created_at')->andReturn($createdAt);
        $place->shouldReceive('getAttribute')->with('updated_at')->andReturn($updatedAt);
        $place->created_at = $createdAt;
        $place->updated_at = $updatedAt;

        $translation = Mockery::mock(PlaceTranslation::class)->makePartial();
        $translation->slug = 'test-slug';
        $translation->title = 'Test Place';
        $translation->description = 'Test description';
        $translation->practical_info = 'Test info';
        $translation->shouldReceive('setAttribute')->andReturn(true);

        $place->shouldReceive('getAttribute')->with('translations')->andReturn(collect([$translation]));

        $tag = Mockery::mock(Tag::class)->makePartial();
        $tag->id = 1;
        $tag->color = '#FF0000';
        $tag->shouldReceive('setAttribute')->andReturn(true);

        $tagTranslation = Mockery::mock(TagTranslation::class)->makePartial();
        $tagTranslation->name = 'NASA';
        $tagTranslation->slug = 'nasa';
        $tagTranslation->shouldReceive('setAttribute')->andReturn(true);

        $tag->shouldReceive('getAttribute')->with('translations')->andReturn(collect([$tagTranslation]));
        $place->shouldReceive('getAttribute')->with('tags')->andReturn(collect([$tag]));

        $photo1 = Mockery::mock(Photo::class)->makePartial();
        $photo1->id = 1;
        $photo1->is_main = true;
        $photo1->shouldReceive('setAttribute')->andReturn(true);
        $photo1->shouldReceive('getAttribute')->with('url')->andReturn('https://example.com/main-photo.jpg');
        $photo1->shouldReceive('getAttribute')->with('medium_url')->andReturn('https://example.com/main-photo-medium.jpg');
        $photo1->url = 'https://example.com/main-photo.jpg';
        $photo1->medium_url = 'https://example.com/main-photo-medium.jpg';

        $photo2 = Mockery::mock(Photo::class)->makePartial();
        $photo2->id = 2;
        $photo2->is_main = false;
        $photo2->shouldReceive('setAttribute')->andReturn(true);
        $photo2->shouldReceive('getAttribute')->with('url')->andReturn('https://example.com/photo2.jpg');
        $photo2->shouldReceive('getAttribute')->with('medium_url')->andReturn('https://example.com/photo2-medium.jpg');
        $photo2->url = 'https://example.com/photo2.jpg';
        $photo2->medium_url = 'https://example.com/photo2-medium.jpg';

        $place->shouldReceive('getAttribute')->with('photos')->andReturn(collect([$photo1, $photo2]));

        return $place;
    }

    private function createPlaceMockWithTagsWithoutTranslation(): Place
    {
        $place = Mockery::mock(Place::class)->makePartial();
        $place->id = 1;
        $place->latitude = 28.5728;
        $place->longitude = -80.6490;
        $place->address = 'Kennedy Space Center, FL';
        $place->shouldReceive('setAttribute')->andReturn(true);

        $createdAt = now();
        $updatedAt = now();
        $place->shouldReceive('getAttribute')->with('created_at')->andReturn($createdAt);
        $place->shouldReceive('getAttribute')->with('updated_at')->andReturn($updatedAt);
        $place->created_at = $createdAt;
        $place->updated_at = $updatedAt;

        $translation = Mockery::mock(PlaceTranslation::class)->makePartial();
        $translation->slug = 'test-slug';
        $translation->title = 'Test Place';
        $translation->description = 'Test description';
        $translation->practical_info = 'Test info';
        $translation->shouldReceive('setAttribute')->andReturn(true);

        $place->shouldReceive('getAttribute')->with('translations')->andReturn(collect([$translation]));

        $tag = Mockery::mock(Tag::class)->makePartial();
        $tag->id = 1;
        $tag->color = '#FF0000';
        $tag->shouldReceive('setAttribute')->andReturn(true);
        $tag->shouldReceive('getAttribute')->with('translations')->andReturn(collect([]));

        $place->shouldReceive('getAttribute')->with('tags')->andReturn(collect([$tag]));
        $place->shouldReceive('getAttribute')->with('photos')->andReturn(collect([]));

        return $place;
    }

    private function createPlaceMockWithoutPhotos(): Place
    {
        $place = Mockery::mock(Place::class)->makePartial();
        $place->id = 1;
        $place->latitude = 28.5728;
        $place->longitude = -80.6490;
        $place->address = 'Kennedy Space Center, FL';
        $place->shouldReceive('setAttribute')->andReturn(true);

        $createdAt = now();
        $updatedAt = now();
        $place->shouldReceive('getAttribute')->with('created_at')->andReturn($createdAt);
        $place->shouldReceive('getAttribute')->with('updated_at')->andReturn($updatedAt);
        $place->created_at = $createdAt;
        $place->updated_at = $updatedAt;

        $translation = Mockery::mock(PlaceTranslation::class)->makePartial();
        $translation->slug = 'test-slug';
        $translation->title = 'Test Place';
        $translation->description = 'Test description';
        $translation->practical_info = 'Test info';
        $translation->shouldReceive('setAttribute')->andReturn(true);

        $place->shouldReceive('getAttribute')->with('translations')->andReturn(collect([$translation]));
        $place->shouldReceive('getAttribute')->with('tags')->andReturn(collect([]));
        $place->shouldReceive('getAttribute')->with('photos')->andReturn(collect([]));

        return $place;
    }

    private function createPlaceMockWithoutPracticalInfo(): Place
    {
        $place = Mockery::mock(Place::class)->makePartial();
        $place->id = 1;
        $place->latitude = 28.5728;
        $place->longitude = -80.6490;
        $place->address = 'Kennedy Space Center, FL';
        $place->shouldReceive('setAttribute')->andReturn(true);

        $createdAt = now();
        $updatedAt = now();
        $place->shouldReceive('getAttribute')->with('created_at')->andReturn($createdAt);
        $place->shouldReceive('getAttribute')->with('updated_at')->andReturn($updatedAt);
        $place->created_at = $createdAt;
        $place->updated_at = $updatedAt;

        $translation = Mockery::mock(PlaceTranslation::class)->makePartial();
        $translation->slug = 'test-slug';
        $translation->title = 'Test Place';
        $translation->description = 'Test description';
        $translation->practical_info = null;
        $translation->shouldReceive('setAttribute')->andReturn(true);

        $place->shouldReceive('getAttribute')->with('translations')->andReturn(collect([$translation]));
        $place->shouldReceive('getAttribute')->with('tags')->andReturn(collect([]));
        $place->shouldReceive('getAttribute')->with('photos')->andReturn(collect([]));

        return $place;
    }
}
