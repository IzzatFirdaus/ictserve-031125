# Spesifikasi Migrasi Data (Data Migration Specification - DMS)

**Sistem ICTServe**  
**Versi:** 3.5.0 (SemVer)  
**Tarikh Kemaskini:** 30 November 2025  
**Status:** Aktif  
**Klasifikasi:** Terhad - Dalaman MOTAC  
**Penulis:** Pasukan Pembangunan BPM MOTAC  
**Standard Rujukan:** ISO 8000 (Data Quality), ISO/IEC 38505-1 (Governance of Data)

---

## Maklumat Dokumen (Document Information)

| Atribut              | Nilai                                     |
| -------------------- | ----------------------------------------- |
| **Versi**            | 3.5.0                                     |
| **Tarikh Kemaskini** | 30 November 2025                          |
| **Status**           | Aktif                                     |
| **Klasifikasi**      | Terhad - Dalaman MOTAC                    |
| **Pematuhi**         | ISO 8000, ISO/IEC 38505-1                 |
| **Bahasa**           | Bahasa Melayu (utama), English (teknikal) |

> Notis Penggunaan Dalaman: Spesifikasi ini adalah untuk migrasi data dalaman MOTAC; pastikan pematuhan PDPA.

---

## Sejarah Perubahan (Changelog)

| Versi | Tarikh           | Perubahan                                                                                                                                                                                                                                                                                                                                               | Penulis     |
| ----- | ---------------- | ------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------- | ----------- |
| 3.5.0 | 30 November 2025 | True Hybrid Architecture v3.5.0: Penyelarasan dengan D00-D04 v3.5.0. Self-registration (@motac.gov.my), flexible login (email/username), optional guest-to-account linking, dual audit system (owen-it + spatie), Laravel Telescope (superuser only). Hapus rujukan LDAP/SSO. Tukar `divisions` kepada `departments`. Pematuhan Jabatan Digital Negara. | Pasukan BPM |
| 3.4.0 | 6 Januari 2026   | Hybrid Architecture v3.4.0: Staff migration to users table, email-based linking, restore LDAP/SSO as optional authentication. Penyelarasan dengan D00-D08 v3.4.0.                                                                                                                                                                                       | Pasukan BPM |
| 3.3.0 | 29 November 2025 | Hapus Section 4.4 Profil Pengguna migration (staff tidak dimigrasikan). Penyelarasan penuh Guest-First architecture.                                                                                                                                                                                                                                    | Pasukan BPM |
| 3.2.0 | 29 November 2025 | Penyelarasan dengan Guest-First architecture: ganti FK user dengan string fields (name, email, division_code, grade). Klarifikasi hanya admin/superuser dimigrasikan ke users table.                                                                                                                                                                    | Pasukan BPM |
| 2.2.0 | 29 November 2025 | Kemaskini dokumentasi sistem: pengesahan versi teknologi semasa. Penyelarasan dengan D00-D05.                                                                                                                                                                                                                                                           | Pasukan BPM |
| 2.1.0 | 6 Januari 2025   | Kemaskini rujukan teknologi: Laravel 12.40.1, PHP 8.2.12                                                                                                                                                                                                                                                                                                | Pasukan BPM |
| 2.0.0 | 17 Oktober 2025  | Penyeragaman mengikut D00-D14, SemVer, cross-reference                                                                                                                                                                                                                                                                                                  | Pasukan BPM |
| 1.0.0 | September 2025   | Versi awal spesifikasi migrasi data                                                                                                                                                                                                                                                                                                                     | Pasukan BPM |

---

## Rujukan Dokumen Berkaitan (Related Document References)

- **[D00_SYSTEM_OVERVIEW.md]** - Ringkasan Sistem
- **[D05_DATA_MIGRATION_PLAN.md]** - Pelan Migrasi Data (strategy & timeline)
- **[D09_DATABASE_DOCUMENTATION.md]** - Dokumentasi Pangkalan Data (target schema)
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
| submitter_name          | string(255) | Ya          | Nama penghantar                       |
| submitter_email         | string(255) | Ya          | E-mel penghantar                      |
| submitter_phone         | string(50)  | Ya          | Nombor telefon                        |
| submitter_division_code | string(50)  | Ya          | Kod bahagian                          |
| submitter_grade         | string(50)  | Tidak       | Gred jawatan                          |
| damage_type             | string(100) | Ya          | Kategori kerosakan (dropdown)         |
| damage_info             | text        | Ya          | Keterangan masalah                    |
| asset_no                | string(100) | Conditional | Diisi jika aduan berkaitan perkakasan |
| declaration             | boolean     | Ya          | Perakuan/disclaimer                   |
| status                  | string(50)  | Ya          | Status tiket                          |
| created_at              | timestamp   | Ya          | Tarikh aduan dibuat                   |
| updated_at              | timestamp   | Ya          | Tarikh kemaskini terakhir             |

**Indeks:** `(user_id, status)`, `(submitter_email, status)`

> **Nota Hybrid Model**: `user_id` NULL = Guest; NOT NULL = Staff berdaftar

### 4.2. Data Pinjaman Peralatan ICT

| Field                   | Jenis Data  | Mandatori   | Keterangan                           |
| ----------------------- | ----------- | ----------- | ------------------------------------ |
| id                      | bigint      | Ya          | Primary key                          |
| user_id                 | bigint      | Tidak       | FK → users.id (NULL untuk Guest)     |
| applicant_name          | string(255) | Ya          | Nama pemohon                         |
| applicant_email         | string(255) | Ya          | E-mel pemohon                        |
| applicant_phone         | string(50)  | Ya          | Telefon pemohon                      |
| applicant_division_code | string(50)  | Ya          | Kod bahagian                         |
| applicant_grade         | string(50)  | Ya          | Jawatan & gred                       |
| purpose                 | string(255) | Ya          | Tujuan pinjaman                      |
| location                | string(255) | Ya          | Lokasi penggunaan                    |
| loan_start_date         | date        | Ya          | Tarikh mula pinjam                   |
| loan_end_date           | date        | Ya          | Tarikh dijangka pulang               |
| equipment_list          | json/text   | Ya          | Senarai peralatan, kuantiti, catatan |
| declaration             | boolean     | Ya          | Perakuan/disclaimer                  |
| endorsement_status      | string(20)  | Ya          | PENDING / APPROVED / REJECTED        |
| endorsement_date        | date        | Conditional | Tarikh kelulusan (jika diluluskan)   |
| return_notes            | text        | Tidak       | Catatan semasa pulang                |
| created_at              | timestamp   | Ya          | Tarikh permohonan                    |
| updated_at              | timestamp   | Ya          | Tarikh kemaskini terakhir            |

**Indeks:** `(user_id, status)`, `(applicant_email, status)`

> **Nota Hybrid Model**: `user_id` NULL = Guest; NOT NULL = Staff berdaftar. Kelulusan melalui email token (dual approval workflow).

### 4.3. Inventori Peralatan ICT (Asset Inventory)

| Field       | Jenis Data  | Mandatori | Keterangan                                 |
| ----------- | ----------- | --------- | ------------------------------------------ |
| id          | bigint      | Ya        | Primary Key                                |
| asset_type  | string(100) | Ya        | Jenis peralatan                            |
| brand       | string(100) | Ya        | Jenama                                     |
| model       | string(100) | Ya        | Model                                      |
| serial_no   | string(100) | Ya        | No Siri / Tag ID                           |
| accessories | json/text   | Tidak     | Senarai aksesori                           |
| status      | string(50)  | Ya        | Status (Available, Loaned, Returned, etc.) |
| created_at  | timestamp   | Ya        | Tarikh daftar                              |
| updated_at  | timestamp   | Ya        | Tarikh terakhir kemaskini                  |

### 4.4. Profil Pengguna (User Profiles)

| Field                    | Jenis Data                     | Mandatori | Keterangan                                         |
| ------------------------ | ------------------------------ | --------- | -------------------------------------------------- |
| id                       | bigint, PK                     | Ya        | Primary Key                                        |
| name                     | string(255)                    | Ya        | Nama pegawai                                       |
| email                    | string(255)                    | Ya        | E-mel kerajaan @motac.gov.my (unique)              |
| email_verified_at        | timestamp                      | Tidak     | Tarikh pengesahan e-mel                            |
| phone                    | string(30)                     | Ya        | Telefon pegawai                                    |
| department_id            | bigint, FK nullable            | Tidak     | FK → departments.id (nullable, ON DELETE SET NULL) |
| grade                    | string(50)                     | Tidak     | Gred jawatan (VARCHAR, simplified)                 |
| staff_number             | string(50)                     | Tidak     | Nombor staf (optional)                             |
| role                     | enum(staff, admin, superuser)  | Ya        | Peranan sistem (DEFAULT 'staff')                   |
| password                 | string(255)                    | Ya        | Hash kata laluan                                   |
| two_factor_secret        | text (nullable)                | Tidak     | Rahsia TOTP (untuk superuser)                      |
| two_factor_confirmed_at  | timestamp                      | Tidak     | Tarikh pengesahan 2FA                              |
| locale                   | enum(ms, en)                   | Ya        | Bahasa pilihan (DEFAULT 'ms')                      |
| notify_email_frequency   | enum(immediate, daily, weekly) | Ya        | Kekerapan e-mel (DEFAULT 'immediate')              |
| notify_in_app            | boolean                        | Ya        | Notifikasi dalam aplikasi (DEFAULT TRUE)           |
| guest_submissions_linked | integer                        | Ya        | Bilangan submissions dilink (DEFAULT 0)            |
| remember_token           | string(100)                    | Tidak     | Token remember me                                  |
| last_login_at            | timestamp                      | Tidak     | Tarikh login terakhir                              |
| last_login_ip            | string(45)                     | Tidak     | IP login terakhir                                  |
| created_at               | timestamp                      | Ya        | Tarikh cipta                                       |
| updated_at               | timestamp                      | Ya        | Tarikh kemaskini                                   |
| deleted_at               | timestamp                      | Tidak     | Soft delete timestamp                              |

**Indeks:** `(email)`, `(email_prefix via SUBSTRING_INDEX)`, `(role)`, `(department_id)`, `(staff_number)`

> **Nota True Hybrid Architecture v3.5.0:**
>
> - **Admin/Superuser**: Diseeded (bukan migrasi) atau dicipta oleh superuser
> - **Staff**: Boleh self-register dengan @motac.gov.my ATAU dimigrasikan dari sistem legacy
> - **Guest**: Role virtual (tidak disimpan, user_id=NULL dalam submissions)
> - **Self-Registration**: Staf mendaftar dengan e-mel @motac.gov.my, pengesahan e-mel WAJIB
> - **Flexible Login**: E-mel penuh ATAU nama pengguna pendek selepas pendaftaran
> - **Account Linking**: Optional - pengguna memilih untuk link submissions tetamu sedia ada
> - **Tiada LDAP/SSO**: Semua authentication melalui Laravel Breeze sahaja

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
    name, email, phone, department_id, grade, staff_number,
    role, password, email_verified_at, locale,
    notify_email_frequency, notify_in_app, guest_submissions_linked,
    created_at, updated_at
)
SELECT
    name,
    email,
    phone,
    department_id,
    grade,
    staff_id as staff_number,
    'staff' as role,
    '$2y$12$HASHED_DEFAULT_PASSWORD' as password,
    NOW() as email_verified_at,  -- Auto-verified for migrated staff
    'ms' as locale,
    'immediate' as notify_email_frequency,
    TRUE as notify_in_app,
    0 as guest_submissions_linked,
    NOW() as created_at,
    NOW() as updated_at
FROM legacy_staff_table
WHERE email LIKE '%@motac.gov.my';  -- Only @motac.gov.my emails
```

### 8.2. Langkah 2: Link Historical Tickets via Email

```sql
-- Link historical helpdesk tickets to Staff users (case-insensitive)
UPDATE helpdesk_tickets ht
INNER JOIN users u ON LOWER(ht.submitter_email) = LOWER(u.email)
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

### 8.7. Validasi Post-Migration (Enhanced)

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
```

---

## 9. PENUTUP (Conclusion)

Spesifikasi ini memastikan semua migrasi data ke sistem Helpdesk & ICT Asset Loan MOTAC BPM adalah berkualiti, selamat, boleh jejak, dan patuh kepada piawaian antarabangsa dan polisi BPM. True Hybrid Architecture v3.5.0 membolehkan sistem menyokong:

- **Self-Registration**: Staff mendaftar dengan @motac.gov.my
- **Flexible Login**: E-mel penuh ATAU nama pengguna pendek
- **Optional Account Linking**: Staff memilih untuk link submissions tetamu sedia ada
- **Dual Audit System**: owen-it/laravel-auditing (compliance) + spatie/laravel-activitylog (operations)
- **Guest Submissions**: Kekal disokong dengan user_id=NULL
- **Tiada LDAP/SSO**: Semua authentication melalui Laravel Breeze sahaja

Semua pihak terlibat wajib mematuhi dokumen ini sepanjang proses migrasi.

---
