<?php

namespace App\Services\Admin\Place\Create;

use App\Contracts\Repositories\Admin\Place\Create\PlaceCreateRepositoryInterface;
use App\Exceptions\Photo\PhotoProcessingException;
use App\Exceptions\Photo\PhotoValidationException;
use App\Exceptions\Photo\UnexpectedPhotoException;
use App\Models\Place;
use App\Services\Photo\PhotoProcessingService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PlaceCreateService
{
    public function __construct(
        private PlaceCreateRepositoryInterface $repository,
        private PhotoProcessingService $photoService
    ) {}

    /**
     * Create a new place with all related data.
     *
     * @param  array<string, mixed>  $data
     *
     * @throws PhotoValidationException|PhotoProcessingException|UnexpectedPhotoException|\Throwable
     */
    public function create(array $data): Place
    {
        return DB::transaction(function () use ($data) {
            // Create base place
            $place = $this->repository->create([
                'latitude' => $data['latitude'],
                'longitude' => $data['longitude'],
                'address' => $data['address'] ?? null,
                'admin_id' => $data['admin_id'],
                'is_featured' => $data['is_featured'] ?? false,
                'request_id' => $data['request_id'] ?? null,
            ]);

            // Create translations
            $this->repository->createTranslations($place, $data['translations']);

            // Attach relations
            if (! empty($data['category_ids'])) {
                $this->repository->attachCategories($place, $data['category_ids']);
            }

            if (! empty($data['tag_ids'])) {
                $this->repository->attachTags($place, $data['tag_ids']);
            }

            // Process and copy PlaceRequest photos (first, to set correct sort_order)
            $nextSortOrder = 0;
            if (! empty($data['place_request_photos'])) {
                $nextSortOrder = $this->copyPlaceRequestPhotos($place, $data['place_request_photos']);
            }

            // Process and create new uploaded photos
            if (! empty($data['photos'])) {
                $this->processAndCreatePhotos($place, $data['photos'], $nextSortOrder);
            }

            // Mark PlaceRequest as accepted if applicable
            if (! empty($data['request_id'])) {
                $placeRequest = $this->repository->findPlaceRequestById($data['request_id']);

                if ($placeRequest) {
                    $this->repository->markPlaceRequestAsAccepted($placeRequest, $data['admin_id']);

                    Log::info('PlaceRequest marked as accepted', [
                        'place_request_id' => $placeRequest->id,
                        'place_id' => $place->id,
                        'admin_id' => $data['admin_id'],
                    ]);
                }
            }

            Log::info('Place created successfully', [
                'place_id' => $place->id,
                'admin_id' => $data['admin_id'],
            ]);

            return $place;
        });
    }

    /**
     * Process uploaded photos and create photo records.
     *
     * Gestion EXHAUSTIVE des exceptions :
     * - PhotoValidationException : erreur métier (format, taille) → rollback + throw
     * - PhotoProcessingException : erreur technique (mémoire, driver) → rollback + throw
     * - \Throwable : erreur imprévue → rollback + log + wrapper + throw
     *
     * @param  array<UploadedFile>  $uploadedPhotos
     * @param  int  $startSortOrder  Sort order de départ (pour continuer après PlaceRequest photos)
     *
     * @throws PhotoValidationException|PhotoProcessingException|UnexpectedPhotoException
     */
    private function processAndCreatePhotos(Place $place, array $uploadedPhotos, int $startSortOrder = 0): void
    {
        $photoData = [];
        $storedFilenames = [];
        $sortOrder = $startSortOrder;

        foreach ($uploadedPhotos as $uploadedPhoto) {
            try {
                // Process and store photo with thumbnails
                $processedData = $this->photoService->processWithThumbnails(
                    $uploadedPhoto,
                    'place_photos',
                    '',
                    null
                );

                $photoData[] = [
                    'filename' => $processedData['filename'],
                    'original_name' => $processedData['original_name'],
                    'mime_type' => $processedData['mime_type'],
                    'size' => $processedData['size'],
                    'alt_text' => null,
                    'is_main' => $sortOrder === 0, // Main photo = first photo overall
                    'sort_order' => $sortOrder,
                ];

                $storedFilenames[] = $processedData['filename'];
                $sortOrder++;

            } catch (PhotoValidationException|PhotoProcessingException $e) {
                // Exceptions attendues : rollback + propagation SANS modification
                $this->rollbackStoredPhotos($storedFilenames);

                Log::warning('Photo processing failed with expected exception', [
                    'place_id' => $place->id,
                    'exception_type' => get_class($e),
                    'message' => $e->getMessage(),
                    'photos_rolled_back' => count($storedFilenames),
                ]);

                throw $e;
            } catch (\Throwable $e) {
                // Exception imprévue : rollback + log critique + wrapper
                $this->rollbackStoredPhotos($storedFilenames);

                Log::critical('Unexpected error during photo processing', [
                    'place_id' => $place->id,
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

        if (! empty($photoData)) {
            $this->repository->createPhotos($place, $photoData);
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
    private function rollbackStoredPhotos(array $filenames): void
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
                ]);

            } catch (\Throwable $e) {
                // Erreur de rollback : logger mais NE PAS bloquer
                Log::error('Failed to rollback photo during error handling', [
                    'filename' => $filename,
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString(),
                ]);
            }
        }
    }

    /**
     * Copy photos from PlaceRequest to Place with thumbnail generation.
     *
     * @param  array<int, array{id: int, url: string, medium_url: string, source: string}>  $placeRequestPhotos
     * @return int Next sort_order value (nombre de photos copiées)
     *
     * @throws PhotoProcessingException|UnexpectedPhotoException
     */
    private function copyPlaceRequestPhotos(Place $place, array $placeRequestPhotos): int
    {
        $photoData = [];
        $sortOrder = 0;

        foreach ($placeRequestPhotos as $prPhoto) {
            try {
                // Copier la photo depuis place_request_photos vers place_photos
                $processedData = $this->photoService->copyPhotoWithThumbnails(
                    $prPhoto['id'],
                    'place_request_photos',
                    'place_photos',
                    ''
                );

                $photoData[] = [
                    'filename' => $processedData['filename'],
                    'original_name' => $processedData['original_name'],
                    'mime_type' => $processedData['mime_type'],
                    'size' => $processedData['size'],
                    'alt_text' => null,
                    'is_main' => $sortOrder === 0, // Première photo = principale
                    'sort_order' => $sortOrder,
                ];

                $sortOrder++;

            } catch (PhotoProcessingException $e) {
                Log::warning('PlaceRequest photo copy failed', [
                    'place_id' => $place->id,
                    'place_request_photo_id' => $prPhoto['id'],
                    'message' => $e->getMessage(),
                ]);

                throw $e;
            } catch (\Throwable $e) {
                Log::critical('Unexpected error copying PlaceRequest photo', [
                    'place_id' => $place->id,
                    'place_request_photo_id' => $prPhoto['id'],
                    'exception_type' => get_class($e),
                    'message' => $e->getMessage(),
                    'trace' => $e->getTraceAsString(),
                ]);

                throw new UnexpectedPhotoException(
                    'Une erreur inattendue est survenue lors de la copie des photos. Veuillez réessayer.',
                    'photo.unexpected',
                    $e
                );
            }
        }

        if (! empty($photoData)) {
            $this->repository->createPhotos($place, $photoData);
        }

        return $sortOrder; // Retourne le prochain sort_order disponible
    }
}
