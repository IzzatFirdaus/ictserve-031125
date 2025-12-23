# Draw.io Diagram Templates for ICTServe KRISA Documents

This document provides detailed templates and prompts for creating all diagrams referenced in the ICTServe KRISA documentation using Draw.io (diagrams.net).

## Table of Contents

- [A. Business Requirements Specification (D02 BRS) Diagrams](#a-business-requirements-specification-d02-brs-diagrams)
- [B. System Requirements Specification (D03 SRS) Diagrams](#b-system-requirements-specification-d03-srs-diagrams)
- [C. Software Design Document (D04 SDD) Diagrams](#c-software-design-document-d04-sdd-diagrams)
- [D. General Guidelines](#d-general-guidelines)

---

## A. Business Requirements Specification (D02 BRS) Diagrams

### 1. Rajah 1: Gambaran Bisnes Pengurusan ICTServe

**File Name:** `D02_Rajah1_Gambaran_Bisnes_ICTServe.drawio`

**Draw.io Template Prompt:**

```text
Create a high-level business overview diagram with the following elements:

LAYOUT: Horizontal flow from left to right
STYLE: Modern business diagram with MOTAC color scheme (blue/green)

COMPONENTS:
1. LEFT SIDE - User Groups (3 swim lanes):
   - "Warga MOTAC (Staf)" [Rectangle with person icon]
   - "Warga MOTAC (Tetamu)" [Rectangle with guest icon]
   - "Pegawai Pelulus (Gred 41+)" [Rectangle with approval icon]

2. CENTER - ICTServe Portal (Main container):
   - "Portal Hibrid ICTServe" [Large rounded rectangle]
   - Sub-components inside:
     * "Akses Tetamu" [Small box]
     * "Dashboard Staf" [Small box]
     * "Panel Admin" [Small box]

3. RIGHT SIDE - BPM Units (2 swim lanes):
   - "Unit Teknikal BPM" [Rectangle with tools icon]
   - "Unit Aset BPM" [Rectangle with inventory icon]

CONNECTIONS:
- Bidirectional arrows between users and portal
- Arrows from portal to BPM units
- Label arrows with: "Aduan", "Pinjaman", "Kelulusan", "Pemprosesan"

COLORS:
- User groups: Light blue (#E3F2FD)
- Portal: Green gradient (#4CAF50 to #81C784)
- BPM units: Orange (#FF9800)
- Arrows: Dark blue (#1976D2)

ANNOTATIONS:
- Add "True Hybrid Architecture" label at top
- Include MOTAC logo in top-right corner
```

### 2. Rajah 2: Arkitektur Bisnes ICTServe

**File Name:** `D02_Rajah2_Arkitektur_Bisnes.drawio`

**Draw.io Template Prompt:**

```text
Create a layered business architecture diagram:

LAYOUT: 4-tier architecture (top to bottom)
STYLE: Enterprise architecture diagram with clear layer separation

LAYERS:
1. PRESENTATION LAYER (Top):
   - "Web Browser" [Browser icon]
   - "Mobile Device" [Phone icon]
   - "Intranet Access" [Network icon]

2. APPLICATION LAYER:
   - "ICTServe v3.6.1" [Large container with Laravel logo]
   - Sub-modules in grid:
     * "Helpdesk Module" [Ticket icon]
     * "Asset Loan Module" [Asset icon]
     * "AI Chatbot Module" [Bot icon]
     * "Admin Panel" [Settings icon]

3. INTEGRATION LAYER:
   - "Laravel Breeze (Auth)" [Key icon]
   - "Filament (Admin UI)" [Panel icon]
   - "Livewire (Real-time)" [Lightning icon]
   - "AWS Bedrock API" [Cloud icon]
   - "Ollama Local AI" [CPU icon]

4. DATA LAYER (Bottom):
   - "MySQL Database" [Database icon]
   - "Redis Cache/Queue" [Memory icon]
   - "File Storage" [Folder icon]
   - "Audit Logs" [Document icon]

CONNECTIONS:
- Vertical arrows between layers
- Horizontal connections within layers
- Dotted lines for optional integrations

COLORS:
- Presentation: Light gray (#F5F5F5)
- Application: Blue gradient (#2196F3)
- Integration: Purple (#9C27B0)
- Data: Green (#4CAF50)

ANNOTATIONS:
- "True Hybrid Access" label on presentation layer
- "Cloud Hybrid AI" label on integration layer
```

### 3. Rajah 3: Arkitektur Maklumat Sistem

**File Name:** `D02_Rajah3_Arkitektur_Maklumat.drawio`

**Draw.io Template Prompt:**

```text
Create an information architecture diagram showing data relationships:

LAYOUT: Entity-relationship style with central hub
STYLE: Database/information architecture diagram

CENTRAL HUB:
- "ICTServe Data Hub" [Large hexagon in center]

MAIN ENTITIES (Around the hub):
1. "User Profiles" [Rectangle with user icon]
   - Attributes: NRIC, Name, Email, Department, Role

2. "Helpdesk Tickets" [Rectangle with ticket icon]
   - Attributes: Ticket No, Category, Status, Priority, SLA

3. "Asset Inventory" [Rectangle with inventory icon]
   - Attributes: Asset ID, Type, Status, Location, Condition

4. "Loan Applications" [Rectangle with form icon]
   - Attributes: Loan No, Asset, Dates, Approver, Status

5. "AI Conversations" [Rectangle with chat icon]
   - Attributes: Session ID, Query, Response, Model Used

6. "Audit Trails" [Rectangle with log icon]
   - Attributes: Action, User, Timestamp, Changes, IP

RELATIONSHIPS:
- Lines connecting entities to central hub
- Cardinality labels (1:M, M:M, etc.)
- Different line styles for different relationship types

DATA FLOWS:
- Arrows showing data movement
- Labels: "Create", "Read", "Update", "Delete", "Audit"

COLORS:
- Central hub: Gold (#FFC107)
- User data: Blue (#2196F3)
- Operational data: Green (#4CAF50)
- System data: Purple (#9C27B0)
- Audit data: Red (#F44336)

ANNOTATIONS:
- "PDPA 2010 Compliant" label
- "Dual Audit System" notation
```

### 4. Rajah 4: Hirarki Fungsi Bisnes

**File Name:** `D02_Rajah4_Hirarki_Fungsi.drawio`

**Draw.io Template Prompt:**

```text
Create a hierarchical organizational chart of business functions:

LAYOUT: Tree structure (top-down hierarchy)
STYLE: Organizational chart with function boxes

ROOT LEVEL:
- "ICTServe System" [Large rounded rectangle at top]

LEVEL 1 - Main Modules (5 branches):
1. "BF-ICT-AM: Pengurusan Akses" [Rectangle]
2. "BF-ICT-AD: Pengurusan Aduan" [Rectangle]
3. "BF-ICT-PJ: Pengurusan Pinjaman" [Rectangle]
4. "BF-ICT-AI: Bantuan Pintar" [Rectangle]
5. "BF-ICT-PT: Pentadbiran" [Rectangle]

LEVEL 2 - Sub-functions (under each module):
AM (Access Management):
- "Pendaftaran Diri"
- "Log Masuk Hibrid"
- "Pengurusan Profil"

AD (Helpdesk):
- "Hantar Aduan"
- "Proses Tiket"
- "Tutup Aduan"

PJ (Asset Loan):
- "Mohon Pinjaman"
- "Kelulusan"
- "Serah/Pulang"

AI (Smart Assistant):
- "FAQ Chatbot"
- "Auto-Reply"
- "Analisis Dokumen"

PT (Administration):
- "Pengurusan Data"
- "Laporan"
- "Audit"

CONNECTIONS:
- Solid lines for direct relationships
- Tree-style connectors

COLORS:
- Root: Dark blue (#1565C0)
- Level 1: Medium blue (#1976D2)
- Level 2: Light blue (#42A5F5)
- Connectors: Gray (#757575)

ANNOTATIONS:
- Function codes (BF-ICT-XX) in each box
- "True Hybrid Support" label where applicable
```

### 5. Rajah 5: Aliran Proses PFD-ICT-AD (Pengurusan Aduan)

**File Name:** `D02_Rajah5_Proses_Helpdesk.drawio`

**Draw.io Template Prompt:**

```text
Create a detailed process flow diagram for helpdesk management:

LAYOUT: Horizontal swimlane flowchart
STYLE: BPMN-style process diagram

SWIMLANES (Top to bottom):
1. "Pengguna (Staf/Tetamu)" [Light blue background]
2. "Sistem ICTServe" [Light green background]
3. "Admin BPM" [Light orange background]

PROCESS FLOW (Left to right):
START: "Mula" [Circle]

PENGGUNA LANE:
1. "Akses Portal" [Rectangle]
2. "Pilih Mod Akses" [Diamond] → "Login?" → Yes/No paths
3. "Isi Borang Aduan" [Rectangle]
4. "Hantar Aduan" [Rectangle]
5. "Terima Notifikasi" [Rectangle]

SISTEM LANE:
1. "Semak Status Login" [Diamond]
2. "Auto-fill Data" [Rectangle] (if logged in)
3. "Validasi Borang" [Rectangle]
4. "Jana Nombor Tiket" [Rectangle]
5. "Simpan ke Database" [Rectangle]
6. "Hantar E-mel" [Rectangle]
7. "Notifikasi Real-time" [Rectangle]

ADMIN LANE:
1. "Terima Notifikasi" [Rectangle]
2. "Semak Tiket" [Rectangle]
3. "Proses Aduan" [Rectangle]
4. "Kemas Kini Status" [Rectangle]
5. "Tutup Tiket" [Rectangle]

END: "Selesai" [Circle]

DECISION POINTS:
- "Login?" [Diamond with Yes/No paths]
- "Valid?" [Diamond with Yes/No paths]
- "Selesai?" [Diamond with Yes/No paths]

CONNECTIONS:
- Solid arrows for main flow
- Dotted arrows for notifications
- Different colors for different types of actions

COLORS:
- Start/End: Green circles
- Process: Blue rectangles
- Decision: Yellow diamonds
- Notification: Purple rectangles

ANNOTATIONS:
- "True Hybrid Access" label
- SLA timers on critical steps
- "Dual Audit" notation
```

### 6. Rajah 6: Aliran Proses PFD-ICT-PJ (Pinjaman Aset)

**File Name:** `D02_Rajah6_Proses_Pinjaman.drawio`

**Draw.io Template Prompt:**

```text
Create a detailed process flow diagram for asset loan management:

LAYOUT: Horizontal swimlane flowchart with approval workflow
STYLE: BPMN-style process diagram with email integration

SWIMLANES (Top to bottom):
1. "Pemohon (Staf/Tetamu)" [Light blue background]
2. "Sistem ICTServe" [Light green background]
3. "Pegawai Pelulus (Gred 41+)" [Light purple background]
4. "Admin BPM" [Light orange background]

PROCESS FLOW:
START: "Mula" [Circle]

PEMOHON LANE:
1. "Akses Portal Pinjaman" [Rectangle]
2. "Semak Ketersediaan Aset" [Rectangle]
3. "Isi Borang Permohonan" [Rectangle]
4. "Hantar Permohonan" [Rectangle]
5. "Terima Status" [Rectangle]

SISTEM LANE:
1. "Semak Konflik Jadual" [Diamond]
2. "Validasi Permohonan" [Rectangle]
3. "Jana Kod Rujukan" [Rectangle]
4. "Simpan Permohonan" [Rectangle]
5. "Jana Token Kelulusan" [Rectangle]
6. "Hantar E-mel Pelulus" [Rectangle]
7. "Kemas Kini Status" [Rectangle]

PELULUS LANE:
1. "Terima E-mel" [Rectangle]
2. "Klik Pautan Token" [Rectangle]
3. "Semak Butiran" [Rectangle]
4. "Buat Keputusan" [Diamond] → "Lulus/Tolak"
5. "Masukkan Ulasan" [Rectangle]
6. "Hantar Keputusan" [Rectangle]

ADMIN LANE:
1. "Terima Notifikasi" [Rectangle]
2. "Sediakan Aset" [Rectangle] (if approved)
3. "Serah Aset" [Rectangle]
4. "Rekod Serah Terima" [Rectangle]
5. "Pantau Tarikh Pulang" [Rectangle]

END: "Selesai" [Circle]

DECISION POINTS:
- "Aset Tersedia?" [Diamond]
- "Permohonan Valid?" [Diamond]
- "Keputusan Pelulus?" [Diamond]

EMAIL INTEGRATION:
- Email icons for notification steps
- Token security symbols
- Digital signature indicators

COLORS:
- Approval process: Green
- Rejection process: Red
- System process: Blue
- Email/notification: Purple

ANNOTATIONS:
- "Token-based Approval" label
- "No Login Required for Approver" note
- Security indicators for signed URLs
```

### 7. Rajah 7: Aliran Proses PFD-ICT-AI (Bantuan Pintar)

**File Name:** `D02_Rajah7_Proses_AI.drawio`

**Draw.io Template Prompt:**

```text
Create a detailed AI processing flow diagram:

LAYOUT: Vertical decision tree with parallel processing paths
STYLE: Technical flowchart with AI/ML elements

MAIN FLOW:
START: "Pengguna Taip Soalan" [Circle]

ANALYSIS LAYER:
1. "Terima Input" [Rectangle]
2. "Praproses Teks" [Rectangle]
3. "Analisis Jenis Soalan" [Diamond]

ROUTING DECISION:
"Router AI" [Large diamond in center]
- "Soalan Mudah/FAQ?" → Ollama Path
- "Soalan Kompleks/Analisis?" → Bedrock Path
- "Data Sensitif?" → Local Only

OLLAMA PATH (Left branch):
1. "Ollama Local LLM" [Rectangle with CPU icon]
2. "Cari FAQ Database" [Rectangle]
3. "RAG Pipeline (Local)" [Rectangle]
4. "Jana Jawapan (BM)" [Rectangle]

BEDROCK PATH (Right branch):
1. "AWS Bedrock API" [Rectangle with cloud icon]
2. "Claude Model Selection" [Rectangle]
3. "Advanced Reasoning" [Rectangle]
4. "Generate Response" [Rectangle]

CONVERGENCE:
1. "Format Jawapan" [Rectangle]
2. "Terjemah ke BM" [Rectangle] (if needed)
3. "Streaming Response" [Rectangle]
4. "Log Interaksi" [Rectangle]

END: "Papar Jawapan" [Circle]

FALLBACK MECHANISMS:
- "Ollama Offline?" → Route to Bedrock
- "Bedrock Error?" → Route to FAQ
- "Both Fail?" → Contact Admin

AI MODEL INDICATORS:
- Ollama: Local server icon
- Bedrock: AWS cloud icon
- Model selection: Gear icons

SECURITY ELEMENTS:
- Data classification labels
- Encryption indicators for cloud path
- Local processing emphasis

COLORS:
- Local processing: Green (#4CAF50)
- Cloud processing: Blue (#2196F3)
- Decision points: Orange (#FF9800)
- Security elements: Red (#F44336)

ANNOTATIONS:
- "Cloud Hybrid AI" main label
- "Data Sovereignty" for local path
- "Advanced Reasoning" for cloud path
- Performance metrics (latency, accuracy)
```

---

## B. System Requirements Specification (D03 SRS) Diagrams

### 8. Hybrid Workflow Architecture Diagram

**File Name:** `D03_Hybrid_Workflow_Architecture.drawio`

**Draw.io Template Prompt:**

```text
Create a technical workflow diagram showing the True Hybrid architecture:

LAYOUT: Decision tree with parallel user paths
STYLE: Technical system diagram with authentication flows

ENTRY POINT:
"Pengguna Akses Sistem" [Rectangle at top]

AUTHENTICATION CHECK:
"Auth::check()?" [Large diamond]
- True path (right): "Authenticated User"
- False path (left): "Guest User"

AUTHENTICATED PATH:
1. "Load User Profile" [Rectangle]
2. "Auto-fill Form Data" [Rectangle]
3. "Set user_id = Auth::id()" [Rectangle]
4. "Access Full Dashboard" [Rectangle]
5. "Link to User History" [Rectangle]

GUEST PATH:
1. "Manual Form Entry" [Rectangle]
2. "Set user_id = NULL" [Rectangle]
3. "Generate Session Token" [Rectangle]
4. "Limited Access Mode" [Rectangle]
5. "Email-based Tracking" [Rectangle]

CONVERGENCE:
"Save Submission" [Rectangle]
"Send Email Notification" [Rectangle]
"Generate Reference Number" [Rectangle]

DATABASE REPRESENTATION:
- Show nullable user_id field
- Guest data fields (submitter_name, submitter_email)
- Audit trail connections

TECHNICAL ANNOTATIONS:
- Laravel Breeze authentication
- Livewire form handling
- Database schema indicators
- Session management

COLORS:
- Authenticated flow: Blue (#2196F3)
- Guest flow: Green (#4CAF50)
- Shared components: Purple (#9C27B0)
- Database: Orange (#FF9800)
```

### 9. AI Model Routing Decision Tree

**File Name:** `D03_AI_Model_Routing.drawio`

**Draw.io Template Prompt:**

```text
Create a technical decision tree for AI model selection:

LAYOUT: Binary decision tree with technical details
STYLE: Algorithm flowchart with performance metrics

INPUT:
"User Query Input" [Rectangle at top]

PREPROCESSING:
1. "Text Preprocessing" [Rectangle]
2. "Language Detection" [Rectangle]
3. "Intent Classification" [Rectangle]

DECISION MATRIX:
"Model Selection Router" [Large diamond]

DECISION CRITERIA (Multiple diamonds):
1. "Data Sensitivity?" [Diamond]
   - High → Local Only
   - Low → Either Model

2. "Query Complexity?" [Diamond]
   - Simple/FAQ → Ollama
   - Complex/Analysis → Bedrock

3. "Response Time Priority?" [Diamond]
   - Fast → Ollama (Local)
   - Accuracy → Bedrock (Cloud)

4. "Ollama Available?" [Diamond]
   - Yes → Use Ollama
   - No → Fallback to Bedrock

OLLAMA PROCESSING:
1. "Local LLM Processing" [Rectangle]
2. "FAQ Database Search" [Rectangle]
3. "RAG Pipeline (Local)" [Rectangle]
4. "Response Generation" [Rectangle]
Performance: "~200ms latency"

BEDROCK PROCESSING:
1. "AWS API Call" [Rectangle]
2. "Claude Model Selection" [Rectangle]
3. "Advanced Reasoning" [Rectangle]
4. "Response Generation" [Rectangle]
Performance: "~2-5s latency"

OUTPUT PROCESSING:
1. "Response Formatting" [Rectangle]
2. "Bahasa Melayu Translation" [Rectangle]
3. "Streaming to Client" [Rectangle]
4. "Usage Logging" [Rectangle]

MONITORING:
- Performance metrics boxes
- Cost tracking indicators
- Error handling paths

COLORS:
- Decision points: Yellow (#FFC107)
- Local processing: Green (#4CAF50)
- Cloud processing: Blue (#2196F3)
- Monitoring: Red (#F44336)

TECHNICAL DETAILS:
- API endpoints
- Model versions
- Performance SLAs
- Fallback mechanisms
```

---

## C. Software Design Document (D04 SDD) Diagrams

**Note:** These are anticipated diagrams that should be created for the D04 document

### 10. System Architecture Diagram

**File Name:** `D04_System_Architecture.drawio`

**Draw.io Template Prompt:**

```text
Create a comprehensive system architecture diagram:

LAYOUT: Multi-tier architecture with component details
STYLE: Enterprise system architecture

TIERS (Top to bottom):
1. CLIENT TIER:
   - Web Browsers (Chrome, Firefox, Safari)
   - Mobile Devices (iOS, Android)
   - Network: Intranet/Internet access

2. PRESENTATION TIER:
   - Laravel Blade Templates
   - Livewire Components
   - Alpine.js Frontend
   - Tailwind CSS Styling

3. APPLICATION TIER:
   - Laravel 12 Framework
   - Filament 4 Admin Panel
   - Authentication (Breeze + SSO)
   - Business Logic Services

4. INTEGRATION TIER:
   - Ollama Local AI Server
   - AWS Bedrock API
   - Email Services (SMTP)
   - WebSocket (Reverb)

5. DATA TIER:
   - MySQL Primary Database
   - Redis Cache/Queue
   - File Storage System
   - Backup Systems

SECURITY LAYER (Overlay):
- HTTPS/TLS encryption
- CSRF protection
- Authentication guards
- Authorization policies

MONITORING LAYER (Side panel):
- Laravel Pulse
- Application logs
- Performance metrics
- Error tracking

DEPLOYMENT ENVIRONMENT:
- Docker containers
- Load balancers
- CDN integration
- Backup systems
```

### 11. Database Entity Relationship Diagram

**File Name:** `D04_Database_ERD.drawio`

**Draw.io Template Prompt:**

```text
Create a comprehensive ERD for the ICTServe system:

MAIN ENTITIES:
1. users (Authentication)
2. helpdesk_tickets (Helpdesk module)
3. helpdesk_categories
4. helpdesk_comments
5. loan_applications (Asset loan module)
6. assets
7. loan_transactions
8. bedrock_conversations (AI module)
9. faqs
10. activity_log (Audit)
11. audits (Audit)

RELATIONSHIPS:
- Show all foreign key relationships
- Include nullable relationships for hybrid access
- Polymorphic relationships for auditing

TECHNICAL DETAILS:
- Primary keys (PK)
- Foreign keys (FK)
- Indexes
- Constraints
- Data types for key fields

HYBRID ARCHITECTURE INDICATORS:
- Highlight nullable user_id fields
- Show guest data handling
- Audit trail connections
```

### 12. Component Interaction Diagram

**File Name:** `D04_Component_Interaction.drawio`

**Draw.io Template Prompt:**

```text
Create a component interaction diagram showing how different parts of the system communicate:

COMPONENTS:
1. Frontend Components (Livewire/Volt)
2. Backend Services
3. Database Layer
4. External APIs
5. Queue System
6. Real-time Communication

INTERACTIONS:
- HTTP requests/responses
- WebSocket connections
- Database queries
- API calls
- Queue jobs
- Event broadcasting

PATTERNS:
- Request/Response cycles
- Event-driven architecture
- Observer patterns
- Service layer interactions
```

---

## D. General Guidelines

### Color Scheme Standards

**MOTAC Brand Colors:**

- Primary Blue: #1565C0
- Secondary Green: #4CAF50
- Accent Orange: #FF9800
- Warning Red: #F44336
- Neutral Gray: #757575

**Functional Colors:**

- Success: #4CAF50
- Warning: #FF9800
- Error: #F44336
- Info: #2196F3
- Neutral: #9E9E9E

### Typography Guidelines

**Fonts:**

- Headers: Arial Bold, 14-16pt
- Body text: Arial Regular, 10-12pt
- Technical labels: Courier New, 9-10pt

### Icon Standards

**Use consistent icons from:**

- Material Design Icons
- Feather Icons
- Font Awesome (where appropriate)

### Diagram Conventions

**Shapes:**

- Processes: Rounded rectangles
- Decisions: Diamonds
- Data stores: Cylinders
- External entities: Squares
- Start/End: Circles/Ovals

**Lines:**

- Solid: Direct relationships
- Dashed: Optional/conditional
- Dotted: Notifications/events
- Thick: Primary flow
- Thin: Secondary flow

### Export Settings

**For Documentation:**

- Format: PNG (300 DPI)
- Background: Transparent or white
- Size: Fit to content with 20px margin

**For Presentations:**

- Format: SVG (vector)
- Background: White
- Size: Standard slide dimensions

### File Naming Convention

```text
[Document]_[Diagram_Type]_[Description].drawio

Examples:
- D02_Rajah1_Gambaran_Bisnes.drawio
- D03_Workflow_Hybrid_Architecture.drawio
- D04_ERD_Complete_Database.drawio
```

### Version Control

- Save source .drawio files in `/docs/KRISA/diagrams/`
- Export PNG/SVG to `/public/images/diagrams/`
- Include version numbers in file names for major updates
- Maintain changelog in diagram description

---

## Usage Instructions

1. **Open Draw.io:** Go to <https://app.diagrams.net/>
2. **Create New Diagram:** Choose appropriate template
3. **Follow Template Prompt:** Use the detailed instructions above
4. **Apply Brand Guidelines:** Use MOTAC colors and fonts
5. **Export Files:** Save both .drawio source and PNG/SVG exports
6. **Update Documentation:** Link exported images in respective documents

---

**Document Version:** 1.0  
**Last Updated:** December 17, 2025  
**Created By:** ICTServe Development Team  
**Status:** Active Template
