<?php

namespace App\Exceptions;

use Exception;

/**
 * Exception de base pour toutes les exceptions applicatives.
 *
 * Garantit que toutes les exceptions de l'app ont :
 * - Un message formaté pour l'utilisateur
 * - Un code HTTP approprié
 * - La chaîne de previous exceptions
 *
 * Principe : Les messages sont formatés à la SOURCE et ne sont JAMAIS modifiés
 * par les couches intermédiaires. Seule la couche de présentation (Controller/Livewire)
 * affiche le message final.
 */
abstract class ApplicationException extends Exception
{
    private string $userMessage = '';

    private ?string $technicalMessage = null;

    private string $errorType;

    public function __construct(
        string $message,
        string $type = 'general',
        ?\Throwable $e = null,
    ) {

        $this->userMessage = $message;

        if ($e) {
            $this->technicalMessage = $e->getMessage();
        }

        $this->errorType = $type;

        $fullMessage = $this->userMessage;

        if ($this->shouldShowTechnicalDetails() && $this->technicalMessage && ($this->technicalMessage !== $this->userMessage)) {
            $fullMessage .= " [Technical: {$this->technicalMessage}]";
        }

        parent::__construct($fullMessage);

    }

    public function getUserMessage(): string
    {
        return $this->userMessage;
    }

    public function getTechnicalMessage(): string
    {
        return $this->technicalMessage;
    }

    /**
     * Vérifier si on doit afficher les détails techniques.
     */
    public function shouldShowTechnicalDetails(): bool
    {
        return app()->environment('local', 'development', 'testing');
    }

    public function getErrorType(): string
    {
        return $this->errorType;
    }
}
