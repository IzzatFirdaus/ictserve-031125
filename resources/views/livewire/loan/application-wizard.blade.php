{{--
/**
 * Component name: Loan Application Wizard (Multi-Step)
 * Description: Multi-step loan application wizard with True Hybrid Architecture support
 *
 * Steps:
 * 1. Applicant Information
 * 2. Responsible Officer
 * 3. Asset Selection
 * 4. Loan Dates
 * 5. Purpose & Location
 * 6. Acknowledgement & Declaration
 *
 * @author Pasukan BPM MOTAC
 * @trace D03-FR-042 (Asset Loan Application)
 * @trace D04 §5.2 (Loan Module Design)
 * @trace D12 §9 (WCAG 2.2 AA Compliance)
 * @trace D14 §8 (MOTAC Branding)
 * @trace Requirements 3.1, 3.2, 3.4, 24.2, 25.1, 25.2, 25.3, 25.6
 * @version 3.5.0
 * @created 2025-12-02
 */
--}}

<?php

use App\Models\AssetCategory;
use App\Models\Division;
use App\Models\User;
use App\Services\AssetAvailabilityService;
use App\Services\LoanApplicationService;
use App\Services\WorkingDayCalculator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Volt\Component;

new class extends Component {
    // Form reference code per PK.(S).MOTAC.07.(L3)
    public string $formReferenceCode = 'PK.(S).MOTAC.07.(L3)';

    // Wizard state
    public int $currentStep = 1;
    public int $totalSteps = 6;
    public bool $submitting = false;

    // Step 1: Applicant Information
    public string $applicantName = '';
    public string $applicantPosition = '';
    public string $applicantGrade = '';
    public string $applicantPhone = '';
    public string $applicantEmail = '';
    public ?int $divisionId = null;

    // Step 2: Responsible Officer
    public bool $isApplicantResponsible = true;
    public string $responsibleOfficerName = '';
    public string $responsibleOfficerPosition = '';
    public string $responsibleOfficerGrade = '';
    public string $responsibleOfficerPhone = '';
    public bool $responsibleOfficerAcknowledgement = false;

    // Step 3: Asset Selection
    public array $selectedAssets = [];
    public array $assetAvailability = [];

    // Step 4: Loan Dates
    public string $loanStartDate = '';
    public string $loanEndDate = '';
    public bool $emergencyRequest = false;
    public string $emergencyJustification = '';

    // Step 5: Purpose & Location
    public string $purpose = '';
    public string $location = '';
    public string $specialInstructions = '';

    // Step 6: Acknowledgement
    public bool $termsAcknowledged = false;
    public bool $pdpaAcknowledged = false;
    public string $applicantSignature = '';

    // Approver selection
    public ?int $approverId = null;
    public string $approverSearch = '';
    public array $approverResults = [];

    // Validation messages
    public string $minDateMessage = '';
    public string $nextAvailableDate = '';

    /**
     * Mount the component with initial data
     */
    public function mount(): void
    {
        // Pre-fill authenticated user data (True Hybrid Architecture)
        if (Auth::check()) {
            $user = Auth::user();
            $this->applicantName = $user->name ?? '';
            $this->applicantPhone = $user->phone ?? '';
            $this->applicantEmail = $user->email ?? '';
            $this->divisionId = $user->division_id;

            // Build position/grade from user's data
            if ($user->position) {
                $this->applicantPosition = app()->getLocale() === 'ms'
                    ? $user->position->name_ms
                    : $user->position->name_en;
            }
            if ($user->grade) {
                $this->applicantGrade = app()->getLocale() === 'ms'
                    ? $user->grade->name_ms
                    : $user->grade->name_en;
            }
        }

        // Initialize dates with 3-day lead time
        $calculator = app(WorkingDayCalculator::class);
        $nextDate = $calculator->getNextAvailableDate(now(), 3);
        $this->loanStartDate = $nextDate->format('Y-m-d');
        $this->loanEndDate = $nextDate->copy()->addDays(7)->format('Y-m-d');

        // Initialize with one empty asset row
        $this->selectedAssets = [
            ['category_id' => '', 'quantity' => 1, 'notes' => ''],
        ];
    }

    /**
     * Validation rules for each step
     */
    protected function getStepRules(int $step): array
    {
        return match ($step) {
            1 => $this->getStep1Rules(),
            2 => $this->getStep2Rules(),
            3 => [
                'selectedAssets' => 'required|array|min:1',
                'selectedAssets.*.category_id' => 'required|exists:asset_categories,id',
                'selectedAssets.*.quantity' => 'required|integer|min:1|max:10',
            ],
            4 => [
                'loanStartDate' => 'required|date|after:today',
                'loanEndDate' => 'required|date|after:loanStartDate',
                'emergencyJustification' => 'required_if:emergencyRequest,true|nullable|string|min:50|max:1000',
            ],
            5 => [
                'purpose' => 'required|string|min:10|max:500',
                'location' => 'required|string|max:255',
            ],
            6 => [
                'termsAcknowledged' => 'accepted',
                'pdpaAcknowledged' => 'accepted',
                'applicantSignature' => 'required|string|max:255',
                'approverId' => 'required|exists:users,id',
            ],
            default => [],
        };
    }

    /**
     * Step 1 validation rules (authentication-aware)
     */
    protected function getStep1Rules(): array
    {
        if (Auth::check()) {
            // Authenticated users have pre-filled data
            return [
                'divisionId' => 'required|exists:divisions,id',
            ];
        }

        // Guest users must fill all fields
        return [
            'applicantName' => 'required|string|max:255',
            'applicantPosition' => 'required|string|max:255',
            'applicantGrade' => 'required|string|max:100',
            'applicantPhone' => 'required|string|max:20',
            'applicantEmail' => 'required|email|max:255',
            'divisionId' => 'required|exists:divisions,id',
        ];
    }

    /**
     * Step 2 validation rules (Responsible Officer)
     */
    protected function getStep2Rules(): array
    {
        if ($this->isApplicantResponsible) {
            return ['isApplicantResponsible' => 'boolean'];
        }

        return [
            'isApplicantResponsible' => 'boolean',
            'responsibleOfficerName' => 'required|string|max:255',
            'responsibleOfficerPosition' => 'required|string|max:255',
            'responsibleOfficerGrade' => 'required|string|max:100',
            'responsibleOfficerPhone' => 'required|string|max:20',
            'responsibleOfficerAcknowledgement' => 'accepted',
        ];
    }

    /**
     * Navigate to next step
     */
    public function nextStep(): void
    {
        $this->validate($this->getStepRules($this->currentStep));

        // Additional validation for step 4 (3-day lead time)
        if ($this->currentStep === 4 && !$this->emergencyRequest) {
            $this->validateLeadTime();
        }

        // Check asset availability when moving from step 3
        if ($this->currentStep === 3) {
            $this->checkAllAssetAvailability();
        }

        if ($this->currentStep < $this->totalSteps) {
            $this->currentStep++;
        }
    }

    /**
     * Navigate to previous step
     */
    public function previousStep(): void
    {
        if ($this->currentStep > 1) {
            $this->currentStep--;
        }
    }

    /**
     * Go to specific step
     */
    public function goToStep(int $step): void
    {
        if ($step >= 1 && $step <= $this->totalSteps && $step <= $this->currentStep) {
            $this->currentStep = $step;
        }
    }

    /**
     * Validate 3-day lead time requirement
     */
    protected function validateLeadTime(): void
    {
        if (empty($this->loanStartDate)) {
            return;
        }

        $calculator = app(WorkingDayCalculator::class);

        if (!$calculator->validateLeadTime(now(), $this->loanStartDate, 3)) {
            $nextAvailable = $calculator->getNextAvailableDate(now(), 3);
            $this->nextAvailableDate = $nextAvailable->format('d/m/Y');
            $this->minDateMessage = __('loan.validation.min_lead_time', ['date' => $this->nextAvailableDate]);
            $this->addError('loanStartDate', $this->minDateMessage);
        }
    }

    /**
     * Handle isApplicantResponsible toggle change
     * Per Requirement 25.3: When checked (true), auto-populate from applicant
     * When unchecked (false), show separate fields for different responsible officer
     */
    public function updatedIsApplicantResponsible(bool $value): void
    {
        if ($value) {
            // Auto-populate from applicant data when checked (per Req 25.3)
            $this->responsibleOfficerName = $this->applicantName;
            $this->responsibleOfficerPosition = $this->applicantPosition;
            $this->responsibleOfficerGrade = $this->applicantGrade;
            $this->responsibleOfficerPhone = $this->applicantPhone;
        } else {
            // Clear fields when unchecked to allow separate responsible officer entry
            $this->responsibleOfficerName = '';
            $this->responsibleOfficerPosition = '';
            $this->responsibleOfficerGrade = '';
            $this->responsibleOfficerPhone = '';
            $this->responsibleOfficerAcknowledgement = false;
        }
    }

    /**
     * Add asset row
     */
    public function addAssetRow(): void
    {
        $this->selectedAssets[] = ['category_id' => '', 'quantity' => 1, 'notes' => ''];
    }

    /**
     * Remove asset row
     */
    public function removeAssetRow(int $index): void
    {
        if (count($this->selectedAssets) > 1) {
            unset($this->selectedAssets[$index]);
            $this->selectedAssets = array_values($this->selectedAssets);
            unset($this->assetAvailability[$index]);
        }
    }

    /**
     * Check asset availability when category or dates change
     */
    public function checkAssetAvailability(int $index): void
    {
        $item = $this->selectedAssets[$index] ?? null;

        if (!$item || empty($item['category_id'])) {
            unset($this->assetAvailability[$index]);
            return;
        }

        $availabilityService = app(AssetAvailabilityService::class);
        $this->assetAvailability[$index] = $availabilityService->checkCategoryAvailability(
            (int) $item['category_id'],
            $this->loanStartDate ?: date('Y-m-d', strtotime('+3 days')),
            $this->loanEndDate ?: date('Y-m-d', strtotime('+10 days')),
            (int) ($item['quantity'] ?? 1)
        );
    }

    /**
     * Check all asset availability
     */
    protected function checkAllAssetAvailability(): void
    {
        foreach ($this->selectedAssets as $index => $item) {
            if (!empty($item['category_id'])) {
                $this->checkAssetAvailability($index);
            }
        }
    }

    /**
     * Update availability when asset selection changes
     */
    public function updatedSelectedAssets(mixed $value, string $key): void
    {
        unset($value); // Unused but required by Livewire hook signature
        $parts = explode('.', $key);
        if (\count($parts) >= 1) {
            $index = (int) $parts[0];
            $this->checkAssetAvailability($index);
        }
    }

    /**
     * Update availability when dates change
     */
    public function updatedLoanStartDate(): void
    {
        $this->checkAllAssetAvailability();
        $this->resetErrorBag('loanStartDate');
        $this->minDateMessage = '';
    }

    public function updatedLoanEndDate(): void
    {
        $this->checkAllAssetAvailability();
    }

    /**
     * Search for approvers (Grade 41+)
     */
    public function searchApprovers(): void
    {
        if (strlen($this->approverSearch) < 2) {
            $this->approverResults = [];
            return;
        }

        $this->approverResults = User::query()
            ->where('is_active', true)
            ->whereHas('grade', fn($q) => $q->where('level', '>=', 41))
            ->where(function ($query) {
                $query->where('name', 'like', "%{$this->approverSearch}%")
                    ->orWhere('email', 'like', "%{$this->approverSearch}%")
                    ->orWhere('staff_id', 'like', "%{$this->approverSearch}%");
            })
            ->with(['division', 'grade'])
            ->limit(10)
            ->get()
            ->map(fn($user) => [
                'id' => $user->id,
                'name' => $user->name,
                'staff_id' => $user->staff_id,
                'grade' => $user->grade?->name_ms ?? 'N/A',
                'division' => $user->division?->name_ms ?? 'N/A',
            ])
            ->toArray();
    }

    /**
     * Select an approver
     */
    public function selectApprover(int $approverId): void
    {
        $this->approverId = $approverId;
        $this->approverSearch = '';
        $this->approverResults = [];
    }

    /**
     * Submit the loan application
     */
    public function submit(): void
    {
        // Validate all steps
        for ($step = 1; $step <= $this->totalSteps; $step++) {
            $this->validate($this->getStepRules($step));
        }

        $this->submitting = true;

        try {
            DB::beginTransaction();

            $applicationData = $this->prepareApplicationData();
            $loanService = app(LoanApplicationService::class);
            $application = $loanService->createHybridApplication($applicationData, Auth::user());

            DB::commit();

            session()->flash('success', __('loan.messages.application_submitted', [
                'application_number' => $application->application_number,
            ]));

            $this->redirect(route('loan.success', ['reference' => $application->application_number]), navigate: true);
        } catch (\Exception $e) {
            DB::rollBack();
            $this->addError('submit', __('loan.messages.submission_failed'));

            logger()->error('Loan application submission failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
        } finally {
            $this->submitting = false;
        }
    }

    /**
     * Prepare application data for submission
     */
    protected function prepareApplicationData(): array
    {
        $data = [
            'form_reference_code' => $this->formReferenceCode,
            'applicant_name' => $this->applicantName,
            'applicant_position' => $this->applicantPosition,
            'applicant_grade' => $this->applicantGrade,
            'applicant_email' => $this->applicantEmail ?: (Auth::user()?->email ?? ''),
            'applicant_phone' => $this->applicantPhone,
            'staff_id' => Auth::user()?->staff_id ?? 'GUEST',
            'grade' => $this->extractGrade($this->applicantGrade),
            'division_id' => $this->divisionId,
            'purpose' => $this->purpose,
            'location' => $this->location,
            'loan_start_date' => $this->loanStartDate,
            'expected_return_date' => $this->loanEndDate,
            'loan_end_date' => $this->loanEndDate,
            'items' => $this->selectedAssets,
            'applicant_digital_signature' => $this->applicantSignature,
            'terms_acknowledged' => $this->termsAcknowledged,
            'is_applicant_responsible' => $this->isApplicantResponsible,
            'approver_id' => $this->approverId,
            'priority' => $this->emergencyRequest ? 'urgent' : 'normal',
            'special_instructions' => $this->emergencyRequest ? $this->emergencyJustification : ($this->specialInstructions ?: null),
        ];

        // Add responsible officer data if different from applicant
        if (!$this->isApplicantResponsible) {
            $data['responsible_officer_name'] = $this->responsibleOfficerName;
            $data['responsible_officer_position'] = $this->responsibleOfficerPosition;
            $data['responsible_officer_grade'] = $this->responsibleOfficerGrade;
            $data['responsible_officer_phone'] = $this->responsibleOfficerPhone;
        }

        return $data;
    }

    /**
     * Extract grade number from position string
     */
    protected function extractGrade(string $position): string
    {
        preg_match('/\d+/', $position, $matches);
        return $matches[0] ?? '41';
    }

    /**
     * Get divisions for dropdown
     */
    public function getDivisionsProperty()
    {
        $locale = app()->getLocale();
        $orderColumn = $locale === 'ms' ? 'name_ms' : 'name_en';

        return Division::query()
            ->where('is_active', true)
            ->orderBy($orderColumn)
            ->get(['id', 'code', 'name_ms', 'name_en']);
    }

    /**
     * Get asset categories for dropdown
     */
    public function getAssetCategoriesProperty()
    {
        return AssetCategory::where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();
    }

    /**
     * Get selected approver details
     */
    public function getSelectedApproverProperty()
    {
        if (!$this->approverId) {
            return null;
        }

        return User::with(['division', 'grade'])->find($this->approverId);
    }

    /**
     * Render the component
     */
    public function render(): mixed
    {
        $layout = Auth::check() ? 'layouts.portal' : 'layouts.front';

        return view('livewire.loan.application-wizard-view', [
            'layout' => $layout,
        ]);
    }
};
?>
