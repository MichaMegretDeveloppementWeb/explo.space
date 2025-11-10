<?php

namespace App\Contracts\Repositories\Admin\Tag\Edit;

use App\Models\Tag;

interface TagUpdateRepositoryInterface
{
    /**
     * Find a tag with all relations for editing
     */
    public function findForEdit(int $id): ?Tag;

    /**
     * Update tag base data
     *
     * @param  array{color?: string, is_active?: bool}  $tagData
     */
    public function update(Tag $tag, array $tagData): bool;

    /**
     * Update or create translations for a tag
     *
     * @param  array<string, array{name: string, slug: string, description: ?string}>  $translations
     */
    public function updateTranslations(Tag $tag, array $translations): void;

    /**
     * Detach tag from all associated places
     * Used before deletion to avoid orphaned relations
     */
    public function detachFromPlaces(Tag $tag): void;

    /**
     * Delete a tag
     */
    public function delete(Tag $tag): bool;
}
