# Implementation Plan

## ICTServe Update v3 - True Hybrid Architecture v3.5.0

This implementation plan converts the feature design into discrete, actionable coding tasks. Each task builds incrementally on previous tasks, ensuring no orphaned code.

**Reference Documents:**

- `.kiro/specs/ictserve-update-v3/requirements.md` - 20 requirements with acceptance criteria
- `.kiro/specs/ictserve-update-v3/design.md` - Architecture, services, 46 correctness properties
- `docs/D00-D17` - System documentation suite

**Testing Framework:** PHPUnit 11.5.44 with Faker for property-based testing

---

## Phase 1: Database Schema and Migrations

- [ ] 1. Database Schema and Migrations

  - [ ] 1.1 Create users table migration with True Hybrid fields

    - Add role enum (staff, admin, superuser), locale, notification_preferences JSON, two_factor_secret
    - Add email_verified_at for self-registration flow
    - Add staff_number, division_code, grade fields
    - _Requirements: 15.3, 16.1, 17.4, 19.1_

  - [ ]\* 1.2 Write property test for user role validation

    - **Property 46: Telescope Role Restriction**
    - **Validates: Requirements 20.2, 20.3**

  - [ ] 1.3 Update helpdesk_tickets migration with user_id nullable FK

    - Add user_id (nullable FK to users, ON DELETE SET NULL)
    - Add index on user_id for My Dashboard queries
    - Add status_token_hash (VARCHAR 128, indexed)
    - Retain all submitter\_\* columns for guest fallback
    - _Requirements: 1.5, 17.2, 18.2_

  - [ ] 1.4 Update loan_applications migration with user_id nullable FK

    - Add user_id (nullable FK to users, ON DELETE SET NULL)
    - Add index on user_id for My Dashboard queries
    - Add approval_token_hash, status_token_hash (VARCHAR 128, indexed)
    - Add approval_token_expires_at timestamp
    - Retain all applicant\_\* columns for guest fallback
    - _Requirements: 3.5, 17.2, 18.2_

  - [ ] 1.5 Create loan_approvals table migration

    - Fields: loan_application_id (FK), approver_email, approver_grade, decision (enum), remarks, decision_at, decision_ip_hash, token_hash
    - _Requirements: 4.3_

  - [ ] 1.6 Create loan_transactions table migration

    - Fields: loan_application_id (FK), asset_id (FK), transaction_type (enum: CHECK_OUT, CHECK_IN), admin_id (FK), condition_notes, damage_reported, damage_photos (JSON), transaction_at
    - _Requirements: 6.1, 6.2_

  - [ ] 1.7 Verify dual audit tables exist (audits, activity_log)
    - Ensure owen-it/laravel-auditing and spatie/laravel-activitylog migrations are run
    - _Requirements: 19.1, 19.2_

- [ ] 2. Checkpoint - Ensure all migrations pass
  - Run `php artisan migrate` and verify schema
  - Ensure all tests pass, ask the user if questions arise.

---

## Phase 2: Core Models and Relationships

- [ ] 3. Core Models and Relationships

  - [ ] 3.1 Update User model with True Hybrid fields

    - Add role accessor/mutator, notification_preferences cast, locale attribute
    - Add relationships: helpdeskTickets(), loanApplications(), assignedTickets()
    - Implement Auditable trait (owen-it) and LogsActivity trait (spatie)
    - Add extractUsernameFromEmail() method for flexible login
    - _Requirements: 15.3, 17.4, 19.1, 19.2_

  - [ ]\* 3.2 Write property test for email domain validation

    - **Property 35: Email Domain Validation**
    - **Validates: Requirements 15.2**

  - [ ] 3.3 Update HelpdeskTicket model with hybrid data association

    - Add user() relationship (nullable belongsTo)
    - Add status_token_hash attribute with SHA-512 hashing
    - Implement Auditable and LogsActivity traits
    - Add scopes: forUser(), byStatusToken(), bySLA()
    - Add ticket_number generation (HD-YYYYMM-XXXX format)
    - _Requirements: 1.5, 2.1, 17.2_

  - [ ]\* 3.4 Write property test for ticket number format

    - **Property 1: Ticket Number Format Consistency**
    - **Validates: Requirements 1.3**

  - [ ]\* 3.5 Write property test for status token round-trip

    - **Property 5: Status Token Lookup Round-Trip**
    - **Validates: Requirements 2.1**

  - [ ] 3.6 Update LoanApplication model with hybrid data association

    - Add user() relationship (nullable belongsTo)
    - Add approval_token_hash, status_token_hash attributes
    - Implement Auditable and LogsActivity traits
    - Add scopes: forUser(), byApprovalToken(), byStatusToken()
    - Add reference generation (LA-YYYYMM-XXXX format)
    - _Requirements: 3.5, 4.1, 17.2_

  - [ ]\* 3.7 Write property test for loan reference format

    - **Property 8: Loan Reference Format Consistency**
    - **Validates: Requirements 3.3**

  - [ ] 3.8 Create LoanApproval model

    - Add loanApplication() relationship
    - Add decision enum cast, token_hash attribute
    - Implement Auditable and LogsActivity traits
    - _Requirements: 4.3_

  - [ ]\* 3.9 Write property test for approval decision recording

    - **Property 12: Approval Decision Recording**
    - **Validates: Requirements 4.3**

  - [ ] 3.10 Create LoanTransaction model

    - Add loanApplication(), asset(), admin() relationships
    - Add transaction_type enum cast, damage_photos JSON cast
    - Implement Auditable and LogsActivity traits
    - _Requirements: 6.1, 6.2_

  - [ ]\* 3.11 Write property test for check-out transaction recording
    - **Property 18: Check-out Transaction Recording**
    - **Validates: Requirements 6.1**

- [ ] 4. Checkpoint - Ensure all model tests pass
  - Run `php artisan test --filter=Model`
  - Ensure all tests pass, ask the user if questions arise.

---

## Phase 3: Service Layer Implementation

- [ ] 5. Token Service Implementation

  - [ ] 5.1 Create TokenService with interface

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

- [ ] 6. Helpdesk Service Implementation

  - [ ] 6.1 Create HelpdeskService with interface

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

- [ ] 7. Loan Service Implementation

  - [ ] 7.1 Create LoanService with interface

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

- [ ] 8. Approval Service Implementation

  - [ ] 8.1 Create ApprovalService with interface
    - Implement initiateApproval(), findApprover()
    - Implement recordDecision() with IP hash logging
    - Implement sendApprovalEmail() with signed URL
    - _Requirements: 4.1, 4.3, 4.5_

- [ ] 9. Registration Service Implementation

  - [ ] 9.1 Create RegistrationService with interface

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

- [ ] 10. Account Linking Service Implementation

  - [ ] 10.1 Create AccountLinkingService with interface

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

- [ ] 11. Notification Preference Service Implementation

  - [ ] 11.1 Create NotificationPreferenceService with interface
    - Implement getPreferences(), updatePreferences()
    - Implement shouldSendEmail(), getDigestFrequency()
    - _Requirements: 17.5_

- [ ] 12. Checkpoint - Ensure all service tests pass
  - Run `php artisan test --filter=Service`
  - Ensure all tests pass, ask the user if questions arise.

---

## Phase 4: Authentication and Authorization

- [ ] 13. Self-Registration Implementation

  - [ ] 13.1 Create registration form (Livewire component)

    - WCAG 2.2 AA compliant bilingual form
    - Email domain validation (@motac.gov.my)
    - Password confirmation with strength indicator
    - _Requirements: 15.1, 15.2_

  - [ ] 13.2 Implement email verification flow
    - Signed URL generation (24-hour expiry)
    - Verification controller and route
    - Success/error handling with bilingual messages
    - _Requirements: 15.4, 15.5_

- [ ] 14. Flexible Login Implementation

  - [ ] 14.1 Customize Laravel Breeze login

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

- [ ] 15. Role-Based Access Control

  - [ ] 15.1 Configure Spatie Permission for roles

    - Define staff, admin, superuser roles
    - Create permissions for each module
    - _Requirements: 5.1, 6.4, 7.1_

  - [ ] 15.2 Implement Laravel Telescope gate
    - Restrict access to superuser role only
    - Return 403 for non-superuser access
    - _Requirements: 20.2, 20.3_

- [ ] 16. Checkpoint - Ensure all auth tests pass
  - Run `php artisan test --filter=Auth`
  - Ensure all tests pass, ask the user if questions arise.

---

## Phase 5: Guest Portal Components (Livewire/Volt)

- [ ] 17. Helpdesk Ticket Form

  - [ ] 17.1 Create TicketForm Livewire component

    - Hybrid form with Auth::check() logic for auto-fill
    - Real-time validation with Livewire 3.7.0
    - File upload (max 5 files, 5MB each, PDF/JPG/PNG/DOCX)
    - PDPA acknowledgement checkbox
    - _Requirements: 1.1, 1.2, 1.4, 1.5_

  - [ ]\* 17.2 Write property test for real-time validation response

    - **Property 2: Real-time Validation Response**
    - **Validates: Requirements 1.2**

  - [ ]\* 17.3 Write property test for attachment validation
    - **Property 3: Attachment Validation**
    - **Validates: Requirements 1.4**

- [ ] 18. Status Checker Component

  - [ ] 18.1 Create StatusChecker Livewire component
    - Token-based lookup for tickets and loans
    - Display status, timeline, public comments
    - Bilingual error messages for invalid tokens
    - _Requirements: 2.1, 2.2_

- [ ] 19. Loan Application Wizard

  - [ ] 19.1 Create LoanApplicationWizard Volt component
    - Multi-step wizard (Applicant → Assets → Dates → Purpose → Acknowledgement)
    - Hybrid form with Auth::check() logic for auto-fill
    - Real-time asset availability checking
    - _Requirements: 3.1, 3.2, 3.4_

- [ ] 20. Approval Page Component

  - [ ] 20.1 Create ApprovalPage Volt component
    - Guest-accessible via signed URL
    - Display application summary
    - Approve/Reject buttons with remarks field
    - _Requirements: 4.2, 4.3_

- [ ] 21. Checkpoint - Ensure all guest portal tests pass
  - Run `php artisan test --filter=Guest`
  - Ensure all tests pass, ask the user if questions arise.

---

## Phase 6: Staff Dashboard

- [ ] 22. My Dashboard Implementation

  - [ ] 22.1 Create Dashboard Livewire component

    - Display submission history (tickets + loans)
    - Profile summary with edit link
    - Notification center
    - _Requirements: 17.1, 17.2_

  - [ ]\* 22.2 Write property test for submission history completeness

    - **Property 40: Submission History Completeness**
    - **Validates: Requirements 17.2**

  - [ ] 22.3 Create Profile Settings component

    - Edit phone, division, grade fields
    - Notification preferences (email frequency, in-app toggle)
    - _Requirements: 17.4, 17.5_

  - [ ]\* 22.4 Write property test for profile update persistence
    - **Property 41: Profile Update Persistence**
    - **Validates: Requirements 17.4**

- [ ] 23. Account Linking Feature

  - [ ] 23.1 Create AccountLinking Livewire component
    - Email input for finding unlinked submissions
    - Display matching submissions for confirmation
    - Link button with success feedback
    - _Requirements: 18.1, 18.2, 18.3, 18.4, 18.5_

- [ ] 24. Checkpoint - Ensure all dashboard tests pass
  - Run `php artisan test --filter=Dashboard`
  - Ensure all tests pass, ask the user if questions arise.

---

## Phase 7: Filament Admin Panel

- [ ] 25. Helpdesk Ticket Resource

  - [ ] 25.1 Create HelpdeskTicketResource

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

- [ ] 26. Loan Application Resource

  - [ ] 26.1 Create LoanApplicationResource

    - CRUD operations with filtering (status, date, division)
    - Approval chain status display
    - Check-out/Check-in actions
    - Damage reporting with photo upload
    - _Requirements: 6.1, 6.2, 6.3, 6.4_

  - [ ]\* 26.2 Write property test for loan application filtering accuracy
    - **Property 21: Loan Application Filtering Accuracy**
    - **Validates: Requirements 6.4**

- [ ] 27. Dashboard Widgets

  - [ ] 27.1 Create HelpdeskStatsWidget

    - Open/In-Progress/Resolved counts
    - SLA compliance percentage
    - Real-time updates via Laravel Reverb
    - _Requirements: 5.1_

  - [ ] 27.2 Create LoanStatsWidget

    - Pending/Active/Overdue counts
    - Real-time updates via Laravel Reverb
    - _Requirements: 6.4_

  - [ ] 27.3 Create RecentActivityWidget
    - Real-time activity feed
    - WebSocket integration
    - _Requirements: 8.1_

- [ ] 28. Audit Log Viewer

  - [ ] 28.1 Create unified audit log page

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

- [ ] 29. Superuser Configuration

  - [ ] 29.1 Create configuration management page
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

- [ ] 31. Email Notifications

  - [ ] 31.1 Create ticket notification emails

    - Ticket created confirmation
    - Status change notification
    - SLA breach warning
    - _Requirements: 1.7, 2.3, 8.2_

  - [ ]\* 31.2 Write property test for status change notification

    - **Property 7: Status Change Notification**
    - **Validates: Requirements 2.3**

  - [ ] 31.3 Create loan notification emails

    - Application submitted confirmation
    - Approval request email with signed URL
    - Decision notification
    - Overdue reminders (48h before, on due, daily after)
    - _Requirements: 4.1, 4.5, 8.3_

  - [ ]\* 31.4 Write property test for overdue asset reminder schedule
    - **Property 26: Overdue Asset Reminder Schedule**
    - **Validates: Requirements 8.3**

- [ ] 32. WebSocket Notifications (Laravel Reverb)

  - [ ] 32.1 Configure Laravel Reverb channels

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

- [ ] 33. Queue Jobs

  - [ ] 33.1 Create notification queue jobs

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

## Phase 13: Integration Testing

- [ ] 44. End-to-End Testing

  - [ ]\* 44.1 Write E2E tests for guest helpdesk flow

    - Submit ticket → receive token → check status
    - _Requirements: 1.1-1.7, 2.1-2.3_

  - [ ]\* 44.2 Write E2E tests for guest loan flow

    - Submit application → approval email → decision → status check
    - _Requirements: 3.1-3.6, 4.1-4.5_

  - [ ]\* 44.3 Write E2E tests for staff dashboard flow

    - Register → verify email → login → view dashboard → link submissions
    - _Requirements: 15.1-15.5, 16.1-16.5, 17.1-17.5, 18.1-18.5_

  - [ ]\* 44.4 Write E2E tests for admin workflow
    - Login → manage tickets → manage loans → view audit
    - _Requirements: 5.1-5.5, 6.1-6.4, 7.1-7.5_

- [ ] 45. Final Checkpoint - Ensure all tests pass
  - Run full test suite: `php artisan test`
  - Run Playwright E2E tests: `npx playwright test`
  - Run Lighthouse audits (accessibility: 100, performance: 90+)
  - Ensure all tests pass, ask the user if questions arise.

---

## Summary

**Total Tasks:** 45 main tasks with 77 sub-tasks
**Property Tests:** 46 correctness properties covered
**Requirements Coverage:** All 20 requirements with acceptance criteria
**Checkpoints:** 13 validation checkpoints

**Testing Framework:**

- PHPUnit 11.5.44 for unit and feature tests
- Faker for property-based test data generation
- Playwright for E2E browser testing
- Lighthouse for accessibility and performance audits
