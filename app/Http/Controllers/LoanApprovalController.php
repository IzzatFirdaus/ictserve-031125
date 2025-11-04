<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Enums\LoanStatus;
use App\Mail\LoanApplicationDecision;
use App\Models\LoanApplication;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;

/**
 * Loan Approval Controller
 *
 * Handles email-based approval workflow for Grade 41+ officers.
 * Provides secure token-based approval without requiring system login.
 *
 * @component Controller
 *
 * @description WCAG 2.2 AA compliant approval workflow with secure token validation
 *
 * @author Pasukan BPM MOTAC
 *
 * @trace D03-FR-002.1 Email approval workflow
 * @trace D03-FR-002.3 Token-based approval processing
 * @trace Requirements 1.1, 2.1, 7.1, 8.1
 *
 * @wcag_level AA
 *
 * @version 1.0.0
 *
 * @created 2025-11-04
 */
class LoanApprovalController extends Controller
{
    /**
     * Show approval confirmation page
     *
     * @param  string  $token  Approval token from email link
     */
    public function showApprovalForm(string $token): View|RedirectResponse
    {
        $application = LoanApplication::where('approval_token', $token)->first();

        if (! $application) {
            return redirect()->route('welcome')
                ->with('error', __('asset_loan.approval.token_invalid'));
        }

        if (! $application->isTokenValid($token)) {
            return redirect()->route('welcome')
                ->with('error', __('asset_loan.approval.token_expired'));
        }

        return view('loans.approval-form', [
            'application' => $application,
            'token' => $token,
            'action' => 'approve',
        ]);
    }

    /**
     * Show decline confirmation page
     *
     * @param  string  $token  Approval token from email link
     */
    public function showDeclineForm(string $token): View|RedirectResponse
    {
        $application = LoanApplication::where('approval_token', $token)->first();

        if (! $application) {
            return redirect()->route('welcome')
                ->with('error', __('asset_loan.approval.token_invalid'));
        }

        if (! $application->isTokenValid($token)) {
            return redirect()->route('welcome')
                ->with('error', __('asset_loan.approval.token_expired'));
        }

        return view('loans.approval-form', [
            'application' => $application,
            'token' => $token,
            'action' => 'decline',
        ]);
    }

    /**
     * Process approval via email link
     *
     * @param  Request  $request  HTTP request with token and optional comments
     */
    public function approve(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'token' => 'required|string',
            'comments' => 'nullable|string|max:1000',
        ]);

        $application = LoanApplication::where('approval_token', $validated['token'])->first();

        if (! $application) {
            return redirect()->route('welcome')
                ->with('error', __('asset_loan.approval.token_invalid'));
        }

        if (! $application->isTokenValid($validated['token'])) {
            return redirect()->route('welcome')
                ->with('error', __('asset_loan.approval.token_expired'));
        }

        DB::beginTransaction();

        try {
            // Update application status
            $application->update([
                'status' => LoanStatus::APPROVED,
                'approved_by_name' => $application->approver_email,
                'approved_at' => now(),
                'approval_method' => 'email',
                'approval_remarks' => $validated['comments'] ?? 'Approved via email workflow',
                'approval_token' => null,
                'approval_token_expires_at' => null,
            ]);

            // Send confirmation email to applicant
            Mail::to($application->applicant_email)
                ->send(new LoanApplicationDecision($application, true));

            // Log approval action
            Log::info('Loan application approved via email', [
                'application_id' => $application->id,
                'application_number' => $application->application_number,
                'approver_email' => $application->approver_email,
                'approval_method' => 'email',
            ]);

            DB::commit();

            return redirect()->route('welcome')
                ->with('success', __('asset_loan.approval.approved_success', [
                    'application_number' => $application->application_number,
                ]));
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Failed to approve loan application via email', [
                'application_id' => $application->id ?? null,
                'error' => $e->getMessage(),
            ]);

            return redirect()->route('welcome')
                ->with('error', __('asset_loan.approval.approval_failed'));
        }
    }

    /**
     * Process decline via email link
     *
     * @param  Request  $request  HTTP request with token and required reason
     */
    public function decline(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'token' => 'required|string',
            'reason' => 'required|string|max:1000',
        ]);

        $application = LoanApplication::where('approval_token', $validated['token'])->first();

        if (! $application) {
            return redirect()->route('welcome')
                ->with('error', __('asset_loan.approval.token_invalid'));
        }

        if (! $application->isTokenValid($validated['token'])) {
            return redirect()->route('welcome')
                ->with('error', __('asset_loan.approval.token_expired'));
        }

        DB::beginTransaction();

        try {
            // Update application status
            $application->update([
                'status' => LoanStatus::REJECTED,
                'rejected_reason' => $validated['reason'],
                'approval_method' => 'email',
                'approval_remarks' => $validated['reason'],
                'approval_token' => null,
                'approval_token_expires_at' => null,
            ]);

            // Send confirmation email to applicant
            Mail::to($application->applicant_email)
                ->send(new LoanApplicationDecision($application, false));

            // Log decline action
            Log::info('Loan application declined via email', [
                'application_id' => $application->id,
                'application_number' => $application->application_number,
                'approver_email' => $application->approver_email,
                'approval_method' => 'email',
                'reason' => $validated['reason'],
            ]);

            DB::commit();

            return redirect()->route('welcome')
                ->with('success', __('asset_loan.approval.declined_success', [
                    'application_number' => $application->application_number,
                ]));
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Failed to decline loan application via email', [
                'application_id' => $application->id ?? null,
                'error' => $e->getMessage(),
            ]);

            return redirect()->route('welcome')
                ->with('error', __('asset_loan.approval.decline_failed'));
        }
    }
}
