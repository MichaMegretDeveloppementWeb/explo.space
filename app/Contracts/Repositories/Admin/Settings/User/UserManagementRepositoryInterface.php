<?php

namespace App\Contracts\Repositories\Admin\Settings\User;

use App\Models\User;
use Illuminate\Support\Collection;

interface UserManagementRepositoryInterface
{
    /**
     * Find a user by ID.
     */
    public function findById(int $id): ?User;

    /**
     * Find a user by email.
     */
    public function findByEmail(string $email): ?User;

    /**
     * Check if a user exists with the given email (excluding specific user ID).
     */
    public function existsWithEmail(string $email, ?int $excludeUserId = null): bool;

    /**
     * Create a new user.
     *
     * @param  array<string, mixed>  $data
     */
    public function create(array $data): User;

    /**
     * Update an existing user.
     *
     * @param  array<string, mixed>  $data
     */
    public function update(User $user, array $data): User;

    /**
     * Get all admin users (admin + super_admin).
     *
     * @return Collection<int, User>
     */
    public function getAllAdmins(): Collection;
}
