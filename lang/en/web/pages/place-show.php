<?php

return [
    // Sections
    'sections' => [
        'overview' => 'Overview',
        'description' => 'Description',
        'practical_info' => 'Practical information',
        'photos' => 'Photos',
        'location' => 'Location',
    ],

    // Metadata
    'metadata' => [
        'added_on' => 'Added on',
        'last_updated' => 'Last updated',
        'coordinates' => 'GPS coordinates',
        'address' => 'Address',
    ],

    // Tags
    'tags' => [
        'title' => 'Topics',
    ],

    // Actions
    'actions' => [
        'report_error' => 'Report an error',
        'suggest_edit' => 'Suggest an edit',
        'view_on_map' => 'View on map',
        'share' => 'Share',
    ],

    // Edit request form (report error or suggest modification)
    'edit_request' => [
        'button' => 'Report an error or suggest an edit',
        'title' => 'Report an error or suggest an edit',

        // Request type
        'type_label' => 'Type of request',
        'type_signalement' => 'Report an error (inaccurate information, closed place, etc.)',
        'type_modification' => 'Suggest an edit (improve the information)',

        // Field selection
        'select_fields' => 'Which fields would you like to modify?',
        'field_title' => 'Title',
        'field_description' => 'Description',
        'field_coordinates' => 'GPS coordinates',
        'field_address' => 'Address',
        'field_practical_info' => 'Practical information',

        // Value comparison
        'current_value' => 'Current value',
        'new_value_placeholder' => 'Proposed new value',

        // Description/Comment
        'description_label_signalement' => 'Describe the issue you found',
        'description_label_modification' => 'Comment on your modifications (optional)',
        'description_placeholder' => 'Describe in detail the modifications or issue you found...',
        'description_placeholder_signalement' => 'Describe in detail the issue you found...',
        'description_placeholder_modification' => 'Add a comment to explain your modifications (optional)...',

        // Contact email
        'contact_email_label' => 'Your contact email',
        'contact_email_placeholder' => 'your@email.com',

        // Actions
        'submit' => 'Send request',
        'submitting' => 'Sending...',
        'cancel' => 'Cancel',

        // Messages
        'success' => 'Your request has been sent successfully. We will review it as soon as possible.',
        'error' => 'An error occurred while sending your request. Please try again.',
    ],

    // Photos
    'photos' => [
        'main_photo' => 'Main photo',
        'gallery' => 'Photo gallery',
        'view_fullscreen' => 'View fullscreen',
        'previous' => 'Previous photo',
        'next' => 'Next photo',
        'close' => 'Close',
        'counter' => ':current of :total',
    ],

    // Map
    'map' => [
        'title' => 'Location',
        'view_larger' => 'View larger',
        'open_in_maps' => 'Open in Maps',
    ],

    // Errors
    'errors' => [
        'not_found' => 'This place does not exist or is no longer available.',
        'translation_missing' => 'This page is not available in your language.',
    ],

    // Photo suggestion form
    'photo_suggestion' => [
        'button' => 'Suggest new photos',
        'title' => 'Suggest new photos',
        'subtitle' => 'Share your photos of this place to enrich the listing',
        'photos_label' => 'Photos',
        'upload_button' => 'Upload photos',
        'drag_drop' => 'or drag and drop',
        'file_requirements' => 'JPEG, PNG, WebP up to 5 MB (max 10 photos)',
        'selected_photos' => 'Selected photos',
        'validating' => 'Validating photos...',
        'contact_email' => 'Your contact email',
        'email_placeholder' => 'your@email.com',
        'cancel' => 'Cancel',
        'submit' => 'Submit',
        'submitting' => 'Submitting...',
        'success' => 'Your photos have been submitted successfully. We will review them as soon as possible.',
    ],

    // SEO - Generic keywords
    'seo' => [
        'keywords' => [
            'space exploration',
            'space conquest',
            'space tourism',
        ],
    ],
];
