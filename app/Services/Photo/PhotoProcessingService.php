<?php

namespace App\Services\Photo;

use App\Exceptions\Photo\PhotoProcessingException;
use App\Exceptions\Photo\PhotoValidationException;
use App\Models\PlaceRequestPhoto;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Drivers\Gd\Driver as GdDriver;
use Intervention\Image\Drivers\Imagick\Driver as ImagickDriver;
use Intervention\Image\ImageManager;
use Intervention\Image\Interfaces\ImageInterface;

class PhotoProcessingService
{
    private ImageManager $imageManager;

    public function __construct()
    {
        $this->imageManager = new ImageManager($this->determineDriver());
    }

    /**
     * Process and store uploaded photo WITH thumbnails (for validated Places).
     *
     * @param  UploadedFile  $file  The uploaded file
     * @param  string  $disk  Storage disk name (e.g., 'place_photos')
     * @param  string  $path  Storage path within disk (e.g., '' or '42')
     * @param  int|null  $maxFileSize  Max file size in bytes (null = use config default)
     * @return array{filename: string, original_name: string, mime_type: string, size: int}
     *
     * @throws PhotoValidationException|PhotoProcessingException
     */
    public function processWithThumbnails(UploadedFile $file, string $disk, string $path = '', ?int $maxFileSize = null): array
    {
        // DEFENSE 1: Temporarily increase memory_limit to handle large images
        $previousLimit = ini_get('memory_limit');
        ini_set('memory_limit', '256M');

        try {
            $startMemory = memory_get_usage(true);

            // 1. Validate file
            $this->validateFile($file, $maxFileSize);

            // 2. Generate short filename (23 chars)
            $filename = $this->generateShortFilename();

            // 3. Load image with Imagick/GD
            $image = $this->imageManager->read($file->getPathname());

            // 4. Resize to max 1200px (before any compression)
            $image = $this->resizeToMaxDimensions($image);

            // 5. Compress original to WebP ≤ 200KB with adaptive quality
            $compressedOriginal = $this->compressToWebP($image);

            // 6. Save original (compressed)
            $fullPath = $this->buildFullPath($path, $filename);
            Storage::disk($disk)->put($fullPath, $compressedOriginal['data']);

            // 7. Generate adaptive thumbnails (proportional to original)
            $this->generateAdaptiveThumbnails($image, $filename, $disk, $path);

            // 8. Cleanup memory
            unset($image);
            gc_collect_cycles();

            return [
                'filename' => $filename,
                'original_name' => $file->getClientOriginalName(),
                'mime_type' => 'image/webp',
                'size' => $compressedOriginal['size'],
            ];

        } catch (PhotoValidationException $e) {
            // Validation error: let it bubble up as-is
            throw $e;
        } catch (\Throwable $e) {

            // Technical error (memory, driver, etc.): log and wrap
            Log::error('Photo processing failed', [
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
                'original_name' => $file->getClientOriginalName(),
                'file_size' => $file->getSize(),
            ]);

            // Throw exception with automatic message handling (generic + details in dev)
            throw PhotoProcessingException::fromException($e);
        } finally {
            // Always restore original memory_limit
            ini_set('memory_limit', $previousLimit);
        }
    }

    /**
     * Process and store uploaded photo WITHOUT thumbnails (for PlaceRequests).
     * Same as processWithThumbnails but skips thumbnail generation.
     *
     * @param  UploadedFile  $file  The uploaded file
     * @param  string  $disk  Storage disk name (e.g., 'place_request_photos')
     * @param  string  $path  Storage path within disk (e.g., '42')
     * @param  int|null  $maxFileSize  Max file size in bytes (null = use config default)
     * @return array{filename: string, original_name: string, mime_type: string, size: int}
     *
     * @throws PhotoValidationException|PhotoProcessingException
     */
    public function processWithoutThumbnails(UploadedFile $file, string $disk, string $path = '', ?int $maxFileSize = null): array
    {
        // DEFENSE 1: Temporarily increase memory_limit to handle large images
        $previousLimit = ini_get('memory_limit');
        ini_set('memory_limit', '256M');

        try {
            $startMemory = memory_get_usage(true);

            // 1. Validate file
            $this->validateFile($file, $maxFileSize);

            // 2. Generate short filename (23 chars)
            $filename = $this->generateShortFilename();

            // 3. Load image with Imagick/GD
            $image = $this->imageManager->read($file->getPathname());

            // 4. Resize to max 1200px (before any compression)
            $image = $this->resizeToMaxDimensions($image);

            // 5. Compress original to WebP ≤ 200KB with adaptive quality
            $compressedOriginal = $this->compressToWebP($image);

            // 6. Save original (compressed)
            $fullPath = $this->buildFullPath($path, $filename);
            Storage::disk($disk)->put($fullPath, $compressedOriginal['data']);

            // 7. NO THUMBNAILS for PlaceRequests

            // 8. Cleanup memory
            unset($image);
            gc_collect_cycles();

            return [
                'filename' => $filename,
                'original_name' => $file->getClientOriginalName(),
                'mime_type' => 'image/webp',
                'size' => $compressedOriginal['size'],
            ];

        } catch (PhotoValidationException $e) {
            // Validation error: let it bubble up as-is
            throw $e;
        } catch (\Throwable $e) {

            // Technical error (memory, driver, etc.): log and wrap
            Log::error('Photo processing failed', [
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
                'original_name' => $file->getClientOriginalName(),
                'file_size' => $file->getSize(),
            ]);

            // Throw exception with automatic message handling (generic + details in dev)
            throw PhotoProcessingException::fromException($e);
        } finally {
            // Always restore original memory_limit
            ini_set('memory_limit', $previousLimit);
        }
    }

    /**
     * Delete a photo and optionally its thumbnails.
     *
     * @param  string  $filename  The photo filename
     * @param  string  $disk  Storage disk name
     * @param  string  $path  Storage path within disk (e.g., '' or '42')
     * @param  bool  $withThumbnails  Whether to delete thumbnails (true for Places, false for PlaceRequests)
     */
    public function deletePhoto(string $filename, string $disk, string $path = '', bool $withThumbnails = true): bool
    {
        $storage = Storage::disk($disk);

        // Delete original
        $fullPath = $this->buildFullPath($path, $filename);
        $storage->delete($fullPath);

        // Delete thumbnails only if requested
        if ($withThumbnails) {
            $thumbPath = $this->buildFullPath($path, 'thumbs/'.$filename);
            $mediumPath = $this->buildFullPath($path, 'medium/'.$filename);

            $storage->delete($thumbPath);
            $storage->delete($mediumPath);
        }

        return true;
    }

    /**
     * Copy an existing photo from one disk to another with thumbnail generation.
     *
     * @param  int  $placeRequestPhotoId  ID du PlaceRequestPhoto à copier
     * @param  string  $sourceDisk  Disk source (ex: place_request_photos)
     * @param  string  $destinationDisk  Disk destination (ex: place_photos)
     * @param  string  $destinationPath  Sous-dossier dans le disk destination
     * @return array{filename: string, original_name: string, mime_type: string, size: int}
     *
     * @throws PhotoProcessingException
     */
    public function copyPlaceRequestPhotoWithThumbnails(
        int $placeRequestPhotoId,
        string $sourceDisk,
        string $destinationDisk,
        string $destinationPath = ''
    ): array {
        // Charger le PlaceRequestPhoto
        $sourcePhoto = PlaceRequestPhoto::query()->find($placeRequestPhotoId);

        if (! $sourcePhoto) {
            throw new PhotoProcessingException(
                "Photo source introuvable (ID: {$placeRequestPhotoId}).",
                'photo.not_found'
            );
        }

        $sourceStorage = Storage::disk($sourceDisk);
        $destStorage = Storage::disk($destinationDisk);
        $sourceFilename = $sourcePhoto->filename;
        $sourcePath = $sourcePhoto->place_request_id.'/'.$sourceFilename;

        // Vérifier que le fichier source existe
        if (! $sourceStorage->exists($sourcePath)) {
            throw new PhotoProcessingException(
                "Fichier source introuvable: {$sourcePath}",
                'photo.source_not_found'
            );
        }

        try {
            // Générer nouveau nom avec UUID pour éviter conflits
            $extension = pathinfo($sourceFilename, PATHINFO_EXTENSION);
            $newFilename = $this->generateShortFilename();

            // Copier le fichier original
            $sourceContent = $sourceStorage->get($sourcePath);
            $destinationFullPath = $this->buildFullPath($destinationPath, $newFilename);
            $destStorage->put($destinationFullPath, $sourceContent);

            // Charger l'image pour générer les thumbnails
            $image = $this->imageManager->read($sourceContent);

            // Générer les miniatures
            $this->generateAdaptiveThumbnails($image, $newFilename, $destinationDisk, $destinationPath);

            // Cleanup
            unset($image);
            gc_collect_cycles();

            return [
                'filename' => $newFilename,
                'original_name' => $sourcePhoto->original_name,
                'mime_type' => $sourcePhoto->mime_type,
                'size' => $sourcePhoto->size,
            ];

        } catch (\Throwable $e) {
            Log::error('Photo copy failed', [
                'place_request_photo_id' => $placeRequestPhotoId,
                'source_disk' => $sourceDisk,
                'destination_disk' => $destinationDisk,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            throw new PhotoProcessingException(
                "Erreur lors de la copie de la photo: {$e->getMessage()}",
                'photo.copy_failed',
                $e
            );
        }
    }

    /**
     * Determine which image driver to use (Imagick if available, otherwise GD).
     */
    private function determineDriver(): GdDriver|ImagickDriver
    {
        $configDriver = config('upload.images.driver', 'auto');

        if ($configDriver === 'auto') {
            return extension_loaded('imagick')
                ? new ImagickDriver
                : new GdDriver;
        }

        return match ($configDriver) {
            'imagick' => new ImagickDriver,
            'gd' => new GdDriver,
            default => new GdDriver,
        };
    }

    /**
     * Validate uploaded file.
     *
     * @throws PhotoValidationException
     */
    private function validateFile(UploadedFile $file, ?int $maxFileSize = null): void
    {
        $maxSizeKB = $maxFileSize ?? (config('upload.images.max_size_kb') * 1024);
        $allowedMimes = config('upload.images.allowed_mimes');

        // Check file size
        if ($file->getSize() > $maxSizeKB) {
            $maxSizeMB = round($maxSizeKB / 1048576, 1);

            throw new PhotoValidationException(
                "La taille du fichier dépasse la limite autorisée de {$maxSizeMB} Mo.",
                'photo.size_limit',
            );
        }

        // Check mime type
        if (! in_array($file->getMimeType(), $allowedMimes, true)) {
            throw PhotoValidationException::invalidFormat();
        }

        // Check if file is a valid image and get dimensions
        $imageInfo = @getimagesize($file->getPathname());
        if (! $imageInfo) {
            throw new PhotoValidationException(
                "Une des images sélectionnée n'est pas valide. Modifiez votre sélection."
            );
        }

        // PREVENTIVE CHECK: Estimate memory required and reject if risk of crash
        $this->validateMemoryRequirement($imageInfo, $file->getClientOriginalName());
    }

    /**
     * Validate that processing the image won't cause memory exhaustion.
     * This PREVENTS the fatal "Allowed memory size exhausted" error.
     *
     * @param  array<mixed>  $imageInfo  Result from getimagesize()
     *
     * @throws PhotoValidationException
     */
    private function validateMemoryRequirement(array $imageInfo, string $filename): void
    {
        $width = $imageInfo[0];
        $height = $imageInfo[1];

        // Calculate estimated memory needed for processing
        // Formula: (width × height × 3 bytes RGB) × 3 (original + work + output)
        $pixels = $width * $height;
        $bytesPerPixel = 3; // RGB
        $processingMultiplier = 3; // Original + intermediate + output
        $estimatedMemoryBytes = $pixels * $bytesPerPixel * $processingMultiplier;

        // Get available memory
        $availableMemoryBytes = $this->getAvailableMemory();

        // Safety threshold: use only configured % of available memory to be extra safe
        $safetyThreshold = config('upload.images.memory_safety_threshold', 0.8);
        $safeThreshold = $availableMemoryBytes * $safetyThreshold;

        // Reject if estimated memory exceeds safe threshold
        if ($estimatedMemoryBytes > $safeThreshold) {
            $imageSizeMp = round($pixels / 1000000, 1);
            $estimatedMb = round($estimatedMemoryBytes / 1024 / 1024);
            $availableMb = round($availableMemoryBytes / 1024 / 1024);

            throw new PhotoValidationException(
                "Une image est trop grande pour être traitée ({$imageSizeMp} mégapixels, {$width}×{$height}). ".
                "Veuillez réduire la résolution de votre image avant de l'uploader. ".
                'Résolution maximale recommandée : 2000×2000 pixels.',
                'photo.size_limit'
            );
        }
    }

    /**
     * Calculate available memory for image processing.
     */
    private function getAvailableMemory(): int
    {
        // Get PHP memory_limit
        $memoryLimit = ini_get('memory_limit');

        // Convert to bytes
        $memoryLimitBytes = $this->convertToBytes($memoryLimit);

        // Get current memory usage
        $currentUsage = memory_get_usage(true);

        // Available = Limit - Current Usage
        return $memoryLimitBytes - $currentUsage;
    }

    /**
     * Convert PHP memory string (e.g., "128M") to bytes.
     */
    private function convertToBytes(string $value): int
    {
        $value = trim($value);
        $unit = strtolower($value[strlen($value) - 1]);
        $number = (int) substr($value, 0, -1);

        return match ($unit) {
            'g' => $number * 1024 * 1024 * 1024,
            'm' => $number * 1024 * 1024,
            'k' => $number * 1024,
            default => (int) $value,
        };
    }

    /**
     * Generate short unique WebP filename.
     * Format: {timestamp}_{random-7-chars}.webp (22 chars total)
     */
    private function generateShortFilename(): string
    {
        $timestamp = now()->timestamp; // 10 chars
        $random = Str::random(7); // 7 chars

        return "{$timestamp}_{$random}.webp"; // 10 + 1 + 7 + 5 = 23 chars
    }

    /**
     * Resize image to max dimensions while preserving aspect ratio.
     * - Applies max to dominant side (width for landscape, height for portrait)
     * - Crops to max ratio (10:1) if image is too extreme
     */
    private function resizeToMaxDimensions(ImageInterface $image): ImageInterface
    {
        $maxWidth = config('upload.images.original.max_width');
        $maxHeight = config('upload.images.original.max_height');
        $maxRatio = config('upload.images.max_aspect_ratio');

        $width = $image->width();
        $height = $image->height();
        $ratio = $width / $height;

        // Check for extreme ratio (> 10:1 or < 1:10)
        if ($ratio > $maxRatio || $ratio < 1 / $maxRatio) {
            $image = $this->cropToMaxRatio($image, $maxRatio);
            $width = $image->width();
            $height = $image->height();
        }

        // Resize based on dominant side
        if ($width >= $height) {
            // Landscape: apply max to width
            if ($width > $maxWidth) {
                $image->scale(width: $maxWidth); // Height auto-adjusts
            }
        } else {
            // Portrait: apply max to height
            if ($height > $maxHeight) {
                $image->scale(height: $maxHeight); // Width auto-adjusts
            }
        }

        return $image;
    }

    /**
     * Crop image to max acceptable aspect ratio.
     * Centers the crop to preserve important content.
     */
    private function cropToMaxRatio(ImageInterface $image, float $maxRatio): ImageInterface
    {
        $width = $image->width();
        $height = $image->height();
        $ratio = $width / $height;

        if ($ratio > $maxRatio) {
            // Too wide: crop width
            $newWidth = (int) ($height * $maxRatio);
            $image->crop(
                width: $newWidth,
                height: $height,
                position: 'center'
            );
        } elseif ($ratio < 1 / $maxRatio) {
            // Too tall: crop height
            $newHeight = (int) ($width * $maxRatio);
            $image->crop(
                width: $width,
                height: $newHeight,
                position: 'center'
            );
        }

        return $image;
    }

    /**
     * Compress image to WebP format until it reaches target size.
     * Uses adaptive quality reduction.
     *
     * @return array{data: string, size: int}
     */
    private function compressToWebP(ImageInterface $image): array
    {
        $maxSizeKB = config('upload.images.original.max_size_kb');
        $maxSizeBytes = $maxSizeKB * 1024;
        $qualityStart = config('upload.images.original.quality_start');
        $qualityMin = config('upload.images.original.quality_min');

        $quality = $qualityStart;
        $data = '';
        $size = 0;

        while ($quality >= $qualityMin) {
            // Encode to WebP
            $encoded = $image->toWebp(quality: $quality);
            $data = (string) $encoded;
            $size = strlen($data);

            // Check if size is acceptable
            if ($size <= $maxSizeBytes) {
                return [
                    'data' => $data,
                    'size' => $size,
                ];
            }

            // Reduce quality for next iteration
            $quality -= 5;
        }

        // If still too large at minimum quality, return anyway
        // (should rarely happen with reasonable images after resize)
        return [
            'data' => $data,
            'size' => $size,
        ];
    }

    /**
     * Generate adaptive thumbnails proportional to original size.
     * - medium: min(800, original × 2/3)
     * - thumbs: min(300, original × 1/2)
     * - No upscaling: skips thumbnails larger than original
     */
    private function generateAdaptiveThumbnails(ImageInterface $original, string $filename, string $diskName, string $path): void
    {
        $disk = Storage::disk($diskName);
        $originalWidth = $original->width();
        $thumbnails = config('upload.images.thumbnails');

        foreach ($thumbnails as $sizeName => $config) {
            // Calculate target width: min(max_width, original × ratio)
            $targetWidth = (int) min(
                $config['max_width'],
                $originalWidth * $config['ratio_of_original']
            );

            // Skip if target would be >= original (no upscaling)
            if ($targetWidth >= $originalWidth) {
                continue;
            }

            // Create thumbnail from original (no clone, new instance)
            $thumbnail = $this->imageManager->read((string) $original->toWebp());
            $thumbnail->scale(width: $targetWidth);

            // Encode with fixed quality
            $encoded = $thumbnail->toWebp(quality: $config['quality']);

            // Save in subdirectory
            $thumbnailPath = $this->buildFullPath($path, "{$sizeName}/{$filename}");
            $disk->put($thumbnailPath, (string) $encoded);

            // Explicit memory cleanup
            unset($thumbnail);
        }
    }

    /**
     * Copy an existing EditRequest photo with thumbnail generation.
     *
     * @param  string  $sourceFilename  Raw filename (ex: 'photo.jpg')
     * @param  int  $editRequestId  ID de l'EditRequest (utilisé pour construire le chemin source)
     * @param  string  $sourceDisk  Disk source (ex: edit_request_photos)
     * @param  string  $destinationDisk  Disk destination (ex: place_photos)
     * @param  string  $destinationPath  Sous-dossier dans le disk destination
     * @return array{filename: string, original_name: string, mime_type: string, size: int}
     *
     * @throws PhotoProcessingException
     */
    public function copyEditRequestPhotoWithThumbnails(
        string $sourceFilename,
        int $editRequestId,
        string $sourceDisk,
        string $destinationDisk,
        string $destinationPath = ''
    ): array {
        $sourceStorage = Storage::disk($sourceDisk);
        $destStorage = Storage::disk($destinationDisk);
        $sourcePath = $editRequestId.'/'.$sourceFilename;

        // Vérifier que le fichier source existe
        if (! $sourceStorage->exists($sourcePath)) {
            throw new PhotoProcessingException(
                "Fichier source EditRequest introuvable: {$sourcePath}",
                'photo.source_not_found'
            );
        }

        try {
            // Générer nouveau nom avec UUID pour éviter conflits
            $newFilename = $this->generateShortFilename();

            // Copier le fichier original
            $sourceContent = $sourceStorage->get($sourcePath);
            $destinationFullPath = $this->buildFullPath($destinationPath, $newFilename);
            $destStorage->put($destinationFullPath, $sourceContent);

            // Charger l'image pour générer les thumbnails
            $image = $this->imageManager->read($sourceContent);

            // Générer les miniatures
            $this->generateAdaptiveThumbnails($image, $newFilename, $destinationDisk, $destinationPath);

            // Cleanup
            unset($image);
            gc_collect_cycles();

            // Récupérer les métadonnées du fichier copié
            $fileSize = $destStorage->size($destinationFullPath);
            $mimeType = $destStorage->mimeType($destinationFullPath) ?: 'image/webp';

            return [
                'filename' => $newFilename,
                'original_name' => $sourceFilename,
                'mime_type' => $mimeType,
                'size' => $fileSize,
            ];

        } catch (\Throwable $e) {
            Log::error('EditRequest photo copy failed', [
                'source_filename' => $sourceFilename,
                'edit_request_id' => $editRequestId,
                'source_disk' => $sourceDisk,
                'destination_disk' => $destinationDisk,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            throw new PhotoProcessingException(
                "Erreur lors de la copie de la photo EditRequest: {$e->getMessage()}",
                'photo.copy_failed',
                $e
            );
        }
    }

    /**
     * Build full path by combining path and filename.
     * Handles empty paths gracefully.
     *
     * @param  string  $path  Base path (can be empty)
     * @param  string  $filename  Filename or subpath
     * @return string Full path
     */
    private function buildFullPath(string $path, string $filename): string
    {
        if (empty($path)) {
            return $filename;
        }

        return rtrim($path, '/').'/'.$filename;
    }
}
