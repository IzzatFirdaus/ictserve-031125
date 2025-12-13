# Spesifikasi Keperluan Perisian (Software Requirements Specification - SRS)

**Sistem ICTServe**  
**Versi:** 3.5.0 (SemVer)  
**Tarikh Kemaskini:** 30 November 2025  
**Status:** Aktif  
**Klasifikasi:** Terhad - Dalaman BPM MOTAC  
**Penulis:** Pasukan Pembangunan BPM MOTAC  
**Standard Rujukan:** ISO/IEC/IEEE 29148, ISO/IEC/IEEE 15288, WCAG 2.2 AA, OWASP ASVS L2, MyGOV Digital Service Standards v2.1.0

---

## Maklumat Dokumen (Document Information)

| Atribut              | Nilai                                                                                                      |
| -------------------- | ---------------------------------------------------------------------------------------------------------- |
| **Versi**            | 3.5.0                                                                                                      |
| **Tarikh Kemaskini** | 30 November 2025                                                                                           |
| **Status**           | Aktif                                                                                                      |
| **Klasifikasi**      | Terhad - Dalaman BPM MOTAC                                                                                 |
| **Pematuhi**         | ISO/IEC/IEEE 29148, ISO/IEC/IEEE 15288, WCAG 2.2 AA, OWASP ASVS L2, MyGOV Digital Service Standards v2.1.0 |
| **Bahasa**           | Bahasa Melayu (utama), English (teknikal)                                                                  |

> Notis Penggunaan Dalaman: Sistem ini adalah untuk kegunaan warga kerja MOTAC (staf dan pegawai gred) sahaja dan tidak dibuka kepada orang awam.

---

## Sejarah Perubahan (Changelog)

| Versi | Tarikh           | Perubahan                                                                                                                                                                                                                                                                 | Penulis                 |
| ----- | ---------------- | ------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------- | ----------------------- |
| 3.5.0 | 30 November 2025 | True Hybrid Architecture: Self-registration (@motac.gov.my), flexible login (email/username), optional guest-to-account linking, dual audit system (owen-it + spatie), Laravel Telescope (superuser only), multi-channel notifications. Pematuhan Jabatan Digital Negara. | Pasukan Pembangunan BPM |
| 3.6.0 | 8 Disember 2025  | Bahasa Melayu sahaja untuk antara muka: Kemaskini SRS-HELP-001 borang dwibahasa→Bahasa Melayu sahaja. Kemaskini rujukan bahasa dwibahasa automatik→Bahasa Melayu sahaja. Penyelarasan dengan D00-D17 v3.6.0.                                                              | Pasukan Pembangunan BPM |
| 3.4.0 | 29 November 2025 | Hybrid Architecture: Staff boleh log masuk (Laravel Breeze - akaun pangkalan data) untuk Dashboard ATAU gunakan borang tetamu. Tambah SRS-AUTH-001 (Dual Entry), SRS-DATA-001 (Hybrid Association). Nullable user_id FK. Penyelarasan dengan D00/D02/D04 v3.4.0.          | Pasukan Pembangunan BPM |
| 3.2.0 | 29 November 2025 | Kemaskini dokumentasi sistem: pengesahan versi teknologi semasa (Laravel 12.40.1, PHP 8.2.12, Livewire 3.7.0, Filament 4.1.10, PHPUnit 11.5.44, Larastan 3.8.0, Laravel Pint 1.26.0). Penyelarasan dengan D00-D02 v3.2.0.                                                 | Pasukan Pembangunan BPM |
| 3.1.0 | 29 November 2025 | Kemaskini kepada teknologi semasa: Laravel 12.40.1, Livewire 3.7.0, Filament 4.1.10, Volt 1.10.1, Alpine.js 3, Tailwind CSS 4.1.17, Laravel Reverb 1.6.2, Laravel Echo 2.2.6, PHPUnit 11.5.44. Pematuhan WCAG 2.2 AA dan OWASP ASVS L2.                                   | Pasukan Pembangunan BPM |
| 3.0.1 | 31 Oktober 2025  | Penyelarasan pautan dalaman: rujukan ke GLOSSARY dipusatkan ke `docs/GLOSSARY.md`; pindahkan dokumen induk dan versi terkini ke `docs/`.                                                                                                                                  | Pasukan Pembangunan BPM |
| 3.0.0 | 31 Oktober 2025  | Penjajaran penuh kepada seni bina dalaman (internal-only), autentikasi pengguna staf, kelulusan berperingkat dalam sistem, dan pematuhan WCAG 2.2 AA.                                                                                                                     | Pasukan Pembangunan BPM |
| 2.0.0 | 17 Oktober 2025  | Penyeragaman mengikut D00-D14, SemVer, cross-reference                                                                                                                                                                                                                    | Pasukan BPM             |
| 1.0.0 | September 2025   | Versi awal SRS                                                                                                                                                                                                                                                            | Pasukan BPM             |

---

## Rujukan Dokumen Berkaitan (Related Document References)

- **[D00_SYSTEM_OVERVIEW.md]** - System vision and governance (v3.5.0)
- **[D01_SYSTEM_DEVELOPMENT_PLAN.md]** - Development methodology (v3.5.0)
- **[D02_BUSINESS_REQUIREMENTS_SPECIFICATION.md]** - Business requirements (v3.5.0)
- **[D04_SOFTWARE_DESIGN_DOCUMENT.md]** - Architecture and design (v3.5.0)
- **[D05_DATA_MIGRATION_PLAN.md]** - Data migration strategy
- **[D06_DATA_MIGRATION_SPECIFICATION.md]** - Migration specifications
- **[D07_SYSTEM_INTEGRATION_PLAN.md]** - Integration planning
- **[D08_SYSTEM_INTEGRATION_SPECIFICATION.md]** - Integration specifications
- **[D09_DATABASE_DOCUMENTATION.md]** - Database schema and dual audit (v3.5.0)
- **[D10_SOURCE_CODE_DOCUMENTATION.md]** - Code organization
- **[D11_TECHNICAL_DESIGN_DOCUMENTATION.md]** - Technical infrastructure
- **[D12_UI_UX_DESIGN_GUIDE.md]** - UI/UX guidelines (v3.5.0)
- **[D13_UI_UX_FRONTEND_FRAMEWORK.md]** - Frontend framework (v3.5.0)
- **[D14_UI_UX_STYLE_GUIDE.md]** - Style guide (v3.5.0)
- **[D15_LANGUAGE_MS_EN.md]** - Language localization (Bahasa Melayu sahaja, v3.6.0)
- **[D16_BROADCASTING_SETUP.md]** - WebSocket configuration (Laravel Reverb)
- **[D17_QUEUE_MANAGEMENT_HORIZON.md]** - Queue management (Laravel Horizon)
- **docs/helpdesk_form_to_model.md** - Helpdesk data mapping
- **docs/loan_form_to_model.md** - Asset loan data mapping
- **docs/frontend/accessibility-guidelines.md** - WCAG 2.2 AA compliance
- **docs/frontend/core-web-vitals-testing-guide.md** - Performance testing
- **docs/performance-optimization-report.md** - Performance audit results

---

## 1. TUJUAN DOKUMEN (Purpose)

Dokumen ini mendefinisikan keperluan perisian terperinci untuk ICTServe sebagai sistem dalaman (internal-only) untuk warga kerja MOTAC. Ia meliputi keperluan fungsional, antara muka, data, keselamatan, dan kebolehcapaian untuk memastikan modul Helpdesk & Asset Loan beroperasi dengan log masuk pengguna dalaman dan kawalan pentadbiran melalui panel Filament 4.1.10. Sistem dibina menggunakan Laravel 12.40.1, Livewire 3.7.0, Volt 1.10.1, Alpine.js 3, Tailwind CSS 4.1.17, dan Laravel Reverb 1.6.2 untuk komunikasi masa nyata.

---

## 2. SKOP SISTEM (System Scope)

Skop meliputi:

- Borang dalaman Bahasa Melayu untuk Helpdesk & Asset Loan (akses tetamu ATAU authenticated staff).
- Perkhidmatan backend (Laravel 12.40.1, Livewire 3.7.0, Volt 1.10.1, queue) bagi pengesahan data, notifikasi, kelulusan, audit, dan laporan.
- Panel pentadbiran Filament 4.1.10 untuk `admin` dan `superuser`.
- **Hybrid Access Model**: Staff log masuk (Laravel Breeze) untuk My Dashboard, view history, auto-fill forms ATAU gunakan borang tetamu (quick access, tracked via Token).
- **Hybrid Data Association**: **CRITICAL:** If Auth::check() === true, link submission to user_id (nullable FK). If false, user_id=NULL, fallback to submitter_email. Email notifications sent to submitter_email regardless.
- Komunikasi masa nyata menggunakan Laravel Reverb 1.6.2 dan Laravel Echo 2.2.6.
- Integrasi dengan e-mel, SMS gateway, dan storan objek untuk lampiran.

Di luar skop:

- Portal awam untuk pengguna luar.
- Modul self-service untuk kemaskini profil pengguna awam.

---

## 3. DEFINISI, AKRONIM & SINGKATAN (Definitions, Acronyms & Abbreviations)

| Istilah                        | Makna                                                                                                                      |
| ------------------------------ | -------------------------------------------------------------------------------------------------------------------------- |
| **Hybrid Access Model**        | Dual entry: Staff log masuk (Laravel Breeze) untuk Dashboard ATAU gunakan borang tetamu (quick access).                    |
| **Authenticated Staff**        | Staff log masuk via Laravel Breeze → akses My Dashboard, view history, auto-fill forms.                                    |
| **Guest/Quick Access**         | Staff gunakan borang intranet tanpa log masuk → tracked via Token, manual entry required.                                  |
| **Hybrid Data Association**    | **CRITICAL:** If logged in, link submission to user_id (nullable FK). If guest, user_id=NULL, fallback to submitter_email. |
| **My Dashboard**               | Authenticated staff portal: view submission history, profile, notifications (DB + Email).                                  |
| **Admin**                      | Pegawai BPM yang memproses tiket & permohonan melalui Filament.                                                            |
| **Superuser**                  | Pegawai BPM yang mentadbir konfigurasi, integrasi, dan audit.                                                              |
| **Signed Approval Link (SAL)** | Pautan dengan token bertanda tangan (JWT + hash) yang membolehkan kelulusan tanpa log masuk.                               |
| **SLA**                        | Service Level Agreement.                                                                                                   |
| **WCAG 2.2 AA**                | Piawaian kebolehcapaian W3C.                                                                                               |
| **ASVS**                       | OWASP Application Security Verification Standard.                                                                          |
| **Laravel Pulse**              | Real-time application performance monitoring dashboard (v1.3.0) for tracking slow queries, queue jobs, and server health.  |
| **Laravel Sanctum**            | API token authentication system (v4.0) for secure API access with configurable abilities and expiration.                   |
| **Laravel Socialite**          | OAuth 2.0 social authentication library (v5.x) for Google Workspace SSO integration.                                       |
| **Pickup OTP**                 | 4-digit one-time password for secure asset collection with 24-hour validity.                                               |
| **Email Reply-to-Ticket**      | IMAP/webhook integration allowing users to reply to ticket notifications via email.                                        |
| **Fuzzy Search**               | Intelligent search with typo tolerance and partial matching using Levenshtein distance algorithm.                          |
| **Session Timeout Warning**    | 2-minute warning modal before 30-minute session expiry.                                                                    |
| **Onboarding Tour**            | Interactive guided tour for new users with contextual help tooltips.                                                       |
| **Dashboard Widgets**          | Customizable real-time widgets for admin dashboard (Ticket Stats, Loan Stats, SLA Compliance, Recent Activity).            |
| **Saved Filters**              | User-specific filter combinations that can be saved, named, and reused across sessions.                                    |
| **Touch Gestures**             | Mobile-optimized interactions including swipe navigation, pull-to-refresh, and infinite scroll.                            |
| **API Token**                  | Sanctum-based authentication token for API access with configurable abilities and expiration.                              |
| **Google Workspace SSO**       | OAuth 2.0 authentication using Google Workspace accounts restricted to @motac.gov.my domain.                               |

---

## 4. PERSEKITARAN SISTEM (System Environment)

- **Platform:** Laravel 12.40.1, PHP 8.2.12, Livewire 3.7.0, Volt 1.10.1, Filament 4.1.10.
- **Frontend:** Vite 7.0.7 + Tailwind CSS 4.1.17, Alpine.js 3, layout `guest.blade.php`, `@vite` bundling, responsive breakpoints (rujuk D13 §5).
- **Real-time:** Laravel Reverb 1.6.2 (WebSocket server), Laravel Echo 2.2.6 (client).
- **Backend:** PHP-FPM, queue (Redis), scheduled jobs (`artisan schedule:run`), Filament resources untuk operasi pentadbiran.
- **Database:** MySQL 8 (utf8mb4), migrasi Laravel, audit tables (`activity_log`, `loan_audits`).
- **Security Controls:** CSRF, rate limiting, reCAPTCHA Enterprise, signed routes, hashed tokens, encryption at rest untuk fail sensitif.
- **Deployment:** Docker/Nginx atau bare-metal (rujuk D11 §2 & D00 §11a).
- **Monitoring:** Laravel Telescope (restricted), Prometheus/Grafana untuk metrik, Sentry untuk error tracking.

Nota: Tiada modul Laravel Breeze/Fortify untuk pengguna awam; guard `web` digunakan untuk portal staf/My Dashboard manakala guard `filament` digunakan untuk panel pentadbir.

---

## 5. KEPERLUAN FUNGSI (Functional Requirements)

### 5.1. Modul Helpdesk Ticketing (Hybrid Flow)

| ID           | Keperluan               | Perincian                                                                                                                                                                                              |
| ------------ | ----------------------- | ------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------ |
| SRS-HELP-001 | Borang Hybrid           | Staff boleh mengisi borang Bahasa Melayu sebagai tetamu ATAU authenticated user. Medan wajib: nama, e-mel, telefon, bahagian, gred, kategori, deskripsi, lampiran, perakuan PDPA.                      |
| SRS-AUTH-001 | Dual Entry Model        | **NEW:** Staff boleh log masuk (Laravel Breeze) untuk My Dashboard ATAU gunakan borang tetamu. System detect Auth::check() untuk auto-fill dan user_id linking.                                        |
| SRS-DATA-001 | Hybrid Data Association | **CRITICAL:** If Auth::check() === true, link submission to user*id (nullable FK). If false, user_id=NULL, require manual submitter*\* fields. Email notifications sent to submitter_email regardless. |
| SRS-FORM-001 | Auto-fill Data          | Jika staff log masuk (Auth::check() === true), borang auto-fill nama, e-mel, telefon, bahagian, gred dari profil pengguna. Jika guest, require manual entry.                                           |
| SRS-HELP-002 | Validasi Masa Nyata     | Livewire 3.7.0 + Volt 1.10.1 memaparkan ralat masa nyata dengan Alpine.js 3, memastikan format e-mel/telefon sah, had lampiran (≤5MB, 5 fail).                                                         |
| SRS-HELP-003 | Penjanaan Tiket         | Sistem menjana `ticket_number`, status awal `OPEN`, menyimpan metadata tetamu (`submitter_name`, `submitter_email`).                                                                                   |
| SRS-HELP-004 | Notifikasi Tetamu       | E-mel pengesahan dihantar dengan ringkasan tiket & pautan semakan status (token).                                                                                                                      |
| SRS-HELP-005 | Triage Admin            | `admin` menerima notifikasi queue dan real-time melalui Laravel Reverb 1.6.2, boleh menukar status (In Progress, Awaiting Info, Resolved, Closed) melalui Filament 4.1.10.                             |
| SRS-HELP-006 | Komunikasi              | `admin` boleh menambah komen; tetamu menerima e-mel setiap kemas kini.                                                                                                                                 |
| SRS-HELP-007 | SLA & Eskalasi          | Sistem menjejaki masa tindak balas; `superuser` menerima amaran SLA (rujuk D11 §7).                                                                                                                    |
| SRS-HELP-008 | Lampiran                | Fail disimpan di storan objek dengan metadata; akses dihadkan kepada `admin`/`superuser`.                                                                                                              |

### 5.2. Modul ICT Asset Loan (Hybrid Flow)

| ID           | Keperluan                | Perincian                                                                                                                                               |
| ------------ | ------------------------ | ------------------------------------------------------------------------------------------------------------------------------------------------------- |
| SRS-LOAN-001 | Borang Permohonan Hybrid | Staff mengisi data pemohon (auto-fill jika log masuk), butiran aset, tarikh mula/tamat, lokasi, tujuan, perakuan PDPA.                                  |
| SRS-LOAN-002 | Pemeriksaan Ketersediaan | Sistem menyemak konflik tempahan aset secara real-time menggunakan Livewire 3.7.0 + Alpine.js 3, status `loan_transactions`, dan memaparkan alternatif. |
| SRS-LOAN-003 | Penjanaan Permohonan     | Permohonan disimpan dengan kod rujukan unik, status `PENDING_SUPERVISOR_APPROVAL`.                                                                      |
| SRS-LOAN-004 | Kelulusan E-mel          | `ApprovalService` menjana token bertanda tangan (JWT) dan menghantar e-mel kepada pegawai Gred 41 dengan butang **Luluskan / Tolak**.                   |
| SRS-LOAN-005 | Laman Kelulusan          | Pautan membawa ke halaman tetamu ringkas yang memaparkan maklumat permohonan dan pilihan keputusan. Tiada log masuk diperlukan.                         |
| SRS-LOAN-006 | Rekod Keputusan          | Keputusan (APPROVED/REJECTED), catatan, masa, alamat IP pegawai disimpan dalam `loan_approvals`.                                                        |
| SRS-LOAN-007 | Pengeluaran Aset         | `admin` menandakan `loan_transactions` (Check-out, Check-in), merekod pegawai BPM yang mengurus aset.                                                   |
| SRS-LOAN-008 | Notifikasi & Peringatan  | Tetamu & `admin` menerima e-mel bagi setiap perubahan status; peringatan dihantar 3 hari sebelum tarikh pulang.                                         |
| SRS-LOAN-009 | Audit Trail              | Semua tindakan direkod dalam `loan_audits` dan `activity_log` (rujuk D09 §4.6 & §4.7).                                                                  |

### 5.3. Portal Pentadbiran Filament (Admin & Superuser)

| ID          | Keperluan             | Perincian                                                                                                                                                                           |
| ----------- | --------------------- | ----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------- |
| SRS-ADM-001 | Autentikasi Pentadbir | `admin`, `superuser`, dan `staff` wujud dalam jadual `users`. Guard Filament memerlukan 2FA (TOTP) bagi `superuser`. Staff boleh log masuk untuk My Dashboard.                      |
| SRS-ADM-006 | My Dashboard (Staff)  | **NEW:** Authenticated staff akses My Dashboard: view submission history (helpdesk + loan), profile management, notification center (DB + Email), quick actions.                    |
| SRS-ADM-002 | Kawalan Peranan       | `admin` mempunyai akses operasi; `superuser` mempunyai akses konfigurasi, audit, tetapan integrasi; `staff` mempunyai akses dashboard peribadi sahaja.                              |
| SRS-ADM-003 | Dashboard             | Papar metrik SLA, backlog tiket, status aset, permohonan tertunggak, dan audit terkini menggunakan Filament 4.1.10 widgets dengan kemaskini real-time melalui Laravel Reverb 1.6.2. |
| SRS-ADM-004 | Pengurusan Kandungan  | `admin` boleh menyunting salinan borang (soalan bantu, tooltip) tanpa menyentuh kod.                                                                                                |
| SRS-ADM-005 | Laporan               | Eksport CSV/PDF untuk statistik, pematuhan, dan audit.                                                                                                                              |
| SRS-ADM-007 | Laravel Pulse         | **NEW:** `admin` dan `superuser` akses Laravel Pulse dashboard untuk monitor prestasi real-time: slow queries, queue jobs, server health, memory usage.                             |
| SRS-ADM-008 | Laravel Telescope     | **NEW:** `superuser` sahaja akses Laravel Telescope tanpa sekatan untuk debugging: requests, commands, jobs, exceptions, logs, queries, models, events, mail, notifications.        |

### 5.4. Layanan Integrasi & Notifikasi

- E-mel dihantar melalui SMTP kerajaan dengan fallback (SES). Semua e-mel dibina menggunakan templat WCAG (teks + HTML).
- SMS dihantar menggunakan gateway BPM; API token disimpan dalam pengurus rahsia.
- Webhooks (opsyen) untuk memaklumkan sistem lain, dikawal oleh `superuser`.
- **Multi-channel notifications**: Staff boleh pilih keutamaan notifikasi (email immediate/daily digest/weekly digest, in-app toggle).
- **Email Reply-to-Ticket**: IMAP/webhook integration membolehkan pengguna reply ticket notifications via email (future enhancement).

### 5.5. Autentikasi & Keselamatan Tambahan

| ID           | Keperluan                  | Perincian                                                                                                                                                     |
| ------------ | -------------------------- | ------------------------------------------------------------------------------------------------------------------------------------------------------------- |
| SRS-AUTH-002 | Self-Registration          | Staff boleh register sendiri dengan email @motac.gov.my. Sistem hantar verification email dengan signed URL (24 jam validity). Email verification required.  |
| SRS-AUTH-003 | Flexible Login             | Staff boleh login dengan full email (`user@motac.gov.my`) ATAU short username (`user`). Sistem authenticate terhadap kedua-dua format.                       |
| SRS-AUTH-004 | Account Linking            | Staff boleh link historical guest submissions ke account baharu. Sistem search `user_id=NULL` records dengan matching email, display untuk confirmation.     |
| SRS-AUTH-005 | Google Workspace SSO       | **OPTIONAL:** OAuth 2.0 integration dengan Google Workspace, restricted to @motac.gov.my domain. Fallback to standard login jika SSO tidak available.        |
| SRS-AUTH-006 | Session Timeout Warning    | Sistem display 2-minute warning modal sebelum 30-minute session expiry. User boleh extend session atau logout.                                               |
| SRS-AUTH-007 | Pickup OTP                 | Sistem generate 4-digit OTP untuk secure asset collection. OTP valid 24 jam, sent via email/SMS. Admin verify OTP sebelum release asset.                     |

### 5.6. API & Integrasi Sistem

| ID          | Keperluan            | Perincian                                                                                                                                                                                                                |
| ----------- | -------------------- | ------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------ |
| SRS-API-001 | Laravel Sanctum Auth | **FUTURE-READY:** API token authentication untuk external integrations. Configurable abilities: `read:tickets`, `write:tickets`, `read:loans`, `write:loans`, `admin:all`. Token expiration configurable (default 30d). |
| SRS-API-002 | API Rate Limiting    | API endpoints enforce rate limiting: 60 req/min untuk authenticated users, 20 req/min untuk guest. Throttle by IP + user_id.                                                                                            |
| SRS-API-003 | API Documentation    | Swagger/OpenAPI documentation untuk semua public API endpoints. Auto-generated dari route definitions, accessible at `/api/documentation`.                                                                               |

### 5.7. Keperluan Audit & Logging (Dual System)

| ID           | Keperluan                    | Perincian                                                                                                                                                                                |
| ------------ | ---------------------------- | ---------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------- |
| SRS-AUDIT-01 | Owen-it Laravel Auditing     | Field-level audit tracking untuk compliance. Record old/new values, user ID, IP hash, timestamp untuk semua auditable models. Retention 7 tahun.                                        |
| SRS-AUDIT-02 | Spatie Activity Log          | User activity logging untuk operational dashboards. Record description, subject, causer, properties untuk significant actions. Retention 7 tahun.                                        |
| SRS-AUDIT-03 | Unified Audit View           | Superuser boleh view combined audit trail dari kedua-dua systems. Filter by date, user, action type, entity. Export to CSV/PDF.                                                         |
| SRS-AUDIT-04 | Guest Identification         | Tetamu dikenal pasti melalui metadata (`submitter_email`) dan alamat IP hashed + User Agent. Tiada PII stored in plain text.                                                            |
| SRS-AUDIT-05 | SIEM Integration             | Log audit dihantar ke SIEM BPM setiap 15 minit via secure API. Include security events, failed logins, permission changes.                                                              |
| SRS-AUDIT-06 | Immutable Audit Trail        | Audit logs adalah Write Once Read Many (WORM). Tiada deletion/modification allowed. Integrity verified via cryptographic hashing.                                                       |

### 5.8. Keperluan UX & Usability Enhancements

| ID          | Keperluan           | Perincian                                                                                                                                                                                                 |
| ----------- | ------------------- | --------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------- |
| SRS-UX-001  | Onboarding Tour     | Interactive guided tour untuk new users. Contextual help tooltips untuk key features. Skip/replay options. Tour progress saved per user.                                                                 |
| SRS-UX-002  | Fuzzy Search        | Intelligent search dengan typo tolerance. Levenshtein distance algorithm untuk partial matching. Search across tickets, loans, assets, users.                                                            |
| SRS-UX-003  | Saved Filters       | Users boleh save filter combinations dengan custom names. Reusable across sessions. Shareable dengan team members (admin only).                                                                          |
| SRS-UX-004  | Touch Gestures      | Mobile-optimized interactions: swipe navigation, pull-to-refresh, infinite scroll. Touch targets minimum 44x44 pixels. Haptic feedback untuk actions.                                                    |
| SRS-UX-005  | Dashboard Widgets   | Customizable real-time widgets: Ticket Stats, Loan Stats, SLA Compliance, Recent Activity, Performance Metrics, System Health. Drag-and-drop reordering. Widget preferences saved per user.              |
| SRS-UX-006  | Keyboard Shortcuts  | Comprehensive keyboard shortcuts untuk power users. `/` untuk search, `?` untuk help, `n` untuk new ticket/loan. Shortcuts displayed in help modal.                                                      |
| SRS-UX-007  | Dark Mode Support   | **FUTURE:** Optional dark mode untuk reduce eye strain. Maintain WCAG 2.2 AA contrast ratios. User preference saved. System-wide toggle.                                                                 |

---

## 6. KEPERLUAN ANTARA MUKA (Interface Requirements)

- **UI Web Tetamu:** Layout `guest.blade.php`, komponen Livewire, warna WCAG (Primary #0056B3, Secondary #0B4D8F).
- **UI Tetamu Kelulusan:** Halaman ringan memaparkan ringkasan permohonan dengan pilihan dua butang + input catatan.
- **Filament Admin UI:** Tema tinggi kontras (rujuk `filament-admin-interface-compliance.md`).
- **Integrasi Pihak Ketiga:** JSON REST API untuk SMS gateway dan potensi webhook.
- **Accessibility:** Semua komponen mematuhi `aria` semantics, `role`, `aria-live` untuk mesej status.

---

## 7. KEPERLUAN DATA (Data Requirements)

- `users` menyimpan medan: nama, e-mel kerajaan, telefon, bahagian, gred, role (`admin`, `superuser`, atau `staff`), 2FA secret (opsyenal), preferences (`locale` untuk authenticated users), `department_id` (FK), `grade` (string). **Staff role** untuk authenticated users yang akses My Dashboard.
- `helpdesk_tickets` menyimpan **nullable `user_id` FK (ON DELETE SET NULL)** + metadata tetamu (`submitter_name`, `submitter_email`, `submitter_phone`, `division_code`, `grade`), kategori, status, SLA, lampiran. **Index on `user_id`** for My Dashboard queries (WHERE user_id = Auth::id()).
- `loan_applications` menyimpan **nullable `user_id` FK (ON DELETE SET NULL)** + data tetamu, aset, tarikh pinjaman, tujuan, status, `approval_token`. **Index on `user_id`** for My Dashboard queries (WHERE user_id = Auth::id()).
- `loan_approvals` menyimpan `approver_email`, `approver_grade`, `decision`, `decision_at`, `decision_ip` (hashed), catatan.
- `status_tokens` menyimpan token unik untuk tetamu semak status.
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
- Bahasa Melayu sahaja untuk semua antara muka (rujuk D15 v3.6.0).

### 8.5. Backup & Recovery

- Backup DB harian; retention 30 hari.
- Fail lampiran disalin ke storan sekunder 1x sehari.
- Pelan pemulihan diuji dua kali setahun.

### 8.6. Auditability

- Semua perubahan status memerlukan alasan (catatan).
- `superuser` boleh menjana laporan audit; log boleh dieksport ke CSV.

### 8.7. Integrasi (Integration)

- SMTP, SMS gateway, optional webhook.
- **Self-registration** dengan Laravel Breeze untuk @motac.gov.my email domain.
- **Optional Google Workspace SSO** via Laravel Socialite (OAuth 2.0), restricted to @motac.gov.my domain.
- **Laravel Sanctum** untuk API authentication (future-ready, configurable token abilities).
- **Laravel Pulse** untuk real-time performance monitoring (admin/superuser access).
- **Laravel Telescope** untuk debugging dan monitoring (superuser only, unrestricted access).
- **Email Reply-to-Ticket** via IMAP/webhook integration (future enhancement).

### 8.8. Pematuhan Polisi & Undang-undang

- PDPA, MCMC SMS guideline, MyGOV DSS v2.1.0, ISO/IEC 27001 Annex A (rujukan).

---

## 9. KEPERLUAN PERISIAN LUAR (External Software Requirements)

### 9.1. Core Dependencies

- **PHP**: 8.2.12
- **Laravel Framework**: 12.40.1
- **Composer**: Latest stable
- **Node.js**: 20.x LTS
- **NPM**: 10.x
- **Redis**: 7.0 (caching, queue, Reverb backend)
- **MySQL**: 8.0 (utf8mb4)
- **ClamAV**: Latest (file scanning)
- **Supervisor**: Latest (queue worker management)

### 9.2. Laravel Ecosystem

- **Livewire**: 3.7.0 (server-driven UI)
- **Livewire Volt**: 1.10.1 (single-file components)
- **Filament**: 4.1.10 (admin panel)
- **Laravel Breeze**: 2.3.8 (authentication scaffolding)
- **Laravel Reverb**: 1.6.2 (WebSocket server)
- **Laravel Echo**: 2.2.6 (WebSocket client)
- **Laravel Telescope**: 5.x (debugging, superuser only)
- **Laravel Pulse**: 1.3.0 (performance monitoring)
- **Laravel Sanctum**: 4.0 (API authentication)
- **Laravel Socialite**: 5.x (OAuth 2.0 for Google Workspace SSO)
- **Owen-it Laravel Auditing**: 14.x (compliance audit)
- **Spatie Laravel Activity Log**: 4.x (user activity logging)
- **Spatie Laravel Permission**: 6.23 (RBAC)

### 9.3. Frontend Dependencies

- **Vite**: 7.0.7 (asset bundling)
- **Tailwind CSS**: 4.1.17 (styling)
- **Alpine.js**: 3.x (included with Livewire)
- **Pusher JS**: 8.x (WebSocket protocol)

### 9.4. Development Tools

- **Laravel Pint**: 1.26.0 (PSR-12 formatting)
- **Larastan**: 3.8.0 (PHPStan for Laravel)
- **PHPUnit**: 11.5.44 (testing)
- **Playwright**: 1.56.1 (E2E testing)
- **Laravel Prompts**: 0.3.8 (interactive CLI)

### 9.5. External Services

- **reCAPTCHA Enterprise**: Google (bot mitigation)
- **GOV SMTP**: Government email server dengan fallback ke AWS SES
- **BPM SMS Gateway**: Internal SMS service
- **Google Workspace**: OAuth 2.0 provider (optional SSO)
- **Sentry**: Error tracking (opsyenal)
- **Grafana/Prometheus**: Metrics monitoring (opsyenal)

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

### 12.1. Functional Success Criteria

- ≥ 95% e-mel kelulusan diselesaikan tanpa bantuan manual.
- ≥ 90% staff adoption rate untuk self-registration dalam 3 bulan.
- ≥ 80% staff menggunakan authenticated dashboard (vs guest forms) selepas 6 bulan.
- Account linking success rate ≥ 95% untuk historical submissions.

### 12.2. Performance Success Criteria

- Skor Lighthouse ≥ 90 (Desktop/Mobile) untuk borang helpdesk & loan.
- Core Web Vitals: LCP < 2.5s, FID < 100ms, CLS < 0.1.
- API response time < 200ms untuk 95th percentile.
- Queue job processing < 30s untuk 99th percentile.
- Laravel Pulse dashboard load time < 2s.

### 12.3. Security Success Criteria

- 0 insiden kebocoran data berkaitan pautan kelulusan.
- 0 successful brute force attacks (rate limiting effective).
- 100% file uploads scanned dengan ClamAV sebelum storage.
- Audit trail completeness ≥ 99.9% (no missing records).

### 12.4. Accessibility Success Criteria

- Tiada aduan kritikal berkaitan aksesibiliti dalam audit dwi-tahunan.
- Lighthouse accessibility score = 100 untuk semua public pages.
- WCAG 2.2 AA compliance verified via automated + manual testing.
- Keyboard navigation functional untuk 100% interactive elements.

### 12.5. Usability Success Criteria

- User satisfaction score ≥ 4.0/5.0 (post-implementation survey).
- Average time to submit ticket/loan ≤ 3 minutes (authenticated users).
- Help desk ticket volume reduction ≥ 20% (due to improved UX).
- Onboarding tour completion rate ≥ 70% untuk new users.

---

## 12A. KEPERLUAN BRANDING & VISUAL IDENTITY

### 12A.1. MOTAC Official Branding (MyGOV DSS v2.1.0 Compliance)

| ID           | Keperluan         | Perincian                                                                                                                                                                                                                                |
| ------------ | ----------------- | ---------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------- |
| SRS-BRAND-01 | Jata Negara       | Display Malaysian Coat of Arms (`public/images/jata-negara.svg`) di header semua public-facing pages (guest forms, status check, approval pages). Minimum size 48x48 pixels. Positioned top-left atau center-top per MyGOV DSS v2.1.0. |
| SRS-BRAND-02 | MOTAC Logo        | Display official MOTAC logo (`public/images/motac-logo.png`) alongside Jata Negara. Maintain aspect ratio. Minimum height 40px. Link to MOTAC official website.                                                                         |
| SRS-BRAND-03 | BPM Identity      | Display "Bahagian Pengurusan Maklumat (BPM)" subtitle di header. Font: Poppins SemiBold 14px. Color: #0B4D8F (MOTAC Secondary Blue).                                                                                                    |
| SRS-BRAND-04 | Color Palette     | Primary: #0056B3 (MOTAC Blue), Secondary: #0B4D8F (Dark Blue), Accent: #FFB81C (Gold), Success: #28A745, Warning: #FFC107, Danger: #DC3545. Maintain WCAG 2.2 AA contrast ratios.                                                       |
| SRS-BRAND-05 | Typography        | Headings: Poppins (SemiBold/Bold), Body: Inter (Regular/Medium). Font sizes: H1 32px, H2 24px, H3 20px, Body 16px, Small 14px. Line height 1.5 untuk readability.                                                                       |
| SRS-BRAND-06 | Footer Branding   | Display copyright "© 2025 Kementerian Pelancongan, Seni dan Budaya Malaysia. Hak Cipta Terpelihara." Links to Privacy Policy, Terms of Use, Contact BPM.                                                                                |
| SRS-BRAND-07 | Email Branding    | Email templates include Jata Negara, MOTAC logo, BPM contact info. Maintain brand colors. Plain text fallback untuk accessibility.                                                                                                      |
| SRS-BRAND-08 | PDF Report Header | Generated PDF reports include Jata Negara, MOTAC logo, document title, generation date, BPM watermark. Footer with page numbers dan confidentiality notice.                                                                             |

### 12A.2. MYDS Guidelines Adoption

ICTServe adopts Malaysia Government Design System (MYDS) guidelines sebagai best practices:

- **MYDS 12-8-4 Grid**: Responsive breakpoints - Desktop (≥1024px), Tablet (768-1023px), Mobile (≤767px). Implemented via Tailwind responsive utilities.
- **MYDS Spacing Scale**: 8px base unit (8, 16, 24, 32, 48, 64px). Implemented via Tailwind spacing utilities (`space-2`, `space-4`, etc.).
- **MYDS Typography**: Inter untuk body text, Poppins untuk headings. Font weights: Regular (400), Medium (500), SemiBold (600), Bold (700).
- **MYDS Colors**: Adopt MYDS color palette untuk consistency dengan government digital services. Maintain WCAG 2.2 AA compliance.
- **MYDS Motion**: Subtle animations (200-300ms) untuk transitions. Respect `prefers-reduced-motion` untuk accessibility.

**Implementation Note**: MYDS principles implemented using existing tech stack (Tailwind CSS 4.x, Livewire 3.7, Filament 4.1) rather than MYDS React components.

## 13. GLOSARI & RUJUKAN (Glossary & References)

Lihat D12-D14 untuk istilah UI/UX, `GLOSSARY.md` untuk istilah am (dikemas kini kepada True Hybrid Architecture).

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
