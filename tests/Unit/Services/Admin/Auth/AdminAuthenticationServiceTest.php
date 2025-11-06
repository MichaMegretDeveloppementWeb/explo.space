<?php

namespace Tests\Unit\Services\Admin\Auth;

use App\DTO\Admin\Auth\LoginCredentialsDTO;
use App\DTO\Admin\Auth\LoginResultDTO;
use App\Models\User;
use App\Services\Admin\Auth\AdminAuthenticationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Tests\TestCase;

class AdminAuthenticationServiceTest extends TestCase
{
    use RefreshDatabase;

    private AdminAuthenticationService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new AdminAuthenticationService;
    }

    public function test_authenticate_succeeds_with_valid_admin_credentials(): void
    {
        // Arrange
        $admin = User::factory()->create([
            'email' => 'admin@test.com',
            'password' => bcrypt('password123'),
            'role' => 'admin',
        ]);

        $credentials = new LoginCredentialsDTO(
            email: 'admin@test.com',
            password: 'password123',
            remember: false,
            ip: '127.0.0.1'
        );

        RateLimiter::clear($this->service->generateThrottleKey('admin@test.com', '127.0.0.1'));

        // Act
        $result = $this->service->authenticate($credentials);

        // Assert
        $this->assertInstanceOf(LoginResultDTO::class, $result);
        $this->assertTrue($result->success);
        $this->assertNotNull($result->redirectUrl);
        $this->assertTrue(Auth::check());
        $this->assertEquals($admin->id, Auth::id());
    }

    public function test_authenticate_fails_with_invalid_credentials(): void
    {
        // Arrange
        User::factory()->create([
            'email' => 'admin@test.com',
            'password' => bcrypt('correct-password'),
            'role' => 'admin',
        ]);

        $credentials = new LoginCredentialsDTO(
            email: 'admin@test.com',
            password: 'wrong-password',
            remember: false,
            ip: '127.0.0.1'
        );

        RateLimiter::clear($this->service->generateThrottleKey('admin@test.com', '127.0.0.1'));

        // Act
        $result = $this->service->authenticate($credentials);

        // Assert
        $this->assertFalse($result->success);
        $this->assertNotNull($result->errorMessage);
        $this->assertStringContainsString('identifiants', $result->errorMessage);
        $this->assertFalse(Auth::check());
    }

    // Test removed: impossible scenario - all users must have admin or super_admin role (see ENUM in migration)
    // The hasAdminRights() check in the service is defensive programming for future-proofing

    public function test_authenticate_is_rate_limited_after_max_attempts(): void
    {
        // Arrange
        $email = 'test@test.com';
        $ip = '127.0.0.1';
        $throttleKey = $this->service->generateThrottleKey($email, $ip);

        // Simuler 5 tentatives échouées
        for ($i = 0; $i < 5; $i++) {
            RateLimiter::hit($throttleKey, 60);
        }

        $credentials = new LoginCredentialsDTO(
            email: $email,
            password: 'password',
            remember: false,
            ip: $ip
        );

        // Act
        $result = $this->service->authenticate($credentials);

        // Assert
        $this->assertFalse($result->success);
        $this->assertNotNull($result->errorMessage);
        $this->assertStringContainsString('Trop de tentatives', $result->errorMessage);
        $this->assertStringContainsString('secondes', $result->errorMessage);
    }

    public function test_authenticate_clears_rate_limiter_on_success(): void
    {
        // Arrange
        $admin = User::factory()->create([
            'email' => 'admin@test.com',
            'password' => bcrypt('password123'),
            'role' => 'admin',
        ]);

        $email = 'admin@test.com';
        $ip = '127.0.0.1';
        $throttleKey = $this->service->generateThrottleKey($email, $ip);

        // Simuler 2 tentatives échouées
        RateLimiter::hit($throttleKey, 60);
        RateLimiter::hit($throttleKey, 60);

        $this->assertTrue(RateLimiter::attempts($throttleKey) > 0);

        $credentials = new LoginCredentialsDTO(
            email: $email,
            password: 'password123',
            remember: false,
            ip: $ip
        );

        // Act
        $result = $this->service->authenticate($credentials);

        // Assert
        $this->assertTrue($result->success);
        $this->assertEquals(0, RateLimiter::attempts($throttleKey));
    }

    public function test_logout_invalidates_session(): void
    {
        // Arrange
        $admin = User::factory()->create(['role' => 'admin']);

        // Create a request with session
        $request = \Illuminate\Http\Request::create('/logout', 'POST');
        $request->setLaravelSession($this->app['session.store']);
        $this->app->instance('request', $request);

        Auth::login($admin);
        $this->assertTrue(Auth::check());

        // Act
        $this->service->logout();

        // Assert
        $this->assertFalse(Auth::check());
    }

    public function test_generate_throttle_key_creates_unique_key(): void
    {
        // Act
        $key1 = $this->service->generateThrottleKey('admin@test.com', '127.0.0.1');
        $key2 = $this->service->generateThrottleKey('admin@test.com', '192.168.1.1');
        $key3 = $this->service->generateThrottleKey('other@test.com', '127.0.0.1');

        // Assert
        $this->assertEquals('admin@test.com|127.0.0.1', $key1);
        $this->assertNotEquals($key1, $key2);
        $this->assertNotEquals($key1, $key3);
        $this->assertNotEquals($key2, $key3);
    }

    public function test_generate_throttle_key_normalizes_email_to_lowercase(): void
    {
        // Act
        $key1 = $this->service->generateThrottleKey('ADMIN@TEST.COM', '127.0.0.1');
        $key2 = $this->service->generateThrottleKey('admin@test.com', '127.0.0.1');

        // Assert
        $this->assertEquals($key1, $key2);
    }

    public function test_is_rate_limited_returns_false_when_under_limit(): void
    {
        // Arrange
        $throttleKey = 'test@test.com|127.0.0.1';
        RateLimiter::clear($throttleKey);

        // Faire 2 tentatives (sous la limite de 5)
        RateLimiter::hit($throttleKey, 60);
        RateLimiter::hit($throttleKey, 60);

        // Act
        $result = $this->service->isRateLimited($throttleKey);

        // Assert
        $this->assertFalse($result);
    }

    public function test_is_rate_limited_returns_true_when_over_limit(): void
    {
        // Arrange
        $throttleKey = 'test@test.com|127.0.0.1';
        RateLimiter::clear($throttleKey);

        // Faire 5 tentatives (atteindre la limite)
        for ($i = 0; $i < 5; $i++) {
            RateLimiter::hit($throttleKey, 60);
        }

        // Act
        $result = $this->service->isRateLimited($throttleKey);

        // Assert
        $this->assertTrue($result);
    }

    public function test_get_rate_limit_seconds_returns_remaining_time(): void
    {
        // Arrange
        $throttleKey = 'test@test.com|127.0.0.1';
        RateLimiter::clear($throttleKey);

        // Faire 5 tentatives pour déclencher le rate limit
        for ($i = 0; $i < 5; $i++) {
            RateLimiter::hit($throttleKey, 60);
        }

        // Act
        $seconds = $this->service->getRateLimitSeconds($throttleKey);

        // Assert
        $this->assertGreaterThan(0, $seconds);
        $this->assertLessThanOrEqual(60, $seconds);
    }

    public function test_authenticate_with_remember_me_enabled(): void
    {
        // Arrange
        $admin = User::factory()->create([
            'email' => 'admin@test.com',
            'password' => bcrypt('password123'),
            'role' => 'admin',
        ]);

        $credentials = new LoginCredentialsDTO(
            email: 'admin@test.com',
            password: 'password123',
            remember: true, // Remember me activé
            ip: '127.0.0.1'
        );

        RateLimiter::clear($this->service->generateThrottleKey('admin@test.com', '127.0.0.1'));

        // Act
        $result = $this->service->authenticate($credentials);

        // Assert
        $this->assertTrue($result->success);
        $this->assertTrue(Auth::check());
        // Note: Auth::viaRemember() is only true when authenticated FROM a remember cookie,
        // not when setting remember during initial authentication
        $this->assertFalse(Auth::viaRemember());
    }
}
