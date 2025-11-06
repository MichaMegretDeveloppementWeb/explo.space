<?php

namespace App\Services\Admin\PlaceRequest\PlaceRequestList;

use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class PlaceRequestListSortingValidationService
{
    /**
     * Colonnes autorisées pour le tri
     */
    private const ALLOWED_COLUMNS = ['title', 'status', 'created_at'];

    /**
     * Directions autorisées pour le tri
     */
    private const ALLOWED_DIRECTIONS = ['asc', 'desc'];

    /**
     * Tri par défaut
     */
    private const DEFAULT_SORT = [
        'sortBy' => 'created_at',
        'sortDirection' => 'desc',
    ];

    /**
     * Valider et nettoyer les paramètres de tri
     *
     * @param  array{sortBy?: string, sortDirection?: string}  $sorting
     * @return array{sortBy: string, sortDirection: string}
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
            'sortBy' => $validated['sortBy'] ?? self::DEFAULT_SORT['sortBy'],
            'sortDirection' => $validated['sortDirection'] ?? self::DEFAULT_SORT['sortDirection'],
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
     * @return array{sortBy: string, sortDirection: string}
     */
    public function getDefaultSort(): array
    {
        return self::DEFAULT_SORT;
    }
}
