<?php

namespace Tests\Unit\Ai\Agents;

use App\Ai\Agents\AltTextAgent;
use Tests\TestCase;

class AltTextAgentTest extends TestCase
{
    public function test_instructions_contain_key_directives(): void
    {
        $agent = AltTextAgent::make();
        $instructions = (string) $agent->instructions();

        $this->assertStringContainsString('alt text', $instructions);
        $this->assertStringContainsString('SEO', $instructions);
        $this->assertStringContainsString('150', $instructions);
        $this->assertStringContainsString('English', $instructions);
    }

    public function test_agent_has_no_tools(): void
    {
        $agent = AltTextAgent::make();

        $this->assertFalse($agent instanceof \Laravel\Ai\Contracts\HasTools);
    }

    public function test_agent_has_no_structured_output(): void
    {
        $agent = AltTextAgent::make();

        $this->assertFalse($agent instanceof \Laravel\Ai\Contracts\HasStructuredOutput);
    }

    public function test_fake_returns_text_response(): void
    {
        AltTextAgent::fake([
            'Kennedy Space Center launch pad 39A with Space Shuttle on the platform, Cape Canaveral, Florida',
        ]);

        $agent = AltTextAgent::make();
        $response = $agent->prompt('Place: Kennedy Space Center, Type: Launch facility, Location: Cape Canaveral, FL. Image: launch_pad.jpg');

        $this->assertNotNull($response);
        $this->assertStringContainsString('Kennedy Space Center', $response->text);

        AltTextAgent::assertPrompted(fn ($prompt) => str_contains($prompt->prompt, 'Kennedy Space Center'));
    }
}
