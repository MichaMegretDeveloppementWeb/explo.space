<?php

namespace Tests\Unit\Services\Web\Place\PhotoSuggestion;

use App\Contracts\Repositories\Web\Place\PhotoSuggestion\PhotoSuggestionCreateRepositoryInterface;
use App\Enums\RequestStatus;
use App\Exceptions\Photo\PhotoProcessingException;
use App\Exceptions\Photo\PhotoValidationException;
use App\Exceptions\Photo\UnexpectedPhotoException;
use App\Models\EditRequest;
use App\Services\Photo\PhotoProcessingService;
use App\Services\Web\Place\PhotoSuggestion\PhotoSuggestionCreateService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class PhotoSuggestionCreateServiceTest extends TestCase
{
    private PhotoSuggestionCreateService $service;

    private PhotoSuggestionCreateRepositoryInterface $repository;

    private PhotoProcessingService $photoProcessingService;

    protected function setUp(): void
    {
        parent::setUp();

        $this->repository = $this->createMock(PhotoSuggestionCreateRepositoryInterface::class);
        $this->photoProcessingService = $this->createMock(PhotoProcessingService::class);

        $this->service = new PhotoSuggestionCreateService(
            $this->repository,
            $this->photoProcessingService
        );
    }

    /** @test */
    public function it_creates_photo_suggestion_successfully(): void
    {
        // Arrange
        $placeId = 1;
        $contactEmail = 'test@example.com';
        $uploadedFiles = [
            UploadedFile::fake()->image('photo1.jpg', 800, 600),
            UploadedFile::fake()->image('photo2.jpg', 1024, 768),
        ];

        $editRequest = new EditRequest([
            'id' => 1,
            'place_id' => $placeId,
            'type' => 'photo_suggestion',
            'contact_email' => $contactEmail,
            'suggested_changes' => ['photos' => []],
            'status' => RequestStatus::Submitted->value,
        ]);
        $editRequest->id = 1;

        // Mock repository create
        $this->repository
            ->expects($this->once())
            ->method('create')
            ->with($this->callback(function ($data) use ($placeId, $contactEmail) {
                return $data['place_id'] === $placeId
                    && $data['type'] === 'photo_suggestion'
                    && $data['contact_email'] === $contactEmail
                    && $data['suggested_changes'] === ['photos' => []]
                    && $data['detected_language'] === 'unknown'
                    && $data['status'] === RequestStatus::Submitted->value;
            }))
            ->willReturn($editRequest);

        // Mock photo processing
        $this->photoProcessingService
            ->expects($this->exactly(2))
            ->method('processWithoutThumbnails')
            ->willReturnOnConsecutiveCalls(
                ['filename' => 'photo1_processed.jpg'],
                ['filename' => 'photo2_processed.jpg']
            );

        // Act
        DB::shouldReceive('transaction')->once()->andReturnUsing(function ($callback) {
            return $callback();
        });

        $result = $this->service->create([
            'place_id' => $placeId,
            'contact_email' => $contactEmail,
            'photos' => $uploadedFiles,
        ]);

        // Assert
        $this->assertInstanceOf(EditRequest::class, $result);
        $this->assertEquals($placeId, $result->place_id);
        $this->assertEquals($contactEmail, $result->contact_email);
    }

    /** @test */
    public function it_rolls_back_photos_on_validation_exception(): void
    {
        // Arrange
        $placeId = 1;
        $uploadedFiles = [
            UploadedFile::fake()->image('photo1.jpg'),
            UploadedFile::fake()->image('invalid.svg'), // Invalid format
        ];

        $editRequest = new EditRequest(['id' => 1]);
        $editRequest->id = 1;

        $this->repository
            ->method('create')
            ->willReturn($editRequest);

        // First photo succeeds, second throws exception
        $this->photoProcessingService
            ->expects($this->exactly(2))
            ->method('processWithoutThumbnails')
            ->willReturnOnConsecutiveCalls(
                ['filename' => 'photo1_processed.jpg'],
                $this->throwException(PhotoValidationException::svgNotAllowed())
            );

        // Mock deletePhoto for rollback
        $this->photoProcessingService
            ->expects($this->once())
            ->method('deletePhoto')
            ->with('photo1_processed.jpg', 'edit_request_photos', '1', false);

        DB::shouldReceive('transaction')->once()->andReturnUsing(function ($callback) {
            return $callback();
        });

        // Act & Assert
        $this->expectException(PhotoValidationException::class);
        $this->expectExceptionMessage('Les fichiers SVG ne sont pas autorisés pour des raisons de sécurité.');

        $this->service->create([
            'place_id' => $placeId,
            'contact_email' => 'test@example.com',
            'photos' => $uploadedFiles,
        ]);
    }

    /** @test */
    public function it_rolls_back_photos_on_processing_exception(): void
    {
        // Arrange
        $uploadedFiles = [UploadedFile::fake()->image('photo1.jpg')];
        $editRequest = new EditRequest(['id' => 1]);
        $editRequest->id = 1;

        $this->repository->method('create')->willReturn($editRequest);

        $this->photoProcessingService
            ->method('processWithoutThumbnails')
            ->willThrowException(new PhotoProcessingException('Storage error', 'storage.failed'));

        DB::shouldReceive('transaction')->once()->andReturnUsing(function ($callback) {
            return $callback();
        });

        // Act & Assert
        $this->expectException(PhotoProcessingException::class);

        $this->service->create([
            'place_id' => 1,
            'contact_email' => 'test@example.com',
            'photos' => $uploadedFiles,
        ]);
    }

    /** @test */
    public function it_wraps_unexpected_exceptions(): void
    {
        // Arrange
        $uploadedFiles = [UploadedFile::fake()->image('photo1.jpg')];
        $editRequest = new EditRequest(['id' => 1]);
        $editRequest->id = 1;

        $this->repository->method('create')->willReturn($editRequest);

        $this->photoProcessingService
            ->method('processWithoutThumbnails')
            ->willThrowException(new \RuntimeException('Unexpected error'));

        DB::shouldReceive('transaction')->once()->andReturnUsing(function ($callback) {
            return $callback();
        });

        // Act & Assert
        $this->expectException(UnexpectedPhotoException::class);
        $this->expectExceptionMessage('Une erreur inattendue est survenue lors du traitement des photos.');

        $this->service->create([
            'place_id' => 1,
            'contact_email' => 'test@example.com',
            'photos' => $uploadedFiles,
        ]);
    }

    /** @test */
    public function it_processes_multiple_photos_successfully(): void
    {
        // Arrange
        $uploadedFiles = [
            UploadedFile::fake()->image('photo1.jpg'),
            UploadedFile::fake()->image('photo2.jpg'),
        ];

        $editRequest = new EditRequest(['id' => 1]);
        $editRequest->id = 1;

        $this->repository
            ->method('create')
            ->willReturn($editRequest);

        // Mock photo processing for multiple photos
        $this->photoProcessingService
            ->expects($this->exactly(2))
            ->method('processWithoutThumbnails')
            ->willReturnOnConsecutiveCalls(
                ['filename' => 'photo1_abc.jpg'],
                ['filename' => 'photo2_def.jpg']
            );

        DB::shouldReceive('transaction')->once()->andReturnUsing(function ($callback) {
            return $callback();
        });

        // Act
        $result = $this->service->create([
            'place_id' => 1,
            'contact_email' => 'test@example.com',
            'photos' => $uploadedFiles,
        ]);

        // Assert - Verify the service completes successfully
        $this->assertInstanceOf(EditRequest::class, $result);
        $this->assertEquals(1, $result->id);
    }

    /** @test */
    public function it_uses_correct_disk_and_path_for_photo_storage(): void
    {
        // Arrange
        $editRequestId = 42;
        $uploadedFiles = [UploadedFile::fake()->image('photo.jpg')];

        $editRequest = new EditRequest(['id' => $editRequestId]);
        $editRequest->id = $editRequestId;

        $this->repository->method('create')->willReturn($editRequest);

        // Assert correct parameters
        $this->photoProcessingService
            ->expects($this->once())
            ->method('processWithoutThumbnails')
            ->with(
                $this->anything(),
                'edit_request_photos', // Disk name
                '42', // EditRequest ID as string
                $this->anything()
            )
            ->willReturn(['filename' => 'photo.jpg']);

        DB::shouldReceive('transaction')->once()->andReturnUsing(function ($callback) {
            return $callback();
        });

        // Act
        $this->service->create([
            'place_id' => 1,
            'contact_email' => 'test@example.com',
            'photos' => $uploadedFiles,
        ]);
    }
}
