<?php

namespace Tests\Unit\Ai\Agents;

use App\Ai\Agents\EnrichmentAgent;
use Laravel\Ai\Contracts\HasStructuredOutput;
use Laravel\Ai\Providers\Tools\WebSearch;
use Tests\TestCase;

class EnrichmentAgentTest extends TestCase
{
    public function test_instructions_contain_key_directives(): void
    {
        $agent = EnrichmentAgent::make();
        $instructions = (string) $agent->instructions();

        $this->assertStringContainsString('space', $instructions);
        $this->assertStringContainsString('description', $instructions);
        $this->assertStringContainsString('GPS', $instructions);
        $this->assertStringContainsString('practical', $instructions);
        $this->assertStringContainsString('English', $instructions);
        $this->assertStringContainsString('tags', $instructions);
    }

    public function test_tools_with_web_search_enabled(): void
    {
        $agent = EnrichmentAgent::make(useWebSearch: true);
        $tools = iterator_to_array($agent->tools());

        $this->assertCount(1, $tools);
        $this->assertInstanceOf(WebSearch::class, $tools[0]);
    }

    public function test_tools_without_web_search(): void
    {
        $agent = EnrichmentAgent::make(useWebSearch: false);
        $tools = iterator_to_array($agent->tools());

        $this->assertCount(0, $tools);
    }

    public function test_default_constructor_enables_web_search(): void
    {
        $agent = EnrichmentAgent::make();
        $tools = iterator_to_array($agent->tools());

        $this->assertCount(1, $tools);
        $this->assertInstanceOf(WebSearch::class, $tools[0]);
    }

    public function test_instructions_mention_web_search_when_enabled(): void
    {
        $agent = EnrichmentAgent::make(useWebSearch: true);
        $instructions = (string) $agent->instructions();

        $this->assertStringContainsString('Use web search to find detailed', $instructions);
    }

    public function test_instructions_mention_knowledge_when_web_search_disabled(): void
    {
        $agent = EnrichmentAgent::make(useWebSearch: false);
        $instructions = (string) $agent->instructions();

        $this->assertStringContainsString('Use your extensive knowledge', $instructions);
    }

    public function test_agent_implements_structured_output(): void
    {
        $agent = EnrichmentAgent::make();

        $this->assertInstanceOf(HasStructuredOutput::class, $agent);
    }

    public function test_schema_defines_all_required_fields(): void
    {
        $agent = EnrichmentAgent::make();
        $schema = $agent->schema(new \Illuminate\JsonSchema\JsonSchema);

        $this->assertArrayHasKey('title', $schema);
        $this->assertArrayHasKey('description', $schema);
        $this->assertArrayHasKey('address', $schema);
        $this->assertArrayHasKey('latitude', $schema);
        $this->assertArrayHasKey('longitude', $schema);
        $this->assertArrayHasKey('practical_info', $schema);
        $this->assertArrayHasKey('source_urls', $schema);
        $this->assertArrayHasKey('suggested_tags', $schema);
    }

    public function test_fake_returns_structured_response(): void
    {
        EnrichmentAgent::fake([
            [
                'title' => 'Kennedy Space Center',
                'description' => 'NASA\'s primary launch center located on the east coast of Florida.',
                'address' => 'Kennedy Space Center, FL 32899, USA',
                'latitude' => 28.5721,
                'longitude' => -80.6480,
                'practical_info' => 'Open daily 9AM-5PM. Adult tickets $57.',
                'source_urls' => ['https://www.kennedyspacecenter.com'],
                'suggested_tags' => 'launch site, NASA, historical, museum',
            ],
        ]);

        $agent = EnrichmentAgent::make();
        $response = $agent->prompt('Enrich: Kennedy Space Center, Cape Canaveral, Florida');

        $this->assertNotNull($response);

        EnrichmentAgent::assertPrompted(fn ($prompt) => str_contains($prompt->prompt, 'Kennedy Space Center'));
    }
}
