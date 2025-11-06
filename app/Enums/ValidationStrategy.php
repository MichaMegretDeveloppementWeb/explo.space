<?php

namespace App\Enums;

/**
 * Stratégies de validation des filtres
 *
 * Définit le comportement à adopter lors de la validation des filtres :
 * - THROW : Lance une exception immédiatement (pour interactions utilisateur)
 * - CORRECT_SILENTLY : Corrige les valeurs invalides sans lever d'exception (pour init URL)
 * - COLLECT_ERRORS : Collecte toutes les erreurs sans throw (pour validation batch)
 */
enum ValidationStrategy: string
{
    /**
     * Lance une exception dès la première erreur détectée
     * Usage : PlaceFilters, PlaceMap, PlaceList (interactions utilisateur)
     */
    case THROW = 'throw';

    /**
     * Corrige silencieusement les erreurs et log pour debug
     * Usage : PlaceExplorer (initialisation depuis URL)
     */
    case CORRECT_SILENTLY = 'correct_silently';

    /**
     * Collecte toutes les erreurs sans lever d'exception
     * Usage : Validation batch, rapports de validation
     */
    case COLLECT_ERRORS = 'collect_errors';
}
