<?php

namespace App\Services\Admin\Settings\AdminList;

use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class AdminListFilterValidationService
{
    /**
     * Valider et nettoyer les filtres
     *
     * @param  array{search?: string}  $filters
     * @return array{search: string}
     *
     * @throws ValidationException
     */
    public function validate(array $filters): array
    {
        $validator = Validator::make($filters, [
            'search' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        $validated = $validator->validated();

        return [
            'search' => trim($validated['search'] ?? ''),
        ];
    }

    /**
     * Vérifier si les filtres sont vides
     *
     * @param  array{search: string}  $filters
     */
    public function areFiltersEmpty(array $filters): bool
    {
        return empty($filters['search']);
    }
}
