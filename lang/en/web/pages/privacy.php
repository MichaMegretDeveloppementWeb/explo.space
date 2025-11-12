<?php

return [
    'breadcrumb' => [
        'current' => 'Privacy Policy',
    ],

    'hero' => [
        'badge' => 'Privacy',
        'title' => [
            'part1' => 'Privacy',
            'part2' => 'Policy',
        ],
        'subtitle' => 'Protection and management of your personal data on '.config('app.name').'.',
    ],

    'sections' => [
        'intro' => [
            'title' => 'Introduction',
            'content' => [
                'text1' => 'At '.config('app.name').', we take the protection of your personal data very seriously. This privacy policy informs you about how we collect, use, store, and protect your information.',
                'text2' => 'By using our website, you accept the practices described in this privacy policy.',
            ],
        ],

        'responsible' => [
            'title' => 'Data Controller',
            'content' => [
                'text' => 'The data controller is:',
                'name' => '<strong>'.config('company.owner').'</strong>',
                'email' => 'Email: <a href="mailto:'.config('mail.destination_mail_contact').'" class="text-blue-600 hover:underline">'.config('mail.destination_mail_contact').'</a>',
            ],
        ],

        'data_collected' => [
            'title' => 'Data Collected',
            'content' => [
                'intro' => 'We only collect data strictly necessary for the service:',
                'categories' => [
                    [
                        'title' => 'Contact Data',
                        'items' => [
                            'Email address (when proposing places or reporting errors)',
                        ],
                    ],
                    [
                        'title' => 'Technical Data',
                        'items' => [
                            'IP address (for anti-spam protection and rate limiting)',
                            'Technical cookies (session, preferences)',
                            'Navigation information (pages visited, duration)',
                        ],
                    ],
                    [
                        'title' => 'Content Data',
                        'items' => [
                            'Information about proposed places (title, description, coordinates)',
                            'Uploaded photos',
                            'Reports and proposed corrections',
                        ],
                    ],
                ],
            ],
        ],

        'data_usage' => [
            'title' => 'Data Usage',
            'content' => [
                'intro' => 'Your data is used exclusively for the following purposes:',
                'items' => [
                    'Process your place proposals and reports',
                    'Respond and inform you about the processing of your requests',
                    'Ensure proper functioning and security of the website',
                    'Prevent abuse and spam (reCAPTCHA, rate limiting)',
                    'Improve our services and user experience',
                    'Comply with our legal obligations',
                ],
            ],
        ],

        'legal_basis' => [
            'title' => 'Legal Basis',
            'content' => [
                'intro' => 'The processing of your data is based on the following legal grounds:',
                'items' => [
                    '<strong>Consent</strong>: for the use of non-essential cookies',
                    '<strong>Service execution</strong>: for processing your proposals',
                    '<strong>Legitimate interest</strong>: for security and website improvement',
                    '<strong>Legal obligation</strong>: for the retention of certain data',
                ],
            ],
        ],

        'data_retention' => [
            'title' => 'Retention Period',
            'content' => [
                'intro' => 'We retain your data for the following periods:',
                'items' => [
                    '<strong>Contact emails</strong>: 3 years after processing your request',
                    '<strong>Connection logs</strong>: 12 months',
                    '<strong>Session cookies</strong>: session duration',
                    '<strong>Preference cookies</strong>: 1 year',
                    '<strong>Published content</strong>: as long as the place is active on the site',
                ],
            ],
        ],

        'data_sharing' => [
            'title' => 'Data Sharing',
            'content' => [
                'intro' => 'We do not sell or rent your personal data. Your data may be shared only in the following cases:',
                'items' => [
                    '<strong>Hosting provider</strong>: Hostinger International Ltd. (data storage)',
                    '<strong>Anti-spam service</strong>: Google reCAPTCHA (form validation)',
                    '<strong>Legal obligation</strong>: upon request from competent authorities',
                ],
                'text' => 'These service providers are subject to confidentiality obligations and may only use your data within the scope of their services.',
            ],
        ],

        'your_rights' => [
            'title' => 'Your Rights',
            'content' => [
                'intro' => 'In accordance with GDPR, you have the following rights:',
                'items' => [
                    '<strong>Right of access</strong>: obtain a copy of your data',
                    '<strong>Right to rectification</strong>: correct inaccurate data',
                    '<strong>Right to erasure</strong>: request deletion of your data',
                    '<strong>Right to restriction</strong>: limit the processing of your data',
                    '<strong>Right to portability</strong>: retrieve your data in a structured format',
                    '<strong>Right to object</strong>: object to the processing of your data',
                    '<strong>Right to withdraw consent</strong>: at any time',
                ],
                'how_to' => 'To exercise your rights, contact us at: <a href="mailto:'.config('mail.destination_mail_contact').'" class="text-blue-600 hover:underline">'.config('mail.destination_mail_contact').'</a>',
                'deadline' => 'We undertake to respond within a maximum of 1 month.',
            ],
        ],

        'security' => [
            'title' => 'Data Security',
            'content' => [
                'intro' => 'We implement appropriate security measures to protect your data:',
                'items' => [
                    'HTTPS encryption for all communications',
                    'Password protection for administrator access',
                    'Regular data backups',
                    'Anti-spam protection and rate limiting',
                    'Regular system updates',
                ],
            ],
        ],

        'cookies' => [
            'title' => 'Cookies and Trackers',
            'content' => [
                'intro' => 'We use the following cookies:',
                'essential' => [
                    'title' => 'Essential cookies (required)',
                    'items' => [
                        '<strong>Laravel session</strong>: CSRF security and session maintenance',
                        '<strong>Language preference</strong>: remembering your language choice',
                    ],
                ],
                'functional' => [
                    'title' => 'Functional cookies',
                    'items' => [
                        '<strong>Google reCAPTCHA</strong>: anti-spam protection',
                    ],
                ],
                'management' => 'You can manage your cookie preferences in your browser settings. Note that disabling essential cookies may affect the functioning of the site.',
            ],
        ],

        'international_transfer' => [
            'title' => 'International Transfers',
            'content' => 'Your data is hosted within the European Union (Hostinger). Some third-party services (Google reCAPTCHA) may involve transfers outside the EU, governed by the European Commission\'s standard contractual clauses.',
        ],

        'minors' => [
            'title' => 'Children\'s Data',
            'content' => 'Our website is not specifically intended for minors under 15 years of age. We do not knowingly collect data about children under 15. If you are a parent and believe your child has provided us with information, contact us to have it deleted.',
        ],

        'updates' => [
            'title' => 'Policy Updates',
            'content' => 'We reserve the right to modify this privacy policy at any time. Changes take effect upon publication on this page. We encourage you to regularly consult this page to stay informed.',
        ],

        'complaints' => [
            'title' => 'Complaints',
            'content' => [
                'text' => 'If you believe your rights are not being respected, you can file a complaint with your national data protection authority.',
                'cnil' => 'For France: <strong>CNIL</strong> - National Commission on Informatics and Liberty',
                'address' => '3 Place de Fontenoy, 75007 Paris',
                'website' => 'Website: <a href="https://www.cnil.fr" target="_blank" rel="noopener noreferrer" class="text-blue-600 hover:underline">www.cnil.fr</a>',
            ],
        ],

        'contact' => [
            'title' => 'Contact',
            'content' => [
                'intro' => 'For any questions regarding this privacy policy or your personal data:',
                'email' => 'Email: <a href="mailto:'.config('mail.destination_mail_contact').'" class="text-blue-600 hover:underline">'.config('mail.destination_mail_contact').'</a>',
                'form' => 'Contact form: <a href="'.localRoute('contact').'" class="text-blue-600 hover:underline">'.localRoute('contact').'</a>',
            ],
        ],
    ],

    'last_updated' => 'Last updated',

    'seo' => [
        'title' => 'Privacy Policy',
        'description' => 'Privacy policy and personal data protection on '.config('app.name').'. Information about collection, use, and your GDPR rights.',
        'keywords' => 'privacy policy, personal data protection, GDPR, privacy, cookies, user rights, '.config('app.name'),
        'og' => [
            'type' => 'website',
            'image_alt' => config('app.name').' - Privacy Policy',
        ],
        'twitter' => [
            'card' => 'summary',
        ],
    ],
];
