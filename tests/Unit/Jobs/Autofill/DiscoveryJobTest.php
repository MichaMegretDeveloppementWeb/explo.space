<?php

namespace Tests\Unit\Jobs\Autofill;

use App\Ai\Agents\DiscoveryAgent;
use App\Enums\AutofillItemStatus;
use App\Enums\AutofillMessageType;
use App\Enums\AutofillWorkflowStatus;
use App\Jobs\Autofill\DiscoveryJob;
use App\Models\AutofillWorkflow;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DiscoveryJobTest extends TestCase
{
    use RefreshDatabase;

    public function test_discovery_creates_items_and_awaits_selection(): void
    {
        DiscoveryAgent::fake([
            [
                'places' => [
                    [
                        'name' => 'Baikonur Cosmodrome',
                        'approximate_location' => 'Kazakhstan',
                        'justification' => 'First and largest space launch facility.',
                        'latitude' => 45.9646,
                        'longitude' => 63.3052,
                    ],
                    [
                        'name' => 'Guiana Space Centre',
                        'approximate_location' => 'French Guiana',
                        'justification' => 'European spaceport near the equator.',
                        'latitude' => 5.2322,
                        'longitude' => -52.7693,
                    ],
                ],
            ],
        ]);

        $admin = User::factory()->create();
        $workflow = AutofillWorkflow::factory()->create([
            'admin_id' => $admin->id,
            'status' => AutofillWorkflowStatus::Pending,
            'query' => 'Space launch sites',
            'provider' => 'openai',
            'requested_quantity' => 5,
        ]);

        $job = new DiscoveryJob($workflow->id);
        $job->handle(new \App\Services\Admin\Autofill\AutofillPipelineService);

        $workflow->refresh();
        $this->assertSame(AutofillWorkflowStatus::AwaitingSelection, $workflow->status);

        $this->assertDatabaseHas('autofill_items', [
            'workflow_id' => $workflow->id,
            'name' => 'Baikonur Cosmodrome',
            'status' => AutofillItemStatus::Discovered->value,
        ]);

        $this->assertDatabaseHas('autofill_items', [
            'workflow_id' => $workflow->id,
            'name' => 'Guiana Space Centre',
            'status' => AutofillItemStatus::Discovered->value,
        ]);

        $this->assertDatabaseHas('autofill_messages', [
            'workflow_id' => $workflow->id,
            'type' => AutofillMessageType::Selection->value,
        ]);
    }

    public function test_discovery_with_zero_results_completes_workflow(): void
    {
        DiscoveryAgent::fake([
            ['places' => []],
        ]);

        $admin = User::factory()->create();
        $workflow = AutofillWorkflow::factory()->create([
            'admin_id' => $admin->id,
            'status' => AutofillWorkflowStatus::Pending,
            'query' => 'Pizza restaurants',
            'provider' => 'openai',
            'requested_quantity' => 5,
        ]);

        $job = new DiscoveryJob($workflow->id);
        $job->handle(new \App\Services\Admin\Autofill\AutofillPipelineService);

        $workflow->refresh();
        $this->assertSame(AutofillWorkflowStatus::Completed, $workflow->status);
    }

    public function test_discovery_logs_step(): void
    {
        DiscoveryAgent::fake([
            ['places' => [[
                'name' => 'Test',
                'approximate_location' => 'Test',
                'justification' => 'Test',
                'latitude' => 0.0,
                'longitude' => 0.0,
            ]]],
        ]);

        $admin = User::factory()->create();
        $workflow = AutofillWorkflow::factory()->create([
            'admin_id' => $admin->id,
            'status' => AutofillWorkflowStatus::Pending,
            'query' => 'Test',
            'provider' => 'openai',
            'requested_quantity' => 1,
        ]);

        $job = new DiscoveryJob($workflow->id);
        $job->handle(new \App\Services\Admin\Autofill\AutofillPipelineService);

        $this->assertDatabaseHas('autofill_step_logs', [
            'workflow_id' => $workflow->id,
            'step' => 'discovery',
        ]);
    }
}
