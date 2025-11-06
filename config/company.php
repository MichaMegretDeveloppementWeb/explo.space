<?php

return [
    'name' => env('COMPANY_NAME', 'Explo space'),
    'legal_name' => env('COMPANY_NAME', 'Explo space'),
    'country' => env('COMPANY_COUNTRY_CODE', 'FR'),
    'founding_date' => env('COMPANY_FOUNDING_AT', '2025'),
    'owner' => env('COMPANY_OWNER', 'Explo space'),
    'social' => [
        'twitter' => [
            'url' => env('TWITTER_URL', ''),
            'creator' => env('TWITTER_CREATOR', ''),
        ],
    ],
];
