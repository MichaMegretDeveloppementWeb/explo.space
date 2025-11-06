<?php

return [
    'no_results' => 'No address found for this search. Try with more specific terms.',
    'connection_failed' => 'Unable to connect to the geolocation service. Please check your internet connection.',
    'rate_limited' => 'Too many searches performed. Please wait :seconds seconds before trying again.',
    'service' => [
        'rate_limit' => 'Too many searches in progress. Please wait a few seconds before trying again.',
        'unavailable' => 'The geolocation service is temporarily unavailable. Please try again later.',
        'generic' => 'Geolocation service error. Please try again.',
    ],
    'request_failed' => 'Error during geolocation request. Please try again.',
    'unexpected' => 'An unexpected error occurred. Please try again.',

    // Reverse geocoding specific errors
    'reverse' => [
        'no_results' => 'No address found for these coordinates.',
        'invalid_coordinates' => 'The provided coordinates are not valid.',
        'location_not_geocodable' => 'This location cannot be geocoded (unmapped area or ocean).',
    ],
];
