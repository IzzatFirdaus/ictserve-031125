# D18: Dokumentasi AI Chatbot Ollama-Bedrock (AI Chatbot Ollama-Bedrock Documentation)

## ICTServe v3.6.1 - Cloud Hybrid AI Architecture

| Atribut | Nilai |
|---------|-------|
| **Versi** | 1.0.1 |
| **Tarikh Kemaskini** | 17 Disember 2025 |
| **Status** | Aktif - Sedia untuk Pelaksanaan |
| **Klasifikasi** | Terhad - Dalaman BPM MOTAC |
| **Pematuhi** | ISO/IEC/IEEE 42010, ISO/IEC/IEEE 15288, ISO/IEC/IEEE 12207, ISO/IEC/IEEE 29148, WCAG 2.2 AA, OWASP ASVS L2, PDPA 2010, MyGOV Digital Service Standards v2.1.0 |
| **Bahasa** | Bahasa Melayu sahaja (D15 v3.6.0+) |

> **Notis Penggunaan Dalaman**: Sistem ini adalah untuk kegunaan warga kerja MOTAC (staf dan pegawai gred) sahaja dan tidak dibuka kepada orang awam (internal use only).

---

## Sejarah Perubahan (Changelog)

| Versi | Tarikh | Perubahan | Penulis |
|-------|--------|-----------|---------|
| 1.0.0 | 2025-12-14 | Dokumen asal D18 - Konsolidasi lengkap dokumentasi AI Chatbot termasuk API Reference, Deployment Guide, Emergency Procedures, dan pematuhan D00-D17 v3.6.0 | Pasukan Pembangunan BPM |
| 1.0.1 | 2025-12-17 | **Kemaskini Teknologi Stack**: Laravel 12.42.0, Livewire 3.7.1, Laravel Pulse 1.4.6, Laravel Reverb 1.6.3, Laravel Sanctum 4.2.1, Laravel Socialite 5.24.0, PHPUnit 11.5.46, Tailwind CSS 4.1.17, Laravel MCP 0.3.4, Laravel Prompts 0.3.8, Larastan 3.8.1, Laravel Pint 1.26.0, Laravel Telescope 5.16.0. Kemaskini versi sistem kepada ICTServe v3.6.1, penyelarasan dengan D00-D18 v3.6.1, pengesahan Bahasa Melayu sahaja (v3.6.0+) | Pasukan Pembangunan BPM |

---

## Rujukan Dokumen Berkaitan (Related Document References)

| Dokumen | Penerangan | Versi |
|---------|------------|-------|
| [D00_SYSTEM_OVERVIEW.md](D00_SYSTEM_OVERVIEW.md) | Gambaran keseluruhan sistem dan tadbir urus | v3.6.1 |
| [D03_SOFTWARE_REQUIREMENTS_SPECIFICATION.md](D03_SOFTWARE_REQUIREMENTS_SPECIFICATION.md) | Spesifikasi keperluan perisian | v3.6.1 |
| [D04_SOFTWARE_DESIGN_DOCUMENT.md](D04_SOFTWARE_DESIGN_DOCUMENT.md) | Seni bina dan reka bentuk | v3.6.1 |
| [D09_DATABASE_DOCUMENTATION.md](D09_DATABASE_DOCUMENTATION.md) | Skema pangkalan data dan dual audit | v3.6.1 |
| [D11_TECHNICAL_DESIGN_DOCUMENTATION.md](D11_TECHNICAL_DESIGN_DOCUMENTATION.md) | Infrastruktur teknikal | v3.6.1 |
| [D15_LANGUAGE_MS_EN.md](D15_LANGUAGE_MS_EN.md) | Penyetempatan bahasa (Bahasa Melayu sahaja) | v3.6.1 |
| [D16_BROADCASTING_SETUP.md](D16_BROADCASTING_SETUP.md) | Konfigurasi WebSocket (Laravel Reverb) | v3.6.1 |
| [D17_QUEUE_MANAGEMENT_HORIZON.md](D17_QUEUE_MANAGEMENT_HORIZON.md) | Pengurusan queue (Laravel Queue + Redis; Horizon tidak dipasang) | v3.6.1 |

---

## Kandungan (Table of Contents)

1. [Glosari (Glossary)](#1-glosari-glossary)
2. [Ringkasan Eksekutif (Executive Summary)](#2-ringkasan-eksekutif-executive-summary)
3. [Seni Bina Cloud Hybrid AI (Architecture)](#3-seni-bina-cloud-hybrid-ai-architecture)
4. [Keperluan Sistem (System Requirements)](#4-keperluan-sistem-system-requirements)
5. [Strategi Penghalaan Pertanyaan (Query Routing Strategy)](#5-strategi-penghalaan-pertanyaan-query-routing-strategy)
6. [Butiran Pelaksanaan (Implementation Details)](#6-butiran-pelaksanaan-implementation-details)
7. [Pengendalian Respons (Response Handling)](#7-pengendalian-respons-response-handling)
8. [Pengoptimuman Kos (Cost Optimization)](#8-pengoptimuman-kos-cost-optimization)
9. [Konfigurasi (Configuration)](#9-konfigurasi-configuration)
10. [Rujukan API (API Reference)](#10-rujukan-api-api-reference)
11. [Panduan Deployment (Deployment Guide)](#11-panduan-deployment-deployment-guide)
12. [Prosedur Kecemasan (Emergency Procedures)](#12-prosedur-kecemasan-emergency-procedures)
13. [Pengujian (Testing)](#13-pengujian-testing)
14. [Penyelesaian Masalah (Troubleshooting)](#14-penyelesaian-masalah-troubleshooting)
15. [Pematuhan D00-D18 v3.6.1 (Compliance)](#15-pematuhan-d00-d18-v361-compliance)

---

## 1. Glosari (Glossary)

| Istilah | Definisi |
|---------|----------|
| **Ollama** | Pelayan Large Language Model tempatan yang menyediakan keupayaan AI tanpa kebergantungan API luaran |
| **AWS Bedrock** | Perkhidmatan AI terurus Amazon yang menyediakan akses kepada model Claude (Opus, Sonnet, Haiku) |
| **RAG** | Retrieval-Augmented Generation - teknik AI yang menggabungkan pengambilan dokumen dengan penjanaan bahasa |
| **FAQ_Bot** | Sistem Q&A perbualan untuk sokongan helpdesk yang boleh diakses melalui borang tetamu dan portal authenticated |
| **Document_Analysis** | Perkhidmatan ringkasan dan pengekstrakan kandungan dokumen PDF/Word untuk pengguna authenticated dan admin |
| **Auto_Reply** | Draf respons yang dijana LLM untuk tiket dan permohonan pinjaman yang memerlukan aliran kerja kelulusan admin |
| **Vector_Embeddings** | Perwakilan berangka teks untuk carian semantik |
| **PII** | Maklumat Pengenalan Peribadi yang memerlukan perlindungan di bawah PDPA 2010 |
| **True_Hybrid_Architecture** | Seni bina hibrid sebenar ICTServe v3.6.0 dengan self-registration dan akses fleksibel |
| **Self_Registration** | Pendaftaran sendiri staf MOTAC dengan e-mel @motac.gov.my dan pengesahan e-mel |
| **Flexible_Login** | Log masuk fleksibel menggunakan e-mel penuh atau nama pengguna pendek |
| **Account_Linking** | Pautan akaun opsyen untuk penyerahan tetamu terdahulu ke akaun authenticated |
| **Dual_Audit_System** | Sistem audit dwi menggunakan owen-it (compliance) dan spatie (operations) |
| **Cloud_Hybrid_Architecture** | Seni bina hibrid yang menggabungkan pemprosesan tempatan (Ollama) dan cloud (AWS Bedrock) |
| **Multi_Model_Intelligence** | Keupayaan untuk menggunakan model AI yang berbeza berdasarkan jenis tugas dan keperluan prestasi |
| **Streaming_Responses** | Respons AI yang dihantar secara berperingkat untuk pengalaman pengguna yang lebih responsif |
| **Web_Augmented_Responses** | Respons AI yang diperkaya dengan maklumat terkini dari carian web |
| **Model_Routing** | Penghalaan automatik permintaan AI kepada model yang paling sesuai berdasarkan jenis tugas |
| **Conversation_Management** | Pengurusan konteks perbualan yang dipertingkat dengan memori jangka panjang |
| **SSE** | Server-Sent Events - protokol untuk streaming data dari pelayan ke klien |
| **Laravel Sanctum** | Sistem pengesahan API token untuk Laravel |
| **Laravel Reverb** | Pelayan WebSocket untuk notifikasi masa nyata |
| **Laravel Horizon** | Dashboard pengurusan queue (opsyenal). **Tidak dipasang** dalam repo v3.6.1; pemantauan queue menggunakan Laravel Pulse + Filament Failed Jobs/Email Logs |
| **Laravel Pulse** | Dashboard pemantauan prestasi masa nyata |
| **Laravel Telescope** | Alat debugging untuk Laravel (superuser sahaja) |

---

## 2. Ringkasan Eksekutif (Executive Summary)

### 2.1 Tujuan (Purpose)

ICTServe melaksanakan **True Hybrid AI Architecture** yang menggabungkan AWS Bedrock (model Claude) dengan Ollama tempatan (sistem FAQ berasaskan RAG) dalam satu antara muka chat bersepadu. Pengguna berinteraksi dengan satu sistem yang menghalakan pertanyaan secara pintar kepada backend AI yang optimum.

### 2.2 Prinsip Utama (Key Principles)

- **Antara Muka Tunggal**: Pengguna berinteraksi dengan SATU sistem chat
- **Penghalaan Pintar**: Sistem memutuskan AI mana yang digunakan berdasarkan analisis pertanyaan
- **Respons Hibrid**: Menggabungkan Ollama (pengetahuan FAQ) + Bedrock (penaakulan)
- **Pengalaman Lancar**: Pengguna tidak perlu tahu AI mana yang menjawab
- **Pengoptimuman Kos**: Ollama percuma dahulu, Bedrock mahal apabila diperlukan

### 2.3 Ciri Utama v3.6.1 (Key Features)

| Ciri | Penerangan | Status |
|------|------------|--------|
| Cloud Hybrid AI Architecture | Pendekatan pemprosesan dwi (Ollama + AWS Bedrock) dengan model routing pintar | ✅ Selesai |
| Multi-Model Intelligence | Model Claude 4.x (Opus 4.5, Sonnet 4.5, Haiku 4.5) dengan task-specific routing | ✅ Selesai |
| Streaming Responses | Server-Sent Events (SSE) untuk pengalaman pengguna yang responsif | 🔄 Future |
| Web-Augmented Responses | Integrasi DuckDuckGo untuk konteks terkini | ✅ Selesai |
| Enhanced Conversation Management | Model BedrockConversation dengan save/load/delete | ✅ Selesai |
| MCP Server Integration | 3 tools untuk AI assistants (Amazon Q, Kiro) | ✅ Selesai |
| Data Residency Compliance | Klasifikasi data automatik untuk pemprosesan tempatan vs cloud | ✅ Selesai |
| Bahasa Melayu Sahaja | Antara muka AI tanpa penukar bahasa (D15 v3.6.0) | ✅ Selesai |

### 2.4 Konteks Integrasi Kritikal

Integrasi **Cloud Hybrid AI Architecture** mesti selaras dengan **True Hybrid Architecture** ICTServe v3.6.0:

1. **Akses Tetamu (Tanpa Log Masuk)**: FAQ Bot berkuasa AI dengan model routing pintar boleh diakses pada borang awam untuk sokongan pantas tanpa pengesahan
2. **Portal Authenticated (Log Masuk Diperlukan)**: Ciri AI dipertingkat untuk staf termasuk analisis dokumen dengan web-augmented responses, conversation management dengan memori jangka panjang, dan respons peribadi menggunakan multi-model intelligence
3. **Akses Admin (Panel Filament)**: Antara muka pengurusan AI hibrid untuk peranan admin dan superuser termasuk konfigurasi model (Ollama vs Bedrock), aliran kerja kelulusan auto-reply dengan streaming responses, pengurusan FAQ, dan ingestion dokumen dengan model selection berdasarkan jenis kandungan

### 2.5 Penekanan Utama

- **Komunikasi berasaskan e-mel** untuk notifikasi
- **Pematuhan WCAG 2.2 Level AA** untuk semua antara muka termasuk streaming responses
- **Sasaran prestasi Core Web Vitals** yang dipertingkat (LCP <2.5s, FID <100ms, CLS <0.1)
- **Antara muka Bahasa Melayu sahaja** (v3.6.0)
- **Jejak audit komprehensif** dengan pengekalan 7 tahun untuk pematuhan
- **Data residensi Malaysia** untuk pemprosesan cloud

---

## 3. Seni Bina Cloud Hybrid AI (Architecture)

### 3.1 Rajah Seni Bina Sistem (System Architecture Diagram)

```text
┌─────────────────────────────────────────────────────────────────────┐
│                    ICTServe Hybrid AI Interface                      │
│                         (BedrockChat.php)                            │
├─────────────────────────────────────────────────────────────────────┤
│                                                                      │
│  ┌────────────────────────────────────────────────────────────────┐ │
│  │                    User Chat Interface                         │ │
│  │  • Model Selection (Opus/Sonnet/Haiku)                        │ │
│  │  • Internet Search Toggle                                      │ │
│  │  • Conversation History                                        │ │
│  │  • FAQ Suggestions (context-aware)                            │ │
│  └────────────────────────────────────────────────────────────────┘ │
│                              │                                       │
│                              ▼                                       │
│  ┌────────────────────────────────────────────────────────────────┐ │
│  │                   Query Analysis Layer                         │ │
│  │  • Keyword Detection (FAQ vs Complex)                         │ │
│  │  • Intent Classification                                       │ │
│  │  • Context Evaluation                                          │ │
│  └────────────────────────────────────────────────────────────────┘ │
│                              │                                       │
│              ┌───────────────┼───────────────┐                      │
│              ▼               ▼               ▼                      │
│  ┌──────────────────┐ ┌──────────────┐ ┌──────────────────┐        │
│  │   FAQ Query      │ │ Hybrid Query │ │  Complex Query   │        │
│  │   (Ollama RAG)   │ │ (Both AIs)   │ │  (Bedrock Only)  │        │
│  └──────────────────┘ └──────────────┘ └──────────────────┘        │
│              │               │               │                      │
│              ▼               ▼               ▼                      │
│  ┌────────────────────────────────────────────────────────────────┐ │
│  │                   Response Aggregation                         │ │
│  │  • Source Attribution                                          │ │
│  │  • Markdown Rendering                                          │ │
│  │  • Conversation Persistence                                    │ │
│  └────────────────────────────────────────────────────────────────┘ │
│                                                                      │
└─────────────────────────────────────────────────────────────────────┘

┌─────────────────────────────────────────────────────────────────────┐
│                        Backend Services                              │
├─────────────────────────────────────────────────────────────────────┤
│                                                                      │
│  ┌──────────────────────┐         ┌──────────────────────┐         │
│  │    RagService        │         │   BedrockService     │         │
│  │    (Ollama)          │         │   (AWS Bedrock)      │         │
│  ├──────────────────────┤         ├──────────────────────┤         │
│  │ • FAQ Knowledge Base │         │ • Claude Opus 4.5    │         │
│  │ • Embedding Search   │         │ • Claude Sonnet 4.5  │         │
│  │ • Context Retrieval  │         │ • Claude Haiku 4.5   │         │
│  │ • Local Processing   │         │ • Internet Search    │         │
│  └──────────────────────┘         └──────────────────────┘         │
│           │                                │                        │
│           ▼                                ▼                        │
│  ┌──────────────────────┐         ┌──────────────────────┐         │
│  │   Ollama Server      │         │   AWS Bedrock API    │         │
│  │   (localhost:11434)  │         │   (us-east-1)        │         │
│  └──────────────────────┘         └──────────────────────┘         │
│                                                                      │
└─────────────────────────────────────────────────────────────────────┘
```

### 3.2 Tanggungjawab Komponen (Component Responsibilities)

| Komponen | Tanggungjawab |
|----------|---------------|
| **BedrockChat** | Komponen Livewire utama, mengatur respons hibrid |
| **RagService** | Integrasi Ollama, pengambilan pengetahuan FAQ |
| **BedrockService** | Pembungkus API AWS Bedrock |
| **EmbeddingService** | Operasi vector embeddings untuk carian semantik |
| **ModelRouter** | Pemilihan model pintar berdasarkan kompleksiti tugas |
| **PIIDetectionService** | Pengesanan dan sanitasi PII (PDPA 2010) |
| **NetworkMonitoringService** | Pengesanan ketersambungan luaran (D11 v3.6.0) |

### 3.3 Timbunan Teknologi (Technology Stack)

| Komponen | Teknologi | Tujuan |
|----------|-----------|--------|
| **Bedrock** | AWS Bedrock Runtime | Model Claude Opus 4.5/Sonnet 4.5/Haiku 4.5 |
| **Ollama** | Local LLM + RAG | Pangkalan pengetahuan khusus FAQ |
| **Frontend** | Livewire 3.7.1 + Volt 1.10.1 | Antara muka chat reaktif |
| **Backend** | Laravel 12.42.0, PHP 8.2.12 | Orkestrasi API |
| **Admin Panel** | Filament 4.1.10 | Antara muka pengurusan AI |
| **Real-time** | Laravel Reverb 1.6.3 | Notifikasi WebSocket |
| **Queue** | Laravel Queue + Redis | Pemprosesan kerja latar belakang (Horizon tidak dipasang) |
| **Audit** | owen-it + spatie | Sistem audit dwi (D09 v3.6.1) |

### 3.4 Seni Bina Lapisan Perkhidmatan (Service Layer Architecture)

```text
┌─────────────────────────────────────────────────────────────────────────────┐
│                        Laravel 12.42.0 Application                         │
├─────────────────────────────────────────────────────────────────────────────┤
│  Controllers (API & Web) - Bahasa Melayu sahaja                            │
│  ├── OllamaController (API endpoints)                                      │
│  ├── FaqController (Hybrid: Guest + Auth, nullable user_id FK)             │
│  ├── DocumentController (Admin/Superuser only)                             │
│  ├── AutoReplyController (Approval workflow dengan email tokens)           │
│  └── Auth Controllers (Self-Registration, Flexible Login, Account Linking) │
├─────────────────────────────────────────────────────────────────────────────┤
│  Services (Business Logic) - D00-D18 v3.6.1 Compliant                     │
│  ├── OllamaClient (HTTP wrapper + health check)                            │
│  ├── BedrockClient (AWS SDK wrapper + model routing)                       │
│  ├── RagService (RAG + conversation context, Bahasa Melayu responses)      │
│  ├── DocumentService (Ingest, Analysis, PII detection)                     │
│  ├── EmbeddingService (Vector operations + caching)                        │
│  ├── ModelRouter (Smart model selection based on task complexity)          │
│  ├── StreamingResponseService (SSE handler for long responses)             │
│  ├── WebSearchService (DuckDuckGo integration for web-augmented responses) │
│  └── BilingualSupportService (Dilumpuhkan v3.6.0 - sentiasa return 'ms')   │
├─────────────────────────────────────────────────────────────────────────────┤
│  Models & Data Layer (True Hybrid Architecture)                            │
│  ├── User (Self-registration, nullable relationships, locale='ms')         │
│  ├── Faq, Document, DocumentChunk (with user_id nullable FK)               │
│  ├── Embedding, AutoReplyTemplate, AutoReplyDraft                          │
│  ├── MessageLog (Dual audit trail: owen-it + spatie)                       │
│  ├── BedrockConversation (Enhanced conversation management)                │
│  └── ActivityLog, Audits (Dual audit system untuk compliance)             │
├─────────────────────────────────────────────────────────────────────────────┤
│  Jobs & Queues (Laravel Queue + Redis)                                     │
│  ├── DocumentIngestJob (Background processing)                             │
│  ├── EmbeddingJob (Vector generation)                                      │
│  ├── AutoReplyGenerationJob (AI response drafts)                           │
│  └── NotificationDigestJob (Multi-channel notifications)                   │
├─────────────────────────────────────────────────────────────────────────────┤
│  Monitoring & Performance (Laravel Pulse + Telescope + Sanctum)            │
│  ├── Laravel Pulse (Real-time performance monitoring - admin/superuser)    │
│  ├── Laravel Telescope (Debugging - superuser only)                        │
│  ├── Laravel Sanctum (API token authentication)                            │
│  └── Laravel Reverb (Real-time WebSocket notifications)                    │
└─────────────────────────────────────────────────────────────────────────────┘
```

---

## 4. Keperluan Sistem (System Requirements)

### 4.1 Keperluan Minimum Perkakasan (Minimum Hardware Requirements)

| Komponen | Minimum | Disyorkan | Catatan |
|----------|---------|-----------|---------|
| **CPU** | 4 cores (Intel i5/AMD Ryzen 5) | 8 cores (Intel i7/AMD Ryzen 7) | Untuk pemprosesan AI Ollama |
| **RAM** | 16GB | 32GB | Model AI memerlukan 8-16GB |
| **Storage** | 100GB SSD | 500GB NVMe SSD | Untuk model AI dan data |
| **Network** | 100 Mbps | 1 Gbps | Untuk akses API dan WebSocket |

### 4.2 Keperluan Perisian (Software Requirements)

| Perisian | Versi | Keperluan | Catatan |
|----------|-------|-----------|---------|
| **PHP** | 8.2.12+ | Wajib | Dengan ekstensi: curl, json, mbstring, xml, zip |
| **MySQL** | 8.0+ | Wajib | Dengan InnoDB engine |
| **Redis** | 7.0+ | Wajib | Untuk cache dan queue |
| **Nginx/Apache** | Latest | Wajib | Web server dengan SSL |
| **Ollama** | Latest | Wajib | Local LLM server |
| **Node.js** | 22+ | Wajib | Untuk asset compilation |
| **Composer** | 2.6+ | Wajib | PHP dependency manager |

### 4.3 Model AI Ollama

| Model | Saiz | RAM Diperlukan | Kegunaan |
|-------|------|----------------|----------|
| **llama3.1:8b-instruct-q4_K_M** | 4.7GB | 8GB | Pengeluaran (disyorkan) |
| **llama3.1:8b-instruct-fp16** | 16GB | 20GB | Kualiti tinggi |
| **llama3.1:7b-instruct-q4_K_M** | 4.1GB | 6GB | Pembangunan |

### 4.4 Model AWS Bedrock

| Model | Use Case | Speed | Cost | Recommendation |
|-------|----------|-------|------|----------------|
| **Opus 4.5** | Complex reasoning, analysis, formal responses | Slow | High | Complex queries only |
| **Sonnet 4.5** | Balanced performance, document analysis | Medium | Medium | Default for most tasks |
| **Haiku 4.5** | Quick responses, simple FAQ queries | Fast | Low | FAQ fallback, hybrid enhancement |
| **Nova Pro** | Balanced multimodal tasks | Medium | Medium | Document analysis with images |
| **Nova Lite** | Fast text processing | Fast | Low | Simple FAQ, quick responses |
| **Nova Micro** | Ultra-fast simple tasks | Very Fast | Very Low | Basic FAQ, status checks |
| **Titan Text Express** | Enterprise text generation | Medium | Low | Auto-reply drafts |
| **Titan Text Lite** | Lightweight text tasks | Fast | Very Low | Simple text processing |

### 4.5 Model Rate Limits

| Model | Requests/Min | Tokens/Min | Use Case |
|-------|--------------|------------|----------|
| **Opus 4.5** | 10 | 20,000 | Complex reasoning, formal responses |
| **Sonnet 4.5** | 20 | 40,000 | Balanced performance, document analysis |
| **Haiku 4.5** | 50 | 100,000 | Quick responses, simple FAQ queries |
| **Nova Micro** | 100 | 150,000 | Ultra-fast basic tasks |
| **Nova Lite** | 80 | 120,000 | Fast text processing |
| **Nova Pro** | 40 | 60,000 | Balanced multimodal tasks |
| **Titan Text Lite** | 60 | 80,000 | Lightweight text generation |
| **Titan Text Express** | 30 | 50,000 | Enterprise text generation |

---

## 5. Strategi Penghalaan Pertanyaan (Query Routing Strategy)

### 5.1 Klasifikasi Pertanyaan (Query Classification)

Sistem mengklasifikasikan pertanyaan masuk kepada tiga kategori:

#### 5.1.1 Pertanyaan Khusus FAQ (`faq_specific`)

**Ciri-ciri:**

- Mengandungi kata kunci khusus ICTServe
- Bertanya tentang helpdesk, pinjaman aset, prosedur sistem
- Soalan fakta dengan jawapan definitif

**Kata Kunci:**

```php
$faqKeywords = [
    'tiket', 'helpdesk', 'pinjaman', 'aset', 'status',
    'permohonan', 'sistem', 'ictserve', 'motac', 'bpm',
    'kelulusan', 'gred', 'pegawai', 'borang', 'sla'
];
```

**Penghalaan:** → Ollama RAG Service

#### 5.1.2 Pertanyaan Penaakulan Kompleks (`complex_reasoning`)

**Ciri-ciri:**

- Memerlukan analisis, perbandingan, atau pemikiran kreatif
- Soalan pengetahuan umum
- Permintaan strategik atau nasihat

**Kata Kunci:**

```php
$complexKeywords = [
    'analisis', 'bandingkan', 'jelaskan', 'mengapa',
    'bagaimana jika', 'strategi', 'cadangan', 'pendapat',
    'kelebihan', 'kekurangan', 'implikasi'
];
```

**Penghalaan:** → AWS Bedrock Claude

#### 5.1.3 Pertanyaan Hibrid (`hybrid`)

**Ciri-ciri:**

- Mengandungi kedua-dua kata kunci FAQ dan kompleks
- Memerlukan pengetahuan fakta + penaakulan
- Soalan "mengapa" tentang prosedur ICTServe

**Contoh:** "Mengapa sistem pinjaman aset perlu kelulusan Gred 41?"

**Penghalaan:** → Ollama (fakta) + Bedrock (penaakulan)

### 5.2 Algoritma Analisis Pertanyaan

```php
private function analyzeQuery(string $query): string
{
    $faqKeywords = [
        'tiket', 'helpdesk', 'pinjaman', 'aset', 'status',
        'permohonan', 'sistem', 'ictserve', 'motac', 'bpm',
        'kelulusan', 'gred', 'pegawai', 'borang', 'sla'
    ];

    $complexKeywords = [
        'analisis', 'bandingkan', 'jelaskan', 'mengapa',
        'bagaimana jika', 'strategi', 'cadangan', 'pendapat',
        'kelebihan', 'kekurangan', 'implikasi'
    ];

    $queryLower = strtolower($query);

    $faqScore = 0;
    $complexScore = 0;

    foreach ($faqKeywords as $keyword) {
        if (str_contains($queryLower, $keyword)) {
            $faqScore++;
        }
    }

    foreach ($complexKeywords as $keyword) {
        if (str_contains($queryLower, $keyword)) {
            $complexScore++;
        }
    }

    // Determine query type based on scores
    if ($faqScore > 0 && $complexScore > 0) {
        return 'hybrid';
    }

    if ($faqScore > 0) {
        return 'faq_specific';
    }

    return 'complex_reasoning';
}
```

### 5.3 Logik Penghalaan Model (Model Routing Logic)

```php
class ModelRouter
{
    public function selectModel(string $taskType, array $context): string
    {
        $complexity = $this->analyzeComplexity($context);
        
        return match([$taskType, $complexity]) {
            ['faq', 'simple'] => config('bedrock.models.fast_response'),      // Haiku
            ['faq', 'complex'] => config('bedrock.models.high_quality'),      // Sonnet
            ['document_analysis', _] => config('bedrock.models.high_quality'), // Sonnet
            ['auto_reply', _] => config('bedrock.models.high_quality'),        // Sonnet
            default => config('bedrock.models.default'),                       // Sonnet
        };
    }
    
    private function analyzeComplexity(array $context): string
    {
        $indicators = [
            'question_length' => strlen($context['query'] ?? ''),
            'technical_terms' => $this->countTechnicalTerms($context['query'] ?? ''),
            'context_size' => count($context['retrieved_docs'] ?? []),
        ];
        
        $score = ($indicators['question_length'] > 100 ? 1 : 0) +
                ($indicators['technical_terms'] > 3 ? 1 : 0) +
                ($indicators['context_size'] > 5 ? 1 : 0);
                
        return $score >= 2 ? 'complex' : 'simple';
    }
}
```

### 5.4 Contoh Klasifikasi Pertanyaan

| Pertanyaan | FAQ Score | Complex Score | Klasifikasi |
|------------|-----------|---------------|-------------|
| "Cara hantar tiket" | 1 | 0 | faq_specific |
| "Status pinjaman aset" | 2 | 0 | faq_specific |
| "Analisis sistem" | 0 | 1 | complex_reasoning |
| "Bandingkan pendekatan" | 0 | 1 | complex_reasoning |
| "Mengapa tiket perlu SLA?" | 1 | 1 | hybrid |
| "Jelaskan proses kelulusan" | 1 | 1 | hybrid |

---

## 6. Butiran Pelaksanaan (Implementation Details)

### 6.1 Keperluan API Ollama Kritikal

> **PENTING**: Keperluan berikut adalah kritikal untuk komunikasi yang betul dengan pelayan Ollama.

#### 6.1.1 Parameter `stream: false` (WAJIB)

```php
// ✅ BETUL: Sertakan stream: false
$payload = [
    'model' => 'gemma3:1b',
    'prompt' => $userQuery,
    'stream' => false,  // KRITIKAL: Mesti false untuk respons JSON tunggal
];
```

#### 6.1.2 Endpoint Embeddings: `/api/embed` (BUKAN `/api/embeddings`)

```php
// ✅ BETUL: Gunakan /api/embed
$response = Http::post($this->config['url'] . '/api/embed', $payload);
```

#### 6.1.3 Parameter Embeddings: `input` (BUKAN `prompt`)

```php
// ✅ BETUL: Gunakan 'input' parameter
$payload = [
    'model' => 'nomic-embed-text',
    'input' => $text,  // Parameter yang betul untuk /api/embed
];
```

#### 6.1.4 Model Embedding Khusus

Gunakan model embedding khusus (`nomic-embed-text`) untuk penjanaan embedding, bukan model chat (`gemma3:1b`).

### 6.2 Keperluan Inference Profile AWS Bedrock (KRITIKAL)

> **PENTING**: Direct model IDs tidak berfungsi dengan on-demand throughput. Gunakan inference profile format:

```env
# ❌ SALAH - Direct model ID
AWS_BEDROCK_MODEL_ID=anthropic.claude-opus-4-5-20251101-v1:0

# ✅ BETUL - Global inference profile (Opus 4.5)
AWS_BEDROCK_MODEL_ID=global.anthropic.claude-opus-4-5-20251101-v1:0

# ✅ BETUL - US inference profile (Sonnet/Haiku)
AWS_BEDROCK_MODEL_ID=us.anthropic.claude-haiku-4-5-20251001-v1:0
```

### 6.3 OllamaClient Service Interface

```php
interface OllamaClientContract
{
    public function generate(array $payload): array;
    public function embeddings(string $text): array;
    public function chat(array $messages): array;
    public function models(): array;
    public function healthCheck(): bool;
    public function getCachedResponse(string $cacheKey): ?array;
    public function cacheResponse(string $cacheKey, array $response, int $ttl): void;
}
```

### 6.4 BedrockClient Service Interface

```php
interface BedrockClientContract
{
    public function invokeModel(string $modelId, array $payload): array;
    public function invokeModelWithStreaming(string $modelId, array $payload): Generator;
    public function listFoundationModels(): array;
    public function getModelInfo(string $modelId): array;
    public function healthCheck(): bool;
    public function estimateCost(string $modelId, int $inputTokens, int $outputTokens): float;
}
```

### 6.5 Fasa Pelaksanaan (Implementation Phases)

Integrasi Cloud Hybrid AI dilaksanakan dalam **13 fasa** komprehensif:

| Fasa | Nama | Status | Komponen Utama |
|------|------|--------|----------------|
| 1 | Asas & Infrastruktur | ✅ Selesai | OllamaClient, config/ollama.php |
| 2 | Skema Pangkalan Data & Model | ✅ Selesai | Faq, Document, MessageLog models |
| 3 | Core AI Services | ✅ Selesai | RagService, DocumentService, EmbeddingService |
| 4 | Background Jobs & Queue | ✅ Selesai | DocumentIngestJob, EmbeddingJob |
| 5 | API Endpoints & Controllers | ✅ Selesai | FaqController, DocumentController |
| 6 | Filament Admin Interface | ✅ Selesai | FaqResource, DocumentResource |
| 7 | Security & Compliance | ✅ Selesai | PIIDetectionService, Policies |
| 8 | Livewire Components | ✅ Selesai | FaqBot, FaqBotWidget |
| 9 | Email Notifications | ✅ Selesai | ApprovalEmailToken, signed URLs |
| 10 | Performance Optimization | ✅ Selesai | Redis caching, Laravel Pulse |
| 11 | Testing & Documentation | ✅ Selesai | PHPUnit 12 tests, API docs |
| 12 | Deployment & Monitoring | ✅ Selesai | Health checks, alerting |
| 13 | Cloud Hybrid AI (Bedrock) | ✅ Selesai | BedrockService, ModelRouter |

---

## 7. Pengendalian Respons (Response Handling)

### 7.1 Struktur Respons (Response Structure)

Semua respons mengikut struktur bersepadu tanpa mengira sumber:

```php
[
    'content' => string,      // Teks respons AI
    'source' => string,       // 'ollama', 'bedrock', atau 'hybrid'
    'sources' => array,       // Sumber FAQ (jika berkenaan)
    'tokens' => ?int,         // Penggunaan token (Bedrock sahaja)
]
```

### 7.2 Atribusi Sumber (Source Attribution)

UI memaparkan penunjuk halus yang menunjukkan AI mana yang menyumbang:

| Sumber | Paparan | Ikon |
|--------|---------|------|
| `ollama` | "Sumber: Pangkalan Data FAQ" | 📚 Book |
| `bedrock` | "Dijana oleh: Claude {Model}" | 🤖 CPU |
| `hybrid` | "Gabungan: FAQ + AI Analysis" | ✨ Sparkles |

### 7.3 Strategi Fallback

```text
┌─────────────────────────────────────────────────────────┐
│                   Fallback Chain                         │
├─────────────────────────────────────────────────────────┤
│                                                          │
│  FAQ Query:                                              │
│  ┌──────────┐    ┌──────────┐    ┌──────────┐          │
│  │  Ollama  │───▶│ Bedrock  │───▶│  Error   │          │
│  │   RAG    │    │ Fallback │    │ Message  │          │
│  └──────────┘    └──────────┘    └──────────┘          │
│                                                          │
│  Hybrid Query:                                           │
│  ┌──────────┐    ┌──────────┐    ┌──────────┐          │
│  │ Ollama + │───▶│ Bedrock  │───▶│  Error   │          │
│  │ Bedrock  │    │   Only   │    │ Message  │          │
│  └──────────┘    └──────────┘    └──────────┘          │
│                                                          │
│  Complex Query:                                          │
│  ┌──────────┐    ┌──────────┐                           │
│  │ Bedrock  │───▶│  Error   │                           │
│  │  Direct  │    │ Message  │                           │
│  └──────────┘    └──────────┘                           │
│                                                          │
└─────────────────────────────────────────────────────────┘
```

---

## 8. Pengoptimuman Kos (Cost Optimization)

### 8.1 Perbandingan Harga (Pricing Comparison)

| Sistem AI | Kos | Kelajuan | Kes Penggunaan |
|-----------|-----|----------|----------------|
| **Ollama (Local)** | Percuma | Pantas | Pertanyaan FAQ, pangkalan pengetahuan |
| **Bedrock Haiku** | $0.25/1M tokens | Pantas | Penaakulan mudah |
| **Bedrock Sonnet** | $3/1M tokens | Sederhana | Tugas seimbang |
| **Bedrock Opus** | $15/1M tokens | Perlahan | Analisis kompleks |

### 8.2 Strategi Pengoptimuman

```text
┌─────────────────────────────────────────────────────────┐
│              Cost Optimization Flow                      │
├─────────────────────────────────────────────────────────┤
│                                                          │
│  Query Received                                          │
│       │                                                  │
│       ▼                                                  │
│  ┌─────────────┐                                        │
│  │   Analyze   │                                        │
│  │    Query    │                                        │
│  └─────────────┘                                        │
│       │                                                  │
│       ├──────────────────────────────────┐              │
│       │                                  │              │
│       ▼                                  ▼              │
│  ┌─────────────┐                  ┌─────────────┐      │
│  │ FAQ Query?  │──Yes──▶          │   Ollama    │ FREE │
│  └─────────────┘                  │    RAG      │      │
│       │                           └─────────────┘      │
│       │ No                               │              │
│       ▼                                  │              │
│  ┌─────────────┐                         │              │
│  │   Hybrid?   │──Yes──▶ Ollama + Haiku ─┘              │
│  └─────────────┘         (Minimal Cost)                 │
│       │                                                  │
│       │ No                                               │
│       ▼                                                  │
│  ┌─────────────┐                                        │
│  │  Use Model  │                                        │
│  │  Selected   │ (User choice: Haiku/Sonnet/Opus)      │
│  └─────────────┘                                        │
│                                                          │
└─────────────────────────────────────────────────────────┘
```

### 8.3 Jangkaan Penjimatan Kos (Expected Cost Savings)

| Jenis Pertanyaan | Tanpa Hibrid | Dengan Hibrid | Penjimatan |
|------------------|--------------|---------------|------------|
| FAQ (70%) | $3/1M tokens | $0 | 100% |
| Hybrid (20%) | $3/1M tokens | $0.25/1M | 92% |
| Complex (10%) | $3/1M tokens | $3/1M | 0% |
| **Purata** | **$3/1M** | **$0.55/1M** | **82%** |

---

## 9. Konfigurasi (Configuration)

### 9.1 Pembolehubah Persekitaran (Environment Variables)

```env
# AWS Bedrock Configuration
AWS_ACCESS_KEY_ID=your_access_key
AWS_SECRET_ACCESS_KEY=your_secret_key
AWS_BEDROCK_REGION=us-east-1
AWS_BEDROCK_MODEL_ID=us.anthropic.claude-sonnet-4-5-20250929-v1:0

# AWS Bedrock Nova Models
AWS_BEDROCK_MODEL_NOVA_MICRO=amazon.nova-micro-v1:0
AWS_BEDROCK_MODEL_NOVA_LITE=amazon.nova-lite-v1:0
AWS_BEDROCK_MODEL_NOVA_PRO=amazon.nova-pro-v1:0

# AWS Bedrock Titan Models
AWS_BEDROCK_MODEL_TITAN_TEXT_LITE=amazon.titan-text-lite-v1
AWS_BEDROCK_MODEL_TITAN_TEXT_EXPRESS=amazon.titan-text-express-v1
AWS_BEDROCK_MODEL_TITAN_EMBED_IMAGE=amazon.titan-embed-image-v1

# Ollama Configuration
OLLAMA_HOST=http://localhost:11434
OLLAMA_MODEL=llama3.1
OLLAMA_EMBEDDING_MODEL=nomic-embed-text

# Hybrid Configuration
HYBRID_AI_ENABLED=true
HYBRID_FAQ_THRESHOLD=0.3
HYBRID_FALLBACK_ENABLED=true

# Konfigurasi Cache dan Queue (Redis)
CACHE_DRIVER=redis
QUEUE_CONNECTION=redis
SESSION_DRIVER=redis

# Konfigurasi Broadcasting (Laravel Reverb)
BROADCAST_DRIVER=reverb
REVERB_APP_ID=ictserve-ai
REVERB_APP_KEY=your-reverb-key
REVERB_APP_SECRET=your-reverb-secret
REVERB_HOST=127.0.0.1
REVERB_PORT=6001
REVERB_SCHEME=http

# Konfigurasi Laravel Pulse
PULSE_ENABLED=true
PULSE_DOMAIN=pulse.ictserve.motac.gov.my
PULSE_PATH=pulse

# Konfigurasi Laravel Telescope (Superuser sahaja)
TELESCOPE_ENABLED=true
TELESCOPE_DOMAIN=telescope.ictserve.motac.gov.my
TELESCOPE_PATH=telescope

# Konfigurasi Audit dan Logging
AUDIT_ENABLED=true
AUDIT_DRIVER=database
ACTIVITY_LOGGER_ENABLED=true
```

### 9.2 Fail Konfigurasi Hibrid AI

Konfigurasi AI dipecahkan kepada beberapa fail (source of truth):
- `config/ollama.php` (endpoint, model, timeout, caching, RAG settings)
- `config/bedrock.php` (model ID, region, default model, retries)
- `config/ollama-laravel.php` (integrasi pakej `ollama-laravel`)
- `config/ai-broadcasting.php` (channel AI untuk Reverb/Echo)

Logik routing/strategi pemilihan model:
- `app/Services/ModelRouter.php`
- `app/Services/BedrockRoutingConfigurationService.php`
- `app/Services/RagService.php`

---

## 10. Rujukan API (API Reference)

### 10.1 Pengenalan API

API Integrasi AI Ollama menyediakan akses programmatik kepada ciri-ciri AI dalam sistem ICTServe termasuk FAQ Bot, Analisis Dokumen, dan Auto-Reply. Semua endpoint menggunakan pengesahan Laravel Sanctum dan mematuhi standard D00-D18 v3.6.1.

**Base URL:**

```text
https://ictserve.motac.gov.my/api/v1/ollama
```

**Pengesahan (Authentication):**

Semua endpoint memerlukan token Laravel Sanctum dalam header Authorization:

```text
Authorization: Bearer {token}
```

**Format Respons:**

```json
{
  "success": true|false,
  "data": {
    // Response data
  },
  "error": {
    "message": "Mesej ralat dalam Bahasa Melayu",
    "code": "ERROR_CODE"
  },
  "meta": {
    "request_id": "uuid",
    "timestamp": "2025-12-12T10:00:00Z"
  }
}
```

### 10.2 FAQ Bot API

#### POST /faq/query

Hantar pertanyaan kepada FAQ Bot AI.

**Keperluan Kebenaran:** `read:tickets` atau `admin:all`

**Parameter Permintaan:**

```json
{
  "query": "string (required, max:500)",
  "context": "string (optional, max:1000)"
}
```

**Contoh Permintaan:**

```bash
curl -X POST https://ictserve.motac.gov.my/api/v1/ollama/faq/query \
  -H "Authorization: Bearer {token}" \
  -H "Content-Type: application/json" \
  -d '{
    "query": "Bagaimana cara memohon pinjaman laptop?",
    "context": "Saya adalah staf baharu dan memerlukan laptop untuk kerja."
  }'
```

**Respons Berjaya:**

```json
{
  "success": true,
  "data": {
    "answer": "Untuk memohon pinjaman laptop, sila ikuti langkah berikut...",
    "confidence": 0.85,
    "sources": [
      {
        "title": "Panduan Pinjaman Aset ICT",
        "url": "/documents/panduan-pinjaman-aset"
      }
    ],
    "conversation_id": "uuid",
    "response_time": 2.3
  },
  "meta": {
    "request_id": "uuid",
    "timestamp": "2025-12-12T10:00:00Z"
  }
}
```

#### GET /faq/conversation/{id}

Dapatkan sejarah perbualan FAQ.

**Keperluan Kebenaran:** `read:tickets` atau `admin:all`

**Respons Berjaya:**

```json
{
  "success": true,
  "data": {
    "conversation_id": "uuid",
    "messages": [
      {
        "type": "user",
        "content": "Bagaimana cara reset password?",
        "timestamp": "2025-12-12T10:00:00Z"
      },
      {
        "type": "assistant",
        "content": "Untuk reset password, sila ikuti langkah berikut...",
        "confidence": 0.92,
        "timestamp": "2025-12-12T10:00:03Z"
      }
    ],
    "created_at": "2025-12-12T10:00:00Z",
    "expires_at": "2025-12-12T10:30:00Z"
  }
}
```

### 10.3 Document Analysis API

#### POST /documents/upload

Muat naik dokumen untuk analisis AI.

**Keperluan Kebenaran:** `admin:all`

**Parameter Permintaan (Multipart Form):**

```text
file: File (required, mimes:pdf,docx,txt, max:10MB)
metadata: JSON string (optional)
```

**Contoh Permintaan:**

```bash
curl -X POST https://ictserve.motac.gov.my/api/v1/ollama/documents/upload \
  -H "Authorization: Bearer {token}" \
  -F "file=@panduan-pengguna.pdf" \
  -F 'metadata={"category":"manual","department":"ICT"}'
```

**Respons Berjaya:**

```json
{
  "success": true,
  "data": {
    "document_id": 123,
    "filename": "panduan-pengguna.pdf",
    "status": "processing",
    "job_id": "uuid",
    "estimated_completion": "2025-12-12T10:05:00Z"
  }
}
```

#### GET /documents/{id}/status

Semak status pemprosesan dokumen.

**Respons Berjaya:**

```json
{
  "success": true,
  "data": {
    "document_id": 123,
    "filename": "panduan-pengguna.pdf",
    "status": "completed",
    "progress": 100,
    "chunks_created": 45,
    "embeddings_generated": 45,
    "processing_time": 120.5,
    "summary": "Dokumen ini mengandungi panduan lengkap untuk pengguna sistem ICTServe...",
    "key_topics": ["login", "pinjaman aset", "helpdesk", "laporan"]
  }
}
```

#### GET /documents/{id}/search

Cari dalam dokumen menggunakan semantic search.

**Parameter Query:**

- `query` (required): Pertanyaan carian
- `limit` (optional, default: 5): Bilangan hasil maksimum

**Respons Berjaya:**

```json
{
  "success": true,
  "data": {
    "query": "cara login",
    "results": [
      {
        "chunk_id": 5,
        "content": "Untuk log masuk ke sistem, masukkan e-mel atau nama pengguna...",
        "similarity": 0.89,
        "page": 12,
        "context": "Bab 3: Pengesahan Pengguna"
      }
    ],
    "total_results": 3,
    "search_time": 0.15
  }
}
```

### 10.4 Auto-Reply API

#### POST /auto-reply/generate

Jana draf auto-reply untuk tiket atau permohonan.

**Keperluan Kebenaran:** `admin:all`

**Parameter Permintaan:**

```json
{
  "replyable_type": "string (required: helpdesk_ticket|loan_application)",
  "replyable_id": "integer (required)",
  "template_id": "integer (optional)",
  "custom_context": "string (optional, max:1000)"
}
```

**Respons Berjaya:**

```json
{
  "success": true,
  "data": {
    "message": "Draf auto-reply sedang dijana. Anda akan menerima notifikasi apabila selesai.",
    "async": true,
    "job_id": "uuid",
    "request_id": "uuid",
    "estimated_completion": "2025-12-12T10:02:00Z"
  }
}
```

#### POST /auto-reply/{id}/approve

Luluskan draf auto-reply.

**Keperluan Kebenaran:** `admin:all` atau `approver` role

**Respons Berjaya:**

```json
{
  "success": true,
  "data": {
    "message": "Draf berjaya diluluskan dan akan dihantar kepada pengguna.",
    "draft": {
      "id": 789,
      "status": "approved",
      "approved_by": "Approver User",
      "approved_at": "2025-12-12T10:05:00Z"
    }
  }
}
```

#### POST /auto-reply/email-action

Tindakan kelulusan melalui e-mel (tanpa login).

**Keperluan Kebenaran:** Tiada (menggunakan token e-mel)

**Parameter Permintaan:**

```json
{
  "token": "string (required)",
  "action": "string (required: approve|reject)",
  "reason": "string (required jika action=reject)"
}
```

### 10.5 Health Check API

#### GET /health

Semak kesihatan sistem AI.

**Keperluan Kebenaran:** Tiada

**Respons Berjaya:**

```json
{
  "success": true,
  "data": {
    "status": "healthy",
    "services": {
      "ollama": {
        "status": "up",
        "response_time": 0.05,
        "model": "llama3.1",
        "last_check": "2025-12-12T10:00:00Z"
      },
      "database": {
        "status": "up",
        "response_time": 0.02
      },
      "redis": {
        "status": "up",
        "response_time": 0.01
      },
      "queue": {
        "status": "up",
        "pending_jobs": 3,
        "failed_jobs": 0
      }
    },
    "performance": {
      "avg_response_time": 2.1,
      "cache_hit_rate": 0.87,
      "uptime": "99.95%"
    }
  }
}
```

### 10.6 Had Kadar (Rate Limiting)

Semua endpoint tertakluk kepada had kadar berikut:

- **Per User**: 60 permintaan setiap minit
- **Per IP**: 1000 permintaan setiap jam
- **Burst Allowance**: 10 permintaan tambahan

**Header Respons Had Kadar:**

```text
X-RateLimit-Limit: 60
X-RateLimit-Remaining: 45
X-RateLimit-Reset: 1702377600
Retry-After: 60
```

### 10.7 Kod Ralat (Error Codes)

| Kod HTTP | Kod Ralat | Penerangan |
|----------|-----------|------------|
| 400 | `INVALID_REQUEST` | Permintaan tidak sah atau parameter hilang |
| 401 | `UNAUTHORIZED` | Token pengesahan tidak sah atau hilang |
| 403 | `FORBIDDEN` | Tiada kebenaran untuk mengakses sumber |
| 404 | `NOT_FOUND` | Sumber tidak dijumpai |
| 422 | `VALIDATION_ERROR` | Ralat pengesahan data |
| 429 | `RATE_LIMIT_EXCEEDED` | Had kadar melebihi |
| 500 | `INTERNAL_ERROR` | Ralat pelayan dalaman |
| 503 | `SERVICE_UNAVAILABLE` | Perkhidmatan tidak tersedia |

---

## 11. Panduan Deployment (Deployment Guide)

### 11.1 Pemasangan Ollama Server

#### Linux/Ubuntu

```bash
# Muat turun dan pasang Ollama
curl -fsSL https://ollama.ai/install.sh | sh

# Mulakan perkhidmatan Ollama
sudo systemctl start ollama
sudo systemctl enable ollama

# Semak status
sudo systemctl status ollama
```

#### Windows

```powershell
# Muat turun dari https://ollama.ai/download/windows
# Jalankan installer dan ikuti arahan

# Semak pemasangan
ollama --version
```

### 11.2 Konfigurasi Model AI

```bash
# Muat turun model yang disyorkan untuk pengeluaran
ollama pull llama3.1:8b-instruct-q4_K_M

# Semak model yang dipasang
ollama list

# Uji model
ollama run llama3.1:8b-instruct-q4_K_M "Halo, bagaimana anda hari ini?"
```

### 11.3 Konfigurasi Ollama untuk Pengeluaran

**Fail Konfigurasi:** `/etc/systemd/system/ollama.service`

```ini
[Unit]
Description=Ollama Service
After=network-online.target

[Service]
ExecStart=/usr/local/bin/ollama serve
User=ollama
Group=ollama
Restart=always
RestartSec=3
Environment="OLLAMA_HOST=127.0.0.1:11434"
Environment="OLLAMA_MODELS=/var/lib/ollama/models"
Environment="OLLAMA_KEEP_ALIVE=5m"
Environment="OLLAMA_MAX_LOADED_MODELS=1"

[Install]
WantedBy=default.target
```

```bash
sudo systemctl daemon-reload
sudo systemctl restart ollama
sudo systemctl status ollama
```

### 11.4 Konfigurasi Laravel ICTServe

#### Migration untuk AI Tables

```bash
# Jalankan semua migration (termasuk jadual AI yang berada dalam `database/migrations/`)
php artisan migrate

# (Opsyen) Semak jadual AI yang dicipta (jika DB driver menyokong)
php artisan db:show --table=faqs,documents,document_chunks,auto_reply_templates,auto_reply_drafts,message_logs,bedrock_conversations
```

#### Seeding Data Awal

```bash
# Tiada seeder khusus AI dalam repo v3.6.1.
# Data AI (FAQ, dokumen, templat auto-reply) diurus melalui Filament:
# - app/Filament/Resources/OllamaAI/*
#
# Seeder yang disediakan fokus kepada data rujukan (contoh):
# php artisan db:seed --class=DivisionSeeder
# php artisan db:seed --class=TicketCategorySeeder
```

### 11.5 Konfigurasi Queue dan Jobs

#### Laravel Queue (Redis) - Setup Semasa (Repo v3.6.1)

> **Nota**: `laravel/horizon` **tidak dipasang** dalam repo v3.6.1. Pemantauan queue menggunakan:
> - Laravel Pulse (metrik prestasi & job watcher)
> - Filament resources (contoh: Failed Jobs, Email Logs)

**Konfigurasi utama**:
- `config/queue.php` (default `QUEUE_CONNECTION`, sambungan Redis, failed jobs)
- `.env` / `.env.production` / `.env.staging` (contoh: `QUEUE_CONNECTION=redis`)

**Nama queue yang digunakan oleh AI jobs (contoh)**:
- `documents` (DocumentIngestJob)
- `embeddings` (EmbeddingJob)
- `auto-reply` (AutoReplyGenerationJob)
- `notifications` (notifikasi)
- `emails` (e-mel kelulusan/approval)
- `digests` (ringkasan notifikasi)

**Mulakan worker (contoh)**:

```bash
# Worker untuk Redis dengan pelbagai queue
php artisan queue:work redis --queue=default,notifications,emails,digests,documents,embeddings,auto-reply --tries=3 --timeout=1200
```

### 11.6 Konfigurasi Laravel Reverb (WebSocket)

```bash
# Mulakan Reverb server
php artisan reverb:serve --host=127.0.0.1 --port=8080 --scheme=http

# Atau gunakan supervisor untuk pengeluaran
sudo supervisorctl start reverb
```

**Konfigurasi Nginx untuk WebSocket:**

```nginx
# Tambah ke konfigurasi Nginx
location /app/ {
    proxy_pass http://127.0.0.1:8080;
    proxy_http_version 1.1;
    proxy_set_header Upgrade $http_upgrade;
    proxy_set_header Connection "upgrade";
    proxy_set_header Host $host;
    proxy_set_header X-Real-IP $remote_addr;
    proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for;
    proxy_set_header X-Forwarded-Proto $scheme;
}
```

### 11.7 Konfigurasi Keselamatan

#### SSL/TLS Configuration

```nginx
server {
    listen 443 ssl http2;
    server_name ictserve.motac.gov.my;
    
    ssl_certificate /path/to/ssl/certificate.crt;
    ssl_certificate_key /path/to/ssl/private.key;
    ssl_protocols TLSv1.2 TLSv1.3;
    ssl_ciphers ECDHE-RSA-AES256-GCM-SHA512:DHE-RSA-AES256-GCM-SHA512;
    
    root /var/www/ictserve/public;
    index index.php;
    
    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }
    
    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }
}
```

#### Firewall Configuration

```bash
# UFW Firewall rules
sudo ufw allow 22/tcp    # SSH
sudo ufw allow 80/tcp    # HTTP
sudo ufw allow 443/tcp   # HTTPS
sudo ufw allow 6001/tcp  # Reverb WebSocket (internal only)
sudo ufw deny 11434/tcp  # Ollama (internal only)

# Aktifkan firewall
sudo ufw enable
```

### 11.8 Prosedur Backup

#### Script Backup Automatik

```bash
#!/bin/bash
# backup-ictserve-ai.sh

DATE=$(date +%Y%m%d_%H%M%S)
BACKUP_DIR="/backup/ictserve-ai"
DB_NAME="ictserve"

# Backup MySQL
mysqldump -u backup_user -p$MYSQL_BACKUP_PASSWORD \
    --single-transaction \
    --routines \
    --triggers \
    $DB_NAME > $BACKUP_DIR/mysql_$DATE.sql

# Backup Ollama models
tar -czf $BACKUP_DIR/ollama_models_$DATE.tar.gz /var/lib/ollama/models/

# Backup Laravel storage
tar -czf $BACKUP_DIR/laravel_storage_$DATE.tar.gz /var/www/ictserve/storage/

# Cleanup old backups (keep 30 days)
find $BACKUP_DIR -name "*.sql" -mtime +30 -delete
find $BACKUP_DIR -name "*.tar.gz" -mtime +30 -delete

echo "Backup completed: $DATE"
```

#### Cron Job untuk Backup Harian

```bash
# Tambah ke crontab
0 2 * * * /usr/local/bin/backup-ictserve-ai.sh >> /var/log/backup.log 2>&1
```

### 11.9 Health Check dan Monitoring

#### Script Health Check

```bash
#!/bin/bash
# health-check-ictserve-ai.sh

API_URL="https://ictserve.motac.gov.my/api/v1/ollama"
LOG_FILE="/var/log/ictserve-health.log"

# Check API health
HTTP_STATUS=$(curl -s -o /dev/null -w "%{http_code}" $API_URL/health)

if [ $HTTP_STATUS -eq 200 ]; then
    echo "$(date): API Health OK" >> $LOG_FILE
else
    echo "$(date): API Health FAILED - Status: $HTTP_STATUS" >> $LOG_FILE
    # Send alert email
    echo "ICTServe AI API health check failed" | mail -s "ICTServe Alert" admin@motac.gov.my
fi

# Check Ollama server
OLLAMA_STATUS=$(curl -s http://127.0.0.1:11434/api/tags | jq -r '.models | length')

if [ "$OLLAMA_STATUS" -gt 0 ]; then
    echo "$(date): Ollama Server OK - Models: $OLLAMA_STATUS" >> $LOG_FILE
else
    echo "$(date): Ollama Server FAILED" >> $LOG_FILE
    # Restart Ollama
    sudo systemctl restart ollama
fi
```

---

## 12. Prosedur Kecemasan (Emergency Procedures)

### 12.1 Kontak Kecemasan (Emergency Contacts)

#### Pasukan Respons Kecemasan (24/7)

| Peranan | Nama | Telefon Pejabat | Telefon Bimbit | E-mel | Waktu Respons |
|---------|------|----------------|----------------|-------|---------------|
| **Lead Developer** |  |  |  |  | 15 minit |
| **System Administrator** |  |  |  |  | 30 minit |
| **Database Administrator** |  |  |  |  | 30 minit |
| **Network Administrator** |  |  |  |  | 30 minit |

#### Pengurusan Atasan

| Peranan | Nama | Telefon | E-mel | Untuk Eskalasi |
|---------|------|---------|-------|----------------|
| **Ketua Bahagian ICT** |  |  |  | Tahap 3+ |
| **Pengarah BPM** |  |  |  | Tahap 4+ |

### 12.2 Tahap Kecemasan (Emergency Levels)

#### Tahap 1: Masalah Kecil (Minor Issues)

**Kriteria:**

- Masa respons API > 5 saat tetapi < 10 saat
- Penggunaan CPU/memori 80-90%
- Beberapa ralat dalam log (< 10 dalam sejam)
- Cache hit rate < 80%

**Tindakan:**

- Hubungi System Administrator
- Pantau melalui Laravel Pulse dashboard
- Dokumentasikan dalam log sistem

**Masa Respons:** 30 minit

#### Tahap 2: Masalah Sederhana (Moderate Issues)

**Kriteria:**

- Masa respons API > 10 saat
- Penggunaan CPU/memori > 90%
- Perkhidmatan terdegradasi tetapi masih boleh diakses
- Banyak ralat dalam log (10-50 dalam sejam)
- Ollama server tidak responsif

**Tindakan:**

- Hubungi Lead Developer dan System Administrator
- Aktifkan graceful degradation
- Restart perkhidmatan yang bermasalah
- Notifikasi pengguna melalui sistem

**Masa Respons:** 15 minit

#### Tahap 3: Masalah Kritikal (Critical Issues)

**Kriteria:**

- API tidak boleh diakses sama sekali
- Database connection gagal
- Perkhidmatan utama down
- Ralat kritikal berterusan (> 50 dalam sejam)
- Keselamatan sistem terjejas

**Tindakan:**

- Hubungi semua ahli pasukan teknikal
- Aktifkan prosedur disaster recovery
- Pertimbangkan rollback ke versi stabil
- Notifikasi pengurusan atasan
- Dokumentasikan semua tindakan

**Masa Respons:** 5 minit

#### Tahap 4: Bencana Sistem (System Disaster)

**Kriteria:**

- Kehilangan data kritikal
- Kerosakan perkakasan utama
- Serangan keselamatan yang berjaya
- Sistem tidak dapat dipulihkan dalam 4 jam

**Tindakan:**

- Hubungi semua kontak kecemasan
- Aktifkan disaster recovery site
- Hubungi vendor perkakasan/perisian
- Notifikasi pengurusan tertinggi
- Aktifkan protokol komunikasi krisis

**Masa Respons:** Segera

### 12.3 Prosedur Pemulihan Khusus

#### Pemulihan Ollama Server Crash

**Gejala:**

- API mengembalikan "SERVICE_UNAVAILABLE"
- Ollama process tidak berjalan
- Model tidak dapat diakses

**Tindakan Pemulihan:**

```bash
# Semak status Ollama
sudo systemctl status ollama
ps aux | grep ollama

# Restart Ollama service
sudo systemctl restart ollama

# Semak model yang tersedia
ollama list

# Re-download model jika perlu
ollama pull llama3.1:8b-instruct-q4_K_M

# Test model
ollama run llama3.1:8b-instruct-q4_K_M "Test message"

# Restart aplikasi services
sudo supervisorctl restart ictserve-horizon
```

#### Pemulihan Memory Exhaustion

**Gejala:**

- "Out of memory" errors dalam log
- Aplikasi menjadi sangat perlahan
- Process killed oleh OOM killer

**Tindakan Pemulihan:**

```bash
# Semak penggunaan memori
free -h
ps aux --sort=-%mem | head -20

# Restart perkhidmatan yang menggunakan memori tinggi
sudo systemctl restart php8.2-fpm
sudo supervisorctl restart ictserve-horizon

# Tukar ke model AI yang lebih kecil sementara
ollama pull llama3.1:7b-instruct-q4_K_M

# Update konfigurasi sementara
cd /var/www/ictserve
sed -i 's/OLLAMA_MODEL=.*/OLLAMA_MODEL=llama3.1:7b-instruct-q4_K_M/' .env
php artisan config:cache

# Clear semua cache
php artisan cache:clear
redis-cli FLUSHALL
```

### 12.4 Prosedur Rollback Kecemasan

#### Rollback Pantas (Quick Rollback)

```bash
# Rollback automatik ke backup terkini
/usr/local/bin/rollback.sh --app-backup /backup/ictserve-releases/ictserve_backup_latest.tar.gz

# Atau rollback interaktif
/usr/local/bin/rollback.sh --interactive
```

#### Rollback Database Sahaja

```bash
# Jika hanya database bermasalah
cd /var/www/ictserve
php artisan down

# Restore database
mysql -u root -p ictserve_production < /backup/ictserve-releases/database_backup_latest.sql

# Run migrations jika perlu
php artisan migrate --force

php artisan up
```

### 12.5 Senarai Semak Pasca-Kecemasan

Selepas Pemulihan Sistem:

- [ ] **Sistem Berfungsi**: Semua perkhidmatan berjalan normal
- [ ] **Data Integrity**: Semua data penting utuh dan boleh diakses
- [ ] **Performance**: Masa respons dalam had normal
- [ ] **Security**: Tiada kelemahan keselamatan yang terbuka
- [ ] **Monitoring**: Semua sistem pemantauan aktif
- [ ] **Backup**: Backup terkini telah dibuat
- [ ] **Documentation**: Semua tindakan didokumentasikan
- [ ] **Notification**: Pengguna dimaklumkan sistem telah pulih
- [ ] **Post-Mortem**: Analisis punca masalah dijadualkan

---

## 13. Pengujian (Testing)

### 13.1 Unit Tests

**Contoh (pseudo) untuk konsep routing** (bukan fail sebenar; hanya contoh struktur ujian)

**Fail sebenar untuk rujukan (source of truth)**:
- `tests/Unit/Services/ModelRouterTest.php`
- `tests/Unit/Services/RagServiceTest.php`
- `tests/Unit/Services/OllamaClientTest.php`
- `tests/Unit/Services/BedrockServiceTest.php`

```php
<?php

declare(strict_types=1);

namespace Tests\Unit\Services;

use App\Services\BedrockService;
use App\Services\RagService;
use PHPUnit\Framework\Attributes\Test;
use PHPU\Framework\Attributes\DataProvider;
use Tests\TestCase;
use Mockery;

class HybridAiServiceTest extends TestCase
{
    #[Test]
    public function it_routes_faq_queries_to_ollama(): void
    {
        $ragService = Mockery::mock(RagService::class);
        $ragService->shouldReceive('processQuery')
            ->once()
            ->andReturn([
                'success' => true,
                'answer' => 'FAQ response',
                'sources' => []
            ]);

        $this->app->instance(RagService::class, $ragService);

        $response = $this->analyzeAndRoute('Bagaimana cara menghantar tiket helpdesk?');

        $this->assertEquals('ollama', $response['source']);
    }

    #[Test]
    public function it_routes_complex_queries_to_bedrock(): void
    {
        $bedrockService = Mockery::mock(BedrockService::class);
        $bedrockService->shouldReceive('invoke')
            ->once()
            ->andReturn([
                'success' => true,
                'content' => 'Bedrock response',
                'usage' => ['output_tokens' => 100]
            ]);

        $this->app->instance(BedrockService::class, $bedrockService);

        $response = $this->analyzeAndRoute('Analisis kelebihan cloud computing');

        $this->assertEquals('bedrock', $response['source']);
    }

    #[Test]
    public function it_handles_hybrid_queries_with_both_systems(): void
    {
        $ragService = Mockery::mock(RagService::class);
        $ragService->shouldReceive('processQuery')
            ->once()
            ->andReturn([
                'success' => true,
                'answer' => 'FAQ facts',
                'sources' => ['source1']
            ]);

        $bedrockService = Mockery::mock(BedrockService::class);
        $bedrockService->shouldReceive('invoke')
            ->once()
            ->andReturn([
                'success' => true,
                'content' => 'Enhanceresponse',
                'usage' => ['output_tokens' => 150]
            ]);

        $this->app->instance(RagService::class, $ragService);
        $this->app->instance(BedrockService::class, $bedrockService);

        $response = $this->analyzeAndRoute('Mengapa sistem pinjaman perlu kelulusan Gred 41?');

        $this->assertEquals('hybrid', $response['source']);
    }

    #[Test]
    public function it_falls_back_to_bedrock_when_ollama_fails(): void
    {
        $ragService = Mockery::mock(RagService::class);
        $ragService->shouldReceive('processQuery')
            ->once()
            ->andThrow(new \Exception('Ollama unavailable'));

        $bedrockService = Mockery::mock(BedrockService::class);
        $bedrockService->shouldReceive('invoke')
            ->once()
            ->andReturn([
                'success' => true,
                'content' => 'Fallback response',
                'usage' => ['output_tokens' => 80]
            ]);

        $this->app->instance(RagService::class, $ragService);
        $this->app->instance(BedrockService::class, $bedrockService);

        $response = $this->analyzeAndRoute('Bagaimana cara menghantar tiket?');

        $this->assertEquals('bedrock', $response['source']);
    }

    #[Test]
    #[DataProvider('queryClassificationProvider')]
    public function it_correctly_classifies_queries(
        string $query,
        string $expectedType
    ): void {
        $type = $this->classifyQuery($query);
        $this->assertEquals($expectedType, $type);
    }

    public static function queryClassificationProvider(): array
    {
        return [
            'faq_tiket' => ['Bagaimana hantar tiket?', 'faq_specific'],
      'faq_pinjaman' => ['Status permohonan pinjaman aset', 'faq_specific'],
            'complex_analisis' => ['Analisis sistem ini', 'complex_reasoning'],
            'complex_bandingkan' => ['Bandingkan dua pendekatan', 'complex_reasoning'],
            'hybrid_mengapa_tiket' => ['Mengapa tiket perlu SLA?', 'hybrid'],
            'hybrid_jelaskan_pinjaman' => ['Jelaskan proses kelulusan pinjaman', 'hybrid'],
        ];
    }
}
```

### 13.2 Feature Tests

**Contoh (pseudo) untuk konsep UI hibrid** (bukan fail sebenar; hanya contoh struktur ujian)

**Fail sebenar untuk rujukan (source of truth)**:
- `tests/Feature/BedrockChatTest.php`
- `tests/Feature/AI/ModelRouterTest.php`
- `tests/Feature/AI/HybridQueryRouterTest.php`
- `tests/Feature/AI/HybridPerformanceTest.php`

```php
<?php

declare(strict_types=1);

namespace Tests\Feature\Livewire;

use App\Livewire\BedrockChat;
use App\Models\User;
use App\Services\BedrockService;
use App\Services\RagService;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;
use Mockery;

class BedrockChatHybridTest extends TestCase
{
    #[Test]
    public function it_displays_faq_suggestions_with_context(): void
    {
        $user = User::factory()->create();

        Livewire::actingAs($user)
            ->test(BedrockChat::class, ['context' => 'faq'])
            ->assertSet('context', 'faq')
            ->assertNotEmpty('faqSuggestions');
    }

    #[Test]
    public function it_sends_faq_query_to_ollama(): void
    {
        $user = User::factory()->create();

        $ragService = Mockery::mock(RagService::class);
        $ragService->shouldReceive('processQuery')
            ->once()
            ->andReturn([
                'success' => true,
                'answer' => 'Untuk menghantar tiket...',
                'sources' => ['FAQ Guide']
            ]);

        $this->app->instance(RagService::class, $ragService);

        Livewire::actingAs($user)
            ->test(BedrockChat::class)
            ->set('prompt', 'Bagaimana cara menghantar tiket helpdesk?')
            ->call('send')
            ->assertSet('prompt', '')
            ->assertCount('messages', 2);
    }

    #[Test]
    public function it_shows_source_attribution_for_ollama_response(): void
    {
        $user = User::factory()->create();

        $ragService = Mockery::mock(RagService::class);
        $ragService->shouldReceive('processQuery')
            ->andReturn([
                'success' => true,
                'answer' => 'FAQ response',
                'sources' => ['Source 1']
            ]);

        $this->app->instance(RagService::class, $ragService);

        Livewire::actingAs($user)
            ->test(BedrockChat::class)
            ->set('prompt', 'Status tiket saya')
            ->call('send')
            ->assertSee('Sumber: Pangkalan Data FAQ');
    }

    #[Test]
    public function it_handles_hybrid_query_with_both_systems(): void
    {
        $user = User::factory()->create();

        $ragService = Mockery::mock(RagService::class);
        $ragService->shouldReceive('processQuery')
            ->andReturn([
                'success' => true,
                'answer' => 'FAQ facts',
                'sources' => []
            ]);

        $bedrockService = Mockery::mock(BedrockService::class);
        $bedrockService->shouldReceive('invoke')
            ->andReturn([
                'success' => true,            'content' => 'Enhanced analysis',
                'usage' => ['output_tokens' => 100]
            ]);

        $this->app->instance(RagService::class, $ragService);
        $this->app->instance(BedrockService::class, $bedrockService);

        Livewire::actingAs($user)
            ->test(BedrockChat::class)
            ->set('prompt', 'Mengapa pinjaman perlu kelulusan Gred 41?')
            ->call('send')
            ->assertSee('Gabungan: FAQ + AI Analysis');
    }
}
```

### 13.3 Menjalankan Ujian (Running Tests)

```bash
# Run all hybrid AI tests
php artisan test --filter=Hybrid

# Run specific test file
php artisan test tests/Feature/BedrockChatTest.php

# Run with coverage
php artisan test --filter=Hybrid --coverage --min=80
```

---

## 14. Penyelesaian Masalah (Troubleshooting)

### 14.1 Masalah Biasa dan Penyelesaian

#### 14.1.1 Ollama Connection Failed

**Gejala:**

- Pertanyaan FAQ sentiasa fallback ke Bedrock
- Error: "Connection refused to localhost:11434"

**Penyelesaian:**

```bash
# Check if Ollama is running
curl http://localhost:114

# Start Ollama service
ollama seVerify model is available
ollama list
```

#### 14.1.2 Bedrock Authentication Error

**Gejala:**

- Pertanyaan kompleks gagal dengan ralat 403
- Error: "UnauthorizedException"

**Penyelesaian:**

```bash
# Verify AWS credentials
aws sts get-caller-identity

# Check Bedrock model access
aws bedrock list-foundation-models --region us-east-1

# Ensure model is enabled in AWS Console
```

#### 14.1.3 Model Access Denied

```text
Error: ValidationException: You don't have access to the model
Fix: Enable model in AWS Bedrock Console → Model access → Manage model access
Wait: 2-5 minutes for approval
```

#### 14.1.4 Inference Profile Required (KRITIKAL)

```text
Error: ValidationException: The provided model identifier is invalid
Cause: Direct model IDs don't work with on-demand throughput
Fix: Use inference profile format (global.* or us.*)
```

#### 14.1.5 Livewire toJSON Error

```text
Error: Uncaught TypeError: component.toJSON is not a function
Causes: Multiple root elements in Blade view, complex objects in properties
Fixes:
1. Ensure single root div in Blade view
2. Pass collections to view, don't store in properties
3. Clear all caches: php artisan optimize:clear
```

#### 14.1.6 Markdown Not Rendering

```bash
# Install dependencies
composer require league/commonmark
npm install @tailwindcss/typography
npm run build
```

#### 14.1.7 DuckDuckGo Search Returns Empty

```php
// Use HTML endpoint with regex parsing
$url = 'https://html.duckduckgo.com/html/?q=' . urlencode($query);
preg_match_all('/<a class="result__snippet"[^>]*>(.*?)<\/a>/s', $html, $matches);
```

### 14.2 Arahan Debugging (Debugging Commands)

```bash
# Check Service Registration
php artisan tinker
$bedrock = app(\App\Services\BedrockService::class);
dd($bedrock);

# Check Configuration
config('bedrock.region');
config('bedrock.model_id');

# Check Database
\App\Models\BedrockConversation::count();
\App\Models\BedrockConversation::latest()->first();

# Check Routes
php artisan route:list | grep bedrock

# Check Livewire Components
php artisan livewire:list
```

### 14.3 Lokasi Log (Log Locations)

- **Laravel**: `storage/logs/laravel.log`
- **MCP Server (debugging)**: rujuk `routes/ai.php`, `docs/Laravel_MCP.md`, dan log aplikasi di `storage/logs/`
- **Browser Console**: F12 → Console tab
- **Network**: F12 → Network tab (check Livewire requests)

### 14.4 Health Check Endpoint

```php
// routes/api.php
Route::get('/health/hybrid-ai', function () {
    $ollama = app(RagService::class)->healthCheck();
    $bedrock = app(BedrockService::class)->healthCheck();

    return response()->json([
        'status' => ($ollama && $bedrock) ? 'healthy' : 'degraded',
        'ollama' => $ollama ? 'up' : 'down',
        'bedrock' => $bedrock ? 'up' : 'down',
        'timestamp' => now()->toIso8601String(),
    ]);
});
```

---

## 15. Pematuhan D00-D18 v3.6.1 (Compliance)

### 15.1 Matriks Pematuhan (Compliance Matrix)

Integrasi Cloud Hybrid AI mematuhi sepenuhnya dokumentasi D00-D18 v3.6.1:

| Dokumen | Keperluan | Pelaksanaan Sistem AI |
|---------|-----------|----------------------|
| **D00** | True Hybrid Architecture | Nullable user_id FK, Self-Registration, Account Linking |
| **D03** | 38+ SRS Requirements | 9 keperluan khusus AI (Req 1-9) |
| **D04** | Architecture Patterns | Service Layer, RAG Pipeline, Model Router |
| **D09** | Dual Audit System | owen-it (compliance) + spatie (operations) |
| **D11** | Technical Infrastructure | Laravel Pulse + Telescope + Sanctum + Reverb |
| **D12-D14** | WCAG 2.2 AA | Accessible streaming UI, 4.5:1 contrast |
| **D15** | Bahasa Melayu sahaja | No language switcher, all AI responses in Malay |
| **D16** | Laravel Reverb | Real-time AI notifications via WebSocket |
| **D17** | Laravel Queue + Redis | Queue management untuk AI jobs (Horizon tidak dipasang) |

### 15.2 Butiran Pematuhan (Compliance Details)

#### 15.2.1 D00 - True Hybrid Architecture

- **Nullable user_id FK**: Semua model AI (Faq, Document, MessageLog) menyokong nullable user_id untuk akses tetamu
- **Self-Registration**: Staf MOTAC boleh mendaftar sendiri dengan e-mel @motac.gov.my
- **Account Linking**: Penyerahan tetamu terdahulu boleh dipautkan ke akaun authenticated

#### 15.2.2 D03 - Software Requirements Specification

9 keperluan khusus AI telah dilaksanakan:

| Keperluan | Penerangan | Status |
|-----------|------------|--------|
| Req 1 | FAQ Bot dengan RAG | ✅ Selesai |
| Req 2 | Document Analysis | ✅ Selesai |
| Req 3 | Auto-Reply Generation | ✅ Selesai |
| Req 4 | Multi-Model Intelligence | ✅ Selesai |
| Req 5 | Streaming Responses | 🔄 Future |
| Req 6 | Web-Augmented Responses | ✅ Selesai |
| Req 7 | Conversation Management | ✅ Selesai |
| Req 8 | MCP Server Integration | ✅ Selesai |
| Req 9 | AWS Bedrock Integration | ✅ Selesai |

#### 15.2.3 D04 - Software Design Document

- **Service Layer**: OllamaClient, BedrockClient, RagService, DocumentService
- **RAG Pipeline**: EmbeddingService, Vector Search, Context Retrieval
- **Model Router**: Smart model selection berdasarkan task complexity

#### 15.2.4 D09 - Database Documentation

Dual Audit System untuk semua operasi AI:

```php
// owen-it (Compliance Audit)
use OwenIt\Auditing\Contracts\Auditable;

class MessageLog extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;
}

// spatie (Operations Audit)
use Spatie\Activitylog\Traits\LogsActivity;

class BedrockConversation extends Model
{
    use LogsActivity;
}
```

#### 15.2.5 D11 - Technical Design Documentation

- **Laravel Pulse**: Real-time performance monitoring untuk AI services
- **Laravel Telescope**: Debugging untuk superuser sahaja
- **Laravel Sanctum**: API token authentication untuk AI endpoints
- **Laravel Reverb**: WebSocket untuk real-time AI notifications

#### 15.2.6 D12-D14 - UI/UX Design Standards

- **WCAG 2.2 AA**: Semua komponen AI mematuhi accessibility standards
- **Color Contrast**: 4.5:1 untuk teks, 3:1 untuk UI elements
- **Focus Indicators**: 3px outline dengan 2px offset
- **Touch Targets**: Minimum 44x44px untuk interactive elements

#### 15.2.7 D15 - Language Standards

- **Bahasa Melayu sahaja**: Semua respons AI dalam Bahasa Melayu
- **No Language Switcher**: Penukar bahasa dilumpuhkan (v3.6.0)
- **BilingualSupportService**: Sentiasa return 'ms' locale

#### 15.2.8 D16 - Broadcasting Setup

- **Laravel Reverb**: WebSocket server untuk real-time AI notifications
- **Echo Integration**: Client-side WebSocket handling
- **Channel Authorization**: Private channels untuk authenticated users

#### 15.2.9 D17 - Queue Management

- **Laravel Queue + Redis**: Pemprosesan job latar belakang untuk AI (Horizon tidak dipasang)
- **Job Types**: DocumentIngestJob, EmbeddingJob, AutoReplyGenerationJob
- **Redis Driver**: High-performance queue backend

### 15.3 Senarai Semak Pematuhan (Compliance Checklist)

#### Pre-Deployment

- [x] True Hybrid Architecture (nullable user_id FK)
- [x] Dual Audit System (owen-it + spatie)
- [x] Bahasa Melayu sahaja (no language switcher)
- [x] WCAG 2.2 AA compliance
- [x] Core Web Vitals targets met
- [x] Data residency Malaysia

#### Post-Deployment Verification

- [ ] Homepage loads successfully
- [ ] API health endpoint responds
- [ ] FAQ Bot responds in Bahasa Melayu
- [ ] WebSocket connections working
- [ ] Queue jobs processing
- [ ] Monitoring dashboards accessible

---

## Lampiran (Appendix)

### A. Fail Konfigurasi (Configuration Files)

#### A.1 config/ollama.php

```php
<?php

return [
    'model' => env('OLLAMA_MODEL', 'llama3.1'),
    'url' => env('OLLAMA_URL', 'http://127.0.0.1:11434'),
    'default_prompt' => env('OLLAMA_DEFAULT_PROMPT', 'Bagaimana saya boleh membantu anda hari ini?'),
    'connection' => [
        'timeout' => env('OLLAMA_CONNECTION_TIMEOUT', 300),
        'retry_attempts' => 3,
        'retry_delay' => 1000,
    ],
    'cache' => [
        'enabled' => env('OLLAMA_CACHE_ENABLED', true),
        'ttl' => env('OLLAMA_CACHE_TTL', 3600),
        'driver' => env('OLLAMA_CACHE_DRIVER', 'redis'),
    ],
    'performance' => [
        'max_response_time' => 5,
        'quantized_model' => env('OLLAMA_QUANTIZED_MODEL', true),
        'context_window' => 4096,
    ],
    'rate_limiting' => [
        'per_user' => 60,
        'per_ip' => 1000,
    ],
];
```

#### A.2 config/bedrock.php

```php
<?php

return [
    'enabled' => env('BEDROCK_ENABLED', false),
    'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    'models' => [
        'default' => env('BEDROCK_DEFAULT_MODEL', 'us.anthropic.claude-sonnet-4-5-20250929-v1:0'),
        'opus' => env('AWS_BEDROCK_MODEL_OPUS', 'global.anthropic.claude-opus-4-5-20251101-v1:0'),
        'sonnet' => env('AWS_BEDROCK_MODEL_SONNET', 'us.anthropic.claude-sonnet-4-5-20250929-v1:0'),
        'haiku' => env('AWS_BEDROCK_MODEL_HAIKU', 'us.anthropic.claude-haiku-4-5-20251001-v1:0'),
        'nova_micro' => env('AWS_BEDROCK_MODEL_NOVA_MICRO', 'amazon.nova-micro-v1:0'),
        'nova_lite' => env('AWS_BEDROCK_MODEL_NOVA_LITE', 'amazon.nova-lite-v1:0'),
        'nova_pro' => env('AWS_BEDROCK_MODEL_NOVA_PRO', 'amazon.nova-pro-v1:0'),
        'titan_text_lite' => env('AWS_BEDROCK_MODEL_TITAN_TEXT_LITE', 'amazon.titan-text-lite-v1'),
        'titan_text_express' => env('AWS_BEDROCK_MODEL_TITAN_TEXT_EXPRESS', 'amazon.titan-text-express-v1'),
        'high_quality' => 'us.anthropic.claude-sonnet-4-5-20250929-v1:0',
        'fast_response' => 'amazon.nova-lite-v1:0',
        'cost_effective' => 'amazon.nova-micro-v1:0',
    ],
    'routing' => [
        'faq_simple' => 'nova_lite',
        'faq_complex' => 'sonnet',
        'document_analysis' => 'sonnet',
        'auto_reply' => 'titan_text_express',
        'status_check' => 'nova_micro',
        'multimodal' => 'nova_pro',
    ],
    'rate_limits' => [
        'opus' => ['rpm' => 10, 'tpm' => 20000],
        'sonnet' => ['rpm' => 20, 'tpm' => 40000],
        'haiku' => ['rpm' => 50, 'tpm' => 100000],
        'nova_micro' => ['rpm' => 100, 'tpm' => 150000],
        'nova_lite' => ['rpm' => 80, 'tpm' => 120000],
        'nova_pro' => ['rpm' => 40, 'tpm' => 60000],
        'titan_text_lite' => ['rpm' => 60, 'tpm' => 80000],
        'titan_text_express' => ['rpm' => 30, 'tpm' => 50000],
    ],
    'streaming' => [
        'enabled' => env('BEDROCK_STREAMING_ENABLED', true),
        'chunk_size' => 1024,
        'timeout' => 30,
    ],
    'web_search' => [
        'enabled' => env('BEDROCK_WEB_SEARCH_ENABLED', false),
        'provider' => env('WEB_SEARCH_PROVIDER', 'duckduckgo'),
        'max_results' => 5,
    ],
    'data_residency' => [
        'enforce_malaysia' => env('BEDROCK_ENFORCE_MALAYSIA_RESIDENCY', true),
        'allowed_regions' => ['ap-southeast-1'],
        'data_classification' => [
            'public' => 'allow_cloud',
            'internal' => 'local_only',
            'confidential' => 'local_only',
            'restricted' => 'local_only',
        ],
    ],
    'performance' => [
        'max_response_time' => 3,
        'retry_attempts' => 2,
        'fallback_to_ollama' => true,
    ],
];
```

### B. Rujukan Dokumentasi Tambahan (Additional Documentation References)

#### B.1 Dokumentasi API

| Dokumen | Penerangan | Lokasi |
|---------|------------|--------|
| [ollama-ai-api-documentation.md](ollama/api/ollama-ai-api-documentation.md) | Rujukan API penuh | `docs/ollama/api/` |
| [ollama-ai-integration-api.md](ollama/api/ollama-ai-integration-api.md) | Panduan integrasi | `docs/ollama/api/` |
| [API_AUTHENTICATION_IMPLEMENTATION.md](ollama/api/API_AUTHENTICATION_IMPLEMENTATION.md) | Pelaksanaan auth | `docs/ollama/api/` |
| [ollama-openapi-spec.yaml](ollama/api/ollama-openapi-spec.yaml) | OpenAPI 3.0 spec | `docs/ollama/api/` |

#### B.2 Dokumentasi Deployment

| Dokumen | Penerangan | Lokasi |
|---------|------------|--------|
| [ollama-ai-deployment-guide.md](ollama/deployment/ollama-ai-deployment-guide.md) | Panduan deployment penuh | `docs/ollama/deployment/` |
| [ollama-ai-production-deployment.md](ollama/deployment/ollama-ai-production-deployment.md) | Setup pengeluaran | `docs/ollama/deployment/` |
| [emergency-procedures.md](ollama/deployment/emergency-procedures.md) | Prosedur kecemasan | `docs/ollama/deployment/` |
| [ollama-ai-deployment-checklist.md](ollama/deployment/ollama-ai-deployment-checklist.md) | Senarai semak deployment | `docs/ollama/deployment/` |
| [production-environment-setup.md](ollama/deployment/production-environment-setup.md) | Setup persekitaran | `docs/ollama/deployment/` |

#### B.3 Dokumentasi Spesifikasi

| Dokumen | Penerangan | Lokasi |
|---------|------------|--------|
| [requirements.md](.kiro/specs/ollama-ai-integration/requirements.md) | Keperluan v3.6.1 | `.kiro/specs/ollama-ai-integration/` |
| [design.md](.kiro/specs/ollama-ai-integration/design.md) | Reka bentuk v3.6.1 | `.kiro/specs/ollama-ai-integration/` |
| [tasks.md](.kiro/specs/ollama-ai-integration/tasks.md) | Tugas v3.6.6 | `.kiro/specs/ollama-ai-integration/` |

---

**Dokumen ini mematuhi D00-D18 v3.6.1 dan menyediakan panduan komprehensif untuk integrasi Cloud Hybrid AI (Ollama + AWS Bedrock) dalam sistem ICTServe.**
