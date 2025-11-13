<?php

return [
    'hero' => [
        'title' => [
            'part1' => 'L\'univers spatial',
            'part2' => 'à portée de main',
        ],
        'subtitle' => [
            'part1' => 'Découvrez les lieux emblématiques',
            'part2' => 'de la conquête spatiale.',
            'part3' => 'Des centres de lancement aux musées, explorez l\'histoire spatiale mondiale.',
        ],
        'cta' => [
            'primary' => 'Explorer maintenant',
            'secondary' => 'Proposer un lieu',
        ],
        'stats' => [
            'places' => [
                'label' => 'Lieux référencés',
            ],
            'featured' => [
                'label' => 'Lieux emblématiques',
            ],
            'themes' => [
                'label' => 'Thématiques',
            ],
        ],
    ],

    'how_it_works' => [
        'title' => 'Comment ça marche ?',
        'subtitle' => 'Découvrir les lieux spatiaux n\'a jamais été aussi simple',
        'steps' => [
            'search' => [
                'title' => 'Recherchez',
                'description' => 'Choisissez votre mode de recherche : autour de votre position ou dans le monde entier.',
            ],
            'explore' => [
                'title' => 'Explorez',
                'description' => 'Parcourez les résultats sur une carte interactive et découvrez les lieux qui vous intéressent.',
            ],
            'discover' => [
                'title' => 'Découvrez',
                'description' => 'Consultez les fiches détaillées avec photos, informations historiques et conseils pratiques.',
            ],
        ],
    ],

    'features' => [
        'title' => 'Deux modes de recherche',
        'subtitle' => 'Découvrez les lieux spatiaux selon vos besoins : autour de vous ou par thématique mondiale.',
        'modes' => [
            'proximity' => [
                'title' => 'Recherche "Autour de moi"',
                'description' => 'Vous êtes quelque part et souhaitez découvrir les lieux spatiaux dans un rayon défini ? Utilisez notre recherche géolocalisée avec un rayon ajustable jusqu\'à 1500 km.',
                'benefits' => [
                    'geolocation' => 'Géolocalisation automatique',
                    'custom_radius' => 'Rayon ajustable jusqu\'à 1500 km',
                ],
                'mockup' => [
                    'search_placeholder' => 'Paris, France',
                    'geolocation_button' => 'Me géolocaliser',
                    'radius_label' => 'Rayon de recherche',
                    'radius_display' => '200 km',
                    'results_count' => ':count lieux trouvés dans un rayon de :radius km',
                ],
            ],
            'thematic' => [
                'title' => 'Recherche thématique',
                'description' => 'Explorez tous les lieux mondiaux liés à une thématique précise : NASA, SpaceX, missions Apollo, observatoires, musées spatiaux et bien plus.',
                'benefits' => [
                    'themes_available' => 'De nombreuses thématiques disponibles',
                    'worldwide_coverage' => 'Couverture mondiale',
                    'smart_clustering' => 'Clustering intelligent',
                ],
                'mockup' => [
                    'tag_selector_placeholder' => 'Sélectionner une thématique',
                    'popular_tags' => 'Thématiques populaires',
                    'tag_nasa' => 'NASA',
                    'tag_apollo' => 'Apollo',
                    'results_nasa' => ':count lieux trouvés pour "NASA"',
                    'results_apollo' => ':count lieux trouvés pour "Apollo"',
                    'results_count' => ':count lieux dans la thématique :tag',
                ],
            ],
        ],
    ],

    'community_contribution' => [
        'badge' => 'Communauté',
        'title' => 'Participez à l\'aventure spatiale',
        'subtitle' => 'Rejoignez notre communauté de passionnés et contribuez à enrichir une base de données spatiale collaborative mondiale.',
        'actions' => [
            'propose_places' => [
                'title' => 'Proposez de nouveaux lieux',
                'description' => 'Vous connaissez un lieu spatial remarquable qui n\'est pas encore référencé ? Partagez-le avec la communauté et enrichissez notre base de données mondiale.',
                'benefits' => [
                    'simple_form' => 'Ajout simple via formulaire guidé',
                    'expert_validation' => 'Validation par notre équipe d\'experts',
                    'automatic_publication' => 'Publication automatique une fois approuvé',
                ],
                'mockup' => [
                    'form_title' => 'Nouveau lieu spatial',
                    'status_pending' => 'En cours de validation',
                ],
            ],
            'improve_info' => [
                'title' => 'Améliorez les informations',
                'description' => 'Vous avez des informations complémentaires ou avez repéré une erreur ? Aidez-nous à maintenir la qualité et la précision de notre base de données.',
                'benefits' => [
                    'error_reporting' => 'Signalement d\'erreurs simplifié',
                    'additional_info' => 'Ajout d\'informations complémentaires',
                    'transparent_moderation' => 'Modération transparente',
                ],
                'mockup' => [
                    'place_title' => 'Centre spatial Kennedy',
                    'field_coordinates' => 'Coordonnées GPS',
                    'field_practical_info' => 'Informations pratiques',
                    'field_photos' => 'Photos',
                    'suggestion_title' => 'Modification suggérée',
                    'suggestion_example' => '"Les coordonnées GPS semblent incorrectes..."',
                ],
            ],
        ],
    ],

    'community_stats' => [
        'title' => 'Une communauté passionnée',
        'subtitle' => 'Rejoignez les explorateurs du monde entier qui partagent votre passion pour l\'espace',
        'stats' => [
            'places' => [
                'count' => '1,247',
                'label' => 'Lieux référencés',
            ],
            'members' => [
                'count' => '12,847',
                'label' => 'Membres actifs',
            ],
            'countries' => [
                'count' => '54',
                'label' => 'Pays couverts',
            ],
            'monthly_submissions' => [
                'count' => '127',
                'label' => 'Propositions ce mois',
            ],
            'submissions' => [
                'label' => 'Propositions de lieux',
            ],
        ],
    ],

    'featured_places' => [
        'badge' => 'Lieux emblématiques',
        'title' => 'Des destinations extraordinaires',
        'subtitle' => 'Découvrez quelques-uns des lieux les plus fascinants de notre collection mondiale',
        'no_places' => 'Aucun lieu emblématique disponible pour le moment.',
        'cta' => 'Voir tous les lieux emblématiques',
    ],

    'why_cosmap' => [
        'title' => 'Pourquoi choisir '.config('app.name').' ?',
        'subtitle' => 'Une plateforme collaborative dédiée à la cartographie mondiale des sites spatiaux',
        'benefits' => [
            'collaborative_database' => [
                'title' => 'Base de données collaborative',
                'description' => 'Une cartographie enrichie continuellement par une communauté de passionnés, validée par nos experts',
            ],
            'smart_search' => [
                'title' => 'Recherche intelligente',
                'description' => 'Trouvez facilement les sites spatiaux près de chez vous ou explorez par thématique dans le monde entier',
            ],
            'engaged_community' => [
                'title' => 'Communauté engagée',
                'description' => 'Participez à un réseau mondial d\'explorateurs qui enrichissent la base de données et partagent leur passion',
            ],
            'guaranteed_quality' => [
                'title' => 'Qualité garantie',
                'description' => 'Informations vérifiées et mises à jour régulièrement grâce à une modération rigoureuse et collaborative',
            ],
        ],
    ],

    'cta' => [
        'title' => [
            'part1' => 'Votre voyage spatial',
            'part2' => 'commence ici',
        ],
        'subtitle' => 'Explorez les lieux emblématiques de la conquête spatiale, depuis les rampes de lancement historiques jusqu\'aux observatoires de pointe. L\'univers vous attend.',
        'buttons' => [
            'primary' => 'Démarrer l\'exploration',
            'secondary' => 'Proposer un lieu',
        ],
    ],

    // SEO intégré pour cette page
    'seo' => [
        'title' => 'L\'univers spatial à portée de main',
        'description' => 'Découvrez les lieux emblématiques de la conquête spatiale. Des centres de lancement aux musées, explorez l\'histoire spatiale mondiale sur '.config('app.name').'.',
        'keywords' => 'espace, spatial, conquête spatiale, NASA, SpaceX, lieux spatiaux, centres de lancement, musées espace, astronomie',

        // Open Graph spécifique à cette page
        'og' => [
            'type' => 'website',
            'image_alt' => config('app.name').' - Découvrez l\'univers spatial',
        ],

        // Twitter Cards spécifique à cette page
        'twitter' => [
            'card' => 'summary_large_image',
        ],

        // JSON-LD spécifique à cette page
        'json_ld' => [
            'website' => [
                'description' => 'Découvrez l\'univers spatial à portée de main',
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
                'description' => 'Plateforme collaborative de découverte des lieux emblématiques de la conquête spatiale et de l\'exploration de l\'univers.',
                'founding_date' => config('company.founding_date'),
                'founders' => [
                    [
                        '@type' => 'Person',
                        'name' => config('company.owner'),
                    ],
                ],
                'contact_point' => [
                    '@type' => 'ContactPoint',
                    'contactType' => 'Support client',
                    'availableLanguage' => ['French', 'English'],
                ],
                'same_as' => [
                    // URLs des réseaux sociaux seront ajoutées ici
                ],
            ],
        ],
    ],
];
