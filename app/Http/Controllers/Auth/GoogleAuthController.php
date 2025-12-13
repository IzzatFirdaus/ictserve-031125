<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Contracts\GoogleSsoServiceInterface;
use App\Exceptions\InvalidEmailDomainException;
use App\Http\Controllers\Controller;
use App\Models\SsoAuditLog;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\InvalidStateException;

/**
 * Google OAuth Controller
 *
 * Handles Google SSO authentication flow with comprehensive error handling
 * and user-friendly messages in Bahasa Melayu.
 *
 * @author Pasukan BPM MOTAC
 *
 * @trace D03-FR-001.3 (Google SSO Authentication)
 * @trace D04 §6.1 (Security)
 * @trace Requirements 2.1, 2.2, 2.3 (Error Handling and User Feedback)
 *
 * @version 3.6.0
 *
 * @created 2025-12-07
 *
 * @updated 2025-12-13 - Refactored to use GoogleSsoService with enhanced error handling
 */
class GoogleAuthController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @param  GoogleSsoServiceInterface  $ssoService  The Google SSO service
     */
    public function __construct(
        private readonly GoogleSsoServiceInterface $ssoService
    ) {}

    /**
     * Redirect user to Google OAuth page
     *
     * Initiates the OAuth flow by redirecting to Google's authentication page.
     * Checks service health before redirecting to provide early failure detection.
     */
    public function redirect(): RedirectResponse
    {
        // Check if Google SSO service is available before redirecting
        $healthStatus = $this->ssoService->getHealthStatus();

        if (! $healthStatus['available']) {
            Log::warning('Google SSO service unavailable during redirect', [
                'message' => $healthStatus['message'],
                'ip_address' => request()->ip(),
            ]);

            return $this->handleServiceUnavailable($healthStatus['message'] ?? null);
        }

        return Socialite::driver('google')->redirect();
    }

    /**
     * Handle Google OAuth callback
     *
     * Processes the OAuth callback from Google, validates the user,
     * and authenticates them into the system.
     */
    public function callback(): RedirectResponse
    {
        try {
            $googleUser = Socialite::driver('google')->user();

            // Use service to create or update user (handles domain validation)
            $user = $this->ssoService->createOrUpdateUser($googleUser);

            // Check if user account is active
            if (! $this->isUserActive($user)) {
                $this->ssoService->logAuthenticationAttempt(
                    $user->email,
                    false,
                    'Account disabled',
                    $user->id
                );

                return $this->handleAccountDisabled($user->email);
            }

            // Log user in with remember me enabled
            Auth::login($user, remember: true);

            // Regenerate session for security
            session()->regenerate();

            Log::info('User authenticated via Google SSO', [
                'user_id' => $user->id,
                'email' => $user->email,
                'ip_address' => request()->ip(),
            ]);

            return redirect()->intended(route('dashboard', absolute: false));
        } catch (InvalidEmailDomainException $e) {
            return $this->handleDomainError($e);
        } catch (InvalidStateException $e) {
            return $this->handleOAuthStateError($e);
        } catch (ConnectException $e) {
            return $this->handleNetworkError($e);
        } catch (RequestException $e) {
            return $this->handleRequestError($e);
        } catch (\Exception $e) {
            return $this->handleGeneralError($e);
        }
    }

    /**
     * Check if user account is active
     *
     * @param  \App\Models\User  $user  The user to check
     * @return bool True if user is active
     */
    private function isUserActive(\App\Models\User $user): bool
    {
        // Check is_active property if it exists
        if (property_exists($user, 'is_active') || isset($user->is_active)) {
            return (bool) $user->is_active;
        }

        return true;
    }

    /**
     * Handle service unavailable error
     *
     * Returns user to login page with appropriate error message
     * when Google SSO service is not available.
     */
    private function handleServiceUnavailable(?string $message = null): RedirectResponse
    {
        SsoAuditLog::logFailure([
            'email' => 'unknown',
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'error_type' => 'network_error',
            'error_message' => $message ?? __('auth.google_sso_unavailable'),
        ]);

        return redirect()->route('login')
            ->withErrors([
                'email' => __('auth.google_sso_unavailable'),
            ])
            ->with('sso_fallback', true);
    }

    /**
     * Handle domain validation error
     *
     * Logs the attempt and returns user-friendly error message
     * when email domain is not @motac.gov.my.
     *
     * @param  InvalidEmailDomainException  $e  The domain validation exception
     */
    private function handleDomainError(InvalidEmailDomainException $e): RedirectResponse
    {
        Log::warning('Google SSO domain validation failed', [
            'email' => $e->getEmail(),
            'provided_domain' => $e->getProvidedDomain(),
            'allowed_domains' => $e->getAllowedDomains(),
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);

        return redirect()->route('login')
            ->withErrors([
                'email' => __('auth.google_sso_domain_error'),
            ])
            ->with('sso_fallback', true);
    }

    /**
     * Handle OAuth state validation error
     *
     * Occurs when the OAuth state parameter is invalid or expired,
     * typically due to session issues or CSRF protection.
     *
     * @param  InvalidStateException  $e  The OAuth state exception
     */
    private function handleOAuthStateError(InvalidStateException $e): RedirectResponse
    {
        Log::warning('Google SSO OAuth state validation failed', [
            'error' => $e->getMessage(),
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);

        // Log authentication attempt for audit
        $this->ssoService->logAuthenticationAttempt(
            'unknown',
            false,
            'OAuth state validation failed: '.$e->getMessage()
        );

        return redirect()->route('login')
            ->withErrors([
                'email' => __('auth.google_sso_oauth_error'),
            ])
            ->with('sso_fallback', true);
    }

    /**
     * Handle network connection error
     *
     * Occurs when unable to connect to Google's OAuth servers.
     *
     * @param  ConnectException  $e  The connection exception
     */
    private function handleNetworkError(ConnectException $e): RedirectResponse
    {
        Log::error('Google SSO network connection failed', [
            'error' => $e->getMessage(),
            'ip_address' => request()->ip(),
        ]);

        // Log authentication attempt for audit
        $this->ssoService->logAuthenticationAttempt(
            'unknown',
            false,
            'Network connection failed: '.$e->getMessage()
        );

        return redirect()->route('login')
            ->withErrors([
                'email' => __('auth.google_sso_network_error'),
            ])
            ->with('sso_fallback', true);
    }

    /**
     * Handle HTTP request error from Google API
     *
     * Occurs when Google's API returns an error response.
     *
     * @param  RequestException  $e  The request exception
     */
    private function handleRequestError(RequestException $e): RedirectResponse
    {
        $statusCode = $e->hasResponse() ? $e->getResponse()?->getStatusCode() : null;

        Log::error('Google SSO API request failed', [
            'error' => $e->getMessage(),
            'status_code' => $statusCode,
            'ip_address' => request()->ip(),
        ]);

        // Log authentication attempt for audit
        $this->ssoService->logAuthenticationAttempt(
            'unknown',
            false,
            'API request failed: '.$e->getMessage()
        );

        // Determine appropriate error message based on status code
        $errorKey = match ($statusCode) {
            401, 403 => 'auth.google_sso_oauth_error',
            500, 502, 503, 504 => 'auth.google_sso_unavailable',
            default => 'auth.google_sso_failed',
        };

        return redirect()->route('login')
            ->withErrors([
                'email' => __($errorKey),
            ])
            ->with('sso_fallback', true);
    }

    /**
     * Handle account disabled error
     *
     * Returns user to login page when their account is disabled.
     *
     * @param  string  $email  The user's email address
     */
    private function handleAccountDisabled(string $email): RedirectResponse
    {
        Log::warning('Google SSO login attempt for disabled account', [
            'email' => $email,
            'ip_address' => request()->ip(),
        ]);

        return redirect()->route('login')
            ->withErrors([
                'email' => __('auth.google_sso_account_disabled'),
            ])
            ->with('sso_fallback', true);
    }

    /**
     * Handle general/unexpected errors
     *
     * Catches any unexpected exceptions and provides a generic
     * user-friendly error message while logging full details.
     *
     * @param  \Exception  $e  The exception
     */
    private function handleGeneralError(\Exception $e): RedirectResponse
    {
        Log::error('Google SSO authentication failed with unexpected error', [
            'error' => $e->getMessage(),
            'exception_class' => get_class($e),
            'trace' => $e->getTraceAsString(),
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);

        // Log authentication attempt for audit
        $this->ssoService->logAuthenticationAttempt(
            'unknown',
            false,
            'Unexpected error: '.$e->getMessage()
        );

        return redirect()->route('login')
            ->withErrors([
                'email' => __('auth.google_sso_failed'),
            ])
            ->with('sso_fallback', true);
    }
}
