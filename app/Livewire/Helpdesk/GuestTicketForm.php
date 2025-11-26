<?php

declare(strict_types=1);

namespace App\Livewire\Helpdesk;

use App\Models\Division;
use App\Models\HelpdeskTicket;
use App\Models\TicketCategory;
use Illuminate\Support\Facades\Mail;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\WithFileUploads;

/**
 * Guest Helpdesk Ticket Form - Multi-step Wizard
 * 
 * ISO Compliance: PK.(S).MOTAC.07.(L1)
 * 
 * Features:
 * - Multi-step wizard with progress indicators
 * - Real-time validation (300ms debounce)
 * - File upload with drag-and-drop (max 5 files)
 * - Searchable division select
 * - Mandatory declaration with exact legacy text
 * - Email confirmation within 60 seconds
 * - Rate limiting and CSRF protection
 * 
 * @trace D03-FR-011, D12-§9, D14-§2.2
 * @requirements 1.1, 1.2, 9.1, 12.3, 13.6
 */
class GuestTicketForm extends Component
{
    use WithFileUploads;

    // Form Data
    #[Validate('required|string|max:255')]
    public string $guest_name = '';

    #[Validate('required|email|max:255')]
    public string $guest_email = '';

    #[Validate('required|string|max:20')]
    public string $guest_phone = '';

    #[Validate('nullable|string|max:50')]
    public string $guest_staff_id = '';

    #[Validate('required')]
    public ?int $division_id = null;

    #[Validate('required|string|max:50')]
    public string $job_grade = '';

    #[Validate('required')]
    public ?int $category_id = null;

    #[Validate('required|in:low,normal,high,urgent')]
    public string $priority = 'normal';

    #[Validate('required|string|max:255')]
    public string $subject = '';

    #[Validate('required|string|min:10|max:5000')]
    public string $description = '';

    #[Validate('nullable|array|max:5')]
    public array $attachments = [];

    #[Validate('accepted')]
    public bool $declaration_accepted = false;

    // Wizard State
    public int $currentStep = 1;
    public int $totalSteps = 3;

    // UI State
    public bool $isSubmitting = false;
    public bool $submitted = false;
    public ?string $ticketNumber = null;
    public string $divisionSearch = '';

    /**
     * Get all divisions for the select dropdown
     */
    #[Computed]
    public function divisions()
    {
        return Division::query()
            ->when($this->divisionSearch, function ($query) {
                $query->where('name', 'like', '%' . $this->divisionSearch . '%');
            })
            ->orderBy('name')
            ->get();
    }

    /**
     * Get all ticket categories
     */
    #[Computed]
    public function categories()
    {
        return TicketCategory::query()
            ->whereNull('parent_id')
            ->orderBy('name')
            ->get();
    }

    /**
     * Move to next step with validation
     */
    public function nextStep(): void
    {
        $this->validateCurrentStep();
        
        if ($this->currentStep < $this->totalSteps) {
            $this->currentStep++;
        }
    }

    /**
     * Move to previous step
     */
    public function previousStep(): void
    {
        if ($this->currentStep > 1) {
            $this->currentStep--;
        }
    }

    /**
     * Validate only the current step
     */
    protected function validateCurrentStep(): void
    {
        match ($this->currentStep) {
            1 => $this->validate([
                'guest_name' => 'required|string|max:255',
                'guest_email' => 'required|email|max:255',
                'guest_phone' => 'required|string|max:20',
                'guest_staff_id' => 'nullable|string|max:50',
                'division_id' => 'required',
                'job_grade' => 'required|string|max:50',
            ]),
            2 => $this->validate([
                'category_id' => 'required',
                'subject' => 'required|string|max:255',
                'description' => 'required|string|min:10|max:5000',
                'priority' => 'required|in:low,normal,high,urgent',
            ]),
            3 => $this->validate([
                'declaration_accepted' => 'accepted',
            ]),
            default => null,
        };
    }

    /**
     * Submit the helpdesk ticket
     */
    public function submit(): void
    {
        $this->isSubmitting = true;

        try {
            // Final validation
            $this->validate();

            // Create ticket
            $ticket = HelpdeskTicket::create([
                'ticket_number' => HelpdeskTicket::generateTicketNumber(),
                'guest_name' => $this->guest_name,
                'guest_email' => $this->guest_email,
                'guest_phone' => $this->guest_phone,
                'guest_staff_id' => $this->guest_staff_id ?: null,
                'division_id' => $this->division_id,
                'job_grade' => $this->job_grade,
                'category_id' => $this->category_id,
                'priority' => $this->priority,
                'subject' => $this->subject,
                'description' => $this->description,
                'status' => 'open',
                'declaration_accepted' => $this->declaration_accepted,
            ]);

            // Handle file uploads if any
            if (!empty($this->attachments)) {
                foreach ($this->attachments as $attachment) {
                    $path = $attachment->store('helpdesk-attachments/' . $ticket->ticket_number, 'public');
                    
                    $ticket->attachments()->create([
                        'filename' => $attachment->getClientOriginalName(),
                        'path' => $path,
                        'size' => $attachment->getSize(),
                        'mime_type' => $attachment->getMimeType(),
                    ]);
                }
            }

            // Calculate SLA due dates
            $ticket->calculateSLADueDates();

            // Send email confirmation (async queue)
            Mail::to($this->guest_email)->queue(
                new \App\Mail\HelpdeskTicketCreated($ticket)
            );

            // Success state
            $this->submitted = true;
            $this->ticketNumber = $ticket->ticket_number;

        } catch (\Exception $e) {
            $this->addError('submission', __('An error occurred. Please try again.'));
            logger()->error('Guest ticket submission failed', [
                'error' => $e->getMessage(),
                'email' => $this->guest_email,
            ]);
        } finally {
            $this->isSubmitting = false;
        }
    }

    /**
     * Reset and start new submission
     */
    public function resetForm(): void
    {
        $this->reset();
        $this->currentStep = 1;
    }

    public function render()
    {
        return view('livewire.helpdesk.guest-ticket-form');
    }
}
