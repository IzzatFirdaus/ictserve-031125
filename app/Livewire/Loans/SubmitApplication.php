<?php

declare(strict_types=1);

namespace App\Livewire\Loans;

use App\Models\Asset;
use App\Models\Division;
use App\Services\AssetAvailabilityService;
use App\Services\LoanApplicationService;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Validate;
use Livewire\Component;

/**
 * Component name: Submit Loan Application (Guest Form)
 * Description: WCAG 2.2 AA compliant multi-step wizard for guest asset loan application
 *
 * @author Pasukan BPM MOTAC
 *
 * @trace D03-FR-001.2, D03-FR-012.1-12.5
 * @trace D04 §6.1 (Frontend Component Architecture)
 * @trace D10 §7 (Component Documentation)
 * @trace D12 §9 (WCAG 2.2 AA Compliance)
 *
 * @requirements 1.2, 1.4, 11.1-11.7, 21.5
 *
 * @wcag-level AA
 *
 * @version 1.0.0
 *
 * @created 2025-11-03
 */
class SubmitApplication extends Component
{
    // Wizard state
    public int $currentStep = 1;

    public int $totalSteps = 4;

    // Step 1: Applicant Information
    #[Validate('required|string|max:255')]
    public string $applicant_name = '';

    #[Validate('required|email|max:255')]
    public string $applicant_email = '';

    #[Validate('required|string|max:20')]
    public string $applicant_phone = '';

    #[Validate('nullable|string|max:50')]
    public ?string $staff_id = null;

    #[Validate('required|in:41,44,48,52,54')]
    public string $grade = '41';

    #[Validate('required|exists:divisions,id')]
    public ?int $division_id = null;

    // Step 2: Asset Selection
    #[Validate('required|array|min:1')]
    public array $selected_assets = [];

    public string $search_query = '';

    public array $availability_status = [];

    // Step 3: Loan Period
    #[Validate('required|date|after:today')]
    public ?string $start_date = null;

    #[Validate('required|date|after:start_date')]
    public ?string $end_date = null;

    #[Validate('required|string|min:10|max:1000')]
    public string $purpose = '';

    #[Validate('required|string|max:255')]
    public string $location = '';

    // Submission state
    public bool $isSubmitting = false;

    public ?string $applicationNumber = null;

    /**
     * Get available divisions
     */
    #[Computed]
    public function divisions()
    {
        $locale = app()->getLocale();
        $orderColumn = $locale === 'ms' ? 'name_ms' : 'name_en';

        return Division::query()
            ->where('is_active', true)
            ->orderBy($orderColumn)
            ->get([
                'id',
                'name_en',
                'name_ms',
            ])
            ->map(function (Division $division) use ($locale) {
                $division->setAttribute('localized_name', $locale === 'ms' ? $division->name_ms : $division->name_en);

                return $division;
            });
    }

    /**
     * Get available assets
     */
    #[Computed]
    public function availableAssets()
    {
        return Asset::query()
            ->where('status', 'available')
            ->when($this->search_query, function ($query) {
                $query->where(function ($q) {
                    $q->where('name', 'like', '%'.$this->search_query.'%')
                        ->orWhere('asset_tag', 'like', '%'.$this->search_query.'%')
                        ->orWhere('description', 'like', '%'.$this->search_query.'%');
                });
            })
            ->with('category')
            ->orderBy('name')
            ->get();
    }

    /**
     * Toggle asset selection
     */
    public function toggleAsset(int $assetId): void
    {
        if (in_array($assetId, $this->selected_assets)) {
            $this->selected_assets = array_values(array_diff($this->selected_assets, [$assetId]));
        } else {
            $this->selected_assets[] = $assetId;
        }

        $this->checkAvailability();
    }

    /**
     * Check asset availability for selected dates
     */
    public function checkAvailability(): void
    {
        if (empty($this->selected_assets) || ! $this->start_date || ! $this->end_date) {
            return;
        }

        $service = app(AssetAvailabilityService::class);
        $this->availability_status = $service->checkAvailability(
            $this->selected_assets,
            $this->start_date,
            $this->end_date
        );
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
                'applicant_name' => 'required|string|max:255',
                'applicant_email' => 'required|email|max:255',
                'applicant_phone' => 'required|string|max:20',
                'grade' => 'required|in:41,44,48,52,54',
                'division_id' => 'required|exists:divisions,id',
            ]),
            2 => $this->validate([
                'selected_assets' => 'required|array|min:1',
            ]),
            3 => $this->validate([
                'start_date' => 'required|date|after:today',
                'end_date' => 'required|date|after:start_date',
                'purpose' => 'required|string|min:10|max:1000',
                'location' => 'required|string|max:255',
            ]),
            default => null,
        };
    }

    /**
     * Submit the application
     */
    public function submit(): void
    {
        $this->isSubmitting = true;

        try {
            // Final validation
            $this->validate();

            DB::beginTransaction();

            // Create application using service
            $service = app(LoanApplicationService::class);
            $application = $service->createHybridApplication([
                'applicant_name' => $this->applicant_name,
                'applicant_email' => $this->applicant_email,
                'applicant_phone' => $this->applicant_phone,
                'staff_id' => $this->staff_id,
                'grade' => $this->grade,
                'division_id' => $this->division_id,
                'items' => $this->selected_assets,
                'loan_start_date' => $this->start_date,
                'loan_end_date' => $this->end_date,
                'purpose' => $this->purpose,
                'location' => $this->location,
            ], null); // Guest submission

            DB::commit();

            $this->applicationNumber = $application->application_number;
            $this->currentStep = $this->totalSteps;

            $this->dispatch('application-submitted', applicationNumber: $this->applicationNumber);
        } catch (\Exception $e) {
            DB::rollBack();
            $this->isSubmitting = false;

            $this->dispatch('submission-failed', message: __('loans.submission_failed'));

            throw $e;
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
     * Render component
     */
    public function render()
    {
        return view('livewire.loans.submit-application')
            ->layout('components.layout.guest');
    }
}
