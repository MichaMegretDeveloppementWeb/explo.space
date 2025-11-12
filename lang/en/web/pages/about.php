<?php

return [
    'breadcrumb' => [
        'current' => 'About',
    ],

    'hero' => [
        'badge' => 'About',
        'title' => [
            'part1' => 'Discover the space universe',
            'part2' => 'together',
        ],
        'subtitle' => 'A collaborative platform dedicated to the global mapping of iconic places related to space exploration and the discovery of the universe.',
    ],

    'mission' => [
        'title' => 'Our mission',
        'content' => [
            'intro' => config('app.name').' was born from a passion for space exploration and a simple observation: places related to space conquest are scattered around the world, but no platform brings them together in a comprehensive and accessible way.',
            'goal' => 'Our mission is to create the largest collaborative global database listing all iconic places of space adventure: from historic launch centers to cutting-edge observatories, from space-dedicated museums to landing sites of emblematic missions.',
            'vision' => 'We want to enable every space enthusiast, whether a simple curious person or an astronomy expert, to easily discover and explore these fascinating places that have marked humanity\'s history in space.',
        ],
    ],

    'how_it_works' => [
        'title' => 'How it works',
        'subtitle' => 'A simple, functional platform accessible to everyone',
        'steps' => [
            'search' => [
                'title' => 'Two search modes',
                'description' => 'Search for space places around your location with an adjustable radius up to 1500 km, or explore the entire world by filtering by theme (NASA, SpaceX, Apollo missions, observatories, etc.).',
                'features' => [
                    'proximity' => '"Around me" mode with geolocation',
                    'worldwide' => '"Worldwide" mode with thematic filters',
                    'map' => 'Interactive map with intelligent clustering',
                ],
            ],
            'discover' => [
                'title' => 'Discover places',
                'description' => 'Each place has a complete profile with detailed description, precise GPS coordinates, practical information, and photo gallery to immerse you in space history.',
                'features' => [
                    'complete_info' => 'Detailed profiles with history',
                    'photos' => 'Optimized photo galleries',
                    'practical' => 'Practical information (access, hours)',
                ],
            ],
            'contribute' => [
                'title' => 'Join the adventure',
                'description' => 'Our platform is collaborative: propose new places, report errors, suggest improvements. Each contribution is validated by our team before publication to ensure quality.',
                'features' => [
                    'propose' => 'Easily propose new places',
                    'improve' => 'Report errors or add information',
                    'moderation' => 'Rigorous moderation to ensure quality',
                ],
            ],
        ],
    ],

    'contribute' => [
        'title' => 'Contribute to '.config('app.name'),
        'subtitle' => 'Join our community of enthusiasts and enrich the global database',
        'why_contribute' => [
            'title' => 'Why contribute?',
            'reasons' => [
                'share_passion' => [
                    'title' => 'Share your passion',
                    'description' => 'Let others discover the space places you know and love.',
                ],
                'enrich_database' => [
                    'title' => 'Enrich the database',
                    'description' => 'Each place added or information completed helps thousands of explorers.',
                ],
                'quality' => [
                    'title' => 'Ensure quality',
                    'description' => 'Your error reports help maintain reliable information.',
                ],
            ],
        ],
        'how_to_contribute' => [
            'title' => 'How to contribute?',
            'steps' => [
                'propose' => [
                    'title' => '1. Propose a new place',
                    'description' => 'Use our guided form to submit a space place not yet listed. Provide GPS coordinates, add a description and photos.',
                ],
                'validation' => [
                    'title' => '2. Validation by our team',
                    'description' => 'Our moderation team verifies the information, completes it if necessary, and assigns appropriate themes.',
                ],
                'publication' => [
                    'title' => '3. Automatic publication',
                    'description' => 'Once validated, your place is automatically published and visible to everyone. You receive an email notification.',
                ],
            ],
        ],
        'cta' => 'Propose a place now',
    ],

    'philosophy' => [
        'title' => 'Our philosophy',
        'subtitle' => 'Strong values serving the community',
        'values' => [
            'functional' => [
                'title' => 'Functional first',
                'description' => 'Our clean design prioritizes functionality and ergonomics. Each interface element has a specific purpose: helping you discover and explore space places easily.',
            ],
            'free' => [
                'title' => 'Free and accessible',
                'description' => 'No user account required to explore places. Space knowledge should be accessible to everyone, without barriers or costs.',
            ],
            'collaborative' => [
                'title' => 'Collaborative and open',
                'description' => config('app.name').'\'s richness comes from its community. Every visitor can propose places and report errors, creating a living and constantly enriched database.',
            ],
            'quality' => [
                'title' => 'Guaranteed quality',
                'description' => 'All contributions are verified by our team before publication. We guarantee reliable, accurate, and regularly updated information.',
            ],
            'privacy' => [
                'title' => 'Privacy respect',
                'description' => 'We use privacy-respecting technologies (OpenStreetMap, no invasive tracking). Your personal data is never sold or shared.',
            ],
            'sustainable' => [
                'title' => 'Sustainable and scalable',
                'description' => 'Technical architecture designed to last and evolve with community needs. We build a lasting platform for future generations of space enthusiasts.',
            ],
        ],
    ],

    // SEO intégré pour cette page
    'seo' => [
        'title' => 'About - Our mission and vision',
        'description' => 'Discover '.config('app.name').', the collaborative platform dedicated to the global mapping of iconic space exploration places. Our mission, how we work, and our values.',
        'keywords' => 'about, mission, vision, collaborative platform, space mapping, space places, space conquest, universe exploration',

        // Open Graph spécifique à cette page
        'og' => [
            'type' => 'website',
            'image_alt' => config('app.name').' - About our mission',
        ],

        // Twitter Cards spécifique à cette page
        'twitter' => [
            'card' => 'summary_large_image',
        ],
    ],
];
