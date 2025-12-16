# MANUAL PENGGUNA SISTEM

## ICTSERVE
(Sistem Pengurusan Helpdesk & Pinjaman Aset ICT)

![Logo Agensi](../public/images/motac-logo.png)

| Medan                 | Nilai                                            |
| --------------------- | ------------------------------------------------ |
| **NAMA AGENSI**       | : Bahagian Pengurusan Maklumat (BPM), MOTAC      |
| **NAMA AGENSI INDUK** | : Kementerian Pelancongan, Seni dan Budaya Malaysia |
| **TARIKH DOKUMEN**    | : 15 Disember 2025                               |
| **VERSI DOKUMEN**     | : 3.7.0 (True Hybrid Architecture & Cloud Hybrid AI) |

---

## i. Keterangan Dokumen

Manual ini menyediakan panduan lengkap untuk pengguna (Staf, Tetamu, dan Pegawai Pelulus) dalam menggunakan ICTServe v3.7.0, merangkumi pendaftaran, akses tetamu, modul Helpdesk, Pinjaman Aset, Pembantu AI, serta penyelesaian masalah asas. Dokumen mematuhi piawaian KRISA dan menekankan ciri **True Hybrid Architecture** dan **Cloud Hybrid AI**.

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

| No. Versi | Tarikh            | Ringkasan Pindaan                                                        | Penyedia                |
| --------- | ----------------- | ------------------------------------------------------------------------ | ----------------------- |
| 3.7.0     | 15 Disember 2025  | Kemas kini Cloud Hybrid AI, True Hybrid Access, Bahasa Melayu sahaja.   | Pasukan Pembangunan BPM |
| 3.6.1     | 15 Disember 2025  | Kemaskini manual pengguna selaras dengan versi sistem 3.6.1 (Hybrid AI).| Pasukan Pembangunan BPM |
| 3.0.0     | 31 Oktober 2025   | Versi awal manual pengguna untuk sistem ICTServe v3.0.                  | Pasukan Pembangunan BPM |

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

1. **PENGENALAN**
   - 1.1 Tujuan Manual
   - 1.2 Skop Manual
   - 1.3 Sasaran Pengguna
   - 1.4 Gambaran Keseluruhan Sistem

2. **KEPERLUAN SISTEM**
   - 2.1 Keperluan Perkakasan
   - 2.2 Keperluan Perisian
   - 2.3 Keperluan Rangkaian

3. **AKSES DAN KONFIGURASI**
   - 3.1 Akses Sistem
   - 3.2 Pendaftaran dan Log Masuk
   - 3.3 Konfigurasi Profil

4. **PENGENALAN ANTARA MUKA PENGGUNA**
   - 4.1 Dashboard Utama
   - 4.2 Menu Navigasi
   - 4.3 Pintasan Papan Kekunci

5. **FUNGSI-FUNGSI SISTEM**
   - 5.1 Modul Helpdesk (Aduan Kerosakan)
   - 5.2 Modul Pinjaman Aset ICT
   - 5.3 Modul Kelulusan (Pegawai Gred 41+)
   - 5.4 Bantuan AI (Chatbot & FAQ)

6. **PENYELENGGARAAN DAN SOKONGAN**
   - 6.1 Hubungi Sokongan Teknikal

7. **PENYELESAIAN MASALAH**
   - 7.1 Soalan Lazim (FAQ)

8. **LAMPIRAN**

---

## v. Senarai Gambarajah

| Rajah | Penerangan                                               | Fail Imej                                                     |
| ----- | -------------------------------------------------------- | ------------------------------------------------------------- |
| 1     | Halaman Utama (Welcome) — Desktop (Cerah)               | ../public/images/development/welcome-desktop-light.png        |
| 2     | Halaman Utama (Welcome) — Mudah Alih (Cerah)            | ../public/images/development/welcome-mobile-light.png         |
| 3     | Log Masuk — Desktop (Cerah)                              | ../public/images/development/login-desktop-light.png          |
| 4     | Pendaftaran Akaun — Desktop (Cerah)                      | ../public/images/development/register-desktop-light.png       |
| 5     | Akses Pantas (Laman Utama)                               | ../public/images/development/quick-home.png                   |
| 6     | Borang Tiket Helpdesk — Desktop (Cerah)                  | ../public/images/development/helpdesk-create-desktop-light.png|
| 7     | Penghantaran Tiket Berjaya — Desktop (Cerah)            | ../public/images/development/helpdesk-submit-desktop-light.png|
| 8     | Borang Pinjaman Aset — Desktop (Cerah)                   | ../public/images/development/loan-create-desktop-light.png    |
| 9     | Wizard Helpdesk (Langkah 1–4) — Desktop (Cerah)         | ../public/images/development/helpdesk-wizard-step1-desktop-light.png, ...-step4-... |
| 10    | Wizard Pinjaman (Langkah 1–4) — Desktop (Cerah)         | ../public/images/development/loan-wizard-step1-desktop-light.png, ...-step4-...     |
| 11    | Chatbot/FAQ — Widget Terbuka                             | ../public/images/development/faq-bot-widget-open.png          |
| 12    | Semak Status Tiket/Pinjaman — Desktop (Cerah)            | ../public/images/development/status-check-desktop-light.png   |

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
| OTP     | One-Time Password (Kata Laluan Sekali Guna)     |
| SLA     | Service Level Agreement (Perjanjian Tahap Perkhidmatan) |
| AI      | Artificial Intelligence (Kecerdasan Buatan)     |
| URL     | Uniform Resource Locator                        |
| FAQ     | Frequently Asked Questions                       |

### b. Definisi

| Terma/Istilah           | Definisi                                                                                                                   |
| ----------------------- | -------------------------------------------------------------------------------------------------------------------------- |
| **Hybrid Access**       | Kebolehan menggunakan sistem sama ada sebagai tetamu (tanpa log masuk) atau pengguna berdaftar.                            |
| **Dashboard**           | Halaman utama yang memaparkan ringkasan status tiket dan pinjaman.                                                         |
| **Pegawai Pelulus**     | Pegawai Gred 41+ yang meluluskan permohonan pinjaman aset melalui e-mel/token.                                             |
| **Tiket**               | Rekod aduan kerosakan yang didaftarkan dalam sistem.                                                                       |
| **Pembantu AI**         | Chatbot pintar yang menjawab soalan FAQ dan membantu menganalisis dokumen sokongan.                                        |

---

## viii. Sumber Rujukan

1. D03_SPESIFIKASI_KEPERLUAN_SISTEM_SRS_ICTSERVE.md (v3.7.0)
2. D18_AI_CHATBOT_OLLAMA_BEDROCK.md (v1.0.0)
3. USER_MANUAL_MS.md (v3.0.0)

---

## 1. PENGENALAN

### 1.1 Tujuan Manual

Memberi panduan kepada pengguna ICTServe v3.7.0 untuk mengakses, menghantar aduan, memohon pinjaman aset, berinteraksi dengan Pembantu AI, dan memahami proses kelulusan.

### 1.2 Skop Manual

- Panduan akses dan log masuk (termasuk mod tetamu).
- Arahan penggunaan modul Helpdesk dan Pinjaman Aset.
- Panduan untuk Pegawai Pelulus.
- Penggunaan ciri bantuan AI.
- Penyelesaian masalah lazim.

### 1.3 Sasaran Pengguna

| Kategori Pengguna | Keterangan |
| ----------------- | ---------- |
| **Staf MOTAC**    | Membuat aduan atau memohon pinjaman aset melalui log masuk. |
| **Tetamu**        | Menghantar aduan atau permohonan pantas tanpa log masuk. |
| **Pegawai Pelulus**| Pegawai Gred 41+ yang meluluskan permohonan pinjaman melalui e-mel/token. |
| **Pentadbir**     | Rujuk Manual Pentadbir untuk fungsi lanjut. |

### 1.4 Gambaran Keseluruhan Sistem

ICTServe ialah sistem pengurusan perkhidmatan ICT dalaman yang menyokong **True Hybrid Architecture** (Tetamu atau Log Masuk). Sistem menyediakan Helpdesk, Pinjaman Aset, kelulusan e-mel, dan Pembantu AI berasaskan Ollama (on-prem) serta AWS Bedrock untuk analisis kompleks.

---

## 2. KEPERLUAN SISTEM

### 2.1 Keperluan Perkakasan

- Komputer/telefon pintar/tablet dengan sambungan internet.
- Pengimbas QR (opsyenal) untuk semak status pinjaman.

### 2.2 Keperluan Perisian

| Perisian | Keperluan |
| -------- | --------- |
| Pelayar Web | Google Chrome (disyorkan), Mozilla Firefox, Microsoft Edge, Safari (terkini) |
| Pemaparan PDF | Adobe Acrobat Reader atau pelayar web dengan sokongan PDF |

### 2.3 Keperluan Rangkaian

- Sambungan Internet atau Intranet MOTAC yang stabil.

---

## 3. AKSES DAN KONFIGURASI

### 3.1 Akses Sistem

1. Buka pelayar web dan layari `https://ictserve.motac.gov.my`.
2. Pilih **Log Masuk** atau gunakan akses **Tetamu** untuk tindakan pantas.

Contoh paparan:

![Akses Pantas — Laman Utama](../public/images/development/quick-home.png)

![Akses Pantas — Log Masuk Pantas](../public/images/development/quick-login.png)

### 3.2 Pendaftaran dan Log Masuk

**Pendaftaran (Staf Berdaftar)**
1. Klik **Daftar**.
2. Masukkan Nama dan E-mel rasmi `@motac.gov.my`.
3. Cipta kata laluan dan sahkan melalui e-mel pengesahan.

Contoh paparan:

![Pendaftaran — Desktop (Cerah)](../public/images/development/register-desktop-light.png)


**Log Masuk**
1. Klik **Log Masuk**.
2. Masukkan e-mel atau nama pengguna pendek (contoh: `ali.abu`).
3. Masukkan kata laluan, kemudian log masuk.
4. *(Opsyenal)* Log masuk Google Workspace jika didayakan.

Contoh paparan:

![Log Masuk — Desktop (Cerah)](../public/images/development/login-desktop-light.png)

![Log Masuk — Modal Terbuka](../public/images/development/login-modal-open.png)

### 3.3 Konfigurasi Profil

- Kemas kini maklumat profil (nama, telefon, bahagian).
- Tukar kata laluan secara berkala.
- Tetapkan bahasa paparan (Bahasa Melayu sebagai lalai).

---

## 4. PENGENALAN ANTARA MUKA PENGGUNA

### 4.1 Dashboard Utama

- Memaparkan ringkasan tiket, status pinjaman, dan notifikasi.

Contoh paparan:

![Dashboard — Desktop (Cerah)](../public/images/development/welcome-desktop-light.png)

![Dashboard — Mudah Alih (Cerah)](../public/images/development/welcome-mobile-light.png)

### 4.2 Menu Navigasi

- Menu sisi menyediakan pautan ke Helpdesk, Pinjaman, Kelulusan, dan AI Chatbot.

Contoh paparan:

![Menu Navigasi — Paparan Laptop](../public/images/development/welcome-laptop-light.png)

### 4.3 Pintasan Papan Kekunci

- Gunakan carian pantas (Ctrl/Cmd + K) jika disediakan untuk melompat ke modul tertentu.

---

## 5. FUNGSI-FUNGSI SISTEM

### 5.1 Modul Helpdesk (Aduan Kerosakan)

**Menghantar Tiket (Tetamu atau Log Masuk)**
1. Pilih **Hantar Tiket**.
2. Isi Nama, E-mel, Bahagian, Kategori, dan Keterangan masalah.
3. Lampirkan fail jika perlu (had 10MB).
4. Hantar dan terima Nombor Tiket serta pautan status.

Contoh paparan:

![Borang Tiket Helpdesk — Desktop (Cerah)](../public/images/development/helpdesk-create-desktop-light.png)

![Penghantaran Tiket Berjaya — Desktop (Cerah)](../public/images/development/helpdesk-submit-desktop-light.png)

Langkah-langkah Wizard (contoh):

![Wizard Helpdesk — Langkah 1](../public/images/development/helpdesk-wizard-step1-desktop-light.png)

![Wizard Helpdesk — Langkah 2](../public/images/development/helpdesk-wizard-step2-desktop-light.png)

![Wizard Helpdesk — Langkah 3](../public/images/development/helpdesk-wizard-step3-desktop-light.png)

![Wizard Helpdesk — Langkah 4](../public/images/development/helpdesk-wizard-step4-desktop-light.png)

### 5.2 Modul Pinjaman Aset ICT

1. Pilih **Mohon Pinjaman**.
2. Pilih jenis aset dan tarikh mula/tamat.
3. Masukkan tujuan dan nama **Pegawai Pelulus**.
4. Hantar permohonan. Pegawai Pelulus menerima e-mel token untuk meluluskan.

Contoh paparan:

![Borang Pinjaman Aset — Desktop (Cerah)](../public/images/development/loan-create-desktop-light.png)

Langkah-langkah Wizard (contoh):

![Wizard Pinjaman — Langkah 1](../public/images/development/loan-wizard-step1-desktop-light.png)

![Wizard Pinjaman — Langkah 2](../public/images/development/loan-wizard-step2-desktop-light.png)

![Wizard Pinjaman — Langkah 3](../public/images/development/loan-wizard-step3-desktop-light.png)

![Wizard Pinjaman — Langkah 4](../public/images/development/loan-wizard-step4-desktop-light.png)

### 5.3 Modul Kelulusan (Pegawai Gred 41+)

- E-mel kelulusan mengandungi pautan token untuk **Lulus** atau **Tolak**.
- Status permohonan dikemas kini serta-merta selepas keputusan.

### 5.4 Bantuan AI (Chatbot & FAQ)

- Masukkan soalan di ruangan Chatbot.
- Soalan fakta/sensitif diproses oleh **Ollama (On-Prem)**; soalan analisis kompleks menggunakan **AWS Bedrock**.
- Semak jawapan dan ikut cadangan tindakan jika disediakan.

Contoh paparan:

![Chatbot/FAQ — Widget Terbuka](../public/images/development/faq-bot-widget-open.png)

---

## 6. PENYELENGGARAAN DAN SOKONGAN

- Hubungi **Unit Helpdesk BPM MOTAC**
- Emel: helpdesk@motac.gov.my
- Telefon: Sambungan 1234

---

## 7. PENYELESAIAN MASALAH

- **Tidak boleh log masuk**: Semak e-mel/pengaktifan akaun; cuba set semula kata laluan.
- **Tiket tidak dihantar**: Pastikan saiz lampiran < 10MB dan sambungan rangkaian stabil.
- **Jawapan AI tidak tepat**: Cuba perincikan soalan atau rujuk manual/FAQ.

Contoh paparan:

![Semak Status — Desktop (Cerah)](../public/images/development/status-check-desktop-light.png)

---

## 8. LAMPIRAN

- Jadual Peranan Pengguna (Staf, Tetamu, Pegawai Pelulus).
- Contoh e-mel kelulusan dengan token.
