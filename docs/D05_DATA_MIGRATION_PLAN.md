# Pelan Migrasi Data (Data Migration Plan - DMP)

**Sistem ICTServe**
**Versi:** 2.0.0 (SemVer)
**Tarikh Kemaskini:** 17 Oktober 2025
**Status:** Aktif
**Klasifikasi:** Terhad - Dalaman MOTAC
**Penulis:** Pasukan Pembangunan BPM MOTAC
**Standard Rujukan:** ISO 8000 (Data Quality), ISO/IEC 27701 (Privacy Information Management)

---

## Maklumat Dokumen (Document Information)

| Atribut                | Nilai                                    |
|------------------------|------------------------------------------|
| **Versi**              | 2.0.0                                    |
| **Tarikh Kemaskini**   | 17 Oktober 2025                          |
| **Status**             | Aktif                                    |
| **Klasifikasi**        | Terhad - Dalaman MOTAC                   |
| **Pematuhi**           | ISO 8000, ISO/IEC 27701                  |
| **Bahasa**             | Bahasa Melayu (utama), English (teknikal)|

> Notis Penggunaan Dalaman: Migrasi data ini melibatkan data dalaman MOTAC dan tidak berkaitan data awam.

---

## Sejarah Perubahan (Changelog)

| Versi  | Tarikh          | Perubahan                                      | Penulis       |
|--------|-----------------|------------------------------------------------|---------------|
| 1.0.0  | September 2025  | Versi awal pelan migrasi data                  | Pasukan BPM   |
| 2.0.0  | 17 Oktober 2025 | Penyeragaman mengikut D00-D14, SemVer, cross-reference | Pasukan BPM   |

---

## Rujukan Dokumen Berkaitan (Related Document References)

- **[D00_SYSTEM_OVERVIEW.md]** - Ringkasan Sistem
- **[D06_DATA_MIGRATION_SPECIFICATION.md]** - Spesifikasi Migrasi Data (detail teknikal)
- **[D09_DATABASE_DOCUMENTATION.md]** - Dokumentasi Pangkalan Data (target schema)
- **[GLOSSARY.md]** - Glosari Istilah Sistem


---

## 1. TUJUAN DOKUMEN (Purpose)

Dokumen ini menerangkan perancangan menyeluruh bagi migrasi data ke sistem **Helpdesk & ICT Asset Loan** yang berasaskan Laravel 12 untuk Bahagian Pengurusan Maklumat (BPM), MOTAC. Pelan ini mematuhi piawaian **ISO 8000** untuk kualiti data (data quality) dan **ISO/IEC 27701** untuk pengurusan privasi maklumat (privacy information management).

---

## 2. SKOP MIGRASI (Scope)

- Migrasi semua data berkaitan aduan ICT, inventori aset, sejarah pinjaman, dan maklumat pengguna dari sistem lama (manual, Excel, Access, atau sistem digital terdahulu) ke sistem baru Laravel.
- Data yang terlibat:
  - Tiket Aduan Kerosakan ICT
  - Data Pinjaman Aset ICT
  - Inventori Aset ICT
  - Profil pengguna (staf MOTAC)
- Termasuk metadata (timestamp, status, logs) & audit trail.


---

## 3. SUMBER DATA (Data Sources)

- **Manual Records**: Borang kertas, fail PDF, dokumen cetak.
- **Digital Files**: Microsoft Excel, CSV, Access DB, sistem aduan lama.
- **Sistem Sedia Ada**: Database, API, atau sistem legacy lain.


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

- **Inventori Data**: Kenalpasti semua sumber data, struktur, dan owner.
- **Data Mapping**: Padankan field sumber ke field dalam sistem Laravel (contoh: `user_fullname` → `users.name`, `asset_id_legacy` → `assets.tag_id`).
- **Data Dictionary**: Sediakan kamus data untuk semua field.


### 5.2. Data Cleansing & Standardization

- **Deduplication**: Buang rekod berganda.
- **Validation**: Pastikan format, completeness, dan konsistensi (contoh: tarikh dalam `YYYY-MM-DD`, email valid).
- **Standardization**: Tukar kod/kategori lama ke kod baru sistem Laravel (mapping kategori kerosakan, status pinjaman, dsb).


### 5.3. Data Migration Tools & Scripts

- Gunakan skrip migrasi Laravel (php artisan db:seed, custom import scripts).
- Import CSV/Excel guna Laravel Excel package atau Eloquent batch insert.
- Logging setiap proses import untuk audit dan troubleshooting.


### 5.4. Data Migration Execution

- **Dry Run**: Ujian migrasi di staging/dev, semak hasil.
- **Validation**: Cross-check jumlah rekod, field penting, dan random sampling.
- **Go-Live Migration**: Laksanakan migrasi pada waktu off-peak, pastikan backup tersedia.
- **Post-Migration Review**: Audit data dalam sistem baru, semak error log.


### 5.5. Data Protection & Privacy

- **Encryption**: Data at-rest dan in-transit.
- **Access Control**: Data migrasi hanya boleh diakses oleh pasukan yang dibenarkan.
- **Data Retention**: Hapus data peribadi dari sistem lama mengikut polisi retention MOTAC selepas migrasi berjaya.


---

## 6. JADUAL MIGRASI (Migration Schedule)

| Fasa               | Tempoh         | Aktiviti                          |
|--------------------|---------------|-----------------------------------|
| Penilaian & Mapping| 1 minggu      | Data inventory, mapping, dictionary|
| Cleansing/Standard | 1 minggu      | Deduplication, validation         |
| Skrip & Ujian      | 1 minggu      | Scripting, dry run, validation    |
| Migrasi Sebenar    | 1-2 hari      | Go-live migration, backup         |
| Audit & Review     | 3 hari        | Post-migration review, reporting  |

---

## 7. RISIKO & MITIGASI (Risks & Mitigation)

| Risiko                        | Langkah Mitigasi                          |
|-------------------------------|-------------------------------------------|
| Data rosak/kehilangan         | Full backup, dry run, rollback script     |
| Data duplikasi/tidak konsisten| Cleansing, validation, mapping yang teliti|
| Kebocoran data peribadi       | Encryption, access control, audit trail   |
| Fail integrasi legacy         | Early testing, manual import jika perlu   |

---

## 8. KAWALAN KUALITI & AUDIT (Quality & Audit Controls)

- **Verification**: Setiap batch migrasi diverifikasi (random sampling & total record).
- **Audit Trail**: Skrip log semua aktiviti migrasi.
- **Reporting**: Laporan status migrasi, error, dan data issue kepada BPM.


---

## 9. PELAN PEMULIHAN BENCANA (Disaster Recovery Plan)

**Pematuhan Standard**: ISO/IEC/IEEE 12207:2017 (Software Lifecycle) §7.5 (Maintenance & Support)

Sistem **Helpdesk & ICT Asset Loan MOTAC BPM** mesti memiliki pelan pemulihan bencana (Disaster Recovery Plan) yang komprehensif untuk memastikan kontinuitas bisnis dan perlindungan data dalam situasi darurat.

### 9.1. Tujuan & Scope

- **Tujuan**: Memastikan sistem dapat dipulihkan dengan cepat & data dapat di-restore dengan aman dalam event bencana
- **Scope**: Seluruh sistem termasuk aplikasi, database, backup storage, dan infrastructure


### 9.2. Sasaran Pemulihan (Recovery Targets)

| Target | Nilai | Justifikasi |
|--------|-------|------------|
| **RTO (Recovery Time Objective)** | 4 jam | Sistem mesti online dalam 4 jam selepas bencana terdeteksi |
| **RPO (Recovery Point Objective)** | 1 jam | Data loss tidak boleh lebih dari 1 jam (automated hourly backup) |
| **MTBF (Mean Time Between Failure)** | >8000 jam (11 bulan) | Target uptime 99.5% → ~3.5 hours/month allowable downtime |
| **MTTR (Mean Time To Recover)** | 2 jam | Average recovery time target |

### 9.3. Skenario Bencana & Tindakan (Disaster Scenarios & Response)

| Skenario | Jenis | Tindakan Respons | Waktu Estimasi |
|----------|------|------------------|----------------|
| **Database Corruption** | Data | Run DB integrity check; restore from last clean backup | 30-60 min |
| **Server Disk Full** | Infrastructure | Extend disk space; purge old logs | 15-30 min |
| **Network Outage** | Infrastructure | Reroute via backup network; alert admin | 10-20 min |
| **Cybersecurity Incident (Data Breach)** | Security | Isolate system; forensics; patch vulnerability; restore from backup | 2-4 hours |
| **Complete Data Center Failure** | Critical | Activate DR site; restore from encrypted backup (cold storage) | 4 hours |
| **Ransomware Attack** | Security | Immediate isolation; restore from immutable backup | 2-4 hours |

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

## 10. PENUTUP

Pelan migrasi ini memastikan data lama dipindahkan ke sistem Helpdesk & ICT Asset Loan MOTAC BPM secara selamat, berkualiti, dan patuh piawaian antarabangsa (ISO 8000, ISO/IEC 27701). Semua proses didokumen, diaudit, dan boleh disemak oleh pihak pengurusan BPM.

---
