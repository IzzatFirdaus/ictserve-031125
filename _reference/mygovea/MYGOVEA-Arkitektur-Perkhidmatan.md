---
title: 'Arkitektur Perkhidmatan — MyGovEA'
source: 'https://mygovea.jdn.gov.my/page-arkitektur-perkhidmatan/'
fetched_by: 'IzzatFirdaus'
fetched_at: '2025-08-29 23:25:34 UTC'
language: 'ms-MY'
notes: |
  Dokumen ini dihasilkan daripada kandungan halaman "Arkitektur Perkhidmatan" (MyGovEA).
  Semua bahagian teks asal disertakan dan distrukturkan semula ke dalam format Markdown
  untuk rujukan teknikal dan dokumentasi. Gambar-gambar dirujuk sebagai fail tempatan
  (lihat bahagian "Aset Gambar" di bawah) kerana sumber imej tidak dimuat turun.
---

# Arkitektur Perkhidmatan — MyGovEA

Arkitektur Perkhidmatan ini merupakan panduan asas yang diperlukan bagi mereka bentuk senibina perkhidmatan digital kerajaan. Arkitektur perkhidmatan terdiri daripada 15 komponen iaitu Ekosistem End-to-End, Identifikasi, Pelbagai Platform, Direktori Perkhidmatan, Platform Bersepadu, Perkhidmatan Perisian, Pendekatan Modular, Teknologi Baharu Muncul, Multi-tenancy, Single-Sign-On, Interoperabiliti, Perkhidmatan Data, Infrastruktur Generik, Keselamatan dan Pemantauan.

Reka bentuk Arkitektur Bersepadu Perkhidmatan Kerajaan boleh digambarkan seperti rajah di bawah.

Perkhidmatan Digital Kerajaan perlu dibangunkan berteraskan rekabentuk bersepadu ini bagi membangunkan suatu standard atau piawaian yang mengintegrasikan pelbagai elemen di peringkat pelaksanaan pembangunan (OSI Layer). Penyelarasan di peringkat awal pembangunan berupaya menghasilkan perkhidmatan yang holistik dan terbaik.

## Kandungan

- [Web](#web)
- [Mobile](#mobile)
- [Kiosk](#kiosk)

## Ekosistem End-to-End (E2E)

Pembangunan perkhidmatan mengambil kira penyelesaian dalam ekosistem digital sepenuhnya (End-to-End) bagi mengatasi campur tangan manusia (human intervention) dan proses manual yang berpotensi menimbulkan birokrasi dalam perkhidmatan.

Langkah-langkah pelaksanaan E2E:

1. Menetapkan Matlamat dan Objektif: Menentukan matlamat proses serta menetapkan matlamat yang boleh diukur bagi proses berkenaan. Penglibatan pihak berkepentingan juga sangat penting dan bagaimana pihak berkenaan mendapat manfaat daripada proses berkenaan.
2. Mengenal pasti Peranan dan Tanggungjawab Ahli Pasukan: Memastikan proses berjalan lancar dan efisien adalah penting. Setiap ahli pasukan sepatutnya memahami peranan mereka dan jangkaan yang datang dengan peranan tersebut.
3. Merekod dan Mendokumenkan Proses Kerja: Menerangkan setiap langkah dalam proses dan butiran tentang siapa yang bertanggungjawab bagi setiap tugas. Dokumentasi adalah penting untuk mengesan kemajuan dan mengenal pasti sebarang halangan.
4. Pengujian dan Pemurnian Proses: Memastikan proses yang berkaitan adalah lengkap dan berfungsi dengan cekap. Ini termasuk penilaian jurang yang wujud serta melaksana penyesuaian yang diperlukan.

Rujukan: [End-to-End Process (cFlow)](https://www.cflowapps.com/end-to-end-process/)

## Identifikasi

Identifikasi (pengesahan identiti) adalah proses memastikan hanya individu sah dapat mengakses aplikasi. Elemen biasanya termasuk:

- Nama Pengguna & Kata Laluan
- Pengesahan Pelbagai Faktor (MFA)
- Biometrik (cap jari, pengecaman wajah)
- Token / Kunci Keselamatan
- Soalan Keselamatan

### Kategori pengguna perkhidmatan kerajaan

- **Warganegara** — pengesahan boleh dilaksanakan bersama Jabatan Pendaftaran Negara (JPN); identiti boleh diperoleh menerusi MyGovernment (JDN) dan PADU (KE) jika pengguna berdaftar.
- **Bukan Warganegara** — pengesahan boleh dilaksanakan bersama Jabatan Imigresen Malaysia (JIM).
- **Penduduk Tetap** — pengesahan menerusi JIM.
- **Penjawat Awam** — pengesahan melalui JPA menerusi HRMIS.

> Nota: Log masuk bagi Warganegara dan Penjawat Awam ke perkhidmatan kerajaan hendaklah menggunakan Identiti Digital Nasional (IDN) untuk pengalaman log masuk berpusat.

## Pelbagai Platform

Perkhidmatan perlu tersedia pada beberapa platform utama: web, mobile dan kiosk. Pembangunan mesti mematuhi piawaian UI/UX dan ciri platform.

### Web {#web}

- Responsif untuk pelbagai saiz skrin.
- Operasi dalam talian memerlukan sambungan Internet.
- Sesuai untuk fungsi kompleks dan aliran proses panjang.
- Sokongan pelayar utama (Chrome, Firefox, Opera, Safari).
- Kecekapan untuk komponen JavaScript & CSS.

### Mobile {#mobile}

- Aplikasi pada telefon pintar/tablet, skrin sentuh kecil.
- Boleh beroperasi secara dalam atau luar talian bergantung fungsi.
- Sesuai untuk fungsi ringan (Carian, Semakan, Permohonan, Bayaran).
- Platform utama: Android, iOS, Huawei.

### Kiosk {#kiosk}

- Terminal berdiri sendiri di lokasi awam.
- Stesen perkhidmatan mandiri untuk direktori, semakan, carian.
- Data lazimnya disimpan secara tempatan atau Intranet.
- Pastikan perlindungan SSL (GPKI) untuk komunikasi; agensi boleh langgan GPKI JDN.

## Direktori Perkhidmatan

Direktori Perkhidmatan menyimpan maklumat perkhidmatan digital kerajaan untuk mengelakkan pertindanan fungsi antara agensi, menggalakkan perkongsian kod dan penggunaan semula, serta menyediakan rujukan awal sebelum pembangunan.

Alat & rujukan:

- **RASA (JDN)** — kemaskini maklumat perkhidmatan (akan digantikan/diintegrasi semasa pelancaran PRIMMS pada 2026).
- **PRIMMS** — ICT Project Management & Monitoring System (sasar 2026).
- **DDSA (Kamus Data Sektor Awam)** — katalog data generik (kod/sintaks): Negeri, Daerah, Kementerian, Jantina, dsb.

Contoh kes guna: perkongsian modul Tempahan Bilik Mesyuarat antara agensi untuk jimat masa & kos.

## Platform Bersepadu

Platform bersepadu menggabungkan pelbagai komponen/perkhidmatan menjadi satu ekosistem koheren. Objektifnya termasuk menyederhanakan pengurusan, menyediakan pengalaman pengguna seragam dan menyokong integrasi antara perkhidmatan.

Strategi pelaksanaan:

1. Identifikasi keperluan perniagaan.
2. Pemilihan teknologi integrasi.
3. Reka bentuk sistem bersepadu.
4. Pembangunan, pengujian, pelaksanaan dan latihan.

Contoh: **MyGovernment (malaysia.gov.my)** — platform bersepadu yang menggunakan konsep MiniApp (Aplikasi Mini) untuk onboarding agensi.

## Perkhidmatan Perisian (SaaS)

Perkhidmatan SaaS membolehkan agensi guna aplikasi generik tanpa pemasangan tempatan. Biasanya dihoskan di Pusat Data Sektor Awam (PDSA).

Contoh SaaS oleh JDN:

- **DDMS** — Digital Document Management System. Laman: [https://ddms.malaysia.gov.my](https://ddms.malaysia.gov.my)
- **SPOT-Me** — Sistem Pemantauan Operasi Tugas. Laman: [https://www.spotme.gov.my](https://www.spotme.gov.my)
- **MyMesyuarat** — Pengurusan mesyuarat digital. Laman: [https://www.mymesyuarat.gov.my](https://www.mymesyuarat.gov.my)
- **MyGovEvent** — Pengurusan acara sektor awam. Laman: [https://mygovevent.mampu.gov.my](https://mygovevent.mampu.gov.my)
- **GovChat** — Aplikasi pemesejan kerajaan. Laman: [https://gc.mampu.gov.my](https://gc.mampu.gov.my)
- **MyTC** — Tempahan Fasiliti Kerajaan. Laman: [https://www.mytc.gov.my](https://www.mytc.gov.my)
- **DRSA** — Data Raya Sektor Awam. Laman: [https://drsa.gov.my](https://drsa.gov.my)
- **BaaS (Blockchain as a Service)** — langganan perkhidmatan blockchain (dijadualkan suku 2, 2025).

SaaS mengurangkan pembangunan sistem yang sama berulang kali dan menggalakkan penggunaan bersama sumber.

## Pendekatan Modular

Perkhidmatan dibina sebagai modul yang boleh digunakan semula merentas agensi. Pendekatan modular memudahkan pembangunan, ujian, dan penyelenggaraan.

### Kiosk (modular)

\- Terminal berdiri sendiri untuk direktori, semakan dan carian perkhidmatan; data boleh disimpan secara tempatan atau melalui Intranet.

### Direktori & Platform

\- Direktori perkhidmatan, platform bersepadu dan penawaran SaaS digabungkan sebagai modul yang boleh dimuat naik/diterapkan oleh agensi mengikut keperluan.

## Teknologi Baharu Muncul

Komponen contoh: Kecerdasan Buatan (AI), Internet of Things (IoT), Blockchain, Data Raya (Big Data), Pengkomputeran Awan (Cloud), Analisis Data, dan Teknologi & Bahan Termaju.

## Multi-tenancy

Multi-tenancy merujuk kepada seni bina perisian di mana satu aplikasi melayani pelbagai pelanggan atau tenant. Terdapat beberapa model pengedaran berdasarkan keperluan dan kos:

1. Aplikasi atau pangkalan data tunggal (shared schema)
2. Aplikasi tunggal, pelbagai pangkalan data (separate databases/multiple schemas)
3. Pelbagai aplikasi, pelbagai pangkalan data

## Single Sign-On (SSO)

SSO membolehkan pengguna log masuk sekali untuk mengakses pelbagai aplikasi tanpa perlu mengulangi proses log masuk. Kaedah pelaksanaan termasuk Federated Identity Management (FIM), OAuth, OpenID Connect (OIDC), dan SAML.

## Interoperabiliti

Interoperabiliti ialah keupayaan sistem berbeza untuk berkomunikasi, berkongsi dan menggunakan data secara lancar. Aspek utama: Perundangan, Organisasi, Semantik dan Teknikal.

Perkhidmatan sokongan: **MyGDX (MyGovernment Data Exchange)** — platform perkongsian data JDN.

## Perkhidmatan Data (DaaS)

DaaS membolehkan akses data sebagai perkhidmatan, menyediakan fleksibiliti, integrasi mudah dan menyokong analitis serta pematuhan keselamatan.

## Infrastruktur Generik

Infrastruktur generik disediakan oleh JDN termasuk MyGov\*Net (rangkaian) dan MyGovUC (komunikasi & kolaborasi).

## Keselamatan

Asas keselamatan: Triad CIA — Kerahsiaan, Integriti, Ketersediaan.

Amalan & kawalan: Secure coding & DevSecOps, pengurusan tampalan, IPS/WAF, MFA/SSO/IDN, audit keselamatan, penyulitan end-to-end, HSM, pematuhan ISO/IEC 27001/NIST.

Rujukan:

- Surat Pekeliling Am Bil 4 Tahun 2024 – Garis Panduan Penilaian Tahap Keselamatan Rangkaian dan Sistem ICT Sektor Awam
- Surat Pekeliling Am Bil 4 Tahun 2022 – Pengurusan dan Pengendalian Insiden Keselamatan Siber Sektor Awam

## Pemantauan

Pemantauan membantu meningkatkan ketersediaan sistem, prestasi, keselamatan, pelaporan, pengurusan kapasiti dan penambahbaikan berterusan.

Inisiatif & alat: MyRAM, SPLASK, perisian analitik web.

## Pautan Pantas dan Rujukan

- Portal Sistem Repositori MyGovEA
- Rujukan Enterprise Architecture
- Portal Rasmi Kerajaan Malaysia ([https://malaysia.gov.my](https://malaysia.gov.my))
- IASA — An Association for all IT Architect
- Portal Rasmi Jabatan Digital Negara ([https://jdn.gov.my](https://jdn.gov.my))
- RASA (JDN)
- PRIMMS (pelancaran 2026)
- DDSA — Kamus Data Sektor Awam
- DRSA ([https://drsa.gov.my](https://drsa.gov.my))
- MyGDX — MyGovernment Data Exchange
- Dasar Perkongsian Data Sektor Awam
- Pekeliling Pelaksanaan Data Terbuka Sektor Awam
- MyGovCloud / PDSA / MyGovUC / MyGov\*Net

## Hubungi Kami

JABATAN DIGITAL NEGARA
Bangunan MKN Embassy Techzone
Blok B, No. 3200 Jalan Teknokrat 2
63000 Cyberjaya, Sepang
Selangor Darul Ehsan

Tel: 603-8000 8000
E-mel: [bsa_ea@jdn.gov.my](mailto:bsa_ea@jdn.gov.my)

Bilangan Pelawat Hari Ini: 31
Jumlah Pelawat: 67560

Laman ini sesuai dipapar menggunakan pelayar web Microsoft Edge & Google Chrome versi terkini dengan resolusi minima 1366×768

© 2025 All Rights Reserved.

## Identifikasi

Identifikasi dalam aplikasi digital merujuk kepada proses pengesahan identiti pengguna untuk memastikan bahawa hanya individu yang sah dapat mengakses aplikasi tersebut. Ini penting untuk keselamatan data dan privasi pengguna. Proses ini biasanya melibatkan beberapa langkah atau elemen seperti:

Nama Pengguna dan Kata Laluan: Pengguna perlu memasukkan nama pengguna dan kata laluan yang unik untuk log masuk ke dalam aplikasi.
Pengesahan Pelbagai Faktor (MFA): Selain daripada nama pengguna dan kata laluan, pengguna mungkin perlu memasukkan kod yang dihantar ke telefon bimbit atau e-mel mereka untuk lapisan keselamatan tambahan.
Biometrik: Pengenalan melalui cap jari, pengecaman wajah, atau imbasan retina yang memastikan hanya pengguna sebenar yang boleh mengakses aplikasi.
Token atau Kunci Keselamatan: Pengguna perlu menggunakan token fizikal atau kunci keselamatan digital untuk mengesahkan identiti mereka.
Soalan Keselamatan: Soalan peribadi yang hanya diketahui oleh pengguna sebagai langkah tambahan untuk mengesahkan identiti mereka.
Identifikasi pengguna perkhidmatan kerajaan dikelaskan mengikut empat (4) kategori iaitu Warganegara, Bukan Warganegara, Penduduk Tetap dan Penjawat Awam seperti berikut:

Warganegara merupakan Individu yang mempunyai kewarganegaraan Malaysia secara sah. Mereka mempunyai hak dan tanggungjawab penuh sebagai rakyat Malaysia, termasuk hak untuk mengundi, bekerja tanpa sekatan, dan menerima kemudahan daripada kerajaan. Pengesahan identiti warganegara boleh dilaksanakan dengan kerjasama Jabatan Pendaftaran Negara (JPN). Identiti lengkap pengguna boleh diperoleh menerusi MyGovernment oleh Jabatan Digital Negara (JDN) dan PADU oleh Kementerian Ekonomi (KE) sekiranya pengguna telah sedia mendaftar kedua-dua platform tersebut.
Bukan Warganegara merupakan individu yang bukan pemegang kewarganegaraan Malaysia. Mereka mungkin berada di Malaysia untuk tujuan sementara seperti pelancongan, pekerjaan, atau pendidikan, tetapi tidak mempunyai hak penuh seperti warganegara Malaysia. Pengesahan identiti bukan warganegara boleh dilaksanakan dengan kerjasama Jabatan Imigresen Malaysia (JIM).
Penduduk Tetap merupakan Individu yang bukan warganegara Malaysia tetapi diberi status untuk menetap secara tetap di Malaysia. Mereka mempunyai beberapa hak yang serupa dengan warganegara, seperti bekerja dan tinggal di Malaysia tanpa had masa, tetapi tidak mempunyai hak untuk mengundi. Pengesahan identiti bukan warganegara boleh dilaksanakan dengan kerjasama Jabatan Imigresen Malaysia (JIM).
Penjawat Awam merupakan Individu yang bekerja di sektor awam atau kerajaan Malaysia. Pengesahan identiti bukan warganegara boleh dilaksanakan dengan kerjasama Jabatan Perkhidmatan Awam (JPA) menerusi aplikasi HRMIS.
Agensi kerajaan merupakan agensi Persekutuan, Badan Berkanun dan Negeri yang mempunyai akses kepada perkhidmatan digital khusus dan umum kerajaan.
Log masuk pengguna yang bertaraf Warganegara dan Penjawat Awam ke perkhidmatan kerajaan hendaklah menggunakan Identiti Digital Nasional (IDN). Dengan ini pengguna tidak perlu mengingati pelbagai nama dan kata laluan yang berbeza.

## Pelbagai Platform

Perkhidmatan boleh disediakan dalam dua platform utama iaitu secara web dan mobile. Kedua-dua platform ini diperlukan bersesuaian dengan teknologi semasa yang digunakan oleh pemegang taruh. Pembangunan platform perlu mematuhi Rekabentuk Komponen UI/UX yang dilampirkan bersama garis panduan ini. Berikut merupakan ciri-ciri utama dalam penyediaan pelbagai platform:

Web merupakan perkhidmatan digital yang diakses melalui pelayar web pada komputer atau peranti mudah alih. Pembangunan perkhidmatan yang berasaskan kepada teknologi web hendaklah mengambil kira elemen berikut:
Pembangunan web responsif untuk menyesuaikan dengan pelbagai saiz skrin.
Beroperasi secara dalam talian dan memerlukan sambungan Internet.
Memaparkan fungsi penuh perkhidmatan pada pelbagai jenis peranti dengan paparan antara muka skrin yang lebih besar.
Sesuai digunakan bagi perkhidmatan dengan fungsi yang kompleks, mempunyai pelbagai aliran proses serta medan input yang banyak.
Boleh dilarikan sekurang-kurangnya menggunakan pelayar web seperti Chrome, Firefox, Opera dan Safari.
Platform dapat menampung (compatible) setiap pelayar web terutamanya bagi komponen Javascript dan Cascading Style Sheet (CSS).
Mobile merupakan perkhidmatan digital yang diakses melalui aplikasi mudah alih pada telefon pintar atau tablet. Pembangunan perkhidmatan yang berasaskan kepada teknologi mobile hendaklah mengambil kira elemen berikut:
Boleh dicapai menerusi peranti mobile yang menggunakan skrin sentuh pada paparan antara muka skrin yang lebih kecil.
Beroperasi secara dalam atau luar talian bergantung pada fungsi aplikasi.
Sesuai bagi perkhidmatan yang mempunyai medan input yang sedikit serta fungsi yang ringkas (lightweight) seperti Carian, Semakan, Permohonan, dan Bayaran.
Boleh dilarikan sekurang-kurangnya menggunakan platform Android, IOS dan Huawei.
Sentiasa mengemaskini platform mengikut perubahan versi teknologi dari semasa ke semasa.
Kiosk merupakan perkhidmatan digital yang diakses melalui peranti berdiri sendiri (terminal kiosk) yang biasanya ditempatkan di lokasi tertentu seperti di pejabat kerajaan atau premis-premis terbuka. Pembangunan perkhidmatan kiosk hendaklah mengambil kira elemen berikut:
Berfungsi sebagai stesen perkhidmatan mandiri.
Akses melalui peranti fizikal dengan skrin sentuh besar.
Terminal yang menyediakan perkhidmatan asas seperti direktori, semakan dan carian.
Lazimnya data disimpan secara local atau Intranet.
Sesuai diletakkan di kawasan tumpuan umum sama ada premis kerajaan atau swasta.
Agensi hendaklah memastikan akses kepada platform ini dilindungi menggunakan protokol keselamatan SSL (Secure Sockets Layer) yang boleh dilanggan daripada JDN menerusi perkhidmatan Prasarana Kunci Awam Kerajaan (Government Public Key Infrastructure – GPKI) dengan merujuk kepada Dasar Perkhidmatan Prasarana Kunci Awam Kerajaan.

## Direktori Perkhidmatan

Direktori Perkhidmatan merupakan platform yang menempatkan informasi berkenaan perkhidmatan digital yang telah dibangunkan oleh agensi kerajaan. Direktori perkhidmatan boleh digunakan sebagai rujukan kepada agensi sebelum membangunkan sesuatu perkhidmatan. Tujuannya bagi mengatasi pertindanan fungsi, proses dan prosedur di antara agensi yang akhirnya menyebabkan karenah birokrasi dan mengelirukan pengguna. Selain itu direktori perkhidmatan dapat dimanfaatkan bagi menggalakkan perkongsian pintar dan mengoptimum perkhidmatan sedia ada.

Sebagai contoh, sistem generik seperti Tempahan Bilik Mesyuarat telah banyak dibangunkan oleh pelbagai agensi. Sistem ini lazimnya mempunyai fungsi dan proses yang sama. Agensi yang berhasrat untuk membangunkan sistem yang sama, boleh mendapatkan persetujuan daripada agensi pemilik bagi kebenaran perkongsian kod sumber. Dengan ini, masa dan kos pembangunan semula dapat dijimatkan.

Maklumat berkenaan perkhidmatan boleh dikemaskini menerusi aplikasi RASA yang dibangunkan oleh JDN. Agensi kerajaan boleh merujuk kepada RASA terlebih dahulu sebagai langkah awal semasa merancang untuk membangunkan perkhidmatan. Rujukan kepada RASA ini berkuatkuasa sehingga aplikasi ICT Project Management & Monitoring System (PRIMMS) dilancarkan pada tahun 2026. Selain itu katalog data yang menetapkan kod dan sintaks data generik seperi Negeri, Daerah, Kementerian, Jantina dan lain-lain boleh diakses menerusi platform Kamus Data Sektor Awam (DDSA) dengan merujuk kepada pekeliling Penggunaan Dan Pemakaian Data Dictionary Sektor Awam (DDSA) Sebagai Standard Di Agensi-Agensi Kerajaan.

## Platform Bersepadu

Platform bersepadu menyatukan pelbagai komponen, perkhidmatan, atau aplikasi ke dalam satu ekosistem yang koheren dan berfungsi dengan lancar. Platform ini direka untuk memudahkan pemilik perkhidmatan dan pengguna untuk mengakses, mengurus dan menyelenggara pelbagai fungsi dan perkhidmatan dalam satu ekosistem perkhidmatan secara bersepadu tanpa beralih kepada aplikasi yang berbeza. Interaksi dan integrasi antara perkhidmatan dapat dilakukan dengan lebih lancar di dalam satu platform. Berikut merupakan strategi pelaksanaan platform bersepadu:

Identifikasi Keperluan: Mengenal pasti keperluan perniagaan dan fungsi yang perlu disepadukan.
Pemilihan Teknologi: Memilih teknologi dan alat yang sesuai untuk integrasi berdasarkan kepada fungsi.
Reka Bentuk Sistem: Merancang reka bentuk sistem yang menyatukan pelbagai komponen bagi mendapatkan pengalaman pengguna yang seragam.
Pembangunan dan Pengujian: Membangunkan dan menguji platform untuk memastikan keserasian dan fungsi yang lancar.
Pelaksanaan dan Latihan: Melaksanakan platform dan memberikan latihan kepada pengguna untuk memastikan penggunaan yang efektif.
Gerbang Rasmi Kerajaan Malaysia (MyGovernment) merupakan platform bersepadu bagi menempatkan perkhidmatan utama kerajaan mengikut pesona yang boleh dilayari menerusi capaian malaysia.gov.my. Setiap agensi perlu mematuhi ekosistem dan standard UI/UX yang tersedia bagi membolehkan proses kemasukan (on-boarding) perkhidmatan ke dalam platform MyGovernment. Setiap perkhidmatan yang disediakan di dalam MyGovernment dikendalikan secara modular menggunakan konsep Aplikasi Mini (MiniApp). Bagi memudahkan kemasukan agensi ke dalam MyGovernment mengikut standard yang ditetapkan, agensi hendaklah mematuhi ekosistem MiniApp yang disediakan.

## Perkhidmatan Perisian

Perkhidmatan Perisian atau Software as a Service (SaaS) merupakan aplikasi generik yang boleh digunakan oleh agensi secara gunasama melalui Internet tanpa perlu memasangnya pada persekitaran/komputer mereka sendiri. Aplikasi ini lazimnya dihoskan di Pusat Data Sektor Awam (PDSA) dan boleh digunakan oleh agensi secara percuma mengikut tahap kebenaran dan akses yang ditetapkan. Penyediaan perkhidmatan aplikasi dapat mengawal agensi daripada membangunkan pelbagai perkhidmatan yang sama dan bertindan antara satu sama lain.

Contoh SaaS yang disediakan oleh JDN adalah seperti berikut:

DDMS: Digital Document Management System (DDMS) merupakan sistem pengurusan rekod yang menguruskan rekod rasmi dan rahsia rasmi kerajaan bagi keseluruhan kitaran hayatnya iaitu daripada proses pewujudan, penawanan, penyimpanan, penyelenggaraan, pengedaran dan pelupusan rekod secara digital dan sistematik. DDMS dapat meningkatkan ketelusan pentadbiran dalam persekitaran selamat dan sistematik serta memastikan pengurusan rekod elektronik yang lebih efisien secara digital bagi meningkatkan prestasi sistem penyampaian kerajaan. Sistem ini boleh dilayari menerusi pautan ddms.malaysia.gov.my.

## Keselamatan

Asas keselamatan: Triad CIA — Kerahsiaan, Integriti, Ketersediaan.

Amalan & kawalan: Secure coding & DevSecOps, pengurusan tampalan, IPS/WAF, MFA/SSO/IDN, audit keselamatan, penyulitan end-to-end, HSM, pematuhan ISO/IEC 27001/NIST.

Rujukan:

- Surat Pekeliling Am Bil 4 Tahun 2024 – Garis Panduan Penilaian Tahap Keselamatan Rangkaian dan Sistem ICT Sektor Awam
- Surat Pekeliling Am Bil 4 Tahun 2022 – Pengurusan dan Pengendalian Insiden Keselamatan Siber Sektor Awam

## Pemantauan

Pemantauan membantu meningkatkan ketersediaan sistem, prestasi, keselamatan, pelaporan, pengurusan kapasiti dan penambahbaikan berterusan.

Inisiatif & alat: MyRAM, SPLASK, perisian analitik web.

## Pautan Pantas dan Rujukan

- Portal Sistem Repositori MyGovEA
- Rujukan Enterprise Architecture
- Portal Rasmi Kerajaan Malaysia ([https://malaysia.gov.my](https://malaysia.gov.my))
- IASA — An Association for all IT Architect
- Portal Rasmi Jabatan Digital Negara ([https://jdn.gov.my](https://jdn.gov.my))
- RASA (JDN)
- PRIMMS (pelancaran 2026)
- DDSA — Kamus Data Sektor Awam
- DRSA ([https://drsa.gov.my](https://drsa.gov.my))
- MyGDX — MyGovernment Data Exchange
- Dasar Perkongsian Data Sektor Awam
- Pekeliling Pelaksanaan Data Terbuka Sektor Awam
- MyGovCloud / PDSA / MyGovUC / MyGov\*Net

## Hubungi Kami

JABATAN DIGITAL NEGARA
Bangunan MKN Embassy Techzone
Blok B, No. 3200 Jalan Teknokrat 2
63000 Cyberjaya, Sepang
Selangor Darul Ehsan

Tel: 603-8000 8000
E-mel: [bsa_ea@jdn.gov.my](mailto:bsa_ea@jdn.gov.my)

Bilangan Pelawat Hari Ini: 31
Jumlah Pelawat: 67560

Laman ini sesuai dipapar menggunakan pelayar web Microsoft Edge & Google Chrome versi terkini dengan resolusi minima 1366×768

© 2025 All Rights Reserved.

## Identifikasi

Identifikasi dalam aplikasi digital merujuk kepada proses pengesahan identiti pengguna untuk memastikan bahawa hanya individu yang sah dapat mengakses aplikasi tersebut. Ini penting untuk keselamatan data dan privasi pengguna. Proses ini biasanya melibatkan beberapa langkah atau elemen seperti:

Nama Pengguna dan Kata Laluan: Pengguna perlu memasukkan nama pengguna dan kata laluan yang unik untuk log masuk ke dalam aplikasi.
Pengesahan Pelbagai Faktor (MFA): Selain daripada nama pengguna dan kata laluan, pengguna mungkin perlu memasukkan kod yang dihantar ke telefon bimbit atau e-mel mereka untuk lapisan keselamatan tambahan.
Biometrik: Pengenalan melalui cap jari, pengecaman wajah, atau imbasan retina yang memastikan hanya pengguna sebenar yang boleh mengakses aplikasi.
Token atau Kunci Keselamatan: Pengguna perlu menggunakan token fizikal atau kunci keselamatan digital untuk mengesahkan identiti mereka.
Soalan Keselamatan: Soalan peribadi yang hanya diketahui oleh pengguna sebagai langkah tambahan untuk mengesahkan identiti mereka.
Identifikasi pengguna perkhidmatan kerajaan dikelaskan mengikut empat (4) kategori iaitu Warganegara, Bukan Warganegara, Penduduk Tetap dan Penjawat Awam seperti berikut:

Warganegara merupakan Individu yang mempunyai kewarganegaraan Malaysia secara sah. Mereka mempunyai hak dan tanggungjawab penuh sebagai rakyat Malaysia, termasuk hak untuk mengundi, bekerja tanpa sekatan, dan menerima kemudahan daripada kerajaan. Pengesahan identiti warganegara boleh dilaksanakan dengan kerjasama Jabatan Pendaftaran Negara (JPN). Identiti lengkap pengguna boleh diperoleh menerusi MyGovernment oleh Jabatan Digital Negara (JDN) dan PADU oleh Kementerian Ekonomi (KE) sekiranya pengguna telah sedia mendaftar kedua-dua platform tersebut.
Bukan Warganegara merupakan individu yang bukan pemegang kewarganegaraan Malaysia. Mereka mungkin berada di Malaysia untuk tujuan sementara seperti pelancongan, pekerjaan, atau pendidikan, tetapi tidak mempunyai hak penuh seperti warganegara Malaysia. Pengesahan identiti bukan warganegara boleh dilaksanakan dengan kerjasama Jabatan Imigresen Malaysia (JIM).
Penduduk Tetap merupakan Individu yang bukan warganegara Malaysia tetapi diberi status untuk menetap secara tetap di Malaysia. Mereka mempunyai beberapa hak yang serupa dengan warganegara, seperti bekerja dan tinggal di Malaysia tanpa had masa, tetapi tidak mempunyai hak untuk mengundi. Pengesahan identiti bukan warganegara boleh dilaksanakan dengan kerjasama Jabatan Imigresen Malaysia (JIM).
Penjawat Awam merupakan Individu yang bekerja di sektor awam atau kerajaan Malaysia. Pengesahan identiti bukan warganegara boleh dilaksanakan dengan kerjasama Jabatan Perkhidmatan Awam (JPA) menerusi aplikasi HRMIS.
Agensi kerajaan merupakan agensi Persekutuan, Badan Berkanun dan Negeri yang mempunyai akses kepada perkhidmatan digital khusus dan umum kerajaan.
Log masuk pengguna yang bertaraf Warganegara dan Penjawat Awam ke perkhidmatan kerajaan hendaklah menggunakan Identiti Digital Nasional (IDN). Dengan ini pengguna tidak perlu mengingati pelbagai nama dan kata laluan yang berbeza.

## Pelbagai Platform

Perkhidmatan boleh disediakan dalam dua platform utama iaitu secara web dan mobile. Kedua-dua platform ini diperlukan bersesuaian dengan teknologi semasa yang digunakan oleh pemegang taruh. Pembangunan platform perlu mematuhi Rekabentuk Komponen UI/UX yang dilampirkan bersama garis panduan ini. Berikut merupakan ciri-ciri utama dalam penyediaan pelbagai platform:

Web merupakan perkhidmatan digital yang diakses melalui pelayar web pada komputer atau peranti mudah alih. Pembangunan perkhidmatan yang berasaskan kepada teknologi web hendaklah mengambil kira elemen berikut:
Pembangunan web responsif untuk menyesuaikan dengan pelbagai saiz skrin.
Beroperasi secara dalam talian dan memerlukan sambungan Internet.
Memaparkan fungsi penuh perkhidmatan pada pelbagai jenis peranti dengan paparan antara muka skrin yang lebih besar.
Sesuai digunakan bagi perkhidmatan dengan fungsi yang kompleks, mempunyai pelbagai aliran proses serta medan input yang banyak.
Boleh dilarikan sekurang-kurangnya menggunakan pelayar web seperti Chrome, Firefox, Opera dan Safari.
Platform dapat menampung (compatible) setiap pelayar web terutamanya bagi komponen Javascript dan Cascading Style Sheet (CSS).
Mobile merupakan perkhidmatan digital yang diakses melalui aplikasi mudah alih pada telefon pintar atau tablet. Pembangunan perkhidmatan yang berasaskan kepada teknologi mobile hendaklah mengambil kira elemen berikut:
Boleh dicapai menerusi peranti mobile yang menggunakan skrin sentuh pada paparan antara muka skrin yang lebih kecil.
Beroperasi secara dalam atau luar talian bergantung pada fungsi aplikasi.
Sesuai bagi perkhidmatan yang mempunyai medan input yang sedikit serta fungsi yang ringkas (lightweight) seperti Carian, Semakan, Permohonan, dan Bayaran.
Boleh dilarikan sekurang-kurangnya menggunakan platform Android, IOS dan Huawei.
Sentiasa mengemaskini platform mengikut perubahan versi teknologi dari semasa ke semasa.
Kiosk merupakan perkhidmatan digital yang diakses melalui peranti berdiri sendiri (terminal kiosk) yang biasanya ditempatkan di lokasi tertentu seperti di pejabat kerajaan atau premis-premis terbuka. Pembangunan perkhidmatan kiosk hendaklah mengambil kira elemen berikut:
Berfungsi sebagai stesen perkhidmatan mandiri.
Akses melalui peranti fizikal dengan skrin sentuh besar.
Terminal yang menyediakan perkhidmatan asas seperti direktori, semakan dan carian.
Lazimnya data disimpan secara local atau Intranet.
Sesuai diletakkan di kawasan tumpuan umum sama ada premis kerajaan atau swasta.
Agensi hendaklah memastikan akses kepada platform ini dilindungi menggunakan protokol keselamatan SSL (Secure Sockets Layer) yang boleh dilanggan daripada JDN menerusi perkhidmatan Prasarana Kunci Awam Kerajaan (Government Public Key Infrastructure – GPKI) dengan merujuk kepada Dasar Perkhidmatan Prasarana Kunci Awam Kerajaan.

## Direktori Perkhidmatan

Direktori Perkhidmatan merupakan platform yang menempatkan informasi berkenaan perkhidmatan digital yang telah dibangunkan oleh agensi kerajaan. Direktori perkhidmatan boleh digunakan sebagai rujukan kepada agensi sebelum membangunkan sesuatu perkhidmatan. Tujuannya bagi mengatasi pertindanan fungsi, proses dan prosedur di antara agensi yang akhirnya menyebabkan karenah birokrasi dan mengelirukan pengguna. Selain itu direktori perkhidmatan dapat dimanfaatkan bagi menggalakkan perkongsian pintar dan mengoptimum perkhidmatan sedia ada.

Sebagai contoh, sistem generik seperti Tempahan Bilik Mesyuarat telah banyak dibangunkan oleh pelbagai agensi. Sistem ini lazimnya mempunyai fungsi dan proses yang sama. Agensi yang berhasrat untuk membangunkan sistem yang sama, boleh mendapatkan persetujuan daripada agensi pemilik bagi kebenaran perkongsian kod sumber. Dengan ini, masa dan kos pembangunan semula dapat dijimatkan.

Maklumat berkenaan perkhidmatan boleh dikemaskini menerusi aplikasi RASA yang dibangunkan oleh JDN. Agensi kerajaan boleh merujuk kepada RASA terlebih dahulu sebagai langkah awal semasa merancang untuk membangunkan perkhidmatan. Rujukan kepada RASA ini berkuatkuasa sehingga aplikasi ICT Project Management & Monitoring System (PRIMMS) dilancarkan pada tahun 2026. Selain itu katalog data yang menetapkan kod dan sintaks data generik seperi Negeri, Daerah, Kementerian, Jantina dan lain-lain boleh diakses menerusi platform Kamus Data Sektor Awam (DDSA) dengan merujuk kepada pekeliling Penggunaan Dan Pemakaian Data Dictionary Sektor Awam (DDSA) Sebagai Standard Di Agensi-Agensi Kerajaan.

## Platform Bersepadu

Platform bersepadu menyatukan pelbagai komponen, perkhidmatan, atau aplikasi ke dalam satu ekosistem yang koheren dan berfungsi dengan lancar. Platform ini direka untuk memudahkan pemilik perkhidmatan dan pengguna untuk mengakses, mengurus dan menyelenggara pelbagai fungsi dan perkhidmatan dalam satu ekosistem perkhidmatan secara bersepadu tanpa beralih kepada aplikasi yang berbeza. Interaksi dan integrasi antara perkhidmatan dapat dilakukan dengan lebih lancar di dalam satu platform. Berikut merupakan strategi pelaksanaan platform bersepadu:

Identifikasi Keperluan: Mengenal pasti keperluan perniagaan dan fungsi yang perlu disepadukan.
Pemilihan Teknologi: Memilih teknologi dan alat yang sesuai untuk integrasi berdasarkan kepada fungsi.
Reka Bentuk Sistem: Merancang reka bentuk sistem yang menyatukan pelbagai komponen bagi mendapatkan pengalaman pengguna yang seragam.
Pembangunan dan Pengujian: Membangunkan dan menguji platform untuk memastikan keserasian dan fungsi yang lancar.
Pelaksanaan dan Latihan: Melaksanakan platform dan memberikan latihan kepada pengguna untuk memastikan penggunaan yang efektif.
Gerbang Rasmi Kerajaan Malaysia (MyGovernment) merupakan platform bersepadu bagi menempatkan perkhidmatan utama kerajaan mengikut pesona yang boleh dilayari menerusi capaian malaysia.gov.my. Setiap agensi perlu mematuhi ekosistem dan standard UI/UX yang tersedia bagi membolehkan proses kemasukan (on-boarding) perkhidmatan ke dalam platform MyGovernment. Setiap perkhidmatan yang disediakan di dalam MyGovernment dikendalikan secara modular menggunakan konsep Aplikasi Mini (MiniApp). Bagi memudahkan kemasukan agensi ke dalam MyGovernment mengikut standard yang ditetapkan, agensi hendaklah mematuhi ekosistem MiniApp yang disediakan.

## Perkhidmatan Perisian

Perkhidmatan Perisian atau Software as a Service (SaaS) merupakan aplikasi generik yang boleh digunakan oleh agensi secara gunasama melalui Internet tanpa perlu memasangnya pada persekitaran/komputer mereka sendiri. Aplikasi ini lazimnya dihoskan di Pusat Data Sektor Awam (PDSA) dan boleh digunakan oleh agensi secara percuma mengikut tahap kebenaran dan akses yang ditetapkan. Penyediaan perkhidmatan aplikasi dapat mengawal agensi daripada membangunkan pelbagai perkhidmatan yang sama dan bertindan antara satu sama lain.

Contoh SaaS yang disediakan oleh JDN adalah seperti berikut:

DDMS: Digital Document Management System (DDMS) merupakan sistem pengurusan rekod yang menguruskan rekod rasmi dan rahsia rasmi kerajaan bagi keseluruhan kitaran hayatnya iaitu daripada proses pewujudan, penawanan, penyimpanan, penyelenggaraan, pengedaran dan pelupusan rekod secara digital dan sistematik. DDMS dapat meningkatkan ketelusan pentadbiran dalam persekitaran selamat dan sistematik serta memastikan pengurusan rekod elektronik yang lebih efisien secara digital bagi meningkatkan prestasi sistem penyampaian kerajaan. Sistem ini boleh dilayari menerusi pautan ddms.malaysia.gov.my.

## Keselamatan

Asas keselamatan: Triad CIA — Kerahsiaan, Integriti, Ketersediaan.

Amalan & kawalan: Secure coding & DevSecOps, pengurusan tampalan, IPS/WAF, MFA/SSO/IDN, audit keselamatan, penyulitan end-to-end, HSM, pematuhan ISO/IEC 27001/NIST.

Rujukan:

- Surat Pekeliling Am Bil 4 Tahun 2024 – Garis Panduan Penilaian Tahap Keselamatan Rangkaian dan Sistem ICT Sektor Awam
- Surat Pekeliling Am Bil 4 Tahun 2022 – Pengurusan dan Pengendalian Insiden Keselamatan Siber Sektor Awam

## Pemantauan

Pemantauan membantu meningkatkan ketersediaan sistem, prestasi, keselamatan, pelaporan, pengurusan kapasiti dan penambahbaikan berterusan.

Inisiatif & alat: MyRAM, SPLASK, perisian analitik web.

## Pautan Pantas dan Rujukan

- Portal Sistem Repositori MyGovEA
- Rujukan Enterprise Architecture
- Portal Rasmi Kerajaan Malaysia ([https://malaysia.gov.my](https://malaysia.gov.my))
- IASA — An Association for all IT Architect
- Portal Rasmi Jabatan Digital Negara ([https://jdn.gov.my](https://jdn.gov.my))
- RASA (JDN)
- PRIMMS (pelancaran 2026)
- DDSA — Kamus Data Sektor Awam
- DRSA ([https://drsa.gov.my](https://drsa.gov.my))
- MyGDX — MyGovernment Data Exchange
- Dasar Perkongsian Data Sektor Awam
- Pekeliling Pelaksanaan Data Terbuka Sektor Awam
- MyGovCloud / PDSA / MyGovUC / MyGov\*Net

## Hubungi Kami

JABATAN DIGITAL NEGARA
Bangunan MKN Embassy Techzone
Blok B, No. 3200 Jalan Teknokrat 2
63000 Cyberjaya, Sepang
Selangor Darul Ehsan

Tel: 603-8000 8000
E-mel: [bsa_ea@jdn.gov.my](mailto:bsa_ea@jdn.gov.my)

Bilangan Pelawat Hari Ini: 31
Jumlah Pelawat: 67560

Laman ini sesuai dipapar menggunakan pelayar web Microsoft Edge & Google Chrome versi terkini dengan resolusi minima 1366×768

© 2025 All Rights Reserved.

## Identifikasi

Identifikasi dalam aplikasi digital merujuk kepada proses pengesahan identiti pengguna untuk memastikan bahawa hanya individu yang sah dapat mengakses aplikasi tersebut. Ini penting untuk keselamatan data dan privasi pengguna. Proses ini biasanya melibatkan beberapa langkah atau elemen seperti:

Nama Pengguna dan Kata Laluan: Pengguna perlu memasukkan nama pengguna dan kata laluan yang unik untuk log masuk ke dalam aplikasi.
Pengesahan Pelbagai Faktor (MFA): Selain daripada nama pengguna dan kata laluan, pengguna mungkin perlu memasukkan kod yang dihantar ke telefon bimbit atau e-mel mereka untuk lapisan keselamatan tambahan.
Biometrik: Pengenalan melalui cap jari, pengecaman wajah, atau imbasan retina yang memastikan hanya pengguna sebenar yang boleh mengakses aplikasi.
Token atau Kunci Keselamatan: Pengguna perlu menggunakan token fizikal atau kunci keselamatan digital untuk mengesahkan identiti mereka.
Soalan Keselamatan: Soalan peribadi yang hanya diketahui oleh pengguna sebagai langkah tambahan untuk mengesahkan identiti mereka.
Identifikasi pengguna perkhidmatan kerajaan dikelaskan mengikut empat (4) kategori iaitu Warganegara, Bukan Warganegara, Penduduk Tetap dan Penjawat Awam seperti berikut:

Warganegara merupakan Individu yang mempunyai kewarganegaraan Malaysia secara sah. Mereka mempunyai hak dan tanggungjawab penuh sebagai rakyat Malaysia, termasuk hak untuk mengundi, bekerja tanpa sekatan, dan menerima kemudahan daripada kerajaan. Pengesahan identiti warganegara boleh dilaksanakan dengan kerjasama Jabatan Pendaftaran Negara (JPN). Identiti lengkap pengguna boleh diperoleh menerusi MyGovernment oleh Jabatan Digital Negara (JDN) dan PADU oleh Kementerian Ekonomi (KE) sekiranya pengguna telah sedia mendaftar kedua-dua platform tersebut.
Bukan Warganegara merupakan individu yang bukan pemegang kewarganegaraan Malaysia. Mereka mungkin berada di Malaysia untuk tujuan sementara seperti pelancongan, pekerjaan, atau pendidikan, tetapi tidak mempunyai hak penuh seperti warganegara Malaysia. Pengesahan identiti bukan warganegara boleh dilaksanakan dengan kerjasama Jabatan Imigresen Malaysia (JIM).
Penduduk Tetap merupakan Individu yang bukan warganegara Malaysia tetapi diberi status untuk menetap secara tetap di Malaysia. Mereka mempunyai beberapa hak yang serupa dengan warganegara, seperti bekerja dan tinggal di Malaysia tanpa had masa, tetapi tidak mempunyai hak untuk mengundi. Pengesahan identiti bukan warganegara boleh dilaksanakan dengan kerjasama Jabatan Imigresen Malaysia (JIM).
Penjawat Awam merupakan Individu yang bekerja di sektor awam atau kerajaan Malaysia. Pengesahan identiti bukan warganegara boleh dilaksanakan dengan kerjasama Jabatan Perkhidmatan Awam (JPA) menerusi aplikasi HRMIS.
Agensi kerajaan merupakan agensi Persekutuan, Badan Berkanun dan Negeri yang mempunyai akses kepada perkhidmatan digital khusus dan umum kerajaan.
Log masuk pengguna yang bertaraf Warganegara dan Penjawat Awam ke perkhidmatan kerajaan hendaklah menggunakan Identiti Digital Nasional (IDN). Dengan ini pengguna tidak perlu mengingati pelbagai nama dan kata laluan yang berbeza.

## Pelbagai Platform

Perkhidmatan boleh disediakan dalam dua platform utama iaitu secara web dan mobile. Kedua-dua platform ini diperlukan bersesuaian dengan teknologi semasa yang digunakan oleh pemegang taruh. Pembangunan platform perlu mematuhi Rekabentuk Komponen UI/UX yang dilampirkan bersama garis panduan ini. Berikut merupakan ciri-ciri utama dalam penyediaan pelbagai platform:

Web merupakan perkhidmatan digital yang diakses melalui pelayar web pada komputer atau peranti mudah alih. Pembangunan perkhidmatan yang berasaskan kepada teknologi web hendaklah mengambil kira elemen berikut:
Pembangunan web responsif untuk menyesuaikan dengan pelbagai saiz skrin.
Beroperasi secara dalam talian dan memerlukan sambungan Internet.
Memaparkan fungsi penuh perkhidmatan pada pelbagai jenis peranti dengan paparan antara muka skrin yang lebih besar.
Sesuai digunakan bagi perkhidmatan dengan fungsi yang kompleks, mempunyai pelbagai aliran proses serta medan input yang banyak.
Boleh dilarikan sekurang-kurangnya menggunakan pelayar web seperti Chrome, Firefox, Opera dan Safari.
Platform dapat menampung (compatible) setiap pelayar web terutamanya bagi komponen Javascript dan Cascading Style Sheet (CSS).
Mobile merupakan perkhidmatan digital yang diakses melalui aplikasi mudah alih pada telefon pintar atau tablet. Pembangunan perkhidmatan yang berasaskan kepada teknologi mobile hendaklah mengambil kira elemen berikut:
Boleh dicapai menerusi peranti mobile yang menggunakan skrin sentuh pada paparan antara muka skrin yang lebih kecil.
Beroperasi secara dalam atau luar talian bergantung pada fungsi aplikasi.
Sesuai bagi perkhidmatan yang mempunyai medan input yang sedikit serta fungsi yang ringkas (lightweight) seperti Carian, Semakan, Permohonan, dan Bayaran.
Boleh dilarikan sekurang-kurangnya menggunakan platform Android, IOS dan Huawei.
Sentiasa mengemaskini platform mengikut perubahan versi teknologi dari semasa ke semasa.
Kiosk merupakan perkhidmatan digital yang diakses melalui peranti berdiri sendiri (terminal kiosk) yang biasanya ditempatkan di lokasi tertentu seperti di pejabat kerajaan atau premis-premis terbuka. Pembangunan perkhidmatan kiosk hendaklah mengambil kira elemen berikut:
Berfungsi sebagai stesen perkhidmatan mandiri.
Akses melalui peranti fizikal dengan skrin sentuh besar.
Terminal yang menyediakan perkhidmatan asas seperti direktori, semakan dan carian.
Lazimnya data disimpan secara local atau Intranet.
Sesuai diletakkan di kawasan tumpuan umum sama ada premis kerajaan atau swasta.
Agensi hendaklah memastikan akses kepada platform ini dilindungi menggunakan protokol keselamatan SSL (Secure Sockets Layer) yang boleh dilanggan daripada JDN menerusi perkhidmatan Prasarana Kunci Awam Kerajaan (Government Public Key Infrastructure – GPKI) dengan merujuk kepada Dasar Perkhidmatan Prasarana Kunci Awam Kerajaan.

## Direktori Perkhidmatan

Direktori Perkhidmatan merupakan platform yang menempatkan informasi berkenaan perkhidmatan digital yang telah dibangunkan oleh agensi kerajaan. Direktori perkhidmatan boleh digunakan sebagai rujukan kepada agensi sebelum membangunkan sesuatu perkhidmatan. Tujuannya bagi mengatasi pertindanan fungsi, proses dan prosedur di antara agensi yang akhirnya menyebabkan karenah birokrasi dan mengelirukan pengguna. Selain itu direktori perkhidmatan dapat dimanfaatkan bagi menggalakkan perkongsian pintar dan mengoptimum perkhidmatan sedia ada.

Sebagai contoh, sistem generik seperti Tempahan Bilik Mesyuarat telah banyak dibangunkan oleh pelbagai agensi. Sistem ini lazimnya mempunyai fungsi dan proses yang sama. Agensi yang berhasrat untuk membangunkan sistem yang sama, boleh mendapatkan persetujuan daripada agensi pemilik bagi kebenaran perkongsian kod sumber. Dengan ini, masa dan kos pembangunan semula dapat dijimatkan.

Maklumat berkenaan perkhidmatan boleh dikemaskini menerusi aplikasi RASA yang dibangunkan oleh JDN. Agensi kerajaan boleh merujuk kepada RASA terlebih dahulu sebagai langkah awal semasa merancang untuk membangunkan perkhidmatan. Rujukan kepada RASA ini berkuatkuasa sehingga aplikasi ICT Project Management & Monitoring System (PRIMMS) dilancarkan pada tahun 2026. Selain itu katalog data yang menetapkan kod dan sintaks data generik seperi Negeri, Daerah, Kementerian, Jantina dan lain-lain boleh diakses menerusi platform Kamus Data Sektor Awam (DDSA) dengan merujuk kepada pekeliling Penggunaan Dan Pemakaian Data Dictionary Sektor Awam (DDSA) Sebagai Standard Di Agensi-Agensi Kerajaan.

## Platform Bersepadu

Platform bersepadu menyatukan pelbagai komponen, perkhidmatan, atau aplikasi ke dalam satu ekosistem yang koheren dan berfungsi dengan lancar. Platform ini direka untuk memudahkan pemilik perkhidmatan dan pengguna untuk mengakses, mengurus dan menyelenggara pelbagai fungsi dan perkhidmatan dalam satu ekosistem perkhidmatan secara bersepadu tanpa beralih kepada aplikasi yang berbeza. Interaksi dan integrasi antara perkhidmatan dapat dilakukan dengan lebih lancar di dalam satu platform. Berikut merupakan strategi pelaksanaan platform bersepadu:

Identifikasi Keperluan: Mengenal pasti keperluan perniagaan dan fungsi yang perlu disepadukan.
Pemilihan Teknologi: Memilih teknologi dan alat yang sesuai untuk integrasi berdasarkan kepada fungsi.
Reka Bentuk Sistem: Merancang reka bentuk sistem yang menyatukan pelbagai komponen bagi mendapatkan pengalaman pengguna yang seragam.
Pembangunan dan Pengujian: Membangunkan dan menguji platform untuk memastikan keserasian dan fungsi yang lancar.
Pelaksanaan dan Latihan: Melaksanakan platform dan memberikan latihan kepada pengguna untuk memastikan penggunaan yang efektif.
Gerbang Rasmi Kerajaan Malaysia (MyGovernment) merupakan platform bersepadu bagi menempatkan perkhidmatan utama kerajaan mengikut pesona yang boleh dilayari menerusi capaian malaysia.gov.my. Setiap agensi perlu mematuhi ekosistem dan standard UI/UX yang tersedia bagi membolehkan proses kemasukan (on-boarding) perkhidmatan ke dalam platform MyGovernment. Setiap perkhidmatan yang disediakan di dalam MyGovernment dikendalikan secara modular menggunakan konsep Aplikasi Mini (MiniApp). Bagi memudahkan kemasukan agensi ke dalam MyGovernment mengikut standard yang ditetapkan, agensi hendaklah mematuhi ekosistem MiniApp yang disediakan.

## Perkhidmatan Perisian

Perkhidmatan Perisian atau Software as a Service (SaaS) merupakan aplikasi generik yang boleh digunakan oleh agensi secara gunasama melalui Internet tanpa perlu memasangnya pada persekitaran/komputer mereka sendiri. Aplikasi ini lazimnya dihoskan di Pusat Data Sektor Awam (PDSA) dan boleh digunakan oleh agensi secara percuma mengikut tahap kebenaran dan akses yang ditetapkan. Penyediaan perkhidmatan aplikasi dapat mengawal agensi daripada membangunkan pelbagai perkhidmatan yang sama dan bertindan antara satu sama lain.

Contoh SaaS yang disediakan oleh JDN adalah seperti berikut:

DDMS: Digital Document Management System (DDMS) merupakan sistem pengurusan rekod yang menguruskan rekod rasmi dan rahsia rasmi kerajaan bagi keseluruhan kitaran hayatnya iaitu daripada proses pewujudan, penawanan, penyimpanan, penyelenggaraan, pengedaran dan pelupusan rekod secara digital dan sistematik. DDMS dapat meningkatkan ketelusan pentadbiran dalam persekitaran selamat dan sistematik serta memastikan pengurusan rekod elektronik yang lebih efisien secara digital bagi meningkatkan prestasi sistem penyampaian kerajaan. Sistem ini boleh dilayari menerusi pautan ddms.malaysia.gov.my.

## Keselamatan

Asas keselamatan: Triad CIA — Kerahsiaan, Integriti, Ketersediaan.

Amalan & kawalan: Secure coding & DevSecOps, pengurusan tampalan, IPS/WAF, MFA/SSO/IDN, audit keselamatan, penyulitan end-to-end, HSM, pematuhan ISO/IEC 27001/NIST.

Rujukan:

- Surat Pekeliling Am Bil 4 Tahun 2024 – Garis Panduan Penilaian Tahap Keselamatan Rangkaian dan Sistem ICT Sektor Awam
- Surat Pekeliling Am Bil 4 Tahun 2022 – Pengurusan dan Pengendalian Insiden Keselamatan Siber Sektor Awam

## Pemantauan

Pemantauan membantu meningkatkan ketersediaan sistem, prestasi, keselamatan, pelaporan, pengurusan kapasiti dan penambahbaikan berterusan.

Inisiatif & alat: MyRAM, SPLASK, perisian analitik web.

## Pautan Pantas dan Rujukan

- Portal Sistem Repositori MyGovEA
- Rujukan Enterprise Architecture
- Portal Rasmi Kerajaan Malaysia ([https://malaysia.gov.my](https://malaysia.gov.my))
- IASA — An Association for all IT Architect
- Portal Rasmi Jabatan Digital Negara ([https://jdn.gov.my](https://jdn.gov.my))
- RASA (JDN)
- PRIMMS (pelancaran 2026)
- DDSA — Kamus Data Sektor Awam
- DRSA ([https://drsa.gov.my](https://drsa.gov.my))
- MyGDX — MyGovernment Data Exchange
- Dasar Perkongsian Data Sektor Awam
- Pekeliling Pelaksanaan Data Terbuka Sektor Awam
- MyGovCloud / PDSA / MyGovUC / MyGov\*Net

## Hubungi Kami

JABATAN DIGITAL NEGARA
Bangunan MKN Embassy Techzone
Blok B, No. 3200 Jalan Teknokrat 2
63000 Cyberjaya, Sepang
Selangor Darul Ehsan

Tel: 603-8000 8000
E-mel: [bsa_ea@jdn.gov.my](mailto:bsa_ea@jdn.gov.my)

Bilangan Pelawat Hari Ini: 31
Jumlah Pelawat: 67560

Laman ini sesuai dipapar menggunakan pelayar web Microsoft Edge & Google Chrome versi terkini dengan resolusi minima 1366×768

© 2025 All Rights Reserved.

## Identifikasi

Identifikasi dalam aplikasi digital merujuk kepada proses pengesahan identiti pengguna untuk memastikan bahawa hanya individu yang sah dapat mengakses aplikasi tersebut. Ini penting untuk keselamatan data dan privasi pengguna. Proses ini biasanya melibatkan beberapa langkah atau elemen seperti:

Nama Pengguna dan Kata Laluan: Pengguna perlu memasukkan nama pengguna dan kata laluan yang unik untuk log masuk ke dalam aplikasi.
Pengesahan Pelbagai Faktor (MFA): Selain daripada nama pengguna dan kata laluan, pengguna mungkin perlu memasukkan kod yang dihantar ke telefon bimbit atau e-mel mereka untuk lapisan keselamatan tambahan.
Biometrik: Pengenalan melalui cap jari, pengecaman wajah, atau imbasan retina yang memastikan hanya pengguna sebenar yang boleh mengakses aplikasi.
Token atau Kunci Keselamatan: Pengguna perlu menggunakan token fizikal atau kunci keselamatan digital untuk mengesahkan identiti mereka.
Soalan Keselamatan: Soalan peribadi yang hanya diketahui oleh pengguna sebagai langkah tambahan untuk mengesahkan identiti mereka.
Identifikasi pengguna perkhidmatan kerajaan dikelaskan mengikut empat (4) kategori iaitu Warganegara, Bukan Warganegara, Penduduk Tetap dan Penjawat Awam seperti berikut:

Warganegara merupakan Individu yang mempunyai kewarganegaraan Malaysia secara sah. Mereka mempunyai hak dan tanggungjawab penuh sebagai rakyat Malaysia, termasuk hak untuk mengundi, bekerja tanpa sekatan, dan menerima kemudahan daripada kerajaan. Pengesahan identiti warganegara boleh dilaksanakan dengan kerjasama Jabatan Pendaftaran Negara (JPN). Identiti lengkap pengguna boleh diperoleh menerusi MyGovernment oleh Jabatan Digital Negara (JDN) dan PADU oleh Kementerian Ekonomi (KE) sekiranya pengguna telah sedia mendaftar kedua-dua platform tersebut.
Bukan Warganegara merupakan individu yang bukan pemegang kewarganegaraan Malaysia. Mereka mungkin berada di Malaysia untuk tujuan sementara seperti pelancongan, pekerjaan, atau pendidikan, tetapi tidak mempunyai hak penuh seperti warganegara Malaysia. Pengesahan identiti bukan warganegara boleh dilaksanakan dengan kerjasama Jabatan Imigresen Malaysia (JIM).
Penduduk Tetap merupakan Individu yang bukan warganegara Malaysia tetapi diberi status untuk menetap secara tetap di Malaysia. Mereka mempunyai beberapa hak yang serupa dengan warganegara, seperti bekerja dan tinggal di Malaysia tanpa had masa, tetapi tidak mempunyai hak untuk mengundi. Pengesahan identiti bukan warganegara boleh dilaksanakan dengan kerjasama Jabatan Imigresen Malaysia (JIM).
Penjawat Awam merupakan Individu yang bekerja di sektor awam atau kerajaan Malaysia. Pengesahan identiti bukan warganegara boleh dilaksanakan dengan kerjasama Jabatan Perkhidmatan Awam (JPA) menerusi aplikasi HRMIS.
Agensi kerajaan merupakan agensi Persekutuan, Badan Berkanun dan Negeri yang mempunyai akses kepada perkhidmatan digital khusus dan umum kerajaan.
Log masuk pengguna yang bertaraf Warganegara dan Penjawat Awam ke perkhidmatan kerajaan hendaklah menggunakan Identiti Digital Nasional (IDN). Dengan ini pengguna tidak perlu mengingati pelbagai nama dan kata laluan yang berbeza.

## Pelbagai Platform

Perkhidmatan boleh disediakan dalam dua platform utama iaitu secara web dan mobile. Kedua-dua platform ini diperlukan bersesuaian dengan teknologi semasa yang digunakan oleh pemegang taruh. Pembangunan platform perlu mematuhi Rekabentuk Komponen UI/UX yang dilampirkan bersama garis panduan ini. Berikut merupakan ciri-ciri utama dalam penyediaan pelbagai platform:

Web merupakan perkhidmatan digital yang diakses melalui pelayar web pada komputer atau peranti mudah alih. Pembangunan perkhidmatan yang berasaskan kepada teknologi web hendaklah mengambil kira elemen berikut:
Pembangunan web responsif untuk menyesuaikan dengan pelbagai saiz skrin.
Beroperasi secara dalam talian dan memerlukan sambungan Internet.
Memaparkan fungsi penuh perkhidmatan pada pelbagai jenis peranti dengan paparan antara muka skrin yang lebih besar.
Sesuai digunakan bagi perkhidmatan dengan fungsi yang kompleks, mempunyai pelbagai aliran proses serta medan input yang banyak.
Boleh dilarikan sekurang-kurangnya menggunakan pelayar web seperti Chrome, Firefox, Opera dan Safari.
Platform dapat menampung (compatible) setiap pelayar web terutamanya bagi komponen Javascript dan Cascading Style Sheet (CSS).
Mobile merupakan perkhidmatan digital yang diakses melalui aplikasi mudah alih pada telefon pintar atau tablet. Pembangunan perkhidmatan yang berasaskan kepada teknologi mobile hendaklah mengambil kira elemen berikut:
Boleh dicapai menerusi peranti mobile yang menggunakan skrin sentuh pada paparan antara muka skrin yang lebih kecil.
Beroperasi secara dalam atau luar talian bergantung pada fungsi aplikasi.
Sesuai bagi perkhidmatan yang mempunyai medan input yang sedikit serta fungsi yang ringkas (lightweight) seperti Carian, Semakan, Permohonan, dan Bayaran.
Boleh dilarikan sekurang-kurangnya menggunakan platform Android, IOS dan Huawei.
Sentiasa mengemaskini platform mengikut perubahan versi teknologi dari semasa ke semasa.
Kiosk merupakan perkhidmatan digital yang diakses melalui peranti berdiri sendiri (terminal kiosk) yang biasanya ditempatkan di lokasi tertentu seperti di pejabat kerajaan atau premis-premis terbuka. Pembangunan perkhidmatan kiosk hendaklah mengambil kira elemen berikut:
Berfungsi sebagai stesen perkhidmatan mandiri.
Akses melalui peranti fizikal dengan skrin sentuh besar.
Terminal yang menyediakan perkhidmatan asas seperti direktori, semakan dan carian.
Lazimnya data disimpan secara local atau Intranet.
Sesuai diletakkan di kawasan tumpuan umum sama ada premis kerajaan atau swasta.
Agensi hendaklah memastikan akses kepada platform ini dilindungi menggunakan protokol keselamatan SSL (Secure Sockets Layer) yang boleh dilanggan daripada JDN menerusi perkhidmatan Prasarana Kunci Awam Kerajaan (Government Public Key Infrastructure – GPKI) dengan merujuk kepada Dasar Perkhidmatan Prasarana Kunci Awam Kerajaan.

## Direktori Perkhidmatan

Direktori Perkhidmatan merupakan platform yang menempatkan informasi berkenaan perkhidmatan digital yang telah dibangunkan oleh agensi kerajaan. Direktori perkhidmatan boleh digunakan sebagai rujukan kepada agensi sebelum membangunkan sesuatu perkhidmatan. Tujuannya bagi mengatasi pertindanan fungsi, proses dan prosedur di antara agensi yang akhirnya menyebabkan karenah birokrasi dan mengelirukan pengguna. Selain itu direktori perkhidmatan dapat dimanfaatkan bagi menggalakkan perkongsian pintar dan mengoptimum perkhidmatan sedia ada.

Sebagai contoh, sistem generik seperti Tempahan Bilik Mesyuarat telah banyak dibangunkan oleh pelbagai agensi. Sistem ini lazimnya mempunyai fungsi dan proses yang sama. Agensi yang berhasrat untuk membangunkan sistem yang sama, boleh mendapatkan persetujuan daripada agensi pemilik bagi kebenaran perkongsian kod sumber. Dengan ini, masa dan kos pembangunan semula dapat dijimatkan.

Maklumat berkenaan perkhidmatan boleh dikemaskini menerusi aplikasi RASA yang dibangunkan oleh JDN. Agensi kerajaan boleh merujuk kepada RASA terlebih dahulu sebagai langkah awal semasa merancang untuk membangunkan perkhidmatan. Rujukan kepada RASA ini berkuatkuasa sehingga aplikasi ICT Project Management & Monitoring System (PRIMMS) dilancarkan pada tahun 2026. Selain itu katalog data yang menetapkan kod dan sintaks data generik seperi Negeri, Daerah, Kementerian, Jantina dan lain-lain boleh diakses menerusi platform Kamus Data Sektor Awam (DDSA) dengan merujuk kepada pekeliling Penggunaan Dan Pemakaian Data Dictionary Sektor Awam (DDSA) Sebagai Standard Di Agensi-Agensi Kerajaan.

## Platform Bersepadu

Platform bersepadu menyatukan pelbagai komponen, perkhidmatan, atau aplikasi ke dalam satu ekosistem yang koheren dan berfungsi dengan lancar. Platform ini direka untuk memudahkan pemilik perkhidmatan dan pengguna untuk mengakses, mengurus dan menyelenggara pelbagai fungsi dan perkhidmatan dalam satu ekosistem perkhidmatan secara bersepadu tanpa beralih kepada aplikasi yang berbeza. Interaksi dan integrasi antara perkhidmatan dapat dilakukan dengan lebih lancar di dalam satu platform. Berikut merupakan strategi pelaksanaan platform bersepadu:

Identifikasi Keperluan: Mengenal pasti keperluan perniagaan dan fungsi yang perlu disepadukan.
Pemilihan Teknologi: Memilih teknologi dan alat yang sesuai untuk integrasi berdasarkan kepada fungsi.
Reka Bentuk Sistem: Merancang reka bentuk sistem yang menyatukan pelbagai komponen bagi mendapatkan pengalaman pengguna yang seragam.
Pembangunan dan Pengujian: Membangunkan dan menguji platform untuk memastikan keserasian dan fungsi yang lancar.
Pelaksanaan dan Latihan: Melaksanakan platform dan memberikan latihan kepada pengguna untuk memastikan penggunaan yang efektif.
Gerbang Rasmi Kerajaan Malaysia (MyGovernment) merupakan platform bersepadu bagi menempatkan perkhidmatan utama kerajaan mengikut pesona yang boleh dilayari menerusi capaian malaysia.gov.my. Setiap agensi perlu mematuhi ekosistem dan standard UI/UX yang tersedia bagi membolehkan proses kemasukan (on-boarding) perkhidmatan ke dalam platform MyGovernment. Setiap perkhidmatan yang disediakan di dalam MyGovernment dikendalikan secara modular menggunakan konsep Aplikasi Mini (MiniApp). Bagi memudahkan kemasukan agensi ke dalam MyGovernment mengikut standard yang ditetapkan, agensi hendaklah mematuhi ekosistem MiniApp yang disediakan.

## Perkhidmatan Perisian

Perkhidmatan Perisian atau Software as a Service (SaaS) merupakan aplikasi generik yang boleh digunakan oleh agensi secara gunasama melalui Internet tanpa perlu memasangnya pada persekitaran/komputer mereka sendiri. Aplikasi ini lazimnya dihoskan di Pusat Data Sektor Awam (PDSA) dan boleh digunakan oleh agensi secara percuma mengikut tahap kebenaran dan akses yang ditetapkan. Penyediaan perkhidmatan aplikasi dapat mengawal agensi daripada membangunkan pelbagai perkhidmatan yang sama dan bertindan antara satu sama lain.

Contoh SaaS yang disediakan oleh JDN adalah seperti berikut:

DDMS: Digital Document Management System (DDMS) merupakan sistem pengurusan rekod yang menguruskan rekod rasmi dan rahsia rasmi kerajaan bagi keseluruhan kitaran hayatnya iaitu daripada proses pewujudan, penawanan, penyimpanan, penyelenggaraan, pengedaran dan pelupusan rekod secara digital dan sistematik. DDMS dapat meningkatkan ketelusan pentadbiran dalam persekitaran selamat dan sistematik serta memastikan pengurusan rekod elektronik yang lebih efisien secara digital bagi meningkatkan prestasi sistem penyampaian kerajaan. Sistem ini boleh dilayari menerusi pautan ddms.malaysia.gov.my.

## Keselamatan

Asas keselamatan: Triad CIA — Kerahsiaan, Integriti, Ketersediaan.

Amalan & kawalan: Secure coding & DevSecOps, pengurusan tampalan, IPS/WAF, MFA/SSO/IDN, audit keselamatan, penyulitan end-to-end, HSM, pematuhan ISO/IEC 27001/NIST.

Rujukan:

- Surat Pekeliling Am Bil 4 Tahun 2024 – Garis Panduan Penilaian Tahap Keselamatan Rangkaian dan Sistem ICT Sektor Awam
- Surat Pekeliling Am Bil 4 Tahun 2022 – Pengurusan dan Pengendalian Insiden Keselamatan Siber Sektor Awam

## Pemantauan

Pemantauan membantu meningkatkan ketersediaan sistem, prestasi, keselamatan, pelaporan, pengurusan kapasiti dan penambahbaikan berterusan.

Inisiatif & alat: MyRAM, SPLASK, perisian analitik web.

## Pautan Pantas dan Rujukan

- Portal Sistem Repositori MyGovEA
- Rujukan Enterprise Architecture
- Portal Rasmi Kerajaan Malaysia ([https://malaysia.gov.my](https://malaysia.gov.my))
- IASA — An Association for all IT Architect
- Portal Rasmi Jabatan Digital Negara ([https://jdn.gov.my](https://jdn.gov.my))
- RASA (JDN)
- PRIMMS (pelancaran 2026)
- DDSA — Kamus Data Sektor Awam
- DRSA ([https://drsa.gov.my](https://drsa.gov.my))
- MyGDX — MyGovernment Data Exchange
- Dasar Perkongsian Data Sektor Awam
- Pekeliling Pelaksanaan Data Terbuka Sektor Awam
- MyGovCloud / PDSA / MyGovUC / MyGov\*Net

## Hubungi Kami

JABATAN DIGITAL NEGARA
Bangunan MKN Embassy Techzone
Blok B, No. 3200 Jalan Teknokrat 2
63000 Cyberjaya, Sepang
Selangor Darul Ehsan

Tel: 603-8000 8000
E-mel: [bsa_ea@jdn.gov.my](mailto:bsa_ea@jdn.gov.my)

Bilangan Pelawat Hari Ini: 31
Jumlah Pelawat: 67560

Laman ini sesuai dipapar menggunakan pelayar web Microsoft Edge & Google Chrome versi terkini dengan resolusi minima 1366×768

© 2025 All Rights Reserved.

## Identifikasi

Identifikasi dalam aplikasi digital merujuk kepada proses pengesahan identiti pengguna untuk memastikan bahawa hanya individu yang sah dapat mengakses aplikasi tersebut. Ini penting untuk keselamatan data dan privasi pengguna. Proses ini biasanya melibatkan beberapa langkah atau elemen seperti:

Nama Pengguna dan Kata Laluan: Pengguna perlu memasukkan nama pengguna dan kata laluan yang unik untuk log masuk ke dalam aplikasi.
Pengesahan Pelbagai Faktor (MFA): Selain daripada nama pengguna dan kata laluan, pengguna mungkin perlu memasukkan kod yang dihantar ke telefon bimbit atau e-mel mereka untuk lapisan keselamatan tambahan.
Biometrik: Pengenalan melalui cap jari, pengecaman wajah, atau imbasan retina yang memastikan hanya pengguna sebenar yang boleh mengakses aplikasi.
Token atau Kunci Keselamatan: Pengguna perlu menggunakan token fizikal atau kunci keselamatan digital untuk mengesahkan identiti mereka.
Soalan Keselamatan: Soalan peribadi yang hanya diketahui oleh pengguna sebagai langkah tambahan untuk mengesahkan identiti mereka.
Identifikasi pengguna perkhidmatan kerajaan dikelaskan mengikut empat (4) kategori iaitu Warganegara, Bukan Warganegara, Penduduk Tetap dan Penjawat Awam seperti berikut:

Warganegara merupakan Individu yang mempunyai kewarganegaraan Malaysia secara sah. Mereka mempunyai hak dan tanggungjawab penuh sebagai rakyat Malaysia, termasuk hak untuk mengundi, bekerja tanpa sekatan, dan menerima kemudahan daripada kerajaan. Pengesahan identiti warganegara boleh dilaksanakan dengan kerjasama Jabatan Pendaftaran Negara (JPN). Identiti lengkap pengguna boleh diperoleh menerusi MyGovernment oleh Jabatan Digital Negara (JDN) dan PADU oleh Kementerian Ekonomi (KE) sekiranya pengguna telah sedia mendaftar kedua-dua platform tersebut.
Bukan Warganegara merupakan individu yang bukan pemegang kewarganegaraan Malaysia. Mereka mungkin berada di Malaysia untuk tujuan sementara seperti pelancongan, pekerjaan, atau pendidikan, tetapi tidak mempunyai hak penuh seperti warganegara Malaysia. Pengesahan identiti bukan warganegara boleh dilaksanakan dengan kerjasama Jabatan Imigresen Malaysia (JIM).
Penduduk Tetap merupakan Individu yang bukan warganegara Malaysia tetapi diberi status untuk menetap secara tetap di Malaysia. Mereka mempunyai beberapa hak yang serupa dengan warganegara, seperti bekerja dan tinggal di Malaysia tanpa had masa, tetapi tidak mempunyai hak untuk mengundi. Pengesahan identiti bukan warganegara boleh dilaksanakan dengan kerjasama Jabatan Imigresen Malaysia (JIM).
Penjawat Awam merupakan Individu yang bekerja di sektor awam atau kerajaan Malaysia. Pengesahan identiti bukan warganegara boleh dilaksanakan dengan kerjasama Jabatan Perkhidmatan Awam (JPA) menerusi aplikasi HRMIS.
Agensi kerajaan merupakan agensi Persekutuan, Badan Berkanun dan Negeri yang mempunyai akses kepada perkhidmatan digital khusus dan umum kerajaan.
Log masuk pengguna yang bertaraf Warganegara dan Penjawat Awam ke perkhidmatan kerajaan hendaklah menggunakan Identiti Digital Nasional (IDN). Dengan ini pengguna tidak perlu mengingati pelbagai nama dan kata laluan yang berbeza.

## Pelbagai Platform

Perkhidmatan boleh disediakan dalam dua platform utama iaitu secara web dan mobile. Kedua-dua platform ini diperlukan bersesuaian dengan teknologi semasa yang digunakan oleh pemegang taruh. Pembangunan platform perlu mematuhi Rekabentuk Komponen UI/UX yang dilampirkan bersama garis panduan ini. Berikut merupakan ciri-ciri utama dalam penyediaan pelbagai platform:

Web merupakan perkhidmatan digital yang diakses melalui pelayar web pada komputer atau peranti mudah alih. Pembangunan perkhidmatan yang berasaskan kepada teknologi web hendaklah mengambil kira elemen berikut:
Pembangunan web responsif untuk menyesuaikan dengan pelbagai saiz skrin.
Beroperasi secara dalam talian dan memerlukan sambungan Internet.
Memaparkan fungsi penuh perkhidmatan pada pelbagai jenis peranti dengan paparan antara muka skrin yang lebih besar.
Sesuai digunakan bagi perkhidmatan dengan fungsi yang kompleks, mempunyai pelbagai aliran proses serta medan input yang banyak.
Boleh dilarikan sekurang-kurangnya menggunakan pelayar web seperti Chrome, Firefox, Opera dan Safari.
Platform dapat menampung (compatible) setiap pelayar web terutamanya bagi komponen Javascript dan Cascading Style Sheet (CSS).
Mobile merupakan perkhidmatan digital yang diakses melalui aplikasi mudah alih pada telefon pintar atau tablet. Pembangunan perkhidmatan yang berasaskan kepada teknologi mobile hendaklah mengambil kira elemen berikut:
Boleh dicapai menerusi peranti mobile yang menggunakan skrin sentuh pada paparan antara muka skrin yang lebih kecil.
Beroperasi secara dalam atau luar talian bergantung pada fungsi aplikasi.
Sesuai bagi perkhidmatan yang mempunyai medan input yang sedikit serta fungsi yang ringkas (lightweight) seperti Carian, Semakan, Permohonan, dan Bayaran.
Boleh dilarikan sekurang-kurangnya menggunakan platform Android, IOS dan Huawei.
Sentiasa mengemaskini platform mengikut perubahan versi teknologi dari semasa ke semasa.
Kiosk merupakan perkhidmatan digital yang diakses melalui peranti berdiri sendiri (terminal kiosk) yang biasanya ditempatkan di lokasi tertentu seperti di pejabat kerajaan atau premis-premis terbuka. Pembangunan perkhidmatan kiosk hendaklah mengambil kira elemen berikut:
Berfungsi sebagai stesen perkhidmatan mandiri.
Akses melalui peranti fizikal dengan skrin sentuh besar.
Terminal yang menyediakan perkhidmatan asas seperti direktori, semakan dan carian.
Lazimnya data disimpan secara local atau Intranet.
Sesuai diletakkan di kawasan tumpuan umum sama ada premis kerajaan atau swasta.
Agensi hendaklah memastikan akses kepada platform ini dilindungi menggunakan protokol keselamatan SSL (Secure Sockets Layer) yang boleh dilanggan daripada JDN menerusi perkhidmatan Prasarana Kunci Awam Kerajaan (Government Public Key Infrastructure – GPKI) dengan merujuk kepada Dasar Perkhidmatan Prasarana Kunci Awam Kerajaan.

## Direktori Perkhidmatan

Direktori Perkhidmatan merupakan platform yang menempatkan informasi berkenaan perkhidmatan digital yang telah dibangunkan oleh agensi kerajaan. Direktori perkhidmatan boleh digunakan sebagai rujukan kepada agensi sebelum membangunkan sesuatu perkhidmatan. Tujuannya bagi mengatasi pertindanan fungsi, proses dan prosedur di antara agensi yang akhirnya menyebabkan karenah birokrasi dan mengelirukan pengguna. Selain itu direktori perkhidmatan dapat dimanfaatkan bagi menggalakkan perkongsian pintar dan mengoptimum perkhidmatan sedia ada.

Sebagai contoh, sistem generik seperti Tempahan Bilik Mesyuarat telah banyak dibangunkan oleh pelbagai agensi. Sistem ini lazimnya mempunyai fungsi dan proses yang sama. Agensi yang berhasrat untuk membangunkan sistem yang sama, boleh mendapatkan persetujuan daripada agensi pemilik bagi kebenaran perkongsian kod sumber. Dengan ini, masa dan kos pembangunan semula dapat dijimatkan.

Maklumat berkenaan perkhidmatan boleh dikemaskini menerusi aplikasi RASA yang dibangunkan oleh JDN. Agensi kerajaan boleh merujuk kepada RASA terlebih dahulu sebagai langkah awal semasa merancang untuk membangunkan perkhidmatan. Rujukan kepada RASA ini berkuatkuasa sehingga aplikasi ICT Project Management & Monitoring System (PRIMMS) dilancarkan pada tahun 2026. Selain itu katalog data yang menetapkan kod dan sintaks data generik seperi Negeri, Daerah, Kementerian, Jantina dan lain-lain boleh diakses menerusi platform Kamus Data Sektor Awam (DDSA) dengan merujuk kepada pekeliling Penggunaan Dan Pemakaian Data Dictionary Sektor Awam (DDSA) Sebagai Standard Di Agensi-Agensi Kerajaan.

## Platform Bersepadu

Platform bersepadu menyatukan pelbagai komponen, perkhidmatan, atau aplikasi ke dalam satu ekosistem yang koheren dan berfungsi dengan lancar. Platform ini direka untuk memudahkan pemilik perkhidmatan dan pengguna untuk mengakses, mengurus dan menyelenggara pelbagai fungsi dan perkhidmatan dalam satu ekosistem perkhidmatan secara bersepadu tanpa beralih kepada aplikasi yang berbeza. Interaksi dan integrasi antara perkhidmatan dapat dilakukan dengan lebih lancar di dalam satu platform. Berikut merupakan strategi pelaksanaan platform bersepadu:

Identifikasi Keperluan: Mengenal pasti keperluan perniagaan dan fungsi yang perlu disepadukan.
Pemilihan Teknologi: Memilih teknologi dan alat yang sesuai untuk integrasi berdasarkan kepada fungsi.
Reka Bentuk Sistem: Merancang reka bentuk sistem yang menyatukan pelbagai komponen bagi mendapatkan pengalaman pengguna yang seragam.
Pembangunan dan Pengujian: Membangunkan dan menguji platform untuk memastikan keserasian dan fungsi yang lancar.
Pelaksanaan dan Latihan: Melaksanakan platform dan memberikan latihan kepada pengguna untuk memastikan penggunaan yang efektif.
Gerbang Rasmi Kerajaan Malaysia (MyGovernment) merupakan platform bersepadu bagi menempatkan perkhidmatan utama kerajaan mengikut pesona yang boleh dilayari menerusi capaian malaysia.gov.my. Setiap agensi perlu mematuhi ekosistem dan standard UI/UX yang tersedia bagi membolehkan proses kemasukan (on-boarding) perkhidmatan ke dalam platform MyGovernment. Setiap perkhidmatan yang disediakan di dalam MyGovernment dikendalikan secara modular menggunakan konsep Aplikasi Mini (MiniApp). Bagi memudahkan kemasukan agensi ke dalam MyGovernment mengikut standard yang ditetapkan, agensi hendaklah mematuhi ekosistem MiniApp yang disediakan.

## Perkhidmatan Perisian

Perkhidmatan Perisian atau Software as a Service (SaaS) merupakan aplikasi generik yang boleh digunakan oleh agensi secara gunasama melalui Internet tanpa perlu memasangnya pada persekitaran/komputer mereka sendiri. Aplikasi ini lazimnya dihoskan di Pusat Data Sektor Awam (PDSA) dan boleh digunakan oleh agensi secara percuma mengikut tahap kebenaran dan akses yang ditetapkan. Penyediaan perkhidmatan aplikasi dapat mengawal agensi daripada membangunkan pelbagai perkhidmatan yang sama dan bertindan antara satu sama lain.

Contoh SaaS yang disediakan oleh JDN adalah seperti berikut:

DDMS: Digital Document Management System (DDMS) merupakan sistem pengurusan rekod yang menguruskan rekod rasmi dan rahsia rasmi kerajaan bagi keseluruhan kitaran hayatnya iaitu daripada proses pewujudan, penawanan, penyimpanan, penyelenggaraan, pengedaran dan pelupusan rekod secara digital dan sistematik. DDMS dapat meningkatkan ketelusan pentadbiran dalam persekitaran selamat dan sistematik serta memastikan pengurusan rekod elektronik yang lebih efisien secara digital bagi meningkatkan prestasi sistem penyampaian kerajaan. Sistem ini boleh dilayari menerusi pautan ddms.malaysia.gov.my.

## Keselamatan

Asas keselamatan: Triad CIA — Kerahsiaan, Integriti, Ketersediaan.

Amalan & kawalan: Secure coding & DevSecOps, pengurusan tampalan, IPS/WAF, MFA/SSO/IDN, audit keselamatan, penyulitan end-to-end, HSM, pematuhan ISO/IEC 27001/NIST.

Rujukan:

- Surat Pekeliling Am Bil 4 Tahun 2024 – Garis Panduan Penilaian Tahap Keselamatan Rangkaian dan Sistem ICT Sektor Awam
- Surat Pekeliling Am Bil 4 Tahun 2022 – Pengurusan dan Pengendalian Insiden Keselamatan Siber Sektor Awam

## Pemantauan

Pemantauan membantu meningkatkan ketersediaan sistem, prestasi, keselamatan, pelaporan, pengurusan kapasiti dan penambahbaikan berterusan.

Inisiatif & alat: MyRAM, SPLASK, perisian analitik web.

## Pautan Pantas dan Rujukan

- Portal Sistem Repositori MyGovEA
- Rujukan Enterprise Architecture
- Portal Rasmi Kerajaan Malaysia ([https://malaysia.gov.my](https://malaysia.gov.my))
- IASA — An Association for all IT Architect
- Portal Rasmi Jabatan Digital Negara ([https://jdn.gov.my](https://jdn.gov.my))
- RASA (JDN)
- PRIMMS (pelancaran 2026)
- DDSA — Kamus Data Sektor Awam
- DRSA ([https://drsa.gov.my](https://drsa.gov.my))
- MyGDX — MyGovernment Data Exchange
- Dasar Perkongsian Data Sektor Awam
- Pekeliling Pelaksanaan Data Terbuka Sektor Awam
- MyGovCloud / PDSA / MyGovUC / MyGov\*Net

## Hubungi Kami

JABATAN DIGITAL NEGARA
Bangunan MKN Embassy Techzone
Blok B, No. 3200 Jalan Teknokrat 2
63000 Cyberjaya, Sepang
Selangor Darul Ehsan

Tel: 603-8000 8000
E-mel: [bsa_ea@jdn.gov.my](mailto:bsa_ea@jdn.gov.my)

Bilangan Pelawat Hari Ini: 31
Jumlah Pelawat: 67560

Laman ini sesuai dipapar menggunakan pelayar web Microsoft Edge & Google Chrome versi terkini dengan resolusi minima 1366×768

© 2025 All Rights Reserved.
