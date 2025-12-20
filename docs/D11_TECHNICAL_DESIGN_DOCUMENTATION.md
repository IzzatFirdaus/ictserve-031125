# Dokumentasi Rekabentuk Teknikal (Technical Design Documentation)

**Sistem ICTServe**  
**Versi:** 3.6.1 (SemVer)  
**Tarikh Kemaskini:** 17 Disember 2025  
**Status:** Aktif  
**Klasifikasi:** Terhad - Dalaman MOTAC  
**Penulis:** Pasukan Pembangunan BPM MOTAC  
**Standard Rujukan:** IEEE 1016:2009 (Huraian Reka Bentuk Perisian), ISO/IEC/IEEE 2651x series, ISO 9001, ISO/IEC/IEEE 12207, OWASP Transport Security, TLS 1.3, AES-256

---

## Maklumat Dokumen (Document Information)

| Atribut              | Nilai                                                       |
| -------------------- | ----------------------------------------------------------- |
| **Versi**            | 3.6.1                                                       |
| **Tarikh Kemaskini** | 17 Disember 2025                                            |
| **Status**           | Aktif                                                       |
| **Klasifikasi**      | Terhad - Dalaman MOTAC                                      |
| **Pematuhi**         | IEEE 1016, ISO/IEC/IEEE 2651x series, ISO 9001, ISO/IEC/IEEE 12207 |
| **Bahasa**           | Bahasa Melayu (utama), istilah teknikal English bila perlu  |

> Notis Penggunaan Dalaman: Reka bentuk teknikal ini adalah khusus untuk sistem dalaman MOTAC; bukan untuk aplikasi awam.

---

## Sejarah Perubahan (Changelog)

| Versi | Tarikh           | Perubahan                                                                                                                                                                                                                                                                          | Penulis     |
| ----- | ---------------- | ---------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------- | ----------- |
| 3.6.1 | 17 Disember 2025 | Kemaskini teknologi stack: Laravel 12.43.1, Livewire 3.7.3, Laravel Pulse 1.4.7, Laravel Reverb 1.6.3, Laravel Sanctum 4.2.1, Laravel Socialite 5.24.0, PHPUnit 11.5.46, Tailwind CSS 4.1.18, Laravel MCP 0.3.4, Laravel Prompts 0.3.8, Larastan 3.8.1, Laravel Pint 1.26.0, Laravel Telescope 5.16.0, Laravel Horizon 5.41.0. Penyelarasan dengan D00-D18 v3.6.1. | Pasukan BPM |
| 1.0.0 | September 2025   | Versi awal dokumentasi rekabentuk teknikal                                                                                                                                                                                                                                         | Pasukan BPM |
| 2.0.0 | 17 Oktober 2025  | Penyeragaman mengikut D00-D14, SemVer, cross-reference                                                                                                                                                                                                                             | Pasukan BPM |
| 2.1.0 | 19 Oktober 2025  | Tambah §7a Internationalization & Language Support                                                                                                                                                                                                                                 | Pasukan BPM |
| 3.0.0 | 29 November 2025 | Kemaskini Laravel 12, Filament 4, Livewire 3, WebSocket, security middleware                                                                                                                                                                                                       | Pasukan BPM |
| 3.1.0 | 29 November 2025 | Hapus staff/approver roles; klarifikasi Guest-First architecture                                                                                                                                                                                                                   | Pasukan BPM |
| 3.3.0 | 29 November 2025 | Penjajaran penuh Guest-First: Hapus staff/approver middleware aliases, kemaskini RBAC                                                                                                                                                                                              | Pasukan BPM |
| 3.4.0 | 29 November 2025 | Hybrid Architecture: Re-introduced staff role with view-own-history, edit-profile capabilities. Penyelarasan dengan D00/D02/D03/D04/D09 v3.4.0.                                                                                                                                    | Pasukan BPM |
| 3.5.0 | 1 Disember 2025  | True Hybrid Architecture v3.5.0: Laravel Pulse (performance monitoring), Laravel Sanctum (API authentication), Google SSO (optional), Responsible Officer, Accessory Tracking, Form Reference Codes, MOTAC Branding. Kemaskini RBAC dan security. Penyelarasan dengan D00-D10 v3.5.0. | Pasukan BPM |

---

## Rujukan Dokumen Berkaitan (Related Document References)

- **[D00_SYSTEM_OVERVIEW.md]** - Ringkasan Sistem
- **[D01_SYSTEM_DEVELOPMENT_PLAN.md]** - Pelan Pembangunan Sistem
- **[D04_SOFTWARE_DESIGN_DOCUMENT.md]** - Rekabentuk Perisian (high-level design)
- **[D09_DATABASE_DOCUMENTATION.md]** - Dokumentasi Pangkalan Data
- **[D10_SOURCE_CODE_DOCUMENTATION.md]** - Dokumentasi Kod Sumber
- **[D16_BROADCASTING_SETUP.md]** - Konfigurasi WebSocket & Real-time
- **[GLOSSARY.md]** - Glosari Istilah Sistem

---

## 1. Tujuan Dokumen (Purpose)

Dokumen ini merangkum rekabentuk teknikal sistem **Helpdesk & ICT Asset Loan BPM MOTAC** termasuk senibina, modul, integrasi, spesifikasi data, keselamatan, dan kawalan kualiti. Dokumentasi ini mematuhi piawaian **IEEE 1016** (software design), **ISO/IEC/IEEE 2651x series** (software and documentation engineering), **ISO 9001** (quality management), dan **ISO/IEC/IEEE 12207** (software lifecycle processes).

---

## 2. Skop (Scope)

- Meliputi semua aspek teknikal sistem: backend, frontend, database, API, authentication, authorization, audit trail, integrasi dalaman/luaran, real-time features, dan deployment.
- Pengguna: Staf MOTAC, Pegawai BPM, Ketua Bahagian (Grade 41+), Admin BPM.

---

## 3. Teknologi Teras (Core Technology Stack)

### 3.1. Backend Stack

| Komponen              | Versi   | Fungsi                            |
| --------------------- | ------- | --------------------------------- |
| **PHP**               | 8.2.12  | Bahasa pengaturcaraan utama       |
| **Laravel**           | 12.43.1 | Framework aplikasi web            |
| **Filament**          | 4.3.1   | Admin panel framework             |
| **Livewire**          | 3.7.3   | Server-driven UI components       |
| **Livewire Volt**     | 1.10.1  | Single-file Livewire components   |
| **Laravel Reverb**    | 1.6.3   | WebSocket server untuk real-time  |
| **Spatie Permission** | 6.23    | Role-based access control         |
| **Laravel Auditing**  | 14.x    | Field-level audit trail (owen-it) |
| **Activity Log**      | 4.x     | User activity logging (spatie)    |
| **Laravel Pulse**     | 1.4.7   | Performance monitoring (admin/superuser) |
| **Laravel Sanctum**   | 4.2.1   | API token authentication           |
| **Laravel Socialite** | 5.24.0  | Google OAuth SSO (opsyen)          |
| **Laravel Telescope** | 5.16.0  | System debugging (superuser only)  |
| **Laravel Horizon**   | 5.41.0  | Redis queue monitoring & management |
| **Ollama**            | Latest  | Local LLM server (FAQ, RAG)       |
| **AWS Bedrock**       | Latest  | Cloud AI (Claude models)          |

### 3.2. Frontend Stack

| Komponen         | Versi  | Fungsi                           |
| ---------------- | ------ | -------------------------------- |
| **Tailwind CSS** | 4.1.18 | Utility-first CSS framework      |
| **Alpine.js**    | 3.x    | Lightweight JavaScript framework |
| **Laravel Echo** | 2.2.6  | WebSocket client                 |
| **Pusher JS**    | 8.x    | WebSocket protocol               |
| **Vite**         | 7.0.7  | Asset bundling                   |

### 3.3. Database & Storage

| Komponen   | Versi | Fungsi                    |
| ---------- | ----- | ------------------------- |
| **MySQL**  | 8.x   | Production database       |
| **SQLite** | -     | Development/testing       |
| **Redis**  | 7.x   | Caching, queue, Pulse backend |

### 3.4. Development Tools

| Komponen         | Versi   | Fungsi                   |
| ---------------- | ------- | ------------------------ |
| **PHPUnit**      | 11.5.46 | Testing framework        |
| **Larastan**     | 3.8.1   | Static analysis          |
| **Laravel Pint** | 1.26.0  | Code formatting (PSR-12) |
| **Playwright**   | 1.57.0  | E2E testing              |

---

## 4. Senibina Sistem (System Architecture)

### 4.1. Laravel 12 Architecture

Laravel 12 menggunakan struktur fail yang dipermudahkan:

- **Tiada Kernel HTTP dalam `app/` (Laravel 12)** - Middleware didaftarkan dalam `bootstrap/app.php`
- **Tiada Kernel Console dalam `app/` (Laravel 12)** - Commands auto-register dari `app/Console/Commands/`
- **Service Providers** - Didaftarkan dalam `bootstrap/providers.php`

### 4.2. Application Bootstrap (`bootstrap/app.php`)

```php
return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        apiPrefix: 'api',
        commands: __DIR__ . '/../routes/console.php',
        channels: __DIR__ . '/../routes/channels.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Global middleware
        $middleware->append(SecurityHeadersMiddleware::class);
        $middleware->append(SetLocaleMiddleware::class);
        $middleware->append(SessionTimeoutMiddleware::class);

        // Middleware aliases
        $middleware->alias([
            'role' => RoleMiddleware::class,
            'permission' => PermissionMiddleware::class,
            'two-factor' => TwoFactorVerify::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // Exception handling
    })->create();
```

### 4.3. Lapisan Sistem (System Layers)

```text
┌─────────────────────────────────────────────────────────┐
│                  PRESENTATION LAYER                      │
│  Blade + Livewire 3 + Volt + Filament 4 + Tailwind 4    │
└─────────────────────────────────────────────────────────┘
                           │
┌─────────────────────────────────────────────────────────┐
│                  APPLICATION LAYER                       │
│  Controllers + Services + Jobs + Middleware + Policies   │
└─────────────────────────────────────────────────────────┘
                           │
┌─────────────────────────────────────────────────────────┐
│                  INTEGRATION LAYER                       │
│  RESTful API + WebSocket (Reverb) + Email + Audit Trail │
└─────────────────────────────────────────────────────────┘
                           │
┌─────────────────────────────────────────────────────────┐
│                     DATA LAYER                           │
│  Eloquent ORM + MySQL + Redis Cache + File Storage       │
└─────────────────────────────────────────────────────────┘
```

---

## 5. Modul Utama (Main Modules)

### 5.1. Helpdesk Ticketing Module

**Fungsi**: Borang aduan kerosakan, pengurusan tiket, SLA tracking, notifikasi, audit trail

**Komponen Utama**:

| Komponen                        | Lokasi                          | Fungsi                        |
| ------------------------------- | ------------------------------- | ----------------------------- |
| `HelpdeskTicket` Model          | `app/Models/HelpdeskTicket.php` | Data model dengan hybrid arch |
| `HelpdeskTicketResource`        | `app/Filament/Resources/`       | Filament CRUD admin           |
| `HelpdeskTicketPolicy`          | `app/Policies/`                 | Authorization rules           |
| `SLAManagementService`          | `app/Services/`                 | SLA calculation & tracking    |
| `TicketStatusTransitionService` | `app/Services/`                 | Status workflow management    |

**Hybrid Architecture Support**:

- Guest submissions (tanpa `user_id`)
- Authenticated submissions (dengan `user_id`)
- Ticket number format: `HD[YYYY][000001-999999]`

### 5.2. ICT Asset Loan Module

**Fungsi**: Permohonan pinjaman, workflow kelulusan email-based, pengeluaran/pemulangan aset

**Komponen Utama**:

| Komponen                  | Lokasi                           | Fungsi                     |
| ------------------------- | -------------------------------- | -------------------------- |
| `LoanApplication` Model   | `app/Models/LoanApplication.php` | Data model dengan approval |
| `LoanApplicationResource` | `app/Filament/Resources/`        | Filament CRUD admin        |
| `LoanApplicationPolicy`   | `app/Policies/`                  | Authorization rules        |
| `LoanApplicationService`  | `app/Services/`                  | Business logic             |
| `DualApprovalService`     | `app/Services/`                  | Email + portal approval    |

**Email-Based Approval**:

- Approvers (Grade 41+) boleh approve/reject via email link
- Signed tokens untuk security
- No login required untuk approval action

### 5.3. Asset Management Module

**Fungsi**: CRUD aset, tracking status, depreciation, maintenance

**Komponen Utama**:

| Komponen              | Lokasi                 | Fungsi                   |
| --------------------- | ---------------------- | ------------------------ |
| `Asset` Model         | `app/Models/Asset.php` | Asset data dengan status |
| `AssetCategory` Model | `app/Models/`          | Asset categorization     |
| `AssetTransaction`    | `app/Models/`          | Checkout/return tracking |
| `AssetMaintenance`    | `app/Models/`          | Maintenance records      |

### 5.4. Cross-Module Integration

**Fungsi**: Integrasi antara Helpdesk dan Asset Loan modules

**Features**:

- Asset-ticket linking untuk damage reporting
- Auto-create maintenance ticket dari damaged asset return
- Unified analytics dashboard

---

## 6. Authentication & Authorization

### 6.1. Authentication Stack

| Komponen               | Fungsi                     |
| ---------------------- | -------------------------- |
| **Laravel Breeze**     | Authentication scaffolding |
| **Two-Factor Auth**    | Optional 2FA via TOTP      |
| **Session Management** | Redis-backed sessions      |
| **Remember Me**        | Persistent login tokens    |

### 6.2. Role-Based Access Control (RBAC) - True Hybrid v3.5.0

**Roles** (via Spatie Permission):

| Role          | Level | Capabilities                                                                                                 |
| ------------- | ----- | ------------------------------------------------------------------------------------------------------------ |
| **staff**     | 0     | View own submission history, edit profile, access Dashboard, submit as authenticated, link guest submissions |
| **admin**     | 1     | Manage tickets, assets, users, view all data, assign tickets                                                 |
| **superuser** | 2     | Full system access, configuration, audit logs, user management, system config, Laravel Telescope             |

**Nota True Hybrid Architecture v3.5.0**:

- **Self-Registration**: Staff MOTAC boleh self-register dengan email @motac.gov.my
- **Email Verification**: WAJIB sebelum akses Dashboard/Profile
- **Flexible Login**: Email penuh (`user@motac.gov.my`) ATAU username pendek (`user`)
- **Optional Account Linking**: Selepas login pertama, papar prompt untuk link submissions sedia ada
- **Tiada LDAP**: SSO Google Workspace adalah opsyen (Laravel Socialite) selain login biasa

Admin dan superuser memerlukan authentication untuk pengurusan sistem. Approvers (Grade 41+) meluluskan permohonan melalui signed email tokens, bukan login sistem.

**Staff Capabilities** (role='staff'):

| Capability                | Description                                                                        | Implementation                                         |
| ------------------------- | ---------------------------------------------------------------------------------- | ------------------------------------------------------ |
| `view-own-history`        | Lihat tiket/permohonan sendiri sahaja                                              | Policy: `WHERE user_id = Auth::id()`                   |
| `edit-profile`            | Kemaskini maklumat peribadi (name, phone, division_id, grade_id, locale, notify*\*) | Route: `/profile`, Policy: `update(Auth::user())`      |
| `access-dashboard`        | Akses Dashboard Staf dengan statistik peribadi                                     | Route: `/dashboard`, Middleware: `auth,verified,staff` |
| `submit-as-authenticated` | Hantar tiket/permohonan dengan user_id linkage (auto-fill forms dari profile)      | Form: Pre-populate dari Auth::user() attributes        |
| `link-guest-submissions`  | Link submissions tetamu sedia ada kepada akaun                                     | Route: `/dashboard/link-submissions`                   |

**Query Pattern untuk Staff**:

```php
// Staff hanya boleh lihat submission sendiri
$tickets = HelpdeskTicket::where('user_id', Auth::id())->get();
$loans = LoanApplication::where('user_id', Auth::id())->get();

// Admin/Superuser boleh lihat semua
$tickets = HelpdeskTicket::all();
$loans = LoanApplication::all();
```

**Admin Capabilities** (role='admin'):

- `manage-tickets`: Full CRUD on helpdesk_tickets
- `manage-loans`: Full CRUD on loan_applications
- `manage-assets`: Full CRUD on assets
- `assign-tickets`: Assign tickets to staff
- `view-all-data`: Access all submissions regardless of user_id

**Superuser Capabilities** (role='superuser'):

- All admin capabilities PLUS:
- `manage-users`: Create/update/delete user accounts
- `manage-roles`: Assign roles and permissions
- `view-audit-logs`: Access full audit trail (both owen-it and spatie)
- `system-configuration`: Update system settings
- `access-telescope`: Full Laravel Telescope access (unrestricted)

### 6.3. Middleware Stack

```php
// Global Middleware (bootstrap/app.php)
SecurityHeadersMiddleware::class,    // Security headers
SetLocaleMiddleware::class,          // Language detection
SessionTimeoutMiddleware::class,     // Session timeout handling
ImpersonationMiddleware::class,      // Admin impersonation

// Route Middleware Aliases
'role' => RoleMiddleware::class,
'permission' => PermissionMiddleware::class,
'two-factor' => TwoFactorVerify::class,
'ip.blocking' => IpBlockingMiddleware::class,
'guest.ratelimit' => GuestFormRateLimiter::class,
'auth.optional' => OptionalAuthMiddleware::class,  // Hybrid: allows Auth::check() OR Guest
'staff' => StaffMiddleware::class,                // Staff-only routes (Dashboard, Profile)
'verified' => EnsureEmailIsVerified::class,       // Email verification required
'telescope' => TelescopeAccessMiddleware::class,  // Superuser only
```

**Nota True Hybrid Architecture v3.5.0**:

- Middleware `auth.optional` membenarkan akses untuk authenticated users DAN guests
- Middleware `staff` memerlukan authentication dengan role='staff' (Dashboard, Profile)
- Admin/superuser authenticate untuk pengurusan sistem
- Staf boleh submit sebagai guests ATAU authenticated users (jika login)

### 6.4. Policy-Based Authorization (Hybrid)

```php
// Example: HelpdeskTicketPolicy (Hybrid Architecture)
public function view(?User $user, HelpdeskTicket $ticket): bool
{
    // Admin/Superuser: Full access
    if ($user && $user->hasRole(['admin', 'superuser'])) {
        return true;
    }

    // Authenticated user: Own tickets
    if ($user && $user->id === $ticket->user_id) {
        return true;
    }

    // Guest: Validate status token from query parameter
    $statusToken = request()->query('status_token');
    if ($statusToken && Hash::check($ticket->uuid . $ticket->submitter_email, $statusToken)) {
        return true;
    }

    return false;
}

public function update(?User $user, HelpdeskTicket $ticket): bool
{
    // Only admin/superuser or assigned staff can update
    return $user && (
        $user->hasRole(['admin', 'superuser'])
        || $user->id === $ticket->assigned_to_user
    );
}
```

**Nota**: Policy menerima `?User $user` (nullable) untuk sokongan hybrid. Guests validate via status token; authenticated users via `user_id` match.

### 6.5. Dual Audit System Architecture

#### Package 1: owen-it/laravel-auditing v14.x (COMPLIANCE)

- **Table**: `audits`
- **Purpose**: Field-level change tracking untuk PDPA compliance
- **Models**: `Auditable` trait pada `HelpdeskTicket`, `LoanApplication`, `Asset`, `User`
- **Events**: created, updated, deleted
- **Retention**: 7 years

#### Package 2: spatie/laravel-activitylog v4.x (OPERATIONS)

- **Table**: `activity_log`
- **Purpose**: User activity tracking untuk dashboard dan reports
- **Events**: login, logout, form submissions, approvals
- **Use cases**: User activity feed, Filament widgets

#### Package 3: Laravel Telescope v5.x (DEBUGGING)

- **Access**: Superuser ONLY (via `TelescopeAccessMiddleware`)
- **Route**: `/telescope`
- **Features**: ALL enabled (requests, queries, jobs, exceptions)
- **Retention**: 7 days

#### Package 4: Laravel Pulse v1.3.0 (PERFORMANCE) - v3.5.0

- **Tables**: `pulse_aggregates`, `pulse_entries`, `pulse_values`
- **Access**: Admin and Superuser
- **Route**: `/pulse`
- **Features**: Slow queries, queue metrics, server health, cache stats
- **Retention**: 7 days (auto-pruned)

```php
// config/telescope.php
'middleware' => [
    'web',
    Authorize::class,
],

// TelescopeServiceProvider
Gate::define('viewTelescope', function ($user) {
    return $user->isSuperuser();
});
```

---

## 7. Real-Time Features (WebSocket)

### 7.1. Laravel Reverb Configuration

**Server**: Laravel Reverb 1.6.3 (native Laravel WebSocket server)

```php
// config/broadcasting.php
'reverb' => [
    'driver' => 'reverb',
    'key' => env('REVERB_APP_KEY'),
    'secret' => env('REVERB_APP_SECRET'),
    'app_id' => env('REVERB_APP_ID'),
    'options' => [
        'host' => env('REVERB_HOST', 'localhost'),
        'port' => env('REVERB_PORT', 8080),
        'scheme' => env('REVERB_SCHEME', 'http'),
    ],
],
```

### 7.2. Broadcasting Channels

```php
// routes/channels.php
Broadcast::channel('tickets.{ticketId}', function ($user, $ticketId) {
    return $user->can('view', HelpdeskTicket::find($ticketId));
});

Broadcast::channel('loans.{loanId}', function ($user, $loanId) {
    return $user->can('view', LoanApplication::find($loanId));
});

Broadcast::channel('user.{userId}', function ($user, $userId) {
    return (int) $user->id === (int) $userId;
});
```

### 7.3. Event Broadcasting

```php
// Example: TicketStatusChanged Event
class TicketStatusChanged implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public HelpdeskTicket $ticket,
        public string $oldStatus,
        public string $newStatus,
    ) {}

    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('tickets.' . $this->ticket->id),
            new PrivateChannel('user.' . $this->ticket->user_id),
        ];
    }
}
```

---

## 8. Internationalization & Language Support

### 8.1. Language Architecture (Bahasa Melayu Sahaja, v3.6.0)

**Supported Locales**: `ms` (Bahasa Melayu), `en` (English)
**Default Locale**: `en` (configurable via `APP_LOCALE`)
**Configuration**: `config/app.php`

```php
'locale' => env('APP_LOCALE', 'en'),
'fallback_locale' => env('APP_FALLBACK_LOCALE', 'en'),
'supported_locales' => explode(',', env('SUPPORTED_LOCALES', 'ms,en')),
```

### 8.2. Locale Resolution Priority

**Middleware**: `SetLocaleMiddleware` (registered in `bootstrap/app.php`)

**Priority Order**:

1. **Session** (`locale` key)
2. **Cookie** (`locale` cookie, 12-month expiry)
3. **URL Query Parameter** (`?lang=ms|en`)
4. **Browser Detection** (`Accept-Language` header)
5. **Fallback** (`config('app.locale')`)

### 8.3. Language Switcher Component (DILUMPUHKAN v3.6.0)

**Framework**: Livewire 3.x
**Location**: `app/Http/Controllers/LanguageController.php` (route: `routes/web.php` → `/change-locale/{locale}`)

**Features**:

- Dropdown menu with flag icons
- Persists to session, cookie, and user profile
- WCAG 2.2 AA accessible (keyboard navigation, screen reader support)

---

## 9. Keselamatan Sistem (System Security)

### 9.1. Security Middleware

| Middleware                  | Fungsi                               |
| --------------------------- | ------------------------------------ |
| `SecurityHeadersMiddleware` | CSP, X-Frame-Options, HSTS headers   |
| `IpBlockingMiddleware`      | Block malicious IPs                  |
| `GuestFormRateLimiter`      | Rate limit guest form submissions    |
| `TwoFactorVerify`           | Enforce 2FA for sensitive operations |
| `SessionTimeoutMiddleware`  | Auto-logout after inactivity         |

### 9.2. Security Headers

```php
// SecurityHeadersMiddleware
$response->headers->set('X-Content-Type-Options', 'nosniff');
$response->headers->set('X-Frame-Options', 'SAMEORIGIN');
$response->headers->set('X-XSS-Protection', '1; mode=block');
$response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');
$response->headers->set('Permissions-Policy', 'geolocation=(), microphone=()');
```

### 9.3. Encryption & Key Management

| Konteks                     | Algoritma | Key Size | Rotation       |
| --------------------------- | --------- | -------- | -------------- |
| **Data at Rest (Database)** | AES-GCM   | 256-bit  | Quarterly      |
| **Data in Transit (HTTPS)** | TLS 1.3   | 256-bit  | Auto (cert)    |
| **Password Hashing**        | bcrypt    | Dynamic  | Per change     |
| **API Tokens (Sanctum)**    | SHA-256   | 256-bit  | 30-day expiry  |
| **Status Tokens**           | SHA-512   | 512-bit  | 72-hour expiry |
| **Google OAuth**            | OAuth 2.0 | -        | Per session    |

### 9.4. Audit Trail

**Package**: `owen-it/laravel-auditing` v14.x

```php
// Model with audit trail
class HelpdeskTicket extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;
    use HasAuditTrail;

    protected $auditInclude = [
        'status', 'priority', 'assigned_to_user',
    ];
}
```

---

## 10. Email Notification System

### 10.1. Email Architecture

| Komponen                   | Lokasi          | Fungsi                     |
| -------------------------- | --------------- | -------------------------- |
| `EmailNotificationService` | `app/Services/` | Centralized email dispatch |
| `EmailLog` Model           | `app/Models/`   | Email delivery tracking    |
| `EmailTemplate` Model      | `app/Models/`   | Configurable templates     |
| Mailable Classes           | `app/Mail/`     | Email content generation   |

### 10.2. Queue-Based Email

```php
// Jobs for async email
SendTicketCreatedEmail::class,
SendLoanApprovedEmail::class,
SendAssetOverdueEmail::class,
RetryFailedEmail::class,
```

### 10.3. Email Logging

```php
// EmailLog tracks all outgoing emails
$emailLog = EmailLog::create([
    'mailable_class' => get_class($mailable),
    'recipient' => $recipient,
    'subject' => $subject,
    'status' => 'pending',
    'sent_at' => null,
]);
```

---

## 11. Services Layer

### 11.1. Core Services

| Service                           | Fungsi                                    |
| --------------------------------- | ----------------------------------------- |
| `EmailNotificationService`        | Centralized email dispatch                |
| `SLAManagementService`            | SLA calculation dan breach detection      |
| `CrossModuleIntegrationService`   | Helpdesk-Asset Loan integration           |
| `DualApprovalService`             | Email + portal approval workflow          |
| `AssetAvailabilityService`        | Asset booking conflict detection          |
| `NotificationService`             | Multi-channel notifications               |
| `AuditExportService`              | Audit log export functionality            |
| `GoogleSsoService` (v3.5.0)       | Google Workspace OAuth 2.0 authentication |
| `ApiTokenService` (v3.5.0)        | Laravel Sanctum API token management      |
| `PerformanceMonitoringService` (v3.5.0) | Laravel Pulse performance metrics   |
| `AccessoryTrackingService` (v3.5.0)     | Loan accessory check-out/check-in   |
| `ResponsibleOfficerService` (v3.5.0)    | Responsible Officer management      |
| `AccountLinkingService` (v3.5.0)        | Guest-to-account linking            |

### 11.2. Service Pattern

```php
// Example: SLAManagementService
class SLAManagementService
{
    public function calculateDueDates(HelpdeskTicket $ticket): void
    {
        $category = $ticket->category;
        $ticket->sla_response_due_at = now()->addHours($category->sla_response_hours);
        $ticket->sla_resolution_due_at = now()->addHours($category->sla_resolution_hours);
        $ticket->save();
    }

    public function checkBreaches(): Collection
    {
        return HelpdeskTicket::query()
            ->whereNull('sla_breached_at')
            ->where('sla_resolution_due_at', '<', now())
            ->get();
    }
}
```

---

## 12. AI Infrastructure (Cloud Hybrid Architecture)

> **Trace:** D18 v1.0.0 (Cloud Hybrid AI Architecture), D03 §5.9 (AI Requirements), D04 §12 (AI Integration Layer)

### 12.1. AI Infrastructure Overview

ICTServe mengintegrasikan **True Hybrid AI Architecture** yang menggabungkan Ollama (local LLM) dengan AWS Bedrock (cloud AI) untuk memberikan pengalaman AI yang optimum dengan penghalaan pintar berdasarkan jenis pertanyaan.

**Infrastructure Components:**

| Komponen | Lokasi | Fungsi | Status |
|----------|--------|--------|--------|
| **Ollama Server** | localhost:11434 | Local LLM untuk FAQ dan data sovereignty | ✅ Active |
| **AWS Bedrock** | us-east-1 | Cloud AI untuk complex reasoning | ✅ Active |
| **Model Router** | `app/Services/ModelRouter.php` | Smart query routing | ✅ Active |
| **Vector Database** | MySQL + JSON columns | Embeddings storage | ✅ Active |
| **MCP Server** | Port 3000 | AI tool integration | ✅ Active |

### 12.2. Ollama Infrastructure

**Server Configuration:**

```bash
# Ollama Service Configuration
OLLAMA_HOST=http://127.0.0.1:11434
OLLAMA_MODEL=llama3.1:8b-instruct-q4_K_M
OLLAMA_EMBEDDING_MODEL=nomic-embed-text
OLLAMA_KEEP_ALIVE=5m
OLLAMA_MAX_LOADED_MODELS=1
```

**System Requirements:**

| Resource | Minimum | Recommended | Purpose |
|----------|---------|-------------|---------|
| **CPU** | 4 cores | 8 cores | Model inference |
| **RAM** | 16GB | 32GB | Model loading (8-16GB per model) |
| **Storage** | 100GB SSD | 500GB NVMe | Model storage |
| **Network** | 100 Mbps | 1 Gbps | API communication |

**Health Monitoring:**

```php
// Ollama Health Check Service
class OllamaHealthService
{
    public function checkHealth(): array
    {
        try {
            $response = Http::timeout(5)->get($this->config['url'] . '/api/tags');
            return [
                'status' => 'up',
                'models' => $response->json()['models'] ?? [],
                'response_time' => $response->transferStats->getTransferTime(),
                'last_check' => now()
            ];
        } catch (\Throwable $e) {
            return ['status' => 'down', 'error' => $e->getMessage()];
        }
    }
}
```

### 12.3. AWS Bedrock Infrastructure

**Model Configuration:**

| Model | Use Case | Speed | Cost | Rate Limit |
|-------|----------|-------|------|------------|
| **Claude Opus 4.5** | Complex reasoning | Slow | High | 10 RPM, 20K TPM |
| **Claude Sonnet 4.5** | Balanced tasks | Medium | Medium | 20 RPM, 40K TPM |
| **Claude Haiku 4.5** | Quick responses | Fast | Low | 50 RPM, 100K TPM |
| **Nova Pro** | Multimodal | Medium | Medium | 40 RPM, 60K TPM |
| **Nova Lite** | Fast text | Fast | Low | 80 RPM, 120K TPM |
| **Nova Micro** | Ultra-fast | Very Fast | Very Low | 100 RPM, 150K TPM |

**Authentication & Security:**

```php
// AWS Bedrock Configuration
AWS_ACCESS_KEY_ID=your_access_key
AWS_SECRET_ACCESS_KEY=your_secret_key
AWS_BEDROCK_REGION=us-east-1
AWS_BEDROCK_MODEL_ID=us.anthropic.claude-sonnet-4-5-20250929-v1:0

// Data Residency Compliance
BEDROCK_ENFORCE_MALAYSIA_RESIDENCY=true
BEDROCK_ALLOWED_REGIONS=ap-southeast-1
```

### 12.4. Model Routing Infrastructure

**Query Analysis Pipeline:**

```php
class ModelRouter
{
    private array $faqKeywords = [
        'tiket', 'helpdesk', 'pinjaman', 'aset', 'status',
        'permohonan', 'sistem', 'ictserve', 'motac', 'bpm'
    ];
    
    private array $complexKeywords = [
        'analisis', 'bandingkan', 'jelaskan', 'mengapa',
        'bagaimana jika', 'strategi', 'cadangan'
    ];
    
    public function routeRequest(string $query, array $context = []): array
    {
        $queryType = $this->analyzeQuery($query);
        
        return match($queryType) {
            'faq_specific' => $this->routeToOllama($query, $context),
            'complex_reasoning' => $this->routeToBedrock($query, $context),
            'hybrid' => $this->routeToHybrid($query, $context),
            default => $this->routeToFallback($query, $context)
        };
    }
}
```

**Fallback Strategy:**

```text
┌─────────────────────────────────────────────────────────┐
│                   Fallback Chain                         │
├─────────────────────────────────────────────────────────┤
│                                                          │
│  FAQ Query:                                              │
│  ┌──────────┐    ┌──────────┐    ┌──────────┐          │
│  │  Ollama  │───▶│ Bedrock  │───▶│  Static  │          │
│  │   RAG    │    │ Fallback │    │   FAQ    │          │
│  └──────────┘    └──────────┘    └──────────┘          │
│                                                          │
│  Complex Query:                                          │
│  ┌──────────┐    ┌──────────┐                           │
│  │ Bedrock  │───▶│  Error   │                           │
│  │  Direct  │    │ Message  │                           │
│  └──────────┘    └──────────┘                           │
│                                                          │
└─────────────────────────────────────────────────────────┘
```

### 12.5. Data Infrastructure

**Enhanced Database Schema:**

```sql
-- AI-specific tables
CREATE TABLE bedrock_conversations (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NULL,
    title VARCHAR(255) NOT NULL,
    messages JSON NOT NULL,
    model_used VARCHAR(100) NOT NULL,
    total_tokens INT UNSIGNED DEFAULT 0,
    cost_estimate DECIMAL(10,4) DEFAULT 0.0000,
    metadata JSON NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    INDEX idx_user_id (user_id),
    INDEX idx_model_used (model_used),
    INDEX idx_created_at (created_at)
);

CREATE TABLE message_logs (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    conversation_id BIGINT UNSIGNED NULL,
    user_id BIGINT UNSIGNED NULL,
    query TEXT NOT NULL,
    response TEXT NOT NULL,
    model_used VARCHAR(100) NOT NULL,
    confidence_score DECIMAL(3,2) NULL,
    processing_time DECIMAL(8,3) NOT NULL,
    tokens_used INT UNSIGNED DEFAULT 0,
    cost DECIMAL(10,4) DEFAULT 0.0000,
    source ENUM('ollama', 'bedrock', 'hybrid') NOT NULL,
    metadata JSON NULL,
    created_at TIMESTAMP NULL,
    INDEX idx_conversation_id (conversation_id),
    INDEX idx_user_id (user_id),
    INDEX idx_source (source),
    INDEX idx_created_at (created_at)
);

CREATE TABLE document_chunks (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    document_id BIGINT UNSIGNED NOT NULL,
    chunk_text TEXT NOT NULL,
    chunk_index INT UNSIGNED NOT NULL,
    embedding JSON NULL,
    metadata JSON NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    INDEX idx_document_id (document_id),
    INDEX idx_chunk_index (chunk_index)
);
```

### 12.6. Performance & Monitoring

**AI Service Metrics:**

| Metric | Target | Monitoring Tool | Alert Threshold |
|--------|--------|-----------------|-----------------|
| **Response Time** | <5s | Laravel Pulse | >10s |
| **Ollama Uptime** | >99% | Health Check | <95% |
| **Bedrock Success Rate** | >95% | CloudWatch | <90% |
| **Cost per Query** | <$0.01 | Custom Dashboard | >$0.05 |
| **Cache Hit Rate** | >80% | Redis Metrics | <70% |

**Health Check Endpoints:**

```php
// routes/api.php
Route::get('/health/ai', function () {
    $ollama = app(OllamaHealthService::class)->check();
    $bedrock = app(BedrockHealthService::class)->check();
    
    return response()->json([
        'status' => ($ollama['status'] === 'up' && $bedrock['status'] === 'up') ? 'healthy' : 'degraded',
        'services' => compact('ollama', 'bedrock'),
        'timestamp' => now()->toISOString()
    ]);
});
```

### 12.7. Security & Compliance

**Data Classification:**

```php
class DataClassificationService
{
    public function classifyForProcessing(string $content): string
    {
        // PII Detection
        $piiResult = app(PIIDetectionService::class)->detectAndSanitize($content);
        if ($piiResult['has_pii']) {
            return 'local_only'; // Ollama only
        }
        
        // Sensitive keyword detection
        $sensitiveKeywords = ['confidential', 'rahsia', 'sulit', 'internal'];
        foreach ($sensitiveKeywords as $keyword) {
            if (stripos($content, $keyword) !== false) {
                return 'local_only';
            }
        }
        
        return 'allow_cloud'; // Can use Bedrock
    }
}
```

**Audit Trail Integration:**

```php
// AI operations audit
class AIAuditService
{
    public function logAIInteraction(array $data): void
    {
        // Owen-it audit (compliance)
        MessageLog::create($data);
        
        // Spatie activity log (operations)
        activity()
            ->causedBy(auth()->user())
            ->withProperties($data)
            ->log('AI interaction');
    }
}
```

### 12.8. Deployment & Infrastructure

**Docker Configuration:**

```yaml
# docker-compose.yml
services:
  ollama:
    image: ollama/ollama:latest
    ports:
      - "11434:11434"
    volumes:
      - ollama_data:/root/.ollama
    environment:
      - OLLAMA_KEEP_ALIVE=5m
      - OLLAMA_MAX_LOADED_MODELS=1
    restart: unless-stopped
    
  app:
    build: .
    depends_on:
      - ollama
      - mysql
      - redis
    environment:
      - OLLAMA_HOST=http://ollama:11434
      - AWS_BEDROCK_REGION=us-east-1
```

**Production Deployment:**

```bash
# Ollama setup
curl -fsSL https://ollama.ai/install.sh | sh
ollama pull llama3.1:8b-instruct-q4_K_M
ollama pull nomic-embed-text

# Laravel setup
php artisan migrate
php artisan queue:work redis --queue=default,notifications,emails,digests,documents,embeddings,auto-reply --tries=3 --timeout=1200
php artisan reverb:serve --host=127.0.0.1 --port=8080 --scheme=http
```

---

## 13. Pemantauan & Pemberitahuan (Monitoring & Alerting)

### 12.1. Performance Metrics

| KPI                     | Target     | Alatan                    |
| ----------------------- | ---------- | ------------------------- |
| **Uptime**              | 99.5%      | Health checks (`/up`, `/api/health/*`) + Laravel Pulse |
| **Response Time (p95)** | <2 seconds | Laravel Pulse (v3.5.0)    |
| **Error Rate (5xx)**    | <0.5%      | Sentry                    |
| **Database Query Time** | <500ms avg | Laravel Pulse (v3.5.0)    |
| **Queue Job Failures**  | <2%        | Laravel Pulse (v3.5.0)    |
| **Cache Hit Rate**      | >90%       | Laravel Pulse (v3.5.0)    |
| **Server Health**       | CPU <80%   | Laravel Pulse (v3.5.0)    |

### 12.2. Logging Configuration

```php
// config/logging.php
'channels' => [
    'stack' => [
        'driver' => 'stack',
        'channels' => ['daily', 'slack'],
    ],
    'daily' => [
        'driver' => 'daily',
        'path' => storage_path('logs/laravel.log'),
        'days' => 14,
    ],
],
```

---

## 13. Senibina Deployment (Deployment Architecture)

### 13.1. Infrastructure Stack

```text
┌─────────────────────────────────────────────────────────┐
│         END-USER CLIENTS (Browser)                      │
│     Windows/macOS: Chrome, Firefox, Safari, Edge        │
└──────────────┬──────────────────────────────────────────┘
               │
         HTTPS/TLS 1.3
               │
┌──────────────▼──────────────────────────────────────────┐
│  LOAD BALANCER (HAProxy or AWS ALB)                     │
│  - SSL/TLS termination                                  │
│  - Health checks (/up endpoint)                         │
└──────────────┬──────────────────────────────────────────┘
               │
   ┌───────────┼───────────┐
   │           │           │
┌──▼──┐     ┌──▼──┐     ┌──▼──┐
│APP-1│     │APP-2│     │APP-N│  (N app servers)
│Nginx│     │Nginx│     │Nginx│
│PHP82│     │PHP82│     │PHP82│
└──┬──┘     └──┬──┘     └──┬──┘
   │           │           │
   └───────────┼───────────┘
               │
        ┌──────▼──────┐
        │ Redis Cluster│ (Session + Cache + Queue)
        └──────┬──────┘
               │
        ┌──────▼──────┐
        │ MySQL 8.0   │ (Primary + Replica)
        └─────────────┘
```

### 13.2. Server Specifications

| Server Role       | OS           | vCPU | RAM    | Storage     |
| ----------------- | ------------ | ---- | ------ | ----------- |
| **App Server**    | Ubuntu 22.04 | 4+   | 8+ GB  | 50 GB SSD   |
| **MySQL Primary** | Ubuntu 22.04 | 8+   | 16+ GB | 500+ GB SSD |
| **Redis Cluster** | Ubuntu 22.04 | 4+   | 8+ GB  | 100 GB SSD  |
| **Load Balancer** | Ubuntu 22.04 | 2+   | 4 GB   | 20 GB SSD   |

### 13.3. Deployment Commands

```bash
# Pull latest code
git pull origin main

# Install dependencies
composer install --no-dev --optimize-autoloader

# Run migrations
php artisan migrate --force

# Cache configuration
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Restart services
systemctl restart php-fpm
systemctl restart nginx

# Verify health
curl -s http://localhost/up
```

---

## 14. Kawalan Kualiti (Quality Control)

### 14.1. Code Quality Tools

| Tool             | Fungsi            | Command                      |
| ---------------- | ----------------- | ---------------------------- |
| **Laravel Pint** | PSR-12 formatting | `vendor/bin/pint`            |
| **PHPStan**      | Static analysis   | `vendor/bin/phpstan analyse` |
| **PHPUnit**      | Testing           | `php artisan test`           |
| **Playwright**   | E2E testing       | `npx playwright test`        |

### 14.2. CI/CD Pipeline

```yaml
name: CI
on: [push, pull_request]
jobs:
  test:
    runs-on: ubuntu-latest
    steps:
      - uses: actions/checkout@v4
      - run: composer install
      - run: vendor/bin/pint --test
      - run: vendor/bin/phpstan analyse
      - run: php artisan test --coverage
      - run: npm run build
```

---

## 15. Penutup

Dokumentasi ini menjadi rujukan rasmi bagi pembangunan, audit, dan penambahbaikan sistem Helpdesk & ICT Asset Loan BPM MOTAC. Semua komponen direka untuk skalabiliti, keselamatan, dan kualiti mengikut piawaian **IEEE 1016**, **ISO/IEC/IEEE 2651x series**, **ISO 9001**, dan **ISO/IEC/IEEE 12207**.

**True Hybrid Architecture v3.5.0 Technical Features:**

- Laravel Pulse for real-time performance monitoring (admin/superuser)
- Laravel Sanctum for API token authentication with scoped abilities
- Google Workspace SSO via Laravel Socialite (optional)
- Enhanced security with SHA-512 status tokens and OAuth 2.0
- New services: GoogleSsoService, ApiTokenService, PerformanceMonitoringService, AccessoryTrackingService, ResponsibleOfficerService, AccountLinkingService
- Redis 7.x for caching, queue, and Pulse backend

---

## Glosari & Rujukan (Glossary & References)

Sila rujuk **[GLOSSARY.md]** untuk istilah teknikal seperti:

- **Rekabentuk Teknikal (Technical Design)**: Spesifikasi terperinci senibina dan komponen sistem
- **MVC (Model-View-Controller)**: Pola senibina pemisahan logik aplikasi
- **RESTful API**: Application Programming Interface mengikut prinsip REST
- **WebSocket**: Protocol untuk komunikasi real-time bidirectional
- **RBAC**: Role-Based Access Control

**Dokumen Rujukan**:

- **D00_SYSTEM_OVERVIEW.md** - Gambaran keseluruhan sistem
- **D04_SOFTWARE_DESIGN_DOCUMENT.md** - Rekabentuk perisian (high-level)
- **D09_DATABASE_DOCUMENTATION.md** - Dokumentasi pangkalan data
- **D10_SOURCE_CODE_DOCUMENTATION.md** - Dokumentasi kod sumber
- **D16_BROADCASTING_SETUP.md** - Konfigurasi WebSocket

---

**Dokumen ini mematuhi piawaian IEEE 1016:2009 (Software Design), ISO/IEC/IEEE 26512:2018 (Software User Documentation), ISO 9001:2015 (Quality Management Systems), dan ISO/IEC/IEEE 12207:2017 (Software Lifecycle Processes).**
