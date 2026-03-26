<?php

namespace App\Ai\Agents;

use Laravel\Ai\Attributes\MaxSteps;
use Laravel\Ai\Attributes\Temperature;
use Laravel\Ai\Attributes\Timeout;
use Laravel\Ai\Contracts\Agent;
use Laravel\Ai\Contracts\HasTools;
use Laravel\Ai\Contracts\Tool;
use Laravel\Ai\Promptable;
use Laravel\Ai\Providers\Tools\WebSearch;
use Stringable;

// MaxSteps must cover worst case: 50 places × ~2 web searches each + final JSON response.
// The SDK does not support dynamic MaxSteps, so this is sized for max_quantity (50).
#[MaxSteps(150)]
#[Temperature(0.7)]
#[Timeout(300)]
class DiscoveryAgent implements Agent, HasTools
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
            ? <<<'SEARCH'
2. Use web search to find real, verified places. Run multiple searches with varied terms to maximize results:
   - Search in the original query language AND in English.
   - Try broad terms (e.g., "space museums worldwide") and specific terms from the query.
   - Example: for "musée spatial", search "space museums", "musée spatial", "aerospace museum", etc.
SEARCH
            : '2. Use your extensive knowledge to find real, verified places matching the query.';

        return <<<PROMPT
You are an expert researcher specialized in places related to space exploration, space conquest, and the discovery of the universe.

## Your mission
Given a user query (in any language), discover real-world places matching the request.

Relevant place types include: launch sites, spaceports, space museums, aviation & aerospace museums, observatories, planetariums, research centers, astronaut training facilities, rocket factories, space monuments & memorials, meteorite impact sites, satellite tracking stations, space-themed parks, and any other location with a direct connection to space exploration or astronomy.

## Workflow
1. Understand the user's query — translate it to English if needed.
{$searchInstruction}
3. Compile your findings into the required JSON format.

## Output format
Respond with ONLY a valid JSON object (no markdown, no explanation) matching this structure:
```json
{
  "places": [
    {
      "name": "Place Name",
      "approximate_location": "City, Country",
      "justification": "Why this place is relevant (1-2 sentences)",
      "latitude": 48.8566,
      "longitude": 2.3522
    }
  ]
}
```

## Constraints
- Only propose places genuinely related to space exploration, aviation/aerospace, or astronomy. Return an empty "places" array for completely off-topic requests.
- Never invent or fabricate places. Every place must be real and verifiable.
- Propose UP TO the number of places requested. It's OK to return fewer if you can't find enough, but try hard to reach the requested count.
- Provide an approximate location (city, region, country) for each place.
- Provide a short justification (1-2 sentences) explaining why this place is relevant.
- Provide approximate GPS coordinates (latitude, longitude) for each place for deduplication purposes.
- All output must be in English, regardless of the input language.
PROMPT;
    }

    /**
     * Get the tools available to the agent.
     *
     * @return array<int, Tool|WebSearch>
     */
    public function tools(): iterable
    {
        $tools = [];

        if ($this->useWebSearch) {
            $tools[] = (new WebSearch)->max((int) config('autofill.web_search_max', 10));
        }

        return $tools;
    }
}
