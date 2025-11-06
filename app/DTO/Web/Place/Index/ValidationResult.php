<?php

namespace App\DTO\Web\Place\Index;

/**
 * Résultat de validation des filtres d'exploration
 *
 * Encapsule le résultat complet d'une validation, incluant :
 * - Le statut de validation (valide ou non)
 * - Les erreurs détectées par clé de filtre
 * - Les filtres corrigés (valeurs valides ou valeurs par défaut)
 * - Les filtres originaux (pour traçabilité et logging)
 *
 * Utilisé par PlaceExplorationFiltersValidationService pour retourner
 * un résultat de validation structuré selon la stratégie appliquée.
 */
readonly class ValidationResult
{
    /**
     * @param  bool  $isValid  Indique si tous les filtres sont valides
     * @param  array<string, string>  $errors  Map clé filtre => code erreur (ex: ['mode' => 'invalid_mode'])
     * @param  array<string, mixed>  $correctedFilters  Filtres après correction (valeurs valides ou defaults)
     * @param  array<string, mixed>  $originalFilters  Filtres avant toute correction (traçabilité)
     */
    public function __construct(
        public bool $isValid,
        public array $errors,
        public array $correctedFilters,
        public array $originalFilters
    ) {}

    /**
     * Vérifie si un filtre spécifique a une erreur
     *
     * @param  string  $filterKey  Clé du filtre (ex: 'mode', 'latitude', 'radius')
     * @return bool True si le filtre a une erreur
     */
    public function hasError(string $filterKey): bool
    {
        return isset($this->errors[$filterKey]);
    }

    /**
     * Récupère le code d'erreur pour un filtre spécifique
     *
     * @param  string  $filterKey  Clé du filtre
     * @return string|null Code d'erreur ou null si pas d'erreur
     */
    public function getError(string $filterKey): ?string
    {
        return $this->errors[$filterKey] ?? null;
    }

    /**
     * Récupère tous les codes d'erreur (sans les clés)
     *
     * @return array<int, string> Liste des codes d'erreur
     */
    public function getErrorCodes(): array
    {
        return array_values($this->errors);
    }

    /**
     * Compte le nombre d'erreurs détectées
     *
     * @return int Nombre d'erreurs
     */
    public function getErrorCount(): int
    {
        return count($this->errors);
    }

    /**
     * Vérifie si au moins une erreur a été détectée
     *
     * @return bool True si au moins une erreur
     */
    public function hasErrors(): bool
    {
        return ! $this->isValid;
    }
}
