<?php

return [
    // Filtres
    'filters' => [
        'validation_error_title' => 'Filtres invalides',
        'invalid_mode' => 'Mode de recherche invalide (:mode). Veuillez sélectionner "Autour de moi" ou "Monde entier".',
        'invalid_radius' => 'Le rayon sélectionné (:radius km) est invalide. Veuillez choisir un rayon entre :min km et :max km.',
        'invalid_coordinates' => 'La :type (:value) est invalide. Les valeurs valides sont comprises entre :range.',
        'coordinate_latitude' => 'latitude',
        'coordinate_longitude' => 'longitude',
        'invalid_tags' => ':count tag(s) invalide(s) : :tags. Veuillez vérifier votre sélection.',
    ],

    // Chargement carte
    'map' => [
        'loading_failed' => 'Impossible de charger la carte. Veuillez rafraîchir la page ou réessayer plus tard.',
        'coordinates_loading_failed' => 'Impossible de charger les marqueurs sur la carte. Veuillez réessayer.',
        'bounding_box_error' => 'Erreur lors du calcul de la zone visible. Veuillez zoomer ou déplacer la carte.',
        'system_error' => 'Une erreur est survenue lors du chargement de la carte. Veuillez réessayer. Si le problème persiste, contactez le support client.',
    ],

    // Chargement liste
    'list' => [
        'loading_failed' => 'Impossible de charger les lieux. Veuillez réessayer.',
        'no_results' => 'Aucun lieu trouvé correspondant à vos critères de recherche.',
        'system_error' => 'Une erreur est survenue lors du chargement des résultats. Veuillez réessayer. Si le problème persiste, contactez le support client.',
    ],

    // Erreurs système (affichées uniquement si critique pour utilisateur)
    'system' => [
        'database_error' => 'Une erreur technique est survenue. Notre équipe a été notifiée. Veuillez réessayer dans quelques instants.',
        'service_unavailable' => 'Le service est temporairement indisponible. Veuillez réessayer dans quelques instants.',
    ],
];
