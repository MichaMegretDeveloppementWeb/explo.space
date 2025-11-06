<?php

namespace App\Services\Web\Place\PhotoSuggestion;

use App\Contracts\Repositories\Web\Place\PhotoSuggestion\PhotoSuggestionCreateRepositoryInterface;
use App\Enums\RequestStatus;
use App\Exceptions\Photo\PhotoProcessingException;
use App\Exceptions\Photo\PhotoValidationException;
use App\Exceptions\Photo\UnexpectedPhotoException;
use App\Models\EditRequest;
use App\Services\Photo\PhotoProcessingService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PhotoSuggestionCreateService
{
    public function __construct(
        private readonly PhotoSuggestionCreateRepositoryInterface $repository,
        private readonly PhotoProcessingService $photoProcessingService
    ) {}

    /**
     * Create a new photo suggestion edit request
     *
     * @param  array{place_id: int, contact_email: string, photos: array<int, UploadedFile>}  $data
     *
     * @throws PhotoValidationException|PhotoProcessingException|UnexpectedPhotoException|\Throwable
     */
    public function create(array $data): EditRequest
    {
        return DB::transaction(function () use ($data) {
            // Create the EditRequest first with empty photos array
            $editRequest = $this->repository->create([
                'place_id' => $data['place_id'],
                'type' => 'photo_suggestion',
                'contact_email' => $data['contact_email'],
                'suggested_changes' => [
                    'photos' => [],
                ],
                'detected_language' => 'unknown',
                'status' => RequestStatus::Submitted->value,
            ]);

            // Process photos and collect filenames
            $photoFilenames = $this->processPhotos($data['photos'], $editRequest->id);

            // Update the EditRequest with photo filenames
            $editRequest->update([
                'suggested_changes' => [
                    'photos' => $photoFilenames,
                ],
            ]);

            return $editRequest;
        });
    }

    /**
     * Process and store uploaded photos without thumbnails
     *
     * @param  array<int, UploadedFile>  $uploadedPhotos
     * @return array<int, string> Array of filenames
     *
     * @throws PhotoValidationException|PhotoProcessingException|UnexpectedPhotoException
     */
    private function processPhotos(array $uploadedPhotos, int $editRequestId): array
    {
        $maxFileSize = config('upload.images.max_size_kb') * 1024;
        $storedFilenames = [];

        foreach ($uploadedPhotos as $uploadedPhoto) {
            try {
                // Process photo using PhotoProcessingService
                $photoData = $this->photoProcessingService->processWithoutThumbnails(
                    $uploadedPhoto,
                    'edit_request_photos',
                    (string) $editRequestId,
                    $maxFileSize
                );

                // Store only the filename
                $storedFilenames[] = $photoData['filename'];

            } catch (PhotoValidationException|PhotoProcessingException $e) {
                // Expected exceptions: rollback + propagate without modification
                $this->rollbackStoredPhotos($storedFilenames, $editRequestId);

                Log::warning('Photo processing failed for photo suggestion', [
                    'edit_request_id' => $editRequestId,
                    'exception_type' => get_class($e),
                    'message' => $e->getMessage(),
                    'photos_rolled_back' => count($storedFilenames),
                ]);

                throw $e;
            } catch (\Throwable $e) {
                // Unexpected exception: rollback + log critical + wrap
                $this->rollbackStoredPhotos($storedFilenames, $editRequestId);

                Log::critical('Unexpected error during photo suggestion processing', [
                    'edit_request_id' => $editRequestId,
                    'exception_type' => get_class($e),
                    'message' => $e->getMessage(),
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                    'trace' => $e->getTraceAsString(),
                    'photos_rolled_back' => count($storedFilenames),
                ]);

                throw new UnexpectedPhotoException(
                    'Une erreur inattendue est survenue lors du traitement des photos. Veuillez réessayer.',
                    'unexpected',
                    $e
                );
            }
        }

        return $storedFilenames;
    }

    /**
     * Rollback stored photos in case of error
     *
     * IMPORTANT: This method must NEVER throw exceptions
     * Rollback errors are logged but don't interrupt the flow
     *
     * @param  array<int, string>  $filenames
     */
    private function rollbackStoredPhotos(array $filenames, int $editRequestId): void
    {
        foreach ($filenames as $filename) {
            try {
                $this->photoProcessingService->deletePhoto(
                    $filename,
                    'edit_request_photos',
                    (string) $editRequestId,
                    false
                );

                Log::info('Photo rolled back successfully', [
                    'filename' => $filename,
                    'edit_request_id' => $editRequestId,
                ]);

            } catch (\Throwable $e) {
                // Rollback error: log but DON'T block
                Log::error('Failed to rollback photo during error handling', [
                    'filename' => $filename,
                    'edit_request_id' => $editRequestId,
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString(),
                ]);
            }
        }
    }
}
