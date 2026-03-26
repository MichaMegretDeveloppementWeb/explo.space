<?php

namespace App\Enums;

enum AutofillWorkflowStatus: string
{
    case Pending = 'pending';
    case Discovering = 'discovering';
    case AwaitingSelection = 'awaiting_selection';
    case Enriching = 'enriching';
    case AwaitingImages = 'awaiting_images';
    case Translating = 'translating';
    case Saving = 'saving';
    case Completed = 'completed';

    public function label(): string
    {
        return match ($this) {
            self::Pending => 'En attente',
            self::Discovering => 'Recherche en cours',
            self::AwaitingSelection => 'Sélection requise',
            self::Enriching => 'Enrichissement en cours',
            self::AwaitingImages => 'Sélection images requise',
            self::Translating => 'Traduction en cours',
            self::Saving => 'Enregistrement en cours',
            self::Completed => 'Terminé',
        };
    }

    /**
     * Pipeline is at a checkpoint where user input is needed.
     */
    public function isAwaiting(): bool
    {
        return in_array($this, [self::AwaitingSelection, self::AwaitingImages]);
    }
}
