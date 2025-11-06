<?php

return [
    // Main page title
    'title' => 'Propose a Space Place',
    'subtitle' => 'Share a place related to space exploration or universe discovery',

    // Description
    'description' => 'Do you know a site, museum, observatory or other place dedicated to space? Propose it to our community! Your contribution will be reviewed by our team before publication.',

    // Informative badges
    'badges' => [
        'team_review' => 'Team review',
        'email_notification' => 'Email notification',
        'community_contribution' => 'Community contribution',
    ],

    // Form - Contact information
    'contact' => [
        'title' => 'Your Contact Information',
        'email' => 'Your email address',
        'email_placeholder' => 'your@email.com',
        'email_help' => 'We will only contact you to inform you about the status of your proposal.',
    ],

    // Form - Location
    'location' => [
        'title' => 'Place Location',
        'search' => 'Search for an address',
        'search_placeholder' => 'Search for an address...',
        'search_help' => 'Type at least 3 characters to see suggestions',
        'no_results' => 'No address found',
        'address_validated' => 'Address validated',
        'interactive_map' => 'Interactive map',
        'map_help' => 'Click on the map to set coordinates, or move the marker',
        'latitude' => 'Latitude',
        'longitude' => 'Longitude',
        'address_field' => 'Place address',
        'address_placeholder' => 'Complete address',
        'address_help' => 'Address that will be saved and visible to users',
        'manual_entry_hint' => 'Enter manually',
        'optional' => 'Optional',
    ],

    // Form - Place information
    'place_info' => [
        'title' => 'Place Information',
        'name' => 'Place name',
        'name_placeholder' => 'E.g.: Kennedy Space Center',
        'description' => 'Description',
        'description_placeholder' => 'Describe the place, its history, its features...',
        'description_help' => 'Be as detailed as possible to help our team validate your proposal',
        'practical_info' => 'Practical Information',
        'practical_info_placeholder' => 'Hours, rates, access, visiting conditions...',
        'practical_info_help' => 'This information will help visitors plan their visit',
    ],

    // Form - Photos
    'photos' => [
        'title' => 'Place Photos',
        'upload' => 'Upload photos',
        'or_drag_drop' => 'or drag and drop',
        'formats_help' => 'JPEG, PNG, WebP up to :max_size_mb MB (max :max_files photos)',
        'cumulative_limit' => 'Cumulative limits',
        'mb_total' => 'MB total',
        'error_file_too_large' => 'The file ":filename" (:size MB) exceeds the maximum allowed size.',
        'error_total_too_large' => 'The total size of files (:size MB) exceeds the cumulative limit. Please select fewer or smaller files.',
        'preview' => 'Selected photos',
        'selected_photo' => 'Selected photo',
        'main_auto' => 'Main (auto)',
        'remove_photo' => 'Remove this photo',
        'tips_title' => 'Tips for photos',
        'tip_quality' => 'Use high-quality and well-framed photos',
        'tip_formats' => 'Accepted formats: JPEG, PNG, WebP (no SVG)',
        'uploading' => 'Validating photos...',
        'optional' => 'Optional',
    ],

    // Validation messages
    'validation' => [
        // Contact
        'email_required' => 'Your email address is required.',
        'email_valid' => 'The email address must be valid.',
        'email_max' => 'The email address cannot exceed :max characters.',

        // Place information
        'title_required' => 'The place name is required.',
        'title_min' => 'The place name must be at least :min characters.',
        'title_max' => 'The place name cannot exceed :max characters.',
        'description_max' => 'The description cannot exceed :max characters.',
        'practical_info_max' => 'The practical information cannot exceed :max characters.',

        // Location
        'latitude_numeric' => 'The latitude must be a number.',
        'latitude_between' => 'The latitude must be between :min and :max.',
        'longitude_numeric' => 'The longitude must be a number.',
        'longitude_between' => 'The longitude must be between :min and :max.',
        'address_max' => 'The address cannot exceed :max characters.',

        // Photos
        'photos_max' => 'You can only add up to :max photos.',
        'photo_size' => 'Each photo cannot exceed :max MB.',
        'photo_format' => 'Photos must be in JPEG, PNG or WebP format.',

        // reCAPTCHA
        'recaptcha_required' => 'Security verification is required. Please try again.',
    ],

    // Action buttons
    'actions' => [
        'cancel' => 'Cancel',
        'submit' => 'Submit my proposal',
        'submitting' => 'Submitting...',
    ],

    // Success/error messages
    'messages' => [
        'success' => 'Thank you! Your proposal has been successfully submitted. Our team will review it and keep you informed by email.',
        'error' => 'An error occurred while submitting your proposal. Please try again.',
        'recaptcha_error' => 'An error occurred with the security verification. Please try again.',
        'photos_limit_exceeded' => 'You cannot have more than :max photos in total. You already have :current photo(s).',
        'photos_validated' => ':count photo(s) validated',
        'photos_unexpected_error' => 'An unexpected error occurred while processing photos. Please try again.',
    ],

    // SEO section
    'seo' => [
        'title' => 'Propose a Space Place',
        'description' => 'Share a space-related place with our community. Propose a site, museum, observatory or any other place dedicated to space exploration.',
        'keywords' => 'propose space place, add space location, contribution, space museum, observatory, space site',

        'og' => [
            'type' => 'website',
            'image_alt' => 'Propose a space place on Explo.space',
        ],

        'twitter' => [
            'card' => 'summary_large_image',
        ],
    ],
];
