<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Validation - Demande de modification/signalement
    |--------------------------------------------------------------------------
    |
    | Messages de validation traduits pour le formulaire de demande de
    | modification ou de signalement d'erreur sur un lieu.
    |
    */

    // Champs généraux
    'type' => [
        'required' => 'Le type de demande est requis.',
        'in' => 'Le type de demande doit être un signalement ou une modification.',
    ],

    'description' => [
        'required' => 'La description est requise.',
        'min' => 'La description doit contenir au moins :min caractères.',
        'max' => 'La description ne peut pas dépasser :max caractères.',
    ],

    'contact_email' => [
        'required' => "L'email de contact est requis.",
        'email' => "L'email de contact doit être une adresse email valide.",
        'max' => "L'email de contact ne peut pas dépasser :max caractères.",
    ],

    // Champs sélectionnés pour modification
    'selected_fields' => [
        'required' => 'Veuillez sélectionner au moins un champ à modifier.',
        'min' => 'Veuillez sélectionner au moins un champ à modifier.',
        'array' => 'Les champs sélectionnés doivent être une liste.',
        'in' => 'Le champ sélectionné est invalide.',
    ],

    // Nouvelles valeurs
    'new_values' => [
        'title' => [
            'required' => 'Le nouveau titre est requis si vous modifiez ce champ.',
            'max' => 'Le titre ne peut pas dépasser :max caractères.',
        ],
        'description' => [
            'required' => 'La nouvelle description est requise si vous modifiez ce champ.',
            'max' => 'La description ne peut pas dépasser :max caractères.',
        ],
        'address' => [
            'required' => 'La nouvelle adresse est requise si vous modifiez ce champ.',
            'max' => "L'adresse ne peut pas dépasser :max caractères.",
        ],
        'practical_info' => [
            'required' => 'Les nouvelles informations pratiques sont requises si vous modifiez ce champ.',
            'max' => 'Les informations pratiques ne peuvent pas dépasser :max caractères.',
        ],
        'coordinates' => [
            'lat' => [
                'required' => 'La latitude est requise si vous modifiez les coordonnées.',
                'numeric' => 'La latitude doit être un nombre.',
                'between' => 'La latitude doit être comprise entre :min et :max degrés.',
            ],
            'lng' => [
                'required' => 'La longitude est requise si vous modifiez les coordonnées.',
                'numeric' => 'La longitude doit être un nombre.',
                'between' => 'La longitude doit être comprise entre :min et :max degrés.',
            ],
        ],
    ],

    // reCAPTCHA
    'recaptcha' => [
        'required' => 'La validation anti-robot est requise.',
        'failed' => 'La validation anti-robot a échoué. Veuillez réessayer.',
    ],
];
