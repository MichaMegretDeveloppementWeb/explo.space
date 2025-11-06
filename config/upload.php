<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Configuration des uploads d'images
    |--------------------------------------------------------------------------
    |
    | Configuration centralisée pour la gestion des uploads d'images
    | dans toute l'application.
    |
    */

    'images' => [
        // Taille maximale par image uploadée (en KB)
        'max_size_kb' => 10240, // 10 Mo

        // Nombre maximum d'images par entité
        'max_files' => 10,

        // Extensions autorisées
        'allowed_extensions' => ['jpg', 'jpeg', 'png', 'webp'],

        // Types MIME autorisés
        'allowed_mimes' => ['image/jpeg', 'image/jpg', 'image/png', 'image/webp'],

        // ==========================================
        // Configuration du traitement d'images
        // ==========================================

        // Dimensions maximales de l'image principale (original)
        'original' => [
            'max_width' => 1200,         // Largeur max (appliquée au côté dominant)
            'max_height' => 1200,        // Hauteur max (appliquée au côté dominant)
            'max_size_kb' => 200,        // Taille max après compression WebP
            'quality_start' => 90,       // Qualité WebP de départ
            'quality_min' => 60,         // Qualité WebP minimale acceptable
        ],

        // Miniatures générées automatiquement
        'thumbnails' => [
            'medium' => [
                'max_width' => 800,           // Largeur maximale
                'ratio_of_original' => 2 / 3,   // 2/3 de l'original si plus petit
                'quality' => 85,              // Qualité WebP fixe
            ],
            'thumbs' => [
                'max_width' => 300,           // Largeur maximale
                'ratio_of_original' => 1 / 2,   // 1/2 de l'original si plus petit
                'quality' => 85,              // Qualité WebP fixe
            ],
        ],

        // Garde-fou ratio extrême (10:1 ou 1:10)
        'max_aspect_ratio' => 10,

        // Seuil de sécurité mémoire (% de mémoire disponible utilisable)
        // 0.8 = utilise max 80% de la mémoire disponible pour éviter les crashs
        'memory_safety_threshold' => 0.9,

        // Driver d'image (Imagick si disponible, sinon GD)
        'driver' => env('IMAGE_DRIVER', 'auto'), // 'auto', 'imagick', 'gd'

        // Monitoring mémoire (log en dev/staging)
        'log_memory_usage' => env('APP_ENV') !== 'production',
    ],
];
