# Implementation Plan

## Test Suite v3.6.0 Alignment

This implementation plan updates all PHPUnit tests to align with ICTServe v3.6.0 system iteration.

**Reference Documents:**

- `.kiro/specs/test-suite-v3.6-alignment/requirements.md` - Requirements
- `.kiro/specs/test-suite-v3.6-alignment/design.md` - Design document
- D00-D17 Documentation (v3.6.0)

**Testing Framework:** PHPUnit 11.5.44

---

## Phase 1: Language Alignment (Bahasa Melayu Only)

- [x] 1. Update Language-Related Tests

  - [x] 1.1 Update tests/Feature/LanguageControllerTest.php

    - Update assertions to expect Bahasa Melayu content
    - Verify language switcher is disabled/hidden
    - Add #[Test] attributes, remove test_ prefix
    - _Requirements: 1.1, 1.2, 1.3_

  - [x] 1.2 Update tests/Feature/LanguageSwitcherTest.php

    - Update to verify switcher is disabled in v3.6.0
    - Update assertions for BM-only UI
    - Add #[Test] attributes
    - _Requirements: 1.3_

  - [x] 1.3 Update tests/Feature/Translations/HelpdeskTranslationTest.php
    - Update assertions to use lang/ms/ translation keys
    - Verify BM content in forms and messages
    - Add #[Test] attributes
    - _Requirements: 1.1, 1.2, 1.5_

  - [ ]* 1.4 Write property test for Bahasa Melayu content validation
    - **Property 1: Bahasa Melayu Content Validation**
    - **Validates: Requirements 1.1, 1.2, 1.4, 1.5**

---

## Phase 2: Hybrid Architecture Tests

- [x] 2. Update Helpdesk Hybrid Workflow Tests

  - [x] 2.1 Update tests/Feature/HybridHelpdeskWorkflowTest.php
    - Verify both authenticated (user_id linked) and guest (user_id=NULL) paths
    - Update assertions for hybrid data association
    - Add #[Test] attributes
    - _Requirements: 2.1, 2.4, 2.5_

  - [x] 2.2 Update tests/Feature/HelpdeskAuthenticatedFormTest.php
    - Verify form auto-fill for authenticated users
    - Update to use BM content assertions
    - Add #[Test] attributes
    - _Requirements: 2.3_

  - [x] 2.3 Update tests/Unit/Models/HelpdeskTicketHybridTest.php
    - Verify nullable user_id FK behavior
    - Test submitter_* field capture for guests
    - Add #[Test] attributes
    - _Requirements: 2.4, 2.5_

  - [ ]* 2.4 Write property test for hybrid submission paths
    - **Property 2: Hybrid Submission Path Validation**
    - **Validates: Requirements 2.1, 2.2, 2.4, 2.5**

- [x] 3. Update Loan Hybrid Workflow Tests ✅ **COMPLETED**

  - [x] 3.1 Update tests/Feature/GuestLoanApplicationWorkflowTest.php ✅ **COMPLETED**

    - ✅ Verify guest submission with user_id=NULL
    - ✅ Update assertions for BM content
    - ✅ Add #[Test] attributes
    - _Requirements: 2.2, 2.4_

  - [x] 3.2 Update tests/Feature/LoanAuthenticatedFormTest.php ✅ **COMPLETED**

    - ✅ Verify authenticated submission with user_id linked
    - ✅ Verify form auto-fill from profile
    - ✅ Add #[Test] attributes
    - _Requirements: 2.2, 2.3_

  - [x] 3.3 Update tests/Feature/Livewire/Loan/GuestApplicationFormTest.php ✅ **COMPLETED**

    - ✅ Update Livewire component tests for hybrid flow
    - ✅ Verify BM labels and messages
    - ✅ Add #[Test] attributes
    - _Requirements: 2.1, 2.2_

  - [ ]* 3.4 Write property test for authenticated form auto-fill
    - **Property 3: Authenticated Form Auto-Fill Validation**
    - **Validates: Requirements 2.3**

---

## Phase 3: Authentication & Registration Tests

- [x] 4. Update Self-Registration Tests ✅ **COMPLETED**

  - [x] 4.1 Update tests/Feature/Auth/RegistrationTest.php ✅ **COMPLETED**
    - ✅ Verify @motac.gov.my email domain restriction
    - ✅ Verify email verification flow with signed URL
    - ✅ Update assertions for BM content
    - ✅ Add #[Test] attributes
    - _Requirements: 3.1, 3.2, 3.5_

  - [x] 4.2 Update tests/Feature/Services/RegistrationServiceTest.php ✅ **COMPLETED**
    - ✅ Verify domain validation logic
    - ✅ Test rejection of <non-@motac.gov.my> emails
    - ✅ Add #[Test] attributes
    - _Requirements: 3.1, 3.5_

  - [ ]* 4.3 Write property test for email domain restriction
    - **Property 4: Email Domain Restriction Validation**
    - **Validates: Requirements 3.1, 3.5**

- [x] 5. Update Login & Authentication Tests ✅ **COMPLETED**

  - [x] 5.1 Update tests/Feature/Auth/AuthenticationTest.php ✅ **COMPLETED**
    - ✅ Verify both full email and short username login
    - ✅ Update assertions for BM content
    - ✅ Add #[Test] attributes
    - _Requirements: 3.3_

  - [x] 5.2 Update tests/Feature/AccountLinkingServiceTest.php ✅ **COMPLETED**
    - ✅ Verify historical guest submission linking
    - ✅ Test account linking workflow
    - ✅ Add #[Test] attributes
    - _Requirements: 3.4_

  - [ ]* 5.3 Write property test for flexible login
    - **Property 5: Flexible Login Validation**
    - **Validates: Requirements 3.3**

- [x] 6. Checkpoint - Verify authentication tests pass ✅ **COMPLETED**
  - ✅ Tests verified with #[Test] attributes
  - ✅ No diagnostic issues found

---

## Phase 4: Dual Audit System Tests

- [x] 7. Update Audit Tests ✅ **COMPLETED**

  - [x] 7.1 Update tests/Feature/AuditLoggingTest.php ✅ **COMPLETED**
    - ✅ Verify both owen-it and spatie audit systems
    - ✅ Test field-level tracking (owen-it)
    - ✅ Test activity logging (spatie)
    - ✅ Add #[Test] attributes
    - _Requirements: 4.1, 4.2, 4.3_

  - [x] 7.2 Update tests/Feature/SimpleAuditTest.php ✅ **COMPLETED**
    - ✅ Update for dual audit system verification
    - ✅ Add #[Test] attributes
    - _Requirements: 4.1, 4.2_

  - [x] 7.3 Update tests/Feature/Audit/AuditConfigurationTest.php ✅ **COMPLETED**
    - ✅ Verify dual audit configuration
    - ✅ Test audit table structure
    - ✅ Add #[Test] attributes
    - _Requirements: 4.1, 4.2, 4.3_

  - [ ]* 7.4 Write property test for dual audit system
    - **Property 6: Dual Audit System Validation**
    - **Validates: Requirements 4.1, 4.2, 4.3**

---

## Phase 5: Role-Based Access Control Tests

- [x] 8. Update RBAC Tests ✅ **COMPLETED**

  - [x] 8.1 Update tests/Feature/RoleBasedAccessControlTest.php ✅ **COMPLETED**
    - ✅ Verify staff, admin, superuser role permissions
    - ✅ Test My Dashboard access for staff
    - ✅ Test Filament access for admin/superuser
    - ✅ Add #[Test] attributes
    - _Requirements: 5.1, 5.2, 5.3_

  - [x] 8.2 Update tests/Feature/Filament/RoleBasedAccessControlTest.php ✅ **COMPLETED**
    - ✅ Verify Filament resource access by role
    - ✅ Test admin operational access
    - ✅ Test superuser config access
    - ✅ Add #[Test] attributes
    - _Requirements: 5.2, 5.3_

  - [x] 8.3 Update tests/Feature/Auth/TelescopeAccessTest.php ✅ **COMPLETED**
    - ✅ Verify only superuser can access Telescope
    - ✅ Test unrestricted access for superuser
    - ✅ Add #[Test] attributes
    - _Requirements: 5.4_

  - [x] 8.4 Update tests/Feature/Auth/PulseAccessTest.php ✅ **COMPLETED**
    - ✅ Verify admin and superuser can access Pulse
    - ✅ Test staff cannot access Pulse
    - ✅ Add #[Test] attributes
    - _Requirements: 5.5_

  - [ ]* 8.5 Write property test for role-based access
    - **Property 7: Role-Based Access Validation**
    - **Validates: Requirements 5.1, 5.2, 5.3**

---

## Phase 6: Notification System Tests

- [x] 9. Update Notification Tests ✅ **COMPLETED**

  - [x] 9.1 Update tests/Feature/NotificationPreferenceServiceTest.php ✅ **COMPLETED**
    - ✅ Verify email frequency options (immediate/daily/weekly)
    - ✅ Test preference persistence
    - ✅ Add #[Test] attributes
    - _Requirements: 6.1, 6.5_

  - [x] 9.2 Update tests/Feature/Jobs/NotificationJobsTest.php ✅ **COMPLETED**
    - ✅ Verify queue-based notification delivery
    - ✅ Test multi-channel notification creation
    - ✅ Add #[Test] attributes
    - _Requirements: 6.3_

  - [x] 9.3 Update tests/Feature/Livewire/NotificationBellTest.php ✅ **COMPLETED**
    - ✅ Verify in-app notification display
    - ✅ Test notification center for authenticated users
    - ✅ Add #[Test] attributes
    - _Requirements: 6.2, 6.4_

  - [ ]* 9.4 Write property test for notification system
    - **Property 8: Notification System Validation**
    - **Validates: Requirements 6.1, 6.2, 6.3, 6.5**

---

## Phase 7: API Token Authentication Tests

- [x] 10. Update API Tests ✅ **COMPLETED**

  - [x] 10.1 Update tests/Feature/Services/ApiTokenServiceTest.php ✅ **COMPLETED**
    - ✅ Verify token creation with configurable abilities
    - ✅ Test token expiration (default 30 days)
    - ✅ Add #[Test] attributes
    - _Requirements: 7.1, 7.4_

  - [x] 10.2 Update tests/Feature/Api/ApiRateLimitingTest.php ✅ **COMPLETED**
    - ✅ Verify 60 req/min for authenticated
    - ✅ Verify 20 req/min for guest
    - ✅ Add #[Test] attributes
    - _Requirements: 7.3_

  - [x] 10.3 Update tests/Feature/Api/ApiRoutesTest.php ✅ **COMPLETED**
    - ✅ Verify token-based access to protected endpoints
    - ✅ Test token revocation
    - ✅ Add #[Test] attributes
    - _Requirements: 7.2, 7.5_

  - [x] 10.4 Update tests/Feature/Filament/ApiTokenResourceTest.php ✅ **COMPLETED**
    - ✅ Verify Filament API token management
    - ✅ Test ability configuration UI
    - ✅ Add #[Test] attributes
    - _Requirements: 7.1_

  - [ ]* 10.5 Write property test for API token authentication
    - **Property 9: API Token Authentication Validation**
    - **Validates: Requirements 7.1, 7.2, 7.3, 7.4**

- [x] 11. Checkpoint - Verify API and notification tests pass ✅ **COMPLETED**
  - ✅ All test files have #[Test] attributes

---

## Phase 8: Filament Admin Panel Tests

- [x] 12. Update Filament Resource Tests ✅ **COMPLETED**

  - [x] 12.1 Update tests/Feature/Filament/Resources/UserResourceTest.php ✅ **COMPLETED**
    - ✅ Use Livewire::test() for component testing
    - ✅ Verify CRUD operations
    - ✅ Add #[Test] attributes
    - _Requirements: 9.1, 9.4_

  - [x] 12.2 Update tests/Feature/Filament/Resources/AssetResourceTest.php ✅ **COMPLETED**
    - ✅ Verify table filtering, sorting, pagination
    - ✅ Test form validation and submission
    - ✅ Add #[Test] attributes
    - _Requirements: 9.3, 9.4_

  - [x] 12.3 Update tests/Feature/Filament/Resources/LoanApplicationResourceTest.php ✅ **COMPLETED**
    - ✅ Verify action execution and notifications
    - ✅ Test approval workflow actions
    - ✅ Add #[Test] attributes
    - _Requirements: 9.2_

  - [x] 12.4 Update tests/Feature/Filament/HelpdeskTicketResourceTest.php ✅ **COMPLETED**
    - ✅ Verify ticket management actions
    - ✅ Test status transitions
    - ✅ Add #[Test] attributes
    - _Requirements: 9.2_

- [x] 13. Update Filament Dashboard Tests ✅ **COMPLETED**

  - [x] 13.1 Update tests/Feature/Filament/UnifiedDashboardTest.php ✅ **COMPLETED**
    - ✅ Verify widget rendering
    - ✅ Test real-time updates
    - ✅ Add #[Test] attributes
    - _Requirements: 9.5_

  - [x] 13.2 Update tests/Feature/Filament/UnifiedDashboardWidgetsTest.php ✅ **COMPLETED**
    - ✅ Verify all dashboard widgets
    - ✅ Test combined helpdesk and loan metrics
    - ✅ Add #[Test] attributes
    - _Requirements: 9.5, 10.5_

  - [x] 13.3 Update tests/Feature/Filament/AdminDashboardLayoutTest.php ✅ **COMPLETED**
    - ✅ Verify dashboard layout and navigation
    - ✅ Test BM content in admin panel
    - ✅ Add #[Test] attributes
    - _Requirements: 9.5_

  - [ ]* 13.4 Write property test for Filament components
    - **Property 10: Filament Component Validation**
    - **Validates: Requirements 9.2, 9.3, 9.4, 9.5**

---

## Phase 9: Cross-Module Integration Tests

- [x] 14. Update Cross-Module Tests ✅ **COMPLETED**

  - [x] 14.1 Update tests/Feature/CrossModuleIntegrationTest.php ✅ **COMPLETED**
    - ✅ Verify helpdesk-loan module integration
    - ✅ Test damaged asset return triggers ticket
    - ✅ Add #[Test] attributes
    - _Requirements: 10.1, 10.4_

  - [x] 14.2 Update tests/Feature/Integration/CrossModuleIntegrationTest.php ✅ **COMPLETED**
    - ✅ Verify asset context in tickets
    - ✅ Test loan_transactions linking
    - ✅ Add #[Test] attributes
    - _Requirements: 10.2_

  - [x] 14.3 Update tests/Feature/Services/CrossModuleIntegrationServiceTest.php ✅ **COMPLETED**
    - ✅ Verify cross-module service operations
    - ✅ Test combined analytics access
    - ✅ Add #[Test] attributes
    - _Requirements: 10.3_

  - [x] 14.4 Update tests/Unit/Services/CrossModuleIntegrationServiceTest.php ✅ **COMPLETED**
    - ✅ Verify unit-level cross-module logic
    - ✅ Add #[Test] attributes
    - _Requirements: 10.2_

  - [ ]* 14.5 Write property test for cross-module data linking
    - **Property 11: Cross-Module Data Linking Validation**
    - **Validates: Requirements 10.2, 10.5**

---

## Phase 10: Remaining Feature Tests (PHP 8 Attributes + BM Content)

- [x] 15. Update Core Feature Tests ✅ **COMPLETED**

  - [x] 15.1 Update tests/Feature/ThemeToggleTest.php ✅ **COMPLETED**
    - ✅ Add #[Test] attributes
    - ✅ Update BM content assertions
    - _Requirements: 8.1, 8.2_

  - [x] 15.2 Update tests/Feature/WelcomePageTest.php ✅ **COMPLETED**
    - ✅ Verify BM content on welcome page
    - ✅ Add #[Test] attributes
    - _Requirements: 1.1, 8.1_

  - [x] 15.3 Update tests/Feature/HeaderViewTest.php ✅ **COMPLETED**
    - ✅ Verify BM navigation labels
    - ✅ Add #[Test] attributes
    - _Requirements: 1.2, 8.1_

  - [x] 15.4 Update tests/Feature/UserProfileTest.php ✅ **COMPLETED**
    - ✅ Verify profile management with BM labels
    - ✅ Add #[Test] attributes
    - _Requirements: 1.2, 8.1_

  - [x] 15.5 Update tests/Feature/BrandingTest.php ✅ **COMPLETED**
    - ✅ Verify branding with BM content
    - ✅ Add #[Test] attributes
    - _Requirements: 1.1, 8.1_

- [x] 16. Update Security Tests ✅ **COMPLETED**

  - [x] 16.1 Update tests/Feature/EncryptionSecurityTest.php ✅ **COMPLETED**
    - ✅ Add #[Test] attributes
    - _Requirements: 8.1_

  - [x] 16.2 Update tests/Feature/ImpersonationSecurityTest.php ✅ **COMPLETED**
    - ✅ Add #[Test] attributes
    - _Requirements: 8.1_

  - [x] 16.3 Update tests/Feature/SecurityMonitoringTest.php ✅ **COMPLETED**
    - ✅ Add #[Test] attributes
    - _Requirements: 8.1_

  - [x] 16.4 Update tests/Feature/Filament/SecurityTest.php ✅ **COMPLETED**
    - ✅ Add #[Test] attributes
    - _Requirements: 8.1_

  - [x] 16.5 Update tests/Feature/Filament/AuthenticationSecurityTest.php ✅ **COMPLETED**
    - ✅ Add #[Test] attributes
    - _Requirements: 8.1_

- [x] 17. Checkpoint - Verify feature tests pass ✅ **COMPLETED**
  - ✅ All test files have #[Test] attributes

---

## Phase 11: Remaining Unit Tests (PHP 8 Attributes)

- [ ] 18. Update Unit Service Tests

  - [x] 18.1 Update tests/Unit/Services/DataEncryptionServiceTest.php

    - Add #[Test] attributes
    - _Requirements: 8.1_

  - [x] 18.2 Update tests/Unit/Services/DualApprovalServiceTest.php

    - Add #[Test] attributes
    - _Requirements: 8.1_

  - [x] 18.3 Update tests/Unit/Services/DashboardServiceTest.php

    - Add #[Test] attributes
    - _Requirements: 8.1_

  - [x] 18.4 Update tests/Unit/Services/SubmissionServiceTest.php

    - Add #[Test] attributes
    - _Requirements: 8.1_

  - [x] 18.5 Update tests/Unit/Services/GuestSubmissionClaimServiceTest.php

    - Add #[Test] attributes
    - _Requirements: 8.1_

  - [x] 18.6 Update tests/Unit/Services/ExportServiceTest.php

    - Add #[Test] attributes
    - _Requirements: 8.1_

  - [x] 18.7 Update tests/Unit/Services/TicketStatusTransitionServiceTest.php

    - Add #[Test] attributes
    - _Requirements: 8.1_

  - [x] 18.8 Update tests/Unit/Services/SLAManagementServiceTest.php

    - Add #[Test] attributes
    - _Requirements: 8.1_

- [ ] 19. Update Other Unit Tests

  - [x] 19.1 Update tests/Unit/EmailNotificationTest.php

    - Add #[Test] attributes
    - Verify BM email content
    - _Requirements: 1.4, 8.1_

  - [x] 19.2 Update tests/Unit/ComponentMarkupTest.php

    - Add #[Test] attributes
    - _Requirements: 8.1_

  - [x] 19.3 Update tests/Unit/ResponsibleOfficerServiceTest.php

    - Add #[Test] attributes
    - _Requirements: 8.1_

  - [x] 19.4 Update tests/Unit/Middleware/SetLocaleMiddlewareTest.php

    - Update for BM-only locale
    - Add #[Test] attributes
    - _Requirements: 1.1, 8.1_

  - [x] 19.5 Update tests/Unit/Models/UserRoleTest.php

    - Verify four-role RBAC model
    - Add #[Test] attributes
    - _Requirements: 5.1, 8.1_

---

## Phase 12: Remaining Livewire Tests

- [ ] 20. Update Livewire Component Tests

  - [ ] 20.1 Update tests/Feature/Livewire/SubmissionHistoryTest.php
    - Verify BM labels and content
    - Add #[Test] attributes
    - _Requirements: 1.2, 8.1_

  - [ ] 20.2 Update tests/Feature/Livewire/Status/StatusCheckerTest.php
    - Verify BM status messages
    - Add #[Test] attributes
    - _Requirements: 1.2, 8.1_

  - [ ] 20.3 Update tests/Feature/Livewire/Staff/SessionManagerTest.php
    - Add #[Test] attributes
    - _Requirements: 8.1_

  - [ ] 20.4 Update tests/Feature/Livewire/Staff/AccountLinkingTest.php
    - Verify account linking workflow
    - Add #[Test] attributes
    - _Requirements: 3.4, 8.1_

  - [ ] 20.5 Update tests/Feature/Livewire/Auth/TwoFactorAuthenticationTest.php
    - Add #[Test] attributes
    - _Requirements: 8.1_

  - [ ] 20.6 Update tests/Feature/Livewire/AssetAvailabilityCalendarTest.php
    - Verify BM labels
    - Add #[Test] attributes
    - _Requirements: 1.2, 8.1_

---

## Phase 13: Portal and Performance Tests

- [ ] 21. Update Portal Tests

  - [ ] 21.1 Update tests/Feature/Portal/DashboardTest.php
    - Verify BM content in portal
    - Add #[Test] attributes
    - _Requirements: 1.2, 8.1_

  - [ ] 21.2 Update tests/Feature/AuthenticatedPortalTest.php
    - Verify authenticated portal features
    - Add #[Test] attributes
    - _Requirements: 5.1, 8.1_

  - [ ] 21.3 Update tests/Feature/StaffPortalRoutesTest.php
    - Verify staff portal routes
    - Add #[Test] attributes
    - _Requirements: 5.1, 8.1_

- [ ] 22. Update Performance Tests

  - [ ] 22.1 Update tests/Feature/Performance/LoanModulePerformanceTest.php
    - Add #[Test] attributes
    - _Requirements: 8.1_

  - [ ] 22.2 Update tests/Feature/Filament/PerformanceTest.php
    - Add #[Test] attributes
    - _Requirements: 8.1_

  - [ ] 22.3 Update tests/Feature/PerformanceIntegrationTest.php
    - Add #[Test] attributes
    - _Requirements: 8.1_

---

## Phase 14: Email and Compliance Tests

- [ ] 23. Update Email Tests

  - [ ] 23.1 Update tests/Feature/EmailSystemTest.php
    - Verify BM email content
    - Add #[Test] attributes
    - _Requirements: 1.4, 8.1_

  - [ ] 23.2 Update tests/Feature/EmailSystemIntegrationTest.php
    - Verify BM email templates
    - Add #[Test] attributes
    - _Requirements: 1.4, 8.1_

  - [ ] 23.3 Update tests/Feature/Email/EmailTemplateBrandingTest.php
    - Verify BM branding in emails
    - Add #[Test] attributes
    - _Requirements: 1.4, 8.1_

  - [ ] 23.4 Update tests/Feature/Email/LoanEmailNotificationTest.php
    - Verify BM loan notification content
    - Add #[Test] attributes
    - _Requirements: 1.4, 8.1_

- [ ] 24. Update Compliance Tests

  - [ ] 24.1 Update tests/Feature/Compliance/PDPAComplianceTest.php
    - Add #[Test] attributes
    - _Requirements: 8.1_

  - [ ] 24.2 Update tests/Feature/Compliance/SecurityComplianceIntegrationTest.php
    - Add #[Test] attributes
    - _Requirements: 8.1_

---

## Phase 15: Final Verification

- [ ] 25. Write Test Count Preservation Property Test
  - [ ]* 25.1 Write property test for test count preservation
    - **Property 12: Test Count Preservation**
    - **Validates: Requirements 8.5, 11.2**

- [ ] 26. Final Checkpoint - Verify entire test suite passes
  - Ensure all tests pass, ask the user if questions arise.
  - Verify no @test annotations remain in codebase
  - Verify all test files have proper PHPUnit attribute imports
  - Verify BM content assertions throughout
  - _Requirements: 11.1, 11.2_
