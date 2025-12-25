# Spesifikasi Migrasi Data (Data Migration Specification - DMS)

**Sistem ICTServe**  
**Versi:** 3.6.1 (SemVer)  
**Tarikh Kemaskini:** 17 Disember 2025  
**Status:** Aktif  
**Klasifikasi:** Terhad - Dalaman MOTAC  
**Penulis:** Pasukan Pembangunan BPM MOTAC  
**Standard Rujukan:** ISO 8000 (Data Quality), ISO/IEC 38505-1 (Governance of Data), RFC 5322, ISO 8601, TLS 1.3, AES-256, PDPA 2010

---

## Maklumat Dokumen (Document Information)

| Atribut              | Nilai                                     |
| -------------------- | ----------------------------------------- |
| **Versi**            | 3.6.1                                     |
| **Tarikh Kemaskini** | 17 Disember 2025                          |
| **Status**           | Aktif                                     |
| **Klasifikasi**      | Terhad - Dalaman MOTAC                    |
| **Pematuhi**         | ISO 8000, ISO/IEC 38505-1, PDPA 2010                 |
| **Bahasa**           | Bahasa Melayu (utama), istilah teknikal English bila perlu |

> Notis Penggunaan Dalaman: Spesifikasi ini adalah untuk migrasi data dalaman MOTAC; pastikan pematuhan PDPA.

---

## Sejarah Perubahan (Changelog)

| Versi | Tarikh           | Perubahan                                                                                                                                                                                                                                                                                                                                               | Penulis     |
| ----- | ---------------- | ------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------- | ----------- |
| 3.6.1 | 17 Disember 2025 | Kemaskini teknologi stack: Laravel 12.43.1, Livewire 3.7.3, Laravel Pulse 1.4.7, Laravel Reverb 1.6.3, Laravel Sanctum 4.2.1, Laravel Socialite 5.24.0, PHPUnit 11.5.46, Tailwind CSS 4.1.18, Laravel MCP 0.3.4, Laravel Prompts 0.3.8, Larastan 3.8.1, Laravel Pint 1.26.0, Laravel Telescope 5.16.0. Penyelarasan dengan D00-D18 v3.6.1. | Pasukan BPM |
| 3.5.0 | 1 Disember 2025  | True Hybrid Architecture v3.5.0: Self-registration (@motac.gov.my), flexible login (email/username), account linking, dual audit (owen-it + spatie), Laravel Pulse, Sanctum API, Google SSO (optional), Responsible Officer, Accessory Tracking, Form Reference Codes, MOTAC Branding, Enhanced UX. Penyelarasan dengan D00-D05 v3.5.0. | Pasukan BPM |
| 3.7.0 | 15 Disember 2025 | AI Chatbot Integration: Tambah struktur data AI (faqs, documents, embeddings, bedrock_conversations, ai_message_logs). Rujukan D18 v1.0.0 Cloud Hybrid AI Architecture (Ollama + AWS Bedrock).                                                                                                                                          | Pasukan BPM |
| 3.4.0 | 29 November 2025   | Hybrid Architecture v3.4.0: Staff migration to users table, email-based linking, restore LDAP/SSO as optional authentication. Penyelarasan dengan D00-D08 v3.4.0.                                                                                                                                                                                       | Pasukan BPM |
| 3.3.0 | 29 November 2025 | Hapus Section 4.4 Profil Pengguna migration (staff tidak dimigrasikan). Penyelarasan penuh Guest-First architecture.                                                                                                                                                                                                                                    | Pasukan BPM |
| 3.2.0 | 29 November 2025 | Penyelarasan dengan Guest-First architecture: ganti FK user dengan string fields (name, email, division_code, grade). Klarifikasi hanya admin/superuser dimigrasikan ke users table.                                                                                                                                                                    | Pasukan BPM |
| 2.2.0 | 29 November 2025 | Kemaskini dokumentasi sistem: pengesahan versi teknologi semasa. Penyelarasan dengan D00-D05.                                                                                                                                                                                                                                                           | Pasukan BPM |
| 2.1.0 | 6 Januari 2025   | Kemaskini rujukan teknologi: Laravel 12.43.1, PHP 8.2.12                                                                                                                                                                                                                                                                                                | Pasukan BPM |
| 2.0.0 | 17 Oktober 2025  | Penyeragaman mengikut D00-D14, SemVer, cross-reference                                                                                                                                                                                                                                                                                                  | Pasukan BPM |
| 1.0.0 | September 2025   | Versi awal spesifikasi migrasi data                                                                                                                                                                                                                                                                                                                     | Pasukan BPM |

---

## Rujukan Dokumen Berkaitan (Related Document References)

- **[D00_SYSTEM_OVERVIEW.md]** - Ringkasan Sistem (v3.5.0)
- **[D03_SOFTWARE_REQUIREMENTS_SPECIFICATION.md]** - Spesifikasi Keperluan Perisian (v3.5.0)
- **[D04_SOFTWARE_DESIGN_DOCUMENT.md]** - Dokumen Rekabentuk Perisian (v3.5.0)
- **[D05_DATA_MIGRATION_PLAN.md]** - Pelan Migrasi Data (strategy & timeline, v3.5.0)
- **[D09_DATABASE_DOCUMENTATION.md]** - Dokumentasi Pangkalan Data (target schema, dual audit)
- **[D11_TECHNICAL_DESIGN_DOCUMENTATION.md]** - Dokumentasi Rekabentuk Teknikal
- **[D15_LANGUAGE_MS_EN.md]** - Panduan Lokalisasi (Bahasa Melayu sahaja, v3.6.0)
- **[D18_AI_CHATBOT_OLLAMA_BEDROCK.md]** - AI Chatbot Integration (Cloud Hybrid AI, v1.0.0)
- **[GLOSSARY.md]** - Glosari Istilah Sistem

---

## 1. TUJUAN DOKUMEN (Purpose)

Dokumen ini menggariskan spesifikasi teknikal dan piawaian yang perlu dipatuhi bagi proses migrasi data ke sistem **Helpdesk & ICT Asset Loan** berasaskan Laravel 12 untuk Bahagian Pengurusan Maklumat (BPM), MOTAC. Ia mematuhi **ISO 8000** untuk kualiti data (data quality) dan **ISO/IEC 38505-1** untuk tadbir urus data (data governance).

---

## 2. SKOP SPESIFIKASI (Scope)

- Migrasi data merangkumi:
  - **Tiket Aduan Kerosakan ICT** (Complaint Tickets)
  - **Data Pinjaman Peralatan ICT** (Equipment Loan Records)
  - **Inventori Peralatan ICT** (Asset Inventory)
  - **Profil Pengguna** (User Profiles)
- Semua metadata berkaitan (timestamp, status, logs, audit trail) juga mesti dimigrasikan.

---

## 3. PIAWAIAN DATA (Data Standards)

### 3.1. Kualiti Data (ISO 8000 Data Quality)

- **Ketepatan (Accuracy)**: Data mesti tepat, tanpa kesilapan ejaan atau digit.
- **Kelengkapan (Completeness)**: Semua field wajib dan penting mesti diisi (rujuk breakdown borang rasmi).
- **Konsistensi (Consistency)**: Format data seperti tarikh, nombor telefon, kod aset mesti seragam.
- **Keunikan (Uniqueness)**: Tiada rekod duplikat (duplicate ticket, asset, atau user).
- **Kebolehan Jejak (Traceability)**: Setiap perubahan atau migrasi mesti direkod untuk audit.

### 3.2. Tadbir Urus Data (ISO/IEC 38505-1)

- **Accountability**: Tanggungjawab setiap proses migrasi jelas (team, person in charge).
- **Transparency**: Semua proses migrasi didokumen dan boleh diaudit.
- **Security & Privacy**: Data peribadi dilindungi sepanjang proses migrasi.
- **Compliance**: Pematuhan kepada polisi dalaman BPM & undang-undang berkaitan (contoh: PDPA).

---

## 4. STRUKTUR DATA SASARAN (Target Data Structure)

### 4.1. Tiket Aduan Kerosakan ICT

| Field                   | Jenis Data  | Mandatori   | Keterangan                            |
| ----------------------- | ----------- | ----------- | ------------------------------------- |
| id                      | bigint      | Ya          | Auto increment (primary key)          |
| user_id                 | bigint      | Tidak       | FK → users.id (NULL untuk Guest)      |
| ticket_number           | string(50)  | Ya          | Nombor tiket (unik)                   |
| guest_name              | string(255) | Tidak       | Nama penghantar (untuk guest)         |
| guest_email             | string(255) | Tidak       | E-mel penghantar (untuk guest)        |
| guest_phone             | string(20)  | Tidak       | Nombor telefon (untuk guest)          |
| guest_division          | string(100) | Tidak       | Bahagian/unit (teks) (untuk guest)    |
| guest_grade             | string(10)  | Tidak       | Gred (untuk guest)                    |
| division_id             | bigint      | Tidak       | FK → divisions.id (untuk staf berdaftar) |
| category_id             | bigint      | Ya          | FK → ticket_categories.id             |
| priority                | enum        | Ya          | low/normal/high/urgent                |
| subject                 | string(255) | Ya          | Tajuk aduan                           |
| description             | text        | Ya          | Keterangan masalah                    |
| damage_type             | string      | Tidak       | Jenis kerosakan (jika berkaitan)      |
| asset_id                | bigint      | Tidak       | FK → assets.id (jika berkaitan perkakasan) |
| declaration_accepted    | boolean     | Ya          | Perakuan/disclaimer                   |
| status                  | enum        | Ya          | open/assigned/in_progress/pending_user/resolved/closed |
| created_at              | timestamp   | Ya          | Tarikh aduan dibuat                   |
| updated_at              | timestamp   | Ya          | Tarikh kemaskini terakhir             |

**Indeks (ringkasan):** `(user_id, status)`, `(guest_email, status)`, `(status, priority)`, `(ticket_number)`

> **Nota Hybrid Model**: `user_id` NULL = Guest; NOT NULL = Staff berdaftar

### 4.2. Data Pinjaman Peralatan ICT

| Field                                | Jenis Data  | Mandatori   | Keterangan                                    |
| ------------------------------------ | ----------- | ----------- | --------------------------------------------- |
| id                                   | bigint      | Ya          | Primary key                                   |
| user_id                              | bigint      | Tidak       | FK → users.id (NULL untuk Guest)              |
| applicant_name                       | string(255) | Ya          | Nama pemohon                                  |
| applicant_email                      | string(255) | Ya          | E-mel pemohon                                 |
| applicant_phone                      | string(20)  | Ya          | Telefon pemohon                               |
| applicant_position                   | string      | Ya          | Jawatan pemohon                               |
| applicant_grade                      | string      | Ya          | Gred jawatan (teks)                           |
| staff_id                             | string(20)  | Ya          | ID staf MOTAC                                 |
| grade                                | string(10)  | Ya          | Gred ringkas (contoh: 41/44/48/52/54)         |
| division_id                          | bigint      | Ya          | FK → divisions.id                             |
| application_number                   | string(20)  | Ya          | Nombor permohonan (unik)                      |
| purpose                              | text        | Ya          | Tujuan pinjaman                               |
| location                             | string      | Ya          | Lokasi penggunaan                             |
| return_location                      | string      | Ya          | Lokasi pemulangan                             |
| loan_start_date                      | date        | Ya          | Tarikh mula pinjam                            |
| loan_end_date                        | date        | Ya          | Tarikh dijangka pulang                        |
| status                               | enum        | Ya          | Status permohonan (rujuk migration)           |
| priority                             | enum        | Ya          | low/normal/high/urgent                        |
| terms_acknowledged                   | boolean     | Ya          | Perakuan/disclaimer                           |
| form_reference_code                  | string(50)  | Ya          | Kod rujukan borang (DEFAULT PK.(S).MOTAC.07.(L3)) |
| is_applicant_responsible             | boolean     | Ya          | Pemohon adalah Pegawai Bertanggungjawab (DEFAULT TRUE) |
| responsible_officer_name             | string(255) | Conditional | Nama Pegawai Bertanggungjawab (jika berbeza)  |
| responsible_officer_grade            | string(50)  | Conditional | Gred Pegawai Bertanggungjawab                 |
| responsible_officer_phone            | string(50)  | Conditional | Telefon Pegawai Bertanggungjawab              |
| responsible_officer_acknowledgement  | boolean     | Conditional | Perakuan Pegawai Bertanggungjawab             |
| created_at                           | timestamp   | Ya          | Tarikh permohonan                             |
| updated_at                           | timestamp   | Ya          | Tarikh kemaskini terakhir                     |

**Indeks (ringkasan):** `(user_id, status)`, `(applicant_email, status)`, `(application_number)`, `(division_id)`

> **Nota Hybrid Model**: `user_id` NULL = Guest; NOT NULL = Staff berdaftar. Kelulusan melalui email token (dual approval workflow).
>
> **Nota Responsible Officer (v3.5.0)**: Jika `is_applicant_responsible` = TRUE, Pegawai Bertanggungjawab adalah pemohon sendiri. Jika FALSE, medan `responsible_officer_*` mesti diisi.

### 4.3. Inventori Peralatan ICT (Asset Inventory)

| Field       | Jenis Data  | Mandatori | Keterangan                                 |
| ----------- | ----------- | --------- | ------------------------------------------ |
| id          | bigint      | Ya        | Primary Key                                |
| asset_type  | string(100) | Ya        | Jenis peralatan                            |
| brand       | string(100) | Ya        | Jenama                                     |
| model       | string(100) | Ya        | Model                                      |
| serial_no   | string(100) | Ya        | No Siri / Tag ID                           |
| accessories | json/text   | Tidak     | Senarai aksesori standard                  |
| status      | string(50)  | Ya        | Status (Available, Loaned, Returned, etc.) |
| created_at  | timestamp   | Ya        | Tarikh daftar                              |
| updated_at  | timestamp   | Ya        | Tarikh terakhir kemaskini                  |

### 4.3.1. Aksesori Transaksi Pinjaman (Loan Transaction Accessories) - v3.5.0

| Field               | Jenis Data                                                                          | Mandatori   | Keterangan                                |
| ------------------- | ----------------------------------------------------------------------------------- | ----------- | ----------------------------------------- |
| id                  | bigint                                                                              | Ya          | Primary Key                               |
| loan_transaction_id | bigint                                                                              | Ya          | FK → loan_transactions.id                 |
| accessory_type      | enum(POWER_ADAPTER, BAG, MOUSE, USB_CABLE, HDMI_VGA_CABLE, REMOTE, OTHERS)          | Ya          | Jenis aksesori                            |
| accessory_name      | string(100)                                                                         | Conditional | Nama aksesori (wajib jika type = OTHERS)  |
| present_at_checkout | boolean                                                                             | Ya          | Ada semasa check-out                      |
| present_at_checkin  | boolean                                                                             | Conditional | Ada semasa check-in (NULL jika belum)     |
| condition_notes     | text                                                                                | Tidak       | Catatan keadaan aksesori                  |
| created_at          | timestamp                                                                           | Ya          | Tarikh cipta                              |
| updated_at          | timestamp                                                                           | Ya          | Tarikh kemaskini                          |

**Indeks:** `(loan_transaction_id)`, `(accessory_type)`

> **Nota Accessory Tracking (v3.5.0)**: Setiap transaksi pinjaman merekod aksesori yang disertakan semasa check-out dan check-in. Perbezaan akan dipaparkan sebagai discrepancy.

### 4.4. Profil Pengguna (User Profiles)

> **Source of truth (skema sebenar)**: `database/migrations/2025_11_03_043900_create_users_table.php`

Ringkasan medan berkaitan migrasi (bukan senarai penuh):

| Field         | Jenis Data          | Mandatori | Keterangan |
|--------------|---------------------|----------:|-----------|
| id           | bigint, PK          | Ya        | Primary key |
| name         | string(255)         | Ya        | Nama pegawai |
| email        | string(255)         | Ya        | E-mel kerajaan (unique) |
| email_verified_at | timestamp      | Tidak     | Tarikh pengesahan e-mel |
| role         | enum               | Ya        | `staff` / `approver` / `admin` / `superuser` |
| staff_number | string(50)          | Tidak     | Nombor staf (opsyen) |
| division_code| string(20)          | Tidak     | Kod bahagian/unit (string) |
| division_id  | bigint, FK nullable | Tidak     | FK → `divisions.id` (ON DELETE SET NULL) |
| grade_id     | bigint, FK nullable | Tidak     | FK → `grades.id` (ON DELETE SET NULL) |
| position_id  | bigint, FK nullable | Tidak     | FK → `positions.id` (ON DELETE SET NULL) |
| phone        | string(20)          | Tidak     | Telefon |
| mobile       | string(20)          | Tidak     | Telefon bimbit |
| locale       | string(10)          | Ya        | **DEPRECATED v3.6.0**: sentiasa `ms` |
| google_id    | string(255)         | Tidak     | Google OAuth ID (opsyen SSO) |
| theme_preference | string(10)      | Ya        | `light` / `dark` / `system` |

**Indeks (ringkasan)**: `(email)`, `(role)`, `(staff_number)`, `(division_code)`, `(division_id, grade_id)`, `(google_id)`

> **Nota True Hybrid Architecture v3.5.0:**
>
> - **Admin/Superuser**: Diseeded (bukan migrasi) atau dicipta oleh superuser
> - **Staff**: Boleh self-register dengan @motac.gov.my ATAU dimigrasikan dari sistem legacy
> - **Guest**: Role virtual (tidak disimpan, user_id=NULL dalam submissions)
> - **Self-Registration**: Staf mendaftar dengan e-mel @motac.gov.my, pengesahan e-mel WAJIB
> - **Flexible Login**: E-mel penuh ATAU nama pengguna pendek selepas pendaftaran
> - **Account Linking**: Optional - pengguna memilih untuk link submissions tetamu sedia ada
> - **Google SSO**: Optional - boleh diaktifkan untuk @motac.gov.my domain
> - **Enhanced UX**: Onboarding tour, dashboard customization, saved filters, theme preference

### 4.5. Tiket Aduan - Form Reference Code (v3.5.0)

Tambahan medan untuk helpdesk_tickets:

| Field               | Jenis Data  | Mandatori | Keterangan                                        |
| ------------------- | ----------- | --------- | ------------------------------------------------- |
| form_reference_code | string(50)  | Ya        | Kod rujukan borang (DEFAULT PK.(S).MOTAC.07.(L1)) |

### 4.6. Laravel Pulse Tables (v3.5.0)

| Table             | Purpose                    | Key Fields                                    |
| ----------------- | -------------------------- | --------------------------------------------- |
| pulse_values      | Metrics storage            | timestamp, type, key, key_hash, value         |
| pulse_entries     | Detailed entries           | timestamp, type, key, key_hash, value         |
| pulse_aggregates  | Aggregated metrics         | bucket, period, type, key, aggregate, value   |

### 4.7. Laravel Sanctum API Tokens (v3.5.0)

| Field          | Jenis Data  | Mandatori | Keterangan                           |
| -------------- | ----------- | --------- | ------------------------------------ |
| id             | bigint      | Ya        | Primary Key                          |
| tokenable_type | string(255) | Ya        | Model type (App\Models\User)         |
| tokenable_id   | bigint      | Ya        | User ID                              |
| name           | string(255) | Ya        | Token name (e.g., "mobile-app")      |
| token          | string(64)  | Ya        | SHA-256 hashed token (unique)        |
| abilities      | text        | Tidak     | JSON array of abilities              |
| last_used_at   | timestamp   | Tidak     | Tarikh penggunaan terakhir           |
| expires_at     | timestamp   | Tidak     | Tarikh tamat tempoh                  |
| created_at     | timestamp   | Ya        | Tarikh cipta                         |
| updated_at     | timestamp   | Ya        | Tarikh kemaskini                     |

**Indeks:** `(token)`, `(tokenable_type, tokenable_id)`

---

## 5. KESELAMATAN & PRIVASI (Security & Privacy)

- **Data encryption** semasa pemindahan (at-rest & in-transit).
- **Access control**: Hanya team migrasi diberi akses data mentah.
- **Logging**: Semua aktiviti migrasi direkod dalam audit trail.
- **Anonimisasi/Pseudonimisasi** untuk data sensitif jika perlu.

---

## 6. PROSES VALIDASI & UJIAN MIGRASI (Validation & Testing)

- **Dry run migration** pada database staging; bandingkan jumlah dan sampel data.
- **Validation rules**: Semua field wajib mesti diisi, foreign key sah, tiada duplikasi.
- **Post-migration audit**: Laporan error, rekod gagal, perbandingan data asal vs data baru.
- **User Acceptance Test (UAT)**: BPM semak data selepas migrasi.

---

## 7. KAWALAN TADBIR URUS DATA (Data Governance Controls)

- **Dokumentasi penuh** setiap langkah migrasi.
- **Approval & sign-off** oleh BPM sebelum dan selepas migrasi.
- **Backup & rollback**: Backup sebelum migrasi; pelan rollback sekiranya gagal.
- **Compliance review**: Semakan pematuhan ISO 8000 & ISO/IEC 38505-1 selepas migrasi.

---

## 8. PROSES MIGRASI TRUE HYBRID ARCHITECTURE v3.5.0

### 8.1. Langkah 1: Migrasi Staff ke users table (Enhanced)

```sql
-- Migrate legacy Staff to users table dengan medan baharu
INSERT INTO users (
    name, email, phone, staff_number, division_code,
    role, password, email_verified_at, locale, theme_preference,
    guest_submissions_linked, created_at, updated_at
)
SELECT
    name,
    email,
    phone,
    staff_id as staff_number,
    division_code,
    'staff' as role,
    '$2y$12$HASHED_DEFAULT_PASSWORD' as password,
    NOW() as email_verified_at,  -- Auto-verified for migrated staff
    'ms' as locale, -- DEPRECATED v3.6.0: sentiasa 'ms'
    'system' as theme_preference,
    0 as guest_submissions_linked,
    NOW() as created_at,
    NOW() as updated_at
FROM legacy_staff_table
WHERE email LIKE '%@motac.gov.my';  -- Only @motac.gov.my emails

-- (Opsyen) Padankan FK division_id selepas divisions disemai:
-- UPDATE users u
-- JOIN divisions d ON d.code = u.division_code
-- SET u.division_id = d.id
-- WHERE u.division_id IS NULL AND u.division_code IS NOT NULL;
```

### 8.2. Langkah 2: Link Historical Tickets via Email

```sql
-- Link historical helpdesk tickets to Staff users (case-insensitive)
UPDATE helpdesk_tickets ht
INNER JOIN users u ON LOWER(ht.guest_email) = LOWER(u.email)
SET ht.user_id = u.id
WHERE ht.user_id IS NULL AND u.role = 'staff';
```

### 8.3. Langkah 3: Link Historical Loan Applications via Email

```sql
-- Link historical loan applications to Staff users (case-insensitive)
UPDATE loan_applications la
INNER JOIN users u ON LOWER(la.applicant_email) = LOWER(u.email)
SET la.user_id = u.id
WHERE la.user_id IS NULL AND u.role = 'staff';
```

### 8.4. Langkah 4: Update Guest Submissions Linked Count

```sql
-- Update guest_submissions_linked count untuk setiap staff
UPDATE users u
SET guest_submissions_linked = (
    SELECT COUNT(*) FROM helpdesk_tickets WHERE user_id = u.id
) + (
    SELECT COUNT(*) FROM loan_applications WHERE user_id = u.id
)
WHERE role = 'staff';
```

### 8.5. Langkah 5: Setup Dual Audit Tables

```sql
-- Verify audits table (owen-it/laravel-auditing v14.x)
CREATE TABLE IF NOT EXISTS audits (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    user_type VARCHAR(255) NULL,
    user_id BIGINT UNSIGNED NULL,
    event VARCHAR(255) NOT NULL,
    auditable_type VARCHAR(255) NOT NULL,
    auditable_id BIGINT UNSIGNED NOT NULL,
    old_values JSON NULL,
    new_values JSON NULL,
    url TEXT NULL,
    ip_address VARCHAR(45) NULL,
    user_agent VARCHAR(1023) NULL,
    tags VARCHAR(255) NULL,
    guest_identifier VARCHAR(255) NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_auditable (auditable_type, auditable_id),
    INDEX idx_user (user_type, user_id),
    INDEX idx_created_at (created_at)
);

-- Verify activity_log table (spatie/laravel-activitylog v4.x)
CREATE TABLE IF NOT EXISTS activity_log (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    log_name VARCHAR(255) NULL,
    description TEXT NOT NULL,
    subject_type VARCHAR(255) NULL,
    subject_id BIGINT UNSIGNED NULL,
    causer_type VARCHAR(255) NULL,
    causer_id BIGINT UNSIGNED NULL,
    properties JSON NULL,
    batch_uuid CHAR(36) NULL,
    event VARCHAR(255) NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    INDEX idx_subject (subject_type, subject_id),
    INDEX idx_causer (causer_type, causer_id),
    INDEX idx_log_name (log_name)
);
```

### 8.6. Langkah 6: Preserve Guest Submissions

> **PENTING**: Submissions dengan `user_id = NULL` dikekalkan sebagai Guest submissions.
> Tiada tindakan diperlukan - sistem akan terus menyokong Guest submissions.
>
> **Optional Account Linking**: Apabila staff baharu mendaftar, sistem akan memaparkan
> prompt untuk link submissions sedia ada berdasarkan padanan e-mel. Ini adalah PILIHAN pengguna.

### 8.7. Langkah 7: Setup Laravel Pulse Tables

```sql
-- Create pulse_values table
CREATE TABLE IF NOT EXISTS pulse_values (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    timestamp INT UNSIGNED NOT NULL,
    type VARCHAR(255) NOT NULL,
    `key` VARCHAR(255) NOT NULL,
    key_hash BINARY(16) GENERATED ALWAYS AS (UNHEX(MD5(`key`))) STORED NOT NULL,
    value BIGINT NOT NULL,
    INDEX idx_pulse_values_timestamp (timestamp),
    INDEX idx_pulse_values_type_key (type, key_hash),
    UNIQUE INDEX idx_pulse_values_unique (type, key_hash, timestamp)
);

-- Create pulse_entries table
CREATE TABLE IF NOT EXISTS pulse_entries (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    timestamp INT UNSIGNED NOT NULL,
    type VARCHAR(255) NOT NULL,
    `key` VARCHAR(255) NOT NULL,
    key_hash BINARY(16) GENERATED ALWAYS AS (UNHEX(MD5(`key`))) STORED NOT NULL,
    value BIGINT NULL,
    INDEX idx_pulse_entries_timestamp (timestamp),
    INDEX idx_pulse_entries_type_key (type, key_hash)
);

-- Create pulse_aggregates table
CREATE TABLE IF NOT EXISTS pulse_aggregates (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    bucket INT UNSIGNED NOT NULL,
    period INT UNSIGNED NOT NULL,
    type VARCHAR(255) NOT NULL,
    `key` VARCHAR(255) NOT NULL,
    key_hash BINARY(16) GENERATED ALWAYS AS (UNHEX(MD5(`key`))) STORED NOT NULL,
    aggregate VARCHAR(255) NOT NULL,
    value DECIMAL(20, 2) NOT NULL,
    count INT UNSIGNED NULL,
    INDEX idx_pulse_aggregates_bucket (bucket),
    INDEX idx_pulse_aggregates_period_type (period, type, aggregate, bucket),
    UNIQUE INDEX idx_pulse_aggregates_unique (bucket, period, type, aggregate, key_hash)
);
```

### 8.8. Langkah 8: Setup Laravel Sanctum API Tokens

```sql
-- Create personal_access_tokens table
CREATE TABLE IF NOT EXISTS personal_access_tokens (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    tokenable_type VARCHAR(255) NOT NULL,
    tokenable_id BIGINT UNSIGNED NOT NULL,
    name VARCHAR(255) NOT NULL,
    token VARCHAR(64) NOT NULL,
    abilities TEXT NULL,
    last_used_at TIMESTAMP NULL,
    expires_at TIMESTAMP NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    UNIQUE INDEX idx_personal_access_tokens_token (token),
    INDEX idx_personal_access_tokens_tokenable (tokenable_type, tokenable_id)
);
```

### 8.9. Langkah 9: Add Responsible Officer & Accessory Tracking

```sql
-- Add Responsible Officer columns to loan_applications
ALTER TABLE loan_applications
ADD COLUMN IF NOT EXISTS is_applicant_responsible BOOLEAN DEFAULT TRUE,
ADD COLUMN IF NOT EXISTS responsible_officer_name VARCHAR(255) NULL,
ADD COLUMN IF NOT EXISTS responsible_officer_grade VARCHAR(50) NULL,
ADD COLUMN IF NOT EXISTS responsible_officer_phone VARCHAR(50) NULL,
ADD COLUMN IF NOT EXISTS responsible_officer_acknowledgement BOOLEAN DEFAULT FALSE;

-- Create loan_transaction_accessories table
CREATE TABLE IF NOT EXISTS loan_transaction_accessories (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    loan_transaction_id BIGINT UNSIGNED NOT NULL,
    accessory_type ENUM('POWER_ADAPTER', 'BAG', 'MOUSE', 'USB_CABLE', 'HDMI_VGA_CABLE', 'REMOTE', 'OTHERS') NOT NULL,
    accessory_name VARCHAR(100) NULL,
    present_at_checkout BOOLEAN NOT NULL DEFAULT FALSE,
    present_at_checkin BOOLEAN NULL,
    condition_notes TEXT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_loan_transaction (loan_transaction_id),
    FOREIGN KEY (loan_transaction_id) REFERENCES loan_transactions(id) ON DELETE CASCADE
);

-- Update existing loan applications with default Responsible Officer values
UPDATE loan_applications
SET is_applicant_responsible = TRUE
WHERE is_applicant_responsible IS NULL;
```

### 8.10. Langkah 10: Add Form Reference Codes

```sql
-- Add form reference code to helpdesk_tickets
ALTER TABLE helpdesk_tickets
ADD COLUMN IF NOT EXISTS form_reference_code VARCHAR(50) DEFAULT 'PK.(S).MOTAC.07.(L1)';

-- Add form reference code to loan_applications
ALTER TABLE loan_applications
ADD COLUMN IF NOT EXISTS form_reference_code VARCHAR(50) DEFAULT 'PK.(S).MOTAC.07.(L3)';

-- Update existing records
UPDATE helpdesk_tickets SET form_reference_code = 'PK.(S).MOTAC.07.(L1)' WHERE form_reference_code IS NULL;
UPDATE loan_applications SET form_reference_code = 'PK.(S).MOTAC.07.(L3)' WHERE form_reference_code IS NULL;
```

### 8.11. Langkah 11: Add Google SSO Columns (Optional)

```sql
-- Add Google SSO columns to users table (optional feature)
ALTER TABLE users
ADD COLUMN IF NOT EXISTS google_id VARCHAR(255) NULL,
ADD COLUMN IF NOT EXISTS google_avatar VARCHAR(500) NULL,
ADD COLUMN IF NOT EXISTS auth_provider ENUM('local', 'google') DEFAULT 'local';

-- Add index for Google ID lookup
CREATE INDEX IF NOT EXISTS idx_users_google_id ON users(google_id);

-- Update existing users to local auth provider
UPDATE users SET auth_provider = 'local' WHERE auth_provider IS NULL;
```

### 8.12. Langkah 12: Add Enhanced UX Columns

```sql
-- Add Enhanced UX columns to users table
ALTER TABLE users
ADD COLUMN IF NOT EXISTS onboarding_completed BOOLEAN DEFAULT FALSE,
ADD COLUMN IF NOT EXISTS dashboard_layout JSON NULL,
ADD COLUMN IF NOT EXISTS saved_filters JSON NULL,
ADD COLUMN IF NOT EXISTS theme_preference ENUM('light', 'dark', 'system') DEFAULT 'system';

-- Set defaults for existing users (mark onboarding as completed for migrated users)
UPDATE users SET
    onboarding_completed = TRUE,
    theme_preference = 'system'
WHERE onboarding_completed IS NULL;
```

### 8.13. Validasi Post-Migration (Enhanced)

```sql
-- Verify Staff migration count
SELECT COUNT(*) as staff_count FROM users WHERE role = 'staff';

-- Verify email domain compliance (@motac.gov.my only)
SELECT COUNT(*) as invalid_emails
FROM users
WHERE role = 'staff' AND email NOT LIKE '%@motac.gov.my';
-- Expected: 0

-- Verify email_verified_at is set for migrated staff
SELECT COUNT(*) as unverified_migrated_staff
FROM users
WHERE role = 'staff' AND email_verified_at IS NULL AND created_at < NOW();
-- Expected: 0 (all migrated staff should be auto-verified)

-- Verify linked tickets count
SELECT
    COUNT(*) as total_tickets,
    SUM(CASE WHEN user_id IS NOT NULL THEN 1 ELSE 0 END) as staff_tickets,
    SUM(CASE WHEN user_id IS NULL THEN 1 ELSE 0 END) as guest_tickets
FROM helpdesk_tickets;

-- Verify linked loan applications count
SELECT
    COUNT(*) as total_loans,
    SUM(CASE WHEN user_id IS NOT NULL THEN 1 ELSE 0 END) as staff_loans,
    SUM(CASE WHEN user_id IS NULL THEN 1 ELSE 0 END) as guest_loans
FROM loan_applications;

-- Verify form reference codes are set
SELECT COUNT(*) as tickets_without_form_code
FROM helpdesk_tickets WHERE form_reference_code IS NULL OR form_reference_code = '';
-- Expected: 0

SELECT COUNT(*) as loans_without_form_code
FROM loan_applications WHERE form_reference_code IS NULL OR form_reference_code = '';
-- Expected: 0

-- Verify Responsible Officer defaults
SELECT COUNT(*) as loans_without_responsible_flag
FROM loan_applications WHERE is_applicant_responsible IS NULL;
-- Expected: 0

-- Verify Laravel Pulse tables exist
SELECT 'pulse_values' as table_name, COUNT(*) as exists_check
FROM information_schema.tables WHERE table_name = 'pulse_values'
UNION ALL
SELECT 'pulse_entries', COUNT(*) FROM information_schema.tables WHERE table_name = 'pulse_entries'
UNION ALL
SELECT 'pulse_aggregates', COUNT(*) FROM information_schema.tables WHERE table_name = 'pulse_aggregates';
-- Expected: All 1

-- Verify Sanctum tokens table exists
SELECT COUNT(*) as sanctum_table_exists
FROM information_schema.tables WHERE table_name = 'personal_access_tokens';
-- Expected: 1

-- Verify Enhanced UX columns
SELECT COUNT(*) as users_without_theme FROM users WHERE theme_preference IS NULL;
-- Expected: 0

-- Verify dual audit tables
SELECT 'audits' as table_name, COUNT(*) as row_count FROM audits
UNION ALL
SELECT 'activity_log', COUNT(*) FROM activity_log;

-- Verify accessory tracking table exists
SELECT COUNT(*) as accessory_table_exists
FROM information_schema.tables WHERE table_name = 'loan_transaction_accessories';
-- Expected: 1
```

---

## 9. PENUTUP (Conclusion)

Spesifikasi ini memastikan semua migrasi data ke sistem Helpdesk & ICT Asset Loan MOTAC BPM adalah berkualiti, selamat, boleh jejak, dan patuh kepada piawaian antarabangsa dan polisi BPM. True Hybrid Architecture v3.5.0 membolehkan sistem menyokong:

**Core Authentication & Access:**

- **Self-Registration**: Staff mendaftar dengan @motac.gov.my
- **Flexible Login**: E-mel penuh ATAU nama pengguna pendek
- **Optional Account Linking**: Staff memilih untuk link submissions tetamu sedia ada
- **Google SSO**: Optional - boleh diaktifkan untuk @motac.gov.my domain

**Audit & Monitoring:**

- **Dual Audit System**: owen-it/laravel-auditing (compliance) + spatie/laravel-activitylog (operations)
- **Laravel Pulse**: Performance monitoring (admin/superuser)
- **Laravel Telescope**: Debugging (superuser sahaja)

**API & Integration:**

- **Laravel Sanctum**: API token authentication untuk integrasi masa hadapan
- **RESTful API**: Versioned endpoints (/api/v1/)

**Enhanced Features:**

- **Responsible Officer**: Tracking untuk loan applications
- **Accessory Tracking**: Check-out/check-in dengan discrepancy detection
- **Form Reference Codes**: PK.(S).MOTAC.07.(L1) dan PK.(S).MOTAC.07.(L3)
- **Enhanced UX**: Onboarding tour, dashboard customization, saved filters, theme preference
- **Guest Submissions**: Kekal disokong dengan user_id=NULL

Semua pihak terlibat wajib mematuhi dokumen ini sepanjang proses migrasi.

---
