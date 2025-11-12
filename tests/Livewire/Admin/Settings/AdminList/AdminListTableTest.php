<?php

namespace Tests\Livewire\Admin\Settings\AdminList;

use App\Livewire\Admin\Settings\AdminList\AdminListTable;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class AdminListTableTest extends TestCase
{
    use RefreshDatabase;

    public function test_component_can_be_rendered_by_super_admin(): void
    {
        $superAdmin = User::factory()->create(['role' => 'super_admin']);

        $this->actingAs($superAdmin);

        Livewire::test(AdminListTable::class)
            ->assertStatus(200);
    }

    public function test_component_displays_list_of_admins(): void
    {
        $superAdmin = User::factory()->create(['role' => 'super_admin', 'name' => 'Super Admin']);
        $admin1 = User::factory()->create(['role' => 'admin', 'name' => 'Admin One']);
        $admin2 = User::factory()->create(['role' => 'admin', 'name' => 'Admin Two']);

        $this->actingAs($superAdmin);

        Livewire::test(AdminListTable::class)
            ->assertSee('Super Admin')
            ->assertSee('Admin One')
            ->assertSee('Admin Two');
    }

    public function test_search_filters_admins_by_name(): void
    {
        $superAdmin = User::factory()->create(['role' => 'super_admin']);
        User::factory()->create(['role' => 'admin', 'name' => 'John Doe']);
        User::factory()->create(['role' => 'admin', 'name' => 'Jane Smith']);

        $this->actingAs($superAdmin);

        Livewire::test(AdminListTable::class)
            ->set('search', 'John')
            ->assertSee('John Doe')
            ->assertDontSee('Jane Smith');
    }

    public function test_search_filters_admins_by_email(): void
    {
        $superAdmin = User::factory()->create(['role' => 'super_admin']);
        User::factory()->create(['role' => 'admin', 'email' => 'john@example.com']);
        User::factory()->create(['role' => 'admin', 'email' => 'jane@example.com']);

        $this->actingAs($superAdmin);

        Livewire::test(AdminListTable::class)
            ->set('search', 'john@example.com')
            ->assertSee('john@example.com')
            ->assertDontSee('jane@example.com');
    }

    public function test_sorting_by_name_works(): void
    {
        $superAdmin = User::factory()->create(['role' => 'super_admin']);

        $this->actingAs($superAdmin);

        Livewire::test(AdminListTable::class)
            ->call('sortByColumn', 'name')
            ->assertSet('sortBy', 'name')
            ->assertSet('sortDirection', 'desc');
    }

    public function test_sorting_toggles_direction(): void
    {
        $superAdmin = User::factory()->create(['role' => 'super_admin']);

        $this->actingAs($superAdmin);

        Livewire::test(AdminListTable::class)
            ->call('sortByColumn', 'name')
            ->assertSet('sortDirection', 'desc')
            ->call('sortByColumn', 'name')
            ->assertSet('sortDirection', 'asc');
    }

    public function test_changing_per_page_updates_pagination(): void
    {
        $superAdmin = User::factory()->create(['role' => 'super_admin']);

        $this->actingAs($superAdmin);

        Livewire::test(AdminListTable::class)
            ->assertSet('perPage', 20)
            ->call('updatePerPage', 50)
            ->assertSet('perPage', 50);
    }

    public function test_filters_update_event_changes_search(): void
    {
        $superAdmin = User::factory()->create(['role' => 'super_admin']);

        $this->actingAs($superAdmin);

        Livewire::test(AdminListTable::class)
            ->dispatch('filters:updated', search: 'test')
            ->assertSet('search', 'test');
    }

    public function test_open_delete_modal_sets_correct_admin(): void
    {
        $superAdmin = User::factory()->create(['role' => 'super_admin']);
        $adminToDelete = User::factory()->create(['role' => 'admin']);

        $this->actingAs($superAdmin);

        Livewire::test(AdminListTable::class)
            ->call('openDeleteModal', $adminToDelete->id)
            ->assertSet('deleteModalOpen', true)
            ->assertSet('adminToDelete', $adminToDelete->id);
    }

    public function test_close_delete_modal_resets_state(): void
    {
        $superAdmin = User::factory()->create(['role' => 'super_admin']);
        $adminToDelete = User::factory()->create(['role' => 'admin']);

        $this->actingAs($superAdmin);

        Livewire::test(AdminListTable::class)
            ->call('openDeleteModal', $adminToDelete->id)
            ->call('closeDeleteModal')
            ->assertSet('deleteModalOpen', false)
            ->assertSet('adminToDelete', null);
    }

    public function test_confirm_delete_successfully_deletes_admin(): void
    {
        $superAdmin = User::factory()->create(['role' => 'super_admin']);
        $adminToDelete = User::factory()->create(['role' => 'admin', 'name' => 'Admin To Delete']);

        $this->actingAs($superAdmin);

        Livewire::test(AdminListTable::class)
            ->call('openDeleteModal', $adminToDelete->id)
            ->call('confirmDelete')
            ->assertDispatched('flash-message');

        $this->assertDatabaseMissing('users', ['id' => $adminToDelete->id]);
    }

    public function test_confirm_delete_fails_when_admin_not_found(): void
    {
        $superAdmin = User::factory()->create(['role' => 'super_admin']);

        $this->actingAs($superAdmin);

        Livewire::test(AdminListTable::class)
            ->call('openDeleteModal', 9999) // ID inexistant
            ->call('confirmDelete')
            ->assertDispatched('flash-message');
    }

    public function test_confirm_delete_fails_without_permission(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $superAdminTarget = User::factory()->create(['role' => 'super_admin']);

        $this->actingAs($admin);

        Livewire::test(AdminListTable::class)
            ->call('openDeleteModal', $superAdminTarget->id)
            ->call('confirmDelete')
            ->assertDispatched('flash-message');

        $this->assertDatabaseHas('users', ['id' => $superAdminTarget->id]);
    }

    public function test_confirm_delete_fails_when_no_admin_selected(): void
    {
        $superAdmin = User::factory()->create(['role' => 'super_admin']);

        $this->actingAs($superAdmin);

        Livewire::test(AdminListTable::class)
            ->call('confirmDelete')
            ->assertDispatched('flash-message');
    }

    public function test_open_role_modal_sets_correct_admin_and_role(): void
    {
        $superAdmin = User::factory()->create(['role' => 'super_admin']);
        $adminToEdit = User::factory()->create(['role' => 'admin']);

        $this->actingAs($superAdmin);

        Livewire::test(AdminListTable::class)
            ->call('openRoleModal', $adminToEdit->id)
            ->assertSet('roleModalOpen', true)
            ->assertSet('adminToEdit', $adminToEdit->id)
            ->assertSet('newRole', 'admin');
    }

    public function test_close_role_modal_resets_state(): void
    {
        $superAdmin = User::factory()->create(['role' => 'super_admin']);
        $adminToEdit = User::factory()->create(['role' => 'admin']);

        $this->actingAs($superAdmin);

        Livewire::test(AdminListTable::class)
            ->call('openRoleModal', $adminToEdit->id)
            ->call('closeRoleModal')
            ->assertSet('roleModalOpen', false)
            ->assertSet('adminToEdit', null)
            ->assertSet('newRole', null);
    }

    public function test_confirm_role_change_successfully_updates_role(): void
    {
        $superAdmin = User::factory()->create(['role' => 'super_admin']);
        $adminToEdit = User::factory()->create(['role' => 'admin', 'name' => 'Test Admin']);

        $this->actingAs($superAdmin);

        Livewire::test(AdminListTable::class)
            ->call('openRoleModal', $adminToEdit->id)
            ->set('newRole', 'super_admin')
            ->call('confirmRoleChange')
            ->assertDispatched('flash-message');

        $this->assertDatabaseHas('users', [
            'id' => $adminToEdit->id,
            'role' => 'super_admin',
        ]);
    }

    public function test_confirm_role_change_fails_with_invalid_role(): void
    {
        $superAdmin = User::factory()->create(['role' => 'super_admin']);
        $adminToEdit = User::factory()->create(['role' => 'admin']);

        $this->actingAs($superAdmin);

        Livewire::test(AdminListTable::class)
            ->call('openRoleModal', $adminToEdit->id)
            ->set('newRole', 'invalid_role')
            ->call('confirmRoleChange')
            ->assertDispatched('flash-message');
    }

    public function test_confirm_role_change_fails_when_admin_not_found(): void
    {
        $superAdmin = User::factory()->create(['role' => 'super_admin']);

        $this->actingAs($superAdmin);

        Livewire::test(AdminListTable::class)
            ->set('adminToEdit', 9999)
            ->set('newRole', 'admin')
            ->call('confirmRoleChange')
            ->assertDispatched('flash-message');
    }

    public function test_confirm_role_change_shows_info_when_role_unchanged(): void
    {
        $superAdmin = User::factory()->create(['role' => 'super_admin']);
        $adminToEdit = User::factory()->create(['role' => 'admin']);

        $this->actingAs($superAdmin);

        Livewire::test(AdminListTable::class)
            ->call('openRoleModal', $adminToEdit->id)
            ->set('newRole', 'admin') // Même rôle
            ->call('confirmRoleChange')
            ->assertDispatched('flash-message');
    }

    public function test_confirm_role_change_fails_without_permission(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $superAdminTarget = User::factory()->create(['role' => 'super_admin']);

        $this->actingAs($admin);

        Livewire::test(AdminListTable::class)
            ->call('openRoleModal', $superAdminTarget->id)
            ->set('newRole', 'admin')
            ->call('confirmRoleChange')
            ->assertDispatched('flash-message');
    }

    public function test_confirm_role_change_fails_with_missing_information(): void
    {
        $superAdmin = User::factory()->create(['role' => 'super_admin']);

        $this->actingAs($superAdmin);

        Livewire::test(AdminListTable::class)
            ->call('confirmRoleChange')
            ->assertDispatched('flash-message');
    }

    public function test_pagination_resets_after_search_update(): void
    {
        $superAdmin = User::factory()->create(['role' => 'super_admin']);

        // Créer assez d'admins pour avoir plusieurs pages
        User::factory()->count(25)->create(['role' => 'admin']);

        $this->actingAs($superAdmin);

        $component = Livewire::test(AdminListTable::class)
            ->set('perPage', 10);

        // Vérifier qu'on est sur la page 1 initialement
        $this->assertEquals(1, $component->get('paginators')['page'] ?? 1);

        // Aller à la page 2
        $component->set('paginators.page', 2);

        // Appliquer un filtre devrait réinitialiser à la page 1
        $component->dispatch('filters:updated', search: 'test');

        $this->assertEquals(1, $component->get('paginators')['page'] ?? 1);
    }
}
