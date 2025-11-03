# Requirements Document

## Introduction

The Helpdesk Module is a comprehensive ticketing system for managing ICT damage complaints and support requests within the MOTAC BPM organization. This system will replace manual processes (phone and walk-in) with a structured digital ticketing system that provides full audit trails, SLA tracking, and automated workflows.

## Glossary

- **Helpdesk_System**: The complete ticketing system for managing ICT support requests and damage complaints using guest-only public architecture
- **Tiket_Helpdesk**: A digital record of an ICT issue or support request submitted by MOTAC staff via guest-only public forms with format HD[YYYY][000001-999999]
- **Admin**: Administrative users with Filament admin panel access for managing helpdesk tickets and assignments (only role with login access)
- **Superuser**: Super administrative users with full Filament admin access for helpdesk system configuration (only role with login access)
- **Guest_Only_Model**: System architecture where all public-facing helpdesk functionality requires no authentication or user accounts
- **Public_Forms**: Guest-accessible helpdesk ticket submission forms for all MOTAC staff without login requirements
- **Email_Workflow**: Primary communication method using automated email notifications for ticket updates and status changes with 60-second delivery target
- **SLA**: Service Level Agreement defining response and resolution time targets with automated escalation at 25% before breach
- **Status_Tiket**: Current state of a ticket in its lifecycle (Baru, Ditugaskan, Dalam_Proses, Pending_User, Resolved, Closed, Reopened)
- **Audit_Trail**: Complete chronological record of all activities and changes in the system with 7-year retention per PDPA 2010
- **Auto_Assignment**: Automated process of assigning tickets to appropriate MOTAC divisions or external agencies based on predefined rules
- **Escalation**: Process of raising ticket priority or escalating to higher management levels when SLA thresholds are approaching
- **Admin_Dashboard**: Filament admin interface showing all tickets and system metrics (admin/superuser only)
- **Guest_Portal**: Public interface for submitting tickets without authentication (guest-only access)
- **MOTAC**: Ministry of Tourism, Arts and Culture Malaysia
- **PDPA_2010**: Personal Data Protection Act 2010 (Malaysian data protection law)
- **ISO_8601**: International standard for date and time representation (YYYY-MM-DDTHH:MM:SSZ)
- **AES_256**: Advanced Encryption Standard with 256-bit key length for data encryption
- **CSRF**: Cross-Site Request Forgery protection mechanism
- **Filament_Admin_Panel**: Filament v4 administrative interface for backend ticket management
- **WCAG_Compliance**: Web Content Accessibility Guidelines 2.2 Level AA compliance with minimum 4.5:1 text contrast ratio and 3:1 UI component contrast ratio
- **Compliant_Color_Palette**: WCAG 2.2 AA compliant colors - Primary #0056b3 (6.8:1 contrast), Success #198754 (4.9:1 contrast), Warning #ff8c00 (4.5:1 contrast), Danger #b50c0c (8.2:1 contrast)
- **Deprecated_Colors**: Old non-compliant colors to be removed - Warning Yellow #F1C40F, Danger Red #E74C3C
- **Core_Web_Vitals**: Performance standards with Largest Contentful Paint <2.5s, First Input Delay <100ms, Cumulative Layout Shift <0.1, Time to First Byte <600ms
- **LCP**: Largest Contentful Paint - measures loading performance (target: <2.5 seconds)
- **FID**: First Input Delay - measures interactivity (target: <100 milliseconds)
- **CLS**: Cumulative Layout Shift - measures visual stability (target: <0.1)
- **TTFB**: Time to First Byte - measures server response time (target: <600 milliseconds)
- **Focus_Indicators**: Visible focus indicators with 3-4px outline, 2px offset, and minimum 3:1 contrast ratio for keyboard navigation
- **Touch_Targets**: Minimum 44×44px interactive elements for mobile accessibility compliance per WCAG 2.2 Level AA
- **Component_Library**: Unified Blade component library for consistent helpdesk interfaces (accessibility, data, form, layout, navigation, responsive, ui)
- **Session_Locale**: Language preference persistence using session and cookie storage only (no user profile storage)
- **Bilingual_Support**: Comprehensive Bahasa Melayu and English language support for all helpdesk interfaces with 100% translation coverage
- **Bahasa_Melayu**: Malay language (primary language for ICTServe)
- **ARIA**: Accessible Rich Internet Applications - technical specification for accessibility
- **Livewire_v3**: Server-driven UI framework for Laravel used in helpdesk components
- **Volt_v1**: Single-file component system for Livewire used in helpdesk forms

## Requirements

### Requirement 1

**User Story:** As a MOTAC staff member, I want to submit ICT damage complaints through a guest-only digital form without any login requirements, so that I can report issues efficiently and receive proper support via email.

#### Acceptance Criteria

1. WHEN a MOTAC staff member accesses the helpdesk portal, THE Helpdesk_System SHALL display a **guest-only** complaint form with required fields (name, email, phone, staff_id, description) using WCAG 2.2 Level AA compliant design and no authentication barriers
2. WHEN a guest user submits a valid complaint form, THE Helpdesk_System SHALL generate a unique ticket number automatically and send confirmation email with ticket details using compliant email templates
3. WHEN a ticket is created, THE Helpdesk_System SHALL notify admin users via email and admin panel dashboard for manual assignment to appropriate MOTAC divisions
4. WHERE the guest user provides asset information, THE Helpdesk_System SHALL link the ticket to the relevant asset in the admin backend with proper audit trail
5. IF required fields are missing, THEN THE Helpdesk_System SHALL display WCAG 2.2 Level AA compliant validation errors with proper ARIA attributes, focus management, and prevent submission

### Requirement 2

**User Story:** As an admin user, I want to receive and manage guest-submitted tickets through the Filament admin panel, so that I can efficiently assign tickets to appropriate divisions and track resolution progress.

#### Acceptance Criteria

1. WHEN a guest ticket is submitted, THE Helpdesk_System SHALL notify admin users via email and display the ticket in the Filament admin panel dashboard
2. WHILE managing tickets, THE Helpdesk_System SHALL allow admin users to assign tickets to MOTAC divisions or external agencies and update status through the admin panel
3. WHEN admin needs additional information, THE Helpdesk_System SHALL allow sending email requests to the original guest submitter and pause SLA timer
4. WHEN an issue is resolved, THE Helpdesk_System SHALL allow admin users to mark tickets as resolved and send confirmation email to the guest submitter
5. THE Helpdesk_System SHALL maintain complete communication history for each ticket accessible only through the admin panel

### Requirement 3

**User Story:** As an admin or superuser, I want to monitor ticket performance and manage assignments through the Filament admin panel, so that I can ensure SLA compliance and optimize support operations.

#### Acceptance Criteria

1. THE Helpdesk_System SHALL provide a Filament admin dashboard showing ticket statistics, performance metrics, and WCAG 2.2 Level AA compliant data visualizations using the compliant color palette
2. WHEN SLA thresholds are approaching, THE Helpdesk_System SHALL send escalation alerts to admin users via email and admin panel notifications
3. THE Helpdesk_System SHALL allow admin users to manually assign tickets to MOTAC divisions or external agencies through the admin panel
4. THE Helpdesk_System SHALL generate reports on ticket volume, resolution times, and division performance with export capabilities (CSV, PDF, Excel)
5. WHERE tickets exceed SLA limits, THE Helpdesk_System SHALL automatically escalate to superuser and send email notifications to management

### Requirement 4

**User Story:** As a guest ticket submitter, I want to track my ticket status via email notifications, so that I can stay informed about issue resolution progress without requiring login access.

#### Acceptance Criteria

1. WHEN a guest user receives a ticket confirmation email, THE Helpdesk_System SHALL include a unique ticket tracking number and current status information
2. WHEN ticket status changes from any state to another state, THE Helpdesk_System SHALL send email notification to the guest submitter within 60 seconds
3. WHEN admin requests additional information, THE Helpdesk_System SHALL send email to the guest submitter with specific questions and allow email-based response
4. WHEN a ticket status changes to resolved, THE Helpdesk_System SHALL send email requesting guest confirmation of resolution within 24 hours
5. IF the guest submitter responds via email indicating dissatisfaction with resolution, THEN THE Helpdesk_System SHALL reopen the ticket and notify admin users within 60 seconds

### Requirement 5

**User Story:** As a system administrator, I want comprehensive audit trails and security controls, so that I can ensure PDPA 2010 compliance and maintain system integrity.

#### Acceptance Criteria

1. WHEN any user action or system change occurs, THE Helpdesk_System SHALL log the event with ISO 8601 timestamp, user identifier, action type, and affected data within 1 second
2. THE Helpdesk_System SHALL implement role-based access control with exactly two roles (admin and superuser) and enforce role permissions on all Filament admin panel operations
3. WHEN data is submitted through guest forms or admin operations, THE Helpdesk_System SHALL validate all input fields against defined constraints and reject invalid data with specific error messages
4. THE Helpdesk_System SHALL encrypt all personal data at rest using AES-256 encryption and maintain audit logs for 7 years per PDPA 2010 requirements
5. WHEN admin users access the Filament admin panel, THE Helpdesk_System SHALL require secure authentication with session timeout after 30 minutes of inactivity and enforce CSRF protection on all state-changing operations

### Requirement 6

**User Story:** As any user of the helpdesk system, I want full WCAG 2.2 Level AA accessibility compliance across all interfaces, so that I can access the system regardless of my abilities or assistive technology needs.

#### Acceptance Criteria

1. THE Helpdesk_System SHALL meet WCAG 2.2 Level AA requirements including minimum 4.5:1 text contrast ratio and 3:1 UI component contrast ratio using the compliant color palette
2. WHEN users navigate the helpdesk interface, THE Helpdesk_System SHALL provide visible focus indicators with 3-4px outline, 2px offset, and minimum 3:1 contrast ratio
3. THE Helpdesk_System SHALL ensure all interactive elements meet minimum 44×44px touch target size requirements for mobile accessibility
4. THE Helpdesk_System SHALL provide proper semantic HTML structure with ARIA landmarks (banner, navigation, main, complementary, contentinfo) and screen reader support
5. WHERE users require keyboard navigation, THE Helpdesk_System SHALL provide skip links, keyboard shortcuts, and full keyboard accessibility without mouse dependency

### Requirement 7

**User Story:** As any user of the helpdesk system, I want optimal performance that meets Core Web Vitals standards, so that I can access services quickly and efficiently on any device.

#### Acceptance Criteria

1. THE Helpdesk_System SHALL achieve Largest Contentful Paint (LCP) of less than 2.5 seconds for all helpdesk pages
2. THE Helpdesk_System SHALL achieve First Input Delay (FID) of less than 100 milliseconds for all interactive elements
3. THE Helpdesk_System SHALL achieve Cumulative Layout Shift (CLS) of less than 0.1 to prevent visual instability
4. THE Helpdesk_System SHALL achieve Time to First Byte (TTFB) of less than 600 milliseconds for server response times
5. WHERE performance monitoring is required, THE Helpdesk_System SHALL achieve Lighthouse scores of Performance 90+, Accessibility 100, Best Practices 100, SEO 100

### Requirement 8

**User Story:** As a MOTAC staff member, I want comprehensive bilingual support with seamless language switching, so that I can use the helpdesk system in my preferred language (Bahasa Melayu or English).

#### Acceptance Criteria

1. THE Helpdesk_System SHALL provide complete bilingual support for Bahasa Melayu and English across all public helpdesk interfaces
2. WHEN users switch languages, THE Helpdesk_System SHALL persist language preference using session and cookie storage only (no user profile persistence required)
3. THE Helpdesk_System SHALL display all helpdesk content, forms, validation messages, and email notifications in the selected language
4. THE Helpdesk_System SHALL provide accessible language switcher component with proper ARIA labels and keyboard navigation support
5. WHERE translation coverage is required, THE Helpdesk_System SHALL ensure 100% translation coverage for all user-facing helpdesk text and maintain translation consistency
