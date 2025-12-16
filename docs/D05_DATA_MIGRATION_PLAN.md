# Pelan Migrasi Data (Data Migration Plan - DMP)

**Sistem ICTServe**  
**Versi:** 3.6.1 (SemVer)  
**Tarikh Kemaskini:** 17 Disember 2025  
**Status:** Aktif  
**Klasifikasi:** Terhad - Dalaman MOTAC  
**Penulis:** Pasukan Pembangunan BPM MOTAC  
**Standard Rujukan:** ISO 8000 (Data Quality), ISO/IEC 27701 (Privacy Information Management)

---

## Maklumat Dokumen (Document Information)

| Atribut              | Nilai                                     |
| -------------------- | ----------------------------------------- |
| **Versi**            | 3.6.1                                     |
| **Tarikh Kemaskini** | 17 Disember 2025                          |
| **Status**           | Aktif                                     |
| **Klasifikasi**      | Terhad - Dalaman MOTAC                    |
| **Pematuhi**         | ISO 8000, ISO/IEC 27701                   |
| **Bahasa**           | Bahasa Melayu sahaja (v3.6.0)             |
| **Pematuhi**         | ISO 8000, ISO/IEC 27701                   |
| **Bahasa**           | Bahasa Melayu (utama), English (teknikal) |

> Notis Penggunaan Dalaman: Migrasi data ini melibatkan data dalaman MOTAC dan tidak berkaitan data awam.

---

## Sejarah Perubahan (Changelog)

| Versi | Tarikh           | Perubahan                                                                                                                                                                                                | Penulis     |
| ----- | ---------------- | -------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------- | ----------- |
| 3.5.0 | 1 Disember 2025  | True Hybrid Architecture v3.5.0: Self-registration (@motac.gov.my), flexible login (email/username), account linking, dual audit (owen-it + spatie), Laravel Pulse, Sanctum API, Google SSO (optional), MOTAC branding. Penyelarasan dengan D00-D04 v3.5.0. | Pasukan BPM |
| 3.6.0 | 8 Disember 2025  | Bahasa Melayu sahaja untuk antara muka: Kemaskini rujukan bilingual support→Bahasa Melayu sahaja dalam migration plan. Penyelarasan dengan D00-D17 v3.6.0.                                                  | Pasukan BPM |
| 3.7.0 | 15 Disember 2025 | AI Chatbot Integration: Tambah migrasi data AI (FAQ, dokumen, embeddings, conversation history). Rujukan D18 v1.0.0 Cloud Hybrid AI Architecture (Ollama + AWS Bedrock).                                    | Pasukan BPM |
| 3.4.0 | 30 November 2025 | Hybrid Architecture v3.4.0: Migrate legacy staff to users table, link historical submissions via email, restore LDAP/SSO as optional authentication. Penyelarasan dengan D00-D08 v3.4.0.                 | Pasukan BPM |
| 3.3.0 | 29 November 2025 | Penyelarasan versi dengan D00 v3.3.0 dan D04 v3.3.0: standardisasi dokumentasi guest-first architecture, token-based workflows, disaster recovery plan, dan teknologi stack terkini (Playwright 1.56.1). | Pasukan BPM |
| 3.1.0 | 29 November 2025 | Kemaskini dokumentasi sistem: pengesahan versi teknologi semasa (Laravel 12.40.1, PHP 8.2.12). Penyelarasan dengan D00-D04.                                                                              | Pasukan BPM |
| 3.0.0 | 22 Januari 2025  | Kemaskini kepada seni bina guest-first: tiada migrasi akaun pengguna tetamu, fokus kepada data pentadbiran dan rekod sejarah                                                                             | Pasukan BPM |
| 2.1.0 | 6 Januari 2025   | Kemaskini rujukan teknologi: Laravel 12.40.1, PHP 8.2.12                                                                                                                                                 | Pasukan BPM |
| 2.0.0 | 17 Oktober 2025  | Penyeragaman mengikut D00-D14, SemVer, cross-reference                                                                                                                                                   | Pasukan BPM |
| 1.0.0 | September 2025   | Versi awal pelan migrasi data                                                                                                                                                                            | Pasukan BPM |

---

## Rujukan Dokumen Berkaitan (Related Document References)

- **[D00_SYSTEM_OVERVIEW.md]** - Ringkasan Sistem (v3.5.0)
- **[D03_SOFTWARE_REQUIREMENTS_SPECIFICATION.md]** - Spesifikasi Keperluan Perisian (v3.5.0)
- **[D04_SOFTWARE_DESIGN_DOCUMENT.md]** - Dokumen Rekabentuk Perisian (v3.5.0)
- **[D06_DATA_MIGRATION_SPECIFICATION.md]** - Spesifikasi Migrasi Data (detail teknikal)
- **[D09_DATABASE_DOCUMENTATION.md]** - Dokumentasi Pangkalan Data (target schema, dual audit)
- **[D11_TECHNICAL_DESIGN_DOCUMENTATION.md]** - Dokumentasi Rekabentuk Teknikal
- **[D15_LANGUAGE_MS_EN.md]** - Panduan Lokalisasi (Bahasa Melayu sahaja, v3.6.0)
- **[D18_AI_CHATBOT_OLLAMA_BEDROCK.md]** - AI Chatbot Integration (Cloud Hybrid AI, v1.0.0)
- **[GLOSSARY.md]** - Glosari Istilah Sistem

---

## 1. TUJUAN DOKUMEN (Purpose)

Dokumen ini menerangkan perancangan menyeluruh bagi migrasi data ke sistem **Helpdesk & ICT Asset Loan** yang berasaskan Laravel 12.40.1 untuk Bahagian Pengurusan Maklumat (BPM), MOTAC. Pelan ini mematuhi piawaian **ISO 8000** untuk kualiti data (data quality) dan **ISO/IEC 27701** untuk pengurusan privasi maklumat (privacy information management).

**Nota Penting**: Sistem baharu menggunakan True Hybrid Architecture v3.5.0 di mana staff boleh self-register dengan @motac.gov.my dan log masuk ATAU gunakan borang tetamu. Migrasi data fokus kepada:

- **Migrate Legacy Staff**: Populate users table dengan staff data untuk enable login (Laravel Breeze dengan self-registration @motac.gov.my) dan link historical submissions
- **Email Verification Setup**: Set email_verified_at untuk staff dimigrasikan (auto-verified untuk existing staff)
- Rekod sejarah tiket helpdesk (link ke user_id jika staff, NULL jika guest)
- Rekod sejarah permohonan pinjaman aset (link ke user_id jika staff, NULL jika guest)
- Data inventori aset ICT
- Akaun pentadbir sistem (admin & superuser)
- Metadata dan audit trail

---

## 2. SKOP MIGRASI (Scope)

- Migrasi data berkaitan aduan ICT, inventori aset, dan sejarah pinjaman dari sistem lama (manual, Excel, Access, atau sistem digital terdahulu) ke sistem baru Laravel 12.40.1.
- Data yang terlibat:
  - **Staff Profiles**: Migrate legacy staff ke users table (role='staff') untuk enable self-registration dan Dashboard access. Termasuk medan baharu: email_verified_at, locale, notify_email_frequency, notify_in_app, staff_number, guest_submissions_linked
  - **Tiket Helpdesk**: Rekod sejarah tiket dengan link ke user_id (jika staff) atau NULL (jika guest)
  - **Permohonan Pinjaman Aset**: Rekod sejarah permohonan dengan link ke user_id (jika staff) atau NULL (jika guest)
  - **Inventori Aset ICT**: Data lengkap aset termasuk kategori, status, dan sejarah penggunaan
  - **Akaun Pentadbir**: Akaun admin dan superuser (seeded, bukan migrasi)
  - **Bahagian/Unit**: Rujukan bahagian MOTAC untuk validasi
- Termasuk metadata (timestamp, status, logs) & audit trail.
- **True Hybrid Model v3.5.0**: Staff dimigrasikan ke users table dengan self-registration capability (@motac.gov.my); historical submissions dilink via email matching; optional guest-to-account linking

---

## 3. SUMBER DATA (Data Sources)

- **Manual Records**: Borang kertas, fail PDF, dokumen cetak (tiket helpdesk dan permohonan pinjaman lama)
- **Digital Files**: Microsoft Excel, CSV, Access DB, sistem aduan lama
- **Sistem Sedia Ada**: Database legacy, API, atau sistem pengurusan aset terdahulu
- **Direktori Staff Legacy**: Untuk validasi bahagian dan gred pegawai (import ke users table dengan self-registration capability)

---

## 4. PRINSIP MIGRASI (Migration Principles)

- **Integrity**: Data dipindahkan tanpa kehilangan, perubahan, atau kerosakan.
- **Quality**: Data dibersihkan, distandardkan, dan valid mengikut ISO 8000 (Data Quality).
- **Privacy & Security**: Pemindahan dan penyimpanan data patuh ISO/IEC 27701; data peribadi dilindungi, hanya access role tertentu dibenarkan.
- **Traceability**: Setiap rekod migrasi boleh dijejak (audit trail).
- **Rollback Capability**: Pelan pemulihan sekiranya migrasi gagal.

---

## 5. LANGKAH-LANGKAH MIGRASI (Migration Steps)

### 5.1. Data Assessment & Mapping

- **Inventori Data**: Kenalpasti semua sumber data, struktur, dan owner
- **Data Mapping**: Padankan field sumber ke field dalam sistem Laravel:
  - Staff:
    - `staff_name` → `users.name`
    - `staff_email` → `users.email` (mesti @motac.gov.my)
    - `department_code` → `users.department_id`
    - `staff_id` → `users.staff_number` (optional)
    - Set `email_verified_at` = NOW() (auto-verified untuk existing staff)
    - Set `locale` = 'ms' (default)
    - Set `notify_email_frequency` = 'immediate' (default)
    - Set `notify_in_app` = TRUE (default)
    - Set `guest_submissions_linked` = 0 (akan dikemaskini oleh linking script)
  - Tiket helpdesk: `ticket_no` → `helpdesk_tickets.ticket_number`, `submitter_email` → link via `users.email` → `user_id`
  - Pinjaman: `loan_ref` → `loan_applications.reference`, `applicant_email` → link via `users.email` → `user_id`
  - Aset: `asset_id_legacy` → `assets.tag_id`, `asset_name` → `assets.name`
- **Data Dictionary**: Sediakan kamus data untuk semua field
- **Nota Hybrid Model**: Staff dimigrasikan ke users table; submissions dilink via email matching (user_id NOT NULL = Staff, NULL = Guest)

### 5.2. Data Cleansing & Standardization

- **Deduplication**: Buang rekod berganda berdasarkan nombor rujukan unik
- **Validation**: Pastikan format, completeness, dan konsistensi:
  - Tarikh dalam format `YYYY-MM-DD`
  - E-mel dalam format valid (RFC 5322)
  - Telefon dalam format standard Malaysia
  - Enum values (status, priority, kategori) mematuhi definisi sistem baharu
- **Standardization**: Tukar kod/kategori lama ke kod baru sistem Laravel:
  - Status tiket: `OPEN`, `IN_PROGRESS`, `AWAITING_INFO`, `RESOLVED`, `CLOSED`
  - Status pinjaman: `PENDING_SUPERVISOR_APPROVAL`, `APPROVED`, `REJECTED`, `AWAITING_COLLECTION`, `ON_LOAN`, `RETURNED`, `DAMAGED`
  - Priority: `LOW`, `MEDIUM`, `HIGH`, `CRITICAL`
- **Anonymization**: Pastikan data peribadi sensitif dihashed/encrypted mengikut PDPA

### 5.3. Data Migration Tools & Scripts

- **Laravel Migrations**: Gunakan `php artisan migrate` untuk struktur database
- **Laravel Seeders**: Gunakan `php artisan db:seed` untuk data rujukan (bahagian, kategori)
- **Custom Import Scripts**: Skrip PHP untuk import data sejarah:
  - `ImportStaffUsersCommand` - Migrate legacy staff ke users table dengan medan baharu (role='staff')
  - `ImportHelpdeskTicketsCommand` - Import tiket helpdesk lama
  - `ImportLoanApplicationsCommand` - Import permohonan pinjaman lama
  - `ImportAssetsCommand` - Import inventori aset
  - `LinkHistoricalSubmissionsCommand` - Link submissions ke user_id via email matching
  - `UpdateGuestSubmissionsCountCommand` - Kemaskini guest_submissions_linked count
  - `SetupDualAuditTablesCommand` - Verify/create audit tables (audits + activity_log)
- **Laravel Excel Package**: Untuk import CSV/Excel dengan validasi
- **Batch Processing**: Gunakan Laravel Queue untuk import besar (>1000 rekod)
- **Logging**: Setiap proses import dilog dalam `storage/logs/migration.log` untuk audit dan troubleshooting
- **Progress Tracking**: Gunakan progress bar dan notification untuk pemantauan real-time

### 5.4. Data Migration Execution

- **Dry Run**: Ujian migrasi di staging/dev environment:
  - Jalankan skrip import dengan flag `--dry-run`
  - Semak hasil dalam database staging
  - Validasi integriti data dan foreign key constraints
- **Validation**: Cross-check jumlah rekod, field penting, dan random sampling:
  - Bandingkan jumlah rekod sumber vs destinasi
  - Semak field kritikal (ticket_number, reference, email, status)
  - Random sampling 5% rekod untuk validasi manual
- **Go-Live Migration**: Laksanakan migrasi pada waktu off-peak (hujung minggu/cuti umum):
  - Pastikan full backup database tersedia
  - Aktifkan maintenance mode (`php artisan down`)
  - Jalankan skrip migrasi dengan monitoring
  - Verify data integrity selepas migrasi
  - Nyahaktif maintenance mode (`php artisan up`)
- **Post-Migration Review**: Audit data dalam sistem baru:
  - Semak error log (`storage/logs/migration.log`)
  - Verify foreign key relationships
  - Test functionality utama (create ticket, create loan application)
  - Generate migration report untuk dokumentasi

### 5.5. Data Protection & Privacy

- **Encryption**:
  - Data at-rest: MySQL encryption untuk field sensitif
  - Data in-transit: HTTPS/TLS untuk semua komunikasi
  - Token hashing: SHA-512 untuk approval tokens dan status tokens
- **Access Control**:
  - Data migrasi hanya boleh diakses oleh admin & superuser
  - Gunakan Laravel Policies untuk authorization
  - Audit trail untuk semua akses data migrasi
- **Data Retention**:
  - Hapus data peribadi dari sistem lama mengikut polisi retention MOTAC (7 tahun untuk rekod audit)
  - Archive data lama ke cold storage selepas migrasi berjaya
  - Secure deletion menggunakan `shred` atau equivalent untuk fail sensitif
- **PDPA Compliance**:
  - Pastikan consent declaration untuk semua rekod lama
  - Anonymize data jika consent tidak tersedia
  - Document data processing activities (DPA)

---

## 6. JADUAL MIGRASI (Migration Schedule)

| Fasa                | Tempoh   | Aktiviti                                        | Output                                 |
| ------------------- | -------- | ----------------------------------------------- | -------------------------------------- |
| Penilaian & Mapping | 1 minggu | Data inventory, mapping, dictionary             | Data mapping document, data dictionary |
| Cleansing/Standard  | 1 minggu | Deduplication, validation, standardization      | Cleaned data files, validation report  |
| Skrip & Ujian       | 2 minggu | Scripting, dry run, validation, testing         | Migration scripts, test report         |
| Migrasi Sebenar     | 1-2 hari | Go-live migration, backup, verification         | Migrated database, backup files        |
| Audit & Review      | 3 hari   | Post-migration review, reporting, documentation | Migration report, audit log            |

**Nota**: Jadual ini adalah anggaran dan boleh diselaraskan berdasarkan saiz data dan kompleksiti migrasi.

---

## 7. RISIKO & MITIGASI (Risks & Mitigation)

| Risiko                         | Kesan     | Kebarangkalian | Langkah Mitigasi                                                           |
| ------------------------------ | --------- | -------------- | -------------------------------------------------------------------------- |
| Data rosak/kehilangan          | Tinggi    | Rendah         | Full backup sebelum migrasi, dry run di staging, rollback script tersedia  |
| Data duplikasi/tidak konsisten | Sederhana | Sederhana      | Cleansing menyeluruh, validation rules ketat, mapping yang teliti          |
| Kebocoran data peribadi        | Tinggi    | Rendah         | Encryption at-rest & in-transit, access control ketat, audit trail lengkap |
| Fail integrasi legacy          | Sederhana | Sederhana      | Early testing dengan sample data, manual import sebagai fallback           |
| Foreign key constraint errors  | Sederhana | Sederhana      | Validate relationships sebelum import, import dalam urutan yang betul      |
| Performance degradation        | Rendah    | Sederhana      | Batch processing, queue jobs, optimize database indexes                    |
| Downtime melebihi window       | Sederhana | Rendah         | Rehearsal migration, optimize scripts, parallel processing jika sesuai     |

---

## 8. KAWALAN KUALITI & AUDIT (Quality & Audit Controls)

- **Verification**:
  - Setiap batch migrasi diverifikasi dengan random sampling (5% rekod)
  - Cross-check jumlah rekod sumber vs destinasi
  - Validate foreign key relationships
  - Test functionality dengan data yang dimigrasi
- **Audit Trail**:
  - Skrip log semua aktiviti migrasi dalam `storage/logs/migration.log`
  - Laravel Auditing package merekod semua perubahan data
  - Timestamp dan user ID untuk setiap operasi migrasi
- **Reporting**:
  - Laporan status migrasi real-time kepada BPM
  - Error report dengan details dan recommended actions
  - Data quality report dengan metrics (completeness, accuracy, consistency)
  - Final migration report dengan summary dan lessons learned
- **Quality Metrics**:
  - Data completeness: >95% field wajib diisi
  - Data accuracy: >98% validation pass rate
  - Data consistency: 100% foreign key integrity
  - Migration success rate: >99% rekod berjaya dimigrasi

---

## 9. PELAN PEMULIHAN BENCANA (Disaster Recovery Plan)

**Pematuhan Standard**: ISO/IEC/IEEE 12207:2017 (Software Lifecycle) §7.5 (Maintenance & Support)

Sistem **Helpdesk & ICT Asset Loan MOTAC BPM** mesti memiliki pelan pemulihan bencana (Disaster Recovery Plan) yang komprehensif untuk memastikan kontinuitas bisnis dan perlindungan data dalam situasi darurat.

### 9.1. Tujuan & Scope

- **Tujuan**: Memastikan sistem dapat dipulihkan dengan cepat & data dapat di-restore dengan aman dalam event bencana
- **Scope**: Seluruh sistem termasuk aplikasi, database, backup storage, dan infrastructure

### 9.2. Sasaran Pemulihan (Recovery Targets)

| Target                               | Nilai                | Justifikasi                                                      |
| ------------------------------------ | -------------------- | ---------------------------------------------------------------- |
| **RTO (Recovery Time Objective)**    | 4 jam                | Sistem mesti online dalam 4 jam selepas bencana terdeteksi       |
| **RPO (Recovery Point Objective)**   | 1 jam                | Data loss tidak boleh lebih dari 1 jam (automated hourly backup) |
| **MTBF (Mean Time Between Failure)** | >8000 jam (11 bulan) | Target uptime 99.5% → ~3.5 hours/month allowable downtime        |
| **MTTR (Mean Time To Recover)**      | 2 jam                | Average recovery time target                                     |

### 9.3. Skenario Bencana & Tindakan (Disaster Scenarios & Response)

| Skenario                                 | Jenis          | Tindakan Respons                                                    | Waktu Estimasi |
| ---------------------------------------- | -------------- | ------------------------------------------------------------------- | -------------- |
| **Database Corruption**                  | Data           | Run DB integrity check; restore from last clean backup              | 30-60 min      |
| **Server Disk Full**                     | Infrastructure | Extend disk space; purge old logs                                   | 15-30 min      |
| **Network Outage**                       | Infrastructure | Reroute via backup network; alert admin                             | 10-20 min      |
| **Cybersecurity Incident (Data Breach)** | Security       | Isolate system; forensics; patch vulnerability; restore from backup | 2-4 hours      |
| **Complete Data Center Failure**         | Critical       | Activate DR site; restore from encrypted backup (cold storage)      | 4 hours        |
| **Ransomware Attack**                    | Security       | Immediate isolation; restore from immutable backup                  | 2-4 hours      |
| **User Registration Failure**            | Operations     | Verify email domain validation; check DNS MX; restore from backup   | 30-60 min      |

### 9.4. Backup Strategy

- **Type**: Incremental daily + full weekly backups
- **Location**: Local (NAS) + remote (cloud/encrypted storage off-site)
- **Encryption**: AES-256 encrypted with keys stored in separate HSM/Vault
- **Schedule**:
  - Full backup: Every Sunday 2:00 AM UTC (6 hours full retention)
  - Incremental: Daily Mon-Sat 2:00 AM UTC (30 days retention)
  - Archive: Move >30 day backups to cold storage (7 years retention for audit)
- **Verification**: Monthly restore test pada staging environment untuk confirm integrity

### 9.5. Failover & Failback Procedures

**Failover Procedure** (when primary system down):

1. Detect failure via monitoring alerts (email/SMS to on-call)
2. Validate failure confirmed (manual check on critical issues)
3. Initiate failover to secondary/DR site:
   - Start standby application servers
   - Point DNS to secondary server IP
   - Restore latest database backup to DR DB
   - Verify system accessibility untuk users
4. Notify stakeholders (BPM, management) of incident & ETA
5. Begin incident investigation & forensics

**Failback Procedure** (when primary system recovered):

1. Fix underlying issue on primary system
2. Restore data consistency (sync with secondary)
3. Perform full system test on primary
4. Graceful switchback (during low-usage window if possible)
5. Monitor primary for stability (30 minutes)
6. Document incident & lessons learned

### 9.6. Dokumentasi & Testing

- **Runbook**: Step-by-step failover/failback procedures documented dan ditest quarterly
- **Contact List**: Emergency contacts (DBA, DevOps, Management) accessible 24/7
- **DR Test**: Full DR drill conducted semi-annually (Oct & Apr)
- **Documentation**: Keep runbook updated post-incident with actual timings & issues encountered

**Rujukan**: Lihat **[D09_DATABASE_DOCUMENTATION.md]** §7-8 untuk backup & audit logging strategy yang complementary dengan disaster recovery plan ini.

---

## 10. PERTIMBANGAN KHUSUS TRUE HYBRID ARCHITECTURE v3.5.0

### 10.1. Migrasi Staff ke Users Table (Enhanced)

Sistem baharu menggunakan True Hybrid Architecture dengan self-registration. Strategi migrasi:

- **Migrate Legacy Staff**: Populate users table dengan staff data lengkap
- **Email Verification**: Set `email_verified_at` = NOW() untuk staff dimigrasikan (auto-verified)
- **New User Columns**: Populate medan baharu (locale, notify\_\*, staff_number, guest_submissions_linked)
- **Link Historical Submissions**: Update helpdesk_tickets dan loan_applications dengan user_id via email matching
- **Optional Account Linking**: Sistem akan memaparkan prompt kepada staff baharu untuk link submissions sedia ada
- **Default Password**: Set default password untuk staff (force password reset on first login)
- **NO LDAP/SSO**: Semua authentication melalui Laravel Breeze sahaja

### 10.2. Email-Based Linking Strategy (Updated)

#### Langkah 1: Migrate Staff dengan Medan Baharu

```sql
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

#### Langkah 2: Link Historical Tickets

```sql
UPDATE helpdesk_tickets ht
INNER JOIN users u ON LOWER(ht.submitter_email) = LOWER(u.email)
SET ht.user_id = u.id
WHERE ht.user_id IS NULL AND u.role = 'staff';

-- Update guest_submissions_linked count
UPDATE users u
SET guest_submissions_linked = (
    SELECT COUNT(*) FROM helpdesk_tickets WHERE user_id = u.id
) + (
    SELECT COUNT(*) FROM loan_applications WHERE user_id = u.id
)
WHERE role = 'staff';
```

#### Langkah 3: Link Historical Loan Applications

```sql
UPDATE loan_applications la
INNER JOIN users u ON LOWER(la.applicant_email) = LOWER(u.email)
SET la.user_id = u.id
WHERE la.user_id IS NULL AND u.role = 'staff';
```

### 10.3. Dual Audit System Migration

Migrasi mesti menyediakan kedua-dua jadual audit:

```sql
-- Verify audits table exists (owen-it/laravel-auditing)
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

-- Verify activity_log table exists (spatie/laravel-activitylog)
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

### 10.4. Validasi Post-Migration (Enhanced)

```sql
-- Verify Staff migration count
SELECT COUNT(*) as staff_count FROM users WHERE role = 'staff';

-- Verify email domain compliance
SELECT COUNT(*) as invalid_emails
FROM users
WHERE role = 'staff' AND email NOT LIKE '%@motac.gov.my';

-- Verify email_verified_at is set for migrated staff
SELECT COUNT(*) as unverified_staff
FROM users
WHERE role = 'staff' AND email_verified_at IS NULL;

-- Verify linked submissions count accuracy
SELECT
    u.id,
    u.name,
    u.guest_submissions_linked,
    (SELECT COUNT(*) FROM helpdesk_tickets WHERE user_id = u.id) +
    (SELECT COUNT(*) FROM loan_applications WHERE user_id = u.id) as actual_count
FROM users u
WHERE role = 'staff'
HAVING guest_submissions_linked != actual_count;

-- Verify dual audit tables exist
SELECT 'audits' as table_name, COUNT(*) as row_count FROM audits
UNION ALL
SELECT 'activity_log', COUNT(*) FROM activity_log;
```

### 10.5. Migration Scripts (Laravel Commands)

Update migration commands untuk v3.5.0:

- **ImportStaffUsersCommand** - Migrate legacy staff ke users table dengan medan baharu
- **ImportHelpdeskTicketsCommand** - Import tiket helpdesk lama
- **ImportLoanApplicationsCommand** - Import permohonan pinjaman lama
- **ImportAssetsCommand** - Import inventori aset
- **LinkHistoricalSubmissionsCommand** - Link submissions ke user_id via email matching
- **UpdateGuestSubmissionsCountCommand** - Kemaskini guest_submissions_linked count
- **SetupDualAuditTablesCommand** - Verify/create audit tables
- **SetupLaravelPulseCommand** - Configure Laravel Pulse monitoring tables
- **SetupSanctumTokensCommand** - Initialize API token infrastructure
- **ImportMOTACBrandingAssetsCommand** - Verify/import MOTAC branding assets

### 10.6. Google Workspace SSO Migration (Optional)

Jika organisasi memilih untuk enable Google Workspace SSO:

```sql
-- Add Google SSO columns to users table
ALTER TABLE users
ADD COLUMN google_id VARCHAR(255) NULL AFTER remember_token,
ADD COLUMN google_avatar VARCHAR(500) NULL AFTER google_id,
ADD COLUMN auth_provider ENUM('local', 'google') DEFAULT 'local' AFTER google_avatar,
ADD INDEX idx_google_id (google_id);

-- Update existing users to local auth provider
UPDATE users SET auth_provider = 'local' WHERE auth_provider IS NULL;
```

**Nota**: Google SSO adalah optional dan boleh diaktifkan kemudian tanpa menjejaskan migrasi utama.

### 10.7. Laravel Pulse Tables Migration

```sql
-- Create pulse_values table for metrics storage
CREATE TABLE IF NOT EXISTS pulse_values (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    timestamp INT UNSIGNED NOT NULL,
    type VARCHAR(255) NOT NULL,
    key VARCHAR(255) NOT NULL,
    key_hash BINARY(16) GENERATED ALWAYS AS (UNHEX(MD5(`key`))) STORED NOT NULL,
    value BIGINT NOT NULL,
    INDEX idx_pulse_values_timestamp (timestamp),
    INDEX idx_pulse_values_type_key (type, key_hash),
    UNIQUE INDEX idx_pulse_values_unique (type, key_hash, timestamp)
);

-- Create pulse_entries table for detailed entries
CREATE TABLE IF NOT EXISTS pulse_entries (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    timestamp INT UNSIGNED NOT NULL,
    type VARCHAR(255) NOT NULL,
    key VARCHAR(255) NOT NULL,
    key_hash BINARY(16) GENERATED ALWAYS AS (UNHEX(MD5(`key`))) STORED NOT NULL,
    value BIGINT NULL,
    INDEX idx_pulse_entries_timestamp (timestamp),
    INDEX idx_pulse_entries_type_key (type, key_hash)
);

-- Create pulse_aggregates table for aggregated metrics
CREATE TABLE IF NOT EXISTS pulse_aggregates (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    bucket INT UNSIGNED NOT NULL,
    period INT UNSIGNED NOT NULL,
    type VARCHAR(255) NOT NULL,
    key VARCHAR(255) NOT NULL,
    key_hash BINARY(16) GENERATED ALWAYS AS (UNHEX(MD5(`key`))) STORED NOT NULL,
    aggregate VARCHAR(255) NOT NULL,
    value DECIMAL(20, 2) NOT NULL,
    count INT UNSIGNED NULL,
    INDEX idx_pulse_aggregates_bucket (bucket),
    INDEX idx_pulse_aggregates_period_type (period, type, aggregate, bucket),
    UNIQUE INDEX idx_pulse_aggregates_unique (bucket, period, type, aggregate, key_hash)
);
```

### 10.8. Laravel Sanctum API Tokens Migration

```sql
-- Create personal_access_tokens table for API authentication
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

### 10.9. Responsible Officer & Accessory Tracking Migration

```sql
-- Add Responsible Officer columns to loan_applications
ALTER TABLE loan_applications
ADD COLUMN is_applicant_responsible BOOLEAN DEFAULT TRUE AFTER acknowledgement,
ADD COLUMN responsible_officer_name VARCHAR(255) NULL AFTER is_applicant_responsible,
ADD COLUMN responsible_officer_grade VARCHAR(50) NULL AFTER responsible_officer_name,
ADD COLUMN responsible_officer_phone VARCHAR(50) NULL AFTER responsible_officer_grade,
ADD COLUMN responsible_officer_acknowledgement BOOLEAN DEFAULT FALSE AFTER responsible_officer_phone;

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
```

### 10.10. Form Reference Codes Migration

```sql
-- Add form reference codes to helpdesk_tickets
ALTER TABLE helpdesk_tickets
ADD COLUMN form_reference_code VARCHAR(50) DEFAULT 'PK.(S).MOTAC.07.(L1)' AFTER declaration;

-- Add form reference codes to loan_applications
ALTER TABLE loan_applications
ADD COLUMN form_reference_code VARCHAR(50) DEFAULT 'PK.(S).MOTAC.07.(L3)' AFTER acknowledgement;

-- Update existing records with default form codes
UPDATE helpdesk_tickets SET form_reference_code = 'PK.(S).MOTAC.07.(L1)' WHERE form_reference_code IS NULL;
UPDATE loan_applications SET form_reference_code = 'PK.(S).MOTAC.07.(L3)' WHERE form_reference_code IS NULL;
```

### 10.11. MOTAC Branding Assets Verification

Verify all required branding assets exist in `public/images/`:

| Asset File                       | Purpose                  | Required Size | Format |
| -------------------------------- | ------------------------ | ------------- | ------ |
| `jata-negara.svg`                | Malaysian Coat of Arms   | Vector        | SVG    |
| `motac-logo.png`                 | MOTAC logo               | 120x120       | PNG    |
| `motac-logo-32.png`              | Notification icon        | 32x32         | PNG    |
| `motac-logo-64.png`              | Medium icon              | 64x64         | PNG    |
| `bpm-logo.png`                   | BPM division logo        | Variable      | PNG    |
| `favicon.ico`                    | Browser favicon          | Multi-size    | ICO    |
| `web-app-manifest-192x192.png`   | PWA icon (small)         | 192x192       | PNG    |
| `web-app-manifest-512x512.png`   | PWA icon (large)         | 512x512       | PNG    |

**Verification Script:**

```bash
#!/bin/bash
# verify-branding-assets.sh

ASSETS_DIR="public/images"
REQUIRED_FILES=(
    "jata-negara.svg"
    "motac-logo.png"
    "motac-logo-32.png"
    "motac-logo-64.png"
    "bpm-logo.png"
    "favicon.ico"
    "web-app-manifest-192x192.png"
    "web-app-manifest-512x512.png"
)

echo "Verifying MOTAC branding assets..."
for file in "${REQUIRED_FILES[@]}"; do
    if [ -f "$ASSETS_DIR/$file" ]; then
        echo "✓ $file exists"
    else
        echo "✗ $file MISSING"
    fi
done
```

### 10.12. User Preferences Migration

```sql
-- Add user preference columns for enhanced UX features
ALTER TABLE users
ADD COLUMN onboarding_completed BOOLEAN DEFAULT FALSE AFTER guest_submissions_linked,
ADD COLUMN dashboard_layout JSON NULL AFTER onboarding_completed,
ADD COLUMN saved_filters JSON NULL AFTER dashboard_layout,
ADD COLUMN theme_preference ENUM('light', 'dark', 'system') DEFAULT 'system' AFTER saved_filters;

-- Set defaults for existing users
UPDATE users SET
    onboarding_completed = TRUE,
    theme_preference = 'system'
WHERE onboarding_completed IS NULL;
```

---

## 11. PENUTUP

Pelan migrasi ini memastikan data lama dipindahkan ke sistem Helpdesk & ICT Asset Loan MOTAC BPM secara selamat, berkualiti, dan patuh piawaian antarabangsa (ISO 8000, ISO/IEC 27701). Semua proses didokumen, diaudit, dan boleh disemak oleh pihak pengurusan BPM.

**Nota Penting**: Migrasi ini diselaraskan dengan True Hybrid Architecture v3.5.0 sistem baharu:

**Core Authentication & Access:**

- Staff dimigrasikan ke users table dengan self-registration capability (@motac.gov.my)
- Flexible login dengan e-mel penuh ATAU nama pengguna pendek
- Optional guest-to-account linking (pengguna memilih)
- Google Workspace SSO (optional, boleh diaktifkan kemudian)

**Audit & Monitoring:**

- Dual audit system (owen-it + spatie) untuk compliance dan operations
- Laravel Telescope untuk debugging (superuser sahaja)
- Laravel Pulse untuk performance monitoring (admin/superuser)

**API & Integration:**

- Laravel Sanctum untuk API token authentication
- RESTful API endpoints (/api/v1/) untuk integrasi masa hadapan

**Enhanced Features:**

- Responsible Officer tracking untuk loan applications
- Accessory tracking untuk asset check-out/check-in
- Form reference codes (PK.(S).MOTAC.07.(L1), PK.(S).MOTAC.07.(L3))
- User preferences (dashboard layout, saved filters, theme)
- Onboarding tour completion tracking

**Branding & Compliance:**

- MOTAC branding assets (Jata Negara, logos, PWA icons)
- WCAG 2.2 AA accessibility compliance
- Bahasa Melayu sahaja (v3.6.0)

**Authentication Note**: Semua authentication melalui Laravel Breeze (local database) dengan optional Google Workspace SSO. Tiada integrasi LDAP penuh dalam v3.5.0.

Rujuk **[D09_DATABASE_DOCUMENTATION.md]** untuk struktur database lengkap dan **[D06_DATA_MIGRATION_SPECIFICATION.md]** untuk spesifikasi teknikal terperinci.

---
