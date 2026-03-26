<?php

namespace Tests\Unit\Ai\Agents;

use App\Ai\Agents\DiscoveryAgent;
use Laravel\Ai\Contracts\HasStructuredOutput;
use Laravel\Ai\Providers\Tools\WebSearch;
use Tests\TestCase;

class DiscoveryAgentTest extends TestCase
{
    public function test_instructions_contain_key_directives(): void
    {
        $agent = DiscoveryAgent::make();
        $instructions = (string) $agent->instructions();

        $this->assertStringContainsString('space', $instructions);
        $this->assertStringContainsString('English', $instructions);
        $this->assertStringContainsString('web search', $instructions);
    }

    public function test_instructions_mention_web_search_when_enabled(): void
    {
        $agent = DiscoveryAgent::make(useWebSearch: true);
        $instructions = (string) $agent->instructions();

        $this->assertStringContainsString('Use web search to find real', $instructions);
        $this->assertStringNotContainsString('Use your extensive knowledge', $instructions);
    }

    public function test_instructions_mention_knowledge_when_web_search_disabled(): void
    {
        $agent = DiscoveryAgent::make(useWebSearch: false);
        $instructions = (string) $agent->instructions();

        $this->assertStringContainsString('Use your extensive knowledge', $instructions);
        $this->assertStringNotContainsString('Use web search to find real', $instructions);
    }

    public function test_tools_with_web_search_enabled(): void
    {
        $agent = DiscoveryAgent::make(useWebSearch: true);
        $tools = iterator_to_array($agent->tools());

        $this->assertCount(1, $tools);
        $this->assertInstanceOf(WebSearch::class, $tools[0]);
    }

    public function test_tools_without_web_search(): void
    {
        $agent = DiscoveryAgent::make(useWebSearch: false);
        $tools = iterator_to_array($agent->tools());

        $this->assertCount(0, $tools);
    }

    public function test_default_constructor_enables_web_search(): void
    {
        $agent = DiscoveryAgent::make();
        $tools = iterator_to_array($agent->tools());

        $this->assertCount(1, $tools);
        $this->assertInstanceOf(WebSearch::class, $tools[0]);
    }

    public function test_agent_does_not_implement_structured_output(): void
    {
        // DiscoveryAgent uses text-based JSON response (not HasStructuredOutput)
        // because its schema contains nested objects (array of place objects) which
        // are incompatible across providers: OpenAI requires additionalProperties:false
        // on all nested objects, while Gemini rejects it entirely.
        $agent = DiscoveryAgent::make();

        $this->assertNotInstanceOf(HasStructuredOutput::class, $agent);
    }

    public function test_instructions_include_json_format(): void
    {
        $agent = DiscoveryAgent::make();
        $instructions = (string) $agent->instructions();

        $this->assertStringContainsString('JSON', $instructions);
        $this->assertStringContainsString('"places"', $instructions);
        $this->assertStringContainsString('"latitude"', $instructions);
        $this->assertStringContainsString('"longitude"', $instructions);
    }

    public function test_instructions_mention_gps_coordinates(): void
    {
        $agent = DiscoveryAgent::make();
        $instructions = (string) $agent->instructions();

        $this->assertStringContainsString('latitude', $instructions);
        $this->assertStringContainsString('longitude', $instructions);
    }

    public function test_fake_returns_response(): void
    {
        DiscoveryAgent::fake([
            [
                'places' => [
                    [
                        'name' => 'Baikonur Cosmodrome',
                        'approximate_location' => 'Baikonur, Kazakhstan',
                        'justification' => 'The world\'s first and largest operational space launch facility.',
                        'latitude' => 45.9646,
                        'longitude' => 63.3052,
                    ],
                ],
            ],
        ]);

        $agent = DiscoveryAgent::make();
        $response = $agent->prompt('Find space launch sites in Central Asia');

        $this->assertNotNull($response);

        DiscoveryAgent::assertPrompted('Find space launch sites in Central Asia');
    }

    public function test_fake_with_empty_places(): void
    {
        DiscoveryAgent::fake([
            ['places' => []],
        ]);

        $agent = DiscoveryAgent::make();
        $response = $agent->prompt('Find pizza restaurants');

        $this->assertNotNull($response);

        DiscoveryAgent::assertPrompted('Find pizza restaurants');
    }

    public function test_instructions_list_relevant_place_types(): void
    {
        $agent = DiscoveryAgent::make();
        $instructions = (string) $agent->instructions();

        $this->assertStringContainsString('launch sites', $instructions);
        $this->assertStringContainsString('observatories', $instructions);
        $this->assertStringContainsString('planetariums', $instructions);
        $this->assertStringContainsString('aviation', $instructions);
    }
}
