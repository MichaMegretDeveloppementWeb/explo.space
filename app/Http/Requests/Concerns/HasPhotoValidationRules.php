<?php

namespace App\Http\Requests\Concerns;

/**
 * Trait pour centraliser les règles de validation des photos.
 *
 * Utilisé par :
 * - PlaceStoreRequest (validation lors de l'enregistrement du formulaire)
 * - ManagesPhotos (validation temps réel lors de l'upload)
 *
 * Source unique de vérité pour éviter les incohérences.
 */
trait HasPhotoValidationRules
{
    /**
     * Règles de validation pour les photos
     *
     * @param  string  $fieldName  Nom du champ à valider ('photos' ou 'pendingPhotos')
     * @return array<string, array<int, string>>
     */
    protected function getPhotoValidationRules(string $fieldName = 'photos'): array
    {
        $maxSizeKB = config('upload.images.max_size_kb');
        $maxFiles = config('upload.images.max_files');
        $mimes = implode(',', config('upload.images.allowed_extensions'));

        return [
            $fieldName => ['nullable', 'array', "max:{$maxFiles}"],
            "{$fieldName}.*" => ['file', "mimes:{$mimes}", "max:{$maxSizeKB}"],
        ];
    }

    /**
     * Messages personnalisés pour les erreurs de validation des photos
     *
     * @param  string  $fieldName  Nom du champ validé ('photos' ou 'pendingPhotos')
     * @return array<string, string>
     */
    protected function getPhotoValidationMessages(string $fieldName = 'photos'): array
    {
        $maxSizeKB = config('upload.images.max_size_kb');
        $maxSizeMB = round($maxSizeKB / 1024, 1);
        $maxFiles = config('upload.images.max_files');

        return [
            "{$fieldName}.max" => __('web/validation/photos.max', ['max' => $maxFiles]),
            "{$fieldName}.*.mimes" => __('web/validation/photos.mimes'),
            "{$fieldName}.*.max" => __('web/validation/photos.size', ['size' => $maxSizeMB]),
        ];
    }

    /**
     * Configuration complète pour injection dans Alpine.js ou autres
     *
     * @return array{maxFiles: int, maxSizeKB: int, maxSizeMB: float, allowedExtensions: array<int, string>, allowedMimes: array<int, string>}
     */
    public function getPhotoValidationConfig(): array
    {
        return [
            'maxFiles' => config('upload.images.max_files'),
            'maxSizeKB' => config('upload.images.max_size_kb'),
            'maxSizeMB' => round(config('upload.images.max_size_kb') / 1024, 1),
            'allowedExtensions' => config('upload.images.allowed_extensions'),
            'allowedMimes' => config('upload.images.allowed_mimes'),
        ];
    }
}
