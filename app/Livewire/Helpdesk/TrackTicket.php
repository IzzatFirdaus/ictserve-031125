<?php

declare(strict_types=1);

namespace App\Livewire\Helpdesk;

use App\Models\HelpdeskTicket;
use App\Traits\OptimizedLivewireComponent;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Livewire\Attributes\On;
use Livewire\Attributes\Validate;
use Livewire\Component;

/**
 * TrackTicket Component
 *
 * Provides guest-accessible tracking for helpdesk tickets with secure verification
 * through ticket number + email combination and WCAG compliant timeline output.
 *
 * @requirements 1.2, 1.4, 11.6, 21.5
 */
class TrackTicket extends Component
{
    use OptimizedLivewireComponent;

    /**
     * Define relationships to eager load for N+1 prevention
     *
     * @return array<int, string>
     */
    protected function getEagerLoadRelationships(): array
    {
        return ['category', 'division', 'assignedUser'];
    }

    #[Validate('required|string|max:50')]
    public string $ticketNumber = '';

    #[Validate('required|email|max:255')]
    public string $email = '';

    public ?HelpdeskTicket $ticket = null;

    /**
     * @var array<int, array<string, mixed>>
     */
    public array $timeline = [];

    public bool $notFound = false;

    public bool $showResults = false;

    public function mount(?string $ticketNumber = null, ?string $email = null): void
    {
        if ($ticketNumber) {
            $this->ticketNumber = $ticketNumber;
        }

        if ($email) {
            $this->email = $email;
        }

        if ($ticketNumber && $email) {
            $this->track();
        }
    }

    #[On('refresh-ticket-tracking')]
    public function refreshTimeline(): void
    {
        if ($this->showResults) {
            $this->track();
        }
    }

    public function track(): void
    {
        $this->validate();
        $this->resetErrorBag();

        $ticket = $this->queryTicket($this->ticketNumber);

        if (! $ticket || ! $this->canViewTicket($ticket)) {
            $this->ticket = null;
            $this->timeline = [];
            $this->notFound = true;
            $this->showResults = false;

            return;
        }

        $this->ticket = $ticket;
        $this->timeline = $this->buildTimeline($ticket);
        $this->notFound = false;
        $this->showResults = true;
    }

    protected function queryTicket(string $ticketNumber): ?HelpdeskTicket
    {
        /** @var Builder<HelpdeskTicket> $query */
        $query = HelpdeskTicket::query()
            ->with(['category', 'division', 'assignedUser'])
            ->where('ticket_number', strtoupper(Str::of($ticketNumber)->trim()->toString()));

        return $query->first();
    }

    protected function canViewTicket(HelpdeskTicket $ticket): bool
    {
        $email = strtolower(trim($this->email));

        if ($ticket->isGuestSubmission()) {
            return strtolower((string) $ticket->guest_email) === $email;
        }

        return strtolower((string) $ticket->user?->email) === $email
            || strtolower((string) $ticket->guest_email) === $email;
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    protected function buildTimeline(HelpdeskTicket $ticket): array
    {
        $events = collect([
            [
                'key' => 'submitted',
                'label' => __('helpdesk.track.submitted'),
                'timestamp' => $ticket->created_at,
                'description' => __('helpdesk.track.submitted_desc'),
            ],
            [
                'key' => 'assigned',
                'label' => __('helpdesk.track.assigned'),
                'timestamp' => $ticket->assigned_at,
                'description' => $ticket->assignedUser
                    ? __('helpdesk.track.assigned_to', ['name' => $ticket->assignedUser->name])
                    : __('helpdesk.track.assigning'),
            ],
            [
                'key' => 'responded',
                'label' => __('helpdesk.track.responded'),
                'timestamp' => $ticket->responded_at,
                'description' => __('helpdesk.track.responded_desc'),
            ],
            [
                'key' => 'resolved',
                'label' => __('helpdesk.track.resolved'),
                'timestamp' => $ticket->resolved_at,
                'description' => $ticket->resolution_notes
                    ? $ticket->resolution_notes
                    : __('helpdesk.track.resolved_desc'),
            ],
            [
                'key' => 'closed',
                'label' => __('helpdesk.track.closed'),
                'timestamp' => $ticket->closed_at,
                'description' => __('helpdesk.track.closed_desc'),
            ],
        ])->filter(fn (array $event) => $event['timestamp'] !== null);

        return $events->map(function (array $event) use ($ticket) {
            $timestamp = $event['timestamp'];
            $time = $timestamp instanceof Carbon ? $timestamp->translatedFormat('d M Y, h:i A') : null;

            return [
                'label' => $event['label'],
                'description' => $event['description'],
                'completed' => $timestamp instanceof Carbon,
                'time' => $time,
                'current' => $this->isCurrentStage($ticket, $event['key']),
            ];
        })->values()->all();
    }

    protected function isCurrentStage(HelpdeskTicket $ticket, string $key): bool
    {
        return match ($key) {
            'submitted' => $ticket->status === 'open',
            'assigned' => $ticket->status === 'assigned',
            'responded' => $ticket->status === 'in_progress',
            'resolved' => $ticket->status === 'resolved',
            'closed' => $ticket->status === 'closed',
            default => false,
        };
    }

    public function render(): \Illuminate\View\View
    {
        return view('livewire.helpdesk.track-ticket');
    }
}
