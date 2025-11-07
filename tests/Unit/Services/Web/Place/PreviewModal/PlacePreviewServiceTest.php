<?php

namespace Tests\Unit\Services\Web\Place\PreviewModal;

use App\Contracts\Repositories\Web\Place\PreviewModal\PlacePreviewRepositoryInterface;
use App\DTO\Web\Place\PlacePreviewDTO;
use App\Exceptions\Web\Place\PreviewModal\PlaceNotFoundException;
use App\Exceptions\Web\Place\PreviewModal\PlaceTranslationNotFoundException;
use App\Services\Web\Place\PreviewModal\PlacePreviewService;
use Mockery;
use Tests\TestCase;

class PlacePreviewServiceTest extends TestCase
{
    private PlacePreviewRepositoryInterface $mockRepository;

    private PlacePreviewService $service;

    protected function setUp(): void
    {
        parent::setUp();

        $this->mockRepository = Mockery::mock(PlacePreviewRepositoryInterface::class);
        $this->service = new PlacePreviewService($this->mockRepository);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_get_place_preview_by_id_returns_dto_on_success(): void
    {
        $placeId = 42;
        $expectedDto = new PlacePreviewDTO(
            id: $placeId,
            slug: 'test-place',
            title: 'Test Place',
            descriptionExcerpt: 'Test description',
            mainPhotoUrl: 'https://example.com/photo.jpg',
            isFeatured: false,
            tags: []
        );

        $this->mockRepository
            ->shouldReceive('getPlacePreviewById')
            ->once()
            ->with($placeId)
            ->andReturn($expectedDto);

        $result = $this->service->getPlacePreviewById($placeId);

        $this->assertInstanceOf(PlacePreviewDTO::class, $result);
        $this->assertEquals($expectedDto, $result);
    }

    public function test_get_place_preview_by_id_throws_exception_for_invalid_id_zero(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid place ID: 0');

        $this->mockRepository
            ->shouldReceive('getPlacePreviewById')
            ->never();

        $this->service->getPlacePreviewById(0);
    }

    public function test_get_place_preview_by_id_throws_exception_for_invalid_id_negative(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid place ID: -5');

        $this->mockRepository
            ->shouldReceive('getPlacePreviewById')
            ->never();

        $this->service->getPlacePreviewById(-5);
    }

    public function test_get_place_preview_by_id_propagates_place_not_found_exception(): void
    {
        $placeId = 999;

        $this->mockRepository
            ->shouldReceive('getPlacePreviewById')
            ->once()
            ->with($placeId)
            ->andThrow(new PlaceNotFoundException($placeId));

        $this->expectException(PlaceNotFoundException::class);

        $this->service->getPlacePreviewById($placeId);
    }

    public function test_get_place_preview_by_id_propagates_translation_not_found_exception(): void
    {
        $placeId = 42;
        $locale = 'en';

        $this->mockRepository
            ->shouldReceive('getPlacePreviewById')
            ->once()
            ->with($placeId)
            ->andThrow(new PlaceTranslationNotFoundException($placeId, $locale));

        $this->expectException(PlaceTranslationNotFoundException::class);

        $this->service->getPlacePreviewById($placeId);
    }

    public function test_get_place_preview_by_id_accepts_valid_positive_ids(): void
    {
        $validIds = [1, 42, 100, 999999];

        foreach ($validIds as $id) {
            $expectedDto = new PlacePreviewDTO(
                id: $id,
                slug: "place-{$id}",
                title: "Place {$id}",
                descriptionExcerpt: 'Test description',
                mainPhotoUrl: null,
                isFeatured: false,
                tags: []
            );

            $this->mockRepository
                ->shouldReceive('getPlacePreviewById')
                ->once()
                ->with($id)
                ->andReturn($expectedDto);

            $result = $this->service->getPlacePreviewById($id);

            $this->assertEquals($id, $result->id);
        }
    }

    public function test_service_does_not_catch_repository_exceptions(): void
    {
        $placeId = 42;

        // Simulate an unexpected exception from repository
        $this->mockRepository
            ->shouldReceive('getPlacePreviewById')
            ->once()
            ->with($placeId)
            ->andThrow(new \RuntimeException('Database connection failed'));

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Database connection failed');

        $this->service->getPlacePreviewById($placeId);
    }

    public function test_invalid_argument_exception_message_specifies_must_be_positive(): void
    {
        try {
            $this->service->getPlacePreviewById(-10);
            $this->fail('Expected InvalidArgumentException was not thrown');
        } catch (\InvalidArgumentException $e) {
            $this->assertStringContainsString('positive integer', $e->getMessage());
        }
    }
}
