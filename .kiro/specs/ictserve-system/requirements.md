# Requirements Document

## Introduction

The ICTServe System is a comprehensive digital platform for managing ICT services within the Ministry of Tourism, Arts and Culture Malaysia (MOTAC). This system operates on a **hybrid architecture** combining guest-accessible public forms with authenticated internal portal features for MOTAC staff. The system encompasses two main integrated modules: the Helpdesk Ticketing System and the ICT Asset Loan Management System.

**Critical Architecture**: The system provides a dual-access model:

1. **Guest Access (No Login)**: Public forms for helpdesk tickets and asset loan applications, email-based approvals for Grade 41+ officers, and status tracking via email links
2. **Authenticated Access (Login Required)**: Internal portal for staff to view their submissions, manage profiles, and access advanced features
3. **Admin Access (Filament Panel)**: Backend management for admin and superuser roles only

The system emphasizes email-based workflows for guest interactions, authenticated portal features for staff convenience, automated notifications, and comprehensive backend management while adhering to WCAG 2.2 Level AA accessibility standards and Core Web Vitals performance targets.

**Version**: 3.0.0 (SemVer)
**Last Updated**: 31 Oktober 2025
**Status**: Active - Aligned with D00-D15 Standards
**Classification**: Restricted - Internal MOTAC BPM
**Standards Compliance**: ISO/IEC/IEEE 12207, 29148, 15288, WCAG 2.2 AA, MyGOV Digital Service Standards v2.1.0, PDPA 2010

## Glossary

- **ICTServe_System**: The complete integrated platform for managing ICT services at MOTAC BPM with **hybrid architecture** (guest + authenticated access)
- **Helpdesk_Module**: Digital ticketing system for managing ICT support requests via public forms (guest) and internal portal (authenticated staff)
- **Asset_Loan_Module**: Digital system for managing ICT equipment loan lifecycle from public application (guest) to authenticated staff tracking and email-based approval
- **BPM_MOTAC**: Bahagian Pengurusan Maklumat (Information Management Division) of MOTAC
- **Staf_MOTAC**: MOTAC staff members who can access system via guest forms (no login) OR authenticated portal (with login) for enhanced features
- **Pegawai_Penyokong**: Grade 41+ officers who approve loan applications via **email links** (no system login required) or through authenticated portal
- **Admin**: Administrative users with Filament admin panel access for backend system management (login required, operational role)
- **Superuser**: Super administrative users with full Filament admin access and system configuration rights (login required, governance role)
- **Hybrid_Architecture**: System design combining guest-accessible public forms with authenticated internal portal features
- **Guest_Access**: Public forms accessible without authentication for quick submissions
- **Authenticated_Access**: Internal portal requiring login for staff to view submissions, manage profiles, and access advanced features
- **Public_Forms**: Guest-accessible forms for helpdesk tickets and asset loan applications (no login required)
- **Internal_Portal**: Authenticated area for staff to manage their submissions and access enhanced features (login required)
- **Email_Workflow**: Primary interaction method for guest approvals and notifications
- **Filament_Admin**: Backend administrative interface accessible only to admin and superuser roles
- **Audit_Trail**: Complete chronological record of all system activities and changes
- **SLA**: Service Level Agreement defining response and resolution time targets
- **Integrasi_Modul**: Seamless integration between helpdesk and asset loan modules in admin backend
- **Dashboard_Admin**: Unified admin dashboard showing metrics from both modules (Filament-based)
- **Sistem_Notifikasi**: Automated email notification system for all user interactions
- **Kalendar_Tempahan**: Visual booking calendar for asset availability management (admin view)
- **Matriks_Kelulusan**: Approval matrix based on applicant grade and asset value
- **Responsif_Design**: User interface that adapts to desktop, tablet, and mobile devices
- **Frontend_Components**: Unified component library for consistent public-facing interfaces
- **Bilingual_Support**: Comprehensive Bahasa Melayu and English language support
- **WCAG_Compliance**: Web Content Accessibility Guidelines 2.2 Level AA compliance with strict color contrast ratios (4.5:1 text, 3:1 UI components)
- **MOTAC_Branding**: Consistent visual identity and branding across all interfaces using compliant color palette
- **Core_Web_Vitals**: Performance standards with LCP <2.5s, FID <100ms, CLS <0.1, TTFB <600ms targets
- **Compliant_Color_Palette**: WCAG 2.2 AA compliant colors - Primary #0056b3 (6.8:1), Success #198754 (4.9:1), Warning #ff8c00 (4.5:1), Danger #b50c0c (8.2:1)
- **Deprecated_Colors**: Old non-compliant colors to be removed - Warning Yellow #F1C40F, Danger Red #E74C3C
- **Focus_Indicators**: Visible focus indicators with 3-4px outline, 2px offset, and 3:1 contrast ratio minimum for keyboard navigation
- **Touch_Targets**: Minimum 44×44px interactive elements for mobile accessibility compliance
- **Semantic_HTML**: Proper HTML5 semantic elements (header, nav, main, footer) with ARIA landmarks
- **Image_Optimization**: WebP format with fallbacks, explicit dimensions, lazy loading, and fetchpriority attributes
- **Asset_Optimization**: Vite configuration with Gzip/Brotli compression, code splitting, and minification
- **Performance_Budgets**: Automated monitoring and alerting when Core Web Vitals thresholds are exceeded
- **Inline_Styles_Refactoring**: All inline style attributes and style blocks must be refactored into external CSS files and imported via Vite
- **Critical_Images**: Header logos and hero images must not have loading="lazy" and must have fetchpriority="high"
- **Non_Critical_Images**: Footer logos and non-essential images must have loading="lazy" and fetchpriority="low"
- **Browser_Compatibility**: Chrome 90+, Firefox 88+, Safari 14+, Edge 90+ (Chromium) support matrix
- **Lighthouse_Targets**: Performance 90+, Accessibility 100, Best Practices 100, SEO 100 scores required
- **Guest_Forms**: Public-facing forms accessible without authentication for all MOTAC staff
- **Email_Workflows**: Primary communication method using automated email notifications and approval links
- **Session_Locale**: Language preference persistence using session and cookie only (no user profile storage)
- **Frontend_Audit**: Comprehensive review of existing Blade, Livewire, and Volt components against D00-D15 standards
- **Compliance_Upgrade**: Process of modifying existing components to meet D00-D15 requirements
- **Component_Documentation**: Standardized documentation for all frontend components per D10 and D12 standards
- **Component_Metadata**: Standardized metadata headers including name, description, author, trace references, standards compliance, and timestamps
- **Requirements_Traceability**: Traceability links between frontend components and D03 software requirements
- **Email_Template_Compliance**: Email template compliance with accessibility and branding standards
- **Error_Page_Compliance**: Error page compliance with accessibility and user experience standards
- **Admin_Interface_Compliance**: Filament admin interface compliance with D00-D15 standards
- **Livewire_Komponen**: Dynamic UI components using Livewire 3 for real-time interactions
- **Volt_Komponen**: Single-file Livewire components for simplified development
- **Sistem_Komponen**: Reusable Blade, Livewire, and Volt components following design system

## Requirements

### Requirement 1

**User Story:** As a MOTAC staff member, I want to access the ICTServe system through both guest forms (quick access) and authenticated portal (enhanced features), so that I can submit requests quickly when needed or manage my submissions comprehensively when logged in.

#### Acceptance Criteria (Requirement 1)

1. WHEN a MOTAC staff member accesses the ICTServe portal, THE ICTServe_System SHALL provide dual access options: guest forms (no login required) for quick submissions AND authenticated portal (login required) for enhanced features
2. WHEN a staff member submits a helpdesk ticket as a guest, THE ICTServe_System SHALL generate a unique ticket number in format HD\[YYYY\]\[000001-999999\], send confirmation email within 60 seconds with ticket details, and provide option to claim ticket in authenticated portal
3. WHEN a staff member logs into the authenticated portal, THE ICTServe_System SHALL display their complete submission history including all helpdesk tickets and asset loan applications, allow profile management for contact information and preferences, enable internal comments on submissions, and provide real-time status tracking
4. WHEN a staff member submits an asset loan application (guest or authenticated), THE ICTServe_System SHALL send email notification within 60 seconds to the appropriate approving officer (Grade 41+) with secure approval/decline links containing time-limited tokens valid for 7 days
5. THE ICTServe_System SHALL maintain WCAG 2.2 Level AA compliant UI/UX design across all interfaces (guest forms, authenticated portal, admin panel) using unified component library with compliant color palette achieving minimum 4.5:1 contrast ratio for text and 3:1 for UI components
6. WHERE approving officers receive loan applications, THE ICTServe_System SHALL allow email-based approval through secure token-based links (no login required) OR approval through authenticated portal (login required) with both methods updating application status within 5 seconds

### Requirement 2

**User Story:** As an admin user, I want comprehensive system integration between helpdesk and asset loan modules through the Filament admin panel, so that I can manage all backend operations and maintain data consistency.

#### Acceptance Criteria (Requirement 2)

1. WHEN an admin accesses the Filament admin panel, THE ICTServe_System SHALL provide unified access to both helpdesk and asset loan management interfaces through a single navigation menu with role-based visibility
2. WHEN a helpdesk ticket relates to a loaned asset, THE ICTServe_System SHALL automatically link the ticket to the relevant asset record using asset_id foreign key relationship and display the linkage in both ticket and asset detail views
3. WHEN an asset is returned with condition marked as damaged or faulty, THE ICTServe_System SHALL automatically create a maintenance ticket in the helpdesk module within 5 seconds with asset details, damage description, and assigned to maintenance category
4. THE ICTServe_System SHALL maintain a single source of truth for staff data, asset information, and organizational structure using normalized database schema with referential integrity constraints
5. WHEN asset history is requested in the admin panel, THE ICTServe_System SHALL display both complete loan history and all related helpdesk tickets in chronological order with pagination of 25 records per page

### Requirement 3

**User Story:** As an admin or superuser, I want a unified administrative interface using Filament with role-based access control, so that I can efficiently manage both asset inventory and helpdesk operations while staff users access appropriate features through the authenticated portal.

#### Acceptance Criteria (Requirement 3)

1. THE ICTServe_System SHALL provide a Filament-based admin panel accessible to admin and superuser roles with comprehensive backend management capabilities including CRUD operations for all entities, reporting, and system configuration
2. THE ICTServe_System SHALL implement role-based access control (RBAC) with four distinct roles: staff (authenticated portal access to own submissions), approver (Grade 41+ approval rights for loan applications), admin (operational management of helpdesk and assets), and superuser (full system governance including user management and configuration)
3. WHEN managing assets in the admin panel, THE ICTServe_System SHALL display related helpdesk tickets and complete maintenance history in a tabbed interface with WCAG 2.2 Level AA compliant color contrast ratios and keyboard navigation
4. THE ICTServe_System SHALL allow superuser to create, update, and delete user accounts and assign roles (staff, approver, admin, superuser) based on organizational hierarchy with audit logging of all role changes
5. THE ICTServe_System SHALL provide integrated dashboard analytics displaying KPIs from both helpdesk (ticket volume, resolution time, SLA compliance) and asset loan operations (utilization rate, overdue items, approval time) using compliant color palette and accessible data visualizations with alternative text
6. WHERE data export is required, THE ICTServe_System SHALL generate unified reports in CSV, PDF, and Excel formats combining data from both modules with proper column headers, accessible table structure, and metadata
7. THE ICTServe_System SHALL maintain comprehensive audit trails for all administrative actions performed by admin and superuser roles including timestamp, user identifier, action type, affected entity, and before/after values

### Requirement 4

**User Story:** As a system stakeholder, I want the ICTServe system to be built using modern Laravel 12 architecture with Livewire 3 and Volt, so that the system is maintainable, scalable, and follows current best practices.

#### Acceptance Criteria (Requirement 4)

1. THE ICTServe_System SHALL be built using Laravel 12 framework with PHP 8.2 or higher following MVC architecture patterns with controllers in app/Http/Controllers, models in app/Models, and views in resources/views
2. THE ICTServe_System SHALL implement Livewire 3 components for dynamic user interactions without page refreshes including real-time form validation, live search, and dynamic content updates
3. WHERE single-file components reduce complexity, THE ICTServe_System SHALL use Livewire Volt for components with fewer than 100 lines of PHP logic
4. THE ICTServe_System SHALL use Blade templating engine with consistent component library stored in resources/views/components across all modules following naming convention kebab-case
5. THE ICTServe_System SHALL implement Eloquent ORM with proper relationships (belongsTo, hasMany, belongsToMany) between helpdesk tickets, asset loans, users, divisions, and organizational entities with foreign key constraints

### Requirement 5

**User Story:** As a compliance officer, I want the ICTServe system to meet all MOTAC standards and international compliance requirements, so that the system adheres to regulatory and accessibility standards.

#### Acceptance Criteria (Requirement 5)

1. THE ICTServe_System SHALL comply with WCAG 2.2 Level AA accessibility standards for all user interfaces including minimum 4.5:1 contrast ratio for text, keyboard navigation support, ARIA attributes, and screen reader compatibility
2. THE ICTServe_System SHALL implement PDPA (Personal Data Protection Act) 2010 compliance for data handling including consent management, data retention policies, secure storage with encryption, and data subject rights (access, correction, deletion)
3. THE ICTServe_System SHALL follow ISO/IEC/IEEE standards 12207 (software lifecycle), 29148 (requirements engineering), and 15288 (system engineering) as specified in D00-D14 documentation with traceability matrices
4. THE ICTServe_System SHALL provide bilingual support with Bahasa Melayu as primary language and English as secondary language for all user interfaces, email templates, error messages, and system notifications with language switcher accessible on every page
5. THE ICTServe_System SHALL implement comprehensive audit trails meeting Malaysian government compliance requirements including 7-year retention period, immutable logs, timestamp accuracy within 1 second, and complete action history

### Requirement 6

**User Story:** As a guest user accessing ICTServe services, I want responsive and accessible user interfaces that work across all devices with WCAG 2.2 Level AA compliance, so that I can access ICTServe services from desktop, tablet, or mobile devices regardless of my abilities.

#### Acceptance Criteria (Requirement 6)

1. THE ICTServe_System SHALL implement responsive design using Tailwind CSS framework supporting desktop, tablet, and mobile viewports with minimum 44×44px touch targets
2. THE ICTServe_System SHALL ensure all interactive elements are accessible via keyboard navigation with visible focus indicators meeting 3:1 contrast ratio minimum
3. THE ICTServe_System SHALL maintain **minimum 4.5:1 color contrast ratio** for all text and **3:1 contrast ratio** for UI components using the compliant color palette
4. THE ICTServe_System SHALL provide clear visual feedback for all user actions and system states using accessible color combinations and not relying on color alone
5. WHERE forms are used, THE ICTServe_System SHALL implement real-time validation with clear error messaging, proper ARIA attributes, and screen reader announcements

### Requirement 7

**User Story:** As a system administrator, I want robust data management and integration capabilities, so that the system can integrate with existing MOTAC systems and maintain data integrity.

#### Acceptance Criteria (Requirement 7)

1. THE ICTServe_System SHALL use MySQL 8.0 or higher database with proper indexing on foreign keys and frequently queried columns, and foreign key constraints with CASCADE or RESTRICT actions for referential integrity
2. THE ICTServe_System SHALL implement Redis 7.0 or higher for caching frequently accessed data with TTL of 3600 seconds and session management with 7200 seconds timeout
3. WHERE external integration is required, THE ICTServe_System SHALL provide RESTful API endpoints following OpenAPI 3.0 specification for HRMIS staff data lookup and email notification systems with authentication tokens and rate limiting of 100 requests per minute
4. THE ICTServe_System SHALL implement automated daily backup procedures at 02:00 MYT with Recovery Time Objective (RTO) of 4 hours and Recovery Point Objective (RPO) of 24 hours, storing backups in geographically separate location
5. THE ICTServe_System SHALL maintain referential integrity between helpdesk tickets, asset loans, users, divisions, grades, and organizational data using foreign key constraints with appropriate CASCADE or RESTRICT delete actions

### Requirement 8

**User Story:** As a system stakeholder, I want comprehensive monitoring, reporting, and analytics capabilities, so that I can track system performance and make data-driven decisions.

#### Acceptance Criteria (Requirement 8)

1. THE ICTServe_System SHALL provide unified dashboard analytics combining metrics from both helpdesk (total tickets, open tickets, average resolution time, SLA compliance percentage) and asset loan modules (total loans, active loans, overdue items, asset utilization rate) with data refresh every 300 seconds
2. THE ICTServe_System SHALL generate automated reports on daily, weekly, and monthly schedules covering system usage statistics, SLA compliance metrics, and asset utilization rates with email delivery to designated admin users
3. THE ICTServe_System SHALL implement real-time monitoring of system performance (response time, database query time, cache hit rate) and availability (uptime percentage, failed requests) with metrics collected every 60 seconds
4. THE ICTServe_System SHALL provide configurable alerts via email and admin panel notifications for SLA breaches (within 15 minutes of breach), overdue asset returns (24 hours before due date), and critical system issues (within 5 minutes of detection)
5. WHERE business intelligence is required, THE ICTServe_System SHALL export data in CSV, PDF, and Excel (XLSX) formats with proper column headers, data formatting, and file size limit of 50MB per export

### Requirement 9

**User Story:** As a security administrator, I want comprehensive security controls and audit capabilities, so that the system maintains data security and provides complete audit trails.

#### Acceptance Criteria (Requirement 9)

1. THE ICTServe_System SHALL implement role-based access control (RBAC) with four distinct roles: staff (authenticated portal access), approver (Grade 41+ approval rights), admin (operational management), and superuser (full system governance)
2. THE ICTServe_System SHALL log all admin actions, guest form submissions, authenticated user actions, and system changes using Laravel Auditing package with timestamp, user identifier, action type, and affected data
3. THE ICTServe_System SHALL implement secure authentication for authenticated portal and admin panel access using Laravel Breeze or Jetstream with password hashing, session management, and CSRF protection
4. THE ICTServe_System SHALL encrypt sensitive data at rest using AES-256 encryption and in transit using TLS 1.3 or higher with valid certificates
5. WHERE audit requirements exist, THE ICTServe_System SHALL maintain immutable audit logs for minimum 7 years including all guest form submissions, authenticated user actions, email-based approvals, and administrative changes

### Requirement 10

**User Story:** As a system user, I want automated workflow management and notification systems, so that I receive timely updates and the system handles routine processes automatically.

#### Acceptance Criteria (Requirement 10)

1. THE ICTServe_System SHALL implement automated email notifications within 60 seconds for all status changes (ticket assignment, resolution, loan approval/rejection) and important events (SLA breach, overdue returns) using queued jobs
2. THE ICTServe_System SHALL use Laravel Queue system with Redis driver for background processing of email notifications, report generation, and data exports with retry mechanism of 3 attempts and exponential backoff
3. WHEN SLA thresholds are within 25% of breach time, THE ICTServe_System SHALL automatically escalate tickets to next level supervisor and send email alerts to admin users with escalation reason and ticket details
4. THE ICTServe_System SHALL implement automated reminder systems sending email notifications 48 hours before asset return due date, on due date, and daily for overdue items until returned
5. WHERE workflow automation is beneficial, THE ICTServe_System SHALL provide configurable business rules and triggers accessible to superuser through admin panel including condition definitions, action specifications, and enable/disable toggles

### Requirement 11

**User Story:** As a MOTAC staff member, I want accessible and responsive interfaces with bilingual support across both guest forms and authenticated portal, so that I can easily submit and manage helpdesk tickets and asset loan applications from any device.

#### Acceptance Criteria (Requirement 11)

1. THE ICTServe_System SHALL provide public-facing guest forms that comply with WCAG 2.2 Level AA accessibility standards with no authentication required
2. THE ICTServe_System SHALL provide authenticated portal interfaces that comply with WCAG 2.2 Level AA accessibility standards for logged-in staff
3. THE ICTServe_System SHALL implement responsive design using Tailwind CSS framework supporting desktop, tablet, and mobile viewports across all interfaces
4. THE ICTServe_System SHALL provide bilingual support (Bahasa Melayu primary, English secondary) for all guest forms, authenticated portal, and admin interfaces
5. THE ICTServe_System SHALL implement real-time form validation with clear error messaging for both guest and authenticated submissions
6. WHERE form submission occurs (guest or authenticated), THE ICTServe_System SHALL provide immediate confirmation with ticket/application number and send confirmation email
7. THE ICTServe_System SHALL allow staff to optionally claim guest submissions in the authenticated portal by matching email addresses

### Requirement 12

**User Story:** As an approving officer (Grade 41+), I want to approve or decline asset loan applications via email links without logging into the system, so that I can quickly process requests from anywhere.

#### Acceptance Criteria (Requirement 12)

1. WHEN an asset loan application is submitted, THE ICTServe_System SHALL send email notification to the appropriate approving officer based on applicant grade and asset value
2. THE ICTServe_System SHALL include secure approval and decline links in the email notification with time-limited tokens
3. WHEN an approving officer clicks an approval/decline link, THE ICTServe_System SHALL process the decision and update the application status
4. THE ICTServe_System SHALL send confirmation email to both the applicant and approving officer after decision is processed
5. WHERE approval links expire, THE ICTServe_System SHALL notify admin users to manually process the application

### Requirement 13

**User Story:** As an admin user, I want to manage helpdesk tickets by assigning them to appropriate divisions or external agencies, so that issues are resolved by the right teams.

#### Acceptance Criteria (Requirement 13)

1. WHEN a helpdesk ticket is submitted via public form, THE ICTServe_System SHALL notify admin users via email and admin panel dashboard
2. THE ICTServe_System SHALL allow admin users to assign tickets to MOTAC divisions or external agencies through the Filament admin panel
3. THE ICTServe_System SHALL send email notifications to assigned divisions/agencies with ticket details and resolution instructions
4. THE ICTServe_System SHALL track ticket status and send automated reminders for pending tickets
5. WHERE tickets are resolved, THE ICTServe_System SHALL send confirmation email to the original requester with resolution details

### Requirement 14

**User Story:** As a system stakeholder, I want unified frontend components and interfaces across all public-facing pages, so that users have a consistent experience when accessing ICTServe services.

#### Acceptance Criteria (Requirement 14)

1. THE ICTServe_System SHALL implement a unified component library for all public-facing interfaces including forms, navigation, and feedback components
2. THE ICTServe_System SHALL provide responsive design using Tailwind CSS framework supporting desktop, tablet, and mobile viewports
3. THE ICTServe_System SHALL ensure WCAG 2.2 Level AA accessibility compliance across all public interfaces
4. THE ICTServe_System SHALL implement comprehensive bilingual support (Bahasa Melayu primary, English secondary) with language switcher
5. WHERE MOTAC branding is required, THE ICTServe_System SHALL maintain consistent visual identity across all public pages

### Requirement 15

**User Story:** As a frontend developer, I want modern component architecture using Livewire 3 and Volt, so that the system provides interactive user experiences while maintaining code maintainability.

#### Acceptance Criteria (Requirement 15)

1. THE ICTServe_System SHALL implement Livewire 3 components for dynamic user interactions in public forms and admin interfaces
2. WHERE appropriate, THE ICTServe_System SHALL use Livewire Volt for single-file components to reduce complexity
3. THE ICTServe_System SHALL implement real-time form validation and user feedback using Livewire reactive properties
4. THE ICTServe_System SHALL provide optimized component performance with caching and lazy loading strategies
5. WHERE component reusability is beneficial, THE ICTServe_System SHALL create shared component library accessible across all modules

### Requirement 16

**User Story:** As a compliance officer, I want all existing frontend components audited against D00-D15 standards, so that I can identify gaps and ensure the system meets all documentation and accessibility requirements.

#### Acceptance Criteria (Requirement 16)

1. THE ICTServe_System SHALL systematically review all Blade templates, Livewire components, Volt components, email templates, error pages, and Filament admin interfaces against D03-D15 standards
2. THE ICTServe_System SHALL generate a comprehensive compliance report identifying gaps in accessibility, documentation, branding, performance, metadata, and requirements traceability
3. THE ICTServe_System SHALL categorize findings by severity (critical, high, medium, low) and compliance area
4. THE ICTServe_System SHALL use existing compliant components as reference standards for compliance validation
5. WHERE components fail D00-D15 standards, THE ICTServe_System SHALL document specific violations and provide corrective actions

### Requirement 17

**User Story:** As a developer and maintainer, I want all frontend components to have standardized metadata and requirements traceability, so that I can efficiently understand, maintain, and validate component compliance.

#### Acceptance Criteria (Requirement 17)

1. THE ICTServe_System SHALL include standardized header comments with name, description, author, trace references to D00-D15 standards, and timestamps
2. THE ICTServe_System SHALL link each component to specific D03 software requirements and D04 design specifications
3. THE ICTServe_System SHALL document accessibility features, supported browsers, and WCAG 2.2 compliance level
4. THE ICTServe_System SHALL include usage examples and integration guidelines per D10 documentation standards
5. WHERE components are updated, THE ICTServe_System SHALL maintain version history and change tracking per D11 specifications

### Requirement 18

**User Story:** As an administrator and email recipient, I want all email templates and error pages to meet D00-D15 compliance standards, so that all user touchpoints provide consistent, accessible, and professional experiences.

#### Acceptance Criteria (Requirement 18)

1. THE ICTServe_System SHALL ensure all email templates meet WCAG 2.2 Level AA accessibility standards with proper semantic HTML and compliant color contrast ratios
2. THE ICTServe_System SHALL implement MOTAC branding and bilingual support per D14 and D15 standards in all email templates using the compliant color palette
3. THE ICTServe_System SHALL ensure all error pages provide accessible, helpful, and branded user experiences with proper ARIA attributes and semantic structure
4. THE ICTServe_System SHALL ensure Filament admin interfaces meet D00-D15 standards for accessibility, documentation, and branding using compliant colors and WCAG 2.2 Level AA requirements
5. THE ICTServe_System SHALL include proper metadata, documentation, and testing procedures per D10 and D11 standards with Core Web Vitals performance monitoring

### Requirement 19

**User Story:** As a system stakeholder, I want the ICTServe system to achieve strict performance and accessibility standards with Lighthouse 90+ Performance scores, so that all users have fast, accessible experiences that meet government compliance requirements.

#### Acceptance Criteria (Requirement 19)

1. THE ICTServe_System SHALL achieve Core Web Vitals targets: LCP <2.5 seconds, FID <100 milliseconds, CLS <0.1, and TTFB <600 milliseconds on all public pages
2. THE ICTServe_System SHALL achieve Lighthouse scores of 90+ for Performance and 100 for Accessibility on all public-facing pages with automated monitoring
3. THE ICTServe_System SHALL implement comprehensive image optimization: WebP format with JPEG fallbacks using picture elements, explicit width/height attributes, critical images with fetchpriority="high", non-critical images with loading="lazy" and fetchpriority="low"
4. THE ICTServe_System SHALL implement Vite configuration optimization: Gzip and Brotli compression, code splitting with manual chunks for vendor/livewire, Terser minification with drop_console in production, correct TailwindCSS content paths for purging
5. THE ICTServe_System SHALL use the compliant color palette exclusively: Primary #0056b3 (6.8:1), Success #198754 (4.9:1), Warning #ff8c00 (4.5:1), Danger #b50c0c (8.2:1) and remove all instances of deprecated Warning Yellow #F1C40F and Danger Red #E74C3C

### Requirement 20

**User Story:** As a MOTAC staff member, I want the language switcher to work seamlessly without requiring user accounts, so that I can switch between Bahasa Melayu and English while using guest-only forms.

#### Acceptance Criteria (Requirement 20)

1. THE ICTServe_System SHALL implement language switcher that persists preferences using **session and cookie only** (no user profile storage, no users.locale database column)
2. WHEN a guest user switches language, THE ICTServe_System SHALL store the preference in session storage and browser cookie for future visits without any authentication
3. THE ICTServe_System SHALL completely remove all logic related to users.locale database column, User Profile persistence, and Auth::user()->update(['locale' => $locale]) from language switching
4. THE ICTServe_System SHALL implement simplified language resolution priority: Session → Cookie → Browser → Fallback (no user profile in chain, no authentication required)
5. WHERE language preference is set, THE ICTServe_System SHALL maintain the selection across all guest form interactions and provide WCAG 2.2 Level AA compliant language switcher with 44×44px touch targets and keyboard navigation

### Requirement 21

**User Story:** As a MOTAC staff member, I want optimized guest-only forms for helpdesk tickets and asset loan applications, so that I can quickly submit requests with excellent performance and accessibility.

#### Acceptance Criteria (Requirement 21)

1. THE ICTServe_System SHALL delete obsolete authenticated layouts (resources/views/layouts/app.blade.php, resources/views/components/layout/public.blade.php) and refactor resources/views/layouts/guest.blade.php as the default public layout
2. THE ICTServe_System SHALL implement guest-only form flows removing all authenticated user paths (helpdesk_tickets.user_id) and using only guest fields (name, email, phone, staff_id, grade, division)
3. THE ICTServe_System SHALL ensure all form fields have associated labels, error messages linked via aria-describedby, and aria-invalid="true" for WCAG 2.2 Level AA compliance
4. THE ICTServe_System SHALL optimize Livewire components using wire:model.live.debounce.300ms for dynamic fields, wire:model.lazy for large text fields, #[Computed] properties for derived data, and eager-loading with with() to prevent N+1 queries
5. THE ICTServe_System SHALL implement #[Lazy] loading for heavy sub-components and provide guest-only workflows with email notifications as primary communication method

### Requirement 22

**User Story:** As a MOTAC staff member, I want an authenticated internal portal where I can view my submission history, manage my profile, and access enhanced features, so that I have better control and visibility over my ICT service requests.

#### Acceptance Criteria (Requirement 22)

1. THE ICTServe_System SHALL provide an authenticated internal portal accessible via Laravel Breeze/Jetstream authentication for MOTAC staff
2. WHEN a staff member logs into the internal portal, THE ICTServe_System SHALL display a personalized dashboard showing their helpdesk tickets and asset loan applications
3. THE ICTServe_System SHALL allow authenticated staff to view detailed status, add comments, upload additional attachments, and track resolution progress for their submissions
4. THE ICTServe_System SHALL provide profile management features allowing staff to update contact information, preferences, and notification settings
5. THE ICTServe_System SHALL implement role-based access where staff can only view/edit their own submissions unless they have approver, admin, or superuser roles
6. THE ICTServe_System SHALL allow staff to link guest submissions to their authenticated account by verifying email ownership
7. THE ICTServe_System SHALL maintain consistent WCAG 2.2 Level AA compliance, MOTAC branding, and bilingual support across the authenticated portal

### Requirement 23

**User Story:** As a system administrator, I want a clean hybrid architecture with clear separation between guest forms and authenticated features, so that the system maintains security while providing flexibility for different access patterns.

#### Acceptance Criteria (Requirement 23)

1. THE ICTServe_System SHALL implement clear separation between guest-accessible routes (no authentication) and authenticated routes (login required)
2. THE ICTServe_System SHALL provide dual approval workflows: email-based approval links for Grade 41+ officers (no login) AND authenticated portal approval for logged-in approvers
3. THE ICTServe_System SHALL ensure helpdesk workflow supports both guest ticket submission and authenticated staff ticket management through the internal portal
4. THE ICTServe_System SHALL refactor all inline style attributes and style blocks into external CSS files imported via Vite as documented in inline-styles-refactoring requirements
5. THE ICTServe_System SHALL implement comprehensive browser compatibility testing for Chrome 90+, Firefox 88+, Safari 14+, Edge 90+ with automated cross-browser validation
6. THE ICTServe_System SHALL maintain audit trails distinguishing between guest submissions, authenticated submissions, and administrative actions
