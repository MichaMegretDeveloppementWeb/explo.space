<?php

namespace Tests\Feature\Commands;

use App\Enums\AutofillWorkflowState;
use App\Enums\AutofillWorkflowStatus;
use App\Models\AutofillWorkflow;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class CleanupAutofillCommandTest extends TestCase
{
    use RefreshDatabase;

    public function test_command_runs_successfully(): void
    {
        Storage::fake('autofill_temp');

        $this->artisan('autofill:cleanup')
            ->assertExitCode(0)
            ->expectsOutputToContain('Cleaned up 0 orphaned temp directories.');
    }

    public function test_command_cleans_orphaned_directories(): void
    {
        Storage::fake('autofill_temp');
        Storage::disk('autofill_temp')->put('99999/image.jpg', 'fake');

        $this->artisan('autofill:cleanup')
            ->assertExitCode(0)
            ->expectsOutputToContain('Cleaned up 1 orphaned temp directory.');

        Storage::disk('autofill_temp')->assertMissing('99999/image.jpg');
    }

    public function test_command_preserves_active_workflow_files(): void
    {
        Storage::fake('autofill_temp');

        $admin = User::factory()->create();
        $workflow = AutofillWorkflow::factory()->create([
            'admin_id' => $admin->id,
            'status' => AutofillWorkflowStatus::Enriching,
            'state' => AutofillWorkflowState::Active,
        ]);

        Storage::disk('autofill_temp')->put($workflow->id.'/image.jpg', 'fake');

        $this->artisan('autofill:cleanup')
            ->assertExitCode(0)
            ->expectsOutputToContain('Cleaned up 0 orphaned temp directories.');

        Storage::disk('autofill_temp')->assertExists($workflow->id.'/image.jpg');
    }
}
