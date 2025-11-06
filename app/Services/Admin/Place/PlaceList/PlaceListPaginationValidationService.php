<?php

namespace App\Services\Admin\Place\PlaceList;

use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class PlaceListPaginationValidationService
{
    /**
     * Valeurs autorisées pour le nombre d'éléments par page
     */
    private const ALLOWED_PER_PAGE = [10, 20, 30, 50];

    /**
     * Valeur par défaut
     */
    private const DEFAULT_PER_PAGE = 20;

    /**
     * Valider et nettoyer le paramètre de pagination
     *
     * @param  array{perPage?: int}  $pagination
     *
     * @throws ValidationException
     */
    public function validateAndClean(array $pagination): int
    {
        $validator = Validator::make($pagination, [
            'perPage' => 'nullable|integer|in:'.implode(',', self::ALLOWED_PER_PAGE),
        ]);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        $validated = $validator->validated();

        return $validated['perPage'] ?? self::DEFAULT_PER_PAGE;
    }

    /**
     * Récupérer les valeurs autorisées
     *
     * @return array<int, int>
     */
    public function getAllowedValues(): array
    {
        return self::ALLOWED_PER_PAGE;
    }

    /**
     * Récupérer la valeur par défaut
     */
    public function getDefaultValue(): int
    {
        return self::DEFAULT_PER_PAGE;
    }
}
