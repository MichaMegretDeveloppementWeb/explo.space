<?php

namespace App\Services\Admin\PlaceRequest\PlaceRequestList;

use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class PlaceRequestListFilterValidationService
{
    /**
     * Valider et nettoyer les filtres
     *
     * @param  array{status?: string|array<string>}  $filters
     * @return array{status: array<string>}
     *
     * @throws ValidationException
     */
    public function validateAndClean(array $filters): array
    {
        // Normaliser le statut AVANT validation
        if (isset($filters['status'])) {
            // Si "all" ou chaîne vide, normaliser en tableau vide
            if ($filters['status'] === 'all' || $filters['status'] === '') {
                $filters['status'] = [];
            } elseif (is_string($filters['status'])) {
                // Trim spaces et filtrer les valeurs vides pour les chaînes séparées par virgules
                // array_values() pour ré-indexer les clés après array_filter
                $filters['status'] = array_values(array_filter(
                    array_map('trim', explode(',', $filters['status']))
                ));
            }
        }

        // Maintenant valider avec le tableau normalisé
        $validator = Validator::make($filters, [
            'status' => 'nullable|array',
            'status.*' => 'string|in:submitted,pending,accepted,refused',
        ]);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        $validated = $validator->validated();

        // Retourner le statut validé (déjà un tableau après normalisation)
        return [
            'status' => $validated['status'] ?? [],
        ];
    }

    /**
     * Vérifier si les filtres sont vides (affichage de tous les statuts)
     *
     * @param  array{status: array<string>}  $filters
     */
    public function areFiltersEmpty(array $filters): bool
    {
        return empty($filters['status']);
    }
}
