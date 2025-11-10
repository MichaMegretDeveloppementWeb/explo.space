<?php

namespace App\Repositories\Admin\Tag\Edit;

use App\Contracts\Repositories\Admin\Tag\Edit\TagUpdateRepositoryInterface;
use App\Models\Tag;
use App\Models\TagTranslation;
use Illuminate\Support\Str;

class TagUpdateRepository implements TagUpdateRepositoryInterface
{
    public function findForEdit(int $id): ?Tag
    {
        return Tag::with([
            'translations',
        ])->find($id);
    }

    /**
     * @param  array{color?: string, is_active?: bool}  $tagData
     */
    public function update(Tag $tag, array $tagData): bool
    {
        return $tag->update([
            'color' => $tagData['color'] ?? $tag->color,
            'is_active' => $tagData['is_active'] ?? $tag->is_active,
        ]);
    }

    public function updateTranslations(Tag $tag, array $translations): void
    {
        foreach ($translations as $locale => $translationData) {
            TagTranslation::updateOrCreate(
                [
                    'tag_id' => $tag->id,
                    'locale' => $locale,
                ],
                [
                    'name' => $translationData['name'],
                    'slug' => $translationData['slug'] ?? Str::slug($translationData['name']),
                    'description' => $translationData['description'] ?? null,
                    'status' => 'published', // Tags are always published
                ]
            );
        }
    }

    public function detachFromPlaces(Tag $tag): void
    {
        $tag->places()->detach();
    }

    public function delete(Tag $tag): bool
    {
        return $tag->delete();
    }
}
