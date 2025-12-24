# Requirements Document

## Introduction

The ICTServe System is a comprehensive digital platform for managing ICT services within the Ministry of Tourism, Arts and Culture Malaysia (MOTAC). This system operates on a **PKS-Compliant SSO-Only Architecture** with **mandatory LDAP/Active Directory authentication** for all users, ensuring full accountability per PKS 5.2.1. The system encompasses three main integrated modules: the Helpdesk Ticketing System, the ICT Asset Loan Management System, and the **Cloud Hybrid AI Chatbot** (Ollama + AWS Bedrock) with data sovereignty compliance.

**Critical Architecture v4.0 (PKS Compliant)**: The system provides **intranet-only deployment** with **mandatory SSO authentication** and **Bahasa Melayu exclusive UI**:

1. **Walk-in/Kiosk Mode (SSO Required)**: Kiosk terminals for quick helpdesk tickets and asset loan applications using SSO LDAP/Active Directory authentication - **NO GUEST ACCESS** per PKS 5.2.1
2. **Authenticated Staff Portal (SSO Required)**: Internal portal for staff to view their submissions, manage profiles, access advanced features, and **enhanced AI capabilities** including document analysis, conversation management, and personalized responses
3. **Admin Access (Filament Panel)**: Backend management for admin and superuser roles including **AI configuration**, model management, FAQ administration, and performance monitoring

**PKS Compliance Requirements**:

- **PKS 5.2.1 (Accountability)**: All system activities MUST be traceable to authenticated staff via mandatory user_id FK (NOT NULL)
- **PKS 9.2.1 (Data Transfer)**: Secure API Gateway with DLP filters for cloud AI, intranet air-gap policies maintained
- **PKS 4.2 (Data Sovereignty)**: Intranet-only deployment, local Ollama processing for sensitive data, MyGovCloud prioritization
- **PKS 5.4.3 (Password Policy)**: 8 chars minimum, 90-day expiry, 3 attempts lockout via MOTAC Active Directory

The system emphasizes **Bahasa Melayu only interface** (language switcher disabled), **HRMIS auto-provisioning** replacing manual registration, automated notifications, **Cloud Hybrid AI integration** (Ollama local + AWS Bedrock cloud with DLP), and comprehensive backend management while adhering to WCAG 2.2 Level AA accessibility standards and Core Web Vitals performance targets.

**Version**: 4.0 (SemVer)  
**Last Updated**: 24 Disember 2025  
**Status**: Active - Aligned with KRISA D00-D18 v4.0 Standards  
**Classification**: Restricted - Internal MOTAC BPM  
**Standards Compliance**: ISO/IEC/IEEE 12207, 29148, 15288, WCAG 2.2 AA, MyGOV Digital Service Standards v2.1.0, PDPA 2010, OWASP ASVS L2, **PKS 5.2.1, 9.2.1, 4.2, 5.4.3**, PSPM MyGovCloud

## Glossary

- **ICTServe_System**: The complete integrated platform for managing ICT services at MOTAC BPM with **PKS-Compliant SSO-Only Architecture** (mandatory authentication), **Bahasa Melayu exclusive UI**, and **Cloud Hybrid AI** (Ollama + AWS Bedrock with DLP)
- **Helpdesk_Module**: Digital ticketing system for managing ICT support requests via Walk-in/Kiosk Mode (SSO) and internal portal (authenticated staff) with AI-powered auto-reply generation
- **Asset_Loan_Module**: Digital system for managing ICT equipment loan lifecycle from Walk-in/Kiosk application (SSO) to authenticated staff tracking, email-based approval, and AI-assisted document analysis
- **AI_Chatbot_Module**: Cloud Hybrid AI system combining Ollama (local LLM for FAQ and sensitive data) and AWS Bedrock (Claude models for complex reasoning on public data only) with smart model routing, streaming responses, and DLP filtering per PKS 9.2.1
- **BPM_MOTAC**: Bahagian Pengurusan Maklumat (Information Management Division) of MOTAC
- **Staf_MOTAC**: MOTAC staff members who MUST access system via SSO LDAP/Active Directory authentication per PKS 5.2.1 - NO GUEST ACCESS
- **Pegawai_Penyokong**: Grade 41+ officers who approve loan applications via **authenticated email links** with identity verification through HRMIS
- **Admin**: Administrative users with Filament admin panel access for backend system management (SSO required, operational role)
- **Superuser**: Super administrative users with full Filament admin access and system configuration rights (SSO required with 2FA, governance role)
- **PKS_Compliant_Architecture**: System design with mandatory SSO authentication for ALL users, eliminating guest access per PKS 5.2.1 accountability requirements
- **Walk_in_Kiosk_Mode**: Kiosk terminals for quick submissions using SSO LDAP/Active Directory authentication - replaces deprecated Guest Mode
- **HRMIS_Auto_Provisioning**: Automatic user account creation synchronized with HR System (HRMIS) for active employment verification - replaces manual @motac.gov.my registration
- **Mandatory_User_Linkage**: All system activities MUST have user_id as mandatory FK (NOT NULL) for full accountability per PKS 5.2.1
- **Authenticated_Access**: Internal portal requiring SSO login for staff to view submissions, manage profiles, and access advanced features
- **Dual_Audit_System**: Simultaneous compliance auditing (field-level via owen-it) and operational logging (user activity via spatie) with 7-year retention
- **Internal_Portal**: Authenticated area for staff to manage their submissions and access enhanced features (SSO required)
- **Email_Workflow**: Notification method for authenticated approvals with identity verification
- **Filament_Admin**: Backend administrative interface accessible only to admin and superuser roles via SSO
- **Audit_Trail**: Complete chronological record of all system activities linked to authenticated user_id
- **SLA**: Service Level Agreement defining response and resolution time targets
- **Integrasi_Modul**: Seamless integration between helpdesk and asset loan modules in admin backend
- **Dashboard_Admin**: Unified admin dashboard showing metrics from both modules (Filament-based)
- **Sistem_Notifikasi**: Automated email notification system for authenticated user interactions
- **Kalendar_Tempahan**: Visual booking calendar for asset availability management (admin view)
- **Matriks_Kelulusan**: Approval matrix based on applicant grade and asset value with HRMIS verification
- **Responsif_Design**: User interface that adapts to desktop, tablet, and mobile devices
- **Frontend_Components**: Unified component library for consistent interfaces
- **Bahasa_Melayu_Sahaja**: Exclusive Bahasa Melayu interface support (language switcher disabled)
- **WCAG_Compliance**: Web Content Accessibility Guidelines 2.2 Level AA compliance with strict color contrast ratios (4.5:1 text, 3:1 UI components)
- **MOTAC_Branding**: Consistent visual identity and branding across all interfaces using compliant color palette
- **Core_Web_Vitals**: Performance standards with LCP <2.5s, FID <100ms, CLS <0.1, TTFB <600ms targets
- **Compliant_Color_Palette**: WCAG 2.2 AA compliant colors - Primary #0056b3 (6.8:1), Success #198754 (4.9:1), Warning #ff8c00 (4.5:1), Danger #b50c0c (8.2:1)
- **Laravel_Pulse**: Performance monitoring system accessible to admin and superuser roles
- **Laravel_Sanctum**: API token authentication system for external integrations
- **LDAP_Active_Directory**: MOTAC authentication server for mandatory SSO per PKS 5.2.1
- **Laravel_Telescope**: System debugging tool accessible to superuser only
- **Laravel_Reverb**: WebSocket server for real-time features and notifications
- **Laravel_Horizon**: Redis queue dashboard and monitoring system for background job management
- **Ollama**: Open-source local LLM server for running AI models on-premise, ensuring data sovereignty per PKS 4.2 and PDPA compliance
- **AWS_Bedrock**: Amazon's managed AI service for complex reasoning on PUBLIC DATA ONLY after DLP filtering per PKS 9.2.1
- **DLP_Filtering**: Data Loss Prevention filters mandatory before any cloud AI processing per PKS 9.2.1
- **Data_Sovereignty**: Sensitive government data MUST be processed within Malaysian jurisdiction per PKS 4.2
- **RAG**: Retrieval-Augmented Generation - AI technique combining document retrieval with language generation for FAQ responses
- **Model_Routing**: Smart automatic routing of AI requests to Ollama (sensitive) or Bedrock (public) based on data classification
- **Streaming_Responses**: Server-Sent Events (SSE) for progressive AI response delivery
- **PII_Detection**: Automatic detection and sanitization of Personally Identifiable Information for PDPA 2010 compliance
- **MCP_Server**: Model Context Protocol server for AI assistant integrations (Amazon Q, Kiro IDE)
- **PKS_5_2_1**: Polisi Keselamatan Siber accountability principle - all activities must be traceable to responsible individual
- **PKS_9_2_1**: Data transfer procedures and confidentiality protection - DLP required for cloud services
- **PKS_4_2**: Data sovereignty and jurisdiction - sensitive data must be processed locally
- **PKS_5_4_3**: Password policy - 8 chars, 90-day expiry, 3 attempts lockout via Active Directory
- **CSIRT**: Computer Security Incident Response Team - MOTAC's security incident response coordination team per PKS requirements
- **NACSA**: National Cyber Security Agency - Malaysia's national cybersecurity authority for incident reporting
- **MyCERT**: Malaysia Computer Emergency Response Team - national incident reporting and coordination center
- **BCP**: Business Continuity Plan - documented procedures for maintaining operations during disruptions per PKS requirements
- **DRP**: Disaster Recovery Plan - procedures for recovering ICT systems after disasters per PKS requirements
- **RTO**: Recovery Time Objective - maximum acceptable time to restore system operations (4 hours per PKS)
- **RPO**: Recovery Point Objective - maximum acceptable data loss period (24 hours per PKS)
- **NDA**: Non-Disclosure Agreement - confidentiality agreement required for third-party access per PKS
- **Change_Management**: Controlled process for evaluating, approving, and documenting ICT system changes per PKS
- **Third_Party_Access**: Controlled access for vendors and contractors with time-limited tokens and enhanced audit logging
- **Security_Training**: Mandatory PKS security awareness training for all system users with annual renewal
- **PSPM**: Pelan Strategik Pendigitalan MOTAC 2022-2026 - MOTAC's digital transformation strategic plan
- **Teras_Aplikasi**: PSPM Pillar 1 - Application development and digital service delivery
- **Teras_Data**: PSPM Pillar 2 - Data management, analytics, and governance
- **Teras_Infrastruktur_ICT**: PSPM Pillar 3 - ICT infrastructure modernization and cloud adoption
- **Teras_Tadbir_Urus_Keupayaan**: PSPM Pillar 4 - Governance and digital capability building
- **MyDIGITAL**: Malaysia Digital Economy Blueprint - national digital transformation initiative
- **PSPSA**: Pelan Strategik Perkhidmatan Awam - Public Service Strategic Plan for digital government

## Requirements

### Requirement 1: PKS 5.2.1 Compliant SSO-Only Architecture

**User Story:** As a MOTAC staff member, I want to access the ICTServe system through mandatory SSO authentication (LDAP/Active Directory), so that all my activities are fully traceable and accountable per PKS 5.2.1.

#### Acceptance Criteria

1. WHEN a MOTAC staff member accesses the ICTServe portal, THE ICTServe_System SHALL require mandatory SSO authentication via LDAP/Active Directory with **Bahasa Melayu exclusive interface** - NO GUEST ACCESS per PKS 5.2.1
2. WHEN a staff member submits a helpdesk ticket via Walk-in/Kiosk Mode, THE ICTServe_System SHALL authenticate via SSO, generate a unique ticket number in format HD[YYYY\][000001-999999], link to mandatory user_id FK (NOT NULL), and send confirmation email within 60 seconds
3. WHEN a staff member logs into the authenticated portal, THE ICTServe_System SHALL display their complete submission history including all helpdesk tickets and asset loan applications, allow profile management for contact information and preferences, enable internal comments on submissions, and provide real-time status tracking
4. WHEN a staff member submits an asset loan application, THE ICTServe_System SHALL authenticate via SSO, link to mandatory user_id FK (NOT NULL), and send email notification within 60 seconds to the appropriate approving officer (Grade 41+) with secure approval links containing time-limited tokens valid for 7 days
5. THE ICTServe_System SHALL maintain WCAG 2.2 Level AA compliant UI/UX design across all interfaces (Walk-in/Kiosk, authenticated portal, admin panel) using unified component library with compliant color palette achieving minimum 4.5:1 contrast ratio for text and 3:1 for UI components
6. WHERE approving officers receive loan applications, THE ICTServe_System SHALL verify approver identity via HRMIS before processing approval through authenticated email links OR portal-based approval with both methods updating application status within 5 seconds

### Requirement 2: HRMIS Auto-Provisioning and Account Management

**User Story:** As a MOTAC staff member, I want my account to be automatically provisioned through HRMIS integration, so that only active employees can access the system with verified credentials.

#### Acceptance Criteria

1. THE ICTServe_System SHALL implement HRMIS auto-provisioning to automatically create user accounts for active MOTAC employees - replacing manual @motac.gov.my registration
2. WHEN a staff member's employment status changes in HRMIS, THE ICTServe_System SHALL automatically update or deactivate the corresponding user account within 24 hours
3. THE ICTServe_System SHALL integrate with MOTAC LDAP/Active Directory for SSO authentication with password policy enforcement per PKS 5.4.3 (8 chars, 90-day expiry, 3 attempts)
4. WHEN a staff member authenticates, THE ICTServe_System SHALL verify active employment status via HRMIS before granting access
5. THE ICTServe_System SHALL maintain user profiles with staff_id, grade_id, division_id, and position information synchronized from HRMIS for organizational hierarchy

### Requirement 3: Dual Audit System and PKS Compliance

**User Story:** As a compliance officer, I want comprehensive audit trails that track all activities to authenticated users, so that I can ensure PKS 5.2.1 accountability and regulatory compliance.

#### Acceptance Criteria

1. THE ICTServe_System SHALL implement dual audit system using owen-it/laravel-auditing for compliance tracking (field-level changes) and spatie/laravel-activitylog for operational logging (user activities) with mandatory user_id linkage
2. THE ICTServe_System SHALL maintain immutable audit logs for minimum 7 years including all authenticated user actions, email-based approvals, and administrative changes - ALL linked to user_id per PKS 5.2.1
3. THE ICTServe_System SHALL log all admin actions, authenticated user actions, and system changes with timestamp, mandatory user_id (NOT NULL), action type, and affected data
4. WHERE audit requirements exist, THE ICTServe_System SHALL provide audit trail viewing interfaces in Filament admin with filtering by user, submission type, user role, and date range
5. THE ICTServe_System SHALL comply with PDPA (Personal Data Protection Act) 2010 for data handling including consent management, data retention policies, secure storage with encryption, and data subject rights

### Requirement 4: Enhanced Admin Panel with Performance Monitoring

**User Story:** As an admin or superuser, I want enhanced administrative capabilities including performance monitoring and system debugging tools, so that I can efficiently manage the system and troubleshoot issues.

#### Acceptance Criteria

1. THE ICTServe_System SHALL provide Laravel Pulse v1.3.0 performance monitoring accessible to admin and superuser roles with real-time metrics for response times, database queries, and system performance
2. THE ICTServe_System SHALL implement Laravel Telescope debugging tool accessible to superuser only for system debugging, query analysis, and error tracking
3. THE ICTServe_System SHALL provide unified Filament admin panel with role-based access control (RBAC) supporting four distinct roles: staff, approver, admin, and superuser
4. WHEN managing assets in the admin panel, THE ICTServe_System SHALL display related helpdesk tickets and complete maintenance history in a tabbed interface with WCAG 2.2 Level AA compliant design
5. THE ICTServe_System SHALL provide integrated dashboard analytics displaying KPIs from both helpdesk and asset loan modules with real-time updates using Laravel Reverb

### Requirement 5: API Integration and LDAP/Active Directory SSO

**User Story:** As a system administrator, I want API integration capabilities and mandatory LDAP/Active Directory SSO, so that the system integrates with MOTAC infrastructure and ensures PKS 5.2.1 compliance.

#### Acceptance Criteria

1. THE ICTServe_System SHALL implement Laravel Sanctum v4.0 for API token authentication enabling secure external integrations
2. THE ICTServe_System SHALL implement mandatory LDAP/Active Directory SSO for all user authentication per PKS 5.2.1 - NO alternative authentication methods
3. WHERE external integration is required, THE ICTServe_System SHALL provide RESTful API endpoints following OpenAPI 3.0 specification with authentication tokens and rate limiting of 100 requests per minute
4. THE ICTServe_System SHALL maintain API documentation and versioning for external system integrations
5. THE ICTServe_System SHALL log all API access and authentication attempts for security monitoring with mandatory user_id linkage

### Requirement 6: Real-Time Communication and Notifications

**User Story:** As a system user, I want real-time notifications and updates, so that I receive immediate feedback on system changes and status updates.

#### Acceptance Criteria

1. THE ICTServe_System SHALL implement Laravel Reverb v1.6.3 WebSocket server for real-time features and notifications with Redis scaling support and Pulse/Telescope integration
2. THE ICTServe_System SHALL use Laravel Echo v2.2.6 for client-side WebSocket communication with exponential backoff reconnection (1s-30s, max 10 attempts) and graceful fallback mechanisms
3. THE ICTServe_System SHALL provide multi-channel notifications (email, database, WebSocket) based on user preferences with WCAG 2.2 AA compliant toast notifications
4. WHEN status changes occur, THE ICTServe_System SHALL broadcast real-time updates to authenticated users via private WebSocket channels (user.{userId}, admin.notifications) within 5 seconds
5. THE ICTServe_System SHALL implement automated email notifications within 60 seconds for all status changes and important events using Laravel Horizon queued jobs
6. THE ICTServe_System SHALL support authenticated channel access via user-specific channels (ticket.{userId}.{ticketId}, loan.{userId}.{loanId}) with SSO token validation for secure notifications

### Requirement 7: Bahasa Melayu Exclusive Interface

**User Story:** As a MOTAC staff member, I want the system interface to be exclusively in Bahasa Melayu, so that I can use the system in my preferred official language.

#### Acceptance Criteria

1. THE ICTServe_System SHALL provide **Bahasa Melayu exclusive interface** for all user-facing components (guest forms, authenticated portal, admin panel)
2. THE ICTServe_System SHALL disable the language switcher component while maintaining English translation files for technical reference
3. THE ICTServe_System SHALL ensure all user interface text, error messages, email templates, and system notifications are in Bahasa Melayu
4. THE ICTServe_System SHALL maintain consistent Bahasa Melayu terminology across all modules and interfaces
5. WHERE technical documentation is required, THE ICTServe_System SHALL maintain English versions for developer reference while keeping user interfaces in Bahasa Melayu only

### Requirement 8: Enhanced Helpdesk Module with SSO

**User Story:** As a MOTAC staff member, I want an enhanced helpdesk system that requires SSO authentication for all submissions with comprehensive tracking and management capabilities per PKS 5.2.1.

#### Acceptance Criteria

1. THE ICTServe_System SHALL provide helpdesk ticket submission via Walk-in/Kiosk Mode (SSO required) and authenticated portal (SSO required) - NO GUEST SUBMISSIONS per PKS 5.2.1
2. WHEN a helpdesk ticket is submitted, THE ICTServe_System SHALL authenticate via SSO, link to mandatory user_id FK (NOT NULL), automatically generate ticket number, send confirmation email, and notify admin users
3. THE ICTServe_System SHALL implement SLA tracking with automated escalation when thresholds are within 25% of breach time
4. THE ICTServe_System SHALL allow admin users to assign tickets to MOTAC divisions or external agencies through the Filament admin panel
5. THE ICTServe_System SHALL provide ticket status lifecycle management with automated notifications for status changes to authenticated users

### Requirement 9: Enhanced Asset Loan Module with SSO

**User Story:** As a MOTAC staff member, I want an enhanced asset loan system that requires SSO authentication for all applications with comprehensive asset management per PKS 5.2.1.

#### Acceptance Criteria

1. THE ICTServe_System SHALL provide asset loan application via Walk-in/Kiosk Mode (SSO required) and authenticated portal (SSO required) - NO GUEST APPLICATIONS per PKS 5.2.1
2. THE ICTServe_System SHALL implement approval workflows with approver identity verification via HRMIS before processing email-based approval OR portal-based approval
3. THE ICTServe_System SHALL maintain asset inventory with real-time availability tracking and booking calendar
4. WHEN an asset is returned with damage, THE ICTServe_System SHALL automatically create a maintenance ticket in the helpdesk module within 5 seconds with mandatory user_id linkage
5. THE ICTServe_System SHALL implement approval matrix logic based on applicant grade and asset value for automatic approver assignment with HRMIS verification

### Requirement 10: System Integration and Cross-Module Features

**User Story:** As an admin user, I want seamless integration between helpdesk and asset loan modules, so that I can manage all operations through a unified interface.

#### Acceptance Criteria

1. THE ICTServe_System SHALL provide unified admin dashboard combining metrics from both helpdesk and asset loan modules
2. WHEN a helpdesk ticket relates to a loaned asset, THE ICTServe_System SHALL automatically link the ticket to the relevant asset record
3. THE ICTServe_System SHALL maintain a single source of truth for staff data, asset information, and organizational structure
4. THE ICTServe_System SHALL provide cross-module reporting combining data from both helpdesk and asset loan systems
5. THE ICTServe_System SHALL implement unified search across both modules in the admin panel

### Requirement 11: Performance and Accessibility Compliance

**User Story:** As a system user, I want the system to meet high performance standards and accessibility requirements, so that I can use the system efficiently regardless of my abilities or device.

#### Acceptance Criteria

1. THE ICTServe_System SHALL meet Core Web Vitals performance targets: LCP <2.5s, FID <100ms, CLS <0.1, TTFB <600ms
2. THE ICTServe_System SHALL comply with WCAG 2.2 Level AA accessibility standards including minimum 4.5:1 contrast ratio for text and 3:1 for UI components
3. THE ICTServe_System SHALL implement responsive design supporting desktop, tablet, and mobile viewports with minimum 44×44px touch targets
4. THE ICTServe_System SHALL ensure all interactive elements are accessible via keyboard navigation with visible focus indicators
5. THE ICTServe_System SHALL provide proper ARIA attributes, semantic HTML structure, and screen reader compatibility

### Requirement 12: Security and Data Protection

**User Story:** As a security administrator, I want comprehensive security controls and data protection measures, so that the system maintains data security and regulatory compliance.

#### Acceptance Criteria

1. THE ICTServe_System SHALL implement secure authentication using Laravel Breeze with password hashing, session management, and CSRF protection
2. THE ICTServe_System SHALL encrypt sensitive data at rest using AES-256 encryption and in transit using TLS 1.3 or higher
3. THE ICTServe_System SHALL implement role-based access control with four distinct roles and proper authorization policies
4. THE ICTServe_System SHALL provide secure API endpoints with authentication tokens and rate limiting
5. THE ICTServe_System SHALL maintain comprehensive security logs and monitoring for unauthorized access attempts

### Requirement 13: Automated Workflow Management

**User Story:** As a system user, I want automated workflow management and notification systems, so that I receive timely updates and the system handles routine processes automatically.

#### Acceptance Criteria

1. THE ICTServe_System SHALL implement automated email notifications using Laravel Queue system with Redis driver managed by Laravel Horizon
2. THE ICTServe_System SHALL provide automated reminder systems for overdue asset returns and pending approvals
3. THE ICTServe_System SHALL implement configurable business rules and triggers accessible to superuser through admin panel
4. THE ICTServe_System SHALL provide automated report generation with scheduled email delivery to designated admin users
5. THE ICTServe_System SHALL implement retry mechanisms with exponential backoff for failed notifications and processes

### Requirement 23: Laravel Horizon Queue Management

**User Story:** As an admin or superuser, I want comprehensive queue management and monitoring using Laravel Horizon, so that I can monitor background job processing, identify failed jobs, and ensure reliable system operations.

#### Acceptance Criteria

1. THE ICTServe_System SHALL implement Laravel Horizon v5.x for Redis queue management with dashboard accessible to admin and superuser roles
2. THE ICTServe_System SHALL configure queue supervisors for ICTServe-specific job types including helpdesk notifications, loan approval workflows, AI processing, and report generation
3. THE ICTServe_System SHALL implement job balancing strategies with auto-scaling based on queue workload for optimal resource utilization
4. THE ICTServe_System SHALL provide real-time metrics including job throughput, wait times, failed job counts, and worker status with 60-second refresh intervals
5. THE ICTServe_System SHALL implement automated alerting for queue issues including long wait times exceeding 60 seconds, failed job accumulation exceeding 10 jobs, and worker process failures
6. THE ICTServe_System SHALL configure job retry policies with exponential backoff (10s, 30s, 60s) and maximum retry attempts of 3 for transient failures
7. THE ICTServe_System SHALL implement job tagging for ICTServe operations enabling filtering by module (helpdesk, asset-loan, ai-chatbot) and priority level
8. THE ICTServe_System SHALL integrate Horizon metrics with Laravel Pulse for unified performance monitoring and historical trend analysis

### Requirement 24: Laravel Reverb Real-Time Communication (PKS 5.2.1 Compliant)

**User Story:** As an authenticated MOTAC staff member, I want reliable real-time WebSocket communication for instant notifications and live updates, so that I receive immediate feedback on system changes without page refreshes while maintaining full accountability per PKS 5.2.1.

#### Acceptance Criteria

1. THE ICTServe_System SHALL implement Laravel Reverb v1.6.3 WebSocket server with configurable host (default 0.0.0.0), port (default 8080), and max request size (10,000 bytes)
2. THE ICTServe_System SHALL configure Redis scaling support for horizontal scaling with configurable channel settings and connection pooling
3. THE ICTServe_System SHALL integrate Reverb with Laravel Pulse (15-second ingest interval) and Laravel Telescope (15-second ingest interval) for comprehensive monitoring
4. THE ICTServe_System SHALL implement Laravel Echo v2.2.6 client with exponential backoff reconnection strategy (1s-30s with jitter, maximum 10 attempts) and graceful fallback mechanisms
5. THE ICTServe_System SHALL provide private channel authorization for authenticated users ONLY (user.{userId}, admin.notifications) with policy-based access control and mandatory user_id linkage per PKS 5.2.1 - NO GUEST CHANNELS
6. THE ICTServe_System SHALL support authenticated submission channels (ticket.{userId}.{ticketId}, loan.{userId}.{loanId}) with SSO token validation for secure notifications - ALL channels require authenticated user_id
7. THE ICTServe_System SHALL implement AI-specific broadcast channels (ai-status, ai-alerts, ai-performance, ai-approvals) with role-based authorization for admin/superuser/approver access
8. THE ICTServe_System SHALL provide WCAG 2.2 AA compliant connection status UI with reconnection toast notifications and custom events (echo:connected, echo:disconnected, echo:unavailable)

### Requirement 25: PKS 9.2.1 Data Transfer and DLP Compliance

**User Story:** As a security administrator, I want all data transfers to cloud AI services to be filtered through DLP (Data Loss Prevention) controls, so that sensitive government data is protected per PKS 9.2.1.

#### Acceptance Criteria

1. THE ICTServe_System SHALL implement mandatory DLP filtering for ALL data sent to AWS Bedrock cloud AI services per PKS 9.2.1
2. THE ICTServe_System SHALL classify data as SENSITIVE (local Ollama only) or PUBLIC (cloud Bedrock allowed) before any AI processing
3. THE ICTServe_System SHALL automatically detect and block PII, government classified information, and internal MOTAC data from cloud transmission
4. THE ICTServe_System SHALL maintain audit logs of all DLP filter decisions with user_id, data classification, and routing decision
5. THE ICTServe_System SHALL provide DLP configuration interface in Filament admin for superuser to manage classification rules
6. THE ICTServe_System SHALL implement secure API Gateway patterns for all external cloud service communications

### Requirement 26: PKS 4.2 Data Sovereignty Compliance

**User Story:** As a compliance officer, I want sensitive government data to be processed within Malaysian jurisdiction, so that data sovereignty requirements per PKS 4.2 are maintained.

#### Acceptance Criteria

1. THE ICTServe_System SHALL deploy on intranet-only infrastructure with no direct public internet access for sensitive operations
2. THE ICTServe_System SHALL process ALL sensitive data using local Ollama LLM server within MOTAC infrastructure per PKS 4.2
3. THE ICTServe_System SHALL prioritize MyGovCloud for any cloud infrastructure requirements
4. THE ICTServe_System SHALL maintain data residency logs showing processing location for all AI operations
5. THE ICTServe_System SHALL implement air-gap policies preventing sensitive data from leaving Malaysian jurisdiction

### Requirement 27: PKS 5.4.3 Password Policy Compliance

**User Story:** As a security administrator, I want password policies enforced through MOTAC Active Directory, so that authentication meets PKS 5.4.3 requirements.

#### Acceptance Criteria

1. THE ICTServe_System SHALL enforce minimum 8 character password length via LDAP/Active Directory integration per PKS 5.4.3
2. THE ICTServe_System SHALL enforce 90-day password expiry policy via Active Directory synchronization
3. THE ICTServe_System SHALL implement 3 failed login attempts lockout with automatic unlock after 30 minutes
4. THE ICTServe_System SHALL display password policy requirements in Bahasa Melayu during any password-related operations
5. THE ICTServe_System SHALL log all authentication attempts (success and failure) with user_id for security monitoring

### Requirement 14: Monitoring and Analytics

**User Story:** As a system administrator, I want comprehensive monitoring and analytics capabilities, so that I can track system performance and make data-driven decisions.

#### Acceptance Criteria

1. THE ICTServe_System SHALL provide real-time system monitoring using Laravel Pulse with metrics collection every 60 seconds
2. THE ICTServe_System SHALL implement configurable alerts for SLA breaches, system issues, and performance degradation
3. THE ICTServe_System SHALL provide unified analytics dashboard with KPIs from both helpdesk and asset loan modules
4. THE ICTServe_System SHALL generate automated reports in CSV, PDF, and Excel formats with proper formatting and metadata
5. THE ICTServe_System SHALL maintain performance baselines and alerting when Core Web Vitals thresholds are exceeded

### Requirement 15: Data Management and Backup

**User Story:** As a system administrator, I want robust data management and backup capabilities, so that the system can maintain data integrity and recover from failures.

#### Acceptance Criteria

1. THE ICTServe_System SHALL use MySQL 8.0 or higher with proper indexing and foreign key constraints for referential integrity
2. THE ICTServe_System SHALL implement Redis 7.0 or higher for caching and session management with appropriate TTL settings
3. THE ICTServe_System SHALL implement automated daily backup procedures with Recovery Time Objective (RTO) of 4 hours and Recovery Point Objective (RPO) of 24 hours
4. THE ICTServe_System SHALL maintain referential integrity between all entities using foreign key constraints with appropriate CASCADE or RESTRICT actions
5. THE ICTServe_System SHALL provide data export functionality with file size limits and proper error handling

### Requirement 16: Laravel Pulse Performance Monitoring

**User Story:** As an admin or superuser, I want comprehensive performance monitoring using Laravel Pulse, so that I can identify performance bottlenecks and optimize system operations.

#### Acceptance Criteria

1. THE ICTServe_System SHALL configure Laravel Pulse v1.3.0 with comprehensive metrics collection including request duration, database queries, cache hits/misses, and queue job processing times
2. THE ICTServe_System SHALL provide real-time performance dashboards accessible to admin and superuser roles with metrics updated every 60 seconds
3. THE ICTServe_System SHALL implement automated alerting for performance threshold breaches including response times exceeding 2 seconds, database query times exceeding 500ms, and queue job failures
4. THE ICTServe_System SHALL create custom performance metrics for ICTServe-specific operations including ticket processing time, loan approval workflow duration, and asset availability check latency
5. THE ICTServe_System SHALL integrate performance monitoring with existing dual audit systems for comprehensive operational visibility

### Requirement 17: Laravel Telescope System Debugging

**User Story:** As a superuser, I want comprehensive system debugging capabilities using Laravel Telescope, so that I can diagnose issues and troubleshoot system problems effectively.

#### Acceptance Criteria

1. THE ICTServe_System SHALL configure Laravel Telescope v5.x with superuser-only access enforced through middleware and authorization policies
2. THE ICTServe_System SHALL set up comprehensive request monitoring including HTTP requests, database queries, cache operations, scheduled tasks, and queue jobs
3. THE ICTServe_System SHALL implement error tracking and exception logging with stack traces, request context, and user information for debugging
4. THE ICTServe_System SHALL create custom debugging tools for ICTServe-specific operations including email delivery tracking, approval workflow debugging, and asset availability verification
5. THE ICTServe_System SHALL integrate debugging data with performance monitoring for correlated analysis of system issues

### Requirement 18: Comprehensive Testing Suite

**User Story:** As a developer, I want comprehensive automated testing coverage, so that I can ensure system reliability and prevent regressions.

#### Acceptance Criteria

1. THE ICTServe_System SHALL implement comprehensive unit tests for all models, services, and controllers using PHPUnit v12 with PHP 8 attributes achieving minimum 80% code coverage
2. THE ICTServe_System SHALL implement property-based testing for correctness properties validation including data integrity, business rule enforcement, and state transitions
3. THE ICTServe_System SHALL implement integration tests for cross-module functionality including helpdesk-asset loan integration, dual audit system, and notification workflows
4. THE ICTServe_System SHALL implement Core Web Vitals performance testing with automated validation ensuring LCP <2.5s, FID <100ms, CLS <0.1, TTFB <600ms
5. THE ICTServe_System SHALL implement accessibility testing with WCAG 2.2 AA compliance verification using automated tools and manual testing protocols

### Requirement 19: Cloud Hybrid AI Chatbot Integration

**User Story:** As a MOTAC staff member, I want an AI-powered chatbot that can answer my questions about ICTServe system and help with common tasks, so that I can get instant support without waiting for human assistance.

#### Acceptance Criteria

1. THE ICTServe_System SHALL implement Cloud Hybrid AI Architecture combining Ollama (local LLM for FAQ) and AWS Bedrock (Claude models for complex reasoning) with smart model routing based on query analysis
2. WHEN a user submits a query to the AI chatbot, THE ICTServe_System SHALL analyze the query and route it to the optimal AI backend: Ollama for FAQ-specific queries, Bedrock for complex reasoning, or hybrid for combined responses
3. THE ICTServe_System SHALL provide AI chatbot access to both guest users (FAQ Bot on public forms) and authenticated users (enhanced AI features in portal) with appropriate feature differentiation
4. THE ICTServe_System SHALL implement multi-model intelligence supporting Claude Opus 4.5 (complex), Sonnet 4.5 (balanced), and Haiku 4.5 (fast) with automatic model selection based on task complexity
5. THE ICTServe_System SHALL ensure data residency compliance by automatically classifying queries for local (Ollama) vs cloud (Bedrock) processing based on PII detection and PDPA 2010 requirements
6. THE ICTServe_System SHALL provide conversation management capabilities including save, load, and delete conversations with long-term memory for authenticated users
7. THE ICTServe_System SHALL implement web-augmented responses using DuckDuckGo integration for queries requiring current information
8. THE ICTServe_System SHALL provide AI response times under 5 seconds for FAQ queries (Ollama) and under 15 seconds for complex queries (Bedrock) with confidence scoring and source attribution

### Requirement 20: AI Auto-Reply and Document Analysis

**User Story:** As an admin user, I want AI-generated response drafts for common ticket categories and AI-powered document analysis, so that I can respond to users faster and process documents more efficiently.

#### Acceptance Criteria

1. THE ICTServe_System SHALL generate AI-powered auto-reply drafts for helpdesk tickets using smart model routing, requiring admin review and approval before sending
2. THE ICTServe_System SHALL implement document analysis capabilities for PDF/DOCX/images using AWS Bedrock Nova Pro with semantic search via vector embeddings
3. THE ICTServe_System SHALL provide automated document categorization with confidence scoring and PII detection for PDPA 2010 compliance
4. THE ICTServe_System SHALL implement MCP Server integration providing 3 tools for AI assistants (Amazon Q, Kiro IDE) with standardized interface for AI tool access
5. THE ICTServe_System SHALL provide AI management interfaces in Filament admin including model configuration, FAQ management, conversation analytics, and health monitoring for Ollama and Bedrock systems

### Requirement 21: Asset Management Lifecycle

**User Story:** As an admin user, I want comprehensive asset lifecycle management including preventive maintenance scheduling and inter-department transfers, so that I can maintain asset health and track asset movements across the organization.

#### Acceptance Criteria

1. THE ICTServe_System SHALL implement preventive maintenance scheduling based on time intervals (monthly/quarterly/annually) with automated reminder notifications
2. THE ICTServe_System SHALL support corrective maintenance tracking in response to reported issues including troubleshooting logs, parts tracking, and repair history
3. THE ICTServe_System SHALL implement asset transfer workflows between divisions with approval signatures, timestamps, handover certificates, and custodian assignment
4. THE ICTServe_System SHALL maintain asset custodian records with department head accountability for assets under their custody
5. THE ICTServe_System SHALL provide asset utilization analytics with predictive insights for maintenance scheduling and replacement planning

### Requirement 22: System Validation and Go-Live

**User Story:** As a project stakeholder, I want comprehensive system validation before go-live, so that I can ensure the system meets all requirements and is ready for production use.

#### Acceptance Criteria

1. THE ICTServe_System SHALL pass all automated tests with comprehensive coverage validation including unit tests, integration tests, and end-to-end tests achieving minimum 80% code coverage
2. THE ICTServe_System SHALL validate all performance targets including Core Web Vitals thresholds (LCP <2.5s, FID <100ms, CLS <0.1, TTFB <600ms) and AI response time requirements
3. THE ICTServe_System SHALL confirm all security requirements including authentication, authorization, data encryption, audit trail functionality, and AI data residency compliance
4. THE ICTServe_System SHALL verify all integration points including email workflows, real-time communication, cross-module features, and Cloud Hybrid AI integration
5. THE ICTServe_System SHALL complete final user acceptance testing with documented sign-off from designated stakeholders including AI chatbot functionality validation

### Requirement 28: PKS CSIRT Integration and Incident Response

**User Story:** As a security administrator, I want the system to integrate with MOTAC CSIRT for incident response coordination, so that security incidents are properly reported and managed per PKS requirements.

#### Acceptance Criteria

1. THE ICTServe_System SHALL implement automated security incident detection and alerting for unauthorized access attempts, data breaches, and system anomalies per PKS incident management requirements
2. THE ICTServe_System SHALL provide incident reporting interfaces that generate reports compatible with NACSA and MyCERT reporting requirements
3. THE ICTServe_System SHALL maintain incident logs with timestamp, affected systems, user_id, incident type, and response actions for minimum 7 years
4. THE ICTServe_System SHALL implement automated escalation workflows for critical security incidents to CSIRT MOTAC within 15 minutes of detection
5. THE ICTServe_System SHALL provide incident classification (High/Medium/Low) based on impact assessment and affected data sensitivity per PKS guidelines

### Requirement 29: PKS Business Continuity and Disaster Recovery (BCP/DRP)

**User Story:** As a system administrator, I want comprehensive business continuity and disaster recovery capabilities, so that the system can recover from disasters and maintain service continuity per PKS requirements.

#### Acceptance Criteria

1. THE ICTServe_System SHALL implement automated backup procedures with RTO (Recovery Time Objective) of 4 hours and RPO (Recovery Point Objective) of 24 hours per PKS BCP requirements
2. THE ICTServe_System SHALL maintain disaster recovery site configuration with data replication to secondary location within MOTAC infrastructure
3. THE ICTServe_System SHALL implement automated failover mechanisms for critical system components including database, cache, and queue services
4. THE ICTServe_System SHALL provide system health monitoring with automated alerts for service degradation and potential failures
5. THE ICTServe_System SHALL document and test DRP procedures annually with documented results and improvement recommendations per PKS audit requirements

### Requirement 30: PKS Security Awareness and Training Compliance

**User Story:** As a compliance officer, I want the system to track security awareness training completion, so that all users meet PKS mandatory training requirements.

#### Acceptance Criteria

1. THE ICTServe_System SHALL track user security awareness training completion status with training date, expiry date, and certification records
2. THE ICTServe_System SHALL implement automated reminders for users with expiring or incomplete security training (30 days, 7 days, 1 day before expiry)
3. THE ICTServe_System SHALL restrict access to sensitive features for users who have not completed mandatory security training per PKS requirements
4. THE ICTServe_System SHALL provide training compliance reports for admin and superuser roles showing completion rates by division and role
5. THE ICTServe_System SHALL integrate with MOTAC training management system for automatic training record synchronization

### Requirement 31: PKS Change Management Compliance

**User Story:** As a system administrator, I want controlled change management processes for all ICT system changes, so that changes are properly evaluated, approved, and documented per PKS requirements.

#### Acceptance Criteria

1. THE ICTServe_System SHALL implement change request workflows requiring approval from designated authorities before any system configuration changes
2. THE ICTServe_System SHALL maintain change logs with timestamp, change description, requester user_id, approver user_id, and rollback procedures
3. THE ICTServe_System SHALL implement automated rollback capabilities for failed changes with documented recovery procedures
4. THE ICTServe_System SHALL require risk assessment documentation for all changes affecting security controls or sensitive data processing
5. THE ICTServe_System SHALL provide change audit reports for compliance review showing all system changes within specified date ranges

### Requirement 32: PKS Third-Party Security Management

**User Story:** As a security administrator, I want comprehensive third-party access controls, so that vendor and contractor access is properly managed and monitored per PKS requirements.

#### Acceptance Criteria

1. THE ICTServe_System SHALL implement time-limited access tokens for third-party users with automatic expiration based on contract end dates
2. THE ICTServe_System SHALL require NDA (Non-Disclosure Agreement) acknowledgment before granting any third-party system access
3. THE ICTServe_System SHALL maintain separate audit trails for all third-party user activities with enhanced logging detail
4. THE ICTServe_System SHALL implement automatic access termination workflows when third-party contracts expire or are terminated
5. THE ICTServe_System SHALL provide third-party access reports showing all active vendor accounts, access levels, and last activity timestamps

### Requirement 33: PSPM Strategic Alignment - Digital Service Integration

**User Story:** As a MOTAC digital transformation stakeholder, I want the system to align with PSPM 2022-2026 strategic objectives, so that ICTServe contributes to MOTAC's digital transformation goals.

#### Acceptance Criteria

1. THE ICTServe_System SHALL provide integrated digital services supporting PSPM Teras Aplikasi objectives with end-to-end digital workflows
2. THE ICTServe_System SHALL implement data analytics capabilities supporting PSPM Teras Data objectives with management dashboards and KPI tracking
3. THE ICTServe_System SHALL utilize modern infrastructure supporting PSPM Teras Infrastruktur ICT objectives including cloud-ready architecture and scalable design
4. THE ICTServe_System SHALL support digital capability building per PSPM Teras Tadbir Urus & Keupayaan through user-friendly interfaces and comprehensive documentation
5. THE ICTServe_System SHALL align with MyDIGITAL and PSPSA national digital transformation initiatives through standards compliance and interoperability
