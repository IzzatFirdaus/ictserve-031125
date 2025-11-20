<?php

declare(strict_types=1);

/**
 * Component name: Guest Loan Application
 * Description: Multi-step loan application form handler with validation and submission
 *
 * @author Pasukan BPM MOTAC
 *
 * @trace D03-FR-042 (Asset Loan Application)
 * @trace D04 §5.2 (Loan Module Design)
 *
 * @version 2.0.0
 *
 * @created 2025-11-04
 */

namespace App\Livewire;

use App\Models\AssetCategory;
use App\Models\Division;
use App\Services\LoanApplicationService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Livewire\Attributes\Validate;
use Livewire\Component;

class GuestLoanApplication extends Component
{
    public int $currentStep = 1;

    public int $totalSteps = 7;

    public bool $submitting = false;

    public string $approverSearch = '';

    /** @var array<string, mixed> */
    public array $approverResults = [];

    // Form data array
    /** @var array<string, mixed> */
    public array $form = [
        // BAHAGIAN 1: Maklumat Pemohon
        'applicant_name' => '',
        'applicant_position' => '',
        'applicant_grade' => '',
        'phone' => '',
        'division_id' => null,
        'purpose' => '',
        'location' => '',
        'loan_start_date' => '',
        'expected_return_date' => '',

        // BAHAGIAN 2: Pegawai Bertanggungjawab (conditional)
        'is_responsible_officer' => false,
        'responsible_officer_name' => '',
        'responsible_officer_position' => '',
        'responsible_officer_grade' => '',
        'responsible_officer_phone' => '',

        // BAHAGIAN 3: Butiran Peralatan
        'equipment_items' => [
            ['equipment_type' => '', 'quantity' => 1, 'notes' => ''],
        ],

        // BAHAGIAN 4: Syarat-Syarat (Terms)
        'terms_acknowledged' => false,

        // BAHAGIAN 5: Pengesahan Pemohon (Declaration)
        'applicant_digital_signature' => '',

        // BAHAGIAN 6: Approver Selection (Grade 41+)
        'approver_id' => null,
    ];

    /** @var array<int, array<string, string>> */
    protected array $stepValidationRules = [
        1 => [
            'form.applicant_name' => 'required|string|max:255',
            'form.applicant_position' => 'required|string|max:255',
            'form.applicant_grade' => 'required|string|max:100',
            'form.phone' => 'required|string|max:20',
            'form.division_id' => 'required|exists:divisions,id',
            'form.purpose' => 'required|string|max:500',
            'form.location' => 'required|string|max:255',
            'form.loan_start_date' => 'required|date|after:today',
            'form.expected_return_date' => 'required|date|after:form.loan_start_date',
        ],
        2 => [
            'form.is_responsible_officer' => 'boolean',
            'form.responsible_officer_name' => 'required_if:form.is_responsible_officer,true|nullable|string|max:255',
            'form.responsible_officer_position' => 'required_if:form.is_responsible_officer,true|nullable|string|max:255',
            'form.responsible_officer_grade' => 'required_if:form.is_responsible_officer,true|nullable|string|max:100',
            'form.responsible_officer_phone' => 'required_if:form.is_responsible_officer,true|nullable|string|max:20',
        ],
        3 => [
            'form.equipment_items' => 'required|array|min:1',
            'form.equipment_items.*.equipment_type' => 'required|exists:asset_categories,id',
            'form.equipment_items.*.quantity' => 'required|integer|min:1',
            'form.equipment_items.*.notes' => 'nullable|string|max:255',
        ],
        4 => [
            'form.terms_acknowledged' => 'accepted',
        ],
        5 => [
            'form.applicant_digital_signature' => 'required|string|max:255',
        ],
        6 => [
            'form.approver_id' => 'required|exists:users,id',
        ],
        7 => [
            // Final review - no validation needed (just confirmation page)
        ],
    ];

    /**
     * @return array<string, string>
     */
    protected function messages(): array
    {
        return [
            'form.equipment_items.*.equipment_type.required' => __('loan.validation.equipment_type_required'),
            'form.equipment_items.*.equipment_type.exists' => __('loan.validation.equipment_type_exists'),
            'form.equipment_items.*.quantity.required' => __('loan.validation.quantity_required'),
            'form.equipment_items.*.quantity.integer' => __('loan.validation.quantity_integer'),
            'form.equipment_items.*.quantity.min' => __('loan.validation.quantity_min'),
            'form.responsible_officer_name.required_if' => __('loan.validation.responsible_officer_name_required'),
            'form.responsible_officer_position.required_if' => __('loan.validation.responsible_officer_position_required'),
            'form.responsible_officer_phone.required_if' => __('loan.validation.responsible_officer_phone_required'),
        ];
    }

    /**
     * @return array<string, string>
     */
    protected function validationAttributes(): array
    {
        return [
            'form.equipment_items.*.equipment_type' => __('loan.table.equipment_type'),
            'form.equipment_items.*.quantity' => __('loan.table.quantity'),
            'form.equipment_items.*.notes' => __('loan.table.notes'),
        ];
    }

    public function mount(): void
    {
        // Pre-fill authenticated user data
        if (Auth::check()) {
            $user = Auth::user();
            $this->form['applicant_name'] = $user->name ?? '';
            $this->form['phone'] = $user->phone ?? '';
            $this->form['division_id'] = $user->division_id;

            // Build position/grade from user's data
            if ($user->position) {
                $positionName = app()->getLocale() === 'ms'
                    ? $user->position->name_ms
                    : $user->position->name_en;
                $this->form['applicant_position'] = $positionName;
            }
            if ($user->grade) {
                $gradeName = app()->getLocale() === 'ms'
                    ? $user->grade->name_ms
                    : $user->grade->name_en;
                $this->form['applicant_grade'] = $gradeName;
            }
        }

        // Initialize form with default values
        $this->form['loan_start_date'] = date('Y-m-d', strtotime('+1 day'));
        $this->form['expected_return_date'] = date('Y-m-d', strtotime('+7 days'));
    }

    public function nextStep(): void
    {
        // Validate current step with authentication-aware logic
        $this->validateCurrentStep();

        // Move to next step
        if ($this->currentStep < $this->totalSteps) {
            $this->currentStep++;
        }
    }

    protected function validateCurrentStep(): void
    {
        if ($this->currentStep === 1) {
            $this->validateStep1();
        } else {
            $this->validate($this->stepValidationRules[$this->currentStep]);
        }
    }

    protected function validateStep1(): void
    {
        // Authenticated users don't need to fill contact fields
        if (Auth::check()) {
            // Only validate loan-specific fields for authenticated users
            $this->validate([
                'form.purpose' => 'required|string|max:500',
                'form.location' => 'required|string|max:255',
                'form.loan_start_date' => 'required|date|after:today',
                'form.expected_return_date' => 'required|date|after:form.loan_start_date',
            ]);

            return;
        }

        // Guest users must fill all fields
        $this->validate($this->stepValidationRules[1]);
    }

    public function previousStep(): void
    {
        if ($this->currentStep > 1) {
            $this->currentStep--;
        }
    }

    public function addEquipmentRow(): void
    {
        $this->form['equipment_items'][] = [
            'equipment_type' => '',
            'quantity' => 1,
            'notes' => '',
        ];
    }

    public function removeEquipmentRow(): void
    {
        if (count($this->form['equipment_items']) > 1) {
            array_pop($this->form['equipment_items']);
        }
    }

    public function searchApprovers(): void
    {
        if (strlen($this->approverSearch) < 2) {
            $this->approverResults = [];

            return;
        }

        // Search for Grade 41+ officers
        $this->approverResults = \App\Models\User::query()
            ->where('is_active', true)
            ->whereHas('grade', function ($query) {
                $query->where('grade_number', '>=', 41);
            })
            ->where(function ($query) {
                $query->where('name', 'like', '%'.$this->approverSearch.'%')
                    ->orWhere('email', 'like', '%'.$this->approverSearch.'%')
                    ->orWhere('staff_id', 'like', '%'.$this->approverSearch.'%');
            })
            ->with(['division', 'grade'])
            ->limit(10)
            ->get()
            ->map(function ($user) {
                return [
                    'id' => $user->id,
                    'name' => $user->name,
                    'staff_id' => $user->staff_id,
                    'grade' => $user->grade?->name_ms ?? 'N/A',
                    'division' => $user->division?->name_ms ?? 'N/A',
                ];
            })
            ->toArray();
    }

    public function selectApprover(int $approverId): void
    {
        $this->form['approver_id'] = $approverId;
        $this->approverSearch = '';
        $this->approverResults = [];
    }

    /**
     * Alias for submitForm() - for testing compatibility
     */
    public function submit(): void
    {
        $this->submitForm();
    }

    public function submitForm(): void
    {
        // Validate all steps
        foreach ($this->stepValidationRules as $rules) {
            $this->validate($rules);
        }

        $this->submitting = true;

        try {
            DB::beginTransaction();

            // Prepare application data
            $applicationData = [
                'applicant_name' => $this->form['applicant_name'],
                'applicant_position' => $this->form['applicant_position'],
                'applicant_grade' => $this->form['applicant_grade'],
                'applicant_email' => Auth::user()?->email ?? $this->form['phone'].'@temp.motac.gov.my',
                'applicant_phone' => $this->form['phone'],
                'staff_id' => Auth::user()?->staff_id ?? 'GUEST',
                'grade' => $this->extractGrade($this->form['applicant_grade']),
                'division_id' => $this->form['division_id'],
                'purpose' => $this->form['purpose'],
                'location' => $this->form['location'],
                'loan_start_date' => $this->form['loan_start_date'],
                'expected_return_date' => $this->form['expected_return_date'],
                'loan_end_date' => $this->form['expected_return_date'], // For backward compatibility
                'items' => $this->form['equipment_items'],
                'applicant_digital_signature' => $this->form['applicant_digital_signature'],
                'terms_acknowledged' => $this->form['terms_acknowledged'],
                'is_responsible_officer' => $this->form['is_responsible_officer'],
                'approver_id' => $this->form['approver_id'],
            ];

            // Add responsible officer if different from applicant
            if (! $this->form['is_responsible_officer']) {
                $applicationData['responsible_officer_name'] = $this->form['responsible_officer_name'];
                $applicationData['responsible_officer_position'] = $this->form['responsible_officer_position'];
                $applicationData['responsible_officer_grade'] = $this->form['responsible_officer_grade'];
                $applicationData['responsible_officer_phone'] = $this->form['responsible_officer_phone'];
            }

            // Create loan application
            $loanService = app(LoanApplicationService::class);
            $application = $loanService->createHybridApplication($applicationData, Auth::user());

            DB::commit();

            // Redirect to success page with application number
            session()->flash('success', __('loan.messages.application_submitted', [
                'application_number' => $application->application_number,
            ]));

            // Use Livewire redirect method
            $this->redirect(route('loan.guest.apply'), navigate: true);
        } catch (\Exception $e) {
            DB::rollBack();

            $this->addError('submit', __('loan.messages.submission_failed'));

            // Log error for debugging
            logger()->error('Loan application submission failed', [
                'error' => $e->getMessage(),
                'form_data' => $this->form,
            ]);
        } finally {
            $this->submitting = false;
        }
    }

    private function extractGrade(string $position): string
    {
        // Extract grade from position string (e.g., "Pegawai Tadbir N41" -> "41")
        preg_match('/\d+/', $position, $matches);

        return $matches[0] ?? '41';
    }

    public function render(): View
    {
        $locale = app()->getLocale();
        $orderColumn = $locale === 'ms' ? 'name_ms' : 'name_en';

        // Fetch divisions with proper error handling
        $divisions = Division::query()
            ->where('is_active', true)
            ->orderBy($orderColumn)
            ->get([
                'id',
                'code',
                'name_ms',
                'name_en',
            ]);

        $layout = (Auth::check() || request()->routeIs('loan.authenticated.*'))
            ? 'layouts.portal'
            : 'layouts.front';

        return view('livewire.guest-loan-application', [
            'divisions' => $divisions,
            'equipmentTypes' => AssetCategory::where('is_active', true)
                ->orderBy('sort_order')
                ->orderBy('name')
                ->get(),
        ])->layout($layout);
    }
}
