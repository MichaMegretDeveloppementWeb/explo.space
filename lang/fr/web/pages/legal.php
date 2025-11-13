<?php

return [
    'breadcrumb' => [
        'current' => 'Mentions légales',
    ],

    'hero' => [
        'badge' => 'Mentions légales',
        'title' => [
            'part1' => 'Mentions',
            'part2' => 'légales',
        ],
        'subtitle' => 'Informations légales et éditoriales du site '.config('app.name').'.',
    ],

    'sections' => [
        'editor' => [
            'title' => 'Éditeur du site',
            'content' => [
                'intro' => 'Le site '.config('app.name').' est édité par :',
                'name' => '<strong>'.config('company.owner').'</strong>',
                'status' => 'Particulier',
                'email' => 'Email : <a href="mailto:'.config('mail.from.address').'" class="text-blue-600 hover:underline">'.config('mail.from.address').'</a>',
            ],
        ],

        'publication' => [
            'title' => 'Directeur de la publication',
            'content' => 'Le directeur de la publication du site est <strong>'.config('company.owner').'</strong>.',
        ],

        'developer' => [
            'title' => 'Développement',
            'content' => [
                'intro' => 'Le site a été développé par :',
                'name' => '<strong>Micha Megret</strong> - Développeur web freelance',
                'address' => 'Adresse : 261 rue des Tattes, 74500 Publier',
                'website' => 'Site web : <a href="https://micha-megret.fr" target="_blank" rel="noopener noreferrer" class="text-blue-600 hover:underline">www.micha-megret.fr</a>',
            ],
        ],

        'hosting' => [
            'title' => 'Hébergement',
            'content' => [
                'intro' => 'Le site est hébergé par :',
                'name' => '<strong>Hostinger International Ltd.</strong>',
                'address' => '61 Lordou Vironos Street, 6023 Larnaca, Chypre',
                'website' => 'Site web : <a href="https://www.hostinger.fr" target="_blank" rel="noopener noreferrer" class="text-blue-600 hover:underline">www.hostinger.fr</a>',
            ],
        ],

        'intellectual_property' => [
            'title' => 'Propriété intellectuelle',
            'content' => [
                'intro' => 'L\'ensemble du contenu de ce site (textes, images, vidéos, design, etc.) est la propriété exclusive de '.config('company.owner').' ou de ses partenaires, sauf mention contraire.',
                'rights' => 'Toute reproduction, distribution, modification, adaptation, retransmission ou publication de ces différents éléments est strictement interdite sans l\'accord exprès par écrit de '.config('company.owner').'.',
                'exception' => 'Les photographies et contenus visuels de lieux spatiaux peuvent être soumis aux droits d\'auteur de leurs créateurs respectifs. Leur utilisation sur ce site est faite à titre informatif et pédagogique.',
            ],
        ],

        'personal_data' => [
            'title' => 'Données personnelles et RGPD',
            'content' => [
                'responsible' => 'Le responsable du traitement des données personnelles collectées sur '.config('app.name').' est '.config('company.owner').'.',
                'principles' => 'Conformément au Règlement Général sur la Protection des Données (RGPD) et à la loi "Informatique et Libertés", vous disposez d\'un droit d\'accès, de rectification, de suppression et de portabilité de vos données personnelles.',
                'collected_data' => [
                    'title' => 'Données collectées :',
                    'items' => [
                        'Email de contact lors des propositions de lieux ou signalements',
                        'Informations de navigation (cookies techniques)',
                        'Adresse IP pour le rate limiting (protection anti-spam)',
                    ],
                ],
                'usage' => 'Ces données sont utilisées uniquement pour :',
                'usage_items' => [
                    'Assurer le fonctionnement du service',
                    'Répondre à vos demandes',
                    'Améliorer nos services',
                ],
                'retention' => 'Vos données sont conservées pendant la durée nécessaire aux finalités pour lesquelles elles ont été collectées, puis supprimées.',
                'rights' => 'Pour exercer vos droits, contactez-nous à l\'adresse : <a href="mailto:'.config('mail.destination_mail_contact').'" class="text-blue-600 hover:underline">'.config('mail.destination_mail_contact').'</a>',
                'cnil' => 'Vous disposez également du droit d\'introduire une réclamation auprès de la CNIL (Commission Nationale de l\'Informatique et des Libertés) : <a href="https://www.cnil.fr" target="_blank" rel="noopener noreferrer" class="text-blue-600 hover:underline">www.cnil.fr</a>',
            ],
        ],

        'cookies' => [
            'title' => 'Cookies',
            'content' => [
                'intro' => 'Le site utilise des cookies techniques essentiels au fonctionnement du service :',
                'items' => [
                    'Cookie de session Laravel (sécurité CSRF)',
                    'Cookie de préférence de langue',
                    'Google reCAPTCHA (protection anti-spam)',
                ],
                'acceptance' => 'En utilisant ce site, vous acceptez l\'utilisation de ces cookies nécessaires.',
                'management' => 'Vous pouvez gérer vos préférences de cookies dans les paramètres de votre navigateur.',
            ],
        ],

        'liability' => [
            'title' => 'Responsabilité',
            'content' => [
                'accuracy' => config('company.owner').' s\'efforce d\'assurer l\'exactitude et la mise à jour des informations diffusées sur ce site, dont il se réserve le droit de corriger, à tout moment et sans préavis, le contenu.',
                'disclaimer' => 'Toutefois, '.config('company.owner').' ne peut garantir l\'exactitude, la précision ou l\'exhaustivité des informations mises à disposition sur ce site.',
                'external_links' => 'Les liens hypertextes mis en place dans le cadre du site en direction d\'autres sites ne sauraient engager la responsabilité de '.config('company.owner').'.',
            ],
        ],

        'applicable_law' => [
            'title' => 'Droit applicable',
            'content' => 'Les présentes mentions légales sont régies par le droit français. Tout litige relatif à l\'utilisation du site '.config('app.name').' est soumis à la compétence exclusive des tribunaux français.',
        ],

        'contact' => [
            'title' => 'Contact',
            'content' => [
                'intro' => 'Pour toute question concernant ces mentions légales, vous pouvez nous contacter :',
                'email' => 'Par email : <a href="mailto:'.config('mail.destination_mail_contact').'" class="text-blue-600 hover:underline">'.config('mail.destination_mail_contact').'</a>',
                'form' => 'Via notre formulaire de contact',
                'form_url' => 'contact', // Route name for localRoute() in view
            ],
        ],
    ],

    'last_updated' => 'Dernière mise à jour',

    // SEO intégré pour cette page
    'seo' => [
        'title' => 'Mentions légales',
        'description' => 'Mentions légales, informations éditoriales et RGPD du site '.config('app.name').'. Informations sur l\'éditeur, l\'hébergeur et vos droits.',
        'keywords' => 'mentions légales, informations légales, RGPD, données personnelles, éditeur, hébergeur, '.config('app.name'),

        // Open Graph spécifique à cette page
        'og' => [
            'type' => 'website',
            'image_alt' => config('app.name').' - Mentions légales',
        ],

        // Twitter Cards spécifique à cette page
        'twitter' => [
            'card' => 'summary',
        ],
    ],
];
