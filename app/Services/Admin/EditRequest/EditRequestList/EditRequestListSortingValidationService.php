<?php

namespace App\Services\Admin\EditRequest\EditRequestList;

use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class EditRequestListSortingValidationService
{
    /**
     * Colonnes autorisées pour le tri
     */
    private const ALLOWED_COLUMNS = ['place', 'type', 'contact_email', 'status', 'created_at'];

    /**
     * Directions autorisées pour le tri
     */
    private const ALLOWED_DIRECTIONS = ['asc', 'desc'];

    /**
     * Tri par défaut
     */
    private const DEFAULT_SORT = [
        'column' => 'created_at',
        'direction' => 'desc',
    ];

    /**
     * Valider et nettoyer les paramètres de tri
     *
     * @param  array{sortBy?: string, sortDirection?: string}  $sorting
     * @return array{column: string, direction: string}
     *
     * @throws ValidationException
     */
    public function validate(array $sorting): array
    {
        $validator = Validator::make($sorting, [
            'sortBy' => 'nullable|string|in:'.implode(',', self::ALLOWED_COLUMNS),
            'sortDirection' => 'nullable|string|in:'.implode(',', self::ALLOWED_DIRECTIONS),
        ]);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        $validated = $validator->validated();

        return [
            'column' => $validated['sortBy'] ?? self::DEFAULT_SORT['column'],
            'direction' => $validated['sortDirection'] ?? self::DEFAULT_SORT['direction'],
        ];
    }

    /**
     * Récupérer la liste des colonnes autorisées
     *
     * @return array<int, string>
     */
    public function getAllowedColumns(): array
    {
        return self::ALLOWED_COLUMNS;
    }

    /**
     * Récupérer les directions autorisées
     *
     * @return array<int, string>
     */
    public function getAllowedDirections(): array
    {
        return self::ALLOWED_DIRECTIONS;
    }

    /**
     * Récupérer le tri par défaut
     *
     * @return array{column: string, direction: string}
     */
    public function getDefaultSort(): array
    {
        return self::DEFAULT_SORT;
    }
}
