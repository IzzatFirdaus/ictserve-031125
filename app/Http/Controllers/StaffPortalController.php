<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Enums\LoanStatus;
use App\Models\HelpdeskTicket;
use App\Models\LoanApplication;
use App\Services\HybridHelpdeskService;
use App\Services\LoanApplicationService;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware as HasMiddlewareContract;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\View\View;

/**
 * Controller: StaffPortalController
 *
 * Handles authenticated staff portal operations including dashboard,
 * submission history, and guest submission claiming.
 *
 * @see D03-FR-022.1 (Authenticated staff portal)
 * @see D03-FR-022.2 (Staff dashboard)
 * @see D03-FR-022.6 (Guest submission claiming)
 * @see D04 §6.4 (Staff Portal Controller)
 *
 * @version 1.0.0
 *
 * @author Pasukan BPM MOTAC
 *
 * @created 2025-11-03
 */
class StaffPortalController extends Controller implements HasMiddlewareContract
{
    /**
     * Register the controller middleware.
     *
     * @return array<int, \Illuminate\Routing\Controllers\Middleware>
     */
    public static function middleware(): array
    {
        return [
            new Middleware('auth'),
            new Middleware('verified'),
        ];
    }

    /**
     * Create a new controller instance.
     */
    public function __construct(
        private LoanApplicationService $loanApplications,
        private HybridHelpdeskService $helpdeskService
    ) {}

    /**
     * Display the staff dashboard.
     */
    public function index(): View
    {
        $user = auth()->user();

        // Get statistics
        $openTickets = $user->helpdeskTickets()->where('status', 'open')->count();
        $activeLoans = $user->loanApplications()->where('status', 'approved')->count();

        // Get pending approvals count (for approvers only)
        $pendingApprovals = $user->canApprove()
            ? LoanApplication::query()
                ->where('status', LoanStatus::UNDER_REVIEW)
                ->whereRaw('LOWER(approver_email) = ?', [strtolower((string) $user->email)])
                ->count()
            : 0;

        // Get resolved tickets this month
        $resolvedThisMonth = $user->helpdeskTickets()
            ->where('status', 'resolved')
            ->whereMonth('resolved_at', now()->month)
            ->whereYear('resolved_at', now()->year)
            ->count();

        // Get recent submissions
        $recentTickets = $user->helpdeskTickets()
            ->with(['category', 'division'])
            ->latest()
            ->take(5)
            ->get();

        $recentApplications = $user->loanApplications()
            ->with(['asset', 'approver'])
            ->latest()
            ->take(5)
            ->get();

        return view('staff.dashboard', compact(
            'openTickets',
            'activeLoans',
            'pendingApprovals',
            'resolvedThisMonth',
            'recentTickets',
            'recentApplications'
        ));
    }

    /**
     * Display the user profile page.
     */
    public function profile(): View
    {
        $user = auth()->user();

        return view('staff.profile', compact('user'));
    }

    /**
     * Claim a guest submission by linking it to the authenticated user.
     */
    public function claim(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email',
            'submission_type' => 'required|in:ticket,loan',
            'submission_id' => 'required|integer',
        ]);

        $user = auth()->user();

        // Verify email matches
        if ($user->email !== $validated['email']) {
            return back()->withErrors(['email' => 'Email does not match your account.']);
        }

        // Claim the submission
        if ($validated['submission_type'] === 'ticket') {
            $ticket = HelpdeskTicket::findOrFail($validated['submission_id']);

            // Verify it's a guest submission and email matches
            if (! $ticket->isGuestSubmission() || $ticket->guest_email !== $user->email) {
                return back()->withErrors(['submission' => 'Unable to claim this ticket.']);
            }

            // Link ticket to user
            try {
                $this->helpdeskService->claimGuestTicket($ticket, $user);
            } catch (\Throwable $exception) {
                return back()->withErrors([
                    'submission' => $exception->getMessage(),
                ]);
            }

            return redirect()->route('staff.tickets.show', $ticket)
                ->with('success', 'Ticket claimed successfully.');
        } else {
            $application = LoanApplication::findOrFail($validated['submission_id']);

            // Verify it's a guest submission and email matches
            if (! $application->isGuestSubmission() || $application->applicant_email !== $user->email) {
                return back()->withErrors(['submission' => 'Unable to claim this application.']);
            }

            // Link application to user
            try {
                $this->loanApplications->claimGuestApplication($application, $user);
            } catch (\Throwable $exception) {
                return back()->withErrors([
                    'submission' => $exception->getMessage(),
                ]);
            }

            return redirect()->route('staff.loans.show', $application)
                ->with('success', 'Application claimed successfully.');
        }
    }
}
