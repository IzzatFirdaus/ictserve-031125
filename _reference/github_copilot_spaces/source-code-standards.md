# Source Code Standards & Structure (v3.5.0)

> **Context:** Strict coding standards and directory structure for the ICTServe Laravel 12 application. Use this to determine file placement and syntax style.

## 1. Directory Structure Map
**Follow this exact structure when creating new files:**

* **Livewire Components:**
  * `app/Livewire/Guest/`: Public forms (Helpdesk, Loan).
  * `app/Livewire/Staff/`: Authenticated staff dashboard.
  * `app/Livewire/Status/`: Guest status tracking pages.
  * `app/Livewire/Approver/`: Email approval workflow.
* **Filament Admin:** `app/Filament/Resources/`
* **Services:** `app/Services/` (Business Logic)
* **Models:** `app/Models/`

## 2. PHP Coding Standards (Laravel 12)
**Strict Adherence Required:**

* **Strict Typing:** All files must start with `declare(strict_types=1);`.
* **Constructor Promotion:** Use PHP 8.2 constructor property promotion.

    ```php
    public function __construct(
        public readonly EmailNotificationService $emailService,
    ) {}
    ```

* **Return Types:** Explicitly declare return types for all methods.
* **Model Casts:** Use the new `protected function casts(): array` method syntax, NOT the `$casts` property.

## 3. Core Class Implementations

### `HelpdeskTicket` Model

* **Traits:** `use HasAuditTrail, HasFactory, SoftDeletes;`
* **Key Methods:**
  * `isGuestSubmission(): bool`
  * `getSubmitterEmail(): string` (Handles logic for Guest vs Auth user)

### `User` Model (v3.5.0 Enhanced)

* **Traits:** `use HasRoles, HasFactory, Notifiable, SoftDeletes;`
* **Key Methods:**
  * `linkGoogle(string $id)`: For SSO.
  * `linkGuestSubmissions(array $ids)`: For account linking logic.

### Service Layer Contracts

* **`GoogleSsoServiceInterface`**: Handles OAuth 2.0 flow.
* **`ApiTokenServiceInterface`**: Manages Sanctum tokens.
* **`AccessoryTrackingServiceInterface`**: Logic for `loan_transaction_accessories`.

## 4. Testing Strategy

* **Unit Tests:** `tests/Unit/` (Target: 80% coverage).
* **Feature Tests:** `tests/Feature/` (Must test Guest vs Auth flows).
* **E2E Tests:** `tests/e2e/` (Playwright).

## 5. Technology Stack Versions

* **PHP:** 8.2.12
* **Laravel:** 12.40.1
* **Filament:** 4.1.10
* **Livewire:** 3.7.0
* **Tailwind:** 4.1.17
