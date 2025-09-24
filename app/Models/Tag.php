<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Tag extends Model
{
    /** @use HasFactory<\Database\Factories\TagFactory> */
    use HasFactory;

    protected $fillable = [
        'color',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    /**
     * @return HasMany<TagTranslation, $this>
     */
    public function translations(): HasMany
    {
        return $this->hasMany(TagTranslation::class);
    }

    /**
     * Get translation for specific locale
     */
    public function translate(string $locale): ?TagTranslation
    {
        return $this->translations->firstWhere('locale', $locale);
    }

    /**
     * Get translation for current app locale
     */
    public function getTranslationAttribute(): ?TagTranslation
    {
        return $this->translate(app()->getLocale());
    }

    public function isActive(): bool
    {
        return $this->is_active;
    }

    /**
     * @return BelongsToMany<Place, $this>
     */
    public function places(): BelongsToMany
    {
        return $this->belongsToMany(Place::class, 'place_tag');
    }
}
