# ICTServe v3.6.1 - Rajah Swimlane

**Sistem**: ICTServe (Dalaman Sahaja) - Pengurusan Helpdesk & Pinjaman Aset  
**Versi**: 3.6.1 (Disember 2025)  
**Skop**: Aliran proses menyeluruh dari permohonan hingga penyelesaian/audit  
**Format**: Rajah Swimlane Mermaid

---

## Rajah 1: Gambaran Keseluruhan Sistem (Tahap Tinggi)

```mermaid
graph TD
    subgraph Guest["Pengguna Tetamu Tanpa Log Masuk"]
        GuestForm["Akses Borang Awam<br/>helpdesk.local/submit<br/>Tiada Log Masuk Diperlukan"]
    end
    
    subgraph Staff["Staf Diautentikasi"]
        StaffDash["Papan Pemuka Log Masuk<br/>Borang Auto-Isi<br/>Paparan Sejarah"]
    end
    
    subgraph Supervisor["Penyelia/Pelulus"]
        EmailApproval["Keputusan Melalui Pautan E-mel<br/>Tiada Log Masuk Diperlukan<br/>Gred 41+"]
    end
    
    subgraph Admin["Portal Pentadbir"]
        AdminPanel["Panel Filament<br/>Saringan Triage & Proses<br/>Dilindungi RBAC"]
    end
    
    subgraph AI["Sistem AI"]
        AIBot["Bot Soalan Lazim FAQ<br/>Penghalaan Model<br/>Ollama + Bedrock"]
    end
    
    subgraph Services["Perkhidmatan Backend"]
        Queue["Sistem Baris Gilir<br/>Laravel Horizon"]
        WebSocket["Masa Nyata<br/>Laravel Reverb"]
        Audit["Audit Dwi<br/>owen-it + spatie"]
    end
    
    subgraph DB["Lapisan Data"]
        Database["MySQL 8.0<br/>Disulitkan<br/>AES-256"]
    end
    
    GuestForm --> |Tiket Helpdesk| Queue
    StaffDash --> |Permohonan Pinjaman| Queue
    EmailApproval --> |Keputusan Kelulusan| Queue
    AdminPanel --> |Proses & Kemas Kini| Queue
    AIBot --> |Respons Pertanyaan| Queue
    Queue --> |Kerja Async| WebSocket
    Queue --> |E-mel/SMS| Services
    AdminPanel --> |Kemas Kini| WebSocket
    Queue --> |Rekod Log| Audit
    Audit --> |Simpan Rekod| Database
    AdminPanel --> |Baca| Database
    StaffDash --> |Pertanyaan| Database
```

---

## Rajah 2: Penyerahan Tiket Helpdesk oleh Tetamu

```mermaid
sequenceDiagram
    actor Guest as Pengguna Tetamu
    participant Form as Borang Livewire<br/>Pengesahan
    participant API as API Backend<br/>Perkhidmatan Laravel
    participant DB as Pangkalan Data<br/>MySQL
    participant Queue as Baris Gilir<br/>Laravel Horizon
    participant Email as Perkhidmatan E-mel<br/>SMTP
    participant WebSocket as WebSocket<br/>Siaran Broadcast
    participant Admin as Panel Pentadbir<br/>Masa Nyata
    participant Audit as Pelog Audit

    Guest->>Form: 1. Layari borang helpdesk
    Form->>Form: 2. Dipaparkan dalam Bahasa Melayu
    Guest->>Form: 3. Isi: nama, e-mel, kategori, keterangan
    Form->>Form: 4. Pengesahan masa nyata Livewire Alpine.js
    Form->>Form: 5. Sahkan: medan wajib, format e-mel
    alt Pengesahan Gagal
        Form->>Guest: Papar mesej ralat
        Guest->>Form: Betulkan & hantar semula
    else Pengesahan Lulus
        Form->>API: 6. Hantar data borang disulitkan
        API->>API: 7. Pengesahan & sanitasi backend
        API->>API: 8. Jana ticket_number cth: TKT-2025-001234
        API->>API: 9. Tetapkan status = "OPEN"
        API->>API: 10. Tetapkan user_id = NULL tetamu
        API->>DB: 11. Simpan HelpdeskTicket<br/>nama, e-mel, kategori, keterangan
        DB->>Audit: 12. Rekod peristiwa penciptaan
        API->>Queue: 13. Masuk baris gilir kerja<br/>SendHelpdeskConfirmation
        API->>Audit: 14. Log: "Tetamu mencipta tiket #TKT-2025-001234"
        API->>Guest: 15. Pulangkan halaman pengesahan<br/>Token status untuk tetamu semak kemas kini
        Queue->>Email: 16. Proses kerja e-mel async
        Email->>Guest: 17. Hantar e-mel pengesahan<br/>"Tiket anda #TKT-2025-001234 telah dicipta"
        API->>WebSocket: 18. Siarkan broadcast event "newTicket"
        WebSocket->>Admin: 19. Notifikasi masa nyata ke papan pemuka pentadbir<br/>Notifikasi toast + ikon loceng
        Admin->>Admin: 20. Pentadbir menerima notifikasi Reverb 1.6.3<br/>Papan pemuka auto-muat semula
    end
```

---

## Rajah 3: Staf Diautentikasi dengan Borang Auto-Isi

```mermaid
sequenceDiagram
    actor Staff as Staf Diautentikasi
    participant Auth as Laravel Breeze<br/>Pengawal Auth Guard
    participant Dashboard as Papan Pemuka<br/>Livewire
    participant Form as Komponen Borang<br/>Auto-Isi
    participant UserSvc as Perkhidmatan Pengguna<br/>Carian Profil
    participant API as API Backend
    participant DB as Pangkalan Data
    participant Queue as Baris Gilir
    participant Audit as Pelog Audit

    Staff->>Auth: 1. Log masuk menggunakan e-mel/kata laluan
    Auth->>Auth: 2. Sahkan kelayakan hash Bcrypt
    Auth->>Dashboard: 3. Cipta sesi & token auth
    Dashboard->>Staff: 4. Alih hala ke papan pemuka<br/>Papar ucapan & menu
    Staff->>Dashboard: 5. Klik "Hantar Tiket Helpdesk"
    Dashboard->>Form: 6. Semak Auth::check() = TRUE
    Form->>UserSvc: 7. Minta data profil pengguna
    UserSvc->>DB: 8. Pertanyaan rekod pengguna
    DB->>UserSvc: 9. Pulangkan: nama, e-mel, telefon, bahagian, gred
    UserSvc->>Form: 10. Pulangkan data profil
    Form->>Form: 11. Auto-isi medan borang:<br/>- Nama: user.name<br/>- E-mel: user.email<br/>- Telefon: user.phone<br/>- Bahagian: user.department
    Form->>Staff: 12. Papar borang dengan nilai auto-isi
    Staff->>Form: 13. Semak data auto-isi boleh ubah
    Staff->>Form: 14. Isi baki: kategori, keterangan, keutamaan
    Form->>Form: 15. Pengesahan masa nyata Livewire 3.7.3
    alt Pengesahan Lulus
        Form->>API: 16. Hantar dengan user_id = authenticated_user_id
        API->>API: 17. Pengesahan & pengayaan enrichment backend
        API->>API: 18. Jana ticket_number
        API->>API: 19. Tetapkan status = "OPEN"
        API->>DB: 20. Simpan HelpdeskTicket<br/>sertakan user_id untuk pengguna diautentikasi
        DB->>Audit: 21. Rekod perubahan medan
        API->>Queue: 22. Masuk baris gilir notifikasi<br/>- E-mel jika berkenaan<br/>- WebSocket ke pentadbir
        API->>Audit: 23. Log: "Pengguna user_id mencipta tiket #TKT-2025-001234"
        API->>Staff: 24. Papar pengesahan & alih hala ke papan pemuka
        Queue->>Queue: 25. Proses kerja async
        API->>Dashboard: 26. Tambah tiket baharu ke seksyen "Penyerahan Saya"
        Dashboard->>Staff: 27. Papar senarai terkini masa nyata melalui polling Livewire
    else Pengesahan Gagal
        Form->>Staff: Papar ralat pengesahan
    end
```

---

## Rajah 4: Kelulusan Pinjaman melalui E-mel (Tiada Log Masuk Diperlukan)

```mermaid
sequenceDiagram
    actor Applicant as Staf/Tetamu<br/>Pemohon Pinjaman
    participant Form as Borang Pinjaman<br/>Penyerahan Permohonan
    participant LoanSvc as Perkhidmatan Pinjaman<br/>Pemprosesan
    participant ApprovalSvc as Perkhidmatan Kelulusan<br/>Penjanaan Token
    participant DB as Pangkalan Data
    actor Supervisor as Penyelia<br/>Gred 41+ Tiada Log Masuk
    participant Email as Perkhidmatan E-mel<br/>SMTP
    participant Web as Web Awam<br/>Halaman Pengesahan Token
    participant AdminAPI as API Pentadbir<br/>Rekod Keputusan
    participant Audit as Pelog Audit

    Applicant->>Form: 1. Hantar permohonan pinjaman<br/>asset_id, tarikh dimohon
    Form->>LoanSvc: 2. Sahkan permohonan
    LoanSvc->>DB: 3. Semak ketersediaan aset
    DB->>LoanSvc: 4. Pulangkan status stok
    alt Aset Tidak Tersedia
        LoanSvc->>Applicant: Aset tidak tersedia<br/>Papar alternatif
    else Aset Tersedia
        LoanSvc->>ApprovalSvc: 5. Mulakan proses kelulusan
        ApprovalSvc->>ApprovalSvc: 6. Tentukan penyelia<br/>Berdasarkan bahagian pemohon<br/>Penapis: Gred 41+
        ApprovalSvc->>ApprovalSvc: 7. Jana token JWT<br/>payload: application_id,<br/>timestamp,<br/>action: approve atau reject,<br/>exp: +7 days
        ApprovalSvc->>DB: 8. Simpan approval_request<br/>status = PENDING_SUPERVISOR<br/>token_hash = hash JWT
        ApprovalSvc->>Email: 9. Masuk baris gilir e-mel kelulusan<br/>dengan butang keputusan
        Email->>Supervisor: 10. Hantar e-mel tiada log masuk<br/>Subjek: "Permintaan Kelulusan Pinjaman Aset"<br/>Butang Luluskan: pautan + token<br/>Butang Tolak: pautan + token
        Supervisor->>Email: 11. Penyelia buka e-mel<br/>di mana-mana klien e-mel
        Supervisor->>Email: 12. Klik "Luluskan" atau "Tolak"<br/>URL butang mengandungi token JWT
        Email->>Web: 13. Navigasi ke halaman kelulusan<br/>?token=JWT_TOKEN&action=approve
        Web->>Web: 14. Sahkan struktur token
        alt Token Tidak Sah/Tamat Tempoh
            Web->>Supervisor: Ralat: token tidak sah/tamat tempoh<br/>Cadang hubungi pentadbir
        else Token Sah
            Web->>AdminAPI: 15. Nyahkod JWT & ekstrak application_id
            AdminAPI->>ApprovalSvc: 16. Sahkan gred penyelia ≥ 41
            ApprovalSvc->>DB: 17. Semak kebenaran penyelia
            DB->>ApprovalSvc: 18. Pulangkan keputusan kebenaran
            alt Semakan Gred Gagal
                Web->>Supervisor: Penyelia tidak dibenarkan
            else Semakan Gred Lulus
                AdminAPI->>DB: 19. Rekod keputusan kelulusan<br/>supervisor_id = verified_supervisor<br/>decision_timestamp = now()<br/>decision_ip_address = request.ip<br/>action = approve atau reject
                DB->>Audit: 20. Log perubahan medan<br/>audit pematuhan owen-it
                AdminAPI->>DB: 21. Kemas kini status permohonan<br/>status = APPROVED atau REJECTED
                AdminAPI->>Audit: 22. Log: "Penyelia id meluluskan/menolak pinjaman app_id"
                AdminAPI->>Applicant: 23. Hantar notifikasi<br/>"Permohonan pinjaman anda telah DILULUSKAN/DITOLAK"
                alt Keputusan = APPROVED
                    AdminAPI->>AdminAPI: 24. Tambah ke senarai tugasan pentadbir<br/>"Aset sedia untuk serahan/checkout"
                    AdminAPI->>Web: 25. Papar pengesahan: "Pinjaman DILULUSKAN"<br/>Pentadbir akan hubungi untuk serahan
                else Keputusan = REJECTED
                    AdminAPI->>Web: 26. Papar mesej: "Pinjaman DITOLAK"<br/>Sebab: nota opsyenal
                end
            end
        end
    end
```

---

## Rajah 5: Saringan (Triage) & Pemprosesan Tiket oleh Pentadbir

```mermaid
sequenceDiagram
    actor Admin as Pentadbir Filament<br/>Diautentikasi
    participant Auth as Auth Filament<br/>Pengawal RBAC
    participant Filament as Panel Filament<br/>Papan Pemuka
    participant Resource as Sumber Tiket<br/>Antaramuka CRUD
    participant UpdateSvc as Perkhidmatan Kemas Kini<br/>Logik Perniagaan
    participant DB as Pangkalan Data
    participant Notif as Baris Gilir Notifikasi<br/>Laravel Horizon
    participant Email as Perkhidmatan E-mel
    participant WebSocket as WebSocket<br/>Siaran Masa Nyata
    participant OtherAdmin as Pentadbir Lain<br/>Papan Pemuka
    participant Audit as Pelog Audit
    participant Submitter as Pemohon

    Admin->>Auth: 1. Log masuk ke Filament
    Auth->>Auth: 2. Sahkan kelayakan pentadbir
    Auth->>Filament: 3. Cipta sesi & kebenaran pentadbir
    Filament->>Admin: 4. Muat papan pemuka pentadbir<br/>Senarai tiket masa nyata
    Admin->>Resource: 5. Klik tiket<br/>Lihat butiran tajuk, pemohon, status, lampiran
    Resource->>DB: 6. Pertanyaan tiket & data berkaitan<br/>- Komen<br/>- Lampiran<br/>- Sejarah perubahan
    DB->>Resource: 7. Pulangkan data tiket
    Resource->>Admin: 8. Papar panel butiran tiket<br/>Status semasa: OPEN
    Admin->>Admin: 9. Semak tiket & buat keputusan:<br/>Pilihan 1: Tukar status<br/>Pilihan 2: Agih kepada rakan setugas<br/>Pilihan 3: Tambah komen<br/>Pilihan 4: Minta maklumat daripada pemohon
    alt Pentadbir Tukar Status
        Admin->>Resource: 10a. Kemas kini status:<br/>OPEN → IN_PROGRESS<br/>atau IN_PROGRESS → RESOLVED<br/>atau RESOLVED → CLOSED
        Resource->>UpdateSvc: 11a. Sahkan peralihan status
        UpdateSvc->>DB: 12a. Kemas kini status tiket
        DB->>Audit: 13a. Rekod perubahan medan<br/>audit pematuhan owen-it<br/>daripada: "OPEN"<br/>kepada: "IN_PROGRESS"<br/>changed_by: admin_id<br/>changed_at: timestamp
    else Pentadbir Agih kepada Rakan Setugas
        Admin->>Resource: 10b. Pilih rakan setugas daripada dropdown
        Resource->>UpdateSvc: 11b. Sahkan kebenaran pengagihan
        UpdateSvc->>DB: 12b. Kemas kini medan assigned_to
        DB->>Audit: 13b. Rekod perubahan medan pengagihan
    else Pentadbir Tambah Komen
        Admin->>Resource: 10c. Taip komen dalaman<br/>"Menunggu alat ganti, tindakan susulan pada Jumaat"
        Resource->>UpdateSvc: 11c. Simpan komen dengan cap masa
        UpdateSvc->>DB: 12c. Masukkan rekod komen
        DB->>Audit: 13c. Log penciptaan komen
    end
    
    alt Perlu Notifikasi kepada Pemohon
        UpdateSvc->>Notif: 14. Masuk baris gilir notifikasi:<br/>SendTicketStatusUpdate<br/>- recipient: submitter_email<br/>- ticket_id: TKT-2025-001234<br/>- new_status: IN_PROGRESS
        Notif->>Email: 15. Proses e-mel secara async<br/>Subjek: "Tiket #TKT-2025-001234<br/>Status Dikemas Kini kepada Dalam Proses"<br/>Badan: respons templat<br/>Bahasa: Bahasa Melayu
        Email->>Submitter: 16. Hantar e-mel kepada tetamu/staf<br/>dengan butiran tiket & langkah seterusnya
    end
    
    UpdateSvc->>WebSocket: 17. Siarkan event perubahan<br/>kepada semua papan pemuka pentadbir yang bersambung<br/>event: "ticket_updated"<br/>ticket_id: TKT-2025-001234<br/>new_status: "IN_PROGRESS"<br/>changed_by: admin_name<br/>timestamp
    
    WebSocket->>OtherAdmin: 18. Kemas kini masa nyata<br/>Laravel Reverb 1.6.3<br/>Papan pemuka pentadbir lain<br/>auto-muat semula senarai
    OtherAdmin->>OtherAdmin: 19. Notifikasi toast:<br/>"Tiket TKT-2025-001234<br/>dikemas kini oleh admin_name"
    
    UpdateSvc->>Audit: 20. Log aktiviti:<br/>"Pentadbir admin_id menukar status kepada IN_PROGRESS<br/>tiket #TKT-2025-001234"
    Audit->>DB: 21. Tulis log aktiviti<br/>spatie ActivityLog
    
    UpdateSvc->>Admin: 22. Papar toast pengesahan:<br/>"Tiket berjaya dikemas kini"
    Admin->>Admin: 23. Papan pemuka auto-muat semula<br/>Tiket dipindahkan ke seksyen IN_PROGRESS
```

---

## Rajah 6: AI Hibrid Awan - Bot FAQ dengan Penghalaan Model

```mermaid
sequenceDiagram
    actor User as Pengguna<br/>Tetamu atau Staf
    participant Chat as Antaramuka Sembang<br/>Komponen Livewire
    participant Classifier as Pengelas Pertanyaan<br/>Perkhidmatan Backend
    participant ModelRouter as Penghala Model<br/>Logik Keputusan
    participant DLP as Penapis DLP<br/>Data Loss Prevention
    participant Ollama as Ollama<br/>LLM Tempatan On-Premise
    participant RAG as Perkhidmatan RAG<br/>Dapatan Pangkalan Ilmu
    participant Bedrock as AWS Bedrock<br/>Model Claude Awan
    participant Formatter as Pemformat Respons<br/>Markdown + Bahasa Melayu
    participant Stream as Server-Sent Events SSE<br/>Respons Berstrim
    participant Logger as Pelog<br/>Audit Perbualan

    User->>Chat: 1. Buka antaramuka Bot FAQ
    Chat->>Chat: 2. Papar dalam Bahasa Melayu<br/>Sejarah sembang dipaparkan
    User->>Chat: 3. Taip pertanyaan<br/>Contoh: "Bagaimana cara reset kata laluan?"
    Chat->>Classifier: 4. Hantar pertanyaan untuk pengelasan
    Classifier->>Classifier: 5. Analisis kandungan pertanyaan<br/>Semak PII kata laluan, e-mel, ID<br/>Semak data kewangan<br/>Semak kelayakan/kredensial sistem
    
    alt Pertanyaan Mengandungi Data Sensitif
        Classifier->>ModelRouter: 6a. Kepekaan data: TINGGI<br/>PII/kewangan/kredensial dikesan
        ModelRouter->>ModelRouter: 7a. Keputusan: guna Ollama<br/>Sebab: kedaulatan data PKS 9.2.1<br/>Penghantaran ke awan tidak dibenarkan
        ModelRouter->>Ollama: 8a. Halakan ke LLM tempatan
        Ollama->>RAG: 9a. Dapatkan dokumen relevan<br/>daripada pangkalan ilmu tempatan<br/>menggunakan carian keserupaan vektor
        RAG->>Ollama: 10a. Pulangkan 3 dokumen FAQ teratas<br/>Tetingkap konteks: 4 mesej terakhir
        Ollama->>Ollama: 11a. Jana respons menggunakan model Ollama<br/>cth: Mistral, Llama<br/>Prompt: "Pengguna bertanya: query<br/>Konteks: RAG docs<br/>Jawab dalam Bahasa Melayu sahaja"
        Ollama->>Formatter: 12a. Pulangkan teks respons
    else Pertanyaan Maklumat Umum
        Classifier->>ModelRouter: 6b. Kepekaan data: RENDAH<br/>Tiada data sensitif dikesan
        ModelRouter->>DLP: 7b. Laksanakan penapis DLP<br/>Semak untuk:<br/>- Kredensial<br/>- IP dalaman<br/>- Data terhad<br/>- Pelanggaran pematuhan
        alt Penapis DLP Menyekat
            DLP->>Chat: 8b-blocked. Pertanyaan tidak dapat diproses<br/>Disekat oleh polisi keselamatan
            Chat->>User: Mesej: "Maaf, pertanyaan ini<br/>tidak dapat diproses data sensitif"
        else Penapis DLP Lulus
            DLP->>Bedrock: 9b. Halakan ke AWS Bedrock<br/>dengan kelulusan DLP
            Bedrock->>Bedrock: 10b. Pilih model Claude:<br/>- Pertanyaan ringkas → Claude Haiku pantas<br/>- Sederhana → Claude Sonnet seimbang<br/>- Kompleks → Claude Opus berkuasa
            Bedrock->>RAG: 11b. Opsyen Perkayakan konteks dengan<br/>carian web DuckDuckGo<br/>untuk maklumat terkini
            RAG->>Bedrock: 12b. Pulangkan konteks diperkaya
            Bedrock->>Bedrock: 13b. Jana respons dengan<br/>konteks diperkaya<br/>Output: markdown berstruktur
            Bedrock->>Formatter: 14b. Pulangkan respons
        end
    end
    
    Formatter->>Formatter: 15. Format respons:<br/>- Pastikan Bahasa Melayu<br/>- Kemas format markdown<br/>- Sahkan nada & kebermanfaatan<br/>- Potong jika > 2000 aksara
    
    Formatter->>Stream: 16. Strim respons ke klien<br/>menggunakan SSE<br/>Kepingan teks: 50-100 aksara setiap kepingan
    Stream->>Chat: 17. Terima & papar teks berstrim<br/>Masa nyata: setiap kepingan muncul seperti menaip
    Chat->>User: 18. Papar respons dalam antaramuka sembang<br/>dengan avatar & cap masa
    
    User->>Chat: 19. Pilihan: tanya soalan susulan<br/>Konteks perbualan dikekalkan
    Chat->>Classifier: 20. Ulang aliran dari langkah 4<br/>bersama sejarah perbualan
    
    Classifier->>Logger: 21. Selepas respons siap:<br/>Log entri perbualan
    Logger->>Logger: 22. Rekod:<br/>- user_id atau NULL untuk tetamu<br/>- teks pertanyaan<br/>- teks respons<br/>- model_used Ollama vs Bedrock<br/>- processing_time_ms<br/>- data_sensitivity_level<br/>- timestamp
    Logger->>Logger: 23. Opsyen Rekod maklum balas<br/>Penilaian suka/tidak suka<br/>Ulasan pengguna
    Logger->>Chat: 24. Simpan sejarah perbualan<br/>mengikut polisi retensi message_logs 90 hari
```

---

## Rajah 7: Kitar Hayat Lengkap Pinjaman Aset

```mermaid
sequenceDiagram
    actor Applicant as Staf/Tetamu<br/>Pemohon Pinjaman
    participant Form as Borang Pinjaman<br/>Penyerahan Permohonan
    participant LoanSvc as Perkhidmatan Pinjaman<br/>Pemprosesan
    participant Supervisor as Penyelia<br/>Kelulusan E-mel
    participant Admin as Pentadbir<br/>Panel Filament
    participant AssetMgmt as Pengurusan Aset<br/>Perkhidmatan Inventori
    participant Notifications as Baris Gilir Notifikasi
    participant Checkout as Proses Serahan (Checkout)<br/>Rekod Transaksi
    participant Monitoring as Perkhidmatan Pemantauan<br/>Peringatan & SLA
    participant Return as Proses Pemulangan<br/>Pemeriksaan & Rekod
    participant Maintenance as Penyelenggaraan<br/>Jika Rosak
    participant Audit as Pelog Audit
    participant Archive as Perkhidmatan Arkib<br/>Simpanan Pematuhan

    Applicant->>Form: 1. Isi permohonan pinjaman<br/>asset_type, kuantiti, tarikh dimohon
    Form->>LoanSvc: 2. Hantar permohonan
    LoanSvc->>AssetMgmt: 3. Semak ketersediaan aset<br/>stok vs checkout tertangguh
    AssetMgmt->>LoanSvc: 4. Pulangan: Tersedia = 5, Dimohon = 2
    LoanSvc->>Notifications: 5. Masuk baris gilir: maklumkan penyelia<br/>status = PENDING_SUPERVISOR_APPROVAL
    Notifications->>Supervisor: 6. E-mel dengan pautan kelulusan<br/>[Luluskan] [Tolak]
    Supervisor->>Supervisor: 7. Semak & buat keputusan
    alt Penyelia Menolak
        Supervisor->>LoanSvc: 8a. Klik butang Tolak<br/>Simpan keputusan dengan cap masa & IP
        LoanSvc->>Notifications: 9a. Masuk baris gilir: maklumkan pemohon<br/>"Permohonan pinjaman anda telah ditolak"
        LoanSvc->>Audit: 10a. Log: "Penyelia menolak pinjaman #[id]"
        LoanSvc->>Applicant: Status = REJECTED
    else Penyelia Meluluskan
        Supervisor->>LoanSvc: 8b. Klik butang Luluskan<br/>Melalui e-mel (tiada log masuk)
        LoanSvc->>Admin: 9b. Maklumkan pentadbir: "Sedia untuk serahan/checkout"<br/>Tambah ke senarai tugasan papan pemuka
        Admin->>Admin: 10b. Semak checkout tertangguh<br/>Sahkan identiti & kelayakan pemohon
        Admin->>Checkout: 11b. Cipta transaksi checkout<br/>asset_id, applicant_id, start_date
        Checkout->>AssetMgmt: 12b. Kemas kini status aset<br/>asset.status = CHECKED_OUT
        Checkout->>Audit: 13b. Cipta rekod loan_transaction<br/>id, asset_id, applicant_id<br/>checkout_date = now()
        Checkout->>Notifications: 14b. Masuk baris gilir: sahkan checkout<br/>"Serahan aset #[ref] anda sedia"
        Checkout->>Applicant: 15b. Hantar e-mel pengesahan<br/>butiran aset, tarikh perlu pulang
        Checkout->>Monitoring: 16b. Daftar untuk pemantauan<br/>Tarikh perlu pulang: [end_date]<br/>Peringatan: -3 hari
        Monitoring->>Applicant: 17b. Hantar peringatan 3 hari sebelum<br/>"Sila pulangkan aset sebelum [date]"
        Applicant->>Applicant: 18b. Guna aset dalam tempoh pinjaman
        Applicant->>Return: 19b. Pulangkan kepada pentadbir pada tarikh dipersetujui
        Return->>Admin: 20b. Pentadbir periksa keadaan<br/>- Semak fizikal<br/>- Uji fungsi<br/>- Dokumentasi keadaan<br/>Opsyen: GOOD / MINOR_DAMAGE / MAJOR_DAMAGE
        alt Aset Dalam Keadaan Baik
            Admin->>AssetMgmt: 21b-good. Tetapkan status aset = AVAILABLE
            Admin->>Audit: 22b-good. Kemas kini loan_transaction<br/>checkin_date = now()<br/>condition_report = "GOOD"<br/>damage_amount = 0
            Admin->>Notifications: 23b-good. Masuk baris gilir: pinjaman selesai<br/>"Pinjaman berjaya dipulangkan & ditutup"
            Admin->>Applicant: 24b-good. Hantar notifikasi penutupan
        else Aset Rosak
            Admin->>Maintenance: 21d-damage. Cipta permohonan penyelenggaraan<br/>asset_id, damage_description<br/>estimated_cost, priority
            Admin->>Audit: 22d-damage. Rekod kerosakan<br/>loan_transaction.condition_report = damage_desc<br/>damage_amount = [estimated_cost]
            Maintenance->>Maintenance: 23d-damage. Jadualkan pembaikan<br/>Hantar ke baris gilir penyelenggaraan<br/>Jejak kemajuan
            Admin->>Notifications: 24d-damage. Masuk baris gilir: notifikasi kerosakan<br/>"Kerosakan aset direkodkan"
            Admin->>Applicant: 25d-damage. Maklumkan penilaian kerosakan<br/>(Opsyen: perlu kelulusan pembayaran<br/>mengikut polisi kerosakan aset)
            Maintenance->>AssetMgmt: 26d-damage. Selesai pembaikan & kemas kini<br/>asset.status = AVAILABLE
        end
    end
    
    Audit->>Audit: 25. Log audit akhir<br/>- Jejak audit dwi (owen-it + spatie)<br/>- Semua peristiwa transaksi direkodkan<br/>- Perubahan medan direkodkan
    
    Archive->>Archive: 26. Arkib pematuhan<br/>- Rekod audit/aktiviti: retensi 7 tahun
    
    Archive->>Applicant: 27. Kitar hayat pinjaman selesai<br/>Transaksi dipaparkan dalam sejarah pemohon
```

---

## Rajah 8: Pemantauan & Akses Audit oleh Superuser

```mermaid
sequenceDiagram
    actor Superuser as Superuser<br/>Pentadbir Sistem
    participant Auth as Auth Filament<br/>2FA (TOTP)
    participant Filament as Panel Filament<br/>Papan Pemuka
    participant Pulse as Laravel Pulse<br/>Metrik Masa Nyata
    participant AuditLog as Pelog Audit<br/>owen-it (Pematuhan)
    participant ActivityLog as Log Aktiviti<br/>spatie (Operasi)
    participant Analytics as Enjin Analitik<br/>Penjanaan Laporan
    participant DB as Pangkalan Data
    participant Reports as Penjana Laporan<br/>Eksport PDF/Excel
    participant Telescope as Telescope<br/>Nyahpepijat (Opsyen)
    participant Horizon as Horizon<br/>Pemantauan Baris Gilir

    Superuser->>Auth: 1. Log masuk ke Filament
    Auth->>Auth: 2. Minta kod TOTP
    Superuser->>Auth: 3. Masukkan kod 2FA
    Auth->>Auth: 4. Sahkan TOTP (tingkap 30 saat)
    Auth->>Filament: 5. Cipta sesi superuser<br/>Berikan semua kebenaran
    Filament->>Superuser: 6. Muat papan pemuka superuser
    
    alt Pantau Kesihatan Sistem
        Superuser->>Pulse: 7a. Klik widget "Kesihatan Sistem"
        Pulse->>DB: 8a. Pertanyaan metrik (24 jam terakhir)<br/>- Bilangan pengguna aktif<br/>- Permintaan per minit<br/>- Kadar ralat<br/>- Pertanyaan perlahan
        DB->>Pulse: 9a. Pulangkan data metrik
        Pulse->>Superuser: 10a. Papar carta masa nyata<br/>Penunjuk status Hijau/Kuning/Merah
    else Akses Log Audit
        Superuser->>AuditLog: 7b. Klik "Log Audit"<br/>owen-it Auditing (pematuhan)
        AuditLog->>DB: 8b. Pertanyaan jadual audit<br/>Penapis: julat tarikh, jenis entiti, tindakan
        DB->>AuditLog: 9b. Pulangkan rekod audit
        AuditLog->>Superuser: 10b. Papar jejak audit<br/>- Bila medan berubah (timestamp)<br/>- Daripada nilai → Kepada nilai<br/>- Diubah oleh (user_id)<br/>- Alamat IP<br/>- Maklumat pelayar
    else Semak Log Aktiviti
        Superuser->>ActivityLog: 7c. Klik "Log Aktiviti"<br/>spatie ActivityLog (operasi)
        ActivityLog->>DB: 8c. Pertanyaan jadual aktiviti<br/>Penapis: pengguna, tarikh, jenis tindakan
        DB->>ActivityLog: 9c. Pulangkan rekod aktiviti
        ActivityLog->>Superuser: 10c. Papar aktiviti pengguna<br/>- Pengguna X melihat tiket Y<br/>- Pengguna X mencipta pinjaman #Z<br/>- Pengguna X menukar status A ke B<br/>- Pengguna X memuat turun laporan
    else Jana Laporan Analitik
        Superuser->>Analytics: 7d. Klik "Jana Laporan"<br/>Pilih: julat tarikh, jenis laporan
        Analytics->>DB: 8d. Pertanyaan data agregat:<br/>- Statistik tiket (bilangan, purata masa selesai)<br/>- Statistik pinjaman (kadar kelulusan, penggunaan aset)<br/>- Penggunaan Bot AI (pertanyaan, model digunakan)<br/>- Log ralat (kegagalan, punca)
        DB->>Analytics: 9d. Pulangkan data agregat
        Analytics->>Reports: 10d. Format laporan dalam PDF/Excel
        Reports->>Superuser: 11d. Muat turun laporan<br/>Simpan ke komputer atau e-mel
    else Pantau Status Baris Gilir
        Superuser->>Horizon: 7e. Klik "Status Baris Gilir"<br/>Papan pemuka Laravel Horizon
        Horizon->>Horizon: 8e. Papar:<br/>- Kerja menunggu (bilangan)<br/>- Kerja sedang diproses<br/>- Kerja gagal (stack trace)<br/>- Kadar pemprosesan baris gilir
        Horizon->>DB: 9e. Pertanyaan jadual failed_jobs
        DB->>Horizon: 10e. Pulangkan butiran kerja gagal
        Horizon->>Superuser: 11e. Papar pilihan cuba semula<br/>Superuser boleh: Retry, Delete, Lihat log
    end
    
    alt Eksport Data Pematuhan
        Superuser->>Reports: 12. Minta eksport data<br/>Format: CSV/JSON<br/>Julat: 7 hari terakhir
        Reports->>DB: 13. Pertanyaan log audit + aktiviti<br/>Data sensitif ditanda
        DB->>Reports: 14. Pulangkan set data penuh
        Reports->>Reports: 15. Terap watermark pematuhan<br/>- Tambah: "TERHAD"<br/>- Tambah: cap masa eksport & ID pengeksport<br/>- Hash watermark untuk elak ubah suai
        Reports->>Superuser: 16. Jana ZIP disulitkan
        Superuser->>Superuser: 17. Muat turun eksport<br/>Untuk serahan audit
    else Konfigurasi Sistem
        Superuser->>Filament: 18. Klik "Konfigurasi"
        Filament->>Superuser: 19. Papar tetapan sistem:<br/>- Konfigurasi e-mel<br/>- Templat notifikasi<br/>- Keutamaan model AI<br/>- Polisi retensi data<br/>- Ambang SLA<br/>- Peranan & kebenaran pengguna
        Superuser->>Filament: 20. Ubah tetapan jika perlu
        Filament->>DB: 21. Kemas kini konfigurasi dalam pangkalan data
        Filament->>AuditLog: 22. Log perubahan konfigurasi<br/>"Superuser menukar AI_MODEL_PREFERENCE daripada Bedrock kepada Ollama"
    end
    
    AuditLog->>AuditLog: 23. Rekod audit akhir<br/>Akses superuser direkodkan<br/>Tindakan dijejak<br/>Cap masa & IP direkodkan
```

---

## Legenda & Panduan Notasi

### Pelaku/Swimlane

- 🔓 **Pengguna Tetamu**: Akses awam tanpa log masuk (rate-limited)
- 🔐 **Staf (Diautentikasi)**: Log masuk melalui Laravel Breeze (e-mel/nama pengguna + kata laluan)
- ✅ **Penyelia/Pelulus**: Gred 41+, keputusan melalui e-mel (tiada log masuk)
- ⚙️ **Pentadbir**: Akses panel Filament (dilindungi RBAC)
- 👨‍💼 **Superuser**: Akses penuh sistem dengan 2FA (TOTP)
- 🤖 **Sistem AI**: Penghalaan pintar & penjanaan respons
- 🔧 **Perkhidmatan Backend**: Baris gilir, WebSocket, notifikasi, audit

### Petunjuk Aliran Data

- **→** = Operasi segerak (jawapan segera)
- **⟹** = Operasi tak segerak (kerja baris gilir, webhook)
- **◉** = Siaran masa nyata (WebSocket, Laravel Reverb 1.6.3)
- **[database]** = Operasi baca/tulis pangkalan data

### Titik Keputusan

- **Berlian (◇)** = Titik keputusan/cabang
- **YA** = Aliran diteruskan (berjaya/diluluskan)
- **TIDAK** / **❌** = Aliran beralih ke ralat/alternatif

### Petunjuk Status

- ✓ / **✅** = Berjaya, operasi selesai
- ❌ / **✗** = Gagal, operasi ditolak
- ⏳ = Tertangguh, menunggu keputusan
- ⟲ = Mekanisme cuba semula

### Rujukan Teknologi

- **Laravel Breeze** = Rangka kerja autentikasi
- **Livewire 3.7.3** = Komponen reaktif & kemas kini masa nyata
- **Alpine.js 3** = Interaktiviti ringan
- **Filament 4.3.1** = Panel pentadbir (CRUD, RBAC, Server-Driven UI)
- **Laravel Reverb 1.6.3** = Pelayan WebSocket untuk komunikasi masa nyata
- **Laravel Horizon 5.41.0** = Papan pemuka pemantauan baris gilir
- **Laravel Pulse 1.4.7** = Metrik sistem masa nyata
- **Laravel Telescope 5.16.0** = Alat nyahpepijat (superuser sahaja)
- **Ollama** = LLM tempatan (on-premise, kedaulatan data)
- **AWS Bedrock** = Model AI awan (Claude Opus/Sonnet/Haiku)

### Keselamatan & Pematuhan

- **🔒** = Perlu autentikasi
- **🛡️** = Penapis keselamatan/DLP digunakan
- **📋** = Log audit (sistem dwi: owen-it + spatie)
- **TLS 1.3** = Penyulitan HTTPS dalam transit
- **AES-256** = Penyulitan pangkalan data semasa rehat
- **JWT** = Autentikasi berasaskan token
- **TOTP** = Kata laluan sekali guna berasaskan masa (2FA)

### Tahap Kepekaan Data

- **RENDAH** = Maklumat umum (boleh hala ke Bedrock)
- **SEDERHANA** = Data operasi dalaman (perlu semakan DLP)
- **TINGGI** = PII/kewangan/kredensial (mesti guna Ollama; tiada awan)

### Rujukan Masa

- **Segera** = Segerak, operasi menghalang (blocking)
- **Async** = Baris gilir, tidak menghalang (melalui Laravel Horizon)
- **Berjadual** = Cron job (pemantauan, peringatan, pengarkiban)
- **Berstrim** = SSE (pecahan respons masa nyata)

### Nota Bahasa

- **Bahasa Melayu** = Bahasa antaramuka utama (v3.6.0+), termasuk respons AI (v3.6.1)
- **Fail `lang/en/`** = Dikekalkan untuk rujukan teknikal; penukar bahasa dilumpuhkan

---

## Indeks Rujukan Silang

| Rajah | Dokumen v3.6.1 | Seksyen | Keperluan Utama |
|------|----------------|---------|----------------|
| Rajah 1 (Gambaran Keseluruhan) | D00, D03 | §1, §2 | Seni bina sistem, modul, peranan |
| Rajah 2 (Helpdesk Tetamu) | D03, D04, D09 | §5.1, §4.1 | Akses hibrid, borang Livewire, penciptaan tiket |
| Rajah 3 (Helpdesk Staf) | D03, D04, D12 | §5.1, §4.1, §3.1 | Auto-isi, autentikasi, papan pemuka |
| Rajah 4 (Kelulusan Pinjaman) | D03, D04, D09 | §5.2, §4.2, §4.3 | Kelulusan melalui e-mel, token JWT, pengesahan token |
| Rajah 5 (Triage Pentadbir) | D03, D04, D11 | §5.3, §5, §6 | RBAC, panel Filament, kemas kini masa nyata, log audit |
| Rajah 6 (Bot FAQ AI) | D03, D04, D18 | §5.9, §8, §5 | Penghalaan model, pengelasan data, Ollama vs Bedrock, RAG |
| Rajah 7 (Kitar Hayat Pinjaman) | D03, D04, D09 | §5.2, §4.2, §4.3 | Aliran penuh, kelulusan, checkout, pemulangan, penyelenggaraan |
| Rajah 8 (Superuser) | D03, D04, D11 | §5.3, §5, §6 | Pemantauan, akses audit, analitik, pematuhan |

---

## Maklumat Dokumen

**Sistem**: ICTServe v3.6.1 (Dalaman Sahaja) - Helpdesk & Pinjaman Aset  
**Tarikh**: 29 Disember 2025  
**Format**: Rajah Swimlane Mermaid (serasi dengan GitHub Markdown)  
**Bahasa**: Bahasa Melayu (v3.6.0+; termasuk AI Chatbot v3.6.1)  
**Aksesibiliti**: Patuh WCAG 2.2 Tahap AA  
**Skop**: Dokumentasi proses menyeluruh daripada permohonan hingga audit/arkib  
**Status**: ✅ Sedia Produksi

---

**Tamat Dokumen Rajah Swimlane**
