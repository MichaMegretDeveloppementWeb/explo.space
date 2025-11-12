<?php

namespace Tests\Livewire\Admin\Settings;

use App\Livewire\Admin\Settings\SettingsContainer;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class SettingsContainerTest extends TestCase
{
    use RefreshDatabase;

    public function test_component_can_be_rendered(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin);

        Livewire::test(SettingsContainer::class)
            ->assertStatus(200)
            ->assertSee('Mon profil')
            ->assertSee('Mot de passe');
    }

    public function test_default_tab_is_profile(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin);

        Livewire::test(SettingsContainer::class)
            ->assertSet('activeTab', 'profile');
    }

    public function test_can_switch_to_password_tab(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin);

        Livewire::test(SettingsContainer::class)
            ->call('setActiveTab', 'password')
            ->assertSet('activeTab', 'password');
    }

    public function test_admin_cannot_access_restricted_tabs(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin);

        Livewire::test(SettingsContainer::class)
            ->call('setActiveTab', 'create-admin')
            ->assertSet('activeTab', 'profile') // Redirigé vers profile
            ->call('setActiveTab', 'admin-list')
            ->assertSet('activeTab', 'profile'); // Redirigé vers profile
    }

    public function test_super_admin_can_access_all_tabs(): void
    {
        $superAdmin = User::factory()->create(['role' => 'super_admin']);

        $this->actingAs($superAdmin);

        Livewire::test(SettingsContainer::class)
            ->call('setActiveTab', 'create-admin')
            ->assertSet('activeTab', 'create-admin')
            ->call('setActiveTab', 'admin-list')
            ->assertSet('activeTab', 'admin-list')
            ->call('setActiveTab', 'profile')
            ->assertSet('activeTab', 'profile')
            ->call('setActiveTab', 'password')
            ->assertSet('activeTab', 'password');
    }

    public function test_invalid_tab_redirects_to_profile(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin);

        Livewire::test(SettingsContainer::class)
            ->call('setActiveTab', 'invalid-tab')
            ->assertSet('activeTab', 'profile');
    }

    public function test_public_tabs_are_accessible_by_all_admins(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin);

        Livewire::test(SettingsContainer::class)
            ->call('setActiveTab', 'profile')
            ->assertSet('activeTab', 'profile')
            ->call('setActiveTab', 'password')
            ->assertSet('activeTab', 'password');
    }

    public function test_url_parameter_is_respected_for_valid_tab(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin);

        Livewire::test(SettingsContainer::class)
            ->set('activeTab', 'password')
            ->assertSet('activeTab', 'password');
    }

    public function test_url_parameter_with_restricted_tab_redirects_admin_to_profile(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin);

        // Simuler l'arrivée sur la page avec ?tab=create-admin dans l'URL
        Livewire::withQueryParams(['tab' => 'create-admin'])
            ->test(SettingsContainer::class)
            ->assertSet('activeTab', 'profile'); // Redirigé silencieusement
    }

    public function test_url_parameter_with_restricted_tab_is_allowed_for_super_admin(): void
    {
        $superAdmin = User::factory()->create(['role' => 'super_admin']);

        $this->actingAs($superAdmin);

        Livewire::withQueryParams(['tab' => 'create-admin'])
            ->test(SettingsContainer::class)
            ->assertSet('activeTab', 'create-admin');
    }

    public function test_super_admin_sees_restricted_tabs_in_navigation(): void
    {
        $superAdmin = User::factory()->create(['role' => 'super_admin']);

        $this->actingAs($superAdmin);

        Livewire::test(SettingsContainer::class)
            ->assertSee('Créer un administrateur')
            ->assertSee('Liste des administrateurs');
    }

    public function test_admin_does_not_see_restricted_tabs_in_navigation(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin);

        Livewire::test(SettingsContainer::class)
            ->assertDontSee('Créer un administrateur')
            ->assertDontSee('Liste des administrateurs');
    }

    public function test_mount_validates_initial_tab(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin);

        // Tester avec un onglet invalide passé initialement
        Livewire::test(SettingsContainer::class, ['activeTab' => 'non-existent'])
            ->assertSet('activeTab', 'profile');
    }

    public function test_active_tab_content_is_loaded_dynamically(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin);

        // Vérifier que le contenu change selon l'onglet actif
        $component = Livewire::test(SettingsContainer::class);

        // Onglet profile
        $component->assertSet('activeTab', 'profile')
            ->assertSeeLivewire('admin.settings.profile-update.profile-update-form');

        // Onglet password
        $component->call('setActiveTab', 'password')
            ->assertSet('activeTab', 'password')
            ->assertSeeLivewire('admin.settings.password-update.password-update-form');
    }
}
