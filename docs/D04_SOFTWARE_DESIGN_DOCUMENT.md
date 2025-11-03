# Dokumen Rekabentuk Perisian (Software Design Document - SDD)

**Sistem ICTServe**  
**Versi:** 3.0.0 (SemVer)  
**Tarikh Kemaskini:** 31 Oktober 2025  
**Status:** Aktif  
**Klasifikasi:** Terhad - Dalaman BPM MOTAC  
**Penulis:** Pasukan Pembangunan BPM MOTAC  
**Standard Rujukan:** ISO/IEC/IEEE 42010, ISO/IEC/IEEE 15288, WCAG 2.2 AA, OWASP ASVS L2

---

## Maklumat Dokumen (Document Information)

| Atribut            | Nilai                                               |
|--------------------|-----------------------------------------------------|
| **Versi**          | 3.0.0                                               |
| **Tarikh Kemaskini** | 31 Oktober 2025                                   |
| **Status**         | Aktif                                               |
| **Klasifikasi**    | Terhad - Dalaman BPM MOTAC                          |
| **Pematuhi**       | ISO/IEC/IEEE 42010, ISO/IEC/IEEE 15288, WCAG 2.2 AA |
| **Bahasa**         | Bahasa Melayu (utama), English (teknikal)           |

> Notis Penggunaan Dalaman: Sistem ini digunakan secara dalaman oleh staf dan pegawai gred MOTAC; ia bukan sistem awam.

---

## Sejarah Perubahan (Changelog)

| Versi | Tarikh          | Perubahan                                                                                                                                             | Penulis                 |
|-------|-----------------|--------------------------------------------------------------------------------------------------------------------------------------------------------|-------------------------|
| 3.0.0 | 31 Oktober 2025 | Rekabentuk dikemas kini kepada seni bina dalaman (internal-only); autentikasi pengguna dalaman, RBAC, penyusunan semula modul Helpdesk & Loan, dan pengukuhan audit/kelulusan dalam sistem. | Pasukan Pembangunan BPM |
| 2.0.0 | 17 Oktober 2025 | Penyeragaman mengikut D00-D14, SemVer, cross-reference                                                                                                 | Pasukan BPM             |
| 1.0.0 | September 2025  | Versi awal SDD                                                                                                                                         | Pasukan BPM             |

---

## Rujukan Dokumen Berkaitan

- **[D00_SYSTEM_OVERVIEW.md]**
- **[D01_SYSTEM_DEVELOPMENT_PLAN.md]**
- **[D02_BUSINESS_REQUIREMENTS_SPECIFICATION.md]**
- **[D03_SOFTWARE_REQUIREMENTS_SPECIFICATION.md]**
- **[D05_DATA_MIGRATION_PLAN.md]**
- **[D06_DATA_MIGRATION_SPECIFICATION.md]**
- **[D07_SYSTEM_INTEGRATION_PLAN.md]**
- **[D08_SYSTEM_INTEGRATION_SPECIFICATION.md]**
- **[D09_DATABASE_DOCUMENTATION.md]**
- **[D11_TECHNICAL_DESIGN_DOCUMENTATION.md]**
- **[D12_UI_UX_DESIGN_GUIDE.md]**
- **[D13_UI_UX_FRONTEND_FRAMEWORK.md]**
- **[D14_UI_UX_STYLE_GUIDE.md]**
- **docs/helpdesk_form_to_model.md**
- **docs/loan_form_to_model.md**

---

## 1. TUJUAN DOKUMEN (Purpose)

SDD ini menghuraikan rekabentuk teknikal ICTServe sebagai sistem dalaman (internal-only). Ia memperincikan seni bina, modul, komponen data, dan aliran kerja bagi memastikan portal dalaman Helpdesk & Asset Loan berfungsi dengan autentikasi staf, manakala operasi pentadbiran dikawal oleh panel Filament v4.

---

## 2. SKOP REKABENTUK (Design Scope)

Skop merangkumi:

- Portal dalaman (Laravel Blade + Livewire v3, Volt).
- Backend servis (Laravel 12) untuk pengesahan input, notifikasi, audit, dan layanan kelulusan.
- Panel Filament v4 untuk `admin` dan `superuser`.
- Penyimpanan data (MySQL, S3/MinIO), queue, dan integrasi e-mel/SMS.

Di luar skop:

- Portal awam dan interaksi tetamu.
- Aplikasi mudah alih natif (boleh diambil masa hadapan melalui API).

---

## 3. SENIBINA SISTEM (System Architecture)

### 3.1. Architectural Pattern: MVC + Service Layer

- **Presentation:** Blade + Livewire (authenticated), Filament (admin).
- **Application:** Controller / Livewire components memanggil servis domain (`HelpdeskService`, `LoanService`, `ApprovalService`).
- **Domain:** Model Eloquent, event, policy.
- **Infrastructure:** Queue (Redis), Mail, SMS Gateway, Storage, Audit.

### 3.2. Layered Components

| Lapisan | Komponen | Nota |
|---------|----------|------|
| Presentation | Livewire components (`helpdesk.ticket-form`, `loan.application-form`), authenticated pages, Filament resources | Mematuhi D12–D14 |
| Service | `HelpdeskService`, `LoanService`, `ApprovalService`, `NotificationService` | Mengandungi logik domain & integrasi |
| Persistence | Eloquent models (`HelpdeskTicket`, `LoanApplication`, `LoanApproval`, `User`) | Menyimpan data pengguna dalaman & pentadbir |
| Infrastructure | Queue jobs, mail templates, SMS client, storage adapter | Diurus melalui dependency injection |
| Security | Middleware (`auth`, `verified`, `signed`), rate limiter, audit middleware | Menjamin integriti |

### 3.3. Deployment Diagram

- **Frontend & Backend**: Laravel monolith (Nginx/PHP-FPM).
- **Queue**: Redis + Supervisor.
- **Database**: MySQL 8.
- **Storage**: MinIO/S3 (lampiran tetamu), lokal (sementara).
- **Monitoring**: Prometheus/Grafana, Sentry (opsyen).
- **Security**: WAF, HTTPS, reCAPTCHA Enterprise.

---

## 4. REKABENTUK MODUL (Module Design)

### 4.1. Helpdesk Ticketing (Internal)

**Komponen Utama**

- `resources/views/helpdesk/create.blade.php` (layout diautentikasi + Livewire).
- `app/Livewire/Helpdesk/TicketForm.php` – logik pemprosesan borang.
- `app/Services/Helpdesk/HelpdeskService.php` – penjanaan tiket, pengurusan lampiran, notifikasi.
- `app/Models/HelpdeskTicket.php` – model + relationships (`comments`, `attachments`).
- `app/Filament/Resources/HelpdeskTicketResource.php` – paparan pentadbiran.

**Aliran Kerja**

1. Tetamu mengakses borang. Livewire memuatkan senarai bahagian, kategori, gred.
2. Input disahkan (server & client). Lampiran dimuat naik ke storan sementara.
3. `HelpdeskService::createTicket()` menyimpan rekod, memindahkan lampiran ke S3/MinIO, dan menjana token status.
4. Notifikasi e-mel dihantar kepada tetamu & `admin`; job queue digunakan.
5. `admin` mengurus tiket melalui Filament (status, tugasan, komen). `superuser` mempunyai akses read-only + audit.

**Pertimbangan Rekabentuk**

- Tiada `Auth::user()` dipanggil; semua input tetamu disimpan sebagai metadata.
- Rate limiter `throttle:guest` menghalang spam.
- Templat e-mel mematuhi WCAG (teks + HTML, kontras tinggi).

### 4.2. ICT Asset Loan (Guest + Email Approval)

**Komponen Utama**

- `resources/views/loan/create.blade.php` + `app/Livewire/Loan/ApplicationForm.php`.
- `app/Services/Loan/LoanService.php` – logik permohonan & pengurusan aset.
- `app/Services/Loan/ApprovalService.php` – menjana token kelulusan, menghantar e-mel, memproses keputusan.
- `app/Models/LoanApplication.php`, `LoanItem`, `LoanTransaction`, `LoanApproval`.
- `app/Filament/Resources/LoanApplicationResource.php`.

**Aliran Kerja**

1. Tetamu mengisi borang; sistem memeriksa stok & konflik jadual.
2. `LoanService::createApplication()` menyimpan permohonan dengan status `PENDING_SUPERVISOR_APPROVAL`.
3. `ApprovalService` mencari e-mel ketua bahagian (konfigurasi) atau menggunakan e-mel yang dimasukkan tetamu.
4. Token bertanda tangan dijana (`SignedUrl + hashed token`), e-mel dihantar melalui queue.
5. Pegawai klik pautan; `ApprovalController` memaparkan ringkasan (guest layout). Keputusan direkod.
6. `LoanService::progressWorkflow()` mengemaskini status, menjana `loan_transactions`, menjadualkan peringatan.

**Pertimbangan Rekabentuk**

- Pautan kelulusan menggunakan `signedRoute` + `loan_approvals.token_hash`.
- Token sah selama 72 jam; `superuser` boleh menjana semula.
- Semua catatan kelulusan disimpan dalam `loan_audits`.

### 4.3. Inventory Management (Backend Admin)

- Filament resource untuk `Asset` dan `LoanTransaction`.
- `admin` mengurus katalog aset; `superuser` meluluskan perubahan kritikal.
- Fitur audit: setiap perubahan aset menghasilkan rekod `activity_log`.

### 4.5. Reporting & Dashboard

- `app/Filament/Widgets/*` menyediakan metrik SLA, backlog, penggunaan aset.
- Widget menggunakan query builder, caching 15 min untuk mengurangkan beban.

### 4.6. Audit Trail

- `spatie/laravel-activitylog` dipasang.
- Trait `LogsActivity` pada model utama (`LoanApplication`, `HelpdeskTicket`, `Asset`).
- Log disalurkan ke SIEM melalui `AuditExportJob`.

---

## 5. REKABENTUK PANGKALAN DATA (Database Design)

### 5.1. Entity-Relationship Diagram (ERD) — High Level

Lihat D09 §3 untuk ERD lengkap. Kemaskini utama:

- `users` hanya mengandungi `admin`/`superuser`.
- `helpdesk_tickets` mempunyai medan `submitter_*`.
- `loan_approvals` menyimpan `approver_email`, `approver_grade`, `token_hash`, `decision`, `decision_at`.
- `status_tokens` (opsyen) menyimpan token untuk semakan status tetamu.

### 5.2. Database Fields

Rujuk D09 §4 untuk definisi terperinci. Perubahan utama:

- Tiada `users.locale`; keutamaan bahasa disimpan dalam cookie & sesi.
- Indeks baharu pada `loan_approvals.token_hash` dan `helpdesk_tickets.ticket_number`.
- `loan_transactions` menambah `handover_by_admin_id`, `received_by_admin_id`.

---

## 6. REKABENTUK ANTARA MUKA (Interface Design)

### 6.1. Web UI

- Layout `guest.blade.php` dengan `header`, `main`, `footer`, `nav`.
- Komponen Livewire mematuhi D12-D14 (warna, tipografi, spacing).
- Language switcher memanipulasi cookie & sesi (rujuk D15) tanpa profil pengguna.
- Halaman status tetamu: memaparkan garis masa permohonan/tiket, status semasa, tarikh penting.

### 6.2. User Experience (UX)

- Wizard forms (Helpdesk, Loan) dengan indikator langkah.
- Pemberitahuan inline (toast) mematuhi ARIA (`role="status"`, `aria-live="polite"`).
- Peringatan tarikh memaparkan ringkasan (contoh: "Serahan aset: 12 Nov 2025, jam 9.00 pagi").

---

## 8. REKABENTUK KESELAMATAN (Security Design)

- **Lapisan Pertahanan:**
  - CSRF token untuk semua borang.
  - `throttle:guest` middleware 60/min.
  - reCAPTCHA Enterprise.
  - Signed URL + hash token untuk kelulusan/status.
  - Sesi tetamu terhad (tiada penyimpanan maklumat peribadi selain status token).
- **Pengurusan Peranan:**
  - `users.role` = `admin` atau `superuser`.
  - Policy & gate menegakkan perbezaan fungsi.
- **Audit & Logging:**
  - `activity_log` + `loan_audits` + `auth_log_events` (Filament).
  - Export berkala ke SIEM.
- **Perlindungan Data:**
  - Encryption (Laravel `encrypt`) untuk e-mel, telefon.
  - Lampiran disimpan di storan objek dengan presigned URL (expiration 15 min).
  - Sanitasi fail (ClamAV).
- **Ketahanan:** Backup, failover DB, fallback queue.

---

## 9. REKABENTUK PENYENGGARAAN & PEMANTAUAN (Maintenance & Monitoring Design)

- **Pemantauan:** Prometheus mengumpul metrik (trafik, masa tindak, ralat). Grafana memaparkan dashboard. Alertmanager menghantar amaran ke Ops BPM.
- **Logging:** Laravel log → ELK stack. Tingkatan log: `info` untuk operasi biasa, `warning` untuk SLA, `error` untuk ralat.
- **Pemantauan Prestasi:** Lighthouse CI, k6 (ujian beban) dijalankan berkala.
- **Pengurusan Konfigurasi:** `.env` – tetapan e-mel, SMS, storage. `superuser` boleh mengemas kini templat e-mel melalui Filament.
- **Penyenggaraan Berkala:** Semakan modul queue, audit storage lampiran, putaran token kelulusan.

---

## 10. REKABENTUK UJIAN (Testing Design)

- **Unit & Feature Tests:** PHPUnit + Pest menguji servis (`HelpdeskServiceTest`, `LoanApprovalTest`, `ApprovalLinkSignatureTest`).
- **Livewire Tests:** Memastikan borang tetamu memaparkan validasi, status, lampiran.
- **Browser Tests:** Laravel Dusk / Cypress untuk aliran tetamu & kelulusan e-mel.
- **Accessibility Tests:** axe-core, Lighthouse, manual (NVDA, VoiceOver) – rujuk D12 §8.
- **Performance Tests:** k6/Artillery (opsyen) untuk ujian kapasiti.
- **Security Tests:** OWASP ZAP, `php artisan security:scan` (opsyen), semakan manual token.

---

## 11. REKABENTUK PENGOPTIMUMAN (Optimization Design)

- **Prestasi Frontend:** Lazy-loading gambar, `fetchpriority="high"` pada hero, `loading="lazy"` untuk lampiran, minifikasi CSS/JS melalui Vite.
- **Prestasi Backend:** Caching (config, route, view), caching query Filament (15 min), queue memproses e-mel/SMS.
- **Data:** Penggunaan indeks, partitioning audit (opsyen).
- **Asset:** Kompresi Brotli/ gzip, HTTP/2, CDN (opsyen).

---

## 12. LAMPIRAN (Appendices)

### 12.1. Diagram Senibina Sistem

- Diagram konteks, komponen, dan deployment (rujuk `design/architecture/` repo – akan dikemas kini).

### 12.2. Checklist Verifikasi Reka Bentuk

- Menggunakan `docs/frontend/d00-d15-standards-compliance-checker.md`.
- Semak item: Guest-only flow, SAL tokens, WCAG, Filament roles, audit log.

---

## 13. PENUTUP

Rekabentuk ini memastikan ICTServe mematuhi mandat guest-first: interaksi awam melalui borang tetamu yang mematuhi WCAG dan prestasi tinggi, sementara pemprosesan, kelulusan, dan kawalan kekal di tangan `admin` dan `superuser`. Penerapan modul yang dihurai di sini perlu disahkan melalui ujian berterusan dan pemantauan bagi memastikan keberkesanan operasi BPM.
