# Implementation Plan - Unified Frontend Pages Redesign

## Overview

This implementation plan integrates the frontend redesign requirements from three core systems:

-   **ICTServe Core System** (hybrid architecture with guest + authenticated + admin)
-   **Updated Helpdesk Module** (guest-only forms with email workflows)
-   **Updated Loan Module** (dual approval workflows with asset tracking)

The unified frontend provides a seamless experience across all access levels with WCAG 2.2 Level AA compliance, optimal Core Web Vitals performance, and complete cross-module integration.

## Integration Achievements

**✅ COMPLETED INTEGRATION:**

-   Unified component library with x-category.component-name structure
-   WCAG 2.2 AA compliant color palette (Primary #0056b3, Success #198754, Warning #ff8c00, Danger #b50c0c)
-   Cross-module integration (asset-ticket linking, unified dashboards, shared organizational data)
-   Hybrid architecture supporting guest, authenticated, and admin access levels
-   Bilingual support (Bahasa Melayu primary, English secondary) with session/cookie persistence
-   OptimizedLivewireComponent trait with caching, lazy loading, and query optimization
-   Email-based workflows with 60-second delivery SLA
-   Four-role RBAC (Staff, Approver, Admin, Superuser)

## Implementation Phases

**Phase 1**: Foundation & Component Library (Tasks 1-5)
**Phase 2**: Guest-Only Forms & Public Pages (Tasks 6-10)
**Phase 3**: Authenticated Portal (Tasks 11-15)
**Phase 4**: Admin Panel Integration (Tasks 16-18)
**Phase 5**: Performance & Accessibility (Tasks 19-22)
**Phase 6**: Documentation & Deployment (Tasks 23-25)

---

## Phase 1: Foundation & Component Library

### Task 1: Unified Component Library Audit and Enhancement

-   [-] 1. Unified Component Library Audit and Enhancement

    -   [x] 1.1 Audit existing component library structure

        -   Review all components in resources/views/components/
        -   Verify proper categorization (accessibility/, data/, form/, layout/, navigation/, responsive/, ui/)
        -   Check for duplicate or obsolete components
        -   Document component inventory with usage patterns
        -   _Requirements: 11.1, 17.1, 17.2_

    -   [x] 1.2 Implement standardized component metadata headers
        -   Add metadata to all components: @component, @description, @author, @trace, @updated, @version
        -   Link components to D03 requirements and D04 design specifications
        -   Document WCAG 2.2 Level AA compliance features
        -   Add usage examples and integration guidelines
        -   _Requirements: 17.1, 17.2, 17.3, 17.4_
    -   [-] 1.3 Verify WCAG 2.2 AA compliant color palette implementation

        -   Ensure compliant colors used exclusively: Primary #0056b3 (6.8:1), Success #198754 (4.9:1), Warning #ff8c00 (4.5:1), Danger #b50c0c (8.2:1)
        -   Remove deprecated colors completely: Warning Yellow #F1C40F, Danger Red #E74C3C
        -   Verify 4.5:1 text contrast and 3:1 UI component contrast ratios
        -   Test color contrast with WebAIM Contrast Checker
        -   _Requirements: 6.1, 6.3, 14.2, 15.2_

    -   [x] 1.4 Implement responsive design patterns

        -   Verify breakpoints: mobile (320px-414px), tablet (768px-1024px), desktop (1280px-1920px)
        -   Test all components across viewport sizes
        -   Ensure no horizontal scrolling on mobile
        -   Verify 44×44px minimum touch targets on all interactive elements
        -   _Requirements: 6.5, 14.5, 15.4_

    -   [x] 1.5 Create component testing framework
        -   Set up automated accessibility testing with axe DevTools
        -   Create Livewire component tests for interactive components
        -   Implement visual regression testing for UI components
        -   Document testing procedures and standards
        -   _Requirements: 13.1, 13.2, 13.3_

### Task 2: Layout Components Integration

-   [ ] 2. Layout Components Integration

    -   [ ] 2.1 Verify guest layout (x-layout.guest)

        -   Ensure resources/views/components/layout/guest.blade.php exists and is properly structured
        -   Verify MOTAC branding (Jata Negara, MOTAC logo)
        -   Check language switcher integration
        -   Test navigation menu with proper ARIA landmarks
        -   Verify footer with compliance information
        -   _Requirements: 1.1, 5.1, 6.1, 14.1_

    -   [ ] 2.2 Verify authenticated layout (x-layout.app)

        -   Ensure resources/views/components/layout/app.blade.php exists
        -   Verify authenticated header with user menu and notifications
        -   Check sidebar navigation with role-based menu items
        -   Test breadcrumbs and main content area
        -   Verify proper ARIA landmarks (banner, navigation, main, contentinfo)
        -   _Requirements: 18.1, 18.2, 25.2, 25.3_

    -   [ ] 2.3 Implement skip links and keyboard shortcuts

        -   Add x-accessibility.skip-links component to all layouts
        -   Implement Alt+M (main content), Alt+S (sidebar), Alt+U (user menu)
        -   Test keyboard navigation across all pages
        -   Verify screen reader announcements
        -   _Requirements: 25.2, 6.5, 11.1_

    -   [ ] 2.4 Verify header and footer consistency
        -   Check header component across guest and authenticated layouts
        -   Verify footer component with proper links and copyright
        -   Test language switcher visibility and functionality
        -   Ensure consistent MOTAC branding
        -   _Requirements: 6.1, 6.4, 18.4_

### Task 3: Form Components Enhancement

-   [ ] 3. Form Components Enhancement

    -   [ ] 3.1 Verify form input components

        -   Check x-form.input with proper ARIA attributes
        -   Verify x-form.select with keyboard navigation
        -   Test x-form.textarea with character counter
        -   Ensure x-form.checkbox with proper labeling
        -   Verify x-form.file-upload with drag-and-drop support
        -   _Requirements: 11.4, 12.2, 21.1, 6.3_

    -   [ ] 3.2 Implement real-time validation patterns

        -   Add wire:model.live.debounce.300ms for dynamic fields
        -   Implement wire:model.lazy for large text fields
        -   Create proper ARIA error messaging (aria-invalid, aria-describedby)
        -   Add loading states with wire:loading
        -   _Requirements: 11.4, 12.2, 21.4, 6.4_

    -   [ ] 3.3 Create hybrid form support

        -   Implement conditional field display (@auth/@guest directives)
        -   Support guest fields (guest_name, guest_email, guest_phone, guest_grade, guest_division)
        -   Pre-fill authenticated user data from auth()->user()
        -   Add proper validation for both guest and authenticated submissions
        -   _Requirements: 1.1, 1.2, 1.3, 21.1, 22.1_

    -   [ ] 3.4 Test form accessibility
        -   Test keyboard navigation through all form fields
        -   Verify screen reader compatibility (NVDA, JAWS, VoiceOver)
        -   Check error message announcements with ARIA live regions
        -   Test form submission with loading states
        -   _Requirements: 6.3, 6.4, 11.1, 13.1_

### Task 4: UI Components Verification

-   [ ] 4. UI Components Verification

    -   [ ] 4.1 Verify button components

        -   Check x-ui.button variants (primary, secondary, danger, ghost)
        -   Verify 44×44px minimum touch targets
        -   Test focus indicators (3-4px outline, 2px offset, 3:1 contrast)
        -   Ensure proper ARIA labels and roles
        -   _Requirements: 6.3, 6.5, 14.4, 15.2_

    -   [ ] 4.2 Verify card components

        -   Check x-ui.card variants (default, elevated, outlined)
        -   Test hover effects and transitions
        -   Verify proper semantic structure
        -   Ensure responsive behavior
        -   _Requirements: 8.2, 11.1, 14.1_

    -   [ ] 4.3 Verify alert and notification components

        -   Check x-ui.alert types (success, warning, danger, info)
        -   Verify ARIA live regions (aria-live="polite" or "assertive")
        -   Test dismissible alerts with keyboard support
        -   Ensure proper color contrast
        -   _Requirements: 11.2, 11.3, 25.5, 6.3_

    -   [ ] 4.4 Verify modal and dropdown components
        -   Check x-ui.modal with focus management
        -   Verify x-ui.dropdown with keyboard navigation
        -   Test Escape key to close
        -   Ensure proper ARIA attributes (aria-modal, aria-haspopup)
        -   _Requirements: 11.2, 11.4, 6.5, 25.2_

### Task 5: Data and Navigation Components

-   [ ] 5. Data and Navigation Components

    -   [ ] 5.1 Verify data table components

        -   Check x-data.table with sortable columns
        -   Verify proper ARIA sort attributes
        -   Test keyboard navigation through table rows
        -   Ensure responsive table behavior (horizontal scroll or card layout on mobile)
        -   _Requirements: 9.1, 11.1, 21.2, 4.2_

    -   [ ] 5.2 Verify status badge components

        -   Check x-data.status-badge with compliant colors
        -   Verify proper semantic meaning (not relying on color alone)
        -   Test with screen readers
        -   Ensure consistent usage across modules
        -   _Requirements: 6.1, 15.2, 1.5, 11.1_

    -   [ ] 5.3 Verify navigation components

        -   Check x-navigation.breadcrumbs with structured data
        -   Verify x-navigation.tabs with ARIA tablist
        -   Test x-navigation.pagination with keyboard support
        -   Ensure x-navigation.sidebar with role-based menu items
        -   _Requirements: 18.3, 25.3, 6.5, 11.1_

    -   [ ] 5.4 Verify accessibility components
        -   Check x-accessibility.skip-links functionality
        -   Verify x-accessibility.aria-live-region for announcements
        -   Test x-accessibility.focus-trap for modals
        -   Ensure x-accessibility.language-switcher with session/cookie persistence
        -   _Requirements: 25.2, 25.5, 20.1, 20.5, 6.5_

---

## Phase 2: Guest-Only Forms & Public Pages

### Task 6: Welcome and Public Pages

-   [ ] 6. Welcome and Public Pages

    -   [ ] 6.1 Redesign welcome page (welcome.blade.php)

        -   Implement hero section with MOTAC branding and gradient background
        -   Create service cards using x-data.service-card (Helpdesk, Asset Loan, Services, Support)
        -   Add statistics section with x-ui.card components
        -   Implement CTA section with proper styling
        -   Add x-accessibility.skip-links and x-accessibility.aria-live-region
        -   _Requirements: 1.1, 6.1, 6.4, 8.2, 14.1_

    -   [ ] 6.2 Redesign accessibility statement page (accessibility.blade.php)

        -   Implement page header with breadcrumbs
        -   Create commitment section with x-ui.card
        -   Add standards and guidelines section (WCAG 2.2 AA, ISO 9241)
        -   Create accessibility features grid
        -   Implement known limitations and supported technologies sections
        -   Add contact section with gradient background
        -   _Requirements: 2.1, 2.2, 2.3, 2.4, 2.5, 2.6, 6.1_

    -   [ ] 6.3 Redesign contact page (contact.blade.php)

        -   Implement page header with breadcrumbs and alerts
        -   Create responsive two-column layout (contact info + form)
        -   Add contact information sidebar with cards
        -   Implement contact form with validation
        -   Add form accessibility features (ARIA attributes, error messages)
        -   Implement emergency contact alert
        -   _Requirements: 3.1, 3.2, 3.3, 3.4, 6.3, 6.5_

    -   [ ] 6.4 Redesign services page (services.blade.php)
        -   Implement page header with breadcrumbs
        -   Create services grid with x-responsive.grid
        -   Add service cards (Helpdesk, Asset Loan, Service Request, Issue Reporting, Support)
        -   Implement CTA section with gradient background
        -   Add footer compliance note
        -   _Requirements: 4.1, 4.2, 4.3, 4.4, 4.5, 6.1, 8.2_

### Task 7: Guest Helpdesk Ticket Form

-   [ ] 7. Guest Helpdesk Ticket Form

    -   [ ] 7.1 Create GuestTicketForm Livewire Volt component

        -   Implement enhanced guest fields (name, email, phone, staff_id, grade, division)
        -   Add ticket details fields with real-time validation
        -   Implement asset selection with conditional display
        -   Add OptimizedLivewireComponent trait
        -   _Requirements: 1.1, 1.2, 4.2, 6.3, 11.1_

    -   [ ] 7.2 Create guest ticket form Blade template

        -   Use x-ui.card for sections
        -   Implement x-form.input and x-form.select with WCAG compliance
        -   Add x-form.file-upload for attachments
        -   Ensure 44×44px touch targets and proper ARIA attributes
        -   _Requirements: 4.1, 5.2, 6.2, 11.4_

    -   [ ] 7.3 Implement form validation and error handling

        -   Add real-time validation with 300ms debouncing
        -   Implement proper ARIA error messaging
        -   Add loading states with wire:loading
        -   Create success page with ticket number and tracking link
        -   _Requirements: 6.3, 6.4, 11.4, 1.2_

    -   [ ] 7.4 Test guest ticket form
        -   Test form submission workflow end-to-end
        -   Verify email confirmation within 60 seconds
        -   Test keyboard navigation and screen reader compatibility
        -   Verify WCAG 2.2 Level AA compliance
        -   _Requirements: 11.1, 11.6, 22.2, 6.1_

### Task 8: Guest Asset Loan Application Form

-   [ ] 8. Guest Asset Loan Application Form

    -   [ ] 8.1 Create GuestLoanApplicationForm Livewire Volt component

        -   Implement applicant information fields (name, email, phone, staff_id, grade, division)
        -   Add asset selection with availability checking
        -   Implement loan period selection with date pickers
        -   Add OptimizedLivewireComponent trait
        -   _Requirements: 1.1, 1.4, 10.2, 21.1_

    -   [ ] 8.2 Create asset availability calendar component

        -   Implement AssetAvailabilityCalendar Livewire component
        -   Add real-time availability checking with visual feedback
        -   Implement booking calendar interface with conflict detection
        -   Add keyboard navigation support (arrow keys, Enter, Escape)
        -   _Requirements: 3.4, 17.4, 10.4, 13.2_

    -   [ ] 8.3 Create guest loan application Blade template

        -   Use multi-step wizard pattern (applicant info → asset selection → loan period → confirmation)
        -   Implement x-form components with WCAG compliance
        -   Add progress indicator with ARIA attributes
        -   Ensure responsive design and touch targets
        -   _Requirements: 1.5, 6.1, 7.1, 21.5_

    -   [ ] 8.4 Test guest loan application form
        -   Test complete application workflow
        -   Verify asset availability checking
        -   Test email confirmation and approval request
        -   Verify WCAG 2.2 Level AA compliance
        -   _Requirements: 1.4, 2.1, 10.2, 6.1_

### Task 9: Email Templates and Notifications

-   [ ] 9. Email Templates and Notifications

    -   [ ] 9.1 Create guest notification email templates

        -   Ticket confirmation email with tracking link
        -   Loan application confirmation email
        -   Status update emails for both modules
        -   Use compliant color palette and WCAG compliance
        -   _Requirements: 1.2, 1.4, 8.1, 9.1_

    -   [ ] 9.2 Create bilingual email templates

        -   Implement Bahasa Melayu (primary) and English (secondary) versions
        -   Add automatic language detection based on user preferences
        -   Ensure consistent MOTAC branding
        -   Include proper email accessibility features
        -   _Requirements: 6.4, 15.2, 15.3, 8.1_

    -   [ ] 9.3 Implement email approval workflow

        -   Create approval request emails with secure token links
        -   Implement approval processing endpoints
        -   Add email approval tracking and audit logging
        -   Test 7-day token expiration
        -   _Requirements: 2.1, 2.3, 2.4, 9.4_

    -   [ ] 9.4 Test email system
        -   Test email delivery within 60-second SLA
        -   Verify bilingual email generation
        -   Test queue processing and retry mechanisms
        -   Verify email approval workflow
        -   _Requirements: 8.2, 9.1, 9.2, 2.4_

### Task 10: Cross-Page Consistency

-   [ ] 10. Cross-Page Consistency

    -   [ ] 10.1 Verify consistent header across all guest pages

        -   Check MOTAC branding (Jata Negara, MOTAC logo)
        -   Verify language switcher presence and functionality
        -   Test navigation menu with proper ARIA landmarks
        -   Ensure consistent styling
        -   _Requirements: 6.1, 6.4, 18.4_

    -   [ ] 10.2 Verify consistent footer across all guest pages

        -   Check footer links (About, Accessibility, Contact)
        -   Verify copyright notice
        -   Test all footer links
        -   Ensure consistent styling
        -   _Requirements: 6.1, 6.4_

    -   [ ] 10.3 Verify bilingual support on all guest pages

        -   Test language switcher on each page
        -   Verify all translation keys working
        -   Check for hardcoded text
        -   Test language persistence across navigation
        -   _Requirements: 6.2, 6.6, 20.5_

    -   [ ] 10.4 Run comprehensive accessibility audit on guest pages
        -   Run Lighthouse accessibility audit (target: 100/100)
        -   Run axe DevTools audit
        -   Test with NVDA, JAWS, VoiceOver screen readers
        -   Document and fix all issues
        -   _Requirements: 6.6, 7.2, 13.1_

---

## Phase 3: Authenticated Portal

### Task 11: Authenticated Portal Layout

-   [ ] 11. Authenticated Portal Layout

    -   [ ] 11.1 Implement authenticated layout structure

        -   Create resources/views/layouts/app.blade.php with proper HTML5 structure
        -   Add meta tags (charset, viewport, CSRF token)
        -   Include Vite assets and Livewire styles/scripts
        -   Add skip links targeting #main-content, #sidebar-navigation, #user-menu
        -   _Requirements: 18.1, 25.2_

    -   [ ] 11.2 Implement authenticated header component

        -   Create resources/views/layouts/partials/authenticated-header.blade.php
        -   Add MOTAC branding (Jata Negara + MOTAC logo)
        -   Implement language switcher
        -   Add notification bell with unread count badge
        -   Implement user menu dropdown (profile, settings, logout)
        -   _Requirements: 18.2, 18.4, 19.4_

    -   [ ] 11.3 Implement sidebar navigation component

        -   Create resources/views/layouts/partials/sidebar-navigation.blade.php
        -   Add role-based menu items (Staff, Approver, Admin, Superuser)
        -   Implement active state styling
        -   Add collapsible sidebar for mobile
        -   _Requirements: 18.3, 25.3_

    -   [ ] 11.4 Test authenticated layout
        -   Test layout responsiveness (mobile, tablet, desktop)
        -   Test skip links and keyboard navigation
        -   Test role-based menu visibility
        -   Verify WCAG 2.2 Level AA compliance
        -   _Requirements: 24.1, 25.1_

### Task 12: Staff Dashboard

-   [ ] 12. Staff Dashboard

    -   [ ] 12.1 Create AuthenticatedDashboard Livewire component

        -   Create App\Livewire\AuthenticatedDashboard.php with OptimizedLivewireComponent trait
        -   Implement #[Computed] properties for statistics, recentTickets, recentLoans
        -   Add caching strategy (5-minute cache with user-specific keys)
        -   Implement query optimization with eager loading
        -   _Requirements: 19.1, 24.2, 24.3_

    -   [ ] 12.2 Implement statistics cards section

        -   Create 4-column grid using x-responsive.grid
        -   Add "My Open Tickets", "My Pending Loans", "My Approvals" (Grade 41+), "Overdue Items" cards
        -   Implement real-time updates with wire:poll.30s
        -   Add proper icons and color coding
        -   _Requirements: 19.1, 19.2_

    -   [ ] 12.3 Implement quick actions section

        -   Create horizontal button group
        -   Add "New Ticket", "Request Loan", "View All Services" buttons
        -   Ensure proper focus indicators and touch targets
        -   _Requirements: 19.3_

    -   [ ] 12.4 Implement recent activity grid

        -   Create 2-column grid (My Recent Tickets | My Recent Loans)
        -   Display max 5 items per column with status badges
        -   Add "View All" links
        -   Implement loading states and empty states
        -   _Requirements: 19.2, 19.5_

    -   [ ] 12.5 Test staff dashboard
        -   Test with different user roles
        -   Test real-time updates and polling
        -   Test loading states and empty states
        -   Verify responsive layout
        -   _Requirements: 24.1, 25.1_

### Task 13: User Profile Management

-   [ ] 13. User Profile Management

    -   [ ] 13.1 Create UserProfile Livewire component

        -   Create App\Livewire\UserProfile.php with OptimizedLivewireComponent trait
        -   Add #[Validate] attributes for editable fields
        -   Implement mount() and updateProfile() methods
        -   _Requirements: 20.1, 20.2_

    -   [ ] 13.2 Implement profile information card

        -   Create card with editable fields (name, phone)
        -   Add read-only fields (email, staff_id, grade, division)
        -   Implement auto-save or Save button
        -   Display success/error alerts
        -   _Requirements: 20.1, 20.2_

    -   [ ] 13.3 Implement notification preferences card

        -   Create card with email notification checkboxes
        -   Add options: ticket_updates, loan_approvals, system_announcements
        -   Implement auto-save on change
        -   Add loading indicator
        -   _Requirements: 20.3_

    -   [ ] 13.4 Implement password change card

        -   Create card with password fields
        -   Add password strength indicator
        -   Implement validation and change functionality
        -   Display success/error messages
        -   _Requirements: 20.4_

    -   [ ] 13.5 Test user profile page
        -   Test form validation
        -   Test auto-save functionality
        -   Test password change
        -   Verify WCAG 2.2 Level AA compliance
        -   _Requirements: 24.1, 25.1_

### Task 14: Submission History

-   [ ] 14. Submission History

    -   [ ] 14.1 Create SubmissionHistory Livewire component

        -   Create App\Livewire\SubmissionHistory.php with OptimizedLivewireComponent trait
        -   Add #[Lazy] attribute for lazy loading
        -   Implement search and filter properties
        -   Implement #[Computed] properties for filteredTickets and filteredLoans
        -   _Requirements: 21.1, 24.2_

    -   [ ] 14.2 Implement tabbed interface

        -   Create tabbed interface using x-navigation.tabs (My Tickets | My Loan Requests)
        -   Implement tab switching with proper ARIA attributes
        -   Add loading states for each tab
        -   _Requirements: 21.1_

    -   [ ] 14.3 Implement search and filters section

        -   Add search input (wire:model.debounce.300ms)
        -   Add status filter dropdown
        -   Add date range filter
        -   Add Reset filters button
        -   _Requirements: 21.4_

    -   [ ] 14.4 Implement tickets tab content

        -   Create data table using x-data.table
        -   Add columns: Ticket ID, Subject, Status, Priority, Created Date, Last Updated
        -   Implement sortable columns with ARIA sort attributes
        -   Add pagination
        -   _Requirements: 21.2_

    -   [ ] 14.5 Implement loans tab content

        -   Create card grid (x-responsive.grid cols="1" mdCols="2" lgCols="3")
        -   Display asset image/icon, name, model, status badge
        -   Show loan period, approval status, return status
        -   Add "View Details" button
        -   _Requirements: 21.3_

    -   [ ] 14.6 Test submission history page
        -   Test tab switching and content loading
        -   Test search and filters functionality
        -   Test sorting and pagination
        -   Verify WCAG 2.2 Level AA compliance
        -   _Requirements: 24.1, 25.1_

### Task 15: Guest Submission Claiming and Approvals

-   [ ] 15. Guest Submission Claiming and Approvals

    -   [ ] 15.1 Create ClaimSubmissions Livewire component

        -   Create App\Livewire\ClaimSubmissions.php with OptimizedLivewireComponent trait
        -   Implement searchEmail property with validation
        -   Implement searchSubmissions() and claimSelected() methods
        -   _Requirements: 22.1, 22.2_

    -   [ ] 15.2 Implement search form and results

        -   Create search form card with email input
        -   Display results count and tabbed interface (Tickets | Loan Requests)
        -   Implement results tables/grids with checkboxes
        -   Add Claim Selected button with confirmation modal
        -   _Requirements: 22.2, 22.3_

    -   [ ] 15.3 Create ApprovalInterface Livewire component (Grade 41+)

        -   Create App\Livewire\ApprovalInterface.php with authorization check
        -   Implement filter properties and #[Computed] property for pendingApprovals
        -   Add approval/rejection functionality
        -   _Requirements: 23.1, 23.2, 23.3_

    -   [ ] 15.4 Implement approval interface UI

        -   Create approvals data table with filters
        -   Implement approval detail modal
        -   Add approval action form with comments
        -   Create confirmation modal
        -   _Requirements: 23.2, 23.3, 23.4_

    -   [ ] 15.5 Test claiming and approval functionality
        -   Test email search and claiming process
        -   Test approval workflow (Grade 41+ only)
        -   Test email notifications
        -   Verify audit trail logging
        -   _Requirements: 22.4, 23.4, 24.1_

---

## Phase 4: Admin Panel Integration

### Task 16: Filament Resource Enhancement

-   [ ] 16. Filament Resource Enhancement

    -   [ ] 16.1 Enhance HelpdeskTicketResource

        -   Verify form with hybrid submission support
        -   Add asset integration fields
        -   Create table with guest/authenticated type badges
        -   Add filters for submission type and asset linkage
        -   _Requirements: 2.1, 3.2, 3.3_

    -   [ ] 16.2 Enhance LoanApplicationResource

        -   Verify form with hybrid submission support
        -   Add approval workflow visualization
        -   Create table with approval status badges
        -   Add filters for approval status and submission type
        -   _Requirements: 3.1, 3.2, 10.1_

    -   [ ] 16.3 Enhance AssetResource

        -   Verify asset CRUD operations
        -   Add asset lifecycle management
        -   Implement condition tracking
        -   Add maintenance history integration
        -   _Requirements: 3.1, 18.1, 18.2_

    -   [ ] 16.4 Test Filament resources
        -   Test CRUD operations for all resources
        -   Test role-based access control
        -   Test filters and search functionality
        -   Verify data integrity
        -   _Requirements: 3.1, 4.4, 10.1_

### Task 17: Unified Admin Dashboard

-   [ ] 17. Unified Admin Dashboard

    -   [ ] 17.1 Create unified dashboard widgets

        -   Create HelpdeskStatsOverview widget with guest vs authenticated metrics
        -   Create AssetLoanStatsOverview widget with utilization metrics
        -   Create CrossModuleIntegrationChart widget
        -   Use compliant color palette
        -   _Requirements: 3.2, 4.1, 13.1_

    -   [ ] 17.2 Implement real-time data updates

        -   Add wire:poll.300s for dashboard widgets
        -   Implement caching strategy (5-minute cache)
        -   Add loading states
        -   _Requirements: 13.3, 24.2_

    -   [ ] 17.3 Create cross-module reports

        -   Implement integrated reports combining helpdesk and asset loan data
        -   Add data export functionality (CSV, PDF, Excel)
        -   Create report templates
        -   _Requirements: 3.4, 3.6, 13.2_

    -   [ ] 17.4 Test unified dashboard
        -   Test with different admin roles
        -   Test real-time updates
        -   Test data export functionality
        -   Verify performance
        -   _Requirements: 4.1, 13.1, 13.3_

### Task 18: Cross-Module Integration

-   [ ] 18. Cross-Module Integration

    -   [ ] 18.1 Implement asset-ticket linking

        -   Create automatic helpdesk ticket creation for damaged returned assets
        -   Implement asset selection in helpdesk ticket forms
        -   Add maintenance workflow integration
        -   _Requirements: 2.2, 2.3, 16.1, 16.5_

    -   [ ] 18.2 Implement unified search

        -   Create search interface across tickets and loan applications
        -   Add asset identifier and user information search
        -   Implement search result ranking
        -   _Requirements: 16.4, 4.2_

    -   [ ] 18.3 Implement shared organizational data

        -   Verify synchronization for users, divisions, grades
        -   Add referential integrity constraints
        -   Implement data consistency validation
        -   _Requirements: 16.2, 8.1, 4.3_

    -   [ ] 18.4 Test cross-module integration
        -   Test asset-ticket linking workflow
        -   Test unified search functionality
        -   Test data consistency across modules
        -   Verify audit trail
        -   _Requirements: 16.1, 16.2, 16.4_

---

## Phase 5: Performance & Accessibility

### Task 19: Performance Optimization

-   [ ] 19. Performance Optimization

    -   [ ] 19.1 Implement Livewire optimization patterns

        -   Apply OptimizedLivewireComponent trait to all components
        -   Implement #[Computed] properties for derived data
        -   Add #[Lazy] loading for heavy components
        -   Use wire:model.live.debounce.300ms for dynamic fields
        -   _Requirements: 14.1, 14.2, 24.2_

    -   [ ] 19.2 Optimize database queries

        -   Add proper indexing for all foreign keys
        -   Implement eager loading to prevent N+1 queries
        -   Add Redis caching for frequently accessed data
        -   _Requirements: 8.1, 8.2, 24.3_

    -   [ ] 19.3 Optimize frontend assets

        -   Configure Vite for optimal bundling
        -   Implement image optimization (WebP with JPEG fallbacks)
        -   Add CSS purging and minification
        -   Implement lazy loading for images
        -   _Requirements: 7.1, 7.2, 15.4_

    -   [ ] 19.4 Run Core Web Vitals tests

        -   Test LCP < 2.5s on all pages
        -   Test FID < 100ms on all pages
        -   Test CLS < 0.1 on all pages
        -   Test TTFB < 600ms on all pages
        -   _Requirements: 7.1, 7.2, 14.1_

    -   [ ] 19.5 Run Lighthouse performance audit
        -   Run on all guest pages (target: 90+ performance, 100 accessibility)
        -   Run on all authenticated pages
        -   Run on admin panel pages
        -   Document results and optimize
        -   _Requirements: 7.1, 7.2, 24.1_

### Task 20: Accessibility Compliance

-   [ ] 20. Accessibility Compliance

    -   [ ] 20.1 Implement focus indicators

        -   Add 3-4px outline with 2px offset on all interactive elements
        -   Ensure 3:1 contrast ratio minimum for focus indicators
        -   Test keyboard navigation on all pages
        -   _Requirements: 25.1, 6.5_

    -   [ ] 20.2 Verify skip links and keyboard shortcuts

        -   Test skip links (Alt+M, Alt+S, Alt+U)
        -   Verify skip links target correct elements
        -   Test keyboard shortcuts functionality
        -   _Requirements: 25.2_

    -   [ ] 20.3 Verify ARIA landmarks

        -   Check role="banner" on header with aria-label
        -   Check role="navigation" on sidebar with aria-label
        -   Check role="main" on main content with aria-label
        -   Check role="contentinfo" on footer with aria-label
        -   _Requirements: 25.3_

    -   [ ] 20.4 Verify touch targets

        -   Ensure minimum 44×44px for all interactive elements
        -   Test on mobile devices (iOS, Android)
        -   Verify proper spacing between touch targets
        -   _Requirements: 25.4_

    -   [ ] 20.5 Implement ARIA live regions

        -   Add aria-live="polite" for status updates
        -   Add aria-live="assertive" for error messages
        -   Test with screen readers (NVDA, JAWS, VoiceOver)
        -   Verify announcements are clear and timely
        -   _Requirements: 25.5_

    -   [ ] 20.6 Run comprehensive accessibility audit
        -   Run Lighthouse accessibility audit on all pages (target: 100/100)
        -   Run axe DevTools audit on all pages
        -   Test with NVDA screen reader (Windows)
        -   Test with JAWS screen reader (Windows)
        -   Test with VoiceOver screen reader (macOS)
        -   Document and fix all issues
        -   _Requirements: 25.1, 6.6, 13.1_

### Task 21: Browser Compatibility Testing

-   [ ] 21. Browser Compatibility Testing

    -   [ ] 21.1 Test on desktop browsers

        -   Test on Chrome 90+ (Windows, macOS, Linux)
        -   Test on Firefox 88+ (Windows, macOS, Linux)
        -   Test on Safari 14+ (macOS)
        -   Test on Edge 90+ (Windows)
        -   _Requirements: 7.3, 13.1_

    -   [ ] 21.2 Test on mobile browsers

        -   Test on Chrome Mobile (Android)
        -   Test on Safari Mobile (iOS)
        -   Test responsive design (320px-414px)
        -   Test touch interactions
        -   _Requirements: 7.3, 15.4_

    -   [ ] 21.3 Document browser-specific issues
        -   Document any browser-specific bugs
        -   Implement workarounds or polyfills
        -   Test fixes across all browsers
        -   _Requirements: 7.3_

### Task 22: Security and Compliance Validation

-   [ ] 22. Security and Compliance Validation

    -   [ ] 22.1 Verify role-based access control

        -   Test four-role RBAC (Staff, Approver, Admin, Superuser)
        -   Verify proper authorization on all routes
        -   Test policy-based authorization
        -   _Requirements: 10.1, 4.4_

    -   [ ] 22.2 Verify audit trail functionality

        -   Test audit logging for all models
        -   Verify 7-year retention policy
        -   Test audit log viewing and searching
        -   _Requirements: 10.2, 10.5_

    -   [ ] 22.3 Verify data encryption and security

        -   Test AES-256 encryption for sensitive data
        -   Verify TLS 1.3 for data in transit
        -   Test secure token generation for email approvals
        -   Verify CSRF protection
        -   _Requirements: 10.3, 10.4_

    -   [ ] 22.4 Conduct security penetration testing
        -   Perform penetration testing for vulnerabilities
        -   Validate PDPA 2010 compliance
        -   Test audit trail integrity
        -   Document findings and remediation
        -   _Requirements: 10.4, 6.2_

---

## Phase 6: Documentation & Deployment

### Task 23: Implementation Documentation

-   [ ] 23. Implementation Documentation

    -   [ ] 23.1 Create implementation summary

        -   Document all changes made to each page
        -   List all components used
        -   Document new patterns and conventions
        -   Include before/after screenshots
        -   _Requirements: 8.4, 17.1_

    -   [ ] 23.2 Update component usage documentation

        -   Document new component usage patterns
        -   Add examples to design.md
        -   Update component library documentation
        -   _Requirements: 8.4, 17.2_

    -   [ ] 23.3 Create testing documentation

        -   Document test results (accessibility, performance, browser compatibility)
        -   Include Lighthouse scores for all pages
        -   Include StandardsComplianceChecker scores
        -   Document known issues or limitations
        -   _Requirements: 8.4, 13.1_

    -   [ ] 23.4 Update system documentation
        -   Update D10 (Source Code Documentation) with new components
        -   Update D12 (UI/UX Design Guide) with implemented patterns
        -   Update D14 (Style Guide) with WCAG 2.2 AA compliance verification
        -   Document component library usage patterns
        -   _Requirements: 17.1, 17.2, 17.3, 17.4_

### Task 24: User Documentation

-   [ ] 24. User Documentation

    -   [ ] 24.1 Create end-user guides

        -   Document helpdesk module features for end users
        -   Document asset loan module features for end users
        -   Include screenshots of key workflows
        -   Create bilingual versions (English and Bahasa Melayu)
        -   _Requirements: 8.4, 6.4_

    -   [ ] 24.2 Create admin user guides

        -   Document Filament admin panel features
        -   Create guides for ticket management
        -   Create guides for asset and loan management
        -   Document reporting and analytics features
        -   _Requirements: 8.4, 3.2_

    -   [ ] 24.3 Create video tutorials

        -   Create video tutorials for guest form submissions
        -   Create video tutorials for authenticated portal features
        -   Create video tutorials for admin panel operations
        -   Add captions in both languages
        -   _Requirements: 8.4, 6.4_

    -   [ ] 24.4 Create FAQ and troubleshooting guides
        -   Document common issues and solutions
        -   Create troubleshooting flowcharts
        -   Add contact information for support
        -   _Requirements: 8.4_

### Task 25: Deployment and Monitoring

-   [ ] 25. Deployment and Monitoring

    -   [ ] 25.1 Create deployment checklist

        -   List all files modified
        -   List dependencies (should be minimal)
        -   List environment variables
        -   List database migrations
        -   Create rollback plan
        -   _Requirements: 8.4_

    -   [ ] 25.2 Conduct peer code review

        -   Request review from 2+ team members
        -   Address all review comments
        -   Get approval from tech lead
        -   _Requirements: 8.3_

    -   [ ] 25.3 Conduct user acceptance testing

        -   Test with actual MOTAC users
        -   Gather feedback on usability
        -   Test with users who have disabilities
        -   Document feedback and address critical issues
        -   _Requirements: 8.4_

    -   [ ] 25.4 Deploy to staging environment

        -   Deploy all changes to staging
        -   Run smoke tests on staging
        -   Verify all pages work correctly
        -   Test with production-like data
        -   _Requirements: 8.4_

    -   [ ] 25.5 Conduct final QA on staging

        -   Run full test suite (accessibility, performance, browser compatibility)
        -   Verify all requirements are met
        -   Get sign-off from QA team
        -   _Requirements: 8.4_

    -   [ ] 25.6 Deploy to production

        -   Create deployment plan with rollback strategy
        -   Deploy during maintenance window
        -   Monitor for errors and performance issues
        -   Verify all pages work correctly in production
        -   _Requirements: 8.4_

    -   [ ] 25.7 Post-deployment monitoring
        -   Monitor Core Web Vitals for 7 days
        -   Monitor error logs for issues
        -   Gather user feedback
        -   Address any issues that arise
        -   _Requirements: 8.4, 7.2_

---

## Success Criteria

### Functional Requirements

-   ✅ Hybrid architecture supporting guest, authenticated, and admin access
-   ✅ Unified component library with consistent styling and accessibility
-   ✅ Cross-module integration between helpdesk and asset loan systems
-   ✅ Email-based workflows with 60-second delivery SLA
-   ✅ Bilingual support with session/cookie persistence

### Technical Requirements

-   ✅ WCAG 2.2 Level AA compliance across all interfaces (target: 100/100 Lighthouse accessibility)
-   ✅ Core Web Vitals targets achieved (LCP <2.5s, FID <100ms, CLS <0.1, TTFB <600ms)
-   ✅ Lighthouse performance scores (90+ performance, 100 accessibility, 100 best practices, 100 SEO)
-   ✅ Browser compatibility (Chrome 90+, Firefox 88+, Safari 14+, Edge 90+)
-   ✅ Mobile responsiveness (320px-414px mobile, 768px-1024px tablet, 1280px-1920px desktop)

### Integration Requirements

-   ✅ Seamless helpdesk and asset loan module integration
-   ✅ Shared organizational data consistency
-   ✅ Automated maintenance workflow integration
-   ✅ Unified dashboard analytics
-   ✅ Cross-module search functionality

### Quality Requirements

-   ✅ 80%+ overall test coverage, 95%+ for critical paths
-   ✅ 95%+ StandardsComplianceChecker scores
-   ✅ Zero critical accessibility violations
-   ✅ Complete D00-D15 documentation compliance
-   ✅ Comprehensive audit trail with 7-year retention

---

## Estimated Timeline

**Phase 1: Foundation & Component Library** (Tasks 1-5): 2-3 weeks
**Phase 2: Guest-Only Forms & Public Pages** (Tasks 6-10): 3-4 weeks
**Phase 3: Authenticated Portal** (Tasks 11-15): 3-4 weeks
**Phase 4: Admin Panel Integration** (Tasks 16-18): 2-3 weeks
**Phase 5: Performance & Accessibility** (Tasks 19-22): 2-3 weeks
**Phase 6: Documentation & Deployment** (Tasks 23-25): 2-3 weeks

**Total Estimated Time**: 14-20 weeks (3.5-5 months)

---

**Document Version**: 2.0  
**Last Updated**: 2025-11-03  
**Author**: Frontend Engineering Team  
**Status**: Ready for Implementation  
**Integration**: ICTServe System + Updated Helpdesk Module + Updated Loan Module
