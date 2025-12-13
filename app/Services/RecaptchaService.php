<?php

declare(strict_types=1);

namespace App\Services;

use App\Services\Contracts\RecaptchaServiceInterface;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * reCAPTCHA Enterprise Service Implementation
 *
 * Provides server-side verification of reCAPTCHA Enterprise tokens.
 * Uses Google Cloud reCAPTCHA Enterprise API for verification.
 *
 * @see Requirements 14.2 - Invisible reCAPTCHA on all guest forms
 * @see https://cloud.google.com/recaptcha-enterprise/docs/create-assessment
 */
class RecaptchaService implements RecaptchaServiceInterface
{
    private const API_URL = 'https://recaptchaenterprise.googleapis.com/v1/projects/%s/assessments?key=%s';

    private string $siteKey;

    private string $apiKey;

    private string $projectId;

    private float $minScore;

    private bool $enabled;

    /** @var array<string, string> */
    private array $actions;

    public function __construct()
    {
        $this->siteKey = config('recaptcha.site_key', '');
        $this->apiKey = config('recaptcha.api_key', '');
        $this->projectId = config('recaptcha.project_id', '');
        $this->minScore = (float) config('recaptcha.min_score', 0.5);
        $this->enabled = (bool) config('recaptcha.enabled', true);
        $this->actions = config('recaptcha.actions', []);
    }

    /**
     * {@inheritDoc}
     */
    public function verify(string $token, string $action, ?string $ipAddress = null): array
    {
        // Return success if reCAPTCHA is disabled (for testing)
        if (! $this->enabled) {
            return [
                'success' => true,
                'score' => 1.0,
                'action' => $action,
                'error_codes' => [],
            ];
        }

        // Validate configuration
        if (empty($this->siteKey) || empty($this->apiKey) || empty($this->projectId)) {
            Log::warning('reCAPTCHA Enterprise not configured properly', [
                'has_site_key' => ! empty($this->siteKey),
                'has_api_key' => ! empty($this->apiKey),
                'has_project_id' => ! empty($this->projectId),
            ]);

            // Fail open in development, fail closed in production
            if (app()->environment('local', 'testing')) {
                return [
                    'success' => true,
                    'score' => 1.0,
                    'action' => $action,
                    'error_codes' => ['configuration-missing'],
                ];
            }

            return [
                'success' => false,
                'score' => 0.0,
                'action' => $action,
                'error_codes' => ['configuration-error'],
            ];
        }

        try {
            $url = sprintf(self::API_URL, $this->projectId, $this->apiKey);

            $payload = [
                'event' => [
                    'token' => $token,
                    'siteKey' => $this->siteKey,
                    'expectedAction' => $action,
                ],
            ];

            if ($ipAddress) {
                $payload['event']['userIpAddress'] = $ipAddress;
            }

            $response = Http::timeout(10)
                ->withHeaders(['Content-Type' => 'application/json'])
                ->post($url, $payload);

            if (! $response->successful()) {
                Log::error('reCAPTCHA Enterprise API error', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);

                return [
                    'success' => false,
                    'score' => 0.0,
                    'action' => $action,
                    'error_codes' => ['api-error'],
                ];
            }

            $data = $response->json();

            // Extract risk analysis
            $riskAnalysis = $data['riskAnalysis'] ?? [];
            $score = (float) ($riskAnalysis['score'] ?? 0.0);
            $reasons = $riskAnalysis['reasons'] ?? [];

            // Extract token properties
            $tokenProperties = $data['tokenProperties'] ?? [];
            $valid = $tokenProperties['valid'] ?? false;
            $tokenAction = $tokenProperties['action'] ?? '';

            // Verify token validity
            if (! $valid) {
                Log::warning('reCAPTCHA token invalid', [
                    'reasons' => $reasons,
                    'token_properties' => $tokenProperties,
                ]);

                return [
                    'success' => false,
                    'score' => $score,
                    'action' => $tokenAction,
                    'error_codes' => ['invalid-token'],
                ];
            }

            // Verify action matches
            if ($tokenAction !== $action) {
                Log::warning('reCAPTCHA action mismatch', [
                    'expected' => $action,
                    'actual' => $tokenAction,
                ]);

                return [
                    'success' => false,
                    'score' => $score,
                    'action' => $tokenAction,
                    'error_codes' => ['action-mismatch'],
                ];
            }

            // Check score threshold
            $success = $score >= $this->minScore;

            if (! $success) {
                Log::info('reCAPTCHA score below threshold', [
                    'score' => $score,
                    'threshold' => $this->minScore,
                    'reasons' => $reasons,
                ]);
            }

            return [
                'success' => $success,
                'score' => $score,
                'action' => $tokenAction,
                'error_codes' => $success ? [] : ['score-too-low'],
            ];
        } catch (\Exception $e) {
            Log::error('reCAPTCHA verification exception', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return [
                'success' => false,
                'score' => 0.0,
                'action' => $action,
                'error_codes' => ['verification-failed'],
            ];
        }
    }

    /**
     * {@inheritDoc}
     */
    public function isEnabled(): bool
    {
        return $this->enabled;
    }

    /**
     * {@inheritDoc}
     */
    public function getSiteKey(): string
    {
        return $this->siteKey;
    }

    /**
     * {@inheritDoc}
     */
    public function getMinScore(): float
    {
        return $this->minScore;
    }

    /**
     * {@inheritDoc}
     */
    public function getActionName(string $formType): string
    {
        return $this->actions[$formType] ?? $formType;
    }
}
