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
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Validate;
use Livewire\Component;

class GuestLoanApplication extends Component
{
    public int $currentStep = 1;

    public bool $submitting = false;

    // Form data array
    public array $form = [
        // Step 1: Applicant Information
        'applicant_name' => '',
        'position' => '',
        'phone' => '',
        'division_id' => null,
        'purpose' => '',
        'location' => '',
        'loan_start_date' => '',
        'loan_end_date' => '',

        // Step 2: Responsible Officer
        'is_responsible_officer' => false,
        'responsible_officer_name' => '',
        'responsible_officer_position' => '',
        'responsible_officer_phone' => '',

        // Step 3: Equipment Items
        'equipment_items' => [
            ['equipment_type' => '', 'quantity' => 1, 'notes' => ''],
        ],
        'applicant_signature' => '',

        // Step 4: Confirmation
        'accept_terms' => false,
    ];

    protected array $stepValidationRules = [
        1 => [
            'form.applicant_name' => 'required|string|max:255',
            'form.position' => 'required|string|max:255',
            'form.phone' => 'required|string|max:20',
            'form.division_id' => 'required|exists:divisions,id',
            'form.purpose' => 'required|string|max:500',
            'form.location' => 'required|string|max:255',
            'form.loan_start_date' => 'required|date|after:today',
            'form.loan_end_date' => 'required|date|after:form.loan_start_date',
        ],
        2 => [
            'form.is_responsible_officer' => 'boolean',
            'form.responsible_officer_name' => 'required_if:form.is_responsible_officer,false|string|max:255',
            'form.responsible_officer_position' => 'required_if:form.is_responsible_officer,false|string|max:255',
            'form.responsible_officer_phone' => 'required_if:form.is_responsible_officer,false|string|max:20',
        ],
        3 => [
            'form.equipment_items' => 'required|array|min:1',
            'form.equipment_items.*.equipment_type' => 'required|exists:asset_categories,id',
            'form.equipment_items.*.quantity' => 'required|integer|min:1',
            'form.equipment_items.*.notes' => 'nullable|string|max:255',
        ],
        4 => [
            'form.accept_terms' => 'accepted',
        ],
    ];

    protected function messages(): array
    {
        return [
            'form.equipment_items.*.equipment_type.required' => __('loan.validation.equipment_type_required'),
            'form.equipment_items.*.equipment_type.exists' => __('loan.validation.equipment_type_exists'),
            'form.equipment_items.*.quantity.required' => __('loan.validation.quantity_required'),
            'form.equipment_items.*.quantity.integer' => __('loan.validation.quantity_integer'),
            'form.equipment_items.*.quantity.min' => __('loan.validation.quantity_min'),
        ];
    }

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
        if (auth()->check()) {
            $user = auth()->user();
            $this->form['applicant_name'] = $user->name ?? '';
            $this->form['phone'] = $user->phone ?? '';
            $this->form['division_id'] = $user->division_id;

            // Build position/grade string from user's data
            if ($user->grade) {
                $gradeName = app()->getLocale() === 'ms'
                    ? $user->grade->name_ms
                    : $user->grade->name_en;
                $this->form['position'] = $gradeName;
            }
        }

        // Initialize form with default values
        $this->form['loan_start_date'] = date('Y-m-d', strtotime('+1 day'));
        $this->form['loan_end_date'] = date('Y-m-d', strtotime('+7 days'));
    }

    public function nextStep(): void
    {
        // Validate current step with authentication-aware logic
        $this->validateCurrentStep();

        // Move to next step
        if ($this->currentStep < 4) {
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
        if (auth()->check()) {
            // Only validate loan-specific fields for authenticated users
            $this->validate([
                'form.purpose' => 'required|string|max:500',
                'form.location' => 'required|string|max:255',
                'form.loan_start_date' => 'required|date|after:today',
                'form.loan_end_date' => 'required|date|after:form.loan_start_date',
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

    /**
     * Alias for submitForm() - for testing compatibility
     */
    public function submit()
    {
        return $this->submitForm();
    }

    public function submitForm()
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
                'applicant_email' => auth()->user()?->email ?? $this->form['phone'].'@temp.motac.gov.my',
                'applicant_phone' => $this->form['phone'],
                'staff_id' => auth()->user()?->staff_id ?? 'GUEST',
                'grade' => $this->extractGrade($this->form['position']),
                'division_id' => $this->form['division_id'],
                'purpose' => $this->form['purpose'],
                'location' => $this->form['location'],
                'loan_start_date' => $this->form['loan_start_date'],
                'loan_end_date' => $this->form['loan_end_date'],
                'items' => $this->form['equipment_items'],
            ];

            // Add responsible officer if different from applicant
            if (! $this->form['is_responsible_officer']) {
                $applicationData['responsible_officer'] = [
                    'name' => $this->form['responsible_officer_name'],
                    'position' => $this->form['responsible_officer_position'],
                    'phone' => $this->form['responsible_officer_phone'],
                ];
            }

            // Create loan application
            $loanService = app(LoanApplicationService::class);
            $application = $loanService->createHybridApplication($applicationData, auth()->user());

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

    public function render()
    {
        $locale = app()->getLocale();
        $orderColumn = $locale === 'ms' ? 'name_ms' : 'name_en';

        $layout = (auth()->check() || request()->routeIs('loan.authenticated.*'))
            ? 'layouts.portal'
            : 'layouts.front';

        return view('livewire.guest-loan-application', [
            'divisions' => Division::query()
                ->orderBy($orderColumn)
                ->get([
                    'id',
                    'code',
                    'name_ms',
                    'name_en',
                ]),
            'equipmentTypes' => AssetCategory::orderBy('name')->get(),
        ])->layout($layout);
    }
}
