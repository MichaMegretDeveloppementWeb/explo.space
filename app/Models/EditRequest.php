<?php

namespace App\Models;

use App\Enums\RequestStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property string|null $description_translation
 * @property array<int, array<string, mixed>>|null $suggested_changes
 */
class EditRequest extends Model
{
    /** @use HasFactory<\Database\Factories\EditRequestFactory> */
    use HasFactory;

    protected $fillable = [
        'contact_email',
        'detected_language',
        'place_id',
        'type',
        'description',
        'description_translation',
        'suggested_changes',
        'applied_changes',
        'status',
        'admin_reason',
        'viewed_by_admin_id',
        'processed_by_admin_id',
        'viewed_at',
        'processed_at',
    ];

    protected function casts(): array
    {
        return [
            'status' => RequestStatus::class,
            'suggested_changes' => 'array',
            'applied_changes' => 'array',
            'viewed_at' => 'datetime',
            'processed_at' => 'datetime',
        ];
    }

    /**
     * @return BelongsTo<Place, $this>
     */
    public function place(): BelongsTo
    {
        return $this->belongsTo(Place::class);
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function viewedByAdmin(): BelongsTo
    {
        return $this->belongsTo(User::class, 'viewed_by_admin_id');
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function processedByAdmin(): BelongsTo
    {
        return $this->belongsTo(User::class, 'processed_by_admin_id');
    }

    public function isModification(): bool
    {
        return $this->type === 'modification';
    }

    public function isSignalement(): bool
    {
        return $this->type === 'signalement';
    }

    public function isPhotoSuggestion(): bool
    {
        return $this->type === 'photo_suggestion';
    }

    public function isSubmitted(): bool
    {
        return $this->status === RequestStatus::Submitted;
    }

    public function isPending(): bool
    {
        return $this->status === RequestStatus::Pending;
    }

    public function isAccepted(): bool
    {
        return $this->status === RequestStatus::Accepted;
    }

    public function isRefused(): bool
    {
        return $this->status === RequestStatus::Refused;
    }

    /**
     * Retourne le label du type de demande
     */
    public function getTypeLabel(): string
    {
        return match ($this->type) {
            'signalement' => 'Signalement',
            'modification' => 'Proposition de modification',
            'photo_suggestion' => 'Proposition de photos',
            default => 'Demande',
        };
    }

    /**
     * Accessor pour proposed_data (pour les modifications)
     * Retourne les champs proposés depuis suggested_changes
     *
     * @return array<string, mixed>|null
     */
    public function getProposedDataAttribute(): ?array
    {
        if ($this->type !== 'modification') {
            return null;
        }

        /** @var array<string, mixed>|null */
        return $this->suggested_changes ?? null;
    }

    /**
     * Accessor pour suggested_photo_paths (pour les photo_suggestions)
     * Retourne les chemins des photos depuis suggested_changes
     *
     * @return array<int, string>|null
     */
    public function getSuggestedPhotoPathsAttribute(): ?array
    {
        if ($this->type !== 'photo_suggestion') {
            return null;
        }

        /** @var array<int, string>|null */
        return $this->suggested_changes ?? null;
    }

    /**
     * Accessor pour details (pour les signalements)
     * Retourne les détails du signalement depuis description
     */
    public function getDetailsAttribute(): ?string
    {
        if ($this->type !== 'signalement') {
            return null;
        }

        return $this->description;
    }

    /**
     * Accessor pour proposed_latitude
     * Extrait la latitude depuis suggested_changes[coordinates][new_value][lat]
     */
    public function getProposedLatitudeAttribute(): ?float
    {
        $coordinatesField = collect($this->suggested_changes ?? [])
            ->firstWhere('field', 'coordinates');

        if ($coordinatesField && isset($coordinatesField['new_value']['lat'])) {
            return (float) $coordinatesField['new_value']['lat'];
        }

        return null;
    }

    /**
     * Accessor pour proposed_longitude
     * Extrait la longitude depuis suggested_changes[coordinates][new_value][lng]
     */
    public function getProposedLongitudeAttribute(): ?float
    {
        $coordinatesField = collect($this->suggested_changes ?? [])
            ->firstWhere('field', 'coordinates');

        if ($coordinatesField && isset($coordinatesField['new_value']['lng'])) {
            return (float) $coordinatesField['new_value']['lng'];
        }

        return null;
    }
}
