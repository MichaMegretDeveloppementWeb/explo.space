<?php

namespace App\Services\Admin\Auth;

use App\Contracts\Services\Admin\Auth\AdminAuthenticationServiceInterface;
use App\DTO\Admin\Auth\LoginCredentialsDTO;
use App\DTO\Admin\Auth\LoginResultDTO;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;

class AdminAuthenticationService implements AdminAuthenticationServiceInterface
{
    /**
     * Nombre maximum de tentatives de connexion
     */
    private const MAX_ATTEMPTS = 5;

    /**
     * Durée du rate limiting en minutes
     */
    private const DECAY_MINUTES = 1;

    /**
     * Tenter d'authentifier un administrateur avec les credentials fournis
     */
    public function authenticate(LoginCredentialsDTO $credentials): LoginResultDTO
    {
        // Générer la clé de throttle
        $throttleKey = $this->generateThrottleKey($credentials->email, $credentials->ip);

        // Vérifier le rate limiting
        if ($this->isRateLimited($throttleKey)) {
            $seconds = $this->getRateLimitSeconds($throttleKey);

            return LoginResultDTO::failure(
                "Trop de tentatives de connexion. Veuillez réessayer dans {$seconds} secondes."
            );
        }

        // Tentative d'authentification
        $authenticated = Auth::attempt(
            ['email' => $credentials->email, 'password' => $credentials->password],
            $credentials->remember
        );

        if (! $authenticated) {
            // Incrémenter le compteur de tentatives
            RateLimiter::hit($throttleKey, self::DECAY_MINUTES * 60);

            return LoginResultDTO::failure(
                'Ces identifiants ne correspondent pas à nos enregistrements.'
            );
        }

        // Vérifier que l'utilisateur a des droits admin
        if (! Auth::user()->hasAdminRights()) {
            // Déconnecter l'utilisateur non-admin
            Auth::logout();

            // Incrémenter le compteur de tentatives
            RateLimiter::hit($throttleKey, self::DECAY_MINUTES * 60);

            return LoginResultDTO::failure(
                'Ces identifiants ne correspondent pas à nos enregistrements.'
            );
        }

        // Authentification réussie
        // Reset rate limiter
        RateLimiter::clear($throttleKey);

        // Retourner le succès avec l'URL de redirection
        return LoginResultDTO::success(route('admin.dashboard'));
    }

    /**
     * Déconnecter l'administrateur actuel
     */
    public function logout(): void
    {
        Auth::logout();

        request()->session()->invalidate();
        request()->session()->regenerateToken();
    }

    /**
     * Vérifier si le rate limiting est dépassé pour une clé donnée
     */
    public function isRateLimited(string $throttleKey): bool
    {
        return RateLimiter::tooManyAttempts($throttleKey, self::MAX_ATTEMPTS);
    }

    /**
     * Obtenir le nombre de secondes restantes avant le prochain essai
     */
    public function getRateLimitSeconds(string $throttleKey): int
    {
        return RateLimiter::availableIn($throttleKey);
    }

    /**
     * Générer une clé de throttle unique pour un email et une IP
     */
    public function generateThrottleKey(string $email, string $ip): string
    {
        return strtolower($email).'|'.$ip;
    }
}
