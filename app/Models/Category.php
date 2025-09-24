<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    /** @use HasFactory<\Database\Factories\CategoryFactory> */
    use HasFactory;

    // Catégories internes pour organisation admin uniquement
    // Non visible par les visiteurs (contrairement aux tags)

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
     * @return HasMany<CategoryTranslation, $this>
     */
    public function translations(): HasMany
    {
        return $this->hasMany(CategoryTranslation::class);
    }

    /**
     * Get translation for specific locale
     */
    public function translate(string $locale): ?CategoryTranslation
    {
        return $this->translations->firstWhere('locale', $locale);
    }

    /**
     * Get translation for current app locale
     */
    public function getTranslationAttribute(): ?CategoryTranslation
    {
        return $this->translate(app()->getLocale());
    }

    /**
     * @return BelongsToMany<Place, $this>
     */
    public function places(): BelongsToMany
    {
        return $this->belongsToMany(Place::class, 'place_category');
    }

    public function isActive(): bool
    {
        return $this->is_active;
    }
}
