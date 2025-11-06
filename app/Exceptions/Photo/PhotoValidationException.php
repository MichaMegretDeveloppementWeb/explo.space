<?php

namespace App\Exceptions\Photo;

use App\Exceptions\ApplicationException;

/**
 * Exception levée lors de la validation d'une photo uploadée.
 *
 * Cette exception représente une erreur métier (utilisateur)
 * et doit afficher un message clair à l'utilisateur.
 *
 * Exemples d'erreurs de validation :
 * - Format de fichier non autorisé (SVG, etc.)
 * - Taille de fichier trop importante
 * - Dimensions d'image non conformes
 * - Extension de fichier invalide
 */
class PhotoValidationException extends ApplicationException
{
    public function __construct(
        string $message = "Le fichier photo n'est pas valide.",
        string $type = 'photo.general',
        ?\Throwable $e = null
    ) {
        parent::__construct($message, $type, $e);
    }

    /**
     * Helper : Format invalide
     */
    public static function invalidFormat(): self
    {
        return new self(
            'Le fichier doit être une image (JPEG, PNG, WebP).',
            'photo.invalid_format'
        );
    }

    /**
     * Helper : Fichier trop lourd
     */
    public static function fileTooLarge(): self
    {
        return new self(
            'Le fichier ne doit pas dépasser la taille maximale autorisée.',
            'photo.size_limit'
        );
    }

    /**
     * Helper : SVG non autorisé
     */
    public static function svgNotAllowed(): self
    {
        return new self(
            'Les fichiers SVG ne sont pas autorisés pour des raisons de sécurité.',
            'photo.svg_not_allowed'
        );
    }
}
