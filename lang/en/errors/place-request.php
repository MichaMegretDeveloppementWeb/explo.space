<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Place Request Errors
    |--------------------------------------------------------------------------
    |
    | Translated error messages for the place request form.
    | These translations are based on the exception's errorType.
    |
    */

    // Photos - Validation
    'photo.general' => 'The photo file is not valid.',
    'photo.invalid_format' => 'The file must be an image (JPEG, PNG, WebP).',
    'photo.size_limit' => 'The file must not exceed the maximum allowed size.',
    'photo.svg_not_allowed' => 'SVG files are not allowed for security reasons.',

    // Photos - Processing
    'photo.processing' => 'An error occurred while processing the images. Please try again with lighter images.',
    'photo.unexpected' => 'An unexpected error occurred while processing photos. Please try again.',

    // Other possible error types
    'general' => 'An error occurred. Please try again.',
    'recaptcha.failed' => 'reCAPTCHA verification failed. Please try again.',
    'database.error' => 'A database error occurred. Please try again.',
];
