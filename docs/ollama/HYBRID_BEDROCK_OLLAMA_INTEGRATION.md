# Hybrid Bedrock-Ollama Integration Guide

## ICTServe v3.6.1 - Cloud Hybrid AI Architecture

**Document ID**: D18_HYBRID_AI_INTEGRATION  
**Version**: 1.3.0  
**Last Updated**: 2025-12-14  
**Author**: Pasukan Pembangunan BPM MOTAC  
**Status**: Aktif - Sedia untuk Pelaksanaan  
**Klasifikasi**: Terhad - Dalaman BPM MOTAC  
**Standard Rujukan**: ISO/IEC/IEEE 42010, ISO/IEC/IEEE 15288, ISO/IEC/IEEE 12207, ISO/IEC/IEEE 29148, WCAG 2.2 AA, OWASP ASVS L2, PDPA 2010, MyGOV Digital Service Standards v2.1.0

> **Notis Penggunaan Dalaman**: Sistem ini adalah untuk kegunaan warga kerja MOTAC (staf dan pegawai gred) sahaja dan tidak dibuka kepada orang awam (internal use only).

---

## Rujukan Dokumen Berkaitan (Related Document References)

| Document | Description | Version |
|----------|-------------|---------|
| [D00_SYSTEM_OVERVIEW.md](../D00_SYSTEM_OVERVIEW.md) | System vision and governance | v3.6.0 |
| [D03_SOFTWARE_REQUIREMENTS_SPECIFICATION.md](../D03_SOFTWARE_REQUIREMENTS_SPECIFICATION.md) | Software requirements | v3.6.0 |
| [D04_SOFTWARE_DESIGN_DOCUMENT.md](../D04_SOFTWARE_DESIGN_DOCUMENT.md) | Architecture and design | v3.6.0 |
| [D09_DATABASE_DOCUMENTATION.md](../D09_DATABASE_DOCUMENTATION.md) | Database schema and dual audit | v3.6.0 |
| [D11_TECHNICAL_DESIGN_DOCUMENTATION.md](../D11_TECHNICAL_DESIGN_DOCUMENTATION.md) | Technical infrastructure | v3.6.0 |
| [D15_LANGUAGE_MS_EN.md](../D15_LANGUAGE_MS_EN.md) | Language localization (Bahasa Melayu sahaja) | v3.6.0 |
| [requirements.md](../../.kiro/specs/ollama-ai-integration/requirements.md) | AI Integration Requirements Specification | v3.6.1 |
| [design.md](../../.kiro/specs/ollama-ai-integration/design.md) | AI Integration Design Document | v3.6.1 |
| [tasks.md](../../.kiro/specs/ollama-ai-integration/tasks.md) | AI Integration Implementation Plan | v3.6.6 |
| [D12_UI_UX_DESIGN_GUIDE.md](../D12_UI_UX_DESIGN_GUIDE.md) | UI/UX guidelines | v3.6.0 |
| [D13_UI_UX_FRONTEND_FRAMEWORK.md](../D13_UI_UX_FRONTEND_FRAMEWORK.md) | Frontend framework | v3.6.0 |
| [D14_UI_UX_STYLE_GUIDE.md](../D14_UI_UX_STYLE_GUIDE.md) | Style guide | v3.6.0 |
| [D16_BROADCASTING_SETUP.md](../D16_BROADCASTING_SETUP.md) | WebSocket configuration (Laravel Reverb) | v3.6.0 |
| [D17_QUEUE_MANAGEMENT_HORIZON.md](../D17_QUEUE_MANAGEMENT_HORIZON.md) | Queue management (Laravel Horizon) | v3.6.0 |

---

## Sejarah Perubahan (Changelog)

| Versi | Tarikh | Perubahan | Penulis |
|-------|--------|-----------|---------|
| 1.3.0 | 2025-12-14 | **API & Deployment Integration**: Added comprehensive API Reference Summary (endpoints, authentication, rate limiting, error codes), Deployment Guide Summary (production setup, emergency procedures, monitoring), and updated references to detailed documentation. | Pasukan Pembangunan BPM |
| 1.2.0 | 2025-12-14 | **Comprehensive Spec Sync**: Integrated full requirements from requirements.md v3.6.1 (9 keperluan), design patterns from design.md v3.6.1, and implementation phases from tasks.md v3.6.6. Added glossary, deprecated components, service layer architecture, and complete acceptance criteria. | Pasukan Pembangunan BPM |
| 1.1.0 | 2025-12-14 | Added requirements traceability, implementation phases, spec references | Pasukan Pembangunan BPM |
| 1.0.0 | 2025-12-14 | Initial hybrid integration documentation | Pasukan Pembangunan BPM |

---

## Table of Contents

1. [Overview](#overview)
2. [Requirements Traceability](#requirements-traceability)
3. [Architecture](#architecture)
4. [Query Routing Strategy](#query-routing-strategy)
5. [Implementation Details](#implementation-details)
6. [Response Handling](#response-handling)
7. [User Experience](#user-experience)
8. [Cost Optimization](#cost-optimization)
9. [Configuration](#configuration)
10. [Testing](#testing)
11. [Troubleshooting](#troubleshooting)
12. [Implementation Phases](#implementation-phases)
13. [Glossary (Glosari)](#glossary-glosari)
14. [Deprecated Components (Komponen Dilumpuhkan)](#deprecated-components-komponen-dilumpuhkan---d15-v360)
15. [Service Layer Architecture](#service-layer-architecture-dari-designmd-v361)
16. [API Reference Summary](#api-reference-summary)
17. [Deployment Guide Summary](#deployment-guide-summary)
18. [Emergency Procedures Summary](#emergency-procedures-summary)

---

## Glossary (Glosari)

| Term | Definition |
|------|------------|
| **Ollama** | Pelayan Large Language Model tempatan yang menyediakan keupayaan AI tanpa kebergantungan API luaran |
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

---

## Overview

### Purpose

ICTServe implements a **True Hybrid AI Architecture** that seamlessly combines AWS Bedrock (Claude models) with local Ollama (RAG-based FAQ system) within a single unified chat interface. Users interact with one system that intelligently routes queries to the optimal AI backend.

### Key Principles

- **Single Interface**: Users interact with ONE chat system
- **Intelligent Routing**: System decides which AI to use based on query analysis
- **Hybrid Responses**: Combines Ollama (FAQ knowledge) + Bedrock (reasoning)
- **Seamless Experience**: Users don't need to know which AI is responding
- **Cost Optimization**: Free Ollama first, expensive Bedrock when needed

### Technology Stack

| Component | Technology | Purpose |
|-----------|------------|---------|
| **Bedrock** | AWS Bedrock Runtime | Claude Opus 4.5/Sonnet 4.5/Haiku 4.5 models |
| **Ollama** | Local LLM + RAG | FAQ-specific knowledge base |
| **Frontend** | Livewire 3.7 + Volt 1.10 | Reactive chat interface |
| **Backend** | Laravel 12.40.1, PHP 8.2.12 | API orchestration |
| **Admin Panel** | Filament 4.1.10 | AI management interface |
| **Real-time** | Laravel Reverb 1.6.2 | WebSocket notifications |
| **Queue** | Laravel Horizon | Background job processing |
| **Audit** | owen-it + spatie | Dual audit system (D09 v3.6.0) |

### Ciri Utama v3.6.1 (Key Features)

- **Cloud Hybrid AI Architecture**: Dual processing approach (Ollama + AWS Bedrock) dengan model routing pintar
- **Multi-Model Intelligence**: Claude 4.x models (Opus 4.5, Sonnet 4.5, Haiku 4.5) dengan task-specific routing
- **Streaming Responses**: Server-Sent Events (SSE) untuk pengalaman pengguna yang responsif (Future Enhancement)
- **Web-Augmented Responses**: DuckDuckGo integration untuk konteks terkini (COMPLETED)
- **Enhanced Conversation Management**: BedrockConversation model dengan save/load/delete (COMPLETED)
- **MCP Server Integration**: 3 tools untuk AI assistants (Amazon Q, Kiro) (COMPLETED)
- **Data Residency Compliance**: Klasifikasi data automatik untuk pemprosesan tempatan vs cloud
- **Bahasa Melayu Sahaja**: Antara muka AI tanpa penukar bahasa (D15 v3.6.0)

### Konteks Integrasi Kritikal (dari requirements.md v3.6.1)

Integrasi **Cloud Hybrid AI Architecture** mesti selaras dengan **True Hybrid Architecture** ICTServe v3.6.0:

1. **Akses Tetamu (Tanpa Log Masuk)**: FAQ Bot berkuasa AI dengan model routing pintar boleh diakses pada borang awam untuk sokongan pantas tanpa pengesahan
2. **Portal Authenticated (Log Masuk Diperlukan)**: Ciri AI dipertingkat untuk staf termasuk analisis dokumen dengan web-augmented responses, conversation management dengan memori jangka panjang, dan respons peribadi menggunakan multi-model intelligence
3. **Akses Admin (Panel Filament)**: Antara muka pengurusan AI hibrid untuk peranan admin dan superuser termasuk konfigurasi model (Ollama vs Bedrock), aliran kerja kelulusan auto-reply dengan streaming responses, pengurusan FAQ, dan ingestion dokumen dengan model selection berdasarkan jenis kandungan

#### Penekanan Utama

- **Komunikasi berasaskan e-mel** untuk notifikasi
- **Pematuhan WCAG 2.2 Level AA** untuk semua antara muka termasuk streaming responses
- **Sasaran prestasi Core Web Vitals** yang dipertingkat (LCP <2.5s, FID <100ms, CLS <0.1)
- **Antara muka Bahasa Melayu sahaja** (v3.6.0)
- **Jejak audit komprehensif** dengan pengekalan 7 tahun untuk pematuhan
- **Data residensi Malaysia** untuk pemprosesan cloud

---

## Requirements Traceability

### Keperluan AI Integration (dari requirements.md v3.6.1)

| Req ID | Keperluan | Status | Komponen |
|--------|-----------|--------|----------|
| **Req 1** | Sistem FAQ Bot AI (True Hybrid Architecture) | ✅ Completed | RagService, FaqBot |
| **Req 2** | Sistem Analisis Dokumen AI (Admin & Superuser) | ✅ Completed | DocumentService |
| **Req 3** | Sistem Auto-Reply AI (Aliran Kerja Kelulusan) | ✅ Completed | AutoReplyService |
| **Req 4** | Sistem Audit dan Pematuhan AI (Dual Audit System) | ✅ Completed | MessageLog, DataLineage |
| **Req 5** | Kebolehcapaian dan Pematuhan WCAG 2.2 AA | ✅ Completed | All UI components |
| **Req 6** | Privasi Data dan Keselamatan (Pemprosesan LLM Tempatan) | ✅ Completed | PIIDetectionService |
| **Req 7** | API RESTful dan Integrasi Sistem (Laravel Sanctum) | ✅ Completed | API Controllers |
| **Req 8** | Prestasi dan Pengoptimuman (Core Web Vitals) | ✅ Completed | Caching, Monitoring |
| **Req 9** | AWS Bedrock Integration (Cloud Hybrid) | ✅ Completed | BedrockService |

### Keperluan 9: AWS Bedrock Integration (Detailed - dari requirements.md v3.6.1)

**Cerita Pengguna:** Sebagai pentadbir sistem dan pembangun, saya mahu integrasi dengan AWS Bedrock sebagai alternatif cloud-based AI, supaya sistem mempunyai fleksibiliti untuk menggunakan model AI yang lebih berkuasa apabila diperlukan sambil mengekalkan pemprosesan tempatan untuk data sensitif.

#### Kriteria Penerimaan Penuh (Req 9)

| # | Kriteria | Status |
|---|----------|--------|
| 9.1 | **Multi-Model Intelligence**: Sistem MESTI menyokong pelbagai model Claude (Opus 4.5, Sonnet 4.5, Haiku 4.5) dengan pemilihan automatik berdasarkan jenis tugas dan kompleksiti | ✅ |
| 9.2 | **Model Routing**: Sistem MESTI menghalakan permintaan kepada model yang paling sesuai (FAQ simple → Haiku, FAQ complex → Sonnet, Document Analysis → Sonnet, Auto-Reply → Opus) | ✅ |
| 9.3 | **Streaming Responses**: Sistem MESTI menyokong streaming responses melalui SSE untuk respons panjang | 🔄 Future |
| 9.4 | **Web-Augmented Responses**: Sistem MESTI menyokong integrasi carian web (DuckDuckGo) untuk konteks terkini | ✅ |
| 9.5 | **Conversation Management**: Sistem MESTI menyediakan pengurusan perbualan dengan memori jangka panjang (30 hari authenticated, 24 jam guests) | ✅ |
| 9.6 | **Hybrid Configuration**: Sistem MESTI membolehkan pentadbir memilih model default dan menetapkan routing rules melalui Filament | ⏳ Pending |
| 9.7 | **Data Residency Compliance**: Sistem MESTI mengekalkan pematuhan residensi data Malaysia dengan klasifikasi data automatik | ⏳ Pending |
| 9.8 | **Performance Monitoring**: Sistem MESTI mengintegrasikan dengan Laravel Pulse untuk pemantauan prestasi masa nyata | ⏳ Pending |
| 9.9 | **Fallback Mechanism**: Sistem MESTI fallback kepada Ollama tempatan secara automatik jika AWS Bedrock tidak tersedia | ✅ |

### Kriteria Penerimaan Utama

**FAQ Bot (Req 1):**

- ✅ Respons dalam masa 5 saat (P95)
- ✅ Konteks perbualan 30 minit untuk authenticated users
- ✅ Fallback ke sokongan manusia jika skor < 0.3
- ✅ Bahasa Melayu sahaja (D15 v3.6.0)
- ✅ Dual Audit System logging (owen-it + spatie)

**AWS Bedrock (Req 9):**

- ✅ Multi-model intelligence (Claude Opus/Sonnet/Haiku)
- ✅ Model routing berdasarkan kompleksiti tugas
- ✅ Fallback ke Ollama jika Bedrock tidak tersedia
- ✅ Data residency compliance (Malaysia)
- ✅ Cost monitoring dan optimization

---

## Architecture

### System Architecture Diagram

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

### Component Responsibilities

| Component | Responsibility |
|-----------|----------------|
| **BedrockChat** | Main Livewire component, orchestrates hybrid responses |
| **RagService** | Ollama integration, FAQ knowledge retrieval |
| **BedrockService** | AWS Bedrock API wrapper |
| **EmbeddingService** | Vector embeddings for semantic search |
| **ModelRouter** | Smart model selection based on task complexity |
| **PIIDetectionService** | PII detection and sanitization (PDPA 2010) |
| **NetworkMonitoringService** | External connectivity detection (D11 v3.6.0) |

### Keperluan API Ollama Kritikal (dari design.md)

> **PENTING**: Keperluan berikut adalah kritikal untuk komunikasi yang betul dengan pelayan Ollama.

**1. Parameter `stream: false` (WAJIB)**

```php
// ✅ BETUL: Sertakan stream: false
$payload = [
    'model' => 'gemma3:1b',
    'prompt' => $userQuery,
    'stream' => false,  // KRITIKAL: Mesti false untuk respons JSON tunggal
];
```

**2. Endpoint Embeddings: `/api/embed` (BUKAN `/api/embeddings`)**

```php
// ✅ BETUL: Gunakan /api/embed
$response = Http::post($this->config['url'] . '/api/embed', $payload);
```

**3. Parameter Embeddings: `input` (BUKAN `prompt`)**

```php
// ✅ BETUL: Gunakan 'input' parameter
$payload = [
    'model' => 'nomic-embed-text',
    'input' => $text,  // Parameter yang betul untuk /api/embed
];
```

#### 4. Model Embedding Khusus

Gunakan model embedding khusus (`nomic-embed-text`) untuk penjanaan embedding, bukan model chat (`gemma3:1b`).

---

## Query Routing Strategy

### Query Classification

The system classifies incoming queries into three categories:

#### 1. FAQ-Specific Queries (`faq_specific`)

**Characteristics:**

- Contains ICTServe-specific keywords
- Asks about helpdesk, asset loans, system procedures
- Factual questions with definitive answers

**Keywords:**

```php
$faqKeywords = [
    'tiket', 'helpdesk', 'pinjaman', 'aset', 'status',
    'permohonan', 'sistem', 'ictserve', 'motac', 'bpm',
    'kelulusan', 'gred', 'pegawai', 'borang', 'sla'
];
```

**Routing:** → Ollama RAG Service

#### 2. Complex Reasoning Queries (`complex_reasoning`)

**Characteristics:**

- Requires analysis, comparison, or creative thinking
- General knowledge questions
- Strategic or advisory requests

**Keywords:**

```php
$complexKeywords = [
    'analisis', 'bandingkan', 'jelaskan', 'mengapa',
    'bagaimana jika', 'strategi', 'cadangan', 'pendapat',
    'kelebihan', 'kekurangan', 'implikasi'
];
```

**Routing:** → AWS Bedrock Claude

#### 3. Hybrid Queries (`hybrid`)

**Characteristics:**

- Contains both FAQ and complex keywords
- Requires factual knowledge + reasoning
- "Why" questions about ICTServe procedures

**Example:** "Mengapa sistem pinjaman aset perlu kelulusan Gred 41?"

**Routing:** → Ollama (facts) + Bedrock (reasoning)

### Query Analysis Algorithm

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

---

## Implementation Details

### Enhanced BedrockChat Component

**File:** `app/Livewire/BedrockChat.php`

```php
<?php

declare(strict_types=1);

namespace App\Livewire;

use App\Services\BedrockService;
use App\Services\RagService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Livewire\Component;

class BedrockChat extends Component
{
    public string $prompt = '';
    public string $model = 'sonnet';
    public array $messages = [];
    public bool $useInternet = false;
    public ?int $conversationId = null;
    public bool $showSidebar = false;
    public bool $sending = false;
    public ?string $context = null;
    public array $faqSuggestions = [];
    public bool $showAiSource = true;

    private RagService $ragService;
    private BedrockService $bedrockService;

    public function boot(): void
    {
        $this->ragService = app(RagService::class);
        $this->bedrockService = app(BedrockService::class);
    }

    public function send(): void
    {
        if (empty(trim($this->prompt))) {
            return;
        }

        $this->sending = true;

        try {
            // Add user message
            $this->messages[] = [
                'role' => 'user',
                'content' => $this->prompt,
            ];

            // Get hybrid response
            $response = $this->getHybridResponse($this->prompt);

            // Add assistant response with source attribution
            $this->messages[] = [
                'role' => 'assistant',
                'content' => $response['content'],
                'model' => $this->model,
                'source' => $response['source'],
                'sources' => $response['sources'] ?? [],
                'tokens' => $response['tokens'] ?? null,
            ];

            // Save conversation
            $this->saveConversation();

            // Clear prompt
            $this->prompt = '';
        } catch (\Exception $e) {
            Log::error('Hybrid chat error: ' . $e->getMessage());

            $this->messages[] = [
                'role' => 'assistant',
                'content' => 'Maaf, terdapat ralat. Sila cuba lagi.',
                'model' => $this->model,
                'source' => 'error',
                'error' => true,
            ];
        } finally {
            $this->sending = false;
        }
    }

    private function getHybridResponse(string $query): array
    {
        $queryType = $this->analyzeQuery($query);

        return match ($queryType) {
            'faq_specific' => $this->handleFaqQuery($query),
            'complex_reasoning' => $this->handleBedrockQuery($query),
            'hybrid' => $this->handleHybridQuery($query),
            default => $this->handleBedrockQuery($query)
        };
    }

    private function handleFaqQuery(string $query): array
    {
        try {
            $ollamaResponse = $this->ragService->processQuery(
                $query,
                session()->getId(),
                Auth::id(),
                Auth::user()?->email
            );

            if ($ollamaResponse['success']) {
                return [
                    'content' => $ollamaResponse['answer'],
                    'source' => 'ollama',
                    'sources' => $ollamaResponse['sources'] ?? []
                ];
            }
        } catch (\Exception $e) {
            Log::warning('Ollama FAQ query failed, falling back to Bedrock', [
                'error' => $e->getMessage(),
                'query' => $query
            ]);
        }

        // Fallback to Bedrock with FAQ context
        return $this->handleBedrockQuery($query, 'faq_fallback');
    }

    private function handleHybridQuery(string $query): array
    {
        try {
            // Step 1: Get FAQ knowledge from Ollama
            $ollamaResponse = $this->ragService->processQuery(
                $query,
                session()->getId()
            );

            if ($ollamaResponse['success']) {
                // Step 2: Enhance with Bedrock reasoning
                $enhancedPrompt = "Berdasarkan maklumat FAQ ini:\n\n" .
                                 $ollamaResponse['answer'] .
                                 "\n\nSoalan pengguna: " . $query .
                                 "\n\nBerikan respons yang komprehensif dalam Bahasa Melayu.";

                $bedrockResponse = $this->bedrockService->invoke(
                    $enhancedPrompt,
                    1500,
                    $this->getModelId()
                );

                if ($bedrockResponse['success']) {
                    return [
                        'content' => $bedrockResponse['content'],
                        'source' => 'hybrid',
                        'sources' => $ollamaResponse['sources'] ?? [],
                        'tokens' => $bedrockResponse['usage']['output_tokens'] ?? null
                    ];
                }
            }
        } catch (\Exception $e) {
            Log::warning('Hybrid query failed, falling back to Bedrock only', [
                'error' => $e->getMessage()
            ]);
        }

        return $this->handleBedrockQuery($query);
    }

    private function handleBedrockQuery(string $query, ?string $context = null): array
    {
        $prompt = $query;

        if ($context === 'faq_fallback') {
            $prompt = "Soalan berkaitan sistem ICTServe MOTAC: " . $query .
                     "\n\nJawab dalam Bahasa Melayu berdasarkan konteks helpdesk dan pinjaman aset ICT.";
        }

        $response = $this->bedrockService->invoke(
            $prompt,
            1000,
            $this->getModelId()
        );

        return [
            'content' => $response['content'] ?? 'Maaf, terdapat ralat. Sila cuba lagi.',
            'source' => 'bedrock',
            'sources' => [],
            'tokens' => $response['usage']['output_tokens'] ?? null
        ];
    }

    private function getModelId(): string
    {
        return match ($this->model) {
            'opus' => 'global.anthropic.claude-opus-4-5:v1',
            'sonnet' => 'us.anthropic.claude-3-5-sonnet-20241022-v2:0',
            'haiku' => 'us.anthropic.claude-3-5-haiku-20241022-v1:0',
            default => 'us.anthropic.claude-3-5-sonnet-20241022-v2:0',
        };
    }
}
```

---

## Response Handling

### Response Structure

All responses follow a unified structure regardless of source:

```php
[
    'content' => string,      // The AI response text
    'source' => string,       // 'ollama', 'bedrock', or 'hybrid'
    'sources' => array,       // FAQ sources (if applicable)
    'tokens' => ?int,         // Token usage (Bedrock only)
]
```

### Source Attribution

The UI displays subtle indicators showing which AI contributed:

| Source | Display | Icon |
|--------|---------|------|
| `ollama` | "Sumber: Pangkalan Data FAQ" | 📚 Book |
| `bedrock` | "Dijana oleh: Claude {Model}" | 🤖 CPU |
| `hybrid` | "Gabungan: FAQ + AI Analysis" | ✨ Sparkles |

### Blade Template Implementation

```blade
{{-- Response Source Indicator --}}
@if($showAiSource && isset($message['source']))
    <div class="mt-2 text-xs text-gray-500 border-t pt-2">
        @switch($message['source'])
            @case('ollama')
                <span class="inline-flex items-center">
                    <x-heroicon-o-book-open class="w-3 h-3 mr-1"/>
                    Sumber: Pangkalan Data FAQ
                </span>
                @if(!empty($message['sources']))
                    <ul class="mt-1 ml-4 list-disc">
                        @foreach($message['sources'] as $source)
                            <li>{{ $source['title'] ?? $source }}</li>
                        @endforeach
                    </ul>
                @endif
                @break
            @case('bedrock')
                <span class="inline-flex items-center">
                    <x-heroicon-o-cpu-chip class="w-3 h-3 mr-1"/>
                    Dijana oleh: Claude {{ ucfirst($message['model']) }}
                </span>
                @if(isset($message['tokens']))
                    <span class="ml-2 text-gray-400">
                        ({{ $message['tokens'] }} tokens)
                    </span>
                @endif
                @break
            @case('hybrid')
                <span class="inline-flex items-center">
                    <x-heroicon-o-sparkles class="w-3 h-3 mr-1"/>
                    Gabungan: FAQ + AI Analysis
                </span>
                @break
        @endswitch
    </div>
@endif
```

### Fallback Strategy

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

## User Experience

### Scenario Examples

#### Scenario 1: FAQ Query

```text
User: "Bagaimana cara menghantar tiket helpdesk?"

System Analysis:
├── Keywords detected: "tiket", "helpdesk"
├── FAQ Score: 2
├── Complex Score: 0
└── Classification: faq_specific

Routing: → Ollama RAG Service

Response:
┌─────────────────────────────────────────────────────────┐
│ Untuk menghantar tiket helpdesk:                        │
│                                                          │
│ 1. Lawati halaman penghantaran helpdesk                 │
│ 2. Isi maklumat hubungan dan butiran isu anda           │
│ 3. Muat naik fail berkaitan (pilihan)                   │
│ 4. Terima pengakuan dan hantar                          │
│                                                          │
│ Anda akan menerima e-mel pengesahan dengan nombor       │
│ tiket dalam masa 60 saat.                               │
│                                                          │
│ ─────────────────────────────────────────────────────── │
│ 📚 Sumber: Pangkalan Data FAQ                           │
│    • Panduan Pengguna ICTServe v3.6.0                   │
└─────────────────────────────────────────────────────────┘
```

#### Scenario 2: Complex Reasoning Query

```text
User: "Analisis kelebihan dan kekurangan sistem cloud computing"

System Analysis:
├── Keywords detected: "analisis", "kelebihan", "kekurangan"
├── FAQ Score: 0
├── Complex Score: 3
└── Classification: complex_reasoning

Routing: → AWS Bedrock Claude

Response:
┌─────────────────────────────────────────────────────────┐
│ ## Analisis Sistem Cloud Computing                      │
│                                                          │
│ ### Kelebihan:                                          │
│ 1. **Skalabiliti** - Mudah dikembangkan mengikut        │
│    keperluan                                             │
│ 2. **Kos Efektif** - Bayar mengikut penggunaan          │
│ 3. **Kebolehcapaian** - Akses dari mana-mana sahaja     │
│                                                          │
│ ### Kekurangan:                                         │
│ 1. **Kebergantungan Internet** - Memerlukan sambungan   │
│    yang stabil                                           │
│ 2. **Keselamatan Data** - Risiko kebocoran data         │
│ 3. **Kawalan Terhad** - Bergantung kepada penyedia      │
│                                                          │
│ ─────────────────────────────────────────────────────── │
│ 🤖 Dijana oleh: Claude Sonnet (847 tokens)              │
└─────────────────────────────────────────────────────────┘
```

#### Scenario 3: Hybrid Query

```text
User: "Mengapa sistem pinjaman aset perlu kelulusan Gred 41?"

System Analysis:
├── Keywords detected: "pinjaman", "aset", "kelulusan", "gred" (FAQ)
├── Keywords detected: "mengapa" (Complex)
├── FAQ Score: 4
├── Complex Score: 1
└── Classification: hybrid

Routing: → Ollama (facts) + Bedrock (reasoning)

Step 1 - Ollama RAG:
"Semua permohonan pinjaman mesti diluluskan oleh pegawai 
Gred 41 atau ke atas."

Step 2 - Bedrock Enhancement:
Enhanced prompt with FAQ context + reasoning request

Response:
┌─────────────────────────────────────────────────────────┐
│ ## Keperluan Kelulusan Gred 41 untuk Pinjaman Aset      │
│                                                          │
│ ### Fakta:                                              │
│ Semua permohonan pinjaman aset ICT mesti diluluskan     │
│ oleh pegawai Gred 41 atau ke atas.                      │
│                                                          │
│ ### Sebab-sebab:                                        │
│                                                          │
│ 1. **Akauntabiliti** - Pegawai kanan bertanggungjawab   │
│    terhadap aset kerajaan                               │
│                                                          │
│ 2. **Kawalan Dalaman** - Memastikan penggunaan aset     │
│    mengikut prosedur yang ditetapkan                    │
│                                                          │
│ 3. **Pematuhan Pekeliling** - Mengikut Pekeliling       │
│    Perbendaharaan berkaitan pengurusan aset             │
│                                                          │
│ 4. **Pengurusan Risiko** - Mengurangkan risiko          │
│    kehilangan atau kerosakan aset                       │
│                                                          │
│ ─────────────────────────────────────────────────────── │
│ ✨ Gabungan: FAQ + AI Analysis                          │
│    • Sumber: Panduan Pinjaman Aset ICT                  │
└─────────────────────────────────────────────────────────┘
```

### UI Components

#### Model Selection

```blade
<select wire:model.live="model" 
        class="rounded-md border-gray-300 focus:ring-primary-500">
    <option value="haiku">Haiku 4.5 (Pantas)</option>
    <option value="sonnet">Sonnet 4.5 (Seimbang)</option>
    <option value="opus">Opus 4.5 (Berkuasa)</option>
</select>
```

#### FAQ Suggestions (Context-Aware)

```blade
@if($context === 'faq' && !empty($faqSuggestions))
    <div class="mb-4 p-4 bg-blue-50 rounded-lg">
        <h4 class="text-sm font-medium text-blue-800 mb-2">
            Soalan Lazim:
        </h4>
        <div class="flex flex-wrap gap-2">
            @foreach($faqSuggestions as $suggestion)
                <button wire:click="useFaqSuggestion('{{ $suggestion }}')"
                        class="text-xs px-3 py-1 bg-white border border-blue-200 
                               rounded-full hover:bg-blue-100 transition-colors">
                    {{ $suggestion }}
                </button>
            @endforeach
        </div>
    </div>
@endif
```

---

## Cost Optimization

### Pricing Comparison

| AI System | Cost | Speed | Use Case |
|-----------|------|-------|----------|
| **Ollama (Local)** | Free | Fast | FAQ queries, knowledge base |
| **Bedrock Haiku** | $0.25/1M tokens | Fast | Simple reasoning |
| **Bedrock Sonnet** | $3/1M tokens | Medium | Balanced tasks |
| **Bedrock Opus** | $15/1M tokens | Slow | Complex analysis |

### Optimization Strategy

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

### Expected Cost Savings

| Query Type | Without Hybrid | With Hybrid | Savings |
|------------|----------------|-------------|---------|
| FAQ (70%) | $3/1M tokens | $0 | 100% |
| Hybrid (20%) | $3/1M tokens | $0.25/1M | 92% |
| Complex (10%) | $3/1M tokens | $3/1M | 0% |
| **Average** | **$3/1M** | **$0.55/1M** | **82%** |

---

## Configuration

### Environment Variables

```env
# AWS Bedrock Configuration
AWS_ACCESS_KEY_ID=your_access_key
AWS_SECRET_ACCESS_KEY=your_secret_key
AWS_BEDROCK_REGION=us-east-1
AWS_BEDROCK_MODEL_ID=us.anthropic.claude-3-5-sonnet-20241022-v2:0

# Ollama Configuration
OLLAMA_HOST=http://localhost:11434
OLLAMA_MODEL=llama3.1
OLLAMA_EMBEDDING_MODEL=nomic-embed-text

# Hybrid Configuration
HYBRID_AI_ENABLED=true
HYBRID_FAQ_THRESHOLD=0.3
HYBRID_FALLBACK_ENABLED=true
```

### Config File

**File:** `config/hybrid_ai.php`

```php
<?php

return [
    'enabled' => env('HYBRID_AI_ENABLED', true),

    'ollama' => [
        'host' => env('OLLAMA_HOST', 'http://localhost:11434'),
        'model' => env('OLLAMA_MODEL', 'llama3.1'),
        'embedding_model' => env('OLLAMA_EMBEDDING_MODEL', 'nomic-embed-text'),
        'timeout' => 30,
    ],

    'bedrock' => [
        'region' => env('AWS_BEDROCK_REGION', 'us-east-1'),
        'default_model' => env('AWS_BEDROCK_MODEL_ID'),
        'models' => [
            'opus' => 'global.anthropic.claude-opus-4-5:v1',
            'sonnet' => 'us.anthropic.claude-3-5-sonnet-20241022-v2:0',
            'haiku' => 'us.anthropic.claude-3-5-haiku-20241022-v1:0',
        ],
    ],

    'routing' => [
        'faq_keywords' => [
            'tiket', 'helpdesk', 'pinjaman', 'aset', 'status',
            'permohonan', 'sistem', 'ictserve', 'motac', 'bpm',
            'kelulusan', 'gred', 'pegawai', 'borang', 'sla'
        ],
        'complex_keywords' => [
            'analisis', 'bandingkan', 'jelaskan', 'mengapa',
            'bagaimana jika', 'strategi', 'cadangan', 'pendapat',
            'kelebihan', 'kekurangan', 'implikasi'
        ],
        'faq_threshold' => env('HYBRID_FAQ_THRESHOLD', 0.3),
    ],

    'fallback' => [
        'enabled' => env('HYBRID_FALLBACK_ENABLED', true),
        'default_model' => 'sonnet',
    ],
];
```

---

## Testing

### Unit Tests

**File:** `tests/Unit/Services/HybridAiServiceTest.php`

```php
<?php

declare(strict_types=1);

namespace Tests\Unit\Services;

use App\Services\BedrockService;
use App\Services\RagService;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\DataProvider;
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
                'content' => 'Enhanced response',
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

### Feature Tests

**File:** `tests/Feature/Livewire/BedrockChatHybridTest.php`

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
                'success' => true,
                'content' => 'Enhanced analysis',
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

### Running Tests

```bash
# Run all hybrid AI tests
php artisan test --filter=Hybrid

# Run specific test file
php artisan test tests/Feature/Livewire/BedrockChatHybridTest.php

# Run with coverage
php artisan test --filter=Hybrid --coverage --min=80
```

---

## Troubleshooting

### Common Issues

#### 1. Ollama Connection Failed

**Symptoms:**

- FAQ queries always fall back to Bedrock
- Error: "Connection refused to localhost:11434"

**Solutions:**

```bash
# Check if Ollama is running
curl http://localhost:11434/api/tags

# Start Ollama service
ollama serve

# Verify model is available
ollama list
```

#### 2. Bedrock Authentication Error

**Symptoms:**

- Complex queries fail with 403 error
- Error: "UnauthorizedException"

**Solutions:**

```bash
# Verify AWS credentials
aws sts get-caller-identity

# Check Bedrock model access
aws bedrock list-foundation-models --region us-east-1

# Ensure model is enabled in AWS Console
```

#### 3. Hybrid Queries Not Working

**Symptoms:**

- Queries classified as hybrid but only one AI responds
- Missing source attribution

**Solutions:**

```php
// Check configuration
config('hybrid_ai.enabled'); // Should be true

// Verify both services are available
app(RagService::class)->healthCheck();
app(BedrockService::class)->healthCheck();
```

#### 4. Slow Response Times

**Symptoms:**

- Hybrid queries take >10 seconds
- Timeout errors

**Solutions:**

```php
// Reduce token limits for hybrid queries
$bedrockResponse = $this->bedrockService->invoke(
    $enhancedPrompt,
    1000,  // Reduce from 1500
    $this->getModelId()
);

// Use Haiku for hybrid enhancement
'haiku' => 'us.anthropic.claude-3-5-haiku-20241022-v1:0'
```

### Logging

Enable detailed logging for debugging:

```php
// In BedrockChat.php
Log::channel('hybrid_ai')->info('Query analysis', [
    'query' => $query,
    'type' => $queryType,
    'faq_score' => $faqScore,
    'complex_score' => $complexScore,
]);

Log::channel('hybrid_ai')->info('Response generated', [
    'source' => $response['source'],
    'tokens' => $response['tokens'] ?? null,
    'duration_ms' => $duration,
]);
```

### Health Check Endpoint

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

## Implementation Phases

### Ringkasan Fasa Pelaksanaan (dari tasks.md v3.6.6)

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

### Fasa 13: Cloud Hybrid AI Integration (Detail)

**Tasks Completed:**

| Task | Description | Status |
|------|-------------|--------|
| 13.1 | BedrockService Implementation | ✅ Completed |
| 13.2 | Model Router (Smart Routing) | ✅ Completed |
| 13.3 | Streaming Response Service | 🔄 Future Enhancement |
| 13.4 | Web Search Integration (DuckDuckGo) | ✅ Completed |
| 13.5 | Conversation Management | ✅ Completed |
| 13.6 | Hybrid Configuration | ✅ Completed |
| 13.7 | Data Residency Compliance | ✅ Completed |
| 13.8 | Performance Monitoring | ✅ Completed |
| 13.9 | MCP Server Integration | ✅ Completed |
| 13.10 | Web Interface (BedrockChat) | ✅ Completed |
| 13.11 | Testing & Validation | ✅ Completed |
| 13.12 | Troubleshooting & Error Handling | ✅ Completed |

### Model Selection Strategy (dari design.md v3.6.1)

| Model | Use Case | Speed | Cost | Recommendation |
|-------|----------|-------|------|----------------|
| **Opus 4.5** | Complex reasoning, analysis, formal responses | Slow | High | Complex queries only |
| **Sonnet 4.5** | Balanced performance, document analysis | Medium | Medium | Default for most tasks |
| **Haiku 4.5** | Quick responses, simple FAQ queries | Fast | Low | FAQ fallback, hybrid enhancement |

### Model Routing Logic

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
}
```

---

## References

### Related Documentation (D00-D17 v3.6.0)

| Document | Description | Version |
|----------|-------------|---------|
| [D00_SYSTEM_OVERVIEW.md](../D00_SYSTEM_OVERVIEW.md) | True Hybrid Architecture, System Governance | v3.6.0 |
| [D03_SOFTWARE_REQUIREMENTS_SPECIFICATION.md](../D03_SOFTWARE_REQUIREMENTS_SPECIFICATION.md) | 38+ SRS Requirements including AI | v3.6.0 |
| [D04_SOFTWARE_DESIGN_DOCUMENT.md](../D04_SOFTWARE_DESIGN_DOCUMENT.md) | Architecture and Design Patterns | v3.6.0 |
| [D09_DATABASE_DOCUMENTATION.md](../D09_DATABASE_DOCUMENTATION.md) | Dual Audit System (owen-it + spatie) | v3.6.0 |
| [D11_TECHNICAL_DESIGN_DOCUMENTATION.md](../D11_TECHNICAL_DESIGN_DOCUMENTATION.md) | Technical Infrastructure | v3.6.0 |
| [D12_UI_UX_DESIGN_GUIDE.md](../D12_UI_UX_DESIGN_GUIDE.md) | UI/UX Guidelines | v3.6.0 |
| [D13_UI_UX_FRONTEND_FRAMEWORK.md](../D13_UI_UX_FRONTEND_FRAMEWORK.md) | Frontend Framework (Livewire/Volt) | v3.6.0 |
| [D14_UI_UX_STYLE_GUIDE.md](../D14_UI_UX_STYLE_GUIDE.md) | Style Guide (MyDS v2025.2) | v3.6.0 |
| [D15_LANGUAGE_MS_EN.md](../D15_LANGUAGE_MS_EN.md) | Bahasa Melayu sahaja Guidelines | v3.6.0 |
| [D16_BROADCASTING_SETUP.md](../D16_BROADCASTING_SETUP.md) | Laravel Reverb WebSocket | v3.6.0 |
| [D17_QUEUE_MANAGEMENT_HORIZON.md](../D17_QUEUE_MANAGEMENT_HORIZON.md) | Laravel Horizon Queue Management | v3.6.0 |

### AWS Bedrock Documentation Suite

| Document | Description |
|----------|-------------|
| [README.md](../aws_bedrock/README.md) | Overview and Architecture |
| [IMPLEMENTATION.md](../aws_bedrock/IMPLEMENTATION.md) | Implementation Details and Code Patterns |
| [API_REFERENCE.md](../aws_bedrock/API_REFERENCE.md) | BedrockService Methods and Usage |
| [MCP_SERVER.md](../aws_bedrock/MCP_SERVER.md) | Model Context Protocol Integration |
| [WEB_INTERFACE.md](../aws_bedrock/WEB_INTERFACE.md) | Livewire BedrockChat Component |
| [TROUBLESHOOTING.md](../aws_bedrock/TROUBLESHOOTING.md) | Common Errors and Fixes |
| [SETUP.md](../aws_bedrock/SETUP.md) | Installation and Configuration |

### Ollama Documentation

- [Ollama API Documentation](./api/ollama-ai-api-documentation.md) - Ollama API Reference
- [Ollama Deployment Guide](./deployment/ollama-ai-deployment-guide.md) - Deployment Instructions

### Kiro Spec Files (Source of Truth)

| File | Description | Version |
|------|-------------|---------|
| [requirements.md](../../.kiro/specs/ollama-ai-integration/requirements.md) | 9 Keperluan AI Integration (Req 1-9) | v3.6.1 |
| [design.md](../../.kiro/specs/ollama-ai-integration/design.md) | Cloud Hybrid Architecture Design | v3.6.1 |
| [tasks.md](../../.kiro/specs/ollama-ai-integration/tasks.md) | 15 Fasa Implementation Plan | v3.6.6 |

### External Resources

- [AWS Bedrock Documentation](https://docs.aws.amazon.com/bedrock/) - Official AWS Bedrock Docs
- [Ollama Documentation](https://ollama.ai/docs) - Official Ollama Docs
- [Livewire 3 Documentation](https://livewire.laravel.com/docs) - Livewire v3.7 Docs
- [Laravel 12 Documentation](https://laravel.com/docs/12.x) - Laravel v12.40.1 Docs
- [Filament 4 Documentation](https://filamentphp.com/docs) - Filament v4.1.10 Docs
- [Tailwind CSS 4 Documentation](https://tailwindcss.com/docs) - Tailwind v4.1.17 Docs

---

## Detailed Requirements from Spec Files

### Keperluan 9: AWS Bedrock Integration (dari requirements.md v3.6.1)

**Cerita Pengguna:** Sebagai pentadbir sistem dan pembangun, saya mahu integrasi dengan AWS Bedrock sebagai alternatif cloud-based AI, supaya sistem mempunyai fleksibiliti untuk menggunakan model AI yang lebih berkuasa apabila diperlukan sambil mengekalkan pemprosesan tempatan untuk data sensitif.

#### Kriteria Penerimaan Penuh

1. **Multi-Model Intelligence**: APABILA permintaan AI diproses, sistem MESTI menyokong pelbagai model Claude (Opus 4.5, Sonnet 4.5, Haiku 4.5) dengan pemilihan automatik berdasarkan jenis tugas dan kompleksiti
2. **Model Routing**: SEMASA memproses permintaan, sistem MESTI menghalakan permintaan kepada model yang paling sesuai berdasarkan:
   - FAQ simple (< 50 words) → Haiku (fastest, lowest cost)
   - FAQ complex (> 50 words, technical terms) → Sonnet (balanced)
   - Document Analysis → Sonnet (accuracy)
   - Auto-Reply generation → Opus (best language quality)
3. **Streaming Responses**: JIKA respons panjang dijana, sistem MESTI menyokong streaming responses melalui Server-Sent Events (SSE) untuk pengalaman pengguna yang lebih responsif (Future Enhancement)
4. **Web-Augmented Responses**: DI MANA maklumat terkini diperlukan, sistem MESTI menyokong integrasi carian web (DuckDuckGo) untuk memperkaya respons AI dengan konteks terkini
5. **Conversation Management**: Sistem MESTI menyediakan pengurusan perbualan yang dipertingkat dengan memori jangka panjang (30 hari untuk authenticated users, 24 jam untuk guests)
6. **Hybrid Configuration**: Sistem MESTI membolehkan pentadbir memilih model default, menetapkan had kos, dan mengkonfigurasi routing rules melalui panel admin Filament
7. **Data Residency Compliance**: Sistem MESTI mengekalkan pematuhan residensi data Malaysia dengan klasifikasi data automatik (public/internal/confidential/restricted)
8. **Performance Monitoring**: Sistem MESTI mengintegrasikan dengan Laravel Pulse untuk pemantauan prestasi masa nyata termasuk metrik Bedrock (response time, token usage, cost)
9. **Fallback Mechanism**: JIKA AWS Bedrock tidak tersedia, sistem MESTI fallback kepada Ollama tempatan secara automatik tanpa gangguan perkhidmatan

### Model Rate Limits (dari config/bedrock.php)

| Model | Requests/Min | Tokens/Min | Use Case |
|-------|--------------|------------|----------|
| **Opus 4.5** | 10 | 20,000 | Complex reasoning, formal responses |
| **Sonnet 4.5** | 20 | 40,000 | Balanced performance, document analysis |
| **Haiku 4.5** | 50 | 100,000 | Quick responses, simple FAQ queries |

### Inference Profile Requirements (CRITICAL)

> **PENTING**: Direct model IDs tidak berfungsi dengan on-demand throughput. Gunakan inference profile format:

```env
# ❌ WRONG - Direct model ID
AWS_BEDROCK_MODEL_ID=anthropic.claude-opus-4-5-20251101-v1:0

# ✅ CORRECT - Global inference profile (Opus 4.5)
AWS_BEDROCK_MODEL_ID=global.anthropic.claude-opus-4-5-20251101-v1:0

# ✅ CORRECT - US inference profile (Sonnet/Haiku)
AWS_BEDROCK_MODEL_ID=us.anthropic.claude-haiku-4-5-20251001-v1:0
```

---

## Detailed Design Patterns (dari design.md v3.6.1)

### BedrockClient Service Architecture

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

### Model Router Implementation

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

### Streaming Response Service Pattern

```php
class StreamingResponseService
{
    public function streamBedrockResponse(string $modelId, array $payload): Generator
    {
        $stream = $this->bedrockClient->invokeModelWithStreaming($modelId, $payload);
        
        foreach ($stream as $chunk) {
            $processedChunk = $this->processStreamChunk($chunk);
            
            if ($processedChunk) {
                yield [
                    'type' => 'content',
                    'data' => $processedChunk,
                    'timestamp' => now()->toISOString(),
                ];
            }
        }
        
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

### Data Classification Service

```php
class DataClassificationService
{
    private array $classificationRules = [
        'public' => 'allow_cloud',           // FAQ responses, general queries
        'internal' => 'local_only',          // Staff data, internal documents
        'confidential' => 'local_only',      // Personal data, sensitive info
        'restricted' => 'local_only',        // Classified government info
    ];
    
    public function classifyData(string $content): string
    {
        // Automatic classification based on content analysis
        if ($this->containsPII($content)) {
            return 'confidential';
        }
        
        if ($this->containsInternalTerms($content)) {
            return 'internal';
        }
        
        return 'public';
    }
    
    public function canProcessInCloud(string $classification): bool
    {
        return $this->classificationRules[$classification] === 'allow_cloud';
    }
}
```

---

## Complete Implementation Phases (dari tasks.md v3.6.6)

### Phase Summary Table

| Fasa | Nama | Status | Tugas | Komponen Utama |
|------|------|--------|-------|----------------|
| 1 | Asas & Infrastruktur | ✅ Selesai | 6 | OllamaClient, config/ollama.php |
| 2 | Skema Pangkalan Data & Model | ✅ Selesai | 5 | Faq, Document, MessageLog models |
| 3 | Core AI Services | ✅ Selesai | 4 | RagService, DocumentService, EmbeddingService |
| 4 | Background Jobs & Queue | ✅ Selesai | 4 | DocumentIngestJob, EmbeddingJob |
| 5 | API Endpoints & Controllers | ✅ Selesai | 5 | FaqController, DocumentController |
| 6 | Filament Admin Interface | ✅ Selesai | 5 | FaqResource, DocumentResource |
| 7 | Security & Compliance | ✅ Selesai | 6 | PIIDetectionService, Policies |
| 8 | Caching & Performance | ✅ Selesai | 4 | Redis caching, Laravel Pulse |
| 9 | Accessibility & i18n | ✅ Selesai | 4 | WCAG 2.2 AA, Bahasa Melayu sahaja |
| 10 | Testing & QA | 🔄 Partial | 6 | PHPUnit 12 tests |
| 11 | Real-time Notifications | ✅ Selesai | 3 | Laravel Reverb, Broadcasting events |
| 12 | Documentation & Deployment | 🔄 Partial | 4 | API docs, deployment guides |
| 13 | Cloud Hybrid AI (Bedrock) | ✅ Selesai | 12 | BedrockService, ModelRouter |
| 14 | Enhanced Database Schema | ⏳ Pending | 3 | Bedrock-specific tables |
| 15 | Enhanced Testing Strategy | ⏳ Pending | 3 | Hybrid system tests |

### Phase 13: Cloud Hybrid AI Integration (Detailed)

| Task | Description | Status | Files |
|------|-------------|--------|-------|
| 13.1 | BedrockService Implementation | ✅ Completed | `app/Services/BedrockService.php`, `config/bedrock.php` |
| 13.2 | Model Router (Smart Routing) | ✅ Completed | `app/Services/ModelRouter.php` |
| 13.3 | Streaming Response Service | 🔄 Future | SSE implementation pending |
| 13.4 | Web Search Integration | ✅ Completed | DuckDuckGo in `BedrockChat.php` |
| 13.5 | Conversation Management | ✅ Completed | `BedrockConversation` model |
| 13.6 | Hybrid Configuration | ⏳ Pending | Filament admin interface |
| 13.7 | Data Residency Compliance | ⏳ Pending | `DataClassificationService` |
| 13.8 | Performance Monitoring | ⏳ Pending | Laravel Pulse integration |
| 13.9 | MCP Server Integration | ✅ Completed | `mcp-servers/bedrock-server.js` |
| 13.10 | Web Interface (BedrockChat) | ✅ Completed | `app/Livewire/BedrockChat.php` |
| 13.11 | Testing & Validation | ⏳ Pending | PHPUnit tests |
| 13.12 | Troubleshooting & Error Handling | ⏳ Pending | Error patterns documented |

### Phase 14: Enhanced Database Schema (Pending)

New tables required for full Bedrock integration:

1. **bedrock_model_configs**: Model configuration and routing rules
2. **bedrock_usage_logs**: Cost and performance tracking
3. **conversation_contexts**: Enhanced conversation management
4. **web_search_logs**: Audit trail for external searches
5. **model_performance_metrics**: Performance analytics

### Phase 15: Enhanced Testing Strategy (Pending)

1. **Bedrock Integration Tests**: Mocked AWS SDK responses
2. **Performance & Load Tests**: 100 concurrent requests
3. **Compliance & Security Tests**: Data residency enforcement

---

## Common Errors and Fixes (dari TROUBLESHOOTING.md)

### Error 1: Model Access Denied

```text
Error: ValidationException: You don't have access to the model
Fix: Enable model in AWS Bedrock Console → Model access → Manage model access
Wait: 2-5 minutes for approval
```

### Error 2: Inference Profile Required (CRITICAL)

```text
Error: ValidationException: The provided model identifier is invalid
Cause: Direct model IDs don't work with on-demand throughput
Fix: Use inference profile format (global.* or us.*)
```

### Error 3: Livewire toJSON Error

```text
Error: Uncaught TypeError: component.toJSON is not a function
Causes: Multiple root elements in Blade view, complex objects in properties
Fixes:
1. Ensure single root div in Blade view
2. Pass collections to view, don't store in properties
3. Clear all caches: php artisan optimize:clear
```

### Error 4: Markdown Not Rendering

```bash
# Install dependencies
composer require league/commonmark
npm install @tailwindcss/typography
npm run build
```

### Error 5: DuckDuckGo Search Returns Empty

```php
// Use HTML endpoint with regex parsing
$url = 'https://html.duckduckgo.com/html/?q=' . urlencode($query);
preg_match_all('/<a class="result__snippet"[^>]*>(.*?)<\/a>/s', $html, $matches);
```

### Debugging Commands

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

### Log Locations

- **Laravel**: `storage/logs/laravel.log`
- **MCP Server**: `scripts/mcp-debug.log`
- **Browser Console**: F12 → Console tab
- **Network**: F12 → Network tab (check Livewire requests)

---

## D00-D17 v3.6.0 Compliance Requirements

All Cloud Hybrid AI features must maintain:

| Document | Requirement | Implementation |
|----------|-------------|----------------|
| **D00** | True Hybrid Architecture | Nullable user_id FK pattern, Self-Registration |
| **D09** | Dual Audit System | owen-it (compliance) + spatie (operations) |
| **D11** | Technical Infrastructure | Laravel Pulse + Telescope + Sanctum + Reverb |
| **D12-D14** | WCAG 2.2 AA | Accessible streaming UI, color contrast |
| **D15** | Bahasa Melayu sahaja | No language switcher, all AI responses in Malay |
| **D16** | Laravel Reverb | Real-time AI notifications via WebSocket |
| **D17** | Laravel Horizon | Queue management for AI jobs |

---

## Changelog

| Version | Date | Changes |
|---------|------|---------|
| 1.2.0 | 2025-12-14 | Added detailed requirements from specs, design patterns, complete implementation phases, troubleshooting guide, D00-D17 compliance requirements |
| 1.1.0 | 2025-12-14 | Added requirements traceability, implementation phases, spec references |
| 1.0.0 | 2025-12-14 | Initial hybrid integration documentation |

---

## Appendix

### A. Query Classification Examples

| Query | FAQ Score | Complex Score | Classification |
|-------|-----------|---------------|----------------|
| "Cara hantar tiket" | 1 | 0 | faq_specific |
| "Status pinjaman aset" | 2 | 0 | faq_specific |
| "Analisis sistem" | 0 | 1 | complex_reasoning |
| "Bandingkan pendekatan" | 0 | 1 | complex_reasoning |
| "Mengapa tiket perlu SLA?" | 1 | 1 | hybrid |
| "Jelaskan proses kelulusan" | 1 | 1 | hybrid |

### B. Model Selection Guide

| Use Case | Recommended Model | Reason |
|----------|-------------------|--------|
| FAQ queries | Ollama | Free, fast, accurate |
| Simple reasoning | Haiku | Fast, cheap |
| Balanced tasks | Sonnet | Good quality/cost ratio |
| Complex analysis | Opus | Best reasoning |
| Hybrid enhancement | Haiku | Cost-effective |

### C. Performance Benchmarks

| Query Type | Avg Response Time | Avg Tokens | Avg Cost |
|------------|-------------------|------------|----------|
| FAQ (Ollama) | 0.8s | N/A | $0 |
| Complex (Haiku) | 1.2s | 150 | $0.04 |
| Complex (Sonnet) | 2.5s | 300 | $0.90 |
| Complex (Opus) | 5.0s | 500 | $7.50 |
| Hybrid | 2.0s | 200 | $0.05 |

---

## Deprecated Components (Komponen Dilumpuhkan) - D15 v3.6.0

Mengikut D15 v3.6.0, komponen berikut telah dilumpuhkan sebagai sebahagian daripada peralihan ke antara muka Bahasa Melayu sahaja:

| Komponen | Status | Nota |
|----------|--------|------|
| `LanguageSwitcher` | **Dipadam** | Komponen Livewire untuk menukar bahasa |
| `BilingualSupportService` | Dilumpuhkan | Semua kaedah kini mengembalikan 'ms' sahaja |
| `SetLocale` middleware | Dilumpuhkan | Sentiasa menetapkan locale kepada 'ms' |
| `users.locale` column | Dilumpuhkan | Sentiasa mengembalikan 'ms' (kolum dikekalkan) |
| `ictserve_locale` cookie | **Dipadam** | Cookie dipadam pada login/logout |
| Fail terjemahan `lang/en/` | Dikekalkan | Untuk rujukan teknikal dan kemungkinan penggunaan masa depan |

**Implikasi untuk AI System:**

- Semua respons AI dalam Bahasa Melayu sahaja
- Tiada pengesanan bahasa automatik
- BilingualSupportService sentiasa return 'ms'
- Templat e-mel dan notifikasi dalam Bahasa Melayu sahaja

---

## Service Layer Architecture (dari design.md v3.6.1)

### Seni Bina Lapisan Perkhidmatan

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
│  ├── BedrockClient (AWS SDK wrapper + model routing)                       │
│  ├── RagService (RAG + conversation context, Bahasa Melayu responses)      │
│  ├── DocumentService (Ingest, Analysis, PII detection)                     │
│  ├── EmbeddingService (Vector operations + caching)                        │
│  ├── ModelRouter (Smart model selection based on task complexity)          │
│  ├── StreamingResponseService (SSE handler for long responses)             │
│  ├── WebSearchService (DuckDuckGo integration for web-augmented responses) │
│  ├── RegistrationService (Self-registration @motac.gov.my + verification)  │
│  ├── AuthenticationService (Flexible login email/username)                 │
│  ├── AccountLinkingService (Link guest submissions to accounts)            │
│  └── BilingualSupportService (Dilumpuhkan v3.6.0 - sentiasa return 'ms')   │
├─────────────────────────────────────────────────────────────────────────────┤
│  Models & Data Layer (True Hybrid Architecture)                            │
│  ├── User (Self-registration, nullable relationships, locale='ms')         │
│  ├── Faq, Document, DocumentChunk (with user_id nullable FK)               │
│  ├── Embedding, AutoReplyTemplate, AutoReplyDraft                          │
│  ├── MessageLog (Dual audit trail: owen-it + spatie)                       │
│  ├── BedrockConversation (Enhanced conversation management)                │
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

### OllamaClient Service Interface

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

### Keperluan API Ollama Kritikal (dari design.md)

> **PENTING**: Keperluan berikut adalah kritikal untuk komunikasi yang betul dengan pelayan Ollama.

**1. Parameter `stream: false` (WAJIB)**

```php
// ✅ BETUL: Sertakan stream: false
$payload = [
    'model' => 'gemma3:1b',
    'prompt' => $userQuery,
    'stream' => false,  // KRITIKAL: Mesti false untuk respons JSON tunggal
];
```

**2. Endpoint Embeddings: `/api/embed` (BUKAN `/api/embeddings`)**

```php
// ✅ BETUL: Gunakan /api/embed
$response = Http::post($this->config['url'] . '/api/embed', $payload);
```

**3. Parameter Embeddings: `input` (BUKAN `prompt`)**

```php
// ✅ BETUL: Gunakan 'input' parameter
$payload = [
    'model' => 'nomic-embed-text',
    'input' => $text,  // Parameter yang betul untuk /api/embed
];
```

#### 4. Model Embedding Khusus

Gunakan model embedding khusus (`nomic-embed-text`) untuk penjanaan embedding, bukan model chat (`gemma3:1b`).

---

## All 9 Requirements Summary (dari requirements.md v3.6.1)

### Keperluan 1: Sistem FAQ Bot AI (True Hybrid Architecture)

**Cerita Pengguna:** Sebagai ahli staf MOTAC yang mengakses ICTServe, saya mahu menanyakan sistem FAQ berkuasa AI melalui kedua-dua borang tetamu dan portal authenticated.

**Kriteria Penerimaan Utama:**

- Respons dalam masa 5 saat (P95) mematuhi Core Web Vitals
- Konteks perbualan 30 minit untuk authenticated users
- Fallback ke sokongan manusia jika skor < 0.3
- Bahasa Melayu sahaja (D15 v3.6.0)
- Dual Audit System logging (owen-it + spatie)
- WCAG 2.2 Level AA compliance
- Account Linking untuk guest-to-authenticated data transfer

### Keperluan 2: Sistem Analisis Dokumen AI (Admin & Superuser)

**Cerita Pengguna:** Sebagai admin atau superuser, saya mahu memuat naik dan menganalisis dokumen menggunakan AI melalui panel admin Filament.

**Kriteria Penerimaan Utama:**

- Sokongan PDF, DOCX, TXT (max 10MB)
- PII detection dan sanitization automatik
- Vector embeddings dengan caching Redis 24 jam
- Retry mechanism dengan exponential backoff (3 percubaan)
- Role-based access (staff: dokumen sendiri, admin: semua, superuser: penuh)

### Keperluan 3: Sistem Auto-Reply AI (Aliran Kerja Kelulusan)

**Cerita Pengguna:** Sebagai admin atau juruteknik, saya mahu draf balasan yang dijana AI untuk tiket helpdesk dan permohonan pinjaman aset.

**Kriteria Penerimaan Utama:**

- Template-based response generation
- Approval workflow (draft → pending_review → approved/rejected → sent)
- Email-based approval dengan signed URLs (7 hari validity)
- Notifikasi dalam 60 saat via e-mel dan Laravel Reverb

### Keperluan 4: Sistem Audit dan Pematuhan AI (Dual Audit System)

**Cerita Pengguna:** Sebagai pentadbir sistem dan pegawai pematuhan, saya mahu jejak audit komprehensif untuk semua operasi AI.

**Kriteria Penerimaan Utama:**

- X-Request-ID untuk kebolehkesanan
- PII sanitization sebelum logging
- Pengekalan 7 tahun untuk compliance
- PDPA 2010 data subject rights support
- Laravel Telescope untuk superuser sahaja

### Keperluan 5: Kebolehcapaian dan Pematuhan WCAG 2.2 AA

**Cerita Pengguna:** Sebagai pengguna dengan keperluan kebolehcapaian, saya mahu semua antara muka AI dapat diakses sepenuhnya.

**Kriteria Penerimaan Utama:**

- HTML5 semantik dengan ARIA landmarks
- Navigasi papan kekunci penuh dengan fokus visible
- Kontras warna minimum 4.5:1 (teks), 3:1 (UI)
- Sasaran sentuh minimum 44×44px
- Bahasa Melayu sahaja dengan `lang="ms"`

### Keperluan 6: Privasi Data dan Keselamatan (Pemprosesan LLM Tempatan)

**Cerita Pengguna:** Sebagai pegawai privasi data, saya mahu pemprosesan AI tempatan tanpa panggilan API luaran.

**Kriteria Penerimaan Utama:**

- Ollama pada localhost:11434 sahaja
- Enkripsi AES-256 (at rest) dan TLS 1.3 (in transit)
- Network monitoring untuk unauthorized connections
- Data residency dalam Malaysia
- RBAC dengan 4 peranan (staff, approver, admin, superuser)

### Keperluan 7: API RESTful dan Integrasi Sistem (Laravel Sanctum)

**Cerita Pengguna:** Sebagai integrator sistem, saya mahu API RESTful untuk perkhidmatan AI.

**Kriteria Penerimaan Utama:**

- JSON response dengan X-Request-ID
- Laravel Sanctum authentication dengan configurable abilities
- Rate limiting (60/min per user, 1000/hour per IP)
- OpenAPI 3.0/Swagger documentation
- URL-based versioning (/api/v1/, /api/v2/)

### Keperluan 8: Prestasi dan Pengoptimuman (Core Web Vitals)

**Cerita Pengguna:** Sebagai pemantau prestasi, saya mahu masa respons AI yang dioptimumkan.

**Kriteria Penerimaan Utama:**

- P50 < 2s, P95 < 5s, P99 < 8s
- 95% uptime dengan failover < 30s
- Graceful degradation (4 peringkat)
- Redis caching (FAQ: 1 jam, embeddings: 24 jam)
- Core Web Vitals (LCP <2.5s, FID <100ms, CLS <0.1)

### Keperluan 9: AWS Bedrock Integration (Cloud Hybrid)

**Cerita Pengguna:** Sebagai pentadbir sistem, saya mahu integrasi dengan AWS Bedrock sebagai alternatif cloud-based AI.

**Kriteria Penerimaan Utama:**

- Multi-model intelligence (Opus/Sonnet/Haiku)
- Smart model routing berdasarkan task complexity
- Web-augmented responses (DuckDuckGo)
- Conversation management (30 hari authenticated, 24 jam guests)
- Fallback ke Ollama jika Bedrock tidak tersedia
- Data residency compliance (Malaysia)

---

## D00-D17 v3.6.0 Compliance Matrix

| Document | Requirement | AI System Implementation |
|----------|-------------|--------------------------|
| **D00** | True Hybrid Architecture | Nullable user_id FK, Self-Registration, Account Linking |
| **D03** | 38+ SRS Requirements | 9 AI-specific requirements (Req 1-9) |
| **D04** | Architecture Patterns | Service Layer, RAG Pipeline, Model Router |
| **D09** | Dual Audit System | owen-it (compliance) + spatie (operations) |
| **D11** | Technical Infrastructure | Laravel Pulse + Telescope + Sanctum + Reverb |
| **D12-D14** | WCAG 2.2 AA | Accessible streaming UI, 4.5:1 contrast |
| **D15** | Bahasa Melayu sahaja | No language switcher, all AI responses in Malay |
| **D16** | Laravel Reverb | Real-time AI notifications via WebSocket |
| **D17** | Laravel Horizon | Queue management for AI jobs |

---

## Appendix: Configuration Files

### config/ollama.php (dari design.md)

```php
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

### config/bedrock.php (dari design.md)

```php
return [
    'enabled' => env('BEDROCK_ENABLED', false),
    'region' => env('AWS_DEFAULT_REGION', 'ap-southeast-1'),
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

---

## API Reference Summary

> **Dokumentasi Penuh**: Lihat [ollama-ai-api-documentation.md](./api/ollama-ai-api-documentation.md) untuk rujukan API lengkap.

### Base URL dan Versioning

```text
Production: https://ictserve.motac.gov.my/api/v1/ollama
Development: http://127.0.0.1:8000/api/v1/ollama
```

### Authentication (Laravel Sanctum)

Semua endpoint API memerlukan pengesahan menggunakan Laravel Sanctum token:

```bash
# Header Authentication
Authorization: Bearer {your-api-token}
Accept: application/json
Content-Type: application/json
```

**Token Generation:**

```php
// Generate token untuk pengguna
$token = $user->createToken('api-access', ['ollama:read', 'ollama:write'])->plainTextToken;
```

### API Endpoints Summary

| Endpoint | Method | Description | Auth Required |
|----------|--------|-------------|---------------|
| `/health` | GET | Health check status | No |
| `/faq/query` | POST | Query FAQ Bot | Yes |
| `/faq/suggestions` | GET | Get FAQ suggestions | Yes |
| `/documents/upload` | POST | Upload document for analysis | Yes (Admin) |
| `/documents/{id}/analyze` | POST | Analyze uploaded document | Yes (Admin) |
| `/auto-reply/generate` | POST | Generate auto-reply draft | Yes (Admin) |
| `/auto-reply/{id}/approve` | POST | Approve auto-reply | Yes (Admin) |
| `/conversations` | GET | List user conversations | Yes |
| `/conversations/{id}` | GET | Get conversation details | Yes |
| `/conversations/{id}` | DELETE | Delete conversation | Yes |

### Rate Limiting

| Scope | Limit | Window |
|-------|-------|--------|
| Per User | 60 requests | 1 minute |
| Per IP | 1000 requests | 1 hour |
| Burst | 10 requests | 10 seconds |

**Rate Limit Headers:**

```text
X-RateLimit-Limit: 60
X-RateLimit-Remaining: 55
X-RateLimit-Reset: 1702540800
```

### Request/Response Format

**FAQ Query Request:**

```json
{
  "query": "Bagaimana cara menghantar tiket helpdesk?",
  "session_id": "optional-session-id",
  "context": "faq"
}
```

**FAQ Query Response:**

```json
{
  "success": true,
  "data": {
    "answer": "Untuk menghantar tiket helpdesk...",
    "confidence": 0.85,
    "sources": ["FAQ Guide v3.6.0"],
    "session_id": "abc123",
    "processing_time_ms": 450
  },
  "meta": {
    "request_id": "req_abc123",
    "timestamp": "2025-12-14T10:30:00Z",
    "model": "llama3.1"
  }
}
```

### Error Codes

| Code | HTTP Status | Description | Resolution |
|------|-------------|-------------|------------|
| `VALIDATION_ERROR` | 400 | Invalid request parameters | Check request format |
| `UNAUTHORIZED` | 401 | Missing or invalid token | Provide valid Sanctum token |
| `FORBIDDEN` | 403 | Insufficient permissions | Check user role/abilities |
| `NOT_FOUND` | 404 | Resource not found | Verify resource ID |
| `RATE_LIMITED` | 429 | Too many requests | Wait and retry |
| `SERVICE_UNAVAILABLE` | 503 | Ollama server unavailable | Check Ollama status |
| `INTERNAL_ERROR` | 500 | Server error | Contact support |

**Error Response Format:**

```json
{
  "success": false,
  "error": {
    "code": "VALIDATION_ERROR",
    "message": "Query parameter is required",
    "details": {
      "field": "query",
      "rule": "required"
    }
  },
  "meta": {
    "request_id": "req_xyz789",
    "timestamp": "2025-12-14T10:30:00Z"
  }
}
```

### Code Examples

**PHP (Laravel):**

```php
use Illuminate\Support\Facades\Http;

$response = Http::withToken($apiToken)
    ->post('https://ictserve.motac.gov.my/api/v1/ollama/faq/query', [
        'query' => 'Bagaimana cara menghantar tiket?',
        'context' => 'faq'
    ]);

if ($response->successful()) {
    $answer = $response->json('data.answer');
}
```

**JavaScript (Fetch):**

```javascript
const response = await fetch('https://ictserve.motac.gov.my/api/v1/ollama/faq/query', {
    method: 'POST',
    headers: {
        'Authorization': `Bearer ${apiToken}`,
        'Content-Type': 'application/json',
        'Accept': 'application/json'
    },
    body: JSON.stringify({
        query: 'Bagaimana cara menghantar tiket?',
        context: 'faq'
    })
});

const data = await response.json();
console.log(data.data.answer);
```

**cURL:**

```bash
curl -X POST https://ictserve.motac.gov.my/api/v1/ollama/faq/query \
  -H "Authorization: Bearer YOUR_API_TOKEN" \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{"query": "Bagaimana cara menghantar tiket?", "context": "faq"}'
```

---

## Deployment Guide Summary

> **Dokumentasi Penuh**: Lihat [ollama-ai-deployment-guide.md](./deployment/ollama-ai-deployment-guide.md) untuk panduan deployment lengkap.

### System Requirements

#### Minimum Hardware

| Component | Minimum | Recommended |
|-----------|---------|-------------|
| CPU | 4 cores (Intel i5/AMD Ryzen 5) | 8 cores (Intel i7/AMD Ryzen 7) |
| RAM | 16GB | 32GB |
| Storage | 100GB SSD | 500GB NVMe SSD |
| Network | 100 Mbps | 1 Gbps |

#### Software Requirements

| Software | Version | Purpose |
|----------|---------|---------|
| PHP | 8.2.12+ | Laravel runtime |
| MySQL | 8.0+ | Database |
| Redis | 7.0+ | Cache, Queue, Sessions |
| Nginx/Apache | Latest | Web server |
| Ollama | Latest | Local LLM server |
| Node.js | 22+ | Asset compilation |

### Ollama Model Requirements

| Model | Size | RAM Required | Use Case |
|-------|------|--------------|----------|
| llama3.1:8b-instruct-q4_K_M | 4.7GB | 8GB | Production (recommended) |
| llama3.1:8b-instruct-fp16 | 16GB | 20GB | High quality |
| llama3.1:7b-instruct-q4_K_M | 4.1GB | 6GB | Development |

### Quick Deployment Steps

```bash
# 1. Install Ollama
curl -fsSL https://ollama.ai/install.sh | sh
sudo systemctl enable ollama && sudo systemctl start ollama
ollama pull llama3.1:8b-instruct-q4_K_M

# 2. Deploy Laravel Application
cd /var/www
git clone https://github.com/motac/ictserve.git ictserve-ai
cd ictserve-ai
composer install --no-dev --optimize-autoloader
npm ci && npm run build

# 3. Configure Environment
cp .env.production.example .env
php artisan key:generate
php artisan migrate --force

# 4. Cache Configuration
php artisan config:cache
php artisan route:cache
php artisan view:cache

# 5. Start Services
sudo systemctl restart php8.2-fpm nginx
sudo supervisorctl start ictserve-horizon ictserve-reverb
```

### Environment Configuration (.env.production)

```bash
# Application
APP_ENV=production
APP_DEBUG=false
APP_URL=https://ictserve.motac.gov.my

# Ollama AI
OLLAMA_MODEL=llama3.1:8b-instruct-q4_K_M
OLLAMA_URL=http://127.0.0.1:11434
OLLAMA_CONNECTION_TIMEOUT=300
OLLAMA_CACHE_ENABLED=true
OLLAMA_CACHE_TTL=3600

# AWS Bedrock (Cloud Hybrid)
BEDROCK_ENABLED=true
AWS_DEFAULT_REGION=ap-southeast-1
BEDROCK_DEFAULT_MODEL=us.anthropic.claude-3-5-sonnet-20241022-v2:0

# Cache & Queue
CACHE_DRIVER=redis
QUEUE_CONNECTION=redis
SESSION_DRIVER=redis

# Broadcasting (Laravel Reverb)
BROADCAST_DRIVER=reverb
REVERB_HOST=127.0.0.1
REVERB_PORT=6001

# Monitoring
PULSE_ENABLED=true
TELESCOPE_ENABLED=true
```

### Supervisor Configuration

**Laravel Horizon:**

```ini
[program:ictserve-horizon]
command=php /var/www/ictserve-ai/artisan horizon
user=www-data
autostart=true
autorestart=true
redirect_stderr=true
stdout_logfile=/var/www/ictserve-ai/storage/logs/horizon.log
```

**Laravel Reverb:**

```ini
[program:ictserve-reverb]
command=php /var/www/ictserve-ai/artisan reverb:start --host=127.0.0.1 --port=6001
user=www-data
autostart=true
autorestart=true
redirect_stderr=true
stdout_logfile=/var/www/ictserve-ai/storage/logs/reverb.log
```

### Health Check Endpoints

| Endpoint | Purpose | Expected Response |
|----------|---------|-------------------|
| `/api/v1/ollama/health` | API health | `{"status": "healthy"}` |
| `http://127.0.0.1:11434/api/tags` | Ollama status | Model list |
| `/admin/pulse` | Performance dashboard | Admin access |

### Monitoring Setup

**Laravel Pulse Dashboard:**

- URL: `https://ictserve.motac.gov.my/admin/pulse`
- Access: Admin dan Superuser sahaja
- Metrics: Response times, queue jobs, server health

**Laravel Telescope (Superuser Only):**

- URL: `https://ictserve.motac.gov.my/admin/telescope`
- Access: Superuser sahaja
- Features: Request debugging, query monitoring, job tracking

### Backup Strategy

```bash
# Daily Database Backup (2:00 AM)
0 2 * * * mysqldump -u backup_user ictserve_production | gzip > /backup/mysql_$(date +\%Y\%m\%d).sql.gz

# Weekly Application Backup (Sunday 3:00 AM)
0 3 * * 0 tar -czf /backup/app_$(date +\%Y\%m\%d).tar.gz /var/www/ictserve-ai

# Ollama Models Backup
tar -czf /backup/ollama_models.tar.gz /var/lib/ollama/models/
```

### Security Configuration

**Firewall Rules:**

```bash
sudo ufw allow 22/tcp    # SSH
sudo ufw allow 80/tcp    # HTTP
sudo ufw allow 443/tcp   # HTTPS
sudo ufw allow 6001/tcp  # Reverb (internal)
sudo ufw deny 11434/tcp  # Block external Ollama
sudo ufw enable
```

**SSL/TLS Requirements:**

- TLS 1.2 dan 1.3 sahaja
- HSTS enabled (max-age 1 tahun)
- SSL Labs rating A+ target

---

## Emergency Procedures Summary

> **Dokumentasi Penuh**: Lihat [emergency-procedures.md](./deployment/emergency-procedures.md) untuk prosedur kecemasan lengkap.

### Emergency Levels

| Level | Criteria | Response Time | Actions |
|-------|----------|---------------|---------|
| **Tahap 1** | Response time > 5s, CPU 80-90% | 30 min | Monitor, clear cache |
| **Tahap 2** | Response time > 10s, service degraded | 15 min | Restart services, notify users |
| **Tahap 3** | API down, database failure | 5 min | Full team response, consider rollback |
| **Tahap 4** | Data loss, security breach | Immediate | Disaster recovery, management notification |

### Quick Response Commands

**Tahap 1 - Performance Issues:**

```bash
# Clear caches
php artisan cache:clear
php artisan config:cache

# Restart queue workers
sudo supervisorctl restart ictserve-horizon

# Monitor for 15 minutes
watch -n 60 'curl -s https://ictserve.motac.gov.my/api/v1/ollama/health | jq .data.status'
```

**Tahap 2 - Service Degraded:**

```bash
# Restart all services
sudo systemctl restart php8.2-fpm nginx
sudo supervisorctl restart all

# Check Ollama
sudo systemctl restart ollama
curl http://127.0.0.1:11434/api/tags

# Enable graceful degradation
php artisan ai:enable-degradation --level=2
```

**Tahap 3 - Critical Issues:**

```bash
# Put application in maintenance mode
php artisan down --message="Sistem dalam penyelenggaraan kecemasan"

# Check all services
sudo systemctl status nginx mysql redis-server php8.2-fpm ollama

# Consider rollback
/usr/local/bin/rollback.sh --interactive
```

### Rollback Procedures

**Quick Rollback:**

```bash
# Rollback to previous stable version
cd /var/www/ictserve-ai
git checkout previous-stable-tag
composer install --no-dev --optimize-autoloader
php artisan migrate:rollback --step=1
php artisan config:cache
sudo systemctl restart php8.2-fpm nginx
```

**Database Rollback:**

```bash
# Restore from backup
mysql -u root -p ictserve_production < /backup/mysql_YYYYMMDD.sql

# Rollback specific migration
php artisan migrate:rollback --path=database/migrations/ollama
```

### Emergency Contacts

| Role | Contact | Response Time |
|------|---------|---------------|
| Lead Developer | +603-8000-1234 | 15 min |
| System Administrator | +603-8000-1235 | 30 min |
| Database Administrator | +603-8000-1236 | 30 min |
| Network Administrator | +603-8000-1237 | 30 min |

### Post-Incident Checklist

- [ ] System functioning normally
- [ ] Data integrity verified
- [ ] Performance within targets
- [ ] Security verified
- [ ] Monitoring active
- [ ] Backup completed
- [ ] Documentation updated
- [ ] Users notified of resolution
- [ ] Post-mortem scheduled

---

## Deployment Checklist Summary

> **Dokumentasi Penuh**: Lihat [ollama-ai-deployment-checklist.md](./deployment/ollama-ai-deployment-checklist.md) untuk senarai semak lengkap.

### Pre-Deployment

- [ ] Server meets minimum requirements (16GB RAM, 8 cores, 100GB SSD)
- [ ] PHP 8.2.12+, MySQL 8.0+, Redis 7.0+ installed
- [ ] Ollama installed and llama3.1 model downloaded
- [ ] SSL certificate valid and configured
- [ ] Firewall rules configured (block external Ollama access)
- [ ] Backup system configured and tested

### Application Deployment

- [ ] Repository cloned and correct branch checked out
- [ ] Composer dependencies installed (--no-dev)
- [ ] NPM dependencies installed and assets built
- [ ] Environment file configured (.env.production)
- [ ] Application key generated
- [ ] Database migrations run
- [ ] Configuration cached

### Service Configuration

- [ ] Nginx/Apache virtual host configured with SSL
- [ ] Laravel Horizon systemd service enabled
- [ ] Laravel Reverb systemd service enabled
- [ ] Cron jobs configured for scheduled tasks
- [ ] Log rotation configured

### Post-Deployment Verification

- [ ] Homepage loads successfully
- [ ] API health endpoint responds
- [ ] FAQ Bot responds in Bahasa Melayu
- [ ] WebSocket connections working
- [ ] Queue jobs processing
- [ ] Monitoring dashboards accessible

### D00-D17 v3.6.0 Compliance

- [ ] True Hybrid Architecture (nullable user_id FK)
- [ ] Dual Audit System (owen-it + spatie)
- [ ] Bahasa Melayu sahaja (no language switcher)
- [ ] WCAG 2.2 AA compliance
- [ ] Core Web Vitals targets met
- [ ] Data residency Malaysia

---

## Additional API Documentation References

| Document | Description | Location |
|----------|-------------|----------|
| [ollama-ai-api-documentation.md](./api/ollama-ai-api-documentation.md) | Full API reference | `docs/ollama/api/` |
| [ollama-ai-integration-api.md](./api/ollama-ai-integration-api.md) | Integration guide | `docs/ollama/api/` |
| [API_AUTHENTICATION_IMPLEMENTATION.md](./api/API_AUTHENTICATION_IMPLEMENTATION.md) | Auth implementation | `docs/ollama/api/` |
| [ollama-openapi-spec.yaml](./api/ollama-openapi-spec.yaml) | OpenAPI 3.0 spec | `docs/ollama/api/` |

## Additional Deployment Documentation References

| Document | Description | Location |
|----------|-------------|----------|
| [ollama-ai-deployment-guide.md](./deployment/ollama-ai-deployment-guide.md) | Full deployment guide | `docs/ollama/deployment/` |
| [ollama-ai-production-deployment.md](./deployment/ollama-ai-production-deployment.md) | Production setup | `docs/ollama/deployment/` |
| [emergency-procedures.md](./deployment/emergency-procedures.md) | Emergency procedures | `docs/ollama/deployment/` |
| [ollama-ai-deployment-checklist.md](./deployment/ollama-ai-deployment-checklist.md) | Deployment checklist | `docs/ollama/deployment/` |
| [production-environment-setup.md](./deployment/production-environment-setup.md) | Environment setup | `docs/ollama/deployment/` |

---

**Dokumen ini mematuhi D11 v3.6.0 Technical Design Documentation dan menyediakan panduan komprehensif untuk integrasi Cloud Hybrid AI (Ollama + AWS Bedrock) dalam sistem ICTServe.**
