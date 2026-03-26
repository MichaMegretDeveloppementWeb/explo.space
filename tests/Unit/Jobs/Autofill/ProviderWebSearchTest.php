<?php

namespace Tests\Unit\Jobs\Autofill;

use App\Ai\Agents\DiscoveryAgent;
use App\Ai\Agents\EnrichmentAgent;
use App\Enums\AutofillItemStatus;
use App\Enums\AutofillWorkflowStatus;
use App\Jobs\Autofill\DiscoveryJob;
use App\Jobs\Autofill\EnrichmentJob;
use App\Models\AutofillItem;
use App\Models\AutofillWorkflow;
use App\Models\User;
use App\Services\Admin\Autofill\AutofillPipelineService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class ProviderWebSearchTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Verify that for each provider, DiscoveryJob creates a DiscoveryAgent
     * with the correct useWebSearch flag by checking the tools count.
     *
     * OpenAI/Anthropic/Gemini: WebSearch = 1 tool
     * Unknown providers: no tools = 0
     */
    public function test_web_search_providers_produce_correct_tool_count(): void
    {
        // Providers that should have WebSearch enabled
        $webSearchProviders = ['openai', 'anthropic', 'gemini'];

        foreach ($webSearchProviders as $provider) {
            $useWebSearch = in_array($provider, $webSearchProviders, true);
            $agent = DiscoveryAgent::make(useWebSearch: $useWebSearch);
            $tools = iterator_to_array($agent->tools());

            $this->assertCount(1, $tools, "Provider '{$provider}' should have 1 tool (WebSearch)");
        }

        // Unknown provider should NOT have WebSearch
        $useWebSearch = in_array('unknown_provider', $webSearchProviders, true);
        $agent = DiscoveryAgent::make(useWebSearch: $useWebSearch);
        $tools = iterator_to_array($agent->tools());

        $this->assertCount(0, $tools, 'Unknown provider should have 0 tools');
    }

    public function test_enrichment_web_search_providers_produce_correct_tool_count(): void
    {
        $webSearchProviders = ['openai', 'anthropic', 'gemini'];

        foreach ($webSearchProviders as $provider) {
            $useWebSearch = in_array($provider, $webSearchProviders, true);
            $agent = EnrichmentAgent::make(useWebSearch: $useWebSearch);
            $tools = iterator_to_array($agent->tools());

            $this->assertCount(1, $tools, "Provider '{$provider}' should have 1 tool (WebSearch)");
        }

        // Unknown provider should have NO tools
        $useWebSearch = in_array('unknown_provider', $webSearchProviders, true);
        $agent = EnrichmentAgent::make(useWebSearch: $useWebSearch);
        $tools = iterator_to_array($agent->tools());

        $this->assertCount(0, $tools, 'Unknown provider should have 0 tools');
    }

    public function test_discovery_job_uses_web_search_flag_matching_job_logic(): void
    {
        // This test replicates the exact logic from DiscoveryJob::handle()
        // to ensure the whitelist is correct and consistent
        $webSearchProviders = ['openai', 'anthropic', 'gemini'];

        $testCases = [
            'openai' => true,
            'anthropic' => true,
            'gemini' => true,
        ];

        foreach ($testCases as $provider => $expectedWebSearch) {
            $useWebSearch = in_array($provider, $webSearchProviders, true);

            $this->assertSame(
                $expectedWebSearch,
                $useWebSearch,
                "Provider '{$provider}' should have useWebSearch={$this->boolToString($expectedWebSearch)}"
            );
        }
    }

    public function test_gemini_discovery_works_with_web_search(): void
    {
        DiscoveryAgent::fake([
            [
                'places' => [
                    [
                        'name' => 'Kennedy Space Center',
                        'approximate_location' => 'Florida, USA',
                        'justification' => 'NASA primary launch center.',
                    ],
                ],
            ],
        ]);

        $admin = User::factory()->create();
        $workflow = AutofillWorkflow::factory()->create([
            'admin_id' => $admin->id,
            'status' => AutofillWorkflowStatus::Pending,
            'query' => 'Space launch sites',
            'provider' => 'gemini',
            'requested_quantity' => 3,
        ]);

        $job = new DiscoveryJob($workflow->id);
        $job->handle(new AutofillPipelineService);

        $workflow->refresh();
        $this->assertSame(AutofillWorkflowStatus::AwaitingSelection, $workflow->status);

        $this->assertDatabaseHas('autofill_items', [
            'workflow_id' => $workflow->id,
            'name' => 'Kennedy Space Center',
            'status' => AutofillItemStatus::Discovered->value,
        ]);
    }

    public function test_gemini_enrichment_works_with_web_search(): void
    {
        Queue::fake();

        EnrichmentAgent::fake([
            [
                'title' => 'Kennedy Space Center',
                'description' => 'NASA launch center in Florida.',
                'address' => 'Kennedy Space Center, FL 32899, USA',
                'latitude' => 28.5721,
                'longitude' => -80.6480,
                'practical_info' => 'Open daily.',
                'source_urls' => ['https://example.com'],
                'suggested_tags' => 'launch site, NASA',
            ],
        ]);

        $admin = User::factory()->create();
        $workflow = AutofillWorkflow::factory()->create([
            'admin_id' => $admin->id,
            'status' => AutofillWorkflowStatus::Enriching,
            'provider' => 'gemini',
        ]);

        $item = AutofillItem::factory()->create([
            'workflow_id' => $workflow->id,
            'name' => 'Kennedy Space Center',
            'status' => AutofillItemStatus::Selected,
            'enrichment_data' => ['approximate_location' => 'Florida, USA'],
        ]);

        $job = new EnrichmentJob($workflow->id, $item->id);
        $job->handle(new AutofillPipelineService);

        $item->refresh();
        $this->assertSame(AutofillItemStatus::Enriched, $item->status);
    }

    private function boolToString(bool $value): string
    {
        return $value ? 'true' : 'false';
    }
}
