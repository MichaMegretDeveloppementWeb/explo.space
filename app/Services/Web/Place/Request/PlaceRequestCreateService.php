<?php

namespace App\Services\Web\Place\Request;

use App\Contracts\Repositories\Web\Place\PlaceRequest\PlaceRequestCreateRepositoryInterface;
use App\Contracts\Translation\TranslationStrategyInterface;
use App\Enums\RequestStatus;
use App\Exceptions\Photo\PhotoProcessingException;
use App\Exceptions\Photo\PhotoValidationException;
use App\Exceptions\Photo\UnexpectedPhotoException;
use App\Models\PlaceRequest;
use App\Services\Photo\PhotoProcessingService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class PlaceRequestCreateService
{
    public function __construct(
        private readonly PlaceRequestCreateRepositoryInterface $repository,
        private readonly TranslationStrategyInterface $translationStrategy,
        private readonly PhotoProcessingService $photoProcessingService
    ) {}

    /**
     * Créer une nouvelle demande de lieu.
     *
     * @param  array{title: string, description: ?string, practical_info: ?string, latitude: ?float, longitude: ?float, address: ?string, contact_email: string, photos?: array<int, UploadedFile>}  $data
     *
     * @throws PhotoValidationException|PhotoProcessingException|UnexpectedPhotoException|\Throwable
     */
    public function create(array $data): PlaceRequest
    {
        return DB::transaction(function () use ($data) {
            // Extraire et combiner les textes pour la détection de langue
            $textsToAnalyze = array_filter([
                $data['title'] ?? '',
                $data['description'] ?? '',
                $data['practical_info'] ?? '',
            ]);

            // Combiner les textes avec des espaces et limiter à 50 caractères
            $combinedText = implode(' ', $textsToAnalyze);
            $combinedText = mb_substr($combinedText, 0, 50);

            // Détecter la langue via la Translation Strategy (minimum 20 caractères)
            $detectedLanguage = strlen($combinedText) >= 5
                ? $this->translationStrategy->detectLanguage($combinedText)
                : 'unknown';

            // Générer le slug depuis le titre
            $slug = Str::slug($data['title']);

            // Créer la demande de lieu
            $placeRequest = $this->repository->create([
                'title' => $data['title'],
                'slug' => $slug,
                'description' => $data['description'] ?? null,
                'practical_info' => $data['practical_info'] ?? null,
                'latitude' => $data['latitude'] ?? null,
                'longitude' => $data['longitude'] ?? null,
                'address' => $data['address'] ?? null,
                'contact_email' => $data['contact_email'],
                'detected_language' => $detectedLanguage,
                'status' => RequestStatus::Submitted->value, // Cast to string for repository
            ]);

            // Stocker les photos si présentes (sans générer de miniatures)
            if (! empty($data['photos'])) {
                $this->storePhotosWithoutThumbnails($placeRequest, $data['photos']);
            }

            return $placeRequest;
        });
    }

    /**
     * Stocker les photos sans générer de miniatures.
     * Les miniatures seront générées par l'admin lors de l'acceptation.
     *
     * Gestion EXHAUSTIVE des exceptions :
     * - PhotoValidationException : erreur métier (format, taille) → rollback + throw
     * - PhotoProcessingException : erreur technique (mémoire, driver) → rollback + throw
     * - \Throwable : erreur imprévue → rollback + log + wrapper + throw
     *
     * @param  array<int, UploadedFile>  $uploadedPhotos
     *
     * @throws PhotoValidationException|PhotoProcessingException|UnexpectedPhotoException
     */
    private function storePhotosWithoutThumbnails(PlaceRequest $placeRequest, array $uploadedPhotos): void
    {
        $maxFileSize = config('upload.images.max_size_kb') * 1024;
        $storedPhotos = [];

        foreach ($uploadedPhotos as $index => $uploadedPhoto) {
            try {
                // Utiliser PhotoProcessingService pour traiter l'image
                $photoData = $this->photoProcessingService->processWithoutThumbnails(
                    $uploadedPhoto,
                    'place_request_photos',
                    (string) $placeRequest->id,
                    $maxFileSize
                );

                // Créer l'enregistrement en base
                $this->repository->createPhoto($placeRequest, [
                    'filename' => $photoData['filename'],
                    'original_name' => $photoData['original_name'],
                    'mime_type' => $photoData['mime_type'],
                    'size' => $photoData['size'],
                    'sort_order' => $index,
                ]);

                // Garder trace des photos enregistrées pour rollback si nécessaire
                $storedPhotos[] = $photoData['filename'];

            } catch (PhotoValidationException|PhotoProcessingException $e) {
                // Exceptions attendues : rollback + propagation SANS modification
                $this->rollbackStoredPhotos($storedPhotos, $placeRequest->id);

                Log::warning('Photo processing failed for place request', [
                    'place_request_id' => $placeRequest->id,
                    'exception_type' => get_class($e),
                    'message' => $e->getMessage(),
                    'photos_rolled_back' => count($storedPhotos),
                ]);

                throw $e;
            } catch (\Throwable $e) {
                // Exception imprévue : rollback + log critique + wrapper
                $this->rollbackStoredPhotos($storedPhotos, $placeRequest->id);

                Log::critical('Unexpected error during place request photo processing', [
                    'place_request_id' => $placeRequest->id,
                    'exception_type' => get_class($e),
                    'message' => $e->getMessage(),
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                    'trace' => $e->getTraceAsString(),
                    'photos_rolled_back' => count($storedPhotos),
                ]);

                throw new UnexpectedPhotoException(
                    'Une erreur inattendue est survenue lors du traitement des photos. Veuillez réessayer.',
                    'unexpected',
                    $e
                );
            }
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
    private function rollbackStoredPhotos(array $filenames, int $placeRequestId): void
    {
        foreach ($filenames as $filename) {
            try {
                $this->photoProcessingService->deletePhoto(
                    $filename,
                    'place_request_photos',
                    (string) $placeRequestId,
                    false
                );

                Log::info('Photo rolled back successfully', [
                    'filename' => $filename,
                    'place_request_id' => $placeRequestId,
                ]);

            } catch (\Throwable $e) {
                // Erreur de rollback : logger mais NE PAS bloquer
                Log::error('Failed to rollback photo during error handling', [
                    'filename' => $filename,
                    'place_request_id' => $placeRequestId,
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString(),
                ]);
            }
        }
    }
}
