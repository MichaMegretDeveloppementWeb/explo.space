<?php

namespace App\Strategies\Translation;

use App\Contracts\Translation\TranslationStrategyInterface;
use App\Exceptions\Translation\TranslationException;
use DeepL\AuthorizationException;
use DeepL\QuotaExceededException;
use DeepL\Translator;
use DeepL\TranslatorOptions;
use GuzzleHttp\Client as GuzzleClient;
use Illuminate\Support\Facades\Log;

class DeepLTranslationStrategy implements TranslationStrategyInterface
{
    private Translator $translator;

    public function __construct()
    {
        $apiKey = config('deepl.api_key');

        if (empty($apiKey)) {
            throw new \RuntimeException('DeepL API key is not configured');
        }

        // Configuration du client HTTP (désactiver SSL pour dev local)
        $httpClient = new GuzzleClient([
            'verify' => app()->environment('production'),
        ]);

        // Initialiser le client DeepL avec le SDK officiel
        $this->translator = new Translator(
            $apiKey,
            [TranslatorOptions::HTTP_CLIENT => $httpClient]
        );
    }

    /**
     * Check if the DeepL API key is valid and has remaining quota
     *
     * @return array{character_count: int, character_limit: int, is_quota_exceeded: bool}
     *
     * @throws TranslationException
     */
    public function checkUsage(): array
    {
        try {
            $usage = $this->translator->getUsage();

            return [
                'character_count' => $usage->character->count ?? 0,
                'character_limit' => $usage->character->limit ?? 0,
                'is_quota_exceeded' => ($usage->character->limit ?? 0) > 0 && ($usage->character->count ?? 0) >= ($usage->character->limit ?? 0),
            ];
        } catch (AuthorizationException $e) {
            Log::error('DeepL API key is invalid', [
                'error' => $e->getMessage(),
            ]);
            throw TranslationException::invalidApiKey();
        } catch (QuotaExceededException $e) {
            Log::error('DeepL quota exceeded', [
                'error' => $e->getMessage(),
            ]);
            throw TranslationException::quotaExceeded();
        } catch (\Exception $e) {
            Log::error('DeepL usage check failed', [
                'error' => $e->getMessage(),
            ]);
            throw TranslationException::connectionFailed($e->getMessage());
        }
    }

    public function translate(string $text, string $sourceLang, string $targetLang): string
    {
        if (empty($text)) {
            return '';
        }

        try {
            $result = $this->translator->translateText(
                $text,
                $this->mapSourceLanguageCode($sourceLang),
                $this->mapTargetLanguageCode($targetLang)
            );

            return $result->text;

        } catch (AuthorizationException $e) {
            Log::error('DeepL API key is invalid', [
                'error' => $e->getMessage(),
                'source_lang' => $sourceLang,
                'target_lang' => $targetLang,
            ]);
            throw TranslationException::invalidApiKey();
        } catch (QuotaExceededException $e) {
            Log::error('DeepL quota exceeded', [
                'error' => $e->getMessage(),
                'source_lang' => $sourceLang,
                'target_lang' => $targetLang,
            ]);
            throw TranslationException::quotaExceeded();
        } catch (\Exception $e) {
            Log::error('DeepL translation failed', [
                'error' => $e->getMessage(),
                'source_lang' => $sourceLang,
                'target_lang' => $targetLang,
            ]);
            throw TranslationException::translationFailed($e->getMessage());
        }
    }

    /**
     * @throws TranslationException
     */
    public function translateBatch(array $texts, string $sourceLang, string $targetLang): array
    {
        if (empty($texts)) {
            return [];
        }

        $textValues = array_values($texts);
        $textKeys = array_keys($texts);

        try {
            $results = $this->translator->translateText(
                $textValues,
                $this->mapSourceLanguageCode($sourceLang),
                $this->mapTargetLanguageCode($targetLang)
            );

            $translations = [];
            foreach ($textKeys as $index => $key) {
                $translations[$key] = $results[$index]->text;
            }

            return $translations;

        } catch (AuthorizationException $e) {
            Log::error('DeepL API key is invalid', [
                'error' => $e->getMessage(),
                'source_lang' => $sourceLang,
                'target_lang' => $targetLang,
                'texts_count' => count($texts),
            ]);
            throw TranslationException::invalidApiKey();
        } catch (QuotaExceededException $e) {
            Log::error('DeepL quota exceeded', [
                'error' => $e->getMessage(),
                'source_lang' => $sourceLang,
                'target_lang' => $targetLang,
                'texts_count' => count($texts),
            ]);
            throw TranslationException::quotaExceeded();
        } catch (\Exception $e) {
            Log::error('DeepL batch translation failed', [
                'error' => $e->getMessage(),
                'source_lang' => $sourceLang,
                'target_lang' => $targetLang,
                'texts_count' => count($texts),
            ]);
            throw TranslationException::translationFailed($e->getMessage());
        }
    }

    /**
     * Detect the language of a text using DeepL's translation detection.
     *
     * Uses a double-check strategy to prevent false positives:
     * 1. First attempt: Translate text to EN to detect source language
     * 2. If translated text equals source text (unchanged), this may indicate:
     *    - Text is already in English, OR
     *    - Text is too short/ambiguous for reliable detection
     * 3. Second verification: Translate first 20 chars to FR to confirm detection
     * 4. Return 'unknown' if:
     *    - Second translation also unchanged (truly ambiguous)
     *    - Two detections disagree (inconsistent results)
     *
     * This approach significantly reduces false positives in language detection
     * by requiring consistency across multiple translation attempts.
     *
     * @param  string  $text  The text to detect language for
     * @return string Language code (e.g., 'fr', 'en') or 'unknown' if detection fails
     */
    public function detectLanguage(string $text): string
    {
        if (empty($text)) {
            return 'unknown';
        }

        try {
            // Traduire vers EN pour détecter la langue source
            $result = $this->translator->translateText(
                $text,
                null,
                $this->mapTargetLanguageCode('en')
            );

            if ($result->detectedSourceLang) {

                if ($text === $result->text) {

                    $textBis = mb_substr($text, 0, 20);

                    $resultBis = $this->translator->translateText(
                        $textBis,
                        null,
                        $this->mapTargetLanguageCode('fr')
                    );

                    if ($textBis === $resultBis->text) {
                        return 'unknown';
                    }

                    if ($result->detectedSourceLang !== $resultBis->detectedSourceLang) {
                        return 'unknown';
                    }

                }

                return $this->normalizeLanguageCode($result->detectedSourceLang);
            }

            return 'unknown';

        } catch (\Exception $e) {
            Log::warning('DeepL language detection failed', [
                'error' => $e->getMessage(),
                'text_length' => strlen($text),
            ]);

            return 'unknown';
        }
    }

    public function getSupportedLanguages(): array
    {
        try {
            $targetLanguages = $this->translator->getTargetLanguages();

            $languages = [];
            foreach ($targetLanguages as $language) {
                $languages[] = $this->normalizeLanguageCode($language->code);
            }

            return $languages;

        } catch (\Exception $e) {
            Log::warning('DeepL get languages failed', [
                'error' => $e->getMessage(),
            ]);

            return $this->getFallbackLanguages();
        }
    }

    /**
     * Map application language codes to DeepL source language format (lowercase).
     * DeepL expects source languages as lowercase ISO 639-1 codes.
     */
    private function mapSourceLanguageCode(string $lang): string
    {
        return match (strtolower($lang)) {
            'fr' => 'fr',
            'en' => 'en',
            'es' => 'es',
            'de' => 'de',
            'it' => 'it',
            'pt' => 'pt',
            'nl' => 'nl',
            'pl' => 'pl',
            default => strtolower($lang),
        };
    }

    /**
     * Map application language codes to DeepL target language format.
     * DeepL expects target languages as specific uppercase codes.
     */
    private function mapTargetLanguageCode(string $lang): string
    {
        return match (strtolower($lang)) {
            'fr' => 'FR',
            'en' => 'EN-GB',
            'es' => 'ES',
            'de' => 'DE',
            'it' => 'IT',
            'pt' => 'PT-PT',
            'nl' => 'NL',
            'pl' => 'PL',
            default => strtoupper($lang),
        };
    }

    /**
     * Normalize DeepL language codes to application format.
     */
    private function normalizeLanguageCode(string $lang): string
    {
        $normalized = strtolower($lang);

        return match ($normalized) {
            'en-gb', 'en-us', 'en' => 'en',
            'pt-pt', 'pt-br', 'pt' => 'pt',
            default => substr($normalized, 0, 2),
        };
    }

    /**
     * Fallback list of supported languages if API call fails.
     *
     * @return array<string>
     */
    private function getFallbackLanguages(): array
    {
        return ['fr', 'en', 'es', 'de', 'it', 'pt', 'nl', 'pl'];
    }
}
