<?php

namespace App\Contracts\Repositories\Web\Tag;

use Illuminate\Database\Eloquent\Collection;

interface TagSelectionRepositoryInterface
{
    /**
     * Get all published and active tags with translations for a specific locale
     * Optimized with minimal data selection for dropdown performance
     *
     * @return Collection<int, \App\Models\TagTranslation>
     */
    public function getPublishedActiveTagsForLocale(string $locale): Collection;

    /**
     * Get tags by their slug list for a specific locale
     * Returns only existing tags, invalid slugs are silently ignored
     *
     * @param  array<int, string>  $slugs
     * @return Collection<int, \App\Models\TagTranslation>
     */
    public function getBySlugListInLocale(array $slugs, string $locale): Collection;

    /**
     * Validate that slugs exist for a specific locale
     * Returns only valid slugs, used for URL parameter cleaning
     *
     * @param  array<int, string>  $slugs
     * @return array<int, string>
     */
    public function validateSlugsExistInLocale(array $slugs, string $locale): array;
}
