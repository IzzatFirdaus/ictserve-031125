[Berikut adalah **D03 Dokumen Spesifikasi Keperluan Sistem (SRS)** bagi Sistem ICTServe versi 3.7.0. Dokumen ini disediakan berdasarkan templat piawaian KRISA (Kejuruteraan Sistem Aplikasi Sektor Awam) dan menggabungkan maklumat daripada dokumen D00 hingga D18 yang telah diberikan.

-----

# DOKUMEN SPESIFIKASI KEPERLUAN SISTEM (SRS)

## SISTEM PENGURUSAN HELPDESK & PINJAMAN ASET ICT (ICTSERVE)

| Medan | Nilai |
| :--- | :--- |
| **NAMA AGENSI** | Bahagian Pengurusan Maklumat (BPM), Kementerian Pelancongan, Seni dan Budaya (MOTAC) |
| **NAMA AGENSI INDUK** | JABATAN DIGITAL NEGARA (JDN) |
| **TARIKH DOKUMEN** | 15 Disember 2025 |
| **VERSI DOKUMEN** | 3.7.0 (Cloud Hybrid AI & True Hybrid Architecture) |

-----

## i. Keterangan Dokumen

Dokumen ini menerangkan spesifikasi keperluan sistem secara terperinci bagi **Sistem ICTServe**. Ia merangkumi keperluan fungsian dan bukan fungsian untuk modul Helpdesk, Pinjaman Aset, Pengurusan Inventori, dan Integrasi AI Hibrid (Cloud Hybrid AI). Dokumen ini menjadi rujukan utama bagi fasa rekabentuk, pembangunan, dan pengujian sistem.

Sistem ini menggunakan seni bina **"True Hybrid"** yang membenarkan akses dwi-mod (Tetamu dan Staf Berdaftar) serta integrasi **AI Hibrid** (Ollama + AWS Bedrock).

-----

## ii. Semakan dan Pengesahan Dokumen

### SEMAKAN DOKUMEN

| Disemak Oleh | Jawatan | Tandatangan | Tarikh Semakan |
| :--- | :--- | :--- | :--- |
| **Ketua Pasukan Pembangunan** | Lead Developer | *[Tandatangan]* | 15 Disember 2025 |
| **Pegawai Keselamatan ICT** | ICT Security Officer (ICTSO) | *[Tandatangan]* | 15 Disember 2025 |

### PENGESAHAN DOKUMEN

| Disahkan Oleh | Jawatan | Tandatangan | Tarikh Pengesahan |
| :--- | :--- | :--- | :--- |
| **Pengarah BPM** | Pemilik Sistem | *[Tandatangan]* | 16 Disember 2025 |
| **Ketua Pegawai Digital (CDO)** | CDO MOTAC | *[Tandatangan]* | 16 Disember 2025 |

-----

## iii. Kawalan Dokumen

### KAWALAN DOKUMEN

| No. Versi | Tarikh | Ringkasan Pindaan | Penyedia |
| :--- | :--- | :--- | :--- |
| 1.0.0 | Sept 2025 | Versi awal SRS. | Pasukan BPM |
| 3.0.0 | 31 Okt 2025 | Kemaskini seni bina Guest-First dan Laravel 12. | Pasukan BPM |
| 3.5.0 | 01 Dis 2025 | **True Hybrid Architecture**: Self-registration, SSO, Dual Audit, Laravel Pulse. | Pasukan BPM |
| 3.6.0 | 08 Dis 2025 | Pelaksanaan polisi **Bahasa Melayu Sahaja** pada antaramuka. | Pasukan BPM |
| 3.7.0 | 15 Dis 2025 | **Cloud Hybrid AI**: Integrasi Ollama (Local) + AWS Bedrock (Cloud) untuk Chatbot & Analisis Dokumen (Rujuk D18). | Pasukan BPM |

-----

## iv. Kandungan

*(Kandungan dijana secara automatik berdasarkan struktur dokumen)*

1. **PENGENALAN**
2. **PEMODELAN FUNGSI SISTEM**
3. **PEMODELAN USE CASE**
4. **PEMODELAN MAKLUMAT (DATA)**
5. **PEMODELAN PROSES SISTEM**
6. **PENENTUAN KEPERLUAN BUKAN FUNGSIAN**
7. **LAMPIRAN**

-----

## v. Senarai Gambarajah

*(Rujuk Dokumen D04 Software Design Document & D18 AI Architecture untuk rajah visual)*

| No. | Tajuk Gambarajah | Rujukan Dokumen |
| :--- | :--- | :--- |
| Rajah 1 | Hierarki Fungsian Sistem ICTServe | D04 |
| Rajah 2 | Aliran Kerja Hybrid Authentication | D04 / D11 |
| Rajah 3 | Seni Bina Cloud Hybrid AI (Ollama + Bedrock) | D18 |
| Rajah 4 | Entity Relationship Diagram (ERD) v3.6.1 | D09 |

-----

## vi. Senarai Jadual

| No. | Tajuk Jadual | Rujukan Dokumen |
| :--- | :--- | :--- |
| Jadual 1 | Senarai Aktor Sistem | D02 / D03 |
| Jadual 2 | Definisi Entiti Pangkalan Data | D09 |
| Jadual 3 | Spesifikasi API Endpoint | D08 |

-----

## vii. Definisi dan Akronim

### a. Akronim

| Akronim | Keterangan |
| :--- | :--- |
| **AI** | *Artificial Intelligence* (Kecerdasan Buatan) |
| **BPM** | Bahagian Pengurusan Maklumat |
| **LLM** | *Large Language Model* |
| **RAG** | *Retrieval-Augmented Generation* |
| **RBAC** | *Role-Based Access Control* |
| **SLA** | *Service Level Agreement* |
| **SSO** | *Single Sign-On* |
| **TOTP** | *Time-based One-Time Password* |

### b. Definisi

| Terma/Istilah | Definisi |
| :--- | :--- |
| **True Hybrid Architecture** | Model di mana staf boleh memilih untuk log masuk (untuk dashboard peribadi) atau menggunakan borang tetamu (akses pantas) tanpa halangan. |
| **Cloud Hybrid AI** | Gabungan pemprosesan AI tempatan (Ollama) untuk data sensitif/FAQ dan AI awan (AWS Bedrock) untuk penaakulan kompleks. |
| **Dual Audit** | Penggunaan dua sistem audit serentak: satu untuk pematuhan data (field-level) dan satu untuk aktiviti operasi. |

-----

## viii. Sumber Rujukan

1. **D00\_SYSTEM\_OVERVIEW.md** (Ringkasan Sistem v3.6.1)
2. **D18\_AI\_CHATBOT\_OLLAMA\_BEDROCK.md** (Seni Bina AI)
3. **Pekeliling Kemajuan Pentadbiran Awam Bil. 1 Tahun 2022** (Pendigitalan Penyampaian Perkhidmatan)
4. **MyGOV Digital Service Standards v2.1.0**

-----

## 1\. PENGENALAN

### 1.1 Tujuan Sistem

Sistem ICTServe dibangunkan untuk menggantikan kaedah manual/lama dalam pengurusan aduan ICT dan pinjaman aset di MOTAC. Sistem ini bertujuan untuk meningkatkan kecekapan penyampaian perkhidmatan melalui automasi, integrasi AI pintar, dan aksesibiliti yang fleksibel (Hybrid) bagi semua warga kerja MOTAC.

### 1.2 Skop Sistem

Sistem ini merangkumi modul-modul berikut:

1. **Pengurusan Aduan (Helpdesk):** Pelaporan kerosakan perkakasan, perisian, dan rangkaian.
2. **Pengurusan Pinjaman Aset:** Tempahan, kelulusan, dan pemulangan peralatan ICT.
3. **Pengurusan Inventori:** Pangkalan data aset ICT.
4. **Kecerdasan Buatan (AI):** Chatbot FAQ, Analisis Dokumen, dan Auto-Reply (Drafting).
5. **Pentadbiran:** Papan pemuka (Dashboard), laporan statistik, dan audit.

### 1.3 Gambaran Keseluruhan Sistem

ICTServe beroperasi di persekitaran intranet/internet tertutup MOTAC. Ia menggunakan **Laravel 12** sebagai kerangka utama, disokong oleh pangkalan data **MySQL**, cache **Redis**, dan komunikasi masa nyata **Laravel Reverb**. Sistem ini mengintegrasikan komponen AI melalui **Ollama** (Local) dan **AWS Bedrock** (Cloud).

### 1.4 Senarai Aktor Sistem

| Aktor | Peranan/Tanggungjawab |
| :--- | :--- |
| **Tetamu (Guest)** | Staf MOTAC yang menggunakan sistem tanpa log masuk (akses pantas). |
| **Staf Berdaftar** | Staf MOTAC yang mendaftar akaun untuk melihat sejarah dan papan pemuka peribadi. |
| **Pentadbir (Admin)** | Pegawai BPM yang menguruskan tiket, aset, dan operasi harian. |
| **Superuser** | Pentadbir atasan dengan akses penuh ke konfigurasi sistem, audit log, dan debugging (Telescope). |
| **Pegawai Pelulus** | Pegawai (Gred 41+) yang meluluskan pinjaman melalui pautan e-mel (Token). |
| **Sistem AI** | Ejen automatik yang menjawab FAQ dan menganalisis dokumen. |

-----

## 2\. PEMODELAN FUNGSI SISTEM (HIERARKI)

### 2.1 Struktur Modul Utama

Sistem dibahagikan kepada fungsi-fungsi utama berikut (Rujuk D00/D02):

* **SF-01: Pengurusan Pengguna (User Management)**
  * SF-01-01: Pendaftaran Sendiri (Self-Registration @motac.gov.my)
  * SF-01-02: Log Masuk Fleksibel (Email/Username) & SSO (Google)
  * SF-01-03: Pautan Akaun (Account Linking) untuk rekod terdahulu
* **SF-02: Helpdesk & Aduan**
  * SF-02-01: Serahan Tiket (Hibrid: Tetamu/Berdaftar)
  * SF-02-02: Semakan Status (Token/Dashboard)
  * SF-02-03: Tindakan Pembaikan & SLA
* **SF-03: Pinjaman Aset ICT**
  * SF-03-01: Permohonan Pinjaman
  * SF-03-02: Kelulusan (Workflow E-mel)
  * SF-03-03: Penjejakan Aksesori (Check-out/Check-in)
* **SF-04: Perkhidmatan AI (Cloud Hybrid)**
  * SF-04-01: FAQ Chatbot (Ollama)
  * SF-04-02: Analisis Dokumen (Bedrock/Ollama)
  * SF-04-03: Penjanaan Auto-Reply
* **SF-05: Pentadbiran & Laporan**
  * SF-05-01: Papan Pemuka Statistik
  * SF-05-02: Audit Trail & Performance (Pulse)

-----

## 3\. PEMODELAN USE CASE (RINGKASAN)

*(Perincian penuh terdapat dalam D02 Business Requirements)*

### 3.1 UC-01: Mengurus Tiket Aduan (Mod Hibrid)

* **Aktor:** Tetamu, Staf Berdaftar.
* **Keterangan:** Pengguna mengisi borang aduan. Jika log masuk, maklumat diri diisi automatik. Jika tetamu, perlu isi manual. Tiket dihantar dan notifikasi e-mel diterima.

### 3.2 UC-02: Meluluskan Pinjaman Aset

* **Aktor:** Pegawai Pelulus (Gred 41+).
* **Keterangan:** Pegawai menerima e-mel dengan pautan bertanda (Signed URL). Klik pautan membawa ke halaman keputusan (Lulus/Tolak) tanpa perlu log masuk ke sistem.

### 3.3 UC-03: Interaksi Chatbot AI

* **Aktor:** Semua Pengguna.
* **Keterangan:** Pengguna bertanya soalan. Sistem menentukan laluan (Routing): Soalan mudah ke Ollama (Local), soalan kompleks ke AWS Bedrock (Cloud).

-----

## 4\. PEMODELAN MAKLUMAT (DATA)

*(Merujuk kepada D09 Database Documentation)*

### 4.1 Entiti Utama

| Entiti | Atribut Utama | Hubungan |
| :--- | :--- | :--- |
| **Users** | `id`, `email`, `role`, `google_id` | 1:N kepada Tickets, Loans |
| **HelpdeskTickets** | `id`, `ticket_no`, `user_id` (Nullable), `status` | N:1 kepada Users (Optional) |
| **LoanApplications** | `id`, `ref_code`, `status`, `approval_token` | N:1 kepada Users (Optional) |
| **BedrockConversations** | `id`, `context`, `model_used`, `messages` (JSON) | Menyimpan sejarah chat AI |

### 4.2 Kamus Data Ringkas (Data Dictionary)

* **user\_id (Nullable)**: Kunci asing ke jadual `users`. Jika `NULL`, rekod tersebut adalah daripada pengguna Tetamu.
* **form\_reference\_code**: Kod rujukan borang rasmi (cth: PK.(S).MOTAC.07.(L1)) untuk pematuhan ISO.

-----

## 5\. PEMODELAN PROSES SISTEM (ALIRAN DATA)

### 5.1 Aliran Proses Hibrid (Hybrid Workflow)

1. **Pengguna mengakses sistem.**
2. **Keputusan:** Adakah pengguna log masuk?
      * **Ya:** Sistem mengambil data profil dari DB. Borang diisi automatik. Rekod disimpan dengan `user_id`. Notifikasi dihantar ke Dashboard + E-mel.
      * **Tidak (Tetamu):** Pengguna mengisi borang manual. Rekod disimpan dengan `user_id = NULL`. Notifikasi dihantar ke E-mel sahaja.

### 5.2 Aliran Proses AI (AI Routing Workflow)

1. **Pengguna menghantar pertanyaan (Query).**
2. **Model Router (Service):** Menganalisis kompleksiti soalan dan sensitiviti data.
      * **Data Sensitif/FAQ Mudah:** Dihantar ke **Ollama (Local)**.
      * **Analisis Kompleks/Umum:** Dihantar ke **AWS Bedrock (Cloud)**.
3. **Respons:** Jawapan dipaparkan kepada pengguna (Streaming response).

-----

## 6\. PENENTUAN KEPERLUAN BUKAN FUNGSIAN

### 6.1 Keperluan Prestasi

* **NF-PERF-01:** Masa tindak balas aplikasi mestilah \< 2 saat untuk 95% permintaan (Dipantau via Laravel Pulse).
* **NF-PERF-02:** Respons Chatbot AI mestilah bermula (First Token) dalam masa \< 200ms.

### 6.2 Keperluan Keselamatan

* **NF-SEC-01:** Semua data sensitif (PII) mesti disulitkan (AES-256).
* **NF-SEC-02:** Sistem mesti mempunyai **Dual Audit Trail** (Audit Pematuhan & Log Aktiviti Operasi).
* **NF-SEC-03:** Capaian API mesti menggunakan Token Sanctum dengan had kadar (Rate Limiting).

### 6.3 Keperluan Kedaulatan Data (Data Sovereignty)

* **NF-SOV-01:** Data terperingkat Kerajaan TIDAK BOLEH dihantar ke Cloud AI (Bedrock). Ia mesti diproses secara tempatan (Ollama).

### 6.4 Keperluan Bahasa

* **NF-LANG-01:** Antaramuka sistem mestilah dalam **Bahasa Melayu** sepenuhnya (Pematuhan v3.6.0).

-----

## 7\. LAMPIRAN

### Lampiran A: Spesifikasi Borang

* **Borang Aduan:** Rujuk fail `docs/helpdesk_form_to_model.md`
* **Borang Pinjaman:** Rujuk fail `docs/loan_form_to_model.md`

### Lampiran B: Senarai Kod Rujukan Borang

1. **PK.(S).MOTAC.07.(L1)** - Borang Aduan Kerosakan ICT
2. **PK.(S).MOTAC.07.(L3)** - Borang Permohonan Pinjaman Aset

-----

**Dokumen Tamat**
