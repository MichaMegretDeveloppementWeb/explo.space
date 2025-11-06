<?php

namespace Tests\Unit\Services\Web\Place;

use App\Models\PlaceRequest;
use App\Services\Web\Place\Request\PlaceRequestCreateService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PlaceRequestCreateServiceTest extends TestCase
{
    use RefreshDatabase;

    private PlaceRequestCreateService $service;

    protected function setUp(): void
    {
        parent::setUp();

        $this->service = app(PlaceRequestCreateService::class);
        Storage::fake('local');
    }

    public function test_it_creates_a_place_request_successfully_without_photos(): void
    {
        // Arrange
        $data = [
            'title' => 'Centre Spatial Kennedy',
            'description' => 'Description du centre spatial',
            'practical_info' => 'Infos pratiques',
            'latitude' => 28.5721,
            'longitude' => -80.6480,
            'address' => 'Kennedy Space Center, FL, USA',
            'contact_email' => 'test@example.com',
        ];

        // Act
        $result = $this->service->create($data);

        // Assert
        $this->assertInstanceOf(PlaceRequest::class, $result);
        $this->assertEquals('Centre Spatial Kennedy', $result->title);
        $this->assertEquals('centre-spatial-kennedy', $result->slug);
        $this->assertThat($result->detected_language, $this->logicalOr(
            $this->equalTo('fr'),
            $this->equalTo('unknown')
        ));
        $this->assertEquals(\App\Enums\RequestStatus::Submitted, $result->status);
        $this->assertDatabaseHas('place_requests', [
            'title' => 'Centre Spatial Kennedy',
            'slug' => 'centre-spatial-kennedy',
        ]);
    }

    public function test_it_creates_a_place_request_with_minimal_data(): void
    {
        // Arrange
        $data = [
            'title' => 'Test Place',
            'contact_email' => 'test@example.com',
        ];

        // Act
        $result = $this->service->create($data);

        // Assert
        $this->assertInstanceOf(PlaceRequest::class, $result);
        $this->assertEquals('Test Place', $result->title);
        $this->assertNull($result->description);
        $this->assertNull($result->latitude);
        // Language detection may return 'en' or 'unknown' depending on detection service
        $this->assertThat($result->detected_language, $this->logicalOr(
            $this->equalTo('en'),
            $this->equalTo('unknown')
        ));
    }

    public function test_it_creates_a_place_request_with_photos(): void
    {
        // Arrange
        $data = [
            'title' => 'Test Place',
            'contact_email' => 'test@example.com',
            'photos' => [
                UploadedFile::fake()->image('photo1.jpg', 1000, 1000)->size(2000),
                UploadedFile::fake()->image('photo2.jpg', 1000, 1000)->size(1500),
            ],
        ];

        // Act
        $result = $this->service->create($data);

        // Assert
        $this->assertInstanceOf(PlaceRequest::class, $result);
        $this->assertCount(2, $result->photos);
    }

    public function test_it_generates_slug_from_title(): void
    {
        // Arrange
        $data = [
            'title' => 'Centre Spatial de Kourou - Ariane 5',
            'contact_email' => 'test@example.com',
        ];

        // Act
        $result = $this->service->create($data);

        // Assert
        $this->assertEquals('centre-spatial-de-kourou-ariane-5', $result->slug);
    }

    public function test_it_handles_special_characters_in_slug(): void
    {
        // Arrange
        $data = [
            'title' => 'Observatoire de l\'ESA à Paris',
            'contact_email' => 'test@example.com',
        ];

        // Act
        $result = $this->service->create($data);

        // Assert
        $this->assertEquals('observatoire-de-lesa-a-paris', $result->slug);
        $this->assertStringNotContainsString('\'', $result->slug);
    }

    public function test_it_stores_coordinates_correctly(): void
    {
        // Arrange
        $data = [
            'title' => 'Test',
            'contact_email' => 'test@example.com',
            'latitude' => 48.8566,
            'longitude' => 2.3522,
        ];

        // Act
        $result = $this->service->create($data);

        // Assert
        $this->assertEquals(48.8566, $result->latitude);
        $this->assertEquals(2.3522, $result->longitude);
    }
}
