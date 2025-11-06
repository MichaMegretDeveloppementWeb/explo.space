<?php

namespace App\Repositories\Admin\Tag;

use App\Contracts\Repositories\Admin\Tag\TagSelectionRepositoryInterface;
use App\Models\Tag;
use App\Models\TagTranslation;
use Illuminate\Database\Eloquent\Collection;

class TagSelectionRepository implements TagSelectionRepositoryInterface
{
    /**
     * Get all tags with their translations for all locales
     * Eager loads translations to avoid N+1 queries
     * Orders by the first translation's name for consistency
     */
    public function getAll(): Collection
    {
        return Tag::query()
            ->with(['translations' => function ($query) {
                $query->orderBy('locale');
            }])
            ->where('is_active', true)
            ->get()
            ->sortBy(function ($tag) {
                // Sort by first translation name (usually 'fr')
                $firstTranslation = $tag->translations->first();

                return $firstTranslation->name ?? '';
            })
            ->values();
    }

    /**
     * Get all published and active tags with translations for a specific locale
     * Used for loading all available tags in admin dropdowns
     */
    public function getPublishedActiveTagsForLocale(string $locale): Collection
    {
        return TagTranslation::query()
            ->select(['tag_translations.name', 'tag_translations.slug'])
            ->join('tags', 'tag_translations.tag_id', '=', 'tags.id')
            ->where('tag_translations.locale', $locale)
            ->where('tag_translations.status', 'published')
            ->where('tags.is_active', true)
            ->orderBy('tag_translations.name')
            ->get();
    }

    /**
     * Search tags by name in a specific locale
     * Used for tag autosuggestion in admin filters
     */
    public function searchByNameInLocale(string $query, string $locale, int $limit = 10): Collection
    {
        return TagTranslation::query()
            ->select(['tag_translations.name', 'tag_translations.slug'])
            ->join('tags', 'tag_translations.tag_id', '=', 'tags.id')
            ->where('tag_translations.locale', $locale)
            ->where('tag_translations.status', 'published')
            ->where('tags.is_active', true)
            ->where('tag_translations.name', 'LIKE', "%{$query}%")
            ->orderBy('tag_translations.name')
            ->limit($limit)
            ->get();
    }

    /**
     * Translate tag slugs from one locale to another
     * Used when switching locale in admin filters to preserve tag selection
     *
     * Algorithm:
     * 1. Find tag_ids from source locale slugs
     * 2. Find corresponding slugs in target locale for those tag_ids
     * 3. Return only slugs that have valid translations
     */
    public function translateSlugsToLocale(array $slugs, string $fromLocale, string $toLocale): array
    {
        if (empty($slugs)) {
            return [];
        }

        // Step 1: Find tag_ids from source locale
        $tagIds = TagTranslation::query()
            ->join('tags', 'tag_translations.tag_id', '=', 'tags.id')
            ->where('tag_translations.locale', $fromLocale)
            ->where('tag_translations.status', 'published')
            ->where('tags.is_active', true)
            ->whereIn('tag_translations.slug', $slugs)
            ->pluck('tag_translations.tag_id')
            ->toArray();

        if (empty($tagIds)) {
            return [];
        }

        // Step 2: Find corresponding slugs in target locale
        return TagTranslation::query()
            ->join('tags', 'tag_translations.tag_id', '=', 'tags.id')
            ->where('tag_translations.locale', $toLocale)
            ->where('tag_translations.status', 'published')
            ->where('tags.is_active', true)
            ->whereIn('tag_translations.tag_id', $tagIds)
            ->pluck('tag_translations.slug')
            ->toArray();
    }
}
