<?php

return [
    'name' => [
        'required' => 'Le nom complet est obligatoire.',
        'min' => 'Le nom complet doit contenir au moins :min caractères.',
        'max' => 'Le nom complet ne peut pas dépasser :max caractères.',
    ],
    'email' => [
        'required' => 'L\'adresse email est obligatoire.',
        'email' => 'L\'adresse email doit être une adresse email valide.',
        'max' => 'L\'adresse email ne peut pas dépasser :max caractères.',
    ],
    'subject' => [
        'max' => 'Le sujet ne peut pas dépasser :max caractères.',
    ],
    'message' => [
        'required' => 'Le message est obligatoire.',
        'min' => 'Le message doit contenir au moins :min caractères.',
        'max' => 'Le message ne peut pas dépasser :max caractères.',
    ],
    'recaptcha_token' => [
        'required' => 'La vérification de sécurité est requise.',
    ],
];
