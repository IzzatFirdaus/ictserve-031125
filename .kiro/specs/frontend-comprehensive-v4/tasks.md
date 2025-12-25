# ICTServe Frontend Comprehensive v4.0 - Implementation Tasks

**Sistem ICTServe**  
**Versi:** 4.0.1 (SemVer)  
**Tarikh Kemaskini:** 25 Disember 2025  
**Status:** Aktif - PKS Compliance Migration Phase  
**Klasifikasi:** Terhad - Dalaman BPM MOTAC  

---

## Document Information

| Attribute | Value |
|-----------|-------|
| **Version** | 4.0.1 |
| **Last Updated** | 25 December 2025 |
| **Status** | Active - PKS Compliance Migration with SSO Mandatory Architecture |
| **Classification** | Restricted - Internal BPM MOTAC |
| **Dependencies** | requirements.md v4.0.0, design.md v4.0.0 |
| **PKS References** | Seksyen 5.2.1, 9.2.1, 4.2, 5.4.3, 10.1, 10.2, 11.1, 12.1 |

> **PENTING - PKS 5.2.1 COMPLIANCE**: Fasa 19 menambah tugas untuk migrasi PKS Compliance termasuk penghapusan Guest Mode dan pelaksanaan SSO Mandatory Architecture.

---

## Implementation Plan Overview

This consolidated implementation plan combines all frontend development tasks across the **SSO Mandatory Architecture** (formerly True Hybrid Architecture), including Walk-in/Kiosk Mode with SSO, authenticated portal, Filament admin panel, and **Cloud Hybrid AI interfaces with DLP filtering (D18 v4.0)**. Each task builds incrementally and focuses on code implementation, testing, and integration.

**CRITICAL PKS 5.2.1 COMPLIANCE**: Guest Mode has been **eliminated**. All users must authenticate via SSO (LDAP/Active Directory) to ensure full accountability and non-repudiation.

**Consolidated Scope:**

- Foundation setup and core architecture
- Bahasa Melayu exclusive interface implementation
- Theme switcher system
- Unified component library development
- Figma MCP integration
- Livewire 3.7.3 and Volt 1.10.1 architecture
- Filament admin panel enhancements
- Authenticated portal development
- Real-time features and notifications
- Cross-module integration
- Performance optimization and accessibility compliance
- **Cloud Hybrid AI Chat Interface with DLP (D18 v4.0 - UPDATED)**
- **FAQ Bot Widget with Ollama RAG (authenticated users only)**
- **AI Admin Management Interface (D18 v4.0)**
- **PKS Compliance Migration (Phase 19 - NEW)**
  - SSO Mandatory Authentication (PKS 5.2.1)
  - Walk-in/Kiosk Mode with SSO
  - HRMIS Auto-Provisioning
  - DLP Filtering for Cloud AI (PKS 9.2.1)
  - Intranet-Only Deployment (PKS 4.2)
  - Password Policy Compliance (PKS 5.4.3)
  - Dual Audit System with 7-Year Retention
  - Incident Response Management (PKS 10.1)
  - Business Continuity and Disaster Recovery (PKS 10.2)
  - Third Party Access Management (PKS 11.1)
  - Security Awareness Training Compliance (PKS 12.1)
  - PSPM 2022-2026 Strategic Alignment (Teras 1-4)

---

## Phase 1: Foundation and Core Architecture

### 1.1 Foundation Setup and Core Architecture

- [x] 1.1.1 Set up Laravel 12.40.1 with modern stack ✓
  - Laravel 12.40.1 with PHP 8.2.12 configured ✓ (composer.json)
  - Livewire 3.7.0, Volt 1.10.1, Tailwind CSS 4.1.17, Alpine.js 3.x installed ✓
  - Filament 4.1.10 admin panel configured ✓ (app/Providers/Filament/)
  - Laravel Reverb 1.6.2 WebSocket server configured ✓ (config/reverb.php)
  - _Requirements: 6.1, 8.1, 10.1_

- [x] 1.1.2 Configure Tailwind CSS 4 with @theme directive ✓
  - @theme configuration in resources/css/app.css ✓ (line 33+)
  - MyDS Design System tokens integrated ✓
  - CSS custom properties for theme switching ✓ (light/dark mode)
  - Vite configuration optimized ✓ (vite.config.js)
  - _Requirements: 2.1, 4.1, 4.2_

- [x] 1.1.3 Create OptimizedLivewireComponent trait ✓
  - Caching mechanisms implemented ✓ (app/Traits/OptimizedLivewireComponent.php)
  - Lazy loading capabilities (lazyLoadInChunks, getCursorPaginatedResults) ✓
  - Query optimization patterns (getOptimizedQuery, applySelectOptimization) ✓
  - Performance monitoring hooks (enablePerformanceMonitoring, getPerformanceMetrics) ✓
  - Used by 15+ Livewire components across the application ✓
  - _Requirements: 6.1, 13.1, 13.2_

- [x] 1.1.4 Set up unified component library structure ✓
  - Directory structure created: accessibility/, data/, form/, layout/, navigation/, responsive/, ui/ ✓
  - Component metadata headers with D00-D17 traceability ✓
  - Consistent naming convention (x-category.component-name) ✓
  - Component documentation in docs/frontend/ ✓
  - _Requirements: 5.1, 5.2, 5.3_

### 1.2 MyDS Design System Integration

- [x] 1.2.1 Implement MyDS color token mapping ✓
  - Semantic color tokens (--bg-_, --txt-_, --otl-_, --fr-_) in @theme ✓
  - MOTAC compliant colors mapped (Primary #0056B3, Secondary #0B4D8F, etc.) ✓
  - Theme-aware color switching implemented ✓
  - Color contrast validation (WCAG 2.2 AA compliant) ✓
  - _Requirements: 4.1, 7.1, 7.4_

- [x] 1.2.2 Configure MyDS typography system ✓
  - Poppins font for headings (--font-heading) ✓
  - Inter font for body text (--font-body) ✓
  - Responsive typography scales (text-xs to text-4xl) ✓
  - Font loading optimization via Vite ✓
  - _Requirements: 4.2_

- [x] 1.2.3 Implement MyDS spacing and layout systems ✓
  - Spacing system (--space-1 to --space-16, 4px increments) ✓
  - Radius system (--radius-xs to --radius-full) ✓
  - Shadow system (--shadow-button, --shadow-card, --shadow-dropdown) ✓
  - Responsive grid system (MyDS 12-8-4 columns) ✓
  - _Requirements: 4.3, 4.4, 4.5_

- [ ]* 1.2.4 Write property test for MyDS compliance
  - **Property 1: MyDS Design System Compliance**
  - **Validates: Requirements 4.1, 4.2, 4.3, 4.4, 4.5**

---

## Phase 2: Bahasa Melayu Exclusive Interface

### 2.1 BM Interface Implementation

- [x] 2.1.1 Remove language switcher components ✓
  - Deleted `app/Livewire/LanguageSwitcher.php` component ✓
  - Deleted `resources/views/livewire/language-switcher.blade.php` view ✓
  - Removed commented language switcher references from navigation components ✓
  - Updated `LanguageController.php` to enforce BM-only ('ms') locale ✓
  - Updated `SetLocaleMiddleware.php` to enforce BM-only ('ms') locale ✓
  - Configuration already set to `['ms']` in `config/app.php` ✓
  - All 14 LanguageSwitcherTest tests pass ✓
  - _Requirements: 1.1, 1.2_

- [x] 2.1.2 Convert UI text to Bahasa Melayu exclusively
  - Update all labels, buttons, messages, and notifications to BM
  - Convert form validation messages to Bahasa Melayu
  - Update help text and tooltips to BM
  - Maintain English in comments for technical reference
  - _Requirements: 1.1, 1.3, 1.4_

- [x] 2.1.3 Convert email templates to BM-exclusive
  - Update all automated email templates to Bahasa Melayu
  - Convert notification emails to BM format
  - Update system announcement templates
  - Maintain email template consistency across modules
  - _Requirements: 1.5_

- [ ]* 2.1.4 Write property test for BM exclusive interface
  - **Property 2: Bahasa Melayu Exclusive Interface**
  - **Validates: Requirements 1.1, 1.2, 1.3, 1.4, 1.5**

---

## Phase 3: Theme Switcher System

### 3.1 Theme Switcher Implementation

- [x] 3.1.1 Create theme toggle component
  - Create theme-toggle.blade.php component with light/dark mode toggle
  - Implement ☀️/🌙 icons with smooth transitions
  - Add ARIA attributes for accessibility
  - Set up keyboard navigation support
  - _Requirements: 2.1, 2.5, 7.3_

- [x] 3.1.2 Implement theme persistence and FOUT prevention
  - Create theme-init-script.blade.php for FOUT prevention
  - Set up localStorage persistence with 'theme' key
  - Implement light mode as immutable default (no system preference detection)
  - Add theme initialization on page load
  - _Requirements: 2.1, 2.2, 2.3_

- [x] 3.1.3 Ensure WCAG compliance in both themes
  - Validate contrast ratios in light and dark themes
  - Test focus indicators in both themes
  - Ensure minimum 3:1 contrast for UI components
  - Verify 4.5:1 contrast for text elements
  - _Requirements: 2.4, 7.1, 7.2_

- [ ]* 3.1.4 Write property test for theme persistence
  - **Property 3: Theme Persistence and Accessibility**
  - **Validates: Requirements 2.1, 2.2, 2.3, 2.4**

---

## Phase 4: Unified Component Library

### 4.1 Accessibility Components

- [x] 4.1.1 Create accessibility foundation components
  - Implement skip-links.blade.php for keyboard navigation ✓
  - Create aria-live-region.blade.php for dynamic content announcements ✓
  - Build focus-trap.blade.php for modal accessibility ✓
  - Develop screen-reader-text.blade.php for hidden content ✓
  - _Requirements: 7.3, 7.4_

### 4.2 Form Components

- [x] 4.2.1 Build accessible form components
  - Create input.blade.php with proper ARIA attributes and validation ✓
  - Implement select.blade.php with keyboard navigation ✓
  - Build textarea.blade.php with character counting ✓
  - Create checkbox.blade.php and radio.blade.php with proper labeling ✓
  - Develop file-upload.blade.php with drag-and-drop support ✓
  - _Requirements: 5.4, 7.4, 7.5_

### 4.3 UI Components

- [x] 4.3.1 Create core UI components
  - Implement button.blade.php with multiple variants and states ✓
  - Create card.blade.php with consistent styling ✓
  - Build modal.blade.php with focus management ✓
  - Develop dropdown.blade.php with keyboard navigation ✓
  - Create alert.blade.php, badge.blade.php, toast.blade.php components ✓
  - Create loading.blade.php with spinner, dots, pulse, skeleton variants ✓
  - _Requirements: 5.4, 7.2, 7.5_

### 4.4 Layout and Navigation Components

- [x] 4.4.1 Build layout foundation components ✓
  - Create header.blade.php with responsive navigation ✓
  - Implement navbar.blade.php with role-based menu items ✓
  - Build sidebar.blade.php for admin interfaces ✓
  - Create footer.blade.php with consistent branding ✓
  - Develop container.blade.php with responsive grid ✓
  - Create responsive/grid.blade.php for MyDS 12-8-4 column system ✓
  - Create responsive/mobile-menu.blade.php for mobile navigation ✓
  - Create responsive/breakpoint-indicator.blade.php for development ✓
  - _Requirements: 5.4, 15.1, 15.2_

- [x] 4.4.2 Build portal layout system ✓
  - Create portal/layouts/app.blade.php with unified layout structure ✓
  - Implement portal/components/header.blade.php with MOTAC branding ✓
  - Build portal/components/navbar.blade.php with authenticated navigation ✓
  - Create portal/components/sidebar.blade.php with role-based menu ✓
  - Implement portal/components/footer.blade.php with government compliance ✓
  - Add portal/components/breadcrumb.blade.php for navigation context ✓
  - Create portal/components/accessibility-menu.blade.php for WCAG compliance ✓
  - Build portal/partials/flash-messages.blade.php for user feedback ✓
  - _Requirements: 5.4, 7.3, 9.1_

- [x] 4.4.3 Write feature tests for portal layout components ✓
  - Created PortalLayoutComponentsTest.php with core region validation ✓
  - Tests portal layout renders header, navbar, sidebar, footer, main content ✓
  - Tests data rights pages render with proper layout structure ✓
  - Tests breadcrumb integration and accessibility features ✓
  - **Validates: Requirements 5.4, 7.3, 9.1**

- [ ]* 4.4.4 Write property test for component accessibility
  - **Property 4: Component WCAG 2.2 AA Compliance**
  - **Validates: Requirements 7.1, 7.2, 7.3, 7.4, 7.5, 7.6**

---

## Phase 5: Figma MCP Integration

### 5.1 Figma MCP Setup

- [ ] 5.1.1 Configure Figma MCP tools
  - Set up get_design_context tool for component extraction
  - Configure create_design_system_rules for automated rule generation
  - Implement get_code_connect_map for design-code mapping
  - Create FigmaDesignContext model for data handling
  - _Requirements: 3.1, 3.2_

- [ ] 5.1.2 Implement design-to-code transformation
  - Create transformation pipeline from React/Tailwind to Livewire/Blade
  - Set up color mapping from Figma colors to Compliant_Color_Palette
  - Implement component structure conversion
  - Add design system rules storage in .kiro/steering/design-system.md
  - _Requirements: 3.3, 3.4, 3.5_

- [ ]* 5.1.3 Write property test for Figma design consistency
  - **Property 5: Figma Design Consistency**
  - **Validates: Requirements 3.1, 3.2, 3.3, 3.4**

---

## Phase 6: Livewire 3.7 and Volt 1.10 Architecture

### 6.1 Enhanced Livewire Components

- [x] 6.1.1 Implement OptimizedLivewireComponent trait enhancements
  - Add advanced caching mechanisms for computed properties ✓
  - Implement lazy loading for large datasets (lazyLoadInChunks, getCursorPaginatedResults) ✓
  - Set up query optimization patterns (getOptimizedQuery, applySelectOptimization) ✓
  - Create performance monitoring and debugging tools (enablePerformanceMonitoring, getPerformanceMetrics) ✓
  - _Requirements: 6.1, 13.1, 13.2_

- [x] 6.1.2 Create Volt components for interactive elements
  - Build Volt components for forms with real-time validation ✓
  - Create filter components with wire:model.live.debounce.300ms (search-filter.blade.php) ✓
  - Implement modal components with proper state management (confirm-modal.blade.php) ✓
  - Develop search components with instant results ✓
  - _Requirements: 6.2, 6.3_

- [x] 6.1.3 Implement computed properties and loading states
  - Use #[Computed] attribute for derived data calculations (cacheComputedProperty) ✓
  - Add proper loading states with skeleton loaders (getSkeletonLoader) ✓
  - Implement ARIA live regions for dynamic content updates (getAriaLiveAttributes) ✓
  - Create progress indicators for long-running operations (progress-indicator.blade.php) ✓
  - _Requirements: 6.4, 6.5, 7.4_

- [ ]* 6.1.4 Write property test for Livewire performance
  - **Property 6: Livewire Performance Optimization**
  - **Validates: Requirements 6.1, 6.2, 6.3, 6.4, 13.1**

---

## Phase 7: Filament Admin Panel Enhancement

### 7.1 Filament Panel Configuration

- [x] 7.1.1 Configure Filament panel with WCAG colors and RBAC ✓
  - Set up AdminPanelProvider with MOTAC compliant colors ✓ (Primary #0056b3, Success #198754, Warning #ff8c00, Danger #b50c0c)
  - Configure four-role RBAC (Staff, Approver, Admin, Superuser) ✓ (via AdminAccessMiddleware)
  - Implement navigation groups and branding ✓ (Operations, Management, System groups with MOTAC logo)
  - Set up database notifications and global search ✓ (30s polling, Cmd+K/Ctrl+K bindings)
  - _Requirements: 8.1, 8.2, 14.2_

### 7.2 Filament Resources Enhancement

- [x] 7.2.1 Enhance HelpdeskTicketResource
  - Implement comprehensive table with advanced filtering ✓ (existing)
  - Add assignment actions with SLA calculation ✓ (existing)
  - Create status transition workflows ✓ (existing)
  - Implement bulk operations with reporting ✓ (existing)
  - _Requirements: 8.3, 8.5, 11.1_

- [x] 7.2.2 Enhance LoanApplicationResource
  - Create loan management interface with calendar widget ✓
  - Implement issuance and return actions ✓
  - Add condition tracking and auto-maintenance ticket creation ✓
  - Build approval workflow integration ✓
  - Added ViewLoanApplication page and LoanApplicationInfolist schema ✓
  - Enhanced LoanApplicationsTable with overdue indicators, bulk approve, export ✓
  - _Requirements: 8.3, 8.5, 11.1, 11.2_

- [x] 7.2.3 Enhance AssetResource and UserResource
  - Build asset inventory management with utilization analytics ✓ (existing)
  - Implement user management with superuser-only access ✓ (existing)
  - Add bulk operations and activity tracking ✓ (existing)
  - Create comprehensive audit trails ✓ (existing via Auditable trait)
  - _Requirements: 8.3, 8.5, 14.3_

### 7.3 Filament Dashboard and Widgets

- [x] 7.3.1 Create unified dashboard with real-time widgets ✓
  - Implement statistics widgets with 300-second refresh ✓ (UnifiedDashboardOverview with CacheableWidget trait)
  - Create trend charts and utilization analytics ✓ (UnifiedAnalyticsChart, AssetUtilizationWidget, TicketVolumeChart)
  - Build activity feed with real-time updates ✓ (RecentActivityFeedWidget with WebSocket listeners)
  - Add critical alerts and quick actions ✓ (CriticalAlertsWidget, QuickActionsWidget with BM translations)
  - _Requirements: 8.4, 10.2, 10.3_

- [ ]* 7.3.2 Write property test for Filament RBAC
  - **Property 7: Filament Role-Based Access Control**
  - **Validates: Requirements 8.1, 8.2, 14.2**

---

## Phase 8: Authenticated Portal Development

### 8.1 Staff Dashboard Implementation

- [x] 8.1.1 Create personalized staff dashboard ✓
  - Implement statistics cards with real-time updates (300s polling) ✓ (AuthenticatedDashboard with OptimizedLivewireComponent)
  - Build recent activity feed with lazy loading ✓ (recentActivities computed property with caching)
  - Create quick actions widget with role-based visibility ✓ (Grade 41+ approval card conditional)
  - Add responsive design for mobile/tablet/desktop ✓ (grid-cols-1/2/4 responsive layout)
  - _Requirements: 9.1, 15.1, 15.2_

### 8.2 Submission History Management

- [x] 8.2.1 Build comprehensive submission history interface ✓
  - Create tabbed interface for helpdesk tickets and asset loans ✓ (SubmissionHistory with activeTab)
  - Implement advanced filtering with saved searches ✓ (statusFilter, dateFrom, dateTo, search)
  - Add sorting, pagination, and search capabilities ✓ (sortBy, WithPagination, debounced search)
  - Build submission detail view with timeline and comments ✓ (SubmissionDetail component)
  - _Requirements: 9.2, 11.4_

- [ ] 8.2.2 ~~Implement guest submission claiming~~ **REMOVE FOR PKS 5.2.1**
  - ~~Create email-based submission matching system~~ **REMOVE** - Guest Mode eliminated
  - ~~Build claim verification workflow~~ **REMOVE** - All submissions require SSO
  - ~~Add automatic account linking functionality~~ **REMOVE** - No guest submissions to claim
  - ~~Implement audit logging for claim actions~~ **REMOVE** - Feature deprecated
  - **PKS 5.2.1 UPDATE**: Remove ClaimSubmissions component entirely
  - **Files to Update**:
    - `app/Livewire/Staff/ClaimSubmissions.php` - DELETE
    - `resources/views/livewire/staff/claim-submissions.blade.php` - DELETE
    - `tests/Unit/Services/GuestSubmissionClaimServiceTest.php` - DELETE
    - `app/Services/GuestSubmissionClaimService.php` - DELETE (if exists)
  - _Requirements: 22.1 (PKS 5.2.1 - Guest Mode elimination)_

### 8.3 Profile Management

- [x] 8.3.1 Create comprehensive profile management ✓
  - Build profile form with real-time validation ✓ (UserProfile with Livewire validation)
  - Implement notification preferences management ✓ (updateNotificationPreferences, emailFrequency)
  - Create security settings with password change ✓ (updatePassword with Password rules)
  - Add profile completeness indicator ✓ (read-only fields with requestCorrection)
  - _Requirements: 9.3_

### 8.4 Approval Interface for Grade 41+ Officers

- [x] 8.4.1 Build loan approval interface ✓
  - Create approval queue with filtering and sorting ✓ (pendingApprovals with statusFilter, applicantSearch)
  - Implement approval modal with comprehensive details ✓ (openApprovalModal, approvalRemarks)
  - Add bulk approval/rejection functionality ✓ (bulkApprove, bulkReject with selectedApplications)
  - Build approval workflow with email notifications ✓ (NotificationService.sendApprovalDecision)
  - _Requirements: 9.4_

- [ ]* 8.4.2 Write property test for portal authentication
  - **Property 8: Portal Authentication and Authorization**
  - **Validates: Requirements 9.1, 9.4, 14.2**

---

## Phase 9: Real-Time Features and Notifications

### 9.1 Laravel Reverb WebSocket Integration

- [ ] 9.1.1 Configure Laravel Reverb for real-time features **NEEDS PKS UPDATE**
  - Set up WebSocket server configuration ✓ (config/reverb.php, config/broadcasting.php)
  - Create private channels for user-specific updates ✓ (routes/channels.php - user.{userId})
  - ~~Create guest UUID channels (ticket.{uuid}, loan.{uuid})~~ **REMOVE** - PKS 5.2.1
  - Implement broadcasting events for status changes ✓ (TicketStatusChanged, LoanStatusChanged with ShouldBroadcast)
  - Add client-side Echo listeners ✓ (resources/js/bootstrap.js with connection state management)
  - **PKS 5.2.1 UPDATE**: Remove guest UUID channel authorization
  - **Files to Update**:
    - `routes/channels.php` - Remove `private-ticket.{uuid}` and `private-loan.{uuid}` guest channels
    - `app/Events/Concerns/BroadcastsToHybridChannels.php` - Remove guest channel logic
    - `app/Events/CommentPosted.php` - Update for authenticated-only channels
    - `app/Events/StatusUpdated.php` - Update for authenticated-only channels
  - _Requirements: 10.1, 10.3, 22.1 (PKS 5.2.1)_

### 9.2 Notification System

- [x] 9.2.1 Build comprehensive notification center ✓
  - Create notification bell with unread count ✓ (NotificationBell.php with categorized notifications)
  - Implement notification center with filtering ✓ (NotificationCenter.php with pagination, bulk actions)
  - Add real-time notification broadcasting ✓ (NotificationCreated event, RealtimeNotificationListener component)
  - Build email notification preferences ✓ (NotificationPreferences.php component exists)
  - _Requirements: 10.2, 10.4, 10.5_

- [x] 9.2.2 Implement ARIA live regions for accessibility ✓
  - Add screen reader announcements for notifications ✓ (aria-live.blade.php with polite/assertive regions)
  - Create accessible notification interactions ✓ (notification-bell.blade.php with ARIA attributes)
  - Implement keyboard navigation for notification center ✓ (tabindex, focus management, Escape key handling)
  - Add proper ARIA labels and roles ✓ (role="menu", aria-expanded, aria-haspopup, aria-controls)
  - _Requirements: 10.4, 7.4_

- [x]* 9.2.3 Write feature test for notification functionality ✓
  - Created NotificationFunctionalityTest.php with 17 test cases ✓
  - Tests notification bell display, mark as read, mark all as read ✓
  - Tests notification center filtering (all, unread, read, by type) ✓
  - Tests notification deletion, pagination, real-time updates ✓
  - Tests WCAG compliance with BM-exclusive interface ✓
  - **Validates: Requirements 10.1, 10.2, 10.3, 10.4**

---

## Phase 10: Cross-Module Integration

### 10.1 Helpdesk and Asset Loan Integration

- [x] 10.1.1 Implement automatic maintenance ticket creation ✓
  - Create auto-ticket generation for damaged asset returns (5s SLA) ✓ (CrossModuleIntegrationService::createMaintenanceTicket)
  - Build cross_module_integrations table and relationships ✓ (migration 2025_11_03_045426)
  - Implement asset information display in ticket details ✓ (CrossModuleIntegrationsRelationManager)
  - Add ticket history display in asset details ✓ (getUnifiedAssetHistory, getAssetLifecycleReport)
  - _Requirements: 11.1, 11.2, 11.3_

### 10.2 Unified Search and Navigation

- [x] 10.2.1 Build unified search across modules ✓
  - Create global search with relevance ranking ✓ (UnifiedSearchService, FuzzySearchService)
  - Implement cross-module result categorization ✓ (UnifiedSearch Livewire component)
  - Add search result previews and quick actions ✓ (unified-search.blade.php modal)
  - Build search history and saved searches ✓ (Filament UnifiedSearch page)
  - _Requirements: 11.4_

- [x] 10.2.2 Implement referential integrity ✓
  - Add foreign key constraints for data integrity ✓ (cross_module_integrations migration)
  - Create cascade and restrict rules for deletions ✓ (cascadeOnDelete, restrictOnDelete)
  - Implement data validation across modules ✓ (CrossModuleIntegration model validation)
  - Add integrity checking utilities ✓ (ReferentialIntegrityTest.php)
  - _Requirements: 11.5_

- [ ]* 10.2.3 Write property test for cross-module integration
  - **Property 10: Cross-Module Data Integrity**
  - **Validates: Requirements 11.1, 11.2, 11.3, 11.4, 11.5**

---

## Phase 11: Export and Reporting

### 11.1 Export Functionality

- [x] 11.1.1 Implement comprehensive export system ✓
  - Created ExportService for CSV, Excel, and PDF formats ✓ (app/Services/ExportService.php)
  - Built ReportBuilder Filament page with module selection and filtering ✓ (app/Filament/Pages/ReportBuilder.php)
  - Implemented queue processing for large exports (>1000 records) ✓ (app/Jobs/ExportSubmissionsJob.php)
  - Added WCAG-compliant export formatting ✓ (ReportExportService with BM headers)
  - Created DataExportService for loan applications and assets ✓
  - Implemented CleanupExportsCommand for file retention ✓
  - _Requirements: 12.1, 12.4_

### 11.2 Automated Reporting

- [x] 11.2.1 Build automated report scheduling ✓
  - Created ReportScheduleResource Filament interface ✓ (app/Filament/Resources/Reports/ReportScheduleResource.php)
  - Implemented email delivery via AutomatedReportService ✓ (app/Services/AutomatedReportService.php)
  - Added ReportSchedule model with frequency options (daily/weekly/monthly) ✓
  - Built report history and tracking with last_run_at/next_run_at ✓
  - Created ReportBuilderService for data extraction ✓
  - _Requirements: 12.2, 12.3_

- [x]* 11.2.2 Write feature tests for export functionality ✓
  - Created ExportFunctionalityTest with 17 test cases ✓
  - Tests CSV export, PDF export, large export queueing ✓
  - Tests file retention, date filtering, permissions ✓
  - Tests progress indicators and filename patterns ✓
  - **Validates: Requirements 12.1, 12.2, 12.4**

---

## Phase 12: Performance Optimization

### 12.1 Core Web Vitals Optimization

- [x] 12.1.1 Achieve Core Web Vitals targets
  - Optimize Largest Contentful Paint (LCP <2.5s) ✓ (PerformanceOptimizationService, critical CSS inlining)
  - Minimize First Input Delay (FID <100ms) ✓ (JavaScript deferral, code splitting in vite.config.js)
  - Reduce Cumulative Layout Shift (CLS <0.1) ✓ (skeleton-loader component, performance.css reservations)
  - Optimize Time to First Byte (TTFB <600ms) ✓ (PerformanceOptimizationMiddleware, Redis caching)
  - _Requirements: 13.1_

### 12.2 Caching and Query Optimization

- [x] 12.2.1 Implement comprehensive caching strategy
  - Set up Redis caching for dashboard statistics (5-minute TTL) ✓ (PerformanceOptimizationService::cacheDashboardStats)
  - Implement user data caching (10-minute TTL) ✓ (PerformanceOptimizationService::cacheUserData)
  - Add query result caching for expensive operations ✓ (QueryOptimization trait)
  - Create cache invalidation strategies ✓ (invalidateUserCache, invalidateQueryCache methods)
  - _Requirements: 13.2_

- [x] 12.2.2 Optimize database queries
  - Implement eager loading to prevent N+1 queries ✓ (QueryOptimization::withEagerLoading)
  - Add database indexes for frequently queried columns ✓ (existing migrations)
  - Optimize complex queries with query builder ✓ (buildOptimizedQuery method)
  - Create query monitoring and optimization tools ✓ (measureQueryTime, enableSlowQueryLogging)
  - _Requirements: 13.3_

### 12.3 Frontend Asset Optimization

- [x] 12.3.1 Optimize frontend performance
  - Implement lazy loading for images and components ✓ (optimized-image.blade.php component)
  - Set up code splitting for route-based chunks ✓ (vite.config.js manualChunks)
  - Add image optimization with WebP format ✓ (getOptimizedImageSources method)
  - Create progressive enhancement patterns ✓ (skeleton-loader.blade.php, performance.css)
  - _Requirements: 13.4_

- [ ]* 12.3.2 Write property test for performance targets
  - **Property 12: Performance Optimization**
  - **Validates: Requirements 13.1, 13.2, 13.3, 13.4**

---

## Phase 13: Mobile Optimization

### 13.1 Responsive Design Implementation

- [x] 13.1.1 Implement comprehensive responsive design ✓
  - Create responsive breakpoints (320px, 768px, 1280px) ✓ (MobileOptimizationService::getBreakpoints)
  - Build mobile-optimized navigation with hamburger menu ✓ (mobile-menu.blade.php)
  - Implement bottom navigation bar for quick access ✓ (bottom-navigation.blade.php with 44×44px touch targets)
  - Add floating action button (FAB) for primary actions ✓ (floating-action-button.blade.php with speed dial)
  - Created MobileOptimizationService for device detection and optimization ✓
  - Created MobileOptimizationMiddleware for request handling ✓
  - _Requirements: 15.1, 15.2, 15.5_

### 13.2 Touch-Friendly Interactions

- [x] 13.2.1 Build mobile-specific interactions ✓
  - Implement swipe gestures for navigation ✓ (swipe-actions.blade.php with keyboard fallback)
  - Add pull-to-refresh for dashboard updates ✓ (pull-to-refresh.blade.php with Livewire integration)
  - Create touch-friendly form interactions ✓ (touch-input.blade.php with autocomplete, inputmode)
  - Ensure minimum 44×44px touch targets ✓ (min-h-11 min-w-11 classes throughout)
  - _Requirements: 15.3, 7.5_

- [x] 13.2.2 Optimize mobile performance ✓
  - Reduce data transfer for mobile connections ✓ (MobileOptimizationService::getOptimizedImageSizes)
  - Implement offline capability for cached data ✓ (MobileOptimizationService::getOfflineConfig)
  - Add progressive web app features ✓ (getMobileMetaTags with viewport-fit, theme-color)
  - Create mobile-specific loading strategies ✓ (getPaginationLimit, generateSrcset)
  - _Requirements: 15.4_

- [x]* 13.2.3 Write feature test for mobile accessibility ✓
  - Created MobileOptimizationTest.php with 20+ test cases ✓
  - Tests device detection (mobile, tablet, desktop) ✓
  - Tests breakpoint configuration with MyDS compliance ✓
  - Tests touch target WCAG compliance (44×44px minimum) ✓
  - Tests component rendering with BM-exclusive interface ✓
  - **Validates: Requirements 15.1, 15.2, 15.3, 7.5**

---

## Phase 14: Security and Compliance

### 14.1 Security Implementation

- [x] 14.1.1 Implement comprehensive security controls
  - Set up secure authentication with Laravel Breeze ✓ (existing Laravel Breeze integration)
  - Implement session management with timeout controls ✓ (SessionTimeoutMiddleware - 30 min timeout)
  - Add CSRF protection and rate limiting ✓ (Laravel CSRF + GuestFormRateLimiter)
  - Create security monitoring and alerting ✓ (SecurityMonitoringService with alerts)
  - Added SecurityHeadersMiddleware with OWASP-compliant headers (CSP, HSTS, X-Frame-Options) ✓
  - Implemented IpBlockingService with auto-block for repeat offenders ✓
  - _Requirements: 14.1, 14.2_

### 14.2 Audit and Compliance

- [x] 14.2.1 Build comprehensive audit system
  - Implement audit logging with 7-year retention ✓ (config/audit.php - retention.years = 7)
  - Create PDPA 2010 compliance features ✓ (PDPAComplianceService with consent, retention, data rights)
  - Add data subject rights management ✓ (getUserPersonalData, requestDataCorrection, requestDataDeletion)
  - Build security incident tracking ✓ (SecurityMonitoringService.createAlert, logSecurityEvent)
  - Implemented HashedIpAddressResolver for PDPA-compliant IP storage ✓
  - Dual Audit System: owen-it (compliance) + spatie (operational) ✓
  - _Requirements: 14.3, 14.4, 14.5_

- [x]* 14.2.2 Write property test for security compliance
  - **Property 14: Security and Audit Compliance**
  - **Validates: Requirements 14.1, 14.2, 14.3, 14.4, 14.5**
  - Existing tests: SecurityComplianceValidationTest.php, FilamentSecurityTest.php ✓
  - Tests cover: PDPA consent, data retention, data subject rights, security monitoring ✓

---

## Phase 15: Cloud Hybrid AI Interface (D18 v4.0 - PKS 9.2.1 DLP)

### 15.1 AI Chat Interface Implementation

- [ ] 15.1.1 Create BedrockChat Livewire component **NEEDS PKS UPDATE**
  - Implement AI chat interface at `/ai/chat` route ✓ (Added route)
  - Add model selection dropdown (Opus 4.5, Sonnet 4.5, Haiku 4.5) ✓ (Existing)
  - Implement query analysis and model routing logic ✓ (Existing)
  - Add conversation history display with markdown rendering ✓ (Existing)
  - **PKS 5.2.1 UPDATE**: Require SSO authentication for AI chat access
  - **PKS 9.2.1 UPDATE**: Add DLP filtering indicator in UI
  - **Files to Update**:
    - `app/Livewire/AI/ChatInterface.php` - Add auth middleware, DLP indicator
    - `resources/views/livewire/ai/chat-interface.blade.php` - Add DLP status display
  - _Requirements: 16.1, 16.2, 16.3, 22.1 (PKS 5.2.1), 25.1 (PKS 9.2.1)_

- [ ] 15.1.2 Implement model routing service with DLP **NEEDS PKS UPDATE**
  - Create ModelRouter service for query classification ✓ (Existing)
  - Implement FAQ keyword detection (tiket, helpdesk, pinjaman, etc.) ✓ (Existing)
  - Add complex reasoning keyword detection (analisis, bandingkan, etc.) ✓ (Existing)
  - Route queries to appropriate backend (Ollama/Bedrock/Both) ✓ (Existing)
  - **PKS 9.2.1 UPDATE**: Add DLP filtering before cloud AI routing
  - **Files to Update**:
    - `app/Services/AI/ModelRouter.php` - Add DLP check before Bedrock routing
    - `app/Services/AI/DlpFilteringService.php` - CREATE for PII detection
  - _Requirements: 16.2, 16.3, 25.2, 25.3 (PKS 9.2.1)_

- [x] 15.1.3 Add conversation management features ✓
  - Implement BedrockConversation model integration ✓ (Existing)
  - Add save/load/delete conversation functionality ✓ (Existing)
  - Persist conversation history across sessions ✓ (Existing)
  - Display conversation list with search and filtering ✓ (Existing)
  - _Requirements: 16.4_

- [x] 15.1.4 Implement response source attribution ✓
  - Display source indicator (Ollama, Bedrock, Hybrid) ✓ (Existing)
  - Add confidence scoring display ✓ (Existing)
  - Implement web-augmented response toggle ✓ (Existing)
  - Ensure all responses in Bahasa Melayu ✓ (Existing)
  - _Requirements: 16.5, 16.6, 16.8_

- [ ]* 15.1.5 Write property test for AI query routing with DLP
  - **Property 21: Cloud Hybrid AI Query Routing**
  - **Property 30: DLP Filtering for Cloud AI (PKS 9.2.1)**
  - **Validates: Requirements 16.1, 16.2, 16.3, 25.1, 25.2, 25.3**

### 15.2 FAQ Bot Widget Implementation

- [ ] 15.2.1 Create FaqBotWidget Livewire component **NEEDS PKS UPDATE**
  - Implement floating button (bottom-right, 44×44px) ✓ (Existing)
  - Create expandable chat panel with ARIA dialog pattern ✓ (Existing)
  - Add focus trap for accessibility ✓ (Existing)
  - Implement keyboard navigation (ESC to close, Tab navigation) ✓ (Existing)
  - **PKS 5.2.1 UPDATE**: Require SSO authentication - no guest access
  - **Files to Update**:
    - `app/Livewire/AI/FaqBotWidget.php` - Add auth check, hide for guests
    - `resources/views/livewire/ai/faq-bot-widget.blade.php` - Add @auth directive
  - _Requirements: 17.1, 17.2, 17.5, 22.1 (PKS 5.2.1)_

- [x] 15.2.2 Integrate Ollama RAG service ✓
  - Connect to RagService for FAQ queries ✓ (Existing)
  - Implement context-aware FAQ suggestions ✓ (Existing)
  - Add response time optimization (<5 seconds target) ✓ (Existing)
  - Display FAQ sources with confidence scores ✓ (Existing)
  - _Requirements: 17.3, 17.4_

- [x] 15.2.3 Implement accessibility features ✓
  - Add ARIA live regions for response announcements ✓ (Existing)
  - Implement screen reader support ✓ (Existing)
  - Ensure WCAG 2.2 AA compliance ✓ (Existing)
  - Add BM-exclusive content display ✓ (Existing)
  - _Requirements: 17.5, 17.7, 17.8_

- [ ]* 15.2.4 Write property test for FAQ Bot accessibility
  - **Property 22: FAQ Bot Widget Accessibility**
  - **Validates: Requirements 17.1, 17.2, 17.5, 17.7**

### 15.3 AI Admin Management Interface

- [x] 15.3.1 Create AI Dashboard Filament widget ✓
  - Implement real-time metrics display (model usage, response times) ✓ (OllamaPerformance page)
  - Add cost estimation widget ✓ (ModelRouter service)
  - Display multi-system health status (Ollama, Bedrock, DuckDuckGo) ✓ (OllamaPerformance page)
  - Implement 300-second auto-refresh ✓ (Cache with 60s TTL)
  - _Requirements: 18.1, 18.6_

- [x] 15.3.2 Implement FaqResource for knowledge base management ✓
  - Create CRUD interface for FAQ entries ✓ (Existing)
  - Add bulk import/export functionality ✓ (Existing)
  - Implement category management ✓ (Existing)
  - Add embedding regeneration action ✓ (Existing)
  - _Requirements: 18.2_

- [x] 15.3.3 Create DocumentResource for AI document ingestion ✓
  - Implement document upload with PII detection ✓ (Existing)
  - Add semantic search configuration ✓ (Existing)
  - Display document chunk preview ✓ (Existing)
  - Implement ingestion status tracking ✓ (Existing)
  - _Requirements: 18.3_

- [x] 15.3.4 Build AutoReplyResource for approval workflow ✓
  - Create review interface for AI-generated drafts ✓ (Existing)
  - Implement approve/reject actions ✓ (Existing)
  - Add edit capability before approval ✓ (Existing)
  - Display generation metadata and confidence ✓ (Existing)
  - _Requirements: 18.4_

- [x] 15.3.5 Create Model Configuration page ✓
  - Implement Ollama model parameter tuning ✓ (BedrockModelConfigResource)
  - Add Bedrock model configuration ✓ (BedrockModelConfigResource)
  - Display model availability status ✓ (OllamaPerformance page)
  - Add rate limit monitoring ✓ (ModelRouter service)
  - _Requirements: 18.5_

- [ ]* 15.3.6 Write property test for AI admin dashboard
  - **Property 25: AI Admin Dashboard Metrics**
  - **Validates: Requirements 18.1, 18.6, 18.8**

---

## Phase 16: Final Integration and Testing

### 16.1 System Integration Testing

- [x] 16.1.1 Comprehensive integration testing ✓
  - Test all four layers (guest, portal, admin, AI) integration ✓
  - Verify cross-module functionality including AI features ✓
  - Test real-time features across all interfaces ✓
  - Validate role-based access controls for AI features ✓
  - Phase16FinalIntegrationTest.php: 23 tests, 45 assertions passing ✓
  - _Requirements: All consolidated requirements_

### 16.2 Accessibility and Performance Validation

- [x] 16.2.1 Final accessibility and performance validation ✓
  - Run comprehensive WCAG 2.2 AA compliance testing including AI interfaces ✓
  - Validate Core Web Vitals across all pages including AI chat ✓
  - Test mobile responsiveness and touch interactions for FAQ Bot ✓
  - Verify theme switching and BM exclusive interface in AI components ✓
  - WcagComplianceTest.php: 4 tests, 58 assertions passing ✓
  - AccessibilityAuditTest.php: 13 tests passing ✓
  - LoanModuleWcagComplianceTest.php: 20 tests passing ✓
  - _Requirements: 7.6, 13.5, 15.5, 16.7, 17.7_

- [ ]* 16.2.2 Write comprehensive system test
  - **Property 15: Complete System Integration**
  - **Validates: All 18 consolidated requirements**

---

## Progress Summary

### Completed Phases

| Phase | Status | Completion Date |
|-------|--------|-----------------|
| Phase 1: Foundation and Core Architecture | ✅ Complete | 14 December 2025 |
| Phase 2: BM Interface | ✅ Complete | 14 December 2025 |
| Phase 3: Theme Switcher | ✅ Complete | 14 December 2025 |
| Phase 4: Unified Component Library | ✅ Complete | 14 December 2025 |
| Phase 6: Livewire 3.7 and Volt 1.10 Architecture | ✅ Complete | 14 December 2025 |
| Phase 7: Filament Admin Panel | ✅ Complete | 14 December 2025 |
| Phase 8: Authenticated Portal Development | ✅ Complete | 14 December 2025 |
| Phase 9: Real-Time Features and Notifications | ✅ Complete | 14 December 2025 |
| Phase 10: Cross-Module Integration | ✅ Complete | 14 December 2025 |
| Phase 11: Export and Reporting | ✅ Complete | 14 December 2025 |
| Phase 12: Performance Optimization | ✅ Complete | 14 December 2025 |
| Phase 13: Mobile Optimization | ✅ Complete | 14 December 2025 |
| Phase 14: Security and Compliance | ✅ Complete | 14 December 2025 |

### Partially Completed Phases

| Phase | Status | Notes |
|-------|--------|-------|
| Phase 1: Foundation | ✅ Complete | All infrastructure verified and documented |
| Phase 2: BM Interface | ✅ Complete | 2.1.1, 2.1.2, 2.1.3 complete; 2.1.4 optional test |

### Completed Phases (Continued)

| Phase | Status | Completion Date |
|-------|--------|-----------------|
| Phase 15: Cloud Hybrid AI Interface (D18 v1.0.1) | ✅ Complete | 17 December 2025 |

### Pending Phases

- Phase 5: Figma MCP Integration (optional - design-to-code workflow)
- Phase 16: Final Integration and Testing (comprehensive system validation)

---

## Notes

- Tasks marked with `*` are optional property-based tests
- All tasks reference specific requirements from requirements.md v3.6.1-r1
- Implementation follows D00-D18 documentation standards
- Phase 15 (AI) tasks are NEW additions per D18 v1.0.1 Cloud Hybrid AI Architecture

---

## Phase 16: FRONTPAGE_DESIGN_ANALYSIS Remediation

### 16.1 Translation Key Fixes (P0 - Critical)

- [x] 16.1.1 Add missing translation keys to status.php ✓
  - Add `page_tagline` key to `lang/ms/status.php`
  - Add `quick_help_title` key to `lang/ms/status.php`
  - Add `quick_help_email` key to `lang/ms/status.php`
  - Add `quick_help_phone` key to `lang/ms/status.php`
  - Add `quick_help_ticket` key to `lang/ms/status.php`
  - Add `quick_help_ticket_cta` key to `lang/ms/status.php`
  - _Requirements: 21.1, 21.4_

- [x] 16.1.2 Add corresponding English translation keys ✓
  - Add same keys to `lang/en/status.php` for technical reference
  - Ensure key consistency between BM and EN files
  - _Requirements: 21.4_

### 16.2 Landing Page Enhancements

- [x] 16.2.1 Verify landing page WCAG compliance ✓
  - Hero section: White text on dark background (15:1 contrast) ✓
  - Services section: Dark text on light background (15.4:1 contrast) ✓
  - CTA buttons: Proper contrast ratios verified ✓
  - _Requirements: 16.2, 7.1_

- [x] 16.2.2 Verify touch targets on landing page ✓
  - All CTA buttons use `min-h-11` (44px) ✓
  - FAQ accordion buttons use `min-h-11` ✓
  - Modal close buttons use `min-h-11 min-w-11` ✓
  - _Requirements: 16.3, 7.5_

### 16.3 FAQ Bot Widget Fixes

- [x] 16.3.1 Fix FAQ Bot header button touch targets ✓
  - Updated minimize button from `min-h-8 min-w-8` to `min-h-11 min-w-11` ✓
  - Updated close button from `min-h-8 min-w-8` to `min-h-11 min-w-11` ✓
  - Added `flex items-center justify-center` for proper icon centering ✓
  - _Requirements: 17.4, 7.5_

- [x] 16.3.2 Verify FAQ Bot ARIA compliance ✓
  - `role="dialog"` implemented ✓
  - `aria-modal="true"` implemented ✓
  - `aria-labelledby` implemented ✓
  - `aria-live="polite"` for messages ✓
  - ESC key to close implemented ✓
  - _Requirements: 17.3, 17.5_

### 16.4 Status Check Page Fixes

- [x] 16.4.1 Fix Quick Help sidebar translations ✓
  - Update blade template to use proper translation keys ✓
  - Verify all sidebar text displays in Bahasa Melayu ✓
  - Test both light and dark mode rendering ✓
  - _Requirements: 19.4, 21.1_

- [x] 16.4.2 Verify Status Check form accessibility ✓
  - Token input with `wire:model.live.debounce.300ms` ✓
  - Dropdown with proper ARIA attributes ✓
  - Loading state with spinner animation ✓
  - Error messages with `role="alert"` ✓
  - _Requirements: 19.2, 19.3_

### 16.5 Guest Helpdesk Form Verification

- [x] 16.5.1 Verify multi-step wizard compliance ✓
  - 3-step wizard with progress indicator ✓
  - Step labels in Bahasa Melayu ✓
  - Touch targets 44×44px on step indicators ✓
  - _Requirements: 20.1_

- [x] 16.5.2 Verify declaration (Perakuan) section ✓
  - Bilingual legal text (BM + EN) ✓
  - Mandatory checkboxes with validation ✓
  - Warning border styling ✓
  - _Requirements: 20.4_

- [x] 16.5.3 Verify Optimistic UI implementation ✓
  - Immediate feedback on submission ✓
  - Rollback mechanism on error ✓
  - ARIA live region for announcements ✓
  - _Requirements: 20.5_

- [ ]* 16.5.4 Write property test for translation completeness
  - **Property 14: Translation Key Completeness**
  - **Validates: Requirements 21.1, 21.2, 21.3, 21.4, 21.5**

---

## Phase 17: Final Validation Checkpoint

- [x] 17.1 Checkpoint - Ensure all translation keys are complete ✓
  - Run translation key validation script ✓ (Phase17FinalValidationTest)
  - Verify no raw keys displayed in UI ✓ (8 tests passing)
  - Test all pages in both light and dark modes ✓ (theme tests passing)
  - _Requirements: 21.1, 21.2, 21.3_

- [x] 17.2 Checkpoint - Ensure all WCAG 2.2 AA requirements met ✓
  - Run accessibility validation tests ✓ (Phase17FinalValidationTest)
  - Verify ARIA landmarks, focus indicators, keyboard navigation ✓ (15 tests passing)
  - Test keyboard navigation on all interactive elements ✓
  - _Requirements: 7.1, 7.2, 7.3, 7.4, 7.5, 7.6_

---

## Updated Progress Summary

### Phase 16 Status

| Task | Status | Notes |
|------|--------|-------|
| 16.1.1 Add missing BM translation keys | ✅ Complete | 6 keys added |
| 16.1.2 Add EN translation keys | ✅ Complete | For technical reference |
| 16.2.1 Landing page WCAG compliance | ✅ Complete | Contrast ratios verified |
| 16.2.2 Landing page touch targets | ✅ Complete | All buttons 44×44px |
| 16.3.1 FAQ Bot touch targets | ✅ Complete | Fixed 13 Dec 2025 |
| 16.3.2 FAQ Bot ARIA compliance | ✅ Complete | All attributes verified |
| 16.4.1 Status Check translations | ✅ Complete | Quick Help sidebar verified |
| 16.4.2 Status Check accessibility | ✅ Complete | Form fully accessible |
| 16.5.1 Wizard compliance | ✅ Complete | 3-step wizard verified |
| 16.5.2 Declaration section | ✅ Complete | Bilingual text verified |
| 16.5.3 Optimistic UI | ✅ Complete | Rollback mechanism works |

### Phase 17 Status

| Task | Status | Notes |
|------|--------|-------|
| 17.1 Translation key validation | ✅ Complete | 8 tests, no raw keys in UI |
| 17.2 WCAG 2.2 AA compliance | ✅ Complete | 15 tests, all landmarks verified |

### Overall Completion: 100%

**Remaining Items:**

1. ~~Phase 17 Final Validation Checkpoint~~ ✅ Complete
2. Optional property-based tests (marked with *)

**Completed Critical Items (17 December 2025):**

1. ✅ Translation keys added to `lang/ms/status.php` and `lang/en/status.php`
2. ✅ Status Check page Quick Help sidebar using proper translation keys
3. ✅ Phase 16 Integration Testing - 23 tests, 45 assertions passing
4. ✅ WCAG 2.2 AA Compliance Testing - All accessibility tests passing
5. ✅ Phase 17 Final Validation - 23 tests, 67 assertions passing

---

---

## Phase 18: Portal Layout System Implementation ✅

### 18.1 Portal Layout Architecture

- [x] 18.1.1 Create comprehensive portal layout structure ✅
  - Implemented portal/layouts/app.blade.php with unified layout ✅
  - Added WCAG 2.2 AA compliant landmark structure (header, nav, main, aside, footer) ✅
  - Integrated theme switcher with FOUT prevention ✅
  - Added responsive design with mobile-first approach ✅
  - _Requirements: 19.1, 19.3, 19.5_

- [x] 18.1.2 Build portal navigation components ✅
  - Created portal/components/header.blade.php with MOTAC branding ✅
  - Implemented portal/components/navbar.blade.php with role-based navigation ✅
  - Built portal/components/sidebar.blade.php with collapsible design ✅
  - Added portal/components/breadcrumb.blade.php with ARIA landmarks ✅
  - _Requirements: 19.2, 19.4, 19.6_

- [x] 18.1.3 Implement accessibility and compliance features ✅
  - Added portal/components/accessibility-menu.blade.php for WCAG features ✅
  - Created portal/partials/flash-messages.blade.php with ARIA live regions ✅
  - Implemented portal/components/footer.blade.php with government compliance ✅
  - Built portal/data-rights/ pages for PDPA 2010 compliance ✅
  - _Requirements: 19.4, 19.7, 19.8_

- [x] 18.1.4 Write comprehensive tests for portal layout ✅
  - Created PortalLayoutComponentsTest.php with core region validation ✅
  - Tests portal layout renders all required components ✅
  - Tests data rights pages integration ✅
  - Validates WCAG landmark structure and accessibility ✅
  - **Property 26: Portal Layout System Compliance validated**

---

**Document Version**: 3.6.1-r2  
**Last Updated**: 17 December 2025  
**Author**: BPM MOTAC Development Team  
**Status**: Complete - All Phases (1-17) Validated

- All tasks reference specific requirements from the consolidated requirements.md
- Tasks build incrementally on previous phases
- Each task is actionable by a coding agent
- Focus on implementation tasks that can be executed within development environment
- Property tests validate correctness across multiple inputs and scenarios

---

## Task Execution Guidelines

### Implementation Priority

1. **Foundation First**: Complete tasks 1-3 before proceeding to component development
2. **Component Library**: Build unified components (task 4) before implementing specific features
3. **Integration Last**: Cross-module integration (task 9) requires completion of individual modules
4. **Testing Continuous**: Execute property-based and unit tests alongside implementation tasks

### Quality Gates

- All code must pass Laravel Pint PSR-12 formatting
- All components must include WCAG 2.2 AA compliance verification
- All Livewire components must use OptimizedLivewireComponent trait
- All text must be in Bahasa Melayu exclusively per v3.6.0 policy

### Performance Requirements

- Core Web Vitals targets must be met: LCP <2.5s, FID <100ms, CLS <0.1, TTFB <600ms
- Lighthouse scores: Performance 90+, Accessibility 100, Best Practices 100, SEO 100
- Component reusability target: 95%+ across all interfaces

### Documentation Standards

- All components must include metadata headers with D00-D17 traceability
- Property-based tests must reference specific correctness properties
- User documentation must be provided in Bahasa Melayu

---

## Next Implementation Priority

Based on the current progress, the recommended implementation order for remaining phases:

1. **Phase 2: BM Interface** (2.1.1, 2.1.4) - Complete language switcher removal
2. **Phase 1: Foundation** - Verify existing infrastructure, complete any gaps
3. **Phase 14: Security and Compliance** - Audit and PDPA requirements
4. **Phase 5: Figma MCP Integration** - Design system automation
5. **Phase 15: Final Integration** - System-wide validation

---

**Document Version**: 4.0.1  
**Last Updated**: 25 December 2025  
**Author**: BPM MOTAC Development Team  
**Status**: Active - PKS Compliance Migration Phase

---

## Phase 19: PKS Compliance Migration (v4.0.0 - KRISA D01-D10, D17 v4.0)

### 19.1 SSO Mandatory Authentication (PKS 5.2.1)

- [ ] 19.1.1 Delete guest-related Livewire components (P0 - Critical)
  - DELETE `app/Livewire/GuestLoanApplication.php`
  - DELETE `app/Livewire/Staff/ClaimSubmissions.php`
  - DELETE `resources/views/livewire/staff/claim-submissions.blade.php`
  - DELETE `resources/views/livewire/guest-loan-application.blade.php` (if exists)
  - REMOVE `/claim-submissions` route from `routes/web.php`
  - _Requirements: 22.1, 22.2_

- [ ] 19.1.2 Delete guest-related test files (P0 - Critical)
  - DELETE `tests/Feature/GuestLoanApplicationWorkflowTest.php`
  - DELETE `tests/Feature/Livewire/GuestLoanApplicationTest.php`
  - DELETE `tests/Feature/Livewire/GuestLoanApplicationEnhancementTest.php` (if exists)
  - DELETE `tests/Feature/AccountLinkingServiceTest.php` (if exists)
  - DELETE `tests/Unit/Services/GuestSubmissionClaimServiceTest.php` (if exists)
  - _Requirements: 22.1_

- [ ] 19.1.3 Update test files to remove guest references (P0 - Critical)
  - UPDATE `tests/Feature/Performance/LivewireOptimizationTest.php` - Remove GuestLoanApplication imports and tests
  - UPDATE `tests/Feature/LoanAuthenticatedFormTest.php` - Remove guest-related test cases
  - UPDATE `tests/Feature/PerformanceIntegrationTest.php` - Remove GuestLoanApplication references
  - UPDATE `tests/Feature/Livewire/GuestLoanResponsibleOfficerTest.php` - DELETE or convert to authenticated
  - _Requirements: 22.1_

- [ ] 19.1.4 Eliminate Guest Mode completely
  - Remove all guest form routes (/helpdesk/create, /loans/create for unauthenticated users)
  - Update service selection modal to SSO-only (remove "Tidak (Tetamu)" option)
  - Add PKS 5.2.1 compliance notice to login page
  - Update landing page CTAs to redirect to SSO login
  - _Requirements: 22.1, 22.2_

- [ ] 19.1.2 Implement user_id mandatory foreign key
  - Add migration to make user_id NOT NULL on helpdesk_tickets table
  - Add migration to make user_id NOT NULL on loan_applications table
  - Update all form submissions to require authenticated user
  - Add database constraint validation
  - _Requirements: 22.3_

- [ ] 19.1.3 Implement authentication event logging
  - Create AuthenticationEventLogger service
  - Log all login events (success, failure) with timestamp, IP, user agent
  - Log all logout events
  - Implement failed login attempt tracking
  - _Requirements: 22.4, 22.5_

- [ ]* 19.1.4 Write property test for SSO mandatory authentication
  - **Property 27: SSO Mandatory Authentication (PKS 5.2.1)**
  - **Validates: Requirements 22.1, 22.2, 22.3, 22.4, 22.5**

### 19.2 Walk-in/Kiosk Mode with SSO

- [ ] 19.2.1 Create Walk-in/Kiosk Mode interface
  - Create KioskLogin Livewire component at /kiosk/login route
  - Implement simplified SSO login form for kiosk terminals
  - Add prominent "Log Keluar" button in kiosk interface
  - Implement kiosk-specific styling (larger touch targets, simplified UI)
  - _Requirements: 23.1, 23.2, 23.5_

- [ ] 19.2.2 Implement kiosk session management
  - Create KioskSessionMiddleware for 5-minute inactivity timeout
  - Implement automatic logout on inactivity
  - Add session warning modal at 4 minutes
  - Clear session data on logout
  - _Requirements: 23.3_

- [ ] 19.2.3 Implement LDAP user data pre-population
  - Create LdapUserDataService for fetching user information
  - Pre-populate name, email, department, grade from LDAP after authentication
  - Display read-only user information in forms
  - Add "Maklumat dari sistem HR" indicator
  - _Requirements: 23.4_

- [ ]* 19.2.4 Write property test for Walk-in/Kiosk Mode
  - **Property 28: Walk-in/Kiosk Mode SSO Compliance**
  - **Validates: Requirements 23.1, 23.2, 23.3, 23.4, 23.5**

### 19.3 HRMIS Auto-Provisioning

- [ ] 19.3.1 Fix HrmisIntegrationService Larastan errors (P1 - High)
  - Fix property type declarations for `$baseUrl`, `$apiKey`, `$timeout`, `$cacheMinutes`
  - Add proper return type annotations for all methods
  - Fix mixed type access issues (30+ errors identified)
  - Add proper array shape type hints
  - **File**: `app/Services/HrmisIntegrationService.php`
  - _Requirements: 24.1, 24.2_

- [ ] 19.3.2 Create HRMIS synchronization service
  - Create HrmisIntegrationService for user synchronization
  - Implement daily sync job (HrmisSyncJob) via Laravel scheduler
  - Add user creation for new employees
  - Add user deactivation for terminated employees
  - _Requirements: 24.1, 24.2, 24.3_

- [ ] 19.3.2 Implement user data updates from HRMIS
  - Update department, grade, position from HRMIS data
  - Log all user data changes for audit trail
  - Handle HRMIS sync failures with retry mechanism
  - Send notification to admin on sync failures
  - _Requirements: 24.4_

- [ ] 19.3.3 Remove manual registration functionality
  - Remove /register route and RegisterController
  - Update authentication flow to HRMIS-only provisioning
  - Add "Hubungi HR untuk pendaftaran akaun" message for unprovisioned users
  - Update documentation to reflect HRMIS-only provisioning
  - _Requirements: 24.5_

- [ ]* 19.3.4 Write property test for HRMIS auto-provisioning
  - **Property 29: HRMIS Auto-Provisioning Synchronization**
  - **Validates: Requirements 24.1, 24.2, 24.3, 24.4, 24.5**

### 19.4 DLP Filtering for Cloud AI (PKS 9.2.1)

- [ ] 19.4.1 Create DLP filtering service
  - Create DlpFilteringService for PII detection
  - Implement IC number detection and redaction (regex: \d{6}-\d{2}-\d{4})
  - Implement phone number detection and redaction
  - Implement email address detection and redaction
  - _Requirements: 25.1, 25.2_

- [ ] 19.4.2 Implement AI query routing with DLP
  - Update ModelRouter to check for PII before cloud AI routing
  - Route PII-containing queries to local Ollama only
  - Add DLP scan result to query metadata
  - Display "Data sensitif dikesan - menggunakan AI tempatan" indicator
  - _Requirements: 25.3_

- [ ] 19.4.3 Implement cloud AI request logging
  - Create CloudAiRequestLog model and migration
  - Log all cloud AI requests with data classification (public, internal, confidential)
  - Add request/response size tracking
  - Implement log retention policy (7 years)
  - _Requirements: 25.4_

- [ ] 19.4.4 Implement secure API gateway
  - Configure TLS 1.3 for all AWS Bedrock communications
  - Add certificate pinning for Bedrock endpoints
  - Implement request signing with AWS SigV4
  - Add connection timeout and retry configuration
  - _Requirements: 25.5_

- [ ]* 19.4.5 Write property test for DLP filtering
  - **Property 30: DLP Filtering for Cloud AI (PKS 9.2.1)**
  - **Validates: Requirements 25.1, 25.2, 25.3, 25.4, 25.5**

### 19.5 Intranet-Only Deployment (PKS 4.2)

- [ ] 19.5.1 Implement network access restrictions
  - Create IntranetOnlyMiddleware to reject external requests
  - Configure allowed IP ranges (MOTAC intranet only)
  - Add appropriate error message for external access attempts
  - Log all rejected external access attempts
  - _Requirements: 26.1, 26.2_

- [ ] 19.5.2 Configure data storage restrictions
  - Verify all database connections point to MOTAC Data Center
  - Verify all file storage uses local MOTAC storage
  - Verify all log files stored within MOTAC infrastructure
  - Add configuration validation on application boot
  - _Requirements: 26.3_

- [ ] 19.5.3 Configure local AI for data sovereignty
  - Ensure Ollama LLM runs on MOTAC infrastructure
  - Configure Ollama as primary AI for sensitive data
  - Add fallback to Ollama when Bedrock unavailable
  - Document data sovereignty compliance
  - _Requirements: 26.4_

- [ ] 19.5.4 Implement network segmentation
  - Document firewall rules for MOTAC IP ranges
  - Add network configuration validation
  - Create network access audit report
  - Test external access rejection
  - _Requirements: 26.5_

- [ ]* 19.5.5 Write property test for intranet-only deployment
  - **Property 31: Intranet-Only Deployment (PKS 4.2)**
  - **Validates: Requirements 26.1, 26.2, 26.3, 26.4, 26.5**

### 19.6 Password Policy Compliance (PKS 5.4.3)

- [ ] 19.6.1 Implement password policy rules
  - Create PasswordPolicyService with PKS 5.4.3 rules
  - Enforce minimum 8-character password length
  - Add password complexity validation (if required by LDAP)
  - Display password requirements in Bahasa Melayu
  - _Requirements: 27.1, 27.5_

- [ ] 19.6.2 Implement password expiry management
  - Create PasswordExpiryMiddleware for 90-day expiry check
  - Send notification 14 days before password expiry
  - Force password change on expired passwords
  - Track password change history
  - _Requirements: 27.2_

- [ ] 19.6.3 Implement account lockout
  - Create AccountLockoutService for failed login tracking
  - Lock account after 3 consecutive failed attempts
  - Implement 30-minute lockout duration
  - Send notification to user on account lockout
  - _Requirements: 27.3_

- [ ] 19.6.4 Implement password reuse prevention
  - Create PasswordHistory model and migration
  - Store hashed passwords for last 12 passwords
  - Validate new password against history
  - Display "Kata laluan telah digunakan sebelum ini" error
  - _Requirements: 27.4_

- [ ]* 19.6.5 Write property test for password policy
  - **Property 32: Password Policy Compliance (PKS 5.4.3)**
  - **Validates: Requirements 27.1, 27.2, 27.3, 27.4, 27.5**

### 19.7 Dual Audit System Enhancement

- [ ] 19.7.1 Verify dual audit system configuration
  - Verify owen-it/laravel-auditing configuration for compliance audit
  - Verify spatie/laravel-activitylog configuration for operational audit
  - Ensure both systems log to separate tables
  - Add audit system health check
  - _Requirements: 28.1_

- [ ] 19.7.2 Implement 7-year retention policy
  - Update audit configuration for 7-year retention
  - Create AuditRetentionJob for old record archival
  - Implement audit log archival to cold storage
  - Add retention policy documentation
  - _Requirements: 28.2_

- [ ] 19.7.3 Enhance audit record completeness
  - Ensure all CRUD operations logged with before/after values
  - Add user_id to all audit records
  - Add IP address and user agent to all audit records
  - Add timestamp with timezone to all audit records
  - _Requirements: 28.3, 28.4_

- [ ] 19.7.4 Implement audit search and export
  - Create AuditLogResource in Filament for admin access
  - Implement search by user, date range, action type
  - Add export functionality (CSV, PDF) for compliance reporting
  - Add audit log dashboard widget
  - _Requirements: 28.5_

- [ ]* 19.7.5 Write property test for dual audit system
  - **Property 33: Dual Audit System with 7-Year Retention**
  - **Validates: Requirements 28.1, 28.2, 28.3, 28.4, 28.5**

### 19.8 PKS Compliance Checkpoint

- [ ] 19.8.1 Checkpoint - Verify PKS 5.2.1 compliance
  - Verify Guest Mode completely eliminated
  - Verify all access requires SSO authentication
  - Verify user_id mandatory FK on all transactional tables
  - Verify authentication event logging
  - _Requirements: 22.1-22.5_

- [ ] 19.8.2 Checkpoint - Verify PKS 9.2.1 compliance
  - Verify DLP filtering active for cloud AI
  - Verify PII detection and redaction working
  - Verify cloud AI request logging
  - Verify TLS 1.3 for Bedrock communications
  - _Requirements: 25.1-25.5_

- [ ] 19.8.3 Checkpoint - Verify PKS 4.2 and 5.4.3 compliance
  - Verify intranet-only access restrictions
  - Verify password policy enforcement
  - Verify account lockout functionality
  - Verify dual audit system with 7-year retention
  - _Requirements: 26.1-26.5, 27.1-27.5, 28.1-28.5_

### 19.9 Incident Response Interface (PKS 10.1)

- [ ] 19.9.1 Create incident reporting form
  - Create IncidentReport Livewire component at /incidents/report route
  - Implement mandatory fields (incident type, severity, description, affected systems)
  - Add incident classification dropdown (Tinggi/Sederhana/Rendah) per PKS severity matrix
  - Display form in Bahasa Melayu with WCAG 2.2 AA compliance
  - _Requirements: 29.1, 29.3_

- [ ] 19.9.2 Implement CSIRT notification system
  - Create IncidentNotificationService for CSIRT team alerts
  - Implement email notification within 5 minutes of incident report
  - Add in-app notification via Laravel Reverb WebSocket
  - Create CSIRT team role and notification preferences
  - _Requirements: 29.2_

- [ ] 19.9.3 Build incident lifecycle tracking interface
  - Create IncidentLifecycle Livewire component for status tracking
  - Implement 7-stage workflow (Pengesanan → Pelaporan → Penilaian → Pembendungan → Pemulihan → Penutupan → Laporan)
  - Add timeline visualization with status transitions
  - Implement role-based status update permissions
  - _Requirements: 29.4_

- [ ] 19.9.4 Create NACSA/MyCERT reporting interface
  - Create IncidentReportGenerator service for compliance reports
  - Implement report template with timeline, actions taken, lessons learned
  - Add export functionality (PDF, CSV) for NACSA/MyCERT submission
  - Ensure 7-year retention for incident history
  - _Requirements: 29.5, 29.6_

- [ ]* 19.9.5 Write property test for incident response management
  - **Property 34: Incident Response Management (PKS 10.1)**
  - **Validates: Requirements 29.1, 29.2, 29.3, 29.4, 29.5, 29.6**

### 19.10 BCP/DRP Monitoring Dashboard (PKS 10.2)

- [ ] 19.10.1 Create system health dashboard
  - Create SystemHealthDashboard Filament widget
  - Display RTO (Recovery Time Objective) and RPO (Recovery Point Objective) metrics
  - Add system availability percentage with 99.5% threshold indicator
  - Implement real-time health status via Laravel Pulse integration
  - _Requirements: 30.1_

- [ ] 19.10.2 Implement backup monitoring interface
  - Create BackupMonitoringService for automated backup status tracking
  - Display last backup timestamp on admin dashboard
  - Implement backup failure alerts via email and in-app notification
  - Add recovery test results display with pass/fail indicators
  - _Requirements: 30.2, 30.6_

- [ ] 19.10.3 Build BCP notification system
  - Create BcpNotificationService for availability alerts
  - Trigger notification when availability drops below 99.5%
  - Implement escalation matrix for designated personnel
  - Add notification history and acknowledgment tracking
  - _Requirements: 30.3_

- [ ] 19.10.4 Create DRP activation interface
  - Create DrpActivation Filament page for authorized administrators
  - Implement DRP activation workflow with confirmation modal
  - Log all BCP/DRP activities with timestamp and responsible personnel
  - Add DRP status indicator on admin dashboard
  - _Requirements: 30.4, 30.5_

- [ ]* 19.10.5 Write property test for BCP/DRP monitoring
  - **Property 35: Business Continuity Monitoring (PKS 10.2)**
  - **Validates: Requirements 30.1, 30.2, 30.3, 30.4, 30.5, 30.6**

### 19.11 Third Party Access Management (PKS 11.1)

- [ ] 19.11.1 Create NDA acknowledgment workflow
  - Create NdaAcknowledgment Livewire component
  - Implement NDA document display with acceptance checkbox
  - Store NDA acceptance timestamp and IP address
  - Block third-party access until NDA acknowledged
  - _Requirements: 31.1_

- [ ] 19.11.2 Implement time-limited access management
  - Create ThirdPartyAccessService for vendor access control
  - Implement access expiration with configurable duration
  - Add automatic access revocation on contract expiry
  - Send administrator notification on access expiration
  - _Requirements: 31.2, 31.4_

- [ ] 19.11.3 Build third-party audit trail
  - Create ThirdPartyAccessLog model and migration
  - Log all third-party activities with company name, purpose, access scope
  - Implement enhanced audit trail with session tracking
  - Add 7-year retention for third-party access logs
  - _Requirements: 31.3_

- [ ] 19.11.4 Create third-party access reporting
  - Create ThirdPartyAccessReport Filament page
  - Display active vendors, access duration, and activities
  - Implement filtering by vendor, date range, access type
  - Add export functionality (CSV, PDF) for compliance reporting
  - _Requirements: 31.5, 31.6_

- [ ]* 19.11.5 Write property test for third-party access management
  - **Property 36: Third Party Access Control (PKS 11.1)**
  - **Validates: Requirements 31.1, 31.2, 31.3, 31.4, 31.5, 31.6**

### 19.12 Security Training Compliance (PKS 12.1)

- [ ] 19.12.1 Create training status display
  - Add security training completion status to user profile page
  - Display training completion date and certificate link
  - Show training expiry countdown (annual requirement)
  - Implement training status badge on user dashboard
  - _Requirements: 32.1, 32.6_

- [ ] 19.12.2 Implement training reminder notifications
  - Create TrainingReminderService for automated notifications
  - Send reminders at 14 days, 7 days, 1 day before deadline
  - Implement email and in-app notification channels
  - Add notification preferences for training reminders
  - _Requirements: 32.2_

- [ ] 19.12.3 Implement feature restrictions for non-compliant users
  - Create TrainingComplianceMiddleware for access control
  - Restrict access to sensitive features for users without training
  - Display "Sila lengkapkan latihan keselamatan" message
  - Add grace period configuration for new employees
  - _Requirements: 32.3_

- [ ] 19.12.4 Build training compliance reporting
  - Create TrainingComplianceReport Filament page
  - Display compliance percentage by department
  - Implement HRMIS integration for training record sync
  - Add export functionality for compliance reporting
  - _Requirements: 32.4, 32.5_

- [ ]* 19.12.5 Write property test for security training compliance
  - **Property 37: Security Training Compliance (PKS 12.1)**
  - **Validates: Requirements 32.1, 32.2, 32.3, 32.4, 32.5, 32.6**

### 19.13 PSPM Strategic Alignment Dashboard

- [ ] 19.13.1 Implement end-to-end digital services (Teras 1)
  - Verify all helpdesk and loan processes are fully digital
  - Remove any remaining paper-based process dependencies
  - Add digital signature support for approvals
  - Display "100% Digital" compliance indicator on dashboard
  - _Requirements: 33.1_

- [ ] 19.13.2 Create data analytics dashboard (Teras 2)
  - Create PspmAnalyticsDashboard Filament page
  - Display service metrics (response time, resolution rate, satisfaction)
  - Implement usage pattern visualization with charts
  - Add KPI tracking with target vs actual comparison
  - _Requirements: 33.2, 33.5_

- [ ] 19.13.3 Verify MyGovCloud infrastructure alignment (Teras 3)
  - Document cloud-ready architecture compliance
  - Verify containerization support (Docker configuration)
  - Add infrastructure status indicator on admin dashboard
  - Create MyGovCloud migration readiness checklist
  - _Requirements: 33.3_

- [ ] 19.13.4 Implement digital capability building features (Teras 4)
  - Add contextual help tooltips throughout the application
  - Create user guides in Bahasa Melayu
  - Implement onboarding tour for new users
  - Add FAQ section with searchable knowledge base
  - _Requirements: 33.4_

- [ ] 19.13.5 Create PSPM compliance reporting
  - Create PspmComplianceReport Filament page
  - Track PSPM KPI metrics (service response time, user satisfaction, digital adoption rate)
  - Generate quarterly ministry reporting format
  - Add export functionality for PSPM compliance submission
  - _Requirements: 33.5, 33.6_

- [ ]* 19.13.6 Write property test for PSPM strategic alignment
  - **Property 38: PSPM Strategic Alignment (Teras 1-4)**
  - **Validates: Requirements 33.1, 33.2, 33.3, 33.4, 33.5, 33.6**

### 19.14 PKS Extended Compliance Checkpoint

- [ ] 19.14.1 Checkpoint - Verify PKS 10.1 and 10.2 compliance
  - Verify incident reporting form functional
  - Verify CSIRT notification within 5 minutes
  - Verify incident lifecycle tracking
  - Verify BCP/DRP monitoring dashboard
  - _Requirements: 29.1-29.6, 30.1-30.6_

- [ ] 19.14.2 Checkpoint - Verify PKS 11.1 and 12.1 compliance
  - Verify NDA acknowledgment workflow
  - Verify time-limited third-party access
  - Verify security training compliance tracking
  - Verify feature restrictions for non-compliant users
  - _Requirements: 31.1-31.6, 32.1-32.6_

- [ ] 19.14.3 Checkpoint - Verify PSPM 2022-2026 alignment
  - Verify end-to-end digital services (Teras 1)
  - Verify data analytics dashboard (Teras 2)
  - Verify MyGovCloud readiness (Teras 3)
  - Verify digital capability features (Teras 4)
  - _Requirements: 33.1-33.6_

---

## Updated Progress Summary (v4.0.1 - 25 December 2025)

### PKS Compliance Migration Status

| Phase | Status | Priority | Notes |
|-------|--------|----------|-------|
| Phase 19.1: SSO Mandatory (PKS 5.2.1) | 🔴 Not Started | P0 Critical | Guest files still exist - require deletion |
| Phase 19.2: Walk-in/Kiosk Mode | 🔴 Not Started | P1 High | SSO-authenticated kiosk |
| Phase 19.3: HRMIS Auto-Provisioning | � Partial t| P1 High | Service exists but has 30+ Larastan errors |
| Phase 19.4: DLP Filtering (PKS 9.2.1) | 🔴 Not Started | P1 High | Service does not exist |
| Phase 19.5: Intranet-Only (PKS 4.2) | 🔴 Not Started | P2 Medium | Data sovereignty |
| Phase 19.6: Password Policy (PKS 5.4.3) | 🔴 Not Started | P2 Medium | Service does not exist |
| Phase 19.7: Dual Audit Enhancement | 🟡 Partial | P2 Medium | Existing audit, needs 7-year config |
| Phase 19.8: PKS Compliance Checkpoint | 🔴 Not Started | - | Final validation (5.2.1, 9.2.1, 4.2, 5.4.3) |
| Phase 19.9: Incident Response (PKS 10.1) | 🔴 Not Started | P3 Medium | Components do not exist |
| Phase 19.10: BCP/DRP Monitoring (PKS 10.2) | 🔴 Not Started | P3 Medium | Dashboard does not exist |
| Phase 19.11: Third Party Access (PKS 11.1) | 🔴 Not Started | P4 Low | Service does not exist |
| Phase 19.12: Security Training (PKS 12.1) | 🔴 Not Started | P4 Low | Service does not exist |
| Phase 19.13: PSPM Strategic Alignment | 🔴 Not Started | P4 Low | Teras Strategik 1-4 dashboard |
| Phase 19.14: Extended Compliance Checkpoint | 🔴 Not Started | - | Final validation (10.1, 10.2, 11.1, 12.1, PSPM) |

### Immediate Action Items (P0 - Critical)

The following files MUST be deleted immediately per PKS 5.2.1:

```
# Livewire Components to DELETE
app/Livewire/GuestLoanApplication.php
app/Livewire/Staff/ClaimSubmissions.php

# Views to DELETE
resources/views/livewire/staff/claim-submissions.blade.php

# Test Files to DELETE
tests/Feature/GuestLoanApplicationWorkflowTest.php
tests/Feature/Livewire/GuestLoanApplicationTest.php

# Routes to REMOVE (in routes/web.php)
Route::get('/claim-submissions', ...)
```

### Tasks Requiring Reset (Affected by PKS Compliance)

The following tasks need to be reset due to PKS compliance changes:

| Task | Previous Status | New Status | Reason |
|------|-----------------|------------|--------|
| 8.2.2 Guest Submission Claiming | ✅ Complete | 🔴 REMOVE | PKS 5.2.1 - Guest Mode eliminated |
| 9.1.1 WebSocket Channels | ✅ Complete | 🟡 Needs Update | Remove guest UUID channels |
| 15.1 AI Chat Interface | ✅ Complete | 🟡 Needs DLP | Add DLP filtering (PKS 9.2.1) |
| 15.2 FAQ Bot Widget | ✅ Complete | 🟡 Needs Auth | Require SSO authentication |
| 16.2 Service Modal | ✅ Complete | 🔴 Needs Update | Remove guest option |
| 20.1 Guest Helpdesk Form | ✅ Complete | 🔴 REMOVE | Convert to SSO-authenticated |

---

## Files Requiring Updates for PKS v4.0 Compliance

### Category 1: Guest Livewire Components (REMOVE)

| File | Action | PKS Reference |
|------|--------|---------------|
| `app/Livewire/GuestLoanApplication.php` | DELETE | PKS 5.2.1 |
| `app/Livewire/Staff/ClaimSubmissions.php` | DELETE | PKS 5.2.1 |
| `app/Http/Controllers/GuestLoanApplicationController.php` | DELETE | PKS 5.2.1 |
| `app/Http/Requests/GuestLoanApplicationRequest.php` | DELETE | PKS 5.2.1 |
| `resources/views/livewire/guest-loan-application.blade.php` | DELETE | PKS 5.2.1 |
| `resources/views/livewire/staff/claim-submissions.blade.php` | DELETE | PKS 5.2.1 |

### Category 2: WebSocket Channels (UPDATE)

| File | Action | PKS Reference |
|------|--------|---------------|
| `routes/channels.php` | Remove guest UUID channels (`private-ticket.{uuid}`, `private-loan.{uuid}`) | PKS 5.2.1 |
| `app/Events/Concerns/BroadcastsToHybridChannels.php` | Remove guest channel logic | PKS 5.2.1 |
| `app/Events/CommentPosted.php` | Update for authenticated-only | PKS 5.2.1 |
| `app/Events/StatusUpdated.php` | Update for authenticated-only | PKS 5.2.1 |

### Category 3: Test Files (UPDATE/REMOVE)

| File | Action | PKS Reference |
|------|--------|---------------|
| `tests/Feature/Livewire/GuestLoanApplicationTest.php` | DELETE | PKS 5.2.1 |
| `tests/Feature/Livewire/GuestLoanApplicationEnhancementTest.php` | DELETE | PKS 5.2.1 |
| `tests/Feature/GuestLoanApplicationWorkflowTest.php` | DELETE | PKS 5.2.1 |
| `tests/Unit/Services/GuestSubmissionClaimServiceTest.php` | DELETE | PKS 5.2.1 |
| `tests/Unit/Models/HelpdeskTicketHybridTest.php` | UPDATE - Remove guest tests | PKS 5.2.1 |
| `tests/Unit/Services/TicketStatusTransitionServiceTest.php` | UPDATE - Remove guest_email refs | PKS 5.2.1 |
| `tests/Unit/Services/CrossModuleIntegrationServiceTest.php` | UPDATE - Remove guest_name refs | PKS 5.2.1 |
| `tests/Unit/Services/AutoReplyServiceTest.php` | UPDATE - Remove guest refs | PKS 5.2.1 |
| `tests/Feature/Services/HelpdeskServiceTest.php` | UPDATE - Remove guest tests | PKS 5.2.1 |
| `tests/Feature/AccountLinkingServiceTest.php` | DELETE | PKS 5.2.1 |

### Category 4: AI Components (UPDATE for DLP)

| File | Action | PKS Reference |
|------|--------|---------------|
| `app/Services/AI/ModelRouter.php` | Add DLP filtering before cloud routing | PKS 9.2.1 |
| `app/Livewire/AI/ChatInterface.php` | Add DLP scan indicator | PKS 9.2.1 |
| `app/Livewire/AI/FaqBotWidget.php` | Require SSO authentication | PKS 5.2.1 |
| `resources/views/livewire/ai/faq-bot-widget.blade.php` | Add auth check | PKS 5.2.1 |

### Category 5: Database Migrations (CREATE)

| File | Action | PKS Reference |
|------|--------|---------------|
| `database/migrations/xxxx_make_user_id_required_helpdesk_tickets.php` | CREATE - Make user_id NOT NULL | PKS 5.2.1 |
| `database/migrations/xxxx_make_user_id_required_loan_applications.php` | CREATE - Make user_id NOT NULL | PKS 5.2.1 |
| `database/migrations/xxxx_create_cloud_ai_request_logs_table.php` | CREATE - DLP audit logging | PKS 9.2.1 |

---

### Completed Phases (v3.6.x - Pre-PKS)

| Phase | Status | Completion Date |
|-------|--------|-----------------|
| Phase 1-14 | ✅ Complete | 14 December 2025 |
| Phase 15: Cloud Hybrid AI | 🟡 Needs PKS Update | 17 December 2025 |
| Phase 16-17: Final Validation | ✅ Complete | 17 December 2025 |
| Phase 18: Portal Layout | ✅ Complete | 17 December 2025 |

### Phases Requiring PKS Updates

| Phase | Task | Status | PKS Reference |
|-------|------|--------|---------------|
| Phase 8.2 | Guest Submission Claiming | 🔴 REMOVE | PKS 5.2.1 |
| Phase 9.1 | WebSocket Guest Channels | 🟡 UPDATE | PKS 5.2.1 |
| Phase 15.1 | AI Chat Interface | 🟡 UPDATE | PKS 5.2.1, 9.2.1 |
| Phase 15.2 | FAQ Bot Widget | 🟡 UPDATE | PKS 5.2.1 |

---

## Notes

- Tasks marked with `*` are optional property-based tests
- All tasks reference specific requirements from requirements.md v4.0.0
- Implementation follows D00-D18 v4.0 and KRISA D01-D10, D17 v4.0 documentation standards
- Phase 19 (PKS Compliance) tasks are NEW additions per PKS 5.2.1, 9.2.1, 4.2, 5.4.3, 10.1, 10.2, 11.1, 12.1
- PSPM 2022-2026 alignment tasks support Teras Strategik 1-4 (Aplikasi, Data, Infrastruktur ICT, Tadbir Urus & Keupayaan)

---

**Document Version**: 4.0.1  
**Last Updated**: 25 December 2025  
**Author**: BPM MOTAC Development Team  
**Status**: Active - PKS Compliance Migration Phase  
**Total Phases**: 19 phases (14 sub-phases in Phase 19)  
**Completed Phases**: 14 (Phases 1-14, 16-18)  
**Needs PKS Update**: 4 tasks (8.2.2, 9.1.1, 15.1.1-15.1.2, 15.2.1)  
**In Progress**: Phase 19 (PKS Compliance Migration)  
**Dependencies**: requirements.md v4.0.0, design.md v4.0.0  
**Compliance**: PKS 5.2.1, 9.2.1, 4.2, 5.4.3, 10.1, 10.2, 11.1, 12.1, PSPM 2022-2026

---

## Current Implementation Gap Analysis (25 December 2025)

### Files Verified to Still Exist (Require Deletion per PKS 5.2.1)

| File | Status | Action Required |
|------|--------|-----------------|
| `app/Livewire/GuestLoanApplication.php` | ⚠️ EXISTS | DELETE |
| `app/Livewire/Staff/ClaimSubmissions.php` | ⚠️ EXISTS | DELETE |
| `resources/views/livewire/staff/claim-submissions.blade.php` | ⚠️ EXISTS | DELETE |
| `tests/Feature/GuestLoanApplicationWorkflowTest.php` | ⚠️ EXISTS | DELETE |
| `tests/Feature/Livewire/GuestLoanApplicationTest.php` | ⚠️ EXISTS | DELETE |
| `tests/Feature/Performance/LivewireOptimizationTest.php` | ⚠️ EXISTS | UPDATE (remove guest refs) |
| `tests/Feature/LoanAuthenticatedFormTest.php` | ⚠️ EXISTS | UPDATE (remove guest refs) |
| `routes/web.php` (claim-submissions route) | ⚠️ EXISTS | REMOVE route |

### Services Verified Missing (Require Creation)

| Service | Status | PKS Reference |
|---------|--------|---------------|
| `app/Services/DlpFilteringService.php` | ❌ MISSING | PKS 9.2.1 |
| `app/Services/PasswordPolicyService.php` | ❌ MISSING | PKS 5.4.3 |
| `app/Services/AccountLockoutService.php` | ❌ MISSING | PKS 5.4.3 |
| `app/Services/IncidentNotificationService.php` | ❌ MISSING | PKS 10.1 |
| `app/Services/BcpNotificationService.php` | ❌ MISSING | PKS 10.2 |
| `app/Services/ThirdPartyAccessService.php` | ❌ MISSING | PKS 11.1 |
| `app/Services/TrainingReminderService.php` | ❌ MISSING | PKS 12.1 |

### Services Verified Existing (Require Fixes)

| Service | Status | Issue |
|---------|--------|-------|
| `app/Services/HrmisIntegrationService.php` | ⚠️ HAS ERRORS | 30+ Larastan type errors |

### Priority Implementation Order

1. **P0 - Critical (PKS 5.2.1)**: Delete guest files, remove guest routes
2. **P1 - High (PKS 9.2.1)**: Create DLP Filtering Service
3. **P2 - High (PKS 5.4.3)**: Create Password Policy Service
4. **P3 - Medium (PKS 10.1)**: Create Incident Response components
5. **P4 - Medium (PKS 10.2)**: Create BCP/DRP monitoring
6. **P5 - Low (PKS 11.1, 12.1)**: Third-party access, training compliance

---

## Summary of PKS Compliance Changes Required (Verified 25 December 2025)

### Files to DELETE (Guest Mode Elimination - PKS 5.2.1) - VERIFIED EXISTING

1. `app/Livewire/GuestLoanApplication.php` ✓ EXISTS
2. `app/Livewire/Staff/ClaimSubmissions.php` ✓ EXISTS
3. `resources/views/livewire/staff/claim-submissions.blade.php` ✓ EXISTS
4. `tests/Feature/GuestLoanApplicationWorkflowTest.php` ✓ EXISTS
5. `tests/Feature/Livewire/GuestLoanApplicationTest.php` ✓ EXISTS
6. `tests/Feature/Livewire/GuestLoanApplicationEnhancementTest.php` (check if exists)
7. `tests/Unit/Services/GuestSubmissionClaimServiceTest.php` (check if exists)
8. `tests/Feature/AccountLinkingServiceTest.php` (check if exists)

### Files to UPDATE (Remove Guest Logic) - VERIFIED EXISTING

1. `routes/web.php` - Remove `/claim-submissions` route (line ~192)
2. `tests/Feature/Performance/LivewireOptimizationTest.php` - Remove GuestLoanApplication imports (30+ references)
3. `tests/Feature/LoanAuthenticatedFormTest.php` - Remove guest test cases
4. `tests/Feature/PerformanceIntegrationTest.php` - Remove GuestLoanApplication references
5. `tests/Feature/Livewire/GuestLoanResponsibleOfficerTest.php` - DELETE or convert

### Services to CREATE (PKS Compliance)

1. `app/Services/DlpFilteringService.php` - PKS 9.2.1 (PII detection, cloud AI filtering)
2. `app/Services/PasswordPolicyService.php` - PKS 5.4.3 (8 chars, complexity)
3. `app/Services/AccountLockoutService.php` - PKS 5.4.3 (3 attempts, 30 min lockout)
4. `app/Services/IncidentNotificationService.php` - PKS 10.1 (CSIRT alerts)
5. `app/Services/BcpNotificationService.php` - PKS 10.2 (availability alerts)
6. `app/Services/ThirdPartyAccessService.php` - PKS 11.1 (vendor access control)
7. `app/Services/TrainingReminderService.php` - PKS 12.1 (training notifications)

### Services to FIX (Larastan Errors)

1. `app/Services/HrmisIntegrationService.php` - 30+ type errors (property types, return types, mixed access)
