<?php

return [
    // Filters
    'filters' => [
        'validation_error_title' => 'Invalid filters',
        'invalid_mode' => 'Invalid search mode (:mode). Please select "Nearby" or "Worldwide".',
        'invalid_radius' => 'The selected radius (:radius km) is invalid. Please choose a radius between :min km and :max km.',
        'invalid_coordinates' => 'The :type (:value) is invalid. Valid values are between :range.',
        'coordinate_latitude' => 'latitude',
        'coordinate_longitude' => 'longitude',
        'invalid_tags' => ':count invalid tag(s): :tags. Please check your selection.',
    ],

    // Map loading
    'map' => [
        'loading_failed' => 'Unable to load the map. Please refresh the page or try again later.',
        'coordinates_loading_failed' => 'Unable to load map markers. Please try again.',
        'bounding_box_error' => 'Error calculating visible area. Please zoom or move the map.',
        'system_error' => 'An error occurred while loading the map. Please try again. If the problem persists, contact customer support.',
    ],

    // List loading
    'list' => [
        'loading_failed' => 'Unable to load places. Please try again.',
        'no_results' => 'No places found matching your search criteria.',
        'system_error' => 'An error occurred while loading results. Please try again. If the problem persists, contact customer support.',
    ],

    // System errors (displayed only if critical for user)
    'system' => [
        'database_error' => 'A technical error occurred. Our team has been notified. Please try again in a few moments.',
        'service_unavailable' => 'The service is temporarily unavailable. Please try again in a few moments.',
    ],
];
