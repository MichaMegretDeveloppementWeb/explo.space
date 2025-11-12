<?php

namespace App\Repositories\Admin\Settings\User;

use App\Contracts\Repositories\Admin\Settings\User\UserManagementRepositoryInterface;
use App\Models\User;
use Illuminate\Support\Collection;

class UserManagementRepository implements UserManagementRepositoryInterface
{
    /**
     * Find a user by ID.
     */
    public function findById(int $id): ?User
    {
        return User::find($id);
    }

    /**
     * Find a user by email.
     */
    public function findByEmail(string $email): ?User
    {
        return User::where('email', $email)->first();
    }

    /**
     * Check if a user exists with the given email (excluding specific user ID).
     */
    public function existsWithEmail(string $email, ?int $excludeUserId = null): bool
    {
        $query = User::where('email', $email);

        if ($excludeUserId !== null) {
            $query->where('id', '!=', $excludeUserId);
        }

        return $query->exists();
    }

    /**
     * Create a new user.
     *
     * @param  array<string, mixed>  $data
     */
    public function create(array $data): User
    {
        return User::create($data);
    }

    /**
     * Update an existing user.
     *
     * @param  array<string, mixed>  $data
     */
    public function update(User $user, array $data): User
    {
        $user->update($data);

        return $user->fresh();
    }

    /**
     * Get all admin users (admin + super_admin).
     *
     * @return Collection<int, User>
     */
    public function getAllAdmins(): Collection
    {
        return User::whereIn('role', ['admin', 'super_admin'])
            ->orderBy('name')
            ->get();
    }
}
