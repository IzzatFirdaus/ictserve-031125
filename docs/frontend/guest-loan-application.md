# Guest Loan Application System Documentation

## Overview

The Guest Loan Application system provides a multi-step wizard for ICT asset loan requests, supporting both guest (unauthenticated) and authenticated users. The system implements the hybrid architecture defined in D03-FR-042 and D04 §5.2.

## Architecture

### Components

1. **GuestLoanApplication** (`app/Livewire/GuestLoanApplication.php`) - Main Livewire component
2. **LoanApplicationService** (`app/Services/LoanApplicationService.php`) - Business logic service
3. **AssetAvailabilityService** (`app/Services/AssetAvailabilityService.php`) - Real-time availability checking
4. **WorkingDayCalculator** (`app/Services/WorkingDayCalculator.php`) - Lead time validation

### Form Structure (7 Steps)

| Step | Section    | Description                                    |
| ---- | ---------- | ---------------------------------------------- |
| 1    | BAHAGIAN 1 | Applicant Information (Maklumat Pemohon)       |
| 2    | BAHAGIAN 2 | Responsible Officer (Pegawai Bertanggungjawab) |
| 3    | BAHAGIAN 3 | Equipment Details (Butiran Peralatan)          |
| 4    | BAHAGIAN 4 | Terms & Conditions (Syarat-Syarat)             |
| 5    | BAHAGIAN 5 | Applicant Declaration (Pengesahan Pemohon)     |
| 6    | BAHAGIAN 6 | Approver Selection (Grade 41+)                 |
| 7    | Review     | Final Review & Submission                      |

## Features

### 1. Hybrid Architecture Support

The form automatically adapts based on authentication status:

**Authenticated Users:**

- Pre-filled name, phone, division from user profile
- Position and grade auto-populated from user relationships
- Reduced validation requirements for contact fields

**Guest Users:**

- All fields must be manually entered
- Temporary email generated for tracking
- Full validation on all contact fields

### 2. Working Day Calculator Integration

Enforces 3-day minimum lead time excluding:

- Weekends (Saturday, Sunday)
- Malaysian public holidays

```php
// Validation example
$calculator = app(WorkingDayCalculator::class);
if (!$calculator->validateLeadTime(now(), $startDate, 3)) {
    $nextAvailable = $calculator->getNextAvailableDate(now(), 3);
    // Show error with next available date
}
```

### 3. Emergency Request Override

Users can bypass the 3-day rule with:

- Emergency request checkbox
- Mandatory justification (minimum 50 characters)
- Priority automatically set to "urgent"

### 4. Real-Time Asset Availability

Equipment availability is checked in real-time:

```php
$availabilityService = app(AssetAvailabilityService::class);
$result = $availabilityService->checkCategoryAvailability(
    $categoryId,
    $startDate,
    $endDate,
    $quantity
);
// Returns: ['available' => bool, 'count' => int, 'message' => string]
```

### 5. Responsible Officer Delegation

When applicant is not the responsible officer:

- Toggle reveals additional fields
- Responsible officer details stored separately
- `is_delegate` flag set in database

### 6. Approver Search

Grade 41+ officers searchable by:

- Name
- Email
- Staff ID

Results include division and grade information.

## Database Schema

### loan_applications Table Extensions

```sql
-- BAHAGIAN 1: Extended applicant info
applicant_position VARCHAR(255),
applicant_grade VARCHAR(100),

-- BAHAGIAN 2: Responsible officer (conditional)
is_responsible_officer BOOLEAN DEFAULT TRUE,
responsible_officer_name VARCHAR(255),
responsible_officer_position VARCHAR(255),
responsible_officer_grade VARCHAR(100),
responsible_officer_phone VARCHAR(20),

-- BAHAGIAN 4: Declaration
applicant_digital_signature VARCHAR(255),
applicant_declaration_date TIMESTAMP,
terms_acknowledged BOOLEAN DEFAULT FALSE,
```

## Validation Rules

### Step 1: Applicant Information

| Field                   | Rules                                                |
| ----------------------- | ---------------------------------------------------- |
| applicant_name          | required, string, max:255                            |
| applicant_position      | required, string, max:255                            |
| applicant_grade         | required, string, max:100                            |
| phone                   | required, string, max:20                             |
| division_id             | required, exists:divisions,id                        |
| purpose                 | required, string, max:500                            |
| location                | required, string, max:255                            |
| loan_start_date         | required, date, after:today                          |
| expected_return_date    | required, date, after:loan_start_date                |
| emergency_justification | required_if:emergency_request,true, min:50, max:1000 |

### Step 2: Responsible Officer

| Field                        | Rules                                   |
| ---------------------------- | --------------------------------------- |
| responsible_officer_name     | required_if:is_responsible_officer,true |
| responsible_officer_position | required_if:is_responsible_officer,true |
| responsible_officer_grade    | required_if:is_responsible_officer,true |
| responsible_officer_phone    | required_if:is_responsible_officer,true |

### Step 3: Equipment

| Field                             | Rules                                |
| --------------------------------- | ------------------------------------ |
| equipment_items                   | required, array, min:1               |
| equipment_items.\*.equipment_type | required, exists:asset_categories,id |
| equipment_items.\*.quantity       | required, integer, min:1             |
| equipment_items.\*.notes          | nullable, string, max:255            |

### Step 4-6: Terms, Declaration, Approver

| Field                       | Rules                     |
| --------------------------- | ------------------------- |
| terms_acknowledged          | accepted                  |
| applicant_digital_signature | required, string, max:255 |
| approver_id                 | required, exists:users,id |

## Bilingual Support

All form labels and messages support Bahasa Melayu and English:

```php
// Translation files
lang/en/loan.php
lang/ms/loan.php

// Usage in component
__('loan.validation.equipment_type_required')
__('loan.messages.application_submitted', ['application_number' => $number])
```

## Error Handling

### Submission Errors

```php
try {
    DB::beginTransaction();
    // Create application
    DB::commit();
} catch (\Exception $e) {
    DB::rollBack();
    $this->addError('submit', __('loan.messages.submission_failed'));
    logger()->error('Loan application submission failed', [
        'error' => $e->getMessage(),
        'form_data' => $this->form,
    ]);
}
```

### Validation Errors

- Per-step validation prevents progression
- Real-time validation with `wire:model.live.debounce.300ms`
- Clear error messages in user's language

## Usage

### Route Registration

```php
// routes/web.php
Route::get('/loan/apply', GuestLoanApplication::class)
    ->name('loan.guest.apply');

Route::middleware('auth')->group(function () {
    Route::get('/portal/loan/apply', GuestLoanApplication::class)
        ->name('loan.authenticated.apply');
});
```

### View Integration

```blade
{{-- resources/views/livewire/guest-loan-application.blade.php --}}
<div class="max-w-4xl mx-auto">
    {{-- Progress indicator --}}
    <x-ui.progress-steps :current="$currentStep" :total="$totalSteps" />

    {{-- Step content --}}
    @switch($currentStep)
        @case(1)
            @include('livewire.loan.step-1-applicant')
            @break
        {{-- ... other steps --}}
    @endswitch

    {{-- Navigation buttons --}}
    <div class="flex justify-between mt-6">
        @if($currentStep > 1)
            <x-ui.button wire:click="previousStep">
                {{ __('loan.buttons.previous') }}
            </x-ui.button>
        @endif

        @if($currentStep < $totalSteps)
            <x-ui.button wire:click="nextStep" variant="primary">
                {{ __('loan.buttons.next') }}
            </x-ui.button>
        @else
            <x-ui.button wire:click="submitForm" variant="success" :disabled="$submitting">
                {{ __('loan.buttons.submit') }}
            </x-ui.button>
        @endif
    </div>
</div>
```

## Testing

### Feature Tests

```php
// tests/Feature/Livewire/GuestLoanApplicationTest.php
public function test_guest_can_submit_loan_application()
{
    Livewire::test(GuestLoanApplication::class)
        ->set('form.applicant_name', 'Test User')
        ->set('form.purpose', 'Official meeting')
        // ... set all required fields
        ->call('submitForm')
        ->assertHasNoErrors()
        ->assertRedirect(route('loan.guest.apply'));
}

public function test_validates_3_day_lead_time()
{
    Livewire::test(GuestLoanApplication::class)
        ->set('form.loan_start_date', now()->addDay()->format('Y-m-d'))
        ->call('nextStep')
        ->assertHasErrors(['form.loan_start_date']);
}
```

## Compliance

### ISO Document Reference

- **Document ID**: PK.(S).MOTAC.07.(L3)
- **Display Location**: Top-right corner of form header
- **Terms & Conditions**: 11 specific terms from ISO document

### WCAG 2.2 AA Compliance

- All form fields have proper labels
- Error messages linked to fields via ARIA
- Keyboard navigation supported
- Focus management between steps
- 4.5:1 text contrast ratio

### D00-D15 Traceability

- **D03-FR-042**: Asset Loan Application requirements
- **D04 §5.2**: Loan Module Design specifications
- **D09**: Database schema documentation
- **D15**: Bilingual support requirements

---

**Document Version**: 1.0  
**Last Updated**: 2025-11-27  
**Author**: ICTServe Development Team  
**Status**: Production Ready
