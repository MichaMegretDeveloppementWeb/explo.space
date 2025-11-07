<?php

namespace App\Services\Admin\Place\Edit;

use App\Contracts\Repositories\Admin\Place\Edit\PlaceUpdateRepositoryInterface;
use App\Enums\RequestStatus;
use App\Exceptions\Admin\Place\PlaceNotFoundException;
use App\Exceptions\Photo\PhotoProcessingException;
use App\Exceptions\Photo\PhotoValidationException;
use App\Exceptions\Photo\UnexpectedPhotoException;
use App\Models\EditRequest;
use App\Models\Place;
use App\Services\Photo\PhotoProcessingService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class PlaceUpdateService
{
    public function __construct(
        private PlaceUpdateRepositoryInterface $repository,
        private PhotoProcessingService $photoService
    ) {}

    /**
     * Load a place for editing with all relations.
     */
    public function loadForEdit(int $placeId): ?Place
    {
        return $this->repository->findForEdit($placeId);
    }

    /**
     * Update an existing place with all related data.
     *
     * @param  array<string, mixed>  $data
     *
     * @throws PhotoValidationException|PhotoProcessingException|UnexpectedPhotoException|\App\Exceptions\Admin\Place\PlaceNotFoundException|\Throwable
     */
    public function update(int $placeId, array $data): Place
    {
        return DB::transaction(function () use ($placeId, $data) {
            $place = $this->repository->findForEdit($placeId);

            if (! $place) {
                throw new PlaceNotFoundException;
            }

            // Update base place data
            $this->repository->update($place, [
                'latitude' => $data['latitude'],
                'longitude' => $data['longitude'],
                'address' => $data['address'] ?? null,
                'is_featured' => $data['is_featured'] ?? false,
            ]);

            // Update translations
            $this->repository->updateTranslations($place, $data['translations']);

            // Sync relations
            $this->repository->syncCategories($place, $data['category_ids'] ?? []);
            $this->repository->syncTags($place, $data['tag_ids'] ?? []);

            // Handle photo deletions
            if (! empty($data['deleted_photo_ids'])) {
                $this->deletePhotos($place, $data['deleted_photo_ids']);
            }

            // Add new photos
            if (! empty($data['new_photos'])) {
                $this->processAndAddPhotos($place, $data['new_photos']);
            }

            // Add EditRequest photos if present
            if (! empty($data['edit_request_photos'])) {
                $this->processEditRequestPhotos($place, $data['edit_request_photos']);
            }

            // Update photo order
            if (! empty($data['photo_order'])) {
                $this->repository->updatePhotoOrder($place, $data['photo_order']);
            }

            // Set main photo
            if (! empty($data['main_photo_id'])) {
                $this->repository->setMainPhoto($place, $data['main_photo_id']);
            }

            // Auto-assign first photo as main if no main photo exists
            $this->ensureMainPhoto($place);

            // Mark EditRequest as accepted if present
            if (! empty($data['edit_request_id'])) {
                $appliedChanges = [
                    'fields' => $data['selected_fields'] ?? [],
                    'photos' => $data['selected_photos'] ?? [],
                ];
                $this->acceptEditRequest($data['edit_request_id'], $data['admin_id'], $appliedChanges);
            }

            Log::info('Place updated successfully', [
                'place_id' => $place->id,
                'edit_request_id' => $data['edit_request_id'] ?? null,
            ]);

            return $place->fresh([
                'translations',
                'categories',
                'tags',
                'photos',
            ]);
        });
    }

    /**
     * Delete photos and their files.
     *
     * @param  array<int>  $photoIds
     */
    private function deletePhotos(Place $place, array $photoIds): void
    {
        foreach ($photoIds as $photoId) {
            $photo = $this->repository->findPhotoById($photoId);

            if ($photo && $photo->place_id === $place->id) {
                try {
                    // Delete files from storage (original + thumbnails)
                    $this->photoService->deletePhoto(
                        $photo->filename,
                        'place_photos',
                        '', // Path vide pour Places
                        true // Avec thumbnails
                    );

                    // Delete database record
                    $this->repository->deletePhoto($photoId);

                    Log::info('Photo deleted successfully', [
                        'photo_id' => $photoId,
                        'place_id' => $place->id,
                        'filename' => $photo->filename,
                    ]);

                } catch (\Exception $e) {
                    Log::error('Failed to delete photo', [
                        'photo_id' => $photoId,
                        'place_id' => $place->id,
                        'filename' => $photo->filename ?? 'unknown',
                        'error' => $e->getMessage(),
                    ]);
                }
            }
        }
    }

    /**
     * Process and add new photos to a place.
     *
     * Gestion EXHAUSTIVE des exceptions :
     * - PhotoValidationException : erreur métier (format, taille) → rollback + throw
     * - PhotoProcessingException : erreur technique (mémoire, driver) → rollback + throw
     * - \Throwable : erreur imprévue → rollback + log + wrapper + throw
     *
     * @param  array<UploadedFile>  $uploadedPhotos
     *
     * @throws PhotoValidationException|PhotoProcessingException|UnexpectedPhotoException
     */
    private function processAndAddPhotos(Place $place, array $uploadedPhotos): void
    {
        $photoData = [];
        $storedFilenames = [];
        $currentMaxSortOrder = $place->photos()->max('sort_order') ?? -1;
        $sortOrder = $currentMaxSortOrder + 1;

        foreach ($uploadedPhotos as $uploadedPhoto) {
            try {
                // Process and store photo with thumbnails
                $processedData = $this->photoService->processWithThumbnails(
                    $uploadedPhoto,
                    'place_photos',
                    '',
                    config('upload.images.max_size_kb') * 1024
                );

                $photoData[] = [
                    'filename' => $processedData['filename'],
                    'original_name' => $processedData['original_name'],
                    'mime_type' => $processedData['mime_type'],
                    'size' => $processedData['size'],
                    'alt_text' => null,
                    'is_main' => false,
                    'sort_order' => $sortOrder,
                ];

                $storedFilenames[] = $processedData['filename'];
                $sortOrder++;

            } catch (PhotoValidationException|PhotoProcessingException $e) {
                // Exceptions attendues : rollback + propagation SANS modification
                $this->rollbackStoredPhotos($storedFilenames, $place->id);

                Log::warning('Photo processing failed during place update', [
                    'place_id' => $place->id,
                    'exception_type' => get_class($e),
                    'message' => $e->getMessage(),
                    'photos_rolled_back' => count($storedFilenames),
                ]);

                throw $e;
            } catch (\Throwable $e) {
                // Exception imprévue : rollback + log critique + wrapper
                $this->rollbackStoredPhotos($storedFilenames, $place->id);

                Log::critical('Unexpected error during photo update', [
                    'place_id' => $place->id,
                    'exception_type' => get_class($e),
                    'message' => $e->getMessage(),
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                    'trace' => $e->getTraceAsString(),
                    'photos_rolled_back' => count($storedFilenames),
                ]);

                throw new UnexpectedPhotoException(
                    "Une erreur inattendue est survenue lors de l'ajout des photos. Veuillez réessayer.",
                    'unexpected',
                    $e
                );
            }
        }

        if (! empty($photoData)) {
            $this->repository->addPhotos($place, $photoData);
        }
    }

    /**
     * Rollback des photos enregistrées en cas d'erreur.
     *
     * IMPORTANT : Cette méthode ne doit JAMAIS lever d'exception.
     * Les erreurs de rollback sont loggées mais n'interrompent pas le flux.
     *
     * @param  array<int, string>  $filenames
     */
    private function rollbackStoredPhotos(array $filenames, int $placeId): void
    {
        foreach ($filenames as $filename) {
            try {
                $this->photoService->deletePhoto(
                    $filename,
                    'place_photos',
                    '',
                    true
                );

                Log::info('Photo rolled back successfully', [
                    'filename' => $filename,
                    'place_id' => $placeId,
                ]);

            } catch (\Throwable $e) {
                // Erreur de rollback : logger mais NE PAS bloquer
                Log::error('Failed to rollback photo during error handling', [
                    'filename' => $filename,
                    'place_id' => $placeId,
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString(),
                ]);
            }
        }
    }

    /**
     * Ensure that the place has a main photo.
     * If no main photo exists, automatically set the first photo as main.
     */
    private function ensureMainPhoto(Place $place): void
    {
        // Refresh photos relation
        $place->load('photos');

        // Check if there's already a main photo
        $hasMainPhoto = $place->photos->contains('is_main', true);

        if (! $hasMainPhoto && $place->photos->isNotEmpty()) {
            // No main photo exists but we have photos: set first one as main
            $firstPhoto = $place->photos->sortBy('sort_order')->first();

            if ($firstPhoto) {
                $this->repository->setMainPhoto($place, $firstPhoto->id);

                Log::info('Auto-assigned first photo as main', [
                    'place_id' => $place->id,
                    'photo_id' => $firstPhoto->id,
                ]);
            }
        }
    }

    /**
     * Process EditRequest photos by copying them from EditRequest storage to place_photos.
     *
     * Utilise copyEditRequestPhotoWithThumbnails() + rollback pattern identique à processAndAddPhotos().
     *
     * @param  array<int, array{id: string, url: string, source: string, path: string, edit_request_id: int, filename: string}>  $editRequestPhotos
     *
     * @throws PhotoValidationException|PhotoProcessingException|UnexpectedPhotoException
     */
    private function processEditRequestPhotos(Place $place, array $editRequestPhotos): void
    {
        $photoData = [];
        $storedFilenames = [];
        $currentMaxSortOrder = $place->photos()->max('sort_order') ?? -1;
        $sortOrder = $currentMaxSortOrder + 1;

        foreach ($editRequestPhotos as $editRequestPhoto) {
            try {
                // Copier la photo avec génération de thumbnails via PhotoProcessingService
                $processedData = $this->photoService->copyEditRequestPhotoWithThumbnails(
                    $editRequestPhoto['filename'],
                    $editRequestPhoto['edit_request_id'],
                    'edit_request_photos',
                    'place_photos',
                    ''
                );

                $photoData[] = [
                    'filename' => $processedData['filename'],
                    'original_name' => $processedData['original_name'],
                    'mime_type' => $processedData['mime_type'],
                    'size' => $processedData['size'],
                    'alt_text' => null,
                    'is_main' => false,
                    'sort_order' => $sortOrder,
                ];

                $storedFilenames[] = $processedData['filename'];
                $sortOrder++;

            } catch (PhotoProcessingException $e) {
                // Exception attendue du PhotoProcessingService : rollback + propagation
                $this->rollbackStoredPhotos($storedFilenames, $place->id);

                Log::warning('EditRequest photo processing failed', [
                    'place_id' => $place->id,
                    'exception_type' => get_class($e),
                    'message' => $e->getMessage(),
                    'photos_rolled_back' => count($storedFilenames),
                ]);

                throw $e;
            } catch (\Throwable $e) {
                // Exception imprévue : rollback + log critique + wrapper
                $this->rollbackStoredPhotos($storedFilenames, $place->id);

                Log::critical('Unexpected error during EditRequest photo copy', [
                    'place_id' => $place->id,
                    'exception_type' => get_class($e),
                    'message' => $e->getMessage(),
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                    'trace' => $e->getTraceAsString(),
                    'photos_rolled_back' => count($storedFilenames),
                ]);

                throw new UnexpectedPhotoException(
                    "Une erreur inattendue est survenue lors de l'ajout des photos proposées. Veuillez réessayer.",
                    'unexpected',
                    $e
                );
            }
        }

        if (! empty($photoData)) {
            $this->repository->addPhotos($place, $photoData);

            Log::info('EditRequest photos added to place', [
                'place_id' => $place->id,
                'count' => count($photoData),
            ]);
        }
    }

    /**
     * Mark EditRequest as accepted and save which changes were applied.
     *
     * @param  array<string, array<int, mixed>>  $appliedChanges  Structure: ['fields' => [...], 'photos' => [...]]
     */
    private function acceptEditRequest(int $editRequestId, int $adminId, array $appliedChanges): void
    {
        /** @var EditRequest|null $editRequest */
        $editRequest = EditRequest::query()->find($editRequestId);

        if (! $editRequest) {
            Log::warning('EditRequest not found for acceptance', [
                'edit_request_id' => $editRequestId,
            ]);

            return;
        }

        $editRequest->update([
            'status' => RequestStatus::Accepted,
            'applied_changes' => $appliedChanges,
            'processed_by_admin_id' => $adminId,
            'processed_at' => now(),
        ]);

        Log::info('EditRequest marked as accepted', [
            'edit_request_id' => $editRequestId,
            'place_id' => $editRequest->place_id,
            'admin_id' => $adminId,
            'applied_changes' => $appliedChanges,
        ]);
    }
}
