<?php

return [
    'hero' => [
        'title' => 'Explore Space Universe',
        'subtitle' => 'Discover iconic places of space conquest and universe exploration near you or worldwide.',
    ],

    // Place preview modal
    'place_preview' => [
        'view_detail' => 'View full details',
        'error_title' => 'Error',
        'error_not_found' => 'This place was not found.',
        'error_invalid_id' => 'Invalid place identifier.',
        'error_translation_missing' => 'The translation for this place is not available in the selected language.',
        'error_loading' => 'An error occurred while loading the place.',
        'close' => 'Close',
    ],

    'seo' => [
        'title' => 'Explore Space Places',
        'description' => 'Explore over 1,200 iconic places of space conquest and universe exploration. Search by geographical proximity or discover the whole world with our interactive map.',
        'keywords' => 'explore, space places, space conquest, NASA, SpaceX, interactive map, proximity, universe exploration',

        // Open Graph spécifique à cette page
        'og' => [
            'type' => 'website',
            'image_alt' => 'Explore space places on '.config('app.name'),
        ],

        // Twitter Cards spécifique à cette page
        'twitter' => [
            'card' => 'summary_large_image',
        ],

        // JSON-LD spécifique à cette page
        'json_ld' => [
            'website' => [
                'description' => 'Explore iconic places of space conquest near you or worldwide',
                'search_action' => [
                    '@type' => 'SearchAction',
                    'target' => [
                        '@type' => 'EntryPoint',
                        'urlTemplate' => 'https://explo.space/en/explore?mode=proximity&address={search_term_string}',
                    ],
                    'query-input' => 'required name=search_term_string',
                ],
            ],
        ],
    ],

    // Livewire component translations specific to this page
    'livewire' => [
        // Mode selector
        'mode_proximity' => 'Near me',
        'mode_worldwide' => 'Worldwide',

        // Address search
        'address_placeholder' => 'Enter an address',

        // Radius control
        'radius_search_label' => 'Search radius',
        'radius_unit' => 'km',

        // Tag filtering
        'tags_search_placeholder' => 'Search by theme...',
        'filters_tags_label' => 'Filters',
        'filters_title' => 'Filters',
        'clear_all_tags' => 'Clear all',

        // Results header
        'results_title' => 'Results',
        'results_radius' => 'Within a radius of',
        'spatial_places' => 'Space places',
        'places_count' => 'place(s)',
        'filters_count' => 'filter(s)',
        'loading' => 'Loading...',
        'loading_more' => 'Loading...',
        'no_more_results' => 'You have reached the end of results',

        // Results list/cards
        'no_place_found' => 'No place found',
        'no_results_proximity_no_address' => 'Enter an address or enable your geolocation to discover nearby space places.',
        'no_results_proximity_zone' => 'No space places in this area. Try widening the search radius.',
        'no_results_worldwide_no_tags' => 'Select at least one theme to discover space places around the world.',
        'no_results_worldwide_tags' => 'No space places match the selected themes. Try different themes.',
        'no_results_filters' => 'No places match the selected filters.',
        'no_results_criteria' => 'No places match your search criteria.',

        // Empty States - State 1: Minimum conditions NOT met (encouraging, blue)
        'empty_state_start_search_title' => 'Start your search',
        'empty_state_proximity_no_address' => 'Enter an address in the search bar or enable your geolocation to discover space places around you.',
        'empty_state_worldwide_no_tags' => 'Select at least one theme to explore matching space places worldwide.',

        // Empty States - State 2: Search performed WITHOUT results (neutral, gray)
        'no_results_title' => 'No place found',
        'no_results_proximity_suggestion' => 'Try widening the search radius.',
        'no_results_worldwide_suggestion' => 'Try different themes or remove some filters.',

        // Mobile specific
        'location_required' => 'Location required',
        'location_required_text' => 'Enter an address or enable your geolocation to discover nearby space places',
        'geolocating' => 'Locating...',
        'geolocate_me' => '📍 Locate me',
        'view_detail' => 'View detail',
        'directions' => 'Directions',
        'increase_radius' => 'No space places in this area. Try increasing the search radius.',

        // Pagination
        'previous' => 'Previous',
        'next' => 'Next',
        'pagination_of' => 'of',

        // Map
        'map_proximity_search' => 'Proximity search',
        'map_proximity_description' => 'Enter an address in the search bar or enable your geolocation to discover space places around you.',
        'map_radius_up_to' => 'Up to 1500 km radius',
        'map_realtime_search' => 'Real-time search',
        'map_interactive' => 'Interactive Map',
        'map_places_in_radius' => 'place(s) within a radius of',
        'map_spatial_places_worldwide' => 'space place(s) worldwide',
        'map_filtered_by' => 'Filtered by',
        'map_themes' => 'theme(s)',
        'map_loading' => 'Loading map...',
    ],
];
