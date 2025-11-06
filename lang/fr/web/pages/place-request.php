<?php

return [
    // Titre principal de la page
    'title' => 'Proposer un lieu spatial',
    'subtitle' => 'Partagez un lieu lié à la conquête spatiale ou à la découverte de l\'univers',

    // Description
    'description' => 'Vous connaissez un site, musée, observatoire ou autre lieu dédié à l\'espace ? Proposez-le à notre communauté ! Votre contribution sera examinée par notre équipe avant publication.',

    // Badges informatifs
    'badges' => [
        'team_review' => 'Examen par l\'équipe',
        'email_notification' => 'Notification par email',
        'community_contribution' => 'Contribution à la communauté',
    ],

    // Formulaire - Informations de contact
    'contact' => [
        'title' => 'Vos coordonnées',
        'email' => 'Votre adresse email',
        'email_placeholder' => 'votre@email.com',
        'email_help' => 'Nous vous contacterons uniquement pour vous informer du statut de votre proposition.',
    ],

    // Formulaire - Localisation
    'location' => [
        'title' => 'Localisation du lieu',
        'search' => 'Rechercher une adresse',
        'search_placeholder' => 'Rechercher une adresse...',
        'search_help' => 'Saisissez au moins 3 caractères pour voir les suggestions',
        'no_results' => 'Aucune adresse trouvée',
        'address_validated' => 'Adresse validée',
        'interactive_map' => 'Carte interactive',
        'map_help' => 'Cliquez sur la carte pour définir les coordonnées, ou déplacez le marqueur',
        'latitude' => 'Latitude',
        'longitude' => 'Longitude',
        'address_field' => 'Adresse du lieu',
        'address_placeholder' => 'Adresse complète',
        'address_help' => 'Adresse qui sera enregistrée et visible par les utilisateurs',
        'manual_entry_hint' => 'Renseigner manuellement',
        'optional' => 'Optionnel',
    ],

    // Formulaire - Informations sur le lieu
    'place_info' => [
        'title' => 'Informations sur le lieu',
        'name' => 'Nom du lieu',
        'name_placeholder' => 'Ex: Centre spatial Kennedy',
        'description' => 'Description',
        'description_placeholder' => 'Décrivez le lieu, son histoire, ses particularités...',
        'description_help' => 'Soyez le plus précis possible pour aider notre équipe à valider votre proposition',
        'practical_info' => 'Informations pratiques',
        'practical_info_placeholder' => 'Horaires, tarifs, accès, conditions de visite...',
        'practical_info_help' => 'Ces informations aideront les visiteurs à planifier leur visite',
    ],

    // Formulaire - Photos
    'photos' => [
        'title' => 'Photos du lieu',
        'upload' => 'Télécharger des photos',
        'or_drag_drop' => 'ou glisser-déposer',
        'formats_help' => 'JPEG, PNG, WebP jusqu\'à :max_size_mb Mo (max :max_files photos)',
        'cumulative_limit' => 'Limites cumulées',
        'mb_total' => 'Mo total',
        'error_file_too_large' => 'Le fichier ":filename" (:size Mo) dépasse la taille limite autorisée.',
        'error_total_too_large' => 'La taille totale des fichiers (:size Mo) dépasse la taille limite cumulée autorisée. Veuillez sélectionner moins de fichiers ou des fichiers plus petits.',
        'preview' => 'Photos sélectionnées',
        'selected_photo' => 'Photo sélectionnée',
        'main_auto' => 'Principale (auto)',
        'remove_photo' => 'Retirer cette photo',
        'tips_title' => 'Conseils pour les photos',
        'tip_quality' => 'Privilégiez des photos de haute qualité et bien cadrées',
        'tip_formats' => 'Formats acceptés : JPEG, PNG, WebP (pas de SVG)',
        'uploading' => 'Validation des photos en cours...',
        'optional' => 'Optionnel',
    ],

    // Messages de validation
    'validation' => [
        // Contact
        'email_required' => 'Votre adresse email est obligatoire.',
        'email_valid' => 'L\'adresse email doit être valide.',
        'email_max' => 'L\'adresse email ne peut pas dépasser :max caractères.',

        // Place information
        'title_required' => 'Le nom du lieu est obligatoire.',
        'title_min' => 'Le nom du lieu doit contenir au moins :min caractères.',
        'title_max' => 'Le nom du lieu ne peut pas dépasser :max caractères.',
        'description_max' => 'La description ne peut pas dépasser :max caractères.',
        'practical_info_max' => 'Les informations pratiques ne peuvent pas dépasser :max caractères.',

        // Location
        'latitude_numeric' => 'La latitude doit être un nombre.',
        'latitude_between' => 'La latitude doit être comprise entre :min et :max.',
        'longitude_numeric' => 'La longitude doit être un nombre.',
        'longitude_between' => 'La longitude doit être comprise entre :min et :max.',
        'address_max' => 'L\'adresse ne peut pas dépasser :max caractères.',

        // Photos
        'photos_max' => 'Vous ne pouvez ajouter que :max photos maximum.',
        'photo_size' => 'Chaque photo ne peut pas dépasser :max Mo.',
        'photo_format' => 'Les photos doivent être au format JPEG, PNG ou WebP.',

        // reCAPTCHA
        'recaptcha_required' => 'La vérification de sécurité est obligatoire. Veuillez réessayer.',
    ],

    // Boutons d'action
    'actions' => [
        'cancel' => 'Annuler',
        'submit' => 'Soumettre ma proposition',
        'submitting' => 'Envoi en cours...',
    ],

    // Messages de succès/erreur
    'messages' => [
        'success' => 'Merci ! Votre proposition a été envoyée avec succès. Notre équipe va l\'examiner et vous tiendra informé par email.',
        'error' => 'Une erreur s\'est produite lors de l\'envoi de votre proposition. Veuillez réessayer.',
        'recaptcha_error' => 'Une erreur est survenue avec la vérification de sécurité. Veuillez réessayer.',
        'photos_limit_exceeded' => 'Vous ne pouvez pas avoir plus de :max photos au total. Vous avez déjà :current photo(s).',
        'photos_validated' => ':count photo(s) validée(s)',
        'photos_unexpected_error' => 'Une erreur inattendue est survenue lors du traitement des photos. Veuillez réessayer.',
    ],

    // Section SEO
    'seo' => [
        'title' => 'Proposer un lieu spatial',
        'description' => "Partagez un lieu lié à la conquête spatiale avec notre communauté. Proposez un site, musée, observatoire ou tout autre lieu dédié à l'exploration spatiale.",
        'keywords' => 'proposer lieu spatial, ajouter lieu spatial, contribution, musée espace, observatoire, site spatial',

        'og' => [
            'type' => 'website',
            'image_alt' => 'Proposer un lieu spatial sur Explo.space',
        ],

        'twitter' => [
            'card' => 'summary_large_image',
        ],
    ],
];
