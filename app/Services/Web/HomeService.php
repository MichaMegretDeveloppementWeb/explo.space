<?php

namespace App\Services\Web;

use App\Models\EditRequest;
use App\Models\Place;
use App\Models\PlaceRequest;
use App\Models\Tag;
use App\Models\User;
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

    /**
     * Get all statistics (for hero and community sections)
     *
     * @return array{places_count: int, featured_places_count: int, active_tags_count: int, active_members: int, total_submissions: int}
     */
    public function getStats(): array
    {
        return [
            'places_count' => $this->getPlacesCount(),
            'featured_places_count' => $this->getFeaturedPlacesCount(),
            'active_tags_count' => $this->getActiveTagsCount(),
            'active_members' => $this->getActiveMembersCount(),
            'total_submissions' => $this->getTotalSubmissions(),
        ];
    }

    /**
     * Count total places
     */
    private function getPlacesCount(): int
    {
        return Place::count();
    }

    /**
     * Count active members (admins + unique emails from proposals)
     */
    private function getActiveMembersCount(): int
    {
        // Count admins
        $adminsCount = User::count();

        // Get unique emails from PlaceRequest
        $placeRequestEmails = PlaceRequest::select('contact_email')
            ->distinct()
            ->pluck('contact_email');

        // Get unique emails from EditRequest
        $editRequestEmails = EditRequest::select('contact_email')
            ->distinct()
            ->pluck('contact_email');

        // Merge and get unique emails
        $uniqueEmails = $placeRequestEmails->merge($editRequestEmails)->unique();

        return $adminsCount + $uniqueEmails->count();
    }

    /**
     * Count total place submissions (all PlaceRequests)
     */
    private function getTotalSubmissions(): int
    {
        return PlaceRequest::count();
    }

    /**
     * Count featured places (is_featured = true)
     */
    private function getFeaturedPlacesCount(): int
    {
        return Place::where('is_featured', true)->count();
    }

    /**
     * Count active tags (is_active = true)
     */
    private function getActiveTagsCount(): int
    {
        return Tag::where('is_active', true)->count();
    }
}
