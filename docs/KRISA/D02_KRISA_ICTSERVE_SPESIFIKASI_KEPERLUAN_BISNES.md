# D02 DOKUMEN SPESIFIKASI KEPERLUAN BISNES (BRS)

**SISTEM ICTSERVE**

*(Sistem Pengurusan Helpdesk dan Pinjaman Aset ICT)*

| | |
| :--- | :--- |
| **NAMA AGENSI** | : Bahagian Pengurusan Maklumat (BPM) |
| **NAMA AGENSI INDUK** | : Kementerian Pelancongan, Seni dan Budaya Malaysia (MOTAC) |
| **TARIKH DOKUMEN** | : 24 Disember 2025 |
| **VERSI DOKUMEN** | : 4.0 |

---

## i. Keterangan Dokumen

Dokumen ini menerangkan keperluan bisnes bagi pembangunan **Sistem ICTServe** sebagai platform web untuk pengurusan tiket helpdesk dan permohonan pinjaman aset ICT bagi kegunaan warga kerja MOTAC. Sistem ini akan menggantikan proses manual sedia ada dengan automasi untuk meningkatkan kecekapan perkhidmatan ICT.

Dokumen ini mematuhi piawaian **KRISA (Kejuruteraan Sistem Aplikasi Sektor Awam)** yang ditetapkan oleh MAMPU.

---

## ii. Semakan dan Pengesahan Dokumen

Seksyen ini adalah ruangan bagi pegawai-pegawai yang bertanggungjawab untuk melakukan semakan dan pengesahan kepada maklumat-maklumat yang terkandung di dalam dokumen ini.

### SEMAKAN DOKUMEN

| Disemak Oleh | Jawatan | Tandatangan | Tarikh Semakan |
| :--- | :--- | :--- | :--- |
| **Puan Ketua Pasukan** | Penganalisis Sistem Kanan | [Tandatangan Digital] | 24 Disember 2025 |
| **En. Arkitek Sistem** | Ketua Pembangun Sistem | [Tandatangan Digital] | 24 Disember 2025 |

### PENGESAHAN DOKUMEN

| Disahkan Oleh | Jawatan | Tandatangan | Tarikh Pengesahan |
| :--- | :--- | :--- | :--- |
| **Pengarah BPM** | Pengurus Projek | [Tandatangan Digital] | 24 Disember 2025 |
| **Ketua Pegawai Digital (CDO)** | Penasihat Projek | [Tandatangan Digital] | 24 Disember 2025 |

---

## iii. Kawalan Dokumen

### KAWALAN DOKUMEN

| No. Versi | Tarikh | Ringkasan Pindaan | Penyedia |
| :--- | :--- | :--- | :--- |
| 1.0 | 01-09-2025 | Versi awal BRS. | Pasukan BPM |
| 2.0 | 17-10-2025 | Penyeragaman KRISA dan modul asas. | Pasukan BPM |
| 3.0 | 31-10-2025 | Penambahan ciri SSO dan AI. | Pasukan BPM |
| 4.0 | 24 Disember 2025 | Pematuhan polisi keselamatan dan finalisasi keperluan. | Pasukan BPM |

**Nota Penentuan Nombor Versi:**

- Pindaan kecil/sederhana: Perubahan angka selepas titik perpuluhan (contoh: 3.6 → 3.7)
- Pindaan besar: Perubahan angka utama (contoh: 3.7 → 4.0)

---

## iv. Kandungan

1. [PENGENALAN](#1-pengenalan) ... 5
   - 1.1 Tujuan Bisnes
   - 1.2 Skop Bisnes
   - 1.3 Gambaran Keseluruhan Projek
   - 1.4 Senarai Pemegang Taruh
2. [KEPERLUAN PENGURUSAN BISNES](#2-keperluan-pengurusan-bisnes) ... 8
   - 2.1 Matlamat dan Objektif
   - 2.2 Arkitektur Bisnes
   - 2.3 Arkitektur Maklumat
3. [KEPERLUAN PENGOPERASIAN BISNES](#3-keperluan-pengoperasian-bisnes) ... 12
   - 3.1 Keperluan Fungsi Bisnes
   - 3.2 Keperluan Proses Bisnes
   - 3.3 Pengiraan Saiz Sistem Aplikasi
4. [LAMPIRAN](#4-lampiran) ... 30

---

## v. Senarai Gambarajah

- Gambarajah 1.1: Struktur Organisasi ICTServe ... §1.3

---

## vi. Senarai Jadual

- Jadual 1.1: Senarai Pemegang Taruh ... §1.4
- Jadual 2.1: Matlamat dan Objektif ... §2.1
- Jadual 3.1: Keterangan Fungsi Bisnes ... §3.1
- Jadual 3.2: Pengiraan Function Point ... §3.3

---

## vii. Definisi dan Akronim

### a. Akronim

| Akronim | Keterangan |
| :--- | :--- |
| **AI** | *Artificial Intelligence* (Kecerdasan Buatan) |
| **BPM** | Bahagian Pengurusan Maklumat |
| **BRS** | *Business Requirement Specification* (Spesifikasi Keperluan Bisnes) |
| **ICT** | *Information and Communication Technology* (Teknologi Maklumat dan Komunikasi) |
| **KRISA** | Kejuruteraan Sistem Aplikasi Sektor Awam |
| **LLM** | *Large Language Model* (Model Bahasa Besar) |
| **MAMPU** | Unit Pemodenan Tadbiran dan Perancangan Pengurusan Malaysia |
| **MOTAC** | Kementerian Pelancongan, Seni dan Budaya Malaysia |
| **PDPA** | Akta Perlindungan Data Peribadi 2010 |
| **RAG** | *Retrieval-Augmented Generation* (Penjanaan Berbantu Perolehan) |
| **SLA** | *Service Level Agreement* (Perjanjian Tahap Perkhidmatan) |
| **SSO** | *Single Sign-On* (Log Masuk Tunggal) |
| **WCAG** | *Web Content Accessibility Guidelines* (Garis Panduan Kebolehcapaian Kandungan Web) |

### b. Definisi

| Terma/Istilah | Definisi |
| :--- | :--- |
| **Helpdesk** | Perkhidmatan sokongan teknikal untuk menyelesaikan masalah ICT pengguna. |
| **Pinjaman Aset** | Proses peminjaman peralatan ICT untuk kegunaan rasmi. |
| **SSO** | Single Sign-On - sistem log masuk tunggal menggunakan kredensial organisasi. |
| **AI Chatbot** | Sistem bantuan automatik untuk menjawab pertanyaan lazim. |
| **SLA** | Service Level Agreement - perjanjian tahap perkhidmatan yang dijanjikan. |

---

## viii. Sumber Rujukan

1. **Buku Panduan Kejuruteraan Sistem Aplikasi Sektor Awam (KRISA)**. MAMPU.
2. **Polisi Keselamatan Siber MOTAC** - Garis panduan keselamatan maklumat organisasi.
3. **Pelan Strategik Pendigitalan MOTAC 2022-2026** - Hala tuju digital organisasi.
4. **MyGOV Digital Service Standards** - Piawaian perkhidmatan digital kerajaan.
5. **Pekeliling Am Bilangan 2 Tahun 2012** - Tatacara Pengurusan Aset Kerajaan.
6. **ISO/IEC/IEEE 29148:2018** - Systems and software engineering requirements.
7. **Personal Data Protection Act 2010 (PDPA)** - Undang-undang perlindungan data peribadi.

---

## 1. PENGENALAN

### 1.1 Tujuan Bisnes

Bahagian Pengurusan Maklumat (BPM), MOTAC memerlukan sistem bersepadu untuk menggantikan proses manual dalam menguruskan perkhidmatan ICT. Sistem ICTServe dibangunkan untuk memodenkan pengurusan **Helpdesk** dan **Pinjaman Aset ICT** dengan ciri automasi dan bantuan pintar.

Sistem ini bertujuan untuk:

a) **Meningkatkan kecekapan** - Automasi proses dan pengurangan masa pemprosesan
b) **Memudahkan akses** - Portal web yang mudah digunakan untuk semua warga MOTAC  
c) **Meningkatkan ketelusan** - Penjejakan status real-time dan audit trail
d) **Mengurangkan konflik** - Sistem pengesanan pertindihan tempahan aset
e) **Bantuan pintar** - AI chatbot untuk penyelesaian masalah pantas
f) **Pematuhan keselamatan** - Mengikut polisi keselamatan organisasi

### 1.2 Skop Bisnes

Seksyen ini menjelaskan penentuan skop bagi domain bisnes organisasi yang terlibat.

Projek ini merangkumi skop bisnes berikut:

a) **Pengurusan Aduan ICT**: Pelaporan kerosakan perkakasan/perisian, penjejakan status, dan komunikasi antara pengguna dan teknikal.

b) **Pengurusan Pinjaman Aset**: Tempahan peralatan ICT, semakan ketersediaan, dan kelulusan pegawai secara digital.

c) **Bantuan Pintar**: Sistem chatbot untuk menjawab pertanyaan lazim dan memberikan bantuan automatik.

d) **Pengurusan Pengguna**: Pendaftaran pengguna, pengesahan identiti, dan pengurusan profil.

e) **Pemantauan & Laporan**: Dashboard prestasi, audit trail, dan laporan statistik untuk pengurusan.

f) **Integrasi Sistem**: API untuk integrasi dengan sistem sedia ada dan aplikasi masa depan.

### 1.3 Gambaran Keseluruhan Projek

Seksyen ini menerangkan struktur organisasi yang berkaitan dengan domain bisnes serta hubungannya dengan entiti luar.

Sistem ICTServe bertindak sebagai hab utama perkhidmatan ICT MOTAC. Ia menghubungkan Warga MOTAC (pengguna akhir) dengan Pasukan Teknikal BPM (penyedia perkhidmatan) dan Unit Aset untuk memudahkan pengurusan perkhidmatan ICT secara bersepadu.

**Struktur Organisasi:**

- **Pengurusan MOTAC**: Menetapkan dasar dan memantau prestasi
- **BPM**: Pemilik sistem dan pengurusan operasi
- **Unit Teknikal ICT**: Pengurusan helpdesk dan penyelesaian masalah
- **Unit Aset ICT**: Pengurusan inventori dan pinjaman aset
- **Warga MOTAC**: Pengguna akhir sistem

### 1.4 Senarai Pemegang Taruh

Seksyen ini menyenaraikan dan menerangkan pemegang-pemegang taruh yang terlibat dengan domain bisnes berkenaan.

**Jadual 1.1: Senarai Pemegang Taruh**

| Pemegang Taruh | Peranan/Tanggungjawab | Kepentingan |
| :--- | :--- | :--- |
| **Pengurusan Tertinggi MOTAC** | Memantau prestasi perkhidmatan ICT dan pematuhan dasar. Menetapkan KPI dan budget. | Tinggi |
| **BPM** | Pemilik sistem dan pengurusan operasi harian. Bertanggungjawab untuk SLA dan kualiti perkhidmatan. | Tinggi |
| **Pentadbir Sistem** | Pengurusan konfigurasi sistem dan audit keselamatan. | Tinggi |
| **Warga MOTAC** | Pengguna akhir sistem untuk aduan dan pinjaman aset. | Tinggi |
| **Pegawai Kelulusan** | Meluluskan permohonan pinjaman aset. | Sederhana |

---

## 2. KEPERLUAN PENGURUSAN BISNES

### 2.1 Matlamat dan Objektif

Seksyen ini menyenaraikan dan menerangkan matlamat, objektif dan hasil bisnes yang ingin dicapai melalui pelaksanaan sistem yang akan dibangunkan.

#### 2.1.1 Matlamat Utama

Mewujudkan persekitaran pengurusan perkhidmatan ICT yang responsif dan mesra pengguna bagi menyokong operasi harian MOTAC.

#### 2.1.2 Objektif Sistem

**Jadual 2.1: Matlamat dan Objektif**

| No. | Objektif | Keterangan | Sasaran | Tempoh |
| :--- | :--- | :--- | :--- | :--- |
| 1 | **Aksesibiliti Tinggi** | Menyediakan akses mudah untuk semua warga MOTAC. | 100% Warga MOTAC boleh akses sistem. | Fasa 1 |
| 2 | **Pengurangan Masa Respons** | Menggunakan sistem automatik untuk penyelesaian masalah pantas. | Pengurangan 30% masa penyelesaian tiket. | Fasa 2 |
| 3 | **Integriti Data** | Merekod semua transaksi dengan jejak audit yang lengkap. | 100% transaksi diaudit. | Fasa 1 |
| 4 | **Pemantauan Efisien** | Memaparkan status sistem dan SLA secara masa nyata. | Uptime 99.5%, SLA compliance 95%. | Fasa 2 |
| 5 | **Keselamatan Data** | Memastikan data diproses dengan selamat mengikut polisi organisasi. | 100% pematuhan polisi keselamatan. | Fasa 1 |

### 2.2 Arkitektur Bisnes

Seksyen ini menjelaskan dan menyediakan Arkitektur Bisnes yang berkaitan dengan sistem yang akan dibangunkan.

#### 2.2.1 Komponen Arkitektur Bisnes

Sistem ICTServe beroperasi dalam ekosistem MOTAC dengan komponen berikut:

- **Medium Perkhidmatan**: Aplikasi Web, Notifikasi E-mel, API
- **Pengguna Perkhidmatan**: Staf MOTAC, Admin, Pegawai Kelulusan
- **Perkhidmatan Utama**: Helpdesk, Pinjaman Aset, Pemantauan, Laporan
- **Maklumat (Data)**: Profil Pengguna, Tiket Aduan, Inventori Aset, Audit Trail

**Rajah 2: Arkitektur Bisnes ICTServe**

### 2.3 Arkitektur Maklumat

Seksyen ini menerangkan Arkitektur Maklumat bagi sistem aplikasi yang akan dibangunkan.

#### 2.3.1 Hubungan Pengguna, Proses dan Maklumat

Sistem ICTServe menghubungkan pengguna dengan proses bisnes dan maklumat yang berkaitan:

| Pengguna | Proses Bisnes | Maklumat Terlibat |
| :--- | :--- | :--- |
| **Warga MOTAC** | Menghantar Aduan / Memohon Aset | Profil Pengguna, Butiran Tiket, Tarikh Pinjaman |
| **Admin BPM** | Mengurus Tiket / Aset / Notifikasi | Status Tiket, Inventori Aset, Log Audit |
| **Pegawai Kelulusan** | Meluluskan Pinjaman | Token Kelulusan, Butiran Permohonan |

---

## 3. KEPERLUAN PENGOPERASIAN BISNES

### 3.1 Keperluan Fungsi Bisnes

#### 3.1.1 Penggunaan Notasi

Seksyen ini menyenaraikan notasi-notasi yang akan digunakan untuk menyediakan Model Fungsi Bisnes.

Jadual berikut menerangkan notasi ID fungsi bisnes:

| Notasi | Keterangan |
| :--- | :--- |
| **BF-ICT-XX-YY** | Format ID Fungsi Bisnes untuk sistem ICTServe. |
| **BF** | *Business Function* (Fungsi Bisnes). |
| **ICT** | Kod Sistem ICTServe. |
| **XX** | Kod Modul (cth: AD=Aduan, PJ=Pinjaman, AI=Kecerdasan Buatan, AM=Akses Management). |
| **YY** | Kod Sub-fungsi (cth: 01, 02, 03). |

#### 3.1.2 Model Fungsi Bisnes

Seksyen ini menyediakan Model Fungsi Bisnes yang terdiri daripada Struktur Hirarki Fungsi serta keterangan bagi fungsi-fungsi berkenaan.

##### a) Struktur Hirarki Fungsi Bisnes

Sistem ICTServe mempunyai fungsi bisnes utama berikut:

- **BF-ICT-AM**: Pengurusan Akses dan Pengguna
- **BF-ICT-AD**: Pengurusan Aduan Helpdesk  
- **BF-ICT-PJ**: Pengurusan Pinjaman Aset
- **BF-ICT-PM**: Pemantauan dan Audit
- **BF-ICT-JL**: Dashboard dan Laporan

##### b) Keterangan Fungsi Bisnes

**Jadual 3.1: Keterangan Fungsi Bisnes**

| ID Fungsi | Nama Fungsi | Keterangan | Pengguna Terlibat |
| :--- | :--- | :--- | :--- |
| **BF-ICT-AM** | **Pengurusan Akses** | Mengurus pendaftaran, log masuk, dan profil pengguna | Semua Pengguna |
| **BF-ICT-AD** | **Pengurusan Aduan** | Menghantar, memantau, dan menyelesaikan tiket kerosakan ICT | Warga MOTAC, Admin |
| **BF-ICT-PJ** | **Pengurusan Pinjaman** | Memohon peralatan, menyemak stok, meluluskan, dan memulangkan aset | Warga MOTAC, Admin, Pelulus |
| **BF-ICT-PM** | **Pemantauan & Audit** | Mengurus pemantauan prestasi dan audit keselamatan | Admin, Superuser |
| **BF-ICT-JL** | **Dashboard & Laporan** | Mengurus data rujukan dan menjana laporan prestasi | Admin, Superuser |

#### 3.1.3 Senarai Pengguna

Seksyen ini menyenaraikan senarai pengguna-pengguna yang terlibat secara langsung dengan fungsi bisnes.

| Pengguna | Peranan | Tanggungjawab | Fungsi Terlibat |
| :--- | :--- | :--- | :--- |
| **Staf MOTAC** | Pengguna Berdaftar | Membuat permohonan, melihat sejarah, menguruskan profil | Semua Modul |
| **Admin** | Pegawai Operasi | Memproses tiket, menyediakan aset, memantau SLA | Operasi Harian |
| **Pegawai Kelulusan** | Pembuat Keputusan | Meluluskan/menolak permohonan pinjaman aset | Pinjaman Aset |

### 3.2 Keperluan Proses Bisnes

#### 3.2.1 Penggunaan Notasi

Seksyen ini menyenaraikan notasi-notasi yang akan digunakan untuk menyediakan Model Proses.

Menggunakan notasi standard carta alir dengan simbol-simbol berikut:

| Simbol | Keterangan |
| :--- | :--- |
| Oval | Titik mula atau tamat proses |
| Segi empat tepat | Aktiviti atau proses yang dilaksanakan |
| Berlian | Titik keputusan dengan pilihan Ya/Tidak |

#### 3.2.2 Model Proses Bisnes

Seksyen ini menyediakan Model Proses Bisnes yang merangkumi Aliran Proses Bisnes dan Definisi Fungsi Bisnes.

##### a) Proses Pengurusan Aduan (Helpdesk)

**Aliran Proses Utama:**

1. Pengguna akses sistem dan log masuk
2. Pengguna isi borang aduan dengan butiran masalah
3. Sistem jana nombor tiket dan hantar notifikasi
4. Admin semak dan proses tiket
5. Pengguna dimaklumkan status penyelesaian

##### b) Proses Pinjaman Aset ICT

**Aliran Proses Utama:**

1. Pengguna pilih aset dan tarikh pinjaman
2. Sistem semak ketersediaan aset
3. Pengguna hantar permohonan dengan justifikasi
4. Pegawai kelulusan semak dan buat keputusan
5. Sistem hantar notifikasi keputusan kepada pemohon

##### c) Proses Pemantauan dan Laporan

**Aliran Proses Utama:**

1. Sistem kumpul data prestasi secara automatik
2. Admin jana laporan mengikut tempoh yang diperlukan
3. Sistem papar dashboard dengan metrik utama
4. Pengurusan semak laporan untuk membuat keputusan

### 3.3 Pengiraan Saiz Sistem Aplikasi

Seksyen ini menyediakan Pengiraan Saiz Sistem Aplikasi dengan menggunakan kaedah Function Points Analysis.

#### 3.3.1 Komponen Function Points

**Jadual 3.2: Pengiraan Function Point**

| Komponen | Bilangan | Kompleksiti | Faktor Pemberat | Jumlah (UFP) |
| :--- | :---: | :---: | :---: | :---: |
| **External Input (EI)** | 10 | Sederhana | 4 | 40 |
| **External Output (EO)** | 8 | Sederhana | 5 | 40 |
| **External Inquiry (EQ)** | 12 | Sederhana | 4 | 48 |
| **Internal Logical File (ILF)** | 8 | Sederhana | 7 | 56 |
| **External Interface File (EIF)** | 3 | Sederhana | 5 | 15 |
| **JUMLAH (UFP)** | | | | **199** |

#### 3.3.2 Anggaran Usaha dan Kos

Berdasarkan 199 Function Points:

- **Anggaran Usaha**: 199 FP × 8 jam/FP = **1,592 jam** ≈ **199 hari manusia**
- **Anggaran Tempoh**: 199 hari ÷ 3 pembangun = **66 hari** ≈ **3 bulan**

*Nota: Anggaran ini adalah untuk pembangunan sistem asas. Ciri lanjutan akan menambah FP di fasa akan datang.*

---

## 4. LAMPIRAN

Seksyen ini merupakan ruangan untuk menyertakan dokumen-dokumen sokongan yang perlu dirujuk.

### Lampiran A: Borang Rujukan

- **Borang Aduan Kerosakan ICT** - Template borang untuk pelaporan masalah teknikal
- **Borang Permohonan Pinjaman Aset ICT** - Template permohonan pinjaman peralatan
- **Borang Kelulusan Pinjaman** - Template keputusan pegawai pelulus

### Lampiran B: Dokumen Rujukan

- **Polisi Keselamatan ICT MOTAC** - Garis panduan keselamatan organisasi
- **Manual Prosedur Pengurusan Aset** - Prosedur sedia ada untuk pengurusan aset
- **SLA Perkhidmatan ICT** - Perjanjian tahap perkhidmatan yang dijanjikan

---

**TAMAT DOKUMEN / END OF DOCUMENT**

*Dokumen ini adalah hak milik Kementerian Pelancongan, Seni dan Budaya Malaysia (MOTAC) dan tertakluk kepada Akta Rahsia Rasmi 1972.*

---

## Tandatangan & Kelulusan
### Signatures & Approvals

| Peranan | Nama | Tandatangan | Tarikh |
|---------|------|-------------|--------|
| **Disediakan oleh:** | Pasukan BPM | [Tandatangan Digital] | 24 Disember 2025 |
| **Disemak oleh:** | Puan Ketua Pasukan | [Tandatangan Digital] | 24 Disember 2025 |
| **Diluluskan oleh:** | Pengarah BPM | [Tandatangan Digital] | 24 Disember 2025 |

---

**Nota:** Dokumen ini adalah harta intelek MOTAC dan tidak boleh digunakan tanpa kebenaran bertulis.

**Hak Cipta © 2025 MOTAC. Hak Cipta Terpelihara.**
