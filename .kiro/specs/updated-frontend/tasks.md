# Updated Frontend - Implementation Tasks

## Introduction

This document provides a comprehensive, actionable task breakdown for implementing the ICTServe frontend upgrade. Tasks are organized into 6 phases with clear dependencies, effort estimates, and traceability to requirements and design specifications.

**Key Conventions**:

- Tasks marked with `*` are optional (testing, documentation) for faster MVP delivery
- All core implementation tasks are required
- Each task references specific requirements from requirements.md

## Task Organization

### Phase Structure

- **Phase 1**: Foundation (Weeks 1-3) - Laravel 12.x, Livewire 3.x, Volt 1, Tailwind 4.1, Alpine.js 3.x
- **Phase 2**: Components (Weeks 4-6) - Unified component library with WCAG 2.2 AA compliance
- **Phase 3**: Guest Interface (Weeks 7-9) - Public forms without authentication
- **Phase 4**: Portal (Weeks 10-12) - Authenticated staff dashboard and management
- **Phase 5**: Integration (Weeks 13-15) - Cross-module functionality
- **Phase 6**: Deployment (Weeks 16-18) - Testing, optimization, production launch

### Priority Levels

- **P0**: Critical path, blocks other tasks
- **P1**: High priority, core functionality
- **P2**: Medium priority, important features

### Effort Estimates

- **S**: 1-2 days | **M**: 3-5 days | **L**: 1-2 weeks | **XL**: 2-3 weeks

---

## Phase 1: Foundation and Core Infrastructure

### 1.1 Laravel 12.x Foundation Setup

**Priority**: P0 | **Effort**: L

Upgrade to Laravel 12.x with PHP 8.3+ and establish hybrid architecture.

- [x] 1.1.1 Upgrade Laravel to 12.x with PHP 8.3+ compatibility
- [x] 1.1.2 Configure hybrid architecture (guest/authenticated/admin layers)
- [x] 1.1.3 Implement service providers and dependency injection
- [x] 1.1.4 Configure middleware stack (locale, auth, security headers)
- [x] 1.1.5 Setup Redis queue system for background processing
- [x] 1.1.6 **Update Database Schema**: Add `responsible_officer_details` (JSON) and `is_delegate` (boolean) columns to `loan_applications` table
- [x] 1.1.7 **Create WorkingDayCalculator Service**: Implement 3-day minimum lead time calculation excluding weekends and Malaysian public holidays
- [ ]\* 1.1.8 Write unit tests for service providers and middleware

**Requirements**: R01, R09 | **Design**: Architecture Overview

**Compliance Note**: Tasks 1.1.6-1.1.7 implement legacy business logic requirements for asset loan module.

---

### 1.2 Livewire 3.x Integration

**Priority**: P0 | **Effort**: M

Integrate Livewire 3.x with OptimizedLivewireComponent trait.

- [x] 1.2.1 Install and configure Livewire 3.x
- [x] 1.2.2 Create OptimizedLivewireComponent trait (caching, lazy loading, query optimization)
- [x] 1.2.3 Configure PHP 8 attributes (#[Reactive], #[Computed], #[Lazy])
- [x] 1.2.4 Implement event handling with $this->dispatch()
- [x] 1.2.5 Document wire:loading and wire:key patterns
- [ ]\* 1.2.6 Write unit tests for OptimizedLivewireComponent trait

**Requirements**: R02 | **Design**: Livewire Architecture

---

### 1.3 Volt 1 Setup

**Priority**: P1 | **Effort**: S

Configure Volt 1 for single-file components.

- [x] 1.3.1 Install and configure Volt 1
- [x] 1.3.2 Create directory structure in resources/views/livewire/
- [x] 1.3.3 Document functional API (state(), computed(), on())
- [x] 1.3.4 Establish naming conventions (kebab-case)
- [ ]\* 1.3.5 Create conversion guidelines from traditional Livewire
- [ ]\* 1.3.6 Build example Volt components

**Requirements**: R03 | **Design**: Volt Component Design

---

### 1.4 Tailwind CSS 4.1 Configuration

**Priority**: P0 | **Effort**: M

Configure Tailwind with WCAG-compliant colors and MOTAC branding.

- [x] 1.4.1 Install Tailwind CSS 4.1 with Lightning CSS engine
- [x] 1.4.2 Implement @theme CSS variables for MOTAC branding (CSS-first configuration)
- [x] 1.4.3 **Benchmark HMR and build times** with Vite integration (de-risk Tailwind 4.0)
- [x] 1.4.4 Implement WCAG-compliant color palette (Primary #0056b3, Success #198754, Warning #ff8c00, Danger #b50c0c)
- [x] 1.4.5 Configure content scanning (resources/views/**/\*.blade.php, app/Livewire/**/\*.php)
- [x] 1.4.6 Configure production optimization (<50KB gzipped)
- [ ]\* 1.4.7 Document design tokens and usage guidelines

**Requirements**: R04 | **Design**: Tailwind Design System

**Risk Mitigation**: Task 1.4.3 validates Tailwind 4.0 + Vite integration before full component development.

---

### 1.5 Alpine.js 3.x Integration

**Priority**: P1 | **Effort**: S

Configure Alpine.js patterns for client-side interactivity.

- [x] 1.5.1 Document Alpine.js patterns (x-data, x-show, x-transition, x-trap)
- [x] 1.5.2 Create reusable Alpine components in resources/views/components/alpine/
- [x] 1.5.3 Implement focus management and keyboard navigation patterns
- [x] 1.5.4 Document ARIA attribute toggling with Alpine
- [ ]\* 1.5.5 Create integration examples with Livewire

**Requirements**: R05 | **Design**: Alpine.js Patterns

---

## Phase 2: Component Library and Design System

### 2.1 Component Library Structure

**Priority**: P0 | **Effort**: M

Create unified component library with proper organization.

- [x] 2.1.1 Create component categories (accessibility/, data/, form/, layout/, navigation/, responsive/, ui/, alpine/)
- [x] 2.1.2 Implement component metadata headers (name, WCAG level, version, traceability)
- [x] 2.1.3 Establish versioning system for components
- [x] 2.1.4 **Setup automated Accessibility Linting** (axe-core) in CI pipeline
- [x] 2.1.5 **Configure Visual Regression Testing** (Spatie laravel-snapshot-testing or Pest)
- [x] 2.1.6 **Create Component Playground page** (internal route /dev/components for visual testing)
- [ ]\* 2.1.7 Create documentation template
- [ ]\* 2.1.8 Implement D00-D15 traceability system

**Requirements**: R06 | **Design**: Component Organization

**Shift-Left Strategy**: Tasks 2.1.4-2.1.6 enforce quality gates at component level, preventing Phase 6 refactoring.

---

### 2.2 UI Components Development

**Priority**: P1 | **Effort**: L

Develop core UI components with WCAG 2.2 AA compliance.

- [x] 2.2.1 Create x-ui.button with variants (default, primary, secondary, success, warning, danger)
- [x] 2.2.2 **Run axe-core accessibility check** on x-ui.button (blocking quality gate)
- [x] 2.2.3 **Create visual regression snapshot** for x-ui.button
- [x] 2.2.4 Create x-ui.card with header, body, footer sections
- [x] 2.2.5 **Run axe-core accessibility check** on x-ui.card (blocking quality gate)
- [x] 2.2.6 **Create visual regression snapshot** for x-ui.card
- [x] 2.2.7 Create x-ui.modal with focus trap and keyboard navigation
- [x] 2.2.8 **Run axe-core accessibility check** on x-ui.modal (blocking quality gate)
- [x] 2.2.9 **Create visual regression snapshot** for x-ui.modal
- [x] 2.2.10 Create x-ui.alert with dismissible functionality
- [x] 2.2.11 Create x-ui.badge with status variants
- [x] 2.2.12 Create x-ui.dropdown with keyboard navigation
- [x] 2.2.13 **Verify 4.5:1 text contrast and 44×44px touch targets** (automated in axe-core)
- [x] 2.2.14 **Create x-ui.user-info-card**: Reusable component for displaying read-only profile data (Name, Grade, Department) with green/teal card styling
- [x] 2.2.15 **Create x-ui.stats-card with dynamic styling**: Dashboard statistics card with conditional icon colors (green/neutral for 0, red for >0)
- [ ]\* 2.2.16 Add components to Playground page (/dev/components)

**Requirements**: R06, R07 | **Design**: UI Components, Portal Interface

**Quality Gate**: Each component MUST pass axe-core checks before proceeding to next component.

**Portal Enhancement**: Tasks 2.2.14-2.2.15 standardize authenticated portal components based on visual audit findings.

---

### 2.3 Form Components Development

**Priority**: P1 | **Effort**: L

Develop form components with validation and ARIA support.

- [x] 2.3.1 Create x-form.input with validation states and ARIA attributes
- [x] 2.3.2 Create x-form.select with search and multi-select options
- [x] 2.3.3 Create x-form.textarea with character counting
- [x] 2.3.4 Create x-form.checkbox and x-form.radio with proper labeling
- [x] 2.3.5 Create x-form.file-upload with drag-and-drop
- [x] 2.3.6 Integrate with Livewire validation and wire:model patterns
- [x] 2.3.7 Implement real-time validation with debouncing (300ms)
- [ ]\* 2.3.8 Write integration tests for form validation

**Requirements**: R06, R09 | **Design**: Form Components

---

### 2.4 Accessibility Components

**Priority**: P0 | **Effort**: M

Develop specialized accessibility components.

- [x] 2.4.1 Create x-accessibility.skip-links with proper navigation
- [x] 2.4.2 Create x-accessibility.language-switcher with 44×44px touch targets
- [x] 2.4.3 Create x-accessibility.aria-live-region for dynamic updates
- [x] 2.4.4 Create x-accessibility.focus-trap for modals
- [x] 2.4.5 Implement keyboard navigation patterns (Tab, Escape, Enter)
- [x] 2.4.6 Test with screen readers (NVDA, JAWS)
- [ ]\* 2.4.7 Document accessibility testing procedures

**Requirements**: R07 | **Design**: Accessibility Features

---

### 2.5 Layout and Navigation Components

**Priority**: P1 | **Effort**: M

Develop layout and navigation components for responsive design.

- [x] 2.5.1 Create x-layout.guest with header, main, footer
- [x] 2.5.2 Create x-layout.portal with sidebar, header, main
- [x] 2.5.3 Create x-navigation.main-menu with responsive hamburger pattern
- [x] 2.5.4 Create x-navigation.breadcrumb with structured data
- [x] 2.5.5 Create x-navigation.pagination with accessibility features
- [x] 2.5.6 Implement responsive breakpoint handling (320px-1920px)
- [x] 2.5.7 **Implement Keyboard Shortcuts Manager**: Global hotkey listener (Alpine.js @keydown.window) for Alt+N (New Ticket), Alt+D (Dashboard), Alt+H (Help), etc.
- [x] 2.5.8 **Create Keyboard Shortcuts Help Modal**: Triggered by ? key, displays all available shortcuts with descriptions (bilingual)
- [ ]\* 2.5.9 Create mobile-first usage examples

**Requirements**: R15 | **Design**: Layout System, Keyboard Navigation

**Power User Feature**: Tasks 2.5.7-2.5.8 implement sophisticated keyboard shortcuts for authenticated portal (visual audit finding from Screenshot 4).

---

## Phase 3: Guest Forms and Public Interface

### 3.1 Guest Helpdesk Form

**Priority**: P1 | **Effort**: L

Implement guest helpdesk ticket submission with multi-step wizard.

- [x] 3.1.1 Create multi-step wizard with progress indicators
- [x] 3.1.2 Implement real-time validation with wire:model.live.debounce.300ms
- [x] 3.1.3 Add file upload with drag-and-drop (max 5 files, WebP optimization)
- [x] 3.1.4 **Implement Optimistic UI** for form submission (immediate feedback, rollback on error)
- [x] 3.1.5 Implement email confirmation within 60 seconds
- [x] 3.1.6 Add rate limiting (60 req/min) and CSRF protection
- [x] 3.1.7 **Implement URL-based locale** (/ms/ticket/... or /en/ticket/...)
- [x] 3.1.8 Implement bilingual support with language switcher
- [x] 3.1.9 **Implement ISO Compliance Header**: Display document ID `PK.(S).MOTAC.07.(L1)` in top-right corner of form (match Asset Loan form header style)
- [x] 3.1.10 **Implement Searchable Division Select**: Create virtual scrolled searchable combobox for "Bahagian" field (large list optimization, NOT native HTML select)
- [x] 3.1.11 **Implement "Perakuan" Gate**: Add mandatory checkbox with **exact legacy legal text**: "Saya memperakui dan mengesahkan bahawa semua maklumat yang diberikan di dalam eBorang Laporan Kerosakan ini adalah benar..."
- [x] 3.1.12 **FIX: Verify ISO Header Display**: Ensure PK.(S).MOTAC.07.(L1) is visible in top-right corner on BOTH guest and authenticated versions (currently missing)
- [x] 3.1.13 **FIX: Update Declaration Text**: Replace generic declaration with exact legacy legal text (currently using generic text)
- [x] 3.1.14 **Standardize User Info Display**: Apply x-ui.user-info-card component (green/teal card style) to authenticated Helpdesk form
- [ ]\* 3.1.15 Write feature tests for ticket submission workflow (including optimistic UI rollback and compliance gates)

**Requirements**: R09, R12, R13, R18 | **Design**: Guest Interface, Optimistic UI Pattern, ISO Compliance

**Compliance Note**: Tasks 3.1.9-3.1.11 implement legacy ISO compliance requirements (PK.(S).MOTAC.07.(L1)).

**CRITICAL FIX**: Tasks 3.1.12-3.1.14 address visual audit findings - ISO header missing on both guest AND authenticated forms, user info display inconsistent.

**UX Enhancement**: Task 3.1.4 provides immediate feedback while server processes 60-second email workflow.

---

### 3.2 Guest Asset Loan Form

**Priority**: P1 | **Effort**: L

Implement guest asset loan application with availability checking.

- [x] 3.2.1 Create multi-step application wizard
- [x] 3.2.2 Implement asset availability checking with real-time updates
- [x] 3.2.3 Integrate approval workflow (Grade 41+ email approvals)
- [x] 3.2.4 Add email notifications for status changes
- [x] 3.2.5 Implement asset calendar for booking dates with WorkingDayCalculator (3-day minimum lead time)
- [x] 3.2.6 **Implement ISO Compliance Header**: Display document ID `PK.(S).MOTAC.07.(L3)` in top-right corner of form
- [x] 3.2.7 **Implement "On Behalf" Toggle**: Show/hide Responsible Officer fields based on user selection (is_delegate checkbox)
- [x] 3.2.8 **Implement T&C Accordion**: Display 11 specific terms and conditions from PK.(S).MOTAC.07.(L3) in expandable accordion before declaration
- [x] 3.2.9 **Implement WorkingDayCalculator Validation**: Enforce 3-day minimum lead time excluding weekends and Malaysian public holidays
- [x] 3.2.10 Add terms and conditions acceptance with mandatory checkbox
- [ ]\* 3.2.11 Write feature tests for loan application workflow (including working day validation and on-behalf logic)

**Requirements**: R09, R11 | **Design**: Guest Interface

**Compliance Note**: Tasks 3.2.6-3.2.9 implement legacy ISO compliance requirements (PK.(S).MOTAC.07.(L3)) and business logic.

---

### 3.3 Public Landing Pages

**Priority**: P2 | **Effort**: M

Create public landing pages with service information.

- [x] 3.3.1 Design and implement homepage with service overview
- [x] 3.3.2 Create service information pages (helpdesk, asset loan)
- [x] 3.3.3 Implement FAQ section with search functionality
- [x] 3.3.4 Add contact information and support hours
- [x] 3.3.5 Ensure responsive design across all devices
- [x] 3.3.6 **Implement Contact Form Integration**: Route "Hantar Mesej Kepada Kami" submissions to Helpdesk module as "General Enquiry" category tickets
- [x] 3.3.7 **Return Ticket ID on Contact Submission**: Display generated Ticket ID to user after Contact form submission for tracking
- [x] 3.3.8 **Implement Service Request Routing**: Define "Permintaan Perkhidmatan" card logic (route to Helpdesk form with pre-filled "Service Request" category)
- [ ]\* 3.3.9 Implement SEO optimization and meta tags

**Requirements**: R15 | **Design**: Guest Interface

**Integration Note**: Tasks 3.3.6-3.3.8 prevent dead-end forms by routing Contact and Service Request to tracked Helpdesk tickets.

---

### 3.4 Guest Form Security

**Priority**: P0 | **Effort**: S

Implement security measures for guest forms.

- [x] 3.4.1 Configure rate limiting middleware (60 requests per minute)
- [x] 3.4.2 Enhance CSRF protection for AJAX requests
- [x] 3.4.3 Implement input validation and sanitization
- [x] 3.4.4 Add honeypot fields for bot detection
- [x] 3.4.5 Configure IP-based blocking for abuse prevention
- [ ]\* 3.4.6 Document security monitoring procedures

**Requirements**: R14 | **Design**: Security Measures

---

## Phase 4: Authenticated Portal and Dashboard

### 4.0 Unified Authentication (NEW)

**Priority**: P0 | **Effort**: S

Consolidate login interfaces and ensure bilingual support.

- [x] 4.0.1 **Merge Admin and Staff Login Views**: Create single unified login layout with role detection after authentication
- [x] 4.0.2 **Add Language Switcher to Login**: Ensure Bahasa Melayu/English toggle is visible on login screen
- [x] 4.0.3 **Standardize Login Styling**: Ensure consistent field spacing, button styling, and responsive behavior
- [x] 4.0.4 **Implement Role-Based Redirect**: Detect user role (Admin vs Staff) after login and redirect to appropriate dashboard

**Requirements**: R10, R13 | **Design**: Authentication System

**UX Fix**: Tasks 4.0.1-4.0.4 address visual audit findings - fragmented login screens with inconsistent styling and missing language toggle.

---

### 4.1 User Authentication and Profile

**Priority**: P1 | **Effort**: M

Implement authentication system with profile management.

- [x] 4.1.1 Configure Laravel authentication with email verification
- [x] 4.1.2 Create profile management interface with editable and read-only fields
- [x] 4.1.3 **Implement Profile Data Sync Logic**: Populate read-only fields (Email, Staff ID, Grade, Department) from User seeder/Admin input
- [x] 4.1.4 **Add "Request Data Correction" Action**: Link next to read-only fields that opens Helpdesk ticket with "Profile Data Correction" category
- [x] 4.1.5 Implement notification preferences configuration
- [x] 4.1.6 Add language preference persistence (session/cookie, 1-year expiration)
- [x] 4.1.7 Implement account linking for claiming guest submissions
- [x] 4.1.8 Add password reset functionality
- [ ]\* 4.1.9 Write feature tests for authentication flows

**Requirements**: R10, R13 | **Design**: Authentication System, Profile Management

**Data Integrity**: Tasks 4.1.3-4.1.4 address visual audit finding (Screenshot 9) - read-only profile fields need sync source and correction workflow.

---

### 4.2 Personalized Dashboard

**Priority**: P1 | **Effort**: L

Create personalized dashboard with statistics and quick actions.

- [x] 4.2.1 Implement dashboard with key statistics (Open Tickets, Pending Loans, Approvals, Overdue Items, Claimable Submissions)
- [x] 4.2.2 **Implement Dynamic Stats Card Styling**: Use x-ui.stats-card with conditional colors (green/neutral for count=0, red for count>0)
- [x] 4.2.3 Create recent activity feed with filtering options
- [x] 4.2.4 Add quick action buttons for common tasks
- [x] 4.2.5 Ensure responsive design for mobile and desktop
- [x] 4.2.6 Implement real-time updates using Livewire
- [x] 4.2.7 Add performance optimization with Redis caching (5-minute cache)
- [ ]\* 4.2.8 Write feature tests for dashboard functionality
- [x] 4.2.9 **Dynamic Dashboard State Consistency**: Ensure Frontend Portal Dashboard "Overdue" card logic matches Filament admin panel logic (Green for 0, Red for >0) to maintain consistent mental model between Admin and User views
  - Apply same conditional styling logic as Filament widgets
  - Verify color coding consistency across both interfaces
  - _Requirements: UX Consistency, Mental Model Alignment_

**Requirements**: R10, R08 | **Design**: Dashboard Interface

**UX Enhancement**: Task 4.2.2 addresses visual audit finding (Screenshot 3) - "Overdue" card shows red icon even when count is 0 (should be green/neutral). Task 4.2.9 ensures consistency between Admin and Portal views.

---

### 4.3 Submission History

**Priority**: P1 | **Effort**: M

Implement submission history with filtering and search.

- [ ] 4.3.1 Create unified submission history (tickets and loan applications)
- [ ] 4.3.2 Implement advanced filtering (status, date, category, type)
- [ ] 4.3.3 Add search functionality across all submission data
- [ ] 4.3.4 **Implement "Tuntut Penyerahan" (Claim Submission) Workflow**: Allow staff to claim guest tickets submitted with their email
- [ ] 4.3.5 **Add Email Verification for Claiming**: Send OTP to email before linking guest ticket to staff account (security measure)
- [ ] 4.3.6 **Display "Boleh Dituntut" (Claimable) Count**: Show number of guest tickets matching staff email on dashboard
- [ ] 4.3.7 Implement bulk operations for multiple submissions
- [ ] 4.3.8 Add export functionality (CSV, PDF)
- [ ] 4.3.9 Optimize pagination with performance caching
- [ ]\* 4.3.10 Write feature tests for search, filtering, and claiming workflow

**Requirements**: R10, R11 | **Design**: Dashboard Interface, Ticket Claiming

**Security Feature**: Tasks 4.3.4-4.3.6 implement ticket claiming workflow (Screenshot 2) with OTP verification to prevent unauthorized account linking.

---

### 4.4 Approval Interface (Grade 41+)

**Priority**: P1 | **Effort**: L

Create approval interface for Grade 41+ users.

- [ ] 4.4.1 Implement approval queue with pending items
- [ ] 4.4.2 Add bulk approval/rejection functionality
- [ ] 4.4.3 **Implement Optimistic UI** for approval actions (immediate state update, rollback on failure)
- [ ] 4.4.4 Create approval history with audit trail
- [ ] 4.4.5 Implement email-based approval with secure tokens (7-day expiration)
- [ ] 4.4.6 Add delegation functionality for temporary approvers
- [ ] 4.4.7 Implement SLA monitoring and alerts
- [ ] 4.4.8 **Implement Admin Impersonation** feature (view dashboard as specific Grade 41+ approver)
- [ ]\* 4.4.9 Write feature tests for approval workflows (including optimistic UI rollback)

**Requirements**: R10, R12 | **Design**: Approval System, Optimistic UI Pattern

**Debug Enhancement**: Task 4.4.8 enables admins to debug access issues by viewing exact approver perspective.

---

## Phase 5: Cross-Module Integration

### 5.0 Admin Panel Architecture Refinement (NEW)

**Priority**: P0 | **Effort**: M

Implement strategic architectural improvements for Filament admin panel.

- [x] 5.0.1 **Redirect Filament Widgets to Portal**: Ensure clicking approval items in Filament widgets (Loan Approval Queue, Ticket Queue) opens Frontend Portal approval page instead of Filament Edit resource

  - Modify widget table actions to use `url()` with `route('portal.loans.approve', $record)`
  - Set `canEdit()` to return false for approval-related resources
  - Add "Review in Portal" action with external link icon
  - _Requirements: Admin Panel Architecture, Approver Separation_

- [x] 5.0.2 **Enrich Widget Data with Relationships**: Update Filament widget queries to eager load user and department relationships for rich data display

  - Modify `getTableQuery()` to include `->with(['user', 'user.department', 'assets'])`
  - Update `getTableColumns()` to display User Name, Department, Asset Type instead of just Ticket ID
  - Add time elapsed badge with color coding (Red: >2 days, Amber: >1 day, Green: <1 day)
  - Format: Primary = User Name, Secondary = Dept + Asset, Badge = Time Elapsed
  - _Requirements: Data Visualization, Rich Widget Display_

- [x] 5.0.3 **Implement Impersonation Security Middleware**: Create CheckImpersonation middleware with action blocking and audit logging

  - Create `app/Http/Middleware/CheckImpersonation.php`
  - Block critical actions: password change, email update, account deletion
  - Log all impersonation actions with admin_id and impersonated_user_id
  - Register middleware in `bootstrap/app.php` for portal routes
  - _Requirements: Impersonation Security, Audit Compliance_

- [x] 5.0.4 **Create Impersonation Visual Banner**: Implement yellow warning banner for impersonation state

  - Create `resources/views/components/impersonation-banner.blade.php`
  - Display admin name, impersonated user name, "Stop Impersonating" link
  - Fixed position at top of all portal pages (z-index: 50)
  - Yellow background (#FCD34D) with black text for high visibility
  - _Requirements: Impersonation UX, Security Transparency_

- [x] 5.0.5 **Add Filament Impersonation Action**: Implement "View as User" action in UserResource

  - Add impersonate action to UserResource table actions
  - Require confirmation modal with security warning
  - Set session variable `impersonate_user_id`
  - Log impersonation start with Laravel Auditing
  - Redirect to portal dashboard after impersonation start
  - _Requirements: Admin Impersonation, Audit Trail_

- [ ]\* 5.0.6 **Test Impersonation Security**: Verify action blocking and audit logging
  - Test blocked actions return 403 during impersonation
  - Verify audit logs contain admin_id and impersonated_user_id
  - Test "Stop Impersonating" functionality
  - Verify banner displays on all portal pages
  - _Requirements: Security Testing, Compliance Verification_

**Compliance Note**: Tasks 5.0.1-5.0.5 implement strategic architectural improvements based on visual audit findings and security best practices.

---

### 5.1 Unified Admin Dashboard

**Priority**: P1 | **Effort**: L

Create unified admin dashboard with integrated analytics.

- [ ] 5.1.1 Implement combined metrics (ticket volume, SLA compliance, asset utilization, overdue items)
- [ ] 5.1.2 Add real-time statistics with auto-refresh
- [ ] 5.1.3 Create interactive charts using Chart.js
- [ ] 5.1.4 Implement drill-down functionality for detailed analysis
- [ ] 5.1.5 Add export capabilities for all reports
- [ ] 5.1.6 Optimize performance with Redis caching
- [ ]\* 5.1.7 Write integration tests for cross-module metrics

**Requirements**: R11 | **Design**: Admin Dashboard

---

### 5.2 Cross-Module Search

**Priority**: P2 | **Effort**: M

Implement unified search across tickets and loan applications.

- [ ] 5.2.1 Create global search across helpdesk tickets and loan applications
- [ ] 5.2.2 Implement advanced filtering (module, status, date range, user)
- [ ] 5.2.3 Add search result ranking and relevance scoring
- [ ] 5.2.4 Implement search history and saved searches
- [ ] 5.2.5 Optimize performance with search indexing
- [ ]\* 5.2.6 Add export search results functionality

**Requirements**: R11 | **Design**: Search System

---

### 5.3 Asset-Ticket Linking

**Priority**: P2 | **Effort**: M

Implement asset-ticket linking for hardware issues.

- [ ] 5.3.1 **Implement Event-Driven Architecture** (AssetReturned event, CreateDamageTicketListener)
- [ ] 5.3.2 Create asset-ticket relationship with foreign key constraints
- [ ] 5.3.3 **Implement Soft Linking logic** (preserve historical ticket links if asset deleted)
- [ ] 5.3.4 Implement automatic ticket creation for damaged returns (within 5 seconds via event listener)
- [ ] 5.3.5 Add asset history tracking with linked tickets
- [ ] 5.3.6 Implement maintenance scheduling based on ticket patterns
- [ ] 5.3.7 Add asset condition monitoring and alerts
- [ ]\* 5.3.8 Write integration tests for event-driven asset-ticket linking (test event firing and listening independently)

**Requirements**: R11 | **Design**: Asset Integration, Event-Driven Architecture

**Decoupling**: Task 5.3.1 ensures Asset module doesn't know how to create tickets, only fires events.

---

### 5.4 Integrated Reporting

**Priority**: P2 | **Effort**: L

Create comprehensive reporting system.

- [ ] 5.4.1 Implement report builder with drag-and-drop interface
- [ ] 5.4.2 Create pre-built report templates
- [ ] 5.4.3 Add multiple export formats (CSV, PDF, Excel)
- [ ] 5.4.4 Implement scheduled report generation and email delivery
- [ ] 5.4.5 Add report sharing and collaboration features
- [ ] 5.4.6 **Implement OTP Handover Modal**: Admin interface to validate 4-digit Pickup OTP before marking asset as 'Issued'
- [ ] 5.4.7 **Implement OTP Generation**: Generate and send 4-digit OTP to borrower via email when loan approved
- [ ]\* 5.4.8 Optimize performance for large datasets

**Requirements**: R11 | **Design**: Reporting System, Asset Handover Logic

**Security Note**: Tasks 5.4.6-5.4.7 implement digital handshake verification for asset pickup.

---

## Phase 6: Testing, Optimization, and Deployment

### 6.1 Comprehensive Testing

**Priority**: P0 | **Effort**: XL

Implement comprehensive testing suite.

- [ ] 6.1.1 Write unit tests for business logic (target: 80%+ coverage)
- [ ] 6.1.2 Write feature tests for user workflows (target: 95%+ critical paths)
- [ ] 6.1.3 Write integration tests for cross-module functionality
- [ ] 6.1.4 Run accessibility tests with Lighthouse and axe DevTools (target: 100 score)
- [ ] 6.1.5 Perform cross-browser testing (Chrome 90+, Firefox 88+, Safari 14+, Edge 90+)
- [ ] 6.1.6 Test mobile devices across viewports (320px-1920px)
- [ ]\* 6.1.7 Generate test coverage reports and documentation

**Requirements**: R16 | **Design**: Testing Strategy

---

### 6.2 Performance Optimization

**Priority**: P1 | **Effort**: L

Optimize application performance to achieve Core Web Vitals targets.

- [ ] 6.2.1 Achieve Core Web Vitals (LCP <2.5s, FID <100ms, CLS <0.1, TTFB <600ms)
- [ ] 6.2.2 Achieve Lighthouse scores (Performance 90+, Accessibility 100, Best Practices 100, SEO 100)
- [ ] 6.2.3 Implement image optimization (WebP format, lazy loading, explicit dimensions)
- [ ] 6.2.4 Optimize CSS and JavaScript (code splitting, minification)
- [ ] 6.2.5 Implement Redis caching for frequently accessed data
- [ ] 6.2.6 Optimize database queries and add indexes
- [ ]\* 6.2.7 Document performance optimization strategies

**Requirements**: R08 | **Design**: Performance Strategy

---

### 6.3 Security Audit and Compliance

**Priority**: P0 | **Effort**: M

Conduct security audit and ensure PDPA 2010 compliance.

- [ ] 6.3.1 Perform security vulnerability assessment and remediation
- [ ] 6.3.2 Conduct PDPA 2010 compliance audit
- [ ] 6.3.3 Implement data encryption for sensitive information (AES-256)
- [ ] 6.3.4 Configure audit trail system with 7-year retention
- [ ] 6.3.5 Enforce security headers and HTTPS
- [ ]\* 6.3.6 Perform penetration testing and vulnerability scanning

**Requirements**: R14 | **Design**: Security Implementation

---

### 6.4 Documentation and Training

**Priority**: P1 | **Effort**: L

Create comprehensive documentation and training materials.

- [ ] 6.4.1 Write user manuals in Bahasa Melayu and English
- [ ] 6.4.2 Create administrator guides with system configuration
- [ ] 6.4.3 Document component library with usage examples
- [ ]\* 6.4.4 Create API documentation for integrations
- [ ]\* 6.4.5 Produce video tutorials for common workflows
- [ ]\* 6.4.6 Implement in-system help and tooltips

**Requirements**: R17 | **Design**: Documentation Strategy

---

### 6.5 Deployment and Production Setup

**Priority**: P0 | **Effort**: M

Configure production environment and deploy.

- [ ] 6.5.1 Configure production environment with load balancing
- [ ] 6.5.2 Setup CI/CD pipeline with automated testing
- [ ] 6.5.3 Perform database migration preservation
- [ ] 6.5.4 Implement monitoring and alerting system
- [ ] 6.5.5 Configure backup and disaster recovery procedures
- [ ]\* 6.5.6 Setup performance monitoring dashboards

**Requirements**: All requirements | **Design**: Deployment Strategy

---

### 6.6 User Acceptance Testing and Go-Live

**Priority**: P0 | **Effort**: M

Conduct UAT and coordinate go-live.

- [ ] 6.6.1 Conduct user acceptance testing with MOTAC stakeholders
- [ ] 6.6.2 Collect feedback and resolve issues
- [ ] 6.6.3 Execute go-live planning and communication
- [ ] 6.6.4 Conduct user training sessions
- [ ] 6.6.5 Distribute support documentation
- [ ]\* 6.6.6 Setup post-launch monitoring and support

**Requirements**: All requirements | **Design**: All design elements

---

## Task Dependencies

### Critical Path

1. Task 1.1 (Laravel Foundation) → 1.2 (Livewire) → 1.3 (Volt) → 2.1 (Component Structure) → 2.2-2.5 (Components) → 3.1-3.2 (Guest Forms) → 4.1-4.4 (Portal) → 6.1-6.6 (Testing & Deployment)

### Parallel Development Opportunities

- **Phase 1**: Tasks 1.4 (Tailwind) and 1.5 (Alpine) can run parallel to 1.1-1.3
- **Phase 2**: Tasks 2.2-2.5 can run parallel after 2.1 completes
- **Phase 3**: Tasks 3.1-3.3 can run parallel after Phase 2 completes
- **Phase 4**: Tasks 4.1-4.4 can run parallel after 4.1 completes
- **Phase 5**: Tasks 5.1-5.4 can run parallel after Phase 4 completes

---

## Risk Management

### High-Risk Tasks

1. **Task 1.1** - Critical path dependency, Laravel upgrade complexity
2. **Task 6.1** - Large scope, comprehensive testing requirements
3. **Task 5.1** - Complex cross-module integration
4. **Task 6.3** - Compliance and security audit requirements

### Mitigation Strategies

- Early prototyping for high-risk components
- Parallel development where dependencies allow
- Regular stakeholder reviews for validation
- Automated testing from day one
- Security review at each phase completion

---

## Success Metrics

### Technical Metrics

- **Code Coverage**: 80%+ overall, 95%+ critical paths
- **Performance**: Core Web Vitals compliance (LCP <2.5s, FID <100ms, CLS <0.1)
- **Accessibility**: 100% WCAG 2.2 AA compliance (Lighthouse score 100)
- **Security**: Zero critical vulnerabilities
- **Quality**: 95%+ automated test pass rate

### Business Metrics

- **User Satisfaction**: 90%+ positive feedback
- **System Adoption**: 95%+ of target users active
- **Performance**: 50%+ improvement in page load times
- **Accessibility**: 100% compliance with government standards
- **Maintenance**: 30%+ reduction in support tickets

---

## Timeline Summary

| Phase   | Duration    | Key Deliverables         | Critical Path |
| ------- | ----------- | ------------------------ | ------------- |
| Phase 1 | Weeks 1-3   | Foundation Setup         | Yes           |
| Phase 2 | Weeks 4-6   | Component Library        | Yes           |
| Phase 3 | Weeks 7-9   | Guest Forms              | Partial       |
| Phase 4 | Weeks 10-12 | Authenticated Portal     | Partial       |
| Phase 5 | Weeks 13-15 | Cross-Module Integration | No            |
| Phase 6 | Weeks 16-18 | Testing & Deployment     | Yes           |

**Total Duration**: 18 weeks  
**Critical Path**: 12 weeks  
**Buffer Time**: 6 weeks for parallel development

---

## Kiro IDE Automation

### Traceability Enforcement

**Commit Message Validation**:

Create `.kiro/rules/commit-message.rule`:

```yaml
name: "Enforce Requirement Traceability"
trigger: "pre-commit"
validation:
  pattern: "^(feat|fix|refactor|test|docs):\\s.+\\s\\(R\\d{2}(,\\s?R\\d{2})*\\)$"
  message: "Commit must reference requirement IDs (e.g., feat: add modal (R06, R07))"
```

**Component Scaffolding**:

Create Kiro Task: "Generate Volt Component"

```bash
# Auto-generates Volt component with:
# - OptimizedLivewireComponent trait
# - Standard metadata header (name, WCAG level, version, traceability)
# - Placeholder for state(), computed(), on()
php artisan make:volt {name} --with-metadata
```

### Quality Gate Automation

**CI/CD Pipeline** (.github/workflows/quality-gates.yml):

```yaml
name: Quality Gates
on: [pull_request]
jobs:
  accessibility:
    runs-on: ubuntu-latest
    steps:
      - name: Run axe-core accessibility tests
        run: php artisan test --filter=AccessibilityTest
      - name: Block PR if accessibility fails
        if: failure()
        run: exit 1

  visual-regression:
    runs-on: ubuntu-latest
    steps:
      - name: Run visual regression tests
        run: php artisan test --filter=SnapshotTest
      - name: Block PR if snapshots differ
        if: failure()
        run: exit 1
```

## Implementation Notes

### Optional Tasks (marked with `*`)

Optional tasks focus on testing, documentation, and nice-to-have features. These can be deferred for faster MVP delivery while maintaining core functionality and compliance requirements.

### Core Implementation Priority

1. Foundation and architecture (Phase 1)
2. Component library with WCAG compliance (Phase 2)
3. Guest forms and portal interfaces (Phases 3-4)
4. Cross-module integration (Phase 5)
5. Essential testing and deployment (Phase 6)

### Shift-Left Testing Priority

**Phase 2 Quality Gates** (Non-Negotiable):

- axe-core accessibility checks (automated)
- Visual regression snapshot tests
- WCAG 2.2 AA manual verification
- Component NOT "Done" until all gates pass

**Phase 3-4 Protection**:

- Visual regression tests prevent styling refactors from breaking forms
- Optimistic UI tests validate rollback scenarios

### Next Steps

To begin implementation:

1. Open this tasks.md file in Kiro IDE
2. Click "Start task" next to task 1.1.1
3. Complete each sub-task sequentially
4. Mark tasks complete as you finish them
5. Move to the next task only after completing the current one
6. **Enforce quality gates**: Components cannot proceed without passing accessibility tests

---

## Strategic Improvements Summary

This task list incorporates strategic architectural improvements:

1. **Tailwind 4.0 De-Risking**: HMR benchmarking (Task 1.4.3) validates integration before full development
2. **Volt Governance**: State complexity criteria replaces "lines of code" metric
3. **Shift-Left Testing**: Accessibility and visual regression integrated into Phase 2 (Tasks 2.1.4-2.1.6, 2.2.2-2.2.9)
4. **Optimistic UI**: Immediate feedback for 60-second email workflows (Tasks 3.1.4, 4.4.3)
5. **URL-Based Locale**: Resilient language persistence for in-app browsers (Task 3.1.7)
6. **Event-Driven Decoupling**: Asset-ticket linking via events (Task 5.3.1)
7. **Soft Linking**: Historical data preservation (Task 5.3.3)
8. **Admin Impersonation**: Debug tool for access issues (Task 4.4.8)
9. **Kiro IDE Automation**: Commit message validation, component scaffolding, CI/CD quality gates

**Risk Mitigation**: Early validation prevents Phase 6 refactoring. Quality gates block PRs on accessibility/visual regression failures.

---

**Document Version**: 2.0  
**Last Updated**: 2025-01-21  
**Status**: Tasks Approved - Strategic Improvements Integrated  
**Technology Stack**: Laravel 12.x | Livewire 3.x | Volt 1 | Tailwind CSS 4.1 | Alpine.js 3.x  
**Methodology**: Shift-Left Testing | Event-Driven Architecture | Optimistic UI
