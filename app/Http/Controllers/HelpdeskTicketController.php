<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\HelpdeskTicket;
use App\Services\HybridHelpdeskService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class HelpdeskTicketController extends Controller
{
    public function __construct(
        private readonly HybridHelpdeskService $helpdeskService
    ) {}

    /**
     * Claim a guest ticket by an authenticated user
     */
    public function claim(Request $request, HelpdeskTicket $ticket): RedirectResponse
    {
        try {
            // Verify the authenticated user can claim this ticket
            $user = $request->user();

            if ($user === null) {
                return back()->with('error', __('helpdesk.authentication_required'));
            }

            if (! $ticket->isGuestSubmission()) {
                return back()->with('error', __('helpdesk.ticket_already_claimed'));
            }

            if ($ticket->guest_email !== $user->email) {
                return back()->with('error', __('helpdesk.cannot_claim_ticket_different_email'));
            }

            // Claim the ticket
            $this->helpdeskService->claimGuestTicket($ticket, $user);

            return redirect()
                ->route('helpdesk.authenticated.ticket.show', $ticket)
                ->with('success', __('helpdesk.ticket_claimed_successfully'));
        } catch (\Exception) {
            return back()->with('error', __('helpdesk.ticket_claim_failed'));
        }
    }
}
