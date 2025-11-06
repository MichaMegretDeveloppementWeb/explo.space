<?php

namespace App\Strategies\Translation;

use App\Contracts\Translation\TranslationStrategyInterface;

class TranslationResolver
{
    /** @var array<string, TranslationStrategyInterface> */
    private array $instances = [];

    public function __construct() {}

    /**
     * Get a translation strategy instance via the specified driver.
     */
    public function via(string $driver): TranslationStrategyInterface
    {
        // Return cached instance if exists
        if (isset($this->instances[$driver])) {
            return $this->instances[$driver];
        }

        // Resolve and cache the strategy instance
        $this->instances[$driver] = $this->resolve($driver);

        return $this->instances[$driver];
    }

    /**
     * Resolve the translation strategy instance for the given driver.
     */
    private function resolve(string $driver): TranslationStrategyInterface
    {
        return match ($driver) {
            'deepl' => new DeepLTranslationStrategy,
            default => throw new \InvalidArgumentException("Translation driver [{$driver}] is not supported"),
        };
    }
}
