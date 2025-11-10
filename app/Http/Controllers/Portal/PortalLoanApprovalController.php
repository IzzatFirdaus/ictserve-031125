<?php

declare(strict_types=1);

namespace App\Http\Controllers\Portal;

use App\Enums\LoanStatus;
use App\Http\Controllers\Controller;
use App\Mail\LoanApprovalNotification;
use App\Models\LoanApplication;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

/**
 * PortalLoanApprovalController
 *
 * Handles authenticated portal approval actions (approve / reject) for loan applications.
 *
 * trace: D03-FR-012.3; D04 §6.1; D11 §8
 */
class PortalLoanApprovalController extends Controller
{
    /**
     * Approve an application assigned to the current approver.
     */
    public function approve(Request $request, LoanApplication $application): RedirectResponse
    {
        $user = Auth::user();

        // Only assigned approver (or higher) can approve
        abort_unless(
            $user !== null
            && in_array(strtolower($user->role ?? ''), ['approver', 'admin', 'superuser'], true)
            && strtolower((string) $application->approver_email) === strtolower((string) $user->email),
            403
        );

        $comments = (string) $request->input('comments', '');

        $application->update([
            'status' => LoanStatus::APPROVED,
            'approved_at' => now(),
            'approved_by_name' => $user->name,
            'approval_method' => 'portal',
            'approval_remarks' => $comments,
        ]);

        // Queue email notification to applicant
        if (! empty($application->applicant_email)) {
            Mail::to($application->applicant_email)->queue(new LoanApprovalNotification($application));
        }

        return back()->with('status', __('Application approved.'));
    }

    /**
     * Reject an application assigned to the current approver.
     */
    public function reject(Request $request, LoanApplication $application): RedirectResponse
    {
        $user = Auth::user();

        abort_unless(
            $user !== null
            && in_array(strtolower($user->role ?? ''), ['approver', 'admin', 'superuser'], true)
            && strtolower((string) $application->approver_email) === strtolower((string) $user->email),
            403
        );

        $comments = (string) $request->input('comments', '');

        $application->update([
            'status' => LoanStatus::REJECTED,
            'rejected_reason' => $comments,
            'approval_method' => 'portal',
            'approved_at' => null,
            'approved_by_name' => null,
        ]);

        return back()->with('status', __('Application rejected.'));
    }
}
