# Ringkasan Sistem (System Overview)

**Sistem ICTServe**  
**Versi:** 3.0.0 (SemVer)  
**Tarikh Kemaskini:** 31 Oktober 2025  
**Status:** Aktif  
**Klasifikasi:** Terhad - Dalaman BPM MOTAC  
**Penulis:** Pasukan Pembangunan BPM MOTAC  
**Standard Rujukan:** ISO/IEC/IEEE 15288, ISO/IEC/IEEE 12207, WCAG 2.2 AA, MyGOV Digital Service Standards v2.1.0

---

## Maklumat Dokumen (Document Information)

| Atribut            | Nilai                                                                                       |
|--------------------|---------------------------------------------------------------------------------------------|
| **Versi**          | 3.0.0                                                                                       |
| **Tarikh Kemaskini** | 31 Oktober 2025                                                                           |
| **Status**         | Aktif                                                                                       |
| **Klasifikasi**    | Terhad - Dalaman BPM MOTAC                                                                  |
| **Pematuhi**       | ISO/IEC/IEEE 15288, ISO/IEC/IEEE 12207, WCAG 2.2 AA, MyGOV Digital Service Standards v2.1.0 |
| **Bahasa**         | Bahasa Melayu (utama), English (teknikal)                                                   |

> Notis Penggunaan Dalaman: Sistem ini adalah untuk kegunaan warga kerja MOTAC (staf dan pegawai gred) sahaja dan tidak dibuka kepada orang awam (internal use only).

---

## Sejarah Perubahan (Changelog)

| Versi  | Tarikh          | Perubahan                                                                                                                                                                                                       | Penulis                 |
|--------|-----------------|------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------|-------------------------|
| 3.0.0  | 31 Oktober 2025 | Peralihan penuh kepada seni bina dalaman (internal-only): portal staf MOTAC berasaskan Laravel 12 dengan Login (Breeze/Jetstream), kelulusan dalam sistem (role-based), Filament v4 untuk pentadbiran, dan pematuhan WCAG 2.2 AA. Rujukan silang D02, D03, D04, D09, D11, D12–D14, `helpdesk_form_to_model.md`, `loan_form_to_model.md`. | Pasukan Pembangunan BPM |
| 2.0.0  | 17 Oktober 2025 | Penyeragaman mengikut D00-D14, SemVer, cross-reference                                                                                                                                                           | Pasukan BPM             |
| 1.0.0  | September 2025  | Versi awal dokumentasi sistem                                                                                                                                                                                   | Pasukan BPM             |

---

## Rujukan Dokumen Berkaitan (Related Document References)

- **[D01_SYSTEM_DEVELOPMENT_PLAN.md]**
- **[D02_BUSINESS_REQUIREMENTS_SPECIFICATION.md]**
- **[D03_SOFTWARE_REQUIREMENTS_SPECIFICATION.md]**
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
- **docs/helpdesk_form_to_model.md**
- **docs/loan_form_to_model.md**
- **docs/frontend/accessibility-guidelines.md**
- **docs/frontend/color-contrast-accessibility.md**
- **docs/frontend/core-web-vitals-testing-guide.md**
- **docs/performance-optimization-report.md**
- **docs/frontend/filament-admin-interface-compliance.md**

---

## Ringkasan Eksekutif (Executive Summary)

ICTServe beroperasi sebagai platform dalaman (internal-only) untuk warga kerja MOTAC. Akses adalah melalui portal intranet dengan pengesahan (Login) dan kawalan berasaskan peranan (RBAC) bagi staf, pegawai gred, pentadbir, dan penyelia. Modul utama ialah Helpdesk (aduan ICT) dan Pinjaman Aset ICT; kedua-duanya berjalan dalam ekosistem Laravel 12 + Livewire v3 + Filament v4 dengan jejak audit (audit trail), pematuhan aksesibiliti WCAG 2.2 AA, dan standard keselamatan dalaman.

Ekosistem ini digerakkan oleh Laravel 12 + Livewire v3, memanfaatkan audit trail menyeluruh (D09), automasi notifikasi berasaskan queue, dan garis panduan aksesibiliti & prestasi terkini daripada pakej dokumen pematuhan v2.1.0.

---

## 1. Modul Helpdesk ICT (Pengguna Dalaman)

Modul helpdesk digunakan oleh staf MOTAC melalui portal dalaman untuk mencipta dan menjejak tiket aduan ICT.

### 1.1. Fungsi Utama Helpdesk

- **Borang Dalaman WCAG 2.2 AA**  
  Livewire v3 mengekalkan borang bertahap (progressive disclosure) dengan pemeriksaan masa nyata, sasaran sentuh yang sesuai, dan fokus visual mengikut D12–D14.
- **Profil Pengguna Dalaman**  
  Akaun pengguna (user_id) digunakan; e-mel, nombor telefon, bahagian, dan gred jawatan diambil daripada profil pengguna (rujuk `helpdesk_form_to_model.md`).
- **Lampiran & Bukti**  
  Sehingga 5 fail (gambar, PDF) disokong, dengan penukaran automatik kepada WebP apabila sesuai (rujuk `image-optimization-implementation.md`).
- **Notifikasi E-mel Automatik**  
  Tetamu menerima pengesahan, manakala `admin` menerima ping melalui queue. Status tiket diterjemahkan kepada e-mel triage & SLA (rujuk D11 §6).
- **Laluan Penyelesaian (Resolution Paths)**  
  Tugas ditugaskan kepada `admin` melalui Filament. `superuser` memantau SLA, audit, dan eskalasi.
- **Dashboard Operasi Filament**  
  Hanya boleh diakses oleh `admin` & `superuser`. Paparan menyokong laporan SLA, ringkasan backlog, dan rekod audit.

### 1.2. Manfaat untuk BPM

- **Tanggungjawab Berlapik**  
  Audit trail menyimpan identiti pengguna dalaman, masa tindakan, dan catatan dalaman.
- **Saluran Tunggal**  
  Semua aduan ditapis melalui borang dalaman; tiada lagi tiket manual.
- **Prestasi Boleh Ukur**  
  Panel Filament menyediakan metrik SLA, kekerapan kategori, dan purata masa pemulihan.

---

## 2. Modul Peminjaman Aset ICT (Pengguna Dalaman)

Modul peminjaman mengurus permohonan aset oleh pengguna dalaman dan pemegang kuasa kelulusan mengikut peranan.

### 2.1. Fungsi Utama Asset Loan

- **Borang Permohonan Dalaman**  
  Pengguna memilih aset, tarikh, lokasi, dan tujuan. Validasi stok dan konflik tarikh dilakukan masa nyata (rujuk `loan_form_to_model.md`).
- **Kelulusan Melalui Pautan E-mel**  
  Sistem menjana permintaan kelulusan untuk Ketua Bahagian (≥ Gred 41) menggunakan token bertanda masa. Pengesahan dibuat melalui klik pautan yang mengesahkan e-mel, gred, dan keputusan (APPROVE / REJECT) tanpa log masuk.
- **Pengurusan Kitaran Hidup Aset**  
  `admin` merekod pengeluaran, pemulangan, kerosakan, dan audit menggunakan modul Filament. `superuser` menyelaras audit berkala.
- **Notifikasi & Peringatan**  
  Penyewaan menghampiri tarikh tamat memicu e-mel & SMS (gateway MCMC) kepada peminjam tetamu, dengan salinan kepada `admin`.
- **Rekod Automatik & Audit**  
  Setiap keputusan kelulusan, ubah status aset, dan catatan pulangan dicap masa dan disimpan dalam `loan_transactions` + `loan_audits`.

### 2.2. Manfaat untuk BPM

- **Ketelusan Kelulusan**  
  Rantaian kelulusan dapat ditelusuri tanpa memerlukan akaun serantau.
- **Penguatkuasaan Polisi**  
  Polisi gred, tempoh, dan catuan aset dikuatkuasa oleh peraturan backend.
- **Analitik Aset**  
  Laporan penggunaan aset, kadar kerosakan, dan backlog permohonan disediakan dalam Filament.

---

## 3. Integrasi Kedua Modul (Module Integration)

### 3.1. Integrasi Antara Helpdesk & Asset Loan

- **Konteks Aset dalam Tiket**  
  Laporan kerosakan bagi aset yang sedang dipinjam akan mengaitkan tiket dengan entri `loan_transactions` semasa untuk tindakan segera.
- **Pemantauan SLA**  
  Kemas kini pemulangan aset boleh mencetuskan tiket penyelenggaraan automatik jika kerosakan dilaporkan.
- **Analitik Gabungan**  
  `superuser` mengakses papan pemuka yang menggabungkan data tiket dan pinjaman untuk analisa trend (contoh, aset dengan kadar kerosakan tinggi).

---

## 4. Aspek Teknikal (Technical Aspects)

### 4.1. Senibina Sistem (System Architecture)

- **Frontend Tetamu**  
  Laravel 12 + Livewire v3 + Volt dengan layout `resources/views/layouts/guest.blade.php`. Tiada modul log masuk awam; penyimpanan status menggunakan Session + Cookie.
- **Backend Filament v4**  
  Panel pentadbiran tunggal (`/admin`) dengan SSO larangan; hanya `admin` & `superuser` (rujuk D11 §2). Spatie permissions kini memetakan dua peranan sahaja.
- **Servis Notifikasi & Kelulusan**  
  Queue Laravel mengendalikan e-mel, SMS (melalui gateway BPM), dan pautan kelulusan bertanda tangan (JWT + hashed token). Lihat D04 §4.2 serta D11 §6.
- **Audit & Logging**  
  `spatie/laravel-activitylog` merekod tindakan backend; tetamu dicap menggunakan metadata yang dihantar dari borang.
- **Keselamatan**  
  CSRF untuk borang tetamu, rate limiting, reCAPTCHA Enterprise (mode invisible) untuk mencegah spam, dan sanitasi input ketat bagi lampiran.

### 4.2. Database Design

- **`users`**  
  Menyimpan akaun `admin` dan `superuser` sahaja. Medan `role` diset `admin` atau `superuser`. Tiada rekod bagi staf MOTAC kerana tetamu tidak mempunyai akaun.
- **`helpdesk_tickets`, `helpdesk_comments`, `helpdesk_attachments`**  
  Menyimpan data borang tetamu; medan `submitter_name`, `submitter_email`, `submitter_phone` menggantikan `user_id`.
- **`loan_applications`, `loan_items`, `loan_transactions`, `loan_approvals`**  
  Menyimpan permohonan, item, dan kelulusan e-mel. `loan_approvals` mengekalkan `approver_email`, `approver_grade`, `signed_token`, dan cap masa.
- **`loan_audits`, `audits`, `activity_log`**  
  Menyokong keperluan D09 untuk jejak audit.

---

## 5. Peranan BPM sebagai Pemilik Sistem (BPM as System Owner)

### 5.1. Tanggungjawab BPM

| Peranan       | Tanggungjawab                                                                                                                                                                    |
|---------------|----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------|
| **admin**     | Menjalankan triage tiket, mengurus inventori aset, memproses permohonan, memantau SLA harian, dan mengekalkan kandungan borang tetamu.                                          |
| **superuser** | Mentadbir konfigurasi sistem, pengurusan akaun pentadbir, menyemak audit & keselamatan, meluluskan konfigurasi modul, mengurus integrasi, dan bertindak sebagai pegawai pematuhan. |

---

## 6. Saluran Interaksi Awam

- **Borang Helpdesk (`/helpdesk`)**  
  Tetamu menghantar tiket; sistem balas dengan nombor rujukan dan e-mel PDF ringkas.
- **Borang Peminjaman (`/loan`)**  
  Tetamu menghantar permohonan; status dihantar melalui e-mel, manakala kelulusan disempurnakan melalui pautan khas.
- **Penjejakan Status**  
  Tetamu menggunakan URL status khas dengan token (tiada log masuk) untuk menyemak perkembangan tiket atau permohonan.

---

## 7. Modul Utama

| Modul                   | Deskripsi                                                                 | Peranan Backend                         |
|-------------------------|---------------------------------------------------------------------------|-----------------------------------------|
| Helpdesk Guest Form     | Borang aduan, pengurusan SLA, lampiran, e-mel tetamu                      | `admin` memproses tiket melalui Filament |
| Asset Loan Guest Form   | Borang pinjaman, kelulusan e-mel, rekod transaksi                         | `admin` mengurus permohonan & aset      |
| Filament Admin          | Dashboard operasi, laporan gabungan, pengurusan aset                      | `admin` & `superuser` sahaja            |
| Sistem Audit & Notifikasi | Queue e-mel/SMS, log audit, pemantauan                                   | `superuser` memantau, `admin` bertindak  |

---

## 8. Konteks MOTAC

- Menyokong inisiatif **Digital MOTAC 2025** dengan fokus perkhidmatan awam digital.
- Menggantikan sistem dalaman lama (intranet/Excel) dengan borang awam yang boleh diakses dari intranet & Internet.
- Mematuhi polisi PDPA, garis panduan MCMC untuk SMS OTP, dan keperluan Arkib Negara untuk rekod digital.

---

## 9. UI/UX & Aksesibiliti (User Interface & Accessibility)

### 9.1. Piawaian UI/UX

- Mematuhi D12-D14, `accessibility-guidelines.md`, dan `color-contrast-accessibility.md`.
- Palet baharu: Primary `#0056B3`, Secondary `#0B4D8F`, Success `#1B7C54`, Warning `#CC7700`, Danger `#B3002D`.
- Inline focus ring 3px warna `#0B4D8F`, jarak minimum 16px.
- Layout asas `guest.blade.php` menggunakan `aria` landmarks (`header`, `main`, `footer`, `nav`).
- Bahasa dwibahasa: pengesanan awal `Accept-Language`, fallback ke cookie `locale`, kemudian sesi (rujuk D15).
- Semua komponen diuji terhadap `accessibility-testing-checklist.md` dan pencapaian Lighthouse 90+ (rujuk `core-web-vitals-testing-guide.md`).

### 9.2. Ciri-ciri Utama

- Navigasi breadcrumb ringkas tanpa menu pengguna.
- Borang berbilang langkah dengan status indicator.
- Komponen tetingkap modal untuk status kelulusan (untuk tetamu) dengan tumpuan (focus trap) mematuhi ARIA.

---

## 10. Migrasi Data & Integrasi (Data Migration & Integration)

### 10.1. Integrasi Luaran (External Integration)

| Sistem              | Tujuan                                          | Integrasi                                  |
|---------------------|-------------------------------------------------|--------------------------------------------|
| SMTP / GOV Mail     | Penghantaran e-mel tetamu & pautan kelulusan    | Laravel queue + MOU BPM                    |
| SMS Gateway BPM     | Peringatan due date & OTP (opsyen)              | REST API (token service)                   |
| MyIdentity (Opsyen Masa Depan) | Pengesahan identiti pegawai Gred 41        | Belum diaktifkan; memerlukan MAMPU clearance |
| Tiada LDAP / SSO    | Not applicable                                  | Semua tetamu tanpa log masuk; admin Filament guna credential dalaman sahaja |

Migrasi data daripada sistem terdahulu melibatkan import rekod tiket & pinjaman ke jadual baharu dengan memetakan `staff_no` lama kepada metadata tetamu (rujuk D05 & D06). Tiada migrasi akaun pengguna.

---

## 11. Pematuhan Piawaian (Standards Compliance)

- **WCAG 2.2 AA** – Lihat `accessibility-guidelines.md` & `color-contrast-accessibility.md`.
- **Performance Optimisation** – `performance-optimization-report.md`, `core-web-vitals-testing-guide.md`.
- **Security & Audit** – D09 (audit trail), D11 §8 (security design).
- **Documentation Traceability** – D01 §9.3 memastikan semua perubahan direkod.
- **MyGOV Digital Service Standards v2.1.0** – Bukti pematuhan disimpan dalam `filament-admin-interface-compliance.md` & `css-js-optimization-audit.md`.

---

## 11a. Arsitektur Penempatan (Deployment Architecture)

### 11a.1. Infrastruktur Penempatan (Deployment Infrastructure)

- **Frontend**: Laravel served via Nginx/Apache, Vite build assets, HTTP/2, Brotli compression.
- **Backend**: PHP-FPM 8.2, Supervisor queue workers untuk notifikasi.
- **Database**: MySQL 8 dengan replikasi read-only (opsyen), backup automatik harian.
- **Object Storage**: MinIO/S3 untuk lampiran tetamu dengan polisi retention.

### 11a.2. Keselamatan Penempatan (Deployment Security)

- Enforce HTTPS + HSTS.
- WAF menapis trafik robot/spam ke borang tetamu.
- Secrets diurus melalui `.env` & Azure Key Vault (perancangan).
- Audit log disalurkan ke SIEM BPM setiap 15 minit.

---

## 12. Glosari & Rujukan (Glossary & References)

### 12.1. Istilah Utama

| Istilah               | Takrif                                                                                             |
|-----------------------|----------------------------------------------------------------------------------------------------|
| **Tetamu (Guest)**    | Pengguna awam yang mengisi borang tanpa log masuk.                                                 |
| **Admin**             | Pegawai BPM yang memproses tiket & permohonan melalui Filament.                                    |
| **Superuser**         | Pegawai pengurusan BPM yang mentadbir konfigurasi, keselamatan, dan audit.                         |
| **Signed Approval Link** | Pautan e-mel ber-token yang membolehkan kelulusan tanpa log masuk.                              |
| **guest.blade.php**   | Layout utama untuk semua paparan tetamu.                                                           |

---

## Kesimpulan (Conclusion)

Peralihan kepada seni bina guest-first memastikan ICTServe memenuhi mandat BPM untuk menyediakan perkhidmatan digital yang boleh diakses umum sambil mengekalkan kawalan ketat di peringkat pentadbiran. Semua dokumen D00-D15, garis panduan aksesibiliti, dan laporan prestasi kini selaras dengan realiti baharu ini: tiada akaun staf, borang tetamu sebagai pintu masuk, dan pentadbiran terhad kepada `admin` dan `superuser` sahaja.
