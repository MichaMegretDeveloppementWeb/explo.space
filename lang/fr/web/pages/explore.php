<?php

return [
    'hero' => [
        'title' => 'Explorer l\'univers spatial',
        'subtitle' => 'Découvrez les lieux emblématiques de la conquête spatiale et de l\'exploration de l\'univers près de chez vous ou dans le monde entier.',
    ],

    // Modale de prévisualisation de lieu
    'place_preview' => [
        'view_detail' => 'Voir le détail complet',
        'error_title' => 'Erreur',
        'error_not_found' => 'Ce lieu n\'a pas été trouvé.',
        'error_invalid_id' => 'Identifiant de lieu invalide.',
        'error_translation_missing' => 'La traduction de ce lieu n\'est pas disponible dans la langue sélectionnée.',
        'error_loading' => 'Une erreur est survenue lors du chargement du lieu.',
        'close' => 'Fermer',
    ],

    'seo' => [
        'title' => 'Explorer les lieux spatiaux',
        'description' => 'Explorez plus de 1,200 lieux emblématiques de la conquête spatiale et de l\'exploration de l\'univers. Recherchez par proximité géographique ou découvrez le monde entier avec notre carte interactive.',
        'keywords' => 'explorer, lieux spatiaux, conquête spatiale, NASA, SpaceX, carte interactive, proximité, exploration univers',

        // Open Graph spécifique à cette page
        'og' => [
            'type' => 'website',
            'image_alt' => 'Explorer les lieux spatiaux sur '.config('app.name'),
        ],

        // Twitter Cards spécifique à cette page
        'twitter' => [
            'card' => 'summary_large_image',
        ],

        // JSON-LD spécifique à cette page
        'json_ld' => [
            'website' => [
                'description' => 'Explorez les lieux emblématiques de la conquête spatiale près de vous ou dans le monde entier',
                'search_action' => [
                    '@type' => 'SearchAction',
                    'target' => [
                        '@type' => 'EntryPoint',
                        'urlTemplate' => 'https://explo.space/fr/explorer?mode=proximity&address={search_term_string}',
                    ],
                    'query-input' => 'required name=search_term_string',
                ],
            ],
        ],
    ],

    // Traductions du composant Livewire spécifique à cette page
    'livewire' => [
        // Mode selector
        'mode_proximity' => 'Autour de moi',
        'mode_worldwide' => 'Monde entier',

        // Address search
        'address_placeholder' => 'Saisir une adresse',

        // Radius control
        'radius_search_label' => 'Rayon de recherche',
        'radius_unit' => 'km',

        // Tag filtering
        'tags_search_placeholder' => 'Rechercher par thématique...',
        'filters_tags_label' => 'Filtres',
        'filters_title' => 'Filtres',
        'clear_all_tags' => 'Effacer tout',

        // Featured places filter
        'featured_toggle_label' => 'Lieux emblématiques',
        'featured_toggle_help' => 'Afficher uniquement les lieux sélectionnés par la rédaction',

        // Results header
        'results_title' => 'Résultats',
        'results_radius' => 'Dans un rayon de',
        'spatial_places' => 'Lieux spatiaux',
        'places_count' => 'lieu(x)',
        'filters_count' => 'filtre(s)',
        'loading' => 'Chargement...',
        'loading_more' => 'Chargement...',
        'no_more_results' => 'Vous avez atteint la fin des résultats',

        // Results list/cards
        'no_place_found' => 'Aucun lieu trouvé',
        'no_results_proximity_no_address' => 'Saisissez une adresse ou activez votre géolocalisation pour découvrir les lieux spatiaux à proximité.',
        'no_results_proximity_zone' => 'Aucun lieu spatial dans cette zone. Essayez d\'élargir le rayon de recherche.',
        'no_results_worldwide_no_tags' => 'Sélectionnez au moins une thématique pour découvrir les lieux spatiaux dans le monde.',
        'no_results_worldwide_tags' => 'Aucun lieu spatial ne correspond aux thématiques sélectionnées. Essayez d\'autres thématiques.',
        'no_results_filters' => 'Aucun lieu ne correspond aux filtres sélectionnés.',
        'no_results_criteria' => 'Aucun lieu ne correspond à vos critères de recherche.',

        // Empty States - État 1 : Conditions minimales NON réunies (encourageant, bleu)
        'empty_state_start_search_title' => 'Commencez votre recherche',
        'empty_state_proximity_no_address' => 'Saisissez une adresse dans la barre de recherche ou activez votre géolocalisation pour découvrir les lieux spatiaux autour de vous.',
        'empty_state_worldwide_no_tags' => 'Sélectionnez au moins une thématique pour explorer les lieux spatiaux correspondants dans le monde entier.',

        // Empty States - État 2 : Recherche effectuée SANS résultats (neutre, gris)
        'no_results_title' => 'Aucun lieu trouvé',
        'no_results_proximity_suggestion' => 'Essayez d\'élargir le rayon de recherche.',
        'no_results_worldwide_suggestion' => 'Essayez d\'autres thématiques ou supprimez certains filtres.',

        // Mobile specific
        'location_required' => 'Localisation requise',
        'location_required_text' => 'Saisissez une adresse ou activez votre géolocalisation pour découvrir les lieux spatiaux à proximité',
        'geolocating' => 'Localisation...',
        'geolocate_me' => '📍 Me localiser',
        'view_detail' => 'Voir le détail',
        'view_place' => 'Voir le lieu',
        'directions' => 'Itinéraire',
        'increase_radius' => 'Aucun lieu spatial dans cette zone. Essayez d\'augmenter le rayon de recherche.',

        // Pagination
        'previous' => 'Précédent',
        'next' => 'Suivant',
        'pagination_of' => 'sur',

        // Map
        'map_proximity_search' => 'Recherche par proximité',
        'map_proximity_description' => 'Saisissez une adresse dans la barre de recherche ou activez votre géolocalisation pour découvrir les lieux spatiaux autour de vous.',
        'map_radius_up_to' => 'Jusqu\'à 1500 km de rayon',
        'map_realtime_search' => 'Recherche temps réel',
        'map_interactive' => 'Carte Interactive',
        'map_places_in_radius' => 'lieu(x) dans un rayon de',
        'map_spatial_places_worldwide' => 'lieu(x) spatial(ux) dans le monde',
        'map_filtered_by' => 'Filtré par',
        'map_themes' => 'thématique(s)',
        'map_loading' => 'Chargement de la carte...',
    ],
];
