# D00–D18 Diagram Index

Senarai ini menghimpunkan semua rujukan **diagram/rajah** yang wujud dalam dokumen `docs/D00_*.md` hingga `docs/D18_*.md` (top-level).

> Nota: Dokumen di bawah `docs/KRISA/` tidak termasuk kerana bukan julat `D00–D18` top-level.

---

## D01 — System Development Plan

- Mentioned: ERD (Entity Relationship Diagram) untuk `users`, `tickets`, `assets`, `loans` — `docs/D01_SYSTEM_DEVELOPMENT_PLAN.md#L121`
- Mentioned: “ERD: Entity Relationship Diagram” — `docs/D01_SYSTEM_DEVELOPMENT_PLAN.md#L388`

## D02 — Business Requirements Specification

### Project overview / stakeholder relationship diagram

Source: `docs/D02_BUSINESS_REQUIREMENTS_SPECIFICATION.md#L95`

```text
+---------------------------------------------------------------------+
|                        PENGURUSAN MOTAC                             |
+---------------------------------------------------------------------+
                              |
              +---------------v----------------+
              |  BAHAGIAN PENGURUSAN MAKLUMAT  |
              |            (BPM)               |
              +---------------+----------------+
                              |
               +--------------+--------------+
               |                             |
          +----v----+                   +----v----+
          |  UNIT   |                   |  UNIT   |
          |TEKNIKAL |                   |  ASET   |
          |  ICT    |                   |  ICT    |
          +---------+                   +---------+
               |                             |
          +----v----+                   +----v----+
          |HELPDESK |                   |PINJAMAN |
          |/SERVICE |                   |ASET ICT |
          |  DESK   |                   |         |
          +---------+                   +---------+
               |                             |
               v                             v
+---------------------------------------------------------------------+
|                     PENGGUNA AKHIR                                  |
|                   (WARGA MOTAC)                                     |
+---------------------------------------------------------------------+
```

### Arkitektur Bisnes (Business Architecture)

Source: `docs/D02_BUSINESS_REQUIREMENTS_SPECIFICATION.md#L170`

```text
+-------------------------------------------------------------------------+
|                        MEDIUM PERKHIDMATAN                              |
|  Aplikasi Web | Portal Dalaman | E-mel | Notifikasi Push | API (Future) |
+-------------------------------------------------------------------------+
|                       PENGGUNA PERKHIDMATAN                             |
| DALAMAN: Warga MOTAC (Staff/Guest) | Admin | Superuser | Pegawai Kelulusan |
+-------------------------------------------------------------------------+
|                        PERKHIDMATAN UTAMA                               |
| Pengurusan Helpdesk | Pengurusan Pinjaman Aset | Pemantauan Prestasi   |
| Pengurusan Pengguna | Audit & Keselamatan | Laporan & Dashboard         |
+-------------------------------------------------------------------------+
|             SISTEM APLIKASI YANG MENYOKONG PERKHIDMATAN                 |
|  +---------------------------+  +----------------------------------+     |
|  | Modul Helpdesk/ServiceDesk|  | Modul Pinjaman Aset ICT          |     |
|  | - Hybrid Submission       |  | - Hybrid Application             |     |
|  | - SLA Management          |  | - Email Approval Workflow        |     |
|  | - Real-time Notifications |  | - Asset Check-out/Check-in       |     |
|  | - Status Tracking         |  | - Conflict Detection             |     |
|  +---------------------------+  +----------------------------------+     |
|  +---------------------------+  +----------------------------------+     |
|  | Modul Pengurusan Pengguna |  | Modul Pemantauan & Audit         |     |
|  | - Self-Registration       |  | - Laravel Pulse Dashboard        |     |
|  | - Flexible Login          |  | - Laravel Telescope (Superuser)  |     |
|  | - Account Linking         |  | - Dual Audit (owen-it + spatie)  |     |
|  | - Profile Management      |  | - Performance Monitoring         |     |
|  +---------------------------+  +----------------------------------+     |
|  +---------------------------+  +----------------------------------+     |
|  | Modul API & Integrasi     |  | Modul Laporan & Dashboard        |     |
|  | - Laravel Sanctum         |  | - Real-time Widgets              |     |
|  | - Google Workspace SSO    |  | - KPI Metrics                    |     |
|  | - Token Management        |  | - Export Reports                 |     |
|  +---------------------------+  +----------------------------------+     |
+-------------------------------------------------------------------------+
|                          MAKLUMAT (DATA)                                |
| DALAMAN: Pengguna | Tiket | Aset | Pinjaman | Audit | Performance Metrics |
| LUARAN: Google Workspace (SSO) | Email Gateway | External APIs (Future)  |
+-------------------------------------------------------------------------+
|                           TEKNOLOGI                                     |
| Laravel 12.43.1 | PHP 8.2.12 | MySQL 8.0 | Redis 7.0 | Livewire 3.7.3    |
| Filament 4.3.1 | Laravel Reverb 1.6.3 | Laravel Pulse 1.4.7              |
| Laravel Sanctum 4.2.1 | Laravel Socialite 5.24.0 | Tailwind CSS 4.1.18          |
+-------------------------------------------------------------------------+
```

### Arkitektur Maklumat (Information Architecture)

Source: `docs/D02_BUSINESS_REQUIREMENTS_SPECIFICATION.md#L217`

```text
+------------+--------------------------------------------------+------------------+
|  PENGGUNA  |                 PROSES BISNES                    |    MAKLUMAT      |
+------------+--------------------------------------------------+------------------+
| Warga      | Mengurus Profil Pengguna                         | Maklumat         |
| MOTAC      | - Self-Registration                              | Pengguna         |
| (Staff/    | - Flexible Login                                 | (users table)    |
| Guest)     | - Account Linking                                |                  |
|            |                                                  |                  |
|            | Mengurus Helpdesk & ServiceDesk <-------------> | Maklumat Tiket   |
|            | - Hybrid Submission (Auth/Guest)                 | (helpdesk_tickets|
|            | - Status Tracking                                | submitter_*)     |
|            | - SLA Management                                 |                  |
|            |                                                  |                  |
| Pegawai    | Mengurus Pinjaman Aset ICT <------------------> | Maklumat Aset    |
| Kelulusan  | - Hybrid Application                             | (assets,         |
|            | - Email Approval Workflow                        | loan_applications|
|            | - Asset Check-out/Check-in                       | applicant_*)     |
|            |                                                  |                  |
| Admin      | Mengurus Operasi Harian                          | Maklumat         |
|            | - Ticket Management                              | Transaksi        |
|            | - Asset Management                               | (loan_transactions|
|            | - Notification Management                        | loan_approvals)  |
|            |                                                  |                  |
| Superuser  | Mengurus Konfigurasi & Audit <----------------> | Maklumat Audit   |
|            | - System Configuration                           | (audits,         |
|            | - Dual Audit Review                              | activity_log)    |
|            | - Laravel Telescope Access                       |                  |
|            | - Laravel Pulse Monitoring                       | Performance Data |
|            |                                                  | (pulse_entries,  |
|            |                                                  | pulse_values)    |
|            |                                                  |                  |
|            | Mengurus Laporan & Dashboard                     | Maklumat Laporan |
|            | - Real-time Metrics                              | (aggregated data)|
|            | - KPI Dashboards                                 |                  |
|            | - Export Reports                                 |                  |
+------------+--------------------------------------------------+------------------+
|            | SISTEM MEMBEKAL MAKLUMAT                         |                  |
|            | - Google Workspace (SSO - Optional)              |                  |
|            | - Email Gateway (Notifications)                  |                  |
|            | - External APIs (Future Integration)             |                  |
+------------+--------------------------------------------------+------------------+
```

- Mentioned: Carta alir & diagram (rujuk D04/D11) — `docs/D02_BUSINESS_REQUIREMENTS_SPECIFICATION.md#L525`

### Struktur Hierarki Fungsi Bisnes (Business Function Hierarchy)

Source: `docs/D02_BUSINESS_REQUIREMENTS_SPECIFICATION.md#L550`

```text
+-----------------------------------------------------------------------+
|                              BF-IS                                    |
|           Mengurus Perkhidmatan ICT MOTAC Dengan Efisien              |
+-----------------------------------------------------------------------+
                              |
        +---------------------+---------------------+---------------------+
        |                     |                     |                     |
   +----------+          +----------+          +----------+          +----------+
   | BF-IS-MP |          | BF-IS-HS |          | BF-IS-PA |          | BF-IS-PM |
   | Mengurus |          | Helpdesk |          | Pinjaman |          | Pemantauan|
   | Pengguna |          | Service  |          | Aset ICT |          | & Audit  |
   +----------+          | Desk     |          +----------+          +----------+
        |                +----------+               |                     |
        |                     |                     |                     |
   +---------+          +---------+           +---------+           +---------+
   |BF-IS-MP-|          |BF-IS-HS-|           |BF-IS-PA-|           |BF-IS-PM-|
   |SR       |          |TK       |           |PP       |           |PS       |
   |Self-Reg |          |Tiket    |           |Permohonan|          |Pulse    |
   +---------+          +---------+           +---------+           +---------+
   +---------+          +---------+           +---------+           +---------+
   |BF-IS-MP-|          |BF-IS-HS-|           |BF-IS-PA-|           |BF-IS-PM-|
   |FL       |          |SLA      |           |KL       |           |TS       |
   |Flexible |          |Pengurusan|          |Kelulusan|           |Telescope|
   |Login    |          +---------+           +---------+           +---------+
   +---------+          +---------+           +---------+           +---------+
   +---------+          |BF-IS-HS-|           |BF-IS-PA-|           |BF-IS-PM-|
   |BF-IS-MP-|          |NT       |           |CO       |           |DA       |
   |AL       |          |Notifikasi|          |Check-out|           |Dual     |
   |Account  |          +---------+           |Check-in |           |Audit    |
   |Linking  |                                +---------+           +---------+
   +---------+                                                      +---------+
                                                                    |BF-IS-PM-|
                                                                    |API      |
                                                                    |Sanctum  |
                                                                    +---------+
        |
   +----------+
   | BF-IS-JL |
   | Dashboard|
   | & Laporan|
   +----------+
```

## D03 — Software Requirements Specification

- Mentioned: Carta alir & diagram (rujuk D04/D11) — `docs/D03_SOFTWARE_REQUIREMENTS_SPECIFICATION.md#L587`

## D04 — Software Design Document

### System Context Diagram

Source: `docs/D04_SOFTWARE_DESIGN_DOCUMENT.md#L1880`

```text
+-------------------------------------------------------------------------+
|                        EXTERNAL SYSTEMS                                 |
|  Google Workspace (SSO) | Email Gateway | AWS Bedrock | Ollama Server   |
+-------------------------------------------------------------------------+
                                  |
                                  v
+-------------------------------------------------------------------------+
|                          ICTServe v3.6.1                                |
|                     True Hybrid Architecture                            |
+-------------------------------------------------------------------------+
|                                                                         |
|  +-------------------------------------------------------------------+  |
|  |                      CORE COMPONENTS                              |  |
|  +-------------------------------------------------------------------+  |
|  |                                                                   |  |
|  |  +-----------------------+  +-----------------------+             |  |
|  |  | Helpdesk Module       |  | Asset Loan Module     |             |  |
|  |  | - Hybrid Submission   |  | - Hybrid Application  |             |  |
|  |  | - SLA Management      |  | - Email Approval      |             |  |
|  |  | - Status Tracking     |  | - Check-out/Check-in  |             |  |
|  |  +-----------------------+  +-----------------------+             |  |
|  |                                                                   |  |
|  |  +-----------------------+  +-----------------------+             |  |
|  |  | User Management       |  | Admin Panel           |             |  |
|  |  | - Self-Registration   |  | - Filament 4.3.1      |             |  |
|  |  | - Flexible Login      |  | - Laravel Telescope   |             |  |
|  |  | - Account Linking     |  | - Laravel Pulse       |             |  |
|  |  +-----------------------+  +-----------------------+             |  |
|  |                                                                   |  |
|  |  +-----------------------+  +-----------------------+             |  |
|  |  | AI Chatbot            |  | Audit & Monitoring    |             |  |
|  |  | - Ollama (Local)      |  | - Dual Audit System   |             |  |
|  |  | - Bedrock (Cloud)     |  | - owen-it + spatie    |             |  |
|  |  | - RAG Service         |  | - Performance Metrics |             |  |
|  |  +-----------------------+  +-----------------------+             |  |
|  |                                                                   |  |
|  +-------------------------------------------------------------------+  |
|                                                                         |
+-------------------------------------------------------------------------+
                                  |
                                  v
+-------------------------------------------------------------------------+
|                            USER TYPES                                   |
|  Guest Users | Staff (Authenticated) | Admin | Superuser | Approvers    |
+-------------------------------------------------------------------------+
```

### Component Diagram (Internal Architecture)

Source: `docs/D04_SOFTWARE_DESIGN_DOCUMENT.md#L1881`

```text
+-------------------------------------------------------------------------+
|                      PRESENTATION LAYER                                 |
|  Blade Templates | Livewire 3.7.3 | Volt 1.10.1 | Filament 4.3.1       |
|  Alpine.js 3 | Tailwind CSS 4.1.18                                      |
+-------------------------------------------------------------------------+
                                  |
+-------------------------------------------------------------------------+
|                      APPLICATION LAYER                                  |
|                                                                         |
|  +-------------------------------------------------------------------+  |
|  | Controllers                                                       |  |
|  | - HelpdeskController | LoanController | AuthController          |  |
|  | - AdminController | ApiController                                |  |
|  +-------------------------------------------------------------------+  |
|                                  |                                      |
|  +-------------------------------------------------------------------+  |
|  | Services (Business Logic)                                         |  |
|  | - HelpdeskService | LoanService | ApprovalService               |  |
|  | - RegistrationService | AccountLinkingService                    |  |
|  | - RagService | BedrockService | OllamaClient                    |  |
|  | - NotificationService | TokenService                             |  |
|  +-------------------------------------------------------------------+  |
|                                  |                                      |
|  +-------------------------------------------------------------------+  |
|  | Middleware & Policies                                             |  |
|  | - SecurityMonitoring | RateLimiting | Authorization             |  |
|  +-------------------------------------------------------------------+  |
|                                                                         |
+-------------------------------------------------------------------------+
                                  |
+-------------------------------------------------------------------------+
|                        DOMAIN LAYER                                     |
|                                                                         |
|  +-------------------------------------------------------------------+  |
|  | Models (Eloquent ORM)                                             |  |
|  | - User | HelpdeskTicket | LoanApplication | Asset               |  |
|  | - Faq | Document | BedrockConversation                          |  |
|  | - Audit | Activity (Dual Audit System)                          |  |
|  +-------------------------------------------------------------------+  |
|                                  |                                      |
|  +-------------------------------------------------------------------+  |
|  | Events & Listeners                                                |  |
|  | - TicketCreated | LoanApproved | UserRegistered                 |  |
|  +-------------------------------------------------------------------+  |
|                                  |                                      |
|  +-------------------------------------------------------------------+  |
|  | Jobs (Queue Workers)                                              |  |
|  | - SendEmailNotification | ProcessApproval                        |  |
|  | - DocumentIngestJob | EmbeddingJob                              |  |
|  +-------------------------------------------------------------------+  |
|                                                                         |
+-------------------------------------------------------------------------+
                                  |
+-------------------------------------------------------------------------+
|                    INFRASTRUCTURE LAYER                                 |
|                                                                         |
|  +-------------------------------------------------------------------+  |
|  | Data Persistence                                                  |  |
|  | - MySQL 8.0 (Primary Database)                                   |  |
|  | - Redis 7.0 (Cache, Queue, Session, Reverb)                     |  |
|  +-------------------------------------------------------------------+  |
|                                  |                                      |
|  +-------------------------------------------------------------------+  |
|  | External Integrations                                             |  |
|  | - Email Gateway (SMTP)                                           |  |
|  | - Google Workspace (OAuth 2.0)                                   |  |
|  | - AWS Bedrock (Claude Models)                                    |  |
|  | - Ollama Server (Local AI)                                       |  |
|  +-------------------------------------------------------------------+  |
|                                  |                                      |
|  +-------------------------------------------------------------------+  |
|  | Monitoring & Observability                                        |  |
|  | - Laravel Pulse (Performance)                                    |  |
|  | - Laravel Telescope (Debugging)                                  |  |
|  | - Laravel Reverb (WebSocket)                                     |  |
|  +-------------------------------------------------------------------+  |
|                                                                         |
+-------------------------------------------------------------------------+
```

### Deployment Architecture

Source: `docs/D04_SOFTWARE_DESIGN_DOCUMENT.md#L1882`

```text
+-------------------------------------------------------------------------+
|                         SECURITY TIER                                   |
|  Load Balancer (HAProxy/AWS ALB) | SSL/TLS 1.3 | WAF | DDoS Protection |
+-------------------------------------------------------------------------+
                                  |
+-------------------------------------------------------------------------+
|                       APPLICATION TIER                                  |
|                                                                         |
|  +-------------------+  +-------------------+  +-------------------+    |
|  | App Server 1      |  | App Server 2      |  | App Server N      |    |
|  | - Nginx 1.24      |  | - Nginx 1.24      |  | - Nginx 1.24      |    |
|  | - PHP-FPM 8.2.12  |  | - PHP-FPM 8.2.12  |  | - PHP-FPM 8.2.12  |    |
|  | - Laravel 12.43.1 |  | - Laravel 12.43.1 |  | - Laravel 12.43.1 |    |
|  +-------------------+  +-------------------+  +-------------------+    |
|                                                                         |
+-------------------------------------------------------------------------+
                                  |
+-------------------------------------------------------------------------+
|                         WORKER TIER                                     |
|                                                                         |
|  +-------------------+  +-------------------+                           |
|  | Queue Worker 1    |  | Queue Worker 2    |                           |
|  | - Redis Queue     |  | - Redis Queue     |                           |
|  | - Supervisor      |  | - Supervisor      |                           |
|  +-------------------+  +-------------------+                           |
|                                                                         |
|  +-------------------+  +-------------------+                           |
|  | Reverb Server 1   |  | Reverb Server 2   |                           |
|  | - WebSocket       |  | - WebSocket       |                           |
|  | - Port 8080       |  | - Port 8080       |                           |
|  +-------------------+  +-------------------+                           |
|                                                                         |
+-------------------------------------------------------------------------+
                                  |
+-------------------------------------------------------------------------+
|                          DATA TIER                                      |
|                                                                         |
|  +-------------------------------------------------------------------+  |
|  | MySQL 8.0 Cluster                                                 |  |
|  | - Primary (Read/Write)                                           |  |
|  | - Replica 1 (Read-only)                                          |  |
|  | - Replica 2 (Read-only)                                          |  |
|  +-------------------------------------------------------------------+  |
|                                  |                                      |
|  +-------------------------------------------------------------------+  |
|  | Redis 7.0 Cluster                                                 |  |
|  | - Cache Node 1 | Cache Node 2 | Cache Node 3                     |  |
|  | - Queue Backend | Session Store | Reverb Backend                |  |
|  +-------------------------------------------------------------------+  |
|                                                                         |
+-------------------------------------------------------------------------+
                                  |
+-------------------------------------------------------------------------+
|                        STORAGE TIER                                     |
|  S3/MinIO (File Attachments) | Backup Storage (Daily/Weekly)           |
+-------------------------------------------------------------------------+
                                  |
+-------------------------------------------------------------------------+
|                      MONITORING TIER                                    |
|  Laravel Pulse | Laravel Telescope | Prometheus | Grafana | ELK Stack    |
+-------------------------------------------------------------------------+
```

### Guest User Flow (Helpdesk Ticket Submission)

Source: `docs/D04_SOFTWARE_DESIGN_DOCUMENT.md#L1883`

```text
Guest User                 Frontend              Backend                Database
    |                          |                      |                      |
    |---(1) Access Form------->|                      |                      |
    |                          |                      |                      |
    |                          |---(2) Load Form----->|                      |
    |                          |                      |                      |
    |                          |<--(3) Form HTML------|                      |
    |                          |                      |                      |
    |<--(4) Display Form-------|                      |                      |
    |                          |                      |                      |
    |---(5) Fill & Submit----->|                      |                      |
    |    (Manual Entry)        |                      |                      |
    |                          |                      |                      |
    |                          |---(6) Validate------>|                      |
    |                          |      (CSRF, reCAPTCHA, Rules)              |
    |                          |                      |                      |
    |                          |                      |---(7) Create Ticket->|
    |                          |                      |    (user_id = NULL)  |
    |                          |                      |                      |
    |                          |                      |<--(8) Ticket ID------||
    |                          |                      |                      |
    |                          |                      |---(9) Generate Token>|
    |                          |                      |    (SHA-512 hash)    |
    |                          |                      |                      |
    |                          |                      |---(10) Dual Audit--->|
    |                          |                      |    (owen-it+spatie)  |
    |                          |                      |                      |
    |                          |                      |---(11) Queue Email-->|
    |                          |                      |    (Redis Queue)     |
    |                          |                      |                      |
    |                          |<--(12) Success-------|                      |
    |                          |    + Status Token    |                      |
    |                          |                      |                      |
    |<--(13) Confirmation------|                      |                      |
    |    + Token Link          |                      |                      |
    |                          |                      |                      |
    |---(14) Check Status----->|                      |                      |
    |    (via Token Link)      |                      |                      |
    |                          |                      |                      |
    |                          |---(15) Verify Token->|                      |
    |                          |                      |                      |
    |                          |                      |---(16) Fetch Ticket->|
    |                          |                      |                      |
    |                          |                      |<--(17) Ticket Data---||
    |                          |                      |                      |
    |                          |<--(18) Status Info---|                      |
    |                          |                      |                      |
    |<--(19) Display Status----|                      |                      |
    |                          |                      |                      |
```

### Loan Application Approval Workflow

Source: `docs/D04_SOFTWARE_DESIGN_DOCUMENT.md#L1884`

```text
Applicant          Frontend         Backend          Approver         Database
    |                  |                  |                  |                |
    |-(1) Submit------>|                  |                  |                |
    |    Application   |                  |                  |                |
    |                  |                  |                  |                |
    |                  |-(2) Validate---->|                  |                |
    |                  |                  |                  |                |
    |                  |                  |-(3) Create------>|                |
    |                  |                  |    Application   |                |
    |                  |                  |                  |                |
    |                  |                  |<-(4) App ID------|                |
    |                  |                  |                  |                |
    |                  |                  |-(5) Generate---->|                |
    |                  |                  |    Signed URL    |                |
    |                  |                  |    (72h expiry)  |                |
    |                  |                  |                  |                |
    |                  |                  |-(6) Queue Email->|                |
    |                  |                  |    to Approver   |                |
    |                  |                  |                  |                |
    |                  |<-(7) Success-----|                  |                |
    |                  |                  |                  |                |
    |<-(8) Confirmation|                  |                  |                |
    |                  |                  |                  |                |
    |                  |                  |                  |<-(9) Email-----||
    |                  |                  |                  |    with Link   |
    |                  |                  |                  |                |
    |                  |                  |                  |-(10) Click---->|
    |                  |                  |                  |    Link        |
    |                  |                  |                  |                |
    |                  |                  |<-(11) Verify-----|                |
    |                  |                  |      Signature   |                |
    |                  |                  |                  |                |
    |                  |                  |-(12) Fetch------>|                |
    |                  |                  |      Application |                |
    |                  |                  |                  |                |
    |                  |                  |<-(13) App Data---|                |
    |                  |                  |                  |                |
    |                  |                  |-(14) Display---->|                |
    |                  |                  |      to Approver |                |
    |                  |                  |                  |                |
    |                  |                  |                  |-(15) Approve-->|
    |                  |                  |                  |    or Reject   |
    |                  |                  |                  |                |
    |                  |                  |<-(16) Decision---|                |
    |                  |                  |                  |                |
    |                  |                  |-(17) Update----->|                |
    |                  |                  |      Status      |                |
    |                  |                  |                  |                |
    |                  |                  |-(18) Dual Audit->|                |
    |                  |                  |                  |                |
    |                  |                  |-(19) Queue Email>|                |
    |                  |                  |      to Applicant|                |
    |                  |                  |                  |                |
    |<-(20) Email------|<-(21) Notify-----|                  |                |
    |    Notification  |                  |                  |                |
    |                  |                  |                  |                |
```

## D07 — System Integration Plan

- Mentioned: Integration flow diagrams (sequence diagrams, flowcharts) — `docs/D07_SYSTEM_INTEGRATION_PLAN.md#L350`

## D08 — System Integration Specification

- Mentioned: Sequence diagram & flowchart untuk integrasi utama — `docs/D08_SYSTEM_INTEGRATION_SPECIFICATION.md#L302`

## D11 — Technical Design Documentation

### System layers (Presentation/Application/Integration/Data)

Source: `docs/D11_TECHNICAL_DESIGN_DOCUMENTATION.md#L163`

```text
┌─────────────────────────────────────────────────────────┐
│                  PRESENTATION LAYER                      │
│  Blade + Livewire 3 + Volt + Filament 4 + Tailwind 4    │
└─────────────────────────────────────────────────────────┘
                           │
┌─────────────────────────────────────────────────────────┐
│                  APPLICATION LAYER                       │
│  Controllers + Services + Jobs + Middleware + Policies   │
└─────────────────────────────────────────────────────────┘
                           │
┌─────────────────────────────────────────────────────────┐
│                  INTEGRATION LAYER                       │
│  RESTful API + WebSocket (Reverb) + Email + Audit Trail │
└─────────────────────────────────────────────────────────┘
                           │
┌─────────────────────────────────────────────────────────┐
│                     DATA LAYER                           │
│  Eloquent ORM + MySQL + Redis Cache + File Storage       │
└─────────────────────────────────────────────────────────┘
```

### Fallback chain (AI routing)

Source: `docs/D11_TECHNICAL_DESIGN_DOCUMENTATION.md#L797`

```text
┌─────────────────────────────────────────────────────────┐
│                   Fallback Chain                         │
├─────────────────────────────────────────────────────────┤
│                                                          │
│  FAQ Query:                                              │
│  ┌──────────┐    ┌──────────┐    ┌──────────┐          │
│  │  Ollama  │───▶│ Bedrock  │───▶│  Static  │          │
│  │   RAG    │    │ Fallback │    │   FAQ    │          │
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

### Deployment architecture (load balancer → app servers → Redis/MySQL)

Source: `docs/D11_TECHNICAL_DESIGN_DOCUMENTATION.md#L1031`

```text
┌─────────────────────────────────────────────────────────┐
│         END-USER CLIENTS (Browser)                      │
│     Windows/macOS: Chrome, Firefox, Safari, Edge        │
└──────────────┬──────────────────────────────────────────┘
               │
         HTTPS/TLS 1.3
               │
┌──────────────▼──────────────────────────────────────────┐
│  LOAD BALANCER (HAProxy or AWS ALB)                     │
│  - SSL/TLS termination                                  │
│  - Health checks (/up endpoint)                         │
└──────────────┬──────────────────────────────────────────┘
               │
   ┌───────────┼───────────┐
   │           │           │
┌──▼──┐     ┌──▼──┐     ┌──▼──┐
│APP-1│     │APP-2│     │APP-N│  (N app servers)
│Nginx│     │Nginx│     │Nginx│
│PHP82│     │PHP82│     │PHP82│
└──┬──┘     └──┬──┘     └──┬──┘
   │           │           │
   └───────────┼───────────┘
               │
        ┌──────▼──────┐
        │ Redis Cluster│ (Session + Cache + Queue)
        └──────┬──────┘
               │
        ┌──────▼──────┐
        │ MySQL 8.0   │ (Primary + Replica)
        └─────────────┘
```

## D12 — UI/UX Design Guide

### Guest layout hierarchy

Source: `docs/D12_UI_UX_DESIGN_GUIDE.md#L244`

```text
┌─────────────────────────────────────┐
│ Header (Navigation + Language)      │
├─────────────────────────────────────┤
│                                     │
│ Main Content Area                   │
│ - Hero Section                      │
│ - Form/Content                      │
│ - AI Chat Widget (if enabled)       │
│                                     │
├─────────────────────────────────────┤
│ Footer (Links + Copyright)          │
└─────────────────────────────────────┘
```

### Authenticated layout hierarchy

Source: `docs/D12_UI_UX_DESIGN_GUIDE.md#L261`

```text
┌─────────────────────────────────────┐
│ Header (Nav + Notifications + User) │
├─────────────────────────────────────┤
│ Sidebar │ Main Content Area         │
│ - Menu  │ - Breadcrumbs            │
│ - Stats │ - Page Content           │
│         │ - AI Assistant           │
│         │                          │
├─────────────────────────────────────┤
│ Footer (System Info + Links)        │
└─────────────────────────────────────┘
```

### Admin layout (Filament) hierarchy

Source: `docs/D12_UI_UX_DESIGN_GUIDE.md#L277`

```text
┌─────────────────────────────────────┐
│ Admin Header (Filament Navigation)  │
├─────────────────────────────────────┤
│ Sidebar │ Admin Content            │
│ - Admin │ - Dashboard/Resources    │
│   Menu  │ - AI Analytics           │
│ - Stats │ - System Monitoring      │
│         │                          │
└─────────────────────────────────────┘
```

- Codebase check: `resources/views/layouts/guest.blade.php` dan `resources/views/layouts/app.blade.php` wujud dalam repo semasa.

## D13 — UI/UX Frontend Framework

### Layout directory structure

Source: `docs/D13_UI_UX_FRONTEND_FRAMEWORK.md#L484`

```text
resources/views/
├── components/
│   ├── layouts/
│   │   ├── app.blade.php          # Authenticated layout
│   │   └── guest.blade.php        # Public/guest layout
│   ├── forms/                     # Form components
│   ├── ui/                        # UI components
│   └── auth/                      # Auth components (v3.5.0)
├── livewire/                      # Livewire/Volt components
│   ├── auth/                      # Auth components (v3.5.0)
│   ├── dashboard/                 # Dashboard components
│   └── account/                   # Account management (v3.5.0)
├── filament/                      # Filament view overrides
└── auth/                          # Laravel Breeze auth views
```

- Codebase check: `resources/views/components/layouts/guest.blade.php` tiada; layout guest semasa berada di `resources/views/layouts/guest.blade.php`.

## D16 — Broadcasting Setup

### Broadcasting workflow (step-by-step flow)

Source: `docs/D16_BROADCASTING_SETUP.md#L104`

```text
1. Acara dipicu (Event dispatched) → app/Events/NotificationCreated.php
   ↓
2. Penyiaran disahkan oleh shouldBroadcast() & broadcastOn()
   ↓
3. Pekerjaan baris gilir disimpan (Redis queue)
   ↓
4. Pekerja baris gilir memproses & menghubungi penyedia
   ↓
5. Penyedia menghantar mesej ke saluran (Reverb/Pusher)
   ↓
6. Frontend Echo menerima & mengemas kini antarmuka tanpa muatan semula
```

- Codebase check: `app/Events/NotificationCreated.php` wujud dalam repo semasa.

## D18 — AI Chatbot (Ollama + Bedrock)

### Rajah seni bina sistem (System architecture diagram)

Source: `docs/D18_AI_CHATBOT_OLLAMA_BEDROCK.md#L144`

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

- Codebase check (ringkas): `app/Livewire/BedrockChat.php`, `app/Services/RagService.php`, `app/Services/BedrockService.php` wujud dalam repo semasa.

### Seni bina lapisan perkhidmatan (Service layer architecture)

Source: `docs/D18_AI_CHATBOT_OLLAMA_BEDROCK.md#L233`

```text
┌─────────────────────────────────────────────────────────────────────────────┐
│                        Laravel 12.43.1 Application                         │
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

- Codebase check (ketidakpadanan dalam repo semasa):
  - Tidak dijumpai: `app/Services/BedrockClient.php`, `app/Services/WebSearchService.php`, `app/Services/StreamingResponseService.php`
  - Tidak dijumpai: `app/Http/Controllers/OllamaController.php`, `app/Http/Controllers/FaqController.php`, `app/Http/Controllers/DocumentController.php`, `app/Http/Controllers/AutoReplyController.php`

### Fallback chain

Source: `docs/D18_AI_CHATBOT_OLLAMA_BEDROCK.md#L622`

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

### Cost optimization flow

Source: `docs/D18_AI_CHATBOT_OLLAMA_BEDROCK.md#L663`

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
