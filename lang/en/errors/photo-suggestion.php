<?php

return [
    // User errors (upload)
    'photos_limit_exceeded' => 'You cannot add more than :max photos in total. You already have :current.',

    // Technical errors (upload)
    'unexpected_upload' => 'An unexpected error occurred while uploading photos. Please try again.',

    // User errors (submission)
    'place_not_found' => 'This place no longer exists or has been deleted. Cannot submit photos.',
    'photo_validation' => 'One or more photos do not meet validation criteria. Please check their format and size.',

    // Technical errors (submission)
    'photo_processing' => 'An error occurred while processing your photos. Please try again in a few moments.',
    'unexpected_photo' => 'An unexpected error occurred while processing photos. Please try again.',
    'database' => 'A database error occurred. Please try again in a few moments.',
    'unexpected' => 'An unexpected error occurred. Please try again.',
];
