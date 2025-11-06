<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class PlaceTranslation extends Model
{
    /** @use HasFactory<\Database\Factories\PlaceTranslationFactory> */
    use HasFactory;

    protected $fillable = [
        'place_id',
        'locale',
        'title',
        'slug',
        'description',
        'practical_info',
        'status',
        'source_hash',
    ];

    protected function casts(): array
    {
        return [
            'status' => 'string',
        ];
    }

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function (PlaceTranslation $translation) {
            if (empty($translation->slug)) {
                $translation->slug = Str::slug($translation->title);
            }
        });

        static::updating(function (PlaceTranslation $translation) {
            if ($translation->isDirty('title') && empty($translation->getOriginal('slug'))) {
                $translation->slug = Str::slug($translation->title);
            }
        });
    }

    /**
     * @return BelongsTo<Place, $this>
     */
    public function place(): BelongsTo
    {
        return $this->belongsTo(Place::class);
    }

    /**
     * Scope for published translations
     *
     * @param  Builder<PlaceTranslation>  $query
     */
    public function scopePublished(Builder $query): void
    {
        $query->where('status', 'published');
    }

    /**
     * Scope for specific locale
     *
     * @param  Builder<PlaceTranslation>  $query
     */
    public function scopeForLocale(Builder $query, string $locale): void
    {
        $query->where('locale', $locale);
    }

    /**
     * Check if translation is published
     */
    public function isPublished(): bool
    {
        return $this->status === 'published';
    }

    /**
     * Check if translation is draft
     */
    public function isDraft(): bool
    {
        return $this->status === 'draft';
    }
}
