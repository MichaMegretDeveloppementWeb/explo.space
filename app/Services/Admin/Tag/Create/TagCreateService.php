<?php

namespace App\Services\Admin\Tag\Create;

use App\Contracts\Repositories\Admin\Tag\Create\TagCreateRepositoryInterface;
use App\Models\Tag;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TagCreateService
{
    public function __construct(
        private TagCreateRepositoryInterface $repository
    ) {}

    /**
     * Create a new tag with translations.
     *
     * @param  array<string, mixed>  $data
     *
     * @throws \Throwable
     */
    public function create(array $data): Tag
    {
        return DB::transaction(function () use ($data) {
            // Create base tag
            $tag = $this->repository->create([
                'color' => $data['color'],
                'is_active' => $data['is_active'] ?? true,
            ]);

            // Create translations
            $this->repository->createTranslations($tag, $data['translations']);

            Log::info('Tag created successfully', [
                'tag_id' => $tag->id,
                'admin_id' => auth()->id(),
            ]);

            return $tag->load('translations');
        });
    }
}
