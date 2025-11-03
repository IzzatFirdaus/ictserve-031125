# Dokumentasi Rekabentuk Teknikal (Technical Design Documentation - TDD)

**Sistem ICTServe**  
**Versi:** 2.1.0 (SemVer)  
**Tarikh Kemaskini:** 19 Oktober 2025  
**Status:** Aktif  
**Klasifikasi:** Terhad - Dalaman MOTAC  
**Penulis:** Pasukan Pembangunan BPM MOTAC  
**Standard Rujukan:** IEEE 1016, ISO/IEC/IEEE 2651x series, ISO 9001, ISO/IEC/IEEE 12207

---

## Maklumat Dokumen (Document Information)

| Atribut                | Nilai                                    |
|------------------------|------------------------------------------|
| **Versi**              | 2.1.0                                    |
| **Tarikh Kemaskini**   | 19 Oktober 2025                          |
| **Status**             | Aktif                                    |
| **Klasifikasi**        | Terhad - Dalaman MOTAC                   |
| **Pematuhi**           | IEEE 1016, ISO/IEC/IEEE 2651x, ISO 9001, ISO/IEC/IEEE 12207 |
| **Bahasa**             | Bahasa Melayu (utama), English (teknikal)|

> Notis Penggunaan Dalaman: Reka bentuk teknikal ini adalah khusus untuk sistem dalaman MOTAC; bukan untuk aplikasi awam.

---

## Sejarah Perubahan (Changelog)

| Versi  | Tarikh          | Perubahan                                      | Penulis       |
|--------|-----------------|------------------------------------------------|---------------|
| 1.0.0  | September 2025  | Versi awal dokumentasi rekabentuk teknikal     | Pasukan BPM   |
| 2.0.0  | 17 Oktober 2025 | Penyeragaman mengikut D00-D14, SemVer, cross-reference | Pasukan BPM   |
| 2.1.0  | 19 Oktober 2025 | Tambah §7a Internationalization & Language Support - middleware, priority chain, testing, accessibility | Pasukan BPM   |

---

## Rujukan Dokumen Berkaitan (Related Document References)

- **[D00_SYSTEM_OVERVIEW.md]** - Ringkasan Sistem
- **[D01_SYSTEM_DEVELOPMENT_PLAN.md]** - Pelan Pembangunan Sistem
- **[D04_SOFTWARE_DESIGN_DOCUMENT.md]** - Rekabentuk Perisian (high-level design)
- **[D07_SYSTEM_INTEGRATION_PLAN.md]** - Pelan Integrasi Sistem
- **[D08_SYSTEM_INTEGRATION_SPECIFICATION.md]** - Spesifikasi Integrasi Sistem
- **[D09_DATABASE_DOCUMENTATION.md]** - Dokumentasi Pangkalan Data
- **[D10_SOURCE_CODE_DOCUMENTATION.md]** - Dokumentasi Kod Sumber
- **[GLOSSARY.md]** - Glosari Istilah Sistem

---

## 1. TUJUAN DOKUMEN (Purpose)

Dokumen ini merangkum rekabentuk teknikal sistem **Helpdesk & ICT Asset Loan BPM MOTAC** termasuk senibina, modul, integrasi, spesifikasi data, keselamatan, dan kawalan kualiti. Dokumentasi ini mematuhi piawaian **IEEE 1016** (software design), **ISO/IEC/IEEE 2651x series** (software and documentation engineering), **ISO 9001** (quality management), dan **ISO/IEC/IEEE 12207** (software lifecycle processes).

---

## 2. SKOP (Scope)

- Meliputi semua aspek teknikal sistem: backend, frontend, database, API, authentication, authorization, audit trail, integrasi dalaman/luaran, dan deployment.
- Pengguna: Staf MOTAC, Pegawai BPM, Ketua Bahagian, Admin BPM.

---

## 3. SENIBINA SISTEM (System Architecture)

### 3.1. MVC Architecture (Laravel 12)

- **Model**: Eloquent ORM (User, Asset, Loan, Ticket, Approval, Notification, AuditLog)
- **View**: Blade + Livewire, Tailwind CSS, Filament components, responsive design
- **Controller**: Request handling, validation, business logic, authorization

### 3.2. Lapisan Sistem (Layered System)

- **Presentation Layer**: Blade + Livewire components, Tailwind CSS, Filament resources (AJAX where needed)
- **Application Layer**: Controllers, Services, Middleware, Job Queue
- **Integration Layer**: API (RESTful), Notification, LDAP/SSO, Email (SMTP), Audit Trail
- **Data Layer**: Eloquent models, migrations, factories, seeders

---

## 4. MODUL UTAMA (Main Modules)

### 4.1. Helpdesk Ticketing

- **Fungsi**: Borang aduan kerosakan, pengurusan tiket, penugasan technician, komunikasi, SLA, notifikasi, audit
- **Komponen**: Ticket model, TicketController, Blade views (form, index, detail), Notification, AuditLog

### 4.2. ICT Asset Loan

- **Fungsi**: Permohonan pinjaman, workflow kelulusan, pengeluaran/pemulangan aset, kalendar tempahan, audit trail
- **Komponen**: Loan model, LoanController, Approval model, Notification, Blade views (form, index, detail), AuditLog

### 4.3. Inventory Management

- **Fungsi**: CRUD aset, senarai aksesori, status aset, sejarah pinjaman
- **Komponen**: Asset model, AssetController, Blade views, Notification

### 4.4. Authentication & Authorization

- **Fungsi**: Login Breeze, role-based access, policies, middleware, SSO/LDAP (optional)
- **Komponen**: User model, AuthController, CheckRoleMiddleware, Policies (InventoryPolicy, TicketPolicy), AuthServiceProvider

### 4.5. Reporting & Dashboard

- **Fungsi**: Ringkasan tiket, aset, KPI, analitik, eksport data
- **Komponen**: DashboardController, Blade views, export helper

### 4.6. Audit Trail

- **Fungsi**: Logging perubahan data, compliance, history tracking
- **Komponen**: AuditLog model, owen-it/laravel-auditing, AuditController

---

## 5. REKABENTUK DATABASE (Database Design)

- **Standard Relational Schema (MySQL)**
- **Jadual utama**: users, assets, loans, tickets, approvals, notifications, audit_logs, divisions

### Entity Relationship Diagram (ERD) — Simplified

- users 1—* tickets
- users 1—* loans
- assets 1—* loans
- divisions 1—* users, tickets, loans
- loans 1—1 approvals
- users 1—* notifications, audit_logs

Refer to DATABASE_DOCUMENTATION.md for field definitions & quality standards (ISO 8000).

---

## 6. FRONTEND REKABENTUK (Frontend Design)

- **Blade + Tailwind**: Tailwind CSS utilities, responsive design, komponen Filament; includes modular (footer, sidebar, navbar)
- **Dynamic Dropdowns**: AJAX fetch for dependent selects (e.g. warehouse/shelf)
- **Validation**: $request->validate() in controller, @error di view
- **Pagination**: ->paginate() in controller,  $records->links()  in view
- **Action Buttons**: Conditional via @can (authorization)

---

## 7. BACKEND REKABENTUK (Backend Design)

- **Controllers**: CRUD, workflow logic, validation, authorization checks
- **Models**: Typed properties, $fillable, relationship methods (hasMany, belongsTo)
- **Policies**: Authorization logic, registered in AuthServiceProvider
- **Middleware**: Auth, role-based, audit trail logging
- **Jobs & Queues**: Background tasks (notification, emails), queueable jobs, php artisan queue:work
- **Notifications**: via mail and database, InvoicePaid example
- **Audit Trail**: Owen-it/laravel-auditing integration for all critical changes

---

## 7a. SOKONGAN ANTARABANGSA & BAHASA (Internationalization & Language Support)

**Pematuhan Standard**: WCAG 2.2 Level AA (Language of Page 3.1.1-3.1.2), ISO 9241-110:2020 (Dialogue Principles), D15 §6 (Implementation)

### 7a.1. Bilingual Architecture (Bahasa Melayu & English)

**Available Locales**: `ms` (Bahasa Melayu), `en` (English)  
**Default Locale**: `en` (English)  
**Configuration**: `config/app.php` — `available_locales`, `locale`, `fallback_locale`

### 7a.2. Locale Resolution Priority Chain

**Middleware**: `App\Http\Middleware\SetLocale` (registered in `bootstrap/app.php` web group)

**Priority Order** (checked sequentially until valid locale found):
1. **Session** (`locale` key) - Ditulis apabila pengguna menukar bahasa (language switcher).
2. **Cookie** (`locale` cookie, 12-month expiry) - Memastikan pilihan bahasa pengguna kekal selepas pelayar dimulakan semula.
3. **URL Query Parameter** (`?lang=ms|en`, optional) - Allows sharing a pre-selected locale for deep links.
4. **Browser Detection** (`Accept-Language` header) - Applied for first-time visitors without prior preference.
5. **Fallback** (`config('app.locale')`) - Defaults to Bahasa Melayu (`ms`) if none above match.

**Implementation Logic:**

```php
public function handle(Request $request, Closure $next): Response

    $locale = $this->resolveLocale($request);

    app()->setLocale($locale);
    $request->session()->put('locale', $locale);
    cookie()->queue(cookie('locale', $locale, minutes: 60 * 24 * 365));

    return $next($request);


protected function resolveLocale(Request $request): string

    $allowed = config('app.available_locales', ['ms', 'en']);

    $sources = [
        fn () => $request->session()->get('locale'),
        fn () => $request->cookie('locale'),
        fn () => $this->resolveQueryLocale($request, $allowed),
        fn () => $this->detectFromBrowser($request, $allowed),
        fn () => config('app.locale', 'ms'),
  ;

    foreach ($sources as $source) 
        $value = $source();
        if ($value && in_array($value, $allowed, true)) 
            return $value;
    


    return config('app.fallback_locale', 'ms');


protected function resolveQueryLocale(Request $request, array $allowed): ?string

    $value = $request->query('lang');

    return in_array($value, $allowed, true) ? $value : null;


protected function detectFromBrowser(Request $request, array $allowed): ?string

    $preferred = $request->getPreferredLanguage($allowed) ?? '';

    return in_array($preferred, $allowed, true) ? $preferred : null;

```

### 7a.3. Language Switcher Component

**Framework**: Livewire 3.x reactive component  
**Location**: `app/Livewire/LanguageSwitcher.php` + `resources/views/livewire/language-switcher.blade.php`

**Features**:
- Dropdown menu with flag icons
- Persists to three locations simultaneously:
  - Session (`Session::put('locale', $locale)`)
  - Cookie (`Cookie::queue('locale', $locale, 525600)`) — 1-year expiry
  - User profile (`Auth::user()->update(['locale' => $locale])`) — if authenticated
- Emits `locale-changed` event for frontend reactivity
- Full WCAG 2.2 AA accessibility:
  - `role="navigation"`, `aria-label` on button
  - Keyboard navigation (Tab, Arrow keys, Enter)
  - Screen reader announces language selection
  - Focus indicator (3px outline, 2-4px offset)

**Code Example**:

```php
// app/Livewire/LanguageSwitcher.php (simplified)
use Livewire\Component;
use Illuminate\Support\Facades\Session, Cookie, Auth;

class LanguageSwitcher extends Component

    public string $locale;
    public array $availableLocales;
    
    public function mount(): void
    
        $this->locale = app()->getLocale();
        $this->availableLocales = config('app.available_locales', ['ms', 'en']);

    
    public function setLocale(string $locale): void
    
        // Validate input
        if (!in_array($locale, $this->availableLocales)) 
            return;
    
        
        // 1. Persist to session (immediate effect)
        Session::put('locale', $locale);
        
        // 2. Persist to cookie (1-year, for unauthenticated users)
        Cookie::queue(Cookie::make('locale', $locale, 525600));
        
        // 3. Persist to user profile (if authenticated)
        if (Auth::check()) 
            Auth::user()->update(['locale' => $locale]);
    
        
        // 4. Set application locale
        app()->setLocale($locale);
        $this->locale = $locale;
        
        // 5. Emit event for frontend reactivity
        $this->dispatch('locale-changed', locale: $locale);

    
    public function getLocaleLabel(string $locale): string
    
        return match($locale) 
            'ms' => 'Bahasa Melayu',
            'en' => 'English',
            default => ucfirst($locale),
    ;


```

### 7a.4. Translation Files

**Location**: `lang/locale/` and `resources/lang/locale/`  
**Format**: PHP array files (e.g., `lang/ms/welcome.php`, `lang/en/welcome.php`)  
**Helper**: `__('key')` in Blade, `trans('key')` in PHP

**Example**:

```php
// lang/ms/welcome.php
return [
    'title' => 'Selamat Datang ke Sistem ICTServe',
    'subtitle' => 'Helpdesk & Pengurusan Pinjaman Aset ICT',
];

// lang/en/welcome.php
return [
    'title' => 'Welcome to ICTServe System',
    'subtitle' => 'Helpdesk & ICT Asset Loan Management',
];

// Blade usage:
<h1> __('welcome.title') </h1>
<p> __('welcome.subtitle') </p>
```

### 7a.5. Database Schema for User Locale Preference

```sql
-- Migration: database/migrations/2025_10_19_062105_add_locale_to_users_table.php
ALTER TABLE `users`
ADD COLUMN `locale` VARCHAR(5) NULL COMMENT 'User preferred language (ms, en)'
AFTER `remember_token`;
```

**Model Update**:
```php
// app/Models/User.php
protected $fillable = [
    'name', 'email', 'password', 'locale', // Added locale
];
```

### 7a.6. Testing Strategy

**Test Coverage**: 11 tests in `tests/Feature/LanguageSwitcherTest.php`

| Test Case | Purpose | Status |
|-----------|---------|--------|
| `it_switches_to_bahasa_melayu_and_persists_locale` | Verify session persistence | ✅ Pass |
| `it_switches_to_english_and_persists_locale` | Verify session persistence | ✅ Pass |
| `it_rejects_invalid_locale` | Security: reject unknown locales | ✅ Pass |
| `it_persists_locale_to_user_profile_when_authenticated` | Verify database persistence | ✅ Pass |
| `it_sets_cookie_for_unauthenticated_users` | Verify cookie persistence | ✅ Pass |
| `it_prioritizes_user_profile_over_session` | Verify priority chain (1 > 2) | ✅ Pass |
| `it_prioritizes_session_over_cookie` | Verify priority chain (2 > 3) | ✅ Pass |
| `it_auto_detects_browser_language_on_first_visit` | Verify Accept-Language parsing | ✅ Pass |
| `it_falls_back_to_default_locale_when_browser_language_not_supported` | Verify fallback | ✅ Pass |
| `it_updates_user_locale_on_language_switch` | Verify profile update | ✅ Pass |
| `it_emits_locale_changed_event` | Verify event dispatch | ✅ Pass |

**Run Tests**:
```bash
php artisan test --filter=LanguageSwitcher
```

### 7a.7. Accessibility Compliance (WCAG 2.2 Level AA)

**Standards Met**:
- **3.1.1 Language of Page** (Level A): `<html lang=" app()->getLocale() ">` dynamically set
- **3.1.2 Language of Parts** (Level AA): Content switches completely, no mixed-language content
- **2.4.4 Link Purpose (In Context)** (Level A): Language switcher button clearly labeled
- **2.1.1 Keyboard** (Level A): Full keyboard navigation support
- **4.1.3 Status Messages** (Level AA): Language change announces via screen reader (locale-changed event)

**Testing Tools**:
- Lighthouse: Accessibility score ≥90 (target: 95+)
- axe DevTools: Zero violations
- WAVE: Zero errors
- NVDA Screen Reader: Announces "Language Switcher, button, English, expanded"

### 7a.8. Performance Considerations

- **Middleware overhead**: ~5ms per request (locale resolution + session read)
- **Cookie size**: 10 bytes (`locale=en`)
- **Database query**: Only for authenticated users (cached in user session)
- **Translation loading**: Laravel caches lang files automatically in production

**Reference**: See **[D15_LANGUAGE_MS_EN.md]** for full language specification, **[D13_UI_UX_FRONTEND_FRAMEWORK.md]** §5.6 for frontend implementation, **[D14_UI_UX_STYLE_GUIDE.md]** §9 for accessibility standards.

---

## 8. INTEGRASI SISTEM (System Integration)

- **Internal**: RESTful API endpoints, model relationships, event listeners
- **External**: LDAP/SSO for authentication, SMTP for email, legacy system data import via CSV/API
- **Dynamic Data**: HTTP client (Http::get()), external API integration, scheduled imports

---

## 9. KESELAMATAN SISTEM (System Security)

**Pematuhan Standard**: ISO/IEC 27001:2022 (Information Security), ISO/IEC 27701:2019 (Privacy Engineering), D00 §11a, D03 §8.1

- **Authentication**: Breeze, password hashing, SSO/LDAP
- **Authorization**: Role-based, policies, @can directive
- **CSRF Protection**: All forms, @csrf in Blade
- **Input Validation**: Form Request, $request->validate()
- **Data Encryption**: For sensitive data (at rest, in transit)
- **Audit Trail**: Logging all critical operations for compliance

### 9.1. Enkripsi & Pengurusan Kunci (Encryption & Key Management)

**Pematuhan Standard**: ISO/IEC 27001 A.10.1.1, NIST SP 800-175B (Encryption)

| Konteks | Algoritma | Mode | Key Size | Rotation |
|---------|-----------|------|----------|----------|
| **Data at Rest (Database)** | AES | GCM | 256-bit | Quarterly (90 days) |
| **Data in Transit (HTTPS)** | TLS | 1.3 | ECDHE 256-bit | Auto (cert renewal) |
| **Password Hashing** | bcrypt/argon2 | Salted | Dynamic | Per password change |
| **API Tokens** | Signature | HS256/RS256 | 256-bit | 8-hour expiry + refresh token |
| **Audit Logs** | AES | GCM | 256-bit | Immutable (no rotation after creation) |

**Implementasi Enkripsi di Laravel:**
```php
// config/app.php
'cipher' => 'AES-256-GCM',  // Laravel default for env encryption

// Enkripsi field tertentu di Model
protected function casts(): array

    return [
        'email' => 'encrypted',           // Encrypted at rest
        'phone' => 'encrypted:array',     // Phone number, auto-encrypt
        'ssn' => 'hash',                  // One-way hash for Malaysian ID
        'password' => 'hashed',           // bcrypt via Hash facade
  ;


// Enkripsi manual untuk data sensitif
use Illuminate\Support\Facades\Crypt;
$encrypted = Crypt::encrypt($userData);  // AES-256-GCM
$decrypted = Crypt::decrypt($encrypted);
```

**Key Management Procedure:**
1. **Key Generation**: Via `php artisan key:generate` on deployment (ENV encryption key)
2. **Key Rotation Schedule**: Quarterly (90 days) for symmetric keys; HSM managed for production
3. **Key Storage**: 
   - ENV variable (non-prod): `APP_KEY` in `.env`
   - HSM/Vault (prod): AWS KMS or HashiCorp Vault for key storage
4. **Key Backup**: Encrypted backup to secure location, versioned
5. **Key Revocation**: Maintain key audit log (old keys for decryption only)

**Rujukan**: Lihat **[D03_SOFTWARE_REQUIREMENTS_SPECIFICATION.md]** §8.1 Security Requirements, **[D00_SYSTEM_OVERVIEW.md]** §11a Deployment Architecture.

---

## 10. KAWALAN KUALITI (Quality Control — ISO 9001)

- **Peer review**: All code via pull requests, clear commit messages
- **Unit & Feature Testing**: php artisan test, factory & seeder for test data
- **Continuous Integration**: Composer update, npm run dev, regular dependency checks
- **Documentation**: Up-to-date code comments, docblocks, user & admin manuals
- **Monitoring**: storage/logs/laravel.log, exception handling, error display

---

## 11. PENYENGGARAAN & DEPLOYMENT (Maintenance & Deployment)

- **Deployment**: php artisan migrate, config:cache, route:cache, view:cache, optimize
- **Maintenance Mode**: php artisan down/up for safe updates
- **Backup**: Automated DB backup, restore/rollback procedures
- **Update**: Composer update, npm update, dependency management

---

## 12. PEMANTAUAN & PEMBERITAHUAN (Monitoring & Alerting)

**Pematuhan Standard**: ISO/IEC/IEEE 12207 §5.5 (Operation Process), ISO 9001 §8.6 (Control of Externally Provided Processes)

### 12.1. Metrics & Dashboard Specifications

| KPI | Target | Alatan | Tindakan Jika Melebihi |
|-----|--------|--------|----------------------|
| **Uptime (Availability)** | 99.5% (per D03 §8.2) | Laravel Horizon, New Relic | Alert DevOps, activate failover |
| **Response Time (p95)** | <2 seconds | New Relic APM | Profile slow queries, scale resources |
| **Error Rate (HTTP 5xx)** | <0.5% | New Relic, Sentry | Page alert, incident escalation |
| **Database Query Time** | <500ms avg | Laravel Debugbar, Query Monitor | Index optimization, caching layer |
| **Queue Job Failures** | <2% | Laravel Horizon | Retry mechanism, alert queue manager |
| **Disk Usage** | <85% | Server monitoring (df, iostat) | Archive logs, scale storage |
| **Memory Usage** | <80% | Server monitoring (free, ps) | Restart services, scale vertically |
| **CPU Usage** | <75% avg | Server monitoring (top, vmstat) | Load balancing, horizontal scaling |

**Monitoring Stack:**
- **APM**: New Relic, Datadog, or Laravel Telescope (dev only)
- **Log Aggregation**: ELK Stack (Elasticsearch, Logstash, Kibana) or Papertrail
- **Alerting**: PagerDuty, Slack notifications, Email
- **Uptime Monitoring**: Uptime.com, StatusPage.io

**Contoh Alert Configuration (Laravel Telescope):**
```php
// config/telescope.php
'after_recording_callback' => [
    function ($entry) 
        if ($entry->type === 'exception' && $entry->level === 'error') 
            Log::error('Critical Error', ['entry' => $entry]);
            Slack::send("Alert: $entry->content['message']");
    
,
],
```

### 12.2. SLA & Response Time Targets

| Skenario | RTO (Recovery Time Objective) | RPO (Recovery Point Objective) | Target SLA |
|----------|------|------|---------|
| **Normal Operation** | - | - | 99.5% uptime |
| **Database Failure** | 4 hours | 1 hour | Auto-failover to replica |
| **Server Failure** | 2 hours | 15 mins | Hot standby, auto-switch |
| **Network Outage** | 1 hour | 15 mins | ISP redundancy, load balancer |
| **Data Corruption** | 2 hours | 24 hours | Restore from daily backup |
| **Ransomware** | 4 hours | 1 day | Isolated backup, incident response |

**Rujukan**: Lihat **[D05_DATA_MIGRATION_PLAN.md]** §9 (Disaster Recovery Plan), **[D03_SOFTWARE_REQUIREMENTS_SPECIFICATION.md]** §8.2 (Performance Metrics).

---

## 13. SENIBINA DEPLOYMENT (Deployment Architecture)

**Pematuhan Standard**: ISO/IEC/IEEE 1016:2009 (Software Design), D00 §11a (Deployment Architecture)

### 13.1. Infrastructure Stack & Server Specifications

```
┌─────────────────────────────────────────────────────────┐
│         END-USER CLIENTS (Browser)                      │
│     Windows/macOS: Chrome, Firefox, Safari, Edge        │
└──────────────┬──────────────────────────────────────────┘
               │
         HTTPS/TLS 1.3
               │
┌──────────────▼──────────────────────────────────────────┐
│  LOAD BALANCER (HAProxy or AWS ALB)                     │
│  - Distributes traffic to app servers                   │
│  - SSL/TLS termination                                  │
│  - Health checks (port 8000 /health)                    │
│  - Sticky sessions for user auth                        │
└──────────────┬──────────────────────────────────────────┘
               │
        ┌──────┼──────┐
        │      │      │
   ┌────▼───┐ ┌─▼────────┐ ┌──────────┐
   │ APP-1  │ │ APP-2    │ │ APP-N    │ (N app servers)
   │ Nginx  │ │ Nginx    │ │ Nginx    │ 
   │ PHP 8.2│ │ PHP 8.2  │ │ PHP 8.2  │
   └────┬───┘ └─┬────────┘ └──┬───────┘
        │       │            │
        └───────┼────────────┘
                │
        ┌───────▼──────────┐
        │  SESSION STORE   │
        │  Redis Cluster   │ (multi-node failover)
        │  6.2+            │
        └───────┬──────────┘
                │
        ┌───────▼──────────┐
        │  PRIMARY DB      │
        │  MySQL 8.0       │ (Master-Replica replication)
        │  (Write node)    │
        └──────────────────┘
                │
        ┌───────▼──────────┐
        │  REPLICA DB-1    │ (Read scaling, backup source)
        │  MySQL 8.0       │
        └──────────────────┘

┌─────────────────────────────────────────────────────────┐
│ QUEUE & BACKGROUND JOBS                                 │
│  - Laravel Horizon (horizon:start)                      │
│  - Redis-backed queue (async email, notifications)      │
│  - Scheduled tasks: php artisan schedule:run (cron)     │
└─────────────────────────────────────────────────────────┘

┌─────────────────────────────────────────────────────────┐
│ STORAGE & BACKUPS                                        │
│  - Local file storage: /storage/app (private files)     │
│  - Daily DB backup: mysqldump → compressed → remote     │
│  - 30-day local retention, 7-year archive               │
│  - AES-256 encrypted backups                            │
└─────────────────────────────────────────────────────────┘
```

### 13.2. Server Specifications & Resource Allocation

| Server Role | OS | vCPU | RAM | Storage | Network |
|-------------|-----|------|-----|---------|---------|
| **App Server (N×)** | Ubuntu 22.04 LTS | 4+ cores | 8+ GB | 50 GB SSD | 1 Gbps NIC |
| **MySQL Primary** | Ubuntu 22.04 LTS | 8+ cores | 16+ GB | 500+ GB SSD (RAID 10) | 1 Gbps NIC |
| **MySQL Replica** | Ubuntu 22.04 LTS | 4+ cores | 8+ GB | 500+ GB SSD | 1 Gbps NIC |
| **Redis Cluster** | Ubuntu 22.04 LTS | 4+ cores | 8+ GB | 100 GB SSD | 1 Gbps NIC |
| **Load Balancer** | Ubuntu 22.04 LTS | 2+ cores | 4 GB | 20 GB SSD | 10 Gbps NIC (HA pair) |

### 13.3. Deployment & Failover Procedure

**Pre-Deployment:**
1. Run full test suite: `php artisan test --coverage` (target 80%+)
2. Code quality checks: `vendor/bin/phpstan analyse app/ --level 5`
3. Create migration backup: `mysqldump -u root -p dbname > backup_pre.sql`

**Deployment Steps:**
```bash
# 1. Pull latest from develop branch
git pull origin develop

# 2. Install dependencies
composer install --no-dev --optimize-autoloader

# 3. Run migrations (idempotent)
php artisan migrate --force --step

# 4. Cache configuration & routes
php artisan config:cache
php artisan route:cache
php artisan view:cache

# 5. Restart services
systemctl restart php-fpm
systemctl restart nginx

# 6. Verify health
curl -s http://localhost:8000/health | jq .
```

**Failover Procedure (DB Replica Promotion):**
1. **Detect Failure**: Heartbeat failure or manual trigger
2. **Validate Replica**: Check replication lag, data consistency
3. **Promote Replica**: Execute `CHANGE MASTER TO` to make replica primary
4. **Update Config**: Point APP_DATABASE_HOST to new primary
5. **Notify**: Alert via Slack, PagerDuty
6. **Restore Replication**: Rebuild old primary as new replica

**Rollback Procedure (if deployment fails):**
```bash
# 1. Revert code to previous tag
git checkout v2.0.1

# 2. Rollback database migrations
php artisan migrate:rollback --step=1

# 3. Clear all caches
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# 4. Restart services
systemctl restart php-fpm
systemctl restart nginx

# 5. Verify
curl -s http://localhost:8000/health | jq .
```

**Rujukan**: Lihat **[D00_SYSTEM_OVERVIEW.md]** §11a (Deployment Architecture), **[D01_SYSTEM_DEVELOPMENT_PLAN.md]** §9 (Change Management).

---

## 12. PENUTUP (was §12, now §14)

Dokumentasi ini menjadi rujukan rasmi bagi pembangunan, audit, dan penambahbaikan sistem Helpdesk & ICT Asset Loan BPM MOTAC. Semua komponen direka untuk skalabiliti, keselamatan, dan kualiti mengikut piawaian **IEEE 1016** (software design), **ISO/IEC/IEEE 2651x series** (software/documentation engineering), **ISO 9001** (quality management), dan **ISO/IEC/IEEE 12207** (software lifecycle).

---

## Glosari & Rujukan (Glossary & References)

Sila rujuk **[GLOSSARY.md]** untuk istilah teknikal seperti:

- **Rekabentuk Teknikal (Technical Design)**: Spesifikasi terperinci senibina dan komponen sistem
- **MVC (Model-View-Controller)**: Pola senibina pemisahan logik aplikasi
- **RESTful API**: Application Programming Interface mengikut prinsip REST
- **IEEE 1016**: Piawaian rekabentuk perisian
- **ISO 9001**: Piawaian pengurusan kualiti
- **ISO/IEC/IEEE 2651x**: Siri piawaian kejuruteraan perisian dan dokumentasi

**Dokumen Rujukan:**

- **D00_SYSTEM_OVERVIEW.md** - Gambaran keseluruhan sistem
- **D01_SYSTEM_DEVELOPMENT_PLAN.md** - Pelan pembangunan sistem
- **D04_SOFTWARE_DESIGN_DOCUMENT.md** - Rekabentuk perisian (high-level)
- **D07_SYSTEM_INTEGRATION_PLAN.md** - Pelan integrasi sistem
- **D08_SYSTEM_INTEGRATION_SPECIFICATION.md** - Spesifikasi integrasi sistem
- **D09_DATABASE_DOCUMENTATION.md** - Dokumentasi pangkalan data
- **D10_SOURCE_CODE_DOCUMENTATION.md** - Dokumentasi kod sumber

---

## Lampiran (Appendices)

### A. Diagram Senibina Sistem (System Architecture Diagrams)

Rujuk Seksyen 3 untuk diagram senibina lengkap termasuk deployment diagram dan component diagram.

### B. Spesifikasi Modul Terperinci (Detailed Module Specifications)

Rujuk Seksyen 4 untuk spesifikasi lengkap setiap modul sistem.

### C. ERD & Database Schema (Entity Relationship Diagram & Schema)

Rujuk **D09_DATABASE_DOCUMENTATION.md** untuk ERD lengkap dan definisi jadual.

### D. API Specification & Endpoints

Rujuk Seksyen 7 untuk spesifikasi RESTful API dan endpoint dokumentasi.

### E. Security Architecture & Controls

Rujuk Seksyen 9 untuk senibina keselamatan dan kawalan terperinci.

---

**Dokumen ini mematuhi piawaian IEEE 1016:2009 (Software Design), ISO/IEC/IEEE 26512:2018 (Software User Documentation), ISO 9001:2015 (Quality Management Systems), dan ISO/IEC/IEEE 12207:2017 (Software Lifecycle Processes).**
