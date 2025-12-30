# Data Flow Diagram (DFD) ICTServe v3.6.1 (Bahasa Melayu)

Dokumen ini menghimpunkan rajah DFD berperingkat (Context → Level 0 → Level 1 → Level 2) bagi ICTServe v3.6.1.

## Nota Notasi (ringkas)
- **Entiti luaran**: kotak segi empat
- **Proses**: kotak berpenjuru bulat (diwakili sebagai node proses)
- **Data store**: `[(Dx: ...)]`
- **Aliran data**: anak panah berlabel

> Nota versi: Sesetengah dokumen ringkasan sistem mungkin menyebut pendekatan SSO-only untuk versi seterusnya. Rajah di bawah memodelkan **aliran v3.6.1** (termasuk saluran tetamu/token jika diaktifkan).

---

## DFD-ICT-0 — Rajah Konteks (Context Diagram)

```mermaid
flowchart TD
    %% User Entities
    STAFF_AUTH[Staf MOTAC - Authenticated]
    GUEST[Staf/Tetamu - Tanpa Log Masuk + Token]
    ADMIN[Admin BPM]
    SUPERUSER[Superuser BPM]
    APPROVER[Pegawai Kelulusan]

    %% Core System
    ICTSERVE((Sistem ICTServe))

    %% Infrastructure Services
    EMAIL[Sistem E-mel - SMTP]
    RECAPTCHA[reCAPTCHA]
    STORAGE[Storan Lampiran]
    REDIS[Redis - Queue/Cache]
    REVERB[WebSocket - Laravel Reverb]
    SSO[SSO/OAuth Provider - Opsyen]
    AI[Hybrid AI - Ollama + AWS Bedrock]

    %% User Interactions
    STAFF_AUTH --> ICTSERVE
    ICTSERVE --> STAFF_AUTH
    
    GUEST --> ICTSERVE
    ICTSERVE --> GUEST
    
    ADMIN --> ICTSERVE
    ICTSERVE --> ADMIN
    
    SUPERUSER --> ICTSERVE
    ICTSERVE --> SUPERUSER
    
    APPROVER --> ICTSERVE
    ICTSERVE --> APPROVER

    %% Infrastructure Interactions
    ICTSERVE --> EMAIL
    EMAIL --> ICTSERVE
    
    ICTSERVE --> RECAPTCHA
    RECAPTCHA --> ICTSERVE
    
    ICTSERVE --> STORAGE
    
    ICTSERVE --> REDIS
    REDIS --> ICTSERVE
    
    ICTSERVE --> REVERB
    REVERB --> STAFF_AUTH
    
    ICTSERVE --> SSO
    SSO --> ICTSERVE
    
    ICTSERVE --> AI
    AI --> ICTSERVE

    subgraph Pengguna[Entiti Pengguna]
        STAFF_AUTH
        GUEST
        ADMIN
        SUPERUSER
        APPROVER
    end

    subgraph Infrastruktur[Perkhidmatan Infrastruktur]
        EMAIL
        RECAPTCHA
        STORAGE
        REDIS
        REVERB
        SSO
        AI
    end
```

---

## DFD-ICT-L0 — Level 0 (Decomposition)

```mermaid
graph TD
    STAFF_AUTH[Staf MOTAC - Authenticated]
    GUEST[Staf/Tetamu - Token]
    ADMIN[Admin BPM]
    APPROVER[Pegawai Kelulusan]

    EMAIL[Sistem E-mel]
    REVERB[Reverb]
    REDIS[Redis]
    STORAGE[Storan Lampiran]
    AI_EXT[Hybrid AI]

    subgraph L0[ICTServe — Level 0]
        P1[P1: 1.0 Pengurusan Helpdesk]
        P2[P2: 2.0 Pengurusan Pinjaman Aset]
        P3[P3: 3.0 Pengurusan Inventori]
        P4[P4: 4.0 Autentikasi & Kebenaran]
        P5[P5: 5.0 Pentadbiran - Filament]
        P6[P6: 6.0 Audit & Logging]
        P7[P7: 7.0 Notifikasi & Komunikasi]
        P8[P8: 8.0 AI Integration]

        DS_USERS[(D1: Users/Auth/RBAC)]
        DS_HELPDESK[(D2: Helpdesk Tickets)]
        DS_LOANS[(D3: Loan Applications)]
        DS_ASSETS[(D4: Assets/Inventory)]
        DS_AUDIT[(D5: Audits/Activity/Logs)]
        DS_AI[(D6: AI/Knowledge/Conversations)]
    end

    STAFF_AUTH -->|Login SSO dan Akses Sistem| P4
    P4 -->|Sesi atau Profil| DS_USERS

    STAFF_AUTH -->|Hantar Tiket| P1
    GUEST -->|Hantar Tiket dengan Token| P1
    P1 -->|Rekod Tiket| DS_HELPDESK
    P1 -->|Permintaan Notifikasi| P7

    STAFF_AUTH -->|Mohon Pinjaman| P2
    GUEST -->|Mohon Pinjaman dengan Token| P2
    P2 -->|Rekod Pinjaman| DS_LOANS
    P2 -->|Semak Ketersediaan Aset| P3
    P3 -->|Rekod Inventori| DS_ASSETS
    P2 -->|Permintaan Notifikasi| P7

    APPROVER -->|Keputusan Kelulusan| P2

    ADMIN -->|Operasi atau Semakan| P5
    P5 -->|Query Data| DS_HELPDESK
    P5 -->|Query Data| DS_LOANS
    P5 -->|Query Data| DS_ASSETS

    P1 -->|Log Aktiviti| P6
    P2 -->|Log Aktiviti| P6
    P3 -->|Log Aktiviti| P6
    P4 -->|Log Aktiviti| P6
    P5 -->|Log Aktiviti| P6
    P6 -->|Audit Trail| DS_AUDIT

    P7 -->|E-mel| EMAIL
    P7 -->|Broadcast| REVERB
    P7 -->|Dispatch Job| REDIS

    P1 -->|Lampiran| STORAGE
    P2 -->|Lampiran| STORAGE

    STAFF_AUTH -->|Soalan atau FAQ| P8
    GUEST -->|Soalan atau FAQ| P8
    P8 -->|Simpan Log atau Conversation| DS_AI
    P8 -->|Panggilan AI| AI_EXT
    AI_EXT -->|Respons| P8
```

---

## DFD-ICT-PP — Level 1: Pengurusan Pengguna

```mermaid
graph TD
    STAFF[Staf/Pengguna]
    ADMIN[Pentadbir Sistem]
    SSO[SSO/OAuth Provider]
    EMAIL[Sistem E-mel]

    subgraph PP[1.0 Pengurusan Pengguna]
        PP1[1.1 Daftar Pengguna opsyen]
        PP2[1.2 Login Sistem]
        PP3[1.3 Kemaskini Profil]
        PP4[1.4 Lupa Kata Laluan]
    end

    DS_USERS[(D1: Users/Auth/RBAC)]
    DS_AUDIT[(D5: Audits/Activity/Logs)]

    STAFF -->|Maklumat Pendaftaran| PP1
    PP1 -->|Simpan Rekod| DS_USERS
    PP1 -->|E-mel Pengesahan jika ada| EMAIL

    STAFF -->|Kredensial Login atau SSO| PP2
    SSO -->|Token atau Profil Asas| PP2
    PP2 -->|Semak Pengguna| DS_USERS
    PP2 -->|Rekod Audit| DS_AUDIT
    PP2 -->|Akses Sistem| STAFF

    STAFF -->|Kemaskini Profil| PP3
    PP3 -->|Kemaskini Rekod| DS_USERS
    PP3 -->|Rekod Audit| DS_AUDIT

    STAFF -->|Permintaan Reset| PP4
    PP4 -->|Semak Akaun| DS_USERS
    PP4 -->|Hantar Pautan Reset| EMAIL
```

---

## DFD-ICT-HD — Level 1: Pengurusan Helpdesk

```mermaid
graph TD
    STAFF[Staf Authenticated]
    GUEST[Staf atau Tetamu dengan Token]
    TECH[Juruteknik atau Admin]

    EMAIL[Sistem E-mel]
    REVERB[Reverb]
    REDIS[Redis Queue]
    AI_EXT[Hybrid AI]
    STORAGE[Storan Lampiran]

    subgraph HD[2.0 Pengurusan Helpdesk]
        H1[2.1 Buat Tiket Helpdesk]
        H2[2.2 Agih Tiket kepada Juruteknik]
        H3[2.3 Proses Tiket]
        H4[2.4 Tutup Tiket]
    end

    DS_TICKETS[(D2: Helpdesk Tickets)]
    DS_COMMENTS[(D2.1: Helpdesk Comments)]
    DS_AUDIT[(D5: Audits/Activity/Logs)]

    STAFF -->|Aduan Permintaan dengan Lampiran| H1
    GUEST -->|Aduan Permintaan dengan Lampiran| H1

    H1 -->|Cadangan Kategori atau Keutamaan| AI_EXT
    AI_EXT -->|Cadangan| H1

    H1 -->|Simpan Tiket| DS_TICKETS
    H1 -->|Simpan Lampiran| STORAGE
    H1 -->|Dispatch Notifikasi| REDIS

    H1 -->|Tiket Baru| H2
    H2 -->|Semak Tiket| DS_TICKETS
    H2 -->|Agih Tugasan| TECH
    H2 -->|Kemaskini Status| DS_TICKETS

    TECH -->|Tindakan Penyelesaian| H3
    H3 -->|Komen atau Progress| DS_COMMENTS
    H3 -->|Kemaskini Status| DS_TICKETS
    H3 -->|Broadcast Status| REVERB
    H3 -->|Rekod Audit| DS_AUDIT

    TECH -->|Tutup Tiket| H4
    STAFF -->|Maklumbalas jika ada| H4
    H4 -->|Simpan Maklumbalas| DS_COMMENTS
    H4 -->|Kemaskini Status| DS_TICKETS
    H4 -->|Hantar Notifikasi E-mel atau Reverb| EMAIL
    H4 -->|Broadcast Status| REVERB
    H4 -->|Rekod Audit| DS_AUDIT
```

---

## DFD-ICT-AL — Level 1: Pengurusan Pinjaman Aset

```mermaid
graph TD
    STAFF[Staf Authenticated]
    GUEST[Staf atau Tetamu dengan Token]
    APPROVER[Pegawai Kelulusan]
    OFFICER[Pegawai Aset atau Admin]

    EMAIL[Sistem E-mel]
    REDIS[Redis Queue]
    REVERB[Reverb]

    subgraph AL[3.0 Pengurusan Pinjaman Aset]
        A1[3.1 Mohon Pinjaman]
        A2[3.2 Proses Kelulusan]
        A3[3.3 Rekod Penyerahan Check-out]
        A4[3.4 Rekod Pemulangan Check-in]
    end

    DS_LOANS[(D3: Loan Applications)]
    DS_ASSETS[(D4: Assets/Inventory)]
    DS_TX[(D3.1: Loan Transactions)]
    DS_AUDIT[(D5: Audits/Activity/Logs)]

    STAFF -->|Permohonan Pinjaman| A1
    GUEST -->|Permohonan Pinjaman| A1

    A1 -->|Semak Ketersediaan| DS_ASSETS
    A1 -->|Simpan Permohonan| DS_LOANS
    A1 -->|Dispatch Permintaan Kelulusan| REDIS

    A2 -->|Pautan Kelulusan E-mel| EMAIL
    APPROVER -->|Keputusan Lulus atau Tolak| A2
    A2 -->|Kemaskini Status| DS_LOANS
    A2 -->|Notifikasi Keputusan| EMAIL
    A2 -->|Broadcast Status| REVERB
    A2 -->|Rekod Audit| DS_AUDIT

    OFFICER -->|Maklumat Penyerahan| A3
    A3 -->|Kemaskini Status Aset| DS_ASSETS
    A3 -->|Rekod Transaksi| DS_TX
    A3 -->|Kemaskini Pinjaman| DS_LOANS
    A3 -->|Rekod Audit| DS_AUDIT

    STAFF -->|Pulangan Aset| A4
    OFFICER -->|Pengesahan Pemulangan| A4
    A4 -->|Kemaskini Status Aset| DS_ASSETS
    A4 -->|Rekod Transaksi| DS_TX
    A4 -->|Tutup Pinjaman| DS_LOANS
    A4 -->|Notifikasi| EMAIL
    A4 -->|Rekod Audit| DS_AUDIT
```

---

## DFD-ICT-IM — Level 1: Pengurusan Inventori

```mermaid
graph TD
    OFFICER[Pegawai Aset]
    ADMIN[Pentadbir]
    STAFF[Staf]

    subgraph IM[4.0 Pengurusan Inventori]
        I1[4.1 Daftar Aset Baru]
        I2[4.2 Kemaskini Maklumat Aset]
        I3[4.3 Jana Kod QR]
        I4[4.4 Urus Status Aset]
    end

    DS_ASSETS[(D4: Assets/Inventory)]
    DS_AUDIT[(D5: Audits/Activity/Logs)]

    OFFICER -->|Maklumat Aset Baru| I1
    I1 -->|Simpan Aset| DS_ASSETS
    I1 -->|Rekod Audit| DS_AUDIT

    OFFICER -->|Kemaskini Data| I2
    ADMIN -->|Kemaskini Data| I2
    I2 -->|Kemaskini Rekod| DS_ASSETS
    I2 -->|Rekod Audit| DS_AUDIT

    OFFICER -->|Permintaan QR| I3
    I3 -->|Baca Data Aset| DS_ASSETS
    I3 -->|Kod QR| OFFICER

    OFFICER -->|Perubahan Status| I4
    I4 -->|Kemaskini Status| DS_ASSETS
    I4 -->|Rekod Audit| DS_AUDIT
    STAFF -->|Imbas QR| I4
    I4 -->|Maklumat Aset| STAFF
```

---

## DFD-ICT-HD-2 — Level 2: Helpdesk (Tiket + Lampiran + SLA + Notifikasi)

```mermaid
graph TD
    STAFF[Staf Authenticated]
    GUEST[Staf atau Tetamu dengan Token]
    ADMIN[Admin atau Juruteknik]

    RECAPTCHA[reCAPTCHA]
    CLAMAV[ClamAV Imbas Virus]
    STORAGE[Storan Lampiran]
    REDIS[Redis Queue]
    REVERB[Reverb]
    EMAIL[Sistem E-mel]
    AI_EXT[Hybrid AI]

    subgraph HD2[2.x Pecahan Helpdesk]
        H21[2.1 Terima Input Tiket]
        H22[2.2 Validasi dan Sanitasi]
        H23[2.3 Verifikasi reCAPTCHA tetamu]
        H24[2.4 Proses Lampiran Imbas Simpan]
        H25[2.5 Simpan Tiket]
        H26[2.6 Agihan dan Kemas Kini Status]
        H27[2.7 Pemantauan SLA]
        H28[2.8 Notifikasi E-mel Reverb]
    end

    DS_USERS[(D1: Users)]
    DS_TICKETS[(D2: Helpdesk Tickets)]
    DS_COMMENTS[(D2.1: Helpdesk Comments)]
    DS_ATTACH[(D2.2: Helpdesk Attachments)]
    DS_AUDIT[(D5: Audits/Activity/Logs)]

    STAFF -->|Borang Tiket Lampiran| H21
    GUEST -->|Borang Tiket Lampiran| H21

    H21 -->|Data Mentah| H22
    H22 -->|Ralat Validasi| STAFF
    H22 -->|Semak Pengguna| DS_USERS

    GUEST -->|Token atau Skor reCAPTCHA| H23
    H23 -->|Semak Skor| RECAPTCHA
    RECAPTCHA -->|Keputusan| H23
    H23 -->|Gagal blok| GUEST
    H23 -->|Lulus| H24

    STAFF -->|Lampiran| H24
    H24 -->|Imbas Virus| CLAMAV
    CLAMAV -->|Bersih atau Kuarantin| H24
    H24 -->|Simpan Fail| STORAGE
    H24 -->|Simpan Metadata| DS_ATTACH

    H22 -->|Cadangan AI kategori atau keutamaan| AI_EXT
    AI_EXT -->|Cadangan| H22

    H22 -->|Data Valid| H25
    H25 -->|Simpan Tiket| DS_TICKETS
    H25 -->|Rekod Audit| DS_AUDIT

    ADMIN -->|Ambil atau Agih atau Kemaskini| H26
    H26 -->|Baca Tiket| DS_TICKETS
    H26 -->|Kemas Kini Status| DS_TICKETS
    H26 -->|Komen atau Progress| DS_COMMENTS
    H26 -->|Rekod Audit| DS_AUDIT

    H27 -->|Baca Tiket dan SLA| DS_TICKETS
    H27 -->|Dispatch SLA alert job| REDIS

    H28 -->|Dispatch email job| REDIS
    REDIS -->|Hantar E-mel| EMAIL

    H28 -->|Broadcast events| REVERB
    REVERB -->|Status atau Notifikasi| STAFF
```

---

## DFD-ICT-AL-2 — Level 2: Pinjaman Aset (Permohonan + Kelulusan + Check-out/in)

```mermaid
graph TD
    STAFF[Staf Authenticated]
    GUEST[Staf atau Tetamu dengan Token]
    APPROVER[Pegawai Kelulusan]
    OFFICER[Pegawai Aset atau Admin]

    EMAIL[Sistem E-mel]
    REDIS[Redis Queue]
    REVERB[Reverb]

    subgraph AL2[3.x Pecahan Pinjaman Aset]
        A21[3.1 Terima Permohonan]
        A22[3.2 Validasi & Polisi]
        A23[3.3 Semak Ketersediaan Aset]
        A24[3.4 Simpan Permohonan]
        A25[3.5 Jana Token Kelulusan]
        A26[3.6 Hantar Permintaan Kelulusan]
        A27[3.7 Rekod Keputusan Kelulusan]
        A28[3.8 Check-out Aset]
        A29[3.9 Check-in Aset]
        A2A[3.10 Notifikasi & Broadcast]
    end

    DS_LOANS[(D3: Loan Applications)]
    DS_LOAN_TOKENS[(D3.2: Approval Tokens)]
    DS_APPROVALS[(D3.3: Loan Approvals)]
    DS_ASSETS[(D4: Assets)]
    DS_TX[(D3.1: Loan Transactions)]
    DS_AUDIT[(D5: Audits/Activity/Logs)]

    STAFF -->|Borang Pinjaman| A21
    GUEST -->|Borang Pinjaman| A21

    A21 -->|Data Mentah| A22
    A22 -->|Data Valid| A23
    A22 -->|Ralat| STAFF

    A23 -->|Query Aset| DS_ASSETS
    DS_ASSETS -->|Status Aset| A23

    A23 -->|Lulus Semak| A24
    A24 -->|Simpan Permohonan| DS_LOANS
    A24 -->|Rekod Audit| DS_AUDIT

    A25 -->|Cipta Token| DS_LOAN_TOKENS
    A26 -->|Dispatch email job| REDIS
    REDIS -->|Hantar Pautan Kelulusan| EMAIL

    APPROVER -->|Lulus atau Tolak dengan Token| A27
    A27 -->|Simpan Keputusan| DS_APPROVALS
    A27 -->|Kemas Kini Status| DS_LOANS
    A27 -->|Rekod Audit| DS_AUDIT

    OFFICER -->|Proses Penyerahan| A28
    A28 -->|Update Status Aset| DS_ASSETS
    A28 -->|Rekod Transaksi| DS_TX
    A28 -->|Update Pinjaman| DS_LOANS
    A28 -->|Rekod Audit| DS_AUDIT

    OFFICER -->|Proses Pemulangan| A29
    A29 -->|Update Status Aset| DS_ASSETS
    A29 -->|Rekod Transaksi| DS_TX
    A29 -->|Tutup Pinjaman| DS_LOANS
    A29 -->|Rekod Audit| DS_AUDIT

    A2A -->|E-mel Status| EMAIL
    A2A -->|Broadcast Status| REVERB
    REVERB -->|Kemaskini UI| STAFF
```

---

## DFD-ICT-AI-2 — Level 2: AI/RAG (Chat + Dokumen + Auto-reply)

```mermaid
graph TD
    STAFF[Staf Authenticated]
    GUEST[Staf atau Tetamu]

    OLLAMA[Ollama Local]
    BEDROCK[AWS Bedrock]
    REDIS[Redis Queue]
    REVERB[Reverb]

    subgraph AI2[8.x Pecahan AI Integration]
        AI21[8.1 Terima Soalan/Prompt]
        AI22[8.2 Klasifikasi dan Routing ModelRouter]
        AI23[8.3 RAG Retrieval FAQ/Chunks]
        AI24[8.4 Jana Respons LLM]
        AI25[8.5 Simpan Conversation dan Logs]
        AI26[8.6 Auto-Reply Draft opsyen]
        AI27[8.7 Broadcast Status AI]
    end

    DS_FAQ[(D6.1: FAQs)]
    DS_DOCS[(D6.2: Documents)]
    DS_CHUNKS[(D6.3: Document Chunks dan Embedding)]
    DS_CONV[(D6.4: Conversations Auth/Guest)]
    DS_MSGLOG[(D6.5: Message Logs)]
    DS_AUTOREPLY[(D6.6: Auto Reply Drafts Templates)]

    STAFF -->|Soalan| AI21
    GUEST -->|Soalan| AI21

    AI21 -->|Prompt| AI22
    AI22 -->|FAQ lookup| AI23

    AI23 -->|Ambil FAQ| DS_FAQ
    AI23 -->|Ambil Dokumen| DS_DOCS
    AI23 -->|Ambil Chunk Relevan| DS_CHUNKS

    AI22 -->|Route Local| OLLAMA
    AI22 -->|Route Cloud jika diaktifkan| BEDROCK

    OLLAMA -->|Respons| AI24
    BEDROCK -->|Respons| AI24

    AI24 -->|Jawapan| STAFF

    AI25 -->|Simpan perbualan| DS_CONV
    AI25 -->|Simpan log mesej| DS_MSGLOG

    AI26 -->|Dispatch auto-reply job| REDIS
    REDIS -->|Generate Draft| DS_AUTOREPLY

    AI27 -->|Broadcast events| REVERB
    REVERB -->|Status AI| STAFF
```
