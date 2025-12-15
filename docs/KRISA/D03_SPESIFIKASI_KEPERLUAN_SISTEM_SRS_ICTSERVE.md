# DOKUMEN SPESIFIKASI KEPERLUAN SISTEM (SRS)

## SISTEM PENGURUSAN HELPDESK & PINJAMAN ASET ICT (ICTSERVE)

![Logo Agensi](logo_placeholder.png)

| Medan                 | Nilai                                                                    |
| --------------------- | ------------------------------------------------------------------------ |
| **NAMA AGENSI**       | : Bahagian Pengurusan Maklumat (BPM), MOTAC                              |
| **NAMA AGENSI INDUK** | : Kementerian Pelancongan, Seni dan Budaya Malaysia (MOTAC)              |
| **TARIKH DOKUMEN**    | : 15 Disember 2025                                                       |
| **VERSI DOKUMEN**     | : 3.7.0 (Cloud Hybrid AI & True Hybrid Architecture)                     |

---

## i. Keterangan Dokumen

Dokumen ini menerangkan spesifikasi keperluan sistem secara terperinci bagi **Sistem ICTServe** mengikut piawaian KRISA (Kejuruteraan Sistem Aplikasi Sektor Awam). Ia merangkumi keperluan fungsian dan bukan fungsian untuk modul Helpdesk, Pinjaman Aset, Pengurusan Inventori, dan Integrasi AI Hibrid (Cloud Hybrid AI). Dokumen ini menjadi rujukan utama bagi fasa rekabentuk, pembangunan, dan pengujian sistem.

Sistem ini menggunakan seni bina **"True Hybrid"** yang membenarkan akses dwi-mod (Tetamu dan Staf Berdaftar) serta integrasi **AI Hibrid** (Ollama + AWS Bedrock) untuk kedaulatan data dan pematuhan keselamatan.

---

## ii. Semakan dan Pengesahan Dokumen

Seksyen ini adalah ruangan bagi pegawai-pegawai yang bertanggungjawab untuk melakukan semakan dan pengesahan kepada maklumat-maklumat yang terkandung di dalam dokumen ini. Sila sertakan maklumat seperti nama, jawatan, tandatangan dan tarikh semakan atau kelulusan.

### SEMAKAN DOKUMEN

| Disemak Oleh | Jawatan | Tandatangan | Tarikh Semakan |
|--------------|---------|-------------|----------------|
| **Ketua Pasukan Pembangunan** | Lead Developer | *[Tandatangan]* | 15 Disember 2025 |
| **Pegawai Keselamatan ICT** | ICT Security Officer (ICTSO) | *[Tandatangan]* | 15 Disember 2025 |

### PENGESAHAN DOKUMEN

| Disahkan Oleh | Jawatan | Tandatangan | Tarikh Pengesahan |
|---------------|---------|-------------|-------------------|
| **Pengarah BPM** | Pemilik Sistem | *[Tandatangan]* | 16 Disember 2025 |
| **Ketua Pegawai Digital (CDO)** | CDO MOTAC | *[Tandatangan]* | 16 Disember 2025 |

---

## iii. Kawalan Dokumen

Seksyen ini adalah ruangan untuk mencatatkan maklumat-maklumat penyediaan dokumen termasuk maklumat pindaan yang telah dilakukan ke atas dokumen ini. Sila masukkan nombor versi, tarikh, ringkasan pindaan dan nama penyedia di dalam jadual seperti di bawah:

### KAWALAN DOKUMEN

| No. Versi | Tarikh            | Ringkasan Pindaan                                                                                                                                                   | Penyedia                |
| --------- | ----------------- | ------------------------------------------------------------------------------------------------------------------------------------------------------------------- | ----------------------- |
| 1.0.0     | September 2025    | Versi awal SRS.                                                                                                                                                     | Pasukan BPM             |
| 3.0.0     | 31 Oktober 2025   | Kemaskini seni bina Guest-First dan Laravel 12.                                                                                                                     | Pasukan Pembangunan BPM |
| 3.5.0     | 30 November 2025  | **True Hybrid Architecture**: Self-registration, SSO, Dual Audit, Laravel Pulse.                                                                                   | Pasukan Pembangunan BPM |
| 3.6.0     | 8 Disember 2025   | Pelaksanaan polisi **Bahasa Melayu Sahaja** pada antaramuka.                                                                                                       | Pasukan Pembangunan BPM |
| 3.6.1     | 14 Disember 2025  | Kemaskini Cloud Hybrid AI Integration (Ollama + AWS Bedrock), modul Asset Management, dan Laporan & Analitik.                                                      | Pasukan Pembangunan BPM |
| 3.7.0     | 15 Disember 2025  | **Cloud Hybrid AI**: Integrasi Ollama (Local) + AWS Bedrock (Cloud) untuk Chatbot & Analisis Dokumen (Rujuk D18).                                                  | Pasukan Pembangunan BPM |

**Nota Penentuan Nombor Versi:**

- Pindaan kecil/sederhana: Perubahan angka selepas titik perpuluhan (contoh: 3.6 → 3.7)
- Pindaan besar: Perubahan angka utama (contoh: 3.7 → 4.0)

---

## iv. Kandungan

Seksyen ini merupakan ruangan untuk memasukkan maklumat kandungan dokumen berserta nombor muka surat yang terlibat.

### KANDUNGAN

- i. Keterangan Dokumen
- ii. Semakan dan Pengesahan Dokumen
- iii. Kawalan Dokumen
- iv. Kandungan
- v. Senarai Gambarajah
- vi. Senarai Jadual
- vii. Definisi dan Akronim
- viii. Sumber Rujukan

1. **PENGENALAN**
   - 1.1 Tujuan Sistem
   - 1.2 Skop Sistem
   - 1.3 Gambaran Keseluruhan Sistem
   - 1.4 Senarai Pemegang Taruh

2. **KEPERLUAN SISTEM**
   - 2.1 Keperluan Fungsi
   - 2.2 Keperluan Bukan Fungsi
   - 2.3 Keperluan Antara Muka
   - 2.4 Keperluan Prestasi
   - 2.5 Keperluan Keselamatan

3. **SPESIFIKASI KEPERLUAN FUNGSI**
   - 3.1 Penggunaan Notasi
   - 3.2 Model Keperluan Fungsi
   - 3.3 Senarai Keperluan Fungsi Terperinci

4. **SPESIFIKASI KEPERLUAN BUKAN FUNGSI**
   - 4.1 Keperluan Prestasi
   - 4.2 Keperluan Kebolehgunaan
   - 4.3 Keperluan Kebolehpercayaan
   - 4.4 Keperluan Keselamatan
   - 4.5 Keperluan Kebolehselenggaraan
   - 4.6 Keperluan Persekitaran

5. **KEPERLUAN ANTARA MUKA**
   - 5.1 Antara Muka Pengguna
   - 5.2 Antara Muka Perkakasan
   - 5.3 Antara Muka Perisian
   - 5.4 Antara Muka Komunikasi

6. **KEKANGAN SISTEM**
   - 6.1 Kekangan Reka Bentuk
   - 6.2 Kekangan Pelaksanaan
   - 6.3 Kekangan Persekitaran

7. **LAMPIRAN**

---

## v. Senarai Gambarajah

Seksyen ini adalah ruangan untuk memasukkan senarai gambarajah yang terdapat di dalam dokumen ini berserta nombor muka surat yang berkaitan.

Rujuk Dokumen D04 Software Design Document untuk gambarajah seni bina terperinci.

---

## vi. Senarai Jadual

Seksyen ini adalah ruangan untuk memasukkan senarai jadual yang terdapat di dalam dokumen ini berserta nombor muka surat yang berkaitan.

Senarai jadual dijana secara automatik berdasarkan kandungan dokumen.

---

## vii. Definisi dan Akronim

### a. Akronim

| Akronim | Keterangan                                      |
| ------- | ----------------------------------------------- |
| AI      | Artificial Intelligence                         |
| ASVS    | Application Security Verification Standard      |
| BPM     | Bahagian Pengurusan Maklumat                    |
| JDN     | Jabatan Digital Negara                          |
| LLM     | Large Language Model                            |
| MOTAC   | Kementerian Pelancongan, Seni dan Budaya        |
| OTP     | One-Time Password                               |
| RAG     | Retrieval-Augmented Generation                  |
| RBAC    | Role-Based Access Control                       |
| SAL     | Signed Approval Link                            |
| SLA     | Service Level Agreement                         |
| SRS     | Software Requirements Specification             |
| SSO     | Single Sign-On                                  |
| TOTP    | Time-based One-Time Password                    |
| WCAG    | Web Content Accessibility Guidelines            |

### b. Definisi

| Terma/Istilah                  | Definisi                                                                                                                                                                                                                    |
| ------------------------------ | --------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------- |
| **True Hybrid Architecture**   | Model sistem di mana staf boleh memilih untuk log masuk secara formal (dengan akaun MyGov/NRIC) atau menggunakan borang tetamu tanpa log masuk. Kedua-dua mod akses disokong dengan penuh dalam sistem yang sama.        |
| **Cloud Hybrid AI**            | Gabungan pemprosesan AI tempatan (Ollama) untuk data sensitif atau soalan FAQ ringkas, dan AI awan (AWS Bedrock Claude) untuk analisis dokumen kompleks atau reasoning mendalam. Sistem menghalakan query secara automatik berdasarkan jenis soalan dan kepekaan data. |
| **Dual Audit**                 | Penggunaan dua sistem audit serentak: OwenIt\Auditing (field-level changes) dan Spatie Activity Log (user actions), memberikan jejak audit lengkap untuk pematuhan PDPA 2010 dan OWASP ASVS.                              |
| **Hybrid Access Model**        | Model dwi-akses di mana staf boleh log masuk untuk fungsi penuh atau menggunakan borang tetamu untuk akses pantas.                                                                                                         |
| **Authenticated Staff**        | Staf yang log masuk melalui Laravel Breeze untuk akses My Dashboard dan sejarah permohonan.                                                                                                                                |
| **Guest/Quick Access**         | Penggunaan borang intranet tanpa log masuk, dikesan melalui Token dan input manual.                                                                                                                                        |
| **Admin**                      | Pegawai BPM yang memproses tiket dan permohonan melalui panel Filament.                                                                                                                                                    |
| **Superuser**                  | Pegawai BPM yang mentadbir konfigurasi sistem, integrasi, dan audit.                                                                                                                                                       |
| **Ollama**                     | Pelayan LLM sumber terbuka untuk menjalankan model AI secara tempatan (on-premise).                                                                                                                                        |

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
1. **Modul Pinjaman Aset**: Pengurusan permohonan dan kelulusan pinjaman aset ICT.
1. **Portal Pentadbiran**: Panel kawalan untuk Admin dan Superuser menguruskan tiket, aset, dan laporan.
1. **Integrasi AI**: Chatbot FAQ, auto-reply, dan analisis dokumen menggunakan Ollama dan AWS Bedrock.
1. **Aplikasi Hibrid**: Akses melalui log masuk staf atau borang tetamu pantas.

### 1.3 Gambaran Keseluruhan Sistem

ICTServe dibangunkan berasaskan seni bina hibrid menggunakan Laravel 12 sebagai kerangka backend dan Livewire/Volt untuk frontend interaktif. Sistem ini menggunakan pangkalan data MySQL untuk penyimpanan data berstruktur dan Redis untuk pengurusan barisan (queue) serta komunikasi masa nyata (Reverb). Panel pentadbiran dibina menggunakan Filament 4.

### 1.4 Senarai Aktor dan Pemegang Taruh

Seksyen ini menghuraikan aktor-aktor utama yang terlibat dalam interaksi dengan sistem ICTServe dan peranan mereka.

| Aktor/Pemegang Taruh | Peranan/Tanggungjawab                                                                                                      | Kepentingan                                                                         |
| -------------------- | -------------------------------------------------------------------------------------------------------------------------- | ----------------------------------------------------------------------------------- |
| **Tetamu**           | Pengguna anonim yang mengakses borang tetamu tanpa log masuk untuk hantar aduan atau permohonan pinjaman pantas (Guest Mode). | Akses mudah dan pantas tanpa keperluan akaun; sokongan untuk warga yang terlupa kata laluan atau akaun tidak aktif. |
| **Staf Berdaftar**   | Warga kerja MOTAC yang log masuk menggunakan MyGov/NRIC atau kredensial Laravel Breeze untuk akses penuh.                 | Akses My Dashboard, sejarah tiket/pinjaman, notifikasi, dan papan pemuka peribadi.  |
| **Pegawai Pelulus**  | Pegawai (Gred 41+) yang meluluskan permohonan pinjaman aset melalui pautan e-mel dengan token digital.                    | Kelulusan berperingkat tanpa perlu log masuk ke sistem; meningkatkan fleksibiliti.  |
| **Admin BPM**        | Pegawai BPM yang memproses tiket aduan dan permohonan pinjaman, mengurus inventori aset melalui Panel Filament.           | Menguruskan aliran kerja harian, status tiket, dan pengagihan aset.                 |
| **Superuser**        | Pentadbir tertinggi dengan akses penuh ke konfigurasi sistem, Laravel Telescope, Pulse, audit log, dan integrasi AI.       | Penyelenggaraan sistem, konfigurasi lanjutan, dan pengurusan keselamatan.           |
| **Sistem AI**        | Ejen automatik (Ollama Chatbot & AWS Bedrock) yang menjawab FAQ, menganalisis dokumen, dan menghasilkan cadangan auto-reply. | Meningkatkan kecekapan dan mengurangkan beban kerja manual dengan automasi pintar.  |

---

## 2. KEPERLUAN SISTEM

### 2.1 Keperluan Fungsi

Sistem mesti menyediakan fungsi utama berikut:

1. Pengurusan Tiket Helpdesk (Daftar, Kemaskini, Tutup).
1. Pengurusan Pinjaman Aset (Mohon, Lulus/Tolak, Serah/Pulang).
1. Pengurusan Inventori Aset (Daftar, Selenggara, Lupus).
1. Laporan dan Analitik (Statistik Tiket, SLA, Penggunaan Aset).
1. Bantuan AI (Chatbot, Analisis Dokumen).

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

### 2.6 Pemodelan Fungsi Sistem

Fungsi sistem ICTServe dikelaskan kepada 5 fungsi utama (SF) berikut:

#### SF-01: Pengurusan Pengguna

- **SF-01.1**: Pendaftaran Pengguna Baru (Self-registration dengan MyGov/NRIC)
- **SF-01.2**: Autentikasi dan Log Masuk (Laravel Breeze + SSO)
- **SF-01.3**: Pengurusan Profil dan Kata Laluan

#### SF-02: Helpdesk & Aduan

- **SF-02.1**: Penyerahan Tiket Aduan (Hybrid: Guest/Auth)
- **SF-02.2**: Pemprosesan dan Penugasan Tiket
- **SF-02.3**: Penjanaan Laporan Tiket dan SLA

#### SF-03: Pinjaman Aset ICT

- **SF-03.1**: Penyerahan Permohonan Pinjaman (Hybrid: Guest/Auth)
- **SF-03.2**: Kelulusan Berperingkat (E-mel dengan Token)
- **SF-03.3**: Pengurusan Serah Terima dan Pulangan

#### SF-04: Perkhidmatan AI

- **SF-04.1**: Chatbot FAQ (Ollama)
- **SF-04.2**: Auto-Reply Suggestion (AWS Bedrock)
- **SF-04.3**: Analisis Dokumen (RAG Pipeline)

#### SF-05: Pentadbiran & Laporan

- **SF-05.1**: Panel Pentadbiran (Filament)
- **SF-05.2**: Penjanaan Laporan dan Analitik (Laravel Pulse)

---

## 3. SPESIFIKASI KEPERLUAN FUNGSI

### 3.1 Penggunaan Notasi

| Notasi       | Keterangan                                      |
| ------------ | ----------------------------------------------- |
| SRS-XXX-###  | ID Unik Keperluan (Contoh: SRS-HELP-001)        |

### 3.2 Model Keperluan Fungsi

#### 3.2.1 Pemodelan Use Case

##### UC-01: Mengurus Tiket Aduan (Mod Hibrid)

- **Aktor Utama**: Staf Berdaftar / Tetamu
- **Prasyarat**: Pengguna berada di rangkaian intranet MOTAC
- **Aliran Utama**:
  1. Pengguna memilih sama ada log masuk atau gunakan borang tetamu
  2. Isi borang aduan dengan medan wajib (kategori, deskripsi, lampiran)
  3. Sistem menjana nombor tiket unik dan hantar e-mel pengesahan
  4. Admin BPM memproses tiket melalui Panel Filament
- **Aliran Alternatif**: Jika pengguna log masuk, sistem auto-fill maklumat dari profil
- **Syarat Pasca**: Tiket berjaya disimpan; notifikasi e-mel dihantar

##### UC-02: Meluluskan Pinjaman Aset

- **Aktor Utama**: Pegawai Pelulus (Gred 41+)
- **Prasyarat**: Permohonan pinjaman telah disahkan oleh Admin BPM
- **Aliran Utama**:
  1. Pegawai Pelulus menerima e-mel dengan pautan kelulusan digital (token)
  2. Klik pautan untuk semak butiran permohonan
  3. Pilih "Lulus" atau "Tolak" dengan ulasan (jika ada)
  4. Sistem kemas kini status dan hantar e-mel pemakluman ke pemohon
- **Syarat Pasca**: Status permohonan dikemas kini; notifikasi e-mel dihantar

##### UC-03: Interaksi Chatbot AI

- **Aktor Utama**: Staf Berdaftar / Tetamu
- **Prasyarat**: Chatbot widget tersedia di laman utama
- **Aliran Utama**:
  1. Pengguna klik ikon chatbot dan taip soalan
  2. Sistem menghalakan soalan ke Ollama (FAQ ringkas) atau AWS Bedrock (soalan kompleks)
  3. Chatbot menjawab dalam Bahasa Melayu berdasarkan dokumen rujukan
  4. Jika perlu, chatbot cadangkan pengguna hantar tiket formal
- **Syarat Pasca**: Pengguna mendapat jawapan; interaksi dilog untuk analitik

#### 3.2.2 Pemodelan Maklumat

Entiti utama dalam sistem ICTServe:

| Entiti                  | Keterangan                                                                                         | Atribut Utama                                                                       |
| ----------------------- | -------------------------------------------------------------------------------------------------- | ----------------------------------------------------------------------------------- |
| **Users**               | Mewakili staf berdaftar (authenticated users) dengan akaun Laravel Breeze/SSO.                     | id, name, email, nric_last_4, employee_no, department, phone, role                  |
| **HelpdeskTickets**     | Mewakili tiket aduan ICT yang dihantar melalui borang hybrid (guest/auth).                         | id, ticket_no, user_id (nullable), category_id, description, attachments, status    |
| **LoanApplications**    | Mewakili permohonan pinjaman aset ICT dengan aliran kelulusan berperingkat.                        | id, loan_no, user_id (nullable), asset_id, purpose, loan_date, return_date, status  |
| **BedrockConversations**| Mewakili sejarah perbualan chatbot AI dengan staf atau tetamu.                                     | id, session_id, user_id (nullable), query, response, model_used, created_at        |

**Nota Kamus Data**: Medan `user_id` (nullable) menunjukkan sama ada penyerahan dibuat oleh pengguna berdaftar (FK ke `users.id`) atau tetamu (NULL). Jika NULL, medan seperti `submitter_name`, `submitter_email` diisi secara manual.

#### 3.2.3 Pemodelan Proses

##### Aliran Kerja Hibrid (Hybrid Workflow)

```text
                     ┌──────────────────────────────┐
                     │  Pengguna Akses Borang       │
                     └────────────┬─────────────────┘
                                  │
                     ┌────────────▼─────────────────┐
                     │   Auth::check()?             │
                     └────┬──────────────────┬──────┘
                          │ Ya (Login)       │ Tidak (Guest)
                ┌─────────▼─────────┐  ┌────▼─────────────┐
                │ Auto-fill profile │  │ Isi manual       │
                │ user_id = Auth ID │  │ user_id = NULL   │
                └─────────┬─────────┘  └────┬─────────────┘
                          │                  │
                     ┌────▼──────────────────▼─────┐
                     │  Simpan Penyerahan ke DB    │
                     │  Hantar E-mel Pengesahan    │
                     └─────────────────────────────┘
```

##### Aliran Penghalaan AI (AI Routing Workflow)

```text
                     ┌──────────────────────────────┐
                     │  Pengguna Taip Soalan        │
                     └────────────┬─────────────────┘
                                  │
                     ┌────────────▼─────────────────┐
                     │   Soalan Sensitif/Kompleks?  │
                     └────┬──────────────────┬──────┘
                          │ Ya                │ Tidak
                ┌─────────▼─────────┐  ┌─────▼─────────────┐
                │ AWS Bedrock       │  │ Ollama (Local)    │
                │ (Cloud AI)        │  │ (On-premise)      │
                └─────────┬─────────┘  └────┬──────────────┘
                          │                  │
                     ┌────▼──────────────────▼─────┐
                     │  Hantar Jawapan ke Pengguna │
                     │  Log Interaksi (Analytics)  │
                     └─────────────────────────────┘
```

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

| ID           | Keperluan Prestasi                      | Metrik           | Sasaran          | Keutamaan | Justifikasi                                                                 |
| ------------ | --------------------------------------- | ---------------- | ---------------- | --------- | --------------------------------------------------------------------------- |
| NF-PERF-01   | Masa Tindak Balas HTTP                  | Response Time    | < 2s (95th %ile) | Tinggi    | Pengalaman pengguna optimum untuk laman web interaktif dalaman MOTAC.       |
| NF-PERF-02   | Masa Jawapan Pertama Chatbot            | Time to First Token (TTFT) | < 200ms | Tinggi | Interaksi chatbot real-time untuk FAQ dan sokongan segera.                  |
| SRS-PERF-001 | Masa Tindak Balas Borang                | TTFB             | < 500ms          | Tinggi    | Muat pantas borang tetamu untuk pengalaman pengguna yang lancar.            |
| SRS-PERF-002 | Masa Memuat Dashboard                   | LCP              | < 3s             | Tinggi    | Paparan cepat dashboard pentadbiran untuk produktiviti tinggi.              |
| SRS-PERF-003 | Pemprosesan Queue                       | Job Duration     | < 30s            | Sederhana | Barisan kerja latar belakang (e-mel, notifikasi) diproses dalam masa wajar. |

### 4.2 Keperluan Kebolehgunaan

- **Navigasi Jelas**: Breadcrumbs pendek, menu hierarki yang mudah difahami.
- **Borang Terstruktur**: Borang disusun dalam wizard atau logical grouping untuk kurangkan beban kognitif.
- **Bahasa Melayu Sahaja**: Semua teks antara muka dalam Bahasa Melayu (sokongan dwibahasa untuk label teknikal sahaja).
- **Mod Gelap (Opsyenal)**: Sokongan tema gelap untuk kebolehgunaan dalam pelbagai persekitaran kerja.
- **Accessibility**: Pematuhan WCAG 2.2 AA (kontras warna, keyboard navigation, screen reader support).

### 4.3 Keperluan Kebolehpercayaan

- **Backup Harian**: Pangkalan data disandarkan setiap hari; retention 30 hari untuk disaster recovery.
- **Fail Lampiran**: Fail lampiran disalin ke storan sekunder 1x sehari untuk redundansi.
- **Uptime Target**: 99.5% uptime untuk masa operasi biasa (8 pagi - 6 petang).
- **Graceful Degradation**: Sistem kekal berfungsi (mod baca sahaja) jika perkhidmatan AI tidak tersedia.

### 4.4 Keperluan Keselamatan

| ID          | Keperluan Keselamatan                                      | Metrik/Piawaian        | Keutamaan |
| ----------- | ---------------------------------------------------------- | ---------------------- | --------- |
| NF-SEC-01   | Penyulitan Data Sensitif (AES-256)                         | OWASP ASVS L2          | Tinggi    |
| NF-SEC-02   | Dual Audit Trail (OwenIt + Spatie)                         | PDPA 2010, ASVS        | Tinggi    |
| NF-SEC-03   | Sanctum Token dengan Rate Limiting                         | 60 requests/min per IP | Tinggi    |
| SRS-SEC-001 | Pematuhan OWASP ASVS L2                                    | ASVS 4.0.3 L2          | Tinggi    |
| SRS-SEC-002 | Signed Routes + Token Hashed untuk Kelulusan               | SHA-256 Hash           | Tinggi    |
| SRS-SEC-003 | Rate Limit Borang Tetamu                                   | 60/min per IP          | Sederhana |
| SRS-SEC-004 | Audit Log Immutable (Write Once Read Many)                 | 7 tahun retention      | Tinggi    |

### 4.5 Keperluan Kebolehselenggaraan

- **Dokumentasi Kod**: Semua kod didokumentasikan dengan baik menggunakan PHPDoc dan komen inline untuk fungsi kompleks.
- **Laravel Pint**: Kod diformat mengikut PSR-12 menggunakan Laravel Pint dalam CI/CD pipeline.
- **Larastan**: Analisis statik kod menggunakan Larastan (PHPStan) untuk mengesan bug awal.
- **Testing**: Unit tests dan feature tests diselenggarakan dengan coverage minimum 70%.

### 4.6 Keperluan Persekitaran

| ID          | Keperluan Kedaulatan Data                                   | Justifikasi                                                             | Keutamaan |
| ----------- | ----------------------------------------------------------- | ----------------------------------------------------------------------- | --------- |
| NF-SOV-01   | Data Sovereignty: Tiada Data Kerajaan ke Awan               | Pematuhan kepada Pekeliling JDN dan PDPA 2010 untuk data sensitif.     | Tinggi    |
| NF-LANG-01  | Antara Muka Bahasa Melayu Sahaja                            | Pematuhan kepada Dasar Bahasa Kebangsaan untuk sistem kerajaan.        | Tinggi    |

**Infrastruktur**:

- **Pelayan**: Linux/Windows (Dockerized) dengan Docker Compose untuk deployment.
- **Pangkalan Data**: MySQL 8.0+ dengan replikasi master-slave untuk high availability.
- **Cache/Queue**: Redis 7.0+ untuk session management, queue, dan cache.
- **WebSocket**: Laravel Reverb untuk komunikasi real-time (notifikasi, dashboard updates).
- **AI On-Premise**: Ollama running locally untuk FAQ chatbot dan data residency compliance.
- **AI Cloud**: AWS Bedrock (Claude model) untuk dokumen kompleks dan reasoning (dengan user consent).

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
4. Gambarajah dan carta alir terperinci dirujuk dalam Dokumen D04 (Software Design Document).
5. Keperluan teknikal dan spesifikasi infrastruktur terperinci dirujuk dalam Dokumen D11 (Technical Design Documentation).

---

## Maklumat Dokumen

- **Tarikh Terakhir Dikemaskini:** 15 Disember 2025
- **Versi:** 3.7.0 (Cloud Hybrid AI & True Hybrid Architecture)
- **Status:** Aktif
- **Penyedia:** Pasukan Pembangunan BPM, MOTAC
- **Agensi Induk:** Kementerian Pelancongan, Seni dan Budaya Malaysia (MOTAC)
- **Pematuhan:** ISO/IEC/IEEE 29148:2018, MyGOV Digital Service Standards v2.1.0, KRISA Standards
