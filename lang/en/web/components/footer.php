<?php

return [
    'sections' => [
        'about' => [
            'title' => 'About',
            'links' => [
                'mission' => 'Our mission',
                'how_it_works' => 'How it works',
                'team' => 'Team',
                'contact' => 'Contact',
            ],
        ],
        'explore' => [
            'title' => 'Explore',
            'links' => [
                'search' => 'Search a place',
                'featured' => 'Featured places',
                'latest' => 'Latest additions',
                'all_tags' => 'All tags',
            ],
        ],
        'community' => [
            'title' => 'Community',
            'links' => [
                'suggest_place' => 'Suggest a place',
                'report_error' => 'Report an error',
                'contributors' => 'Contributors',
                'guidelines' => 'Guidelines',
            ],
        ],
        'support' => [
            'title' => 'Support',
            'links' => [
                'help_center' => 'Help center',
                'faq' => 'FAQ',
                'contact_us' => 'Contact us',
                'report_bug' => 'Report a bug',
            ],
        ],
    ],
    'brand' => [
        'name' => config('app.name'),
        'tagline' => 'Discover space universe',
    ],
    'legal' => [
        'terms' => 'Legal notice',
        'conditions' => 'Terms of use',
        'privacy' => 'Privacy',
        'cookies' => 'Cookies',
    ],
    'copyright' => [
        'text' => 'All rights reserved.',
        'tagline' => 'Created with passion for universe explorers.',
    ],
];
