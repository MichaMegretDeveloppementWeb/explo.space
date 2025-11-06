<?php

namespace App\Services\Admin\EditRequest\EditRequestList;

use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class EditRequestListFilterValidationService
{
    /**
     * Valider et nettoyer les filtres
     *
     * @param  array{search?: string, type?: string, status?: string}  $filters
     * @return array{search: string, type: string, status: string}
     *
     * @throws ValidationException
     */
    public function validateAndClean(array $filters): array
    {
        $validator = Validator::make($filters, [
            'search' => 'nullable|string|max:255',
            'type' => 'nullable|string|in:modification,signalement,photo_suggestion',
            'status' => 'nullable|string|in:submitted,pending,accepted,refused',
        ]);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        $validated = $validator->validated();

        return [
            'search' => trim($validated['search'] ?? ''),
            'type' => $validated['type'] ?? '',
            'status' => $validated['status'] ?? '',
        ];
    }

    /**
     * Vérifier si les filtres sont vides
     *
     * @param  array{search: string, type: string, status: string}  $filters
     */
    public function areFiltersEmpty(array $filters): bool
    {
        return empty($filters['search']) && empty($filters['type']) && empty($filters['status']);
    }
}
