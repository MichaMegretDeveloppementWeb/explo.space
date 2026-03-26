<?php

namespace Tests\Feature\Livewire\Admin\Autofill;

use App\Enums\AutofillItemStatus;
use App\Enums\AutofillMessageType;
use App\Enums\AutofillWorkflowState;
use App\Enums\AutofillWorkflowStatus;
use App\Livewire\Admin\Autofill\AutofillChat;
use App\Models\AutofillItem;
use App\Models\AutofillWorkflow;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Livewire\Livewire;
use Tests\TestCase;

class AutofillChatTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create(['role' => 'admin']);
        $this->actingAs($this->admin);
    }

    public function test_component_renders_without_active_workflow(): void
    {
        Livewire::test(AutofillChat::class)
            ->assertStatus(200)
            ->assertSet('activeWorkflowId', null)
            ->assertSet('isPolling', false)
            ->assertSee('Assistant de remplissage');
    }

    public function test_component_renders_with_active_workflow(): void
    {
        $workflow = AutofillWorkflow::factory()->create([
            'admin_id' => $this->admin->id,
            'status' => AutofillWorkflowStatus::Discovering,
            'state' => AutofillWorkflowState::Active,
            'query' => 'Space launch sites',
        ]);

        Livewire::test(AutofillChat::class)
            ->assertStatus(200)
            ->assertSet('activeWorkflowId', $workflow->id)
            ->assertSet('isPolling', true);
    }

    public function test_component_renders_with_paused_workflow_no_polling(): void
    {
        $workflow = AutofillWorkflow::factory()->paused()->create([
            'admin_id' => $this->admin->id,
            'status' => AutofillWorkflowStatus::Discovering,
            'query' => 'Space launch sites',
        ]);

        Livewire::test(AutofillChat::class)
            ->assertStatus(200)
            ->assertSet('activeWorkflowId', $workflow->id)
            ->assertSet('isPolling', false);
    }

    public function test_dismissed_workflow_not_loaded_on_mount(): void
    {
        AutofillWorkflow::factory()->completed()->create([
            'admin_id' => $this->admin->id,
        ]);

        AutofillWorkflow::factory()->abandoned()->create([
            'admin_id' => $this->admin->id,
        ]);

        Livewire::test(AutofillChat::class)
            ->assertStatus(200)
            ->assertSet('activeWorkflowId', null)
            ->assertSee('Assistant de remplissage');
    }

    public function test_start_workflow_with_valid_data(): void
    {
        Queue::fake();

        Livewire::test(AutofillChat::class)
            ->set('query', 'Space launch sites in Europe')
            ->set('provider', 'openai')
            ->set('quantity', 5)
            ->call('startWorkflow')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('autofill_workflows', [
            'admin_id' => $this->admin->id,
            'query' => 'Space launch sites in Europe',
            'provider' => 'openai',
            'requested_quantity' => 5,
            'state' => AutofillWorkflowState::Active->value,
        ]);
    }

    public function test_start_workflow_validation_fails_with_empty_query(): void
    {
        Livewire::test(AutofillChat::class)
            ->set('query', '')
            ->set('provider', 'openai')
            ->set('quantity', 5)
            ->call('startWorkflow')
            ->assertHasErrors(['query' => 'required']);
    }

    public function test_start_workflow_validation_fails_with_invalid_provider(): void
    {
        Livewire::test(AutofillChat::class)
            ->set('query', 'Space launch sites')
            ->set('provider', 'invalid_provider')
            ->set('quantity', 5)
            ->call('startWorkflow')
            ->assertHasErrors(['provider' => 'in']);
    }

    public function test_start_workflow_validation_fails_with_quantity_too_high(): void
    {
        Livewire::test(AutofillChat::class)
            ->set('query', 'Space launch sites')
            ->set('provider', 'openai')
            ->set('quantity', 999)
            ->call('startWorkflow')
            ->assertHasErrors(['quantity' => 'max']);
    }

    public function test_blocked_by_other_admin_active_workflow(): void
    {
        $otherAdmin = User::factory()->create(['role' => 'admin', 'name' => 'Jean Dupont']);

        AutofillWorkflow::factory()->create([
            'admin_id' => $otherAdmin->id,
            'status' => AutofillWorkflowStatus::Discovering,
            'state' => AutofillWorkflowState::Active,
        ]);

        Livewire::test(AutofillChat::class)
            ->assertSet('blockedByOtherAdmin', true)
            ->assertSet('blockedByAdminName', 'Jean Dupont');
    }

    public function test_interrupt_confirm_shown_when_starting_with_active_own_workflow(): void
    {
        Queue::fake();

        AutofillWorkflow::factory()->create([
            'admin_id' => $this->admin->id,
            'status' => AutofillWorkflowStatus::Discovering,
            'state' => AutofillWorkflowState::Active,
        ]);

        Livewire::test(AutofillChat::class)
            ->set('query', 'New query')
            ->set('provider', 'openai')
            ->set('quantity', 5)
            ->call('startWorkflow')
            ->assertSet('showInterruptConfirm', true);
    }

    public function test_confirm_interrupt_abandons_old_and_creates_new_workflow(): void
    {
        Queue::fake();

        $workflow = AutofillWorkflow::factory()->create([
            'admin_id' => $this->admin->id,
            'status' => AutofillWorkflowStatus::Discovering,
            'state' => AutofillWorkflowState::Active,
        ]);

        Livewire::test(AutofillChat::class)
            ->set('query', 'New query')
            ->set('provider', 'openai')
            ->set('quantity', 5)
            ->call('startWorkflow')
            ->call('confirmInterrupt');

        $workflow->refresh();
        $this->assertSame(AutofillWorkflowState::Abandoned, $workflow->state);

        $this->assertDatabaseHas('autofill_workflows', [
            'admin_id' => $this->admin->id,
            'query' => 'New query',
            'status' => AutofillWorkflowStatus::Pending->value,
            'state' => AutofillWorkflowState::Active->value,
        ]);
    }

    public function test_submit_selection_at_checkpoint_1(): void
    {
        Queue::fake();

        $workflow = AutofillWorkflow::factory()->awaitingSelection()->create([
            'admin_id' => $this->admin->id,
        ]);

        $item1 = AutofillItem::factory()->create([
            'workflow_id' => $workflow->id,
            'status' => AutofillItemStatus::Discovered,
        ]);

        $item2 = AutofillItem::factory()->create([
            'workflow_id' => $workflow->id,
            'status' => AutofillItemStatus::Discovered,
        ]);

        Livewire::test(AutofillChat::class)
            ->set('selectedItems', [$item1->id])
            ->call('submitSelection');

        $this->assertDatabaseHas('autofill_items', [
            'id' => $item1->id,
            'status' => AutofillItemStatus::Selected->value,
        ]);

        $this->assertDatabaseHas('autofill_items', [
            'id' => $item2->id,
            'status' => AutofillItemStatus::Skipped->value,
        ]);
    }

    public function test_empty_selection_shows_confirmation(): void
    {
        AutofillWorkflow::factory()->awaitingSelection()->create([
            'admin_id' => $this->admin->id,
        ]);

        Livewire::test(AutofillChat::class)
            ->set('selectedItems', [])
            ->call('submitSelection')
            ->assertSet('showEmptySelectionConfirm', true);
    }

    public function test_confirm_empty_selection_completes_workflow(): void
    {
        $workflow = AutofillWorkflow::factory()->awaitingSelection()->create([
            'admin_id' => $this->admin->id,
        ]);

        Livewire::test(AutofillChat::class)
            ->set('selectedItems', [])
            ->call('submitSelection')
            ->call('confirmEmptySelection');

        $workflow->refresh();
        $this->assertSame(AutofillWorkflowStatus::Completed, $workflow->status);
        $this->assertSame(AutofillWorkflowState::Completed, $workflow->state);
    }

    public function test_abandon_active_workflow(): void
    {
        $workflow = AutofillWorkflow::factory()->create([
            'admin_id' => $this->admin->id,
            'status' => AutofillWorkflowStatus::Enriching,
            'state' => AutofillWorkflowState::Active,
        ]);

        Livewire::test(AutofillChat::class)
            ->call('abandonWorkflow')
            ->assertSet('activeWorkflowId', null)
            ->assertSet('isPolling', false);

        $workflow->refresh();
        $this->assertSame(AutofillWorkflowState::Abandoned, $workflow->state);
    }

    public function test_abandon_paused_workflow(): void
    {
        $workflow = AutofillWorkflow::factory()->paused()->create([
            'admin_id' => $this->admin->id,
            'status' => AutofillWorkflowStatus::Enriching,
        ]);

        Livewire::test(AutofillChat::class)
            ->assertSet('activeWorkflowId', $workflow->id)
            ->call('abandonWorkflow')
            ->assertSet('activeWorkflowId', null)
            ->assertSet('isPolling', false);

        $workflow->refresh();
        $this->assertSame(AutofillWorkflowState::Abandoned, $workflow->state);
    }

    public function test_resume_paused_workflow(): void
    {
        Queue::fake();

        $workflow = AutofillWorkflow::factory()->paused()->create([
            'admin_id' => $this->admin->id,
            'status' => AutofillWorkflowStatus::Enriching,
        ]);

        AutofillItem::factory()->create([
            'workflow_id' => $workflow->id,
            'status' => AutofillItemStatus::Failed,
            'step_failed_at' => 'enrichment',
        ]);

        Livewire::test(AutofillChat::class)
            ->call('resumeWorkflow')
            ->assertSet('isPolling', true);

        $workflow->refresh();
        $this->assertSame(AutofillWorkflowState::Active, $workflow->state);

        Queue::assertPushed(\App\Jobs\Autofill\EnrichmentJob::class);
    }

    public function test_refresh_messages_loads_messages(): void
    {
        $workflow = AutofillWorkflow::factory()->awaitingSelection()->create([
            'admin_id' => $this->admin->id,
        ]);

        $workflow->messages()->create([
            'type' => AutofillMessageType::Progress,
            'role' => \App\Enums\AutofillMessageRole::System,
            'payload' => ['text' => 'Recherche en cours...'],
            'created_at' => now(),
        ]);

        $component = Livewire::test(AutofillChat::class)
            ->call('refreshMessages');

        $this->assertCount(1, $component->get('messages'));
    }
}
