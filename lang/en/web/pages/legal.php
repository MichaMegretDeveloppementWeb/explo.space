<?php

return [
    'breadcrumb' => [
        'current' => 'Legal Notice',
    ],

    'hero' => [
        'badge' => 'Legal Notice',
        'title' => [
            'part1' => 'Legal',
            'part2' => 'Notice',
        ],
        'subtitle' => 'Legal and editorial information for the '.config('app.name').' website.',
    ],

    'sections' => [
        'editor' => [
            'title' => 'Website Publisher',
            'content' => [
                'intro' => 'The '.config('app.name').' website is published by:',
                'name' => '<strong>'.config('company.owner').'</strong>',
                'status' => 'Individual',
                'email' => 'Email: <a href="mailto:'.config('mail.from.address').'" class="text-blue-600 hover:underline">'.config('mail.from.address').'</a>',
            ],
        ],

        'publication' => [
            'title' => 'Publication Director',
            'content' => 'The publication director of the website is <strong>'.config('company.owner').'</strong>.',
        ],

        'developer' => [
            'title' => 'Development',
            'content' => [
                'intro' => 'The website was developed by:',
                'name' => '<strong>Micha Megret</strong> - Freelance web developer',
                'address' => 'Address: 261 rue des Tattes, 74500 Publier, France',
                'website' => 'Website: <a href="https://micha-megret.fr" target="_blank" rel="noopener noreferrer" class="text-blue-600 hover:underline">www.micha-megret.fr</a>',
            ],
        ],

        'hosting' => [
            'title' => 'Hosting',
            'content' => [
                'intro' => 'The website is hosted by:',
                'name' => '<strong>Hostinger International Ltd.</strong>',
                'address' => '61 Lordou Vironos Street, 6023 Larnaca, Cyprus',
                'website' => 'Website: <a href="https://www.hostinger.com" target="_blank" rel="noopener noreferrer" class="text-blue-600 hover:underline">www.hostinger.com</a>',
            ],
        ],

        'intellectual_property' => [
            'title' => 'Intellectual Property',
            'content' => [
                'intro' => 'All content on this website (texts, images, videos, design, etc.) is the exclusive property of '.config('company.owner').' or its partners, unless otherwise stated.',
                'rights' => 'Any reproduction, distribution, modification, adaptation, retransmission or publication of these elements is strictly prohibited without the express written consent of '.config('company.owner').'.',
                'exception' => 'Photographs and visual content of space locations may be subject to copyright by their respective creators. Their use on this site is for informational and educational purposes.',
            ],
        ],

        'personal_data' => [
            'title' => 'Personal Data and GDPR',
            'content' => [
                'responsible' => 'The data controller for personal data collected on '.config('app.name').' is '.config('company.owner').'.',
                'principles' => 'In accordance with the General Data Protection Regulation (GDPR), you have the right to access, rectify, delete, and port your personal data.',
                'collected_data' => [
                    'title' => 'Collected data:',
                    'items' => [
                        'Contact email during place suggestions or reports',
                        'Navigation information (technical cookies)',
                        'IP address for rate limiting (anti-spam protection)',
                    ],
                ],
                'usage' => 'This data is used only to:',
                'usage_items' => [
                    'Ensure the service operates',
                    'Respond to your requests',
                    'Improve our services',
                ],
                'retention' => 'Your data is retained for the period necessary for the purposes for which it was collected, then deleted.',
                'rights' => 'To exercise your rights, contact us at: <a href="mailto:'.config('mail.destination_mail_contact').'" class="text-blue-600 hover:underline">'.config('mail.destination_mail_contact').'</a>',
                'cnil' => 'You also have the right to lodge a complaint with your national data protection authority.',
            ],
        ],

        'cookies' => [
            'title' => 'Cookies',
            'content' => [
                'intro' => 'The website uses technical cookies essential to the service:',
                'items' => [
                    'Laravel session cookie (CSRF security)',
                    'Language preference cookie',
                    'Google reCAPTCHA (anti-spam protection)',
                ],
                'acceptance' => 'By using this site, you accept the use of these necessary cookies.',
                'management' => 'You can manage your cookie preferences in your browser settings.',
            ],
        ],

        'liability' => [
            'title' => 'Liability',
            'content' => [
                'accuracy' => config('company.owner').' strives to ensure the accuracy and updating of information published on this site, and reserves the right to correct the content at any time without notice.',
                'disclaimer' => 'However, '.config('company.owner').' cannot guarantee the accuracy, precision, or completeness of information made available on this site.',
                'external_links' => 'Hyperlinks to other websites do not engage the liability of '.config('company.owner').'.',
            ],
        ],

        'applicable_law' => [
            'title' => 'Applicable Law',
            'content' => 'These legal notices are governed by French law. Any dispute relating to the use of the '.config('app.name').' website is subject to the exclusive jurisdiction of French courts.',
        ],

        'contact' => [
            'title' => 'Contact',
            'content' => [
                'intro' => 'For any questions regarding these legal notices, you can contact us:',
                'email' => 'By email: <a href="mailto:'.config('mail.destination_mail_contact').'" class="text-blue-600 hover:underline">'.config('mail.destination_mail_contact').'</a>',
                'form' => 'Via our <a href="'.localRoute('contact').'" class="text-blue-600 hover:underline">contact form</a>',
            ],
        ],
    ],

    'last_updated' => 'Last updated',

    // SEO intégré pour cette page
    'seo' => [
        'title' => 'Legal Notice',
        'description' => 'Legal notice, editorial information and GDPR for '.config('app.name').'. Information about the publisher, hosting provider and your rights.',
        'keywords' => 'legal notice, legal information, GDPR, personal data, publisher, hosting, '.config('app.name'),

        // Open Graph spécifique à cette page
        'og' => [
            'type' => 'website',
            'image_alt' => config('app.name').' - Legal Notice',
        ],

        // Twitter Cards spécifique à cette page
        'twitter' => [
            'card' => 'summary',
        ],
    ],
];
