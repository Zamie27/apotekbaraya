<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ReCaptcha implements ValidationRule
{
    /**
     * Minimum score threshold for reCAPTCHA v3
     * Score ranges from 0.0 (likely bot) to 1.0 (likely human)
     */
    private float $threshold;

    /**
     * Create a new rule instance.
     *
     * @param float $threshold Minimum score threshold (default: 0.5)
     */
    public function __construct(float $threshold = 0.5)
    {
        $this->threshold = $threshold;
    }

    /**
     * Run the validation rule.
     *
     * @param  \Closure(string, ?string=): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        // Skip reCAPTCHA validation in development mode for dev tokens
        if (config('app.env') !== 'production' && str_starts_with($value, 'dev-token-')) {
            Log::info('Development mode: Skipping reCAPTCHA validation', [
                'token' => $value,
                'environment' => config('app.env')
            ]);
            return;
        }
        
        // Check if reCAPTCHA is configured
        $secretKey = config('services.recaptcha.secret_key');
        
        if (empty($secretKey) || $secretKey === 'your_secret_key_here') {
            Log::warning('reCAPTCHA secret key not configured');
            $fail('reCAPTCHA configuration is missing.');
            return;
        }

        // Validate the token with Google reCAPTCHA API
        try {
            $response = Http::asForm()->post('https://www.google.com/recaptcha/api/siteverify', [
                'secret' => $secretKey,
                'response' => $value,
                'remoteip' => request()->ip(),
            ]);

            $result = $response->json();

            // Check if the request was successful
            if (!$response->successful()) {
                Log::error('reCAPTCHA API request failed', ['response' => $result]);
                $fail('reCAPTCHA verification failed. Please try again.');
                return;
            }

            // Check if reCAPTCHA verification was successful
            if (!$result['success']) {
                Log::warning('reCAPTCHA verification failed', [
                    'error_codes' => $result['error-codes'] ?? [],
                    'ip' => request()->ip()
                ]);
                $fail('reCAPTCHA verification failed. Please try again.');
                return;
            }

            // Check the score (reCAPTCHA v3 specific)
            $score = $result['score'] ?? 0;
            if ($score < $this->threshold) {
                Log::warning('reCAPTCHA score too low', [
                    'score' => $score,
                    'threshold' => $this->threshold,
                    'ip' => request()->ip()
                ]);
                $fail('Security verification failed. Please try again.');
                return;
            }

            // Log successful verification
            Log::info('reCAPTCHA verification successful', [
                'score' => $score,
                'ip' => request()->ip()
            ]);

        } catch (\Exception $e) {
            Log::error('reCAPTCHA verification exception', [
                'message' => $e->getMessage(),
                'ip' => request()->ip()
            ]);
            $fail('reCAPTCHA verification failed. Please try again.');
        }
    }
}
