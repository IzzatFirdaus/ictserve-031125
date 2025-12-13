<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Services\Contracts\RecaptchaServiceInterface;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * reCAPTCHA Verification Middleware
 *
 * Verifies reCAPTCHA Enterprise tokens on form submissions.
 * Applies invisible reCAPTCHA verification to guest forms.
 *
 * @see Requirements 14.2 - Invisible reCAPTCHA on all guest forms
 */
class VerifyRecaptcha
{
    public function __construct(
        private readonly RecaptchaServiceInterface $recaptchaService
    ) {}

    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  $action  The expected reCAPTCHA action name
     */
    public function handle(Request $request, Closure $next, string $action = 'submit'): Response
    {
        // Skip verification if reCAPTCHA is disabled
        if (! $this->recaptchaService->isEnabled()) {
            return $next($request);
        }

        // Skip verification for GET requests
        if ($request->isMethod('GET')) {
            return $next($request);
        }

        // Get the reCAPTCHA token from the request
        $token = $request->input('recaptcha_token') ?? $request->header('X-Recaptcha-Token');

        if (empty($token)) {
            return $this->failResponse($request, __('validation.recaptcha_required'));
        }

        // Verify the token
        $result = $this->recaptchaService->verify(
            $token,
            $this->recaptchaService->getActionName($action),
            $request->ip()
        );

        if (! $result['success']) {
            $errorMessage = match ($result['error_codes'][0] ?? 'unknown') {
                'invalid-token' => __('validation.recaptcha_invalid'),
                'action-mismatch' => __('validation.recaptcha_action_mismatch'),
                'score-too-low' => __('validation.recaptcha_suspicious'),
                'configuration-error' => __('validation.recaptcha_configuration_error'),
                default => __('validation.recaptcha_failed'),
            };

            return $this->failResponse($request, $errorMessage);
        }

        // Store the score in the request for logging purposes
        $request->attributes->set('recaptcha_score', $result['score']);

        return $next($request);
    }

    /**
     * Generate a failure response.
     */
    private function failResponse(Request $request, string $message): Response
    {
        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'message' => $message,
                'errors' => ['recaptcha' => [$message]],
            ], 422);
        }

        return back()
            ->withInput()
            ->withErrors(['recaptcha' => $message]);
    }
}
