# DOKUMEN SPESIFIKASI KEPERLUAN BISNES (BRS)

## SISTEM PENGURUSAN PERKHIDMATAN ICT (ICTSERVE)

![Logo Agensi](logo_placeholder.png)

| Medan | Nilai |
| :--- | :--- |
| **NAMA AGENSI** | : BAHAGIAN PENGURUSAN MAKLUMAT (BPM) |
| **NAMA AGENSI INDUK** | : KEMENTERIAN PELANCONGAN, SENI DAN BUDAYA (MOTAC) |
| **TARIKH DOKUMEN** | : 14 DISEMBER 2025 |
| **VERSI DOKUMEN** | : 3.6.1 |

---

## i. Keterangan Dokumen

Dokumen ini menerangkan keperluan bisnes dan pengguna bagi pembangunan **Sistem Pengurusan Perkhidmatan ICT (ICTServe)** Versi 3.6.1. Kandungannya merangkumi maklumat terperinci skop bisnes, gambaran keseluruhan projek, pemegang taruh yang terlibat, keperluan pengurusan bisnes, keperluan pengoperasian bisnes dan keperluan proses bisnes yang merangkumi senibina **True Hybrid** dan integrasi **Cloud Hybrid AI** (Ollama + AWS Bedrock). Dokumen ini akan menjadi input utama kepada penyediaan Spesifikasi Keperluan Sistem (SRS) dan mematuhi piawaian KRISA (Kejuruteraan Sistem Aplikasi Sektor Awam).

---

## ii. Semakan dan Pengesahan Dokumen

Seksyen ini adalah ruangan bagi pegawai-pegawai yang bertanggungjawab untuk melakukan semakan dan pengesahan kepada maklumat-maklumat yang terkandung di dalam dokumen ini.

### SEMAKAN DOKUMEN

| Disemak Oleh | Jawatan | Tandatangan | Tarikh Semakan |
| :--- | :--- | :--- | :--- |
| **Puan Ketua Pasukan** | Pengurus Projek ICT | | 14-12-2025 |
| **En. Arkitek Sistem** | Ketua Teknikal | | 14-12-2025 |

### PENGESAHAN DOKUMEN

| Disahkan Oleh | Jawatan | Tandatangan | Tarikh Pengesahan |
| :--- | :--- | :--- | :--- |
| **Pengarah BPM** | Pemilik Projek | | |
| **Ketua Pegawai Digital (CDO)** | Penasihat Projek | | |

---

## iii. Kawalan Dokumen

### KAWALAN DOKUMEN

| No. Versi | Tarikh | Ringkasan Pindaan | Penyedia |
| :--- | :--- | :--- | :--- |
| 1.0 | 01-09-2025 | Versi awal BRS. | Pasukan BPM |
| 2.0 | 17-10-2025 | Penyeragaman KRISA dan modul asas. | Pasukan BPM |
| 3.0 | 31-10-2025 | Peningkatan kepada Laravel 12 & Filament 4. | Pasukan BPM |
| 3.5 | 30-11-2025 | Penambahan ciri *True Hybrid Architecture* (Self-Registration). | Pasukan BPM |
| 3.6.1 | 14-12-2025 | Integrasi *Cloud Hybrid AI* (Ollama + Bedrock) dan Bahasa Melayu sepenuhnya. | Pasukan BPM |

**Nota Penentuan Nombor Versi:**

- Pindaan kecil/sederhana: Perubahan angka selepas titik perpuluhan (contoh: 1.2 → 1.3)
- Pindaan besar: Perubahan angka utama (contoh: 1.2 → 2.0)

---

## iv. Kandungan

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
   - 1.1 Tujuan Bisnes
   - 1.2 Skop Bisnes
   - 1.3 Gambaran Keseluruhan Bisnes
   - 1.4 Senarai Pemegang Taruh

2. **KEPERLUAN PENGURUSAN BISNES**
   - 2.1 Matlamat dan Objektif
   - 2.2 Arkitektur Bisnes
   - 2.3 Arkitektur Maklumat

3. **KEPERLUAN PENGOPERASIAN BISNES**
   - 3.1 Keperluan Fungsi Bisnes
   - 3.2 Keperluan Proses Bisnes
   - 3.3 Pengiraan Saiz Sistem Aplikasi

4. **LAMPIRAN**

---

## v. Senarai Gambarajah

Seksyen ini merupakan ruangan untuk memasukkan senarai nombor rujukan bagi gambarajah-gambarajah yang terkandung di dalam dokumen berserta nombor muka surat yang terlibat.

### SENARAI GAMBARAJAH

| No. | Tajuk Gambarajah | Muka Surat |
| :--- | :--- | :--- |
| Rajah 1 | Gambaran Bisnes Pengurusan ICTServe | 5 |
| Rajah 2 | Arkitektur Bisnes ICTServe | 7 |
| Rajah 3 | Arkitektur Maklumat Sistem | 8 |
| Rajah 4 | Hirarki Fungsi Bisnes | 10 |
| Rajah 5 | Aliran Proses PFD-ICT-AD (Pengurusan Aduan) | 13 |
| Rajah 6 | Aliran Proses PFD-ICT-PJ (Pinjaman Aset) | 18 |
| Rajah 7 | Aliran Proses PFD-ICT-AI (Bantuan Pintar) | 24 |

---

## vi. Senarai Jadual

Seksyen ini merupakan ruangan untuk memasukkan senarai nombor rujukan bagi jadual-jadual yang terkandung di dalam dokumen berserta nombor muka surat yang terlibat.

### SENARAI JADUAL

| No. | Tajuk Jadual | Muka Surat |
| :--- | :--- | :--- |
| Jadual 1 | Senarai Pemegang Taruh | 6 |
| Jadual 2 | Matlamat dan Objektif | 7 |
| Jadual 3 | Keterangan Fungsi Bisnes | 11 |
| Jadual 4 | Definisi Aktiviti PFD-ICT-AD-01 | 14 |
| Jadual 5 | Definisi Aktiviti PFD-ICT-PJ-01 | 19 |
| Jadual 6 | Definisi Aktiviti PFD-ICT-AI-01 | 25 |
| Jadual 7 | Pengiraan Function Point | 30 |

---

## vii. Definisi dan Akronim

### a. Akronim

Sub seksyen ini adalah ruangan untuk menerangkan akronim-akronim yang digunakan di dalam dokumen.

| Akronim | Keterangan |
| :--- | :--- |
| **AI** | *Artificial Intelligence* (Kecerdasan Buatan) |
| **BPM** | Bahagian Pengurusan Maklumat |
| **BRS** | *Business Requirement Specification* |
| **ICT** | *Information and Communication Technology* |
| **KRISA** | Kejuruteraan Sistem Aplikasi Sektor Awam |
| **LLM** | *Large Language Model* |
| **MOTAC** | Kementerian Pelancongan, Seni dan Budaya |
| **PDPA** | Akta Perlindungan Data Peribadi |
| **RAG** | *Retrieval-Augmented Generation* |
| **SLA** | *Service Level Agreement* |

### b. Definisi

Sub seksyen ini adalah ruangan untuk menerangkan definisi bagi terma atau istilah yang digunakan di dalam dokumen.

| Terma/Istilah | Definisi |
| :--- | :--- |
| **True Hybrid** | Model akses yang membenarkan pengguna menggunakan sistem sama ada melalui log masuk (staf) atau sebagai tetamu tanpa akaun. |
| **Ollama** | Enjin AI tempatan untuk memproses data sensitif dan FAQ asas. |
| **Bedrock** | Perkhidmatan AI awan (AWS) untuk pemprosesan penaakulan kompleks. |
| **Filament** | Rangka kerja panel pentadbiran yang digunakan oleh Admin dan Superuser. |

---

## viii. Sumber Rujukan

Seksyen ini adalah ruangan untuk menyenaraikan semua sumber-sumber rujukan yang digunakan di dalam penyediaan dokumen ini, contohnya seperti surat pekeliling perkhidmatan, manual prosedur kerja, garis-garis panduan, dokumen-dokumen piawaian ISO/IEC/IEEE dan bahan rujukan lain yang berkaitan.

### SUMBER RUJUKAN

1. Buku Panduan Kejuruteraan Sistem Aplikasi Sektor Awam (KRISA). MAMPU.
2. MyGOV Digital Service Standards v2.1.0.
3. Dasar Keselamatan ICT (DKICT) MOTAC.
4. Pekeliling Am Bilangan 2 Tahun 2012 (Tatacara Pengurusan Aset).
5. ISO/IEC/IEEE 29148:2018 Systems and software engineering — Life cycle processes — Requirements engineering.

---

## 1. PENGENALAN

### 1.1 Tujuan Bisnes

Seksyen ini menerangkan latarbelakang, sebab-sebab dan bagaimana Sistem Pengurusan Perkhidmatan ICT (ICTServe) yang akan dibangunkan dapat membantu dan menyumbang untuk mencapai objektif bisnes.

Bahagian Pengurusan Maklumat (BPM), MOTAC memerlukan satu sistem bersepadu yang lebih efisien untuk menggantikan proses manual dan sistem legasi dalam menguruskan perkhidmatan ICT. Sistem ICTServe dibangunkan untuk memodenkan pengurusan **Helpdesk (Aduan Kerosakan)** dan **Pinjaman Aset ICT** dengan ciri automasi pintar (AI) dan aksesibiliti yang tinggi.

Sistem ini bertujuan untuk:

a) Menyelesaikan masalah pertindihan tempahan aset.
b) Mempercepatkan masa respons aduan melalui bantuan AI Chatbot.
c) Memudahkan akses warga MOTAC melalui konsep *True Hybrid* (pilihan Log Masuk atau Tetamu).
d) Memastikan pematuhan SLA dan audit yang telus.

### 1.2 Skop Bisnes

Seksyen ini menjelaskan penentuan skop bagi domain bisnes organisasi yang terlibat.

Projek ini merangkumi skop bisnes berikut:

a) **Pengurusan Aduan ICT**: Pelaporan kerosakan perkakasan/perisian, penjejakan status, dan komunikasi antara pengguna dan teknikal.
b) **Pengurusan Pinjaman Aset**: Tempahan peralatan ICT, semakan ketersediaan, dan kelulusan pegawai secara digital (token e-mel).
c) **Bantuan Pintar (AI)**: Chatbot hibrid (Ollama + Bedrock) untuk menjawab soalan lazim dan menganalisis dokumen sokongan.
d) **Pengurusan Pengguna**: Pendaftaran kendiri (*Self-registration*) staf, integrasi SSO Google (Opsyenal), dan pengurusan profil.
e) **Pemantauan & Laporan**: Dashboard prestasi masa nyata (Laravel Pulse), audit trail, dan laporan statistik KPI.

### 1.3 Gambaran Keseluruhan Projek

Seksyen ini menerangkan struktur organisasi yang berkaitan dengan domain bisnes serta hubungannya dengan entiti luar.

Sistem ICTServe bertindak sebagai hab utama perkhidmatan ICT MOTAC. Ia menghubungkan Warga MOTAC (pengguna akhir) dengan Pasukan Teknikal BPM (penyedia perkhidmatan) dan Unit Aset. Sistem ini disokong oleh infrastruktur AI Hibrid untuk meningkatkan kecekapan.

*[Ruangan untuk Rajah 1: Gambaran Bisnes Pengurusan ICTServe]*
*(Rajah menggambarkan aliran dari Pengguna -> Portal Hybrid -> Proses Bisnes -> Unit BPM)*

### 1.4 Senarai Pemegang Taruh

Seksyen ini menyenaraikan dan menerangkan pemegang-pemegang taruh yang terlibat dengan domain bisnes berkenaan.

| Pemegang Taruh | Peranan/Tanggungjawab | Kepentingan |
| :--- | :--- | :--- |
| **Pengurusan Tertinggi MOTAC** | Memantau prestasi perkhidmatan ICT dan pematuhan dasar. | Tinggi |
| **BPM (Unit Teknikal & Aset)** | Pemilik sistem; mengurus operasi harian tiket dan aset. | Tinggi |
| **Pentadbir Sistem (Superuser)** | Mengurus konfigurasi, audit, keselamatan, dan infrastruktur AI. | Tinggi |
| **Warga MOTAC (Staf)** | Pengguna berdaftar yang menggunakan dashboard dan profil peribadi. | Tinggi |
| **Warga MOTAC (Tetamu)** | Pengguna yang membuat aduan pantas tanpa log masuk. | Sederhana |
| **Pegawai Pelulus (Gred 41+)** | Meluluskan permohonan pinjaman aset melalui e-mel. | Sederhana |

---

## 2. KEPERLUAN PENGURUSAN BISNES

### 2.1 Matlamat dan Objektif

Seksyen ini menyenaraikan dan menerangkan matlamat, objektif dan hasil bisnes yang ingin dicapai melalui pelaksanaan sistem yang akan dibangunkan.

#### 2.1.1 Matlamat Utama

Mewujudkan persekitaran pengurusan perkhidmatan ICT yang responsif, pintar, dan mesra pengguna bagi menyokong operasi harian MOTAC.

#### 2.1.2 Objektif Sistem

| No. | Objektif | Keterangan | Sasaran |
| :--- | :--- | :--- | :--- |
| 1 | Aksesibiliti Tinggi | Menyediakan akses pantas melalui mod *Guest* dan *Self-Registration*. | 100% Warga MOTAC boleh akses. |
| 2 | Pengurangan Masa Respons | Menggunakan AI untuk menjawab soalan lazim (FAQ) secara automatik. | Pengurangan 30% tiket pertanyaan umum. |
| 3 | Integriti Data | Merekod semua transaksi dan kelulusan dengan jejak audit (Dual Audit). | 100% transaksi diaudit. |
| 4 | Pemantauan Efisien | Memaparkan status kesihatan sistem dan SLA secara masa nyata. | Uptime 99.5%. |

### 2.2 Arkitektur Bisnes

Seksyen ini menjelaskan dan menyediakan Arkitektur Bisnes yang berkaitan dengan sistem yang akan dibangunkan.

#### 2.2.1 Komponen Arkitektur Bisnes

Sistem ICTServe beroperasi dalam ekosistem MOTAC dengan komponen berikut:

- **Medium Perkhidmatan**: Aplikasi Web (Intranet), Notifikasi E-mel, WebSocket (Real-time)
- **Pengguna Perkhidmatan**: Staf (Authenticated), Tetamu, Admin, Superuser
- **Perkhidmatan Utama**: Helpdesk, Pinjaman Aset, Bantuan AI
- **Sistem Aplikasi**: ICTServe v3.6.1 (Laravel 12)
- **Maklumat (Data)**: Profil Pengguna, Tiket Aduan, Inventori Aset, Audit Trail, FAQ Knowledge Base
- **Teknologi**: Pangkalan Data MySQL, Redis (Queue), Ollama (Local AI), AWS Bedrock (Cloud AI), WebSocket (Pusher/Reverb)

### 2.3 Arkitektur Maklumat

Seksyen ini menerangkan Arkitektur Maklumat bagi sistem aplikasi yang akan dibangunkan.

#### 2.3.1 Hubungan Pengguna, Proses dan Maklumat

| Pengguna | Proses Bisnes | Maklumat Terlibat |
| :--- | :--- | :--- |
| **Warga MOTAC** | Menghantar Aduan / Memohon Aset | Profil Pengguna, Butiran Tiket, Tarikh Pinjaman |
| **AI Chatbot** | Menjawab Pertanyaan / Analisis | Pangkalan Pengetahuan (FAQ), Dokumen Polisi |
| **Admin BPM** | Mengurus Tiket / Aset | Status Tiket, Inventori Aset, Log Audit |
| **Pegawai Pelulus** | Meluluskan Pinjaman | Token Kelulusan, Butiran Permohonan |

---

## 3. KEPERLUAN PENGOPERASIAN BISNES

### 3.1 Keperluan Fungsi Bisnes

#### 3.1.1 Penggunaan Notasi

Seksyen ini menyenaraikan notasi-notasi yang akan digunakan untuk menyediakan Model Fungsi Bisnes.

Jadual berikut menerangkan notasi ID fungsi bisnes:

| Notasi | Keterangan |
| :--- | :--- |
| **BF-ICT-XX-YY** | Format ID Fungsi Bisnes. |
| **BF** | *Business Function*. |
| **ICT** | Kod Sistem ICTServe. |
| **XX** | Kod Modul (cth: AD=Aduan, PJ=Pinjaman, AI=Kecerdasan Buatan). |
| **YY** | Kod Sub-fungsi. |

#### 3.1.2 Model Fungsi Bisnes

Seksyen ini menyediakan Model Fungsi Bisnes yang terdiri daripada Struktur Hirarki Fungsi serta keterangan bagi fungsi-fungsi berkenaan.

##### a) Struktur Hirarki Fungsi Bisnes

Sistem dipecahkan kepada modul-modul utama berikut:

1. **BF-ICT-AD**: Pengurusan Aduan (Helpdesk)
2. **BF-ICT-PJ**: Pengurusan Pinjaman Aset
3. **BF-ICT-AI**: Bantuan Pintar (AI Chatbot)
4. **BF-ICT-PT**: Pentadbiran & Laporan
5. **BF-ICT-AM**: Pengurusan Akses (Auth & Profile)

##### b) Keterangan Fungsi Bisnes

| ID Fungsi | Nama Fungsi | Keterangan | Pengguna Terlibat |
| :--- | :--- | :--- | :--- |
| **BF-ICT-AM** | **Pengurusan Akses** | Mengurus pendaftaran diri, log masuk hibrid, dan profil pengguna. | Semua Pengguna |
| **BF-ICT-AD** | **Pengurusan Aduan** | Menghantar, memantau, dan menyelesaikan tiket kerosakan ICT. | Warga MOTAC, Admin |
| **BF-ICT-PJ** | **Pengurusan Pinjaman** | Memohon peralatan, menyemak stok, meluluskan, dan memulangkan aset. | Warga MOTAC, Admin, Pelulus |
| **BF-ICT-AI** | **Bantuan Pintar** | Menjawab pertanyaan (FAQ) dan analisis dokumen menggunakan AI Hibrid. | Semua Pengguna |
| **BF-ICT-PT** | **Pentadbiran** | Mengurus data rujukan, inventori, audit, dan menjana laporan prestasi. | Admin, Superuser |

#### 3.1.3 Senarai Pengguna

Seksyen ini menyenaraikan senarai pengguna-pengguna yang terlibat secara langsung dengan fungsi bisnes.

| Pengguna | Peranan | Tanggungjawab | Fungsi Terlibat |
| :--- | :--- | :--- | :--- |
| **Staf (Auth)** | Pengguna Berdaftar | Membuat permohonan, melihat sejarah dashboard. | Semua Modul |
| **Tetamu** | Pengguna Tidak Berdaftar | Membuat aduan/permohonan pantas, semakan status via token. | Aduan, Pinjaman, AI |
| **Admin** | Pegawai Operasi | Memproses tiket, menyediakan aset, memantau SLA. | Semua Modul |
| **Superuser** | Pentadbir Teknikal | Konfigurasi sistem, audit log, pemantauan AI. | Pentadbiran |

### 3.2 Keperluan Proses Bisnes

#### 3.2.1 Penggunaan Notasi

Seksyen ini menyenaraikan notasi-notasi yang akan digunakan untuk menyediakan Model Proses.

Menggunakan notasi standard carta alir (*Flowchart*) dan Rajah Aliran Proses (PFD).

#### 3.2.2 Model Proses Bisnes

Seksyen ini menyediakan Model Proses Bisnes yang merangkumi Aliran Proses Bisnes dan Definisi Fungsi Bisnes.

##### a) Proses Pengurusan Aduan (Helpdesk)

###### Rajah Aliran Proses (PFD-ICT-AD)

*[Ruangan untuk Rajah 5: Aliran Proses Aduan]*
*(Mula -> Pilih Kategori -> Isi Borang (Auto-fill jika login) -> Hantar -> Notifikasi -> Admin Diagnosis -> Selesai -> Penilaian)*

###### Definisi Aktiviti Fungsi Bisnes (Jadual 4)

| Rujukan Fungsi | **BF-ICT-AD** |
| :--- | :--- |
| **Nama Fungsi** | Pengurusan Aduan |
| **Nama Aktiviti** | **PFD-ICT-AD-01: Menghantar Aduan Kerosakan** |
| **Keterangan** | Pengguna melaporkan masalah ICT melalui borang dalam talian. |
| **Aktor** | Warga MOTAC (Staf/Tetamu) |
| **Prasyarat** | Tiada (Akses rangkaian Intranet/Internet). |
| **Input** | Nama, Emel, Bahagian, Kategori Masalah, Keterangan, Lampiran. |
| **Output** | Nombor Tiket, Emel Notifikasi. |
| **Aliran Utama** | 1. Pengguna akses borang.<br>2. Sistem semak status log masuk (Isi automatik jika ya).<br>3. Pengguna isi butiran kerosakan.<br>4. Pengguna hantar borang.<br>5. Sistem simpan dan jana No. Tiket. |
| **Aliran Alternatif** | Jika pengguna memilih bantuan AI, sistem akan cuba menyelesaikan masalah sebelum tiket dihantar. |
| **Aliran Pengecualian** | Jika lampiran fail melebihi had saiz (10MB), sistem akan tolak penghantaran. |
| **Syarat Pasca** | Tiket direkodkan dalam pangkalan data, emel notifikasi dihantar kepada pengguna dan admin. |

##### b) Proses Pinjaman Aset ICT

###### Rajah Aliran Proses (PFD-ICT-PJ)

*[Ruangan untuk Rajah 6: Aliran Proses Pinjaman]*
*(Mula -> Semak Ketersediaan -> Mohon -> Emel kpd Pelulus -> Pelulus Klik Pautan -> Keputusan -> Notifikasi -> Pengambilan Aset)*

###### Definisi Aktiviti Fungsi Bisnes (Jadual 5)

| Rujukan Fungsi | **BF-ICT-PJ** |
| :--- | :--- |
| **Nama Fungsi** | Pengurusan Pinjaman Aset |
| **Nama Aktiviti** | **PFD-ICT-PJ-01: Memohon Pinjaman Aset** |
| **Keterangan** | Pengguna memohon peralatan ICT untuk tempoh tertentu. |
| **Aktor** | Warga MOTAC |
| **Prasyarat** | Aset tersedia pada tarikh yang dipilih. |
| **Input** | Jenis Aset, Tarikh Mula/Tamat, Tujuan, Lokasi, Nama Pegawai Pelulus. |
| **Output** | Permohonan Status 'Menunggu Kelulusan', Emel kepada Pelulus. |
| **Aliran Utama** | 1. Pengguna pilih tarikh & aset.<br>2. Sistem semak konflik jadual.<br>3. Pengguna hantar permohonan.<br>4. Sistem hantar e-mel token kepada Pegawai Pelulus. |
| **Aliran Alternatif** | Jika aset tidak tersedia pada tarikh dipilih, sistem cadangkan tarikh alternatif. |
| **Aliran Pengecualian** | Jika tiada pegawai pelulus dinyatakan, permohonan ditolak. |
| **Syarat Pasca** | Permohonan direkodkan, status dipaparkan dalam dashboard pengguna. |

##### c) Proses Bantuan Pintar (AI Chatbot)

###### Rajah Aliran Proses (PFD-ICT-AI)

*[Ruangan untuk Rajah 7: Aliran Proses AI]*
*(Mula -> Pengguna Taip Soalan -> Router Analisis -> (Mudah: Ollama / Kompleks: Bedrock) -> Jawapan Dijana -> Papar Jawapan)*

###### Definisi Aktiviti Fungsi Bisnes (Jadual 6)

| Rujukan Fungsi | **BF-ICT-AI** |
| :--- | :--- |
| **Nama Fungsi** | Bantuan Pintar |
| **Nama Aktiviti** | **PFD-ICT-AI-01: Pertanyaan Chatbot** |
| **Keterangan** | Pengguna berinteraksi dengan AI untuk mendapatkan bantuan pantas. |
| **Aktor** | Semua Pengguna |
| **Teknologi** | Ollama (Local), AWS Bedrock (Cloud) |
| **Aliran Utama** | 1. Pengguna taip soalan.<br>2. `ModelRouter` analisis soalan.<br>3. Jika fakta/sensitif -> Guna Ollama (Local).<br>4. Jika analisis kompleks -> Guna Bedrock (Cloud).<br>5. Jawapan dipaparkan secara *streaming*. |
| **Aliran Alternatif** | Jika Ollama tidak tersedia, sistem guna Bedrock sebagai fallback (jika pengguna bersetuju). |
| **Aliran Pengecualian** | Jika kedua-dua AI tidak boleh digunakan, sistem papar mesej untuk hubungi admin. |
| **Syarat Pasca** | Interaksi direkodkan untuk tujuan pembelajaran mesin dan audit. |

### 3.3 Pengiraan Saiz Sistem Aplikasi

Seksyen ini menyediakan Pengiraan Saiz Sistem Aplikasi dengan menggunakan kaedah Function Points Analysis.

Pengiraan ini menggunakan kaedah *Function Point Analysis* (FPA) secara anggaran berdasarkan modul yang disenaraikan.

#### 3.3.1 Komponen Function Points

| Komponen | Bilangan | Kompleksiti | Faktor Pemberat (Avg) | Jumlah (UFP) |
| :--- | :---: | :---: | :---: | :---: |
| **External Input (EI)**<br>(Borang Aduan, Pinjaman, Login, Chat, Profil, Config) | 12 | Sederhana | 4 | 48 |
| **External Output (EO)**<br>(Notifikasi, Laporan PDF, Slip Tiket, Respons AI) | 8 | Sederhana | 5 | 40 |
| **External Inquiry (EQ)**<br>(Carian Tiket, Status Aset, Dashboard, Log Audit) | 10 | Sederhana | 4 | 40 |
| **Internal Logical File (ILF)**<br>(Users, Tickets, Assets, Loans, Audits, FAQs) | 15 | Tinggi | 10 | 150 |
| **External Interface File (EIF)**<br>(Google SSO, AWS Bedrock API, Ollama API) | 3 | Tinggi | 7 | 21 |
| **JUMLAH (UFP)** | | | | **299** |

#### 3.3.2 Faktor Pelarasan Teknikal

| Faktor | Nilai | Justifikasi |
|--------|-------|-------------|
| Komunikasi Data | 1.15 | Sistem menggunakan WebSocket, Redis Queue, dan API eksternal (AWS Bedrock) |

#### 3.3.3 Pengiraan Akhir

| Keterangan | Nilai |
| :--- | :--- |
| Jumlah Function Points Tidak Dilaras (UFP) | 299 |
| Faktor Pelarasan Teknikal (TCF) | 1.15 |
| **Jumlah Function Points Dilaras (AFP)** | **344** |

*Nota: Anggaran ini adalah untuk pembangunan fasa pertama. Peningkatan ciri AI dan integrasi sistem akan menambah FP di fasa akan datang.*

---

## 4. LAMPIRAN

Seksyen ini merupakan ruangan untuk menyertakan dokumen-dokumen sokongan yang perlu dirujuk seperti pekeliling dan garis panduan, minit mesyuarat, borang-borang fizikal, surat-surat rasmi, manual prosedur kerja sedia ada, dan dokumen rujukan teknikal.

### Lampiran A: Borang Rujukan

- Borang Aduan Kerosakan ICT
- Borang Permohonan Pinjaman Aset ICT

### Lampiran B: Carta Alir Proses

- Carta Alir Helpdesk (PFD-ICT-AD)
- Carta Alir Pinjaman Aset (PFD-ICT-PJ)
- Carta Alir Bantuan AI (PFD-ICT-AI)

### Lampiran C: Glosari Istilah Teknikal

- Rujuk Seksyen vii (Definisi dan Akronim)

---

## Nota Penting

1. Dokumen ini disediakan mengikut piawaian KRISA (Kejuruteraan Sistem Aplikasi Sektor Awam)
2. Semua seksyen telah dilengkapkan mengikut keperluan projek ICTServe v3.6.1
3. Rujuk kepada Buku Panduan KRISA untuk panduan terperinci
4. Dokumen ini perlu disemak dan disahkan oleh pihak yang berkenaan sebelum pelaksanaan
5. Sebarang perubahan kepada dokumen ini perlu melalui proses kawalan dokumen yang ditetapkan

---

## Maklumat Dokumen

**Tarikh Terakhir Dikemaskini:** 14 Disember 2025  
**Versi:** 3.6.1  
**Status:** Aktif

---

*Dokumen ini disediakan mengikut piawaian KRISA dan perlu disemak serta disahkan oleh pihak yang berkenaan sebelum pelaksanaan.*
