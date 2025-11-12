<?php

return [
    'sections' => [
        'about' => [
            'title' => 'À propos',
            'links' => [
                'mission' => 'Notre mission',
                'how_it_works' => 'Comment ça marche',
                'contribute' => 'Contribuer',
                'philosophy' => 'Notre philosophie',
            ],
        ],
        'explore' => [
            'title' => 'Explorer',
            'links' => [
                'around_me' => 'Autour de moi',
                'worldwide' => 'Monde entier',
                'featured' => 'Lieux emblématiques',
            ],
        ],
        'community' => [
            'title' => 'Communauté',
            'links' => [
                'suggest_place' => 'Proposer un lieu',
                'report_error' => 'Signaler une erreur',
                'contributors' => 'Contributeurs',
                'guidelines' => 'Guidelines',
            ],
        ],
        'support' => [
            'title' => 'Support',
            'links' => [
                'help_center' => 'Centre d\'aide',
                'faq' => 'FAQ',
                'contact_us' => 'Nous contacter',
                'report_bug' => 'Signaler un bug',
            ],
        ],
    ],
    'brand' => [
        'name' => config('app.name'),
        'tagline' => 'Découvrez l\'univers spatial',
    ],
    'legal' => [
        'legal_notice' => 'Mentions légales',
        'privacy' => 'Politique de confidentialité',
    ],
    'copyright' => [
        'text' => 'Tous droits réservés.',
        'tagline' => 'Créé avec passion pour les explorateurs de l\'univers.',
    ],
];
