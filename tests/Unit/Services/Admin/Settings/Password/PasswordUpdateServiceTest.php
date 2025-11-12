<?php

namespace Tests\Unit\Services\Admin\Settings\Password;

use App\Contracts\Repositories\Admin\Settings\User\UserManagementRepositoryInterface;
use App\Models\User;
use App\Services\Admin\Settings\Password\PasswordUpdateService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Tests\TestCase;

class PasswordUpdateServiceTest extends TestCase
{
    use RefreshDatabase;

    private PasswordUpdateService $service;

    private UserManagementRepositoryInterface $repository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->repository = $this->mock(UserManagementRepositoryInterface::class);
        $this->service = new PasswordUpdateService($this->repository);
    }

    public function test_update_password_succeeds_with_different_new_password(): void
    {
        // Arrange
        $user = User::factory()->create([
            'password' => Hash::make('old-password'),
        ]);

        $newPassword = 'new-secure-password-123';

        $this->repository
            ->shouldReceive('update')
            ->once()
            ->with($user, ['password' => $newPassword])
            ->andReturn($user);

        Log::shouldReceive('info')
            ->once()
            ->with('User password updated', ['user_id' => $user->id]);

        // Act
        $result = $this->service->updatePassword($user, $newPassword);

        // Assert
        $this->assertInstanceOf(User::class, $result);
        $this->assertEquals($user->id, $result->id);
    }

    public function test_update_password_throws_exception_when_new_password_is_same_as_old(): void
    {
        // Arrange
        $user = User::factory()->create([
            'password' => Hash::make('same-password'),
        ]);

        // Act & Assert
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Le nouveau mot de passe doit être différent de l\'ancien.');

        $this->service->updatePassword($user, 'same-password');
    }

    public function test_update_password_logs_successful_update(): void
    {
        // Arrange
        $user = User::factory()->create([
            'password' => Hash::make('old-password'),
        ]);

        $newPassword = 'new-password';

        $this->repository
            ->shouldReceive('update')
            ->once()
            ->andReturn($user);

        Log::shouldReceive('info')
            ->once()
            ->with('User password updated', ['user_id' => $user->id]);

        // Act
        $this->service->updatePassword($user, $newPassword);

        // Assert - Log expectation verified by Mockery
    }

    public function test_update_password_calls_repository_with_correct_data(): void
    {
        // Arrange
        $user = User::factory()->create([
            'password' => Hash::make('old-password'),
        ]);

        $newPassword = 'brand-new-password';

        $this->repository
            ->shouldReceive('update')
            ->once()
            ->with($user, \Mockery::on(function ($data) use ($newPassword) {
                return isset($data['password']) && $data['password'] === $newPassword;
            }))
            ->andReturn($user);

        Log::shouldReceive('info')->once();

        // Act
        $this->service->updatePassword($user, $newPassword);

        // Assert - Expectations verified by Mockery
    }

    public function test_update_password_returns_updated_user_instance(): void
    {
        // Arrange
        $user = User::factory()->create([
            'password' => Hash::make('old-password'),
        ]);

        $updatedUser = User::factory()->make([
            'id' => $user->id,
            'email' => $user->email,
        ]);

        $this->repository
            ->shouldReceive('update')
            ->once()
            ->andReturn($updatedUser);

        Log::shouldReceive('info')->once();

        // Act
        $result = $this->service->updatePassword($user, 'new-password');

        // Assert
        $this->assertSame($updatedUser, $result);
    }
}
