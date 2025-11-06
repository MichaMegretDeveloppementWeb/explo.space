<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class RecaptchaRule implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string, ?string=): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        // Si le token est vide, échec immédiat
        if (empty($value)) {
            $fail(__('web/validation/place-request.recaptcha.missing'));

            return;
        }

        try {
            // Appel à l'API Google reCAPTCHA
            $response = Http::asForm()->post('https://www.google.com/recaptcha/api/siteverify', [
                'secret' => config('recaptcha.secret_key'),
                'response' => $value,
                'remoteip' => request()->ip(),
            ]);

            $result = $response->json();

            // Vérifier le succès de la vérification
            if (! $response->successful() || ! isset($result['success'])) {
                Log::warning('reCAPTCHA verification failed: API error', [
                    'status' => $response->status(),
                    'result' => $result,
                ]);
                $fail(__('web/validation/place-request.recaptcha.failed'));

                return;
            }

            // Vérifier que la vérification a réussi
            if (! $result['success']) {
                Log::warning('reCAPTCHA verification failed: not successful', [
                    'error_codes' => $result['error-codes'] ?? [],
                ]);
                $fail(__('web/validation/place-request.recaptcha.failed'));

                return;
            }

            // Vérifier le score (reCAPTCHA v3)
            $minScore = config('recaptcha.min_score', 0.5);
            $score = $result['score'] ?? 0;

            if ($score < $minScore) {
                Log::warning('reCAPTCHA score too low', [
                    'score' => $score,
                    'min_score' => $minScore,
                ]);
                $fail(__('web/validation/place-request.recaptcha.low_score'));

                return;
            }

        } catch (\Exception $e) {
            // En cas d'erreur, logger et rejeter
            Log::error('reCAPTCHA verification exception', [
                'error' => $e->getMessage(),
            ]);
            $fail(__('web/validation/place-request.recaptcha.error'));
        }
    }
}
