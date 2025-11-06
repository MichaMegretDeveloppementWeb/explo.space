<?php

namespace App\Enums;

enum RequestStatus: string
{
    case Submitted = 'submitted';
    case Pending = 'pending';
    case Accepted = 'accepted';
    case Refused = 'refused';

    /**
     * Retourne le label à afficher pour le statut
     */
    public function label(): string
    {
        return match ($this) {
            self::Submitted => 'Envoyé',
            self::Pending => 'Vue',
            self::Accepted => 'Accepté',
            self::Refused => 'Refusé',
        };
    }

    /**
     * Retourne les classes CSS pour le badge de statut
     */
    public function badgeClasses(): string
    {
        return match ($this) {
            self::Submitted => 'bg-yellow-50 text-yellow-800 border-yellow-200',
            self::Pending => 'bg-yellow-50 text-yellow-800 border-yellow-200',
            self::Accepted => 'bg-green-50 text-green-800 border-green-200',
            self::Refused => 'bg-red-50 text-red-800 border-red-200',
        };
    }

    /**
     * Retourne la couleur principale du statut (pour les cercles, etc.)
     */
    public function color(): string
    {
        return match ($this) {
            self::Submitted => 'yellow',
            self::Pending => 'yellow',
            self::Accepted => 'green',
            self::Refused => 'red',
        };
    }

    /**
     * Vérifie si le statut permet des actions de modération (acceptation)
     * Inclut Refused pour permettre la création d'un lieu même après refus
     */
    public function canBeModerated(): bool
    {
        return in_array($this, [self::Submitted, self::Pending, self::Refused]);
    }

    /**
     * Vérifie si la proposition peut être refusée
     * Exclut Refused pour éviter de refuser une proposition déjà refusée
     */
    public function canBeRefused(): bool
    {
        return in_array($this, [self::Submitted, self::Pending]);
    }

    /**
     * Vérifie si le statut est final (traité)
     */
    public function isFinal(): bool
    {
        return in_array($this, [self::Accepted, self::Refused]);
    }
}
