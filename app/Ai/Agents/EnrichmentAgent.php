<?php

namespace App\Ai\Agents;

use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Ai\Attributes\MaxSteps;
use Laravel\Ai\Attributes\Temperature;
use Laravel\Ai\Attributes\Timeout;
use Laravel\Ai\Contracts\Agent;
use Laravel\Ai\Contracts\HasStructuredOutput;
use Laravel\Ai\Contracts\HasTools;
use Laravel\Ai\Contracts\Tool;
use Laravel\Ai\Promptable;
use Laravel\Ai\Providers\Tools\WebSearch;
use Stringable;

// MaxSteps sized for single-place enrichment: ~3-5 web searches + final JSON response.
// The SDK does not support dynamic MaxSteps, so 30 provides ample headroom.
#[MaxSteps(30)]
#[Temperature(0.4)]
#[Timeout(120)]
class EnrichmentAgent implements Agent, HasStructuredOutput, HasTools
{
    use Promptable;

    public function __construct(
        private readonly bool $useWebSearch = true,
    ) {}

    /**
     * Get the instructions that the agent should follow.
     */
    public function instructions(): Stringable|string
    {
        $searchInstruction = $this->useWebSearch
            ? '1. Use web search to find detailed, up-to-date information about the place.'
            : '1. Use your extensive knowledge to provide detailed, accurate information about the place.';

        return <<<PROMPT
You are a specialist in documenting places related to space exploration and the discovery of the universe.

## Your mission
Given a place name and its approximate location, produce a complete, accurate, and engaging documentation of that place.

## Workflow
{$searchInstruction}
2. Cross-reference multiple sources to ensure accuracy.
3. Produce the structured output with all required fields.

## Output requirements

### title
The official, commonly used English name of the place.

### description
A well-written description of 2-3 paragraphs in English. Be informative, engaging, and factual. Cover:
- What the place is and its significance in space exploration
- Key historical events or achievements associated with it
- What visitors can see or experience today (if applicable)
IMPORTANT formatting rules for description:
- Separate paragraphs with \n\n inside the string value.
- Do NOT include any URLs, citations, links, or markdown formatting (no [text](url), no bold, no italic).
- URLs belong ONLY in the source_urls field, never in description text.

### address
The complete physical address (street, city, state/region, postal code, country). If no precise street address exists, provide the most specific address available.

### latitude / longitude
GPS coordinates with maximum precision (at least 5 decimal places). If GPS coordinates are provided in the prompt, verify them and correct if needed. Otherwise, look them up. Accuracy is critical — these will be used on an interactive map.

### practical_info
Practical visitor information in English: opening hours, admission fees, how to get there, accessibility, tips. If information is unavailable, explicitly state "Information not available" — NEVER invent practical details.
Same formatting rules as description: no URLs, no markdown, no citations. Plain text only.

### source_urls
An array of 1-5 URLs of the sources you used for this documentation. Only include real, verifiable URLs.

### suggested_tags
Suggest relevant thematic tags in English as a comma-separated string (e.g., "launch site, NASA, historical, museum"). These are free-text suggestions to help the admin categorize the place — not definitive assignments.

## Constraints
- All output must be in English.
- Never fabricate information. If something is unknown, say so explicitly.
- Coordinates must be as precise as possible.
- Do not include promotional or subjective language.
PROMPT;
    }

    /**
     * Get the agent's structured output schema definition.
     *
     * @return array<string, \Illuminate\JsonSchema\Types\Type>
     */
    public function schema(JsonSchema $schema): array
    {
        return [
            'title' => $schema->string()->required(),
            'description' => $schema->string()->required(),
            'address' => $schema->string()->required(),
            'latitude' => $schema->number()->required(),
            'longitude' => $schema->number()->required(),
            'practical_info' => $schema->string()->required(),
            'source_urls' => $schema->array()->items($schema->string())->required(),
            'suggested_tags' => $schema->string()->required(),
        ];
    }

    /**
     * Get the tools available to the agent.
     *
     * @return array<int, Tool|WebSearch>
     */
    public function tools(): iterable
    {
        if ($this->useWebSearch) {
            return [
                (new WebSearch)->max((int) config('autofill.web_search_max', 10)),
            ];
        }

        return [];
    }
}
