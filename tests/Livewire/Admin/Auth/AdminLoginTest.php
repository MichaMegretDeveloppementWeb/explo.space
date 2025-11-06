<?php

namespace Tests\Livewire\Admin\Auth;

use App\Livewire\Admin\Auth\AdminLogin;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Livewire\Livewire;
use Tests\TestCase;

class AdminLoginTest extends TestCase
{
    use RefreshDatabase;

    public function test_component_can_be_rendered(): void
    {
        Livewire::test(AdminLogin::class)
            ->assertStatus(200)
            ->assertSee('email')
            ->assertSee('password')
            ->assertSee('remember');
    }

    public function test_email_is_required(): void
    {
        Livewire::test(AdminLogin::class)
            ->set('email', '')
            ->set('password', 'password')
            ->call('authenticate')
            ->assertHasErrors(['email' => 'required'])
            ->assertSee('L\'adresse email est obligatoire.');
    }

    public function test_email_must_be_valid_email(): void
    {
        Livewire::test(AdminLogin::class)
            ->set('email', 'invalid-email')
            ->set('password', 'password')
            ->call('authenticate')
            ->assertHasErrors(['email' => 'email'])
            ->assertSee('L\'adresse email doit être valide.');
    }

    public function test_password_is_required(): void
    {
        Livewire::test(AdminLogin::class)
            ->set('email', 'admin@test.com')
            ->set('password', '')
            ->call('authenticate')
            ->assertHasErrors(['password' => 'required'])
            ->assertSee('Le mot de passe est obligatoire.');
    }

    public function test_validation_errors_displayed_in_real_time(): void
    {
        Livewire::test(AdminLogin::class)
            ->set('email', 'invalid')
            ->assertHasErrors(['email' => 'email'])
            ->set('email', 'valid@email.com')
            ->assertHasNoErrors(['email']);
    }

    public function test_authenticate_with_valid_credentials(): void
    {
        $admin = User::factory()->create([
            'email' => 'admin@test.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);

        Livewire::test(AdminLogin::class)
            ->set('email', 'admin@test.com')
            ->set('password', 'password')
            ->call('authenticate')
            ->assertHasNoErrors()
            ->assertRedirect(route('admin.dashboard'));

        $this->assertAuthenticatedAs($admin);
    }

    public function test_authenticate_fails_with_invalid_credentials(): void
    {
        Livewire::test(AdminLogin::class)
            ->set('email', 'wrong@test.com')
            ->set('password', 'wrongpassword')
            ->call('authenticate')
            ->assertHasErrors(['email'])
            ->assertSee('Ces identifiants ne correspondent pas à nos enregistrements.');

        $this->assertGuest();
    }

    public function test_password_reset_after_failed_attempt(): void
    {
        Livewire::test(AdminLogin::class)
            ->set('email', 'wrong@test.com')
            ->set('password', 'wrongpassword')
            ->call('authenticate')
            ->assertSet('password', '');
    }

    public function test_remember_me_functionality(): void
    {
        $admin = User::factory()->create([
            'email' => 'admin@test.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);

        Livewire::test(AdminLogin::class)
            ->set('email', 'admin@test.com')
            ->set('password', 'password')
            ->set('remember', true)
            ->call('authenticate')
            ->assertRedirect(route('admin.dashboard'));

        $this->assertAuthenticatedAs($admin);
    }

    public function test_super_admin_can_authenticate(): void
    {
        $superAdmin = User::factory()->create([
            'email' => 'superadmin@test.com',
            'password' => Hash::make('password'),
            'role' => 'super_admin',
        ]);

        Livewire::test(AdminLogin::class)
            ->set('email', 'superadmin@test.com')
            ->set('password', 'password')
            ->call('authenticate')
            ->assertHasNoErrors()
            ->assertRedirect(route('admin.dashboard'));

        $this->assertAuthenticatedAs($superAdmin);
    }
}
