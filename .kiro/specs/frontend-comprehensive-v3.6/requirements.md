# ICTServe Frontend Comprehensive v3.6.0 - Requirements Document

**Sistem ICTServe**  
**Versi:** 3.6.0 (SemVer)  
**Tarikh Kemaskini:** 11 Disember 2025  
**Status:** Aktif  
**Klasifikasi:** Terhad - Dalaman BPM MOTAC  
**Standard Rujukan:** ISO/IEC/IEEE 15288, ISO/IEC/IEEE 12207, ISO 9241-210/110/11, WCAG 2.2 AA, MyGOV Digital Service Standards v2.1.0, MyDS Design System v2025.2

---

## Document Information

| Attribute | Value |
|-----------|-------|
| **Version** | 3.6.0 |
| **Last Updated** | 11 December 2025 |
| **Status** | Active - Consolidated from filament-admin-access and staff-dashboard-profile specs |
| **Classification** | Restricted - Internal BPM MOTAC |
| **Compliance** | ISO/IEC/IEEE 15288, 12207, ISO 9241-210/110/11, WCAG 2.2 AA, MyGOV Digital Standards v2.1.0 |
| **Language** | Bahasa Melayu (primary), English (technical) |

> **Notis Penggunaan Dalaman**: Ini adalah untuk kegunaan warga kerja MOTAC sahaja dan tidak dibuka kepada orang awam (internal use only).

---

## Related Document References (D00-D17)

- **[D00_SYSTEM_OVERVIEW.md]** - System Overview (v3.6.0) - True Hybrid Architecture
- **[D03_SOFTWARE_REQUIREMENTS_SPECIFICATION.md]** - Software Requirements Specification
- **[D04_SOFTWARE_DESIGN_DOCUMENT.md]** - Software Design Document (v3.6.0)
- **[D09_DATABASE_DOCUMENTATION.md]** - Database Documentation (Dual Audit System)
- **[D11_TECHNICAL_DESIGN_DOCUMENTATION.md]** - Technical Design Documentation
- **[D12_UI_UX_DESIGN_GUIDE.md]** - UI/UX Design Guide (v3.6.0) - Design principles, layouts, components
- **[D13_UI_UX_FRONTEND_FRAMEWORK.md]** - Frontend Framework (v3.6.0) - Technical implementation
- **[D14_UI_UX_STYLE_GUIDE.md]** - Style Guide (v3.6.0) - Visual standards, MyDS alignment
- **[D15_LANGUAGE_MS_EN.md]** - Language Guide (Bahasa Melayu exclusive, v3.6.0)
- **[D16_BROADCASTING_SETUP.md]** - Laravel Reverb WebSocket Configuration
- **[D17_QUEUE_MANAGEMENT_HORIZON.md]** - Queue Management for Notifications

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
- **Laravel_12**: Laravel 12.40.1 framework with PHP 8.2.12, modern syntax, and enhanced performance (ref: D00 §4.1)
- **Livewire_3**: Livewire 3.7.0 server-driven UI framework with reactive components (ref: D12 §2, D13 §2.1)
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
- **Laravel_Reverb**: Laravel Reverb 1.6.2 WebSocket server for real-time features (ref: D00 §4.1, D16)
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
