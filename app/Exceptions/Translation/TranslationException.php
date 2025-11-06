<?php

namespace App\Exceptions\Translation;

use Exception;

/**
 * Base exception for translation errors
 */
class TranslationException extends Exception
{
    private string $userMessage;

    private string $technicalMessage;

    /**
     * Create a new translation exception for quota exceeded
     */
    public static function quotaExceeded(): self
    {
        $exception = new self(
            'Le quota de traduction a été dépassé.',
            429
        );

        $exception->userMessage = 'Le quota de traduction a été dépassé. Veuillez réessayer plus tard ou contacter l\'administrateur.';
        $exception->technicalMessage = 'DeepL API quota exceeded';

        return $exception;
    }

    /**
     * Create a new translation exception for invalid API key
     */
    public static function invalidApiKey(): self
    {
        $exception = new self(
            'La clé API de traduction est invalide.',
            401
        );

        $exception->userMessage = 'La clé API de traduction est invalide. Veuillez contacter l\'administrateur.';
        $exception->technicalMessage = 'DeepL API key is invalid or missing';

        return $exception;
    }

    /**
     * Create a new translation exception for connection failure
     */
    public static function connectionFailed(string $technicalDetails): self
    {
        $exception = new self(
            'Impossible de se connecter au service de traduction.',
            503
        );

        $exception->userMessage = 'Impossible de se connecter au service de traduction. Veuillez réessayer.';
        $exception->technicalMessage = 'DeepL API connection failed: '.$technicalDetails;

        return $exception;
    }

    /**
     * Create a new translation exception for general error
     */
    public static function translationFailed(string $technicalDetails): self
    {
        $exception = new self(
            'La traduction a échoué.',
            500
        );

        $exception->userMessage = 'La traduction a échoué. Veuillez réessayer ou contacter l\'administrateur.';
        $exception->technicalMessage = 'DeepL translation failed: '.$technicalDetails;

        return $exception;
    }

    /**
     * Get user-friendly message (always safe to display)
     */
    public function getUserMessage(): string
    {
        return $this->userMessage ?? $this->getMessage();
    }

    /**
     * Get technical message (for logs and development mode only)
     */
    public function getTechnicalMessage(): string
    {
        return $this->technicalMessage ?? $this->getMessage();
    }

    /**
     * Get appropriate message based on environment
     */
    public function getDisplayMessage(): string
    {
        return app()->environment('local', 'development')
            ? $this->getTechnicalMessage()
            : $this->getUserMessage();
    }
}
