<?php

return [
    'breadcrumb' => [
        'current' => 'Contact',
    ],

    'hero' => [
        'badge' => 'Contact',
        'title' => [
            'part1' => 'Une question ?',
            'part2' => 'Contactez-nous',
        ],
        'subtitle' => 'Notre équipe est là pour vous aider. Envoyez-nous un message et nous vous répondrons dans les plus brefs délais.',
    ],

    'contact_info' => [
        'title' => 'Informations de contact',
        'email' => [
            'label' => 'Email',
            'value' => 'contact@explo.space',
        ],
        'response_time' => [
            'label' => 'Délai de réponse',
            'value' => 'Sous 48 heures ouvrées',
        ],
    ],

    'form' => [
        'title' => 'Envoyez-nous un message',
        'subtitle' => 'Remplissez le formulaire ci-dessous et nous vous répondrons rapidement.',

        'fields' => [
            'name' => [
                'label' => 'Nom complet',
                'placeholder' => 'Jean Dupont',
            ],
            'email' => [
                'label' => 'Adresse email',
                'placeholder' => 'jean.dupont@example.com',
            ],
            'subject' => [
                'label' => 'Sujet',
                'placeholder' => 'De quoi souhaitez-vous parler ?',
                'optional' => '(optionnel)',
            ],
            'message' => [
                'label' => 'Message',
                'placeholder' => 'Écrivez votre message ici...',
            ],
        ],

        'submit' => 'Envoyer le message',
        'sending' => 'Envoi en cours...',

        'success' => [
            'title' => 'Message envoyé !',
            'message' => 'Merci pour votre message. Nous vous répondrons dans les plus brefs délais.',
        ],

        'errors' => [
            'title' => 'Erreur lors de l\'envoi',
            'message' => 'Une erreur s\'est produite lors de l\'envoi du message. Veuillez réessayer.',
            'rate_limit' => 'Vous avez atteint la limite d\'envoi. Veuillez patienter avant de renvoyer un message.',
            'recaptcha' => 'Erreur de vérification de sécurité. Veuillez actualiser la page et réessayer.',
        ],
    ],

    // SEO intégré pour cette page
    'seo' => [
        'title' => 'Contactez-nous',
        'description' => 'Une question ou une suggestion ? Contactez l\'équipe '.config('app.name').' via notre formulaire de contact. Nous vous répondrons dans les plus brefs délais.',
        'keywords' => 'contact, support, aide, question, suggestion, '.config('app.name'),

        // Open Graph spécifique à cette page
        'og' => [
            'type' => 'website',
            'image_alt' => config('app.name').' - Contactez-nous',
        ],

        // Twitter Cards spécifique à cette page
        'twitter' => [
            'card' => 'summary',
        ],
    ],
];
