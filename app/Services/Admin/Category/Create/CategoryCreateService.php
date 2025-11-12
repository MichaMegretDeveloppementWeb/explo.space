<?php

namespace App\Services\Admin\Category\Create;

use App\Contracts\Repositories\Admin\Category\Create\CategoryCreateRepositoryInterface;
use App\Models\Category;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CategoryCreateService
{
    public function __construct(
        private CategoryCreateRepositoryInterface $repository
    ) {}

    /**
     * Create a new category
     *
     * @param  array<string, mixed>  $data
     *
     * @throws \Throwable
     */
    public function create(array $data): Category
    {
        return DB::transaction(function () use ($data) {
            $category = $this->repository->create([
                'name' => $data['name'],
                'slug' => $data['slug'],
                'description' => $data['description'] ?? null,
                'color' => strtoupper($data['color']),
                'is_active' => $data['is_active'] ?? true,
            ]);

            Log::info('Category created successfully', [
                'category_id' => $category->id,
                'admin_id' => auth()->id(),
            ]);

            return $category;
        });
    }
}
