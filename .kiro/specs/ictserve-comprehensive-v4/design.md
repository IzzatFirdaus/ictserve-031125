# Design Document

## Overview

The ICTServe System v4.0 is designed as a comprehensive, integrated digital platform for managing ICT services within MOTAC BPM using a **PKS-Compliant SSO-Only Architecture** with **mandatory LDAP/Active Directory authentication** for ALL users, ensuring full accountability per PKS 5.2.1. The system provides intranet-only deployment with mandatory SSO authentication, eliminating guest access entirely. The system includes **Cloud Hybrid AI Architecture** integrating Ollama (local LLM for sensitive data per PKS 4.2) with AWS Bedrock (Claude models for public data only after DLP filtering per PKS 9.2.1) for intelligent assistance.

**Critical Design Principle v4.0 (PKS Compliant)**: The system operates on a **PKS-Compliant SSO-Only Architecture with Bahasa Melayu exclusive interface and Cloud Hybrid AI with DLP**:

1. **Walk-in/Kiosk Mode (SSO Required)**: Kiosk terminals for quick helpdesk tickets and asset loan applications using SSO LDAP/Active Directory authentication - **NO GUEST ACCESS** per PKS 5.2.1
2. **Authenticated Portal (SSO Required)**: Internal portal for staff to view submission history, manage profiles, add comments, track status, and access **enhanced AI features** including document analysis, conversation management, and personalized responses
3. **Admin Access (Filament Panel)**: Backend management accessible to four roles: staff (own submissions), approver (Grade 41+ approval rights), admin (operational management), and superuser (full governance), with **AI configuration and monitoring** capabilities

The design emphasizes **Bahasa Melayu exclusive UI** (language switcher disabled), **mandatory SSO authentication** for all access paths, **HRMIS auto-provisioning** replacing manual registration, email-based approvals for Grade 41+ officers with HRMIS verification, unified frontend components, seamless module integration, modern web technologies (Laravel 12.43.1, Livewire 3.7.3, Volt 1.10.1, Filament 4.3.1, Laravel Breeze 2.3.8), responsive UI/UX following **WCAG 2.2 Level AA standards**, **Core Web Vitals performance targets**, dual audit system with mandatory user_id linkage, comprehensive monitoring with Laravel Pulse 1.4.6 and Telescope 5.16.0, and **Cloud Hybrid AI integration** (Ollama + AWS Bedrock with DLP) per D18 v1.0.1 and PKS compliance.

**PKS Compliance Design Principles**:

- **PKS 5.2.1 (Accountability)**: All database tables with user interactions have mandatory user_id FK (NOT NULL) - no nullable user references
- **PKS 9.2.1 (Data Transfer)**: DLP filtering service mandatory before any cloud AI (Bedrock) processing
- **PKS 4.2 (Data Sovereignty)**: Sensitive data processed locally via Ollama only, intranet deployment
- **PKS 5.4.3 (Password Policy)**: Enforced via LDAP/Active Directory integration (8 chars, 90-day expiry, 3 attempts)
- **PKS CSIRT Integration**: Automated incident detection, NACSA/MyCERT reporting, 15-minute escalation SLA
- **PKS BCP/DRP**: RTO 4 hours, RPO 24 hours, automated failover, DR site replication
- **PKS Security Training**: Mandatory training tracking, access restrictions for non-compliant users
- **PKS Change Management**: Approval workflows, risk assessment, rollback procedures, audit trails
- **PKS Third-Party Security**: Time-limited access, NDA requirements, enhanced audit logging
- **PSPM Alignment**: Digital service integration across all four strategic pillars

## Architecture

### System Architecture Overview

```mermaid
graph TB
    subgraph "Walk-in/Kiosk Interface Layer - SSO REQUIRED (Bahasa Melayu Sahaja)"
        KioskForms[Walk-in/Kiosk Forms - SSO Authentication Required]
        ResponsiveUI[Responsive Mobile/Desktop Interface - WCAG 2.2 AA]
        ComponentLibrary[Unified Component Library - Compliant Colors]
        EmailLinks[Email-Based Approval Links - HRMIS Verification Required]
    end

    subgraph "Authenticated Interface Layer - STAFF PORTAL (Bahasa Melayu Sahaja)"
        StaffPortal[Authenticated Staff Portal - Laravel Breeze + SSO]
        HRMISProvisioning[HRMIS Auto-Provisioning - Replaces Manual Registration]
        StaffAuth[Staff Authentication via LDAP/Active Directory]
        StaffDashboard[Staff Dashboard - Submission History & Tracking]
        PortalApproval[Portal-Based Approval - Grade 41+ Officers with HRMIS Verification]
        MandatorySSO[Mandatory SSO - NO Alternative Authentication]
    end

    subgraph "Admin Interface Layer - FILAMENT PANEL (Bahasa Melayu Sahaja)"
        FilamentAdmin[Filament Admin Panel - 4 Roles: Staff/Approver/Admin/Superuser]
        AdminAuth[Role-Based Access Control - RBAC with SSO]
        AdminDashboard[Unified Admin Dashboard - WCAG 2.2 AA]
        LaravelPulse[Laravel Pulse - Performance Monitoring]
        LaravelTelescope[Laravel Telescope - System Debugging]
    end

    subgraph "Application Layer"
        Helpdesk[Enhanced Helpdesk Module - SSO Only + Admin]
        AssetLoan[Enhanced Asset Loan Module - SSO Only + Admin]
        Integration[Module Integration Layer]
        EmailWorkflow[Email-Based Workflow Engine - HRMIS Verification]
        ComponentSystem[Livewire/Volt Component System - Performance Optimized]
        RealtimeComm[Laravel Reverb + Echo - Real-time Communication - Authenticated Only]
    end

    subgraph "PKS Compliance Layer"
        DLPFilter[DLP Filtering Service - PKS 9.2.1]
        DataClassifier[Data Classification Engine - Sensitive vs Public]
        AuditEnforcer[Mandatory User ID Enforcer - PKS 5.2.1]
        DataSovereignty[Data Sovereignty Controller - PKS 4.2]
        CSIRTIntegration[CSIRT Integration - Incident Response]
        BCPDRPController[BCP/DRP Controller - Business Continuity]
        TrainingCompliance[Security Training Compliance Tracker]
        ChangeManagement[Change Management Workflow Engine]
        ThirdPartyAccess[Third-Party Access Controller]
    end

    subgraph "Business Logic Layer"
        SSODataMgmt[SSO-Only Data Management - Mandatory User Linkage]
        AssetMgmt[Enhanced Asset Management - Multi-Role Access]
        TicketMgmt[Enhanced Ticket Management - Multi-Role Access]
        HRMISApprovalFlow[HRMIS-Verified Approval Workflows - Email + Portal]
        ReportEngine[Enhanced Reporting Engine - Role-Based Access]
        DualAuditEngine[Dual Audit Engine - owen-it + spatie with Mandatory user_id]
        APILayer[Laravel Sanctum - API Authentication]
    end

    subgraph "Data Layer"
        MySQL[(MySQL Database - user_id NOT NULL)]
        Redis[(Redis Cache & Sessions)]
        FileStorage[File Storage]
        AuditDB[(Dual Audit Database - 7 Year Retention)]
    end

    subgraph "External Systems"
        EmailSMTP[Email Server - Primary Communication Channel]
        HRMIS[HRMIS Integration - Auto-Provisioning & Verification]
        LDAP[LDAP/Active Directory - Mandatory SSO]
        CSIRTMOTAC[CSIRT MOTAC - Incident Response Coordination]
        NACSA[NACSA/MyCERT - National Incident Reporting]
        TrainingSystem[MOTAC Training Management System]
        DRSite[Disaster Recovery Site - Secondary Location]
    end

    subgraph "AI Layer - PKS Compliant"
        OllamaLocal[Ollama Local LLM - Sensitive Data Only - PKS 4.2]
        BedrockCloud[AWS Bedrock - Public Data Only After DLP - PKS 9.2.1]
        ModelRouter[Smart Model Router with DLP Check]
    end

    KioskForms --> ComponentLibrary
    ResponsiveUI --> ComponentLibrary
    ComponentLibrary --> ComponentSystem
    EmailLinks --> HRMISApprovalFlow

    StaffPortal --> HRMISProvisioning
    HRMISProvisioning --> StaffAuth
    StaffAuth --> StaffDashboard
    StaffDashboard --> Integration
    PortalApproval --> HRMISApprovalFlow
    MandatorySSO --> StaffAuth

    FilamentAdmin --> AdminAuth
    AdminAuth --> AdminDashboard
    AdminDashboard --> Integration
    LaravelPulse --> AdminDashboard
    LaravelTelescope --> AdminDashboard

    KioskForms --> Helpdesk
    KioskForms --> AssetLoan
    StaffPortal --> Helpdesk
    StaffPortal --> AssetLoan
    ComponentSystem --> Helpdesk
    ComponentSystem --> AssetLoan
    RealtimeComm --> Helpdesk
    RealtimeComm --> AssetLoan

    Helpdesk --> SSODataMgmt
    AssetLoan --> AssetMgmt
    Integration --> TicketMgmt
    Integration --> HRMISApprovalFlow
    EmailWorkflow --> HRMISApprovalFlow

    SSODataMgmt --> AuditEnforcer
    AuditEnforcer --> MySQL
    AssetMgmt --> MySQL
    TicketMgmt --> MySQL
    HRMISApprovalFlow --> Redis
    ReportEngine --> MySQL
    DualAuditEngine --> AuditDB
    APILayer --> MySQL

    EmailWorkflow --> EmailSMTP
    SSODataMgmt --> HRMIS
    HRMISApprovalFlow --> EmailSMTP
    StaffAuth --> LDAP
    RealtimeComm --> Redis

    ModelRouter --> DLPFilter
    DLPFilter --> DataClassifier
    DataClassifier --> OllamaLocal
    DataClassifier --> BedrockCloud
    DataSovereignty --> OllamaLocal
```

### Technology Stack v4.0 (PKS Compliant)

| Layer | Technology | Version | Purpose |
|-------|------------|---------|---------|
| **Backend Framework** | Laravel | 12.43.1 | Core application framework |
| **Language** | PHP | 8.4.1 | Server-side programming |
| **Authentication** | Laravel Breeze + LDAP | 2.3.8 | Mandatory SSO authentication per PKS 5.2.1 |
| **Frontend Framework** | Livewire | 3.7.3 | Dynamic UI components |
| **Single-File Components** | Volt | 1.10.1 | Simplified component development |
| **Admin Panel** | Filament | 4.3.1 | Administrative interface (4 roles) |
| **Templating** | Blade | - | Server-side templating |
| **CSS Framework** | Tailwind CSS | 4.1.18 | Utility-first styling with compliant colors |
| **Build Tool** | Vite | 7.0.7 | Asset compilation and optimization |
| **Database** | MySQL | 8.0+ | Primary data storage with mandatory user_id FK |
| **Cache/Sessions** | Redis | 7.0+ | Caching and session management |
| **Queue System** | Laravel Horizon | 5.x | Redis queue dashboard and monitoring |
| **Performance Monitoring** | Laravel Pulse | 1.4.6 | Real-time performance metrics |
| **System Debugging** | Laravel Telescope | 5.16.0 | System debugging (superuser only) |
| **API Authentication** | Laravel Sanctum | 4.2.1 | API token authentication |
| **SSO Integration** | LdapRecord-Laravel | 3.x | LDAP/Active Directory SSO per PKS 5.2.1 |
| **Real-time Communication** | Laravel Reverb | 1.6.3 | WebSocket server (authenticated channels only) |
| **WebSocket Client** | Laravel Echo | 2.2.6 | Client-side WebSocket communication |
| **Compliance Audit** | owen-it/laravel-auditing | 14.x | Field-level audit tracking with mandatory user_id |
| **Operational Logging** | spatie/laravel-activitylog | 4.x | User activity logging with mandatory user_id |
| **Local AI (LLM)** | Ollama | Latest | Local LLM server for sensitive data (PKS 4.2) |
| **Cloud AI** | AWS Bedrock | - | Claude models for public data only (PKS 9.2.1) |
| **DLP Filtering** | Custom Service | - | Data Loss Prevention per PKS 9.2.1 |
| **MCP Server** | Laravel MCP | 0.3.4 | Model Context Protocol for AI assistants |
| **Testing** | PHPUnit | 11.5.46 | Unit and feature testing |
| **Static Analysis** | Larastan | 3.8.1 | PHPStan Level 9 analysis |
| **Code Formatting** | Laravel Pint | 1.26.0 | PSR-12 compliance |

### Design Patterns

- **PKS-Compliant SSO-Only Pattern**: Mandatory SSO authentication for ALL users, eliminating guest access per PKS 5.2.1
- **Mandatory User Linkage Pattern**: All database records with user interactions have user_id as NOT NULL FK
- **HRMIS Auto-Provisioning Pattern**: Automatic user account creation synchronized with HR System
- **DLP-First AI Pattern**: All cloud AI requests pass through DLP filtering before transmission per PKS 9.2.1
- **Data Sovereignty Pattern**: Sensitive data routed to local Ollama only per PKS 4.2
- **Bahasa Melayu Exclusive UI**: Single language interface with disabled language switcher
- **Email-First Communication**: Primary interaction through automated email workflows with HRMIS verification
- **HRMIS-Verified Approval Pattern**: Approver identity verification via HRMIS before processing
- **Dual Audit Pattern**: Simultaneous compliance auditing and operational logging with mandatory user_id
- **Cloud Hybrid AI Pattern**: Smart routing between local Ollama (sensitive) and cloud Bedrock (public after DLP)
- **MVC (Model-View-Controller)**: Laravel's core architectural pattern
- **Repository Pattern**: Data access abstraction for testability
- **Service Layer Pattern**: Business logic encapsulation
- **Observer Pattern**: Event-driven notifications and audit logging
- **Strategy Pattern**: Configurable approval workflows via email and portal
- **Factory Pattern**: Model factories for testing and seeding
- **Role-Based Access Control (RBAC)**: Four-tier role system (staff, approver, admin, superuser)
- **Queue Supervisor Pattern**: Laravel Horizon for Redis queue management with auto-scaling workers

## Components and Interfaces

### Core Components v4.0 (PKS Compliant)

#### 1. PKS-Compliant Authentication Component with HRMIS Auto-Provisioning

```php
// PKS 5.2.1 COMPLIANT ARCHITECTURE - Four-Role RBAC System with HRMIS Auto-Provisioning
// Roles: staff (portal access), approver (Grade 41+ approval), admin (operational), superuser (governance)
// NO GUEST ACCESS - All users MUST authenticate via LDAP/Active Directory

class User extends Authenticatable
{
    use Auditable, LogsActivity;

    protected $fillable = [
        'name', 'email', 'username', 'password', 'role', 'staff_id', 
        'grade_id', 'division_id', 'position_id', 'email_verified_at',
        'hrmis_synced_at', 'ldap_guid', 'is_active'
    ];

    // Four roles in the PKS-compliant SSO-only system
    public function isStaff(): bool
    {
        return $this->role === 'staff';
    }

    public function isApprover(): bool
    {
        return $this->role === 'approver';
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isSuperuser(): bool
    {
        return $this->role === 'superuser';
    }

    // Enhanced access control
    public function canApprove(): bool
    {
        return in_array($this->role, ['approver', 'admin', 'superuser']);
    }

    public function hasAdminAccess(): bool
    {
        return in_array($this->role, ['admin', 'superuser']);
    }

    public function canAccessTelescope(): bool
    {
        return $this->role === 'superuser';
    }

    public function canAccessPulse(): bool
    {
        return in_array($this->role, ['admin', 'superuser']);
    }

    // HRMIS Auto-Provisioning - replaces manual registration
    public function isHRMISSynced(): bool
    {
        return $this->hrmis_synced_at !== null;
    }

    // LDAP/Active Directory validation
    public function isLDAPAuthenticated(): bool
    {
        return $this->ldap_guid !== null;
    }

    // Relationships for authenticated users
    public function division(): BelongsTo
    {
        return $this->belongsTo(Division::class);
    }

    public function grade(): BelongsTo
    {
        return $this->belongsTo(Grade::class);
    }

    public function position(): BelongsTo
    {
        return $this->belongsTo(Position::class);
    }

    public function helpdeskTickets(): HasMany
    {
        return $this->hasMany(HelpdeskTicket::class);
    }

    public function loanApplications(): HasMany
    {
        return $this->hasMany(LoanApplication::class);
    }

    // NO guest submission linking - all submissions require authenticated user_id
}
```

#### 2. PKS-Compliant Helpdesk Module Component

```php
// PKS 5.2.1 Compliant Helpdesk Ticket Model - SSO-Only, Mandatory user_id
class HelpdeskTicket extends Model
{
    use Auditable, LogsActivity;

    protected $fillable = [
        'ticket_number', 
        'user_id', // MANDATORY - NOT NULL per PKS 5.2.1
        'staff_id', 'division_id', 'category_id', 'priority', 'subject',
        'description', 'status', 'assigned_to_division', 'assigned_to_agency',
        'assigned_to_user', 'asset_id', 'admin_notes', 'sla_due_at', 
        'escalated_at', 'resolved_at', 'closed_at'
    ];

    // REMOVED: guest_name, guest_email, guest_phone - NO GUEST ACCESS per PKS 5.2.1

    protected $casts = [
        'created_at' => 'datetime',
        'sla_due_at' => 'datetime',
        'escalated_at' => 'datetime',
        'resolved_at' => 'datetime',
        'closed_at' => 'datetime',
        'assigned_at' => 'datetime',
    ];

    // Enhanced status constants
    const STATUS_OPEN = 'open';
    const STATUS_ASSIGNED = 'assigned';
    const STATUS_IN_PROGRESS = 'in_progress';
    const STATUS_PENDING_USER = 'pending_user';
    const STATUS_RESOLVED = 'resolved';
    const STATUS_CLOSED = 'closed';
    const STATUS_ESCALATED = 'escalated';

    // Priority constants
    const PRIORITY_LOW = 'low';
    const PRIORITY_MEDIUM = 'medium';
    const PRIORITY_HIGH = 'high';
    const PRIORITY_CRITICAL = 'critical';

    // MANDATORY user relationship - NOT NULL per PKS 5.2.1
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function division(): BelongsTo
    {
        return $this->belongsTo(Division::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(TicketCategory::class);
    }

    public function asset(): BelongsTo
    {
        return $this->belongsTo(Asset::class);
    }

    public function assignedDivision(): BelongsTo
    {
        return $this->belongsTo(Division::class, 'assigned_to_division');
    }

    public function assignedUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to_user');
    }

    public function comments(): HasMany
    {
        return $this->hasMany(HelpdeskComment::class);
    }

    public function attachments(): HasMany
    {
        return $this->hasMany(HelpdeskAttachment::class);
    }

    // REMOVED: isGuestSubmission() - NO GUEST SUBMISSIONS per PKS 5.2.1

    public function getSubmitterNameAttribute(): string
    {
        return $this->user->name; // Always from authenticated user
    }

    public function getSubmitterEmailAttribute(): string
    {
        return $this->user->email; // Always from authenticated user
    }

    public function generateTicketNumber(): string
    {
        return 'HD' . date('Y') . str_pad($this->id, 6, '0', STR_PAD_LEFT);
    }

    // SLA management
    public function calculateSLADueDate(): Carbon
    {
        $hours = match($this->priority) {
            self::PRIORITY_CRITICAL => 4,
            self::PRIORITY_HIGH => 8,
            self::PRIORITY_MEDIUM => 24,
            self::PRIORITY_LOW => 72,
            default => 24
        };

        return $this->created_at->addHours($hours);
    }

    public function isSLABreached(): bool
    {
        return $this->sla_due_at && now()->isAfter($this->sla_due_at) && 
               !in_array($this->status, [self::STATUS_RESOLVED, self::STATUS_CLOSED]);
    }

    public function getSLAStatusAttribute(): string
    {
        if (in_array($this->status, [self::STATUS_RESOLVED, self::STATUS_CLOSED])) {
            return 'met';
        }

        if ($this->isSLABreached()) {
            return 'breached';
        }

        $timeRemaining = now()->diffInHours($this->sla_due_at, false);
        $totalTime = $this->created_at->diffInHours($this->sla_due_at);
        $percentageRemaining = ($timeRemaining / $totalTime) * 100;

        if ($percentageRemaining <= 25) {
            return 'warning';
        }

        return 'on_track';
    }
}
```

#### 3. PKS-Compliant Asset Loan Module Component

```php
// PKS 5.2.1 Compliant Loan Application Model - SSO-Only, Mandatory user_id, HRMIS-Verified Approval
class LoanApplication extends Model
{
    use Auditable, LogsActivity;

    protected $fillable = [
        'application_number', 
        'user_id', // MANDATORY - NOT NULL per PKS 5.2.1
        'staff_id', 'grade_id', 'division_id', 'position_id', 'asset_id', 'purpose',
        'start_date', 'end_date', 'status', 'approver_id', 'approver_name', 'approver_email',
        'approval_token', 'token_expires_at', 'approval_remarks', 'approval_method',
        'approved_at', 'rejected_at', 'extended_until', 'returned_at', 'return_condition',
        'hrmis_verified_at' // HRMIS verification timestamp for approver
    ];

    // REMOVED: applicant_name, applicant_email, applicant_phone - NO GUEST ACCESS per PKS 5.2.1

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'approved_at' => 'datetime',
        'rejected_at' => 'datetime',
        'token_expires_at' => 'datetime',
        'returned_at' => 'datetime',
        'hrmis_verified_at' => 'datetime',
    ];

    // Enhanced status constants
    const STATUS_PENDING_APPROVAL = 'pending_approval';
    const STATUS_APPROVED = 'approved';
    const STATUS_REJECTED = 'rejected';
    const STATUS_ACTIVE = 'active';
    const STATUS_OVERDUE = 'overdue';
    const STATUS_RETURNED = 'returned';
    const STATUS_EXTENDED = 'extended';

    // Approval method constants
    const APPROVAL_METHOD_EMAIL = 'email';
    const APPROVAL_METHOD_PORTAL = 'portal';

    // Return condition constants
    const CONDITION_GOOD = 'good';
    const CONDITION_FAIR = 'fair';
    const CONDITION_DAMAGED = 'damaged';
    const CONDITION_FAULTY = 'faulty';

    // MANDATORY user relationship - NOT NULL per PKS 5.2.1
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approver_id');
    }

    public function division(): BelongsTo
    {
        return $this->belongsTo(Division::class);
    }

    public function grade(): BelongsTo
    {
        return $this->belongsTo(Grade::class);
    }

    public function position(): BelongsTo
    {
        return $this->belongsTo(Position::class);
    }

    public function asset(): BelongsTo
    {
        return $this->belongsTo(Asset::class);
    }

    public function transaction(): HasOne
    {
        return $this->hasOne(AssetTransaction::class);
    }

    // REMOVED: isGuestSubmission() - NO GUEST SUBMISSIONS per PKS 5.2.1

    public function getApplicantNameAttribute(): string
    {
        return $this->user->name; // Always from authenticated user
    }

    public function getApplicantEmailAttribute(): string
    {
        return $this->user->email; // Always from authenticated user
    }

    // Enhanced approval token management with HRMIS verification
    public function generateApprovalToken(): string
    {
        $this->approval_token = Str::random(64);
        $this->token_expires_at = now()->addDays(7);
        $this->save();

        return $this->approval_token;
    }

    public function isTokenValid(string $token): bool
    {
        return $this->approval_token === $token
            && $this->token_expires_at > now()
            && $this->status === self::STATUS_PENDING_APPROVAL;
    }

    // HRMIS verification check
    public function isHRMISVerified(): bool
    {
        return $this->hrmis_verified_at !== null;
    }

    public function generateApplicationNumber(): string
    {
        return 'AL' . date('Y') . str_pad($this->id, 6, '0', STR_PAD_LEFT);
    }

    // Enhanced status management
    public function isOverdue(): bool
    {
        return $this->status === self::STATUS_ACTIVE && 
               $this->end_date < now()->toDateString();
    }

    public function canBeExtended(): bool
    {
        return in_array($this->status, [self::STATUS_ACTIVE, self::STATUS_EXTENDED]) &&
               !$this->isOverdue();
    }

    public function requiresMaintenanceTicket(): bool
    {
        return $this->status === self::STATUS_RETURNED &&
               in_array($this->return_condition, [self::CONDITION_DAMAGED, self::CONDITION_FAULTY]);
    }
}
```

### Enhanced Frontend Component Architecture v3.6.0

#### 1. Bahasa Melayu Exclusive Component Library Structure

```text
resources/views/components/
├── layout/
│   ├── guest.blade.php        # Guest layout for public forms (Bahasa Melayu sahaja)
│   ├── app.blade.php          # Authenticated layout for staff portal (Bahasa Melayu sahaja)
│   ├── header.blade.php       # Site header with MOTAC branding
│   ├── auth-header.blade.php  # Authenticated header with user menu
│   └── footer.blade.php       # Site footer with accessibility links
├── form/
│   ├── input.blade.php        # WCAG compliant input with proper labels
│   ├── select.blade.php       # Accessible select with ARIA attributes
│   ├── textarea.blade.php     # Accessible textarea component
│   ├── checkbox.blade.php     # WCAG compliant checkbox
│   └── file-upload.blade.php  # Accessible file upload component
├── ui/
│   ├── button.blade.php       # 44×44px minimum touch targets
│   ├── card.blade.php         # Accessible card container
│   ├── alert.blade.php        # ARIA live region alerts
│   ├── badge.blade.php        # Accessible status badges
│   └── modal.blade.php        # Focus trap modal dialogs
├── navigation/
│   ├── breadcrumbs.blade.php  # Accessible breadcrumb navigation
│   ├── pagination.blade.php   # WCAG compliant pagination
│   └── skip-links.blade.php   # Skip navigation for keyboard users
├── data/
│   ├── table.blade.php        # Accessible data tables
│   ├── status-badge.blade.php # Color + icon + text status indicators
│   └── progress-bar.blade.php # Accessible progress indicators
├── accessibility/
│   ├── aria-live.blade.php    # Screen reader announcements
│   ├── focus-trap.blade.php   # Focus management
│   └── language-disabled.blade.php # Disabled language switcher (v3.6.0)
└── monitoring/
    ├── pulse-widget.blade.php # Laravel Pulse performance widgets
    └── telescope-link.blade.php # Laravel Telescope access (superuser only)
```

#### 2. Enhanced Guest Form Components (Bahasa Melayu Sahaja)

```blade
<!-- Enhanced Guest Helpdesk Ticket Form Component - WCAG 2.2 AA Compliant -->
<div class="max-w-4xl mx-auto p-6 bg-white rounded-lg shadow-lg">
    <x-navigation.skip-links />

    <header class="mb-8" role="banner">
        <h1 class="text-3xl font-bold text-gray-900 mb-2">
            Hantar Tiket Helpdesk
        </h1>
        <p class="text-gray-600">
            Laporkan isu teknikal atau minta sokongan ICT - Tiada log masuk diperlukan
        </p>
    </header>

    <main role="main">
        <form wire:submit.prevent="submitTicket" class="space-y-6" novalidate>
            <!-- Real-time validation with ARIA announcements -->
            <div wire:loading.class="opacity-50" wire:target="submitTicket">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <x-form.input
                        name="name"
                        label="Nama Penuh"
                        wire:model.live.debounce.300ms="form.name"
                        required
                        aria-describedby="name-help"
                        autocomplete="name" />

                    <x-form.input
                        name="email"
                        type="email"
                        label="Alamat E-mel"
                        wire:model.live.debounce.300ms="form.email"
                        required
                        aria-describedby="email-help"
                        autocomplete="email" />
                </div>

                <x-form.select
                    name="category_id"
                    label="Kategori Isu"
                    wire:model.live="form.category_id"
                    :options="$categories"
                    required
                    aria-describedby="category-help" />

                <x-form.select
                    name="priority"
                    label="Keutamaan"
                    wire:model.live="form.priority"
                    :options="$priorities"
                    required
                    aria-describedby="priority-help" />

                <x-form.textarea
                    name="description"
                    label="Penerangan Masalah"
                    wire:model.live.debounce.500ms="form.description"
                    rows="5"
                    required
                    aria-describedby="description-help"
                    minlength="10"
                    maxlength="5000" />

                <x-form.file-upload
                    name="attachments"
                    label="Lampiran (Pilihan)"
                    wire:model="form.attachments"
                    multiple
                    accept="image/*,.pdf,.doc,.docx"
                    aria-describedby="attachments-help" />
            </div>

            <div class="flex justify-end space-x-4" role="group" aria-label="Tindakan borang">
                <x-ui.button
                    type="button"
                    variant="secondary"
                    wire:click="clearForm">
                    Kosongkan Borang
                </x-ui.button>

                <x-ui.button
                    type="submit"
                    variant="primary"
                    wire:loading.attr="disabled"
                    aria-describedby="submit-help">
                    <span wire:loading.remove>Hantar Tiket</span>
                    <span wire:loading aria-live="polite">Menghantar...</span>
                </x-ui.button>
            </div>
        </form>
    </main>

    <!-- Enhanced ARIA Live Region for Screen Reader Announcements -->
    <div aria-live="polite" aria-atomic="true" class="sr-only" id="form-announcements">
        <span wire:loading wire:target="submitTicket">Sedang menghantar tiket...</span>
        @if (session('success'))
            <span>{{ session('success') }}</span>
        @endif
    </div>

    <!-- Real-time validation feedback -->
    @if ($errors->any())
        <x-ui.alert type="error" class="mt-4">
            <h3>Sila betulkan ralat berikut:</h3>
            <ul class="list-disc list-inside mt-2">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </x-ui.alert>
    @endif
</div>
```

### Enhanced Dual Approval Workflow v3.6.0

#### 1. Enhanced Dual Approval Service with Performance Monitoring

```php
class EnhancedDualApprovalService
{
    public function __construct(
        private PerformanceMonitoringService $monitor,
        private AuditService $audit
    ) {}

    public function sendApprovalRequest(LoanApplication $application): void
    {
        $this->monitor->startTimer('approval_request_processing');

        try {
            // Generate secure approval token for email-based approval
            $token = $application->generateApprovalToken();

            // Determine approver based on applicant grade and asset value
            $approver = ApprovalMatrix::getApproverByGrade($application->grade_id, $application->asset);

            // Try to find approver user account for portal-based approval
            $approverUser = User::where('email', $approver['email'])
                ->where('role', 'approver')
                ->first();

            // Update application with approver details
            $application->update([
                'approver_id' => $approverUser?->id,
                'approver_name' => $approver['name'],
                'approver_email' => $approver['email'],
            ]);

            // Send email with DUAL approval options:
            // 1. Email-based approval links (no login required)
            // 2. Portal-based approval link (login required)
            Mail::to($approver['email'])->send(new EnhancedLoanApprovalRequest($application, $token));

            // Real-time notification via Laravel Reverb
            if ($approverUser) {
                broadcast(new LoanApprovalRequested($application, $approverUser));
            }

            // Dual audit logging
            $this->audit->logApprovalRequest($application, $approver, [
                'approval_methods' => ['email', 'portal'],
                'token_expires_at' => $application->token_expires_at,
                'approver_found' => $approverUser !== null,
            ]);

        } finally {
            $this->monitor->endTimer('approval_request_processing');
        }
    }

    // Enhanced email-based approval (no login required)
    public function processEmailApproval(string $token, bool $approved, ?string $remarks = null): array
    {
        $this->monitor->startTimer('email_approval_processing');

        try {
            $application = LoanApplication::where('approval_token', $token)->firstOrFail();

            if (!$application->isTokenValid($token)) {
                return [
                    'success' => false,
                    'message' => 'Pautan kelulusan ini telah tamat tempoh atau tidak sah.',
                ];
            }

            $application->update([
                'status' => $approved ? LoanApplication::STATUS_APPROVED : LoanApplication::STATUS_REJECTED,
                'approved_at' => now(),
                'approval_remarks' => $remarks,
                'approval_token' => null, // Invalidate token
                'approval_method' => LoanApplication::APPROVAL_METHOD_EMAIL,
            ]);

            $this->sendApprovalNotifications($application, $approved);
            $this->audit->logApprovalDecision($application, $approved, 'email');

            // Real-time notification
            broadcast(new LoanApplicationDecisionMade($application));

            return [
                'success' => true,
                'message' => $approved
                    ? 'Permohonan diluluskan berjaya melalui e-mel.'
                    : 'Permohonan ditolak berjaya melalui e-mel.',
            ];

        } finally {
            $this->monitor->endTimer('email_approval_processing');
        }
    }

    // Enhanced portal-based approval (login required)
    public function processPortalApproval(LoanApplication $application, User $approver, bool $approved, ?string $remarks = null): array
    {
        $this->monitor->startTimer('portal_approval_processing');

        try {
            // Verify approver has permission
            if (!$approver->canApprove()) {
                return [
                    'success' => false,
                    'message' => 'Anda tidak mempunyai kebenaran untuk meluluskan permohonan.',
                ];
            }

            $application->update([
                'status' => $approved ? LoanApplication::STATUS_APPROVED : LoanApplication::STATUS_REJECTED,
                'approved_at' => now(),
                'approval_remarks' => $remarks,
                'approver_id' => $approver->id,
                'approval_token' => null, // Invalidate email token
                'approval_method' => LoanApplication::APPROVAL_METHOD_PORTAL,
            ]);

            $this->sendApprovalNotifications($application, $approved);
            $this->audit->logApprovalDecision($application, $approved, 'portal', $approver);

            // Real-time notification
            broadcast(new LoanApplicationDecisionMade($application));

            return [
                'success' => true,
                'message' => $approved
                    ? 'Permohonan diluluskan berjaya melalui portal.'
                    : 'Permohonan ditolak berjaya melalui portal.',
            ];

        } finally {
            $this->monitor->endTimer('portal_approval_processing');
        }
    }

    private function sendApprovalNotifications(LoanApplication $application, bool $approved): void
    {
        // Send confirmation to applicant (guest or authenticated)
        $applicantEmail = $application->user ? $application->user->email : $application->applicant_email;
        Mail::to($applicantEmail)->send(new EnhancedLoanApplicationDecision($application, $approved));

        // Send confirmation to approver
        Mail::to($application->approver_email)->send(new ApprovalConfirmation($application, $approved));

        // Real-time notification to authenticated applicant
        if ($application->user) {
            $application->user->notify(new LoanDecisionNotification($application, $approved));
        }
    }
}
```

## Correctness Properties

*A property is a characteristic or behavior that should hold true across all valid executions of a system—essentially, a formal statement about what the system should do. Properties serve as the bridge between human-readable specifications and machine-verifiable correctness guarantees.*

### Property 1: PKS 5.2.1 Mandatory User Linkage
*For any* system submission (helpdesk ticket, loan application, AI conversation), the user_id field MUST be NOT NULL and linked to an authenticated user via SSO - NO GUEST SUBMISSIONS allowed.
**Validates: Requirements 1.1, 1.3, 3.1, 25.1**

### Property 2: Bahasa Melayu Interface Exclusivity
*For any* user interface component, all text content should be exclusively in Bahasa Melayu with no language switching capability available to end users.
**Validates: Requirements 7.1, 7.2, 7.4**

### Property 3: HRMIS Auto-Provisioning Synchronization
*For any* user account, the account MUST be created through HRMIS auto-provisioning and authenticated via LDAP/Active Directory - NO manual registration or alternative authentication methods.
**Validates: Requirements 2.1, 2.2, 2.3, 27.1**

### Property 4: Dual Audit Trail with Mandatory User ID
*For any* system action, both compliance audit (owen-it) and operational log (spatie) entries MUST be created with mandatory user_id linkage per PKS 5.2.1.
**Validates: Requirements 3.1, 3.2, 3.3, 25.1**

### Property 5: Role-Based Access Control Enforcement
*For any* user with a specific role, access to system features should be strictly limited to those authorized for that role level (staff < approver < admin < superuser).
**Validates: Requirements 3.1, 4.3, 12.3**

### Property 6: Performance Monitoring Access Control
*For any* user attempting to access Laravel Pulse, access should only be granted to users with admin or superuser roles, while Laravel Telescope access should be restricted to superuser only.
**Validates: Requirements 4.1, 4.2**

### Property 7: Real-Time Notification via Authenticated Channels Only
*For any* status change event, notifications should be delivered through authenticated WebSocket channels only (user.{userId}) - NO guest/UUID-based channels per PKS 5.2.1.
**Validates: Requirements 6.1, 6.4, 6.5, 24.5, 24.6**

### Property 8: PKS 9.2.1 DLP Filtering for Cloud AI
*For any* AI query routed to AWS Bedrock, the query MUST pass through DLP filtering first - sensitive data MUST be blocked and routed to local Ollama instead.
**Validates: Requirements 25.1, 25.2, 25.3**

### Property 9: SLA Calculation Consistency
*For any* helpdesk ticket, SLA due date calculation should be consistent based on priority level and should trigger appropriate alerts at 25% remaining time.
**Validates: Requirements 8.3, 13.2**

### Property 10: HRMIS-Verified Approval Workflow
*For any* asset loan application, approval through either email-based or portal-based methods MUST verify approver identity via HRMIS before processing.
**Validates: Requirements 9.2, 1.6, 2.4**

### Property 11: Asset-Ticket Integration Automation
*For any* asset returned with damaged or faulty condition, a maintenance ticket should be automatically created within 5 seconds with proper asset linkage and mandatory user_id.
**Validates: Requirements 9.4, 10.2, 25.1**

### Property 12: WCAG 2.2 AA Compliance Verification
*For any* user interface component, color contrast ratios should meet or exceed 4.5:1 for text and 3.1 for UI components, with proper ARIA attributes and keyboard navigation support.
**Validates: Requirements 11.1, 11.2, 11.4**

### Property 13: API Authentication Token Validation
*For any* API request using Laravel Sanctum tokens, authentication should be properly validated and rate limiting should be enforced at 100 requests per minute.
**Validates: Requirements 5.1, 5.3, 12.4**

### Property 14: Core Web Vitals Performance Targets
*For any* page load, performance metrics should meet Core Web Vitals targets: LCP <2.5s, FID <100ms, CLS <0.1, TTFB <600ms.
**Validates: Requirements 11.1, 14.5**

### Property 15: Data Backup and Recovery Integrity
*For any* backup operation, data integrity should be maintained with RTO of 4 hours and RPO of 24 hours, with successful restoration verification.
**Validates: Requirements 15.3, 15.4**

### Property 16: Queue Job Processing Reliability
*For any* queued job in the system, Laravel Horizon should process the job within the configured timeout, retry failed jobs with exponential backoff, and maintain accurate metrics for monitoring.
**Validates: Requirements 23.1, 23.4, 23.6**

### Property 17: PKS 4.2 Data Sovereignty Compliance
*For any* sensitive government data, processing MUST occur locally via Ollama within Malaysian jurisdiction - NO transmission to cloud services without DLP approval.
**Validates: Requirements 26.1, 26.2, 26.4**

### Property 18: PKS 5.4.3 Password Policy Enforcement
*For any* authentication attempt, password policy MUST be enforced via LDAP/Active Directory (8 chars minimum, 90-day expiry, 3 failed attempts lockout).
**Validates: Requirements 27.1, 27.2, 27.3**

### Property 19: PKS CSIRT Incident Response Automation
*For any* security incident detected (unauthorized access, data breach, system anomaly), the system MUST automatically alert CSIRT MOTAC within 15 minutes and generate NACSA/MyCERT compatible incident reports with mandatory user_id linkage.
**Validates: Requirements 28.1, 28.2, 28.4, 28.5**

### Property 20: PKS BCP/DRP Recovery Compliance
*For any* system failure or disaster scenario, the system MUST achieve RTO of 4 hours and RPO of 24 hours with automated failover mechanisms and data replication to secondary site within MOTAC infrastructure.
**Validates: Requirements 29.1, 29.2, 29.3, 29.4**

### Property 21: PKS Security Training Access Control
*For any* user attempting to access sensitive features, the system MUST verify security training completion status and restrict access for users with expired or incomplete training per PKS requirements.
**Validates: Requirements 30.1, 30.2, 30.3, 30.4**

### Property 22: PKS Change Management Workflow Enforcement
*For any* system configuration change, the change MUST be approved through the change management workflow with risk assessment documentation, rollback procedures, and complete audit trail with requester and approver user_id.
**Validates: Requirements 31.1, 31.2, 31.3, 31.4, 31.5**

### Property 23: PKS Third-Party Access Control
*For any* third-party user (vendor/contractor), access MUST be time-limited with automatic expiration, require NDA acknowledgment, and maintain separate enhanced audit trails per PKS requirements.
**Validates: Requirements 32.1, 32.2, 32.3, 32.4, 32.5**

### Property 24: PSPM Strategic Alignment Compliance
*For any* digital service feature, the implementation MUST align with PSPM 2022-2026 strategic objectives across all four pillars (Aplikasi, Data, Infrastruktur ICT, Tadbir Urus & Keupayaan) and national digital transformation initiatives.
**Validates: Requirements 33.1, 33.2, 33.3, 33.4, 33.5**

## Error Handling

### Error Categories v3.6.0

1. **Authentication Errors**: Invalid credentials, expired sessions, unauthorized access attempts
2. **Validation Errors**: Form validation failures, data type mismatches, required field violations
3. **Authorization Errors**: Insufficient permissions, role-based access violations
4. **System Errors**: Database connection failures, external service timeouts, server errors
5. **Performance Errors**: Core Web Vitals threshold breaches, slow query detection
6. **Integration Errors**: API failures, WebSocket connection issues, email delivery failures

### Error Handling Strategy

- **Graceful Degradation**: System continues operating with reduced functionality when non-critical components fail
- **User-Friendly Messages**: All error messages displayed in Bahasa Melayu with clear guidance for resolution
- **Comprehensive Logging**: All errors logged through dual audit system with appropriate severity levels
- **Real-Time Monitoring**: Laravel Pulse integration for performance error detection and alerting
- **Automatic Recovery**: Retry mechanisms with exponential backoff for transient failures

## Testing Strategy

### Dual Testing Approach

The system employs both unit testing and property-based testing for comprehensive coverage:

**Unit Testing**:

- Specific examples and edge cases
- Integration points between components
- Error condition handling
- User interface interactions

**Property-Based Testing**:

- Universal properties across all inputs
- Correctness property validation
- System behavior verification
- Performance characteristic testing

**Property-Based Testing Framework**: PHPUnit with custom property testing extensions
**Minimum Iterations**: 100 iterations per property test
**Test Tagging**: Each property test tagged with format: **Feature: ictserve-comprehensive-v3.6, Property {number}: {property_text}**

### Testing Requirements

- **Coverage Target**: 90% code coverage minimum
- **Performance Testing**: Core Web Vitals validation on all pages
- **Accessibility Testing**: WCAG 2.2 AA compliance verification
- **Security Testing**: Authentication, authorization, and data protection validation
- **Integration Testing**: Cross-module functionality and external service integration
- **Load Testing**: System performance under expected user loads
