# Gambarajah Aliran “Manual → Sistem” (ICTServe v3.6.1)

Dokumen ini memaparkan set **Mermaid diagram** yang memetakan aliran **manual (borang/prosedur)** kepada **tindak balas sistem ICTServe**.

## Legend (Ringkas)

- **Tetamu**: pengguna tanpa log masuk (D17).
- **Staf/Pentadbir**: pengguna berdaftar / admin (D17).
- **Aplikasi ICTServe**: Laravel (UI + backend).
- **DB**: MySQL.
- **Queue**: Redis + Laravel Queue/Horizon (D17 queue doc).

---

## 1) Gambaran Keseluruhan: Manual → Sistem

```mermaid
flowchart LR
    subgraph Manual[Manual / Borang / Prosedur]
        M1[Isi borang manual\n- helpdesk/pinjaman]
        M2[Hantar borang\nsecara manual / intranet lama]
        M3[Dapat nombor rujukan\natau semak status secara manual]
    end

    subgraph System[ICTServe - Sistem]
        S1[Buka borang web\n/helpdesk/create atau /loan/create]
        S2[Isi & hantar borang\nPOST /helpdesk/submit atau submit wizard]
        S3[Validasi input\n- rate limit + rules]
        S4[Simpan rekod\n- MySQL]
        S5[Jana ID rujukan\nNombor tiket / nombor permohonan + token]
        S6[Paparan kejayaan\n/helpdesk/success atau /loan/success]
        S7[Notifikasi\n- queue: emails/notifications]
        S8[Semak status sendiri\n/helpdesk/track /loan/tracking /status]
        S9[Operasi admin\n/admin dan /horizon]
    end

    M1 --> M2 --> M3

    M1 -. ditransformasikan kepada .-> S1
    S1 --> S2 --> S3 --> S4 --> S5 --> S6
    S5 --> S8
    S4 --> S7
    S7 --> S9
```

---

## 2) Helpdesk (Tetamu): Cipta Tiket (Manual Borang → Sistem)
Rujukan laluan: D17 Manual Pengguna.
Rujukan medan borang manual: `_reference/ICT DAMAGE COMPLAINT FORM (ServiceDesk ICT) - DETAILED BREAKDOWN.txt`.

```mermaid
sequenceDiagram
    autonumber
    actor U as Tetamu
    participant B as Pelayar
    participant A as ICTServe - Laravel
    participant DB as MySQL
    participant Q as Queue - Redis
    participant N as Notifikasi/Emel - Job

    U->>B: Buka /helpdesk/create
    B->>A: GET /helpdesk/create
    A-->>B: Papar borang tiket - tetamu

    Note over U,B: Isi medan - rujukan borang manual:<br/>Nama Penuh, Bahagian, Gred Jawatan (opsyen),<br/>E-mel, No Telefon, Jenis Kerosakan, Maklumat, Perakuan

    U->>B: Hantar borang
    B->>A: POST /helpdesk/submit
    A->>A: Middleware keselamatan\n- cth: rate limit tetamu
    A->>A: Validasi input

    alt Validasi gagal
        A-->>B: 422 + mesej ralat pada borang
    else Validasi lulus
        A->>DB: INSERT helpdesk_ticket
        DB-->>A: OK - id
        A->>A: Jana nombor tiket + token rujukan
        A->>Q: Dispatch SendTicketNotification - notifications
        Q-->>N: Worker proses job
        N-->>U: Hantar notifikasi/emel - asinkron
        A-->>B: Redirect /helpdesk/success\n- dengan nombor tiket
        B-->>U: Papar kejayaan + nombor tiket
    end
```

---

## 3) Helpdesk (Tetamu): Jejak Tiket
Rujukan laluan: D17 Manual Pengguna (`/helpdesk/track/{ticketNumber?}`).

```mermaid
flowchart TD
    U[Tetamu] --> P[Halaman /helpdesk/track]
    P --> I[Masukkan nombor tiket\natau guna URL /helpdesk/track/ticketNumber]
    I --> V[Validasi format nombor tiket]

    V -->|Tidak sah| E[Paparan ralat\nNombor tiket tidak sah]
    V -->|Sah| Q[Query tiket dalam DB]

    Q -->|Tidak jumpa| NF[Paparan\nTiket tidak ditemui]
    Q -->|Jumpa| S[Paparan status tiket\n- cth: diterima/dalam tindakan/selesai]

    S --> X[Tamat]
    E --> X
    NF --> X
```

---

## 4) Pinjaman Aset ICT (Tetamu): Permohonan Wizard (Manual Borang → Sistem)
Rujukan laluan: D17 Manual Pengguna (`/loan/create`, alias `/loan/apply`, legacy `/loan/create-legacy`).
Rujukan medan/peranan manual: `_reference/ICT EQUIPMENT LOAN APPLICATION FORM - DETAILED BREAKDOWN.txt`.

```mermaid
sequenceDiagram
    autonumber
    actor U as Tetamu
    participant B as Pelayar
    participant A as ICTServe - Laravel
    participant DB as MySQL
    participant Q as Queue - Redis
    participant Mail as Emel Kelulusan/Notifikasi

    U->>B: Buka /loan/create
    B->>A: GET /loan/create
    A-->>B: Papar wizard - 3 langkah

    Note over U,B: Langkah wizard lazim: 1 Maklumat pemohon,<br/>2 Maklumat pegawai bertanggungjawab, 3 Peralatan + pengesahan

    U->>B: Hantar permohonan - submit wizard
    B->>A: POST - submit /loan/create - aliran wizard
    A->>A: Validasi input

    alt Validasi gagal
        A-->>B: Papar ralat pada langkah berkaitan
    else Validasi lulus
        A->>DB: INSERT loan_application + item peralatan
        DB-->>A: OK - id
        A->>A: Jana nombor permohonan + token

        opt Perlu sokongan/kelulusan - rujukan borang: Gred 41+
            A->>Q: Dispatch SendApprovalRequest - emails
            Q-->>Mail: Worker hantar emel kelulusan - signed URL/token
        end

        A->>Q: Dispatch SendLoanNotification - notifications
        A-->>B: Redirect ke halaman kejayaan\n- cth: /loan/success
        B-->>U: Papar nombor permohonan/token
    end
```

---

## 5) Pinjaman Aset ICT: Jejak Permohonan
Rujukan laluan: D17 Manual Pengguna (`/loan/tracking/{applicationNumber?}`).

```mermaid
flowchart TD
    U[Tetamu] --> P[Halaman /loan/tracking]
    P --> I[Masukkan nombor permohonan\natau guna URL /loan/tracking/applicationNumber]
    I --> V[Validasi format nombor permohonan]

    V -->|Tidak sah| E[Paparan ralat\nNombor permohonan tidak sah]
    V -->|Sah| Q[Query permohonan dalam DB]

    Q -->|Tidak jumpa| NF[Paparan\nPermohonan tidak ditemui]
    Q -->|Jumpa| S[Paparan status permohonan\n- cth: dalam semakan/disokong/diluluskan/ditolak/siap]

    S --> X[Tamat]
    E --> X
    NF --> X
```

---

## 6) Semakan Status Bersepadu (Token)
Rujukan laluan: D17 Manual Pengguna (`/status` atau `/status/{token}`).

```mermaid
flowchart TD
    U[Pengguna] --> P[Halaman /status\natau /status/token]
    P --> T[Ambil token\n- dari URL atau input]
    T --> V[Validasi token]

    V -->|Tidak sah| E[Paparan ralat\nToken tidak sah]
    V -->|Sah| R[Padankan token\n- tiket atau pinjaman]

    R -->|Tiket| HT[Query helpdesk_tickets]
    R -->|Pinjaman| LA[Query loan_applications]

    HT -->|Jumpa| HS[Paparan status tiket]
    HT -->|Tidak jumpa| HNF[Paparan\nRekod tiket tidak ditemui]

    LA -->|Jumpa| LS[Paparan status pinjaman]
    LA -->|Tidak jumpa| LNF[Paparan\nRekod pinjaman tidak ditemui]

    HS --> X[Tamat]
    HNF --> X
    LS --> X
    LNF --> X
    E --> X
```

---

## 7) Staf / Pentadbir / Operasi: Pengurusan & Pemantauan
Rujukan laluan:

- D17 Manual Pengguna (modul staf): `/staff/dashboard`, `/staff/tickets`, `/staff/loans`
- D17 Manual Pentadbir: `/admin` dan `/horizon` (gate `viewHorizon`)
- D17 Queue doc: job notifikasi & emel diproses melalui queue.

```mermaid
flowchart LR
    subgraph Staff[Staf - berdaftar]
        SD[/staff/dashboard/]
        ST[/staff/tickets/]
        SL[/staff/loans/]
    end

    subgraph Admin[Pentadbir]
        FA[/admin - Filament/]
        HX[/horizon - Queue Monitor/]
    end

    subgraph Core[ICTServe Core]
        DB[(MySQL)]
        R[(Redis Queue)]
        J[Jobs\nnotifications/emails]
    end

    SD --> ST --> DB
    SD --> SL --> DB

    FA --> DB
    HX --> R

    DB --> J --> R
    R --> J

    HX -.-> INFO[Gate viewHorizon:\nLocal: allow\nNon-local: admin/superuser]
    style INFO fill:#f9f,stroke:#333,stroke-width:2px
```
