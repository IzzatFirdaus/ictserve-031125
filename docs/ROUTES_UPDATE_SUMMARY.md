# Routes Update Summary - Livewire Integration

**Date**: 2025-11-04  
**Author**: Pasukan BPM MOTAC  
**Version**: 2.0.0

## Overview

Updated the routing structure to integrate Livewire-based components for both the loan application and helpdesk forms, following the ICTServe hybrid architecture pattern.

## Changes Made

### 1. Guest Helpdesk Routes (No Authentication Required)

**Prefix**: `/helpdesk`  
**Name Prefix**: `helpdesk.`

```php
Route::get('/create', App\Livewire\Helpdesk\SubmitTicket::class)->name('create');
Route::get('/submit', App\Livewire\Helpdesk\SubmitTicket::class)->name('submit');
```

**Available URLs**:

-   `GET /helpdesk/create` → `helpdesk.create`
-   `GET /helpdesk/submit` → `helpdesk.submit`

**Component**: `App\Livewire\Helpdesk\SubmitTicket`

---

### 2. Guest Loan Application Routes (No Authentication Required)

**Prefix**: `/loan`  
**Name Prefix**: `loan.guest.`

```php
Route::get('/apply', App\Livewire\GuestLoanApplication::class)->name('apply');
Route::get('/create', App\Livewire\GuestLoanApplication::class)->name('create');
```

**Available URLs**:

-   `GET /loan/apply` → `loan.guest.apply`
-   `GET /loan/create` → `loan.guest.create`

**Component**: `App\Livewire\GuestLoanApplication`

**Features**:

-   4-page multi-step form
-   WCAG 2.2 AA compliant
-   Bilingual support (MS/EN)
-   Real-time validation
-   Dynamic equipment selection

---

### 3. Authenticated Loan Routes (Login Required)

**Prefix**: `/loans`  
**Name Prefix**: `loan.authenticated.`  
**Middleware**: `auth`, `verified`

```php
Route::get('/create', App\Livewire\GuestLoanApplication::class)->name('create');
```

**Available URLs**:

-   `GET /loans/create` → `loan.authenticated.create`

**Note**: Authenticated users can use the same loan application form as guests, but with their user data pre-filled.

---

### 4. Authenticated Helpdesk Routes (Login Required)

**Prefix**: `/helpdesk`  
**Name Prefix**: `helpdesk.authenticated.`  
**Middleware**: `auth`, `verified`

```php
Route::get('/create', App\Livewire\Helpdesk\SubmitTicket::class)->name('create');
```

**Available URLs**:

-   `GET /helpdesk/create` → `helpdesk.authenticated.create`

---

### 5. Email Approval Routes (No Authentication Required)

**Prefix**: `/loan/approval`  
**Name Prefix**: `loan.approval.`

**Maintained existing routes**:

-   `GET /loan/approval/{token}` → `loan.approval.show`
-   `POST /loan/approval/{token}/approve` → `loan.approval.approve`
-   `POST /loan/approval/{token}/decline` → `loan.approval.decline`
-   `GET /loan/approval/success` → `loan.approval.success`

**Purpose**: Email-based approval workflow for Grade 41+ officers

---

## Routes Removed

The following old controller-based routes were removed:

```php
// OLD - Removed
Route::get('/apply', [App\Http\Controllers\GuestLoanApplicationController::class, 'create']);
Route::post('/apply', [App\Http\Controllers\GuestLoanApplicationController::class, 'store']);
Route::post('/check-availability', [App\Http\Controllers\GuestLoanApplicationController::class, 'checkAvailability']);
```

**Reason**: Replaced with Livewire components for better interactivity and real-time validation.

---

## TODO: Routes for Future Implementation

The following routes are commented out and need Livewire components to be created:

### Guest Routes

```php
// TODO: Create TrackTicket Livewire component
// Route::get('/track/{ticketNumber}', App\Livewire\Helpdesk\TrackTicket::class)->name('track');

// TODO: Create GuestLoanTracking Livewire component
// Route::get('/tracking/{applicationNumber}', App\Livewire\GuestLoanTracking::class)->name('tracking');
```

### Authenticated Loan Routes

```php
// TODO: Create AuthenticatedLoanDashboard Livewire component
// Route::get('/', App\Livewire\AuthenticatedLoanDashboard::class)->name('index');
// Route::get('/dashboard', App\Livewire\AuthenticatedLoanDashboard::class)->name('dashboard');
// Route::get('/history', App\Livewire\LoanHistory::class)->name('history');

// TODO: Create LoanDetails and LoanExtension Livewire components
// Route::get('/{id}', App\Livewire\LoanDetails::class)->name('show');
// Route::get('/{id}/extend', App\Livewire\LoanExtension::class)->name('extend');
```

### Authenticated Helpdesk Routes

```php
// TODO: Create Helpdesk Dashboard and related Livewire components
// Route::get('/', App\Livewire\Helpdesk\Dashboard::class)->name('index');
// Route::get('/dashboard', App\Livewire\Helpdesk\Dashboard::class)->name('dashboard');
// Route::get('/tickets', App\Livewire\Helpdesk\MyTickets::class)->name('tickets');
// Route::get('/tickets/{id}', App\Livewire\Helpdesk\TicketDetails::class)->name('ticket.show');
```

---

## Route Naming Convention

Following Laravel and ICTServe best practices:

-   **Guest routes**: `{module}.{action}` (e.g., `helpdesk.create`, `loan.guest.apply`)
-   **Authenticated routes**: `{module}.authenticated.{action}` (e.g., `loan.authenticated.create`)
-   **Approval routes**: `loan.approval.{action}` (e.g., `loan.approval.show`)

---

## Testing Routes

### Test Guest Loan Application

```bash
# Visit the loan application form
http://localhost/loan/apply
http://localhost/loan/create
```

### Test Guest Helpdesk

```bash
# Visit the helpdesk form
http://localhost/helpdesk/create
http://localhost/helpdesk/submit
```

### Test Authenticated Routes (Login Required)

```bash
# Visit authenticated loan form
http://localhost/loans/create

# Visit authenticated helpdesk form
http://localhost/helpdesk/create
```

---

## Integration with Livewire Components

### GuestLoanApplication Component

**Location**: `app/Livewire/GuestLoanApplication.php`  
**View**: `resources/views/livewire/guest-loan-application.blade.php`

**Features**:

-   Multi-step form (4 pages)
-   Step validation
-   Dynamic equipment rows
-   Real-time validation with debouncing
-   WCAG 2.2 AA compliant
-   Bilingual support

### SubmitTicket Component

**Location**: `app/Livewire/Helpdesk/SubmitTicket.php`  
**View**: `resources/views/livewire/helpdesk/submit-ticket.blade.php`

**Features**:

-   Guest-accessible helpdesk form
-   Real-time validation
-   File upload support
-   WCAG 2.2 AA compliant

---

## Next Steps

1. **Create Missing Components**:

    - `App\Livewire\Helpdesk\TrackTicket`
    - `App\Livewire\GuestLoanTracking`
    - `App\Livewire\AuthenticatedLoanDashboard`
    - `App\Livewire\LoanHistory`
    - `App\Livewire\LoanDetails`
    - `App\Livewire\LoanExtension`
    - `App\Livewire\Helpdesk\Dashboard`
    - `App\Livewire\Helpdesk\MyTickets`
    - `App\Livewire\Helpdesk\TicketDetails`

2. **Uncomment Routes**: Once components are created, uncomment the corresponding routes

3. **Testing**: Write feature tests for all new Livewire routes

4. **Documentation**: Update API documentation with new route structure

---

## Compliance & Standards

-   **WCAG 2.2 AA**: All routes serve accessible components
-   **Laravel 12**: Following latest routing conventions
-   **Livewire 3**: Using modern Livewire patterns
-   **ICTServe Architecture**: Hybrid guest + authenticated access model
-   **PDPA 2010**: Data protection compliance maintained

---

## References

-   **Spec**: `.kiro/specs/updated-loan-module/`
-   **Design**: `.kiro/specs/updated-loan-module/design.md`
-   **Requirements**: `.kiro/specs/updated-loan-module/requirements.md`
-   **Tasks**: `.kiro/specs/updated-loan-module/tasks.md`

---

**Status**: ✅ Routes Updated Successfully  
**Next Task**: Create missing Livewire components for TODO routes
