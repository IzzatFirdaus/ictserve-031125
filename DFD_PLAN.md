# Pelan Terperinci DFD ICTServe v3.6.1 (Bahasa Melayu)

## 1. Objektif
Dokumen ini menerangkan pelan kerja untuk menghasilkan **Data Flow Diagram (DFD)** yang:

- **Terperinci** (Level 0 → Level 1 → Level 2)
- **Konsisten dengan dokumentasi v3.6.1** (KRISA + dokumen D00–D18)
- **Sejajar dengan pelaksanaan sistem** (konfigurasi, modul, dan aliran data operasi)

Hasil akhir:

- Fail **DFD_DIAGRAMS.md** yang mengandungi beberapa rajah Mermaid (format DFD) dengan label Bahasa Melayu.

---

## 2. Skop DFD (Apa yang akan dimodelkan)
DFD memfokuskan **aliran data** antara:

- **Entiti luaran** (pengguna/peranan, sistem e-mel, AI, reCAPTCHA, dsb.)
- **Proses dalaman** (Helpdesk, Pinjaman Aset, Inventori, Pengguna/Auth, Pentadbiran)
- **Data store** (MySQL tables/collections, storan lampiran, audit/log)

### 2.1 Modul dalam skop (selari v3.6.1)
- **Pengurusan Pengguna & Akses** (Auth/SSO opsyen, sesi, profil)
- **Pengurusan Helpdesk** (tiket, komen, lampiran, SLA, tugasan)
- **Pengurusan Pinjaman Aset** (permohonan, kelulusan, transaksi check-out/in)
- **Pengurusan Inventori Aset** (aset, kategori, transaksi, QR)
- **Notifikasi & Komunikasi**
  - E-mel (SMTP) 
  - Penyiaran masa nyata (Laravel Reverb + Echo)
  - Jobs/queue (Redis + Horizon)
- **AI / RAG / Auto-reply** (Ollama + AWS Bedrock, ingestion dokumen, embeddings, log perbualan)
- **Pentadbiran & Pemantauan** (Filament `/admin`, Horizon `/horizon`, audit & log)

### 2.2 Di luar skop (untuk elak DFD terlalu luas)
- Reka bentuk UI/UX terperinci (dibuat dalam dokumen workflow/swimlane)
- Konfigurasi infrastruktur low-level (load balancer, firewall, dll.)
- DFD integrasi data antara sistem kerajaan yang memerlukan kontrak API lengkap (akan dirujuk ringkas sahaja)

---

## 3. Sumber Rujukan & “Source of Truth”
Keutamaan sumber (mengikut kebolehpercayaan untuk DFD v3.6.1):

1. **KRISA D03 (Spesifikasi Keperluan Sistem)** — seksyen DFD + notasi + DFD Level 0/1.
2. **Dokumen D08 (Integrasi Sistem)** — skop API, kawalan keselamatan integrasi, dan sempadan sistem.
3. **Dokumen D16 (Broadcasting/Reverb)** — aliran real-time event, saluran, dan autorisasi.
4. **Dokumen D17 (Queue/Horizon)** — aliran job queue, senarai job/queue, dan pemantauan.
5. **Dokumen D18 (AI Cloud Hybrid)** — aliran data AI (routing, RAG, logging, events).
6. **Dokumen D09 (DB)** + **migrations** — untuk menamakan data store dengan istilah domain yang tepat.

### 3.1 Polisi apabila berlaku percanggahan versi
Dalam repo, sesetengah dokumen ringkasan boleh mengandungi naratif versi seterusnya (contoh: v4.0.0). Untuk tugasan ini:

- **Skop sasaran kekal v3.6.1.**
- Jika ada perbezaan naratif (contoh: SSO-only vs hybrid), DFD akan:
  - memaparkan **aliran v3.6.1** (mengikut D03/D08/D16/D17/D18), dan
  - menambah **nota ringkas** bahawa deployment tertentu boleh menutup aliran tetamu jika polisi organisasi memerlukan.

---

## 4. Notasi & Konvensyen (Mermaid untuk DFD)
Rujukan notasi KRISA (D03):

- **External Entity**: Rectangle
- **Process**: Circle/Rounded Rectangle
- **Data Store**: Open Rectangle
- **Data Flow**: Arrow

### 4.1 Konvensyen penamaan & penomboran
Agar konsisten dengan KRISA dan rujukan sedia ada:

- **DFD-ICT-0**: Context Diagram
- **DFD-ICT-L0**: Level 0 decomposition (proses 1.0–8.0)
- **DFD-ICT-PP**: Level 1 – Pengurusan Pengguna
- **DFD-ICT-HD**: Level 1 – Pengurusan Helpdesk
- **DFD-ICT-AL**: Level 1 – Pengurusan Pinjaman Aset
- **DFD-ICT-IM**: Level 1 – Pengurusan Inventori
- **DFD-ICT-HD-2**: Level 2 – pecahan proses Helpdesk (tiket + SLA + lampiran + notifikasi)
- **DFD-ICT-AL-2**: Level 2 – pecahan proses Pinjaman Aset (permohonan + kelulusan + check-out/in)
- **DFD-ICT-AI-2**: Level 2 – pecahan proses AI/RAG (chat, ingestion, embeddings, auto-reply)

### 4.2 “Balancing rule” (WAJIB)
- Semua input/output pada **Context** mesti “terjaga” apabila dipecah ke **Level 0**.
- Semua input/output proses **Level 0** mesti “terjaga” apabila dipecah ke **Level 1**.
- Semua input/output proses **Level 1** mesti “terjaga” apabila dipecah ke **Level 2**.

---

## 5. Inventori Komponen DFD (Senarai awal)
Senarai ini menjadi “kamus” untuk memastikan label konsisten antara rajah.

### 5.1 Entiti luaran (contoh utama)
- **Staf MOTAC (Authenticated)**
- **Staf/Tetamu (Token / tanpa log masuk)** — jika diaktifkan dalam v3.6.1
- **Admin BPM / Juruteknik / Pegawai Aset / Superuser**
- **Pegawai Kelulusan** (melalui pautan kelulusan e-mel)
- **Sistem E-mel (SMTP)**
- **reCAPTCHA** (untuk borang tetamu jika diaktifkan)
- **Storan Objek** (S3/MinIO) untuk lampiran
- **Redis** (queue/cache/broadcast backend)
- **Laravel Reverb** (WebSocket server)
- **Hybrid AI**: Ollama (tempatan) + AWS Bedrock (cloud)
- **SSO/OAuth (opsyen)**: Google Workspace / SSO provider

### 5.2 Data store (kategori)
- **DS_USERS**: `users`, `sessions`, `password_reset_tokens`, RBAC tables
- **DS_HELPDESK**: tiket, komen, lampiran, kategori, SLA-related
- **DS_LOANS**: permohonan, item, kelulusan, token, transaksi
- **DS_INVENTORY**: aset, kategori aset, transaksi aset
- **DS_AUDIT_LOGS**: `audits`, `activity_log`, `notifications`, `email_logs`
- **DS_AI**: `faqs`, `documents`, `document_chunks`, `message_logs`, `bedrock_conversations`, `guest_conversations`, `auto_reply_*`

---

## 6. Langkah Kerja (Extensively Plan)
### 6.1 Pengumpulan input (berdasarkan dokumen v3.6.1)
- Ekstrak sempadan sistem & entiti luaran (D03 + D04 + D08)
- Ekstrak aliran notifikasi & real-time (D16)
- Ekstrak aliran queue jobs & queue name (D17)
- Ekstrak aliran AI/RAG/auto-reply (D18)
- Petakan data store kepada domain table (D09 + migrations)

### 6.2 Pembinaan rajah (iteratif)
1. Bina **Context Diagram (DFD-ICT-0)**
2. Pecahkan ke **Level 0 decomposition (DFD-ICT-L0)**
3. Pecahkan ke **Level 1** mengikut domain (PP/HD/AL/IM)
4. Tambah **Level 2** untuk proses paling kritikal:
   - Helpdesk (HD-2)
   - Pinjaman Aset (AL-2)
   - AI/RAG (AI-2)

### 6.3 Validasi (balancing + konsistensi istilah)
- Semak “balancing rule” setiap aras
- Semak istilah peranan dan data store konsisten
- Semak aliran yang sensitif (AI cloud, token approvals, email links) dinyatakan secara ringkas tetapi jelas

### 6.4 Kemas akhir & penerbitan
- Susun rajah dari umum → spesifik
- Tambah ringkasan di bawah setiap rajah: input, output, data store terlibat
- Pastikan semua Mermaid boleh dirender (tiada ID node tidak sah)

---

## 7. Checklist Siap (WAJIB)
- Semua rajah penting wujud: Context, Level 0, Level 1 (PP/HD/AL/IM), Level 2 (HD/AL/AI)
- Semua aliran e-mel/queue/reverb diwakili sekurang-kurangnya pada Level 0/Level 2
- Notasi KRISA dipatuhi (entiti/proses/data store/aliran)
- Percanggahan naratif versi (jika ada) dicatat sebagai nota ringkas tanpa mengubah skop v3.6.1

---

## 8. Output
- **DFD_DIAGRAMS.md** — koleksi rajah DFD (Mermaid) ICTServe v3.6.1 dalam Bahasa Melayu.
