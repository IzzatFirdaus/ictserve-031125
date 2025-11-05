# Implementation Plan - Unified Frontend Pages Redesign

## Overview

This implementation plan focuses on completing the frontend redesign for the ICTServe system, with emphasis on the **authenticated portal** (Phase 3) which is the critical missing piece. The backend infrastructure, component library, and guest forms are complete. This plan prioritizes the authenticated staff portal to provide a complete user experience.

## Current Status Summary

**✅ COMPLETED:**

- Phase 1: Component library with WCAG 2.2 AA compliance
- Phase 2 (Partial): Guest forms (helpdesk tickets, loan applications)
- Backend: Database schema, services, email workflows, RBAC

**🎯 PRIORITY: Phase 3 - Authenticated Portal** (Tasks 1-5)
**📋 REMAINING: Public pages, admin enhancements, final testing** (Tasks 6-10)

---

## Phase 3: Authenticated Portal (PRIORITY)

### Task 1: Authenticated Portal Layout and Navigation

**Goal**: Create the authenticated staff portal layout with proper navigation and WCAG compliance

- [x] 1.1 Enhance authenticated layout (resources/views/layouts/app.blade.php)

  - Verify proper HTML5 structure with semantic landmarks
  - Add skip links targeting #main-content, #sidebar-navigation, #user-menu
  - Include Vite assets and Livewire styles/scripts
  - Ensure MOTAC branding consistency
  - _Requirements: 18.1, 25.2_

- [x] 1.2 Create/enhance authenticated header component

  - File: resources/views/components/layout/auth-header.blade.php
  - Add MOTAC branding (Jata Negara + MOTAC logo)
  - Implement language switcher integration
  - Add notification bell with unread count badge (wire:poll.30s)
  - Implement user menu dropdown (profile, settings, logout)
  - Ensure 44×44px touch targets and proper ARIA attributes
  - _Requirements: 18.2, 18.4, 19.4_

- [x] 1.3 Create sidebar navigation component

  - File: resources/views/components/layout/portal-navigation.blade.php (verify/enhance)
  - Add role-based menu items (Staff, Approver, Admin, Superuser)
  - Implement active state styling with proper contrast
  - Add collapsible sidebar for mobile (Alpine.js)
  - Include proper ARIA navigation landmarks
  - _Requirements: 18.3, 25.3_

- [x] 1.4 Test authenticated layout
  - Test layout responsiveness (320px, 768px, 1280px, 1920px)
  - Test skip links and keyboard navigation (Tab, Shift+Tab, Alt+M, Alt+S, Alt+U)
  - Test role-based menu visibility for all 4 roles
  - Verify WCAG 2.2 Level AA compliance (Lighthouse 100 accessibility)
  - _Requirements: 24.1, 25.1_

### Task 2: Staff Dashboard (AuthenticatedDashboard)

**Goal**: Create personalized staff dashboard with statistics, recent activity, and quick actions

- [x] 2.1 Create AuthenticatedDashboard Livewire component

  - File: app/Livewire/Staff/AuthenticatedDashboard.php
  - Extend with OptimizedLivewireComponent trait
  - Implement #[Computed] properties: statistics(), recentTickets(), recentLoans()
  - Add caching strategy (5-minute cache with user-specific keys)
  - Implement query optimization with eager loading (with(['user', 'asset']))
  - _Requirements: 19.1, 24.2, 24.3_

- [x] 2.2 Create dashboard Blade template

  - File: resources/views/livewire/staff/authenticated-dashboard.blade.php
  - Use x-layout.app layout
  - Implement 4-column statistics grid (x-responsive.grid)
  - Add statistics cards: "My Open Tickets", "My Pending Loans", "My Approvals" (Grade 41+), "Overdue Items"
  - Implement real-time updates with wire:poll.30s
  - Use compliant color palette for status indicators
  - _Requirements: 19.1, 19.2_

- [x] 2.3 Implement quick actions section

  - Create horizontal button group with proper spacing
  - Add buttons: "New Ticket" (route to helpdesk.create), "Request Loan" (route to loan.guest.apply), "View All Services"
  - Ensure 44×44px minimum touch targets
  - Add proper focus indicators (3-4px outline, 2px offset, 3:1 contrast)
  - _Requirements: 19.3_

- [x] 2.4 Implement recent activity grid

  - Create 2-column responsive grid (My Recent Tickets | My Recent Loans)
  - Display max 5 items per column with x-data.status-badge
  - Add "View All" links to submission history
  - Implement loading states (wire:loading) and empty states
  - Use wire:key for list items
  - _Requirements: 19.2, 19.5_

- [x] 2.5 Add dashboard route

  - Route: /staff/dashboard (authenticated, verified)
  - Update routes/web.php to use AuthenticatedDashboard component
  - Test with different user roles (staff, approver, admin, superuser)
  - _Requirements: 24.1_

- [x] 2.6 Test staff dashboard
  - Test with all 4 user roles (verify Grade 41+ approval card visibility)
  - Test real-time updates and polling behavior
  - Test loading states and empty states
  - Verify responsive layout on mobile, tablet, desktop
  - Run Lighthouse audit (target: 90+ performance, 100 accessibility)
  - _Requirements: 24.1, 25.1_

### Task 3: User Profile Management (UserProfile)

**Goal**: Create user profile page with editable information, notification preferences, and password change

- [x] 3.1 Create UserProfile Livewire component

  - File: app/Livewire/Staff/UserProfile.php
  - Extend with OptimizedLivewireComponent trait
  - Add #[Validate] attributes for editable fields (name, phone)
  - Implement mount() to load user data
  - Implement updateProfile() method with validation
  - Implement updateNotificationPreferences() method
  - Implement updatePassword() method with current password verification
  - _Requirements: 20.1, 20.2, 20.3, 20.4_

- [x] 3.2 Create profile Blade template

  - File: resources/views/livewire/staff/user-profile.blade.php
  - Use x-layout.app layout
  - Create 3-card layout: Profile Information, Notification Preferences, Password Change
  - _Requirements: 20.1_

- [x] 3.3 Implement profile information card

  - Use x-ui.card component
  - Add editable fields: name (x-form.input), phone (x-form.input)
  - Add read-only fields: email, staff_id, grade, division
  - Implement Save button with wire:click="updateProfile"
  - Display success/error alerts (x-ui.alert)
  - Add wire:loading states
  - _Requirements: 20.1, 20.2_

- [x] 3.4 Implement notification preferences card

  - Use x-ui.card component
  - Add checkboxes (x-form.checkbox): ticket_updates, loan_approvals, system_announcements
  - Implement auto-save on change (wire:change="updateNotificationPreferences")
  - Add loading indicator (wire:loading)
  - Store preferences in users.notification_preferences JSON column
  - _Requirements: 20.3_

- [x] 3.5 Implement password change card

  - Use x-ui.card component
  - Add password fields: current_password, password, password_confirmation
  - Add password strength indicator (Alpine.js)
  - Implement validation (min 8 chars, uppercase, lowercase, number, special char)
  - Display success/error messages
  - Clear form on successful change
  - _Requirements: 20.4_

- [x] 3.6 Add profile route

  - Route: /staff/profile (authenticated)
  - Update routes/web.php to use UserProfile component
  - Test profile updates and password changes
  - _Requirements: 20.1_

- [x] 3.7 Test user profile page
  - Test form validation for all fields
  - Test auto-save functionality for notification preferences
  - Test password change with correct/incorrect current password
  - Verify WCAG 2.2 Level AA compliance
  - Test keyboard navigation through all form fields
  - _Requirements: 24.1, 25.1_

### Task 4: Submission History (SubmissionHistory)

**Goal**: Create submission history page with tabbed interface for tickets and loan applications

- [x] 4.1 Create SubmissionHistory Livewire component

  - File: app/Livewire/Staff/SubmissionHistory.php
  - Extend with OptimizedLivewireComponent trait
  - Add #[Lazy] attribute for lazy loading
  - Implement properties: activeTab, search, statusFilter, dateFrom, dateTo
  - Implement #[Computed] properties: filteredTickets(), filteredLoans()
  - Add query optimization with eager loading
  - Implement caching strategy (5-minute cache)
  - _Requirements: 21.1, 24.2_

- [x] 4.2 Create submission history Blade template

  - File: resources/views/livewire/staff/submission-history.blade.php
  - Use x-layout.app layout
  - Implement tabbed interface using x-navigation.tabs (My Tickets | My Loan Requests)
  - Add proper ARIA attributes (role="tablist", aria-selected)
  - _Requirements: 21.1_

- [x] 4.3 Implement search and filters section

  - Add search input (wire:model.live.debounce.300ms="search")
  - Add status filter dropdown (x-form.select)
  - Add date range filters (x-form.input type="date")
  - Add Reset filters button
  - Ensure filters work for both tabs
  - _Requirements: 21.4_

- [x] 4.4 Implement tickets tab content

  - Create data table using x-data.table
  - Add columns: Ticket ID, Subject, Status, Priority, Created Date, Last Updated
  - Implement sortable columns with ARIA sort attributes
  - Add pagination (x-navigation.pagination)
  - Include status badges (x-data.status-badge)
  - Add "View Details" link for each ticket
  - _Requirements: 21.2_

- [x] 4.5 Implement loans tab content

  - Create card grid (x-responsive.grid cols="1" mdCols="2" lgCols="3")
  - Display asset image/icon, name, model
  - Show status badge (x-data.status-badge)
  - Display loan period, approval status, return status
  - Add "View Details" button
  - Implement empty state message
  - _Requirements: 21.3_

- [x] 4.6 Add submission history route

  - Route: /staff/history (authenticated, verified)
  - Add to routes/web.php
  - Test with users who have multiple submissions
  - _Requirements: 21.1_

- [x] 4.7 Test submission history page
  - Test tab switching and content loading
  - Test search functionality across both tabs
  - Test filters (status, date range)
  - Test sorting and pagination
  - Verify WCAG 2.2 Level AA compliance
  - Test keyboard navigation (Tab, Arrow keys for table)
  - _Requirements: 24.1, 25.1_

### Task 5: Guest Submission Claiming and Approvals

**Goal**: Create interfaces for claiming guest submissions and approving loan applications (Grade 41+)

- [x] 5.1 Create ClaimSubmissions Livewire component

  - File: app/Livewire/Staff/ClaimSubmissions.php
  - Extend with OptimizedLivewireComponent trait
  - Implement searchEmail property with validation
  - Implement #[Computed] properties: foundTickets(), foundLoans()
  - Implement searchSubmissions() method safety
  - Add audit logging for claim actions
  - _Requirements: 22.1, 22.2_

- [x] 5.2 Create claim submissions Blade template

  - File: resources/views/livewire/staff/claim-submissions.blade.php
  - Use x-layout.app layout
  - Create search form card with email input
  - Display results count and tabbed interface (Tickets | Loan Requests)
  - Implement results tables/grids with checkboxes
  - Add "Claim Selected" button with confirmation modal (x-ui.modal)
  - Show success/error alerts after claiming
  - _Requirements: 22.2, 22.3_

- [x] 5.3 Create ApprovalInterface Livewire component (Grade 41+)

  - File: app/Livewire/Staff/ApprovalInterface.php
  - Extend with OptimizedLivewireComponent trait
  - Add authorization check (Grade 41+ only) in mount()
  - Implement filter properties: status, dateFrom, dateTo, applicantSearch
  - Implement #[Computed] property: pendingApprovals()
  - Implement approve() and reject() methods
  - Add email notification triggers
  - Add audit logging for approval actions
  - _Requirements: 23.1, 23.2, 23.3_

- [x] 5.4 Create approval interface Blade template

  - File: resources/views/livewire/staff/approval-interface.blade.php
  - Use x-layout.app layout
  - Create approvals data table with filters
  - Implement approval detail modal (x-ui.modal)
  - Add approval action form with comments (x-form.textarea)
  - Create confirmation modal for approve/reject actions
  - Display approval history for each application
  - _Requirements: 23.2, 23.3, 23.4_

- [x] 5.5 Add claiming and approval routes

  - Route: /staff/claim-submissions (authenticated, verified)
  - Route: /staff/approvals (authenticated, verified, Grade 41+ middleware)
  - Update routes/web.php
  - Test authorization for approval interface
  - _Requirements: 22.1, 23.1_

- [x] 5.6 Test claiming and approval functionality
  - Test email search and claiming process
  - Test approval workflow (Grade 41+ only)
  - Test email notifications after approval/rejection
  - Verify audit trail logging
  - Test with users below Grade 41 (should be denied access to approvals)
  - Verify WCAG 2.2 Level AA compliance
  - _Requirements: 22.4, 23.4, 24.1_

---

## Phase 2: Public Pages (REMAINING)

### Task 6: Public Information Pages

**Goal**: Create accessibility statement, contact, and services pages

- [x] 6.1 Create accessibility statement page

  - File: resources/views/pages/accessibility.blade.php
  - Route: /accessibility
  - Implement page header with breadcrumbs
  - Create commitment section with x-ui.card
  - Add standards section (WCAG 2.2 AA, ISO 9241, PDPA 2010)
  - Create accessibility features grid
  - Implement known limitations section
  - Add supported technologies section (browsers, screen readers)
  - Add contact section for accessibility issues
  - _Requirements: 2.1, 2.2, 2.3, 2.4, 2.5, 2.6, 6.1_

- [x] 6.2 Create contact page

  - File: resources/views/pages/contact.blade.php
  - Route: /contact
  - Implement page header with breadcrumbs
  - Create responsive two-column layout (contact info + form)
  - Add contact information sidebar with cards (phone, email, address, hours)
  - Implement contact form with validation (name, email, subject, message)
  - Add form accessibility features (ARIA attributes, error messages)
  - Implement emergency contact alert
  - Add form submission with email notification
  - _Requirements: 3.1, 3.2, 3.3, 3.4, 6.3, 6.5_

- [x] 6.3 Create services page

  - File: resources/views/pages/services.blade.php
  - Route: /services
  - Implement page header with breadcrumbs
  - Create services grid with x-responsive.grid
  - Add service cards: Helpdesk, Asset Loan, Service Request, Issue Reporting, Support
  - Implement CTA section with gradient background
  - Add footer compliance note
  - Link to respective service forms
  - _Requirements: 4.1, 4.2, 4.3, 4.4, 4.5, 6.1, 8.2_

- [x] 6.4 Test public pages
  - Test all links and navigation
  - Verify bilingual support (Bahasa Melayu + English)
  - Run Lighthouse accessibility audit (target: 100/100)
  - Test with screen readers (NVDA, JAWS, VoiceOver)
  - Verify WCAG 2.2 Level AA compliance
  - _Requirements: 6.6, 7.2, 13.1_

---

## Phase 4: Admin Panel Enhancement (REMAINING)

### Task 7: Filament Resource Frontend Enhancement

**Goal**: Enhance Filament resources with better frontend presentation and WCAG compliance

- [x] 7.1 Enhance HelpdeskTicketResource frontend

  - Verify table columns display properly
  - Add guest/authenticated type badges
  - Enhance filters UI (submission type, asset linkage, status)
  - Improve form layout with sections
  - Add asset integration field visibility
  - Test responsive behavior
  - _Requirements: 2.1, 3.2, 3.3_

- [x] 7.2 Enhance LoanApplicationResource frontend

  - Verify table columns display properly
  - Add approval workflow visualization
  - Enhance approval status badges
  - Improve filters UI (approval status, submission type)
  - Add approval timeline display
  - Test responsive behavior
  - _Requirements: 3.1, 3.2, 10.1_

- [x] 7.3 Enhance AssetResource frontend

  - Verify asset lifecycle display
  - Add condition tracking visualization
  - Enhance maintenance history display
  - Improve asset detail view
  - Add asset availability calendar integration
  - Test responsive behavior
  - _Requirements: 3.1, 18.1, 18.2_

- [x] 7.4 Test Filament resources
  - Test CRUD operations for all resources
  - Test role-based access control
  - Test filters and search functionality
  - Verify data integrity
  - Run accessibility audit on admin panel
  - _Requirements: 3.1, 4.4, 10.1_

### Task 8: Unified Admin Dashboard Widgets

**Goal**: Create unified dashboard widgets combining helpdesk and asset loan metrics

- [x] 8.1 Create HelpdeskStatsOverview widget

  - File: app/Filament/Widgets/HelpdeskStatsOverview.php
  - Display guest vs authenticated ticket metrics
  - Show SLA compliance statistics
  - Add ticket volume trends
  - Use compliant color palette
  - Implement caching (5-minute cache)
  - _Requirements: 3.2, 4.1, 13.1_

- [x] 8.2 Create AssetLoanStatsOverview widget

  - File: app/Filament/Widgets/AssetLoanStatsOverview.php
  - Display utilization metrics
  - Show approval workflow statistics
  - Add overdue items count
  - Use compliant color palette
  - Implement caching (5-minute cache)
  - _Requirements: 3.2, 4.1, 13.1_

- [x] 8.3 Create CrossModuleIntegrationChart widget

  - File: app/Filament/Widgets/CrossModuleIntegrationChart.php
  - Display asset-ticket linking statistics
  - Show maintenance workflow metrics
  - Add chart visualization (Chart.js)
  - Use compliant color palette
  - Implement real-time updates (wire:poll.300s)
  - _Requirements: 3.2, 13.3_

- [x] 8.4 Test unified dashboard
  - Test with different admin roles
  - Test real-time updates
  - Verify performance (caching effectiveness)
  - Test responsive behavior
  - _Requirements: 4.1, 13.1, 13.3_

---

## Phase 5: Performance & Accessibility (FINAL VALIDATION)

### Task 9: Performance Optimization and Testing

**Goal**: Validate and optimize performance across all pages

- [x] 9.1 Run Core Web Vitals tests

  - Test LCP < 2.5s on all pages (guest, authenticated, admin)
  - Test FID < 100ms on all pages
  - Test CLS < 0.1 on all pages
  - Test TTFB < 600ms on all pages
  - Document results and optimize as needed
  - _Requirements: 7.1, 7.2, 14.1_

- [x] 9.2 Run Lighthouse performance audit

  - Run on all guest pages (target: 90+ performance, 100 accessibility)
  - Run on all authenticated pages (target: 90+ performance, 100 accessibility)
  - Run on admin panel pages (target: 85+ performance, 100 accessibility)
  - Document results and create optimization plan
  - _Requirements: 7.1, 7.2, 24.1_

- [x] 9.3 Optimize identified performance issues
  - Implement image lazy loading where missing
  - Optimize Livewire component queries
  - Add strategic caching where needed
  - Minimize JavaScript bundle size
  - _Requirements: 7.1, 7.2, 15.4_

### Task 10: Comprehensive Accessibility Testing

**Goal**: Validate WCAG 2.2 Level AA compliance across all pages

- [ ] 10.1 Run automated accessibility testing

  - Run axe DevTools on all pages
  - Run Lighthouse accessibility audit (target: 100/100)
  - Document all issues found
  - _Requirements: 25.1, 6.1_

- [ ] 10.2 Manual accessibility testing

  - Test with NVDA screen reader (Windows)
  - Test with JAWS screen reader (Windows)
  - Test with VoiceOver screen reader (macOS/iOS)
  - Test keyboard navigation on all pages
  - Test focus indicators visibility
  - _Requirements: 25.1, 25.2, 6.5_

- [ ] 10.3 Fix accessibility issues

  - Fix all critical and serious issues
  - Address moderate issues where feasible
  - Document any known limitations
  - Update accessibility statement
  - _Requirements: 25.3, 2.1_

- [ ] 10.4 Cross-browser testing

  - Test on Chrome 90+ (Windows, macOS, Android)
  - Test on Firefox 88+ (Windows, macOS)
  - Test on Safari 14+ (macOS, iOS)
  - Test on Edge 90+ (Windows)
  - Document browser-specific issues
  - _Requirements: 24.1, 7.1_

- [ ] 10.5 Final validation
  - Run complete test suite
  - Verify all requirements met
  - Get stakeholder approval
  - Prepare for deployment
  - _Requirements: 24.1, 24.5_

---

## Success Criteria

The frontend pages redesign will be considered complete when:

1. **Phase 1**: Component library established with WCAG 2.2 AA compliance (COMPLETED)
2. **Phase 2 (Partial)**: Guest forms operational (COMPLETED)
3. 🎯 **Phase 3**: All authenticated portal components implemented and tested (PRIORITY)
4. 📋 **Phase 2 (Remaining)**: Public information pages created
5. 📋 **Phase 4**: Admin panel frontend enhanced
6. 📋 **Phase 5**: Performance and accessibility validated
7. 📋 **All pages**: Lighthouse scores 90+ performance, 100 accessibility
8. 📋 **All pages**: WCAG 2.2 Level AA compliance verified
9. 📋 **All pages**: Core Web Vitals targets met (LCP <2.5s, FID <100ms, CLS <0.1)
10. 📋 **Documentation**: User guides and technical documentation complete

---

**Document Version**: 2.0 (Focused)  
**Last Updated**: 2025-11-05  
**Status**: Ready for Phase 3 Implementation  
**Priority**: Authenticated Portal (Tasks 1-5) - _Requirements: 24.1, 24.5_
