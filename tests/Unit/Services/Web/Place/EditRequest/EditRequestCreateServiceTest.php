<?php

namespace Tests\Unit\Services\Web\Place\EditRequest;

use App\Contracts\Repositories\Web\Place\EditRequest\EditRequestCreateRepositoryInterface;
use App\Contracts\Translation\TranslationStrategyInterface;
use App\DTO\Web\Place\PlaceDetailDTO;
use App\Enums\RequestStatus;
use App\Models\EditRequest;
use App\Services\Web\Place\EditRequest\EditRequestCreateService;
use Tests\TestCase;

class EditRequestCreateServiceTest extends TestCase
{
    private EditRequestCreateService $service;

    private EditRequestCreateRepositoryInterface $repository;

    private TranslationStrategyInterface $translationStrategy;

    private PlaceDetailDTO $placeDTO;

    protected function setUp(): void
    {
        parent::setUp();

        $this->repository = $this->createMock(EditRequestCreateRepositoryInterface::class);
        $this->translationStrategy = $this->createMock(TranslationStrategyInterface::class);

        $this->service = new EditRequestCreateService(
            $this->translationStrategy,
            $this->repository
        );

        $this->placeDTO = new PlaceDetailDTO(
            id: 1,
            slug: 'test-place',
            title: 'Test Place',
            description: 'Test description',
            practicalInfo: 'Test info',
            latitude: 48.8566,
            longitude: 2.3522,
            address: 'Paris, France',
            tags: [],
            photos: [],
            mainPhotoUrl: null,
            createdAt: now()->toDateTimeString(),
            updatedAt: now()->toDateTimeString()
        );
    }

    /** @test */
    public function it_creates_signalement_edit_request_with_language_detection(): void
    {
        // Arrange
        $validatedData = [
            'type' => 'signalement',
            'description' => 'Il y a une erreur dans les informations',
            'contact_email' => 'visitor@example.com',
        ];

        $editRequest = new EditRequest(['id' => 1]);
        $editRequest->id = 1;

        // Mock language detection
        $this->translationStrategy
            ->expects($this->once())
            ->method('detectLanguage')
            ->with('Il y a une erreur dans les informations')
            ->willReturn('fr');

        // Mock repository create
        $this->repository
            ->expects($this->once())
            ->method('create')
            ->with($this->callback(function ($data) {
                return $data['place_id'] === 1
                    && $data['type'] === 'signalement'
                    && $data['contact_email'] === 'visitor@example.com'
                    && $data['description'] === 'Il y a une erreur dans les informations'
                    && $data['detected_language'] === 'fr'
                    && $data['status'] === RequestStatus::Submitted
                    && $data['suggested_changes'] === null;
            }))
            ->willReturn($editRequest);

        // Act
        $result = $this->service->createEditRequest($validatedData, $this->placeDTO);

        // Assert
        $this->assertInstanceOf(EditRequest::class, $result);
    }

    /** @test */
    public function it_creates_modification_with_suggested_changes(): void
    {
        // Arrange
        $validatedData = [
            'type' => 'modification',
            'description' => 'Voici mes suggestions',
            'contact_email' => 'modifier@example.com',
            'selected_fields' => ['title', 'description'],
            'new_values' => [
                'title' => 'New Title',
                'description' => 'New Description',
            ],
        ];

        $editRequest = new EditRequest(['id' => 1]);
        $editRequest->id = 1;

        // Mock language detection
        $this->translationStrategy
            ->method('detectLanguage')
            ->willReturn('fr');

        // Mock repository create
        $this->repository
            ->expects($this->once())
            ->method('create')
            ->with($this->callback(function ($data) {
                return $data['place_id'] === 1
                    && $data['type'] === 'modification'
                    && is_array($data['suggested_changes'])
                    && count($data['suggested_changes']) === 2
                    && $data['suggested_changes'][0]['field'] === 'title'
                    && $data['suggested_changes'][0]['old_value'] === 'Test Place'
                    && $data['suggested_changes'][0]['new_value'] === 'New Title'
                    && $data['suggested_changes'][0]['status'] === 'pending'
                    && $data['suggested_changes'][1]['field'] === 'description'
                    && $data['suggested_changes'][1]['old_value'] === 'Test description'
                    && $data['suggested_changes'][1]['new_value'] === 'New Description';
            }))
            ->willReturn($editRequest);

        // Act
        $result = $this->service->createEditRequest($validatedData, $this->placeDTO);

        // Assert
        $this->assertInstanceOf(EditRequest::class, $result);
    }

    /** @test */
    public function it_detects_language_from_combined_texts(): void
    {
        // Arrange
        $validatedData = [
            'type' => 'modification',
            'description' => 'This is a description',
            'contact_email' => 'test@example.com',
            'selected_fields' => ['title', 'practical_info'],
            'new_values' => [
                'title' => 'New English Title',
                'practical_info' => 'Practical information here',
            ],
        ];

        $editRequest = new EditRequest(['id' => 1]);
        $editRequest->id = 1;

        // Mock language detection - should combine all texts
        $this->translationStrategy
            ->expects($this->once())
            ->method('detectLanguage')
            ->with($this->stringContains('This is a description'))
            ->willReturn('en');

        $this->repository->method('create')->willReturn($editRequest);

        // Act
        $result = $this->service->createEditRequest($validatedData, $this->placeDTO);

        // Assert
        $this->assertInstanceOf(EditRequest::class, $result);
    }

    /** @test */
    public function it_returns_unknown_when_text_too_short(): void
    {
        // Arrange
        $validatedData = [
            'type' => 'signalement',
            'description' => 'X', // Too short
            'contact_email' => 'test@example.com',
        ];

        $editRequest = new EditRequest(['id' => 1]);
        $editRequest->id = 1;

        // Mock should NOT be called for too short text
        $this->translationStrategy
            ->expects($this->never())
            ->method('detectLanguage');

        // Repository should receive 'unknown'
        $this->repository
            ->expects($this->once())
            ->method('create')
            ->with($this->callback(function ($data) {
                return $data['detected_language'] === 'unknown';
            }))
            ->willReturn($editRequest);

        // Act
        $result = $this->service->createEditRequest($validatedData, $this->placeDTO);

        // Assert
        $this->assertInstanceOf(EditRequest::class, $result);
    }

    /** @test */
    public function it_handles_language_detection_exception(): void
    {
        // Arrange
        $validatedData = [
            'type' => 'signalement',
            'description' => 'This should trigger an exception',
            'contact_email' => 'test@example.com',
        ];

        $editRequest = new EditRequest(['id' => 1]);
        $editRequest->id = 1;

        // Mock language detection to throw exception
        $this->translationStrategy
            ->method('detectLanguage')
            ->willThrowException(new \RuntimeException('API error'));

        // Repository should receive 'unknown' as fallback
        $this->repository
            ->expects($this->once())
            ->method('create')
            ->with($this->callback(function ($data) {
                return $data['detected_language'] === 'unknown';
            }))
            ->willReturn($editRequest);

        // Act
        $result = $this->service->createEditRequest($validatedData, $this->placeDTO);

        // Assert
        $this->assertInstanceOf(EditRequest::class, $result);
    }

    /** @test */
    public function it_builds_suggested_changes_with_field_labels(): void
    {
        // Arrange
        $validatedData = [
            'type' => 'modification',
            'description' => 'Modifications',
            'contact_email' => 'test@example.com',
            'selected_fields' => ['title'],
            'new_values' => [
                'title' => 'Updated Title',
            ],
        ];

        $editRequest = new EditRequest(['id' => 1]);
        $editRequest->id = 1;

        $this->translationStrategy->method('detectLanguage')->willReturn('fr');

        // Capture the suggested_changes structure
        $capturedData = null;
        $this->repository
            ->method('create')
            ->willReturnCallback(function ($data) use (&$capturedData, $editRequest) {
                $capturedData = $data;

                return $editRequest;
            });

        // Act
        $this->service->createEditRequest($validatedData, $this->placeDTO);

        // Assert
        $this->assertNotNull($capturedData);
        $this->assertIsArray($capturedData['suggested_changes']);
        $this->assertArrayHasKey('field_label', $capturedData['suggested_changes'][0]);
        $this->assertEquals('title', $capturedData['suggested_changes'][0]['field']);
        $this->assertEquals('Test Place', $capturedData['suggested_changes'][0]['old_value']);
        $this->assertEquals('Updated Title', $capturedData['suggested_changes'][0]['new_value']);
        $this->assertEquals('pending', $capturedData['suggested_changes'][0]['status']);
    }

    /** @test */
    public function it_handles_coordinates_modification(): void
    {
        // Arrange
        $validatedData = [
            'type' => 'modification',
            'description' => 'Wrong coordinates',
            'contact_email' => 'test@example.com',
            'selected_fields' => ['coordinates'],
            'new_values' => [
                'coordinates' => [
                    'lat' => 40.7128,
                    'lng' => -74.0060,
                ],
            ],
        ];

        $editRequest = new EditRequest(['id' => 1]);
        $editRequest->id = 1;

        $this->translationStrategy->method('detectLanguage')->willReturn('en');

        // Capture suggested_changes
        $capturedData = null;
        $this->repository
            ->method('create')
            ->willReturnCallback(function ($data) use (&$capturedData, $editRequest) {
                $capturedData = $data;

                return $editRequest;
            });

        // Act
        $this->service->createEditRequest($validatedData, $this->placeDTO);

        // Assert
        $this->assertNotNull($capturedData);
        $this->assertEquals('coordinates', $capturedData['suggested_changes'][0]['field']);
        $this->assertEquals([
            'lat' => 48.8566,
            'lng' => 2.3522,
        ], $capturedData['suggested_changes'][0]['old_value']);
        $this->assertEquals([
            'lat' => 40.7128,
            'lng' => -74.0060,
        ], $capturedData['suggested_changes'][0]['new_value']);
    }

    /** @test */
    public function it_handles_address_modification(): void
    {
        // Arrange
        $validatedData = [
            'type' => 'modification',
            'description' => 'Wrong address',
            'contact_email' => 'test@example.com',
            'selected_fields' => ['address'],
            'new_values' => [
                'address' => 'New York, USA',
            ],
        ];

        $editRequest = new EditRequest(['id' => 1]);
        $editRequest->id = 1;

        $this->translationStrategy->method('detectLanguage')->willReturn('en');

        // Capture suggested_changes
        $capturedData = null;
        $this->repository
            ->method('create')
            ->willReturnCallback(function ($data) use (&$capturedData, $editRequest) {
                $capturedData = $data;

                return $editRequest;
            });

        // Act
        $this->service->createEditRequest($validatedData, $this->placeDTO);

        // Assert
        $this->assertNotNull($capturedData);
        $this->assertEquals('address', $capturedData['suggested_changes'][0]['field']);
        $this->assertEquals('Paris, France', $capturedData['suggested_changes'][0]['old_value']);
        $this->assertEquals('New York, USA', $capturedData['suggested_changes'][0]['new_value']);
    }

    /** @test */
    public function it_handles_practical_info_modification(): void
    {
        // Arrange
        $validatedData = [
            'type' => 'modification',
            'description' => 'Update practical info',
            'contact_email' => 'test@example.com',
            'selected_fields' => ['practical_info'],
            'new_values' => [
                'practical_info' => 'New practical information',
            ],
        ];

        $editRequest = new EditRequest(['id' => 1]);
        $editRequest->id = 1;

        $this->translationStrategy->method('detectLanguage')->willReturn('fr');

        // Capture suggested_changes
        $capturedData = null;
        $this->repository
            ->method('create')
            ->willReturnCallback(function ($data) use (&$capturedData, $editRequest) {
                $capturedData = $data;

                return $editRequest;
            });

        // Act
        $this->service->createEditRequest($validatedData, $this->placeDTO);

        // Assert
        $this->assertNotNull($capturedData);
        $this->assertEquals('practical_info', $capturedData['suggested_changes'][0]['field']);
        $this->assertEquals('Test info', $capturedData['suggested_changes'][0]['old_value']);
        $this->assertEquals('New practical information', $capturedData['suggested_changes'][0]['new_value']);
    }

    /** @test */
    public function it_limits_text_to_50_characters_for_detection(): void
    {
        // Arrange
        $longDescription = str_repeat('This is a very long description that exceeds fifty characters. ', 5);
        $validatedData = [
            'type' => 'signalement',
            'description' => $longDescription,
            'contact_email' => 'test@example.com',
        ];

        $editRequest = new EditRequest(['id' => 1]);
        $editRequest->id = 1;

        // Mock should receive truncated text (max 50 chars)
        $this->translationStrategy
            ->expects($this->once())
            ->method('detectLanguage')
            ->with($this->callback(function ($text) {
                return strlen($text) <= 50;
            }))
            ->willReturn('en');

        $this->repository->method('create')->willReturn($editRequest);

        // Act
        $result = $this->service->createEditRequest($validatedData, $this->placeDTO);

        // Assert
        $this->assertInstanceOf(EditRequest::class, $result);
    }

    /** @test */
    public function it_creates_with_default_submitted_status(): void
    {
        // Arrange
        $validatedData = [
            'type' => 'signalement',
            'description' => 'Test description',
            'contact_email' => 'test@example.com',
        ];

        $editRequest = new EditRequest(['id' => 1]);
        $editRequest->id = 1;

        $this->translationStrategy->method('detectLanguage')->willReturn('fr');

        // Verify status is Submitted
        $this->repository
            ->expects($this->once())
            ->method('create')
            ->with($this->callback(function ($data) {
                return $data['status'] === RequestStatus::Submitted;
            }))
            ->willReturn($editRequest);

        // Act
        $result = $this->service->createEditRequest($validatedData, $this->placeDTO);

        // Assert
        $this->assertInstanceOf(EditRequest::class, $result);
    }
}
