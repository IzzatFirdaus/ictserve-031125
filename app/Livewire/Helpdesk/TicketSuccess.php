<?php

declare(strict_types=1);

namespace App\Livewire\Helpdesk;

use Illuminate\View\View;
use Livewire\Attributes\Layout;
use Livewire\Component;

/**
 * Ticket Success Component
 *
 * Displays success confirmation after helpdesk ticket submission.
 * Shows ticket number, confirmation message, and next steps.
 *
 * @trace D03-FR-011, D14-§9.3
 *
 * @requirements 20.4, 20.5
 */
#[Layout('layouts.front')]
class TicketSuccess extends Component
{
    public ?string $ticketNumber = null;

    public bool $canClaim = false;

    public function mount(): void
    {
        $ticketNumber = session('ticket_number');
        $canClaim = session('can_claim', false);

        $this->ticketNumber = is_string($ticketNumber) ? $ticketNumber : null;
        $this->canClaim = is_bool($canClaim) ? $canClaim : false;

        // Clear session data after retrieving
        session()->forget(['ticket_number', 'can_claim']);
    }

    public function render(): \Illuminate\View\View: View
    {
        return view('livewire.helpdesk.ticket-success');
    }
}
