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
- Laravel Pulse 1.3.0 (performance monitoring - admin/superuser) per Req 36
- Laravel Sanctum 4.0 (API token authentication) per Req 37
- Laravel Socialite 5.x (Google OAuth SSO - optional) per Req 38
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
- Features:
  - Form reference code display (PK.(S).MOTAC.07.(L1)) in header per Requirement 24
  - MyGovEA-compliant minimalist interface per Requirement 23
  - Contextual help tooltips for complex fields
- Inputs: name, email, phone, division, grade, category, description, attachments, PDPA acknowledgement
- Outputs: Ticket creation with status token, confirmation email
- Validation: Per `docs/helpdesk_form_to_model.md`

#### LoanApplicationWizard (Volt Component)

- Location: `resources/views/livewire/loan/application-wizard.blade.php`
- Purpose: Multi-step loan application with asset availability checking and Responsible Officer designation
- Steps: Applicant Info → Responsible Officer → Asset Selection → Date Range → Purpose → Acknowledgement
- Features:
  - Form reference code display (PK.(S).MOTAC.07.(L3)) in header per Requirement 24
  - Responsible Officer section with "Applicant is same" checkbox per Requirement 25
  - Conditional fields for separate Responsible Officer (name, grade, phone)
  - Responsible Officer acknowledgement statement per PK.(S).MOTAC.07.(L3) Part 4
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

### MOTAC Branding Components (per D12-D14, MyGOV DSS v2.1.0)

#### Brand Assets Registry

```text
public/images/
├── jata-negara.svg          # Malaysian Coat of Arms (vector, scalable)
├── motac-logo.jpeg          # MOTAC logo (source, high-res)
├── motac-logo.png           # MOTAC logo (PNG derivative, 120x120)
├── motac-logo-32.png        # MOTAC logo (notification icon, 32x32)
├── motac-logo-64.png        # MOTAC logo (medium icon, 64x64)
├── bpm-logo.png             # BPM division logo (120x120)
├── favicon.ico              # Browser favicon (MOTAC-branded)
├── web-app-manifest-192x192.png  # PWA icon (192x192)
└── web-app-manifest-512x512.png  # PWA icon (512x512)
```

#### GovHeader (Blade Component)

- Location: `resources/views/components/layout/gov-header.blade.php`
- Purpose: Official government header with Jata Negara and MOTAC branding
- Usage: All public-facing pages (guest forms, status check, approval pages)
- Structure:

  ```blade
  <header class="bg-white border-b border-gray-200">
    <div class="max-w-7xl mx-auto px-4 py-3">
      <div class="flex items-center space-x-4">
        {{-- Jata Negara (Malaysian Coat of Arms) --}}
        <img src="{{ asset('images/jata-negara.svg') }}" 
             alt="{{ __('common.jata_negara') }}" 
             class="h-12 w-auto" width="48" height="48">
        
        {{-- MOTAC Logo --}}
        <img src="{{ asset('images/motac-logo.png') }}" 
             alt="{{ __('common.motac_logo') }}" 
             class="h-10 w-auto" width="40" height="40">
        
        {{-- Ministry Name --}}
        <div class="hidden sm:block">
          <p class="text-sm font-semibold text-gray-900">
            {{ __('common.motac_full_name') }}
          </p>
          <p class="text-xs text-gray-600">
            {{ __('common.bpm_full_name') }}
          </p>
        </div>
      </div>
    </div>
  </header>
  ```

#### GovFooter (Blade Component)

- Location: `resources/views/components/layout/gov-footer.blade.php`
- Purpose: Official government footer with ministry information and disclaimer
- Usage: All pages (guest, authenticated, admin)
- Structure:

  ```blade
  <footer class="bg-gray-900 text-white py-8">
    <div class="max-w-7xl mx-auto px-4">
      <div class="flex flex-col md:flex-row items-center justify-between">
        {{-- Ministry Branding --}}
        <div class="flex items-center space-x-4 mb-4 md:mb-0">
          <img src="{{ asset('images/jata-negara.svg') }}" 
               alt="{{ __('common.jata_negara') }}" 
               class="h-10 w-auto filter brightness-0 invert">
          <div>
            <p class="font-semibold">{{ __('common.motac_full_name') }}</p>
            <p class="text-sm text-gray-400">{{ __('common.gov_disclaimer') }}</p>
          </div>
        </div>
        
        {{-- Copyright --}}
        <p class="text-sm text-gray-400">
          © {{ date('Y') }} {{ __('common.bpm_full_name') }}
        </p>
      </div>
    </div>
  </footer>
  ```

#### FormHeader (Blade Component)

- Location: `resources/views/components/form/header.blade.php`
- Purpose: Branded header for guest forms (helpdesk, loan application)
- Usage: Top of all guest submission forms
- Structure:

  ```blade
  <div class="bg-linear-to-r from-blue-900 to-blue-700 text-white p-6 rounded-t-lg">
    <div class="flex items-center space-x-4">
      <img src="{{ asset('images/bpm-logo.png') }}" 
           alt="BPM MOTAC" 
           class="h-16 w-16 rounded object-cover">
      <div>
        <h1 class="text-xl font-bold">{{ $title }}</h1>
        <p class="text-sm text-blue-200">{{ $subtitle }}</p>
      </div>
    </div>
  </div>
  ```

#### EmailBranding (Mail Component)

- Location: `resources/views/vendor/mail/html/header.blade.php`
- Purpose: MOTAC branding in all email notifications
- Structure:

  ```blade
  <tr>
    <td class="header" style="text-align: center; padding: 25px 0;">
      {{-- Jata Negara --}}
      <img src="{{ asset('images/jata-negara.svg') }}" 
           alt="{{ __('common.jata_negara') }}" 
           style="height: 60px; margin-bottom: 10px;">
      
      {{-- MOTAC Logo --}}
      <img src="{{ asset('images/motac-logo.png') }}" 
           alt="{{ __('common.motac_logo') }}" 
           style="height: 50px;">
      
      {{-- Ministry Tagline --}}
      <p style="color: #0056b3; font-size: 14px; margin-top: 10px;">
        {{ __('common.motac_tagline') }}
      </p>
    </td>
  </tr>
  ```

#### PDFBranding (PDF Export Component)

- Location: `resources/views/exports/pdf/letterhead.blade.php`
- Purpose: Official letterhead for PDF exports (audit reports, receipts)
- Structure:

  ```blade
  <div style="border-bottom: 2px solid #0056b3; padding-bottom: 20px; margin-bottom: 20px;">
    <table width="100%">
      <tr>
        <td width="80">
          <img src="{{ public_path('images/jata-negara.svg') }}" height="60">
        </td>
        <td width="80">
          <img src="{{ public_path('images/motac-logo.png') }}" height="50">
        </td>
        <td>
          <p style="font-size: 16px; font-weight: bold; color: #0056b3;">
            {{ __('common.motac_full_name') }}
          </p>
          <p style="font-size: 12px; color: #666;">
            {{ __('common.bpm_full_name') }}
          </p>
        </td>
      </tr>
    </table>
  </div>
  ```

#### Filament Admin Branding

- Location: `app/Providers/Filament/AdminPanelProvider.php`
- Configuration:

  ```php
  ->brandName('ICTServe Admin')
  ->brandLogo(asset('images/motac-logo.png'))
  ->brandLogoHeight('2.5rem')
  ->favicon(asset('favicon.ico'))
  ->darkModeBrandLogo(asset('images/motac-logo.png'))
  ```

#### Browser Notification Icon

- Location: `resources/js/portal-echo.js`
- Configuration:

  ```javascript
  new Notification(title, {
      body: message,
      icon: '/images/motac-logo-32.png',
      badge: '/images/motac-logo-32.png'
  });
  ```

### Admin Panel Components (Filament 4.1.10 per D03 SRS-ADM)

#### HelpdeskTicketResource

- Location: `app/Filament/Resources/HelpdeskTicketResource.php`
- Features: CRUD, filtering, status management, assignment, SLA indicators
- Actions: Assign, Update Status, Add Comment, Close

#### LoanApplicationResource

- Location: `app/Filament/Resources/LoanApplicationResource.php`
- Features: CRUD, approval chain, check-out/check-in, damage reporting, accessory tracking
- Actions: Process Approval, Check-out, Check-in, Report Damage
- Check-out Features (per Requirement 26):
  - Accessory checklist (Power Adapter, Bag, Mouse, USB Cable, HDMI/VGA Cable, Remote, Others)
  - Condition notes per accessory
  - Custom accessory name field for "Others"
- Check-in Features (per Requirement 26):
  - Pre-populated accessory checklist from check-out
  - Discrepancy highlighting for missing/changed items
  - Accessory condition comparison
- Display Features (per Requirement 25):
  - Applicant and Responsible Officer information (when different)
  - Form reference code (PK.(S).MOTAC.07.(L3))

#### DashboardWidgets (per D03 SRS-ADM-003)

- HelpdeskStatsWidget: Open/In-Progress/Resolved counts, SLA compliance
- LoanStatsWidget: Pending/Active/Overdue counts
- RecentActivityWidget: Real-time activity feed via Laravel Reverb
- PerformanceMetricsWidget: Laravel Pulse integration for real-time performance data per Req 36
- SystemHealthWidget: Server health metrics (CPU, memory, disk) per Req 36

### Performance Monitoring Components (per D03 §8.2, Requirement 36)

#### PulseDashboard

- Location: `/pulse` route (Laravel Pulse built-in)
- Purpose: Real-time application performance monitoring dashboard
- Access: Restricted to admin and superuser roles via `PulseServiceProvider`
- Features:
  - Slow query tracking (>500ms threshold)
  - Queue job performance metrics
  - Request response time analysis
  - Server health metrics (CPU, memory, disk)
  - Cache hit/miss rates
  - 7-day data retention with automatic pruning

#### PulseServiceProvider Configuration

```php
// app/Providers/PulseServiceProvider.php
public function boot(): void
{
    Pulse::user(fn ($user) => [
        'name' => $user->name,
        'email' => $user->email,
    ]);

    Gate::define('viewPulse', function (User $user) {
        return in_array($user->role, ['admin', 'superuser']);
    });
}
```

### API Authentication Components (per D03 SRS-API-001, Requirement 37)

#### ApiTokenResource (Filament Resource)

- Location: `app/Filament/Resources/ApiTokenResource.php`
- Purpose: Manage API tokens for admin/superuser users
- Features:
  - Token creation with abilities selection
  - Token revocation
  - Usage statistics display
  - Expiration management
- Access: Restricted to admin and superuser roles

#### API Routes Configuration

```php
// routes/api.php
Route::middleware(['auth:sanctum', 'throttle:api'])->group(function () {
    // Ticket endpoints
    Route::get('/tickets', [ApiTicketController::class, 'index'])
        ->middleware('ability:read:tickets,admin:all');
    Route::post('/tickets', [ApiTicketController::class, 'store'])
        ->middleware('ability:write:tickets,admin:all');
    
    // Loan endpoints
    Route::get('/loans', [ApiLoanController::class, 'index'])
        ->middleware('ability:read:loans,admin:all');
    Route::post('/loans', [ApiLoanController::class, 'store'])
        ->middleware('ability:write:loans,admin:all');
});
```

### Google Workspace SSO Components (per D03 SRS-AUTH-001, Requirement 38)

#### GoogleSsoController

- Location: `app/Http/Controllers/Auth/GoogleSsoController.php`
- Purpose: Handle Google OAuth 2.0 authentication flow
- Methods:
  - `redirect()`: Redirect to Google OAuth consent screen
  - `callback()`: Handle Google OAuth callback and user creation/linking
- Features:
  - Domain validation (@motac.gov.my only)
  - Auto-account creation for new users
  - Account linking for existing users
  - Audit logging for all OAuth events

#### Google Login Button Component

- Location: `resources/views/components/auth/google-login-button.blade.php`
- Purpose: "Sign in with Google" button for login page
- Structure:

  ```blade
  <a href="{{ route('auth.google.redirect') }}" 
     class="flex items-center justify-center w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm bg-white text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
    <svg class="w-5 h-5 mr-2" viewBox="0 0 24 24">
      {{-- Google logo SVG --}}
    </svg>
    {{ __('auth.sign_in_with_google') }}
  </a>
  ```

#### Socialite Configuration

```php
// config/services.php
'google' => [
    'client_id' => env('GOOGLE_CLIENT_ID'),
    'client_secret' => env('GOOGLE_CLIENT_SECRET'),
    'redirect' => env('GOOGLE_REDIRECT_URI', '/auth/google/callback'),
],
```

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

#### AccessoryTrackingService (per D03 SRS-LOAN-007, Requirement 26)

```php
interface AccessoryTrackingServiceInterface
{
    public function getStandardAccessories(): array; // Returns enum values
    public function recordCheckoutAccessories(LoanTransaction $transaction, array $accessories): void;
    public function recordCheckinAccessories(LoanTransaction $transaction, array $accessories): void;
    public function getAccessoryDiscrepancies(LoanTransaction $checkoutTx, LoanTransaction $checkinTx): array;
    public function getAccessoriesForTransaction(LoanTransaction $transaction): Collection;
}
```

#### ResponsibleOfficerService (per D03 SRS-LOAN-001, Requirement 25)

```php
interface ResponsibleOfficerServiceInterface
{
    public function setResponsibleOfficer(LoanApplication $app, array $officerData): void;
    public function copyApplicantAsResponsibleOfficer(LoanApplication $app): void;
    public function getResponsibleOfficerDetails(LoanApplication $app): array;
    public function isApplicantResponsible(LoanApplication $app): bool;
}
```

#### PerformanceMonitoringService (per D03 §8.2, Requirement 36)

```php
interface PerformanceMonitoringServiceInterface
{
    public function getSlowQueries(int $thresholdMs = 500): Collection;
    public function getQueueJobMetrics(): array;
    public function getRequestMetrics(): array;
    public function getServerHealthMetrics(): array;
    public function checkPerformanceThresholds(): array; // Returns exceeded thresholds
    public function triggerPerformanceAlert(string $metric, float $value, float $threshold): void;
    public function pruneOldData(int $retentionDays = 7): int;
}
```

#### ApiTokenService (per D03 SRS-API-001, Requirement 37)

```php
interface ApiTokenServiceInterface
{
    public function createToken(User $user, string $name, array $abilities = ['*'], ?int $expirationDays = 30): NewAccessToken;
    public function revokeToken(User $user, int $tokenId): bool;
    public function revokeAllTokens(User $user): int;
    public function getActiveTokens(User $user): Collection;
    public function validateTokenAbilities(PersonalAccessToken $token, array $requiredAbilities): bool;
    public function logTokenUsage(PersonalAccessToken $token, string $action): void;
}
```

#### GoogleSsoService (per D03 SRS-AUTH-001, Requirement 38)

```php
interface GoogleSsoServiceInterface
{
    public function redirectToGoogle(): RedirectResponse;
    public function handleGoogleCallback(): User;
    public function validateGoogleDomain(string $email): bool; // Must be @motac.gov.my
    public function findOrCreateUser(SocialiteUser $googleUser): User;
    public function linkGoogleAccount(User $user, SocialiteUser $googleUser): void;
    public function unlinkGoogleAccount(User $user): void;
    public function isGoogleLinked(User $user): bool;
}
```

## Data Models (per D09)

### HelpdeskTicket (per D09 §4.1)

```text
helpdesk_tickets
├── id (BIGINT, PK)
├── user_id (BIGINT, FK → users, NULLABLE) - Hybrid: linked if authenticated per D02 FR-050
├── ticket_number (VARCHAR(20), UNIQUE) - Format: HD-YYYYMM-XXXX
├── form_reference_code (VARCHAR(50), DEFAULT 'PK.(S).MOTAC.07.(L1)') - Official form code per Req 24
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
├── form_reference_code (VARCHAR(50), DEFAULT 'PK.(S).MOTAC.07.(L3)') - Official form code per Req 24
├── applicant_name (VARCHAR(255)) - Guest metadata (always stored)
├── applicant_email (VARCHAR(255)) - Encrypted at rest
├── applicant_phone (VARCHAR(50)) - Encrypted at rest
├── applicant_division_code (VARCHAR(20))
├── applicant_grade (VARCHAR(50))
├── is_applicant_responsible (BOOLEAN, DEFAULT TRUE) - Applicant = Responsible Officer per Req 25
├── responsible_officer_name (VARCHAR(255), NULLABLE) - Per PK.(S).MOTAC.07.(L3) Part 2
├── responsible_officer_grade (VARCHAR(50), NULLABLE) - Position & Grade
├── responsible_officer_phone (VARCHAR(50), NULLABLE) - Encrypted at rest
├── purpose (TEXT)
├── location (VARCHAR(255))
├── loan_start_date (DATE)
├── loan_end_date (DATE)
├── acknowledgement (BOOLEAN) - PDPA acknowledgement
├── responsible_officer_acknowledgement (BOOLEAN, DEFAULT FALSE) - Per PK.(S).MOTAC.07.(L3) Part 4
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

### LoanTransactionAccessory (per D09 §4.4, Requirement 26)

```text
loan_transaction_accessories
├── id (BIGINT, PK)
├── loan_transaction_id (BIGINT, FK → loan_transactions)
├── accessory_type (ENUM: POWER_ADAPTER, BAG, MOUSE, USB_CABLE, HDMI_VGA_CABLE, REMOTE, OTHERS)
├── accessory_name (VARCHAR(100), NULLABLE) - For OTHERS type only
├── present_at_checkout (BOOLEAN, DEFAULT FALSE)
├── present_at_checkin (BOOLEAN, NULLABLE) - NULL until check-in
├── condition_notes (TEXT, NULLABLE)
├── created_at (TIMESTAMP)
└── updated_at (TIMESTAMP)
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
├── google_id (VARCHAR(255), NULLABLE, UNIQUE) - Google OAuth ID per Req 38
├── google_token (TEXT, NULLABLE) - Google OAuth access token (encrypted)
├── google_refresh_token (TEXT, NULLABLE) - Google OAuth refresh token (encrypted)
├── remember_token (VARCHAR(100), NULLABLE)
├── created_at (TIMESTAMP)
└── updated_at (TIMESTAMP)
```

### PersonalAccessToken (Laravel Sanctum per D03 SRS-API-001, Requirement 37)

```text
personal_access_tokens
├── id (BIGINT, PK)
├── tokenable_type (VARCHAR(255)) - Polymorphic type (App\Models\User)
├── tokenable_id (BIGINT) - User ID
├── name (VARCHAR(255)) - Token name/description
├── token (VARCHAR(64), UNIQUE) - SHA-256 hashed token
├── abilities (TEXT, NULLABLE) - JSON array of abilities
├── last_used_at (TIMESTAMP, NULLABLE)
├── expires_at (TIMESTAMP, NULLABLE) - Token expiration
├── created_at (TIMESTAMP)
└── updated_at (TIMESTAMP)
```

### PulseEntry (Laravel Pulse per D03 §8.2, Requirement 36)

```text
pulse_entries
├── id (BIGINT, PK)
├── timestamp (INT) - Unix timestamp
├── type (VARCHAR(255)) - Entry type (slow_query, slow_request, etc.)
├── key (TEXT) - Entry key (query hash, route, etc.)
├── key_hash (BINARY(16)) - MD5 hash for indexing
├── value (BIGINT, NULLABLE) - Numeric value (duration, count)
└── INDEX (type, key_hash, timestamp)
```

### PulseValue (Laravel Pulse Aggregates per D03 §8.2, Requirement 36)

```text
pulse_values
├── id (BIGINT, PK)
├── timestamp (INT) - Unix timestamp
├── type (VARCHAR(255)) - Value type
├── key (TEXT) - Value key
├── key_hash (BINARY(16)) - MD5 hash for indexing
├── value (TEXT) - Stored value
└── INDEX (type, key_hash, timestamp)
```

### ApiTokenUsageLog (per D09 §4.6, Requirement 37)

```text
api_token_usage_logs
├── id (BIGINT, PK)
├── personal_access_token_id (BIGINT, FK → personal_access_tokens)
├── user_id (BIGINT, FK → users)
├── action (VARCHAR(100)) - API action performed
├── endpoint (VARCHAR(255)) - API endpoint accessed
├── ip_hash (VARCHAR(128)) - Hashed IP address
├── user_agent (VARCHAR(255), NULLABLE)
├── response_status (INT) - HTTP response status
├── created_at (TIMESTAMP)
└── INDEX (user_id, created_at)
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

### MOTAC Branding Properties

**Property 47: Jata Negara Presence on Public Pages**
_For any_ public-facing page (guest forms, status check, approval pages), the rendered HTML SHALL contain an `<img>` element with `src` attribute pointing to `images/jata-negara.svg` and `alt` attribute containing the localized Jata Negara text.
**Validates: Requirements 21.1**

**Property 48: MOTAC Logo Presence on Public Pages**
_For any_ public-facing page, the rendered HTML SHALL contain an `<img>` element with `src` attribute pointing to `images/motac-logo.png` and `alt` attribute containing the localized MOTAC logo text.
**Validates: Requirements 21.2**

**Property 49: BPM Logo Presence in Form Headers**
_For any_ guest submission form (helpdesk, loan), the form header section SHALL contain an `<img>` element with `src` attribute pointing to `images/bpm-logo.png`.
**Validates: Requirements 21.3**

**Property 50: Filament Admin Panel Branding**
_For any_ authenticated admin panel page, the Filament brand logo SHALL be configured to use `images/motac-logo.png` with proper alt text from translation keys.
**Validates: Requirements 21.4**

**Property 51: Email Template Branding**
_For any_ email notification sent by the system, the email HTML SHALL contain both Jata Negara and MOTAC logo images with the official ministry tagline.
**Validates: Requirements 21.5**

**Property 52: Browser Notification Icon**
_For any_ browser notification triggered by the system, the notification SHALL use `images/motac-logo-32.png` as the icon.
**Validates: Requirements 21.6**

**Property 53: PDF Export Branding**
_For any_ PDF export (audit reports, submission receipts), the document SHALL contain Jata Negara, MOTAC logo, and official letterhead styling with ministry name.
**Validates: Requirements 21.7**

**Property 54: PWA Manifest Icons**
_For any_ PWA installation, the manifest SHALL reference MOTAC-branded icons at `web-app-manifest-192x192.png` and `web-app-manifest-512x512.png`.
**Validates: Requirements 21.8**

**Property 55: Footer Ministry Name**
_For any_ page footer, the rendered HTML SHALL contain the full ministry name from `common.motac_full_name` translation key.
**Validates: Requirements 21.9**

**Property 56: Logo Alt Text Accessibility**
_For any_ MOTAC or Jata Negara image element, the `alt` attribute SHALL be populated from the appropriate translation key (`common.motac_logo` or `common.jata_negara`) in the current locale.
**Validates: Requirements 21.10**

### Government Visual Standards Properties

**Property 57: MOTAC Primary Color Usage**
_For any_ branded UI element (headers, buttons, links), the primary brand color SHALL be #0056b3 or a WCAG-compliant variant.
**Validates: Requirements 22.1**

**Property 58: Logo Clear Space**
_For any_ logo placement, the surrounding padding SHALL be at least 8px to maintain clear space requirements.
**Validates: Requirements 22.2**

**Property 59: Logo Integrity**
_For any_ government logo (Jata Negara, MOTAC), the image SHALL NOT be distorted, recolored, or modified from the original asset.
**Validates: Requirements 22.3**

**Property 60: Government Disclaimer Presence**
_For any_ public page footer, the text "Sistem Rasmi Kerajaan Malaysia" SHALL be displayed.
**Validates: Requirements 22.5**

### MyGovEA Design Principles Properties

**Property 61: Citizen-Centric Design Implementation**
_For any_ user interface, the design SHALL prioritize user needs with intuitive navigation, clear feedback, and minimal cognitive load per MyGovEA Berpaksikan Rakyat principle.
**Validates: Requirements 23.1**

**Property 62: Minimalist Interface Compliance**
_For any_ page layout, the interface SHALL avoid unnecessary components, maintain consistent navigation patterns, and ensure intuitive user flows per MyGovEA Antara Muka Minimalis principle.
**Validates: Requirements 23.2**

**Property 63: Error Prevention Confirmation Dialogs**
_For any_ destructive action (delete, cancel submission, reject application), the system SHALL display a confirmation dialog requiring explicit user confirmation before proceeding per MyGovEA Pencegahan Ralat principle.
**Validates: Requirements 23.3**

**Property 64: Contextual Help Availability**
_For any_ complex form field or action, the system SHALL provide contextual help via tooltips, and the footer SHALL contain links to FAQ and user manual per MyGovEA Panduan dan Dokumentasi principle.
**Validates: Requirements 23.4**

**Property 65: Cognitive Load Reduction**
_For any_ form or dashboard, information SHALL be organized logically with clear visual hierarchy, avoiding information overload per MyGovEA Kognitif principle.
**Validates: Requirements 23.5**

**Property 66: Hierarchical Navigation Structure**
_For any_ navigation menu, content SHALL be organized in logical hierarchies that facilitate easy discovery and predictable user journeys per MyGovEA Struktur Hierarki principle.
**Validates: Requirements 23.6**

**Property 67: User Control Consistency**
_For any_ interactive element, controls SHALL be clear, consistent, and allow users to understand and predict system behavior per MyGovEA Kawalan Pengguna principle.
**Validates: Requirements 23.7**

### Official Form Reference Code Properties

**Property 68: Helpdesk Form Reference Code Display**
_For any_ helpdesk ticket form, the official form reference code "PK.(S).MOTAC.07.(L1)" SHALL be displayed in the form header area (top-right of form container).
**Validates: Requirements 24.1**

**Property 69: Loan Form Reference Code Display**
_For any_ loan application form, the official form reference code "PK.(S).MOTAC.07.(L3)" SHALL be displayed in the form header area (top-right of form container).
**Validates: Requirements 24.2**

**Property 70: Form Reference Code Storage**
_For any_ created helpdesk ticket or loan application, the `form_reference_code` field SHALL be populated with the appropriate official code and stored in the database.
**Validates: Requirements 24.3**

**Property 71: PDF Export Form Reference Code**
_For any_ PDF export or receipt, the document header SHALL include the official form reference code matching the submission type.
**Validates: Requirements 24.4**

### Responsible Officer Workflow Properties

**Property 72: Responsible Officer Section Display**
_For any_ loan application wizard, the system SHALL display a "Responsible Officer" section with a checkbox "Applicant is the same as Responsible Officer" (default: checked).
**Validates: Requirements 25.1**

**Property 73: Responsible Officer Fields Toggle**
_For any_ loan application where the checkbox is unchecked, the system SHALL display and require additional fields: name, position & grade, and phone number for the Responsible Officer.
**Validates: Requirements 25.2**

**Property 74: Responsible Officer Auto-Population**
_For any_ loan application where the checkbox is checked, the system SHALL auto-populate Responsible Officer fields from Applicant data and hide the additional input fields.
**Validates: Requirements 25.3**

**Property 75: Responsible Officer Data Storage**
_For any_ created loan application, the system SHALL store Responsible Officer information in dedicated fields: `responsible_officer_name`, `responsible_officer_grade`, `responsible_officer_phone`, and `is_applicant_responsible`.
**Validates: Requirements 25.4**

**Property 76: Responsible Officer Display Differentiation**
_For any_ loan application detail view (status check, admin view, PDF export) where Applicant differs from Responsible Officer, the system SHALL clearly display both parties' information separately.
**Validates: Requirements 25.5**

**Property 77: Responsible Officer Acknowledgement**
_For any_ loan application, the system SHALL include and require acceptance of the Responsible Officer acknowledgement statement per PK.(S).MOTAC.07.(L3) Part 4.
**Validates: Requirements 25.6**

### Asset Accessory Tracking Properties

**Property 78: Accessory Checklist Display at Check-out**
_For any_ asset check-out operation, the system SHALL display an accessory checklist with standard items: Power Adapter, Bag, Mouse, USB Cable, HDMI/VGA Cable, Remote, and Others.
**Validates: Requirements 26.1**

**Property 79: Accessory Status Recording**
_For any_ accessory item during check-out, the system SHALL allow marking as "Included" or "Not Included" with optional condition notes.
**Validates: Requirements 26.2**

**Property 80: Custom Accessory Entry**
_For any_ check-out where "Others" is selected, the system SHALL provide a text field to specify the additional accessory name.
**Validates: Requirements 26.3**

**Property 81: Accessory Checklist Pre-population at Check-in**
_For any_ asset check-in operation, the system SHALL display the accessory checklist pre-populated with check-out data for comparison.
**Validates: Requirements 26.4**

**Property 82: Accessory Discrepancy Highlighting**
_For any_ check-in operation, the system SHALL highlight discrepancies between check-out and check-in accessory status (missing items, condition changes).
**Validates: Requirements 26.5**

**Property 83: Accessory Data Storage**
_For any_ accessory tracking operation, the system SHALL store data in `loan_transaction_accessories` table with: transaction_id, accessory_type, accessory_name (for Others), present_at_checkout, present_at_checkin, and condition_notes.
**Validates: Requirements 26.6**

**Property 84: Accessory Report Inclusion**
_For any_ loan transaction report, the system SHALL include complete accessory tracking information for audit purposes.
**Validates: Requirements 26.7**

### Performance Monitoring Properties (Requirement 36)

**Property 85: Slow Query Detection**
_For any_ database query exceeding 500ms execution time, the system SHALL record it in Laravel Pulse with query details, frequency, and execution time.
**Validates: Requirements 36.2**

**Property 86: Queue Job Metrics Tracking**
_For any_ queue job execution, the system SHALL track processing time, failure status, and retry count in Laravel Pulse metrics.
**Validates: Requirements 36.3**

**Property 87: Request Pattern Tracking**
_For any_ HTTP request, the system SHALL record response time, memory usage, and cache hit/miss status in Laravel Pulse.
**Validates: Requirements 36.4**

**Property 88: Server Health Metrics**
_For any_ Pulse dashboard access, the system SHALL display current CPU usage, memory consumption, and disk space utilization.
**Validates: Requirements 36.5**

**Property 89: Pulse Access Control**
_For any_ user attempting to access `/pulse`, the system SHALL allow access only if user role is `admin` or `superuser`. All other users SHALL receive HTTP 403 Forbidden.
**Validates: Requirements 36.6**

**Property 90: Pulse Data Retention**
_For any_ Pulse data older than 7 days, the system SHALL automatically prune it during scheduled cleanup. Data within 7 days SHALL be retained.
**Validates: Requirements 36.7**

**Property 91: Performance Alert Triggering**
_For any_ performance metric exceeding configured threshold, the system SHALL trigger an alert via configured notification channels within 60 seconds.
**Validates: Requirements 36.8**

### API Authentication Properties (Requirement 37)

**Property 92: API Token Generation**
_For any_ token creation request by admin/superuser, the system SHALL generate a Sanctum personal access token with specified abilities and expiration period.
**Validates: Requirements 37.1, 37.2**

**Property 93: Token Abilities Enforcement**
_For any_ API request with a token, the system SHALL validate that the token has the required abilities for the requested endpoint. Requests without required abilities SHALL receive HTTP 403 Forbidden.
**Validates: Requirements 37.3**

**Property 94: API Rate Limiting**
_For any_ API endpoint, the system SHALL enforce rate limiting: 60 requests/minute for authenticated tokens, 10 requests/minute for unauthenticated requests. Exceeded limits SHALL receive HTTP 429 Too Many Requests.
**Validates: Requirements 37.4**

**Property 95: API Authentication Audit Logging**
_For any_ API authentication attempt (success or failure), the system SHALL create an audit log entry with token ID, user ID, action, endpoint, IP hash, and timestamp.
**Validates: Requirements 37.5**

### Google Workspace SSO Properties (Requirement 38)

**Property 96: Google Domain Restriction**
_For any_ Google OAuth authentication attempt, the system SHALL accept only `@motac.gov.my` email domain. All other domains SHALL be rejected with clear error message.
**Validates: Requirements 38.2**

**Property 97: Auto-Account Creation for New Google Users**
_For any_ first-time Google user with valid `@motac.gov.my` email, the system SHALL auto-create a staff account with role `staff`, status `active`, and profile data from Google (name, email).
**Validates: Requirements 38.3**

**Property 98: Existing Account Google Linking**
_For any_ existing user authenticating via Google where email matches, the system SHALL link the Google OAuth to the existing account without creating a duplicate.
**Validates: Requirements 38.4**

**Property 99: Google OAuth Audit Logging**
_For any_ Google OAuth authentication event (success, failure, account creation, account linking), the system SHALL create an audit log entry with event type, user ID, Google ID, and timestamp.
**Validates: Requirements 38.6**

**Property 100: Google OAuth Fallback**
_For any_ Google OAuth failure, the system SHALL display a clear error message and provide fallback to traditional email/password login without blocking access.
**Validates: Requirements 38.7**

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
- **Laravel Pulse Integration**: Verify slow query detection, queue metrics, server health per Req 36

### API Authentication Testing (per D03 SRS-API-001, Requirement 37)

- **Token Generation**: Test token creation with various abilities and expiration
- **Token Validation**: Test ability enforcement on protected endpoints
- **Rate Limiting**: Test 60/min authenticated, 10/min unauthenticated limits
- **Audit Logging**: Verify all API authentication events are logged

### Google SSO Testing (per D03 SRS-AUTH-001, Requirement 38)

- **Domain Validation**: Test @motac.gov.my restriction
- **Auto-Account Creation**: Test new user creation from Google profile
- **Account Linking**: Test existing account linking via email match
- **Fallback Handling**: Test graceful degradation on OAuth failure

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
