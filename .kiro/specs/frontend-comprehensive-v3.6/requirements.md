# ICTServe Frontend Comprehensive v3.6.1 - Requirements Document

**Sistem ICTServe**  
**Versi:** 3.6.1 (SemVer)  
**Tarikh Kemaskini:** 17 Disember 2025  
**Status:** Aktif  
**Klasifikasi:** Terhad - Dalaman BPM MOTAC  
**Standard Rujukan:** ISO/IEC/IEEE 15288, ISO/IEC/IEEE 12207, ISO 9241-210/110/11, WCAG 2.2 AA, MyGOV Digital Service Standards v2.1.0, MyDS Design System v2025.2

---

## Document Information

| Attribute | Value |
|-----------|-------|
| **Version** | 3.6.1-r1 |
| **Last Updated** | 17 December 2025 |
| **Status** | Active - Updated with D18 Cloud Hybrid AI Architecture and technology stack v3.6.1 |
| **Classification** | Restricted - Internal BPM MOTAC |
| **Compliance** | ISO/IEC/IEEE 15288, 12207, ISO 9241-210/110/11, WCAG 2.2 AA, MyGOV Digital Standards v2.1.0 |
| **Language** | Bahasa Melayu (primary), English (technical) |
| **Source Documents** | D00-D18 v3.6.1, FRONTEND-DEVELOPMENT-v3-6-0.md, FRONTPAGE_DESIGN_ANALYSIS_v3.6.0.md |
| **ISO Document Reference** | PK.(S).MOTAC.07.(L1) - ICTServe Portal |

> **Notis Penggunaan Dalaman**: Ini adalah untuk kegunaan warga kerja MOTAC sahaja dan tidak dibuka kepada orang awam (internal use only).

---

## Changelog

| Version | Date | Changes | Author |
|---------|------|---------|--------|
| 3.6.1-r1 | 17 December 2025 | **D18 AI Chatbot Integration**: Added Requirement 16 (Cloud Hybrid AI Chat Interface), Requirement 17 (FAQ Bot Widget), Requirement 18 (AI Admin Management Interface). Updated technology stack to v3.6.1 (Laravel 12.42.0, Livewire 3.7.1, Laravel Reverb 1.6.3, etc.). Added AI-related glossary terms. Cross-reference D18 v1.0.1. | BPM Development Team |
| 3.6.0-r6 | 14 December 2025 | Updated with FRONTPAGE_DESIGN_ANALYSIS_v3.6.0 and FRONTEND-DEVELOPMENT-v3-6-0 findings | BPM Development Team |

---

## Related Document References (D00-D18)

- **[D00_SYSTEM_OVERVIEW.md]** - System Overview (v3.6.1) - True Hybrid Architecture + Cloud Hybrid AI
- **[D03_SOFTWARE_REQUIREMENTS_SPECIFICATION.md]** - Software Requirements Specification
- **[D04_SOFTWARE_DESIGN_DOCUMENT.md]** - Software Design Document (v3.6.1)
- **[D09_DATABASE_DOCUMENTATION.md]** - Database Documentation (Dual Audit System)
- **[D11_TECHNICAL_DESIGN_DOCUMENTATION.md]** - Technical Design Documentation
- **[D12_UI_UX_DESIGN_GUIDE.md]** - UI/UX Design Guide (v3.6.1) - Design principles, layouts, components
- **[D13_UI_UX_FRONTEND_FRAMEWORK.md]** - Frontend Framework (v3.6.1) - Technical implementation
- **[D14_UI_UX_STYLE_GUIDE.md]** - Style Guide (v3.6.1) - Visual standards, MyDS alignment
- **[D15_LANGUAGE_MS_EN.md]** - Language Guide (Bahasa Melayu exclusive, v3.6.0+)
- **[D16_BROADCASTING_SETUP.md]** - Laravel Reverb WebSocket Configuration (v3.6.1)
- **[D17_QUEUE_MANAGEMENT_HORIZON.md]** - Queue Management for Notifications (Laravel Queue + Redis)
- **[D18_AI_CHATBOT_OLLAMA_BEDROCK.md]** - Cloud Hybrid AI Architecture (v1.0.1) - Ollama + AWS Bedrock Integration

---

## Introduction

This specification defines the comprehensive requirements for the complete frontend system of ICTServe v3.6.0, consolidating all user interface components across the **True Hybrid Architecture**. This includes guest forms, authenticated staff portal, and Filament admin panel with unified design system, accessibility compliance, and performance optimization.

**Consolidated Scope:**

- **Guest Forms**: Public-facing helpdesk and asset loan submission forms
- **Authenticated Portal**: Staff dashboard, submission history, profile management, and approval interface
- **Filament Admin Panel**: Administrative interface with RBAC, reporting, and system management
- **Unified Component Library**: Standardized components across all three layers
- **Cross-Module Integration**: Deep integration between helpdesk and asset loan modules

**Key v3.6.0 Features:**

- **Bahasa Melayu Exclusive UI**: Complete removal of language switcher, BM-only interface per government directive
- **Theme Switcher**: Light/dark mode toggle with light as immutable default, localStorage persistence, FOUT prevention
- **MyDS Design System Compliance**: Full alignment with Malaysia Government Design System v2025.2
- **Figma MCP Integration**: Design-to-code workflow using Figma Model Context Protocol
- **Enhanced Performance**: Core Web Vitals optimization, Livewire 3.7 patterns, Volt 1.10 components
- **Four-Role RBAC**: Staff, Approver (Grade 41+), Admin, Superuser with comprehensive authorization
- **Real-Time Features**: Laravel Reverb WebSocket integration for live updates and notifications

## Glossary

- **ICTServe**: ICT Service Management System for MOTAC (Ministry of Tourism, Arts and Culture Malaysia)
- **True_Hybrid_Architecture**: Three-tier system supporting guest forms, authenticated staff portal, and Filament admin (ref: D00 §1, D12 §5.1)
- **Laravel_12**: Laravel 12.42.0 framework with PHP 8.2.12, modern syntax, and enhanced performance (ref: D00 §4.1 v3.6.1)
- **Livewire_3**: Livewire 3.7.1 server-driven UI framework with reactive components (ref: D12 §2, D13 §2.1)
- **Volt_1**: Livewire Volt 1.10.1 single-file component API for simplified development (ref: D13 §2.1)
- **Tailwind_CSS_4**: Tailwind CSS 4.1.17 utility-first CSS framework with @theme configuration (ref: D13 §2.2, D14 §2)
- **Alpine_js_3**: Alpine.js 3.x lightweight JavaScript framework included with Livewire (ref: D12 §2)
- **Filament_4**: Filament 4.1.10 Server-Driven UI (SDUI) admin panel framework (ref: D00 §4.1)
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
- **Cloud_Hybrid_AI**: True Hybrid AI Architecture combining Ollama (local LLM) with AWS Bedrock (cloud AI) for intelligent query routing (ref: D18 v1.0.1)
- **FAQ_Bot**: AI-powered Q&A system accessible via guest forms and authenticated portal with model routing (ref: D18 §2.3)
- **Multi_Model_Intelligence**: Claude Opus 4.5, Sonnet 4.5, Haiku 4.5, Nova Pro/Lite/Micro for task-specific AI routing (ref: D18 §4.4)
- **Model_Routing**: Smart query classification (FAQ → Ollama, Complex → Bedrock, Hybrid → Both) for cost optimization (ref: D18 §5)
- **Streaming_Responses**: Server-Sent Events (SSE) for responsive AI chat experience (ref: D18 §2.3 - future)
- **Web_Augmented_Responses**: DuckDuckGo integration for current context in AI responses (ref: D18 §3.2.2)
- **Conversation_Management**: Enhanced BedrockConversation model with save/load/delete and long-term memory (ref: D18 §6)
- **Auto_Reply_Generation**: AI-generated response drafts for tickets with admin approval workflow (ref: D18 §3.2.2)
- **Document_Analysis**: AI-powered PDF/DOCX parsing with semantic search and PII detection (ref: D18 §3.2.2)
- **Theme_Switcher**: Light/dark mode toggle with light as immutable default, localStorage persistence, FOUT prevention (ref: D12 §6.10, D14 §6.1.2)
- **BM_Exclusive_UI**: Bahasa Melayu exclusive interface per v3.6.0 government directive (ref: D15 v3.6.0)
- **Filament_Panel**: Admin interface at `/admin` with four-role RBAC and comprehensive resource management
- **Authenticated_Portal**: Internal staff portal requiring login for enhanced features beyond guest forms
- **Staff_Dashboard**: Personalized dashboard displaying user-specific statistics, recent activity, and quick actions
- **Approval_Interface**: Dedicated interface for Grade 41+ approvers to review and process loan applications
- **Internal_Comments**: Staff-only comments on submissions visible only to authenticated users and admins
- **Real_Time_Tracking**: Live status updates for submissions using Livewire polling and Laravel Echo broadcasting
- **Notification_Center**: Centralized interface for viewing and managing system notifications
- **Export_Functionality**: User ability to export submission history and reports in CSV, PDF formats
- **Activity_Timeline**: Chronological view of all user actions and submission status changes
- **Guest_Submission_Claiming**: Process for authenticated users to claim their previous guest submissions via email matching

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

5. THE ICTServe_System SHALL implement visual regression testing with Playwright screenshots for all critical pages across light/dark themes and responsive breakpoints

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

**Document Version**: 3.6.0-r6  
**Last Updated**: 14 December 2025  
**Author**: BPM MOTAC Development Team  
**Status**: Active - Ready for Implementation Phase  
**Compliance**: D00-D17 v3.6.0, MyDS Design System v2025.2, WCAG 2.2 AA
