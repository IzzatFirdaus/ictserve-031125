<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\HelpdeskTicket;
use App\Models\LoanApplication;
use Illuminate\Http\Request;
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
class StaffPortalController extends Controller
{
    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        $this->middleware(['auth', 'verified']);
    }

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
            ? LoanApplication::where('status', 'pending_approval')
                ->where('approver_id', $user->id)
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
            $ticket->update(['user_id' => $user->id]);

            return redirect()->route('staff.tickets.show', $ticket)
                ->with('success', 'Ticket claimed successfully.');
        } else {
            $application = LoanApplication::findOrFail($validated['submission_id']);

            // Verify it's a guest submission and email matches
            if (! $application->isGuestSubmission() || $application->applicant_email !== $user->email) {
                return back()->withErrors(['submission' => 'Unable to claim this application.']);
            }

            // Link application to user
            $application->update(['user_id' => $user->id]);

            return redirect()->route('staff.loans.show', $application)
                ->with('success', 'Application claimed successfully.');
        }
    }
}
