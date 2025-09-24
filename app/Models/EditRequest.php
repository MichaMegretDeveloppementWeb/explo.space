<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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
        'suggested_changes',
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
            'suggested_changes' => 'array',
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

    public function isSubmitted(): bool
    {
        return $this->status === 'submitted';
    }

    public function isAccepted(): bool
    {
        return $this->status === 'accepted';
    }
}
