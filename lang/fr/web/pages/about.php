<?php

return [
    'breadcrumb' => [
        'current' => 'À propos',
    ],

    'hero' => [
        'badge' => 'À propos',
        'title' => [
            'part1' => 'Découvrir l\'univers spatial',
            'part2' => 'ensemble',
        ],
        'subtitle' => 'Une plateforme collaborative dédiée à la cartographie mondiale des lieux emblématiques de la conquête spatiale et de l\'exploration de l\'univers.',
    ],

    'mission' => [
        'title' => 'Notre mission',
        'content' => [
            'intro' => config('app.name').' est né d\'une passion pour l\'exploration spatiale et d\'un constat simple : les lieux liés à la conquête spatiale sont éparpillés dans le monde entier, mais aucune plateforme ne les rassemble de manière exhaustive et accessible.',
            'goal' => 'Notre mission est de créer une base de données mondiale collaborative recensant tous les lieux emblématiques de l\'aventure spatiale : des centres de lancement historiques aux observatoires de pointe, des musées dédiés à l\'espace aux sites d\'atterrissage de missions emblématiques.',
            'vision' => 'Nous voulons permettre à chaque passionné d\'espace, qu\'il soit simple curieux ou expert en astronomie, de découvrir et d\'explorer facilement ces lieux fascinants qui ont marqué l\'histoire de l\'humanité dans l\'espace.',
        ],
    ],

    'how_it_works' => [
        'title' => 'Comment ça marche',
        'subtitle' => 'Une plateforme simple, fonctionnelle et accessible à tous',
        'steps' => [
            'search' => [
                'title' => 'Deux modes de recherche',
                'description' => 'Recherchez les lieux spatiaux autour de votre position avec un rayon ajustable jusqu\'à 1500 km, ou explorez le monde entier en filtrant par thématique (NASA, SpaceX, missions Apollo, observatoires, etc.).',
                'features' => [
                    'proximity' => 'Mode "Autour de moi" avec géolocalisation',
                    'worldwide' => 'Mode "Monde entier" avec filtres thématiques',
                    'map' => 'Carte interactive avec clustering intelligent',
                ],
            ],
            'discover' => [
                'title' => 'Découvrez les lieux',
                'description' => 'Chaque lieu dispose d\'une fiche complète avec description détaillée, coordonnées GPS précises, informations pratiques et galerie de photos pour vous plonger dans l\'histoire spatiale.',
                'features' => [
                    'complete_info' => 'Fiches détaillées avec historique',
                    'photos' => 'Galeries photos optimisées',
                    'practical' => 'Informations pratiques (accès, horaires)',
                ],
            ],
            'contribute' => [
                'title' => 'Participez à l\'aventure',
                'description' => 'Notre plateforme est collaborative : proposez de nouveaux lieux, signalez des erreurs, suggérez des améliorations. Chaque contribution est validée par notre équipe avant publication pour garantir la qualité.',
                'features' => [
                    'propose' => 'Proposez de nouveaux lieux facilement',
                    'improve' => 'Signalez des erreurs ou ajoutez des informations',
                    'moderation' => 'Modération rigoureuse pour assurer la qualité',
                ],
            ],
        ],
    ],

    'contribute' => [
        'title' => 'Contribuer à '.config('app.name'),
        'subtitle' => 'Rejoignez notre communauté de passionnés et enrichissez la base de données mondiale',
        'why_contribute' => [
            'title' => 'Pourquoi contribuer ?',
            'reasons' => [
                'share_passion' => [
                    'title' => 'Partagez votre passion',
                    'description' => 'Faites découvrir aux autres les lieux spatiaux que vous connaissez et aimez.',
                ],
                'enrich_database' => [
                    'title' => 'Enrichissez la base de données',
                    'description' => 'Chaque lieu ajouté ou information complétée aide les explorateurs du monde entier.',
                ],
                'quality' => [
                    'title' => 'Garantissez la qualité',
                    'description' => 'Vos signalements d\'erreurs permettent de maintenir des informations fiables.',
                ],
            ],
        ],
        'how_to_contribute' => [
            'title' => 'Comment contribuer ?',
            'steps' => [
                'propose' => [
                    'title' => '1. Proposez un nouveau lieu',
                    'description' => 'Utilisez notre formulaire guidé pour soumettre un lieu spatial non encore référencé. Indiquez les coordonnées GPS, ajoutez une description et des photos.',
                ],
                'validation' => [
                    'title' => '2. Validation par notre équipe',
                    'description' => 'Notre équipe de modérateurs vérifie les informations, complète si nécessaire, et assigne les thématiques appropriées.',
                ],
                'publication' => [
                    'title' => '3. Publication automatique',
                    'description' => 'Une fois validé, votre lieu est automatiquement publié et visible par tous. Vous recevez une notification par email.',
                ],
            ],
        ],
        'cta' => 'Proposer un lieu maintenant',
    ],

    'philosophy' => [
        'title' => 'Notre philosophie',
        'subtitle' => 'Des valeurs fortes au service de la communauté',
        'values' => [
            'functional' => [
                'title' => 'Fonctionnel avant tout',
                'description' => 'Notre design épuré privilégie la fonctionnalité et l\'ergonomie. Chaque élément de l\'interface a un but précis : vous aider à découvrir et explorer les lieux spatiaux facilement.',
            ],
            'free' => [
                'title' => 'Gratuit et accessible',
                'description' => 'Aucun compte utilisateur requis pour explorer les lieux. La connaissance spatiale doit être accessible à tous, sans barrière ni coût.',
            ],
            'collaborative' => [
                'title' => 'Collaboratif et ouvert',
                'description' => 'La richesse de '.config('app.name').' vient de sa communauté. Chaque visiteur peut proposer des lieux et signaler des erreurs, créant ainsi une base de données vivante et constamment enrichie.',
            ],
            'quality' => [
                'title' => 'Qualité garantie',
                'description' => 'Toutes les contributions sont vérifiées par notre équipe avant publication. Nous garantissons des informations fiables, précises et régulièrement mises à jour.',
            ],
            'privacy' => [
                'title' => 'Respect de la vie privée',
                'description' => 'Nous utilisons des technologies respectueuses de la vie privée (OpenStreetMap, pas de tracking invasif). Vos données personnelles ne sont jamais vendues ni partagées.',
            ],
            'sustainable' => [
                'title' => 'Durable et évolutif',
                'description' => 'Architecture technique pensée pour durer et évoluer avec les besoins de la communauté. Nous construisons une plateforme pérenne pour les générations futures de passionnés d\'espace.',
            ],
        ],
    ],

    // SEO intégré pour cette page
    'seo' => [
        'title' => 'À propos - Notre mission et notre vision',
        'description' => 'Découvrez '.config('app.name').', la plateforme collaborative dédiée à la cartographie mondiale des lieux emblématiques de la conquête spatiale. Notre mission, notre fonctionnement et nos valeurs.',
        'keywords' => 'à propos, mission, vision, plateforme collaborative, cartographie spatiale, lieux spatiaux, conquête spatiale, exploration univers',

        // Open Graph spécifique à cette page
        'og' => [
            'type' => 'website',
            'image_alt' => config('app.name').' - À propos de notre mission',
        ],

        // Twitter Cards spécifique à cette page
        'twitter' => [
            'card' => 'summary_large_image',
        ],
    ],
];
