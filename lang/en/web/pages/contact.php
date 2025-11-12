<?php

return [
    'breadcrumb' => [
        'current' => 'Contact',
    ],

    'hero' => [
        'badge' => 'Contact',
        'title' => [
            'part1' => 'Have a question?',
            'part2' => 'Contact us',
        ],
        'subtitle' => 'Our team is here to help. Send us a message and we\'ll get back to you as soon as possible.',
    ],

    'contact_info' => [
        'title' => 'Contact information',
        'email' => [
            'label' => 'Email',
            'value' => 'contact@explo.space',
        ],
        'response_time' => [
            'label' => 'Response time',
            'value' => 'Within 48 business hours',
        ],
    ],

    'form' => [
        'title' => 'Send us a message',
        'subtitle' => 'Fill out the form below and we\'ll respond quickly.',

        'fields' => [
            'name' => [
                'label' => 'Full name',
                'placeholder' => 'John Doe',
            ],
            'email' => [
                'label' => 'Email address',
                'placeholder' => 'john.doe@example.com',
            ],
            'subject' => [
                'label' => 'Subject',
                'placeholder' => 'What would you like to talk about?',
                'optional' => '(optional)',
            ],
            'message' => [
                'label' => 'Message',
                'placeholder' => 'Write your message here...',
            ],
        ],

        'submit' => 'Send message',
        'sending' => 'Sending...',

        'success' => [
            'title' => 'Message sent!',
            'message' => 'Thank you for your message. We will respond as soon as possible.',
        ],

        'errors' => [
            'title' => 'Error sending message',
            'message' => 'An error occurred while sending the message. Please try again.',
            'rate_limit' => 'You have reached the sending limit. Please wait before sending another message.',
            'recaptcha' => 'Security verification error. Please refresh the page and try again.',
        ],

        'validation' => [
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
        ],
    ],

    // SEO intégré pour cette page
    'seo' => [
        'title' => 'Contact us',
        'description' => 'Have a question or suggestion? Contact the '.config('app.name').' team via our contact form. We will respond as soon as possible.',
        'keywords' => 'contact, support, help, question, suggestion, '.config('app.name'),

        // Open Graph spécifique à cette page
        'og' => [
            'type' => 'website',
            'image_alt' => config('app.name').' - Contact us',
        ],

        // Twitter Cards spécifique à cette page
        'twitter' => [
            'card' => 'summary',
        ],
    ],
];
