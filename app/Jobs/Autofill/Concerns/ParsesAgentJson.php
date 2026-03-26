<?php

namespace App\Jobs\Autofill\Concerns;

use Illuminate\Support\Facades\Log;

trait ParsesAgentJson
{
    /**
     * Parse a JSON response from an AI agent's text output.
     *
     * Different providers format responses differently:
     * - OpenAI: bare JSON
     * - Anthropic: may wrap in <json> tags, markdown fences with preamble, or add <cite> tags
     *
     * @param  string  $text  Raw text from the agent response
     * @param  string|null  $key  Optional top-level key to extract (e.g. 'places')
     * @return array<string, mixed>
     */
    protected function parseJsonResponse(string $text, ?string $key = null): array
    {
        $json = $this->extractJson($text);

        $decoded = json_decode($json, true);

        // AI models sometimes embed literal control characters (newlines, tabs)
        // inside JSON string values, which is invalid JSON. Escape them and retry.
        if ($decoded === null && json_last_error() === JSON_ERROR_CTRL_CHAR) {
            $sanitized = str_replace(["\r\n", "\r", "\n", "\t"], ['\\n', '\\n', '\\n', '\\t'], $json);
            $decoded = json_decode($sanitized, true);
        }

        if (! is_array($decoded)) {
            Log::warning('[Autofill] Failed to parse JSON from agent response', [
                'text_preview' => mb_substr($text, 0, 200),
                'json_error' => json_last_error_msg(),
            ]);

            return [];
        }

        if ($key !== null) {
            return $decoded[$key] ?? [];
        }

        return $decoded;
    }

    /**
     * Extract JSON from various AI response formats.
     */
    private function extractJson(string $text): string
    {
        $text = trim($text);

        // Strip Anthropic <cite> tags that pollute string values
        $text = preg_replace('/<cite[^>]*>.*?<\/cite>/s', '', $text);

        // 1. Try bare JSON (entire text is valid JSON)
        if (str_starts_with($text, '{') || str_starts_with($text, '[')) {
            return $text;
        }

        // 2. Extract from <json>...</json> XML tags (Anthropic)
        if (preg_match('/<json>\s*(.*?)\s*<\/json>/s', $text, $matches)) {
            return trim($matches[1]);
        }

        // 3. Extract from markdown code fences (non-anchored to handle preamble)
        if (preg_match('/```(?:json)?\s*\n(.*?)\n\s*```/s', $text, $matches)) {
            return trim($matches[1]);
        }

        // 4. Last resort: find the first { ... } block in the text
        if (preg_match('/(\{.*\})/s', $text, $matches)) {
            return trim($matches[1]);
        }

        return $text;
    }
}
