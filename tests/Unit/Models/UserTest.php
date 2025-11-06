<?php

namespace Tests\Unit\Models;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_has_default_admin_role(): void
    {
        $user = User::factory()->create();

        $this->assertEquals('admin', $user->role);
        $this->assertTrue($user->isAdmin());
        $this->assertTrue($user->hasAdminRights());
        $this->assertFalse($user->isSuperAdmin());
    }

    public function test_user_can_be_admin(): void
    {
        $user = User::factory()->create(['role' => 'admin']);

        $this->assertEquals('admin', $user->role);
        $this->assertTrue($user->isAdmin());
        $this->assertTrue($user->hasAdminRights());
        $this->assertFalse($user->isSuperAdmin());
    }

    public function test_user_can_be_super_admin(): void
    {
        $user = User::factory()->create(['role' => 'super_admin']);

        $this->assertEquals('super_admin', $user->role);
        $this->assertFalse($user->isAdmin()); // super_admin is different from admin
        $this->assertTrue($user->hasAdminRights()); // but has admin rights
        $this->assertTrue($user->isSuperAdmin());
    }

    public function test_user_has_admin_relations(): void
    {
        $user = User::factory()->create();

        // Relations pour les demandes vues et traitées par cet admin
        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Relations\HasMany::class, $user->viewedPlaceRequests());
        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Relations\HasMany::class, $user->processedPlaceRequests());
        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Relations\HasMany::class, $user->viewedEditRequests());
        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Relations\HasMany::class, $user->processedEditRequests());
        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Relations\HasMany::class, $user->managedPlaces());
    }
}
