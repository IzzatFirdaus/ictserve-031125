# D17 DOKUMEN MANUAL PENGGUNA SISTEM

**SISTEM ICTSERVE**

*(Sistem Pengurusan Helpdesk dan Pinjaman Aset ICT)*

| | |
| :--- | :--- |
| **NAMA AGENSI** | : Bahagian Pengurusan Maklumat (BPM) |
| **NAMA AGENSI INDUK** | : Kementerian Pelancongan, Seni dan Budaya Malaysia (MOTAC) |
| **TARIKH DOKUMEN** | : 23 Disember 2025 |
| **VERSI DOKUMEN** | : 4.0.0 |

---

## i. Keterangan Dokumen

Dokumen ini menyediakan panduan lengkap untuk pengguna Sistem ICTServe yang merangkumi prosedur penggunaan, fungsi sistem, dan arahan langkah demi langkah untuk mengakses dan menggunakan sistem. Manual ini disediakan mengikut piawaian WCAG 2.2 AA untuk kebolehcapaian dan mematuhi garis panduan kegunaan sistem kerajaan Malaysia.

## ii. Kawalan Dokumen

**KAWALAN DOKUMEN**

| No. Versi | Tarikh | Ringkasan Pindaan | Penyedia |
| :--- | :--- | :--- | :--- |
| 1.0.0 | 15 September 2025 | Versi awal manual pengguna | Pasukan Pembangunan BPM |
| 2.0.0 | 23 Disember 2025 | Kemaskini struktur dan kandungan | Pasukan Pembangunan BPM |
| 4.0.0 | 24 Disember 2025 | Kemaskini panduan pengguna dan fungsi sistem | Pasukan Pembangunan BPM |

## iii. Kandungan

1. [PENGENALAN](#1-pengenalan) ... 3
2. [OVERVIEW SISTEM](#2-overview-sistem) ... 5
3. [KETERANGAN FUNGSI SISTEM](#3-keterangan-fungsi-sistem) ... 7
4. [ARAHAN PENGGUNAAN SISTEM](#4-arahan-penggunaan-sistem) ... 12
5. [PENGENDALIAN RALAT](#5-pengendalian-ralat) ... 20

## iv. Senarai Gambarajah

- Gambarajah 1: Aliran Pengguna Sistem ICTServe ... 6
- Gambarajah 2: Proses Penyerahan Tiket Helpdesk ... 9
- Gambarajah 3: Proses Permohonan Pinjaman Aset ... 11
- Gambarajah 4: Interaksi dengan AI Chatbot ... 14

## v. Senarai Jadual

- Jadual 1: Jenis Pengguna dan Akses ... 5
- Jadual 2: Senarai Fungsi Utama ... 8
- Jadual 3: Status Tiket dan Maknanya ... 10
- Jadual 4: Kod Ralat Biasa ... 21

---

## 1. PENGENALAN

Manual Pengguna Sistem ICTServe mengandungi semua maklumat penting bagi pengguna untuk menggunakan sepenuhnya sistem pengurusan helpdesk dan pinjaman aset dalaman MOTAC. Manual ini termasuklah penerangan tentang fungsi sistem dan keupayaan, prosedur langkah demi langkah untuk akses sistem dan kaedah penggunaannya, serta panduan penggunaan AI Chatbot untuk sokongan automatik.

### 1.1. Tujuan dan Skop

**Tujuan:**

- Menyediakan panduan komprehensif untuk semua jenis pengguna sistem
- Memastikan pengguna dapat menggunakan sistem dengan berkesan
- Mengurangkan keperluan sokongan teknikal melalui dokumentasi yang jelas

**Skop:**

- Panduan untuk staf MOTAC (pengguna berdaftar)
- Panduan untuk Walk-in/Kiosk Mode
- Panduan untuk pentadbir sistem
- Penggunaan AI Chatbot untuk FAQ dan sokongan

### 1.2. Organisasi Manual

Manual ini disusun mengikut aliran penggunaan sistem:

- **Bahagian 1-2**: Pengenalan dan gambaran keseluruhan sistem
- **Bahagian 3**: Penerangan fungsi-fungsi utama sistem
- **Bahagian 4**: Arahan langkah demi langkah penggunaan
- **Bahagian 5**: Pengendalian masalah dan ralat

### 1.3. Maklumat Untuk Dihubungi

**Sokongan Teknikal:**

- **Helpdesk BPM MOTAC**: 03-xxxx-xxxx (Waktu Pejabat)
- **E-mel Sokongan**: <helpdesk@motac.gov.my>
- **Sistem Sokongan**: Gunakan fungsi "Hantar Tiket" dalam sistem
- **AI Chatbot**: Tersedia 24/7 untuk soalan asas dan FAQ

**Pegawai Bertanggungjawab:**

- **Ketua BPM**: Pegawai Teknologi Maklumat F54
- **Pentadbir Sistem**: Pegawai Teknologi Maklumat F44
- **Sokongan Pengguna**: Pegawai Teknologi Maklumat F41

### 1.4. Rujukan Projek

Dokumen berkaitan yang boleh dirujuk:

- **D00_SYSTEM_OVERVIEW.md** - Gambaran keseluruhan sistem
- **D02_BUSINESS_REQUIREMENTS_SPECIFICATION.md** - Keperluan perniagaan
- **D03_SOFTWARE_REQUIREMENTS_SPECIFICATION.md** - Keperluan perisian
- **D04_SYSTEM_DESIGN_SPECIFICATION.md** - Spesifikasi rekabentuk sistem

### 1.5. Sumber Rujukan Keselamatan

1. **Polisi Keselamatan Siber MOTAC** - Garis panduan keselamatan siber agensi
2. **Personal Data Protection Act 2010 (PDPA)** - Malaysian data protection legislation
3. **WCAG 2.2 AA** - Web Content Accessibility Guidelines Level AA
4. **MyGOV Digital Service Standards v2.1.0** - Malaysian Government Digital Service Standards

- D00_SYSTEM_OVERVIEW.md - Gambaran keseluruhan sistem
- D03_SOFTWARE_REQUIREMENTS_SPECIFICATION.md - Keperluan sistem
- D15_LANGUAGE_MS_EN.md - Panduan bahasa (Bahasa Melayu sahaja)
- D18_AI_CHATBOT_OLLAMA_BEDROCK.md - Dokumentasi AI Chatbot

### 1.5. Fungsi Utama Sistem

Sistem ICTServe menyokong operasi harian BPM MOTAC melalui:

**Pengurusan Helpdesk:**

- Penyerahan tiket aduan ICT
- Penjejakan status tiket
- Komunikasi dengan pentadbir
- Penyelesaian masalah teknikal

**Pengurusan Pinjaman Aset:**

- Permohonan pinjaman peralatan ICT
- Proses kelulusan bertingkat
- Penjejakan status permohonan
- Pengurusan pemulangan aset

**Sokongan AI:**

- FAQ Bot untuk soalan biasa
- Analisis dokumen automatik
- Cadangan penyelesaian masalah
- Sokongan 24/7

### 1.6. Glosari

| Istilah | Definisi |
| :--- | :--- |
| **AI Chatbot** | Sistem kecerdasan buatan untuk menjawab soalan dan memberikan sokongan |
| **Helpdesk** | Sistem sokongan teknikal untuk mengendalikan aduan dan masalah ICT |
| **Staf MOTAC** | Pegawai MOTAC yang mempunyai akaun sistem yang sah |
| **Walk-in/Kiosk Mode** | Mod akses untuk pengguna walk-in |
| **Tiket** | Rekod aduan atau permintaan sokongan teknikal |

## 2. OVERVIEW SISTEM

### 2.1. Tujuan

Sistem ICTServe dibangunkan untuk:

- Menyelaraskan proses sokongan ICT dalam MOTAC
- Menyediakan platform bersepadu untuk helpdesk dan pinjaman aset
- Meningkatkan kecekapan pengurusan sumber ICT
- Menyediakan sokongan AI untuk penyelesaian masalah pantas
- Memastikan pematuhan kepada piawaian keselamatan kerajaan

### 2.2. Keterangan Sistem

Sistem ICTServe menggunakan **Intranet Architecture** yang membolehkan:

**Akses Terkawal:**

- Staf MOTAC boleh log masuk dengan akaun sistem yang disediakan
- Walk-in/Kiosk Mode untuk pengguna sementara

**Fungsi Utama:**

```mermaid
graph TD
    A[Sistem ICTServe] --> B[Modul Helpdesk]
    A --> C[Modul Pinjaman Aset]
    A --> D[AI Chatbot]
    A --> E[Panel Pentadbir]
    
    B --> B1[Hantar Tiket]
    B --> B2[Jejak Status]
    B --> B3[Komunikasi]
    
    C --> C1[Mohon Pinjaman]
    C --> C2[Proses Kelulusan]
    C --> C3[Pengurusan Aset]
    
    D --> D1[FAQ Bot]
    D --> D2[Analisis Dokumen]
    D --> D3[Sokongan 24/7]
    
    E --> E1[Urus Tiket]
    E --> E2[Urus Aset]
    E --> E3[Laporan]
```

**Jenis Pengguna:**

| Jenis Pengguna | Akses | Fungsi Utama |
| :--- | :--- | :--- |
| **Walk-in/Kiosk Mode** | Akses sementara sistem | Hantar tiket, mohon pinjaman, semak status |
| **Staf MOTAC** | Log masuk dengan akaun sistem | Semua fungsi + dashboard peribadi |
| **Pentadbir** | Log masuk dengan kebenaran admin | Urus tiket, kelulusan, laporan |
| **Superuser** | Log masuk dengan kebenaran penuh | Semua fungsi + konfigurasi sistem |

## 3. KETERANGAN FUNGSI SISTEM

### 3.1. Senarai Fungsi Sistem

| Fungsi | Pengguna | Keterangan |
| :--- | :--- | :--- |
| **Penyerahan Tiket Helpdesk** | Staf MOTAC | Hantar aduan atau permintaan sokongan ICT |
| **Penjejakan Status Tiket** | Staf MOTAC | Semak status dan kemajuan tiket melalui dashboard peribadi |
| **Permohonan Pinjaman Aset** | Staf MOTAC | Mohon pinjaman peralatan ICT |
| **Penjejakan Status Pinjaman** | Staf MOTAC | Semak status permohonan pinjaman melalui dashboard |
| **AI FAQ Bot** | Staf MOTAC | Dapatkan jawapan pantas untuk soalan biasa |
| **Dashboard Peribadi** | Staf/Admin | Lihat ringkasan tiket dan pinjaman peribadi |
| **Pengurusan Tiket** | Admin | Proses dan selesaikan tiket helpdesk |
| **Kelulusan Pinjaman** | Admin | Luluskan atau tolak permohonan pinjaman |
| **Pengurusan Aset** | Admin | Urus inventori dan status aset |
| **Laporan dan Analitik** | Admin | Jana laporan prestasi dan statistik |

### 3.2. Perincian Keterangan bagi Fungsi Sistem

#### 3.2.1. Penyerahan Tiket Helpdesk

**Tujuan:** Membolehkan pengguna menghantar aduan atau permintaan sokongan ICT

**Pengawalan:** Tiada had bilangan tiket untuk staf berdaftar

**Input yang diperlukan:**

- Nama penghantar
- E-mel hubungan
- Subjek aduan
- Keterangan terperinci masalah
- Kategori masalah (perkakasan, perisian, rangkaian, dll.)
- Tahap keutamaan (rendah, normal, tinggi, kritikal)
- Lampiran (opsional)

**Output yang dijangka:**

- Nombor tiket unik (format: HD[YYYY\][MM\][0001-9999])
- E-mel pengesahan kepada staf yang disahkan
- Pautan ke dashboard peribadi untuk penjejakan status

**Hubungan dengan fungsi lain:**

- Berkait dengan sistem notifikasi e-mel
- Terintegrasi dengan AI Chatbot untuk cadangan penyelesaian
- Berkait dengan pengurusan aset jika melibatkan perkakasan

#### 3.2.2. Permohonan Pinjaman Aset

**Tujuan:** Membolehkan staf memohon pinjaman peralatan ICT untuk tugasan rasmi

**Pengawalan:** Hanya staf MOTAC yang layak, perlu kelulusan ketua bahagian

**Input yang diperlukan:**

- Maklumat pemohon (nama, jawatan, bahagian)
- Jenis aset yang diperlukan
- Tempoh pinjaman
- Tujuan penggunaan
- Lokasi penggunaan
- Pegawai bertanggungjawab (jika berbeza)

**Output yang dijangka:**

- Nombor permohonan unik (format: LA[YYYY][MM][0001-9999])
- E-mel pengesahan
- E-mel kelulusan kepada ketua bahagian
- Notifikasi status kepada pemohon

#### 3.2.3. AI FAQ Bot

**Tujuan:** Menyediakan jawapan pantas untuk soalan biasa dan sokongan 24/7

**Keupayaan:**

- Menjawab soalan dalam Bahasa Melayu
- Carian semantik dalam pangkalan pengetahuan
- Cadangan penyelesaian berdasarkan masalah serupa
- Penghalaan kepada pentadbir jika diperlukan

**Jenis soalan yang boleh dijawab:**

- Prosedur pinjaman aset
- Masalah ICT biasa
- Polisi dan garis panduan
- Status sistem dan penyelenggaraan

## 4. ARAHAN PENGGUNAAN SISTEM

### 4.1. Log Masuk Sistem

#### 4.1.1. Akses Walk-in/Kiosk Mode

1. **Buka pelayar web** dan navigasi ke alamat sistem ICTServe
2. **Pilih "Walk-in/Kiosk Mode"** di halaman utama
3. **Log masuk dengan akaun sistem** yang disediakan
4. **Pilih perkhidmatan** yang diperlukan:
   - Hantar Tiket Helpdesk
   - Mohon Pinjaman Aset
   - Semak Status
   - Tanya AI Chatbot

#### 4.1.2. Log Masuk Staf MOTAC

**Kaedah Log Masuk:**

1. **Navigasi ke sistem ICTServe**
2. **Klik "Log Masuk"**
3. **Masukkan kredensial** akaun sistem
4. **Sistem akan mengesahkan** maklumat pengguna
5. **Akses dashboard** peribadi

### 4.2. Proses Pengoperasian Sistem

#### 4.2.1. Menghantar Tiket Helpdesk

**Langkah 1: Akses Borang Tiket**

- Staf: Log masuk dan klik "Tiket Baharu" di dashboard
- Walk-in/Kiosk Mode: Log masuk dan pilih "Hantar Tiket"

**Langkah 2: Isi Maklumat Asas**

```
Nama: [Masukkan nama penuh]
E-mel: [Masukkan e-mel yang sah]
Telefon: [Nombor telefon untuk dihubungi]
Bahagian: [Pilih dari senarai atau tulis manual]
```

**Langkah 3: Keterangan Masalah**

```
Subjek: [Ringkasan masalah dalam 1 baris]
Kategori: [Pilih: Perkakasan/Perisian/Rangkaian/Lain-lain]
Keutamaan: [Rendah/Normal/Tinggi/Kritikal]
Keterangan: [Terangkan masalah dengan terperinci]
```

**Langkah 4: Lampiran (Opsional)**

- Klik "Pilih Fail" untuk melampirkan skrin tangkap atau dokumen
- Saiz maksimum: 10MB per fail
- Format yang diterima: PDF, JPG, PNG, DOC, DOCX

**Langkah 5: Hantar dan Pengesahan**

- Semak semula maklumat
- Klik "Hantar Tiket"
- Catat nombor tiket untuk rujukan
- Akses dashboard peribadi untuk penjejakan status

#### 4.2.2. Memohon Pinjaman Aset

**Langkah 1: Akses Borang Permohonan**

- Klik "Mohon Pinjaman Aset" di halaman utama atau dashboard

**Langkah 2: Maklumat Pemohon**

```
Nama: [Masukkan nama penuh]
E-mel: [Masukkan e-mel yang sah]
Nombor Staf: [Nombor staf MOTAC]
Jawatan: [Jawatan semasa]
Bahagian: [Bahagian/Unit kerja]
```

**Langkah 3: Butiran Permohonan**

```
Jenis Aset: [Pilih dari senarai atau nyatakan]
Kuantiti: [Bilangan unit diperlukan]
Tarikh Mula: [Tarikh mula pinjaman]
Tarikh Tamat: [Tarikh akhir pinjaman]
Tujuan: [Terangkan tujuan penggunaan]
Lokasi: [Tempat penggunaan aset]
```

**Langkah 4: Pegawai Bertanggungjawab**

- Pilih "Saya sendiri" atau nyatakan pegawai lain
- Jika pegawai lain, nyatakan maklumat pegawai tersebut
- Sistem akan mengesahkan maklumat

**Langkah 5: Hantar Permohonan**

- Semak semula maklumat
- Klik "Hantar Permohonan"
- Catat nombor permohonan
- E-mel pemberitahuan akan dihantar kepada pegawai kelulusan

#### 4.2.3. Menggunakan AI Chatbot

**Langkah 1: Akses Chatbot**

- Klik ikon chat di sudut kanan bawah
- Atau pilih "Tanya AI" di menu utama

**Langkah 2: Mulakan Perbualan**

```
Contoh soalan yang boleh ditanya:
- "Bagaimana cara memohon pinjaman laptop?"
- "Kenapa komputer saya lambat?"
- "Apa polisi penggunaan aset ICT?"
- "Bagaimana cara semak status tiket?"
```

**Langkah 3: Interaksi Lanjutan**

- AI akan memberikan jawapan dalam Bahasa Melayu
- Boleh bertanya soalan susulan
- Jika AI tidak dapat membantu, akan cadangkan hubungi pentadbir

#### 4.2.4. Menyemak Status

**Untuk Semua Pengguna:**

1. Log masuk ke dashboard
2. Lihat senarai tiket dan permohonan di halaman utama
3. Klik pada item untuk melihat butiran
4. Gunakan fungsi carian untuk mencari tiket atau permohonan tertentu

### 4.3. Penamatan dan Pengoperasi Semula Sistem

#### 4.3.1. Log Keluar

**Staf yang telah log masuk:**

1. Klik nama pengguna di sudut kanan atas
2. Pilih "Log Keluar"
3. Sistem akan kembali ke halaman utama

**Walk-in/Kiosk Mode:**

- Klik "Log Keluar" selepas selesai menggunakan sistem
- Sistem akan kembali ke halaman log masuk SSO
- Pastikan log keluar untuk keselamatan akaun

#### 4.3.2. Sesi Tamat Tempoh

- Sesi staf akan tamat selepas 2 jam tidak aktif
- Sistem akan paparkan amaran 5 minit sebelum tamat
- Klik "Sambung Sesi" untuk memanjangkan

#### 4.3.3. Pemulihan Sesi

Jika sesi terputus secara tidak dijangka:

1. Muat semula halaman pelayar
2. Log masuk semula jika diperlukan
3. Data yang belum disimpan mungkin hilang
4. Gunakan fungsi "Simpan Draf" untuk mengelakkan kehilangan data

## 5. PENGENDALIAN RALAT

### 5.1. Mesej Ralat Biasa

| Kod Ralat | Mesej | Maksud | Penyelesaian |
| :--- | :--- | :--- | :--- |
| **E001** | "E-mel tidak sah" | Format e-mel salah | Pastikan format e-mel betul (<contoh@motac.gov.my>) |
| **E002** | "Medan wajib kosong" | Ada medan yang tidak diisi | Isi semua medan yang bertanda * |
| **E003** | "Fail terlalu besar" | Lampiran melebihi 10MB | Kecilkan saiz fail atau gunakan format lain |
| **E004** | "Token tidak sah" | Token status salah/luput | Semak semula token atau minta token baharu |
| **E005** | "Sesi tamat tempoh" | Sesi pengguna telah luput | Log masuk semula |
| **E006** | "Akses ditolak" | Tiada kebenaran akses | Hubungi pentadbir untuk kebenaran |
| **E007** | "Sistem dalam penyelenggaraan" | Sistem sedang dikemaskini | Cuba lagi kemudian atau hubungi sokongan |
| **E008** | "Rangkaian terputus" | Masalah sambungan internet | Semak sambungan internet dan cuba lagi |
| **E009** | "Pengesahan gagal" | Kredensial tidak sah | Pastikan username/password betul, hubungi IT support jika masih gagal |
| **E010** | "Pengesahan status gagal" | Status pengguna tidak dapat disahkan | Hubungi pentadbir untuk kemaskini status |
| **E011** | "Perkhidmatan pengesahan tidak tersedia" | Perkhidmatan authentication tidak dapat dihubungi | Tunggu beberapa minit dan cuba lagi, hubungi Helpdesk BPM jika berterusan |
| **E012** | "Akses ditolak" | Pengguna tidak mempunyai akses yang sah | Pastikan akaun aktif, hubungi pentadbir sistem |

### 5.2. Langkah Penyelesaian Masalah

#### 5.2.1. Masalah Pengesahan

**Masalah:** Pengesahan gagal
**Penyelesaian:**

1. Pastikan username dan password betul
2. Semak caps lock tidak aktif
3. Pastikan akaun tidak dikunci (hubungi IT support jika dikunci)
4. Cuba log masuk dari komputer lain untuk test
5. Hubungi Helpdesk BPM jika masih gagal

**Masalah:** Perkhidmatan pengesahan tidak tersedia
**Penyelesaian:**

1. Tunggu 2-3 minit dan cuba lagi (mungkin maintenance sementara)
2. Semak sambungan rangkaian intranet
3. Hubungi Helpdesk BPM untuk status perkhidmatan
4. Cuba lagi selepas beberapa minit

**Masalah:** Pengesahan status gagal
**Penyelesaian:**

1. Hubungi pentadbir untuk kemaskini status
2. Pastikan maklumat peribadi adalah terkini
3. Jika baru bertugas, tunggu kemaskini status (biasanya 1-2 hari kerja)
4. Hubungi pentadbir sistem jika masalah berterusan

#### 5.2.2. Masalah Log Masuk

**Masalah:** Tidak dapat log masuk
**Penyelesaian:**

1. Bersihkan cache dan cookies pelayar
2. Cuba gunakan pelayar lain (Chrome, Firefox, Edge)
3. Pastikan JavaScript enabled
4. Hubungi pentadbir jika masih gagal

**Masalah:** E-mel pengesahan tidak diterima
**Penyelesaian:**

1. Semak folder spam/junk
2. Pastikan e-mel @motac.gov.my aktif
3. Minta hantar semula e-mel pengesahan
4. Hubungi IT support jika masih tiada

#### 5.2.3. Masalah Penyerahan Borang

**Masalah:** Borang tidak dapat dihantar
**Penyelesaian:**

1. Semak semua medan wajib telah diisi
2. Pastikan sambungan internet stabil
3. Cuba muat semula halaman
4. Gunakan pelayar yang berbeza
5. Hubungi sokongan teknikal

**Masalah:** Lampiran tidak dapat dimuat naik
**Penyelesaian:**

1. Semak saiz fail tidak melebihi 10MB
2. Pastikan format fail disokong
3. Cuba kompres fail
4. Gunakan format PDF untuk dokumen
5. Hubungi pentadbir jika masih gagal

#### 5.2.3. Masalah AI Chatbot

**Masalah:** AI tidak memberikan jawapan yang tepat
**Penyelesaian:**

1. Cuba tulis soalan dengan lebih jelas
2. Gunakan kata kunci yang spesifik
3. Tanya dalam Bahasa Melayu
4. Jika masih tidak membantu, hubungi pentadbir

### 5.3. Bantuan Helpdesk

#### 5.3.1. Saluran Sokongan

**Sokongan Dalam Talian:**

- **AI Chatbot**: Tersedia 24/7 untuk soalan asas
- **Sistem Tiket**: Hantar tiket untuk masalah teknikal
- **E-mel**: <helpdesk@motac.gov.my>

**Sokongan Telefon:**

- **Waktu Pejabat**: Isnin - Jumaat, 8:00 AM - 5:00 PM
- **Nombor**: 03-xxxx-xxxx (sambungan helpdesk)
- **Kecemasan**: Hubungi pentadbir sistem terus

#### 5.3.2. Maklumat Yang Perlu Disediakan

Apabila menghubungi sokongan, sediakan:

- Nama dan jawatan
- E-mel yang digunakan
- Keterangan masalah yang terperinci
- Langkah yang telah dicuba
- Skrin tangkap ralat (jika ada)
- Nombor tiket (jika berkaitan)

#### 5.3.3. Masa Respons Sokongan

| Tahap Keutamaan | Masa Respons | Masa Penyelesaian |
| :--- | :--- | :--- |
| **Kritikal** | 1 jam | 4 jam |
| **Tinggi** | 4 jam | 1 hari kerja |
| **Normal** | 1 hari kerja | 3 hari kerja |
| **Rendah** | 2 hari kerja | 5 hari kerja |

---

**PENUTUP**

Manual ini akan dikemaskini mengikut keperluan dan perubahan sistem. Untuk versi terkini, sila rujuk sistem atau hubungi pentadbir. Sebarang cadangan penambahbaikan manual ini boleh dihantar melalui sistem tiket.

**TAMAT DOKUMEN / END OF DOCUMENT**

*Dokumen ini adalah hak milik Kementerian Pelancongan, Seni dan Budaya Malaysia (MOTAC) dan tertakluk kepada Akta Rahsia Rasmi 1972.*
