# Rajah Aliran AI Chatbot (AI Chatbot Flow Diagrams) — ICTServe v3.6.1

**Versi sistem:** 3.6.1
**Tarikh:** 2025-12-29
**Nota:** Dokumen ini mengandungi rajah aliran AI Chatbot menggunakan Mermaid.

---

## Notasi

- Nod keputusan menggunakan bentuk berlian (contoh: `X{Soalan?}`)
- Aktiviti asinkron (queue/broadcast) ditanda dengan gaya `async`

```mermaid
flowchart TD
  classDef async stroke-dasharray: 5 5;
```

---

## 1) AI Chatbot End-to-End (High-Level)

Berasaskan D03 §5.9 (SRS-AI-001..020), D18 §5 (routing), D16 (event real-time), D17 (jobs/queues), D15 (BM sahaja).

```mermaid
flowchart TD
  classDef async stroke-dasharray: 5 5;

  A([Mula]) --> B[Pengguna akses /ai/chat]
  B --> C{Authenticated?}

  C -- Ya --> D[Resolve user_id + perbualan berterusan]
  C -- Tidak --> E[Wujudkan conversation UUID - guest + status token]

  D --> F[Pengguna taip pertanyaan - BM]
  E --> F

  F --> G[Analisis pertanyaan: FAQ vs kompleks vs hibrid]
  G --> H[Klasifikasi data: public vs internal/confidential/restricted]

  H --> I{Local-only? - kedaulatan data}
  I -- Ya --> J[Ollama - Local + RAG]
  I -- Tidak --> K[DLP + PII detection/sanitization]

  K --> L{Lulus DLP/PII gate?}
  L -- Tidak --> M[Blok/ubah: guna Ollama local + jawapan minimum]
  L -- Ya --> N{Jenis pertanyaan}

  N -- FAQ awam --> O[Ollama - keutamaan atau Bedrock - selepas DLP]
  N -- Kompleks awam --> P[Bedrock Claude - selepas DLP]
  N -- Hibrid --> Q[Hibrid: Ollama - fakta + Bedrock - penaakulan]

  J --> R[Jana jawapan BM]
  M --> R
  O --> R
  P --> R
  Q --> R

  R --> S[Message logging + routing decision - tanpa PII]:::async
  R --> T[Broadcast status AI - untuk pemantauan admin]:::async
  R --> U[Paparkan respons di UI]

  U --> V([Tamat / Keadaan stabil])
```

---

## 2) Decision Tree: Routing + Data Residency + DLP/PII Gate

Berasaskan D18 §5.1 (faq_sensitive, faq_public, complex_sensitive, complex_public) dan D03 SRS-AI-018 (data residency), SRS-AI-007 (PII), SRS-AI-009 (fallback).

```mermaid
flowchart TD
  classDef async stroke-dasharray: 5 5;

  A([Pertanyaan Masuk]) --> B[Analisis: faq_specific / complex_reasoning / hybrid]
  B --> C[Semak sensitiviti + data residency]

  C --> D{internal/confidential/restricted?}
  D -- Ya --> E[Local-only: Ollama SAHAJA]

  D -- Tidak --> F{Kategori D18}

  F -- faq_sensitive --> E
  F -- complex_sensitive --> E

  F -- faq_public --> G[DLP/PII gate]
  F -- complex_public --> G
  F -- hybrid --> G

  G --> H{Lulus DLP/PII?}
  H -- Tidak --> I[Fallback: Ollama local + cadang FAQ statik]
  H -- Ya --> J{Jenis pertanyaan}

  J -- FAQ awam --> K[Ollama - keutamaan / Bedrock - opsyen]
  J -- Kompleks awam --> L[Bedrock Claude]
  J -- Hibrid --> M[Ollama - fakta + Bedrock - penaakulan]

  K --> N[Jawapan BM]
  L --> N
  M --> N
  E --> N
  I --> N

  N --> O{Service degraded?}
  O -- Ya --> P[Kurangkan fungsi: local-only + respons ringkas]
  O -- Tidak --> Q([Selesai])
  P --> Q
```

---

## 3) Conversation Lifecycle: Guest vs Auth

Berasaskan D03 SRS-AI-005 (pengurusan perbualan) dan D16 (saluran subscription guest UUID).

```mermaid
flowchart TD
  classDef async stroke-dasharray: 5 5;

  A([Mula]) --> B{Authenticated?}

  B -- Ya --> C[Perbualan dikaitkan kepada user_id]
  C --> D[Save / Load / Delete perbualan]
  D --> E[Memori lebih panjang - user-linked]

  B -- Tidak --> F[Perbualan guest: conversation UUID]
  F --> G[Simpan session-based - 24 jam]

  E --> H{Export PDF?}
  G --> H

  H -- Ya --> I[Jana PDF perbualan]:::async
  H -- Tidak --> J([Tamat])

  I --> J
```

---

## 4) Ops & Observability (Reverb + Pulse + Horizon)

Berasaskan D16 §3.4 (AI events + channels) dan D03 SRS-AI-019 (performance monitoring), D17 (Horizon).

```mermaid
flowchart TD
  classDef async stroke-dasharray: 5 5;

  A([AI Request Diproses]) --> B[Dispatch AI status events]:::async

  B --> C[private-ai-status: AIProcessingStarted/Completed]:::async
  B --> D[private-ai-alerts: AIErrorOccurred / Degraded / Restored]:::async
  B --> E[private-ai-performance: metrik sanitized]:::async
  B --> F[private-ai-approvals: auto-reply approvals]:::async

  E --> G[Laravel Pulse: AI metrics + response time]:::async
  F --> H[Admin/Superuser semak draf auto-reply]:::async

  D --> I{Kegagalan perkhidmatan?}
  I -- Ya --> J[Fallback chain: Ollama → Bedrock → static FAQ]
  I -- Tidak --> K([Stabil])

  J --> K

  L[Queue AI/RAG - Redis] --> M[Horizon dashboard /horizon]:::async
  M --> K
```

---

## 5) RAG Ingestion Pipeline (Jobs/Queues)

Berasaskan D17 §4.2 (AI/RAG & Dokumen).

```mermaid
flowchart TD
  classDef async stroke-dasharray: 5 5;

  A([Dokumen dimuat naik / sumber dokumen]) --> B[Dispatch DocumentIngestJob]:::async
  B --> C[Chunking + metadata]
  C --> D[Dispatch EmbeddingJob]:::async
  D --> E[Jana embeddings - RAG]
  E --> F[Simpan embeddings + index]

  G([AI Chat: pertanyaan]) --> H[Retrieval: cari dokumen relevan]
  F --> H
  H --> I[Gabung konteks → prompt]
  I --> J[Jawab - Ollama/Bedrock mengikut routing]
```

---

## 6) Streaming Responses (FUTURE)

**Nota:** SRS-AI-013 menyatakan streaming SSE sebagai FUTURE. D16 menunjukkan contoh event streaming pada saluran perbualan.

```mermaid
flowchart TD
  classDef async stroke-dasharray: 5 5;

  A([Mulakan respons]) --> B{Streaming aktif? - FUTURE}
  B -- Tidak --> C[Hantar respons penuh]
  B -- Ya --> D[Hantar chunk demi chunk - SSE]:::async
  D --> E[Kemaskini UI: AI sedang menaip...]
  E --> F[Streaming selesai]
  C --> G([Tamat])
  F --> G
```
