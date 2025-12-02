<div style="display: flex; flex-direction: column; align-items: center; justify-content: center; text-align: center; padding: 200px 0 20px 0;">

<div style="font-size: 24px; margin-bottom: 12px;"><strong>Amazon Q</strong></div>
<div style="background: rgba(128, 128, 128, 0.15); border: 1px solid rgba(128, 128, 128, 0.25); border-radius: 8px; padding: 8px; margin: 4px 0; text-align: center;">
<div style="font-size: 14px; margin-bottom: 4px;"><strong>Did you know?</strong></div>
<div>Create and add Saved Prompts using the @ context menu</div>
</div>

Select code & ask me to explain, debug or optimize it, or type `/` for quick actions

</div>

---

plan to implement similar if not close to the original helpdesk thats ictserve currently will be replacing it

---

I'll create a comprehensive implementation plan to replicate the original helpdesk form functionality in ICTServe.

---

sequentialthinking

---

sequentialthinking

---

sequentialthinking

---

sequentialthinking

---

sequentialthinking

---

# Implementation Plan: Original Helpdesk Form Replication

## 📋 Overview

Replicate the original MOTAC helpdesk form functionality with exact field mappings, department/division lists, and issue categories.

---

## 🎯 Phase 1: Database Schema (Day 1 - Morning)

### 1.1 Create Division Model & Migration

```bash
php artisan make:model Division -mfs
php artisan make:migration add_division_and_job_grade_to_helpdesk_tickets
```

**Migration: `add_division_and_job_grade_to_helpdesk_tickets`**

```php
Schema::table('helpdesk_tickets', function (Blueprint $table) {
    $table->foreignId('division_id')->nullable()->after('guest_phone')->constrained()->nullOnDelete();
    $table->string('job_grade')->nullable()->after('division_id');
    $table->boolean('declaration_accepted')->default(false)->after('description');
});
```

### 1.2 Division Seeder Data

**Key divisions from screenshots:**

- Pejabat Menteri
- Pejabat Timbalan Menteri  
- Pejabat Ketua Setiausaha
- Pejabat Timbalan Ketua Setiausaha (Kebudayaan/Pelancongan/Pengurusan)
- 15+ Bahagian (divisions)
- 14+ State offices
- Special units (Audit, Integriti, KPI, COVID-19, etc.)

### 1.3 Update Ticket Categories

**Exact categories from original:**

1. Perkakasan - PC, Laptop, Printer, Scanner, Projektor
2. Perkakasan - iPad
3. Perkakasan - Permohonan Toner
4. Aplikasi - E-Mail, Anti Virus
5. Aplikasi - iGFMAS, HRMIS, ePerolehan, GPKI
6. Aplikasi - Laman Web, INTRAnet
7. Aplikasi - Sistem Kehadiran (e-Jari)
8. Aplikasi - Lain-lain
9. Network - Ibu Pejabat
10. Network - Pejabat Negeri
11. Lain-lain Aduan

---

## 🎨 Phase 2: Frontend Form (Day 1 - Afternoon)

### 2.1 Update Livewire Component

**Component: `app/Livewire/GuestHelpdeskForm.php`**

Add properties:

```php
public ?int $division_id = null;
public string $job_grade = '';
public bool $declaration_accepted = false;
```

Update validation:

```php
protected function rules(): array
{
    return [
        'guest_name' => ['required', 'string', 'max:255'],
        'division_id' => ['required', 'exists:divisions,id'],
        'job_grade' => ['nullable', 'string', 'max:100'],
        'guest_email' => ['required', 'email', 'max:255'],
        'guest_phone' => ['required', 'string', 'max:20'],
        'category_id' => ['required', 'exists:ticket_categories,id'],
        'description' => ['required', 'string', 'min:10'],
        'declaration_accepted' => ['accepted'],
    ];
}
```

### 2.2 Form Layout (Blade View)

**Match original design:**

- Light gray input backgrounds (`bg-gray-100`)
- Full-width inputs
- Proper spacing between fields
- Declaration checkbox with full text
- "Sila Pilih" placeholders for dropdowns

---

## 🌐 Phase 3: Translations (Day 2 - Morning)

### 3.1 Malay Translations

**File: `resources/lang/ms/helpdesk.php`**

Add keys:

- `job_grade`
- `division`
- `declaration_text`
- Division names array
- Issue category names array

### 3.2 English Translations

**File: `resources/lang/en/helpdesk.php`**

Mirror structure with English equivalents.

---

## 🧪 Phase 4: Testing (Day 2 - Afternoon)

### 4.1 Feature Tests

```bash
php artisan make:test Helpdesk/GuestHelpdeskFormTest
```

Test scenarios:

- ✅ Form submission with all required fields
- ✅ Validation errors for missing fields
- ✅ Division dropdown population
- ✅ Category dropdown with exact options
- ✅ Declaration checkbox requirement

### 4.2 E2E Tests (Playwright)

Update `tests/e2e/helpdesk.refactored.spec.ts`:

- Test division selection
- Test job grade input
- Test declaration checkbox
- Verify form matches original layout

---

## 🎛️ Phase 5: Admin Panel (Day 3)

### 5.1 Update Filament Resource

**File: `app/Filament/Resources/HelpdeskTicketResource.php`**

Add columns:

- Division (with relationship)
- Job Grade
- Declaration status

Add filters:

- Filter by division
- Filter by category (updated list)

---

## 📊 Implementation Checklist

### Database & Models

- [ ] Create Division model, migration, factory, seeder
- [ ] Add division_id, job_grade, declaration_accepted to helpdesk_tickets
- [ ] Seed divisions with exact MOTAC structure (60+ entries)
- [ ] Update ticket categories seeder (11 exact categories)
- [ ] Update HelpdeskTicket model relationships

### Backend Logic

- [ ] Update GuestHelpdeskForm component properties
- [ ] Add validation rules for new fields
- [ ] Update form submission logic
- [ ] Update email notifications to include division & job grade

### Frontend UI

- [ ] Update helpdesk form Blade view layout
- [ ] Add division dropdown (searchable, 60+ options)
- [ ] Add job grade text input
- [ ] Update category dropdown with exact 11 options
- [ ] Add declaration checkbox with full BM text
- [ ] Apply styling to match original (gray inputs, spacing)
- [ ] Ensure WCAG 2.2 AA compliance

### Translations

- [ ] Add MS translations for divisions (60+ entries)
- [ ] Add MS translations for categories (11 entries)
- [ ] Add MS declaration text
- [ ] Add EN translations (mirror structure)
- [ ] Update form labels and placeholders

### Testing

- [ ] Feature test: Form submission with new fields
- [ ] Feature test: Validation rules
- [ ] Feature test: Division relationship
- [ ] E2E test: Complete form flow
- [ ] Accessibility test: WCAG compliance
- [ ] Manual test: Compare with original screenshots

### Admin Panel

- [ ] Update HelpdeskTicketResource table columns
- [ ] Add division filter
- [ ] Update form schema for editing
- [ ] Test bulk operations with new fields

---

## 📁 Files to Create/Modify

### New Files (5)

1. `app/Models/Division.php`
2. `database/migrations/YYYY_MM_DD_create_divisions_table.php`
3. `database/migrations/YYYY_MM_DD_add_division_and_job_grade_to_helpdesk_tickets.php`
4. `database/factories/DivisionFactory.php`
5. `database/seeders/DivisionSeeder.php`

### Modified Files (15)

1. `app/Livewire/GuestHelpdeskForm.php`
2. `app/Models/HelpdeskTicket.php`
3. `resources/views/livewire/guest-helpdesk-form.blade.php`
4. `resources/lang/ms/helpdesk.php`
5. `resources/lang/en/helpdesk.php`
6. `database/seeders/TicketCategorySeeder.php`
7. `app/Filament/Resources/HelpdeskTicketResource.php`
8. `app/Filament/Resources/HelpdeskTicketResource/Pages/ListHelpdeskTickets.php`
9. `app/Mail/TicketCreatedConfirmation.php`
10. `resources/views/emails/helpdesk/ticket-created.blade.php`
11. `tests/Feature/Helpdesk/GuestHelpdeskFormTest.php`
12. `tests/e2e/helpdesk.refactored.spec.ts`
13. `app/Policies/HelpdeskTicketPolicy.php`
14. `routes/web.php` (if route changes needed)
15. `database/seeders/DatabaseSeeder.php`

---

## ⏱️ Estimated Timeline

| Phase | Duration | Priority |
|-------|----------|----------|
| Database & Models | 3 hours | HIGH |
| Backend Logic | 2 hours | HIGH |
| Frontend UI | 4 hours | HIGH |
| Translations | 2 hours | MEDIUM |
| Testing | 3 hours | HIGH |
| Admin Panel | 2 hours | MEDIUM |
| **Total** | **16 hours (2 days)** | |

---

## 🚀 Next Steps

1. **Review & Approve Plan** - Confirm approach with stakeholders
2. **Start Phase 1** - Database schema and Division model
3. **Iterative Development** - Complete each phase with testing
4. **User Acceptance Testing** - Compare with original form
5. **Deployment** - Migrate data and deploy to production

---

**Ready to proceed with implementation?** I can start with Phase 1 (Database Schema) immediately.

---

plan to implement similar if not close to the loan application form (PK.(S).MOTAC.07.(L3)) that ictserve currently will be digitizing it as one of the two main modules, the other is the helpdesk module.

- BAHAGIAN 1
- BAHAGIAN 2 (only applicable if the applicant is the PEGAWAI BERTANGGUNGJAWAB as in not the applicant, but the applicant&#39;s surrogate. but the loaned asset is within the PEGAWAI BERTANGGUNGJAWAB&#39;s responsibility.)
- BAHAGIAN 3
- BAHAGIAN 4 (applicant must confirm their responsibility of the said loaned asset. no signage or stamp of authority needed only a digitized computer signage)
- BAHAGIAN 5 (this must be approved by any officer staff who are GRADE 41 and above only. this section of the application must be emailed/notified through in-system notification to the related OFFICER. their name selected by the applicant by typing into [query from db the list of officers with grade 41+] and the applicant will await the approval or decline by the approver officer.)
- BAHAGIAN 6 &amp; 7 (will be managed by the staff of BPM - Bahagian Pengurusan Maklumat, Unit Operasi, Teknikal &amp; Khidmat Pengguna with the applicant or the PEGAWAI BERTANGGUNGJAWAB during the PENGELUARAN ASET and PEMULANGAN ASET phases.
- BAHAGIAN 8 (will only be handled by the staff of BPM - Bahagian Pengurusan Maklumat, Unit Operasi, Teknikal &amp; Khidmat Pengguna in regards to the loaned assets)

included before the end of the application form, the applicant should be reading the mandatory SYARAT-SYARAT PERMOHONAN PEMINJAMAN PERALATAN ICT UNTUK KEGUNAAN RASMI KEMENTERIAN PELANCONGAN, SENI DAN BUDAYA. All 11 points. but for the last point, it should be adapted suitable with the digitized version of the form.)

---

I'll create a comprehensive implementation plan for the ICT Asset Loan Application form digitization.

---

sequentialthinking

---

sequentialthinking

---

sequentialthinking

---

sequentialthinking

---

sequentialthinking

---

sequentialthinking

---

sequentialthinking

---

sequentialthinking

---

# Implementation Plan: ICT Asset Loan Application Form Digitization
## PK.(S).MOTAC.07.(L3)

---

## 📋 Executive Summary

Digitize the physical ICT Asset Loan Application form into a multi-step web application with:

- **7-step wizard** for applicant submission
- **Email-based approval** workflow (Grade 41+ officers)
- **BPM staff workflows** for asset issuance and return
- **Bilingual support** (MS/EN)
- **WCAG 2.2 AA compliance**

---

## 🎯 Form Structure Analysis

### Section Breakdown

| Section | Title | Responsibility | Implementation |
|---------|-------|----------------|----------------|
| **Bahagian 1** | Maklumat Pemohon | Applicant | Multi-step form (Step 1) |
| **Bahagian 2** | Maklumat Pegawai Bertanggungjawab | Applicant (conditional) | Multi-step form (Step 2) |
| **Bahagian 3** | Maklumat Peralatan | Applicant | Multi-step form (Step 3) |
| **Syarat-Syarat** | Terms & Conditions | Applicant | Multi-step form (Step 4) |
| **Bahagian 4** | Pengesahan Pemohon | Applicant | Multi-step form (Step 5) |
| **Bahagian 5** | Pengesahan Bahagian/Unit | Grade 41+ Officer | Email approval workflow (Step 6) |
| **Bahagian 6** | Semasa Peminjaman | BPM Staff | Filament admin panel |
| **Bahagian 7** | Semasa Pemulangan | BPM Staff | Filament admin panel |
| **Bahagian 8** | Maklumat Peminjaman | BPM Staff | Filament admin panel |

---

## 🗄️ Phase 1: Database Schema (Days 1-2)

### 1.1 Extend loan_applications Table

```bash
php artisan make:migration extend_loan_applications_table
```

**Migration:**

```php
Schema::table('loan_applications', function (Blueprint $table) {
    // Bahagian 1
    $table->string('applicant_position')->after('guest_name');
    $table->string('applicant_grade')->after('applicant_position');
    $table->foreignId('division_id')->nullable()->after('applicant_grade')->constrained();
    $table->text('purpose')->after('division_id');
    $table->string('location')->after('purpose');
    $table->date('loan_start_date')->after('location');
    $table->date('expected_return_date')->after('loan_start_date');
    
    // Bahagian 2
    $table->boolean('is_responsible_officer')->default(true)->after('expected_return_date');
    $table->string('responsible_officer_name')->nullable()->after('is_responsible_officer');
    $table->string('responsible_officer_position')->nullable();
    $table->string('responsible_officer_grade')->nullable();
    $table->string('responsible_officer_phone')->nullable();
    
    // Bahagian 4
    $table->timestamp('applicant_declaration_date')->nullable();
    $table->string('applicant_digital_signature')->nullable();
    $table->boolean('terms_acknowledged')->default(false);
    
    // Bahagian 5
    $table->foreignId('approver_id')->nullable()->constrained('users');
    $table->enum('approval_status', ['pending', 'approved', 'rejected'])->default('pending');
    $table->timestamp('approval_date')->nullable();
    $table->string('approver_digital_signature')->nullable();
    $table->text('approval_notes')->nullable();
    
    // Status tracking
    $table->enum('status', [
        'draft', 'pending_approval', 'approved', 'rejected',
        'issued', 'active', 'returned', 'completed'
    ])->default('draft');
});
```

### 1.2 Create loan_items Table

```bash
php artisan make:model LoanItem -mf
```

**Migration:**

```php
Schema::create('loan_items', function (Blueprint $table) {
    $table->id();
    $table->foreignId('loan_application_id')->constrained()->cascadeOnDelete();
    $table->string('equipment_type'); // Bahagian 3
    $table->integer('quantity');
    $table->text('notes')->nullable();
    
    // Bahagian 8 (filled by BPM staff)
    $table->foreignId('asset_id')->nullable()->constrained();
    $table->string('brand_model')->nullable();
    $table->string('serial_number')->nullable();
    $table->json('accessories')->nullable(); // Checkboxes
    $table->text('other_accessories')->nullable();
    
    $table->timestamps();
});
```

### 1.3 Create loan_transactions Table

```bash
php artisan make:model LoanTransaction -mf
```

**Migration:**

```php
Schema::create('loan_transactions', function (Blueprint $table) {
    $table->id();
    $table->foreignId('loan_application_id')->constrained()->cascadeOnDelete();
    $table->enum('transaction_type', ['issuance', 'return']);
    
    // Bahagian 6 & 7
    $table->foreignId('issuing_officer_id')->nullable()->constrained('users');
    $table->foreignId('receiving_officer_id')->nullable()->constrained('users');
    $table->timestamp('transaction_date');
    $table->text('notes')->nullable();
    $table->string('issuing_officer_signature')->nullable();
    $table->string('receiving_officer_signature')->nullable();
    
    $table->timestamps();
});
```

### 1.4 Create loan_approval_tokens Table

```bash
php artisan make:migration create_loan_approval_tokens_table
```

**Migration:**

```php
Schema::create('loan_approval_tokens', function (Blueprint $table) {
    $table->id();
    $table->foreignId('loan_application_id')->constrained()->cascadeOnDelete();
    $table->string('token', 64)->unique();
    $table->timestamp('expires_at');
    $table->boolean('used')->default(false);
    $table->timestamp('used_at')->nullable();
    $table->timestamps();
});
```

### 1.5 Create Enums

```bash
php artisan make:enum LoanStatus
php artisan make:enum ApprovalStatus
php artisan make:enum TransactionType
```

**LoanStatus.php:**

```php
enum LoanStatus: string
{
    case DRAFT = 'draft';
    case PENDING_APPROVAL = 'pending_approval';
    case APPROVED = 'approved';
    case REJECTED = 'rejected';
    case ISSUED = 'issued';
    case ACTIVE = 'active';
    case RETURNED = 'returned';
    case COMPLETED = 'completed';
    
    public function label(): string
    {
        return match($this) {
            self::DRAFT => __('loan.status.draft'),
            self::PENDING_APPROVAL => __('loan.status.pending_approval'),
            // ... etc
        };
    }
}
```

### 1.6 Update Models

**LoanApplication.php:**

```php
protected $fillable = [
    'applicant_position', 'applicant_grade', 'division_id', 'purpose',
    'location', 'loan_start_date', 'expected_return_date',
    'is_responsible_officer', 'responsible_officer_name',
    'responsible_officer_position', 'responsible_officer_grade',
    'responsible_officer_phone', 'applicant_declaration_date',
    'applicant_digital_signature', 'terms_acknowledged',
    'approver_id', 'approval_status', 'approval_date',
    'approver_digital_signature', 'approval_notes', 'status',
];

protected function casts(): array
{
    return [
        'loan_start_date' => 'date',
        'expected_return_date' => 'date',
        'is_responsible_officer' => 'boolean',
        'terms_acknowledged' => 'boolean',
        'applicant_declaration_date' => 'datetime',
        'approval_date' => 'datetime',
        'status' => LoanStatus::class,
        'approval_status' => ApprovalStatus::class,
    ];
}

public function division(): BelongsTo
{
    return $this->belongsTo(Division::class);
}

public function approver(): BelongsTo
{
    return $this->belongsTo(User::class, 'approver_id');
}

public function items(): HasMany
{
    return $this->hasMany(LoanItem::class);
}

public function transactions(): HasMany
{
    return $this->hasMany(LoanTransaction::class);
}
```

**User.php (add scope):**

```php
public function scopeGrade41AndAbove(Builder $query): Builder
{
    return $query->where('grade', '>=', 41);
}
```

---

## 🎨 Phase 2: Multi-Step Form (Days 3-5)

### 2.1 Create Main Livewire Component

```bash
php artisan make:livewire GuestLoanApplicationForm
```

**Component Structure:**

```php
<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\Validate;

class GuestLoanApplicationForm extends Component
{
    // Step management
    public int $currentStep = 1;
    public int $totalSteps = 7;
    
    // Bahagian 1
    #[Validate('required|string|max:255')]
    public string $applicant_name = '';
    
    #[Validate('required|string|max:255')]
    public string $applicant_position = '';
    
    #[Validate('required|string|max:50')]
    public string $applicant_grade = '';
    
    #[Validate('required|string|max:20')]
    public string $applicant_phone = '';
    
    #[Validate('required|exists:divisions,id')]
    public ?int $division_id = null;
    
    #[Validate('required|string')]
    public string $purpose = '';
    
    #[Validate('required|string|max:255')]
    public string $location = '';
    
    #[Validate('required|date|after_or_equal:today')]
    public $loan_start_date = null;
    
    #[Validate('required|date|after:loan_start_date')]
    public $expected_return_date = null;
    
    // Bahagian 2
    public bool $is_responsible_officer = true;
    
    #[Validate('required_if:is_responsible_officer,false|nullable|string|max:255')]
    public string $responsible_officer_name = '';
    
    #[Validate('required_if:is_responsible_officer,false|nullable|string|max:255')]
    public string $responsible_officer_position = '';
    
    #[Validate('required_if:is_responsible_officer,false|nullable|string|max:50')]
    public string $responsible_officer_grade = '';
    
    #[Validate('required_if:is_responsible_officer,false|nullable|string|max:20')]
    public string $responsible_officer_phone = '';
    
    // Bahagian 3
    public array $equipment_items = [
        ['equipment_type' => '', 'quantity' => 1, 'notes' => '']
    ];
    
    // Bahagian 4
    public bool $terms_acknowledged = false;
    
    // Bahagian 5
    public string $applicant_signature = '';
    
    // Bahagian 6
    public ?int $approver_id = null;
    public string $approver_search = '';
    
    public function mount(): void
    {
        // Load draft if exists
    }
    
    public function nextStep(): void
    {
        $this->validateCurrentStep();
        $this->currentStep++;
        $this->saveDraft();
    }
    
    public function previousStep(): void
    {
        $this->currentStep--;
    }
    
    public function goToStep(int $step): void
    {
        if ($step < $this->currentStep) {
            $this->currentStep = $step;
        }
    }
    
    protected function validateCurrentStep(): void
    {
        match($this->currentStep) {
            1 => $this->validate([
                'applicant_name' => 'required|string|max:255',
                'applicant_position' => 'required|string|max:255',
                'applicant_grade' => 'required|string|max:50',
                'applicant_phone' => 'required|string|max:20',
                'division_id' => 'required|exists:divisions,id',
                'purpose' => 'required|string',
                'location' => 'required|string|max:255',
                'loan_start_date' => 'required|date|after_or_equal:today',
                'expected_return_date' => 'required|date|after:loan_start_date',
            ]),
            2 => $this->validateBahagian2(),
            3 => $this->validateBahagian3(),
            4 => $this->validate(['terms_acknowledged' => 'accepted']),
            5 => $this->validate(['applicant_signature' => 'required|string|min:3']),
            6 => $this->validate(['approver_id' => 'required|exists:users,id']),
            default => null,
        };
    }
    
    protected function validateBahagian2(): void
    {
        if (!$this->is_responsible_officer) {
            $this->validate([
                'responsible_officer_name' => 'required|string|max:255',
                'responsible_officer_position' => 'required|string|max:255',
                'responsible_officer_grade' => 'required|string|max:50',
                'responsible_officer_phone' => 'required|string|max:20',
            ]);
        }
    }
    
    protected function validateBahagian3(): void
    {
        $this->validate([
            'equipment_items' => 'required|array|min:1',
            'equipment_items.*.equipment_type' => 'required|string',
            'equipment_items.*.quantity' => 'required|integer|min:1',
        ]);
    }
    
    public function addEquipmentRow(): void
    {
        $this->equipment_items[] = ['equipment_type' => '', 'quantity' => 1, 'notes' => ''];
    }
    
    public function removeEquipmentRow(int $index): void
    {
        unset($this->equipment_items[$index]);
        $this->equipment_items = array_values($this->equipment_items);
    }
    
    public function searchApprovers(): Collection
    {
        return User::grade41AndAbove()
            ->where('name', 'like', "%{$this->approver_search}%")
            ->limit(10)
            ->get();
    }
    
    public function selectApprover(int $userId): void
    {
        $this->approver_id = $userId;
        $this->approver_search = User::find($userId)->name;
    }
    
    public function submitApplication(): void
    {
        $this->validateCurrentStep();
        
        $application = LoanApplication::create([
            'guest_name' => $this->applicant_name,
            'guest_email' => $this->applicant_email,
            'guest_phone' => $this->applicant_phone,
            'applicant_position' => $this->applicant_position,
            'applicant_grade' => $this->applicant_grade,
            'division_id' => $this->division_id,
            'purpose' => $this->purpose,
            'location' => $this->location,
            'loan_start_date' => $this->loan_start_date,
            'expected_return_date' => $this->expected_return_date,
            'is_responsible_officer' => $this->is_responsible_officer,
            'responsible_officer_name' => $this->responsible_officer_name,
            'responsible_officer_position' => $this->responsible_officer_position,
            'responsible_officer_grade' => $this->responsible_officer_grade,
            'responsible_officer_phone' => $this->responsible_officer_phone,
            'applicant_declaration_date' => now(),
            'applicant_digital_signature' => $this->applicant_signature,
            'terms_acknowledged' => $this->terms_acknowledged,
            'approver_id' => $this->approver_id,
            'status' => LoanStatus::PENDING_APPROVAL,
        ]);
        
        // Create loan items
        foreach ($this->equipment_items as $item) {
            $application->items()->create($item);
        }
        
        // Send approval email
        $this->sendApprovalRequest($application);
        
        // Redirect to confirmation
        $this->redirect(route('loan.confirmation', $application));
    }
    
    protected function saveDraft(): void
    {
        // Save to session or database
    }
    
    public function render()
    {
        return view('livewire.guest-loan-application-form');
    }
}
```

### 2.2 Create Blade View with Step Navigation

**resources/views/livewire/guest-loan-application-form.blade.php:**

```blade
<div class="max-w-4xl mx-auto p-6">
    <!-- Progress Indicator -->
    <div class="mb-8">
        <div class="flex justify-between items-center">
            @for ($i = 1; $i <= $totalSteps; $i++)
                <div class="flex-1">
                    <div class="flex items-center">
                        <div class="w-10 h-10 rounded-full flex items-center justify-center
                            {{ $currentStep >= $i ? 'bg-blue-600 text-white' : 'bg-gray-300 text-gray-600' }}">
                            {{ $i }}
                        </div>
                        @if ($i < $totalSteps)
                            <div class="flex-1 h-1 {{ $currentStep > $i ? 'bg-blue-600' : 'bg-gray-300' }}"></div>
                        @endif
                    </div>
                    <div class="text-xs mt-2 text-center">
                        {{ __("loan.step_{$i}_title") }}
                    </div>
                </div>
            @endfor
        </div>
    </div>

    <!-- Form Steps -->
    <form wire:submit="submitApplication">
        @if ($currentStep === 1)
            @include('livewire.loan-steps.step-1-applicant-info')
        @elseif ($currentStep === 2)
            @include('livewire.loan-steps.step-2-responsible-officer')
        @elseif ($currentStep === 3)
            @include('livewire.loan-steps.step-3-equipment')
        @elseif ($currentStep === 4)
            @include('livewire.loan-steps.step-4-terms')
        @elseif ($currentStep === 5)
            @include('livewire.loan-steps.step-5-declaration')
        @elseif ($currentStep === 6)
            @include('livewire.loan-steps.step-6-approver')
        @elseif ($currentStep === 7)
            @include('livewire.loan-steps.step-7-confirmation')
        @endif

        <!-- Navigation Buttons -->
        <div class="flex justify-between mt-8">
            @if ($currentStep > 1)
                <button type="button" wire:click="previousStep" class="btn btn-secondary">
                    {{ __('loan.previous') }}
                </button>
            @endif

            @if ($currentStep < $totalSteps)
                <button type="button" wire:click="nextStep" class="btn btn-primary ml-auto">
                    {{ __('loan.next') }}
                </button>
            @else
                <button type="submit" class="btn btn-primary ml-auto">
                    {{ __('loan.submit') }}
                </button>
            @endif
        </div>
    </form>
</div>
```

---

## 📧 Phase 3: Approval Workflow (Days 6-7)

### 3.1 Create Approval Token System

**Service: DualApprovalService.php:**

```php
<?php

namespace App\Services;

use App\Models\LoanApplication;
use App\Models\LoanApprovalToken;
use Illuminate\Support\Str;

class DualApprovalService
{
    public function generateApprovalToken(LoanApplication $application): LoanApprovalToken
    {
        return LoanApprovalToken::create([
            'loan_application_id' => $application->id,
            'token' => Str::random(64),
            'expires_at' => now()->addDays(7),
        ]);
    }
    
    public function validateToken(string $token): ?LoanApprovalToken
    {
        return LoanApprovalToken::where('token', $token)
            ->where('used', false)
            ->where('expires_at', '>', now())
            ->first();
    }
    
    public function approveApplication(LoanApprovalToken $token, string $signature): void
    {
        $application = $token->loanApplication;
        
        $application->update([
            'approval_status' => ApprovalStatus::APPROVED,
            'approval_date' => now(),
            'approver_digital_signature' => $signature,
            'status' => LoanStatus::APPROVED,
        ]);
        
        $token->update(['used' => true, 'used_at' => now()]);
        
        // Send approval notification to applicant
        Mail::to($application->guest_email)->send(new LoanApplicationApproved($application));
    }
    
    public function rejectApplication(LoanApprovalToken $token, string $reason): void
    {
        $application = $token->loanApplication;
        
        $application->update([
            'approval_status' => ApprovalStatus::REJECTED,
            'approval_date' => now(),
            'approval_notes' => $reason,
            'status' => LoanStatus::REJECTED,
        ]);
        
        $token->update(['used' => true, 'used_at' => now()]);
        
        // Send rejection notification to applicant
        Mail::to($application->guest_email)->send(new LoanApplicationRejected($application));
    }
}
```

### 3.2 Create Approval Controller

```bash
php artisan make:controller LoanApprovalController
```

**LoanApprovalController.php:**

```php
<?php

namespace App\Http\Controllers;

use App\Services\DualApprovalService;
use Illuminate\Http\Request;

class LoanApprovalController extends Controller
{
    public function __construct(
        private DualApprovalService $approvalService
    ) {}
    
    public function approve(string $token)
    {
        $approvalToken = $this->approvalService->validateToken($token);
        
        if (!$approvalToken) {
            return view('loan.approval-expired');
        }
        
        return view('loan.approve', [
            'application' => $approvalToken->loanApplication,
            'token' => $token,
        ]);
    }
    
    public function submitApproval(Request $request, string $token)
    {
        $request->validate([
            'signature' => 'required|string|min:3',
        ]);
        
        $approvalToken = $this->approvalService->validateToken($token);
        
        if (!$approvalToken) {
            return redirect()->route('loan.approval-expired');
        }
        
        $this->approvalService->approveApplication($approvalToken, $request->signature);
        
        return view('loan.approval-success');
    }
    
    public function reject(string $token)
    {
        $approvalToken = $this->approvalService->validateToken($token);
        
        if (!$approvalToken) {
            return view('loan.approval-expired');
        }
        
        return view('loan.reject', [
            'application' => $approvalToken->loanApplication,
            'token' => $token,
        ]);
    }
    
    public function submitRejection(Request $request, string $token)
    {
        $request->validate([
            'reason' => 'required|string|min:10',
        ]);
        
        $approvalToken = $this->approvalService->validateToken($token);
        
        if (!$approvalToken) {
            return redirect()->route('loan.approval-expired');
        }
        
        $this->approvalService->rejectApplication($approvalToken, $request->reason);
        
        return view('loan.rejection-success');
    }
}
```

### 3.3 Create Email Mailables

```bash
php artisan make:mail LoanApplicationSubmitted
php artisan make:mail LoanApprovalRequest
php artisan make:mail LoanApplicationApproved
php artisan make:mail LoanApplicationRejected
php artisan make:mail AssetReadyForCollection
php artisan make:mail AssetReturnReminder
php artisan make:mail AssetReturnConfirmation
```

**Example: LoanApprovalRequest.php:**

```php
<?php

namespace App\Mail;

use App\Models\LoanApplication;
use App\Models\LoanApprovalToken;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class LoanApprovalRequest extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public LoanApplication $application,
        public LoanApprovalToken $token
    ) {}

    public function build()
    {
        return $this->subject(__('loan.email.approval_request_subject', [
                'ref' => $application->reference_number
            ]))
            ->markdown('emails.loan.approval-request');
    }
}
```

**Email Template: approval-request.blade.php:**

```blade
@component('mail::message')
# {{ __('loan.email.approval_request_title') }}

{{ __('loan.email.approval_request_intro', ['name' => $application->guest_name]) }}

## {{ __('loan.application_details') }}

- **{{ __('loan.applicant_name') }}**: {{ $application->guest_name }}
- **{{ __('loan.division') }}**: {{ $application->division->name_ms }}
- **{{ __('loan.purpose') }}**: {{ $application->purpose }}
- **{{ __('loan.loan_period') }}**: {{ $application->loan_start_date->format('d/m/Y') }} - {{ $application->expected_return_date->format('d/m/Y') }}

## {{ __('loan.equipment_requested') }}

@foreach($application->items as $item)
- {{ $item->equipment_type }} ({{ $item->quantity }} {{ __('loan.units') }})
@endforeach

@component('mail::button', ['url' => route('loan.approve', $token->token), 'color' => 'success'])
{{ __('loan.approve_application') }}
@endcomponent

@component('mail::button', ['url' => route('loan.reject', $token->token), 'color' => 'error'])
{{ __('loan.reject_application') }}
@endcomponent

{{ __('loan.email.approval_note') }}

{{ __('common.regards') }},<br>
{{ config('app.name') }}
@endcomponent
```

---

## 🎛️ Phase 4: BPM Staff Workflows (Days 8-10)

### 4.1 Create Filament Resource

```bash
php artisan make:filament-resource LoanApplication --no-interaction
```

**LoanApplicationResource.php:**

```php
<?php

namespace App\Filament\Resources;

use App\Filament\Resources\LoanApplicationResource\Pages;
use App\Models\LoanApplication;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class LoanApplicationResource extends Resource
{
    protected static ?string $model = LoanApplication::class;
    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';
    protected static ?string $navigationLabel = 'Permohonan Pinjaman';
    protected static ?string $navigationGroup = 'Pengurusan Aset';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Maklumat Pemohon')
                    ->schema([
                        Forms\Components\TextInput::make('guest_name')
                            ->label('Nama Penuh')
                            ->disabled(),
                        Forms\Components\TextInput::make('applicant_position')
                            ->label('Jawatan & Gred')
                            ->disabled(),
                        Forms\Components\Select::make('division_id')
                            ->label('Bahagian/Unit')
                            ->relationship('division', 'name_ms')
                            ->disabled(),
                        Forms\Components\Textarea::make('purpose')
                            ->label('Tujuan Permohonan')
                            ->disabled(),
                    ])
                    ->columns(2),
                    
                Forms\Components\Section::make('Tempoh Pinjaman')
                    ->schema([
                        Forms\Components\DatePicker::make('loan_start_date')
                            ->label('Tarikh Pinjaman')
                            ->disabled(),
                        Forms\Components\DatePicker::make('expected_return_date')
                            ->label('Tarikh Dijangka Pulang')
                            ->disabled(),
                    ])
                    ->columns(2),
                    
                Forms\Components\Section::make('Status Kelulusan')
                    ->schema([
                        Forms\Components\Select::make('approval_status')
                            ->label('Status')
                            ->options([
                                'pending' => 'Menunggu Kelulusan',
                                'approved' => 'Diluluskan',
                                'rejected' => 'Ditolak',
                            ])
                            ->disabled(),
                        Forms\Components\TextInput::make('approver.name')
                            ->label('Pelulus')
                            ->disabled(),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('reference_number')
                    ->label('No. Rujukan')
                    ->searchable()
                    ->sortable(),
                    
                Tables\Columns\TextColumn::make('guest_name')
                    ->label('Pemohon')
                    ->searchable(),
                    
                Tables\Columns\TextColumn::make('division.name_ms')
                    ->label('Bahagian')
                    ->sortable(),
                    
                Tables\Columns\TextColumn::make('loan_start_date')
                    ->label('Tarikh Pinjaman')
                    ->date('d/m/Y')
                    ->sortable(),
                    
                Tables\Columns\BadgeColumn::make('status')
                    ->label('Status')
                    ->colors([
                        'warning' => 'pending_approval',
                        'success' => 'approved',
                        'danger' => 'rejected',
                        'info' => 'issued',
                        'primary' => 'active',
                    ]),
                    
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Tarikh

---

continue

---


# Implementation Plan: ICT Asset Loan Application Form (Continued)

---

## 🎛️ Phase 4: BPM Staff Workflows (Days 8-10) - Continued

### 4.1 Filament Resource Table (Continued)

```php
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Tarikh Permohonan')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        'pending_approval' => 'Menunggu Kelulusan',
                        'approved' => 'Diluluskan',
                        'rejected' => 'Ditolak',
                        'issued' => 'Dikeluarkan',
                        'active' => 'Aktif',
                        'returned' => 'Dipulangkan',
                    ]),
                    
                Tables\Filters\SelectFilter::make('division_id')
                    ->label('Bahagian')
                    ->relationship('division', 'name_ms'),
                    
                Tables\Filters\Filter::make('loan_date')
                    ->form([
                        Forms\Components\DatePicker::make('from')->label('Dari'),
                        Forms\Components\DatePicker::make('until')->label('Hingga'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when($data['from'], fn($q, $date) => $q->whereDate('loan_start_date', '>=', $date))
                            ->when($data['until'], fn($q, $date) => $q->whereDate('loan_start_date', '<=', $date));
                    }),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\Action::make('issue')
                    ->label('Keluarkan Aset')
                    ->icon('heroicon-o-arrow-right-circle')
                    ->color('success')
                    ->visible(fn (LoanApplication $record) => $record->status === LoanStatus::APPROVED)
                    ->url(fn (LoanApplication $record) => route('filament.admin.pages.issue-loan-assets', ['record' => $record->id])),
                    
                Tables\Actions\Action::make('record_return')
                    ->label('Rekod Pemulangan')
                    ->icon('heroicon-o-arrow-left-circle')
                    ->color('warning')
                    ->visible(fn (LoanApplication $record) => $record->status === LoanStatus::ACTIVE)
                    ->url(fn (LoanApplication $record) => route('filament.admin.pages.record-asset-return', ['record' => $record->id])),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListLoanApplications::route('/'),
            'view' => Pages\ViewLoanApplication::route('/{record}'),
        ];
    }
}
```

### 4.2 Custom Filament Page: Issue Loan Assets (Bahagian 6)

```bash
php artisan make:filament-page IssueLoanAssets --no-interaction
```

**IssueLoanAssets.php:**

```php
<?php

namespace App\Filament\Pages;

use App\Models\LoanApplication;
use App\Models\Asset;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Pages\Page;
use Filament\Notifications\Notification;

class IssueLoanAssets extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-arrow-right-circle';
    protected static string $view = 'filament.pages.issue-loan-assets';
    protected static bool $shouldRegisterNavigation = false;
    
    public ?LoanApplication $record = null;
    public ?array $data = [];
    
    public function mount(): void
    {
        $this->record = LoanApplication::findOrFail(request('record'));
        $this->form->fill();
    }
    
    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Maklumat Permohonan')
                    ->schema([
                        Forms\Components\Placeholder::make('applicant')
                            ->label('Pemohon')
                            ->content($this->record->guest_name),
                        Forms\Components\Placeholder::make('division')
                            ->label('Bahagian')
                            ->content($this->record->division->name_ms),
                    ])
                    ->columns(2),
                    
                Forms\Components\Section::make('Tugasan Aset')
                    ->schema([
                        Forms\Components\Repeater::make('asset_assignments')
                            ->schema([
                                Forms\Components\Select::make('loan_item_id')
                                    ->label('Item Permohonan')
                                    ->options($this->record->items->pluck('equipment_type', 'id'))
                                    ->required(),
                                    
                                Forms\Components\Select::make('asset_id')
                                    ->label('Aset')
                                    ->options(Asset::where('status', 'available')->pluck('name', 'id'))
                                    ->searchable()
                                    ->required(),
                                    
                                Forms\Components\TextInput::make('brand_model')
                                    ->label('Jenama & Model'),
                                    
                                Forms\Components\TextInput::make('serial_number')
                                    ->label('No. Siri / Tag ID'),
                                    
                                Forms\Components\CheckboxList::make('accessories')
                                    ->label('Aksesori')
                                    ->options([
                                        'power_adapter' => 'Power Adapter',
                                        'bag' => 'Beg',
                                        'mouse' => 'Mouse',
                                        'usb_cable' => 'Kabel USB',
                                        'hdmi_vga_cable' => 'Kabel HDMI/VGA',
                                        'remote' => 'Remote',
                                    ])
                                    ->columns(3),
                                    
                                Forms\Components\TextInput::make('other_accessories')
                                    ->label('Lain-lain Aksesori'),
                            ])
                            ->columns(2)
                            ->defaultItems(1),
                    ]),
                    
                Forms\Components\Section::make('Pengesahan Pengeluaran (Bahagian 6)')
                    ->schema([
                        Forms\Components\DateTimePicker::make('issuance_date')
                            ->label('Tarikh Pengeluaran')
                            ->default(now())
                            ->required(),
                            
                        Forms\Components\Select::make('issuing_officer_id')
                            ->label('Pegawai Pengeluar')
                            ->options(fn() => \App\Models\User::pluck('name', 'id'))
                            ->searchable()
                            ->required(),
                            
                        Forms\Components\TextInput::make('issuing_officer_signature')
                            ->label('Tandatangan Pegawai Pengeluar')
                            ->required(),
                            
                        Forms\Components\Select::make('receiving_officer_id')
                            ->label('Pegawai Penerima')
                            ->options(fn() => \App\Models\User::pluck('name', 'id'))
                            ->searchable()
                            ->required(),
                            
                        Forms\Components\TextInput::make('receiving_officer_signature')
                            ->label('Tandatangan Pegawai Penerima')
                            ->required(),
                    ])
                    ->columns(2),
            ])
            ->statePath('data');
    }
    
    public function issue(): void
    {
        $data = $this->form->getState();
        
        // Create transaction record
        $transaction = $this->record->transactions()->create([
            'transaction_type' => 'issuance',
            'issuing_officer_id' => $data['issuing_officer_id'],
            'receiving_officer_id' => $data['receiving_officer_id'],
            'transaction_date' => $data['issuance_date'],
            'issuing_officer_signature' => $data['issuing_officer_signature'],
            'receiving_officer_signature' => $data['receiving_officer_signature'],
        ]);
        
        // Assign assets to loan items
        foreach ($data['asset_assignments'] as $assignment) {
            $loanItem = $this->record->items()->find($assignment['loan_item_id']);
            $loanItem->update([
                'asset_id' => $assignment['asset_id'],
                'brand_model' => $assignment['brand_model'],
                'serial_number' => $assignment['serial_number'],
                'accessories' => $assignment['accessories'],
                'other_accessories' => $assignment['other_accessories'],
            ]);
            
            // Update asset status
            Asset::find($assignment['asset_id'])->update(['status' => 'loaned']);
        }
        
        // Update application status
        $this->record->update(['status' => LoanStatus::ACTIVE]);
        
        // Send notification
        Notification::make()
            ->title('Aset berjaya dikeluarkan')
            ->success()
            ->send();
            
        $this->redirect(route('filament.admin.resources.loan-applications.index'));
    }
}
```

### 4.3 Custom Filament Page: Record Asset Return (Bahagian 7)

```bash
php artisan make:filament-page RecordAssetReturn --no-interaction
```

**RecordAssetReturn.php:**

```php
<?php

namespace App\Filament\Pages;

use App\Models\LoanApplication;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Pages\Page;
use Filament\Notifications\Notification;

class RecordAssetReturn extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-arrow-left-circle';
    protected static string $view = 'filament.pages.record-asset-return';
    protected static bool $shouldRegisterNavigation = false;
    
    public ?LoanApplication $record = null;
    public ?array $data = [];
    
    public function mount(): void
    {
        $this->record = LoanApplication::findOrFail(request('record'));
        $this->form->fill();
    }
    
    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Maklumat Pinjaman')
                    ->schema([
                        Forms\Components\Placeholder::make('applicant')
                            ->label('Peminjam')
                            ->content($this->record->guest_name),
                        Forms\Components\Placeholder::make('loan_period')
                            ->label('Tempoh Pinjaman')
                            ->content($this->record->loan_start_date->format('d/m/Y') . ' - ' . $this->record->expected_return_date->format('d/m/Y')),
                    ])
                    ->columns(2),
                    
                Forms\Components\Section::make('Aset Dipulangkan')
                    ->schema([
                        Forms\Components\Repeater::make('returned_items')
                            ->schema([
                                Forms\Components\Placeholder::make('equipment')
                                    ->label('Peralatan')
                                    ->content(fn($state) => $this->record->items->find($state['loan_item_id'])?->equipment_type ?? ''),
                                    
                                Forms\Components\Select::make('condition')
                                    ->label('Keadaan')
                                    ->options([
                                        'good' => 'Baik',
                                        'damaged' => 'Rosak',
                                        'missing' => 'Hilang',
                                    ])
                                    ->required(),
                                    
                                Forms\Components\Textarea::make('notes')
                                    ->label('Catatan'),
                            ])
                            ->default(fn() => $this->record->items->map(fn($item) => [
                                'loan_item_id' => $item->id,
                                'condition' => 'good',
                                'notes' => '',
                            ])->toArray())
                            ->columns(2),
                    ]),
                    
                Forms\Components\Section::make('Pengesahan Pemulangan (Bahagian 7)')
                    ->schema([
                        Forms\Components\DateTimePicker::make('return_date')
                            ->label('Tarikh Pemulangan')
                            ->default(now())
                            ->required(),
                            
                        Forms\Components\Select::make('returning_officer_id')
                            ->label('Pegawai Yang Memulangkan')
                            ->options(fn() => \App\Models\User::pluck('name', 'id'))
                            ->searchable()
                            ->required(),
                            
                        Forms\Components\TextInput::make('returning_officer_signature')
                            ->label('Tandatangan')
                            ->required(),
                            
                        Forms\Components\Select::make('receiving_officer_id')
                            ->label('Pegawai Terima Pulangan')
                            ->options(fn() => \App\Models\User::pluck('name', 'id'))
                            ->searchable()
                            ->required(),
                            
                        Forms\Components\TextInput::make('receiving_officer_signature')
                            ->label('Tandatangan')
                            ->required(),
                            
                        Forms\Components\Textarea::make('general_notes')
                            ->label('Catatan Umum')
                            ->columnSpanFull(),
                    ])
                    ->columns(2),
            ])
            ->statePath('data');
    }
    
    public function recordReturn(): void
    {
        $data = $this->form->getState();
        
        // Create return transaction
        $transaction = $this->record->transactions()->create([
            'transaction_type' => 'return',
            'issuing_officer_id' => $data['returning_officer_id'],
            'receiving_officer_id' => $data['receiving_officer_id'],
            'transaction_date' => $data['return_date'],
            'issuing_officer_signature' => $data['returning_officer_signature'],
            'receiving_officer_signature' => $data['receiving_officer_signature'],
            'notes' => $data['general_notes'],
        ]);
        
        // Update asset statuses
        foreach ($this->record->items as $item) {
            if ($item->asset_id) {
                $item->asset->update(['status' => 'available']);
            }
        }
        
        // Update application status
        $this->record->update(['status' => LoanStatus::COMPLETED]);
        
        Notification::make()
            ->title('Pemulangan aset berjaya direkodkan')
            ->success()
            ->send();
            
        $this->redirect(route('filament.admin.resources.loan-applications.index'));
    }
}
```

---

## 🌐 Phase 5: Translations (Days 11-12)

### 5.1 Malay Translations

**resources/lang/ms/loan.php:**

```php
<?php

return [
    'form_title' => 'Borang Permohonan Peminjaman Peralatan ICT Untuk Kegunaan Rasmi',
    'form_code' => 'PK.(S).MOTAC.07.(L3)',
    
    // Steps
    'step_1_title' => 'Maklumat Pemohon',
    'step_2_title' => 'Pegawai Bertanggungjawab',
    'step_3_title' => 'Peralatan',
    'step_4_title' => 'Syarat-Syarat',
    'step_5_title' => 'Pengesahan',
    'step_6_title' => 'Pelulus',
    'step_7_title' => 'Selesai',
    
    // Bahagian 1
    'section_1_title' => 'BAHAGIAN 1 | MAKLUMAT PEMOHON',
    'section_1_note' => 'Tanda * WAJIB diisi.',
    'applicant_name' => 'Nama Penuh',
    'position_grade' => 'Jawatan & Gred',
    'phone_number' => 'No. Telefon',
    'division_unit' => 'Bahagian/Unit',
    'application_purpose' => 'Tujuan Permohonan',
    'location' => 'Lokasi',
    'loan_start_date' => 'Tarikh Pinjaman',
    'expected_return_date' => 'Tarikh Dijangka Pulang',
    
    // Bahagian 2
    'section_2_title' => 'BAHAGIAN 2 | MAKLUMAT PEGAWAI BERTANGGUNGJAWAB',
    'section_2_note' => 'Tanda * WAJIB diisi.',
    'responsible_officer_checkbox' => 'Sila tandakan √ jika Pemohon adalah Pegawai Bertanggungjawab. Bahagian ini hanya perlu diisi jika Pegawai Bertanggungjawab bukan Pemohon.',
    
    // Bahagian 3
    'section_3_title' => 'BAHAGIAN 3 | MAKLUMAT PERALATAN',
    'equipment_type' => 'Jenis Peralatan',
    'quantity' => 'Kuantiti',
    'notes' => 'Catatan',
    'add_equipment' => 'Tambah Peralatan',
    'remove_equipment' => 'Buang',
    
    // Bahagian 4
    'section_4_title' => 'BAHAGIAN 4 | PENGESAHAN PEMOHON (PEGAWAI BERTANGGUNGJAWAB)',
    'declaration_text' => 'Saya dengan ini mengesahkan dan memperakukan bahawa semua peralatan yang dipinjam adalah untuk kegunaan rasmi dan berada di bawah tanggungjawab dan penyeliaan saya sepanjang tempoh tersebut;',
    'digital_signature' => 'Tandatangan & Cop (jika ada)',
    'signature_name' => 'Nama',
    'date' => 'Tarikh',
    
    // Bahagian 5
    'section_5_title' => 'BAHAGIAN 5 | PENGESAHAN BAHAGIAN / UNIT / SEKSYEN',
    'approval_requirement' => 'Permohonan yang lengkap diisi oleh pemohon hendaklah DISOKONG OLEH PEGAWAI SEKURANG-KURANGNYA GRED 41 DAN KE ATAS.',
    'select_approver' => 'Pilih Pegawai Pelulus',
    'search_approver' => 'Cari pegawai (Gred 41 ke atas)',
    'approval_status' => 'Status Kelulusan',
    'approved' => 'DISOKONG',
    'rejected' => 'TIDAK DISOKONG',
    
    // Terms & Conditions
    'terms_title' => 'SYARAT-SYARAT PERMOHONAN PEMINJAMAN PERALATAN ICT UNTUK KEGUNAAN RASMI KEMENTERIAN PELANCONGAN, SENI DAN BUDAYA',
    'terms_reminder' => 'Peringatan:',
    'term_1' => 'Sila isi borang ini dengan lengkap. Tanda * adalah WAJIB diisi.',
    'term_2' => 'Permohonan adalah tertakluk kepada ketersediaan peralatan melalui konsep 'First Come, First Serve'. Permohonan akan ditolak dan diuruskan dalam tempoh tiga (3) hari bekerja dari tarikh permohonan lengkap diterima. BPM tidak bertanggungjawab di atas ketersediaan peralatan jika pemohon gagal mematuhi tempoh ini.',
    'term_3' => 'Pemohon hendaklah mengemukakan Borang Permohonan Pinjaman Peralatan ICT yang lengkap diisi dan ditandatangani kepada BPM semasa mengambil peralatan.',
    'term_4' => 'Pemohon diingatkan untuk menyemak dan memeriksa kesempurnaan peralatan semasa mengambil dan sebelum memulangkan peralatan yang dipinjam. Kehilangan dan kekurangan pada peralatan semasa pemulangan adalah dibawah tanggungjawab pemohon dan tindakan melalui peraturan-peraturan yang berkuatkuasa boleh diambil.',
    'term_5' => 'Pemohon merujuk kepada kakitangan yang melengkapkan borang permohonan peminjaman peralatan ICT.',
    'term_6' => 'Pegawai Bertanggungjawab merujuk kepada kakitangan yang bertanggungjawab ke atas penggunaan, keselamatan dan kerosakan perlatan pinjaman.',
    'term_7' => 'Pegawai Pengeluar merujuk kepada kakitangan BPM yang mengeluarkan peralatan untuk diberikan kepada Pegawai Penerima.',
    'term_8' => 'Pegawai Penerima merujuk kepada kakitangan yang menerima peralatan daripada Pegawai Pengeluar.',
    'term_9' => 'Pegawai Yang Memulangkan merujuk kepada kakitangan yang memulangkan peralatan yang dipinjam.',
    'term_10' => 'Pegawai Terima Pulangan merujuk kepada kakitangan BPM yang menerima peralatan yang dipulangkan oleh Pegawai Yang Memulangkan.',
    'term_11' => 'Borang yang telah lengkap diisi hendaklah dihantar kepada: Bahagian Pengurusan Maklumat, KEMENTERIAN PELANCONGAN, SENI DAN BUDAYA. Sebarang pertanyaan sila hubungi: Unit Operasi Rangkaian dan Khidmat Pengguna, Bahagian Pengurusan Maklumat',
    'term_11_digital' => 'Borang yang telah lengkap diisi akan diproses secara automatik melalui sistem ICTServe. Sebarang pertanyaan sila hubungi: Unit Operasi Rangkaian dan Khidmat Pengguna, Bahagian Pengurusan Maklumat',
    'terms_acknowledgment' => 'Saya telah membaca dan bersetuju dengan semua syarat-syarat di atas.',
    
    // Buttons
    'next' => 'Seterusnya',
    'previous' => 'Kembali',
    'submit' => 'Hantar Permohonan',
    'save_draft' => 'Simpan Draf',
    
    // Status
    'status' => [
        'draft' => 'Draf',
        'pending_approval' => 'Menunggu Kelulusan',
        'approved' => 'Diluluskan',
        'rejected' => 'Ditolak',
        'issued' => 'Dikeluarkan',
        'active' => 'Aktif',
        'returned' => 'Dipulangkan',
        'completed' => 'Selesai',
    ],
    
    // Email
    'email' => [
        'approval_request_subject' => 'Permohonan Kelulusan Peminjaman Aset ICT - :ref',
        'approval_request_title' => 'Permohonan Kelulusan Peminjaman Aset ICT',
        'approval_request_intro' => 'Anda telah dipilih sebagai pelulus untuk permohonan peminjaman aset ICT daripada :name.',
        'approval_note' => 'Sila klik butang di atas untuk meluluskan atau menolak permohonan ini. Pautan ini sah selama 7 hari.',
    ],
];
```

### 5.2 English Translations

**resources/lang/en/loan.php:**

```php
<?php

return [
    'form_title' => 'ICT Equipment Loan Application Form For Official Use',
    'form_code' => 'PK.(S).MOTAC.07.(L3)',
    
    // Steps
    'step_1_title' => 'Applicant Information',
    'step_2_title' => 'Responsible Officer',
    'step_3_title' => 'Equipment',
    'step_4_title' => 'Terms & Conditions',
    'step_5_title' => 'Declaration',
    'step_6_title' => 'Approver',
    'step_7_title' => 'Complete',
    
    // Bahagian 1
    'section_1_title' => 'SECTION 1 | APPLICANT INFORMATION',
    'section_1_note' => 'Fields marked with * are REQUIRED.',
    'applicant_name' => 'Full Name',
    'position_grade' => 'Position & Grade',
    'phone_number' => 'Phone Number',
    'division_unit' => 'Division/Unit',
    'application_purpose' => 'Purpose of Application',
    'location' => 'Location',
    'loan_start_date' => 'Loan Start Date',
    'expected_return_date' => 'Expected Return Date',
    
    // ... (mirror MS structure with EN translations)
];
```

---

## 🧪 Phase 6: Testing (Days 13-14)

### 6.1 Feature Tests

**tests/Feature/Loan/GuestLoanApplicationTest.php:**

```php
<?php

namespace Tests\Feature\Loan;

use App\Models\{Division, User, LoanApplication};
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Volt\Volt;
use Tests\TestCase;

class GuestLoanApplicationTest extends TestCase
{
    use RefreshDatabase;

    public function test_step_1_validates_required_fields(): void
    {
        Volt::test('guest-loan-application-form')
            ->set('applicant_name', '')
            ->call('nextStep')
            ->assertHasErrors(['applicant_name' => 'required']);
    }

    public function test_step_2_conditional_validation(): void
    {
        Volt::test('guest-loan-application-form')
            ->set('is_responsible_officer', false)
            ->set('responsible_officer_name', '')
            ->call('nextStep')
            ->assertHasErrors(['responsible_officer_name' => 'required']);
    }

    public function test_can_add_equipment_rows(): void
    {
        Volt::test('guest-loan-application-form')
            ->call('addEquipmentRow')
            ->assertCount('equipment_items', 2);
    }

    public function test_can_submit_complete_application(): void
    {
        $division = Division::factory()->create();
        $approver = User::factory()->create(['grade' => 41]);

        Volt::test('guest-loan-application-form')
            ->set('applicant_name', 'Test User')
            ->set('applicant_position', 'Pegawai Tadbir')
            ->set('applicant_grade', 'N41')
            ->set('applicant_phone', '0123456789')
            ->set('division_id', $division->id)
            ->set('purpose', 'Official work')
            ->set('location', 'Office')
            ->set('loan_start_date', now()->addDay())
            ->set('expected_return_date', now()->addWeek())
            ->set('equipment_items', [
                ['equipment_type' => 'Laptop', 'quantity' => 1, 'notes' => '']
            ])
            ->set('terms_acknowledged', true)
            ->set('applicant_signature', 'Test User')
            ->set('approver_id', $approver->id)
            ->call('submitApplication')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('loan_applications', [
            'guest_name' => 'Test User',
            'status' => 'pending_approval',
        ]);
    }
}
```

### 6.2 E2E Tests (Playwright)

**tests/e2e/loan-application.spec.ts:**

```typescript
import { test, expect } from '@playwright/test';

test.describe('Loan Application Form', () => {
  test('complete loan application workflow', async ({ page }) => {
    await page.goto('/loan');
    
    // Step 1: Applicant Info
    await page.fill('[name="applicant_name"]', 'Ahmad bin Ali');
    await page.fill('[name="applicant_position"]', 'Pegawai Tadbir');
    await page.fill('[name="applicant_grade"]', 'N41');
    await page.fill('[name="applicant_phone"]', '0123456789');
    await page.selectOption('[name="division_id"]', { index: 1 });
    await page.fill('[name="purpose"]', 'Kerja rasmi di luar pejabat');
    await page.fill('[name="location"]', 'Pejabat Negeri Selangor');
    await page.fill('[name="loan_start_date"]', '2025-12-01');
    await page.fill('[name="expected_return_date"]', '2025-12-07');
    await page.click('button:has-text("Seterusnya")');
    
    // Step 2: Responsible Officer (skip if applicant is responsible)
    await page.check('[name="is_responsible_officer"]');
    await page.click('button:has-text("Seterusnya")');
    
    // Step 3: Equipment
    await page.fill('[name="equipment_items.0.equipment_type"]', 'Laptop');
    await page.fill('[name="equipment_items.0.quantity"]', '1');
    await page.click('button:has-text("Seterusnya")');
    
    // Step 4: Terms
    await page.check('[name="terms_acknowledged"]');
    await page.click('button:has-text("Seterusnya")');
    
    // Step 5: Declaration
    await page.fill('[name="applicant_signature"]', 'Ahmad bin Ali');
    await page.click('button:has-text("Seterusnya")');
    
    // Step 6: Approver
    await page.fill('[name="approver_search"]', 'Dato');
    await page.click('.approver-result:first-child');
    await page.click('button:has-text("Hantar Permohonan")');
    
    // Confirmation
    await expect(page.locator('text=Permohonan Berjaya')).toBeVisible();
  });
});
```

---

## 📊 Implementation Summary

### Files to Create (~45 files)

**Database (8 files):**

1. Migration: `extend_loan_applications_table`
2. Migration: `create_loan_items_table`
3. Migration:

---

continue

---

# Implementation Plan: ICT Asset Loan Application (Continued)

---

## 📊 Implementation Summary (Continued)

### Files to Create (~45 files)

**Database (8 files):**

1. `database/migrations/YYYY_MM_DD_extend_loan_applications_table.php`
2. `database/migrations/YYYY_MM_DD_create_loan_items_table.php`
3. `database/migrations/YYYY_MM_DD_create_loan_transactions_table.php`
4. `database/migrations/YYYY_MM_DD_create_loan_approval_tokens_table.php`
5. `app/Enums/LoanStatus.php`
6. `app/Enums/ApprovalStatus.php`
7. `app/Enums/TransactionType.php`
8. `database/factories/LoanItemFactory.php`

**Models (3 files):**

1. `app/Models/LoanItem.php`
2. `app/Models/LoanTransaction.php`
3. `app/Models/LoanApprovalToken.php`

**Livewire Components (8 files):**

1. `app/Livewire/GuestLoanApplicationForm.php`
2. `resources/views/livewire/guest-loan-application-form.blade.php`
3. `resources/views/livewire/loan-steps/step-1-applicant-info.blade.php`
4. `resources/views/livewire/loan-steps/step-2-responsible-officer.blade.php`
5. `resources/views/livewire/loan-steps/step-3-equipment.blade.php`
6. `resources/views/livewire/loan-steps/step-4-terms.blade.php`
7. `resources/views/livewire/loan-steps/step-5-declaration.blade.php`
8. `resources/views/livewire/loan-steps/step-6-approver.blade.php`

**Services (2 files):**

1. `app/Services/DualApprovalService.php`
2. `app/Services/LoanReminderService.php`

**Controllers (1 file):**

1. `app/Http/Controllers/LoanApprovalController.php`

**Mail Classes (7 files):**

1. `app/Mail/LoanApplicationSubmitted.php`
2. `app/Mail/LoanApprovalRequest.php`
3. `app/Mail/LoanApplicationApproved.php`
4. `app/Mail/LoanApplicationRejected.php`
5. `app/Mail/AssetReadyForCollection.php`
6. `app/Mail/AssetReturnReminder.php`
7. `app/Mail/AssetReturnConfirmation.php`

**Email Templates (7 files):**

1. `resources/views/emails/loan/application-submitted.blade.php`
2. `resources/views/emails/loan/approval-request.blade.php`
3. `resources/views/emails/loan/application-approved.blade.php`
4. `resources/views/emails/loan/application-rejected.blade.php`
5. `resources/views/emails/loan/asset-ready.blade.php`
6. `resources/views/emails/loan/return-reminder.blade.php`
7. `resources/views/emails/loan/return-confirmation.blade.php`

**Filament Resources (5 files):**

1. `app/Filament/Resources/LoanApplicationResource.php`
2. `app/Filament/Resources/LoanApplicationResource/Pages/ListLoanApplications.php`
3. `app/Filament/Resources/LoanApplicationResource/Pages/ViewLoanApplication.php`
4. `app/Filament/Pages/IssueLoanAssets.php`
5. `app/Filament/Pages/RecordAssetReturn.php`

**Translations (2 files):**

1. `resources/lang/ms/loan.php`
2. `resources/lang/en/loan.php`

**Tests (4 files):**

1. `tests/Feature/Loan/GuestLoanApplicationTest.php`
2. `tests/Feature/Loan/LoanApprovalWorkflowTest.php`
3. `tests/Feature/Loan/BPMStaffWorkflowTest.php`
4. `tests/e2e/loan-application.spec.ts`

---

### Files to Modify (~15 files)

1. `app/Models/LoanApplication.php` - Extend with new fields
2. `app/Models/User.php` - Add Grade 41+ scope
3. `app/Models/Asset.php` - Add loan relationship
4. `routes/web.php` - Add approval routes
5. `routes/console.php` - Add reminder scheduler
6. `database/seeders/DatabaseSeeder.php` - Include loan seeders
7. `config/mail.php` - Email configuration
8. `app/Providers/AppServiceProvider.php` - Register services
9. `resources/views/layouts/app.blade.php` - Add loan navigation
10. `resources/views/welcome.blade.php` - Add loan application link
11. `app/Filament/Resources/AssetResource.php` - Add loan status
12. `tailwind.config.js` - Add custom colors if needed
13. `package.json` - E2E test scripts
14. `phpunit.xml` - Test configuration
15. `README.md` - Update documentation

---

## 📅 Detailed Timeline

### Week 1: Foundation (Days 1-5)

**Day 1: Database Schema**

- ✅ Create migrations (4 files)
- ✅ Create enums (3 files)
- ✅ Update models (3 files)
- ✅ Run migrations and test

**Day 2: Multi-Step Form Structure**

- ✅ Create main Livewire component
- ✅ Create step 1-3 views
- ✅ Implement step navigation
- ✅ Add validation logic

**Day 3: Multi-Step Form Completion**

- ✅ Create step 4-6 views
- ✅ Implement equipment table
- ✅ Add approver search
- ✅ Test form flow

**Day 4: Approval Workflow**

- ✅ Create DualApprovalService
- ✅ Create LoanApprovalController
- ✅ Create approval token system
- ✅ Test approval routes

**Day 5: Email Notifications**

- ✅ Create 7 mailable classes
- ✅ Create email templates
- ✅ Test email sending
- ✅ Queue configuration

### Week 2: BPM Workflows (Days 6-10)

**Day 6: Filament Resource**

- ✅ Create LoanApplicationResource
- ✅ Configure table and filters
- ✅ Add custom actions
- ✅ Test admin panel

**Day 7: Asset Issuance (Bahagian 6)**

- ✅ Create IssueLoanAssets page
- ✅ Implement asset assignment
- ✅ Add digital signatures
- ✅ Test issuance workflow

**Day 8: Asset Return (Bahagian 7)**

- ✅ Create RecordAssetReturn page
- ✅ Implement return logic
- ✅ Add condition tracking
- ✅ Test return workflow

**Day 9: Translations**

- ✅ Add MS translations (200+ keys)
- ✅ Add EN translations (200+ keys)
- ✅ Test language switching
- ✅ Verify all UI text

**Day 10: UI/UX Polish**

- ✅ Apply MOTAC branding
- ✅ Responsive design
- ✅ Accessibility compliance
- ✅ Loading states

### Week 3: Testing & Deployment (Days 11-15)

**Day 11: Feature Tests**

- ✅ Write form validation tests
- ✅ Write approval workflow tests
- ✅ Write BPM workflow tests
- ✅ Achieve 80%+ coverage

**Day 12: E2E Tests**

- ✅ Write Playwright tests
- ✅ Test complete workflows
- ✅ Test error scenarios
- ✅ Cross-browser testing

**Day 13: Accessibility Tests**

- ✅ Run Axe accessibility tests
- ✅ Fix WCAG violations
- ✅ Test keyboard navigation
- ✅ Test screen readers

**Day 14: Integration & UAT**

- ✅ Integrate with helpdesk module
- ✅ Update navigation
- ✅ User acceptance testing
- ✅ Bug fixes

**Day 15: Deployment**

- ✅ Deploy to staging
- ✅ Final testing
- ✅ Production deployment
- ✅ Documentation

---

## 🔄 Scheduled Jobs & Automation

### Reminder Scheduler

**routes/console.php:**

```php
use App\Services\LoanReminderService;
use Illuminate\Support\Facades\Schedule;

Schedule::call(function () {
    app(LoanReminderService::class)->sendReturnReminders();
})->daily();

Schedule::call(function () {
    app(LoanReminderService::class)->sendOverdueNotifications();
})->daily();
```

**LoanReminderService.php:**

```php
<?php

namespace App\Services;

use App\Models\LoanApplication;
use App\Mail\AssetReturnReminder;
use Illuminate\Support\Facades\Mail;

class LoanReminderService
{
    public function sendReturnReminders(): void
    {
        // 3 days before due
        $this->sendReminder(3);
        
        // 1 day before due
        $this->sendReminder(1);
        
        // On due date
        $this->sendReminder(0);
    }
    
    private function sendReminder(int $daysBeforeDue): void
    {
        $applications = LoanApplication::where('status', LoanStatus::ACTIVE)
            ->whereDate('expected_return_date', now()->addDays($daysBeforeDue))
            ->get();
            
        foreach ($applications as $application) {
            Mail::to($application->guest_email)
                ->send(new AssetReturnReminder($application, $daysBeforeDue));
        }
    }
    
    public function sendOverdueNotifications(): void
    {
        $overdueApplications = LoanApplication::where('status', LoanStatus::ACTIVE)
            ->whereDate('expected_return_date', '<', now())
            ->get();
            
        foreach ($overdueApplications as $application) {
            $daysOverdue = now()->diffInDays($application->expected_return_date);
            
            Mail::to($application->guest_email)
                ->send(new AssetReturnReminder($application, -$daysOverdue));
        }
    }
}
```

---

## 🎯 Critical Success Factors

### Must-Have Features

1. **✅ 7-Step Wizard** - Smooth navigation, validation per step
2. **✅ Conditional Bahagian 2** - Show/hide based on checkbox
3. **✅ Dynamic Equipment Table** - Add/remove rows
4. **✅ Grade 41+ Approver Search** - Database query with autocomplete
5. **✅ Email-Based Approval** - Secure token system (7-day expiry)
6. **✅ BPM Asset Issuance** - Assign specific assets from inventory
7. **✅ BPM Asset Return** - Record condition and signatures
8. **✅ Automated Reminders** - 3 days, 1 day, due date, overdue
9. **✅ Bilingual Support** - Complete MS/EN translations
10. **✅ WCAG 2.2 AA** - Full accessibility compliance

### Performance Targets

- **Form Load Time**: < 2 seconds
- **Step Transition**: < 500ms
- **Email Delivery**: < 30 seconds
- **Approval Processing**: < 5 seconds
- **Database Queries**: < 100ms per page

### Security Requirements

- **Token Expiry**: 7 days
- **Single-Use Tokens**: Cannot reuse approval links
- **CSRF Protection**: All forms protected
- **Input Sanitization**: All user inputs sanitized
- **Authorization**: Grade 41+ verification for approvals
- **Audit Trail**: All actions logged

---

## 📝 Routes Summary

**Public Routes:**

```php
Route::get('/loan', GuestLoanApplicationForm::class)->name('loan.create');
Route::get('/loan/confirmation/{application}', [LoanController::class, 'confirmation'])->name('loan.confirmation');
```

**Approval Routes (Token-Based):**

```php
Route::get('/loan/approve/{token}', [LoanApprovalController::class, 'approve'])->name('loan.approve');
Route::post('/loan/approve/{token}', [LoanApprovalController::class, 'submitApproval'])->name('loan.approve.submit');
Route::get('/loan/reject/{token}', [LoanApprovalController::class, 'reject'])->name('loan.reject');
Route::post('/loan/reject/{token}', [LoanApprovalController::class, 'submitRejection'])->name('loan.reject.submit');
```

**Filament Routes (Auto-registered):**

```php
/admin/loan-applications
/admin/loan-applications/{id}
/admin/pages/issue-loan-assets?record={id}
/admin/pages/record-asset-return?record={id}
```

---

## 🚀 Quick Start Commands

```bash
# Phase 1: Database Setup
php artisan make:migration extend_loan_applications_table
php artisan make:model LoanItem -mf
php artisan make:model LoanTransaction -mf
php artisan make:model LoanApprovalToken -m
php artisan make:enum LoanStatus
php artisan make:enum ApprovalStatus
php artisan migrate

# Phase 2: Components
php artisan make:livewire GuestLoanApplicationForm

# Phase 3: Services & Controllers
php artisan make:service DualApprovalService
php artisan make:controller LoanApprovalController

# Phase 4: Emails
php artisan make:mail LoanApprovalRequest --markdown=emails.loan.approval-request

# Phase 5: Filament
php artisan make:filament-resource LoanApplication
php artisan make:filament-page IssueLoanAssets
php artisan make:filament-page RecordAssetReturn

# Phase 6: Tests
php artisan make:test Loan/GuestLoanApplicationTest
npm run test:e2e
```

---

## ✅ Final Checklist

### Pre-Deployment

- [ ] All migrations run successfully
- [ ] All seeders populate data correctly
- [ ] All 7 form steps validate properly
- [ ] Conditional Bahagian 2 works correctly
- [ ] Equipment table add/remove functions
- [ ] Approver search returns Grade 41+ only
- [ ] Email approval links work (approve/reject)
- [ ] Token expiry enforced (7 days)
- [ ] BPM issuance workflow complete
- [ ] BPM return workflow complete
- [ ] All 7 email notifications send correctly
- [ ] Automated reminders scheduled
- [ ] MS/EN translations complete
- [ ] WCAG 2.2 AA compliance verified
- [ ] All tests pass (Feature + E2E)
- [ ] Performance targets met
- [ ] Security audit passed
- [ ] Documentation updated

### Post-Deployment

- [ ] Monitor email delivery rates
- [ ] Track approval response times
- [ ] Monitor overdue loans
- [ ] Collect user feedback
- [ ] Address bug reports
- [ ] Optimize slow queries
- [ ] Update documentation as needed

---

## 📞 Support & Maintenance

### Monitoring

- **Email Queue**: Monitor failed jobs
- **Approval Tokens**: Clean expired tokens monthly
- **Overdue Loans**: Weekly report to BPM
- **System Errors**: Real-time alerts

### Maintenance Tasks

- **Weekly**: Review overdue loans
- **Monthly**: Clean expired tokens
- **Quarterly**: Review and update terms
- **Annually**: Audit trail archival

---

**Implementation Ready**: All phases planned, files identified, timeline established. Ready to begin Phase 1 (Database Schema) immediately upon approval.

**Estimated Completion**: 15 working days (3 weeks)  
**Team Size**: 1-2 developers  
**Dependencies**: Division seeder, User grades, Asset inventory system
