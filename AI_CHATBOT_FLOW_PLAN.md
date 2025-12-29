# Pelan Rajah Aliran AI Chatbot (AI Chatbot Flow Plan) — ICTServe v3.6.1

**Sistem:** ICTServe
**Versi:** 3.6.1
**Tarikh kemaskini pelan:** 2025-12-29
**Bahasa output:** Bahasa Melayu (utama), istilah teknikal dikekalkan bila perlu
**Skop:** Rajah aliran (*flow*) AI Chatbot end-to-end berpandukan dokumen v3.6.1

---

## 1. Tujuan

Dokumen ini merancang secara menyeluruh penghasilan rajah aliran terperinci untuk **AI Chatbot ICTServe** (Cloud Hybrid AI Architecture) yang menerangkan:

- Bagaimana pengguna (tetamu / staf) mengakses `/ai/chat` dan memulakan perbualan.
- Bagaimana sistem **mengklasifikasikan pertanyaan** dan **menentukan kedaulatan data**.
- Bagaimana sistem melakukan **DLP/PII gating** sebelum sebarang pemprosesan cloud.
- Bagaimana sistem menjalankan **Ollama (tempatan)**, **AWS Bedrock (cloud)**, atau **aliran hibrid**.
- Bagaimana sistem mengurus perbualan, logging/audit, pemantauan prestasi, serta fallback dan prosedur kecemasan.

Rajah ini bertujuan menyokong pemahaman teknikal (dev/ops) dan pematuhan (PDPA/PPDA, audit trail, data residency).

---

## 2. Ruang Lingkup

### 2.1 Dalam skop

#### Pintu masuk & peranan pengguna

- Tetamu vs authenticated users (SRS-AI-001).
- Perbezaan pengurusan perbualan: guest session-based (24 jam) vs user-linked (SRS-AI-005).

#### Klasifikasi pertanyaan & kedaulatan data

- Klasifikasi jenis pertanyaan (FAQ/kompleks/hibrid) dan sensitiviti data (sensitif/awam) (D18 §5.1).
- Aliran routing berdasarkan klasifikasi (D18 §5.1.1–5.1.4, D03 SRS-AI-007, SRS-AI-018).

#### Penghalaan Model (routing) & pemilihan model

- Routing: Ollama local sahaja untuk pertanyaan sensitif, DLP→Bedrock untuk awam/kompleks, hibrid untuk gabungan fakta+penaakulan (D03 SRS-AI-011, D18 §5.3).
- Pemilihan model Bedrock mengikut task/complexity (D18 §5.3).

#### Penapisan & kawalan keselamatan

- DLP filters sebelum cloud (D18 §5.1.4).
- PII detection dan sanitization untuk PDPA 2010 compliance (D03 SRS-AI-007).
- Content filtering untuk harmful/profanity/security risk (D03 SRS-AI-010).

#### Web-Augmented Responses

- Toggle web search (DuckDuckGo) + caching + content filtering (D03 SRS-AI-014).

#### Observability & real-time status

- AI real-time events untuk pemantauan admin/superuser (D16 §3.4).
- Strategi saluran (auth vs guest UUID) dan contoh subscription (D16 §3.3).

#### Operasi & background jobs (AI/RAG)

- Job/queue AI yang didokumenkan: `DocumentIngestJob`, `EmbeddingJob`, `AutoReplyGenerationJob` (D17 §4.2).
- Implikasi timeout/worker dan pemantauan Horizon/Pulse (D17, D03 SRS-AI-019).

#### Fallback & emergency

- Health monitoring dan auto-fallback chain (D03 SRS-AI-009).
- Prosedur kecemasan (D03 SRS-AI-020).

### 2.2 Di luar skop

- Rajah UI/UX skrin demi skrin.
- Perincian penuh implementasi kelas/servis (rajah ini fokus aliran proses dan keputusan).
- Integrasi lain di luar AI (kecuali kaitan langsung seperti approval auto-reply dan queue/broadcast).

---

## 3. Sumber Rujukan (Dokumen v3.6.1)

Rujukan utama untuk kandungan rajah:

- **D18**: Strategi penghalaan dengan kedaulatan data (klasifikasi, routing, pemilihan model).
- **D03**: Keperluan AI (SRS-AI-001..020) termasuk routing, data residency, logging, monitoring, streaming (future).
- **D16**: Reverb/Echo broadcasting termasuk AI event channels dan dual-channel strategy (auth vs guest UUID).
- **D17**: Horizon/queue termasuk job AI/RAG dan cadangan operasi.
- **D15**: Keperluan bahasa (v3.6.0+ **Bahasa Melayu sahaja**, termasuk AI Chatbot).
- **D00/D04**: Konteks seni bina dan integrasi lapisan AI (untuk rujukan naratif).

---

## 4. Kekangan & Prinsip Pemodelan

### 4.1 Bahasa & pematuhan UI

- **Bahasa Melayu sahaja (v3.6.0+)** untuk label/teks rajah termasuk AI Chatbot (D15).
- Istilah teknikal dikekalkan: Ollama, AWS Bedrock, DLP, PII, RAG, SSE, Reverb, Horizon.

### 4.2 Prinsip pemodelan aliran

- Fokus kepada **decision points** yang menentukan path utama:
  - Guest vs Auth
  - Klasifikasi pertanyaan
  - Data residency / sensitiviti
  - Lulus/gagal DLP/PII gate
  - Service health / degraded mode
  - Web search toggle

- Setiap rajah:
  - Bermula dengan “Mula” dan tamat dengan “Tamat / Keadaan stabil”.
  - Menandakan aktiviti asinkron (queue/broadcast) dengan gaya `async`.

---

## 5. Aktor, Sistem, dan Komponen (Untuk Node Rajah)

### 5.1 Aktor

- **Tetamu (Guest)**
- **Staf (Authenticated User)**
- **Admin/Superuser** (pemantauan, konfigurasi, approvals)

### 5.2 Komponen sistem yang perlu muncul dalam rajah

- **UI Web**: Halaman AI Chat `/ai/chat`
- **Backend**: Controller/endpoint AI Chat, Conversation Manager
- **Router**: Klasifikasi pertanyaan + data residency + model routing
- **DLP/PII**: Penapisan & sanitization sebelum cloud
- **RAG**: Retrieval dokumen/FAQ + embeddings
- **LLM**:
  - **Ollama (Local)**
  - **AWS Bedrock (Cloud)**
- **Web Search**: DuckDuckGo (toggle)
- **Logging & Audit**: message logging + audit trail
- **Queue**: Redis + Horizon (AI jobs)
- **Broadcasting**: Reverb + Echo (status/events)
- **Monitoring**: Laravel Pulse + dashboard admin

---

## 6. Matriks Routing (Terjemahan Operasi daripada D18 + D03)

Matriks ini akan diwakili sebagai keputusan utama dalam rajah.

### 6.1 Klasifikasi D18 (contoh kategori)

- `faq_sensitive` → **Ollama Local SAHAJA**
- `faq_public` → Ollama (keutamaan) atau Bedrock (selepas DLP)
- `complex_sensitive` → **Ollama Local SAHAJA**
- `complex_public` → **DLP Filters → Bedrock Claude**
- `hybrid` → **Ollama (fakta/RAG) + Bedrock (penaakulan)** (SRS-AI-011)

### 6.2 Data Residency (SRS-AI-018)

- `public` → boleh ke cloud (tertakluk DLP/PII gate)
- `internal/confidential/restricted` → local-only

### 6.3 Fallback chain (SRS-AI-009)

- Lalai: Ollama → (jika perlu/allowed) Bedrock → Static FAQ
- Degraded mode: kekal local-only + respon ringkas + cadang FAQ/statik

---

## 7. Senario Aliran Teras (Step-by-step yang rajah akan tunjuk)

### 7.1 Aliran permulaan perbualan

1. Pengguna akses `/ai/chat`
1. Sistem tentukan konteks pengguna (guest vs auth)
1. Sistem wujudkan/resolve `conversation_id` (guest UUID / auth user-linked)
1. Pengguna hantar pertanyaan

### 7.2 Aliran routing + pemprosesan

1. Klasifikasi query: FAQ vs kompleks vs hibrid (D18 §5.2)
1. Klasifikasi sensitiviti + data residency (D18 §5.1, SRS-AI-018)
1. Jika laluan cloud berpotensi → jalankan DLP + PII detection/sanitization
1. Pilih laluan: Ollama local / Bedrock cloud / Hybrid (dua panggilan / dua jawapan → digabung)

### 7.3 Aliran web augmentation (opsyen)

1. Jika toggle web search aktif → cari DuckDuckGo → cache → filter → tambah konteks

### 7.4 Aliran respons

1. Jana respons BM (selari D15) + metadata ringkas (contoh: model yang digunakan jika dipaparkan)
1. Log: query, routing decision, confidence score, tanpa PII (SRS-AI-004)
1. Hantar respons ke UI

### 7.5 Aliran status real-time

1. Broadcast status pemprosesan / alert untuk pemantauan admin (D16 §3.4)

---

## 8. Aliran Operasi AI/RAG (Queue) yang perlu muncul

Rajah akan memodelkan sekurang-kurangnya 1 aliran ingestion + embeddings:

- `DocumentIngestJob` → chunking/metadata → dispatch `EmbeddingJob` (D17 §4.2)
- `EmbeddingJob` → jana embeddings (RAG)

Dan aliran auto-reply (bila relevan dengan AI):

- `AutoReplyGenerationJob` → routing (Ollama/Bedrock) → hasilkan draf → memerlukan approval (D16 event approvals)

---

## 9. Real-time Events & Saluran (D16)

Rajah perlu menonjolkan:

- Saluran auth vs guest UUID (dual strategy)
- AI events untuk admin/superuser:
  - `AIProcessingStarted`, `AIProcessingCompleted`
  - `AIErrorOccurred`, `AIServiceDegraded`, `AIServiceRestored`
  - `AIPerformanceUpdate`, `AIPerformanceAlert` (sanitized)
  - `AutoReplyDraftCreated`, `AutoReplyApproved`, `AutoReplyRejected`

---

## 10. Set Rajah Yang Akan Dihasilkan (Dalam AI_CHATBOT_FLOW_DIAGRAMS.md)

#### AI Chatbot End-to-End (High-Level)

- Pintu masuk `/ai/chat` → klasifikasi → routing → respons + logging + events.

#### Decision Tree: Routing + Data Residency + DLP/PII Gate

- Memusatkan keputusan kritikal dan fallback.

#### Conversation Lifecycle: Guest vs Auth

- Cipta/restore, simpan, tamat, export PDF (SRS-AI-005).

#### Ops & Observability (Reverb + Pulse + Horizon)

- Event channels, degraded mode, alert, metrics.

#### RAG Ingestion Pipeline (Jobs/Queues)

- `DocumentIngestJob` → `EmbeddingJob` → storage → retrieval dalam chat.

---

## 11. Kriteria “Siap” (Definition of Done)

- Semua label rajah dalam Bahasa Melayu (D15), istilah teknikal konsisten.
- Rajah menggambarkan routing D18 (sensitif→local-only, awam→DLP→cloud, hibrid).
- Rajah memasukkan:
  - perbezaan guest vs auth
  - logging/audit (tanpa PII)
  - event real-time (D16)
  - jobs/queue AI (D17)
  - fallback chain + degraded mode
- Mermaid boleh dirender tanpa ralat sintaks.
