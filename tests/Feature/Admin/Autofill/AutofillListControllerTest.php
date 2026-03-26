<?php

namespace Tests\Feature\Admin\Autofill;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AutofillListControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_autofill_page_requires_authentication(): void
    {
        $response = $this->get(route('admin.autofill.index'));

        $response->assertRedirect(route('admin.login'));
    }

    public function test_autofill_page_accessible_for_admin(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($admin)->get(route('admin.autofill.index'));

        $response->assertStatus(200);
        $response->assertViewIs('admin.autofill.index');
    }

    public function test_autofill_page_contains_livewire_components(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($admin)->get(route('admin.autofill.index'));

        $response->assertStatus(200);
        $response->assertSeeLivewire('admin.autofill.autofill-chat');
        $response->assertSeeLivewire('admin.autofill.autofill-history');
    }
}
