<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Events\GoogleSsoLinked;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Laravel\Socialite\Facades\Socialite;

/**
 * Google OAuth Controller
 *
 * @author Pasukan BPM MOTAC
 * @trace D03-FR-001.3 (Google SSO Authentication)
 * @trace D04 §6.1 (Security)
 * @trace Requirements 15.6, 15.7 (Google SSO Flow)
 * @version 3.5.0
 * @created 2025-12-07
 */
class GoogleAuthController extends Controller
{
    /**
     * Redirect user to Google OAuth page
     */
    public function redirect(): RedirectResponse
    {
        return Socialite::driver('google')
            ->scopes(['email', 'profile'])
            ->redirect();
    }

    /**
     * Handle Google OAuth callback
     */
    public function callback(): RedirectResponse
    {
        try {
            $googleUser = Socialite::driver('google')->user();

            // Validate email domain
            $email = strtolower($googleUser->getEmail());
            if (!str_ends_with($email, '@motac.gov.my')) {
                return redirect()->route('login')
                    ->withErrors([
                        'email' => __('auth.email_domain_error'),
                    ]);
            }

            // Find or create user
            $user = User::where('email', $email)->first();

            if (!$user) {
                // Create new user from Google profile
                $user = User::create([
                    'name' => $googleUser->getName(),
                    'email' => $email,
                    'email_verified_at' => now(),
                    'google_id' => $googleUser->getId(),
                    'avatar' => $googleUser->getAvatar(),
                ]);

                Log::info('New user registered via Google SSO', [
                    'user_id' => $user->id,
                    'email' => $email,
                ]);
            } else {
                // Update Google ID and avatar if not set
                if (!$user->google_id) {
                    $user->update([
                        'google_id' => $googleUser->getId(),
                        'avatar' => $googleUser->getAvatar(),
                        'email_verified_at' => $user->email_verified_at ?? now(),
                    ]);

                    // Dispatch broadcast event for real-time UI update (Echo/Reverb)
                    // Frontend listeners in resources/js/portal-echo.js will receive this
                    GoogleSsoLinked::dispatch($user);

                    Log::info('Existing user linked to Google SSO', [
                        'user_id' => $user->id,
                        'email' => $email,
                    ]);
                }
            }

            // Log user in
            Auth::login($user, remember: true);

            session()->regenerate();

            return redirect()->intended(route('dashboard', absolute: false));

        } catch (\Exception $e) {
            Log::error('Google SSO authentication failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return redirect()->route('login')
                ->withErrors([
                    'email' => __('auth.google_sso_failed'),
                ]);
        }
    }
}
