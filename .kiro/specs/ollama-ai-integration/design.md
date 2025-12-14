# Dokumen Rekabentuk Integrasi AI Ollama (Ollama AI Integration Design Document)

**Sistem ICTServe**  
**Versi:** 3.6.6 (SemVer)  
**Tarikh Kemaskini:** 14 Disember 2025  
**Status:** Aktif - Cloud Hybrid AI Architecture Implemented  
**Klasifikasi:** Terhad - Dalaman BPM MOTAC  
**Penulis:** Pasukan Pembangunan BPM MOTAC  
**Standard Rujukan:** ISO/IEC/IEEE 42010, ISO/IEC/IEEE 15288, WCAG 2.2 AA, OWASP ASVS L2, MyGOV Digital Service Standards v2.1.0

---

## Maklumat Dokumen (Document Information)

| Atribut              | Nilai                                               |
| -------------------- | --------------------------------------------------- |
| **Versi**            | 3.6.6                                               |
| **Tarikh Kemaskini** | 14 Disember 2025                                    |
| **Status**           | Aktif - Cloud Hybrid AI Architecture Implemented    |
| **Klasifikasi**      | Terhad - Dalaman BPM MOTAC                          |
| **Pematuhi**         | ISO/IEC/IEEE 42010, ISO/IEC/IEEE 15288, WCAG 2.2 AA |
| **Bahasa**           | Bahasa Melayu sahaja (v3.6.0)                       |
| **Spesifikasi Induk** | .kiro/specs/ictserve-comprehensive-v3.6 (v3.6.0)   |
| **Kebolehkesanan Keperluan** | Semua keputusan rekabentuk dipetakan kepada requirements.md |
| **Penyelarasan D00-D17** | Selaras dengan D00-D17 v3.6.0 (True Hybrid Architecture, Bahasa Melayu sahaja, Dual Audit System) |

> Notis Penggunaan Dalaman: Sistem ini digunakan secara dalaman oleh staf dan pegawai gred MOTAC; ia bukan sistem awam.

---

## Sejarah Perubahan (Changelog)

| Versi | Tarikh           | Perubahan                                                                                                                                                                                                                                                                 | Penulis                 |
| ----- | ---------------- | ------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------- | ----------------------- |
| 3.6.6 | 14 Disember 2025 | **Final Documentation Sync**: Comprehensive sync dengan `docs/ollama/HYBRID_BEDROCK_OLLAMA_INTEGRATION.md` dan `docs/D18_AI_CHATBOT_OLLAMA_BEDROCK.md`. Menambah Hybrid Query Routing patterns, FAQ/Complex/Hybrid classification, fallback strategies, cost optimization (82% savings), dan complete testing patterns dengan PHPUnit 12 attributes. | Pasukan Pembangunan BPM |
| 3.6.5 | 12 Disember 2025 | **Bedrock Documentation Sync v4**: Mengemaskini dengan insights dari keseluruhan `docs/aws_bedrock/` documentation suite. Menambah troubleshooting patterns, verification checklist, dan debugging commands. | Pasukan Pembangunan BPM |
| 3.6.1 | 12 Disember 2025 | **AWS Bedrock Integration**: Cloud Hybrid Architecture dengan multi-model intelligence (Claude 4.5 Opus/Sonnet/Haiku), streaming responses via Server-Sent Events, web-augmented responses, enhanced conversation management, model routing optimization, cost monitoring, performance analytics, dan data residency compliance untuk Malaysia. Menambah BedrockClient service, ModelRouter, StreamingResponseService, dan WebSearchService. Mengekalkan D00-D17 v3.6.0 compliance. | Pasukan Pembangunan BPM |
| 3.6.0 | 11 Disember 2025 | **Penyelarasan D00-D17 v3.6.0**: True Hybrid Architecture, Self-Registration (@motac.gov.my), Flexible Login, Account Linking, Dual Audit System (owen-it + spatie), Laravel Telescope (superuser only), Laravel Pulse/Sanctum/Socialite integration, **Bahasa Melayu sahaja** (tiada penukar bahasa), Laravel Reverb real-time notifications. | Pasukan Pembangunan BPM |
| 1.0.0 | 05 November 2025 | Versi awal rekabentuk integrasi Ollama-Laravel dengan ICTServe v3.0.0                                                                                                                                                                                                    | Pasukan Pembangunan BPM |

---

## Rujukan Dokumen Berkaitan (Related Document References)

- **[D00_SYSTEM_OVERVIEW.md]** - System vision and governance (v3.6.0)
- **[D03_SOFTWARE_REQUIREMENTS_SPECIFICATION.md]** - Software requirements (v3.6.0)
- **[D04_SOFTWARE_DESIGN_DOCUMENT.md]** - Architecture and design (v3.6.0)
- **[D09_DATABASE_DOCUMENTATION.md]** - Database schema and dual audit (v3.6.0)
- **[D11_TECHNICAL_DESIGN_DOCUMENTATION.md]** - Technical infrastructure (v3.6.0)
- **[D12_UI_UX_DESIGN_GUIDE.md]** - UI/UX guidelines (v3.6.0)
- **[D13_UI_UX_FRONTEND_FRAMEWORK.md]** - Frontend framework (v3.6.0)
- **[D14_UI_UX_STYLE_GUIDE.md]** - Style guide (v3.6.0)
- **[D15_LANGUAGE_MS_EN.md]** - Language localization (Bahasa Melayu sahaja, v3.6.0)
- **[D16_BROADCASTING_SETUP.md]** - WebSocket configuration (Laravel Reverb v1.6.2)
- **[D17_QUEUE_MANAGEMENT_HORIZON.md]** - Queue management (Laravel Horizon)
- **[D18_AI_CHATBOT_OLLAMA_BEDROCK.md]** - AI Chatbot Hybrid Architecture (NEW)
- **[docs/ollama/HYBRID_BEDROCK_OLLAMA_INTEGRATION.md]** - Comprehensive Hybrid Implementation Guide

---

## Ringkasan Eksekutif (Executive Summary)

Integrasi **Cloud Hybrid AI Architecture** menyediakan backend berkuasa AI yang komprehensif untuk modul Helpdesk dan Pinjaman Aset ICT dalam sistem ICTServe. Sistem ini memanfaatkan **dual AI processing approach** yang menggabungkan Large Language Models (LLMs) tempatan melalui pelayan Ollama dan perkhidmatan AI cloud terurus melalui AWS Bedrock untuk menyampaikan keupayaan FAQ Bot dengan multi-model intelligence, Analisis Dokumen dengan web-augmented responses, dan Auto-Reply dengan conversation management yang dipertingkat sambil mengekalkan standard privasi, keselamatan, dan kebolehcapaian yang ketat.

Rekabentuk mengikuti seni bina modular dengan pemisahan kebimbangan yang jelas, memastikan skalabiliti, kebolehselenggaraan, dan pematuhan dengan standard kerajaan Malaysia (D00-D17 v3.6.0).

**Ciri Utama v3.6.0:**

- **True Hybrid Architecture**: Akses fleksibel melalui borang tetamu atau portal authenticated dengan nullable user_id FK
- **Self-Registration**: Pendaftaran sendiri staf MOTAC dengan e-mel @motac.gov.my dan pengesahan e-mel
- **Flexible Login**: Log masuk menggunakan e-mel penuh atau nama pengguna pendek
- **Account Linking**: Pautan akaun opsyen untuk penyerahan tetamu terdahulu ke akaun authenticated
- **Bahasa Melayu Sahaja**: Antara muka AI dalam Bahasa Melayu sahaja tanpa penukar bahasa mengikut D15 v3.6.0
- **Dual Audit System**: Sistem audit dwi menggunakan owen-it (compliance) dan spatie (operations)
- **Laravel Pulse Integration**: Pemantauan prestasi masa nyata untuk admin/superuser
- **Laravel Sanctum**: Pengesahan token API untuk integrasi masa depan
- **Laravel Socialite**: OAuth 2.0 Google Workspace SSO (opsyen) untuk @motac.gov.my
- **Laravel Telescope**: Debugging dan pemantauan untuk superuser sahaja (tiada sekatan)

## Seni Bina Sistem (System Architecture)

### Seni Bina Sistem Peringkat Tinggi (High-Level System Architecture)

```text
┌─────────────────────────────────────────────────────────────────────────────┐
│                           ICTServe v3.6.0 True Hybrid Architecture          │
├─────────────────────────────────────────────────────────────────────────────┤
│  ┌─────────────────┐    ┌──────────────────┐    ┌─────────────────┐         │
│  │   Guest Forms   │    │   Authenticated  │    │  Admin Panel    │         │
│  │  (Borang Tetamu)│    │     Portal       │    │   (Filament)    │         │
│  │ - FAQ Bot AI    │    │  (Self-Register) │    │ - Document AI   │         │
│  │ - Quick Access  │    │ - My Dashboard   │    │ - Auto-Reply    │         │
│  │ - Bahasa Melayu │    │ - Account Link   │    │ - Pulse/Telescope│         │
│  └─────────────────┘    └──────────────────┘    └─────────────────┘         │
│           │                       │                       │                 │
│           └───────────────────────┼───────────────────────┘                 │
│                                   ▼                                         │
│  ┌─────────────────────────────────────────────────────────────────────────┐ │
│  │                        Laravel 12.40.1 API                             │ │
│  │  ┌─────────────────┐  ┌──────────────────┐  ┌─────────────────┐        │ │
│  │  │   FAQ Bot AI    │  │  Document AI     │  │   Auto-Reply    │        │ │
│  │  │   (Hybrid)      │  │   (Admin Only)   │  │   (Approval)    │        │ │
│  │  │ - Bahasa Melayu │  │ - PII Detection  │  │ - Email Tokens  │        │ │
│  │  │ - RAG Pipeline  │  │ - Vector Search  │  │ - Dual Audit    │        │ │
│  │  └─────────────────┘  └──────────────────┘  └─────────────────┘        │ │
│  └─────────────────────────────────────────────────────────────────────────┘ │
│                                   │                                         │
│                                   ▼                                         │
│  ┌─────────────────┐    ┌──────────────────┐    ┌─────────────────┐         │
│  │  Ollama Server  │    │   MySQL 8.0      │    │   Redis 7.0     │         │
│  │  (localhost)    │    │ - FAQs           │    │ - Cache         │         │
│  │ - llama3.1      │    │ - Documents      │    │ - Queue         │         │
│  │ - Embeddings    │    │ - Embeddings     │    │ - Sessions      │         │
│  │ - Bahasa Melayu │    │ - Dual Audit     │    │ - Reverb        │         │
│  │                 │    │ - Nullable FK    │    │ - Sanctum       │         │
│  └─────────────────┘    └──────────────────┘    └─────────────────┘         │
└─────────────────────────────────────────────────────────────────────────────┘
```

### Cloud Hybrid AI Architecture (AWS Bedrock Integration)

```text
┌─────────────────────────────────────────────────────────────────────────────┐
│                    ICTServe v3.6.1 Cloud Hybrid AI Architecture            │
├─────────────────────────────────────────────────────────────────────────────┤
│  ┌─────────────────┐    ┌──────────────────┐    ┌─────────────────┐         │
│  │   Guest Forms   │    │   Authenticated  │    │  Admin Panel    │         │
│  │ - Smart Routing │    │     Portal       │    │   (Filament)    │         │
│  │ - Streaming UI  │    │ - Enhanced Conv  │    │ - Model Config  │         │
│  │ - Web-Augmented │    │ - Long-term Mem  │    │ - Performance   │         │
│  └─────────────────┘    └──────────────────┘    └─────────────────┘         │
│           │                       │                       │                 │
│           └───────────────────────┼───────────────────────┘                 │
│                                   ▼                                         │
│  ┌─────────────────────────────────────────────────────────────────────────┐ │
│  │                    AI Processing Layer (Hybrid)                        │ │
│  │  ┌─────────────────┐  ┌──────────────────┐  ┌─────────────────┐        │ │
│  │  │  Model Router   │  │  Conversation    │  │  Response       │        │ │
│  │  │  (Smart Route)  │  │  Manager         │  │  Processor      │        │ │
│  │  │ - Task Analysis │  │ - Context Memory │  │ - Streaming     │        │ │
│  │  │ - Model Select  │  │ - Personalization│  │ - Web Search    │        │ │
│  │  └─────────────────┘  └──────────────────┘  └─────────────────┘        │ │
│  └─────────────────────────────────────────────────────────────────────────┘ │
│                                   │                                         │
│                                   ▼                                         │
│  ┌─────────────────┐    ┌──────────────────┐    ┌─────────────────┐         │
│  │  Local AI       │    │   Cloud AI       │    │   Data Layer    │         │
│  │  (Ollama)       │    │  (AWS Bedrock)   │    │   (Enhanced)    │         │
│  │ - llama3.1      │    │ - Claude 3.5     │    │ - Conversations │         │
│  │ - Fast Response │    │ - Sonnet/Haiku   │    │ - Model Configs │         │
│  │ - Privacy First │    │ - Amazon Titan   │    │ - Performance   │         │
│  │ - Offline Mode  │    │ - Web Search     │    │ - Audit Trail   │         │
│  └─────────────────┘    └──────────────────┘    └─────────────────┘         │
└─────────────────────────────────────────────────────────────────────────────┘

### AWS Bedrock Service Architecture (Detailed)

```text
┌─────────────────────────────────────────────────────────────────────────────┐
│                    AWS Bedrock Integration Layer (v3.6.1)                  │
├─────────────────────────────────────────────────────────────────────────────┤
│  ┌─────────────────┐    ┌──────────────────┐    ┌─────────────────┐         │
│  │  Model Router   │    │  Cost Monitor    │    │  Performance    │         │
│  │  (Smart Route)  │    │  (Usage Track)   │    │  Analytics      │         │
│  │ - Task Analysis │    │ - Token Count    │    │ - Response Time │         │
│  │ - Model Select  │    │ - Cost Estimate  │    │ - Quality Score │         │
│  │ - Fallback      │    │ - Budget Alert   │    │ - Model Compare │         │
│  └─────────────────┘    └──────────────────┘    └─────────────────┘         │
│           │                       │                       │                 │
│           └───────────────────────┼───────────────────────┘                 │
│                                   ▼                                         │
│  ┌─────────────────────────────────────────────────────────────────────────┐ │
│  │                    Bedrock Service Layer                               │ │
│  │  ┌─────────────────┐  ┌──────────────────┐  ┌─────────────────┐        │ │
│  │  │  BedrockClient  │  │  StreamingService│  │  WebSearchService│        │ │
│  │  │  (AWS SDK)      │  │  (SSE Handler)   │  │  (Bing/Google)   │        │ │
│  │  │ - Model Invoke  │  │ - Chunk Process  │  │ - Query Builder  │        │ │
│  │  │ - Health Check  │  │ - Buffer Manage  │  │ - Source Verify  │        │ │
│  │  │ - Error Handle  │  │ - WCAG Support   │  │ - Content Filter │        │ │
│  │  └─────────────────┘  └──────────────────┘  └─────────────────┘        │ │
│  └─────────────────────────────────────────────────────────────────────────┘ │
│                                   │                                         │
│                                   ▼                                         │
│  ┌─────────────────┐    ┌──────────────────┐    ┌─────────────────┐         │
│  │  Foundation     │    │   Data Layer     │    │   Compliance    │         │
│  │  Models         │    │   (Enhanced)     │    │   Layer         │         │
│  │ - Claude 3.5    │    │ - Model Configs  │    │ - Data Classify │         │
│  │ - Sonnet/Haiku  │    │ - Conversations  │    │ - Region Check  │         │
│  │ - Amazon Titan  │    │ - Cost Tracking  │    │ - Audit Trail   │         │
│  │ - Custom Models │    │ - Performance    │    │ - Residency     │         │
│  └─────────────────┘    └──────────────────┘    └─────────────────┘         │
└─────────────────────────────────────────────────────────────────────────────┘
```

```

### Seni Bina Lapisan Perkhidmatan (Service Layer Architecture)

```text
┌─────────────────────────────────────────────────────────────────────────────┐
│                        Laravel 12.40.1 Application                         │
├─────────────────────────────────────────────────────────────────────────────┤
│  Controllers (API & Web) - Bahasa Melayu sahaja                            │
│  ├── OllamaController (API endpoints)                                      │
│  ├── FaqController (Hybrid: Guest + Auth, nullable user_id FK)             │
│  ├── DocumentController (Admin/Superuser only)                             │
│  ├── AutoReplyController (Approval workflow dengan email tokens)           │
│  └── Auth Controllers (Self-Registration, Flexible Login, Account Linking) │
├─────────────────────────────────────────────────────────────────────────────┤
│  Services (Business Logic) - D00-D17 v3.6.0 Compliant                     │
│  ├── OllamaClient (HTTP wrapper + health check)                            │
│  ├── RagService (RAG + conversation context, Bahasa Melayu responses)      │
│  ├── DocumentService (Ingest, Analysis, PII detection)                     │
│  ├── EmbeddingService (Vector operations + caching)                        │
│  ├── RegistrationService (Self-registration @motac.gov.my + verification)  │
│  ├── AuthenticationService (Flexible login email/username)                 │
│  ├── AccountLinkingService (Link guest submissions to accounts)            │
│  └── BilingualSupportService (Dilumpuhkan v3.6.0 - sentiasa return 'ms' mengikut D15) │
├─────────────────────────────────────────────────────────────────────────────┤
│  Models & Data Layer (True Hybrid Architecture)                            │
│  ├── User (Self-registration, nullable relationships, locale='ms')         │
│  ├── Faq, Document, DocumentChunk (with user_id nullable FK)               │
│  ├── Embedding, AutoReplyTemplate, AutoReplyDraft                          │
│  ├── MessageLog (Dual audit trail: owen-it + spatie)                       │
│  ├── GuestConversation, ApprovalEmailToken (Account linking support)      │
│  └── ActivityLog, Audits (Dual audit system untuk compliance)             │
├─────────────────────────────────────────────────────────────────────────────┤
│  Jobs & Queues (Laravel Horizon Integration)                               │
│  ├── DocumentIngestJob (Background processing)                             │
│  ├── EmbeddingJob (Vector generation)                                      │
│  ├── AutoReplyGenerationJob (AI response drafts)                           │
│  ├── EmailVerificationJob (Self-registration @motac.gov.my)                │
│  ├── AccountLinkingJob (Historical submission linking)                     │
│  └── NotificationDigestJob (Multi-channel notifications)                   │
├─────────────────────────────────────────────────────────────────────────────┤
│  Monitoring & Performance (Laravel Pulse + Telescope + Sanctum)            │
│  ├── Laravel Pulse (Real-time performance monitoring - admin/superuser)    │
│  ├── Laravel Telescope (Debugging - superuser only, tiada sekatan)         │
│  ├── Laravel Sanctum (API token authentication untuk future integrations)  │
│  ├── Laravel Socialite (Google Workspace SSO opsyen @motac.gov.my)         │
│  └── Laravel Reverb (Real-time WebSocket notifications)                    │
└─────────────────────────────────────────────────────────────────────────────┘
```

## Komponen Dilumpuhkan (Deprecated Components) - D15 v3.6.0

Mengikut D15 v3.6.0, komponen berikut telah dilumpuhkan sebagai sebahagian daripada peralihan ke antara muka Bahasa Melayu sahaja:

| Komponen                    | Status       | Nota                                                    |
| --------------------------- | ------------ | ------------------------------------------------------- |
| `LanguageSwitcher`          | **Dipadam**  | Komponen Livewire untuk menukar bahasa                  |
| `BilingualSupportService`   | Dilumpuhkan  | Semua kaedah kini mengembalikan 'ms' sahaja             |
| `SetLocale` middleware      | Dilumpuhkan  | Sentiasa menetapkan locale kepada 'ms'                  |
| `users.locale` column       | Dilumpuhkan  | Sentiasa mengembalikan 'ms' (kolum dikekalkan)          |
| `ictserve_locale` cookie    | **Dipadam**  | Cookie dipadam pada login/logout                        |
| Fail terjemahan `lang/en/`  | Dikekalkan   | Untuk rujukan teknikal dan kemungkinan penggunaan masa depan |

**Implikasi untuk AI System:**

- Semua respons AI dalam Bahasa Melayu sahaja
- Tiada pengesanan bahasa automatik
- BilingualSupportService sentiasa return 'ms'
- Templat e-mel dan notifikasi dalam Bahasa Melayu sahaja

## Components and Interfaces

### 1. OllamaClient Service

**Purpose**: HTTP client wrapper for Ollama API communication

**Key Methods**:

- `generate(array $payload): array` - Text generation
- `embeddings(string $text): array` - Vector embeddings
- `chat(array $messages): array` - Chat completion
- `models(): array` - List available models

#### Keperluan API Ollama Kritikal (Critical Ollama API Requirements)

> **PENTING**: Keperluan berikut adalah kritikal untuk komunikasi yang betul dengan pelayan Ollama. Kegagalan mematuhi keperluan ini akan menyebabkan respons yang tidak dijangka atau kegagalan.

**1. Parameter `stream: false` (WAJIB)**

Semua permintaan ke endpoint `/api/generate` dan `/api/chat` MESTI menyertakan `'stream' => false` dalam payload. Tanpa parameter ini, Ollama API akan mengembalikan respons streaming (newline-delimited JSON) yang tidak boleh diproses sebagai objek JSON tunggal.

```php
// ✅ BETUL: Sertakan stream: false
$payload = [
    'model' => 'gemma3:1b',
    'prompt' => $userQuery,
    'stream' => false,  // KRITIKAL: Mesti false untuk respons JSON tunggal
];

// ❌ SALAH: Tanpa stream parameter (lalai kepada streaming)
$payload = [
    'model' => 'gemma3:1b',
    'prompt' => $userQuery,
    // stream tidak ditetapkan - akan menyebabkan respons streaming
];
```

**2. Endpoint Embeddings: `/api/embed` (BUKAN `/api/embeddings`)**

Endpoint yang betul untuk penjanaan embedding adalah `/api/embed`, bukan `/api/embeddings`. Endpoint lama `/api/embeddings` tidak lagi disokong dalam versi Ollama terkini.

```php
// ✅ BETUL: Gunakan /api/embed
$response = Http::post($this->config['url'] . '/api/embed', $payload);

// ❌ SALAH: Endpoint lama tidak disokong
$response = Http::post($this->config['url'] . '/api/embeddings', $payload);
```

**3. Parameter Embeddings: `input` (BUKAN `prompt`)**

Endpoint `/api/embed` menggunakan parameter `input` untuk teks yang hendak di-embed, bukan `prompt`.

```php
// ✅ BETUL: Gunakan 'input' parameter
$payload = [
    'model' => 'nomic-embed-text',
    'input' => $text,  // Parameter yang betul untuk /api/embed
];

// ❌ SALAH: 'prompt' tidak dikenali oleh /api/embed
$payload = [
    'model' => 'nomic-embed-text',
    'prompt' => $text,  // Tidak akan berfungsi
];
```

**4. Model Embedding Khusus**

Gunakan model embedding khusus (`nomic-embed-text`) untuk penjanaan embedding, bukan model chat (`gemma3:1b`). Model chat tidak dioptimumkan untuk penjanaan embedding dan akan menghasilkan vektor yang kurang berkualiti.

```php
// ✅ BETUL: Model embedding khusus
$embeddingModel = $this->config['embedding_model'] ?? 'nomic-embed-text';

// ❌ SALAH: Model chat untuk embedding
$embeddingModel = $this->config['model']; // gemma3:1b - tidak sesuai untuk embedding
```

**5. Normalisasi Respons Embeddings**

API `/api/embed` mengembalikan `embeddings` (array of arrays). Untuk keserasian dengan kod sedia ada, normalisasikan kepada `embedding` (single array).

```php
// Normalisasi respons
if (isset($response['embeddings']) && is_array($response['embeddings']) && !empty($response['embeddings'])) {
    $response['embedding'] = $response['embeddings'][0];
}
```

**Configuration** (`config/ollama.php`) - Selaras dengan D11 Technical Design v3.6.0:

```php
return [
    'model' => env('OLLAMA_MODEL', 'llama3.1'),
    'url' => env('OLLAMA_URL', 'http://127.0.0.1:11434'),
    'default_prompt' => env('OLLAMA_DEFAULT_PROMPT', 'Bagaimana saya boleh membantu anda hari ini?'), // Bahasa Melayu sahaja (D15 v3.6.0)
    'connection' => [
        'timeout' => env('OLLAMA_CONNECTION_TIMEOUT', 300),
        'retry_attempts' => 3,
        'retry_delay' => 1000, // milliseconds
    ],
    'cache' => [
        'enabled' => env('OLLAMA_CACHE_ENABLED', true),
        'ttl' => env('OLLAMA_CACHE_TTL', 3600), // 1 hour
        'driver' => env('OLLAMA_CACHE_DRIVER', 'redis'),
    ],
    'performance' => [
        'max_response_time' => 5, // seconds (Req 8.1)
        'quantized_model' => env('OLLAMA_QUANTIZED_MODEL', true),
        'context_window' => 4096, // tokens
    ],
    'rate_limiting' => [
        'per_user' => 60, // requests per minute
        'per_ip' => 1000, // requests per hour
    ],
];
```

**Interface Contract**:

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

**Caching Strategy** (Req 8.4):

- **FAQ Queries**: Cache responses for 1 hour, tagged by query hash
- **Document Embeddings**: Cache for 24 hours, invalidate on document update
- **Common Queries**: Pre-warm cache with top 50 FAQ queries
- **Cache Keys**: `ollama:faq:{hash}`, `ollama:embedding:{doc_id}:{chunk_index}`

**Design Rationale**: Caching reduces Ollama server load and improves response times for common queries (Req 8.4). Quantized models optimize memory usage while maintaining quality (Req 8.5). Configuration follows D11 Technical Design standards for environment variables and service configuration.

### 1.1. BedrockClient Service (Cloud AI Integration)

**Purpose**: AWS Bedrock client wrapper for cloud-based AI processing with multi-model support

**Key Methods**:

- `invokeModel(string $modelId, array $payload): array` - Model invocation
- `invokeModelWithStreaming(string $modelId, array $payload): Generator` - Streaming responses
- `listFoundationModels(): array` - Available models
- `getModelInfo(string $modelId): array` - Model specifications

**Supported Models**:

- **Claude 3.5 Sonnet** (`anthropic.claude-3-5-sonnet-20241022-v2:0`): High-quality responses, complex reasoning
- **Claude 3.5 Haiku** (`anthropic.claude-3-5-haiku-20241022-v1:0`): Fast responses, simple queries
- **Amazon Titan Text G1** (`amazon.titan-text-premier-v1:0`): Cost-effective, general purpose

**Configuration** (`config/bedrock.php`):

```php
return [
    'enabled' => env('BEDROCK_ENABLED', false),
    'region' => env('AWS_DEFAULT_REGION', 'ap-southeast-1'), // Malaysia region
    'models' => [
        'default' => env('BEDROCK_DEFAULT_MODEL', 'anthropic.claude-3-5-haiku-20241022-v1:0'),
        'high_quality' => 'anthropic.claude-3-5-sonnet-20241022-v2:0',
        'fast_response' => 'anthropic.claude-3-5-haiku-20241022-v1:0',
        'cost_effective' => 'amazon.titan-text-premier-v1:0',
    ],
    'routing' => [
        'faq_simple' => 'fast_response',
        'faq_complex' => 'high_quality',
        'document_analysis' => 'high_quality',
        'auto_reply' => 'high_quality',
    ],
    'streaming' => [
        'enabled' => env('BEDROCK_STREAMING_ENABLED', true),
        'chunk_size' => 1024,
        'timeout' => 30,
    ],
    'web_search' => [
        'enabled' => env('BEDROCK_WEB_SEARCH_ENABLED', false),
        'provider' => env('WEB_SEARCH_PROVIDER', 'bing'), // 'bing' or 'google'
        'api_key' => env('WEB_SEARCH_API_KEY'),
        'max_results' => 5,
    ],
    'data_residency' => [
        'enforce_malaysia' => env('BEDROCK_ENFORCE_MALAYSIA_RESIDENCY', true),
        'allowed_regions' => ['ap-southeast-1'], // Singapore (closest to Malaysia)
        'data_classification' => [
            'public' => 'allow_cloud',
            'internal' => 'local_only',
            'confidential' => 'local_only',
            'restricted' => 'local_only',
        ],
    ],
    'performance' => [
        'max_response_time' => 3, // seconds (faster than Ollama target)
        'retry_attempts' => 2,
        'fallback_to_ollama' => true,
    ],
];
```

**Interface Contract**:

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

**Model Routing Logic**:

```php
class ModelRouter
{
    public function selectModel(string $taskType, array $context): string
    {
        // Analyze task complexity
        $complexity = $this->analyzeComplexity($context);
        
        // Route based on task type and complexity
        return match([$taskType, $complexity]) {
            ['faq', 'simple'] => config('bedrock.models.fast_response'),
            ['faq', 'complex'] => config('bedrock.models.high_quality'),
            ['document_analysis', _] => config('bedrock.models.high_quality'),
            ['auto_reply', _] => config('bedrock.models.high_quality'),
            default => config('bedrock.models.default'),
        };
    }
    
    private function analyzeComplexity(array $context): string
    {
        $indicators = [
            'question_length' => strlen($context['query'] ?? ''),
            'technical_terms' => $this->countTechnicalTerms($context['query'] ?? ''),
            'context_size' => count($context['retrieved_docs'] ?? []),
        ];
        
        // Simple heuristic for complexity scoring
        $score = ($indicators['question_length'] > 100 ? 1 : 0) +
                ($indicators['technical_terms'] > 3 ? 1 : 0) +
                ($indicators['context_size'] > 5 ? 1 : 0);
                
        return $score >= 2 ? 'complex' : 'simple';
    }
}
```

**Streaming Response Implementation**:

```php
class StreamingResponseService
{
    public function streamBedrockResponse(string $modelId, array $payload): Generator
    {
        $stream = $this->bedrockClient->invokeModelWithStreaming($modelId, $payload);
        
        foreach ($stream as $chunk) {
            // Process chunk and yield formatted response
            $processedChunk = $this->processStreamChunk($chunk);
            
            if ($processedChunk) {
                yield [
                    'type' => 'content',
                    'data' => $processedChunk,
                    'timestamp' => now()->toISOString(),
                ];
            }
        }
        
        // Final chunk with metadata
        yield [
            'type' => 'complete',
            'data' => null,
            'metadata' => [
                'model_used' => $modelId,
                'total_tokens' => $this->getTokenCount(),
                'processing_time' => $this->getProcessingTime(),
            ],
        ];
    }
}
```

**Design Rationale**: Bedrock integration provides access to state-of-the-art models while maintaining fallback to local Ollama processing (Req 9.2). Model routing optimizes cost and performance by selecting appropriate models for different task types (Req 9.1). Streaming responses improve user experience with real-time content delivery while maintaining WCAG 2.2 AA compliance.

**Enhanced Implementation Details** (Based on AWS Bedrock Documentation):

**BedrockService Implementation** (`app/Services/BedrockService.php`):

```php
<?php

declare(strict_types=1);

namespace App\Services;

use Aws\BedrockRuntime\BedrockRuntimeClient;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use App\Contracts\BedrockClientContract;

class BedrockService implements BedrockClientContract
{
    public function __construct(
        private BedrockRuntimeClient $client
    ) {}

    public function invoke(string $prompt, int $maxTokens = 1000, ?string $modelId = null): array
    {
        try {
            $modelId = $modelId ?? config('bedrock.model_id');

            $response = $this->client->invokeModel([
                'modelId' => $modelId,
                'contentType' => 'application/json',
                'accept' => 'application/json',
                'body' => json_encode([
                    'anthropic_version' => 'bedrock-2023-05-31',
                    'max_tokens' => $maxTokens,
                    'messages' => [
                        [
                            'role' => 'user',
                            'content' => $prompt,
                        ],
                    ],
                ]),
            ]);

            $result = json_decode($response['body']->getContents(), true);

            return [
                'success' => true,
                'content' => $result['content'][0]['text'] ?? '',
                'usage' => $result['usage'] ?? [],
                'model_used' => $modelId,
                'timestamp' => now()->toISOString(),
            ];
        } catch (\Exception $e) {
            Log::error('Bedrock API Error: ' . $e->getMessage(), [
                'model_id' => $modelId,
                'prompt_length' => strlen($prompt),
                'max_tokens' => $maxTokens,
            ]);

            return [
                'success' => false,
                'content' => 'Maaf, terdapat masalah dengan perkhidmatan AI. Sila cuba lagi.',
                'usage' => [],
                'error' => $e->getMessage(),
            ];
        }
    }

    public function invokeModelWithStreaming(string $modelId, array $payload): \Generator
    {
        // Implementation for streaming responses
        $stream = $this->client->invokeModelWithResponseStream([
            'modelId' => $modelId,
            'contentType' => 'application/json',
            'accept' => 'application/json',
            'body' => json_encode($payload),
        ]);

        foreach ($stream['body'] as $chunk) {
            $data = json_decode($chunk['chunk']['bytes'], true);
            if (isset($data['delta']['text'])) {
                yield $data['delta']['text'];
            }
        }
    }

    public function healthCheck(): bool
    {
        try {
            $result = $this->invoke('Test', 10);
            return $result['success'];
        } catch (\Exception $e) {
            return false;
        }
    }

    public function estimateCost(string $modelId, int $inputTokens, int $outputTokens): float
    {
        // Cost estimation based on model pricing
        $pricing = [
            'anthropic.claude-3-5-sonnet-20241022-v2:0' => ['input' => 0.003, 'output' => 0.015],
            'anthropic.claude-3-5-haiku-20241022-v1:0' => ['input' => 0.00025, 'output' => 0.00125],
            'amazon.titan-text-premier-v1:0' => ['input' => 0.0005, 'output' => 0.0015],
        ];

        $rates = $pricing[$modelId] ?? ['input' => 0.001, 'output' => 0.005];
        
        return ($inputTokens / 1000 * $rates['input']) + ($outputTokens / 1000 * $rates['output']);
    }
}
```

**Enhanced BedrockChat Component** (`app/Livewire/BedrockChat.php`):

```php
<?php

declare(strict_types=1);

namespace App\Livewire;

use Livewire\Component;
use App\Services\BedrockService;
use App\Services\WebSearchService;
use App\Models\BedrockConversation;
use Illuminate\Support\Facades\Auth;

class BedrockChat extends Component
{
    public string $prompt = '';
    public string $model = 'haiku'; // Default to fastest model
    public array $messages = [];
    public bool $useInternet = false;
    public ?int $conversationId = null;
    public bool $showSidebar = true;
    public bool $sending = false;
    public array $modelOptions = [
        'haiku' => 'Claude 3.5 Haiku (Pantas)',
        'sonnet' => 'Claude 3.5 Sonnet (Seimbang)',
        'opus' => 'Claude 4.5 Opus (Berkuasa)',
    ];

    protected $rules = [
        'prompt' => 'required|string|max:10000',
        'model' => 'required|in:haiku,sonnet,opus',
    ];

    public function mount(?int $id = null): void
    {
        if ($id) {
            $this->loadConversation($id);
        }
    }

    public function send(): void
    {
        $this->validate();

        if (empty(trim($this->prompt))) {
            return;
        }

        $this->sending = true;

        // Add user message
        $this->messages[] = [
            'role' => 'user',
            'content' => $this->prompt,
            'timestamp' => now()->toISOString(),
        ];

        // Web search if enabled
        $context = '';
        if ($this->useInternet) {
            $context = $this->searchWeb($this->prompt);
        }

        // Build prompt with context (Bahasa Melayu)
        $systemPrompt = 'Anda adalah pembantu AI yang membantu staf MOTAC. Jawab dalam Bahasa Melayu sahaja.';
        $fullPrompt = $context 
            ? "{$systemPrompt}\n\nKonteks dari carian web:\n\n{$context}\n\nSoalan pengguna: {$this->prompt}"
            : "{$systemPrompt}\n\nSoalan pengguna: {$this->prompt}";

        // Model mapping with updated IDs
        $modelMap = [
            'opus' => 'global.anthropic.claude-opus-4-5-20251101-v1:0',
            'sonnet' => 'us.anthropic.claude-sonnet-4-5-20250929-v1:0',
            'haiku' => 'us.anthropic.claude-haiku-4-5-20251001-v1:0',
        ];

        // Call Bedrock API
        $bedrock = app(BedrockService::class);
        $result = $bedrock->invoke($fullPrompt, 2000, $modelMap[$this->model]);

        // Add assistant response
        if ($result['success']) {
            $this->messages[] = [
                'role' => 'assistant',
                'content' => $result['content'],
                'timestamp' => now()->toISOString(),
                'model_used' => $this->model,
                'tokens_used' => $result['usage']['output_tokens'] ?? 0,
            ];
        } else {
            $this->messages[] = [
                'role' => 'assistant',
                'content' => 'Maaf, saya tidak dapat memproses permintaan anda sekarang. Sila cuba lagi atau hubungi sokongan teknikal.',
                'timestamp' => now()->toISOString(),
                'error' => true,
            ];
        }

        // Save conversation
        $this->saveConversation();

        // Reset input
        $this->prompt = '';
        $this->sending = false;
    }

    public function newConversation(): void
    {
        $this->conversationId = null;
        $this->messages = [];
        $this->prompt = '';
        $this->sending = false;
    }

    public function loadConversation(int $id): void
    {
        $conversation = BedrockConversation::findOrFail($id);
        $this->conversationId = $id;
        $this->messages = $conversation->messages;
        $this->model = $conversation->model;
        $this->sending = false;
    }

    public function deleteConversation(int $id): void
    {
        BedrockConversation::findOrFail($id)->delete();
        
        if ($this->conversationId === $id) {
            $this->newConversation();
        }
    }

    private function saveConversation(): void
    {
        if (empty($this->messages)) {
            return;
        }

        $title = $this->conversationId 
            ? BedrockConversation::find($this->conversationId)->title
            : substr($this->messages[0]['content'], 0, 50) . '...';

        if ($this->conversationId) {
            BedrockConversation::where('id', $this->conversationId)->update([
                'messages' => $this->messages,
                'model' => $this->model,
                'updated_at' => now(),
            ]);
        } else {
            $conversation = BedrockConversation::create([
                'title' => $title,
                'messages' => $this->messages,
                'model' => $this->model,
                'user_id' => Auth::id(), // Link to authenticated user
            ]);
            $this->conversationId = $conversation->id;
        }
    }

    private function searchWeb(string $query): string
    {
        try {
            $webSearch = app(WebSearchService::class);
            return $webSearch->search($query, 5); // Limit to 5 results
        } catch (\Exception $e) {
            Log::warning('Web search failed: ' . $e->getMessage());
            return '';
        }
    }

    public function render()
    {
        $conversations = Auth::check() 
            ? BedrockConversation::where('user_id', Auth::id())->latest()->limit(20)->get()
            : collect();

        return view('livewire.bedrock-chat', [
            'conversations' => $conversations,
        ]);
    }
}
```

**Enhanced Database Schema** (`database/migrations/create_bedrock_conversations_table.php`):

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bedrock_conversations', function (Blueprint $table) {
            $table->id();
            $table->string('title')->nullable();
            $table->json('messages');
            $table->string('model')->default('haiku');
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade');
            $table->integer('total_tokens')->default(0);
            $table->decimal('estimated_cost', 8, 6)->default(0);
            $table->timestamps();
            
            $table->index(['user_id', 'created_at']);
            $table->index('model');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bedrock_conversations');
    }
};
```

**Enhanced Configuration** (`config/bedrock.php`):

```php
<?php

return [
    'enabled' => env('BEDROCK_ENABLED', false),
    'region' => env('AWS_BEDROCK_REGION', 'ap-southeast-1'), // Singapore (closest to Malaysia)
    'version' => 'latest',
    'credentials' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
    ],
    'model_id' => env('AWS_BEDROCK_MODEL_ID', 'us.anthropic.claude-haiku-4-5-20251001-v1:0'),
    
    'models' => [
        'default' => 'us.anthropic.claude-haiku-4-5-20251001-v1:0',
        'high_quality' => 'global.anthropic.claude-opus-4-5-20251101-v1:0',
        'fast_response' => 'us.anthropic.claude-haiku-4-5-20251001-v1:0',
        'balanced' => 'us.anthropic.claude-sonnet-4-5-20250929-v1:0',
    ],
    
    'routing' => [
        'faq_simple' => 'fast_response',
        'faq_complex' => 'balanced',
        'document_analysis' => 'high_quality',
        'auto_reply' => 'balanced',
    ],
    
    'rate_limiting' => [
        'per_user' => 60, // requests per hour
        'per_model' => [
            'opus' => 10,   // requests per hour
            'sonnet' => 30, // requests per hour  
            'haiku' => 60,  // requests per hour
        ],
    ],
    
    'performance' => [
        'max_response_time' => 30, // seconds
        'retry_attempts' => 2,
        'fallback_to_ollama' => true,
    ],
    
    'data_residency' => [
        'enforce_malaysia' => env('BEDROCK_ENFORCE_MALAYSIA_RESIDENCY', true),
        'allowed_regions' => ['ap-southeast-1'], // Singapore only
        'data_classification' => [
            'public' => 'allow_cloud',
            'internal' => 'local_only',
            'confidential' => 'local_only',
            'restricted' => 'local_only',
        ],
    ],
];
```ate models for different task types (Req 9.1). Streaming responses improve user experience with real-time content delivery while maintaining WCAG 2.2 AA compliance.

### 1.2. MCP Server Integration (Model Context Protocol)

**Purpose**: Expose Bedrock models to AI assistants (Amazon Q, Kiro) via standardized protocol

**Enhanced MCP Server Implementation** (`mcp-servers/bedrock-server.js`):

```javascript
#!/usr/bin/env node

import { Server } from '@modelcontextprotocol/sdk/server/index.js';
import { StdioServerTransport } from '@modelcontextprotocol/sdk/server/stdio.js';
import { CallToolRequestSchema, ListToolsRequestSchema } from '@modelcontextprotocol/sdk/types.js';
import { BedrockRuntimeClient, InvokeModelCommand } from '@aws-sdk/client-bedrock-runtime';

const MODEL_IDS = {
  opus: 'global.anthropic.claude-opus-4-5-20251101-v1:0',
  sonnet: 'us.anthropic.claude-sonnet-4-5-20250929-v1:0',
  haiku: 'us.anthropic.claude-haiku-4-5-20251001-v1:0',
};

const client = new BedrockRuntimeClient({
  region: process.env.AWS_BEDROCK_REGION || 'ap-southeast-1', // Malaysia region
  credentials: {
    accessKeyId: process.env.AWS_ACCESS_KEY_ID,
    secretAccessKey: process.env.AWS_SECRET_ACCESS_KEY,
  },
});

const server = new Server(
  {
    name: 'bedrock-opus',
    version: '1.0.0',
  },
  {
    capabilities: {
      tools: {},
    },
  }
);

server.setRequestHandler(ListToolsRequestSchema, async () => ({
  tools: [
    {
      name: 'invoke_claude_opus',
      description: 'Invoke AWS Bedrock Claude Opus 4.5 (most powerful)',
      inputSchema: {
        type: 'object',
        properties: {
          prompt: { 
            type: 'string', 
            description: 'The prompt to send to Claude (akan dijawab dalam Bahasa Melayu)' 
          },
          maxTokens: { 
            type: 'number', 
            description: 'Maximum tokens to generate', 
            default: 4096 
          },
        },
        required: ['prompt'],
      },
    },
    {
      name: 'invoke_claude_sonnet',
      description: 'Invoke AWS Bedrock Claude Sonnet 4.5 (balanced)',
      inputSchema: {
        type: 'object',
        properties: {
          prompt: { 
            type: 'string', 
            description: 'The prompt to send to Claude (akan dijawab dalam Bahasa Melayu)' 
          },
          maxTokens: { 
            type: 'number', 
            description: 'Maximum tokens to generate', 
            default: 4096 
          },
        },
        required: ['prompt'],
      },
    },
    {
      name: 'invoke_claude_haiku',
      description: 'Invoke AWS Bedrock Claude Haiku 4.5 (fastest)',
      inputSchema: {
        type: 'object',
        properties: {
          prompt: { 
            type: 'string', 
            description: 'The prompt to send to Claude (akan dijawab dalam Bahasa Melayu)' 
          },
          maxTokens: { 
            type: 'number', 
            description: 'Maximum tokens to generate', 
            default: 4096 
          },
        },
        required: ['prompt'],
      },
    },
  ],
}));

server.setRequestHandler(CallToolRequestSchema, async (request) => {
  const { name, arguments: args } = request.params;

  const modelMap = {
    invoke_claude_opus: MODEL_IDS.opus,
    invoke_claude_sonnet: MODEL_IDS.sonnet,
    invoke_claude_haiku: MODEL_IDS.haiku,
  };

  const modelId = modelMap[name];
  if (!modelId) {
    throw new Error(`Unknown tool: ${name}`);
  }

  try {
    // Add Bahasa Melayu system prompt
    const systemPrompt = 'Anda adalah pembantu AI yang membantu staf MOTAC. Jawab dalam Bahasa Melayu sahaja dengan tepat dan berguna.';
    const fullPrompt = `${systemPrompt}\n\nSoalan: ${args.prompt}`;

    const command = new InvokeModelCommand({
      modelId,
      contentType: 'application/json',
      accept: 'application/json',
      body: JSON.stringify({
        anthropic_version: 'bedrock-2023-05-31',
        max_tokens: args.maxTokens || 4096,
        messages: [{ role: 'user', content: fullPrompt }],
      }),
    });

    const response = await client.send(command);
    const result = JSON.parse(new TextDecoder().decode(response.body));

    return {
      content: [
        {
          type: 'text',
          text: result.content[0].text,
        },
      ],
      metadata: {
        model_used: modelId,
        tokens_used: result.usage?.output_tokens || 0,
        timestamp: new Date().toISOString(),
      },
    };
  } catch (error) {
    return {
      content: [
        {
          type: 'text',
          text: `Maaf, terdapat ralat: ${error.message}`,
        },
      ],
      isError: true,
    };
  }
});

const transport = new StdioServerTransport();
await server.connect(transport);
```

**MCP Configuration** (`.kiro/settings/mcp.json`):

```json
{
  "mcpServers": {
    "bedrock-opus": {
      "command": "node",
      "args": ["mcp-servers/bedrock-server.js"],
      "env": {
        "AWS_ACCESS_KEY_ID": "${AWS_ACCESS_KEY_ID}",
        "AWS_SECRET_ACCESS_KEY": "${AWS_SECRET_ACCESS_KEY}",
        "AWS_BEDROCK_REGION": "ap-southeast-1"
      },
      "disabled": false,
      "autoApprove": [
        "invoke_claude_haiku",
        "invoke_claude_sonnet"
      ]
    }
  }
}
```

**Location**: `mcp-servers/bedrock-server.js`

**Exposed Tools**:

1. **invoke_claude_opus** - Claude Opus 4.5 (most powerful, complex reasoning)
2. **invoke_claude_sonnet** - Claude Sonnet 4.5 (balanced performance)  
3. **invoke_claude_haiku** - Claude Haiku 4.5 (fastest responses)

**Tool Schema**:

```javascript
{
  name: 'invoke_claude_opus',
  description: 'Invoke AWS Bedrock Claude Opus 4.5 (most powerful)',
  inputSchema: {
    type: 'object',
    properties: {
      prompt: { type: 'string', description: 'The prompt to send to Claude' },
      maxTokens: { type: 'number', description: 'Maximum tokens to generate', default: 4096 },
    },
    required: ['prompt'],
  },
}
```

**Implementation Architecture**:

```javascript
const MODEL_IDS = {
  opus: 'global.anthropic.claude-opus-4-5-20251101-v1:0',
  sonnet: 'us.anthropic.claude-sonnet-4-5-20250929-v1:0',
  haiku: 'us.anthropic.claude-haiku-4-5-20251001-v1:0',
};

const client = new BedrockRuntimeClient({
  region: process.env.AWS_BEDROCK_REGION || 'us-east-1',
  credentials: {
    accessKeyId: process.env.AWS_ACCESS_KEY_ID,
    secretAccessKey: process.env.AWS_SECRET_ACCESS_KEY,
  },
});
```

**Security Features**:

- **Environment-based Credentials**: AWS keys stored in environment variables
- **Request Validation**: Input schema validation for all tool calls
- **Error Handling**: Graceful error responses with sanitized messages
- **Rate Limiting**: Inherits AWS Bedrock service quotas

**Usage by AI Assistants**:

```javascript
// Amazon Q or Kiro can invoke:
await invoke_claude_opus({
  prompt: "Analyze this ICTServe system architecture",
  maxTokens: 2000
});
```

**Design Rationale**: MCP integration enables AI assistants to leverage Bedrock models for enhanced capabilities while maintaining security and standardization. Supports ICTServe's AI-augmented development workflow.

### 1.3. Conversation Management System

**Purpose**: Persistent conversation storage and management for enhanced user experience

**Model**: `BedrockConversation`

**Schema**:

```php
Schema::create('bedrock_conversations', function (Blueprint $table) {
    $table->id();
    $table->string('title')->nullable();
    $table->json('messages');
    $table->string('model')->default('opus');
    $table->timestamps();
});
```

**Key Features**:

1. **Conversation Persistence**: Save and reload chat history
2. **Model Tracking**: Remember which model was used per conversation
3. **Title Generation**: Auto-generate titles from first message
4. **Message History**: Store complete conversation context

**Livewire Component Integration**:

```php
class BedrockChat extends Component
{
    public ?int $conversationId = null;
    public array $messages = [];
    public string $model = 'opus';
    
    public function loadConversation(int $id): void
    {
        $conversation = BedrockConversation::findOrFail($id);
        $this->conversationId = $id;
        $this->messages = $conversation->messages;
        $this->model = $conversation->model;
    }
    
    public function saveConversation(): void
    {
        if ($this->conversationId) {
            BedrockConversation::where('id', $this->conversationId)->update([
                'messages' => $this->messages,
                'model' => $this->model,
            ]);
        } else {
            $conversation = BedrockConversation::create([
                'title' => substr($this->messages[0]['content'], 0, 50),
                'messages' => $this->messages,
                'model' => $this->model,
            ]);
            $this->conversationId = $conversation->id;
        }
    }
}
```

**Design Rationale**: Conversation management improves user experience by maintaining context across sessions and enabling conversation resumption. Integrates with ICTServe's True Hybrid Architecture for both guest and authenticated users.

### 1.4. Web Search Integration Service

**Purpose**: Augment AI responses with real-time web information

**Implementation**: DuckDuckGo HTML scraping with content filtering

**Key Methods**:

```php
class WebSearchService
{
    public function searchWeb(string $query): string
    {
        try {
            $url = 'https://html.duckduckgo.com/html/?q=' . urlencode($query);
            $html = @file_get_contents($url);
            
            if ($html === false) {
                return '';
            }

            preg_match_all('/<a class="result__snippet"[^>]*>(.*?)<\/a>/s', $html, $matches);
            
            if (empty($matches[1])) {
                return '';
            }

            $results = array_slice($matches[1], 0, 5);
            $cleanResults = array_map(fn($r) => strip_tags($r), $results);
            
            return implode("\n\n", $cleanResults);
        } catch (\Exception $e) {
            Log::warning('Web search failed', ['error' => $e->getMessage()]);
            return '';
        }
    }
}
```

**Security Controls**:

- **Domain Filtering**: Only trusted domains (gov.my, motac.gov.my, wikipedia.org)
- **Content Sanitization**: Strip HTML tags and malicious content
- **Rate Limiting**: Prevent abuse of external search services
- **Error Handling**: Graceful fallback when search unavailable

**Integration with Bedrock**:

```php
public function send(): void
{
    // Web search if enabled
    $context = '';
    if ($this->useInternet) {
        $context = $this->searchWeb($this->prompt);
    }

    // Build prompt with context
    $fullPrompt = $context 
        ? "Context from web search:\n\n{$context}\n\nUser question: {$this->prompt}"
        : $this->prompt;

    // Call Bedrock API with augmented prompt
    $result = $bedrock->invoke($fullPrompt, 2000, $modelMap[$this->model]);
}
```

**Design Rationale**: Web search integration provides current information beyond training data cutoffs, enhancing response accuracy and relevance. Security controls ensure safe external data integration while maintaining compliance with Malaysian data protection requirements.

### 1.5. Cost Monitoring and Budget Controls

**Purpose**: Monitor and control AWS Bedrock usage costs with automated budget enforcement

**Cost Estimation Service**:

```php
class BedrockCostService
{
    // Pricing per 1K tokens (USD) - as of December 2025
    private const MODEL_PRICING = [
        'anthropic.claude-3-5-sonnet-20241022-v2:0' => [
            'input' => 0.003,   // $3 per 1M input tokens
            'output' => 0.015,  // $15 per 1M output tokens
        ],
        'anthropic.claude-3-5-haiku-20241022-v1:0' => [
            'input' => 0.00025, // $0.25 per 1M input tokens
            'output' => 0.00125, // $1.25 per 1M output tokens
        ],
        'amazon.titan-text-premier-v1:0' => [
            'input' => 0.0005,  // $0.5 per 1M input tokens
            'output' => 0.0015,  // $1.5 per 1M output tokens
        ],
    ];
    
    public function estimateCost(string $modelId, int $inputTokens, int $outputTokens): float
    {
        $pricing = self::MODEL_PRICING[$modelId] ?? self::MODEL_PRICING['anthropic.claude-3-5-haiku-20241022-v1:0'];
        
        $inputCost = ($inputTokens / 1000) * $pricing['input'];
        $outputCost = ($outputTokens / 1000) * $pricing['output'];
        
        return $inputCost + $outputCost;
    }
    
    public function checkBudgetConstraints(float $estimatedCost): bool
    {
        $dailySpend = $this->getDailySpend();
        $monthlySpend = $this->getMonthlySpend();
        
        $dailyBudget = config('bedrock.budget.daily_usd', 100.00);
        $monthlyBudget = config('bedrock.budget.monthly_usd', 2000.00);
        
        if (($dailySpend + $estimatedCost) > $dailyBudget) {
            $this->sendBudgetAlert('daily', $dailySpend, $estimatedCost);
            return false;
        }
        
        if (($monthlySpend + $estimatedCost) > $monthlyBudget) {
            $this->sendBudgetAlert('monthly', $monthlySpend, $estimatedCost);
            return false;
        }
        
        return true;
    }
}
```

**Budget Monitoring Dashboard**:

- **Real-time Cost Tracking**: Live dashboard showing current spend vs. budget
- **Usage Analytics**: Cost breakdown by model, user, and time period
- **Budget Alerts**: Automated notifications at 80%, 90%, 100% thresholds
- **Cost Optimization**: Recommendations for model selection based on usage patterns

**Design Rationale**: Cost monitoring prevents budget overruns while enabling data-driven optimization of model selection and usage patterns. Essential for sustainable cloud AI integration in government environments.

### 1.6. Performance Analytics and Monitoring

**Purpose**: Comprehensive monitoring of Bedrock integration performance and quality

**Metrics Collection**:

```php
class BedrockMetricsCollector
{
    public function recordModelInvocation(array $metrics): void
    {
        $data = [
            'model_id' => $metrics['model_id'],
            'response_time_ms' => $metrics['response_time_ms'],
            'input_tokens' => $metrics['input_tokens'],
            'output_tokens' => $metrics['output_tokens'],
            'cost_usd' => $metrics['cost_usd'],
            'success' => $metrics['success'],
            'error_type' => $metrics['error_type'] ?? null,
            'user_id' => auth()->id(),
            'timestamp' => now(),
        ];
        
        // Store in time-series database (Redis)
        Redis::zadd('bedrock:metrics:' . date('Y-m-d'), time(), json_encode($data));
        
        // Update real-time counters
        Redis::incr('bedrock:requests:today');
        Redis::incrby('bedrock:tokens:today', $metrics['input_tokens'] + $metrics['output_tokens']);
        Redis::incrbyfloat('bedrock:cost:today', $metrics['cost_usd']);
    }
}
```

**Performance Dashboard Widgets**:

1. **Response Time Metrics**: P50, P95, P99 response times by model
2. **Cost Analytics**: Daily/monthly spend with budget tracking
3. **Quality Metrics**: Success rates, error distribution, retry counts
4. **Usage Patterns**: Requests per hour, peak usage times, model popularity
5. **Comparative Analysis**: Bedrock vs. Ollama performance comparison

**Alerting System**:

- **Performance Degradation**: Alert when response times exceed thresholds
- **Error Rate Spikes**: Notification when error rates increase significantly
- **Budget Thresholds**: Automated alerts for cost overruns
- **Service Health**: Monitor AWS Bedrock service availability

**Design Rationale**: Comprehensive monitoring enables proactive performance management, cost optimization, and quality assurance for cloud AI services. Integrates with Laravel Pulse for unified ICTServe monitoring.

### 1.7. Enhanced Configuration Management

**Configuration File** (`config/bedrock.php`):

```php
<?php

return [
    // Core Configuration
    'enabled' => env('BEDROCK_ENABLED', false),
    'region' => env('AWS_DEFAULT_REGION', 'ap-southeast-1'), // Malaysia compliance
    'version' => 'latest',
    
    // Authentication
    'credentials' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
    ],
    
    // Model Configuration
    'models' => [
        'default' => env('BEDROCK_DEFAULT_MODEL', 'anthropic.claude-3-5-haiku-20241022-v1:0'),
        'high_quality' => 'anthropic.claude-3-5-sonnet-20241022-v2:0',
        'fast_response' => 'anthropic.claude-3-5-haiku-20241022-v1:0',
        'cost_effective' => 'amazon.titan-text-premier-v1:0',
    ],
    
    // Smart Routing Rules
    'routing' => [
        'faq_simple' => 'fast_response',
        'faq_complex' => 'high_quality',
        'document_analysis' => 'high_quality',
        'auto_reply' => 'high_quality',
        'conversation' => 'default',
    ],
    
    // Streaming Configuration
    'streaming' => [
        'enabled' => env('BEDROCK_STREAMING_ENABLED', true),
        'chunk_size' => 1024,
        'timeout' => 30,
        'buffer_size' => 8192,
    ],
    
    // Web Search Integration
    'web_search' => [
        'enabled' => env('BEDROCK_WEB_SEARCH_ENABLED', false),
        'provider' => env('WEB_SEARCH_PROVIDER', 'duckduckgo'),
        'max_results' => 5,
        'timeout' => 10,
        'trusted_domains' => [
            'gov.my', 'motac.gov.my', 'malaysia.gov.my',
            'wikipedia.org', 'stackoverflow.com'
        ],
    ],
    
    // Data Residency and Compliance
    'data_residency' => [
        'enforce_malaysia' => env('BEDROCK_ENFORCE_MALAYSIA_RESIDENCY', true),
        'allowed_regions' => ['ap-southeast-1'], // Singapore (closest to Malaysia)
        'data_classification' => [
            'public' => 'allow_cloud',
            'internal' => 'local_only',
            'confidential' => 'local_only',
            'restricted' => 'local_only',
        ],
    ],
    
    // Performance and Reliability
    'performance' => [
        'max_response_time' => 3, // seconds (faster than Ollama target)
        'retry_attempts' => 2,
        'retry_delay' => 1000, // milliseconds
        'fallback_to_ollama' => true,
        'circuit_breaker' => [
            'failure_threshold' => 5,
            'recovery_timeout' => 60,
        ],
    ],
    
    // Budget and Cost Controls
    'budget' => [
        'daily_usd' => env('BEDROCK_DAILY_BUDGET', 100.00),
        'monthly_usd' => env('BEDROCK_MONTHLY_BUDGET', 2000.00),
        'alert_thresholds' => [80, 90, 100], // Percentage thresholds
        'admin_email' => env('BEDROCK_ADMIN_EMAIL', 'ict@bpm.gov.my'),
    ],
    
    // Monitoring and Logging
    'monitoring' => [
        'metrics_retention_days' => 90,
        'detailed_logging' => env('BEDROCK_DETAILED_LOGGING', false),
        'performance_tracking' => true,
        'cost_tracking' => true,
    ],
];
```

**Environment Variables** (`.env`):

```env
# AWS Bedrock Configuration
BEDROCK_ENABLED=true
AWS_ACCESS_KEY_ID=your_access_key_here
AWS_SECRET_ACCESS_KEY=your_secret_key_here
AWS_DEFAULT_REGION=ap-southeast-1
BEDROCK_DEFAULT_MODEL=anthropic.claude-3-5-haiku-20241022-v1:0

# Streaming and Web Search
BEDROCK_STREAMING_ENABLED=true
BEDROCK_WEB_SEARCH_ENABLED=false
WEB_SEARCH_PROVIDER=duckduckgo

# Budget Controls
BEDROCK_DAILY_BUDGET=100.00
BEDROCK_MONTHLY_BUDGET=2000.00
BEDROCK_ADMIN_EMAIL=ict@bpm.gov.my

# Data Residency
BEDROCK_ENFORCE_MALAYSIA_RESIDENCY=true

# Performance
BEDROCK_DETAILED_LOGGING=false
```

**Design Rationale**: Comprehensive configuration management enables fine-tuned control over Bedrock integration behavior, cost management, and compliance requirements. Environment-based configuration supports different deployment environments while maintaining security best practices.ate models for different task types (Req 9.1). Streaming responses improve user experience with real-time feedback (Req 9.3).

**Cost Optimization Strategy**:

```php
class CostOptimizationService
{ate models for different task types (Req 9.1). Streaming responses improve user experience for longer AI generations (Req 9.3). Data residency controls ensure compliance with Malaysian data protection requirements (Req 9.7).

### 2. RagService (Retrieval-Augmented Generation)

**Purpose**: Implements RAG pipeline for context-aware AI responses with conversation context management

**Key Components**:

- **Retrieval Engine**: Semantic search using vector embeddings
- **Context Builder**: Assembles relevant documents/FAQs
- **Prompt Constructor**: Builds structured prompts with context
- **Response Processor**: Post-processes AI outputs
- **Conversation Manager**: Maintains conversation history for follow-up questions
- **Fallback Handler**: Provides graceful responses when no relevant answers found

**RAG Pipeline Flow**:

1. User query → Embedding generation
2. Vector similarity search → Relevant chunks
3. Context assembly → Prompt construction
4. AI generation → Response post-processing
5. Audit logging → Response delivery

---

## Hybrid Query Routing System (v3.6.6)

### Overview

The Hybrid Query Routing System intelligently routes user queries between local Ollama processing and cloud-based AWS Bedrock based on query classification, complexity analysis, and cost optimization. This approach achieves **82% cost savings** by routing 70% of FAQ queries to free local Ollama processing.

### Query Classification Algorithm

```php
class HybridQueryRouter
{
    // FAQ-specific keywords (route to Ollama)
    private const FAQ_KEYWORDS = [
        'cara', 'bagaimana', 'apa', 'bila', 'di mana', 'siapa',
        'tiket', 'pinjaman', 'aset', 'status', 'borang', 'permohonan',
        'helpdesk', 'sokongan', 'bantuan', 'masalah', 'isu',
    ];
    
    // Complex reasoning keywords (route to Bedrock)
    private const COMPLEX_KEYWORDS = [
        'analisis', 'bandingkan', 'jelaskan', 'mengapa', 'cadangkan',
        'strategi', 'implikasi', 'kesan', 'ramalan', 'penilaian',
        'optimum', 'alternatif', 'kelebihan', 'kelemahan',
    ];
    
    public function classifyQuery(string $query): string
    {
        $faqScore = $this->calculateKeywordScore($query, self::FAQ_KEYWORDS);
        $complexScore = $this->calculateKeywordScore($query, self::COMPLEX_KEYWORDS);
        
        // Classification logic
        if ($faqScore >= 2 && $complexScore === 0) {
            return 'faq_specific';      // Route to Ollama
        }
        
        if ($complexScore >= 1 && $faqScore === 0) {
            return 'complex_reasoning'; // Route to Bedrock
        }
        
        if ($faqScore >= 1 && $complexScore >= 1) {
            return 'hybrid';            // Ollama first, Bedrock enhancement
        }
        
        return 'general';               // Default to Ollama with Bedrock fallback
    }
    
    public function routeQuery(string $query, array $context = []): array
    {
        $classification = $this->classifyQuery($query);
        
        return match($classification) {
            'faq_specific' => [
                'primary' => 'ollama',
                'fallback' => 'bedrock_haiku',
                'model' => 'gemma3:1b',
            ],
            'complex_reasoning' => [
                'primary' => 'bedrock',
                'fallback' => 'ollama',
                'model' => $this->selectBedrockModel($context),
            ],
            'hybrid' => [
                'primary' => 'ollama',
                'enhancement' => 'bedrock_haiku',
                'model' => 'gemma3:1b',
            ],
            default => [
                'primary' => 'ollama',
                'fallback' => 'bedrock_haiku',
                'model' => 'gemma3:1b',
            ],
        };
    }
    
    private function selectBedrockModel(array $context): string
    {
        $complexity = $this->analyzeComplexity($context);
        
        return match($complexity) {
            'high' => 'global.anthropic.claude-opus-4-5-20251101-v1:0',
            'medium' => 'us.anthropic.claude-sonnet-4-5-20250929-v1:0',
            'low' => 'us.anthropic.claude-haiku-4-5-20251001-v1:0',
            default => 'us.anthropic.claude-haiku-4-5-20251001-v1:0',
        };
    }
}
```

### Query Classification Examples

| Query | FAQ Score | Complex Score | Classification | Route |
|-------|-----------|---------------|----------------|-------|
| "Cara hantar tiket" | 2 | 0 | faq_specific | Ollama |
| "Status pinjaman aset" | 2 | 0 | faq_specific | Ollama |
| "Analisis sistem" | 0 | 1 | complex_reasoning | Bedrock |
| "Bandingkan pendekatan" | 0 | 1 | complex_reasoning | Bedrock |
| "Mengapa tiket perlu SLA?" | 1 | 1 | hybrid | Ollama + Bedrock |
| "Jelaskan proses kelulusan" | 1 | 1 | hybrid | Ollama + Bedrock |

### Fallback Chain Diagram

```text
┌─────────────────────────────────────────────────────────────────────────────┐
│                    Hybrid Query Routing Fallback Chain                      │
├─────────────────────────────────────────────────────────────────────────────┤
│                                                                             │
│  User Query                                                                 │
│      │                                                                      │
│      ▼                                                                      │
│  ┌─────────────────┐                                                        │
│  │ Query Classifier │                                                       │
│  │ (FAQ/Complex/   │                                                        │
│  │  Hybrid/General)│                                                        │
│  └────────┬────────┘                                                        │
│           │                                                                 │
│           ▼                                                                 │
│  ┌─────────────────────────────────────────────────────────────────────┐   │
│  │                        Routing Decision                              │   │
│  ├─────────────────┬─────────────────┬─────────────────┬───────────────┤   │
│  │   FAQ_SPECIFIC  │ COMPLEX_REASON  │     HYBRID      │    GENERAL    │   │
│  │                 │                 │                 │               │   │
│  │  ┌───────────┐  │  ┌───────────┐  │  ┌───────────┐  │ ┌───────────┐ │   │
│  │  │  Ollama   │  │  │  Bedrock  │  │  │  Ollama   │  │ │  Ollama   │ │   │
│  │  │ (Primary) │  │  │ (Primary) │  │  │ (Primary) │  │ │ (Primary) │ │   │
│  │  └─────┬─────┘  │  └─────┬─────┘  │  └─────┬─────┘  │ └─────┬─────┘ │   │
│  │        │        │        │        │        │        │       │       │   │
│  │        ▼        │        ▼        │        ▼        │       ▼       │   │
│  │  ┌───────────┐  │  ┌───────────┐  │  ┌───────────┐  │ ┌───────────┐ │   │
│  │  │  Bedrock  │  │  │  Ollama   │  │  │  Bedrock  │  │ │  Bedrock  │ │   │
│  │  │  Haiku    │  │  │ (Fallback)│  │  │  Haiku    │  │ │  Haiku    │ │   │
│  │  │ (Fallback)│  │  │           │  │  │(Enhance)  │  │ │(Fallback) │ │   │
│  │  └─────┬─────┘  │  └─────┬─────┘  │  └─────┬─────┘  │ └─────┬─────┘ │   │
│  │        │        │        │        │        │        │       │       │   │
│  │        ▼        │        ▼        │        ▼        │       ▼       │   │
│  │  ┌───────────┐  │  ┌───────────┐  │  ┌───────────┐  │ ┌───────────┐ │   │
│  │  │  Static   │  │  │  Static   │  │  │  Static   │  │ │  Static   │ │   │
│  │  │ Fallback  │  │  │ Fallback  │  │  │ Fallback  │  │ │ Fallback  │ │   │
│  │  │ (Bahasa   │  │  │ (Bahasa   │  │  │ (Bahasa   │  │ │ (Bahasa   │ │   │
│  │  │  Melayu)  │  │  │  Melayu)  │  │  │  Melayu)  │  │ │  Melayu)  │ │   │
│  │  └───────────┘  │  └───────────┘  │  └───────────┘  │ └───────────┘ │   │
│  └─────────────────┴─────────────────┴─────────────────┴───────────────┘   │
│                                                                             │
└─────────────────────────────────────────────────────────────────────────────┘
```

### Cost Optimization Flow

```text
┌─────────────────────────────────────────────────────────────────────────────┐
│                    Cost Optimization Strategy (82% Savings)                 │
├─────────────────────────────────────────────────────────────────────────────┤
│                                                                             │
│  Query Distribution (Typical ICTServe Usage):                               │
│  ┌─────────────────────────────────────────────────────────────────────┐   │
│  │  FAQ Queries (70%)        │ Complex Queries (20%) │ Hybrid (10%)    │   │
│  │  ████████████████████████ │ ██████████            │ █████           │   │
│  │  → Ollama (FREE)          │ → Bedrock (PAID)      │ → Mixed         │   │
│  └─────────────────────────────────────────────────────────────────────┘   │
│                                                                             │
│  Cost Breakdown (per 1000 queries):                                         │
│  ┌─────────────────────────────────────────────────────────────────────┐   │
│  │  Without Hybrid:                                                     │   │
│  │  - All queries to Bedrock Sonnet: 1000 × $0.90 = $900.00            │   │
│  │                                                                      │   │
│  │  With Hybrid Routing:                                                │   │
│  │  - FAQ (700 queries) → Ollama: $0.00                                │   │
│  │  - Complex (200 queries) → Bedrock Sonnet: 200 × $0.90 = $180.00    │   │
│  │  - Hybrid (100 queries) → Ollama + Haiku: 100 × $0.04 = $4.00       │   │
│  │  - Total: $184.00                                                    │   │
│  │                                                                      │   │
│  │  SAVINGS: $900.00 - $184.00 = $716.00 (79.6% savings)               │   │
│  └─────────────────────────────────────────────────────────────────────┘   │
│                                                                             │
│  Model Cost Comparison (per query):                                         │
│  ┌─────────────────────────────────────────────────────────────────────┐   │
│  │  Model          │ Avg Response Time │ Avg Tokens │ Avg Cost         │   │
│  │  ───────────────┼───────────────────┼────────────┼─────────────────  │   │
│  │  Ollama (local) │ 0.8s              │ N/A        │ $0.00            │   │
│  │  Haiku 4.5      │ 1.2s              │ 150        │ $0.04            │   │
│  │  Sonnet 4.5     │ 2.5s              │ 300        │ $0.90            │   │
│  │  Opus 4.5       │ 5.0s              │ 500        │ $7.50            │   │
│  │  Hybrid (avg)   │ 2.0s              │ 200        │ $0.05            │   │
│  └─────────────────────────────────────────────────────────────────────┘   │
│                                                                             │
└─────────────────────────────────────────────────────────────────────────────┘
```

### Model Selection Guide

| Use Case | Recommended Model | Reason | Cost/Query |
|----------|-------------------|--------|------------|
| FAQ queries | Ollama (gemma3:1b) | Free, fast, accurate for ICTServe FAQ | $0.00 |
| Simple reasoning | Haiku 4.5 | Fast, cheap, good quality | $0.04 |
| Balanced tasks | Sonnet 4.5 | Good quality/cost ratio | $0.90 |
| Complex analysis | Opus 4.5 | Best reasoning capability | $7.50 |
| Hybrid enhancement | Haiku 4.5 | Cost-effective enhancement | $0.04 |

### Rate Limits per Model

| Model | Requests/Min | Tokens/Min | Recommended Use |
|-------|--------------|------------|-----------------|
| **Opus 4.5** | 10 | 20,000 | Complex reasoning, formal responses |
| **Sonnet 4.5** | 20 | 40,000 | Balanced performance, document analysis |
| **Haiku 4.5** | 50 | 100,000 | Quick responses, simple FAQ queries |
| **Ollama** | Unlimited | Unlimited | FAQ queries, local processing |

### Inference Profile Requirements (CRITICAL)

> **PENTING**: Direct model IDs tidak berfungsi dengan on-demand throughput. Gunakan inference profile format:

```env
# ❌ WRONG - Direct model ID (akan gagal)
AWS_BEDROCK_MODEL_ID=anthropic.claude-opus-4-5-20251101-v1:0

# ✅ CORRECT - Global inference profile (Opus 4.5)
AWS_BEDROCK_MODEL_ID=global.anthropic.claude-opus-4-5-20251101-v1:0

# ✅ CORRECT - US inference profile (Sonnet/Haiku)
AWS_BEDROCK_MODEL_ID=us.anthropic.claude-haiku-4-5-20251001-v1:0
```

### Implementation Status

| Component | Status | Files |
|-----------|--------|-------|
| HybridQueryRouter | ✅ Completed | `app/Services/HybridQueryRouter.php` |
| Query Classification | ✅ Completed | Integrated in RagService |
| Fallback Chain | ✅ Completed | BedrockService + OllamaClient |
| Cost Monitoring | ✅ Completed | BedrockCostService |
| Rate Limiting | ✅ Completed | Laravel RateLimiter |
| Streaming Responses | 🔄 Future | SSE implementation pending |

---

## User Experience Scenarios (v3.6.6)

### Scenario 1: FAQ Query (Ollama Route)

```text
User: "Bagaimana cara hantar tiket helpdesk?"

1. Query Classification: faq_specific (FAQ score: 2, Complex score: 0)
2. Route: Ollama (gemma3:1b)
3. RAG Pipeline: Retrieve relevant FAQ entries
4. Response Time: ~0.8s
5. Cost: $0.00

Response: "Untuk menghantar tiket helpdesk, sila ikuti langkah berikut:
1. Layari portal ICTServe di http://127.0.0.1:8000
2. Klik 'Borang Aduan ICT' di halaman utama
3. Isi maklumat yang diperlukan
4. Klik 'Hantar' untuk menghantar tiket anda"
```

### Scenario 2: Complex Query (Bedrock Route)

```text
User: "Analisis kesan pelaksanaan sistem dual audit terhadap prestasi aplikasi"

1. Query Classification: complex_reasoning (FAQ score: 0, Complex score: 2)
2. Route: Bedrock Sonnet 4.5
3. Context: System architecture documentation
4. Response Time: ~2.5s
5. Cost: ~$0.90

Response: "Pelaksanaan sistem dual audit (owen-it + spatie) memberi kesan berikut:

**Kelebihan:**
- Pematuhan audit yang komprehensif (owen-it untuk compliance)
- Logging operasi yang terperinci (spatie untuk aktiviti)
- Kebolehkesanan penuh untuk semua perubahan data

**Kesan Prestasi:**
- Peningkatan ~5-10% dalam masa respons untuk operasi tulis
- Penggunaan storan tambahan untuk log audit
- Mitigasi: Penggunaan queue untuk audit logging asinkron

**Cadangan Optimisasi:**
- Gunakan Redis untuk caching audit queries
- Implementasi audit log archival untuk data >90 hari"
```

### Scenario 3: Hybrid Query (Ollama + Bedrock Enhancement)

```text
User: "Mengapa tiket helpdesk perlu SLA dan bagaimana ia dikira?"

1. Query Classification: hybrid (FAQ score: 1, Complex score: 1)
2. Route: Ollama (primary) + Bedrock Haiku (enhancement)
3. Ollama Response: Basic SLA information from FAQ
4. Bedrock Enhancement: Detailed calculation explanation
5. Response Time: ~2.0s
6. Cost: ~$0.04

Response: "**Keperluan SLA (dari FAQ):**
Tiket helpdesk memerlukan SLA untuk memastikan respons tepat pada masanya.

**Pengiraan SLA (penjelasan terperinci):**
SLA dikira berdasarkan kategori tiket:
- Kritikal: 4 jam respons, 24 jam penyelesaian
- Tinggi: 8 jam respons, 48 jam penyelesaian
- Sederhana: 24 jam respons, 72 jam penyelesaian
- Rendah: 48 jam respons, 1 minggu penyelesaian

Masa dikira dari tarikh penciptaan tiket, tidak termasuk hujung minggu dan cuti umum."
```

---

## Testing Patterns (PHPUnit 12 with PHP 8 Attributes)

### Hybrid Query Router Tests

```php
<?php

declare(strict_types=1);

namespace Tests\Feature\AI;

use App\Services\HybridQueryRouter;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class HybridQueryRouterTest extends TestCase
{
    private HybridQueryRouter $router;

    protected function setUp(): void
    {
        parent::setUp();
        $this->router = app(HybridQueryRouter::class);
    }

    #[Test]
    public function it_classifies_faq_queries_correctly(): void
    {
        $query = 'Cara hantar tiket helpdesk';
        $classification = $this->router->classifyQuery($query);
        
        $this->assertEquals('faq_specific', $classification);
    }

    #[Test]
    public function it_classifies_complex_queries_correctly(): void
    {
        $query = 'Analisis kesan pelaksanaan sistem dual audit';
        $classification = $this->router->classifyQuery($query);
        
        $this->assertEquals('complex_reasoning', $classification);
    }

    #[Test]
    public function it_classifies_hybrid_queries_correctly(): void
    {
        $query = 'Mengapa tiket perlu SLA dan bagaimana dikira?';
        $classification = $this->router->classifyQuery($query);
        
        $this->assertEquals('hybrid', $classification);
    }

    #[Test]
    #[DataProvider('queryClassificationProvider')]
    public function it_routes_queries_to_correct_provider(
        string $query,
        string $expectedClassification,
        string $expectedPrimary
    ): void {
        $classification = $this->router->classifyQuery($query);
        $route = $this->router->routeQuery($query);
        
        $this->assertEquals($expectedClassification, $classification);
        $this->assertEquals($expectedPrimary, $route['primary']);
    }

    public static function queryClassificationProvider(): array
    {
        return [
            'faq_query' => [
                'Cara hantar tiket',
                'faq_specific',
                'ollama',
            ],
            'complex_query' => [
                'Analisis sistem',
                'complex_reasoning',
                'bedrock',
            ],
            'hybrid_query' => [
                'Mengapa tiket perlu SLA?',
                'hybrid',
                'ollama',
            ],
            'general_query' => [
                'Hello',
                'general',
                'ollama',
            ],
        ];
    }

    #[Test]
    public function it_selects_appropriate_bedrock_model_for_complexity(): void
    {
        $highComplexityContext = [
            'query' => 'Analisis komprehensif kesan pelaksanaan sistem dengan pelbagai faktor',
            'retrieved_docs' => array_fill(0, 10, 'doc'),
        ];
        
        $route = $this->router->routeQuery(
            $highComplexityContext['query'],
            $highComplexityContext
        );
        
        $this->assertStringContainsString('opus', $route['model']);
    }

    #[Test]
    public function it_falls_back_to_ollama_when_bedrock_unavailable(): void
    {
        // Mock Bedrock service to throw exception
        $this->mock(\App\Services\BedrockService::class)
            ->shouldReceive('invoke')
            ->andThrow(new \Exception('Service unavailable'));
        
        $route = $this->router->routeQuery('Analisis sistem');
        
        $this->assertEquals('ollama', $route['fallback']);
    }
}
```

### BedrockService Integration Tests

```php
<?php

declare(strict_types=1);

namespace Tests\Feature\AI;

use App\Services\BedrockService;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\Group;
use Tests\TestCase;

#[Group('bedrock')]
class BedrockServiceTest extends TestCase
{
    #[Test]
    public function it_invokes_bedrock_model_successfully(): void
    {
        $bedrock = app(BedrockService::class);
        
        $result = $bedrock->invoke(
            'Hello, respond in Bahasa Melayu',
            100,
            'us.anthropic.claude-haiku-4-5-20251001-v1:0'
        );
        
        $this->assertTrue($result['success']);
        $this->assertNotEmpty($result['content']);
        $this->assertArrayHasKey('usage', $result);
    }

    #[Test]
    public function it_handles_invalid_model_id_gracefully(): void
    {
        $bedrock = app(BedrockService::class);
        
        $result = $bedrock->invoke(
            'Test prompt',
            100,
            'invalid.model.id'
        );
        
        $this->assertFalse($result['success']);
        $this->assertArrayHasKey('error', $result);
    }

    #[Test]
    public function it_estimates_cost_correctly(): void
    {
        $bedrock = app(BedrockService::class);
        
        $cost = $bedrock->estimateCost(
            'us.anthropic.claude-haiku-4-5-20251001-v1:0',
            1000,  // input tokens
            500    // output tokens
        );
        
        $this->assertIsFloat($cost);
        $this->assertGreaterThan(0, $cost);
    }

    #[Test]
    public function it_performs_health_check(): void
    {
        $bedrock = app(BedrockService::class);
        
        $isHealthy = $bedrock->healthCheck();
        
        $this->assertIsBool($isHealthy);
    }
}
```

---

## Troubleshooting Patterns (v3.6.6)

### Common Errors and Fixes

| Error | Cause | Fix |
|-------|-------|-----|
| `ValidationException: The provided model identifier is invalid` | Direct model ID used instead of inference profile | Use `global.*` or `us.*` prefix |
| `Uncaught TypeError: component.toJSON is not a function` | Multiple root elements in Blade view | Ensure single root div |
| `Model Access Denied` | Model not enabled in AWS Console | Enable model in Bedrock Console |
| `Ollama connection refused` | Ollama server not running | Start with `ollama serve` |
| `Rate limit exceeded` | Too many requests per minute | Implement request queuing |

### Debugging Commands

```bash
# Check Bedrock Service
php artisan tinker
$bedrock = app(\App\Services\BedrockService::class);
$result = $bedrock->invoke('Test', 10);
dd($result);

# Check Ollama Service
$ollama = app(\App\Contracts\OllamaClientContract::class);
$health = $ollama->healthCheck();
dd($health);

# Check Query Router
$router = app(\App\Services\HybridQueryRouter::class);
$classification = $router->classifyQuery('Cara hantar tiket');
dd($classification);

# Check Configuration
config('bedrock.model_id');
config('ollama.url');

# Clear All Caches
php artisan optimize:clear
```

### Log Locations

- **Laravel Logs**: `storage/logs/laravel.log`
- **Bedrock Errors**: Search for `Bedrock API Error` in logs
- **Ollama Errors**: Search for `Ollama` in logs
- **Query Routing**: Search for `HybridQueryRouter` in logs

3. Context assembly (including conversation history) → Prompt construction
4. LLM generation → Response post-processing
5. Source citation → Final response
6. Conversation state update

**Conversation Context Management**:

- Store last 5 conversation turns in session/cache
- Include conversation history in prompt context
- Implement context window management (max 4096 tokens)
- Clear conversation context after 30 minutes of inactivity

**Guest User Conversation History** (Req 1.7):

- Store guest conversations with session ID and optional email
- Provide "Claim Conversation" feature in authenticated portal
- Match conversations by email address when user logs in
- Transfer conversation history to authenticated user account
- Maintain conversation continuity across guest → authenticated transition

**Fallback Response Strategy**:

- **No Results Found** (similarity score < 0.3): Direct user to human support with ticket creation link
- **Low Confidence** (similarity score 0.3-0.5): Provide best match with disclaimer and human support option
- **Service Unavailable**: Return cached common responses or maintenance message
- **Rate Limit Exceeded**: Queue request or provide estimated wait time

**Enhanced Conversation Management (Bedrock Integration)**:

- **Long-term Memory**: Store conversation context for up to 30 days for authenticated users
- **Personalization**: Learn from user interaction patterns and preferences
- **Cross-session Continuity**: Maintain context across different browser sessions
- **Smart Context Compression**: Summarize older conversation turns to fit context window
- **Multi-model Consistency**: Maintain conversation coherence when switching between Ollama and Bedrock

**Web-Augmented Response Pipeline**:

1. **Query Analysis**: Determine if current information is needed
2. **Web Search**: Query approved search APIs (Bing Search, Google Custom Search)
3. **Source Validation**: Verify credibility and relevance of search results
4. **Content Integration**: Merge web content with existing knowledge base
5. **Response Generation**: Create comprehensive answer with source citations
6. **Audit Trail**: Log all external sources used for compliance

**Conversation Context Schema**:

```php
// Enhanced conversation context structure
[
    'user_id' => 123,
    'session_id' => 'uuid',
    'conversation_history' => [
        [
            'turn' => 1,
            'user_message' => 'Bagaimana cara reset password?',
            'ai_response' => 'Untuk reset password...',
            'model_used' => 'ollama:llama3.1',
            'timestamp' => '2025-12-12 10:30:00',
            'sources' => ['faq_123', 'doc_456'],
        ],
        // ... more turns
    ],
    'user_preferences' => [
        'preferred_response_length' => 'medium',
        'technical_level' => 'intermediate',
        'language' => 'ms',
    ],
    'context_summary' => 'User asking about password management and account security',
    'last_updated' => '2025-12-12 10:35:00',
    'expires_at' => '2026-01-11 10:30:00', // 30 days for authenticated users
]
```

**Design Rationale**: Enhanced conversation management enables more natural, personalized interactions (Req 9.5), while web-augmented responses provide up-to-date information beyond the static knowledge base (Req 9.4). Long-term memory and personalization improve user experience over time. Conversation context enables natural follow-up questions (Req 1.2), while fallback mechanisms ensure users always receive helpful guidance even when AI cannot provide direct answers (Req 1.3). Guest conversation history supports True Hybrid Architecture (D00 v3.6.0) with seamless transition from guest to authenticated access.

### 3. DocumentService

**Purpose**: Document ingestion, processing, and analysis

**Supported Formats**: PDF, DOCX, TXT (up to 10MB)

**Processing Pipeline**:

1. **Upload Validation**: File type, size, security checks
2. **Text Extraction**: Using `spatie/pdf-to-text`, `phpoffice/phpword`
3. **Content Chunking**: Split into searchable segments
4. **PII Sanitization**: Detect and redact sensitive information
5. **Embedding Generation**: Create vector representations
6. **Storage**: Persist chunks and embeddings

### 4. Filament Admin Interface

**Resources**:

- `FaqResource`: Manage FAQ entries with search and tagging
- `DocumentResource`: Upload, status tracking, re-ingestion
- `AutoReplyTemplateResource`: Template management with approval workflows
- `MessageLogResource`: Audit trail viewing with filtering

**Auto-Reply Approval Workflow**:

- **Draft Generation**: Auto-reply created with status "pending_review"
- **Review Queue**: Filament table showing pending drafts with priority sorting
- **Approval Actions**: Approve, Reject, Edit & Approve buttons
- **Approval Roles**: Admin and Superuser roles can approve (via Spatie Permission)
- **Notification**: Email notification to technician when draft approved/rejected
- **Audit Trail**: All approval actions logged with approver ID and timestamp

**Workflow States**:

1. `draft` → Auto-generated, awaiting review
2. `pending_review` → Submitted for approval
3. `approved` → Ready to send to user
4. `rejected` → Returned to technician with feedback
5. `sent` → Delivered to user

**Email-Based Approval Workflow** (Req 3.6):

- **Notification Delivery**: Send email to approvers within 60 seconds of draft creation
- **Secure Token Links**: Generate time-limited tokens (7-day validity) for approval actions
- **Email Template**: Include draft preview, approve/reject buttons, and remarks field
- **One-Click Approval**: Allow approval directly from email without admin panel login
- **Token Security**: Single-use tokens with HMAC signature verification
- **Fallback**: Provide admin panel link for users who prefer traditional workflow
- **Audit Trail**: Log all email-based approval actions with token ID and timestamp

**Design Rationale**: Approval workflow ensures quality control for AI-generated responses (Req 3.3) while maintaining accountability through audit trails (Req 4.1). Email-based approval tokens align with ICTServe's existing approval workflows (D00 v3.6.0) and support the four-tier role system (staff, approver, admin, superuser).

**Accessibility Features** - Pematuhan D12-D14 v3.6.0:

- WCAG 2.2 AA compliant forms and tables mengikut D14 Style Guide
- Keyboard navigation support (Tab, Enter, Escape for modals) selaras dengan D13 Frontend Framework
- Screen reader compatibility (ARIA labels, live regions for notifications)
- Color contrast compliance (4.5:1 text, 3:1 UI components) menggunakan palet ICTServe (Primary #0056B3, Secondary #0B4D8F)
- **Bahasa Melayu sahaja** (D15 v3.6.0): Tiada dwibahasa labels, semua teks dalam Bahasa Melayu
- Focus indicators (2px outline, visible on all interactive elements) mengikut D14 specifications
- Skip navigation links for keyboard users ("Langkau ke kandungan utama")
- **Minimum Touch Target Size** (Req 5.6): All interactive elements (buttons, links, form controls) sized at minimum 44×44px for mobile accessibility
- **Accessible Loading States** (Req 5.7):
  - Clear visual feedback for loading states with spinner and text dalam Bahasa Melayu
  - ARIA live regions for dynamic content updates
  - Error messages with proper ARIA attributes and role="alert"
  - Success notifications with accessible color combinations (not color-only)
  - Loading indicators with aria-busy and aria-live="polite"

## Data Models

### Core Models

```php
// Faq Model
class Faq extends Model implements Auditable

    use HasFactory, SoftDeletes, \OwenIt\Auditing\Auditable;

    protected $fillable = [
        'question', 'answer', 'tags', 'match_score', 'created_by'
  ;

    protected function casts(): array

        return [
            'tags' => 'array',
            'match_score' => 'float',
      ;



// Document Model
class Document extends Model implements Auditable

    use HasFactory, SoftDeletes, \OwenIt\Auditing\Auditable;

    protected $fillable = [
        'filename', 'metadata', 'uploaded_by', 'status'
  ;

    protected function casts(): array

        return [
            'metadata' => 'array',
      ;


    public function chunks(): HasMany

        return $this->hasMany(DocumentChunk::class);



// DocumentChunk Model
class DocumentChunk extends Model

    protected $fillable = [
        'document_id', 'chunk_text', 'embedding', 'source', 'chunk_index'
  ;

    protected function casts(): array

        return [
            'embedding' => 'array',
      ;


    public function document(): BelongsTo

        return $this->belongsTo(Document::class);


```

### Database Schema

**Migration Examples**:

```php
// FAQs Table - True Hybrid Architecture (D00 v3.6.0)
Schema::create('faqs', function (Blueprint $table) {
    $table->id();
    $table->string('question')->index();
    $table->longText('answer');
    $table->json('tags')->nullable();
    $table->float('match_score')->nullable();
    $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete(); // Nullable FK untuk hybrid access
    $table->timestamps();
    $table->softDeletes();

    $table->fullText(['question', 'answer']); // Full-text search fallback
});

// Documents and Chunks
Schema::create('documents', function (Blueprint $table) {
    $table->id();
    $table->string('filename');
    $table->json('metadata')->nullable();
    $table->foreignId('uploaded_by')->constrained('users');
    $table->enum('status', ['pending', 'processing', 'completed', 'failed'])->default('pending');
    $table->timestamps();
    $table->softDeletes();
});

Schema::create('document_chunks', function (Blueprint $table) {
    $table->id();
    $table->foreignId('document_id')->constrained('documents')->cascadeOnDelete();
    $table->text('chunk_text');
    $table->json('embedding'); // Vector storage
    $table->string('source')->nullable();
    $table->integer('chunk_index');
    $table->timestamps();

    $table->index(['document_id', 'chunk_index']);
});

// Auto-Reply Templates and Drafts
Schema::create('auto_reply_templates', function (Blueprint $table) {
    $table->id();
    $table->string('name');
    $table->text('template_content');
    $table->json('variables')->nullable(); // Dynamic placeholders
    $table->enum('status', ['draft', 'active', 'archived'])->default('draft');
    $table->foreignId('created_by')->constrained('users');
    $table->timestamps();
    $table->softDeletes();
});

Schema::create('auto_reply_drafts', function (Blueprint $table) {
    $table->id();
    $table->morphs('replyable'); // Polymorphic: tickets, loan applications
    $table->text('draft_content');
    $table->foreignId('template_id')->nullable()->constrained('auto_reply_templates')->nullOnDelete();
    $table->enum('status', ['draft', 'pending_review', 'approved', 'rejected', 'sent'])->default('draft');
    $table->foreignId('generated_by')->constrained('users'); // Technician
    $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
    $table->timestamp('approved_at')->nullable();
    $table->text('rejection_reason')->nullable();
    $table->timestamps();
    $table->softDeletes();

    $table->index(['status', 'created_at']);
});

// Message Logs (Audit Trail with Immutability)
Schema::create('message_logs', function (Blueprint $table) {
    $table->id();
    $table->uuid('request_id')->unique(); // X-Request-ID for traceability
    $table->enum('operation_type', ['faq_query', 'document_analysis', 'auto_reply_generation']);
    $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
    $table->text('sanitized_input'); // PII-redacted input
    $table->text('response_summary')->nullable();
    $table->json('metadata')->nullable(); // Model used, tokens, processing time
    $table->string('hash', 64); // SHA-256 hash for immutability verification
    $table->string('previous_hash', 64)->nullable(); // Chain of custody
    $table->timestamp('processed_at');
    $table->timestamps();

    $table->index(['operation_type', 'processed_at']);
    $table->index('request_id');
    $table->index('hash');
});

// Data Lineage Tracking
Schema::create('data_lineage', function (Blueprint $table) {
    $table->id();
    $table->uuid('lineage_id')->unique();
    $table->string('source_type'); // 'document', 'faq', 'user_input'
    $table->unsignedBigInteger('source_id');
    $table->string('transformation_type'); // 'embedding', 'chunking', 'sanitization'
    $table->json('transformation_metadata');
    $table->string('destination_type'); // 'embedding', 'chunk', 'response'
    $table->unsignedBigInteger('destination_id')->nullable();
    $table->timestamp('processed_at');
    $table->timestamps();

    $table->index(['source_type', 'source_id']);
    $table->index('lineage_id');
});
```

// Guest Conversation History
Schema::create('guest_conversations', function (Blueprint $table) {
$table->id();
$table->string('session_id')->index();
$table->string('email')->nullable()->index();
$table->json('conversation_history'); // Array of message turns
$table->foreignId('claimed_by_user_id')->nullable()->constrained('users')->nullOnDelete();
$table->timestamp('claimed_at')->nullable();
$table->timestamp('expires_at'); // 30-minute session timeout
$table->timestamps();

    $table->index(['email', 'claimed_by_user_id']);

});

// Approval Email Tokens
Schema::create('approval_email_tokens', function (Blueprint $table) {
$table->id();
$table->foreignId('auto_reply_draft_id')->constrained('auto_reply_drafts')->cascadeOnDelete();
$table->string('token', 64)->unique();
$table->string('action'); // 'approve' or 'reject'
$table->timestamp('expires_at');
$table->boolean('used')->default(false);
$table->timestamp('used_at')->nullable();
$table->string('used_by_ip')->nullable();
$table->timestamps();

    $table->index(['token', 'used']);

});

// Bedrock Model Configurations
Schema::create('bedrock_model_configs', function (Blueprint $table) {
    $table->id();
    $table->string('model_id')->unique(); // e.g., 'anthropic.claude-3-5-sonnet-20241022-v2:0'
    $table->string('display_name'); // e.g., 'Claude 3.5 Sonnet'
    $table->string('provider'); // 'anthropic', 'amazon', 'meta'
    $table->json('capabilities'); // ['text_generation', 'conversation', 'reasoning']
    $table->json('pricing'); // {'input_tokens': 0.003, 'output_tokens': 0.015}
    $table->integer('max_tokens')->default(4096);
    $table->integer('context_window')->default(200000);
    $table->boolean('supports_streaming')->default(true);
    $table->boolean('supports_function_calling')->default(false);
    $table->enum('status', ['active', 'deprecated', 'disabled'])->default('active');
    $table->json('routing_rules')->nullable(); // Task type mappings
    $table->timestamps();
});

// Bedrock Usage Tracking
Schema::create('bedrock_usage_logs', function (Blueprint $table) {
    $table->id();
    $table->uuid('request_id')->index();
    $table->string('model_id');
    $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
    $table->enum('operation_type', ['faq_query', 'document_analysis', 'auto_reply', 'conversation']);
    $table->integer('input_tokens');
    $table->integer('output_tokens');
    $table->decimal('estimated_cost', 10, 6); // USD
    $table->integer('response_time_ms');
    $table->float('quality_score')->nullable(); // 0.0-1.0
    $table->json('metadata')->nullable(); // Additional metrics
    $table->timestamp('processed_at');
    $table->timestamps();

    $table->index(['model_id', 'processed_at']);
    $table->index(['user_id', 'processed_at']);
});

// Enhanced Conversation Context (Bedrock)
Schema::create('conversation_contexts', function (Blueprint $table) {
    $table->id();
    $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
    $table->string('session_id')->index();
    $table->json('conversation_history'); // Extended history with model info
    $table->json('user_preferences')->nullable(); // Learned preferences
    $table->text('context_summary')->nullable(); // AI-generated summary
    $table->string('primary_model')->nullable(); // Preferred model for this conversation
    $table->json('model_performance')->nullable(); // Model performance tracking
    $table->timestamp('last_interaction_at');
    $table->timestamp('expires_at'); // 30 days for authenticated, 30 min for guest
    $table->timestamps();

    $table->index(['user_id', 'last_interaction_at']);
    $table->index(['session_id', 'expires_at']);
});

// Web Search Integration
Schema::create('web_search_logs', function (Blueprint $table) {
    $table->id();
    $table->uuid('request_id')->index();
    $table->string('search_provider'); // 'bing', 'google'
    $table->text('search_query');
    $table->json('search_results'); // Sanitized results
    $table->json('selected_sources'); // Sources used in response
    $table->boolean('content_filtered')->default(false);
    $table->text('filter_reason')->nullable();
    $table->timestamp('searched_at');
    $table->timestamps();

    $table->index(['search_provider', 'searched_at']);
});

// Model Performance Analytics
Schema::create('model_performance_metrics', function (Blueprint $table) {
    $table->id();
    $table->string('model_id');
    $table->enum('metric_type', ['response_time', 'quality_score', 'cost_efficiency', 'user_satisfaction']);
    $table->float('metric_value');
    $table->json('context')->nullable(); // Task type, complexity, etc.
    $table->date('metric_date');
    $table->timestamps();

    $table->index(['model_id', 'metric_type', 'metric_date']);
    $table->unique(['model_id', 'metric_type', 'metric_date']);
});

**Design Rationale** - Selaras dengan D00-D17 v3.6.0:

- Auto-reply tables support approval workflow (Req 3.3, 3.4) mengikut ICTServe approval patterns
- Email-based approval tokens enable one-click approval without login (Req 3.6) selaras dengan existing loan approval workflow
- Guest conversation history supports claiming feature for authenticated users (Req 1.7) menyokong True Hybrid Architecture
- Message logs with X-Request-ID and cryptographic hashing enable audit traceability (Req 4.1, 4.2, 4.6) mengikut D09 Database Documentation
- Data lineage table tracks data transformations for compliance (Req 6.5) mematuhi PDPA 2010 requirements
- Nullable user_id FK pattern konsisten dengan helpdesk_tickets dan loan_applications (D09 v3.6.0)
- Dual Audit System integration (owen-it + spatie) mengikut D00 System Overview v3.6.0
- 90-day retention enforced via scheduled cleanup job (Req 4.4)

## Correctness Properties

*A property is a characteristic or behavior that should hold true across all valid executions of a system-essentially, a formal statement about what the system should do. Properties serve as the bridge between human-readable specifications and machine-verifiable correctness guarantees.*

### Bedrock Integration Correctness Properties

Based on the prework analysis, the following correctness properties ensure reliable AWS Bedrock integration:

**Property 1: Bedrock Authentication and Model Access**
*For any* valid AWS Bedrock configuration, when the system is enabled, it should successfully authenticate and provide access only to authorized foundation models (Claude 3.5 Sonnet, Claude 3.5 Haiku, Amazon Titan) with proper IAM validation
**Validates: Requirements 9.1**

**Property 2: Smart Model Routing with Fallback**
*For any* AI processing request, the system should analyze task complexity and route to the appropriate model (Ollama or Bedrock), and when Bedrock is unavailable, automatically fallback to local Ollama processing without service interruption
**Validates: Requirements 9.2**

**Property 3: Streaming Response Implementation**
*For any* request requiring streaming responses, the system should properly implement Server-Sent Events (SSE) with correct chunk processing, buffer management, and WCAG 2.2 AA compliance for accessibility
**Validates: Requirements 9.3**

**Property 4: Web-Augmented Response Integration**
*For any* query requiring current information, the system should integrate with approved web search APIs (Bing Search, Google Custom Search), validate source credibility, and maintain audit trails for all external sources used
**Validates: Requirements 9.4**

**Property 5: Enhanced Conversation Management**
*For any* authenticated user conversation, the system should maintain context memory for up to 30 days, enable cross-session continuity, and provide personalized responses based on interaction history
**Validates: Requirements 9.5**

**Property 6: Hybrid Configuration Management**
*For any* administrative configuration change, the system should allow real-time model selection (Ollama vs Bedrock) without service disruption and apply changes consistently across all user sessions
**Validates: Requirements 9.6**

**Property 7: Data Residency Compliance**
*For any* data processing request, the system should classify data sensitivity, route confidential data to local processing only, and ensure all cloud processing occurs within Malaysian-compliant AWS regions (ap-southeast-1)
**Validates: Requirements 9.7**

**Property 8: Performance Monitoring Integration**
*For any* AI operation, the system should collect comprehensive metrics (response time, cost, quality), integrate with Laravel Pulse and Telescope, and provide real-time performance analytics accessible to admin/superuser roles
**Validates: Requirements 9.8**

### Property Reflection

After reviewing all identified properties, the following consolidations were made:

- **No Redundancy Detected**: Each property validates unique aspects of Bedrock integration
- **Comprehensive Coverage**: Properties cover authentication, routing, streaming, web search, conversation management, configuration, compliance, and monitoring
- **Distinct Validation**: Each property provides unique validation value without overlap
- **Implementation Ready**: All properties are specific enough for property-based testing implementation

## Error Handling

### Enhanced Error Handling for AWS Bedrock Integration

**AWS Bedrock Specific Error Categories**:

```php
class BedrockErrorHandler
{
    public function handleBedrockError(\Exception $e): array
    {
        $errorCode = $e->getCode();
        $errorMessage = $e->getMessage();
        
        return match(true) {
            str_contains($errorMessage, 'ValidationException') => [
                'success' => false,
                'content' => 'Maaf, format permintaan tidak sah. Sila cuba lagi.',
                'error_type' => 'validation',
                'user_message' => 'Format permintaan tidak betul',
                'retry_after' => null,
            ],
            
            str_contains($errorMessage, 'AccessDeniedException') => [
                'success' => false,
                'content' => 'Maaf, akses ke model AI tidak dibenarkan. Sila hubungi pentadbir.',
                'error_type' => 'access_denied',
                'user_message' => 'Tiada kebenaran akses',
                'retry_after' => null,
            ],
            
            str_contains($errorMessage, 'ThrottlingException') => [
                'success' => false,
                'content' => 'Perkhidmatan AI sedang sibuk. Sila cuba lagi dalam beberapa minit.',
                'error_type' => 'rate_limit',
                'user_message' => 'Terlalu banyak permintaan',
                'retry_after' => 60, // seconds
            ],
            
            str_contains($errorMessage, 'ServiceQuotaExceededException') => [
                'success' => false,
                'content' => 'Had penggunaan AI telah dicapai. Sila cuba lagi esok.',
                'error_type' => 'quota_exceeded',
                'user_message' => 'Had penggunaan tercapai',
                'retry_after' => 86400, // 24 hours
            ],
            
            str_contains($errorMessage, 'ModelTimeoutException') => [
                'success' => false,
                'content' => 'Permintaan mengambil masa terlalu lama. Sila cuba dengan soalan yang lebih ringkas.',
                'error_type' => 'timeout',
                'user_message' => 'Masa tamat tempoh',
                'retry_after' => 30,
            ],
            
            default => [
                'success' => false,
                'content' => 'Maaf, terdapat masalah teknikal. Sila cuba lagi atau hubungi sokongan.',
                'error_type' => 'unknown',
                'user_message' => 'Ralat tidak diketahui',
                'retry_after' => 300,
            ],
        };
    }
    
    public function logError(\Exception $e, array $context = []): void
    {
        Log::error('Bedrock API Error', [
            'message' => $e->getMessage(),
            'code' => $e->getCode(),
            'file' => $e->getFile(),
            'line' => $e->getLine(),
            'context' => $context,
            'timestamp' => now()->toISOString(),
            'user_id' => Auth::id(),
            'ip_address' => request()->ip(),
        ]);
    }
}
```

**Fallback Strategy Implementation**:

```php
class HybridAIService
{
    public function __construct(
        private BedrockService $bedrockService,
        private OllamaClient $ollamaClient,
        private BedrockErrorHandler $errorHandler
    ) {}
    
    public function processQuery(string $query, array $options = []): array
    {
        // Try Bedrock first if enabled
        if (config('bedrock.enabled')) {
            try {
                $result = $this->bedrockService->invoke(
                    $query, 
                    $options['max_tokens'] ?? 2000,
                    $options['model_id'] ?? null
                );
                
                if ($result['success']) {
                    return array_merge($result, ['source' => 'bedrock']);
                }
                
                // Log Bedrock failure and fallback
                Log::warning('Bedrock failed, falling back to Ollama', [
                    'bedrock_error' => $result['error'] ?? 'Unknown error',
                    'query_length' => strlen($query),
                ]);
                
            } catch (\Exception $e) {
                $this->errorHandler->logError($e, ['query' => substr($query, 0, 100)]);
            }
        }
        
        // Fallback to Ollama
        try {
            $result = $this->ollamaClient->generate([
                'model' => config('ollama.model'),
                'prompt' => $query,
                'stream' => false,
            ]);
            
            return [
                'success' => true,
                'content' => $result['response'] ?? '',
                'source' => 'ollama',
                'fallback_used' => config('bedrock.enabled'),
            ];
            
        } catch (\Exception $e) {
            $this->errorHandler->logError($e, ['query' => substr($query, 0, 100)]);
            
            return [
                'success' => false,
                'content' => 'Maaf, kedua-dua perkhidmatan AI tidak tersedia. Sila hubungi sokongan teknikal.',
                'source' => 'none',
                'error' => 'Both AI services unavailable',
            ];
        }
    }
}
```

**Enhanced Monitoring and Alerting**:

```php
class BedrockMonitoringService
{
    public function recordMetrics(array $result, string $modelId, float $processingTime): void
    {
        // Record to Laravel Pulse
        Pulse::record('bedrock_request', [
            'model' => $modelId,
            'success' => $result['success'],
            'processing_time' => $processingTime,
            'tokens_used' => $result['usage']['output_tokens'] ?? 0,
            'cost_estimate' => $this->calculateCost($modelId, $result['usage'] ?? []),
        ]);
        
        // Check for performance issues
        if ($processingTime > 30) {
            $this->sendSlowResponseAlert($modelId, $processingTime);
        }
        
        // Check for high error rates
        $errorRate = $this->calculateErrorRate($modelId);
        if ($errorRate > 0.1) { // 10% error rate threshold
            $this->sendHighErrorRateAlert($modelId, $errorRate);
        }
    }
    
    private function sendSlowResponseAlert(string $modelId, float $time): void
    {
        Notification::route('mail', config('app.admin_email'))
            ->notify(new SlowBedrockResponseNotification($modelId, $time));
    }
    
    private function sendHighErrorRateAlert(string $modelId, float $rate): void
    {
        Notification::route('mail', config('app.admin_email'))
            ->notify(new HighBedrockErrorRateNotification($modelId, $rate));
    }
}
```

### Error Categories and Responses

1. **Ollama Connection Errors**

- Timeout: Retry with exponential backoff (3 attempts: 1s, 2s, 4s)
- Service unavailable: Graceful degradation to cached responses
- Model not found: Fallback to default model (llama3.1)

1. **Document Processing Errors**

- Unsupported format: Clear error message with supported formats (PDF, DOCX, TXT)
- File too large: Size limit notification (10MB max) with compression suggestions
- Extraction failure: Partial processing with manual review option

1. **API Validation Errors**

- Standard Laravel validation dengan mesej ralat Bahasa Melayu sahaja (D15 v3.6.0)
- Rate limiting: 429 status with retry-after headers (60 requests/minute per user)
- Authentication: 401/403 with clear access requirements

1. **Performance Degradation**

- **Resource Threshold Exceeded**: When CPU > 80% or Memory > 90%
        - Queue non-urgent requests
        - Return cached responses for common queries
        - Notify admins via email
- **Response Time SLA Breach**: When response > 5 seconds
        - Log performance metrics
        - Switch to lighter model if available
        - Enable aggressive caching

### Graceful Degradation Strategy

**Tier 1 - Full Service** (Normal operation):

- Real-time AI responses
- Full RAG pipeline with embeddings
- Conversation context maintained

**Tier 2 - Reduced Service** (CPU > 70% or response time > 4s):

- Cached responses for common queries
- Simplified RAG (keyword search fallback)
- Limited conversation context (last 2 turns only)

**Tier 3 - Minimal Service** (CPU > 85% or Ollama unavailable):

- Cached responses only
- Static FAQ search (full-text)
- No AI generation, direct to human support

**Tier 4 - Emergency Mode** (System critical):

- All AI features disabled
- Display maintenance message
- Queue all requests for later processing

**Design Rationale**: Multi-tier degradation ensures system remains functional under load (Req 8.3) while maintaining user experience through cached responses and fallback mechanisms. Performance monitoring integrates with Laravel Pulse (D00 v3.6.0) for admin/superuser visibility.

### Error Response Format (D15 v3.6.0 - Bahasa Melayu Sahaja)

```json
{
    "success": false,
    "error": {
        "code": "OLLAMA_TIMEOUT",
        "message": "Perkhidmatan AI tidak tersedia buat sementara",
        "details": {
            "retry_after": 30,
            "fallback_available": true,
            "degradation_tier": 2
        }
    },
    "request_id": "uuid-here"
}
```

**Nota D15 v3.6.0**: Semua mesej ralat dalam Bahasa Melayu sahaja. Tiada sokongan dwibahasa atau penukar bahasa.

## Testing Strategy

### Enhanced Unit Tests with AWS Bedrock Integration

**BedrockService Unit Tests** (`tests/Unit/BedrockServiceTest.php`):

```php
<?php

declare(strict_types=1);

namespace Tests\Unit;

use Tests\TestCase;
use App\Services\BedrockService;
use Aws\BedrockRuntime\BedrockRuntimeClient;
use Aws\Result;
use Mockery;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\DataProvider;

class BedrockServiceTest extends TestCase
{
    private BedrockService $service;
    private $mockClient;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->mockClient = Mockery::mock(BedrockRuntimeClient::class);
        $this->service = new BedrockService($this->mockClient);
    }

    #[Test]
    public function it_can_invoke_model_successfully(): void
    {
        $mockResponse = new Result([
            'body' => $this->createMockStream([
                'content' => [['text' => 'Saya boleh membantu anda.']],
                'usage' => ['input_tokens' => 10, 'output_tokens' => 5],
            ]),
        ]);

        $this->mockClient
            ->shouldReceive('invokeModel')
            ->once()
            ->with(Mockery::on(function ($params) {
                return $params['modelId'] === 'us.anthropic.claude-haiku-4-5-20251001-v1:0'
                    && $params['contentType'] === 'application/json';
            }))
            ->andReturn($mockResponse);

        $result = $this->service->invoke('Hello', 1000);

        $this->assertTrue($result['success']);
        $this->assertEquals('Saya boleh membantu anda.', $result['content']);
        $this->assertEquals(5, $result['usage']['output_tokens']);
    }

    #[Test]
    #[DataProvider('errorProvider')]
    public function it_handles_bedrock_errors_gracefully(string $errorClass, string $expectedMessage): void
    {
        $this->mockClient
            ->shouldReceive('invokeModel')
            ->once()
            ->andThrow(new $errorClass('Test error'));

        $result = $this->service->invoke('Test prompt');

        $this->assertFalse($result['success']);
        $this->assertStringContains($expectedMessage, $result['content']);
    }

    public static function errorProvider(): array
    {
        return [
            ['Aws\Exception\AwsException', 'Maaf, terdapat masalah'],
            ['InvalidArgumentException', 'Maaf, terdapat masalah'],
            ['RuntimeException', 'Maaf, terdapat masalah'],
        ];
    }

    #[Test]
    public function it_estimates_cost_correctly(): void
    {
        $cost = $this->service->estimateCost(
            'anthropic.claude-3-5-haiku-20241022-v1:0',
            1000, // input tokens
            500   // output tokens
        );

        // Haiku pricing: $0.00025 input, $0.00125 output per 1K tokens
        $expectedCost = (1000 / 1000 * 0.00025) + (500 / 1000 * 0.00125);
        $this->assertEquals($expectedCost, $cost);
    }

    private function createMockStream(array $data): \Psr\Http\Message\StreamInterface
    {
        $stream = Mockery::mock(\Psr\Http\Message\StreamInterface::class);
        $stream->shouldReceive('getContents')->andReturn(json_encode($data));
        return $stream;
    }
}
```

**BedrockChat Livewire Component Tests** (`tests/Feature/BedrockChatTest.php`):

```php
<?php

declare(strict_types=1);

namespace Tests\Feature;

use Tests\TestCase;
use App\Livewire\BedrockChat;
use App\Services\BedrockService;
use App\Models\User;
use App\Models\BedrockConversation;
use Livewire\Livewire;
use Mockery;
use PHPUnit\Framework\Attributes\Test;

class BedrockChatTest extends TestCase
{
    #[Test]
    public function authenticated_user_can_send_message(): void
    {
        $user = User::factory()->create();
        
        $mockService = Mockery::mock(BedrockService::class);
        $mockService->shouldReceive('invoke')
            ->once()
            ->andReturn([
                'success' => true,
                'content' => 'Saya faham soalan anda.',
                'usage' => ['output_tokens' => 10],
            ]);
        
        $this->app->instance(BedrockService::class, $mockService);

        Livewire::actingAs($user)
            ->test(BedrockChat::class)
            ->set('prompt', 'Bagaimana cara menggunakan sistem ini?')
            ->set('model', 'haiku')
            ->call('send')
            ->assertSet('prompt', '')
            ->assertCount('messages', 2)
            ->assertSee('Saya faham soalan anda.');
    }

    #[Test]
    public function conversation_is_saved_to_database(): void
    {
        $user = User::factory()->create();
        
        $mockService = Mockery::mock(BedrockService::class);
        $mockService->shouldReceive('invoke')->andReturn([
            'success' => true,
            'content' => 'Respons AI',
            'usage' => ['output_tokens' => 5],
        ]);
        
        $this->app->instance(BedrockService::class, $mockService);

        Livewire::actingAs($user)
            ->test(BedrockChat::class)
            ->set('prompt', 'Test message')
            ->call('send');

        $this->assertDatabaseHas('bedrock_conversations', [
            'user_id' => $user->id,
            'model' => 'haiku',
        ]);
    }

    #[Test]
    public function user_can_load_previous_conversation(): void
    {
        $user = User::factory()->create();
        $conversation = BedrockConversation::factory()->create([
            'user_id' => $user->id,
            'messages' => [
                ['role' => 'user', 'content' => 'Hello'],
                ['role' => 'assistant', 'content' => 'Hi there!'],
            ],
            'model' => 'sonnet',
        ]);

        Livewire::actingAs($user)
            ->test(BedrockChat::class)
            ->call('loadConversation', $conversation->id)
            ->assertSet('conversationId', $conversation->id)
            ->assertSet('model', 'sonnet')
            ->assertCount('messages', 2);
    }

    #[Test]
    public function error_handling_displays_user_friendly_message(): void
    {
        $user = User::factory()->create();
        
        $mockService = Mockery::mock(BedrockService::class);
        $mockService->shouldReceive('invoke')
            ->once()
            ->andReturn([
                'success' => false,
                'content' => 'API Error',
                'error' => 'ThrottlingException',
            ]);
        
        $this->app->instance(BedrockService::class, $mockService);

        Livewire::actingAs($user)
            ->test(BedrockChat::class)
            ->set('prompt', 'Test message')
            ->call('send')
            ->assertSee('Maaf, saya tidak dapat memproses');
    }
}
```

**MCP Server Integration Tests** (`tests/Feature/McpServerTest.php`):

```php
<?php

declare(strict_types=1);

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Support\Facades\Process;
use PHPUnit\Framework\Attributes\Test;

class McpServerTest extends TestCase
{
    #[Test]
    public function mcp_server_responds_to_list_tools(): void
    {
        $input = json_encode([
            'jsonrpc' => '2.0',
            'id' => 1,
            'method' => 'tools/list',
        ]);

        $result = Process::input($input)
            ->path(base_path())
            ->run('node mcp-servers/bedrock-server.js');

        $this->assertEquals(0, $result->exitCode());
        
        $response = json_decode($result->output(), true);
        $this->assertArrayHasKey('result', $response);
        $this->assertArrayHasKey('tools', $response['result']);
        $this->assertCount(3, $response['result']['tools']);
    }

    #[Test]
    public function mcp_server_can_invoke_claude_haiku(): void
    {
        $this->markTestSkipped('Requires AWS credentials for integration testing');
        
        $input = json_encode([
            'jsonrpc' => '2.0',
            'id' => 1,
            'method' => 'tools/call',
            'params' => [
                'name' => 'invoke_claude_haiku',
                'arguments' => [
                    'prompt' => 'Apa itu AI?',
                    'maxTokens' => 100,
                ],
            ],
        ]);

        $result = Process::input($input)
            ->timeout(30)
            ->path(base_path())
            ->run('node mcp-servers/bedrock-server.js');

        $this->assertEquals(0, $result->exitCode());
        
        $response = json_decode($result->output(), true);
        $this->assertArrayHasKey('result', $response);
        $this->assertArrayHasKey('content', $response['result']);
    }
}
```

- **OllamaClient**: Mock HTTP responses, test error handling
- **RagService**: Test retrieval accuracy, prompt construction
- **DocumentService**: Test extraction, chunking, sanitization
- **Models**: Test relationships, validation, casting

### Feature Tests

- **FAQ API**: End-to-end query processing
- **Document Upload**: Complete ingestion pipeline
- **Auto-Reply**: Generation and approval workflow
- **Admin Interface**: Filament resource operations

### Accessibility Tests

- **Automated**: axe-core, Lighthouse CI integration
- **Manual**: Screen reader testing (NVDA/JAWS)
- **Keyboard Navigation**: Tab order, focus indicators
- **Color Contrast**: WCAG 2.2 AA compliance verification

### Performance Tests (Req 8.1, 8.2, 8.5)

- **Load Testing**:
  - Simulate 100 concurrent FAQ queries
  - Target: 95% requests complete within 5 seconds
  - Tool: Apache JMeter or Laravel Dusk
  - Metrics: Response time, throughput, error rate
- **Memory Usage**:
  - Monitor Ollama server memory consumption
  - Target: < 16GB RAM for quantized models
  - Validate model optimization (Q4_K_M quantization)
  - Test memory leaks during extended operation
- **Response Times**:
  - 5-second SLA compliance for 95th percentile
  - P50: < 2 seconds, P95: < 5 seconds, P99: < 8 seconds
  - Monitor degradation under load
  - Test cache hit/miss performance
- **Database Performance**:
  - Vector similarity search optimization
  - Target: < 100ms for embedding retrieval
  - Index performance validation
  - Query plan analysis for N+1 prevention
- **Uptime and Availability** (Req 8.2):
  - Target: 95% uptime during normal load
  - Health check endpoint monitoring
  - Graceful degradation testing
  - Failover and recovery time measurement

### Performance Monitoring Dashboard (Req 8.7)

**Purpose**: Real-time monitoring and historical analysis of AI system performance

**Dashboard Location**: Filament admin panel at `/admin/ollama/performance`

**Key Metrics Displayed**:

1. **Response Time Metrics**:

- P50, P95, P99 response times (line chart, last 24 hours)
- Average response time by operation type (bar chart)
- Response time distribution histogram

1. **System Health**:

- Current uptime percentage (gauge widget)
- Ollama server status (online/offline indicator)
- Failed requests count (last hour, last 24 hours)
- Error rate percentage (line chart)

1. **Cache Performance**:

- Cache hit rate percentage (gauge widget)
- Cache size and memory usage (progress bar)
- Top cached queries (table)
- Cache invalidation events (timeline)

1. **Database Performance**:

- Average database query time (gauge widget)
- Slow query count (last hour)
- N+1 query detection alerts
- Vector similarity search performance

1. **Resource Utilization**:

- CPU usage percentage (line chart)
- Memory usage (line chart with threshold indicators)
- Disk I/O operations
- Network bandwidth usage

1. **AI Operations Statistics**:

- Total operations by type (pie chart)
- Operations per hour (line chart)
- Average tokens per request
- Model usage distribution

**Data Collection**:

- **Frequency**: Metrics collected every 60 seconds
- **Storage**: Time-series data in Redis with 30-day retention
- **Aggregation**: Hourly and daily rollups for historical analysis
- **Alerting**: Email notifications when thresholds exceeded

**Dashboard Features**:

- **Date Range Selector**: Last hour, 24 hours, 7 days, 30 days, custom
- **Auto-Refresh**: Configurable refresh interval (30s, 1m, 5m, off)
- **Export**: Download metrics as CSV or PDF report
- **Drill-Down**: Click metrics to view detailed logs and traces
- **WCAG 2.2 AA Compliant**: Accessible charts with data tables and ARIA labels

**Implementation**:

- Use Filament Widgets for dashboard components
- Laravel Telescope integration for request tracing
- Redis for time-series metric storage
- Chart.js or ApexCharts for visualizations
- Background job for metric aggregation

**Design Rationale**: Real-time dashboard enables proactive performance monitoring, quick issue identification, and data-driven optimization decisions (Req 8.7).

## Bedrock Implementation Architecture

### Service Layer Implementation

**BedrockService** (`app/Services/BedrockService.php`):

```php
<?php

namespace App\Services;

use Aws\BedrockRuntime\BedrockRuntimeClient;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class BedrockService implements BedrockClientContract
{
    public function __construct(
        private BedrockRuntimeClient $client,
        private BedrockCostService $costService,
        private BedrockMetricsCollector $metricsCollector
    ) {}

    public function invoke(string $prompt, int $maxTokens = 1000, ?string $modelId = null): array
    {
        $startTime = microtime(true);
        $modelId = $modelId ?? config('bedrock.models.default');
        
        try {
            // Estimate cost and check budget
            $estimatedCost = $this->costService->estimateCost($modelId, strlen($prompt) / 4, $maxTokens);
            
            if (!$this->costService->checkBudgetConstraints($estimatedCost)) {
                return [
                    'success' => false,
                    'content' => 'Budget limit exceeded. Request blocked.',
                    'usage' => [],
                    'error_type' => 'budget_exceeded',
                ];
            }

            // Invoke model
            $response = $this->client->invokeModel([
                'modelId' => $modelId,
                'contentType' => 'application/json',
                'accept' => 'application/json',
                'body' => json_encode([
                    'anthropic_version' => 'bedrock-2023-05-31',
                    'max_tokens' => $maxTokens,
                    'messages' => [
                        [
                            'role' => 'user',
                            'content' => $prompt,
                        ],
                    ],
                ]),
            ]);

            $result = json_decode($response['body']->getContents(), true);
            $responseTime = (microtime(true) - $startTime) * 1000;

            // Record metrics
            $this->metricsCollector->recordModelInvocation([
                'model_id' => $modelId,
                'response_time_ms' => $responseTime,
                'input_tokens' => $result['usage']['input_tokens'] ?? 0,
                'output_tokens' => $result['usage']['output_tokens'] ?? 0,
                'cost_usd' => $estimatedCost,
                'success' => true,
            ]);

            return [
                'success' => true,
                'content' => $result['content'][0]['text'] ?? '',
                'usage' => $result['usage'] ?? [],
                'metadata' => [
                    'model' => $modelId,
                    'response_time_ms' => $responseTime,
                    'cost_usd' => $estimatedCost,
                ],
            ];
        } catch (\Exception $e) {
            $responseTime = (microtime(true) - $startTime) * 1000;
            
            Log::error('Bedrock API Error', [
                'model' => $modelId,
                'error' => $e->getMessage(),
                'response_time_ms' => $responseTime,
            ]);

            // Record failed metrics
            $this->metricsCollector->recordModelInvocation([
                'model_id' => $modelId,
                'response_time_ms' => $responseTime,
                'input_tokens' => 0,
                'output_tokens' => 0,
                'cost_usd' => 0,
                'success' => false,
                'error_type' => get_class($e),
            ]);

            return [
                'success' => false,
                'content' => 'Error: ' . $e->getMessage(),
                'usage' => [],
                'error_type' => get_class($e),
            ];
        }
    }

    public function invokeModelWithStreaming(string $modelId, array $payload): \Generator
    {
        try {
            $response = $this->client->invokeModelWithResponseStream([
                'modelId' => $modelId,
                'contentType' => 'application/json',
                'accept' => 'application/json',
                'body' => json_encode($payload),
            ]);

            foreach ($response['body'] as $chunk) {
                $chunkData = json_decode($chunk['chunk']['bytes'], true);
                
                if (isset($chunkData['delta']['text'])) {
                    yield [
                        'type' => 'content_block_delta',
                        'delta' => ['text' => $chunkData['delta']['text']],
                    ];
                }
                
                if (isset($chunkData['type']) && $chunkData['type'] === 'message_stop') {
                    yield [
                        'type' => 'message_stop',
                        'usage' => $chunkData['usage'] ?? [],
                    ];
                    break;
                }
            }
        } catch (\Exception $e) {
            Log::error('Bedrock Streaming Error', ['error' => $e->getMessage()]);
            yield [
                'type' => 'error',
                'error' => $e->getMessage(),
            ];
        }
    }

    public function healthCheck(): bool
    {
        try {
            $response = $this->client->listFoundationModels([
                'byProvider' => 'anthropic',
            ]);
            
            return !empty($response['modelSummaries']);
        } catch (\Exception $e) {
            Log::warning('Bedrock health check failed', ['error' => $e->getMessage()]);
            return false;
        }
    }
}
```

### Web Interface Implementation

**BedrockChat Livewire Component** (`app/Livewire/BedrockChat.php`):

```php
<?php

namespace App\Livewire;

use Livewire\Component;
use App\Services\BedrockService;
use App\Services\WebSearchService;
use App\Models\BedrockConversation;

class BedrockChat extends Component
{
    public string $prompt = '';
    public string $model = 'haiku';
    public array $messages = [];
    public bool $useInternet = false;
    public ?int $conversationId = null;
    public bool $showSidebar = true;
    public bool $sending = false;

    protected $listeners = ['conversationSelected' => 'loadConversation'];

    public function mount(?int $id = null): void
    {
        if ($id) {
            $this->loadConversation($id);
        }
    }

    public function send(): void
    {
        if (empty(trim($this->prompt))) {
            return;
        }

        $this->sending = true;

        // Add user message
        $this->messages[] = [
            'role' => 'user',
            'content' => $this->prompt,
            'timestamp' => now()->toISOString(),
        ];

        // Web search if enabled
        $context = '';
        if ($this->useInternet) {
            $webSearch = app(WebSearchService::class);
            $context = $webSearch->searchWeb($this->prompt);
        }

        // Build prompt with context
        $fullPrompt = $context 
            ? "Context from web search:\n\n{$context}\n\nUser question: {$this->prompt}"
            : $this->prompt;

        // Map model name to ID
        $modelMap = [
            'opus' => 'global.anthropic.claude-opus-4-5-20251101-v1:0',
            'sonnet' => 'us.anthropic.claude-sonnet-4-5-20250929-v1:0',
            'haiku' => 'us.anthropic.claude-haiku-4-5-20251001-v1:0',
        ];

        // Call Bedrock API
        $bedrock = app(BedrockService::class);
        $result = $bedrock->invoke($fullPrompt, 2000, $modelMap[$this->model]);

        // Add assistant response
        if ($result['success']) {
            $this->messages[] = [
                'role' => 'assistant',
                'content' => $result['content'],
                'timestamp' => now()->toISOString(),
                'metadata' => $result['metadata'] ?? [],
                'sources' => $context ? ['web_search'] : [],
            ];
        } else {
            $this->messages[] = [
                'role' => 'assistant',
                'content' => 'Maaf, terdapat ralat dalam memproses permintaan anda: ' . $result['content'],
                'timestamp' => now()->toISOString(),
                'error' => true,
            ];
        }

        // Save conversation
        $this->saveConversation();

        // Reset input
        $this->prompt = '';
        $this->sending = false;
    }

    public function newConversation(): void
    {
        $this->conversationId = null;
        $this->messages = [];
        $this->prompt = '';
        $this->sending = false;
    }

    public function loadConversation(int $id): void
    {
        $conversation = BedrockConversation::findOrFail($id);
        $this->conversationId = $id;
        $this->messages = $conversation->messages;
        $this->model = $conversation->model;
        $this->sending = false;
    }

    public function deleteConversation(int $id): void
    {
        BedrockConversation::findOrFail($id)->delete();
        
        if ($this->conversationId === $id) {
            $this->newConversation();
        }
        
        $this->dispatch('conversationDeleted', $id);
    }

    private function saveConversation(): void
    {
        if (empty($this->messages)) {
            return;
        }

        $title = $this->conversationId 
            ? BedrockConversation::find($this->conversationId)->title
            : substr($this->messages[0]['content'], 0, 50);

        if ($this->conversationId) {
            BedrockConversation::where('id', $this->conversationId)->update([
                'messages' => $this->messages,
                'model' => $this->model,
                'updated_at' => now(),
            ]);
        } else {
            $conversation = BedrockConversation::create([
                'title' => $title,
                'messages' => $this->messages,
                'model' => $this->model,
            ]);
            $this->conversationId = $conversation->id;
        }
    }

    public function render()
    {
        $conversations = BedrockConversation::latest()->limit(20)->get();
        
        return view('livewire.bedrock-chat', [
            'conversations' => $conversations,
        ]);
    }
}
```

### Database Schema Implementation

**Migration for Bedrock Conversations**:

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bedrock_conversations', function (Blueprint $table) {
            $table->id();
            $table->string('title')->nullable();
            $table->json('messages');
            $table->string('model')->default('haiku');
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade');
            $table->timestamps();
            
            $table->index(['user_id', 'created_at']);
            $table->index('model');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bedrock_conversations');
    }
};
```

**Migration for Bedrock Metrics**:

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bedrock_metrics', function (Blueprint $table) {
            $table->id();
            $table->string('model_id');
            $table->integer('response_time_ms');
            $table->integer('input_tokens');
            $table->integer('output_tokens');
            $table->decimal('cost_usd', 10, 6);
            $table->boolean('success');
            $table->string('error_type')->nullable();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->timestamp('created_at');
            
            $table->index(['created_at', 'model_id']);
            $table->index(['user_id', 'created_at']);
            $table->index('success');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bedrock_metrics');
    }
};
```

### Route Configuration

**Web Routes** (`routes/web.php`):

```php
use App\Livewire\BedrockChat;

Route::middleware(['auth'])->group(function () {
    Route::get('/bedrock-chat/{id?}', BedrockChat::class)->name('bedrock-chat');
});

// Public route for guest access (if enabled)
Route::get('/ai-chat', BedrockChat::class)->name('ai-chat-guest');
```

**API Routes** (`routes/api.php`):

```php
use App\Http\Controllers\Api\BedrockController;

Route::middleware(['auth:sanctum'])->prefix('v1/bedrock')->group(function () {
    Route::post('/invoke', [BedrockController::class, 'invoke']);
    Route::post('/stream', [BedrockController::class, 'stream']);
    Route::get('/models', [BedrockController::class, 'listModels']);
    Route::get('/health', [BedrockController::class, 'health']);
    Route::get('/metrics', [BedrockController::class, 'metrics']);
});
```

### Service Provider Registration

**BedrockServiceProvider** (`app/Providers/BedrockServiceProvider.php`):

```php
<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Aws\BedrockRuntime\BedrockRuntimeClient;
use App\Services\BedrockService;
use App\Contracts\BedrockClientContract;

class BedrockServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(BedrockRuntimeClient::class, function ($app) {
            return new BedrockRuntimeClient([
                'region' => config('bedrock.region'),
                'version' => config('bedrock.version'),
                'credentials' => [
                    'key' => config('bedrock.credentials.key'),
                    'secret' => config('bedrock.credentials.secret'),
                ],
            ]);
        });

        $this->app->bind(BedrockClientContract::class, BedrockService::class);
    }

    public function boot(): void
    {
        $this->publishes([
            __DIR__.'/../../config/bedrock.php' => config_path('bedrock.php'),
        ], 'bedrock-config');
    }
}
```

### Bedrock-Specific Testing Strategy

#### Model Performance Testing

**Purpose**: Validate Bedrock model performance, cost efficiency, and quality across different task types

**Test Categories**:

1. **Model Routing Tests**:
   - Verify correct model selection based on task complexity
   - Test fallback mechanisms when preferred model unavailable
   - Validate cost optimization through smart routing
   - Measure routing decision time (target: <50ms)

2. **Streaming Response Tests**:
   - Test Server-Sent Events (SSE) implementation
   - Validate chunk processing and buffer management
   - Test WCAG 2.2 AA compliance for streaming content
   - Verify graceful handling of connection interruptions

3. **Web-Augmented Response Tests**:
   - Test web search integration (Bing Search API, Google Custom Search)
   - Validate source credibility verification
   - Test content filtering and sanitization
   - Verify audit trail for external sources

4. **Cost Monitoring Tests**:
   - Validate token counting accuracy
   - Test cost estimation algorithms
   - Verify budget alert mechanisms
   - Test cost optimization strategies

#### Integration Testing

**Hybrid Architecture Tests**:

- **Ollama-Bedrock Failover**: Test seamless fallback from Bedrock to Ollama
- **Model Consistency**: Verify conversation coherence when switching models
- **Performance Comparison**: Benchmark response quality and speed
- **Data Residency**: Validate Malaysian data classification and routing

**API Integration Tests**:

- **AWS SDK Integration**: Test Bedrock client connectivity and error handling
- **Authentication**: Validate IAM role-based access and security
- **Rate Limiting**: Test AWS service quotas and throttling
- **Regional Compliance**: Verify ap-southeast-1 region enforcement

#### Load Testing (Bedrock-Specific)

**Concurrent Model Usage**:

- Test multiple model invocations simultaneously
- Validate resource allocation and queuing
- Measure performance degradation under load
- Test auto-scaling and throttling mechanisms

**Cost Impact Testing**:

- Monitor cost accumulation under various load patterns
- Test cost-based circuit breakers
- Validate budget enforcement mechanisms
- Measure cost per operation across models

#### Quality Assurance Testing

**Response Quality Metrics**:

- **Accuracy**: Compare responses against ground truth datasets
- **Relevance**: Measure response relevance to user queries
- **Consistency**: Test response consistency across multiple runs
- **Bias Detection**: Validate responses for cultural and linguistic bias

**Bahasa Melayu Language Testing**:

- Test model performance with Bahasa Melayu inputs
- Validate cultural context understanding
- Test technical term translation accuracy
- Verify response appropriateness for Malaysian context

#### Security and Compliance Testing

**Data Residency Testing**:

- Verify data classification enforcement
- Test regional routing compliance
- Validate cross-border data transfer prevention
- Test audit trail completeness

**Privacy Testing**:

- Test PII detection in Bedrock requests/responses
- Validate data sanitization before cloud processing
- Test conversation context privacy controls
- Verify PDPA 2010 compliance mechanisms

#### Automated Testing Pipeline

**Continuous Integration Tests**:

```yaml
# .github/workflows/bedrock-tests.yml
name: Bedrock Integration Tests
on: [push, pull_request]

jobs:
  bedrock-tests:
    runs-on: ubuntu-latest
    steps:
      - name: Model Routing Tests
        run: php artisan test --filter=BedrockModelRoutingTest
      
      - name: Streaming Response Tests
        run: php artisan test --filter=BedrockStreamingTest
      
      - name: Cost Monitoring Tests
        run: php artisan test --filter=BedrockCostTest
      
      - name: Integration Tests
        run: php artisan test --filter=BedrockIntegrationTest
        env:
          AWS_ACCESS_KEY_ID: ${{ secrets.AWS_ACCESS_KEY_ID }}
          AWS_SECRET_ACCESS_KEY: ${{ secrets.AWS_SECRET_ACCESS_KEY }}
          BEDROCK_ENABLED: true
```

**Performance Benchmarking**:

- Daily automated performance tests
- Model comparison reports
- Cost efficiency tracking
- Quality score monitoring

**Test Data Management**:

- Synthetic test datasets for various scenarios
- Anonymized production data samples
- Multi-language test cases (Bahasa Melayu focus)
- Edge case and error condition datasets

**Design Rationale**: Comprehensive Bedrock testing ensures reliable cloud AI integration while maintaining performance, cost efficiency, and compliance standards (Req 9.1-9.8). Automated testing pipeline enables continuous validation of model performance and cost optimization.

## Security Considerations

### Data Protection

- **PII Sanitization**: Automated detection and redaction using regex patterns (IC numbers, phone numbers, emails)
- **Encryption**: AES-256 for sensitive data at rest, TLS 1.3 for data in transit
- **Access Control**: Role-based permissions with Spatie Laravel Permission (4 roles: Staff, Approver, Admin, Superuser)
- **Audit Logging**: Comprehensive trail with X-Request-ID for request traceability
- **Immutable Audit Logs** (Req 4.6):
  - SHA-256 cryptographic hashing of each audit log entry
  - Chain of custody with previous_hash linking
  - Tamper detection through hash verification
  - Append-only log structure (no updates or deletes)
  - Periodic integrity verification job

### Network Security

- **Local Processing**: No external API calls, all LLM processing on localhost:11434
- **TLS Encryption**: All internal communications between Laravel and Ollama server
- **Rate Limiting**: 60 requests/minute per user, 1000 requests/hour per IP
- **Input Validation**: Comprehensive sanitization (strip HTML tags, SQL injection prevention, XSS protection)
- **Firewall Rules**: Ollama server accessible only from Laravel application server
- **External Connectivity Detection** (Req 6.3):
  - Monitor all outbound network connections during AI operations
  - Block unauthorized external API calls
  - Log security events with alert severity levels
  - Email notification to admin users within 5 minutes of detection
  - Automatic service degradation to prevent data leakage

### Privacy Compliance (PDPA 2010)

- **Data Minimization**: Collect only necessary information (no personal data in embeddings)
- **Retention Policies**:
  - Operational logs: 90 days
  - Audit logs: 7 years (compliance requirement)
  - Embeddings: Retained while source document active
  - Conversation context: 30 minutes session timeout
- **User Rights**:
  - Access: API endpoint to retrieve user's AI interaction history
  - Correction: Update/delete message logs via admin panel
  - Deletion: Cascade delete all user AI data on account deletion
- **Consent Management**: Clear privacy notices on first AI interaction
- **Data Residency**: All data stored within Malaysian jurisdiction (no cross-border transfers)

### Data Lineage Tracking

**Purpose**: Track data transformations for compliance and debugging (Req 6.5)

**Tracked Operations**:

1. Document upload → Text extraction → Chunking → Embedding generation
2. User query → Embedding → Vector search → Response generation
3. PII detection → Sanitization → Storage

**Lineage Record Structure**:

```php
[
    'lineage_id' => 'uuid',
    'source_type' => 'document',
    'source_id' => 123,
    'transformation_type' => 'embedding',
    'transformation_metadata' => [
        'model' => 'llama3.1',
        'embedding_dimensions' => 4096,
        'processing_time_ms' => 250
    ],
    'destination_type' => 'embedding',
    'destination_id' => 456,
    'processed_at' => '2025-11-05 10:30:00'
]
```

**Design Rationale**: Data lineage enables compliance audits, debugging, and impact analysis when data sources change. Essential for PDPA compliance and data governance (Req 6.5). Aligns with D09 Database Documentation v3.6.0 audit requirements and 7-year retention policies.

### Bedrock-Specific Security Considerations

#### Cloud Data Protection

**Data Classification and Routing** (Req 9.7):

```php
class DataClassificationService
{
    private const CLASSIFICATION_RULES = [
        'public' => 'allow_cloud',
        'internal' => 'local_only',
        'confidential' => 'local_only',
        'restricted' => 'local_only',
    ];
    
    public function classifyContent(string $content): string
    {
        // PII detection patterns
        $piiPatterns = [
            'ic_number' => '/\b\d{6}-\d{2}-\d{4}\b/',
            'phone_number' => '/\+60\d{8,9}/',
            'email' => '/[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}/',
            'bank_account' => '/\b\d{10,16}\b/',
        ];
        
        foreach ($piiPatterns as $type => $pattern) {
            if (preg_match($pattern, $content)) {
                return 'confidential'; // Force local processing
            }
        }
        
        // Content sensitivity analysis
        $sensitiveKeywords = [
            'rahsia', 'sulit', 'terhad', 'confidential', 'secret',
            'password', 'kata laluan', 'pin', 'salary', 'gaji'
        ];
        
        foreach ($sensitiveKeywords as $keyword) {
            if (stripos($content, $keyword) !== false) {
                return 'internal'; // Local processing preferred
            }
        }
        
        return 'public'; // Safe for cloud processing
    }
    
    public function canProcessInCloud(string $classification): bool
    {
        return self::CLASSIFICATION_RULES[$classification] === 'allow_cloud';
    }
}
```

**Regional Compliance Enforcement**:

- **Allowed Regions**: Only `ap-southeast-1` (Singapore - closest to Malaysia)
- **Region Validation**: Verify Bedrock endpoint region before each request
- **Fallback Strategy**: Route to local Ollama if region compliance fails
- **Audit Trail**: Log all region routing decisions with justification

#### AWS IAM Security

**Principle of Least Privilege**:

```json
{
  "Version": "2012-10-17",
  "Statement": [
    {
      "Effect": "Allow",
      "Action": [
        "bedrock:InvokeModel",
        "bedrock:InvokeModelWithResponseStream",
        "bedrock:ListFoundationModels",
        "bedrock:GetFoundationModel"
      ],
      "Resource": [
        "arn:aws:bedrock:ap-southeast-1::foundation-model/anthropic.claude-3-5-sonnet-20241022-v2:0",
        "arn:aws:bedrock:ap-southeast-1::foundation-model/anthropic.claude-3-5-haiku-20241022-v1:0",
        "arn:aws:bedrock:ap-southeast-1::foundation-model/amazon.titan-text-premier-v1:0"
      ],
      "Condition": {
        "StringEquals": {
          "aws:RequestedRegion": "ap-southeast-1"
        }
      }
    }
  ]
}
```

**Security Controls**:

- **Role-Based Access**: Separate IAM roles for different environments (dev, staging, prod)
- **Temporary Credentials**: Use AWS STS for time-limited access tokens
- **Request Signing**: All requests signed with AWS Signature Version 4
- **VPC Endpoints**: Use VPC endpoints for private connectivity (if available)

#### Cost Security and Budget Controls

**Budget Enforcement** (Req 9.8):

```php
class BedrockCostController
{
    private const DAILY_BUDGET_USD = 100.00;
    private const MONTHLY_BUDGET_USD = 2000.00;
    
    public function checkBudgetBeforeRequest(float $estimatedCost): bool
    {
        $dailySpend = $this->getDailySpend();
        $monthlySpend = $this->getMonthlySpend();
        
        if (($dailySpend + $estimatedCost) > self::DAILY_BUDGET_USD) {
            $this->triggerBudgetAlert('daily', $dailySpend, $estimatedCost);
            return false;
        }
        
        if (($monthlySpend + $estimatedCost) > self::MONTHLY_BUDGET_USD) {
            $this->triggerBudgetAlert('monthly', $monthlySpend, $estimatedCost);
            return false;
        }
        
        return true;
    }
    
    private function triggerBudgetAlert(string $period, float $currentSpend, float $requestCost): void
    {
        // Send immediate alert to admin
        Mail::to(config('bedrock.admin_email'))
            ->send(new BudgetExceededAlert($period, $currentSpend, $requestCost));
        
        // Log security event
        Log::warning('Bedrock budget threshold exceeded', [
            'period' => $period,
            'current_spend' => $currentSpend,
            'request_cost' => $requestCost,
            'user_id' => auth()->id(),
        ]);
    }
}
```

**Cost Monitoring Features**:

- **Real-time Tracking**: Monitor token usage and cost per request
- **Budget Alerts**: Automated notifications at 80%, 90%, 100% thresholds
- **Usage Analytics**: Daily/monthly cost reports with model breakdown
- **Cost Optimization**: Automatic model downgrading when budget constraints active

#### Web Search Security

**Source Validation and Content Filtering**:

```php
class WebSearchSecurityService
{
    private const TRUSTED_DOMAINS = [
        'gov.my', 'motac.gov.my', 'malaysia.gov.my',
        'wikipedia.org', 'stackoverflow.com'
    ];
    
    private const BLOCKED_CONTENT_TYPES = [
        'adult', 'gambling', 'violence', 'hate_speech'
    ];
    
    public function validateSearchResults(array $results): array
    {
        $validatedResults = [];
        
        foreach ($results as $result) {
            // Domain validation
            $domain = parse_url($result['url'], PHP_URL_HOST);
            if (!$this->isDomainTrusted($domain)) {
                continue;
            }
            
            // Content filtering
            if ($this->containsBlockedContent($result['content'])) {
                Log::warning('Blocked web search result', [
                    'url' => $result['url'],
                    'reason' => 'content_filter',
                ]);
                continue;
            }
            
            // Sanitize content
            $result['content'] = $this->sanitizeContent($result['content']);
            $validatedResults[] = $result;
        }
        
        return $validatedResults;
    }
    
    private function isDomainTrusted(string $domain): bool
    {
        foreach (self::TRUSTED_DOMAINS as $trustedDomain) {
            if (str_ends_with($domain, $trustedDomain)) {
                return true;
            }
        }
        return false;
    }
}
```

**Web Search Audit Trail**:

- **Query Logging**: Log all web search queries with sanitized content
- **Source Tracking**: Record all external sources used in responses
- **Content Filtering**: Log blocked content with reasons
- **Usage Analytics**: Monitor web search usage patterns and costs

#### Streaming Response Security

**Server-Sent Events (SSE) Security**:

- **Authentication**: Validate user session for each SSE connection
- **Rate Limiting**: Limit concurrent streaming connections per user
- **Content Validation**: Sanitize each chunk before streaming
- **Connection Security**: Use HTTPS for all streaming connections
- **Timeout Controls**: Automatic connection termination after inactivity

**WCAG 2.2 AA Compliance for Streaming**:

- **Screen Reader Support**: ARIA live regions for streaming content updates
- **Keyboard Navigation**: Pause/resume controls accessible via keyboard
- **Visual Indicators**: Clear loading states and progress indicators
- **Error Handling**: Accessible error messages for connection failures

**Design Rationale**: Bedrock-specific security controls ensure safe cloud AI integration while maintaining data sovereignty, cost control, and compliance with Malaysian regulations (Req 9.7, 9.8). Multi-layered security approach protects against data leakage, cost overruns, and unauthorized access.

### Audit Report Generation (Req 4.7)

**Purpose**: Generate comprehensive audit reports for regulatory compliance and internal review

**Report Formats**:

- **CSV**: Comma-separated values for data analysis tools
- **PDF**: Formatted reports with MOTAC branding and accessibility features
- **Excel**: Spreadsheet format with multiple sheets and pivot tables

**Report Types**:

1. **AI Operations Summary**: Aggregated statistics by operation type, user, date range
2. **User Activity Report**: Individual user AI interaction history with timestamps
3. **Compliance Audit Report**: PDPA compliance verification with data lineage tracking
4. **Performance Metrics Report**: Response times, cache hit rates, error rates
5. **Security Incident Report**: Unauthorized access attempts, PII detection events

**Report Features**:

- **Accessible Structure**: Proper column headers, table markup, metadata for screen readers
- **Bahasa Melayu Sahaja** (D15 v3.6.0): Headers dan labels dalam Bahasa Melayu sahaja tanpa penukar bahasa
- **Date Range Filtering**: Custom date ranges with preset options (last 7 days, 30 days, 90 days, custom)
- **Export Scheduling**: Automated report generation on schedule (daily, weekly, monthly)
- **Secure Distribution**: Email delivery with password-protected attachments for sensitive reports

**Implementation**:

- Use Laravel Excel package for Excel/CSV generation
- Use DomPDF or Snappy for PDF generation with WCAG-compliant templates
- Queue report generation jobs for large datasets
- Store generated reports in secure storage with 90-day retention

## API Design and Versioning

### RESTful API Structure (Req 7.1, 7.4, 7.5)

**Base URL**: `/api/v1/ollama`

**Endpoints**:

```text
POST   /api/v1/ollama/faq/query           - FAQ Bot query
POST   /api/v1/ollama/documents/upload    - Document upload
GET    /api/v1/ollama/documents/{id}      - Document status
POST   /api/v1/ollama/documents/{id}/analyze - Trigger analysis
POST   /api/v1/ollama/auto-reply/generate - Generate draft reply
GET    /api/v1/ollama/auto-reply/{id}     - Get draft status
PUT    /api/v1/ollama/auto-reply/{id}/approve - Approve draft
GET    /api/v1/ollama/health               - Health check
```

### API Versioning Strategy (Req 7.5)

**Versioning Approach**: URL-based versioning (`/api/v1/`, `/api/v2/`)

**Version Support Policy**:

- **Current Version** (v1): Full support, all features
- **Previous Version** (v0): Deprecated, 6-month sunset period
- **Legacy Version**: Read-only, 12-month total support

**Breaking Changes Requiring New Version**:

- Response structure changes
- Required parameter additions
- Authentication method changes
- Endpoint removal or renaming

**Non-Breaking Changes** (Same Version):

- Optional parameter additions
- New endpoints
- Response field additions
- Performance improvements

**Version Migration Path**:

1. Announce new version 3 months before release
2. Provide migration guide with code examples
3. Run both versions in parallel for 6 months
4. Deprecate old version with sunset date
5. Remove old version after 12 months total

**Version Headers**:

```text
X-API-Version: 1.0
X-Deprecated: false
X-Sunset-Date: null
```

### Authentication and Rate Limiting (Req 7.2, 7.3)

**Authentication**: Laravel Sanctum token-based

```text
Authorization: Bearer {token}
```

**Rate Limiting**:

- **Per User**: 60 requests/minute
- **Per IP**: 1000 requests/hour
- **Burst Allowance**: 10 requests (for short spikes)

**Rate Limit Headers**:

```text
X-RateLimit-Limit: 60
X-RateLimit-Remaining: 45
X-RateLimit-Reset: 1699123456
Retry-After: 30
```

**Rate Limit Response** (429 Too Many Requests) - Bahasa Melayu sahaja (D15 v3.6.0):

```json
{
    "success": false,
    "error": {
        "code": "RATE_LIMIT_EXCEEDED",
        "message": "Terlalu banyak permintaan. Sila cuba lagi kemudian.", // Bahasa Melayu sahaja
        "details": {
            "retry_after": 30,
            "limit": 60,
            "window": "1 minute"
        }
    },
    "request_id": "uuid-here"
}
```

### API Integration with ICTServe Infrastructure (Req 7.6)

**Shared Infrastructure Components**:

- **Authentication**: Unified Laravel Sanctum token system across all ICTServe APIs
- **Rate Limiting**: Shared Redis-based rate limiter with consistent limits
- **Logging**: Centralized logging with Laravel Auditing package
- **Error Handling**: Consistent error response format across helpdesk, asset loan, and AI APIs
- **Middleware Stack**: Shared middleware for authentication, rate limiting, CORS, and request logging

**Integration Points**:

1. **Helpdesk Module Integration**:

- Auto-reply generation for ticket responses
- FAQ Bot embedded in ticket submission forms
- Document analysis for ticket attachments

1. **Asset Loan Module Integration**:

- Auto-reply generation for loan application responses
- Document analysis for loan-related documents
- FAQ Bot for loan policy questions

1. **Unified API Gateway**:

- Single API base URL: `/api/v1/`
- Consistent authentication across all modules
- Shared rate limiting pool
- Unified API documentation at `/api/documentation`

**Design Rationale**: Shared infrastructure reduces code duplication, ensures consistent behavior, and simplifies maintenance across all ICTServe modules (Req 7.6).

### OpenAPI/Swagger Documentation (Req 7.4)

**Documentation URL**: `/api/documentation`

**Specification Format**: OpenAPI 3.0

**Included Information**:

- All endpoints with request/response examples
- Authentication requirements (Laravel Sanctum integration)
- Rate limiting details
- Error codes dan messages dalam Bahasa Melayu sahaja (D15 v3.6.0)
- Versioning information
- Code examples (PHP, JavaScript, cURL)

**Auto-Generation**: Using `darkaonline/l5-swagger` package

**Design Rationale**: URL-based versioning provides clear version identification (Req 7.5). Backward compatibility for 2 major versions ensures smooth transitions for API consumers. OpenAPI documentation enables easy integration and testing (Req 7.4). API design follows ICTServe standards (D03 v3.6.0) with Laravel Sanctum authentication and consistent error handling.

### API Response Metadata (Req 7.7)

**Purpose**: Provide transparency and debugging information for AI-generated responses

**Metadata Structure**:

```json
{
    "success": true,
    "data": {
        "response": "AI-generated content here...",
        "sources": [
            { "type": "faq", "id": 123, "title": "How to reset password" },
            { "type": "document", "id": 456, "filename": "IT_Policy_2024.pdf" }
        ]
    },
    "metadata": {
        "model": "llama3.1",
        "processing_time_ms": 1250,
        "confidence_score": 0.87,
        "tokens_used": 450,
        "cache_hit": false,
        "embedding_similarity": 0.92,
        "rag_sources_count": 3,
        "conversation_turns": 2
    },
    "request_id": "uuid-here"
}
```

**Metadata Fields**:

- **model**: LLM model used for generation (e.g., "llama3.1", "llama3.1:8b-q4")
- **processing_time_ms**: Total processing time in milliseconds
- **confidence_score**: AI confidence in response accuracy (0.0-1.0)
- **tokens_used**: Number of tokens consumed in generation
- **cache_hit**: Whether response was served from cache
- **embedding_similarity**: Similarity score for RAG retrieval (0.0-1.0)
- **rag_sources_count**: Number of documents/FAQs used in context
- **conversation_turns**: Number of conversation turns in context
- **source_citations**: Array of source documents with IDs and titles

**Use Cases**:

- **Debugging**: Identify performance bottlenecks and low-confidence responses
- **Monitoring**: Track model performance and cache effectiveness
- **Transparency**: Show users which sources informed the AI response
- **Optimization**: Identify opportunities for caching and model tuning

**Design Rationale**: Metadata enables debugging, monitoring, and transparency for AI operations while maintaining user trust through source citations (Req 7.7). Integrates with Laravel Pulse (D00 v3.6.0) for performance monitoring and Laravel Telescope (superuser only) for detailed debugging.

## Deployment Architecture

### Production Environment

```text
┌─────────────────┐    ┌──────────────────┐    ┌─────────────────┐
│   Load Balancer │    │   Laravel App    │    │  Ollama Server  │
│  (nginx/HAProxy)│◄──►│   (PHP-FPM)      │◄──►│  (Systemd)      │
└─────────────────┘    └──────────────────┘    └─────────────────┘
                              │                         │
                              ▼                         ▼
                    ┌──────────────────┐    ┌─────────────────┐
                    │   Database       │    │  Model Storage  │
                    │   (MySQL)        │    │  (Local/NFS)    │
                    └──────────────────┘    └─────────────────┘
```

### Systemd Service Configuration

```ini
[Unit]
Description=Ollama LLM Server
After=network.target

[Service]
ExecStart=/usr/bin/ollama serve --host 127.0.0.1 --port 11434
Restart=always
User=ollama
Environment=OLLAMA_MODELS=/opt/ollama/models

[Install]
WantedBy=multi-user.target
```

### Resource Requirements

- **CPU**: 4+ cores (8+ recommended for production)
- **RAM**: 16GB minimum (32GB+ for larger models)
- **Storage**: 100GB+ for models and document storage
- **Network**: Internal-only communication (no external access)

This design ensures a robust, scalable, and compliant AI integration that meets all requirements while maintaining the highest standards of security, accessibility, and performance.

## Requirements Traceability Matrix

This section maps all requirements from requirements.md to specific design components, ensuring complete coverage.

### Requirement 1: FAQ Bot System

| Acceptance Criteria                        | Design Component                                           | Implementation Details                                                            |
| ------------------------------------------ | ---------------------------------------------------------- | --------------------------------------------------------------------------------- |
| 1.1: 5-second response with RAG            | RagService, OllamaClient, Caching Strategy                 | RAG pipeline with vector embeddings, Redis caching (1-hour TTL), quantized models |
| 1.2: Conversation context (30 min)         | RagService Conversation Manager, guest_conversations table | Session-based history storage, last 5 turns maintained, 30-minute expiry          |
| 1.3: Fallback responses (similarity < 0.3) | RagService Fallback Handler                                | Graceful degradation with ticket creation links, confidence thresholds            |
| 1.4: Bahasa Melayu sahaja (D15 v3.6.0)     | BilingualSupportService (dilumpuhkan), templat Bahasa Melayu | Tiada penukar bahasa, sentiasa return 'ms', mengikut D15 v3.6.0               |
| 1.5: Audit logging (7-year retention)      | message_logs table, PII sanitization                       | X-Request-ID traceability, sanitized inputs, 7-year retention policy              |
| 1.6: WCAG 2.2 AA compliance                | Filament accessibility features                            | 4.5:1 text contrast, keyboard navigation, ARIA attributes, screen reader support  |
| 1.7: Guest conversation claiming           | guest_conversations table, email matching                  | Email-based conversation transfer to authenticated accounts                       |

### Requirement 2: Document Analysis

| Acceptance Criteria                           | Design Component                         | Implementation Details                                                |
| --------------------------------------------- | ---------------------------------------- | --------------------------------------------------------------------- |
| 2.1: Document processing pipeline             | DocumentService, Laravel Queue           | spatie/pdf-to-text, phpoffice/phpword, chunking, embedding generation |
| 2.2: Vector embeddings with caching           | EmbeddingService, Redis cache            | Ollama embeddings, MySQL storage, 24-hour Redis TTL                   |
| 2.3: PII detection and sanitization           | PII regex patterns, sanitization logic   | IC numbers, phone numbers, emails redacted, audit logging             |
| 2.4: Error handling with retry                | Exponential backoff, email notifications | 3 attempts (1s, 2s, 4s), mesej ralat Bahasa Melayu sahaja (D15 v3.6.0), admin email alerts |
| 2.5: File format support (PDF/DOCX/TXT, 10MB) | DocumentService validation               | File type validation, size limits, accessible upload interface        |
| 2.6: Data lineage tracking (7-year)           | data_lineage table                       | Source, transformation, destination tracking with 7-year retention    |
| 2.7: Role-based document access               | Spatie Permission, DocumentPolicy        | Staff: own documents, Admin: all documents, Superuser: full access    |

### Requirement 3: Auto-Reply System

| Acceptance Criteria                      | Design Component                            | Implementation Details                                                    |
| ---------------------------------------- | ------------------------------------------- | ------------------------------------------------------------------------- |
| 3.1: Contextual draft generation         | Auto_Reply service, RAG pipeline            | Ticket/application history, user context, knowledge base integration      |
| 3.2: Template-based responses            | auto_reply_templates table                  | Dynamic content insertion, templat Bahasa Melayu sahaja (D15 v3.6.0), professional tone |
| 3.3: Approval workflow                   | auto_reply_drafts table, status transitions | Draft → pending_review → approved/rejected → sent workflow                |
| 3.4: Email notifications (60s)           | Laravel Queue, email notifications          | Admin/superuser notifications, approval/rejection actions, audit logging  |
| 3.5: WCAG 2.2 AA approval interface      | Filament admin panel                        | Keyboard navigation, ARIA attributes, screen reader compatibility         |
| 3.6: Email-based approval (7-day tokens) | approval_email_tokens table                 | Secure token-based links, one-click approval, HMAC signature verification |
| 3.7: WCAG-compliant email templates      | ICTServe email templates                    | MOTAC branding, compliant color palette, accessibility features           |

### Requirement 4: Audit and Compliance

| Acceptance Criteria                                   | Design Component                         | Implementation Details                                                     |
| ----------------------------------------------------- | ---------------------------------------- | -------------------------------------------------------------------------- |
| 4.1: Comprehensive audit logging                      | message_logs table, Laravel Auditing     | X-Request-ID, timestamp, user ID, operation type, sanitized input/output   |
| 4.2: PII sanitization in logs                         | Regex patterns, automated redaction      | IC numbers, phone numbers, emails redacted before storage                  |
| 4.3: Log retention (90-day operational, 7-year audit) | Scheduled cleanup jobs, archival storage | Operational logs archived after 90 days, audit logs retained 7 years       |
| 4.4: PDPA data subject rights                         | API endpoints, admin panel               | Access, correction, deletion rights with cascade delete on account removal |
| 4.5: Audit trail viewing interface                    | Filament MessageLogResource              | Filtering by operation type, date range, user, status with pagination      |
| 4.6: Immutable audit logs                             | Cryptographic hashing, chain of custody  | SHA-256 hashing, previous_hash linking, tamper detection                   |
| 4.7: Audit report generation                          | Report generation service                | CSV, PDF, Excel formats dengan struktur accessible dan Bahasa Melayu sahaja (D15 v3.6.0) |

### Requirement 5: Accessibility Compliance

| Acceptance Criteria                       | Design Component                         | Implementation Details                                                       |
| ----------------------------------------- | ---------------------------------------- | ---------------------------------------------------------------------------- |
| 5.1: WCAG 2.2 AA markup                   | Semantic HTML5, ARIA landmarks           | Header, nav, main, footer elements with proper role attributes               |
| 5.2: Full keyboard navigation             | Focus indicators, skip links             | 3-4px outline, 2px offset, 3:1 contrast ratio, focus trap for modals         |
| 5.3: Alternative text for visual content  | ARIA labels, screen reader announcements | Alt text, ARIA live regions for dynamic content updates                      |
| 5.4: Bahasa Melayu sahaja (D15 v3.6.0)    | BilingualSupportService (dilumpuhkan)    | Bahasa Melayu sahaja, tiada penukar bahasa, mengikut D15 v3.6.0              |
| 5.5: Color contrast compliance            | ICTServe compliant color palette         | 4.5:1 text contrast, 3:1 UI components, Primary #0056b3, Success #198754     |
| 5.6: Minimum touch targets (44×44px)      | Button and link sizing                   | All interactive elements meet mobile accessibility standards                 |
| 5.7: Accessible feedback for AI responses | Loading states, error messages           | Color-independent feedback, ARIA live regions, accessible color combinations |

### Requirement 6: Data Privacy and Security

| Acceptance Criteria                          | Design Component                        | Implementation Details                                                               |
| -------------------------------------------- | --------------------------------------- | ------------------------------------------------------------------------------------ |
| 6.1: Local LLM processing (localhost:11434)  | OllamaClient configuration              | No external API calls, all processing within MOTAC infrastructure                    |
| 6.2: Encryption (AES-256, TLS 1.3)           | Laravel encryption, HTTPS configuration | Data at rest encrypted, TLS 1.3 for data in transit                                  |
| 6.3: External connectivity detection         | Network monitoring, security alerts     | Block unauthorized transmissions, log security events, email alerts within 5 minutes |
| 6.4: Data residency (Malaysian jurisdiction) | MySQL and Redis hosting                 | All data stored within MOTAC infrastructure, no cross-border transfers               |
| 6.5: Data lineage tracking (7-year)          | data_lineage table                      | Source, transformation, destination tracking with 7-year retention                   |
| 6.6: Role-based access control (4 roles)     | Spatie Permission, policies             | Staff, Approver, Admin, Superuser roles with granular permissions                    |
| 6.7: Automated PII sanitization              | Regex patterns, detection logic         | IC numbers, phone numbers, emails automatically redacted before storage              |

### Requirement 7: RESTful API Integration

| Acceptance Criteria                          | Design Component                              | Implementation Details                                                           |
| -------------------------------------------- | --------------------------------------------- | -------------------------------------------------------------------------------- |
| 7.1: Standard JSON responses                 | API response format                           | Success status, data payload, error details, X-Request-ID for traceability       |
| 7.2: Authentication and rate limiting        | Laravel Sanctum, Redis rate limiter           | 60 requests/minute per user, 1000 requests/hour per IP, burst allowance of 10    |
| 7.3: Mesej ralat Bahasa Melayu sahaja        | Error response format                         | Bahasa Melayu sahaja (D15 v3.6.0), HTTP status codes                            |
| 7.4: OpenAPI 3.0 documentation               | darkaonline/l5-swagger                        | /api/documentation endpoint with code examples, authentication, rate limiting    |
| 7.5: URL-based versioning                    | API versioning strategy                       | /api/v1/, /api/v2/ with 2-version backward compatibility, 6-month sunset period  |
| 7.6: ICTServe API infrastructure integration | Shared authentication, rate limiting, logging | Unified API gateway, consistent error handling, shared middleware stack          |
| 7.7: AI response metadata                    | Response metadata structure                   | Model used, processing time, confidence score, source citations for transparency |

### Requirement 8: Performance and Optimization

| Acceptance Criteria                                 | Design Component                              | Implementation Details                                                           |
| --------------------------------------------------- | --------------------------------------------- | -------------------------------------------------------------------------------- |
| 8.1: 5-second response time (95th percentile)       | Performance optimization, caching             | P50 < 2s, P95 < 5s, P99 < 8s, Core Web Vitals compliance                         |
| 8.2: 95% uptime under normal load (100 users)       | Health check monitoring, graceful degradation | Failover recovery < 30 seconds, multi-tier degradation strategy                  |
| 8.3: Graceful degradation (CPU > 80%, Memory > 90%) | Multi-tier degradation strategy               | Tier 1-4 degradation levels, email notifications to admins                       |
| 8.4: Caching strategy                               | Redis cache with tagged keys                  | FAQ queries (1 hour), embeddings (24 hours), top 50 queries pre-warmed           |
| 8.5: Quantized models (< 16GB RAM)                  | Q4_K_M quantization                           | Model warm-up, keep-alive functionality, memory optimization                     |
| 8.6: Core Web Vitals compliance                     | Frontend optimization                         | LCP < 2.5s, FID < 100ms, CLS < 0.1, TTFB < 600ms, Lighthouse 90+                 |
| 8.7: Performance monitoring dashboard               | Filament performance dashboard                | Metrics every 60 seconds, response time, cache hit rate, uptime, failed requests |

## Design Validation

This design has been validated against all 8 requirements with 56 acceptance criteria. Each requirement is fully addressed through specific architectural components, database schemas, service implementations, and infrastructure configurations.

**Key Design Principles**:

1. **Modularity**: Clear separation of concerns with dedicated services for each feature
2. **Scalability**: Queue-based processing, caching strategies, and graceful degradation
3. **Security**: Local processing, encryption, PII sanitization, and immutable audit logs
4. **Accessibility**: WCAG 2.2 AA compliance across all interfaces
5. **Compliance**: PDPA 2010 adherence with data lineage tracking and retention policies
6. **Performance**: Optimized response times, caching, and quantized models
7. **Maintainability**: Standard Laravel patterns, comprehensive documentation, and API versioning

This design ensures a robust, scalable, and compliant AI integration that meets all requirements while maintaining the highest standards of security, accessibility, and performance.

---

## Isu Diketahui & Pembetulan (Known Issues & Fixes)

### Isu 1: FAQ Bot Sentiasa Mengembalikan Mesej Fallback (KRITIKAL - DISELESAIKAN)

**Tarikh Dikesan**: 12 Disember 2025  
**Status**: ✅ Diselesaikan  
**Keterukan**: Kritikal - Menjejaskan semua interaksi FAQ Bot dalam konteks web

#### Gejala

- FAQ Bot sentiasa mengembalikan mesej fallback: "Maaf, saya tidak dapat memberikan jawapan yang tepat..."
- Ollama dikonfigurasi dengan betul dan berfungsi dalam tinker
- Tiada mesej ralat yang jelas dalam log aplikasi

#### Punca Akar

Lajur `ip_address` dalam jadual `audits` adalah `varchar(45)` (direka untuk alamat IPv6), tetapi `HashedIpAddressResolver` mencipta alamat IP yang di-hash SHA-256 yang panjangnya 64 aksara untuk pematuhan PDPA.

**Ralat**: `SQLSTATE[22001]: String data, right truncated: 1406 Data too long for column 'ip_address'`

#### Mengapa Tinker Berfungsi

Dalam tinker, tiada konteks permintaan HTTP, jadi `request()->ip()` mengembalikan `null`, dan `HashedIpAddressResolver` mengembalikan `null` dan bukannya IP yang di-hash, mengelakkan isu saiz lajur.

#### Pembetulan

1. Cipta migrasi untuk mengubah lajur `ip_address` dari `varchar(45)` ke `varchar(64)`:

```php
// database/migrations/2025_12_12_132219_modify_audits_ip_address_column_length.php
Schema::table('audits', function (Blueprint $table) {
    $table->string('ip_address', 64)->nullable()->change();
});
```

1. Jalankan migrasi: `php artisan migrate`
2. Kosongkan cache: `php artisan optimize:clear`

#### Fail Berkaitan

- `database/migrations/2025_12_12_132219_modify_audits_ip_address_column_length.php` (dicipta)
- `app/Resolvers/HashedIpAddressResolver.php` (disemak - mencipta hash SHA-256 64-aksara)
- `app/Models/MessageLog.php` (disemak - menggunakan trait Auditable)
- `app/Services/RagService.php` (disemak - berfungsi dengan betul)
- `config/audit.php` (disemak - menggunakan HashedIpAddressResolver)

#### Keperluan Berkaitan

- D09 §4.6 - Dual Audit System requirements
- Keperluan 4.1, 4.6 - Audit logging dengan pematuhan PDPA

#### Pengajaran

1. **Pengesahan Saiz Lajur**: Apabila menggunakan resolver tersuai yang mengubah data (seperti hashing), pastikan lajur pangkalan data boleh menampung output yang diubah
2. **Ujian Konteks Web**: Sentiasa uji ciri dalam konteks web sebenar, bukan hanya dalam tinker, kerana tingkah laku mungkin berbeza
3. **Pengendalian Ralat Audit**: Kegagalan audit trail boleh menyebabkan kegagalan senyap dalam operasi utama - pertimbangkan untuk menambah logging yang lebih baik untuk ralat audit
