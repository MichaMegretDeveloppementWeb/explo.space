<?php

namespace App\Services\Admin\Place\PlaceList;

use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class PlaceListFilterValidationService
{
    /**
     * Valider et nettoyer les filtres
     *
     * @param  array{search?: string, tags?: array<int, string>, locale?: string}  $filters
     * @return array{search: string, tags: array<int, string>, locale: string}
     *
     * @throws ValidationException
     */
    public function validate(array $filters): array
    {
        $validator = Validator::make($filters, [
            'search' => 'nullable|string|max:255',
            'tags' => 'nullable|array',
            'tags.*' => 'string|max:255',
            'locale' => 'nullable|string|in:fr,en',
        ]);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        $validated = $validator->validated();

        return [
            'search' => trim($validated['search'] ?? ''),
            'tags' => $validated['tags'] ?? [],
            'locale' => $validated['locale'] ?? config('locales.default', 'fr'),
        ];
    }

    /**
     * Vérifier si les filtres sont vides
     *
     * @param  array{search: string, tags: array<int, string>, locale: string}  $filters
     */
    public function areFiltersEmpty(array $filters): bool
    {
        return empty($filters['search']) && empty($filters['tags']);
    }
}
