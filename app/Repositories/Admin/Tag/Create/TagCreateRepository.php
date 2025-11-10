<?php

namespace App\Repositories\Admin\Tag\Create;

use App\Contracts\Repositories\Admin\Tag\Create\TagCreateRepositoryInterface;
use App\Models\Tag;
use App\Models\TagTranslation;
use Illuminate\Support\Str;

class TagCreateRepository implements TagCreateRepositoryInterface
{
    /**
     * @param  array{color: string, is_active: bool}  $tagData
     */
    public function create(array $tagData): Tag
    {
        return Tag::create([
            'color' => $tagData['color'],
            'is_active' => $tagData['is_active'] ?? true,
        ]);
    }

    public function createTranslations(Tag $tag, array $translations): void
    {
        foreach ($translations as $locale => $translationData) {
            TagTranslation::create([
                'tag_id' => $tag->id,
                'locale' => $locale,
                'name' => $translationData['name'],
                'slug' => $translationData['slug'] ?? Str::slug($translationData['name']),
                'description' => $translationData['description'] ?? null,
                'status' => 'published', // Tags are always published
            ]);
        }
    }
}
