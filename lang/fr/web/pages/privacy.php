<?php

return [
    'breadcrumb' => [
        'current' => 'Politique de confidentialité',
    ],

    'hero' => [
        'badge' => 'Confidentialité',
        'title' => [
            'part1' => 'Politique de',
            'part2' => 'confidentialité',
        ],
        'subtitle' => 'Protection et gestion de vos données personnelles sur '.config('app.name').'.',
    ],

    'sections' => [
        'intro' => [
            'title' => 'Introduction',
            'content' => [
                'text1' => 'Chez '.config('app.name').', nous accordons une grande importance à la protection de vos données personnelles. Cette politique de confidentialité vous informe sur la manière dont nous collectons, utilisons, stockons et protégeons vos informations.',
                'text2' => 'En utilisant notre site, vous acceptez les pratiques décrites dans cette politique de confidentialité.',
            ],
        ],

        'responsible' => [
            'title' => 'Responsable du traitement',
            'content' => [
                'text' => 'Le responsable du traitement des données personnelles est :',
                'name' => '<strong>'.config('company.owner').'</strong>',
                'email' => 'Email : <a href="mailto:'.config('mail.destination_mail_contact').'" class="text-blue-600 hover:underline">'.config('mail.destination_mail_contact').'</a>',
            ],
        ],

        'data_collected' => [
            'title' => 'Données collectées',
            'content' => [
                'intro' => 'Nous collectons uniquement les données strictement nécessaires au fonctionnement du service :',
                'categories' => [
                    [
                        'title' => 'Données de contact',
                        'items' => [
                            'Adresse email (lors de la proposition de lieux ou signalement d\'erreurs)',
                        ],
                    ],
                    [
                        'title' => 'Données techniques',
                        'items' => [
                            'Adresse IP (pour la protection anti-spam et rate limiting)',
                            'Cookies techniques (session, préférences)',
                            'Informations de navigation (pages visitées, durée)',
                        ],
                    ],
                    [
                        'title' => 'Données de contenu',
                        'items' => [
                            'Informations sur les lieux proposés (titre, description, coordonnées)',
                            'Photos téléchargées',
                            'Signalements et corrections proposés',
                        ],
                    ],
                ],
            ],
        ],

        'data_usage' => [
            'title' => 'Utilisation des données',
            'content' => [
                'intro' => 'Vos données sont utilisées exclusivement pour les finalités suivantes :',
                'items' => [
                    'Traiter vos propositions de lieux et signalements',
                    'Vous répondre et vous informer du traitement de vos demandes',
                    'Assurer le bon fonctionnement et la sécurité du site',
                    'Prévenir les abus et le spam (reCAPTCHA, rate limiting)',
                    'Améliorer nos services et l\'expérience utilisateur',
                    'Respecter nos obligations légales',
                ],
            ],
        ],

        'legal_basis' => [
            'title' => 'Base légale du traitement',
            'content' => [
                'intro' => 'Le traitement de vos données repose sur les bases légales suivantes :',
                'items' => [
                    '<strong>Consentement</strong> : pour l\'utilisation de cookies non essentiels',
                    '<strong>Exécution d\'un service</strong> : pour le traitement de vos propositions',
                    '<strong>Intérêt légitime</strong> : pour la sécurité et l\'amélioration du site',
                    '<strong>Obligation légale</strong> : pour la conservation de certaines données',
                ],
            ],
        ],

        'data_retention' => [
            'title' => 'Durée de conservation',
            'content' => [
                'intro' => 'Nous conservons vos données pendant les durées suivantes :',
                'items' => [
                    '<strong>Emails de contact</strong> : 3 ans après le traitement de votre demande',
                    '<strong>Logs de connexion</strong> : 12 mois',
                    '<strong>Cookies de session</strong> : durée de la session',
                    '<strong>Cookies de préférences</strong> : 1 an',
                    '<strong>Contenu publié</strong> : tant que le lieu est actif sur le site',
                ],
            ],
        ],

        'data_sharing' => [
            'title' => 'Partage des données',
            'content' => [
                'intro' => 'Nous ne vendons ni ne louons vos données personnelles. Vos données peuvent être partagées uniquement dans les cas suivants :',
                'items' => [
                    '<strong>Hébergeur</strong> : Hostinger International Ltd. (stockage des données)',
                    '<strong>Service anti-spam</strong> : Google reCAPTCHA (validation des formulaires)',
                    '<strong>Obligation légale</strong> : sur demande des autorités compétentes',
                ],
                'text' => 'Ces prestataires sont soumis à des obligations de confidentialité et ne peuvent utiliser vos données que dans le cadre de leurs services.',
            ],
        ],

        'your_rights' => [
            'title' => 'Vos droits',
            'content' => [
                'intro' => 'Conformément au RGPD, vous disposez des droits suivants :',
                'items' => [
                    '<strong>Droit d\'accès</strong> : obtenir une copie de vos données',
                    '<strong>Droit de rectification</strong> : corriger vos données inexactes',
                    '<strong>Droit à l\'effacement</strong> : demander la suppression de vos données',
                    '<strong>Droit à la limitation</strong> : limiter le traitement de vos données',
                    '<strong>Droit à la portabilité</strong> : récupérer vos données dans un format structuré',
                    '<strong>Droit d\'opposition</strong> : vous opposer au traitement de vos données',
                    '<strong>Droit de retirer votre consentement</strong> : à tout moment',
                ],
                'how_to' => 'Pour exercer vos droits, contactez-nous à : <a href="mailto:'.config('mail.destination_mail_contact').'" class="text-blue-600 hover:underline">'.config('mail.destination_mail_contact').'</a>',
                'deadline' => 'Nous nous engageons à répondre dans un délai maximum d\'1 mois.',
            ],
        ],

        'security' => [
            'title' => 'Sécurité des données',
            'content' => [
                'intro' => 'Nous mettons en œuvre des mesures de sécurité appropriées pour protéger vos données :',
                'items' => [
                    'Chiffrement HTTPS pour toutes les communications',
                    'Protection par mot de passe des accès administrateur',
                    'Sauvegardes régulières des données',
                    'Protection anti-spam et rate limiting',
                    'Mises à jour régulières des systèmes',
                ],
            ],
        ],

        'cookies' => [
            'title' => 'Cookies et traceurs',
            'content' => [
                'intro' => 'Nous utilisons les cookies suivants :',
                'essential' => [
                    'title' => 'Cookies essentiels (obligatoires)',
                    'items' => [
                        '<strong>Session Laravel</strong> : sécurité CSRF et maintien de session',
                        '<strong>Préférence de langue</strong> : mémorisation de votre choix de langue',
                    ],
                ],
                'functional' => [
                    'title' => 'Cookies fonctionnels',
                    'items' => [
                        '<strong>Google reCAPTCHA</strong> : protection anti-spam',
                    ],
                ],
                'management' => 'Vous pouvez gérer vos préférences de cookies dans les paramètres de votre navigateur. Notez que la désactivation des cookies essentiels peut affecter le fonctionnement du site.',
            ],
        ],

        'international_transfer' => [
            'title' => 'Transferts internationaux',
            'content' => 'Vos données sont hébergées au sein de l\'Union Européenne (Hostinger). Certains services tiers (Google reCAPTCHA) peuvent impliquer des transferts hors UE, encadrés par les clauses contractuelles types de la Commission européenne.',
        ],

        'minors' => [
            'title' => 'Données des mineurs',
            'content' => 'Notre site ne s\'adresse pas spécifiquement aux mineurs de moins de 15 ans. Nous ne collectons pas sciemment de données concernant des enfants de moins de 15 ans. Si vous êtes parent et pensez que votre enfant nous a fourni des informations, contactez-nous pour les faire supprimer.',
        ],

        'updates' => [
            'title' => 'Modifications de la politique',
            'content' => 'Nous nous réservons le droit de modifier cette politique de confidentialité à tout moment. Les modifications entrent en vigueur dès leur publication sur cette page. Nous vous encourageons à consulter régulièrement cette page pour rester informé.',
        ],

        'complaints' => [
            'title' => 'Réclamations',
            'content' => [
                'text' => 'Si vous estimez que vos droits ne sont pas respectés, vous pouvez introduire une réclamation auprès de la CNIL :',
                'cnil' => '<strong>CNIL</strong> - Commission Nationale de l\'Informatique et des Libertés',
                'address' => '3 Place de Fontenoy, 75007 Paris',
                'website' => 'Site web : <a href="https://www.cnil.fr" target="_blank" rel="noopener noreferrer" class="text-blue-600 hover:underline">www.cnil.fr</a>',
            ],
        ],

        'contact' => [
            'title' => 'Contact',
            'content' => [
                'intro' => 'Pour toute question concernant cette politique de confidentialité ou vos données personnelles :',
                'email' => 'Email : <a href="mailto:'.config('mail.destination_mail_contact').'" class="text-blue-600 hover:underline">'.config('mail.destination_mail_contact').'</a>',
                'form' => 'Formulaire de contact : <a href="'.localRoute('contact').'" class="text-blue-600 hover:underline">'.localRoute('contact').'</a>',
            ],
        ],
    ],

    'last_updated' => 'Dernière mise à jour',

    'seo' => [
        'title' => 'Politique de confidentialité',
        'description' => 'Politique de confidentialité et protection des données personnelles sur '.config('app.name').'. Informations sur la collecte, l\'utilisation et vos droits RGPD.',
        'keywords' => 'politique de confidentialité, protection données personnelles, RGPD, vie privée, cookies, droits utilisateurs, '.config('app.name'),
        'og' => [
            'type' => 'website',
            'image_alt' => config('app.name').' - Politique de confidentialité',
        ],
        'twitter' => [
            'card' => 'summary',
        ],
    ],
];
