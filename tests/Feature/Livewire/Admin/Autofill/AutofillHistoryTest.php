<?php

namespace Tests\Feature\Livewire\Admin\Autofill;

use App\Enums\AutofillWorkflowState;
use App\Enums\AutofillWorkflowStatus;
use App\Livewire\Admin\Autofill\AutofillHistory;
use App\Models\AutofillWorkflow;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class AutofillHistoryTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create(['role' => 'admin']);
        $this->actingAs($this->admin);
    }

    public function test_component_renders(): void
    {
        Livewire::test(AutofillHistory::class)
            ->assertStatus(200);
    }

    public function test_shows_completed_workflows(): void
    {
        AutofillWorkflow::factory()->completed()->create([
            'admin_id' => $this->admin->id,
            'query' => 'Space launch sites',
        ]);

        Livewire::test(AutofillHistory::class)
            ->assertSee('Space launch sites')
            ->assertSee('Terminé');
    }

    public function test_shows_active_workflows(): void
    {
        AutofillWorkflow::factory()->create([
            'admin_id' => $this->admin->id,
            'status' => AutofillWorkflowStatus::Discovering,
            'state' => AutofillWorkflowState::Active,
            'query' => 'Active workflow query',
        ]);

        Livewire::test(AutofillHistory::class)
            ->assertSee('Active workflow query')
            ->assertSee('Actif');
    }

    public function test_shows_paused_workflows(): void
    {
        AutofillWorkflow::factory()->paused()->create([
            'admin_id' => $this->admin->id,
            'status' => AutofillWorkflowStatus::Discovering,
            'query' => 'Paused workflow query',
        ]);

        Livewire::test(AutofillHistory::class)
            ->assertSee('Paused workflow query')
            ->assertSee('En pause');
    }

    public function test_shows_abandoned_workflows(): void
    {
        AutofillWorkflow::factory()->abandoned()->create([
            'admin_id' => $this->admin->id,
            'query' => 'Abandoned workflow query',
        ]);

        Livewire::test(AutofillHistory::class)
            ->assertSee('Abandoned workflow query')
            ->assertSee('Abandonné');
    }

    public function test_delete_dismissed_workflow(): void
    {
        $workflow = AutofillWorkflow::factory()->completed()->create([
            'admin_id' => $this->admin->id,
        ]);

        Livewire::test(AutofillHistory::class)
            ->call('deleteWorkflow', $workflow->id);

        $this->assertDatabaseMissing('autofill_workflows', ['id' => $workflow->id]);
    }

    public function test_cannot_delete_active_workflow(): void
    {
        $workflow = AutofillWorkflow::factory()->create([
            'admin_id' => $this->admin->id,
            'status' => AutofillWorkflowStatus::Enriching,
            'state' => AutofillWorkflowState::Active,
        ]);

        Livewire::test(AutofillHistory::class)
            ->call('deleteWorkflow', $workflow->id);

        $this->assertDatabaseHas('autofill_workflows', ['id' => $workflow->id]);
    }

    public function test_cannot_delete_paused_workflow(): void
    {
        $workflow = AutofillWorkflow::factory()->paused()->create([
            'admin_id' => $this->admin->id,
        ]);

        Livewire::test(AutofillHistory::class)
            ->call('deleteWorkflow', $workflow->id);

        $this->assertDatabaseHas('autofill_workflows', ['id' => $workflow->id]);
    }
}
