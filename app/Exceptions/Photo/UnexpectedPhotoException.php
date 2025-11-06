<?php

namespace App\Exceptions\Photo;

use App\Exceptions\ApplicationException;

/**
 * Exception levée lors d'une erreur imprévue durant le traitement photo.
 *
 * Cette exception wrapper toutes les erreurs inattendues pour garantir
 * un message cohérent à l'utilisateur.
 *
 * Exemples d'erreurs wrappées :
 * - OutOfMemoryError
 * - Erreurs inconnues de driver
 * - Erreurs système non anticipées
 * - Exceptions inattendues du framework
 */
class UnexpectedPhotoException extends ApplicationException
{
    public function __construct(
        string $message = 'Une erreur inattendue est survenue lors du traitement des photos. Veuillez réessayer.',
        string $type = 'photo.unexpected',
        ?\Throwable $e = null
    ) {
        parent::__construct($message, $type, $e);
    }
}
