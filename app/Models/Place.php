<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Place extends Model
{
    /** @use HasFactory<\Database\Factories\PlaceFactory> */
    use HasFactory;

    // is_featured = true : lieu affiché dans "références à la une" de la page d'accueil
    // admin_id : administrateur qui a créé ou validé ce lieu
    // request_id : PlaceRequest d'origine si le lieu provient d'une demande utilisateur

    protected $fillable = [
        'latitude',
        'longitude',
        'address',
        'is_featured',
        'admin_id',
        'request_id',
    ];

    protected function casts(): array
    {
        return [
            'latitude' => 'decimal:7',
            'longitude' => 'decimal:7',
            'is_featured' => 'boolean',
        ];
    }

    /**
     * @return HasMany<PlaceTranslation, $this>
     */
    public function translations(): HasMany
    {
        return $this->hasMany(PlaceTranslation::class);
    }

    /**
     * Get translation for specific locale
     */
    public function translate(string $locale): ?PlaceTranslation
    {
        return $this->translations->firstWhere('locale', $locale);
    }

    /**
     * Get translation for current app locale
     */
    public function getTranslationAttribute(): ?PlaceTranslation
    {
        return $this->translate(app()->getLocale());
    }

    /**
     * @return BelongsToMany<Tag, $this>
     */
    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class, 'place_tag');
    }

    /**
     * @return BelongsToMany<Category, $this>
     */
    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class, 'place_category');
    }

    /**
     * @return HasMany<Photo, $this>
     */
    public function photos(): HasMany
    {
        return $this->hasMany(Photo::class);
    }

    /**
     * @return HasOne<Photo, $this>
     */
    public function mainPhoto(): HasOne
    {
        return $this->hasOne(Photo::class)->where('is_main', true);
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function admin(): BelongsTo
    {
        return $this->belongsTo(User::class, 'admin_id');
    }

    /**
     * @return BelongsTo<PlaceRequest, $this>
     */
    public function placeRequest(): BelongsTo
    {
        return $this->belongsTo(PlaceRequest::class, 'request_id');
    }
}
