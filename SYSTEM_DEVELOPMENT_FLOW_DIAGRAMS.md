# Rajah Aliran Pembangunan Sistem (System Development Flow) ICTServe v3.6.1

Rajah di bawah memodelkan aliran pembangunan ICTServe berdasarkan Pelan Pembangunan Sistem v3.6.1.

Sumber utama: `_reference/versions/v3.6.1_D01_SYSTEM_DEVELOPMENT_PLAN.md`

---

## SDF-ICT-0 — Gambaran Keseluruhan SDLC (Fasa 4.1–4.5)

```mermaid
flowchart TD
    START([Mula])

    subgraph Roles[Peranan Utama]
        PO[Project Owner - BPM MOTAC]
        PM[Project Manager]
        SA[System Analyst]
        DEV[Lead/Backend/Frontend Developer]
        QA[QA/Test Engineer]
        DO[DevOps Engineer]
        EU[End User - Staf MOTAC]
    end

    subgraph P41[4.1 Inisiasi & Keperluan]
        RQ_GATHER[Pengumpulan Keperluan]
        RQ_DOC[Dokumentasi Keperluan]
        D02[D02: Business Requirements]
        D03[D03: Software Requirements]
    end

    subgraph P42[4.2 Rekabentuk Sistem]
        DES_ARCH[Reka Bentuk Seni Bina]
        DES_DB[Reka Bentuk Pangkalan Data]
        DES_UI[Reka Bentuk Antara Muka]
        DES_RT[Reka Bentuk Real-time]
        D04[D04: Software Design Document]
        D09[D09: Database Documentation]
        D12[D12: UI/UX Design Guide]
    end

    subgraph P43[4.3 Pembangunan - Implementation]
        ENV_SETUP[Setup Environment - Docker, Vite]
        BUILD_CORE[Pembangunan Core - Auth, Model, Migration]
        BUILD_MOD[Pembangunan Modul - Helpdesk, Loan, Inventory]
        BUILD_ADMIN[Filament Admin - SDUI]
        BUILD_RT[Real-time - Reverb/Echo]
        BUILD_API[API Auth - Sanctum & SSO - Opsyen]
        BUILD_AUDIT[Audit Trail + Debug/Monitoring]
        BUILD_AI[AI Cloud Hybrid - Ollama + Bedrock]
        D10[D10: Source Code Documentation]
    end

    subgraph P44[4.4 Ujian - Testing]
        T_UNIT[Unit Test - PHPUnit]
        T_FEATURE[Feature Test - Laravel]
        T_E2E[E2E Test - Playwright]
        T_A11Y[Accessibility Test - Axe-core — WCAG 2.2 AA]
        T_STATIC[Static Analysis - Larastan/PHPStan]
        T_FORMAT[Code Quality - Laravel Pint — PSR-12]
        T_UAT[User Acceptance Testing - UAT]
    end

    subgraph P45[4.5 Deployment & Maintenance]
        DEPLOY_DEV[Deployment: Development]
        DEPLOY_PROD[Deployment: Production Intranet]
        BUILD_ASSETS[Vite Build - Production Assets]
        DEPLOY_RT[Deploy Reverb - WebSocket]
        OPTIMIZE[Optimize/Cache - config/route/view]
        MONITOR[Log & Monitoring - Pulse/Audit/Logs]
        MAINT[Maintenance - Patch/Backup/Support]
    end

    START --> SA
    SA --> RQ_GATHER
    PO --> RQ_GATHER
    RQ_GATHER --> RQ_DOC
    RQ_DOC --> D02
    RQ_DOC --> D03

    D02 --> DES_ARCH
    D03 --> DES_ARCH
    DES_ARCH --> D04
    DES_DB --> D09
    DES_UI --> D12
    DES_RT --> D04

    D04 --> ENV_SETUP
    DEV --> ENV_SETUP
    ENV_SETUP --> BUILD_CORE
    BUILD_CORE --> BUILD_MOD
    BUILD_MOD --> BUILD_ADMIN
    BUILD_MOD --> BUILD_RT
    BUILD_MOD --> BUILD_API
    BUILD_MOD --> BUILD_AUDIT
    BUILD_MOD --> BUILD_AI
    BUILD_MOD --> D10

    BUILD_MOD --> T_FORMAT
    T_FORMAT --> T_STATIC
    T_STATIC --> T_UNIT
    T_UNIT --> T_FEATURE
    T_FEATURE --> T_E2E
    T_E2E --> T_A11Y
    T_A11Y --> T_UAT
    EU --> T_UAT

    T_UAT --> DEPLOY_DEV
    DO --> DEPLOY_DEV
    DEPLOY_DEV --> BUILD_ASSETS
    BUILD_ASSETS --> DEPLOY_RT
    DEPLOY_RT --> DEPLOY_PROD
    DEPLOY_PROD --> OPTIMIZE
    OPTIMIZE --> MONITOR
    MONITOR --> MAINT
```

---

## SDF-ICT-1 — Aliran Milestone & Deliverable (Seksyen 6)

```mermaid
flowchart TD
    M1[Inisiasi & Keperluan\nDeliverable: D02, D03, ERD]
    M2[Rekabentuk Sistem\nDeliverable: D04, Wireframe, Database Schema]
    M3[Setup Development\nDeliverable: Docker environment, CI/CD pipeline]
    M4[Pembangunan Core\nDeliverable: Authentication, Models, Migrations]
    M5[Pembangunan Modules\nDeliverable: Helpdesk, Asset Loan, Filament Admin]
    M6[Real-time Features\nDeliverable: Reverb integration]
    M7[Performance & API\nDeliverable: Pulse, Sanctum API, Google SSO - opsyen]
    M8[UI/UX Implementation\nDeliverable: Livewire components, Styling, Accessibility]
    M9[Ujian & UAT\nDeliverable: PHPUnit, Playwright, Axe-core, UAT]
    M10[Documentation\nDeliverable: D09-D14, Manual Pengguna, API docs]
    M11[Deployment\nDeliverable: Production deployment, monitoring setup]
    M12[Maintenance\nDeliverable: Patch, backup, support, monitoring]

    M1 --> M2 --> M3 --> M4 --> M5 --> M6 --> M7 --> M8 --> M9 --> M10 --> M11 --> M12
```

---

## SDF-ICT-QA — Quality Gates (Ujian, Analisis Statik, Aksesibiliti)

```mermaid
flowchart TD
    DEV_CHANGE[Perubahan Kod / Feature Baharu] --> Q_FORMAT[Pint - PSR-12]
    Q_FORMAT --> Q_STATIC[Larastan - PHPStan untuk Laravel]
    Q_STATIC --> Q_UNIT[PHPUnit — Unit Test]
    Q_UNIT --> Q_FEATURE[Laravel — Feature Test]
    Q_FEATURE --> Q_E2E[Playwright — E2E Test]
    Q_E2E --> Q_A11Y[Axe-core — WCAG 2.2 AA]
    Q_A11Y --> Q_UAT[UAT bersama BPM & Staf MOTAC]
    Q_UAT --> READY[Ready untuk Deployment]
```

---

## SDF-ICT-AI — Aliran Pembangunan AI (Seksyen 6.1)

```mermaid
flowchart TD
    AI_START([Mula: Keperluan AI - D18])

    AI1[Fasa 1: Asas & Infrastruktur\nDeliverable: OllamaClient, config/ollama.php, health checks]
    AI2[Fasa 2: Skema Pangkalan Data\nDeliverable: Faq/Document/MessageLog models + migrations]
    AI3[Fasa 3: Core AI Services\nDeliverable: RagService, DocumentService, EmbeddingService]
    AI4[Fasa 4: Background Jobs\nDeliverable: DocumentIngestJob, EmbeddingJob, queue setup]
    AI5[Fasa 5: API Endpoints\nDeliverable: Controllers + routes]
    AI6[Fasa 6: Filament Admin\nDeliverable: Resources, widgets]
    AI7[Fasa 7: Security & Compliance\nDeliverable: PIIDetectionService, policies, audit]
    AI8[Fasa 8: Livewire Components\nDeliverable: FaqBot, widget, chat UI]
    AI9[Fasa 9: Email Notifications\nDeliverable: ApprovalEmailToken, signed URLs]
    AI10[Fasa 10: Performance Optimization\nDeliverable: Redis caching, Pulse integration]
    AI11[Fasa 11: Testing & Documentation\nDeliverable: Tests + API docs]
    AI12[Fasa 12: Deployment & Monitoring\nDeliverable: Health checks, alerting, production setup]
    AI13[Fasa 13: Cloud Hybrid AI - Bedrock\nDeliverable: BedrockService, ModelRouter, hybrid responses]

    AI_START --> AI1 --> AI2 --> AI3 --> AI4 --> AI5 --> AI6 --> AI7 --> AI8 --> AI9 --> AI10 --> AI11 --> AI12 --> AI13
```
