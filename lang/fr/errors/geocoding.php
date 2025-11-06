<?php

return [
    'no_results' => 'Aucune adresse trouvée pour cette recherche. Essayez avec des termes plus précis.',
    'connection_failed' => 'Impossible de se connecter au service de géolocalisation. Vérifiez votre connexion internet.',
    'rate_limited' => 'Trop de recherches effectuées. Veuillez patienter :seconds secondes avant de réessayer.',
    'service' => [
        'rate_limit' => 'Trop de recherches en cours. Veuillez patienter quelques secondes avant de réessayer.',
        'unavailable' => 'Le service de géolocalisation est temporairement indisponible. Veuillez réessayer plus tard.',
        'generic' => 'Erreur du service de géolocalisation. Veuillez réessayer.',
    ],
    'request_failed' => 'Erreur lors de la requête de géolocalisation. Veuillez réessayer.',
    'unexpected' => 'Une erreur inattendue s\'est produite. Veuillez réessayer.',

    // Reverse geocoding specific errors
    'reverse' => [
        'no_results' => 'Aucune adresse trouvée pour ces coordonnées.',
        'invalid_coordinates' => 'Les coordonnées fournies ne sont pas valides.',
        'location_not_geocodable' => 'Cette localisation ne peut pas être géocodée (zone non cartographiée ou océan).',
    ],
];
