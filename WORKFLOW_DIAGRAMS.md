# Rajah Aliran Kerja (Workflow Diagrams) — ICTServe v3.6.1

**Versi sistem:** 3.6.1
**Tarikh:** 2025-12-29
**Nota:** Dokumen ini mengandungi rajah *workflow* (bukan swimlane) menggunakan Mermaid.

---

## Notasi

- Nod keputusan menggunakan bentuk berlian (contoh: `X{Soalan?}`)
- Aktiviti asinkron (queue/broadcast) ditanda dengan gaya `async`

```mermaid
flowchart TD
  classDef async stroke-dasharray: 5 5;
```

---

## 1) Workflow Sistem Menyeluruh (High-Level)

```mermaid
flowchart TD
  classDef async stroke-dasharray: 5 5;

  A([Mula]) --> B{Pengguna berlog masuk?}

  B -- Ya --> C[Staf Authenticated: My Dashboard / Borang Auto-fill]
  B -- Tidak --> D[Quick Access - Tetamu: Borang Manual]

  C --> E{Pilih modul}
  D --> E

  E -->|Helpdesk| F[Isi borang Helpdesk]
  E -->|Pinjaman Aset| G[Isi borang Pinjaman Aset - Wizard]
  E -->|AI Chat| H[Gunakan AI Chat /ai/chat]

  F --> I[Validasi + Lampiran + Jana ticket_number]
  I --> J[Generate status token - semakan status]
  J --> K[Dispatch notifikasi - e-mel + real-time]:::async

  G --> L[Semak ketersediaan aset - real-time]
  L --> M[Create permohonan + soft lock aset]
  M --> N[Mulakan kelulusan e-mel - signed URL + token]:::async

  H --> O[Analisis pertanyaan + klasifikasi sensitiviti]
  O --> P{Routing AI}
  P -->|Ollama - RAG| Q[Jawab FAQ - tempatan]
  P -->|Bedrock - cloud| R[DLP filters → Bedrock]
  P -->|Hibrid| S[Ollama - fakta + Bedrock - penaakulan]

  K --> T[Admin menerima notifikasi di Filament]:::async
  N --> U[Approver klik pautan: Lulus / Tolak]
  U --> V[Admin proses pengeluaran/pemulangan aset - Filament]

  T --> W([Tamat / Keadaan stabil])
  V --> W
  Q --> W
  R --> W
  S --> W
```

---

## 2) Workflow Helpdesk (Hybrid) — Cipta Tiket hingga Penutupan

Berdasarkan D04 §4.1 dan D03 §5.1.

```mermaid
flowchart TD
  classDef async stroke-dasharray: 5 5;

  A([Mula]) --> B[Pengguna akses /helpdesk/create]
  B --> C{Pengguna log masuk?}

  C -- Ya --> D[Auto-fill maklumat staf]
  C -- Tidak --> E[Input manual + reCAPTCHA - tetamu]

  D --> F[Isi butiran tiket + lampiran]
  E --> F

  F --> G[Validasi masa nyata - Livewire + validasi pelayan]
  G --> H{Validasi lulus?}
  H -- Tidak --> F

  H -- Ya --> I[HelpdeskService createTicket]
  I --> J[Jana ticket_number - HD-YYYYMM-XXXX]
  I --> K{Pengguna log masuk?}
  K -- Ya --> L[Simpan user_id + data submitter]
  K -- Tidak --> M[Simpan guest_*; user_id = NULL]

  L --> N[Upload lampiran - S3/MinIO + metadata]
  M --> N
  N --> O[Jana status token - SHA-512 hash]
  O --> P[Set SLA due date - berdasarkan priority]

  P --> Q[Hantar e-mel pengesahan + pautan semakan status]:::async
  P --> R[Notifikasi admin: e-mel + Reverb]:::async

  R --> S[Admin triage di Filament: assign / kemas kini status]
  S --> T[Tambah komen dalaman - tidak dipaparkan kepada tetamu]
  S --> U[Kemas kini status - In Progress / Awaiting Info / Resolved / Closed]

  U --> V[Hantar e-mel kemas kini kepada submitter_email]:::async
  U --> W{SLA breach?}
  W -- Ya --> X[Amaran SLA ke admin/superuser - Reverb + e-mel]:::async
  W -- Tidak --> Y[Teruskan pemprosesan]

  V --> Z([Tamat / Tiket stabil])
  X --> Z
  Y --> Z
```

---

## 3) Workflow Pinjaman Aset (Hybrid) — Permohonan hingga Pemulangan

Berdasarkan D04 §4.2 dan D03 §5.2.

```mermaid
flowchart TD
  classDef async stroke-dasharray: 5 5;

  A([Mula]) --> B[Pengguna akses /loan/create]
  B --> C{Pengguna log masuk?}

  C -- Ya --> D[Auto-fill pemohon]
  C -- Tidak --> E[Input manual pemohon]

  D --> F[Wizard: pilih aset + tarikh + tujuan + lokasi]
  E --> F

  F --> G[Semak ketersediaan aset - real-time]
  G --> H{Konflik tarikh / aset tidak tersedia?}
  H -- Ya --> F
  H -- Tidak --> I[LoanService createApplication]

  I --> J[Jana rujukan - LA-YYYYMM-XXXX]
  I --> K{Pengguna log masuk?}
  K -- Ya --> L[Simpan user_id + applicant data]
  K -- Tidak --> M[Simpan applicant_*; user_id = NULL]

  L --> N[Set status: PENDING_SUPERVISOR_APPROVAL]
  M --> N
  N --> O[Soft lock / reserve aset]

  O --> P[ApprovalService initiateApproval]
  P --> Q[Jana signed URL + token hash - SHA-512, tamat tempoh 72 jam]
  Q --> R[Queue e-mel kelulusan kepada approver]:::async

  R --> S[Approver klik pautan]
  S --> T{Signed URL + token sah?}
  T -- Tidak --> U[Papar ralat / token luput]
  T -- Ya --> V[Papar ringkasan permohonan]

  V --> W{Keputusan approver}
  W -->|Lulus| X[Rekod APPROVED + metadata]
  W -->|Tolak| Y[Rekod REJECTED + metadata]

  X --> Z[LoanService progressWorkflow: AWAITING_COLLECTION] 
  Y --> AA[Lepaskan reservation aset]

  Z --> AB[Admin keluarkan aset - Filament: check-out + resit]
  AB --> AC[Status: ON_LOAN]

  AC --> AD[Perkakasan dipulang - admin: check-in + semak kondisi]
  AD --> AE{Rosak?}
  AE -- Ya --> AF[Status: DAMAGED + auto-create tiket maintenance]:::async
  AE -- Tidak --> AG[Status: RETURNED]

  AF --> AH([Tamat / Pinjaman selesai])
  AG --> AH
  U --> AH
```

---

## 4) Workflow Kelulusan Pautan E-mel (Signed Approval Link)

Fokus pada token bertandatangan dan audit metadata (D04 §4.2, D03 SRS-LOAN-004 hingga SRS-LOAN-006).

```mermaid
flowchart TD
  classDef async stroke-dasharray: 5 5;

  A([Mula]) --> B[Admin/Service mulakan kelulusan]
  B --> C[Kenal pasti approver email]
  C --> D[Jana token raw]
  D --> E[Hash token - SHA-512 + simpan dalam loan_approvals]
  E --> F[Jana signed URL - HMAC + expiry]
  F --> G[Queue e-mel kepada approver]:::async

  G --> H[Approver buka e-mel dan klik pautan]
  H --> I{Signed URL sah?}
  I -- Tidak --> J[Henti: pautan tidak sah]

  I -- Ya --> K{Token luput?}
  K -- Ya --> L[Henti: token luput - minta regen]

  K -- Tidak --> M[Papar ringkasan permohonan + pilihan keputusan]
  M --> N{Approve / Reject}

  N -->|Approve| O[Rekod APPROVED + IP hash + timestamp + user-agent]
  N -->|Reject| P[Rekod REJECTED + remarks + metadata]

  O --> Q[Trigger post-approval actions - status, transaksi]:::async
  P --> Q

  Q --> R([Tamat])
  J --> R
  L --> R
```

---

## 5) Workflow Notifikasi Masa Nyata (Reverb/Echo)

Berdasarkan D16 (strategi saluran dwi + aliran penyiaran).

```mermaid
flowchart TD
  classDef async stroke-dasharray: 5 5;

  A([Mula]) --> B[Perubahan berlaku - tiket/pinjaman/AI]
  B --> C[Event dispatched - ShouldBroadcast]
  C --> D[Job penyiaran masuk ke Redis queue]:::async
  D --> E[Queue worker proses job]:::async
  E --> F[Reverb hantar mesej ke saluran]:::async

  F --> G{Jenis saluran}
  G -->|Authenticated| H[private user dengan id]
  G -->|Guest Ticket| I[private ticket dengan uuid]
  G -->|Guest Loan| J[private loan dengan uuid]
  G -->|Admin/AIs| K[private ai atau admin.notifications]

  H --> L[Echo terima event → kemas kini UI]
  I --> L
  J --> L
  K --> L

  L --> M([Tamat])
```

---

## 6) Workflow Queue & Pemantauan (Redis + Horizon)

Berdasarkan D17.

```mermaid
flowchart TD
  classDef async stroke-dasharray: 5 5;

  A([Mula]) --> B[Service dispatch job / queue mail]
  B --> C{Queue sasaran}

  C -->|notifications| D[Redis: notifications]:::async
  C -->|emails| E[Redis: emails]:::async
  C -->|digests| F[Redis: digests]:::async
  C -->|documents| G[Redis: documents]:::async
  C -->|embeddings| H[Redis: embeddings]:::async
  C -->|auto-reply| I[Redis: auto-reply]:::async
  C -->|default| J[Redis: default]:::async

  D --> K[Worker - fast proses]:::async
  E --> K
  F --> K

  G --> L[Worker - long proses]:::async
  H --> L
  I --> L

  J --> K

  K --> M{Berjaya?}
  L --> N{Berjaya?}

  M -- Ya --> O[Log/metrics: Pulse/Horizon]
  N -- Ya --> O

  M -- Tidak --> P[Retry ikut tries/backoff]:::async
  N -- Tidak --> P

  P --> Q{Masih gagal selepas retry?}
  Q -- Ya --> R[Rekod ke failed_jobs + stack trace]
  R --> S[Alert admin - threshold + FailedJobs Resource]:::async
  Q -- Tidak --> T[Kembali diproses]

  O --> U([Tamat])
  S --> U
  T --> U
```

---

## 7) Workflow Pendaftaran Staff & Pengesahan E-mel

Berdasarkan D04 §4.6.

```mermaid
flowchart TD
  classDef async stroke-dasharray: 5 5;

  A([Mula]) --> B[Staff akses /register]
  B --> C[Isi nama, e-mel motac.gov.my, kata laluan, profil]
  C --> D[Semak domain e-mel + pendua akaun]
  D --> E{Valid?}
  E -- Tidak --> C

  E -- Ya --> F[Create user - email_verified_at = NULL, role=staff, locale=ms]
  F --> G[Jana signed URL pengesahan e-mel - 24 jam]
  G --> H[Queue e-mel pengesahan]:::async

  H --> I[Staff klik pautan pengesahan]
  I --> J{Signed URL sah dan belum luput?}
  J -- Tidak --> K[Pengesahan gagal / luput]
  J -- Ya --> L[Set email_verified_at + log aktiviti]
  L --> M[Redirect ke login]

  K --> N([Tamat])
  M --> N
```

---

## 8) Workflow Log Masuk Fleksibel (E-mel/Nama Pengguna)

Berdasarkan D04 §4.7.

```mermaid
flowchart TD
  A([Mula]) --> B[Staff akses /login]
  B --> C[Input: e-mel penuh atau nama pengguna pendek]
  C --> D{Input mengandungi simbol @?}
  D -- Ya --> E[Anggap sebagai e-mel]
  D -- Tidak --> F[Append domain motac.gov.my]

  E --> G[Attempt authenticate]
  F --> G

  G --> H{Berjaya?}
  H -- Tidak --> I[Rate limit / lockout + mesej generik]
  H -- Ya --> J[Regenerate session + log aktiviti]
  J --> K[Redirect ke dashboard atau intended]

  I --> L([Tamat])
  K --> L
```

---

## 9) Workflow AI Chat (Cloud Hybrid) — Ollama + AWS Bedrock

Berdasarkan D18 (routing + DLP + web augmentation + conversation management).

```mermaid
flowchart TD
  classDef async stroke-dasharray: 5 5;

  A([Mula]) --> B[Pengguna buka /ai/chat]
  B --> C[Input pertanyaan]
  C --> D[Analisis niat + kompleksiti + konteks perbualan]
  D --> E[Klasifikasi sensitiviti data - data residency]

  E --> F{Perlu pemprosesan awan?}
  F -- Tidak --> G[Ollama - tempatan: RAG + FAQ]

  F -- Ya --> H[DLP filters wajib - buang/sanitasi PII]
  H --> I{DLP lulus?}
  I -- Tidak --> J[Paksa pemprosesan tempatan - Ollama / tolak permintaan]

  I -- Ya --> K{Mod jawapan}
  K -->|Complex Reasoning| L[AWS Bedrock - Claude — jawab]
  K -->|Hibrid| M[Ollama - fakta + Bedrock - penaakulan]

  D --> N{Toggle carian web diaktifkan?}
  N -- Ya --> O[WebSearchService - DuckDuckGo]:::async
  O --> P[Augment prompt dengan konteks web]
  P --> K
  N -- Tidak --> K

  G --> Q[Simpan perbualan - conversation management]
  J --> Q
  L --> Q
  M --> Q

  Q --> R[Opsyen: broadcast status AI ke admin - Reverb]:::async
  R --> S([Tamat])
```
