<?php

return [
    'hero' => [
        'title' => [
            'part1' => 'The space universe',
            'part2' => 'at your fingertips',
        ],
        'subtitle' => [
            'part1' => 'Discover iconic places',
            'part2' => 'related to space exploration.',
            'part3' => 'From launch centers to museums, explore world space history.',
        ],
        'cta' => [
            'primary' => 'Start exploring',
            'secondary' => 'Suggest a place',
        ],
        'stats' => [
            'places' => [
                'label' => 'Places listed',
            ],
            'featured' => [
                'label' => 'Iconic places',
            ],
            'themes' => [
                'label' => 'Themes',
            ],
        ],
    ],

    'how_it_works' => [
        'title' => 'How does it work?',
        'subtitle' => 'Discovering space locations has never been easier',
        'steps' => [
            'search' => [
                'title' => 'Search',
                'description' => 'Choose your search mode: around your location or worldwide.',
            ],
            'explore' => [
                'title' => 'Explore',
                'description' => 'Browse results on an interactive map and discover places that interest you.',
            ],
            'discover' => [
                'title' => 'Discover',
                'description' => 'Consult detailed sheets with photos, historical information and practical advice.',
            ],
        ],
    ],

    'features' => [
        'title' => 'Two search modes',
        'subtitle' => 'Discover space locations according to your needs: around you or by global theme.',
        'modes' => [
            'proximity' => [
                'title' => 'Search "Around me"',
                'description' => 'You are somewhere and want to discover space locations within a defined radius? Use our geolocated search with an adjustable radius up to 1500 km.',
                'benefits' => [
                    'geolocation' => 'Automatic geolocation',
                    'custom_radius' => 'Adjustable radius up to 1500 km',
                ],
                'mockup' => [
                    'search_placeholder' => 'Paris, France',
                    'geolocation_button' => 'Locate me',
                    'radius_label' => 'Search radius',
                    'radius_display' => '200 km',
                    'results_count' => ':count places found within :radius km radius',
                ],
            ],
            'thematic' => [
                'title' => 'Thematic search',
                'description' => 'Explore all global locations linked to a specific theme: NASA, SpaceX, Apollo missions, observatories, space museums and much more.',
                'benefits' => [
                    'themes_available' => 'Many themes available',
                    'worldwide_coverage' => 'Global coverage',
                    'smart_clustering' => 'Smart clustering',
                ],
                'mockup' => [
                    'tag_selector_placeholder' => 'Select a theme',
                    'popular_tags' => 'Popular themes',
                    'tag_nasa' => 'NASA',
                    'tag_apollo' => 'Apollo',
                    'results_nasa' => ':count places found for "NASA"',
                    'results_apollo' => ':count places found for "Apollo"',
                    'results_count' => ':count places in :tag theme',
                ],
            ],
        ],
    ],

    'community_contribution' => [
        'badge' => 'Community',
        'title' => 'Join the space adventure',
        'subtitle' => 'Join our community of enthusiasts and help build a global collaborative space database.',
        'actions' => [
            'propose_places' => [
                'title' => 'Suggest new places',
                'description' => 'Know a remarkable space place that isn\'t listed yet? Share it with the community and enrich our global database.',
                'benefits' => [
                    'simple_form' => 'Easy addition via guided form',
                    'expert_validation' => 'Validation by our team of experts',
                    'automatic_publication' => 'Automatic publication once approved',
                ],
                'mockup' => [
                    'form_title' => 'New space place',
                    'status_pending' => 'Under validation',
                ],
            ],
            'improve_info' => [
                'title' => 'Improve information',
                'description' => 'Have additional information or spotted an error? Help us maintain the quality and accuracy of our database.',
                'benefits' => [
                    'error_reporting' => 'Simplified error reporting',
                    'additional_info' => 'Additional information addition',
                    'transparent_moderation' => 'Transparent moderation',
                ],
                'mockup' => [
                    'place_title' => 'Kennedy Space Center',
                    'field_coordinates' => 'GPS coordinates',
                    'field_practical_info' => 'Practical information',
                    'field_photos' => 'Photos',
                    'suggestion_title' => 'Suggested modification',
                    'suggestion_example' => '"The GPS coordinates seem incorrect..."',
                ],
            ],
        ],
    ],

    'community_stats' => [
        'title' => 'A passionate community',
        'subtitle' => 'Join explorers from around the world who share your passion for space',
        'stats' => [
            'places' => [
                'count' => '1,247',
                'label' => 'Places listed',
            ],
            'members' => [
                'count' => '12,847',
                'label' => 'Active members',
            ],
            'countries' => [
                'count' => '54',
                'label' => 'Countries covered',
            ],
            'monthly_submissions' => [
                'count' => '127',
                'label' => 'Submissions this month',
            ],
            'submissions' => [
                'label' => 'Place submissions',
            ],
        ],
    ],

    'featured_places' => [
        'badge' => 'Iconic places',
        'title' => 'Extraordinary destinations',
        'subtitle' => 'Discover some of the most fascinating places in our global collection',
        'no_places' => 'No iconic places available at the moment.',
        'places' => [
            'kennedy_space_center' => [
                'title' => 'Kennedy Space Center',
                'description' => 'Historic NASA launch center in Florida, birthplace of Apollo missions and space shuttles.',
                'location' => 'Florida, USA',
                'tag' => 'NASA',
            ],
            'baikonour_cosmodrome' => [
                'title' => 'Baikonur Cosmodrome',
                'description' => 'World\'s first cosmodrome, historic site of Gagarin\'s flight and current base for Soyuz missions.',
                'location' => 'Kazakhstan',
                'tag' => 'Roscosmos',
            ],
            'alma_observatory' => [
                'title' => 'ALMA Observatory',
                'description' => 'World\'s largest astronomical project, 66 antennas in the Atacama desert to probe the distant universe.',
                'location' => 'Chile',
                'tag' => 'Observatory',
            ],
        ],
        'cta' => 'See all iconic places',
    ],

    'latest_places' => [
        'badge' => 'Latest additions',
        'title' => 'Newly discovered places',
        'subtitle' => 'Discover the places recently added to our global collection',
        'no_places' => 'No recent places available at the moment.',
    ],

    'why_cosmap' => [
        'title' => 'Why choose '.config('app.name').' ?',
        'subtitle' => 'A collaborative platform dedicated to global mapping of space sites',
        'benefits' => [
            'collaborative_database' => [
                'title' => 'Collaborative database',
                'description' => 'Mapping continuously enriched by a community of enthusiasts, validated by our experts',
            ],
            'smart_search' => [
                'title' => 'Smart search',
                'description' => 'Easily find space sites near you or explore by theme worldwide',
            ],
            'engaged_community' => [
                'title' => 'Engaged community',
                'description' => 'Participate in a global network of explorers who enrich the database and share their passion',
            ],
            'guaranteed_quality' => [
                'title' => 'Guaranteed quality',
                'description' => 'Information verified and regularly updated thanks to rigorous and collaborative moderation',
            ],
        ],
    ],

    'cta' => [
        'title' => [
            'part1' => 'Your space journey',
            'part2' => 'starts here',
        ],
        'subtitle' => 'Explore iconic places of space exploration, from historic launch pads to cutting-edge observatories. The universe awaits you.',
        'buttons' => [
            'primary' => 'Start exploring',
            'secondary' => 'Suggest a place',
        ],
    ],

    // SEO integration for this page
    'seo' => [
        'title' => 'The spatial universe within reach',
        'description' => 'Discover iconic space exploration sites. From launch centers to museums, explore worldwide space history on '.config('app.name').' .',
        'keywords' => 'space, spatial, space exploration, NASA, SpaceX, space sites, launch centers, space museums, astronomy',

        // Open Graph specific to this page
        'og' => [
            'type' => 'website',
            'image_alt' => config('app.name').' - Discover the spatial universe',
        ],

        // Twitter Cards specific to this page
        'twitter' => [
            'card' => 'summary_large_image',
        ],

        // JSON-LD specific to this page
        'json_ld' => [
            'website' => [
                'description' => 'Discover the spatial universe within reach',
                'search_action' => [
                    '@type' => 'SearchAction',
                    'target' => [
                        '@type' => 'EntryPoint',
                        'urlTemplate' => 'https://explo.space/explorer?q={search_term_string}',
                    ],
                    'query-input' => 'required name=search_term_string',
                ],
            ],
            'organization' => [
                'name' => config('app.name'),
                'legal_name' => config('app.name'),
                'description' => 'Collaborative platform for discovering iconic sites of space exploration and universe discovery.',
                'founding_date' => config('company.founding_date'),
                'founders' => [
                    [
                        '@type' => 'Person',
                        'name' => config('company.owner'),
                    ],
                ],
                'contact_point' => [
                    '@type' => 'ContactPoint',
                    'contactType' => 'Customer Support',
                    'availableLanguage' => ['French', 'English'],
                ],
                'same_as' => [
                    // Social media URLs will be added here
                ],
            ],
        ],
    ],
];
