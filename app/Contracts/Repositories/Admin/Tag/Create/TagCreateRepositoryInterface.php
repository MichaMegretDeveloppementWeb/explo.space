<?php

namespace App\Contracts\Repositories\Admin\Tag\Create;

use App\Models\Tag;

interface TagCreateRepositoryInterface
{
    /**
     * Create a new tag with base data
     *
     * @param  array{color: string, is_active: bool}  $tagData
     */
    public function create(array $tagData): Tag;

    /**
     * Create translations for a tag
     *
     * @param  array<string, array{name: string, slug: string, description: ?string}>  $translations
     */
    public function createTranslations(Tag $tag, array $translations): void;
}
