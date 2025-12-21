Use Case Diagrams (UC-ICT-XX format)
Data Flow Diagrams (DFD-ICT-XX format)
Entity Relationship Diagram (ERD)

UC-ICT-PP: Pengurusan Pengguna (User Management)

```mermaid

graph TB
    subgraph "UC-ICT-PP: Pengurusan Pengguna"
        PP1[Daftar Pengguna Baru]
        PP2[Login Sistem]
        PP3[Kemaskini Profil]
        PP4[Lupa Kata Laluan]
        PP5[Logout]
        PP6[Tukar Kata Laluan]
    end
    
    User((Pengguna/Staff))
    Admin((Pentadbir Sistem))
    SSO[Single Sign-On System]
    
    User --> PP1
    User --> PP2
    User --> PP3
    User --> PP4
    User --> PP5
    User --> PP6
    Admin --> PP1
    Admin --> PP3
    PP2 -.include.-> SSO

```

UC-ICT-HD: Pengurusan Helpdesk

```mermaid

graph TB
    subgraph "UC-ICT-HD: Pengurusan Helpdesk"
        HD1[Buat Tiket Helpdesk]
        HD2[Papar Senarai Tiket]
        HD3[Cari Tiket]
        HD4[Kemaskini Status Tiket]
        HD5[Tutup Tiket]
        HD6[Beri Maklumbalas]
        HD7[Agih Tiket]
        HD8[Eskalasi Tiket]
    end
    
    Staff((Staff))
    Technician((Juruteknik ICT))
    Admin((Pentadbir Sistem))
    AI[Hybrid Cloud AI]
    
    Staff --> HD1
    Staff --> HD2
    Staff --> HD3
    Staff --> HD6
    Technician --> HD2
    Technician --> HD3
    Technician --> HD4
    Technician --> HD5
    Technician --> HD7
    Admin --> HD8
    HD1 -.extend.-> AI
    HD7 -.extend.-> AI

```

UC-ICT-AL: Pengurusan Pinjaman Aset (Asset Loan Management)

```mermaid

graph TB
    subgraph "UC-ICT-AL: Pengurusan Pinjaman Aset"
        AL1[Mohon Pinjaman Aset]
        AL2[Semak Ketersediaan Aset]
        AL3[Lulus Permohonan]
        AL4[Tolak Permohonan]
        AL5[Rekod Penyerahan Aset]
        AL6[Rekod Pemulangan Aset]
        AL7[Papar Senarai Pinjaman]
        AL8[Cari Rekod Pinjaman]
        AL9[Buat Laporan Kerosakan]
    end
    
    Staff((Staff))
    AssetOfficer((Pegawai Aset))
    Admin((Pentadbir Sistem))
    
    Staff --> AL1
    Staff --> AL2
    Staff --> AL9
    AssetOfficer --> AL3
    AssetOfficer --> AL4
    AssetOfficer --> AL5
    AssetOfficer --> AL6
    AssetOfficer --> AL7
    AssetOfficer --> AL8
    Admin --> AL7
    Admin --> AL8

```

UC-ICT-IM: Pengurusan Inventori (Inventory Management)

```mermaid

graph TB
    subgraph "UC-ICT-IM: Pengurusan Inventori"
        IM1[Daftar Aset Baru]
        IM2[Kemaskini Maklumat Aset]
        IM3[Papar Senarai Inventori]
        IM4[Cari Aset]
        IM5[Hapus Rekod Aset]
        IM6[Tandakan Aset Rosak]
        IM7[Tandakan Aset Dilupuskan]
        IM8[Jana Kod QR Aset]
        IM9[Imbas Kod QR]
    end
    
    AssetOfficer((Pegawai Aset))
    Admin((Pentadbir Sistem))
    Staff((Staff))
    
    AssetOfficer --> IM1
    AssetOfficer --> IM2
    AssetOfficer --> IM3
    AssetOfficer --> IM4
    AssetOfficer --> IM5
    AssetOfficer --> IM6
    AssetOfficer --> IM7
    AssetOfficer --> IM8
    Admin --> IM1
    Admin --> IM2
    Admin --> IM5
    Staff --> IM3
    Staff --> IM4
    Staff --> IM9

```

UC-ICT-DL: Dashboard & Laporan

```mermaid

graph TB
    subgraph "UC-ICT-DL: Dashboard & Laporan"
        DL1[Paparan Dashboard Helpdesk]
        DL2[Paparan Dashboard Pinjaman]
        DL3[Paparan Dashboard Inventori]
        DL4[Jana Laporan Statistik]
        DL5[Cetak Laporan]
        DL6[Eksport Laporan]
        DL7[Papar Analitik AI]
    end
    
    Staff((Staff))
    Manager((Pengurus))
    Admin((Pentadbir Sistem))
    AI[Hybrid Cloud AI]
    
    Staff --> DL1
    Manager --> DL1
    Manager --> DL2
    Manager --> DL3
    Manager --> DL4
    Manager --> DL5
    Manager --> DL6
    Admin --> DL4
    Admin --> DL7
    DL7 -.include.-> AI

```

UC-ICT-PS: Pentadbiran Sistem (System Administration)

```mermaid

graph TB
    subgraph "UC-ICT-PS: Pentadbiran Sistem"
        PS1[Selenggara Kategori Tiket]
        PS2[Selenggara Keutamaan]
        PS3[Selenggara Status]
        PS4[Selenggara Jenis Aset]
        PS5[Selenggara Lokasi]
        PS6[Selenggara Jabatan]
        PS7[Selenggara Peranan Pengguna]
        PS8[Audit Log PDPA]
        PS9[Konfigurasi Hybrid AI]
    end
    
    Admin((Pentadbir Sistem))
    
    Admin --> PS1
    Admin --> PS2
    Admin --> PS3
    Admin --> PS4
    Admin --> PS5
    Admin --> PS6
    Admin --> PS7
    Admin --> PS8
    Admin --> PS9

```

Data Flow Diagrams

DFD-ICT-0: Context Diagram (Vertical Layout)

```mermaid

graph LR
    subgraph External
        Staff[Staff/Pengguna]
        Technician[Juruteknik ICT]
        AssetOfficer[Pegawai Aset]
        Manager[Pengurus]
        SSO[Single Sign-On System]
        AI[Hybrid Cloud AI<br/>Ollama + AWS Bedrock]
    end
    
    subgraph "0: Sistem ICTServe"
        ICTServe[Sistem Pengurusan<br/>Perkhidmatan ICT]
    end
    
    Staff -->|Permohonan Helpdesk/Pinjaman| ICTServe
    Staff -->|Maklumat Login| ICTServe
    ICTServe -->|Status Tiket/Pinjaman| Staff
    
    Technician -->|Kemaskini Status Tiket| ICTServe
    ICTServe -->|Senarai Tugasan| Technician
    
    AssetOfficer -->|Maklumat Aset/Kelulusan| ICTServe
    ICTServe -->|Laporan Inventori| AssetOfficer
    
    Manager -->|Permintaan Laporan| ICTServe
    ICTServe -->|Dashboard & Analitik| Manager
    
    SSO -->|Token Pengesahan| ICTServe
    ICTServe -->|Permintaan Autentikasi| SSO
    
    AI -->|Cadangan AI/Analisis| ICTServe
    ICTServe -->|Data untuk Pemprosesan| AI

```

DFD-ICT-PP: Level 1 - Pengurusan Pengguna

```mermaid

graph TB
    Staff[Staff/Pengguna]
    Admin[Pentadbir Sistem]
    SSO[SSO System]
    
    subgraph "1.0: Pengurusan Pengguna"
        P1[1.1<br/>Daftar<br/>Pengguna]
        P2[1.2<br/>Login<br/>Sistem]
        P3[1.3<br/>Kemaskini<br/>Profil]
        P4[1.4<br/>Lupa Kata<br/>Laluan]
    end
    
    D1[(D1: PENGGUNA)]
    D2[(D2: AUDIT_LOG)]
    
    Staff -->|Maklumat Pendaftaran| P1
    P1 -->|Simpan Rekod| D1
    P1 -->|Akaun Berjaya| Staff
    
    Staff -->|Kredensial Login| P2
    SSO -->|Token| P2
    P2 -->|Semak Pengguna| D1
    P2 -->|Rekod Audit| D2
    P2 -->|Akses Sistem| Staff
    
    Staff -->|Maklumat Kemaskini| P3
    P3 -->|Kemaskini Rekod| D1
    P3 -->|Rekod Audit| D2
    
    Staff -->|Permintaan Reset| P4
    P4 -->|Semak Email| D1
    P4 -->|Link Reset| Staff

```

DFD-ICT-HD: Level 1 - Pengurusan Helpdesk

```mermaid

graph TB
    Staff[Staff]
    Tech[Juruteknik]
    AI[Hybrid AI]
    
    subgraph "2.0: Pengurusan Helpdesk"
        H1[2.1<br/>Buat Tiket<br/>Helpdesk]
        H2[2.2<br/>Agih Tiket<br/>kepada Juruteknik]
        H3[2.3<br/>Proses<br/>Tiket]
        H4[2.4<br/>Tutup<br/>Tiket]
    end
    
    D3[(D3: TIKET_HELPDESK)]
    D4[(D4: MAKLUMBALAS)]
    D5[(D2: AUDIT_LOG)]
    
    Staff -->|Aduan/Permintaan| H1
    H1 -->|Analisis AI| AI
    AI -->|Cadangan Kategori/Keutamaan| H1
    H1 -->|Simpan Tiket| D3
    H1 -->|Notifikasi Tiket| Staff
    
    H1 -->|Tiket Baru| H2
    AI -->|Cadangan Agihan| H2
    H2 -->|Semak Tiket| D3
    H2 -->|Agih Tugasan| Tech
    H2 -->|Kemaskini Status| D3
    
    Tech -->|Tindakan Penyelesaian| H3
    H3 -->|Kemaskini Progress| D3
    H3 -->|Status Terkini| Staff
    H3 -->|Rekod Audit| D5
    
    Tech -->|Tutup Tiket| H4
    Staff -->|Maklumbalas| H4
    H4 -->|Simpan Maklumbalas| D4
    H4 -->|Kemaskini Status| D3
    H4 -->|Rekod Audit| D5

```

DFD-ICT-AL: Level 1 - Pengurusan Pinjaman Aset

```mermaid

graph TB
    Staff[Staff]
    Officer[Pegawai Aset]
    
    subgraph "3.0: Pengurusan Pinjaman Aset"
        A1[3.1<br/>Mohon<br/>Pinjaman]
        A2[3.2<br/>Proses<br/>Kelulusan]
        A3[3.3<br/>Rekod<br/>Penyerahan]
        A4[3.4<br/>Rekod<br/>Pemulangan]
    end
    
    D6[(D6: PINJAMAN_ASET)]
    D7[(D7: ASET)]
    D8[(D2: AUDIT_LOG)]
    
    Staff -->|Permohonan Pinjaman| A1
    A1 -->|Semak Ketersediaan| D7
    A1 -->|Simpan Permohonan| D6
    A1 -->|Notifikasi| Officer
    
    Officer -->|Keputusan Kelulusan| A2
    A2 -->|Kemaskini Status| D6
    A2 -->|Rekod Audit| D8
    A2 -->|Notifikasi Keputusan| Staff
    
    Officer -->|Maklumat Penyerahan| A3
    A3 -->|Kemaskini Status Aset| D7
    A3 -->|Kemaskini Pinjaman| D6
    A3 -->|Rekod Audit| D8
    
    Staff -->|Pulangan Aset| A4
    Officer -->|Pengesahan Pemulangan| A4
    A4 -->|Kemaskini Status Aset| D7
    A4 -->|Tutup Pinjaman| D6
    A4 -->|Rekod Audit| D8

```

DFD-ICT-IM: Level 1 - Pengurusan Inventori

```mermaid

graph TB
    Officer[Pegawai Aset]
    Admin[Pentadbir]
    Staff[Staff]
    
    subgraph "4.0: Pengurusan Inventori"
        I1[4.1<br/>Daftar<br/>Aset Baru]
        I2[4.2<br/>Kemaskini<br/>Maklumat Aset]
        I3[4.3<br/>Jana<br/>Kod QR]
        I4[4.4<br/>Urus Status<br/>Aset]
    end
    
    D7[(D7: ASET)]
    D9[(D9: JENIS_ASET)]
    D10[(D10: LOKASI)]
    D8[(D2: AUDIT_LOG)]
    
    Officer -->|Maklumat Aset Baru| I1
    I1 -->|Semak Jenis| D9
    I1 -->|Semak Lokasi| D10
    I1 -->|Simpan Aset| D7
    I1 -->|Rekod Audit| D8
    
    Officer -->|Kemaskini Data| I2
    Admin -->|Kemaskini Data| I2
    I2 -->|Kemaskini Rekod| D7
    I2 -->|Rekod Audit| D8
    
    Officer -->|Permintaan QR| I3
    I3 -->|Baca Data Aset| D7
    I3 -->|Kod QR| Officer
    
    Officer -->|Perubahan Status| I4
    I4 -->|Kemaskini Status| D7
    I4 -->|Rekod Audit| D8
    Staff -->|Imbas QR| I4
    I4 -->|Maklumat Aset| Staff

```

Entity Relationship Diagram (ERD)

```mermaid

erDiagram
    PENGGUNA ||--o{ TIKET_HELPDESK : "membuat"
    PENGGUNA ||--o{ PINJAMAN_ASET : "memohon"
    PENGGUNA ||--o{ MAKLUMBALAS : "memberi"
    PENGGUNA }o--|| PERANAN_PENGGUNA : "mempunyai"
    PENGGUNA }o--|| JABATAN : "berkhidmat"
    
    TIKET_HELPDESK }o--|| KATEGORI_TIKET : "dikategorikan"
    TIKET_HELPDESK }o--|| KEUTAMAAN : "mempunyai"
    TIKET_HELPDESK }o--|| STATUS_TIKET : "berstatus"
    TIKET_HELPDESK ||--o{ MAKLUMBALAS : "menerima"
    TIKET_HELPDESK }o--o| PENGGUNA : "diagihkan_kepada"
    
    PINJAMAN_ASET }o--|| ASET : "melibatkan"
    PINJAMAN_ASET }o--|| STATUS_PINJAMAN : "berstatus"
    PINJAMAN_ASET }o--o| PENGGUNA : "diluluskan_oleh"
    
    ASET }o--|| JENIS_ASET : "berjenis"
    ASET }o--|| LOKASI : "berada_di"
    ASET }o--|| STATUS_ASET : "berstatus"
    ASET ||--o{ PINJAMAN_ASET : "dipinjam"
    
    AUDIT_LOG }o--|| PENGGUNA : "direkod_oleh"
    
    PENGGUNA {
        int id_pengguna PK
        string nama_pengguna
        string email
        string kata_laluan
        string no_telefon
        int id_peranan FK
        int id_jabatan FK
        datetime tarikh_daftar
        boolean aktif
    }
    
    TIKET_HELPDESK {
        int id_tiket PK
        string no_rujukan
        int id_pemohon FK
        int id_kategori FK
        int id_keutamaan FK
        int id_status FK
        string tajuk
        text penerangan
        int id_juruteknik FK
        datetime tarikh_buka
        datetime tarikh_tutup
        text penyelesaian
    }
    
    PINJAMAN_ASET {
        int id_pinjaman PK
        string no_rujukan
        int id_pemohon FK
        int id_aset FK
        date tarikh_mula
        date tarikh_tamat
        int id_status FK
        text tujuan
        int id_pelulus FK
        datetime tarikh_lulus
        datetime tarikh_serah
        datetime tarikh_pulang
        text catatan
    }
    
    ASET {
        int id_aset PK
        string kod_aset
        string nama_aset
        int id_jenis FK
        string no_siri
        int id_lokasi FK
        int id_status FK
        date tarikh_beli
        decimal nilai
        text spesifikasi
        string kod_qr
    }
    
    MAKLUMBALAS {
        int id_maklumbalas PK
        int id_tiket FK
        int id_pengguna FK
        int rating
        text komen
        datetime tarikh_maklumbalas
    }
    
    KATEGORI_TIKET {
        int id_kategori PK
        string nama_kategori
        text penerangan
        boolean aktif
    }
    
    KEUTAMAAN {
        int id_keutamaan PK
        string nama_keutamaan
        int masa_tindakbalas_jam
        boolean aktif
    }
    
    STATUS_TIKET {
        int id_status PK
        string nama_status
        string kod_warna
        boolean aktif
    }
    
    STATUS_PINJAMAN {
        int id_status PK
        string nama_status
        string kod_warna
        boolean aktif
    }
    
    JENIS_ASET {
        int id_jenis PK
        string nama_jenis
        text penerangan
        boolean aktif
    }
    
    LOKASI {
        int id_lokasi PK
        string nama_lokasi
        string kod_lokasi
        text alamat
        boolean aktif
    }
    
    STATUS_ASET {
        int id_status PK
        string nama_status
        string kod_warna
        boolean aktif
    }
    
    PERANAN_PENGGUNA {
        int id_peranan PK
        string nama_peranan
        text penerangan
        boolean aktif
    }
    
    JABATAN {
        int id_jabatan PK
        string nama_jabatan
        string kod_jabatan
        boolean aktif
    }
    
    AUDIT_LOG {
        int id_log PK
        int id_pengguna FK
        string aktiviti
        string modul
        text data_lama
        text data_baru
        string ip_address
        datetime tarikh_masa
    }

```

Implementation Notes:
For your D03 document, you should:

Section 3 (Use Case Modeling) - Include all 6 use case diagrams above with descriptions for each use case
Section 4 (Information Modeling) - Include the ERD diagram with complete data dictionary tables
Section 5 (System Process Modeling) - Include the Context Diagram and all Level 1 DFDs

Additional diagrams you may need:

Function Hierarchy Diagram (SF-ICT) - Tree structure showing all modules and functions
Actor-Function Mapping Tables - Matrix showing which roles can access which functions