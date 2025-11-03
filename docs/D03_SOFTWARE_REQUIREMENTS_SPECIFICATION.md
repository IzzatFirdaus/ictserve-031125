# Spesifikasi Keperluan Perisian (Software Requirements Specification - SRS)

**Sistem ICTServe**  
**Versi:** 3.0.0 (SemVer)  
**Tarikh Kemaskini:** 31 Oktober 2025  
**Status:** Aktif  
**Klasifikasi:** Terhad - Dalaman BPM MOTAC  
**Penulis:** Pasukan Pembangunan BPM MOTAC  
**Standard Rujukan:** ISO/IEC/IEEE 29148, ISO/IEC/IEEE 15288, WCAG 2.2 AA, OWASP ASVS L2, MyGOV Digital Service Standards v2.1.0

---

## Maklumat Dokumen (Document Information)

| Atribut            | Nilai                                                                                               |
|--------------------|-----------------------------------------------------------------------------------------------------|
| **Versi**          | 3.0.0                                                                                               |
| **Tarikh Kemaskini** | 31 Oktober 2025                                                                                   |
| **Status**         | Aktif                                                                                               |
| **Klasifikasi**    | Terhad - Dalaman BPM MOTAC                                                                          |
| **Pematuhi**       | ISO/IEC/IEEE 29148, ISO/IEC/IEEE 15288, WCAG 2.2 AA, OWASP ASVS L2, MyGOV Digital Service Standards v2.1.0 |
| **Bahasa**         | Bahasa Melayu (utama), English (teknikal)                                                           |

> Notis Penggunaan Dalaman: Sistem ini adalah untuk kegunaan warga kerja MOTAC (staf dan pegawai gred) sahaja dan tidak dibuka kepada orang awam.

---

## Sejarah Perubahan (Changelog)

| Versi | Tarikh          | Perubahan                                                                                                                                                                                            | Penulis                 |
|-------|-----------------|-------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------|-------------------------|
| 3.0.1 | 31 Oktober 2025 | Penyelarasan pautan dalaman: rujukan ke GLOSSARY dipusatkan ke `docs/GLOSSARY.md`; pindahkan dokumen induk dan versi terkini ke `docs/`.                                                            | Pasukan Pembangunan BPM |
| 3.0.0 | 31 Oktober 2025 | Penjajaran penuh kepada seni bina dalaman (internal-only), autentikasi pengguna staf, kelulusan berperingkat dalam sistem, dan pematuhan WCAG 2.2 AA. | Pasukan Pembangunan BPM |
| 2.0.0 | 17 Oktober 2025 | Penyeragaman mengikut D00-D14, SemVer, cross-reference                                                                                                                                               | Pasukan BPM             |
| 1.0.0 | September 2025  | Versi awal SRS                                                                                                                                                                                       | Pasukan BPM             |

---

## Rujukan Dokumen Berkaitan (Related Document References)

- **[D00_SYSTEM_OVERVIEW.md]**
- **[D01_SYSTEM_DEVELOPMENT_PLAN.md]**
- **[D02_BUSINESS_REQUIREMENTS_SPECIFICATION.md]**
- **[D04_SOFTWARE_DESIGN_DOCUMENT.md]**
- **[D05_DATA_MIGRATION_PLAN.md]**
- **[D06_DATA_MIGRATION_SPECIFICATION.md]**
- **[D07_SYSTEM_INTEGRATION_PLAN.md]**
- **[D08_SYSTEM_INTEGRATION_SPECIFICATION.md]**
- **[D09_DATABASE_DOCUMENTATION.md]**
- **[D10_SOURCE_CODE_DOCUMENTATION.md]**
- **[D11_TECHNICAL_DESIGN_DOCUMENTATION.md]**
- **[D12_UI_UX_DESIGN_GUIDE.md]**
- **[D13_UI_UX_FRONTEND_FRAMEWORK.md]**
- **[D14_UI_UX_STYLE_GUIDE.md]**
- **[D15_LANGUAGE_MS_EN.md]**
- **docs/helpdesk_form_to_model.md**
- **docs/loan_form_to_model.md**
- **docs/frontend/accessibility-guidelines.md**
- **docs/frontend/core-web-vitals-testing-guide.md**
- **docs/performance-optimization-report.md**

---

## 1. TUJUAN DOKUMEN (Purpose)

Dokumen ini mendefinisikan keperluan perisian terperinci untuk ICTServe sebagai sistem dalaman (internal-only) untuk warga kerja MOTAC. Ia meliputi keperluan fungsional, antara muka, data, keselamatan, dan kebolehcapaian untuk memastikan modul Helpdesk & Asset Loan beroperasi dengan log masuk pengguna dalaman dan kawalan pentadbiran melalui panel Filament v4.

---

## 2. SKOP SISTEM (System Scope)

Skop meliputi:

- Borang dalaman dwibahasa untuk Helpdesk & Asset Loan.
- Perkhidmatan backend (Laravel 12, Livewire v3, queue) bagi pengesahan data, notifikasi, kelulusan, audit, dan laporan.
- Panel pentadbiran Filament v4 untuk `admin` dan `superuser`.
- Integrasi dengan e-mel, SMS gateway, dan storan objek untuk lampiran.

Di luar skop:

- Portal awam untuk pengguna luar.
- Integrasi LDAP/SSO untuk pengguna awam.
- Modul self-service untuk kemaskini profil pengguna (tiada akaun awam).

---

## 3. DEFINISI, AKRONIM & SINGKATAN (Definitions, Acronyms & Abbreviations)

| Istilah | Makna |
|---------|-------|
| **Pengguna Dalaman** | Staf MOTAC yang menggunakan sistem melalui portal intranet (login diperlukan). |
| **Admin** | Pegawai BPM yang memproses tiket & permohonan melalui Filament. |
| **Superuser** | Pegawai BPM yang mentadbir konfigurasi, integrasi, dan audit. |
| **Signed Approval Link (SAL)** | Pautan dengan token bertanda tangan (JWT + hash) yang membolehkan kelulusan tanpa log masuk. |
| **SLA** | Service Level Agreement. |
| **WCAG 2.2 AA** | Piawaian kebolehcapaian W3C. |
| **ASVS** | OWASP Application Security Verification Standard. |

---

## 4. PERSEKITARAN SISTEM (System Environment)

- **Platform:** Laravel 12, PHP 8.2, Livewire v3, Volt, Filament v4.
- **Frontend:** Vite + Tailwind CSS, layout `guest.blade.php`, `@vite` bundling, responsive breakpoints (rujuk D13 §5).
- **Backend:** PHP-FPM, queue (Redis), scheduled jobs (`artisan schedule:run`), Filament resources untuk operasi pentadbiran.
- **Database:** MySQL 8 (utf8mb4), migrasi Laravel, audit tables (`activity_log`, `loan_audits`).
- **Security Controls:** CSRF, rate limiting, reCAPTCHA Enterprise, signed routes, hashed tokens, encryption at rest untuk fail sensitif.
- **Deployment:** Docker/Nginx atau bare-metal (rujuk D11 §2 & D00 §11a).
- **Monitoring:** Laravel Telescope (restricted), Prometheus/Grafana untuk metrik, Sentry untuk error tracking.

Nota: Tiada modul Laravel Breeze/Fortify untuk pengguna awam; hanya guard `filament` digunakan.

---

## 5. KEPERLUAN FUNGSI (Functional Requirements)

### 5.1. Modul Helpdesk Ticketing (Internal Flow)

| ID | Keperluan | Perincian |
|----|-----------|-----------|
| SRS-HELP-001 | Borang Tetamu | Tetamu boleh mengisi borang dwibahasa (BM/EN) dengan medan wajib: nama, e-mel, telefon, bahagian, gred, kategori, deskripsi, lampiran, perakuan PDPA. |
| SRS-HELP-002 | Validasi Masa Nyata | Livewire memaparkan ralat masa nyata, memastikan format e-mel/telefon sah, had lampiran (≤5MB, 5 fail). |
| SRS-HELP-003 | Penjanaan Tiket | Sistem menjana `ticket_number`, status awal `OPEN`, menyimpan metadata tetamu (`submitter_name`, `submitter_email`). |
| SRS-HELP-004 | Notifikasi Tetamu | E-mel pengesahan dihantar dengan ringkasan tiket & pautan semakan status (token). |
| SRS-HELP-005 | Triage Admin | `admin` menerima notifikasi queue, boleh menukar status (In Progress, Awaiting Info, Resolved, Closed) melalui Filament. |
| SRS-HELP-006 | Komunikasi | `admin` boleh menambah komen; tetamu menerima e-mel setiap kemas kini. |
| SRS-HELP-007 | SLA & Eskalasi | Sistem menjejaki masa tindak balas; `superuser` menerima amaran SLA (rujuk D11 §7). |
| SRS-HELP-008 | Lampiran | Fail disimpan di storan objek dengan metadata; akses dihadkan kepada `admin`/`superuser`. |

### 5.2. Modul ICT Asset Loan (Internal Flow)

| ID | Keperluan | Perincian |
|----|-----------|-----------|
| SRS-LOAN-001 | Borang Permohonan Tetamu | Tetamu mengisi data pemohon, butiran aset, tarikh mula/tamat, lokasi, tujuan, perakuan PDPA. |
| SRS-LOAN-002 | Pemeriksaan Ketersediaan | Sistem menyemak konflik tempahan aset, status `loan_transactions`, dan memaparkan alternatif. |
| SRS-LOAN-003 | Penjanaan Permohonan | Permohonan disimpan dengan kod rujukan unik, status `PENDING_SUPERVISOR_APPROVAL`. |
| SRS-LOAN-004 | Kelulusan E-mel | `ApprovalService` menjana token bertanda tangan (JWT) dan menghantar e-mel kepada pegawai Gred 41 dengan butang **Luluskan / Tolak**. |
| SRS-LOAN-005 | Laman Kelulusan | Pautan membawa ke halaman tetamu ringkas yang memaparkan maklumat permohonan dan pilihan keputusan. Tiada log masuk diperlukan. |
| SRS-LOAN-006 | Rekod Keputusan | Keputusan (APPROVED/REJECTED), catatan, masa, alamat IP pegawai disimpan dalam `loan_approvals`. |
| SRS-LOAN-007 | Pengeluaran Aset | `admin` menandakan `loan_transactions` (Check-out, Check-in), merekod pegawai BPM yang mengurus aset. |
| SRS-LOAN-008 | Notifikasi & Peringatan | Tetamu & `admin` menerima e-mel bagi setiap perubahan status; peringatan dihantar 3 hari sebelum tarikh pulang. |
| SRS-LOAN-009 | Audit Trail | Semua tindakan direkod dalam `loan_audits` dan `activity_log` (rujuk D09 §4.6 & §4.7). |

### 5.3. Portal Pentadbiran Filament (Admin & Superuser)

| ID | Keperluan | Perincian |
|----|-----------|-----------|
| SRS-ADM-001 | Autentikasi Pentadbir | Hanya `admin` & `superuser` wujud dalam jadual `users`. Guard Filament memerlukan 2FA (TOTP) bagi `superuser`. |
| SRS-ADM-002 | Kawalan Peranan | `admin` mempunyai akses operasi; `superuser` mempunyai akses konfigurasi, audit, tetapan integrasi. |
| SRS-ADM-003 | Dashboard | Papar metrik SLA, backlog tiket, status aset, permohonan tertunggak, dan audit terkini. |
| SRS-ADM-004 | Pengurusan Kandungan | `admin` boleh menyunting salinan borang (soalan bantu, tooltip) tanpa menyentuh kod. |
| SRS-ADM-005 | Laporan | Eksport CSV/PDF untuk statistik, pematuhan, dan audit. |

### 5.4. Layanan Integrasi & Notifikasi

- E-mel dihantar melalui SMTP kerajaan dengan fallback (SES). Semua e-mel dibina menggunakan templat WCAG (teks + HTML).
- SMS dihantar menggunakan gateway BPM; API token disimpan dalam pengurus rahsia.
- Webhooks (opsyen) untuk memaklumkan sistem lain, dikawal oleh `superuser`.

### 5.5. Keperluan Audit & Logging

- Setiap tindakan backend dicatat (model, ID, perubahan, aktor).
- Tetamu dikenal pasti melalui metadata (`submitter_email`) dan alamat IP hashed + UA.
- Log audit dihantar ke SIEM BPM setiap 15 minit.

---

## 6. KEPERLUAN ANTARA MUKA (Interface Requirements)

- **UI Web Tetamu:** Layout `guest.blade.php`, komponen Livewire, warna WCAG (Primary #0056B3, Secondary #0B4D8F).
- **UI Tetamu Kelulusan:** Halaman ringan memaparkan ringkasan permohonan dengan pilihan dua butang + input catatan.
- **Filament Admin UI:** Tema tinggi kontras (rujuk `filament-admin-interface-compliance.md`).
- **Integrasi Pihak Ketiga:** JSON REST API untuk SMS gateway dan potensi webhook.
- **Accessibility:** Semua komponen mematuhi `aria` semantics, `role`, `aria-live` untuk mesej status.

---

## 7. KEPERLUAN DATA (Data Requirements)

- `users` menyimpan medan: nama, e-mel kerajaan, telefon, role (`admin` atau `superuser`), 2FA secret (opsyenal), preferences (tanpa `locale`).
- `helpdesk_tickets` menyimpan metadata tetamu (`submitter_name`, `submitter_email`, `submitter_phone`, `division_code`, `grade`), kategori, status, SLA, lampiran.
- `loan_applications` menyimpan data tetamu, aset, tarikh pinjaman, tujuan, status, `approval_token`.
- `loan_approvals` menyimpan `approver_email`, `approver_grade`, `decision`, `decision_at`, `decision_ip` (hashed), catatan.
- `status_tokens` (opsyen) menyimpan token unik untuk tetamu semak status.
- Semua data peribadi disulitkan semasa rehat (Eloquent casts + encryption) untuk medan sensitif (telefon, e-mel) mengikut D09.

---

## 8. KEPERLUAN BUKAN FUNGSI (Non-Functional Requirements)

### 8.1. Keselamatan (Security)

- Mematuhi OWASP ASVS L2.
- Signed routes + token hashed untuk kelulusan & status.
- Rate limit 60/min per IP untuk borang tetamu; reCAPTCHA (invisible) untuk mitigasi bot.
- Fail lampiran diimbas (ClamAV) sebelum boleh dimuat turun.
- Audit log immutable (Write Once Read Many) selama 7 tahun.

### 8.2. Prestasi (Performance)

- LCP < 2.5s, FID < 100ms, TTFB < 500ms untuk borang tetamu.
- Queue memproses notifikasi < 30s.
- Filament dashboard memuat < 3s dengan caching.

### 8.3. Kebolehskalaan (Scalability)

- Boleh diskalakan mendatar (multiple app servers) menggunakan Redis untuk session & queue.
- Boleh menambah borang tetamu baharu melalui modul Livewire tambahan.

### 8.4. Kebolehgunaan (Usability)

- Navigasi jelas, breadcrumbs pendek, tiada menu pengguna.
- Borang disusun dalam wizard/logical grouping, menyokong keyboard-only navigation.
- Bahasa dwibahasa automatik (rujuk D15).

### 8.5. Backup & Recovery

- Backup DB harian; retention 30 hari.
- Fail lampiran disalin ke storan sekunder 1x sehari.
- Pelan pemulihan diuji dua kali setahun.

### 8.6. Auditability

- Semua perubahan status memerlukan alasan (catatan).
- `superuser` boleh menjana laporan audit; log boleh dieksport ke CSV.

### 8.7. Integrasi (Integration)

- SMTP, SMS gateway, optional webhook.
- Tiada integrasi LDAP/SSO untuk tetamu.

### 8.8. Pematuhan Polisi & Undang-undang

- PDPA, MCMC SMS guideline, MyGOV DSS v2.1.0, ISO/IEC 27001 Annex A (rujukan).

---

## 9. KEPERLUAN PERISIAN LUAR (External Software Requirements)

- PHP 8.2, Composer, Node.js 20, Redis, MySQL 8, ClamAV, Supervisor.
- reCAPTCHA Enterprise, GOV SMTP, BPM SMS gateway.
- Sentry (opsyenal), Grafana/Prometheus.

---

## 10. KEPERLUAN PENGURUSAN (Management Requirements)

- DevOps pipeline menjalankan `vendor/bin/pint`, `vendor/bin/phpstan`, `php artisan test`, Lighthouse (CI).
- Perubahan keperluan didokumentasi melalui D01 §9.3 (Change Request ID, impak, pemilik, pelan rollback).
- QA melaksanakan ujian Livewire & penerimaan tetamu (rujuk `testing/user-acceptance-testing-guide.md` – bakal dikemas kini).

---

## 11. KEPERLUAN UNDANG-UNDANG & DASAR (Legal & Policy Requirements)

- PDPA 2010, ISO/IEC 27701 (privacy), Arahan Keselamatan ICT MOTAC, MyGOV Digital Service Standards v2.1.0, WCAG 2.2 AA.
- Data retention: Tetamu 7 tahun, audit 7 tahun, log queue 12 bulan.
- Penyulitan: TLS 1.3, AES-256 at rest untuk data sensitif.

---

## 12. KEPERLUAN KEBERJAYAAN (Success Criteria)

- ≥ 95% e-mel kelulusan diselesaikan tanpa bantuan manual.
- Tiada aduan kritikal berkaitan aksesibiliti dalam audit dwi-tahunan.
- Skor Lighthouse ≥ 90 (Desktop/Mobile) untuk borang helpdesk & loan.
- 0 insiden kebocoran data berkaitan pautan kelulusan.

---

## 13. GLOSARI & RUJUKAN (Glossary & References)

Lihat D12-D14 untuk istilah UI/UX, `GLOSSARY.md` untuk istilah am (dikemas kini kepada guest-first).

---

## 14. LAMPIRAN (Appendices)

### 14.1. Borang Rujukan

- `helpdesk_form_to_model.md`
- `loan_form_to_model.md`

### 14.2. Carta Alir & Diagram

- Diagram senibina (D04 §3, D11 §2).
- Carta alir kelulusan e-mel (D04 §4.2).

### 14.3. Dokumen Sokongan

- `filament-admin-interface-compliance.md`
- `accessibility-testing-checklist.md`
- `core-web-vitals-testing-guide.md`

---

## 15. MATRIKS PEMETAAN KEPERLUAN (Requirements Traceability Matrix)

RTM diselenggara dalam `docs/rtm/helpdesk_requirements_rtm.csv`, `docs/rtm/loan_requirements_rtm.csv`, dan `docs/rtm/coredata_requirements_rtm.csv`. Semua keperluan SRS versi 3.0.0 diberi prefix `SRS-3.x` dan dipetakan kepada SDD (D04), TDD (D11), serta kes ujian PHPUnit/Livewire. Pengurusan perubahan mematuhi D01 §9.3.
