<?php

namespace App\Contracts\Services\Admin\Auth;

use App\DTO\Admin\Auth\LoginCredentialsDTO;
use App\DTO\Admin\Auth\LoginResultDTO;

interface AdminAuthenticationServiceInterface
{
    /**
     * Tenter d'authentifier un administrateur avec les credentials fournis
     */
    public function authenticate(LoginCredentialsDTO $credentials): LoginResultDTO;

    /**
     * Déconnecter l'administrateur actuel
     */
    public function logout(): void;

    /**
     * Vérifier si le rate limiting est dépassé pour une clé donnée
     */
    public function isRateLimited(string $throttleKey): bool;

    /**
     * Obtenir le nombre de secondes restantes avant le prochain essai
     */
    public function getRateLimitSeconds(string $throttleKey): int;

    /**
     * Générer une clé de throttle unique pour un email et une IP
     */
    public function generateThrottleKey(string $email, string $ip): string;
}
