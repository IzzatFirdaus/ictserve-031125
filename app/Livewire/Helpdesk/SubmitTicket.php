<?php

declare(strict_types=1);

namespace App\Livewire\Helpdesk;

use App\Livewire\Forms\HelpdeskTicketForm;
use App\Models\Category;
use App\Models\Division;
use App\Models\HelpdeskTicket;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Mail;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;

/**
 * Submit Helpdesk Ticket Component
 *
 * Optimized Livewire component for guest helpdesk ticket submission
 * with real-time validation, bilingual support, and WCAG 2.2 AA compliance
 *
 * @requirements 1.1, 1.2, 11.1-11.7, 15.1-15.4, 21.4
 * @wcag-level AA
 * @version 1.0.0
 */
#[Layout('layouts.guest')]
#[Title('Submit Helpdesk Ticket')]
class SubmitTicket extends Component
{
    use WithFileUploads;

    public HelpdeskTicketForm $form;

    public array $attachments = [];

    public bool $submitted = false;

    public ?string $ticketNumber = null;

    /**
     * Get divisions with caching and eager loading
     *
     * Uses #[Computed] for performance optimization
     */
    #[Computed]
    public function divisions(): Collection
    {
        return Division::query()
            ->select('id', 'name')
            ->orderBy('name')
            ->get();
    }

    /**
     * Get categories with caching and eager loading
     *
     * Uses #[Computed] for performance optimization
     */
    #[Computed]
    public function categories(): Collection
    {
        return Category::query()
            ->where('type', 'helpdesk')
            ->select('id', 'name', 'description')
            ->orderBy('name')
            ->get();
    }

    /**
     * Submit the helpdesk ticket
     *
     * Validates form, creates ticket, sends confirmation email
     */
    public function submitTicket(): void
    {
        $this->form->validate();

        // Create ticket with guest fields
        $ticket = HelpdeskTicket::create([
            'ticket_number' => $this->generateTicketNumber(),
            'user_id' => auth()->id(), // NULL for guest, user ID for authenticated
            'guest_name' => $this->form->name,
            'guest_email' => $this->form->email,
            'guest_phone' => $this->form->phone,
            'staff_id' => $this->form->staff_id,
            'division_id' => $this->form->division_id,
            'category_id' => $this->form->category_id,
            'subject' => $this->form->subject,
            'description' => $this->form->description,
            'priority' => $this->form->priority,
            'status' => 'open',
        ]);

        // Handle file attachments
        if (!empty($this->attachments)) {
            foreach ($this->attachments as $file) {
                $ticket->attachments()->create([
                    'filename' => $file->getClientOriginalName(),
                    'path' => $file->store('helpdesk-attachments', 'private'),
                    'size' => $file->getSize(),
                    'mime_type' => $file->getMimeType(),
                ]);
            }
        }

        // Send confirmation email (queued for performance)
        // Mail::to($ticket->guest_email)->queue(new TicketCreatedConfirmation($ticket));

        // Set success state
        $this->submitted = true;
        $this->ticketNumber = $ticket->ticket_number;

        // Announce to screen readers
        $this->dispatch('ticket-submitted', ticketNumber: $this->ticketNumber);
    }

    /**
     * Clear the form
     */
    public function clearForm(): void
    {
        $this->form->reset();
        $this->attachments = [];
        $this->submitted = false;
        $this->ticketNumber = null;
    }

    /**
     * Generate unique ticket number
     *
     * Format: HD[YYYY][000001-999999]
     */
    private function generateTicketNumber(): string
    {
        $year = date('Y');
        $lastTicket = HelpdeskTicket::whereYear('created_at', $year)
            ->orderBy('id', 'desc')
            ->first();

        $sequence = $lastTicket ? ((int) substr($lastTicket->ticket_number, -6)) + 1 : 1;

        return 'HD' . $year . str_pad((string) $sequence, 6, '0', STR_PAD_LEFT);
    }

    /**
     * Render the component
     */
    public function render()
    {
        return view('livewire.helpdesk.submit-ticket');
    }
}
