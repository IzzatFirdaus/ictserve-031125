# Dokumentasi Kod Sumber (Source Code Documentation)

**Sistem ICTServe**
**Versi:** 2.0.0 (SemVer)
**Tarikh Kemaskini:** 17 Oktober 2025
**Status:** Aktif
**Klasifikasi:** Terhad - Dalaman MOTAC
**Penulis:** Pasukan Pembangunan BPM MOTAC
**Standard Rujukan:** ISO/IEC/IEEE 5055, ISO/IEC/IEEE 25000 Series (SQuaRE), ISO/IEC/IEEE 12207

---

## Maklumat Dokumen (Document Information)

| Atribut                | Nilai                                    |
|------------------------|------------------------------------------|
| **Versi**              | 2.0.0                                    |
| **Tarikh Kemaskini**   | 17 Oktober 2025                          |
| **Status**             | Aktif                                    |
| **Klasifikasi**        | Terhad - Dalaman MOTAC                   |
| **Pematuhi**           | ISO/IEC/IEEE 5055, 25000 Series, 12207   |
| **Bahasa**             | Bahasa Melayu (utama), English (teknikal)|

---

## Sejarah Perubahan (Changelog)

| Versi  | Tarikh          | Perubahan                                      | Penulis       |
|--------|-----------------|------------------------------------------------|---------------|
| 1.0.0  | September 2025  | Versi awal dokumentasi kod sumber              | Pasukan BPM   |
| 2.0.0  | 17 Oktober 2025 | Penyeragaman mengikut D00-D14, SemVer, cross-reference | Pasukan BPM   |

---

## Rujukan Dokumen Berkaitan (Related Document References)

- **[D00_SYSTEM_OVERVIEW.md]** - Ringkasan Sistem
- **[D01_SYSTEM_DEVELOPMENT_PLAN.md]** - Pelan Pembangunan Sistem
- **[D04_SOFTWARE_DESIGN_DOCUMENT.md]** - Rekabentuk Perisian
- **[D11_TECHNICAL_DESIGN_DOCUMENTATION.md]** - Dokumentasi Rekabentuk Teknikal
- **[GLOSSARY.md]** - Glosari Istilah Sistem


---

## 1. TUJUAN DOKUMEN (Purpose)

Dokumen ini memberi penerangan struktur kod sumber, gaya penulisan, piawaian kualiti, dan kawalan perubahan bagi sistem **Helpdesk & ICT Asset Loan BPM MOTAC**, berpandukan piawaian **ISO/IEC/IEEE 5055** (software quality), **ISO/IEC/IEEE 25000 Series (SQuaRE)** (quality requirements and evaluation), dan **ISO/IEC/IEEE 12207** (software lifecycle processes).

---

## 2. SKOP (Scope)

- Semua kod sumber Laravel 12 (PHP), Blade views, JS, CSS, migration, seeder, factory, dan konfigurasi.
- Piawaian penulisan kod, komen, dokumentasi fungsi, dan kawalan versi.
- Penekanan pada maintainability, reliability, security, dan usability.


---

## 3. STRUKTUR KOD SUMBER (Source Code Structure)

### 3.1. Direktori Utama

| Folder/File                       | Fungsi/Kandungan                                       |
|-----------------------------------|--------------------------------------------------------|
| `app/Models/`                     | Definisi model Eloquent (User, Inventory, Asset, Loan) |
| `app/Http/Controllers/`           | Controller — logik aplikasi dan CRUD                   |
| `app/Policies/`                   | Policy autorisasi (contoh: InventoryPolicy)            |
| `app/Http/Middleware/`            | Middleware (auth, role, audit)                         |
| `app/Notifications/`              | Notifikasi custom                                      |
| `app/Jobs/`                       | Pengurusan queue jobs                                  |
| `resources/views/`                | Blade views — antaramuka pengguna                      |
| `database/migrations/`            | Migrations — skema DB                                  |
| `database/seeders/`               | Seeders — data permulaan/test                          |
| `database/factories/`             | Factories — data ujian                                 |
| `routes/web.php`                  | Definisi route aplikasi web                            |
| `routes/api.php`                  | Definisi route API                                     |
| `config/`                         | Konfigurasi sistem, queue, mail dsb                    |
| `.env`                            | Konfigurasi environment (DB, mail, queue)              |

---

## 4. PIAWAIAN PENULISAN KOD (Coding Standards)

### 4.1. PHP (Laravel)

- **PSR-12**: Pematuhan kepada standard PHP PSR-12.
- **Naming convention**: Model singular (Inventory), controller camel case (InventoryController), method camelCase.
- **Type hinting**: Gunakan type hinting (typed properties) untuk method dan class.
- **$fillable**: Semua model menggunakan protected $fillable untuk mass assignment.
- **Relationship**: hasMany(), belongsTo(), morphMany() jika perlu.
- **Error handling**: Validation via $request->validate(), try-catch untuk exception.


### 4.2. Blade

- **Extends/Includes**: Gunakan @extends, @include untuk layout dan partial.
- **Loop/Display**: @foreach, @if,  $variable .
- **Validation**: @error directive, display validation message.
- **Security**: Semua input menggunakan @csrf.


### 4.3. JavaScript

- **ES6**: Gunakan let/const, arrow function, fetch API.
- **Separation of concerns**: JS untuk interaktiviti dropdown, AJAX, dsb.
- **Asset management**: Guna Vite (resources/js, resources/css) dan import modul; aset terbitan tersedia dalam `public/build`.


### 4.4. Comments & Documentation

- **Docblock**: Semua fungsi/method ada docblock (/** ... */) dan parameter/type dijelaskan.
- **Inline comments**: Komen pada logik rumit atau edge case.
- **Class-level**: Setiap class/model/controller ada penerangan ringkas di atas kelas.
- **README**: Projek ada README untuk setup & usage.


---

## 5. KUALITI KOD (Code Quality Attributes — SQuaRE)

| Kualiti (ISO/IEC 25000) | Penjelasan/Penerapan                  |
|-------------------------|---------------------------------------|
| **Fungsionaliti**       | Semua fungsi utama diuji, kod modular |
| **Kebolehgunaan**       | UI konsisten, error jelas, form valid |
| **Kebolehpeliharaan**   | Kod mudah dibaca, diubah, modular     |
| **Kebolehpercayaan**    | Exception handling, audit trail, test |
| **Efisiensi**           | Query optimized, cache digunakan      |
| **Keselamatan**         | CSRF, XSS, validation, roles, audit   |

---

## 6. DOKUMENTASI FUNGSI UTAMA (Key Function Documentation)

### 6.1. Model

**Example: Inventory.php**

```php

/**

 * Model Inventory.
 * Menyimpan data inventori aset ICT.
 * @property int $id
 * @property int $user_id
 * @property string $name
 * @property int $qty
 * @property float $price
 * @property string $description
 */

class Inventory extends Model

    use HasFactory;

    protected $fillable = ['user_id', 'name', 'qty', 'price', 'description'];

    /**

     * Relationship: belongs to User
     */

    public function user()  return $this->belongsTo(User::class);

```

### 6.2. Controller

**Example: InventoryController.php**

```php

/**

 * Controller untuk CRUD inventori.
 * Method utama: index(), create(), store(), show(), edit(), update(), destroy()
 */

class InventoryController extends Controller

    /**

     * Senarai inventori (pagination)
     */

    public function index()
        $inventories = Inventory::with('user')->paginate(10);
        return view('inventory.index', compact('inventories'));

    // ... other methods documented similarly

```

### 6.3. Policy

**InventoryPolicy.php**

```php

/**

 * Policy untuk autorisasi inventori.
 * Method: view(), update(), delete()
 */

public function update(User $user, Inventory $inventory)
    // Hanya pemilik atau admin boleh update
    return $user->role === 'admin' || $user->id === $inventory->user_id;

```

---

## 7. UJIAN (Testing)

- Semua kod diuji dengan php artisan test (unit, feature).
- Test case ada komen menjelaskan tujuan ujian.
- Data ujian gunakan factory dan seeder.


---

## 7. STRATEGI UJIAN (Testing Strategy)

**Pematuhan Standard**: ISO/IEC/IEEE 12207 (Software Lifecycle), D01 §4.4 (Testing Phase)

### 7.1. Jenis-Jenis Ujian & Framework

| Jenis Ujian | Framework | Cakupan | Sasaran |
|-------------|-----------|--------|--------|
| **Unit Testing** | PHPUnit 11+ (Laravel Pest Syntax) | Fungsi individual, logic kondisi | 80%+ coverage |
| **Integration Testing** | PHPUnit + Laravel TestCase | API endpoints, database transactions, auth flow | All critical paths |
| **Feature Testing** | Livewire::test(), Volt::test() | UI components, user interactions, forms | CRUD operations, workflows |
| **UAT (User Acceptance)** | Manual + test scripts | Business workflows, user requirements | All D02 business requirements |
| **Regression Testing** | PHPUnit suite on CI/CD | Detect breaking changes | Run before every release |
| **Performance Testing** | Laravel Horizon, ab tool | Response time, concurrent requests | D03 §8.2 targets (<2s) |
| **Security Testing** | Manual code review + OWASP Zap | CSRF, SQL injection, XSS, auth bypass | D03 §8.1 security requirements |

### 7.2. Test Case Examples (PHPUnit + Pest Syntax)

**Contoh Unit Test (Ticket Model Validation):**
```php

// tests/Unit/TicketTest.php
test('ticket requires damage_type', function ()
    $ticket = Ticket::factory()->make(['damage_type' => null]);
    expect($ticket->validate())->toFail();
);

test('ticket status must be valid enum', function ()
    $ticket = Ticket::factory()->create(['status' => 'Open']);
    expect($ticket->status)->toEqual(TicketStatus::Open);
);
```

**Contoh Feature Test (Loan Approval Workflow):**
```php

// tests/Feature/LoanApprovalTest.php
test('division head can approve pending loan', function ()
    $loan = Loan::factory()->create(['status' => 'Pending']);
    $user = User::factory()->asDivisionHead()->create();

    actingAs($user)->patch(route('loans.approve', $loan), ['approval_remarks' => 'Approved'])
        ->assertRedirect()
        ->assertSessionHas('success', 'Loan approved');

    expect($loan->refresh()->status)->toEqual('Approved');
);
```

**Contoh Livewire Component Test (Search Filter):**
```php

// tests/Feature/TicketSearchTest.php
test('ticket table filters by damage_type', function ()
    Ticket::factory(5)->create(['damage_type' => 'Pencemar Peranti']);
    Ticket::factory(3)->create(['damage_type' => 'Hilang']);

    Livewire::test(TicketTable::class)
        ->set('filterDamageType', 'Pencemar Peranti')
        ->assertSee('Pencemar Peranti')
        ->assertDontSee('Hilang');
);
```

### 7.3. Test Coverage & CI/CD

**Coverage Targets (per ISO/IEC/IEEE 12207):**

- Unit tests: 80%+ branch coverage
- Integration tests: All API endpoints (D08 11 endpoints)
- Feature tests: All CRUD workflows + approval chain
- Regression: Full suite runs on every PR before merge


**CI/CD Pipeline (.github/workflows/test.yml):**
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
```

**Rujukan**: Lihat **[D01_SYSTEM_DEVELOPMENT_PLAN.md]** §4.4 untuk fasa ujian lengkap; **[D03_SOFTWARE_REQUIREMENTS_SPECIFICATION.md]** §8 untuk metrik kualiti (Response Time <2s, Uptime 99.5%).

---

## 8. KAWALAN VERSI & PERUBAHAN (Version & Change Control)

**Pematuhan Standard**: ISO/IEC/IEEE 15288 §6.4.10 (Configuration Management), D01 §9.3 (Change Management)

### 8.1. Git Workflow & Branching Strategy

| Cabang | Tujuan | Proteksi | Merge Policy |
|--------|--------|---------|--------------|
| **main** | Production release | ✅ Locked | Squash-merge dari release, tagged semantic version |
| **develop** | Integration branch | ✅ Protected | Fast-forward merge dari feature branches, all CI passing |
| **feature/*** | Feature development | ✅ PR required | Rebase on develop, min. 1 reviewer, CI green |
| **bugfix/*** | Bug fixes | ✅ PR required | Same as feature branches |
| **hotfix/*** | Prod emergency | ✅ Direct merge allowed | Merge to main → develop immediately, tag hotfix |

**Contoh Workflow:**
```bash

# 1. Create feature branch
git checkout -b feature/department-bulk-import develop

# 2. Commit with descriptive messages
git commit -m "feat: add bulk import for departments via CSV"
git commit -m "test: add tests for CSV parsing and validation"

# 3. Push & create PR for code review
git push origin feature/department-bulk-import

# 4. After PR approval & CI pass, merge to develop
git checkout develop
git merge --squash feature/department-bulk-import
git push origin develop
```

### 8.2. Commit Message Convention

**Format**: `<type>(<scope>): <subject>`

| Type | Scope | Contoh |
|------|-------|--------|
| **feat** | module name | `feat(ticket): add damage severity classification` |
| **fix** | module name | `fix(auth): prevent CSRF in login form` |
| **test** | test type | `test(integration): add loan approval workflow tests` |
| **docs** | section | `docs(api): update endpoint rate limiting specs` |
| **chore** | tool/deps | `chore: update phpstan level to 6` |
| **refactor** | component | `refactor(loan): simplify approval condition logic` |

---

## 9. ALAT & KAWALAN KUALITI (QA Tools & Quality Assurance)

**Pematuhan Standard**: ISO/IEC/IEEE 5055 (Software Quality), ISO 9001 (Quality Management)

### 9.1. Static Code Analysis & Linting

| Alat | Fungsi | Konfigurasi | Target |
|-----|--------|-----------|--------|
| **PHPStan** | Static analysis (find bugs before runtime) | Level 5 (strict), `phpstan.neon` config | Zero issues |
| **Laravel Pint** | Code formatting & PSR-12 compliance | `.pint.json`, auto-fix on save | 100% compliance |
| **Stylelint** | CSS/Tailwind validation | `.stylelintrc`, Tailwind plugin | Zero warnings |

**Contoh Konfigurasi (phpstan.neon):**
```ini

parameters:
    level: 5
    paths:

        - app
    excludePaths:

        - app/Console/Kernel.php
    reportUnmatchedIgnoredErrors: true

```

**Run Commands:**
```bash

vendor/bin/phpstan analyse app/ --level 5
vendor/bin/pint --dirty  # Format changed files only
npm run lint:css         # Stylelint (Tailwind)
```

### 9.2. Automated Testing via CI/CD (GitHub Actions)

**Pipeline Stages** (`.github/workflows/ci.yml`):
```yaml

stages:

  - lint (PHPStan + Pint)
  - test (PHPUnit full suite, coverage >80%)
  - build (npm run build, generate assets)
  - deploy (to staging/prod with approval)
```

**Status Checks on PR:**

- ✅ All CI jobs pass (lint, test, build)
- ✅ Code coverage ≥80%
- ✅ Min. 1 reviewer approval
- ✅ Branch protected (no direct pushes)


### 9.3. Manual QA Checklist (per ISO 9001)

**Pre-Release QA Checklist:**

- [ ] All unit tests pass (`php artisan test --coverage`)
- [ ] PHPStan analysis clean (Level 5, zero issues)
- [ ] Code formatted with Pint (`vendor/bin/pint`)
- [ ] Feature tested on Chrome, Firefox, Safari (D03 §4.1)
- [ ] Accessibility tested with Lighthouse (WCAG 2.2 AA, D14 §9)
- [ ] Database migrations tested on fresh instance
- [ ] Admin panel functionality verified (Filament CRUD)
- [ ] API endpoints tested with Postman/curl
- [ ] Security: CSRF tokens, auth middleware, XSS prevention
- [ ] Performance: Response time <2s, DB queries optimized
- [ ] Documentation updated (code comments, README, D10-D14)
- [ ] Changelog entry added with version tag


**Rujukan**: Lihat **[D01_SYSTEM_DEVELOPMENT_PLAN.md]** §4.4 (Testing Phase), **[D03_SOFTWARE_REQUIREMENTS_SPECIFICATION.md]** §8 (Non-Functional Requirements), **[D14_UI_UX_STYLE_GUIDE.md]** §9 (Accessibility).

---

## 10. PENUTUP

Dokumentasi ini memberi rujukan lengkap untuk pembangun, auditor, dan pentadbir sistem Helpdesk & ICT Asset Loan BPM MOTAC dalam memahami, mengurus, dan meningkatkan kualiti kod sumber mengikut piawaian antarabangsa **ISO/IEC/IEEE 5055** (software quality), **ISO/IEC/IEEE 25000 Series (SQuaRE)**, dan **ISO/IEC/IEEE 12207** (software lifecycle).

---

## Glosari & Rujukan (Glossary & References)

Sila rujuk **[GLOSSARY.md]** untuk istilah teknikal seperti:

- **Kod Sumber (Source Code)**: Teks kod program yang ditulis dalam bahasa pengaturcaraan
- **SQuaRE (Systems and Software Quality Requirements and Evaluation)**: Siri piawaian ISO/IEC 25000
- **Coding Standards**: Garis panduan penulisan kod yang konsisten
- **PSR-12**: PHP Standards Recommendation untuk gaya kod
- **ISO/IEC/IEEE 5055**: Piawaian kualiti perisian automatik
- **ISO/IEC/IEEE 25000**: Piawaian keperluan dan penilaian kualiti sistem/perisian


**Dokumen Rujukan:**

- **D00_SYSTEM_OVERVIEW.md** - Gambaran keseluruhan sistem
- **D01_SYSTEM_DEVELOPMENT_PLAN.md** - Pelan pembangunan sistem
- **D04_SOFTWARE_DESIGN_DOCUMENT.md** - Rekabentuk perisian
- **D11_TECHNICAL_DESIGN_DOCUMENTATION.md** - Rekabentuk teknikal terperinci


---

## Lampiran (Appendices)

### A. Contoh Dokumentasi Kod (Code Documentation Examples)

Rujuk Seksyen 6 untuk contoh dokumentasi fungsi dan kelas.

### B. Piawaian PSR-12 & Laravel Best Practices

- **PSR-12**: Extended Coding Style Guide (<https://www.php-fig.org/psr/psr-12/>)
- **Laravel Coding Standards**: Rujuk Laravel Documentation (<https://laravel.com/docs>)
- **PHP Stan Level**: Level 5 (strict code analysis)


### C. Metrik Kualiti Kod (Code Quality Metrics)

- **Cyclomatic Complexity**: Maksimum 10 per fungsi
- **Code Coverage**: Minimum 80% untuk critical paths
- **Technical Debt Ratio**: Maksimum 5%
- **Maintainability Index**: Minimum 70


### D. Checklist Code Review

Rujuk Seksyen 8 untuk proses kawalan perubahan kod.

---

**Dokumen ini mematuhi piawaian ISO/IEC/IEEE 5055:2021 (Software Quality), ISO/IEC 25000:2014 (SQuaRE), dan ISO/IEC/IEEE 12207:2017 (Software Lifecycle Processes).**
