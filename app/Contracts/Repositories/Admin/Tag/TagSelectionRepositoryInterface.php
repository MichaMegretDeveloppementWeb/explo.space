<?php

namespace App\Contracts\Repositories\Admin\Tag;

use Illuminate\Database\Eloquent\Collection;

interface TagSelectionRepositoryInterface
{
    /**
     * Get all tags with their translations for all locales
     * Used in admin forms to populate tag selection dropdowns
     * Returns tags with eager-loaded translations
     *
     * @return Collection<int, \App\Models\Tag>
     */
    public function getAll(): Collection;

    /**
     * Get all published and active tags with translations for a specific locale
     * Optimized with minimal data selection for dropdown performance
     *
     * @return Collection<int, \App\Models\TagTranslation>
     */
    public function getPublishedActiveTagsForLocale(string $locale): Collection;

    /**
     * Search tags by name in a specific locale
     * Case insensitive, searches anywhere in the name
     *
     * @return Collection<int, \App\Models\TagTranslation>
     */
    public function searchByNameInLocale(string $query, string $locale, int $limit = 10): Collection;

    /**
     * Translate tag slugs from one locale to another
     * Returns translated slugs for existing tags, removes tags without translation
     *
     * @param  array<int, string>  $slugs
     * @return array<int, string>
     */
    public function translateSlugsToLocale(array $slugs, string $fromLocale, string $toLocale): array;
}
