# Dokumentasi Kod Sumber (Source Code Documentation)

**Sistem ICTServe**  
**Versi:** 3.6.1 (SemVer)  
**Tarikh Kemaskini:** 17 Disember 2025  
**Status:** Aktif  
**Klasifikasi:** Terhad - Dalaman MOTAC  
**Penulis:** Pasukan Pembangunan BPM MOTAC  
**Standard Rujukan:** ISO/IEC/IEEE 5055, ISO/IEC/IEEE 25000 Series (SQuaRE), ISO/IEC/IEEE 12207

---

## Maklumat Dokumen (Document Information)

| Atribut              | Nilai                                     |
| -------------------- | ----------------------------------------- |
| **Versi**            | 3.6.1                                     |
| **Tarikh Kemaskini** | 17 Disember 2025                          |
| **Status**           | Aktif                                     |
| **Klasifikasi**      | Terhad - Dalaman MOTAC                    |
| **Pematuhi**         | ISO/IEC/IEEE 5055, ISO/IEC/IEEE 25000, ISO/IEC/IEEE 12207 |
| **Bahasa**           | Bahasa Melayu sahaja (v3.6.0)             |
| **Pematuhi**         | ISO/IEC/IEEE 5055, 25000 Series, 12207    |
| **Bahasa**           | Bahasa Melayu (utama), English (teknikal) |

---

## Sejarah Perubahan (Changelog)

| Versi | Tarikh           | Perubahan                                                                                                                                                                                                                                                                                  | Penulis     |
| ----- | ---------------- | ------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------ | ----------- |
| 1.0.0 | September 2025   | Versi awal dokumentasi kod sumber                                                                                                                                                                                                                                                          | Pasukan BPM |
| 2.0.0 | 17 Oktober 2025  | Penyeragaman mengikut D00-D14, SemVer, cross-reference                                                                                                                                                                                                                                     | Pasukan BPM |
| 3.0.0 | 29 November 2025 | Kemaskini struktur kod, Laravel 12, Filament 4, Livewire 3, Tailwind CSS                                                                                                                                                                                                                   | Pasukan BPM |
| 3.1.0 | 29 November 2025 | Selaraskan dengan Guest-First: hapus Staff/Portal, tambah Guest/Status                                                                                                                                                                                                                     | Pasukan BPM |
| 3.5.0 | 1 Disember 2025  | True Hybrid Architecture v3.5.0: Laravel Pulse, Sanctum API, Google SSO, Responsible Officer, Accessory Tracking, Form Reference Codes, MOTAC Branding. New services: GoogleSsoService, ApiTokenService, PerformanceMonitoringService, AccessoryTrackingService, ResponsibleOfficerService. Penyelarasan dengan D00-D09 v3.5.0. | Pasukan BPM |
| 3.6.0 | 8 Disember 2025  | Bahasa Melayu sahaja (v3.6.0): Language switcher dilumpuhkan. | Pasukan BPM |
| 3.7.0 | 14 Disember 2025 | Cloud Hybrid AI Architecture v3.7.0: OllamaClient, BedrockService, RagService, ModelRouter, EmbeddingService, PIIDetectionService, StreamingResponseService, WebSearchService. AI Livewire components: BedrockChat, FaqBot, FaqBotWidget. AI Models: Faq, Document, DocumentChunk, Embedding, BedrockConversation, MessageLog, AutoReplyTemplate, AutoReplyDraft. AI Jobs: DocumentIngestJob, EmbeddingJob, AutoReplyGenerationJob. Penyelarasan dengan D18 v1.0.0. | Pasukan BPM |

---

## Rujukan Dokumen Berkaitan (Related Document References)

- **[D00_SYSTEM_OVERVIEW.md]** - Ringkasan Sistem
- **[D01_SYSTEM_DEVELOPMENT_PLAN.md]** - Pelan Pembangunan Sistem
- **[D04_SOFTWARE_DESIGN_DOCUMENT.md]** - Rekabentuk Perisian
- **[D11_TECHNICAL_DESIGN_DOCUMENTATION.md]** - Dokumentasi Rekabentuk Teknikal
- **[D18_AI_CHATBOT_OLLAMA_BEDROCK.md]** - Dokumentasi AI Chatbot Ollama-Bedrock (v3.7.0)
- **[GLOSSARY.md]** - Glosari Istilah Sistem

---

## 1. Tujuan Dokumen (Purpose)

Dokumen ini memberi penerangan struktur kod sumber, gaya penulisan, piawaian kualiti, dan kawalan perubahan bagi sistem **Helpdesk & ICT Asset Loan BPM MOTAC**, berpandukan piawaian **ISO/IEC/IEEE 5055** (software quality), **ISO/IEC/IEEE 25000 Series (SQuaRE)** (quality requirements and evaluation), dan **ISO/IEC/IEEE 12207** (software lifecycle processes).

---

## 2. Skop (Scope)

- Semua kod sumber Laravel 12 (PHP 8.2), Blade views, Livewire/Volt components, JS, CSS, migration, seeder, factory, dan konfigurasi.
- Piawaian penulisan kod, komen, dokumentasi fungsi, dan kawalan versi.
- Penekanan pada maintainability, reliability, security, dan usability.

---

## 3. Teknologi Teras (Core Technology Stack)

| Komponen              | Versi   | Fungsi                                |
| --------------------- | ------- | ------------------------------------- |
| **PHP**               | 8.2.12  | Bahasa pengaturcaraan utama           |
| **Laravel**           | 12.40.1 | Framework aplikasi web                |
| **Filament**          | 4.1.10  | Admin panel framework                 |
| **Livewire**          | 3.7.0   | Server-driven UI components           |
| **Livewire Volt**     | 1.10.1  | Single-file Livewire components       |
| **Tailwind CSS**      | 4.1.17  | Utility-first CSS framework           |
| **Alpine.js**         | 3.x     | Lightweight JavaScript framework      |
| **Laravel Reverb**    | 1.6.2   | WebSocket server untuk real-time      |
| **Laravel Echo**      | 2.2.6   | WebSocket client                      |
| **PHPUnit**           | 11.5.44 | Testing framework                     |
| **Larastan**          | 3.8.0   | Static analysis (PHPStan for Laravel) |
| **Laravel Pint**      | 1.26.0  | Code formatting (PSR-12)              |
| **MySQL**             | 8.x     | Production database                   |
| **SQLite**            | -       | Development/testing database          |
| **Spatie Permission** | 6.23    | Role-based access control             |
| **Laravel Auditing**  | 14.x    | Field-level audit trail (owen-it)     |
| **Activity Log**      | 4.x     | User activity logging (spatie)        |
| **Laravel Pulse**     | 1.3.0   | Performance monitoring (v3.5.0)       |
| **Laravel Sanctum**   | 4.0     | API token authentication (v3.5.0)     |
| **Laravel Socialite** | 5.x     | Google OAuth SSO (v3.5.0)             |
| **Laravel Telescope** | 5.x     | System debugging (superuser only)     |

---

## 4. Struktur Kod Sumber (Source Code Structure)

### 4.1. Direktori Utama Aplikasi (`app/`)

| Folder                  | Fungsi/Kandungan                                               |
| ----------------------- | -------------------------------------------------------------- |
| `app/Console/Commands/` | Artisan commands (auto-registered)                             |
| `app/Enums/`            | PHP 8.1+ Enums (ApprovalStatus, AssetStatus, LoanStatus, etc.) |
| `app/Events/`           | Event classes untuk broadcasting dan listeners                 |
| `app/Filament/`         | Filament v4 resources, pages, widgets, clusters                |
| `app/Http/Controllers/` | HTTP controllers untuk web dan API routes                      |
| `app/Http/Middleware/`  | Custom middleware (registered in bootstrap/app.php)            |
| `app/Http/Requests/`    | Form Request validation classes                                |
| `app/Jobs/`             | Queued jobs (email, exports, notifications)                    |
| `app/Listeners/`        | Event listeners untuk email, audit, notifications              |
| `app/Livewire/`         | Livewire v3 components (class-based)                           |
| `app/Mail/`             | Mailable classes dengan template support                       |
| `app/Models/`           | Eloquent models dengan relationships dan traits                |
| `app/Notifications/`    | Notification classes (database, mail, broadcast)               |
| `app/Observers/`        | Model observers untuk lifecycle events                         |
| `app/Policies/`         | Authorization policies untuk RBAC                              |
| `app/Providers/`        | Service providers (registered in bootstrap/providers.php)      |
| `app/Rules/`            | Custom validation rules                                        |
| `app/Services/`         | Business logic services (see §7 for v3.5.0 services)           |
| `app/Traits/`           | Reusable traits (HasAuditTrail, OptimizedQueries, etc.)        |
| `app/View/Components/`  | Blade view components                                          |

### 4.2. Direktori Filament (`app/Filament/`)

| Folder                | Fungsi/Kandungan                            |
| --------------------- | ------------------------------------------- |
| `Filament/Clusters/`  | Grouped resources dan pages                 |
| `Filament/Exports/`   | Export classes untuk data export            |
| `Filament/Pages/`     | Custom Filament pages (Dashboard, Settings) |
| `Filament/Resources/` | CRUD resources untuk models                 |
| `Filament/Widgets/`   | Dashboard widgets (stats, charts)           |
| `Filament/Actions/`   | Custom Filament actions (v3.5.0)            |

### 4.3. Direktori Livewire (`app/Livewire/`)

| Folder               | Fungsi/Kandungan                                 |
| -------------------- | ------------------------------------------------ |
| `Livewire/Actions/`  | Action components                                |
| `Livewire/Approver/` | Approval workflow components                     |
| `Livewire/Assets/`   | Asset management components                      |
| `Livewire/Auth/`     | Authentication components (Admin/Superuser only) |
| `Livewire/Forms/`    | Form components                                  |
| `Livewire/Guest/`    | Guest form components (helpdesk, loan)           |
| `Livewire/Helpdesk/` | Helpdesk module components                       |
| `Livewire/Loans/`    | Loan application components                      |
| `Livewire/Status/`   | Status tracking pages (guest token-based)        |
| `Livewire/Staff/`    | Staff dashboard components (v3.5.0)              |
| `Livewire/Profile/`  | Profile management components (v3.5.0)           |

### 4.4. Direktori Database (`database/`)

| Folder                 | Fungsi/Kandungan                             |
| ---------------------- | -------------------------------------------- |
| `database/factories/`  | Model factories untuk testing (16 factories) |
| `database/migrations/` | Database migrations (timestamped, 50+ files) |
| `database/seeders/`    | Database seeders untuk data permulaan        |
| `database/data/`       | Static data files (CSV imports)              |

### 4.5. Direktori Resources (`resources/`)

| Folder             | Fungsi/Kandungan                    |
| ------------------ | ----------------------------------- |
| `resources/css/`   | Tailwind CSS source files           |
| `resources/js/`    | JavaScript entry points dan modules |
| `resources/views/` | Blade templates dan Volt components |
| `lang/`            | Translation files (en, ms)          |

### 4.6. Direktori Routes (`routes/`)

| File                  | Fungsi/Kandungan                         |
| --------------------- | ---------------------------------------- |
| `routes/web.php`      | Web routes (guest, authenticated, admin) |
| `routes/api.php`      | API routes dengan versioning             |
| `routes/auth.php`     | Authentication routes (Laravel Breeze)   |
| `routes/channels.php` | Broadcasting channel authorization       |
| `routes/console.php`  | Console commands dan scheduling          |

### 4.7. Direktori Tests (`tests/`)

| Folder            | Fungsi/Kandungan                        |
| ----------------- | --------------------------------------- |
| `tests/Unit/`     | Unit tests untuk isolated components    |
| `tests/Feature/`  | Feature tests untuk integration testing |
| `tests/Browser/`  | Laravel Dusk browser tests              |
| `tests/e2e/`      | Playwright E2E tests                    |
| `tests/Concerns/` | Test traits dan helpers                 |

---

## 5. Piawaian Penulisan Kod (Coding Standards)

### 5.1. PHP (Laravel 12)

**Strict Typing:**

```php
<?php

declare(strict_types=1);

namespace App\Models;
```

**Constructor Property Promotion (PHP 8.2):**

```php
public function __construct(
    public readonly EmailNotificationService $emailService,
    public readonly SLAManagementService $slaService,
) {}
```

**Type Declarations:**

```php
protected function isAccessible(User $user, ?string $path = null): bool
{
    // Implementation
}
```

**Model Casts (Laravel 12 Method Syntax):**

```php
protected function casts(): array
{
    return [
        'sla_response_due_at' => 'datetime',
        'declaration_accepted' => 'boolean',
        'escalation_level' => 'integer',
    ];
}
```

**Relationship Type Hints:**

```php
/** @return BelongsTo<User, HelpdeskTicket> */
public function user(): BelongsTo
{
    return $this->belongsTo(User::class);
}
```

### 5.2. Enums (PHP 8.1+)

```php
<?php

declare(strict_types=1);

namespace App\Enums;

enum LoanStatus: string
{
    case Pending = 'pending';
    case Approved = 'approved';
    case Rejected = 'rejected';
    case Collected = 'collected';
    case Returned = 'returned';
}
```

### 5.3. Livewire v3 Components

**Class-based Component:**

```php
<?php

declare(strict_types=1);

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\Layout;

#[Layout('layouts.app')]
class Dashboard extends Component
{
    public function render()
    {
        return view('livewire.dashboard');
    }
}
```

**Volt Single-File Component:**

```php
<?php

use function Livewire\Volt\{state, computed, mount};

state(['count' => 0]);

$increment = fn () => $this->count++;

?>

<div>
    <h1>{{ $count }}</h1>
    <button wire:click="increment">+</button>
</div>
```

### 5.4. Filament v4 Resources

```php
<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\TextInput;

class HelpdeskTicketResource extends Resource
{
    protected static ?string $model = HelpdeskTicket::class;

    protected static ?string $navigationIcon = 'heroicon-o-ticket';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Section::make('Ticket Details')
                ->schema([
                    TextInput::make('subject')
                        ->required()
                        ->maxLength(255),
                ]),
        ]);
    }
}
```

### 5.5. Blade Templates

**Component Usage:**

```blade
<x-layouts.app>
    <x-slot name="header">
        {{ __('Dashboard') }}
    </x-slot>

    @foreach ($items as $item)
        <div wire:key="item-{{ $item->id }}">
            {{ $item->name }}
        </div>
    @endforeach
</x-layouts.app>
```

**Livewire Directives:**

```blade
<button wire:click="save" wire:loading.attr="disabled">
    <span wire:loading.remove>{{ __('Save') }}</span>
    <span wire:loading>{{ __('Saving...') }}</span>
</button>
```

### 5.6. Tailwind CSS v4

**CSS Import (v4 Syntax):**

```css
@import "tailwindcss";

@theme {
 --color-brand: oklch(0.72 0.11 178);
}
```

**Utility Classes:**

```html
<div class="flex gap-4 p-6 bg-white dark:bg-gray-800 rounded-lg shadow">
 <span class="text-gray-900 dark:text-white">Content</span>
</div>
```

### 5.7. Comments & Documentation

**PHPDoc Blocks:**

```php
/**
 * HelpdeskTicket Model - Enhanced with Hybrid Architecture Support
 *
 * Supports both guest submissions (no user_id) and authenticated submissions.
 * Integrates with asset loan system for cross-module functionality.
 *
 * @see D03 Software Requirements Specification - Requirement 1, 2
 * @see D04 Software Design Document - Hybrid Architecture
 * @see D09 Database Documentation - helpdesk_tickets table
 *
 * @property string|null $guest_email
 */
```

---

## 6. Model Utama (Core Models)

### 6.1. HelpdeskTicket

```php
/**
 * Helpdesk ticket dengan hybrid architecture support.
 *
 * @property int $id
 * @property string $ticket_number Format: HD[YYYY][000001-999999]
 * @property int|null $user_id Null untuk guest submissions
 * @property string|null $guest_email Email untuk guest submissions
 * @property string $status Open|In Progress|Resolved|Closed
 * @property string $priority Low|Medium|High|Critical
 */
class HelpdeskTicket extends Model implements Auditable
{
    use HasAuditTrail, HasFactory, OptimizedQueries, SoftDeletes;

    // Relationships
    public function user(): BelongsTo;
    public function division(): BelongsTo;
    public function category(): BelongsTo;
    public function comments(): HasMany;
    public function attachments(): HasMany;

    // Hybrid support methods
    public function isGuestSubmission(): bool;
    public function getSubmitterName(): string;
    public function getSubmitterEmail(): string;
}
```

### 6.2. LoanApplication

```php
/**
 * Loan application dengan email-based approval workflow.
 *
 * @property int $id
 * @property string $application_number Format: LA[YYYY][000001-999999]
 * @property string $status Pending|Approved|Rejected|Collected|Returned
 * @property string|null $tracking_token Token untuk guest tracking
 */
class LoanApplication extends Model implements Auditable
{
    use HasAuditTrail, HasFactory, SoftDeletes;

    // Relationships
    public function user(): BelongsTo;
    public function items(): HasMany;
    public function transactions(): HasMany;
    public function approvalTokens(): HasMany;

    // Workflow methods
    public function approve(User $approver, ?string $remarks = null): void;
    public function reject(User $approver, string $reason): void;
}
```

### 6.3. User (v3.5.0 Enhanced)

```php
/**
 * User model dengan RBAC support via Spatie Permission.
 * Enhanced for True Hybrid Architecture v3.5.0.
 *
 * @property int $id
 * @property string $name
 * @property string $email Must be @motac.gov.my for staff
 * @property string|null $google_id Google OAuth ID (v3.5.0)
 * @property int|null $department_id FK to departments
 * @property string|null $grade Gred pegawai
 * @property string|null $staff_number Nombor staf
 * @property string $role staff|admin|superuser
 * @property string $locale ms|en
 * @property string $notify_email_frequency immediate|daily|weekly
 * @property bool $notify_in_app
 * @property int $guest_submissions_linked Count of linked submissions
 */
class User extends Authenticatable implements Auditable
{
    use HasRoles, HasFactory, Notifiable, SoftDeletes;

    // Relationships
    public function department(): BelongsTo;
    public function helpdeskTickets(): HasMany;
    public function loanApplications(): HasMany;
    public function tokens(): MorphMany; // Sanctum API tokens

    // Authorization helpers
    public function isAdmin(): bool;
    public function isSuperuser(): bool;
    public function isStaff(): bool;
    public function canApprove(LoanApplication $loan): bool;

    // Google SSO helpers (v3.5.0)
    public function hasGoogleLinked(): bool;
    public function linkGoogle(string $googleId): void;
    public function unlinkGoogle(): void;

    // Account linking helpers (v3.5.0)
    public function getUnlinkedSubmissions(): Collection;
    public function linkGuestSubmissions(array $submissionIds): int;

    // Note: Approvers identified by email in loan_approvals table, not User role
}
```

### 6.4. LoanTransactionAccessory (v3.5.0)

```php
/**
 * Accessory tracking for loan check-out/check-in.
 *
 * @property int $id
 * @property int $loan_transaction_id
 * @property string $accessory_type POWER_ADAPTER|BAG|MOUSE|USB_CABLE|HDMI_VGA_CABLE|REMOTE|OTHERS
 * @property string|null $accessory_name For OTHERS type
 * @property bool $present_at_checkout
 * @property bool|null $present_at_checkin NULL until check-in
 * @property string|null $condition_notes
 */
class LoanTransactionAccessory extends Model
{
    use HasFactory;

    // Relationships
    public function transaction(): BelongsTo;

    // Helper methods
    public function hasDiscrepancy(): bool;
    public function getAccessoryLabel(): string;
}
```

---

## 7. Services Layer

### 7.1. Email Notification Service

```php
/**
 * Centralized email notification service.
 *
 * @see D03 §7.2 Email Notification Requirements
 */
class EmailNotificationService
{
    public function sendTicketCreatedNotification(HelpdeskTicket $ticket): void;
    public function sendLoanApprovalRequest(LoanApplication $loan): void;
    public function sendLoanStatusUpdate(LoanApplication $loan): void;
}
```

### 7.2. SLA Management Service

```php
/**
 * SLA tracking dan breach detection service.
 *
 * @see D03 §8.2 SLA Requirements
 */
class SLAManagementService
{
    public function calculateDueDates(HelpdeskTicket $ticket): void;
    public function checkBreaches(): Collection;
    public function escalateTicket(HelpdeskTicket $ticket): void;
}
```

### 7.3. Cross-Module Integration Service

```php
/**
 * Integration between Helpdesk and Asset Loan modules.
 *
 * @see D04 §5.3 Cross-Module Integration
 */
class CrossModuleIntegrationService
{
    public function createMaintenanceTicket(Asset $asset, string $reason): HelpdeskTicket;
    public function linkAssetToTicket(HelpdeskTicket $ticket, Asset $asset): void;
}
```

### 7.4. Google SSO Service (v3.5.0)

```php
/**
 * Google Workspace OAuth 2.0 authentication service.
 *
 * @see D03 SRS-AUTH-001 Google SSO Requirements
 */
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

### 7.5. API Token Service (v3.5.0)

```php
/**
 * Laravel Sanctum API token management service.
 *
 * @see D03 SRS-API-001 API Authentication Requirements
 */
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

### 7.6. Performance Monitoring Service (v3.5.0)

```php
/**
 * Laravel Pulse performance monitoring service.
 *
 * @see D03 §8.2 Performance Requirements
 */
interface PerformanceMonitoringServiceInterface
{
    public function getSlowQueries(int $thresholdMs = 500): Collection;
    public function getQueueJobMetrics(): array;
    public function getRequestMetrics(): array;
    public function getServerHealthMetrics(): array;
    public function checkPerformanceThresholds(): array;
    public function triggerPerformanceAlert(string $metric, float $value, float $threshold): void;
    public function pruneOldData(int $retentionDays = 7): int;
}
```

### 7.7. Accessory Tracking Service (v3.5.0)

```php
/**
 * Loan accessory tracking service for check-out/check-in.
 *
 * @see D03 SRS-LOAN-007 Accessory Tracking Requirements
 */
interface AccessoryTrackingServiceInterface
{
    public function getStandardAccessories(): array; // Returns enum values
    public function recordCheckoutAccessories(LoanTransaction $transaction, array $accessories): void;
    public function recordCheckinAccessories(LoanTransaction $transaction, array $accessories): void;
    public function getAccessoryDiscrepancies(LoanTransaction $checkoutTx, LoanTransaction $checkinTx): array;
    public function getAccessoriesForTransaction(LoanTransaction $transaction): Collection;
}
```

### 7.8. Responsible Officer Service (v3.5.0)

```php
/**
 * Responsible Officer management for loan applications.
 *
 * @see D03 SRS-LOAN-001 Responsible Officer Requirements
 */
interface ResponsibleOfficerServiceInterface
{
    public function setResponsibleOfficer(LoanApplication $app, array $officerData): void;
    public function copyApplicantAsResponsibleOfficer(LoanApplication $app): void;
    public function getResponsibleOfficerDetails(LoanApplication $app): array;
    public function isApplicantResponsible(LoanApplication $app): bool;
}
```

### 7.9. Account Linking Service

```php
/**
 * Optional guest-to-account linking service.
 *
 * @see D02 FR-050 Account Linking Requirements
 */
interface AccountLinkingServiceInterface
{
    public function findUnlinkedSubmissions(string $email): Collection;
    public function linkSubmissions(User $user, array $submissionIds): int;
    public function getLinkedSubmissionCount(User $user): int;
}
```

### 7.10. AI Services (Cloud Hybrid AI Architecture v3.7.0)

> **Rujukan:** D18_AI_CHATBOT_OLLAMA_BEDROCK.md v1.0.0

#### 7.10.1. OllamaClient Service

```php
/**
 * HTTP wrapper for Ollama local LLM server.
 *
 * @see D18 §6.1 Keperluan API Ollama Kritikal
 */
interface OllamaClientContract
{
    public function generate(array $payload): array;
    public function embeddings(string $text): array;
    public function chat(array $messages): array;
    public function models(): array;
    public function healthCheck(): bool;
    public function getCachedResponse(string $cacheKey): ?array;
    public function cacheResponse(string $cacheKey, array $response, int $ttl): void;
}
```

#### 7.10.2. BedrockService

```php
/**
 * AWS Bedrock API wrapper for Claude models.
 * Supports Opus 4.5, Sonnet 4.5, Haiku 4.5.
 *
 * @see D18 §6.2 Keperluan Inference Profile AWS Bedrock
 */
interface BedrockClientContract
{
    public function invokeModel(string $modelId, array $payload): array;
    public function invokeModelWithStreaming(string $modelId, array $payload): Generator;
    public function listFoundationModels(): array;
    public function getModelInfo(string $modelId): array;
    public function healthCheck(): bool;
    public function estimateCost(string $modelId, int $inputTokens, int $outputTokens): float;
}
```

#### 7.10.3. RagService

```php
/**
 * Retrieval-Augmented Generation service for FAQ knowledge base.
 *
 * @see D18 §3.2 Tanggungjawab Komponen
 */
interface RagServiceContract
{
    public function query(string $question, ?array $context = null): array;
    public function retrieveContext(string $query, int $limit = 5): Collection;
    public function generateResponse(string $query, Collection $context): string;
    public function getConversationHistory(string $conversationId): array;
    public function saveConversation(string $conversationId, array $messages): void;
}
```

#### 7.10.4. ModelRouter Service

```php
/**
 * Smart model selection based on task complexity.
 *
 * @see D18 §5.3 Logik Penghalaan Model
 */
class ModelRouter
{
    public function selectModel(string $taskType, array $context): string;
    public function analyzeQuery(string $query): string; // Returns: faq_specific|complex_reasoning|hybrid
    public function getModelConfig(string $modelName): array;
}
```

#### 7.10.5. EmbeddingService

```php
/**
 * Vector embeddings operations for semantic search.
 *
 * @see D18 §3.2 Tanggungjawab Komponen
 */
interface EmbeddingServiceContract
{
    public function generateEmbedding(string $text): array;
    public function batchGenerateEmbeddings(array $texts): array;
    public function searchSimilar(array $queryEmbedding, int $limit = 5): Collection;
    public function storeEmbedding(int $documentChunkId, array $embedding): void;
}
```

#### 7.10.6. PIIDetectionService

```php
/**
 * PII detection and sanitization for PDPA 2010 compliance.
 *
 * @see D18 §3.2 Tanggungjawab Komponen
 */
interface PIIDetectionServiceContract
{
    public function detectPII(string $text): array;
    public function sanitize(string $text): string;
    public function classifyDataResidency(string $text): string; // Returns: local|cloud
    public function getDetectedPatterns(): array;
}
```

#### 7.10.7. DocumentService

```php
/**
 * Document ingestion and analysis service.
 *
 * @see D18 §10.3 Document Analysis API
 */
interface DocumentServiceContract
{
    public function ingest(UploadedFile $file, array $metadata = []): Document;
    public function analyze(Document $document): array;
    public function chunk(Document $document, int $chunkSize = 500): Collection;
    public function search(Document $document, string $query, int $limit = 5): Collection;
    public function getSummary(Document $document): string;
}
```

#### 7.10.8. StreamingResponseService (Future)

```php
/**
 * Server-Sent Events handler for streaming AI responses.
 *
 * @see D18 §2.3 Ciri Utama v3.6.1 - Streaming Responses (Future)
 */
interface StreamingResponseServiceContract
{
    public function stream(string $modelId, array $payload): Generator;
    public function handleSSE(Generator $stream): StreamedResponse;
    public function formatChunk(string $content, string $type = 'content'): string;
}
```

#### 7.10.9. WebSearchService

```php
/**
 * DuckDuckGo integration for web-augmented responses.
 *
 * @see D18 §2.3 Ciri Utama v3.6.1 - Web-Augmented Responses
 */
interface WebSearchServiceContract
{
    public function search(string $query, int $limit = 5): array;
    public function augmentContext(string $query, array $existingContext): array;
    public function isEnabled(): bool;
}
```

### 7.11. AI Models (v3.7.0)

#### 7.11.1. Faq Model

```php
/**
 * FAQ knowledge base entry.
 *
 * @property int $id
 * @property string $question
 * @property string $answer
 * @property string $category
 * @property int|null $user_id Nullable for guest-accessible FAQs
 * @property bool $is_published
 */
class Faq extends Model implements Auditable
{
    use HasAuditTrail, HasFactory, SoftDeletes;

    public function chunks(): HasMany;
    public function embeddings(): HasManyThrough;
}
```

#### 7.11.2. Document Model

```php
/**
 * Document for AI analysis and RAG.
 *
 * @property int $id
 * @property string $filename
 * @property string $original_filename
 * @property string $mime_type
 * @property int $size
 * @property string $status processing|completed|failed
 * @property string|null $summary
 * @property array|null $key_topics
 */
class Document extends Model implements Auditable
{
    use HasAuditTrail, HasFactory, SoftDeletes;

    public function chunks(): HasMany;
    public function user(): BelongsTo;
}
```

#### 7.11.3. BedrockConversation Model

```php
/**
 * Enhanced conversation management with memory.
 *
 * @property int $id
 * @property string $conversation_id UUID
 * @property int|null $user_id
 * @property array $messages
 * @property string $model_used
 * @property Carbon $expires_at
 */
class BedrockConversation extends Model
{
    use HasFactory;

    public function user(): BelongsTo;
    public function addMessage(string $role, string $content): void;
    public function getMessages(): array;
}
```

### 7.12. AI Jobs (v3.7.0)

#### 7.12.1. DocumentIngestJob

```php
/**
 * Background job for document processing.
 *
 * @see D18 §6.5 Fasa Pelaksanaan - Fasa 4
 */
class DocumentIngestJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(public Document $document) {}
    public function handle(DocumentService $service): void;
    public function failed(Throwable $exception): void;
}
```

#### 7.12.2. EmbeddingJob

```php
/**
 * Background job for vector embedding generation.
 */
class EmbeddingJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(public DocumentChunk $chunk) {}
    public function handle(EmbeddingService $service): void;
}
```

#### 7.12.3. AutoReplyGenerationJob

```php
/**
 * Background job for AI-generated auto-reply drafts.
 *
 * @see D18 §10.4 Auto-Reply API
 */
class AutoReplyGenerationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public string $replyableType,
        public int $replyableId,
        public ?int $templateId = null
    ) {}
    public function handle(BedrockService $bedrock, RagService $rag): void;
}
```

### 7.13. AI Livewire Components (v3.7.0)

#### 7.13.1. BedrockChat Component

```php
/**
 * Main hybrid AI chat interface.
 *
 * @see D18 §3.1 Rajah Seni Bina Sistem
 */
class BedrockChat extends Component
{
    public string $message = '';
    public string $selectedModel = 'sonnet';
    public bool $enableInternetSearch = false;
    public array $conversationHistory = [];
    public ?string $conversationId = null;

    public function sendMessage(): void;
    public function selectModel(string $model): void;
    public function toggleInternetSearch(): void;
    public function clearConversation(): void;
    public function loadConversation(string $id): void;
    public function saveConversation(): void;
}
```

#### 7.13.2. FaqBot Component

```php
/**
 * FAQ Bot widget for guest and authenticated users.
 *
 * @see D18 §2.4 Konteks Integrasi Kritikal
 */
class FaqBot extends Component
{
    public string $query = '';
    public array $suggestions = [];
    public ?array $response = null;

    public function askQuestion(): void;
    public function selectSuggestion(string $suggestion): void;
    public function clearResponse(): void;
}
```

---

## 8. Kualiti Kod (Code Quality Attributes)

| Kualiti (ISO/IEC 25000) | Penjelasan/Penerapan                                    |
| ----------------------- | ------------------------------------------------------- |
| **Fungsionaliti**       | Semua fungsi utama diuji, kod modular                   |
| **Kebolehgunaan**       | UI konsisten, error jelas, form validation              |
| **Kebolehpeliharaan**   | Kod mudah dibaca, diubah, modular dengan services layer |
| **Kebolehpercayaan**    | Exception handling, audit trail, comprehensive tests    |
| **Efisiensi**           | Query optimized, eager loading, caching                 |
| **Keselamatan**         | CSRF, XSS prevention, RBAC, audit logging               |
| **Kebolehcapaian**      | WCAG 2.2 AA compliance, Bahasa Melayu sahaja (v3.6.0)  |

---

## 9. Strategi Ujian (Testing Strategy)

### 9.1. Jenis-Jenis Ujian & Framework

| Jenis Ujian             | Framework                  | Cakupan                            | Sasaran            |
| ----------------------- | -------------------------- | ---------------------------------- | ------------------ |
| **Unit Testing**        | PHPUnit 11                 | Fungsi individual, logic kondisi   | 80%+ coverage      |
| **Feature Testing**     | PHPUnit + Laravel TestCase | API endpoints, database, auth flow | All critical paths |
| **Livewire Testing**    | Livewire::test()           | UI components, user interactions   | CRUD operations    |
| **Browser Testing**     | Laravel Dusk               | Full browser automation            | Critical flows     |
| **E2E Testing**         | Playwright                 | Cross-browser, accessibility       | User journeys      |
| **Performance Testing** | Lighthouse, ab tool        | Response time, concurrent requests | <2s response       |
| **Security Testing**    | Manual + OWASP Zap         | CSRF, SQL injection, XSS           | D03 §8.1           |

### 9.2. Test Examples

**Feature Test (PHPUnit):**

```php
<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\HelpdeskTicket;
use App\Models\User;
use Tests\TestCase;

class HelpdeskTicketTest extends TestCase
{
    public function test_authenticated_user_can_create_ticket(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->post(route('helpdesk.store'), [
                'subject' => 'Test Ticket',
                'description' => 'Test description',
                'category_id' => 1,
            ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('helpdesk_tickets', [
            'subject' => 'Test Ticket',
            'user_id' => $user->id,
        ]);
    }
}
```

**Livewire Component Test:**

```php
<?php

declare(strict_types=1);

namespace Tests\Feature\Livewire;

use App\Livewire\Helpdesk\TicketForm;
use Livewire\Livewire;
use Tests\TestCase;

class TicketFormTest extends TestCase
{
    public function test_form_validates_required_fields(): void
    {
        Livewire::test(TicketForm::class)
            ->set('subject', '')
            ->call('submit')
            ->assertHasErrors(['subject' => 'required']);
    }
}
```

### 9.3. CI/CD Pipeline

```yaml
name: Tests
on: [push, pull_request]
jobs:
  test:
    runs-on: ubuntu-latest
    steps:
      - uses: actions/checkout@v4
      - run: composer install
      - run: php artisan test --coverage
      - run: vendor/bin/phpstan analyse app/ --level 5
      - run: vendor/bin/pint --test
      - run: npm run build
```

---

## 10. Kawalan Versi & Perubahan (Version Control)

### 10.1. Git Branching Strategy

| Cabang         | Tujuan              | Proteksi          | Merge Policy                  |
| -------------- | ------------------- | ----------------- | ----------------------------- |
| **main**       | Production release  | ✅ Locked         | Squash-merge, tagged version  |
| **develop**    | Integration branch  | ✅ Protected      | Fast-forward, CI passing      |
| **feature/\*** | Feature development | ✅ PR required    | Rebase on develop, 1 reviewer |
| **bugfix/\***  | Bug fixes           | ✅ PR required    | Same as feature               |
| **hotfix/\***  | Prod emergency      | ✅ Direct allowed | Merge to main → develop       |

### 10.2. Commit Message Convention

**Format:** `<type>(<scope>): <subject>`

| Type         | Scope       | Contoh                                            |
| ------------ | ----------- | ------------------------------------------------- |
| **feat**     | module name | `feat(helpdesk): add SLA breach notifications`    |
| **fix**      | module name | `fix(loan): correct approval workflow validation` |
| **test**     | test type   | `test(feature): add loan approval workflow tests` |
| **docs**     | section     | `docs(api): update endpoint documentation`        |
| **chore**    | tool/deps   | `chore: update phpstan level to 6`                |
| **refactor** | component   | `refactor(services): simplify email notification` |

---

## 11. Alat Kawalan Kualiti (Quality Assurance Tools)

### 11.1. Static Analysis & Linting

| Alat             | Fungsi                   | Konfigurasi           | Target          |
| ---------------- | ------------------------ | --------------------- | --------------- |
| **PHPStan**      | Static analysis          | Level 5, phpstan.neon | Zero issues     |
| **Laravel Pint** | Code formatting (PSR-12) | .pint.json            | 100% compliance |
| **Stylelint**    | CSS/Tailwind validation  | .stylelintrc          | Zero warnings   |
| **ESLint**       | JavaScript linting       | eslint.config.js      | Zero errors     |

### 11.2. Run Commands

```bash
# PHP formatting
vendor/bin/pint --dirty

# Static analysis
vendor/bin/phpstan analyse app/ --level 5

# Run tests
php artisan test

# Run specific test
php artisan test --filter=HelpdeskTicketTest

# Frontend build
npm run build

# All quality checks
composer run quality:check
npm run quality
```

### 11.3. Pre-Release QA Checklist

- [ ] All unit tests pass (`php artisan test --coverage`)
- [ ] PHPStan analysis clean (Level 5, zero issues)
- [ ] Code formatted with Pint (`vendor/bin/pint`)
- [ ] Feature tested on Chrome, Firefox, Safari
- [ ] Accessibility tested with Lighthouse (WCAG 2.2 AA)
- [ ] Database migrations tested on fresh instance
- [ ] Admin panel functionality verified (Filament CRUD)
- [ ] API endpoints tested
- [ ] Security: CSRF tokens, auth middleware, XSS prevention
- [ ] Performance: Response time <2s, DB queries optimized
- [ ] Documentation updated (code comments, README, D10-D14)
- [ ] Changelog entry added with version tag

---

## 12. Metrik Kualiti Kod (Code Quality Metrics)

| Metrik                    | Sasaran       | Alat Pengukuran |
| ------------------------- | ------------- | --------------- |
| **Cyclomatic Complexity** | Max 10/fungsi | PHPStan         |
| **Code Coverage**         | Min 80%       | PHPUnit         |
| **Technical Debt Ratio**  | Max 5%        | SonarQube       |
| **Maintainability Index** | Min 70        | PHPStan         |
| **Response Time**         | <2s           | Lighthouse      |
| **Lighthouse Score**      | ≥90           | Lighthouse      |

---

## 13. Penutup

Dokumentasi ini memberi rujukan lengkap untuk pembangun, auditor, dan pentadbir sistem Helpdesk & ICT Asset Loan BPM MOTAC dalam memahami, mengurus, dan meningkatkan kualiti kod sumber mengikut piawaian antarabangsa **ISO/IEC/IEEE 5055** (software quality), **ISO/IEC/IEEE 25000 Series (SQuaRE)**, dan **ISO/IEC/IEEE 12207** (software lifecycle).

**True Hybrid Architecture v3.5.0 Code Features:**

- Google SSO integration via `GoogleSsoService` and Laravel Socialite
- API token management via `ApiTokenService` and Laravel Sanctum
- Performance monitoring via `PerformanceMonitoringService` and Laravel Pulse
- Accessory tracking via `AccessoryTrackingService` for loan check-out/check-in
- Responsible Officer management via `ResponsibleOfficerService`
- Account linking via `AccountLinkingService` for guest-to-staff migration
- Enhanced User model with `google_id`, notification preferences, and soft deletes
- New `LoanTransactionAccessory` model for accessory discrepancy detection
- Form reference codes (PK.(S).MOTAC.07.(L1/L3)) in ticket and loan models

---

## Glosari & Rujukan (Glossary & References)

Sila rujuk **[GLOSSARY.md]** untuk istilah teknikal seperti:

- **Kod Sumber (Source Code)**: Teks kod program yang ditulis dalam bahasa pengaturcaraan
- **SQuaRE**: Systems and Software Quality Requirements and Evaluation (ISO/IEC 25000)
- **PSR-12**: PHP Standards Recommendation untuk gaya kod
- **RBAC**: Role-Based Access Control
- **SLA**: Service Level Agreement
- **WCAG**: Web Content Accessibility Guidelines

**Dokumen Rujukan:**

- **D00_SYSTEM_OVERVIEW.md** - Gambaran keseluruhan sistem
- **D01_SYSTEM_DEVELOPMENT_PLAN.md** - Pelan pembangunan sistem
- **D04_SOFTWARE_DESIGN_DOCUMENT.md** - Rekabentuk perisian
- **D11_TECHNICAL_DESIGN_DOCUMENTATION.md** - Rekabentuk teknikal terperinci

---

## Lampiran (Appendices)

### A. Piawaian PSR-12 & Laravel Best Practices

- **PSR-12**: Extended Coding Style Guide (<https://www.php-fig.org/psr/psr-12/>)
- **Laravel Coding Standards**: Rujuk Laravel Documentation (<https://laravel.com/docs>)
- **PHPStan Level**: Level 5 (strict code analysis)

### B. Checklist Code Review

- [ ] Strict typing declared
- [ ] Type hints on all parameters and return types
- [ ] PHPDoc blocks on classes and complex methods
- [ ] Relationships have proper type hints
- [ ] No N+1 query issues (eager loading used)
- [ ] Form Request validation used (not inline)
- [ ] Authorization via policies
- [ ] Audit trail implemented where required
- [ ] Tests written for new functionality

---

**Dokumen ini mematuhi piawaian ISO/IEC/IEEE 5055:2021 (Software Quality), ISO/IEC 25000:2014 (SQuaRE), dan ISO/IEC/IEEE 12207:2017 (Software Lifecycle Processes).**
