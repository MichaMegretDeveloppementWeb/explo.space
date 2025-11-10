<?php

namespace App\Repositories\Admin\Tag;

use App\Contracts\Repositories\Admin\Tag\TagListRepositoryInterface;
use App\Models\Tag;
use Illuminate\Pagination\LengthAwarePaginator;

class TagListRepository implements TagListRepositoryInterface
{
    /**
     * Get paginated tags with filters and sorting
     *
     * @param  array{search: string, activeFilter: string, locale: string}  $filters
     * @param  array{column: string, direction: string}  $sorting
     * @return LengthAwarePaginator<int, \App\Models\Tag>
     */
    public function getPaginatedTags(array $filters, array $sorting, int $perPage): LengthAwarePaginator
    {
        $query = Tag::query()
            ->with([
                // Eager load only the translation for the selected locale
                'translations' => fn ($q) => $q->where('locale', $filters['locale'])
                    ->select('id', 'tag_id', 'locale', 'name', 'slug'),
            ])
            ->withCount('places'); // Count associated places

        // Apply search filter on tag name (in selected locale)
        if (! empty($filters['search'])) {
            $query->whereHas('translations', function ($q) use ($filters) {
                $q->where('name', 'like', '%'.$filters['search'].'%');
            });
        }

        // Apply active/inactive filter
        if ($filters['activeFilter'] === 'active') {
            $query->where('is_active', true);
        } elseif ($filters['activeFilter'] === 'inactive') {
            $query->where('is_active', false);
        }
        // 'all' = no filter

        // Apply sorting
        if ($sorting['column'] === 'name') {
            // Sort by translated name: join with translations table
            $query->join('tag_translations', function ($join) use ($filters) {
                $join->on('tags.id', '=', 'tag_translations.tag_id')
                    ->where('tag_translations.locale', '=', $filters['locale']);
            })
                ->orderBy('tag_translations.name', $sorting['direction'])
                ->select('tags.*'); // Avoid selecting translation columns
        } elseif ($sorting['column'] === 'places_count') {
            // Sort by places count (already loaded via withCount)
            $query->orderBy('places_count', $sorting['direction']);
        } else {
            // Sort by column on tags table directly
            $query->orderBy('tags.'.$sorting['column'], $sorting['direction']);
        }

        return $query->paginate($perPage);
    }
}
