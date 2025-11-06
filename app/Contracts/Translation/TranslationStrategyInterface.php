<?php

namespace App\Contracts\Translation;

interface TranslationStrategyInterface
{
    /**
     * Translate a single text from source language to target language.
     */
    public function translate(string $text, string $sourceLang, string $targetLang): string;

    /**
     * Translate multiple texts in batch from source language to target language.
     *
     * @param  array<string, string>  $texts  Associative array of key => text pairs
     * @return array<string, string> Associative array of key => translated text pairs
     */
    public function translateBatch(array $texts, string $sourceLang, string $targetLang): array;

    /**
     * Detect the language of the given text.
     */
    public function detectLanguage(string $text): string;

    /**
     * Get the list of supported language codes.
     *
     * @return array<string>
     */
    public function getSupportedLanguages(): array;
}
