# Implementation Plan

## Overview

This implementation plan bridges the gap between the current ICTServe codebase and the PKS v4.0 compliant design. Tasks are organized by priority with PKS compliance migration as the critical first phase.

**Current State Analysis**:

- Guest mode functionality still exists in models, services, and tests (needs removal per PKS 5.2.1)
- HrmisIntegrationService exists but has Larastan errors (needs fixes)
- DLP filtering service does not exist (needs creation per PKS 9.2.1)
- LDAP/AD SSO not implemented (needs implementation per PKS 5.2.1)
- Laravel Pulse, Telescope, Horizon, Reverb are configured and working
- Asset management lifecycle features are implemented

---

## Phase 0: PKS Compliance Migration (v4.0) - CRITICAL PRIORITY

- [ ] 0.1 PKS 5.2.1 Accountability Migration - Remove Guest Mode

  - [ ] 0.1.1 Remove guest fields from HelpdeskTicket model
    - Remove guest_name, guest_email, guest_phone, guest_staff_id, guest_grade, guest_division columns
    - Remove isGuestSubmission() method
    - Update getSubmitterName(), getSubmitterEmail(), getSubmitterIdentifier() to use user relationship only
    - Update $fillable array to remove guest fields
    - _Requirements: 1.1, 1.2, 3.1, 8.1, 25.1_
    - **Files**: `app/Models/HelpdeskTicket.php`

  - [ ] 0.1.2 Remove guest fields from LoanApplication model
    - Remove applicant_name, applicant_email, applicant_phone columns
    - Remove isGuestSubmission() method
    - Update getApplicantName(), getApplicantEmail() to use user relationship only
    - Update $fillable array to remove guest fields
    - _Requirements: 1.4, 1.5, 3.1, 9.1, 25.1_
    - **Files**: `app/Models/LoanApplication.php`

  - [ ] 0.1.3 Create database migration for PKS compliance
    - Create migration to drop guest columns from helpdesk_tickets
    - Create migration to drop guest columns from loan_applications
    - Update user_id columns to NOT NULL with foreign key constraints
    - Add hrmis_synced_at, ldap_guid, is_active columns to users table
    - _Requirements: 1.1, 2.1, 2.2, 3.1, 25.1_
    - **Files**: `database/migrations/2025_12_25_000001_pks_compliance_remove_guest_mode.php`

  - [ ] 0.1.4 Remove guest-related Livewire components
    - Delete `app/Livewire/GuestLoanApplication.php`
    - Delete `app/Livewire/GuestLoanTracking.php`
    - Update `app/Livewire/AuthenticatedDashboard.php` - remove guest claiming functionality
    - _Requirements: 1.1, 1.4, 8.1, 9.1, 25.1_

  - [ ] 0.1.5 Remove guest-related services
    - Delete `app/Services/GuestSubmissionClaimService.php`
    - Delete `app/Services/AccountLinkingService.php`
    - Delete `app/Contracts/AccountLinkingServiceInterface.php` (if exists)
    - Update `app/Services/HybridHelpdeskService.php` - remove guest submission logic
    - Update `app/Services/LoanApplicationService.php` - remove claimGuestApplication method
    - _Requirements: 1.1, 3.1, 25.1_

  - [ ] 0.1.6 Update policies for SSO-only access
    - Update `app/Policies/HelpdeskTicketPolicy.php` - remove guest email matching logic
    - Update `app/Policies/LoanApplicationPolicy.php` - remove guest email matching and claim method
    - _Requirements: 1.1, 3.1, 12.3, 25.1_

  - [ ] 0.1.7 Update observers for SSO-only
    - Update `app/Observers/HelpdeskTicketObserver.php` - remove guest notification handling
    - Update `app/Observers/HelpdeskCommentObserver.php` - remove guest notification handling
    - Update `app/Observers/LoanApplicationObserver.php` - remove guest submitter logging
    - _Requirements: 3.1, 6.5, 25.1_

  - [ ] 0.1.8 Update notifications for SSO-only
    - Update `app/Notifications/HelpdeskTicketCreated.php` - remove guest submission type
    - Update `app/Notifications/HelpdeskTicketStatusUpdated.php` - remove guest channel logic
    - Update `app/Notifications/TicketStatusUpdatedNotification.php` - remove guest tracking link
    - Update `app/Notifications/TicketCommentAddedNotification.php` - remove guest tracking link
    - _Requirements: 6.3, 6.5, 25.1_

  - [ ] 0.1.9 Update Filament resources
    - Update `app/Filament/Resources/HelpdeskTicketResource.php` - remove guest field columns/filters
    - Update `app/Filament/Resources/LoanApplicationResource.php` - remove guest field columns/filters
    - Update `app/Filament/Widgets/RecentTicketsTable.php` - remove isGuestSubmission() usage
    - _Requirements: 4.1, 8.1, 9.1, 25.1_

  - [ ] 0.1.10 Update seeders and factories
    - Update `database/seeders/HelpdeskTicketSeeder.php` - remove guest data, use authenticated users
    - Update `database/factories/HelpdeskTicketFactory.php` - remove guest fields
    - Update `database/factories/LoanApplicationFactory.php` - remove guest fields
    - _Requirements: 3.1, 25.1_

- [ ] 0.2 PKS 5.2.1 - Implement LDAP/Active Directory SSO

  - [ ] 0.2.1 Install and configure LdapRecord-Laravel
    - Install LdapRecord-Laravel package via Composer
    - Publish LDAP configuration files
    - Configure LDAP connection to MOTAC Active Directory in config/ldap.php
    - _Requirements: 1.1, 2.3, 5.2, 27.1_

  - [ ] 0.2.2 Update User model for LDAP authentication
    - Add LdapAuthenticatable interface and AuthenticatesWithLdap trait
    - Add ldap_guid, hrmis_synced_at, is_active columns handling
    - Implement isLDAPAuthenticated() method
    - _Requirements: 1.1, 2.1, 2.3, 5.2_
    - **Files**: `app/Models/User.php`

  - [ ] 0.2.3 Configure LDAP authentication provider
    - Update config/auth.php for LDAP provider
    - Configure LDAP user provider with attribute mapping
    - Set up LDAP password policy enforcement (8 chars, 90-day expiry, 3 attempts)
    - _Requirements: 2.3, 5.2, 27.1, 27.2, 27.3_

  - [ ] 0.2.4 Remove alternative authentication methods
    - Delete `app/Services/GoogleSsoService.php`
    - Delete `app/Services/RegistrationService.php` (replace with HRMIS)
    - Remove Google SSO routes from routes/web.php
    - Remove self-registration routes
    - _Requirements: 1.1, 5.2, 25.1_

- [ ] 0.3 PKS 5.2.1 - HRMIS Auto-Provisioning

  - [ ] 0.3.1 Fix HrmisIntegrationService Larastan errors
    - Fix property type declarations for $baseUrl, $apiKey, $timeout, $cacheMinutes
    - Add proper return type annotations for all methods
    - Fix mixed type access issues
    - _Requirements: 2.1, 2.2_
    - **Files**: `app/Services/HrmisIntegrationService.php`

  - [ ] 0.3.2 Create HRMIS synchronization scheduled job
    - Create HrmisSyncJob for daily user synchronization
    - Register job in Console/Kernel.php or routes/console.php
    - Implement user creation for new employees
    - Implement user deactivation for terminated employees
    - _Requirements: 2.2, 2.4, 2.5_

  - [ ] 0.3.3 Implement HRMIS verification for approvers
    - Add HRMIS verification check before processing approvals
    - Update DualApprovalService to verify approver identity via HRMIS
    - Add hrmis_verified_at timestamp to loan_applications
    - _Requirements: 1.6, 2.4, 9.2_

- [ ] 0.4 PKS 9.2.1 - Data Transfer and DLP Compliance

  - [ ] 0.4.1 Create DLP Filtering Service
    - Create `app/Services/DlpFilteringService.php`
    - Implement PII detection rules (IC numbers, phone numbers, emails)
    - Implement government classified data detection
    - Create data classification engine (SENSITIVE vs PUBLIC)
    - _Requirements: 25.1, 25.2, 25.3_

  - [ ] 0.4.2 Update ModelRouter with DLP integration
    - Modify `app/Services/ModelRouter.php` to pass all requests through DLP filter
    - Block sensitive data from AWS Bedrock transmission
    - Route sensitive queries to local Ollama only
    - _Requirements: 25.1, 25.2, 25.5_

  - [ ] 0.4.3 Implement DLP audit logging
    - Create DLP audit log table migration
    - Log all DLP filter decisions with user_id, data classification, routing decision
    - Add DLP bypass audit alerts for superuser review
    - _Requirements: 25.4, 25.6_

- [ ] 0.5 PKS 4.2 - Data Sovereignty Compliance

  - [ ] 0.5.1 Configure Ollama for sensitive data processing
    - Update OllamaClient to be primary processor for sensitive data
    - Implement data residency logging for AI operations
    - Create compliance dashboard widget for data sovereignty monitoring
    - _Requirements: 26.2, 26.4_

- [ ] 0.6 PKS 5.4.3 - Password Policy Compliance

  - [ ] 0.6.1 Configure Active Directory password policy
    - Verify LDAP password policy enforcement (8 chars, 90-day expiry)
    - Implement 3 failed attempts lockout with 30-minute unlock
    - Add password policy display in Bahasa Melayu
    - Log all authentication attempts for security monitoring
    - _Requirements: 27.1, 27.2, 27.3, 27.4, 27.5_

---

## Phase 1: WebSocket Channel Updates (PKS 5.2.1 Authenticated Channels)

- [ ] 1.1 Update WebSocket channels for authenticated-only access

  - [ ] 1.1.1 Update routes/channels.php
    - Remove guest UUID channels (private-ticket.{uuid}, private-loan.{uuid})
    - Update submission channels to authenticated-only (ticket.{userId}.{ticketId})
    - Ensure all channels require authenticated user_id
    - _Requirements: 6.4, 6.5, 24.5, 24.6, 25.1_
    - **Files**: `routes/channels.php`

  - [ ] 1.1.2 Update broadcast events
    - Update `app/Events/CommentPosted.php` for authenticated-only channels
    - Update `app/Events/StatusUpdated.php` for authenticated-only channels
    - Remove `app/Events/Concerns/BroadcastsToHybridChannels.php` guest channel logic
    - _Requirements: 6.4, 6.5, 25.1_

  - [ ] 1.1.3 Update JavaScript bootstrap
    - Update `resources/js/bootstrap.js` - remove guest channel subscriptions
    - Ensure all Echo channel subscriptions require authentication
    - _Requirements: 6.2, 6.4, 25.1_

---

## Phase 2: Test Suite Updates (PKS Compliance)

- [ ] 2.1 Remove guest-related tests

  - [ ] 2.1.1 Delete guest test files
    - Delete `tests/Feature/AccountLinkingServiceTest.php`
    - Delete `tests/Feature/GuestLoanApplicationWorkflowTest.php`
    - Delete `tests/Feature/GuestLoanTrackingTest.php`
    - Delete `tests/Feature/Livewire/GuestLoanApplicationTest.php` (if exists)
    - Delete `tests/Unit/Services/GuestSubmissionClaimServiceTest.php` (if exists)
    - Delete `tests/Unit/GoogleAuthControllerUnitTest.php`
    - _Requirements: 18.1, 25.1_

  - [ ] 2.1.2 Update existing tests for SSO-only
    - Update `tests/Feature/HybridHelpdeskWorkflowTest.php` - remove guest tests
    - Update `tests/Feature/LoanAuthenticatedFormTest.php` - ensure SSO-only
    - Update `tests/Feature/HelpdeskAuthenticatedFormTest.php` - ensure SSO-only
    - Update `tests/Unit/Models/HelpdeskTicketHybridTest.php` (if exists) - remove guest tests
    - _Requirements: 18.1, 18.3, 25.1_

- [ ] 2.2 Create PKS compliance tests

  - [ ] 2.2.1 Create mandatory user_id tests
    - Test that helpdesk_tickets cannot be created without user_id
    - Test that loan_applications cannot be created without user_id
    - Test that all audit logs have mandatory user_id
    - _Requirements: 3.1, 25.1_

  - [ ] 2.2.2 Create DLP filtering tests
    - Test PII detection (IC numbers, phone numbers, emails)
    - Test data classification (SENSITIVE vs PUBLIC)
    - Test Bedrock blocking for sensitive data
    - Test Ollama routing for sensitive queries
    - _Requirements: 25.1, 25.2, 25.3_

  - [ ] 2.2.3 Create LDAP authentication tests
    - Test LDAP authentication flow
    - Test password policy enforcement
    - Test failed login lockout
    - _Requirements: 2.3, 5.2, 27.1, 27.2, 27.3_

---

## Phase 3: Cloud Hybrid AI with DLP (PKS 9.2.1)

- [ ] 3.1 AI Chatbot PKS Compliance Updates

  - [ ] 3.1.1 Update BedrockChat component for SSO-only
    - Update `app/Livewire/BedrockChat.php` - require authenticated user
    - Ensure all conversations linked to mandatory user_id
    - Remove any guest AI access
    - _Requirements: 19.3, 19.6, 25.1_

  - [ ] 3.1.2 Update BedrockConversation model
    - Ensure user_id is NOT NULL
    - Update factory and seeder for authenticated users only
    - _Requirements: 19.6, 25.1_

  - [ ] 3.1.3 Update AI services for DLP compliance
    - Update `app/Services/BedrockService.php` - integrate DLP filtering
    - Update `app/Services/RagService.php` - ensure local processing for sensitive FAQ
    - Update `app/Services/DocumentService.php` - DLP check before cloud processing
    - _Requirements: 25.1, 25.2, 26.2_

- [ ] 3.2 Auto-Reply and Document Analysis

  - [ ] 3.2.1 Update AutoReplyService for PKS compliance
    - Ensure all auto-replies linked to authenticated user_id
    - Integrate DLP filtering for auto-reply content
    - _Requirements: 20.1, 25.1_

  - [ ] 3.2.2 Update DocumentService for DLP
    - Route sensitive documents to Ollama only
    - Block sensitive document content from Bedrock
    - Add DLP audit logging for document processing
    - _Requirements: 20.2, 20.3, 25.1, 25.2, 26.2_

---

## Phase 4: PKS Extended Compliance (NEW)

- [ ] 4.1 PKS CSIRT Integration (Requirement 28)

  - [ ] 4.1.1 Create SecurityIncidentService
    - Implement automated threat detection
    - Create detection rules for unauthorized access, data breaches, anomalies
    - Configure real-time alerting with severity classification
    - _Requirements: 28.1, 28.5_

  - [ ] 4.1.2 Build CSIRT escalation workflows
    - Create automated escalation to CSIRT MOTAC within 15 minutes
    - Implement incident notification channels
    - Build incident response tracking
    - _Requirements: 28.4_

  - [ ] 4.1.3 Implement NACSA/MyCERT reporting
    - Create incident report generator compatible with NACSA format
    - Implement MyCERT reporting interface
    - Build automated report submission workflows
    - _Requirements: 28.2_

  - [ ] 4.1.4 Build incident management dashboard
    - Create Filament admin interface for incident management
    - Implement incident timeline visualization
    - Maintain 7-year incident log retention
    - _Requirements: 28.3_

- [ ] 4.2 PKS Business Continuity and Disaster Recovery (Requirement 29)

  - [ ] 4.2.1 Implement automated backup procedures
    - Configure automated daily backups with RTO 4 hours, RPO 24 hours
    - Implement incremental and full backup strategies
    - Create backup verification and integrity checks
    - _Requirements: 29.1_

  - [ ] 4.2.2 Configure disaster recovery site
    - Set up data replication to secondary location
    - Implement database replication with MySQL master-slave
    - Configure Redis cluster replication
    - _Requirements: 29.2_

  - [ ] 4.2.3 Build automated failover mechanisms
    - Implement health monitoring for critical components
    - Create automated failover triggers
    - Build failover testing procedures
    - _Requirements: 29.3, 29.4_

  - [ ] 4.2.4 Create DRP documentation
    - Document complete DRP procedures in Bahasa Melayu
    - Implement annual DRP testing schedule
    - Create DRP test result templates
    - _Requirements: 29.5_

- [ ] 4.3 PKS Security Training Compliance (Requirement 30)

  - [ ] 4.3.1 Implement training tracking system
    - Add security_training_completed_at, training_expiry_date to users table
    - Create SecurityTrainingRecord model
    - Implement training status verification middleware
    - _Requirements: 30.1_

  - [ ] 4.3.2 Build automated training reminders
    - Create scheduled job for training expiry notifications
    - Implement email and in-app reminders
    - Add training deadline escalation
    - _Requirements: 30.2_

  - [ ] 4.3.3 Implement training-based access restrictions
    - Create middleware to restrict sensitive features for non-compliant users
    - Implement graceful access denial with training redirect
    - _Requirements: 30.3_

  - [ ] 4.3.4 Build training compliance reporting
    - Create training compliance reports by division and role
    - Implement completion rate analytics
    - Add export functionality for compliance audits
    - _Requirements: 30.4, 30.5_

- [ ] 4.4 PKS Change Management Compliance (Requirement 31)

  - [ ] 4.4.1 Implement change request workflows
    - Create ChangeRequest model with approval workflow
    - Implement change request submission in Filament
    - Build approval routing based on change type
    - _Requirements: 31.1_

  - [ ] 4.4.2 Build change logging and audit trails
    - Create comprehensive change logs
    - Implement rollback procedure documentation
    - Add change impact assessment tracking
    - _Requirements: 31.2_

  - [ ] 4.4.3 Implement automated rollback capabilities
    - Create database migration rollback procedures
    - Implement configuration change rollback
    - Build rollback testing and verification
    - _Requirements: 31.3_

  - [ ] 4.4.4 Build risk assessment documentation
    - Create risk assessment templates
    - Implement mandatory risk documentation for sensitive changes
    - Add risk approval workflow
    - _Requirements: 31.4, 31.5_

- [ ] 4.5 PKS Third-Party Security Management (Requirement 32)

  - [ ] 4.5.1 Implement time-limited third-party access
    - Add is_third_party, contract_end_date, nda_acknowledged_at to users
    - Create ThirdPartyAccessToken model with automatic expiration
    - Implement automatic access termination on contract expiry
    - _Requirements: 32.1, 32.4_

  - [ ] 4.5.2 Build NDA acknowledgment workflow
    - Create NDA acknowledgment interface
    - Implement NDA version tracking
    - Add NDA compliance verification middleware
    - _Requirements: 32.2_

  - [ ] 4.5.3 Implement enhanced third-party audit logging
    - Create separate audit trail for third-party activities
    - Implement enhanced logging detail for vendor actions
    - Add suspicious activity alerting
    - _Requirements: 32.3_

  - [ ] 4.5.4 Build third-party access reporting
    - Create third-party access reports
    - Implement contract expiry notifications
    - Add third-party access analytics
    - _Requirements: 32.5_

---

## Phase 5: PSPM Strategic Alignment (Requirement 33)

- [ ] 5.1 PSPM Teras Aplikasi alignment
  - Ensure end-to-end digital workflows for all ICTServe services
  - Implement mobile-responsive design for all interfaces
  - Add digital service delivery metrics and KPIs
  - _Requirements: 33.1_

- [ ] 5.2 PSPM Teras Data capabilities
  - Implement management dashboards with KPI tracking
  - Create data analytics capabilities for decision support
  - Add data governance and quality monitoring
  - _Requirements: 33.2_

- [ ] 5.3 PSPM Teras Infrastruktur ICT compliance
  - Verify cloud-ready architecture and scalable design
  - Implement infrastructure monitoring and optimization
  - Add capacity planning and resource management
  - _Requirements: 33.3_

- [ ] 5.4 PSPM Teras Tadbir Urus & Keupayaan
  - Create comprehensive user documentation in Bahasa Melayu
  - Implement user-friendly interfaces with accessibility compliance
  - Add digital capability building resources
  - _Requirements: 33.4_

- [ ] 5.5 National digital transformation alignment
  - Ensure MyDIGITAL standards compliance
  - Implement PSPSA interoperability requirements
  - Add national digital transformation metrics
  - _Requirements: 33.5_

---

## Phase 6: Documentation Updates (PKS v4.0)

- [ ] 6.1 Update system documentation

  - [ ] 6.1.1 Update D00-D17 documentation
    - Update all documentation to reflect v4.0 PKS compliance changes
    - Remove references to guest mode functionality
    - Add LDAP SSO documentation
    - _Requirements: 7.4, 17.1_

  - [ ] 6.1.2 Create D18 PKS Compliance Documentation
    - Document DLP filtering requirements and implementation
    - Document LDAP SSO configuration
    - Document HRMIS auto-provisioning
    - Document CSIRT integration procedures
    - _Requirements: 17.2, 17.3, 25.1_

---

## Phase 7: Final Validation (PKS v4.0)

- [ ] 7.1 Execute comprehensive test suite
  - Run complete PHPUnit test suite (target: 80%+ coverage)
  - Execute all Livewire and Volt component tests (SSO-only)
  - Run Filament admin panel tests
  - Validate all tests pass with zero failures
  - _Requirements: 18.1, 18.2, 18.3, 19.1, 25.1_

- [ ] 7.2 Validate performance and accessibility
  - Execute Core Web Vitals testing (LCP <2.5s, FID <100ms, CLS <0.1, TTFB <600ms)
  - Run WCAG 2.2 AA accessibility audit
  - Validate response time requirements
  - _Requirements: 11.1, 11.2, 11.3, 11.4, 11.5, 19.2_

- [ ] 7.3 Confirm security and audit trail
  - Verify LDAP/AD SSO authentication for all roles
  - Test data encryption at rest (AES-256) and in transit (TLS 1.3)
  - Validate dual audit system with mandatory user_id
  - Validate DLP filtering for cloud AI per PKS 9.2.1
  - Validate data sovereignty compliance per PKS 4.2
  - _Requirements: 3.1, 3.2, 12.1, 12.2, 25.1, 26.1_

- [ ] 7.4 Verify integration points
  - Test email workflow delivery and tracking
  - Validate WebSocket real-time updates (authenticated channels only)
  - Verify cross-module integration
  - Test API endpoints with Laravel Sanctum
  - Validate LDAP/AD SSO integration
  - _Requirements: 5.1, 5.2, 6.1, 6.2, 10.1, 10.2, 25.1_

- [ ] 7.5 Complete UAT and training
  - Conduct UAT sessions with representative users
  - Document and resolve all UAT findings
  - Deliver user training materials in Bahasa Melayu
  - PKS compliance training for administrators
  - _Requirements: 7.1, 7.2, 7.3, 7.4, 19.5, 25.1_

- [ ] 7.6 Execute go-live procedures
  - Deploy to production environment
  - Configure production monitoring (Laravel Pulse)
  - Enable production error tracking (Laravel Telescope)
  - Activate automated backup procedures
  - PKS compliance monitoring activation
  - _Requirements: 14.1, 14.2, 15.1, 15.2, 15.3, 25.1_

---

## Completed Tasks (Previously Implemented)

- [x] 5.1 Create enhanced Filament asset inventory management system
  - Enhanced AssetResource with real-time availability tracking ✅
  - Enhanced AssetCategoryResource with usage analytics ✅
  - Enhanced asset specification and condition tracking ✅
  - _Requirements: 9.3, 10.2, 14.1, 14.2_

- [x] 5.6 Build enhanced asset transaction management
  - Enhanced asset check-out/check-in processes ✅
  - Enhanced overdue asset tracking ✅
  - Enhanced damage reporting with maintenance ticket creation ✅
  - _Requirements: 9.4, 10.2, 14.1, 14.2_

- [x] 7.1 Create enhanced WCAG 2.2 AA compliant component library
  - Enhanced unified Blade component library ✅
  - Enhanced responsive design ✅
  - Enhanced MOTAC branding with compliant colors ✅
  - _Requirements: 7.1, 7.2, 7.4, 11.1, 11.2, 11.3, 11.4, 11.5_

- [x] 7.2 Implement enhanced Livewire/Volt architecture
  - Enhanced Laravel localization with Bahasa Melayu ✅
  - Enhanced Livewire 3.7.x components ✅
  - Enhanced Volt single-file components ✅
  - _Requirements: 7.1, 7.2, 7.3, 7.4, 15.1, 15.2, 15.3, 15.4_

- [x] 8.1 Implement Laravel Pulse performance monitoring system
  - Configure Laravel Pulse v1.4.6 with comprehensive metrics ✅
  - Set up real-time performance dashboards ✅
  - Implement automated alerting for performance thresholds ✅
  - Create custom ICTServe-specific Pulse recorders ✅
  - _Requirements: 4.1, 4.2, 14.1, 14.2, 14.5, 16.1, 16.2, 16.3, 16.4, 16.5_

- [x] 8.2 Implement Laravel Telescope system debugging
  - Configure Laravel Telescope v5.x with superuser-only access ✅
  - Set up comprehensive request monitoring ✅
  - Implement error tracking and debugging ✅
  - Create custom debugging tools for ICTServe operations ✅
  - _Requirements: 4.2, 12.1, 14.1, 17.1, 17.2, 17.3, 17.4, 17.5_

- [x] 8.3 Build enhanced security and API integration
  - Implement Laravel Sanctum v4.x for API authentication ✅
  - Configure Google Workspace SSO (to be replaced with LDAP) ✅
  - Set up security monitoring ✅
  - Implement API rate limiting ✅
  - _Requirements: 5.1, 5.2, 5.3, 5.4, 12.1, 12.2, 12.4, 12.5_

- [x] 8.5 Implement Laravel Horizon queue management system
  - Install and configure Laravel Horizon v5.x ✅
  - Configure queue supervisors for ICTServe job types ✅
  - Implement job balancing strategies ✅
  - Set up real-time metrics and alerting ✅
  - Configure job retry policies ✅
  - Implement job tagging for ICTServe operations ✅
  - Create Horizon production deployment configuration ✅
  - _Requirements: 23.1, 23.2, 23.3, 23.4, 23.5, 23.6, 23.7, 23.8_

- [x] 14.1 Implement preventive maintenance scheduling
  - Create maintenance schedule configuration ✅
  - Implement automated reminder notifications ✅
  - Build maintenance history tracking ✅
  - _Requirements: 21.1_

- [x] 14.2 Build corrective maintenance tracking
  - Create troubleshooting log functionality ✅
  - Implement parts tracking and repair history ✅
  - Link maintenance tickets to asset records ✅
  - _Requirements: 21.2_

- [x] 14.3 Implement asset transfer workflows
  - Create transfer order documentation ✅
  - Implement approval workflow with signatures ✅
  - Add handover certificate generation ✅
  - Track custodian assignments ✅
  - _Requirements: 21.3, 21.4_

- [x] 14.4 Build asset utilization analytics
  - Create predictive maintenance insights ✅
  - Implement replacement planning recommendations ✅
  - Add utilization reporting dashboard ✅
  - _Requirements: 21.5_

---

## Implementation Status Summary

| Phase | Status | Priority | Key Changes |
|-------|--------|----------|-------------|
| Phase 0: PKS Migration | ⬜ NOT STARTED | CRITICAL | Guest mode removal, LDAP SSO, HRMIS, DLP |
| Phase 1: WebSocket Updates | ⬜ NOT STARTED | HIGH | Authenticated channels only |
| Phase 2: Test Suite Updates | ⬜ NOT STARTED | HIGH | Remove guest tests, add PKS tests |
| Phase 3: AI DLP Compliance | ⬜ NOT STARTED | HIGH | DLP filtering for cloud AI |
| Phase 4: PKS Extended | ⬜ NOT STARTED | MEDIUM | CSIRT, BCP/DRP, Training, Change Mgmt |
| Phase 5: PSPM Alignment | ⬜ NOT STARTED | MEDIUM | Strategic alignment |
| Phase 6: Documentation | ⬜ NOT STARTED | LOW | Update D00-D18 |
| Phase 7: Final Validation | ⬜ NOT STARTED | HIGH | PKS compliance validation |

---

## Files Requiring Updates for PKS v4.0

### Models (Guest Field Removal)

- `app/Models/HelpdeskTicket.php` - Remove guest fields, isGuestSubmission()
- `app/Models/LoanApplication.php` - Remove guest fields, isGuestSubmission()
- `app/Models/User.php` - Add LDAP fields, remove Google SSO fields

### Services to Remove

- `app/Services/GuestSubmissionClaimService.php`
- `app/Services/AccountLinkingService.php`
- `app/Services/GoogleSsoService.php`
- `app/Services/RegistrationService.php`

### Services to Create

- `app/Services/DlpFilteringService.php` - PKS 9.2.1
- `app/Services/LdapAuthenticationService.php` - PKS 5.2.1

### Services to Update

- `app/Services/HrmisIntegrationService.php` - Fix Larastan errors
- `app/Services/HybridHelpdeskService.php` - Remove guest logic
- `app/Services/LoanApplicationService.php` - Remove guest claiming
- `app/Services/ModelRouter.php` - Add DLP integration
- `app/Services/BedrockService.php` - Add DLP filtering
- `app/Services/DocumentService.php` - Add DLP filtering

### Livewire Components to Remove

- `app/Livewire/GuestLoanApplication.php`
- `app/Livewire/GuestLoanTracking.php`

### Tests to Remove

- `tests/Feature/AccountLinkingServiceTest.php`
- `tests/Feature/GuestLoanApplicationWorkflowTest.php`
- `tests/Feature/GuestLoanTrackingTest.php`
- `tests/Unit/GoogleAuthControllerUnitTest.php`

### WebSocket Files to Update

- `routes/channels.php` - Remove guest UUID channels
- `app/Events/Concerns/BroadcastsToHybridChannels.php` - Remove guest logic
- `resources/js/bootstrap.js` - Remove guest channel subscriptions

---

## PKS Compliance Requirements Summary

| PKS Section | Requirement | Implementation |
|-------------|-------------|----------------|
| PKS 5.2.1 | Accountability | Mandatory user_id FK (NOT NULL), no guest access |
| PKS 9.2.1 | Data Transfer | DLP filtering before cloud AI |
| PKS 4.2 | Data Sovereignty | Sensitive data via Ollama only |
| PKS 5.4.3 | Password Policy | LDAP/AD (8 chars, 90-day, 3 attempts) |
| PKS CSIRT | Incident Response | 15-minute escalation, NACSA/MyCERT reporting |
| PKS BCP/DRP | Business Continuity | RTO 4 hours, RPO 24 hours |
| PKS Training | Security Awareness | Mandatory tracking, access restrictions |
| PKS Change Mgmt | Change Control | Approval workflows, rollback procedures |
| PKS Third-Party | Vendor Security | Time-limited access, NDA, enhanced audit |
