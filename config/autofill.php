<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default Provider
    |--------------------------------------------------------------------------
    |
    | The default AI provider used for auto-fill workflows.
    | Supported: "openai", "anthropic", "gemini"
    |
    */

    'default_provider' => env('AUTOFILL_DEFAULT_PROVIDER', 'openai'),

    /*
    |--------------------------------------------------------------------------
    | Quantity Limits
    |--------------------------------------------------------------------------
    */

    'default_quantity' => 10,
    'max_quantity' => 50,

    /*
    |--------------------------------------------------------------------------
    | Provider Models
    |--------------------------------------------------------------------------
    |
    | Model identifiers per provider, grouped by tier:
    | - mid: balanced quality/cost (enrichment, images, translation)
    | - light: fast and cheap (discovery, deduplication)
    |
    */

    'providers' => [
        'openai' => [
            'models' => [
                'mid' => 'gpt-4o-mini',
                'light' => 'gpt-4o-mini',
            ],
        ],
        'anthropic' => [
            'models' => [
                'mid' => 'claude-sonnet-4-6',
                'light' => 'claude-haiku-4-5-20251001',
            ],
        ],
        'gemini' => [
            'models' => [
                'mid' => 'gemini-3-flash-preview',
                'light' => 'gemini-3-flash-preview',
            ],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Images
    |--------------------------------------------------------------------------
    */

    'images' => [
        'max_per_place' => 5,
        'temp_disk' => 'autofill_temp',
    ],

    /*
    |--------------------------------------------------------------------------
    | Image Sources API Keys
    |--------------------------------------------------------------------------
    */

    'image_sources' => [
        'wikimedia_user_agent' => env('WIKIMEDIA_USER_AGENT', 'AirSpaceAtlas/1.0'),
        'unsplash_key' => env('UNSPLASH_ACCESS_KEY'),
        'pexels_key' => env('PEXELS_API_KEY'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Deduplication
    |--------------------------------------------------------------------------
    |
    | Radius in meters for considering two places as duplicates.
    |
    */

    'deduplication_radius' => 2000,
    'deduplication_name_threshold' => 60,
    'deduplication_max_iterations' => 3,

    /*
    |--------------------------------------------------------------------------
    | Web Search
    |--------------------------------------------------------------------------
    */

    'web_search_max' => 10,

    /*
    |--------------------------------------------------------------------------
    | Feature Flags
    |--------------------------------------------------------------------------
    */

    'features' => [
        'tag_suggestions' => true,
    ],

    /*
    |--------------------------------------------------------------------------
    | Queue
    |--------------------------------------------------------------------------
    |
    | Named queue for auto-fill jobs on the default queue connection.
    |
    */

    'queue' => env('AUTOFILL_QUEUE', 'autofill'),

];
