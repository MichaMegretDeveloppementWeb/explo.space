<?php

return [
    'sections' => [
        'about' => [
            'title' => 'À propos',
            'links' => [
                'mission' => 'Notre mission',
                'how_it_works' => 'Comment ça marche',
                'team' => 'Équipe',
                'contact' => 'Contact',
            ],
        ],
        'explore' => [
            'title' => 'Explorer',
            'links' => [
                'search' => 'Rechercher un lieu',
                'featured' => 'Lieux à la une',
                'latest' => 'Derniers ajouts',
                'all_tags' => 'Tous les tags',
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
        'terms' => 'Mentions légales',
        'conditions' => 'CGU',
        'privacy' => 'Confidentialité',
        'cookies' => 'Cookies',
    ],
    'copyright' => [
        'text' => 'Tous droits réservés.',
        'tagline' => 'Créé avec passion pour les explorateurs de l\'univers.',
    ],
];
