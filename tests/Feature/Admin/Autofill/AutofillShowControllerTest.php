<?php

namespace Tests\Feature\Admin\Autofill;

use App\Models\AutofillWorkflow;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AutofillShowControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_show_page_requires_authentication(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $workflow = AutofillWorkflow::factory()->completed()->create([
            'admin_id' => $admin->id,
        ]);

        $response = $this->get(route('admin.autofill.show', $workflow->id));

        $response->assertRedirect(route('admin.login'));
    }

    public function test_show_page_accessible_for_admin(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $workflow = AutofillWorkflow::factory()->completed()->create([
            'admin_id' => $admin->id,
        ]);

        $response = $this->actingAs($admin)->get(route('admin.autofill.show', $workflow->id));

        $response->assertStatus(200);
        $response->assertViewIs('admin.autofill.show');
        $response->assertSeeLivewire('admin.autofill.autofill-workflow-detail');
    }

    public function test_show_page_returns_404_for_invalid_workflow(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($admin)->get(route('admin.autofill.show', 99999));

        $response->assertStatus(404);
    }
}
