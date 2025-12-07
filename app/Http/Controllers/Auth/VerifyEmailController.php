<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Events\EmailVerified;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Log;

/**
 * Email Verification Controller
 *
 * Handles email verification for self-registered MOTAC staff.
 * Implements 24-hour signed URL verification per D01 §4.3.
 *
 * @trace D01 §4.3 (Self-registration requirements)
 * @trace D03 SRS-AUTH-001 (Authentication requirements)
 * @trace Requirements 15.4, 15.5 (Email Verification Flow)
 *
 * @version 2.0.0
 *
 * @updated 2025-12-02 - Task 13.2: Enhanced with bilingual messages and audit logging
 */
class VerifyEmailController extends Controller
{
    /**
     * Mark the authenticated user's email address as verified.
     */
    public function __invoke(EmailVerificationRequest $request): RedirectResponse
    {
        $user = $request->user();

        // Check if already verified
        if ($user->hasVerifiedEmail()) {
            Log::info('Email verification attempted for already verified user', [
                'user_id' => $user->id,
                'email' => $user->email,
                'ip' => $request->ip(),
            ]);

            return redirect()
                ->intended(route('dashboard', absolute: false))
                ->with('status', 'already-verified')
                ->with('message', __('auth.verification_already_verified'));
        }

        // Fulfill the verification request
        $request->fulfill();

        // Dispatch broadcast event for real-time UI update (Echo/Reverb)
        // Frontend listeners in resources/js/portal-echo.js will receive this
        EmailVerified::dispatch($user);

        // Log successful verification
        Log::info('Email verified successfully', [
            'user_id' => $user->id,
            'email' => $user->email,
            'ip' => $request->ip(),
            'verified_at' => now()->toIso8601String(),
        ]);

        return redirect()
            ->intended(route('dashboard', absolute: false))
            ->with('status', 'verified')
            ->with('message', __('auth.verification_success'));
    }
}
