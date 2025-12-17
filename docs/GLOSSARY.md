# Glosari Istilah Sistem ICTServe (System Glossary)

Sistem Helpdesk & ICT Asset Loan MOTAC BPM

| Atribut          | Nilai                                                                                     |
| :--------------- | :---------------------------------------------------------------------------------------- |
| Versi            | 3.6.1 (SemVer)                                                                            |
| Tarikh Kemaskini | 17 Disember 2025                                                                          |
| Status           | Aktif - Penyeragaman Mengikut D00-D18                                                     |
| Klasifikasi      | Terhad - Dalaman MOTAC                                                                    |
| Penulis          | Pasukan Pembangunan BPM MOTAC                                                             |
| Standard Rujukan | ISO/IEC/IEEE 29148, ISO 8000, ISO 9001, ISO/IEC/IEEE 12207, WCAG 2.2 AA, MyGOV DSS v2.1.0 |

---

## Maklumat Dokumen (Document Information)

| Atribut          | Nilai                                                                                     |
| :--------------- | :---------------------------------------------------------------------------------------- |
| Versi            | 3.6.1                                                                                     |
| Tarikh Kemaskini | 17 Disember 2025                                                                          |
| Status           | Aktif - Penyeragaman Mengikut D00-D18                                                     |
| Klasifikasi      | Terhad - Dalaman MOTAC                                                                    |
| Pematuhi         | ISO/IEC/IEEE 29148, ISO 8000, ISO 9001, ISO/IEC/IEEE 12207, WCAG 2.2 AA, MyGOV DSS v2.1.0 |
| Bahasa           | Bahasa Melayu sahaja (v3.6.0+)                                                            |

> Notis Penggunaan Dalaman: Semua istilah dalam glosari ini digunakan untuk sistem dalaman MOTAC (internal use only) dan bukan untuk kegunaan awam.

---

## Tujuan Dokumen (Purpose)

Dokumen ini menyediakan definisi komprehensif untuk semua istilah, akronim, dan konsep teknikal yang digunakan dalam sistem ICTServe dan dokumentasinya. Ia berfungsi sebagai rujukan terpusat untuk memastikan konsistensi penggunaan istilah di seluruh dokumentasi dan kod sistem.

---

## Cara Menggunakan Glosari Ini (How to Use This Glossary)

- **Istilah** disusun mengikut abjad untuk memudahkan carian
- **Definisi** memberikan penjelasan ringkas tetapi jelas tentang istilah tersebut
- **Konteks** menunjukkan bidang atau kategori penggunaan istilah
- Untuk istilah teknikal, rujuk dokumentasi terspesialisasi yang berkaitan untuk butiran lanjut

---

## Istilah Teknikal dan Akronim

| Istilah                                       | Definisi                                                                                                                       | Konteks                     |
| :-------------------------------------------- | :----------------------------------------------------------------------------------------------------------------------------- | :-------------------------- |
| Account Linking / Pautan Akaun                | Proses menghubungkan penyerahan tetamu (guest submissions) kepada akaun pengguna berdaftar berdasarkan padanan e-mel           | Teknikal - Autentikasi      |
| Active Directory (AD)                         | Perkhidmatan direktori Microsoft untuk pengurusan pengguna, komputer, dan sumber rangkaian dalam domain Windows                | Autentikasi & Keselamatan   |
| Activity Log                                  | Pakej Spatie untuk merekod aktiviti pengguna secara terperinci (berbeza dari audit trail untuk pematuhan)                      | Teknikal - Pengauditan      |
| AI Chatbot                                    | Sistem chatbot berkuasa AI menggunakan Cloud Hybrid Architecture (Ollama + AWS Bedrock) untuk sokongan pengguna (D18 v1.0.1)   | Teknikal - AI               |
| API                                           | Application Programming Interface - Antara muka untuk integrasi dan komunikasi antara sistem                                   | Teknikal - Integrasi        |
| AWS Bedrock                                   | Perkhidmatan AI terurus Amazon yang menyediakan akses kepada model Claude (Opus 4.5, Sonnet 4.5, Haiku 4.5)                    | Teknikal - AI               |
| API Token                                     | Token Laravel Sanctum untuk akses API dengan abilities dan expiration yang boleh dikonfigurasi                                 | Teknikal - Keselamatan API  |
| Audit Trail                                   | Rekod kronologi aktiviti sistem yang merekod siapa, apa, bila, dan di mana sesuatu tindakan dilakukan                          | Keselamatan & Pematuhan     |
| BPM                                           | Bahagian Pengurusan Maklumat - Unit organisasi yang menguruskan aset dan inventori ICT                                         | Organisasi                  |
| Broadcasting                                  | Sistem penyiaran mesej masa nyata menggunakan WebSocket untuk notifikasi dan kemas kini langsung                               | Teknikal - Real-time        |
| Caching                                       | Teknik penyimpanan data sementara untuk meningkatkan prestasi dan mengurangkan beban pelayan                                   | Teknikal - Prestasi         |
| ClamAV                                        | Perisian antivirus open-source untuk mengimbas fail yang dimuat naik sebelum disimpan                                          | Teknikal - Keselamatan      |
| Cloud Hybrid AI Architecture                  | Seni bina hibrid v3.6.0 yang menggabungkan pemprosesan AI tempatan (Ollama) dan cloud (AWS Bedrock) dengan model routing pintar | Teknikal - AI               |
| CRUD                                          | Create, Read, Update, Delete - Operasi data asas dalam sistem pengurusan                                                       | Teknikal - Operasi Data     |
| Domain-Driven Design (DDD)                    | Pendekatan reka bentuk perisian yang memberi tumpuan kepada pemodelan domain perniagaan                                        | Seni Bina Perisian          |
| DRP                                           | Disaster Recovery Plan - Pelan Pemulihan Bencana untuk kesinambungan perniagaan                                                | Operasi & Pengurusan Risiko |
| Dual Audit System                             | Sistem audit dua lapisan menggunakan owen-it/laravel-auditing untuk pematuhan dan spatie/laravel-activitylog untuk operasi     | Teknikal - Pengauditan      |
| Eloquent ORM                                  | Laravel's Object-Relational Mapping system untuk berinteraksi dengan pangkalan data menggunakan objek PHP                      | Teknikal - Pangkalan Data   |
| Email Verification / Pengesahan E-mel         | Proses mengesahkan e-mel pengguna selepas pendaftaran sendiri sebelum akses penuh diberikan                                    | Teknikal - Autentikasi      |
| Filament                                      | Framework admin panel untuk Laravel yang menyediakan antara muka CRUD yang kaya (v4.1.10)                                      | Teknologi Stack             |
| Flexible Login / Log Masuk Fleksibel          | Keupayaan log masuk menggunakan e-mel penuh atau nama pengguna pendek (selepas pendaftaran)                                    | Teknikal - Autentikasi      |
| Foreign Key                                   | Medan dalam jadual pangkalan data yang merujuk kepada primary key jadual lain untuk mewujudkan hubungan                        | Teknikal - Pangkalan Data   |
| Google Workspace SSO                          | Single Sign-On menggunakan akaun Google Workspace @motac.gov.my melalui Laravel Socialite (opsyen)                             | Teknikal - Autentikasi      |
| HA                                            | High Availability - Ketersediaan Tinggi untuk memastikan sistem beroperasi dengan masa henti minimum                           | Teknikal - Infrastruktur    |
| Helpdesk                                      | Sistem sokongan teknikal untuk menangani aduan dan permintaan bantuan pengguna                                                 | Modul Sistem                |
| Horizon                                       | Dashboard Laravel untuk pemantauan queue (opsyenal). **Tidak dipasang** dalam repo v3.6.1; pemantauan queue menggunakan Laravel Pulse + Filament Failed Jobs/Email Logs | Teknikal - Queue Management |
| HRMIS                                         | Human Resource Management Information System - Sistem maklumat pengurusan sumber manusia MOTAC                                 | Sistem Luaran               |
| ICT                                           | Information and Communication Technology - Teknologi Maklumat dan Komunikasi                                                   | Am                          |
| Idempotency                                   | Sifat operasi yang memastikan pelaksanaan berulang menghasilkan hasil yang sama tanpa kesan sampingan berganda                 | Teknikal - API              |
| JWT                                           | JSON Web Token - Standard untuk mencipta token akses yang membawa tuntutan yang ditandatangani                                 | Keselamatan - Autentikasi   |
| KPI                                           | Key Performance Indicator - Penunjuk Prestasi Utama untuk mengukur kejayaan objektif                                           | Pengurusan & Analitik       |
| Laravel                                       | Framework PHP open-source untuk pembangunan aplikasi web dengan sintaks yang elegan (v12.42.0)                                 | Teknologi Stack             |
| Laravel Breeze                                | Scaffolding autentikasi Laravel yang ringan untuk login, registration, dan password reset (v2.3.8)                             | Teknologi Stack             |
| Laravel Echo                                  | Library JavaScript untuk subscription kepada channels dan listening events dalam aplikasi Laravel (v2.2.6)                     | Teknikal - Real-time        |
| Laravel Pulse                                 | Dashboard pemantauan prestasi masa nyata untuk admin/superuser - slow queries, queue metrics, server health (v1.4.6)           | Teknikal - Monitoring       |
| Laravel Reverb                                | WebSocket server Laravel untuk komunikasi masa nyata yang berprestasi tinggi (v1.6.3)                                          | Teknikal - Real-time        |
| Laravel Sanctum                               | Sistem autentikasi API token-based dengan abilities dan expiration management (v4.2.1)                                         | Teknikal - Keselamatan API  |
| Laravel Socialite                             | Library OAuth 2.0 untuk integrasi dengan Google Workspace SSO (v5.24.0)                                                        | Teknikal - Autentikasi      |
| Laravel Telescope                             | Alat debugging dan monitoring untuk superuser - requests, jobs, exceptions, queries, mail (v5.16.0)                            | Teknikal - Debugging        |
| LDAP                                          | Lightweight Directory Access Protocol - Protokol untuk mengakses dan menyelenggara perkhidmatan maklumat direktori             | Autentikasi & Keselamatan   |
| Livewire                                      | Framework full-stack Laravel untuk membina antara muka dinamik tanpa meninggalkan PHP (v3.7.1)                                 | Teknologi Stack - Frontend  |
| Migration                                     | Fail PHP yang menentukan perubahan skema pangkalan data dan membolehkan version control untuk database                         | Teknikal - Pangkalan Data   |
| MOTAC                                         | Kementerian Pelancongan, Seni dan Budaya Malaysia                                                                              | Organisasi                  |
| MVC                                           | Model-View-Controller - Corak seni bina perisian yang memisahkan logik aplikasi                                                | Teknikal - Seni Bina        |
| My Dashboard / Portal Staf                    | Portal peribadi untuk staf berdaftar - sejarah tiket/pinjaman, profil, notifikasi, pautan penyerahan lama                      | Modul Sistem                |
| MyGOV DSS                                     | MyGOV Digital Service Standards v2.1.0 - Standard perkhidmatan digital kerajaan Malaysia                                       | Standard - Pematuhan        |
| Model Routing                                 | Penghalaan automatik permintaan AI kepada model yang paling sesuai berdasarkan jenis tugas dan kompleksiti                     | Teknikal - AI               |
| MySQL                                         | Sistem pengurusan pangkalan data relasi open-source yang digunakan oleh ICTServe (v8.0+)                                       | Teknologi Stack             |
| Notification Preferences / Tetapan Notifikasi | Tetapan pengguna untuk kekerapan e-mel (serta-merta, harian, mingguan) dan notifikasi dalam aplikasi                           | Teknikal - Notifikasi       |
| OAuth 2.0                                     | Protokol autorisasi standard untuk akses API dan SSO (digunakan oleh Laravel Socialite)                                        | Teknikal - Autentikasi      |
| Ollama                                        | Pelayan Large Language Model tempatan yang menyediakan keupayaan AI tanpa kebergantungan API luaran                            | Teknikal - AI               |
| ORM                                           | Object-Relational Mapping - Teknik untuk menukar data antara sistem objek dan pangkalan data relasi                            | Teknikal - Pangkalan Data   |
| PDPA                                          | Personal Data Protection Act - Akta Perlindungan Data Peribadi Malaysia 2010                                                   | Undang-undang & Pematuhan   |
| Primary Key                                   | Medan unik dalam jadual pangkalan data yang mengenal pasti setiap rekod                                                        | Teknikal - Pangkalan Data   |
| Pusher                                        | Perkhidmatan WebSocket yang menyediakan protokol untuk komunikasi masa nyata                                                   | Teknikal - Real-time        |
| Queue                                         | Sistem barisan untuk pemprosesan tugas latar belakang secara asinkron                                                          | Teknikal - Prestasi         |
| RAG                                           | Retrieval-Augmented Generation - teknik AI yang menggabungkan pengambilan dokumen dengan penjanaan bahasa                      | Teknikal - AI               |
| Rate Limiting                                 | Kawalan had permintaan API/form untuk mencegah penyalahgunaan (60/min API, 10/min forms)                                       | Teknikal - Keselamatan      |
| RBAC                                          | Role-Based Access Control - Kawalan Akses Berasaskan Peranan untuk menguruskan kebenaran                                       | Keselamatan - Autorisasi    |
| Redis                                         | Sistem penyimpanan data dalam memori yang digunakan untuk cache, session, dan queue backend (v7.0+)                            | Teknologi Stack             |
| Repository Pattern                            | Corak reka bentuk yang memisahkan logik akses data dari logik perniagaan                                                       | Teknikal - Seni Bina        |
| REST                                          | Representational State Transfer - Gaya seni bina web untuk API                                                                 | Teknikal - API              |
| RTO                                           | Recovery Time Objective - Objektif Masa Pemulihan selepas bencana                                                              | Operasi - DRP               |
| RPO                                           | Recovery Point Objective - Objektif Titik Pemulihan untuk kehilangan data maksimum                                             | Operasi - DRP               |
| Seeder                                        | Fail PHP yang mengisi pangkalan data dengan data awal atau ujian                                                               | Teknikal - Pangkalan Data   |
| Self-Registration / Pendaftaran Sendiri       | Keupayaan staf MOTAC mendaftar akaun sendiri menggunakan e-mel @motac.gov.my tanpa campur tangan pentadbir                     | Teknikal - Autentikasi      |
| Service Layer                                 | Lapisan dalam aplikasi yang mengandungi logik perniagaan kompleks                                                              | Teknikal - Seni Bina        |
| Signed Approval Link                          | Pautan e-mel ber-token yang membolehkan kelulusan tanpa log masuk (untuk approvers Gred 41+)                                   | Teknikal - Kelulusan        |
| SLA                                           | Service Level Agreement - Perjanjian Tahap Perkhidmatan yang menentukan masa respons dan penyelesaian                          | Operasi & Pengurusan        |
| Soft Delete                                   | Teknik menandakan rekod sebagai dipadam tanpa mengalih keluar dari pangkalan data                                              | Teknikal - Pangkalan Data   |
| Spatie Permission                             | Package Laravel untuk menguruskan peranan dan kebenaran pengguna (v6.23)                                                       | Teknologi Stack             |
| SSL/TLS                                       | Secure Sockets Layer/Transport Layer Security - Protokol keselamatan untuk komunikasi rangkaian                                | Keselamatan - Enkripsi      |
| SSE                                           | Server-Sent Events - protokol untuk streaming data dari pelayan ke klien untuk respons AI masa nyata                           | Teknikal - AI               |
| SSoT                                          | Single Source of Truth - Prinsip di mana data atau maklumat disimpan di satu lokasi utama sahaja                               | Konsep - Pengurusan Data    |
| Streaming Responses                           | Respons AI yang dihantar secara berperingkat untuk pengalaman pengguna yang lebih responsif                                    | Teknikal - AI               |
| Supervisor                                    | Process monitor untuk Linux yang memastikan queue workers sentiasa berjalan                                                    | Teknikal - Queue Management |
| Tailwind CSS                                  | Framework CSS utility-first untuk membina antara muka pengguna (v4.1.17)                                                       | Teknologi Stack - Frontend  |
| TOTP                                          | Time-based One-Time Password - Kata laluan sekali guna berasaskan masa untuk 2FA superuser                                     | Keselamatan - 2FA           |
| True Hybrid Architecture                      | Seni bina v3.6.0 yang menggabungkan Guest-First (penyerahan tanpa log masuk) dengan self-registration pilihan untuk staf MOTAC | Seni Bina Perisian          |
| UUID                                          | Universally Unique Identifier - Pengecam Unik Sejagat (format 128-bit)                                                         | Teknikal - Pengecam Data    |
| Vite                                          | Build tool moden untuk projek frontend yang pantas dan ringan (v7.0.7)                                                         | Teknologi Stack - Frontend  |
| Volt                                          | Single-file Livewire components dengan simplified syntax (v1.10.1)                                                             | Teknologi Stack - Frontend  |
| WCAG                                          | Web Content Accessibility Guidelines - Garis panduan aksesibiliti kandungan web (v2.2 AA)                                      | Standard - Aksesibiliti     |
| Web-Augmented Responses                       | Respons AI yang diperkaya dengan maklumat terkini dari carian web (DuckDuckGo integration)                                     | Teknikal - AI               |
| WebSocket                                     | Protokol komunikasi dua hala yang membolehkan pertukaran data masa nyata antara pelayan dan pelanggan                          | Teknikal - Real-time        |
| Worker                                        | Proses latar belakang yang memproses jobs dari queue secara berterusan                                                         | Teknikal - Queue Management |

---

## Istilah Domain Perniagaan

| Istilah              | Definisi                                                                                                  | Konteks             |
| :------------------- | :-------------------------------------------------------------------------------------------------------- | :------------------ |
| Aset ICT             | Peralatan atau peranti teknologi maklumat seperti laptop, projektor, tablet yang boleh dipinjam           | Domain - Pinjaman   |
| Auto-Assignment      | Proses automatik menugaskan tiket atau permohonan kepada pegawai yang sesuai berdasarkan peraturan sistem | Domain - Helpdesk   |
| Escalation           | Proses meningkatkan keutamaan atau menaikkan isu kepada tahap pengurusan yang lebih tinggi                | Domain - Helpdesk   |
| Gred Jawatan         | Tahap jawatan pegawai dalam perkhidmatan awam (contoh: Gred 41, 44, 48, JUSA)                             | Domain - Organisasi |
| Inventori            | Senarai lengkap aset ICT yang tersedia, dipinjam, atau dalam penyelenggaraan                              | Domain - Pinjaman   |
| Kelulusan Berjenjang | Proses kelulusan yang melalui beberapa peringkat pegawai berdasarkan hierarki atau nilai aset             | Domain - Pinjaman   |
| Keutamaan Tiket      | Tahap kepentingan tiket helpdesk (Kritikal, Tinggi, Sederhana, Rendah)                                    | Domain - Helpdesk   |
| Matriks Kelulusan    | Jadual yang menentukan siapa pelulus yang sesuai berdasarkan gred pemohon dan nilai aset                  | Domain - Pinjaman   |
| Pegawai Penyokong    | Pegawai atasan yang berperanan meluluskan permohonan pinjaman                                             | Domain - Pinjaman   |
| Pemohon              | Pengguna yang membuat permohonan pinjaman aset ICT                                                        | Domain - Pinjaman   |
| Peralatan Tersedia   | Aset ICT yang berada di stor dan boleh dipinjam                                                           | Domain - Pinjaman   |
| Staf BPM             | Pegawai dari Bahagian Pengurusan Maklumat yang menguruskan transaksi aset                                 | Domain - Pinjaman   |
| Status Pinjaman      | Keadaan semasa permohonan pinjaman dalam aliran kerja                                                     | Domain - Pinjaman   |
| Status Tiket         | Keadaan semasa tiket helpdesk dalam kitaran hayatnya                                                      | Domain - Helpdesk   |
| Tempoh Pinjaman      | Jangka masa aset ICT boleh dipinjam (biasanya 7-60 hari bergantung kepada jenis aset dan gred)            | Domain - Pinjaman   |
| Tiket Helpdesk       | Rekod aduan atau permintaan sokongan teknikal yang dihantar oleh pengguna                                 | Domain - Helpdesk   |
| Transaksi            | Rekod pengeluaran atau pemulangan aset ICT                                                                | Domain - Pinjaman   |

---

## Istilah Peranan Sistem

| Istilah (Teknikal) | Nama Deskriptif             | Definisi                                               | Tanggungjawab Utama                                                                    |
| :----------------- | :-------------------------- | :----------------------------------------------------- | :------------------------------------------------------------------------------------- |
| `superuser`        | Pentadbir Super             | Pentadbir tertinggi dengan akses penuh kepada sistem   | Konfigurasi sistem, pengurusan pengguna, backup, keselamatan, Telescope, Pulse, audit  |
| `admin`            | Pentadbir Sistem            | Pentadbir dengan akses luas untuk operasi harian       | Pemantauan sistem, pengurusan kandungan, laporan, Pulse, Failed Jobs (queue)           |
| `staff`            | Staf Berdaftar              | Staf MOTAC yang mendaftar sendiri dengan @motac.gov.my | Melihat sejarah penyerahan, menerima notifikasi, pautkan penyerahan tetamu, My Dashboard |
| (Guest)            | Tetamu / Staf MOTAC         | Staf MOTAC menggunakan guest forms tanpa login         | Submit helpdesk tickets dan loan applications via guest form                           |
| (Approver)         | Pegawai Penyokong / Pelulus | Pegawai yang meluluskan permohonan (Gred 41+)          | Meluluskan/menolak permohonan via signed email tokens                                  |

> **Nota True Hybrid v3.6.0:**
>
> - **Guest** boleh memilih untuk mendaftar akaun selepas penyerahan untuk akses yang lebih baik
> - **Staff** yang mendaftar sendiri mesti mengesahkan e-mel sebelum akses penuh
> - **Staff** boleh log masuk menggunakan e-mel penuh ATAU nama pengguna pendek
> - **Staff** boleh log masuk menggunakan Google Workspace SSO (opsyen)
> - **Approvers** tidak memerlukan akaun sistem - kelulusan melalui pautan e-mel yang ditandatangani
> - **Admin/Superuser** sahaja yang memerlukan pengurusan akaun manual oleh pentadbir
> - **Superuser** mempunyai akses penuh ke Laravel Telescope dan Laravel Pulse
> - **Admin** mempunyai akses ke Laravel Pulse; pemantauan queue melalui Failed Jobs/Email Logs (Filament). Laravel Horizon tidak dipasang dalam repo v3.6.1.
> - **Bahasa Melayu sahaja** - penukar bahasa dilumpuhkan dalam v3.6.0
> - **AI Chatbot** - Cloud Hybrid AI Architecture (Ollama + AWS Bedrock) tersedia untuk semua pengguna (D18)

---

## Status dan Aliran Kerja

### Status Pinjaman Aset ICT

| Status           | Definisi                                             | Tindakan Seterusnya             |
| :--------------- | :--------------------------------------------------- | :------------------------------ |
| `Draft`          | Permohonan masih dalam draf dan belum dihantar       | Pengguna boleh edit atau hantar |
| `Submitted`      | Permohonan telah dihantar dan menunggu semakan       | Sistem tugaskan kepada pelulus  |
| `Under_Review`   | Permohonan sedang disemak oleh pelulus               | Pelulus buat keputusan          |
| `Pending_Info`   | Permohonan memerlukan maklumat tambahan dari pemohon | Pemohon kemaskini maklumat      |
| `Approved`       | Permohonan telah diluluskan                          | Staf BPM sediakan aset          |
| `Rejected`       | Permohonan ditolak                                   | Proses tamat                    |
| `Ready_Issuance` | Aset sedia untuk diambil                             | Staf BPM keluarkan aset         |
| `Issued`         | Aset telah dikeluarkan kepada pemohon                | Pemohon gunakan aset            |
| `In_Use`         | Aset sedang digunakan oleh pemohon                   | Tunggu tarikh pulang            |
| `Return_Due`     | Tarikh pulang hampir atau sudah sampai               | Pemohon pulangkan aset          |
| `Returning`      | Proses pemulangan sedang berlaku                     | Staf BPM periksa aset           |
| `Returned`       | Aset telah dipulangkan dan disahkan                  | Staf BPM kemaskini inventori    |
| `Completed`      | Transaksi pinjaman selesai sepenuhnya                | Proses tamat                    |

### Status Tiket Helpdesk

| Status              | Definisi                                        | Tindakan Seterusnya                 |
| :------------------ | :---------------------------------------------- | :---------------------------------- |
| `Baru`              | Tiket baru dicipta dan belum ditugaskan         | Sistem atau admin tugaskan agen     |
| `Ditugaskan`        | Tiket telah ditugaskan kepada agen IT           | Agen mulakan kerja                  |
| `Dalam_Proses`      | Agen sedang menyelesaikan isu                   | Agen selesaikan atau minta maklumat |
| `Menunggu_Pengguna` | Agen memerlukan maklumat tambahan dari pengguna | Pengguna berikan maklumat           |
| `Selesai`           | Isu telah diselesaikan oleh agen                | Pengguna sahkan penyelesaian        |
| `Disahkan`          | Pengguna mengesahkan isu telah selesai          | Sistem tutup tiket                  |
| `Ditutup`           | Tiket ditutup secara rasmi                      | Proses tamat                        |
| `Dibuka_Semula`     | Tiket dibuka semula kerana isu berulang         | Agen teruskan penyelesaian          |

---

## Singkatan dan Akronim Tambahan

| Singkatan  | Kepanjangan                          | Konteks                     |
| :--------- | :----------------------------------- | :-------------------------- |
| 2FA        | Two-Factor Authentication            | Keselamatan                 |
| AJAX       | Asynchronous JavaScript and XML      | Teknikal - Frontend         |
| CORS       | Cross-Origin Resource Sharing        | Teknikal - Keselamatan Web  |
| CSRF       | Cross-Site Request Forgery           | Keselamatan - Ancaman Web   |
| CSV        | Comma-Separated Values               | Format Data                 |
| DTO        | Data Transfer Object                 | Teknikal - Seni Bina        |
| HTTP/HTTPS | HyperText Transfer Protocol (Secure) | Protokol Komunikasi         |
| JSON       | JavaScript Object Notation           | Format Data                 |
| LDAPS      | LDAP over SSL                        | Keselamatan - Autentikasi   |
| MTTR       | Mean Time To Repair/Resolve          | Operasi - Metrik            |
| PDF        | Portable Document Format             | Format Dokumen              |
| SQL        | Structured Query Language            | Teknikal - Pangkalan Data   |
| SSH        | Secure Shell                         | Keselamatan - Akses Pelayan |
| SSO        | Single Sign-On                       | Keselamatan - Autentikasi   |
| URL        | Uniform Resource Locator             | Teknikal - Web              |
| XSS        | Cross-Site Scripting                 | Keselamatan - Ancaman Web   |

---

## Rujukan Dokumen Berkaitan (Related Document References)

Glosari ini digunakan merentas semua dokumentasi sistem. Untuk konteks lengkap penggunaan istilah:

### Dokumentasi D00-D18 (Standard Documentation)

| Kod | Tajuk                                                                                    | Penerangan                                        |
| :-- | :--------------------------------------------------------------------------------------- | :------------------------------------------------ |
| D00 | [D00_SYSTEM_OVERVIEW.md](D00_SYSTEM_OVERVIEW.md)                                         | Ringkasan Sistem                                  |
| D01 | [D01_SYSTEM_DEVELOPMENT_PLAN.md](D01_SYSTEM_DEVELOPMENT_PLAN.md)                         | Pelan Pembangunan Sistem                          |
| D02 | [D02_BUSINESS_REQUIREMENTS_SPECIFICATION.md](D02_BUSINESS_REQUIREMENTS_SPECIFICATION.md) | Spesifikasi Keperluan Perniagaan                  |
| D03 | [D03_SOFTWARE_REQUIREMENTS_SPECIFICATION.md](D03_SOFTWARE_REQUIREMENTS_SPECIFICATION.md) | Spesifikasi Keperluan Perisian                    |
| D04 | [D04_SOFTWARE_DESIGN_DOCUMENT.md](D04_SOFTWARE_DESIGN_DOCUMENT.md)                       | Dokumen Rekabentuk Perisian                       |
| D05 | [D05_DATA_MIGRATION_PLAN.md](D05_DATA_MIGRATION_PLAN.md)                                 | Pelan Migrasi Data                                |
| D06 | [D06_DATA_MIGRATION_SPECIFICATION.md](D06_DATA_MIGRATION_SPECIFICATION.md)               | Spesifikasi Migrasi Data                          |
| D07 | [D07_SYSTEM_INTEGRATION_PLAN.md](D07_SYSTEM_INTEGRATION_PLAN.md)                         | Pelan Integrasi Sistem                            |
| D08 | [D08_SYSTEM_INTEGRATION_SPECIFICATION.md](D08_SYSTEM_INTEGRATION_SPECIFICATION.md)       | Spesifikasi Integrasi Sistem                      |
| D09 | [D09_DATABASE_DOCUMENTATION.md](D09_DATABASE_DOCUMENTATION.md)                           | Dokumentasi Pangkalan Data                        |
| D10 | [D10_SOURCE_CODE_DOCUMENTATION.md](D10_SOURCE_CODE_DOCUMENTATION.md)                     | Dokumentasi Kod Sumber                            |
| D11 | [D11_TECHNICAL_DESIGN_DOCUMENTATION.md](D11_TECHNICAL_DESIGN_DOCUMENTATION.md)           | Dokumentasi Rekabentuk Teknikal                   |
| D12 | [D12_UI_UX_DESIGN_GUIDE.md](D12_UI_UX_DESIGN_GUIDE.md)                                   | Panduan Rekabentuk UI/UX                          |
| D13 | [D13_UI_UX_FRONTEND_FRAMEWORK.md](D13_UI_UX_FRONTEND_FRAMEWORK.md)                       | Dokumentasi Rangka Kerja Frontend                 |
| D14 | [D14_UI_UX_STYLE_GUIDE.md](D14_UI_UX_STYLE_GUIDE.md)                                     | Panduan Gaya UI/UX                                |
| D15 | [D15_LANGUAGE_MS_EN.md](D15_LANGUAGE_MS_EN.md)                                           | Panduan Lokalisasi Bahasa (Bahasa Melayu sahaja)  |
| D16 | [D16_BROADCASTING_SETUP.md](D16_BROADCASTING_SETUP.md)                                   | Dokumentasi Setup Broadcasting & WebSocket        |
| D17 | [D17_QUEUE_MANAGEMENT_HORIZON.md](D17_QUEUE_MANAGEMENT_HORIZON.md)                       | Dokumentasi Pengurusan Queue (Redis; Horizon tidak dipasang) |
| D18 | [D18_AI_CHATBOT_OLLAMA_BEDROCK.md](D18_AI_CHATBOT_OLLAMA_BEDROCK.md)                     | Dokumentasi AI Chatbot Cloud Hybrid (Ollama-Bedrock) |

### Dokumentasi Sokongan

| Dokumen                                                              | Penerangan                          |
| :------------------------------------------------------------------- | :---------------------------------- |
| [ICTServe_System_Documentation.md](ICTServe_System_Documentation.md) | Dokumentasi komprehensif sistem     |
| [INDEX.md](INDEX.md)                                                 | Indeks dokumentasi                  |
| [README.md](README.md)                                               | Panduan setup dan penggunaan sistem |

---

## Piawaian Rujukan (Referenced Standards)

Istilah dan konsep dalam glosari ini mematuhi piawaian berikut:

| Piawaian              | Tajuk                                           | Aplikasi                       |
| :-------------------- | :---------------------------------------------- | :----------------------------- |
| ISO/IEC/IEEE 29148    | Systems and software engineering — Requirements | Keperluan sistem & perisian    |
| ISO 8000              | Data quality                                    | Kualiti data                   |
| ISO 9001              | Quality management systems                      | Pengurusan kualiti             |
| ISO/IEC/IEEE 12207    | Software lifecycle processes                    | Proses lifecycle perisian      |
| ISO/IEC/IEEE 15288    | System lifecycle processes                      | Proses lifecycle sistem        |
| IEEE 1016             | Software design descriptions                    | Rekabentuk perisian            |
| ISO 9241-210          | Human-centred design                            | Rekabentuk berpusatkan manusia |
| WCAG 2.2 Level AA     | Web Content Accessibility Guidelines            | Aksesibiliti web               |
| ISO/IEC 27701         | Privacy information management                  | Pengurusan privasi maklumat    |
| MyGOV DSS v2.1.0      | MyGOV Digital Service Standards                 | Standard perkhidmatan digital  |

---

## Sejarah Perubahan (Changelog)

| Versi | Tarikh           | Perubahan                                                                                                                                                                                                                                                                                                                                                  | Penulis           |
| :---- | :--------------- | :--------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------- | :---------------- |
| 3.6.1 | 17 Disember 2025 | Penambahan istilah AI Chatbot, Cloud Hybrid AI Architecture, Ollama, AWS Bedrock, RAG, Model Routing, Streaming Responses, Web-Augmented Responses. Penambahan D18 dalam rujukan dokumen. Kemaskini Bahasa Melayu sahaja (v3.6.0+). Penyelarasan penuh dengan D00-D18 v3.6.1.                                                                               | Pasukan BPM MOTAC |
| 3.6.0 | 14 Disember 2025 | Bahasa Melayu sahaja (penukar bahasa dilumpuhkan). Cloud Hybrid AI Architecture (D18). Penyelarasan dengan D00-D18 v3.6.0.                                                                                                                                                                                                                                  | Pasukan BPM MOTAC |
| 3.5.0 | 1 Disember 2025  | Penambahan istilah Laravel Pulse, Laravel Sanctum, Laravel Socialite, Google Workspace SSO, API Token, Rate Limiting, TOTP, ClamAV, OAuth 2.0, MyGOV DSS, My Dashboard, Volt, SSO. Kemaskini versi teknologi. Penyelarasan penuh dengan D00-D17 v3.5.0.                                                                                                    | Pasukan BPM MOTAC |
| 3.5.0 | 30 November 2025 | True Hybrid Architecture v3.5.0: Self-registration (@motac.gov.my), flexible login (email/username), email verification, optional guest-to-account linking, dual audit system (owen-it + spatie), Laravel Telescope (superuser only), notification preferences. Penyelarasan dengan D00-D14 v3.5.0.                                                        | Pasukan BPM MOTAC |
| 3.3.0 | 29 November 2025 | Klarifikasi User (Admin/Superuser sahaja), tambah definisi Guest (Staff via public form)                                                                                                                                                                                                                                                                   | Pasukan BPM MOTAC |
| 2.2.0 | 29 November 2025 | Kemaskini rujukan D00-D17, tambah istilah Broadcasting/Queue/Horizon, perbaiki format markdownlint                                                                                                                                                                                                                                                         | Pasukan BPM MOTAC |
| 2.1.1 | 31 Oktober 2025  | Penambahbaikan format dan struktur                                                                                                                                                                                                                                                                                                                         | Pasukan BPM MOTAC |
| 2.0.0 | 17 Oktober 2025  | Penyeragaman mengikut D00-D14, tambah rujukan dokumen dan piawaian                                                                                                                                                                                                                                                                                         | Pasukan BPM MOTAC |
| 1.0.0 | September 2025   | Versi awal dengan istilah teknikal asas                                                                                                                                                                                                                                                                                                                    | Pasukan BPM MOTAC |

---

Dokumen ini disediakan mengikut piawaian ISO/IEC/IEEE 29148, ISO 8000, ISO 9001, dan MyGOV Digital Service Standards v2.1.0 dan akan dikemaskini mengikut keperluan projek.
