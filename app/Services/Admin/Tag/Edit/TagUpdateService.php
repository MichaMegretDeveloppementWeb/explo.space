<?php

namespace App\Services\Admin\Tag\Edit;

use App\Contracts\Repositories\Admin\Tag\Edit\TagUpdateRepositoryInterface;
use App\Exceptions\Admin\Tag\TagNotFoundException;
use App\Models\Tag;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TagUpdateService
{
    public function __construct(
        private TagUpdateRepositoryInterface $repository
    ) {}

    /**
     * Load a tag for editing with all relations.
     */
    public function loadForEdit(int $tagId): ?Tag
    {
        return $this->repository->findForEdit($tagId);
    }

    /**
     * Update an existing tag with translations.
     *
     * @param  array<string, mixed>  $data
     *
     * @throws TagNotFoundException|\Throwable
     */
    public function update(int $tagId, array $data): Tag
    {
        return DB::transaction(function () use ($tagId, $data) {
            $tag = $this->repository->findForEdit($tagId);

            if (! $tag) {
                throw new TagNotFoundException;
            }

            // Update base tag data
            $this->repository->update($tag, [
                'color' => $data['color'],
                'is_active' => $data['is_active'] ?? true,
            ]);

            // Update translations
            $this->repository->updateTranslations($tag, $data['translations']);

            Log::info('Tag updated successfully', [
                'tag_id' => $tag->id,
                'admin_id' => auth()->id(),
            ]);

            return $tag->fresh(['translations']);
        });
    }

    /**
     * Delete a tag and detach it from all associated places.
     *
     * @throws TagNotFoundException|\Throwable
     */
    public function delete(int $tagId): bool
    {
        return DB::transaction(function () use ($tagId) {
            $tag = $this->repository->findForEdit($tagId);

            if (! $tag) {
                throw new TagNotFoundException;
            }

            // Count associated places for logging
            $associatedPlacesCount = $tag->places()->count();

            // Detach from all places first
            $this->repository->detachFromPlaces($tag);

            // Delete the tag (translations will be cascade deleted)
            $deleted = $this->repository->delete($tag);

            if ($deleted) {
                Log::info('Tag deleted successfully', [
                    'tag_id' => $tagId,
                    'admin_id' => auth()->id(),
                    'detached_places_count' => $associatedPlacesCount,
                ]);
            }

            return $deleted;
        });
    }
}
