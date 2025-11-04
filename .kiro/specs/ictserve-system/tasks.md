# Implementation Plan

## Phase 1: Foundation & Core Infrastructure

- [x] 1. Project Setup and Core Infrastructure

  - Initialize Laravel 12 project with required dependencies (Livewire 3, Volt, Filament 4, Tailwind CSS 3, Vite 4)
  - Configure database connections (MySQL 8.0+ primary, Redis 7.0+ cache/sessions)
  - Set up Vite build configuration with Gzip/Brotli compression, code splitting, Terser minification
  - Configure environment files for development, staging, and production with proper security settings
  - Set up TailwindCSS with compliant color palette and comprehensive content paths for purging
  - _Requirements: 1.1, 4.1, 4.2, 4.3, 4.4, 4.5, 19.3, 19.4_

- [x] 2. Database Schema and Models Implementation

  - [x] 2.1 Create hybrid architecture database migrations

    - Users table with four roles (staff, approver, admin, superuser), staff_id, grade_id, division_id, locale
    - Divisions, grades tables for organizational structure
    - Assets, asset_categories, asset_transactions tables with availability tracking
    - Helpdesk_tickets table with nullable user_id and guest fields (guest_name, guest_email, guest_phone)
    - Loan_applications table with nullable user_id, approver_id and guest fields (applicant_name, applicant_email, applicant_phone)
    - Approval tracking fields: approval_token, token_expires_at, approval_method (email/portal), approval_remarks
    - Audit_logs, notifications, settings tables with 7-year retention support
    - _Requirements: 1.1, 1.3, 1.6, 7.1, 7.2, 7.5, 9.5, 22.1, 23.1_

  - [x] 2.2 Implement Eloquent models with hybrid support

    - User model with four role methods (isStaff, isApprover, isAdmin, isSuperuser) and canApprove() helper
    - HelpdeskTicket model with nullable user_id, isGuestSubmission(), getSubmitterNameAttribute(), getSubmitterEmailAttribute()
    - LoanApplication model with nullable user_id and approver_id, generateApprovalToken(), isTokenValid(), approval_method tracking
    - Define proper relationships (belongsTo, hasMany) supporting both guest and authenticated submissions
    - Add model factories for testing with guest and authenticated variants
    - Implement model observers for audit trail automation and email notifications
    - _Requirements: 1.1, 1.3, 1.6, 4.5, 7.5, 9.4, 22.1, 23.2_

  - [x] 2.3 Set up comprehensive audit trail system
    - Configure Laravel Auditing package for all models (User, HelpdeskTicket, LoanApplication, Asset)
    - Track guest submissions, authenticated submissions, and administrative actions separately
    - Implement audit log viewing interfaces in Filament admin with filtering by submission type
    - Create audit report generation with 7-year retention and immutable logs
    - Add audit trail for dual approval workflows (email-based and portal-based)
    - _Requirements: 2.2, 5.5, 9.2, 9.5, 23.6_

- [x] 3. Hybrid Authentication and User Management System

  - [x] 3.1 Implement Laravel Breeze/Jetstream for authenticated staff portal

    - Installed Laravel Breeze with Livewire Volt components (login, register, password reset, email verification)
    - Authentication routes configured in routes/auth.php
    - Volt components created in resources/views/livewire/pages/auth/
    - Auth middleware protecting internal routes while keeping guest forms public
    - Session management configured with database driver (Redis ready)
    - _Requirements: 1.1, 1.3, 22.1, 22.2, 22.4, 9.1, 9.3, 23.1_

  - [x] 3.2 Set up four-role RBAC system

    - Four user roles defined: staff, approver, admin, superuser in User model
    - Role helper methods implemented: isStaff(), isApprover(), isAdmin(), isSuperuser(), canApprove(), hasAdminAccess()
    - EnsureUserHasRole middleware created for role-based route protection
    - UserPolicy created with viewAny(), view(), create(), update(), updateRole(), delete() authorization methods
    - HelpdeskTicketPolicy and LoanApplicationPolicy migrated to four-role system (removed Spatie dependencies)
    - UserFactory enhanced with default 'staff' role and state methods (staff(), approver(), admin(), superuser())
    - Comprehensive test suite created with 10 tests covering all RBAC functionality (all passing)
    - _Requirements: 1.6, 3.1, 3.2, 3.3, 9.1, 9.2, 22.5, 23.2_

  - [x] 3.3 Create comprehensive user management in Filament

    - Filament UserResource created with form schema, table configuration, and pages (Create, Edit, List)
    - UserForm schema with role-based field visibility (only superuser can change roles)
    - Role assignment interface with proper authorization via UserPolicy
    - User profile management supporting staff_id, grade_id, division_id, position_id
    - UserResource navigation visibility and access control based on hasAdminAccess()
    - Password handling for create vs edit contexts (required on create, optional on edit)
    - _Requirements: 3.1, 3.2, 3.3, 3.4, 9.3, 22.2_

  - [x] 3.4 Create hybrid submission tracking and claiming system
    - StaffPortalController fully implemented with index() (dashboard), claim() (claim submissions), profile() (user profile)
    - Models support nullable user_id for guest submissions (HelpdeskTicket, LoanApplication)
    - Audit trail configured with $auditInclude in User model (tracks role, name, email, status, etc.)
    - Submission claiming functionality in StaffPortalController (link guest submissions via email verification)
    - Staff portal routes configured in routes/web.php under /staff prefix with auth + verified middleware
    - _Requirements: 1.1, 1.2, 1.3, 11.7, 22.1, 22.6, 23.3_

## Phase 2: Core Module Implementation

- [x] 4. Helpdesk Module Implementation with Hybrid Support

  - [x] 4.1 Create hybrid ticket submission system

    - ✅ Built Livewire guest form component (app/Livewire/Helpdesk/SubmitTicket.php)
    - ✅ HelpdeskTicket model supports hybrid architecture with nullable user_id and guest fields
    - ✅ Routes configured for guest (/helpdesk/create) and authenticated (/helpdesk/authenticated/create)
    - ✅ File attachment support implemented (HelpdeskAttachment model, relationship defined)
    - ✅ Automatic ticket numbering implemented (generateTicketNumber() called in HelpdeskTicketObserver)
    - ✅ Confirmation email implemented (TicketCreatedConfirmation sent via TicketNotificationService)
    - ✅ Guest ticket tracking component implemented (app/Livewire/Helpdesk/TrackTicket.php)
    - _Requirements: 1.1, 1.2, 1.3, 11.1, 11.6, 22.2, 22.3_

  - [x] 4.2 Implement Filament admin resources for helpdesk

    - ✅ HelpdeskTicketResource with form schema, table configuration, and pages
    - ✅ Ticket assignment interface (divisions, agencies, users) via bulk actions
    - ✅ Status lifecycle management (OPEN → ASSIGNED → IN_PROGRESS → PENDING_USER → RESOLVED → CLOSED)
    - ✅ SLA tracking indicators and breach alerts in table columns
    - ✅ Bulk operations (assign, update status, close) with confirmation dialogs
    - ✅ TicketCategoryResource for category management
    - _Requirements: 2.2, 2.5, 3.1, 3.3, 13.1-13.5_

  - [x] 4.3 Build helpdesk ticket management workflows

    - ✅ TicketAssignmentService: Manual and automatic assignment with workload balancing
    - ✅ Internal comments system with is_internal flag for staff-only comments
    - ✅ SLATrackingService: Automated escalation at 25% breach threshold
    - ✅ Notification system: 4 notification classes (Assigned, StatusUpdated, CommentAdded, SLABreachWarning)
    - ✅ HelpdeskTicketObserver: Automatic ticket number generation, SLA calculation, status notifications
    - ✅ HelpdeskCommentObserver: Comment notifications to submitters and assigned users
    - ✅ MonitorSLACommand: Scheduled command for SLA monitoring
    - ✅ Resolution and closure workflows with status transitions
    - _Requirements: 2.2, 2.5, 8.4, 10.1, 10.3, 13.1-13.5, 22.3_

  - [x] 4.4 Create authenticated helpdesk portal components

    - ✅ Dashboard component: Stats overview (open, pending, resolved, claimable) + recent tickets
    - ✅ MyTickets component: Paginated list with search, status filter, and claim functionality
    - ✅ TicketDetails component: Full ticket view with comments, attachments, and claim option
    - ✅ HybridHelpdeskService: Guest ticket claiming with email verification and audit trail
    - ✅ All components support hybrid architecture (guest + authenticated)
    - _Requirements: 22.2, 22.3, 22.6, 23.3_

  - [x] 4.5 Build helpdesk reporting and analytics
    - Create unified dashboard showing ticket statistics and KPIs
    - Implement report generation for ticket volume, resolution times, agent performance
    - Build analytics covering both guest and authenticated submissions
    - Add data export functionality (CSV, PDF, Excel)
    - _Requirements: 3.2, 3.5, 3.6, 8.1, 8.2, 8.5_

- [x] 5. Asset Loan Module Implementation with Dual Approval

  - [x] 5.1 Create Filament asset inventory management system

    - Create AssetResource with form schema, table configuration, and pages
    - Create AssetCategoryResource for category management
    - Implement asset specification tracking (model, serial number, condition, location)
    - Add asset condition tracking and maintenance history
    - Build asset availability calendar using Livewire with booking visualization
    - Implement asset utilization reporting and analytics
    - _Requirements: 2.3, 3.1, 3.3, 7.1_

  - [x] 5.2 Implement hybrid loan application workflow

    - ✅ Created GuestLoanApplication Livewire component (app/Livewire/GuestLoanApplication.php)
    - ✅ LoanApplication model supports hybrid architecture with nullable user_id and guest fields
    - ✅ Routes configured for guest (/loan/apply) and authenticated (/loans/create)
    - ✅ EmailApprovalController exists for email-based approval
    - ✅ Approval token generation implemented (generateApprovalToken(), isTokenValid())
    - ✅ DualApprovalService implemented (app/Services/DualApprovalService.php)
    - ✅ Approval matrix implemented (ApprovalMatrixService determines approver by grade and asset value)
    - ✅ Email templates implemented (approval-request, application-decision, approval-confirmation, application-submitted, status-updated)
    - ✅ Portal-based approval interface implemented (LoanApplicationsTable with approve/reject/extend actions)
    - ✅ Guest loan tracking component implemented (app/Livewire/GuestLoanTracking.php)
    - _Requirements: 1.4, 1.5, 1.6, 2.1, 2.2, 6.2, 10.1, 10.4, 12.1-12.5, 22.2, 23.2_

  - [x] 5.3 Build DualApprovalService and email workflow

    - Create DualApprovalService with sendApprovalRequest(), processEmailApproval(), processPortalApproval()
    - Implement approval matrix logic based on grade and asset value
    - Build email templates for approval requests (with dual approval options)
    - Create email templates for approval decisions (approved/declined)
    - Implement approval decision tracking (approval_method: email/portal, approval_remarks)
    - Send status update emails within 60 seconds to applicants
    - _Requirements: 1.4, 1.5, 1.6, 10.1, 10.2, 12.1-12.5, 23.2_

  - [x] 5.4 Create Filament loan application management

    - Create LoanApplicationResource with form schema, table configuration, and pages
    - Implement approval interface for authenticated approvers (portal-based approval)
    - Build loan status lifecycle management
    - Add bulk operations (approve, decline, extend)
    - Create loan reporting and analytics
    - _Requirements: 3.1, 3.3, 3.4, 8.1, 8.2_

  - [x] 5.5 Build authenticated loan portal components

    - Create AuthenticatedLoanDashboard Livewire component
    - Build LoanHistory component for viewing all user applications
    - Implement LoanDetails component with status tracking
    - Create LoanExtension component for extension requests
    - Add guest submission claiming functionality
    - _Requirements: 22.2, 22.3, 22.6, 23.3_

  - [x] 5.6 Build asset transaction management
    - Create asset check-out process with condition assessment
    - Implement asset check-in process with return condition verification
    - Build overdue asset tracking with automated reminders
    - Add damage reporting with automatic maintenance ticket creation
    - Implement transaction history and audit trail
    - _Requirements: 2.3, 3.2, 3.5, 10.4_

- [x] 6. Module Integration and Cross-System Features

  - [x] 6.1 Implement unified dashboards

    - Create admin dashboard combining helpdesk (ticket volume, SLA compliance) and asset loan metrics (utilization, overdue items)
    - Build authenticated staff dashboard showing personal tickets, loans, and pending approvals (for approvers)
    - Implement real-time updates using Livewire polling with 300-second refresh for dashboards
    - Add quick stats widgets with WCAG 2.2 AA compliant visualizations
    - _Requirements: 1.1, 1.3, 1.4, 3.4, 3.5, 8.1, 22.2_

  - [x] 6.2 Create cross-module integration services

    - Implement automatic ticket creation for damaged returned assets within 5 seconds
    - Build asset-ticket linking for hardware-related issues using asset_id foreign key
    - Create unified search across both modules (tickets and loans) in admin panel
    - Add cross-module reporting combining data from helpdesk and asset loan systems
    - _Requirements: 2.1, 2.2, 2.3, 2.5_

  - [x] 6.3 Build unified reporting system
    - Create integrated reports combining data from both modules (tickets, loans, assets)
    - Implement data export functionality (CSV, PDF, Excel) with 50MB file size limit
    - Build scheduled report generation with automated email delivery to designated admin users
    - Add report templates for common analytics (SLA compliance, asset utilization, approval times)
    - _Requirements: 3.4, 3.6, 8.2, 8.5_

## Phase 3: Frontend & User Experience with WCAG 2.2 AA Compliance

- [x] 7. Unified Frontend Component Library and Design Systemem

  - [x] 7.1 Create WCAG 2.2 AA compliant component library

    - Build unified Blade component library structure:
      - layout/ (guest.blade.php, app.blade.php, header.blade.php, auth-header.blade.php, footer.blade.php)
      - form/ (input.blade.php, select.blade.php, textarea.blade.php, checkbox.blade.php, file-upload.blade.php)
      - ui/ (button.blade.php, card.blade.php, alert.blade.php, badge.blade.php, modal.blade.php)
      - navigation/ (breadcrumbs.blade.php, pagination.blade.php, skip-links.blade.php)
      - data/ (table.blade.php, status-badge.blade.php, progress-bar.blade.php)
      - accessibility/ (aria-live.blade.php, focus-trap.blade.php, language-switcher.blade.php)
    - Implement responsive design using Tailwind CSS 3 for desktop (1280px-1920px), tablet (768px-1024px), mobile (320px-414px)
    - Create MOTAC branding with compliant color palette:
      - Primary #0056b3 (6.8:1 contrast), Success #198754 (4.9:1), Warning #ff8c00 (4.5:1), Danger #b50c0c (8.2:1)
      - Remove deprecated colors: Warning Yellow #F1C40F and Danger Red #E74C3C completely
    - Ensure keyboard navigation with visible focus indicators (3-4px outline, 2px offset, 3:1 contrast minimum)
    - Implement proper ARIA labels, landmarks (header, nav, main, footer), and semantic HTML5 structure
    - Ensure minimum 4.5:1 text contrast and 3:1 UI component contrast ratios across all components
    - Add minimum 44×44px touch targets for all interactive elements (buttons, links, form controls)
    - Implement screen reader support with ARIA live regions for dynamic content
    - Add skip links for keyboard navigation and alternative text for all images
    - _Requirements: 5.1, 6.1-6.5, 14.1-14.5, 19.5_

  - [x] 7.2 Implement bilingual support and Livewire/Volt architecture

    - Set up Laravel localization system with language files for Bahasa Melayu (primary) and English (secondary)
    - Create WCAG-compliant language switcher with 44×44px touch targets and keyboard navigation
    - Translate all public-facing text, email templates, error messages, and system notifications
    - Implement Livewire 3 components for dynamic interactions:
      - Guest forms (helpdesk ticket, asset loan application)
      - Authenticated portal (dashboard, submission management)
      - Admin interfaces (ticket assignment, asset management)
    - Create Volt single-file components for simplified development (components with <100 lines of PHP logic)
    - Build real-time form validation using Livewire reactive properties with ARIA announcements
    - Implement optimized component performance:
      - wire:model.live.debounce.300ms for dynamic fields
      - wire:model.lazy for large text fields
      - #[Computed] properties for derived data
      - #[Lazy] loading for heavy sub-components
      - Eager loading with with() to prevent N+1 queries
    - Create shared component library accessible across all modules for reusability
    - Add bilingual content validation and testing tools
    - _Requirements: 5.4, 6.1, 14.4, 15.1-15.4, 21.4_

  - [x] 7.3 Implement session/cookie-only language switcher (NO user profile storage)

    - [x] 7.3.1 Backend - Create SetLocaleMiddleware for language detection

      - Create app/Http/Middleware/SetLocaleMiddleware.php with locale detection priority: 1. Session storage: session('locale') - highest priority (explicit user choice) 2. Cookie storage: $request->cookie('locale') - persistent preference (1 year) 3. Accept-Language header: parseAcceptLanguageHeader() - browser preference (first visit) 4. Config fallback: config('app.locale') - system default ('en' or 'ms')
      - Validate locale against supported_locales array ['en', 'ms'] before applying
      - Apply locale using App::setLocale($locale) for current request
      - Register middleware in bootstrap/app.php as global middleware (runs on every request)
      - Add supported_locales to config/app.php: 'supported_locales' => ['en', 'ms']
      - _Requirements: 20.1, 20.2, 7.2, 15.1_

    - [x] 7.3.2 Backend - Create LanguageController for locale switching

      - Create app/Http/Controllers/LanguageController.php with change(Request $request, string $locale) method
      - Validate locale parameter (must be 'en' or 'ms', reject invalid with abort(400))
      - Store locale in session: session(['locale' => $locale]) - immediate application
      - Store locale in cookie: Cookie::queue('locale', $locale, 60 _24_ 365) - 1 year persistence
      - Apply locale to current request: App::setLocale($locale)
      - Redirect back to previous page with success message: redirect()->back()->with('message', \_\_('Language changed successfully'))
      - Add route to routes/web.php: Route::get('/change-locale/locale', [LanguageController::class, 'change'])->where('locale', 'en|ms')->name('change-locale')
      - _Requirements: 20.3, 20.4, 15.2_

    - [x] 7.3.3 Frontend - Create WCAG 2.2 AA compliant language switcher Blade component

      - Create resources/views/components/accessibility/language-switcher.blade.php
      - Implement dropdown button pattern with ARIA menu semantics:
        - Button: aria-haspopup="menu", aria-expanded="true/false", aria-controls="language-menu"
        - Dropdown: role="menu", id="language-menu", hidden by default
        - Menu items: role="menuitem", lang="en"/"ms", aria-current="page" for active language
      - Use Alpine.js (included in Livewire 3) for dropdown toggle behavior:
        - x-data=" open: false " on wrapper
        - @click="open = !open" on button
        - @click.away="open = false" to close on outside click
        - @keydown.escape.window="open = false" for Escape key
      - Display current language with icon and label:
        - Desktop: "🌐 App::currentLocale() === 'en' ? 'English' : 'Bahasa Melayu' "
        - Mobile: "ðŸŒ App::currentLocale() === 'en' ? 'EN' : 'MS' " (abbreviated)
      - Dropdown menu items with native language names and flags:
        - English: link to `route('change-locale', 'en')` with `lang="en"` attribute and English flag/icon.
        - Bahasa Melayu: link to `route('change-locale', 'ms')` with `lang="ms"` attribute and Malay flag/icon.
      - Style with Tailwind CSS ensuring WCAG 2.2 AA compliance:
        - Button min-height: 44px (touch target per WCAG 2.5.5)
        - Active language highlight: bg-blue-600 text-white (6.8:1 contrast per D14)
        - Hover state: bg-gray-100 (4.5:1+ contrast)
        - Focus indicator: ring-2 ring-blue-600 ring-offset-2 (3px outline, 2px offset per D14 §9.2)
      - Implement keyboard navigation:
        - Tab: Focus button
        - Enter/Space: Toggle dropdown
        - Escape: Close dropdown
        - Arrow Down/Up: Navigate menu items (optional enhancement)
      - Responsive design: hidden sm:flex for desktop, sm:hidden for mobile variants
      - _Requirements: 20.5, 14.4, 6.1, 6.2, 6.3, 6.5, 15.3_

    - [x] 7.3.4 Frontend - Integrate language switcher into layouts

      - Add <x-accessibility.language-switcher /> to resources/views/components/layout/header.blade.php (guest layout)
        - Position: Top-right corner of navbar, after main navigation links
        - Wrapper class: ml-auto (pushes to right side)
      - Add <x-accessibility.language-switcher /> to resources/views/components/layout/auth-header.blade.php (authenticated layout)
        - Position: Top-right corner, before user dropdown menu
      - Optional: Add to footer.blade.php as secondary location for accessibility
      - Test visibility and positioning across all page types:
        - Guest pages (welcome, helpdesk form, asset loan form)
        - Authenticated pages (staff dashboard, ticket list, loan applications)
        - Admin panel (Filament pages)
      - Verify no layout shift or overflow on mobile (320px-414px), tablet (768px-1024px), desktop (1280px-1920px)
      - _Requirements: 20.5, 5.1, 14.5, 15.4_

    - [x] 7.3.5 Testing - Unit tests for SetLocaleMiddleware

      - Create tests/Unit/Middleware/SetLocaleMiddlewareTest.php with PHPUnit test cases:
        - testSessionLocaleHasHighestPriority() - session('locale') overrides cookie and header
        - testCookieLocaleTakesPriorityOverAcceptLanguage() - cookie('locale') overrides Accept-Language header
        - testAcceptLanguageHeaderParsedCorrectly() - parseAcceptLanguageHeader() returns valid locale
        - testFallbackToConfigDefault() - config('app.locale') used when no preference set
        - testInvalidLocaleRejected() - invalid locale (e.g., 'fr', 'de') not applied
        - testLocaleAppliedToAppFacade() - App::currentLocale() returns detected locale after middleware
      - Achieve 90%+ code coverage for SetLocaleMiddleware class
      - _Requirements: 14.3, 15.1, 15.2_

    - [x] 7.3.6 Testing - Feature tests for LanguageController

      - Create tests/Feature/LanguageControllerTest.php with feature test cases:
        - testChangeLocaleWithValidLocale() - GET /change-locale/en stores session and cookie, redirects back
        - testChangeLocaleWithInvalidLocale() - GET /change-locale/fr returns 400/404 error
        - testSessionStoredAfterLocaleChange() - session('locale') === 'ms' after change
        - testCookiePersistedAfterLocaleChange() - cookie('locale') exists with 1-year expiry
        - testRedirectBackToPreviousPage() - redirect preserves referer URL
        - testSuccessMessageDisplayed() - session('message') contains success message
      - Test both guest and authenticated user scenarios
      - Achieve 90%+ code coverage for LanguageController class
      - _Requirements: 14.3, 15.2, 15.3_

    - [x] 7.3.7 Testing - Accessibility validation (manual + automated)

      - Manual keyboard navigation testing:
        - Tab key focuses language switcher button
        - Enter/Space opens dropdown menu
        - Arrow keys navigate between menu items (if implemented)
        - Escape key closes dropdown
        - Tab moves focus to next element after closing
      - Screen reader testing with NVDA/JAWS:
        - Button announces current language and "has popup menu"
        - Menu items announce language with correct pronunciation (lang attribute)
        - Active language announces "current page"
      - Color contrast validation using WebAIM Contrast Checker:
        - Active language: #0056b3 on white = 6.8:1 (✓ WCAG AAA)
        - Hover state: verify 4.5:1 minimum (✓ WCAG AA)
        - Focus indicator: verify 3:1 against background (✓ WCAG AA)
      - Touch target size measurement:
        - Button height >= 44px on mobile (✓ WCAG 2.5.5)
        - Menu items height >= 44px on mobile (✓ WCAG 2.5.5)
      - Automated ARIA validation using axe-core DevTools extension:
        - No critical or serious accessibility violations
        - Valid ARIA attributes (aria-haspopup, aria-expanded, aria-current)
        - Proper role assignments (menu, menuitem)
      - Cross-browser testing:
        - Chrome 90+ (desktop and mobile)
        - Firefox 88+ (desktop)
        - Safari 14+ (desktop and iOS)
        - Edge 90+ (desktop)
      - Responsive design testing (viewport sizes):
        - Mobile: 320px, 375px, 414px (portrait)
        - Tablet: 768px, 1024px (landscape)
        - Desktop: 1280px, 1366px, 1920px
      - Document accessibility test results in docs/testing/language-switcher-accessibility-report.md
      - _Requirements: 6.1, 6.2, 6.3, 6.4, 6.5, 14.4, 14.5_

    - [x] 7.3.8 Documentation - Update D12, D14, and component library documentation

      - Add language switcher to D12 UI/UX Design Guide §7.4 (Component Library)
        - Include component description, WCAG compliance level (AA), usage examples
        - Document keyboard navigation behavior and ARIA semantics
        - Provide integration guidelines for new pages
      - Update D14 UI/UX Style Guide §9.5 (Component-Specific Accessibility) table
        - Add row: Language Switcher | ARIA menu button, lang attributes, 44px targets | Keyboard nav (Tab/Enter/Escape), screen reader support
      - Create component header metadata in language-switcher.blade.php:
        - Component name: Language Switcher
        - Description: WCAG 2.2 AA compliant bilingual language selector with session/cookie persistence
        - Requirements: 20.1-20.5, 7.2, 7.3, 14.4, 15.1-15.4
        - WCAG Level: AA (Perceivable 1.4.3, Operable 2.1.1, 2.4.7, 2.5.5, Understandable 3.1.2)
        - Version: 1.0.0
        - Author: Pasukan BPM MOTAC
        - Last Updated: 2025-11-02
      - Document usage examples in component docblock with code snippets
      - Add to component inventory in docs/reference/frontend/component-library.md
      - _Requirements: 17.1, 17.2, 17.3, 17.4, 17.5, 18.4_

  - [x] 7.4 Implement hybrid forms supporting both guest and authenticated access

    - Maintain dual layouts:
      - guest.blade.php for public forms (no authentication required)
      - app.blade.php for authenticated portal (login required)
    - Implement conditional field display based on authentication status using @auth/@guest directives
    - Support both guest fields (manual entry: guest_name, guest_email, guest_phone) and authenticated fields (pre-filled from user profile)
    - Ensure all form fields have:
      - Associated labels with proper for attributes
      - Error messages linked via aria-describedby
      - aria-invalid="true" for fields with validation errors
      - aria-required="true" for required fields
    - Implement Livewire optimization:
      - wire:model.live.debounce.300ms for dynamic fields (category selection, asset search)
      - wire:model.lazy for large text fields (description, purpose)
      - #[Computed] properties for derived data (available assets, approval matrix)
      - Eager-loading with with() to prevent N+1 queries
      - #[Lazy] loading for heavy sub-components (asset calendar, file upload)
    - Add real-time validation with clear error messaging and ARIA announcements
    - _Requirements: 11.5, 21.1-21.5, 22.1, 22.3, 23.1_

  - [x] 7.5 Create public-facing guest forms and pages
    - Build helpdesk ticket submission form with:
      - Multi-step wizard (contact info → issue details → attachments → confirmation)
      - Real-time validation with ARIA announcements
      - File attachment support with drag-and-drop and accessibility
      - Automatic ticket number generation and confirmation email
    - Create asset loan application form with:
      - Multi-step wizard (applicant info → asset selection → loan period → confirmation)
      - Asset availability calendar with keyboard navigation
      - Approval matrix display based on grade and asset value
      - Confirmation email with approval tracking link
    - Implement responsive landing pages and service information pages with MOTAC branding
    - Add confirmation pages with ticket/application numbers and next steps
    - Integrate email notification system as primary communication method
    - Ensure guest-only workflows with no authentication required
    - _Requirements: 1.1, 1.2, 1.4, 11.1-11.7, 21.5_

- [x] 8. Frontend Component Compliance and Metadata

  - [x] 8.1 Audit existing frontend components against D00-D15 standards

    - Systematically review all components:
      - Blade templates (layouts, components, partials)
      - Livewire components (guest forms, authenticated portal, admin interfaces)
      - Volt components (single-file components)
      - Email templates (notifications, approvals, confirmations)
      - Error pages (404, 500, 403, etc.)
      - Filament admin interfaces (resources, widgets, dashboards)
    - Generate comprehensive compliance report identifying gaps in:
      - Accessibility (WCAG 2.2 Level AA compliance, color contrast, keyboard navigation, ARIA attributes)
      - Documentation (component metadata, usage examples, integration guidelines)
      - Branding (MOTAC visual identity, compliant color palette, bilingual support)
      - Performance (Core Web Vitals, image optimization, asset optimization)
      - Metadata (standardized headers, requirements traceability, version history)
      - Requirements traceability (links to D03 requirements and D04 design specifications)
    - Categorize findings by severity (critical, high, medium, low) and compliance area
    - Use existing compliant components as reference standards for compliance validation
    - Document specific violations and provide corrective actions for non-compliant components
    - _Requirements: 16.1-16.5_

  - [x] 8.2 Implement standardized component metadata and traceability

    - Add standardized header comments to all components:
      - Component name and description
      - Author and creation date
      - Trace references to D00-D15 standards (D03 requirements, D04 design, D10 documentation, D12 UI/UX)
      - Timestamps (created_at, updated_at)
      - Version history and change log
    - Link each component to specific D03 software requirements (e.g., Requirements 1.1, 11.1, 22.2)
    - Link each component to D04 design specifications (e.g., Section 6.1 Frontend Component Architecture)
    - Document accessibility features:
      - WCAG 2.2 compliance level (Level AA)
      - Keyboard navigation support
      - Screen reader compatibility
      - ARIA attributes and landmarks
    - Document supported browsers (Chrome 90+, Firefox 88+, Safari 14+, Edge 90+)
    - Include usage examples and integration guidelines per D10 documentation standards
    - Maintain version history and change tracking per D11 specifications for component updates
    - _Requirements: 17.1-17.5_

  - [x] 8.3 Upgrade email templates, error pages, and admin interface compliance
    - Upgrade all email templates:
      - Ensure WCAG 2.2 Level AA accessibility with proper semantic HTML
      - Use compliant color palette (Primary #0056b3, Success #198754, Warning #ff8c00, Danger #b50c0c)
      - Implement MOTAC branding per D14 standards
      - Add bilingual support (Bahasa Melayu primary, English secondary) per D15 standards
      - Include proper metadata and documentation per D10 standards
    - Upgrade all error pages:
      - Provide accessible, helpful, and branded user experiences
      - Use proper ARIA attributes and semantic HTML5 structure
      - Include clear error messages and next steps
      - Add contact information and support links
      - Ensure bilingual support and MOTAC branding
    - Upgrade Filament admin interfaces:
      - Ensure WCAG 2.2 Level AA compliance with compliant colors
      - Implement MOTAC branding and bilingual support
      - Add proper documentation and metadata
      - Include accessibility features (keyboard navigation, ARIA attributes)
    - Include proper metadata, documentation, and testing procedures per D10 and D11 standards
    - Add Core Web Vitals performance monitoring for all interfaces
    - _Requirements: 18.1-18.5_

- [x] 9. Frontend Component Development and Integration

  - [x] 9.1 Build helpdesk module frontend components

    - Create guest helpdesk ticket submission form:
      - Multi-step wizard (contact info → issue details → attachments → confirmation)
      - Real-time validation with ARIA announcements
      - Dynamic category selection with asset linking
      - File attachment support with drag-and-drop and accessibility
      - Automatic ticket number generation (`HD[YYYY][000001-999999]`)
    - Build ticket status tracking and confirmation pages:
      - Confirmation page with ticket number and next steps
      - Status tracking page accessible via email link (no login required)
      - Email notifications for status changes
    - Implement responsive ticket list and detail views for admin:
      - Ticket queue with filtering and sorting
      - Ticket detail view with assignment, comments, and attachments
      - SLA tracking and escalation indicators
    - Add real-time form validation and user feedback with Livewire
    - _Requirements: 1.1, 1.2, 11.1-11.7, 13.1-13.5, 14.1, 15.1_

  - [x] 9.2 Build asset loan module frontend components

    - Create guest asset loan application form:
      - Multi-step wizard (applicant info → asset selection → loan period → confirmation)
      - Asset availability calendar with keyboard navigation
      - Approval matrix display based on grade and asset value
      - Confirmation email with approval tracking link
    - Build asset availability calendar and booking interface:
      - Visual calendar showing asset availability
      - Asset search and filtering by category, specifications
      - Booking interface with date range selection
    - Implement loan status tracking and confirmation pages:
      - Confirmation page with application number and approval timeline
      - Status tracking page accessible via email link (no login required)
      - Email notifications for approval decisions
    - Add asset catalog browsing and search functionality:
      - Asset catalog with categories and specifications
      - Search and filtering by name, category, availability
      - Asset detail pages with specifications and availability
    - _Requirements: 1.4, 1.5, 12.1-12.5, 14.1, 15.1_

  - [x] 9.3 Implement cross-module integration and consistency

    - Create unified navigation and header components:
      - Guest header with MOTAC branding and language switcher
      - Authenticated header with user menu and notifications
      - Admin header with role-based navigation
    - Build integrated dashboard showing both helpdesk and asset loan data:
      - Admin dashboard with combined metrics (ticket volume, SLA compliance, asset utilization, overdue items)
      - Authenticated staff dashboard with personal tickets, loans, and pending approvals
    - Implement consistent styling and branding across all modules:
      - Unified component library with compliant color palette
      - Consistent typography, spacing, and layout
      - MOTAC branding and bilingual support
    - Add cross-module search and linking capabilities:
      - Unified search across tickets and loans
      - Asset-ticket linking for hardware-related issues
      - Cross-module reporting and analytics
    - _Requirements: 2.1, 2.2, 2.5, 14.1, 14.5_

  - [x] 9.4 Build admin panel frontend enhancements

    - Create Filament custom pages for unified reporting:
      - Combined reports from helpdesk and asset loan modules
      - Report templates for common analytics (SLA compliance, asset utilization, approval times)
      - Data export functionality (CSV, PDF, Excel)
    - Build admin dashboard widgets for both modules:
      - Ticket volume and resolution time widgets
      - Asset utilization and overdue items widgets
      - SLA compliance and approval time widgets
      - Real-time updates with 300-second refresh
    - Implement admin notification center and task management:
      - Notification center for ticket assignments, SLA breaches, overdue assets
      - Task management for pending approvals, escalations, maintenance tickets
    - Add bulk operations and advanced filtering for admin users:
      - Bulk ticket assignment and status updates
      - Bulk asset operations (check-out, check-in, maintenance)
      - Advanced filtering by date range, status, category, division
    - _Requirements: 3.1, 3.4, 3.5, 3.6, 8.1, 8.2, 8.5_

  - [x] 9.5 Implement authenticated portal features
    - Create Staff Dashboard:
      - Quick stats (open tickets, active loans, pending approvals, resolved this month)
      - Recent submissions (latest tickets and applications)
      - Quick actions (submit new ticket/application, view all submissions, manage profile)
    - Build User Profile pages:
      - Contact information management (name, email, phone, staff ID)
      - Organizational data (division, grade) with admin approval for changes
      - Language preference (stored in user profile for cross-device consistency)
      - Notification settings (email preferences)
    - Create authenticated navigation components:
      - Sidebar navigation with role-based menu items
      - User menu with profile, settings, logout
      - Breadcrumbs for navigation context
    - Implement dual approval workflows:
      - Email-based approval (no login): secure token-based links with 7-day validity
      - Portal-based approval (with login): approval interface in authenticated portal
      - Approval decision tracking (approval_method: email/portal, approval_remarks)
    - Build submission history and management:
      - View all tickets (own submissions) with filtering and search
      - View all loan applications (own submissions) with status tracking
      - Add internal comments and upload additional attachments
      - Track resolution progress and approval status
    - Implement guest submission claiming:
      - Claim guest submissions by verifying email ownership
      - Link historical guest tickets/applications to authenticated account
    - Add in-app notifications for authenticated users:
      - Notification center for status updates, approvals, comments
      - Real-time notifications using Livewire polling
    - Maintain consistent WCAG 2.2 Level AA compliance, MOTAC branding, and bilingual support across authenticated portal
    - _Requirements: 1.2, 1.3, 1.6, 11.7, 22.1-22.7, 23.2, 23.3_

## Phase 4: Integration & Workflows

- [x] 10. Email-Based Workflow and External System Integration

  - [x] 10.1 Implement comprehensive email notification system

    - Configure SMTP settings and email gateway integration:
      - SMTP server configuration for MOTAC email gateway
      - Email authentication and security settings (TLS 1.3+)
      - Email rate limiting and throttling
    - Create responsive email templates with MOTAC branding:
      - Ticket confirmation emails (guest and authenticated)
      - Loan application confirmation emails (guest and authenticated)
      - Approval request emails for Grade 41+ officers (with dual approval options)
      - Approval decision emails (approved/declined)
      - Status update emails (ticket assignment, resolution, loan approval)
      - Reminder emails (overdue assets: 48 hours before, on due date, daily for overdue)
      - SLA breach alerts for admin users
    - Implement queue-based email sending using Laravel Queue with Redis:
      - Queue jobs for all email notifications
      - Retry mechanism (3 attempts with exponential backoff)
      - Failed job handling and logging
      - Queue monitoring and performance tracking
    - Send automated email notifications within 60 seconds for all status changes
    - Add email tracking and delivery confirmation
    - Implement bilingual email templates (Bahasa Melayu primary, English secondary)
    - Ensure WCAG 2.2 Level AA compliance for email templates
    - _Requirements: 1.2, 1.4, 10.1, 10.2, 10.4, 12.1, 12.3, 12.4, 12.5, 18.1, 18.2_

  - [x] 10.2 Develop dual approval system (email-based AND portal-based)
    - Create DualApprovalService for managing approval workflows:
      - sendApprovalRequest(): Generate approval token, determine approver, send email with dual options
      - processEmailApproval(): Handle email-based approval (no login required)
      - processPortalApproval(): Handle portal-based approval (login required)
      - sendApprovalNotifications(): Send confirmation emails to applicant and approver
      - logApprovalDecision(): Create audit trail for approval decisions
    - Implement approval matrix logic based on grade and asset value
    - Build email templates for approval requests and decisions
    - Create portal-based approval interface for authenticated approvers
    - _Requirements: 1.4, 1.5, 1.6, 10.1, 10.2, 12.1-12.5, 23.2_

## Phase 4.5: Critical Missing Components

- [x] 10.5 Build Missing Filament Admin Resources

  - [x] 10.5.1 Create core Filament resources
    - Create HelpdeskTicketResource with form, table, and pages (List, Create, Edit, View)
    - Create LoanApplicationResource with form, table, and pages
    - Create AssetResource with form, table, and pages
    - Create AssetCategoryResource with form, table, and pages
    - Create DivisionResource with form, table, and pages
    - Create GradeResource with form, table, and pages
    - Create TicketCategoryResource with form, table, and pages
    - _Requirements: 3.1, 3.2, 3.3, 3.4_

  - [x] 10.5.2 Implement Filament widgets and dashboards
    - Create unified admin dashboard combining helpdesk and asset loan metrics
    - Build ticket statistics widget (volume, resolution time, SLA compliance)
    - Create asset utilization widget (utilization rate, overdue items)
    - Implement approval queue widget for approvers
    - Add real-time updates with 300-second refresh
    - _Requirements: 3.4, 3.5, 8.1, 8.2_

  - [x] 10.5.3 Build Filament custom pages
    - Create unified reporting page combining both modules
    - Build analytics page with charts and visualizations
    - Implement data export page (CSV, PDF, Excel)
    - Create system settings page for superuser
    - _Requirements: 3.6, 8.2, 8.5_

- [x] 10.6 Build Missing Livewire Components

  - [x] 10.6.1 Create guest tracking components
    - Build TrackTicket component for guest ticket tracking (app/Livewire/Helpdesk/TrackTicket.php)
    - Create GuestLoanTracking component for guest loan tracking (app/Livewire/GuestLoanTracking.php)
    - Implement status display with timeline visualization
    - Add email-based access (no authentication required)
    - _Requirements: 1.2, 1.4, 11.6, 21.5_

  - [x] 10.6.2 Create authenticated helpdesk components
    - Build Helpdesk Dashboard component (app/Livewire/Helpdesk/Dashboard.php)
    - Create MyTickets component for viewing all user tickets
    - Implement TicketDetails component with comments and attachments
    - Add guest submission claiming functionality
    - _Requirements: 22.2, 22.3, 22.6, 23.3_

  - [x] 10.6.3 Create authenticated loan components
    - Build AuthenticatedLoanDashboard component (app/Livewire/AuthenticatedLoanDashboard.php)
    - Create LoanHistory component for viewing all user applications
    - Implement LoanDetails component with status tracking
    - Build LoanExtension component for extension requests
    - Add guest submission claiming functionality
    - _Requirements: 22.2, 22.3, 22.6, 23.3_

- [x] 10.7 Build Cross-Module Integration Services

  - [x] 10.7.1 Create CrossModuleIntegrationService
    - Implement automatic ticket creation for damaged returned assets
    - Build asset-ticket linking for hardware-related issues
    - Create unified search across both modules
    - Add cross-module reporting combining data from helpdesk and asset loan
    - _Requirements: 2.1, 2.2, 2.3, 2.5_

  - [x] 10.7.2 Implement notification services
    - Create TicketNotificationService for helpdesk notifications
    - Build LoanNotificationService for loan notifications
    - Implement SLANotificationService for SLA breach alerts
    - Create ReminderNotificationService for overdue assets
    - _Requirements: 10.1, 10.3, 10.4_

## Phase 5: Performance, Security & Quality Assurance

- [x] 11. Performance Optimization

  - [x] 11.1 Implement comprehensive performance optimization

    - Achieve Core Web Vitals targets: LCP <2.5s, FID <100ms, CLS <0.1, TTFB <600ms
    - Achieve Lighthouse scores: Performance 90+, Accessibility 100, Best Practices 100, SEO 100
    - Implement image optimization: WebP format with JPEG fallbacks, explicit dimensions, fetchpriority attributes
    - Configure Vite optimization: Gzip/Brotli compression, code splitting with manual chunks, Terser minification with drop_console
    - Refactor all inline style attributes and style blocks into external CSS files imported via Vite
    - Implement lazy loading for images and heavy components
    - Add caching strategies for Livewire components
    - Create performance monitoring and Core Web Vitals tracking
    - _Requirements: 19.1, 19.2, 19.3, 19.4, 19.5, 21.3_

  - [x] 11.2 Implement Redis caching strategy

    - Set up Redis for session storage and application caching
    - Implement query result caching for frequently accessed data
    - Create cache invalidation strategies for data updates
    - _Requirements: 7.2, 8.3_

  - [x] 11.3 Optimize database performance

    - Add proper database indexes for query optimization
    - Implement database query optimization and eager loading
    - Set up database connection pooling and read replicas
    - _Requirements: 7.1, 7.5, 8.3_

  - [x] 11.4 Implement background job processing
    - Set up Laravel Queue workers for background tasks
    - Implement job retry mechanisms and failure handling
    - Create monitoring for queue performance and failures
    - _Requirements: 10.2, 10.3_

- [x] 12. Security Implementation

  - [x] 12.1 Implement comprehensive security measures

    - Enable CSRF protection on all forms:
      - Verify CSRF tokens on all POST/PUT/DELETE requests
      - Include @csrf directive in all Blade forms
      - Configure CSRF token refresh for long-lived sessions
    - Implement rate limiting:
      - Guest forms: 60 requests per minute per IP
      - API endpoints: 100 requests per minute per user
      - Email-based approval links: 10 requests per hour per token
    - Add input validation and sanitization:
      - Server-side validation for all form inputs
      - XSS protection using Laravel's built-in escaping
      - SQL injection prevention using Eloquent ORM and parameterized queries
    - Set up secure headers and HTTPS enforcement:
      - HSTS (HTTP Strict Transport Security) headers
      - X-Frame-Options, X-Content-Type-Options, X-XSS-Protection headers
      - Content Security Policy (CSP) headers
      - Force HTTPS redirection in production
    - Implement invisible reCAPTCHA for guest forms
    - Add file upload security (virus scanning, file type validation, size limits)
    - _Requirements: 9.1, 9.3, 9.4, 21.1_

  - [x] 12.2 Set up data encryption and protection
    - Implement database encryption for sensitive data:
      - Encrypt approval tokens using AES-256
      - Encrypt personal data (email, phone) at rest
      - Use Laravel's encrypted casting for sensitive model attributes
    - Set up secure file storage with access controls:
      - Store attachments in private storage (not publicly accessible)
      - Generate signed URLs for file downloads with expiration
      - Implement file access logging and audit trail
    - Create data retention and deletion policies:
      - 7-year retention for tickets, loans, and audit logs
      - 12-month retention for queue logs and temporary files
      - Automated data deletion after retention period
      - PDPA-compliant data subject rights (access, correction, deletion)
    - Implement secure session management with Redis backend
    - Add password hashing with bcrypt for authenticated users
    - _Requirements: 5.2, 9.4, 9.5_

- [x] 13. Testing Implementation

  - [x] 13.1 Create comprehensive test suite

    - Write unit tests for all business logic and models:
      - User model (role methods, canApprove(), relationships)
      - HelpdeskTicket model (isGuestSubmission(), getSubmitterNameAttribute(), generateTicketNumber())
      - LoanApplication model (generateApprovalToken(), isTokenValid(), approval workflows)
      - DualApprovalService (sendApprovalRequest(), processEmailApproval(), processPortalApproval())
      - Notification services, audit logging, caching strategies
    - Implement feature tests for all user workflows:
      - Guest ticket submission (form validation, email confirmation, ticket creation)
      - Guest loan application (form validation, approval request, email confirmation)
      - Authenticated portal (login, dashboard, submission history, profile management)
      - Email-based approval (token validation, approval decision, notifications)
      - Portal-based approval (authentication, approval interface, decision tracking)
      - Guest submission claiming (email verification, account linking)
    - Create integration tests for external system connections:
      - SMTP email gateway integration
      - Optional HRMIS API integration
      - Redis cache and session management
      - File storage and signed URL generation
    - Write Livewire component tests for all interactive components:
      - Guest forms (ticket submission, loan application)
      - Authenticated portal (dashboard, submission management)
      - Admin interfaces (ticket assignment, asset management)
    - Create Volt component testing for single-file components
    - Implement form validation testing:
      - Required field validation
      - Email format validation
      - Date range validation
      - File upload validation
    - Build component integration testing:
      - Cross-module integration (asset-ticket linking, unified dashboard)
      - Email notification integration
      - Audit trail integration
    - _Requirements: All requirements need test coverage, 14.3, 15.1-15.4_

  - [x] 13.2 Set up automated testing pipeline
    - Configure PHPUnit 11 for automated test execution:
      - Set up phpunit.xml configuration
      - Configure test database (SQLite in-memory for speed)
      - Set up test environment variables
    - Set up code coverage reporting and quality gates:
      - Minimum 80% overall coverage
      - Minimum 95% coverage for critical paths (guest submissions, approvals)
      - Code coverage reports in HTML and Clover formats
    - Implement continuous integration testing workflows:
      - GitHub Actions or GitLab CI pipeline
      - Run tests on every commit and pull request
      - Automated deployment on successful tests
    - Create Laravel Dusk tests for complete user workflows:
      - End-to-end guest ticket submission
      - End-to-end guest loan application with email approval
      - End-to-end authenticated portal workflows
    - Implement cross-browser testing:
      - Chrome 90+ (primary browser)
      - Firefox 88+
      - Safari 14+
      - Edge 90+ (Chromium)
    - Build mobile device testing for responsive design:
      - Mobile viewports (320px-414px)
      - Tablet viewports (768px-1024px)
      - Desktop viewports (1280px-1920px)
    - Create accessibility testing with automated tools:
      - Lighthouse accessibility audits (target: 100 score)
      - axe DevTools automated scanning
      - WAVE accessibility evaluation
      - Manual keyboard navigation testing
      - Manual screen reader testing (NVDA, JAWS, VoiceOver)
    - _Requirements: Quality assurance for all features, 6.1-6.5, 23.5_

## Phase 6: Compliance & Standards

- [x] 14. D00-D15 Compliance Assessment & Component Standardization

  - [x] 14.1 Create component inventory and compliance checker

    - Build ComponentInventoryService to scan and catalog all Blade templates, Livewire/Volt components, email templates, error pages, and Filament admin interfaces
    - Create StandardsComplianceChecker service with D03-D15 rule sets
    - Implement automated accessibility scanning (WCAG 2.2 Level AA)
    - Build documentation coverage analysge and application caching:
      - Configure Redis connection in config/database.php
      - Set session driver to redis in config/session.php
      - Configure cache driver to redis in config/cache.php
    - Implement query result caching for frequently accessed data:
      - Cache divisions, grades, categories, asset categories (TTL: 3600 seconds)
      - Cache asset availability calendar (TTL: 300 seconds)
      - Cache dashboard metrics (TTL: 300 seconds)
    - Create cache invalidation strategies for data updates:
      - Invalidate cache on model create/update/delete using observers
      - Tag-based cache invalidation for related data
      - Cache warming for critical data after invalidation
    - Implement cache monitoring and performance tracking
    - _Requirements: 7.2, 8.3_

  - [x] 11.3 Optimize database performance

    - Add proper database indexes for query optimization:
      - Foreign key indexes (user_id, division_id, grade_id, category_id, asset_id)
      - Frequently queried columns (ticket_number, application_number, status, email)
      - Composite indexes for common query patterns (status + created_at, user_id + status)
    - Implement database query optimization and eager loading:
      - Use with() for eager loading relationships to prevent N+1 queries
      - Use #[Computed] properties in Livewire for derived data
      - Optimize complex queries with query builder and raw SQL where needed
    - Set up database connection pooling and read replicas:
      - Configure connection pooling for MySQL
      - Set up read replicas for reporting and analytics queries
      - Implement read/write splitting for high-traffic scenarios
    - Add database query monitoring and slow query logging
    - _Requirements: 7.1, 7.5, 8.3_

  - [x] 11.4 Implement background job processing

    - Set up Laravel Queue workers with Redis driver:
      - Configure queue connection in config/queue.php
      - Create queue workers for different job types (emails, reports, notifications)
      - Set up supervisor for queue worker management
    - Implement job retry mechanisms and failure handling:
      - Configure retry attempts (3 attempts with exponential backoff)
      - Implement failed job handling and logging
      - Create failed job dashboard in Filament admin
    - Create monitoring for queue performance and failures:
      - Queue metrics (jobs processed, failed jobs, average processing time)
      - Queue health checks and alerts
      - Queue worker monitoring and auto-restart on failure
    - \_Requirements: 10.1, 10.2, 10.3_is tools including metadata validation
    - Create performance baseline measurement system with Core Web Vitals
    - Add bilingual support detection and validation
    - Implement requirements traceability validation
    - Generate comprehensive component registry with metadata and compliance status
    - _Requirements: 16.1, 16.2, 16.3, 17.1, 17.2, 17.3, 18.1_

  - [x] 14.2 Implement gap analysis and compliance reporting

    - Implement compliance scoring and prioritization algorithms
    - Create detailed gap analysis reports with remediation recommendations
    - Build severity classification system (critical, high, medium, low)
    - Generate component-specific compliance roadmaps
    - Build web-based compliance dashboard with real-time metrics
    - Implement compliance trend tracking and historical analysis
    - Create exportable compliance reports for stakeholders
    - Add automated compliance report generation and distribution
    - _Requirements: 16.2, 16.3, 16.4_

  - [x] 14.3 Upgrade email templates, error pages, and admin interface compliance

    - Audit and upgrade all email templates for D00-D15 compliance with WCAG 2.2 Level AA accessibility
    - Audit and upgrade all error pages for accessible, helpful, and branded experiences
    - Audit and upgrade Filament admin interface components for D00-D15 compliance
    - Add MOTAC branding and bilingual support to all templates and interfaces
    - Implement standardized metadata headers for all components
    - Add requirements traceability links to D03 and D04 specifications
    - Create automated metadata validation and generation tools
    - Implement version history and change tracking for components
    - _Requirements: 18.1, 18.2, 18.3, 18.4, 18.5, 17.1, 17.2, 17.3, 17.4, 17.5_

  - [x] 14.4 Implement compliance and standards validation
    - Create D00-D15 standards compliance checker
    - Build component metadata and documentation system
    - Implement automated accessibility auditing
    - Add code quality and formatting validation
    - Create UAT test scenarios for all user roles
    - Implement testing documentation and checklists
    - Build feedback collection and issue tracking
    - _Requirements: 14.3, 5.1, 5.2, 16.4, 16.5, 17.4, 17.5_

- [x] 15. Final Hybrid Architecture Integration
  - [-] 15.1 Review and update documentation
    - Review all associated documents in docs folder and update for consistency with hybrid architecture and four-role RBAC
    - Ensure browser compatibility: Chrome 90+, Firefox 88+, Safari 14+, Edge 90+ with automated testing
    - Implement comprehensive cross-browser validation and compatibility testing
    - Create final compliance verification and validation procedures for hybrid architecture
    - Generate comprehensive implementation completion report with before/after metrics
    - _Requirements: 22.4, 22.5, 23.1, 23.2, 23.3, 23.4, 23.5, 23.6, 18.4, 18.5_

## Phase 7: Monitoring, Documentation & Deployment

- [x] 16. Monitoring and Analytics

  - [x] 16.1 Implement system monitoring

    - Set up application performance monitoring
    - Create health check endpoints for system status
    - Implement error tracking and alerting
    - Build real-time compliance monitoring dashboard
    - Create automated compliance violation detection and alerting
    - Implement performance monitoring and degradation alerts
    - Build accessibility monitoring for production environments
    - Create compliance reporting and audit trail systems
    - _Requirements: 8.3, 8.4, 16.2, 17.2, 18.2_

  - [x] 16.2 Build analytics and reporting dashboard
    - Create executive dashboard with key metrics
    - Implement usage analytics and user behavior tracking
    - Build automated report generation and scheduling
    - _Requirements: 8.1, 8.2, 8.5_

- [ ]* 17. Documentation and Training Materials

  - [ ]* 17.1 Create user documentation

    - Write user manuals for both helpdesk and asset loan modules
    - Create video tutorials for common workflows
    - Build in-system help and tooltips
    - _Requirements: User adoption and training_

  - [ ]* 17.2 Create technical documentation
    - Document API endpoints and integration guides
    - Create deployment and maintenance procedures
    - Write troubleshooting guides for common issues
    - _Requirements: System maintenance and support_

- [ ]* 18. Deployment and Production Setup
  **Note:** This task requires manual execution outside the coding environment (infrastructure provisioning, server configuration, user testing, and training activities).

  - [ ]* 18.1 Prepare production environment

    - Set up production servers with proper security configurations
    - Configure database replication and backup procedures
    - Implement SSL certificates and security hardening
    - Optimize server configuration for public-facing forms
    - Configure CDN integration for static assets
    - _Requirements: 7.4, 9.4, 9.5_

  - [ ]* 18.2 Implement deployment pipeline

    - Create automated deployment scripts and procedures
    - Set up staging environment for pre-production testing
    - Implement rollback procedures for failed deployments
    - Add frontend asset optimization and CDN configuration
    - Create blue-green deployment for zero-downtime upgrades
    - _Requirements: System reliability and maintenance_

  - [ ]* 18.3 Conduct user acceptance testing and training
    - Organize UAT sessions with actual MOTAC staff using public forms
    - Test email-based approval workflows with Grade 41+ officers
    - Provide admin user training for Filament admin panel
    - Create user guides and support documentation
    - Conduct accessibility testing with users who have disabilities
    - _Requirements: User adoption and system success_
