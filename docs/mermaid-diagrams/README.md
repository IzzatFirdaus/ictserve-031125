# ICTServe System Diagrams

All diagrams extracted from D00-D18 documentation in Mermaid format.

## D02 - Business Requirements Specification

### 1. Stakeholder Relationship Diagram

**File**: `D02-stakeholder-relationship.mmd`
```mermaid
graph TD
    A[PENGURUSAN MOTAC] --> B[BAHAGIAN PENGURUSAN MAKLUMAT<br/>BPM]
    B --> C[UNIT TEKNIKAL ICT]
    B --> D[UNIT ASET ICT]
    C --> E[HELPDESK/SERVICE DESK]
    D --> F[PINJAMAN ASET ICT]
    E --> G[PENGGUNA AKHIR<br/>WARGA MOTAC]
    F --> G
```

### 2. Business Architecture
**File**: `D02-business-architecture.mmd`

### 3. Information Architecture
**File**: `D02-information-architecture.mmd`

### 4. Business Function Hierarchy
**File**: `D02-business-function-hierarchy.mmd`

## D04 - Software Design Document

### 1. System Context Diagram
**File**: `D04-system-context.mmd`
```mermaid
graph TB
    subgraph EXTERNAL["External Systems"]
        SMTP[SMTP Server<br/>Email Notifications]
        S3[S3/MinIO<br/>File Storage]
        SIEM[SIEM System<br/>Audit Streaming]
    end
    
    subgraph USERS["Users"]
        GUEST[Guest Users<br/>Quick Access]
        STAFF[MOTAC Staff<br/>@motac.gov.my]
        ADMIN[Admin/Superuser<br/>BPM ICT Team]
        APPROVER[Approvers<br/>Grade 41+]
    end
    
    subgraph ICTSERVE["ICTServe System v3.6.1"]
        PORTAL[Hybrid Portal<br/>Livewire 3.7.3 + Volt 1.10.1]
        FILAMENT[Admin Panel<br/>Filament 4.3.1]
        BACKEND[Backend Services<br/>Laravel 12.43.1]
        DB[(MySQL 8.0<br/>Database)]
        REDIS[(Redis 7.0<br/>Cache/Queue)]
        REVERB[WebSocket Server<br/>Laravel Reverb 1.6.3]
    end
    
    GUEST --> PORTAL
    STAFF --> PORTAL
    ADMIN --> FILAMENT
    APPROVER --> BACKEND
    
    PORTAL --> BACKEND
    FILAMENT --> BACKEND
    BACKEND --> DB
    BACKEND --> REDIS
    BACKEND --> REVERB
    BACKEND --> SMTP
    BACKEND --> S3
    BACKEND --> SIEM
    
    REVERB --> FILAMENT
```

### 2. Component Diagram
**File**: `D04-component-diagram.mmd`

### 3. Deployment Architecture
**File**: `D04-deployment-architecture.mmd`

### 4. Guest User Journey
**File**: `D04-guest-flow.mmd`

### 5. Approval Workflow
**File**: `D04-approval-workflow.mmd`

## D11 - Technical Design Documentation

### 5. System Layers
**File**: `D11-system-layers.mmd`
```mermaid
graph TB
    PRES[PRESENTATION LAYER<br/>Blade + Livewire 3 + Volt<br/>Filament 4 + Tailwind 4]
    APP[APPLICATION LAYER<br/>Controllers + Services<br/>Jobs + Middleware + Policies]
    INT[INTEGRATION LAYER<br/>RESTful API + WebSocket Reverb<br/>Email + Audit Trail]
    DATA[DATA LAYER<br/>Eloquent ORM + MySQL<br/>Redis Cache + File Storage]
    
    PRES --> APP
    APP --> INT
    INT --> DATA
```

### 6. AI Fallback Chain
**File**: `D11-fallback-chain.mmd`

### 7. Deployment Architecture
**File**: `D11-deployment-architecture.mmd`

## D12 - UI/UX Design Guide

### 8. Layout Hierarchy (Guest/Auth/Admin)
**File**: `D12-layout-hierarchy.mmd`

## D16 - Broadcasting Setup

### 9. Broadcasting Workflow
**File**: `D16-broadcasting-workflow.mmd`

## D18 - AI Chatbot (Ollama + Bedrock)

### 10. System Architecture
**File**: `D18-system-architecture.mmd`

### 11. Service Layer Architecture
**File**: `D18-service-layer-architecture.mmd`

### 12. Cost Optimization Flow
**File**: `D18-cost-optimization-flow.mmd`

## Usage

### Viewing Diagrams

1. **VS Code**: Install "Markdown Preview Mermaid Support" extension
2. **GitHub**: Renders automatically in markdown files
3. **Online**: Use https://mermaid.live/ to view/edit

### Embedding in Documentation

```markdown
```mermaid
graph TD
    A[Start] --> B[End]
```
```

## File Locations

All diagram files are stored in `docs/_reference/` with `.mmd` extension:

- D02-stakeholder-relationship.mmd
- D02-business-architecture.mmd
- D02-information-architecture.mmd
- D02-business-function-hierarchy.mmd
- D04-system-context.mmd
- D04-component-diagram.mmd
- D04-deployment-architecture.mmd
- D04-guest-flow.mmd
- D04-approval-workflow.mmd
- D11-system-layers.mmd
- D11-fallback-chain.mmd
- D11-deployment-architecture.mmd
- D12-layout-hierarchy.mmd
- D16-broadcasting-workflow.mmd
- D18-system-architecture.mmd
- D18-service-layer-architecture.mmd
- D18-cost-optimization-flow.mmd

## Standards

- **Format**: Mermaid (`.mmd`)
- **Naming**: `D##-description.mmd`
- **Language**: Technical terms in English, labels in Bahasa Melayu where appropriate
- **Version**: Aligned with D00-D18 v3.6.1
