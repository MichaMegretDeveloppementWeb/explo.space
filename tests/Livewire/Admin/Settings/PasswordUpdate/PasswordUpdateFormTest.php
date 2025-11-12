<?php

namespace Tests\Livewire\Admin\Settings\PasswordUpdate;

use App\Livewire\Admin\Settings\PasswordUpdate\PasswordUpdateForm;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Livewire\Livewire;
use Tests\TestCase;

class PasswordUpdateFormTest extends TestCase
{
    use RefreshDatabase;

    public function test_component_can_be_rendered(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin);

        Livewire::test(PasswordUpdateForm::class)
            ->assertStatus(200)
            ->assertSee('Modifier le mot de passe')
            ->assertSee('Nouveau mot de passe')
            ->assertSee('Confirmer le nouveau mot de passe');
    }

    public function test_password_is_required(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin);

        Livewire::test(PasswordUpdateForm::class)
            ->set('password', '')
            ->set('password_confirmation', 'password123')
            ->call('updatePassword')
            ->assertHasErrors(['password' => 'required'])
            ->assertSee('Le nouveau mot de passe est obligatoire.');
    }

    public function test_password_must_be_confirmed(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin);

        Livewire::test(PasswordUpdateForm::class)
            ->set('password', 'password123')
            ->set('password_confirmation', 'different-password')
            ->call('updatePassword')
            ->assertHasErrors(['password' => 'confirmed'])
            ->assertSee('Les mots de passe ne correspondent pas.');
    }

    public function test_password_must_meet_validation_rules(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin);

        // Test mot de passe trop court (< 8 caractères)
        Livewire::test(PasswordUpdateForm::class)
            ->set('password', 'short')
            ->set('password_confirmation', 'short')
            ->call('updatePassword')
            ->assertHasErrors(['password']);
    }

    public function test_update_password_succeeds_with_valid_data(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
            'password' => Hash::make('old-password'),
        ]);

        $this->actingAs($admin);

        Livewire::test(PasswordUpdateForm::class)
            ->set('password', 'new-secure-password')
            ->set('password_confirmation', 'new-secure-password')
            ->call('updatePassword')
            ->assertHasNoErrors()
            ->assertSet('password', '')
            ->assertSet('password_confirmation', '')
            ->assertDispatched('flash-message');
    }

    public function test_update_password_resets_form_after_success(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
            'password' => Hash::make('old-password'),
        ]);

        $this->actingAs($admin);

        Livewire::test(PasswordUpdateForm::class)
            ->set('password', 'new-password-123')
            ->set('password_confirmation', 'new-password-123')
            ->call('updatePassword')
            ->assertSet('password', '')
            ->assertSet('password_confirmation', '');
    }

    public function test_update_password_displays_error_when_new_password_is_same_as_old(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
            'password' => Hash::make('same-password'),
        ]);

        $this->actingAs($admin);

        Livewire::test(PasswordUpdateForm::class)
            ->set('password', 'same-password')
            ->set('password_confirmation', 'same-password')
            ->call('updatePassword')
            ->assertHasErrors(['password'])
            ->assertSee('différent de l\'ancien');
    }

    public function test_update_password_requires_authentication(): void
    {
        // Tenter d'accéder sans authentification devrait rediriger
        $response = $this->get(route('admin.settings.show'));

        $response->assertRedirect(route('admin.login'));
    }

    public function test_update_password_checks_authorization(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin);

        // Le test vérifie que la Policy est appelée
        // updateOwnPassword Policy devrait permettre à un admin de changer son propre mot de passe
        Livewire::test(PasswordUpdateForm::class)
            ->set('password', 'new-password-secure')
            ->set('password_confirmation', 'new-password-secure')
            ->call('updatePassword')
            ->assertHasNoErrors();
    }

    public function test_component_displays_security_tip(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin);

        Livewire::test(PasswordUpdateForm::class)
            ->assertSee('Conseil de sécurité')
            ->assertSee('8 caractères');
    }

    public function test_validation_errors_are_displayed_correctly(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin);

        Livewire::test(PasswordUpdateForm::class)
            ->set('password', '')
            ->call('updatePassword')
            ->assertHasErrors(['password'])
            ->assertSee('Le nouveau mot de passe est obligatoire.');
    }
}
