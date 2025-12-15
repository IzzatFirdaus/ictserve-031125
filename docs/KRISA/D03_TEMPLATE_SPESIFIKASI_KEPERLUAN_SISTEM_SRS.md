# DOKUMEN SPESIFIKASI KEPERLUAN SISTEM (SRS)

## ICTSERVE
(Sistem Pengurusan Helpdesk & Pinjaman Aset ICT)

![Logo Agensi](../public/images/motac-logo.png)

| Medan                 | Nilai                                            |
| --------------------- | ------------------------------------------------ |
| **NAMA AGENSI**       | : Bahagian Pengurusan Maklumat (BPM), MOTAC      |
| **NAMA AGENSI INDUK** | : Kementerian Pelancongan, Seni dan Budaya Malaysia |
| **TARIKH DOKUMEN**    | : 15 Disember 2025                               |
| **VERSI DOKUMEN**     | : 3.6.1                                          |

---

## i. Keterangan Dokumen

Dokumen ini mendefinisikan keperluan perisian terperinci untuk ICTServe sebagai sistem dalaman (internal-only) untuk warga kerja MOTAC. Ia meliputi keperluan fungsional, antara muka, data, keselamatan, dan kebolehcapaian untuk memastikan modul Helpdesk & Asset Loan beroperasi dengan log masuk pengguna dalaman dan kawalan pentadbiran.

---

## ii. Semakan dan Pengesahan Dokumen

Seksyen ini adalah ruangan bagi pegawai-pegawai yang bertanggungjawab untuk melakukan semakan dan pengesahan kepada maklumat-maklumat yang terkandung di dalam dokumen ini.

### SEMAKAN DOKUMEN

| Disemak Oleh | Jawatan | Tandatangan | Tarikh Semakan |
|--------------|---------|-------------|----------------|
|              |         |             |                |
|              |         |             |                |

### PENGESAHAN DOKUMEN

| Disahkan Oleh | Jawatan | Tandatangan | Tarikh Pengesahan |
|---------------|---------|-------------|-------------------|
|               |         |             |                   |
|               |         |             |                   |

---

## iii. Kawalan Dokumen

### KAWALAN DOKUMEN

| No. Versi | Tarikh            | Ringkasan Pindaan                                                                                                                                                                                                                                   | Penyedia                |
| --------- | ----------------- | --------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------- | ----------------------- |
| 3.6.1     | 14 Disember 2025  | Kemaskini Cloud Hybrid AI Integration (Ollama + AWS Bedrock), modul Asset Management, dan Laporan & Analitik.                                                                                                                                       | Pasukan Pembangunan BPM |
| 3.6.0     | 8 Disember 2025   | Bahasa Melayu sahaja untuk antara muka. Penyelarasan dengan D00-D17 v3.6.0.                                                                                                                                                                         | Pasukan Pembangunan BPM |
| 3.5.0     | 30 November 2025  | True Hybrid Architecture: Self-registration, flexible login, dual audit system, Laravel Telescope, multi-channel notifications.                                                                                                                     | Pasukan Pembangunan BPM |
| 3.4.0     | 29 November 2025  | Hybrid Architecture: Staff log masuk (Laravel Breeze) atau borang tetamu. Tambah SRS-AUTH-001, SRS-DATA-001.                                                                                                                                        | Pasukan Pembangunan BPM |
| 3.2.0     | 29 November 2025  | Kemaskini dokumentasi sistem: pengesahan versi teknologi semasa (Laravel 12, PHP 8.2, Livewire 3, Filament 4).                                                                                                                                      | Pasukan Pembangunan BPM |
| 3.1.0     | 29 November 2025  | Kemaskini teknologi: Laravel 12, Livewire 3, Filament 4, Volt, Alpine.js 3, Tailwind CSS 4, Laravel Reverb. Pematuhan WCAG 2.2 AA.                                                                                                                  | Pasukan Pembangunan BPM |
| 3.0.0     | 31 Oktober 2025   | Penjajaran penuh kepada seni bina dalaman, autentikasi pengguna staf, kelulusan berperingkat, dan pematuhan WCAG 2.2 AA.                                                                                                                            | Pasukan Pembangunan BPM |
| 1.0.0     | September 2025    | Versi awal SRS.                                                                                                                                                                                                                                     | Pasukan BPM             |

---

## iv. Kandungan

- i. Keterangan Dokumen
- ii. Semakan dan Pengesahan Dokumen
- iii. Kawalan Dokumen
- iv. Kandungan
- v. Senarai Gambarajah
- vi. Senarai Jadual
- vii. Definisi dan Akronim
- viii. Sumber Rujukan
- 1. PENGENALAN
- 2. KEPERLUAN SISTEM
- 3. SPESIFIKASI KEPERLUAN FUNGSI
- 4. SPESIFIKASI KEPERLUAN BUKAN FUNGSI
- 5. KEPERLUAN ANTARA MUKA
- 6. KEKANGAN SISTEM
- 7. LAMPIRAN

---

## v. Senarai Gambarajah

*(Rujuk Dokumen D04 Software Design Document untuk gambarajah seni bina terperinci)*

---

## vi. Senarai Jadual

*(Senarai jadual dijana secara automatik berdasarkan kandungan dokumen)*

---

## vii. Definisi dan Akronim

### a. Akronim

| Akronim | Keterangan                                      |
| ------- | ----------------------------------------------- |
| BPM     | Bahagian Pengurusan Maklumat                    |
| MOTAC   | Kementerian Pelancongan, Seni dan Budaya        |
| SRS     | Software Requirements Specification             |
| SLA     | Service Level Agreement                         |
| WCAG    | Web Content Accessibility Guidelines            |
| ASVS    | Application Security Verification Standard      |
| SAL     | Signed Approval Link                            |
| OTP     | One-Time Password                               |
| SSO     | Single Sign-On                                  |
| AI      | Artificial Intelligence                         |
| LLM     | Large Language Model                            |
| RAG     | Retrieval-Augmented Generation                  |

### b. Definisi

| Terma/Istilah           | Definisi                                                                                                                   |
| ----------------------- | -------------------------------------------------------------------------------------------------------------------------- |
| **Hybrid Access Model** | Model dwi-akses di mana staf boleh log masuk untuk fungsi penuh atau menggunakan borang tetamu untuk akses pantas.         |
| **Authenticated Staff** | Staf yang log masuk melalui Laravel Breeze untuk akses My Dashboard dan sejarah permohonan.                                |
| **Guest/Quick Access**  | Penggunaan borang intranet tanpa log masuk, dikesan melalui Token dan input manual.                                        |
| **Admin**               | Pegawai BPM yang memproses tiket dan permohonan melalui panel Filament.                                                    |
| **Superuser**           | Pegawai BPM yang mentadbir konfigurasi sistem, integrasi, dan audit.                                                       |
| **Ollama**              | Pelayan LLM sumber terbuka untuk menjalankan model AI secara tempatan (on-premise).                                        |

---

## viii. Sumber Rujukan

1. D00_SYSTEM_OVERVIEW.md (v3.5.0)
2. D02_BUSINESS_REQUIREMENTS_SPECIFICATION.md (v3.5.0)
3. D04_SOFTWARE_DESIGN_DOCUMENT.md (v3.5.0)
4. D09_DATABASE_DOCUMENTATION.md (v3.5.0)
5. D11_TECHNICAL_DESIGN_DOCUMENTATION.md
6. D12_UI_UX_DESIGN_GUIDE.md (v3.5.0)
7. D15_LANGUAGE_MS_EN.md (v3.6.0)
8. D18_AI_CHATBOT_OLLAMA_BEDROCK.md (v1.0.0)
9. ISO/IEC/IEEE 29148:2018 Systems and software engineering — Life cycle processes — Requirements engineering
10. MyGOV Digital Service Standards v2.1.0

---

## 1. PENGENALAN

### 1.1 Tujuan Sistem

Tujuan pembangunan sistem ICTServe adalah untuk menyediakan platform pengurusan perkhidmatan ICT yang bersepadu bagi warga kerja MOTAC. Sistem ini bertujuan untuk meningkatkan kecekapan pengurusan aduan (Helpdesk) dan pinjaman aset ICT (Asset Loan) melalui automasi aliran kerja, notifikasi masa nyata, dan integrasi AI hibrid.

### 1.2 Skop Sistem

Skop sistem merangkumi:

1. **Modul Helpdesk**: Pengurusan tiket aduan kerosakan ICT.
2. **Modul Pinjaman Aset**: Pengurusan permohonan dan kelulusan pinjaman aset ICT.
3. **Portal Pentadbiran**: Panel kawalan untuk Admin dan Superuser menguruskan tiket, aset, dan laporan.
4. **Integrasi AI**: Chatbot FAQ, auto-reply, dan analisis dokumen menggunakan Ollama dan AWS Bedrock.
5. **Aplikasi Hibrid**: Akses melalui log masuk staf atau borang tetamu pantas.

### 1.3 Gambaran Keseluruhan Sistem

ICTServe dibangunkan berasaskan seni bina hibrid menggunakan Laravel 12 sebagai kerangka backend dan Livewire/Volt untuk frontend interaktif. Sistem ini menggunakan pangkalan data MySQL untuk penyimpanan data berstruktur dan Redis untuk pengurusan barisan (queue) serta komunikasi masa nyata (Reverb). Panel pentadbiran dibina menggunakan Filament 4.

### 1.4 Senarai Pemegang Taruh

| Pemegang Taruh | Peranan/Tanggungjawab                                      | Kepentingan                                                                 |
| -------------- | ---------------------------------------------------------- | --------------------------------------------------------------------------- |
| **Warga MOTAC**| Pengguna Akhir (Staf)                                      | Mengemukakan aduan dan memohon pinjaman aset dengan mudah dan pantas.       |
| **Admin BPM**  | Pengendali Sistem                                          | Memproses tiket dan permohonan, mengurus inventori aset.                    |
| **Superuser**  | Pentadbir Teknikal                                         | Mengurus konfigurasi sistem, audit, dan integrasi teknikal.                 |
| **Pengurusan** | Pembuat Keputusan                                          | Memantau prestasi perkhidmatan ICT melalui laporan dan papan pemuka.        |

---

## 2. KEPERLUAN SISTEM

### 2.1 Keperluan Fungsi

Sistem mesti menyediakan fungsi utama berikut:

1. Pengurusan Tiket Helpdesk (Daftar, Kemaskini, Tutup).
2. Pengurusan Pinjaman Aset (Mohon, Lulus/Tolak, Serah/Pulang).
3. Pengurusan Inventori Aset (Daftar, Selenggara, Lupus).
4. Laporan dan Analitik (Statistik Tiket, SLA, Penggunaan Aset).
5. Bantuan AI (Chatbot, Analisis Dokumen).

#### 2.1.1 Keperluan Fungsi Utama (Ringkasan)

| ID Keperluan | Keterangan Keperluan                                      | Keutamaan |
| ------------ | --------------------------------------------------------- | --------- |
| SRS-HELP-001 | Borang Aduan Hybrid (Guest & Auth)                        | Tinggi    |
| SRS-LOAN-001 | Borang Permohonan Pinjaman Hybrid                         | Tinggi    |
| SRS-ADM-001  | Portal Pentadbiran (Filament)                             | Tinggi    |
| SRS-AI-001   | Chatbot FAQ AI (Hybrid Ollama/Bedrock)                    | Sederhana |
| SRS-RPT-001  | Penjanaan Laporan Berkala                                 | Sederhana |

### 2.2 Keperluan Bukan Fungsi

Sistem mesti mematuhi piawaian prestasi, keselamatan, dan kebolehgunaan yang ditetapkan.

#### 2.2.1 Keperluan Prestasi

| ID Keperluan | Keterangan Keperluan                                      | Metrik | Sasaran |
| ------------ | --------------------------------------------------------- | ------ | ------- |
| SRS-PERF-001 | Masa tindak balas borang tetamu                           | TTFB   | < 500ms |
| SRS-PERF-002 | Masa memuat panel pentadbiran                             | LCP    | < 3s    |

#### 2.2.2 Keperluan Keselamatan

| ID Keperluan | Keterangan Keperluan                                      | Tahap Keselamatan |
| ------------ | --------------------------------------------------------- | ----------------- |
| SRS-SEC-001  | Pematuhan OWASP ASVS L2                                   | Tinggi            |
| SRS-SEC-002  | Penyulitan data sensitif (AES-256)                        | Tinggi            |

### 2.3 Keperluan Antara Muka

Antara muka pengguna mesti responsif, dwibahasa (Bahasa Melayu utama), dan mematuhi WCAG 2.2 AA.

### 2.4 Keperluan Prestasi

Sistem mesti mampu menampung beban pengguna serentak dan memproses barisan kerja (queue) dalam masa yang ditetapkan.

### 2.5 Keperluan Keselamatan

Kawalan akses berasaskan peranan (RBAC), pengesahan dua faktor (2FA) untuk Superuser, dan audit trail yang lengkap.

---

## 3. SPESIFIKASI KEPERLUAN FUNGSI

### 3.1 Penggunaan Notasi

| Notasi       | Keterangan                                      |
| ------------ | ----------------------------------------------- |
| SRS-XXX-###  | ID Unik Keperluan (Contoh: SRS-HELP-001)        |

### 3.2 Model Keperluan Fungsi

*(Rujuk D04 Software Design Document untuk model terperinci)*

### 3.3 Senarai Keperluan Fungsi Terperinci

#### 3.3.1 Modul Helpdesk Ticketing

| ID           | Keperluan               | Perincian                                                                                                                                                                                              |
| ------------ | ----------------------- | ------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------ |
| SRS-HELP-001 | Borang Hybrid           | Staff boleh mengisi borang Bahasa Melayu sebagai tetamu ATAU authenticated user. Medan wajib: nama, e-mel, telefon, bahagian, gred, kategori, deskripsi, lampiran, perakuan PDPA.                      |
| SRS-AUTH-001 | Dual Entry Model        | Staff boleh log masuk (Laravel Breeze) untuk My Dashboard ATAU gunakan borang tetamu. Sistem mengesan Auth::check() untuk auto-fill dan pautan user_id.                                                |
| SRS-DATA-001 | Hybrid Data Association | Jika Auth::check() === true, pautkan penyerahan ke user_id (nullable FK). Jika false, user_id=NULL, perlukan medan penyerah manual. Notifikasi e-mel dihantar ke e-mel penyerah tanpa mengira status.  |
| SRS-FORM-001 | Auto-fill Data          | Jika staff log masuk, borang auto-fill nama, e-mel, telefon, bahagian, gred dari profil pengguna. Jika tetamu, perlukan input manual.                                                                  |
| SRS-HELP-002 | Validasi Masa Nyata     | Livewire + Volt memaparkan ralat masa nyata dengan Alpine.js, memastikan format e-mel/telefon sah, had lampiran (≤5MB, 5 fail).                                                                        |
| SRS-HELP-003 | Penjanaan Tiket         | Sistem menjana `ticket_number`, status awal `OPEN`, menyimpan metadata tetamu (`submitter_name`, `submitter_email`).                                                                                   |
| SRS-HELP-004 | Notifikasi Tetamu       | E-mel pengesahan dihantar dengan ringkasan tiket & pautan semakan status (token).                                                                                                                      |
| SRS-HELP-005 | Triage Admin            | Admin menerima notifikasi queue dan real-time melalui Laravel Reverb, boleh menukar status (In Progress, Awaiting Info, Resolved, Closed) melalui Filament.                                            |
| SRS-HELP-006 | Komunikasi              | Admin boleh menambah komen; tetamu menerima e-mel setiap kemas kini.                                                                                                                                   |
| SRS-HELP-007 | SLA & Eskalasi          | Sistem menjejaki masa tindak balas; Superuser menerima amaran SLA.                                                                                                                                     |
| SRS-HELP-008 | Lampiran                | Fail disimpan di storan objek dengan metadata; akses dihadkan kepada Admin/Superuser.                                                                                                                  |

#### 3.3.2 Modul ICT Asset Loan

| ID           | Keperluan                | Perincian                                                                                                                                               |
| ------------ | ------------------------ | ------------------------------------------------------------------------------------------------------------------------------------------------------- |
| SRS-LOAN-001 | Borang Permohonan Hybrid | Staff mengisi data pemohon (auto-fill jika log masuk), butiran aset, tarikh mula/tamat, lokasi, tujuan, perakuan PDPA.                                  |
| SRS-LOAN-002 | Pemeriksaan Ketersediaan | Sistem menyemak konflik tempahan aset secara real-time menggunakan Livewire + Alpine.js, status `loan_transactions`, dan memaparkan alternatif.         |
| SRS-LOAN-003 | Penjanaan Permohonan     | Permohonan disimpan dengan kod rujukan unik, status `PENDING_SUPERVISOR_APPROVAL`.                                                                      |
| SRS-LOAN-004 | Kelulusan E-mel          | `ApprovalService` menjana token bertanda tangan (JWT) dan menghantar e-mel kepada pegawai Gred 41 dengan butang **Luluskan / Tolak**.                   |
| SRS-LOAN-005 | Laman Kelulusan          | Pautan membawa ke halaman tetamu ringkas yang memaparkan maklumat permohonan dan pilihan keputusan. Tiada log masuk diperlukan.                         |
| SRS-LOAN-006 | Rekod Keputusan          | Keputusan (APPROVED/REJECTED), catatan, masa, alamat IP pegawai disimpan dalam `loan_approvals`.                                                        |
| SRS-LOAN-007 | Pengeluaran Aset         | Admin menandakan `loan_transactions` (Check-out, Check-in), merekod pegawai BPM yang mengurus aset.                                                     |
| SRS-LOAN-008 | Notifikasi & Peringatan  | Tetamu & Admin menerima e-mel bagi setiap perubahan status; peringatan dihantar 3 hari sebelum tarikh pulang.                                           |
| SRS-LOAN-009 | Audit Trail              | Semua tindakan direkod dalam `loan_audits` dan `activity_log`.                                                                                          |

#### 3.3.3 Portal Pentadbiran (Admin & Superuser)

| ID          | Keperluan             | Perincian                                                                                                                                                                           |
| ----------- | --------------------- | ----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------- |
| SRS-ADM-001 | Autentikasi Pentadbir | Admin, Superuser, dan Staff wujud dalam jadual `users`. Guard Filament memerlukan 2FA (TOTP) bagi Superuser.                                                                        |
| SRS-ADM-006 | My Dashboard (Staff)  | Authenticated staff akses My Dashboard: lihat sejarah penyerahan (helpdesk + loan), pengurusan profil, pusat notifikasi.                                                            |
| SRS-ADM-002 | Kawalan Peranan       | Admin (operasi), Superuser (konfigurasi/audit), Staff (dashboard peribadi).                                                                                                         |
| SRS-ADM-003 | Dashboard             | Papar metrik SLA, backlog tiket, status aset, permohonan tertunggak, dan audit terkini menggunakan widget Filament dengan kemaskini real-time.                                      |
| SRS-ADM-004 | Pengurusan Kandungan  | Admin boleh menyunting salinan borang (soalan bantu, tooltip) tanpa menyentuh kod.                                                                                                  |
| SRS-ADM-005 | Laporan               | Eksport CSV/PDF untuk statistik, pematuhan, dan audit.                                                                                                                              |
| SRS-ADM-007 | Laravel Pulse         | Admin dan Superuser akses papan pemuka Laravel Pulse untuk memantau prestasi real-time.                                                                                             |
| SRS-ADM-008 | Laravel Telescope     | Superuser sahaja akses Laravel Telescope untuk debugging mendalam.                                                                                                                  |
| SRS-ADM-009 | Failed Jobs Monitor   | Sumber Filament khusus untuk memantau kerja barisan yang gagal.                                                                                                                     |
| SRS-ADM-010 | Email Log Tracking    | Audit komprehensif semua e-mel sistem termasuk status penghantaran.                                                                                                                 |
| SRS-ADM-011 | System Health Check   | Pemantauan kesihatan sistem masa nyata (DB, Redis, Ollama, dll).                                                                                                                    |

#### 3.3.4 Modul AI & Automasi (Cloud Hybrid)

| ID           | Keperluan                | Perincian                                                                                                                                                                                                           |
| ------------ | ------------------------ | ------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------- |
| SRS-AI-001   | FAQ Bot (Cloud Hybrid)   | Chatbot AI untuk jawab pertanyaan umum. Routing pintar: Ollama (FAQ) + AWS Bedrock Claude (kompleks).                                                                                                               |
| SRS-AI-002   | Auto-Reply Generation    | Draf respons AI untuk kategori tiket umum. Admin semak dan luluskan.                                                                                                                                                |
| SRS-AI-003   | Document Analysis        | Analisis dokumen AI untuk PDF/DOCX/imej menggunakan AWS Bedrock Nova Pro.                                                                                                                                           |
| SRS-AI-004   | Message Logging          | Semua interaksi AI dilog untuk audit. Tiada PII disimpan dalam log mesej.                                                                                                                                           |
| SRS-AI-005   | Conversation Management  | Pengurusan perbualan (simpan/muat/padam). Pautan ke user_id untuk pengguna berdaftar.                                                                                                                               |
| SRS-AI-006   | Admin Panel Management   | Sumber Filament untuk mengurus entri FAQ, templat auto-reply, dan konfigurasi model.                                                                                                                                |
| SRS-AI-007   | Hybrid Processing        | Klasifikasi automatik untuk pemprosesan tempatan (Ollama) vs awan (Bedrock) demi kedaulatan data.                                                                                                                   |
| SRS-AI-011   | Model Routing            | Analisis pertanyaan pintar untuk pemilihan model automatik.                                                                                                                                                         |
| SRS-AI-018   | Data Residency           | Penguatkuasaan residensi data Malaysia untuk pemprosesan awan.                                                                                                                                                      |

---

## 4. SPESIFIKASI KEPERLUAN BUKAN FUNGSI

### 4.1 Keperluan Prestasi

| ID | Keperluan Prestasi | Metrik | Sasaran | Keutamaan |
|----|-------------------|--------|---------|-----------|
| SRS-PERF-001 | Masa Tindak Balas Borang | TTFB | < 500ms | Tinggi |
| SRS-PERF-002 | Masa Memuat Dashboard | LCP | < 3s | Tinggi |
| SRS-PERF-003 | Pemprosesan Queue | Masa | < 30s | Sederhana |

### 4.2 Keperluan Kebolehgunaan

- Navigasi jelas, breadcrumbs pendek.
- Borang disusun dalam wizard/logical grouping.
- Bahasa Melayu sahaja untuk semua antara muka.
- Sokongan mod gelap (opsyenal).

### 4.3 Keperluan Kebolehpercayaan

- Backup DB harian; retention 30 hari.
- Fail lampiran disalin ke storan sekunder 1x sehari.

### 4.4 Keperluan Keselamatan

- Mematuhi OWASP ASVS L2.
- Signed routes + token hashed untuk kelulusan & status.
- Rate limit 60/min per IP untuk borang tetamu.
- Audit log immutable (Write Once Read Many) selama 7 tahun.

### 4.5 Keperluan Kebolehselenggaraan

- Kod didokumentasikan dengan baik (PHPDoc).
- Penggunaan Laravel Pint dan Larastan dalam CI/CD.

### 4.6 Keperluan Persekitaran

- Pelayan: Linux/Windows (Dockerized).
- Pangkalan Data: MySQL 8.0.
- Cache/Queue: Redis 7.0.

---

## 5. KEPERLUAN ANTARA MUKA

### 5.1 Antara Muka Pengguna

- **UI Web Tetamu:** Layout `guest.blade.php`, komponen Livewire, warna WCAG.
- **UI Tetamu Kelulusan:** Halaman ringan memaparkan ringkasan permohonan.
- **Filament Admin UI:** Tema tinggi kontras.

### 5.2 Antara Muka Perkakasan

- Tiada keperluan perkakasan khusus di pihak pengguna (pelayar web standard).

### 5.3 Antara Muka Perisian

- Integrasi dengan SMTP Kerajaan.
- Integrasi dengan SMS Gateway BPM.
- Integrasi dengan Google Workspace (SSO).

### 5.4 Antara Muka Komunikasi

- HTTPS (TLS 1.3) untuk semua komunikasi web.
- WebSocket (WSS) untuk komunikasi masa nyata.

---

## 6. KEKANGAN SISTEM

### 6.1 Kekangan Reka Bentuk

- Mesti menggunakan Laravel 12 dan Filament 4.
- Pematuhan kepada MyGOV Digital Service Standards v2.1.0.

### 6.2 Kekangan Pelaksanaan

- Pembangunan mesti mematuhi garis panduan keselamatan ICT MOTAC.
- Penggunaan Docker untuk persekitaran pembangunan dan produksi.

### 6.3 Kekangan Persekitaran

- Sistem mesti beroperasi dalam rangkaian dalaman (Intranet) MOTAC untuk sebahagian fungsi, namun boleh diakses melalui internet untuk fungsi awam/tetamu tertentu (jika dibenarkan).

---

## 7. LAMPIRAN

### Lampiran A: Borang Rujukan

- `helpdesk_form_to_model.md`
- `loan_form_to_model.md`

### Lampiran B: Carta Alir

- Diagram senibina (D04)
- Carta alir kelulusan e-mel

---

## Nota Penting

1. Dokumen ini disediakan mengikut piawaian KRISA (Kejuruteraan Sistem Aplikasi Sektor Awam).
2. Semua seksyen perlu dilengkapkan mengikut keperluan projek.
3. Dokumen ini perlu disemak dan disahkan oleh pihak yang berkenaan sebelum pelaksanaan.

---

## Maklumat Dokumen

- **Tarikh Terakhir Dikemaskini:** 15 Disember 2025
- **Versi:** 3.6.1
- **Status:** Aktif
