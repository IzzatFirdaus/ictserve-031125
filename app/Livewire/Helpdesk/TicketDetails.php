<?php

declare(strict_types=1);

namespace App\Livewire\Helpdesk;

use App\Models\HelpdeskComment;
use App\Models\HelpdeskTicket;
use App\Models\User;
use App\Services\HybridHelpdeskService;
use App\Traits\OptimizedLivewireComponent;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Validate;
use Livewire\Component;

class TicketDetails extends Component
{
    use OptimizedLivewireComponent;

    /**
     * Define relationships to eager load for N+1 prevention
     *
     * @return array<int, string>
     */
    protected function getEagerLoadRelationships(): array
    {
        return ['category', 'assignedUser', 'comments.user', 'attachments'];
    }

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
        assert($user instanceof User);
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
        assert($user instanceof User);

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

        if ($ticket->user_id === $user?->id) {
            return true;
        }

        return $ticket->guest_email === $user?->email;
    }

    protected function canComment(): bool
    {
        $user = Auth::user();

        return $this->ticket->user_id === $user?->id
            || $this->ticket->guest_email === $user?->email;
    }

    #[Layout('layouts.portal')]
    public function render(): \Illuminate\View\View: View
    {
        return view('livewire.helpdesk.ticket-details');
    }
}
