<?php

namespace App\Repositories\Admin\Settings\AdminList;

use App\Contracts\Repositories\Admin\Settings\AdminList\AdminListRepositoryInterface;
use App\Models\User;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;

class AdminListRepository implements AdminListRepositoryInterface
{
    /**
     * Get paginated list of administrators with filters and sorting.
     *
     * @param  array{search: string}  $filters
     * @param  array{column: string, direction: string}  $sorting
     * @return LengthAwarePaginator<int, User>
     */
    public function getPaginatedAdmins(
        array $filters,
        array $sorting,
        int $perPage
    ): LengthAwarePaginator {
        $query = User::query()
            ->whereIn('role', ['admin', 'super_admin'])
            ->whereNot('id', Auth::user()->id);

        // Apply search filter
        if (! empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                    ->orWhere('email', 'LIKE', "%{$search}%");
            });
        }

        // Apply sorting
        $query->orderBy($sorting['column'], $sorting['direction']);

        // Apply pagination
        return $query->paginate($perPage);
    }
}
