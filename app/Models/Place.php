<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
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
        'coordinates',
        'address',
        'is_featured',
        'admin_id',
        'request_id',
    ];

    protected function casts(): array
    {
        return [
            'latitude' => 'decimal:6', // Précision au mètre
            'longitude' => 'decimal:6', // Précision au mètre
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
     *
     * Note: Avoid using this method in loops or with eagerly loaded relations.
     * Prefer accessing $model->translations->first() to use already loaded data.
     */
    public function translate(string $locale): ?PlaceTranslation
    {
        return $this->translations->firstWhere('locale', $locale);
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

    /**
     * Scope pour recherche dans un rayon géographique
     * Utilise l'index spatial pour performance optimale
     */
    /**
     * @param  Builder<Place>  $query
     */
    public function scopeWithinRadius(Builder $query, float $latitude, float $longitude, float $radiusKm): void
    {
        $radiusMeters = $radiusKm * 1000;

        $query->whereRaw(
            'ST_Distance_Sphere(coordinates, POINT(?, ?)) <= ?',
            [$longitude, $latitude, $radiusMeters]
        );
    }

    /**
     * Scope pour recherche dans une bounding box
     * Plus efficace pour grandes zones
     *
     * @param  Builder<Place>  $query
     */
    public function scopeWithinBounds(Builder $query, float $swLat, float $swLng, float $neLat, float $neLng): void
    {
        $query->whereRaw(
            'ST_Within(coordinates, ST_GeomFromText(?))',
            ["POLYGON(($swLng $swLat, $neLng $swLat, $neLng $neLat, $swLng $neLat, $swLng $swLat))"]
        );
    }

    /**
     * Met à jour automatiquement la colonne coordinates
     * quand latitude/longitude changent
     */
    protected static function boot(): void
    {
        parent::boot();

        static::saving(function (Place $place) {
            // Auto-remplir la colonne coordinates POINT depuis latitude/longitude
            if ($place->latitude && $place->longitude) {
                $place->coordinates = \DB::raw("POINT({$place->longitude}, {$place->latitude})");
            }
        });
    }
}
