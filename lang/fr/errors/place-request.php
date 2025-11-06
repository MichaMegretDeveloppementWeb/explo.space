<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Erreurs de proposition de lieu (Place Request)
    |--------------------------------------------------------------------------
    |
    | Messages d'erreur traduits pour le formulaire de proposition de lieu.
    | Ces traductions sont basées sur le type d'erreur (errorType) de l'exception.
    |
    */

    // Photos - Validation
    'photo.general' => "Le fichier photo n'est pas valide.",
    'photo.invalid_format' => 'Le fichier doit être une image (JPEG, PNG, WebP).',
    'photo.size_limit' => 'Le fichier ne doit pas dépasser la taille maximale autorisée.',
    'photo.svg_not_allowed' => 'Les fichiers SVG ne sont pas autorisés pour des raisons de sécurité.',

    // Photos - Traitement
    'photo.processing' => 'Une erreur est survenue lors du traitement des images. Veuillez réessayer avec des images plus légères.',
    'photo.unexpected' => 'Une erreur inattendue est survenue lors du traitement des photos. Veuillez réessayer.',

    // Autres types d'erreurs possibles
    'general' => 'Une erreur est survenue. Veuillez réessayer.',
    'recaptcha.failed' => 'La vérification reCAPTCHA a échoué. Veuillez réessayer.',
    'database.error' => 'Une erreur de base de données est survenue. Veuillez réessayer.',
];
