<?php

namespace App\Services\Web\Tag;

use App\Contracts\Repositories\Web\Tag\TagSelectionRepositoryInterface;

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
     * Get tags by their slug list for a specific locale
     * Returns only existing and valid tags
     *
     * @param  array<int, string>  $slugs
     * @return array<int, array{slug: string, name: string}>
     */
    public function getTagsBySlugList(array $slugs, string $locale): array
    {
        if (empty($slugs)) {
            return [];
        }

        return $this->repository
            ->getBySlugListInLocale($slugs, $locale)
            ->map(fn ($tag) => [
                'slug' => $tag->slug,
                'name' => $tag->name,
            ])
            ->toArray();
    }

    /**
     * Validate and clean slugs array
     * Removes invalid slugs silently and returns only valid ones
     *
     * @param  array<int, string>  $slugs
     * @return array<int, string>
     */
    public function validateAndCleanSlugs(array $slugs, string $locale): array
    {
        if (empty($slugs)) {
            return [];
        }

        // Remove duplicates and empty values
        $cleanedSlugs = array_values(array_unique(array_filter($slugs, fn ($slug) => strlen($slug) > 0)));

        // Return only existing slugs
        return $this->repository->validateSlugsExistInLocale($cleanedSlugs, $locale);
    }

    /**
     * Build tags for URL parameter from selected tags array
     * Converts [{slug: 'nasa', name: 'NASA'}, ...] to 'nasa,spacex,observatory'
     *
     * @param  array<int, array{slug: string, name: string}>  $selectedTags
     */
    public function buildTagsUrlParameter(array $selectedTags): string
    {
        if (empty($selectedTags)) {
            return '';
        }

        $slugs = collect($selectedTags)->pluck('slug')->filter()->unique();

        return $slugs->implode(',');
    }

    /**
     * Parse tags URL parameter to slugs array
     * Converts 'nasa,spacex,observatory' to ['nasa', 'spacex', 'observatory']
     *
     * @return array<int, string>
     */
    public function parseTagsUrlParameter(string $tagsParameter): array
    {
        if (empty($tagsParameter)) {
            return [];
        }

        return array_filter(
            array_map('trim', explode(',', $tagsParameter)),
            fn ($slug) => strlen($slug) > 0
        );
    }
}
