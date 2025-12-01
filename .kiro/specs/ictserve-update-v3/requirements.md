# Requirements Document

## Introduction

ICTServe Update v3 consolidates the system to version 3.5.0, implementing the **True Hybrid Architecture** for MOTAC staff. This architecture enables flexible access modes: staff can self-register with `@motac.gov.my` email, log in for personalized dashboard access, OR use guest forms for quick submissions without authentication. The system uses Laravel 12.40.1 stack with comprehensive compliance standards (WCAG 2.2 AA, PDPA 2010, MyGOV Digital Service Standards v2.1.0).

**Key v3.5.0 Features (per D00 §4.1):**

- Self-registration with `@motac.gov.my` email domain validation
- Flexible login (full email OR short username)
- Optional guest-to-account linking for historical submissions
- Dual audit system (owen-it/laravel-auditing + spatie/laravel-activitylog)
- Laravel Telescope debugging (superuser only, unrestricted)
- Multi-channel notifications with user preferences

**Reference Documents:**

- D00_SYSTEM_OVERVIEW.md - System vision and governance (v3.5.0)
- D01_SYSTEM_DEVELOPMENT_PLAN.md - Development methodology (v3.5.0)
- D02_BUSINESS_REQUIREMENTS_SPECIFICATION.md - Business requirements (v3.5.0)
- D03_SOFTWARE_REQUIREMENTS_SPECIFICATION.md - Software requirements (v3.5.0)
- D04_SOFTWARE_DESIGN_DOCUMENT.md - Architecture and design (v3.5.0)
- D09_DATABASE_DOCUMENTATION.md - Database schema and dual audit
- D10_SOURCE_CODE_DOCUMENTATION.md - Code organization
- D11_TECHNICAL_DESIGN_DOCUMENTATION.md - Technical infrastructure
- D12_UI_UX_DESIGN_GUIDE.md - UI/UX guidelines
- D13_UI_UX_FRONTEND_FRAMEWORK.md - Frontend framework
- D14_UI_UX_STYLE_GUIDE.md - Style guide
- D15_LANGUAGE_MS_EN.md - Bilingual localization
- D16_BROADCASTING_SETUP.md - WebSocket configuration
- D17_QUEUE_MANAGEMENT_HORIZON.md - Queue management

## Glossary

- **ICTServe**: Internal ICT service management platform for MOTAC BPM (Bahagian Pengurusan Maklumat)
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
