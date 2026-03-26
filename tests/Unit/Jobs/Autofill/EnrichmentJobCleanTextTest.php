<?php

namespace Tests\Unit\Jobs\Autofill;

use App\Jobs\Autofill\EnrichmentJob;
use ReflectionMethod;
use Tests\TestCase;

class EnrichmentJobCleanTextTest extends TestCase
{
    private EnrichmentJob $job;

    protected function setUp(): void
    {
        parent::setUp();

        $this->job = new EnrichmentJob(workflowId: 1, itemId: 1);
    }

    public function test_removes_markdown_links(): void
    {
        $text = 'Visit the [Kennedy Space Center](https://www.nasa.gov/ksc) for launches.';

        $this->assertSame(
            'Visit the Kennedy Space Center for launches.',
            $this->cleanTextField($text)
        );
    }

    public function test_removes_parenthetical_citations(): void
    {
        $text = 'The museum opened in 1976 (https://example.com/source). It has many exhibits.';

        $this->assertSame(
            'The museum opened in 1976. It has many exhibits.',
            $this->cleanTextField($text)
        );
    }

    public function test_removes_parenthetical_source_citations(): void
    {
        $text = 'The center was built in 1962 (source: https://nasa.gov/history/ksc).';

        $this->assertSame(
            'The center was built in 1962.',
            $this->cleanTextField($text)
        );
    }

    public function test_removes_standalone_urls(): void
    {
        $text = 'More info at https://example.com/place and also http://other.com/info here.';

        $result = $this->cleanTextField($text);

        $this->assertStringNotContainsString('https://', $result);
        $this->assertStringNotContainsString('http://', $result);
    }

    public function test_removes_markdown_bold_and_italic(): void
    {
        $text = 'The **Kennedy Space Center** is a *major* launch facility.';

        $this->assertSame(
            'The Kennedy Space Center is a major launch facility.',
            $this->cleanTextField($text)
        );
    }

    public function test_collapses_excessive_whitespace(): void
    {
        $text = "First paragraph.\n\n\n\n\nSecond paragraph.";

        $this->assertSame(
            "First paragraph.\n\nSecond paragraph.",
            $this->cleanTextField($text)
        );
    }

    public function test_splits_long_monolithic_text_into_paragraphs(): void
    {
        // Build a long text with 6 sentences and no paragraph breaks
        $text = 'The Kennedy Space Center is located in Florida. It was established in 1962. '
            .'NASA uses it for space launches. The facility spans over 144,000 acres. '
            .'Visitors can tour the launch pads. The Saturn V rocket is on display there.';

        $result = $this->cleanTextField($text);

        $this->assertStringContainsString("\n\n", $result);
    }

    public function test_does_not_split_short_text(): void
    {
        $text = 'A short description without paragraph breaks.';

        $this->assertSame($text, $this->cleanTextField($text));
    }

    public function test_preserves_existing_paragraph_breaks(): void
    {
        $text = "First paragraph about the place.\n\nSecond paragraph with more details.";

        $this->assertSame($text, $this->cleanTextField($text));
    }

    public function test_clean_source_url_removes_utm_params(): void
    {
        $url = 'https://example.com/page?id=123&utm_source=openai&utm_medium=chat&keep=yes';

        $result = $this->cleanSourceUrl($url);

        $this->assertStringContainsString('id=123', $result);
        $this->assertStringContainsString('keep=yes', $result);
        $this->assertStringNotContainsString('utm_source', $result);
        $this->assertStringNotContainsString('utm_medium', $result);
    }

    public function test_clean_source_url_preserves_url_without_query(): void
    {
        $url = 'https://example.com/page';

        $this->assertSame($url, $this->cleanSourceUrl($url));
    }

    public function test_clean_source_url_preserves_non_tracking_params(): void
    {
        $url = 'https://example.com/search?q=space+museum&lang=en';

        $this->assertSame($url, $this->cleanSourceUrl($url));
    }

    /**
     * Call the private cleanTextField method via reflection.
     */
    private function cleanTextField(string $text): string
    {
        $method = new ReflectionMethod(EnrichmentJob::class, 'cleanTextField');

        return $method->invoke($this->job, $text);
    }

    /**
     * Call the private cleanSourceUrl method via reflection.
     */
    private function cleanSourceUrl(string $url): string
    {
        $method = new ReflectionMethod(EnrichmentJob::class, 'cleanSourceUrl');

        return $method->invoke($this->job, $url);
    }
}
