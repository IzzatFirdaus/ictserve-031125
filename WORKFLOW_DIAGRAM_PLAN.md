# Pelan Rajah Aliran Kerja (Workflow Diagram Plan) — ICTServe v3.6.1

**Sistem:** ICTServe
**Versi:** 3.6.1
**Tarikh kemaskini pelan:** 2025-12-29
**Bahasa output:** Bahasa Melayu (utama), istilah teknikal dikekalkan bila perlu
**Skop:** Rajah *workflow* (bukan swimlane)

---

## 1. Tujuan

Dokumen ini menerangkan perancangan menyeluruh untuk menghasilkan set rajah **aliran kerja (workflow)** bagi ICTServe v3.6.1.

Objektif utama:

- Memodelkan aliran proses end-to-end untuk modul utama (Helpdesk, Pinjaman Aset) dalam seni bina **Hybrid Access Model**.
- Memaparkan titik keputusan penting (Auth vs Guest, kelulusan, SLA, konflik tarikh, status token, retry/fail) serta interaksi asinkron (queue, e-mel, Reverb/WebSocket).
- Meliputi integrasi **Cloud Hybrid AI Architecture** (Ollama + AWS Bedrock) termasuk klasifikasi data, DLP, web augmentation, dan pengurusan perbualan.

---

## 2. Ruang Lingkup

### 2.1 Dalam skop

- Workflow “Hybrid Access”:
  - Staff berlog masuk (Laravel Breeze) untuk My Dashboard / auto-fill / user_id linking.
  - Staff sebagai tetamu (quick access) untuk borang tanpa log masuk (user_id = NULL).
- Helpdesk:
  - Cipta tiket, lampiran, token semakan status, notifikasi e-mel, triage admin, kemas kini status, SLA/eskalasi.
- Pinjaman Aset:
  - Permohonan bertahap, semakan ketersediaan, soft lock, kelulusan melalui pautan e-mel bertandatangan, pengeluaran/pemulangan aset, tiket maintenance automatik jika rosak.
- Infrastruktur proses latar:
  - Queue + Redis + Horizon: dispatch, worker, retry, failed_jobs, pemantauan/alert.
- Real-time:
  - Laravel Reverb + Echo: strategi saluran peribadi (user.{id} vs ticket.{uuid}/loan.{uuid}), event kritikal.
- AI:
  - Model routing, klasifikasi residensi data, DLP filters (sebelum cloud), web augmentation (DuckDuckGo), pengurusan perbualan, dan event real-time AI.

### 2.2 Di luar skop

- Swimlane / pemisahan peranan sebagai “lane”.
- Rajah UI/UX skrin demi skrin.
- Integrasi LDAP/SSO penuh (melainkan dinyatakan dalam keperluan semasa sebagai opsyen; workflow ini fokus pada aliran yang didokumenkan).

---

## 3. Sumber Rujukan (Dokumen v3.6.1)

Sumber utama untuk kandungan workflow:

- D00: Gambaran sistem dan modul, termasuk ringkasan integrasi AI v3.6.1.
- D03: Keperluan fungsional — Helpdesk, Loan, admin, audit, notifikasi, real-time, API/SSO (future-ready).
- D04: Reka bentuk aliran kerja terperinci:
  - Helpdesk Hybrid Flow
  - Loan Hybrid + Token-Based Approval
  - Self-registration, flexible login
- D16: Aliran kerja broadcasting (Reverb/Echo), saluran peribadi, event sedia ada dan event AI.
- D17: Aliran kerja queue (jobs, queue names, worker cadangan, Horizon, failed jobs).
- D18: Aliran kerja AI hibrid (routing, DLP, web search toggle, conversation management, job processing).

---

## 4. Prinsip Pemodelan

### 4.1 Prinsip asas

- Fokus kepada **proses** dan **state/decision**, bukan peranan sebagai lane.
- Setiap workflow:
  - Bermula dengan “Mula” dan tamat dengan “Tamat / Keadaan stabil”.
  - Mempunyai simpul keputusan untuk cabang kritikal (contoh: Auth::check(), token sah, kelulusan, konflik tarikh).
  - Menandakan aktiviti asinkron (queue job) secara eksplisit.

### 4.2 Notasi yang digunakan

Dokumen rajah akan menggunakan Mermaid `flowchart` (utama), dan jika perlu:

- `stateDiagram-v2` untuk *state transition* (contoh status tiket/pinjaman).

Konvensyen label:

- Gunakan Bahasa Melayu untuk label proses.
- Kekalkan istilah teknikal sebagai *proper noun* bila perlu: `Auth::check()`, `user_id`, Redis, Horizon, Reverb, SSE, DLP, RAG, Ollama, AWS Bedrock.

### 4.3 Penandaan asinkron / integrasi

Dalam Mermaid, aktiviti berikut akan ditanda sebagai “asinkron”:

- `dispatch()` job / e-mel berbaris gilir
- event broadcasting melalui Reverb
- pemprosesan AI (ingestion, embeddings, auto-reply)

---

## 5. Set Rajah Yang Akan Dihasilkan

> Nota: Set ini direka untuk liputan end-to-end, tetapi setiap rajah kekal fokus kepada satu aliran utama.

1. **Workflow Sistem Menyeluruh (High-Level)**
   - Gambaran pintu masuk: Guest/Staff/Auth/Admin → Helpdesk/Loan/AI → notifikasi/queue/real-time.

2. **Workflow Helpdesk (Hybrid) — Cipta Tiket hingga Penutupan**
   - Validasi, lampiran, token status, notifikasi, triage admin, SLA.

3. **Workflow Pinjaman Aset (Hybrid) — Permohonan hingga Pemulangan**
   - Wizard, semakan ketersediaan, soft lock, approval e-mel bertoken, checkout/checkin, rosak→tiket.

4. **Workflow Kelulusan Pautan E-mel (Signed Approval Link)**
   - Generate token + signed URL, expiry, tindakan approver, audit metadata.

5. **Workflow Notifikasi Masa Nyata (Reverb/Echo)**
   - Dispatch event → queue → Reverb → Echo → kemas kini UI.

6. **Workflow Queue & Pemantauan (Redis + Horizon)**
   - Dispatch job → worker → retry/backoff → failed_jobs → alert/monitoring.

7. **Workflow Pendaftaran Staff & Pengesahan E-mel**
   - Register → valid domain → create user → verify email (signed URL) → akses.

8. **Workflow Log Masuk Fleksibel (E-mel/Nama Pengguna)**
   - Input → normalisasi → authenticate → rate limit/lockout → redirect.

9. **Workflow AI Chat (Cloud Hybrid)**
   - Input pengguna → klasifikasi sensitiviti → routing (Ollama/Bedrock/Hybrid) → DLP sebelum cloud → (opsyen) web search → respons → simpan perbualan.

---

## 6. Kriteria Penerimaan (Acceptance Criteria)

- Semua rajah:
  - Menggunakan Bahasa Melayu untuk label utama.
  - Tidak menggunakan swimlane.
  - Menyertakan titik keputusan yang selaras dengan dokumen (Auth::check(), token, expiry, approval, konflik).
  - Menandakan aktiviti asinkron (queue/broadcast).
- Liputan minimum mesti merangkumi Helpdesk, Loan, Approval link, Queue/Horizon, Reverb/Echo, dan AI routing.

---

## 7. Semakan Ketepatan (Validation Checklist)

- Selaras dengan SRS (D03) bagi aliran utama.
- Selaras dengan SDD (D04) bagi langkah terperinci dan komponen yang dinamakan.
- Selaras dengan D16 untuk event + channel strategy.
- Selaras dengan D17 untuk queue names/jobs dan pola pemprosesan.
- Selaras dengan D18 untuk AI routing, DLP, web augmentation, conversation management.

---

## 8. Struktur Fail Deliverable

- `WORKFLOW_DIAGRAM_PLAN.md` — pelan (dokumen ini)
- `WORKFLOW_DIAGRAMS.md` — set rajah Mermaid workflow
