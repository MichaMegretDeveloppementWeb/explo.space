<?php

namespace App\Services\Web;

use App\Models\Place;
use Illuminate\Database\Eloquent\Collection;

class HomeService
{
    /**
     * @return Collection<int, Place>
     */
    public function getFeaturedPlaces(): Collection
    {
        return Place::query()->where('is_featured', true)
            ->with([
                'translations' => function ($query) {
                    $query->where('locale', app()->getLocale())
                        ->where('status', 'published');
                },
                'photos' => function ($query) {
                    $query->where('is_main', true);
                },
                'tags.translations' => function ($query) {
                    $query->where('locale', app()->getLocale())
                        ->where('status', 'published');
                },
            ])
            ->whereHas('translations', function ($query) {
                $query->where('locale', app()->getLocale());
            })
            ->orderBy('created_at', 'desc')
            ->limit(3)
            ->get();
    }
}
