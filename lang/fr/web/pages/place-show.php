<?php

return [
    // Sections
    'sections' => [
        'overview' => 'Vue d\'ensemble',
        'description' => 'Description',
        'practical_info' => 'Informations pratiques',
        'photos' => 'Photos',
        'location' => 'Localisation',
    ],

    // Métadonnées
    'metadata' => [
        'added_on' => 'Fiche ajoutée le',
        'last_updated' => 'Dernière modification le',
        'coordinates' => 'Coordonnées GPS',
        'address' => 'Adresse',
    ],

    // Tags
    'tags' => [
        'title' => 'Thématiques',
    ],

    // Actions
    'actions' => [
        'report_error' => 'Signaler une erreur',
        'suggest_edit' => 'Proposer une modification',
        'view_on_map' => 'Voir sur la carte',
        'share' => 'Partager',
    ],

    // Formulaire de demande de modification/signalement
    'edit_request' => [
        'button' => 'Signaler une erreur ou proposer une modification',
        'title' => 'Signaler une erreur ou proposer une modification',

        // Type de demande
        'type_label' => 'Type de demande',
        'type_signalement' => 'Signaler une erreur (information inexacte, lieu fermé, etc.)',
        'type_modification' => 'Proposer une modification (améliorer les informations)',

        // Sélection des champs à modifier
        'select_fields' => 'Quels champs souhaitez-vous modifier ?',
        'field_title' => 'Titre',
        'field_description' => 'Description',
        'field_coordinates' => 'Coordonnées GPS',
        'field_address' => 'Adresse',
        'field_practical_info' => 'Informations pratiques',

        // Comparaison valeurs
        'current_value' => 'Valeur actuelle',
        'new_value_placeholder' => 'Nouvelle valeur proposée',

        // Description/Commentaire
        'description_label_signalement' => 'Décrivez le problème constaté',
        'description_label_modification' => 'Commentaire sur vos modifications (optionnel)',
        'description_placeholder' => 'Décrivez en détail les modifications ou le problème constaté...',
        'description_placeholder_signalement' => 'Décrivez en détail le problème que vous avez constaté...',
        'description_placeholder_modification' => 'Ajoutez un commentaire pour expliquer vos modifications (optionnel)...',

        // Email de contact
        'contact_email_label' => 'Votre email de contact',
        'contact_email_placeholder' => 'votre@email.com',

        // Actions
        'submit' => 'Envoyer la demande',
        'submitting' => 'Envoi en cours...',
        'cancel' => 'Annuler',

        // Messages
        'success' => 'Votre demande a été envoyée avec succès. Nous l\'examinerons dans les plus brefs délais.',
        'error' => 'Une erreur est survenue lors de l\'envoi de votre demande. Veuillez réessayer.',
    ],

    // Photos
    'photos' => [
        'main_photo' => 'Photo principale',
        'gallery' => 'Galerie photos',
        'view_fullscreen' => 'Voir en plein écran',
        'previous' => 'Photo précédente',
        'next' => 'Photo suivante',
        'close' => 'Fermer',
        'counter' => ':current sur :total',
    ],

    // Carte
    'map' => [
        'title' => 'Localisation',
        'view_larger' => 'Voir en grand',
        'open_in_maps' => 'Ouvrir dans Maps',
    ],

    // Erreurs
    'errors' => [
        'not_found' => 'Ce lieu n\'existe pas ou n\'est plus disponible.',
        'translation_missing' => 'Cette page n\'est pas disponible dans votre langue.',
    ],

    // Photo suggestion form
    'photo_suggestion' => [
        'button' => 'Suggérer de nouvelles photos',
        'title' => 'Suggérer de nouvelles photos',
        'subtitle' => 'Partagez vos photos de ce lieu pour enrichir la fiche',
        'photos_label' => 'Photos',
        'upload_button' => 'Télécharger des photos',
        'drag_drop' => 'ou glisser-déposer',
        'file_requirements' => 'JPEG, PNG, WebP jusqu\'à 10 Mo (max 10 photos)',
        'selected_photos' => 'Photos sélectionnées',
        'validating' => 'Validation des photos en cours...',
        'contact_email' => 'Votre email de contact',
        'email_placeholder' => 'votre@email.com',
        'cancel' => 'Annuler',
        'submit' => 'Envoyer',
        'submitting' => 'Envoi en cours...',
        'success' => 'Vos photos ont été envoyées avec succès. Nous les examinerons dans les plus brefs délais.',
    ],

    // SEO - Mots-clés génériques
    'seo' => [
        'keywords' => [
            'exploration spatiale',
            'conquête spatiale',
            'tourisme spatial',
        ],
    ],
];
