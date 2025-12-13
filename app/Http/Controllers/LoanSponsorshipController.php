<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\LoanApplication;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Loan Sponsorship Controller
 *
 * Handles responsible officer sponsorship acknowledgment workflow.
 * When applicant is not the responsible officer, the designated officer
 * must acknowledge sponsorship via secure token link sent by email.
 *
 * @author Pasukan BPM MOTAC
 * @trace D03-FR-002.5 (Responsible Officer Sponsorship)
 * @trace D04 §7.2 (Email Workflow)
 * @trace Requirements 15.3, 15.4 (Sponsorship Acknowledgment)
 * @version 3.5.0
 * @created 2025-12-07
 */
class LoanSponsorshipController extends Controller
{
    /**
     * Acknowledge responsible officer sponsorship via token
     *
     * Validates the sponsorship token, checks expiry, and updates
     * the application to mark sponsorship as acknowledged.
     *
     * @param Request $request
     * @param string $token Sponsorship token from email link
     * @return RedirectResponse
     */
    public function acknowledge(Request $request, string $token): RedirectResponse
    {
        try {
            // Find application by sponsorship token
            $application = LoanApplication::where('sponsorship_token', $token)
                ->whereNotNull('sponsorship_token')
                ->first();

            // Validate token exists
            if (!$application) {
                Log::warning('Invalid sponsorship token attempted', [
                    'token' => $token,
                    'ip' => $request->ip(),
                ]);

                return redirect()->route('guest.loan.status')
                    ->with('error', __('loan.invalid_sponsorship_token'));
            }

            // Check token expiry
            if ($application->sponsorship_token_expires_at < now()) {
                Log::warning('Expired sponsorship token attempted', [
                    'application_id' => $application->id,
                    'token' => $token,
                    'expired_at' => $application->sponsorship_token_expires_at,
                ]);

                return redirect()->route('guest.loan.status')
                    ->with('error', __('loan.sponsorship_token_expired'));
            }

            // Check if already acknowledged
            if ($application->responsible_officer_acknowledged_at) {
                return redirect()->route('guest.loan.status')
                    ->with('info', __('loan.sponsorship_already_acknowledged'));
            }

            // Mark sponsorship as acknowledged
            DB::transaction(function () use ($application) {
                $application->update([
                    'responsible_officer_acknowledged_at' => now(),
                    'sponsorship_token' => null, // Invalidate token after use
                    'sponsorship_token_expires_at' => null,
                ]);

                Log::info('Responsible officer sponsorship acknowledged', [
                    'application_id' => $application->id,
                    'reference_number' => $application->reference_number,
                    'responsible_officer' => $application->responsible_officer_name,
                ]);
            });

            // Redirect to success page with tracking number
            return redirect()->route('guest.loan.status')
                ->with('success', __('loan.sponsorship_acknowledged_successfully'))
                ->with('reference_number', $application->reference_number);

        } catch (\Exception $e) {
            Log::error('Sponsorship acknowledgment failed', [
                'token' => $token,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return redirect()->route('guest.loan.status')
                ->with('error', __('loan.sponsorship_acknowledgment_error'));
        }
    }
}
