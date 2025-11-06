<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Validation - Edit Request / Error Report
    |--------------------------------------------------------------------------
    |
    | Translated validation messages for the edit request and error report form.
    |
    */

    // General fields
    'type' => [
        'required' => 'The request type is required.',
        'in' => 'The request type must be either a report or an edit suggestion.',
    ],

    'description' => [
        'required' => 'The description is required.',
        'min' => 'The description must be at least :min characters.',
        'max' => 'The description cannot exceed :max characters.',
    ],

    'contact_email' => [
        'required' => 'The contact email is required.',
        'email' => 'The contact email must be a valid email address.',
        'max' => 'The contact email cannot exceed :max characters.',
    ],

    // Selected fields for modification
    'selected_fields' => [
        'required' => 'Please select at least one field to modify.',
        'min' => 'Please select at least one field to modify.',
        'array' => 'The selected fields must be a list.',
        'in' => 'The selected field is invalid.',
    ],

    // New values
    'new_values' => [
        'title' => [
            'required' => 'The new title is required if you are modifying this field.',
            'max' => 'The title cannot exceed :max characters.',
        ],
        'description' => [
            'required' => 'The new description is required if you are modifying this field.',
            'max' => 'The description cannot exceed :max characters.',
        ],
        'address' => [
            'required' => 'The new address is required if you are modifying this field.',
            'max' => 'The address cannot exceed :max characters.',
        ],
        'practical_info' => [
            'required' => 'The new practical information is required if you are modifying this field.',
            'max' => 'The practical information cannot exceed :max characters.',
        ],
        'coordinates' => [
            'lat' => [
                'required' => 'The latitude is required if you are modifying coordinates.',
                'numeric' => 'The latitude must be a number.',
                'between' => 'The latitude must be between :min and :max degrees.',
            ],
            'lng' => [
                'required' => 'The longitude is required if you are modifying coordinates.',
                'numeric' => 'The longitude must be a number.',
                'between' => 'The longitude must be between :min and :max degrees.',
            ],
        ],
    ],

    // reCAPTCHA
    'recaptcha' => [
        'required' => 'The anti-robot validation is required.',
        'failed' => 'The anti-robot validation failed. Please try again.',
    ],
];
