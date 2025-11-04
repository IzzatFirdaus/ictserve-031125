<?php

declare(strict_types=1);

namespace App\Livewire\Helpdesk;

use App\Models\Asset;
use App\Models\Division;
use App\Models\TicketCategory;
use App\Services\HybridHelpdeskService;
use App\Traits\OptimizedLivewireComponent;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\WithFileUploads;

/**
 * Component name: Submit Helpdesk Ticket (Guest Form)
 * Description: WCAG 2.2 AA compliant multi-step wizard for guest helpdesk ticket submission
 *
 * @author Pasukan BPM MOTAC
 *
 * @trace D03-FR-001.1, D03-FR-011.1-11.7
 * @trace D04 §6.1 (Frontend Component Architecture)
 * @trace D10 §7 (Component Documentation)
 * @trace D12 §9 (WCAG 2.2 AA Compliance)
 *
 * @requirements 1.1, 1.2, 11.1-11.7, 21.5
 *
 * @wcag-level AA
 *
 * @version 1.0.0
 *
 * @created 2025-11-03
 */
class SubmitTicket extends Component
{
    use OptimizedLivewireComponent;
    use WithFileUploads;

    // Wizard state
    public int $currentStep = 1;

    public int $totalSteps = 4;

    // Step 1: Contact Information
    #[Validate('required|string|max:255')]
    public string $guest_name = '';

    #[Validate('required|email|max:255')]
    public string $guest_email = '';

    #[Validate('required|string|max:20')]
    public string $guest_phone = '';

    #[Validate('nullable|string|max:50')]
    public ?string $staff_id = null;

    #[Validate('required|exists:divisions,id')]
    public ?int $division_id = null;

    // Step 2: Issue Details
    #[Validate('required|exists:ticket_categories,id')]
    public ?int $category_id = null;

    #[Validate('required|in:low,normal,high,urgent')]
    public string $priority = 'normal';

    #[Validate('required|string|max:255')]
    public string $subject = '';

    #[Validate('required|string|min:10|max:5000')]
    public string $description = '';

    #[Validate('nullable|exists:assets,id')]
    public ?int $asset_id = null;

    // Step 3: Attachments
    #[Validate('nullable|array')]
    public array $attachments = [];

    // Submission state
    public bool $isSubmitting = false;

    public ?string $ticketNumber = null;

    /**
     * Get available ticket categories
     */
    #[Computed]
    public function categories()
    {
        $locale = app()->getLocale();
        $nameColumn = $locale === 'ms' ? 'name_ms' : 'name_en';
        $descriptionColumn = $locale === 'ms' ? 'description_ms' : 'description_en';

        return TicketCategory::query()
            ->where('is_active', true)
            ->orderBy($nameColumn)
            ->get([
                'id',
                'name_ms',
                'name_en',
                'description_ms',
                'description_en',
            ])
            ->map(function (TicketCategory $category) use ($nameColumn, $descriptionColumn) {
                $category->setAttribute('name', $category->getAttribute($nameColumn));
                $category->setAttribute('description', $category->getAttribute($descriptionColumn));

                return $category;
            });
    }

    /**
     * Get available divisions
     */
    #[Computed]
    public function divisions()
    {
        $nameColumn = app()->getLocale() === 'ms' ? 'name_ms' : 'name_en';

        return Division::query()
            ->where('is_active', true)
            ->orderBy($nameColumn)
            ->get(['id', 'name_ms', 'name_en']);
    }

    /**
     * Get available assets (optional)
     */
    #[Computed]
    public function assets()
    {
        return Asset::query()
            ->where('status', 'available')
            ->orderBy('name')
            ->get(['id', 'name', 'asset_tag']);
    }

    /**
     * Advance to next step
     */
    public function nextStep(): void
    {
        $this->validateCurrentStep();

        if ($this->currentStep < $this->totalSteps) {
            $this->currentStep++;
            $this->dispatch('step-changed', step: $this->currentStep);
        }
    }

    /**
     * Go back to previous step
     */
    public function previousStep(): void
    {
        if ($this->currentStep > 1) {
            $this->currentStep--;
            $this->dispatch('step-changed', step: $this->currentStep);
        }
    }

    /**
     * Go to specific step
     */
    public function goToStep(int $step): void
    {
        if ($step >= 1 && $step <= $this->currentStep && $step <= $this->totalSteps) {
            $this->currentStep = $step;
            $this->dispatch('step-changed', step: $this->currentStep);
        }
    }

    /**
     * Validate current step
     */
    protected function validateCurrentStep(): void
    {
        match ($this->currentStep) {
            1 => $this->validate([
                'guest_name' => 'required|string|max:255',
                'guest_email' => 'required|email|max:255',
                'guest_phone' => 'required|string|max:20',
                'division_id' => 'required|exists:divisions,id',
            ]),
            2 => $this->validate([
                'category_id' => 'required|exists:ticket_categories,id',
                'priority' => 'required|in:low,normal,high,urgent',
                'subject' => 'required|string|max:255',
                'description' => 'required|string|min:10|max:5000',
            ]),
            3 => $this->validate([
                'attachments' => 'nullable|array|max:5',
                'attachments.*' => 'nullable|file|max:10240|mimes:jpg,jpeg,png,pdf,doc,docx',
            ]),
            default => null,
        };
    }

    /**
     * Submit the ticket
     *
     * Implements conditional logic for guest vs authenticated submission
     *
     * @trace Requirements 1.1, 1.2, 1.3, 4.2
     */
    public function submit(): void
    {
        $this->isSubmitting = true;

        try {
            // Final validation
            $this->validate();

            DB::beginTransaction();

            $service = app(HybridHelpdeskService::class);

            // Conditional logic: Check if user is authenticated
            if (auth()->check()) {
                // Authenticated submission - use enhanced features
                $ticket = $service->createAuthenticatedTicket([
                    'category_id' => $this->category_id,
                    'priority' => $this->priority,
                    'title' => $this->subject,
                    'description' => $this->description,
                    'damage_type' => null, // Not applicable for standard helpdesk
                    'asset_id' => $this->asset_id,
                    'internal_notes' => null, // Can be added in future enhancement
                ], auth()->user());
            } else {
                // Guest submission - use guest fields
                $ticket = $service->createGuestTicket([
                    'guest_name' => $this->guest_name,
                    'guest_email' => $this->guest_email,
                    'guest_phone' => $this->guest_phone,
                    'guest_staff_id' => $this->staff_id,
                    'guest_grade' => null, // Can be enhanced later
                    'guest_division' => null, // Can be enhanced later
                    'category_id' => $this->category_id,
                    'priority' => $this->priority,
                    'title' => $this->subject,
                    'description' => $this->description,
                    'damage_type' => null, // Not applicable for standard helpdesk
                    'asset_id' => $this->asset_id,
                ]);
            }

            // Handle file attachments for both submission types
            if (! empty($this->attachments)) {
                foreach ($this->attachments as $attachment) {
                    $path = $attachment->store('helpdesk-attachments', 'private');
                    $ticket->attachments()->create([
                        'file_name' => $attachment->getClientOriginalName(),
                        'file_path' => $path,
                        'file_size' => $attachment->getSize(),
                        'mime_type' => $attachment->getMimeType(),
                    ]);
                }
            }

            DB::commit();

            $this->ticketNumber = $ticket->ticket_number;
            $this->currentStep = $this->totalSteps;

            // Dispatch appropriate event based on submission type
            $this->dispatch('ticket-submitted', [
                'ticketNumber' => $this->ticketNumber,
                'isAuthenticated' => auth()->check(),
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            $this->isSubmitting = false;

            // Proper error handling with validation feedback
            $errorMessage = __('helpdesk.submission_failed');
            if ($e instanceof \Illuminate\Validation\ValidationException) {
                $errorMessage = __('helpdesk.validation_failed');
            }

            $this->dispatch('submission-failed', message: $errorMessage);

            // Log error for debugging
            Log::error('Ticket submission failed', [
                'error' => $e->getMessage(),
                'user_id' => auth()->id(),
                'guest_email' => $this->guest_email ?? null,
            ]);

            throw $e;
        } finally {
            $this->isSubmitting = false;
        }
    }

    /**
     * Reset form
     */
    public function resetForm(): void
    {
        $this->reset();
        $this->currentStep = 1;
        $this->dispatch('form-reset');
    }

    /**
     * Custom validation messages
     */
    protected function messages(): array
    {
        return [
            'guest_name.required' => __('helpdesk.name_required'),
            'guest_email.required' => __('helpdesk.email_required'),
            'guest_email.email' => __('helpdesk.email_invalid'),
            'guest_phone.required' => __('helpdesk.phone_required'),
            'division_id.required' => __('Division is required'),
            'category_id.required' => __('helpdesk.category_required'),
            'subject.required' => __('helpdesk.subject_required'),
            'description.required' => __('helpdesk.description_required'),
            'description.min' => __('helpdesk.description_min'),
            'description.max' => __('helpdesk.description_max'),
            'attachments.*.max' => __('helpdesk.file_too_large'),
            'attachments.*.mimes' => __('helpdesk.invalid_file_type'),
        ];
    }

    /**
     * Render component
     */
    public function render()
    {
        $layout = (auth()->check() || request()->routeIs('helpdesk.authenticated.*'))
            ? 'layouts.portal'
            : 'layouts.front';

        return view('livewire.helpdesk.submit-ticket', [
            'divisions' => $this->divisions,
            'categories' => $this->categories,
            'assets' => $this->assets,
        ])->layout($layout);
    }
}
