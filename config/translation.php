<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Default Translation Provider
    |--------------------------------------------------------------------------
    |
    | Le fournisseur de traduction par défaut utilisé par l'application.
    | Actuellement supporté : "deepl"
    |
    */
    'default_provider' => env('TRANSLATION_PROVIDER', 'deepl'),

    /*
    |--------------------------------------------------------------------------
    | Translation Providers Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration des différents fournisseurs de traduction.
    |
    */
    'providers' => [
        'deepl' => [
            'api_key' => env('DEEPL_API_KEY', ''),
            'language_names' => [
                // Langues européennes principales
                'fr' => 'Français',
                'en' => 'Anglais',
                'de' => 'Allemand',
                'es' => 'Espagnol',
                'it' => 'Italien',
                'pl' => 'Polonais',
                'nl' => 'Néerlandais',
                'pt' => 'Portugais',
                'ru' => 'Russe',

                // Langues européennes (autres)
                'cs' => 'Tchèque',
                'sv' => 'Suédois',
                'da' => 'Danois',
                'fi' => 'Finnois',
                'el' => 'Grec',
                'hu' => 'Hongrois',
                'ro' => 'Roumain',
                'sk' => 'Slovaque',
                'sl' => 'Slovène',
                'bg' => 'Bulgare',
                'et' => 'Estonien',
                'lv' => 'Letton',
                'lt' => 'Lituanien',
                'uk' => 'Ukrainien',
                'nb' => 'Norvégien',

                // Langues asiatiques
                'ja' => 'Japonais',
                'zh' => 'Chinois',
                'ko' => 'Coréen',
                'id' => 'Indonésien',
                'tr' => 'Turc',

                // Langues hors Europe (autres)
                'ar' => 'Arabe',

                // Valeur spéciale
                'unknown' => 'Inconnue',
            ],
        ],
    ],
];
