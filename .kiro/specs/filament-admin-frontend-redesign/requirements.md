# Requirements Document

## Introduction

This document specifies the requirements for redesigning the Filament admin frontend layout for ICTServe v3.6.1. The current Filament admin interface requires comprehensive frontend improvements to align with MyDS Design System v2025.2, WCAG 2.2 AA accessibility standards, and provide a consistent, professional user experience for admin and superuser roles.

This document also incorporates system recovery and optimization requirements, addressing critical health issues (32.1%), SLA violations, inactive AI services (Ollama and AWS Bedrock), UI/UX data fetching failures, and table/list page inconsistencies. The combined initiative aims to restore system health to >90%, ensure all services are operational, and standardize the table/list page UI/UX across all Filament resources.

## Glossary

- **Filament_Admin_Panel**: The administrative interface built with Filament v4.3.1 for system management
- **MyDS**: Malaysia Government Design System v2025.2 providing design tokens and components
- **WCAG_2.2_AA**: Web Content Accessibility Guidelines Level AA compliance standard
- **Admin_User**: User with 'admin' role having full system management access
- **Superuser**: User with 'superuser' role having elevated privileges beyond admin
- **Widget**: Dashboard component displaying metrics, charts, or quick actions
- **Resource**: Filament CRUD interface for managing database entities
- **Cluster**: Grouped collection of related Filament resources
- **Theme_System**: Dark/light mode toggle with persistent user preference
- **Navigation_Sidebar**: Collapsible left sidebar containing menu items and navigation
- **Dashboard_Layout**: Main admin dashboard page structure with widgets and metrics
- **Login_Page**: Filament admin authentication page restricted to admin/superuser only
- **System_Health**: Overall health score calculated from service availability, SLA compliance, and performance metrics
- **SLA_Violation**: A helpdesk ticket that has exceeded its Service Level Agreement response or resolution time
- **Ollama_Service**: Local AI service for natural language processing and chatbot functionality
- **Bedrock_Service**: AWS Bedrock cloud AI service for advanced AI capabilities
- **Health_Widget**: Dashboard widget displaying real-time system health metrics
- **Chart_Widget**: Filament widget displaying data visualizations (charts, graphs)
- **Ticket_System**: Helpdesk ticket management module for IT support requests
- **Auto_Escalation**: Automated process to escalate tickets based on priority or SLA breach
- **Knowledge_Base**: Repository of articles and solutions for common IT issues
- **Filament_Resource**: A Filament admin panel resource class that provides CRUD operations for a model
- **Table_Widget**: Filament table component displaying paginated data with columns, filters, and actions
- **Export_Action**: Filament action that exports table data to CSV, Excel, or PDF format
- **Translation_Key**: Laravel localization key used for internationalization (i18n)
- **Toggleable_Column**: Table column that can be shown/hidden by user preference
- **Canonical_Resource**: The primary/authoritative resource for a model (vs alias/compat versions)
- **Ollama_AI_Cluster**: Filament cluster grouping AI-related resources (Bedrock config, MessageLog, Documents, etc.)
- **BedrockModelConfig**: Configuration resource for AWS Bedrock AI model parameters
- **MessageLog**: Resource tracking AI conversation logs and performance metrics
- **Performance_Dashboard**: Dashboard page displaying AI service performance metrics
- **FileUpload_Component**: Filament form component for file uploads with drag-and-drop support
- **Empty_State**: UI pattern displayed when a table or list has no records
- **Translation_Key_Leakage**: Bug where raw translation keys (e.g., `ollama.bedrock.navigation_label`) appear in UI instead of translated strings
- **Actionable_Empty_State**: Empty state pattern that includes guidance and call-to-action buttons
- **AssetMaintenance**: Record tracking maintenance activities (routine, repair, upgrade, inspection) for assets
- **AssetMaintenanceResource**: Filament resource for managing asset maintenance records
- **Conditional_Field_Visibility**: Form pattern where fields appear/hide based on other field values

## Requirements

### Requirement 1: Admin Login Page Redesign

**User Story:** As an admin or superuser, I want a professional and accessible login page, so that I can securely access the admin panel with confidence.

#### Acceptance Criteria

1. WHEN an admin visits the Filament login page, THE System SHALL display a centered login form with MyDS-compliant styling
2. WHEN the login form is displayed, THE System SHALL include MOTAC branding (logo and colors) consistent with D12/D14 guidelines
3. WHEN form fields receive focus, THE System SHALL display a 3px outline with 2px offset using `--fr-primary` token
4. WHEN validation errors occur, THE System SHALL display error messages with 4.5:1 contrast ratio and danger color tokens
5. THE Login_Page SHALL restrict access to users with 'admin' or 'superuser' roles only
6. WHEN the page loads, THE System SHALL provide keyboard navigation support with logical tab order
7. THE Login_Page SHALL include "Remember me" checkbox and "Forgot password" link with proper ARIA labels

### Requirement 2: Dashboard Layout Structure

**User Story:** As an admin, I want a well-organized dashboard layout, so that I can quickly access key metrics and navigation.

#### Acceptance Criteria

1. WHEN an admin logs in, THE System SHALL display a dashboard with collapsible sidebar navigation
2. THE Dashboard_Layout SHALL use 12-column grid system (MyDS 12-8-4 responsive grid)
3. WHEN the sidebar is collapsed, THE System SHALL show icon-only navigation with tooltips
4. WHEN the sidebar is expanded, THE System SHALL display icons with text labels (256px width)
5. THE Dashboard_Layout SHALL include a sticky header with user menu, notifications, and theme toggle
6. WHEN widgets are displayed, THE System SHALL use MyDS shadow-card token for elevation
7. THE Dashboard_Layout SHALL maintain minimum 24px spacing between major sections (--space-6)
8. WHEN the viewport is below 1024px, THE System SHALL stack widgets vertically with full width

### Requirement 3: Widget Component Styling

**User Story:** As an admin, I want visually consistent widgets, so that I can easily scan dashboard information.

#### Acceptance Criteria

1. WHEN widgets are rendered, THE System SHALL apply MyDS color tokens (--bg-white, --txt-black-900)
2. WHEN widget headers are displayed, THE System SHALL use Poppins font at 20px (text-xl font-semibold)
3. WHEN widget content includes metrics, THE System SHALL display numbers at 32px (text-3xl) with appropriate color coding
4. THE Widget SHALL include proper ARIA labels for screen reader accessibility
5. WHEN widgets contain charts, THE System SHALL ensure 3:1 contrast ratio for graphical elements
6. WHEN widgets are interactive, THE System SHALL provide hover states with 10% darken effect
7. THE Widget SHALL use border-radius of 12px (--radius-l) for card styling
8. WHEN loading states occur, THE System SHALL display skeleton loaders with aria-busy="true"

### Requirement 4: Navigation Sidebar Enhancement

**User Story:** As an admin, I want clear and accessible navigation, so that I can efficiently move between admin sections.

#### Acceptance Criteria

1. THE Navigation_Sidebar SHALL display menu items with Heroicons (20px w-5 h-5)
2. WHEN a menu item is active, THE System SHALL highlight it with --bg-primary-50 background
3. WHEN menu items receive keyboard focus, THE System SHALL display focus indicator with 3px outline
4. THE Navigation_Sidebar SHALL support keyboard navigation (Tab, Enter, Arrow keys)
5. WHEN the sidebar contains nested items, THE System SHALL indicate expandable sections with chevron icons
6. THE Navigation_Sidebar SHALL persist collapse/expand state in user preferences
7. WHEN hovering over collapsed sidebar icons, THE System SHALL display tooltips with 200ms delay
8. THE Navigation_Sidebar SHALL include role-based menu filtering (admin vs superuser)

### Requirement 5: Filament Resource Pages Styling

**User Story:** As an admin, I want consistent styling across all resource management pages, so that I have a predictable interface.

#### Acceptance Criteria

1. WHEN resource tables are displayed, THE System SHALL use zebra striping with --bg-washed for alternate rows
2. WHEN table headers are rendered, THE System SHALL make them sticky with --bg-white background
3. WHEN action buttons are displayed, THE System SHALL use MyDS button tokens (--shadow-button)
4. THE Resource_Page SHALL ensure form inputs have 12px padding and 8px border-radius
5. WHEN validation errors occur, THE System SHALL display inline error messages with --txt-danger-600
6. THE Resource_Page SHALL support sortable columns with clear visual indicators
7. WHEN pagination is displayed, THE System SHALL use accessible pagination component with ARIA labels
8. THE Resource_Page SHALL maintain 44px minimum touch target size for mobile accessibility

### Requirement 6: Theme System Implementation

**User Story:** As an admin, I want to toggle between light and dark themes, so that I can work comfortably in different lighting conditions.

#### Acceptance Criteria

1. THE Theme_System SHALL provide a toggle button in the header with sun/moon icons
2. WHEN theme is toggled, THE System SHALL persist preference in user settings
3. WHEN dark mode is active, THE System SHALL apply inverted color tokens maintaining 4.5:1 contrast
4. THE Theme_System SHALL transition smoothly using --motion-easeout with 200ms duration
5. WHEN theme changes, THE System SHALL update all components without page reload
6. THE Theme_System SHALL respect system preference on first visit (prefers-color-scheme)
7. WHEN charts are displayed, THE System SHALL adapt colors for theme compatibility

### Requirement 7: Accessibility Compliance

**User Story:** As an admin with disabilities, I want full keyboard and screen reader support, so that I can use the admin panel independently.

#### Acceptance Criteria

1. THE Filament_Admin_Panel SHALL provide skip-to-content link for keyboard users
2. WHEN interactive elements are focused, THE System SHALL display visible focus indicators (3px outline, 2px offset)
3. THE Filament_Admin_Panel SHALL use semantic HTML with proper ARIA landmarks (banner, main, navigation, contentinfo)
4. WHEN forms are displayed, THE System SHALL associate labels with inputs using for/id attributes
5. THE Filament_Admin_Panel SHALL ensure all images have meaningful alt text or aria-hidden for decorative
6. WHEN modals are opened, THE System SHALL trap focus within modal and restore on close
7. THE Filament_Admin_Panel SHALL support screen reader announcements for dynamic content (aria-live regions)
8. WHEN color conveys information, THE System SHALL provide additional non-color indicators (icons, text)

### Requirement 8: Responsive Design

**User Story:** As an admin using a tablet, I want the admin panel to adapt to my screen size, so that I can manage the system on the go.

#### Acceptance Criteria

1. WHEN viewport is below 768px, THE System SHALL hide sidebar and show hamburger menu
2. WHEN viewport is between 768px-1023px, THE System SHALL use 8-column grid layout
3. WHEN viewport is 1024px or above, THE System SHALL use 12-column grid layout
4. THE Filament_Admin_Panel SHALL ensure touch targets are minimum 44x44px on mobile
5. WHEN tables are displayed on mobile, THE System SHALL convert to card view or horizontal scroll
6. THE Filament_Admin_Panel SHALL maintain readability with minimum 16px base font size
7. WHEN forms are displayed on mobile, THE System SHALL stack fields vertically with full width

### Requirement 9: Performance Optimization

**User Story:** As an admin, I want fast page loads and smooth interactions, so that I can work efficiently without delays.

#### Acceptance Criteria

1. THE Filament_Admin_Panel SHALL achieve Largest Contentful Paint (LCP) under 2.5 seconds
2. WHEN widgets load data, THE System SHALL use skeleton loaders to indicate loading state
3. THE Filament_Admin_Panel SHALL lazy load non-critical components below the fold
4. WHEN charts are rendered, THE System SHALL debounce resize events by 300ms
5. THE Filament_Admin_Panel SHALL minimize layout shifts (CLS < 0.1)
6. WHEN navigation occurs, THE System SHALL prefetch linked pages on hover
7. THE Filament_Admin_Panel SHALL cache static assets with appropriate cache headers

### Requirement 10: Component Library Consistency

**User Story:** As a developer, I want reusable Filament components, so that I can maintain consistency across the admin panel.

#### Acceptance Criteria

1. THE Filament_Admin_Panel SHALL use Filament v4.3.1 components as base
2. WHEN custom components are needed, THE System SHALL extend Filament components rather than replace
3. THE Component_Library SHALL document all custom components in resources/views/filament/components
4. WHEN styling components, THE System SHALL use Tailwind CSS v4 utility classes
5. THE Component_Library SHALL provide Blade component examples in documentation
6. WHEN components are updated, THE System SHALL maintain backward compatibility
7. THE Component_Library SHALL include accessibility annotations in component documentation

### Requirement 11: Widget Deduplication (D04 §3.2, SRS-ADM-003)

**User Story:** As an administrator, I want to see each dashboard widget only once, so that I can have a clean and efficient dashboard interface.

#### Acceptance Criteria

1. WHEN the admin dashboard loads, THE System SHALL display each widget type only once
2. WHEN duplicate widgets are detected, THE System SHALL remove the duplicates and keep only the intended instance
3. WHEN widgets are rendered, THE System SHALL validate that no widget class appears multiple times in the same section
4. THE System SHALL log any duplicate widget detection for debugging purposes
5. WHEN the dashboard is refreshed, THE System SHALL maintain the deduplicated state

### Requirement 12: Widget Organization and Categorization (D04 §3.2, SRS-UX-005)

**User Story:** As an administrator, I want widgets to be properly organized by type and importance, so that I can quickly access the most relevant information.

#### Acceptance Criteria

1. WHEN the dashboard loads, THE System SHALL display header widgets in the designated header section only
2. WHEN chart widgets are rendered, THE System SHALL display them in the charts section with proper grid layout
3. WHEN content widgets are displayed, THE System SHALL place them in the main content area
4. THE System SHALL respect the widget sort order defined in each widget class
5. WHEN widgets are categorized, THE System SHALL follow the MyDS 12-8-4 grid system for responsive layout

### Requirement 13: Widget Performance and Caching (D04 §3.2, SRS-RPT-006)

**User Story:** As an administrator, I want the dashboard to load quickly and efficiently, so that I can access information without delays.

#### Acceptance Criteria

1. WHEN widgets are loaded, THE System SHALL implement proper caching mechanisms for widget data
2. WHEN multiple widgets request similar data, THE System SHALL use shared caching to reduce database queries
3. WHEN widgets are rendered, THE System SHALL use lazy loading for non-critical widgets
4. THE System SHALL implement a maximum cache TTL of 300 seconds for real-time widgets
5. WHEN the dashboard is accessed, THE System SHALL load critical widgets first and defer others

### Requirement 14: Widget Configuration Management (SRS-UX-005, D12 §3.4)

**User Story:** As an administrator, I want to be able to configure which widgets are displayed, so that I can customize my dashboard experience.

#### Acceptance Criteria

1. WHEN an administrator accesses widget settings, THE System SHALL provide a configuration interface
2. WHEN widgets are enabled or disabled, THE System SHALL persist the configuration per user or globally
3. WHEN widget configuration changes, THE System SHALL apply changes without requiring a full page reload
4. THE System SHALL provide default widget configurations for different user roles
5. WHEN widgets are configured, THE System SHALL validate that essential widgets cannot be disabled

### Requirement 15: Widget Error Handling and Fallbacks (D04 §3.1 Service Layer)

**User Story:** As an administrator, I want the dashboard to remain functional even when individual widgets fail, so that I can continue working with the available information.

#### Acceptance Criteria

1. WHEN a widget fails to load, THE System SHALL display an error state without breaking the entire dashboard
2. WHEN widget data is unavailable, THE System SHALL show appropriate fallback content
3. WHEN widgets encounter errors, THE System SHALL log the errors for debugging
4. THE System SHALL provide retry mechanisms for failed widgets
5. WHEN critical widgets fail, THE System SHALL notify administrators of the issue

### Requirement 16: Real-time Widget Updates (D16 Laravel Reverb, SRS-ADM-003)

**User Story:** As an administrator, I want widgets to update in real-time, so that I always see the most current system information.

#### Acceptance Criteria

1. WHEN system data changes, THE System SHALL broadcast updates to relevant widgets via Laravel Reverb
2. WHEN widgets receive real-time updates, THE System SHALL update the display without full page refresh
3. WHEN multiple users are viewing the dashboard, THE System SHALL synchronize widget updates across sessions
4. THE System SHALL implement proper rate limiting for real-time updates to prevent performance issues
5. WHEN real-time connections are lost, THE System SHALL gracefully fall back to periodic polling

### Requirement 17: Role-Based Widget Access (SRS-ADM-002, D04 §3.1)

**User Story:** As a system administrator, I want widgets to display appropriate content based on user roles, so that users only see relevant information for their responsibilities.

#### Acceptance Criteria

1. WHEN an admin user accesses the dashboard, THE System SHALL display operational widgets (ticket stats, loan stats, SLA compliance)
2. WHEN a superuser accesses the dashboard, THE System SHALL display all widgets including system configuration and audit widgets
3. WHEN role permissions change, THE System SHALL update widget visibility without requiring logout
4. THE System SHALL hide sensitive widgets from users without appropriate permissions
5. WHEN widgets are role-restricted, THE System SHALL provide clear messaging about access requirements

---

## System Recovery and Optimization Requirements

### Requirement 18: SLA Violation Recovery

**User Story:** As an IT administrator, I want to identify and resolve SLA-breached tickets, so that I can restore compliance and improve service quality.

#### Acceptance Criteria

1. WHEN the system detects SLA-breached tickets, THE Ticket_System SHALL query and display all tickets with SLA_Breached status
2. WHEN an SLA violation is detected, THE System SHALL automatically escalate the ticket to 'Urgent' priority
3. WHEN a ticket is escalated, THE System SHALL notify the assigned ADMIN or Technician role via the notification provider
4. WHEN common low-level tickets are identified (e.g., "Microsoft Office activation", "Keyboard replacement"), THE System SHALL suggest or auto-link relevant Knowledge_Base articles
5. THE System SHALL provide a dashboard view showing current SLA violation count and resolution progress
6. WHEN all SLA violations are resolved, THE System SHALL update the SLA compliance metric to reflect 0 violations

### Requirement 19: Ollama Local AI Service Restoration

**User Story:** As a system administrator, I want the Ollama AI service to be operational, so that users can access local AI-powered features.

#### Acceptance Criteria

1. WHEN the Ollama service status is checked, THE System SHALL verify the service is running on the configured host
2. WHEN the Ollama API endpoint is configured, THE System SHALL validate the OLLAMA_HOST or OLLAMA_API_URL environment variable
3. WHEN the Ollama service is unreachable, THE System SHALL attempt a connection retry with exponential backoff
4. WHEN the Ollama health check succeeds, THE System SHALL display "Aktif" (Active) status with a green indicator
5. IF the Ollama service fails health check, THEN THE System SHALL display "Tidak Aktif" (Inactive) status with a red indicator and log the error
6. THE System SHALL provide a manual service restart option in the admin dashboard

### Requirement 20: AWS Bedrock Cloud AI Service Restoration

**User Story:** As a system administrator, I want the AWS Bedrock AI service to be operational, so that users can access cloud AI-powered features.

#### Acceptance Criteria

1. WHEN the Bedrock service status is checked, THE System SHALL validate AWS credentials (AWS_ACCESS_KEY_ID, AWS_SECRET_ACCESS_KEY, AWS_REGION)
2. WHEN AWS credentials are invalid or missing, THE System SHALL display a clear error message indicating the configuration issue
3. WHEN a 403 (Permission) error occurs, THE System SHALL log the error and display "Kebenaran Ditolak" (Permission Denied) status
4. WHEN a 429 (Throttling) error occurs, THE System SHALL implement retry logic with exponential backoff
5. WHEN the Bedrock health check succeeds, THE System SHALL display "Aktif" (Active) status with a green indicator
6. IF the Bedrock service fails health check, THEN THE System SHALL display "Tidak Aktif" (Inactive) status with appropriate error details

### Requirement 21: System Health Score Calculation

**User Story:** As an administrator, I want accurate real-time system health metrics, so that I can monitor and respond to issues promptly.

#### Acceptance Criteria

1. THE Health_Widget SHALL calculate overall health score based on: service availability, SLA compliance, and performance metrics
2. WHEN any service status changes, THE System SHALL immediately recalculate and update the health score
3. THE System SHALL NOT cache health scores for more than 30 seconds
4. WHEN health score drops below 50%, THE System SHALL display a critical alert with danger color pulse
5. WHEN health score is between 50-80%, THE System SHALL display a warning indicator
6. WHEN health score is above 80%, THE System SHALL display a healthy indicator with green color
7. THE Health_Widget SHALL display individual component health scores (Ollama, Bedrock, Database, Queue)

### Requirement 22: Chart Widget Data Fetching

**User Story:** As an administrator, I want dashboard charts to display data correctly, so that I can visualize system metrics and trends.

#### Acceptance Criteria

1. WHEN a Chart_Widget loads, THE System SHALL fetch data using the appropriate getData() method
2. WHEN data fetching fails, THE System SHALL display a user-friendly error message instead of an empty white box
3. WHEN no data is available, THE System SHALL display a "Tiada Data" (No Data) placeholder with appropriate styling
4. THE Chart_Widget SHALL handle Livewire lifecycle events (mount(), getData()) without failures
5. WHEN JavaScript errors occur in chart rendering, THE System SHALL log the error and display a fallback message
6. THE System SHALL ensure all 6 charts in the "Carta & Analitik" section render correctly

### Requirement 23: Cache Management

**User Story:** As a developer, I want to clear Filament-specific caches, so that I can ensure fresh data is displayed after updates.

#### Acceptance Criteria

1. THE System SHALL provide a command to clear Filament-specific view caches
2. WHEN cache is cleared, THE System SHALL regenerate necessary cached assets
3. THE System SHALL support running `php artisan filament:optimize` for cache management
4. WHEN configuration changes are made, THE System SHALL prompt or automatically clear relevant caches

### Requirement 24: Error Logging and Diagnostics

**User Story:** As a system administrator, I want comprehensive error logging, so that I can diagnose and resolve issues efficiently.

#### Acceptance Criteria

1. WHEN AI service errors occur, THE System SHALL log detailed error information to laravel.log
2. THE System SHALL include timestamps, error codes, and stack traces in log entries
3. WHEN database or API timeout errors occur, THE System SHALL log the specific endpoint and timeout duration
4. THE System SHALL provide a log viewer in the admin dashboard for recent errors
5. WHEN critical errors occur, THE System SHALL send notifications to configured administrators

### Requirement 25: Eliminate Duplicate LoanApplicationResource

**User Story:** As an administrator, I want a single consistent Loan Application list page, so that I can manage loan applications without confusion from duplicate navigation entries.

#### Acceptance Criteria

1. THE System SHALL have only ONE LoanApplicationResource visible in navigation
2. WHEN the canonical resource is identified (per D04 documentation), THE System SHALL disable navigation on the alias/compat resource
3. THE canonical resource (`LoanApplications/LoanApplicationResource.php`) SHALL be the only resource rendering the Permohonan Pinjaman list
4. WHEN the alias resource (`Loans/LoanApplicationResource.php`) is accessed directly via URL, THE System SHALL redirect to the canonical resource
5. THE KPI header SHALL render consistently on the Permohonan Pinjaman list page
6. THE System SHALL NOT display duplicate export buttons due to multiple resource registrations

### Requirement 26: Fix Export Action Translation Keys

**User Story:** As an administrator, I want all export buttons to display proper Malay labels, so that I can understand the export options without seeing raw translation keys.

#### Acceptance Criteria

1. WHEN an export action is displayed, THE System SHALL show a translated Malay label (not raw key like `filament.actions.export_data`)
2. THE System SHALL provide translation keys for all export actions:
   - `Eksport Excel` for Excel export
   - `Eksport PDF` for PDF export
   - `Eksport Laporan` for report export
   - `Eksport Data` for generic data export
3. WHEN a translation key is missing, THE System SHALL fall back to a hardcoded Malay label
4. THE System SHALL NOT expose raw translation keys to end users in any table action

### Requirement 27: Consolidate Export Actions

**User Story:** As an administrator, I want a single, clear export entry point per list page, so that I can export data without confusion from multiple export buttons.

#### Acceptance Criteria

1. EACH list page SHALL have at most ONE export entry point
2. WHEN multiple export formats are available, THE System SHALL use a dropdown menu with format options
3. THE export action SHALL respect user permissions (admin and superuser roles per D03 SRS-ADM-005)
4. WHEN a user without export permission views the table, THE System SHALL NOT display export actions
5. THE export action labels SHALL be in Bahasa Melayu

### Requirement 28: Fix HelpdeskTicketsTable Horizontal Scroll

**User Story:** As a helpdesk operator, I want to view the ticket list without horizontal scrolling, so that I can triage tickets efficiently on standard desktop viewports.

#### Acceptance Criteria

1. THE HelpdeskTicketsTable SHALL NOT require horizontal scroll on viewports 1280px or wider
2. THE subject column SHALL truncate long text with a tooltip showing full content
3. THE System SHALL make non-critical columns toggleable with default hidden state:
   - `relatedAsset.name`
   - `assignedUser.name`
   - `created_at`
4. THE System SHALL keep key operational columns visible by default:
   - `ticket_number`
   - `subject`
   - `priority`
   - `status`
   - `sla_status`
5. WHEN a user toggles column visibility, THE System SHALL persist the preference

### Requirement 29: Fix TicketCategoriesTable Horizontal Scroll

**User Story:** As an administrator, I want to view the ticket category list without horizontal scrolling, so that I can manage categories efficiently.

#### Acceptance Criteria

1. THE TicketCategoriesTable SHALL NOT require horizontal scroll on viewports 1280px or wider
2. THE System SHALL make non-critical columns toggleable with default hidden state:
   - `parent.name_ms`
   - `sla_resolution_hours`
3. THE System SHALL keep key columns visible by default:
   - `code`
   - `name`
   - `sla_response_hours`
   - `is_active`

### Requirement 30: Standardize Resource Labels (i18n Consistency)

**User Story:** As an administrator, I want all resource labels in consistent Bahasa Melayu, so that the interface is professional and easy to navigate.

#### Acceptance Criteria

1. THE System SHALL display all navigation labels in Bahasa Melayu
2. THE System SHALL use these standard labels:
   - "Helpdesk Ticket" → "Tiket Meja Bantuan"
   - "Ticket Category" → "Kategori Tiket"
   - "Loan Application" → "Permohonan Pinjaman"
   - "Asset" → "Aset"
   - "Asset Category" → "Kategori Aset"
3. THE breadcrumb labels SHALL match navigation labels
4. THE System SHALL NOT mix English and Malay labels in the same context
5. WHEN a translation is missing, THE System SHALL use a Malay fallback (not English)

### Requirement 31: Add Operational Filters to HelpdeskTicketsTable

**User Story:** As a helpdesk operator, I want quick-access filters for common triage scenarios, so that I can find relevant tickets faster.

#### Acceptance Criteria

1. THE HelpdeskTicketsTable SHALL provide these operational filters:
   - "Saya ditugaskan" (assigned to current user)
   - "SLA dilanggar" (SLA breached tickets)
   - "Keutamaan: Urgent" (urgent priority filter)
   - "Status: Baru / Dibuka" (new/open status filter)
2. WHEN filters are active, THE System SHALL display a badge count near the filter icon
3. THE filter labels SHALL be in Bahasa Melayu
4. WHEN a filter is applied, THE System SHALL update the table results immediately

### Requirement 32: Table UI Consistency

**User Story:** As an administrator, I want consistent table UI patterns across all resources, so that the admin panel feels cohesive and professional.

#### Acceptance Criteria

1. THE System SHALL use consistent column width patterns across similar data types
2. THE System SHALL use consistent action button placement (row actions on right)
3. THE System SHALL use consistent empty state messaging in Bahasa Melayu
4. THE System SHALL use consistent loading skeleton patterns
5. WHEN a table has no data, THE System SHALL display "Tiada data untuk dipaparkan"

### Requirement 33: Asset Lifecycle Report Page

**User Story:** As an administrator, I want a functional Asset Lifecycle Report page, so that I can view and export asset lifecycle data with filters.

#### Acceptance Criteria

1. WHEN the Asset Lifecycle Report page loads, THE System SHALL display a filter form with date range, asset category, status, location, and lifecycle stage options
2. WHEN the "Jana Laporan" button is clicked, THE System SHALL generate and display the report data in a table
3. THE System SHALL provide export actions for CSV, Excel, and PDF formats
4. WHEN no data matches the filters, THE System SHALL display "Tiada data untuk dipaparkan bagi pilihan penapis semasa"
5. WHEN an error occurs, THE System SHALL display "Ralat memuatkan laporan. Sila cuba lagi atau hubungi pentadbir."
6. THE System SHALL display summary KPI cards when report data is available

### Requirement 34: PDPA Dashboard Page

**User Story:** As an administrator, I want a functional PDPA Dashboard page, so that I can monitor data protection compliance.

#### Acceptance Criteria

1. WHEN the PDPA Dashboard page loads, THE System SHALL display header widgets via getHeaderWidgets()
2. THE DataRetentionAlertWidget SHALL display a check-circle icon when count is 0, and exclamation-triangle when count > 0
3. WHEN a non-superuser views the page, THE System SHALL display an access note about restricted sensitive access logs
4. THE SensitiveAccessLogWidget SHALL display "Terhad kepada Superuser" for non-superusers
5. ALL page text SHALL be in Bahasa Melayu

### Requirement 35: AssetsTable Filter Query Grouping

**User Story:** As an administrator, I want the "needs maintenance" filter to work correctly, so that I can find assets that actually need maintenance.

#### Acceptance Criteria

1. THE `needs_maintenance` filter SHALL use proper query grouping with parentheses
2. THE filter query SHALL be: `(status = 'maintenance' OR condition = 'damaged' OR (next_maintenance_date IS NOT NULL AND next_maintenance_date <= now()+30days))`
3. THE filter SHALL NOT return assets that only match the date condition without proper grouping
4. WHEN the filter is applied, THE System SHALL return only assets that genuinely need maintenance attention

### Requirement 36: Alias Resource URL Redirect

**User Story:** As an administrator, I want to be redirected to the canonical resource when accessing an alias URL, so that I always see the correct, maintained version of the page.

#### Acceptance Criteria

1. WHEN a user accesses the alias LoanApplicationResource URL directly (`/admin/loans/loan-applications`), THE System SHALL redirect to the canonical resource URL (`/admin/loan-applications`)
2. THE redirect SHALL use HTTP 301 (permanent redirect) status code
3. THE redirect SHALL preserve any query parameters from the original URL
4. THE System SHALL log alias URL access for monitoring purposes

### Requirement 37: AssetCategoriesTable Default Sort Validation

**User Story:** As an administrator, I want the asset categories table to sort correctly, so that I can view categories in the intended order.

#### Acceptance Criteria

1. THE AssetCategoriesTable SHALL use `sort_order` column for default sorting only if the column exists
2. IF the `sort_order` column does not exist in the database, THE System SHALL fall back to `name` column
3. THE System SHALL NOT throw database errors due to missing sort columns

### Requirement 38: Management Module i18n Standardization

**User Story:** As an administrator, I want the Management module (Users, Divisions, Grades) to display consistent Bahasa Melayu labels, so that the interface is professional and easy to navigate.

#### Acceptance Criteria

1. THE System SHALL display these standard Malay labels for Management resources:
   - "Division" → "Bahagian"
   - "Grade" → "Gred"
   - "User" → "Pengguna"
   - "Management" (cluster) → "Pengurusan"
2. THE breadcrumb labels SHALL match navigation labels (e.g., "Pengurusan > Bahagian > Senarai")
3. THE System SHALL NOT mix English and Malay labels in the Management module
4. THE page titles SHALL be in Bahasa Melayu (e.g., "Senarai Bahagian" not "Division", "Cipta Pengguna" not "Cipta User")
5. EACH Management resource (UserResource, DivisionResource, GradeResource) SHALL override:
   - `getNavigationLabel()` returning Malay label
   - `getModelLabel()` returning Malay singular label
   - `getPluralModelLabel()` returning Malay plural label

### Requirement 39: Management Tables Horizontal Scroll Fix

**User Story:** As an administrator, I want to view User, Division and Grade tables without horizontal scrolling, so that I can manage reference data efficiently.

#### Acceptance Criteria

1. THE DivisionsTable SHALL NOT require horizontal scroll on viewports 1280px or wider
2. THE GradesTable SHALL NOT require horizontal scroll on viewports 1280px or wider
3. THE UsersTable SHALL NOT require horizontal scroll on viewports 1280px or wider
4. THE System SHALL make non-critical columns toggleable with default hidden state:
   - DivisionsTable: `parent.name_ms` (Induk)
   - UsersTable: `staff_id`, `division.name_ms`, `grade.name_ms`, `position`, `phone`
5. THE System SHALL keep key columns visible by default:
   - DivisionsTable: `code`, `name_ms`, `is_active`
   - GradesTable: `code`, `name_ms`, `level`, `can_approve_loans`
   - UsersTable: `name`, `email`, `roles`, `status`
6. THE name columns SHALL truncate long text with tooltip showing full content

### Requirement 40: Standardize Create Form Action Labels

**User Story:** As an administrator, I want consistent action button labels across all create forms, so that the interface is predictable and professional.

#### Acceptance Criteria

1. THE System SHALL use "Simpan & Tambah Lagi" instead of "Cipta & cipta yang lain" for the create-and-continue action
2. THE System SHALL use consistent action labels across all Filament create forms:
   - Primary save: "Simpan" or "Cipta"
   - Save and continue: "Simpan & Tambah Lagi"
   - Cancel: "Batal"
3. THE action labels SHALL be in proper Bahasa Melayu grammar

### Requirement 41: Boolean Column Accessibility

**User Story:** As an administrator using assistive technology, I want boolean columns to have proper accessibility labels, so that I can understand the data without relying on color alone.

#### Acceptance Criteria

1. THE boolean IconColumn components SHALL include aria-label attributes ("Ya" / "Tidak")
2. THE boolean columns SHALL provide tooltip text on hover
3. THE System SHALL NOT rely solely on color to convey boolean state (icon + color pattern)
4. WHEN a screen reader reads a boolean column, THE System SHALL announce "Ya" or "Tidak"

### Requirement 42: Impersonation Action Role Check Consistency

**User Story:** As a superuser, I want the impersonation action to work correctly, so that I can troubleshoot user issues by viewing the system as that user.

#### Acceptance Criteria

1. THE impersonation action SHALL use the canonical role slug `superuser` (not `Super Admin`)
2. THE impersonation action SHALL only be visible to users with `superuser` role
3. THE impersonation action SHALL NOT be visible when viewing the current user's own record
4. THE impersonation action label SHALL be in Bahasa Melayu ("Lakon Sebagai")
5. THE impersonation action SHALL require confirmation before proceeding
6. THE System SHALL log impersonation events for audit purposes

### Requirement 43: CreateUser Notification Localization

**User Story:** As an administrator, I want user creation notifications in Bahasa Melayu, so that the interface is consistent with the rest of the admin panel.

#### Acceptance Criteria

1. THE CreateUser success notification title SHALL be in Bahasa Melayu ("Pengguna berjaya dicipta")
2. THE welcome email notification body SHALL be in Bahasa Melayu ("Emel alu-aluan telah dihantar ke :email")
3. THE System SHALL NOT display English notification strings in the admin panel
4. THE notification strings SHALL use translation keys or hardcoded Malay fallbacks

### Requirement 44: UsersTable Additional Column Visibility

**User Story:** As an administrator, I want the Users table to show only essential columns by default, so that I can scan user data efficiently without horizontal scrolling.

#### Acceptance Criteria

1. THE UsersTable SHALL hide these columns by default (toggleable):
   - `staff_id`
   - `division.name_ms`
   - `grade.name_ms`
   - `last_login_at`
   - `created_at`
   - `updated_at`
2. THE UsersTable SHALL keep these columns visible by default:
   - `name` (with truncation and tooltip)
   - `email` (with truncation and tooltip)
   - `role` (with badge styling)
   - `is_active` (boolean icon)
3. THE name and email columns SHALL truncate at 35 characters with tooltip showing full value
4. THE UsersTable SHALL NOT require horizontal scroll on viewports 1280px or wider

---

## Ollama AI Module Requirements (Phase 28)

### Requirement 45: Ollama AI Module Translation Key Completeness

**User Story:** As an administrator, I want all Ollama AI module labels and strings in Bahasa Melayu, so that the interface is consistent with the rest of the admin panel.

#### Acceptance Criteria

1. THE System SHALL NOT display raw translation keys (e.g., `ollama.bedrock.navigation_label`) in the UI
2. THE System SHALL provide Malay translations for all `ollama.*` and `ollama.bedrock.*` keys
3. THE navigation labels SHALL be in Bahasa Melayu:
   - "Ollama AI" cluster label
   - "Konfigurasi Model Bedrock" (BedrockModelConfig)
   - "Log Mesej" (MessageLog)
   - "Dokumen" (Document)
   - "Templat" (Template)
   - "FAQ" (FAQ)
   - "Prestasi" (Performance)
4. THE table column headers SHALL be in Bahasa Melayu (not ALL CAPS English)
5. THE form section headers SHALL NOT display `section_*` translation keys
6. THE System SHALL include an automated test to detect raw translation key leakage

### Requirement 46: Ollama AI Navigation Information Architecture

**User Story:** As an administrator, I want the Ollama AI navigation to be logically organized, so that I can find AI-related features efficiently.

#### Acceptance Criteria

1. THE Ollama AI cluster SHALL group related resources logically:
   - Configuration: BedrockModelConfig
   - Content: Document, Template, FAQ
   - Monitoring: MessageLog, Performance Dashboard
2. THE navigation items SHALL NOT display raw translation keys
3. THE navigation order SHALL be consistent and logical (Configuration → Content → Monitoring)
4. THE cluster breadcrumb SHALL display "Ollama AI" (not raw key)

### Requirement 47: Ollama AI Tables Horizontal Scroll Fix

**User Story:** As an administrator, I want Ollama AI tables to fit within the viewport, so that I can view data without horizontal scrolling.

#### Acceptance Criteria

1. THE MessageLogResource table SHALL NOT require horizontal scroll at 1280px viewport
2. THE BedrockModelConfigResource table SHALL NOT require horizontal scroll at 1280px viewport
3. THE MessageLogResource table SHALL hide these columns by default (toggleable):
   - `sanitized_input`
   - `response_summary`
   - `created_at`
4. THE BedrockModelConfigResource table SHALL hide non-critical columns by default
5. THE tables SHALL use `->limit(N)->tooltip()` for long text columns
6. THE table column headers SHALL be in Bahasa Melayu (not ALL CAPS)

### Requirement 48: FileUpload Component Malay Localization

**User Story:** As an administrator, I want file upload components to display Malay instructions, so that the interface is consistent with the rest of the admin panel.

#### Acceptance Criteria

1. THE FileUpload component SHALL display "Seret & Lepas fail atau Klik untuk pilih" (not "Drag & Drop your files or Browse")
2. THE file size limit message SHALL be in Bahasa Melayu
3. THE accepted file types message SHALL be in Bahasa Melayu
4. THE upload progress indicator SHALL use Malay labels
5. THE error messages SHALL be in Bahasa Melayu

### Requirement 49: Performance Dashboard "No Data" Semantics

**User Story:** As an administrator, I want the Performance dashboard to clearly indicate when no data exists, so that I don't confuse "no data" with "0ms response time".

#### Acceptance Criteria

1. WHEN sample count = 0, THE System SHALL display "Tiada data" (not "0ms")
2. THE dashboard SHALL display the time period context (e.g., "Tempoh: 24 jam terakhir")
3. THE dashboard SHALL display the last update timestamp ("Kemaskini terakhir: HH:MM")
4. THE metrics SHALL clearly distinguish between "0" (actual zero) and "no data available"
5. THE empty state SHALL provide guidance on how to generate data

### Requirement 50: Actionable Empty States for AI Module

**User Story:** As an administrator, I want empty states to provide actionable guidance, so that I know how to populate the data.

#### Acceptance Criteria

1. THE empty state message SHALL NOT be generic "Tiada rekod dijumpai"
2. THE empty state SHALL include actionable guidance:
   - "Tiada rekod dijumpai. Klik 'Cipta Dokumen' untuk menambah rekod baharu."
   - "Tiada log mesej. Log akan dipaparkan selepas pengguna berinteraksi dengan AI."
3. THE empty state SHALL include a call-to-action button where appropriate
4. THE empty state icon SHALL be contextually relevant

### Requirement 51: MessageLogResource Table Column Visibility

**User Story:** As an administrator, I want the MessageLog table to show only essential columns by default, so that I can scan log data efficiently.

#### Acceptance Criteria

1. THE MessageLogResource table SHALL keep these columns visible by default:
   - `user.name` (with truncation and tooltip)
   - `model` (Bedrock model name)
   - `status` (success/error badge)
   - `response_time_ms` (formatted)
   - `created_at` (relative time)
2. THE MessageLogResource table SHALL hide these columns by default (toggleable):
   - `sanitized_input` (long text)
   - `response_summary` (long text)
   - `token_count`
   - `cost_estimate`
3. THE column headers SHALL be in Bahasa Melayu

### Requirement 52: BedrockModelConfigResource Table Column Visibility

**User Story:** As an administrator, I want the BedrockModelConfig table to show only essential columns by default, so that I can manage model configurations efficiently.

#### Acceptance Criteria

1. THE BedrockModelConfigResource table SHALL keep these columns visible by default:
   - `name` (model display name)
   - `model_id` (Bedrock model ID)
   - `is_active` (boolean icon)
   - `max_tokens` (numeric)
2. THE BedrockModelConfigResource table SHALL hide these columns by default (toggleable):
   - `temperature`
   - `top_p`
   - `description`
   - `created_at`
   - `updated_at`
3. THE column headers SHALL be in Bahasa Melayu

### Requirement 53: Global "Create Another" Action Label Standardization

**User Story:** As an administrator, I want consistent action labels across all create forms, so that the interface is predictable and professional.

#### Acceptance Criteria

1. THE "create and continue" action label SHALL be "Simpan & Tambah Lagi" globally
2. THE System SHALL override Filament's default `createAnother` translation key
3. THE label SHALL NOT be "Cipta & cipta yang lain" anywhere in the admin panel
4. THE translation SHALL be applied via `lang/vendor/filament-panels/ms/resources/pages/create-record.php`

---

## Asset Maintenance Module Requirements (Phase 29)

### Requirement 54: AssetMaintenanceResource Form Schema Implementation

**User Story:** As an administrator, I want a complete form for creating/editing asset maintenance records, so that I can track maintenance activities for assets.

#### Acceptance Criteria

1. THE AssetMaintenanceForm SHALL include these required fields:
   - `asset_id` (Select with relationship to Asset)
   - `maintenance_type` (Select: routine/repair/upgrade/inspection)
   - `status` (Select: scheduled/in_progress/completed/cancelled)
   - `scheduled_date` (DatePicker, required)
2. THE AssetMaintenanceForm SHALL include these optional fields:
   - `completed_date` (DatePicker, visible only when status = completed)
   - `cost` (TextInput, numeric, MYR)
   - `performed_by_user_id` (Select with relationship to User)
   - `performed_by` (TextInput for external vendor/individual)
   - `notes` (Textarea)
3. THE form SHALL use conditional visibility for `completed_date` field
4. THE form SHALL provide Malay labels and helper text for all fields
5. THE form SHALL NOT render as blank/empty (current broken state)

### Requirement 55: AssetMaintenanceResource Table Schema Implementation

**User Story:** As an administrator, I want a usable table for viewing asset maintenance records, so that I can track and manage maintenance activities.

#### Acceptance Criteria

1. THE AssetMaintenancesTable SHALL display these columns by default:
   - `asset.asset_tag` (Tag Aset)
   - `asset.name` (Nama Aset, with truncation and tooltip)
   - `maintenance_type` (Jenis, with badge and color coding)
   - `status` (Status, with badge and color coding)
   - `scheduled_date` (Tarikh Dijadualkan)
2. THE AssetMaintenancesTable SHALL hide these columns by default (toggleable):
   - `completed_date` (Tarikh Siap)
   - `performedByUser.name` (Staf)
   - `performed_by` (Vendor)
   - `cost` (Kos)
   - `created_at` (Dicipta)
3. THE table SHALL include filters for status and maintenance_type
4. THE table SHALL NOT render as empty shell (current broken state)
5. THE table SHALL use Malay labels for all columns and filters

### Requirement 56: AssetMaintenance Actionable Empty State

**User Story:** As an administrator, I want the empty state to provide guidance, so that I understand what this module is for and how to use it.

#### Acceptance Criteria

1. THE empty state SHALL NOT display generic "Tiada rekod dijumpai"
2. THE empty state SHALL display contextual message:
   - Heading: "Tiada rekod penyelenggaraan"
   - Description: "Klik 'Cipta' untuk merekod penyelenggaraan aset (contoh: servis berkala, pembaikan, pemeriksaan)."
3. THE empty state SHALL include a call-to-action button
4. THE empty state icon SHALL be contextually relevant (e.g., `heroicon-o-wrench-screwdriver`)

### Requirement 57: AssetMaintenanceResource Navigation and Labels

**User Story:** As an administrator, I want consistent Malay labels for the Asset Maintenance module, so that the interface is professional and consistent.

#### Acceptance Criteria

1. THE navigation label SHALL be "Penyelenggaraan Aset" (not English)
2. THE model label SHALL be "Penyelenggaraan" (singular)
3. THE plural model label SHALL be "Penyelenggaraan" (plural)
4. THE breadcrumb SHALL display "Penyelenggaraan Aset"
5. THE resource SHALL use consistent navigation grouping (consider moving to Inventory cluster)

### Requirement 58: AssetMaintenanceResource Eager Loading Fix

**User Story:** As a developer, I want correct eager loading configuration, so that the resource doesn't cause N+1 queries or errors.

#### Acceptance Criteria

1. THE `getEloquentQuery()` method SHALL eager load `asset` relationship
2. THE `getEloquentQuery()` method SHALL eager load `performedByUser` relationship (not `performedBy`)
3. THE eager loading SHALL match the actual model relationship names

---

## Asset Transfer (Pemindahan Aset) Module Requirements (Phase 30)

### Requirement 59: AssetTransferResource Form Schema Implementation

**User Story:** As an administrator, I want a complete form for creating/editing asset transfer records, so that I can track asset movements between users and locations.

#### Acceptance Criteria

1. THE AssetTransferForm SHALL include these required fields:
   - `asset_id` (Select with relationship to Asset)
   - `transfer_date` (DatePicker, required, default today)
   - `status` (Select: pending/approved/completed/cancelled, default 'pending')
   - `to_user_id` (Select with relationship to User)
   - `initiated_by` (Select, default current user, disabled)
2. THE AssetTransferForm SHALL include these optional fields:
   - `from_user_id` (Select with relationship to User)
   - `from_location` (TextInput)
   - `to_location` (TextInput)
   - `approved_by` (Select, visible only when status in ['approved', 'completed'])
   - `notes` (Textarea)
   - `cancellation_reason` (Textarea, visible and required only when status = 'cancelled')
3. THE form SHALL use conditional visibility for `approved_by` and `cancellation_reason` fields
4. THE form SHALL provide Malay labels and helper text for all fields
5. THE form SHALL NOT render as blank/empty (current broken state)

### Requirement 60: AssetTransferResource Table Schema Implementation

**User Story:** As an administrator, I want a usable table for viewing asset transfer records, so that I can track and manage asset movements.

#### Acceptance Criteria

1. THE AssetTransfersTable SHALL display these columns by default:
   - `asset.asset_tag` (Tag Aset)
   - `asset.name` (Nama Aset, with truncation and tooltip)
   - `toUser.name` (Kepada, with truncation and tooltip)
   - `status` (Status, with badge and color coding)
   - `transfer_date` (Tarikh)
2. THE AssetTransfersTable SHALL hide these columns by default (toggleable):
   - `fromUser.name` (Daripada)
   - `from_location` (Lokasi Asal)
   - `to_location` (Lokasi Baharu)
   - `initiator.name` (Dimulakan Oleh)
   - `approver.name` (Diluluskan Oleh)
   - `created_at` (Dicipta)
3. THE table SHALL include filters for status and to_user_id
4. THE table SHALL NOT render as empty shell (current broken state)
5. THE table SHALL use Malay labels for all columns and filters

### Requirement 61: AssetTransfer Actionable Empty State

**User Story:** As an administrator, I want the empty state to provide guidance, so that I understand what this module is for and how to use it.

#### Acceptance Criteria

1. THE empty state SHALL NOT display generic "Tiada rekod dijumpai"
2. THE empty state SHALL display contextual message:
   - Heading: "Tiada rekod pemindahan aset"
   - Description: "Klik 'Cipta' untuk merekod pemindahan aset antara bahagian."
3. THE empty state SHALL include a call-to-action button
4. THE empty state icon SHALL be contextually relevant (e.g., `heroicon-o-arrows-right-left`)

### Requirement 62: AssetTransferResource Navigation and Labels

**User Story:** As an administrator, I want consistent Malay labels for the Asset Transfer module, so that the interface is professional and consistent.

#### Acceptance Criteria

1. THE navigation label SHALL be "Pemindahan Aset" (not English)
2. THE model label SHALL be "Pemindahan Aset" (singular)
3. THE plural model label SHALL be "Pemindahan Aset" (plural)
4. THE breadcrumb SHALL display "Pemindahan Aset"

### Requirement 63: AssetTransferResource Eager Loading Fix

**User Story:** As a developer, I want correct eager loading configuration, so that the resource doesn't cause N+1 queries or errors.

#### Acceptance Criteria

1. THE `getEloquentQuery()` method SHALL eager load `asset` relationship
2. THE `getEloquentQuery()` method SHALL eager load `fromUser` relationship (not `fromDivision`)
3. THE `getEloquentQuery()` method SHALL eager load `toUser` relationship (not `toDivision`)
4. THE `getEloquentQuery()` method SHALL eager load `initiator` relationship (not `transferredBy`)
5. THE `getEloquentQuery()` method SHALL eager load `approver` relationship (not `approvedBy`)
6. THE eager loading SHALL match the actual model relationship names

---

## Helpdesk Reports & Analytics Page Requirements (Phase 31)

### Requirement 64: Translation File Consolidation

**User Story:** As a developer, I want a single source of truth for admin page translations, so that there are no conflicts or duplicate keys.

#### Acceptance Criteria

1. THE System SHALL have only ONE `admin_pages.php` translation file per locale
2. THE translation file SHALL NOT contain duplicate array keys (e.g., `data_visualization`)
3. THE canonical location SHALL be `lang/ms/admin_pages.php`
4. WHEN duplicate files exist, THE System SHALL merge and remove redundant files

### Requirement 65: Helpdesk Reports Translation Keys

**User Story:** As an administrator, I want all Helpdesk Reports page labels in Bahasa Melayu, so that the interface is consistent with the rest of the admin panel.

#### Acceptance Criteria

1. THE page title SHALL use translation key `__('admin_pages.helpdesk_reports.title')` returning "Laporan & Analitik Meja Bantuan"
2. THE navigation label SHALL use translation key returning "Laporan & Analitik"
3. THE KPI labels SHALL use translation keys:
   - `kpi_total_tickets` → "Jumlah Tiket"
   - `kpi_guest_submissions` → "Hantaran Tetamu"
   - `kpi_avg_resolution_time` → "Purata Masa Penyelesaian"
   - `kpi_sla_compliance` → "Pematuhan SLA"
4. THE time unit SHALL use "j" (jam) for hours in Malay, not "h"
5. THE section headings SHALL use translation keys:
   - `by_status` → "Tiket mengikut Status"
   - `by_priority` → "Tiket mengikut Keutamaan"
   - `by_category` → "Tiket mengikut Kategori"

### Requirement 66: Helpdesk Reports Duplicate Section Fix

**User Story:** As an administrator, I want the filter section to appear only once, so that the interface is clean and not confusing.

#### Acceptance Criteria

1. THE "Report Filters" / "Penapis Laporan" section SHALL appear only once on the page
2. THE Section wrapper SHALL be in the Blade view OR the form schema, not both
3. THE duplicate "Generate Report" / "Jana Laporan" button SHALL be removed

### Requirement 67: Helpdesk Reports Three-State Display

**User Story:** As an administrator, I want clear feedback about the report state, so that I understand whether to generate a report or why there's no data.

#### Acceptance Criteria

1. WHEN the page loads (before report generated), THE System SHALL display instruction: "Sila pilih julat tarikh dan klik 'Jana Laporan'."
2. WHEN report is generated but no tickets found, THE System SHALL display: "Tiada tiket dijumpai untuk julat tarikh yang dipilih."
3. WHEN report is generated with data, THE System SHALL display KPIs and breakdown sections
4. THE System SHALL NOT auto-generate report on mount (causing misleading "0" KPIs)

### Requirement 68: Helpdesk Reports Chart Empty States

**User Story:** As an administrator, I want clear feedback when chart sections have no data, so that I don't see blank white boxes.

#### Acceptance Criteria

1. WHEN a breakdown section has no data, THE System SHALL display "Tiada data untuk dipaparkan"
2. THE empty state SHALL use consistent styling with `text-sm text-gray-600 dark:text-gray-400`
3. THE System SHALL NOT display blank white boxes for empty chart sections

---

## Token API Module Requirements (Phase 32)

### Requirement 69: ApiTokenCreated Event Security Fix

**User Story:** As a security-conscious developer, I want the token creation event to not expose sensitive data, so that plaintext tokens are never broadcast via websockets.

#### Acceptance Criteria

1. THE `ApiTokenCreated::dispatch()` SHALL NOT include `$token->plainTextToken` in the payload
2. THE event payload SHALL use safe identifiers only (user ID, token ID, token name, expiry date)
3. THE System SHALL NEVER broadcast plaintext tokens via websockets or real-time channels

### Requirement 70: One-Time Token Reveal UI

**User Story:** As an administrator, I want to see the generated token exactly once with a copy button, so that I can securely save it before it's hidden forever.

#### Acceptance Criteria

1. WHEN a token is created, THE System SHALL display a token reveal banner on the index page
2. THE banner SHALL display heading: "Token berjaya dijana"
3. THE banner SHALL display warning: "Salin token ini sekarang. Token tidak akan dipaparkan lagi."
4. THE banner SHALL include a "Salin" (Copy) button with clipboard functionality
5. AFTER displaying the token, THE System SHALL call `session()->forget('new_api_token')`

### Requirement 71: Scope/Abilities Malay Labels

**User Story:** As an administrator, I want scope labels in Bahasa Melayu, so that I understand what each permission does without knowing technical strings.

#### Acceptance Criteria

1. THE table column SHALL display Malay labels for scopes (e.g., "Baca Tiket" not "read:tickets")
2. THE scope mapping SHALL include:
   - `read:tickets` → "Baca Tiket"
   - `write:tickets` → "Tulis Tiket"
   - `read:loans` → "Baca Pinjaman"
   - `write:loans` → "Tulis Pinjaman"
   - `read:assets` → "Baca Aset"
   - `write:assets` → "Tulis Aset"
   - `admin:all` → "Pentadbir Penuh"
3. THE column SHALL show tooltip with technical scope string on hover
4. THE `admin:all` scope SHALL display with warning badge/color

### Requirement 72: Token Expiry UX Improvement

**User Story:** As an administrator, I want clear guidance on token expiry policy, so that I make informed decisions about token lifetime.

#### Acceptance Criteria

1. THE expiry field helper text SHALL show default policy: "Lalai: 6 bulan"
2. THE helper text SHALL include warning: "Kosongkan untuk token kekal (tidak disyorkan)" with danger color
3. FOR non-superuser users, THE System MAY enforce maximum expiry (e.g., 12 months)

### Requirement 73: Token API Contextual Empty State

**User Story:** As an administrator, I want the empty state to provide guidance, so that I know how to create my first API token.

#### Acceptance Criteria

1. THE empty state SHALL NOT display generic "Tiada rekod dijumpai"
2. THE empty state SHALL display contextual message:
   - Heading: "Tiada Token API"
   - Description: "Klik 'Cipta Token Baharu' untuk jana token API."
3. THE empty state icon SHALL be contextually relevant (e.g., `heroicon-o-key`)

---

## SSO Users & Audit Logs Module Requirements (Phase 33)

### Requirement 74: SsoUserResource Contextual Empty State

**User Story:** As an administrator, I want the SSO Users empty state to explain why there are no records, so that I understand whether SSO is configured and working.

#### Acceptance Criteria

1. THE empty state SHALL NOT display generic "Tiada rekod dijumpai"
2. THE empty state SHALL display contextual message:
   - Heading: "Tiada pengguna SSO"
   - Description: "Rekod akan wujud selepas pengguna log masuk menggunakan Google SSO."
3. IF SSO is not configured, THE System SHALL display alternative message:
   - Heading: "SSO belum dikonfigurasi"
   - Description: "Sila konfigurasi Google SSO untuk membolehkan log masuk SSO."
   - Include link to SSO configuration page if available
4. THE empty state icon SHALL be contextually relevant (e.g., `heroicon-o-user-group`)

### Requirement 75: SsoAuditResource Contextual Empty State

**User Story:** As an administrator, I want the SSO Audit Logs empty state to explain why there are no records, so that I understand when logs will appear.

#### Acceptance Criteria

1. THE empty state SHALL NOT display generic "Tiada rekod dijumpai"
2. THE empty state SHALL display contextual message:
   - Heading: "Tiada log audit SSO"
   - Description: "Log akan direkodkan apabila percubaan log masuk SSO berlaku."
3. THE empty state MAY include hint: "Cuba log masuk melalui SSO untuk menjana rekod ujian."
4. THE empty state icon SHALL be contextually relevant (e.g., `heroicon-o-clipboard-document-list`)

### Requirement 76: SsoUserResource Last Login Column Fix

**User Story:** As an administrator, I want the "Last SSO Login" column to display correctly, so that I can see accurate SSO usage data.

#### Acceptance Criteria

1. THE `last_sso_login_at` column SHALL use computed state via `getStateUsing()` callback
2. THE column SHALL NOT use direct relationship path `ssoAuditLogs.attempted_at` (causes inconsistent behavior)
3. THE column SHALL display `optional($record->ssoAuditLogs->first())->attempted_at` value
4. THE column sorting SHALL be disabled (or implement proper subquery sort if needed)
5. THE column SHALL display "Tidak pernah" placeholder when no SSO login exists

### Requirement 77: SsoAuditResource Table Horizontal Scroll Fix

**User Story:** As an administrator, I want to view the SSO Audit Logs table without horizontal scrolling, so that I can review audit data efficiently.

#### Acceptance Criteria

1. THE SsoAuditLogsTable SHALL NOT require horizontal scroll on viewports 1280px or wider
2. THE System SHALL make non-critical columns toggleable with default hidden state:
   - `error_type` (Jenis Ralat) - often long/rare
   - `ip_address` (Alamat IP) - not always needed
3. THE System SHALL keep key operational columns visible by default:
   - `email` (E-mel)
   - `status` (Status)
   - `attempted_at` (Dicuba Pada)
4. THE `error_type` column SHALL truncate long text with tooltip showing full content
5. THE `email` column SHALL truncate long text with tooltip showing full content

### Requirement 78: SsoAuditResource Tab Badge Count Performance

**User Story:** As an administrator, I want the SSO Audit Logs page to load quickly, so that I can review audit data without delays.

#### Acceptance Criteria

1. THE tab badge counts (Semua, Berjaya, Gagal, Hari Ini) SHALL be cached for 30-60 seconds
2. THE System SHALL NOT execute 3+ separate COUNT queries on every page load
3. THE cache keys SHALL be: `sso_audit:count:all`, `sso_audit:count:success`, `sso_audit:count:failed`, `sso_audit:count:today`
4. THE cache TTL SHALL be configurable (default 60 seconds)
5. WHEN cache is invalidated, THE System SHALL recompute counts on next page load

### Requirement 79: SsoAuditResource Filter Bar UX

**User Story:** As an administrator, I want the filter bar to provide helpful feedback when all counts are zero, so that I understand the audit log state.

#### Acceptance Criteria

1. WHEN all tab counts are 0, THE System SHALL display help text in empty state
2. THE help text SHALL explain: "Tiada rekod audit SSO. Log akan direkodkan apabila percubaan log masuk SSO berlaku."
3. THE filter tabs SHALL remain functional even when counts are 0
4. THE filter labels SHALL be in Bahasa Melayu:
   - "Semua" (All)
   - "Berjaya" (Success)
   - "Gagal" (Failed)
   - "Hari Ini" (Today)

### Requirement 80: SsoUserResource Table Column Visibility

**User Story:** As an administrator, I want the SSO Users table to show only essential columns by default, so that I can scan user data efficiently.

#### Acceptance Criteria

1. THE SsoUsersTable SHALL keep these columns visible by default:
   - `name` (Nama)
   - `email` (E-mel)
   - `google_id` (ID Google)
   - `is_verified` (Disahkan)
   - `sso_login_count` (Bilangan Log Masuk)
2. THE SsoUsersTable SHALL hide these columns by default (toggleable):
   - `last_sso_login_at` (Log Masuk SSO Terakhir)
   - `created_at` (Dicipta)
   - `updated_at` (Dikemaskini)
3. THE name and email columns SHALL truncate long text with tooltip showing full value
4. THE SsoUsersTable SHALL NOT require horizontal scroll on viewports 1280px or wider

### Requirement 81: SsoAuditResource Status Badge Styling

**User Story:** As an administrator, I want clear visual distinction between successful and failed SSO attempts, so that I can quickly identify issues.

#### Acceptance Criteria

1. THE `status` column SHALL use badge styling with color coding:
   - Success: Green badge with "Berjaya" label
   - Failed: Red badge with "Gagal" label
2. THE badge SHALL include icon for non-color accessibility:
   - Success: `heroicon-o-check-circle`
   - Failed: `heroicon-o-x-circle`
3. THE System SHALL NOT rely solely on color to convey status (icon + color + text pattern)

---

## Pulse Dashboard & AutoReplyTemplate Module Requirements (Phase 34)

### Requirement 82: PulseDashboard Access Control Fix

**User Story:** As an admin or superuser, I want to access the Pulse Dashboard page, so that I can monitor system performance metrics.

#### Acceptance Criteria

1. THE `PulseDashboard::canAccess()` method SHALL use `hasAnyRole(['admin', 'superuser'])` instead of `hasRole(['admin', 'superuser'])`
2. WHEN a user with 'admin' role accesses the page, THE System SHALL grant access
3. WHEN a user with 'superuser' role accesses the page, THE System SHALL grant access
4. WHEN a user without admin or superuser role accesses the page, THE System SHALL deny access
5. THE System SHALL NOT incorrectly block admins/superusers due to incorrect Spatie Permission method usage

### Requirement 83: PulseOverviewWidget "No Data" Semantics

**User Story:** As an administrator, I want the Pulse Overview widget to clearly indicate when no data exists, so that I don't confuse "no data" with "0ms response time".

#### Acceptance Criteria

1. WHEN Pulse has no aggregate data (sample count = 0), THE System SHALL display "Tiada data" instead of "0ms" or "0%"
2. THE widget SHALL track "has data" state for each metric (response time, error rate, slow queries)
3. WHEN `getAverageResponseTime()` has no samples, THE System SHALL return null and display "—" with description "Tiada data dalam 1 jam terakhir"
4. WHEN `getErrorRate()` has totalRequests = 0, THE System SHALL return null and display "—" with description "Tiada data dalam 1 jam terakhir"
5. WHEN `getSlowQueriesCount()` has no data, THE System SHALL return null and display "—" with description "Tiada data dalam 1 jam terakhir"
6. THE metrics SHALL clearly distinguish between "0" (actual zero) and "no data available"

### Requirement 84: AutoReplyTemplateResource Auth::id() Null Safety

**User Story:** As an administrator, I want the duplicate action to handle session expiry gracefully, so that I see a friendly error message instead of a system exception.

#### Acceptance Criteria

1. WHEN `Auth::id()` returns null during duplicate action, THE System SHALL display a user-friendly notification instead of throwing an exception
2. THE notification SHALL display title: "Sesi tamat" (Session expired)
3. THE notification SHALL display body: "Sila log masuk semula untuk meneruskan" (Please log in again to continue)
4. THE notification SHALL use danger color styling
5. THE duplicate action SHALL NOT attempt to save with `created_by = null` (which violates foreign key constraint)
6. THE same null safety check SHALL be applied to both `AutoReplyTemplateResource::table()` duplicate action and `ViewAutoReplyTemplate::getHeaderActions()` duplicate action

### Requirement 85: PulseDashboard Malay Summary Header

**User Story:** As an administrator, I want a Malay summary header above the embedded Pulse UI, so that I can understand key metrics without navigating the English Pulse interface.

#### Acceptance Criteria

1. THE PulseDashboard page SHALL display a Malay summary section above the embedded Pulse iframe
2. THE summary section SHALL include these key indicators:
   - Status: "Aktif" / "Tidak Aktif" (based on Pulse availability)
   - "Pengecualian dalam 1 jam terakhir" (Exceptions in last hour)
   - "Permintaan perlahan" (Slow requests count)
   - "Query perlahan" (Slow queries count)
3. THE summary section SHALL use Filament-native widgets/cards (not embedded Pulse)
4. THE summary section SHALL include a prominent "Buka dalam Tab Baru" button for full Pulse access
5. THE embedded Pulse area SHALL include a note: "Paparan teknikal (Laravel Pulse)" to set user expectations about English content
6. THE summary section SHALL refresh data every 30 seconds (matching Pulse polling interval)

---

## Unified Search (Carian Global) Page Requirements (Phase 35)

### Requirement 86: Unified Search Mixed Language Fix

**User Story:** As an administrator, I want all Unified Search page labels in consistent Bahasa Melayu, so that the interface follows the "Bahasa Melayu sahaja" principle.

#### Acceptance Criteria

1. THE filter card labels SHALL be in Bahasa Melayu:
   - "Search Tickets" → "Cari Tiket"
   - "Search Loans" → "Cari Pinjaman"
   - "Search Assets" → "Cari Aset"
   - "Search Users" → "Cari Pengguna"
2. THE result section headings SHALL be in Bahasa Melayu:
   - "Helpdesk Tickets" → "Tiket Meja Bantuan"
   - "Loan Applications" → "Permohonan Pinjaman"
   - "Assets" → "Aset"
   - "Users" → "Pengguna"
3. THE "Assets" word in loan metadata SHALL be in Malay: "X Assets" → "X aset"
4. THE System SHALL NOT mix English and Malay labels on the same page

### Requirement 87: Unified Search Translation Namespace Consistency

**User Story:** As a developer, I want a single translation namespace for the Unified Search page, so that translations are maintainable and consistent.

#### Acceptance Criteria

1. THE Blade view SHALL use `__('admin_pages.unified_search.*')` namespace consistently
2. THE System SHALL NOT use competing `__('unified_search.*')` namespace in the same page
3. THE `UnifiedSearch.php` page class SHALL use `__('admin_pages.unified_search.label')` for navigation
4. THE translation keys SHALL be consolidated in `lang/ms/admin_pages.php` under `unified_search` array
5. THE System SHALL NOT display raw translation keys in the UI

### Requirement 88: Unified Search Translation Keys Completeness

**User Story:** As an administrator, I want all Unified Search UI elements to have proper Malay translations, so that no raw translation keys appear in the interface.

#### Acceptance Criteria

1. THE System SHALL provide these translation keys in `admin_pages.unified_search`:
   - `hero_title` → "Apa yang anda cari?"
   - `hero_subtitle` → "Carian segera untuk tiket, pinjaman, aset, dan pengguna."
   - `input_label` → "Carian global"
   - `placeholder` → "Taip untuk mencari..."
   - `clear` → "Kosongkan"
   - `searching` → "Mencari..."
   - `shortcut_hint` → "Pintasan papan kekunci: Ctrl/⌘K"
2. THE System SHALL provide filter labels:
   - `filters.tickets` → "Cari Tiket"
   - `filters.loans` → "Cari Pinjaman"
   - `filters.assets` → "Cari Aset"
   - `filters.users` → "Cari Pengguna"
3. THE System SHALL provide section headings:
   - `sections.tickets` → "Tiket Meja Bantuan"
   - `sections.loans` → "Permohonan Pinjaman"
   - `sections.assets` → "Aset"
   - `sections.users` → "Pengguna"
4. THE System SHALL provide result messages:
   - `assets_count_label` → "aset"
   - `found_results` → "Dijumpai :count keputusan untuk \":query\"."
   - `no_results_title` → "Tiada keputusan dijumpai"
   - `no_results_message` → "Tiada padanan untuk \":query\". Cuba kata kunci lain."

### Requirement 89: Unified Search Filter Cards Filament Styling

**User Story:** As an administrator, I want the filter cards to match Filament's design system, so that the page feels consistent with the rest of the admin panel.

#### Acceptance Criteria

1. THE filter cards SHALL use Filament-native button or section components
2. THE filter cards SHALL NOT use thick custom borders that differ from Filament's default styling
3. THE filter cards SHALL use subtle background and hover states consistent with Filament
4. THE filter cards SHALL use `x-filament::icon` for icons instead of custom icon styling
5. THE selected state SHALL use `border-primary-500 ring-1 ring-primary-500` for consistency

### Requirement 90: Unified Search Filter Cards Functionality

**User Story:** As an administrator, I want the filter cards to have clear functionality, so that I understand what clicking them does.

#### Acceptance Criteria

1. WHEN a filter card is clicked, THE System SHALL toggle the resource filter (tickets/loans/assets/users)
2. THE filter cards SHALL function as toggles (click to select/deselect)
3. THE selected filter cards SHALL have clear visual distinction from unselected cards
4. THE System SHALL allow multiple filters to be selected simultaneously
5. THE search results SHALL update immediately when filters are toggled

### Requirement 91: Unified Search Keyboard Shortcut Accessibility

**User Story:** As an administrator using keyboard navigation, I want the keyboard shortcut hint to be accessible, so that I can use the search efficiently.

#### Acceptance Criteria

1. THE keyboard shortcut hint (Ctrl/⌘K) SHALL be visible in the search input area
2. THE shortcut hint SHALL include screen reader text: "Pintasan papan kekunci: Ctrl/⌘K"
3. THE shortcut hint SHALL use `aria-hidden="true"` for the visual badge and `sr-only` for screen reader text
4. WHEN Ctrl/⌘K is pressed, THE System SHALL focus the search input field
5. THE search input SHALL have proper `aria-label` attribute

### Requirement 92: Unified Search Filter Cards Accessibility

**User Story:** As an administrator using assistive technology, I want the filter cards to be accessible, so that I can use them with keyboard and screen readers.

#### Acceptance Criteria

1. THE filter cards SHALL be implemented as `<button>` elements (not `<div>` with click handlers)
2. THE filter cards SHALL have proper focus outlines (`focus-visible:ring-3 focus-visible:ring-primary-500`)
3. THE filter cards SHALL have `aria-pressed` attribute indicating selected state
4. THE filter cards SHALL have descriptive `aria-label` (e.g., "Togol penapis Tiket")
5. THE filter cards SHALL be keyboard navigable (Tab, Enter, Space)

### Requirement 93: Unified Search Navigation Badge Consistency

**User Story:** As an administrator, I want the navigation badge to be consistent with the UI shortcut hint, so that there's no confusion about the keyboard shortcut.

#### Acceptance Criteria

1. THE navigation badge in `UnifiedSearch.php` SHALL display "Ctrl/⌘K" (not just "Ctrl+K")
2. THE badge format SHALL match the UI shortcut hint format
3. THE badge SHALL be visible on desktop viewports only (hidden on mobile)

---

## Filter Presets (Pratetap Penapis) Page Requirements (Phase 36)

### Requirement 94: Filter Presets Translation Key Leakage Fix

**User Story:** As an administrator, I want the Filter Presets page title to display properly in Malay, so that the interface looks professional and not broken.

#### Acceptance Criteria

1. THE page title SHALL display "Pratetap Penapis" (not raw key `admin_pages.filter_presets.title`)
2. THE System SHALL provide `admin_pages.filter_presets.title` translation key in the active language file
3. THE System SHALL consolidate duplicate `admin_pages.php` files to ensure correct translations are loaded
4. THE System SHALL NOT display any raw translation keys in the UI

### Requirement 95: Filter Presets Quick Filter Localization

**User Story:** As an administrator, I want all quick filter labels in Bahasa Melayu, so that the interface follows the "Bahasa Melayu sahaja" principle.

#### Acceptance Criteria

1. THE quick filter labels SHALL be in Bahasa Melayu:
   - "Open High Priority Tickets" → "Tiket Keutamaan Tinggi (Masih Dibuka)"
   - "Pending Approval" → "Permohonan Menunggu Kelulusan"
   - "Available Assets" → "Aset Tersedia"
   - "Active Users" → "Pengguna Aktif"
2. THE `FilterPresetService` SHALL return translation keys (not hardcoded English strings)
3. THE Blade view SHALL translate quick filter labels using `__($filter['label_key'])`
4. THE System SHALL NOT mix English and Malay labels on the same page

### Requirement 96: Filter Presets Modal Action Label Fix

**User Story:** As an administrator, I want the modal submit button to say "Simpan" instead of "Hantar", so that the action is clear and consistent.

#### Acceptance Criteria

1. THE create preset modal submit button SHALL display "Simpan" (not "Hantar")
2. THE modal cancel button SHALL display "Batal"
3. THE action labels SHALL be consistent across all similar modals in the system
4. THE System SHALL use `->modalSubmitActionLabel()` to override Filament's default

### Requirement 97: Filter Presets Default Checkbox Helper Text

**User Story:** As an administrator, I want clear explanation of what "default preset" means, so that I understand the impact of my selection.

#### Acceptance Criteria

1. THE "Jadikan sebagai preset lalai" checkbox SHALL have helper text explaining its function
2. THE helper text SHALL read: "Preset lalai akan digunakan secara automatik apabila anda membuka sumber ini."
3. IF setting a new default would override an existing default, THE System SHALL warn the user
4. THE System SHALL enforce only one default preset per resource per user

### Requirement 98: Filter Presets User-Specific Storage

**User Story:** As an administrator, I want my filter presets to be separate from other users' presets, so that my customizations don't affect others.

#### Acceptance Criteria

1. THE preset cache key SHALL include user ID: `filter_presets:user:{userId}:{resource}`
2. THE `getUserPresets()` method SHALL return only the current user's presets
3. THE `saveFilterPreset()` method SHALL store presets under user-specific keys
4. THE System SHALL NOT share presets between different users
5. WHEN a user sets a default preset, THE System SHALL unset any existing default for that resource

### Requirement 99: Filter Presets Complete Translation Keys

**User Story:** As an administrator, I want all Filter Presets UI elements to have proper Malay translations, so that no raw translation keys appear.

#### Acceptance Criteria

1. THE System SHALL provide these translation keys in `admin_pages.filter_presets`:
   - `title` → "Pratetap Penapis"
   - `label` → "Pratetap Penapis"
   - `actions.create` → "Cipta Preset Baharu"
   - `actions.save` → "Simpan"
   - `actions.cancel` → "Batal"
   - `fields.name` → "Nama Preset"
   - `fields.resource` → "Sumber"
   - `fields.is_default` → "Jadikan sebagai preset lalai"
   - `fields.is_default_help` → "Preset lalai akan digunakan secara automatik apabila anda membuka sumber ini."
   - `resources.helpdesk_tickets` → "Tiket Helpdesk"
   - `resources.loan_applications` → "Permohonan Pinjaman"
   - `resources.assets` → "Aset"
   - `resources.users` → "Pengguna"
   - `notifications.created_title` → "Preset berjaya dicipta"
2. THE System SHALL provide quick filter translation keys:
   - `quick_filters.helpdesk.open_high_priority` → "Tiket Keutamaan Tinggi (Masih Dibuka)"
   - `quick_filters.loans.pending_approval` → "Permohonan Menunggu Kelulusan"
   - `quick_filters.assets.available` → "Aset Tersedia"
   - `quick_filters.users.active` → "Pengguna Aktif"

---

## Notification Center (Pusat Pemberitahuan) Page Requirements (Phase 37)

### Requirement 100: Notification Center Translation Key Leakage Fix

**User Story:** As an administrator, I want the Notification Center page title to display properly in Malay, so that the interface looks professional and not broken.

#### Acceptance Criteria

1. THE page title SHALL display "Pusat Pemberitahuan" (not raw key `admin_pages.notification_center.title`)
2. THE System SHALL provide `admin_pages.notification_center.title` translation key in the active language file
3. THE System SHALL consolidate duplicate `admin_pages.php` files to ensure correct translations are loaded
4. THE System SHALL NOT display any raw translation keys in the UI
5. THE navigation label SHALL display "Pusat Pemberitahuan" consistently

### Requirement 101: Notification Center KPI Cards Localization

**User Story:** As an administrator, I want all Notification Center KPI card labels in Bahasa Melayu, so that the interface follows the "Bahasa Melayu sahaja" principle.

#### Acceptance Criteria

1. THE KPI card labels SHALL be in Bahasa Melayu:
   - "Total Notifications" → "Jumlah Pemberitahuan"
   - "Unread" → "Belum Dibaca"
   - "Today" → "Hari Ini"
   - "This Week" → "Minggu Ini"
2. THE System SHALL use translation keys for all KPI labels
3. THE System SHALL NOT mix English and Malay labels on the same page

### Requirement 102: Notification Center Tab Labels Localization

**User Story:** As an administrator, I want all Notification Center tab labels in Bahasa Melayu, so that the interface is consistent.

#### Acceptance Criteria

1. THE tab labels SHALL be in Bahasa Melayu:
   - "All Notifications" → "Semua Pemberitahuan"
   - "Unread" → "Belum Dibaca"
   - "Read" → "Dibaca"
2. THE System SHALL use translation keys for all tab labels
3. THE active tab SHALL have clear visual distinction

### Requirement 103: Notification Center Empty State Localization

**User Story:** As an administrator, I want the empty state messages in Bahasa Melayu with actionable guidance, so that I understand when notifications will appear.

#### Acceptance Criteria

1. THE empty state heading SHALL be in Bahasa Melayu: "Tiada pemberitahuan"
2. THE empty state description SHALL be in Bahasa Melayu: "Anda belum mempunyai sebarang pemberitahuan."
3. THE empty state SHALL include contextual guidance: "Pemberitahuan akan muncul apabila terdapat kemas kini tiket, kelulusan, atau amaran sistem."
4. THE empty state icon SHALL be contextually relevant (e.g., `heroicon-o-bell-slash`)
5. THE empty state SHALL vary based on active filter:
   - All: "Tiada pemberitahuan"
   - Unread: "Tiada pemberitahuan belum dibaca"
   - Read: "Tiada pemberitahuan yang telah dibaca"

### Requirement 104: Notification Center Action Labels Localization

**User Story:** As an administrator, I want all notification action labels in Bahasa Melayu, so that the interface is consistent.

#### Acceptance Criteria

1. THE notification action labels SHALL be in Bahasa Melayu:
   - "View Details" → "Lihat Butiran"
   - "Mark as Read" → "Tandakan Dibaca"
   - "Mark as Unread" → "Tandakan Belum Dibaca"
   - "Delete" → "Padam"
2. THE badge labels SHALL be in Bahasa Melayu:
   - "High Priority" → "Keutamaan Tinggi"
   - "Urgent" → "Segera"
3. THE confirmation dialog SHALL be in Bahasa Melayu:
   - "Are you sure you want to delete this notification?" → "Adakah anda pasti mahu memadam pemberitahuan ini?"

### Requirement 105: Notification Center Header Actions Consistency

**User Story:** As an administrator, I want consistent action button labels and behavior, so that the interface is predictable.

#### Acceptance Criteria

1. THE header action labels SHALL be in Bahasa Melayu:
   - "Kosongkan Semua" (Clear All) - destructive action
   - "Keutamaan" (Preferences) - settings action
   - "Muat Semula" (Refresh) - refresh action
2. THE "Kosongkan Semua" action SHALL require confirmation modal
3. THE confirmation modal SHALL display:
   - Heading: "Kosongkan Semua Pemberitahuan"
   - Description: "Adakah anda pasti mahu memadam semua pemberitahuan? Tindakan ini tidak boleh dibatalkan."
   - Submit: "Kosongkan"
   - Cancel: "Batal"
4. THE System SHALL use consistent verb forms across the app ("Muat Semula" vs "Segarkan" - pick one)

### Requirement 106: Notification Center Load More Functionality Fix

**User Story:** As an administrator, I want the "Load More" button to work correctly, so that I can view older notifications.

#### Acceptance Criteria

1. THE "Load More Notifications" button SHALL call a working `loadMoreNotifications()` method
2. THE `NotificationCenter.php` class SHALL implement `loadMoreNotifications()` method
3. THE method SHALL increment the limit by 50 and reload notifications
4. THE button label SHALL be in Bahasa Melayu: "Muatkan Lagi Pemberitahuan"
5. THE button SHALL be hidden when all notifications are loaded
6. THE System SHALL NOT throw Livewire errors when the button is clicked

### Requirement 107: Notification Center Auto-Refresh Consistency

**User Story:** As an administrator, I want the auto-refresh to update both notifications and stats, so that I see accurate real-time data.

#### Acceptance Criteria

1. WHEN auto-refresh triggers, THE System SHALL refresh both notification list AND stats
2. THE auto-refresh interval SHALL be 30 seconds
3. THE System SHALL pause auto-refresh when the browser tab is not visible
4. THE System SHALL use Livewire polling (`wire:poll.30s`) or consistent JS interval
5. THE refresh method SHALL call both `loadNotifications()` and `loadNotificationStats()`

### Requirement 108: Notification Center Icon Component Fix

**User Story:** As a developer, I want the icon component logic to work correctly, so that notification icons render without errors.

#### Acceptance Criteria

1. THE icon component logic SHALL NOT include no-op `str_replace()` calls
2. THE System SHALL provide fallback icon (`heroicon-o-bell`) if icon component doesn't exist
3. THE System SHALL handle missing or invalid icon strings gracefully
4. THE icon rendering SHALL NOT cause Blade component errors

### Requirement 109: Notification Center Query Performance

**User Story:** As an administrator, I want the Notification Center to load quickly, so that I can access notifications without delays.

#### Acceptance Criteria

1. THE `loadNotificationStats()` method SHALL minimize database queries (ideally 1-2 queries, not 4+)
2. THE stats SHALL be cached briefly (30-60 seconds) to reduce database load
3. THE `loadNotifications()` method SHALL use efficient pagination
4. THE System SHALL NOT execute redundant count queries on every refresh
5. THE page SHALL achieve LCP under 2.5 seconds

### Requirement 110: Notification Center Complete Translation Keys

**User Story:** As an administrator, I want all Notification Center UI elements to have proper Malay translations, so that no raw translation keys appear.

#### Acceptance Criteria

1. THE System SHALL provide these translation keys in `admin_pages.notification_center`:
   - `title` → "Pusat Pemberitahuan"
   - `label` → "Pusat Pemberitahuan"
   - `kpi.total` → "Jumlah Pemberitahuan"
   - `kpi.unread` → "Belum Dibaca"
   - `kpi.today` → "Hari Ini"
   - `kpi.this_week` → "Minggu Ini"
   - `tabs.all` → "Semua Pemberitahuan"
   - `tabs.unread` → "Belum Dibaca"
   - `tabs.read` → "Dibaca"
   - `empty.title` → "Tiada pemberitahuan"
   - `empty.description` → "Anda belum mempunyai sebarang pemberitahuan."
   - `empty.guidance` → "Pemberitahuan akan muncul apabila terdapat kemas kini tiket, kelulusan, atau amaran sistem."
   - `actions.view_details` → "Lihat Butiran"
   - `actions.mark_read` → "Tandakan Dibaca"
   - `actions.mark_unread` → "Tandakan Belum Dibaca"
   - `actions.delete` → "Padam"
   - `actions.mark_all_read` → "Tandakan Semua Dibaca"
   - `actions.clear_all` → "Kosongkan Semua"
   - `actions.preferences` → "Keutamaan"
   - `actions.refresh` → "Muat Semula"
   - `actions.load_more` → "Muatkan Lagi Pemberitahuan"
   - `actions.confirm` → "Sahkan"
   - `actions.cancel` → "Batal"
   - `badges.high_priority` → "Keutamaan Tinggi"
   - `badges.urgent` → "Segera"
   - `modals.clear_all_heading` → "Kosongkan Semua Pemberitahuan"
   - `modals.clear_all_description` → "Adakah anda pasti mahu memadam semua pemberitahuan? Tindakan ini tidak boleh dibatalkan."
   - `modals.delete_confirm` → "Adakah anda pasti mahu memadam pemberitahuan ini?"
2. THE System SHALL NOT display any raw translation keys in the UI

---

## Phase 38: Notification Preferences (Keutamaan Pemberitahuan) Page Fixes

### Requirement 111: Notification Preferences Translation Key Leakage Fix

**User Story:** As an administrator, I want the Notification Preferences page title to display properly in Bahasa Melayu, so that no raw translation keys appear.

#### Acceptance Criteria

1. THE page title SHALL display "Keutamaan Pemberitahuan" (not raw key `admin_pages.notification_preferences.title`)
2. THE navigation label SHALL display "Keutamaan Pemberitahuan" consistently
3. THE System SHALL provide `admin_pages.notification_preferences.title` translation key in the active language file
4. THE System SHALL NOT display any raw translation keys in the UI

### Requirement 112: Notification Preferences Page Description Localization

**User Story:** As an administrator, I want the page description in Bahasa Melayu, so that the interface follows the "Bahasa Melayu sahaja" principle.

#### Acceptance Criteria

1. THE page heading SHALL be in Bahasa Melayu: "Keutamaan Pemberitahuan"
2. THE page description SHALL be in Bahasa Melayu: "Urus tetapan pemberitahuan anda untuk e-mel, dalam aplikasi, dan saluran lain."
3. THE System SHALL use translation keys for all page-level text

### Requirement 113: Notification Preferences Current Settings Summary Localization

**User Story:** As an administrator, I want the "Current Settings Summary" section labels in Bahasa Melayu, so that I can understand my notification settings.

#### Acceptance Criteria

1. THE section heading SHALL be in Bahasa Melayu: "Ringkasan Tetapan Semasa"
2. THE delivery method labels SHALL be in Bahasa Melayu:
   - "Email" → "E-mel"
   - "In-App" → "Dalam Aplikasi"
   - "SMS" → "SMS"
   - "Desktop" → "Desktop"
3. THE status labels SHALL be in Bahasa Melayu:
   - "Enabled" → "Diaktifkan"
   - "Disabled" → "Dinyahaktifkan"

### Requirement 114: Notification Preferences Categories Localization

**User Story:** As an administrator, I want all notification category labels in Bahasa Melayu, so that the interface is consistent.

#### Acceptance Criteria

1. THE category labels SHALL be in Bahasa Melayu:
   - "Helpdesk" → "Meja Bantuan"
   - "Asset Loans" → "Pinjaman Aset"
   - "Security" → "Keselamatan"
   - "System" → "Sistem"
2. THE notification type labels within categories SHALL be in Bahasa Melayu
3. THE System SHALL NOT mix English and Malay labels on the same page

### Requirement 115: Notification Preferences Timing Settings Localization

**User Story:** As an administrator, I want timing-related settings labels in Bahasa Melayu, so that I can configure notification timing.

#### Acceptance Criteria

1. THE timing settings labels SHALL be in Bahasa Melayu:
   - "Digest" → "Ringkasan"
   - "Quiet Hours" → "Waktu Senyap"
   - "Weekend Notifications" → "Pemberitahuan Hujung Minggu"
   - "Start Time" → "Masa Mula"
   - "End Time" → "Masa Tamat"
2. THE frequency options SHALL be in Bahasa Melayu:
   - "Immediate" → "Segera"
   - "Daily" → "Harian"
   - "Weekly" → "Mingguan"

### Requirement 116: Notification Preferences Priority Settings Localization

**User Story:** As an administrator, I want priority-related settings labels in Bahasa Melayu, so that I can configure notification priorities.

#### Acceptance Criteria

1. THE priority settings labels SHALL be in Bahasa Melayu:
   - "Urgent Only Mode" → "Mod Urgent Sahaja"
   - "Minimum Priority" → "Keutamaan Minimum"
   - "Yes" → "Ya"
   - "No" → "Tidak"
2. THE priority level options SHALL be in Bahasa Melayu:
   - "Low" → "Rendah"
   - "Medium" → "Sederhana"
   - "High" → "Tinggi"
   - "Critical" → "Kritikal"

### Requirement 117: Notification Preferences Help Section Localization

**User Story:** As an administrator, I want the help section in Bahasa Melayu, so that I can understand how to configure notifications.

#### Acceptance Criteria

1. THE help section title SHALL be in Bahasa Melayu: "Bantuan & Panduan"
2. THE help section content SHALL be in Bahasa Melayu
3. THE help section SHALL explain each notification category and delivery method
4. THE help section SHOULD be collapsible (default collapsed) for better page scannability

### Requirement 118: Notification Preferences Data Model Alignment (Critical)

**User Story:** As a developer, I want the NotificationPreferenceService to correctly read user preferences, so that notification delivery respects user settings.

#### Acceptance Criteria

1. THE NotificationPreferenceService SHALL read nested preference schema correctly:
   - `helpdesk_notifications.ticket_assigned` for ticket assignment notifications
   - `helpdesk_notifications.sla_breach` for SLA breach notifications
   - `loan_notifications.loan_approved` for loan approval notifications
   - etc.
2. THE service SHALL map flat keys to nested keys:
   - `email_notifications` → `email_enabled`
   - `in_app_notifications` → `in_app_enabled`
3. THE service SHALL check timing settings:
   - `urgent_only_mode` for urgent-only filtering
   - `priority_threshold` for minimum priority filtering
   - `quiet_hours_enabled`, `quiet_hours_start`, `quiet_hours_end` for quiet hours
   - `weekend_notifications` for weekend filtering
4. THE System SHALL NOT send notifications that violate user preferences

### Requirement 119: Notification Preferences Form Schema Translation Keys

**User Story:** As an administrator, I want all form field labels to use proper translation keys, so that no hardcoded English appears.

#### Acceptance Criteria

1. THE form schema SHALL use translation keys for all field labels
2. THE form schema SHALL use translation keys for all helper text
3. THE form schema SHALL use translation keys for all placeholder text
4. THE System SHALL NOT have hardcoded English strings in the form schema

### Requirement 120: Notification Preferences Help Section Collapsible (Optional UX)

**User Story:** As an administrator, I want the help section to be collapsible, so that I can focus on the settings without scrolling past help text.

#### Acceptance Criteria

1. THE help section SHOULD be collapsible with a toggle button
2. THE help section SHOULD be collapsed by default
3. THE toggle button label SHALL be in Bahasa Melayu: "Tunjukkan Bantuan" / "Sembunyikan Bantuan"
4. THE collapsed state SHOULD be persisted in user preferences (optional)

### Requirement 121: Notification Preferences Complete Translation Keys

**User Story:** As an administrator, I want all Notification Preferences UI elements to have proper Malay translations, so that no raw translation keys appear.

#### Acceptance Criteria

1. THE System SHALL provide these translation keys in `admin_pages.notification_preferences`:
   - `title` → "Keutamaan Pemberitahuan"
   - `label` → "Keutamaan Pemberitahuan"
   - `description` → "Urus tetapan pemberitahuan anda untuk e-mel, dalam aplikasi, dan saluran lain."
   - `summary.heading` → "Ringkasan Tetapan Semasa"
   - `summary.email` → "E-mel"
   - `summary.in_app` → "Dalam Aplikasi"
   - `summary.sms` → "SMS"
   - `summary.desktop` → "Desktop"
   - `summary.enabled` → "Diaktifkan"
   - `summary.disabled` → "Dinyahaktifkan"
   - `categories.helpdesk` → "Meja Bantuan"
   - `categories.loans` → "Pinjaman Aset"
   - `categories.security` → "Keselamatan"
   - `categories.system` → "Sistem"
   - `timing.digest` → "Ringkasan"
   - `timing.quiet_hours` → "Waktu Senyap"
   - `timing.weekend` → "Pemberitahuan Hujung Minggu"
   - `timing.start` → "Masa Mula"
   - `timing.end` → "Masa Tamat"
   - `frequency.immediate` → "Segera"
   - `frequency.daily` → "Harian"
   - `frequency.weekly` → "Mingguan"
   - `priority.urgent_only` → "Mod Urgent Sahaja"
   - `priority.minimum` → "Keutamaan Minimum"
   - `priority.low` → "Rendah"
   - `priority.medium` → "Sederhana"
   - `priority.high` → "Tinggi"
   - `priority.critical` → "Kritikal"
   - `help.title` → "Bantuan & Panduan"
   - `help.show` → "Tunjukkan Bantuan"
   - `help.hide` → "Sembunyikan Bantuan"
   - `actions.save` → "Simpan Keutamaan"
   - `actions.reset` → "Set Semula"
   - `messages.saved` → "Keutamaan pemberitahuan telah disimpan."
   - `messages.reset` → "Keutamaan pemberitahuan telah diset semula."
2. THE System SHALL NOT display any raw translation keys in the UI

---

## Phase 39: Alert Configuration (Konfigurasi Sistem Amaran) Page Fixes

### Requirement 122: Alert Configuration KPI Cards Real Data

**User Story:** As an administrator, I want the Alert Configuration KPI cards to display real backend data, so that I can see actual system alert metrics.

#### Acceptance Criteria

1. THE "Amaran Aktif" (Active Alerts) KPI card SHALL display real count from backend (not hardcoded `0`)
2. THE "Kesihatan Sistem" (System Health) KPI card SHALL display real percentage from backend (not hardcoded `95%`)
3. THE "Status Sistem" (System Status) KPI card SHALL display real status from backend (not hardcoded "Sistem beroperasi dengan normal")
4. THE System SHALL provide `getCurrentAlertMetrics()` method in ConfigurableAlertService
5. THE metrics method SHALL NOT trigger alerts (read-only operation)
6. THE KPI cards SHALL update when `refreshDashboardData()` is called

### Requirement 123: Alert Configuration Recent Alerts Backend Storage

**User Story:** As an administrator, I want to see recent alerts that were actually triggered, so that I can monitor system alert history.

#### Acceptance Criteria

1. THE "Amaran Terkini" (Recent Alerts) section SHALL display real alerts from backend
2. THE System SHALL store recent alerts in cache when `sendAlert()` fires
3. THE cache key SHALL be `system_alerts:recent`
4. THE System SHALL keep the last 50 alerts in cache
5. THE System SHALL provide `getRecentAlerts()` method in ConfigurableAlertService
6. THE recent alerts SHALL include: type, severity, message, timestamp
7. THE empty state SHALL display "Tiada amaran terkini" when no alerts exist

### Requirement 124: Alert Configuration Conditional Threshold Fields

**User Story:** As an administrator, I want threshold fields to be disabled when their corresponding toggle is off, so that I don't accidentally configure thresholds for disabled alerts.

#### Acceptance Criteria

1. WHEN `overdue_tickets_enabled` is false, THE `overdue_tickets_threshold` field SHALL be disabled
2. WHEN `overdue_loans_enabled` is false, THE `overdue_loans_threshold` field SHALL be disabled
3. WHEN `approval_delays_enabled` is false, THE `approval_delay_hours` field SHALL be disabled
4. WHEN `asset_shortages_enabled` is false, THE `critical_asset_shortage_percentage` field SHALL be disabled
5. WHEN `system_health_enabled` is false, THE `system_health_threshold` field SHALL be disabled
6. THE threshold fields SHALL also be conditionally required (required only when toggle is on)
7. THE System SHALL use Filament's `->disabled(fn ($get) => ! $get('toggle_field'))` pattern

### Requirement 125: Alert Configuration Validation Constraints

**User Story:** As an administrator, I want validation constraints on threshold fields, so that I cannot enter invalid values.

#### Acceptance Criteria

1. THE `overdue_tickets_threshold` field SHALL have min=1, max=100
2. THE `overdue_loans_threshold` field SHALL have min=1, max=100
3. THE `approval_delay_hours` field SHALL have min=1, max=168 (1 week)
4. THE `critical_asset_shortage_percentage` field SHALL have min=1, max=100
5. THE `system_health_threshold` field SHALL have min=1, max=100
6. THE `response_time_threshold` field SHALL have min=60, max=3600 (1 minute to 1 hour)
7. THE System SHALL display validation errors in Bahasa Melayu

### Requirement 126: Alert Configuration Livewire Polling

**User Story:** As an administrator, I want the Alert Configuration page to auto-refresh using Livewire polling, so that I see updated metrics without manual refresh.

#### Acceptance Criteria

1. THE System SHALL use `wire:poll.30s="refreshDashboardData"` instead of plain JS `setInterval`
2. THE System SHALL pause polling when the browser tab is not visible
3. THE `refreshDashboardData()` method SHALL update KPI metrics and recent alerts
4. THE System SHALL NOT spam alerts when admin opens the page (use read-only metrics methods)
5. THE polling SHALL be efficient (minimal database queries)

### Requirement 127: Alert Configuration Loading/Error/Empty States

**User Story:** As an administrator, I want proper loading, error, and empty states for KPI cards, so that I understand the current data status.

#### Acceptance Criteria

1. WHEN KPI data is loading, THE System SHALL display skeleton loaders with `aria-busy="true"`
2. WHEN KPI data fails to load, THE System SHALL display error state with retry button
3. WHEN no recent alerts exist, THE System SHALL display empty state: "Tiada amaran terkini"
4. THE loading state SHALL use `wire:loading` directive
5. THE error state SHALL display error message in Bahasa Melayu

### Requirement 128: Alert Configuration Translation Key Consistency

**User Story:** As an administrator, I want all Alert Configuration UI elements to use proper translation keys, so that no literal strings appear.

#### Acceptance Criteria

1. THE System SHALL use translation keys for all KPI card labels (not `__('literal string')`)
2. THE System SHALL use translation keys for all form field labels
3. THE System SHALL use translation keys for all button labels
4. THE System SHALL use translation keys for all status messages
5. THE System SHALL NOT use `__('Amaran Aktif')` pattern - use `__('alert_configuration.kpi.active_alerts')` instead

### Requirement 129: Alert Configuration Complete Translation Keys

**User Story:** As an administrator, I want all Alert Configuration UI elements to have proper Malay translations, so that no raw translation keys appear.

#### Acceptance Criteria

1. THE System SHALL provide these translation keys in `admin_pages.alert_configuration`:
   - `title` → "Konfigurasi Sistem Amaran"
   - `label` → "Konfigurasi Amaran"
   - `description` → "Urus tetapan amaran automatik untuk sistem ICTServe."
   - `kpi.active_alerts` → "Amaran Aktif"
   - `kpi.system_health` → "Kesihatan Sistem"
   - `kpi.system_status` → "Status Sistem"
   - `kpi.status_normal` → "Sistem beroperasi dengan normal"
   - `kpi.status_warning` → "Sistem memerlukan perhatian"
   - `kpi.status_critical` → "Sistem dalam keadaan kritikal"
   - `recent.title` → "Amaran Terkini"
   - `recent.empty` → "Tiada amaran terkini"
   - `recent.view_all` → "Lihat Semua Amaran"
   - `thresholds.title` → "Tetapan Had Amaran"
   - `thresholds.overdue_tickets` → "Tiket Tertunggak"
   - `thresholds.overdue_loans` → "Pinjaman Tertunggak"
   - `thresholds.approval_delays` → "Kelewatan Kelulusan"
   - `thresholds.asset_shortages` → "Kekurangan Aset"
   - `thresholds.system_health` → "Kesihatan Sistem"
   - `thresholds.response_time` → "Masa Tindak Balas"
   - `channels.title` → "Saluran Pemberitahuan"
   - `channels.email` → "Pemberitahuan E-mel"
   - `channels.admin_panel` → "Pemberitahuan Panel Admin"
   - `frequency.title` → "Kekerapan Amaran"
   - `frequency.immediate` → "Segera"
   - `frequency.hourly` → "Setiap Jam"
   - `frequency.daily` → "Harian"
   - `actions.save` → "Simpan Konfigurasi"
   - `actions.test` → "Hantar Amaran Ujian"
   - `actions.refresh` → "Muat Semula"
   - `messages.saved` → "Konfigurasi amaran telah disimpan."
   - `messages.test_sent` → "Amaran ujian telah dihantar."
   - `validation.min` → "Nilai minimum ialah :min"
   - `validation.max` → "Nilai maksimum ialah :max"
   - `loading` → "Memuatkan..."
   - `error.load_failed` → "Gagal memuatkan data. Sila cuba lagi."
   - `error.retry` → "Cuba Lagi"
2. THE System SHALL NOT display any raw translation keys in the UI

---

## Phase 40: Report Builder (Pembina Laporan) Page Fixes

### Requirement 130: Report Builder Duplicate CTA Removal

**User Story:** As an administrator, I want a single clear call-to-action for generating previews, so that I'm not confused by duplicate buttons.

#### Acceptance Criteria

1. THE page SHALL have only ONE "Jana Pratonton" (Generate Preview) button
2. THE System SHALL remove either the header action OR the body submit button (not both)
3. THE remaining button SHALL be clearly visible and accessible
4. THE button SHALL be disabled until configuration is valid (module selected, date range set)

### Requirement 131: Report Builder Preview Table Display

**User Story:** As an administrator, I want to see a preview table with sample data, so that I can verify my report configuration before exporting.

#### Acceptance Criteria

1. WHEN preview is generated, THE System SHALL display a table with the first 10-20 rows of data
2. THE preview table SHALL show column headers matching the selected fields
3. THE preview table SHALL display total record count above the table
4. THE preview table SHALL show applied filters summary (date range, statuses)
5. WHEN no records match filters, THE System SHALL display empty state: "Tiada rekod dijumpai. Sila laraskan penapis."
6. THE preview table SHALL use zebra striping for readability

### Requirement 132: Report Builder Export Implementation

**User Story:** As an administrator, I want to actually download exported reports, so that I can use the data outside the system.

#### Acceptance Criteria

1. WHEN export is triggered, THE System SHALL return a downloadable file (not just a notification)
2. THE System SHALL support CSV export as minimum viable implementation
3. THE export file SHALL use Malay column headers (matching UI labels)
4. THE export filename SHALL include module name and date range: `laporan_{module}_{date_from}_{date_to}.csv`
5. IF export format is not implemented, THE System SHALL display "Akan datang" (Coming soon) instead of fake success
6. THE System SHALL NOT show "Export Berjaya" notification without providing a file

### Requirement 133: Report Builder Translation Key Consistency

**User Story:** As an administrator, I want all Report Builder labels in consistent Bahasa Melayu using translation keys, so that the interface is maintainable.

#### Acceptance Criteria

1. THE System SHALL use translation keys for all form labels (not hardcoded strings)
2. THE System SHALL use translation keys for all button labels
3. THE System SHALL use translation keys for all section headings
4. THE System SHALL NOT use `__('literal string')` pattern - use `__('report_builder.field_name')` instead
5. THE exported report headers SHALL be in Bahasa Melayu

### Requirement 134: Report Builder Empty State Guidance

**User Story:** As a first-time user, I want clear guidance on how to use the report builder, so that I understand what to do.

#### Acceptance Criteria

1. WHEN no preview has been generated, THE System SHALL display guidance text explaining the workflow
2. THE guidance SHALL explain: 1) Select module, 2) Set date range, 3) Apply filters, 4) Generate preview, 5) Export
3. THE guidance SHALL be in Bahasa Melayu
4. THE preview section SHALL show placeholder with instructions until preview is generated

### Requirement 135: Report Builder Form Validation

**User Story:** As an administrator, I want form validation to prevent invalid configurations, so that I don't generate empty or broken reports.

#### Acceptance Criteria

1. THE module field SHALL be required
2. THE date_from field SHALL be required
3. THE date_to field SHALL be required
4. THE date_to SHALL be greater than or equal to date_from
5. THE System SHALL display validation errors in Bahasa Melayu
6. THE "Jana Pratonton" button SHALL be disabled until required fields are filled

### Requirement 136: Report Builder Applied Filters Display

**User Story:** As an administrator, I want to see which filters are applied to my report, so that I understand what data is included.

#### Acceptance Criteria

1. WHEN preview is generated, THE System SHALL display applied filters as chips/badges
2. THE filter chips SHALL show: module name, date range, selected statuses
3. THE filter chips SHALL be in Bahasa Melayu
4. THE filter chips SHALL be displayed above the preview table

### Requirement 137: Report Builder Loading States

**User Story:** As an administrator, I want to see loading indicators during preview generation, so that I know the system is working.

#### Acceptance Criteria

1. WHEN preview is being generated, THE System SHALL display loading indicator
2. THE loading indicator SHALL use skeleton loaders or spinner
3. THE loading state SHALL include `aria-busy="true"` for accessibility
4. THE "Jana Pratonton" button SHALL be disabled during loading

### Requirement 138: Report Builder Complete Translation Keys

**User Story:** As an administrator, I want all Report Builder UI elements to have proper Malay translations, so that no raw translation keys appear.

#### Acceptance Criteria

1. THE System SHALL provide these translation keys in `admin_pages.report_builder`:
   - `title` → "Pembina Laporan"
   - `label` → "Pembina Laporan"
   - `description` → "Bina dan eksport laporan tersuai untuk modul sistem."
   - `config.title` → "Konfigurasi Laporan"
   - `config.module` → "Modul"
   - `config.module_placeholder` → "Pilih modul"
   - `config.date_from` → "Tarikh Dari"
   - `config.date_to` → "Tarikh Hingga"
   - `config.statuses` → "Status"
   - `config.statuses_placeholder` → "Pilih status (pilihan)"
   - `modules.helpdesk` → "Meja Bantuan"
   - `modules.loans` → "Pinjaman Aset"
   - `modules.assets` → "Aset"
   - `modules.users` → "Pengguna"
   - `preview.title` → "Pratonton Laporan"
   - `preview.total_records` → "Jumlah Rekod"
   - `preview.filters_applied` → "Penapis Digunakan"
   - `preview.empty` → "Tiada rekod dijumpai"
   - `preview.empty_hint` → "Sila laraskan penapis dan jana pratonton semula."
   - `preview.not_generated` → "Pratonton belum dijana"
   - `preview.not_generated_hint` → "Pilih modul, tetapkan julat tarikh, dan klik 'Jana Pratonton'."
   - `actions.generate` → "Jana Pratonton"
   - `actions.export_csv` → "Eksport CSV"
   - `actions.export_excel` → "Eksport Excel"
   - `actions.export_pdf` → "Eksport PDF"
   - `actions.coming_soon` → "Akan Datang"
   - `messages.generating` → "Menjana pratonton..."
   - `messages.export_success` → "Laporan berjaya dieksport."
   - `messages.export_failed` → "Gagal mengeksport laporan."
   - `messages.no_data` → "Tiada data untuk dieksport."
   - `validation.module_required` → "Sila pilih modul."
   - `validation.date_from_required` → "Sila tetapkan tarikh mula."
   - `validation.date_to_required` → "Sila tetapkan tarikh akhir."
   - `validation.date_range_invalid` → "Tarikh akhir mestilah selepas tarikh mula."
   - `headers.ticket_number` → "Nombor Tiket"
   - `headers.subject` → "Subjek"
   - `headers.status` → "Status"
   - `headers.priority` → "Keutamaan"
   - `headers.created_at` → "Tarikh Dicipta"
   - `headers.resolved_at` → "Tarikh Diselesaikan"
   - `headers.application_number` → "Nombor Permohonan"
   - `headers.applicant` → "Pemohon"
   - `headers.asset_tag` → "Tag Aset"
   - `headers.asset_name` → "Nama Aset"
   - `headers.category` → "Kategori"
2. THE System SHALL NOT display any raw translation keys in the UI

---

## Phase 41: Unified Analytics Dashboard Fixes (Image 51 Observations)

### Requirement 139: Unified Analytics Dashboard Widget Deduplication

**User Story:** As an administrator, I want each widget to appear only once on the Unified Analytics Dashboard, so that I have a clean interface without duplicate KPI cards and charts.

#### Acceptance Criteria

1. WHEN the Unified Analytics Dashboard loads, THE System SHALL display each widget type only once
2. THE System SHALL NOT render widgets via both `getHeaderWidgets()`/`getFooterWidgets()` AND manual `@livewire()` calls in Blade
3. THE `unified-analytics-dashboard.blade.php` view SHALL NOT contain manual `@livewire()` calls for widgets already registered in the page class
4. THE System SHALL use Filament's native widget rendering via `getHeaderWidgets()` and `getFooterWidgets()` methods only
5. WHEN duplicate widgets are detected during development, THE System SHALL log a warning for debugging purposes

### Requirement 140: Active Loans KPI Accuracy (Item Aktif)

**User Story:** As an administrator, I want the "Item Aktif" (Active Items) KPI to accurately reflect all active loans, so that I can trust the dashboard metrics.

#### Acceptance Criteria

1. THE `UnifiedAnalyticsService::getLoanMetrics()` method SHALL use `LoanStatus::isActive()` statuses for active loan count
2. THE active loan count SHALL include loans with status: `ISSUED`, `IN_USE`, `RETURN_DUE`, `RETURNING`
3. THE System SHALL NOT use narrow definition (`issued`, `in_use` only) for active loans
4. THE "Item Aktif" KPI SHALL match the count from `LoanApplication::whereIn('status', LoanStatus::activeStatuses())->count()`
5. THE System SHALL provide `LoanStatus::activeStatuses()` static method returning array of active status values

### Requirement 141: HelpdeskTicketStatus Enum Creation

**User Story:** As a developer, I want a `HelpdeskTicketStatus` enum with consistent status values, so that helpdesk ticket statuses are type-safe and consistent across the system.

#### Acceptance Criteria

1. THE System SHALL create `App\Enums\HelpdeskTicketStatus` enum following the `LoanStatus` pattern
2. THE enum SHALL include these status cases: `OPEN`, `IN_PROGRESS`, `PENDING_INFO`, `RESOLVED`, `CLOSED`, `CANCELLED`
3. THE enum SHALL implement `label(): string` method returning localized Bahasa Melayu labels
4. THE enum SHALL implement `color(): string` method returning WCAG-compliant Filament color tokens
5. THE enum SHALL implement `isActive(): bool` method returning true for `OPEN`, `IN_PROGRESS`, `PENDING_INFO`
6. THE enum SHALL implement `isTerminal(): bool` method returning true for `RESOLVED`, `CLOSED`, `CANCELLED`
7. THE `HelpdeskTicket` model SHALL cast `status` attribute to `HelpdeskTicketStatus` enum

### Requirement 142: Notification Payload Localization

**User Story:** As a user receiving notifications, I want notification messages to display properly formatted status labels in Bahasa Melayu, so that I can understand the notification content.

#### Acceptance Criteria

1. THE `HelpdeskTicketStatusUpdated` notification SHALL NOT use `ucfirst($status)` for status display
2. THE notification payload SHALL use `HelpdeskTicketStatus::from($status)->label()` for localized status labels
3. THE notification `toArray()` method SHALL include these fields:
   - `title`: Localized notification title
   - `message`: Localized notification message
   - `action_url`: URL to view the ticket
   - `action_label`: Localized action button label ("Lihat Tiket")
4. THE notification email SHALL display status labels in proper Bahasa Melayu (not "In_progress" but "Dalam Proses")
5. THE System SHALL apply the same pattern to `LoanApplicationStatusUpdated` and other status notifications

### Requirement 143: ReportBuilder Status Options Alignment

**User Story:** As an administrator using the Report Builder, I want loan status filter options to match the `LoanStatus` enum values, so that I can filter reports accurately.

#### Acceptance Criteria

1. THE `ReportBuilder.php` loan status filter SHALL generate options from `LoanStatus::cases()`
2. THE filter options SHALL use `$status->value` as option value and `$status->label()` as option label
3. THE System SHALL NOT use hardcoded status arrays that may become out of sync with the enum
4. THE filter options SHALL be in Bahasa Melayu (using `label()` method)
5. THE same pattern SHALL apply to helpdesk ticket status filters using `HelpdeskTicketStatus::cases()`

### Requirement 144: KPI Tooltip Definitions

**User Story:** As an administrator, I want tooltips on KPI cards explaining what each metric means, so that I can understand the data being displayed.

#### Acceptance Criteria

1. THE "Item Aktif" KPI card SHALL include tooltip: "Jumlah pinjaman dengan status: Dikeluarkan, Sedang Digunakan, Perlu Dipulangkan, Dalam Proses Pemulangan"
2. THE "Tiket Tertunggak" KPI card SHALL include tooltip: "Tiket yang telah melepasi tarikh akhir SLA penyelesaian"
3. THE "Kesihatan Sistem" KPI card SHALL include tooltip: "Skor kesihatan dikira berdasarkan: Kadar penyelesaian tiket (40%), Kadar kelulusan pinjaman (35%), Ketersediaan aset (25%)"
4. THE tooltips SHALL be accessible via hover and keyboard focus
5. THE tooltips SHALL use `aria-describedby` for screen reader accessibility

---

## Phase 42: Template Laporan Pra‑konfigurasi Page Fixes (Image 52 Observations)

### Requirement 145: Report Template Frequency Label Localization

**User Story:** As an administrator, I want frequency labels on template cards to be in Bahasa Melayu, so that the interface is consistent and professional.

#### Acceptance Criteria

1. THE frequency labels on template cards SHALL be in Bahasa Melayu:
   - "Monthly" → "Bulanan"
   - "Weekly" → "Mingguan"  
   - "Daily" → "Harian"
2. THE System SHALL use translation keys instead of `ucfirst($template['frequency'])`
3. THE translation keys SHALL be: `reports.frequency.monthly`, `reports.frequency.weekly`, `reports.frequency.daily`
4. THE System SHALL NOT display English frequency labels in the Malay interface
5. THE frequency translation pattern SHALL be consistent across all report-related pages

### Requirement 146: Report Template Generation UX Enhancement

**User Story:** As an administrator, I want clear feedback when I generate a report template, so that I know where my report went and how to access it.

#### Acceptance Criteria

1. WHEN a template report is generated successfully, THE System SHALL provide actionable feedback with download/view options
2. THE success message SHALL include:
   - Confirmation that report was generated
   - File name or identifier
   - Download link or button ("Muat Turun")
   - Option to view report history ("Lihat Sejarah Laporan")
3. THE System SHALL NOT display generic "Laporan tersuai berjaya dijana" without providing access to the generated report
4. THE `ReportTemplateService` methods SHALL return complete export artifacts including:
   - `filename`: Generated file name
   - `formatted_size`: Human-readable file size
   - `download_url`: URL for downloading the report
   - `created_at`: Generation timestamp
5. THE success subheading SHALL only appear after actual report generation (not always visible)

### Requirement 147: Report Template Primary Action Visibility

**User Story:** As an administrator, I want clear primary actions on template cards, so that I know how to generate reports from templates.

#### Acceptance Criteria

1. EACH template card SHALL include a visible primary action button labeled "Jana" (Generate)
2. THE template cards MAY include secondary actions:
   - "Pratonton" (Preview) - if preview functionality exists
   - "Konfigurasi" (Configure) - if template customization is available
3. THE primary action SHALL be visually prominent (primary button styling)
4. THE secondary actions SHALL use secondary button styling
5. IF the entire card is clickable, THE System SHALL still include explicit action buttons for clarity

### Requirement 148: Report Template Service Data Export Implementation

**User Story:** As a developer, I want the ReportTemplateService to actually generate downloadable files, so that the template generation feature works end-to-end.

#### Acceptance Criteria

1. THE `ReportTemplateService` SHALL integrate with `DataExportService` to produce actual files
2. THE service methods SHALL return export metadata:
   - `data`: Report data array
   - `format`: Export format (csv, excel, pdf)
   - `metadata`: Report metadata
   - `filename`: Generated filename
   - `formatted_size`: Human-readable file size
   - `download_url`: Temporary download URL or stream response
3. THE System SHALL NOT show success notifications without providing downloadable files
4. THE export process SHALL handle errors gracefully and display appropriate error messages
5. THE generated files SHALL use Malay column headers matching the UI labels

### Requirement 149: Report Template SLA Compliance Field Fix

**User Story:** As a developer, I want the SLA compliance calculation to use correct database fields, so that SLA reports show accurate data.

#### Acceptance Criteria

1. THE `calculateHelpdeskSlaCompliance()` method SHALL use the correct SLA deadline field
2. THE System SHALL replace `$ticket->sla_deadline` with the actual field name:
   - Use `$ticket->sla_resolution_due_at` for resolution SLA compliance
   - Use `$ticket->sla_response_due_at` for response SLA compliance  
3. THE SLA compliance calculation SHALL handle null deadline values gracefully
4. THE method SHALL return accurate compliance percentages based on actual database schema
5. THE System SHALL NOT reference non-existent database fields

### Requirement 150: Report Template Status Enum Usage

**User Story:** As a developer, I want report template methods to use enum values for status checks, so that status filtering is consistent and type-safe.

#### Acceptance Criteria

1. THE `ReportTemplateService` methods SHALL use enum values instead of raw strings:
   - Replace `'in_use'` with `LoanStatus::IN_USE->value`
   - Replace `'available'` with `AssetStatus::AVAILABLE->value`
   - Replace `'resolved'`, `'open'`, `'assigned'`, `'in_progress'` with `HelpdeskTicketStatus` enum values
2. THE System SHALL create `HelpdeskTicketStatus` enum if it doesn't exist
3. THE enum usage SHALL prevent status value drift and typos
4. THE template data methods SHALL be consistent with other parts of the system
5. THE System SHALL normalize helpdesk ticket statuses if needed before enum implementation

### Requirement 151: Report Template Translation Key Standardization

**User Story:** As a developer, I want consistent translation key usage in report templates, so that the interface is maintainable and translatable.

#### Acceptance Criteria

1. THE `ReportTemplates` page class SHALL use translation keys instead of hardcoded Malay strings
2. THE System SHALL replace hardcoded strings with translation keys:
   - Page titles and descriptions
   - Button labels ("Jana", "Menjana…")
   - Success/error messages
   - Status labels and notifications
3. THE translation keys SHALL follow the `reports.*` namespace pattern
4. THE System SHALL NOT mix translation keys with hardcoded strings in the same component
5. THE Blade templates SHALL use `{{ __('reports.template.generate') }}` instead of hardcoded "Jana"

### Requirement 152: Report Template Empty State Enhancement

**User Story:** As an administrator, I want helpful empty states when no templates are available, so that I understand what templates are for and how to create them.

#### Acceptance Criteria

1. WHEN no report templates exist, THE System SHALL display contextual empty state:
   - Heading: "Tiada templat laporan"
   - Description: "Templat laporan membolehkan anda menjana laporan standard dengan cepat."
   - Call-to-action: "Cipta Templat Baharu" (if user has permission)
2. THE empty state SHALL include relevant icon (e.g., `heroicon-o-document-text`)
3. THE empty state SHALL NOT display generic "Tiada rekod dijumpai"
4. THE empty state SHALL provide guidance on what report templates are used for
5. THE call-to-action SHALL only appear for users with template creation permissions

### Requirement 153: Report Template Usage Statistics Accuracy

**User Story:** As an administrator, I want accurate usage statistics at the bottom of the template page, so that I can see which templates are most popular.

#### Acceptance Criteria

1. THE usage statistics SHALL display real data from template generation logs
2. THE statistics SHALL include:
   - Total templates available
   - Total reports generated this month
   - Most popular template name
   - Last generation timestamp
3. THE statistics SHALL update when new reports are generated
4. THE System SHALL track template usage in a `report_template_usage` table or similar
5. THE statistics SHALL be in Bahasa Melayu with proper formatting

### Requirement 154: Report Template Card Accessibility Enhancement

**User Story:** As an administrator using assistive technology, I want template cards to be fully accessible, so that I can navigate and use templates with screen readers.

#### Acceptance Criteria

1. EACH template card SHALL include proper ARIA labels:
   - `aria-label` describing the template purpose
   - `aria-describedby` linking to template description
2. THE template cards SHALL support keyboard navigation
3. THE frequency tags SHALL include `aria-label` with full frequency description
4. THE action buttons SHALL have descriptive labels for screen readers
5. THE template cards SHALL announce their state (available, generating, etc.) to screen readers

### Requirement 155: Report Template Error Handling

**User Story:** As an administrator, I want clear error messages when template generation fails, so that I can understand what went wrong and how to fix it.

#### Acceptance Criteria

1. WHEN template generation fails, THE System SHALL display specific error messages:
   - "Data tidak mencukupi untuk tempoh yang dipilih"
   - "Ralat sambungan pangkalan data"
   - "Templat tidak dijumpai atau telah dipadam"
   - "Had masa tamat untuk penjanaan laporan"
2. THE error messages SHALL be in Bahasa Melayu
3. THE System SHALL provide recovery suggestions where possible
4. THE error state SHALL include a "Cuba Lagi" (Try Again) button
5. THE System SHALL log detailed error information for debugging while showing user-friendly messages

---

## Phase 43: Pusat Eksport Data ICTServe Page Fixes (Image 53 Observations)

### Requirement 156: Export Format Label Localization

**User Story:** As an administrator, I want export format options to be in Bahasa Melayu, so that the interface is consistent and professional.

#### Acceptance Criteria

1. WHEN viewing export format dropdown, THE System SHALL display Malay labels:
   - "CSV — Nilai Dipisahkan Koma (CSV)"
   - "Excel — Hamparan Microsoft Excel (XLSX)"
   - "PDF — Format Dokumen Mudah Alih (PDF)"
2. THE format labels SHALL use translation keys from `lang/ms/exports.php`
3. THE System SHALL NOT display English text like "Comma Separated Values"
4. THE format descriptions SHALL be consistent across all export-related pages
5. THE dropdown options SHALL include format file extension in parentheses for clarity

### Requirement 157: Export Format Implementation Honesty

**User Story:** As an administrator, I want export formats to produce actual valid files, so that I can open them in the expected applications.

#### Acceptance Criteria

1. WHEN exporting as PDF, THE System SHALL generate a valid PDF file (not plain text)
2. WHEN exporting as Excel, THE System SHALL generate a valid XLSX file (not CSV renamed)
3. IF a format is not properly implemented, THE System SHALL either:
   - Disable the option with "Akan Datang" label, OR
   - Implement proper file generation using appropriate libraries
4. THE System SHALL use `barryvdh/laravel-dompdf` or equivalent for PDF generation
5. THE System SHALL use `phpoffice/phpspreadsheet` or equivalent for XLSX generation
6. THE generated files SHALL open correctly in their respective applications without warnings

### Requirement 158: Export History Persistence

**User Story:** As an administrator, I want to see my actual export history, so that I can track what was exported and re-download files.

#### Acceptance Criteria

1. THE System SHALL create a `data_exports` table to store export history
2. EACH export record SHALL include:
   - `user_id` (who triggered the export)
   - `data_type` (loans, assets, helpdesk, etc.)
   - `export_format` (csv, excel, pdf)
   - `filters` (JSON of applied filters)
   - `file_path` (storage path)
   - `file_size` (in bytes)
   - `status` (queued, processing, completed, failed)
   - `error_message` (if failed)
   - `created_at`, `completed_at`
3. THE "Eksport Terkini" table SHALL display real export history (not random/fake data)
4. THE export statistics SHALL be calculated from actual database records
5. THE System SHALL NOT use `rand()` or placeholder data in production UI

### Requirement 159: Export Download Reliability

**User Story:** As an administrator, I want reliable file downloads, so that I can access my exported data.

#### Acceptance Criteria

1. THE System SHALL store exports on a consistent disk (either `public` or via signed URLs)
2. IF using private disk, THE System SHALL provide authenticated download routes
3. EACH completed export in "Eksport Terkini" SHALL have a "Muat Turun" action button
4. THE download links SHALL remain valid for a configurable retention period (default: 7 days)
5. THE System SHALL implement scheduled cleanup of expired export files
6. THE UI copy SHALL accurately describe download behavior (no false "auto-download" claims)

### Requirement 160: Compression Behavior Honesty

**User Story:** As an administrator, I want honest compression behavior, so that I understand what happens to my exported files.

#### Acceptance Criteria

1. WHEN "Mampat Fail Besar" is enabled, THE System SHALL create actual ZIP archives
2. THE System SHALL NOT truncate data and call it "compression"
3. THE helper text SHALL clearly state: "Fail akan dimuat turun sebagai .zip jika melebihi 10MB"
4. THE System SHALL auto-enable compression when estimated file size exceeds threshold
5. IF data must be limited, THE System SHALL use pagination/chunking with clear messaging
6. THE compressed file SHALL contain the complete export (not truncated)

### Requirement 161: Quick Export Behavior Clarity

**User Story:** As an administrator, I want to understand what "Eksport Pantas" does, so that I can use it appropriately.

#### Acceptance Criteria

1. THE "Eksport Pantas" button SHALL have helper text explaining its behavior
2. THE helper text SHALL state: "Guna tetapan lalai (bulan semasa + CSV)"
3. THE System SHALL clearly indicate which filters/parameters are used by quick export
4. WHEN custom parameters are set, THE System SHALL either:
   - Disable quick export, OR
   - Show warning that custom parameters will be ignored
5. THE quick export behavior SHALL be consistent and predictable

### Requirement 162: Date Range Validation

**User Story:** As an administrator, I want date range validation, so that I don't accidentally create invalid or very large exports.

#### Acceptance Criteria

1. THE System SHALL validate that end date is not before start date
2. WHEN end date < start date, THE System SHALL display error: "Tarikh tamat tidak boleh sebelum tarikh mula"
3. WHEN date range exceeds 365 days, THE System SHALL display warning: "Eksport mungkin mengambil masa lebih lama untuk julat tarikh yang besar"
4. THE System SHALL provide sensible default date range (current month)
5. THE validation messages SHALL be in Bahasa Melayu

### Requirement 163: Export Service Consolidation

**User Story:** As a developer, I want consistent export behavior across all export pages, so that the codebase is maintainable.

#### Acceptance Criteria

1. THE System SHALL use a single export service interface for all export operations
2. ALL export pages (DataExportCenter, ReportBuilder, ReportTemplates) SHALL produce consistent output
3. THE export column headers SHALL be in Bahasa Melayu across all export types
4. THE file naming convention SHALL be consistent: `{type}_{date_from}_{date_to}.{ext}`
5. THE export services SHALL share common configuration for formats, compression, and storage

### Requirement 164: Export Status States

**User Story:** As an administrator, I want to see export status states, so that I know if exports are processing, completed, or failed.

#### Acceptance Criteria

1. THE "Eksport Terkini" table SHALL display status badges:
   - "Dalam Giliran" (queued) - gray badge
   - "Sedang Diproses" (processing) - blue badge with spinner
   - "Selesai" (completed) - green badge
   - "Gagal" (failed) - red badge
2. FAILED exports SHALL display error reason on hover/click
3. COMPLETED exports SHALL show file size and download action
4. PROCESSING exports SHALL show progress indicator if available
5. THE status badges SHALL use consistent Filament color tokens

### Requirement 165: Export Page Accessibility

**User Story:** As an administrator using assistive technology, I want the export page to be accessible, so that I can use all features.

#### Acceptance Criteria

1. THE export format dropdown SHALL have proper ARIA labels
2. THE date pickers SHALL be keyboard accessible
3. THE export buttons SHALL have descriptive labels for screen readers
4. THE status badges SHALL include text alternatives (not just color)
5. THE "Eksport Terkini" table SHALL have proper table semantics and headers

---

## Phase 44: Dashboard Visualisasi Data Page Fixes (Image 54 Observations)

### Requirement 166: Real Chart Rendering Implementation

**User Story:** As an administrator, I want to see actual charts with real data, so that I can analyze system performance visually.

#### Acceptance Criteria

1. THE dashboard SHALL render actual Chart.js charts (not placeholder gray boxes)
2. EACH chart panel SHALL display real data from `DataVisualizationService` methods:
   - Ticket Trends: `getTicketTrendsChartData()`
   - Asset Utilization: `getAssetUtilizationChartData()`
   - SLA Compliance: `getSlaComplianceChartData()`
   - Priority Distribution: `getPriorityDistributionChartData()`
   - Resolution Time Trends: `getResolutionTimeTrendsChartData()`
3. THE charts SHALL be interactive (hover tooltips, click events)
4. THE charts SHALL support dark mode color schemes
5. THE placeholder boxes with icons SHALL be completely removed

### Requirement 167: Chart Loading and Empty States

**User Story:** As an administrator, I want clear feedback when charts are loading or have no data, so that I understand the dashboard state.

#### Acceptance Criteria

1. WHILE charts are loading, THE System SHALL display skeleton loaders
2. WHEN no data exists for a chart, THE System SHALL display: "Tiada data dalam tempoh ini"
3. WHEN chart loading fails, THE System SHALL display: "Gagal memuat carta" with "Muat Semula" button
4. THE loading states SHALL use `aria-busy="true"` for accessibility
5. THE empty states SHALL include helpful guidance on why data might be missing

### Requirement 168: SLA Field Reference Corrections

**User Story:** As a developer, I want correct database field references, so that SLA calculations work properly.

#### Acceptance Criteria

1. THE System SHALL use `sla_resolution_due_at` instead of non-existent `sla_deadline` field
2. THE System SHALL use `subject` instead of non-existent `title` field for tickets
3. THE SLA compliance calculation SHALL compare `resolved_at` with `sla_resolution_due_at`
4. THE SLA drilldown SHALL query tickets where `sla_resolution_due_at < now()` and status not resolved/closed
5. THE System SHALL NOT reference any non-existent database fields

### Requirement 169: Status Enum Usage in Visualization

**User Story:** As a developer, I want visualization queries to use enum values, so that status filtering is consistent.

#### Acceptance Criteria

1. THE loan status filters SHALL use `LoanStatus::APPROVED->value`, `LoanStatus::REJECTED->value`, etc.
2. THE asset utilization "in use" check SHALL use `LoanStatus::IN_USE->value`
3. THE pending approval check SHALL use `LoanStatus::PENDING_APPROVAL->value`
4. THE System SHALL NOT use hardcoded status strings like `'approved'`, `'in_use'`
5. THE helpdesk status checks SHALL use `HelpdeskTicketStatus` enum when available

### Requirement 170: Chart Query Performance Optimization

**User Story:** As an administrator, I want charts to load quickly, so that the dashboard feels responsive.

#### Acceptance Criteria

1. THE ticket trends query SHALL use grouped aggregation (not N+1 per-day queries)
2. THE System SHALL use `selectRaw()` with `GROUP BY DATE(created_at)` for daily counts
3. THE chart data SHALL be cached with appropriate TTL (5 minutes recommended)
4. THE System SHALL fill missing dates in PHP (not via additional queries)
5. THE dashboard load time SHALL be under 2 seconds for 30-day range

### Requirement 171: Chart Export Functionality

**User Story:** As an administrator, I want to export charts as images, so that I can include them in reports.

#### Acceptance Criteria

1. EACH chart SHALL have a "Muat turun PNG" button for client-side export
2. THE export SHALL use Chart.js `toBase64Image()` for PNG generation
3. THE exported filename SHALL follow pattern: `{chart_name}_{timestamp}.png`
4. THE "Eksport Dashboard" header action SHALL export all charts or underlying data
5. THE System SHALL NOT show "export success" without providing a downloadable file

### Requirement 172: Badge Label Localization

**User Story:** As an administrator, I want all UI labels in Bahasa Melayu, so that the interface is consistent.

#### Acceptance Criteria

1. THE "Real-time" badge SHALL display "Masa Nyata"
2. THE "Interactive" badge SHALL display "Interaktif"
3. THE export format options SHALL be in Bahasa Melayu:
   - "Imej PNG"
   - "Dokumen PDF"
   - "Vektor SVG"
4. ALL chart titles and labels SHALL use translation keys
5. THE System SHALL NOT display English text in the Malay UI

### Requirement 173: Export Modal Localization

**User Story:** As an administrator, I want export options in Bahasa Melayu, so that I understand what I'm exporting.

#### Acceptance Criteria

1. THE export format dropdown SHALL display Malay labels
2. THE export modal title SHALL be "Eksport Carta"
3. THE export button labels SHALL be "Eksport" and "Batal"
4. THE success notification SHALL be in Bahasa Melayu
5. THE per-chart export button SHALL be labeled "Eksport Carta" (vs header "Eksport Semua")

### Requirement 174: Duplicate Export Affordance Clarity

**User Story:** As an administrator, I want to understand the difference between export options, so that I export the right content.

#### Acceptance Criteria

1. THE header "Eksport Dashboard" SHALL be renamed to "Eksport Semua"
2. THE per-card export buttons SHALL be labeled "Eksport Carta"
3. THE header export SHALL include tooltip: "Eksport semua carta dan data"
4. THE per-card export SHALL include tooltip: "Eksport carta ini sahaja"
5. THE export behavior difference SHALL be clearly communicated
