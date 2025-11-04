<?php

declare(strict_types=1);

namespace App\Livewire\Helpdesk;

use App\Models\HelpdeskTicket;
use Illuminate\Support\Str;
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
        return HelpdeskTicket::query()
            ->with(['category', 'division', 'assignedUser'])
            ->where('ticket_number', strtoupper(Str::of($ticketNumber)->trim()->toString()))
            ->first();
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
                'label' => __('Tiket Dihantar / Ticket Submitted'),
                'timestamp' => $ticket->created_at,
                'description' => __('Permohonan berjaya diterima oleh pasukan ICTServe.'),
            ],
            [
                'key' => 'assigned',
                'label' => __('Tiket Ditugaskan / Assigned'),
                'timestamp' => $ticket->assigned_at,
                'description' => $ticket->assignedUser
                    ? __('Ditugaskan kepada :name', ['name' => $ticket->assignedUser->name])
                    : __('Sedang ditugaskan kepada pegawai bertugas.'),
            ],
            [
                'key' => 'responded',
                'label' => __('Respon Pertama / First Response'),
                'timestamp' => $ticket->responded_at,
                'description' => __('Pegawai ICT telah memberikan maklum balas awal.'),
            ],
            [
                'key' => 'resolved',
                'label' => __('Selesai / Resolved'),
                'timestamp' => $ticket->resolved_at,
                'description' => $ticket->resolution_notes
                    ? $ticket->resolution_notes
                    : __('Isu telah diselesaikan dan menunggu pengesahan.'),
            ],
            [
                'key' => 'closed',
                'label' => __('Ditutup / Closed'),
                'timestamp' => $ticket->closed_at,
                'description' => __('Tiket ditutup selepas pengesahan pemohon.'),
            ],
        ])->filter(fn (array $event) => $event['timestamp'] !== null);

        return $events->map(function (array $event) use ($ticket) {
            $timestamp = $event['timestamp'];

            return [
                'label' => $event['label'],
                'description' => $event['description'],
                'completed' => $timestamp !== null,
                'time' => $timestamp?->translatedFormat('d M Y, h:i A'),
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

    public function render()
    {
        return view('livewire.helpdesk.track-ticket');
    }
}
