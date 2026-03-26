<?php

namespace App\Ai\Providers;

use Laravel\Ai\Contracts\Providers\TextProvider;
use Laravel\Ai\Providers\Concerns\GeneratesText;
use Laravel\Ai\Providers\Concerns\HasTextGateway;
use Laravel\Ai\Providers\Concerns\StreamsText;
use Laravel\Ai\Providers\Provider;

class PerplexityProvider extends Provider implements TextProvider
{
    use GeneratesText;
    use HasTextGateway;
    use StreamsText;

    /**
     * Get the name of the default text model.
     */
    public function defaultTextModel(): string
    {
        return $this->config['models']['text']['default'] ?? 'sonar-pro';
    }

    /**
     * Get the name of the cheapest text model.
     */
    public function cheapestTextModel(): string
    {
        return $this->config['models']['text']['cheapest'] ?? 'sonar';
    }

    /**
     * Get the name of the smartest text model.
     */
    public function smartestTextModel(): string
    {
        return $this->config['models']['text']['smartest'] ?? 'sonar-pro';
    }
}
