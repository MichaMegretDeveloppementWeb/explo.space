<?php

namespace Tests\Unit\Models;

use App\Enums\AutofillItemStatus;
use App\Models\AutofillItem;
use App\Models\AutofillStepLog;
use App\Models\AutofillWorkflow;
use App\Models\User;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AutofillItemTest extends TestCase
{
    use RefreshDatabase;

    public function test_item_can_be_created_with_factory(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $workflow = AutofillWorkflow::factory()->create(['admin_id' => $admin->id]);
        $item = AutofillItem::factory()->create(['workflow_id' => $workflow->id]);

        $this->assertDatabaseHas('autofill_items', ['id' => $item->id]);
    }

    public function test_item_casts_status_to_enum(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $workflow = AutofillWorkflow::factory()->create(['admin_id' => $admin->id]);
        $item = AutofillItem::factory()->create(['workflow_id' => $workflow->id]);

        $this->assertInstanceOf(AutofillItemStatus::class, $item->status);
    }

    public function test_item_casts_json_fields(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $workflow = AutofillWorkflow::factory()->create(['admin_id' => $admin->id]);
        $item = AutofillItem::factory()->enriched()->create(['workflow_id' => $workflow->id]);

        $this->assertIsArray($item->enrichment_data);
        $this->assertArrayHasKey('title', $item->enrichment_data);
    }

    public function test_item_has_required_relations(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $workflow = AutofillWorkflow::factory()->create(['admin_id' => $admin->id]);
        $item = AutofillItem::factory()->create(['workflow_id' => $workflow->id]);

        $this->assertInstanceOf(BelongsTo::class, $item->workflow());
        $this->assertInstanceOf(BelongsTo::class, $item->place());
        $this->assertInstanceOf(HasMany::class, $item->stepLogs());
    }

    public function test_item_belongs_to_workflow(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $workflow = AutofillWorkflow::factory()->create(['admin_id' => $admin->id]);
        $item = AutofillItem::factory()->create(['workflow_id' => $workflow->id]);

        $this->assertEquals($workflow->id, $item->workflow->id);
    }

    public function test_scope_successful_filters_saved_items(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $workflow = AutofillWorkflow::factory()->create(['admin_id' => $admin->id]);

        AutofillItem::factory()->saved()->create(['workflow_id' => $workflow->id]);
        AutofillItem::factory()->failed()->create(['workflow_id' => $workflow->id]);
        AutofillItem::factory()->create(['workflow_id' => $workflow->id]); // discovered

        $this->assertEquals(1, AutofillItem::successful()->count());
    }

    public function test_scope_failed_filters_failed_items(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $workflow = AutofillWorkflow::factory()->create(['admin_id' => $admin->id]);

        AutofillItem::factory()->saved()->create(['workflow_id' => $workflow->id]);
        AutofillItem::factory()->failed()->count(2)->create(['workflow_id' => $workflow->id]);

        $this->assertEquals(2, AutofillItem::failed()->count());
    }

    public function test_scope_selected_excludes_discovered_and_skipped(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $workflow = AutofillWorkflow::factory()->create(['admin_id' => $admin->id]);

        AutofillItem::factory()->create(['workflow_id' => $workflow->id, 'status' => AutofillItemStatus::Discovered]);
        AutofillItem::factory()->create(['workflow_id' => $workflow->id, 'status' => AutofillItemStatus::Skipped]);
        AutofillItem::factory()->selected()->create(['workflow_id' => $workflow->id]);
        AutofillItem::factory()->saved()->create(['workflow_id' => $workflow->id]);

        $this->assertEquals(2, AutofillItem::selected()->count());
    }

    public function test_item_defaults_tokens_and_cost_to_zero(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $workflow = AutofillWorkflow::factory()->create(['admin_id' => $admin->id]);
        $item = AutofillItem::factory()->create(['workflow_id' => $workflow->id]);

        $this->assertEquals(0, $item->tokens_in);
        $this->assertEquals(0, $item->tokens_out);
        $this->assertEquals('0.000000', $item->cost);
    }

    public function test_item_cascade_deletes_step_logs(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $workflow = AutofillWorkflow::factory()->create(['admin_id' => $admin->id]);
        $item = AutofillItem::factory()->create(['workflow_id' => $workflow->id]);
        AutofillStepLog::factory()->count(3)->create([
            'workflow_id' => $workflow->id,
            'item_id' => $item->id,
        ]);

        $item->delete();

        $this->assertEquals(0, AutofillStepLog::where('item_id', $item->id)->count());
    }
}
