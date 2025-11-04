<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Services\DualApprovalService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Email Approval Controller
 *
 * Handles email-based approval workflows for Grade 41+ officers (no login required).
 *
 * @see D03-FR-002.1 Email approval workflow
 * @see D03-FR-002.3 Token-based approval processing
 * @see D03-FR-012.1 Email approval links
 */
class EmailApprovalController extends Controller
{
    public function __construct(
        private DualApprovalService $approvalService
    ) {}

    /**
     * Show approval decision page
     *
     * @param  string  $token  Approval token
     */
    public function show(string $token): View|RedirectResponse
    {
        $application = \App\Models\LoanApplication::where('approval_token', $token)->first();

        if (! $application) {
            return redirect()->route('welcome')->with('error', __('loan.approval.invalid_token'));
        }

        if (! $application->isTokenValid($token)) {
            return redirect()->route('welcome')->with('error', __('loan.approval.token_expired'));
        }

        return view('loan.approval.show', compact('application', 'token'));
    }

    /**
     * Process approval decision via email link
     */
    public function approve(Request $request, string $token): RedirectResponse
    {
        $request->validate([
            'remarks' => 'nullable|string|max:500',
        ]);

        $result = $this->approvalService->processEmailApproval(
            $token,
            true, // Approved
            $request->input('remarks')
        );

        if ($result['success']) {
            return redirect()->route('loan.approval.success')
                ->with('success', $result['message'])
                ->with('application', $result['application']);
        }

        return redirect()->route('welcome')
            ->with('error', $result['message']);
    }

    /**
     * Process rejection decision via email link
     */
    public function decline(Request $request, string $token): RedirectResponse
    {
        $request->validate([
            'remarks' => 'required|string|max:500',
        ]);

        $result = $this->approvalService->processEmailApproval(
            $token,
            false, // Declined
            $request->input('remarks')
        );

        if ($result['success']) {
            return redirect()->route('loan.approval.success')
                ->with('success', $result['message'])
                ->with('application', $result['application']);
        }

        return redirect()->route('welcome')
            ->with('error', $result['message']);
    }

    /**
     * Show approval success page
     */
    public function success(): View
    {
        return view('loan.approval.success');
    }
}
