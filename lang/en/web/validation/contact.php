<?php

return [
    'name' => [
        'required' => 'Full name is required.',
        'min' => 'Full name must be at least :min characters.',
        'max' => 'Full name cannot exceed :max characters.',
    ],
    'email' => [
        'required' => 'Email address is required.',
        'email' => 'Email address must be a valid email address.',
        'max' => 'Email address cannot exceed :max characters.',
    ],
    'subject' => [
        'max' => 'Subject cannot exceed :max characters.',
    ],
    'message' => [
        'required' => 'Message is required.',
        'min' => 'Message must be at least :min characters.',
        'max' => 'Message cannot exceed :max characters.',
    ],
    'recaptcha_token' => [
        'required' => 'Security verification is required.',
    ],
];
