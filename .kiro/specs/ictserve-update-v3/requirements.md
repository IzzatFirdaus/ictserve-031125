# Requirements Document

## Introduction

ICTServe Update v3 consolidates the system to version 3.5.0, implementing the **True Hybrid Architecture** for MOTAC staff. This architecture enables flexible access modes: staff can self-register with `@motac.gov.my` email, log in for personalized dashboard access, OR use guest forms for quick submissions without authentication. The system uses Laravel 12.40.1 stack with comprehensive compliance standards (WCAG 2.2 AA, PDPA 2010, MyGOV Digital Service Standards v2.1.0).

**Key v3.5.0 Features (per D00 §4.1):**

- Self-registration with `@motac.gov.my` email domain validation
- Flexible login (full email OR short username)
- Optional guest-to-account linking for historical submissions
- Dual audit system (owen-it/laravel-auditing + spatie/laravel-activitylog)
- Laravel Telescope debugging (superuser only, unrestricted)
- Laravel Pulse performance monitoring (admin/superuser access)
- Multi-channel notifications with user preferences
- API authentication via Laravel Sanctum (future-ready)
- Optional Google Workspace SSO integration

**Reference Documents:**

- D00_SYSTEM_OVERVIEW.md - System vision and governance (v3.5.0, 1 December 2025)
- D01_SYSTEM_DEVELOPMENT_PLAN.md - Development methodology (v3.5.0, 1 December 2025)
- D02_BUSINESS_REQUIREMENTS_SPECIFICATION.md - Business requirements (v3.5.0)
- D03_SOFTWARE_REQUIREMENTS_SPECIFICATION.md - Software requirements (v3.5.0)
- D04_SOFTWARE_DESIGN_DOCUMENT.md - Architecture and design (v3.5.0)
- D05_DATA_MIGRATION_PLAN.md - Data migration strategy
- D06_DATA_MIGRATION_SPECIFICATION.md - Migration specifications
- D07_SYSTEM_INTEGRATION_PLAN.md - Integration planning
- D08_SYSTEM_INTEGRATION_SPECIFICATION.md - Integration specifications
- D09_DATABASE_DOCUMENTATION.md - Database schema and dual audit (v3.5.0)
- D10_SOURCE_CODE_DOCUMENTATION.md - Code organization
- D11_TECHNICAL_DESIGN_DOCUMENTATION.md - Technical infrastructure
- D12_UI_UX_DESIGN_GUIDE.md - UI/UX guidelines (v3.5.0)
- D13_UI_UX_FRONTEND_FRAMEWORK.md - Frontend framework (v3.5.0)
- D14_UI_UX_STYLE_GUIDE.md - Style guide (v3.5.0)
- D15_LANGUAGE_MS_EN.md - Bilingual localization
- D16_BROADCASTING_SETUP.md - WebSocket configuration (Laravel Reverb)
- D17_QUEUE_MANAGEMENT_HORIZON.md - Queue management (Laravel Horizon)

## Glossary

- **ICTServe**: Internal ICT service management platform for MOTAC BPM (Bahagian Pengurusan Maklumat)
- **MOTAC**: Kementerian Pelancongan, Seni dan Budaya Malaysia (Ministry of Tourism, Arts and Culture Malaysia)
- **BPM**: Bahagian Pengurusan Maklumat (Information Management Division) - the ICT division operating ICTServe
- **Jata Negara**: Malaysian Coat of Arms - official national emblem required on all government digital services per MyGOV DSS v2.1.0
- **MyGOV DSS v2.1.0**: Malaysian Government Digital Service Standards - official guidelines for government digital services including branding requirements
- **True Hybrid Architecture**: System design where staff can choose: (1) Self-register and login for Dashboard/Profile, OR (2) Use guest forms for quick access without login per D00 §1
- **Self-Registration**: Staff with `@motac.gov.my` email can create accounts independently; email verification required per D01 §4.3
- **Flexible Login**: Authenticated users can log in using full email (`user@motac.gov.my`) OR short username (`user`) per D03 SRS-AUTH-001
- **Account Linking**: Optional service to link historical guest submissions to newly registered staff accounts per D02 FR-050
- **Guest Form**: Form accessible without authentication for ticket/loan submission; tracked via status token
- **Signed Approval Link (SAL)**: Token-based URL (JWT + SHA-512 hash) enabling approval decisions without login per D03 SRS-LOAN-004
- **Status Token**: Unique SHA-512 hash allowing users to check ticket/loan status without authentication
- **SLA**: Service Level Agreement defining response/resolution timeframes per D11 §7
- **Filament**: Server-Driven UI (SDUI) framework for Laravel admin panels (v4.1.10)
- **Laravel Reverb**: WebSocket server for real-time communication (v1.6.2) per D16
- **Laravel Echo**: WebSocket client library (v2.2.6) per D16
- **Dual Audit System**: Combined audit using owen-it/laravel-auditing (field-level compliance) + spatie/laravel-activitylog (user activity) per D09 §4.6
- **Laravel Telescope**: Debugging and monitoring tool accessible only to superuser role per D00 §4.1
- **WCAG 2.2 AA**: Web Content Accessibility Guidelines Level AA compliance standard per D12-D14
- **PDPA 2010**: Personal Data Protection Act Malaysia - data privacy compliance
- **MyGOV DSS v2.1.0**: Malaysian Government Digital Service Standards
- **Staff**: Self-registered MOTAC staff with `@motac.gov.my` email (role: `staff`) per D03 SRS-ADM-006
- **Admin**: BPM officer managing daily operations via Filament (role: `admin`)
- **Superuser**: BPM officer managing configuration, audit, integrations, and Laravel Telescope access (role: `superuser`)
- **Grade 41+**: Senior officers authorized to approve loan applications per D02 §6.2
- **MyGovEA**: Malaysian Government Enterprise Architecture - national framework for government digital services per JDN guidelines
- **Berpaksikan Rakyat**: Citizen-centric design principle placing user needs as primary focus per MyGovEA Prinsip Reka Bentuk §1
- **Pencegahan Ralat**: Error prevention design principle requiring confirmation before destructive actions per MyGovEA Prinsip Reka Bentuk §17
- **Form Reference Code**: Official document identifier (e.g., PK.(S).MOTAC.07.(L1) for helpdesk, PK.(S).MOTAC.07.(L3) for loans) per MOTAC BPM standards
- **Responsible Officer (Pegawai Bertanggungjawab)**: Staff member accountable for loaned equipment's use, safety, and any damage during loan period per PK.(S).MOTAC.07.(L3)
- **Applicant (Pemohon)**: Staff member who submits the loan application form per PK.(S).MOTAC.07.(L3)
- **Issuing Officer (Pegawai Pengeluar)**: BPM staff who issues equipment to the recipient per PK.(S).MOTAC.07.(L3)
- **Return Receiving Officer (Pegawai Terima Pulangan)**: BPM staff who receives returned equipment per PK.(S).MOTAC.07.(L3)
- **Asset Accessories**: Standard equipment accessories tracked during check-out/check-in (Power Adapter, Bag, Mouse, USB Cable, HDMI/VGA Cable, Remote) per PK.(S).MOTAC.07.(L3)
- **IDN (Identiti Digital Nasional)**: National Digital Identity for centralized government service authentication per MyGovEA Arkitektur Perkhidmatan §Identifikasi
- **MYDS (Malaysia Government Design System)**: Official design system for Malaysian government digital services; ICTServe adopts MYDS guidelines as best practices while using Tailwind CSS/Livewire components per design.digital.gov.my
- **MYDS Guidelines Adoption**: Approach where MYDS design principles (grid, typography, spacing, colors, motion) are implemented using existing tech stack (Tailwind CSS 4.x, Livewire 3.7, Filament 4.1) rather than MYDS React components
- **MYDS 12-8-4 Grid**: Responsive breakpoint guideline: desktop (≥1024px), tablet (768-1023px), mobile (≤767px) - implemented via Tailwind responsive utilities
- **MYDS Spacing Scale**: 8px base unit spacing guideline (8, 16, 24, 32, 48, 64px) - implemented via Tailwind spacing utilities
- **Inter**: Sans-serif font family recommended for body text per MYDS Typography guidelines
- **Poppins**: Sans-serif font family recommended for headings per MYDS Typography guidelines
- **Pickup OTP**: 4-digit one-time password for secure asset collection with 24-hour validity per D03 SRS-LOAN-007
- **Email Reply-to-Ticket**: IMAP/webhook integration allowing users to reply to ticket notifications via email per D03 SRS-HELP-006
- **Fuzzy Search**: Intelligent search with typo tolerance and partial matching using Levenshtein distance algorithm
- **Session Timeout Warning**: 2-minute warning modal before 30-minute session expiry per D03 §8.1
- **Onboarding Tour**: Interactive guided tour for new users with contextual help tooltips per MyGovEA §18
- **Dashboard Widgets**: Customizable real-time widgets for admin dashboard (Ticket Stats, Loan Stats, SLA Compliance, Recent Activity, Performance Metrics, System Health) per D03 SRS-ADM-003
- **Saved Filters**: User-specific filter combinations that can be saved, named, and reused across sessions
- **Touch Gestures**: Mobile-optimized interactions including swipe navigation, pull-to-refresh, and infinite scroll per D12 §5
- **Levenshtein Distance**: Algorithm for measuring string similarity used in fuzzy search for typo correction
- **Laravel Pulse**: Real-time application performance monitoring dashboard (v1.3.0) for tracking slow queries, queue jobs, and server health per D03 §8.2
- **Laravel Sanctum**: API token authentication system (v4.0) for secure API access with configurable abilities and expiration per D03 SRS-API-001
- **Laravel Socialite**: OAuth 2.0 social authentication library (v5.x) for Google Workspace SSO integration per D03 SRS-AUTH-001
- **API Token**: Sanctum-based authentication token for API access with configurable abilities (read:tickets, write:tickets, read:loans, write:loans, admin:all) and expiration per D03 SRS-API-001
- **Google Workspace SSO**: OAuth 2.0 authentication using Google Workspace accounts restricted to @motac.gov.my domain per D03 SRS-AUTH-001
- **Performance Monitoring**: Real-time application performance tracking using Laravel Pulse for proactive issue identification per D03 §8.2
- **Dual Entry Model**: System design allowing staff to choose between authenticated login (for Dashboard/Profile) or guest form submission (quick access) per D03 SRS-AUTH-001
- **Hybrid Data Association**: Critical data model where authenticated submissions link to user_id (nullable FK), while guest submissions set user_id=NULL with fallback to submitter_email per D03 SRS-DATA-001
- **SIEM Integration**: Security Information and Event Management integration for audit log forwarding to BPM SIEM every 15 minutes per D03 SRS-AUDIT-05
- **Immutable Audit Trail**: Write Once Read Many (WORM) audit logs with cryptographic hashing for integrity verification per D03 SRS-AUDIT-06
- **Keyboard Shortcuts**: Comprehensive keyboard shortcuts for power users (/ for search, ? for help, n for new ticket/loan) per D03 SRS-UX-006
- **Dark Mode Support**: Future optional dark mode maintaining WCAG 2.2 AA contrast ratios per D03 SRS-UX-007

## Requirements

### Requirement 1: Hybrid Helpdesk Ticket Submission

**User Story:** As a MOTAC staff member, I want to submit helpdesk tickets either as an authenticated user (with auto-fill) or as a guest (quick access), so that I can report ICT issues flexibly based on my preference.

**Reference:** D02 FR-001, D02 FR-050, D03 SRS-HELP-001, SRS-HELP-002, SRS-HELP-003, SRS-AUTH-001, SRS-DATA-001

#### Acceptance Criteria

1. WHEN a user accesses the helpdesk form at `/helpdesk/create` THEN THE system SHALL display a WCAG 2.2 AA compliant bilingual form (BM/EN) with fields for name, email, phone, division, grade, category, description, attachments, and PDPA acknowledgement per D03 SRS-HELP-001
2. WHEN an authenticated user (Auth::check() === true) accesses the form THEN THE system SHALL auto-fill name, email, phone, division, and grade from the user profile per D02 FR-001
3. WHEN a guest user (Auth::check() === false) accesses the form THEN THE system SHALL require manual entry of all submitter fields per D02 FR-050
4. WHEN a user enters data into form fields THEN THE system SHALL validate inputs in real-time using Livewire 3.7.0 and display inline error messages within 500ms per D03 SRS-HELP-002
5. WHEN a user submits a valid helpdesk ticket THEN THE system SHALL generate a unique ticket number in format `HD-YYYYMM-XXXX`, store submitter data as metadata fields, and link to `user_id` (nullable FK) if authenticated per D03 SRS-HELP-003
6. WHEN a user uploads attachments THEN THE system SHALL accept up to 5 files, each maximum 5MB, in formats PDF, JPG, PNG, DOCX per D03 SRS-HELP-002
7. WHEN a ticket is successfully created THEN THE system SHALL generate a status token (SHA-512 hash) and send confirmation email with ticket number and status check link within 60 seconds per D03 SRS-HELP-004

### Requirement 2: Helpdesk Status Checking

**User Story:** As a MOTAC staff member, I want to check my ticket status using a token link, so that I can track progress without logging in.

**Reference:** D03 SRS-HELP-004, SRS-HELP-006

#### Acceptance Criteria

1. WHEN a user accesses a valid status token URL THEN THE system SHALL display ticket details including status, timeline, and any admin comments marked as public
2. WHEN a user accesses an expired or invalid token THEN THE system SHALL display a clear error message in both BM and EN with instructions to contact BPM support
3. WHILE a ticket status changes THEN THE system SHALL send email notification to the submitter within 60 seconds of the status update per D03 SRS-HELP-006

### Requirement 3: Hybrid Asset Loan Application

**User Story:** As a MOTAC staff member, I want to submit asset loan applications either as an authenticated user (with auto-fill and history) or as a guest (quick access), so that I can request ICT equipment flexibly.

**Reference:** D02 FR-001, D02 FR-050, D03 SRS-LOAN-001, SRS-LOAN-002, SRS-LOAN-003, SRS-AUTH-001, SRS-DATA-001

#### Acceptance Criteria

1. WHEN a user accesses the loan form at `/loan/create` THEN THE system SHALL display a multi-step wizard with applicant info, asset selection, date range, location, purpose, and PDPA acknowledgement steps per D02 §6.2
2. WHEN an authenticated user (Auth::check() === true) accesses the form THEN THE system SHALL auto-fill applicant name, email, phone, division, and grade from the user profile per D02 FR-001
3. WHEN a guest user (Auth::check() === false) accesses the form THEN THE system SHALL require manual entry of all applicant fields per D02 FR-050
4. WHEN a user selects assets and dates THEN THE system SHALL check availability in real-time using Livewire 3.7.0 and display conflicts or alternatives within 1 second per D03 SRS-LOAN-002
5. WHEN a user submits a valid loan application THEN THE system SHALL generate a unique reference in format `LA-YYYYMM-XXXX`, set status to `PENDING_SUPERVISOR_APPROVAL`, and link to `user_id` (nullable FK) if authenticated per D03 SRS-LOAN-003
6. WHEN a loan application is created THEN THE system SHALL reserve selected assets with soft-lock status to prevent double booking per D03 SRS-LOAN-002

### Requirement 4: Email-Based Loan Approval

**User Story:** As a department head (Grade 41+), I want to approve or reject loan applications via email link, so that I can make decisions without logging into the system.

**Reference:** D02 §6.2, D03 SRS-LOAN-004, SRS-LOAN-005, SRS-LOAN-006

#### Acceptance Criteria

1. WHEN a loan application requires approval THEN THE system SHALL generate a signed URL with JWT token (SHA-512 hashed) valid for 72 hours and send to the designated approver email per D03 SRS-LOAN-004
2. WHEN an approver clicks the approval link THEN THE system SHALL display application summary with Approve and Reject buttons on a guest-accessible page per D03 SRS-LOAN-005
3. WHEN an approver submits a decision THEN THE system SHALL record the decision, timestamp, IP hash, and optional remarks in `loan_approvals` table per D03 SRS-LOAN-006
4. IF an approval token expires THEN THE system SHALL display an expiry message and allow superuser to regenerate the token via Filament panel
5. WHEN an approval decision is recorded THEN THE system SHALL update loan status and notify applicant and admin within 60 seconds per D03 SRS-LOAN-008

### Requirement 5: Admin Helpdesk Management

**User Story:** As a BPM admin, I want to manage helpdesk tickets through Filament panel, so that I can process and resolve ICT issues efficiently.

**Reference:** D03 SRS-HELP-005, SRS-HELP-007, SRS-ADM-003

#### Acceptance Criteria

1. WHEN an admin logs into Filament at `/admin` THEN THE system SHALL display dashboard with ticket metrics (open, in-progress, resolved counts) and SLA compliance indicators per D03 SRS-ADM-003
2. WHEN an admin views ticket list THEN THE system SHALL provide filtering by status, priority, category, and date range with pagination
3. WHEN an admin updates ticket status THEN THE system SHALL require a comment/reason and log the change in audit trail per D09 §4.6
4. WHEN an admin assigns a ticket THEN THE system SHALL send real-time notification via Laravel Reverb to the assigned admin per D16
5. WHILE a ticket approaches SLA breach (25% remaining time) THEN THE system SHALL display visual warning and notify superuser per D03 SRS-HELP-007

### Requirement 6: Admin Asset Loan Management

**User Story:** As a BPM admin, I want to manage asset loans through Filament panel, so that I can process check-out, check-in, and track asset status.

**Reference:** D03 SRS-LOAN-007, SRS-LOAN-009

#### Acceptance Criteria

1. WHEN an admin performs asset check-out THEN THE system SHALL record transaction with admin ID, timestamp, and condition notes in `loan_transactions` per D03 SRS-LOAN-007
2. WHEN an admin performs asset check-in THEN THE system SHALL update asset status and allow damage reporting with photo attachments
3. IF an asset is returned damaged THEN THE system SHALL automatically create a helpdesk maintenance ticket within 5 seconds per D02 §6.3
4. WHEN viewing loan applications THEN THE system SHALL display approval chain status and allow filtering by status, date, and applicant division

### Requirement 7: Superuser Configuration and Audit

**User Story:** As a superuser, I want to configure system settings and view comprehensive audit trails from both audit systems, so that I can maintain governance and compliance.

**Reference:** D03 SRS-ADM-001, SRS-ADM-002, SRS-ADM-004, SRS-ADM-005, D09 §4.6, D09 §4.7

#### Acceptance Criteria

1. WHEN a superuser accesses configuration THEN THE system SHALL allow editing SLA thresholds, email templates, and approval workflow settings per D03 SRS-ADM-004
2. WHEN a superuser views audit logs THEN THE system SHALL display a unified view of both compliance audits (owen-it) and activity logs (spatie) with filtering by date, user, action type, and entity per D09 §4.7
3. WHEN a superuser exports audit data THEN THE system SHALL generate CSV or PDF report with complete audit trail from both audit systems for compliance review per D03 SRS-ADM-005
4. WHEN a superuser requires 2FA THEN THE system SHALL enforce TOTP authentication for superuser role login per D03 SRS-ADM-001
5. WHEN a superuser regenerates an expired approval token THEN THE system SHALL create a new signed URL valid for 72 hours and log the regeneration action per D03 SRS-LOAN-004

### Requirement 8: Real-Time Notifications

**User Story:** As a system administrator, I want real-time notifications for critical events, so that I can respond promptly to urgent issues.

**Reference:** D03 SRS-HELP-005, D16_BROADCASTING_SETUP.md

#### Acceptance Criteria

1. WHEN a high-priority ticket is created THEN THE system SHALL broadcast WebSocket notification to all online admins via Laravel Reverb within 2 seconds per D16
2. WHEN an SLA breach occurs THEN THE system SHALL send email and WebSocket notification to superuser immediately per D03 SRS-HELP-007
3. WHEN an asset becomes overdue THEN THE system SHALL send reminder notifications at 48 hours before, on due date, and daily after overdue per D03 SRS-LOAN-008

### Requirement 9: WCAG 2.2 AA Accessibility Compliance

**User Story:** As a user with accessibility needs, I want the system to be fully accessible, so that I can use all features regardless of ability.

**Reference:** D12_UI_UX_DESIGN_GUIDE.md, D13_UI_UX_FRONTEND_FRAMEWORK.md, D14_UI_UX_STYLE_GUIDE.md

#### Acceptance Criteria

1. THE system SHALL maintain color contrast ratio of 4.5:1 for text and 3:1 for UI components per WCAG 2.2 AA and D14 §3
2. THE system SHALL provide keyboard navigation for all interactive elements with visible focus indicators (3px outline) per D12 §4
3. THE system SHALL include ARIA labels, landmarks, and live regions for screen reader compatibility per D13 §6
4. THE system SHALL ensure touch targets are minimum 44x44 pixels on mobile devices per D12 §5
5. THE system SHALL achieve Lighthouse accessibility score of 100 per D03 §8.2

### Requirement 10: Performance and Core Web Vitals

**User Story:** As a system operator, I want the system to meet performance targets, so that users have a responsive experience.

**Reference:** D03 §8.2, docs/frontend/core-web-vitals-testing-guide.md

#### Acceptance Criteria

1. THE system SHALL achieve Largest Contentful Paint (LCP) under 2.5 seconds for guest forms per D03 §8.2
2. THE system SHALL achieve First Input Delay (FID) under 100ms per D03 §8.2
3. THE system SHALL achieve Cumulative Layout Shift (CLS) under 0.1 per D03 §8.2
4. THE system SHALL process queue notifications within 30 seconds of trigger per D17
5. THE system SHALL load Filament dashboard within 3 seconds with caching enabled per D03 §8.2

### Requirement 11: Bilingual Localization

**User Story:** As a MOTAC staff member, I want to use the system in Bahasa Melayu or English, so that I can work in my preferred language.

**Reference:** D15_LANGUAGE_MS_EN.md

#### Acceptance Criteria

1. THE system SHALL display all guest forms in bilingual format (Bahasa Melayu primary, English secondary) per D15 §2
2. WHEN a user selects a language preference THEN THE system SHALL persist the selection and apply it to all subsequent pages
3. THE system SHALL provide bilingual email notifications with both BM and EN content per D15 §4
4. THE system SHALL use consistent terminology across both languages as defined in GLOSSARY.md

### Requirement 12: Data Privacy and PDPA Compliance

**User Story:** As a data subject, I want my personal data protected according to PDPA 2010, so that my privacy is maintained.

**Reference:** D02 §8.2, D03 §11, D09 §8

#### Acceptance Criteria

1. WHEN a user submits a form THEN THE system SHALL require explicit PDPA acknowledgement checkbox before submission per D02 §8.2
2. THE system SHALL encrypt sensitive personal data (phone, email) at rest using AES-256 per D03 §11
3. THE system SHALL retain guest data for 7 years and purge non-essential attachments after 24 months per D02 §8.2
4. WHEN a data subject requests deletion THEN THE system SHALL process the request through official BPM channels while maintaining audit log integrity per D09 §8

### Requirement 13: Queue and Background Processing

**User Story:** As a system operator, I want reliable background job processing, so that notifications and tasks are executed without blocking user interactions.

**Reference:** D17_QUEUE_MANAGEMENT_HORIZON.md

#### Acceptance Criteria

1. THE system SHALL use Redis as queue backend with Laravel Horizon for monitoring per D17 §2
2. WHEN a job fails THEN THE system SHALL retry with exponential backoff (3 attempts) and notify superuser on final failure per D17 §4
3. THE system SHALL process email notifications within 60 seconds of trigger per D03 SRS-HELP-004
4. WHEN queue workers are unhealthy THEN THE system SHALL alert superuser via configured notification channel per D17 §5

### Requirement 14: Security and Rate Limiting

**User Story:** As a security administrator, I want the system protected against abuse, so that service availability is maintained.

**Reference:** D03 §8.1, docs/security/rate-limiting.md

#### Acceptance Criteria

1. THE system SHALL enforce rate limiting of 60 requests per minute per IP for guest forms per D03 §8.1
2. THE system SHALL implement reCAPTCHA Enterprise (invisible) on all guest forms per D03 §8.1
3. THE system SHALL scan uploaded files with ClamAV before allowing download per D03 §8.1
4. THE system SHALL hash all tokens using SHA-512 and store only hashes in database per D03 §8.1
5. THE system SHALL log all security events to immutable audit trail per D09 §8

### Requirement 15: Staff Self-Registration

**User Story:** As a MOTAC staff member, I want to register for a system account using my official email, so that I can access personalized features like submission history and profile management.

**Reference:** D00 §4.1, D01 §4.3, D02 FR-001, D03 SRS-AUTH-001

#### Acceptance Criteria

1. WHEN a user accesses the registration form at `/register` THEN THE system SHALL display a WCAG 2.2 AA compliant bilingual form with fields for name, email, password, and password confirmation per D01 §4.3
2. WHEN a user enters an email address THEN THE system SHALL validate that the email domain is `@motac.gov.my` and reject all other domains per D00 §4.1
3. WHEN a user submits valid registration data THEN THE system SHALL create a user account with role `staff` and status `pending_verification` per D03 SRS-AUTH-001
4. WHEN a user account is created THEN THE system SHALL send a verification email with a signed URL valid for 24 hours per D01 §4.3
5. WHEN a user clicks the verification link THEN THE system SHALL activate the account and redirect to login page with success message per D01 §4.3

### Requirement 16: Flexible Staff Login

**User Story:** As a registered MOTAC staff member, I want to log in using either my full email or short username, so that I can access my account conveniently.

**Reference:** D00 §4.1, D03 SRS-AUTH-001, D03 SRS-ADM-006

#### Acceptance Criteria

1. WHEN a user accesses the login form at `/login` THEN THE system SHALL display a WCAG 2.2 AA compliant bilingual form accepting email/username and password per D03 SRS-AUTH-001
2. WHEN a user enters a full email (`user@motac.gov.my`) THEN THE system SHALL authenticate against the email field per D03 SRS-AUTH-001
3. WHEN a user enters a short username (`user`) THEN THE system SHALL authenticate by matching the username portion of the email per D03 SRS-AUTH-001
4. WHEN authentication succeeds THEN THE system SHALL redirect to My Dashboard at `/dashboard` per D03 SRS-ADM-006
5. WHEN authentication fails THEN THE system SHALL display a generic error message without revealing whether email/username exists per D03 §8.1

### Requirement 17: Staff Dashboard (My Dashboard)

**User Story:** As an authenticated MOTAC staff member, I want to view my submission history and manage my profile, so that I can track my tickets and loan applications in one place.

**Reference:** D02 FR-001, D03 SRS-ADM-006, D04 §4.1

#### Acceptance Criteria

1. WHEN an authenticated staff user accesses `/dashboard` THEN THE system SHALL display a personalized dashboard with submission history, profile summary, and notification center per D03 SRS-ADM-006
2. WHEN viewing submission history THEN THE system SHALL display all helpdesk tickets and loan applications linked to the user's `user_id` per D02 FR-050
3. WHEN a staff user clicks on a submission THEN THE system SHALL display full details including status timeline and any public comments per D03 SRS-ADM-006
4. WHEN a staff user accesses profile settings THEN THE system SHALL allow editing of phone, division, and grade fields per D03 SRS-ADM-006
5. WHEN a staff user accesses notification preferences THEN THE system SHALL allow configuration of email frequency (immediate, daily digest, weekly digest) and in-app notification toggle per D16

### Requirement 18: Optional Account Linking

**User Story:** As a newly registered staff member, I want to link my previous guest submissions to my new account, so that I can view my complete submission history.

**Reference:** D00 §4.1, D02 FR-050, D03 SRS-DATA-001

#### Acceptance Criteria

1. WHEN a staff user accesses the account linking feature at `/dashboard/link-submissions` THEN THE system SHALL display an explanation of the linking process and a form to enter the email used for guest submissions per D02 FR-050
2. WHEN a staff user submits an email for linking THEN THE system SHALL search for all helpdesk tickets and loan applications with matching `submitter_email` or `applicant_email` where `user_id` is NULL per D03 SRS-DATA-001
3. WHEN matching submissions are found THEN THE system SHALL display a list of submissions for user confirmation before linking per D02 FR-050
4. WHEN a staff user confirms linking THEN THE system SHALL update the `user_id` field on all confirmed submissions and log the action in audit trail per D09 §4.6
5. WHEN linking is complete THEN THE system SHALL display a success message and redirect to dashboard showing the newly linked submissions per D03 SRS-ADM-006

### Requirement 19: Dual Audit System

**User Story:** As a compliance officer, I want comprehensive audit trails for both field-level changes and user activities, so that I can meet PDPA and governance requirements.

**Reference:** D00 §4.1, D09 §4.6, D09 §4.7

#### Acceptance Criteria

1. THE system SHALL use owen-it/laravel-auditing v14.x to record field-level changes (old/new values) on all auditable models per D09 §4.6
2. THE system SHALL use spatie/laravel-activitylog v4.x to record user activities for operational dashboards and reports per D09 §4.7
3. WHEN any auditable model is created, updated, or deleted THEN THE system SHALL create an audit record in the `audits` table with user ID, IP hash, timestamp, and field changes per D09 §4.6
4. WHEN any significant user action occurs THEN THE system SHALL create an activity log entry in the `activity_log` table with description, subject, causer, and properties per D09 §4.7
5. THE system SHALL retain audit records for 7 years to comply with PDPA and Arkib Negara requirements per D02 §8.2

### Requirement 20: Superuser Laravel Telescope Access

**User Story:** As a superuser, I want unrestricted access to Laravel Telescope for debugging and monitoring, so that I can diagnose system issues effectively.

**Reference:** D00 §4.1, D03 SRS-ADM-002

#### Acceptance Criteria

1. WHEN a superuser accesses `/telescope` THEN THE system SHALL display the full Laravel Telescope dashboard without any restrictions per D00 §4.1
2. THE system SHALL restrict Telescope access to users with role `superuser` only per D03 SRS-ADM-002
3. WHEN a non-superuser attempts to access Telescope THEN THE system SHALL return HTTP 403 Forbidden per D03 SRS-ADM-002
4. THE system SHALL enable all Telescope watchers including requests, commands, jobs, exceptions, logs, queries, models, events, mail, notifications, cache, and Redis per D00 §4.1

### Requirement 21: MOTAC Branding and Visual Identity

**User Story:** As a MOTAC staff member, I want to see official MOTAC branding (Jata Negara, MOTAC logo, BPM identity) throughout the system, so that I can recognize this as an official government application and trust its authenticity.

**Reference:** D12_UI_UX_DESIGN_GUIDE.md §2, D13_UI_UX_FRONTEND_FRAMEWORK.md §3, D14_UI_UX_STYLE_GUIDE.md §1, MyGOV Digital Service Standards v2.1.0

#### Acceptance Criteria

1. THE system SHALL display the Jata Negara (Malaysian Coat of Arms) from `public/images/jata-negara.svg` in the header of all public-facing pages (guest forms, status check, approval pages) at minimum 48x48 pixels per MyGOV DSS v2.1.0
2. THE system SHALL display the official MOTAC logo from `public/images/motac-logo.png` alongside the Jata Negara in the header with proper spacing (minimum 16px gap) and responsive sizing per D14 §1
3. THE system SHALL display the BPM (Bahagian Pengurusan Maklumat) logo from `public/images/bpm-logo.png` in form headers and footer sections to identify the operating division per D12 §2
4. WHEN displaying the Filament admin panel THEN THE system SHALL use the MOTAC logo as the brand logo with alt text from `common.motac_logo` translation key per D13 §3
5. THE system SHALL include MOTAC branding in all email templates with the Jata Negara header, MOTAC logo, and official ministry tagline "Kementerian Pelancongan, Seni dan Budaya Malaysia" per D15 §4
6. THE system SHALL display the MOTAC logo in browser notifications using `public/images/motac-logo-32.png` (32x32 raster derivative) per D16
7. THE system SHALL include MOTAC branding in PDF exports (audit reports, submission receipts) with Jata Negara, MOTAC logo, and official letterhead styling per D03 SRS-ADM-005
8. THE system SHALL provide favicon and PWA icons using MOTAC-branded assets (`favicon.ico`, `web-app-manifest-192x192.png`, `web-app-manifest-512x512.png`) per D13 §3
9. THE system SHALL display the official ministry full name "Kementerian Pelancongan, Seni dan Budaya Malaysia (MOTAC)" from `common.motac_full_name` translation key in the footer of all pages per D15 §2
10. THE system SHALL ensure all MOTAC imagery includes proper alt text in both BM (`common.motac_logo`, `common.jata_negara`) and EN translations for WCAG 2.2 AA compliance per D12 §4

### Requirement 22: Government Visual Standards Compliance

**User Story:** As a government compliance officer, I want the system to adhere to Malaysian government visual identity standards, so that it meets official branding requirements for government digital services.

**Reference:** MyGOV Digital Service Standards v2.1.0, D14_UI_UX_STYLE_GUIDE.md

#### Acceptance Criteria

1. THE system SHALL use the official MOTAC color palette with Primary Blue (#0056b3) as the dominant brand color per D14 §3
2. THE system SHALL display government branding elements (Jata Negara, MOTAC logo) with proper proportions and clear space requirements (minimum 8px padding around logos) per MyGOV DSS v2.1.0
3. THE system SHALL NOT distort, recolor, or modify official government imagery (Jata Negara, MOTAC logo) in any way per MyGOV DSS v2.1.0
4. THE system SHALL provide high-resolution logo variants for print exports (minimum 300 DPI for PDF generation) per D03 SRS-ADM-005
5. THE system SHALL display the official government disclaimer "Sistem Rasmi Kerajaan Malaysia" in the footer of all public pages per MyGOV DSS v2.1.0

### Requirement 23: MyGovEA Design Principles Compliance

**User Story:** As a government compliance officer, I want the system to follow MyGovEA design principles (Prinsip Reka Bentuk), so that it meets national digital service architecture standards and provides optimal user experience.

**Reference:** MyGovEA Prinsip Reka Bentuk, MyGovEA Arkitektur Perkhidmatan, SPDK Guidelines

#### Acceptance Criteria

1. THE system SHALL implement "Berpaksikan Rakyat" (Citizen-Centric) design by placing user needs as primary focus, involving users in testing, and collecting feedback for continuous improvement per MyGovEA §1
2. THE system SHALL follow "Antara Muka Minimalis dan Mudah" (Minimalist Interface) by avoiding unnecessary components, using consistent navigation, and ensuring intuitive user flows per MyGovEA §5
3. THE system SHALL implement "Pencegahan Ralat" (Error Prevention) by requiring confirmation dialogs before destructive actions (delete, cancel submission) and providing clear undo options where applicable per MyGovEA §17
4. THE system SHALL provide "Panduan dan Dokumentasi" (Guidance and Documentation) including contextual help tooltips, FAQ section, and user manual accessible from the footer per MyGovEA §18
5. THE system SHALL implement "Kognitif" (Cognitive) design by reducing information overload, organizing content logically, and providing clear visual feedback for user actions per MyGovEA §9
6. THE system SHALL follow "Struktur Hierarki" (Hierarchical Structure) by organizing navigation and content in logical hierarchies that facilitate easy discovery per MyGovEA §12
7. THE system SHALL implement "Kawalan Pengguna" (User Control) by providing clear, consistent controls that allow users to understand and predict system behavior per MyGovEA §16
8. THE system SHALL document future integration path with Identiti Digital Nasional (IDN) for centralized government SSO when MOTAC adopts national identity standards per MyGovEA Arkitektur Perkhidmatan §Identifikasi

### Requirement 24: Official Form Reference Codes

**User Story:** As a MOTAC staff member, I want to see official form reference codes on submission forms, so that I can identify the correct official document and reference it in communications.

**Reference:** PK.(S).MOTAC.07.(L1), PK.(S).MOTAC.07.(L3), MOTAC BPM Document Standards

#### Acceptance Criteria

1. WHEN displaying the helpdesk ticket form THEN THE system SHALL display the official form reference code "PK.(S).MOTAC.07.(L1)" in the form header area per MOTAC BPM standards
2. WHEN displaying the loan application form THEN THE system SHALL display the official form reference code "PK.(S).MOTAC.07.(L3)" in the form header area per MOTAC BPM standards
3. THE system SHALL store the form_reference_code field in both `helpdesk_tickets` and `loan_applications` tables for traceability
4. WHEN generating PDF exports or receipts THEN THE system SHALL include the official form reference code in the document header per MOTAC BPM standards
5. THE system SHALL display form reference codes in a consistent location (top-right of form container) matching the original paper form layout per PK.(S).MOTAC.07 series

### Requirement 25: Responsible Officer Workflow (Loan Applications)

**User Story:** As a MOTAC staff member applying for equipment loan, I want to designate a Responsible Officer (Pegawai Bertanggungjawab) who may differ from myself, so that accountability for the equipment is properly assigned per official loan procedures.

**Reference:** PK.(S).MOTAC.07.(L3) Part 2, D02 §6.2, D03 SRS-LOAN-001

#### Acceptance Criteria

1. WHEN a user accesses the loan application wizard THEN THE system SHALL include a "Responsible Officer" section with a checkbox "Applicant is the same as Responsible Officer" (default: checked) per PK.(S).MOTAC.07.(L3) Part 2
2. WHEN the checkbox is unchecked THEN THE system SHALL display additional fields for Responsible Officer: name, position & grade, and phone number per PK.(S).MOTAC.07.(L3) Part 2
3. WHEN the checkbox is checked THEN THE system SHALL auto-populate Responsible Officer fields from Applicant data and hide the additional input fields
4. THE system SHALL store Responsible Officer information in dedicated fields: `responsible_officer_name`, `responsible_officer_grade`, `responsible_officer_phone`, and `is_applicant_responsible` (boolean) per D09 schema
5. WHEN displaying loan application details (status check, admin view, PDF export) THEN THE system SHALL clearly show both Applicant and Responsible Officer information when they differ
6. THE system SHALL include Responsible Officer acknowledgement statement: "Saya memperakui bahawa peralatan yang dipinjam adalah untuk kegunaan rasmi dan akan berada di bawah tanggungjawab dan pengawasan saya sepanjang tempoh pinjaman" per PK.(S).MOTAC.07.(L3) Part 4

### Requirement 26: Asset Accessory Tracking

**User Story:** As a BPM admin, I want to track standard accessories (Power Adapter, Bag, Mouse, cables) during equipment check-out and check-in, so that I can ensure complete equipment return and identify missing items.

**Reference:** PK.(S).MOTAC.07.(L3) Part 8, D03 SRS-LOAN-007, D09 §4.4

#### Acceptance Criteria

1. WHEN an admin performs asset check-out THEN THE system SHALL display an accessory checklist with standard items: Power Adapter, Bag (Beg), Mouse, USB Cable (Kabel USB), HDMI/VGA Cable (Kabel HDMI/VGA), Remote, and Others (Lain-lain) per PK.(S).MOTAC.07.(L3) Part 8
2. FOR each accessory item THEN THE system SHALL allow the admin to mark as "Included" or "Not Included" with optional condition notes
3. WHEN "Others" is selected THEN THE system SHALL provide a text field to specify the additional accessory name
4. WHEN an admin performs asset check-in THEN THE system SHALL display the same accessory checklist pre-populated with check-out data for comparison
5. THE system SHALL highlight any discrepancies between check-out and check-in accessory status (missing items, condition changes)
6. THE system SHALL store accessory tracking data in `loan_transaction_accessories` table with fields: transaction_id, accessory_type (enum), accessory_name (for Others), present_at_checkout, present_at_checkin, condition_notes per D09 schema
7. WHEN generating loan transaction reports THEN THE system SHALL include complete accessory tracking information for audit purposes

### Requirement 27: MYDS Design Guidelines Adoption

**User Story:** As a government compliance officer, I want the system to adopt Malaysia Government Design System (MYDS) best practices for layout, typography, and visual consistency, so that it aligns with national digital service design guidelines while using our existing Tailwind CSS and Livewire component stack.

**Reference:** MYDS Design Guidelines (design.digital.gov.my), D12_UI_UX_DESIGN_GUIDE.md, D14_UI_UX_STYLE_GUIDE.md

**Note:** This requirement adopts MYDS design principles and guidelines as best practices. The system uses Tailwind CSS 4.x, Livewire 3.7, and Filament 4.1 components (not MYDS React components) configured to follow MYDS specifications.

#### Acceptance Criteria

1. THE system SHALL implement MYDS-aligned responsive breakpoints: desktop (≥1024px), tablet (768-1023px), and mobile (≤767px) using Tailwind CSS responsive utilities per MYDS Grid System guidelines
2. THE system SHALL use Inter font family (or system font fallback) for body text and paragraph content to ensure comfortable reading experience per MYDS Typography guidelines
3. THE system SHALL use Poppins font family (or appropriate heading font) for page headings and section titles to create clear visual hierarchy per MYDS Typography guidelines
4. THE system SHALL implement consistent spacing using 8px base unit scale (8, 16, 24, 32, 48, 64px) via Tailwind spacing utilities per MYDS Spacing guidelines
5. THE system SHALL organize color usage with semantic naming conventions (background, text, border, focus) following MYDS Colour System principles while maintaining MOTAC brand colors (#0056b3 primary)
6. THE system SHALL implement consistent border radius values via Tailwind: rounded-sm (4px), rounded (6-8px), rounded-lg (12px), rounded-xl (14px), rounded-full per MYDS Radius guidelines
7. THE system SHALL apply appropriate shadow depth for UI hierarchy (buttons, cards, dropdowns) using Tailwind shadow utilities per MYDS Shadow guidelines
8. THE system SHALL implement smooth UI transitions: 200ms for micro-interactions, 400ms for modals/alerts, 600ms for page transitions using Tailwind transition utilities per MYDS Motion guidelines
9. THE system SHALL use consistent icon sizing: 16px (small), 20px (base), 24px (large), 32-42px (dialogs) with Heroicons or compatible icon set per MYDS Icon guidelines
10. THE system SHALL configure Tailwind CSS 4.x theme to align with MYDS design tokens where applicable, documented in D14 Style Guide

### Requirement 28: Enhanced Division Dropdown with Fuzzy Search

**User Story:** As a MOTAC staff member filling out forms, I want an intelligent division dropdown with fuzzy search and IP subnet detection, so that I can quickly find my division even with typos or partial matches.

**Reference:** D03 SRS-HELP-002, D12 §4, D13 §6

#### Acceptance Criteria

1. WHEN a user accesses the division dropdown THEN THE system SHALL provide a searchable dropdown with fuzzy search capability al typo tolerance and partial matching per D03 SRS-HELP-002
2. WHEN a user types in the division field THEN THE system SHALL filter results in real-time with highlighting of matched characters and display up to 10 most relevant results
3. WHEN the system detects the user's IP subnet THEN THE system SHALL auto-suggest the most likely division based on network location with option to override
4. WHEN no exact match is found THEN THE system SHALL display "Did you mean..." suggestions with Levenshtein distance algorithm for typo correction
5. THE system SHALL maintain a searchable list of all MOTAC divisions with aliases and common abbreviations (e.g., "BPM", "Bahagian Pengurusan Maklumat")
6. THE system SHALL provide WCAG 2.2 AA compliant dropdown with keyboard navigation (arrow keys, enter to select, escape to close) and screen reader announcements per D12 §4
7. WHEN a division is selected THEN THE system SHALL populate related fields (division code, location) automatically if available

### Requirement 29: Email Reply-to-Ticket Integration

**User Story:** As a MOTAC staff member, I want to reply to ticket notifications via email, so that I can provide additional information without logging into the system.

**Reference:** D03 SRS-HELP-006, D16, D17

#### Acceptance Criteria

1. WHEN a ticket notification email is sent THEN THE system SHALL include a unique reply-to email address in format `ticket-{ticket_id}-{token}@ictserve.motac.gov.my` per D03 SRS-HELP-006
2. WHEN a user replies to a ticket notification email THEN THE system SHALL process the reply via IMAP integration or webhook and add it as a comment to the ticket
3. WHEN processing email replies THEN THE system SHALL extract the message body, strip email signatures and quoted text, and sanitize HTML content
4. WHEN an email reply is received THEN THE system SHALL validate the sender email against the original ticket submitter and notify assigned admin within 60 seconds per D16
5. THE system SHALL support email attachments up to 5MB total per reply and scan them with ClamAV before storage per D03 §8.1
6. THE system SHALL maintain email thread integrity by preserving Message-ID and References headers for proper email client threading
7. WHEN email processing fails THEN THE system SHALL send an auto-reply to the sender explaining the issue and providing alternative contact methods

### Requirement 30: Pickup OTP Digital Handshake

**User Story:** As a BPM admin and loan applicant, I want a secure OTP-based pickup process for asset collection, so that equipment is released only to authorized personnel.

**Reference:** D03 SRS-LOAN-007, D11 §7

#### Acceptance Criteria

1. WHEN a loan application is approved and ready for collection THEN THE system SHALL generate a 4-digit OTP with 24-hour validity and send it to the applicant's email and SMS per D03 SRS-LOAN-007
2. WHEN an applicant arrives for pickup THEN THE admin SHALL enter the OTP in the Filament check-out interface to verify authorization before releasing equipment
3. WHEN the correct OTP is entered THEN THE system SHALL proceed with the check-out process and invalidate the OTP immediately
4. WHEN an incorrect OTP is entered THEN THE system SHALL log the attempt, allow 3 attempts total, and lock the pickup for 1 hour after failed attempts
5. THE system SHALL display OTP status in the loan application view (generated, used, expired, locked) with timestamp information
6. THE system SHALL send OTP reminder notifications 2 hours before expiry and allow superuser to regenerate expired OTPs
7. WHEN OTP verification succeeds THEN THE system SHALL record the pickup timestamp, admin ID, and OTP verification in the audit trail per D09 §4.6

### Requirement 31: Session Timeout with Warning Modal

**User Story:** As a security-conscious user, I want to be warned before my session expires, so that I can save my work and extend my session if needed.

**Reference:** D03 §8.1, D12 §4

#### Acceptance Criteria

1. THE system SHALL implement a 30-minute session timeout for all authenticated users with automatic logout after inactivity per D03 §8.1
2. WHEN 2 minutes remain before session expiry THEN THE system SHALL display a modal warning with countdown timer and options to "Extend Session" or "Logout Now"
3. WHEN a user clicks "Extend Session" THEN THE system SHALL refresh the session token and reset the 30-minute timer
4. WHEN the countdown reaches zero THEN THE system SHALL automatically log out the user and redirect to login page with session expired message
5. THE system SHALL detect user activity (mouse movement, keyboard input, AJAX requests) and reset the timeout counter silently
6. THE system SHALL provide WCAG 2.2 AA compliant warning modal with keyboard navigation and screen reader announcements per D12 §4
7. WHEN a user has unsaved form data THEN THE system SHALL warn about potential data loss in the session timeout modal

### Requirement 32: Onboarding Tour and Contextual Help

**User Story:** As a new MOTAC staff member, I want an interactive onboarding tour and contextual help, so that I can learn to use the system effectively.

**Reference:** D12 §4, D13 §6, MyGovEA Prinsip Reka Bentuk §18

#### Acceptance Criteria

1. WHEN a new user logs in for the first time THEN THE system SHALL offer an optional interactive onboarding tour highlighting key features and navigation per D12 §4
2. WHEN a user starts the onboarding tour THEN THE system SHALL provide step-by-step guidance with spotlight effects, tooltips, and progress indicators
3. WHEN users encounter complex form fields THEN THE system SHALL provide contextual help tooltips with detailed explanations and examples per MyGovEA §18
4. THE system SHALL include a help center accessible from the main navigation with searchable FAQ, video tutorials, and user manual
5. THE system SHALL provide "What's New" notifications for feature updates with dismissible banners and changelog links
6. THE system SHALL offer contextual help based on user role (staff see basic features, admin see management features, superuser see all features)
7. WHEN users access help content THEN THE system SHALL track usage analytics to improve help content and identify common pain points per D09 §4.7

### Requirement 33: Advanced Dashboard Widgets with Real-time Updates

**User Story:** As a BPM admin, I want advanced dashboard widgets with real-time updates and customization options, so that I can monitor system performance and workload effectively.

**Reference:** D03 SRS-ADM-003, D16, D04 §4.1

#### Acceptance Criteria

1. WHEN an admin accesses the dashboard THEN THE system SHALL display 6 customizable widgets: Ticket Stats, Loan Stats, SLA Compliance, Recent Activity, Performance Metrics, and System Health per D03 SRS-ADM-003
2. WHEN dashboard data changes THEN THE system SHALL update widgets in real-time using Laravel Reverb WebSocket with 30-second refresh intervals per D16
3. WHEN viewing ticket statistics THEN THE system SHALL display open, in-progress, resolved counts with trend indicators (up/down arrows) and percentage changes from previous period
4. WHEN viewing SLA compliance THEN THE system SHALL show compliance percentage with color-coded indicators (green >95%, yellow 85-95%, red <85%) and breach alerts
5. THE system SHALL allow widget customization including position, size, and visibility with user preferences stored per role
6. THE system SHALL provide drill-down capability from widgets to detailed views (click ticket count to view ticket list with applied filters)
7. WHEN system performance degrades THEN THE system SHALL display warning indicators in the System Health widget with automatic admin notifications per D17 §5

### Requirement 34: Saved Filter Preferences and Advanced Search

**User Story:** As a frequent system user, I want to save my filter preferences and use advanced search capabilities, so that I can quickly access relevant information without reconfiguring filters each time.

**Reference:** D03 SRS-ADM-003, D04 §4.1

#### Acceptance Criteria

1. WHEN a user applies filters in any Filament resource THEN THE system SHALL provide an option to save the filter combination with a custom name
2. WHEN viewing saved filters THEN THE system SHALL display them in a dropdown menu with options to apply, edit, or delete each saved filter
3. WHEN using advanced search THEN THE system SHALL support multiple criteria with AND/OR operators, date ranges, and wildcard matching
4. THE system SHALL provide search suggestions based on user history and popular searches with autocomplete functionality
5. THE system SHALL maintain separate saved filters per user and per resource (ticket filters separate from loan filters)
6. THE system SHALL allow sharing of saved filters between users of the same role with permission controls
7. WHEN search results are displayed THEN THE system SHALL highlight matching terms and provide export options (CSV, PDF) for filtered results per D03 SRS-ADM-005

### Requirement 35: Mobile-Optimized Portal with Touch Gestures

**User Story:** As a MOTAC staff member using mobile devices, I want a mobile-optimized portal with touch gestures, so that I can access ICTServe features effectively on smartphones and tablets.

**Reference:** D12 §5, D13 §6, MYDS Grid System

#### Acceptance Criteria

1. THE system SHALL provide a responsive mobile interface optimized for devices with screen widths from 320px to 768px per D12 §5
2. WHEN using touch devices THEN THE system SHALL support swipe gestures for navigation (swipe left/right for tabs, swipe down for refresh)
3. WHEN viewing lists on mobile THEN THE system SHALL implement pull-to-refresh functionality and infinite scroll for large datasets
4. THE system SHALL optimize form layouts for mobile with collapsible sections, floating labels, and touch-friendly input controls per MYDS Grid System
5. THE system SHALL provide mobile-specific navigation with hamburger menu, bottom navigation bar, and breadcrumb trails
6. THE system SHALL implement offline capability for viewing previously loaded content with sync when connection is restored
7. WHEN using mobile cameras THEN THE system SHALL allow direct photo capture for attachments and damage reports with image compression

### Requirement 36: Application Performance Monitoring (Laravel Pulse)

**User Story:** As a system administrator, I want real-time performance monitoring dashboards, so that I can proactively identify and resolve performance issues before they impact users.

**Reference:** D03 §8.2, Laravel Pulse Documentation v1.3.0

#### Acceptance Criteria

1. THE system SHALL integrate Laravel Pulse v1.3.0 for real-time application performance monitoring per D03 §8.2
2. THE system SHALL track slow database queries (>500ms threshold) and display in Pulse dashboard with query details, frequency, and execution time per D03 §8.2
3. THE system SHALL monitor queue job performance including processing time, failure rates, and retry patterns per D17
4. THE system SHALL track user request patterns including response times, memory usage, and cache hit rates per D03 §8.2
5. THE system SHALL provide server health metrics including CPU usage, memory consumption, and disk space utilization per D03 §8.2
6. THE system SHALL restrict Pulse dashboard access to admin and superuser roles via `/pulse` route with proper authorization gates per D03 SRS-ADM-002
7. THE system SHALL retain Pulse data for 7 days with automatic pruning to manage storage requirements per D03 §8.2
8. WHEN performance thresholds are exceeded THEN THE system SHALL trigger alerts via configured notification channels per D17 §5

### Requirement 37: API Authentication (Future Consideration)

**User Story:** As a system architect, I want API authentication capabilities, so that the system can support future mobile applications and external integrations while maintaining security standards.

**Reference:** D03 SRS-API-001, Laravel Sanctum Documentation v4.0

#### Acceptance Criteria

1. THE system SHALL integrate Laravel Sanctum v4.0 for API token authentication per D03 SRS-API-001
2. THE system SHALL support token-based authentication for API endpoints with configurable expiration periods (default: 30 days) per D03 SRS-API-001
3. THE system SHALL implement token abilities for fine-grained API permissions (read:tickets, write:tickets, read:loans, write:loans, admin:all) per D03 SRS-API-001
4. THE system SHALL enforce rate limiting on API endpoints (60 requests/minute for authenticated tokens, 10 requests/minute for unauthenticated) per D03 §8.1
5. THE system SHALL log all API authentication attempts and token usage in audit trail per D09 §4.6

### Requirement 38: Google Workspace SSO (Optional Enhancement)

**User Story:** As a MOTAC staff member, I want to log in using my Google Workspace account, so that I can access ICTServe without managing a separate password.

**Reference:** D03 SRS-AUTH-001, Laravel Socialite Documentation v5.x

**Note:** This requirement is optional and depends on MOTAC using Google Workspace for official email. Implementation should be confirmed with MOTAC IT policy.

#### Acceptance Criteria

1. THE system SHALL integrate Laravel Socialite v5.x for Google OAuth 2.0 authentication per D03 SRS-AUTH-001
2. THE system SHALL restrict Google login to `@motac.gov.my` email domain only, rejecting all other domains per D00 §4.1
3. WHEN a first-time Google user authenticates THEN THE system SHALL auto-create a staff account with role `staff` and status `active` using Google profile data (name, email) per D03 SRS-AUTH-001
4. WHEN an existing user authenticates via Google THEN THE system SHALL link the Google OAuth to the existing account when email matches per D03 SRS-AUTH-001
5. THE system SHALL display "Sign in with Google" button on the login page alongside traditional email/password login per D12 §4
6. THE system SHALL log all Google OAuth authentication events in audit trail per D09 §4.6
7. WHEN Google OAuth fails THEN THE system SHALL display a clear error message and fallback to traditional login per D03 §8.1

## Summary

This requirements document for ICTServe v3.5.0 defines **38 requirements** covering the True Hybrid Architecture with comprehensive features:

**Core Modules (Requirements 1-8):**

- Hybrid Helpdesk Ticket Submission with guest/authenticated modes
- Helpdesk Status Checking via token links
- Hybrid Asset Loan Application with multi-step wizard
- Email-Based Loan Approval for Grade 41+ officers
- Admin Helpdesk and Asset Loan Management via Filament
- Superuser Configuration and Audit capabilities
- Real-Time Notifications via Laravel Reverb

**Compliance and Standards (Requirements 9-14):**

- WCAG 2.2 AA Accessibility Compliance
- Performance and Core Web Vitals optimization
- Bilingual Localization (BM/EN)
- Data Privacy and PDPA Compliance
- Queue and Background Processing
- Security and Rate Limiting

**Authentication and User Management (Requirements 15-20):**

- Staff Self-Registration with @motac.gov.my validation
- Flexible Staff Login (email or username)
- Staff Dashboard (My Dashboard)
- Optional Account Linking for guest submissions
- Dual Audit System (owen-it + spatie)
- Superuser Laravel Telescope Access

**Branding and Design (Requirements 21-27):**

- MOTAC Branding and Visual Identity
- Government Visual Standards Compliance
- MyGovEA Design Principles Compliance
- Official Form Reference Codes
- Responsible Officer Workflow
- Asset Accessory Tracking
- MYDS Design Guidelines Adoption

**Enhanced Features (Requirements 28-35):**

- Enhanced Division Dropdown with Fuzzy Search
- Email Reply-to-Ticket Integration
- Pickup OTP Digital Handshake
- Session Timeout with Warning Modal
- Onboarding Tour and Contextual Help
- Advanced Dashboard Widgets with Real-time Updates
- Saved Filter Preferences and Advanced Search
- Mobile-Optimized Portal with Touch Gestures

**Monitoring, API, and Integration (Requirements 36-38):**

- Application Performance Monitoring (Laravel Pulse) - Real-time performance dashboards for proactive issue identification
- API Authentication (Laravel Sanctum) - Token-based API authentication for future mobile/external integrations
- Google Workspace SSO (Optional) - OAuth 2.0 integration for seamless staff authentication (if MOTAC uses Google Workspace)

All requirements adhere to D00-D17 ICTServe core documentation (updated 1 December 2025) and Malaysian government digital service standards.

**Technology Stack (per D00 §4.1):**

- Laravel 12.40.1 (PHP 8.2.12) - Core framework
- Livewire 3.7.0 + Volt 1.10.1 - Reactive components
- Filament 4.1.10 - Admin panel (SDUI)
- Alpine.js 3 - Frontend interactivity
- Tailwind CSS 4.1.17 - Utility-first styling
- Laravel Reverb 1.6.2 - WebSocket server
- Laravel Echo 2.2.6 - WebSocket client
- Laravel Breeze 2.3.8 - Authentication scaffolding
- Laravel Pulse v1.3.0 - Performance monitoring (admin/superuser)
- Laravel Sanctum v4.0 - API token authentication
- Laravel Socialite v5.x - Google Workspace SSO (optional)
- Laravel Telescope v5.x - Debugging (superuser only, unrestricted)
- owen-it/laravel-auditing v14.x - Field-level compliance audit
- spatie/laravel-activitylog v4.x - User activity logging
- Spatie Laravel Permission 6.23 - Role-based access control
- PHPUnit 11.5.44 - Testing framework
- Playwright 1.56.1 - E2E browser testing
- Larastan 3.8.0 - Static analysis
- Laravel Pint 1.26.0 - Code formatting (PSR-12)

**Compliance Standards:**

- WCAG 2.2 AA - Web accessibility
- PDPA 2010 - Malaysian data protection
- MyGOV Digital Service Standards v2.1.0 - Government digital services
- ISO/IEC/IEEE 29148 - Requirements engineering
- ISO/IEC/IEEE 15288 - System lifecycle processes
- OWASP ASVS L2 - Application security

**Spec Deliverables (per D00 changelog):**

- 38 requirements with EARS-compliant acceptance criteria
- 100 correctness properties for property-based testing
- 19 implementation phases in tasks.md
