# ICTServe Update v3 - Design Document

## Overview

ICTServe Update v3 consolidates the system to version 3.5.0, implementing the **True Hybrid Architecture** where MOTAC staff can choose between authenticated access (self-registration, login, personalized dashboard) OR guest access (quick form submission without login). The system uses token-based workflows for status checking and approval processes, with a Filament 4.1.10 admin panel for BPM staff management.

**Reference Documents:**

- D00_SYSTEM_OVERVIEW.md - System vision and True Hybrid Architecture (v3.5.0)
- D04_SOFTWARE_DESIGN_DOCUMENT.md - Primary architecture reference (v3.5.0)
- D09_DATABASE_DOCUMENTATION.md - Database schema and dual audit
- D10_SOURCE_CODE_DOCUMENTATION.md - Code organization
- D11_TECHNICAL_DESIGN_DOCUMENTATION.md - Infrastructure
- D16_BROADCASTING_SETUP.md - WebSocket configuration
- D17_QUEUE_MANAGEMENT_HORIZON.md - Queue management

**Key Architectural Decisions (per D00 §4.1, D04 §3):**

- **True Hybrid Architecture**: Staff can self-register OR use guest forms
- Self-registration with `@motac.gov.my` email domain validation
- Flexible login (full email OR short username)
- Optional account linking for historical guest submissions
- Nullable `user_id` FK in tickets/loans for hybrid data association
- Token-based status checking and approval workflows (SHA-512 hashed)
- Real-time notifications via Laravel Reverb WebSocket server
- Dual audit system (owen-it + spatie) for compliance and operations
- Laravel Telescope for superuser debugging (unrestricted)
- Filament 4.1.10 SDUI admin panel with RBAC (staff/admin/superuser roles)
- WCAG 2.2 AA accessibility compliance throughout
- Bilingual support (Bahasa Melayu primary, English secondary)

**Technology Stack (per D00 §4.1, D03 §4):**

- Laravel 12.40.1 (PHP 8.2.12)
- Laravel Breeze 2.3.8 (authentication scaffolding)
- Livewire 3.7.0 + Volt 1.10.1 (reactive forms)
- Alpine.js 3 (lightweight interactivity)
- Tailwind CSS 4.1.17 (utility-first styling)
- Filament 4.1.10 (admin panel)
- Laravel Reverb 1.6.2 (WebSocket server)
- Laravel Echo 2.2.6 (WebSocket client)
- owen-it/laravel-auditing 14.x (compliance audit)
- spatie/laravel-activitylog 4.x (activity logging)
- Laravel Telescope 5.x (debugging - superuser only)
- MySQL 8.0 (utf8mb4), Redis (queue/cache)

## Architecture

```text
┌─────────────────────────────────────────────────────────────────────────┐
│                      PRESENTATION LAYER (True Hybrid v3.5.0)             │
├─────────────────────────────────────────────────────────────────────────┤
│  Hybrid Portal             │  Staff Dashboard     │  Admin Panel        │
│  - Livewire 3.7.0 Forms    │  - My Dashboard      │  - Filament 4.1.10  │
│  - Volt 1.10.1 Components  │  - Submission History│  - RBAC (3 roles)   │
│  - Alpine.js 3             │  - Profile Settings  │  - Dashboard Widgets│
│  - Auth::check() logic     │  - Notifications     │  - 2FA for superuser│
│  - Auto-fill if logged in  │  - Account Linking   │  - Telescope access │
│  - guest.blade.php layout  │  - app.blade.php     │  - Dual Audit View  │
├─────────────────────────────────────────────────────────────────────────┤
│  Authentication (Laravel Breeze 2.3.8)                                   │
│  - Self-registration (@motac.gov.my only)                                │
│  - Email verification required                                           │
│  - Flexible login (email OR username)                                    │
│  - Password reset flow                                                   │
└─────────────────────────────────────────────────────────────────────────┘
                                    │
┌─────────────────────────────────────────────────────────────────────────┐
│                      APPLICATION LAYER                                   │
├─────────────────────────────────────────────────────────────────────────┤
│  Services                  │  Events              │  Jobs               │
│  - HelpdeskService         │  - TicketCreated     │  - SendEmail        │
│  - LoanService             │  - LoanApproved      │  - Broadcast        │
│  - TokenService            │  - SLABreach         │  - Reminder         │
│  - NotificationService     │  - AssetOverdue      │  - Cleanup          │
│  - ApprovalService         │  - StatusChanged     │  - ScanFile         │
│  - AccountLinkingService   │  - EmailVerified     │  - SendVerification │
│  - RegistrationService     │  - AccountLinked     │  - ProcessDigest    │
└─────────────────────────────────────────────────────────────────────────┘
                                    │
┌─────────────────────────────────────────────────────────────────────────┐
│                        DOMAIN LAYER                                      │
├─────────────────────────────────────────────────────────────────────────┤
│  Models (per D09)          │  Policies            │  Observers          │
│  - User (staff/admin/su)   │  - TicketPolicy      │  - Audit (owen-it)  │
│  - HelpdeskTicket          │  - LoanPolicy        │  - Activity (spatie)│
│  - LoanApplication         │  - AssetPolicy       │  - Status           │
│  - LoanApproval            │  - UserPolicy        │  - Token            │
│  - Asset                   │  - AuditPolicy       │  - SLA              │
│  - LoanTransaction         │                      │                     │
└─────────────────────────────────────────────────────────────────────────┘
                                    │
┌─────────────────────────────────────────────────────────────────────────┐
│                    INFRASTRUCTURE LAYER                                  │
├─────────────────────────────────────────────────────────────────────────┤
│  Database (MySQL 8.0)      │  Queue (Redis)       │  External           │
│  - Nullable user_id FK     │  - Laravel Horizon   │  - GOV SMTP         │
│  - Dual audit tables       │  - Job processing    │  - BPM SMS          │
│  - audits (owen-it)        │  - Broadcasting      │  - ClamAV           │
│  - activity_log (spatie)   │  - Session storage   │  - reCAPTCHA        │
│  - Token hashes indexed    │  - Notification prefs│  - Telescope        │
└─────────────────────────────────────────────────────────────────────────┘
```

## Components and Interfaces

### Guest Portal Components (per D13)

#### HelpdeskTicketForm (Livewire Component)

- Location: `app/Livewire/Helpdesk/TicketForm.php`
- View: `resources/views/livewire/helpdesk/ticket-form.blade.php`
- Purpose: Guest helpdesk ticket submission with real-time validation
- Inputs: name, email, phone, division, grade, category, description, attachments, PDPA acknowledgement
- Outputs: Ticket creation with status token, confirmation email
- Validation: Per `docs/helpdesk_form_to_model.md`

#### LoanApplicationWizard (Volt Component)

- Location: `resources/views/livewire/loan/application-wizard.blade.php`
- Purpose: Multi-step loan application with asset availability checking
- Steps: Applicant Info → Asset Selection → Date Range → Purpose → Acknowledgement
- Outputs: Application creation with approval token generation
- Validation: Per `docs/loan_form_to_model.md`

#### StatusChecker (Livewire Component)

- Location: `app/Livewire/Status/StatusChecker.php`
- Purpose: Token-based status lookup for tickets and loans
- Inputs: Status token (32 chars)
- Outputs: Status details, timeline, public comments

#### ApprovalPage (Volt Component)

- Location: `resources/views/livewire/loan/approval-page.blade.php`
- Purpose: Guest-accessible approval decision page
- Inputs: Signed approval token
- Outputs: Decision recording with remarks

### Admin Panel Components (Filament 4.1.10 per D03 SRS-ADM)

#### HelpdeskTicketResource

- Location: `app/Filament/Resources/HelpdeskTicketResource.php`
- Features: CRUD, filtering, status management, assignment, SLA indicators
- Actions: Assign, Update Status, Add Comment, Close

#### LoanApplicationResource

- Location: `app/Filament/Resources/LoanApplicationResource.php`
- Features: CRUD, approval chain, check-out/check-in, damage reporting
- Actions: Process Approval, Check-out, Check-in, Report Damage

#### DashboardWidgets (per D03 SRS-ADM-003)

- HelpdeskStatsWidget: Open/In-Progress/Resolved counts, SLA compliance
- LoanStatsWidget: Pending/Active/Overdue counts
- RecentActivityWidget: Real-time activity feed via Laravel Reverb

### Service Layer (per D10 §4)

```php
interface HelpdeskServiceInterface
{
    public function createTicket(array $data): HelpdeskTicket;
    public function updateStatus(HelpdeskTicket $ticket, string $status, string $comment): void;
    public function assignTicket(HelpdeskTicket $ticket, User $admin): void;
    public function getByStatusToken(string $token): ?HelpdeskTicket;
    public function calculateSLADueDate(string $category): Carbon;
    public function checkSLABreach(HelpdeskTicket $ticket): bool;
}
```

#### LoanService

```php
interface LoanServiceInterface
{
    public function createApplication(array $data): LoanApplication;
    public function checkAssetAvailability(array $assetIds, Carbon $start, Carbon $end): array;
    public function processApproval(LoanApplication $app, string $decision, ?string $remarks): void;
    public function checkOut(LoanApplication $app, User $admin, array $conditionNotes): void;
    public function checkIn(LoanApplication $app, User $admin, array $returnData): void;
    public function createMaintenanceTicket(LoanApplication $app, Asset $asset): HelpdeskTicket;
}
```

#### TokenService

```php
interface TokenServiceInterface
{
    public function generateStatusToken(Model $model): string;
    public function generateApprovalToken(LoanApplication $app, int $expiryHours = 72): array;
    public function validateStatusToken(string $token, string $type): ?Model;
    public function validateApprovalToken(LoanApplication $app, string $token): bool;
    public function regenerateApprovalToken(LoanApplication $app): array;
}
```

#### ApprovalService (per D03 SRS-LOAN-004)

```php
interface ApprovalServiceInterface
{
    public function initiateApproval(LoanApplication $app): void;
    public function findApprover(LoanApplication $app): string; // Returns approver email
    public function recordDecision(LoanApplication $app, string $decision, ?string $remarks, string $ipHash): void;
    public function sendApprovalEmail(LoanApplication $app, string $approverEmail, string $signedUrl): void;
}
```

#### RegistrationService (per D03 SRS-AUTH-001)

```php
interface RegistrationServiceInterface
{
    public function register(array $data): User;
    public function validateEmailDomain(string $email): bool; // Must be @motac.gov.my
    public function sendVerificationEmail(User $user): void;
    public function verifyEmail(User $user, string $token): bool;
    public function extractUsernameFromEmail(string $email): string; // user@motac.gov.my → user
}
```

#### AccountLinkingService (per D02 FR-050)

```php
interface AccountLinkingServiceInterface
{
    public function findUnlinkedSubmissions(string $email): Collection;
    public function linkSubmissions(User $user, array $submissionIds): int;
    public function getLinkedSubmissionCount(User $user): int;
}
```

#### NotificationPreferenceService (per D16)

```php
interface NotificationPreferenceServiceInterface
{
    public function getPreferences(User $user): array;
    public function updatePreferences(User $user, array $preferences): void;
    public function shouldSendEmail(User $user, string $notificationType): bool;
    public function getDigestFrequency(User $user): string; // immediate, daily, weekly
}
```

## Data Models (per D09)

### HelpdeskTicket (per D09 §4.1)

```text
helpdesk_tickets
├── id (BIGINT, PK)
├── user_id (BIGINT, FK → users, NULLABLE) - Hybrid: linked if authenticated per D02 FR-050
├── ticket_number (VARCHAR(20), UNIQUE) - Format: HD-YYYYMM-XXXX
├── submitter_name (VARCHAR(255)) - Guest metadata (always stored)
├── submitter_email (VARCHAR(255)) - Encrypted at rest
├── submitter_phone (VARCHAR(50)) - Encrypted at rest
├── submitter_division_code (VARCHAR(20))
├── submitter_grade (VARCHAR(50), NULLABLE)
├── category (VARCHAR(100))
├── priority (ENUM: LOW, MEDIUM, HIGH, CRITICAL)
├── description (TEXT)
├── asset_tag (VARCHAR(100), NULLABLE)
├── declaration (BOOLEAN) - PDPA acknowledgement
├── status (ENUM: OPEN, IN_PROGRESS, AWAITING_INFO, RESOLVED, CLOSED)
├── assigned_admin_id (BIGINT, FK → users, NULLABLE)
├── sla_due_at (TIMESTAMP)
├── closed_at (TIMESTAMP, NULLABLE)
├── status_token_hash (VARCHAR(128), INDEXED)
├── created_at (TIMESTAMP)
└── updated_at (TIMESTAMP)
```

### LoanApplication (per D09 §4.2)

```text
loan_applications
├── id (BIGINT, PK)
├── user_id (BIGINT, FK → users, NULLABLE) - Hybrid: linked if authenticated per D02 FR-050
├── reference (VARCHAR(20), UNIQUE) - Format: LA-YYYYMM-XXXX
├── applicant_name (VARCHAR(255)) - Guest metadata (always stored)
├── applicant_email (VARCHAR(255)) - Encrypted at rest
├── applicant_phone (VARCHAR(50)) - Encrypted at rest
├── applicant_division_code (VARCHAR(20))
├── applicant_grade (VARCHAR(50))
├── purpose (TEXT)
├── location (VARCHAR(255))
├── loan_start_date (DATE)
├── loan_end_date (DATE)
├── acknowledgement (BOOLEAN) - PDPA acknowledgement
├── status (ENUM: PENDING_SUPERVISOR_APPROVAL, APPROVED, REJECTED,
│          AWAITING_COLLECTION, ON_LOAN, RETURNED, DAMAGED)
├── approval_token_hash (VARCHAR(128), INDEXED)
├── approval_token_expires_at (TIMESTAMP, NULLABLE)
├── status_token_hash (VARCHAR(128), INDEXED)
├── created_at (TIMESTAMP)
└── updated_at (TIMESTAMP)
```

### LoanApproval (per D09 §4.3)

```text
loan_approvals
├── id (BIGINT, PK)
├── loan_application_id (BIGINT, FK → loan_applications)
├── approver_email (VARCHAR(255)) - Grade 41+ officer
├── approver_grade (VARCHAR(50))
├── decision (ENUM: APPROVED, REJECTED)
├── remarks (TEXT, NULLABLE)
├── decision_at (TIMESTAMP)
├── decision_ip_hash (VARCHAR(128)) - Hashed for privacy
├── token_hash (VARCHAR(128), INDEXED)
└── created_at (TIMESTAMP)
```

### LoanTransaction (per D09 §4.4)

```text
loan_transactions
├── id (BIGINT, PK)
├── loan_application_id (BIGINT, FK → loan_applications)
├── asset_id (BIGINT, FK → assets)
├── transaction_type (ENUM: CHECK_OUT, CHECK_IN)
├── admin_id (BIGINT, FK → users)
├── condition_notes (TEXT, NULLABLE)
├── damage_reported (BOOLEAN, DEFAULT FALSE)
├── damage_photos (JSON, NULLABLE)
├── transaction_at (TIMESTAMP)
└── created_at (TIMESTAMP)
```

### User (per D09 §4.5)

```text
users
├── id (BIGINT, PK)
├── name (VARCHAR(255))
├── email (VARCHAR(255), UNIQUE) - Must be @motac.gov.my for staff
├── email_verified_at (TIMESTAMP, NULLABLE) - Required for full access
├── password (VARCHAR(255)) - Hashed
├── phone (VARCHAR(50), NULLABLE) - Encrypted at rest
├── division_code (VARCHAR(20), NULLABLE)
├── grade (VARCHAR(50), NULLABLE)
├── role (ENUM: staff, admin, superuser) - Default: staff
├── locale (VARCHAR(5), DEFAULT 'ms') - User language preference
├── notification_preferences (JSON, NULLABLE) - Email frequency, in-app toggle
├── two_factor_secret (VARCHAR(255), NULLABLE) - TOTP for superuser
├── remember_token (VARCHAR(100), NULLABLE)
├── created_at (TIMESTAMP)
└── updated_at (TIMESTAMP)
```

## Correctness Properties

_A property is a characteristic or behavior that should hold true across all valid executions of a system-essentially, a formal statement about what the system should do. Properties serve as the bridge between human-readable specifications and machine-verifiable correctness guarantees._

Based on the acceptance criteria analysis and D00-D17 documentation, the following correctness properties must be validated through property-based testing:

### Helpdesk Module Properties

**Property 1: Ticket Number Format Consistency**
_For any_ valid helpdesk ticket submission, the generated ticket number SHALL match the pattern `HD-YYYYMM-XXXX` where YYYY is the 4-digit year, MM is the 2-digit month, and XXXX is a sequential 4-digit number.
**Validates: Requirements 1.3**

**Property 2: Real-time Validation Response**
_For any_ invalid input entered into helpdesk form fields, the system SHALL display an inline error message within 500ms of the input change.
**Validates: Requirements 1.2**

**Property 3: Attachment Validation**
_For any_ file upload attempt, the system SHALL accept files only if: count ≤ 5, size ≤ 5MB each, and format is one of PDF, JPG, PNG, DOCX. All other uploads SHALL be rejected with appropriate error message.
**Validates: Requirements 1.4**

**Property 4: Status Token Generation**
_For any_ successfully created helpdesk ticket, the system SHALL generate a unique SHA-512 hashed status token that can be used to retrieve the ticket details.
**Validates: Requirements 1.5**

**Property 5: Status Token Lookup Round-Trip**
_For any_ valid status token, looking up the token SHALL return the correct ticket with matching details (status, timeline, public comments). This is a round-trip property: create ticket → get token → lookup by token → verify ticket matches.
**Validates: Requirements 2.1**

**Property 6: Invalid Token Rejection**
_For any_ invalid or expired status token, the system SHALL return an error response and NOT return any ticket details.
**Validates: Requirements 2.2**

**Property 7: Status Change Notification**
_For any_ ticket status change, the system SHALL queue an email notification to the submitter within 60 seconds.
**Validates: Requirements 2.3**

### Loan Module Properties

**Property 8: Loan Reference Format Consistency**
_For any_ valid loan application submission, the generated reference SHALL match the pattern `LA-YYYYMM-XXXX` and initial status SHALL be `PENDING_SUPERVISOR_APPROVAL`.
**Validates: Requirements 3.3**

**Property 9: Asset Availability Conflict Detection**
_For any_ asset and date range selection, the system SHALL correctly identify conflicts with existing reservations and return accurate availability status within 1 second.
**Validates: Requirements 3.2**

**Property 10: Asset Soft-Lock on Application**
_For any_ created loan application, the selected assets SHALL be soft-locked for the requested date range, preventing double-booking. Subsequent availability checks for overlapping dates SHALL show these assets as unavailable.
**Validates: Requirements 3.4**

**Property 11: Approval Token Generation and Validation**
_For any_ loan application requiring approval, the system SHALL generate a signed URL with JWT token (SHA-512 hashed) valid for exactly 72 hours. Validation before expiry SHALL succeed; validation after expiry SHALL fail.
**Validates: Requirements 4.1**

**Property 12: Approval Decision Recording**
_For any_ approval decision submission, the system SHALL record: decision (APPROVED/REJECTED), timestamp, IP hash, and optional remarks. All required fields SHALL be present in the `loan_approvals` table.
**Validates: Requirements 4.3**

**Property 13: Approval Status Update**
_For any_ recorded approval decision, the loan application status SHALL be updated accordingly (APPROVED → AWAITING_COLLECTION, REJECTED → REJECTED) and notifications SHALL be queued.
**Validates: Requirements 4.5**

### Admin Panel Properties

**Property 14: Ticket Filtering Accuracy**
_For any_ filter combination (status, priority, category, date range), the returned ticket list SHALL contain only tickets matching ALL specified criteria.
**Validates: Requirements 5.2**

**Property 15: Audit Trail on Status Update**
_For any_ ticket status update by an admin, an audit record SHALL be created containing: admin ID, previous status, new status, comment/reason, and timestamp.
**Validates: Requirements 5.3**

**Property 16: Real-time Assignment Notification**
_For any_ ticket assignment, a WebSocket notification SHALL be broadcast to the assigned admin via Laravel Reverb.
**Validates: Requirements 5.4**

**Property 17: SLA Warning Calculation**
_For any_ ticket with less than 25% of SLA time remaining, the system SHALL flag it for warning display and notify superuser.
**Validates: Requirements 5.5**

**Property 18: Check-out Transaction Recording**
_For any_ asset check-out operation, a transaction record SHALL be created in `loan_transactions` with: admin ID, timestamp, and condition notes.
**Validates: Requirements 6.1**

**Property 19: Check-in Status Update**
_For any_ asset check-in operation, the asset status SHALL be updated and the loan application status SHALL change to RETURNED (or DAMAGED if damage reported).
**Validates: Requirements 6.2**

**Property 20: Automatic Maintenance Ticket on Damage**
_For any_ asset returned with damage flag, the system SHALL automatically create a helpdesk maintenance ticket within 5 seconds.
**Validates: Requirements 6.3**

**Property 21: Loan Application Filtering Accuracy**
_For any_ filter combination (status, date, division), the returned loan application list SHALL contain only applications matching ALL specified criteria.
**Validates: Requirements 6.4**

### Audit and Configuration Properties

**Property 22: Audit Log Filtering Accuracy**
_For any_ audit log filter combination (date, user, action type, entity), the returned logs SHALL contain only entries matching ALL specified criteria.
**Validates: Requirements 7.2**

**Property 23: Audit Export Completeness**
_For any_ audit data export request, the generated file (CSV/PDF) SHALL contain all audit records matching the specified criteria with complete field data.
**Validates: Requirements 7.3**

### Notification Properties

**Property 24: High-Priority Ticket Broadcast**
_For any_ high-priority ticket creation, a WebSocket notification SHALL be broadcast to all online admins within 2 seconds.
**Validates: Requirements 8.1**

**Property 25: SLA Breach Notification**
_For any_ SLA breach occurrence, both email and WebSocket notifications SHALL be sent to superuser immediately.
**Validates: Requirements 8.2**

**Property 26: Overdue Asset Reminder Schedule**
_For any_ asset approaching or past due date, reminder notifications SHALL be scheduled at: 48 hours before, on due date, and daily after overdue.
**Validates: Requirements 8.3**

### Accessibility Properties

**Property 27: Color Contrast Compliance**
_For any_ text element, the color contrast ratio SHALL be at least 4.5:1. _For any_ UI component, the contrast ratio SHALL be at least 3:1.
**Validates: Requirements 9.1**

**Property 28: Keyboard Navigation**
_For any_ interactive element, keyboard navigation SHALL be functional with visible focus indicators (3px outline).
**Validates: Requirements 9.2**

**Property 29: ARIA Implementation**
_For any_ interactive component, appropriate ARIA labels, landmarks, and live regions SHALL be present for screen reader compatibility.
**Validates: Requirements 9.3**

**Property 30: Touch Target Sizing**
_For any_ touch target on mobile devices, the minimum size SHALL be 44x44 pixels.
**Validates: Requirements 9.4**

### Performance Properties

**Property 31: Queue Processing Time**
_For any_ queued notification, processing SHALL complete within 30 seconds of trigger.
**Validates: Requirements 10.4**

### Localization Properties

**Property 32: Bilingual Content Consistency**
_For any_ user-facing text, both Bahasa Melayu and English translations SHALL be available and consistent with GLOSSARY.md terminology.
**Validates: Requirements 11.1, 11.4**

### Security Properties

**Property 33: Token Hash Security**
_For any_ generated token (status or approval), only the SHA-512 hash SHALL be stored in the database, never the plaintext token.
**Validates: Requirements 14.4**

**Property 34: Rate Limiting Enforcement**
_For any_ IP address, requests to guest forms SHALL be limited to 60 per minute. Exceeding this limit SHALL result in HTTP 429 response.
**Validates: Requirements 14.1**

### Self-Registration Properties

**Property 35: Email Domain Validation**
_For any_ registration attempt, the system SHALL accept only email addresses with `@motac.gov.my` domain. All other domains SHALL be rejected with appropriate error message.
**Validates: Requirements 15.2**

**Property 36: Registration Account Creation**
_For any_ valid registration submission, the system SHALL create a user account with role `staff` and status `pending_verification`, and send verification email within 60 seconds.
**Validates: Requirements 15.3, 15.4**

**Property 37: Email Verification Round-Trip**
_For any_ verification link clicked within 24 hours, the system SHALL activate the account and redirect to login. Links older than 24 hours SHALL be rejected.
**Validates: Requirements 15.5**

### Authentication Properties

**Property 38: Flexible Login Acceptance**
_For any_ valid credentials, the system SHALL authenticate using either full email (`user@motac.gov.my`) OR short username (`user`). Both formats SHALL resolve to the same account.
**Validates: Requirements 16.2, 16.3**

**Property 39: Authentication Error Opacity**
_For any_ failed authentication attempt, the system SHALL display a generic error message that does NOT reveal whether the email/username exists in the system.
**Validates: Requirements 16.5**

### Dashboard Properties

**Property 40: Submission History Completeness**
_For any_ authenticated staff user, the dashboard SHALL display ALL helpdesk tickets and loan applications where `user_id` matches the authenticated user's ID.
**Validates: Requirements 17.2**

**Property 41: Profile Update Persistence**
_For any_ profile field update (phone, division, grade), the changes SHALL be persisted immediately and reflected in subsequent form auto-fills.
**Validates: Requirements 17.4**

### Account Linking Properties

**Property 42: Unlinked Submission Discovery**
_For any_ email address submitted for linking, the system SHALL return ALL helpdesk tickets and loan applications with matching `submitter_email` or `applicant_email` where `user_id` is NULL.
**Validates: Requirements 18.2**

**Property 43: Account Linking Atomicity**
_For any_ confirmed linking operation, the system SHALL update `user_id` on ALL selected submissions in a single transaction, ensuring either all succeed or none are modified.
**Validates: Requirements 18.4**

### Dual Audit Properties

**Property 44: Field-Level Audit Completeness**
_For any_ model create, update, or delete operation, the owen-it audit system SHALL record: user ID (or null for guest), IP hash, timestamp, and complete old/new field values.
**Validates: Requirements 19.3**

**Property 45: Activity Log Recording**
_For any_ significant user action (login, submission, approval, status change), the spatie activity log SHALL record: description, subject type/ID, causer type/ID, and relevant properties.
**Validates: Requirements 19.4**

### Telescope Access Properties

**Property 46: Telescope Role Restriction**
_For any_ request to `/telescope`, the system SHALL return HTTP 403 unless the authenticated user has role `superuser`. All other roles SHALL be denied access.
**Validates: Requirements 20.2, 20.3**

## Error Handling

### Guest Form Errors (per D12 §7)

- **Validation Errors**: Display inline with field, WCAG compliant error styling (red border, error icon, descriptive message in BM/EN)
- **File Upload Errors**: Clear message indicating size/type/count violation
- **Submission Errors**: Display error banner with retry option, log to error tracking
- **Rate Limiting**: Display "Too many requests" with countdown timer in both languages

### Token Errors (per D03 SRS-LOAN-005)

- **Invalid Token**: Display "Invalid or expired link" with support contact information in BM/EN
- **Expired Approval Token**: Display expiry message, allow superuser regeneration via Filament
- **Token Tampering**: Log security event, display generic error

### Admin Panel Errors (per D03 SRS-ADM)

- **Authorization Errors**: Redirect to dashboard with "Access denied" notification
- **Database Errors**: Display user-friendly message, log full error, notify superuser
- **WebSocket Disconnection**: Graceful fallback to polling, reconnection attempts

### System Errors (per D17)

- **Queue Failures**: Retry with exponential backoff (3 attempts), notify superuser on final failure
- **Email Delivery Failures**: Log failure, queue for retry, mark notification as failed after 3 attempts
- **SLA Calculation Errors**: Use default SLA, log error, notify admin

## Testing Strategy

### Unit Testing (PHPUnit 11.5.44 per D10 §6)

- **Service Layer Tests**: Test all service methods with mocked dependencies
- **Model Tests**: Test relationships, scopes, accessors, mutators, casts
- **Token Generation Tests**: Verify hash algorithms, expiry calculations
- **SLA Calculation Tests**: Verify correct due dates for all categories

### Property-Based Testing (PHPUnit + Faker)

- **Ticket Number Generation**: Generate random valid submissions, verify format
- **Token Round-Trip**: Create → get token → lookup → verify match
- **Filter Accuracy**: Generate random filter combinations, verify results
- **Availability Checking**: Generate random bookings, verify conflict detection

### Feature Testing (Laravel HTTP Tests per D10 §6)

- **Guest Form Submission**: Test complete submission flow
- **Status Checking**: Test token-based lookup
- **Approval Flow**: Test signed URL generation and decision recording
- **Admin CRUD**: Test all Filament resource operations

### Browser Testing (Playwright per D10 §6)

- **E2E Guest Flows**: Complete ticket and loan submission
- **E2E Admin Flows**: Login, dashboard, ticket management
- **Accessibility Testing**: Automated WCAG 2.2 AA checks
- **Cross-Browser**: Chrome, Firefox, Safari, Edge

### Performance Testing (per D03 §8.2)

- **Core Web Vitals**: Lighthouse CI for LCP, FID, CLS
- **Load Testing**: 100 concurrent users simulation
- **Queue Performance**: Notification processing time verification

### Testing Framework Configuration

```php
// Property-based testing with PHPUnit
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class TicketNumberFormatTest extends TestCase
{
    use RefreshDatabase;

    public function test_ticket_number_format_is_consistent(): void
    {
        for ($i = 0; $i < 100; $i++) {
            $ticket = app(HelpdeskService::class)->createTicket([
                'submitter_name' => fake()->name(),
                'submitter_email' => fake()->email(),
                'submitter_phone' => fake()->phoneNumber(),
                'division_code' => fake()->randomElement(['BPM', 'BTM', 'BKP']),
                'category' => fake()->randomElement(['Hardware', 'Software', 'Network']),
                'description' => fake()->paragraph(),
                'declaration' => true,
            ]);

            $this->assertMatchesRegularExpression(
                '/^HD-\d{6}-\d{4}$/',
                $ticket->ticket_number
            );
        }
    }
}
```

### Accessibility Testing Checklist (per D12-D14)

- [ ] Color contrast 4.5:1 for text, 3:1 for UI components
- [ ] Keyboard navigation functional for all interactive elements
- [ ] Focus indicators visible (3px outline)
- [ ] ARIA labels present on all form fields
- [ ] Screen reader compatibility verified
- [ ] Touch targets minimum 44x44px
- [ ] Lighthouse accessibility score 100
