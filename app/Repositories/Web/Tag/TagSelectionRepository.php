<?php

namespace App\Repositories\Web\Tag;

use App\Contracts\Repositories\Web\Tag\TagSelectionRepositoryInterface;
use App\Models\TagTranslation;
use Illuminate\Database\Eloquent\Collection;

class TagSelectionRepository implements TagSelectionRepositoryInterface
{
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

    public function getBySlugListInLocale(array $slugs, string $locale): Collection
    {
        if (empty($slugs)) {
            return new Collection;
        }

        return TagTranslation::query()
            ->select(['tag_translations.name', 'tag_translations.slug'])
            ->join('tags', 'tag_translations.tag_id', '=', 'tags.id')
            ->where('tag_translations.locale', $locale)
            ->where('tag_translations.status', 'published')
            ->where('tags.is_active', true)
            ->whereIn('tag_translations.slug', $slugs)
            ->orderBy('tag_translations.name')
            ->get();
    }

    public function validateSlugsExistInLocale(array $slugs, string $locale): array
    {
        if (empty($slugs)) {
            return [];
        }

        return TagTranslation::query()
            ->join('tags', 'tag_translations.tag_id', '=', 'tags.id')
            ->where('tag_translations.locale', $locale)
            ->where('tag_translations.status', 'published')
            ->where('tags.is_active', true)
            ->whereIn('tag_translations.slug', $slugs)
            ->pluck('tag_translations.slug')
            ->toArray();
    }
}
