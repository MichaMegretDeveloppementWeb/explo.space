<?php

namespace App\Exceptions\Photo;

use App\Exceptions\ApplicationException;

/**
 * Exception levée lors du traitement technique d'une photo.
 *
 * Erreurs possibles :
 * - Mémoire insuffisante
 * - Format d'image corrompu
 * - Erreur driver (GD/Imagick)
 * - Échec de redimensionnement/compression
 * - Erreur d'écriture sur le disque
 *
 * Cette exception représente une erreur technique et affiche
 * un message générique à l'utilisateur (avec détails en dev).
 */
class PhotoProcessingException extends ApplicationException
{
    public function __construct(
        string $message = 'Une erreur est survenue lors du traitement des images. Veuillez réessayer avec des images plus légères.',
        string $type = 'photo.processing',
        ?\Throwable $e = null
    ) {
        parent::__construct($message, $type, $e);
    }

    /**
     * Créer depuis une exception technique
     */
    public static function fromException(\Throwable $exception): self
    {
        return new self(
            'Une erreur est survenue lors du traitement des images. Veuillez réessayer avec des images plus légères.',
            'photo.processing',
            $exception
        );
    }
}
