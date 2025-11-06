<?php

namespace App\DTO\Admin\Auth;

readonly class LoginResultDTO
{
    public function __construct(
        public bool $success,
        public ?string $errorMessage = null,
        public ?string $redirectUrl = null,
    ) {}

    /**
     * Créer une instance pour un succès d'authentification
     */
    public static function success(string $redirectUrl): self
    {
        return new self(
            success: true,
            errorMessage: null,
            redirectUrl: $redirectUrl,
        );
    }

    /**
     * Créer une instance pour un échec d'authentification
     */
    public static function failure(string $errorMessage): self
    {
        return new self(
            success: false,
            errorMessage: $errorMessage,
            redirectUrl: null,
        );
    }
}
