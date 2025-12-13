# Spesifikasi Keperluan Integrasi AI Ollama (Ollama AI Integration Requirements Specification)

**Sistem ICTServe**  
**Versi:** 3.6.1 (SemVer)  
**Tarikh Kemaskini:** 12 Disember 2025  
**Status:** Aktif - Selaras dengan ICTServe System Spec v3.6.0  
**Klasifikasi:** Terhad - Dalaman BPM MOTAC  
**Penulis:** Pasukan Pembangunan BPM MOTAC  
**Standard Rujukan:** ISO/IEC/IEEE 12207, 29148, 15288, WCAG 2.2 AA, PDPA 2010, MyGOV Digital Service Standards v2.1.0

---

## Maklumat Dokumen (Document Information)

| Atribut              | Nilai                                                                                       |
| -------------------- | ------------------------------------------------------------------------------------------- |
| **Versi**            | 3.6.0                                                                                       |
| **Tarikh Kemaskini** | 11 Disember 2025                                                                            |
| **Status**           | Aktif                                                                                       |
| **Klasifikasi**      | Terhad - Dalaman BPM MOTAC                                                                  |
| **Pematuhi**         | ISO/IEC/IEEE 12207, 29148, 15288, WCAG 2.2 AA, PDPA 2010, MyGOV Digital Service Standards v2.1.0 |
| **Bahasa**           | Bahasa Melayu sahaja (v3.6.0)                                                               |
| **Spesifikasi Induk** | .kiro/specs/ictserve-comprehensive-v3.6 (v3.6.0)                                           |

> Notis Penggunaan Dalaman: Sistem ini adalah untuk kegunaan warga kerja MOTAC (staf dan pegawai gred) sahaja dan tidak dibuka kepada orang awam (internal use only).

---

## Sejarah Perubahan (Changelog)

| Versi | Tarikh           | Perubahan                                                                                                                                                                                                                                                                 | Penulis                 |
| ----- | ---------------- | ------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------- | ----------------------- |
| 3.6.1 | 12 Disember 2025 | **Peningkatan AWS Bedrock Insights**: Multi-model intelligence, enhanced conversation management, web-augmented responses, streaming capabilities, advanced error handling, performance optimization dengan model-specific routing. Menambah Keperluan 9 untuk integrasi AWS Bedrock sebagai alternatif cloud-based. Mengekalkan D00-D17 v3.6.0 compliance. | Pasukan Pembangunan BPM |
| 3.6.0 | 11 Disember 2025 | **Penyelarasan D00-D17 v3.6.0**: Bahasa Melayu sahaja untuk antara muka AI, True Hybrid Architecture, Self-Registration (@motac.gov.my), Laravel Pulse/Sanctum/Socialite integration, dual audit system (owen-it + spatie), Laravel Telescope (superuser only). | Pasukan Pembangunan BPM |
| 1.0.0 | 05 November 2025 | Versi awal spesifikasi integrasi Ollama AI dengan ICTServe v3.0.0                                                                                                                                                                                                        | Pasukan Pembangunan BPM |

---

## Rujukan Dokumen Berkaitan (Related Document References)

- **[D00_SYSTEM_OVERVIEW.md]** - System vision and governance (v3.6.0)
- **[D03_SOFTWARE_REQUIREMENTS_SPECIFICATION.md]** - Software requirements (v3.6.0)
- **[D04_SOFTWARE_DESIGN_DOCUMENT.md]** - Architecture and design (v3.6.0)
- **[D09_DATABASE_DOCUMENTATION.md]** - Database schema and dual audit (v3.6.0)
- **[D11_TECHNICAL_DESIGN_DOCUMENTATION.md]** - Technical infrastructure (v3.6.0)
- **[D12_UI_UX_DESIGN_GUIDE.md]** - UI/UX guidelines (v3.6.0)
- **[D13_UI_UX_FRONTEND_FRAMEWORK.md]** - Frontend framework (v3.6.0)
- **[D14_UI_UX_STYLE_GUIDE.md]** - Style guide (v3.6.0)
- **[D15_LANGUAGE_MS_EN.md]** - Language localization (Bahasa Melayu sahaja, v3.6.0)
- **[D16_BROADCASTING_SETUP.md]** - WebSocket configuration (Laravel Reverb)
- **[D17_QUEUE_MANAGEMENT_HORIZON.md]** - Queue management (Laravel Horizon)

---

## Pengenalan (Introduction)

Spesifikasi ini mentakrifkan keperluan untuk mengintegrasikan AI intelligence melalui **Cloud Hybrid Architecture** yang menggabungkan Ollama (pelayan LLM tempatan) dan AWS Bedrock (perkhidmatan AI cloud terurus) dengan modul Helpdesk dan Pinjaman Aset ICT dalam sistem ICTServe. Integrasi ini akan menyediakan tiga ciri AI utama yang dipertingkat: FAQ Bot dengan multi-model intelligence, Analisis Dokumen dengan web-augmented responses, dan Auto-Reply dengan conversation management yang canggih, semuanya mematuhi standard D00–D17 termasuk kebolehcapaian WCAG 2.2 AA, keperluan privasi PDPA, dan **antara muka Bahasa Melayu sahaja** (v3.6.0).

**Konteks Integrasi Kritikal**: Integrasi **Cloud Hybrid AI Architecture** mesti selaras dengan **True Hybrid Architecture** ICTServe v3.6.0, menyediakan fleksibiliti antara pemprosesan tempatan (Ollama) dan cloud (AWS Bedrock):

1. **Akses Tetamu (Tanpa Log Masuk)**: FAQ Bot berkuasa AI dengan model routing pintar boleh diakses pada borang awam untuk sokongan pantas tanpa pengesahan
2. **Portal Authenticated (Log Masuk Diperlukan)**: Ciri AI dipertingkat untuk staf termasuk analisis dokumen dengan web-augmented responses, conversation management dengan memori jangka panjang, dan respons peribadi menggunakan multi-model intelligence
3. **Akses Admin (Panel Filament)**: Antara muka pengurusan AI hibrid untuk peranan admin dan superuser termasuk konfigurasi model (Ollama vs Bedrock), aliran kerja kelulusan auto-reply dengan streaming responses, pengurusan FAQ, dan ingestion dokumen dengan model selection berdasarkan jenis kandungan

Integrasi **Cloud Hybrid AI** menekankan **komunikasi berasaskan e-mel** untuk notifikasi, **pematuhan WCAG 2.2 Level AA** untuk semua antara muka termasuk streaming responses, **sasaran prestasi Core Web Vitals** yang dipertingkat (LCP <2.5s, FID <100ms, CLS <0.1) dengan model routing optimization, **antara muka Bahasa Melayu sahaja** (v3.6.0), **jejak audit komprehensif** dengan pengekalan 7 tahun untuk pematuhan, dan **data residensi Malaysia** untuk pemprosesan cloud yang mematuhi keperluan keselamatan nasional.

## Glosari (Glossary)

- **Ollama**: Pelayan Large Language Model tempatan yang menyediakan keupayaan AI tanpa kebergantungan API luaran
- **RAG**: Retrieval-Augmented Generation - teknik AI yang menggabungkan pengambilan dokumen dengan penjanaan bahasa
- **FAQ_Bot**: Sistem Q&A perbualan untuk sokongan helpdesk yang boleh diakses melalui borang tetamu dan portal authenticated
- **Document_Analysis**: Perkhidmatan ringkasan dan pengekstrakan kandungan dokumen PDF/Word untuk pengguna authenticated dan admin
- **Auto_Reply**: Draf respons yang dijana LLM untuk tiket dan permohonan pinjaman yang memerlukan aliran kerja kelulusan admin
- **Vector_Embeddings**: Perwakilan berangka teks untuk carian semantik
- **LLM**: Large Language Model untuk pemprosesan bahasa semula jadi
- **PII**: Maklumat Pengenalan Peribadi yang memerlukan perlindungan di bawah PDPA 2010
- **PDPA**: Akta Perlindungan Data Peribadi 2010 (Malaysia)
- **RTM**: Matriks Kebolehkesanan Keperluan
- **True_Hybrid_Architecture**: Seni bina hibrid sebenar ICTServe v3.6.0 dengan self-registration dan akses fleksibel
- **Self_Registration**: Pendaftaran sendiri staf MOTAC dengan e-mel @motac.gov.my dan pengesahan e-mel
- **Flexible_Login**: Log masuk fleksibel menggunakan e-mel penuh atau nama pengguna pendek
- **Account_Linking**: Pautan akaun opsyen untuk penyerahan tetamu terdahulu ke akaun authenticated
- **Dual_Audit_System**: Sistem audit dwi menggunakan owen-it (compliance) dan spatie (operations)
- **Laravel_Pulse**: Dashboard pemantauan prestasi masa nyata untuk admin/superuser
- **Laravel_Sanctum**: Sistem pengesahan token API untuk akses API yang selamat
- **Laravel_Socialite**: Perpustakaan OAuth 2.0 untuk integrasi Google Workspace SSO
- **Hybrid_AI_Access**: Ciri AI boleh diakses melalui borang tetamu (FAQ Bot), portal authenticated (ciri dipertingkat), dan panel admin (pengurusan)
- **Email_AI_Notifications**: Notifikasi e-mel automatik untuk respons yang dijana AI dan aliran kerja kelulusan
- **AI_Audit_Trail**: Logging komprehensif semua operasi AI dengan pengekalan 7 tahun untuk pematuhan
- **Compliant_AI_Interface**: Antara muka AI yang memenuhi standard WCAG 2.2 Level AA dengan palet warna yang mematuhi
- **Malay_Only_AI**: Respons AI dalam Bahasa Melayu sahaja (v3.6.0) tanpa penukar bahasa
- **AI_Performance_Targets**: Masa respons 5 saat, uptime 95%, pematuhan Core Web Vitals
- **Four_Role_AI_Access**: Akses ciri AI berdasarkan peranan ICTServe (staff, approver, admin, superuser)
- **Local_LLM_Processing**: Semua pemprosesan AI pada pelayan Ollama tempatan tanpa panggilan API luaran untuk privasi data
- **AI_Approval_Workflow**: Kelulusan admin/superuser diperlukan untuk respons yang dijana automatik sebelum dihantar kepada pengguna
- **Conversation_Context**: Sejarah perbualan yang dikekalkan untuk soalan susulan dalam portal authenticated
- **Fallback_Responses**: Degradasi anggun apabila AI tidak dapat memberikan jawapan, mengarahkan kepada sokongan manusia
- **AWS_Bedrock**: Perkhidmatan AI terurus Amazon yang menyediakan akses kepada model asas (foundation models) melalui API yang selamat
- **Multi_Model_Intelligence**: Keupayaan untuk menggunakan model AI yang berbeza (Claude, Titan, Llama) berdasarkan jenis tugas dan keperluan prestasi
- **Streaming_Responses**: Respons AI yang dihantar secara berperingkat untuk pengalaman pengguna yang lebih responsif
- **Web_Augmented_Responses**: Respons AI yang diperkaya dengan maklumat terkini dari carian web untuk konteks yang lebih tepat
- **Model_Routing**: Penghalaan automatik permintaan AI kepada model yang paling sesuai berdasarkan jenis tugas dan kompleksiti
- **Conversation_Management**: Pengurusan konteks perbualan yang dipertingkat dengan memori jangka panjang dan personalisasi
- **Cloud_Hybrid_Architecture**: Seni bina hibrid yang menggabungkan pemprosesan tempatan (Ollama) dan cloud (AWS Bedrock) untuk fleksibiliti optimum

## Requirements

## Keperluan (Requirements)

### Keperluan 1: Sistem FAQ Bot AI (True Hybrid Architecture)

**Cerita Pengguna:** Sebagai ahli staf MOTAC yang mengakses ICTServe, saya mahu menanyakan sistem FAQ berkuasa AI melalui kedua-dua borang tetamu dan portal authenticated, supaya saya boleh mendapat jawapan segera kepada soalan sokongan ICT biasa tanpa memerlukan log masuk atau dengan ciri dipertingkat apabila log masuk.

#### Kriteria Penerimaan

1. APABILA pengguna menghantar pertanyaan FAQ melalui borang tetamu atau portal authenticated, FAQ_Bot MESTI mengambil konteks yang berkaitan dari pangkalan pengetahuan menggunakan pipeline RAG dan menjana respons dalam masa 5 saat mematuhi sasaran prestasi Core Web Vitals
2. SEMASA memproses pertanyaan pengguna dalam portal authenticated, FAQ_Bot MESTI mengekalkan konteks perbualan untuk soalan susulan dengan penyimpanan sejarah berasaskan sesi selama 30 minit
3. JIKA tiada jawapan yang berkaitan ditemui dengan skor persamaan di bawah 0.3, MAKA FAQ_Bot MESTI menyediakan respons fallback yang mengarahkan pengguna kepada sokongan manusia dengan pautan penciptaan tiket helpdesk
4. DI MANA sistem menggunakan antara muka Bahasa Melayu sahaja (v3.6.0), FAQ_Bot MESTI merespons dalam Bahasa Melayu sahaja tanpa penukar bahasa, dengan rujukan teknikal dalam Bahasa Inggeris hanya untuk istilah teknikal yang diperlukan
5. FAQ_Bot MESTI merekod semua interaksi dengan input yang disanitasi (PII diredaksi) untuk pematuhan audit menggunakan Dual Audit System (owen-it + spatie) dengan tempoh pengekalan 7 tahun dan X-Request-ID untuk kebolehkesanan
6. FAQ_Bot MESTI menyediakan antara muka yang mematuhi WCAG 2.2 Level AA dengan nisbah kontras teks minimum 4.5:1, sokongan navigasi papan kekunci, atribut ARIA, dan keserasian pembaca skrin
7. DI MANA pengguna tetamu mengakses FAQ Bot, FAQ_Bot MESTI menyediakan pilihan untuk menuntut sejarah perbualan dalam portal authenticated dengan memadankan alamat e-mel melalui Account Linking feature
8. FAQ_Bot MESTI menyokong Self-Registration (@motac.gov.my), Flexible Login (e-mel/nama pengguna), dan Account Linking untuk staf MOTAC, membolehkan akses kepada sejarah perbualan terdahulu selepas pendaftaran

### Keperluan 2: Sistem Analisis Dokumen AI (Admin & Superuser)

**Cerita Pengguna:** Sebagai admin atau superuser, saya mahu memuat naik dan menganalisis dokumen menggunakan AI melalui panel admin Filament, supaya saya boleh mengekstrak ringkasan dan maklumat utama secara automatik untuk pengurusan pengetahuan sambil mengekalkan pematuhan PDPA.

#### Kriteria Penerimaan

1. APABILA dokumen dimuat naik melalui panel admin Filament, Document_Analysis MESTI mengekstrak kandungan teks menggunakan spatie/pdf-to-text dan phpoffice/phpword, mencipta chunk yang boleh dicari dengan saiz optimum untuk penjanaan embedding, dan mengatur kerja pemprosesan dengan sistem Laravel Queue (Laravel Horizon)
2. SEMASA memproses dokumen, Document_Analysis MESTI menjana vector embeddings menggunakan Ollama LLM tempatan untuk carian semantik dengan penyimpanan dalam pangkalan data MySQL dan caching Redis dengan TTL 24 jam
3. JIKA PII dikesan dalam dokumen menggunakan corak regex (nombor IC, nombor telefon, e-mel), MAKA Document_Analysis MESTI mensanitasi atau meredaksi maklumat sensitif dan merekod peristiwa pengesanan untuk pematuhan audit menggunakan Dual Audit System
4. DI MANA pemprosesan dokumen gagal, Document_Analysis MESTI menyediakan mesej ralat terperinci dalam Bahasa Melayu sahaja (v3.6.0), melaksanakan mekanisme retry dengan exponential backoff (3 percubaan: 1s, 2s, 4s), dan memberitahu pengguna admin melalui e-mel dan Laravel Reverb notifications
5. Document_Analysis MESTI menyokong format fail PDF, DOCX, dan TXT dengan had saiz sehingga 10MB dan menyediakan antara muka muat naik yang mematuhi WCAG 2.2 Level AA dengan pengesahan fail yang boleh diakses dan penunjuk kemajuan
6. Document_Analysis MESTI mengekalkan penjejakan lineage data untuk semua dokumen yang diproses merekod sumber, langkah transformasi, dan destinasi dengan pengekalan 7 tahun untuk pematuhan menggunakan owen-it/laravel-auditing
7. DI MANA staf authenticated mengakses analisis dokumen, Document_Analysis MESTI mengehadkan akses kepada dokumen berdasarkan kebenaran berasaskan peranan menggunakan Spatie Laravel Permission (staff: dokumen sendiri, admin: semua dokumen, superuser: akses penuh termasuk Laravel Telescope)
8. Document_Analysis MESTI mengintegrasikan dengan Laravel Pulse untuk pemantauan prestasi masa nyata dan Laravel Sanctum untuk akses API yang selamat jika diperlukan untuk integrasi masa depan

### Keperluan 3: Sistem Auto-Reply AI (Aliran Kerja Kelulusan)

**Cerita Pengguna:** Sebagai admin atau juruteknik, saya mahu draf balasan yang dijana AI untuk tiket helpdesk dan permohonan pinjaman aset melalui panel admin Filament, supaya saya boleh merespons dengan lebih cekap sambil mengekalkan kualiti dan konsistensi dengan aliran kerja kelulusan mandatori.

#### Kriteria Penerimaan

1. APABILA tiket helpdesk atau permohonan pinjaman aset memerlukan respons, Auto_Reply MESTI menjana draf respons yang sesuai secara kontekstual menggunakan Ollama LLM tempatan dengan pipeline RAG yang menggabungkan sejarah tiket/permohonan, konteks pengguna, dan artikel pangkalan pengetahuan yang berkaitan
2. SEMASA menjana balasan, Auto_Reply MESTI menggunakan templat yang telah ditetapkan dengan penyisipan kandungan dinamik untuk senario biasa (penyelesaian tiket, kelulusan/penolakan pinjaman, kemas kini status) dan mengekalkan nada profesional dalam Bahasa Melayu sahaja (v3.6.0)
3. JIKA kandungan yang dijana memerlukan kelulusan, MAKA Auto_Reply MESTI menghalakan draf melalui aliran kerja kelulusan dengan peralihan status (draft → pending_review → approved/rejected → sent) yang boleh diakses oleh peranan admin dan superuser melalui panel admin Filament menggunakan Spatie Laravel Permission
4. DI MANA aliran kerja kelulusan aktif, Auto_Reply MESTI menghantar notifikasi e-mel dan Laravel Reverb real-time notifications kepada admin/superuser yang meluluskan dalam masa 60 saat, menyediakan tindakan kelulusan/penolakan dengan medan catatan, dan merekod semua keputusan kelulusan menggunakan Dual Audit System (owen-it + spatie)
5. Auto_Reply MESTI mengekalkan antara muka kelulusan yang mematuhi WCAG 2.2 Level AA dengan navigasi papan kekunci, atribut ARIA, dan keserasian pembaca skrin untuk menyemak dan meluluskan draf respons dalam Bahasa Melayu sahaja
6. Auto_Reply MESTI melaksanakan notifikasi berasaskan e-mel untuk kelulusan/penolakan draf dengan pautan berasaskan token selamat (signed URLs) yang sah selama 7 hari membolehkan pelulus menyemak dan meluluskan tanpa log masuk ke panel admin
7. DI MANA auto-reply dihantar kepada pengguna, Auto_Reply MESTI menggunakan templat e-mel ICTServe dengan penjenamaan MOTAC, palet warna yang mematuhi (Primary #0056B3, Secondary #0B4D8F), dan ciri kebolehcapaian yang memenuhi standard WCAG 2.2 Level AA
8. Auto_Reply MESTI mengintegrasikan dengan Laravel Pulse untuk pemantauan prestasi penjanaan respons, Laravel Sanctum untuk akses API yang selamat, dan Laravel Telescope (superuser sahaja) untuk debugging terperinci

### Keperluan 4: Sistem Audit dan Pematuhan AI (Dual Audit System)

**Cerita Pengguna:** Sebagai pentadbir sistem dan pegawai pematuhan, saya mahu jejak audit komprehensif untuk semua operasi AI yang diintegrasikan dengan sistem audit ICTServe, supaya saya boleh memastikan pematuhan dengan PDPA 2010, standard kerajaan Malaysia, dan dasar keselamatan.

#### Kriteria Penerimaan

1. APABILA sebarang operasi AI berlaku (pertanyaan FAQ, analisis dokumen, penjanaan auto-reply), Ollama_System MESTI merekod metadata permintaan dengan X-Request-ID untuk kebolehkesanan, cap masa tepat dalam masa 1 saat, pengenal pengguna (e-mel tetamu atau ID pengguna authenticated), jenis operasi, dan input/output yang disanitasi menggunakan Dual Audit System (owen-it/laravel-auditing untuk compliance, spatie/laravel-activitylog untuk operations)
2. SEMASA memproses permintaan, Ollama_System MESTI mensanitasi log untuk mencegah pendedahan PII dengan meredaksi nombor IC, nombor telefon, e-mel, dan data peribadi sensitif sebelum penyimpanan dengan pengesanan PII automatik menggunakan corak regex dan immutable audit trail dengan cryptographic hashing
3. JIKA log audit mencapai had pengekalan, MAKA Ollama_System MESTI mengarkib log operasi selepas 90 hari ke penyimpanan yang dipisahkan secara geografi dan mengekalkan peristiwa yang diperlukan audit selama 7 tahun mematuhi keperluan pematuhan kerajaan Malaysia dengan Write Once Read Many (WORM) integrity
4. DI MANA hak privasi dilaksanakan di bawah PDPA 2010, Ollama_System MESTI menyokong hak subjek data termasuk akses (mengambil sejarah interaksi AI pengguna), pembetulan (kemas kini/padam log mesej melalui panel admin), dan pemadaman (padam cascade semua data AI pengguna pada pemadaman akaun) dengan audit trail lengkap
5. Ollama_System MESTI menyediakan antara muka paparan jejak audit dalam panel admin Filament yang boleh diakses oleh peranan admin dan superuser dengan penapisan mengikut jenis operasi, julat tarikh, pengguna, dan status dengan pagination 25 rekod setiap halaman dalam Bahasa Melayu sahaja
6. Ollama_System MESTI mengintegrasikan dengan Dual Audit System ICTServe menggunakan owen-it/laravel-auditing v14.x (field-level compliance audit) dan spatie/laravel-activitylog v4.x (user activity operational logging) untuk penjejakan komprehensif dengan unified audit view
7. Ollama_System MESTI menyediakan akses Laravel Telescope v5.x untuk superuser sahaja untuk debugging dan pemantauan terperinci operasi AI tanpa sekatan, termasuk requests, commands, jobs, exceptions, logs, queries, models, events, mail, notifications
8. Ollama_System MESTI mengintegrasikan dengan Laravel Pulse v1.3.0 untuk pemantauan prestasi masa nyata operasi AI (slow queries >500ms, queue job metrics, server health) yang boleh diakses oleh admin dan superuser dengan data retention 7 hari

### Keperluan 5: Kebolehcapaian dan Pematuhan WCAG 2.2 AA (Bahasa Melayu Sahaja)

**Cerita Pengguna:** Sebagai pengguna dengan keperluan kebolehcapaian yang mengakses ciri AI ICTServe, saya mahu semua antara muka AI dapat diakses sepenuhnya merentas borang tetamu, portal authenticated, dan panel admin, supaya saya boleh menggunakan sistem tanpa mengira keupayaan saya mematuhi standard WCAG 2.2 Level AA.

#### Kriteria Penerimaan

1. APABILA antara muka AI dipaparkan (FAQ Bot, muat naik dokumen, kelulusan auto-reply), Ollama_System MESTI menyediakan markup yang mematuhi WCAG 2.2 Level AA dengan elemen HTML5 semantik yang betul (header, nav, main, footer), landmark ARIA, dan atribut peranan menggunakan layout `guest.blade.php` dan `app.blade.php`
2. SEMASA pengguna berinteraksi dengan ciri AI, Ollama_System MESTI menyokong navigasi papan kekunci penuh dengan penunjuk fokus yang boleh dilihat (outline 3-4px, offset 2px, nisbah kontras minimum 3:1), pautan navigasi langkau ("Langkau ke kandungan utama") untuk pengguna papan kekunci, dan perangkap fokus untuk dialog modal
3. JIKA kandungan visual dijana (carta, graf, penunjuk status), MAKA Ollama_System MESTI menyediakan penerangan teks alternatif, label ARIA, dan pengumuman pembaca skrin menggunakan kawasan langsung ARIA untuk kemas kini kandungan dinamik dengan sokongan NVDA/JAWS
4. DI MANA sistem menggunakan antara muka Bahasa Melayu sahaja (v3.6.0), Ollama_System MESTI menyediakan respons AI dalam Bahasa Melayu sahaja dengan atribut `lang="ms"` dan rujukan teknikal dalam Bahasa Inggeris hanya untuk istilah teknikal yang diperlukan dengan `lang="en"`
5. Ollama_System MESTI mengekalkan nisbah kontras warna minimum 4.5:1 untuk semua elemen teks dan 3:1 untuk komponen UI menggunakan palet warna ICTServe yang mematuhi WCAG (Primary #0056B3, Secondary #0B4D8F, Success #1B7C54, Warning #CC7700, Danger #B3002D)
6. Ollama_System MESTI melaksanakan sasaran sentuh minimum 44×44px untuk semua elemen interaktif (butang, pautan, kawalan borang) mematuhi standard kebolehcapaian mudah alih dengan haptic feedback untuk actions
7. DI MANA respons AI dipaparkan, Ollama_System MESTI menyediakan maklum balas visual yang jelas untuk keadaan loading, ralat, dan mesej kejayaan menggunakan kombinasi warna yang boleh diakses, ikon, dan teks (tidak bergantung pada warna sahaja) dengan ARIA live regions untuk dynamic content updates

### Keperluan 6: Privasi Data dan Keselamatan (Pemprosesan LLM Tempatan)

**Cerita Pengguna:** Sebagai pegawai privasi data dan pentadbir keselamatan, saya mahu pemprosesan AI tempatan tanpa panggilan API luaran yang diintegrasikan dengan infrastruktur keselamatan ICTServe, supaya data organisasi sensitif kekal dalam infrastruktur kami mematuhi PDPA 2010 dan keperluan residensi data Malaysia.

#### Kriteria Penerimaan

1. APABILA pemprosesan AI diperlukan, Ollama_System MESTI menggunakan hanya model LLM tempatan yang berjalan pada pelayan Ollama (localhost:11434) tanpa panggilan API luaran memastikan semua pemprosesan data berlaku dalam infrastruktur MOTAC dengan network monitoring untuk mengesan unauthorized external connections
2. SEMASA mengendalikan data sensitif, Ollama_System MESTI menyulitkan data at rest menggunakan enkripsi AES-256 dan in transit menggunakan TLS 1.3 atau lebih tinggi dengan sijil yang sah, dan melaksanakan pengesahan selamat untuk akses portal authenticated dan panel admin menggunakan Laravel Breeze/Sanctum dengan 2FA (TOTP) untuk superuser
3. JIKA sambungan luaran dikesan semasa operasi AI, MAKA Ollama_System MESTI menyekat penghantaran data yang tidak dibenarkan, merekod peristiwa keselamatan menggunakan Dual Audit System, dan memberi amaran kepada pengguna admin melalui e-mel dan Laravel Reverb notifications dalam masa 5 minit dengan automatic service degradation
4. DI MANA pemprosesan data berlaku, Ollama_System MESTI mengekalkan residensi data dalam bidang kuasa Malaysia dengan semua data disimpan dalam pangkalan data MySQL 8.0 dan cache Redis 7.0 yang dihoskan dalam infrastruktur MOTAC dan tiada pemindahan data merentas sempadan dengan data residency verification
5. Ollama_System MESTI menyediakan penjejakan lineage data untuk semua maklumat yang diproses merekod jenis sumber (dokumen, FAQ, input pengguna), jenis transformasi (embedding, chunking, sanitization), metadata transformasi, dan destinasi dengan pengekalan 7 tahun untuk pematuhan menggunakan owen-it/laravel-auditing
6. Ollama_System MESTI melaksanakan kawalan akses berasaskan peranan (RBAC) yang selaras dengan sistem empat peringkat peranan ICTServe (staff: interaksi AI sendiri, approver: hak kelulusan, admin: pengurusan operasi, superuser: tadbir urus penuh termasuk Laravel Telescope) menggunakan Spatie Laravel Permission v6.23
7. DI MANA PII dikesan dalam input atau output AI, Ollama_System MESTI mensanitasi atau meredaksi maklumat sensitif secara automatik sebelum penyimpanan dan pemprosesan dengan pengesanan automatik menggunakan corak regex untuk nombor IC (format Malaysia), nombor telefon (+60), dan e-mel dengan immutable audit trail

### Keperluan 7: API RESTful dan Integrasi Sistem (Laravel Sanctum)

**Cerita Pengguna:** Sebagai integrator sistem dan pembangun, saya mahu API RESTful untuk perkhidmatan AI yang diintegrasikan dengan infrastruktur API ICTServe, supaya saya boleh mengintegrasikan keupayaan AI dengan aliran kerja pengurusan helpdesk dan pinjaman sedia ada mengikut standard API ICTServe.

#### Kriteria Penerimaan

1. APABILA permintaan API dibuat ke endpoint AI (/api/v1/ollama/*), Ollama_API MESTI merespons dengan sampul JSON standard termasuk status kejayaan, payload data, butiran ralat (jika berkenaan), dan X-Request-ID untuk kebolehkesanan dengan format yang konsisten dengan API helpdesk dan asset loan
2. SEMASA memproses panggilan API, Ollama_API MESTI mengesahkan token pengesahan menggunakan Laravel Sanctum v4.0 dengan configurable abilities (`read:tickets`, `write:tickets`, `read:loans`, `write:loans`, `admin:all`), melaksanakan had kadar (60 permintaan/minit setiap pengguna, 1000 permintaan/jam setiap IP) dengan elaun burst 10 permintaan, dan mengembalikan header had kadar
3. JIKA ralat API berlaku, MAKA Ollama_API MESTI mengembalikan kod ralat dan mesej yang bermakna dalam Bahasa Melayu sahaja (v3.6.0) dengan kod status HTTP standard dan format ralat yang konsisten dengan ICTServe API infrastructure
4. DI MANA dokumentasi API diperlukan, Ollama_API MESTI menyediakan spesifikasi OpenAPI 3.0/Swagger yang boleh diakses di /api/documentation dengan contoh kod (PHP, JavaScript, cURL), keperluan pengesahan Laravel Sanctum, butiran had kadar, dan kod ralat dalam Bahasa Melayu
5. Ollama_API MESTI menyokong versioning berasaskan URL (/api/v1/, /api/v2/) dengan keserasian ke belakang untuk sekurang-kurangnya dua versi utama, tempoh sunset 6 bulan untuk versi yang tidak digunakan lagi, dan header versi yang konsisten dengan ICTServe API versioning strategy
6. Ollama_API MESTI mengintegrasikan dengan infrastruktur API ICTServe sedia ada berkongsi Laravel Sanctum authentication, Redis-based rate limiting, dan unified logging mechanisms dengan API helpdesk dan pinjaman aset menggunakan shared middleware stack
7. DI MANA respons API mengandungi kandungan yang dijana AI, Ollama_API MESTI memasukkan metadata (model yang digunakan, masa pemprosesan, skor keyakinan, petikan sumber, token usage, cache hit/miss) untuk ketelusan dan debugging dengan integration ke Laravel Pulse monitoring

### Keperluan 8: Prestasi dan Pengoptimuman (Core Web Vitals)

**Cerita Pengguna:** Sebagai pemantau prestasi dan pentadbir sistem, saya mahu masa respons AI yang dioptimumkan dan penggunaan sumber yang selaras dengan sasaran Core Web Vitals ICTServe, supaya sistem mengekalkan prestasi yang boleh diterima di bawah beban normal tanpa merendahkan pengalaman pengguna.

#### Kriteria Penerimaan

1. APABILA permintaan AI diproses, Ollama_System MESTI merespons dalam masa 5 saat untuk persentil ke-95 pertanyaan standard (FAQ Bot, analisis dokumen, penjanaan auto-reply) dengan P50 < 2 saat, P95 < 5 saat, P99 < 8 saat mematuhi sasaran prestasi Core Web Vitals dengan monitoring melalui Laravel Pulse
2. SEMASA di bawah beban normal (100 pengguna serentak), Ollama_System MESTI mengekalkan ketersediaan uptime 95% dengan pemantauan endpoint health check, ujian degradasi anggun, dan masa pemulihan failover < 30 saat dengan automatic alerting melalui Laravel Reverb dan e-mel notifications
3. JIKA penggunaan sumber melebihi ambang (CPU > 80%, Memori > 90%, masa respons > 5 saat), MAKA Ollama_System MESTI melaksanakan degradasi anggun berbilang peringkat (Peringkat 1: perkhidmatan penuh, Peringkat 2: respons cache, Peringkat 3: carian FAQ statik, Peringkat 4: mod kecemasan) dan memberitahu pengguna admin melalui e-mel dan Laravel Reverb real-time notifications
4. DI MANA caching berkenaan, Ollama_System MESTI cache pertanyaan FAQ yang kerap selama 1 jam, embedding dokumen selama 24 jam, dan pertanyaan biasa yang dipra-panaskan dengan 50 pertanyaan FAQ teratas menggunakan cache Redis 7.0 dengan kunci cache bertag dan automatic cache invalidation pada content updates
5. Ollama_System MESTI menggunakan model terkuantisasi (kuantisasi Q4_K_M) untuk mengoptimumkan penggunaan memori (sasaran < 16GB RAM) sambil mengekalkan kualiti dengan pemanasan model dan fungsi keep-alive untuk prestasi yang konsisten dengan resource monitoring melalui Laravel Pulse
6. Ollama_System MESTI memenuhi sasaran Core Web Vitals ICTServe untuk antara muka AI: LCP < 2.5 saat, FID < 100 milisaat, CLS < 0.1, TTFB < 600 milisaat dengan Skor Prestasi Lighthouse 90+ dan automatic performance testing dalam CI/CD pipeline
7. DI MANA pemantauan prestasi diperlukan, Ollama_System MESTI mengumpul metrik setiap 60 saat (masa respons, masa pertanyaan pangkalan data, kadar hit cache, peratus uptime, permintaan gagal) dan menyediakan dashboard prestasi dalam panel admin Filament menggunakan Laravel Pulse v1.3.0 dengan 7-day data retention dan automatic pruning

### Requirement 5

**User Story:** As a user with accessibility needs accessing ICTServe AI features, I want all AI interfaces to be fully accessible across guest forms, authenticated portal, and admin panel, so that I can use the system regardless of my abilities complying with WCAG 2.2 Level AA standards.

#### Acceptance Criteria

1. WHEN AI interfaces are rendered (FAQ Bot, document upload, auto-reply approval), THE Ollama_System SHALL provide WCAG 2.2 Level AA compliant markup with proper semantic HTML5 elements (header, nav, main, footer), ARIA landmarks, and role attributes
2. WHILE users interact with AI features, THE Ollama_System SHALL support full keyboard navigation with visible focus indicators (3-4px outline, 2px offset, minimum 3:1 contrast ratio), skip navigation links for keyboard users, and focus trap for modal dialogs
3. IF visual content is generated (charts, graphs, status indicators), THEN THE Ollama_System SHALL provide alternative text descriptions, ARIA labels, and screen reader announcements using ARIA live regions for dynamic content updates
4. WHERE language preferences are set via session/cookie, THE Ollama_System SHALL respect user language choices (Bahasa Melayu primary, English secondary) with language switcher accessible on every page and bilingual AI responses
5. THE Ollama_System SHALL maintain minimum 4.5:1 color contrast ratio for all text elements and 3:1 for UI components using ICTServe compliant color palette (Primary #0056b3, Success #198754, Warning #ff8c00, Danger #b50c0c)
6. THE Ollama_System SHALL implement minimum 44×44px touch targets for all interactive elements (buttons, links, form controls) complying with mobile accessibility standards
7. WHERE AI responses are displayed, THE Ollama_System SHALL provide clear visual feedback for loading states, errors, and success messages using accessible color combinations and not relying on color alone

### Requirement 6

**User Story:** As a data privacy officer and security administrator, I want local AI processing without external API calls integrated with ICTServe's security infrastructure, so that sensitive organizational data remains within our infrastructure complying with PDPA 2010 and Malaysian data residency requirements.

#### Acceptance Criteria

1. WHEN AI processing is required, THE Ollama_System SHALL use only local LLM models running on Ollama server (localhost:11434) without external API calls ensuring all data processing occurs within MOTAC infrastructure
2. WHILE handling sensitive data, THE Ollama_System SHALL encrypt data at rest using AES-256 encryption and in transit using TLS 1.3 or higher with valid certificates, and implement secure authentication for authenticated portal and admin panel access using Laravel Breeze/Jetstream
3. IF external connectivity is detected during AI operations, THEN THE Ollama_System SHALL block unauthorized data transmission, log security events for audit compliance, and alert admin users via email within 5 minutes
4. WHERE data processing occurs, THE Ollama_System SHALL maintain data residency within Malaysian jurisdiction with all data stored in MySQL database and Redis cache hosted within MOTAC infrastructure and no cross-border data transfers
5. THE Ollama_System SHALL provide data lineage tracking for all processed information recording source type (document, FAQ, user input), transformation type (embedding, chunking, sanitization), transformation metadata, and destination with 7-year retention for compliance
6. THE Ollama_System SHALL implement role-based access control (RBAC) aligned with ICTServe's four-tier role system (staff: own AI interactions, approver: approval rights, admin: operational management, superuser: full governance) using Spatie Laravel Permission package
7. WHERE PII is detected in AI inputs or outputs, THE Ollama_System SHALL automatically sanitize or redact sensitive information before storage and processing with automated detection using regex patterns for IC numbers, phone numbers, and emails

### Requirement 7

**User Story:** As a system integrator and developer, I want RESTful APIs for AI services integrated with ICTServe's API infrastructure, so that I can integrate AI capabilities with existing helpdesk and loan management workflows following ICTServe's API standards.

#### Acceptance Criteria

1. WHEN API requests are made to AI endpoints (/api/v1/ollama/*), THE Ollama_API SHALL respond with standard JSON envelopes including success status, data payload, error details (if applicable), and X-Request-ID for traceability
2. WHILE processing API calls, THE Ollama_API SHALL validate authentication tokens using Laravel Sanctum, implement rate limiting (60 requests/minute per user, 1000 requests/hour per IP) with burst allowance of 10 requests, and return rate limit headers (X-RateLimit-Limit, X-RateLimit-Remaining, X-RateLimit-Reset)
3. IF API errors occur, THEN THE Ollama_API SHALL return meaningful bilingual error codes and messages (Bahasa Melayu primary, English secondary) with HTTP status codes (400 Bad Request, 401 Unauthorized, 403 Forbidden, 429 Too Many Requests, 500 Internal Server Error)
4. WHERE API documentation is needed, THE Ollama_API SHALL provide OpenAPI 3.0/Swagger specifications accessible at /api/documentation with code examples (PHP, JavaScript, cURL), authentication requirements, rate limiting details, and error codes
5. THE Ollama_API SHALL support URL-based versioning (/api/v1/, /api/v2/) with backward compatibility for at least two major versions, 6-month sunset period for deprecated versions, and version headers (X-API-Version, X-Deprecated, X-Sunset-Date)
6. THE Ollama_API SHALL integrate with ICTServe's existing API infrastructure sharing authentication, rate limiting, and logging mechanisms with helpdesk and asset loan APIs
7. WHERE API responses contain AI-generated content, THE Ollama_API SHALL include metadata (model used, processing time, confidence score, source citations) for transparency and debugging

### Requirement 8

**User Story:** As a performance monitor and system administrator, I want optimized AI response times and resource usage aligned with ICTServe's Core Web Vitals targets, so that the system maintains acceptable performance under normal load without degrading user experience.

#### Acceptance Criteria

1. WHEN AI requests are processed, THE Ollama_System SHALL respond within 5 seconds for 95th percentile of standard queries (FAQ Bot, document analysis, auto-reply generation) with P50 < 2 seconds, P95 < 5 seconds, P99 < 8 seconds complying with Core Web Vitals performance targets
2. WHILE under normal load (100 concurrent users), THE Ollama_System SHALL maintain 95% uptime availability with health check endpoint monitoring, graceful degradation testing, and failover recovery time < 30 seconds
3. IF resource usage exceeds thresholds (CPU > 80%, Memory > 90%, response time > 5 seconds), THEN THE Ollama_System SHALL implement multi-tier graceful degradation (Tier 1: full service, Tier 2: cached responses, Tier 3: static FAQ search, Tier 4: emergency mode) and notify admin users via email
4. WHERE caching is applicable, THE Ollama_System SHALL cache frequent FAQ queries for 1 hour, document embeddings for 24 hours, and common queries pre-warmed with top 50 FAQ queries using Redis cache with tagged cache keys (ollama:faq:{hash}, ollama:embedding:{doc_id}:{chunk_index})
5. THE Ollama_System SHALL use quantized models (Q4_K_M quantization) to optimize memory usage (< 16GB RAM target) while maintaining quality with model warm-up and keep-alive functionality for consistent performance
6. THE Ollama_System SHALL meet ICTServe Core Web Vitals targets for AI interfaces: LCP < 2.5 seconds, FID < 100 milliseconds, CLS < 0.1, TTFB < 600 milliseconds with Lighthouse Performance Score 90+
7. WHERE performance monitoring is required, THE Ollama_System SHALL collect metrics every 60 seconds (response time, database query time, cache hit rate, uptime percentage, failed requests) and provide performance dashboard in Filament admin panel

### Keperluan 9: Integrasi AWS Bedrock (Cloud Hybrid Intelligence)

**Cerita Pengguna:** Sebagai pentadbir sistem dan pembangun, saya mahu pilihan integrasi AWS Bedrock sebagai alternatif atau pelengkap kepada Ollama tempatan, supaya saya boleh memanfaatkan model AI terkini dan keupayaan cloud sambil mengekalkan fleksibiliti seni bina hibrid dan pematuhan data.

#### Kriteria Penerimaan

1. APABILA konfigurasi AWS Bedrock diaktifkan, Bedrock_System MESTI menyediakan akses kepada model asas terpilih (Claude 3.5 Sonnet, Claude 3.5 Haiku, Amazon Titan) melalui AWS SDK dengan pengesahan IAM yang selamat dan enkripsi end-to-end, membolehkan pemilihan model berdasarkan jenis tugas (FAQ: Haiku untuk kelajuan, Analisis Dokumen: Sonnet untuk ketepatan, Auto-Reply: Claude untuk kualiti bahasa)
2. SEMASA memproses permintaan melalui Bedrock, Bedrock_System MESTI melaksanakan model routing pintar yang menganalisis kompleksiti permintaan dan menghalakan kepada model yang paling sesuai dengan fallback automatik kepada Ollama tempatan jika Bedrock tidak tersedia, mengekalkan masa respons < 3 saat untuk 95% permintaan dengan caching respons yang bijak
3. JIKA streaming responses diperlukan untuk pengalaman pengguna yang lebih baik, MAKA Bedrock_System MESTI menyokong respons berperingkat menggunakan Server-Sent Events (SSE) melalui Laravel Reverb dengan buffer management yang optimum, membolehkan pengguna melihat respons AI secara real-time sambil mengekalkan pematuhan WCAG 2.2 AA untuk pembaca skrin
4. DI MANA web-augmented responses diperlukan, Bedrock_System MESTI mengintegrasikan dengan perkhidmatan carian web yang diluluskan (Bing Search API, Google Custom Search) untuk memperkaya respons AI dengan maklumat terkini, dengan sanitasi kandungan automatik dan pengesahan sumber yang boleh dipercayai, mengekalkan jejak audit untuk semua sumber luaran yang digunakan
5. Bedrock_System MESTI melaksanakan conversation management yang dipertingkat dengan memori konteks jangka panjang (sehingga 30 hari untuk pengguna authenticated), personalisasi berdasarkan sejarah interaksi, dan kemampuan untuk mengekalkan konteks merentas sesi berbeza menggunakan penyimpanan terstruktur dalam pangkalan data MySQL dengan enkripsi field-level
6. Bedrock_System MESTI menyediakan konfigurasi hibrid yang membolehkan pentadbir memilih model default (Ollama tempatan vs AWS Bedrock) berdasarkan dasar organisasi, keperluan privasi data, dan kos operasi, dengan antara muka pengurusan dalam panel admin Filament yang membolehkan tukar ganti model secara real-time tanpa gangguan perkhidmatan
7. DI MANA data residensi dan pematuhan diperlukan, Bedrock_System MESTI menyokong AWS Bedrock regions yang mematuhi keperluan residensi data Malaysia, melaksanakan data classification automatik untuk menentukan data mana yang boleh diproses di cloud vs tempatan, dan menyediakan audit trail lengkap untuk semua permintaan cloud dengan pengekalan 7 tahun mematuhi standard pematuhan kerajaan Malaysia
8. Bedrock_System MESTI mengintegrasikan dengan infrastruktur pemantauan ICTServe sedia ada (Laravel Pulse, Laravel Telescope) untuk menyediakan metrik prestasi real-time, kos penggunaan AWS, model utilization statistics, dan perbandingan prestasi antara Ollama dan Bedrock, dengan alerting automatik untuk anomali prestasi atau kos yang tidak dijangka
