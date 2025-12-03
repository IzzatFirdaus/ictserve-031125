# Implementation Plan

## ICTServe Update v3 - True Hybrid Architecture v3.5.0

This implementation plan converts the feature design into discrete, actionable coding tasks. Each task builds incrementally on previous tasks, ensuring no orphaned code.

**Reference Documents:**

- `.kiro/specs/ictserve-update-v3/requirements.md` - 38 requirements with acceptance criteria (including MOTAC branding, MyGovEA, Form Codes, Responsible Officer, Accessory Tracking, Laravel Pulse, API Authentication, Google SSO)
- `.kiro/specs/ictserve-update-v3/design.md` - Architecture, services, 100 correctness properties
- `docs/D00-D17` - System documentation suite
- `docs/D12_UI_UX_DESIGN_GUIDE.md` - UI/UX guidelines including MOTAC branding
- `docs/D14_UI_UX_STYLE_GUIDE.md` - Style guide with MOTAC color palette
- `_reference/MYGOVEA-Prinsip-Reka-Bentuk.md` - MyGovEA Design Principles
- `_reference/PK.(S).MOTAC.07.(L1)` - Helpdesk Form Reference
- `_reference/PK.(S).MOTAC.07.(L3)` - Loan Application Form Reference

**Testing Framework:** PHPUnit 11.5.44 with Faker for property-based testing

---

## Phase 1: Database Schema and Migrations

- [x] 1. Database Schema and Migrations

  - [x] 1.1 Create users table migration with True Hybrid fields

    - Add role enum (staff, admin, superuser), locale, notification_preferences JSON, two_factor_secret
    - Add email_verified_at for self-registration flow
    - Add staff_number, division_code, grade fields
    - Add google_id (VARCHAR 255, nullable, unique) for Google OAuth per Req 38
    - Add google_token, google_refresh_token (TEXT, nullable, encrypted) per Req 38
    - Add last_login_at, last_login_ip for audit trail
    - Add guest_submissions_linked counter for account linking
    - _Requirements: 15.3, 16.1, 17.4, 19.1, 38.3, 38.4_

  - [ ]\* 1.2 Write property test for user role validation

    - **Property 46: Telescope Role Restriction**
    - **Validates: Requirements 20.2, 20.3**

  - [x] 1.3 Update helpdesk_tickets migration with user_id nullable FK

    - Add user_id (nullable FK to users, ON DELETE SET NULL)
    - Add index on user_id for My Dashboard queries
    - Add status_token_hash (VARCHAR 128, indexed)
    - Add form_reference_code (VARCHAR 50, default 'PK.(S).MOTAC.07.(L1)')
    - Retain all submitter\_\* columns for guest fallback
    - _Requirements: 1.5, 17.2, 18.2, 24.3_

  - [x] 1.4 Update loan_applications migration with user_id nullable FK

    - Add user_id (nullable FK to users, ON DELETE SET NULL)
    - Add index on user_id for My Dashboard queries
    - Add approval_token_hash, status_token_hash (VARCHAR 128, indexed)
    - Add approval_token_expires_at timestamp
    - Add form_reference_code (VARCHAR 50, default 'PK.(S).MOTAC.07.(L3)')
    - Add is_applicant_responsible (BOOLEAN, default TRUE)
    - Add responsible_officer_name, responsible_officer_grade, responsible_officer_phone
    - Add responsible_officer_acknowledgement (BOOLEAN)
    - Retain all applicant\_\* columns for guest fallback
    - _Requirements: 3.5, 17.2, 18.2, 24.3, 25.4_

  - [x] 1.5 Create loan_approvals table migration

    - Fields: loan_application_id (FK), approver_email, approver_grade, decision (enum), remarks, decision_at, decision_ip_hash, token_hash
    - _Requirements: 4.3_

  - [x] 1.6 Create loan_transactions table migration

    - Fields: loan_application_id (FK), asset_id (FK), transaction_type (enum: CHECK_OUT, CHECK_IN), admin_id (FK), condition_notes, damage_reported, damage_photos (JSON), transaction_at
    - _Requirements: 6.1, 6.2_

  - [x] 1.7 Create loan_transaction_accessories table migration

    - Fields: loan_transaction_id (FK), accessory_type (enum: POWER_ADAPTER, BAG, MOUSE, USB_CABLE, HDMI_VGA_CABLE, REMOTE, OTHERS)
    - Fields: accessory_name (VARCHAR 100, nullable for OTHERS), present_at_checkout (BOOLEAN), present_at_checkin (BOOLEAN, nullable), condition_notes (TEXT)
    - _Requirements: 26.6_

  - [x] 1.8 Create personal_access_tokens migration (Laravel Sanctum)

    - Standard Sanctum migration for API token authentication
    - Fields: tokenable_type, tokenable_id, name, token, abilities, last_used_at, expires_at
    - _Requirements: 37.1, 37.2_

  - [x] 1.9 Create api_token_usage_logs migration

    - Fields: personal_access_token_id (FK), user_id (FK), action, endpoint, ip_hash, user_agent, response_status, created_at
    - Add indexes for user_id and created_at
    - _Requirements: 37.5_

  - [x] 1.10 Create Laravel Pulse migrations

    - Run `php artisan vendor:publish --provider="Laravel\Pulse\PulseServiceProvider"`
    - Verify pulse_entries, pulse_values, pulse_aggregates tables
    - _Requirements: 36.1_

  - [x] 1.11 Verify dual audit tables exist (audits, activity_log)
    - Ensure owen-it/laravel-auditing and spatie/laravel-activitylog migrations are run
    - _Requirements: 19.1, 19.2_

- [x] 2. Checkpoint - Ensure all migrations pass
  - Run `php artisan migrate` and verify schema
  - Ensure all tests pass, ask the user if questions arise.

---

## Phase 2: Core Models and Relationships

- [x] 3. Core Models and Relationships

  - [x] 3.1 Update User model with True Hybrid fields

    - Add role accessor/mutator, notification_preferences cast, locale attribute
    - Add relationships: helpdeskTickets(), loanApplications(), assignedTickets()
    - Implement Auditable trait (owen-it) and LogsActivity trait (spatie)
    - Add extractUsernameFromEmail() method for flexible login
    - Add Google OAuth attributes: google_id, google_token, google_refresh_token
    - Add isGoogleLinked() method
    - _Requirements: 15.3, 17.4, 19.1, 19.2, 38.3, 38.4_

  - [ ]\* 3.2 Write property test for email domain validation

    - **Property 35: Email Domain Validation**
    - **Validates: Requirements 15.2**

  - [x] 3.3 Update HelpdeskTicket model with hybrid data association

    - Add user() relationship (nullable belongsTo)
    - Add status_token_hash attribute with SHA-512 hashing
    - Implement Auditable and LogsActivity traits
    - Add scopes: forUser(), byStatusToken(), bySLA()
    - Add ticket_number generation (HD-YYYYMM-XXXX format)
    - Add form_reference_code attribute with default 'PK.(S).MOTAC.07.(L1)'
    - _Requirements: 1.5, 2.1, 17.2, 24.3_

  - [ ]\* 3.4 Write property test for ticket number format

    - **Property 1: Ticket Number Format Consistency**
    - **Validates: Requirements 1.3**

  - [ ]\* 3.5 Write property test for status token round-trip

    - **Property 5: Status Token Lookup Round-Trip**
    - **Validates: Requirements 2.1**

  - [x] 3.6 Update LoanApplication model with hybrid data association

    - Add user() relationship (nullable belongsTo)
    - Add approval_token_hash, status_token_hash attributes
    - Implement Auditable and LogsActivity traits
    - Add scopes: forUser(), byApprovalToken(), byStatusToken()
    - Add reference generation (LA-YYYYMM-XXXX format)
    - Add form_reference_code attribute with default 'PK.(S).MOTAC.07.(L3)'
    - Add Responsible Officer fields and relationships
    - _Requirements: 3.5, 4.1, 17.2, 24.3, 25.4_

  - [ ]\* 3.7 Write property test for loan reference format

    - **Property 8: Loan Reference Format Consistency**
    - **Validates: Requirements 3.3**

  - [x] 3.8 Create LoanApproval model

    - Add loanApplication() relationship
    - Add decision enum cast, token_hash attribute
    - Implement Auditable and LogsActivity traits
    - _Requirements: 4.3_

  - [ ]\* 3.9 Write property test for approval decision recording

    - **Property 12: Approval Decision Recording**
    - **Validates: Requirements 4.3**

  - [x] 3.10 Create LoanTransaction model

    - Add loanApplication(), asset(), admin() relationships
    - Add transaction_type enum cast, damage_photos JSON cast
    - Implement Auditable and LogsActivity traits
    - _Requirements: 6.1, 6.2_

  - [x] 3.11 Create LoanTransactionAccessory model

    - Add loanTransaction() relationship
    - Add accessory_type enum cast
    - Implement Auditable and LogsActivity traits
    - _Requirements: 26.6_

  - [ ]\* 3.12 Write property test for check-out transaction recording

    - **Property 18: Check-out Transaction Recording**
    - **Validates: Requirements 6.1**

  - [x] 3.13 Create ApiTokenUsageLog model

    - Add relationships to PersonalAccessToken and User
    - Implement Auditable trait
    - _Requirements: 37.5_

- [x] 4. Checkpoint - Ensure all model tests pass
  - Run `php artisan test --filter=Model`
  - Ensure all tests pass, ask the user if questions arise.

---

## Phase 3: Service Layer Implementation

- [-] 5. Token Service Implementation

  - [x] 5.1 Create TokenService with interface

    - Implement generateStatusToken(), generateApprovalToken()
    - Implement validateStatusToken(), validateApprovalToken()
    - Use SHA-512 hashing for all tokens
    - _Requirements: 1.5, 4.1, 14.4_

  - [ ]\* 5.2 Write property test for token hash security

    - **Property 33: Token Hash Security**
    - **Validates: Requirements 14.4**

  - [ ]\* 5.3 Write property test for status token generation

    - **Property 4: Status Token Generation**
    - **Validates: Requirements 1.5**

  - [ ]\* 5.4 Write property test for approval token validation
    - **Property 11: Approval Token Generation and Validation**
    - **Validates: Requirements 4.1**

- [-] 6. Helpdesk Service Implementation

  - [x] 6.1 Create HelpdeskService with interface

    - Implement createTicket() with hybrid user_id logic
    - Implement updateStatus(), assignTicket()
    - Implement getByStatusToken(), calculateSLADueDate()
    - _Requirements: 1.5, 2.1, 5.3, 5.4_

  - [ ]\* 6.2 Write property test for invalid token rejection

    - **Property 6: Invalid Token Rejection**
    - **Validates: Requirements 2.2**

  - [ ]\* 6.3 Write property test for SLA warning calculation
    - **Property 17: SLA Warning Calculation**
    - **Validates: Requirements 5.5**

- [-] 7. Loan Service Implementation

  - [x] 7.1 Create LoanService with interface

    - Implement createApplication() with hybrid user_id logic
    - Implement checkAssetAvailability() with conflict detection
    - Implement processApproval(), checkOut(), checkIn()
    - Implement createMaintenanceTicket() for damaged assets
    - _Requirements: 3.2, 3.4, 4.5, 6.1, 6.2, 6.3_

  - [ ]\* 7.2 Write property test for asset availability conflict detection

    - **Property 9: Asset Availability Conflict Detection**
    - **Validates: Requirements 3.2**

  - [ ]\* 7.3 Write property test for asset soft-lock on application

    - **Property 10: Asset Soft-Lock on Application**
    - **Validates: Requirements 3.4**

  - [ ]\* 7.4 Write property test for approval status update

    - **Property 13: Approval Status Update**
    - **Validates: Requirements 4.5**

  - [ ]\* 7.5 Write property test for check-in status update

    - **Property 19: Check-in Status Update**
    - **Validates: Requirements 6.2**

  - [ ]\* 7.6 Write property test for automatic maintenance ticket on damage
    - **Property 20: Automatic Maintenance Ticket on Damage**
    - **Validates: Requirements 6.3**

- [x] 8. Approval Service Implementation

  - [x] 8.1 Create ApprovalService with interface
    - Implement initiateApproval(), findApprover()
    - Implement recordDecision() with IP hash logging
    - Implement sendApprovalEmail() with signed URL
    - _Requirements: 4.1, 4.3, 4.5_

- [x] 9. Registration Service Implementation

  - [x] 9.1 Create RegistrationService with interface

    - Implement register() with @motac.gov.my validation
    - Implement validateEmailDomain(), sendVerificationEmail()
    - Implement verifyEmail(), extractUsernameFromEmail()
    - _Requirements: 15.2, 15.3, 15.4, 15.5_

  - [ ]\* 9.2 Write property test for registration account creation

    - **Property 36: Registration Account Creation**
    - **Validates: Requirements 15.3, 15.4**

  - [ ]\* 9.3 Write property test for email verification round-trip
    - **Property 37: Email Verification Round-Trip**
    - **Validates: Requirements 15.5**

- [-] 10. Account Linking Service Implementation

  - [x] 10.1 Create AccountLinkingService with interface

    - Implement findUnlinkedSubmissions() by email
    - Implement linkSubmissions() with atomic transaction
    - Implement getLinkedSubmissionCount()
    - _Requirements: 18.2, 18.4_

  - [ ]\* 10.2 Write property test for unlinked submission discovery

    - **Property 42: Unlinked Submission Discovery**
    - **Validates: Requirements 18.2**

  - [ ]\* 10.3 Write property test for account linking atomicity
    - **Property 43: Account Linking Atomicity**
    - **Validates: Requirements 18.4**

- [x] 11. Notification Preference Service Implementation

  - [x] 11.1 Create NotificationPreferenceService with interface
    - Implement getPreferences(), updatePreferences()
    - Implement shouldSendEmail(), getDigestFrequency()
    - _Requirements: 17.5_

- [-] 11A. Responsible Officer Service Implementation

  - [x] 11A.1 Create ResponsibleOfficerService with interface

    - Implement setResponsibleOfficer(), copyApplicantAsResponsibleOfficer()
    - Implement getResponsibleOfficerDetails(), isApplicantResponsible()
    - _Requirements: 25.1, 25.2, 25.3, 25.4_

  - [ ]\* 11A.2 Write property test for Responsible Officer data storage

    - **Property 75: Responsible Officer Data Storage**
    - **Validates: Requirements 25.4**

  - [ ]\* 11A.3 Write property test for Responsible Officer auto-population

    - **Property 74: Responsible Officer Auto-Population**
    - **Validates: Requirements 25.3**

- [-] 11B. Accessory Tracking Service Implementation

  - [x] 11B.1 Create AccessoryTrackingService with interface

    - Implement getStandardAccessories(), recordCheckoutAccessories()
    - Implement recordCheckinAccessories(), getAccessoryDiscrepancies()
    - Implement getAccessoriesForTransaction()
    - _Requirements: 26.1, 26.2, 26.4, 26.5, 26.6_

  - [ ]\* 11B.2 Write property test for accessory data storage

    - **Property 83: Accessory Data Storage**
    - **Validates: Requirements 26.6**

  - [ ]\* 11B.3 Write property test for accessory discrepancy highlighting

    - **Property 82: Accessory Discrepancy Highlighting**
    - **Validates: Requirements 26.5**

- [x] 12. Checkpoint - Ensure all service tests pass
  - Run `php artisan test --filter=Service`
  - Ensure all tests pass, ask the user if questions arise.

---

## Phase 4: Authentication and Authorization

- [x] 13. Self-Registration Implementation

  - [x] 13.1 Create registration form (Livewire component)

    - WCAG 2.2 AA compliant bilingual form
    - Email domain validation (@motac.gov.my)
    - Password confirmation with strength indicator
    - _Requirements: 15.1, 15.2_

  - [x] 13.2 Implement email verification flow
    - Signed URL generation (24-hour expiry)
    - Verification controller and route
    - Success/error handling with bilingual messages
    - _Requirements: 15.4, 15.5_

- [x] 14. Flexible Login Implementation

  - [x] 14.1 Customize Laravel Breeze login

    - Accept full email OR short username
    - Implement username extraction logic
    - Generic error messages (no user enumeration)
    - _Requirements: 16.2, 16.3, 16.5_

  - [ ]\* 14.2 Write property test for flexible login acceptance

    - **Property 38: Flexible Login Acceptance**
    - **Validates: Requirements 16.2, 16.3**

  - [ ]\* 14.3 Write property test for authentication error opacity
    - **Property 39: Authentication Error Opacity**
    - **Validates: Requirements 16.5**

- [x] 15. Role-Based Access Control

  - [x] 15.1 Configure Spatie Permission for roles

    - Define staff, admin, superuser roles
    - Create permissions for each module
    - _Requirements: 5.1, 6.4, 7.1_

  - [x] 15.2 Implement Laravel Telescope gate

    - Restrict access to superuser role only
    - Return 403 for non-superuser access
    - _Requirements: 20.2, 20.3_

  - [x] 15.3 Implement Laravel Pulse gate
    - Restrict `/pulse` route to admin and superuser roles
    - Return 403 for staff access
    - Configure in PulseServiceProvider
    - _Requirements: 36.6_

- [x] 16. Checkpoint - Ensure all auth tests pass
  - Run `php artisan test --filter=Auth`
  - Ensure all tests pass, ask the user if questions arise.

---

## Phase 5: Guest Portal Components (Livewire/Volt)

- [x] 17. Helpdesk Ticket Form

  - [x] 17.1 Create TicketForm Livewire component

    - Hybrid form with Auth::check() logic for auto-fill
    - Real-time validation with Livewire 3.7.0
    - File upload (max 5 files, 5MB each, PDF/JPG/PNG/DOCX)
    - PDPA acknowledgement checkbox
    - Display form reference code PK.(S).MOTAC.07.(L1) in header
    - _Requirements: 1.1, 1.2, 1.4, 1.5, 24.1_

  - [ ]\* 17.2 Write property test for real-time validation response

    - **Property 2: Real-time Validation Response**
    - **Validates: Requirements 1.2**

  - [ ]\* 17.3 Write property test for attachment validation

    - **Property 3: Attachment Validation**
    - **Validates: Requirements 1.4**

  - [ ]\* 17.4 Write property test for helpdesk form reference code display
    - **Property 68: Helpdesk Form Reference Code Display**
    - **Validates: Requirements 24.1**

- [-] 18. Status Checker Component

  - [x] 18.1 Create StatusChecker Livewire component
    - Token-based lookup for tickets and loans
    - Display status, timeline, public comments
    - Bilingual error messages for invalid tokens
    - _Requirements: 2.1, 2.2_

- [x] 19. Loan Application Wizard

  - [x] 19.1 Create LoanApplicationWizard Volt component

    - Multi-step wizard (Applicant → Responsible Officer → Assets → Dates → Purpose → Acknowledgement)
    - Hybrid form with Auth::check() logic for auto-fill
    - Real-time asset availability checking
    - Display form reference code PK.(S).MOTAC.07.(L3) in header
    - _Requirements: 3.1, 3.2, 3.4, 24.2_

  - [x] 19.2 Implement Responsible Officer section

    - Add "Applicant is same as Responsible Officer" checkbox (default: checked)
    - Conditional fields: name, position & grade, phone when unchecked
    - Auto-populate from Applicant data when checked
    - Include Responsible Officer acknowledgement statement
    - _Requirements: 25.1, 25.2, 25.3, 25.6_

  - [ ]\* 19.3 Write property test for Responsible Officer section display

    - **Property 72: Responsible Officer Section Display**
    - **Validates: Requirements 25.1**

  - [ ]\* 19.4 Write property test for Responsible Officer fields toggle

    - **Property 73: Responsible Officer Fields Toggle**
    - **Validates: Requirements 25.2**

  - [ ]\* 19.5 Write property test for Responsible Officer acknowledgement
    - **Property 77: Responsible Officer Acknowledgement**
    - **Validates: Requirements 25.6**

- [x] 20. Approval Page Component

  - [x] 20.1 Create ApprovalPage Volt component
    - Guest-accessible via signed URL
    - Display application summary
    - Approve/Reject buttons with remarks field
    - _Requirements: 4.2, 4.3_

- [ ] 21. Checkpoint - Ensure all guest portal tests pass
  - Run `php artisan test --filter=Guest`
  - Ensure all tests pass, ask the user if questions arise.

---

## Phase 6: Staff Dashboard

- [x] 22. My Dashboard Implementation

  - [x] 22.1 Create Dashboard Livewire component

    - Display submission history (tickets + loans)
    - Profile summary with edit link
    - Notification center
    - _Requirements: 17.1, 17.2_

  - [ ]\* 22.2 Write property test for submission history completeness

    - **Property 40: Submission History Completeness**
    - **Validates: Requirements 17.2**

  - [x] 22.3 Create Profile Settings component

    - Edit phone, division, grade fields
    - Notification preferences (email frequency, in-app toggle)
    - _Requirements: 17.4, 17.5_

  - [ ]\* 22.4 Write property test for profile update persistence
    - **Property 41: Profile Update Persistence**
    - **Validates: Requirements 17.4**

- [x] 23. Account Linking Feature

  - [x] 23.1 Create AccountLinking Livewire component
    - Email input for finding unlinked submissions
    - Display matching submissions for confirmation
    - Link button with success feedback
    - _Requirements: 18.1, 18.2, 18.3, 18.4, 18.5_

- [-] 24. Checkpoint - Ensure all dashboard tests pass
  - Run `php artisan test --filter=Dashboard`
  - Ensure all tests pass, ask the user if questions arise.

---

## Phase 7: Filament Admin Panel

- [x] 25. Helpdesk Ticket Resource

  - [x] 25.1 Create HelpdeskTicketResource

    - CRUD operations with filtering (status, priority, category, date)
    - Status management with required comment
    - Assignment action with WebSocket notification
    - SLA indicators and warning display
    - _Requirements: 5.1, 5.2, 5.3, 5.4, 5.5_

  - [ ]\* 25.2 Write property test for ticket filtering accuracy

    - **Property 14: Ticket Filtering Accuracy**
    - **Validates: Requirements 5.2**

  - [ ]\* 25.3 Write property test for audit trail on status update
    - **Property 15: Audit Trail on Status Update**
    - **Validates: Requirements 5.3**

- [x] 26. Loan Application Resource

  - [x] 26.1 Create LoanApplicationResource

    - CRUD operations with filtering (status, date, division)
    - Approval chain status display
    - Check-out/Check-in actions with accessory tracking
    - Damage reporting with photo upload
    - Display Applicant and Responsible Officer (when different)
    - Display form reference code PK.(S).MOTAC.07.(L3)
    - _Requirements: 6.1, 6.2, 6.3, 6.4, 25.5, 24.2_

  - [x] 26.2 Implement accessory tracking in Check-out action

    - Display accessory checklist (Power Adapter, Bag, Mouse, USB Cable, HDMI/VGA Cable, Remote, Others)
    - Allow marking each as "Included" or "Not Included" with condition notes
    - Custom accessory name field for "Others"
    - _Requirements: 26.1, 26.2, 26.3_

  - [x] 26.3 Implement accessory tracking in Check-in action

    - Pre-populate checklist from check-out data
    - Highlight discrepancies (missing items, condition changes)
    - _Requirements: 26.4, 26.5_

  - [ ]\* 26.4 Write property test for loan application filtering accuracy

    - **Property 21: Loan Application Filtering Accuracy**
    - **Validates: Requirements 6.4**

  - [ ]\* 26.5 Write property test for accessory checklist display at check-out

    - **Property 78: Accessory Checklist Display at Check-out**
    - **Validates: Requirements 26.1**

  - [ ]\* 26.6 Write property test for accessory checklist pre-population at check-in

    - **Property 81: Accessory Checklist Pre-population at Check-in**
    - **Validates: Requirements 26.4**

  - [ ]\* 26.7 Write property test for Responsible Officer display differentiation
    - **Property 76: Responsible Officer Display Differentiation**
    - **Validates: Requirements 25.5**

- [x] 27. Dashboard Widgets

  - [x] 27.1 Create HelpdeskStatsWidget

    - Open/In-Progress/Resolved counts
    - SLA compliance percentage
    - Real-time updates via Laravel Reverb
    - _Requirements: 5.1_

  - [x] 27.2 Create LoanStatsWidget

    - Pending/Active/Overdue counts
    - Real-time updates via Laravel Reverb
    - _Requirements: 6.4_

  - [x] 27.3 Create RecentActivityWidget
    - Real-time activity feed
    - WebSocket integration
    - _Requirements: 8.1_

- [ ] 28. Audit Log Viewer

  - [x] 28.1 Create unified audit log page

    - Combined view of owen-it audits and spatie activity_log
    - Filtering by date, user, action type, entity
    - Export to CSV/PDF
    - _Requirements: 7.2, 7.3_

  - [ ]\* 28.2 Write property test for audit log filtering accuracy

    - **Property 22: Audit Log Filtering Accuracy**
    - **Validates: Requirements 7.2**

  - [ ]\* 28.3 Write property test for audit export completeness
    - **Property 23: Audit Export Completeness**
    - **Validates: Requirements 7.3**

- [x] 29. Superuser Configuration

  - [x] 29.1 Create configuration management page
    - SLA thresholds editing
    - Email template management
    - Approval workflow settings
    - Token regeneration for expired approvals
    - _Requirements: 7.1, 7.4, 7.5_

- [ ] 30. Checkpoint - Ensure all Filament tests pass
  - Run `php artisan test --filter=Filament`
  - Ensure all tests pass, ask the user if questions arise.

---

## Phase 8: Notifications and Real-time

- [x] 31. Email Notifications

  - [x] 31.1 Create ticket notification emails

    - Ticket created confirmation
    - Status change notification
    - SLA breach warning
    - _Requirements: 1.7, 2.3, 8.2_

  - [ ]\* 31.2 Write property test for status change notification

    - **Property 7: Status Change Notification**
    - **Validates: Requirements 2.3**

  - [x] 31.3 Create loan notification emails

    - Application submitted confirmation
    - Approval request email with signed URL
    - Decision notification
    - Overdue reminders (48h before, on due, daily after)
    - _Requirements: 4.1, 4.5, 8.3_

  - [ ]\* 31.4 Write property test for overdue asset reminder schedule
    - **Property 26: Overdue Asset Reminder Schedule**
    - **Validates: Requirements 8.3**

- [x] 32. WebSocket Notifications (Laravel Reverb)

  - [x] 32.1 Configure Laravel Reverb channels

    - Private user channels for personal notifications
    - Admin broadcast channel for high-priority alerts
    - _Requirements: 8.1, 8.2_

  - [ ]\* 32.2 Write property test for high-priority ticket broadcast

    - **Property 24: High-Priority Ticket Broadcast**
    - **Validates: Requirements 8.1**

  - [ ]\* 32.3 Write property test for real-time assignment notification

    - **Property 16: Real-time Assignment Notification**
    - **Validates: Requirements 5.4**

  - [ ]\* 32.4 Write property test for SLA breach notification
    - **Property 25: SLA Breach Notification**
    - **Validates: Requirements 8.2**

- [x] 33. Queue Jobs

  - [x] 33.1 Create notification queue jobs

    - SendTicketNotification job
    - SendLoanNotification job
    - SendApprovalRequest job
    - ProcessNotificationDigest job
    - _Requirements: 10.4, 13.3_

  - [ ]\* 33.2 Write property test for queue processing time
    - **Property 31: Queue Processing Time**
    - **Validates: Requirements 10.4**

- [ ] 34. Checkpoint - Ensure all notification tests pass
  - Run `php artisan test --filter=Notification`
  - Ensure all tests pass, ask the user if questions arise.

---

## Phase 9: Dual Audit System

- [ ] 35. Audit Configuration

  - [ ] 35.1 Configure owen-it/laravel-auditing

    - Enable on all auditable models
    - Configure IP hashing for privacy
    - Set 7-year retention policy
    - _Requirements: 19.1, 19.3_

  - [ ]\* 35.2 Write property test for field-level audit completeness

    - **Property 44: Field-Level Audit Completeness**
    - **Validates: Requirements 19.3**

  - [ ] 35.3 Configure spatie/laravel-activitylog

    - Enable on significant user actions
    - Configure subject and causer tracking
    - _Requirements: 19.2, 19.4_

  - [ ]\* 35.4 Write property test for activity log recording
    - **Property 45: Activity Log Recording**
    - **Validates: Requirements 19.4**

- [ ] 36. Checkpoint - Ensure all audit tests pass
  - Run `php artisan test --filter=Audit`
  - Ensure all tests pass, ask the user if questions arise.

---

## Phase 10: Security and Rate Limiting

- [ ] 37. Security Implementation

  - [ ] 37.1 Implement rate limiting

    - 60 requests/minute per IP for guest forms
    - Configure in bootstrap/app.php
    - _Requirements: 14.1_

  - [ ]\* 37.2 Write property test for rate limiting enforcement

    - **Property 34: Rate Limiting Enforcement**
    - **Validates: Requirements 14.1**

  - [ ] 37.3 Implement reCAPTCHA Enterprise

    - Invisible mode on all guest forms
    - _Requirements: 14.2_

  - [ ] 37.4 Implement ClamAV file scanning

    - Scan uploads before storage
    - _Requirements: 14.3_

  - [ ] 37.5 Implement 2FA for superuser
    - TOTP authentication for superuser login
    - _Requirements: 7.4_

- [ ] 38. Checkpoint - Ensure all security tests pass
  - Run `php artisan test --filter=Security`
  - Ensure all tests pass, ask the user if questions arise.

---

## Phase 11: Accessibility and Localization

- [ ] 39. WCAG 2.2 AA Compliance

  - [ ] 39.1 Implement accessibility features

    - Color contrast 4.5:1 for text, 3:1 for UI
    - Keyboard navigation with 3px focus indicators
    - ARIA labels, landmarks, live regions
    - Touch targets minimum 44x44px
    - _Requirements: 9.1, 9.2, 9.3, 9.4_

  - [ ]\* 39.2 Write property test for color contrast compliance

    - **Property 27: Color Contrast Compliance**
    - **Validates: Requirements 9.1**

  - [ ]\* 39.3 Write property test for keyboard navigation

    - **Property 28: Keyboard Navigation**
    - **Validates: Requirements 9.2**

  - [ ]\* 39.4 Write property test for ARIA implementation

    - **Property 29: ARIA Implementation**
    - **Validates: Requirements 9.3**

  - [ ]\* 39.5 Write property test for touch target sizing
    - **Property 30: Touch Target Sizing**
    - **Validates: Requirements 9.4**

- [ ] 40. Bilingual Localization

  - [ ] 40.1 Create translation files

    - Bahasa Melayu (primary) in lang/ms/
    - English (secondary) in lang/en/
    - Consistent terminology per GLOSSARY.md
    - _Requirements: 11.1, 11.4_

  - [ ]\* 40.2 Write property test for bilingual content consistency

    - **Property 32: Bilingual Content Consistency**
    - **Validates: Requirements 11.1, 11.4**

  - [ ] 40.3 Implement language switching
    - User preference persistence
    - Session-based fallback
    - _Requirements: 11.2_

- [ ] 41. Checkpoint - Ensure all accessibility tests pass
  - Run `php artisan test --filter=Accessibility`
  - Run Lighthouse accessibility audit (target: 100)
  - Ensure all tests pass, ask the user if questions arise.

---

## Phase 12: Performance Optimization

- [ ] 42. Core Web Vitals Optimization

  - [ ] 42.1 Optimize LCP (Largest Contentful Paint)

    - Target: <2.5s for guest forms
    - Implement lazy loading, image optimization
    - _Requirements: 10.1_

  - [ ] 42.2 Optimize FID (First Input Delay)

    - Target: <100ms
    - Minimize JavaScript blocking
    - _Requirements: 10.2_

  - [ ] 42.3 Optimize CLS (Cumulative Layout Shift)

    - Target: <0.1
    - Reserve space for dynamic content
    - _Requirements: 10.3_

  - [ ] 42.4 Optimize Filament dashboard load
    - Target: <3s with caching
    - Implement widget caching
    - _Requirements: 10.5_

- [ ] 43. Checkpoint - Ensure all performance tests pass
  - Run Lighthouse performance audit
  - Verify Core Web Vitals targets met
  - Ensure all tests pass, ask the user if questions arise.

---

## Phase 13: MOTAC Branding and Visual Identity

- [ ] 44. Brand Assets Verification

  - [ ] 44.1 Verify all MOTAC brand assets exist in public/images/

    - Confirm jata-negara.svg (Malaysian Coat of Arms, vector)
    - Confirm motac-logo.png (MOTAC logo, 120x120)
    - Confirm motac-logo-32.png (notification icon, 32x32)
    - Confirm motac-logo-64.png (medium icon, 64x64)
    - Confirm bpm-logo.png (BPM division logo)
    - Confirm favicon.ico (MOTAC-branded)
    - Confirm web-app-manifest-192x192.png and web-app-manifest-512x512.png (PWA icons)
    - _Requirements: 21.1, 21.2, 21.3, 21.8_

- [ ] 45. Government Header Component

  - [ ] 45.1 Create GovHeader Blade component

    - File: resources/views/components/layout/gov-header.blade.php
    - Display Jata Negara (48x48 minimum) from jata-negara.svg
    - Display MOTAC logo (40x40) from motac-logo.png
    - Display ministry name from common.motac_full_name translation
    - Display BPM name from common.bpm_full_name translation
    - Responsive layout (hide text on mobile, show on sm+)
    - _Requirements: 21.1, 21.2, 21.9_

  - [ ]\* 45.2 Write property test for Jata Negara presence

    - **Property 47: Jata Negara Presence on Public Pages**
    - **Validates: Requirements 21.1**

  - [ ]\* 45.3 Write property test for MOTAC logo presence

    - **Property 48: MOTAC Logo Presence on Public Pages**
    - **Validates: Requirements 21.2**

- [ ] 46. Government Footer Component

  - [ ] 46.1 Create GovFooter Blade component

    - File: resources/views/components/layout/gov-footer.blade.php
    - Display Jata Negara (inverted for dark background)
    - Display ministry full name from translation
    - Display government disclaimer "Sistem Rasmi Kerajaan Malaysia"
    - Display copyright with BPM name
    - _Requirements: 21.9, 22.5_

  - [ ]\* 46.2 Write property test for footer ministry name

    - **Property 55: Footer Ministry Name**
    - **Validates: Requirements 21.9**

  - [ ]\* 46.3 Write property test for government disclaimer

    - **Property 60: Government Disclaimer Presence**
    - **Validates: Requirements 22.5**

- [ ] 47. Form Header Branding

  - [ ] 47.1 Update FormHeader Blade component

    - File: resources/views/components/form/header.blade.php
    - Display BPM logo (64x64) from bpm-logo.png
    - Use MOTAC primary blue gradient (#0056b3)
    - Accept $title and $subtitle props
    - _Requirements: 21.3, 22.1_

  - [ ]\* 47.2 Write property test for BPM logo in form headers

    - **Property 49: BPM Logo Presence in Form Headers**
    - **Validates: Requirements 21.3**

- [ ] 48. Filament Admin Panel Branding

  - [ ] 48.1 Configure Filament branding in AdminPanelProvider

    - Set brandLogo to motac-logo.png
    - Set brandLogoHeight to 2.5rem
    - Set favicon to favicon.ico
    - Configure darkModeBrandLogo
    - _Requirements: 21.4_

  - [ ]\* 48.2 Write property test for Filament admin branding

    - **Property 50: Filament Admin Panel Branding**
    - **Validates: Requirements 21.4**

- [ ] 49. Email Template Branding

  - [ ] 49.1 Customize Laravel Mail header template

    - File: resources/views/vendor/mail/html/header.blade.php
    - Add Jata Negara image (60px height)
    - Add MOTAC logo image (50px height)
    - Add ministry tagline from common.motac_tagline
    - Use MOTAC primary blue (#0056b3) for text
    - _Requirements: 21.5_

  - [ ]\* 49.2 Write property test for email template branding

    - **Property 51: Email Template Branding**
    - **Validates: Requirements 21.5**

- [ ] 50. Browser Notification Branding

  - [ ] 50.1 Update portal-echo.js notification icon

    - File: resources/js/portal-echo.js
    - Set notification icon to /images/motac-logo-32.png
    - Set notification badge to /images/motac-logo-32.png
    - _Requirements: 21.6_

  - [ ]\* 50.2 Write property test for browser notification icon

    - **Property 52: Browser Notification Icon**
    - **Validates: Requirements 21.6**

- [ ] 51. PDF Export Branding

  - [ ] 51.1 Create PDF letterhead template

    - File: resources/views/exports/pdf/letterhead.blade.php
    - Add Jata Negara (60px height)
    - Add MOTAC logo (50px height)
    - Add ministry name and BPM name
    - Use MOTAC primary blue for header border
    - _Requirements: 21.7, 22.4_

  - [ ]\* 51.2 Write property test for PDF export branding

    - **Property 53: PDF Export Branding**
    - **Validates: Requirements 21.7**

- [ ] 52. PWA Manifest Configuration

  - [ ] 52.1 Update site.webmanifest with MOTAC branding

    - File: public/site.webmanifest
    - Set name to "ICTServe - MOTAC BPM"
    - Set short_name to "ICTServe"
    - Reference web-app-manifest-192x192.png and web-app-manifest-512x512.png
    - Set theme_color to #0056b3
    - _Requirements: 21.8_

  - [ ]\* 52.2 Write property test for PWA manifest icons

    - **Property 54: PWA Manifest Icons**
    - **Validates: Requirements 21.8**

- [ ] 53. Logo Accessibility and Alt Text

  - [ ] 53.1 Verify all logo alt text uses translation keys

    - Jata Negara: common.jata_negara
    - MOTAC logo: common.motac_logo
    - BPM logo: common.bpm_logo (add if missing)
    - Verify translations exist in both lang/ms/ and lang/en/
    - _Requirements: 21.10_

  - [ ]\* 53.2 Write property test for logo alt text accessibility

    - **Property 56: Logo Alt Text Accessibility**
    - **Validates: Requirements 21.10**

- [ ] 54. Government Visual Standards Compliance

  - [ ] 54.1 Verify MOTAC color palette usage

    - Primary Blue: #0056b3
    - Verify usage in headers, buttons, links
    - Ensure WCAG contrast compliance
    - _Requirements: 22.1_

  - [ ]\* 54.2 Write property test for MOTAC primary color usage

    - **Property 57: MOTAC Primary Color Usage**
    - **Validates: Requirements 22.1**

  - [ ] 54.3 Verify logo clear space and integrity

    - Minimum 8px padding around all logos
    - No distortion, recoloring, or modification
    - _Requirements: 22.2, 22.3_

  - [ ]\* 54.4 Write property test for logo clear space

    - **Property 58: Logo Clear Space**
    - **Validates: Requirements 22.2**

  - [ ]\* 54.5 Write property test for logo integrity
    - **Property 59: Logo Integrity**
    - **Validates: Requirements 22.3**

- [ ] 55. Checkpoint - Ensure all MOTAC branding tests pass
  - Run `php artisan test --filter=Branding`
  - Verify all logos display correctly on guest forms
  - Verify email templates include MOTAC branding
  - Verify PDF exports include letterhead
  - Ensure all tests pass, ask the user if questions arise.

---

## Phase 14: MyGovEA Design Principles and Form Reference Codes

- [ ] 56. MyGovEA Design Principles Implementation

  - [ ] 56.1 Implement citizen-centric design patterns

    - Prioritize user needs in all interfaces
    - Ensure intuitive navigation and clear feedback
    - Minimize cognitive load across all forms
    - _Requirements: 23.1, 23.5_

  - [ ]\* 56.2 Write property test for citizen-centric design

    - **Property 61: Citizen-Centric Design Implementation**
    - **Validates: Requirements 23.1**

  - [ ] 56.3 Implement minimalist interface patterns

    - Remove unnecessary components
    - Maintain consistent navigation patterns
    - Ensure intuitive user flows
    - _Requirements: 23.2_

  - [ ]\* 56.4 Write property test for minimalist interface

    - **Property 62: Minimalist Interface Compliance**
    - **Validates: Requirements 23.2**

  - [ ] 56.5 Implement error prevention patterns

    - Add confirmation dialogs for destructive actions (delete, cancel, reject)
    - Provide clear undo options where applicable
    - _Requirements: 23.3_

  - [ ]\* 56.6 Write property test for error prevention dialogs

    - **Property 63: Error Prevention Confirmation Dialogs**
    - **Validates: Requirements 23.3**

  - [ ] 56.7 Implement contextual help and documentation

    - Add tooltips for complex form fields
    - Create FAQ section accessible from footer
    - Link to user manual from footer
    - _Requirements: 23.4_

  - [ ]\* 56.8 Write property test for contextual help availability
    - **Property 64: Contextual Help Availability**
    - **Validates: Requirements 23.4**

- [ ] 57. Form Reference Code Implementation

  - [ ] 57.1 Add form reference code display to helpdesk form

    - Display PK.(S).MOTAC.07.(L1) in top-right of form container
    - Match original paper form layout
    - _Requirements: 24.1, 24.5_

  - [ ] 57.2 Add form reference code display to loan form

    - Display PK.(S).MOTAC.07.(L3) in top-right of form container
    - Match original paper form layout
    - _Requirements: 24.2, 24.5_

  - [ ]\* 57.3 Write property test for loan form reference code display

    - **Property 69: Loan Form Reference Code Display**
    - **Validates: Requirements 24.2**

  - [ ]\* 57.4 Write property test for form reference code storage

    - **Property 70: Form Reference Code Storage**
    - **Validates: Requirements 24.3**

  - [ ]\* 57.5 Write property test for PDF export form reference code
    - **Property 71: PDF Export Form Reference Code**
    - **Validates: Requirements 24.4**

- [ ] 58. Checkpoint - Ensure all MyGovEA and Form Reference tests pass
  - Run `php artisan test --filter=MyGovEA`
  - Run `php artisan test --filter=FormReference`
  - Verify confirmation dialogs on destructive actions
  - Verify form reference codes display correctly
  - Ensure all tests pass, ask the user if questions arise.

---

## Phase 15: Integration Testing

- [ ] 59. End-to-End Testing

  - [ ]\* 59.1 Write E2E tests for guest helpdesk flow

    - Submit ticket → receive token → check status
    - Verify MOTAC branding visible throughout flow
    - Verify form reference code PK.(S).MOTAC.07.(L1) displayed
    - _Requirements: 1.1-1.7, 2.1-2.3, 21.1-21.3, 24.1_

  - [ ]\* 59.2 Write E2E tests for guest loan flow

    - Submit application with Responsible Officer → approval email → decision → status check
    - Verify email contains MOTAC branding
    - Verify form reference code PK.(S).MOTAC.07.(L3) displayed
    - Verify Responsible Officer section functionality
    - _Requirements: 3.1-3.6, 4.1-4.5, 21.5, 24.2, 25.1-25.6_

  - [ ]\* 59.3 Write E2E tests for staff dashboard flow

    - Register → verify email → login → view dashboard → link submissions
    - Verify government header/footer on all pages
    - _Requirements: 15.1-15.5, 16.1-16.5, 17.1-17.5, 18.1-18.5, 21.9_

  - [ ]\* 59.4 Write E2E tests for admin workflow

    - Login → manage tickets → manage loans with accessory tracking → view audit
    - Verify Filament branding with MOTAC logo
    - Verify accessory checklist at check-out and check-in
    - Verify Responsible Officer display when different from Applicant
    - _Requirements: 5.1-5.5, 6.1-6.4, 7.1-7.5, 21.4, 25.5, 26.1-26.7_

  - [ ]\* 59.5 Write E2E tests for MOTAC branding consistency

    - Navigate all public pages → verify Jata Negara and MOTAC logo present
    - Export PDF → verify letterhead branding with form reference code
    - Trigger notification → verify icon is MOTAC logo
    - _Requirements: 21.1-21.10, 22.1-22.5, 24.4_

  - [ ]\* 59.6 Write E2E tests for MyGovEA compliance
    - Verify confirmation dialogs on destructive actions
    - Verify contextual help tooltips on complex fields
    - Verify minimalist interface patterns
    - _Requirements: 23.1-23.8_

- [ ] 60. Checkpoint - Ensure all E2E tests pass
  - Run `npx playwright test`
  - Verify all user flows complete successfully
  - Ensure all tests pass, ask the user if questions arise.

---

## Phase 16: Performance Monitoring (Laravel Pulse)

- [ ] 61. Laravel Pulse Installation and Configuration

  - [ ] 61.1 Install Laravel Pulse package

    - Run `composer require laravel/pulse`
    - Publish configuration: `php artisan vendor:publish --provider="Laravel\Pulse\PulseServiceProvider"`
    - Run migrations: `php artisan migrate`
    - _Requirements: 36.1_

  - [ ] 61.2 Configure Pulse recorders

    - Enable slow query recorder (>500ms threshold)
    - Enable queue job recorder
    - Enable request recorder
    - Enable server health recorder (CPU, memory, disk)
    - Configure 7-day data retention
    - _Requirements: 36.2, 36.3, 36.4, 36.5, 36.7_

  - [ ] 61.3 Configure Pulse access control

    - Create PulseServiceProvider with Gate definition
    - Restrict `/pulse` route to admin and superuser roles
    - Return 403 for unauthorized access
    - _Requirements: 36.6_

  - [ ]\* 61.4 Write property test for Pulse access control

    - **Property 89: Pulse Access Control**
    - **Validates: Requirements 36.6**

  - [ ]\* 61.5 Write property test for slow query detection

    - **Property 85: Slow Query Detection**
    - **Validates: Requirements 36.2**

  - [ ]\* 61.6 Write property test for Pulse data retention
    - **Property 90: Pulse Data Retention**
    - **Validates: Requirements 36.7**

- [ ] 62. Performance Monitoring Service

  - [ ] 62.1 Create PerformanceMonitoringService with interface

    - Implement getSlowQueries(), getQueueJobMetrics()
    - Implement getRequestMetrics(), getServerHealthMetrics()
    - Implement checkPerformanceThresholds(), triggerPerformanceAlert()
    - Implement pruneOldData() for 7-day retention
    - _Requirements: 36.2, 36.3, 36.4, 36.5, 36.7, 36.8_

  - [ ]\* 62.2 Write property test for queue job metrics tracking

    - **Property 86: Queue Job Metrics Tracking**
    - **Validates: Requirements 36.3**

  - [ ]\* 62.3 Write property test for request pattern tracking

    - **Property 87: Request Pattern Tracking**
    - **Validates: Requirements 36.4**

  - [ ]\* 62.4 Write property test for performance alert triggering
    - **Property 91: Performance Alert Triggering**
    - **Validates: Requirements 36.8**

- [ ] 63. Dashboard Widget Integration

  - [ ] 63.1 Create PerformanceMetricsWidget for Filament dashboard

    - Display slow query count and trends
    - Display queue job success/failure rates
    - Display average response times
    - Link to full Pulse dashboard
    - _Requirements: 36.2, 36.3, 36.4_

  - [ ] 63.2 Create SystemHealthWidget for Filament dashboard

    - Display CPU usage percentage
    - Display memory consumption
    - Display disk space utilization
    - Color-coded status indicators (green/yellow/red)
    - _Requirements: 36.5_

  - [ ]\* 63.3 Write property test for server health metrics display
    - **Property 88: Server Health Metrics**
    - **Validates: Requirements 36.5**

- [ ] 64. Checkpoint - Ensure all Pulse tests pass
  - Run `php artisan test --filter=Pulse`
  - Verify Pulse dashboard accessible at `/pulse`
  - Verify access control (admin/superuser only)
  - Ensure all tests pass, ask the user if questions arise.

---

## Phase 17: API Authentication (Laravel Sanctum)

- [ ] 65. Laravel Sanctum Installation and Configuration

  - [ ] 65.1 Install and configure Laravel Sanctum

    - Run `composer require laravel/sanctum` (if not already installed)
    - Publish configuration: `php artisan vendor:publish --provider="Laravel\Sanctum\SanctumServiceProvider"`
    - Run migrations for personal_access_tokens table
    - Configure token expiration (default: 30 days)
    - _Requirements: 37.1, 37.2_

  - [ ] 65.2 Configure API rate limiting

    - Set 60 requests/minute for authenticated tokens
    - Set 10 requests/minute for unauthenticated requests
    - Configure in bootstrap/app.php
    - _Requirements: 37.4_

  - [ ]\* 65.3 Write property test for API rate limiting
    - **Property 94: API Rate Limiting**
    - **Validates: Requirements 37.4**

- [ ] 66. API Token Service

  - [ ] 66.1 Create ApiTokenService with interface

    - Implement createToken() with abilities and expiration
    - Implement revokeToken(), revokeAllTokens()
    - Implement getActiveTokens(), validateTokenAbilities()
    - Implement logTokenUsage() for audit trail
    - _Requirements: 37.1, 37.2, 37.3, 37.5_

  - [ ]\* 66.2 Write property test for API token generation

    - **Property 92: API Token Generation**
    - **Validates: Requirements 37.1, 37.2**

  - [ ]\* 66.3 Write property test for token abilities enforcement

    - **Property 93: Token Abilities Enforcement**
    - **Validates: Requirements 37.3**

  - [ ]\* 66.4 Write property test for API authentication audit logging
    - **Property 95: API Authentication Audit Logging**
    - **Validates: Requirements 37.5**

- [ ] 67. API Routes and Controllers

  - [ ] 67.1 Create API routes in routes/api.php

    - Define ticket endpoints (GET /tickets, POST /tickets)
    - Define loan endpoints (GET /loans, POST /loans)
    - Apply auth:sanctum middleware
    - Apply ability middleware for fine-grained permissions
    - _Requirements: 37.3_

  - [ ] 67.2 Create ApiTicketController

    - Implement index() with read:tickets ability
    - Implement store() with write:tickets ability
    - Return consistent JSON responses with bilingual messages
    - _Requirements: 37.3_

  - [ ] 67.3 Create ApiLoanController

    - Implement index() with read:loans ability
    - Implement store() with write:loans ability
    - Return consistent JSON responses with bilingual messages
    - _Requirements: 37.3_

- [ ] 68. API Token Management (Filament)

  - [ ] 68.1 Create ApiTokenResource for Filament

    - Token creation form with abilities selection
    - Token list with usage statistics
    - Token revocation action
    - Restrict to admin and superuser roles
    - _Requirements: 37.1, 37.2, 37.3_

- [ ] 69. API Token Usage Logging

  - [ ] 69.1 Create api_token_usage_logs migration

    - Fields: personal_access_token_id, user_id, action, endpoint, ip_hash, user_agent, response_status, created_at
    - Add indexes for user_id and created_at
    - _Requirements: 37.5_

  - [ ] 69.2 Create ApiTokenUsageLog model
    - Add relationships to PersonalAccessToken and User
    - Implement Auditable trait
    - _Requirements: 37.5_

- [ ] 70. Checkpoint - Ensure all API tests pass
  - Run `php artisan test --filter=Api`
  - Test token creation and revocation
  - Test API endpoints with valid/invalid tokens
  - Verify rate limiting enforcement
  - Ensure all tests pass, ask the user if questions arise.

---

## Phase 18: Google Workspace SSO (Optional)

- [ ] 71. Laravel Socialite Installation and Configuration

  - [ ] 71.1 Install Laravel Socialite

    - Run `composer require laravel/socialite`
    - Configure Google OAuth in config/services.php
    - Add GOOGLE_CLIENT_ID, GOOGLE_CLIENT_SECRET, GOOGLE_REDIRECT_URI to .env.example
    - _Requirements: 38.1_

  - [ ] 71.2 Configure Google OAuth credentials
    - Document Google Cloud Console setup steps
    - Configure authorized redirect URIs
    - Set up OAuth consent screen
    - _Requirements: 38.1_

- [ ] 72. Google SSO Service

  - [ ] 72.1 Create GoogleSsoService with interface

    - Implement redirectToGoogle(), handleGoogleCallback()
    - Implement validateGoogleDomain() for @motac.gov.my restriction
    - Implement findOrCreateUser() for auto-account creation
    - Implement linkGoogleAccount(), unlinkGoogleAccount()
    - _Requirements: 38.2, 38.3, 38.4_

  - [ ]\* 72.2 Write property test for Google domain restriction

    - **Property 96: Google Domain Restriction**
    - **Validates: Requirements 38.2**

  - [ ]\* 72.3 Write property test for auto-account creation

    - **Property 97: Auto-Account Creation for New Google Users**
    - **Validates: Requirements 38.3**

  - [ ]\* 72.4 Write property test for existing account linking

    - **Property 98: Existing Account Google Linking**
    - **Validates: Requirements 38.4**

  - [ ]\* 72.5 Write property test for Google OAuth audit logging
    - **Property 99: Google OAuth Audit Logging**
    - **Validates: Requirements 38.6**

- [ ] 73. Google SSO Controller and Routes

  - [ ] 73.1 Create GoogleSsoController

    - Implement redirect() method for OAuth initiation
    - Implement callback() method for OAuth callback handling
    - Handle domain validation errors gracefully
    - Log all OAuth events to audit trail
    - _Requirements: 38.2, 38.3, 38.4, 38.6_

  - [ ] 73.2 Create Google SSO routes

    - GET /auth/google/redirect → GoogleSsoController@redirect
    - GET /auth/google/callback → GoogleSsoController@callback
    - _Requirements: 38.1_

  - [ ]\* 73.3 Write property test for Google OAuth fallback
    - **Property 100: Google OAuth Fallback**
    - **Validates: Requirements 38.7**

- [ ] 74. Google Login UI Component

  - [ ] 74.1 Create Google login button Blade component

    - File: resources/views/components/auth/google-login-button.blade.php
    - Display "Sign in with Google" button with Google logo
    - WCAG 2.2 AA compliant styling
    - Bilingual text support
    - _Requirements: 38.5_

  - [ ] 74.2 Update login page to include Google button
    - Add Google login button below traditional login form
    - Add visual separator ("or")
    - _Requirements: 38.5_

- [ ] 75. User Model Updates for Google OAuth

  - [ ] 75.1 Add Google OAuth fields to users migration

    - Add google_id (VARCHAR 255, nullable, unique)
    - Add google_token (TEXT, nullable, encrypted)
    - Add google_refresh_token (TEXT, nullable, encrypted)
    - _Requirements: 38.3, 38.4_

  - [ ] 75.2 Update User model with Google OAuth attributes
    - Add google_id, google_token, google_refresh_token attributes
    - Add isGoogleLinked() method
    - Add encrypted casts for tokens
    - _Requirements: 38.3, 38.4_

- [ ] 76. Checkpoint - Ensure all Google SSO tests pass
  - Run `php artisan test --filter=GoogleSso`
  - Test domain restriction (@motac.gov.my only)
  - Test auto-account creation for new users
  - Test account linking for existing users
  - Test fallback on OAuth failure
  - Ensure all tests pass, ask the user if questions arise.

---

## Phase 19: Final Integration Testing

- [ ] 77. Extended E2E Testing

  - [ ]\* 77.1 Write E2E tests for Laravel Pulse integration

    - Login as admin → access /pulse → verify dashboard loads
    - Login as staff → access /pulse → verify 403 Forbidden
    - Trigger slow query → verify appears in Pulse
    - _Requirements: 36.1-36.8_

  - [ ]\* 77.2 Write E2E tests for API authentication

    - Create API token → use token to access endpoints → verify success
    - Use token without required ability → verify 403 Forbidden
    - Exceed rate limit → verify 429 Too Many Requests
    - _Requirements: 37.1-37.5_

  - [ ]\* 77.3 Write E2E tests for Google SSO (if enabled)
    - Click Google login → complete OAuth → verify account created/linked
    - Attempt Google login with non-MOTAC email → verify rejection
    - Simulate OAuth failure → verify fallback to traditional login
    - _Requirements: 38.1-38.7_

- [ ] 78. Final Checkpoint - Ensure all tests pass
  - Run full test suite: `php artisan test`
  - Run Playwright E2E tests: `npx playwright test`
  - Run Lighthouse audits (accessibility: 100, performance: 90+)
  - Verify MOTAC branding on all pages visually
  - Verify form reference codes on all forms
  - Verify Responsible Officer workflow
  - Verify accessory tracking at check-out/check-in
  - Verify Laravel Pulse dashboard accessible
  - Verify API authentication working
  - Verify Google SSO (if enabled)
  - Ensure all tests pass, ask the user if questions arise.

---

## Summary

**Total Tasks:** 78 main tasks with 150+ sub-tasks
**Property Tests:** 100 correctness properties covered
**Requirements Coverage:** All 38 requirements with acceptance criteria
**Checkpoints:** 20 validation checkpoints
**Phases:** 19 implementation phases

**New Requirements Added (v3.5.0 Update):**

- **Requirement 23**: MyGovEA Design Principles Compliance (citizen-centric, minimalist, error prevention, help, cognitive, hierarchy, user control)
- **Requirement 24**: Official Form Reference Codes (PK.(S).MOTAC.07.(L1) for helpdesk, PK.(S).MOTAC.07.(L3) for loans)
- **Requirement 25**: Responsible Officer Workflow (separate Applicant and Responsible Officer designation)
- **Requirement 26**: Asset Accessory Tracking (Power Adapter, Bag, Mouse, USB Cable, HDMI/VGA Cable, Remote, Others)
- **Requirement 36**: Application Performance Monitoring (Laravel Pulse) - Real-time performance dashboards
- **Requirement 37**: API Authentication (Laravel Sanctum) - Token-based API access for future integrations
- **Requirement 38**: Google Workspace SSO (Optional) - OAuth 2.0 authentication for @motac.gov.my users

**New Database Tables:**

- `loan_transaction_accessories` - Accessory tracking for check-out/check-in
- `personal_access_tokens` - Laravel Sanctum API tokens
- `pulse_entries` - Laravel Pulse performance data
- `pulse_values` - Laravel Pulse aggregated values
- `api_token_usage_logs` - API token usage audit trail

**New Database Fields:**

- `helpdesk_tickets.form_reference_code` - Official form code
- `loan_applications.form_reference_code` - Official form code
- `loan_applications.is_applicant_responsible` - Applicant = Responsible Officer flag
- `loan_applications.responsible_officer_name` - Responsible Officer name
- `loan_applications.responsible_officer_grade` - Responsible Officer grade
- `loan_applications.responsible_officer_phone` - Responsible Officer phone
- `loan_applications.responsible_officer_acknowledgement` - Acknowledgement flag
- `users.google_id` - Google OAuth ID (optional)
- `users.google_token` - Google OAuth access token (optional)
- `users.google_refresh_token` - Google OAuth refresh token (optional)

**New Packages Required:**

- `laravel/pulse` v1.3.0 - Performance monitoring
- `laravel/sanctum` v4.0 - API token authentication
- `laravel/socialite` v5.x - Google OAuth SSO (optional)

**MOTAC Branding Assets Required:**

- `public/images/jata-negara.svg` - Malaysian Coat of Arms (vector)
- `public/images/motac-logo.png` - MOTAC logo (120x120)
- `public/images/motac-logo-32.png` - Notification icon (32x32)
- `public/images/motac-logo-64.png` - Medium icon (64x64)
- `public/images/bpm-logo.png` - BPM division logo
- `public/images/favicon.ico` - Browser favicon
- `public/images/web-app-manifest-192x192.png` - PWA icon
- `public/images/web-app-manifest-512x512.png` - PWA icon

**Testing Framework:**

- PHPUnit 11.5.44 for unit and feature tests
- Faker for property-based test data generation
- Playwright for E2E browser testing
- Lighthouse for accessibility and performance audits

**New Implementation Phases (16-19):**

- **Phase 16**: Performance Monitoring (Laravel Pulse) - Tasks 61-64
- **Phase 17**: API Authentication (Laravel Sanctum) - Tasks 65-70
- **Phase 18**: Google Workspace SSO (Optional) - Tasks 71-76
- **Phase 19**: Final Integration Testing - Tasks 77-78
