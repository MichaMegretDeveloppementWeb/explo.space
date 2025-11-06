<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default Geocoding Provider
    |--------------------------------------------------------------------------
    |
    | This option controls the default geocoding provider that will be used
    | by the application. You may set this to any of the providers defined
    | in the "providers" array below.
    |
    | Supported: "nominatim", "google", "mapbox"
    |
    */

    'default_provider' => env('GEOCODING_PROVIDER', 'nominatim'),

    /*
    |--------------------------------------------------------------------------
    | Geocoding Providers Configuration
    |--------------------------------------------------------------------------
    |
    | Here you may configure the settings for each geocoding provider.
    | Each provider may require different configuration options such as
    | API keys, URLs, rate limits, etc.
    |
    */

    'providers' => [

        /*
        |--------------------------------------------------------------------------
        | Nominatim (OpenStreetMap)
        |--------------------------------------------------------------------------
        |
        | Nominatim is a free geocoding service based on OpenStreetMap data.
        | No API key required, but rate limiting is enforced.
        |
        | Documentation: https://nominatim.org/release-docs/latest/api/Overview/
        |
        */

        'nominatim' => [
            'url' => env('NOMINATIM_URL', 'https://nominatim.openstreetmap.org'),
            'rate_limit_delay' => env('NOMINATIM_RATE_LIMIT', 1), // seconds between requests
            'user_agent' => env('NOMINATIM_USER_AGENT', config('app.name')),
        ],

        /*
        |--------------------------------------------------------------------------
        | Google Geocoding API
        |--------------------------------------------------------------------------
        |
        | Google's Geocoding API provides accurate geocoding results worldwide.
        | Requires an API key with the Geocoding API enabled.
        |
        | Documentation: https://developers.google.com/maps/documentation/geocoding
        |
        */

        'google' => [
            'api_key' => env('GOOGLE_GEOCODING_API_KEY'),
            'url' => 'https://maps.googleapis.com/maps/api/geocode/json',
        ],

        /*
        |--------------------------------------------------------------------------
        | Mapbox Geocoding API
        |--------------------------------------------------------------------------
        |
        | Mapbox provides fast and accurate geocoding with excellent coverage.
        | Requires an access token from your Mapbox account.
        |
        | Documentation: https://docs.mapbox.com/api/search/geocoding/
        |
        */

        'mapbox' => [
            'access_token' => env('MAPBOX_ACCESS_TOKEN'),
            'url' => 'https://api.mapbox.com/geocoding/v5',
        ],

    ],

];
