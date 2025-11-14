# Pengurusan Dokumentasi ICTServe (iServe)

## ICTServe Documentation Management

**Versi Dokumentasi (Documentation Version):** 4.0.0 (SemVer)
**Tarikh Kemaskini (Last Updated):** 6 November 2025
**Status:** Aktif - Selesai Fasa 2 Reorganisasi (Active - Phase 2 Reorganization Complete)
**Standard Rujukan (Reference Standards):** ISO/IEC/IEEE 12207, ISO/IEC/IEEE 29148, ISO/IEC/IEEE 15288, IEEE 1016, ISO 9241-210, ISO 8000, WCAG 2.2 Level AA

---

## Maklumat Dokumen (Document Information)

| Atribut (Attribute)              | Nilai (Value)                                                      |
| -------------------------------- | ------------------------------------------------------------------ |
| **Versi (Version)**              | 3.0.0                                                              |
| **Tarikh Kemaskini (Updated)**   | 17 Oktober 2025                                                    |
| **Status**                       | Aktif - Diselaraskan penuh dengan D00-D14 (Active - Fully Aligned)|
| **Klasifikasi (Classification)** | Dokumentasi Rujukan Teknikal Dalaman (Internal Technical Reference)|
| **Pematuhi (Compliance)**        | ISO/IEC/IEEE 12207, 29148, 15288, 1016, ISO 8000, 9241-210, WCAG 2.2 Level AA |
| **Bahasa (Language)**            | Bahasa Melayu (Utama) dengan Istilah Inggeris (Primary: Malay with English Technical Terms) |
| **Penulis (Author)**             | Bahagian Pengurusan Maklumat (BPM), MOTAC                          |
| **Pemilik (Owner)**              | BPM MOTAC                                                          |

---

## Sejarah Perubahan (Changelog)

| Versi  | Tarikh          | Perubahan                                      | Penulis       |
|--------|-----------------|------------------------------------------------|---------------|
| 4.1.0  | 6 November 2025 | D13 v2.2.0: Framework consolidation - Blade+Livewire v3+Volt+Tailwind+Alpine+Filament sebagai primary stack; Bootstrap/SB Admin deprecated; Section 5.7 (Livewire v3 & Volt patterns) added; 254 lines content | Claudette AI   |
| 4.0.0  | 6 November 2025 | Fasa 2 selesai: 76 fail diorganisir ke guides/, technical/, testing/, archive/; INDEX.md & README.md dikemaskini | Pasukan BPM   |
| 3.0.0  | 17 Oktober 2025 | Penyeragaman penuh D00-D14, cross-reference lengkap, tambah rujukan piawaian | Pasukan BPM   |
| 2.0.0  | 6 Oktober 2025  | Penambahan struktur berlapis dan SemVer        | Pasukan BPM   |
| 1.0.0  | September 2025  | Versi awal struktur dokumentasi                | Pasukan BPM   |

---

## Tujuan Dokumen (Purpose)

Dokumen ini berfungsi sebagai **Indeks Utama (Master Index)** dan **Panduan Navigasi (Navigation Guide)** kepada semua dokumentasi sistem **Helpdesk & ICT Asset Loan** BPM MOTAC. Ia mengikuti prinsip **Single Source of Truth (SSoT)** dan memastikan semua dokumentasi mematuhi struktur dan piawaian D00-D14.

---

## Prinsip Dokumentasi (Documentation Principles)

Dokumentasi ICTServe mematuhi piawaian antarabangsa dan mengikut prinsip berikut:

### 1. Single Source of Truth (SSoT)

Setiap maklumat utama hanya disimpan di satu lokasi autoritatif untuk mengelakkan konflik dan ketidakkonsistenan.

### 2. Struktur Berlapis (Layered Structure)

- **Lapisan 1: Dokumen Standard D00-D14** - Dokumentasi formal mengikut piawaian ISO/IEC/IEEE (rujuk Google Drive)
- **Lapisan 2: Dokumen Induk** (`ICTServe_System_Documentation.md`) - Gambaran keseluruhan sistem
- **Lapisan 3: Dokumen Terspesialisasi** (folder `versions/`) - Dokumentasi mendalam modul tertentu
- **Lapisan 4: Glosari & Rujukan** (`GLOSSARY.md`) - Definisi istilah dan cross-reference


### 3. Pematuhan Standard (Standards Compliance)

Semua dokumentasi merujuk dan mematuhi dokumen standard D00-D14:

- **D00**: System Overview
- **D01**: System Development Plan (SDP)
- **D02**: Business Requirements Specification (BRS)
- **D03**: Software Requirements Specification (SRS)
- **D04**: Software Design Document (SDD)
- **D05**: Data Migration Plan
- **D06**: Data Migration Specification
- **D07**: System Integration Plan
- **D08**: System Integration Specification
- **D09**: Database Documentation
- **D10**: Source Code Documentation
- **D11**: Technical Design Documentation
- **D12**: UI/UX Design Guide
- **D13**: UI/UX Frontend Framework
- **D14**: UI/UX Style Guide


### 4. Versioning Semantik (Semantic Versioning - SemVer)

Semua dokumen menggunakan format versi **MAJOR.MINOR.PATCH**:

- **MAJOR**: Perubahan besar struktur atau kandungan
- **MINOR**: Penambahan kandungan atau penambahbaikan
- **PATCH**: Pembetulan kecil atau kemaskini


---

## Struktur Folder (Folder Structure)

```text

_reference/
├── README.md                                    (Dokumen ini / This document)
├── ICTServe_System_Documentation.md             (Dokumen Induk / Master Document)
├── GLOSSARY.md                                  (Glosari Terpusat / Centralized Glossary)
├── DOCUMENTATION_STANDARDIZATION_REPORT.md      (Laporan Pematuhan / Compliance Report)
├── COMPLETION_REPORT.md                         (Laporan Penyiapan / Completion Report)
├── DOCUMENTATION_UPDATE_SUMMARY_D00-D14.md      (Ringkasan Kemaskini / Update Summary)
├── FUTURE_IMPLEMENTATION_AI_CHATBOT_USING_OLLAMA.md (Perancangan Masa Depan / Future Planning)
├── versions/                                    (Dokumentasi Terperinci / Detailed Documentation)
│   ├── v2.1.0_Dokumentasi_Flow_Sistem_Helpdesk_ServiceDesk_ICTServe(iServe).md
│   ├── v2.1.0_Dokumentasi_Flow_Sistem_Permohonan_Pinjaman_Aset_ICT_ICTServe(iServe).md
│   ├── v2.1.0_Dokumentasi_Reka_Bentuk_Sistem_ICTServe(iServe).md
│   ├── v2.1.0_Dokumentasi_Reka_Bentuk_ICTServe(iServe).md
│   ├── v2.1.0_Dokumentasi_Jadual_Data_Pengguna_Organisasi_Teras_ICTServe(iServe).md
│   └── v2.1.0_Dokumentasi_Sistem_Notifikasi_E-mel_ICTServe(iServe).md
├── KRISA_DOCS/                                  (Dokumentasi KRISA)
└── original ver/                                (Versi Asal / Original Versions)
```

---

## Pemetaan Dokumentasi ke Standard D00-D14 (Documentation Mapping to D00-D14 Standards)

### Rujukan Standard Utama (Primary Standard References)

| Dokumen Dalaman (Internal Document) | Standard D00-D14 | Tujuan (Purpose) | Status |
|-------------------------------------|------------------|------------------|--------|
| `ICTServe_System_Documentation.md` | D00, D01, D11 | Gambaran keseluruhan sistem (System Overview) | ✅ Aktif |
| `v2.1.0_Dokumentasi_Reka_Bentuk_Sistem_ICTServe(iServe).md` | D04, D11 | Rekabentuk sistem & senibina (System Design & Architecture) | ✅ Aktif |
| `v2.1.0_Dokumentasi_Flow_Sistem_Helpdesk_ServiceDesk_ICTServe(iServe).md` | D02, D03, D04 | Workflow Helpdesk & keperluan (Helpdesk Workflow & Requirements) | ✅ Aktif |
| `v2.1.0_Dokumentasi_Flow_Sistem_Permohonan_Pinjaman_Aset_ICT_ICTServe(iServe).md` | D02, D03, D04 | Workflow Pinjaman Aset (Asset Loan Workflow) | ✅ Aktif |
| `v2.1.0_Dokumentasi_Jadual_Data_Pengguna_Organisasi_Teras_ICTServe(iServe).md` | D06, D09 | Struktur data & migrasi (Data Structure & Migration) | ✅ Aktif |
| `v2.1.0_Dokumentasi_Reka_Bentuk_ICTServe(iServe).md` | D12, D13, D14 | UI/UX, Frontend Framework & Style Guide | ✅ Aktif |
| `v2.1.0_Dokumentasi_Sistem_Notifikasi_E-mel_ICTServe(iServe).md` | D07, D08 | Integrasi sistem notifikasi (Notification System Integration) | ✅ Aktif |
| `GLOSSARY.md` | Semua (All) | Glosari istilah untuk semua dokumen (Terminology Glossary) | ✅ Aktif |
| `DOCUMENTATION_STANDARDIZATION_REPORT.md` | D00-D14 | Laporan pematuhan standard (Standards Compliance Report) | ✅ Aktif |

---

## Dokumen Utama (Main Documents)

### 1. Dokumen Induk (Master Document)

**File:** `ICTServe_System_Documentation.md`
**Rujukan Standard:** D00 (System Overview), D01 (System Development Plan), D11 (Technical Design)
**Versi:** 2.1.0

**Tujuan:**

- Ringkasan eksekutif sistem ICTServe (Executive Summary)
- Gambaran keseluruhan senibina dan objektif sistem (Architecture Overview & Objectives)
- Indeks terancang dengan pautan kepada semua dokumentasi terperinci (Structured Index with Links)
- Rujukan pantas untuk mencari maklumat spesifik (Quick Reference Guide)


**Cara Guna:** Mulakan di sini untuk memahami sistem secara keseluruhan, kemudian ikuti pautan ke dokumen terspesialisasi untuk butiran teknikal.

### Dokumentasi Terspesialisasi (Versi 2.1.0)

Semua dokumen berikut berada dalam folder `versions/` dan merangkumi aspek-aspek tertentu sistem dengan mendalam:

#### Aliran Sistem (Flow Documentation)

- **Helpdesk & Service Desk:** `v2.1.0_Dokumentasi_Flow_Sistem_Helpdesk_ServiceDesk_ICTServe(iServe).md`
  - Aliran proses tiket helpdesk
  - Status dan peralihan tiket
  - Matriks SLA dan KPI


- **Pinjaman Aset ICT:** `v2.1.0_Dokumentasi_Flow_Sistem_Permohonan_Pinjaman_Aset_ICT_ICTServe(iServe).md`
  - Aliran kerja permohonan hingga pemulangan
  - Matriks kelulusan berdasarkan gred
  - Peraturan perniagaan pinjaman


#### Reka Bentuk Sistem (Design Documentation)

- **Reka Bentuk Sistem:** `v2.1.0_Dokumentasi_Reka_Bentuk_Sistem_ICTServe(iServe).md`
  - Seni bina sistem tahap tinggi
  - Komponen teras dan integrasinya
  - Keputusan reka bentuk dan rasional


- **Reka Bentuk UI/UX:** `v2.1.0_Dokumentasi_Reka_Bentuk_ICTServe(iServe).md`
  - Prinsip reka bentuk antara muka
  - Sistem grid responsif
  - Pematuhan aksesibiliti (WCAG 2.1 AA)


#### Data dan Integrasi

- **Skema Pangkalan Data:** `v2.1.0_Dokumentasi_Jadual_Data_Pengguna_Organisasi_Teras_ICTServe(iServe).md`
  - Struktur jadual pangkalan data
  - Hubungan entiti dan foreign keys
  - Strategi indeks dan prestasi


- **Sistem Notifikasi E-mel:** `v2.1.0_Dokumentasi_Sistem_Notifikasi_E-mel_ICTServe(iServe).md`
  - Konfigurasi sistem e-mel
  - Templat notifikasi
  - Pengurusan queue dan pengendalian ralat


### Sumber Rujukan

- **Glosari:** `GLOSSARY.md` - Definisi istilah teknikal dan akronim
- **Panduan Sumbangan:** `../CONTRIBUTING.md` - Garis panduan untuk menyumbang kepada dokumentasi


## Versi Dokumentasi

| Versi  | Tarikh Kemaskini | Status    | Nota                                          |
| ------ | ---------------- | --------- | --------------------------------------------- |
| v3.0.0 | 6 Oktober 2025   | **Aktif** | Versi terkini dengan struktur master document |
| v2.1.0 | 5 Oktober 2025   | Arkib     | Dokumentasi terperinci modular                |
| v2.0   | -                | Arkib     | Versi awal dokumentasi terspesialisasi        |
| v1.0   | -                | Arkib     | Dokumentasi sistem awal                       |

---

## Pematuhan Terhadap D00-D14 Standard (D00-D14 Standards Compliance)

Dokumentasi ICTServe mematuhi piawaian rekabentuk sistem antarabangsa seperti ditakrifkan dalam D00 hingga D14. Semua dokumen D00-D14 kini disediakan dalam folder `_reference` dengan struktur lengkap:

| Dokumen  | Judul                                    | Piawaian            | Lokasi File | Status |
| -------- | ---------------------------------------- | ------------------- | ----------- | ------ |
| **D00**  | System Overview                          | ISO/IEC/IEEE 12207, 15288 | `D00_SYSTEM_OVERVIEW.md` | ✅ Updated v2.0.0 |
| **D01**  | System Development Plan (SDP)            | ISO/IEC/IEEE 12207  | `D01_SYSTEM_DEVELOPMENT_PLAN.md` | ✅ Updated v2.0.0 |
| **D02**  | Business Requirements Specification      | ISO/IEC/IEEE 29148  | `D02_BUSINESS_REQUIREMENTS_SPECIFICATION.md` | ✅ Updated v2.0.0 |
| **D03**  | Software Requirements Specification      | ISO/IEC/IEEE 29148  | `D03_SOFTWARE_REQUIREMENTS_SPECIFICATION.md` | ✅ Exists v1.0.0 |
| **D04**  | Software Design Document                 | IEEE 1016           | `D04_SOFTWARE_DESIGN_DOCUMENT.md` | ✅ Exists v1.0.0 |
| **D05**  | Data Migration Plan                      | ISO 8000, ISO/IEC 27701 | `D05_DATA_MIGRATION_PLAN.md` | ✅ Exists v1.0.0 |
| **D06**  | Data Migration Specification             | ISO 8000, ISO/IEC 38505-1 | `D06_DATA_MIGRATION_SPECIFICATION.md` | ✅ Exists v1.0.0 |
| **D07**  | System Integration Plan                  | ISO/IEC/IEEE 15288, 12207 | `D07_SYSTEM_INTEGRATION_PLAN.md` | ✅ Exists v1.0.0 |
| **D08**  | System Integration Specification         | ISO/IEC/IEEE 15288, 15289 | `D08_SYSTEM_INTEGRATION_SPECIFICATION.md` | ✅ Exists v1.0.0 |
| **D09**  | Database Documentation                   | ISO 8000, IEEE 1016 | `D09_DATABASE_DOCUMENTATION.md` | ✅ Exists v1.0.0 |
| **D10**  | Source Code Documentation                | ISO/IEC/IEEE 5055, 25000 | `D10_SOURCE_CODE_DOCUMENTATION.md` | ✅ Exists v1.0.0 |
| **D11**  | Technical Design Documentation           | IEEE 1016, ISO 9001 | `D11_TECHNICAL_DESIGN_DOCUMENTATION.md` | ✅ Exists v1.0.0 |
| **D12**  | UI/UX Design Guide                       | ISO 9241-210, 110, 11, WCAG 2.2 AA | `D12_UI_UX_DESIGN_GUIDE.md` | ✅ Exists v1.0.0 |
| **D13**  | UI/UX Frontend Framework                 | ISO 9241-210, 110, 11, WCAG 2.2 AA | `D13_UI_UX_FRONTEND_FRAMEWORK.md` | ✅ Exists v1.0.0 |
| **D14**  | UI/UX Style Guide                        | ISO 9001, 9241-210, 110, 11, WCAG 2.2 AA | `D14_UI_UX_STYLE_GUIDE.md` | ✅ Exists v1.0.0 |

### Pemetaan Dokumen Versi ke D00-D14 (Version Documents Mapping)

| Dokumen Versi (Version Document) | Rujukan Standard D00-D14 | Tujuan |
|----------------------------------|--------------------------|--------|
| `v2.1.0_Dokumentasi_Reka_Bentuk_Sistem_ICTServe(iServe).md` | D04, D11 | Implementasi software design |
| `v2.1.0_Dokumentasi_Flow_Sistem_Helpdesk_ServiceDesk_ICTServe(iServe).md` | D02, D03 | Business & software requirements untuk Helpdesk |
| `v2.1.0_Dokumentasi_Flow_Sistem_Permohonan_Pinjaman_Aset_ICT_ICTServe(iServe).md` | D02, D03 | Business & software requirements untuk Asset Loan |
| `v2.1.0_Dokumentasi_Jadual_Data_Pengguna_Organisasi_Teras_ICTServe(iServe).md` | D09 | Database implementation |
| `v2.1.0_Dokumentasi_Reka_Bentuk_ICTServe(iServe).md` | D12, D13, D14 | UI/UX implementation |
| `v2.1.0_Dokumentasi_Sistem_Notifikasi_E-mel_ICTServe(iServe).md` | D07, D08 | System integration implementation |

---

## Panduan Penggunaan

### Untuk Pembangun Baru

1. Mulakan dengan `ICTServe_System_Documentation.md` untuk memahami gambaran keseluruhan
2. Rujuk `GLOSSARY.md` untuk memahami istilah teknikal
3. Akses dokumentasi terspesialisasi mengikut komponen yang anda bangunkan


### Untuk Pentadbir Sistem

1. Rujuk dokumentasi aliran sistem untuk memahami proses perniagaan
2. Gunakan dokumentasi reka bentuk sistem untuk konfigurasi dan deployment
3. Rujuk dokumentasi skema pangkalan data untuk pengurusan data


### Untuk Pihak Pengurusan

1. Baca ringkasan eksekutif dalam dokumen induk
2. Fokus pada objektif sistem dan metrik kejayaan
3. Rujuk dokumentasi aliran untuk memahami proses operasi


## Prinsip Penyelenggaraan (Maintenance Principles)

1. **Konsistensi (Consistency):** Semua dokumentasi mengikut format dan gaya yang sama mengikut standard D00-D14
2. **Ketepatan (Accuracy):** Maklumat dikemas kini seiring dengan perubahan sistem
3. **Keterlacakan (Traceability):** Semua perubahan direkod dalam sejarah revisi (changelog)
4. **Kebolehcapaian (Accessibility):** Pautan silang (cross-reference) memudahkan navigasi antara dokumen
5. **Versioning Semantik (Semantic Versioning):** Gunakan SemVer (MAJOR.MINOR.PATCH) untuk semua dokumen
6. **Bahasa Dwi-modal (Dual-language):** Bahasa Melayu utama dengan istilah teknikal dalam English untuk clarity


---

## Glosari & Terminologi (Glossary & Terminology)

Semua istilah teknikal dan domain perniagaan yang digunakan dalam sistem didokumentasikan di:

**📖 [GLOSSARY.md](GLOSSARY.md)** - Glosari Terpusat untuk Semua Dokumentasi

### Istilah Utama (Key Terms)

- **Helpdesk Ticketing**: Sistem pengurusan aduan ICT
- **Asset Loan**: Peminjaman peralatan ICT
- **SLA**: Service Level Agreement
- **BPM**: Bahagian Pengurusan Maklumat MOTAC
- **MVC**: Model-View-Controller
- **CRUD**: Create, Read, Update, Delete
- **RBAC**: Role-Based Access Control
- **Audit Trail**: Rekod kronologi aktiviti sistem


**Rujukan Penuh:** Lihat [GLOSSARY.md](GLOSSARY.md) untuk 100+ istilah dan definisi lengkap.

---

## Sokongan & Bantuan (Support & Help)

### Untuk Soalan Teknikal

- **Email:** <bpm@motac.gov.my>
- **Dokumentasi Teknikal:** Rujuk D10, D11 untuk butiran implementasi
- **Kod Sumber:** Rujuk `app/`, `resources/`, `database/` dalam repositori


### Untuk Keperluan Perniagaan

- **Dokumentasi Keperluan:** Rujuk D02, D03
- **Workflow & Proses:** Rujuk dokumentasi flow di `versions/`
- **Laporan & Analitik:** Rujuk D11 Seksyen 4.5


### Untuk Penambahbaikan & Feedback

- **Issue Tracker:** Gunakan sistem issue tracking dalaman
- **Change Request:** Ikut proses change management D01
- **Documentation Update:** Rujuk CONTRIBUTING.md untuk panduan


---

## Rujukan Piawaian (Standards References)

Dokumentasi ini mematuhi piawaian antarabangsa berikut:

### Software Engineering Standards

- **ISO/IEC/IEEE 12207:2017** - Software lifecycle processes
- **ISO/IEC/IEEE 15288:2015** - System lifecycle processes
- **ISO/IEC/IEEE 29148:2018** - Requirements engineering
- **IEEE 1016-2009** - Software design descriptions
- **ISO/IEC/IEEE 5055:2021** - Software quality


### Data Quality & Management

- **ISO 8000** - Data quality
- **ISO/IEC 38505-1:2017** - Governance of data
- **ISO/IEC 27701:2019** - Privacy information management


### User Experience & Accessibility

- **ISO 9241-210:2019** - Human-centred design
- **ISO 9241-110:2020** - Dialogue principles
- **ISO 9241-11:2018** - Usability
- **WCAG 2.2 Level AA** - Web Content Accessibility Guidelines


### Quality Management

- **ISO 9001:2015** - Quality management systems


---

## Lampiran (Appendices)

### A. Senarai Semak Dokumentasi (Documentation Checklist)

Setiap dokumen baru atau dikemaskini mesti mempunyai:

- ✅ Header dengan versi SemVer, tarikh, status, klasifikasi
- ✅ Maklumat dokumen (Document Information table)
- ✅ Sejarah perubahan (Changelog)
- ✅ Rujukan dokumen berkaitan (Related Document References)
- ✅ Kandungan utama dalam Bahasa Melayu dengan istilah teknikal English
- ✅ Cross-reference ke dokumen D00-D14 yang relevan
- ✅ Glosari istilah khusus (jika ada)
- ✅ Rujukan piawaian (jika applicable)
- ✅ Footer dengan piawaian compliance statement


### B. Templat Dokumen (Document Template)

Semua dokumen baru mesti mengikut format:

```markdown

# [Tajuk Dokumen] ([English Title])
**[Nama Sistem]**
**Versi:** X.Y.Z (SemVer)
**Tarikh Kemaskini:** DD MMMM YYYY
**Status:** [Aktif/Draf/Arkib]
**Klasifikasi:** [Terhad/Sulit/Awam]
**Penulis:** [Nama Pasukan]
**Standard Rujukan:** [ISO/IEC/IEEE standards]

---

## Maklumat Dokumen (Document Information)
[Table with Atribut and Nilai]

---

## Sejarah Perubahan (Changelog)
[Table with Versi, Tarikh, Perubahan, Penulis]

---

## Rujukan Dokumen Berkaitan (Related Document References)

- **[Dxx_DOCUMENT.md]** - Description


---

[Kandungan Utama / Main Content]

---

**Dokumen ini disediakan mengikut piawaian [list standards] dan akan dikemaskini mengikut keperluan projek.**
```

### C. Panduan Penomboran Versi (Versioning Guide)

**SemVer Format:** MAJOR.MINOR.PATCH

- **MAJOR (X.0.0)**: Perubahan struktur besar, tidak compatible dengan versi sebelum
- **MINOR (X.Y.0)**: Penambahan fungsi baru, backward compatible
- **PATCH (X.Y.Z)**: Pembetulan kecil, typo, update minor


**Contoh:**

- `1.0.0` → Versi awal
- `1.1.0` → Tambah seksyen baru
- `1.1.1` → Betul typo
- `2.0.0` → Restructure lengkap dokumen


---

## Organisasi Direktori (Directory Organization)

### 📁 Struktur Direktori Terkini (Current Directory Structure - Updated Nov 6, 2025)

Dokumentasi telah diorganisir ke dalam kategori berikut untuk kemudahan navigasi dan discoverability:

```text

docs/
├── D00-D15/                           ✅ Dokumen Standard Formal (Formal Standard Documents)
│   ├── D00_SYSTEM_OVERVIEW.md
│   ├── D01_SYSTEM_DEVELOPMENT_PLAN.md
│   ├── ... (16 core D-documents)
│   └── D15_LANGUAGE_MS_EN.md
│
├── guides/                            ✅ Panduan Sistem & Setup (System Guides & Setup)
│   ├── frontend/                      (16 files - accessibility, responsive design, i18n)
│   ├── asset-loan-system-flow.md
│   ├── helpdesk-system-flow.md
│   ├── broadcasting-setup.md
│   ├── HELPDESK_QUICK_REFERENCE.txt
│   └── LOAN_QUICK_REFERENCE.md
│
├── features/                          ✅ Butiran Pengenalan Ciri (Feature Implementation Details)
│   ├── admin-seeding.md
│   ├── component-library-audit-2025-11-05.md
│   ├── helpdesk_form_to_model.md
│   ├── loan_form_to_model.md
│   └── component-upgrade-guide.md
│
├── technical/                         ✅ Pelaksanaan Teknikal (Technical Implementation)
│   ├── frontend/                      (13 files - optimization, components, architecture)
│   ├── implementation/                (Email, dual-approval, hybrid forms)
│   ├── ollama-laravel/                (6 files - AI/LLM integration)
│   ├── EMAIL_NOTIFICATION_SYSTEM.md
│   ├── EMAIL_NOTIFICATION_QUICK_START.md
│   ├── PHPSTAN_ANALYSIS_NOTES.md
│   ├── TASKS_10.1_10.2_CHECKLIST.md
│   ├── devtools-mcp-getting-started.md
│   ├── pdpa-compliance-implementation.md
│   ├── manifest.yml                   (API manifest)
│   └── v1.yml                         (OpenAPI v1 specification)
│
├── testing/                           ✅ Pengujian & Laporan (Testing & Compliance Reports)
│   ├── frontend/                      (13 files - E2E testing, compliance validation)
│   ├── automated-testing-pipeline.md
│   ├── browser-compatibility-testing-guide.md
│   ├── testing-strategy.md
│   ├── compliance-verification-procedures.md
│   ├── comprehensive-test-suite-report.md
│   └── ... (17+ testing & audit documents)
│
├── archive/                           ✅ Dokumentasi Arkib (Historical Documentation)
│   ├── versions/                      (Previous releases)
│   │   ├── 2.1.0/                     (6 files - v2.1.0 system specs)
│   │   │   ├── v2.1.0_Dokumentasi_Flow_Sistem_Helpdesk_ServiceDesk_ICTServe(iServe).md
│   │   │   ├── v2.1.0_Dokumentasi_Flow_Sistem_Permohonan_Pinjaman_Aset_ICT_ICTServe(iServe).md
│   │   │   ├── v2.1.0_Dokumentasi_Reka_Bentuk_Sistem_ICTServe(iServe).md
│   │   │   ├── v2.1.0_Dokumentasi_Reka_Bentuk_ICTServe(iServe).md
│   │   │   ├── v2.1.0_Dokumentasi_Jadual_Data_Pengguna_Organisasi_Teras_ICTServe(iServe).md
│   │   │   └── v2.1.0_Dokumentasi_Sistem_Notifikasi_E-mel_ICTServe(iServe).md
│   │   └── [other versions]
│   └── [historical fixes & temporary files]
│
├── reference/                         ✅ Dokumen Rujukan (Reference Materials)
│   ├── rtm/                           (4 CSV files - Requirements Traceability Matrix)
│   │   ├── coredata_requirements_rtm.csv
│   │   ├── helpdesk_requirements_rtm.csv
│   │   ├── loan_requirements_rtm.csv
│   │   └── requirements-traceability.csv
│   └── [minimal reference materials]
│
├── GLOSSARY.md                        📖 Glosari Istilah (Terminology Glossary)
├── ICTServe_System_Documentation.md   📘 Dokumen Induk (Master Document)
├── INDEX.md                           📑 Indeks Navigasi (Navigation Index) - ✨ UPDATED Nov 6
├── README.md                          📄 Panduan Ini (This Guide)
└── ORGANIZATION_SUMMARY.md            📋 Ringkasan Organisasi (Organization Summary - Phase 1)

    + REORGANIZATION_PHASE_2_COMPLETE.md   📋 Ringkasan Reorganisasi (Phase 2 Summary) - NEW Nov 6
```

**✨ Perubahan Terbaru (Latest Changes - Nov 6, 2025):**

- ✅ 76 dokumen reorganisir dari `reference/` ke lokasi yang lebih sesuai
- ✅ Direktori `guides/`, `technical/`, `testing/`, `archive/` diisi dengan fail-fail yang diorganisir
- ✅ `reference/` sekarang hanya mengandungi RTM (Requirements Traceability Matrix)
- ✅ Semua subdirektori kosong telah dialih keluar (testing, reports, openapi, helpdesk, versions dari reference/)
- ✅ Dokumentasi navigasi (INDEX.md, README.md) dikemaskini untuk mencerminkan struktur baru


### 📍 Dokumen Teras di Akar (Core Documents in Root)

Hanya dokumen berikut sahaja yang disimpan di akar folder `docs/`:

1. **D00_SYSTEM_OVERVIEW.md** → System vision & governance
2. **D01_SYSTEM_DEVELOPMENT_PLAN.md** → Development methodology
3. **D02_BUSINESS_REQUIREMENTS_SPECIFICATION.md** → Business requirements
4. **D03_SOFTWARE_REQUIREMENTS_SPECIFICATION.md** → Functional requirements
5. **D04_SOFTWARE_DESIGN_DOCUMENT.md** → System architecture
6. **D05_DATA_MIGRATION_PLAN.md** → Data migration strategy
7. **D06_DATA_MIGRATION_SPECIFICATION.md** → Technical migration specs
8. **D07_SYSTEM_INTEGRATION_PLAN.md** → Integration strategy
9. **D08_SYSTEM_INTEGRATION_SPECIFICATION.md** → Technical integration specs
10. **D09_DATABASE_DOCUMENTATION.md** → Database schema
11. **D10_SOURCE_CODE_DOCUMENTATION.md** → Code organization
12. **D11_TECHNICAL_DESIGN_DOCUMENTATION.md** → Infrastructure & deployment
13. **D12_UI_UX_DESIGN_GUIDE.md** → UI/UX design standards
14. **D13_UI_UX_FRONTEND_FRAMEWORK.md** → Frontend frameworks
15. **D14_UI_UX_STYLE_GUIDE.md** → Visual design & styling
16. **GLOSSARY.md** → Terminology & definitions
17. **ICTServe_System_Documentation.md** → Master overview document
18. **INDEX.md** → Documentation navigation index (NEW - mulai 1 Nov 2025)
19. **README.md** → Panduan ini


### 🔍 Panduan Pencarian (Finding What You Need)

Dokumentasi sekarang diorganisir mengikut **tujuan dan peranan pengguna**. Gunakan **INDEX.md** untuk navigasi lengkap dengan reading paths.

**Untuk Email Notifications:**

- Cari di: `technical/EMAIL_NOTIFICATION_SYSTEM.md`
- Quick start: `technical/EMAIL_NOTIFICATION_QUICK_START.md`
- Checklist: `technical/TASKS_10.1_10.2_CHECKLIST.md`


**Untuk Helpdesk System:**

- Aliran kerja: `guides/helpdesk-system-flow.md`
- Data model: `features/helpdesk_form_to_model.md`
- RTM & Rujukan: Lihat `INDEX.md` untuk complete reference


**Untuk Asset Loan System:**

- Aliran kerja: `guides/asset-loan-system-flow.md`
- Data model: `features/loan_form_to_model.md`


**Untuk Frontend/UI Development:**

- Piawai: `D12_UI_UX_DESIGN_GUIDE.md`
- Framework: `D13_UI_UX_FRONTEND_FRAMEWORK.md`
- Styling: `D14_UI_UX_STYLE_GUIDE.md`
- Accessibility & Responsive: `guides/frontend/`
- Performance & Components: `technical/frontend/`
- E2E Testing: `testing/frontend/`


**Untuk Database & Data:**

- Skema: `D09_DATABASE_DOCUMENTATION.md`
- Migrasi: `D05_DATA_MIGRATION_PLAN.md`, `D06_DATA_MIGRATION_SPECIFICATION.md`
- RTM: `reference/rtm/`


### ℹ️ Menggunakan INDEX.md & Panduan Lanjut

Untuk navigasi lengkap dan terstruktur, rujuk dokumen-dokumen berikut:

📖 **[INDEX.md](INDEX.md)** - Dokumentasi navigation guide dengan reading paths untuk berbagai roles (Pembangun, Pentadbir, Pengurusan, QA)

📋 **[ORGANIZATION_SUMMARY.md](ORGANIZATION_SUMMARY.md)** - Ringkasan organisasi Fasa 1 (file cleanup & initial organization)

📋 **[REORGANIZATION_PHASE_2_COMPLETE.md](REORGANIZATION_PHASE_2_COMPLETE.md)** - Ringkasan reorganisasi Fasa 2 (reference/ restructuring) - BARU Nov 6, 2025

---

## Kesimpulan (Conclusion)

Dokumentasi ICTServe direka untuk:

✅ **Mematuhi piawaian antarabangsa** (ISO/IEC/IEEE)
✅ **Mudah navigasi** dengan cross-reference lengkap
✅ **Konsisten** dalam format dan terminologi
✅ **Mudah dikemaskini** dengan SemVer dan changelog
✅ **Accessible** untuk semua stakeholder (pembangun, pentadbir, pengurusan)
✅ **Terorganisir dengan rapi** mengikut kategori (sejak 1 Nov 2025)

## Sokongan

Untuk pertanyaan atau isu berkaitan dokumentasi, sila rujuk:

- **Pasukan Pembangunan ICTServe**
- **E-mel:** <support@ictserve.motac.gov.my>
- **Dokumentasi Sumbangan:** `../CONTRIBUTING.md`


Untuk maklumat lanjut atau pertanyaan, sila rujuk dokumen spesifik atau hubungi BPM MOTAC.

---

**Dokumen ini disediakan mengikut piawaian ISO/IEC/IEEE 12207, ISO/IEC/IEEE 29148, ISO/IEC/IEEE 15288, IEEE 1016, ISO 9241-210, ISO 8000, dan WCAG 2.2 Level AA. Ia akan dikemaskini mengikut keperluan projek dan feedback stakeholder.**
