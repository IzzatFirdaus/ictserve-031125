# Dokumen Rekabentuk Perisian (Software Design Document - SDD)

**Sistem ICTServe**  
**Versi:** 3.5.0 (SemVer)  
**Tarikh Kemaskini:** 30 November 2025  
**Status:** Aktif  
**Klasifikasi:** Terhad - Dalaman BPM MOTAC  
**Penulis:** Pasukan Pembangunan BPM MOTAC  
**Standard Rujukan:** ISO/IEC/IEEE 42010, ISO/IEC/IEEE 15288, WCAG 2.2 AA, OWASP ASVS L2

---

## Maklumat Dokumen (Document Information)

| Atribut              | Nilai                                               |
| -------------------- | --------------------------------------------------- |
| **Versi**            | 3.5.0                                               |
| **Tarikh Kemaskini** | 30 November 2025                                    |
| **Status**           | Aktif                                               |
| **Klasifikasi**      | Terhad - Dalaman BPM MOTAC                          |
| **Pematuhi**         | ISO/IEC/IEEE 42010, ISO/IEC/IEEE 15288, WCAG 2.2 AA |
| **Bahasa**           | Bahasa Melayu (utama), English (teknikal)           |

> Notis Penggunaan Dalaman: Sistem ini digunakan secara dalaman oleh staf dan pegawai gred MOTAC; ia bukan sistem awam.

---

## Sejarah Perubahan (Changelog)

| Versi | Tarikh           | Perubahan                                                                                                                                                                                                                                                                 | Penulis                 |
| ----- | ---------------- | ------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------- | ----------------------- |
| 3.5.0 | 30 November 2025 | True Hybrid Architecture: Self-registration (@motac.gov.my), flexible login (email/username), optional guest-to-account linking, dual audit system (owen-it + spatie), Laravel Telescope (superuser only), multi-channel notifications. Pematuhan Jabatan Digital Negara. | Pasukan Pembangunan BPM |
| 3.6.0 | 8 Disember 2025  | Bahasa Melayu sahaja untuk antara muka: Kemaskini semua modul (registration, login, account linking, notifications) kepada Bahasa Melayu sahaja. Language switcher dilumpuhkan. Bilingual support→Bahasa Melayu sahaja. Penyelarasan dengan D00-D17 v3.6.0.                 | Pasukan Pembangunan BPM |
| 3.4.0 | 29 November 2025 | Hybrid Architecture: Restore user_id nullable FK dalam HelpdeskTicket/LoanApplication. Auth::check() logic untuk auto-fill. ERD update: user_id (0..1 relationship). Penyelarasan dengan D00/D02 v3.4.0.                                                                  | Pasukan Pembangunan BPM |
| 3.3.0 | 29 November 2025 | Kemaskini dokumentasi sistem: pengesahan versi teknologi semasa (Laravel 12.40.1, PHP 8.2.12, Livewire 3.7.0, Filament 4.1.10, PHPUnit 11.5.44, Larastan 3.8.0, Laravel Pint 1.26.0). Penyelarasan dengan D00-D03 v3.2.0.                                                 | Pasukan Pembangunan BPM |
| 3.2.0 | 29 November 2025 | Kemaskini rekabentuk guest-first: penjelasan aliran kerja tanpa akaun pengguna, token-based approval, status checking, dan integrasi real-time WebSocket untuk notifikasi pentadbir                                                                                       | Pasukan Pembangunan BPM |
| 3.1.0 | 29 November 2025 | Kemaskini kepada teknologi semasa: Laravel 12.40.1, Livewire 3.7.0, Filament 4.1.10, Volt 1.10.1, Alpine.js 3, Tailwind CSS 4.1.17, Laravel Reverb 1.6.2, Laravel Echo 2.2.6, PHPUnit 11.5.44. Penambahan komunikasi real-time WebSocket.                                 | Pasukan Pembangunan BPM |
| 3.0.0 | 31 Oktober 2025  | Rekabentuk dikemas kini kepada seni bina dalaman (internal-only); autentikasi pengguna dalaman, RBAC, penyusunan semula modul Helpdesk & Loan, dan pengukuhan audit/kelulusan dalam sistem.                                                                               | Pasukan Pembangunan BPM |
| 2.0.0 | 17 Oktober 2025  | Penyeragaman mengikut D00-D14, SemVer, cross-reference                                                                                                                                                                                                                    | Pasukan BPM             |
| 1.0.0 | September 2025   | Versi awal SDD                                                                                                                                                                                                                                                            | Pasukan BPM             |

---

## Rujukan Dokumen Berkaitan

- **[D00_SYSTEM_OVERVIEW.md]** - System vision and governance (v3.5.0)
- **[D01_SYSTEM_DEVELOPMENT_PLAN.md]** - Development methodology (v3.5.0)
- **[D02_BUSINESS_REQUIREMENTS_SPECIFICATION.md]** - Business requirements (v3.5.0)
- **[D03_SOFTWARE_REQUIREMENTS_SPECIFICATION.md]** - Software requirements (v3.5.0)
- **[D05_DATA_MIGRATION_PLAN.md]** - Data migration strategy
- **[D06_DATA_MIGRATION_SPECIFICATION.md]** - Migration specifications
- **[D07_SYSTEM_INTEGRATION_PLAN.md]** - Integration planning
- **[D08_SYSTEM_INTEGRATION_SPECIFICATION.md]** - Integration specifications
- **[D09_DATABASE_DOCUMENTATION.md]** - Database schema and dual audit (v3.5.0)
- **[D11_TECHNICAL_DESIGN_DOCUMENTATION.md]** - Technical infrastructure
- **[D12_UI_UX_DESIGN_GUIDE.md]** - UI/UX guidelines (v3.5.0)
- **[D13_UI_UX_FRONTEND_FRAMEWORK.md]** - Frontend framework (v3.5.0)
- **[D14_UI_UX_STYLE_GUIDE.md]** - Style guide (v3.5.0)
- **[D15_LANGUAGE_MS_EN.md]** - Language localization (Bahasa Melayu sahaja, v3.6.0)
- **[D16_BROADCASTING_SETUP.md]** - WebSocket configuration (Laravel Reverb)
- **[D17_QUEUE_MANAGEMENT_HORIZON.md]** - Queue management (Laravel Horizon)
- **docs/helpdesk_form_to_model.md** - Helpdesk data mapping
- **docs/loan_form_to_model.md** - Asset loan data mapping

---

## 1. TUJUAN DOKUMEN (Purpose)

SDD ini menghuraikan rekabentuk teknikal ICTServe sebagai sistem dalaman (internal-only) dengan seni bina **hybrid**. Ia memperincikan seni bina, modul, komponen data, dan aliran kerja di mana:

- **Staf** boleh pilih login (Laravel Breeze - akaun pangkalan data) untuk Dashboard/Profile ATAU gunakan borang tetamu
- **Database** menyokong nullable `user_id` FK dalam `helpdesk_tickets` dan `loan_applications`
- **Pentadbir** (`admin` & `superuser`) menguruskan tiket, permohonan, dan aset melalui panel Filament 4.1.10
- **Kelulusan** diproses melalui pautan e-mel bertanda tangan (signed URL + token)
- **Status** boleh disemak menggunakan token unik

Sistem dibina menggunakan Laravel 12.40.1, Livewire 3.7.0, Volt 1.10.1, Alpine.js 3, Tailwind CSS 4.1.17, dan Laravel Reverb 1.6.2 untuk komunikasi masa nyata (notifikasi pentadbir).

---

## 2. SKOP REKABENTUK (Design Scope)

Skop merangkumi:

- **Portal Hybrid**: Borang helpdesk dan pinjaman aset dengan pilihan login (Laravel Breeze) atau tetamu (Laravel Blade + Livewire 3.7.0, Volt 1.10.1, Alpine.js 3)
- **Portal Pentadbir**: Panel Filament 4.1.10 untuk `admin` dan `superuser` dengan autentikasi Laravel Breeze
- **Backend Servis**: Laravel 12.40.1 untuk validasi, notifikasi, audit, dan workflow kelulusan
- **Komunikasi Real-time**: Laravel Reverb 1.6.2 (WebSocket server) dan Laravel Echo 2.2.6 (client) untuk notifikasi pentadbir
- **Token-Based Operations**: Signed URLs untuk kelulusan e-mel, status tokens untuk semakan tetamu
- **Penyimpanan**: MySQL 8.0 (data), S3/MinIO (lampiran), Redis (queue & cache)
- **Integrasi**: E-mel (SMTP), SMS (opsyen), direktori/kamus dalaman bahagian (bukan LDAP/SSO dalam v3.4.0)

Di luar skop:

- Aplikasi mudah alih natif (boleh diambil masa hadapan melalui API)
- Integrasi LDAP/SSO penuh (termasuk sync automatik) â€“ di luar skop v3.4.0; sistem semasa menggunakan akaun dalaman Laravel Breeze sahaja

---

## 3. SENIBINA SISTEM (System Architecture)

### 3.1. Architectural Pattern: MVC + Service Layer + Guest-First

- **Presentation Layer:**
  - **Hybrid Portal**: Blade + Livewire 3.7.0 + Volt 1.10.1 + Alpine.js 3 (pilihan login atau tetamu)
  - **Admin Panel**: Filament 4.1.10 (dengan autentikasi Laravel Breeze)
  - **Real-time**: Laravel Echo 2.2.6 client untuk notifikasi pentadbir
- **Application Layer:**
  - Controllers untuk guest routes (helpdesk, loan, status, approval)
  - Livewire components untuk borang interaktif
  - Filament Resources untuk CRUD pentadbir
  - Service classes (`HelpdeskService`, `LoanService`, `ApprovalService`, `NotificationService`)
- **Domain Layer:**
  - Eloquent models (`HelpdeskTicket`, `LoanApplication`, `LoanApproval`, `User`)
  - Events (`TicketCreated`, `LoanApproved`, `AssetOverdue`)
  - Policies untuk authorization pentadbir
  - Enums untuk status dan priority
- **Infrastructure Layer:**
  - Queue (Redis) untuk e-mel, SMS, dan background jobs
  - Mail templates (WCAG 2.2 AA compliant)
  - Storage (S3/MinIO) untuk lampiran
  - Audit logging (Dual System):
  - `owen-it/laravel-auditing` v14.x untuk field-level audit trail (compliance, PDPA)
  - `spatie/laravel-activitylog` v4.x untuk user activity logging (operations, dashboard)
  - Laravel Telescope v5.x untuk debugging (superuser only, unrestricted)
  - WebSocket server (Laravel Reverb 1.6.2)

### 3.2. Layered Components

| Lapisan        | Komponen                                                                                                                                                                                  | Nota                                               |
| -------------- | ----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------- | -------------------------------------------------- |
| Presentation   | **Hybrid**: Livewire 3.7.0 + Volt 1.10.1 components (`helpdesk.ticket-form`, `loan.application-form`), Alpine.js 3 interactivity. **Admin**: Filament 4.1.10 resources dengan autentikasi | Mematuhi D12–D14, WCAG 2.2 AA                      |
| Service        | `HelpdeskService`, `LoanService`, `ApprovalService`, `NotificationService`, `TokenService`                                                                                                | Logik domain, workflow kelulusan, token generation |
| Persistence    | Eloquent models (`HelpdeskTicket`, `LoanApplication`, `LoanApproval`, `User`, `StatusToken`)                                                                                              | Nullable user_id FK + guest tracking columns       |
| Infrastructure | Queue jobs (Redis), mail templates (WCAG compliant), storage (S3/MinIO), WebSocket (Reverb)                                                                                               | Dependency injection via service container         |
| Security       | Middleware (`throttle:guest`, `auth`, `signed`), rate limiter, CSRF, reCAPTCHA, audit logging                                                                                             | Token hashing (SHA-512), encryption (AES-256)      |

### 3.3. Deployment Architecture

**Application Server:**

- Laravel 12.40.1 monolith (Nginx 1.24 + PHP-FPM 8.2.12)
- Vite 7.0.7 untuk asset bundling (CSS/JS)
- Tailwind CSS 4.1.17 untuk styling

**Real-time Communication:**

- Laravel Reverb 1.6.2 (WebSocket server untuk notifikasi pentadbir)
- Laravel Echo 2.2.6 (client-side WebSocket integration)
- Redis untuk broadcasting events

**Background Processing:**

- Redis 7.0 untuk queue dan cache
- Supervisor untuk queue workers
- Laravel Horizon (opsyen) untuk queue monitoring

**Data Storage:**

- MySQL 8.0 untuk relational data
- S3/MinIO untuk lampiran tetamu (presigned URLs, 15 min expiry)
- Local storage untuk temporary files

**Monitoring & Logging:**

- Laravel Telescope (development)
- Prometheus + Grafana (production metrics)
- Sentry (error tracking, opsyen)
- ELK Stack (log aggregation, opsyen)

**Security Infrastructure:**

- WAF (Web Application Firewall)
- HTTPS/TLS 1.3 (Let's Encrypt)
- reCAPTCHA Enterprise untuk borang tetamu
- Rate limiting (60 requests/min untuk guest routes)
- Fail2ban untuk brute force protection

---

## 4. REKABENTUK MODUL (Module Design)

### 4.1. Helpdesk Ticketing (Hybrid)

**Komponen Utama:**

- `resources/views/helpdesk/create.blade.php` - Borang hybrid (guest/auth layout)
- `app/Livewire/Helpdesk/TicketForm.php` - Livewire component dengan Auth::check() logic
- `app/Services/Helpdesk/HelpdeskService.php` - Business logic (create ticket, manage attachments, notifications)
- `app/Models/HelpdeskTicket.php` - Eloquent model dengan nullable `user_id` FK, relationships (`user`, `comments`, `attachments`)
- `app/Filament/Resources/HelpdeskTicketResource.php` - Admin panel resource
- `app/Mail/Helpdesk/TicketCreatedMail.php` - E-mel confirmation (WCAG 2.2 AA)

**Aliran Kerja (Hybrid Flow):**

1. **Pengguna mengakses borang** (`/helpdesk/create`):

   - Livewire component memuatkan dropdown (bahagian, kategori, gred)
   - **If Auth::check()**: Auto-fill nama, e-mel, telefon dari user profile
   - **If guest**: Manual input semua field
   - reCAPTCHA Enterprise dipaparkan untuk guest

2. **Input validation** (real-time + server-side):

   - Client-side: Alpine.js + Livewire validation
   - Server-side: Laravel Form Request (`StoreHelpdeskTicketRequest`)
   - Lampiran: max 5 files, 10MB each, allowed types (PDF, JPG, PNG, DOCX)

3. **Ticket creation** (`HelpdeskService::createTicket()`):

   - Generate unique ticket number (`HD-YYYYMM-XXXX`)
   - **If Auth::check()**: Store `user_id` (nullable FK) + submitter data
   - **If guest**: Store submitter data only, `user_id` = NULL
   - Upload attachments ke S3/MinIO dengan virus scanning (ClamAV)
   - Generate status token (SHA-512 hash) untuk semakan status
   - Calculate SLA due date berdasarkan priority

4. **Notification dispatch** (queued jobs):

   - E-mel kepada tetamu dengan ticket number dan status link
   - E-mel kepada admin team dengan ticket details
   - WebSocket notification ke admin panel (Laravel Reverb)

5. **Admin management** (Filament panel):
   - `admin` boleh assign, update status, add comments
   - `superuser` boleh view all tickets + audit trail
   - Internal comments (tidak visible kepada tetamu)
   - Status updates trigger e-mel notification kepada tetamu

**Pertimbangan Rekabentuk:**

- **Hybrid access**: Nullable `user_id` FK + guest tracking columns (`submitter_name`, `submitter_email`, `submitter_phone`)
- **Auth::check() logic**: Auto-fill borang jika logged in, manual input jika guest
- **Rate limiting**: `throttle:guest,60,1` (60 requests per minute untuk guest), `throttle:auth,120,1` (120 untuk authenticated)
- **CSRF protection**: Semua borang dilindungi CSRF token
- **reCAPTCHA**: Enterprise version untuk spam prevention (guest only)
- **WCAG compliance**: E-mel templates dengan text + HTML version, color contrast 4.5:1
- **Status checking**: Semua pengguna boleh semak status menggunakan token
- **Audit trail**: Semua perubahan dilog menggunakan Laravel Auditing 14.x

### 4.2. ICT Asset Loan (Hybrid + Token-Based Approval)

**Komponen Utama:**

- `resources/views/loan/create.blade.php` - Borang permohonan hybrid
- `app/Livewire/Loan/ApplicationForm.php` - Multi-step wizard component dengan Auth::check() logic
- `app/Services/Loan/LoanService.php` - Business logic (create application, asset management)
- `app/Services/Loan/ApprovalService.php` - Token generation, e-mel approval, decision processing
- `app/Models/LoanApplication.php` - Model dengan nullable `user_id` FK, relationships (`user`, `items`, `transactions`, `approvals`)
- `app/Filament/Resources/LoanApplicationResource.php` - Admin panel resource
- `app/Http/Controllers/Loan/ApprovalController.php` - Handle approval links

**Aliran Kerja (Hybrid + Approval Flow):**

1. **Pengguna mengisi borang** (`/loan/create`):

   - Step 1: Maklumat pemohon (auto-fill jika Auth::check(), manual jika guest)
   - Step 2: Pilih aset (dengan availability check real-time)
   - Step 3: Tarikh pinjaman (dengan conflict detection)
   - Step 4: Tujuan dan lokasi penggunaan
   - Step 5: Review dan submit (dengan PDPA acknowledgement)

2. **Application creation** (`LoanService::createApplication()`):

   - Generate unique reference (`LA-YYYYMM-XXXX`)
   - **If Auth::check()**: Store `user_id` (nullable FK) + applicant data
   - **If guest**: Store applicant data only, `user_id` = NULL
   - Validate asset availability dan date conflicts
   - Set status: `PENDING_SUPERVISOR_APPROVAL`
   - Reserve assets (soft lock)

3. **Approval workflow** (`ApprovalService::initiateApproval()`):

   - Determine approver email (dari config atau user input)
   - Generate signed URL dengan token hash (SHA-512)
   - Token valid 72 jam (configurable)
   - Queue e-mel dengan approval link
   - Store token hash dalam `loan_approvals`

4. **Approver action** (via e-mel link):

   - Click link → `ApprovalController::show()`
   - Verify signed URL dan token validity
   - Display application summary (guest layout, read-only)
   - Approver pilih: Approve atau Reject (dengan remarks)
   - Record decision dengan metadata (IP hash, timestamp, user-agent)

5. **Post-approval workflow** (`LoanService::progressWorkflow()`):

   - **If approved**: Status → `APPROVED` → `AWAITING_COLLECTION`
   - **If rejected**: Status → `REJECTED`, release asset reservation
   - Generate `loan_transactions` record
   - Schedule reminder notifications (collection, return)
   - WebSocket notification ke admin panel

6. **Asset handover** (admin action via Filament):

   - Admin scan QR code atau manual check-out
   - Record `performed_by_admin_id`, `performed_at`, `condition_notes`
   - Status → `ON_LOAN`
   - Generate handover receipt (PDF)

7. **Asset return** (admin action):
   - Admin verify asset condition
   - Record return transaction dengan photos (opsyen)
   - Status → `RETURNED` atau `DAMAGED`
   - If damaged: Auto-create helpdesk ticket untuk maintenance

**Pertimbangan Rekabentuk:**

- **Hybrid access**: Nullable `user_id` FK + guest tracking columns (`applicant_name`, `applicant_email`, `applicant_phone`)
- **Auth::check() logic**: Auto-fill borang jika logged in, manual input jika guest
- **Token-based approval**: Signed URL + SHA-512 hashed token (no login required)
- **Token expiry**: 72 jam (configurable via `config/loan.php`)
- **Token regeneration**: `superuser` boleh regenerate expired tokens
- **Approval audit**: Semua decisions disimpan dalam `loan_approvals` dengan metadata lengkap
- **Asset reservation**: Soft lock (status `reserved`) untuk prevent double booking
- **Conflict detection**: Check overlapping dates untuk same asset
- **Reminder system**: Laravel Scheduler untuk overdue notifications
- **Cross-module integration**: Damaged asset auto-create helpdesk ticket

### 4.3. Inventory Management (Admin Only)

**Komponen:**

- `app/Filament/Resources/AssetResource.php` - CRUD untuk aset ICT
- `app/Filament/Resources/AssetCategoryResource.php` - Kategori aset
- `app/Filament/Resources/LoanTransactionResource.php` - Sejarah transaksi
- `app/Models/Asset.php` - Model dengan status tracking

**Fungsi:**

- **Asset CRUD**: `admin` boleh create, update, delete aset
- **Status management**: `available`, `reserved`, `on_loan`, `maintenance`, `retired`
- **QR code generation**: Auto-generate QR code untuk setiap aset
- **Availability tracking**: Real-time status berdasarkan loan transactions
- **Maintenance scheduling**: Integration dengan helpdesk untuk maintenance tickets
- **Audit trail**: Semua perubahan dilog (Laravel Auditing 14.x)
- **Bulk operations**: Import/export aset via CSV (Laravel Excel)

**Authorization:**

- `admin`: Full CRUD access
- `superuser`: Read-only + audit trail access + approve critical changes (asset retirement)

### 4.4. Reporting & Dashboard (Admin Panel)

**Filament Widgets:**

- `app/Filament/Widgets/HelpdeskStatsWidget.php` - Ticket metrics (open, in progress, resolved)
- `app/Filament/Widgets/LoanStatsWidget.php` - Loan metrics (pending, approved, on loan)
- `app/Filament/Widgets/AssetUtilizationWidget.php` - Asset usage statistics
- `app/Filament/Widgets/SLAComplianceWidget.php` - SLA breach tracking
- `app/Filament/Widgets/RecentActivityWidget.php` - Latest tickets dan loans

**Report Generation:**

- Monthly ticket summary (PDF/Excel)
- Asset utilization report
- SLA compliance report
- Overdue items report
- Custom date range reports

**Performance Optimization:**

- Query result caching (15 minutes)
- Eager loading untuk relationships
- Database indexes untuk common queries
- Pagination untuk large datasets

### 4.5. Audit Trail & Logging

**Audit Implementation:**

- **Package**: `spatie/laravel-activitylog` v4.x
- **Models**: `LogsActivity` trait pada `HelpdeskTicket`, `LoanApplication`, `Asset`, `User`
- **Events logged**: Created, Updated, Deleted, Status Changed, Approval Decision
- **Metadata**: User ID, IP address (hashed), timestamp, old/new values

**Audit Storage:**

- `activity_log` table untuk general activities
- `loan_audits` table untuk loan-specific audit trail
- Retention: 7 years (compliance requirement)

**Audit Export:**

- `AuditExportJob` untuk export ke SIEM (opsyen)
- CSV export via Filament untuk manual review
- Real-time streaming ke ELK Stack (opsyen)

**Audit Access:**

- `admin`: View own activities
- `superuser`: View all activities + export capabilities

### 4.6. Self-Registration Module (v3.5.0)

**Komponen Utama:**

- `resources/views/auth/register.blade.php` - Registration form (Laravel Breeze)
- `app/Services/Auth/RegistrationService.php` - Registration business logic
- `app/Http/Controllers/Auth/RegisteredUserController.php` - Registration controller
- `app/Mail/Auth/VerifyEmailMail.php` - Email verification notification
- `app/Models/User.php` - User model with email verification

**Aliran Kerja (Self-Registration Flow):**

1. **Staff mengakses borang pendaftaran** (`/register`):

   - Display Bahasa Melayu registration form (WCAG 2.2 AA compliant)
   - Fields: Name, Email (@motac.gov.my), Password, Password Confirmation
   - Additional fields: Staff Number, Division, Grade, Phone
   - Real-time email domain validation (client-side + server-side)
   - Password strength indicator (minimum 8 characters, mixed case, numbers, symbols)

2. **Email domain validation** (`RegistrationService::validateEmailDomain()`):

   - Enforce @motac.gov.my domain restriction
   - Reject non-MOTAC emails with Bahasa Melayu error message
   - Check for existing accounts with same email
   - Prevent duplicate registrations

3. **Account creation** (`RegistrationService::register()`):

   - Create user record with `email_verified_at` = NULL
   - Hash password using bcrypt (Laravel default)
   - Set default role: `staff`
   - Set default locale: `ms` (Bahasa Melayu)
   - Set default notification preferences: `{"email": true, "in_app": true, "digest": "daily"}`
   - Generate email verification token (signed URL, 24-hour expiry)

4. **Email verification dispatch** (queued job):

   - Send verification email with signed URL
   - Include Bahasa Melayu instructions
   - Token valid for 24 hours (configurable via `config/auth.php`)
   - Resend option available on login attempt

5. **Email verification** (`/email/verify/{id}/{hash}`):

   - Verify signed URL validity
   - Check token expiration
   - Update `email_verified_at` timestamp
   - Redirect to login with success message
   - Log verification event in activity_log

6. **Post-verification workflow**:
   - Staff can now login using email or username
   - Access to My Dashboard and submission forms
   - Optional: Link historical guest submissions via Account Linking feature

**Pertimbangan Rekabentuk:**

- **Email domain restriction**: Strict @motac.gov.my validation (no exceptions)
- **Username extraction**: Auto-generate username from email (e.g., `ahmad.ibrahim@motac.gov.my` → `ahmad.ibrahim`)
- **Token security**: Signed URLs with HMAC-SHA256 signature
- **Rate limiting**: `throttle:guest,5,1` (5 registration attempts per minute per IP)
- **CSRF protection**: All forms protected with CSRF token
- **Audit trail**: Registration events logged in activity_log
- **WCAG compliance**: Form with proper labels, error messages, keyboard navigation
- **Bahasa Melayu sahaja (v3.6.0)**: All messages in Bahasa Melayu only

### 4.7. Flexible Login Module (v3.5.0)

**Komponen Utama:**

- `resources/views/auth/login.blade.php` - Login form (Laravel Breeze)
- `app/Http/Controllers/Auth/AuthenticatedSessionController.php` - Login controller
- `app/Http/Requests/Auth/LoginRequest.php` - Login validation
- `app/Services/Auth/AuthenticationService.php` - Authentication logic

**Aliran Kerja (Flexible Login Flow):**

1. **Staff mengakses borang login** (`/login`):

   - Display Bahasa Melayu login form
   - Single input field: "Email or Username"
   - Password field with show/hide toggle
   - Remember me checkbox
   - Forgot password link

2. **Input processing** (`AuthenticationService::authenticate()`):

   - Accept full email (`ahmad.ibrahim@motac.gov.my`) OR short username (`ahmad.ibrahim`)
   - If input contains `@`, treat as email
   - If input does not contain `@`, append `@motac.gov.my` to form email
   - Example: `ahmad.ibrahim` → `ahmad.ibrahim@motac.gov.my`

3. **Authentication attempt**:

   - Attempt login with constructed email + password
   - Use Laravel's built-in authentication (bcrypt password verification)
   - Rate limiting: `throttle:login,5,1` (5 attempts per minute per IP)
   - Lockout after 5 failed attempts (1 minute cooldown)

4. **Success handling**:

   - Regenerate session ID (prevent session fixation)
   - Redirect to intended URL or `/dashboard`
   - Log login event in activity_log
   - Update `last_login_at` timestamp

5. **Failure handling**:
   - Generic error message: "These credentials do not match our records."
   - No user enumeration (same message for invalid email/username and wrong password)
   - Log failed attempt with IP hash (security monitoring)
   - Display remaining attempts before lockout

**Pertimbangan Rekabentuk:**

- **Username extraction logic**: Consistent with registration (email prefix before `@`)
- **Security**: No user enumeration, generic error messages
- **Rate limiting**: Prevent brute force attacks
- **Session management**: Secure session cookies (httpOnly, secure, sameSite)
- **Audit trail**: All login attempts logged (success and failure)
- **WCAG compliance**: Accessible form with proper labels and error announcements
- **Bahasa Melayu sahaja (v3.6.0)**: All messages in Bahasa Melayu only

### 4.8. Account Linking Module (v3.5.0)

**Komponen Utama:**

- `resources/views/dashboard/account-linking.blade.php` - Account linking page
- `app/Livewire/Dashboard/AccountLinking.php` - Livewire component
- `app/Services/Dashboard/AccountLinkingService.php` - Linking business logic
- `app/Models/User.php` - User model with linking relationships

**Aliran Kerja (Account Linking Flow):**

1. **Staff mengakses halaman Account Linking** (`/dashboard/account-linking`):

   - Display explanation of account linking feature
   - Email input field (pre-filled with user's registered email)
   - Search button to find unlinked submissions
   - Display count of linked submissions (if any)

2. **Search for unlinked submissions** (`AccountLinkingService::findUnlinkedSubmissions()`):

   - Query `helpdesk_tickets` WHERE `submitter_email` = input email AND `user_id` IS NULL
   - Query `loan_applications` WHERE `applicant_email` = input email AND `user_id` IS NULL
   - Return combined list with ticket/loan numbers, dates, status
   - Display results in table format (sortable, filterable)

3. **Review matching submissions**:

   - Display submission details (ticket/loan number, date, status, description)
   - Checkbox for each submission (select all option available)
   - Confirmation prompt: "Link X submissions to your account?"

4. **Link submissions** (`AccountLinkingService::linkSubmissions()`):

   - Atomic transaction (all or nothing)
   - Update `user_id` FK for selected tickets/loans
   - Preserve original submitter/applicant data (no overwrite)
   - Log linking action in activity_log with metadata (submission IDs, timestamp)
   - Send confirmation email with summary

5. **Post-linking workflow**:
   - Linked submissions now appear in My Dashboard
   - Staff can view full history and status updates
   - Notifications enabled for linked submissions
   - Audit trail preserved (original submission data + linking event)

**Pertimbangan Rekabentuk:**

- **Email matching**: Case-insensitive email comparison
- **Atomic transaction**: Use database transaction to ensure data consistency
- **Audit trail**: Comprehensive logging of linking actions
- **Reversibility**: `superuser` can unlink submissions if needed (via Filament)
- **Privacy**: Only staff can link their own submissions (email must match)
- **Performance**: Index on `submitter_email` and `applicant_email` for fast queries
- **WCAG compliance**: Accessible table with keyboard navigation
- **Bahasa Melayu sahaja (v3.6.0)**: All messages in Bahasa Melayu only

### 4.9. Laravel Pulse Integration (v3.5.0)

**Komponen Utama:**

- `app/Providers/PulseServiceProvider.php` - Pulse configuration
- `config/pulse.php` - Pulse settings
- `routes/pulse.php` - Pulse dashboard routes
- Pulse dashboard accessible at `/pulse` (admin/superuser only)

**Fungsi Monitoring:**

1. **Performance Metrics**:

   - Request throughput (requests per second)
   - Response time distribution (p50, p95, p99)
   - Slow query detection (queries > 1000ms)
   - Memory usage tracking
   - CPU usage monitoring

2. **Queue Monitoring**:

   - Queue job throughput
   - Failed job tracking
   - Job processing time
   - Queue depth (pending jobs)
   - Worker status

3. **Server Health**:

   - Disk usage
   - Database connection pool
   - Redis connection status
   - Cache hit ratio
   - Session count

4. **Application Insights**:
   - Most visited pages
   - Slowest endpoints
   - Exception tracking
   - User activity patterns
   - API usage statistics

**Authorization:**

- `admin`: Read-only access to performance metrics
- `superuser`: Full access + configuration management

**Pertimbangan Rekabentuk:**

- **Data retention**: 7 days (configurable)
- **Sampling rate**: 100% for critical paths, 10% for general traffic
- **Storage**: Redis for real-time data, MySQL for historical data
- **Performance impact**: Minimal overhead (<5ms per request)
- **Privacy**: No PII stored in Pulse data

### 4.10. Laravel Sanctum API (v3.5.0)

**Komponen Utama:**

- `app/Http/Controllers/Api/V1/HelpdeskApiController.php` - Helpdesk API
- `app/Http/Controllers/Api/V1/LoanApiController.php` - Loan API
- `app/Http/Controllers/Api/V1/AuthApiController.php` - Authentication API
- `routes/api.php` - API routes (versioned)
- `config/sanctum.php` - Sanctum configuration

**API Endpoints:**

1. **Authentication** (`/api/v1/auth`):

   - `POST /login` - Generate API token
   - `POST /logout` - Revoke API token
   - `GET /user` - Get authenticated user details

2. **Helpdesk** (`/api/v1/helpdesk`):

   - `GET /tickets` - List user's tickets
   - `GET /tickets/{id}` - Get ticket details
   - `POST /tickets` - Create new ticket
   - `GET /tickets/{id}/status` - Check ticket status

3. **Loan** (`/api/v1/loans`):
   - `GET /applications` - List user's loan applications
   - `GET /applications/{id}` - Get application details
   - `POST /applications` - Create new application
   - `GET /applications/{id}/status` - Check application status

**Token Management:**

- **Token generation**: SHA-256 hashed tokens
- **Token abilities**: Scoped permissions (e.g., `helpdesk:read`, `loan:write`)
- **Token expiration**: 30 days (configurable)
- **Token revocation**: Manual revocation via dashboard or automatic on logout

**Rate Limiting:**

- `throttle:api,60,1` (60 requests per minute per token)
- Higher limits for admin/superuser tokens

**Pertimbangan Rekabentuk:**

- **API versioning**: `/api/v1/` prefix for future compatibility
- **Response format**: JSON with consistent structure (data, meta, errors)
- **Error handling**: HTTP status codes + descriptive error messages
- **Pagination**: Cursor-based pagination for large datasets
- **Filtering**: Query parameters for filtering, sorting, searching
- **Documentation**: OpenAPI 3.0 specification (Swagger UI)
- **Security**: HTTPS only, CORS configuration, rate limiting

### 4.11. Google Workspace SSO (v3.5.0)

**Komponen Utama:**

- `app/Services/Auth/GoogleSsoService.php` - Google OAuth logic
- `app/Http/Controllers/Auth/GoogleSsoController.php` - SSO controller
- `config/services.php` - Google OAuth credentials
- Laravel Socialite package for OAuth implementation

**Aliran Kerja (Google SSO Flow):**

1. **Staff mengakses login page** (`/login`):

   - Display "Sign in with Google Workspace" button
   - Fallback to standard email/username login

2. **OAuth initiation** (`/auth/google`):

   - Redirect to Google OAuth consent screen
   - Request scopes: `openid`, `email`, `profile`
   - Include `hd` parameter to restrict to `motac.gov.my` domain

3. **OAuth callback** (`/auth/google/callback`):

   - Receive authorization code from Google
   - Exchange code for access token
   - Retrieve user profile (email, name, picture)
   - Verify email domain is `@motac.gov.my`

4. **User provisioning** (`GoogleSsoService::provisionUser()`):

   - Check if user exists (by email)
   - If exists: Update profile (name, picture) and login
   - If not exists: Create new user account with verified email
   - Set role: `staff` (default)
   - Log SSO login event

5. **Session establishment**:
   - Regenerate session ID
   - Redirect to `/dashboard`
   - Display welcome message

**Pertimbangan Rekabentuk:**

- **Domain restriction**: Enforce `@motac.gov.my` via `hd` parameter
- **Auto-provisioning**: Create accounts automatically for verified MOTAC emails
- **Profile sync**: Update name and picture on each login
- **Fallback**: Standard login always available (no SSO dependency)
- **Security**: OAuth 2.0 with PKCE, state parameter for CSRF protection
- **Audit trail**: SSO logins logged separately from standard logins

### 4.12. Enhanced UX Features (v3.5.0)

**Komponen Utama:**

- `app/Livewire/Dashboard/OnboardingTour.php` - Interactive tour component
- `app/Services/Search/FuzzySearchService.php` - Fuzzy search logic
- `app/Livewire/Dashboard/SavedFilters.php` - Filter management component
- `resources/js/touch-gestures.js` - Touch gesture handlers

**Fungsi UX:**

1. **Onboarding Tour**:

   - Interactive walkthrough for new users
   - Highlight key features (submit ticket, check status, view dashboard)
   - Skip option available
   - Progress indicator (step X of Y)
   - Completion tracked in user preferences

2. **Fuzzy Search**:

   - Levenshtein distance algorithm for typo tolerance
   - Search across tickets, loans, assets
   - Highlight matching terms
   - Suggest corrections for misspellings
   - Example: "helpdesk" matches "helpdeks", "hlepdesk"

3. **Saved Filters**:

   - Save frequently used filter combinations
   - Name and description for each saved filter
   - Quick apply from dropdown
   - Share filters with team (admin only)
   - Default filter option

4. **Touch Gestures**:

   - Swipe left/right for navigation
   - Pull-to-refresh for dashboard
   - Long-press for context menu
   - Pinch-to-zoom for images
   - Touch-friendly UI (44x44px minimum targets)

5. **Dashboard Customization**:
   - Drag-and-drop widget reordering
   - Show/hide widgets
   - Widget size adjustment
   - Layout persistence per user
   - Reset to default option

**Pertimbangan Rekabentuk:**

- **Performance**: Fuzzy search with debouncing (300ms delay)
- **Accessibility**: Keyboard shortcuts for all gestures
- **Persistence**: User preferences stored in `notification_preferences` JSON
- **Responsive**: Touch gestures disabled on desktop (mouse-only)
- **WCAG compliance**: All features accessible via keyboard

### 4.13. MOTAC Branding Components (v3.5.0)

**Komponen Utama:**

- `resources/views/components/layout/gov-header.blade.php` - Government header
- `resources/views/components/layout/motac-footer.blade.php` - MOTAC footer
- `resources/views/components/branding/jata-negara.blade.php` - Malaysian Coat of Arms
- `resources/views/components/branding/motac-logo.blade.php` - MOTAC logo
- `public/images/` - Brand assets directory

**Brand Assets:**

1. **Jata Negara (Malaysian Coat of Arms)**:

   - File: `public/images/jata-negara.svg`
   - Minimum size: 48x48px
   - Placement: Top-left of all public pages
   - Alt text: "Jata Negara Malaysia"

2. **MOTAC Logo**:

   - File: `public/images/motac-logo.png` (120x120)
   - Placement: Header next to Jata Negara
   - Alt text: "Logo Kementerian Pelancongan, Seni dan Budaya Malaysia"

3. **BPM Logo**:

   - File: `public/images/bpm-logo.png`
   - Placement: Footer
   - Alt text: "Logo Bahagian Pengurusan Maklumat"

4. **Favicon and PWA Icons**:
   - `favicon.ico` (MOTAC-branded)
   - `web-app-manifest-192x192.png`
   - `web-app-manifest-512x512.png`

**Government Header Component:**

- Display Jata Negara (48x48 minimum)
- Display MOTAC logo (40x40)
- Display ministry name: "Kementerian Pelancongan, Seni dan Budaya Malaysia"
- Display division name: "Bahagian Pengurusan Maklumat"
- Responsive layout (hide text on mobile, show on sm+)
- Bahasa Melayu sahaja (v3.6.0)

**MOTAC Footer Component:**

- Display BPM logo
- Display copyright notice
- Display contact information
- Display privacy policy link
- Display terms of service link
- Display accessibility statement link
- Bilingual support

**Color Palette (MOTAC Brand Guidelines):**

- Primary: `#1E3A8A` (MOTAC Blue)
- Secondary: `#DC2626` (MOTAC Red)
- Accent: `#F59E0B` (MOTAC Gold)
- Neutral: `#6B7280` (Gray)
- Success: `#10B981` (Green)
- Warning: `#F59E0B` (Amber)
- Error: `#EF4444` (Red)

**Typography:**

- Headings: Inter (sans-serif)
- Body: Inter (sans-serif)
- Monospace: JetBrains Mono (code blocks)

**Pertimbangan Rekabentuk:**

- **Brand consistency**: Follow MOTAC Brand Guidelines 2024
- **Accessibility**: All logos with proper alt text
- **Responsive**: Logos scale appropriately on mobile
- **Performance**: SVG for vector graphics, optimized PNG for raster
- **Localization**: Bahasa Melayu text for all branding elements (v3.6.0)

---

## 5. REKABENTUK PANGKALAN DATA (Database Design)

### 5.1. Database Architecture Overview

#### Database Engine

MySQL 8.0

#### Character Set

utf8mb4 (full Unicode support)

#### Collation

utf8mb4_unicode_ci

#### Storage Engine

InnoDB (ACID compliance, foreign key support)

#### Design Principles

- **Guest-First Schema**: Submitter/applicant data stored as fields, not foreign keys
- **Audit Trail**: Comprehensive logging via `activity_log` and `loan_audits`
- **Token Security**: SHA-512 hashed tokens for approval and status checking
- **Data Quality**: ISO 8000 compliance with validation constraints
- **Privacy**: PDPA compliance with encryption for sensitive fields

### 5.2. Core Tables Summary

| Table                  | Purpose                       | Key Fields                                       | Relationships                                    |
| ---------------------- | ----------------------------- | ------------------------------------------------ | ------------------------------------------------ |
| `users`                | Admin/superuser accounts only | id, email, role, password                        | → helpdesk_tickets, loan_transactions            |
| `divisions`            | MOTAC organizational units    | id, code, name                                   | Referenced by submitter/applicant division codes |
| `helpdesk_tickets`     | Helpdesk ticket records       | ticket*number, submitter*\*, status              | ← helpdesk_comments, helpdesk_attachments        |
| `helpdesk_comments`    | Ticket comments               | ticket_id, admin_id, body                        | → helpdesk_tickets, users                        |
| `helpdesk_attachments` | Ticket file uploads           | ticket_id, path, checksum                        | → helpdesk_tickets                               |
| `loan_applications`    | Asset loan applications       | reference, applicant\_\*, status                 | ← loan_items, loan_transactions, loan_approvals  |
| `loan_items`           | Assets in loan application    | loan_application_id, asset_id                    | → loan_applications, assets                      |
| `loan_transactions`    | Asset check-out/check-in      | loan_application_id, type, performed_by_admin_id | → loan_applications, users                       |
| `loan_approvals`       | Email approval decisions      | loan_application_id, approver_email, decision    | → loan_applications                              |
| `loan_audits`          | Loan-specific audit trail     | auditable_type, auditable_id, event              | Polymorphic to loan models                       |
| `status_tokens`        | Guest status checking tokens  | token_hash, reference_type, reference_id         | Polymorphic to tickets/loans                     |
| `audits`               | Model audit trail (owen-it)   | auditable_type, auditable_id, old/new_values     | Polymorphic to all models                        |
| `activity_log`         | User activity log (spatie)    | description, subject, causer, properties         | Polymorphic (Spatie)                             |
| `departments`          | MOTAC organizational units    | id, code, name, parent_id                        | → users.department_id                            |
| `assets`               | ICT asset inventory           | asset_code, name, status, category_id            | ← loan_items                                     |
| `asset_categories`     | Asset categorization          | id, name, description                            | → assets                                         |

### 5.3. Guest-First Data Model

#### Helpdesk Tickets (`helpdesk_tickets`)

```sql
CREATE TABLE helpdesk_tickets (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    ticket_number VARCHAR(20) UNIQUE NOT NULL,

    -- Hybrid: nullable user_id FK + guest tracking
    user_id BIGINT UNSIGNED NULL,
    submitter_name VARCHAR(255) NOT NULL,
    submitter_email VARCHAR(255) NOT NULL,
    submitter_phone VARCHAR(50) NOT NULL,
    submitter_division_code VARCHAR(20) NOT NULL,
    submitter_grade VARCHAR(50) NULL,

    -- Ticket details
    category VARCHAR(100) NOT NULL,
    priority ENUM('LOW','MEDIUM','HIGH','CRITICAL') NOT NULL,
    description TEXT NOT NULL,
    asset_tag VARCHAR(100) NULL,
    declaration BOOLEAN NOT NULL DEFAULT FALSE,

    -- Status tracking
    status ENUM('OPEN','IN_PROGRESS','AWAITING_INFO','RESOLVED','CLOSED') NOT NULL DEFAULT 'OPEN',
    assigned_admin_id BIGINT UNSIGNED NULL,
    sla_due_at TIMESTAMP NOT NULL,
    closed_at TIMESTAMP NULL,

    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    INDEX idx_ticket_number (ticket_number),
    INDEX idx_status (status),
    INDEX idx_user (user_id),
    INDEX idx_assigned_admin (assigned_admin_id),
    INDEX idx_sla_due (sla_due_at),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL,
    FOREIGN KEY (assigned_admin_id) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

**ERD Relationship**: `users` (0..1) → `helpdesk_tickets` (nullable FK)

#### Loan Applications (`loan_applications`)

```sql
CREATE TABLE loan_applications (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    reference VARCHAR(20) UNIQUE NOT NULL,

    -- Hybrid: nullable user_id FK + guest tracking
    user_id BIGINT UNSIGNED NULL,
    applicant_name VARCHAR(255) NOT NULL,
    applicant_email VARCHAR(255) NOT NULL,
    applicant_phone VARCHAR(50) NOT NULL,
    applicant_division_code VARCHAR(20) NOT NULL,
    applicant_grade VARCHAR(50) NOT NULL,

    -- Loan details
    purpose TEXT NOT NULL,
    location VARCHAR(255) NOT NULL,
    loan_start_date DATE NOT NULL,
    loan_end_date DATE NOT NULL,
    acknowledgement BOOLEAN NOT NULL DEFAULT FALSE,

    -- Status tracking
    status ENUM(
        'PENDING_SUPERVISOR_APPROVAL',
        'APPROVED',
        'REJECTED',
        'AWAITING_COLLECTION',
        'ON_LOAN',
        'RETURNED',
        'DAMAGED'
    ) NOT NULL DEFAULT 'PENDING_SUPERVISOR_APPROVAL',

    -- Token-based approval
    approval_token_hash VARCHAR(128) NULL,
    approval_token_expires_at TIMESTAMP NULL,

    -- Status checking token
    status_token_hash VARCHAR(128) NULL,

    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    INDEX idx_reference (reference),
    INDEX idx_status (status),
    INDEX idx_user (user_id),
    INDEX idx_approval_token (approval_token_hash),
    INDEX idx_status_token (status_token_hash),
    INDEX idx_loan_dates (loan_start_date, loan_end_date),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

**ERD Relationship**: `users` (0..1) → `loan_applications` (nullable FK)

### 5.4. Token-Based Security Tables

#### Loan Approvals (`loan_approvals`)

```sql
CREATE TABLE loan_approvals (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    loan_application_id BIGINT UNSIGNED NOT NULL,

    -- Approver information (no FK to users - external approver)
    approver_email VARCHAR(255) NOT NULL,
    approver_grade VARCHAR(50) NOT NULL,

    -- Decision details
    decision ENUM('APPROVED','REJECTED') NOT NULL,
    remarks TEXT NULL,
    decision_at TIMESTAMP NOT NULL,
    decision_ip_hash VARCHAR(128) NOT NULL,

    -- Token security
    token_hash VARCHAR(128) NOT NULL,

    -- Metadata
    metadata JSON NULL,

    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    INDEX idx_loan_application (loan_application_id),
    INDEX idx_token_hash (token_hash),
    INDEX idx_decision_at (decision_at),
    FOREIGN KEY (loan_application_id) REFERENCES loan_applications(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

#### Status Tokens (`status_tokens`)

```sql
CREATE TABLE status_tokens (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    token_hash VARCHAR(128) UNIQUE NOT NULL,

    -- Polymorphic relationship
    reference_type VARCHAR(50) NOT NULL,
    reference_id BIGINT UNSIGNED NOT NULL,

    expires_at TIMESTAMP NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    INDEX idx_token_hash (token_hash),
    INDEX idx_reference (reference_type, reference_id),
    INDEX idx_expires_at (expires_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

### 5.5. Admin & User Management

#### Users Table (`users`)

```sql
CREATE TABLE users (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    phone VARCHAR(30) NOT NULL,
    department_id BIGINT UNSIGNED NULL,
    grade VARCHAR(50) NULL,
    role ENUM('staff','admin','superuser') NOT NULL,
    password VARCHAR(255) NOT NULL,
    two_factor_secret TEXT NULL,
    remember_token VARCHAR(100) NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    INDEX idx_email (email),
    INDEX idx_role (role),
    INDEX idx_department (department_id),
    INDEX idx_user_submissions (id),
    FOREIGN KEY (department_id) REFERENCES departments(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

> **Note Hybrid v3.4.0**: This table contains:
>
> - **Admin/Superuser**: Full system access (role='admin' or 'superuser')
> - **Staff**: MOTAC staff with optional login (role='staff', with department_id/grade for profile)
> - **Guest**: Not stored in users table; submissions with user_id=NULL
>
> **Staff Capabilities**: view-own-history, edit-profile, access-dashboard, submit-as-authenticated

### 5.6. Asset Management Tables

#### Assets (`assets`)

```sql
CREATE TABLE assets (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    asset_code VARCHAR(50) UNIQUE NOT NULL,
    name VARCHAR(255) NOT NULL,
    description TEXT NULL,
    category_id BIGINT UNSIGNED NOT NULL,
    status ENUM('available','reserved','on_loan','maintenance','retired') NOT NULL DEFAULT 'available',
    qr_code VARCHAR(255) NULL,
    purchase_date DATE NULL,
    warranty_expiry DATE NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    INDEX idx_asset_code (asset_code),
    INDEX idx_status (status),
    INDEX idx_category (category_id),
    FOREIGN KEY (category_id) REFERENCES asset_categories(id) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

#### Loan Transactions (`loan_transactions`)

```sql
CREATE TABLE loan_transactions (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    loan_application_id BIGINT UNSIGNED NOT NULL,
    type ENUM('CHECK_OUT','CHECK_IN') NOT NULL,
    performed_by_admin_id BIGINT UNSIGNED NOT NULL,
    performed_at TIMESTAMP NOT NULL,
    condition_notes TEXT NULL,
    attachments_json JSON NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    INDEX idx_loan_application (loan_application_id),
    INDEX idx_performed_by (performed_by_admin_id),
    INDEX idx_type (type),
    FOREIGN KEY (loan_application_id) REFERENCES loan_applications(id) ON DELETE CASCADE,
    FOREIGN KEY (performed_by_admin_id) REFERENCES users(id) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

### 5.7. Audit & Logging Tables

#### Activity Log (`activity_log` - Laravel Auditing 14.x)

```sql
CREATE TABLE activity_log (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    log_name VARCHAR(255) NULL,
    description TEXT NOT NULL,
    subject_type VARCHAR(255) NULL,
    subject_id BIGINT UNSIGNED NULL,
    causer_type VARCHAR(255) NULL,
    causer_id BIGINT UNSIGNED NULL,
    properties JSON NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    INDEX idx_subject (subject_type, subject_id),
    INDEX idx_causer (causer_type, causer_id),
    INDEX idx_log_name (log_name)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

#### Loan Audits (`loan_audits`)

```sql
CREATE TABLE loan_audits (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    auditable_type VARCHAR(255) NOT NULL,
    auditable_id BIGINT UNSIGNED NOT NULL,
    event VARCHAR(50) NOT NULL,
    old_values JSON NULL,
    new_values JSON NULL,
    user_id BIGINT UNSIGNED NULL,
    ip_address VARCHAR(45) NULL,
    user_agent TEXT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    INDEX idx_auditable (auditable_type, auditable_id),
    INDEX idx_user (user_id),
    INDEX idx_event (event)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

### 5.8. Data Quality Standards

#### Validation Rules

- **Email**: RFC 5322 compliant, government domain whitelist
- **Phone**: Malaysian format validation (+60 or 0)
- **Dates**: ISO 8601 format, logical date ranges
- **Enums**: Strict type checking, no invalid values
- **Tokens**: SHA-512 hashing, minimum 32 bytes entropy

#### Integrity Constraints

- **Foreign Keys**: CASCADE on delete for dependent records, RESTRICT for critical references
- **Unique Constraints**: `ticket_number`, `reference`, `asset_code`, `email` (users)
- **NOT NULL**: All mandatory fields enforced at database level
- **Check Constraints**: Date ranges (loan_end_date > loan_start_date)

#### Encryption

- **At Rest**: MySQL encryption for sensitive fields (phone, email in some contexts)
- **In Transit**: TLS 1.3 for all database connections
- **Application Level**: Laravel encryption for two_factor_secret

### 5.9. Indexing Strategy

#### Primary Indexes

- All tables: `id` (PRIMARY KEY, AUTO_INCREMENT)
- Unique indexes: `ticket_number`, `reference`, `asset_code`, `email`

#### Performance Indexes

- `helpdesk_tickets`: status, assigned_admin_id, sla_due_at
- `loan_applications`: status, approval_token_hash, loan_dates
- `loan_approvals`: token_hash, decision_at
- `assets`: status, category_id
- `activity_log`: subject (type, id), causer (type, id)

#### Query Optimization

- Composite indexes for common WHERE clauses
- Covering indexes for frequently accessed columns
- Regular ANALYZE TABLE for statistics updates

### 5.10. Backup & Recovery

#### Backup Strategy

- **Daily**: Full MySQL dump (compressed)
- **Hourly**: Binary log backups (point-in-time recovery)
- **Weekly**: S3/MinIO snapshot for attachments
- **Retention**: 30 days for daily, 7 days for hourly

#### Recovery Procedures

- **RTO (Recovery Time Objective)**: < 4 hours
- **RPO (Recovery Point Objective)**: < 1 hour
- **Testing**: Bi-annual disaster recovery drills

### 5.11. Migration Notes

#### Version 3.4.0 Changes (Hybrid Architecture)

- **Restored** nullable `user_id` FK to `helpdesk_tickets` and `loan_applications`
- **Retained** `submitter_*` / `applicant_*` fields for guest tracking
- Added `staff` role to `users` table (alongside admin/superuser)
- Added `division_code` and `grade` columns to `users` for staff profiles
- ERD updated: `users` (0..1) → `helpdesk_tickets` / `loan_applications`
- Auth::check() logic in controllers for auto-fill borang

#### Version 3.0.0 Changes

- Added `submitter_*` fields to `helpdesk_tickets` (guest-first)
- Added `applicant_*` fields to `loan_applications` (guest-first)
- Removed `user_id` foreign key from tickets/loans (reverted in v3.4.0)
- Added `approval_token_hash` and `status_token_hash` columns
- Created `loan_approvals` table for email-based approval workflow
- Created `status_tokens` table for guest status checking
- Added indexes for token lookups and date range queries

#### Migration Scripts

Located in `database/migrations/` with Laravel migration system

#### Data Migration

Refer to D05 (Data Migration Plan) and D06 (Data Migration Specification) for legacy data transformation procedures.

---

## 6. REKABENTUK ANTARA MUKA (Interface Design)

### 6.1. Guest Portal UI

#### Layout Structure

- `resources/views/layouts/guest.blade.php` - Base layout untuk tetamu
- Semantic HTML5: `<header>`, `<nav>`, `<main>`, `<footer>`
- ARIA landmarks untuk screen reader navigation
- Skip links untuk keyboard accessibility

**Components:**

- Language switcher (MS/EN) - **DILUMPUHKAN v3.6.0** (Bahasa Melayu sahaja)
- Breadcrumb navigation
- Progress indicators untuk multi-step forms
- Toast notifications (WCAG 2.2 AA compliant)

**Design System (D12-D14):**

- Colors: MOTAC branding dengan 4.5:1 contrast ratio
- Typography: System font stack, responsive sizing
- Spacing: Tailwind CSS 4.1.17 utility classes
- Components: Reusable Blade components

**Status Checking Page:**

- Timeline visualization untuk ticket/loan progress
- Status badges dengan color coding
- Important dates highlighted
- Download receipt/confirmation (PDF)

### 6.2. Admin Panel UI (Filament)

**Filament 4.1.10 Customization:**

- MOTAC branding (logo, colors)
- Custom navigation groups
- Dashboard widgets dengan real-time data
- Dark mode support

**Resources:**

- Table views dengan filters, sorting, search
- Form views dengan validation
- Bulk actions untuk mass operations
- Export functionality (CSV, Excel, PDF)

### 6.3. User Experience (UX)

**Form Design:**

- Multi-step wizard untuk complex forms (loan application)
- Real-time validation dengan Livewire
- Inline error messages
- Progress saving (draft functionality)

**Accessibility (WCAG 2.2 AA):**

- Keyboard navigation (Tab, Enter, Escape)
- Screen reader support (ARIA labels, live regions)
- Focus indicators (visible outline)
- Color contrast compliance
- Touch targets minimum 44x44px

**Notifications:**

- Toast notifications: `role="status"`, `aria-live="polite"`
- E-mel notifications: Text + HTML versions
- WebSocket notifications untuk admin (Laravel Echo)

**Responsive Design:**

- Mobile-first approach
- Breakpoints: 640px (sm), 768px (md), 1024px (lg), 1280px (xl)
- Touch-friendly UI untuk mobile devices

---

## 7. REKABENTUK KESELAMATAN (Security Design)

### 7.1. Defense in Depth Strategy

#### Layer 1: Network Security

- WAF (Web Application Firewall) untuk filter malicious traffic
- DDoS protection
- HTTPS/TLS 1.3 (Let's Encrypt)
- Firewall rules (allow only necessary ports)

#### Layer 2: Application Security

- **CSRF Protection**: Token untuk semua POST/PUT/DELETE requests
- **Rate Limiting**: `throttle:guest,60,1` (60 requests per minute untuk guest routes)
- **reCAPTCHA Enterprise**: Spam prevention untuk borang tetamu
- **Input Validation**: Server-side validation menggunakan Laravel Form Requests
- **Output Encoding**: Blade automatic escaping, `{{ }}` untuk user input
- **SQL Injection Prevention**: Eloquent ORM dengan parameterized queries
- **XSS Prevention**: Content Security Policy (CSP) headers

#### Layer 3: Authentication & Authorization

- **Admin Authentication**: Laravel Breeze dengan session-based auth
- **Two-Factor Authentication**: TOTP untuk `superuser` (Google Authenticator)
- **Password Policy**: Minimum 12 characters, complexity requirements
- **Session Management**: Secure cookies, HTTP-only, SameSite=Strict
- **Role-Based Access Control**: `admin` vs `superuser` dengan Laravel Policies

#### Layer 4: Data Security

- **Encryption at Rest**: MySQL encryption untuk sensitive fields
- **Encryption in Transit**: HTTPS/TLS untuk semua komunikasi
- **Token Hashing**: SHA-512 untuk approval tokens dan status tokens
- **Password Hashing**: Bcrypt (Laravel default)
- **File Storage**: S3/MinIO dengan presigned URLs (15 min expiry)
- **File Scanning**: ClamAV untuk virus/malware detection

### 7.2. Token-Based Security

**Approval Tokens:**

- Generated: `Str::random(64)` + SHA-512 hash
- Stored: Hashed version dalam `loan_approvals.token_hash`
- Validity: 72 jam (configurable)
- Signed URL: Laravel signed routes untuk tamper protection
- One-time use: Token invalidated selepas decision

**Status Tokens:**

- Generated: `Str::random(32)` + SHA-512 hash
- Stored: Hashed version dalam `status_tokens.token_hash`
- Validity: 90 hari (configurable)
- Rate limited: 10 checks per hour per token

### 7.3. Audit & Logging

**Audit Trail:**

- `activity_log` table (Laravel Auditing 14.x)
- `loan_audits` table untuk loan-specific events
- Logged events: Create, Update, Delete, Status Change, Approval Decision
- Metadata: User ID, IP hash, timestamp, old/new values

**Security Logging:**

- Failed login attempts
- Rate limit violations
- Invalid token access attempts
- File upload rejections
- CSRF token mismatches

**Log Retention:**

- Application logs: 30 hari
- Audit logs: 7 tahun (compliance requirement)
- Security logs: 1 tahun

### 7.4. Compliance & Standards

- **OWASP ASVS L2**: Application Security Verification Standard Level 2
- **PDPA**: Personal Data Protection Act compliance
- **ISO 27001**: Information security management
- **WCAG 2.2 AA**: Accessibility compliance

---

## 8. REKABENTUK PENYENGGARAAN & PEMANTAUAN (Maintenance & Monitoring Design)

### 8.1. Application Monitoring

**Metrics Collection:**

- **Prometheus**: Collect application metrics (request rate, response time, error rate)
- **Grafana**: Visualize metrics dengan custom dashboards
- **Alertmanager**: Send alerts ke Ops BPM (email, SMS, Slack)

**Key Metrics:**

- Request throughput (requests/sec)
- Response time (p50, p95, p99)
- Error rate (4xx, 5xx)
- Queue job processing time
- Database query performance
- WebSocket connection count

### 8.2. Logging Strategy

**Log Levels:**

- `DEBUG`: Development only (verbose)
- `INFO`: Normal operations (ticket created, loan approved)
- `WARNING`: SLA breaches, slow queries
- `ERROR`: Application errors, failed jobs
- `CRITICAL`: System failures, security incidents

**Log Destinations:**

- `storage/logs/laravel.log` (local, daily rotation)
- ELK Stack (Elasticsearch, Logstash, Kibana) untuk centralized logging
- Sentry untuk error tracking dan alerting

**Structured Logging:**

```php
Log::info('Ticket created', [
    'ticket_number' => $ticket->ticket_number,
    'submitter_email' => $ticket->submitter_email,
    'priority' => $ticket->priority,
    'sla_due_at' => $ticket->sla_due_at,
]);
```

### 8.3. Performance Monitoring

**Frontend Performance:**

- Lighthouse CI untuk automated audits
- Core Web Vitals tracking (LCP, FID, CLS)
- Real User Monitoring (RUM) dengan Google Analytics

**Backend Performance:**

- Laravel Telescope (development)
- Laravel Horizon untuk queue monitoring
- Database query profiling (Laravel Debugbar)
- APM tools (New Relic, Datadog - opsyen)

**Load Testing:**

- k6 untuk load testing (monthly)
- Artillery untuk stress testing
- Target: 100 concurrent users, <2s response time

### 8.4. Configuration Management

**Environment Variables:**

- `.env` file untuk configuration (not committed to git)
- `.env.example` sebagai template
- Laravel config caching untuk production (`php artisan config:cache`)

**Dynamic Configuration:**

- `superuser` boleh update email templates via Filament
- Approval workflow settings (timeout, approver list)
- SLA thresholds per priority level

### 8.5. Maintenance Tasks

**Daily:**

- Database backup (automated)
- Queue worker health check
- Log rotation
- Temporary file cleanup

**Weekly:**

- Expired token cleanup
- Audit log archival
- Performance report generation

**Monthly:**

- Security updates (Laravel, packages)
- Load testing
- Backup restoration test
- SSL certificate renewal check

**Quarterly:**

- Dependency updates
- Security audit
- Disaster recovery drill

---

## 9. REKABENTUK UJIAN (Testing Design)

### 9.1. Unit Testing

**Framework**: PHPUnit 11.5.44

**Test Coverage:**

- Service classes (`HelpdeskService`, `LoanService`, `ApprovalService`)
- Model methods dan relationships
- Helper functions dan utilities
- Validation rules

**Example:**

```php
class HelpdeskServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_create_ticket_generates_unique_number(): void
    {
        $service = app(HelpdeskService::class);
        $ticket = $service->createTicket($validatedData);

        $this->assertMatchesRegularExpression('/^HD-\d{6}-\d{4}$/', $ticket->ticket_number);
    }
}
```

### 9.2. Feature Testing

**Test Scenarios:**

- Guest dapat submit helpdesk ticket
- Guest dapat submit loan application
- Admin dapat approve/reject loan
- Status token berfungsi dengan betul
- Approval token expiry handling
- E-mel notifications dihantar
- File upload dan validation

**Example:**

```php
class HelpdeskTicketTest extends TestCase
{
    public function test_guest_can_submit_ticket(): void
    {
        $response = $this->post('/helpdesk', [
            'submitter_name' => 'Ahmad',
            'submitter_email' => 'ahmad@motac.gov.my',
            // ... other fields
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('helpdesk_tickets', [
            'submitter_email' => 'ahmad@motac.gov.my',
        ]);
    }
}
```

### 9.3. Livewire Component Testing

**Components to Test:**

- `TicketForm` - Helpdesk submission
- `ApplicationForm` - Loan application wizard
- `StatusChecker` - Status checking interface

**Example:**

```php
use Livewire\Livewire;

class TicketFormTest extends TestCase
{
    public function test_validates_required_fields(): void
    {
        Livewire::test(TicketForm::class)
            ->set('submitter_name', '')
            ->call('submit')
            ->assertHasErrors(['submitter_name' => 'required']);
    }
}
```

### 9.4. Browser Testing (E2E)

**Framework**: Playwright 1.56.1

**Test Flows:**

- Complete helpdesk ticket submission flow
- Complete loan application flow
- Approval link workflow (e-mel → decision)
- Status checking workflow
- Admin panel operations

**Example:**

```typescript
test("guest can submit helpdesk ticket", async ({ page }) => {
 await page.goto("/helpdesk/create");
 await page.fill('[name="submitter_name"]', "Ahmad");
 await page.fill('[name="submitter_email"]', "ahmad@motac.gov.my");
 // ... fill other fields
 await page.click('button[type="submit"]');
 await expect(page).toHaveURL(/\/helpdesk\/success/);
});
```

### 9.5. Accessibility Testing

**Tools:**

- axe-core 4.11.0 untuk automated checks
- Lighthouse untuk WCAG audit
- Manual testing dengan screen readers (NVDA, JAWS, VoiceOver)

**Compliance Target**: WCAG 2.2 AA

**Test Areas:**

- Keyboard navigation
- Screen reader compatibility
- Color contrast (4.5:1 minimum)
- Focus indicators
- ARIA labels dan landmarks
- Form labels dan error messages

### 9.6. Performance Testing

**Tools:**

- k6 untuk load testing
- Artillery untuk stress testing
- Lighthouse CI untuk frontend performance

**Performance Targets:**

- Response time: <2s (p95)
- Throughput: 100 concurrent users
- Database queries: <50ms (p95)
- Page load: <3s (LCP)

### 9.7. Security Testing

**Automated:**

- OWASP ZAP untuk vulnerability scanning
- Snyk untuk dependency vulnerabilities
- Laravel Security Checker

**Manual:**

- Token security testing (expiry, tampering)
- CSRF protection verification
- Rate limiting effectiveness
- File upload security (malicious files)
- SQL injection attempts
- XSS attempts

### 9.8. Test Automation

**CI/CD Pipeline:**

- Run unit tests pada setiap commit
- Run feature tests pada setiap PR
- Run E2E tests sebelum deployment
- Run accessibility tests weekly
- Run security scans monthly

**Test Coverage Target**: >80% code coverage

---

## 10. REKABENTUK PENGOPTIMUMAN (Optimization Design)

### 10.1. Frontend Optimization

**Asset Optimization:**

- **Vite 7.0.7**: Modern build tool dengan HMR (Hot Module Replacement)
- **Code Splitting**: Separate bundles untuk guest dan admin
- **Tree Shaking**: Remove unused code
- **Minification**: CSS dan JS minified untuk production
- **Compression**: Brotli (primary) dan Gzip (fallback)

**Image Optimization:**

- Lazy loading: `loading="lazy"` untuk images below fold
- Priority hints: `fetchpriority="high"` untuk hero images
- Responsive images: `srcset` untuk different screen sizes
- Modern formats: WebP dengan JPEG fallback
- CDN delivery (opsyen)

**CSS Optimization:**

- Tailwind CSS 4.1.17 dengan JIT compiler
- PurgeCSS untuk remove unused styles
- Critical CSS inlined untuk above-the-fold content
- Font subsetting untuk reduce font file size

**JavaScript Optimization:**

- Alpine.js 3 untuk lightweight interactivity
- Livewire 3.7.0 untuk server-side rendering
- Defer non-critical scripts
- Preload critical resources

### 10.2. Backend Optimization

**Caching Strategy:**

- **Config Cache**: `php artisan config:cache` (production)
- **Route Cache**: `php artisan route:cache` (production)
- **View Cache**: Blade template compilation caching
- **Query Cache**: Redis caching untuk expensive queries (15 min TTL)
- **OPcache**: PHP opcode caching enabled

**Database Optimization:**

- **Indexes**: Proper indexing untuk common queries
  - `helpdesk_tickets.ticket_number` (unique)
  - `loan_applications.reference` (unique)
  - `loan_approvals.token_hash` (index)
  - `status_tokens.token_hash` (index)
- **Eager Loading**: Prevent N+1 queries dengan `with()`
- **Query Optimization**: Use `select()` untuk limit columns
- **Connection Pooling**: MySQL connection pooling
- **Read Replicas**: Separate read/write connections (opsyen)

**Queue Optimization:**

- **Redis**: Fast queue driver
- **Horizon**: Queue monitoring dan management
- **Job Batching**: Process multiple jobs efficiently
- **Failed Job Handling**: Automatic retry dengan exponential backoff

### 10.3. Application Performance

**Laravel Optimization:**

- **Autoloader Optimization**: `composer dump-autoload -o`
- **Route Caching**: Reduce route registration overhead
- **Config Caching**: Eliminate config file parsing
- **Event Caching**: Cache event listeners

**Session Optimization:**

- Redis untuk session storage (fast read/write)
- Session lifetime: 120 minutes
- Garbage collection: Automatic cleanup

**API Rate Limiting:**

- Guest routes: 60 requests/minute
- Admin routes: 120 requests/minute
- Status check: 10 requests/hour per token

### 10.4. Network Optimization

**HTTP/2:**

- Multiplexing untuk parallel requests
- Server push untuk critical resources
- Header compression

**CDN (Optional):**

- CloudFlare untuk static assets
- Edge caching untuk reduce latency
- DDoS protection

**DNS:**

- DNS prefetch untuk external domains
- Preconnect untuk critical origins

### 10.5. Performance Targets

**Core Web Vitals:**

- **LCP (Largest Contentful Paint)**: <2.5s
- **FID (First Input Delay)**: <100ms
- **CLS (Cumulative Layout Shift)**: <0.1

**Backend Performance:**

- Response time (p95): <2s
- Database queries (p95): <50ms
- Queue job processing: <5s

**Lighthouse Score:**

- Performance: >90
- Accessibility: 100
- Best Practices: >90
- SEO: >90

---

## 11. LAMPIRAN (Appendices)

### 11.1. Diagram Senibina Sistem

**Tersedia di:**

- `design/architecture/system-context-diagram.png` - High-level system context
- `design/architecture/component-diagram.png` - Component relationships
- `design/architecture/deployment-diagram.png` - Infrastructure layout
- `design/architecture/guest-flow-diagram.png` - Guest user journey
- `design/architecture/approval-flow-diagram.png` - Approval workflow

### 11.2. Technology Stack Summary

| Category               | Technology        | Version | Purpose                     |
| ---------------------- | ----------------- | ------- | --------------------------- |
| Backend Framework      | Laravel           | 12.40.1 | Application framework       |
| Frontend Framework     | Livewire          | 3.7.0   | Reactive components         |
| Single-File Components | Volt              | 1.10.1  | Simplified Livewire syntax  |
| JavaScript Framework   | Alpine.js         | 3       | Lightweight interactivity   |
| CSS Framework          | Tailwind CSS      | 4.1.17  | Utility-first styling       |
| Admin Panel            | Filament          | 4.1.10  | CRUD interface              |
| Build Tool             | Vite              | 7.0.7   | Asset bundling              |
| WebSocket Server       | Laravel Reverb    | 1.6.2   | Real-time communication     |
| WebSocket Client       | Laravel Echo      | 2.2.6   | Client-side WebSocket       |
| Database               | MySQL             | 8.0     | Relational database         |
| Cache/Queue            | Redis             | 7.0     | In-memory data store        |
| Testing                | PHPUnit           | 11.5.44 | Unit/Feature testing        |
| E2E Testing            | Playwright        | 1.56.1  | Browser automation          |
| Accessibility Testing  | axe-core          | 4.11.0  | WCAG compliance             |
| Audit (Compliance)     | Laravel Auditing  | 14.x    | Field-level audit (owen-it) |
| Audit (Operations)     | Activity Log      | 4.x     | User activity (spatie)      |
| Debugging              | Laravel Telescope | 5.x     | Monitoring (superuser only) |
| Permissions            | Spatie Permission | 6.23    | Role-based access control   |

### 11.3. Checklist Verifikasi Reka Bentuk

**Hybrid Architecture:**

- [ ] Borang accessible dengan atau tanpa login
- [ ] Nullable user_id FK + guest tracking columns
- [ ] Auth::check() logic untuk auto-fill borang
- [ ] Token-based approval workflow implemented
- [ ] Status checking dengan token berfungsi
- [ ] Staff role untuk optional login (Dashboard/Profile)

**Security:**

- [ ] CSRF protection enabled
- [ ] Rate limiting configured
- [ ] reCAPTCHA Enterprise integrated
- [ ] Token hashing (SHA-512) implemented
- [ ] File upload security (virus scanning)
- [ ] Signed URLs untuk approval links

**Accessibility (WCAG 2.2 AA):**

- [ ] Keyboard navigation support
- [ ] Screen reader compatibility
- [ ] Color contrast 4.5:1 minimum
- [ ] ARIA labels dan landmarks
- [ ] Focus indicators visible
- [ ] Form labels dan error messages

**Performance:**

- [ ] Core Web Vitals targets met
- [ ] Database queries optimized
- [ ] Caching strategy implemented
- [ ] Asset optimization (minification, compression)
- [ ] Lazy loading untuk images

**Admin Panel:**

- [ ] Filament 4.1.10 configured
- [ ] Role-based access control (admin vs superuser)
- [ ] Audit trail logging
- [ ] Dashboard widgets functional
- [ ] Real-time notifications (WebSocket)

**Testing:**

- [ ] Unit tests >80% coverage
- [ ] Feature tests untuk critical flows
- [ ] E2E tests untuk user journeys
- [ ] Accessibility tests passing
- [ ] Security tests completed

### 11.4. Rujukan Standard

- **ISO/IEC/IEEE 42010**: Architecture description standard
- **ISO/IEC/IEEE 15288**: Systems and software engineering
- **WCAG 2.2 AA**: Web Content Accessibility Guidelines
- **OWASP ASVS L2**: Application Security Verification Standard
- **PSR-12**: PHP coding style guide
- **Semantic Versioning**: Version numbering (SemVer)

---

## 12. PENUTUP

Rekabentuk ini memastikan ICTServe mematuhi mandat **hybrid architecture**:

- **Staf** boleh mendaftar sendiri dengan e-mel @motac.gov.my dan log masuk (Laravel Breeze) untuk Dashboard/Profile ATAU gunakan borang tetamu
- **Database** menyokong nullable `user_id` FK untuk link submissions ke user accounts
- **Guest tracking** kekal via `submitter_*` / `applicant_*` columns untuk non-auth users
- **Kelulusan** diproses melalui token-based e-mel workflow (signed URLs)
- **Status** boleh disemak menggunakan unique tokens
- **Pentadbir** menguruskan operations melalui Filament panel dengan role-based access (admin/superuser only)
- **Audit trail** lengkap untuk compliance dan security
- **Accessibility** (WCAG 2.2 AA) dan **performance** (Core Web Vitals) diutamakan

Semua komponen yang dihurai dalam dokumen ini mesti:

1. Diuji secara menyeluruh (unit, feature, E2E, accessibility, security)
2. Dipantau secara berterusan (metrics, logs, alerts)
3. Didokumentasikan dengan lengkap (code comments, API docs, user guides)
4. Diaudit secara berkala (security audits, performance reviews)

Rujuk dokumen berkaitan (D00-D15) untuk maklumat lanjut mengenai requirements, implementation, dan standards compliance.
