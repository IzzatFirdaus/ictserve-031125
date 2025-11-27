# Loan Application Service Documentation

## Overview

The Loan Application Service handles the creation and management of ICT asset loan applications. It supports the hybrid architecture (guest and authenticated submissions) and integrates with approval workflows and notification systems.

## Architecture

### Service Location

```
app/Services/LoanApplicationService.php
```

### Dependencies

```php
public function __construct(
    private DualApprovalService $approvalService,
    private NotificationService $notificationService
) {}
```

### Traceability

- **D03-FR-001.1**: Hybrid application creation
- **D03-FR-001.2**: Guest and authenticated submissions
- **D03-FR-011.4**: Extension requests
- **D03-FR-023.2**: Approval/rejection actions
- **D04 §2.1**: Business logic services

## Features

### 1. Hybrid Application Creation

Create loan applications for both guest and authenticated users.

```php
use App\Services\LoanApplicationService;

$service = app(LoanApplicationService::class);

$application = $service->createHybridApplication([
    // BAHAGIAN 1: Applicant Info
    'applicant_name' => 'Ahmad bin Abdullah',
    'applicant_email' => 'ahmad@motac.gov.my',
    'applicant_phone' => '0123456789',
    'staff_id' => 'MOTAC001',
    'grade' => '41',
    'division_id' => 1,
    'applicant_position' => 'Pegawai Tadbir',
    'applicant_grade' => 'N41',

    // Loan Details
    'purpose' => 'Official meeting at Putrajaya',
    'location' => 'Putrajaya Convention Centre',
    'loan_start_date' => '2025-12-01',
    'expected_return_date' => '2025-12-07',

    // BAHAGIAN 2: Responsible Officer (optional)
    'is_responsible_officer' => true,
    'responsible_officer_name' => null,
    'responsible_officer_position' => null,
    'responsible_officer_grade' => null,
    'responsible_officer_phone' => null,

    // BAHAGIAN 3: Equipment
    'items' => [
        ['equipment_type' => 1, 'quantity' => 2],
        ['equipment_type' => 3, 'quantity' => 1],
    ],

    // BAHAGIAN 4: Declaration
    'applicant_digital_signature' => 'Ahmad bin Abdullah',
    'terms_acknowledged' => true,

    // Priority
    'priority' => 'normal',
    'special_instructions' => null,
], $user); // Pass null for guest submissions
```

### 2. Status Management

Update application status with notifications.

```php
use App\Enums\LoanStatus;

$service->updateStatus(
    $application,
    LoanStatus::APPROVED,
    'Approved by Department Head'
);
```

### 3. Approval Processing

Process portal-based approvals.

```php
// Approve
$service->approveApplication(
    $application,
    $approver,
    'Equipment available, approved for collection',
    'portal'
);

// Reject
$service->rejectApplication(
    $application,
    $approver,
    'Insufficient justification provided',
    'portal'
);
```

### 4. Extension Requests

Handle loan extension requests.

```php
$service->requestExtension(
    $application,
    '2025-12-14', // New end date
    'Meeting extended by one week'
);
```

### 5. Guest Application Claiming

Link guest submissions to authenticated accounts.

```php
$service->claimGuestApplication($application, $user);
```

## Database Operations

### Application Creation Flow

```php
DB::beginTransaction();

try {
    // 1. Create loan application
    $application = LoanApplication::create([...]);

    // 2. Create loan items
    $this->createLoanItems($application, $data['items']);

    // 3. Calculate total value
    $this->calculateTotalValue($application);

    // 4. Send confirmation email
    $this->notificationService->sendLoanApplicationConfirmation($application);

    // 5. Route to approver
    $this->approvalService->sendApprovalRequest($application);

    DB::commit();
} catch (\Exception $e) {
    DB::rollBack();
    throw $e;
}
```

### Loan Items Creation

```php
private function createLoanItems(LoanApplication $application, array $items): void
{
    foreach ($items as $item) {
        $assetId = is_array($item) ? ($item['asset_id'] ?? null) : $item;
        $quantity = is_array($item) ? ($item['quantity'] ?? 1) : 1;
        $equipmentType = is_array($item) ? ($item['equipment_type'] ?? null) : null;

        // For guest applications, find available asset by category
        if ($equipmentType && !$assetId) {
            $asset = Asset::where('category_id', $equipmentType)
                ->where('status', 'available')
                ->first();

            if (!$asset) {
                throw new \Exception("No available asset for category: {$equipmentType}");
            }
        } else {
            $asset = Asset::findOrFail($assetId);
        }

        LoanItem::create([
            'loan_application_id' => $application->id,
            'asset_id' => $asset->id,
            'equipment_type' => $equipmentType ?? $asset->category_id,
            'quantity' => $quantity,
            'unit_value' => $asset->current_value,
            'total_value' => $asset->current_value * $quantity,
        ]);
    }
}
```

## Application Number Generation

```php
// Format: LA[YEAR][6-digit sequence]
// Example: LA2025000001

$applicationNumber = LoanApplication::generateApplicationNumber();
```

## Status Workflow

### Available Statuses

```php
enum LoanStatus: string
{
    case SUBMITTED = 'submitted';
    case PENDING_APPROVAL = 'pending_approval';
    case APPROVED = 'approved';
    case REJECTED = 'rejected';
    case IN_USE = 'in_use';
    case RETURNED = 'returned';
    case OVERDUE = 'overdue';
    case CANCELLED = 'cancelled';
}
```

### Status Transitions

```
SUBMITTED → PENDING_APPROVAL → APPROVED → IN_USE → RETURNED
                            ↘ REJECTED

IN_USE → OVERDUE (automatic)
SUBMITTED/PENDING_APPROVAL → CANCELLED
```

## Notification Integration

### Confirmation Email

Sent immediately after application creation:

- Application number
- Equipment list
- Loan dates
- Tracking link

### Status Update Notifications

Sent on status changes:

- Approval/rejection with remarks
- Collection instructions
- Return reminders

### Approval Request

Sent to Grade 41+ approver:

- Application details
- Approve/reject links with secure tokens
- 7-day token expiration

## Error Handling

### Transaction Rollback

```php
try {
    DB::beginTransaction();
    // ... operations
    DB::commit();
} catch (\Exception $e) {
    DB::rollBack();
    Log::error('Failed to create loan application', [
        'error' => $e->getMessage(),
        'data' => $data,
    ]);
    throw $e;
}
```

### Claiming Validation

```php
public function claimGuestApplication(LoanApplication $application, User $user): bool
{
    if (!$application->isGuestSubmission()) {
        throw new Exception('Application already linked to an account.');
    }

    if (strtolower($application->applicant_email) !== strtolower($user->email)) {
        throw new Exception('Email does not match the original applicant.');
    }

    // ... proceed with claiming
}
```

## Logging

### Application Creation

```php
Log::info('Loan application created', [
    'application_number' => $application->application_number,
    'user_id' => $user?->id,
    'is_guest' => $application->isGuestSubmission(),
]);
```

### Status Updates

```php
Log::info('Loan application status updated', [
    'application_number' => $application->application_number,
    'status' => $status->value,
]);
```

### Extension Requests

```php
Log::info('Loan extension requested', [
    'application_number' => $application->application_number,
    'new_end_date' => $newEndDate,
]);
```

### Claiming

```php
Log::info('Loan application claimed by user', [
    'application_number' => $application->application_number,
    'user_id' => $user->id,
]);
```

## Testing

### Unit Tests

```php
public function test_creates_hybrid_application_for_guest()
{
    $service = app(LoanApplicationService::class);

    $application = $service->createHybridApplication([
        'applicant_name' => 'Test User',
        'applicant_email' => 'test@example.com',
        // ... other required fields
    ], null); // null = guest

    $this->assertNull($application->user_id);
    $this->assertTrue($application->isGuestSubmission());
}

public function test_creates_hybrid_application_for_authenticated_user()
{
    $user = User::factory()->create();
    $service = app(LoanApplicationService::class);

    $application = $service->createHybridApplication([
        // ... required fields
    ], $user);

    $this->assertEquals($user->id, $application->user_id);
    $this->assertFalse($application->isGuestSubmission());
}

public function test_claim_validates_email_match()
{
    $application = LoanApplication::factory()->create([
        'user_id' => null,
        'applicant_email' => 'original@example.com',
    ]);

    $user = User::factory()->create(['email' => 'different@example.com']);
    $service = app(LoanApplicationService::class);

    $this->expectException(Exception::class);
    $service->claimGuestApplication($application, $user);
}
```

## API Reference

### Methods

| Method                    | Parameters                                  | Returns         | Description            |
| ------------------------- | ------------------------------------------- | --------------- | ---------------------- |
| `createHybridApplication` | array $data, ?User $user                    | LoanApplication | Create new application |
| `updateStatus`            | LoanApplication, LoanStatus, ?string $notes | void            | Update status          |
| `approveApplication`      | LoanApplication, User, ?string, string      | void            | Approve application    |
| `rejectApplication`       | LoanApplication, User, ?string, string      | void            | Reject application     |
| `requestExtension`        | LoanApplication, string, string             | void            | Request extension      |
| `claimGuestApplication`   | LoanApplication, User                       | bool            | Claim guest submission |

### Data Array Structure

```php
[
    // Required
    'applicant_name' => string,
    'applicant_email' => string,
    'applicant_phone' => string,
    'staff_id' => string,
    'grade' => string,
    'division_id' => int,
    'purpose' => string,
    'location' => string,
    'loan_start_date' => string (Y-m-d),
    'expected_return_date' => string (Y-m-d),
    'items' => array,
    'terms_acknowledged' => bool,

    // Optional
    'applicant_position' => ?string,
    'applicant_grade' => ?string,
    'is_responsible_officer' => bool,
    'responsible_officer_name' => ?string,
    'responsible_officer_position' => ?string,
    'responsible_officer_grade' => ?string,
    'responsible_officer_phone' => ?string,
    'applicant_digital_signature' => ?string,
    'priority' => string (normal|urgent),
    'special_instructions' => ?string,
]
```

---

**Document Version**: 1.0  
**Last Updated**: 2025-11-27  
**Author**: ICTServe Development Team  
**Status**: Production Ready
