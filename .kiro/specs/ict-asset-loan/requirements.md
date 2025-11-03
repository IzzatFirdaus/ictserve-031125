# Requirements Document

## Introduction

The ICT Asset Loan Module is a comprehensive digital system for managing the complete lifecycle of ICT equipment loans within the MOTAC BPM organization. This system will replace manual processes (paper forms, phone calls, manual tracking) with a structured digital workflow that provides full audit trails, approval workflows, and automated asset management. The system integrates seamlessly with the existing helpdesk module and follows all MOTAC standards and compliance requirements.

## Glossary

- **Sistem_Pinjaman_Aset**: The complete ICT asset loan management system with **guest-only** public architecture
- **Permohonan_Pinjaman**: A digital loan application submitted by MOTAC staff via **guest-only** public forms (no authentication required)
- **Aset_ICT**: ICT equipment available for loan (laptops, projectors, tablets, etc.)
- **Guest_Applicant**: MOTAC staff member submitting loan applications via **guest-only** public forms (no authentication required, no user accounts)
- **Email_Approver**: Grade 41+ officers who approve applications via **secure email links only** (no system login required, no user accounts)
- **Admin**: Administrative users with Filament admin panel access for asset management and loan processing (**only role with login access**, simplified RBAC)
- **Superuser**: Super administrative users with full Filament admin access for system configuration (**only role with login access**, simplified RBAC)
- **Guest_Only_Architecture**: System architecture where all public-facing functionality requires no authentication or user accounts
- **Email_Workflow**: Primary interaction method for approvals and notifications via automated email system
- **Matriks_Kelulusan**: Approval matrix based on applicant grade and asset value
- **Aliran_Kerja_Kelulusan**: Multi-level approval workflow based on organizational hierarchy via **email-based approvals**
- **Inventori_Aset**: Real-time asset inventory tracking system
- **Transaksi_Pinjaman**: Complete record of asset issuance and return transactions
- **Kalendar_Tempahan**: Visual booking calendar showing asset availability
- **Sistem_Peringatan**: Automated reminder system for return dates and overdue items
- **Jejak_Audit**: Complete chronological record of all loan activities and changes
- **Status_Pinjaman**: Current state of a loan application in its lifecycle
- **Tempoh_Pinjaman**: Duration for which assets can be borrowed (varies by asset type and user grade)
- **WCAG_Compliance**: Web Content Accessibility Guidelines 2.2 Level AA compliance with strict color contrast ratios (4.5:1 text, 3:1 UI components)
- **Core_Web_Vitals**: Performance standards with LCP <2.5s, FID <100ms, CLS <0.1, TTFB <600ms targets
- **Compliant_Color_Palette**: WCAG 2.2 AA compliant colors - Primary #0056b3 (6.8:1), Success #198754 (4.9:1), Warning #ff8c00 (4.5:1), Danger #b50c0c (8.2:1)
- **Focus_Indicators**: Visible focus indicators with 3-4px outline, 2px offset, and 3:1 contrast ratio minimum for keyboard navigation
- **Touch_Targets**: Minimum 44×44px interactive elements for mobile accessibility compliance
- **Semantic_HTML**: Proper HTML5 semantic elements (header, nav, main, footer) with ARIA landmarks
- **Bilingual_Support**: Comprehensive Bahasa Melayu and English language support with session/cookie-based persistence
- **Session_Locale**: Language preference persistence using session and cookie only (no user profile storage)
- **Component_Library**: Unified reusable Blade, Livewire, and Volt components following design system standards
- **Frontend_Components**: Unified component library for consistent public-facing interfaces with WCAG 2.2 AA compliance

## Requirements

### Requirement 1

**User Story:** As a MOTAC staff member, I want to submit ICT asset loan applications through a guest-only digital form without any login requirements, so that I can request equipment efficiently and receive approval via email.

#### Acceptance Criteria

1. WHEN a MOTAC staff member accesses the loan application portal, THE Sistem_Pinjaman_Aset SHALL display a guest-only loan request form with required fields (name, email, phone, staff_id, grade, asset details) and no authentication barriers
2. WHEN a guest user submits a valid loan application, THE Sistem_Pinjaman_Aset SHALL generate a unique application number automatically and send confirmation email with application details
3. WHEN an application is submitted, THE Sistem_Pinjaman_Aset SHALL send secure email approval links to the appropriate Grade 41+ officer based on the approval matrix (no system login required)
4. WHERE the guest user selects equipment types, THE Sistem_Pinjaman_Aset SHALL show real-time availability and specifications with WCAG 2.2 Level AA compliant interface
5. IF required fields are missing or invalid, THEN THE Sistem_Pinjaman_Aset SHALL display WCAG 2.2 Level AA compliant validation errors with proper ARIA attributes and prevent submission

### Requirement 2

**User Story:** As a Grade 41+ approving officer, I want to review and approve loan applications via secure email links without logging into any system, so that I can ensure proper authorization and asset allocation from anywhere.

#### Acceptance Criteria

1. WHEN a loan application requires approval, THE Sistem_Pinjaman_Aset SHALL send email notification with secure approval/decline links to the designated Grade 41+ officer based on the approval matrix (no system login required)
2. WHILE reviewing applications via email, THE Sistem_Pinjaman_Aset SHALL display applicant details, equipment requested, and justification in the email with time-limited approval tokens
3. WHEN an approver clicks approval/decline links, THE Sistem_Pinjaman_Aset SHALL process the decision and record it with timestamp and comments without requiring system access
4. WHERE additional information is needed, THE Sistem_Pinjaman_Aset SHALL allow requesting clarification from the applicant via email workflows
5. IF an application is approved, THEN THE Sistem_Pinjaman_Aset SHALL notify admin users via email and admin panel for asset preparation and issuance

### Requirement 3

**User Story:** As an admin user, I want to manage asset inventory and process loan transactions through the Filament admin panel, so that I can efficiently handle equipment issuance and returns.

#### Acceptance Criteria

1. THE Sistem_Pinjaman_Aset SHALL maintain real-time inventory status for all ICT assets
2. WHEN processing asset issuance, THE Sistem_Pinjaman_Aset SHALL record complete transaction details including accessories
3. WHEN assets are returned, THE Sistem_Pinjaman_Aset SHALL allow condition assessment and damage reporting
4. THE Sistem_Pinjaman_Aset SHALL update asset status automatically during issuance and return processes
5. WHERE assets require maintenance, THE Sistem_Pinjaman_Aset SHALL integrate with the helpdesk system for ticket creation

### Requirement 4

**User Story:** As a system user, I want to track my loan applications and manage borrowed equipment, so that I can stay informed about approval status and return obligations.

#### Acceptance Criteria

1. THE Sistem_Pinjaman_Aset SHALL allow users to view their loan application history and current status
2. WHEN loan status changes, THE Sistem_Pinjaman_Aset SHALL notify users via email and system notifications
3. WHEN return dates approach, THE Sistem_Pinjaman_Aset SHALL send automated reminder notifications
4. THE Sistem_Pinjaman_Aset SHALL allow users to request loan extensions through the approval workflow
5. IF equipment is damaged or lost, THE Sistem_Pinjaman_Aset SHALL provide a formal reporting mechanism

### Requirement 5

**User Story:** As a system administrator, I want comprehensive asset management and reporting capabilities, so that I can optimize asset utilization and ensure compliance.

#### Acceptance Criteria

1. THE Sistem_Pinjaman_Aset SHALL provide dashboard analytics for asset utilization and loan patterns
2. THE Sistem_Pinjaman_Aset SHALL generate reports on asset usage, overdue items, and damage incidents
3. THE Sistem_Pinjaman_Aset SHALL maintain complete audit trails for all asset transactions and approvals
4. THE Sistem_Pinjaman_Aset SHALL implement role-based access control for different user types
5. THE Sistem_Pinjaman_Aset SHALL comply with PDPA requirements and MOTAC data retention policies

### Requirement 6

**User Story:** As a system stakeholder, I want automated workflow management and SLA compliance, so that loan processes are efficient and meet service standards.

#### Acceptance Criteria

1. THE Sistem_Pinjaman_Aset SHALL implement automated approval routing based on applicant grade and asset value via **email-based workflows**
2. WHEN SLA thresholds are approaching, THE Sistem_Pinjaman_Aset SHALL send escalation alerts to management via email notifications
3. THE Sistem_Pinjaman_Aset SHALL enforce maximum loan periods based on asset type and user grade
4. THE Sistem_Pinjaman_Aset SHALL automatically update asset availability based on booking calendar
5. WHERE integration is required, THE Sistem_Pinjaman_Aset SHALL seamlessly connect with helpdesk and HRMIS systems

### Requirement 7

**User Story:** As any user of the ICT Asset Loan system, I want all interfaces to meet WCAG 2.2 Level AA accessibility standards with optimal performance, so that I can access the system regardless of my abilities or device capabilities.

#### Acceptance Criteria

1. THE Sistem_Pinjaman_Aset SHALL meet WCAG 2.2 Level AA requirements including minimum 4.5:1 text contrast ratio and 3:1 UI component contrast ratio across all interfaces
2. THE Sistem_Pinjaman_Aset SHALL achieve Core Web Vitals performance targets: LCP <2.5s, FID <100ms, CLS <0.1, TTFB <600ms
3. THE Sistem_Pinjaman_Aset SHALL provide visible focus indicators with 3-4px outline, 2px offset, and 3:1 contrast ratio minimum for keyboard navigation
4. THE Sistem_Pinjaman_Aset SHALL implement minimum 44×44px touch targets for all interactive elements on mobile devices
5. WHERE users navigate the system, THE Sistem_Pinjaman_Aset SHALL provide proper semantic HTML5 elements and ARIA landmarks for screen reader compatibility

### Requirement 8

**User Story:** As a MOTAC staff member, I want comprehensive bilingual support with consistent visual design across all loan application interfaces, so that I can use the system in my preferred language with familiar MOTAC branding.

#### Acceptance Criteria

1. THE Sistem_Pinjaman_Aset SHALL support both Bahasa Melayu and English languages with complete translation coverage for all user-facing content
2. THE Sistem_Pinjaman_Aset SHALL persist language preferences using session and cookie storage only (no user profile storage required)
3. THE Sistem_Pinjaman_Aset SHALL use the compliant color palette: Primary #0056b3, Success #198754, Warning #ff8c00, Danger #b50c0c for all UI elements
4. THE Sistem_Pinjaman_Aset SHALL maintain consistent MOTAC branding (logos, colors, typography) across all public-facing interfaces
5. WHERE users interact with forms and components, THE Sistem_Pinjaman_Aset SHALL use unified component library with consistent styling and behavior patterns
