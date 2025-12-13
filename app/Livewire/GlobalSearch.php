<?php

namespace App\Livewire;

use App\Models\HelpdeskTicket;
use App\Models\LoanApplication;
use App\Models\User;
use Livewire\Component;

class GlobalSearch extends Component
{
    public string $query = '';
    public array $results = [];

    public function updatedQuery()
    {
        $this->results = [];

        if (strlen($this->query) < 3) {
            return;
        }

        $this->searchTickets();
        $this->searchLoans();
        
        // Only allow staff/admins to search users
        if (auth()->user()->hasRole(['Staff', 'Approver', 'Super Admin'])) {
            $this->searchUsers();
        }
    }

    protected function searchTickets()
    {
        $tickets = HelpdeskTicket::query()
            ->where('ticket_number', 'like', "%{$this->query}%")
            ->orWhere('subject', 'like', "%{$this->query}%")
            ->take(5)
            ->get();

        foreach ($tickets as $ticket) {
            $this->results[] = [
                'type' => 'Ticket',
                'title' => $ticket->ticket_number,
                'subtitle' => $ticket->subject,
                'url' => route('staff.tickets.show', $ticket),
                'icon' => 'heroicon-o-ticket',
            ];
        }
    }

    protected function searchLoans()
    {
        $loans = LoanApplication::query()
            ->where('application_number', 'like', "%{$this->query}%")
            ->take(5)
            ->get();

        foreach ($loans as $loan) {
            $this->results[] = [
                'type' => 'Loan',
                'title' => $loan->application_number,
                'subtitle' => $loan->status->label(),
                'url' => route('staff.loans.show', $loan),
                'icon' => 'heroicon-o-computer-desktop',
            ];
        }
    }

    protected function searchUsers()
    {
        $users = User::query()
            ->where('name', 'like', "%{$this->query}%")
            ->orWhere('email', 'like', "%{$this->query}%")
            ->take(5)
            ->get();

        foreach ($users as $user) {
            $this->results[] = [
                'type' => 'User',
                'title' => $user->name,
                'subtitle' => $user->email,
                'url' => '#', // No user profile view for now, maybe add later
                'icon' => 'heroicon-o-user',
            ];
        }
    }

    public function render(): \Illuminate\View\View
    {
        return view('livewire.global-search');
    }
}
