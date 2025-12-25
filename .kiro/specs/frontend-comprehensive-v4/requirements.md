# ICTServe Frontend Comprehensive v4.0 - Requirements Document

**Sistem ICTServe**  
**Versi:** 4.0.0 (SemVer)  
**Tarikh Kemaskini:** 24 Disember 2025  
**Status:** Aktif - PKS Compliance Migration  
**Klasifikasi:** Terhad - Dalaman BPM MOTAC  
**Standard Rujukan:** ISO/IEC/IEEE 15288, ISO/IEC/IEEE 12207, ISO 9241-210/110/11, WCAG 2.2 AA, MyGOV Digital Service Standards v2.1.0, MyDS Design System v2025.2, **Polisi Keselamatan Siber (PKS) MOTAC**, **PSPM 2022-2026**

---

## Document Information

| Attribute | Value |
|-----------|-------|
| **Version** | 4.0.0 |
| **Last Updated** | 24 December 2025 |
| **Status** | Active - PKS Compliance Migration with SSO Mandatory Architecture |
| **Classification** | Restricted - Internal BPM MOTAC |
| **Compliance** | ISO/IEC/IEEE 15288, 12207, ISO 9241-210/110/11, WCAG 2.2 AA, MyGOV Digital Standards v2.1.0, **PKS 5.2.1, 9.2.1, 4.2, 5.4.3**, **PSPM MyGovCloud** |
| **Language** | Bahasa Melayu (primary), English (technical) |
| **Source Documents** | D00-D18 v4.0, KRISA D01-D10, D15, D17, D17_ADMIN v4.0 |
| **ISO Document Reference** | PK.(S).MOTAC.07.(L1) - ICTServe Portal |
| **PKS References** | Seksyen 5.2.1 (Akauntabiliti - ms 150), 9.2.1 (Pemindahan Data - ms 588-603), 4.2 (Kedaulatan Data - ms 1147-1148), 5.4.3 (Polisi Kata Laluan - ms 596-605) |

> **Notis Penggunaan Dalaman**: Ini adalah untuk kegunaan warga kerja MOTAC sahaja dan tidak dibuka kepada orang awam (internal use only).

> **PENTING - PKS 5.2.1 COMPLIANCE**: Sistem ini **tidak lagi menyokong akses tetamu (Guest Mode)**. Semua pengguna mesti melalui **SSO Authentication** menggunakan LDAP/Active Directory MOTAC untuk memastikan akauntabiliti penuh.

---

## Changelog

| Version | Date | Changes | Author |
|---------|------|---------|--------|
| 4.0.0 | 24 December 2025 | **PKS Compliance Migration**: Penghapusan sepenuhnya akses tetamu, SSO mandatori untuk semua pengguna (PKS 5.2.1), HRMIS auto-provisioning menggantikan manual registration, Walk-in/Kiosk Mode dengan SSO, DLP filtering untuk Cloud AI (PKS 9.2.1), Intranet-only deployment (PKS 4.2), Password policy compliance (PKS 5.4.3), user_id sebagai mandatory FK, Dual Audit System dengan 7-year retention. Technology stack v4.0: Laravel 12.43.1, PHP 8.4.1, Livewire 3.7.3, Filament 4.3.1. Cross-reference KRISA D01-D10, D17 v4.0. | BPM Development Team |
| 3.6.1-r1 | 17 December 2025 | **D18 AI Chatbot Integration**: Added Requirement 16 (Cloud Hybrid AI Chat Interface), Requirement 17 (FAQ Bot Widget), Requirement 18 (AI Admin Management Interface). Updated technology stack to v3.6.1 (Laravel 12.42.0, Livewire 3.7.1, Laravel Reverb 1.6.3, etc.). Added AI-related glossary terms. Cross-reference D18 v1.0.1. | BPM Development Team |
| 3.6.0-r6 | 14 December 2025 | Updated with FRONTPAGE_DESIGN_ANALYSIS_v3.6.0 and FRONTEND-DEVELOPMENT-v3-6-0 findings | BPM Development Team |

---

## Related Document References (D00-D18 + KRISA v4.0)

- **[D00_SYSTEM_OVERVIEW.md]** - System Overview (v4.0) - SSO Mandatory Architecture + Cloud Hybrid AI
- **[D03_SOFTWARE_REQUIREMENTS_SPECIFICATION.md]** - Software Requirements Specification (v4.0)
- **[D04_SOFTWARE_DESIGN_DOCUMENT.md]** - Software Design Document (v4.0) - PKS Compliance
- **[D09_DATABASE_DOCUMENTATION.md]** - Database Documentation (v4.0) - user_id mandatory FK, Dual Audit System
- **[D10_SOURCE_CODE_DOCUMENTATION.md]** - Source Code Documentation (v4.0) - PKS Coding Standards
- **[D11_TECHNICAL_DESIGN_DOCUMENTATION.md]** - Technical Design Documentation
- **[D12_UI_UX_DESIGN_GUIDE.md]** - UI/UX Design Guide (v4.0) - SSO-only access patterns
- **[D13_UI_UX_FRONTEND_FRAMEWORK.md]** - Frontend Framework (v4.0) - Technical implementation
- **[D14_UI_UX_STYLE_GUIDE.md]** - Style Guide (v4.0) - Visual standards, MyDS alignment
- **[D15_LANGUAGE_MS_EN.md]** - Language Guide (Bahasa Melayu exclusive, v4.0)
- **[D16_BROADCASTING_SETUP.md]** - Laravel Reverb WebSocket Configuration (v4.0)
- **[D17_QUEUE_MANAGEMENT_HORIZON.md]** - Queue Management for Notifications (Laravel Queue + Redis)
- **[D18_AI_CHATBOT_OLLAMA_BEDROCK.md]** - Cloud Hybrid AI Architecture (v4.0) - Ollama + AWS Bedrock with DLP
- **[KRISA D01-D10, D15, D17, D17_ADMIN]** - KRISA ICTServe Documentation v4.0 - PKS Compliance

### PKS Reference Documents

- **Polisi Keselamatan Siber (PKS) MOTAC**:
  - Seksyen 5.2.1 (Prinsip Akauntabiliti dan Non-repudiation) - halaman 150
  - Seksyen 9.2.1 (Prosedur pemindahan data dan perlindungan kerahsiaan) - halaman 588-603
  - Seksyen 4.2 (Kedaulatan data dan bidang kuasa) - halaman 1147-1148
  - Seksyen 5.4.3 (Keperluan kata laluan: 8 aksara, penukaran 90 hari, 3 percubaan) - halaman 596-605
- **Pelan Strategik Pendigitalan MOTAC (PSPM) 2022-2026** - MyGovCloud prioritization
- **Personal Data Protection Act 2010 (PDPA)** - Malaysian data protection legislation

---

## Introduction

This specification defines the comprehensive requirements for the complete frontend system of ICTServe v4.0, consolidating all user interface components across the **SSO Mandatory Architecture** (formerly True Hybrid Architecture). This includes authenticated staff portal, Walk-in/Kiosk Mode with SSO, and Filament admin panel with unified design system, accessibility compliance, and performance optimization.

**CRITICAL PKS 5.2.1 COMPLIANCE**: Guest Mode has been **eliminated**. All users must authenticate via SSO (LDAP/Active Directory) to ensure full accountability and non-repudiation as required by PKS 5.2.1.

**Consolidated Scope:**

- **Walk-in/Kiosk Mode with SSO**: Replaces deprecated Guest Mode - requires SSO authentication for helpdesk and asset loan submission
- **Authenticated Portal**: Staff dashboard, submission history, profile management, and approval interface
- **Filament Admin Panel**: Administrative interface with RBAC, reporting, and system management
- **Unified Component Library**: Standardized components across all layers
- **Cross-Module Integration**: Deep integration between helpdesk and asset loan modules
- **HRMIS Auto-Provisioning**: Automatic user provisioning from HR system

**Key v4.0 Features (PKS Compliance Migration):**

- **SSO Mandatory Authentication**: All users must authenticate via LDAP/Active Directory MOTAC (PKS 5.2.1)
- **Walk-in/Kiosk Mode with SSO**: Replaces Guest Mode - still requires SSO authentication
- **HRMIS Auto-Provisioning**: Automatic account creation from HR system, replaces manual @motac.gov.my registration
- **user_id Mandatory FK**: All activities linked to authenticated users for audit trail
- **DLP Filtering for Cloud AI**: Data Loss Prevention for AWS Bedrock integration (PKS 9.2.1)
- **Intranet-Only Deployment**: System hosted in MOTAC Data Center only (PKS 4.2)
- **Password Policy Compliance**: 8 chars, 90-day expiry, 3 attempts (PKS 5.4.3)
- **Dual Audit System**: Owen-it (compliance) + Spatie (operational) with 7-year retention
- **Bahasa Melayu Exclusive UI**: Complete removal of language switcher, BM-only interface per government directive
- **Theme Switcher**: Light/dark mode toggle with light as immutable default, localStorage persistence, FOUT prevention
- **MyDS Design System Compliance**: Full alignment with Malaysia Government Design System v2025.2
- **Technology Stack v4.0**: Laravel 12.43.1, PHP 8.4.1, Livewire 3.7.3, Filament 4.3.1, Tailwind CSS 4.1.18
- **Four-Role RBAC**: Staff, Approver (Grade 41+), Admin, Superuser with comprehensive authorization
- **Real-Time Features**: Laravel Reverb WebSocket integration for live updates and notifications

## Glossary

- **ICTServe**: ICT Service Management System for MOTAC (Ministry of Tourism, Arts and Culture Malaysia)
- **SSO_Mandatory_Architecture**: System architecture requiring SSO authentication for ALL users via LDAP/Active Directory MOTAC (PKS 5.2.1 compliant) - replaces True Hybrid Architecture
- **Walk_in_Kiosk_Mode**: Access mode for walk-in users at kiosk terminals - still requires SSO authentication (replaces deprecated Guest Mode)
- **HRMIS_Auto_Provisioning**: Automatic user account creation synchronized with HR Management Information System for employment status verification
- **PKS_5_2_1**: Polisi Keselamatan Siber MOTAC Section 5.2.1 - Accountability and Non-repudiation principle requiring all activities to be traceable to authenticated users
- **PKS_9_2_1**: Polisi Keselamatan Siber MOTAC Section 9.2.1 - Data transfer procedures and confidentiality protection requiring DLP filtering for cloud services
- **PKS_4_2**: Polisi Keselamatan Siber MOTAC Section 4.2 - Data sovereignty and jurisdiction requiring intranet-only deployment
- **PKS_5_4_3**: Polisi Keselamatan Siber MOTAC Section 5.4.3 - Password policy (8 chars, 90-day expiry, 3 attempts)
- **PSPM**: Pelan Strategik Pendigitalan MOTAC 2022-2026 - Strategic Digitalization Plan prioritizing MyGovCloud
- **Mandatory_FK_user_id**: Database design pattern where user_id foreign key is required (NOT NULL) for all transactional tables to ensure accountability
- **Dual_Audit_System**: Combined audit approach using owen-it/laravel-auditing (compliance) and spatie/laravel-activitylog (operational) with 7-year retention
- **DLP_Filtering**: Data Loss Prevention filtering applied to data before sending to cloud AI services (AWS Bedrock)
- **Laravel_12**: Laravel 12.43.1 framework with PHP 8.4.1, modern syntax, and enhanced performance (ref: KRISA D10 v4.0)
- **Livewire_3**: Livewire 3.7.3 server-driven UI framework with reactive components (ref: D12 §2, D13 §2.1)
- **Volt_1**: Livewire Volt 1.10.1 single-file component API for simplified development (ref: D13 §2.1)
- **Tailwind_CSS_4**: Tailwind CSS 4.1.18 utility-first CSS framework with @theme configuration (ref: D13 §2.2, D14 §2)
- **Alpine_js_3**: Alpine.js 3.x lightweight JavaScript framework included with Livewire (ref: D12 §2)
- **Filament_4**: Filament 4.3.1 Server-Driven UI (SDUI) admin panel framework (ref: KRISA D10 v4.0)
- **WCAG_2_2_AA**: Web Content Accessibility Guidelines Level AA - 4.5:1 text contrast, 3:1 UI contrast, 44×44px touch targets (ref: D12 §4, D14 §10)
- **MyDS_Design_System**: Malaysia Government Design System v2025.2 - grid, typography, color tokens (ref: D13 §2.2-2.7, D14 §7.4)
- **Figma_MCP**: Model Context Protocol integration for Figma enabling AI-assisted design-to-code workflows
- **Component_Library**: Unified Blade components in accessibility/, data/, form/, layout/, navigation/, responsive/, ui/ categories (ref: D13 §5)
- **OptimizedLivewireComponent**: Performance trait with caching, lazy loading, computed properties (ref: D12 §6.8)
- **Compliant_Color_Palette**: WCAG-compliant MOTAC colors - Primary #0056B3 (7.2:1), Secondary #0B4D8F (8.1:1), Success #1B7C54 (4.6:1), Warning #CC7700 (4.5:1), Danger #B3002D (7.8:1) (ref: D12 §6.5, D14 §4)
- **Core_Web_Vitals**: Performance metrics - LCP <2.5s, FID <100ms, CLS <0.1, TTFB <600ms (ref: D12 §6.8)
- **Cross_Module_Integration**: Deep integration between helpdesk and asset loan modules with shared workflows (ref: D00 §3)
- **Four_Role_RBAC**: Staff, Approver (Grade 41+), Admin, Superuser with Spatie Permission 6.23 (ref: D00 §5.1)
- **Laravel_Reverb**: Laravel Reverb 1.6.3 WebSocket server for real-time features (ref: D00 §4.1, D16)
- **Cloud_Hybrid_AI**: True Hybrid AI Architecture combining Ollama (local LLM) with AWS Bedrock (cloud AI) with DLP filtering (ref: D18 v4.0, PKS 9.2.1)
- **FAQ_Bot**: AI-powered Q&A system accessible via authenticated portal with model routing (ref: D18 §2.3)
- **Multi_Model_Intelligence**: Claude Opus 4.5, Sonnet 4.5, Haiku 4.5, Nova Pro/Lite/Micro for task-specific AI routing (ref: D18 §4.4)
- **Model_Routing**: Smart query classification (FAQ → Ollama, Complex → Bedrock, Hybrid → Both) for cost optimization (ref: D18 §5)
- **Streaming_Responses**: Server-Sent Events (SSE) for responsive AI chat experience (ref: D18 §2.3 - future)
- **Web_Augmented_Responses**: DuckDuckGo integration for current context in AI responses (ref: D18 §3.2.2)
- **Conversation_Management**: Enhanced BedrockConversation model with save/load/delete and long-term memory (ref: D18 §6)
- **Auto_Reply_Generation**: AI-generated response drafts for tickets with admin approval workflow (ref: D18 §3.2.2)
- **Document_Analysis**: AI-powered PDF/DOCX parsing with semantic search and PII detection (ref: D18 §3.2.2)
- **Theme_Switcher**: Light/dark mode toggle with light as immutable default, localStorage persistence, FOUT prevention (ref: D12 §6.10, D14 §6.1.2)
- **BM_Exclusive_UI**: Bahasa Melayu exclusive interface per v3.6.0+ government directive (ref: D15 v4.0)
- **Filament_Panel**: Admin interface at `/admin` with four-role RBAC and comprehensive resource management
- **Authenticated_Portal**: Internal staff portal requiring SSO login for all features
- **Staff_Dashboard**: Personalized dashboard displaying user-specific statistics, recent activity, and quick actions
- **Approval_Interface**: Dedicated interface for Grade 41+ approvers to review and process loan applications
- **Internal_Comments**: Staff-only comments on submissions visible only to authenticated users and admins
- **Real_Time_Tracking**: Live status updates for submissions using Livewire polling and Laravel Echo broadcasting
- **Notification_Center**: Centralized interface for viewing and managing system notifications
- **Export_Functionality**: User ability to export submission history and reports in CSV, PDF formats
- **Activity_Timeline**: Chronological view of all user actions and submission status changes
- **LDAP_Active_Directory**: Microsoft Active Directory / LDAP authentication system used for SSO
- **PKS_10_1**: Polisi Keselamatan Siber MOTAC Section 10.1 - Incident Response Management requiring systematic detection, reporting, and handling of security incidents via CSIRT
- **PKS_10_2**: Polisi Keselamatan Siber MOTAC Section 10.2 - Business Continuity Plan (BCP) and Disaster Recovery Plan (DRP) requirements with RTO/RPO metrics
- **PKS_11_1**: Polisi Keselamatan Siber MOTAC Section 11.1 - Third Party Access Management requiring NDA, time-limited access, and enhanced audit for external vendors
- **PKS_12_1**: Polisi Keselamatan Siber MOTAC Section 12.1 - Security Awareness Training requirements for all users with annual compliance tracking
- **CSIRT**: Cyber Security Incident Response Team MOTAC - dedicated team for incident detection, analysis, and response coordination with NACSA/MyCERT
- **NACSA**: National Cyber Security Agency - national authority for cyber security incident reporting and coordination
- **MyCERT**: Malaysia Computer Emergency Response Team - national CERT for incident reporting and coordination
- **BCP**: Business Continuity Plan - documented procedures ensuring critical business functions continue during disruptions
- **DRP**: Disaster Recovery Plan - technical procedures for recovering IT systems and data after disasters
- **RTO**: Recovery Time Objective - maximum acceptable time to restore service after disruption
- **RPO**: Recovery Point Objective - maximum acceptable data loss measured in time (e.g., last backup)
- **NDA**: Non-Disclosure Agreement - legal agreement required for third-party access to protect confidential information
- **PSPM_Teras_1**: PSPM Strategic Pillar 1 (Aplikasi) - End-to-end digital services and application modernization
- **PSPM_Teras_2**: PSPM Strategic Pillar 2 (Data) - Data governance, integration, and analytics for decision-making
- **PSPM_Teras_3**: PSPM Strategic Pillar 3 (Infrastruktur ICT) - Cloud-ready infrastructure with MyGovCloud prioritization
- **PSPM_Teras_4**: PSPM Strategic Pillar 4 (Tadbir Urus & Keupayaan) - ICT governance and digital capability building
- **MyGovCloud**: Malaysian Government Cloud infrastructure for hosting government digital services

## Requirements

### Requirement 1: Bahasa Melayu Exclusive Interface (v3.6.0)

**User Story:** As a MOTAC staff member, I want the system interface to be exclusively in Bahasa Melayu as per government directive, so that the system aligns with national language policy and provides consistent user experience.

#### Acceptance Criteria

1. THE ICTServe_System SHALL display all user interface elements exclusively in Bahasa Melayu including labels, buttons, messages, notifications, and help text
2. THE ICTServe_System SHALL remove all language switcher components from guest forms, authenticated portal, and admin interfaces
3. THE ICTServe_System SHALL maintain English language files as comments/documentation for technical reference only
4. THE ICTServe_System SHALL use BM-exclusive error messages, validation messages, and system notifications
5. THE ICTServe_System SHALL implement BM-exclusive email templates for all automated communications per D15 v3.6.0 policy

### Requirement 2: Theme Switcher Implementation

**User Story:** As a user with visual preferences, I want a theme switcher that allows me to toggle between light and dark modes, so that I can use the system comfortably in different lighting conditions while maintaining light mode as the professional default.

#### Acceptance Criteria

1. THE ICTServe_System SHALL implement theme switcher with light mode as immutable default (no system preference auto-detection)
2. THE ICTServe_System SHALL persist theme selection in localStorage with 1-year expiration
3. THE ICTServe_System SHALL prevent Flash of Unstyled Content (FOUT) with theme initialization script
4. THE ICTServe_System SHALL maintain WCAG 2.2 AA contrast ratios in both light and dark themes
5. THE ICTServe_System SHALL provide smooth transitions between theme changes with 200ms duration

### Requirement 3: Figma MCP Integration

**User Story:** As a developer, I want Figma MCP integration for design-to-code workflows, so that I can efficiently convert Figma designs to Livewire components while maintaining design consistency.

#### Acceptance Criteria

1. THE ICTServe_System SHALL integrate Figma MCP tools (get_design_context, create_design_system_rules, get_code_connect_map)
2. THE ICTServe_System SHALL transform Figma React/Tailwind output to Livewire/Blade components
3. THE ICTServe_System SHALL map Figma colors to Compliant_Color_Palette tokens automatically
4. THE ICTServe_System SHALL store design system rules in .kiro/steering/design-system.md
5. THE ICTServe_System SHALL maintain visual parity between Figma designs and implemented components

### Requirement 4: MyDS Design System Compliance

**User Story:** As a government system user, I want the interface to comply with Malaysia Design System standards, so that the system provides consistent user experience aligned with government digital services.

#### Acceptance Criteria

1. THE ICTServe_System SHALL implement MyDS color token mapping with semantic tokens (--bg-*, --txt-*, --otl-*, --fr-*)
2. THE ICTServe_System SHALL use MyDS typography system with Poppins for headings and Inter for body text
3. THE ICTServe_System SHALL implement MyDS spacing system with 4px increments from space-1 to space-16
4. THE ICTServe_System SHALL use MyDS radius system (xs: 4px, s: 6px, m: 8px, l: 12px, xl: 14px, full: 9999px)
5. THE ICTServe_System SHALL implement MyDS shadow system (button, card, dropdown shadows)

### Requirement 5: Unified Component Library

**User Story:** As a developer, I want a unified component library across all system layers, so that I can maintain consistency and reduce development time.

#### Acceptance Criteria

1. THE ICTServe_System SHALL organize components in accessibility/, data/, form/, layout/, navigation/, responsive/, ui/ categories
2. THE ICTServe_System SHALL include metadata headers in all components (name, description, WCAG compliance, D00-D17 traceability)
3. THE ICTServe_System SHALL implement consistent naming convention (x-category.component-name)
4. THE ICTServe_System SHALL provide reusable components across guest forms, authenticated portal, and Filament admin
5. THE ICTServe_System SHALL maintain component documentation with usage examples

### Requirement 6: Livewire 3.7 and Volt 1.10 Architecture

**User Story:** As a developer, I want modern Livewire architecture with performance optimizations, so that the system provides responsive user interactions.

#### Acceptance Criteria

1. THE ICTServe_System SHALL implement OptimizedLivewireComponent trait with caching and performance optimizations
2. THE ICTServe_System SHALL use Volt components for simple interactive elements (forms, filters, modals, search)
3. THE ICTServe_System SHALL implement wire:model.live.debounce.300ms for real-time validation
4. THE ICTServe_System SHALL use computed properties with #[Computed] attribute for derived data
5. THE ICTServe_System SHALL provide proper loading states and ARIA live regions for dynamic content

### Requirement 7: WCAG 2.2 AA Accessibility Compliance

**User Story:** As a user with accessibility needs, I want the system to meet WCAG 2.2 Level AA standards, so that I can access all system features regardless of my abilities.

#### Acceptance Criteria

1. THE ICTServe_System SHALL maintain minimum 4.5:1 contrast ratio for text and 3:1 for UI components
2. THE ICTServe_System SHALL implement focus indicators with 3-4px outline, 2px offset, and minimum 3:1 contrast
3. THE ICTServe_System SHALL provide keyboard navigation with logical tab order and skip links
4. THE ICTServe_System SHALL implement ARIA attributes (labels, roles, landmarks, live regions)
5. THE ICTServe_System SHALL ensure minimum 44×44px touch targets for all interactive elements
6. THE ICTServe_System SHALL achieve Lighthouse accessibility score of 100

### Requirement 8: Filament Admin Panel Interface

**User Story:** As an ICT admin, I need a comprehensive admin panel with four-role RBAC, so that I can manage the system efficiently with appropriate access controls.

#### Acceptance Criteria

1. THE Filament_Panel SHALL implement four-role RBAC (Staff, Approver, Admin, Superuser) with 27 permissions
2. THE Filament_Panel SHALL provide comprehensive resource management (HelpdeskTicketResource, LoanApplicationResource, AssetResource, UserResource)
3. THE Filament_Panel SHALL implement unified dashboard with real-time widgets and statistics
4. THE Filament_Panel SHALL provide advanced filtering with deferred filters (Filament 4 default)
5. THE Filament_Panel SHALL support bulk operations with success/failure reporting

### Requirement 9: Authenticated Staff Portal

**User Story:** As a MOTAC staff member, I want a personalized dashboard and portal, so that I can manage my submissions and access role-based features efficiently.

#### Acceptance Criteria

1. THE Authenticated_Portal SHALL display personalized dashboard with statistics cards and real-time updates every 300 seconds
2. THE Authenticated_Portal SHALL provide comprehensive submission history with tabbed interface and advanced filtering
3. THE Authenticated_Portal SHALL implement profile management with notification preferences and security settings
4. THE Authenticated_Portal SHALL provide approval interface for Grade 41+ officers with bulk operations
5. THE Authenticated_Portal SHALL support guest submission claiming via email matching

### Requirement 10: Real-Time Features and Notifications

**User Story:** As a system user, I want real-time updates and notifications, so that I stay informed of important changes without manual refresh.

#### Acceptance Criteria

1. THE ICTServe_System SHALL implement Laravel Reverb WebSocket server for real-time features
2. THE ICTServe_System SHALL provide notification center with unread count and filtering capabilities
3. THE ICTServe_System SHALL broadcast real-time updates for status changes, assignments, and approvals
4. THE ICTServe_System SHALL implement ARIA live regions for screen reader announcements
5. THE ICTServe_System SHALL support email notifications based on user preferences

### Requirement 11: Cross-Module Integration

**User Story:** As a system user, I want seamless integration between helpdesk and asset loan modules, so that I can manage related submissions efficiently.

#### Acceptance Criteria

1. THE ICTServe_System SHALL automatically create maintenance tickets for damaged asset returns within 5 seconds
2. THE ICTServe_System SHALL link related submissions via cross_module_integrations table
3. THE ICTServe_System SHALL display asset information in ticket details and ticket history in asset details
4. THE ICTServe_System SHALL provide unified search across both modules with relevance ranking
5. THE ICTServe_System SHALL maintain referential integrity with foreign key constraints

### Requirement 12: Export and Reporting Functionality

**User Story:** As a system user, I want comprehensive export and reporting capabilities, so that I can analyze data and maintain records offline.

#### Acceptance Criteria

1. THE ICTServe_System SHALL provide export functionality in CSV, Excel, and PDF formats
2. THE ICTServe_System SHALL implement report builder with module selection, date range, and status filters
3. THE ICTServe_System SHALL support automated report scheduling with email delivery
4. THE ICTServe_System SHALL generate WCAG-compliant exports with proper accessibility structure
5. THE ICTServe_System SHALL queue large exports (>1000 records) with progress indicators

### Requirement 13: Performance Optimization

**User Story:** As a system user, I want fast and responsive interfaces, so that I can work efficiently without delays.

#### Acceptance Criteria

1. THE ICTServe_System SHALL achieve Core Web Vitals targets (LCP <2.5s, FID <100ms, CLS <0.1, TTFB <600ms)
2. THE ICTServe_System SHALL implement Redis caching for dashboard statistics (5-minute TTL) and user data (10-minute TTL)
3. THE ICTServe_System SHALL use eager loading to prevent N+1 queries
4. THE ICTServe_System SHALL optimize frontend assets with lazy loading, code splitting, and image optimization
5. THE ICTServe_System SHALL implement skeleton loaders and progressive enhancement

### Requirement 14: Security and Audit Compliance

**User Story:** As a security administrator, I want comprehensive security controls and audit logging, so that the system maintains data integrity and compliance.

#### Acceptance Criteria

1. THE ICTServe_System SHALL implement secure authentication with Laravel Breeze and session management
2. THE ICTServe_System SHALL enforce role-based access control with Laravel policies and middleware
3. THE ICTServe_System SHALL maintain comprehensive audit trails with 7-year retention
4. THE ICTServe_System SHALL implement PDPA 2010 compliance for data handling and user rights
5. THE ICTServe_System SHALL provide security monitoring with real-time alerts for suspicious activities

### Requirement 15: Mobile Optimization and Responsive Design

**User Story:** As a mobile user, I want optimized interfaces for mobile devices, so that I can access system features on any device.

#### Acceptance Criteria

1. THE ICTServe_System SHALL implement responsive design supporting mobile (320px-767px), tablet (768px-1024px), and desktop (1280px+) viewports
2. THE ICTServe_System SHALL provide mobile-optimized navigation with hamburger menu and bottom navigation bar
3. THE ICTServe_System SHALL implement touch-friendly interactions with swipe gestures and pull-to-refresh
4. THE ICTServe_System SHALL optimize mobile performance with reduced data transfer and offline capability
5. THE ICTServe_System SHALL provide floating action button (FAB) for primary actions on mobile
6. THE ICTServe_System SHALL persist theme preference using localStorage with key 'theme' and values 'light'|'dark'
7. THE ICTServe_System SHALL implement FOUT (Flash of Unstyled Theme) prevention via inline JavaScript in `<head>` that applies saved theme before page renders
8. THE ICTServe_System SHALL provide theme toggle in header with ☀️/🌙 icons and smooth 200ms ease-out transition respecting prefers-reduced-motion
9. THE ICTServe_System SHALL maintain WCAG 2.2 AA contrast ratios in both themes (gray-100 on gray-900 = 7:1 for dark mode)

### Requirement 16: Cloud Hybrid AI Chat Interface (D18 v1.0.1)

**User Story:** As a MOTAC staff member, I want an AI-powered chat interface that intelligently routes my queries to the most appropriate AI model (Ollama for FAQ, Bedrock for complex reasoning), so that I can get fast, accurate responses while optimizing system costs.

#### Acceptance Criteria

1. THE ICTServe_System SHALL implement Cloud_Hybrid_AI chat interface accessible at `/ai/chat` for both guest and authenticated users
2. THE ICTServe_System SHALL implement Model_Routing that classifies queries as FAQ (→ Ollama), Complex (→ Bedrock), or Hybrid (→ Both) based on keyword analysis
3. THE ICTServe_System SHALL provide model selection dropdown (Opus 4.5, Sonnet 4.5, Haiku 4.5) for authenticated users with appropriate permissions
4. THE ICTServe_System SHALL implement Conversation_Management with save/load/delete functionality and conversation history persistence
5. THE ICTServe_System SHALL display response source attribution (Ollama, Bedrock, or Hybrid) with confidence scoring
6. THE ICTServe_System SHALL implement Web_Augmented_Responses toggle for DuckDuckGo integration to provide current context
7. THE ICTServe_System SHALL maintain WCAG 2.2 AA compliance for all AI chat components including proper ARIA labels and keyboard navigation
8. THE ICTServe_System SHALL display AI responses in Bahasa Melayu exclusively per D15 v3.6.0+ policy

### Requirement 17: FAQ Bot Widget (D18 v1.0.1)

**User Story:** As a user on any ICTServe page, I want a floating FAQ Bot widget that provides instant AI-powered answers to common questions, so that I can get help without navigating away from my current task.

#### Acceptance Criteria

1. THE ICTServe_System SHALL implement FAQ_Bot widget as a floating button (bottom-right, 44×44px minimum touch target) on all guest and authenticated pages
2. WHEN user clicks the FAQ Bot button, THE ICTServe_System SHALL display an accessible chat panel with ARIA dialog pattern and focus trap
3. THE ICTServe_System SHALL route FAQ queries to Ollama RAG service for fast, cost-effective responses (<5 seconds response time)
4. THE ICTServe_System SHALL display context-aware FAQ suggestions based on current page context (helpdesk, loan, status check)
5. THE ICTServe_System SHALL implement keyboard navigation (ESC to close, Tab navigation within panel) and screen reader support
6. THE ICTServe_System SHALL persist FAQ Bot conversation state within the browser session
7. THE ICTServe_System SHALL implement ARIA live regions for announcing new AI responses to screen readers
8. THE ICTServe_System SHALL display all FAQ Bot content in Bahasa Melayu exclusively

### Requirement 18: AI Admin Management Interface (D18 v1.0.1)

**User Story:** As an admin or superuser, I want a comprehensive AI management interface in Filament, so that I can configure AI models, manage FAQ knowledge base, review auto-reply drafts, and monitor AI system health.

#### Acceptance Criteria

1. THE Filament_Panel SHALL provide AI Dashboard widget displaying real-time metrics (model usage, response times, cost estimates, health status)
2. THE Filament_Panel SHALL implement FaqResource for CRUD operations on FAQ knowledge base with bulk import/export
3. THE Filament_Panel SHALL provide DocumentResource for AI document ingestion with PII detection and semantic search configuration
4. THE Filament_Panel SHALL implement AutoReplyResource for reviewing and approving AI-generated response drafts with approval workflow
5. THE Filament_Panel SHALL provide Model Configuration page for tuning Ollama and Bedrock model parameters (temperature, max_tokens, etc.)
6. THE Filament_Panel SHALL display multi-system health monitoring (Ollama server, AWS Bedrock API, DuckDuckGo integration)
7. THE Filament_Panel SHALL implement conversation analytics with usage patterns and performance insights
8. THE Filament_Panel SHALL restrict AI management features to admin and superuser roles per Four_Role_RBAC

### Requirement 3: Figma MCP Design Integration

**User Story:** As a developer, I want to integrate Figma designs with the ICTServe codebase using Figma MCP, so that I can generate consistent, accessible UI components directly from approved designs.

#### Acceptance Criteria

1. WHEN a developer requests design context from Figma, THE ICTServe_System SHALL use Figma MCP `get_design_context` tool to extract component specifications including layout, colors, typography, and spacing
2. WHEN generating code from Figma designs, THE ICTServe_System SHALL transform React/Tailwind output to Livewire/Blade syntax following existing component patterns in `resources/views/components/`
3. WHEN Figma designs reference colors, THE ICTServe_System SHALL map them to Compliant_Color_Palette tokens defined in `resources/css/app.css` @theme directive
4. THE ICTServe_System SHALL generate design system rules using Figma MCP `create_design_system_rules` tool and store them in `.kiro/steering/design-system.md`
5. WHEN Code Connect mappings exist, THE ICTServe_System SHALL use `get_code_connect_map` to link Figma components to existing Blade/Livewire implementations

### Requirement 4: MyDS Design System Compliance

**User Story:** As a government system, I want ICTServe to comply with MyDS (Malaysia Government Design System) standards, so that the interface aligns with national digital service guidelines.

#### Acceptance Criteria

1. THE ICTServe_System SHALL implement MyDS color token mapping with semantic tokens (--bg-*, --txt-*, --otl-*, --fr-*) as defined in D13 §2.2
2. THE ICTServe_System SHALL use MyDS typography system with Poppins for headings and Inter for body text per D13 §2.4
3. THE ICTServe_System SHALL implement MyDS radius system: xs (4px), s (6px), m (8px), l (12px), xl (14px), full (9999px)
4. THE ICTServe_System SHALL use MyDS spacing system: 4px increments from space-1 (4px) to space-16 (64px)
5. THE ICTServe_System SHALL implement MyDS shadow system: shadow-button, shadow-card, shadow-dropdown per D14 §7.5

### Requirement 5: Unified Component Library Architecture

**User Story:** As a developer maintaining ICTServe, I want a comprehensive component library organized by category, so that all interfaces maintain uniform styling and accessibility standards with maximum reusability.

#### Acceptance Criteria

1. THE ICTServe_System SHALL implement unified Component_Library with standardized categories (accessibility/, data/, form/, layout/, navigation/, responsive/, ui/)
2. THE ICTServe_System SHALL provide component metadata headers with name, description, author, trace references to D00-D17 standards, version history, and WCAG compliance level
3. THE ICTServe_System SHALL use Compliant_Color_Palette exclusively removing deprecated colors and ensuring 95%+ component reuse across all interfaces
4. THE ICTServe_System SHALL implement responsive grid system with MyDS breakpoints (sm: 640px, md: 768px, lg: 1024px, xl: 1280px, 2xl: 1536px)
5. THE ICTServe_System SHALL provide reusable form components with proper ARIA attributes, error handling, and validation states

### Requirement 6: Livewire 3.7 and Volt 1.10 Optimization

**User Story:** As a developer building interactive features, I want optimized Livewire 3.7 and Volt 1.10 components that provide excellent performance and user experience, so that dynamic interfaces are responsive and efficient.

#### Acceptance Criteria

1. THE ICTServe_System SHALL implement Livewire 3.7 components with OptimizedLivewireComponent trait including caching, lazy loading, and query optimization
2. THE ICTServe_System SHALL use Volt_1 components for simplified development with components under 100 lines of PHP logic
3. THE ICTServe_System SHALL implement real-time form validation using wire:model.live.debounce.300ms for dynamic fields and wire:model.lazy for large text fields
4. THE ICTServe_System SHALL provide computed properties (#[Computed]) for derived data and eager loading (with()) to prevent N+1 queries
5. THE ICTServe_System SHALL implement proper loading states, error handling, and ARIA live regions for dynamic content updates

### Requirement 19: Portal Layout System

**User Story:** As an authenticated MOTAC staff member, I want a comprehensive portal layout with role-based navigation and accessibility features, so that I can efficiently access system features appropriate to my role while maintaining government compliance standards.

#### Acceptance Criteria

1. THE ICTServe_System SHALL implement portal layout with header, navbar, sidebar, footer, and main content regions following WCAG 2.2 AA landmark structure
2. THE ICTServe_System SHALL provide role-based navigation with Staff, Approver (Grade 41+), Admin, and Superuser menu items
3. THE ICTServe_System SHALL implement responsive design with collapsible sidebar and mobile-optimized navigation
4. THE ICTServe_System SHALL include accessibility features (skip links, ARIA landmarks, keyboard navigation, focus management)
5. THE ICTServe_System SHALL integrate theme switcher, breadcrumb navigation, and flash message system with ARIA live regions
6. THE ICTServe_System SHALL display MOTAC branding and government compliance footer with proper ISO document references
7. THE ICTServe_System SHALL implement data rights management interface for PDPA 2010 compliance
8. THE ICTServe_System SHALL maintain consistent MyDS Design System styling across all portal components

### Requirement 7: WCAG 2.2 Level AA Accessibility Excellence

**User Story:** As a user with accessibility needs, I want all UI components to meet WCAG 2.2 Level AA standards with new success criteria, so that I can effectively use the system regardless of my abilities.

#### Acceptance Criteria

1. THE ICTServe_System SHALL implement color contrast ratios of 4.5:1 for text and 3:1 for UI components using Compliant_Color_Palette
2. THE ICTServe_System SHALL provide keyboard navigation with visible focus indicators (3-4px outline, 2px offset, 3:1 contrast minimum)
3. THE ICTServe_System SHALL implement skip links for main content and navigation with proper focus management
4. THE ICTServe_System SHALL use semantic HTML5 structure with ARIA landmarks (banner, navigation, main, contentinfo) and proper heading hierarchy
5. THE ICTServe_System SHALL implement touch targets of minimum 44×44px for all interactive elements and ARIA live regions with appropriate politeness levels
6. THE ICTServe_System SHALL comply with WCAG 2.2 new success criteria: SC 2.4.11 Focus Not Obscured, SC 2.5.8 Target Size (Minimum), SC 3.2.6 Consistent Help

### Requirement 8: Core Web Vitals Performance Optimization

**User Story:** As any user accessing ICTServe, I want fast-loading pages with optimized rendering, so that I can access services quickly regardless of device or connection speed.

#### Acceptance Criteria

1. THE ICTServe_System SHALL achieve Core_Web_Vitals targets: LCP <2.5s, FID <100ms, CLS <0.1, TTFB <600ms
2. THE ICTServe_System SHALL implement Livewire optimization with OptimizedLivewireComponent trait, computed properties, lazy loading, and debounced input handling (300ms)
3. THE ICTServe_System SHALL use Redis caching for dashboard statistics (5-minute cache) and asset availability (5-minute cache)
4. THE ICTServe_System SHALL achieve Lighthouse scores: Performance 90+, Accessibility 100, Best Practices 100, SEO 100
5. THE ICTServe_System SHALL implement code splitting and lazy loading for non-critical components with image optimization (WebP format, JPEG fallbacks, lazy loading)

### Requirement 9: True Hybrid Architecture Frontend

**User Story:** As a MOTAC staff member, I want flexible access options through guest forms or authenticated portal, so that I can choose the most appropriate method based on my immediate needs and context.

#### Acceptance Criteria

1. THE ICTServe_System SHALL implement True_Hybrid_Architecture supporting guest-only forms (no authentication), authenticated portal (staff login), and admin panel (Filament 4) with consistent navigation and branding
2. WHEN a user accesses guest forms, THE ICTServe_System SHALL provide helpdesk ticket submission and asset loan application forms without authentication requirements
3. WHEN an authenticated user accesses the portal, THE ICTServe_System SHALL display personalized dashboard with statistics (My Open Tickets, My Pending Loans, My Approvals for Grade 41+, Overdue Items)
4. THE ICTServe_System SHALL provide seamless navigation between guest and authenticated modes with clear visual indicators of current access level
5. THE ICTServe_System SHALL maintain MOTAC branding consistency with Jata Negara and MOTAC logos, typography, and visual identity across all interfaces

### Requirement 10: Cross-Module Integration and Unified Dashboard

**User Story:** As an admin or authenticated user, I want unified dashboards and cross-module integration that combines helpdesk and asset loan data, so that I can monitor system performance and access integrated analytics.

#### Acceptance Criteria

1. THE ICTServe_System SHALL implement unified admin dashboard combining helpdesk metrics (ticket volume, SLA compliance) and asset loan metrics (utilization, overdue items)
2. THE ICTServe_System SHALL provide Cross_Module_Integration search functionality across tickets and loan applications with unified results
3. THE ICTServe_System SHALL implement asset-ticket integration for hardware-related issues using asset_id foreign key relationships
4. WHEN asset condition changes to damaged, THE ICTServe_System SHALL create automatic maintenance workflows with helpdesk ticket integration within 5 seconds
5. THE ICTServe_System SHALL provide integrated reporting combining data from both modules with export functionality (CSV, PDF, Excel)

### Requirement 11: Enhanced Guest Forms with Accessibility

**User Story:** As a MOTAC staff member or external user, I want to access ICT services through enhanced guest forms without requiring user accounts, so that I can quickly submit tickets and loan applications with full accessibility support.

#### Acceptance Criteria

1. WHEN a user accesses guest forms, THE ICTServe_System SHALL provide helpdesk ticket submission and asset loan application forms without authentication requirements
2. WHEN a user interacts with guest forms, THE ICTServe_System SHALL implement WCAG_2_2_AA compliance with proper contrast ratios and touch targets
3. WHEN a user navigates guest forms, THE ICTServe_System SHALL provide keyboard navigation with visible focus indicators and proper tab order
4. WHEN a user submits guest forms, THE ICTServe_System SHALL send confirmation emails within 60 seconds with tracking links and application numbers
5. WHERE users require assistive technology, THE ICTServe_System SHALL provide proper ARIA landmarks, semantic HTML5 structure, and screen reader support

### Requirement 12: Authenticated Portal with Role-Based Features

**User Story:** As an authenticated MOTAC staff member, I want a personalized portal with role-based features and submission management, so that I can access my dashboard, manage submissions, and perform approvals based on my role.

#### Acceptance Criteria

1. WHEN an authenticated user accesses the portal, THE ICTServe_System SHALL display personalized dashboard with real-time statistics using Laravel Reverb WebSocket
2. WHEN an authenticated user views submission history, THE ICTServe_System SHALL display both claimed guest submissions and authenticated submissions with filtering, sorting, and search capabilities
3. WHEN an authenticated user manages profile, THE ICTServe_System SHALL provide editable contact information, notification preferences, and theme settings
4. WHEN a Grade 41+ user accesses approvals, THE ICTServe_System SHALL provide approval interface with loan request details, approval/rejection actions, and bulk operations
5. WHERE users claim guest submissions, THE ICTServe_System SHALL provide email verification and account linking functionality for historical submissions

### Requirement 13: Email-Based Workflows and Notifications

**User Story:** As a user interacting with ICTServe, I want reliable email-based workflows with timely notifications in Bahasa Melayu, so that I can stay informed of status changes and complete approvals efficiently.

#### Acceptance Criteria

1. THE ICTServe_System SHALL implement comprehensive email workflows with MOTAC branding, BM_Exclusive_UI templates, and WCAG_2_2_AA compliance
2. WHEN status changes occur, THE ICTServe_System SHALL send automated email notifications within 60 seconds using queue-based processing with Redis
3. THE ICTServe_System SHALL implement secure email approval system with token-based links (7-day expiration) and approval processing endpoints
4. THE ICTServe_System SHALL provide email templates in Bahasa Melayu for confirmation, approval requests, status updates, reminders, and SLA breach alerts
5. THE ICTServe_System SHALL implement retry mechanism (3 attempts with exponential backoff) and email delivery tracking with failure handling

### Requirement 14: Responsive Design and Mobile Optimization

**User Story:** As a user accessing ICTServe from various devices, I want a responsive interface that adapts to my screen size, so that I can use the system effectively on mobile, tablet, and desktop.

#### Acceptance Criteria

1. THE ICTServe_System SHALL implement MyDS responsive grid system: 12 columns (desktop ≥1024px), 8 columns (tablet 768-1023px), 4 columns (mobile ≤767px)
2. WHEN viewport is mobile (<768px), THE ICTServe_System SHALL transform tables to card views with stacked information
3. THE ICTServe_System SHALL implement responsive navigation: sidebar on desktop, hamburger menu on mobile/tablet
4. THE ICTServe_System SHALL use responsive typography with fluid scaling between breakpoints
5. THE ICTServe_System SHALL optimize images with WebP format, JPEG fallbacks, explicit dimensions, and lazy loading

### Requirement 15: Real-time Features with Laravel Reverb

**User Story:** As an authenticated user, I want real-time notifications and updates that inform me of important changes, so that I can respond promptly to status changes and approvals.

#### Acceptance Criteria

1. THE ICTServe_System SHALL implement notification bell icon in navbar with unread count badge using Laravel Reverb WebSocket
2. WHEN a notification is received, THE ICTServe_System SHALL display toast notification with 400ms slideInUp animation per MyDS motion system
3. THE ICTServe_System SHALL implement notification dropdown with categorized list (Tickets, Loans, System) and mark-as-read functionality
4. THE ICTServe_System SHALL provide notification preferences panel allowing users to configure email frequency and in-app notification types
5. THE ICTServe_System SHALL implement ARIA live regions with appropriate politeness levels for screen reader announcements

### Requirement 16: Security and Audit Compliance

**User Story:** As a system administrator, I want comprehensive security measures and audit compliance that protect sensitive data and maintain regulatory compliance, so that the system meets PDPA 2010 and government security standards.

#### Acceptance Criteria

1. THE ICTServe_System SHALL implement Four_Role_RBAC (Staff, Approver, Admin, Superuser) with proper authorization policies and middleware protection
2. THE ICTServe_System SHALL provide comprehensive audit trail using Laravel Auditing with 7-year retention for all models and administrative actions
3. THE ICTServe_System SHALL implement data encryption for sensitive information (approval tokens, personal data) using AES-256 encryption at rest
4. THE ICTServe_System SHALL enforce security measures including CSRF protection, rate limiting (60 req/min for guest forms), input validation, and secure headers
5. THE ICTServe_System SHALL maintain PDPA 2010 compliance with data retention policies, subject rights (access, correction, deletion), and privacy protection

### Requirement 17: Advanced UI Components and Interactions

**User Story:** As a user interacting with the system, I want advanced UI components that provide smooth interactions and clear feedback, so that I can efficiently complete tasks with confidence.

#### Acceptance Criteria

1. THE ICTServe_System SHALL provide modal component with focus trap, escape key close, and aria-modal="true"
2. THE ICTServe_System SHALL implement dropdown component with aria-expanded, aria-haspopup, and keyboard navigation
3. THE ICTServe_System SHALL provide form wizard component with progress indicator, step validation, and keyboard navigation
4. THE ICTServe_System SHALL implement loading states with skeleton screens, button loading states, and progress indicators
5. THE ICTServe_System SHALL provide toast notifications with auto-dismiss (5 seconds), ARIA live regions, and animation respecting prefers-reduced-motion

### Requirement 18: Data Visualization and Dashboard Widgets

**User Story:** As an admin or staff user, I want clear data visualizations that help me understand system status, so that I can make informed decisions and track performance.

#### Acceptance Criteria

1. THE ICTServe_System SHALL implement statistics cards with icon, count, label, and trend indicator using shadow-card styling
2. THE ICTServe_System SHALL implement SLA compliance gauge/progress bar with color-coded status (green >90%, yellow 70-90%, red <70%)
3. THE ICTServe_System SHALL implement ticket/loan status distribution chart with accessible color palette and data labels
4. THE ICTServe_System SHALL implement recent activity timeline with icon, timestamp, description, and action links
5. THE ICTServe_System SHALL implement Laravel Pulse dashboard integration for superuser performance monitoring

### Requirement 19: Form Components and Validation Enhancement

**User Story:** As a user filling out forms, I want enhanced form components with helpful validation and clear guidance, so that I can complete submissions accurately and efficiently.

#### Acceptance Criteria

1. THE ICTServe_System SHALL implement form inputs with proper labels, required indicators (*), and aria-describedby for hints/errors
2. THE ICTServe_System SHALL implement real-time validation using wire:model.live.debounce.300ms with inline error messages in Bahasa Melayu
3. THE ICTServe_System SHALL implement file upload component with drag-and-drop, progress indicator, and file type/size validation
4. THE ICTServe_System SHALL implement multi-step wizard component with progress indicator, step validation, and navigation controls
5. THE ICTServe_System SHALL implement form autosave using LocalStorage for draft preservation with recovery prompt

### Requirement 20: Testing and Quality Assurance Excellence

**User Story:** As a quality assurance engineer, I want comprehensive testing coverage and automated quality checks that ensure system reliability and accessibility, so that all features work correctly across different browsers and devices.

#### Acceptance Criteria

1. THE ICTServe_System SHALL implement comprehensive test suite with unit tests (business logic, models), feature tests (user workflows), and integration tests (cross-module functionality)
2. THE ICTServe_System SHALL achieve minimum 80% overall code coverage and 95% coverage for critical paths (guest submissions, approvals, cross-module integration)
3. THE ICTServe_System SHALL provide automated accessibility testing with Lighthouse (100 score), axe DevTools, and manual screen reader testing (NVDA, JAWS, VoiceOver)
4. THE ICTServe_System SHALL implement cross-browser testing for Chrome 90+, Firefox 88+, Safari 14+, Edge 90+ with automated validation
5. THE ICTServe_System SHALL provide mobile device testing for responsive design across mobile (320px-414px), tablet (768px-1024px), and desktop (1280px-1920px) viewports

## Standards Compliance Mapping

### D00-D17 Framework Alignment

- **D00 System Overview**: True Hybrid Architecture with guest, authenticated, and admin access levels
- **D03 Software Requirements**: Functional requirements for helpdesk, asset loan, and cross-module integration
- **D04 Software Design**: Component architecture, Livewire/Volt patterns, and integration design
- **D12 UI/UX Design Guide**: Layout patterns, interaction design, and component library
- **D13 Frontend Framework**: Laravel 12, Livewire 3, Volt, Tailwind CSS 4, and Vite 7
- **D14 UI/UX Style Guide**: MOTAC branding, compliant color palette, typography, and accessibility
- **D15 Language Support**: Bahasa Melayu exclusive per v3.6.0 policy (language switcher removed)

### WCAG 2.2 Level AA Compliance

- **SC 1.3.1 Info and Relationships**: Semantic HTML5 and ARIA landmarks
- **SC 1.4.3 Contrast (Minimum)**: 4.5:1 text, 3:1 UI components
- **SC 1.4.11 Non-text Contrast**: 3:1 for UI components and graphics
- **SC 2.1.1 Keyboard**: Full keyboard accessibility with logical tab order
- **SC 2.4.7 Focus Visible**: Visible focus indicators with 3:1 contrast minimum
- **SC 2.4.11 Focus Not Obscured (NEW)**: Focus not hidden by other content
- **SC 2.5.8 Target Size (Minimum) (NEW)**: 44×44px minimum touch targets
- **SC 3.2.6 Consistent Help (NEW)**: Consistent help mechanisms
- **SC 4.1.3 Status Messages**: ARIA live regions for dynamic content

### MyDS Design System Compliance

- **Color System**: Semantic tokens (--bg-*, --txt-*, --otl-*, --fr-*)
- **Typography**: Poppins for headings, Inter for body text
- **Grid System**: 12-8-4 responsive grid aligned with government standards
- **Spacing System**: 4px increments from space-1 to space-16
- **Shadow System**: Button, card, and dropdown shadows per MyDS specifications
- **Motion System**: Purposeful animations with accessibility considerations

### Performance Standards

- **Core Web Vitals**: LCP <2.5s, FID <100ms, CLS <0.1, TTFB <600ms
- **Lighthouse Scores**: Performance 90+, Accessibility 100, Best Practices 100, SEO 100
- **Image Optimization**: WebP format with JPEG fallbacks, lazy loading, responsive images
- **Asset Optimization**: Vite bundling, code splitting, compression, minification
- **Caching Strategy**: Redis for sessions, application cache, and query results

## Success Criteria

The comprehensive frontend redesign will be considered successful when:

1. **Bahasa Melayu Exclusive**: Complete BM-only interface with removed language switcher per v3.6.0 policy
2. **Theme Switcher**: Functional light/dark mode toggle with light as immutable default and FOUT prevention
3. **MyDS Compliance**: Full alignment with Malaysia Government Design System v2025.2
4. **Figma Integration**: Working design-to-code workflow using Figma MCP
5. **Unified Architecture**: Consistent frontend architecture across guest forms, authenticated portal, and admin interfaces
6. **Accessibility Excellence**: 100% WCAG 2.2 Level AA compliance with new success criteria
7. **Performance Targets**: Core Web Vitals achieved on all pages with Lighthouse scores of 90+ Performance, 100 Accessibility
8. **Component Reusability**: Unified component library with 95%+ reuse across all interfaces
9. **Cross-Module Integration**: Seamless integration between helpdesk and asset loan modules
10. **Testing Coverage**: Comprehensive test coverage with 80%+ overall coverage and 95%+ for critical paths
11. **Documentation**: Complete documentation with D00-D17 compliance and user guides in Bahasa Melayu
12. **Production Readiness**: Successful deployment with monitoring, performance tracking, and user acceptance validation

---

**Document Version**: 1.0  
**Last Updated**: 2025-12-11  
**Author**: Frontend Engineering Team  
**Status**: Ready for Design Phase  
**Technology Stack**: Laravel 12 | Livewire 3.7 | Volt 1.10 | Tailwind CSS 4.1 | Alpine.js 3.x | Filament 4.1  
**Consolidates**: figma-ui-redesign, frontend-modernization, frontend-pages-redesign, navigation-redesign-v3.6, theme-switcher-fix, updated-frontend

1. THE ICTServe_System SHALL implement visual regression testing with Playwright screenshots for all critical pages across light/dark themes and responsive breakpoints

---

## New Requirements (Based on FRONTPAGE_DESIGN_ANALYSIS v3.6.0-r5)

### Requirement 21: Landing Page (Frontpage) Design Compliance

**User Story:** As a MOTAC staff member visiting the ICTServe landing page, I want a professional, accessible interface that clearly presents available services, so that I can quickly access helpdesk or loan application features.

#### Acceptance Criteria

1. THE ICTServe_System SHALL implement landing page with hybrid color scheme: dark hero section (Primary-600/700) transitioning to light services section (Gray-50/White)
2. THE ICTServe_System SHALL display MOTAC branding with Jata Negara logo, "ICTServe" title, and "Sistem Perkhidmatan ICT" subtitle in hero section
3. THE ICTServe_System SHALL provide two primary CTA buttons ("Buat Aduan", "Mohon Pinjaman") with minimum 44×44px touch targets and

officer accesses the approval interface, THE ICTServe_System SHALL display pending loan applications with filtering, sorting, and bulk approval/rejection capabilities
5. WHEN an authenticated user claims guest submissions, THE ICTServe_System SHALL match submissions by email address and link them to the user account with audit logging

### Requirement 13: Filament Admin Panel with Four-Role RBAC

**User Story:** As an ICT administrator, I want a comprehensive admin panel with role-based access control, so that I can manage the system efficiently with appropriate permissions for each role.

#### Acceptance Criteria

1. THE Filament_Panel SHALL implement four-role RBAC (Staff, Approver, Admin, Superuser) with 27 granular permissions
2. THE Filament_Panel SHALL provide comprehensive resource management including HelpdeskTicketResource, LoanApplicationResource, AssetResource, and UserResource
3. THE Filament_Panel SHALL implement unified dashboard with real-time widgets refreshing every 300 seconds
4. THE Filament_Panel SHALL provide advanced filtering with deferred filters (Filament 4 default behavior)
5. THE Filament_Panel SHALL support bulk operations with success/failure reporting and audit logging

### Requirement 14: Security and Audit Compliance

**User Story:** As a security administrator, I want comprehensive security controls and audit logging, so that the system maintains data integrity and compliance with government regulations.

#### Acceptance Criteria

1. THE ICTServe_System SHALL implement secure authentication with Laravel Breeze and session management with 30-minute timeout
2. THE ICTServe_System SHALL enforce role-based access control with Laravel policies and middleware
3. THE ICTServe_System SHALL maintain comprehensive audit trails with 7-year retention using dual audit system (owen-it + spatie)
4. THE ICTServe_System SHALL implement PDPA 2010 compliance for data handling including consent management and data subject rights
5. THE ICTServe_System SHALL provide security monitoring with real-time alerts for suspicious activities

### Requirement 15: Mobile Optimization and Responsive Design

**User Story:** As a mobile user, I want optimized interfaces for mobile devices, so that I can access system features on any device with touch-friendly interactions.

#### Acceptance Criteria

1. THE ICTServe_System SHALL implement responsive design supporting mobile (320px-767px), tablet (768px-1024px), and desktop (1280px+) viewports
2. THE ICTServe_System SHALL provide mobile-optimized navigation with hamburger menu and bottom navigation bar
3. THE ICTServe_System SHALL implement touch-friendly interactions with swipe gestures and pull-to-refresh
4. THE ICTServe_System SHALL optimize mobile performance with reduced data transfer and offline capability
5. THE ICTServe_System SHALL provide floating action button (FAB) for primary actions on mobile with 44×44px minimum touch targets

---

## Requirements from FRONTPAGE_DESIGN_ANALYSIS_v3.6.0

### Requirement 16: Landing Page (Frontpage) Design Compliance

**User Story:** As a MOTAC staff member visiting the ICTServe landing page, I want a professional, accessible interface that clearly presents available services, so that I can quickly access helpdesk and loan application features.

#### Acceptance Criteria

1. THE ICTServe_Landing_Page SHALL display MOTAC branding with Jata Negara logo and consistent visual identity per D00 §11
2. THE ICTServe_Landing_Page SHALL implement hybrid color scheme with dark hero section and light services section maintaining WCAG 2.2 AA contrast ratios
3. THE ICTServe_Landing_Page SHALL provide clear CTA buttons ("Buat Aduan", "Mohon Pinjaman") with minimum 44×44px touch targets
4. THE ICTServe_Landing_Page SHALL include reference number search functionality with proper form labels and ARIA attributes
5. THE ICTServe_Landing_Page SHALL display ISO document reference PK.(S).MOTAC.07.(L1) per government compliance requirements

### Requirement 17: FAQ Bot ICTServe (Floating Chatbox)

**User Story:** As a user seeking help, I want an AI-powered FAQ chatbot that provides instant assistance in Bahasa Melayu, so that I can get answers to common questions without waiting for support staff.

#### Acceptance Criteria

1. THE FAQ_Bot_Widget SHALL implement floating chatbox with toggle button at bottom-right corner using Primary-600 (#0056B3) background
2. THE FAQ_Bot_Widget SHALL provide AI-powered responses using Ollama (local LLM) or Amazon Bedrock (cloud AI) backend
3. THE FAQ_Bot_Widget SHALL implement WCAG 2.2 AA compliance with `role="dialog"`, `aria-modal="true"`, `aria-labelledby`, and `aria-live="polite"` for screen reader announcements
4. THE FAQ_Bot_Widget SHALL provide header action buttons (minimize, close) with minimum 44×44px touch targets (`min-h-11 min-w-11`)
5. THE FAQ_Bot_Widget SHALL support keyboard navigation with ESC to close and Tab navigation within the dialog

### Requirement 18: Modal Dialog for Service Selection (True Hybrid Architecture)

**User Story:** As a user clicking service buttons, I want a clear modal dialog explaining guest vs authenticated options, so that I can choose the appropriate submission method.

#### Acceptance Criteria

1. THE Service_Modal SHALL display "Adakah anda sudah log masuk?" prompt with clear explanation of guest vs authenticated options
2. THE Service_Modal SHALL provide "Tidak (Tetamu)" and "Ya (Log Masuk)" buttons with proper focus management
3. THE Service_Modal SHALL include "Maklumat Penting" info box explaining benefits of each option in Bahasa Melayu
4. THE Service_Modal SHALL implement `role="dialog"`, `aria-modal="true"`, and focus trap for accessibility compliance
5. THE Service_Modal SHALL support dismissal via ESC key, close button, and backdrop click

### Requirement 19: Status Check Page Compliance

**User Story:** As a user checking submission status, I want a fully translated status check page with proper form validation, so that I can track my helpdesk tickets and loan applications.

#### Acceptance Criteria

1. THE Status_Check_Page SHALL display all UI elements in Bahasa Melayu with no missing translation keys
2. THE Status_Check_Page SHALL provide token input with `wire:model.live.debounce.300ms` for real-time validation
3. THE Status_Check_Page SHALL implement dropdown for submission type selection (auto-detect, helpdesk ticket, loan application)
4. THE Status_Check_Page SHALL display Quick Help sidebar with properly translated contact information and help links
5. THE Status_Check_Page SHALL support both light and dark themes with WCAG 2.2 AA compliant contrast ratios

### Requirement 20: Guest Helpdesk Form (Multi-Step Wizard)

**User Story:** As a guest user submitting a helpdesk ticket, I want a multi-step wizard form with clear progress indication, so that I can complete the submission process efficiently.

#### Acceptance Criteria

1. THE Guest_Helpdesk_Form SHALL implement 3-step wizard (Personal Info, Issue Details, Declaration) with visual progress indicator
2. THE Guest_Helpdesk_Form SHALL provide searchable division select dropdown for Malaysian government organizational structure
3. THE Guest_Helpdesk_Form SHALL implement Malaysian government grade system (Gred 1-56, JUSA, Turus) in job grade selection
4. THE Guest_Helpdesk_Form SHALL require mandatory declaration (Perakuan) with bilingual legal text (BM + EN) and checkbox acceptance
5. THE Guest_Helpdesk_Form SHALL implement Optimistic UI pattern with immediate feedback and rollback mechanism on error
6. THE Guest_Helpdesk_Form SHALL display ISO document reference PK.(S).MOTAC.07.(L1) per government compliance requirements

### Requirement 21: Translation Key Completeness

**User Story:** As a system administrator, I want all translation keys properly defined in language files, so that no raw translation keys are displayed to users.

#### Acceptance Criteria

1. THE ICTServe_System SHALL define all translation keys in `lang/ms/status.php` including `page_tagline`, `quick_help_title`, `quick_help_email`, `quick_help_phone`, `quick_help_ticket`, `quick_help_ticket_cta`
2. THE ICTServe_System SHALL validate translation key existence during build process to prevent missing translations in production
3. THE ICTServe_System SHALL provide fallback text for any missing translation keys to prevent raw key display
4. THE ICTServe_System SHALL maintain translation key consistency between `lang/ms/` and `lang/en/` directories for technical reference
5. THE ICTServe_System SHALL use `__()` helper function consistently for all user-facing text

---

## Implementation Status Summary

### Completed Requirements (v3.6.0-r6)

| Requirement | Status | Notes |
|-------------|--------|-------|
| Req 1: BM Exclusive Interface | ✅ Complete | Language switcher removed, BM-only enforced |
| Req 2: Theme Switcher | ✅ Complete | Light/dark mode with localStorage persistence |
| Req 4: MyDS Design System | ✅ Complete | Full token mapping implemented |
| Req 5: Unified Component Library | ✅ Complete | All component categories created |
| Req 6: Livewire 3.7/Volt 1.10 | ✅ Complete | OptimizedLivewireComponent trait implemented |
| Req 7: WCAG 2.2 AA | ✅ Complete | 100% Lighthouse accessibility score |
| Req 8: Filament Admin Panel | ✅ Complete | Four-role RBAC implemented |
| Req 9: Authenticated Portal | ✅ Complete | Dashboard, history, profile, approvals |
| Req 10: Real-Time Features | ✅ Complete | Laravel Reverb WebSocket integration |
| Req 11: Cross-Module Integration | ✅ Complete | Auto-maintenance tickets, unified search |
| Req 12: Export/Reporting | ✅ Complete | CSV, Excel, PDF with scheduling |
| Req 13: Performance | ✅ Complete | Core Web Vitals targets met |
| Req 14: Security/Audit | ✅ Complete | PDPA compliance, 7-year retention |
| Req 15: Mobile Optimization | ✅ Complete | Responsive design, touch targets |
| Req 16: Landing Page | ✅ Complete | Hybrid color scheme, MOTAC branding |
| Req 17: FAQ Bot | ✅ Complete | Touch targets fixed (44×44px) |
| Req 18: Service Modal | ✅ Complete | True Hybrid Architecture support |
| Req 19: Status Check Page | 🟡 Partial | Missing translation keys identified |
| Req 20: Guest Helpdesk Form | ✅ Complete | Multi-step wizard, Optimistic UI |
| Req 21: Translation Keys | 🟡 Partial | 6 keys missing in status.php |

### Pending Items

1. **Translation Keys (Req 21)**: Add missing keys to `lang/ms/status.php`:
   - `page_tagline`
   - `quick_help_title`
   - `quick_help_email`
   - `quick_help_phone`
   - `quick_help_ticket`
   - `quick_help_ticket_cta`

2. **Figma MCP Integration (Req 3)**: Optional design-to-code workflow setup

---

## PKS Compliance Requirements (v4.0.0 - KRISA D01-D10, D17 v4.0)

### Requirement 22: SSO Mandatory Authentication (PKS 5.2.1)

**User Story:** As a MOTAC security administrator, I want all system access to require SSO authentication via LDAP/Active Directory, so that every user action is traceable and accountable per PKS 5.2.1 Accountability and Non-repudiation principle.

#### Acceptance Criteria

1. THE ICTServe_System SHALL require SSO authentication via LDAP/Active Directory MOTAC for ALL users including walk-in/kiosk users (PKS 5.2.1)
2. THE ICTServe_System SHALL eliminate Guest Mode completely - no anonymous access to any system features
3. THE ICTServe_System SHALL enforce user_id as mandatory foreign key (NOT NULL) for all transactional tables (helpdesk_tickets, loan_applications, audit_logs)
4. THE ICTServe_System SHALL implement session management with 30-minute timeout and automatic re-authentication prompt
5. THE ICTServe_System SHALL log all authentication events (login, logout, failed attempts) with timestamp, IP address, and user agent for audit trail

### Requirement 23: Walk-in/Kiosk Mode with SSO

**User Story:** As a walk-in user at a MOTAC kiosk terminal, I want to quickly authenticate using my MOTAC credentials to submit helpdesk tickets or loan applications, so that I can access services while maintaining full accountability.

#### Acceptance Criteria

1. THE ICTServe_System SHALL provide Walk-in/Kiosk Mode interface optimized for shared terminal usage with SSO authentication
2. WHEN a walk-in user accesses kiosk terminal, THE ICTServe_System SHALL display SSO login prompt before any form access
3. THE ICTServe_System SHALL implement automatic session termination after 5 minutes of inactivity on kiosk terminals
4. THE ICTServe_System SHALL pre-populate user information (name, email, department, grade) from LDAP/Active Directory after authentication
5. THE ICTServe_System SHALL provide "Log Keluar" (Logout) button prominently displayed for kiosk users to end session

### Requirement 24: HRMIS Auto-Provisioning

**User Story:** As a MOTAC HR administrator, I want user accounts to be automatically provisioned from HRMIS, so that new employees have immediate system access and terminated employees are automatically deactivated.

#### Acceptance Criteria

1. THE ICTServe_System SHALL synchronize user accounts with HRMIS (HR Management Information System) for automatic provisioning
2. WHEN a new employee is added to HRMIS, THE ICTServe_System SHALL create corresponding user account within 24 hours
3. WHEN an employee is terminated in HRMIS, THE ICTServe_System SHALL deactivate user account within 24 hours
4. THE ICTServe_System SHALL update user information (department, grade, position) from HRMIS on daily synchronization
5. THE ICTServe_System SHALL eliminate manual @motac.gov.my registration - all accounts provisioned via HRMIS only

### Requirement 25: DLP Filtering for Cloud AI (PKS 9.2.1)

**User Story:** As a security administrator, I want Data Loss Prevention filtering applied to all data sent to cloud AI services, so that sensitive government information is protected per PKS 9.2.1 data transfer procedures.

#### Acceptance Criteria

1. THE ICTServe_System SHALL implement DLP filtering for all data sent to AWS Bedrock cloud AI service (PKS 9.2.1)
2. THE ICTServe_System SHALL detect and redact PII (IC numbers, phone numbers, addresses) before cloud AI transmission
3. THE ICTServe_System SHALL route sensitive queries to local Ollama LLM instead of cloud AI when PII is detected
4. THE ICTServe_System SHALL log all cloud AI requests with data classification (public, internal, confidential) for audit
5. THE ICTServe_System SHALL implement secure API gateway with TLS 1.3 encryption for all cloud AI communications

### Requirement 26: Intranet-Only Deployment (PKS 4.2)

**User Story:** As a MOTAC IT administrator, I want the system deployed exclusively on MOTAC intranet infrastructure, so that data sovereignty and jurisdiction requirements are met per PKS 4.2.

#### Acceptance Criteria

1. THE ICTServe_System SHALL be deployed exclusively in MOTAC Data Center on intranet infrastructure (PKS 4.2)
2. THE ICTServe_System SHALL reject all external network access attempts with appropriate error message
3. THE ICTServe_System SHALL store all data (database, files, logs) within MOTAC Data Center only
4. THE ICTServe_System SHALL use local Ollama LLM for AI features requiring data sovereignty
5. THE ICTServe_System SHALL implement network segmentation with firewall rules restricting access to MOTAC IP ranges only

### Requirement 27: Password Policy Compliance (PKS 5.4.3)

**User Story:** As a security administrator, I want password policies enforced per PKS 5.4.3 requirements, so that user accounts are protected with strong authentication.

#### Acceptance Criteria

1. THE ICTServe_System SHALL enforce minimum 8-character password length (PKS 5.4.3)
2. THE ICTServe_System SHALL require password change every 90 days with notification 14 days before expiry
3. THE ICTServe_System SHALL lock user account after 3 consecutive failed login attempts for 30 minutes
4. THE ICTServe_System SHALL prevent password reuse for last 12 passwords
5. THE ICTServe_System SHALL display password policy requirements on password change screen in Bahasa Melayu

### Requirement 28: Dual Audit System with 7-Year Retention

**User Story:** As a compliance officer, I want comprehensive audit logging with dual audit system, so that all user activities are traceable for 7 years per government retention requirements.

#### Acceptance Criteria

1. THE ICTServe_System SHALL implement dual audit system using owen-it/laravel-auditing (compliance) and spatie/laravel-activitylog (operational)
2. THE ICTServe_System SHALL retain all audit logs for minimum 7 years per government compliance requirements
3. THE ICTServe_System SHALL log all CRUD operations on transactional tables with before/after values
4. THE ICTServe_System SHALL include user_id, IP address, user agent, and timestamp in all audit records
5. THE ICTServe_System SHALL provide audit log search and export functionality for compliance reporting

### Requirement 29: Incident Response Management (PKS 10.1)

**User Story:** As a CSIRT team member, I want a comprehensive incident response interface, so that security incidents can be detected, reported, and managed efficiently per PKS 10.1 requirements.

#### Acceptance Criteria

1. THE ICTServe_System SHALL provide incident reporting form accessible to all authenticated users with mandatory fields (incident type, severity, description, affected systems)
2. WHEN a security incident is reported, THE ICTServe_System SHALL automatically notify CSIRT team members via email and in-app notification within 5 minutes
3. THE ICTServe_System SHALL implement incident classification (Tinggi/Sederhana/Rendah) based on PKS severity matrix
4. THE ICTServe_System SHALL track incident lifecycle (Pengesanan → Pelaporan → Penilaian → Pembendungan → Pemulihan → Penutupan → Laporan)
5. THE ICTServe_System SHALL generate incident reports with timeline, actions taken, and lessons learned for NACSA/MyCERT reporting
6. THE ICTServe_System SHALL maintain incident history with 7-year retention for compliance audit

### Requirement 30: Business Continuity and Disaster Recovery (PKS 10.2)

**User Story:** As a system administrator, I want BCP/DRP monitoring and management features, so that service continuity is ensured per PKS 10.2 requirements.

#### Acceptance Criteria

1. THE ICTServe_System SHALL display system health dashboard with RTO (Recovery Time Objective) and RPO (Recovery Point Objective) metrics
2. THE ICTServe_System SHALL implement automated backup status monitoring with alerts for backup failures
3. WHEN system availability drops below 99.5%, THE ICTServe_System SHALL trigger BCP notification to designated personnel
4. THE ICTServe_System SHALL provide DRP activation interface for authorized administrators during disaster scenarios
5. THE ICTServe_System SHALL log all BCP/DRP related activities with timestamp and responsible personnel
6. THE ICTServe_System SHALL display last backup timestamp and recovery test results on admin dashboard

### Requirement 31: Third Party Access Management (PKS 11.1)

**User Story:** As a security administrator, I want to manage third-party vendor access with proper controls, so that external access is monitored and compliant with PKS 11.1 requirements.

#### Acceptance Criteria

1. THE ICTServe_System SHALL require NDA (Non-Disclosure Agreement) acknowledgment before granting third-party access
2. THE ICTServe_System SHALL implement time-limited access for third-party users with automatic expiration
3. THE ICTServe_System SHALL log all third-party access activities with enhanced audit trail (company name, purpose, access scope)
4. WHEN third-party contract expires, THE ICTServe_System SHALL automatically revoke access and notify administrators
5. THE ICTServe_System SHALL provide third-party access report showing active vendors, access duration, and activities
6. THE ICTServe_System SHALL enforce principle of least privilege for third-party accounts with role-based access restrictions

### Requirement 32: Security Awareness Training Compliance (PKS 12.1)

**User Story:** As a training administrator, I want to track security awareness training completion, so that all users meet PKS 12.1 training requirements.

#### Acceptance Criteria

1. THE ICTServe_System SHALL display security training completion status on user profile page
2. THE ICTServe_System SHALL send reminder notifications to users with incomplete annual security training (14 days, 7 days, 1 day before deadline)
3. THE ICTServe_System SHALL restrict access to sensitive features for users who have not completed mandatory security training
4. THE ICTServe_System SHALL provide training completion report for administrators showing compliance percentage by department
5. THE ICTServe_System SHALL integrate with MOTAC training system (HRMIS) to sync training completion records
6. THE ICTServe_System SHALL display training certificate download link for completed security awareness courses

### Requirement 33: PSPM Strategic Alignment (Teras Strategik 1-4)

**User Story:** As a MOTAC digital transformation officer, I want the system to align with PSPM 2022-2026 strategic pillars, so that ICTServe contributes to ministry digitalization goals.

#### Acceptance Criteria

1. THE ICTServe_System SHALL implement end-to-end digital services per PSPM Teras 1 (Aplikasi) - no paper-based processes for helpdesk and loan applications
2. THE ICTServe_System SHALL provide data analytics dashboard per PSPM Teras 2 (Data) - service metrics, usage patterns, and KPI tracking
3. THE ICTServe_System SHALL utilize MyGovCloud infrastructure per PSPM Teras 3 (Infrastruktur ICT) - cloud-ready architecture with containerization support
4. THE ICTServe_System SHALL support digital capability building per PSPM Teras 4 (Tadbir Urus & Keupayaan) - user guides, tooltips, and contextual help in Bahasa Melayu
5. THE ICTServe_System SHALL track PSPM KPI metrics (service response time, user satisfaction, digital adoption rate) on admin dashboard
6. THE ICTServe_System SHALL generate PSPM compliance report for quarterly ministry reporting

---

## Implementation Status Summary (v4.0.0)

### PKS Compliance Requirements Status

| Requirement | Status | Notes |
|-------------|--------|-------|
| Req 22: SSO Mandatory (PKS 5.2.1) | 🔴 Not Started | Guest Mode elimination, user_id mandatory FK |
| Req 23: Walk-in/Kiosk Mode with SSO | 🔴 Not Started | Replaces deprecated Guest Mode |
| Req 24: HRMIS Auto-Provisioning | 🔴 Not Started | Replaces manual registration |
| Req 25: DLP Filtering (PKS 9.2.1) | 🔴 Not Started | Cloud AI data protection |
| Req 26: Intranet-Only (PKS 4.2) | 🔴 Not Started | Data sovereignty compliance |
| Req 27: Password Policy (PKS 5.4.3) | 🔴 Not Started | 8 chars, 90-day expiry, 3 attempts |
| Req 28: Dual Audit System | 🟡 Partial | Existing audit, needs 7-year retention |
| Req 29: Incident Response (PKS 10.1) | 🔴 Not Started | CSIRT incident management |
| Req 30: BCP/DRP (PKS 10.2) | 🔴 Not Started | Business continuity monitoring |
| Req 31: Third Party Access (PKS 11.1) | 🔴 Not Started | Vendor access management |
| Req 32: Security Training (PKS 12.1) | 🔴 Not Started | Training compliance tracking |
| Req 33: PSPM Strategic Alignment | 🔴 Not Started | Teras Strategik 1-4 alignment |

### Completed Requirements (v3.6.0-r6)

| Requirement | Status | Notes |
|-------------|--------|-------|
| Req 1: BM Exclusive Interface | ✅ Complete | Language switcher removed, BM-only enforced |
| Req 2: Theme Switcher | ✅ Complete | Light/dark mode with localStorage persistence |
| Req 4: MyDS Design System | ✅ Complete | Full token mapping implemented |
| Req 5: Unified Component Library | ✅ Complete | All component categories created |
| Req 6: Livewire 3.7/Volt 1.10 | ✅ Complete | OptimizedLivewireComponent trait implemented |
| Req 7: WCAG 2.2 AA | ✅ Complete | 100% Lighthouse accessibility score |
| Req 8: Filament Admin Panel | ✅ Complete | Four-role RBAC implemented |
| Req 9: Authenticated Portal | ✅ Complete | Dashboard, history, profile, approvals |
| Req 10: Real-Time Features | ✅ Complete | Laravel Reverb WebSocket integration |
| Req 11: Cross-Module Integration | ✅ Complete | Auto-maintenance tickets, unified search |
| Req 12: Export/Reporting | ✅ Complete | CSV, Excel, PDF with scheduling |
| Req 13: Performance | ✅ Complete | Core Web Vitals targets met |
| Req 14: Security/Audit | ✅ Complete | PDPA compliance, 7-year retention |
| Req 15: Mobile Optimization | ✅ Complete | Responsive design, touch targets |
| Req 16: Landing Page | ✅ Complete | Hybrid color scheme, MOTAC branding |
| Req 17: FAQ Bot | ✅ Complete | Touch targets fixed (44×44px) |
| Req 18: Service Modal | 🔴 Needs Update | Must update for SSO-only access |
| Req 19: Status Check Page | 🟡 Partial | Missing translation keys identified |
| Req 20: Guest Helpdesk Form | 🔴 Needs Update | Must convert to SSO-authenticated form |
| Req 21: Translation Keys | 🟡 Partial | 6 keys missing in status.php |

### Pending Items

1. **PKS Compliance Migration (Req 22-28)**: Implement SSO mandatory architecture
2. **Service Modal Update (Req 18)**: Remove guest option, SSO-only access
3. **Guest Form Conversion (Req 20)**: Convert to Walk-in/Kiosk Mode with SSO
4. **Translation Keys (Req 21)**: Add missing keys to `lang/ms/status.php`
5. **Figma MCP Integration (Req 3)**: Optional design-to-code workflow setup

---

**Document Version**: 4.0.0  
**Last Updated**: 24 December 2025  
**Author**: BPM MOTAC Development Team  
**Status**: Active - PKS Compliance Migration Phase  
**Compliance**: D00-D18 v4.0, KRISA D01-D10, D17 v4.0, MyDS Design System v2025.2, WCAG 2.2 AA, PKS 5.2.1, 9.2.1, 4.2, 5.4.3
