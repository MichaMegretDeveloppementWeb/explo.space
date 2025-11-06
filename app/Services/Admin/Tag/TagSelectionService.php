<?php

namespace App\Services\Admin\Tag;

use App\Contracts\Repositories\Admin\Tag\TagSelectionRepositoryInterface;

class TagSelectionService
{
    public function __construct(
        private readonly TagSelectionRepositoryInterface $repository
    ) {}

    /**
     * Get all available tags for selection in a given locale
     * Returns formatted array ready for frontend consumption
     *
     * @return array<int, array{slug: string, name: string}>
     */
    public function getAvailableTagsForLocale(string $locale): array
    {
        return $this->repository
            ->getPublishedActiveTagsForLocale($locale)
            ->map(fn ($tag) => [
                'slug' => $tag->slug,
                'name' => $tag->name,
            ])
            ->toArray();
    }

    /**
     * Search tags by name in a specific locale
     * Case insensitive, searches anywhere in the name
     *
     * @return array<int, array{slug: string, name: string}>
     */
    public function searchTagsByName(string $query, string $locale, int $limit = 10): array
    {
        $trimmedQuery = trim($query);

        return $this->repository
            ->searchByNameInLocale($trimmedQuery, $locale, $limit)
            ->map(fn ($tag) => [
                'slug' => $tag->slug,
                'name' => $tag->name,
            ])
            ->toArray();
    }

    /**
     * Translate tag slugs from one locale to another
     * Returns translated slugs, removes tags without translation
     *
     * @param  array<int, string>  $slugs
     * @return array<int, string>
     */
    public function translateTagSlugs(array $slugs, string $fromLocale, string $toLocale): array
    {
        if (empty($slugs)) {
            return [];
        }

        return $this->repository->translateSlugsToLocale($slugs, $fromLocale, $toLocale);
    }
}
