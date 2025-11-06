<?php

namespace App\Livewire\Web\Place\Index\Traits;

use App\Services\Web\Tag\TagSelectionService;
use App\Support\Config\PlaceSearchConfig;
use Exception;

/**
 * Trait pour gérer la sélection et filtrage des tags
 */
trait HandlesTagFiltering
{
    public function updatedTagSearchQuery(): void
    {
        if (strlen(trim($this->tagSearchQuery)) >= 2) {
            $this->searchTags();
        } else {
            $this->filteredTags = $this->availableTags;
        }
    }

    /**
     * Select a tag by its slug
     */
    public function selectTag(string $tagSlug): void
    {
        // Check if tag is not already selected
        $alreadySelected = collect($this->selectedTags)->contains('slug', $tagSlug);

        if (! $alreadySelected) {
            $tag = collect($this->availableTags)->firstWhere('slug', $tagSlug);

            if ($tag) {
                $this->selectedTags[] = [
                    'slug' => $tag['slug'],
                    'name' => $tag['name'],
                ];

                $this->updateTagsSlugsFromSelection();
                $this->tagSearchQuery = '';
                $this->filteredTags = $this->availableTags;

                $this->emitFiltersChanged();
            }
        }
    }

    /**
     * Remove a tag by its slug
     */
    public function removeTag(string $tagSlug): void
    {
        $this->selectedTags = collect($this->selectedTags)
            ->reject(fn ($tag) => $tag['slug'] === $tagSlug)
            ->values()
            ->toArray();

        $this->updateTagsSlugsFromSelection();

        $this->emitFiltersChanged();
    }

    /**
     * Initialize tags system
     */
    private function initializeTags(): void
    {
        $this->loadAvailableTags();
        $this->rebuildSelectedTagsFromSlugs();
    }

    /**
     * Load all available tags for current locale
     */
    private function loadAvailableTags(): void
    {
        $this->tagsLoading = true;

        try {
            $tagService = app(TagSelectionService::class);
            $this->availableTags = $tagService->getAvailableTagsForLocale(app()->getLocale());
            $this->filteredTags = $this->availableTags;
        } catch (Exception $e) {
            \Log::error('Error loading available tags', [
                'error' => $e->getMessage(),
                'locale' => app()->getLocale(),
            ]);
            $this->availableTags = [];
            $this->filteredTags = [];
        }

        $this->tagsLoading = false;
    }

    /**
     * Rebuild selectedTags array from URL slugs parameter
     */
    private function rebuildSelectedTagsFromSlugs(): void
    {
        if (empty($this->selectedTagsSlugs)) {
            $this->selectedTags = [];

            return;
        }

        try {
            $tagService = app(TagSelectionService::class);
            $slugs = $tagService->parseTagsUrlParameter($this->selectedTagsSlugs);

            // Clean invalid slugs silently
            $validSlugs = $tagService->validateAndCleanSlugs($slugs, app()->getLocale());
            $this->selectedTags = $tagService->getTagsBySlugList($validSlugs, app()->getLocale());

            // Update URL if some slugs were invalid
            if (count($validSlugs) !== count($slugs)) {
                $this->selectedTagsSlugs = implode(',', $validSlugs);
            }
        } catch (Exception $e) {
            \Log::error('Error rebuilding selected tags from slugs', [
                'error' => $e->getMessage(),
                'slugs' => $this->selectedTagsSlugs,
            ]);
            $this->selectedTags = [];
            $this->selectedTagsSlugs = '';
        }
    }

    /**
     * Search tags by query
     */
    private function searchTags(): void
    {
        $query = trim($this->tagSearchQuery);

        if (strlen($query) < 2) {
            $this->filteredTags = $this->availableTags;

            return;
        }

        $this->filteredTags = collect($this->availableTags)
            ->filter(function ($tag) use ($query) {
                return stripos($tag['name'], $query) !== false;
            })
            ->take(PlaceSearchConfig::TAG_SEARCH_LIMIT) // Limiter pour performances
            ->values()
            ->toArray();
    }

    /**
     * Update tags slugs URL parameter from selected tags
     */
    private function updateTagsSlugsFromSelection(): void
    {
        $tagService = app(TagSelectionService::class);
        $this->selectedTagsSlugs = $tagService->buildTagsUrlParameter($this->selectedTags);
    }

    /**
     * Open mobile tag selector modal
     */
    public function openMobileTagSelector(): void
    {
        $this->showMobileTagSelector = true;
        $this->loadAvailableTags();
    }

    /**
     * Close mobile tag selector modal
     */
    public function closeMobileTagSelector(): void
    {
        $this->showMobileTagSelector = false;
        $this->tagSearchQuery = '';
    }

    /**
     * Apply tag filters and close modal
     */
    public function applyTagFilters(): void
    {
        $this->closeMobileTagSelector();
    }

    /**
     * Clear all selected tags
     */
    public function clearAllTags(): void
    {
        $this->selectedTags = [];
        $this->selectedTagsSlugs = '';

        $this->emitFiltersChanged();
    }
}
