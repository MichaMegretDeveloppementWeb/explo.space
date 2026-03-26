<?php

namespace Tests\Unit\Services\Admin\Autofill;

use App\Enums\AutofillItemStatus;
use App\Enums\AutofillWorkflowState;
use App\Enums\AutofillWorkflowStatus;
use App\Models\AutofillItem;
use App\Models\AutofillMessage;
use App\Models\AutofillStepLog;
use App\Models\AutofillWorkflow;
use App\Models\User;
use App\Services\Admin\Autofill\AutofillCleanupService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AutofillCleanupServiceTest extends TestCase
{
    use RefreshDatabase;

    private AutofillCleanupService $service;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->service = new AutofillCleanupService;
        $this->admin = User::factory()->create();
    }

    public function test_purge_preserves_messages(): void
    {
        $workflow = AutofillWorkflow::factory()->completed()->create([
            'admin_id' => $this->admin->id,
        ]);

        AutofillMessage::factory()->count(3)->create([
            'workflow_id' => $workflow->id,
        ]);

        $this->service->purgeCompletedWorkflow($workflow);

        $this->assertSame(3, $workflow->messages()->count());
    }

    public function test_purge_nullifies_images_data_but_preserves_enrichment_data(): void
    {
        $workflow = AutofillWorkflow::factory()->completed()->create([
            'admin_id' => $this->admin->id,
        ]);

        $item = AutofillItem::factory()->create([
            'workflow_id' => $workflow->id,
            'status' => AutofillItemStatus::Saved,
            'enrichment_data' => ['title' => 'Test', 'justification' => 'Space museum'],
            'images_data' => [['url' => 'https://example.com/img.jpg']],
        ]);

        $this->service->purgeCompletedWorkflow($workflow);

        $item->refresh();
        $this->assertNotNull($item->enrichment_data);
        $this->assertSame('Test', $item->enrichment_data['title']);
        $this->assertSame('Space museum', $item->enrichment_data['justification']);
        $this->assertNull($item->images_data);
    }

    public function test_purge_preserves_step_logs(): void
    {
        $workflow = AutofillWorkflow::factory()->completed()->create([
            'admin_id' => $this->admin->id,
        ]);

        AutofillStepLog::factory()->count(2)->create([
            'workflow_id' => $workflow->id,
        ]);

        $this->service->purgeCompletedWorkflow($workflow);

        $this->assertSame(2, $workflow->stepLogs()->count());
    }

    public function test_purge_deletes_temp_files(): void
    {
        Storage::fake('autofill_temp');
        Storage::disk('autofill_temp')->put('1/image.jpg', 'fake');

        $workflow = AutofillWorkflow::factory()->completed()->create([
            'id' => 1,
            'admin_id' => $this->admin->id,
        ]);

        $this->service->purgeCompletedWorkflow($workflow);

        Storage::disk('autofill_temp')->assertMissing('1/image.jpg');
    }

    public function test_delete_workflow_removes_dismissed_workflow(): void
    {
        $workflow = AutofillWorkflow::factory()->completed()->create([
            'admin_id' => $this->admin->id,
        ]);

        $result = $this->service->deleteWorkflow($workflow);

        $this->assertTrue($result);
        $this->assertDatabaseMissing('autofill_workflows', ['id' => $workflow->id]);
    }

    public function test_delete_workflow_removes_abandoned_workflow(): void
    {
        $workflow = AutofillWorkflow::factory()->abandoned()->create([
            'admin_id' => $this->admin->id,
        ]);

        $result = $this->service->deleteWorkflow($workflow);

        $this->assertTrue($result);
        $this->assertDatabaseMissing('autofill_workflows', ['id' => $workflow->id]);
    }

    public function test_delete_workflow_refuses_active_workflow(): void
    {
        $workflow = AutofillWorkflow::factory()->create([
            'admin_id' => $this->admin->id,
            'status' => AutofillWorkflowStatus::Enriching,
            'state' => AutofillWorkflowState::Active,
        ]);

        $result = $this->service->deleteWorkflow($workflow);

        $this->assertFalse($result);
        $this->assertDatabaseHas('autofill_workflows', ['id' => $workflow->id]);
    }

    public function test_delete_workflow_refuses_paused_workflow(): void
    {
        $workflow = AutofillWorkflow::factory()->paused()->create([
            'admin_id' => $this->admin->id,
        ]);

        $result = $this->service->deleteWorkflow($workflow);

        $this->assertFalse($result);
        $this->assertDatabaseHas('autofill_workflows', ['id' => $workflow->id]);
    }

    public function test_delete_workflow_does_not_delete_created_places(): void
    {
        $workflow = AutofillWorkflow::factory()->completed()->create([
            'admin_id' => $this->admin->id,
        ]);

        // Create a place and associate it with an item
        $place = \App\Models\Place::factory()->create();

        AutofillItem::factory()->create([
            'workflow_id' => $workflow->id,
            'place_id' => $place->id,
            'status' => AutofillItemStatus::Saved,
        ]);

        $this->service->deleteWorkflow($workflow);

        $this->assertDatabaseMissing('autofill_workflows', ['id' => $workflow->id]);
        $this->assertDatabaseMissing('autofill_items', ['workflow_id' => $workflow->id]);
        $this->assertDatabaseHas('places', ['id' => $place->id]);
    }

    public function test_delete_workflow_cleans_temp_files(): void
    {
        Storage::fake('autofill_temp');
        Storage::disk('autofill_temp')->put('1/image.jpg', 'fake');

        $workflow = AutofillWorkflow::factory()->abandoned()->create([
            'id' => 1,
            'admin_id' => $this->admin->id,
        ]);

        $this->service->deleteWorkflow($workflow);

        Storage::disk('autofill_temp')->assertMissing('1/image.jpg');
    }

    public function test_cleanup_orphaned_temp_files_removes_dirs_without_workflow(): void
    {
        Storage::fake('autofill_temp');
        Storage::disk('autofill_temp')->put('99999/image.jpg', 'fake');

        $cleaned = $this->service->cleanupOrphanedTempFiles();

        $this->assertSame(1, $cleaned);
        Storage::disk('autofill_temp')->assertMissing('99999/image.jpg');
    }

    public function test_cleanup_orphaned_temp_files_removes_old_dismissed_dirs(): void
    {
        Storage::fake('autofill_temp');

        $workflow = AutofillWorkflow::factory()->completed()->create([
            'admin_id' => $this->admin->id,
            'completed_at' => now()->subDays(2),
        ]);

        Storage::disk('autofill_temp')->put($workflow->id.'/image.jpg', 'fake');

        $cleaned = $this->service->cleanupOrphanedTempFiles();

        $this->assertSame(1, $cleaned);
        Storage::disk('autofill_temp')->assertMissing($workflow->id.'/image.jpg');
    }

    public function test_cleanup_orphaned_temp_files_preserves_recent_dismissed_dirs(): void
    {
        Storage::fake('autofill_temp');

        $workflow = AutofillWorkflow::factory()->completed()->create([
            'admin_id' => $this->admin->id,
            'completed_at' => now()->subHours(12),
        ]);

        Storage::disk('autofill_temp')->put($workflow->id.'/image.jpg', 'fake');

        $cleaned = $this->service->cleanupOrphanedTempFiles();

        $this->assertSame(0, $cleaned);
        Storage::disk('autofill_temp')->assertExists($workflow->id.'/image.jpg');
    }

    public function test_cleanup_orphaned_temp_files_preserves_active_workflow_dirs(): void
    {
        Storage::fake('autofill_temp');

        $workflow = AutofillWorkflow::factory()->create([
            'admin_id' => $this->admin->id,
            'status' => AutofillWorkflowStatus::Enriching,
            'state' => AutofillWorkflowState::Active,
        ]);

        Storage::disk('autofill_temp')->put($workflow->id.'/image.jpg', 'fake');

        $cleaned = $this->service->cleanupOrphanedTempFiles();

        $this->assertSame(0, $cleaned);
        Storage::disk('autofill_temp')->assertExists($workflow->id.'/image.jpg');
    }
}
