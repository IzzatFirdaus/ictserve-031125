<?php

declare(strict_types=1);

namespace App\Livewire\Helpdesk;

use Livewire\Component;

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

    public function render(): \Illuminate\Contracts\View\View
    {
        return view('livewire.helpdesk.ticket-success')
            ->layout('layouts.guest');
    }
}
