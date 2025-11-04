<?php

declare(strict_types=1);

namespace App\Livewire\Helpdesk;

use App\Models\HelpdeskComment;
use App\Models\HelpdeskTicket;
use App\Services\HybridHelpdeskService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Validate;
use Livewire\Component;

class TicketDetails extends Component
{
    public HelpdeskTicket $ticket;

    #[Validate('required|string|min:3|max:2000')]
    public string $newComment = '';

    public bool $addingComment = false;

    public function mount(HelpdeskTicket $ticket): void
    {
        abort_unless($this->canAccess($ticket), 403);

        $this->ticket = $ticket->load(['category', 'assignedUser', 'comments.user', 'attachments']);
    }

    public function refreshTicket(): void
    {
        $this->ticket->refresh()->load(['category', 'assignedUser', 'comments.user', 'attachments']);
    }

    public function claimTicket(): void
    {
        $user = Auth::user();
        app(HybridHelpdeskService::class)->claimGuestTicket($this->ticket, $user);

        $this->refreshTicket();

        session()->flash('message', __('Tiket berjaya dituntut ke akaun anda.'));
    }

    public function addComment(): void
    {
        $this->addingComment = true;
        $this->validate();

        if (! $this->canComment()) {
            throw ValidationException::withMessages([
                'newComment' => __('Anda tidak dibenarkan menambah komen untuk tiket ini.'),
            ]);
        }

        $user = Auth::user();

        HelpdeskComment::create([
            'helpdesk_ticket_id' => $this->ticket->id,
            'user_id' => $user->id,
            'commenter_name' => $user->name,
            'commenter_email' => $user->email,
            'comment' => $this->newComment,
            'is_internal' => false,
        ]);

        $this->newComment = '';
        $this->addingComment = false;
        $this->refreshTicket();
    }

    protected function canAccess(HelpdeskTicket $ticket): bool
    {
        $user = Auth::user();

        if ($ticket->user_id === $user->id) {
            return true;
        }

        if ($ticket->guest_email === $user->email) {
            return true;
        }

        return false;
    }

    protected function canComment(): bool
    {
        return $this->ticket->user_id === Auth::id()
            || $this->ticket->guest_email === Auth::user()->email;
    }

    public function render()
    {
        return view('livewire.helpdesk.ticket-details');
    }
}
