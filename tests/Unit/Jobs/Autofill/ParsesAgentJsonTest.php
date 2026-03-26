<?php

namespace Tests\Unit\Jobs\Autofill;

use App\Jobs\Autofill\Concerns\ParsesAgentJson;
use Tests\TestCase;

class ParsesAgentJsonTest extends TestCase
{
    use ParsesAgentJson;

    public function test_parses_clean_json(): void
    {
        $json = '{"places": [{"name": "Test Place"}]}';
        $result = $this->parseJsonResponse($json, 'places');

        $this->assertCount(1, $result);
        $this->assertSame('Test Place', $result[0]['name']);
    }

    public function test_parses_json_with_markdown_fences(): void
    {
        $json = "```json\n{\"places\": [{\"name\": \"Test Place\"}]}\n```";
        $result = $this->parseJsonResponse($json, 'places');

        $this->assertCount(1, $result);
        $this->assertSame('Test Place', $result[0]['name']);
    }

    public function test_parses_json_with_plain_fences(): void
    {
        $json = "```\n{\"title\": \"Kennedy Space Center\"}\n```";
        $result = $this->parseJsonResponse($json);

        $this->assertSame('Kennedy Space Center', $result['title']);
    }

    public function test_returns_empty_array_for_invalid_json(): void
    {
        $result = $this->parseJsonResponse('not valid json');

        $this->assertSame([], $result);
    }

    public function test_returns_empty_array_for_empty_string(): void
    {
        $result = $this->parseJsonResponse('');

        $this->assertSame([], $result);
    }

    public function test_returns_empty_array_when_key_missing(): void
    {
        $json = '{"other": "data"}';
        $result = $this->parseJsonResponse($json, 'places');

        $this->assertSame([], $result);
    }

    public function test_returns_full_object_without_key(): void
    {
        $json = '{"title": "Test", "latitude": 28.5}';
        $result = $this->parseJsonResponse($json);

        $this->assertSame('Test', $result['title']);
        $this->assertSame(28.5, $result['latitude']);
    }

    public function test_handles_whitespace_around_json(): void
    {
        $json = "  \n  {\"places\": [{\"name\": \"Place\"}]}  \n  ";
        $result = $this->parseJsonResponse($json, 'places');

        $this->assertCount(1, $result);
    }

    public function test_returns_empty_places_array(): void
    {
        $json = '{"places": []}';
        $result = $this->parseJsonResponse($json, 'places');

        $this->assertSame([], $result);
    }

    public function test_handles_literal_newlines_in_json_values(): void
    {
        // AI models sometimes output literal newlines inside JSON string values
        $json = "{\"title\": \"Space Center\", \"description\": \"First paragraph.\n\nSecond paragraph.\"}";
        $result = $this->parseJsonResponse($json);

        $this->assertSame('Space Center', $result['title']);
        $this->assertStringContainsString('First paragraph.', $result['description']);
        $this->assertStringContainsString('Second paragraph.', $result['description']);
    }

    public function test_handles_literal_tabs_in_json_values(): void
    {
        $json = "{\"title\": \"Space\tCenter\"}";
        $result = $this->parseJsonResponse($json);

        $this->assertStringContainsString('Space', $result['title']);
    }

    public function test_parses_json_wrapped_in_xml_json_tags(): void
    {
        $text = "I'll help you find space-related places.\n<json>\n{\"places\": [{\"name\": \"Safran Museum\"}]}\n</json>";
        $result = $this->parseJsonResponse($text, 'places');

        $this->assertCount(1, $result);
        $this->assertSame('Safran Museum', $result[0]['name']);
    }

    public function test_parses_markdown_fences_with_preamble_text(): void
    {
        $text = "I'll help you find space-related places matching your query.\n```json\n{\"places\": [{\"name\": \"Esrange Space Center\"}]}\n```";
        $result = $this->parseJsonResponse($text, 'places');

        $this->assertCount(1, $result);
        $this->assertSame('Esrange Space Center', $result[0]['name']);
    }

    public function test_parses_json_with_preamble_and_no_fences(): void
    {
        $text = "Here are the results:\n{\"places\": [{\"name\": \"Kennedy Space Center\"}]}";
        $result = $this->parseJsonResponse($text, 'places');

        $this->assertCount(1, $result);
        $this->assertSame('Kennedy Space Center', $result[0]['name']);
    }

    public function test_strips_anthropic_cite_tags(): void
    {
        $json = '{"places": [{"name": "Air and Space Museum", "justification": "Major museum<cite index=\"1\">source</cite> with exhibits."}]}';
        $result = $this->parseJsonResponse($json, 'places');

        $this->assertCount(1, $result);
        $this->assertStringNotContainsString('<cite', $result[0]['justification']);
        $this->assertStringContainsString('Major museum', $result[0]['justification']);
    }
}
