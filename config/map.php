<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Système de bounding box dynamique
    |--------------------------------------------------------------------------
    |
    | Ce paramètre contrôle le comportement de chargement des marqueurs sur la carte.
    |
    | Si true : Les lieux sont chargés dynamiquement selon la zone visible (bounding box)
    |           → Optimisé pour grandes bases de données (> 1000 lieux)
    |           → Requêtes SQL à chaque déplacement/zoom de la carte
    |           → Affichage légèrement différé (~200-300ms au chargement initial)
    |
    | Si false : Tous les lieux correspondant aux filtres sont chargés une fois
    |            → Optimisé pour petites bases de données (< 1000 lieux)
    |            → Une seule requête SQL au chargement
    |            → Affichage immédiat des marqueurs
    |
    | IMPORTANT : Après modification en production, exécuter :
    |   php artisan config:clear
    |   php artisan config:cache
    |
    */
    'use_bounding_box' => env('MAP_USE_BOUNDING_BOX', false),

    /*
    |--------------------------------------------------------------------------
    | Limites géographiques globales
    |--------------------------------------------------------------------------
    |
    | Limites de coordonnées utilisées partout dans l'application :
    | - Validation des filtres d'exploration
    | - Création/édition de lieux (admin et suggestions utilisateurs)
    | - Génération de données de test (factories)
    | - Bounding box par défaut
    |
    | LATITUDE : Limitée à ±85° (projection Web Mercator de Leaflet/OSM)
    |            Au-delà, les pôles sont à l'infini et ne peuvent pas être
    |            affichés correctement sur une carte plane.
    |
    | LONGITUDE : Plage complète ±180°
    |
    */
    'coordinates' => [
        'latitude' => [
            'min' => -85,
            'max' => 85,
        ],
        'longitude' => [
            'min' => -180,
            'max' => 180,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Bounding box par défaut (monde entier)
    |--------------------------------------------------------------------------
    |
    | Utilisée lorsque aucune bounding box n'est fournie explicitement.
    | Représente la zone visible maximale sur une carte Web Mercator.
    |
    */
    'default_bounding_box' => [
        'north' => 85,
        'south' => -85,
        'east' => 180,
        'west' => -180,
    ],
];
